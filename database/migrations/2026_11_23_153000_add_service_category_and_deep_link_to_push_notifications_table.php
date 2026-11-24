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
        if (Schema::hasTable('push_notifications')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('push_notifications', 'category_id')) {
                    $table->char('category_id', 36)->nullable()->after('reference_id');
                }
                if (!Schema::hasColumn('push_notifications', 'on_tap_action')) {
                    $table->string('on_tap_action', 50)->default('general')->after('category_id');
                }
                if (!Schema::hasColumn('push_notifications', 'audience_type')) {
                    $table->string('audience_type', 50)->default('all')->after('on_tap_action');
                }
                if (!Schema::hasColumn('push_notifications', 'sent_count')) {
                    $table->unsignedInteger('sent_count')->default(0)->after('is_sent');
                }
                if (!Schema::hasColumn('push_notifications', 'click_count')) {
                    $table->unsignedInteger('click_count')->default(0)->after('sent_count');
                }
                if (!Schema::hasColumn('push_notifications', 'estimated_reach')) {
                    $table->unsignedInteger('estimated_reach')->default(0)->after('click_count');
                }
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
        if (Schema::hasTable('push_notifications')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                $columns = ['category_id', 'on_tap_action', 'audience_type', 'sent_count', 'click_count', 'estimated_reach'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('push_notifications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
