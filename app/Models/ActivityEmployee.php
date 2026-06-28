<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'phase_id', 'activity_id', 'employee_id', 'appointment_id'
    ];
}
