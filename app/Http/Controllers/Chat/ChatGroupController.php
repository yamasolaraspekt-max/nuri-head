<?php


namespace App\Http\Controllers\Chat;
use App\Http\Controllers\Controller;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\NewLeads;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Validation\Rule;
use App\Events\GroupMembershipUpdated;

class ChatGroupController extends Controller
{

    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|integer|exists:users,id',
            'members.*.role' => 'nullable|string|in:member,moderator,admin',
            'members.*.can_write' => 'nullable|boolean',
            'members.*.history_visibility' => 'nullable|in:all,from_join',

            'customer_id' => 'nullable|integer|exists:new_leads,id',
            'alternative_id' => 'nullable|integer|exists:lead_alternative_adds,id',
            'product_id' => 'nullable|integer|exists:article_groups,id',
            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'avatar' => 'nullable|string',
        ]);

        $authUserId = (int) auth()->id();

        $context = [
            'customer_id' => $data['customer_id'] ?? null,
            'alternative_id' => $data['alternative_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'lead_product_list_id' => $data['lead_product_list_id'] ?? null,
        ];

        $hasContext = collect($context)->filter(fn($v) => !is_null($v))->isNotEmpty();

        if ($hasContext) {
            $existing = ChatGroup::query()
                ->where('customer_id', $context['customer_id'])
                ->where('alternative_id', $context['alternative_id'])
                ->where('product_id', $context['product_id'])
                ->where('lead_product_list_id', $context['lead_product_list_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'duplicate' => true,
                    'message' => 'Chat-Gruppe für diesen Kontext existiert bereits.',
                    'group' => $existing->load('users'),
                ], 200);
            }
        }

        $group = DB::transaction(function () use ($data, $authUserId) {
            $group = ChatGroup::create([
                'name' => $data['name'],
                'avatar' => $data['avatar'] ?? null,
                'created_by' => $authUserId,
                'customer_id' => $data['customer_id'] ?? null,
                'alternative_id' => $data['alternative_id'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'lead_product_list_id' => $data['lead_product_list_id'] ?? null,
            ]);

            $members = [];
            foreach ($data['members'] as $row) {
                $userId = (int) $row['id'];
                if ($userId === $authUserId) {
                    continue;
                }

                $members[$userId] = [
                    'role' => $row['role'] ?? 'member',
                    'status' => 'pending',
                    'invited_by' => $authUserId,
                    'joined_at' => null,
                    'history_visibility' => $row['history_visibility'] ?? 'from_join',
                    'can_write' => array_key_exists('can_write', $row) ? (bool) $row['can_write'] : true,
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
            'duplicate' => false,
            'group' => $group,
            'message' => 'Gruppe wurde erstellt. Eingeladene Mitglieder müssen die Einladung annehmen.',
        ]);
    }

    public function searchCustomers(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $rows = NewLeads::query()
            ->whereNull('deleted_at')
            ->where(function ($qb) use ($q) {
                $qb->where('customer_no', 'like', "%{$q}%")
                    ->orWhere('firma', 'like', "%{$q}%")
                    ->orWhere('lastname', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $out = $rows->map(function (NewLeads $lead) {
            $display = trim(
                ($lead->firma ?: '') . ' ' .
                ($lead->name ?: '') . ' ' .
                ($lead->lastname ?: '')
            );

            if ($display === '') {
                $display = $lead->customer_no ?: 'Kunde ' . $lead->id;
            }

            return [
                'id' => $lead->id,
                'display_name' => $display,
                'customer_no' => $lead->customer_no,
                'city' => $lead->city,
            ];
        });

        return response()->json($out);
    }

    public function update(Request $request, $id)
    {
        $group = ChatGroup::findOrFail($id);

        if ($group->created_by !== Auth::id()) {
            return response()->json(['message' => 'Sie dürfen diese Gruppe nicht bearbeiten.'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|integer|exists:users,id',
            'members.*.role' => 'nullable|string|in:member,moderator,admin',
            'members.*.can_write' => 'nullable|boolean',
            'members.*.history_visibility' => 'nullable|in:all,from_join',
            'members.*.status' => 'nullable|in:pending,accepted,declined',

            'customer_id' => 'nullable|integer|exists:new_leads,id',
            'alternative_id' => 'nullable|integer|exists:lead_alternative_adds,id',
            'product_id' => 'nullable|integer|exists:article_groups,id',
            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'avatar' => 'nullable|string',
        ]);

        $authUserId = (int) Auth::id();
        $memberIdsForRealtime = [];

        $group = DB::transaction(function () use ($group, $data, $authUserId, &$memberIdsForRealtime) {
            $group->update([
                'name' => $data['name'],
                'customer_id' => array_key_exists('customer_id', $data) ? $data['customer_id'] : $group->customer_id,
                'alternative_id' => array_key_exists('alternative_id', $data) ? $data['alternative_id'] : $group->alternative_id,
                'product_id' => array_key_exists('product_id', $data) ? $data['product_id'] : $group->product_id,
                'lead_product_list_id' => array_key_exists('lead_product_list_id', $data) ? $data['lead_product_list_id'] : $group->lead_product_list_id,
                'avatar' => $data['avatar'] ?? $group->avatar,
            ]);

            $existing = DB::table('chat_group_user')
                ->where('chat_group_id', $group->id)
                ->get()
                ->keyBy('user_id');

            $sync = [];

            foreach ($data['members'] as $member) {
                $userId = (int) $member['id'];
                if ($userId === $authUserId) {
                    continue;
                }

                $old = $existing->get($userId);
                $status = $old->status ?? ($member['status'] ?? 'pending');
                if (!in_array($status, ['pending', 'accepted', 'declined'], true)) {
                    $status = 'pending';
                }

                $sync[$userId] = [
                    'role' => $member['role'] ?? ($old->role ?? 'member'),
                    'status' => $status,
                    'invited_by' => $old->invited_by ?? $authUserId,
                    'joined_at' => $old->joined_at ?? ($status === 'accepted' ? now() : null),
                    'history_visibility' => $member['history_visibility'] ?? ($old->history_visibility ?? 'from_join'),
                    'can_write' => array_key_exists('can_write', $member) ? (bool) $member['can_write'] : (bool) ($old->can_write ?? true),
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => now(),
                ];
            }

            $sync[$authUserId] = [
                'role' => 'admin',
                'status' => 'accepted',
                'invited_by' => $existing->get($authUserId)->invited_by ?? $authUserId,
                'joined_at' => $existing->get($authUserId)->joined_at ?? now(),
                'history_visibility' => 'all',
                'can_write' => true,
                'created_at' => $existing->get($authUserId)->created_at ?? now(),
                'updated_at' => now(),
            ];

            $group->users()->sync($sync);
            $memberIdsForRealtime = array_keys($sync);

            return $group->fresh(['users.employee', 'customer', 'alternative', 'product', 'leadProductLine']);
        });

        foreach ($memberIdsForRealtime as $uid) {
            broadcast(new GroupMembershipUpdated($group, (int) $uid))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Gruppe wurde aktualisiert.',
            'group' => $group,
        ]);
    }


    public function getUsers($id)
    {
        $group = ChatGroup::with('users:id,name,lastname')->findOrFail($id);

        return response()->json($group->users);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'image|max:2048',
        ]);

        $path = $request->file('avatar')->store('chat_group_avatars', 'public');

        return response()->json(['path' => '/storage/' . $path]);
    }

    public function addMembers(Request $request, $groupId)
    {
        $group = ChatGroup::findOrFail($groupId);

        $request->validate([
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|integer|exists:users,id',
            'members.*.role' => ['required', Rule::in(['member', 'moderator', 'admin'])],
            'members.*.history_visibility' => ['nullable', Rule::in(['all', 'from_join'])],
            'members.*.can_write' => ['nullable', 'boolean'],
        ]);

        $members = $request->input('members', []);

        // write / update pivot rows
        foreach ($members as $memberPayload) {
            $userId = (int) $memberPayload['id'];

            $group->users()->syncWithoutDetaching([
                $userId => [
                    'role' => $memberPayload['role'] ?? 'member',
                    'status' => 'pending',
                    'invited_by' => Auth::id(),
                    'history_visibility' => $memberPayload['history_visibility'] ?? 'from_join',
                    'can_write' => $memberPayload['can_write'] ?? true,
                    'joined_at' => null,
                ],
            ]);

            // 🔴 here is the snippet I suggested
            broadcast(new GroupMembershipUpdated($group->fresh(), $userId))->toOthers();
        }

        // return updated members for right sidebar
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
            ->get();


        return response()->json([
            'success' => true,
            'members' => $users,
        ]);
    }


    public function createFromPrivate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|integer|exists:users,id',
            'name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|integer|exists:new_leads,id',
            'alternative_id' => 'nullable|integer|exists:lead_alternative_adds,id',
            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'avatar' => 'nullable|string',
        ]);

        $authUser = auth()->user();
        $authUserId = $authUser->id;

        $group = ChatGroup::create([
            'name' => $request->name,
            'created_by' => $authUserId,
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'lead_product_list_id' => $request->lead_product_list_id,
            'avatar' => $request->avatar,
        ]);

        $attach = [];

        foreach ($request->user_ids as $uid) {
            $attach[(int) $uid] = [
                'role' => 'member',
                'status' => 'pending',
                'invited_by' => $authUserId,
                'joined_at' => null,
                'history_visibility' => 'from_join',
                'can_write' => true,
            ];
        }

        // creator
        $attach[$authUserId] = [
            'role' => 'admin',
            'status' => 'accepted',
            'invited_by' => $authUserId,
            'joined_at' => now(),
            'history_visibility' => 'all',
            'can_write' => true,
        ];

        $group->users()->attach($attach);

        // optional system message as you already had...

        return response()->json([
            'success' => true,
            'group_id' => $group->id,
            'group' => $group->fresh('users'),
        ]);
    }


    public function removeMember($groupId, $userId)
    {
        $group = ChatGroup::findOrFail($groupId);

        // membership is on users.id in chat_group_user
        $group->users()->detach($userId);

        $members = DB::table('chat_group_user')
            ->join('users', 'chat_group_user.user_id', '=', 'users.id')
            ->join('employees', 'employees.id', '=', 'users.name')
            ->where('chat_group_user.chat_group_id', $group->id)
            ->whereIn('chat_group_user.status', ['pending', 'accepted'])
            ->select(
                'users.id',
                'employees.name',
                'employees.lastname',
                'employees.image as avatar',
                'chat_group_user.role',
                'chat_group_user.status'
            )
            ->get();


        return response()->json([
            'members' => $members,
        ]);
    }

    public function destroy($id)
    {
        $group = ChatGroup::findOrFail($id);

        $isCreator = $group->created_by == Auth::id();

        $isAdmin = DB::table('chat_group_user')
            ->where('chat_group_id', $id)
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->exists();

        if (!$isCreator && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        \Log::info('is_admin:', [$isAdmin]);

        $group->delete();

        return response()->json([
            'success' => true,
            'group_id' => $id,
        ]);
    }


    public function leave($id)
    {
        $group = ChatGroup::findOrFail($id);
        $userId = Auth::id();

        // just detach if member
        $group->users()->detach($userId);

        // optional: delete if no members left
        if (!$group->users()->exists()) {
            $group->delete();
        }

        return response()->json([
            'success' => true,
            'group_id' => $id,
        ]);
    }


    public function acceptInvite(ChatGroup $group)
    {
        $userId = Auth::id();

        $pivot = DB::table('chat_group_user')
            ->where('chat_group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        if (!$pivot) {
            return response()->json(['error' => 'Not invited'], 404);
        }

        if ($pivot->status === 'accepted') {
            return response()->json(['status' => 'accepted']);
        }

        DB::table('chat_group_user')
            ->where('chat_group_id', $group->id)
            ->where('user_id', $userId)
            ->update([
                'status' => 'accepted',
                'joined_at' => now(),
            ]);

        return response()->json(['status' => 'accepted']);
    }

    public function declineInvite(ChatGroup $group)
    {
        $userId = Auth::id();

        $pivot = DB::table('chat_group_user')
            ->where('chat_group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        if (!$pivot) {
            return response()->json(['error' => 'Not invited'], 404);
        }

        DB::table('chat_group_user')
            ->where('chat_group_id', $group->id)
            ->where('user_id', $userId)
            ->update([
                'status' => 'declined',
                'joined_at' => null,
            ]);

        // or detach instead of keeping the row:
        // DB::table('chat_group_user')->where(...)->delete();

        return response()->json(['status' => 'declined']);
    }


}
