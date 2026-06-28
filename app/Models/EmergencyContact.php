<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable=[
        'emp_id',
        'relation',
        'phone',
        'home_phone',
        'email',
        'street',
        'postal',
        'city',
    ];
}
