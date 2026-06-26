<div class="gt-view" id="gtGanttView">
    <div class="gt-gantt-panel">
        <div class="gt-gantt-head">
            <div>
                <div class="gt-gantt-title">Gantt / Verlauf</div>
                <div class="gt-person-meta">Abhängigkeiten werden als Linien vom Parent/Vorgänger zur Child-Aufgabe angezeigt.</div>
            </div>
            <div class="gt-actions">
                <button type="button" class="gt-btn-soft" data-gantt-zoom="out">− Zoom</button>
                <button type="button" class="gt-btn-soft" data-gantt-zoom="in">+ Zoom</button>
            </div>
        </div>

        <div class="gt-gantt-body">
            <div class="gt-gantt-stage">
                <svg class="gt-gantt-dependency-svg"></svg>

                @php
                    $ganttTasks = collect($boardTasks ?? [])->sortBy(function ($task) {
                        return $task->due_at ?: $task->created_at;
                    })->values();
                @endphp

                @forelse($ganttTasks as $task)
                    @php
                        $steps = collect($task->steps ?? []);
                        $doneSteps = $steps->filter(fn($step) => !empty($step->is_done))->count();
                        $stepCount = $steps->count();
                        $progress = (int) ($task->progress_percent ?? ($stepCount ? round(($doneSteps / max($stepCount, 1)) * 100) : (($task->status ?? '') === 'done' ? 100 : 0)));
                        $progress = max(0, min(100, $progress));
                        $parentIds = collect($task->dependencyParents ?? [])->pluck('id')->implode(',');
                        $childCount = collect($task->dependencyChildren ?? [])->count();
                        $left = ($loop->index % 8) * 6;
                        $width = max(18, 30 + ($stepCount * 4));
                    @endphp

                    <div class="gt-gantt-row">
                        <div>
                            <div class="gt-gantt-name">{{ $task->title }}</div>
                            <div class="gt-gantt-meta">
                                {{ $statusLabels[$task->status] ?? $task->status }} · {{ $progress }}%
                                @if($parentIds !== '') · hat Vorgänger @endif
                                @if($childCount > 0) · blockiert {{ $childCount }} Aufgabe(n) @endif
                            </div>
                        </div>
                        <div class="gt-gantt-line">
                            <div
                                class="gt-gantt-bar {{ $parentIds !== '' ? 'has-parent' : '' }} {{ $childCount > 0 ? 'has-child' : '' }}"
                                data-gantt-task-id="{{ $task->id }}"
                                data-parent-ids="{{ $parentIds }}"
                                style="left: {{ $left }}%; width: {{ min(96 - $left, $width) }}%"
                            >
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

        <div class="gt-gantt-dependency-list">
            <strong>Abhängigkeiten</strong>
            @php
                $dependencies = collect($boardTasks ?? [])->flatMap(function ($task) {
                    return collect($task->dependencyParents ?? [])->map(function ($parent) use ($task) {
                        return ['parent' => $parent, 'child' => $task];
                    });
                });
            @endphp
            @forelse($dependencies as $dependency)
                <div class="gt-dependency-line-item">
                    <span>{{ $dependency['parent']->title }}</span>
                    <span class="gt-dependency-arrow">→</span>
                    <span>{{ $dependency['child']->title }}</span>
                </div>
            @empty
                <div class="gt-person-meta">Noch keine Abhängigkeiten vorhanden.</div>
            @endforelse
        </div>
    </div>
</div>
