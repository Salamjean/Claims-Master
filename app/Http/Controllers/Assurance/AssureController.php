<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\User;
use App\Notifications\AssureAccessNotification;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AssureController extends Controller
{
    protected YellikaSmsService $sms;

    public function __construct(YellikaSmsService $sms)
    {
        $this->sms = $sms;
    }

    public function index()
    {
        $assures = User::where('role', 'assure')
            ->with('contrats')
            ->latest()
            ->paginate(15);

        return view('assurance.assures.index', compact('assures'));
    }

    public function create()
    {
        return view('assurance.assures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Infos personnelles
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'contact' => 'required|string|max:30',
            'adresse' => 'nullable|string|max:255',
            // Infos contrat
            'numero_contrat' => 'required|string|max:100|unique:contrats,numero_contrat',
            'type_contrat' => 'required|string|in:auto,habitation,sante,vie,autre',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'prime' => 'nullable|numeric|min:0',
            'statut' => 'required|in:actif,suspendu,resilie,expire',
            'document_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'contact.required' => 'Le contact est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'numero_contrat.required' => 'Le numéro de contrat est obligatoire.',
            'numero_contrat.unique' => 'Ce numéro de contrat existe déjà.',
            'type_contrat.required' => 'Le type de contrat est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'statut.required' => 'Le statut du contrat est obligatoire.',
        ]);

        try {
            DB::beginTransaction();

            // Génération du code_user : CM-XXXXXX-YYYY (unique)
            do {
                $random = strtoupper(Str::random(6));
                $codeUser = 'CM-' . $random . '-' . date('Y');
            } while (User::where('code_user', $codeUser)->exists());

            // Génération du mot de passe temporaire de 8 caractères
            $plainPassword = Str::random(8);

            // Création de l'utilisateur assuré
            $user = User::create([
                'name' => $request->name,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'contact' => $request->contact,
                'adresse' => $request->adresse,
                'role' => 'assure',
                'code_user' => $codeUser,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
                'must_change_password' => true,
            ]);

            // Gestion du PDF du contrat
            $pdfPath = null;
            if ($request->hasFile('document_pdf')) {
                $pdfPath = $request->file('document_pdf')
                    ->store('contrats', 'public');
            }

            // Création du contrat lié à l'assuré
            Contrat::create([
                'client_id' => $user->id,
                'numero_contrat' => $request->numero_contrat,
                'type_contrat' => $request->type_contrat,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'prime' => $request->prime,
                'statut' => $request->statut,
                'document_pdf' => $pdfPath,
            ]);

            // Envoi du SMS
            $loginUrl = url('/login');
            $smsMsg = "Bienvenue sur CLAIMS MASTER !\n"
                . "Code assure : {$codeUser}\n"
                . "Mot de passe : {$plainPassword}\n"
                . "Connexion : {$loginUrl}\n"
                . "Conservez ces informations.";

            if (!$this->sms->sendSms($request->contact, $smsMsg)) {
                Log::warning("SMS non envoye pour l'assure {$codeUser} ({$request->contact})");
            }

            // Envoi de l'email si adresse fournie
            if ($user->email) {
                try {
                    $user->notify(new AssureAccessNotification(
                        trim($user->name . ' ' . $user->prenom),
                        $codeUser,
                        $plainPassword
                    ));
                    Log::info("Email accès envoyé à {$user->email} pour l'assuré {$codeUser}");
                } catch (\Exception $e) {
                    Log::error("Échec envoi email accès assuré {$codeUser} : " . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('assurance.assures.index')
                ->with('success', "L'assuré {$user->name} a été inscrit avec succès. Code : {$codeUser}.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur inscription assure : ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load('contrats');
        $sinistres = \App\Models\Sinistre::where('user_id', $user->id)->latest()->get();
        return view('assurance.assures.show', compact('user', 'sinistres'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('assurance.assures.index')
            ->with('success', 'Assuré supprimé avec succès.');
    }
}
