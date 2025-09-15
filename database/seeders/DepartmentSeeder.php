<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [1, 2, 3, 4, 5]; // Assuming branch IDs 1-5
        $departments = ['IT', 'Human Resources', 'Sales', 'Marketing', 'Management', 'Administration', 'Finance'];

        $data = [];

        foreach ($branches as $branchId) {
            foreach ($departments as $dept) {
                $data[] = [
                    'name' => $dept,
                    'branch_id' => $branchId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('departments')->insert($data);
    }
}
