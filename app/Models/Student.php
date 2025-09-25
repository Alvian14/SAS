<?php

namespace App\Models;

use App\Models\User;
use App\Models\Classes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    // table name
    protected $table = 'students';

    protected $fillable = ['id_user', 'id_class', 'name', 'nisn', 'entry_year', 'picture'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'id_class');
    }
}
