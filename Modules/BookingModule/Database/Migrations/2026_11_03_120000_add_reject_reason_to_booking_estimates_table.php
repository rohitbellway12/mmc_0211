<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRejectReasonToBookingEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_estimates', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_estimates', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('status');
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
        Schema::table('booking_estimates', function (Blueprint $table) {
            if (Schema::hasColumn('booking_estimates', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }
        });
    }
}

