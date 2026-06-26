<div class="gt-view" id="gtGanttView">
    <div class="gt-gantt-pro">
        <div class="gt-gantt-pro-toolbar">
            <div>
                <div class="gt-gantt-pro-title">Gantt / Projektplan</div>
                <div class="gt-gantt-pro-sub">Praktische Ansicht mit Aufgabenstatus, Fortschritt und roten Abhängigkeitslinien von Vorgänger → abhängige Aufgabe.</div>
            </div>
            <div class="gt-actions">
                <button type="button" class="gt-btn-soft" data-gantt-zoom="out">− Zoom</button>
                <button type="button" class="gt-btn-soft" data-gantt-zoom="in">+ Zoom</button>
            </div>
        </div>

        <div class="gt-gantt-pro-scroll" id="gtGanttBody" data-gantt-zoom-level="1">
            <div class="gt-gantt-pro-inner" id="gtGanttProInner">
                <svg class="gt-gantt-link-svg" id="gtGanttLinkSvg" aria-hidden="true"></svg>

                @php
                    $ganttTasks = collect($boardTasks ?? [])->sortBy(function ($task) {
                        return $task->due_at ?: $task->created_at;
                    })->values();

                    $monthStart = now()->startOfMonth();
                    $monthLabels = collect(range(0, 5))->map(function ($i) use ($monthStart) {
                        return $monthStart->copy()->addMonths($i)->translatedFormat('F');
                    });

                    $statusDotClass = [
                        'open' => 'open',
                        'in_progress' => 'progress',
                        'review' => 'review',
                        'done' => 'done',
                    ];
                @endphp

                <div class="gt-gantt-pro-head">
                    <div class="gt-gantt-left-head">
                        <div>Name</div>
                        <div>Status</div>
                    </div>
                    <div class="gt-gantt-months">
                        @foreach($monthLabels as $monthLabel)
                            <div class="gt-gantt-month">{{ $monthLabel }}</div>
                        @endforeach
                    </div>
                </div>

                @forelse($ganttTasks as $task)
                    @php
                        $steps = collect($task->steps ?? []);
                        $doneSteps = $steps->filter(function ($step) { return !empty($step->is_done); })->count();
                        $stepCount = $steps->count();
                        $progress = (int) ($task->progress_percent ?? 0);
                        if ($progress <= 0 && $stepCount > 0) {
                            $progress = (int) round(($doneSteps / max($stepCount, 1)) * 100);
                        }
                        if (($task->status ?? '') === 'done') { $progress = 100; }
                        $progress = max(0, min(100, $progress));

                        $parents = collect($task->dependencyParents ?? $task->dependsOn ?? []);
                        $children = collect($task->dependencyChildren ?? $task->blockingTasks ?? []);
                        $parentIds = $parents->pluck('id')->implode(',');
                        $status = $task->status ?? 'open';
                        $dot = $statusDotClass[$status] ?? 'open';

                        // Stable layout until you have real start/end dates. Uses due date/order to place bars.
                        $left = (($loop->index * 47) % 760) + 18;
                        $width = max(86, min(260, 115 + ($stepCount * 24)));
                        if ($task->due_at) {
                            $diffDays = max(0, min(175, now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($task->due_at)->startOfDay(), false) + 30));
                            $left = 18 + (int) round($diffDays * 4.1);
                        }
                    @endphp

                    <div class="gt-gantt-pro-row" data-gantt-row-task-id="{{ $task->id }}">
                        <div class="gt-gantt-task-left">
                            <div class="gt-gantt-task-name">
                                <span class="gt-tree-dot"></span>
                                <span class="gt-gantt-task-title">{{ $task->title }}</span>
                            </div>
                            <div class="gt-gantt-state">
                                <span class="gt-gantt-state-dot {{ $dot }}"></span>
                                <span>{{ $statusLabels[$status] ?? $status }}</span>
                            </div>
                        </div>

                        <div class="gt-gantt-timeline-cell">
                            <div
                                class="gt-gantt-bar-pro {{ $parents->count() ? 'has-parent' : '' }} {{ $children->count() ? 'has-child' : '' }}"
                                data-gantt-task-id="{{ $task->id }}"
                                data-parent-ids="{{ $parentIds }}"
                                title="{{ $task->title }} — {{ $progress }}%"
                                style="left: {{ $left }}px; width: {{ $width }}px"
                            >
                                <div class="gt-gantt-bar-pro-fill" style="width: {{ $progress }}%"></div>
                                <div class="gt-gantt-bar-pro-label">{{ $progress }}%</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="gt-empty">Keine Aufgaben für den Gantt-Verlauf.</div>
                @endforelse
            </div>
        </div>

        <div class="gt-gantt-dependency-list">
            <strong>Abhängigkeiten:</strong>
            @php
                $dependencies = collect($boardTasks ?? [])->flatMap(function ($task) {
                    return collect($task->dependencyParents ?? $task->dependsOn ?? [])->map(function ($parent) use ($task) {
                        return ['parent' => $parent, 'child' => $task];
                    });
                });
            @endphp
            @forelse($dependencies as $dependency)
                <span class="gt-dependency-line-item">{{ $dependency['parent']->title }} → {{ $dependency['child']->title }}</span>
            @empty
                <span class="gt-person-meta">Noch keine Abhängigkeiten vorhanden.</span>
            @endforelse
        </div>
    </div>
</div>
