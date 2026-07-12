<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Dava India — Phase 1
 *
 * Gates the `/super/*` route group to authenticated super admins only.
 * A super admin is defined as a user with `is_superadmin = 1` on the
 * `users` table.
 */
class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Backward-compat: the legacy hard-coded admin list still works.
        $administrator_list = config('constants.administrator_usernames');
        $isLegacyAdmin = ! empty($administrator_list)
            && in_array(strtolower($user->username), explode(',', strtolower($administrator_list)));

        if (! $user->isSuperadmin() && ! $isLegacyAdmin) {
            abort(403, 'Unauthorized action. Super admin access required.');
        }

        return $next($request);
    }
}
