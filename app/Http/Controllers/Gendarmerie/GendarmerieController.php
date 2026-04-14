<?php

namespace App\Http\Controllers\Gendarmerie;

use App\Http\Controllers\Controller;
use App\Models\Constat;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GendarmerieController extends Controller
{
    /**
     * Affiche le tableau de bord Gendarmerie
     */
    public function dashboard()
    {
        $user = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($user->id)
            ->with('assure')
            ->latest()
            ->get();

        $total = $sinistres->count();
        $enAttente = $sinistres->where('status', 'en_attente')->count();
        $enCours = $sinistres->where('status', 'en_cours')->count();
        $cloture = $sinistres->whereIn('status', ['traite', 'cloture'])->count();

        return view('gendarmerie.dashboard', compact('user', 'sinistres', 'total', 'enAttente', 'enCours', 'cloture'));
    }

    /**
     * Endpoint AJAX : renvoie les sinistres assignés en JSON (pour auto-refresh)
     */
    public function sinistresJson()
    {
        $user = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($user->id)
            ->with('assure')
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'type_sinistre' => str_replace('_', ' ', $s->type_sinistre),
                'assure' => $s->assure->name . ' ' . ($s->assure->prenom ?? ''),
                'status' => $s->status,
                'photos_count' => $s->photos ? count($s->photos) : 0,
                'created_at' => $s->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json($sinistres);
    }

    /**
     * Sinistres en attente assignés à cette brigade
     */
    public function enAttente()
    {
        $user = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($user->id)
            ->where('status', 'en_attente')
            ->with('assure')
            ->latest()
            ->get();
        return view('gendarmerie.sinistres.en_attente', compact('sinistres'));
    }

    /**
     * Historique de tous les sinistres assignés à cette brigade
     */
    public function historique()
    {
        $user = auth('user')->user();
        $sinistres = Sinistre::whereInvolved($user->id)
            ->with('assure', 'constat')
            ->latest()
            ->get();
        return view('gendarmerie.sinistres.historique', compact('sinistres'));
    }

    /**
     * Afficher le détail d'un sinistre
     */
    public function showSinistre(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($user->id)->exists(), 403);
        
        $sinistre->load('assure', 'constat');
        return view('gendarmerie.sinistres.sinistre_show', compact('sinistre'));
    }

    /**
     * Formulaire de constat
     */
    public function createConstat(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($user->id)->exists(), 403);
        
        $sinistre->load('assure');
        $isAccident = in_array($sinistre->type_sinistre, ['Accident_matériel', 'Accident_corporel']);
        return view('gendarmerie.sinistres.constat', compact('sinistre', 'isAccident'));
    }

    /**
     * Enregistre le constat
     */
    public function storeConstat(Request $request, Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($user->id)->exists(), 403);

        $data = $request->except(['_token', 'ass1_photo', 'ass2_photo', 'croquis', 'photos_plus']);
        $data['sinistre_id'] = $sinistre->id;
        $data['service_id'] = auth('user')->id();

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

        Constat::updateOrCreate(
            ['sinistre_id' => $sinistre->id],
            $data
        );

        // Mettre à jour le statut du sinistre en "en_cours"
        $sinistre->update(['status' => 'traite']);

        return redirect()->route('gendarmerie.sinistres.en_attente')
            ->with('success', 'Constat enregistré avec succès. Le sinistre est clôturé.');
    }

    /**
     * Afficher les détails d'un constat
     */
    public function showConstat(Sinistre $sinistre)
    {
        $user = auth('user')->user();
        abort_unless(Sinistre::where('id', $sinistre->id)->whereInvolved($user->id)->exists(), 403);
        
        $sinistre->load('assure');
        $constat = $sinistre->constat;
        abort_if(!$constat, 404);
        $isAccident = $constat->type_constat === 'accident';
        return view('gendarmerie.sinistres.constat_show', compact('sinistre', 'constat', 'isAccident'));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Affiche le profil du service
     */
    public function profile()
    {
        $user = auth('user')->user();
        return view('gendarmerie.profile', compact('user'));
    }

    /**
     * Met à jour le profil du service
     */
    public function updateProfile(Request $request)
    {
        $user = auth('user')->user();

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
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Affiche le portefeuille de l'unité (Gendarmerie)
     */
    public function wallet()
    {
        $user = auth('user')->user();
        $transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
            ->with(['sinistre.assignedAgent', 'sinistre.assure'])
            ->latest()
            ->paginate(15);

        return view('gendarmerie.wallet', compact('user', 'transactions'));
    }
}
