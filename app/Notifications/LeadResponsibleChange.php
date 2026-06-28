<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadResponsibleChange extends Notification
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
            'type' => $this->eventData['type'],
            'message' => $this->eventData['message'],
            'lead_id' => $this->eventData['lead_id'] ?? null, 
            'responsible_id' => $this->eventData['responsible_id'] ?? null, 
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
