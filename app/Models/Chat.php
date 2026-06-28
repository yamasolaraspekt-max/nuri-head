<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ChatAttachment;
class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'group_id',
        'message',
        'file_path',
        'type',
        'status',
        'is_read',
        'read_at',
        'reply_to_id',
        'shared_from_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Sender
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    // Direct recipient (null for group messages)
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    // Group (null for 1:1)
    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    // Reply reference
    public function replyTo()
    {
        return $this->belongsTo(Chat::class, 'reply_to_id');
    }

    // Read receipts table
    public function reads()
    {
        return $this->hasMany(ChatRead::class, 'chat_id');
    }

    // Users that have read this message (pivot)
    public function readBy()
    {
        return $this->belongsToMany(User::class, 'chat_reads', 'chat_id', 'user_id')
            ->withPivot('read_at');
    }

     public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'chat_id');
    }

    public function mentions()
    {
        return $this->hasMany(\App\Models\ChatMention::class, 'chat_id');
    }


}
