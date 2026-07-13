<?php

namespace App\Http\Controllers\Hopital;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HopitalDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du centre de santé
     */
    public function dashboard()
    {
        $user = auth('user')->user();
        $hospitalId = $user->id;

        // Récupérer tous les sinistres liés à cette clinique/hôpital
        // Soit l'hôpital le plus proche lors de la déclaration
        // Soit l'hôpital d'hospitalisation renseigné lors du constat
        $sinistres = Sinistre::where(function ($q) use ($hospitalId) {
            $q->where('nearest_hospital_id', $hospitalId)
              ->orWhereHas('constat', function ($sq) use ($hospitalId) {
                  $sq->where('hospital_id', $hospitalId);
              });
        })
        ->with(['assure', 'constat', 'service'])
        ->latest()
        ->get();

        $totalAlerts = Sinistre::where('nearest_hospital_id', $hospitalId)->count();
        
        $totalHospitalises = Sinistre::whereHas('constat', function ($q) use ($hospitalId) {
            $q->where('hospital_id', $hospitalId);
        })->count();

        $urgencesEnAttente = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('status', 'en_attente')
            ->count();

        return view('hopital.dashboard', compact('user', 'sinistres', 'totalAlerts', 'totalHospitalises', 'urgencesEnAttente'));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login')->with('success', 'Déconnexion réussie.');
    }
}
