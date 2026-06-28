<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPosition extends Model
{
    
    protected $fillable = [
        'activity_id', 
        'position_id'
    ];
}
