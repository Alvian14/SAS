<?php

namespace App\Http\Controllers\api;

use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Jobs\WriteAttendanceJob;
use App\Models\AttendanceHistory;
use App\Models\AttendanceHistoryDaily;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\FirebaseMessagingService;
use App\Services\NotificationHelperService;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller

{
    protected FirebaseMessagingService $fcm;
    protected NotificationHelperService $notificationHelper;
    public $distanceMaximumTolerance = 100000; // in meters

    public $latOfAttendance = -7.7811912;
    public $lonOfAttendance = 112.0315286;


    public function __construct(FirebaseMessagingService $fcm, NotificationHelperService $notificationHelper)
    {
        $this->fcm = $fcm;
        $this->notificationHelper = $notificationHelper;
    }

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
            // $idStudent = $request->id_student;
            // $idClass   = $request->id_class;
            // $rawCode   = $request->qrcode;

            // get request based on auth user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // check if user has student profile
            $student = $user->student ?? null;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student profile not found for user'], 403);
            }

            $idStudent = $student->id;
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
            // Jika client mengirimkan `date` (format ISO atau yang bisa di-parse oleh Carbon),
            // gunakan itu sebagai waktu sekarang untuk keperluan testing/override.
            if ($request->filled('date')) {
                $now = Carbon::parse($request->input('date'), 'Asia/Jakarta');
            } else {
                // $now = Carbon::now('Asia/Jakarta');
                // testing using date: 1 April 2026 07:15 WIB (should be valid for schedule 7-8)
                $now = Carbon::parse('Asia/Jakarta');
            }

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

    public function permissionReport(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $user = $request->user();
            $student = $user->student ?? null;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student profile not found for user'], 403);
            }

            $startDate = Carbon::parse($request->start_date)->toDateString();
            $endDate = Carbon::parse($request->end_date)->toDateString();

            $permissions = Permission::query()
                ->where('id_student', $student->id)
                ->whereBetween('date_permission', [$startDate, $endDate])
                ->get();

            if ($permissions->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No permission records for this date',
                    'data' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission report fetched',
                'data' => $permissions,
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

    // get attendance in schedule per day by class id. (teacher only)
    public function reportClassById(Request $request, $classId, $date)
    {
        try {
            // check bearer token and get user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $date = Carbon::parse($date)->toDateString();
            $dayName = strtolower(Carbon::parse($date)->locale('id')->dayName);
            $dayIndex = Carbon::parse($date)->dayOfWeekIso; // 1..7

            $students = $this->getStudentsForClass((int) $classId);
            $schedules = $this->getActiveSchedulesForClassByDate((int) $classId, $dayName, $dayIndex);

            $data = [];
            foreach ($schedules as $sch) {
                $data[] = [
                    'schedule' => $sch,
                    'attendances' => $this->buildAttendanceItemsForSchedule($sch, $date, $students),
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

    // daily report attendance for teacher side.
    public function reportDailyAttendance(Request $request)
    {
        try{
            // check bearer token and get user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // get input request $classId, $date
            $request->validate([
                'id_class' => 'required|integer',
                'date' => 'required|date',
            ]);

            $classId = $request->input('id_class');
            $date = $request->input('date');
            $historyReport = AttendanceHistoryDaily::query()
                ->where('id_class', $classId)
                // where active period
                ->whereHas('class.schedules.academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->whereDate('created_at', $date)
                ->with(['class', 'student'])
                ->get();


            if (!$historyReport) {
                return response()->json([
                    'success' => true,
                    'message' => 'No attendance history for this class and date',
                    'data' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daily attendance report for class fetched',
                'data' => $historyReport,
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

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Student>
     */
    private function getStudentsForClass(int $classId)
    {
        return Student::query()
            ->where('id_class', $classId)
            ->with('user')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Schedule>
     */
    private function getActiveSchedulesForClassByDate(int $classId, string $dayName, int $dayIndex)
    {
        return Schedule::query()
            ->where('id_class', $classId)
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
    }

    /**
     * @param \App\Models\Schedule $schedule
     * @param string $date
     * @param \Illuminate\Support\Collection<int, \App\Models\Student> $students
     * @return array<int, array{attendance: \App\Models\AttendanceHistory, status: mixed}>
     */
    private function buildAttendanceItemsForSchedule($schedule, string $date, $students): array
    {
        $attendances = AttendanceHistory::query()
            ->where('id_schedule', $schedule->id)
            ->where('attendance_date', $date)
            ->with(['student.user'])
            ->get();

        $attendanceByStudent = $attendances->keyBy('id_student');
        $items = [];

        foreach ($students as $student) {
            $attendance = $attendanceByStudent->get($student->id);

            if (!$attendance) {
                $attendance = new AttendanceHistory([
                    'period_number' => $schedule->period_start,
                    'status' => 'null',
                    'coordinate' => null,
                    'attendance_date' => $date,
                    'id_student' => $student->id,
                    'id_schedule' => $schedule->id,
                    'id_class' => $schedule->id_class,
                ]);
                $attendance->setRelation('student', $student);
            }

            $items[] = [
                'attendance' => $attendance,
                'status' => $attendance->status,
            ];
        }

        return $items;
    }

    // permission get by class id. (teacher only)
    public function permissionReportByClass(Request $request, $classId)
    {
        try {
            // check bearer token and get user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $request->validate([
                'start_date' => 'required|string',
                'end_date' => 'required|string|after_or_equal:start_date',
            ]);
            
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $permissions = Permission::query()
                ->whereHas('student', function ($q) use ($classId) {
                    $q->where('id_class', $classId);
                })
                ->whereBetween('date_permission', [$startDate, $endDate])
                ->with(['student.user'])
                ->orderBy('date_permission', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Permission report for class fetched',
                'data' => $permissions,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // permission accept for teacher only
    public function permissionAccept(Request $request)
    {
        try {
            // check bearer token and get user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $permissionId = $request->input('permission_id');

            if (!$permissionId) {
                return response()->json(['success' => false, 'message' => 'Permission ID is required'], 422);
            }

            // find permission by id
            $permission = Permission::find($permissionId);
            if (!$permission) {
                return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
            }

            // permission must be processed only, if already accepted or rejected, cannot be processed again.
            if ($permission->status === 'diterima') {
                return response()->json(['success' => false, 'message' => 'Permission already accepted'], 422);
            }
            if ($permission->status === 'ditolak') {
                return response()->json(['success' => false, 'message' => 'Permission already rejected'], 422);
            }

            $student = Student::find($permission->id_student);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found'], 404);
            }

            DB::transaction(function () use ($permission, $user, $request, $student) {
                // update status to accepted
                $permission->status = 'diterima';
                $permission->approved_by = $user->id; // set approved by teacher id
                $permission->feedback = $request->input('feedback', null); // optional feedback message from teacher
                $permission->save();

                // * create attendance record history to related date (only fill with schedule that has active academic period, and match with date permission).
                $permissionReason = $permission->reason; // contoh: 'sakit', 'izin', 'dispen', 'lainnya'
                if ($permissionReason === 'lainnya') {
                    $permissionReason = 'izin';
                }

                $permissionDate = Carbon::parse($permission->date_permission)->startOfDay();
                $permissionTimePeriod = (int) $permission->time_period;
                $createdSchoolDays = 0;
                $currentDate = $permissionDate->copy();

                // Count time_period as school days; weekend days are skipped and replaced by the next weekday.
                while ($createdSchoolDays < $permissionTimePeriod) {
                    if ($currentDate->isWeekend()) {
                        $currentDate->addDay();
                        continue;
                    }

                    $dayName = strtolower($currentDate->locale('id')->dayName);
                    $dayIndex = $currentDate->dayOfWeekIso; // 1..7

                    // search related schedule with day_of_week same and active academic period
                    $schedules = Schedule::query()
                        ->where('id_class', $student->id_class)
                        ->whereHas('academicPeriods', function ($q) {
                            $q->where('is_active', 1);
                        })
                        ->where(function ($q) use ($dayName, $dayIndex) {
                            $q->where('day_of_week', $dayName)
                                ->orWhere('day_of_week', (string) $dayIndex);
                        })
                        ->get();

                    foreach ($schedules as $schedule) {
                        AttendanceHistory::updateOrCreate(
                            [
                                'id_schedule' => $schedule->id,
                                'id_student' => $student->id,
                                'attendance_date' => $currentDate->toDateString(),
                            ],
                            [
                                'id_class' => $schedule->id_class,
                                'period_number' => $schedule->period_start,
                                'coordinate' => "$this->latOfAttendance, $this->lonOfAttendance",
                                'status' => $permissionReason,
                            ]
                        );
                    }

                    $createdSchoolDays++;
                    $currentDate->addDay();
                }
            });

            // * notification zone: send notification to student related to the permission that has been accepted
            $userStudent = $student->user;
            if (!$userStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'User student not found',
                ], 404);
            }

            $fcmToken = $userStudent->device_token;

            $template = $this->notificationHelper->templatePermissionApproval(
                $fcmToken
            );

            $notificationResult = $this->notificationHelper->send($template);

            $createTemplate = $this->notificationHelper->createTemplateForStudent(
                (int) $userStudent->id,
                $template->title,
                $template->body,
                NotificationType::Permission,
                // user id of teacher, convert to int.
                (int) $user->id
            );

            Notification::create($createTemplate->toArray());

            // * end of notification zone


            return response()->json([
                'success' => true,
                'message' => 'Izin diterima untuk siswa ' . $student->name,
                'data' => [
                    'permission' => $permission,
                    'notification_result' => $notificationResult,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function permissionReject(Request $request)
    {
        try {
            // check bearer token and get user
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $permissionId = $request->input('permission_id');

            if (!$permissionId) {
                return response()->json(['success' => false, 'message' => 'Permission ID is required'], 422);
            }

            // find permission by id
            $permission = Permission::find($permissionId);
            if (!$permission) {
                return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
            }

            // permission must be processed only, if already accepted or rejected, cannot be processed again.
            if ($permission->status === 'diterima') {
                return response()->json(['success' => false, 'message' => 'Permission already accepted'], 422);
            }
            if ($permission->status === 'ditolak') {
                return response()->json(['success' => false, 'message' => 'Permission already rejected'], 422);
            }

            $student = Student::find($permission->id_student);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found'], 404);
            }

            DB::transaction(function () use ($permission, $user, $request) {

                // update status to rejected
                $permission->status = 'ditolak';
                $permission->approved_by = $user->id; // set approved by teacher id
                $permission->feedback = $request->input('feedback', null); // optional feedback message from teacher
                $permission->save();
            
            });

            /// send notification while saving to database notification.

            $fcmToken = $student->user->device_token;

            $template = $this->notificationHelper->templatePermissionRejection(
                $fcmToken,
                $request->input('feedback', null) // optional custom message from teacher feedback
            );

            $notificationResult = $this->notificationHelper->send($template);

            $createTemplate = $this->notificationHelper->createTemplateForStudent(
                (int) $student->user->id,
                $template->title,
                $template->body,
                NotificationType::Permission,
                // user id of teacher, convert to int.
                (int) $user->id
            );

            Notification::create($createTemplate->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Izin ditolak untuk siswa ' . $student->name,
                'data' => [
                    'permission' => $permission,
                    'notification_result' => $notificationResult,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reportDisrepancyStudentAttendanceByHistoryId(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // input validate
        $request->validate([
            'attendance_history_id' => 'required|integer',
            'disrepancy_type' => 'nullable|string|in:terlambat,hp_tidak_tersedia,izin,pulang_awal,alasan_lain',
            'description' => 'nullable|string',
        ]);

        $attendanceHistoryId = $request->input('attendance_history_id');

        $attendanceHistory = AttendanceHistory::query()
            ->where('id', $attendanceHistoryId)
            ->with(['schedule'])
            ->first();

        if (!$attendanceHistory) {
            return response()->json(['success' => false, 'message' => 'Attendance history not found'], 404);
        }

        // cek status attendance history if already 'invalid', cannot report disrepancy again.
        if ($attendanceHistory->status === 'invalid') {
            return response()->json(['success' => false, 'message' => 'Attendance history already reported as invalid'], 422);
        }

        /// logic disrepancy here.
        DB::transaction(function () use ($request, $attendanceHistory) {
            $attendanceHistory->status = 'invalid';
            $attendanceHistory->save();

            // create report discrepancy
            $report = new Report();
            $report->id_attendance_history = $attendanceHistory->id;
            $report->id_student = $attendanceHistory->id_student;
            $report->id_class = $attendanceHistory->id_class;
            $report->student_name = $attendanceHistory->student->name ?? 'Unknown Student';
            $report->reported_by = $request->user()->id;
            $report->disrepancy_type = $request->input('disrepancy_type', 'alasan_lain'); // default to 'alasan_lain' if not provided
            $report->description = $request->input('description', null);
            $report->marked_as_resolved = false;
            $report->attendance_date = $attendanceHistory->attendance_date;
            $report->save();

        });

        /// notification zone for report disrepancy, send notification to teacher related to the schedule of attendance history that has been reported.
        $userStudent = $attendanceHistory->student->user;
        $fcmToken = $userStudent->device_token;

        $template = $this->notificationHelper->templateAttendanceViolation(
            $fcmToken,
            $request->input('description', null) // optional custom message from student about the disrepancy
        );

        $notificationResult = $this->notificationHelper->send($template);

        $createTemplate = $this->notificationHelper->createTemplateForStudent(
            (int) $userStudent->id,
            $template->title,
            $template->body,
            NotificationType::AttendanceViolation,
            // user id of system or admin, convert to int. (for example: 0)
            (int) $user->id
        );

        Notification::create($createTemplate->toArray());

        return response()->json([
            'success' => true,
            'data' => [
                'report' => Report::query()->where('id_attendance_history', $attendanceHistory->id)->first(),
                'notification_result' => $notificationResult,
            ]
        ], 200);

    }

}
