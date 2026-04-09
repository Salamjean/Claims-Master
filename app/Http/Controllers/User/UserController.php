<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\sendEmailAfterUserRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => $request->role === 'prestataire' ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => $request->role === 'prestataire' ? 'Le nom du prestataire est requis' : 'Le nom est requis',
            'prenom.required' => 'Le prénom est requis',
            'email.required' => 'L\'email est requis',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact est requis',
            'role.required' => 'Le rôle est requis',
            'adresse.required' => 'L\'adresse est requise',
            'profile_picture.image' => 'La photo de profil doit être une image',
            'profile_picture.mimes' => 'La photo de profil doit être une image',
            'profile_picture.max' => 'La photo de profil doit être une image',
        ]);
        try {
            DB::beginTransaction();

            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return redirect()->back()->withErrors(['email' => 'Cet email est déjà utilisé.'])->withInput();
            }

            $user = new User();
            $user->name = $request->name;
            $user->prenom = $request->prenom;
            $user->email = $request->email;
            $user->contact = $request->contact;
            $user->role = $request->role;
            $user->adresse = $request->adresse;
            $user->password = Hash::make('default');

            if ($request->hasFile('profile_picture')) {
                $request->validate([
                    'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }
            $user->save();

            // Envoi de l'e-mail de vérification
            ResetCodePasswordUser::where('email', $user->email)->delete();
            $code1 = rand(1000, 4000);
            $code = $code1 . '' . $user->id;

            ResetCodePasswordUser::create([
                'code' => $code,
                'email' => $user->email,
            ]);

            Notification::route('mail', $user->email)
                ->notify(new sendEmailAfterUserRegister($code, $user->email));
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'L\'utilisateur a bien été enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement de l\'utilisateur: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.'])->withInput();
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => $request->role === 'prestataire' ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => $request->role === 'prestataire' ? 'Le nom du prestataire est requis' : 'Le nom est requis',
            'prenom.required' => 'Le prénom est requis',
            'email.required' => 'L\'email est requis',
            'email.unique' => 'Cet email est déjà utilisé.',
            'contact.required' => 'Le contact est requis',
            'role.required' => 'Le rôle est requis',
            'adresse.required' => 'L\'adresse est requise',
            'profile_picture.image' => 'La photo de profil doit être une image',
            'profile_picture.mimes' => 'La photo de profil doit être une image',
            'profile_picture.max' => 'La photo de profil doit être une image',
        ]);

        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->prenom = $request->prenom;
            $user->email = $request->email;
            $user->contact = $request->contact;
            $user->role = $request->role;
            $user->adresse = $request->adresse;

            if ($request->hasFile('profile_picture')) {
                // Supprimer l'ancienne photo si elle existe (sauf si c'est une default, à gérer si besoin)
                // if ($user->profile_picture) {
                //     Storage::disk('public')->delete($user->profile_picture);
                // } 
                // Pour l'instant on garde l'ancienne par sécurité ou on laisse Laravel gérer l'écrasement si même nom

                $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $user->save();

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur modifié avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la modification de l\'utilisateur: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue lors de la modification.'])->withInput();
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur archivé avec succès.');
    }

    public function archives()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.users.archives', compact('users'));
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.archives')->with('success', 'Utilisateur restauré avec succès.');
    }
}
