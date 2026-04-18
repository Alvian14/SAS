<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function storeFcmToken(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
            // 'platform'  => 'nullable|string',
            // device_id DIHAPUS
        ]);

        $user = $request->user();

        $user->update([
            'device_token' => $validated['fcm_token'],
            // 'platform'  => $validated['platform'] ?? 'android',
            // device_id tidak dipakai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token stored successfully',
        ]);
    }

    public function getTopicFromClass(Request $request, $classId)
    {
        // request check Authorization header for Bearer token
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $class = Classes::find($classId);

        if (!$class) {
            return response()->json(['success' => false, 'message' => 'Class not found'], 404);
        }

        if (!$class->fcm_topic) {
            return response()->json(['success' => false, 'message' => 'Class does not have an FCM topic'], 400);
        }

        // Logic untuk subscribe ke topic bisa ditambahkan di sini
        // Misalnya simpan di database atau langsung panggil service FCM
        return response()->json([
            'success' => true,
            'message' => "Subscribed to class topic: {$class->fcm_topic}",
            'data' => [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'fcm_topic' => $class->fcm_topic,
            ]
        ], 200);

    }
    
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // check if user is banned

            // check if user id / token has set or not, so user just login in one device only
            
            $token = $user->createToken('auth_token')->plainTextToken;
            $this->syncTopicSubscribe($user);

            $userData = $user->load('teacher', 'student.class');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $userData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage() // bisa dihapus di production untuk keamanan
        ], 500);
        }
    }
    
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            //code...
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:teacher,student',
            ]);
    
            // buat akun user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'profile_picture' => $request->profile_picture ?? null,
            ]);
    
            // buat record sesuai role
            if ($user->role == 'teacher') {
                $request->validate([
                    'nip' => 'required|string|max:50|unique:teachers,nip',
                    'subject' => 'required|string|max:100',
                ]);
    
                $user->teacher()->create([
                    'id_user' => $user->id,
                    'name' => $request->name,
                    'nip' => $request->nip,
                    'subject' => $request->subject,
                ]);
            } else if ($user->role == 'student') {
                
                $request->validate([
                    'nisn' => 'required|string|max:50|unique:students,nisn',
                    'id_class' => 'required|integer',
                    'entry_year' => 'required|integer',
                ]);
                
                $user->student()->create([
                    'id_user' => $user->id,
                    'name' => $request->name,
                    'nisn' => $request->nisn,
                    'id_class' => $request->id_class,
                    'entry_year' => $request->entry_year,
                    'pictures' => $request->pictures ?? null,
                ]);
            }

            $this->syncTopicSubscribe($user);

            DB::commit();
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'success' => true,
                'message' => 'Register success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('teacher', 'student')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors(), // detail error validasi
        ], 422);
        } catch (\Exception $e) {
        DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(), // optional, bisa di-hide kalau production
            ], 500);
        }
    }

    public function feedback(Request $request)
    {
        return response()->json([
            'message' => 'Halo student, ini feedback testing!',
            'user'    => $request->user()->only(['id', 'name', 'email', 'role']),
        ]);
    }

    private function syncTopicSubscribe(User $user): ?string
    {
        $user->loadMissing('student.class');

        if (!$user->student) {
            return null;
        }

        $topics = ['student_notifications'];

        if ($topic = $user->student->class?->fcm_topic) {
            $topics[] = $topic;
        }

        $topics = array_unique(array_filter($topics));

        if (empty($topics)) {
            return null;
        }

        $topicSubscribe = implode(', ', $topics);

        if ($user->topic_subscribe !== $topicSubscribe) {
            $user->update(['topic_subscribe' => $topicSubscribe]);
        }

        $user->topic_subscribe = $topicSubscribe;

        return $topicSubscribe;
    }

    // get student notification list data from notification table.
    // note filter: user_id = auth user id, or his class id.
    public function getStudentNotifications(Request $request)
    {
        $user = $request->user();
        
        $student = $user->student;
        
        if (!$user || !$student) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // validate query parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $classId = $user->student->id_class;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // filter by class id and date range
        $notifications = DB::table('notifications')
            ->where(function ($query) use ($user, $classId) {
                $query->where('receiver_id', $user->id)
                      ->orWhere('class_id', $classId)
                      ->orWhere(function ($q) {
                          $q->whereNull('receiver_id')
                            ->whereNull('class_id');
                      });
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

            
        return response()->json([
            'success' => true,
            'message' => 'Notifications fetched successfully',
            'data' => $notifications,
        ], 200);
    }

}
