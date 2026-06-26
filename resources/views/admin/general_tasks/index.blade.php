@extends('admin.layouts.app')
@section('title', 'Allgemeine Aufgaben')

@php
use Carbon\Carbon;

$authEmployeeId = (int) auth()->user()->name;

$statusLabels = [
    'open' => 'Offen',
    'in_progress' => 'In Bearbeitung',
    'review' => 'Zur Prüfung',
    'done' => 'Erledigt',
    'archived' => 'Archiviert',
];

$priorityLabels = [
    'low' => 'Niedrig',
    'normal' => 'Normal',
    'important' => 'Wichtig',
    'urgent' => 'Dringend',
];

$priorityRank = [
    'urgent' => 1,
    'important' => 2,
    'normal' => 3,
    'low' => 4,
];

$recurrenceLabels = [
    'daily' => 'Täglich',
    'weekly' => 'Wöchentlich',
    'biweekly' => 'Alle 2 Wochen',
    'monthly' => 'Monatlich',
    'quarterly' => 'Quartalsweise',
    'semiannual' => 'Alle 6 Monate',
    'yearly' => 'Jährlich',
];

$weekdayLabels = [
    0 => 'Sonntag',
    1 => 'Montag',
    2 => 'Dienstag',
    3 => 'Mittwoch',
    4 => 'Donnerstag',
    5 => 'Freitag',
    6 => 'Samstag',
];

$columns = [
    'open' => [
        'label' => 'Offene Aufgaben',
        'hint' => 'Für alle oder Abteilung sichtbar',
    ],
    'in_progress' => [
        'label' => 'In Bearbeitung',
        'hint' => 'Übernommene Aufgaben',
    ],
    'review' => [
        'label' => 'Zur Prüfung',
        'hint' => 'Bericht oder Kontrolle offen',
    ],
    'done' => [
        'label' => 'Erledigt',
        'hint' => 'Abgeschlossene Aufgaben',
    ],
];

$taskCollection = collect($tasks ?? []);
$boardTasks = $taskCollection->whereNotIn('status', ['archived'])->sortBy(function ($task) use ($priorityRank) {
    return [
        $priorityRank[$task->priority ?? 'normal'] ?? 5,
        (int) ($task->sort_order ?? 999999),
        $task->due_at ? Carbon::parse($task->due_at)->timestamp : PHP_INT_MAX,
        (int) ($task->id ?? 0),
    ];
});
$archiveTasks = collect($archivedTasks ?? $taskCollection->where('status', 'archived'));
$employeesCollection = collect($employees ?? []);
$activeEmployeesCollection = collect($activeEmployees ?? $employees ?? []);

$myTasks = $boardTasks->filter(function ($task) use ($authEmployeeId) {
    $claimedBy = (int) ($task->claimed_by ?? 0);
    $assigneeIds = collect($task->assignees ?? [])->pluck('id');

    return $claimedBy === $authEmployeeId || $assigneeIds->contains($authEmployeeId);
});

$countOpen = $boardTasks->where('status', 'open')->count();
$countProgress = $boardTasks->where('status', 'in_progress')->count();
$countDone = $taskCollection->where('status', 'done')->count();
$countArchived = $archiveTasks->count();

$recurringNotificationTasks = collect(
    $recurringNotificationTasks
    ?? $boardTasks->filter(function ($task) {
        return !empty($task->is_recurring)
            && !in_array($task->status, ['archived'], true)
            && !empty($task->due_at)
            && Carbon::parse($task->due_at)->lte(now()->addDays(14)->endOfDay());
    })->sortBy('due_at')->take(12)->values()
);

/*
 * Keep JSON payloads safe.
 * Do not place complex PHP arrays/closures directly inside @json().
 * These variables are used by the scripts partials.
 */
$employeePayload = $employeesCollection->map(function ($employee) {
    $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

    return [
        'id' => (int) $employee->id,
        'name' => $fullName !== '' ? $fullName : ('#' . $employee->id),
        'image' => !empty($employee->image) ? asset('images/employee/' . $employee->image) : null,
        'initials' => mb_strtoupper(
            mb_substr($employee->name ?? 'M', 0, 1) . mb_substr($employee->lastname ?? '', 0, 1)
        ),
    ];
})->values()->all();

$taskSelectPayload = $taskCollection->map(function ($task) {
    return [
        'id' => (int) $task->id,
        'title' => '#' . $task->id . ' — ' . ($task->title ?? 'Ohne Titel'),
    ];
})->values()->all();

$gtRoutesPayload = [
    'store' => route('general-tasks.store'),
    'updateBase' => url('/general-tasks'),
    'move' => route('general-tasks.move'),
    'reorder' => url('/general-tasks/reorder'),
    'reportBase' => url('/general-tasks'),
    'stepsBase' => url('/general-tasks'),
    'archiveBase' => url('/general-tasks'),
    'deleteBase' => url('/general-tasks'),
    'cardBase' => url('/general-tasks'),
];

$gtJsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
$gtRoutesJson = json_encode($gtRoutesPayload, $gtJsonFlags);
$employeePayloadJson = json_encode($employeePayload, $gtJsonFlags);
$taskSelectPayloadJson = json_encode($taskSelectPayload, $gtJsonFlags);
@endphp

@once
    @push('style')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        @include('admin.general_tasks.partials.styles')
        @include('admin.general_tasks.partials.styles-workflow-v4')
    @endpush
@endonce

@section('content')
    <div class="gt-wrap" data-auth-employee-id="{{ $authEmployeeId }}">
        <div class="gt-titlebar">
            <div>
                <div class="gt-title">Allgemeine Aufgaben</div>
                <div class="gt-sub">
                    Aufgaben, Schritte, Verantwortliche, Fortschritt, Abhängigkeiten und Archiv übersichtlich verwalten.
                </div>
                <div class="gt-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Home</a>
                    <span>›</span>
                    <span class="current">Allgemeine Aufgaben</span>
                </div>
            </div>

            <div class="gt-actions">
                <button type="button" class="gt-btn-soft" data-gt-sidebar-open="team">
                    <i data-lucide="panel-right-open" style="width:16px;height:16px"></i>
                    Team & Meine Aufgaben
                </button>

                <button type="button" class="gt-btn" onclick="gtOpenCreateTask()">
                    <i data-lucide="plus" style="width:16px;height:16px"></i>
                    Neue Aufgabe
                </button>
            </div>
        </div>

        <div class="gt-stats">
            <div class="gt-stat">
                <div class="gt-stat-icon open"><i data-lucide="inbox"></i></div>
                <div>
                    <div class="gt-stat-label">Offen</div>
                    <div class="gt-stat-value" data-stat="open">{{ $countOpen }}</div>
                    <div class="gt-stat-sub">Noch nicht übernommen</div>
                </div>
            </div>

            <div class="gt-stat">
                <div class="gt-stat-icon progress"><i data-lucide="loader-circle"></i></div>
                <div>
                    <div class="gt-stat-label">In Bearbeitung</div>
                    <div class="gt-stat-value" data-stat="in_progress">{{ $countProgress }}</div>
                    <div class="gt-stat-sub">Aktive Aufgaben</div>
                </div>
            </div>

            <div class="gt-stat">
                <div class="gt-stat-icon done"><i data-lucide="check-circle-2"></i></div>
                <div>
                    <div class="gt-stat-label">Erledigt</div>
                    <div class="gt-stat-value" data-stat="done">{{ $countDone }}</div>
                    <div class="gt-stat-sub">Abgeschlossen</div>
                </div>
            </div>

            <div class="gt-stat">
                <div class="gt-stat-icon archive"><i data-lucide="archive"></i></div>
                <div>
                    <div class="gt-stat-label">Archiv</div>
                    <div class="gt-stat-value" data-stat="archived">{{ $countArchived }}</div>
                    <div class="gt-stat-sub">Archivierte Aufgaben</div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('general-tasks.index') }}" class="gt-toolbar">
            <div class="gt-toolbar-left">
                <div class="gt-filter search">
                    <label class="gt-filter-label">Suche</label>
                    <input
                        class="gt-input"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Titel, Beschreibung, Bericht oder Mitarbeiter suchen"
                    >
                </div>

                <div class="gt-filter">
                    <label class="gt-filter-label">Abteilung</label>
                    <select class="gt-select" name="department_id">
                        <option value="">Alle</option>
                        @foreach($departments ?? [] as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="gt-filter">
                    <label class="gt-filter-label">Priorität</label>
                    <select class="gt-select" name="priority">
                        <option value="">Alle</option>
                        @foreach($priorityLabels as $key => $label)
                            <option value="{{ $key }}" @selected(request('priority') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="gt-toolbar-right">
                <button class="gt-btn-soft" type="submit">Suchen</button>
                <a href="{{ route('general-tasks.index') }}" class="gt-btn-soft">Zurücksetzen</a>
            </div>
        </form>

        <div class="gt-tabs">
            <button class="gt-tab active" type="button" data-view-tab="board">
                <i data-lucide="kanban-square" style="width:15px;height:15px"></i>
                Team Aufgaben
            </button>

            <button class="gt-tab" type="button" data-view-tab="recurring">
                <i data-lucide="repeat-2" style="width:15px;height:15px"></i>
                Wiederkehrende
                @if($recurringNotificationTasks->count())
                    <span class="gt-tab-count">{{ $recurringNotificationTasks->count() }}</span>
                @endif
            </button>

            <button class="gt-tab" type="button" data-view-tab="gantt">
                <i data-lucide="gantt-chart-square" style="width:15px;height:15px"></i>
                Ablaufdiagramm
            </button>

            <button class="gt-tab" type="button" data-view-tab="org">
                <i data-lucide="network" style="width:15px;height:15px"></i>
                Gantt-Diagramm
            </button>

            <button class="gt-tab" type="button" data-view-tab="archive">
                <i data-lucide="archive" style="width:15px;height:15px"></i>
                Archiv
            </button>
        </div>

        <div class="gt-view active" id="gtBoardView">
            <section class="gt-board" id="gtTaskBoard">
                @foreach($columns as $status => $column)
                    @php
    $columnTasks = $boardTasks->where('status', $status);
                    @endphp

                    <div class="gt-column" data-column="{{ $status }}">
                        <div class="gt-column-h">
                            <div>
                                <div class="gt-column-title">{{ $column['label'] }}</div>
                                <div class="gt-column-hint">{{ $column['hint'] }}</div>
                            </div>
                            <span class="gt-count" data-column-count="{{ $status }}">{{ $columnTasks->count() }}</span>
                        </div>

                        <div class="gt-dropzone" data-status="{{ $status }}">
                            @forelse($columnTasks as $task)
                                @include('admin.general_tasks.partials.card')
                            @empty
                                <div class="gt-empty" data-empty="{{ $status }}">Keine Aufgaben</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </section>
        </div>

        @include('admin.general_tasks.partials.recurring-view')

        @include('admin.general_tasks.partials.gantt-view-v4')

        @include('admin.general_tasks.partials.org-chart-view')

        @include('admin.general_tasks.partials.archive-view')

        <button type="button" class="gt-floating-sidebar-btn" data-gt-sidebar-open="mine" title="Team & Meine Aufgaben">
            <i data-lucide="users-round"></i>
        </button>
    </div>

    @include('admin.general_tasks.partials.sidebar')

    @include('admin.general_tasks.partials.task-modal')

    @include('admin.general_tasks.partials.reason-modal')
    @include('admin.general_tasks.partials.report-modal')
    @include('admin.general_tasks.partials.claim-modal')
    @include('admin.general_tasks.partials.step-check-modal')
    @include('admin.general_tasks.partials.task-detail-modal')
    @include('admin.general_tasks.partials.move-status-modal')

    <div class="gt-toast-wrap" id="gtToastWrap"></div>
@endsection

@once
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        @include('admin.general_tasks.partials.scripts')
        @include('admin.general_tasks.partials.scripts-workflow-v4')
    @endpush
@endonce
