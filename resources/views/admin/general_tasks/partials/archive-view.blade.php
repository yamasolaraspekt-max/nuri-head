<div class="gt-view" id="gtArchiveView">
    <div class="gt-archive-list">
        @forelse($archiveTasks as $task)
            <div class="gt-archive-row">
                <div>
                    <strong>{{ $task->title }}</strong>
                    <div class="gt-person-meta">{{ Str::limit($task->description, 90) }}</div>
                </div>

                <div>
                    <span class="gt-badge gray">{{ $statusLabels[$task->status] ?? $task->status }}</span>
                </div>

                <div>{{ optional($task->department)->department_name ?? 'Alle' }}</div>

                <div>{{ $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('d.m.Y H:i') : '—' }}</div>

                <div style="text-align:right">
                    <button class="gt-btn-soft" type="button" data-task-id="{{ $task->id }}" onclick="gtRestoreTask(this)">Wiederherstellen</button>
                </div>
            </div>
        @empty
            <div class="gt-empty">Keine archivierten Aufgaben gefunden.</div>
        @endforelse
    </div>
</div>
