<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\sendEmailAfterUserRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class HospitalController extends Controller
{
    /**
     * Affiche la liste des centres de santé (Hôpitaux / SAMU / Cliniques)
     */
    public function index()
    {
        $hospitals = User::where('role', 'hopital')
            ->latest()
            ->paginate(15);

        return view('admin.hospitals.index', compact('hospitals'));
    }

    /**
     * Affiche le formulaire de création d'une caserne de sapeurs-pompiers
     */
    public function create()
    {
        return view('admin.hospitals.create');
    }

    /**
     * Enregistre un nouveau centre de santé
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'contact' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'adresse' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'has_ambulance' => 'nullable|boolean',
        ], [
            'name.required' => 'Le nom de la caserne est obligatoire.',
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

            // Génération du code utilisateur format: HOSP-XXXXXX-YYYY
            do {
                $random = strtoupper(Str::random(6));
                $codeUser = 'HOSP-' . $random . '-' . date('Y');
            } while (User::where('code_user', $codeUser)->exists());

            // Création de l'utilisateur avec le rôle 'hopital'
            $user = User::create([
                'name' => $request->name,
                'prenom' => 'Urgences',
                'email' => $request->email,
                'contact' => $request->contact,
                'commune' => $request->commune,
                'adresse' => $request->adresse,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'role' => 'hopital',
                'code_user' => $codeUser,
                'password' => Hash::make('default'),
                'email_verified_at' => null,
                'must_change_password' => true,
                'has_ambulance' => $request->boolean('has_ambulance'),
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
                Log::error("Erreur lors de l'envoi de l'email d'activation à la caserne de sapeurs-pompiers {$user->email} : " . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('admin.hospitals.index')
                ->with('success', 'La caserne de sapeurs-pompiers a été créée avec succès. Un e-mail d\'activation a été envoyé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création d'une caserne de sapeurs-pompiers : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création de la caserne de sapeurs-pompiers.')->withInput();
        }
    }
}
