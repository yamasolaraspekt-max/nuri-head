<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadSidebarCountsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public int $customerId;
    public int $alternativeId;
    public int $productId;

    public function __construct(int $customerId, int $alternativeId, int $productId)
    {
        $this->customerId = $customerId;
        $this->alternativeId = $alternativeId;
        $this->productId = $productId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('lead-sidebar-counts');
    }

    public function broadcastAs(): string
    {
        return 'lead.sidebar.counts.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'customer_id' => $this->customerId,
            'alternative_id' => $this->alternativeId,
            'product_id' => $this->productId,
        ];
    }
}