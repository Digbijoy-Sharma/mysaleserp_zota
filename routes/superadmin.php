<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dava India — Super Admin Routes
|--------------------------------------------------------------------------
| /super/* — Dava India super admin panel (cross-store management).
| Gated by EnsureSuperAdmin middleware. Each named route is prefixed with
| "super." so Blade can do route('super.dashboard') etc.
*/

Route::middleware(['setData', 'auth', 'EnsureSuperAdmin', 'language', 'timezone'])
    ->prefix('super')
    ->name('super.')
    ->group(function () {

        // Dashboard
        Route::get('/', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])
            ->name('dashboard');

        // Stores (Phase 4)
        Route::get('/stores', [\App\Http\Controllers\Superadmin\StoreController::class, 'index'])
            ->name('stores.index');
        Route::get('/stores/create', [\App\Http\Controllers\Superadmin\StoreController::class, 'create'])
            ->name('stores.create');
        Route::post('/stores', [\App\Http\Controllers\Superadmin\StoreController::class, 'store'])
            ->name('stores.store');
        Route::get('/stores/{id}/edit', [\App\Http\Controllers\Superadmin\StoreController::class, 'edit'])
            ->name('stores.edit');
        Route::put('/stores/{id}', [\App\Http\Controllers\Superadmin\StoreController::class, 'update'])
            ->name('stores.update');
        Route::post('/stores/{id}/suspend', [\App\Http\Controllers\Superadmin\StoreController::class, 'suspend'])
            ->name('stores.suspend');
        Route::post('/stores/{id}/activate', [\App\Http\Controllers\Superadmin\StoreController::class, 'activate'])
            ->name('stores.activate');
        Route::get('/stores/import', [\App\Http\Controllers\Superadmin\StoreController::class, 'import'])
            ->name('stores.import');
        Route::post('/stores/import', [\App\Http\Controllers\Superadmin\StoreController::class, 'processImport'])
            ->name('stores.processImport');
        Route::get('/stores/export', [\App\Http\Controllers\Superadmin\StoreController::class, 'export'])
            ->name('stores.export');

        // Central products (Phase 2)
        Route::get('/products', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'index'])
            ->name('products.index');
        Route::get('/products/create', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'create'])
            ->name('products.create');
        Route::post('/products', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'store'])
            ->name('products.store');
        Route::get('/products/{id}/edit', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'edit'])
            ->name('products.edit');
        Route::put('/products/{id}', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'update'])
            ->name('products.update');
        Route::delete('/products/{id}', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'destroy'])
            ->name('products.destroy');
        Route::get('/products/import', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'import'])
            ->name('products.import');
        Route::post('/products/import', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'processImport'])
            ->name('products.processImport');
        Route::get('/products/export', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'export'])
            ->name('products.export');

        // Central vendors (Phase 3)
        Route::resource('vendors', \App\Http\Controllers\Superadmin\CentralVendorController::class);

        // Users (Phase 4)
        Route::get('/users', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'create'])
            ->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'store'])
            ->name('users.store');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'edit'])
            ->name('users.edit');
        Route::put('/users/{id}', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'update'])
            ->name('users.update');
        Route::post('/users/{id}/assign-store', [\App\Http\Controllers\Superadmin\SuperadminUserController::class, 'assignStore'])
            ->name('users.assignStore');

        // Reports (Phase 5)
        Route::get('/reports/stock', [\App\Http\Controllers\Superadmin\StockReportController::class, 'index'])
            ->name('reports.stock');
        Route::get('/reports/stock-alerts', [\App\Http\Controllers\Superadmin\StockReportController::class, 'alerts'])
            ->name('reports.stockAlerts');
        Route::get('/reports/stock/export', [\App\Http\Controllers\Superadmin\StockReportController::class, 'export'])
            ->name('reports.stockExport');

        // FEFO JSON API (used by POS)
        Route::get('/api/fefo/{variation}', [\App\Http\Controllers\Superadmin\CentralProductController::class, 'fefo'])
            ->name('api.fefo');

        // Runtime POS settings (Dava India — Phase 6)
        Route::get('/settings/pos', [\App\Http\Controllers\Superadmin\PosSettingsController::class, 'index'])
            ->name('settings.pos');
        Route::post('/settings/pos', [\App\Http\Controllers\Superadmin\PosSettingsController::class, 'save'])
            ->name('settings.pos.save');
    });
