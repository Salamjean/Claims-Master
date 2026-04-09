<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExpertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experts = auth('user')->user()->experts()->latest()->paginate(10);
        return view('assurance.experts.index', compact('experts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assurance.experts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:50',
            'adresse' => 'required|string|max:255',
        ]);

        $expert = new User();
        $expert->name = $request->name;
        $expert->prenom = $request->prenom;
        $expert->email = $request->email;
        $expert->contact = $request->contact;
        $expert->adresse = $request->adresse;
        $expert->role = 'expert';
        $expert->assurance_id = auth('user')->id();
        $expert->password = Hash::make('password123'); // Default password, they can change it later
        $expert->code_user = 'EXP-' . strtoupper(Str::random(6)); // Example logic
        $expert->save();

        return redirect()->route('assurance.experts.index')->with('success', 'L\'expert a été ajouté avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $expert)
    {
        // Ensure this expert belongs to this assurance
        if ($expert->assurance_id !== auth('user')->id()) {
            abort(403);
        }
        return view('assurance.experts.edit', compact('expert'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $expert)
    {
        if ($expert->assurance_id !== auth('user')->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $expert->id,
            'contact' => 'required|string|max:50',
            'adresse' => 'required|string|max:255',
        ]);

        $expert->update($request->only('name', 'prenom', 'email', 'contact', 'adresse'));

        return redirect()->route('assurance.experts.index')->with('success', 'Informations de l\'expert mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $expert)
    {
        if ($expert->assurance_id !== auth('user')->id()) {
            abort(403);
        }

        $expert->delete();

        return redirect()->route('assurance.experts.index')->with('success', 'L\'expert a été retiré.');
    }
}
