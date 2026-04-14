<?php

namespace App\Http\Controllers\Assure;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;
use App\Models\SinistreDocumentSoumis;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinistreDocumentController extends Controller
{
    /**
     * Affiche la page d'upload des documents pour un sinistre.
     */
    public function index(Sinistre $sinistre)
    {
        abort_if($sinistre->user_id !== Auth::id(), 403);

        $documentsAttendus = $sinistre->documentsAttendus()->with('documentsSoumis')->get();

        return view('assure.sinistres.upload-docs', compact('sinistre', 'documentsAttendus'));
    }

    /**
     * Gère l'upload et la vérification IA d'un document.
     */
    public function upload(Request $request, SinistreDocumentAttendu $documentAttendu)
    {
        $sinistre = $documentAttendu->sinistre;
        abort_if($sinistre->user_id !== Auth::id(), 403);

        $request->validate([
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'document_text' => 'nullable|string',
        ]);

        if (!$request->hasFile('document_file') && empty($request->document_text)) {
            return response()->json(['success' => false, 'message' => 'Veuillez fournir un document ou un texte.'], 400);
        }

        // Vérifier si le sinistre complet est déjà clôturé définitivement par l'assurance
        if ($sinistre->status === 'cloture') {
            return response()->json([
                'success' => false, 
                'message' => 'Le dossier de ce sinistre est définitivement clôturé par l\'assurance. Il n\'est plus possible d\'ajouter de pièces.'
            ], 403);
        }

        // Vérifier si ce document précis a déjà été validé par l'assurance (manager_override_status = 'valid')
        $dernierSoumis = $documentAttendu->documentsSoumis()->latest()->first();
        if ($dernierSoumis && $dernierSoumis->manager_override_status === 'valid') {
            return response()->json([
                'success' => false, 
                'message' => 'L\'assurance a déjà validé ce document. Aucune modification n\'est désormais permise.'
            ], 403);
        }

        $soumis = new SinistreDocumentSoumis();
        $soumis->sinistre_document_attendu_id = $documentAttendu->id;

        $aiFeedback = null;
        $aiStatus = 'valid'; // Par défaut

        if ($documentAttendu->type_champ === 'file' && $request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('sinistres_documents', 'public');
            $soumis->file_path = $path;

            // Appel à l'IA Vision pour les images et les PDFs
            $mime = $request->file('document_file')->getClientMimeType();
            if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                $aiService = new AIService();
                $fullPath = storage_path('app/public/' . $path);

                $verification = $aiService->verifyDocumentImage($fullPath, $documentAttendu->nom_document);

                if (is_array($verification) && isset($verification['status'])) {
                    $aiStatus = $verification['status'];
                    $aiFeedback = $verification['feedback'] ?? null;
                } else {
                    $aiStatus = 'pending';
                    $aiFeedback = "L'analyse IA a échoué ou a renvoyé une réponse invalide. Le document sera vérifié manuellement.";
                }
            }
        } else {
            // Texte ou Nombre
            $soumis->file_value = $request->document_text;
            $aiFeedback = "Valeur textuelle enregistrée. Validation automatique.";
        }

        $soumis->ai_compliance_status = $aiStatus;
        $soumis->ai_feedback = $aiFeedback;
        $soumis->save();

        // Mettre à jour le statut du document attendu
        if ($aiStatus === 'invalid') {
            $documentAttendu->status_client = 'rejected';
        } else {
            $documentAttendu->status_client = 'uploaded';
        }
        $documentAttendu->save();

        // Vérifier si tous les documents obligatoires sont uploadés ET valides (ou en attente d'examen manuel)
        $mandatoryDocsCount = $sinistre->documentsAttendus()->where('is_mandatory', true)->count();
        $uploadedDocsCount = $sinistre->documentsAttendus()
            ->where('is_mandatory', true)
            ->where('status_client', 'uploaded') // On ne compte que ceux qui sont 'uploaded'
            ->count();

        // Un dossier ne passe à l'étape 'manager_review' que SI :
        // 1. Tous les documents obligatoires sont uploadés
        // 2. L'agent a terminé le constat sur le terrain (status === 'traite')
        if ($mandatoryDocsCount > 0 && $mandatoryDocsCount === $uploadedDocsCount && $sinistre->status === 'traite') {
            $sinistre->workflow_step = 'manager_review';
            $sinistre->save();
        }

        return response()->json([
            'success' => true,
            'status' => $aiStatus,
            'feedback' => $aiFeedback,
            'soumis_id' => $soumis->id
        ]);
    }
}
