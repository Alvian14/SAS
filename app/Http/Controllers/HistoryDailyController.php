<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistoryDaily;
use App\Models\Classes;
use App\Models\Student;

class HistoryDailyController extends Controller
{
    public function absensiHarian($classId)
    {
        $kelas = Classes::findOrFail($classId);

        $absensi = AttendanceHistoryDaily::with(['student', 'class'])
            ->whereHas('class', function ($q) use ($classId) {
                $q->where('id', $classId);
            })
            ->get();

        return view('pages.absensi.absensi_harian', compact('absensi', 'kelas'));
    }
}
