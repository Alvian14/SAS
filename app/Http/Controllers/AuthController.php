<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Teacher;
use Faker\Provider\Image as ProviderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;

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

     public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // start register student
    public function registerStudent(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                // password wajib diisi dari form, tidak perlu default 'starbaks' dibackend
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

            // Redirect kembali ke identitas siswa dengan pesan sukses
            return redirect()->route('akun.indentitas_siswa')->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi siswa gagal. Silakan coba lagi.')->withInput();
        }
    }



    public function identitasSiswa()
    {
        $students = Student::with(['user', 'class'])->get();
        $classes = Classes::all();
        return view('pages.akun.indentitas_siswa', compact('students', 'classes'));
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        // Hapus user terkait (akan otomatis hapus student jika foreign key cascade)
        if ($student->user) {
            $student->user->delete();
        } else {
            $student->delete();
        }
        return redirect()->route('akun.indentitas_siswa')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function updateStudent(Request $request, $id)
    {
        try {
            // Jika massal (hanya update kelas)
            if ($request->has('id_class') && !$request->has('name')) {
                $student = Student::findOrFail($id);
                $student->id_class = $request->id_class;
                $student->save();
                return redirect()->route('akun.indentitas_siswa')->with('success', 'Kelas siswa berhasil diupdate.');
            }

            // Update lengkap (dari modal edit)
            $student = Student::findOrFail($id);
            $user = $student->user;

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'nisn' => 'required|string|max:50|unique:students,nisn,' . $student->id,
                'id_class' => 'required|integer',
                'entry_year' => 'required|integer',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->email = $request->email;
            if ($request->hasFile('profile_picture')) {
                // Hapus foto lama jika ada
                if ($user->profile_picture) {
                    $oldPath = public_path('storage/student/' . $user->profile_picture);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $image = $request->file('profile_picture');
                $imageName = $image->hashName();
                $image->move(public_path('storage/student/'), $imageName);
                $user->profile_picture = $imageName;
            }
            $user->save();

            $student->name = $request->name;
            $student->nisn = $request->nisn;
            $student->id_class = $request->id_class;
            $student->entry_year = $request->entry_year;
            $student->save();

            return redirect()->route('akun.indentitas_siswa')->with('success', 'Data siswa berhasil diupdate.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update siswa gagal. Silakan coba lagi.')->withInput();
        }
    }

    public function updateStudentClass(Request $request, $id)
    {
        $request->validate([
            'id_class' => 'required|integer',
        ]);

        $student = Student::findOrFail($id);
        $student->id_class = $request->id_class;
        $student->save();

        return redirect()->route('akun.indentitas_siswa')->with('success', 'Kelas siswa berhasil diupdate.');
    }

    // end register student


    // start register teacher
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

            // Redirect ke identitas guru
            return redirect()->route('akun.indentitas_guru')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi guru gagal. Silakan coba lagi.')->withInput();
        }
    }

    public function identitasGuru()
    {
        $teachers = Teacher::with('user')->get();
        return view('pages.akun.indentitas_guru', compact('teachers'));
    }

    // Update teacher


    public function updateTeacher(Request $request, $id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'nip' => 'required|string|max:50|unique:teachers,nip,' . $teacher->id,
                'subject' => 'required|string|max:100',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->email = $request->email;
            if ($request->hasFile('profile_picture')) {
                // Hapus foto lama jika ada
                if ($user->profile_picture) {
                    $oldPath = public_path('storage/teacher/' . $user->profile_picture);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $image = $request->file('profile_picture');
                $imageName = $image->hashName();
                $image->move(public_path('storage/teacher/'), $imageName);
                $user->profile_picture = $imageName;
            }
            $user->save();

            $teacher->name = $request->name;
            $teacher->nip = $request->nip;
            $teacher->subject = $request->subject;
            $teacher->save();

            return redirect()->route('akun.indentitas_guru')->with('success', 'Data guru berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update guru gagal. Silakan coba lagi.')->withInput();
        }
    }
}



