<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Notification;
use App\Services\FirebaseMessagingService;
use App\Services\NotificationHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ClassController extends Controller
{

    protected FirebaseMessagingService $fcm;
    protected NotificationHelperService $notificationHelper;

    // Ambil semua data kelas
    public function index()
    {
        return response()->json([
            'success' => true, 
            'message' => 'Student classes fetched successfully', 
            'data' => Classes::all()],
        200);
    }

    public function __construct(FirebaseMessagingService $fcm, NotificationHelperService $notificationHelper)
    {
        $this->fcm = $fcm;
        $this->notificationHelper = $notificationHelper;
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

    // get schedule for a specific class
    public function schedule(Request $request, $id)
    {
        // check Authorization header for Bearer token
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }
        $schedules = $class->schedules()->whereHas('academicPeriods', function ($q) {
            $q->where('is_active', 1);
        })->with(['subject', 'teacher'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Class schedules fetched successfully',
            'data' => $schedules
        ], 200);
    }
    
    // get schedule for a specific class for a given day index (1=Senin,..7=Minggu)
    public function scheduleByDay(Request $request, $id, $dayindex)
    {
        // same auth check as schedule()
        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !Str::startsWith($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim(Str::after($authHeader, 'Bearer'));
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $class = Classes::find($id);
        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $dayIndex = (int) $dayindex;
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $dayName = $dayNames[$dayIndex] ?? null;

        $query = $class->schedules()->whereHas('academicPeriods', function ($q) {
            $q->where('is_active', 1);
        });

        if ($dayName) {
            $query->where(function ($q) use ($dayIndex, $dayName) {
                $q->where('day_of_week', $dayName)->orWhere('day_of_week', (string) $dayIndex);
            });
        } else {
            $query->where('day_of_week', (string) $dayIndex);
        }

        $schedules = $query->with(['subject', 'teacher'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Class schedules fetched successfully for day',
            'day_index' => $dayIndex,
            'data' => $schedules
        ], 200);
    }
    
    // teacher only
    public function studentClassInformation($id)
    {
        $class = Classes::where('id', $id)->with(['students'])->first();

        if (!$class) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Class information fetched successfully',
            'data' => $class,
        ], 200);
    }

    // send to class topic (announcement, homework, assignment, etc)
    public function sendAnnouncementToClass(Request $request)
    {
        try {

            // user auth check
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = $user->id;

            $request->validate([
                'class_id' => 'required|integer',
                'type' => 'nullable|string',
                'title' => 'nullable|string',
                'body' => 'nullable|string',
                'data' => 'nullable',
            ]);

            $type = $request->input('type', 'announcement'); // default ke announcement

            $classId = $request->input('class_id'); // contoh: '123'
            $title = $request->input('title', null);
            $body  = $request->input('body', null);
            $data = $request->input('data', []);

            // get fcm topic from database based on class_id
            $class = Classes::find($classId);
            if (!$class) {
                return response()->json(['error' => 'Class not found'], 404);
            }

            $topic = $class->fcm_topic;
            if (!$topic) {
                return response()->json(['error' => 'No FCM topic assigned to this class'], 404);
            }

            $template = null;

            switch ($type) {
                case 'assignment':
                    $t = $title;
                    $b = $body  ?? 'Ada tugas baru untuk kelas ini. Silakan cek aplikasi untuk detail.';
                    $template = $this->notificationHelper->templateAssignmentForClass(
                        $topic,
                        $t,
                        $b,
                        $data,
                    );
                    break;

                case 'class_cancelled':
                    // contoh template untuk pengumuman kelas dibatalkan
                    $t = $title ?? 'Kelas Dibatalkan';
                    $b = $body ?? '(pengirim tidak memberikan detail alasan pembatalan)';
                    $template = $this->notificationHelper->templateClassCancelled(
                        $b
                    );
                    break;

                default:
                    // default ke template pengumuman umum untuk kelas
                    $t = $title ?? 'Pengumuman Kelas';
                    $b = $body  ?? 'Ada pengumuman untuk kelas ini.';
                    $template = $this->notificationHelper->templateAnnouncementForClass(
                        $topic,
                        $t,
                        $b,
                        array_merge($data, ['type' => 'announcement_for_class']),
                    );
                    break;
            }

            if (!$template) {
                return response()->json(['error' => 'Failed to create notification template'], 500);
            }

            $resultMessaging = $this->notificationHelper->send($template);

            $createTemplate = $this->notificationHelper->createTemplateForClass(
                $classId,
                $template->title,
                $template->body,
                $type,
                $userId,
            );

            // store to database
            Notification::create($createTemplate->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to class topic successfully',
                'data' => [
                    'messaging' => $resultMessaging,
                    'create_template' => $createTemplate->toArray(),
                ]
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'error' => 'Failed to send notification',
                'message' => $th->getMessage()], 500);
        }

        }

}
