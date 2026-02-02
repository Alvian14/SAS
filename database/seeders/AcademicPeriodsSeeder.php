<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicPeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('academic_periods')->insert([
            [
                'name' => 'Ganjil 2025/2026',
                'start_date' => Carbon::create(2025, 7, 14, 0, 0, 0),
                'end_date' => Carbon::create(2025, 12, 20, 23, 59, 59),
                'is_active' => false, 
                'created_at' => Carbon::create(2025, 7, 1, 0, 0, 0),
                'updated_at' => Carbon::create(2025, 7, 1, 0, 0, 0),
            ],
            [
                'name' => 'Genap 2025/2026',
                'start_date' => Carbon::create(2026, 1, 6, 0, 0, 0),
                'end_date' => Carbon::create(2026, 6, 27, 23, 59, 59),
                'is_active' => true,
                'created_at' => Carbon::create(2025, 12, 1, 0, 0, 0),
                'updated_at' => Carbon::create(2025, 12, 1, 0, 0, 0),
            ],
        ]);
    }
}
