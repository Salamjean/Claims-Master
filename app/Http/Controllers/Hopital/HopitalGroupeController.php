<?php

namespace App\Http\Controllers\Hopital;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResetCodePasswordUser;
use App\Notifications\AgentAccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HopitalGroupeController extends Controller
{
    /**
     * Liste des groupes/utilisateurs inscrits par la caserne
     */
    public function index()
    {
        $hopital = auth('user')->user();
        $groupes = $hopital->groupes()->latest()->get();

        return view('hopital.groupes.index', compact('groupes', 'hopital'));
    }

    /**
     * Formulaire d'inscription d'un utilisateur groupe
     */
    public function create()
    {
        return view('hopital.groupes.create');
    }

    /**
     * Enregistre un nouvel utilisateur avec le rôle 'groupe'
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:20',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'contact.required' => 'Le numéro de contact est obligatoire.',
        ]);

        $hopital = auth('user')->user();
        $code = rand(100000, 999999);

        $groupe = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'contact' => $request->contact,
            'role' => 'groupe',
            'service_id' => $hopital->id,
            'password' => Hash::make(Str::random(16)),
            'must_change_password' => true,
        ]);

        // Enregistrer le code d'activation
        ResetCodePasswordUser::updateOrCreate(
            ['email' => $groupe->email],
            ['code' => $code]
        );

        // Envoyer l'email d'activation
        $groupe->notify(new AgentAccessNotification($groupe, $code, $hopital->name));

        return redirect()->route('hopital.groupes.index')
            ->with('success', "L'utilisateur Groupe " . $groupe->name . " a été inscrit avec succès. Un e-mail d'accès lui a été envoyé.");
    }

    /**
     * Renvoyer l'email d'activation pour un groupe n'ayant pas encore activé son compte
     */
    public function resendActivation(User $groupe)
    {
        $hopital = auth('user')->user();
        abort_if($groupe->service_id !== $hopital->id || $groupe->role !== 'groupe', 403);

        if ($groupe->email_verified_at !== null && !$groupe->must_change_password) {
            return back()->with('error', "Ce groupe a déjà activé son compte.");
        }

        $code = rand(100000, 999999);

        ResetCodePasswordUser::updateOrCreate(
            ['email' => $groupe->email],
            ['code' => $code]
        );

        $groupe->notify(new AgentAccessNotification($groupe, $code, $hopital->name));

        return back()->with('success', "Un nouveau mail d'activation a été envoyé avec succès à " . $groupe->email);
    }

    /**
     * Supprime un utilisateur groupe
     */
    public function destroy(User $groupe)
    {
        abort_if($groupe->service_id !== auth('user')->id() || $groupe->role !== 'groupe', 403);

        $name = $groupe->name;
        $groupe->delete();

        return back()->with('success', "Le groupe " . $name . " a été supprimé.");
    }
}
