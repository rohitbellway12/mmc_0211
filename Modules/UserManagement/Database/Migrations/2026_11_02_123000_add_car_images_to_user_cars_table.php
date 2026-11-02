<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarImagesToUserCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('user_cars', 'car_images')) {
                $table->longText('car_images')->nullable()->after('car_image');
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
        Schema::table('user_cars', function (Blueprint $table) {
            if (Schema::hasColumn('user_cars', 'car_images')) {
                $table->dropColumn('car_images');
            }
        });
    }
}

