<?php
namespace App\Events;

use App\Models\ChatGroup;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class GroupMembershipUpdated implements ShouldBroadcastNow
{
    use SerializesModels;

    public ChatGroup $group;
    public int $userId;

    public function __construct(ChatGroup $group, int $userId)
    {
        // If you want the users relation, load `users`, not `members`
        $this->group = $group->fresh(['users']);
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        // same channel as other user-specific events
        return new PrivateChannel('chat.user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'group-membership-updated';
    }

    public function broadcastWith()
    {
        return [
            'group' => [
                'id'                   => $this->group->id,
                'name'                 => $this->group->name,
                'context_label'        => $this->group->context_label,
                'avatar'               => $this->group->avatar,
                'customer_id'          => $this->group->customer_id,
                'alternative_id'       => $this->group->alternative_id,
                'product_id'           => $this->group->product_id,
                'lead_product_list_id' => $this->group->lead_product_list_id,
                'last_msg'             => $this->group->last_msg ?? null,
                'last_from_name'       => $this->group->last_from_name ?? null,
                // group has no pivot here, so do NOT read $this->group->pivot
                'membership_status'    => 'pending',
                'unread'               => 0,
            ],
        ];
    }
}
