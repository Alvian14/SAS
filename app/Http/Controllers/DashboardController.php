<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $serverTime = now()->format('H:i:s');
        $year = now()->year;

        // Statistik siswa
        $totalSiswa = Student::count();
        $siswaKelas10 = Student::where('id_class', 1)->count();
        $siswaKelas11 = Student::where('id_class', 2)->count();
        $siswaKelas12 = Student::where('id_class', 3)->count();

        // Ambil data absensi harian per hari untuk semua bulan dalam tahun berjalan
        $allMonthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $allMonthlyStats[$m] = DB::table('attendance_history_dailys')
                ->select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as total'))
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->groupBy(DB::raw('DAY(created_at)'))
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();
        }

        $month = now()->month;
        $monthlyStats = $allMonthlyStats[$month];

        return view('pages.dashboard.dashboard', compact(
            'serverTime',
            'totalSiswa',
            'siswaKelas10',
            'siswaKelas11',
            'siswaKelas12',
            'monthlyStats',
            'allMonthlyStats'
        ));
    }
}
