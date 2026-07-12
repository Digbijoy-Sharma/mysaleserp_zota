<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 1
     * Drop the FK on users.business_id so super admins can have business_id = NULL.
     * (The column is kept; only the FK constraint is dropped.)
     */
    public function up()
    {
        // Drop the foreign key if it exists. The FK name follows the
        // Laravel default convention `users_business_id_foreign`.
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('id')->on('business')
                ->onDelete('cascade');
        });
    }
};
