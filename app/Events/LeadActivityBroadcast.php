<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadActivityBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    // We broadcast to a shared private channel for all authenticated users.
    // We will handle the filtering (who wants to see what) on the frontend.
    public function broadcastOn()
{
        return new PrivateChannel('company-activities');
    }

    public function broadcastAs()
    {
        return 'activity.created';
    }
}