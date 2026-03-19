<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use App\Models\Student;

class Classes extends Model
{
    use HasFactory;

    // table name
    protected $table = 'clases';

    protected $fillable = ['name', 'major', 'grade', 'code', 'fcm_topic'];

    public $timestamps = true;

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'id_class');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'id_class');
    }
}
