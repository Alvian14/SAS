<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\Classes;
use App\Models\Notification;
use App\Services\FirebaseMessagingService;
use App\Services\NotificationHelperService;
use Illuminate\Http\Request;

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

            $classId = $request->input('class_id'); // contoh: '123'
            $title = $request->input('title', 'Judul Notifikasi');
            $body  = $request->input('body', 'Isi notifikasi');
            $data = $request->input('data', []);

            // get fcm topic from database based on class_id
            $topic = Classes::find($classId)->fcm_topic;

            if (!$topic) {
                return response()->json(['error' => 'Class not found or no FCM topic assigned'], 404);
            }

            // save to database
            $response = Notification::create([
                'title' => $title,
                'body' => $body,
                'type' => 'class_notification',
                'send_to' => $topic,
                'sender_id' => $userId,
                'receiver_id' => null, // bisa diisi jika ingin target user tertentu
                'class_id' => $classId,
            ]);

            if (!$response) {
                return response()->json(['error' => 'Failed to save notification to database'], 500);
            }

            // send as notification to devices subscribed to the class topic
            $this->fcm->sendToTopic($topic, $title, $body, $data);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to class topic successfully',
                'data' => [
                    'class_id' => $classId,
                    'sender_id' => $userId,
                    'topic' => $topic,
                    'title' => $title,
                    'body' => $body,
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
