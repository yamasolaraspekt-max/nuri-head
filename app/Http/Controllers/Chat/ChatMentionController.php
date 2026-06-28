<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatMention;

class ChatMentionController extends Controller
{
    public function unread()
    {
        $authUserId = (int) auth()->id();

        $mentions = ChatMention::query()
            ->with(['chat', 'mentionedBy.employee', 'group'])
            ->where('mentioned_user_id', $authUserId)
            ->whereNull('read_at')
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn(ChatMention $mention) => $this->transformMention($mention));

        return response()->json([
            'mentions' => $mentions,
        ]);
    }

    public function markRead(ChatMention $mention)
    {
        abort_unless((int) $mention->mentioned_user_id === (int) auth()->id(), 403);

        if (!$mention->read_at) {
            $mention->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    private function transformMention(ChatMention $mention): array
    {
        $mention->loadMissing(['chat', 'mentionedBy.employee', 'group']);

        $sender = $mention->mentionedBy;
        $employee = $sender?->employee;

        $senderName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
        if ($senderName === '') {
            $senderName = $sender?->name ?? 'Mitarbeiter';
        }

        $avatar = $employee && $employee->image
            ? asset('images/employee/' . ltrim((string) $employee->image, '/'))
            : asset('images/gender/users.png');

        return [
            'id' => $mention->id,
            'chat_id' => $mention->chat_id,
            'group_id' => $mention->group_id,
            'mentioned_by_user_id' => $mention->mentioned_by_user_id,
            'sender_name' => $senderName,
            'sender_avatar' => $avatar,
            'group_name' => $mention->group?->name ?: ($mention->group?->context_label ?: 'Chat'),
            'message' => mb_strimwidth((string) ($mention->chat?->message ?? 'Du wurdest in einer Nachricht markiert.'), 0, 160, '…'),
            'created_at' => optional($mention->created_at)?->toIso8601String(),
            'open_url' => $this->buildMentionOpenUrl($mention),
        ];
    }

    private function buildMentionOpenUrl(ChatMention $mention): string
    {
        $params = [
            'message_id' => $mention->chat_id,
        ];

        if ($mention->group_id) {
            $params['group_id'] = $mention->group_id;
        } else {
            $params['user_id'] = $mention->mentioned_by_user_id;
        }

        return url('/admin/chat?' . http_build_query($params));
    }
}
