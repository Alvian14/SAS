<?php

namespace App\Http\Controllers\api;

use App\Models\Classes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    // Ambil semua data kelas
    public function index()
    {
        return response()->json([
            'success' => true, 
            'message' => 'Student classes fetched successfully', 
            'data' => Classes::all()],
        200);
    }

    // Ambil detail kelas berdasarkan id
    public function show($id)
    {
        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }
        return response()->json($class, 200);
    }
}
