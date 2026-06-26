@php
    $ctx = $ctx ?? [];
@endphp

@if($tasks->count())
    @foreach($tasks as $task)
        @php
            $assignedBy = $task->assignedBy ?? null;
            $employees = $task->employees ?? collect();
            $steps = $task->steps ?? $task->keys ?? collect();
            $comments = $task->rootComments ?? $task->comments ?? collect();
        @endphp

        <div class="ma-feed-card" data-task-id="{{ $task->id }}">
            <button type="button" class="ma-feed-head" data-feed-collapse>
                <span class="ma-note-type-icon bg-orange">
                    <i data-feather="check-square"></i>
                </span>

                <span class="flex-grow-1">
                    <span class="ma-feed-title">
                        {{ $task->task_title ?: 'Aufgabe #' . $task->id }}
                    </span>

                    <span class="ma-feed-meta d-block">
                        Status: {{ $task->task_status ?: '-' }}
                        · Priorität: {{ $task->priority ?: '-' }}
                        @if($task->due_date)
                            · Fällig: {{ optional($task->due_date)->format('d.m.Y') }}
                        @endif
                    </span>

                    @if($task->description)
                        <span class="ma-feed-preview d-block">
                            {{ \Illuminate\Support\Str::limit(strip_tags($task->description), 90) }}
                        </span>
                    @endif
                </span>

                <i data-feather="chevron-down"></i>
            </button>

            <div class="ma-feed-body">
                <div class="d-flex align-items-center mb-2">
                    @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                        'employee' => $assignedBy,
                        'size' => 28,
                    ])

                    <div class="ml-2">
                        <div class="ma-feed-title">
                            Erstellt von:
                            {{ $assignedBy ? trim($assignedBy->name . ' ' . $assignedBy->lastname) : '-' }}
                        </div>
                        <div class="ma-feed-meta">
                            {{ optional($task->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                @if($employees->count())
                    <div class="ma-feed-people mb-2">
                        @foreach($employees as $employee)
                            <span class="ma-feed-person">
                                @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                                    'employee' => $employee,
                                    'size' => 22,
                                ])
                                {{ $employee->name }} {{ $employee->lastname }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($task->description)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-1">Beschreibung</div>
                        <div class="ma-feed-content">
                            {!! $task->description !!}
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded p-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="ma-feed-title">Fortschritt</div>
                        <small class="ma-feed-meta">{{ (int) $task->progress }}%</small>
                    </div>

                    <div class="progress" style="height: 7px;">
                        <div class="progress-bar" style="width: {{ max(0, min(100, (int) $task->progress)) }}%;"></div>
                    </div>
                </div>

                @if($steps->count())
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-2">Schritte</div>

                        @foreach($steps as $step)
                            <div class="ma-feed-mini-row">
                                <span>
                                    <i data-feather="{{ $step->is_completed ? 'check-circle' : 'circle' }}"></i>
                                    {{ $step->task ?: 'Schritt #' . $step->id }}
                                </span>

                                <small>
                                    {{ $step->doneBy ? trim($step->doneBy->name . ' ' . $step->doneBy->lastname) : ($step->status ?: '-') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="ma-feed-title mb-2">Kommentare</div>

                @forelse($comments as $comment)
                    <div class="ma-feed-comment">
                        <div class="d-flex align-items-start">
                            @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                                'employee' => $comment->author ?? $comment->employee ?? null,
                                'size' => 26,
                            ])

                            <div class="ml-2 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="ma-feed-author">
                                        {{ optional($comment->author ?? $comment->employee)->name }}
                                        {{ optional($comment->author ?? $comment->employee)->lastname }}
                                    </strong>

                                    <small class="ma-feed-meta">
                                        {{ optional($comment->created_at)->format('d.m.Y H:i') }}
                                    </small>
                                </div>

                                <div class="ma-feed-content mt-1">
                                    {!! $comment->comment !!}
                                </div>
                            </div>
                        </div>

                        @if($comment->replies && $comment->replies->count())
                            <div class="ma-feed-replies">
                                @foreach($comment->replies as $reply)
                                    <div class="ma-feed-comment is-reply">
                                        <div class="ma-feed-meta">
                                            {{ optional($reply->author ?? $reply->employee)->name }}
                                            {{ optional($reply->author ?? $reply->employee)->lastname }}
                                            · {{ optional($reply->created_at)->format('d.m.Y H:i') }}
                                        </div>

                                        <div class="ma-feed-content mt-1">
                                            {!! $reply->comment !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="ma-feed-empty mb-2">
                        Keine Kommentare vorhanden.
                    </div>
                @endforelse

                <form class="ma-context-form mt-2" data-context-post="{{ route('customer.context-feed.task.comment', $task->id) }}">
                    @csrf

                    <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Neuen Aufgaben-Kommentar schreiben..."></textarea>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i data-feather="plus"></i>
                        Kommentar speichern
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@else
    @include('admin.new_leads.layouts.context-feed.empty', [
        'title' => 'Keine Aufgaben',
        'message' => 'Keine Aufgaben für diesen Kundenbereich gefunden.',
        'icon' => 'check-square',
    ])
@endif