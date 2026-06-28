<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomProcess extends Model
{
    public  $fillable = [
        'employee_id',
        'stage_name', 
        'status',  
    ];
}
