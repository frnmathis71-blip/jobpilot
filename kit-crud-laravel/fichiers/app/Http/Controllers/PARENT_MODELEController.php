<?php

namespace App\Http\Controllers;

use App\Models\PARENT_MODELE;
use Illuminate\Http\Request;

class PARENT_MODELEController extends Controller
{
    public function index()
    {
        $PARENT_PLURIEL = PARENT_MODELE::with('PLURIEL')->get();

        return view('PARENT_PLURIEL.index', compact('PARENT_PLURIEL'));
    }

    public function create()
    {
        return view('PARENT_PLURIEL.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'PARENT_CHAMP' => 'required|string|max:255',
        ]);

        PARENT_MODELE::create($validated);

        return redirect()
            ->route('PARENT_PLURIEL.index')
            ->with('success', 'Ajouté avec succès.');
    }

    public function show(PARENT_MODELE $PARENT_SINGULIER)
    {
        return view('PARENT_PLURIEL.show', compact('PARENT_SINGULIER'));
    }

    public function edit(PARENT_MODELE $PARENT_SINGULIER)
    {
        return view('PARENT_PLURIEL.edit', compact('PARENT_SINGULIER'));
    }

    public function update(Request $request, PARENT_MODELE $PARENT_SINGULIER)
    {
        $validated = $request->validate([
            'PARENT_CHAMP' => 'required|string|max:255',
        ]);

        $PARENT_SINGULIER->update($validated);

        return redirect()
            ->route('PARENT_PLURIEL.index')
            ->with('success', 'Modifié avec succès.');
    }

    public function destroy(PARENT_MODELE $PARENT_SINGULIER)
    {
        $PARENT_SINGULIER->delete();

        return redirect()
            ->route('PARENT_PLURIEL.index')
            ->with('success', 'Supprimé avec succès.');
    }
}
