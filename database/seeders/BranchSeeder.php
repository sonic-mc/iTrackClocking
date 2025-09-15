<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
          
            [
                'name' => 'Bulawayo Branch',
                'address' => '45 Robert Mugabe Way, Bulawayo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mutare Branch',
                'address' => '10 Samora Machel Ave, Mutare',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Gweru Branch',
                'address' => '78 Main Street, Gweru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Masvingo Branch',
                'address' => '56 High Street, Masvingo',
               
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('branches')->insert($branches);
    }
}
