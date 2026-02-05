<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PeriodeController;
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

// middleware auth
Route::get('/pages/dashboard/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.index');

######################### AKUN #########################
Route::get('/pages/akun/indentitas_siswa', [AuthController::class, 'identitasSiswa'])
    ->middleware('auth')
    ->name('akun.indentitas_siswa');

Route::delete('/pages/akun/indentitas_siswa/{id}', [AuthController::class, 'destroyStudent'])
    ->middleware('auth')
    ->name('akun.indentitas_siswa.destroy');

Route::put('/pages/akun/indentitas_siswa/{id}', [AuthController::class, 'updateStudent'])
    ->middleware('auth')
    ->name('akun.indentitas_siswa.update');

Route::put('/pages/akun/indentitas_siswa/{id}/edit-kelas', [AuthController::class, 'updateStudentClass'])
    ->middleware('auth')
    ->name('akun.indentitas_siswa.update_kelas');

Route::get('/pages/akun/indentitas_guru', [AuthController::class, 'identitasGuru'])
    ->middleware('auth')
    ->name('akun.indentitas_guru');

Route::post('/register/teacher', [AuthController::class, 'registerTeacher'])
    ->name('register.teacher.post');

Route::put('/pages/akun/indentitas_guru/{id}', [AuthController::class, 'updateTeacher'])
    ->middleware('auth')
    ->name('akun.indentitas_guru.update');

Route::delete('/pages/akun/indentitas_guru/{id}', [AuthController::class, 'destroyTeacher'])
    ->middleware('auth')
    ->name('akun.indentitas_guru.destroy');

// Route untuk hapus siswa

######################### END AKUN #########################


######################## PERIODE #########################
Route::get('/pages/periode/periode', [PeriodeController::class, 'index'])
    ->middleware('auth')
    ->name('periode.index');

Route::post('/pages/periode/periode', [PeriodeController::class, 'store'])
    ->middleware('auth')
    ->name('periode.store');
######################## END PERIODE #########################


######################### JADWAL #########################
Route::get('/pages/jadwal/jadwal', [JadwalController::class, 'index'])
->middleware('auth')
->name('jadwal.index');

######################### END JADWAL #########################


######################## KELAS #########################
Route::get('/pages/kelas/kelas_absensi', [ClassController::class, 'index'])
    ->middleware('auth')
    ->name('kelas.absensi');


Route::get('/pages/kelas/kelas_mapel', [ClassController::class, 'mapel'])
    ->middleware('auth')
    ->name('kelas.mapel');

// Route untuk tambah kelas
Route::post('/pages/kelas/kelas', [ClassController::class, 'store'])
    ->middleware('auth')
    ->name('kelas.store');

// Route untuk hapus kelas
Route::delete('/pages/kelas/kelas/{id}', [ClassController::class, 'destroy'])
    ->middleware('auth')
    ->name('kelas.destroy');

// Route untuk absensi kelas
// Route::get('/pages/kelas/absensi_face_recognition/{id}', function ($id) {
//     return view('pages.kelas.absensi_face_recognition', compact('id'));
// })->middleware('auth')->name('kelas.absensi_face_recognition');



######################## END KELAS #########################


######################## ABSENSI #########################

Route::get('/pages/absensi/absensi_harian', function () {
    return view('pages.absensi.absensi_harian');
})->middleware('auth')->name('absensi.absensi_harian');


Route::get('/pages/absensi/absensi_mapel', function () {
    return view('pages.absensi.absensi_mapel');
})->middleware('auth')->name('absensi.absensi_mapel');

######################## END ABSENSI #########################



