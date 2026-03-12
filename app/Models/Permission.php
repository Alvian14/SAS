<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    // table name
    protected $table = 'permissions';
    
    protected $fillable = ['period_start', 'period_end', 'reason', 'information', 'evidence', 'status', 'feedback', 'date_permission', 'time_period', 'id_student', 'approved_by'];
    
    public $timestamps = true;

    // relationship foreign key
    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student');
    }

    public static function findById($id)
    {
        return self::with('student')->findOrFail($id);
    }

}
