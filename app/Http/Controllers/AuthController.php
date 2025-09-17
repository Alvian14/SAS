<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function registerStudent(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'nisn' => 'required|string|max:50|unique:students,nisn',
                'id_class' => 'required|integer',
                'entry_year' => 'required|integer',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Proses upload gambar jika ada
            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = $image->hashName();
                $image->move(public_path('storage/student/'), $imageName);
            }

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'profile_picture' => $imageName,
            ]);

            $user->student()->create([
                'id_user' => $user->id,
                'name' => $request->name,
                'nisn' => $request->nisn,
                'id_class' => $request->id_class,
                'entry_year' => $request->entry_year,
                'picture' => $imageName,
            ]);

            return redirect()->route('login')->with('success', 'Registrasi siswa berhasil. Silakan login.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi siswa gagal. Silakan coba lagi.')->withInput();
        }
    }

    public function registerTeacher(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'nip' => 'required|string|max:50|unique:teachers,nip',
                'subject' => 'required|string|max:100',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Proses upload gambar jika ada
            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = $image->hashName();
                $image->move(public_path('storage/teacher/'), $imageName);
            }

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'profile_picture' => $imageName,
            ]);

            $user->teacher()->create([
                'id_user' => $user->id,
                'name' => $request->name,
                'nip' => $request->nip,
                'subject' => $request->subject,
            ]);

            return redirect()->route('login')->with('success', 'Registrasi guru berhasil. Silakan login.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi guru gagal. Silakan coba lagi.')->withInput();
        }
    }



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}



