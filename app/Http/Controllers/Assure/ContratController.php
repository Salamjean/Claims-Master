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
    public function create()
    {
        return view('assure.contrats.create');
    }

    /**
     * Enregistre un nouveau contrat
     */
    public function store(Request $request, AIService $aiService)
    {
        $request->validate([
            'numero_contrat' => 'required|string|unique:contrats,numero_contrat',
            'assurance_id' => 'nullable|exists:users,id',
            'plaque' => 'required|string',
            'marque' => 'required|string',
            'modele' => 'required|string',
            'type_vehicule' => 'required|string',
            'immatriculation' => 'required|string',
            'document_pdf' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'attestation_assurance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carte_grise' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visite_technique' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'permis_conduire' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // --- ÉTAPE 1 : Vérification visuelle du Modèle (Template) ---
        if ($request->hasFile('attestation_assurance')) {
            $tempPath = $request->file('attestation_assurance')->getPathname();
            $templateCheck = $aiService->verifyTemplateMatch($tempPath);

            if ($templateCheck && isset($templateCheck['status']) && $templateCheck['status'] === 'invalid') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Le format de l\'attestation ne correspond pas au modèle standard ASACI : ' . ($templateCheck['feedback'] ?? 'Document non reconnu.'));
            }
        }

        // --- ÉTAPE 2 : Vérification IA de la plaque d'immatriculation sur l'Attestation ---
        $aiStatus = 'pending';
        $aiFeedback = null;

        if ($request->hasFile('attestation_assurance')) {
            $mime = $request->file('attestation_assurance')->getMimeType();

            if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                $tempPath = $request->file('attestation_assurance')->getPathname();
                $verification = $aiService->verifyAttestation($tempPath, $request->only(['plaque']));

                $aiStatus = $verification['status'] ?? 'pending';
                $aiFeedback = $verification['feedback'] ?? null;
                $nomAssureurDocument = $verification['assureur'] ?? null;

                if ($aiStatus === 'invalid') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Le document d\'attestation a été rejeté par l\'IA : ' . ($aiFeedback ?? 'La plaque d\'immatriculation ne correspond pas.'));
                }

                // --- ÉTAPE 3 : Attribution automatique de l'assureur ---
                if (!empty($nomAssureurDocument)) {
                    $cleanedName = trim($nomAssureurDocument);

                    // Recherche robuste : on cherche si le nom du document est contenu dans le nom de l'utilisateur OU vice-versa
                    $matchingAssureur = \App\Models\User::where('role', 'assurance')
                        ->where(function ($query) use ($cleanedName) {
                            $query->where('name', 'LIKE', '%' . $cleanedName . '%')
                                ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$cleanedName]);
                        })
                        ->first();

                    if ($matchingAssureur) {
                        $dataInsurer['assurance_id'] = $matchingAssureur->id;
                    } else {
                        $dataInsurer['assurance_id'] = null; // Pas trouvé en base, on ne lie pas à un utilisateur
                    }
                    $dataInsurer['nom_assureur'] = $cleanedName;
                }
            }
        }

        $user = auth('user')->user();

        $data = $request->except(['document_pdf', 'attestation_assurance', 'carte_grise', 'visite_technique', 'permis_conduire']);
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
}
