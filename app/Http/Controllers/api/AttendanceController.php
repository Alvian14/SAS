<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\Schedule;

class AttendanceController extends Controller
{
    public function qrAttendance(Request $request) {
        
        // request input validation
        $request->validate([
            'id_student' => 'required|integer',
            'id_class'   => 'required|integer',
            'qrcode'     => 'required|string',
        ]);

        // request data variable
        $idStudent = $request->id_student;
        $idClass   = $request->id_class;
        $rawCode   = $request->qrcode;

        // request time
        // $now = Carbon::now();
        $now = $now = Carbon::parse('2025-09-22 12:30:00'); 
        $dayOfWeek = strtolower($now->locale('id')->dayName); // example: "senin"

        // schedule search (compare with qrcode)
        $schedule = Schedule::query()
            ->where('id_class', $idClass)
            ->where('code', $rawCode)
            ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Jadwal tidak ditemukan / QR tidak valid'], 404);
        }

        // day validation
        if ($schedule->day_of_week !== $dayOfWeek) {
            return response()->json(['message' => 'Hari tidak sesuai dengan jadwal'], 422);
        }

        // period validation using configuration that has been set
        $periods = config('periods.periods'); // period mapping
        $currentTime = $now->format('H:i');

        $validPeriod = false;
        foreach (range($schedule->period_start, $schedule->period_end) as $p) {
            if (
                isset($periods[$p]) &&
                $currentTime >= $periods[$p]['start'] &&
                $currentTime <  $periods[$p]['end']
            ) {
                $validPeriod = true;
                break;
            }
        }

        if (!$validPeriod) {
            return response()->json(['message' => 'Tidak dalam jam pelajaran sesuai jadwal'], 422);
        }

        // Store attendance history (next update)
        $attendance = 
        [
            'id_student'  => $idStudent,
            'id_schedule' => $schedule->id,
            'status'      => 'hadir',
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
        
        // return response
        return response()->json([
            'message'   => 'Absensi berhasil',
            'schedule'  => $schedule,
            'attendance'=> $attendance,
        ], 200);
    
    }
}
