<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = DB::table('clases')
            ->where('name', '11 TKJ 1')
            ->first();

        if (!$class) {
            throw new \RuntimeException('Kelas 11 TKJ 1 tidak ditemukan. Jalankan ClasesSeeder terlebih dahulu.');
        }

        $teacherId = DB::table('teachers')
            ->orderBy('id')
            ->value('id');

        if (!$teacherId) {
            throw new \RuntimeException('Tidak ada data guru untuk membuat jadwal. Jalankan TeacherSeeder terlebih dahulu.');
        }

        $academicPeriodId = DB::table('academic_periods')
            ->where('is_active', 1)
            ->value('id');

        if (!$academicPeriodId) {
            throw new \RuntimeException('Tidak ada academic period aktif. Jalankan AcademicPeriodsSeeder terlebih dahulu.');
        }

        $subjects = DB::table('subjects')
            ->orderBy('id')
            ->get(['id', 'code', 'name']);

        if ($subjects->count() < 4) {
            throw new \RuntimeException('Data mapel belum cukup untuk membuat jadwal. Jalankan SubjectSeeder terlebih dahulu.');
        }

        $periods = config('periods.periods');
        $scheduleWindows = [
            3 => [[1, 2], [3, 5], [6, 10]],
            4 => [[1, 1], [2, 3], [5, 7], [9, 10]],
        ];

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $daySubjectMap = [
            'senin' => [0, 1, 2],
            'selasa' => [2, 3, 4],
            'rabu' => [1, 3, 0, 4],
            'kamis' => [4, 0, 2],
            'jumat' => [3, 1, 2, 0],
            'sabtu' => [0, 4, 1],
        ];

        foreach ($days as $day) {
            $subjectIndexes = $daySubjectMap[$day];
            $count = count($subjectIndexes);
            $windows = $scheduleWindows[$count];

            foreach ($subjectIndexes as $slotIndex => $subjectIndex) {
                $subject = $subjects->get($subjectIndex);

                if (!$subject) {
                    continue;
                }

                [$periodStart, $periodEnd] = $windows[$slotIndex];

                if (!isset($periods[$periodStart], $periods[$periodEnd])) {
                    throw new \RuntimeException("Konfigurasi periode tidak ditemukan untuk rentang {$periodStart}-{$periodEnd}.");
                }

                $startTime = $periods[$periodStart]['start'];
                $endTime = $periods[$periodEnd]['end'];
                $token = (string) random_int(1000, 9999);
                $rawCode = "{$class->name}-" . strtoupper(substr($day, 0, 3)) . "-{$periodStart}-{$periodEnd}-{$subject->code}-{$academicPeriodId}-{$token}";

                DB::table('schedules')->insert([
                    'id_class' => $class->id,
                    'id_teacher' => $teacherId,
                    'id_subject' => $subject->id,
                    'day_of_week' => $day,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'id_academic_periods' => $academicPeriodId,
                    'code' => Crypt::encryptString($rawCode),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
