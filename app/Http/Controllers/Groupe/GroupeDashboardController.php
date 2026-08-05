<?php

namespace App\Http\Controllers\Groupe;

use App\Http\Controllers\Controller;
use App\Models\EtatDesLieux;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupeDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth('user')->user();
        $hospitalId = $user->service_id; // Le groupe est rattaché à un hôpital via service_id

        $sinistres = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('hospital_status', '!=', 'termine')
            ->whereNull('assigned_groupe_id')
            ->with(['assure', 'constat', 'service', 'etatDesLieux'])
            ->latest()
            ->get();

        $totalAlerts = Sinistre::where('nearest_hospital_id', $hospitalId)->count();

        // Déclarations en_attente non encore récupérées par aucun groupe
        $urgencesEnAttente = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('hospital_status', 'en_attente')
            ->whereNull('assigned_groupe_id')
            ->count();

        // Interventions actives récupérées par CE groupe
        $interventionsEnCours = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('assigned_groupe_id', $user->id)
            ->whereIn('hospital_status', ['en_attente', 'ambulance_en_route', 'arrive'])
            ->count();

        return view('groupe.dashboard', compact('user', 'sinistres', 'totalAlerts', 'urgencesEnAttente', 'interventionsEnCours'));
    }

    public function interventionsEnCours()
    {
        $user = auth('user')->user();
        $hospitalId = $user->service_id;

        $sinistres = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('assigned_groupe_id', $user->id)
            ->where('hospital_status', '!=', 'termine')
            ->with(['assure', 'constat', 'service', 'etatDesLieux'])
            ->latest()
            ->get();

        $enRoute = $sinistres->where('hospital_status', 'ambulance_en_route')->count();
        $arrive  = $sinistres->where('hospital_status', 'arrive')->count();

        $totalHistorique = Sinistre::where('assigned_groupe_id', $user->id)
            ->where('hospital_status', 'termine')
            ->count();

        return view('groupe.interventions', compact('user', 'sinistres', 'enRoute', 'arrive', 'totalHistorique'));
    }

    public function statistiques()
    {
        $user = auth('user')->user();

        // Toutes les déclarations récupérées par ce groupe
        $totalInterventions = Sinistre::where('assigned_groupe_id', $user->id)->count();
        $interventionsTerminees = Sinistre::where('assigned_groupe_id', $user->id)->where('hospital_status', 'termine')->count();
        $interventionsEnAttente = Sinistre::where('assigned_groupe_id', $user->id)->where('hospital_status', 'en_attente')->count();
        $interventionsEnCours = Sinistre::where('assigned_groupe_id', $user->id)->whereIn('hospital_status', ['ambulance_en_route', 'arrive'])->count();

        // Évolution des 6 derniers mois (récupérées par ce groupe)
        $sixMoisAvant = now()->subMonths(6);
        $sinistresMois = Sinistre::where('assigned_groupe_id', $user->id)
            ->where('created_at', '>=', $sixMoisAvant)
            ->get()
            ->groupBy(function ($d) {
                return $d->created_at->format('M Y');
            })->map->count();

        return view('groupe.statistiques', compact(
            'user',
            'totalInterventions',
            'interventionsTerminees',
            'interventionsEnAttente',
            'interventionsEnCours',
            'sinistresMois'
        ));
    }

    public function recuperer(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);

        if ($sinistre->assigned_groupe_id && $sinistre->assigned_groupe_id !== $user->id) {
            return back()->with('error', 'Cette déclaration a déjà été récupérée par une autre équipe.');
        }

        // Récupérer = l'équipe est active et peut ensuite signaler son arrivée
        $sinistre->update([
            'assigned_groupe_id' => $user->id,
            'hospital_status'    => 'ambulance_en_route',
            'hospital_dispatched_at' => $sinistre->hospital_dispatched_at ?? now(),
        ]);

        return back()->with('success', 'Déclaration récupérée — intervention en cours. Cliquez sur "Signaler Arrivée" une fois sur place.');
    }

    public function dispatchAmbulance(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);
        // On peut s'assurer que c'est bien ce groupe qui l'a récupéré, ou au moins l'affecter s'il ne l'était pas encore
        $sinistre->update([
            'hospital_status' => 'ambulance_en_route',
            'assigned_groupe_id' => $user->id,
            'hospital_dispatched_at' => $sinistre->hospital_dispatched_at ?? now(),
        ]);

        return back()->with('success', 'Équipe dépêchée sur les lieux de l\'accident avec succès.');
    }

    public function markArrived(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);

        $sinistre->update([
            'hospital_status' => 'arrive',
            'hospital_arrived_at' => $sinistre->hospital_arrived_at ?? now(),
        ]);

        return back()->with('success', 'Arrivée confirmée sur les lieux de l\'intervention.');
    }

    public function completeTreatment(Request $request, Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->service_id || $sinistre->assigned_groupe_id === $user->id, 403);

        $request->validate([
            'hospital_severity' => 'required|string|in:leger,grave,deces',
            'hospital_notes' => 'nullable|string',
        ]);

        $sinistre->update([
            'hospital_status' => 'termine',
            'hospital_severity' => $request->hospital_severity,
            'hospital_notes' => $request->hospital_notes
        ]);

        return back()->with('success', 'Intervention clôturée et transférée à l\'historique avec succès.');
    }

    public function showEtatDesLieuxForm(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);
        abort_unless($sinistre->assigned_groupe_id === $user->id, 403);

        if (!in_array($sinistre->hospital_status, ['arrive', 'termine'])) {
            return back()->with('error', 'Vous devez d\'abord marquer l\'intervention comme arrivée avant de remplir l\'état des lieux.');
        }

        $lat = (float) ($sinistre->latitude ?? 5.3411);
        $lng = (float) ($sinistre->longitude ?? -3.9812);

        $hospitalService = new \App\Services\HospitalService();
        $nearbyGoogleHospitals = $hospitalService->getNearbyHospitals($lat, $lng, 15);

        $dbHospitals = \App\Models\User::where('role', 'hopital')
            ->select('id', 'name', 'commune', 'latitude', 'longitude')
            ->get();

        $allHospitals = [];

        $haversine = function($l1, $g1, $l2, $g2) {
            if (!$l1 || !$g1 || !$l2 || !$g2) return 9999;
            $earthRadius = 6371;
            $dLat = deg2rad($l2 - $l1);
            $dLng = deg2rad($g2 - $g1);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($l1)) * cos(deg2rad($l2)) *
                 sin($dLng / 2) * sin($dLng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $earthRadius * $c;
        };

        foreach ($dbHospitals as $h) {
            $dist = $haversine($lat, $lng, (float)$h->latitude, (float)$h->longitude);
            $distStr = $dist < 9000 ? ' (~' . round($dist, 1) . ' km)' : '';
            $allHospitals[] = [
                'name' => $h->name,
                'label' => '🏥 ' . $h->name . ($h->commune ? ' (' . $h->commune . ')' : '') . $distStr . ' - [Agrée]',
                'distance' => $dist,
            ];
        }

        foreach ($nearbyGoogleHospitals as $gh) {
            $name = $gh['name'];
            $alreadyIn = false;
            foreach ($allHospitals as $item) {
                if (mb_strtolower(trim($item['name'])) === mb_strtolower(trim($name))) {
                    $alreadyIn = true;
                    break;
                }
            }
            if (!$alreadyIn) {
                $dist = $gh['distance'] ?? $haversine($lat, $lng, (float)($gh['latitude'] ?? 0), (float)($gh['longitude'] ?? 0));
                $distStr = ' (~' . round($dist, 1) . ' km)';
                $addrStr = isset($gh['adresse']) && $gh['adresse'] !== 'Adresse non spécifiée' ? ' - ' . $gh['adresse'] : '';
                $allHospitals[] = [
                    'name' => $name,
                    'label' => '📍 ' . $name . $addrStr . $distStr,
                    'distance' => $dist,
                ];
            }
        }

        usort($allHospitals, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $hospitals = $allHospitals;

        $etatDesLieux = EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        if (!$etatDesLieux) {
            $natureDeduite = match (true) {
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'incendie') || str_contains(strtolower($sinistre->type_sinistre ?? ''), 'feu') => 'Incendie',
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'malaise') || str_contains(strtolower($sinistre->type_sinistre ?? ''), 'sante') => 'Malaise',
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'sauvetage') => 'Sauvetage',
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'inondation') => 'Inondation',
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'gaz') => 'Fuite de gaz',
                str_contains(strtolower($sinistre->type_sinistre ?? ''), 'effondrement') => 'Effondrement',
                default => 'Accident de circulation',
            };

            $graviteDeduite = match ($sinistre->hospital_severity) {
                'critique' => 'Critique',
                'grave' => 'Élevé',
                'moyen' => 'Moyen',
                default => 'Faible',
            };

            $dispatchedAt = $sinistre->hospital_dispatched_at ?? $sinistre->created_at;
            $heureDepart = optional($dispatchedAt)->format('H:i');

            $arrivedAt = $sinistre->hospital_arrived_at ?? ($sinistre->hospital_status === 'arrive' || $sinistre->hospital_status === 'termine' ? $sinistre->updated_at : null);
            $heureArrivee = optional($arrivedAt)->format('H:i');

            $etatDesLieux = new EtatDesLieux([
                'sinistre_id' => $sinistre->id,
                'groupe_id' => $user->id,
                'numero_intervention' => $sinistre->numero_sinistre ?? ('INT-' . $sinistre->id),
                'date_heure_alerte' => $sinistre->created_at ?? $sinistre->date_declaration ?? $sinistre->date_survenance,
                'heure_depart_caserne' => $heureDepart,
                'heure_arrivee_lieux' => $heureArrivee,
                'heure_fin_intervention' => now()->format('H:i'),
                'lieu_exact' => $sinistre->lieu ?? ($sinistre->latitude && $sinistre->longitude ? ($sinistre->latitude . ', ' . $sinistre->longitude) : ''),
                'nature_intervention' => $natureDeduite,
                'description_situation' => $sinistre->description,
                'niveau_gravite' => $graviteDeduite,
                'victimes' => [
                    [
                        'nom' => '',
                        'sexe' => '',
                        'age' => '',
                        'niveau_conscience' => 'Conscient',
                        'decedee' => 'Non',
                        'blessures' => '',
                        'evacuation_hopital' => '',
                        'moyen_transport' => 'Ambulance / VSAV',
                    ]
                ],
            ]);
        }

        return view('groupe.etat_des_lieux_form', compact('user', 'sinistre', 'etatDesLieux', 'hospitals'));
    }

    public function storeEtatDesLieux(Request $request, Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);
        abort_unless($sinistre->assigned_groupe_id === $user->id, 403);

        if (!in_array($sinistre->hospital_status, ['arrive', 'termine'])) {
            return back()->with('error', 'L\'intervention doit être au statut arrivé ou terminé pour enregistrer ou modifier l\'état des lieux.');
        }

        $validated = $request->validate([
            // 1. Informations générales
            'numero_intervention' => ['nullable', 'string', 'max:255'],
            'date_heure_alerte' => ['nullable', 'date'],
            'heure_depart_caserne' => ['nullable', 'string', 'max:50'],
            'heure_arrivee_lieux' => ['nullable', 'string', 'max:50'],
            'heure_fin_intervention' => ['nullable', 'string', 'max:50'],
            'lieu_exact' => ['nullable', 'string', 'max:255'],

            // 2. Nature de l'intervention
            'nature_intervention' => ['required', 'string', 'max:255'],

            // 3. Informations sur le sinistre
            'description_situation' => ['required', 'string'],
            'cause_presumee' => ['required', 'string', 'max:255'],
            'niveau_gravite' => ['required', 'string', 'max:100'],
            'risques_identifies' => ['nullable', 'string'],
            'conditions_meteo' => ['required', 'string', 'max:255'],

            // 4. Victimes (JSON Array)
            'victimes' => ['nullable', 'array'],
            'victimes.*.nom' => ['nullable', 'string', 'max:255'],
            'victimes.*.sexe' => ['required', 'string', 'max:20'],
            'victimes.*.age' => ['nullable', 'string', 'max:20'],
            'victimes.*.etat' => ['nullable', 'string', 'max:255'],
            'victimes.*.blessures' => ['nullable', 'string'],
            'victimes.*.niveau_conscience' => ['required', 'string', 'max:100'],
            'victimes.*.decedee' => ['required', 'string', 'max:10'],
            'victimes.*.evacuation_hopital' => ['required', 'string', 'max:255'],
            'victimes.*.moyen_transport' => ['required', 'string', 'max:255'],

            // 5. Véhicules impliqués (JSON Array)
            'vehicules_impliques' => ['nullable', 'array'],
            'vehicules_impliques.*.type_vehicule' => ['nullable', 'string', 'max:255'],
            'vehicules_impliques.*.immatriculation' => ['nullable', 'string', 'max:100'],
            'vehicules_impliques.*.marque' => ['nullable', 'string', 'max:100'],
            'vehicules_impliques.*.couleur' => ['nullable', 'string', 'max:50'],
            'vehicules_impliques.*.conducteur_identifie' => ['nullable', 'string', 'max:255'],
            'vehicules_impliques.*.nombre_passagers' => ['nullable', 'string', 'max:20'],
            'vehicules_impliques.*.etat_vehicule' => ['nullable', 'string', 'max:255'],

            // 6. Dégâts matériels
            'biens_endommages' => ['nullable', 'string'],
            'batiments_touches' => ['nullable', 'string'],
            'surface_brulee' => ['nullable', 'string', 'max:255'],
            'estimation_degats' => ['nullable', 'string', 'max:255'],
            'biens_sauves' => ['nullable', 'string'],

            // 7. Moyens engagés
            'casernes_mobilisees' => ['nullable', 'string', 'max:255'],
            'vehicules_utilises' => ['nullable', 'array'],
            'nombre_pompiers' => ['nullable', 'string', 'max:100'],
            'materiel_utilise' => ['nullable', 'string'],
            'quantite_eau_utilisee' => ['nullable', 'string', 'max:255'],
            'produits_extincteurs_utilises' => ['nullable', 'string', 'max:255'],

            // 8. Actions réalisées (JSON Array)
            'actions_realisees' => ['nullable', 'array'],

            // 9. Autorités présentes (JSON Array)
            'autorites_presentes' => ['nullable', 'array'],

            // 10. Témoins (JSON Array)
            'temoins' => ['nullable', 'array'],
            'temoins.*.nom' => ['nullable', 'string', 'max:255'],
            'temoins.*.contact' => ['nullable', 'string', 'max:255'],
            'temoins.*.declaration' => ['nullable', 'string'],

            // 11. Chronologie (JSON Array)
            'chronologie' => ['nullable', 'array'],
            'chronologie.*.heure' => ['nullable', 'string', 'max:50'],
            'chronologie.*.evenement' => ['nullable', 'string', 'max:255'],
            'chronologie.*.description' => ['nullable', 'string'],

            // 12. Conclusion
            'situation_maitrisee' => ['nullable', 'string', 'max:50'],
            'cause_probable' => ['nullable', 'string'],
            'recommandations' => ['nullable', 'string'],
            'suites_a_donner' => ['nullable', 'string'],
        ]);

        $arrayFields = [
            'victimes',
            'vehicules_impliques',
            'vehicules_utilises',
            'actions_realisees',
            'autorites_presentes',
            'temoins',
            'chronologie',
        ];

        foreach ($arrayFields as $field) {
            $validated[$field] = array_values(array_filter($request->input($field, [])));
        }

        $validated['sinistre_id'] = $sinistre->id;
        $validated['groupe_id'] = $user->id;

        $existingRecord = EtatDesLieux::where('sinistre_id', $sinistre->id)->first();
        if (empty($validated['heure_fin_intervention']) || !$existingRecord) {
            $validated['heure_fin_intervention'] = $existingRecord?->heure_fin_intervention ?? now()->format('H:i');
        }

        $wasTermine = $sinistre->hospital_status === 'termine';

        EtatDesLieux::updateOrCreate(
            ['sinistre_id' => $sinistre->id],
            $validated
        );

        $sinistre->update([
            'hospital_status' => 'termine',
        ]);

        if ($wasTermine) {
            return redirect()->route('groupe.sinistres.show', $sinistre)
                ->with('success', 'L\'état des lieux a été mis à jour avec succès.');
        }

        return redirect()->route('groupe.interventions')
            ->with('success', 'État des lieux enregistré avec succès. L\'intervention est désormais clôturée et archivée dans l\'historique.');
    }

    public function downloadEtatDesLieuxPdf(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->assigned_groupe_id === $user->id || $sinistre->nearest_hospital_id === $user->service_id, 403);

        $etatDesLieux = EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        if (!$etatDesLieux) {
            return back()->with('error', 'Aucun état des lieux trouvé pour ce sinistre.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.etat_des_lieux', [
                'sinistre' => $sinistre,
                'etatDesLieux' => $etatDesLieux,
                'etat' => $etatDesLieux,
                'user' => $user,
            ]);

            return $pdf->download('etat_des_lieux_' . ($sinistre->numero_sinistre ?? $sinistre->id) . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur generation PDF etat des lieux: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la generation du PDF de l\'etat des lieux : ' . $e->getMessage());
        }
    }

    public function streamEtatDesLieuxPdf(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->assigned_groupe_id === $user->id || $sinistre->nearest_hospital_id === $user->service_id, 403);

        $etatDesLieux = EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        if (!$etatDesLieux) {
            return back()->with('error', 'Aucun état des lieux trouvé pour ce sinistre.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.etat_des_lieux', [
                'sinistre' => $sinistre,
                'etatDesLieux' => $etatDesLieux,
                'etat' => $etatDesLieux,
                'user' => $user,
            ]);

            return $pdf->stream('etat_des_lieux_' . ($sinistre->numero_sinistre ?? $sinistre->id) . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur affichage PDF etat des lieux: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de l\'affichage du PDF.');
        }
    }

    public function showConsultation(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->assigned_groupe_id === $user->id || $sinistre->nearest_hospital_id === $user->service_id, 403);

        $etatDesLieux = EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        return view('groupe.show_consultation', compact('user', 'sinistre', 'etatDesLieux'));
    }

    public function historique()
    {
        $user = auth('user')->user();
        $hospitalId = $user->service_id;

        $sinistres = Sinistre::where(function ($q) use ($hospitalId) {
            $q->where('nearest_hospital_id', $hospitalId)
                ->where('hospital_status', 'termine');
        })
            ->orWhereHas('constat', function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            })
            ->with(['assure', 'constat', 'etatDesLieux'])
            ->latest()
            ->paginate(15);

        return view('groupe.historique', compact('user', 'sinistres'));
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
