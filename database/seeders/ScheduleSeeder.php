<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $day = 'senin'; // contoh untuk Senin
        $className = '10TKJ1';
        $classId = 1;
        $teacherId = 1;

        $subjects = [
            ['id' => 1, 'code' => 'MTK', 'name' => 'Matematika', 'start' => 1, 'end' => 2, 'id_academic_periods' => 2,],
            ['id' => 2, 'code' => 'SENBUD', 'name' => 'Seni Kebudayaan', 'start' => 3, 'end' => 3, 'id_academic_periods' => 2,],
            ['id' => 3, 'code' => 'SEJ', 'name' => 'Sejarah', 'start' => 4, 'end' => 5, 'id_academic_periods' => 2,],
            ['id' => 4, 'code' => 'BD', 'name' => 'Bahasa Daerah', 'start' => 6, 'end' => 6, 'id_academic_periods' => 2,],
            ['id' => 5, 'code' => 'KIM', 'name' => 'Kimia', 'start' => 7, 'end' => 8, 'id_academic_periods' => 2,],
        ];

        $periods = config('periods.periods');

        foreach ($subjects as $subject) {
            $startTime = $periods[(int) $subject['start']]['start'];
            $endTime   = $periods[(int) $subject['end']]['end'];
            $idAcademicPeriods = $subject['id_academic_periods'];

            // token 4 digit random
            $token = (string) random_int(1000, 9999);

            $rawCode = "{$className}-" . strtoupper(substr($day, 0, 3)) . "-{$subject['start']}-{$subject['end']}-{$subject['code']}-{$subject['id_academic_periods']}-{$token}";

            $jsonQr = [
                'raw_code'     => $rawCode,
                'class'        => $className,
                'day_of_week'  => $day,
                'period_start' => $subject['start'],
                'period_end'   => $subject['end'],
                'subject_code' => $subject['code'],
                'token'        => $token,
            ];

            DB::table('schedules')->insert([
                'id_class'     => $classId,
                'id_teacher'   => $teacherId,
                'id_subject'   => $subject['id'],
                'day_of_week'  => $day,
                'period_start' => $subject['start'],
                'period_end'   => $subject['end'],
                'start_time'   => $startTime,
                'end_time'     => $endTime,
                'id_academic_periods' => $idAcademicPeriods,
                'code'         => Crypt::encryptString($rawCode),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            dump($jsonQr);
        }

    }
}
