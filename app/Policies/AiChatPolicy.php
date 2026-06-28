<?php
namespace App\Policies;

use App\Models\AiChat;
use App\Models\User;

class AiChatPolicy
{
    public function view(User $user, AiChat $chat): bool
    {
        if ($chat->user_id === $user->id) return true;
        // participant access
        return $chat->participants()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, AiChat $chat): bool
    {
        return $chat->user_id === $user->id;
    }


      public function delete(User $user, AiChat $chat): bool
        {
            return $chat->user_id === $user->id;
        }
}