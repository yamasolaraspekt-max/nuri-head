@php
    use Illuminate\Support\Str;

    // Current employee
    $currentEmployeeId = $currentEmployeeId ?? null;

    // meta as array
    $meta = $report->meta ?? [];
    if (!is_array($meta)) {
        $meta = [];
    }

    $likes    = $meta['likes'] ?? [];
    $dislikes = $meta['dislikes'] ?? [];
    $items    = $meta['items'] ?? [];

    $likes    = is_array($likes) ? $likes : [];
    $dislikes = is_array($dislikes) ? $dislikes : [];
    $items    = is_array($items) ? $items : [];

    $likesCount    = count($likes);
    $dislikesCount = count($dislikes);
    $commentsCount = count($items);

    // My reaction
    $myReaction = null;
    if ($currentEmployeeId) {
        if (in_array($currentEmployeeId, $likes)) {
            $myReaction = 'like';
        } elseif (in_array($currentEmployeeId, $dislikes)) {
            $myReaction = 'dislike';
        }
    }

    /** @var \App\Models\MainAppointment|null $appointment */
    $appointment = $report->appointment ?? null;

    // -------- NEW: split the combined report text ----------------
    $rawReport = (string) ($report->report ?? '');

    // First line as title
    $titleLine = trim(Str::before($rawReport, "\n"));

    // Content = everything after a blank line (title + \n\n + content)
    $contentPart = Str::after($rawReport, "\n\n");

    // Fallback: if there is no double line break, take everything after first \n
    if (trim($contentPart) === '') {
        $contentPart = Str::after($rawReport, "\n");
    }

    $contentPart = trim($contentPart);
@endphp


<div class="ap-report-card"
     data-report-id="{{ $report->id }}">
    {{-- Header: Titel + Datum + Phase + Termin-Info --}}
    <div class="ap-report-top">
        <div>
            <div class="ap-report-title">{{ $titleLine }}</div>

            <div class="ap-report-sub">
                {{ optional($report->created_at)->format('d.m.Y H:i') }}
                @if($report->stage)
                    · {{ ucfirst($report->stage) }}
                @endif
            </div>

            @if($appointment)
                <div class="ap-report-appt">
                    <i class="feather icon-calendar mr-25"></i>
                    <span class="ap-report-appt-title">
                        {{ $appointment->name ?? 'Termin' }}
                    </span>
                    <span class="ap-report-appt-date">
                        {{ optional($appointment->start_date)->format('d.m.Y') }}
                        @if($appointment->start_time)
                            · {{ \Illuminate\Support\Str::substr($appointment->start_time,0,5) }}
                            @if($appointment->end_time)
                                – {{ \Illuminate\Support\Str::substr($appointment->end_time,0,5) }}
                            @endif
                        @endif
                    </span>
                    @if($appointment->appointment_type)
                        <span class="ap-report-appt-badge">
                            {{ $appointment->appointment_type }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        @if($report->stage)
            <span class="ap-report-stage">{{ ucfirst($report->stage) }}</span>
        @endif
    </div>

    {{-- Inhalt --}}
    <div class="ap-report-body">
            {!! nl2br(e($contentPart !== '' ? $contentPart : $rawReport)) !!} 
        </div>


    {{-- Footer: Autor + Reaktionen + Kommentare-Button --}}
    <div class="ap-report-footer">
        <div class="ap-report-author">
            <img src="{{ $report->author?->image
                        ? asset('images/employee/'.$report->author->image)
                        : asset('images/employee/noimage.png') }}"
                 class="ap-report-avatar"
                 alt="">
            <span>
                {{ $report->author?->lastname }} {{ $report->author?->name }}
            </span>
        </div>

        <div class="ap-report-actions">
            <button type="button"
                    class="ap-report-like {{ $myReaction === 'like' ? 'is-active' : '' }}">
                <i class="feather icon-thumbs-up"></i>
                <span class="ap-report-like-count">{{ $likesCount }}</span>
            </button>

            <button type="button"
                    class="ap-report-dislike {{ $myReaction === 'dislike' ? 'is-active' : '' }}">
                <i class="feather icon-thumbs-down"></i>
                <span class="ap-report-dislike-count">{{ $dislikesCount }}</span>
            </button>

            <button type="button"
                    class="ap-report-comments-toggle"
                    data-report-toggle-comments>
                <i class="feather icon-message-circle mr-25"></i>
                Kommentare ({{ $commentsCount }})
            </button>
        </div>
    </div>

    {{-- Kommentare --}}
    <div class="ap-report-comments" hidden>
        <div class="ap-report-comments-list">
            @foreach($items as $comment)
                @php
                    /** @var \App\Models\Employee|null $employee */
                    $employee = $commentEmployees->get($comment['employee_id'] ?? null);
                @endphp

                <div class="ap-report-comment-row">
                    <img src="{{ $employee && $employee->image
                                ? asset('images/employee/'.$employee->image)
                                : asset('images/employee/noimage.png') }}"
                         class="ap-report-comment-avatar"
                         alt="">
                    <div class="ap-report-comment-bubble">
                        <div class="ap-report-comment-meta">
                            {{ $employee?->lastname }} {{ $employee?->name }}
                            · {{ \Illuminate\Support\Carbon::parse($comment['created_at'] ?? now())->format('d.m.Y H:i') }}
                        </div>
                        <div>{{ $comment['text'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="ap-report-comment-composer mt-1">
            <textarea class="form-control ap-report-comment-text"
                      placeholder="Kommentar schreiben…"></textarea>
            <button type="button"
                    class="btn btn-primary btn-sm ap-report-comment-submit">
                Senden
            </button>
        </div>
    </div>
</div>
