<?php

use Illuminate\Database\Migrations\Migration;
use Modules\ServiceManagement\Entities\Variation;
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
                $existingVariations = Variation::all()->groupBy('service_id');
                foreach ($existingVariations as $serviceId => $serviceVars) {
                    $uniqueVariants = $serviceVars->unique('variant_key');
                    foreach ($uniqueVariants as $v) {
                        foreach ($activeZoneIds as $zoneId) {
                            Variation::firstOrCreate([
                                'service_id' => $serviceId,
                                'variant_key' => $v->variant_key,
                                'zone_id' => (string)$zoneId,
                            ], [
                                'variant' => $v->variant,
                                'price' => $v->price,
                            ]);
                        }
                    }
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
