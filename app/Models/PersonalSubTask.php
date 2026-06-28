<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalSubTask extends Model
{
        use SoftDeletes;
    
        protected $fillable = [
            'task_id', 
            'sub_task_title', 
            'description',  
            
        ];
}
