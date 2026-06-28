<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    use HasFactory;

    protected $fillable=[
        'emp_id',
        'address_name',
        'city',
        'street',
        'apartment',
        'postal',
        'main'
    ];
}
