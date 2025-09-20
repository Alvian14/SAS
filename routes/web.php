<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
    return view('auth.login');
})->name('login');


Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register/student', [AuthController::class, 'registerStudent'])->name('register.student.post');
Route::post('/register/teacher', [AuthController::class, 'registerTeacher'])->name('register.teacher.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// middleware
Route::get('/pages/dashboard/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.index');

Route::get('/pages/akun/indentitas_siswa', function () {
    return view('pages.akun.indentitas_siswa');
})->middleware('auth')->name('akun.indentitas_siswa');

Route::get('/pages/akun/indentitas_guru', function () {
    return view('pages.akun.indentitas_guru');
})->middleware('auth')->name('akun.indentitas_guru');
