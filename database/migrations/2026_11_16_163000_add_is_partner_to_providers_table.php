<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPartnerToProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('providers') && !Schema::hasColumn('providers', 'is_partner')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->boolean('is_partner')->default(0)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('providers') && Schema::hasColumn('providers', 'is_partner')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropColumn('is_partner');
            });
        }
    }
}
