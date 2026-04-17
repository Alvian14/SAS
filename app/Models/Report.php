<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
	protected $table = 'table_report_disrepancy';

	protected $fillable = [
		'id_student',
		'student_name',
		'reported_by',
		'id_attendance_history',
		'id_class',
		'disrepancy_type',
		'description',
		'marked_as_resolved',
		'attendance_date',
	];

	public function student()
	{
		return $this->belongsTo(Student::class, 'id_student');
	}

	public function reporter()
	{
		return $this->belongsTo(User::class, 'reported_by');
	}

	public function attendanceHistory()
	{
		return $this->belongsTo(AttendanceHistory::class, 'id_attendance_history');
	}

	public function class()
	{
		return $this->belongsTo(Classes::class, 'id_class');
	}


}
