<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferNotification extends Notification
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
            'type' => 'offer', // Explicitly specify the type
            'title' => $this->eventData['title'],
            'message' => $this->eventData['message'],
            'lead_id' => $this->eventData['lead_id'], 
            'product_id' => $this->eventData['product_id'], 
            'performed_at' => now()->toDateTimeString(),
        ];
    }


}
