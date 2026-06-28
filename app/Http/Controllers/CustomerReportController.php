<?php

namespace App\Http\Controllers;

use App\Models\CustomerReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerReportComment;

class CustomerReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    } 

    public function list(Request $request)
{
    $reports = CustomerReport::with('reporter')
        ->where('product_id', $request->product_id)
        ->where('customer_id', $request->customer_id)
        ->where('alternative_id', $request->alternative_id)
        ->latest()
        ->get();

    return view('admin.new_leads.partials.report', compact('reports'))->render();
}

public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required',
        'customer_id' => 'required',
        'alternative_id' => 'required',
        'stage' => 'required',
        'report' => 'required'
    ]);

    CustomerReport::create([
        'product_id' => $request->product_id,
        'customer_id' => $request->customer_id,
        'alternative_id' => $request->alternative_id,
        'stage' => $request->stage,
        'report' => $request->report,
        'report_by' => auth()->user()->name,
    ]);

    return response()->json(['success' => true]);
}

public function destroy($id)
{
    $report = CustomerReport::findOrFail($id);
    if ((string) $report->report_by !== auth()->user()->name) {
        abort(403);
    }
    $report->delete();
    return response()->json(['success' => true]);
}


public function show($id)
{
    $report = CustomerReport::findOrFail($id);

    if ((string) $report->report_by !== (string) auth()->user()->name) {
        abort(403);
    }


    return response()->json([
        'stage' => $report->stage,
        'report' => $report->report,
        'date' => $report->created_at->format('Y-m-d'),
    ]);
}

public function update(Request $request, $id)
{
    $report = CustomerReport::findOrFail($id);

    if ((string) $report->report_by !== auth()->user()->name) {
        abort(403);
    }

    $report->update([
        'stage' => $request->stage,
        'report' => $request->report,
    ]);

    return response()->json(['success' => true]);
}


public function kanbanIndex(Request $request)
    {
        $customerId    = $request->integer('customer_id');
        $alternativeId = $request->integer('alternative_id');
        $productId     = $request->integer('product_id') ?: null;

        abort_if(!$customerId || !$alternativeId, 400, 'Kundenkontext fehlt.');

        $query = CustomerReport::with([
                'reporter',                       // Employee
                'comments.user',                  // top-level comments
                'comments.replies.user',          // replies
            ])
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId);

        if ($productId) {
            $query->where(function ($q) use ($productId) {
                $q->whereNull('product_id')
                  ->orWhere('product_id', $productId);
            });
        }

        $reports = $query->latest()->get();

        $html = view('admin.kanban.partials.customer_reports', [
            'reports'        => $reports,
            'customer_id'    => $customerId,
            'alternative_id' => $alternativeId,
            'product_id'     => $productId,
        ])->render();

        return response()->json([
            'status' => 'ok',
            'html'   => $html,
        ]);
    }

    /**
     * POST /kanban/customer-reports
     */
    public function kanbanStore(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'product_id'     => 'nullable|exists:article_groups,id',
            'stage'          => 'nullable|string|max:255',
            'report'         => 'required|string',
            'report_details' => 'nullable|array',
        ]);

        $auth       = Auth::user();
        $employeeId = $auth->name ?? $auth->id;

        $report = CustomerReport::create([
            'customer_id'    => $data['customer_id'],
            'alternative_id' => $data['alternative_id'],
            'product_id'     => $data['product_id'] ?? null,
            'stage'          => $data['stage'] ?? null,
            'report'         => $data['report'],
            'report_details' => $data['report_details'] ?? null,
            'report_by'      => $employeeId,
        ]);

        $report->load([
            'reporter',
            'comments.user',
            'comments.replies.user',
        ]);

        $html = view('admin.kanban.partials.customer_report_card', [
            'report' => $report,
        ])->render();

        return response()->json([
            'status' => 'ok',
            'id'     => $report->id,
            'html'   => $html,
        ]);
    }

    /**
     * POST /kanban/customer-reports/{report}/comment
     */
    public function kanbanComment(Request $request, CustomerReport $report)
    {
        $data = $request->validate([
            'comment'   => 'required|string',
            'parent_id' => 'nullable|exists:customer_report_comments,id',
        ]);

        $auth       = Auth::user();
        $employeeId = $auth->name ;

        $comment = $report->comments()->create([
            'comment'   => $data['comment'],
            'parent_id' => $data['parent_id'] ?? null,
            'user_id'   => $employeeId,
        ]);

        $comment->load('user', 'replies.user');

        $html = view('admin.kanban.partials.customer_report_comment', [
            'comment' => $comment,
        ])->render();

        return response()->json([
            'status' => 'ok',
            'id'     => $comment->id,
            'html'   => $html,
        ]);
    }

}
