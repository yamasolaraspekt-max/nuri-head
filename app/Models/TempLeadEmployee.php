<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempLeadEmployee extends Model
{
    use HasFactory;

    public $fillable = [
        'employee_id', 'department_id', 'position_id'
    ];
}
