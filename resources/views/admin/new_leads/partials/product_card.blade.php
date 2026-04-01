@php 
  
     $progress = ($total ?? 0) > 0 ? round(($done / $total) * 100) : 0;
@endphp

@php
    $stage = $stage ?? '';
    $stageMap = [
        'offer' => 'Angebot', 'deal' => 'Auftrag', 'project' => 'Montage', 'complete' => 'Abschluss',
        'completed' => 'Abschluss', 'ticket' => 'Ticket', 'evaluation' => 'Auswertung',
        'archive' => 'Archiv', 'lead' => 'Lead', 'pause' => 'Pause', 'junk' => 'Junk'
    ];
    $translatedStage = $stageMap[$stage] ?? $stage;
@endphp


<div class="card shadow-sm border-0" style="background: #F1F1F1;">
    <div class="card-body p-3">

        {{-- Header Row --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                     style="width: 48px; height: 48px; font-weight: bold; font-size: 16px;">
                    {{ $initial ?? '–' }}
                </div>
                <img src="{{ asset('images/employee/' . ($employee_image ?? 'default.png')) }}"
                     class="rounded-circle ml-n2 border border-white"
                     style="width: 24px; height: 24px; object-fit: cover;" />
            </div>
            <div>
                <div class="font-weight-bold">{{ ucfirst($service ?? 'Service') }}</div>
                <div class="text-muted small">{{ ucfirst($interest ?? '-') }}</div>
                <div class="text-muted small">{{ $translatedStage }}</div>
            </div>
            <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Details ansehen">
                <i class="feather icon-eye text-primary"></i>
            </button>
        </div>

        {{-- Status --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <div class="font-weight-bold">Status</div>
                <div class="text-muted">aktiv</div>
            </div>
            <div class="d-flex align-items-center">
                <div class="progress" style="width: 100px; height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ $progress }}%;"></div>
                </div>
                <div class="ml-2 text-muted small">{{ $done }}/{{ $total }}</div>
            </div>
        </div>

        <hr class="my-2">

        {{-- Info Sections --}}
        <div class="py-1"><strong>Phase:</strong><br>{{ $phase_name ?? '–' }}</div>
        <div class="py-1"><strong>Schritt:</strong><br>{{ $activity_title ?? '–' }}</div>

        <div class="py-1"><strong>Aufgabe:</strong><br>
            <span class="text-muted">Lorem ipsum Task placeholder</span>
        </div>

        <div class="d-flex align-items-center justify-content-between py-1">
            <div>
                <strong>Zuständig</strong><br>
                <span class="text-muted">{!! $done_by_badge ?? '–' !!}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between py-1">
            <div>
                <strong>erledigt am</strong><br>
                <span class="text-muted">{{ $changed_at ?? '–' }}</span>
            </div>
            <div class="text-muted small">{{ $marked_by_name ?? '–' }}</div>
        </div>

        <div class="py-1">
            <strong>nächster Schritt</strong><br>
            <div id="{{ $carousel_id ?? 'next_phase_station' }}">⏳</div>
        </div>
    </div>
</div>
