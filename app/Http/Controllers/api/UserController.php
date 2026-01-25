<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
            
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('teacher', 'student')
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
                    'picture' => $request->picture ?? null,
                ]);
            }

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

}
