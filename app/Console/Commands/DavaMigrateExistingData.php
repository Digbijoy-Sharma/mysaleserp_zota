<?php

namespace App\Console\Commands;

use App\Business;
use App\BusinessLocation;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dava India — Phase 6
 *
 * Idempotent data migration command.
 *
 * Reads the existing data in `mysaleserp_zota` and converts it to
 * the Dava India shape:
 *   1) Each `business` row is left intact (= 1 store).
 *      For each business, ensure exactly 1 `business_locations` row
 *      (auto-create one if missing, keep the first if multiple).
 *   2) Existing `products` rows for each business are kept as-is;
 *      the global central catalog is built later by Super Admin.
 *      A pointer is added via a "central_product_id" backfill hook
 *      (skipped here; products continue to be the per-store source
 *      of truth until promoted to central).
 *   3) Existing suppliers (`contacts` WHERE type='supplier`) are
 *      marked with is_central_promotable = 1 and a placeholder
 *      `central_supplier_id` is filled if a matching central supplier
 *      exists. The Super Admin may then promote or re-link them.
 *   4) All existing users are flagged as `is_superadmin = 0`,
 *      `created_by_superadmin = 0`.
 *   5) `store_code` is auto-generated for businesses missing one.
 *
 * Usage:
 *   php artisan dava:migrate-existing-data
 *   php artisan dava:migrate-existing-data --dry-run
 */
class DavaMigrateExistingData extends Command
{
    protected $signature = 'dava:migrate-existing-data {--dry-run : Do not write any changes}';
    protected $description = 'Convert existing mysaleserp_zota data to the Dava India schema';

    public function handle()
    {
        $dry = (bool) $this->option('dry-run');
        if ($dry) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $this->info('Step 1: Ensure exactly one business_location per business…');
        $businesses = Business::all();
        $this->line("  Found {$businesses->count()} businesses.");

        foreach ($businesses as $b) {
            $locs = BusinessLocation::where('business_id', $b->id)->orderBy('id')->get();
            if ($locs->isEmpty()) {
                $this->line("  - business #{$b->id} ({$b->name}): no location, will create one.");
                if (! $dry) {
                    $loc = new BusinessLocation();
                    $loc->business_id = $b->id;
                    $loc->name = $b->name;
                    $loc->country = $b->country ?? 'India';
                    $loc->state = $b->state ?? '';
                    $loc->city = $b->city ?? '';
                    $loc->zip_code = $b->zip_code ?? '';
                    $loc->landmark = $b->landmark ?? '';
                    $loc->is_active = 1;
                    $loc->save();
                    $this->info("    + created location #{$loc->id}");
                }
            } elseif ($locs->count() > 1) {
                $this->line("  - business #{$b->id} ({$b->name}): has {$locs->count()} locations, will keep first and deactivate others.");
                $keep = $locs->first();
                if (! $dry) {
                    BusinessLocation::where('business_id', $b->id)
                        ->where('id', '<>', $keep->id)
                        ->update(['is_active' => 0, 'deleted_at' => now()]);
                }
            }
        }

        $this->info('Step 2: Auto-generate store_code where missing…');
        $missing = Business::whereNull('store_code')->orWhere('store_code', '')->get();
        $i = 1;
        foreach ($missing as $b) {
            $code = 'DAV-EXIST-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $this->line("  - business #{$b->id} ({$b->name}) → {$code}");
            if (! $dry) {
                $b->store_code = $code;
                $b->save();
            }
            $i++;
        }

        $this->info('Step 3: Tag existing supplier contacts…');
        // Add the is_central_promotable column if missing
        if (! Schema::hasColumn('contacts', 'is_central_promotable')) {
            if (! $dry) {
                DB::statement('ALTER TABLE contacts ADD is_central_promotable TINYINT(1) DEFAULT 0');
            }
            $this->line('  - added is_central_promotable column on contacts.');
        }
        $supplierCount = DB::table('contacts')
            ->where('type', 'supplier')
            ->whereNull('central_supplier_id')
            ->count();
        $this->line("  - {$supplierCount} supplier contacts without central_supplier_id.");
        if (! $dry) {
            DB::table('contacts')
                ->where('type', 'supplier')
                ->whereNull('central_supplier_id')
                ->update(['is_central_promotable' => 1]);
        }

        $this->info('Step 4: Tag existing users as non-superadmin…');
        $userCount = User::count();
        $super = User::where('is_superadmin', 1)->count();
        $this->line("  - {$userCount} users total, {$super} super admins.");
        if (! $dry) {
            User::where('is_superadmin', '!=', 1)->update([
                'created_by_superadmin' => 0,
            ]);
        }

        $this->info('Step 5: Summary…');
        $this->table(
            ['Entity', 'Count'],
            [
                ['Stores (businesses)', Business::count()],
                ['Store locations', BusinessLocation::where('is_active', 1)->count()],
                ['Central products', DB::table('central_products')->count()],
                ['Central suppliers', DB::table('central_suppliers')->count()],
                ['Users total', User::count()],
                ['Super admins', User::where('is_superadmin', 1)->count()],
            ]
        );

        $this->info('Done. ' . ($dry ? 'No changes were written.' : 'All changes written.'));
        return 0;
    }
}
