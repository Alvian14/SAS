<?php

namespace App\Jobs;

use App\Models\AttendanceHistory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WriteAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Determine attendance time (prefer explicit attendance_time, then created_at, then now)
            $attendanceTime = null;
            if (!empty($this->data['attendance_time'])) {
                $attendanceTime = Carbon::parse($this->data['attendance_time']);
            } elseif (!empty($this->data['created_at'])) {
                $attendanceTime = Carbon::parse($this->data['created_at']);
            } else {
                $attendanceTime = Carbon::now();
            }

            $attributes = [
                'id_student' => $this->data['id_student'],
                'id_schedule' => $this->data['id_schedule'],
                'attendance_date' => $attendanceTime->toDateString(),
            ];

            $values = array_merge($attributes, [
                'id_class' => $this->data['id_class'] ?? null,
                'status' => $this->data['status'] ?? 'hadir',
                'coordinate' => $this->data['coordinate'] ?? null,
                'period_number' => $this->data['period_number'] ?? null,
                'attendance_time' => $attendanceTime->toDateTimeString(),
                'created_at' => $this->data['created_at'] ?? $attendanceTime->toDateTimeString(),
                'updated_at' => now(),
            ]);

            // firstOrCreate prevents duplicate rows and is idempotent.
            $attendance = AttendanceHistory::firstOrCreate($attributes, $values);

            Log::info('WriteAttendanceJob: attendance written', ['id' => $attendance->id ?? null, 'attrs' => $attributes]);
        } catch (\Exception $e) {
            Log::error('WriteAttendanceJob failed', ['error' => $e->getMessage(), 'data' => $this->data]);
            throw $e;
        }
    }
}
