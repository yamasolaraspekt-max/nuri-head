<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    
     protected $fillable = [
        'message_id',
        'chat_id',
        'author_id',
        'text',
        'date',
         'user_id',
        'name',
        'first_name',
        'last_name',
        'work_position',
        'avatar',
    ];
}
