@php
    $ticketCount = $total ?? (method_exists($tickets, 'total') ? $tickets->total() : count($tickets));
@endphp

<style>
    .tk-wrap {
        font-family: Inter, system-ui, -apple-system, sans-serif;
        color: #1f2937;
        max-width: 1843px;
        margin: 3px auto;
        padding: 2px;
    }

    .tk-header { margin-bottom: 18px; margin-top: 20px; }

    .tk-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .tk-title {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.025em;
        color: #111827;
    }

    .tk-sub {
        font-size: 14px;
        color: #6b7280;
        margin-top: 4px;
    }

    .tk-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        font-size: 13px;
        color: #6b7280;
    }

    .tk-breadcrumb a {
        color: #6b7280;
        text-decoration: none;
        font-weight: 700;
    }

    .tk-breadcrumb a:hover { color: #111827; }
    .tk-breadcrumb span.current { color: #111827; font-weight: 800; }

    .tk-analytics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0,1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    @media(max-width:1200px){ .tk-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
    @media(max-width:700px){ .tk-analytics{grid-template-columns:1fr;} }

    .tk-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 92px;
    }

    .tk-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .tk-stat-icon.total { background:#eff6ff; color:#74b2d4; }
    .tk-stat-icon.open { background:#fff7ed; color:#f59e0b; }
    .tk-stat-icon.tasks { background:#ecfdf5; color:#10b981; }
    .tk-stat-icon.type { background:#f3f4f6; color:#6b7280; }

    .tk-stat-meta { min-width: 0; }
    .tk-stat-label {
        font-size: 11px;
        font-weight: 800;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .tk-stat-value {
        font-size: 24px;
        font-weight: 900;
        color: #111827;
        line-height: 1.1;
        margin-top: 4px;
    }

    .tk-stat-sub {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .tk-toolbar {
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        display:flex;
        flex-wrap:wrap;
        gap:14px;
        align-items:flex-end;
        justify-content:space-between;
        margin-bottom:16px;
        box-shadow:0 1px 2px 0 rgb(0 0 0 / .05);
    }

    .tk-toolbar-left {
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
        flex:1;
    }

    .tk-filter-block {
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:170px;
        width:100%;
    }

    .tk-filter-label {
        font-size:11px;
        font-weight:800;
        color:#6b7280;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .tk-input {
        background:#f9fafb;
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:10px 12px;
        font-size:14px;
        width:100%;
    }

    .tk-list-head {
        padding:16px 16px 10px 16px;
        color:#6b7280;
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .tk-list {
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:0 0 16px 0;
    }

    .tk-item {
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        transition:all .2s ease-in-out;
        overflow:hidden;
        margin:0;
    }

    .tk-item:hover {
        border-color:#93c21c;
        box-shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    }

    .tk-item-row { padding:16px; }
    .tk-cell { min-width:0; }

    .tk-cell-title {
        font-size:11px;
        font-weight:800;
        color:#6b7280;
        text-transform:uppercase;
        margin-bottom:4px;
        display:none;
    }

    .tk-main { display:flex; flex-direction:column; min-width:0; }
    .tk-ttl {
        font-weight:800;
        font-size:15px;
        margin-bottom:4px;
        color:#111827;
    }

    .tk-subt {
        font-size:13px;
        color:#6b7280;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .tk-subt-wrap {
        font-size:13px;
        color:#6b7280;
        line-height:1.45;
        white-space:normal;
    }

    .tk-pill {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
    }

    .tk-pill.open { background:#fffbeb; color:#b45309; }
    .tk-pill.progress { background:#eff6ff; color:#1d4ed8; }
    .tk-pill.done { background:#ecfdf5; color:#047857; }
    .tk-pill.default { background:#f3f4f6; color:#374151; }

    .tk-actions {
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
        flex-wrap:wrap;
    }

    .tk-btn,
    .tk-btn-ic {
        border:1px solid #e5e7eb;
        background:#fff;
        color:#374151;
        cursor:pointer;
        transition:all .2s ease-in-out;
        text-decoration:none !important;
    }

    .tk-btn {
        display:inline-flex;
        align-items:center;
        gap:8px;
        height:38px;
        padding:0 14px;
        border-radius:10px;
        font-size:13px;
        font-weight:700;
    }

    .tk-btn:hover,
    .tk-btn-ic:hover {
        transform:translateY(-1px);
        box-shadow:0 4px 12px rgba(0,0,0,.08);
    }

    .tk-btn.primary {
        color:#0f5132;
        border-color:#c7f2df;
        background:#ecfdf5;
    }

    .tk-btn.secondary {
        color:#1d4ed8;
        border-color:#dbeafe;
        background:#eff6ff;
    }

    .tk-avatar-row {
        display:flex;
        align-items:center;
        gap:8px;
        min-width:0;
    }

    .tk-avatar {
        width:30px;
        height:30px;
        border-radius:999px;
        object-fit:cover;
        border:1px solid #e5e7eb;
        flex:0 0 auto;
    }

    .tk-badge-light {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:4px 8px;
        border-radius:999px;
        background:#eff6ff;
        color:#1d4ed8;
        font-size:12px;
        font-weight:800;
    }

    .tk-empty {
        text-align:center;
        padding:60px;
        color:#6b7280;
        background:#fff;
        border:1px dashed #e5e7eb;
        border-radius:16px;
        margin:16px 0;
    }

    .tk-pagination {
        margin-top:18px;
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:14px 16px;
        box-shadow:0 1px 2px 0 rgb(0 0 0 / .05);
    }

    .ticket-grid {
        display:grid;
        grid-template-columns:
            minmax(160px,.9fr)
            minmax(220px,1.2fr)
            minmax(120px,.7fr)
            minmax(190px,1fr)
            minmax(180px,.9fr)
            minmax(240px,1.2fr)
            minmax(190px,.9fr)
            minmax(180px,.9fr);
        gap:14px;
        align-items:center;
    }

    @media (max-width: 1380px) {
        .ticket-grid { grid-template-columns:1fr !important; }
        .tk-list-head { display:none; }
        .tk-cell-title { display:block; }
    }
</style>

<div id="ticketInner" data-ticket-total="{{ $ticketCount }}">
    <div class="tk-wrap"> 
        <div class="tk-analytics">
            <div class="tk-stat">
                <div class="tk-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                </div>
                <div class="tk-stat-meta">
                    <div class="tk-stat-label">Gesamt</div>
                    <div class="tk-stat-value">{{ $ticketCount }}</div>
                    <div class="tk-stat-sub">Tickets insgesamt</div>
                </div>
            </div>

            <div class="tk-stat">
                <div class="tk-stat-icon open">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 3"/>
                    </svg>
                </div>
                <div class="tk-stat-meta">
                    <div class="tk-stat-label">Status</div>
                    <div class="tk-stat-value">{{ $ticketCount }}</div>
                    <div class="tk-stat-sub">Aktive Problemfälle</div>
                </div>
            </div>

            <div class="tk-stat">
                <div class="tk-stat-icon tasks">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div class="tk-stat-meta">
                    <div class="tk-stat-label">Aufgaben</div>
                    <div class="tk-stat-value">{{ $ticketCount }}</div>
                    <div class="tk-stat-sub">Mit Fortschritt & Teams</div>
                </div>
            </div>

            <div class="tk-stat">
                <div class="tk-stat-icon type">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h6v6"/>
                        <path d="M10 14L21 3"/>
                        <path d="M21 14v7H3V3h7"/>
                    </svg>
                </div>
                <div class="tk-stat-meta">
                    <div class="tk-stat-label">Aktion</div>
                    <div class="tk-stat-value">Profil</div>
                    <div class="tk-stat-sub">Direkt zum Ticketprofil</div>
                </div>
            </div>
        </div>

        <div class="tk-toolbar">
            <div class="tk-toolbar-left">
                <div class="tk-filter-block search">
                    <label class="tk-filter-label">Übersicht</label>
                    <div class="tk-input" style="display:flex;align-items:center;background:#fff;padding-left:12px;">
                        Tickets mit Kunde, Verantwortlichem, Aufgabenfortschritt und direktem Profilzugang
                    </div>
                </div>
            </div>
        </div>

        <div class="tk-list-head ticket-grid">
            <div>Ticket</div>
            <div>Kunde</div>
            <div>Produkt</div>
            <div>Verantwortlicher</div>
            <div>Problem-Team</div>
            <div>Aufgaben</div>
            <div>Stand</div>
            <div style="text-align:right;">Aktion</div>
        </div>

        <div class="tk-list">
            @forelse($tickets as $t)
                @php
                    $statusRaw = strtolower((string) ($t->status ?? 'open'));
                    $statusClass = match($statusRaw) {
                        'open' => 'open',
                        'in progress', 'in_progress', 'progress' => 'progress',
                        'done', 'completed' => 'done',
                        default => 'default',
                    };
                @endphp

                <div class="tk-item"
                     data-ticket-id="{{ $t->ticket_id ?? $t->id }}"
                     data-customer-id="{{ $t->customer_id }}"
                     data-alternative-id="{{ $t->alternative_id }}"
                     data-product-id="{{ $t->product_id }}">
                    <div class="tk-item-row ticket-grid">
                        <div class="tk-cell">
                            <div class="tk-cell-title">Ticket</div>
                            <div class="tk-main">
                                <div class="tk-ttl">#{{ $t->ticket_no }}</div>
                                <div class="tk-subt">
                                    <span class="tk-pill {{ $statusClass }}">
                                        {{ $t->status ?? 'open' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Kunde</div>
                            <div class="tk-main">
                                <div class="tk-ttl">
                                    {{ trim(($t->customer_lastname ?? '') . ' ' . ($t->customer_name ?? '')) ?: ($t->firma ?? '—') }}
                                </div>
                                <div class="tk-subt">
                                    {{ trim(($t->postcode ?? '') . ' ' . ($t->city ?? '')) ?: 'Kein Ort' }}
                                </div>
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Produkt</div>
                            <div class="tk-main">
                                <div class="tk-ttl" style="font-size:14px;">{{ $t->product_initial ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Verantwortlicher</div>
                            @if($t->responsible_id)
                                <div class="tk-avatar-row">
                                    @if($t->responsible_image)
                                        <img src="{{ asset('images/employee/'.$t->responsible_image) }}"
                                             alt=""
                                             class="tk-avatar">
                                    @endif
                                    <div class="tk-main">
                                        <div class="tk-ttl" style="font-size:14px;">
                                            {{ $t->responsible_lastname }} {{ $t->responsible_name }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="tk-subt">–</div>
                            @endif
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Mitarbeiter (Problem)</div>
                            <div class="tk-main">
                                @if($t->team_count > 0)
                                    <div class="tk-subt">
                                        <span class="tk-badge-light">{{ $t->team_count }} MA</span>
                                    </div>
                                    <div class="tk-subt-wrap" style="margin-top:6px;">
                                        {{ $t->team_names }}
                                    </div>
                                @else
                                    <div class="tk-subt">keine</div>
                                @endif
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Aufgaben</div>
                            <div class="tk-main">
                                @if($t->total_tasks > 0)
                                    <div class="tk-ttl" style="font-size:14px;">
                                        {{ $t->done_tasks }}/{{ $t->total_tasks }} erledigt
                                    </div>
                                    <div class="tk-subt-wrap">
                                        offen: {{ $t->open_tasks }}<br>
                                        in Arbeit: {{ $t->progress_tasks }}<br>
                                        Team-Slots: {{ $t->team_slots }}
                                    </div>
                                @else
                                    <div class="tk-subt">keine Aufgaben</div>
                                @endif
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Stand</div>
                            <div class="tk-main">
                                <div class="tk-subt-wrap">
                                    erstellt: {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d.m.Y') : '-' }}<br>
                                    aktualisiert: {{ $t->updated_at ? \Carbon\Carbon::parse($t->updated_at)->format('d.m.Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="tk-cell">
                            <div class="tk-cell-title">Aktion</div>
                            <div class="tk-actions">
                                <a href="{{ url('problem/profile/' . ($t->ticket_id ?? $t->id)) }}"
                                   class="tk-btn primary"
                                   title="Ticketprofil öffnen">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    Profil öffnen
                                </a>

                                <a href="{{ url('problem/profile/' . ($t->ticket_id ?? $t->id)) }}"
                                   class="tk-btn secondary"
                                   title="In neuem Tab öffnen"
                                   target="_blank">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 3h6v6"/>
                                        <path d="M10 14L21 3"/>
                                        <path d="M21 14v7H3V3h7"/>
                                    </svg>
                                    Neuer Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="tk-empty">
                    Keine Tickets gefunden.
                </div>
            @endforelse
        </div>

        @if(method_exists($tickets, 'links') && $tickets->hasPages())
            <div class="tk-pagination">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                    <div style="font-size:12px;color:#6b7280;">
                        Zeige <strong>{{ $tickets->firstItem() ?? 0 }}</strong>
                        bis <strong>{{ $tickets->lastItem() ?? 0 }}</strong>
                        von <strong>{{ $tickets->total() }}</strong> Einträgen
                    </div>
                    <div>
                        {{ $tickets->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>