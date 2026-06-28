<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable=[
        'emp_id',
        'degree',
        'major',
        'institution',
        'q_start_year',
        'q_end_year',
        'grade'
    ];
}

