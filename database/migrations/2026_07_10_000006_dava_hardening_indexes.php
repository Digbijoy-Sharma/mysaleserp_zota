<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 6
     *
     * Hardening: indexes, performance, and the activity log table.
     */
    public function up()
    {
        // 1) activity_log — used by Spatie/laravel-activitylog for super admin audits.
        if (! Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject');
                $table->nullableMorphs('causer');
                $table->json('properties')->nullable();
                $table->string('event')->nullable()->index();
                $table->timestamps();
                $table->index('log_name');
            });
        }

        // 2) Indexes on the high-traffic hot paths
        //    Note: variation_location_details does NOT have a business_id column —
        //    the business scope is reached through product_id or location_id.
        if (Schema::hasTable('variation_location_details')) {
            Schema::table('variation_location_details', function (Blueprint $table) {
                if (! $this->indexExists('variation_location_details', 'vld_product_variation_loc')) {
                    $table->index(['product_id', 'variation_id', 'location_id'], 'vld_product_variation_loc');
                }
            });
        }
        if (Schema::hasTable('business')) {
            Schema::table('business', function (Blueprint $table) {
                if (! $this->indexExists('business', 'business_state_idx')) {
                    $table->index('state', 'business_state_idx');
                }
                if (! $this->indexExists('business', 'business_district_idx')) {
                    $table->index('district', 'business_district_idx');
                }
            });
        }
    }

    public function down()
    {
        // No down: indexes are additive
    }

    protected function indexExists(string $table, string $index): bool
    {
        try {
            $conn = Schema::getConnection();
            $db = $conn->getDatabaseName();
            $rows = $conn->select(
                "SELECT 1 FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1",
                [$db, $table, $index]
            );
            return ! empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
