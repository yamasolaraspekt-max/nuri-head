<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OverdueReportCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct($report)
    {
        $employee = Employee::find($report->employee_id ?? $report->report_by ?? null);

        $this->payload = [
            'id'          => $report->id,
            'type'        => $report->type ?? $report->reportable_type ?? 'report',
            'target_id'   => $report->target_id ?? $report->reportable_id ?? null,
            'title'       => $report->title ?? $report->subject ?? 'Neuer Bericht',
            'report'      => $report->report ?? $report->message ?? $report->description ?? '',
            'employee_id' => $employee?->id,
            'employee'    => trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? '')) ?: 'Unbekannt',
            'created_at'  => optional($report->created_at)->toDateTimeString() ?? now()->toDateTimeString(),
            'created_human' => optional($report->created_at)->diffForHumans() ?? 'gerade eben',
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('overdue-reports');
    }

    public function broadcastAs(): string
    {
        return 'overdue.report.created';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}