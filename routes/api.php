<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong'], 200);
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// route login pakai Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // get user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('teacher', 'student');
    });

    // attendance record, permission, report, etc...

});
