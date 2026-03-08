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

        $students = Student::where('id_class', $classId)->get();

        $absensi = AttendanceHistory::with(['student', 'class'])
            ->where('id_class', $classId)
            ->get();

        $result = [];
        foreach ($students as $student) {
            $absensiItem = $absensi->where('id_student', $student->id)->first();
            $result[] = (object)[
                'id'         => $absensiItem ? $absensiItem->id : null,
                'student'    => $student,
                'class'      => $kelas,
                'status'     => $absensiItem ? $absensiItem->status : 'in progress',
                'created_at' => $absensiItem ? $absensiItem->created_at : null,
            ];
        }

        return view('pages.absensi.absensi_mapel', [
            'absensi' => collect($result),
            'kelas'   => $kelas,
        ]);
    }

    public function editStatus(Request $request, $id)
    {
        $absensi = AttendanceHistory::findOrFail($id);
        $request->validate([
            'status' => 'required|string'
        ]);
        $absensi->status = $request->status;
        $absensi->save();

        return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
    }
}
