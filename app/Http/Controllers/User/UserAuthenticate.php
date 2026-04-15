<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\PasswordResetOTP;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserAuthenticate extends Controller
{
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showPortalForgotPassword()
    {
        return view('auth.portal-forgot-password');
    }

    public function handlePortalForgotPassword(Request $request)
    {
        $request->validate([
            'login' => 'required|email',
        ], [
            'login.required' => 'L\'adresse email est obligatoire.',
            'login.email' => 'Veuillez saisir une adresse email valide.',
        ]);

        try {
            $email = trim($request->login);

            // Rechercher l'utilisateur (Uniquement les PROFESSIONNELS)
            $user = User::where('email', $email)
                ->where('role', '!=', 'assure')
                ->first();

            if (!$user) {
                return back()->with('error', 'Aucun compte professionnel ne correspond à cette adresse email.')->withInput();
            }

            // Supprimer les anciens codes
            ResetCodePasswordUser::where('email', $user->email)->delete();

            // Générer OTP
            $code = rand(1000, 9999);
            ResetCodePasswordUser::create([
                'code' => $code,
                'email' => $user->email,
            ]);

            // Notification
            $user->notify(new PasswordResetOTP($code, $user->email));

            return redirect()->route('portal.password.reset', $user->email)->with('success', 'Un code de réinitialisation a été envoyé à votre adresse email professionnelle.');
        } catch (Exception $e) {
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function handleForgotPassword(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ], [
            'login.required' => 'L\'identifiant est obligatoire.',
        ]);

        try {
            $login = trim($request->login);
            $mode = $request->input('login_mode', 'email');

            // --- Détection des formats ---
            $isCodeUser = (bool) preg_match('/^CM-[A-Z0-9]{6}-\d{4}$/i', $login);
            $isPhoneNumber = (bool) preg_match('/^[0-9+]{8,15}$/', $login);
            $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

            // Validation stricte
            if ($mode === 'id' && $isEmail) {
                return back()->with('error', 'Veuillez saisir un identifiant ou un numéro de téléphone, pas une adresse email.')->withInput();
            }
            if ($mode === 'email' && !$isEmail) {
                return back()->with('error', 'Veuillez saisir une adresse email valide.')->withInput();
            }

            // --- Recherche de l'utilisateur (Uniquement les ASSURÉS ici) ---
            $query = User::query()->where('role', 'assure');
            if ($isCodeUser) {
                $query->where('code_user', strtoupper($login));
            } elseif ($isPhoneNumber) {
                $query->where('contact', $login);
            } else {
                $query->where('email', $login);
            }

            $user = $query->first();

            if (!$user) {
                return back()->with('error', 'Aucun compte assuré ne correspond à cet identifiant.')->withInput();
            }

            // Supprimer les anciens codes pour cet email
            ResetCodePasswordUser::where('email', $user->email)->delete();

            // Générer un code OTP 
            $code = rand(1000, 9999);

            ResetCodePasswordUser::create([
                'code' => $code,
                'email' => $user->email,
            ]);

            // Envoyer la notification (toujours par email car c'est le canal configuré)
            $user->notify(new PasswordResetOTP($code, $user->email));

            $routeName = ($user->role === 'assure') ? 'password.reset' : 'portal.password.reset';
            return redirect()->route($routeName, $user->email)->with('success', 'Un code de réinitialisation a été envoyé à votre adresse email (' . $user->email . ').');
        } catch (Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi du code : ' . $e->getMessage());
        }
    }

    public function resetPassword($email)
    {
        $view = \Illuminate\Support\Facades\Route::is('portal.password.reset') ? 'auth.portal-reset-password' : 'auth.reset-password';
        return view($view, compact('email'));
    }

    public function handleResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|exists:reset_code_password_users,code',
            'password' => 'required|min:8|same:confirme_password',
            'confirme_password' => 'required|same:password',
        ], [
            'code.exists' => 'Le code de vérification est invalide.',
            'password.same' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            // Vérifier si le code correspond bien à cet email
            $resetCode = ResetCodePasswordUser::where('email', $request->email)
                ->where('code', $request->code)
                ->first();

            if (!$resetCode) {
                return back()->with('error', 'Le code de vérification ne correspond pas à cet email.');
            }

            // Mettre à jour le mot de passe
            $user->password = Hash::make($request->password);
            $user->save();

            // Supprimer le code utilisé
            $resetCode->delete();

            $loginRoute = ($user->role === 'assure') ? 'login' : 'portal.login';
            return redirect()->route($loginRoute)->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
        } catch (Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la réinitialisation : ' . $e->getMessage());
        }
    }

    public function defineAccess($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return view('auth.define-access', compact('email'));
        } else {
            return redirect()->route('login')->with('error', 'Lien invalide ou expiré.');
        }
    }

    public function submitDefineAccess(Request $request)
    {

        // Validation des données
        $request->validate([
            'code' => 'required|exists:reset_code_password_users,code',
            'password' => 'required|same:confirme_password|min:8',
            'confirme_password' => 'required|same:password',
        ], [
            'code.exists' => 'Le code de réinitialisation est invalide.',
            'password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.same' => 'Les mots de passe doivent être identiques.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);
        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Mise à jour du mot de passe et activation du compte
                $user->email_verified_at = now();
                $user->password = Hash::make($request->password);
                $user->must_change_password = false;
                $user->save();

                ResetCodePasswordUser::where('email', $user->email)->delete();

                // Connexion automatique après activation
                \Illuminate\Support\Facades\Auth::guard('user')->login($user);

                return $this->redirectBasedOnRole($user)->with('success', 'Compte activé avec succès. Bienvenue sur Claims Master !');
            } else {
                return redirect()->route('portal.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    public function showAssureLogin()
    {
        if (auth('user')->check()) {
            return $this->redirectBasedOnRole(auth('user')->user());
        }
        return view('auth.login');
    }

    public function showPortalLogin()
    {
        if (auth('user')->check()) {
            return $this->redirectBasedOnRole(auth('user')->user());
        }
        return view('auth.portal-login');
    }

    protected function redirectBasedOnRole($user)
    {
        $role = $user->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'assurance') return redirect()->route('assurance.dashboard');
        if ($role === 'police') return redirect()->route('police.dashboard');
        if ($role === 'gendarmerie') return redirect()->route('gendarmerie.dashboard');
        if ($role === 'assure') return redirect()->route('assure.dashboard');
        if ($role === 'personnel') return redirect()->route('personnel.dashboard');
        if ($role === 'agent') {
            return redirect()->route('agent.dashboard');
        }

        return redirect()->route('home');
    }

    public function handleAssureLogin(Request $request)
    {
        return $this->processLogin($request, 'assure');
    }

    public function handlePortalLogin(Request $request)
    {
        return $this->processLogin($request, 'portal');
    }

    protected function processLogin(Request $request, $type)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|min:8',
        ], [
            'login.required' => 'L\'identifiant est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            $login = trim($request->login);
            $password = $request->password;
            $mode = $request->input('login_mode', 'email'); // 'id' ou 'email'

            // --- Détection : code_user (CM-XXXXXX-YYYY), téléphone (numérique) ou email ---
            $isCodeUser = (bool) preg_match('/^CM-[A-Z0-9]{6}-\d{4}$/i', $login);
            $isPhoneNumber = (bool) preg_match('/^[0-9+]{8,15}$/', $login);
            $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

            // Validation stricte pour les assurés
            if ($type === 'assure') {
                if ($mode === 'id' && $isEmail) {
                    return redirect()->back()->with('error', 'Veuillez saisir un identifiant ou un numéro de téléphone, pas une adresse email.')->withInput();
                }
                if ($mode === 'email' && !$isEmail) {
                    return redirect()->back()->with('error', 'Veuillez saisir une adresse email valide.')->withInput();
                }
            }

            if ($isCodeUser || ($isPhoneNumber && $type === 'assure')) {
                // Recherche par code_user ou téléphone (uniquement pour les assurés)
                $query = User::where('role', 'assure');

                if ($isCodeUser) {
                    $query->where('code_user', strtoupper($login));
                } else {
                    $query->where('contact', $login);
                }

                $user = $query->first();

                if (!$user) {
                    $msg = $isCodeUser ? 'Code assuré invalide.' : 'Numéro de téléphone non reconnu.';
                    return redirect()->back()->with('error', $msg)->withInput($request->only('login'));
                }

                if (!Hash::check($password, $user->password)) {
                    return redirect()->back()
                        ->with('error', 'Mot de passe incorrect.')
                        ->withInput($request->only('login'));
                }

                \Illuminate\Support\Facades\Auth::guard('user')->login($user);
                return redirect()->route('assure.dashboard')->with('success', 'Bienvenue sur votre espace assuré.');
            } else {
                // Authentification par email
                if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                    return redirect()->back()
                        ->with('error', 'Veuillez saisir une adresse email valide.')
                        ->withInput($request->only('login'));
                }

                $user = User::where('email', $login)->first();

                if (!$user) {
                    return redirect()->back()
                        ->with('error', 'Cette adresse email n\'existe pas.')
                        ->withInput($request->only('login'));
                }

                // Restriction de rôle selon le portail
                if ($type === 'assure' && $user->role !== 'assure') {
                    return redirect()->back()->with('error', 'Ce compte n\'est pas un compte assuré.')->withInput();
                }

                if ($type === 'portal' && $user->role === 'assure') {
                    return redirect()->back()->with('error', 'Les assurés doivent se connecter via l\'interface dédiée.')->withInput();
                }

                if (\Illuminate\Support\Facades\Auth::guard('user')->attempt(['email' => $login, 'password' => $password])) {
                    $user = \Illuminate\Support\Facades\Auth::guard('user')->user();

                    if (is_null($user->email_verified_at)) {
                        \Illuminate\Support\Facades\Auth::guard('user')->logout();
                        return redirect()->back()
                            ->with('error', 'Vous devez vérifier votre adresse email.')
                            ->withInput($request->only('login'));
                    }

                    return $this->redirectBasedOnRole($user);
                } else {
                    return redirect()->back()
                        ->with('error', 'Mot de passe incorrect.')
                        ->withInput($request->only('login'));
                }
            }
        } catch (Exception $e) {
            Log::error('Erreur lors de la connexion : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la connexion');
        }
    }
}
