<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Dava India — Phase 1
 *
 * Idempotently ensure a default super admin account exists.
 * Credentials are read from config('dava.default_superadmin') which
 * pulls from the .env file. Safe to run on every deploy.
 */
class DavaSuperadminSeeder extends Seeder
{
    public function run()
    {
        if (! config('dava.enabled')) {
            return;
        }

        $cfg = config('dava.default_superadmin');
        $username = $cfg['username'] ?? 'davaadmin';

        $user = User::where('username', $username)->first();
        if (! $user) {
            $user = User::create([
                'surname' => '',
                'first_name' => $cfg['first_name'] ?? 'Dava',
                'last_name' => $cfg['last_name'] ?? 'Super Admin',
                'username' => $username,
                'email' => $cfg['email'] ?? null,
                'password' => Hash::make($cfg['password'] ?? 'DavaSuperAdmin2026!'),
                'language' => 'en',
                'is_superadmin' => 1,
                'created_by_superadmin' => 0,
                'business_id' => null,
                'allow_login' => 1,
            ]);
            $this->command->info("DavaSuperadminSeeder: created super admin user '{$username}'.");
        } else {
            $user->is_superadmin = 1;
            $user->save();
            $this->command->info("DavaSuperadminSeeder: promoted existing user '{$username}' to super admin.");
        }

        // Ensure the Spatie permission exists and is assigned to the user.
        $permName = User::superadminPermissionName();
        $perm = Permission::firstOrCreate(
            ['name' => $permName, 'guard_name' => 'web']
        );
        $user->givePermissionTo($perm);
    }
}
