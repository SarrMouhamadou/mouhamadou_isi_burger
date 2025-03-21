<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminGestionnaireController;
use App\Http\Controllers\GestionnaireDashboardController;
use App\Http\Controllers\Auth\ResetPasswordAndProfileController;
use Illuminate\Support\Facades\Auth;

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

    // Par défaut
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
        Route::resource('payments', \App\Http\Controllers\PaymentController::class);
        Route::get('/gestionnaire/dashboard', [GestionnaireDashboardController::class, 'index'])->name('gestionnaire.dashboard');
    });
});
