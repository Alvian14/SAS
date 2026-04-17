<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\AttendanceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::all();
        return view('pages.laporkan.laporkan', compact('reports'));
    }

    public function updateStatus(Request $request, int $id)
    {
        try {
            // Auth user
            $user = $request->user();
            if (!$user) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            // Find report by id
            $report = Report::find($id);
            if (!$report) {
                return redirect()->back()->with('error', 'Data laporan tidak ditemukan');
            }

            // Validate status
            $validStatuses = ['hadir', 'izin', 'sakit', 'alpha', 'dispen', 'invalid'];
            $status = strtolower($request->input('status'));
            if (!in_array($status, $validStatuses)) {
                return redirect()->back()->with('error', 'Status tidak valid');
            }

            // Update attendance history
            if ($report->id_attendance_history) {
                AttendanceHistory::where('id', $report->id_attendance_history)->update(['status' => $status]);
            }

            // Update report marked_as_resolved = 1 
            Report::where('id', $id)->update([
                'marked_as_resolved' => 1
            ]);

            $message = 'Status laporan berhasil diperbarui menjadi ' . ucfirst($status);
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
