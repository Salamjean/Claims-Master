<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterAssureController extends Controller
{
    protected YellikaSmsService $sms;

    public function __construct(YellikaSmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Affiche le formulaire d'inscription pour l'assuré
     */
    public function showRegistrationForm()
    {
        return view('auth.register-assure');
    }

    /**
     * Traite la soumission du formulaire d'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'contact' => 'required|string|max:30|unique:users,contact',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'contact.required' => 'Le contact (téléphone) est obligatoire.',
            'contact.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        try {
            DB::beginTransaction();

            // Génération du code_user : CM-XXXXXX-YYYY (unique)
            do {
                $random = strtoupper(Str::random(6));
                $codeUser = 'CM-' . $random . '-' . date('Y');
            } while (User::where('code_user', $codeUser)->exists());

            // Création de l'utilisateur assuré
            $user = User::create([
                'name' => $request->name,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'contact' => $request->contact,
                'adresse' => $request->adresse,
                'role' => 'assure',
                'code_user' => $codeUser,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'must_change_password' => false, // L'utilisateur a lui-même choisi son mot de passe
            ]);

            // Envoi de SMS de bienvenue
            try {
                $message = "Bonjour {$user->name}, votre espace Assuré Claims Master a été créé. Votre Code Assuré est: {$user->code_user}. Vous pouvez vous connecter avec ce code ou votre email.";

                // Extraire les chiffres du contact pour le SMS
                $cleanContact = preg_replace('/[^0-9]/', '', $user->contact);
                if (strlen($cleanContact) >= 8) {
                    $this->sms->sendSMS($cleanContact, $message);
                } else {
                    Log::warning("Le numéro de téléphone '{$user->contact}' semble invalide pour l'envoi de SMS.");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du SMS d'inscription assuré : " . $e->getMessage());
                // On ne bloque pas l'inscription si le SMS échoue
            }

            DB::commit();

            // S'il n'y a pas d'erreur, on redirige l'utilisateur vers la page de login avec succès
            return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès ! Connectez-vous avec votre email (ou code ' . $codeUser . ') et votre mot de passe.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'inscription assuré : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.')->withInput();
        }
    }
}
