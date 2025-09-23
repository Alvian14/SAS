<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

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
            // $now = Carbon::now();
            $now = $now = Carbon::parse('2025-09-22 11:06:00'); 
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
            $currentPeriod = null;
            
            $validPeriod = false;
            foreach (range($schedule->period_start, $schedule->period_end) as $p) {
                if (
                    isset($periods[$p]) &&
                    $currentTime >= $periods[$p]['start'] &&
                    $currentTime <  $periods[$p]['end']
                ) {
                    $validPeriod = true;
                    $currentPeriod = $p;
                    break;
                }
            }
            
            if (!$validPeriod) {
                return response()->json([
                    'success' => false,
                    'message' => 'jam pelajaran sudah lewat / belum dimulai'], 422);
            }

            $alreadyAttendance = AttendanceHistory::where('id_student', $idStudent)
                ->where('id_schedule', $schedule->id)
                ->whereDate('created_at', $now->toDateString())
                ->first();

            // --- Add debugging here ---
            // Log nilai-nilai yang digunakan dalam query
            Log::info('Checking for existing attendance record:', [
                'id_student' => $idStudent,
                'id_schedule' => $schedule->id,
                'date_query' => $now->toDateString(),
            ]);

            // Log hasil dari query
            Log::info('Query result:', [
                'attendance_record' => $alreadyAttendance,
            ]);
            // --- End debugging ---

            if ($alreadyAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah absen untuk mapel ini!'
                ], 422);
            }
            
            // Store attendance history
            // create new instance model
            $attendance = new AttendanceHistory();
            // Set attribute
            $attendance->id_student = $idStudent;
            $attendance->id_schedule = $schedule->id;
            $attendance->status = 'hadir';
            $attendance->period_number = $currentPeriod;
            // Set created_at
            $attendance->created_at = $now;
            // Set updated_at
            $attendance->updated_at = now(); 
            // save record to database
            $attendance->save();
            
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
