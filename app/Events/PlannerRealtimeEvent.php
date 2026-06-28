<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class PlannerRealtimeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $afterCommit = true;           // important: only after DB commit
    public string $broadcastQueue = 'broadcasts';

    public function __construct(
        public array $channels,               // e.g. ["planner.plan.12", "planner.employee.7"]
        public string $name,                  // e.g. "planner.item.moved"
        public array $payload                 // data for UI
    ) {}

    public function broadcastOn(): array
    {
        return array_values(array_filter(array_map(function ($ch) {
            return $ch ? new PrivateChannel($ch) : null;
        }, $this->channels)));
    }

    public function broadcastAs(): string
    {
        return $this->name;
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
