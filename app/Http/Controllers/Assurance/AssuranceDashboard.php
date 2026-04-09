<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssuranceDashboard extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Statistiques de base
        $totalAssures = \App\Models\User::where('assurance_id', $user->id)->where('role', 'assure')->count();
        $totalSinistres = \App\Models\Sinistre::where('assurance_id', $user->id)->count();
        $sinistresEnAttente = \App\Models\Sinistre::where('assurance_id', $user->id)
            ->whereIn('status', ['en_attente', 'en_cours', 'traite'])
            ->count();
            
        // Derniers sinistres
        $recentSinistres = \App\Models\Sinistre::where('assurance_id', $user->id)
            ->with('assure')
            ->latest()
            ->take(5)
            ->get();

        // Données pour le graphique (6 derniers mois)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = \App\Models\Sinistre::where('assurance_id', $user->id)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            $chartData[] = [
                'month' => $month->translatedFormat('F'),
                'count' => $count
            ];
        }

        return view('assurance.dashboard', compact(
            'totalAssures', 
            'totalSinistres', 
            'sinistresEnAttente',
            'recentSinistres',
            'chartData'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('assurance.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'current_password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $data = $request->only(['name', 'email', 'contact', 'adresse']);

        if ($request->hasFile('profile_picture')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    public function showChangePassword()
    {
        return view('assurance.profile.change_password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return redirect()->route('assurance.profile')->with('success', 'Votre mot de passe a été modifié avec succès.');
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        return redirect()->route('portal.login');
    }
}
