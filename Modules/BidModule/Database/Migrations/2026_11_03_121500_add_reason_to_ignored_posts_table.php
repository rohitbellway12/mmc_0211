<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonToIgnoredPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ignored_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('ignored_posts', 'reason')) {
                $table->text('reason')->nullable()->after('provider_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ignored_posts', function (Blueprint $table) {
            if (Schema::hasColumn('ignored_posts', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }
}

