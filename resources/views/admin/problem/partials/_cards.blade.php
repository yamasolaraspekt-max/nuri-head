@php
$statusLabel = function ($status) {
    return match ($status) {
        'offen' => 'Offen',
        'process' => 'In Bearbeitung',
        'end' => 'Beendet',
        'junk' => 'Junk',
        default => ucfirst($status ?: 'offen'),
    };
};

$statusClass = function ($status) {
    return match ($status) {
        'offen' => 'open',
        'process' => 'process',
        'end' => 'end',
        'junk' => 'junk',
        default => 'open',
    };
};

$priorityClass = function ($priority) {
    return match (strtolower($priority ?? '')) {
        'high' => 'high',
        'medium' => 'medium',
        'low' => 'low',
        default => '',
    };
};

$priorityLabel = function ($priority) {
    return match (strtolower($priority ?? '')) {
        'high' => 'Hoch',
        'medium' => 'Mittel',
        'low' => 'Niedrig',
        default => $priority ?: '—',
    };
};

$errorTypeLabel = function ($type) {
    $type = strtolower(trim((string) $type));

    return match ($type) {
        'complaint' => 'Reklamation',
        'emergency_service' => 'Notdienst',
        'repair' => 'Reparatur',
        'maintenance' => 'Wartung',
        'malfunction' => 'Störung',
        'installation' => 'Installation',
        'configuration_error' => 'Konfiguration',
        'system_outage' => 'Systemausfall',
        'security_issue' => 'Sicherheitsproblem',
        'user_error' => 'Bedienungsfehler',
        'network_problem' => 'Netzwerkfehler',
        'software_bug' => 'Softwarefehler',
        'hardware_defect' => 'Hardwarefehler',
        'spare_part_request' => 'Ersatzteilanfrage',
        'timeout' => 'Zeitüberschreitung',
        'communication_failure' => 'Kommunikationsproblem',
        'power_outage' => 'Energieausfall',
        'update_failure' => 'Updatefehler',
        'access_issue' => 'Zugriffsproblem',
        'other' => 'Sonstiges',

        default => $type
        ? ucfirst(str_replace(['_', '-'], ' ', $type))
        : '—',
    };
};
@endphp
@if($tickets->count())
    <div class="oc-card-grid">
        @foreach($tickets as $ticket)
            <div class="ticket-card">
                <div class="ticket-card-top">
                    <div>
                        <div class="ticket-card-title">
                            #{{ $ticket->ticket_no ?? $ticket->id }}
                        </div>
                        <div class="ticket-card-meta">
                            {{ trim(($ticket->firma ?? '') . ' ' . ($ticket->name ?? '') . ' ' . ($ticket->lastname ?? '')) ?: 'Kein Kunde' }}
                        </div>
                    </div>

                    <span class="oc-status-pill {{ $statusClass($ticket->status) }}">
                        {{ $statusLabel($ticket->status) }}
                    </span>
                </div>

                <div class="ticket-card-section">
                    <div class="ticket-card-label">Produkt</div>
                    <div class="ticket-card-meta">{{ $ticket->product ?: '—' }}</div>
                </div>

                <div class="ticket-card-section">
                    <div class="ticket-card-label">Fehlertyp</div>
                    <div class="ticket-card-meta">{{ $errorTypeLabel($ticket->error_type) }}</div>
                </div>

                <div class="ticket-card-section">
                    <div class="ticket-card-label">Priorität</div>
                    <span class="oc-priority {{ $priorityClass($ticket->priority) }}">
                        {{ $priorityLabel($ticket->priority) }}
                    </span>
                </div>

                <div class="ticket-card-section">
                    <div class="ticket-card-label">Datum</div>
                    <div class="ticket-card-meta">
                        {{ $ticket->start_date ? \Carbon\Carbon::parse($ticket->start_date)->format('d.m.Y') : '—' }}
                    </div>
                </div>

                <div class="ticket-card-actions">
                    <a href="{{ url('problem/profile/' . $ticket->id) }}" class="oc-btn-ic primary" title="Anzeigen">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </a>

                    <a href="{{ url('problem/' . $ticket->id . '/edit') }}" class="oc-btn-ic warning" title="Bearbeiten">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="oc-empty">Keine Tickets gefunden.</div>
@endif