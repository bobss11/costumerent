<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CostumeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PickupController;
use App\Http\Controllers\Admin\ReturnInspectionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\RentalController;
use App\Http\Controllers\InvoiceController;
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
| Invoice / handover proof (print-ready) — keyed by rental_code.
| Viewable by the owning customer or an admin/staff (enforced in controller).
|--------------------------------------------------------------------------
*/
Route::get('/invoice/{rental_code}', [InvoiceController::class, 'show'])
    ->middleware('auth')
    ->name('invoice.show');

/*
|--------------------------------------------------------------------------
| Admin & Staff area (operational) — routes added by later features.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Executive KPI dashboard (also hosts the pickup & return queues).
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Pickup queue + the queue itself lives on the dashboard. The admin
        // layout nav references admin.pickups.index / admin.returns.index, so
        // alias both to the dashboard where the queues are displayed.
        Route::get('/pickups', [DashboardController::class, 'index'])->name('pickups.index');
        Route::get('/returns', [DashboardController::class, 'index'])->name('returns.index');

        // Pickup / POS verification actions.
        Route::get('/rentals/{rental}/pickup', [PickupController::class, 'edit'])->name('pickups.edit');
        Route::match(['put', 'patch'], '/rentals/{rental}/pickup', [PickupController::class, 'update'])->name('pickups.update');

        // Return inspection & deposit settlement actions.
        Route::get('/rentals/{rental}/return', [ReturnInspectionController::class, 'create'])->name('returns.create');
        Route::post('/rentals/{rental}/return', [ReturnInspectionController::class, 'store'])->name('returns.store');

        // Master data CRUD.
        Route::resource('costumes', CostumeController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
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
