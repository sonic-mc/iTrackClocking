<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeofenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example branch geofences (latitude, longitude, radius in meters)
        $geofences = [
            ['branch_id' => 1, 'name' => 'Harare HQ', 'lat' => -17.8252, 'lng' => 31.0335, 'radius' => 500],
            ['branch_id' => 2, 'name' => 'Bulawayo Branch', 'lat' => -20.1320, 'lng' => 28.6265, 'radius' => 500],
            ['branch_id' => 3, 'name' => 'Mutare Branch', 'lat' => -18.9707, 'lng' => 32.6700, 'radius' => 500],
            ['branch_id' => 4, 'name' => 'Gweru Branch', 'lat' => -19.4520, 'lng' => 29.8220, 'radius' => 500],
            ['branch_id' => 5, 'name' => 'Masvingo Branch', 'lat' => -20.0707, 'lng' => 30.8320, 'radius' => 500],
        ];

        $data = [];
        foreach ($geofences as $gf) {
            $data[] = [
                'branch_id' => $gf['branch_id'],
                'name' => $gf['name'],
                'latitude' => $gf['lat'],
                'longitude' => $gf['lng'],
                'radius' => $gf['radius'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('geofences')->insert($data);
    }
}
