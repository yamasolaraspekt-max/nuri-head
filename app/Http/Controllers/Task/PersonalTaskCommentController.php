<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\PersonalTaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonalTaskCommentController extends Controller
{
    public function index($task_id)
    {
        $data = DB::table('personal_task_comments')
            ->leftJoin('employees', 'employees.id', '=', 'personal_task_comments.comment_by')
            ->select(
                'personal_task_comments.*',
                'employees.name',
                'employees.lastname',
                'employees.gender',
                'employees.image'
            )
            ->where('personal_task_comments.task_id', $task_id)
            ->whereNull('personal_task_comments.deleted_at')
            ->where('personal_task_comments.status', 'Published')
            ->orderBy('personal_task_comments.created_at', 'asc')
            ->get();

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:personal_tasks,id',
            'comment' => 'required|string',
        ]);

        $comment = PersonalTaskComment::create([
            'task_id' => $request->task_id,
            'comment_by' => auth()->user()->name,
            'comment' => $request->comment,
            'status' => 'Published',
            'parent_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment saved',
            'id' => $comment->id,
            'comment' => $comment,
        ]);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:personal_tasks,id',
            'parent_id' => 'required|exists:personal_task_comments,id',
            'comment' => 'required|string',
        ]);

        $reply = PersonalTaskComment::create([
            'task_id' => $request->task_id,
            'comment_by' => auth()->user()->name,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
            'status' => 'Published',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply saved',
            'id' => $reply->id,
            'comment' => $reply,
        ]);
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:personal_tasks,id',
            'comment' => 'required|string',
        ]);

        $comment = PersonalTaskComment::create([
            'task_id' => $request->task_id,
            'comment' => $request->comment,
            'comment_by' => auth()->user()->name,
            'status' => 'Published',
        ]);

        return response()->json(['success' => true, 'id' => $comment->id, 'comment' => $comment]);
    }

    public function list($taskId)
    {
        $comments = PersonalTaskComment::where('task_id', $taskId)
            ->where('status', 'Published')
            ->whereNull('parent_id')
            ->with('author', 'replies.author')
            ->latest()
            ->get();

        return view('admin.new_leads.layouts.partials.task_notes', compact('comments'));
    }

    public function delete($id)
    {
        PersonalTaskComment::where('id', $id)->delete();
        return response()->json(['deleted' => true]);
    }

    public function edit(Request $request, $id)
    {
        $request->validate(['comment' => 'required|string']);
        $comment = PersonalTaskComment::findOrFail($id);
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json(['updated' => true]);
    }
}
