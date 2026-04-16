<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\Constat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'agent
     */
    public function dashboard()
    {
        $agent = auth('user')->user();

        // Le dashboard montre le POOL GÉNÉRAL (nouveaux sinistres à récupérer)
        $sinistres = Sinistre::whereInvolved($agent->id, $agent->service_id)
            ->whereNull('assigned_agent_id')
            ->where('status', 'en_attente')
            ->with('assure')
            ->latest()
            ->take(10)
            ->get();

        $totalPublic = Sinistre::whereInvolved($agent->id, $agent->service_id)
            ->whereNull('assigned_agent_id')
            ->where('status', 'en_attente')
            ->count();

        // Stats personnelles pour les compteurs
        $enAttente = Sinistre::where('assigned_agent_id', $agent->id)->where('status', 'en_attente')->count();
        $enCours   = Sinistre::where('assigned_agent_id', $agent->id)->whereIn('status', ['en_cours', 'constat_terrain_ok'])->count();
        $cloture   = Sinistre::where('assigned_agent_id', $agent->id)->where('status', 'cloture')->count();

        return view('agent.dashboard', compact('agent', 'sinistres', 'totalPublic', 'enAttente', 'enCours', 'cloture'));
    }

    /**
     * Endpoint JSON pour l'auto-refresh
     */
    public function sinistresJson()
    {
        $agent = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($agent->id, $agent->service_id)
            ->whereNull('assigned_agent_id')
            ->where('status', 'en_attente')
            ->with('assure')
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'numero_sinistre' => $s->numero_sinistre,
                'assure' => $s->assure->name ?? '—',
                'prenom' => $s->assure->prenom ?? '',
                'code_user' => $s->assure->code_user ?? 'Client',
                'type_sinistre' => str_replace('_', ' ', $s->type_sinistre),
                'photos_count' => count($s->photos ?? []),
                'status' => $s->status,
                'created_at' => $s->created_at->format('d/m/Y H:i'),
                'assigned_agent_id' => $s->assigned_agent_id,
            ]);
        return response()->json($sinistres);
    }

    public function enAttente()
    {
        $agent = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($agent->id, $agent->service_id)
            ->where('status', 'en_attente')
            ->whereNull('assigned_agent_id')
            ->with('assure')
            ->latest()
            ->get();

        return view('agent.sinistres.en_attente', compact('sinistres'));
    }

    public function mesDossiers()
    {
        $agent = auth('user')->user();
        $sinistres = Sinistre::with('assure', 'constat')
            ->where('assigned_agent_id', $agent->id)
            ->whereIn('status', ['en_attente', 'en_cours', 'constat_terrain_ok'])
            ->latest()
            ->get();

        return view('agent.sinistres.mes_dossiers', compact('sinistres'));
    }

    public function historique()
    {
        $agent = auth('user')->user();
        // L'historique ne montre désormais que les dossiers traités par l'agent lui-même
        $sinistres = Sinistre::with('assure', 'assignedAgent')
            ->where('assigned_agent_id', $agent->id)
            ->whereIn('status', ['traite', 'cloture'])
            ->latest()
            ->get();

        return view('agent.sinistres.historique', compact('sinistres'));
    }

    public function claimSinistre(Request $request, Sinistre $sinistre)
    {
        $agent = auth('user')->user();

        // Sécurité : l'agent doit être impliqué (par station ou par proximité)
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($agent->id, $agent->service_id)->exists(), 403);

        // Si déjà attribué à quelqu'un d'autre
        if ($sinistre->assigned_agent_id && $sinistre->assigned_agent_id !== $agent->id) {
            return back()->with('error', 'Ce sinistre est déjà pris en charge par un autre agent.');
        }

        $sinistre->update([
            'assigned_agent_id'  => $agent->id,
            'assigned_service_id' => $agent->service_id,
            'status'             => 'en_cours',
            // Capture de la position GPS de l'agent au moment de la récupération
            'agent_start_lat'    => $request->input('agent_lat') ?? $agent->latitude,
            'agent_start_lng'    => $request->input('agent_lng') ?? $agent->longitude,
        ]);

        // Notification à l'assuré
        if ($sinistre->assure) {
            $sinistre->assure->notify(new \App\Notifications\SinistreClaimedNotification($sinistre, $agent));
        }

        return redirect()->route('agent.sinistres.show', $sinistre->id)
            ->with('success', 'Le sinistre vous a été attribué avec succès. L\'assuré a été informé que vous êtes en route.');
    }

    public function showSinistre(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($agent->id, $agent->service_id)->exists(), 403);
        $sinistre->load(['assure', 'constat']);
        return view('agent.sinistres.sinistre_show', compact('sinistre'));
    }

    public function createConstat(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($agent->id, $agent->service_id)->exists(), 403);
        $sinistre->load('assure');
        $isAccident = in_array($sinistre->type_sinistre, ['Accident_matériel', 'Accident_corporel']);
        return view('agent.sinistres.constat', compact('sinistre', 'isAccident'));
    }

    public function storeConstat(Request $request, Sinistre $sinistre)
    {
        abort_if($sinistre->assigned_service_id !== auth('user')->user()->service_id, 403);

        $data = $request->except(['_token', 'ass1_photo', 'ass2_photo', 'croquis', 'photos_plus']);
        $data['sinistre_id'] = $sinistre->id;
        $data['service_id'] = auth('user')->user()->service_id;

        // Gestion des photos d'assurance et du croquis
        foreach (['ass1_photo', 'ass2_photo'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('constats', 'public');
            }
        }

        // Gestion du croquis (Photo ou Dessin)
        if ($request->hasFile('croquis_file')) {
            $data['croquis'] = $request->file('croquis_file')->store('constats/croquis', 'public');
        } elseif ($request->filled('croquis_data')) {
            $imageData = $request->input('croquis_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
                $fileName = 'croquis_' . time() . '.png';
                $filePath = 'constats/croquis/' . $fileName;
                Storage::disk('public')->put($filePath, $imageData);
                $data['croquis'] = $filePath;
            }
        }

        // Gestion des photos supplémentaires
        if ($request->hasFile('photos_plus')) {
            $paths = [];
            foreach ($request->file('photos_plus') as $file) {
                $paths[] = $file->store('constats/annexes', 'public');
            }
            $data['photos_plus'] = $paths;
        }

        \App\Models\Constat::updateOrCreate(
            ['sinistre_id' => $sinistre->id],
            array_merge($data, ['terrain_valide' => true])
        );

        // Étape 1 : Constat terrain terminé. On ne clôture PAS encore.
        // Le sinistre passe à 'constat_terrain_ok' en attendant la rédaction officielle.
        $sinistre->update(['status' => 'constat_terrain_ok']);

        return redirect()->route('agent.sinistres.show', $sinistre->id)
            ->with('success', '✅ Constat terrain enregistré. Vous pouvez maintenant rédiger le constat officiel.');
    }

    /**
     * Affiche le formulaire de rédaction du constat officiel
     */
    public function showRedaction(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(
            $sinistre->assigned_agent_id === $agent->id ||
                $sinistre->assigned_service_id === $agent->service_id,
            403
        );
        abort_unless($sinistre->constat && $sinistre->constat->terrain_valide, 403, 'Le constat terrain doit être validé avant la rédaction.');

        $sinistre->load(['assure', 'constat', 'service']);
        return view('agent.sinistres.redaction', compact('sinistre'));
    }

    /**
     * Valide la rédaction du constat officiel, clôture le sinistre côté agent
     * et notifie l'assuré par SMS
     */
    public function storeRedaction(Request $request, Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(
            $sinistre->assigned_agent_id === $agent->id ||
                $sinistre->assigned_service_id === $agent->service_id,
            403
        );

        // Validation : soit un texte, soit un PDF — l'un des deux est obligatoire
        $request->validate([
            'redaction_contenu' => 'nullable|string|min:50',
            'redaction_pdf'     => 'nullable|file|mimes:pdf|max:10240',
            'montant_a_payer'   => 'required|integer|min:0',
        ], [
            'redaction_contenu.min' => 'La rédaction texte doit contenir au moins 50 caractères.',
            'redaction_pdf.mimes'   => 'Le fichier doit être un PDF.',
            'redaction_pdf.max'     => 'Le PDF ne doit pas dépasser 10 Mo.',
            'montant_a_payer.required' => 'Le montant à payer par l\'assuré est obligatoire.',
        ]);

        if (!$request->filled('redaction_contenu') && !$request->hasFile('redaction_pdf')) {
            return back()->withErrors(['redaction_contenu' => 'Veuillez saisir une rédaction ou joindre un fichier PDF.'])->withInput();
        }

        $updateData = [
            'redaction_validee'    => true,
            'redaction_validee_at' => now(),
            'montant_a_payer'      => $request->input('montant_a_payer'),
        ];

        if ($request->filled('redaction_contenu')) {
            $updateData['redaction_contenu'] = $request->input('redaction_contenu');
        }

        if ($request->hasFile('redaction_pdf')) {
            $updateData['redaction_pdf'] = $request->file('redaction_pdf')
                ->store('constats/redactions', 'public');
        }

        $sinistre->constat->update($updateData);
        $sinistre->update(['status' => 'traite']);

        // ── SMS Yellika à l'assuré ────────────────────────────────────────────
        $assure      = $sinistre->assure;
        $numero      = $assure->contact ?? null;
        $serviceName = $sinistre->service->name ?? 'notre agence';

        // Suppression des accents pour éviter l'erreur UNICODE de l'API
        $message = "Bonjour {$assure->name}, votre constat de sinistre #{$sinistre->numero_sinistre} est pret. "
            . "Connectez-vous sur votre espace pour regler les frais de {$request->montant_a_payer} FCFA et telecharger votre document. Merci.";

        if ($numero) {
            try {
                // Utilisation du service existant du projet
                app(\App\Services\YellikaSmsService::class)->sendSms($numero, $message);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('SMS Yellika non envoyé : ' . $e->getMessage());
            }
        }

        // Notification interne
        if ($assure) {
            $assure->notify(new \App\Notifications\ConstatPretNotification($sinistre));
        }

        return redirect()->route('agent.constats.rediges')
            ->with('success', '✅ Rédaction validée. Le sinistre est traité et l\'assuré a été notifié par SMS.');
    }

    /**
     * Liste des constats rédigés par l'agent ou son service
     */
    public function constatsRediges()
    {
        $agent = auth('user')->user();

        $constats = \App\Models\Constat::with(['sinistre.assure', 'sinistre.service'])
            ->whereHas('sinistre', function ($q) use ($agent) {
                $q->where(function ($sub) use ($agent) {
                    $sub->where('assigned_agent_id', $agent->id)
                        ->orWhere('assigned_service_id', $agent->service_id);
                });
            })
            ->where('redaction_validee', true)
            ->latest('redaction_validee_at')
            ->get();

        return view('agent.sinistres.constats_rediges', compact('constats'));
    }

    /**
     * Page Statistiques des paiements constats
     */
    public function constatsStatistiques()
    {
        $agent = auth('user')->user();

        $constats = \App\Models\Constat::with(['sinistre.assure'])
            ->whereHas('sinistre', function ($q) use ($agent) {
                $q->where(function ($sub) use ($agent) {
                    $sub->where('assigned_agent_id', $agent->id)
                        ->orWhere('assigned_service_id', $agent->service_id);
                });
            })
            ->where('redaction_validee', true)
            ->latest('redaction_validee_at')
            ->get();

        $online    = $constats->where('statut_paiement', 'success')->where('agent_unlocked', false);
        $deblocage = $constats->where('agent_unlocked', true);
        $pending   = $constats->where('statut_paiement', '!=', 'success');

        $stats = [
            'total'            => $constats->count(),
            'online_count'     => $online->count(),
            'online_montant'   => $online->sum('montant_a_payer'),
            'deblocage_count'  => $deblocage->count(),
            'deblocage_montant' => $deblocage->sum('montant_a_payer'),
            'pending_count'    => $pending->count(),
        ];

        $history = $constats
            ->where('statut_paiement', 'success')
            ->sortByDesc('redaction_validee_at')
            ->values();

        return view('agent.sinistres.constats_statistiques', compact('stats', 'history'));
    }

    /**
     * Marque le constat comme ayant été récupéré physiquement par l'assuré
     */
    public function markAsRecovered(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(
            $sinistre->assigned_agent_id === $agent->id ||
                $sinistre->assigned_service_id === $agent->service_id,
            403
        );

        abort_unless($sinistre->constat && $sinistre->constat->redaction_validee, 403, "Le constat n'est pas encore rédigé.");

        $sinistre->constat->update([
            'recupere_par_assure' => true,
            'recupere_at'         => now(),
        ]);

        return back()->with('success', '✅ Le constat a été marqué comme récupéré par l\'assuré.');
    }

    /**
     * Débloque le téléchargement du constat pour l'assuré SANS créditer le portefeuille.
     * Utilisé quand l'agent choisit de lever la barrière de paiement manuellement.
     */
    public function agentUnlockConstat(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(
            $sinistre->assigned_agent_id === $agent->id ||
                $sinistre->assigned_service_id === $agent->service_id,
            403
        );

        $constat = $sinistre->constat;
        abort_unless($constat && $constat->redaction_validee, 403, "Le constat n'est pas encore rédigé.");
        abort_if($constat->statut_paiement === 'success', 400, 'Ce constat est déjà débloqué.');

        // Marquer comme débloqué par l'agent (pas de crédit wallet)
        $constat->update([
            'statut_paiement'  => 'success',
            'agent_unlocked'   => true,
            'agent_unlocked_at' => now(),
            'agent_unlocked_by' => $agent->id,
        ]);

        // Historique dans wallet_transactions (montant 0, type agent_unlock — aucun crédit)
        \App\Models\WalletTransaction::create([
            'user_id'     => $agent->id,
            'sinistre_id' => $sinistre->id,
            'amount'      => 0,
            'type'        => 'agent_unlock',
            'description' => 'Déblocage manuel agent — constat #' . ($sinistre->numero_sinistre ?? $sinistre->id) . ' (sans paiement assuré)',
            'status'      => 'completed',
        ]);

        return back()->with('success', '✅ Téléchargement débloqué pour l\'assuré. Aucun montant crédité.');
    }

    public function showConstat(Sinistre $sinistre)
    {
        $agent = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($agent->id, $agent->service_id)->exists(), 403);
        $sinistre->load('assure');
        $constat = $sinistre->constat;
        abort_if(!$constat, 404);
        $isAccident = $constat->type_constat === 'accident';
        return view('agent.sinistres.constat_show', compact('sinistre', 'constat', 'isAccident'));
    }

    /**
     * Retourne les coordonnées en temps réel de l'agent assigné (pour le polling du tracking assuré)
     */
    public function getAgentLocation(Sinistre $sinistre)
    {
        $agent = $sinistre->assignedAgent;
        abort_if(!$agent, 404);

        return response()->json([
            'lat' => (float) ($agent->latitude ?? $sinistre->agent_start_lat),
            'lng' => (float) ($agent->longitude ?? $sinistre->agent_start_lng),
            'start_lat' => (float) $sinistre->agent_start_lat,
            'start_lng' => (float) $sinistre->agent_start_lng,
        ]);
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }

    /**
     * Affiche le profil de l'agent
     */
    public function profile()
    {
        $user = auth('user')->user();
        return view('agent.profile', compact('user'));
    }

    /**
     * Met à jour le profil de l'agent
     */
    public function updateProfile(Request $request)
    {
        $user = auth('user')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'current_password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $data = $request->only(['name', 'prenom', 'email', 'contact', 'adresse']);

        if ($request->hasFile('profile_picture')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Affiche le portefeuille de l'agent
     */
    public function wallet()
    {
        $agent = auth('user')->user();
        $transactions = \App\Models\WalletTransaction::where('user_id', $agent->id)
            ->with('sinistre')
            ->latest()
            ->paginate(15);

        return view('agent.wallet', compact('agent', 'transactions'));
    }
}
