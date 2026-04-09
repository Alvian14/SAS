<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicPeriod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;

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
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'code' => 'nullable|string',
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

        // Ambil data untuk kode otomatis
        $class = Classes::find($validated['id_class']);
        $subject = Subject::find($validated['id_subject']);
        $period = $validated['id_academic_periods'] ?? '';
        $token = (string) random_int(1000, 9999);

        $day = strtoupper(substr($validated['day_of_week'], 0, 3));
        $className = $class ? $class->name : '';
        $subjectCode = $subject ? $subject->code : '';
        $periodCode = $period ?: '0';

        $autoCode = "{$className}-{$day}-{$validated['period_start']}-{$validated['period_end']}-{$subjectCode}-{$periodCode}-{$token}";

        $code = $request->input('code') ?: $autoCode;

        // Hash code sebelum simpan ke database
        $hashedCode = Crypt::encryptString($code);

        Schedule::create([
            // make day of week to lowercase
            'day_of_week' => strtolower($validated['day_of_week']),
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'code' => $hashedCode,
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
            'start_time' => 'required|string',
            'end_time' => 'required|string',
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

        // Hash code sebelum update ke database
        $hashedCode = Crypt::encryptString($validated['code']);

        $jadwal = Schedule::findOrFail($id);
        $jadwal->update([
            'day_of_week' => strtolower($validated['day_of_week']),
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'code' => $hashedCode,
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

    public function showQr($id)
    {
        $jadwal = \App\Models\Schedule::findOrFail($id);
        return view('pages.jadwal.qr', compact('jadwal'));
    }
}
