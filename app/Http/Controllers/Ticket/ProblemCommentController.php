<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\ProblemComment;
use App\Models\Problem;
use App\Models\Employee;
use App\Notifications\TicketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,update')->only(['update']);
        $this->middleware('permission:Problem,delete')->only(['destroy']);
    }

    public function fetch($ticket_id)
    {
        $comments = ProblemComment::with('employee')
            ->where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($comment) => $this->formatComment($comment));

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'exists:problems,id'],
            'comment' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $comment = ProblemComment::create([
            'ticket_id' => $request->ticket_id,
            'employee_id' => auth()->user()->name,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        $ticket = Problem::find($request->ticket_id);
        $receiver = $ticket ? Employee::find($ticket->start_user) : null;

        if ($receiver) {
            $receiver->notify(new TicketNotification([
                'title' => 'Neuer Kommentar hinzugefügt',
                'message' => Auth::user()->name . ' hat das Ticket kommentiert #' . $ticket->id,
                'ticket_id' => $ticket->id,
                'employee_id' => auth()->user()->name,
                'type' => 'comment',
            ]));
        }

        $comment->load('employee');

        return response()->json([
            'success' => true,
            'message' => 'Kommentar wurde gespeichert.',
            'comment' => $this->formatComment($comment),
        ]);
    }

    public function update(Request $request, ProblemComment $comment)
    {
        if ((string) $comment->employee_id !== (string) $request->user()->name) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $this->formatComment($comment->fresh('employee')),
        ]);
    }

    public function like(ProblemComment $comment)
    {
        $comment->increment('likes');

        return response()->json([
            'success' => true,
            'likes' => (int) $comment->fresh()->likes,
        ]);
    }

    public function destroy(ProblemComment $comment)
    {
        if ((string) $comment->employee_id !== (string) auth()->user()->name) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kommentar wurde gelöscht.',
        ]);
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'exists:problems,id'],
            'ticket_task_id' => ['required', 'exists:ticket_tasks,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'comment' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $comment = ProblemComment::create([
            'ticket_id' => $request->ticket_id,
            'ticket_task_id' => $request->ticket_task_id,
            'employee_id' => $request->employee_id,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $this->formatComment($comment->load('employee')),
        ]);
    }

    private function formatComment(ProblemComment $comment): array
    {
        $employee = $comment->employee;

        return [
            'id' => $comment->id,
            'ticket_id' => $comment->ticket_id,
            'employee_id' => $comment->employee_id,
            'parent_id' => $comment->parent_id,
            'comment' => $comment->comment,
            'likes' => (int) ($comment->likes ?? 0),
            'created_at' => optional($comment->created_at)->format('d.m.Y H:i'),
            'created_at_human' => optional($comment->created_at)->diffForHumans(),
            'employee' => $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'image' => $employee->image,
                'avatar_url' => $employee->image
                    ? asset('images/employee/' . $employee->image)
                    : asset('images/gender/male.png'),
            ] : null,
        ];
    }
}
