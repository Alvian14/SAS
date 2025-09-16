<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    // table name
    protected $table = 'clases';

    protected $fillable = ['name', 'major', 'grade', 'code'];

    public $timestamps = true;
}
