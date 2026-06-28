<?php

namespace App\Notifications;

use App\Models\WartungOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class WartungOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected WartungOrder $order;
    protected string $event; // created, updated, status_changed, deleted

    public function __construct(WartungOrder $order, string $event)
    {
        $this->order = $order;
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type'       => 'wartung_order',
            'event'      => $this->event,
            'order_id'   => $this->order->id,
            'contract_id'=> $this->order->wartung_contract_id,
            'customer_id'=> $this->order->customer_id,
            'status'     => $this->order->status,
            'title'      => $this->order->title,
        ]);
    }
}
