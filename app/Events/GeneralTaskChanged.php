<?php

namespace App\Events;

use App\Models\GeneralTask;
use App\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GeneralTaskChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $task;
    public string $message;
    public string $action;
    public ?int $actor_id;
    public string $actor_name;

    public function __construct(GeneralTask $task, string $message = 'Aufgabe wurde aktualisiert.', ?string $action = null)
    {
        $task->loadMissing(['department', 'claimedBy', 'assignees', 'steps.assignees', 'steps.checkedBy', 'dependsOn', 'blockingTasks']);

        $this->task = [
            'id' => (int) $task->id,
            'title' => (string) ($task->title ?? ''),
            'status' => (string) ($task->status ?? 'open'),
            'priority' => (string) ($task->priority ?? 'normal'),
            'progress_percent' => (int) ($task->progress_percent ?? 0),
            'created_by' => $task->created_by ? (int) $task->created_by : null,
        ];

        $this->message = $message;
        $this->action = $action ?: $this->detectAction($message);
        $this->actor_id = auth()->check() ? (int) auth()->user()->name : null;
        $employee = $this->actor_id ? Employee::find($this->actor_id) : null;
        $this->actor_name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: 'Mitarbeiter';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('general-tasks');
    }

    public function broadcastAs(): string
    {
        return 'GeneralTaskChanged';
    }

    private function detectAction(string $message): string
    {
        $lower = Str::lower($message);

        if (str_contains($lower, 'neue aufgabe') || str_contains($lower, 'erstellt')) {
            return 'created';
        }

        if (str_contains($lower, 'gelöscht')) {
            return 'deleted';
        }

        if (str_contains($lower, 'archiv')) {
            return 'archived';
        }

        if (str_contains($lower, 'status')) {
            return 'moved';
        }

        return 'updated';
    }
}
