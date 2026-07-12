<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 2 + 4
     * Add store-level fields to business table.
     *  - store_code: short unique code (e.g. DAV-BLR-001)
     *  - drug_license_no: pharmacy drug license number
     *  - gstin: store GSTIN
     *  - state, district, pincode: pharmacy compliance (lifted from business_locations)
     *  - is_suspended: super admin can suspend a store
     *  - suspended_at, suspended_reason
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->string('store_code', 30)->nullable()->unique()->after('name');
            $table->string('drug_license_no', 60)->nullable()->after('tax_number_2');
            $table->string('gstin', 20)->nullable()->after('drug_license_no');
            $table->string('state', 80)->nullable()->after('gstin');
            $table->string('district', 80)->nullable()->after('state');
            $table->string('pincode', 12)->nullable()->after('district');
            $table->boolean('is_suspended')->default(0)->after('owner_id');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->string('suspended_reason', 255)->nullable()->after('suspended_at');
        });
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn([
                'store_code', 'drug_license_no', 'gstin',
                'state', 'district', 'pincode',
                'is_suspended', 'suspended_at', 'suspended_reason',
            ]);
        });
    }
};
