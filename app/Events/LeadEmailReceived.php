<?php

namespace App\Events;

use App\Models\LeadEmail;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadEmailReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public LeadEmail $leadEmail)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('lead-emails'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.email.received';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->leadEmail->id,
            'message_id' => $this->leadEmail->message_id,
            'from' => $this->leadEmail->from,
            'from_name' => $this->leadEmail->from_name,
            'from_email' => $this->leadEmail->from_email,
            'domain' => $this->leadEmail->domain,
            'subject' => $this->leadEmail->subject,
            'date' => optional($this->leadEmail->date)->toDateTimeString(),
            'status' => $this->leadEmail->status,
        ];
    }
}