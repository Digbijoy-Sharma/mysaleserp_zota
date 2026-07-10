<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 3
     *
     * central_suppliers = global supplier master managed by Super Admin.
     *   One central vendor can supply many stores.
     *
     * central_supplier_store = mapping (which stores a vendor is active for).
     *
     * store_supplier_overrides = per-store tweaks (payment terms, credit limit).
     */
    public function up()
    {
        Schema::create('central_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->nullable()->unique();
            $table->string('name', 191);
            $table->string('gstin', 20)->nullable()->index();
            $table->string('drug_license_no', 60)->nullable();
            $table->string('fssai_no', 30)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('address_line1', 191)->nullable();
            $table->string('address_line2', 191)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('pincode', 12)->nullable();
            $table->string('country', 60)->default('India');
            $table->text('payment_terms')->nullable();
            $table->unsignedSmallInteger('lead_time_days')->default(7);
            $table->decimal('credit_limit', 22, 4)->default(0);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 30)->nullable();
            $table->string('bank_ifsc', 15)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('is_active');
        });

        Schema::create('central_supplier_store', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('central_supplier_id');
            $table->foreign('central_supplier_id')->references('id')->on('central_suppliers')->onDelete('cascade');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->unique(['central_supplier_id', 'business_id']);
        });

        Schema::create('store_supplier_overrides', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('central_supplier_id');
            $table->foreign('central_supplier_id')->references('id')->on('central_suppliers')->onDelete('cascade');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->text('payment_terms')->nullable();
            $table->unsignedSmallInteger('lead_time_days')->nullable();
            $table->decimal('credit_limit', 22, 4)->nullable();
            $table->decimal('local_price_multiplier', 5, 2)->nullable();
            $table->boolean('is_preferred')->default(0);
            $table->timestamps();
            $table->unique(['central_supplier_id', 'business_id']);
        });

        // Backfill hook: add a nullable FK on contacts (existing) so we can
        // trace a per-store supplier back to the central master.
        if (! Schema::hasColumn('contacts', 'central_supplier_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->unsignedInteger('central_supplier_id')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'central_supplier_id')) {
                $table->dropColumn('central_supplier_id');
            }
        });
        Schema::dropIfExists('store_supplier_overrides');
        Schema::dropIfExists('central_supplier_store');
        Schema::dropIfExists('central_suppliers');
    }
};
