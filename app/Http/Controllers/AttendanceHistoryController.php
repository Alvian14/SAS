<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistory;
use App\Models\Classes;
use App\Models\Student;

class AttendanceHistoryController extends Controller
{
    public function absensiMapel($classId)
    {
        $kelas = Classes::findOrFail($classId);
        $absensi = AttendanceHistory::with(['student', 'class'])
            ->where('id_class', $classId)
            ->get();

        // Ambil semua siswa di kelas
        $allStudents = Student::where('id_class', $classId)->get();
        // Ambil id siswa yang sudah absen
        $sudahAbsenIds = $absensi->pluck('id_student')->unique();
        // Siswa yang belum absen
        $belumAbsen = $allStudents->whereNotIn('id', $sudahAbsenIds);

        return view('pages.absensi.absensi_mapel', [
            'absensi' => $absensi,
            'kelas'   => $kelas,
            'belumAbsen' => $belumAbsen,
        ]);
    }

    public function editStatus(Request $request, $id)
    {
        $absensi = AttendanceHistory::find($id);
        $siswa = Student::find($id);

        // Jika data absensi ditemukan, update status
        if ($absensi) {
            $request->validate([
                'status' => 'required|string'
            ]);
            $absensi->status = $request->status;
            $absensi->save();
            return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
        }

        // Jika data absensi tidak ditemukan, cek apakah $id adalah id siswa yang belum absen
        if ($siswa) {
            // Buat data absensi baru untuk siswa ini
            $newAbsensi = new AttendanceHistory();
            $newAbsensi->id_student = $siswa->id;
            $newAbsensi->id_class = $siswa->id_class;
            $newAbsensi->status = $request->status;
            $newAbsensi->created_at = now();
            $newAbsensi->updated_at = now();
            $newAbsensi->save();
            return response()->json(['success' => true, 'message' => 'Status absensi berhasil ditambahkan.']);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
    }
}
