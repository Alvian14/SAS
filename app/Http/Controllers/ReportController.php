<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\AttendanceHistory;
use App\Models\Student;
use App\Models\Notification;
use App\Enums\NotificationType;
use App\Services\NotificationHelperService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    protected NotificationHelperService $notificationHelper;
    public $latOfAttendance = -7.7811912;
    public $lonOfAttendance = 112.0315286;

    public function __construct(NotificationHelperService $notificationHelper)
    {
        $this->notificationHelper = $notificationHelper;
    }

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

            // notification zone: send notification to related student after status updated
            $student = Student::find($report->id_student);
            if ($student) {
                $userStudent = $student->user;
                if ($userStudent) {
                    $fcmToken = $userStudent->device_token;
                    $title = 'Laporan Diperbarui';
                    $body = 'Status laporan Anda telah diperbarui menjadi ' . ucfirst($status) . '.';

                    // Push notification is optional; database notification must still be created.
                    if (!empty($fcmToken)) {
                        try {
                             $template = $this->notificationHelper->templateAttendanceViolation(
                                $fcmToken,
                                $request->input('description', null) // optional custom message from student about the disrepancy
                            );
                            $notificationResult = $this->notificationHelper->send($template);
                            $createTemplate = $this->notificationHelper->createTemplateForStudent(
                                (int) $userStudent->id,
                                $template->title,
                                $template->body,
                                NotificationType::AttendanceViolation,
                                // user id of system or admin, convert to int. (for example: 0)
                                (int) $user->id
                            );

                            Notification::create($createTemplate->toArray());
                        } catch (\Throwable $th) {
                            // Ignore push errors so update flow and DB notification keep working.
                        }
                    }

                    $createTemplate = $this->notificationHelper->createTemplateForStudent(
                        (int) $userStudent->id,
                        $title,
                        $body,
                        NotificationType::AttendanceViolation,
                        (int) $user->id
                    );

                    Notification::create($createTemplate->toArray());
                }
            }

            $message = 'Status laporan berhasil diperbarui menjadi ' . ucfirst($status);
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
