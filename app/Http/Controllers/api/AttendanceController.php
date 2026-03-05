<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceHistory;
use App\Models\AttendanceHistoryDaily;
use App\Models\Permission;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Jobs\WriteAttendanceJob;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller

{

    public $distanceMaximumTolerance = 4500; // in meters

    public function qrAttendance(Request $request) {


    /// check academic period active or not.
        
        try {
            // request input validation
            $request->validate([
                'id_student' => 'required|integer',
                'id_class'   => 'required|integer',
                'qrcode'     => 'required|string',
                'latitude'   => 'required|numeric',
                'longitude'  => 'required|numeric',
            ]);
            
            // request data variable
            $idStudent = $request->id_student;
            $idClass   = $request->id_class;
            $rawCode   = $request->qrcode;
            
            // Coordinate Location from device client
            $lat = $request->latitude;
            $lon = $request->longitude;

            // Center of School Location
            $schoolLat = -7.7811912;
            $schoolLon = 112.0315286;

            // count distance with helper
            $distance = getDistanceInMeters($lat, $lon, $schoolLat, $schoolLon);

            if ($distance > $this->distanceMaximumTolerance) {
                return response()->json(
                    [
                    'success' => false,
                    'message' => "Lokasi di luar radius {$this->distanceMaximumTolerance} meter (jarak: {$distance} m)",
                ], 403);
            }

            // log
            error_log('distance: ');
            error_log($distance);

            // request time
            $now = Carbon::parse('2026-02-16 08:35:00', timezone: 'Asia/Jakarta');
            // $now = Carbon::now('Asia/Jakarta');
            $dayOfWeek = strtolower($now->locale('id')->dayName); // example: "senin"
            
            // schedule search (compare with qrcode) => class of student and schedule search
            $schedule = Schedule::query()
                ->where('id_class', $idClass)
                ->where('code', $rawCode)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
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

            $lockKey = "attendance:{$idStudent}:{$schedule->id}:" . $now->toDateString();

            return Cache::lock($lockKey, 5)->block(3, function () use (
                $idStudent,
                $idClass,
                $schedule,
                $lat,
                $lon,
                $currentPeriod,
                $now,
                $lockKey
            ) {
                Log::info('Attendance lock acquired', ['key' => $lockKey]);
                error_log('Attendance lock acquired: ' . $lockKey);

                $alreadyAttendance = AttendanceHistory::where('id_student', $idStudent)
                    ->where('id_schedule', $schedule->id)
                    ->where('attendance_date', $now->toDateString())
                    ->first();

                Log::info('Checking for existing attendance record:', [
                    'id_student' => $idStudent,
                    'id_schedule' => $schedule->id,
                    'date_query' => $now->toDateString(),
                ]);

                Log::info('Query result:', [
                    'attendance_record' => $alreadyAttendance,
                ]);

                if ($alreadyAttendance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah absen untuk mapel ini!'
                    ], 422);
                }

                // Dispatch background job to write attendance (quick response to client)
                $jobData = [
                    'id_student' => $idStudent,
                    'id_schedule' => $schedule->id,
                    'id_class' => $idClass,
                    'status' => 'hadir',
                    'coordinate' => "$lat, $lon",
                    'period_number' => $currentPeriod,
                    'attendance_date' => $now->toDateString(),
                    'created_at' => $now,
                ];

                WriteAttendanceJob::dispatch($jobData);

                return response()->json([
                    'success' => true,
                    'message' => 'Absensi diterima dan sedang diproses',
                    'data' => [
                        'schedule' => $schedule,
                    ]
                ], 202);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            // This will catch validation errors and return a 422 status
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (LockTimeoutException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sedang diproses, silakan coba lagi sebentar',
            ], 429);
        } catch (\Exception $e) {
            // This will catch any other general exceptions
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
        
    
    }

    // get attendance report from eloquent AttendanceHistoryDaily from face recognition attendance
    // based on student and date.
    public function dailyAttendanceReport(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
            ]);

            $user = $request->user();
            $student = $user->student ?? null;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student profile not found for user'], 403);
            }

            $date = Carbon::parse($request->date)->toDateString();

            $attendanceRecords = AttendanceHistoryDaily::query()
                ->where('id_student', $student->id)
                ->whereDate('created_at', $date)
                ->get()
                ->first();
            
            // if record is empty, return message no attendance record for that date
            if (!$attendanceRecords) {
                return response()->json([
                    'success' => true,
                    'message' => 'No attendance record for this date',
                    'data' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daily attendance report fetched',
                'data' => $attendanceRecords,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    // get attendance report based on schedule, student, and date.
    // expected result: List Schedule [schedule + attendance status (hadir, izin, sakit, alpa), if in schedule hasnt attendance record just return attendance status: null]
    // get parameter: id_class, date, id_student (from token)

    // this is for personal student attendance report, to show the attendance status for each schedule in that day.
    public function report(Request $request)
    {
        try {
            $request->validate([
                'id_class' => 'required|integer',
                'date' => 'required|date',
            ]);

            $user = $request->user();
            $student = $user->student ?? null;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student profile not found for user'], 403);
            }

            $idClass = $request->id_class;
            $date = Carbon::parse($request->date)->toDateString();
            $dayName = strtolower(Carbon::parse($date)->locale('id')->dayName);
            $dayIndex = Carbon::parse($date)->dayOfWeekIso; // 1..7

            // fetch schedules for class where academic period is active and day matches
            $schedules = Schedule::query()
                ->where('id_class', $idClass)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->where(function ($q) use ($dayName, $dayIndex) {
                    $q->where('day_of_week', $dayName)
                      ->orWhere('day_of_week', (string) $dayIndex);
                })
                ->with(['subject', 'teacher'])
                ->orderBy('period_start')
                ->get();

            $result = [];
            foreach ($schedules as $sch) {
                $attendance = AttendanceHistory::where('id_student', $student->id)
                    ->where('id_schedule', $sch->id)
                    ->where('attendance_date', $date)
                    ->first();

                $result[] = [
                    'schedule' => $sch,
                    'attendance_status' => $attendance ? $attendance->status : null,
                    'attendance' => $attendance ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance report fetched',
                'data' => $result,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // get attendance report history all student in once class filtered by class, and date now. 
    // expected result: List Schedule [schedule + all user in class + attendance status (hadir, izin, sakit, alpa), if in schedule hasnt attendance record, dont display the student in that schedule] 
    public function reportClass(Request $request)
    {
        try {
            $request->validate([
                'id_class' => 'required|integer',
                'date' => 'required|date',
            ]);

            $idClass = $request->id_class;
            $date = Carbon::parse($request->date)->toDateString();
            $dayName = strtolower(Carbon::parse($date)->locale('id')->dayName);
            $dayIndex = Carbon::parse($date)->dayOfWeekIso; // 1..7

            // schedules for class on that day and active academic period
            $schedules = Schedule::query()
                ->where('id_class', $idClass)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->where(function ($q) use ($dayName, $dayIndex) {
                    $q->where('day_of_week', $dayName)
                      ->orWhere('day_of_week', (string) $dayIndex);
                })
                ->with(['subject', 'teacher', 'class'])
                ->orderBy('period_start')
                ->get();

            $data = [];
            foreach ($schedules as $sch) {
                // get attendance records for that schedule on the date
                $attendances = AttendanceHistory::query()
                    ->where('id_schedule', $sch->id)
                    ->where('attendance_date', $date)
                    ->with(['student.user'])
                    ->get();

                // if there are no attendance records, still include schedule with empty list (per request could be omitted by client)
                $items = [];
                foreach ($attendances as $att) {
                    $items[] = [
                        'attendance' => $att,
                        // 'student' => $att->student,
                        'status' => $att->status,
                    ];
                }

                $data[] = [
                    'schedule' => $sch,
                    'attendances' => $items,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance history for class fetched',
                'data' => $data,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // post permission from student using data 
    public function createPermission(Request $request)
    {
        try {
            $request->validate([
                'reason' => 'required|string',
                'information' => 'nullable|string',
                'evidence' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'date_permission' => 'required|date',
                // count days
                'time_period' => 'required|integer|min:1|max:10', 
            ]);

            $user = $request->user();
            $student = $user->student ?? null;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student profile not found for user'], 403);
            }

            $permission = new Permission();
            $permission->id_student = $student->id;
            $permission->reason = $request->reason;
            $permission->information = $request->information;
            $permission->date_permission = Carbon::parse($request->date_permission)->toDateString();
            $permission->time_period = $request->time_period;

            if ($request->hasFile('evidence')) {
                $file = $request->file('evidence');

                // filename format: {id_student}_{nama_lengkap}_timestamp.extension
                $displayName = $student->name ?? ($student->user->name ?? 'student');
                $safeName = str_replace(' ', '-', strtolower($displayName));
                // Example: 12_budi-sudarsono_1771945730.jpg
                $filename = $student->id . '_' . $safeName . '_' . time() . '.' . $file->getClientOriginalExtension();


                Storage::disk('public')->putFileAs('permission/evidence', $file, $filename);
                $permission->evidence = $filename;
            }

            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission request created successfully',
                'data' => $permission,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
