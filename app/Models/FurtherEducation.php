<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurtherEducation extends Model
{
    use HasFactory;
    protected $fillable=[
        'emp_id',
        'course',
        'major',
        'institution',
        'skill',
        'year',
        'description'
    ];
}
