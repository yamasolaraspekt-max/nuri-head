<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Delivery channels: DB + broadcast
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Database payload
     */
    public function toDatabase($notifiable)
    {
        return $this->buildPayload();
    }

    /**
     * Broadcast payload
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->buildPayload());
    }

    /**
     * Common data payload
     */
    protected function buildPayload(): array
    {
        return [
            'type'         => 'inquiry',
            'title'        => $this->eventData['title'] ?? 'Neue Anfrage',
            'message'      => $this->eventData['message'] ?? '',
            'lead_id'      => $this->eventData['lead_id'] ?? null,
            'from'         => $this->eventData['from'] ?? null,
            'contact_type' => $this->eventData['contact_type'] ?? null,
            'performed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Optional fallback
     */
    public function toArray($notifiable): array
    {
        return $this->buildPayload();
    }
}
