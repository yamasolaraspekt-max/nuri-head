<?php

namespace App\Events;

use App\Models\PersonalTask;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PersonalTaskReminderTriggered implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PersonalTask $task,
        public int $employeeId,
        public string $type = 'reminder'
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee.' . $this->employeeId . '.tasks'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'personal-task.reminder';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'task_id' => $this->task->id,
            'task_title' => $this->task->task_title,
            'description' => $this->task->description,
            'priority' => $this->task->priority,
            'due_date' => optional($this->task->due_date)->format('Y-m-d'),
            'due_time' => $this->task->due_time,
            'reminder_at' => optional($this->task->next_reminder_at)->toDateTimeString(),
            'url' => route('personal-tasks.profile', ['task' => $this->task->id]),
            'message' => $this->type === 'repeat'
                ? 'Wiederholte Aufgabe wurde erstellt.'
                : 'Erinnerung für Aufgabe: ' . $this->task->task_title,
        ];
    }
}