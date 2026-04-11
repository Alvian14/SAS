<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\AttendanceHistory;
use App\Models\Notification;
use Illuminate\Http\Request;

use App\Models\Permission;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\NotificationHelperService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
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
        $permissions = Permission::all();
        return view('pages.perizinan.perizinan', compact('permissions'));
    }

    public function permissionAccept(Request $request, int $id): RedirectResponse
    {
        try {
            // session-auth user for web controller
            $user = $request->user();
            if (!$user) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            // find permission by id
            $permission = Permission::find($id);
            if (!$permission) {
                return redirect()->back()->with('error', 'Data perizinan tidak ditemukan');
            }

            // permission must be processed only, if already accepted or rejected, cannot be processed again.
            if ($permission->status === 'diterima') {
                return redirect()->back()->with('warning', 'Perizinan sudah diterima sebelumnya');
            }
            if ($permission->status === 'ditolak') {
                return redirect()->back()->with('warning', 'Perizinan sudah ditolak sebelumnya');
            }

            $student = Student::find($permission->id_student);
            if (!$student) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
            }

            DB::transaction(function () use ($permission, $user, $request, $student) {
                // update status to accepted
                $permission->status = 'diterima';
                $permission->approved_by = $user->id; // set approved by teacher id
                $permission->feedback = $request->input('feedback', null); // optional feedback message from teacher
                $permission->save();

                // * create attendance record history to related date (only fill with schedule that has active academic period, and match with date permission).
                $permissionReason = $permission->reason; // contoh: 'sakit', 'izin', 'dispen', 'lainnya'
                if ($permissionReason === 'lainnya') {
                    $permissionReason = 'izin';
                }

                $permissionDate = Carbon::parse($permission->date_permission)->startOfDay();
                $permissionTimePeriod = (int) $permission->time_period;
                $createdSchoolDays = 0;
                $currentDate = $permissionDate->copy();

                // Count time_period as school days; weekend days are skipped and replaced by the next weekday.
                while ($createdSchoolDays < $permissionTimePeriod) {
                    if ($currentDate->isWeekend()) {
                        $currentDate->addDay();
                        continue;
                    }

                    $dayName = strtolower($currentDate->locale('id')->dayName);
                    $dayIndex = $currentDate->dayOfWeekIso; // 1..7

                    // search related schedule with day_of_week same and active academic period
                    $schedules = Schedule::query()
                        ->where('id_class', $student->id_class)
                        ->whereHas('academicPeriods', function ($q) {
                            $q->where('is_active', 1);
                        })
                        ->where(function ($q) use ($dayName, $dayIndex) {
                            $q->where('day_of_week', $dayName)
                                ->orWhere('day_of_week', (string) $dayIndex);
                        })
                        ->get();

                    foreach ($schedules as $schedule) {
                        AttendanceHistory::updateOrCreate(
                            [
                                'id_schedule' => $schedule->id,
                                'id_student' => $student->id,
                                'attendance_date' => $currentDate->toDateString(),
                            ],
                            [
                                'id_class' => $schedule->id_class,
                                'period_number' => $schedule->period_start,
                                'coordinate' => "$this->latOfAttendance, $this->lonOfAttendance",
                                'status' => $permissionReason,
                            ]
                        );
                    }

                    $createdSchoolDays++;
                    $currentDate->addDay();
                }
            });

            // notification zone: send notification to related student after permission approved
            $userStudent = $student->user;
            if (!$userStudent) {
                return redirect()->back()->with('error', 'User siswa tidak ditemukan untuk notifikasi');
            }

            $fcmToken = $userStudent->device_token;
            $title = 'Perizinan Disetujui';
            $body = 'Permohonan izin Anda telah disetujui.';

            // Push notification is optional; database notification must still be created.
            if (!empty($fcmToken)) {
                try {
                    $template = $this->notificationHelper->templatePermissionApproval($fcmToken);
                    $this->notificationHelper->send($template);
                    $title = $template->title;
                    $body = $template->body;
                } catch (\Throwable $th) {
                    // Ignore push errors so approval flow and DB notification keep working.
                }
            }

            $createTemplate = $this->notificationHelper->createTemplateForStudent(
                (int) $userStudent->id,
                $title,
                $body,
                NotificationType::Permission,
                (int) $user->id
            );

            Notification::create($createTemplate->toArray());

            return redirect()->back()->with('success', 'Izin diterima untuk siswa ' . $student->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function permissionReject(Request $request, int $id): RedirectResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            $permission = Permission::find($id);
            if (!$permission) {
                return redirect()->back()->with('error', 'Data perizinan tidak ditemukan');
            }

            if ($permission->status === 'diterima') {
                return redirect()->back()->with('warning', 'Perizinan sudah diterima, tidak bisa ditolak');
            }

            if ($permission->status === 'ditolak') {
                return redirect()->back()->with('warning', 'Perizinan sudah ditolak sebelumnya');
            }

            $permission->status = 'ditolak';
            $permission->approved_by = $user->id;
            $permission->feedback = $request->input('feedback', $permission->feedback);
            $permission->save();

            return redirect()->back()->with('success', 'Perizinan berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
