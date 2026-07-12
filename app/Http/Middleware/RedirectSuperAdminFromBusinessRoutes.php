<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Dava India — Phase 1
 *
 * Dava India super admins (is_superadmin = 1) operate the central
 * /super panel. They have no business_id, so any business-scoped
 * route (HomeController, sales, products, reports, ...) would crash
 * with "Attempt to read property on null" or worse, render
 * confusing blank pages.
 *
 * This middleware sends them to the /super dashboard whenever they
 * try to access any non-/super route. It runs only for authenticated
 * super admins and is a no-op for everyone else.
 */
class RedirectSuperAdminFromBusinessRoutes
{
    /**
     * Route prefixes that super admins ARE allowed to use
     * (besides the /super group itself).
     */
    protected $allowedPrefixes = [
        'super',
        'superadmin',        // legacy UltimatePOS superadmin module
        'login',
        'logout',
        'password',
        'register',
        'social',
        '_debugbar',
    ];

    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }
        if (! method_exists($user, 'isSuperadmin') || ! $user->isSuperadmin()) {
            return $next($request);
        }

        // Allow AJAX/API requests to fall through so super admin can
        // still hit /super/api/* endpoints if needed.
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return $next($request);
        }

        $first = $request->segment(1);
        if (in_array($first, $this->allowedPrefixes, true)) {
            return $next($request);
        }

        // Send to /super dashboard.
        return redirect('/super');
    }
}
