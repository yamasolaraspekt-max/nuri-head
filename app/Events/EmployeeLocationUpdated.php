<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn(): array
    {
        $employeeId = (int) ($this->payload['employee_id'] ?? 0);
        $planId = (int) ($this->payload['planner_plan_id'] ?? 0);

        return array_values(array_filter([
            $planId > 0 ? new Channel('planner.plan.' . $planId . '.locations') : null,
            $employeeId > 0 ? new Channel('planner.employee.' . $employeeId . '.location') : null,
        ]));
    }

    public function broadcastAs(): string
    {
        return 'employee.location.updated';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
