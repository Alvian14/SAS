<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\Classes;
use App\Models\Notification;
use App\Services\FirebaseMessagingService;
use App\Services\NotificationHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    //* Dokumentasi Penggunaan Template Message untuk fitur Notifikasi
        //* Contoh penggunaan template untuk kirim ke topic kelas
    // $template = $this->notificationHelper->templateTugasKelas(
    //     'kelas_10_TKJ_1',
    //     'Tugas Baru: Matematika',
    //     'Tugas matematika untuk minggu ini sudah tersedia. Silakan cek aplikasi untuk detailnya.',
    // );
    // $result = $this->notificationHelper->send($template);

        // * Contoh penggunaan template untuk kirim ke semua siswa (pengumuman umum)
    // $template = $this->notificationHelper->templatePengumuman(
    //     'Libur Sekolah',
    //     'Sekolah akan libur pada tanggal 25 Desember 2024 hingga 1 Januari 2025. Selamat berlibur!',
    // );
    // $result = $this->notificationHelper->send($template);

        // * Contoh penggunaan template untuk kirim ke satu device (token)
    // $template = $this->notificationHelper->tokenTemplate(
    //     'fcm_token_device',
    //     'Pengumuman Pribadi',
    //     'Ini adalah pengumuman khusus untuk Anda. Harap cek aplikasi untuk detailnya.',
    // );
    // $result = $this->notificationHelper->send($template);


    //* Dokumentasi Penggunaan Data NotficationCreateTemplate untuk menyimpan ke database

    // $payloadAll = $this->notificationHelper->createTemplateForAll(
    // 'Info Umum',
    // 'Sekolah libur besok',
    // NotificationType::AnnouncementGeneral,
    // auth()->id(),
    // );

    // $payloadClass = $this->notificationHelper->createTemplateForClass(
    //     10,
    //     'Tugas Baru',
    //     'Kerjakan latihan halaman 12',
    //     NotificationType::Assignment,
    //     auth()->id(),
    // );

    // $payloadStudent = $this->notificationHelper->createTemplateForStudent(
    //     25,
    //     'Catatan Personal',
    //     'Harap hubungi wali kelas',
    //     NotificationType::PersonalNote,
    //     auth()->id(),
    // );

   public function index()
    {
        $notifications = Notification::all();
        return view('pages.notifikasi.notifikasi', compact('notifications'));
    }

    protected FirebaseMessagingService $fcm;
    protected NotificationHelperService $notificationHelper;

    public function __construct(FirebaseMessagingService $fcm, NotificationHelperService $notificationHelper)
    {
        $this->fcm = $fcm;
        $this->notificationHelper = $notificationHelper;
    }

    // Test kirim ke satu token
    public function sendToToken(Request $request)
    {
        $token = $request->input('token'); // FCM token dari mobile
        $title = $request->input('title', 'Judul Notifikasi');
        $body  = $request->input('body', 'Isi notifikasi');

        $data = $request->input('data', []); // optional payload

        $this->fcm->sendToToken($token, $title, $body, $data);

        return response()->json(['success' => true]);
    }

    // Kirim ke topic
    public function sendToTopic(Request $request)
    {
        $topic = $request->input('topic'); // contoh: 'session_123'
        $title = $request->input('title', 'Judul Notifikasi');
        $body  = $request->input('body', 'Isi notifikasi');

        $data = $request->input('data', []);

        $this->fcm->sendToTopic($topic, $title, $body, $data);

        return response()->json(['success' => true]);
    }

    // Contoh penggunaan template notifikasi untuk target topic kelas
    public function sendTopicTemplateExample(Request $request)
    {
        try {

            $template = $this->notificationHelper->templateAssignmentForClass(
                $request->input('topic', 'kelas_10_TKJ_1'),
                $request->input('title', 'Tugas Baru: Matematika'),
                $request->input('body', 'Tugas matematika untuk minggu ini silahkan kerjakan buku praktikum halaman 12-15'),
            );

            $result = $this->notificationHelper->send($template);

            $createTemplate = $this->notificationHelper->createTemplateForClass(
                1, // class_id, for testing only
                $template->title,
                $template->body,
                NotificationType::Assignment,
                1, // sender_id, bisa diganti dengan auth()->id() jika sudah ada sistem autentikasi
            );

            // store to database
            Notification::create($createTemplate->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Topic template notification sent successfully',
                'template' => $template->toArray(),
                'create_template' => $createTemplate->toArray(),
                'result' => $result,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send topic template notification',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // notes untuk alvian:
    // $template = $this->notificationHelper->templatePermissionApproval(
    //     $request->input('userid'), // receiver_id, contoh: user id siswa yang izin nya disetujui
    //     // $request->input('title', 'Tugas Baru: Matematika'),
    // );


    // $result = $this->notificationHelper->send($template);

    // $createTemplate = $this->notificationHelper->createTemplateForStudent(
    //     $request->input('userid'), // receiver_id, contoh: user id siswa yang izin nya disetujui
    //     $template->title,
    //     $template->body,
    //     NotificationType::PersonalNote,
    //     1, // sender_id, bisa diganti dengan auth()->id() jika sudah ada sistem autentikasi
    //     );

    // // store to database
    // Notification::create($createTemplate->toArray());


    // universal send (kirim ke semua student device, untuk pengumuman umum seperti libur, maintenance, dll)
    public function sendToAll(Request $request)
    {
        $title = $request->input('title', 'Judul Notifikasi');
        $body  = $request->input('body', 'Isi notifikasi');

        $data = $request->input('data', []);

        // Kirim ke topic global untuk semua siswa
        $this->fcm->sendToTopic('student_notifications', $title, $body, $data);

        return response()->json(['success' => true]);
    }

    // send to class topic (announcement, homework, assignment, etc)
    public function sendToClassTopic(Request $request)
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
                        $topic,
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


//         public function sendWa()
// {
//     $adminPhone = '6281559769075';

//     // Ambil semua data notifikasi
//     $notifications = Notification::latest()->take(1)->get();

//     foreach ($notifications as $notif) {
//         try {
//             Http::withHeaders([
//                 'Authorization' => 'n8wkFAyiDVZQNs6Vydwt',
//             ])->post('https://api.fonnte.com/send', [
//                 'phone' => $adminPhone,
//                 'message' =>
//                     "🔔 NOTIFIKASI BARU\n\n" .
//                     "Judul: {$notif->title}\n" .
//                     "Pesan: {$notif->body}",
//             ]);
//         } catch (\Exception $e) {
//             Log::error($e->getMessage());
//         }
//     }

//     return response()->json([
//         'message' => 'Notifikasi berhasil dikirim'
//     ]);
// }

// public function store(Request $request)
// {
//     Notification::create([
//         'title' => $request->title,
//         'body' => $request->body,
//         'sender_id' => Auth::id()
//     ]);

//     return back()->with('success', 'Notifikasi dibuat');
// }
    public function store(Request $request)
    {
        Notification::create([
            'title' => 'Test',
            'body' => 'Test body',
            'sender_id' => Auth::id()
        ]);

        return back()->with('success', 'Notifikasi dibuat');
    }

    public function destroy(int $id)
    {
        try {
            // Auth user
            $user = request()->user();
            if (!$user) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            // Find notification by id
            $notification = Notification::find($id);
            if (!$notification) {
                return redirect()->back()->with('error', 'Notifikasi tidak ditemukan');
            }

            // Delete notification
            $notification->delete();

            return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
