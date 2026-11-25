<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\CategoryManagement\Entities\Category;
use Modules\ZoneManagement\Entities\Zone;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            $activeZoneIds = Zone::where('is_active', 1)->pluck('id')->toArray();
            if (!empty($activeZoneIds)) {
                $categories = Category::where('position', 1)->get();
                foreach ($categories as $category) {
                    $category->zones()->syncWithoutDetaching($activeZoneIds);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during setup if tables are not ready
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse needed
    }
};
