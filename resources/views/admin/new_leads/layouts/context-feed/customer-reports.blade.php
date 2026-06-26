@php
    $ctx = $ctx ?? [];
@endphp

@if($reports->count())
    @foreach($reports as $report)
        @php
            $reporter = $report->reporter ?? null;
            $comments = $report->comments ?? collect();
        @endphp

        <div class="ma-feed-card" data-customer-report-id="{{ $report->id }}">
            <button type="button" class="ma-feed-head" data-feed-collapse>
                <span class="ma-note-type-icon bg-green">
                    <i data-feather="file-text"></i>
                </span>

                <span class="flex-grow-1">
                    <span class="ma-feed-title">
                        Kundenbericht #{{ $report->id }}
                    </span>

                    <span class="ma-feed-meta d-block">
                        Stage: {{ $report->stage ?: '-' }}
                        · {{ optional($report->created_at)->format('d.m.Y H:i') }}
                    </span>

                    @if($report->report)
                        <span class="ma-feed-preview d-block">
                            {{ \Illuminate\Support\Str::limit(strip_tags($report->report), 90) }}
                        </span>
                    @endif
                </span>

                <i data-feather="chevron-down"></i>
            </button>

            <div class="ma-feed-body">
                <div class="d-flex align-items-center mb-2">
                    @include('admin.new_leads.layouts.context-feed._employee-avatar', [
                        'employee' => $reporter,
                        'size' => 28,
                    ])

                    <div class="ml-2">
                        <div class="ma-feed-title">
                            Geschrieben von:
                            {{ $reporter ? trim($reporter->name . ' ' . $reporter->lastname) : '-' }}
                        </div>

                        <div class="ma-feed-meta">
                            {{ optional($report->created_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded p-2 mb-2">
                    <div class="ma-feed-title mb-1">Bericht</div>
                    <div class="ma-feed-content">
                        {!! $report->report ?: '<span class="text-muted">Kein Berichtstext vorhanden.</span>' !!}
                    </div>
                </div>

                @if($report->report_details)
                    <div class="bg-white rounded p-2 mb-2">
                        <div class="ma-feed-title mb-2">Details</div>

                        @foreach((array) $report->report_details as $key => $value)
                            <div class="ma-feed-mini-row">
                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <small>
                                    @if(is_array($value))
                                        {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="ma-feed-title mb-2">Kommentare</div>

                @forelse($comments as $comment)
                    <div class="ma-feed-comment">
                        <div class="d-flex justify-content-between">
                            <strong class="ma-feed-author">
                                {{ optional($comment->author ?? $comment->employee ?? $comment->creator ?? null)->name }}
                                {{ optional($comment->author ?? $comment->employee ?? $comment->creator ?? null)->lastname }}
                            </strong>

                            <small class="ma-feed-meta">
                                {{ optional($comment->created_at)->format('d.m.Y H:i') }}
                            </small>
                        </div>

                        <div class="ma-feed-content mt-1">
                            {!! $comment->comment ?? $comment->description ?? $comment->message ?? '' !!}
                        </div>
                    </div>
                @empty
                    <div class="ma-feed-empty mb-2">
                        Keine Kommentare vorhanden.
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
@else
    @include('admin.new_leads.layouts.context-feed.empty', [
        'title' => 'Keine Kundenberichte',
        'message' => 'Keine Kundenberichte für diesen Kundenbereich gefunden.',
        'icon' => 'file-text',
    ])
@endif