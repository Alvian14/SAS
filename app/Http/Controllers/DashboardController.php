<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $serverTime = now()->format('H:i:s');
        $year = now()->year;

        // Statistik siswa
        $totalSiswa = Student::count();

        // Get class IDs by grade dari database
        $class10Ids = Classes::where('grade', 10)->pluck('id');
        $class11Ids = Classes::where('grade', 11)->pluck('id');
        $class12Ids = Classes::where('grade', 12)->pluck('id');

        // Count students in each grade
        $siswaKelas10 = Student::whereIn('id_class', $class10Ids)->count();
        $siswaKelas11 = Student::whereIn('id_class', $class11Ids)->count();
        $siswaKelas12 = Student::whereIn('id_class', $class12Ids)->count();

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
