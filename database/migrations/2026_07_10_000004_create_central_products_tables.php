<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 2
     *
     * central_products = the global product master managed by Super Admin.
     *   Every store can transact against a central product; per-store
     *   stock lives in variation_location_details (unchanged).
     *
     * central_product_variations = variation rows (e.g. 10-strip, 30-strip)
     *   belonging to a central product.
     *
     * central_product_store = which central products are available in
     *   which stores. Default-true for new central products when
     *   dava.central_catalog.auto_assign_all_stores is true.
     */
    public function up()
    {
        Schema::create('central_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('sku', 191)->unique();
            $table->string('barcode', 100)->nullable()->index();
            $table->string('barcode_type', 20)->default('EAN-13');
            $table->enum('type', ['single', 'variable'])->default('single');

            // Categorisation
            $table->string('category', 100)->nullable();
            $table->string('sub_category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('unit', 30)->nullable();

            // Pricing & tax defaults
            $table->decimal('default_mrp', 22, 4)->default(0);
            $table->decimal('default_purchase_price', 22, 4)->default(0);
            $table->decimal('default_sell_price', 22, 4)->default(0);
            $table->decimal('default_sell_price_inc_tax', 22, 4)->default(0);
            $table->decimal('default_gst_percent', 5, 2)->default(0);
            $table->string('hsn_code', 20)->nullable();
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');

            // Stock defaults
            $table->boolean('enable_stock')->default(1);
            $table->decimal('default_alert_quantity', 22, 4)->default(0);
            $table->decimal('reorder_level', 22, 4)->nullable();

            // Pharmacy-specific fields
            $table->text('composition')->nullable();
            $table->enum('drug_schedule', ['none', 'H', 'H1', 'X', 'OTC'])->default('OTC');
            $table->boolean('prescription_required')->default(0);
            $table->string('manufacturer', 191)->nullable();
            $table->string('marketed_by', 191)->nullable();
            $table->enum('storage_condition', ['ambient', 'cold', 'dry', 'frozen'])->default('ambient');
            $table->enum('package_form', ['strip', 'bottle', 'injection', 'tube', 'drops', 'sachet', 'other'])->default('strip');
            $table->unsignedInteger('units_per_pack')->default(1);
            $table->boolean('is_banned')->default(0);
            $table->boolean('is_discontinued')->default(0);

            // Image
            $table->string('image', 191)->nullable();
            $table->text('description')->nullable();

            // Audit
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('is_active');
            $table->index('drug_schedule');
        });

        Schema::create('central_product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('central_product_id');
            $table->foreign('central_product_id')->references('id')->on('central_products')->onDelete('cascade');
            $table->string('name', 100)->nullable();              // e.g. "10 Tablets"
            $table->string('sub_sku', 191)->nullable()->unique();
            $table->string('barcode', 100)->nullable()->index();
            $table->decimal('default_mrp', 22, 4)->default(0);
            $table->decimal('default_sell_price', 22, 4)->default(0);
            $table->decimal('default_purchase_price', 22, 4)->default(0);
            $table->unsignedInteger('variation_value_id')->nullable();   // optional FK
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });

        Schema::create('central_product_store', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('central_product_id');
            $table->foreign('central_product_id')->references('id')->on('central_products')->onDelete('cascade');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->unique(['central_product_id', 'business_id']);
            $table->index('business_id');
        });

        Schema::create('store_product_overrides', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('central_product_id');
            $table->foreign('central_product_id')->references('id')->on('central_products')->onDelete('cascade');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->decimal('sell_price', 22, 4)->nullable();
            $table->decimal('sell_price_inc_tax', 22, 4)->nullable();
            $table->decimal('alert_quantity', 22, 4)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->unique(['central_product_id', 'business_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_product_overrides');
        Schema::dropIfExists('central_product_store');
        Schema::dropIfExists('central_product_variations');
        Schema::dropIfExists('central_products');
    }
};
