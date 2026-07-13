<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\sendEmailAfterUserRegister;
use App\Notifications\ServiceConstatAccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ServiceConstatController extends Controller
{
    /**
     * Affiche la liste des services de constats (Police / Gendarmerie)
     */
    public function index()
    {
        $services = User::whereIn('role', ['police', 'gendarmerie'])
            ->latest()
            ->paginate(15);

        return view('admin.services.index', compact('services'));
    }

    /**
     * Affiche le formulaire de création d'un service
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Enregistre un nouveau service de constats
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_service' => 'required|in:police,gendarmerie',
            'email' => 'required|string|email|max:255|unique:users',
            'contact' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'adresse' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'name.required' => 'Le nom du service est obligatoire.',
            'type_service.required' => 'Veuillez sélectionner le type de service.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'contact.required' => 'Le numéro de contact est obligatoire.',
            'adresse.required' => 'L\'adresse géographique est obligatoire.',
            'latitude.numeric' => 'La latitude doit être un nombre valide.',
            'longitude.numeric' => 'La longitude doit être un nombre valide.',
        ]);

        try {
            DB::beginTransaction();

            // Génération du code utilisateur format: SC-XXXXXX-YYYY (Service Constat)
            do {
                $random = strtoupper(Str::random(6));
                $codeUser = 'SC-' . $random . '-' . date('Y');
            } while (User::where('code_user', $codeUser)->exists());


            // Création de l'utilisateur avec le rôle correspondant (police ou gendarmerie)
            $user = User::create([
                'name' => $request->name,
                'prenom' => 'Service', // ou vide, car les services sont souvent des entités
                'email' => $request->email,
                'contact' => $request->contact,
                'commune' => $request->commune,
                'adresse' => $request->adresse,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'role' => $request->type_service, // 'police' ou 'gendarmerie'
                'code_user' => $codeUser,
                'password' => Hash::make('default'),
                'email_verified_at' => null,
                'must_change_password' => true, // Le service devra changer son mot de passe
            ]);

            // Génération d'un code d'activation et envoi email
            try {
                ResetCodePasswordUser::where('email', $user->email)->delete();
                $code = rand(1000, 4000) . '' . $user->id;

                ResetCodePasswordUser::create([
                    'code' => $code,
                    'email' => $user->email,
                ]);

                Notification::route('mail', $user->email)
                    ->notify(new sendEmailAfterUserRegister($code, $user->email));
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi de l'email d'activation au service de constat {$user->email} : " . $e->getMessage());
                // On continue même si l'email échoue, l'admin pourra réinitialiser le mdp si besoin
            }

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'Le service de constat (' . ucfirst($user->role) . ') a été créé avec succès. Un e-mail d\'activation a été envoyé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création d'un service de constat : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création du service.')->withInput();
        }
    }
}
