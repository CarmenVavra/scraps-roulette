<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/impressum', [PageController::class, 'impressum'])->name('impressum');
Route::get('/datenschutz', [PageController::class, 'datenschutz'])->name('datenschutz');

// AJAX-/Livewire-kompatibler JSON-Endpunkt. Rate-Limit "recipes" siehe AppServiceProvider.
Route::post('/recipes/generate', [RecipeController::class, 'generate'])
    ->middleware('throttle:recipes')
    ->name('recipes.generate');
