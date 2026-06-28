<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    protected $fillable = [
        'chat_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
        'is_image',
    ];

    protected $casts = [
        'is_image' => 'boolean',
    ];

    // So JSON has url + name
    protected $appends = ['url', 'name'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function getUrlAttribute()
    {
        return route('chat.attachment.show', $this->id);
    }

    public function getNameAttribute()
    {
        return $this->original_name;
    }
}
