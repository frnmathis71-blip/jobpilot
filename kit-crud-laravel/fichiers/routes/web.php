<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// >>> KIT CRUD (début) — ne pas modifier ces deux lignes de repère
Route::resource('PARENT_PLURIEL', \App\Http\Controllers\PARENT_MODELEController::class)
    ->parameters(['PARENT_PLURIEL' => 'PARENT_SINGULIER']);

Route::resource('PLURIEL', \App\Http\Controllers\MODELEController::class)
    ->parameters(['PLURIEL' => 'SINGULIER']);
// <<< KIT CRUD (fin)
