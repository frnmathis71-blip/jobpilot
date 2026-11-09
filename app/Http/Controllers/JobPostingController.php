<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\JobPosting;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Offer $offer )
    {
        return view('jobposting.create', compact('offer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // L'offre doit exister dans la table offers
        $validated = $request->validate([
            'offer_id' => 'required|exists:offers,id',
        ]);

        // Enregistre la candidature (pas de doublon si le candidat a déjà postulé)
        JobPosting::firstOrCreate([
            'offer_id' => $validated['offer_id'],
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('offer.index')
            ->with('succes', 'Candidature envoyée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
