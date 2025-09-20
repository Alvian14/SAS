<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceHistory extends Model
{
    use HasFactory;

    // table name
    protected $table = 'attendance_histories';

    protected $fillable = ['period_number', 'status', 'id_student', 'id_schedule'];

    public $timestamps = true;

    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'id_schedule');
    }
}
