<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Student;
use App\Models\Teacher;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'is_banned',
        'device_token',
        'device_id',
        'topic_subscribe',
        'profile_picture',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id_user');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'id_user');
    }

    /**
     * Nama tampilan: ambil dari relasi teacher/student, fallback ke email.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('teacher') ? $this->teacher : $this->teacher()->first()) {
            return $this->teacher->name;
        }
        if ($this->relationLoaded('student') ? $this->student : $this->student()->first()) {
            return $this->student->name;
        }
        return $this->email ?? 'Pengguna';
    }

    /**
     * Label role dalam Bahasa Indonesia.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'   => 'Admin',
            'teacher' => 'Guru',
            'student' => 'Siswa',
            default   => ucfirst((string) $this->role),
        };
    }
}
