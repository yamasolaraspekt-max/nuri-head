<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // or ShouldBroadcastNow if you prefer immediate
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels, Dispatchable;

    public Chat $chat;

    public function __construct(Chat $chat)
    {
        // eager-load what we need
        $this->chat = $chat->load(['fromUser.employee', 'replyTo', 'group.users']);
    }

    public function broadcastAs()
    {
        return 'message-sent';
    }

    public function broadcastOn()
    {
        $channels = [];

        if ($this->chat->group_id) {
            // sidebar previews for all members
            foreach ($this->chat->group->users as $user) {
                $channels[] = new PrivateChannel('chat.user.' . $user->id);
            }
            // group conversation stream
            $channels[] = new PrivateChannel('chat.group.' . $this->chat->group_id);
        } else {
            $min = min($this->chat->from_user_id, $this->chat->to_user_id);
            $max = max($this->chat->from_user_id, $this->chat->to_user_id);

            // direct conversation stream
            $channels[] = new PrivateChannel("chat.$min.$max");

            // sidebar preview for receiver
            $channels[] = new PrivateChannel("chat.user.{$this->chat->to_user_id}");

            // (Optional) sender’s own preview; remove if you don’t want it
            $channels[] = new PrivateChannel("chat.user.{$this->chat->from_user_id}");
        }

        Log::debug('MessageSent::broadcastOn channels →', array_map(fn($c) => $c->name, $channels));
        return $channels;
    }

public function broadcastWith(): array
{
    // Ensure needed relations are there
    $this->chat->loadMissing(['fromUser.employee', 'replyTo']);

    // Employee / avatar (match your Blade pathing)
    $employee = optional($this->chat->fromUser)->employee;
        $avatar = null;

        if ($employee && $employee->image) {
            $avatar = Str::startsWith($employee->image, ['http://', 'https://'])
                ? $employee->image
                : asset('images/employee/' . ltrim($employee->image, '/'));
        } else {
            $avatar = asset('images/gender/users.png');
        }
    // Type as stored in DB: 'text' | 'image' | 'file' | 'audio'
    $type = $this->chat->type;

    // File URL + meta (guard if file missing)
    $fileUrl = null;
    $attachments = [];
    $audioUrl = null;

    if ($this->chat->file_path && Storage::disk('public')->exists($this->chat->file_path)) {
        $fileUrl = Storage::disk('public')->url($this->chat->file_path);

        $mime = null;
        $size = null;
        try { $mime = Storage::disk('public')->mimeType($this->chat->file_path); } catch (\Throwable $e) {}
        try { $size = Storage::disk('public')->size($this->chat->file_path); } catch (\Throwable $e) {}

        if ($type === 'audio') {
            $audioUrl = $fileUrl;
        } elseif (in_array($type, ['image', 'file'], true)) {
            $attachments[] = [
                'url'      => $fileUrl,
                'name'     => basename($this->chat->file_path),
                'mime'     => $mime,
                'size'     => $size,
                'is_image' => ($type === 'image'),
            ];
        }
    }

    // Reply preview text
    $replyPreview = null;
    if ($this->chat->reply_to_id && $this->chat->replyTo) {
        $orig = $this->chat->replyTo;
        $base = $orig->message
            ?? ($orig->type === 'audio' ? 'Sprachnachricht'
                : (in_array($orig->type, ['image', 'file'], true) ? 'Anhang' : ''));
        $replyPreview = mb_strimwidth((string) $base, 0, 120, '…');
    }

    return [
        'id'               => $this->chat->id,
        'message'          => $this->chat->message,
        'type'             => $type, // 'text'|'image'|'file'|'audio'
        'from_user_id'     => $this->chat->from_user_id,
        'to_user_id'       => $this->chat->to_user_id,
        'group_id'         => $this->chat->group_id,
        'created_at'       => optional($this->chat->created_at)?->toIso8601String(),
        'deleted_at'       => optional($this->chat->deleted_at)?->toIso8601String(),
        'is_read'          => (bool) $this->chat->is_read,
        'reply_to_id'      => $this->chat->reply_to_id,
        'reply_to_preview' => $replyPreview,

        'from_user' => [
            'id'       => optional($this->chat->fromUser)->id,
            'name'     => (string) ($employee->name ?? ''),
            'lastname' => (string) ($employee->lastname ?? ''),
            'image'    => $avatar,
        ],

        // media (consistent with fetch/send)
        'attachments' => array_values($attachments),
        'audio_url'   => $audioUrl,
        'file_url'    => $fileUrl, // handy for debug/compat
    ];
}


}
