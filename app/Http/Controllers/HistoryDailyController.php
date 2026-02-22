<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistoryDaily;
use App\Models\Classes;
use App\Models\Student;

class HistoryDailyController extends Controller
{
    public function index()
    {
        // Ambil semua data absensi harian beserta relasi siswa dan kelas
        $absensi = AttendanceHistoryDaily::with(['student', 'class'])->orderByDesc('created_at')->get();

        return view('pages.absensi.absensi_harian', compact('absensi'));
    }
}
