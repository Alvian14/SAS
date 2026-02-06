<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicPeriod;

class PeriodeController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::orderByDesc('start_date')->get();
        return view('pages.periode.periode', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|string|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Ubah hanya huruf pertama menjadi kapital (Ganjil/Genap)
        $semester = ucfirst(strtolower($request->semester));
        if ($semester === 'Ganjil') {
            $semester = 'Ganjil';
        } elseif ($semester === 'Genap') {
            $semester = 'Genap';
        }

        $name = $semester . ' ' . $request->tahun_ajaran;

        AcademicPeriod::create([
            'name' => $name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => false,
        ]);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function activate($id)
    {
        // Nonaktifkan semua periode
        AcademicPeriod::query()->update(['is_active' => false]);
        // Aktifkan periode yang dipilih
        AcademicPeriod::where('id', $id)->update(['is_active' => true]);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diaktifkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|string|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $semester = ucfirst(strtolower($request->semester));
        $name = $semester . ' ' . $request->tahun_ajaran;

        $period = AcademicPeriod::findOrFail($id);
        $period->name = $name;
        $period->start_date = $request->start_date;
        $period->end_date = $request->end_date;

        // Jika is_active dicentang, aktifkan hanya periode ini
        if ($request->has('is_active')) {
            AcademicPeriod::query()->update(['is_active' => false]);
            $period->is_active = true;
        } else {
            $period->is_active = false;
        }

        $period->save();

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diupdate.');
    }
}
