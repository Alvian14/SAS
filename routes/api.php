<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\ClassController;
use App\Http\Controllers\api\ScheduleController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// firebase notification test
Route::post('/fcm/send-token', [NotificationController::class, 'sendToToken']);
Route::post('/fcm/send-topic', [NotificationController::class, 'sendToTopic']);

// protected routes, need authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/attendance-daily/report', [AttendanceController::class, 'dailyAttendanceReport']);
    Route::post('/store-fcm', [UserController::class, 'storeFcmToken']);
    Route::post('/get-topic-from-class/{classId}', [UserController::class, 'getTopicFromClass']);
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
    Route::get('/attendance/permission/report', [AttendanceController::class, 'permissionReport']);
    Route::post('/attendance/permission', [AttendanceController::class, 'createPermission']);
    
});

Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('teacher', 'student');
    });

    // schedule teacher personal
    Route::get('/teacher/schedules', [ScheduleController::class, 'scheduleTeacherPersonal']);
    Route::get('/teacher/schedules/day', [ScheduleController::class, 'scheduleTeacherPersonalByDay']);
    Route::get('/schedules/{classId}', [ScheduleController::class, 'scheduleByClass']);

    // get class information
    Route::get('/info/class/{id}', [ClassController::class, 'studentClassInformation']);

    // attendance record, permission...
    Route::get('/teacher/attendance/class/{classId}/{date}', [AttendanceController::class, 'reportClassById']);

});
