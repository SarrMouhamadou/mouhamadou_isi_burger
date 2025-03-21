<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminGestionnaireController;
use App\Http\Controllers\GestionnaireDashboardController;
use App\Http\Controllers\Auth\ResetPasswordAndProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes pour le panier et la wishlist (accessibles à tous)
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/wishlist/toggle/{id}', [CartController::class, 'toggleWishlist'])->name('wishlist.toggle');

// Route pour passer une commande (exige une authentification)
Route::post('/order/place', [OrderController::class, 'place'])->name('order.place')->middleware('auth');

// Routes publiques pour la réinitialisation du mot de passe et la mise à jour du profil
Route::get('/password/reset/{token}', [ResetPasswordAndProfileController::class, 'showForm'])
    ->name('password.reset.profile');

Route::post('/password/reset', [ResetPasswordAndProfileController::class, 'update'])
    ->name('password.update.profile')
    ->middleware('guest');

// Routes d'authentification (générées par Breeze)
require __DIR__ . '/auth.php';

// Dashboard (après connexion) - Redirection basée sur le rôle
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role->name === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role->name === 'gestionnaire') {
        return redirect()->route('gestionnaire.dashboard');
    }

    // Rediriger les clients vers la page d'accueil (welcome.blade.php)
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes de gestion de profil (générées par Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour les ressources (burgers, orders, payments)
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes accessibles uniquement à l'Admin
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Route API pour charger les données dynamiquement
        Route::get('/data/{section}', [AdminDashboardController::class, 'data']);

        // Routes pour les actions sur les gestionnaires
        Route::post('/gestionnaires', [AdminGestionnaireController::class, 'store']);
        Route::get('/gestionnaires/{gestionnaire}', [AdminGestionnaireController::class, 'show']);
        Route::post('/gestionnaires/{gestionnaire}/toggle-status', [AdminGestionnaireController::class, 'toggleStatus'])->name('admin.gestionnaires.toggle-status');
        Route::delete('/gestionnaires/{gestionnaire}', [AdminGestionnaireController::class, 'destroy'])->name('admin.gestionnaires.destroy');
    });

    // Routes accessibles uniquement aux gestionnaires
    Route::middleware('role:gestionnaire')->group(function () {
        Route::resource('burgers', \App\Http\Controllers\BurgerController::class);
        Route::resource('orders', \App\Http\Controllers\OrderController::class);
        // Exclure 'index' de la ressource payments pour éviter le middleware role:gestionnaire
        Route::resource('payments', \App\Http\Controllers\PaymentController::class)->except(['index']);
        Route::get('/gestionnaire/dashboard', [GestionnaireDashboardController::class, 'index'])->name('gestionnaire.dashboard');
        Route::get('/gestionnaire/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('gestionnaire.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('gestionnaire.orders.show');
        Route::patch('/gestionnaire/orders/{order}', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('gestionnaire.orders.update');
    });

    // Routes accessibles aux clients
    Route::middleware('role:client')->group(function () {
        Route::get('/burgers/{burger}', [\App\Http\Controllers\BurgerController::class, 'show'])->name('burgers.show');

        // Routes pour le panier
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

        Route::resource('orders', OrderController::class)->only(['index']);
        // Exclure 'index' ici aussi si nécessaire
        Route::resource('payments', \App\Http\Controllers\PaymentController::class)->only(['index']);
    });

    // Route pour payments.index, accessible à tous les utilisateurs authentifiés
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
});
