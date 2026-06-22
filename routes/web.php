<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginRegister'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showLoginRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/services', [ServiceController::class, 'index'])
    ->name('services.index');

Route::get('/services/{service}', [ServiceController::class, 'show'])
    ->name('services.show');

Route::post('/services/{service}', [ServiceController::class, 'store'])
    ->name('services.store');

Route::get('/facilities', [FacilityController::class, 'index'])
    ->name('facilities.index');

Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'show'])
        ->name('account.profile');

    Route::put('/account/profile', [ProfileController::class, 'update'])
        ->name('account.profile.update');

    Route::get('/account/password', [ProfileController::class, 'editPassword'])
        ->name('account.password.edit');

    Route::put('/account/password', [ProfileController::class, 'updatePassword'])
        ->name('account.password.update');
    Route::put('/account/profile/photo', [ProfileController::class, 'updatePhoto'])
    ->name('account.profile.photo.update');
});