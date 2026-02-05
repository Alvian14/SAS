<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicPeriod;

class PeriodeController extends Controller
{
    public function index()
    {
        $periods = \App\Models\AcademicPeriod::orderByDesc('start_date')->get();
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
}
