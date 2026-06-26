@php
    $ctx = $ctx ?? [];
@endphp

@if($tickets->count())
    @foreach($tickets as $ticket)
        @php
            $responsible = $ticket->responsibleEmployee ?? null;
            $startedBy = $ticket->startUser ?? null;
            $comments = $ticket->comments ?? collect();
            $tasks = $ticket->ticketTasks ?? $ticket->ticket_tasks ?? collect();
        @endphp

        <div class="ma-feed-card" data-ticket-id="{{ $ticket->id }}">
            <button type="button" class="ma-feed-head" data-feed-collapse>
                <span class="ma-note-type-icon bg-pink">
                    <i data-feather="alert-triangle"></i>
                </span>

                <span class="flex-grow-1">
                    <span class="ma-feed-title">
                        {{ $ticket->ticket_no ?: 'Ticket #' . $ticket->id }}
                    </span>

                    <span class="ma-feed-meta d-block">
                        {{ optional($ticket->date)->format('d.m.Y') ?: optional($ticket->created_at)->format('d.m.Y') }}
                        · Status: {{ $ticket->status ?: '-' }}
                        · Priorität: {{ $ticket->priority ?: '-' }}
                    </span>

                    @if($ticket->problem)
                        <span class="ma-feed-preview d-block">
                            {{ \Illuminate\Support\Str::limit(strip_tags($ticket->problem), 90) }}
                        </span>
                    @endif
                </span>

                <i data-feather="chevron-down"></i>
            </button>

            <div class="ma-feed-body">
                <div class="d-flex align-items-center mb-2">
                    @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                        'employee' => $responsible ?: $startedBy,
                        'size' => 28,
                    ])

                    <div class="ml-2">
                        <div class="ma-feed-title">
                            {{ $responsible ? trim($responsible->name . ' ' . $responsible->lastname) : 'Kein Verantwortlicher' }}
                        </div>
                        <div class="ma-feed-meta">
                            Erstellt von:
                            {{ $startedBy ? trim($startedBy->name . ' ' . $startedBy->lastname) : '-' }}
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded p-2 mb-2">
                    <div class="ma-feed-title mb-1">Problem</div>
                    <div class="ma-feed-content">
                        {!! $ticket->problem ?: '<span class="text-muted">Keine Problembeschreibung vorhanden.</span>' !!}
                    </div>
                </div>

                @if($ticket->solution)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-1">Lösung</div>
                        <div class="ma-feed-content">
                            {!! $ticket->solution !!}
                        </div>
                    </div>
                @endif

                @if($tasks->count())
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-2">Ticket-Aufgaben</div>

                        @foreach($tasks as $task)
                            <div class="ma-feed-mini-row">
                                <span>
                                    <i data-feather="check-circle"></i>
                                    {{ $task->title ?? $task->task ?? $task->name ?? 'Aufgabe #' . $task->id }}
                                </span>
                                <small>{{ $task->status ?? '-' }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="ma-feed-title mb-2">Kommentare</div>

                @forelse($comments as $comment)
                    <div class="ma-feed-comment">
                        <div class="d-flex align-items-start">
                            @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                                'employee' => $comment->employee ?? null,
                                'size' => 26,
                            ])

                            <div class="ml-2 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="ma-feed-author">
                                        {{ optional($comment->employee)->name }}
                                        {{ optional($comment->employee)->lastname }}
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
                                        <div class="d-flex align-items-start">
                                            @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                                                'employee' => $reply->employee ?? null,
                                                'size' => 24,
                                            ])

                                            <div class="ml-2 flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong class="ma-feed-author">
                                                        {{ optional($reply->employee)->name }}
                                                        {{ optional($reply->employee)->lastname }}
                                                    </strong>

                                                    <small class="ma-feed-meta">
                                                        {{ optional($reply->created_at)->format('d.m.Y H:i') }}
                                                    </small>
                                                </div>

                                                <div class="ma-feed-content mt-1">
                                                    {!! $reply->comment !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="ma-feed-empty mb-2">
                        Keine Ticket-Kommentare vorhanden.
                    </div>
                @endforelse

                <form class="ma-context-form mt-2" data-context-post="{{ route('customer.context-feed.ticket.comment', $ticket->id) }}">
                    @csrf
                    <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Neuen Ticket-Kommentar schreiben..."></textarea>

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
        'title' => 'Keine Tickets',
        'message' => 'Keine Tickets für diesen Kundenbereich gefunden.',
        'icon' => 'alert-triangle',
    ])
@endif