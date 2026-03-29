<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseMessagingService;

class NotificationController extends Controller
{

    public function index()
    {
        return view('pages.notifikasi.notifikasi');
    }

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
}
