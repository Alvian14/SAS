<?php

use App\Http\Controllers\api\ClassController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong'], 200);
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/classes', [ClassController::class, 'index']);

// route login pakai Sanctum
<<<<<<< HEAD
Route::middleware('auth:sanctum')->group(function () {

=======
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    
>>>>>>> c4941eb28307a7f26c4f295596210523182f54e7
    // get user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('teacher', 'student');
    });

    // attendance record, permission, report, etc...
    Route::get('/testfeedback', [UserController::class, 'feedback']);

});
