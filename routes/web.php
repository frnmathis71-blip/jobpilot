<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OfferController;

Route::view('/', 'welcome')->name('home');

// Consultation : candidat et administrateur
Route::middleware(['auth'])->group(function () {

    Route::resource('candidat', CandidatController::class)
        ->only(['index', 'show']);

    Route::resource('offer', OfferController::class)
        ->only(['index', 'show']);
});

// Administration
Route::middleware(['auth', 'IsAdmin'])->group(function () {

    Route::resource('candidat', CandidatController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('company', CompanyController::class);

    Route::resource('offer', OfferController::class)
        ->only(['destroy']);
});

// Entreprises
Route::middleware(['auth', 'IsCompany'])->group(function () {

    Route::resource('offer', OfferController::class)
        ->only(['create', 'store', 'edit', 'update']);
});

require __DIR__.'/settings.php';