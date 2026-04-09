<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;
use App\Models\SinistreDocumentSoumis;
use Illuminate\Http\Request;
use App\Services\YellikaSmsService;
use App\Notifications\DocumentRejectedNotification;
use Illuminate\Support\Facades\Notification;

class SinistreController extends Controller
{
    protected YellikaSmsService $sms;

    public function __construct(YellikaSmsService $sms)
    {
        $this->sms = $sms;
    }
    /**
     * Liste des sinistres à examiner ou globaux.
     */
    public function index(Request $request)
    {
        // Seulement les sinistres affectés à cette compagnie d'assurance
        $query = Sinistre::query()->with('assure')
            ->where('assurance_id', auth('user')->id());        // Filtre par défaut sur TOUS les sinistres
        if ($request->has('status') && $request->status === 'review') {
            $query->where(function ($q) {
                $q->where('workflow_step', 'manager_review')
                  ->orWhere('status', 'traite');
            });
        } else {
            // Pas de filtre strict par défaut, on liste tout
        }

        $sinistres = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('assurance.sinistres.index', compact('sinistres'));
    }

    /**
     * Afficher les détails du sinistre pour la validation.
     */
    public function show(Sinistre $sinistre)
    {
        $sinistre->load(['assure', 'documentsAttendus.documentsSoumis', 'expert', 'garage']);

        // Seuls les experts et garages de cette assurance
        $assuranceId = auth('user')->id();
        $experts = \App\Models\User::where('role', 'expert')->where('assurance_id', $assuranceId)->get();
        $garages = \App\Models\User::where('role', 'garage')->where('assurance_id', $assuranceId)->get();

        return view('assurance.sinistres.review', compact('sinistre', 'experts', 'garages'));
    }

    /**
     * Outrepasser (Override) la décision de l'IA pour un document précis.
     */
    public function reviewDoc(Request $request, Sinistre $sinistre, SinistreDocumentAttendu $documentAttendu)
    {
        $request->validate([
            'override_status' => 'required|in:valid,invalid,pending',
            'feedback' => 'nullable|string'
        ]);

        $dernierSoumis = $documentAttendu->documentsSoumis()->latest()->first();

        if ($dernierSoumis) {
            $dernierSoumis->manager_override_status = ($request->override_status === 'pending') ? null : $request->override_status;
            
            if ($request->filled('feedback')) {
                $dernierSoumis->ai_feedback = "Observation du gestionnaire : " . $request->feedback;
            }
            $dernierSoumis->save();

            // Si le gestionnaire rejette le document (invalide) ou le déverrouille (pending)
            if (in_array($request->override_status, ['invalid', 'pending'])) {
                // Remettre le document en attente pour le client, pour qu'il puisse le soumettre à nouveau
                $documentAttendu->status_client = 'pending';
                $documentAttendu->save();

                if ($request->override_status === 'invalid') {
                    // Envoyer Email
                    Notification::send($sinistre->assure, new DocumentRejectedNotification($sinistre, $documentAttendu, $request->feedback));

                    // Envoyer SMS si le numéro existe
                    if (!empty($sinistre->assure->contact) && isset($this->sms)) {
                        $smsMessage = "CLAIMS MASTER: Votre document (" . $documentAttendu->nom_document . ") pour le sinistre #" . $sinistre->id . " a ete rejete. Veuillez vous connecter pour le soumettre a nouveau.";
                        $this->sms->sendSms($sinistre->assure->contact, $smsMessage);
                    }
                }
            }
        }

        return back()->with('success', 'Le statut du document a été mis à jour.');
    }

    /**
     * Prendre une décision finale sur le dossier.
     */
    public function decision(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'decision' => 'required|in:valide,rejete,complement_requis',
            'message_client' => 'nullable|string'
        ]);

        if ($request->decision === 'valide') {
            $sinistre->status = 'cloture';
            $sinistre->workflow_step = 'closed_validated';
        } elseif ($request->decision === 'rejete') {
            $sinistre->status = 'cloture';
            $sinistre->workflow_step = 'closed_rejected';
        } elseif ($request->decision === 'complement_requis') {
            $sinistre->status = 'en_attente';
            $sinistre->workflow_step = 'docs_pending';

            // Récupérer les documents rejetés ou toujours en attente
            $docsManquants = $sinistre->documentsAttendus()
                ->where(function ($q) {
                    $q->where('status_client', 'pending')
                        ->orWhereHas('documentsSoumis', function ($sq) {
                            $sq->where('manager_override_status', 'invalid');
                        });
                })->get();

            if ($docsManquants->count() > 0) {
                $aiService = new \App\Services\AIService();
                $docNames = $docsManquants->pluck('nom_document')->toArray();
                $aiMessage = $aiService->generateDocumentRequestMessage($sinistre, $docNames);

                Notification::send($sinistre->assure, new \App\Notifications\DocumentsRequisNotification($sinistre, $docsManquants, $aiMessage));

                // SMS si nécessaire
                if (!empty($sinistre->assure->contact) && isset($this->sms)) {
                    $this->sms->sendSms($sinistre->assure->contact, "CLAIMS MASTER: Des complements sont requis pour votre sinistre #" . $sinistre->id . ". " . substr($aiMessage, 0, 100));
                }
            }
        }

        $sinistre->save();

        return redirect()->route('assurance.sinistres.index')->with('success', 'La décision a été enregistrée avec succès.');
    }

    /**
     * Workflow Step 3: Vérification des garanties
     */
    public function verifyGaranties(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'est_couvert' => 'required|boolean',
            'motif_rejet' => 'nullable|string|required_if:est_couvert,0'
        ]);

        $sinistre->est_couvert = $request->est_couvert;
        if (!$request->est_couvert) {
            $sinistre->motif_rejet = $request->motif_rejet;
            $sinistre->status = 'cloture';
            $sinistre->workflow_step = 'rejected_no_warranty';
            // TODO: Generate and send "Lettre de rejet motivé" to user
        } else {
            $sinistre->workflow_step = 'warranty_verified_pending_assignment';
            if (!$sinistre->numero_sinistre) {
                // Generate unique Sinistre Number
                $sinistre->numero_sinistre = 'SIN-' . date('Ymd') . '-' . str_pad($sinistre->id, 4, '0', STR_PAD_LEFT);
            }
        }
        $sinistre->save();

        return back()->with('success', 'La vérification des garanties a été enregistrée.');
    }

    /**
     * Workflow Step 4/5: Orientation Garage & Expert
     */
    public function assignExpertGarage(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'expert_id' => 'nullable|exists:users,id',
            'garage_id' => 'nullable|exists:users,id'
        ]);

        if ($request->filled('expert_id')) {
            $sinistre->expert_id = $request->expert_id;
            $sinistre->date_mandat_expert = now();
        }

        if ($request->filled('garage_id')) {
            $sinistre->garage_id = $request->garage_id;
            $sinistre->status = 'en_cours';
        }

        $sinistre->workflow_step = 'expert_garage_assigned';
        $sinistre->save();

        return back()->with('success', 'L\'expert et/ou le garage ont été assignés.');
    }

    /**
     * Vues pour impression/génération de PDF
     */
    public function pdfDossier(Sinistre $sinistre)
    {
        $sinistre->load(['assure.contrats', 'assurance.assuranceProfile', 'constat', 'expert', 'garage']);
        return view('assurance.sinistres.pdf.dossier', compact('sinistre'));
    }

    public function pdfPriseEnCharge(Sinistre $sinistre)
    {
        $sinistre->load(['assure', 'assurance', 'garage', 'expert']);
        // Fixer la date du bon de prise en charge à la première impression si vide
        if (!$sinistre->date_bon_prise_charge) {
            $sinistre->date_bon_prise_charge = now();
            $sinistre->save();
        }
        return view('assurance.sinistres.pdf.prise_en_charge', compact('sinistre'));
    }

    public function pdfBonSortie(Sinistre $sinistre)
    {
        $sinistre->load(['assure', 'assurance', 'garage']);
        if (!$sinistre->date_bon_sortie) {
            $sinistre->date_bon_sortie = now();
            $sinistre->save();
        }
        return view('assurance.sinistres.pdf.bon_sortie', compact('sinistre'));
    }
}
