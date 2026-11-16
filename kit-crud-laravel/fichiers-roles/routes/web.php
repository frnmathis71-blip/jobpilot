<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// >>> KIT CRUD (début) — ne pas modifier ces deux lignes de repère
// 1) Réservé au rôle (et à l'admin) : créer, modifier, supprimer
//    Ce groupe DOIT être avant le groupe 2, sinon /xxx/create renvoie 404
Route::middleware(['auth', 'NOM_MIDDLEWARE'])->group(function () {
    Route::resource('PARENT_PLURIEL', \App\Http\Controllers\PARENT_MODELEController::class)
        ->except(['index', 'show'])
        ->parameters(['PARENT_PLURIEL' => 'PARENT_SINGULIER']);

    Route::resource('PLURIEL', \App\Http\Controllers\MODELEController::class)
        ->except(['index', 'show'])
        ->parameters(['PLURIEL' => 'SINGULIER']);
});

// 2) Tout utilisateur connecté : consulter
Route::middleware(['auth'])->group(function () {
    Route::resource('PARENT_PLURIEL', \App\Http\Controllers\PARENT_MODELEController::class)
        ->only(['index', 'show'])
        ->parameters(['PARENT_PLURIEL' => 'PARENT_SINGULIER']);

    Route::resource('PLURIEL', \App\Http\Controllers\MODELEController::class)
        ->only(['index', 'show'])
        ->parameters(['PLURIEL' => 'SINGULIER']);
});
// <<< KIT CRUD (fin)
