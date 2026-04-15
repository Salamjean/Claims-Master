<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePasswordUser;
use App\Models\User;
use App\Notifications\PersonnelAccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PersonnelController extends Controller
{
    /**
     * Liste du personnel de l'assurance connectée
     */
    public function index()
    {
        $assurance = auth('user')->user();
        $personnels = User::where('role', 'personnel')
            ->where('assurance_id', $assurance->id)
            ->latest()
            ->paginate(15);

        return view('assurance.personnel.index', compact('personnels'));
    }

    /**
     * Formulaire de création d'un membre du personnel
     */
    public function create()
    {
        return view('assurance.personnel.create');
    }

    /**
     * Enregistre un nouveau membre du personnel et envoie l'email d'accès
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'prenom'  => 'nullable|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'contact' => 'required|string|max:20',
            'poste'   => 'nullable|string|max:100',
        ], [
            'name.required'    => 'Le nom est obligatoire.',
            'email.required'   => 'L\'adresse email est obligatoire.',
            'email.unique'     => 'Cette adresse email est déjà utilisée.',
            'contact.required' => 'Le contact est obligatoire.',
        ]);

        $assurance = auth('user')->user();

        // Code d'activation à 6 chiffres
        $code = rand(100000, 999999);

        // Génération du code_user
        do {
            $random   = strtoupper(Str::random(6));
            $codeUser = 'PRS-' . $random . '-' . date('Y');
        } while (User::where('code_user', $codeUser)->exists());

        $personnel = User::create([
            'name'               => $request->name,
            'prenom'             => $request->prenom,
            'email'              => $request->email,
            'contact'            => $request->contact,
            'role'               => 'personnel',
            'assurance_id'       => $assurance->id,
            'code_user'          => $codeUser,
            'password'           => Hash::make(Str::random(16)),
            'must_change_password' => true,
        ]);

        // Enregistrer le code d'activation
        ResetCodePasswordUser::updateOrCreate(
            ['email' => $personnel->email],
            ['code'  => $code]
        );

        // Envoyer l'email d'invitation avec le code
        $personnel->notify(new PersonnelAccessNotification($personnel, $code, $assurance->name));

        return redirect()
            ->route('assurance.personnel.index')
            ->with('success', "Le membre du personnel « {$personnel->name} » a été créé. Un email d'activation lui a été envoyé.");
    }

    /**
     * Supprime un membre du personnel
     */
    public function destroy(User $personnel)
    {
        abort_if($personnel->assurance_id !== auth('user')->id(), 403);

        $name = $personnel->name;
        $personnel->delete();

        return back()->with('success', "Le personnel « {$name} » a été supprimé.");
    }
}
