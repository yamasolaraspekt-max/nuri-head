<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherSkill extends Model
{
    use HasFactory;

    protected $fillable=[
        'emp_id',
        'skills',
        'proficiency',
        'year_experience',
    ];
}
