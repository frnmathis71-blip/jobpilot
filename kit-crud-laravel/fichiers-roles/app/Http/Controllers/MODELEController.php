<?php

namespace App\Http\Controllers;

use App\Models\MODELE;
use App\Models\PARENT_MODELE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MODELEController extends Controller
{
    public function index()
    {
        $PLURIEL = MODELE::with('PARENT_SINGULIER')->get();

        return view('PLURIEL.index', compact('PLURIEL'));
    }

    public function create()
    {
        Gate::authorize('create', MODELE::class);

        $PARENT_PLURIEL = PARENT_MODELE::all();

        return view('PLURIEL.create', compact('PARENT_PLURIEL'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MODELE::class);

        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
            'PARENT_SINGULIER_id' => 'required|exists:PARENT_PLURIEL,id',
        ]);

        MODELE::create($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Ajouté avec succès.');
    }

    public function show(MODELE $SINGULIER)
    {
        return view('PLURIEL.show', compact('SINGULIER'));
    }

    public function edit(MODELE $SINGULIER)
    {
        Gate::authorize('update', $SINGULIER);

        $PARENT_PLURIEL = PARENT_MODELE::all();

        return view('PLURIEL.edit', compact('SINGULIER', 'PARENT_PLURIEL'));
    }

    public function update(Request $request, MODELE $SINGULIER)
    {
        Gate::authorize('update', $SINGULIER);

        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
            'PARENT_SINGULIER_id' => 'required|exists:PARENT_PLURIEL,id',
        ]);

        $SINGULIER->update($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Modifié avec succès.');
    }

    public function destroy(MODELE $SINGULIER)
    {
        Gate::authorize('delete', $SINGULIER);

        $SINGULIER->delete();

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Supprimé avec succès.');
    }
}
