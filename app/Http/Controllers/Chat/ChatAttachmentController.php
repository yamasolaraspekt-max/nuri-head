<?php

namespace App\Http\Controllers\Chat;
use App\Http\Controllers\Controller;

use App\Models\ChatAttachment;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;
class ChatAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

 public function show($id)
    {
        $att = ChatAttachment::with('chat')->findOrFail($id);

        $userId = auth()->id();

        if ($att->chat->to_user_id) {
            // private chat
            abort_unless(
                in_array($userId, [$att->chat->from_user_id, $att->chat->to_user_id]),
                403
            );
        } elseif ($att->chat->group_id) {
            // group chat
            abort_unless(
                DB::table('chat_group_user')
                    ->where('chat_group_id', $att->chat->group_id)  // <- THIS column name
                    ->where('user_id', $userId)
                    ->exists(),
                403
            );
        }

        $stream = Storage::disk($att->disk)->readStream($att->path);
        if (! $stream) abort(404);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $att->mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($att->original_name).'"',
            'Cache-Control'       => 'private, max-age=0, no-cache',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatAttachment $chatAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatAttachment $chatAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatAttachment $chatAttachment)
    {
        //
    }
}
