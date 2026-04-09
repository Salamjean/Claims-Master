<?php

namespace App\Http\Controllers\Assure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AssureDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth('user')->user();
        $contrats = $user->contrats()->latest()->get();

        // Statistiques des contrats
        $activeContractsCount = $contrats->where('statut', 'actif')->count();
        $totalPrimes = $contrats->sum('prime');

        // Dernier sinistre avec ses relations
        $dernierSinistre = \App\Models\Sinistre::where('user_id', $user->id)
            ->with(['assignedAgent', 'service', 'constat'])
            ->latest()
            ->first();

        // Nombre de documents en attente (Action Requise)
        $pendingDocumentsCount = \App\Models\SinistreDocumentAttendu::whereHas('sinistre', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status_client', 'pending')
            ->count();

        // Données graphique : sinistres des 6 derniers mois
        $sinistresParMois = \App\Models\Sinistre::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get()
            ->groupBy(fn($s) => $s->created_at->format('Y-m'))
            ->map->count();

        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $chartLabels[] = $month->translatedFormat('M');
            $chartData[] = $sinistresParMois[$key] ?? 0;
        }

        // Activités récentes (mélange de sinistres et documents)
        $recentSinistres = \App\Models\Sinistre::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($s) {
                return [
                    'type' => 'sinistre',
                    'title' => 'Déclaration : ' . str_replace('_', ' ', $s->type_sinistre),
                    'date' => $s->created_at,
                    'status' => $s->status,
                    'icon' => 'fa-car-burst',
                    'color' => 'blue'
                ];
            });

        $recentDocs = \App\Models\SinistreDocumentSoumis::whereHas('documentAttendu.sinistre', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('documentAttendu')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($d) {
                return [
                    'type' => 'document',
                    'title' => 'Document soumis : ' . ($d->documentAttendu->nom_document ?? 'Fichier'),
                    'date' => $d->created_at,
                    'status' => $d->ai_compliance_status,
                    'icon' => 'fa-file-arrow-up',
                    'color' => 'emerald'
                ];
            });

        $recentActivities = $recentSinistres->concat($recentDocs)->sortByDesc('date')->take(6);

        return view('assure.dashboard', compact(
            'user', 'contrats', 'chartLabels', 'chartData', 
            'dernierSinistre', 'pendingDocumentsCount', 
            'activeContractsCount', 'totalPrimes', 'recentActivities'
        ));
    }

    /**
     * Affiche le formulaire de changement de mot de passe obligatoire
     */
    public function showChangePassword()
    {
        $user = auth('user')->user();
        return view('assure.change-password', compact('user'));
    }

    /**
     * Traite le changement de mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
        ]);

        $user = auth('user')->user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('assure.dashboard')
            ->with('success', 'Mot de passe modifié avec succès. Bienvenue sur votre espace !');
    }

    /**
     * Affiche le profil de l'assuré
     */
    public function profile()
    {
        $user = auth('user')->user();
        return view('assure.profile', compact('user'));
    }

    /**
     * Met à jour le profil de l'assuré
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
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté.');
    }

    public function support()
    {
        return view('assure.support');
    }
}
