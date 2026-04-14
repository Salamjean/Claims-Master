<?php

namespace App\Http\Controllers\Assure;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewSinistreNotification;

class SinistreController extends Controller
{
    /**
     * Affiche le formulaire de déclaration.
     */
    public function create()
    {
        $assurances = User::where('role', 'assurance')->get();
        $contrats = auth('user')->user()->contrats()
            ->with('assureur')
            ->where('attestation_ai_status', 'valid')
            ->get();
        return view('assure.sinistres.create', compact('assurances', 'contrats'));
    }

    /**
     * Sinistres en attente de l'assuré connecté.
     */
    public function enAttente()
    {
        $sinistres = Sinistre::with('constat')
            ->where('user_id', auth('user')->id())
            ->whereIn('status', ['en_attente', 'en_cours', 'traite'] )
            ->latest()
            ->get();
        return view('assure.sinistres.en_attente', compact('sinistres'));
    }

    /**
     * Sinistres pris en charge / en cours pour l'assuré.
     */
    public function enCours()
    {
        $sinistres = Sinistre::with('constat')
            ->where('user_id', auth('user')->id())
            ->whereIn('status', ['en_cours', 'traite'])
            ->latest()
            ->get();
        return view('assure.sinistres.en_cours', compact('sinistres'));
    }

    /**
     * Historique de tous les sinistres de l'assuré connecté.
     */
    public function historique()
    {
        $tousSinistres = Sinistre::where('user_id', auth('user')->id())->get();
        
        // L'utilisateur souhaite voir "tous ses sinistres traités" dans l'historique
        $sinistres = Sinistre::with('constat')
            ->where('user_id', auth('user')->id())
            ->whereIn('status', ['traite', 'cloture'])
            ->latest()
            ->get();
            
        return view('assure.sinistres.historique', compact('sinistres', 'tousSinistres'));
    }

    /**
     * Affiche le détail d'un sinistre.
     */
    public function show(Sinistre $sinistre)
    {
        // Sécurité : l'assuré ne peut voir que ses propres sinistres
        abort_if($sinistre->user_id !== auth('user')->id(), 403);
        $sinistre->load('service');
        return view('assure.sinistres.show', compact('sinistre'));
    }

    /**
     * Affiche la page de tracking (suivi GPS simulé de l'agent).
     */
    public function tracking(Sinistre $sinistre)
    {
        // Sécurité : l'assuré ne peut voir que ses propres sinistres
        abort_if($sinistre->user_id !== auth('user')->id(), 403);
        
        // On s'assure qu'un agent est bien assigné
        if (!$sinistre->assigned_agent_id) {
            return redirect()->route('assure.sinistres.show', $sinistre->id)
                ->with('error', 'Aucun agent n\'est encore assigné à cette intervention.');
        }

        $sinistre->load(['assignedAgent', 'service']);
        
        return view('assure.sinistres.tracking', compact('sinistre'));
    }

    /**
     * Supprime un sinistre (uniquement si en_attente).
     */
    public function destroy(Sinistre $sinistre)
    {
        abort_if($sinistre->user_id !== auth('user')->id(), 403);
        abort_if($sinistre->status !== 'en_attente', 403, 'Seuls les sinistres en attente peuvent être supprimés.');

        // Supprimer les photos du stockage
        if ($sinistre->photos) {
            foreach ($sinistre->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $sinistre->delete();

        return redirect()->route('assure.sinistres.en_attente')
            ->with('success', 'Votre déclaration a été supprimée avec succès.');
    }

    /**
     * Enregistre le sinistre et assigne le service le plus proche.
     */
    public function store(Request $request)
    {
        // Augmenter le temps limite d'exécution car plusieurs requêtes API Gemini et envois d'emails peuvent être longs
        set_time_limit(120);

        // 1. Validation de la requête
        $request->validate([
            'type_sinistre' => 'required|array|min:1',
            'type_sinistre.*' => 'string|in:Vol,Incendie,Accident_matériel,Accident_corporel,Bris_de_glace,Autre',
            'assurance_id' => 'nullable|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'methode_constat' => 'nullable|string|in:Amiable,Police_Gendarmerie',
            'assistance_sollicitee' => 'nullable|boolean',
            'nom_assisteur' => 'nullable|string|max:255',
        ]);

        // 2. Gestion de l'upload des photos (s'il y en a)
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('sinistres_photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;
        $typeSinistreArray = $request->type_sinistre;
        $typeSinistreStr = implode(', ', $typeSinistreArray);

        // 3. Algorithme de Haversine (Recherche du service le plus proche)
        // ALERTE : On n'assigne aucun service si :
        // - C'est un constat amiable
        // - C'est UNIQUEMENT un bris de glace (bris de glace pur)
        $isOnlyBrisDeGlace = count($typeSinistreArray) === 1 && in_array('Bris_de_glace', $typeSinistreArray);

        $nearbyUnits = collect();
        if ($request->methode_constat !== 'Amiable' && !$isOnlyBrisDeGlace) {
            $nearbyUnits = \App\Models\User::whereIn('role', ['police', 'gendarmerie', 'agent'])
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

        // 4. Création du sinistre en base de données
        $sinistre = Sinistre::create([
            'user_id' => auth('user')->id(),
            'type_sinistre' => $typeSinistreStr,
            'contrat_id' => $request->contrat_id,
            'assurance_id' => $request->assurance_id,
            'description' => $request->description,
            'latitude' => $userLat,
            'longitude' => $userLng,
            'lieu' => $request->lieu, // Sauvegarder l'adresse textuelle
            'photos' => !empty($photoPaths) ? $photoPaths : null,
            'status' => 'en_attente',
            'assigned_service_id' => $assignedServiceId,
            'assigned_agent_id' => $assignedAgentId,
            'methode_constat' => $request->methode_constat,
            'assistance_sollicitee' => $request->boolean('assistance_sollicitee'),
            'nom_assisteur' => $request->nom_assisteur,
            'nearby_units' => $nearbyUnits->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'distance' => round($u->distance, 2),
                'contact' => $u->contact,
                'parent_service' => ($u->role === 'agent' && $u->service) ? $u->service->name : null,
                'parent_service_id' => ($u->role === 'agent') ? $u->service_id : $u->id,
            ])->toArray(),
        ]);

        // --- TRAITEMENT CONSTAT AMIABLE ---
        if ($request->methode_constat === 'Amiable' && $request->filled('amiable_data')) {
            $data = json_decode($request->amiable_data, true);
            
            // Chemins pour les photos (Signatures et Croquis)
            $croquisPath = null;
            $sigAPath = null;
            $sigBPath = null;

            if (!empty($data['croquis'])) {
                $croquisPath = $this->saveBase64Image($data['croquis'], 'croquis');
            }
            if (!empty($data['signature_a'])) {
                $sigAPath = $this->saveBase64Image($data['signature_a'], 'signature_a');
            }
            if (!empty($data['signature_b'])) {
                $sigBPath = $this->saveBase64Image($data['signature_b'], 'signature_b');
            }

            \App\Models\Constat::create([
                'sinistre_id' => $sinistre->id,
                'assurance_id' => $sinistre->assurance_id,
                'user_id' => $sinistre->user_id,
                'status' => 'termine',
                'description' => $sinistre->description,
                'lieu' => $sinistre->lieu, // Copier le lieu dans le constat
                'methode_redaction' => 'Amiable',
                'redaction_contenu' => $request->amiable_data, // On stocke le JSON brut
                'croquis' => $croquisPath,
                'ass1_photo' => $sigAPath,
                'ass2_photo' => $sigBPath,
            ]);
        }

        // --- NOUVEAU : Notification à la Compagnie d'Assurance ---
        if ($sinistre->assurance_id) {
            $assurance = User::find($sinistre->assurance_id);
            if ($assurance) {
                // Charger les relations nécessaires pour l'email
                $sinistre->load(['assure', 'contrat']);
                $assurance->notify(new NewSinistreNotification($sinistre, $assurance));
            }
        }

        // 5. Workflow IA - Analyse automatique des documents requis
        $docsCrees = [];
        $aiService = new \App\Services\AIService();

        // Récupérer les documents configurés (si existants)
        $documentsDisponibles = \App\Models\DocumentRequis::where('type_sinistre', $request->type_sinistre)
            ->when($request->filled('assurance_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->assurance_id);
            })
            ->get();
        $docNames = $documentsDisponibles->pluck('nom_document')->toArray();

        $descriptionAnalyse = $request->description ?? "Déclaration de sinistre de type " . $typeSinistreStr;
        $report = $aiService->analyzeDeclarationText($typeSinistreStr, $descriptionAnalyse, $docNames);

        if ($report && isset($report['recommended_docs'])) {
            $sinistre->update([
                'ai_analysis_status' => 'analyzed',
                'ai_analysis_report' => $report,
                'workflow_step' => 'docs_pending' // Attente des documents
            ]);

            $docsAcreer = $report['recommended_docs'];
            foreach ($docsAcreer as $docName) {
                // Trouver le type d'input correspondant en BDD si possible, sinon defaut à 'file'
                $baseDoc = $documentsDisponibles->firstWhere('nom_document', $docName);
                $type = $baseDoc ? $baseDoc->type_champ : 'file';

                $sda = \App\Models\SinistreDocumentAttendu::create([
                    'sinistre_id' => $sinistre->id,
                    'nom_document' => $docName,
                    'type_champ' => $type,
                    'is_mandatory' => true,
                ]);
                $docsCrees[] = $sda;
            }

            // 6. Envoi de l'Email / SMS via Notification Laravel
            if (count($docsCrees) > 0) {
                $docNames = array_map(fn($d) => $d->nom_document, $docsCrees);
                $aiMessage = $aiService->generateDocumentRequestMessage($sinistre, $docNames);
                auth('user')->user()->notify(new \App\Notifications\DocumentsRequisNotification($sinistre, $docsCrees, $aiMessage));
            }
        } else {
            $sinistre->update([
                'ai_analysis_status' => 'failed',
            ]);
        }

        // 7. Redirection avec message de succès
        $message = 'Votre déclaration a bien été enregistrée.';
        if ($request->methode_constat === 'Amiable' || ($isOnlyBrisDeGlace ?? false)) {
            $message .= ' (Aucune autorité n\'a été alertée pour ce type de déclaration).';
        } elseif ($request->filled('assurance_id') && isset($docsCrees) && count($docsCrees) > 0) {
            $message .= ' Un e-mail vous a été envoyé avec la liste des documents requis par votre assurance.';
        } elseif (!$request->filled('assurance_id') && ($closestUnit ?? false)) {
            $message .= ' Elle a été automatiquement transmise aux forces de l\'ordre à proximité.';
        }

        return redirect()->route('assure.sinistres.en_attente')->with('success', $message);
    }
    
    /**
     * Retourne les 3 services les plus proches en format JSON pour la vue.
     */
    public function getNearestServices(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        $services = \App\Models\User::whereIn('role', ['police', 'gendarmerie', 'agent'])
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

        return response()->json([
            'status' => 'success',
            'services' => $services
        ]);
    }

    /**
     * Affiche la liste des sinistres nécessitant l\'upload de documents par l\'assuré.
     */
    public function documentsRequis()
    {
        $sinistres = \App\Models\Sinistre::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereHas('documentsAttendus', function ($q) {
                // On liste les sinistres qui ont encore des documents en statut pending ou rejected (si the IA rejected it)
                $q->where('status_client', 'pending');
            })
            ->orWhere('workflow_step', 'docs_pending') // Secours si la step n'a pas été changée
            ->orderBy('created_at', 'desc')
            ->get();

        // Filtrage plus precis en memoire pour s'assurer que seuls ceux de l'utilisateur root remonte bien
        $sinistres = $sinistres->where('user_id', \Illuminate\Support\Facades\Auth::id());

        return view('assure.sinistres.documents', compact('sinistres'));
    }

    /**
     * Affiche la liste des sinistres dont le constat est prêt (rédigé).
     */
    public function constatsPrets(Request $request, \App\Services\WaveService $waveService)
    {
        $sinistres = Sinistre::where('user_id', auth('user')->id())
            ->whereHas('constat', function($q) {
                $q->where('redaction_validee', true);
            })
            ->with(['constat', 'service'])
            ->latest()
            ->get();

        // --- FALLBACK : Vérification synchrone si on revient du paiement ---
        if ($request->get('payment') === 'success') {
            foreach ($sinistres as $s) {
                if ($s->constat && $s->constat->statut_paiement !== 'success' && $s->constat->wave_session_id) {
                    $session = $waveService->retrieveCheckoutSession($s->constat->wave_session_id);
                    if ($session && ($session['payment_status'] === 'succeeded' || $session['payment_status'] === 'completed' || ($session['status'] ?? '') === 'complete')) {
                        $s->constat->update(['statut_paiement' => 'success']);
                        
                        // --- Crédit du portefeuille de l'agent ---
                        if ($s->assigned_agent_id) {
                            $agent = $s->assignedAgent;
                            if ($agent) {
                                $amountToCredit = $s->constat->montant_a_payer;
                                
                                // Vérifier si un crédit n'a pas déjà été effectué
                                $exists = \App\Models\WalletTransaction::where('user_id', $agent->id)
                                    ->where('sinistre_id', $s->id)
                                    ->where('type', 'credit')
                                    ->exists();
                                    
                                if (!$exists) {
                                    $agent->increment('wallet_balance', $amountToCredit);
                                    \App\Models\WalletTransaction::create([
                                        'user_id'     => $agent->id,
                                        'sinistre_id' => $s->id,
                                        'amount'      => $amountToCredit,
                                        'type'        => 'credit',
                                        'description' => "Paiement du constat pour le sinistre #{$s->numero_sinistre} (Fallback)",
                                        'status'      => 'completed',
                                    ]);
                                }
                            }
                        }

                        // --- Crédit du portefeuille du SERVICE (Unité) ---
                        if ($s->assigned_service_id) {
                            $service = $s->service;
                            if ($service) {
                                $amountToCredit = $s->constat->montant_a_payer;
                                
                                // Vérifier si un crédit n'a pas déjà été effectué
                                $existsService = \App\Models\WalletTransaction::where('user_id', $service->id)
                                    ->where('sinistre_id', $s->id)
                                    ->where('type', 'credit')
                                    ->exists();
                                    
                                if (!$existsService) {
                                    $service->increment('wallet_balance', $amountToCredit);
                                    
                                    $agentName = $s->assignedAgent ? $s->assignedAgent->name : 'un agent';
                                    
                                    \App\Models\WalletTransaction::create([
                                        'user_id'     => $service->id,
                                        'sinistre_id' => $s->id,
                                        'amount'      => $amountToCredit,
                                        'type'        => 'credit',
                                        'description' => "Paiement du constat (Agent: {$agentName}) pour le sinistre #{$s->numero_sinistre} (Fallback)",
                                        'status'      => 'completed',
                                    ]);
                                }
                            }
                        }

                        \App\Models\ConstatPayment::updateOrCreate(
                            ['transaction_id' => $s->constat->wave_session_id],
                            [
                                'constat_id'     => $s->constat->id,
                                'amount'         => $session['amount'] ?? $s->constat->montant_a_payer,
                                'payment_method' => 'wave',
                                'status'         => 'success',
                            ]
                        );
                    }
                }
            }
        }

        // Compteur des constats non réglés (prêts mais non payés)
        $countConstatsNonRegles = Sinistre::where('user_id', auth('user')->id())
            ->whereHas('constat', function($q) {
                $q->where('redaction_validee', true)
                  ->where(function($query) {
                      $query->where('statut_paiement', '!=', 'success')
                            ->orWhereNull('statut_paiement');
                  });
            })
            ->count();

        return view('assure.sinistres.constats_prets', compact('sinistres', 'countConstatsNonRegles'));
    }

    /**
     * Affiche la page de paiement/livraison pour un constat.
     */
    public function showPaiementRetrait(Sinistre $sinistre)
    {
        abort_if($sinistre->user_id !== auth('user')->id(), 403);
        abort_unless($sinistre->constat && $sinistre->constat->redaction_validee, 404, "Le constat n'est pas encore prêt.");

        $sinistre->load(['constat', 'service']);
        return view('assure.sinistres.paiement_retrait', compact('sinistre'));
    }

    /**
     * Traite le formulaire de paiement (Redirection vers Wave).
     */
    public function processPaiementRetrait(Request $request, Sinistre $sinistre, \App\Services\WaveService $waveService)
    {
        abort_if($sinistre->user_id !== auth('user')->id(), 403);
        $constat = $sinistre->constat;
        
        $session = $waveService->createCheckoutSession(
            $constat->montant_a_payer,
            'XOF',
            route('assure.constats.prets', ['payment' => 'success']),
            route('assure.constats.paiement', [$sinistre->id, 'payment' => 'error']),
            ['constat_id' => $constat->id, 'sinistre_id' => $sinistre->id]
        );

        if ($session && isset($session['id'])) {
            // Sauvegarde de l'ID de session pour le suivi via Webhook
            $constat->update(['wave_session_id' => $session['id']]);
            
            if (isset($session['wave_launch_url'])) {
                return redirect()->away($session['wave_launch_url']);
            }
        }

        return back()->with('error', 'Impossible d\'initialiser le paiement Wave. Veuillez réessayer.');
    }

    /**
     * Webhook Wave : Reçoit la confirmation de paiement
     */
    public function waveWebhook(Request $request, \App\Services\WaveService $waveService)
    {
        $signature = $request->header('Wave-Signature');
        $body = $request->getContent();

        \Illuminate\Support\Facades\Log::info('Webhook Wave reçu', ['body' => $body]);

        if (!$waveService->verifyWebhook($body, $signature)) {
            \Illuminate\Support\Facades\Log::warning('Signature Webhook Wave invalide.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = json_decode($body, true);
        $type = $payload['type'] ?? null;

        if ($type === 'checkout.session.completed') {
            $sessionData = $payload['data']['object'] ?? $payload['data'];
            $waveSessionId = $sessionData['id'] ?? null;
            $amount = $sessionData['amount'] ?? 0;

            if ($waveSessionId) {
                // Recherche par Session ID puisque Wave CI rejette 'metadata'
                $constat = \App\Models\Constat::where('wave_session_id', $waveSessionId)->first();
                
                if ($constat) {
                    \Illuminate\Support\Facades\Log::info('Paiement Wave matché pour le constat', ['id' => $constat->id]);
                    
                    $constat->update(['statut_paiement' => 'success']);
                    
                    // --- Crédit du portefeuille de l'agent ---
                    $sinistre = $constat->sinistre;
                    if ($sinistre && $sinistre->assigned_agent_id) {
                        $agent = $sinistre->assignedAgent;
                        if ($agent) {
                            $amountToCredit = $constat->montant_a_payer;
                            
                            // Vérifier si un crédit n'a pas déjà été effectué pour ce sinistre
                            $exists = \App\Models\WalletTransaction::where('user_id', $agent->id)
                                ->where('sinistre_id', $sinistre->id)
                                ->where('type', 'credit')
                                ->exists();
                                
                            if (!$exists) {
                                $agent->increment('wallet_balance', $amountToCredit);
                                \App\Models\WalletTransaction::create([
                                    'user_id'     => $agent->id,
                                    'sinistre_id' => $sinistre->id,
                                    'amount'      => $amountToCredit,
                                    'type'        => 'credit',
                                    'description' => "Paiement du constat pour le sinistre #{$sinistre->numero_sinistre}",
                                    'status'      => 'completed',
                                ]);
                                \Illuminate\Support\Facades\Log::info('Portefeuille agent crédité', ['agent_id' => $agent->id, 'amount' => $amountToCredit]);
                            }
                        }
                    }

                    // --- Crédit du portefeuille du SERVICE (Unité) ---
                    if ($sinistre && $sinistre->assigned_service_id) {
                        $service = $sinistre->service;
                        if ($service) {
                            $amountToCredit = $constat->montant_a_payer;
                            
                            // Vérifier si un crédit n'a pas déjà été effectué pour ce service et ce sinistre
                            $existsService = \App\Models\WalletTransaction::where('user_id', $service->id)
                                ->where('sinistre_id', $sinistre->id)
                                ->where('type', 'credit')
                                ->exists();
                                
                            if (!$existsService) {
                                $service->increment('wallet_balance', $amountToCredit);
                                
                                $agentName = $sinistre->assignedAgent ? $sinistre->assignedAgent->name : 'un agent';
                                
                                \App\Models\WalletTransaction::create([
                                    'user_id'     => $service->id,
                                    'sinistre_id' => $sinistre->id,
                                    'amount'      => $amountToCredit,
                                    'type'        => 'credit',
                                    'description' => "Paiement du constat (Agent: {$agentName}) pour le sinistre #{$sinistre->numero_sinistre}",
                                    'status'      => 'completed',
                                ]);
                                \Illuminate\Support\Facades\Log::info('Portefeuille service crédité', ['service_id' => $service->id, 'amount' => $amountToCredit]);
                            }
                        }
                    }

                    \App\Models\ConstatPayment::updateOrCreate(
                        ['transaction_id' => $waveSessionId],
                        [
                            'constat_id'     => $constat->id,
                            'amount'         => $amount,
                            'payment_method' => 'wave',
                            'status'         => 'success',
                        ]
                    );
                } else {
                    \Illuminate\Support\Facades\Log::error('Session Wave non trouvée en base', ['session_id' => $waveSessionId]);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    /**
     * Télécharge le constat amiable au format PDF
     */
    public function downloadConstat($id)
    {
        $sinistre = Sinistre::with('constat')->findOrFail($id);
        $constat = $sinistre->constat;

        if (!$constat || $constat->methode_redaction !== 'Amiable') {
            return back()->with('error', 'Aucun constat amiable trouvé pour ce sinistre.');
        }

        $data = json_decode($constat->redaction_contenu, true);
        
        // Note: Nécessite barryvdh/laravel-dompdf
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.constat_amiable', [
                'sinistre' => $sinistre,
                'constat' => $constat,
                'data' => $data
            ]);
            return $pdf->download('constat_amiable_' . ($sinistre->numero_sinistre ?? $sinistre->id) . '.pdf');
        } catch (\Exception $e) {
            \Log::error("Erreur génération PDF : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du PDF. Assurez-vous que les dépendances sont installées.');
        }
    }

    /**
     * Sauvegarde une image base64 dans le storage
     */
    private function saveBase64Image($base64String, $prefix)
    {
        try {
            if (empty($base64String)) return null;
            
            $image_parts = explode(";base64,", $base64String);
            if (count($image_parts) < 2) return null;
            
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'png';
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = $prefix . '_' . uniqid() . '.' . $image_type;
            $path = 'constats/' . $fileName;
            
            \Storage::disk('public')->put($path, $image_base64);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error("Erreur sauvegarde image base64 : " . $e->getMessage());
            return null;
        }
    }
}
