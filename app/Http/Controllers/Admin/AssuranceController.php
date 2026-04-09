<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssuranceProfile;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\sendEmailAfterUserRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AssuranceController extends Controller
{
    /**
     * Afficher la liste des assurances
     */
    public function index()
    {
        $assurances = User::where('role', 'assurance')
            ->with('assuranceProfile')
            ->latest()
            ->paginate(15);

        return view('admin.assurances.index', compact('assurances'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.assurances.create');
    }

    /**
     * Enregistrer une nouvelle assurance
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'numero_rccm' => 'nullable|string|max:100',
            'path_rccm' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'numero_dfe' => 'nullable|string|max:100',
            'path_dfe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'name.required' => 'Le nom de la compagnie est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'contact.required' => 'Le contact est obligatoire.',
            'path_rccm.mimes' => 'La fiche RCCM doit être un fichier PDF, JPG ou PNG.',
            'path_rccm.max' => 'La fiche RCCM ne doit pas dépasser 5 Mo.',
            'path_dfe.mimes' => 'La fiche DFE doit être un fichier PDF, JPG ou PNG.',
            'path_dfe.max' => 'La fiche DFE ne doit pas dépasser 5 Mo.',
        ]);

        try {
            DB::beginTransaction();

            // Création du compte utilisateur avec le rôle 'assurance'
            $user = new User();
            $user->name = $request->name;
            $user->prenom = $request->prenom;
            $user->email = $request->email;
            $user->contact = $request->contact;
            $user->commune = $request->commune;
            $user->adresse = $request->adresse;
            $user->role = 'assurance'; // forcé
            $user->password = Hash::make('default'); // mot de passe temporaire
            $user->save();

            // Stockage des fichiers RCCM et DFE
            $pathRccm = null;
            $pathDfe = null;

            if ($request->hasFile('path_rccm')) {
                $pathRccm = $request->file('path_rccm')->store('assurances/rccm', 'public');
            }

            if ($request->hasFile('path_dfe')) {
                $pathDfe = $request->file('path_dfe')->store('assurances/dfe', 'public');
            }

            // Création du profil assurance lié
            $user->assuranceProfile()->create([
                'numero_rccm' => $request->numero_rccm,
                'path_rccm' => $pathRccm,
                'numero_dfe' => $request->numero_dfe,
                'path_dfe' => $pathDfe,
            ]);

            // Génération d'un code d'activation et envoi email
            ResetCodePasswordUser::where('email', $user->email)->delete();
            $code = rand(1000, 4000) . '' . $user->id;

            ResetCodePasswordUser::create([
                'code' => $code,
                'email' => $user->email,
            ]);

            Notification::route('mail', $user->email)
                ->notify(new sendEmailAfterUserRegister($code, $user->email));

            DB::commit();

            return redirect()->route('admin.assurances.index')
                ->with('success', 'La compagnie d\'assurance a été inscrite avec succès. Un email d\'activation a été envoyé.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur inscription assurance : ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.'])
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'une assurance
     */
    public function show(User $user)
    {
        $user->load('assuranceProfile');
        return view('admin.assurances.show', compact('user'));
    }

    /**
     * Supprimer une assurance
     */
    public function destroy(User $user)
    {
        // Supprimer les fichiers liés si existants
        if ($user->assuranceProfile) {
            if ($user->assuranceProfile->path_rccm) {
                Storage::disk('public')->delete($user->assuranceProfile->path_rccm);
            }
            if ($user->assuranceProfile->path_dfe) {
                Storage::disk('public')->delete($user->assuranceProfile->path_dfe);
            }
        }

        $user->delete(); // cascade supprime aussi le profil

        return redirect()->route('admin.assurances.index')
            ->with('success', 'Assurance supprimée avec succès.');
    }
}
