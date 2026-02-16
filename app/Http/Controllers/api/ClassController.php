<?php

namespace App\Http\Controllers\api;

use App\Models\Classes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ClassController extends Controller
{
    // Ambil semua data kelas
    public function index()
    {
        return response()->json([
            'success' => true, 
            'message' => 'Student classes fetched successfully', 
            'data' => Classes::all()],
        200);
    }

    // Ambil detail kelas berdasarkan id
    public function show($id)
    {
        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }
        return response()->json($class, 200);
    }

    // get schedule for a specific class
    public function schedule(Request $request, $id)
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

        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }
        $schedules = $class->schedules()->whereHas('academicPeriods', function ($q) {
            $q->where('is_active', 1);
        })->with(['subject', 'teacher'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Class schedules fetched successfully',
            'data' => $schedules
        ], 200);
    }
    
    // get schedule for a specific class for a given day index (1=Senin,..7=Minggu)
    public function scheduleByDay(Request $request, $id, $dayindex)
    {
        // same auth check as schedule()
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $dayIndex = (int) $dayindex;
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $dayName = $dayNames[$dayIndex] ?? null;

        $query = $class->schedules()->whereHas('academicPeriods', function ($q) {
            $q->where('is_active', 1);
        });

        if ($dayName) {
            $query->where(function ($q) use ($dayIndex, $dayName) {
                $q->where('day_of_week', $dayName)->orWhere('day_of_week', (string) $dayIndex);
            });
        } else {
            $query->where('day_of_week', (string) $dayIndex);
        }

        $schedules = $query->with(['subject', 'teacher'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Class schedules fetched successfully for day',
            'day_index' => $dayIndex,
            'data' => $schedules
        ], 200);
    }

}
