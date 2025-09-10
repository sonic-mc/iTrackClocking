<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        $shifts = [
            [
                'name'        => 'Day Shift',
                'start_time'  => '08:00:00',
                'end_time'    => '16:30:00',
                'break_start' => '12:00:00',
                'break_end'   => '12:30:00',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Field Shift',
                'start_time'  => '09:00:00',
                'end_time'    => '17:00:00',
                'break_start' => '13:00:00',
                'break_end'   => '13:30:00',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Night Shift',
                'start_time'  => '18:00:00',
                'end_time'    => '06:00:00',
                'break_start' => '00:00:00',
                'break_end'   => '00:30:00',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('shifts')->insert($shifts);
    }
}
