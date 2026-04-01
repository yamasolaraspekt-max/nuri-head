@php
    $reporter = $report->reporter;
    $name = $reporter ? trim(($reporter->lastname ?? '').' '.($reporter->name ?? '')) : 'Unbekannt';
    $avatar = $reporter && $reporter->image
        ? asset('images/employee/'.$reporter->image)
        : asset('images/employee/noimage.png');
@endphp

<article class="cr-card" data-report-id="{{ $report->id }}">
    <div class="cr-card-head">
        <div class="cr-head-main">
            <div class="cr-author">
                <img src="{{ $avatar }}" alt="{{ $name }}" class="cr-avatar">
                <div>
                    <div class="cr-author-name">{{ $name }}</div>
                    <div class="cr-author-meta">
                        {{ optional($report->created_at)->format('d.m.Y H:i') }}
                        @if($report->stage)
                            · <span class="badge badge-light-secondary">{{ ucfirst($report->stage) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cr-card-body">
        {!! nl2br(e($report->report)) !!}
    </div>

    <div class="cr-card-foot">
        <button type="button"
                class="btn btn-sm btn-outline-secondary cr-toggle-comments"
                data-report-toggle-comments>
            <i class="feather icon-message-circle mr-25"></i>
            Kommentare ({{ $report->comments->count() }})
        </button>
    </div>

    <div class="cr-comments" hidden>
        <div class="cr-comments-list">
            @foreach($report->comments as $comment)
                @include('admin.kanban.partials.customer_report_comment', ['comment' => $comment])
            @endforeach
        </div>

        <div class="cr-comment-form">
            <textarea class="form-control form-control-sm cr-comment-text"
                      rows="2"
                      placeholder="Kommentar schreiben…"></textarea>
            <div class="d-flex justify-content-end mt-50">
                <button type="button"
                        class="btn btn-sm btn-primary cr-comment-submit">
                    Kommentar senden
                </button>
            </div>
        </div>
    </div>
</article>
