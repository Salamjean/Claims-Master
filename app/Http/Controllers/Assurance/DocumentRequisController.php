<?php

namespace App\Http\Controllers\Assurance;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequisController extends Controller
{
    // Liste fixe globale des types de sinistre
    protected $typesSinistre = [
        'Accident_matériel' => ['label' => 'Accident matériel', 'icon' => 'fa-car-burst', 'color' => 'blue'],
        'Accident_corporel' => ['label' => 'Accident corporel', 'icon' => 'fa-crutch', 'color' => 'red'],
        'Vol' => ['label' => 'Vol de véhicule / Braquage', 'icon' => 'fa-mask', 'color' => 'slate'],
        'Incendie' => ['label' => 'Incendie / Feu', 'icon' => 'fa-fire', 'color' => 'orange'],
        'Bris_de_glace' => ['label' => 'Bris de glace pur', 'icon' => 'fa-house-crack', 'color' => 'sky'],
        'Autre' => ['label' => 'Autre incident', 'icon' => 'fa-triangle-exclamation', 'color' => 'yellow']
    ];

    public function index()
    {
        $types = $this->typesSinistre;
        return view('assurance.documents-requis.index', compact('types'));
    }

    public function show($type_sinistre)
    {
        if (!array_key_exists($type_sinistre, $this->typesSinistre)) {
            return redirect()->route('assurance.documents-requis.index')->with('error', 'Type de sinistre invalide.');
        }

        $typeInfo = $this->typesSinistre[$type_sinistre];

        $documents = DocumentRequis::where('user_id', Auth::id())
            ->where('type_sinistre', $type_sinistre)
            ->get();

        return view('assurance.documents-requis.manage', compact('type_sinistre', 'typeInfo', 'documents'));
    }

    public function update(Request $request, $type_sinistre)
    {
        if (!array_key_exists($type_sinistre, $this->typesSinistre)) {
            return redirect()->route('assurance.documents-requis.index')->with('error', 'Type de sinistre invalide.');
        }

        $request->validate([
            'documents' => 'array',
            'documents.*' => 'nullable|string|max:255',
            'types' => 'array',
            'types.*' => 'nullable|in:text,number,file',
        ]);

        $userId = Auth::id();

        // 1. Supprimer les anciennes configuration pour ce type
        DocumentRequis::where('user_id', $userId)
            ->where('type_sinistre', $type_sinistre)
            ->delete();

        // 2. Insérer les nouvelles
        if ($request->has('documents')) {
            foreach ($request->documents as $index => $nom_document) {
                if (!is_null($nom_document) && trim($nom_document) !== '') {
                    $type_champ = $request->types[$index] ?? 'file';
                    DocumentRequis::create([
                        'user_id' => $userId,
                        'type_sinistre' => $type_sinistre,
                        'nom_document' => trim($nom_document),
                        'type_champ' => $type_champ,
                    ]);
                }
            }
        }

        return redirect()->route('assurance.documents-requis.show', $type_sinistre)
            ->with('success', 'Les documents requis ont été mis à jour avec succès.');
    }
}
