<?php

namespace App\Models\Traits;

use App\Models\{Chat, ChatGroup};
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasChatRelations
{
    public function chatGroups(): BelongsToMany
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function ownedChatGroups(): HasMany
    {
        return $this->hasMany(ChatGroup::class, 'created_by');
    }

    public function chatsSent(): HasMany
    {
        return $this->hasMany(Chat::class, 'from_user_id');
    }

    public function chatsReceived(): HasMany
    {
        return $this->hasMany(Chat::class, 'to_user_id');
    }

    public function readChats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_reads', 'user_id', 'chat_id')
                    ->withPivot('read_at');
    }
}
