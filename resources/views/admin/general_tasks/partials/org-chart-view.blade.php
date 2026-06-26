@php
    $priorityLabels = $priorityLabels ?? [
        'urgent' => 'Dringend',
        'important' => 'Wichtig',
        'normal' => 'Normal',
        'low' => 'Niedrig',
    ];

    $statusLabels = $statusLabels ?? [
        'open' => 'Offen',
        'in_progress' => 'In Bearbeitung',
        'review' => 'Zur Prüfung',
        'done' => 'Erledigt',
        'archived' => 'Archiviert',
    ];

    $priorityRank = [
        'urgent' => 1,
        'important' => 2,
        'normal' => 3,
        'low' => 4,
    ];

    $orgTasks = collect($boardTasks ?? [])->whereNotIn('status', ['archived'])->values();
    $taskById = $orgTasks->keyBy('id');
    $allIds = $taskById->keys()->map(fn ($id) => (int) $id)->values();

    $parentMap = [];
    $childMap = [];

    foreach ($orgTasks as $task) {
        $taskId = (int) $task->id;
        $parentIds = collect($task->dependsOn ?? $task->dependencyParents ?? [])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $allIds->contains($id))
            ->unique()
            ->values()
            ->all();

        $parentMap[$taskId] = $parentIds;

        foreach ($parentIds as $parentId) {
            $childMap[$parentId] = $childMap[$parentId] ?? [];
            $childMap[$parentId][] = $taskId;
        }
    }

    $sortTasksByPriority = function ($ids) use ($taskById, $priorityRank) {
        return collect($ids)
            ->filter(fn ($id) => $taskById->has((int) $id))
            ->unique()
            ->sortBy(function ($id) use ($taskById, $priorityRank) {
                $task = $taskById[(int) $id];
                return [
                    $priorityRank[$task->priority ?? 'normal'] ?? 5,
                    (int) ($task->sort_order ?? 999999),
                    $task->due_at ? \Carbon\Carbon::parse($task->due_at)->timestamp : PHP_INT_MAX,
                    (int) $task->id,
                ];
            })
            ->values();
    };

    $rootIds = $sortTasksByPriority($orgTasks->filter(function ($task) use ($parentMap) {
        return empty($parentMap[(int) $task->id] ?? []);
    })->pluck('id')->all());

    if ($rootIds->isEmpty()) {
        $rootIds = $sortTasksByPriority($orgTasks->pluck('id')->all());
    }

    $levels = [];
    $nodeLevel = [];
    $queue = [];

    foreach ($rootIds as $rootId) {
        $nodeLevel[(int) $rootId] = 0;
        $queue[] = (int) $rootId;
    }

    while (!empty($queue)) {
        $currentId = array_shift($queue);
        $currentLevel = (int) ($nodeLevel[$currentId] ?? 0);
        $levels[$currentLevel] = $levels[$currentLevel] ?? [];
        $levels[$currentLevel][] = $currentId;

        $children = $sortTasksByPriority($childMap[$currentId] ?? []);
        foreach ($children as $childId) {
            $childId = (int) $childId;
            $nextLevel = $currentLevel + 1;
            if (!isset($nodeLevel[$childId]) || $nodeLevel[$childId] < $nextLevel) {
                $nodeLevel[$childId] = $nextLevel;
                $queue[] = $childId;
            }
        }
    }

    $visitedIds = collect($nodeLevel)->keys()->map(fn ($id) => (int) $id);
    $unvisitedIds = $sortTasksByPriority($allIds->diff($visitedIds)->all());
    if ($unvisitedIds->count()) {
        $fallbackLevel = empty($levels) ? 0 : max(array_keys($levels)) + 1;
        foreach ($unvisitedIds as $id) {
            $nodeLevel[(int) $id] = $fallbackLevel;
            $levels[$fallbackLevel] = $levels[$fallbackLevel] ?? [];
            $levels[$fallbackLevel][] = (int) $id;
        }
    }

    ksort($levels);
@endphp

<div class="gt-view" id="gtOrgView">
    <section class="gt-org-panel gt-org-panel-v2">
        <div class="gt-org-head">
            <div>
                <div class="gt-org-title-main">Organigramm / Abhängigkeiten</div>
                <div class="gt-org-sub-main">Zeigt die Struktur als Ebenen: Vorgänger oben, abhängige Aufgaben darunter. Innerhalb jeder Ebene wird nach Priorität sortiert.</div>
            </div>
            <div class="gt-org-legend">
                <span class="priority urgent">Dringend</span>
                <span class="priority important">Wichtig</span>
                <span class="priority normal">Normal</span>
                <span class="priority low">Niedrig</span>
            </div>
        </div>

        <div class="gt-org-scroll gt-org-scroll-v2">
            @if($orgTasks->count())
                <div class="gt-org-stage" id="gtOrgStage">
                    <svg class="gt-org-link-svg" id="gtOrgLinkSvg" aria-hidden="true"></svg>

                    @foreach($levels as $level => $ids)
                        @php $orderedIds = $sortTasksByPriority($ids); @endphp
                        <div class="gt-org-level" data-org-level="{{ $level }}">
                            @foreach($orderedIds as $taskId)
                                @php
                                    $task = $taskById[(int) $taskId] ?? null;
                                    if (!$task) { continue; }

                                    $steps = collect($task->steps ?? []);
                                    $stepCount = $steps->count();
                                    $doneSteps = $steps->filter(fn ($step) => !empty($step->is_done))->count();
                                    $progress = (int) ($task->progress_percent ?? 0);

                                    if ($progress <= 0 && $stepCount > 0) {
                                        $progress = (int) round(($doneSteps / max($stepCount, 1)) * 100);
                                    }
                                    if (($task->status ?? '') === 'done') {
                                        $progress = 100;
                                    }
                                    $progress = max(0, min(100, $progress));

                                    $priority = $task->priority ?? 'normal';
                                    $status = $task->status ?? 'open';
                                    $parentIds = collect($parentMap[(int) $task->id] ?? [])->implode(',');
                                    $childCount = collect($childMap[(int) $task->id] ?? [])->count();
                                @endphp

                                <article
                                    class="gt-org-node priority-{{ $priority }} status-{{ $status }}"
                                    data-org-node
                                    data-org-task-id="{{ (int) $task->id }}"
                                    data-org-parent-ids="{{ $parentIds }}"
                                >
                                    <div class="gt-org-node-top">
                                        <span class="gt-org-priority">{{ $priorityLabels[$priority] ?? $priority }}</span>
                                        <span class="gt-org-status">{{ $statusLabels[$status] ?? $status }}</span>
                                    </div>
                                    <div class="gt-org-title">{{ $task->title ?? 'Ohne Titel' }}</div>
                                    <div class="gt-org-sub">{{ $doneSteps }}/{{ $stepCount }} Schritte erledigt</div>
                                    <div class="gt-org-progress"><span style="width:{{ $progress }}%"></span></div>
                                    <div class="gt-org-foot">
                                        <strong>{{ $progress }}%</strong>
                                        <span>{{ $childCount ? $childCount . ' Kind-Aufgabe(n)' : 'Keine Kinder' }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @else
                <div class="gt-empty">Keine Aufgaben für das Organigramm vorhanden.</div>
            @endif
        </div>
    </section>
</div>
