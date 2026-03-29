<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistoryDaily;
use App\Models\Classes;
use App\Models\Student;
use App\Exports\AbsensiHarianExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class HistoryDailyController extends Controller
{
    public function absensiHarian($classId)
    {
        $kelas = Classes::findOrFail($classId);

        // Ambil semua siswa di kelas berdasarkan kolom id_class
        $students = Student::where('id_class', $classId)->get();

        // Ambil absensi harian yang sudah ada
        $absensi = AttendanceHistoryDaily::with(['student', 'class'])
            ->where('id_class', $classId)
            ->get();

        // Gabungkan data siswa dan absensi
        $result = [];
        foreach ($students as $student) {
            $absensiItem = $absensi->where('id_student', $student->id)->first();
            $result[] = (object)[
                'id' => $absensiItem ? $absensiItem->id : null,
                'student' => $student,
                'class' => $kelas,
                'status' => $absensiItem ? $absensiItem->status : 'in progress',
                'created_at' => $absensiItem ? $absensiItem->created_at : null,
                'picture' => $absensiItem ? $absensiItem->picture : null,
            ];
        }

        return view('pages.absensi.absensi_harian', [
            'absensi' => collect($result),
            'kelas' => $kelas
        ]);
    }

    public function editStatus(Request $request, $id)
    {
        $absensi = AttendanceHistoryDaily::findOrFail($id);
        $request->validate([
            'status' => 'required|string'
        ]);
        $absensi->status = $request->status;
        $absensi->save();

        return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
    }

    // public function exportExcel($classId, Request $request)
    // {
    //     // NOTE: Implementasi query disesuaikan dengan struktur tabel absensi di project ini.
    //     // Saya buat defensif: kalau method/data sumber sudah ada untuk halaman absensi harian,
    //     // paling aman dipakai ulang di sini.

    //     $rows = collect();
    //     $kelasName = 'Kelas';

    //     if (method_exists($this, 'getAbsensiHarianRowsForExport')) {
    //         [$rows, $kelasName] = $this->getAbsensiHarianRowsForExport($classId, $request);
    //     } else {
    //         // Fallback minimal: jangan bikin error fatal kalau belum ada helper data.
    //         // Silakan arahkan ke query yang sama seperti DataTables/halaman list.
    //         $rows = new Collection();
    //     }

    //     $tanggal = $request->get('tanggal');
    //     $suffixTanggal = $tanggal ? ('_' . preg_replace('/[^0-9\-]/', '', $tanggal)) : '';
    //     $filename = 'Rekap_Absensi_Harian_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $kelasName) . $suffixTanggal . '.xlsx';

    //     return Excel::download(new AbsensiHarianExport($rows, $kelasName), $filename);
    // }
}
