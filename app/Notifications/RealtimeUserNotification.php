<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification; 
use Illuminate\Notifications\Messages\BroadcastMessage; 

class RealtimeUserNotification extends Notification   implements ShouldQueue
{
     use Queueable;

    public function __construct(
        public array $payload // ['type' => 'task', 'title' => '...', 'message' => '...', 'performed_at' => now()->toISOString()]
    ) {}

    public function via($notifiable): array
    {
        // database + broadcast (via Echo/Reverb)
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return $this->payload;
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        // Will be sent on private channel: "App.Models.User.{id}"
        // Echo has a special `.notification()` listener that handles this.
        return new BroadcastMessage($this->payload + [
            'id' => $this->id, // Notification UUID
        ]);
    }
}
