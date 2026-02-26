<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    // table name
    protected $table = 'teachers';

    protected $fillable = [
        'id_user',
        'name',
        'nip',
        'subject',  /// kode pramudya.
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public static function findById($id)
    {
        return self::with('user')->findOrFail($id);
    }

    /// kode pramudya
    public function getSubjectObjectsAttribute()
    {
        $subjectField = $this->subject ?? ''; // stored as CSV subject codes like "MTK, IPA"
        $names = array_filter(array_map('trim', explode(',', $subjectField)));
        if (empty($names)) {
            return collect();
        }
        // Search by subject.code and return Subject models so we can show their names
        return Subject::whereIn('code', $names)->get();
    }

    public function subjects()
{
    return $this->hasMany(Subject::class, 'id_teacher');
}
}
