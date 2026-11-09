<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

/**
 * Contrôleur des entreprises (CRUD complet).
 *
 * Toutes les routes sont réservées aux administrateurs
 * (middleware IsAdmin dans routes/web.php).
 */
class CompanyController extends Controller
{
    /**
     * GET /company : liste de toutes les entreprises.
     */
    public function index()
    {
        $companies = Company::all();
        return view('company.index', compact('companies'));

    }

    /**
     * GET /company/create : formulaire de création d'une entreprise.
     */
    public function create()
    {
        return view('company.create');
    }

    /**
     * POST /company : enregistre la nouvelle entreprise.
     */
    public function store(StoreCompanyRequest $request)
    {
        // Le nom est obligatoire.
        $validated = $request->validate([
            'name' => 'required',
        ]);

        Company::create($validated);

        return redirect()
            ->route('company.index')
            ->with('succes', 'Entreprise ajoutée avec succès');
    }

    /**
     * GET /company/{company} : détail d'une entreprise et de ses offres.
     */
    public function show(Company $company)
    {
        return view('company.show', compact('company'));
    }

    /**
     * GET /company/{company}/edit : formulaire de modification.
     */
    public function edit(Company $company)
    {
        return view('company.edit', compact('company'));
    }

    /**
     * PUT /company/{company} : enregistre les modifications.
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
     * DELETE /company/{company} : supprime l'entreprise.
     * Ses offres et son compte utilisateur sont aussi supprimés
     * (cascadeOnDelete dans les migrations).
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('company.index')
            ->with('succes', 'Entreprise supprimée avec succès');
    }
}
