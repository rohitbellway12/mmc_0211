<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasColumn('push_notifications', 'send_type')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                $table->string('send_type', 20)->default('immediate')->after('is_active');
                $table->string('repeat_type', 20)->default('once')->after('send_type');
                $table->timestamp('scheduled_at')->nullable()->after('repeat_type');
                $table->time('scheduled_time')->nullable()->after('scheduled_at');
                $table->boolean('is_sent')->default(0)->after('scheduled_time');
                $table->timestamp('last_sent_at')->nullable()->after('is_sent');
            });

            // Set existing records to already sent
            DB::table('push_notifications')->update([
                'send_type' => 'immediate',
                'is_sent' => 1,
                'last_sent_at' => DB::raw('created_at')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropColumn([
                'send_type',
                'repeat_type',
                'scheduled_at',
                'scheduled_time',
                'is_sent',
                'last_sent_at'
            ]);
        });
    }
};
