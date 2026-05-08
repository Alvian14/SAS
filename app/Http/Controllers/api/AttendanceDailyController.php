<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceDailyController extends Controller
{
    public function uploadPicture(Request $request)
    {
        $storagePath = $request->input('storage_path', 'daily-attendance');

        // Accept both 'image' (from Flask) and 'file' (from other clients)
        $file = $request->file('image') ?? $request->file('file');

        if (! $file || ! $file->isValid()) {
            return response()->json(["success" => false, "message" => "No valid file uploaded"], 422);
        }

        $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = Storage::disk('public')->putFileAs(
            $storagePath,
            $file,
            $filename
        );

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }
}
