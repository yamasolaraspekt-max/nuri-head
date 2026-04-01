@php
    $stageLabels = [
        'lead'      => 'Lead',
        'offer'     => 'Angebot',
        'deal'      => 'Auftrag',
        'project'   => 'Montage',
        'complete'  => 'Abschluss',
        'review'    => 'Auswertung',
        'archive'   => 'Archiv',
        'ticket'    => 'Ticket',
        'pause'     => 'Pausiert',
        'cancel'    => 'Storniert',
    ];
@endphp

@foreach($reports as $report)
    <div class="timeline-item">
        <div class="d-flex align-items-center">
            <img src="/images/employee/{{ $report->reporter->image ?? 'default.png' }}" class="rounded-circle mr-2" width="40" height="40">
            <div>
                <strong>{{ $report->reporter->fullname }}</strong>
                 <small class="d-block text-muted">
                    {{ \Carbon\Carbon::parse($report->created_at)->format('d.m.Y') }} —
                    {{ $stageLabels[$report->stage] ?? ucfirst($report->stage) }}
                </small>

            </div>
            @if(auth()->user()->name == $report->report_by)
                <div class="ml-auto">
                    <button class="btn btn-sm btn-outline-primary edit-report" data-id="{{ $report->id }}">Edit</button>
                    <button class="btn btn-sm btn-outline-danger delete-report" data-id="{{ $report->id }}">Delete</button>
                    <button class="btn btn-sm btn-outline-primary open-comment-sidebar" data-report-id="{{ $report->id }}">
                        Kommentare <span class="badge badge-primary" style="  position: absolute; top: 12px;">{{ $report->comments->count() }}</span>
                    </button>

                </div>
            @endif
        </div>
        <div class="mt-2">{!! $report->report !!}</div>
    </div>
@endforeach
