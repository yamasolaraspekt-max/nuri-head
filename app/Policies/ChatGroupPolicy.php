<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChatGroup;

class ChatGroupPolicy
{
    public function update(User $user, ChatGroup $group)
    {
        $relation = $group->users()->where('user_id', $user->id)->first();
        return $relation && $relation->pivot->role === 'admin';
    }

    public function delete(User $user, ChatGroup $group)
    {
        $relation = $group->users()->where('user_id', $user->id)->first();
        return $user->id === $group->created_by || $relation?->pivot->role === 'admin';
    }


    public function manageMembers(User $user, ChatGroup $group)
    {
        $relation = $group->users()->where('user_id', $user->id)->first();
        return $relation && in_array($relation->pivot->role, ['admin', 'moderator']);
    }
}
