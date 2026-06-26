<div class="gt-task-actions">
    <button class="gt-btn-ic primary" type="button" onclick="gtOpenReport(this)" title="Kommentar / Bericht">
        <i data-lucide="message-square-plus" style="width:15px;height:15px"></i>
    </button>

    @if(($task->status ?? '') !== 'archived')
        <button class="gt-btn-ic gt-card-archive-btn" type="button" onclick="gtArchiveTask(this)" data-task-id="{{ $task->id }}" title="Archivieren">
            <i data-lucide="archive" style="width:15px;height:15px"></i>
        </button>

        <button class="gt-btn-ic danger gt-card-delete-btn" type="button" onclick="gtDeleteTask(this)" data-task-id="{{ $task->id }}" title="Löschen">
            <i data-lucide="trash-2" style="width:15px;height:15px"></i>
        </button>
    @endif
</div>
