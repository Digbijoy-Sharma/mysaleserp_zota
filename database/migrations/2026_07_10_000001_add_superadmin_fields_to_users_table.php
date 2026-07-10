<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dava India — Phase 1
     * Add super-admin flags to users.
     *
     *  is_superadmin         : 1 if this user is a Dava India super admin
     *  created_by_superadmin : 1 if account was created by a super admin
     *                          (used for distinguishing store users vs HQ users
     *                          when business_id is NULL)
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_superadmin')->default(0)->after('business_id')->index();
            $table->tinyInteger('created_by_superadmin')->default(0)->after('is_superadmin');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_superadmin', 'created_by_superadmin']);
        });
    }
};
