<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    // table name
    protected $table = 'subjects';

    protected $fillable = ['name', 'type', 'code'];

    public $timestamps = true;


        public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }
}


