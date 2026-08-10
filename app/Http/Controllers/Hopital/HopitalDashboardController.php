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

        // Récupérer uniquement les sinistres actifs pour le tableau de bord
        $sinistres = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('hospital_status', '!=', 'termine')
            ->with(['assure', 'constat', 'service'])
            ->latest()
            ->get();

        $totalAlerts = Sinistre::where('nearest_hospital_id', $hospitalId)->count();
        
        $totalHospitalises = Sinistre::whereHas('constat', function ($q) use ($hospitalId) {
            $q->where('hospital_id', $hospitalId);
        })->count();

        $urgencesEnAttente = Sinistre::where('nearest_hospital_id', $hospitalId)
            ->where('hospital_status', 'en_attente')
            ->count();

        return view('hopital.dashboard', compact('user', 'sinistres', 'totalAlerts', 'totalHospitalises', 'urgencesEnAttente'));
    }

    /**
     * Dépêcher une ambulance pour un sinistre
     */
    public function dispatchAmbulance(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $sinistre->update([
            'hospital_status' => 'ambulance_en_route'
        ]);

        return back()->with('success', 'Ambulance dépêchée sur les lieux de l\'accident avec succès.');
    }

    /**
     * Confirmer l'arrivée de l'ambulance/victime
     */
    public function markArrived(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $sinistre->update([
            'hospital_status' => 'arrive'
        ]);

        return back()->with('success', 'Arrivée confirmée aux urgences. Prise en charge médicale en cours.');
    }

    /**
     * Clôturer la prise en charge et saisir le bilan
     */
    public function completeTreatment(Request $request, Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $request->validate([
            'hospital_severity' => 'required|string|in:leger,grave,deces',
            'hospital_notes' => 'nullable|string',
        ]);

        $sinistre->update([
            'hospital_status' => 'termine',
            'hospital_severity' => $request->hospital_severity,
            'hospital_notes' => $request->hospital_notes
        ]);

        return back()->with('success', 'Bilan médical enregistré et prise en charge clôturée.');
    }

    /**
     * Historique des prises en charge
     */
    public function historique()
    {
        $user = auth('user')->user();
        $hospitalId = $user->id;

        $sinistres = Sinistre::where(function($q) use ($hospitalId) {
                $q->where('nearest_hospital_id', $hospitalId)
                  ->where('hospital_status', 'termine');
            })
            ->orWhereHas('constat', function($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            })
            ->with(['assure', 'constat', 'etatDesLieux'])
            ->latest()
            ->paginate(15);

        return view('hopital.historique', compact('user', 'sinistres'));
    }

    public function downloadEtatDesLieuxPdf(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $etatDesLieux = \App\Models\EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        if (!$etatDesLieux) {
            return back()->with('error', 'Aucun état des lieux trouvé pour ce sinistre.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.etat_des_lieux', [
                'sinistre' => $sinistre,
                'etatDesLieux' => $etatDesLieux,
                'user' => $user,
            ]);

            return $pdf->download('etat_des_lieux_' . ($sinistre->numero_sinistre ?? $sinistre->id) . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur generation PDF etat des lieux hopital: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors du téléchargement du PDF.');
        }
    }

    public function streamEtatDesLieuxPdf(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $etatDesLieux = \App\Models\EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        if (!$etatDesLieux) {
            return back()->with('error', 'Aucun état des lieux trouvé pour ce sinistre.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.etat_des_lieux', [
                'sinistre' => $sinistre,
                'etatDesLieux' => $etatDesLieux,
                'user' => $user,
            ]);

            return $pdf->stream('etat_des_lieux_' . ($sinistre->numero_sinistre ?? $sinistre->id) . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur affichage PDF etat des lieux hopital: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de l\'affichage du PDF.');
        }
    }

    public function showConsultation(Sinistre $sinistre)
    {
        $user = auth('user')->user();

        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $etatDesLieux = \App\Models\EtatDesLieux::where('sinistre_id', $sinistre->id)->first();

        return view('hopital.show_consultation', compact('user', 'sinistre', 'etatDesLieux'));
    }

    /**
     * Gestion de la capacité et ambulance
     */
    public function capacite()
    {
        $user = auth('user')->user();
        return view('hopital.capacite', compact('user'));
    }

    /**
     * Mettre à jour la capacité
     */
    public function updateCapacite(Request $request)
    {
        $user = auth('user')->user();
        
        $request->validate([
            'has_ambulance' => 'required|boolean',
        ]);

        $user->update([
            'has_ambulance' => $request->boolean('has_ambulance')
        ]);

        return back()->with('success', 'Disponibilité de l\'ambulance mise à jour avec succès.');
    }

    /**
     * Liste des rapports d'intervention (états des lieux) soumis par les groupes
     */
    public function rapportsIntervention(Request $request)
    {
        $user = auth('user')->user();
        $hospitalId = $user->id;

        $query = \App\Models\EtatDesLieux::whereHas('sinistre', function ($q) use ($hospitalId) {
            $q->where('nearest_hospital_id', $hospitalId);
        })->with(['sinistre.assure', 'groupe', 'validator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_intervention', 'like', "%{$search}%")
                  ->orWhere('nature_intervention', 'like', "%{$search}%")
                  ->orWhere('nom_agent_signataire', 'like', "%{$search}%")
                  ->orWhere('casernes_mobilisees', 'like', "%{$search}%")
                  ->orWhereHas('sinistre', function ($sq) use ($search) {
                      $sq->where('numero_sinistre', 'like', "%{$search}%")
                        ->orWhere('lieu', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('nature')) {
            $query->where('nature_intervention', $request->nature);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $reports = $query->latest()->paginate(15);

        return view('hopital.rapports_intervention', compact('user', 'reports'));
    }

    /**
     * Valider définitivement un état des lieux
     */
    public function validerEtatDesLieux(\App\Models\EtatDesLieux $etatDesLieux)
    {
        $user = auth('user')->user();
        $sinistre = $etatDesLieux->sinistre;

        abort_unless($sinistre->nearest_hospital_id === $user->id, 403);

        $etatDesLieux->update([
            'status' => 'valide',
            'validated_at' => now(),
            'validated_by' => $user->id,
        ]);

        return back()->with('success', 'Le rapport d\'intervention a été validé avec succès. Il est désormais verrouillé et ne peut plus être modifié par le groupe.');
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
