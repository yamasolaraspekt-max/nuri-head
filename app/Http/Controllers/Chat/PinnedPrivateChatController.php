<?php

namespace App\Http\Controllers\Chat;
use App\Http\Controllers\Controller;

use App\Models\PinnedPrivateChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
class PinnedPrivateChatController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:user,group',
            'id'   => 'required|integer',
        ]);

        $userId = Auth::id();

        if ($data['type'] === 'user') {
            $table = 'pinned_private_chats';
            $col   = 'other_user_id';
        } else { // group
            $table = 'pinned_group_chats';
            $col   = 'chat_group_id';
        }

        $exists = DB::table($table)
            ->where('user_id', $userId)
            ->where($col, $data['id'])
            ->exists();

        if ($exists) {
            DB::table($table)
                ->where('user_id', $userId)
                ->where($col, $data['id'])
                ->delete();

            return response()->json(['is_pinned' => false]);
        }

        DB::table($table)->insert([
            'user_id' => $userId,
            $col      => $data['id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['is_pinned' => true]);
    }
}
