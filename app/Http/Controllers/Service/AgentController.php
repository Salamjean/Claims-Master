<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResetCodePasswordUser;
use App\Notifications\AgentAccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Liste des agents du service connecté
     */
    public function index()
    {
        $service = auth('user')->user();
        $agents = $service->agents()->latest()->get();
        
        return view('service.agents.index', compact('agents', 'service'));
    }

    /**
     * Formulaire de création d'un agent
     */
    public function create()
    {
        return view('service.agents.create');
    }

    /**
     * Enregistre un nouvel agent et envoie l'email d'accès
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:20',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
        ]);

        $service = auth('user')->user();
        $code = rand(100000, 999999);

        $agent = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'contact' => $request->contact,
            'role' => 'agent',
            'service_id' => $service->id,
            'password' => Hash::make(Str::random(16)), // Mot de passe aléatoire initial
            'must_change_password' => true,
        ]);

        // Enregistrer le code d'activation
        ResetCodePasswordUser::updateOrCreate(
            ['email' => $agent->email],
            ['code' => $code]
        );

        // Envoyer la notification avec le CODE
        $agent->notify(new AgentAccessNotification($agent, $code, $service->name));

        return redirect()->route($service->role . '.agents.index')
            ->with('success', "L'agent " . $agent->name . " a été créé avec succès. Un email d'accès lui a été envoyé.");
    }

    /**
     * Supprime un agent
     */
    public function destroy(User $agent)
    {
        abort_if($agent->service_id !== auth('user')->id(), 403);
        
        $name = $agent->name;
        $agent->delete();

        return back()->with('success', "L'agent " . $name . " a été supprimé.");
    }
}
