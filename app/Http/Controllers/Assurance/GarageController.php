<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GarageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $garages = auth('user')->user()->garages()->latest()->paginate(10);
        return view('assurance.garages.index', compact('garages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assurance.garages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|string|max:50',
            'adresse' => 'required|string|max:255',
        ]);

        $garage = new User();
        $garage->name = $request->name;
        $garage->prenom = 'Garage'; // Fix: The table requires this field but garages don't use it
        $garage->email = $request->email;
        $garage->contact = $request->contact;
        $garage->adresse = $request->adresse;
        $garage->role = 'garage';
        $garage->assurance_id = auth('user')->id();
        $garage->password = Hash::make('password123'); // Default password
        $garage->code_user = 'GAR-' . strtoupper(Str::random(6)); // Example logic
        $garage->save();

        return redirect()->route('assurance.garages.index')->with('success', 'Le garage a été ajouté avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $garage)
    {
        // Ensure this garage belongs to this assurance
        if ($garage->assurance_id !== auth('user')->id()) {
            abort(403);
        }
        return view('assurance.garages.edit', compact('garage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $garage)
    {
        if ($garage->assurance_id !== auth('user')->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $garage->id,
            'contact' => 'required|string|max:50',
            'adresse' => 'required|string|max:255',
        ]);

        $garage->update($request->only('name', 'email', 'contact', 'adresse'));

        return redirect()->route('assurance.garages.index')->with('success', 'Informations du garage mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $garage)
    {
        if ($garage->assurance_id !== auth('user')->id()) {
            abort(403);
        }

        $garage->delete();

        return redirect()->route('assurance.garages.index')->with('success', 'Le garage a été retiré.');
    }
}
