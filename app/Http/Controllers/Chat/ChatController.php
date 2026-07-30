<?php

namespace App\Http\Controllers\Chat;

use App\Events\ChatMentionCreated;
use App\Events\GroupMembershipUpdated;
use App\Events\GroupMessageRead;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatAttachment;
use App\Models\ChatGroup;
use App\Models\ChatMention;
use App\Models\Employee;
use App\Models\LeadProductList;
use App\Models\LearningTopic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Views / AI chat index
    // ─────────────────────────────────────────────────────────────

    public function indexApi(Request $request)
    {
        $rows = \App\Models\AiChat::query()
            ->where('user_id', $request->user()->id)
            ->leftJoin('new_leads', 'new_leads.id', '=', 'ai_chats.customer_id')
            ->orderByDesc('ai_chats.last_activity_at')
            ->limit(100)
            ->get([
                'ai_chats.id as chat_id',
                'ai_chats.customer_id',
                'ai_chats.title',
                'ai_chats.last_activity_at',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.full_address',
                'new_leads.city',
            ]);

        return response()->json($rows);
    }

    public function index()
    {
        return view('admin.chats.employee.chat');
    }

    // ─────────────────────────────────────────────────────────────
    // Employees + Groups sidebar datasource
    // Important: chat IDs are users.id. employees.id is only for profile data.
    // ─────────────────────────────────────────────────────────────

    public function getEmployeesAndGroups(Request $request)
    {
        $authUserId = (int) auth()->id();
        $authEmployeeId = (int) auth()->user()->name;
        $customerId = $request->integer('customer_id') ?: null;

        // PB-043: Dieser Endpunkt wird gepollt. Drei unbedingte `Log::info` schrieben je 32 915
        // Zeilen — zusammen 44 % einer 218-MB-Logdatei ohne Rotation, in der 2 054 echte
        // Fehlermeldungen begraben lagen.
        //
        // **Nicht geloescht, sondern an die Bedingung gehaengt, die es hier schon gab:** derselbe
        // `debug`-Schalter, den die Antwort weiter unten auswertet. Wer die Diagnose braucht, ruft
        // `?debug=1` — wie bisher; wer nur pollt, erzeugt keine Zeile mehr.
        $debug = $request->boolean('debug');

        if ($debug) {
            Log::debug('[Chat] getEmployeesAndGroups IN', [
                'auth_user_id' => $authUserId,
                'auth_employee_id' => $authEmployeeId,
                'customer_id' => $customerId,
            ]);
        }

        $pinnedUserIds = Schema::hasTable('pinned_private_chats')
            ? DB::table('pinned_private_chats')
                ->where('user_id', $authUserId)
                ->pluck('other_user_id')
                ->map(fn($value) => (int) $value)
                ->all()
            : [];

        $pinnedGroupIds = Schema::hasTable('pinned_group_chats')
            ? DB::table('pinned_group_chats')
                ->where('user_id', $authUserId)
                ->pluck('chat_group_id')
                ->map(fn($value) => (int) $value)
                ->all()
            : [];

        $users = User::with('employee')
            ->where('id', '!=', $authUserId)
            ->whereHas('employee', function ($query) {
                $query->where('status', 'Active');
            })
            ->get();

        // PB-043: **Die teuerste der drei.** Sie schrieb bei JEDEM Abruf Vorname und Nachname
        // aller aktiven Mitarbeiter in die Logdatei — der Prüfer hat das ausdrücklich als
        // Datenschutz-Kante gemeldet. Hinter dem `debug`-Schalter bleibt die Diagnose erhalten,
        // ohne dass Namen bei jedem Poll auf die Platte gehen.
        if ($debug) {
            Log::debug('[Chat] employees fetched', [
                'count' => $users->count(),
                'map' => $users->map(fn(User $user) => [
                    'user_id' => $user->id,
                    'employee_id' => (int) $user->name,
                    'emp_has_row' => (bool) $user->employee,
                    'emp_status' => $user->employee->status ?? null,
                    'emp_name' => optional($user->employee)->name,
                    'emp_lastname' => optional($user->employee)->lastname,
                ])->values()->all(),
            ]);
        }

        $employees = $users
            ->map(function (User $user) use ($authUserId, $pinnedUserIds) {
                $employee = $user->employee;

                if (!$employee) {
                    Log::warning('[Chat] user without employee row', [
                        'user_id' => $user->id,
                        'user_name_field' => $user->name,
                    ]);
                    return null;
                }

                $lastMsg = Chat::query()
                    ->whereNull('deleted_at')
                    ->where(function ($query) use ($user, $authUserId) {
                        $query->where(function ($sub) use ($user, $authUserId) {
                            $sub->where('from_user_id', $user->id)
                                ->where('to_user_id', $authUserId);
                        })->orWhere(function ($sub) use ($user, $authUserId) {
                            $sub->where('from_user_id', $authUserId)
                                ->where('to_user_id', $user->id);
                        });
                    })
                    ->latest()
                    ->first();

                $unreadCount = Chat::query()
                    ->whereNull('deleted_at')
                    ->whereNull('group_id')
                    ->where('from_user_id', $user->id)
                    ->where('to_user_id', $authUserId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $user->id,
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'employee_status' => $employee->status,
                    'image' => $this->employeeAvatar($employee),
                    'avatar' => $this->employeeAvatar($employee),
                    'last_msg' => $lastMsg?->message ?? 'Keine Nachrichten',
                    'last_msg_at' => $lastMsg?->created_at?->timestamp ?? 0,
                    'unread' => $unreadCount,
                    'is_pinned' => in_array($user->id, $pinnedUserIds, true),
                ];
            })
            ->filter()
            ->sortByDesc('last_msg_at')
            ->values();

        $groupsQuery = ChatGroup::query()
            ->join('chat_group_user', 'chat_group_user.chat_group_id', '=', 'chat_groups.id')
            ->where('chat_group_user.user_id', $authUserId)
            ->whereIn('chat_group_user.status', ['pending', 'accepted']);

        if ($customerId) {
            $groupsQuery->where('chat_groups.customer_id', $customerId);
        }

        $groupsRaw = $groupsQuery
            ->with(['customer', 'alternative', 'product', 'leadProductLine'])
            ->select(
                'chat_groups.*',
                'chat_group_user.role as pivot_role',
                'chat_group_user.status as pivot_status',
                'chat_group_user.history_visibility as pivot_history_visibility',
                'chat_group_user.can_write as pivot_can_write',
                'chat_group_user.joined_at as pivot_joined_at'
            )
            ->get();

        $groups = $groupsRaw
            ->map(function (ChatGroup $group) use ($pinnedGroupIds, $authUserId) {
                $lastMsg = Chat::query()
                    ->whereNull('deleted_at')
                    ->where('group_id', $group->id)
                    ->latest()
                    ->first();

                $customer = $group->customer;
                $alt = $group->alternative;
                $product = $group->product;
                $lead = $group->leadProductLine;

                $customerName = null;
                if ($customer) {
                    $customerName = trim(
                        ($customer->firma ?: '') . ' ' .
                        ($customer->name ?: '') . ' ' .
                        ($customer->lastname ?: '')
                    );
                    if ($customerName === '') {
                        $customerName = 'Kunde ' . $customer->id;
                    }
                }

                $customerAddress = null;
                if ($customer) {
                    $customerAddress = trim(
                        ($customer->street ?: '') . ' ' .
                        ($customer->postcode ?: '') . ' ' .
                        ($customer->city ?: '')
                    );
                }

                $customerPhone = $customer
                    ? (data_get($customer, 'phone')
                        ?? data_get($customer, 'telefon')
                        ?? data_get($customer, 'phone_1')
                        ?? data_get($customer, 'mobile'))
                    : null;

                $unreadCount = Chat::query()
                    ->whereNull('deleted_at')
                    ->where('group_id', $group->id)
                    ->where('from_user_id', '!=', $authUserId)
                    ->whereDoesntHave('readBy', function ($query) use ($authUserId) {
                        $query->where('user_id', $authUserId);
                    })
                    ->count();

                $productLabel = $product?->article_group;
                $altLabel = $alt?->object_name ?: $alt?->city;
                $leadStatus = $lead?->status;

                $metaParts = [];
                if ($customerAddress) {
                    $metaParts[] = $customerAddress;
                }
                if ($altLabel) {
                    $metaParts[] = $altLabel;
                }
                if ($productLabel) {
                    $metaParts[] = $productLabel;
                }

                return [
                    'id' => $group->id,
                    'name' => $group->name ?: ($group->context_label ?? 'Gruppe #' . $group->id),
                    'avatar' => $group->avatar,
                    'customer_id' => $group->customer_id,
                    'alternative_id' => $group->alternative_id,
                    'product_id' => $group->product_id,
                    'lead_product_list_id' => $group->lead_product_list_id,

                    'customer_name' => $customerName,
                    'customer_address' => $customerAddress,
                    'customer_phone' => $customerPhone,
                    'product_label' => $productLabel,
                    'lead_status' => $leadStatus,

                    'context_label' => $group->context_label,
                    'meta_line' => implode(' · ', array_filter($metaParts)),
                    'last_msg' => $lastMsg?->message ?? 'Keine Nachrichten',
                    'last_msg_at' => $lastMsg?->created_at?->timestamp ?? 0,
                    'unread' => $unreadCount,

                    'membership_role' => $group->pivot_role,
                    'membership_status' => $group->pivot_status ?? 'accepted',
                    'history_visibility' => $group->pivot_history_visibility ?? 'all',
                    'can_write' => (bool) ($group->pivot_can_write ?? true),
                    'joined_at' => $group->pivot_joined_at,

                    'is_pinned' => in_array($group->id, $pinnedGroupIds, true),
                ];
            })
            ->sortByDesc('last_msg_at')
            ->values();

        if ($debug) {
            Log::debug('[Chat] getEmployeesAndGroups OUT', [
                'employees_returned' => $employees->count(),
                'groups_returned' => $groups->count(),
            ]);
        }

        // PB-043: dieselbe Bedingung wie oben — vorher stand hier `$request->boolean('debug')`
        // ein zweites Mal. Ein Ausdruck, eine Wahrheit.
        if ($debug) {
            return response()->json([
                'employees' => $employees,
                'groups' => $groups,
                '_debug' => [
                    'auth_user_id' => $authUserId,
                    'auth_employee_id' => $authEmployeeId,
                    'employee_map' => $users->map(fn(User $user) => [
                        'user_id' => $user->id,
                        'employee_id' => (int) $user->name,
                        'has_employee' => (bool) $user->employee,
                        'status' => $user->employee->status ?? null,
                    ])->values(),
                    'group_ids' => $groupsRaw->pluck('id')->values(),
                    'pinned_user_ids' => $pinnedUserIds,
                    'pinned_group_ids' => $pinnedGroupIds,
                ],
            ]);
        }

        return response()->json([
            'employees' => $employees,
            'groups' => $groups,
        ]);
    }

    public function getEmployeeUsers()
    {
        $authUserId = (int) auth()->id();

        $users = User::with('employee')
            ->where('id', '!=', $authUserId)
            ->whereHas('employee', function ($query) {
                $query->where('status', 'Active');
            })
            ->get();

        $employees = $users
            ->map(function (User $user) {
                $employee = $user->employee;

                if (!$employee) {
                    return null;
                }

                return [
                    'id' => $user->id, // users.id for chat, mentions, Echo channels
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'avatar' => $this->employeeAvatar($employee),
                    'image' => $this->employeeAvatar($employee),
                ];
            })
            ->filter()
            ->values();

        return response()->json($employees);
    }

    // ─────────────────────────────────────────────────────────────
    // Messages fetch / readers
    // ─────────────────────────────────────────────────────────────

    public function fetch($otherUserId)
    {
        $authUserId = (int) auth()->id();
        $otherUserId = (int) $otherUserId;

        $justReadIds = Chat::query()
            ->whereNull('deleted_at')
            ->whereNull('group_id')
            ->where('from_user_id', $otherUserId)
            ->where('to_user_id', $authUserId)
            ->where(function ($query) {
                $query->where('is_read', false)
                    ->orWhereNull('read_at');
            })
            ->pluck('id')
            ->all();

        if (!empty($justReadIds)) {
            Chat::whereIn('id', $justReadIds)->update([
                'is_read' => true,
                'read_at' => now(),
                'status' => 'read',
            ]);

            broadcast(new MessageRead(
                senderId: $otherUserId,
                reader: auth()->user(),
                messageIds: $justReadIds,
            ));
        }

        $rows = Chat::with(['fromUser.employee', 'toUser.employee', 'replyTo', 'readBy.employee', 'attachments'])
            ->whereNull('deleted_at')
            ->where(function ($query) use ($authUserId, $otherUserId) {
                $query->where(function ($sub) use ($authUserId, $otherUserId) {
                    $sub->where('from_user_id', $authUserId)
                        ->where('to_user_id', $otherUserId);
                })->orWhere(function ($sub) use ($authUserId, $otherUserId) {
                    $sub->where('from_user_id', $otherUserId)
                        ->where('to_user_id', $authUserId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn(Chat $message) => $this->transformMessage($message));

        return response()->json($rows);
    }

    public function fetchGroupMessages($groupId)
    {
        $authUserId = (int) auth()->id();
        $groupId = (int) $groupId;

        $membership = DB::table('chat_group_user')
            ->where('chat_group_id', $groupId)
            ->where('user_id', $authUserId)
            ->first();

        if (!$membership) {
            return response()->json([], 403);
        }

        if ($membership->status !== 'accepted') {
            return response()->json([]);
        }

        $query = Chat::with(['fromUser.employee', 'toUser.employee', 'replyTo', 'readBy.employee', 'attachments'])
            ->whereNull('deleted_at')
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'asc');

        if ($membership->history_visibility === 'from_join' && $membership->joined_at) {
            $query->where('created_at', '>=', $membership->joined_at);
        }

        $rows = $query->get()
            ->map(fn(Chat $message) => $this->transformMessage($message));

        return response()->json($rows);
    }

    public function readers($id)
    {
        $chat = Chat::with('readBy.employee')->findOrFail($id);

        $readers = $chat->readBy->map(function (User $user) {
            $employee = $user->employee;

            return [
                'id' => $user->id,
                'name' => $employee->name ?? $user->name ?? '',
                'lastname' => $employee->lastname ?? '',
                'read_at' => optional($user->pivot->read_at)->toIso8601String(),
            ];
        })->values();

        return response()->json($readers);
    }

    private function transformMessage(Chat $message): array
    {
        $message->loadMissing([
            'fromUser.employee',
            'toUser.employee',
            'replyTo',
            'readBy.employee',
            'attachments',
        ]);

        $fromUser = $message->fromUser;
        $employee = optional($fromUser)->employee;

        $avatar = $this->employeeAvatar($employee);

        $senderLabel = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
        if ($senderLabel === '' && $fromUser) {
            $senderLabel = $fromUser->name ?: ('User #' . $fromUser->id);
        }

        $attachments = $message->attachments->map(function (ChatAttachment $attachment) {
            return [
                'id' => $attachment->id,
                'url' => $attachment->url,
                'name' => $attachment->name,
                'mime' => $attachment->mime,
                'size' => $attachment->size,
                'is_image' => (bool) $attachment->is_image,
            ];
        })->values()->all();

        $audioUrl = null;
        $audioMime = null;
        $fileUrl = null;

        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            if ($message->type === 'audio') {
                $audioUrl = Storage::disk('public')->url($message->file_path);
                try {
                    $audioMime = Storage::disk('public')->mimeType($message->file_path);
                } catch (\Throwable $exception) {
                    $audioMime = 'audio/webm';
                }
            } elseif (in_array($message->type, ['image', 'file'], true)) {
                $fileUrl = Storage::disk('public')->url($message->file_path);
            }
        }

        $replyPreview = null;
        if ($message->reply_to_id && $message->replyTo) {
            $original = $message->replyTo;
            $base = $original->message
                ?? ($original->type === 'audio'
                    ? 'Sprachnachricht'
                    : (in_array($original->type, ['image', 'file'], true) ? 'Anhang' : ''));
            $replyPreview = mb_strimwidth((string) $base, 0, 120, '…');
        }

        $readBy = collect();

        foreach ($message->readBy as $user) {
            $readerEmployee = $user->employee;
            $readBy->push([
                'id' => $user->id,
                'name' => $readerEmployee->name ?? $user->name ?? '',
                'lastname' => $readerEmployee->lastname ?? '',
                'read_at' => optional($user->pivot->read_at)->toIso8601String(),
            ]);
        }

        if (!$message->group_id && $message->to_user_id && $message->is_read && $message->read_at) {
            $reader = $message->toUser;
            if ($reader) {
                $readerEmployee = $reader->employee;
                $alreadyAdded = $readBy->contains(fn($item) => (int) $item['id'] === (int) $reader->id);

                if (!$alreadyAdded) {
                    $readBy->push([
                        'id' => $reader->id,
                        'name' => $readerEmployee->name ?? $reader->name ?? '',
                        'lastname' => $readerEmployee->lastname ?? '',
                        'read_at' => optional($message->read_at)->toIso8601String(),
                    ]);
                }
            }
        }

        return [
            'id' => $message->id,
            'message' => $message->message,
            'type' => $message->type,
            'status' => $message->status,
            'from_user_id' => $message->from_user_id,
            'to_user_id' => $message->to_user_id,
            'group_id' => $message->group_id,
            'created_at' => optional($message->created_at)?->toIso8601String(),
            'deleted_at' => optional($message->deleted_at)?->toIso8601String(),
            'is_read' => (bool) $message->is_read,
            'reply_to_id' => $message->reply_to_id,
            'reply_to_preview' => $replyPreview,
            'shared_from_id' => $message->shared_from_id,

            'from_user' => [
                'id' => optional($fromUser)->id,
                'name' => (string) ($employee->name ?? ''),
                'lastname' => (string) ($employee->lastname ?? ''),
                'image' => $avatar,
            ],

            'sender_label' => $senderLabel,
            'attachments' => $attachments,
            'audio_url' => $audioUrl,
            'audio_mime' => $audioMime,
            'file_url' => $fileUrl,
            'file_path' => $message->file_path,
            'readers' => $readBy->values()->all(),
            'read_by' => $readBy->values()->all(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Unread / read receipts
    // ─────────────────────────────────────────────────────────────

    public function unreadCounts()
    {
        $authUserId = (int) auth()->id();

        $privateCounts = Chat::query()
            ->whereNull('deleted_at')
            ->whereNull('group_id')
            ->where('to_user_id', $authUserId)
            ->where('is_read', false)
            ->select('from_user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('from_user_id')
            ->pluck('total', 'from_user_id');

        $groupCounts = Chat::query()
            ->whereNull('deleted_at')
            ->whereNotNull('group_id')
            ->whereIn('group_id', function ($query) use ($authUserId) {
                $query->select('chat_group_id')
                    ->from('chat_group_user')
                    ->where('user_id', $authUserId)
                    ->where('status', 'accepted');
            })
            ->where('from_user_id', '!=', $authUserId)
            ->whereDoesntHave('readBy', function ($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            })
            ->select('group_id', DB::raw('COUNT(*) as total'))
            ->groupBy('group_id')
            ->pluck('total', 'group_id');

        return response()->json([
            'private' => $privateCounts,
            'groups' => $groupCounts,
        ]);
    }

    public function markAsRead($userId)
    {
        $reader = auth()->user();
        $userId = (int) $userId;

        $messages = Chat::query()
            ->whereNull('deleted_at')
            ->whereNull('group_id')
            ->where('from_user_id', $userId)
            ->where('to_user_id', $reader->id)
            ->where(function ($query) {
                $query->where('is_read', false)
                    ->orWhereNull('read_at');
            })
            ->get();

        if ($messages->isEmpty()) {
            return response()->json(['success' => true]);
        }

        $ids = $messages->pluck('id')->all();

        Chat::whereIn('id', $ids)->update([
            'read_at' => now(),
            'is_read' => true,
            'status' => 'read',
        ]);

        broadcast(new MessageRead(
            senderId: $userId,
            reader: $reader,
            messageIds: $ids,
        ));

        return response()->json(['success' => true]);
    }

    public function markRead(User $other)
    {
        return $this->markAsRead($other->id);
    }

    public function markGroupRead($groupId)
    {
        $authUserId = (int) auth()->id();
        $groupId = (int) $groupId;

        $membership = DB::table('chat_group_user')
            ->where('chat_group_id', $groupId)
            ->where('user_id', $authUserId)
            ->where('status', 'accepted')
            ->first();

        if (!$membership) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        $messages = Chat::with('readBy.employee')
            ->whereNull('deleted_at')
            ->where('group_id', $groupId)
            ->where('from_user_id', '!=', $authUserId)
            ->whereDoesntHave('readBy', function ($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            })
            ->get();

        if ($messages->isEmpty()) {
            return response()->json(['success' => true]);
        }

        foreach ($messages as $message) {
            $message->readBy()->syncWithoutDetaching([
                $authUserId => ['read_at' => now()],
            ]);

            $message->load('readBy.employee');
            broadcast(new GroupMessageRead($message));
        }

        return response()->json(['success' => true]);
    }

    public function markReadGroup($groupId)
    {
        return $this->markGroupRead($groupId);
    }

    // ─────────────────────────────────────────────────────────────
    // Mentions: persistent @employee notifications
    // ─────────────────────────────────────────────────────────────

    public function unreadMentions()
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

    public function markMentionRead(ChatMention $mention)
    {
        abort_unless((int) $mention->mentioned_user_id === (int) auth()->id(), 403);

        if (!$mention->read_at) {
            $mention->update([
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function createMentionsForChat(Chat $chat, array $mentionedUserIds = []): void
    {
        $authUserId = (int) auth()->id();

        $mentionedUserIds = collect($mentionedUserIds)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0 && $id !== $authUserId)
            ->unique()
            ->values();

        if ($mentionedUserIds->isEmpty()) {
            return;
        }

        foreach ($mentionedUserIds as $mentionedUserId) {
            if (!$this->canMentionUserInChat($chat, $mentionedUserId)) {
                continue;
            }

            $mention = ChatMention::firstOrCreate([
                'chat_id' => $chat->id,
                'mentioned_user_id' => $mentionedUserId,
            ], [
                'group_id' => $chat->group_id,
                'mentioned_by_user_id' => $authUserId,
                'read_at' => null,
            ]);

            $mention->loadMissing(['chat.fromUser.employee', 'mentionedBy.employee', 'group']);

            broadcast(new ChatMentionCreated($mention))->toOthers();
        }
    }

    private function canMentionUserInChat(Chat $chat, int $mentionedUserId): bool
    {
        if ($chat->group_id) {
            return DB::table('chat_group_user')
                ->where('chat_group_id', $chat->group_id)
                ->where('user_id', $mentionedUserId)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();
        }

        return (int) $chat->to_user_id === $mentionedUserId;
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

        $groupName = $mention->group?->name ?: ($mention->group?->context_label ?: 'Chat');
        $messageText = (string) ($mention->chat?->message ?? 'Du wurdest in einer Nachricht markiert.');

        return [
            'id' => $mention->id,
            'chat_id' => $mention->chat_id,
            'group_id' => $mention->group_id,
            'mentioned_by_user_id' => $mention->mentioned_by_user_id,
            'sender_name' => $senderName,
            'sender_avatar' => $this->employeeAvatar($employee),
            'group_name' => $groupName,
            'message' => mb_strimwidth($messageText, 0, 160, '…'),
            'created_at' => optional($mention->created_at)?->toIso8601String(),
            'open_url' => $this->buildMentionOpenUrl($mention),
        ];
    }

    private function buildMentionOpenUrl(ChatMention $mention): string
    {
        $mention->loadMissing(['chat']);

        $params = [
            'message_id' => $mention->chat_id,
        ];

        if ($mention->group_id) {
            $params['group_id'] = $mention->group_id;
        } else {
            // For direct chat, open the sender chat.
            $params['user_id'] = $mention->mentioned_by_user_id;
        }

        return url('/admin/chat?' . http_build_query($params));
    }

    // ─────────────────────────────────────────────────────────────
    // Send / edit / delete
    // ─────────────────────────────────────────────────────────────

    public function send(Request $request)
    {
        $authUserId = (int) auth()->id();

        $request->validate([
            'to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'group_id' => ['nullable', 'integer', 'exists:chat_groups,id'],
            'reply_to_id' => ['nullable', 'integer', 'exists:chats,id'],
            'edit_id' => ['nullable', 'integer', 'exists:chats,id'],
            'type' => ['nullable', Rule::in(['text', 'file', 'image', 'audio'])],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:20480'],
            'file' => ['nullable', 'file', 'max:20480'],
            'voice' => ['nullable', 'file', 'max:20480'],
            'image' => ['nullable', 'image', 'max:20480'],
            'message' => ['nullable', 'string'],
            'mentions' => ['nullable', 'array'],
            'mentions.*' => ['integer', 'exists:users,id'],
        ]);

        if ($request->filled('edit_id')) {
            return $this->updateMessageText($request);
        }

        if (!($request->filled('to_user_id') xor $request->filled('group_id'))) {
            return response()->json([
                'error' => 'Provide exactly one of to_user_id or group_id',
            ], 422);
        }

        $targetUserId = $request->filled('to_user_id') ? (int) $request->input('to_user_id') : null;
        $targetGroupId = $request->filled('group_id') ? (int) $request->input('group_id') : null;
        $replyToId = $request->integer('reply_to_id') ?: null;
        $text = (string) $request->input('message', '');
        $mentions = $request->input('mentions', []);

        if ($targetGroupId) {
            $guardResponse = $this->guardGroupWriteAccess($targetGroupId, $authUserId);
            if ($guardResponse) {
                return $guardResponse;
            }
        }

        if ($request->hasFile('files')) {
            return $this->sendGroupedFiles(
                request: $request,
                authUserId: $authUserId,
                targetUserId: $targetUserId,
                targetGroupId: $targetGroupId,
                replyToId: $replyToId,
                text: $text,
                mentions: is_array($mentions) ? $mentions : []
            );
        }

        return $this->sendSingleMessageOrUpload(
            request: $request,
            authUserId: $authUserId,
            targetUserId: $targetUserId,
            targetGroupId: $targetGroupId,
            replyToId: $replyToId,
            text: $text,
            mentions: is_array($mentions) ? $mentions : []
        );
    }

    private function updateMessageText(Request $request)
    {
        $authUserId = (int) auth()->id();
        $chat = Chat::findOrFail($request->integer('edit_id'));

        abort_unless((int) $chat->from_user_id === $authUserId, 403, 'Not your message');

        $message = trim((string) $request->input('message', ''));
        if ($message === '') {
            return response()->json(['error' => 'Empty message'], 422);
        }

        $chat->update([
            'message' => $message,
            'type' => 'text',
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($chat->fresh()),
        ]);
    }

    private function sendGroupedFiles(
        Request $request,
        int $authUserId,
        ?int $targetUserId,
        ?int $targetGroupId,
        ?int $replyToId,
        string $text,
        array $mentions
    ) {
        $files = collect((array) $request->file('files'))->filter()->values();

        if ($files->isEmpty()) {
            return response()->json(['error' => 'No files uploaded'], 422);
        }

        $allImages = $files->every(fn($file) => str_starts_with((string) $file->getMimeType(), 'image/'));
        $dbType = $allImages ? 'image' : 'file';
        $firstStoredPath = null;

        $chat = DB::transaction(function () use ($files, $authUserId, $targetUserId, $targetGroupId, $replyToId, $text, $dbType, &$firstStoredPath) {
            $chat = Chat::create([
                'from_user_id' => $authUserId,
                'to_user_id' => $targetUserId,
                'group_id' => $targetGroupId,
                'reply_to_id' => $replyToId,
                'type' => $dbType,
                'message' => trim($text) !== '' ? $text : null,
                'file_path' => null,
                'status' => 'sent',
                'is_read' => false,
            ]);

            if ($targetGroupId) {
                $chat->readBy()->syncWithoutDetaching([
                    $authUserId => ['read_at' => now()],
                ]);
            }

            foreach ($files as $file) {
                $mime = (string) $file->getMimeType();
                $isImage = str_starts_with($mime, 'image/');
                $dir = $isImage ? 'chat/images' : 'chat/files';
                $storedPath = $file->store($dir, 'public');

                if (!$firstStoredPath) {
                    $firstStoredPath = $storedPath;
                }

                ChatAttachment::create([
                    'chat_id' => $chat->id,
                    'disk' => 'public',
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $mime,
                    'size' => $file->getSize(),
                    'is_image' => $isImage,
                ]);
            }

            $chat->update([
                'file_path' => $firstStoredPath,
            ]);

            return $chat;
        });

        $chat->load(['fromUser.employee', 'attachments', 'readBy.employee']);

        $this->createMentionsForChat($chat, $mentions);

        broadcast(new MessageSent($chat))->toOthers();

        $message = $this->transformMessage($chat);

        return response()->json([
            'success' => true,
            'message' => $message,
            'messages' => [$message],
        ]);
    }

    private function sendSingleMessageOrUpload(
        Request $request,
        int $authUserId,
        ?int $targetUserId,
        ?int $targetGroupId,
        ?int $replyToId,
        string $text,
        array $mentions
    ) {
        $dbType = 'text';
        $upload = null;

        if ($request->hasFile('voice')) {
            $upload = $request->file('voice');
            $dbType = 'audio';
        } elseif ($request->hasFile('image')) {
            $upload = $request->file('image');
            $dbType = 'image';
        } elseif ($request->hasFile('file')) {
            $upload = $request->file('file');
            $dbType = str_starts_with((string) $upload->getMimeType(), 'image/') ? 'image' : 'file';
        }

        if ($dbType === 'text' && trim($text) === '') {
            return response()->json(['error' => 'Empty message'], 422);
        }

        $storedPath = null;
        if ($upload) {
            $dir = match ($dbType) {
                'audio' => 'chat/voice',
                'image' => 'chat/images',
                default => 'chat/files',
            };

            $storedPath = $upload->store($dir, 'public');
        }

        $chat = Chat::create([
            'from_user_id' => $authUserId,
            'to_user_id' => $targetUserId,
            'group_id' => $targetGroupId,
            'reply_to_id' => $replyToId,
            'type' => $dbType,
            'message' => $dbType === 'text' ? $text : ($text ?: null),
            'file_path' => $storedPath,
            'status' => 'sent',
            'is_read' => false,
        ]);

        if ($targetGroupId) {
            $chat->readBy()->syncWithoutDetaching([
                $authUserId => ['read_at' => now()],
            ]);
        }

        if ($upload && in_array($dbType, ['image', 'file'], true) && $storedPath) {
            ChatAttachment::create([
                'chat_id' => $chat->id,
                'disk' => 'public',
                'path' => $storedPath,
                'original_name' => $upload->getClientOriginalName(),
                'mime' => $upload->getMimeType(),
                'size' => $upload->getSize(),
                'is_image' => $dbType === 'image',
            ]);
        }

        $chat->load(['fromUser.employee', 'attachments', 'readBy.employee']);

        $this->createMentionsForChat($chat, $mentions);

        broadcast(new MessageSent($chat))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $this->transformMessage($chat),
        ]);
    }

    private function guardGroupWriteAccess(int $groupId, int $authUserId)
    {
        $membership = DB::table('chat_group_user')
            ->where('chat_group_id', $groupId)
            ->where('user_id', $authUserId)
            ->first();

        if (!$membership) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        if ($membership->status !== 'accepted') {
            return response()->json(['error' => 'Invitation not accepted'], 403);
        }

        if (!(bool) $membership->can_write) {
            return response()->json(['error' => 'Read-only in this group'], 403);
        }

        return null;
    }

    public function destroy($id)
    {
        $authUserId = (int) auth()->id();
        $chat = Chat::findOrFail($id);

        if ((int) $chat->from_user_id !== $authUserId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $chat->delete();

        return response()->json([
            'success' => true,
            'message_id' => $chat->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Groups
    // ─────────────────────────────────────────────────────────────

    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'members' => ['required', 'array', 'min:1'],
            'members.*.id' => ['required', 'integer', 'exists:users,id'],
            'members.*.role' => ['nullable', Rule::in(['member', 'moderator', 'admin'])],
            'members.*.can_write' => ['nullable', 'boolean'],
            'members.*.history_visibility' => ['nullable', Rule::in(['all', 'from_join'])],
            'customer_id' => ['nullable', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ]);

        $authUserId = (int) auth()->id();

        $group = DB::transaction(function () use ($data, $authUserId) {
            $group = ChatGroup::create([
                'name' => $data['name'],
                'created_by' => $authUserId,
                'customer_id' => $data['customer_id'] ?? null,
                'alternative_id' => $data['alternative_id'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'lead_product_list_id' => $data['lead_product_list_id'] ?? null,
                'avatar' => $data['avatar'] ?? null,
            ]);

            $members = [];

            foreach ($data['members'] as $member) {
                $userId = (int) $member['id'];
                if ($userId === $authUserId) {
                    continue;
                }

                $members[$userId] = [
                    'role' => $member['role'] ?? 'member',
                    'status' => 'pending',
                    'invited_by' => $authUserId,
                    'joined_at' => null,
                    'history_visibility' => $member['history_visibility'] ?? 'from_join',
                    'can_write' => array_key_exists('can_write', $member) ? (bool) $member['can_write'] : true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $members[$authUserId] = [
                'role' => 'admin',
                'status' => 'accepted',
                'invited_by' => $authUserId,
                'joined_at' => now(),
                'history_visibility' => 'all',
                'can_write' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $group->users()->sync($members);

            return $group->fresh(['users.employee', 'customer', 'alternative', 'product', 'leadProductLine']);
        });

        foreach ($group->users as $user) {
            broadcast(new GroupMembershipUpdated($group, (int) $user->id))->toOthers();
        }

        return response()->json([
            'success' => true,
            'group_id' => $group->id,
            'group' => $group,
            'message' => 'Gruppe wurde erstellt. Eingeladene Mitglieder müssen die Einladung annehmen.',
        ]);
    }

    public function leaveGroup(Request $request)
    {
        $request->validate([
            'group_id' => ['required', 'integer', 'exists:chat_groups,id'],
        ]);

        $group = ChatGroup::findOrFail($request->integer('group_id'));
        $group->users()->detach(auth()->id());

        return response()->json(['success' => true]);
    }

    public function getGroupUsers($groupId)
    {
        $users = DB::table('chat_group_user')
            ->join('users', 'chat_group_user.user_id', '=', 'users.id')
            ->join('employees', 'employees.id', '=', 'users.name')
            ->where('chat_group_user.chat_group_id', $groupId)
            ->whereIn('chat_group_user.status', ['pending', 'accepted'])
            ->select(
                'users.id',
                'employees.name',
                'employees.lastname',
                'employees.image as avatar',
                'chat_group_user.role',
                'chat_group_user.status'
            )
            ->get()
            ->map(function ($user) {
                $user->avatar = $user->avatar
                    ? asset('images/employee/' . ltrim($user->avatar, '/'))
                    : asset('images/gender/users.png');
                return $user;
            });

        return response()->json($users);
    }

    // ─────────────────────────────────────────────────────────────
    // Customer context search
    // ─────────────────────────────────────────────────────────────

    public function searchCustomerContexts(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $like = '%' . $term . '%';

        $rows = LeadProductList::query()
            ->join('new_leads', 'lead_product_lists.customer_id', '=', 'new_leads.id')
            ->leftJoin('lead_alternative_adds', 'lead_product_lists.alternative_id', '=', 'lead_alternative_adds.id')
            ->leftJoin('article_groups', 'lead_product_lists.product_id', '=', 'article_groups.id')
            ->whereNull('lead_product_lists.deleted_at')
            ->where(function ($query) use ($like) {
                $query->where('new_leads.firma', 'like', $like)
                    ->orWhere('new_leads.name', 'like', $like)
                    ->orWhere('new_leads.lastname', 'like', $like)
                    ->orWhere('new_leads.customer_no', 'like', $like)
                    ->orWhere('new_leads.city', 'like', $like)
                    ->orWhere('new_leads.street', 'like', $like)
                    ->orWhere('article_groups.article_group', 'like', $like);
            })
            ->orderByDesc('lead_product_lists.created_at')
            ->limit(30)
            ->get([
                'lead_product_lists.id as lpl_id',
                'new_leads.id as customer_id',
                'new_leads.firma',
                'new_leads.name as cust_name',
                'new_leads.lastname',
                'new_leads.street',
                'new_leads.postcode',
                'new_leads.city',
                'article_groups.id as product_id',
                'article_groups.article_group',
                'lead_alternative_adds.id as alternative_id',
                'lead_alternative_adds.object_name',
            ]);

        $results = $rows->map(function ($row) {
            $customerLabel = trim(
                ($row->firma ?: '') . ' ' .
                ($row->cust_name ?: '') . ' ' .
                ($row->lastname ?: '')
            );

            if ($customerLabel === '') {
                $customerLabel = 'Kunde ' . $row->customer_id;
            }

            $address = trim(
                ($row->street ?: '') . ' ' .
                ($row->postcode ?: '') . ' ' .
                ($row->city ?: '')
            );

            $productLabel = $row->article_group ?: 'Produkt';
            $altLabel = $row->object_name;

            $line2Parts = [];
            if ($address !== '') {
                $line2Parts[] = $address;
            }
            if ($altLabel) {
                $line2Parts[] = $altLabel;
            }
            $line2Parts[] = $productLabel;

            return [
                'id' => $row->lpl_id,
                'text' => $customerLabel,
                'line2' => implode(' · ', array_filter($line2Parts)),
                'customer_id' => $row->customer_id,
                'alternative_id' => $row->alternative_id,
                'product_id' => $row->product_id,
                'product_label' => $productLabel,
            ];
        });

        return response()->json($results);
    }

    // ─────────────────────────────────────────────────────────────
    // Learning/tutorial JSON
    // ─────────────────────────────────────────────────────────────

    public function learning(Request $request)
    {
        $query = $request->get('q');

        $topics = LearningTopic::query()
            ->where('is_active', true)
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('title', 'like', '%' . $query . '%')
                        ->orWhere('short_intro', 'like', '%' . $query . '%')
                        ->orWhere('prompt_label', 'like', '%' . $query . '%');
                });
            })
            ->orderBy('difficulty')
            ->orderBy('estimated_minutes')
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'slug',
                'prompt_label',
                'short_intro',
                'estimated_minutes',
                'difficulty',
                'created_at',
            ]);

        return response()->json([
            'topics' => $topics,
        ]);
    }

    public function learningShow(LearningTopic $topic)
    {
        $topic->load([
            'media' => function ($query) {
                $query->orderBy('sort_order');
            },
        ]);

        return response()->json([
            'topic' => [
                'id' => $topic->id,
                'title' => $topic->title,
                'slug' => $topic->slug,
                'prompt_label' => $topic->prompt_label,
                'short_intro' => $topic->short_intro,
                'body' => $topic->body,
                'estimated_minutes' => $topic->estimated_minutes,
                'difficulty' => $topic->difficulty,
                'created_at' => $topic->created_at,
            ],
            'media' => $topic->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'media_type' => $media->media_type,
                    'title' => $media->title,
                    'description' => $media->description,
                    'url' => asset('storage/' . ltrim($media->file_path, '/')),
                    'mime_type' => $media->mime_type,
                    'meta' => $media->meta,
                ];
            }),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Share message
    // Fixed: recipients are users.id, not employees.id.
    // ─────────────────────────────────────────────────────────────

    public function share(Request $request)
    {
        $authUserId = (int) auth()->id();

        $data = $request->validate([
            'message_id' => ['required', 'integer', 'exists:chats,id'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['integer', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $original = Chat::with(['fromUser.employee', 'attachments'])
            ->findOrFail($data['message_id']);

        if (!$this->currentUserCanAccessMessage($original, $authUserId)) {
            abort(403, 'You are not allowed to share this message.');
        }

        $sharedMessages = [];

        DB::transaction(function () use ($data, $original, $authUserId, &$sharedMessages) {
            $recipientIds = collect($data['recipients'])
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0 && $id !== $authUserId)
                ->unique()
                ->values();

            foreach ($recipientIds as $targetUserId) {
                $baseText = (string) $original->message;
                $note = trim((string) ($data['note'] ?? ''));

                $finalText = $baseText;
                if ($note !== '') {
                    $finalText = $note . "\n\n---\nGeteilte Nachricht:\n" . $baseText;
                }

                if ($finalText === '' && in_array($original->type, ['image', 'file', 'audio'], true)) {
                    $finalText = '[Geteilter Anhang]';
                }

                $newMessage = Chat::create([
                    'from_user_id' => $authUserId,
                    'to_user_id' => $targetUserId,
                    'group_id' => null,
                    'type' => $original->type ?? 'text',
                    'message' => $finalText,
                    'reply_to_id' => null,
                    'shared_from_id' => $original->id,
                    'file_path' => $original->file_path,
                    'status' => 'sent',
                    'is_read' => false,
                ]);

                foreach ($original->attachments as $attachment) {
                    ChatAttachment::create([
                        'chat_id' => $newMessage->id,
                        'disk' => $attachment->disk,
                        'path' => $attachment->path,
                        'original_name' => $attachment->original_name,
                        'mime' => $attachment->mime,
                        'size' => $attachment->size,
                        'is_image' => (bool) $attachment->is_image,
                    ]);
                }

                $newMessage->load(['fromUser.employee', 'attachments', 'readBy.employee']);
                $sharedMessages[] = $newMessage;
            }
        });

        foreach ($sharedMessages as $message) {
            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Message shared successfully.',
            'shared_count' => count($sharedMessages),
            'shared_messages' => collect($sharedMessages)
                ->map(fn(Chat $message) => $this->transformMessage($message))
                ->values(),
        ]);
    }

    private function currentUserCanAccessMessage(Chat $message, int $authUserId): bool
    {
        if ((int) $message->from_user_id === $authUserId || (int) $message->to_user_id === $authUserId) {
            return true;
        }

        if ($message->group_id) {
            return DB::table('chat_group_user')
                ->where('chat_group_id', $message->group_id)
                ->where('user_id', $authUserId)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();
        }

        return false;
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function employeeAvatar(?Employee $employee): string
    {
        if ($employee && $employee->image) {
            $image = (string) $employee->image;

            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $image;
            }

            return asset('images/employee/' . ltrim($image, '/'));
        }

        return asset('images/gender/users.png');
    }
}
