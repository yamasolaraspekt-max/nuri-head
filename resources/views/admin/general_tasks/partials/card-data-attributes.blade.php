@php
    use Carbon\Carbon;

    $assignees = collect($task->assignees ?? []);
    $steps = collect($task->steps ?? []);
    $doneSteps = $steps->filter(function ($step) { return !empty($step->is_done); })->count();
    $stepCount = $steps->count();

    $progress = (int) ($task->progress_percent ?? 0);
    if ($progress <= 0 && $stepCount > 0) {
        $progress = (int) round(($doneSteps / max($stepCount, 1)) * 100);
    }
    if (($task->status ?? '') === 'done') { $progress = 100; }
    $progress = max(0, min(100, $progress));

    $plannedMinutes = (int) ($task->planned_minutes ?? $task->soll_minutes ?? $steps->sum(function ($step) { return (int) ($step->planned_minutes ?? $step->soll_minutes ?? 0); }));
    $actualMinutes = (int) ($task->actual_minutes ?? $task->ist_minutes ?? $steps->sum(function ($step) { return (int) ($step->actual_minutes ?? $step->ist_minutes ?? 0); }));

    $dueLocal = $task->due_at ? Carbon::parse($task->due_at)->format('Y-m-d\TH:i') : '';
    $recurrenceEndsLocal = $task->recurrence_ends_at ? Carbon::parse($task->recurrence_ends_at)->format('Y-m-d\TH:i') : '';

    $dependencyParentIds = collect($task->dependencyParents ?? $task->dependsOn ?? [])->pluck('id')->implode(',');
    $dependencyChildIds = collect($task->dependencyChildren ?? $task->blockingTasks ?? [])->pluck('id')->implode(',');
@endphp

data-task-id="{{ $task->id }}"
data-title="{{ e($task->title ?? '') }}"
data-description="{{ e($task->description ?? '') }}"
data-status="{{ $task->status ?? 'open' }}"
data-priority="{{ $task->priority ?? 'normal' }}"
data-visibility="{{ $task->visibility ?? 'all' }}"
data-department-id="{{ $task->department_id }}"
data-due-at-local="{{ $dueLocal }}"
data-planned-hours-today="{{ $task->planned_hours_today }}"
data-assignee-ids="{{ $assignees->pluck('id')->implode(',') }}"
data-claimed-by="{{ $task->claimed_by }}"
data-is-recurring="{{ !empty($task->is_recurring) ? 1 : 0 }}"
data-recurrence-frequency="{{ $task->recurrence_frequency }}"
data-recurrence-weekday="{{ $task->recurrence_weekday }}"
data-recurrence-ends-at-local="{{ $recurrenceEndsLocal }}"
data-show-due-datetime="{{ ($task->show_due_datetime ?? true) ? 1 : 0 }}"
data-task-mode="{{ $task->task_mode ?? 'single' }}"
data-progress-percent="{{ $progress }}"
data-sort-order="{{ (int) ($task->sort_order ?? 999999) }}"
data-planned-minutes="{{ $plannedMinutes }}"
data-actual-minutes="{{ $actualMinutes }}"
data-soll-minutes="{{ $plannedMinutes }}"
data-ist-minutes="{{ $actualMinutes }}"
data-dependency-parent-ids="{{ $dependencyParentIds }}"
data-dependency-child-ids="{{ $dependencyChildIds }}"
