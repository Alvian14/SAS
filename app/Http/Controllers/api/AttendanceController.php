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
        
        try {
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
            $now = Carbon::now();
            // $now = $now = Carbon::parse('2025-09-22 07:10:00'); 
            $dayOfWeek = strtolower($now->locale('id')->dayName); // example: "senin"
            
            // schedule search (compare with qrcode) => class of student and schedule search
            $schedule = Schedule::query()
                ->where('id_class', $idClass)
                ->where('code', $rawCode)
                ->first();
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan / QR tidak valid'], 404);
            }
            
            // day validation
            if ($schedule->day_of_week !== $dayOfWeek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi tidak sesuai dengan jadwal'], 422);
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
                return response()->json([
                    'success' => false,
                    'message' => 'jam pelajaran sudah lewat / belum dimulai'], 422);
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
                'success' => true,
                'message' => 'Absensi berhasil',
                'data' => [
                    'schedule' => $schedule,
                    'attendance' => $attendance,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // This will catch validation errors and return a 422 status
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // This will catch any other general exceptions
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
        
    
    }
}
