<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{

    public function index ()
    {
        $subjects = Subject::all();
        return view('pages.mapel.mapel', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:umum,jurusan',
            'code' => 'required|string|max:50|unique:subjects,code',
        ]);

        Subject::create([
            'name' => $request->name,
            'type' => $request->type,
            'code' => $request->code,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:umum,jurusan',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update([
            'name' => $request->name,
            'type' => $request->type,
            'code' => $request->code,
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil diupdate.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
