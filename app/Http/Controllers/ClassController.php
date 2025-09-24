<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::all();
        return view('pages.kelas.kelas', compact('classes'));
    }

    // Proses tambah kelas
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'grade' => 'required|integer',
            'code'  => 'required|string|max:50|unique:clases,code',
        ]);

        Classes::create([
            'name'  => $request->name,
            'major' => $request->major,
            'grade' => $request->grade,
            'code'  => $request->code,
        ]);

        return redirect()->route('kelas.kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }
}
