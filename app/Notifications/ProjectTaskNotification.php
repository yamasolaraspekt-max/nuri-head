<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectTaskNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     public $eventData;

    public function __construct($eventData)
    {
        $this->eventData = $eventData;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
     public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->eventData['title'],
            'message' => $this->eventData['message'],
            'project_id' => $this->eventData['project_id'] ?? null,
            'phase_id' => $this->eventData['phase_id'] ?? null,
            'customer_id' => $this->eventData['customer_id'] ?? null,
            'alternative_id' => $this->eventData['alternative_id'] ?? null,
            'product_id' => $this->eventData['product_id'] ?? null,
            'activities_id' => $this->eventData['activities_id'] ?? null,
            'sub_task_id' => $this->eventData['sub_task_id'] ?? null,
            'type' => $this->eventData['type'] ?? 'project',
            'performed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
