<?php

namespace App\Notifications;

use App\Models\PersonalTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PersonalTaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PersonalTask $task,
        public string $type = 'reminder'
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->type === 'repeat'
                ? 'Wiederholte Aufgabe'
                : 'Aufgaben-Erinnerung',
            'message' => $this->type === 'repeat'
                ? 'Eine wiederholte Aufgabe wurde erstellt: ' . $this->task->task_title
                : 'Erinnerung: ' . $this->task->task_title,
            'task_id' => $this->task->id,
            'url' => route('personal-tasks.profile', ['task' => $this->task->id]),
        ];
    }
}