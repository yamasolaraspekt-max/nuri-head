<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return $this->buildPayload();
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->buildPayload());
    }

    protected function buildPayload()
    {
        return [
            'title'          => $this->eventData['title'] ?? 'Terminbenachrichtigung',
            'message'        => $this->eventData['message'] ?? '',
            'appointment_id' => $this->eventData['appointment_id'] ?? null,
            'type'           => 'appointment',
            'kind'           => $this->eventData['kind'] ?? 'generic', // created, updated, status, due …
            'performed_at'   => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->buildPayload();
    }
}
