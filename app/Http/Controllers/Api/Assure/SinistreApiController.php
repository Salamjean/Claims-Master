<?php

namespace App\Http\Controllers\Api\Assure;

use App\Http\Controllers\Controller;
use App\Models\Constat;
use App\Models\Contrat;
use App\Models\DocumentRequis;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;
use App\Models\SinistreDocumentSoumis;
use App\Models\User;
use App\Notifications\NewSinistreNotification;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SinistreApiController extends Controller
{
    /**
     * GET /api/v1/assure/sinistres
     * Liste des sinistres de l'assuré avec filtres optionnels par statut.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les assurés authentifiés peuvent consulter leurs sinistres.'
            ], 403);
        }

        $query = Sinistre::where('user_id', $user->id)
            ->with(['contrat', 'assignedAgent', 'service', 'constat']);

        // Filtre optionnel par statut
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'historique') {
                $query->whereIn('status', ['traite', 'cloture']);
            } else {
                $query->where('status', $status);
            }
        }

        $sinistres = $query->latest()->get()->map(fn(Sinistre $s) => $this->formatSinistre($s));

        return response()->json([
            'success' => true,
            'count'   => $sinistres->count(),
            'data'    => $sinistres
        ], 200);
    }

    /**
     * GET /api/v1/assure/sinistres/{id}
     * Détails complets d'un sinistre (avec relations, constat, documents attendus).
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

        $sinistre = Sinistre::where('user_id', $user->id)
            ->with([
                'contrat',
                'assignedAgent',
                'service',
                'nearestHospital',
                'constat.hospital',
                'documentsAttendus.documentsSoumis'
            ])
            ->find($id);

        if (!$sinistre) {
            return response()->json([
                'success' => false,
                'message' => 'Sinistre introuvable ou vous n’avez pas les droits d’accès.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatSinistreDetail($sinistre)
        ], 200);
    }

    /**
     * POST /api/v1/assure/sinistres
     * Déclaration d'un nouveau sinistre par l'assuré connecté.
     */
    public function store(Request $request, AIService $aiService)
    {
        set_time_limit(120); // Temps étendu pour Haversine, IA & emails
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'contrat_id'            => ['required', 'exists:contrats,id'],
            'type_sinistre'         => ['required'], // Array ou String (séparé par virgules)
            'latitude'              => ['required', 'numeric'],
            'longitude'             => ['required', 'numeric'],
            'lieu'                  => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'photos'                => ['nullable', 'array', 'max:5'],
            'photos.*'              => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'methode_constat'       => ['nullable', 'string', 'in:Amiable,Police_Gendarmerie'],
            'assistance_sollicitee' => ['nullable', 'boolean'],
            'nom_assisteur'         => ['nullable', 'string', 'max:255'],
            'amiable_data'          => ['nullable', 'string'],
        ], [
            'contrat_id.required'   => 'Le choix du véhicule / contrat d’assurance est obligatoire.',
            'contrat_id.exists'     => 'Le contrat d’assurance sélectionné n’existe pas.',
            'type_sinistre.required'=> 'Le type de sinistre est obligatoire.',
            'latitude.required'     => 'La position géographique (latitude) est obligatoire.',
            'longitude.required'    => 'La position géographique (longitude) est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Vérification de sécurité du contrat
        $selectedContrat = Contrat::where('client_id', $user->id)->find($request->contrat_id);
        if (!$selectedContrat) {
            return response()->json([
                'success' => false,
                'message' => 'Le véhicule sélectionné n’existe pas ou ne vous appartient pas.'
            ], 403);
        }

        if ($selectedContrat->date_fin && Carbon::parse($selectedContrat->date_fin)->isPast() && !Carbon::parse($selectedContrat->date_fin)->isToday()) {
            return response()->json([
                'success' => false,
                'message' => 'L’assurance de ce véhicule a expiré le ' . Carbon::parse($selectedContrat->date_fin)->format('d/m/Y') . '. Impossible de déclarer un sinistre pour un véhicule non assuré.'
            ], 422);
        }

        // Normalisation du type_sinistre
        if (is_array($request->type_sinistre)) {
            $typeSinistreArray = $request->type_sinistre;
            $typeSinistreStr = implode(', ', $typeSinistreArray);
        } else {
            $typeSinistreStr = trim($request->type_sinistre);
            $typeSinistreArray = array_map('trim', explode(',', $typeSinistreStr));
        }

        // Enregistrement des photos transmises
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('sinistres_photos', 'public');
            }
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        // Algorithme Haversine : Recherche du service / agent de secours le plus proche
        $isOnlyBrisDeGlace = count($typeSinistreArray) === 1 && in_array('Bris_de_glace', $typeSinistreArray);

        $nearbyUnits = collect();
        if ($request->methode_constat !== 'Amiable' && !$isOnlyBrisDeGlace) {
            $nearbyUnits = User::whereIn('role', ['police', 'gendarmerie', 'agent'])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'latitude', 'longitude', 'role', 'contact', 'service_id')
                ->selectRaw("
                    ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?)
                    ) + sin( radians(?) ) *
                    sin( radians( latitude ) ) )
                    ) AS distance
                ", [$userLat, $userLng, $userLat])
                ->with('service:id,name')
                ->orderBy('distance')
                ->take(3)
                ->get();
        }

        $closestUnit = $nearbyUnits->first();
        $assignedServiceId = null;
        $assignedAgentId = null;

        if ($closestUnit) {
            if ($closestUnit->role === 'agent') {
                $assignedAgentId = $closestUnit->id;
                $assignedServiceId = $closestUnit->service_id;
            } else {
                $assignedServiceId = $closestUnit->id;
            }
        }

        // Hôpital / Caserne sapeurs-pompiers le plus proche si Accident corporel
        $nearestHospital = null;
        if (in_array('Accident_corporel', $typeSinistreArray)) {
            $nearestHospital = User::where('role', 'hopital')
                ->where('has_ambulance', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'latitude', 'longitude', 'role', 'contact', 'adresse')
                ->selectRaw("
                    ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?)
                    ) + sin( radians(?) ) *
                    sin( radians( latitude ) ) )
                    ) AS distance
                ", [$userLat, $userLng, $userLat])
                ->orderBy('distance')
                ->first();
        }

        // Création du sinistre
        $sinistre = Sinistre::create([
            'user_id'               => $user->id,
            'contrat_id'            => $selectedContrat->id,
            'assurance_id'          => $selectedContrat->assurance_id,
            'type_sinistre'         => $typeSinistreStr,
            'description'           => $request->description,
            'latitude'              => $userLat,
            'longitude'             => $userLng,
            'lieu'                  => $request->lieu,
            'photos'                => !empty($photoPaths) ? $photoPaths : null,
            'status'                => 'en_attente',
            'assigned_service_id'   => $assignedServiceId,
            'assigned_agent_id'     => $assignedAgentId,
            'methode_constat'       => $request->methode_constat ?? 'Police_Gendarmerie',
            'assistance_sollicitee' => $request->boolean('assistance_sollicitee'),
            'nom_assisteur'         => $request->nom_assisteur,
            'nearest_hospital_id'   => $nearestHospital?->id,
            'nearby_units'          => $nearbyUnits->map(fn($u) => [
                'id'                => $u->id,
                'name'              => $u->name,
                'role'              => $u->role,
                'distance'          => round($u->distance, 2),
                'contact'           => $u->contact,
                'parent_service'    => ($u->role === 'agent' && $u->service) ? $u->service->name : null,
                'parent_service_id' => ($u->role === 'agent') ? $u->service_id : $u->id,
            ])->toArray(),
        ]);

        // Traitement du constat amiable si applicable
        if ($request->methode_constat === 'Amiable' && $request->filled('amiable_data')) {
            $dataAmiable = json_decode($request->amiable_data, true);

            $croquisPath = !empty($dataAmiable['croquis']) ? $this->saveBase64Image($dataAmiable['croquis'], 'croquis') : null;
            $sigAPath    = !empty($dataAmiable['signature_a']) ? $this->saveBase64Image($dataAmiable['signature_a'], 'signature_a') : null;
            $sigBPath    = !empty($dataAmiable['signature_b']) ? $this->saveBase64Image($dataAmiable['signature_b'], 'signature_b') : null;

            Constat::create([
                'sinistre_id'       => $sinistre->id,
                'assurance_id'      => $sinistre->assurance_id,
                'user_id'           => $sinistre->user_id,
                'status'            => 'termine',
                'description'       => $sinistre->description,
                'lieu'              => $sinistre->lieu,
                'methode_redaction' => 'Amiable',
                'redaction_contenu' => $request->amiable_data,
                'croquis'           => $croquisPath,
                'ass1_photo'        => $sigAPath,
                'ass2_photo'        => $sigBPath,
            ]);
        }

        // Notification e-mail à l'Assureur si rattaché
        if ($sinistre->assurance_id) {
            $assurance = User::find($sinistre->assurance_id);
            if ($assurance) {
                try {
                    $sinistre->load(['assure', 'contrat']);
                    $assurance->notify(new NewSinistreNotification($sinistre, $assurance));
                } catch (\Exception $e) {
                    Log::error("Erreur d'envoi notification email assureur via API: " . $e->getMessage());
                }
            }
        }

        // Workflow IA - Génération automatique des documents attendus
        try {
            $documentsDisponibles = DocumentRequis::where('type_sinistre', $typeSinistreStr)
                ->when($sinistre->assurance_id, fn($q) => $q->where('user_id', $sinistre->assurance_id))
                ->get();

            $docNames = $documentsDisponibles->pluck('nom_document')->toArray();
            $descriptionAnalyse = $request->description ?? "Déclaration de sinistre de type " . $typeSinistreStr;

            $report = $aiService->analyzeDeclarationText($typeSinistreStr, $descriptionAnalyse, $docNames);

            if ($report && isset($report['recommended_docs'])) {
                $sinistre->update([
                    'ai_analysis_status' => 'analyzed',
                    'ai_analysis_report' => $report,
                    'workflow_step'      => 'docs_pending',
                ]);

                $docsAcreer = array_filter($report['recommended_docs'], fn($d) => !AIService::isDocumentExcluded($d));

                foreach ($docsAcreer as $docName) {
                    $baseDoc = $documentsDisponibles->firstWhere('nom_document', $docName);
                    $type = $baseDoc ? $baseDoc->type_champ : 'file';
                    $isMandatory = AIService::isDocumentMandatory($docName);

                    SinistreDocumentAttendu::firstOrCreate([
                        'sinistre_id'  => $sinistre->id,
                        'nom_document' => $docName,
                    ], [
                        'type_champ'   => $type,
                        'is_mandatory' => $isMandatory,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Erreur workflow IA déclaration sinistre via API : " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre déclaration de sinistre a été enregistrée avec succès.',
            'data'    => $this->formatSinistreDetail($sinistre->fresh(['contrat', 'assignedAgent', 'service', 'nearestHospital', 'constat', 'documentsAttendus']))
        ], 201);
    }

    /**
     * DELETE /api/v1/assure/sinistres/{id}
     * Annule / Supprime un sinistre (uniquement si le statut est encore 'en_attente').
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

        $sinistre = Sinistre::where('user_id', $user->id)->find($id);

        if (!$sinistre) {
            return response()->json([
                'success' => false,
                'message' => 'Sinistre introuvable.'
            ], 404);
        }

        if ($sinistre->status !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les sinistres avec le statut "en_attente" peuvent être supprimés.'
            ], 422);
        }

        if (is_array($sinistre->photos)) {
            foreach ($sinistre->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $sinistre->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre déclaration de sinistre a été supprimée avec succès.'
        ], 200);
    }

    /**
     * GET /api/v1/assure/sinistres/{id}/tracking
     * Récupère la position GPS et les informations de suivi de l'agent en intervention.
     */
    public function tracking(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $sinistre = Sinistre::where('user_id', $user->id)
            ->with(['assignedAgent', 'service'])
            ->find($id);

        if (!$sinistre) {
            return response()->json([
                'success' => false,
                'message' => 'Sinistre introuvable.'
            ], 404);
        }

        if (!$sinistre->assigned_agent_id && !$sinistre->assigned_service_id) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune unité de secours n’a encore été assignée à ce sinistre.'
            ], 404);
        }

        $agent = $sinistre->assignedAgent;
        $service = $sinistre->service;

        return response()->json([
            'success' => true,
            'data'    => [
                'sinistre_id'          => $sinistre->id,
                'status'               => $sinistre->status,
                'sinistre_latitude'    => (float) $sinistre->latitude,
                'sinistre_longitude'   => (float) $sinistre->longitude,
                'agent_start_latitude' => $sinistre->agent_start_latitude ? (float) $sinistre->agent_start_latitude : null,
                'agent_start_longitude'=> $sinistre->agent_start_longitude ? (float) $sinistre->agent_start_longitude : null,
                'assigned_agent'       => $agent ? [
                    'id'        => $agent->id,
                    'name'      => $agent->name,
                    'prenom'    => $agent->prenom,
                    'contact'   => $agent->contact,
                    'latitude'  => $agent->latitude ? (float) $agent->latitude : null,
                    'longitude' => $agent->longitude ? (float) $agent->longitude : null,
                ] : null,
                'assigned_service'     => $service ? [
                    'id'      => $service->id,
                    'name'    => $service->name,
                    'contact' => $service->contact,
                ] : null,
            ]
        ], 200);
    }

    /**
     * POST /api/v1/assure/sinistres/documents/{documentAttenduId}/upload
     * Upload d'un document requis pour un sinistre avec analyse IA.
     */
    public function uploadDocument(Request $request, $documentAttenduId, AIService $aiService)
    {
        set_time_limit(120);
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $documentAttendu = SinistreDocumentAttendu::with('sinistre')->find($documentAttenduId);

        if (!$documentAttendu || $documentAttendu->sinistre->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Document attendu introuvable.'
            ], 404);
        }

        $sinistre = $documentAttendu->sinistre;

        if ($sinistre->status === 'cloture') {
            return response()->json([
                'success' => false,
                'message' => 'Le dossier de ce sinistre est clôturé. Ajout de pièce impossible.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'document_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'document_text' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!$request->hasFile('document_file') && empty($request->document_text)) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez fournir un fichier document ou une valeur textuelle.'
            ], 400);
        }

        $soumis = new SinistreDocumentSoumis();
        $soumis->sinistre_document_attendu_id = $documentAttendu->id;

        $aiStatus = 'valid';
        $aiFeedback = null;

        if ($documentAttendu->type_champ === 'file' && $request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('sinistres_documents', 'public');
            $soumis->file_path = $path;

            $mime = $request->file('document_file')->getClientMimeType();
            if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
                $fullPath = storage_path('app/public/' . $path);
                $verification = $aiService->verifyDocumentImage($fullPath, $documentAttendu->nom_document);

                if (is_array($verification) && isset($verification['status'])) {
                    $aiStatus = $verification['status'];
                    $aiFeedback = $verification['feedback'] ?? null;
                } else {
                    $aiStatus = 'pending';
                    $aiFeedback = "L'analyse IA a échoué. Le document sera vérifié manuellement.";
                }
            }
        } else {
            $soumis->file_value = $request->document_text;
            $aiFeedback = "Valeur textuelle enregistrée avec succès.";
        }

        $soumis->ai_compliance_status = $aiStatus;
        $soumis->ai_feedback = $aiFeedback;
        $soumis->save();

        $documentAttendu->status_client = ($aiStatus === 'invalid') ? 'rejected' : 'uploaded';
        $documentAttendu->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Document soumis avec succès.',
            'ai_status' => $aiStatus,
            'feedback'  => $aiFeedback,
            'data'      => [
                'id'                 => $soumis->id,
                'file_url'           => $soumis->file_path ? asset('storage/' . $soumis->file_path) : null,
                'file_value'         => $soumis->file_value,
                'compliance_status'  => $soumis->ai_compliance_status,
                'created_at'         => $soumis->created_at?->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * Sauvegarde une image base64 (constat amiable)
     */
    private function saveBase64Image(string $base64String, string $prefix): ?string
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $data = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data === false) return null;

                $filename = $prefix . '_' . uniqid() . '.' . $type;
                $path = 'constats_amiables/' . $filename;
                Storage::disk('public')->put($path, $data);
                return $path;
            }
        } catch (\Exception $e) {
            Log::error("Erreur sauvegarde image base64 via API: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Formatage simple pour la liste
     */
    private function formatSinistre(Sinistre $sinistre): array
    {
        return [
            'id'                    => $sinistre->id,
            'code_sinistre'         => $sinistre->code_sinistre ?? 'SIN-' . $sinistre->id,
            'type_sinistre'         => $sinistre->type_sinistre,
            'status'                => $sinistre->status,
            'description'           => $sinistre->description,
            'lieu'                  => $sinistre->lieu,
            'latitude'              => (float) $sinistre->latitude,
            'longitude'             => (float) $sinistre->longitude,
            'methode_constat'       => $sinistre->methode_constat,
            'assistance_sollicitee' => (bool) $sinistre->assistance_sollicitee,
            'photos_urls'           => is_array($sinistre->photos)
                ? array_map(fn($p) => asset('storage/' . $p), $sinistre->photos)
                : [],
            'contrat'               => $sinistre->contrat ? [
                'id'             => $sinistre->contrat->id,
                'numero_contrat' => $sinistre->contrat->numero_contrat,
                'plaque'         => $sinistre->contrat->plaque,
                'marque'         => $sinistre->contrat->marque,
                'modele'         => $sinistre->contrat->modele,
            ] : null,
            'assigned_agent'        => $sinistre->assignedAgent ? [
                'id'      => $sinistre->assignedAgent->id,
                'name'    => $sinistre->assignedAgent->name,
                'prenom'  => $sinistre->assignedAgent->prenom,
                'contact' => $sinistre->assignedAgent->contact,
            ] : null,
            'created_at'            => $sinistre->created_at?->toIso8601String(),
        ];
    }

    /**
     * Formatage détaillé pour une vue unique
     */
    private function formatSinistreDetail(Sinistre $sinistre): array
    {
        $base = $this->formatSinistre($sinistre);

        $base['nom_assisteur']      = $sinistre->nom_assisteur;
        $base['ai_analysis_status'] = $sinistre->ai_analysis_status;
        $base['ai_analysis_report'] = $sinistre->ai_analysis_report;
        $base['workflow_step']      = $sinistre->workflow_step;
        $base['nearby_units']       = $sinistre->nearby_units;

        $base['service'] = $sinistre->service ? [
            'id'      => $sinistre->service->id,
            'name'    => $sinistre->service->name,
            'contact' => $sinistre->service->contact,
        ] : null;

        $base['nearest_hospital'] = $sinistre->nearestHospital ? [
            'id'      => $sinistre->nearestHospital->id,
            'name'    => $sinistre->nearestHospital->name,
            'contact' => $sinistre->nearestHospital->contact,
            'adresse' => $sinistre->nearestHospital->adresse,
        ] : null;

        $base['constat'] = $sinistre->constat ? [
            'id'                 => $sinistre->constat->id,
            'code_constat'       => $sinistre->constat->code_constat,
            'methode_redaction'  => $sinistre->constat->methode_redaction,
            'redaction_validee'  => (bool) $sinistre->constat->redaction_validee,
            'statut_paiement'    => $sinistre->constat->statut_paiement,
            'redaction_pdf_url'  => $sinistre->constat->redaction_pdf ? asset('storage/' . $sinistre->constat->redaction_pdf) : null,
        ] : null;

        $base['documents_attendus'] = $sinistre->documentsAttendus ? $sinistre->documentsAttendus->map(function($doc) {
            return [
                'id'             => $doc->id,
                'nom_document'   => $doc->nom_document,
                'type_champ'     => $doc->type_champ,
                'is_mandatory'   => (bool) $doc->is_mandatory,
                'status_client'  => $doc->status_client,
                'fichiers_soumis'=> $doc->documentsSoumis ? $doc->documentsSoumis->map(function($soumis) {
                    return [
                        'id'                    => $soumis->id,
                        'file_url'              => $soumis->file_path ? asset('storage/' . $soumis->file_path) : null,
                        'file_value'            => $soumis->file_value,
                        'ai_compliance_status'  => $soumis->ai_compliance_status,
                        'ai_feedback'           => $soumis->ai_feedback,
                        'created_at'            => $soumis->created_at?->toIso8601String(),
                    ];
                }) : [],
            ];
        }) : [];

        return $base;
    }
}
