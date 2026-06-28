<?php
namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function toggleShare(Request $req, AiChat $chat)
    {
        $this->authorize('update', $chat);
        $share = !$chat->is_shared;
        $token = $share ? Str::random(48) : null;

        $chat->update([
            'is_shared' => $share,
            'share_token' => $token,
        ]);

        return response()->json([
            'is_shared' => $chat->is_shared,
            'url' => $chat->is_shared ? route('ai.share.public', $chat->share_token) : null,
        ]);
    }

    public function publicView(string $token)
    {
        $chat = AiChat::where('is_shared', true)->where('share_token', $token)->firstOrFail();
        $chat->load('messages');
        return view('ai.chat_public', compact('chat'));
    }
}