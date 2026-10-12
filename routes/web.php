<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OfferController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::resource('candidat', CandidatController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('offer', OfferController::class);
});



require __DIR__.'/settings.php';
