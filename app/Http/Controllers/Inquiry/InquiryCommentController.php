<?php
namespace App\Http\Controllers\Inquiry;
use App\Http\Controllers\Controller;
use App\Models\InquiryComment;
use Illuminate\Http\Request;

class InquiryCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Inquiry: Rollen-Gate (permission:Inquiry)
        $this->middleware('permission:Inquiry,update')->only(['editComment']);
        $this->middleware('permission:Inquiry,delete')->only(['deleteComment']);
    }

    // Fetch Comments for a specific inquiry
    public function fetchComments($inquiry_id)
    {
        $comments = InquiryComment::with('employee', 'replies') // Ensure 'replies' are included in the query
            ->where('inquiry_id', $inquiry_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Add an empty array for replies if not present
        $comments->each(function ($comment) {
            $comment->replies = $comment->replies ?: [];  // Ensure 'replies' is always an array
        });

        return response()->json($comments);
    }


    // Post a new comment
    public function postComment(Request $request, $inquiry_id)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = InquiryComment::create([
            'inquiry_id' => $inquiry_id,
            'employee_id' => auth()->user()->name,  // Assuming the user is logged in
            'comment' => $validated['comment'],
        ]);

        return response()->json($comment, 201);
    }

    // Edit a comment
    public function editComment(Request $request, $comment_id)
    {
        $comment = InquiryComment::findOrFail($comment_id);

        // Check if the logged-in user is the creator of the comment
        if ($comment->employee_id != auth()->user()->name) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update(['comment' => $request->comment]);

        return response()->json($comment);
    }

    // Delete a comment
    public function deleteComment($comment_id)
    {
        $comment = InquiryComment::findOrFail($comment_id);

        // Check if the logged-in user is the creator of the comment
        if ($comment->employee_id != auth()->user()->name) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['success' => 'Comment deleted']);
    }

    // Like a comment
    public function likeComment($comment_id)
    {
        $comment = InquiryComment::findOrFail($comment_id);
        $comment->likes++;
        $comment->save();

        return response()->json(['likes' => $comment->likes]);
    }

    // Dislike a comment
    public function dislikeComment($comment_id)
    {
        $comment = InquiryComment::findOrFail($comment_id);
        $comment->dislikes++;
        $comment->save();

        return response()->json(['dislikes' => $comment->dislikes]);
    }

    public function replyComment(Request $request, $comment_id)
{
    // Validate the reply
    $validated = $request->validate([
        'comment' => 'required|string',
    ]);

    // Find the parent comment
    $parentComment = InquiryComment::findOrFail($comment_id);

    // Create a reply and associate it with the parent comment
    $reply = InquiryComment::create([
        'inquiry_id' => $parentComment->inquiry_id, // Same inquiry as the parent comment
        'employee_id' => auth()->id(), // Assuming the logged-in user is replying
        'comment' => $validated['comment'],
        'parent_id' => $parentComment->id, // Set parent_id to create a reply
    ]);

    return response()->json($reply, 201);
}
 
}
