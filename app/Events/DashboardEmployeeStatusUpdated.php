<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardEmployeeStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function broadcastOn(): Channel
    {
        return new Channel('dashboard.employee-status');
    }

    public function broadcastAs(): string
    {
        return 'dashboard.employee-status.updated';
    }
}