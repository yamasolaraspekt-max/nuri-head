<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketReport;
use App\Models\TicketReportComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_id' => ['required', 'exists:problems,id'],
            'customer_id' => ['required', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'exists:lead_alternative_adds,id'],
            'product_id' => ['required', 'exists:article_groups,id'],
            'title' => ['required', 'string', 'max:255'],
            'report' => ['required', 'string'],
            'language' => ['nullable', 'string', 'max:20'],
        ]);

        $employeeId = (int) auth()->user()->name;

        $report = TicketReport::create([
            'ticket_id' => $data['ticket_id'],
            'employee_id' => $employeeId,
            'customer_id' => $data['customer_id'],
            'alternative_id' => $data['alternative_id'] ?? null,
            'product_id' => $data['product_id'],
            'title' => $data['title'],
            'report' => $data['report'],
            'language' => $data['language'] ?? 'de',
            'report_date' => now(),
            'likes' => 0,
        ]);

        $report->load(['employee', 'comments.commentedBy']);

        Log::info('Ticket report saved', [
            'report_id' => $report->id,
            'ticket_id' => $report->ticket_id,
            'employee_id' => $employeeId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bericht wurde gespeichert.',
            'data' => $this->formatReport($report),
        ]);
    }

    public function update(Request $request, TicketReport $report): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'report' => ['required', 'string'],
            'language' => ['nullable', 'string', 'max:20'],
        ]);

        $report->update([
            'title' => $data['title'],
            'report' => $data['report'],
            'language' => $data['language'] ?? $report->language ?? 'de',
        ]);

        $report->load(['employee', 'comments.commentedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Bericht wurde aktualisiert.',
            'data' => $this->formatReport($report),
        ]);
    }

    public function destroy(TicketReport $report): JsonResponse
    {
        DB::transaction(function () use ($report) {
            $report->comments()->delete();
            $report->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Bericht wurde gelöscht.',
        ]);
    }

    public function like(TicketReport $report): JsonResponse
    {
        $report->increment('likes');

        return response()->json([
            'success' => true,
            'likes' => (int) $report->fresh()->likes,
        ]);
    }

    public function storeComment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_id' => ['required', 'exists:problems,id'],
            'ticket_report_id' => ['required', 'exists:ticket_reports,id'],
            'customer_id' => ['required', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'exists:lead_alternative_adds,id'],
            'product_id' => ['required', 'exists:article_groups,id'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $employeeId = (int) auth()->user()->name;

        $comment = TicketReportComment::create([
            'ticket_id' => $data['ticket_id'],
            'ticket_report_id' => $data['ticket_report_id'],
            'liked_by' => null,
            'comment_by' => $employeeId,
            'customer_id' => $data['customer_id'],
            'alternative_id' => $data['alternative_id'] ?? null,
            'product_id' => $data['product_id'],
            'comment' => $data['comment'],
        ]);

        $comment->load('commentedBy');

        return response()->json([
            'success' => true,
            'message' => 'Kommentar wurde gespeichert.',
            'data' => [
                'id' => $comment->id,
                'ticket_report_id' => $comment->ticket_report_id,
                'comment' => $comment->comment,
                'comment_by' => $comment->comment_by,
                'employee_name' => optional($comment->commentedBy)->name
                    ? trim(optional($comment->commentedBy)->name . ' ' . optional($comment->commentedBy)->lastname)
                    : 'N/A',
                'created_at_human' => optional($comment->created_at)->diffForHumans(),
            ],
        ]);
    }

    private function formatReport(TicketReport $report): array
    {
        return [
            'id' => $report->id,
            'ticket_id' => $report->ticket_id,
            'employee_id' => $report->employee_id,
            'customer_id' => $report->customer_id,
            'alternative_id' => $report->alternative_id,
            'product_id' => $report->product_id,
            'title' => $report->title,
            'report' => $report->report,
            'language' => $report->language,
            'likes' => (int) ($report->likes ?? 0),
            'report_date' => optional($report->report_date)->format('d.m.Y H:i'),
            'employee_name' => $report->employee
                ? trim(($report->employee->name ?? '') . ' ' . ($report->employee->lastname ?? ''))
                : 'N/A',
            'created_at_human' => optional($report->created_at)->diffForHumans(),
            'updated_at_human' => optional($report->updated_at)->diffForHumans(),
            'comments' => $report->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'employee_name' => $comment->commentedBy
                        ? trim(($comment->commentedBy->name ?? '') . ' ' . ($comment->commentedBy->lastname ?? ''))
                        : 'N/A',
                    'created_at_human' => optional($comment->created_at)->diffForHumans(),
                ];
            })->values(),
        ];
    }
}