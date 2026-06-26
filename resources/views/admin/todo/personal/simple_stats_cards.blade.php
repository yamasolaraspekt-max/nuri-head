<style>
    .pt-summary-row {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: .85rem;
    }

    .pt-summary-card {
        flex: 1 1 190px;
        min-width: 190px;
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: .75rem .9rem .7rem;
        display: flex;
        flex-direction: column;
        gap: .25rem;
    }

    .pt-summary-top {
        display: flex;
        align-items: center;
        gap: .55rem;
    }

    .pt-summary-icon {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pt-summary-icon svg {
        width: 18px;
        height: 18px;
        stroke: #4b5563;
    }

    .pt-summary-label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .pt-summary-value {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        line-height: 1.1;
    }

    .pt-summary-sub {
        font-size: 10px;
        color: #9ca3af;
        margin-top: .15rem;
    }
</style>

@php
    $stats = is_array($stats ?? null) ? $stats : [];
    $defaults = [
        'total' => 0,
        'assigned_by_me' => 0,
        'assigned_to_me' => 0,
        'completed' => 0,
        'paused' => 0,
        'cancel' => 0,
        'rejected' => 0,
        'archived' => 0,
        'shares' => ['done' => 0, 'open' => 0],
    ];
    $stats = array_merge($defaults, $stats);
    $stats['shares'] = array_merge($defaults['shares'], $stats['shares'] ?? []);
@endphp

<div class="pt-summary-row">
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <circle cx="7.5" cy="12" r="3.5" stroke-width="1.7" />
                    <circle cx="16.5" cy="8" r="2.5" stroke-width="1.7" />
                    <circle cx="16.5" cy="16" r="2.5" stroke-width="1.7" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Alle Aufgaben</div>
                <div class="pt-summary-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Erstellt von dir oder dir zugewiesen</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <path d="M15.5 5L19 8.5L8.5 19H5V15.5L15.5 5Z" stroke-width="1.7" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Von mir erstellt</div>
                <div class="pt-summary-value">{{ $stats['assigned_by_me'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Aufgaben, bei denen du der Ersteller bist</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4Z" stroke-width="1.7" />
                    <path d="M5 19.5C6.4 17.4 8.9 16 12 16s5.6 1.4 7 3.5" stroke-width="1.7" stroke-linecap="round" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Mir zugewiesen</div>
                <div class="pt-summary-value">{{ $stats['assigned_to_me'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Tasks, in denen du als Mitarbeiter hinterlegt bist</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="8" stroke-width="1.7" />
                    <path d="M9 12.5L11 14.5L15 10" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Erledigt</div>
                <div class="pt-summary-value">{{ $stats['completed'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Aufgaben mit Status „completed“</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <rect x="8" y="6" width="3" height="12" rx="1" stroke-width="1.7" />
                    <rect x="13" y="6" width="3" height="12" rx="1" stroke-width="1.7" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Pausiert</div>
                <div class="pt-summary-value">{{ $stats['paused'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Aufgaben mit Status „pause“</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="5" width="16" height="4" rx="1" stroke-width="1.7" />
                    <path d="M6 9v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9" stroke-width="1.7" />
                    <path d="M10 13h4" stroke-width="1.7" stroke-linecap="round" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Archiv</div>
                <div class="pt-summary-value">{{ $stats['archived'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Aufgaben, die im Archiv liegen</div>
    </div>
    <div class="pt-summary-card">
        <div class="pt-summary-top">
            <div class="pt-summary-icon"><svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="8" stroke-width="1.7" />
                    <path d="M9.5 9.5L14.5 14.5M14.5 9.5L9.5 14.5" stroke-width="1.7" stroke-linecap="round" />
                </svg></div>
            <div>
                <div class="pt-summary-label">Abgelehnt</div>
                <div class="pt-summary-value">{{ $stats['rejected'] }}</div>
            </div>
        </div>
        <div class="pt-summary-sub">Tasks, die du abgelehnt hast</div>
    </div>
</div>