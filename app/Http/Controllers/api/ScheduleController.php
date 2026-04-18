<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ScheduleController extends Controller
{
    public function scheduleTeacherPersonal(Request $request)
    {
        // check Authorization header for Bearer token
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // get teacher id by token
        $user = $tokenModel->tokenable;
        $teacher = $user?->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }
        $idTeacher = $teacher->id;

        try {
            // find schedules by teacher_id directly
            $schedules = Schedule::where('id_teacher', $idTeacher)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->with(['subject', 'teacher', 'class'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules fetched successfully',
                'data' => $schedules
            ], 200);
        } catch (\Throwable $e) {
            Log::error('scheduleTeacherPersonal error', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }

    }

    public function scheduleTeacherPersonalByDay(Request $request) {
        // check Authorization header for Bearer token
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // get teacher id by token
        $user = $tokenModel->tokenable;
        $teacher = $user?->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $idTeacher = $teacher->id;

        try {
            // request time
            $now = Carbon::parse('2026-03-11 08:35:00', timezone: 'Asia/Jakarta');
            $dayOfWeek = strtolower($now->locale('id')->dayName); // get day name in Indonesian and convert to lowercase

            // find schedules by teacher_id, where day_of_week matches current day, and academic period is active
            $schedules = Schedule::where('id_teacher', $idTeacher)
                ->where('day_of_week', $dayOfWeek)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->with(['subject', 'teacher', 'class'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules fetched successfully',
                'data' => $schedules
            ], 200);
        } catch (\Throwable $e) {
            Log::error('scheduleTeacherPersonalByDay error', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    // get schedule by class and active academic period, for non personal schedule.
    public function scheduleByClass(Request $request, $classId)
    {
        // validate authorize user
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // find schedules by class_id directly

        try {
            //code...
            $schedules = Schedule::where('id_class', $classId)
                ->whereHas('academicPeriods', function ($q) {
                    $q->where('is_active', 1);
                })
                ->with(['subject', 'teacher', 'class'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Schedules fetched successfully',
                'data' => $schedules
            ], 200);
        } catch (\Throwable $th) {
            Log::error('scheduleByClass error', ['exception' => $th]);
            return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $th->getMessage()], 500);
        }
    }

    public function fetchAcademicPeriodActive()
    {
        try {
            $activePeriod = AcademicPeriod::where('is_active', 1)->first();
            if (!$activePeriod) {
                return response()->json(['success' => false, 'message' => 'No active academic period found'], 404);
            }
            return response()->json(['success' => true, 'data' => $activePeriod], 200);
        } catch (\Throwable $th) {
            Log::error('fetchAcademicPeriodActive error', ['exception' => $th]);
            return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $th->getMessage()], 500);
        }
    }

}
