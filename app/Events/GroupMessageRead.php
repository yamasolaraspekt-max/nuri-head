<?php

namespace App\Events;
use App\Models\Chat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupMessageRead implements ShouldBroadcast
{
    public Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.group.' . $this->chat->group_id);
    }

    public function broadcastAs()
    {
        return 'message-read';
    }

    public function broadcastWith()
    {
        $this->chat->loadMissing('readBy.employee');

        return [
            'chat_id' => $this->chat->id,
            'read_by' => $this->chat->readBy->map(function (User $user) {
                $emp = $user->employee;
                $readAt = $user->pivot->read_at ?? null;

                return [
                    'id'       => $user->id,
                    'name'     => $emp->name ?? $user->name ?? '',
                    'lastname' => $emp->lastname ?? '',
                    'read_at'  => $readAt
                        ? Carbon::parse($readAt)->toIso8601String()
                        : null,
                ];
            })->values()->all(),
        ];
    }
}
