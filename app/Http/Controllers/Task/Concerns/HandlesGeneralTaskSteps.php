<?php

namespace App\Http\Controllers\Task\Concerns;

use App\Models\GeneralTask;
use App\Models\GeneralTaskStep;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HandlesGeneralTaskSteps
{
    protected function stepValidationRules(): array
    {
        return [
            'task_mode' => ['nullable', 'in:single,bulk'],

            'steps' => ['nullable', 'array'],
            'steps.*.id' => ['nullable', 'integer', 'exists:general_task_steps,id'],
            'steps.*.sort_order' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'steps.*.title' => ['nullable', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.due_at' => ['nullable', 'date'],
            'steps.*.soll_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'steps.*.planned_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'steps.*.ist_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'steps.*.actual_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'steps.*.assignee_ids' => ['nullable', 'array'],
            'steps.*.assignee_ids.*' => ['nullable', 'integer', 'exists:employees,id'],
            'steps.*._delete' => ['nullable', 'boolean'],
        ];
    }

    protected function syncTaskSteps(GeneralTask $task, Request $request): void
    {
        $mode = $request->input('task_mode', 'single') === 'bulk' ? 'bulk' : 'single';
        $rows = collect($request->input('steps', []))
            ->values()
            ->sortBy(function ($row, $index) {
                return (int) Arr::get($row, 'sort_order', $index + 1);
            })
            ->values();
        $employeeId = (int) auth()->user()->name;

        DB::transaction(function () use ($task, $request, $mode, $rows, $employeeId) {
            $keepIds = [];

            if ($mode === 'single') {
                $single = $rows->first() ?: [];

                $step = $task->steps()->orderBy('sort_order')->first();

                if (!$step) {
                    $step = new GeneralTaskStep([
                        'general_task_id' => $task->id,
                        'created_by' => $employeeId,
                    ]);
                }

                $title = trim((string) Arr::get($single, 'title', ''));
                $description = Arr::has($single, 'description')
                    ? Arr::get($single, 'description')
                    : $task->description;

                $plannedHours = $this->firstExistingStepHours($single, ['soll_hours', 'planned_hours']);
                $actualHours = $this->firstExistingStepHours($single, ['ist_hours', 'actual_hours']);

                $step->fill([
                    'title' => $title !== '' ? $title : $task->title,
                    'description' => $description,
                    'due_at' => Arr::get($single, 'due_at') ?: null,
                    'sort_order' => 1,
                    'soll_minutes' => $this->hoursToMinutes($plannedHours),
                    'ist_minutes' => $actualHours === null ? (int) ($step->ist_minutes ?? 0) : $this->hoursToMinutes($actualHours),
                ]);

                if (!$step->exists) {
                    $step->general_task_id = $task->id;
                    $step->created_by = $employeeId;
                }

                $step->save();

                $assigneeIds = collect($request->input('assignee_ids', []))
                    ->filter(fn($id) => $id !== null && $id !== '')
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                if (empty($assigneeIds)) {
                    $assigneeIds = collect(Arr::get($single, 'assignee_ids', []))
                        ->filter(fn($id) => $id !== null && $id !== '')
                        ->map(fn($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all();
                }

                $step->assignees()->sync($assigneeIds);
                $task->assignees()->sync($assigneeIds);

                $keepIds[] = $step->id;
                $task->steps()->whereNotIn('id', $keepIds)->delete();

                $task->updateQuietly(['task_mode' => 'single']);
            } else {
                $position = 1;
                $taskAssigneeIds = [];

                foreach ($rows as $row) {
                    if (filter_var(Arr::get($row, '_delete'), FILTER_VALIDATE_BOOLEAN)) {
                        if ($id = Arr::get($row, 'id')) {
                            $task->steps()->whereKey((int) $id)->delete();
                        }
                        continue;
                    }

                    $title = trim((string) Arr::get($row, 'title'));

                    if ($title === '') {
                        continue;
                    }

                    $step = null;

                    if ($id = Arr::get($row, 'id')) {
                        $step = $task->steps()->whereKey((int) $id)->first();
                    }

                    if (!$step) {
                        $step = new GeneralTaskStep([
                            'general_task_id' => $task->id,
                            'created_by' => $employeeId,
                        ]);
                    }

                    $plannedHours = $this->firstExistingStepHours($row, ['soll_hours', 'planned_hours']);
                    $actualHours = $this->firstExistingStepHours($row, ['ist_hours', 'actual_hours']);

                    $step->fill([
                        'title' => $title,
                        'description' => Arr::get($row, 'description'),
                        'due_at' => Arr::get($row, 'due_at') ?: null,
                        'sort_order' => $position,
                        'soll_minutes' => $this->hoursToMinutes($plannedHours),
                        'ist_minutes' => $actualHours === null ? (int) ($step->ist_minutes ?? 0) : $this->hoursToMinutes($actualHours),
                    ]);

                    if (!$step->exists) {
                        $step->general_task_id = $task->id;
                        $step->created_by = $employeeId;
                    }

                    $step->save();

                    $assigneeIds = collect(Arr::get($row, 'assignee_ids', []))
                        ->filter(fn($id) => $id !== null && $id !== '')
                        ->map(fn($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all();

                    $step->assignees()->sync($assigneeIds);
                    $taskAssigneeIds = array_merge($taskAssigneeIds, $assigneeIds);

                    $keepIds[] = $step->id;
                    $position++;
                }

                if (empty($keepIds)) {
                    $step = $task->steps()->orderBy('sort_order')->first();

                    if (!$step) {
                        $step = new GeneralTaskStep([
                            'general_task_id' => $task->id,
                            'created_by' => $employeeId,
                        ]);
                    }

                    $step->fill([
                        'title' => $task->title,
                        'description' => $task->description,
                        'due_at' => $task->due_at,
                        'sort_order' => 1,
                        'soll_minutes' => 0,
                        'ist_minutes' => (int) ($step->ist_minutes ?? 0),
                    ]);

                    if (!$step->exists) {
                        $step->general_task_id = $task->id;
                        $step->created_by = $employeeId;
                    }

                    $step->save();

                    $fallbackAssigneeIds = collect($request->input('assignee_ids', []))
                        ->filter(fn($id) => $id !== null && $id !== '')
                        ->map(fn($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all();

                    $step->assignees()->sync($fallbackAssigneeIds);
                    $taskAssigneeIds = $fallbackAssigneeIds;
                    $keepIds[] = $step->id;
                    $mode = 'single';
                }

                $task->steps()->whereNotIn('id', $keepIds)->delete();

                $task->assignees()->sync(collect($taskAssigneeIds)->unique()->values()->all());
                $task->updateQuietly(['task_mode' => $mode]);
            }

            $this->recalculateTaskStepProgress($task);
        });
    }

    protected function ensureDefaultSingleStep(GeneralTask $task): void
    {
        if ($task->steps()->exists()) {
            $this->recalculateTaskStepProgress($task);
            return;
        }

        $employeeId = (int) auth()->user()->name;

        $step = $task->steps()->create([
            'title' => $task->title,
            'description' => $task->description,
            'due_at' => $task->due_at,
            'sort_order' => 1,
            'soll_minutes' => 0,
            'ist_minutes' => 0,
            'created_by' => $employeeId,
        ]);

        $step->assignees()->sync($task->assignees()->pluck('employees.id')->all());
        $this->recalculateTaskStepProgress($task);
    }

    protected function applyStepChecksFromRequest(GeneralTask $task, Request $request): void
    {
        $checks = collect($request->input('step_checks', []))->values();

        if ($checks->isEmpty()) {
            return;
        }

        $employeeId = (int) auth()->user()->name;

        DB::transaction(function () use ($task, $checks, $employeeId) {
            foreach ($checks as $row) {
                $stepId = Arr::get($row, 'id', Arr::get($row, 'step_id'));

                if (!$stepId) {
                    continue;
                }

                $step = $task->steps()->whereKey((int) $stepId)->first();

                if (!$step) {
                    continue;
                }

                $done = filter_var(Arr::get($row, 'is_done'), FILTER_VALIDATE_BOOLEAN);
                $actualHours = $this->firstExistingStepHours($row, ['ist_hours', 'actual_hours']);

                $update = [
                    'is_done' => $done,
                    'checked_by' => $done ? $employeeId : null,
                    'checked_at' => $done ? now() : null,
                ];

                if ($actualHours !== null) {
                    $update['ist_minutes'] = $this->hoursToMinutes($actualHours);
                }

                $step->update($update);

                $reason = trim((string) Arr::get($row, 'reason', ''));

                if ($reason !== '') {
                    $task->reports()->create([
                        'employee_id' => $employeeId,
                        'type' => 'comment',
                        'body' => ($done ? 'Schritt erledigt: ' : 'Schritt wieder geöffnet: ') . $step->title . "\n\nGrund: " . $reason,
                    ]);
                }
            }

            $this->recalculateTaskStepProgress($task);
        });
    }

    protected function recalculateTaskStepProgress(GeneralTask $task): void
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

    protected function hoursToMinutes($hours): int
    {
        if ($hours === null || $hours === '') {
            return 0;
        }

        return max(0, (int) round(((float) str_replace(',', '.', (string) $hours)) * 60));
    }

    protected function firstExistingStepHours(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (Arr::has($row, $key)) {
                $value = Arr::get($row, $key);

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }
}
