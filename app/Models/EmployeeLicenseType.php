<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLicenseType extends Model
{
    use HasFactory;
    protected $table = 'employee_license_types';

    protected $fillable = [
        'employee_license_id',
        'employee_id',
        'grade',
        'type', 
    ];
}
