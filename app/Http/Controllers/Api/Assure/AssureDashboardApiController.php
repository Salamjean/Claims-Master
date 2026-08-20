<?php

namespace App\Http\Controllers\Api\Assure;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;
use App\Models\SinistreDocumentSoumis;
use Illuminate\Http\Request;

class AssureDashboardApiController extends Controller
{
    /**
     * GET /api/v1/assure/dashboard
     * Tableau de bord pour l'assuré connecté.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'assure') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Seuls les assurés authentifiés peuvent accéder à ce tableau de bord.'
            ], 403);
        }

        // 1. Contrats
        $contrats = $user->contrats()->latest()->get();
        $activeContractsCount = $contrats->where('statut', 'actif')->count();
        $totalPrimes = (float) $contrats->sum('prime');

        // 2. Dernier sinistre avec relations
        $dernierSinistre = Sinistre::where('user_id', $user->id)
            ->with(['assignedAgent:id,name,prenom,contact', 'service:id,name,contact', 'constat:id,sinistre_id,code_constat,redaction_validee,statut_paiement'])
            ->latest()
            ->first();

        // 3. Documents en attente d'action client
        $pendingDocumentsCount = SinistreDocumentAttendu::whereHas('sinistre', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('status_client', 'pending')
            ->count();

        // 4. Statistiques des sinistres (KPIs)
        $totalSinistres     = Sinistre::where('user_id', $user->id)->count();
        $enAttenteSinistres = Sinistre::where('user_id', $user->id)->where('status', 'en_attente')->count();
        $enCoursSinistres   = Sinistre::where('user_id', $user->id)->where('status', 'en_cours')->count();
        $clotureSinistres   = Sinistre::where('user_id', $user->id)->where('status', 'cloture')->count();

        // 5. Constats prêts non réglés
        $countConstatsNonRegles = Sinistre::where('user_id', $user->id)
            ->whereHas('constat', function ($q) {
                $q->where('redaction_validee', true)
                    ->where(function ($query) {
                        $query->where('statut_paiement', '!=', 'success')
                            ->orWhereNull('statut_paiement');
                    });
            })
            ->count();

        // 6. Graphique sinistres par mois (6 derniers mois)
        $sinistresParMois = Sinistre::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(fn($s) => $s->created_at->format('Y-m'))
            ->map->count();

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $chartData[] = [
                'month_key'   => $key,
                'month_label' => ucfirst($month->translatedFormat('F Y')),
                'count'       => $sinistresParMois[$key] ?? 0,
            ];
        }

        // 7. Activités récentes (mélange des 5 derniers sinistres + 5 derniers documents soumis)
        $recentSinistres = Sinistre::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'id'          => $s->id,
                    'type'        => 'sinistre',
                    'title'       => 'Déclaration : ' . str_replace('_', ' ', $s->type_sinistre),
                    'code'        => $s->code_sinistre ?? 'SIN-' . $s->id,
                    'status'      => $s->status,
                    'created_at'  => $s->created_at?->toIso8601String(),
                ];
            });

        $recentDocs = SinistreDocumentSoumis::whereHas('documentAttendu.sinistre', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with('documentAttendu:id,nom_document')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($d) {
                return [
                    'id'          => $d->id,
                    'type'        => 'document',
                    'title'       => 'Document soumis : ' . ($d->documentAttendu->nom_document ?? 'Fichier'),
                    'status'      => $d->ai_compliance_status,
                    'created_at'  => $d->created_at?->toIso8601String(),
                ];
            });

        $recentActivities = $recentSinistres->concat($recentDocs)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'prenom'              => $user->prenom,
                    'email'               => $user->email,
                    'contact'             => $user->contact,
                    'code_user'           => $user->code_user,
                    'commune'             => $user->commune,
                    'adresse'             => $user->adresse,
                    'profile_picture_url' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                ],
                'kpi' => [
                    'total_sinistres'         => $totalSinistres,
                    'sinistres_en_attente'    => $enAttenteSinistres,
                    'sinistres_en_cours'      => $enCoursSinistres,
                    'sinistres_clotures'      => $clotureSinistres,
                    'contrats_actifs'         => $activeContractsCount,
                    'total_primes'            => $totalPrimes,
                    'documents_en_attente'    => $pendingDocumentsCount,
                    'constats_non_regles'     => $countConstatsNonRegles,
                ],
                'dernier_sinistre'         => $dernierSinistre,
                'chart_sinistres_par_mois' => $chartData,
                'activites_recentes'       => $recentActivities,
                'contrats_recents'         => $contrats->take(5)->map(function ($c) {
                    return [
                        'id'             => $c->id,
                        'numero_contrat' => $c->numero_contrat,
                        'type_contrat'   => $c->type_contrat,
                        'compagnie'      => $c->nom_assureur ?? 'AMSA Assurances',
                        'statut'         => $c->statut,
                        'prime'          => (float) $c->prime,
                        'date_debut'     => $c->date_debut,
                        'date_fin'       => $c->date_fin,
                    ];
                }),
            ]
        ], 200);
    }
}
