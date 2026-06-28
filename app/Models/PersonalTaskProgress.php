<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalTaskProgress extends Model
{
    //
    use SoftDeletes;
    protected $fillable= [
        'prg_task'
    ];
}
