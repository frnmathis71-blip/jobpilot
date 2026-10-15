<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return view('company.index', compact('companies'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        Company::create($validated);

        return redirect()
            ->route('company.index')
            ->with('succes', 'Entreprise ajoutée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('company.edit', compact('company'));   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $company->update($validated);

        return redirect()
            ->route('company.index')
            ->with('succes', 'Entreprise ajoutée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        
        return redirect()
            ->route('company.index')
            ->with('succes', 'Entreprise supprimée avec succès');
    }
}
