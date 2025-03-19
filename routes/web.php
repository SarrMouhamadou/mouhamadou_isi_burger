<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (après connexion)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes de gestion de profil (générées par Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour les ressources (burgers, orders, payments)
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes accessibles uniquement aux gestionnaires
    Route::middleware('role:gestionnaire')->group(function () {
        Route::resource('burgers', \App\Http\Controllers\BurgerController::class);
        Route::resource('orders', \App\Http\Controllers\OrderController::class);
        Route::resource('payments', \App\Http\Controllers\PaymentController::class);
    });
});

// Routes d'authentification (générées par Breeze)
require __DIR__.'/auth.php';
