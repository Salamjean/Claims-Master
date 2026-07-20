<?php

namespace App\Http\Controllers\Groupe;

use App\Http\Controllers\Controller;
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
            ->with(['assure', 'constat', 'service'])
            ->latest()
            ->get();

        $totalAlerts = Sinistre::where('nearest_hospital_id', $hospitalId)->count();
        
        $urgencesEnAttente = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('hospital_status', 'en_attente')
            ->count();
            
        $interventionsEnCours = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->whereIn('hospital_status', ['ambulance_en_route', 'arrive'])
            ->count();

        return view('groupe.dashboard', compact('user', 'sinistres', 'totalAlerts', 'urgencesEnAttente', 'interventionsEnCours'));
    }

    public function statistiques()
    {
        $user = auth('user')->user();
        $hospitalId = $user->service_id;

        $totalInterventions = Sinistre::where('nearest_hospital_id', $hospitalId)->count();
        $interventionsTerminees = Sinistre::where('nearest_hospital_id', $hospitalId)->where('hospital_status', 'termine')->count();
        $interventionsEnAttente = Sinistre::where('nearest_hospital_id', $hospitalId)->where('hospital_status', 'en_attente')->count();
        $interventionsEnCours = Sinistre::where('nearest_hospital_id', $hospitalId)->whereIn('hospital_status', ['ambulance_en_route', 'arrive'])->count();

        // Statistiques des 6 derniers mois
        $sixMoisAvant = now()->subMonths(6);
        $sinistresMois = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('created_at', '>=', $sixMoisAvant)
            ->get()
            ->groupBy(function($d) {
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

    public function dispatchAmbulance(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);

        $sinistre->update([
            'hospital_status' => 'ambulance_en_route'
        ]);

        return back()->with('success', 'Équipe dépêchée sur les lieux de l\'accident avec succès.');
    }

    public function markArrived(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->service_id, 403);

        $sinistre->update([
            'hospital_status' => 'arrive'
        ]);

        return back()->with('success', 'Arrivée confirmée sur les lieux de l\'intervention.');
    }

    public function historique()
    {
        $user = auth('user')->user();
        $hospitalId = $user->service_id;

        $sinistres = Sinistre::where(function($q) use ($hospitalId) {
                $q->where('nearest_hospital_id', $hospitalId)
                  ->where('hospital_status', 'termine');
            })
            ->orWhereHas('constat', function($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            })
            ->with(['assure', 'constat'])
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
