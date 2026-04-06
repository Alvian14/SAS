<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Teacher;
use App\Models\Subject;
use Faker\Provider\Image as ProviderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showConfirmPassword(Request $request)
    {
        if (!$request->filled('email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link konfirmasi password tidak valid.']);
        }

        return view('auth.confirm-password');
    }

    private function getRegisteredAdminByEmail(string $email): ?User
    {
        return User::where('email', $email)
            ->where('role', 'admin')
            ->first();
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $adminUser = $this->getRegisteredAdminByEmail($request->email);

        if ($adminUser) {
            return redirect()->route('password.confirm.form', [
                'email' => $adminUser->email,
            ])->with('status', 'Email admin terverifikasi. Silakan ganti password baru.');
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $adminUser = $this->getRegisteredAdminByEmail($request->email);

        if (!$adminUser) {
            return back()->withErrors([
                'email' => ['Email tersebut bukan akun admin.'],
            ]);
        }

        $adminUser->password = Hash::make($request->password);
        $adminUser->save();

        return redirect()->route('login')->with('success', 'Password admin berhasil diperbarui. Silakan login.');
    }

    public function showSetting()
    {
        $user = Auth::user();

        return view('pages.setting.setting', compact('user'));
    }

    public function updateSetting(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($request->input('form_type') === 'profile') {
            return $this->updateSettingProfile($request, $user);
        }

        if ($request->input('form_type') === 'password') {
            return $this->updateSettingPassword($request, $user);
        }

        return back()->withErrors(['form' => 'Form tidak valid.']);
    }

    public function deleteProfilePicture()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->route('login');
        }

        if (empty($user->profile_picture)) {
            return back()->with('success', 'Foto profil sudah kosong.');
        }

        $photoPath = public_path('storage/profile/' . $user->profile_picture);
        if (file_exists($photoPath)) {
            @unlink($photoPath);
        }

        $user->profile_picture = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    private function updateSettingProfile(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            if (!empty($user->profile_picture)) {
                $oldPath = public_path('storage/profile/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $image = $request->file('profile_picture');
            $imageName = $image->hashName();
            $image->move(public_path('storage/profile/'), $imageName);
            $user->profile_picture = $imageName;
        }

        $user->save();

        return back()->with('success', 'Email dan gambar profil berhasil diperbarui.');
    }

    private function updateSettingPassword(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
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
                'subjects' => 'required|array|min:1',
                'subjects.*' => 'integer|exists:subjects,id',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = $image->hashName();
                $image->move(public_path('storage/teacher/'), $imageName);
            }

            // Ambil nama semua subject yang dipilih
            $subjectNames = Subject::whereIn('id', $request->subjects)->pluck('code')->toArray();
            $subjectString = implode(', ', $subjectNames);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'profile_picture' => $imageName,
            ]);

            $teacher = new Teacher();
            $teacher->id_user = $user->id;
            $teacher->name = $request->name;
            $teacher->nip = $request->nip;
            $teacher->subject = $subjectString;
            $teacher->save();

            return redirect()->route('akun.indentitas_guru')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi guru gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function identitasGuru()
    {
        $teachers = Teacher::with('user')->get(); // Hapus 'subjects'
        $subjects = Subject::all();



        return view('pages.akun.indentitas_guru', compact('teachers', 'subjects'));
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
                'subjects' => 'required|array|min:1',
                'subjects.*' => 'integer|exists:subjects,id',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->email = $request->email;
            if ($request->hasFile('profile_picture')) {
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

            // Ambil nama semua subject yang dipilih
            $subjectNames = Subject::whereIn('id', $request->subjects)->pluck('code')->toArray();
            $subjectString = implode(', ', $subjectNames);

            $teacher->name = $request->name;
            $teacher->nip = $request->nip;
            $teacher->subject = $subjectString;
            $teacher->save();

            return redirect()->route('akun.indentitas_guru')->with('success', 'Data guru berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update guru gagal. Silakan coba lagi.')->withInput();
        }
    }

    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        // Hapus user terkait (akan otomatis hapus teacher jika foreign key cascade)
        if ($teacher->user) {
            $teacher->user->delete();
        } else {
            $teacher->delete();
        }
        return redirect()->route('akun.indentitas_guru')->with('success', 'Data guru berhasil dihapus.');
    }
}



