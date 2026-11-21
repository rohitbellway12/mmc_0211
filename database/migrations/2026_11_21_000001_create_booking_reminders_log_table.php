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
    public function up(): void
    {
        if (!Schema::hasTable('booking_reminders_log')) {
            Schema::create('booking_reminders_log', function (Blueprint $table) {
                $table->id();
                $table->uuid('booking_id');
                $table->string('target_type', 30); // 'provider' or 'customer'
                $table->string('reminder_type', 50); // 'advance_24h', 'morning_day', 'before_2h', etc.
                $table->uuid('user_id')->nullable(); // provider owner user id or customer user id
                $table->text('fcm_token')->nullable();
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->string('status', 20)->default('sent'); // 'sent', 'failed'
                $table->text('response_log')->nullable();
                $table->timestamps();

                $table->index(['booking_id', 'target_type', 'reminder_type'], 'b_reminders_unique_check');
                $table->index('sent_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reminders_log');
    }
};
