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
}
