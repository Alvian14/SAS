<?php

namespace App\Models;

use App\Models\AcademicPeriod;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // table name
    protected $table = 'schedules';

    protected $fillable = ['day_of_week', 'period_start', 'period_end', 'start_time', 'end_time', 'code', 'id_class', 'id_subject', 'id_teacher', 'id_academic_periods'];

    public $timestamps = true;

    public function class()
    {
        return $this->belongsTo(Classes::class, 'id_class');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'id_subject');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    public function academicPeriods()
    {
        return $this->belongsTo(AcademicPeriod::class, 'id_academic_periods');
    }
}
