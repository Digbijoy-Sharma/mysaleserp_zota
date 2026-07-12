<?php

namespace App\Services;

use App\Business;
use App\BusinessLocation;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Dava India — Phase 4
 *
 * Encapsulates the lifecycle of a Dava India store: a single
 * `business` row plus its single auto-managed `business_locations`
 * row, the default Admin role, an invitee admin user, and the
 * standard set of invoice schemes / payment accounts.
 */
class DavaStoreService
{
    public function __construct(protected BusinessUtil $businessUtil)
    {
    }

    /**
     * Create a new store. Returns ['business' => Business, 'location' => BusinessLocation, 'admin' => User].
     */
    public function createStore(array $data): array
    {
        DB::beginTransaction();
        try {
            // 1) Admin user (the store owner)
            $admin = $this->createStoreAdmin($data);
            $userId = $admin->id;

            // 2) business row
            $businessData = [
                'name' => $data['name'],
                'currency_id' => $data['currency_id'] ?? 134, // INR
                'country' => $data['country'] ?? 'India',
                'state' => $data['state'] ?? '',
                'city' => $data['city'] ?? '',
                'zip_code' => $data['pincode'] ?? '',
                'landmark' => $data['landmark'] ?? '',
                'time_zone' => $data['time_zone'] ?? 'Asia/Kolkata',
                'fy_start_month' => 4, // April — Indian financial year
                'accounting_method' => 'fifo',
                'start_date' => now()->toDateString(),
                'tax_label_1' => 'GSTIN',
                'tax_number_1' => $data['gstin'] ?? null,
                'tax_label_2' => 'Drug Lic.',
                'tax_number_2' => $data['drug_license_no'] ?? null,
                'owner_id' => $userId,
                'enabled_modules' => config('dava.enabled_modules', ['Superadmin','Essentials','Accounting']),
                'store_code' => $data['store_code'] ?? null,
                'gstin' => $data['gstin'] ?? null,
                'drug_license_no' => $data['drug_license_no'] ?? null,
                'state' => $data['state'] ?? null,
                'district' => $data['district'] ?? null,
                'pincode' => $data['pincode'] ?? null,
            ];
            $business = $this->businessUtil->createNewBusiness($businessData);
            $admin->business_id = $business->id;
            $admin->save();

            // 3) one auto-managed business location
            $this->businessUtil->newBusinessDefaultResources($business->id, $userId);
            $location = $this->businessUtil->addLocation($business->id, [
                'name' => $data['name'],
                'country' => $data['country'] ?? 'India',
                'state' => $data['state'] ?? '',
                'city' => $data['city'] ?? '',
                'zip_code' => $data['pincode'] ?? '',
                'landmark' => $data['landmark'] ?? '',
                'mobile' => $data['mobile'] ?? '',
                'alternate_number' => $data['alternate_number'] ?? '',
                'website' => $data['website'] ?? '',
            ]);

            // 4) location permission + ensure Admin role exists
            Permission::firstOrCreate(['name' => 'location.' . $location->id, 'guard_name' => 'web']);
            $adminRoleName = 'Admin#' . $business->id;
            if (! Role::where('name', $adminRoleName)->exists()) {
                $adminRole = Role::create(['name' => $adminRoleName, 'guard_name' => 'web']);
                $adminRole->syncPermissions(Permission::all());
            }
            $admin->assignRole($adminRoleName);

            DB::commit();

            return [
                'business' => $business,
                'location' => $location,
                'admin' => $admin,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createStoreAdmin(array $data): User
    {
        $username = $data['admin_username']
            ?? preg_replace('/[^a-z0-9]+/i', '', strtolower(explode(' ', $data['name'])[0])) . '_admin';

        $existing = User::where('username', $username)->first();
        if ($existing) {
            $username .= '_' . substr(md5(uniqid('', true)), 0, 4);
        }

        return User::create([
            'surname' => '',
            'first_name' => $data['admin_first_name'] ?? 'Store',
            'last_name' => $data['admin_last_name'] ?? 'Admin',
            'username' => $username,
            'email' => $data['admin_email'] ?? null,
            'password' => Hash::make($data['admin_password'] ?? 'Dava@Store#2026'),
            'language' => 'en',
            'is_superadmin' => 0,
            'created_by_superadmin' => 1,
            'business_id' => null, // set after business is created
            'allow_login' => 1,
        ]);
    }
}
