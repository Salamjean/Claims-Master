<?php

namespace App\Http\Controllers\Assure;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContratController extends Controller
{
    /**
     * Affiche la liste des contrats de l'assuré
     */
    public function index()
    {
        $user = auth('user')->user();
        $contrats = $user->contrats()->with('assureur')->latest()->get();
        return view('assure.contrats.index', compact('contrats'));
    }

    /**
     * Affiche le formulaire d'ajout de contrat
     */
    /**
     * Affiche le formulaire d'ajout ou de renouvellement de contrat
     */
    public function create(Request $request)
    {
        $contratExistant = null;
        if ($request->filled('renouveler_id')) {
            $contratExistant = auth('user')->user()->contrats()->find($request->renouveler_id);
        }
        return view('assure.contrats.create', compact('contratExistant'));
    }

    /**
     * Enregistre un nouveau contrat ou met à jour un contrat renouvelé
     */
    public function store(Request $request, AIService $aiService)
    {
        set_time_limit(120); // Empêcher le timeout de 30s lors des traitements IA

        $renouvelerId = $request->input('renouveler_id');
        $contratExistant = $renouvelerId ? auth('user')->user()->contrats()->find($renouvelerId) : null;

        // Si ce n'est pas un renouvellement explicit, vérifier si le véhicule (plaque) existe déjà pour l'utilisateur
        if (!$contratExistant && $request->filled('plaque')) {
            $plaqueClean = strtoupper(preg_replace('/\s+/', '', $request->plaque));
            $existingPlaqueContrat = auth('user')->user()->contrats()->get()->first(function ($c) use ($plaqueClean) {
                return strtoupper(preg_replace('/\s+/', '', $c->plaque)) === $plaqueClean;
            });

            if ($existingPlaqueContrat) {
                return redirect()->route('assure.contrats.create', ['renouveler_id' => $existingPlaqueContrat->id])
                    ->withInput()
                    ->with('error', 'Un contrat existe déjà pour le véhicule avec la plaque "' . $request->plaque . '". Vous avez été réorienté automatiquement vers le mode de renouvellement.');
            }
        }

        $uniqueRule = 'required|string|unique:contrats,numero_contrat';
        if ($contratExistant) {
            $uniqueRule = 'required|string|unique:contrats,numero_contrat,' . $contratExistant->id;
        }

        $request->validate([
            'numero_contrat' => $uniqueRule,
            'assurance_id' => 'nullable|exists:users,id',
            'plaque' => 'required|string',
            'marque' => 'required|string',
            'modele' => 'required|string',
            'type_vehicule' => 'required|string',
            'immatriculation' => 'required|string',
            'document_pdf' => $contratExistant ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' : 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'attestation_assurance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carte_grise' => $contratExistant ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' : 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visite_technique' => $contratExistant ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' : 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'permis_conduire' => $contratExistant ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' : 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // --- ÉTAPE 1 : Audit et Vérification IA de l'Attestation en 1 seul appel rapide ---
        $aiStatus = 'pending';
        $aiFeedback = null;

        if ($request->hasFile('attestation_assurance')) {
            $mime = $request->file('attestation_assurance')->getMimeType();

            if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                $tempPath = $request->file('attestation_assurance')->getPathname();
                $verification = $aiService->verifyAttestation($tempPath, $request->only(['plaque', 'marque', 'modele', 'numero_contrat']));

                $aiStatus = $verification['status'] ?? 'pending';
                $aiFeedback = $verification['feedback'] ?? null;
                $nomAssureurDocument = $verification['assureur'] ?? null;
                $numeroContratDoc = $verification['numero_contrat'] ?? null;
                $dateExpirationDoc = $verification['date_expiration'] ?? null;

                if ($aiStatus === 'invalid') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Le document d\'attestation a été rejeté par l\'IA : ' . ($aiFeedback ?? 'Les informations du document ne correspondent pas.'));
                }

                // --- ÉTAPE 2 : Mise à jour du N° de police si extrait par l'IA ---
                if (!empty($numeroContratDoc)) {
                    $dataInsurer['numero_contrat'] = trim($numeroContratDoc);
                }

                // --- ÉTAPE 3 : Attribution automatique de l'assureur ---
                if (!empty($nomAssureurDocument)) {
                    $cleanedName = trim($nomAssureurDocument);

                    $matchingAssureur = \App\Models\User::where('role', 'assurance')
                        ->where(function ($query) use ($cleanedName) {
                            $query->where('name', 'LIKE', '%' . $cleanedName . '%')
                                ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$cleanedName]);
                        })
                        ->first();

                    if ($matchingAssureur) {
                        $dataInsurer['assurance_id'] = $matchingAssureur->id;
                    } else {
                        $dataInsurer['assurance_id'] = null;
                    }
                    $dataInsurer['nom_assureur'] = $cleanedName;
                }

                // --- ÉTAPE 4 : Extraction de la date d'expiration via l'IA ---
                if (!empty($dateExpirationDoc)) {
                    $parsedDate = AIService::parseFlexibleDate($dateExpirationDoc);
                    if ($parsedDate) {
                        $dataInsurer['date_fin'] = $parsedDate->format('Y-m-d');
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Impossible de parser la date d'expiration IA dans store(): " . $dateExpirationDoc);
                    }
                }
            }
        }

        $user = auth('user')->user();

        $data = $request->except(['document_pdf', 'attestation_assurance', 'carte_grise', 'visite_technique', 'permis_conduire', 'renouveler_id']);
        $data['client_id'] = $user->id;
        $data['type_contrat'] = 'Automobile';
        $data['date_debut'] = now();
        $data['attestation_ai_status'] = $aiStatus;
        $data['attestation_ai_feedback'] = $aiFeedback;

        // Fusion des données d'assureur détectées par l'IA
        if (isset($dataInsurer)) {
            $data = array_merge($data, $dataInsurer);
        }

        if ($request->hasFile('document_pdf')) {
            $path = $request->file('document_pdf')->store('contrats/documents', 'public');
            $data['document_pdf'] = $path;
        }

        if ($request->hasFile('attestation_assurance')) {
            $path = $request->file('attestation_assurance')->store('contrats/attestations', 'public');
            $data['attestation_assurance'] = $path;
        }

        if ($request->hasFile('carte_grise')) {
            $path = $request->file('carte_grise')->store('contrats/cartes_grises', 'public');
            $data['carte_grise'] = $path;
        }

        if ($request->hasFile('visite_technique')) {
            $path = $request->file('visite_technique')->store('contrats/visites_techniques', 'public');
            $data['visite_technique'] = $path;
        }

        if ($request->hasFile('permis_conduire')) {
            $path = $request->file('permis_conduire')->store('contrats/permis', 'public');
            $data['permis_conduire'] = $path;
        }

        if ($contratExistant) {
            if (!$request->hasFile('document_pdf'))
                unset($data['document_pdf']);
            if (!$request->hasFile('carte_grise'))
                unset($data['carte_grise']);
            if (!$request->hasFile('visite_technique'))
                unset($data['visite_technique']);
            if (!$request->hasFile('permis_conduire'))
                unset($data['permis_conduire']);

            $contratExistant->update($data);

            return redirect()->route('assure.contrats.index')
                ->with('success', 'L\'assurance de votre véhicule ' . $contratExistant->plaque . ' a été renouvelée et vérifiée par l\'IA avec succès.');
        }

        Contrat::create($data);

        return redirect()->route('assure.contrats.index')
            ->with('success', 'Votre assurance a été ajoutée avec succès.');
    }

    /**
     * Supprime un contrat
     */
    public function destroy(Contrat $contrat)
    {
        // Sécurité : l'assuré ne peut supprimer que ses propres contrats
        abort_if($contrat->client_id !== auth('user')->id(), 403);

        // Supprimer les fichiers associés du stockage
        if ($contrat->document_pdf) {
            Storage::disk('public')->delete($contrat->document_pdf);
        }
        if ($contrat->attestation_assurance) {
            Storage::disk('public')->delete($contrat->attestation_assurance);
        }
        if ($contrat->carte_grise) {
            Storage::disk('public')->delete($contrat->carte_grise);
        }
        if ($contrat->visite_technique) {
            Storage::disk('public')->delete($contrat->visite_technique);
        }
        if ($contrat->permis_conduire) {
            Storage::disk('public')->delete($contrat->permis_conduire);
        }

        $contrat->delete();

        return redirect()->route('assure.contrats.index')
            ->with('success', 'Votre assurance a été supprimée avec succès.');
    }

    /**
     * Renouvelle uniquement l'attestation d'assurance d'un contrat via l'IA (Modal Pop-up)
     */
    public function renew(Request $request, Contrat $contrat, AIService $aiService)
    {
        set_time_limit(120); // Empêcher le timeout de 30s lors des traitements IA

        // Sécurité : l'assuré ne peut renouveler que ses propres contrats
        abort_if($contrat->client_id !== auth('user')->id(), 403);

        $request->validate([
            'attestation_assurance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('attestation_assurance');
        $tempPath = $file->getPathname();

        // Audit et Vérification IA complète (Structure ASACI, Immatriculation, Marque, Modèle, N° Police & Extraction Échéance) en 1 seul appel rapide
        $verification = $aiService->verifyAttestation($tempPath, $contrat->only(['plaque', 'marque', 'modele']));

        $aiStatus = $verification['status'] ?? 'pending';
        $aiFeedback = $verification['feedback'] ?? null;
        $nomAssureurDocument = $verification['assureur'] ?? null;
        $numeroContratDoc = $verification['numero_contrat'] ?? null;
        $dateExpirationDoc = $verification['date_expiration'] ?? null;

        // Rejet si l'IA déclare le document invalide (informations incohérentes ou immatriculation différente)
        if ($aiStatus === 'invalid') {
            return redirect()->back()
                ->with('error', 'Le document d\'attestation a été rejeté par l\'IA : ' . ($aiFeedback ?? 'Les informations du document ne correspondent pas au véhicule enregistré.'));
        }

        // Extraction souple et robuste de la date d'échéance (Format ISO YYYY-MM-DD ou FR DD/MM/YYYY)
        $parsedExpiration = AIService::parseFlexibleDate($dateExpirationDoc);

        if (!$parsedExpiration) {
            \Illuminate\Support\Facades\Log::warning("ContratController@renew: Impossibilité d'extraire la date d'échéance. Output IA: " . json_encode($verification));
            return redirect()->back()
                ->with('error', 'Rejet du renouvellement : L\'IA n\'a pas pu détecter de date d\'échéance lisible sur l\'attestation (Texte lu: ' . ($dateExpirationDoc ?? 'aucun') . '). Veuillez transmettre une attestation nette et lisible.');
        }

        // Vérification de la date d'expiration : doit être supérieure à aujourd'hui pour réactiver la police
        if ($parsedExpiration->isPast()) {
            return redirect()->back()
                ->with('error', 'Rejet du renouvellement : L\'attestation fournie comporte la date d\'échéance (' . $parsedExpiration->format('d/m/Y') . ') qui est déjà expirée. Pour renouveler, veuillez transmettre une nouvelle attestation en cours de validité.');
        }

        // Supprimer l'ancienne attestation s'il y en a une
        if ($contrat->attestation_assurance) {
            Storage::disk('public')->delete($contrat->attestation_assurance);
        }

        $newPath = $file->store('contrats/attestations', 'public');

        $updateData = [
            'attestation_assurance' => $newPath,
            'attestation_ai_status' => $aiStatus,
            'attestation_ai_feedback' => $aiFeedback,
            'date_fin' => $parsedExpiration->format('Y-m-d'),
        ];

        // Mettre à jour le N° de police si détecté par l'IA sur la nouvelle attestation
        if (!empty($numeroContratDoc)) {
            $updateData['numero_contrat'] = trim($numeroContratDoc);
        }

        // Mettre à jour l'assureur si détecté
        if (!empty($nomAssureurDocument)) {
            $cleanedName = trim($nomAssureurDocument);
            $matchingAssureur = User::where('role', 'assurance')
                ->where(function ($query) use ($cleanedName) {
                    $query->where('name', 'LIKE', '%' . $cleanedName . '%')
                        ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$cleanedName]);
                })
                ->first();

            $updateData['assurance_id'] = $matchingAssureur ? $matchingAssureur->id : null;
            $updateData['nom_assureur'] = $cleanedName;
        }

        $contrat->update($updateData);

        return redirect()->route('assure.contrats.index')
            ->with('success', 'L\'attestation d\'assurance pour le véhicule ' . $contrat->plaque . ' a été renouvelée avec succès ! Nouvelle échéance : ' . $parsedExpiration->format('d/m/Y'));
    }

    /**
     * Scan d'une attestation d'assurance par IA (AJAX) pour pré-remplissage automatique des champs.
     */
    public function scanAttestationAI(Request $request, AIService $aiService)
    {
        set_time_limit(120);

        $request->validate([
            'attestation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('attestation');
        $tempPath = $file->getPathname();

        $result = $aiService->extractAttestationData($tempPath);

        if (($result['status'] ?? '') === 'success') {
            $data = $result['data'] ?? [];
            $existingContratId = null;
            $existingPlaque = null;

            if (!empty($data['plaque'])) {
                $plaqueClean = strtoupper(preg_replace('/\s+/', '', $data['plaque']));
                $found = auth('user')->user()->contrats()->get()->first(function ($c) use ($plaqueClean) {
                    return strtoupper(preg_replace('/\s+/', '', $c->plaque)) === $plaqueClean;
                });

                if ($found) {
                    $existingContratId = $found->id;
                    $existingPlaque = $found->plaque;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Attestation analysée avec succès par l\'IA.',
                'data'    => $data,
                'existing_contrat_id' => $existingContratId,
                'existing_plaque' => $existingPlaque
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de l\'analyse de l\'attestation.'
        ], 422);
    }
}
