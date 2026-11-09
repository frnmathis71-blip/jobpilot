<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Http\Requests\StoreCandidatRequest;
use App\Http\Requests\UpdateCandidatRequest;

/**
 * Contrôleur des candidats (CRUD complet).
 *
 * Qui a accès à quoi (voir routes/web.php) :
 *  - index, show                                → tout utilisateur connecté
 *  - create, store, edit, update, destroy       → administrateurs (IsAdmin)
 */
class CandidatController extends Controller
{
    /**
     * GET /candidat : liste de tous les candidats.
     */
    public function index()
    {
        $candidats = Candidat::all();
        return view('candidat.index', compact('candidats'));
    }

    /**
     * GET /candidat/create : formulaire de création d'un candidat.
     */
    public function create()
    {
        return view('candidat.create');
    }

    /**
     * POST /candidat : enregistre le nouveau candidat.
     */
    public function store(StoreCandidatRequest $request)
    {
        // Le nom complet est obligatoire.
        $validated = $request->validate([
            'fullname' => 'required',
        ]);

        Candidat::create($validated);

        return redirect()
            ->route('candidat.index')
            ->with('succes', 'Candidat ajoutée avec succès.');
    }

    /**
     * GET /candidat/{candidat} : détail d'un candidat.
     */
    public function show(Candidat $candidat)
    {
        return view('candidat.show', compact('candidat'));
    }

    /**
     * GET /candidat/{candidat}/edit : formulaire de modification.
     */
    public function edit(Candidat $candidat)
    {
        return view('candidat.edit', compact('candidat'));
    }

    /**
     * PUT /candidat/{candidat} : enregistre les modifications.
     */
    public function update(UpdateCandidatRequest $request, Candidat $candidat)
    {
        $validated = $request->validate([
            'fullname' => 'required',
        ]);

        $candidat->update($validated);

        return redirect()
            ->route('candidat.index')
            ->with('succes', 'Candidat modifiée avec succès.');
    }

    /**
     * DELETE /candidat/{candidat} : supprime le candidat
     * (et son compte utilisateur, via cascadeOnDelete).
     */
    public function destroy(Candidat $candidat)
    {
        $candidat->delete();
        return redirect()
            ->route('candidat.index')
            ->with('success', 'candidat supprimée avec succès.');
    }
}
