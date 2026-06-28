<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
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
                'ticket_id' => $this->eventData['ticket_id'] ?? null,
                'employee_id' => $this->eventData['employee_id'] ?? null,
                'customer_id' => $this->eventData['customer_id'] ?? null,
                'alternative_id' => $this->eventData['alternative_id'] ?? null,
                'product_id' => $this->eventData['product_id'] ?? null, 
                'type' => $this->eventData['type'] ?? 'ticket',
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
