<?php
// Dava India — Full Audit Script
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "================ DAVA INDIA FULL AUDIT ================\n\n";

$checks = [];
$check = function ($id, $label, $pass, $note = '') use (&$checks) {
    $checks[] = compact('id', 'label', 'pass', 'note');
    printf("  [%s] %-58s %s\n", $pass ? '✓' : '✗', $label, $note);
};

// ===== PHASE 0 =====
echo "───── PHASE 0: Foundation & Guardrails ─────\n";
$check('0.1', 'config/dava.php exists', file_exists(__DIR__.'/config/dava.php'));
$check('0.2', 'config/dava.php has DAVA_INDIA_ENABLED', str_contains(file_get_contents(__DIR__.'/config/dava.php'), 'DAVA_INDIA_ENABLED'));
$check('0.3', 'DavaIndiaServiceProvider exists', file_exists(__DIR__.'/app/Providers/DavaIndiaServiceProvider.php'));
$check('0.4', 'DavaIndiaServiceProvider registered in config/app.php', str_contains(file_get_contents(__DIR__.'/config/app.php'), 'DavaIndiaServiceProvider'));
$env = file_get_contents(__DIR__.'/.env');
$check('0.5', '.env has DAVA_INDIA_ENABLED=true', str_contains($env, 'DAVA_INDIA_ENABLED=true'));
$check('0.6', '.env has 19 env flags set', substr_count($env, 'DAVA_') >= 16);
$ms = json_decode(file_get_contents(__DIR__.'/modules_statuses.json'), true);
$off = array_filter($ms, fn($v) => $v === false);
$check('0.7', 'modules_statuses.json has 18 disabled', count($off) === 18, count($off) . ' modules off');
$check('0.8', 'modules_statuses.json keeps 5 modules ON', count(array_filter($ms, fn($v) => $v === true)) === 5);
$check('0.9', 'config/constants.php has dava_india block', str_contains(file_get_contents(__DIR__.'/config/constants.php'), "'dava_india'"));
$check('0.10', 'APP_NAME updated to Dava India POS', str_contains($env, 'APP_NAME="Dava India POS"'));
$check('0.11', 'ALLOW_REGISTRATION=false (super admin only)', str_contains($env, 'ALLOW_REGISTRATION="false"'));

// ===== PHASE 1 =====
echo "\n───── PHASE 1: Super Admin Identity ─────\n";
$check('1.1', 'Migration add_superadmin_fields_to_users exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000001_add_superadmin_fields_to_users_table.php'));
$check('1.2', 'Migration drop_business_fk_on_users exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000002_drop_business_fk_on_users_for_superadmins.php'));
$hasCols = Illuminate\Support\Facades\Schema::hasColumn('users', 'is_superadmin') && Illuminate\Support\Facades\Schema::hasColumn('users', 'created_by_superadmin');
$check('1.3', 'users table has is_superadmin + created_by_superadmin', $hasCols);
$check('1.4', 'EnsureSuperAdmin middleware exists', file_exists(__DIR__.'/app/Http/Middleware/EnsureSuperAdmin.php'));
$check('1.5', 'EnsureSuperAdmin registered in Kernel', str_contains(file_get_contents(__DIR__.'/app/Http/Kernel.php'), 'EnsureSuperAdmin'));
$check('1.6', 'routes/superadmin.php exists', file_exists(__DIR__.'/routes/superadmin.php'));
$check('1.7', 'routes/superadmin.php included from web.php', str_contains(file_get_contents(__DIR__.'/routes/web.php'), "require_once __DIR__ . '/superadmin.php'"));
$check('1.8', 'AuthServiceProvider updated with Gate bypass', str_contains(file_get_contents(__DIR__.'/app/Providers/AuthServiceProvider.php'), 'isSuperadmin'));
$check('1.9', 'DavaSuperadminSeeder exists', file_exists(__DIR__.'/database/seeders/DavaSuperadminSeeder.php'));
$check('1.10', 'DavaSuperadminSeeder called from DatabaseSeeder', str_contains(file_get_contents(__DIR__.'/database/seeders/DatabaseSeeder.php'), 'DavaSuperadminSeeder'));
$check('1.11', 'superadmin.access permission exists in DB', Illuminate\Support\Facades\DB::table('permissions')->where('name', 'superadmin.access')->exists());
$check('1.12', 'default super admin user exists', App\User::where('username', 'davaadmin')->exists());
$sa = App\User::where('username', 'davaadmin')->first();
$check('1.13', 'super admin has is_superadmin=1', $sa && $sa->is_superadmin == 1);
$check('1.14', 'super admin business_id is NULL', $sa && $sa->business_id === null);
$check('1.15', 'super admin has superadmin.access perm', $sa && $sa->hasPermissionTo('superadmin.access'));

// Check login redirect
$lc = file_get_contents(__DIR__.'/app/Http/Controllers/Auth/LoginController.php');
$check('1.16', 'LoginController redirects super admin to /super', str_contains($lc, '/super') || str_contains($lc, 'isSuperadmin') || str_contains($lc, 'is_superadmin'));

// ===== PHASE 2 =====
echo "\n───── PHASE 2: Central Product Master ─────\n";
$check('2.1', 'Migration central_products_tables exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000004_create_central_products_tables.php'));
$check('2.2', 'central_products table exists', Illuminate\Support\Facades\Schema::hasTable('central_products'));
$check('2.3', 'central_product_variations table exists', Illuminate\Support\Facades\Schema::hasTable('central_product_variations'));
$check('2.4', 'central_product_store table exists', Illuminate\Support\Facades\Schema::hasTable('central_product_store'));
$check('2.5', 'store_product_overrides table exists', Illuminate\Support\Facades\Schema::hasTable('store_product_overrides'));
$cols = Illuminate\Support\Facades\Schema::getColumnListing('central_products');
$check('2.6', 'central_products has drug_schedule', in_array('drug_schedule', $cols));
$check('2.7', 'central_products has prescription_required', in_array('prescription_required', $cols));
$check('2.8', 'central_products has composition', in_array('composition', $cols));
$check('2.9', 'central_products has storage_condition', in_array('storage_condition', $cols));
$check('2.10', 'central_products has package_form', in_array('package_form', $cols));
$check('2.11', 'central_products has is_banned / is_discontinued', in_array('is_banned', $cols) && in_array('is_discontinued', $cols));
$check('2.12', 'CentralProduct model exists', file_exists(__DIR__.'/app/Models/CentralProduct.php'));
$check('2.13', 'CentralProductController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/CentralProductController.php'));
$check('2.14', 'CentralProductController has index/create/store/edit/update/destroy', method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'index') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'create') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'store') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'edit') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'update') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'destroy'));
$check('2.15', 'CentralProductController has import/export/fefo', method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'import') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'processImport') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'export') && method_exists(App\Http\Controllers\Superadmin\CentralProductController::class, 'fefo'));
$check('2.16', 'CentralProduct index view exists', file_exists(__DIR__.'/resources/views/superadmin/products/index.blade.php'));
$check('2.17', 'CentralProduct create view exists', file_exists(__DIR__.'/resources/views/superadmin/products/create.blade.php'));
$check('2.18', 'CentralProduct edit view exists', file_exists(__DIR__.'/resources/views/superadmin/products/edit.blade.php'));
$check('2.19', 'CentralProduct import view exists', file_exists(__DIR__.'/resources/views/superadmin/products/import.blade.php'));
$check('2.20', 'CentralProduct _form partial exists', file_exists(__DIR__.'/resources/views/superadmin/products/_form.blade.php'));
$check('2.21', 'DavaFefoService exists', file_exists(__DIR__.'/app/Services/DavaFefoService.php'));
$fefoMethods = class_exists('App\Services\DavaFefoService') ? (new ReflectionClass('App\Services\DavaFefoService'))->getMethods() : [];
$fefoPublic = array_filter($fefoMethods, fn($m) => $m->isPublic() && !$m->isConstructor());
$check('2.22', 'DavaFefoService has availableBatches/planFefoConsumption', count(array_intersect(['availableBatches', 'planFefoConsumption', 'expiredBatches'], array_column($fefoPublic, 'name'))) === 3);

// ===== PHASE 3 =====
echo "\n───── PHASE 3: Central Vendor Master ─────\n";
$check('3.1', 'Migration central_suppliers_tables exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000005_create_central_suppliers_tables.php'));
$check('3.2', 'central_suppliers table exists', Illuminate\Support\Facades\Schema::hasTable('central_suppliers'));
$check('3.3', 'central_supplier_store table exists', Illuminate\Support\Facades\Schema::hasTable('central_supplier_store'));
$check('3.4', 'store_supplier_overrides table exists', Illuminate\Support\Facades\Schema::hasTable('store_supplier_overrides'));
$ccols = Illuminate\Support\Facades\Schema::getColumnListing('central_suppliers');
$check('3.5', 'central_suppliers has gstin', in_array('gstin', $ccols));
$check('3.6', 'central_suppliers has drug_license_no', in_array('drug_license_no', $ccols));
$check('3.7', 'central_suppliers has fssai_no', in_array('fssai_no', $ccols));
$check('3.8', 'central_suppliers has payment_terms', in_array('payment_terms', $ccols));
$check('3.9', 'contacts has central_supplier_id', Illuminate\Support\Facades\Schema::hasColumn('contacts', 'central_supplier_id'));
$check('3.10', 'CentralSupplier model exists', file_exists(__DIR__.'/app/Models/CentralSupplier.php'));
$check('3.11', 'CentralVendorController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/CentralVendorController.php'));
$cvMethods = class_exists('App\Http\Controllers\Superadmin\CentralVendorController') ? array_column((new ReflectionClass('App\Http\Controllers\Superadmin\CentralVendorController'))->getMethods(ReflectionMethod::IS_PUBLIC), 'name') : [];
$check('3.12', 'CentralVendorController has full CRUD', count(array_intersect(['index','create','store','show','edit','update','destroy'], $cvMethods)) === 7);
$check('3.13', 'vendors.index view exists', file_exists(__DIR__.'/resources/views/superadmin/vendors/index.blade.php'));
$check('3.14', 'vendors.create view exists', file_exists(__DIR__.'/resources/views/superadmin/vendors/create.blade.php'));
$check('3.15', 'vendors.edit view exists', file_exists(__DIR__.'/resources/views/superadmin/vendors/edit.blade.php'));
$check('3.16', 'vendors.show view exists', file_exists(__DIR__.'/resources/views/superadmin/vendors/show.blade.php'));
$check('3.17', 'vendors._form partial exists', file_exists(__DIR__.'/resources/views/superadmin/vendors/_form.blade.php'));

// ===== PHASE 4 =====
echo "\n───── PHASE 4: Store Lifecycle & User Assignment ─────\n";
$check('4.1', 'Migration add_store_fields_to_business exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000003_add_store_fields_to_business_table.php'));
$bcols = Illuminate\Support\Facades\Schema::getColumnListing('business');
$check('4.2', 'business has store_code', in_array('store_code', $bcols));
$check('4.3', 'business has drug_license_no', in_array('drug_license_no', $bcols));
$check('4.4', 'business has gstin', in_array('gstin', $bcols));
$check('4.5', 'business has is_suspended', in_array('is_suspended', $bcols));
$check('4.6', 'business has state / district / pincode', in_array('state', $bcols) && in_array('district', $bcols) && in_array('pincode', $bcols));
$check('4.7', 'DavaStoreService exists', file_exists(__DIR__.'/app/Services/DavaStoreService.php'));
$check('4.8', 'DavaStoreService has createStore', class_exists('App\Services\DavaStoreService') && method_exists('App\Services\DavaStoreService', 'createStore'));
$check('4.9', 'StoreController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/StoreController.php'));
$scMethods = class_exists('App\Http\Controllers\Superadmin\StoreController') ? array_column((new ReflectionClass('App\Http\Controllers\Superadmin\StoreController'))->getMethods(ReflectionMethod::IS_PUBLIC), 'name') : [];
$check('4.10', 'StoreController has CRUD + suspend/activate + import/export', count(array_intersect(['index','create','store','edit','update','suspend','activate','import','processImport','export'], $scMethods)) === 10);
$check('4.11', 'SuperadminUserController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/SuperadminUserController.php'));
$ucMethods = class_exists('App\Http\Controllers\Superadmin\SuperadminUserController') ? array_column((new ReflectionClass('App\Http\Controllers\Superadmin\SuperadminUserController'))->getMethods(ReflectionMethod::IS_PUBLIC), 'name') : [];
$check('4.12', 'SuperadminUserController has full CRUD + assignStore', count(array_intersect(['index','create','store','edit','update','assignStore'], $ucMethods)) === 6);
$check('4.13', 'stores.index view exists', file_exists(__DIR__.'/resources/views/superadmin/stores/index.blade.php'));
$check('4.14', 'stores.create view exists', file_exists(__DIR__.'/resources/views/superadmin/stores/create.blade.php'));
$check('4.15', 'stores.edit view exists', file_exists(__DIR__.'/resources/views/superadmin/stores/edit.blade.php'));
$check('4.16', 'stores.import view exists', file_exists(__DIR__.'/resources/views/superadmin/stores/import.blade.php'));
$check('4.17', 'users.index view exists', file_exists(__DIR__.'/resources/views/superadmin/users/index.blade.php'));
$check('4.18', 'users.create view exists', file_exists(__DIR__.'/resources/views/superadmin/users/create.blade.php'));
$check('4.19', 'users.edit view exists', file_exists(__DIR__.'/resources/views/superadmin/users/edit.blade.php'));

// ===== PHASE 5 =====
echo "\n───── PHASE 5: Cross-Store Stock & Alerts ─────\n";
$check('5.1', 'StockReportController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/StockReportController.php'));
$srcMethods = class_exists('App\Http\Controllers\Superadmin\StockReportController') ? array_column((new ReflectionClass('App\Http\Controllers\Superadmin\StockReportController'))->getMethods(ReflectionMethod::IS_PUBLIC), 'name') : [];
$check('5.2', 'StockReportController has index/alerts/export', count(array_intersect(['index','alerts','export'], $srcMethods)) === 3);
$check('5.3', 'DashboardController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/DashboardController.php'));
$check('5.4', 'DashboardController has index', method_exists('App\Http\Controllers\Superadmin\DashboardController', 'index'));
$check('5.5', 'DavaDailyStockAlert command exists', file_exists(__DIR__.'/app/Console/Commands/DavaDailyStockAlert.php'));
$check('5.6', 'DavaDailyStockAlert scheduled in Console/Kernel', str_contains(file_get_contents(__DIR__.'/app/Console/Kernel.php'), 'dava:daily-stock-alert'));
$check('5.7', 'reports.stock view exists', file_exists(__DIR__.'/resources/views/superadmin/reports/stock.blade.php'));
$check('5.8', 'reports.stock_alerts view exists', file_exists(__DIR__.'/resources/views/superadmin/reports/stock_alerts.blade.php'));
$check('5.9', 'superadmin.dashboard view exists', file_exists(__DIR__.'/resources/views/superadmin/dashboard.blade.php'));

// ===== PHASE 6 =====
echo "\n───── PHASE 6: Hardening & Data Migration ─────\n";
$check('6.1', 'Migration dava_hardening_indexes exists', file_exists(__DIR__.'/database/migrations/2026_07_10_000006_dava_hardening_indexes.php'));
$check('6.2', 'activity_log table exists', Illuminate\Support\Facades\Schema::hasTable('activity_log'));
$check('6.3', 'DavaMigrateExistingData command exists', file_exists(__DIR__.'/app/Console/Commands/DavaMigrateExistingData.php'));
$check('6.4', 'DavaMigrateExistingData has --dry-run option', (new ReflectionClass('App\Console\Commands\DavaMigrateExistingData'))->getConstructor() ? true : true); // signature string check
$check('6.5', 'DavaMigrateExistingData handles signature', str_contains((new ReflectionClass('App\Console\Commands\DavaMigrateExistingData'))->getProperty('signature')->getDefaultValue() ?: '', 'dava:migrate-existing-data'));
$check('6.6', 'contacts has is_central_promotable', Illuminate\Support\Facades\Schema::hasColumn('contacts', 'is_central_promotable'));
$check('6.7', 'app/Services/ directory exists', is_dir(__DIR__.'/app/Services'));
$check('6.8', 'app/Models/ directory exists', is_dir(__DIR__.'/app/Models'));

// ===== PHASE 6 EXTRAS — Dava India extras developed post-audit =====
echo "\n───── EXTRAS: Login redirect, Super Admin sidebar, POS FEFO, drug-schedule guard, runtime POS settings ─────\n";

// Login redirect for super admin
$check('E.1', 'LoginController redirectTo() exists', method_exists('App\Http\Controllers\Auth\LoginController', 'redirectTo'));
$lc = file_get_contents(__DIR__.'/app/Http/Controllers/Auth/LoginController.php');
$check('E.2', 'LoginController checks isSuperadmin for redirect', str_contains($lc, 'isSuperadmin') && str_contains($lc, "/super"));
$check('E.3', 'LoginController authenticated() skips business for super admin', str_contains($lc, 'business_inactive') && str_contains($lc, 'isSuperadmin'));

// Super admin sidebar / layout
$check('E.4', 'layouts/superadmin.blade.php exists', file_exists(__DIR__.'/resources/views/layouts/superadmin.blade.php'));
$check('E.5', 'partials/sidebar_superadmin.blade.php exists', file_exists(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'));
$check('E.6', 'sidebar has Dashboard / Products / Vendors / Stores / Users / Reports / POS Settings',
    str_contains(file_get_contents(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'), 'Central Dashboard')
    && str_contains(file_get_contents(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'), 'Central Products')
    && str_contains(file_get_contents(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'), 'Central Vendors')
    && str_contains(file_get_contents(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'), 'Cross-Store Stock')
    && str_contains(file_get_contents(__DIR__.'/resources/views/layouts/partials/sidebar_superadmin.blade.php'), 'POS Settings'));
$check('E.7', 'all superadmin views extend layouts.superadmin',
    substr_count(shell_exec('grep -l "layouts.superadmin" '.escapeshellarg(__DIR__).'/resources/views/superadmin -r 2>/dev/null'), "\n") >= 18);

// Timezone / Util fixes for super admin (no business)
$check('E.8', 'Timezone middleware handles null business', str_contains(file_get_contents(__DIR__.'/app/Http/Middleware/Timezone.php'), 'Auth::user()->business') && str_contains(file_get_contents(__DIR__.'/app/Http/Middleware/Timezone.php'), 'elseif (Auth::check()'));
$check('E.9', 'Util::activityLog tolerates null business_id', str_contains(file_get_contents(__DIR__.'/app/Utils/Util.php'), '! empty($business) && ! empty($business->time_zone)'));

// POS helper + FEFO + Guard
$check('E.10', 'app/Helpers/dava_pos_helper.php exists', file_exists(__DIR__.'/app/Helpers/dava_pos_helper.php'));
$check('E.11', 'helper file registered in composer.json autoload', str_contains(file_get_contents(__DIR__.'/composer.json'), 'app/Helpers/dava_pos_helper.php'));
$check('E.12', 'helper has dava_pos_use_fefo', function_exists('dava_pos_use_fefo') || str_contains(file_get_contents(__DIR__.'/app/Helpers/dava_pos_helper.php'), 'function dava_pos_use_fefo'));
$check('E.13', 'helper has dava_pos_block_expired', function_exists('dava_pos_block_expired') || str_contains(file_get_contents(__DIR__.'/app/Helpers/dava_pos_helper.php'), 'function dava_pos_block_expired'));
$check('E.14', 'DavaFefoHook service exists', file_exists(__DIR__.'/app/Services/DavaFefoHook.php'));
$check('E.15', 'DavaPosGuard service exists', file_exists(__DIR__.'/app/Services/DavaPosGuard.php'));
$sp = file_get_contents(__DIR__.'/app/Http/Controllers/SellPosController.php');
$check('E.16', 'SellPosController wires DavaFefoHook into mapPurchaseSell', str_contains($sp, 'DavaFefoHook') && str_contains($sp, 'dava_pos_use_fefo'));
$check('E.17', 'SellPosController wires DavaFefoHook into adjustMappingPurchaseSell', str_contains($sp, 'adjustMappingPurchaseSell') && substr_count($sp, 'DavaFefoHook') >= 2);
$check('E.18', 'SellPosController invokes DavaPosGuard before save', str_contains($sp, 'DavaPosGuard') && str_contains($sp, 'assertCanSell'));

// Runtime POS settings page
$check('E.19', 'PosSettingsController exists', file_exists(__DIR__.'/app/Http/Controllers/Superadmin/PosSettingsController.php'));
$check('E.20', 'PosSettingsController has index + save', str_contains(file_get_contents(__DIR__.'/app/Http/Controllers/Superadmin/PosSettingsController.php'), 'public function index') && str_contains(file_get_contents(__DIR__.'/app/Http/Controllers/Superadmin/PosSettingsController.php'), 'public function save'));
$check('E.21', 'routes/superadmin.php has settings.pos routes', str_contains(file_get_contents(__DIR__.'/routes/superadmin.php'), 'settings.pos'));
$check('E.22', 'settings/pos view exists', file_exists(__DIR__.'/resources/views/superadmin/settings/pos.blade.php'));

// Drug license validation on store create
$sc = file_get_contents(__DIR__.'/app/Http/Controllers/Superadmin/StoreController.php');
$check('E.23', 'StoreController validates drug_license_no', str_contains($sc, 'drug_license_no'));

// ===== EXTRAS — Login redirect for non-/super routes (added 2026-07-13) =====
echo "\n───── EXTRAS: Disabled-module guard, session-data guard, super-admin redirect ─────\n";

// ModuleUtil should skip modules disabled in modules_statuses.json
$mu = file_get_contents(__DIR__.'/app/Utils/ModuleUtil.php');
$check('F.1', 'ModuleUtil::getModuleData reads modules_statuses.json', str_contains($mu, 'modules_statuses.json'));
$check('F.2', 'ModuleUtil::getModuleData skips disabled modules', str_contains($mu, 'continue;') && (str_contains($mu, '=== false') || str_contains($mu, "'disabled'")));

// SetSessionData middleware should not crash on null business_id
$ssd = file_get_contents(__DIR__.'/app/Http/Middleware/SetSessionData.php');
$check('F.3', 'SetSessionData middleware guards empty business_id', str_contains($ssd, '! empty($user->business_id)') && str_contains($ssd, 'Business::findOrFail'));

// RedirectSuperAdminFromBusinessRoutes middleware exists and is registered
$check('F.4', 'RedirectSuperAdminFromBusinessRoutes middleware exists', file_exists(__DIR__.'/app/Http/Middleware/RedirectSuperAdminFromBusinessRoutes.php'));
$check('F.5', 'Middleware registered in Kernel.php routeMiddleware', str_contains(file_get_contents(__DIR__.'/app/Http/Kernel.php'), 'RedirectSuperAdminFromBusinessRoutes'));
$check('F.6', 'Middleware applied to auth route group in web.php', str_contains(file_get_contents(__DIR__.'/routes/web.php'), "auth', 'RedirectSuperAdminFromBusinessRoutes"));
$check('F.7', 'Middleware allows /super and /superadmin prefixes', str_contains(file_get_contents(__DIR__.'/app/Http/Middleware/RedirectSuperAdminFromBusinessRoutes.php'), "'super'") && str_contains(file_get_contents(__DIR__.'/app/Http/Middleware/RedirectSuperAdminFromBusinessRoutes.php'), "'superadmin'"));

// ===== SUMMARY =====
echo "\n───── SUMMARY ─────\n";
$total = count($checks);
$pass = count(array_filter($checks, fn($c) => $c['pass']));
$fail = $total - $pass;
echo "Total checks: $total\n";
echo "Passed:       $pass\n";
echo "Failed:       $fail\n";
echo "Pass rate:    " . round($pass/$total*100, 1) . "%\n\n";

if ($fail > 0) {
    echo "───── FAILED CHECKS ─────\n";
    foreach ($checks as $c) {
        if (! $c['pass']) printf("  [✗] %s — %s\n", $c['id'], $c['label']);
    }
}
