<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NoteCategory extends Model
{
    use Softdeletes;

    protected $fillable = [
        'category_name',
        'color', 
        'type', 
        'icon', 
        'status',
        'user'
    ];
}
