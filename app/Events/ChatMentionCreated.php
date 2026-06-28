<?php

namespace App\Events;

use App\Models\ChatMention;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class ChatMentionCreated implements ShouldBroadcastNow
{
    use SerializesModels;

    public ChatMention $mention;

    public function __construct(ChatMention $mention)
    {
        $this->mention = $mention->loadMissing([
            'chat',
            'mentionedBy.employee',
            'group',
        ]);
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.user.' . $this->mention->mentioned_user_id);
    }

    public function broadcastAs(): string
    {
        return 'chat-mention-created';
    }

    public function broadcastWith(): array
    {
        $mention = $this->mention->loadMissing(['chat', 'mentionedBy.employee', 'group']);

        $sender = $mention->mentionedBy;
        $employee = $sender?->employee;

        $senderName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
        if ($senderName === '') {
            $senderName = $sender?->name ?? 'Mitarbeiter';
        }

        $params = [
            'message_id' => $mention->chat_id,
        ];

        if ($mention->group_id) {
            $params['group_id'] = $mention->group_id;
        } else {
            $params['user_id'] = $mention->mentioned_by_user_id;
        }

        $avatar = $employee && $employee->image
            ? asset('images/employee/' . ltrim((string) $employee->image, '/'))
            : asset('images/gender/users.png');

        return [
            'mention' => [
                'id' => $mention->id,
                'chat_id' => $mention->chat_id,
                'group_id' => $mention->group_id,
                'mentioned_by_user_id' => $mention->mentioned_by_user_id,
                'sender_name' => $senderName,
                'sender_avatar' => $avatar,
                'group_name' => $mention->group?->name ?: ($mention->group?->context_label ?: 'Chat'),
                'message' => mb_strimwidth((string) ($mention->chat?->message ?? 'Du wurdest in einer Nachricht markiert.'), 0, 160, '…'),
                'created_at' => optional($mention->created_at)?->toIso8601String(),
                'open_url' => url('/admin/chat?' . http_build_query($params)),
            ],
        ];
    }
}
