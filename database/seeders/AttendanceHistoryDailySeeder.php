<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceHistoryDaily;
use Carbon\Carbon;

class AttendanceHistoryDailySeeder extends Seeder
{
    public function run(): void
    {
        AttendanceHistoryDaily::create([
            'id_student' => 1,
            'id_class' => 1,
            'status' => 'tepat_waktu',
            'picture' => 'https://ui-avatars.com/api/?name=Student+One',
            'created_at' => Carbon::now()->setTime(7, 10, 0),
            'updated_at' => Carbon::now(),
        ]);
    }
}
