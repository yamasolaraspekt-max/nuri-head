<div class="gt-view" id="gtRecurringView">
    <div class="gt-recurring-panel">
        <div class="gt-gantt-head">
            <div class="gt-gantt-title">Wiederkehrende Aufgaben</div>
            <span class="gt-count">{{ $recurringNotificationTasks->count() }}</span>
        </div>

        <div class="gt-gantt-body">
            @forelse($recurringNotificationTasks as $recTask)
                <div class="gt-step-mini">
                    <i data-lucide="repeat-2" style="width:16px;height:16px;color:#b45309"></i>
                    <div class="gt-step-mini-main">
                        <div class="gt-step-mini-title">{{ $recTask->title }}</div>
                        <div class="gt-step-mini-meta">
                            {{ $recTask->recurrence_summary ?? ($recurrenceLabels[$recTask->recurrence_frequency] ?? 'Wiederkehrend') }}
                            @if(($recTask->show_due_datetime ?? true) && $recTask->due_at)
                                · {{ \Carbon\Carbon::parse($recTask->due_at)->format('d.m.Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="gt-empty">Keine wiederkehrenden Aufgaben im aktuellen Zeitraum.</div>
            @endforelse
        </div>
    </div>
</div>
