<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDepartment extends Model
{
    

    protected $fillable = [
        'activity_id', 'department_id'
    ];
}
