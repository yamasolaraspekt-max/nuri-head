<?php

namespace App\Events;

use App\Models\DashboardLiveActivity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardLiveActivityCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public DashboardLiveActivity $activity;

    public function __construct(DashboardLiveActivity $activity)
    {
        $this->activity = $activity;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('employee.' . $this->activity->employee_id);
    }

    public function broadcastAs(): string
    {
        return 'dashboard.activity.created';
    }

    public function broadcastWith(): array
    {
        return [
            'activity' => [
                'id' => $this->activity->id,
                'employee_id' => $this->activity->employee_id,
                'type' => $this->activity->type,
                'action' => $this->activity->action,
                'title' => $this->activity->title,
                'message' => $this->activity->message,
                'url' => $this->activity->url,
                'payload' => $this->activity->payload,
                'read_at' => optional($this->activity->read_at)->toISOString(),
                'is_read' => (bool) $this->activity->read_at,
                'created_at' => optional($this->activity->created_at)->toISOString(),
                'created_human' => optional($this->activity->created_at)->diffForHumans(),
            ],
        ];
    }
}