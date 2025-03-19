<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BurgerController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('burgers', BurgerController::class);
Route::post('burgers/{burger}/archive', [BurgerController::class, 'archive'])->name('burgers.archive');

