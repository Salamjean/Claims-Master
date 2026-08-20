<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * POST /api/v1/auth/register
     * Inscription autonome d'un Assuré.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'prenom'    => ['nullable', 'string', 'max:255'],
            'email'     => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact'   => ['required', 'string', 'max:30', 'unique:users,contact'],
            'commune'   => ['nullable', 'string', 'max:255'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'password'  => ['required', 'string', 'min:6'],
        ], [
            'name.required'    => 'Le nom est obligatoire.',
            'email.email'      => 'L’adresse e-mail doit être valide.',
            'email.unique'     => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
            'contact.required' => 'Le numéro de contact est obligatoire.',
            'contact.unique'   => 'Ce numéro de téléphone est déjà utilisé par un autre compte.',
            'password.required'=> 'Le mot de passe est obligatoire.',
            'password.min'     => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Génération du code_user unique format: CM-XXXXXX-YYYY
            do {
                $random = strtoupper(Str::random(6));
                $codeUser = 'CM-' . $random . '-' . date('Y');
            } while (User::where('code_user', $codeUser)->exists());

            // Création de l'utilisateur avec le rôle 'assure'
            $user = User::create([
                'name'                 => trim($request->input('name')),
                'prenom'               => $request->input('prenom') ? trim($request->input('prenom')) : null,
                'email'                => $request->input('email') ? trim($request->input('email')) : null,
                'contact'              => trim($request->input('contact')),
                'commune'              => $request->input('commune'),
                'adresse'              => $request->input('adresse'),
                'latitude'             => $request->input('latitude'),
                'longitude'            => $request->input('longitude'),
                'role'                 => 'assure',
                'code_user'            => $codeUser,
                'password'             => Hash::make($request->input('password')),
                'email_verified_at'    => now(),
                'must_change_password' => false,
            ]);

            // Envoi éventuel du SMS de bienvenue
            try {
                $message = "Bonjour {$user->name}, votre compte Assuré Claims Master a été créé. Votre Code Assuré est: {$user->code_user}. Vous pouvez vous connecter avec votre contact ou e-mail.";
                $cleanContact = preg_replace('/[^0-9]/', '', $user->contact);
                if (strlen($cleanContact) >= 8) {
                    $smsService = app(YellikaSmsService::class);
                    $smsService->sendSMS($cleanContact, $message);
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du SMS d'inscription assuré via API : " . $e->getMessage());
            }

            DB::commit();

            // Génération du jeton Sanctum pour connexion automatique
            $token = $user->createToken('auth_token_assure')->plainTextToken;

            return response()->json([
                'success'      => true,
                'message'      => 'Inscription de l’assuré réussie avec succès.',
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => [
                    'id'                   => $user->id,
                    'name'                 => $user->name,
                    'prenom'               => $user->prenom,
                    'email'                => $user->email,
                    'contact'              => $user->contact,
                    'role'                 => $user->role,
                    'code_user'            => $user->code_user,
                    'commune'              => $user->commune,
                    'adresse'              => $user->adresse,
                    'latitude'             => $user->latitude,
                    'longitude'            => $user->longitude,
                    'must_change_password' => (bool) $user->must_change_password,
                    'has_ambulance'        => (bool) $user->has_ambulance,
                    'profile_picture_url'  => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                    'assurance_id'         => $user->assurance_id,
                    'service_id'           => $user->service_id,
                    'created_at'           => $user->created_at?->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'inscription assuré via API : " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l’inscription de l’assuré.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
     * POST /api/v1/auth/login
     * Connexion d'un utilisateur (Assuré, Agent, Groupe).
     *
     * Règles de connexion :
     * - Assuré (role: 'assure') : Connexion possible par e-mail OU numéro de téléphone.
     * - Agent (role: 'agent')   : Connexion uniquement par e-mail.
     * - Groupe (role: 'groupe') : Connexion uniquement par e-mail.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
            'role'     => ['nullable', 'string', 'in:assure,agent,groupe'],
        ], [
            'login.required'    => 'L’identifiant (e-mail ou numéro de téléphone) est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'role.in'           => 'Le rôle spécifié est invalide. Rôles autorisés : assure, agent, groupe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Champs obligatoires manquants.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');
        $requestedRole = $request->input('role');

        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL) !== false;
        $cleanPhone = preg_replace('/\D/', '', $loginInput);

        if ($isEmail) {
            // Connexion par EMAIL : s'applique aux 3 rôles (assuré, agent, groupe)
            $user = User::where('email', $loginInput)
                ->whereIn('role', ['assure', 'agent', 'groupe'])
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants incorrects. Aucun compte (assuré, agent ou groupe) ne correspond à cet e-mail.'
                ], 401);
            }
        } else {
            // Connexion par TÉLÉPHONE : réservée EXCLUSIVEMENT à l'assuré
            if (empty($cleanPhone) || strlen($cleanPhone) < 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format d’identifiant invalide. Saisissez une adresse email valide ou un numéro de téléphone.'
                ], 422);
            }

            $user = User::where('role', 'assure')
                ->where(function ($query) use ($loginInput, $cleanPhone) {
                    $query->where('contact', $loginInput)
                          ->orWhere('contact', $cleanPhone)
                          ->orWhere('contact', 'LIKE', '%' . substr($cleanPhone, -8));
                })->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants incorrects. La connexion par numéro de téléphone est exclusivement réservée aux assurés.'
                ], 401);
            }
        }

        // 1. Vérification du mot de passe
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.'
            ], 401);
        }

        // 2. Vérification si un rôle spécifique est exigé dans la requête
        if ($requestedRole && $user->role !== $requestedRole) {
            return response()->json([
                'success' => false,
                'message' => "Rôle incompatible. Ce compte possède le rôle '{$user->role}' et non '{$requestedRole}'."
            ], 403);
        }

        // Création du Token d'accès Sanctum
        $token = $user->createToken('auth_token_' . $user->role)->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Connexion réussie avec succès.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'                   => $user->id,
                'name'                 => $user->name,
                'prenom'               => $user->prenom,
                'email'                => $user->email,
                'contact'              => $user->contact,
                'role'                 => $user->role,
                'code_user'            => $user->code_user,
                'commune'              => $user->commune,
                'adresse'              => $user->adresse,
                'latitude'             => $user->latitude,
                'longitude'            => $user->longitude,
                'must_change_password' => (bool) $user->must_change_password,
                'has_ambulance'        => (bool) $user->has_ambulance,
                'profile_picture_url'  => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'assurance_id'         => $user->assurance_id,
                'service_id'           => $user->service_id,
                'created_at'           => $user->created_at?->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * GET /api/v1/auth/me
     * Récupère les données de l'utilisateur authentifié.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => [
                'id'                   => $user->id,
                'name'                 => $user->name,
                'prenom'               => $user->prenom,
                'email'                => $user->email,
                'contact'              => $user->contact,
                'role'                 => $user->role,
                'code_user'            => $user->code_user,
                'commune'              => $user->commune,
                'adresse'              => $user->adresse,
                'latitude'             => $user->latitude,
                'longitude'            => $user->longitude,
                'must_change_password' => (bool) $user->must_change_password,
                'has_ambulance'        => (bool) $user->has_ambulance,
                'profile_picture_url'  => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'assurance_id'         => $user->assurance_id,
                'service_id'           => $user->service_id,
                'created_at'           => $user->created_at?->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * POST /api/v1/auth/logout
     * Déconnecte l'utilisateur en révoquant son jeton d'accès actuel.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie. Le jeton d’accès a été révoqué.'
        ], 200);
    }
}
