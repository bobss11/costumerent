<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\RentalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public catalog (landing)
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'index'])->name('customer.catalog');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/costumes/{costume}', [CatalogController::class, 'show'])->name('costumes.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin & Staff area (operational) — routes added by later features.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard, pickup queue, return queue, costumes, categories (FEAT-003/004/005).
    });

/*
|--------------------------------------------------------------------------
| Customer area — routes added by later features.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
        Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    });
