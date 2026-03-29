<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Notification;
use App\Services\FirebaseMessagingService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected FirebaseMessagingService $fcm;

    public function __construct(FirebaseMessagingService $fcm)
    {
        $this->fcm = $fcm;
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