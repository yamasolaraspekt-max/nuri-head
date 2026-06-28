<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EmployeeProfileNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Notification delivery channels
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
     * Real-time broadcast payload
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->buildPayload());
    }

    /**
     * Common payload for both database and broadcast
     */
    protected function buildPayload(): array
    {
        return [
            'title'        => $this->eventData['title'] ?? 'Profiländerung',
            'message'      => $this->eventData['message'] ?? '',
            'emp_id'       => $this->eventData['emp_id'] ?? null,
            'type'         => $this->eventData['type'] ?? 'employee',
            'performed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Optional array fallback
     */
    public function toArray($notifiable): array
    {
        return $this->buildPayload();
    }
}
