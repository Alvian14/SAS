<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('teacher', 'student')
        ]);
    }
    
    public function register(Request $request)
    {
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
                'nisn' => 'required|string|max:50',
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('teacher', 'student')
        ], 201);
    }

}
