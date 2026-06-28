<?php

namespace App\Events;

use App\Models\SystemWarning;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemWarningUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public array $warning;

    public function __construct(SystemWarning $warning)
    {
        $this->warning = $warning->publicPayload();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('system-warning');
    }

    public function broadcastAs(): string
    {
        return 'system.warning.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'warning' => $this->warning,
        ];
    }
}