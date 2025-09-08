<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/pages/dashboard',[DashboardController::class, 'index'])->name('dashboard.index');
