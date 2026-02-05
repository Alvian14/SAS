<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $table = 'academic_periods';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        // 'semester', // Uncomment if you add semester column
    ];
}
