<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMention extends Model
{
    protected $fillable = [
        'chat_id',
        'group_id',
        'mentioned_user_id',
        'mentioned_by_user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function mentionedBy()
    {
        return $this->belongsTo(User::class, 'mentioned_by_user_id');
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }
}