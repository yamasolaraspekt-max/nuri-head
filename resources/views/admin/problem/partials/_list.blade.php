@php
    $statusKey = function ($status) {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'open' => 'offen',
            'in_bearbeitung' => 'process',
            'done', 'beendet', 'ended' => 'end',
            default => $status ?: 'offen',
        };
    };

    $statusLabel = function ($status) use ($statusKey) {
        return match ($statusKey($status)) {
            'offen' => 'Offen',
            'process' => 'In Bearbeitung',
            'end' => 'Beendet',
            'junk' => 'Junk',
            default => ucfirst($status ?: 'offen'),
        };
    };

    $statusClass = function ($status) use ($statusKey) {
        return match ($statusKey($status)) {
            'offen' => 'open',
            'process' => 'process',
            'end' => 'end',
            'junk' => 'junk',
            default => 'open',
        };
    };

    $priorityClass = function ($priority) {
        return match (strtolower(trim((string) $priority))) {
            'high', 'dringend', 'sehr dringend', 'sehr_dringend' => 'high',
            'medium', 'mittel' => 'medium',
            'low', 'normal' => 'low',
            default => '',
        };
    };

    $priorityLabel = function ($priority) {
        return match (strtolower(trim((string) $priority))) {
            'high' => 'Hoch',
            'medium' => 'Mittel',
            'low' => 'Niedrig',
            'dringend' => 'Dringend',
            'sehr dringend', 'sehr_dringend' => 'Sehr dringend',
            'normal' => 'Normal',
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
            : 'Kein Fehlertyp',
        };
    };

    $customerName = function ($ticket) {
        return trim(
            ($ticket->firma ?? '') . ' ' .
            ($ticket->name ?? '') . ' ' .
            ($ticket->lastname ?? '')
        ) ?: 'Kein Kunde';
    };

    $ticketDate = function ($date, $format = 'd.m.Y') {
        return $date
            ? \Carbon\Carbon::parse($date)->format($format)
            : '—';
    };
@endphp

<div class="oc-list-head oc-list-head-compact">
    <div>Ticket</div>
    <div>Ticketdaten</div>
    <div>Status</div>
    <div>Priorität</div>
    <div>Datum</div>
    <div style="text-align:right;">Aktionen</div>
</div>

<div class="oc-list oc-list-compact">
    @forelse($tickets as $ticket)
        <div class="oc-item oc-item-compact">
            <div class="oc-item-row oc-item-row-compact">
                <div class="oc-cell oc-ticket-cell">
                    <div class="oc-cell-title">Ticket</div>
                    <span class="oc-id-badge oc-id-badge-sm">
                        #{{ $ticket->ticket_no ?? $ticket->id }}
                    </span>
                </div>

                <div class="oc-cell oc-main-cell">
                    <div class="oc-cell-title">Ticketdaten</div>

                    <div class="oc-compact-title">
                        {{ $customerName($ticket) }}
                    </div>

                    <div class="oc-compact-meta">
                        <span>
                            <strong>Produkt:</strong>
                            {{ $ticket->product ?: '—' }}
                        </span>

                        <span>
                            <strong>Fehler:</strong>
                            {{ $errorTypeLabel($ticket->error_type) }}
                            @if(!empty($ticket->error_code))
                                · {{ $ticket->error_code }}
                            @endif
                        </span>

                        @if(!empty($ticket->street))
                            <span>
                                <strong>Adresse:</strong>
                                {{ $ticket->street }}
                                @if(!empty($ticket->postcode) || !empty($ticket->alt_city))
                                    , {{ trim(($ticket->postcode ?? '') . ' ' . ($ticket->alt_city ?? '')) }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="oc-cell oc-badge-cell">
                    <div class="oc-cell-title">Status</div>
                    <span class="oc-status-pill oc-fixed-badge {{ $statusClass($ticket->status) }}">
                        {{ $statusLabel($ticket->status) }}
                    </span>
                </div>

                <div class="oc-cell oc-badge-cell">
                    <div class="oc-cell-title">Priorität</div>
                    <span class="oc-priority oc-fixed-badge {{ $priorityClass($ticket->priority) }}">
                        {{ $priorityLabel($ticket->priority) }}
                    </span>
                </div>

                <div class="oc-cell oc-date-cell">
                    <div class="oc-cell-title">Datum</div>
                    <div class="oc-date-main">
                        {{ $ticketDate($ticket->start_date) }}
                    </div>
                    <div class="oc-date-sub">
                        Update: {{ $ticketDate($ticket->updated_at, 'd.m.Y H:i') }}
                    </div>
                </div>

                <div class="oc-cell oc-action-cell">
                    <div class="oc-cell-title">Aktionen</div>

                    <div class="oc-actions oc-actions-compact">
                        <a href="{{ url('problem/profile/' . $ticket->id) }}" class="oc-btn-ic primary" title="Anzeigen">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </a>

                        <a href="{{ url('problem_edit/' . $ticket->id) }}" class="oc-btn-ic warning" title="Bearbeiten">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </a>

                        <a href="{{ url('problem_destroy/' . $ticket->id) }}" class="oc-btn-ic danger" title="Löschen"
                            onclick="return confirm('Möchten Sie dieses Ticket wirklich löschen?')">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="oc-empty">Keine Tickets gefunden.</div>
    @endforelse
</div>