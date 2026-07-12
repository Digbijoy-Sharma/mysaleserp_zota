<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

/**
 * DavaIndiaServiceProvider
 *
 * Central service provider for the Dava India pharmacy chain deployment.
 * Loaded conditionally by config('dava.enabled') — all Dava-specific
 * bootstrapping, view composers, observers, and module disables live here.
 */
class DavaIndiaServiceProvider extends ServiceProvider
{
    /**
     * Register Dava India services.
     */
    public function register()
    {
        // No bindings yet — placeholder for future Dava-specific services.
    }

    /**
     * Bootstrap Dava India behaviour.
     */
    public function boot()
    {
        // Guard: if Dava mode is disabled, do nothing. This keeps the
        // original UltimatePOS multi-tenant behaviour intact.
        if (! config('dava.enabled')) {
            return;
        }

        // 1) Enforce 1-location-per-business when multi_location is disabled.
        //    The "BusinessLocationService" we add in Phase 2 will be invoked
        //    from here once it's available. For Phase 0 we just publish the
        //    config flag.
        if (! config('dava.multi_location.enabled')) {
            // Observers are attached in BusinessObserver (Phase 2).
        }

        // 2) Share a few globals with every view for easier branching in
        //    Blade (e.g. @if($__dava_india_enabled) ... @endif).
        View::composer('*', function ($view) {
            $view->with('__dava_india_enabled', (bool) config('dava.enabled'));
            $view->with('__dava_superadmin_enabled', function () {
                return Auth::check() && Auth::user()->is_superadmin == 1;
            });
        });
    }
}
