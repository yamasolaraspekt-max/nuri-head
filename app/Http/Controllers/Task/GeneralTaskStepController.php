<?php

namespace App\Http\Controllers\Task;

use App\Events\GeneralTaskChanged;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\GeneralTask;
use App\Models\GeneralTaskStep;
use Illuminate\Http\Request;

class GeneralTaskStepController extends Controller
{
    public function toggle(Request $request, GeneralTask $generalTask, GeneralTaskStep $step)
    {
        if ((int) $step->general_task_id !== (int) $generalTask->id) {
            abort(404);
        }

        $data = $request->validate([
            'is_done' => ['required', 'boolean'],
            'ist_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'actual_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $employeeId = (int) auth()->user()->name;
        $done = (bool) $data['is_done'];

        $update = [
            'is_done' => $done,
            'checked_by' => $done ? $employeeId : null,
            'checked_at' => $done ? now() : null,
        ];

        $actualHours = $data['actual_hours'] ?? $data['ist_hours'] ?? null;

        if ($actualHours !== null && $actualHours !== '') {
            $update['ist_minutes'] = max(0, (int) round(((float) str_replace(',', '.', (string) $actualHours)) * 60));
        }

        $step->update($update);

        $task = $generalTask->fresh([
            'department',
            'claimedBy',
            'assignees',
            'steps.assignees',
            'steps.checkedBy',
            'dependsOn.assignees',
            'blockingTasks.assignees',
        ]);

        $this->recalculate($task);

        $reason = trim((string) ($data['reason'] ?? ''));

        if ($reason !== '') {
            $task->reports()->create([
                'employee_id' => $employeeId,
                'type' => 'comment',
                'body' => ($done ? 'Schritt erledigt: ' : 'Schritt wieder geöffnet: ') . $step->title . "\n\nGrund: " . $reason,
            ]);
        }

        broadcast(new GeneralTaskChanged(
            $task->fresh([
                'department',
                'claimedBy',
                'assignees',
                'steps.assignees',
                'steps.checkedBy',
                'dependsOn.assignees',
                'blockingTasks.assignees',
            ]),
            'Ein Aufgabenschritt wurde aktualisiert.'
        ))->toOthers();

        $employee = Employee::find($employeeId);
        $checkedByName = $employee
            ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
            : 'Mitarbeiter';

        $freshTask = $task->fresh();
        $freshStep = $step->fresh(['checkedBy']);

        return response()->json([
            'message' => $done ? 'Schritt wurde als erledigt markiert.' : 'Schritt wurde wieder geöffnet.',
            'task_id' => $task->id,
            'step_id' => $step->id,
            'is_done' => $done,
            'progress_percent' => (int) ($freshTask->progress_percent ?? 0),
            'planned_hours' => round(((int) ($freshTask->soll_minutes ?? 0)) / 60, 2),
            'actual_hours' => round(((int) ($freshTask->ist_minutes ?? 0)) / 60, 2),
            'checked_by' => $done ? ($checkedByName ?: 'Mitarbeiter') : null,
            'checked_at' => $done ? optional($freshStep->checked_at)->format('d.m.Y H:i') : null,
        ]);
    }

    private function recalculate(GeneralTask $task): void
    {
        $steps = $task->steps()->get();
        $total = $steps->count();
        $done = $steps->where('is_done', true)->count();
        $planned = (int) $steps->sum('soll_minutes');
        $actual = (int) $steps->sum('ist_minutes');

        if ($total === 0) {
            $percent = 0;
        } elseif ($planned > 0) {
            $donePlanned = (int) $steps->where('is_done', true)->sum('soll_minutes');
            $percent = $donePlanned > 0
                ? (int) round(($donePlanned / max($planned, 1)) * 100)
                : (int) round(($done / max($total, 1)) * 100);
        } else {
            $percent = (int) round(($done / max($total, 1)) * 100);
        }

        $task->updateQuietly([
            'progress_percent' => min(100, max(0, $percent)),
            'soll_minutes' => $planned,
            'ist_minutes' => $actual,
        ]);
    }
}
