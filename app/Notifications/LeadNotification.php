<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Specify delivery channels
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Data stored in the database
     */
    public function toDatabase($notifiable)
    {
        return $this->buildPayload();
    }

    /**
     * Data sent via broadcasting
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->buildPayload());
    }

    /**
     * Shared data payload
     */
    protected function buildPayload(): array
    {
        return [
            'type'         => 'lead',
            'title'        => $this->eventData['title'] ?? 'Neuer Lead',
            'message'      => $this->eventData['message'] ?? '',
            'lead_id'      => $this->eventData['lead_id'] ?? null,
            'from'         => $this->eventData['from'] ?? null,
            'to'           => $this->eventData['to'] ?? null,
            'performed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Optional: array representation
     */
    public function toArray($notifiable): array
    {
        return $this->buildPayload();
    }
}
