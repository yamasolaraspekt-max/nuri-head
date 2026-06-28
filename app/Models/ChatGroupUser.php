<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatGroupUser extends Pivot
{
    protected $table = 'chat_group_user';

    protected $fillable = [
        'chat_group_id',
        'user_id',
        'role',
        'status',              // pending | accepted | declined
        'invited_by',
        'joined_at',
        'history_visibility',  // all | from_join
        'can_write',
    ];

    protected $casts = [
        'joined_at'          => 'datetime',
        'can_write'          => 'boolean',
    ];
}
