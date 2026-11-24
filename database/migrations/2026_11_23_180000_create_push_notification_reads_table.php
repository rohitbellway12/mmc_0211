<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('push_notification_reads')) {
            Schema::create('push_notification_reads', function (Blueprint $table) {
                $table->id();
                $table->string('user_id', 64)->index();
                $table->string('notification_id', 64)->index();
                $table->timestamp('read_at')->useCurrent();
                $table->timestamps();

                $table->unique(['user_id', 'notification_id']);
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
        Schema::dropIfExists('push_notification_reads');
    }
};
