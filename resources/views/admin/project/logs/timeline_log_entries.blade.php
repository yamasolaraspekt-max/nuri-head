@if($logs->count())
        <div class="mb-3 p-2 rounded bg-white border">
            <h6 class="mb-1 text-primary text-1xl">Projektdetails</h6>
            @php
                $serviceMap = [
                    'complete' => 'Komplett',
                    'plan' => 'Planung',
                    'maintenance' => 'Wartung',
                    'montage' => 'Montage',
                    'repair' => 'Reparatur',
                    'others' => 'Sonstiges',
                    'product' => 'Produkt',
                ];
                $translatedService = $serviceMap[$timeline->project_service ?? ''] ?? ucfirst($timeline->project_service ?? '-');
            @endphp
            <div class="small">
                <strong>Projekt:</strong> {{ $translatedService }}<br>
                <strong>Phase:</strong> {{ $timeline->phase_name ?? '-' }}<br>
                <strong>Aktivität:</strong> {{ $timeline->activity_title ?? '-' }}<br>
                <strong>Geplant von:</strong> {{ $timeline->start_date }} bis {{ $timeline->due_date }}<br>
                <strong>Status:</strong> {{ $timeline->is_done ?? 'Nicht definiert' }}<br>
                <strong>Letzter Fortschritt:</strong> {{ $timeline->done_range ?? 0 }}%
            </div>
        </div>


    @foreach($logs as $log)
        <div class="timeline-entry">
            <div><strong>{{ $log->activity_title ?? '-' }}</strong></div>
            <div class="small text-muted">
                <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($log->done_date)->format('d.m.Y') ?? '-' }}<br>
                <i class="feather icon-user"></i> {{ $log->employee_name ?? 'Unbekannt' }}<br>
                <i class="feather icon-bar-chart"></i> Log Fortschritt: {{ $log->timeline_range ?? '-' }}%
            </div>
        </div>
    @endforeach

@else
    <div class="text-center text-muted py-4">
        🚫 Keine Logeinträge gefunden.
    </div>
@endif
