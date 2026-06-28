<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    public function __construct(
        public int $senderId,
        public \App\Models\User $reader,
        public array $messageIds
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel("chat.user.{$this->senderId}");
    }

    public function broadcastAs()
    {
        return 'message-read';
    }

    public function broadcastWith()
    {
        return [
            'message_ids' => $this->messageIds,
            'reader' => [
                'id'       => $this->reader->id,
                'name'     => $this->reader->name,
                'lastname' => $this->reader->lastname,
                'read_at'  => now()->toIso8601String(),
            ],
        ];
    }
}