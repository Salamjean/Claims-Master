<?php

namespace App\Http\Controllers\Api\Assure;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssuranceApiController extends Controller
{
    /**
     * GET /api/v1/assure/assurances
     * Liste toutes les assurances / contrats de l'assuré connecté.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les assurés authentifiés peuvent consulter leurs assurances.'
            ], 403);
        }

        $contrats = Contrat::where('client_id', $user->id)
            ->with('assureur')
            ->latest()
            ->get()
            ->map(fn(Contrat $contrat) => $this->formatContrat($contrat));

        return response()->json([
            'success' => true,
            'count'   => $contrats->count(),
            'data'    => $contrats
        ], 200);
    }

    /**
     * GET /api/v1/assure/assurances/{id}
     * Affiche les détails d'une assurance spécifique.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $contrat = Contrat::where('client_id', $user->id)
            ->with('assureur')
            ->find($id);

        if (!$contrat) {
            return response()->json([
                'success' => false,
                'message' => 'Assurance introuvable ou vous n’avez pas les droits pour y accéder.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatContrat($contrat)
        ], 200);
    }

    /**
     * POST /api/v1/assure/assurances
     * Enregistrement d'une nouvelle assurance / contrat par l'assuré.
     */
    public function store(Request $request, AIService $aiService)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'numero_contrat'        => ['required', 'string', 'max:255', 'unique:contrats,numero_contrat'],
            'plaque'                => ['required', 'string', 'max:255'],
            'marque'                => ['required', 'string', 'max:255'],
            'modele'                => ['required', 'string', 'max:255'],
            'type_vehicule'         => ['nullable', 'string', 'max:255'],
            'immatriculation'       => ['nullable', 'string', 'max:255'],
            'assurance_id'          => ['nullable', 'exists:users,id'],
            'nom_assureur'          => ['nullable', 'string', 'max:255'],
            'prime'                 => ['nullable', 'numeric', 'min:0'],
            'date_debut'            => ['nullable', 'date'],
            'date_fin'              => ['nullable', 'date'],
            'document_pdf'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'attestation_assurance' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'carte_grise'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'visite_technique'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'permis_conduire'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'numero_contrat.required' => 'Le numéro de contrat est obligatoire.',
            'numero_contrat.unique'   => 'Ce numéro de contrat est déjà enregistré.',
            'plaque.required'         => 'La plaque d’immatriculation est obligatoire.',
            'marque.required'         => 'La marque du véhicule est obligatoire.',
            'modele.required'         => 'Le modèle du véhicule est obligatoire.',
            'assurance_id.exists'     => 'La compagnie d’assurance sélectionnée n’existe pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = [
            'client_id'       => $user->id,
            'numero_contrat'  => trim($request->input('numero_contrat')),
            'plaque'          => trim($request->input('plaque')),
            'marque'          => trim($request->input('marque')),
            'modele'          => trim($request->input('modele')),
            'type_vehicule'   => $request->input('type_vehicule', 'Automobile'),
            'immatriculation' => $request->input('immatriculation', trim($request->input('plaque'))),
            'type_contrat'    => 'Automobile',
            'assurance_id'    => $request->input('assurance_id'),
            'nom_assureur'    => $request->input('nom_assureur'),
            'prime'           => $request->input('prime', 0),
            'date_debut'      => $request->input('date_debut', now()->format('Y-m-d')),
            'date_fin'        => $request->input('date_fin'),
            'statut'          => 'actif',
        ];

        // Audit & Traitement IA si une attestation est transmise
        if ($request->hasFile('attestation_assurance')) {
            try {
                $tempPath = $request->file('attestation_assurance')->getPathname();
                $verification = $aiService->verifyAttestation($tempPath, [
                    'plaque'         => $data['plaque'],
                    'marque'         => $data['marque'],
                    'modele'         => $data['modele'],
                    'numero_contrat' => $data['numero_contrat'],
                ]);

                $data['attestation_ai_status']   = $verification['status'] ?? 'pending';
                $data['attestation_ai_feedback'] = $verification['feedback'] ?? null;

                if (!empty($verification['assureur']) && empty($data['nom_assureur'])) {
                    $cleanedName = trim($verification['assureur']);
                    $data['nom_assureur'] = $cleanedName;

                    $matchingAssureur = User::where('role', 'assurance')
                        ->where(function ($query) use ($cleanedName) {
                            $query->where('name', 'LIKE', '%' . $cleanedName . '%')
                                  ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$cleanedName]);
                        })->first();

                    if ($matchingAssureur) {
                        $data['assurance_id'] = $matchingAssureur->id;
                    }
                }

                if (!empty($verification['date_expiration']) && empty($data['date_fin'])) {
                    $parsedDate = AIService::parseFlexibleDate($verification['date_expiration']);
                    if ($parsedDate) {
                        $data['date_fin'] = $parsedDate->format('Y-m-d');
                    }
                }
            } catch (\Exception $e) {
                Log::error("Erreur d'analyse IA de l'attestation via API: " . $e->getMessage());
                $data['attestation_ai_status'] = 'pending';
            }
        }

        // Upload des fichiers
        if ($request->hasFile('document_pdf')) {
            $data['document_pdf'] = $request->file('document_pdf')->store('contrats/documents', 'public');
        }
        if ($request->hasFile('attestation_assurance')) {
            $data['attestation_assurance'] = $request->file('attestation_assurance')->store('contrats/attestations', 'public');
        }
        if ($request->hasFile('carte_grise')) {
            $data['carte_grise'] = $request->file('carte_grise')->store('contrats/cartes_grises', 'public');
        }
        if ($request->hasFile('visite_technique')) {
            $data['visite_technique'] = $request->file('visite_technique')->store('contrats/visites_techniques', 'public');
        }
        if ($request->hasFile('permis_conduire')) {
            $data['permis_conduire'] = $request->file('permis_conduire')->store('contrats/permis', 'public');
        }

        $contrat = Contrat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'L’assurance a été enregistrée avec succès.',
            'data'    => $this->formatContrat($contrat->load('assureur'))
        ], 201);
    }

    /**
     * PUT / POST /api/v1/assure/assurances/{id}
     * Modification d'une assurance existante.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $contrat = Contrat::where('client_id', $user->id)->find($id);

        if (!$contrat) {
            return response()->json([
                'success' => false,
                'message' => 'Assurance introuvable ou vous n’avez pas les droits pour la modifier.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_contrat'        => ['sometimes', 'required', 'string', 'max:255', 'unique:contrats,numero_contrat,' . $contrat->id],
            'plaque'                => ['sometimes', 'required', 'string', 'max:255'],
            'marque'                => ['sometimes', 'required', 'string', 'max:255'],
            'modele'                => ['sometimes', 'required', 'string', 'max:255'],
            'type_vehicule'         => ['nullable', 'string', 'max:255'],
            'immatriculation'       => ['nullable', 'string', 'max:255'],
            'assurance_id'          => ['nullable', 'exists:users,id'],
            'nom_assureur'          => ['nullable', 'string', 'max:255'],
            'prime'                 => ['nullable', 'numeric', 'min:0'],
            'date_debut'            => ['nullable', 'date'],
            'date_fin'              => ['nullable', 'date'],
            'statut'                => ['nullable', 'string', 'in:actif,expire,suspendu'],
            'document_pdf'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'attestation_assurance' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'carte_grise'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'visite_technique'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'permis_conduire'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'numero_contrat', 'plaque', 'marque', 'modele', 'type_vehicule',
            'immatriculation', 'assurance_id', 'nom_assureur', 'prime',
            'date_debut', 'date_fin', 'statut'
        ]);

        // Remplacement des fichiers si fournis
        if ($request->hasFile('document_pdf')) {
            if ($contrat->document_pdf) {
                Storage::disk('public')->delete($contrat->document_pdf);
            }
            $updateData['document_pdf'] = $request->file('document_pdf')->store('contrats/documents', 'public');
        }

        if ($request->hasFile('attestation_assurance')) {
            if ($contrat->attestation_assurance) {
                Storage::disk('public')->delete($contrat->attestation_assurance);
            }
            $updateData['attestation_assurance'] = $request->file('attestation_assurance')->store('contrats/attestations', 'public');
        }

        if ($request->hasFile('carte_grise')) {
            if ($contrat->carte_grise) {
                Storage::disk('public')->delete($contrat->carte_grise);
            }
            $updateData['carte_grise'] = $request->file('carte_grise')->store('contrats/cartes_grises', 'public');
        }

        if ($request->hasFile('visite_technique')) {
            if ($contrat->visite_technique) {
                Storage::disk('public')->delete($contrat->visite_technique);
            }
            $updateData['visite_technique'] = $request->file('visite_technique')->store('contrats/visites_techniques', 'public');
        }

        if ($request->hasFile('permis_conduire')) {
            if ($contrat->permis_conduire) {
                Storage::disk('public')->delete($contrat->permis_conduire);
            }
            $updateData['permis_conduire'] = $request->file('permis_conduire')->store('contrats/permis', 'public');
        }

        $contrat->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'L’assurance a été mise à jour avec succès.',
            'data'    => $this->formatContrat($contrat->fresh('assureur'))
        ], 200);
    }

    /**
     * DELETE /api/v1/assure/assurances/{id}
     * Suppression d'une assurance.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $contrat = Contrat::where('client_id', $user->id)->find($id);

        if (!$contrat) {
            return response()->json([
                'success' => false,
                'message' => 'Assurance introuvable ou vous n’avez pas les droits pour la supprimer.'
            ], 404);
        }

        // Supprimer les fichiers
        foreach (['document_pdf', 'attestation_assurance', 'carte_grise', 'visite_technique', 'permis_conduire'] as $fileKey) {
            if ($contrat->$fileKey) {
                Storage::disk('public')->delete($contrat->$fileKey);
            }
        }

        $contrat->delete();

        return response()->json([
            'success' => true,
            'message' => 'L’assurance a été supprimée avec succès.'
        ], 200);
    }

    /**
     * GET /api/v1/assure/assureurs
     * Liste des compagnies d'assurance disponibles.
     */
    public function listAssureurs()
    {
        $assureurs = User::where('role', 'assurance')
            ->select('id', 'name', 'email', 'contact', 'adresse')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'count'   => $assureurs->count(),
            'data'    => $assureurs
        ], 200);
    }

    /**
     * Formatage uniforme du contrat
     */
    private function formatContrat(Contrat $contrat): array
    {
        return [
            'id'                      => $contrat->id,
            'numero_contrat'          => $contrat->numero_contrat,
            'type_contrat'            => $contrat->type_contrat,
            'plaque'                  => $contrat->plaque,
            'marque'                  => $contrat->marque,
            'modele'                  => $contrat->modele,
            'type_vehicule'           => $contrat->type_vehicule,
            'immatriculation'         => $contrat->immatriculation ?? $contrat->plaque,
            'statut'                  => $contrat->statut,
            'prime'                   => (float) $contrat->prime,
            'date_debut'              => $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('Y-m-d') : null,
            'date_fin'                => $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('Y-m-d') : null,
            'nom_assureur'            => $contrat->nom_assureur ?? ($contrat->assureur?->name ?? 'Non spécifié'),
            'assurance_id'            => $contrat->assurance_id,
            'assureur'                => $contrat->assureur ? [
                'id'      => $contrat->assureur->id,
                'name'    => $contrat->assureur->name,
                'email'   => $contrat->assureur->email,
                'contact' => $contrat->assureur->contact,
            ] : null,
            'attestation_ai_status'   => $contrat->attestation_ai_status,
            'attestation_ai_feedback' => $contrat->attestation_ai_feedback,
            'documents'               => [
                'document_pdf_url'          => $contrat->document_pdf ? asset('storage/' . $contrat->document_pdf) : null,
                'attestation_assurance_url' => $contrat->attestation_assurance ? asset('storage/' . $contrat->attestation_assurance) : null,
                'carte_grise_url'           => $contrat->carte_grise ? asset('storage/' . $contrat->carte_grise) : null,
                'visite_technique_url'      => $contrat->visite_technique ? asset('storage/' . $contrat->visite_technique) : null,
                'permis_conduire_url'       => $contrat->permis_conduire ? asset('storage/' . $contrat->permis_conduire) : null,
            ],
            'created_at'              => $contrat->created_at?->toIso8601String(),
            'updated_at'              => $contrat->updated_at?->toIso8601String(),
        ];
    }

    /**
     * POST /api/v1/assure/assurances/scan-attestation-ai
     * Scan d'une attestation d'assurance par l'IA pour extraire les informations clés.
     */
    public function scanAttestationAI(Request $request, AIService $aiService)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'attestation' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation du fichier.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $file = $request->file('attestation');
        $tempPath = $file->getPathname();

        $result = $aiService->extractAttestationData($tempPath);

        if (($result['status'] ?? '') === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Attestation analysée avec succès par l\'IA.',
                'data'    => $result['data']
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de l\'analyse de l\'attestation.'
        ], 422);
    }
}
