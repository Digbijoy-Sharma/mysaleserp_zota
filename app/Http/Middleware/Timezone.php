<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Timezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timezone = config('app.timezone');

        if (session()->has('business.time_zone')) {
            $timezone = $request->session()->get('business.time_zone');
        } elseif (Auth::check() && Auth::user()->business) {
            $timezone = Auth::user()->business->time_zone;
        }
        // If no business is attached (e.g. Dava India Super Admin), keep app default timezone.

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}
