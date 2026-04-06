<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryDailyController;
use App\Http\Controllers\AttendanceHistoryController;
use App\Http\Controllers\NotificationController;

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

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/confirm-password', [AuthController::class, 'showConfirmPassword'])
    ->middleware('guest')
    ->name('password.confirm.form');

Route::post('/reset-password', [AuthController::class, 'updatePassword'])
    ->middleware('guest')
    ->name('password.update');

// middleware auth
Route::get('/pages/dashboard/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.index');

// ====================== akun =====================
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

// ====================== end akun =====================

// ===================== mata pelajaran ===============
Route::get('/pages/mapel/mapel', [SubjectController::class, 'index'])
    ->middleware('auth')
    ->name('mapel.index');

Route::post('/pages/mapel/mapel', [SubjectController::class, 'store'])
    ->middleware('auth')
    ->name('mapel.store');

Route::put('/pages/mapel/mapel/{id}', [SubjectController::class, 'update'])
    ->middleware('auth')
    ->name('mapel.update');

Route::delete('/pages/mapel/mapel/{id}', [SubjectController::class, 'destroy'])
    ->middleware('auth')
    ->name('mapel.destroy');

// ==================== end mata pelajaran =============

// ======================== periode ====================
Route::get('/pages/periode/periode', [PeriodeController::class, 'index'])
    ->middleware('auth')
    ->name('periode.index');

Route::post('/pages/periode/periode', [PeriodeController::class, 'store'])
    ->middleware('auth')
    ->name('periode.store');

Route::post('/periode/{id}/activate', [PeriodeController::class, 'activate'])->name('periode.activate');
Route::put('/periode/{id}', [PeriodeController::class, 'update'])->name('periode.update');
Route::delete('/periode/{id}', [PeriodeController::class, 'destroy'])->name('periode.destroy');
// ===================== end periode ====================


// ======================== jadwal ========================
Route::get('/pages/jadwal/jadwal', [JadwalController::class, 'index'])
    ->middleware('auth')
    ->name('jadwal.index');

Route::post('/pages/jadwal/jadwal', [JadwalController::class, 'store'])
    ->middleware('auth')
    ->name('jadwal.store');

Route::put('/pages/jadwal/jadwal/{id}', [JadwalController::class, 'update'])
    ->middleware('auth')
    ->name('jadwal.update');
Route::delete('/pages/jadwal/jadwal', [JadwalController::class, 'destroy'])
    ->middleware('auth')
    ->name('jadwal.destroy');
Route::get('/jadwal/qr/{id}', [JadwalController::class, 'showQr'])->name('jadwal.qr');
// ========================= end jadwal ======================


// ============================ kelas ==========================
Route::get('/pages/kelas/kelas_absensi', [ClassController::class, 'harian'])
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



// ========================== end kelas ======================


// ============================ absensi ======================

// Route::get('/pages/absensi/absensi_harian', function () {
//     return view('pages.absensi.absensi_harian');
// })->middleware('auth')->name('absensi.absensi_harian');


Route::get('/pages/absensi/absensi_harian/{classId}', [HistoryDailyController::class, 'absensiHarian'])
    ->middleware('auth')
    ->name('absensi.absensi_harian');

Route::post('/absensi-harian/edit-status/{id}', [HistoryDailyController::class, 'editStatus'])
->name('absensi_harian.edit_status');

Route::get('/absensi-harian/export-excel/{classId}', [HistoryDailyController::class, 'exportExcel'])
    ->middleware('auth')
    ->name('absensi_harian.export_excel');

Route::get('/pages/absensi/absensi_mapel/{classId}', [AttendanceHistoryController::class, 'absensiMapel'])
    ->middleware('auth')
    ->name('absensi.absensi_mapel');

Route::post('/absensi-mapel/edit-status/{id}', [AttendanceHistoryController::class, 'editStatus'])
    ->name('absensi_mapel.edit_status');



// ============================ end absensi ====================

Route::get('/pages/notifikasi/notifikasi', [NotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifikasi.index');

Route::get('/pages/setting/setting', [AuthController::class, 'showSetting'])
    ->middleware('auth')
    ->name('setting.index');

Route::post('/pages/setting/setting', [AuthController::class, 'updateSetting'])
    ->middleware('auth')
    ->name('setting.update');

Route::post('/pages/setting/delete-profile-picture', [AuthController::class, 'deleteProfilePicture'])
    ->middleware('auth')
    ->name('setting.photo.delete');
