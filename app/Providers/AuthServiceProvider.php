<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Ensure the Spatie permission for Dava India super admin exists.
        // Idempotent — safe to run on every boot.
        if (config('dava.enabled', false)) {
            $this->ensureSuperadminPermissionExists();
        }

        Gate::before(function ($user, $ability) {
            if (in_array($ability, ['backup', 'superadmin',
                'manage_modules', ])) {
                $administrator_list = config('constants.administrator_usernames');

                if (in_array(strtolower($user->username), explode(',', strtolower($administrator_list)))) {
                    return true;
                }
            } else {
                if ($user->hasRole('Admin#'.$user->business_id)) {
                    return true;
                }
            }

            // Dava India — explicit super admin bypass: any user with
            // is_superadmin = 1 has access to *every* gate ability.
            if (config('dava.enabled', false) && method_exists($user, 'isSuperadmin') && $user->isSuperadmin()) {
                return true;
            }
        });
    }

    /**
     * Idempotently create the `superadmin.access` Spatie permission.
     */
    protected function ensureSuperadminPermissionExists(): void
    {
        try {
            $permName = \App\User::superadminPermissionName();
            $perm = Permission::where('name', $permName)->first();
            if (! $perm) {
                Permission::create([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
            }
        } catch (\Throwable $e) {
            // Tables may not be migrated yet on first boot — silently skip.
        }
    }
}
