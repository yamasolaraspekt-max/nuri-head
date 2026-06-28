<?php
  
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClipboardUpdated implements ShouldBroadcastNow 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    // ❌ REMOVED the $clipboardItems array from here

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.clipboard.' . $this->userId)
        ];
    }

    public function broadcastAs(): string
    {
        return 'clipboard.updated';
    }
}