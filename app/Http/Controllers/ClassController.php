<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::all();
        return view('pages.kelas.kelas_absensi', compact('classes'));
    }

    // Proses tambah kelas
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'grade' => 'required|integer',
            'code'  => 'required|string|max:50',
        ]);

        Classes::create([
            'name'  => $request->name,
            'major' => $request->major,
            'grade' => $request->grade,
            'code'  => $request->code,
        ]);

        return redirect()->route('kelas.kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $kelas = Classes::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas.kelas')->with('success', 'Kelas berhasil dihapus.');
    }
}
