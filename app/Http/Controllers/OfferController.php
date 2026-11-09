<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Company;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;

/**
 * Contrôleur des offres d'emploi (CRUD complet).
 *
 * Qui a accès à quoi (voir routes/web.php) :
 *  - index, show                   → tout utilisateur connecté
 *  - create, store, edit, update   → entreprises (middleware IsCompany)
 *  - destroy                       → administrateurs (middleware IsAdmin)
 */
class OfferController extends Controller
{
    /**
     * GET /offer : liste de toutes les offres.
     */
    public function index()
    {
        $offers = Offer::all();
        return view('offer.index', compact('offers'));
    }

    /**
     * GET /offer/create : formulaire de création d'une offre.
     * On envoie la liste des entreprises pour remplir le menu déroulant.
     */
    public function create()
    {
        $companies = Company::all();
        return view('offer.create', compact('companies'));
    }

    /**
     * POST /offer : enregistre la nouvelle offre envoyée par le formulaire.
     */
    public function store(StoreOfferRequest $request)
    {
        // Tous les champs sont obligatoires. Les noms (Salary, Name…)
        // correspondent aux colonnes de la table offers et aux "name" du formulaire.
        $validated=$request->validate([
            'company_id'=>'required',
            'Salary'=>'required',
            'Name'=>'required',
            'Description'=>'required',
        ]);

        // Création en base (seuls les champs de $fillable du modèle sont acceptés).
        Offer::create($validated);

        // Retour à la liste avec un message flash.
        return redirect()
            ->route('offer.index')
            ->with('succes', 'Offre ajoutée avec succès');
    }

    /**
     * GET /offer/{offer} : détail d'une offre.
     * Laravel retrouve automatiquement l'offre grâce à l'id dans l'URL
     * (route model binding) et renvoie 404 si elle n'existe pas.
     */
    public function show(Offer $offer)
    {
        return view('offer.show', compact('offer'));
    }

    /**
     * GET /offer/{offer}/edit : formulaire de modification d'une offre.
     */
    public function edit(Offer $offer)
    {
        $companies = Company::all();
        return view('offer.edit', compact('companies', 'offer'));
    }

    /**
     * PUT /offer/{offer} : enregistre les modifications d'une offre.
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
     * DELETE /offer/{offer} : supprime une offre (admin uniquement).
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()
            ->route('offer.index')
            ->with('succes', 'Offre supprimée avec succès');
    }
}
