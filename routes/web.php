<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\JobPostingController;

Route::view('/', 'welcome')->name('home');
Route::view('/dashboard', 'dashboard')->name('dashboard');

// IMPORTANT : l'ordre des groupes compte.
// Laravel retient la PREMIÈRE route qui correspond à l'URL. Les routes
// statiques (/offer/create, /candidat/create) doivent donc être déclarées
// AVANT les routes paramétrées (/offer/{offer}, /candidat/{candidat}),
// sinon "create" est interprété comme un identifiant et la page renvoie 404.

// Entreprises : créer et modifier des offres
Route::middleware(['auth', 'IsCompany'])->group(function () {

    Route::resource('offer', OfferController::class)
        ->only(['create', 'store', 'edit', 'update']);

    // Candidatures reçues sur les offres de l'entreprise
    Route::resource('jobposting', JobPostingController::class)
        ->only(['index']);
});

// Administration : gérer les candidats, les entreprises, supprimer des offres
Route::middleware(['auth', 'IsAdmin'])->group(function () {

    Route::resource('candidat', CandidatController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('company', CompanyController::class);

    Route::resource('offer', OfferController::class)
        ->only(['destroy']);
    
});

Route::middleware(['auth', 'IsCandidat'])->group(function () {

    // Formulaire de candidature : l'offre visée est dans l'URL (/offer/3/jobposting/create)
    Route::get('/offer/{offer}/jobposting/create', [JobPostingController::class, 'create'])
        ->name('jobposting.create');

    Route::resource('jobposting', JobPostingController::class)
        ->only(['store']);
});

// Consultation (lecture seule) : tout utilisateur connecté
Route::middleware(['auth'])->group(function () {

    Route::resource('candidat', CandidatController::class)
        ->only(['index', 'show']);

    Route::resource('offer', OfferController::class)
        ->only(['index', 'show']);

});

require __DIR__.'/settings.php';



