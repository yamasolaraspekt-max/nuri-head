<?php

namespace App\Http\Controllers;

use App\Models\CustomerReportComment;
use Illuminate\Http\Request;

class CustomerReportCommentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($reportId)
    {
        $comments = CustomerReportComment::with([
            'user',
            'replies' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'replies.user',
            'replies.parent'
        ])
        ->where('report_id', $reportId)
        ->whereNull('parent_id')
        ->latest()
        ->get();

        return view('admin.new_leads.partials.comments', compact('comments', 'reportId'))->render();
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_id' => 'required',
            'comment' => 'required',
        ]);

        $comment = CustomerReportComment::create([
            'report_id' => $request->report_id,
            'user_id' => auth()->user()->name,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, CustomerReportComment $comment)
    {
        if ((string) $comment->user_id !== auth()->user()->name) {
            abort(403);
        }

        $comment->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    public function destroy(CustomerReportComment $comment)
    {
        if ((string) $comment->user_id !== auth()->user()->name) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}
