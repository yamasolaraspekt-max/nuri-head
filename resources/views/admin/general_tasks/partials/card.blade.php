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

    $priority = $task->priority ?? 'normal';
    $priorityClass = match ($priority) {
        'urgent' => 'red',
        'important' => 'orange',
        'low' => 'gray',
        default => 'blue',
    };

    $isOverdue = $task->due_at && Carbon::parse($task->due_at)->isPast() && !in_array($task->status, ['done', 'archived'], true);
    $dependencyParents = collect($task->dependencyParents ?? $task->dependsOn ?? []);
    $dependencyChildren = collect($task->dependencyChildren ?? $task->blockingTasks ?? []);

    $stepPayload = $steps->map(function ($step) {
        $checkedBy = $step->checkedBy ?? null;
        $checkedName = $checkedBy ? trim(($checkedBy->name ?? '') . ' ' . ($checkedBy->lastname ?? '')) : '';
        return [
            'id' => $step->id ?? null,
            'title' => $step->title ?? '',
            'description' => $step->description ?? '',
            'planned_hours' => round(((int) ($step->planned_minutes ?? $step->soll_minutes ?? 0)) / 60, 2),
            'actual_hours' => round(((int) ($step->actual_minutes ?? $step->ist_minutes ?? 0)) / 60, 2),
            'due_at' => !empty($step->due_at) ? Carbon::parse($step->due_at)->format('Y-m-d\TH:i') : null,
            'due_at_label' => !empty($step->due_at) ? Carbon::parse($step->due_at)->format('d.m.Y H:i') : null,
            'is_overdue' => !empty($step->due_at) && Carbon::parse($step->due_at)->isPast() && empty($step->is_done),
            'is_done' => !empty($step->is_done),
            'checked_by_name' => $checkedName,
            'checked_at' => !empty($step->checked_at) ? Carbon::parse($step->checked_at)->format('d.m.Y H:i') : null,
            'assignee_ids' => collect($step->assignees ?? [])->pluck('id')->values()->all(),
            'assignee_names' => collect($step->assignees ?? [])->map(function ($employee) {
                return trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('#' . ($employee->id ?? ''));
            })->values()->all(),
        ];
    })->values();

    $stepPayloadJson = json_encode($stepPayload->values()->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp

<article
    class="gt-task gt-card-v5 {{ $isOverdue ? 'is-overdue' : '' }}"
    draggable="true"
    @include('admin.general_tasks.partials.card-data-attributes')
>
    <div class="gt-card-v5-head">
        <div class="gt-card-v5-title-wrap">
            <div class="gt-task-title">{{ $task->title }}</div>
            @if(!empty($task->description))
                <div class="gt-task-desc">{{ \Illuminate\Support\Str::limit(strip_tags($task->description), 90) }}</div>
            @endif
        </div>

        <div class="gt-task-top-actions">
            <button class="gt-btn-ic gt-info-btn" type="button" onclick="gtOpenTaskInfo(this)" title="Details & Schritte anzeigen">
                <i data-lucide="info" style="width:15px;height:15px"></i>
            </button>
            <button class="gt-btn-ic" type="button" onclick="gtOpenEditTask(this)" title="Aufgabe bearbeiten">
                <i data-lucide="pencil" style="width:15px;height:15px"></i>
            </button>
        </div>
    </div>

    <div class="gt-card-v5-meta">
        <span class="gt-badge {{ $priorityClass }}">{{ $priorityLabels[$priority] ?? $priority }}</span>
        <span class="gt-badge {{ ($task->task_mode ?? 'single') === 'bulk' ? 'orange' : 'blue' }}">
            {{ ($task->task_mode ?? 'single') === 'bulk' ? 'Bulk' : 'Single' }}
        </span>
        @if($stepCount > 0)
            <span class="gt-badge green">{{ $doneSteps }}/{{ $stepCount }} Schritte</span>
        @endif
        @if($isOverdue)
            <span class="gt-badge red"><i data-lucide="alarm-clock" style="width:12px;height:12px"></i> Überfällig</span>
        @elseif(($task->show_due_datetime ?? true) && $task->due_at)
            <span class="gt-badge gray"><i data-lucide="calendar-clock" style="width:12px;height:12px"></i>{{ Carbon::parse($task->due_at)->format('d.m. H:i') }}</span>
        @endif
        @if($dependencyParents->count())
            <span class="gt-badge blue"><i data-lucide="git-merge" style="width:12px;height:12px"></i>{{ $dependencyParents->count() }} Vorgänger</span>
        @endif
    </div>

    @include('admin.general_tasks.partials.card-progress')

    <div class="gt-card-v5-foot">
        @include('admin.general_tasks.partials.card-assignees')
        @include('admin.general_tasks.partials.card-actions')
    </div>

    <script type="application/json" id="gtTaskStepsJson-{{ $task->id }}">{!! $stepPayloadJson !!}</script>
</article>
