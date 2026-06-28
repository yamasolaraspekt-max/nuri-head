<?php

namespace App\Notifications;

use App\Models\WartungContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class WartungContractNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected WartungContract $contract;
    protected string $event; // created, updated, status_changed, deleted

    public function __construct(WartungContract $contract, string $event)
    {
        $this->contract = $contract;
        $this->event    = $event;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type'        => 'wartung_contract',
            'event'       => $this->event,
            'contract_id' => $this->contract->id,
            'customer_id' => $this->contract->customer_id,
            'status'      => $this->contract->status,
            'priority'    => $this->contract->priority,
            'contract_no' => $this->contract->contract_no,
            'name'        => $this->contract->name,
        ]);
    }
}
