<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicPeriod;

class JadwalController extends Controller
{
    public function index()
    {
           $kelas = Classes::all();
           $mapel = Subject::all();
           $guru = Teacher::all();
           $periodes = AcademicPeriod::orderBy('start_date', 'desc')->get();
           $periode_aktif = AcademicPeriod::where('is_active', true)->first();
           $jadwal = Schedule::with(['class', 'subject', 'teacher'])->get();
           return view('pages.jadwal.jadwal', compact('kelas', 'mapel', 'guru', 'periodes', 'periode_aktif', 'jadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string',
            'period_start' => 'required|integer',
            'period_end' => 'required|integer',
            'code' => 'required|string',
            'id_class' => 'required|exists:clases,id',
            'id_subject' => 'required|exists:subjects,id',
            'id_teacher' => 'required|exists:teachers,id',
            'id_academic_periods' => 'nullable|exists:academic_periods,id',
        ]);

        // Hitung jam mulai dan jam selesai
        $startHour = 7 + ($validated['period_start'] - 1);
        $endHour = 7 + ($validated['period_end'] - 1);
        $start_time = sprintf('%02d:00', $startHour);
        $end_time = sprintf('%02d:00', $endHour);

        Schedule::create([
            'day_of_week' => $validated['day_of_week'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'code' => $validated['code'],
            'id_class' => $validated['id_class'],
            'id_subject' => $validated['id_subject'],
            'id_teacher' => $validated['id_teacher'],
            'id_academic_periods' => $validated['id_academic_periods'] ?? null,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

     public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string',
            'period_start' => 'required|integer',
            'period_end' => 'required|integer',
            'code' => 'required|string',
            'id_class' => 'required|exists:clases,id',
            'id_subject' => 'required|exists:subjects,id',
            'id_teacher' => 'required|exists:teachers,id',
            'id_academic_periods' => 'nullable|exists:academic_periods,id',
        ]);

        $startHour = 7 + ($validated['period_start'] - 1);
        $endHour = 7 + ($validated['period_end'] - 1);
        $start_time = sprintf('%02d:00', $startHour);
        $end_time = sprintf('%02d:00', $endHour);

        $jadwal = Schedule::findOrFail($id);
        $jadwal->update([
            'day_of_week' => $validated['day_of_week'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'code' => $validated['code'],
            'id_class' => $validated['id_class'],
            'id_subject' => $validated['id_subject'],
            'id_teacher' => $validated['id_teacher'],
            'id_academic_periods' => $validated['id_academic_periods'] ?? null,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diupdate!');
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids) {
            return redirect()->route('jadwal.index')->with('error', 'Tidak ada jadwal yang dipilih untuk dihapus.');
        }
        // Ubah string menjadi array jika perlu
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        Schedule::destroy($ids);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}
