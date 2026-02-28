<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\ClassController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong'], 200);
});

// auth routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// global route for public access
Route::get('/classes', [ClassController::class, 'index']);
// get schedules for a specific class (only active academic period schedules)
Route::get('/classes/{id}/schedule', [ClassController::class, 'schedule']);
Route::get('/classes/{id}/schedule/{dayindex}', [ClassController::class, 'scheduleByDay']);

// protected routes, need authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/attendance-daily/report', [AttendanceController::class, 'dailyAttendanceReport']);
    Route::post('/store-fcm', [UserController::class, 'storeFcmToken']);
});

// route login pakai Sanctum
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    
    // get user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('teacher', 'student');
    });
    
    // attendance record, permission, report, etc...
    Route::get('/testfeedback', [UserController::class, 'feedback']);
    Route::post('/qrattendance', [AttendanceController::class, 'qrAttendance']);
    Route::get('/attendance/report', [AttendanceController::class, 'report']);
    Route::get('/attendance/report/class', [AttendanceController::class, 'reportClass']);
    Route::post('/attendance/permission', [AttendanceController::class, 'createPermission']);
});
