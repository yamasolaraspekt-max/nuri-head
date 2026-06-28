<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PlannerToastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $data) {}

    public function via($notifiable): array
    {
        // remove 'database' if you want realtime-only
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable): array
    {
        return $this->data;
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->data);
    }
}
