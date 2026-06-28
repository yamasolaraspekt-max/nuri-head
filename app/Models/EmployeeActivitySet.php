<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeActivitySet extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_set_id', 'employee_set_id', 'phase_id', 'activity_id'
    ];
    
}
