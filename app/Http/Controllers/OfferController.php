<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Company;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::all();
        return view('offer.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('offer.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferRequest $request)
    {
        $validated=$request->validate([
            'company_id'=>'required',
            'Salary'=>'required',
            'Name'=>'required',
            'Description'=>'required',
        ]);

        Offer::create($validated);

        return redirect()
            ->route('offer.index')
            ->with('succes', 'Offre ajoutée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        return view('offer.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        $companies = Company::all();
        return view('offer.edit', compact('companies', 'offer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $validated=$request->validate([
            'company_id'=>'required',
            'Salary'=>'required',
            'Name'=>'required',
            'Description'=>'required',
        ]);

        $offer->update($validated);

        return redirect()
            ->route('offer.index')
            ->with('succes', 'Offre ajoutée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()
            ->route('offer.index')
            ->with('succes', 'Offre supprimée avec succès');
    }
}
