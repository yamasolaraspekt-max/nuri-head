<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistRoom extends Model
{
    use HasFactory;

    protected $fillable =[
        'story_id', 'unit', 'room_size', 'heating_type', 'customer_id'
    ];
}
