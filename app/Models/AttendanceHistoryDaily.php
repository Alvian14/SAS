<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceHistoryDaily extends Model
{
    protected $table = 'attendance_history_dailys';

    protected $fillable = [
        'status',
        'picture',
        'created_at',
        'updated_at',
        'id_student',
        'id_class',
    ];

    public $timestamps = true;

    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'id_class');
    }
}
