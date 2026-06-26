<div class="gt-view" id="gtGanttView">
    <div class="gt-gantt-panel">
        <div class="gt-gantt-head">
            <div>
                <div class="gt-gantt-title">Gantt / Verlauf</div>
                <div class="gt-person-meta">Fortschritt kommt aus Schritten, Soll/Ist und Prozent.</div>
            </div>

            <div class="gt-actions">
                <button type="button" class="gt-btn-soft" data-gantt-zoom="out">− Zoom</button>
                <button type="button" class="gt-btn-soft" data-gantt-zoom="in">+ Zoom</button>
            </div>
        </div>

        <div class="gt-gantt-body" id="gtGanttBody" data-gantt-zoom-level="1">
            @php
                $ganttTasks = collect($boardTasks ?? [])->sortBy(function ($task) {
                    return $task->due_at ?: $task->created_at;
                })->values();
            @endphp

            @forelse($ganttTasks as $task)
                @php
                    $steps = collect($task->steps ?? []);
                    $doneSteps = $steps->filter(function ($step) {
                        return !empty($step->is_done);
                    })->count();

                    $stepCount = $steps->count();
                    $progress = (int) ($task->progress_percent ?? 0);

                    if ($progress <= 0 && $stepCount > 0) {
                        $progress = (int) round(($doneSteps / max($stepCount, 1)) * 100);
                    }

                    if (($task->status ?? '') === 'done') {
                        $progress = 100;
                    }

                    $progress = max(0, min(100, $progress));

                    $left = ($loop->index % 7) * 6;
                    $width = max(16, 28 + ($stepCount * 4));
                    $sollHours = number_format(((int) ($task->soll_minutes ?? $steps->sum('soll_minutes'))) / 60, 2, ',', '.');
                    $istHours = number_format(((int) ($task->ist_minutes ?? $steps->sum('ist_minutes'))) / 60, 2, ',', '.');
                @endphp

                <div class="gt-gantt-row">
                    <div>
                        <div class="gt-gantt-name">{{ $task->title }}</div>
                        <div class="gt-gantt-meta">
                            {{ $statusLabels[$task->status] ?? $task->status }}
                            · {{ $progress }}%
                            · Soll {{ $sollHours }}h / Ist {{ $istHours }}h
                        </div>
                    </div>

                    <div class="gt-gantt-line">
                        <div class="gt-gantt-bar" style="left: {{ $left }}%; width: {{ min(96 - $left, $width) }}%">
                            <div class="gt-gantt-bar-fill" style="width:{{ $progress }}%"></div>
                            <div class="gt-gantt-percent">{{ $progress }}%</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="gt-empty">Keine Aufgaben für den Gantt-Verlauf.</div>
            @endforelse
        </div>
    </div>
</div>
