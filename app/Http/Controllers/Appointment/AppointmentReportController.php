<?php

namespace App\Http\Controllers\Appointment;
use App\Http\Controllers\Controller;

use App\Models\AppointmentReport;
use App\Models\MainAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentReportController extends Controller
{
    public function index($appointmentId)
    {
        $appointment = MainAppointment::with([
            'reports.author',
        ])->findOrFail($appointmentId);

        $reports = $appointment->reports()
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'          => $r->id,
                    'report'      => $r->report,
                    'report_date' => optional($r->report_date)->toDateString(),
                    'likes'       => $r->likes,
                    'dislikes'    => $r->dislikes,
                    'comments'    => $r->comments ?? [],
                    'author'      => $r->author ? [
                        'id'        => $r->author->id,
                        'name'      => $r->author->name,
                        'lastname'  => $r->author->lastname ?? '',
                        'image'     => $r->author->image ?? 'default.png',
                    ] : null,
                ];
            });

        return response()->json([
            'reports' => $reports,
        ]);
    }


    public function list(MainAppointment $appointment)
    {
        // Reports for this appointment
        $reports = AppointmentReport::with(['employee:id,name,lastname', 'author:id,name,lastname'])
            ->where('appointment_id', $appointment->id)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (AppointmentReport $report) {
                return [
                    'id'               => $report->id,
                    'appointment_id'   => $report->appointment_id,
                    'employee_id'      => $report->employee_id,
                    'type'             => $report->type,
                    'report'           => $report->report,
                    'report_html'      => nl2br(e($report->report)), // used in JS
                    'report_date'      => optional($report->report_date)->toDateString(),
                    'due_date'         => optional($report->due_date)->toDateString(),
                    'next_step'        => $report->next_step,
                    'author_name'      => optional($report->author)->name
                                         .' '.optional($report->author)->lastname,
                    'created_at'       => $report->created_at,
                    'created_at_human' => $report->created_at?->diffForHumans(),
                    'likes'            => $report->likes_count,     // from accessor
                    'dislikes'         => $report->dislikes_count,  // from accessor
                ];
            });

        // Appointment-wide comments – adjust model/relationship name as needed
        $comments = $appointment->comments()
            ->with('employee:id,name,lastname')
            ->latest()
            ->get()
            ->map(function ($comment) {
                return [
                    'id'               => $comment->id,
                    'appointment_id'   => $comment->appointment_id,
                    'employee_id'      => $comment->employee_id,
                    'author_name'      => optional($comment->employee)->name
                                         .' '.optional($comment->employee)->lastname,
                    'comment'          => $comment->comment,
                    'created_at'       => $comment->created_at,
                    'created_at_human' => $comment->created_at?->diffForHumans(),
                ];
            });

        return response()->json([
            'reports'  => $reports,
            'comments' => $comments,
        ]);
    }

    public function store(Request $request, $appointmentId)
    {
        $request->validate([
            'report' => 'required|string',
        ]);

        $appointment = MainAppointment::findOrFail($appointmentId);

        $employeeId = Auth::user()->name;

        $report = AppointmentReport::create([
            'employee_id'   => $employeeId,
            'appointment_id'=> $appointment->id,
            'type'          => 'appointment',
            'report'        => $request->input('report'),
            'report_date'   => now()->toDateString(),
            'report_by'     => $employeeId,
            'comments'      => [],
        ]);

        // optional: mark appointment as having report
        $appointment->update(['is_report' => '1']);

        return response()->json(['success' => true, 'id' => $report->id]);
    }

    public function update(Request $request, $appointmentId, $reportId)
    {
        $request->validate([
            'report' => 'required|string',
        ]);

        $report = AppointmentReport::where('appointment_id', $appointmentId)
            ->findOrFail($reportId);

        $report->update([
            'report'      => $request->input('report'),
            'report_date' => now()->toDateString(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($appointmentId, $reportId)
    {
        $report = AppointmentReport::where('appointment_id', $appointmentId)
            ->findOrFail($reportId);

        $report->delete();

        return response()->json(['success' => true]);
    }

    public function react(Request $request, $reportId)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $report = AppointmentReport::findOrFail($reportId);

        if ($request->type === 'like') {
            $report->increment('likes');
        } else {
            $report->increment('dislikes');
        }

        return response()->json([
            'success'  => true,
            'likes'    => $report->likes,
            'dislikes' => $report->dislikes,
        ]);
    }

    public function comment(Request $request, $reportId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $report = AppointmentReport::findOrFail($reportId);

        $employeeId = Auth::user()->employee_id ?? Auth::id();
        $comments   = $report->comments ?? [];

        $comments[] = [
            'employee_id' => $employeeId,
            'text'        => $request->input('comment'),
            'created_at'  => now()->toDateTimeString(),
        ];

        $report->comments = $comments;
        $report->save();

        return response()->json([
            'success'  => true,
            'comments' => $comments,
        ]);
    }
}
