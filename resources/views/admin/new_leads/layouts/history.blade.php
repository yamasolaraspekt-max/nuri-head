<style>
    .hf-wrap {
        --hf-green: #10b981;
        --hf-red: #ef4444;
        --hf-orange: #f59e0b;
        --hf-blue: #74b2d4;
        --hf-soft-blue: #c0d8ea;
        --hf-text: #1f2937;
        --hf-muted: #6b7280;
        --hf-border: #e5e7eb;
        --hf-bg: #ffffff;
        --hf-soft: #f8fafc;

        background: #fff;
        color: var(--hf-text);
        padding: 14px;
    }

    .hf-wrap *,
    .hf-wrap *::before,
    .hf-wrap *::after {
        box-sizing: border-box;
    }

    .hf-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .hf-title {
        margin: 0;
        font-size: 22px;
        font-weight: 900;
        color: var(--hf-blue);
    }

    .hf-subtitle {
        margin-top: 4px;
        font-size: 13px;
        color: var(--hf-muted);
        line-height: 1.5;
    }

    .hf-filter-card,
    .hf-analytics-card {
        border: 1px solid var(--hf-border);
        border-radius: 22px;
        background: #fff;
        padding: 14px;
        margin-bottom: 14px;
    }

    .hf-filter-title {
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 12px;
        color: var(--hf-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hf-filter-title svg {
        width: 16px;
        height: 16px;
    }

    .hf-filter-grid {
        display: grid;
        grid-template-columns: 1.4fr repeat(4, minmax(130px, .7fr));
        gap: 10px;
        align-items: end;
    }

    .hf-filter-grid.second {
        grid-template-columns: repeat(2, minmax(120px, 1fr)) auto auto;
        margin-top: 10px;
    }

    .hf-field label {
        display: block;
        margin-bottom: 5px;
        font-size: 11px;
        font-weight: 900;
        color: var(--hf-muted);
    }

    .hf-input,
    .hf-select {
        width: 100%;
        border: 1px solid var(--hf-border);
        border-radius: 14px;
        padding: 10px 12px;
        font-size: 13px;
        background: #fff;
        color: var(--hf-text);
        outline: none;
    }

    .hf-input:focus,
    .hf-select:focus {
        border-color: var(--hf-blue);
        box-shadow: 0 0 0 3px rgba(116, 178, 212, .16);
    }

    .hf-btn {
        border: 0;
        border-radius: 14px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-decoration: none;
        white-space: nowrap;
        transition: .15s ease;
    }

    .hf-btn svg {
        width: 15px;
        height: 15px;
    }

    .hf-btn:hover {
        transform: translateY(-1px);
    }

    .hf-btn-primary {
        background: var(--hf-blue);
        color: #fff;
    }

    .hf-btn-soft {
        background: #f3f4f6;
        color: var(--hf-text);
    }

    .hf-analytics-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(110px, 1fr));
        gap: 10px;
    }

    .hf-stat {
        border: 1px solid var(--hf-border);
        border-radius: 18px;
        padding: 12px;
        background: linear-gradient(180deg, #fff, #f8fafc);
        min-height: 82px;
    }

    .hf-stat-value {
        font-size: 22px;
        font-weight: 950;
        color: var(--hf-blue);
        line-height: 1;
    }

    .hf-stat-label {
        font-size: 11px;
        font-weight: 900;
        color: var(--hf-muted);
        margin-top: 7px;
        line-height: 1.4;
    }

    .hf-stat.green .hf-stat-value {
        color: var(--hf-green);
    }

    .hf-stat.orange .hf-stat-value {
        color: var(--hf-orange);
    }

    .hf-stat.red .hf-stat-value {
        color: var(--hf-red);
    }

    .hf-model-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 12px;
    }

    .hf-pill {
        display: inline-flex;
        gap: 6px;
        align-items: center;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 900;
        background: #f3f4f6;
        color: var(--hf-text);
    }

    .hf-pill-count {
        color: var(--hf-blue);
        font-weight: 950;
    }

    .hf-timeline {
        position: relative;
    }

    .hf-date-group {
        margin-bottom: 22px;
    }

    .hf-date-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        border: 1px solid var(--hf-border);
        border-radius: 999px;
        padding: 8px 12px;
        margin-bottom: 12px;
        color: var(--hf-text);
        font-size: 13px;
        font-weight: 950;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    }

    .hf-date-label svg {
        width: 15px;
        height: 15px;
    }

    .hf-timeline-list {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }

    .hf-timeline-list::before {
        content: "";
        position: absolute;
        left: 24px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, var(--hf-soft-blue), transparent);
    }

    .hf-item {
        position: relative;
        padding-left: 66px;
        margin-bottom: 14px;
    }

    .hf-icon {
        position: absolute;
        left: 7px;
        top: 12px;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        border: 2px solid var(--hf-border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .hf-icon svg {
        width: 16px;
        height: 16px;
    }

    .hf-card {
        background: #fff;
        border: 1px solid var(--hf-border);
        border-radius: 22px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .045);
        transition: .18s ease;
    }

    .hf-card.is-open {
        border-color: rgba(116, 178, 212, .55);
        box-shadow: 0 12px 32px rgba(15, 23, 42, .075);
    }

    .hf-card-toggle {
        width: 100%;
        border: 0;
        background: #fff;
        padding: 14px;
        cursor: pointer;
        text-align: left;
        color: inherit;
    }

    .hf-card-toggle:hover {
        background: #f8fafc;
    }

    .hf-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .hf-user {
        font-size: 14px;
        font-weight: 950;
        color: var(--hf-text);
    }

    .hf-card-toggle:hover .hf-user {
        color: var(--hf-blue);
    }

    .hf-meta {
        margin-top: 5px;
        font-size: 12px;
        color: var(--hf-muted);
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .hf-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 4px 8px;
        background: #f3f4f6;
        color: var(--hf-text);
        font-size: 11px;
        font-weight: 900;
    }

    .hf-card-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 0 0 auto;
    }

    .hf-time {
        text-align: right;
        font-size: 12px;
        color: var(--hf-muted);
        line-height: 1.6;
        white-space: nowrap;
    }

    .hf-time svg {
        width: 13px;
        height: 13px;
        vertical-align: -2px;
    }

    .hf-collapse-icon {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: #f3f4f6;
        color: var(--hf-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .18s ease;
        flex: 0 0 auto;
    }

    .hf-collapse-icon svg {
        width: 17px;
        height: 17px;
    }

    .hf-card.is-open .hf-collapse-icon {
        background: rgba(116, 178, 212, .16);
        color: var(--hf-blue);
        transform: rotate(180deg);
    }

    .hf-preview {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--hf-border);
        font-size: 12px;
        color: var(--hf-muted);
        line-height: 1.5;
        text-align: left;
    }

    .hf-preview strong {
        color: var(--hf-text);
    }

    .hf-details {
        display: none;
        border-top: 1px solid var(--hf-border);
        padding: 10px 14px 14px;
        background: #fff;
    }

    .hf-card.is-open .hf-details {
        display: block;
    }

    .hf-changes {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .hf-changes tr:not(:last-child) {
        border-bottom: 1px dashed var(--hf-border);
    }

    .hf-changes td {
        padding: 8px 4px;
        vertical-align: top;
    }

    .hf-field-name {
        width: 180px;
        color: var(--hf-muted);
        font-weight: 900;
    }

    .hf-old,
    .hf-new {
        display: inline-flex;
        max-width: 100%;
        padding: 4px 9px;
        border-radius: 999px;
        font-weight: 900;
        word-break: break-word;
    }

    .hf-old {
        background: rgba(239, 68, 68, .09);
        color: #b91c1c;
    }

    .hf-new {
        background: rgba(16, 185, 129, .10);
        color: #047857;
    }

    .hf-value {
        color: var(--hf-text);
        font-weight: 650;
        line-height: 1.5;
        word-break: break-word;
    }

    .hf-empty {
        border: 1px dashed var(--hf-border);
        border-radius: 22px;
        padding: 30px;
        text-align: center;
        color: var(--hf-muted);
        font-weight: 900;
        background: #fff;
    }

    .hf-end {
        padding: 16px;
        margin-top: 10px;
    }

    @media (max-width: 1200px) {

        .hf-filter-grid,
        .hf-filter-grid.second {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .hf-analytics-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .hf-wrap {
            padding: 10px;
        }

        .hf-head,
        .hf-card-top {
            flex-direction: column;
            align-items: stretch;
        }

        .hf-filter-grid,
        .hf-filter-grid.second,
        .hf-analytics-grid {
            grid-template-columns: 1fr;
        }

        .hf-btn {
            width: 100%;
        }

        .hf-item {
            padding-left: 52px;
        }

        .hf-timeline-list::before {
            left: 18px;
        }

        .hf-icon {
            left: 1px;
        }

        .hf-card-right {
            width: 100%;
            justify-content: space-between;
        }

        .hf-time {
            text-align: left;
        }

        .hf-changes,
        .hf-changes tbody,
        .hf-changes tr,
        .hf-changes td {
            display: block;
            width: 100%;
        }

        .hf-field-name {
            width: 100%;
            padding-bottom: 0;
        }
    }
</style>

@php
    $filters = $filters ?? [];
    $analytics = $analytics ?? [
        'total' => 0,
        'total_days' => 0,
        'created' => 0,
        'updated' => 0,
        'deleted' => 0,
        'restored' => 0,
        'active_users' => 0,
        'model_counts' => collect(),
        'first_log_date' => null,
        'last_log_date' => null,
    ];

    $groupedLogs = $groupedLogs ?? collect();
@endphp

<div class="hf-wrap">
    <div class="hf-head">
        <div>
            <h3 class="hf-title">Historie</h3>
            <div class="hf-subtitle">
                Änderungen, Aktivitäten und Statusverlauf für diesen Kontext.
            </div>
        </div>

        <button type="button" class="hf-btn hf-btn-soft" onclick="window.print()">
            <i data-feather="printer"></i>
            Drucken
        </button>
    </div>

    <form class="hf-filter-card" data-history-filter-form>
        <input type="hidden" name="alternative_id"
            value="{{ $filters['alternative_id'] ?? request('alternative_id') }}">
        <input type="hidden" name="product_id" value="{{ $filters['product_id'] ?? request('product_id') }}">

        <div class="hf-filter-title">
            <i data-feather="filter"></i>
            Filter
        </div>

        <div class="hf-filter-grid">
            <div class="hf-field">
                <label>Suche</label>
                <input type="search" class="hf-input" name="search_text" value="{{ $filters['search_text'] ?? '' }}"
                    placeholder="Mitarbeiter, Änderung, Status, Preis...">
            </div>

            <div class="hf-field">
                <label>Aktion</label>
                <select class="hf-select" name="event_type">
                    <option value="">Alle Aktionen</option>
                    <option value="created" @selected(($filters['event_type'] ?? '') === 'created')>Erstellt</option>
                    <option value="updated" @selected(($filters['event_type'] ?? '') === 'updated')>Aktualisiert</option>
                    <option value="deleted" @selected(($filters['event_type'] ?? '') === 'deleted')>Gelöscht</option>
                    <option value="restored" @selected(($filters['event_type'] ?? '') === 'restored')>Wiederhergestellt
                    </option>
                </select>
            </div>

            <div class="hf-field">
                <label>Typ</label>
                <select class="hf-select" name="model_type">
                    <option value="">Alle Typen</option>
                    <option value="customer" @selected(($filters['model_type'] ?? '') === 'customer')>Kunde</option>
                    <option value="object" @selected(($filters['model_type'] ?? '') === 'object')>Objekt</option>
                    <option value="product" @selected(($filters['model_type'] ?? '') === 'product')>Produkt</option>
                    <option value="note" @selected(($filters['model_type'] ?? '') === 'note')>Notiz</option>
                    <option value="comment" @selected(($filters['model_type'] ?? '') === 'comment')>Kommentar</option>
                    <option value="ticket" @selected(($filters['model_type'] ?? '') === 'ticket')>Ticket</option>
                    <option value="invoice" @selected(($filters['model_type'] ?? '') === 'invoice')>Rechnung</option>
                    <option value="appointment" @selected(($filters['model_type'] ?? '') === 'appointment')>Termin
                    </option>
                </select>
            </div>

            <div class="hf-field">
                <label>Von Datum</label>
                <input type="date" class="hf-input" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </div>

            <div class="hf-field">
                <label>Bis Datum</label>
                <input type="date" class="hf-input" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </div>
        </div>

        <div class="hf-filter-grid second">
            <div class="hf-field">
                <label>Von Uhrzeit</label>
                <input type="time" class="hf-input" name="time_from" value="{{ $filters['time_from'] ?? '' }}">
            </div>

            <div class="hf-field">
                <label>Bis Uhrzeit</label>
                <input type="time" class="hf-input" name="time_to" value="{{ $filters['time_to'] ?? '' }}">
            </div>

            <button type="submit" class="hf-btn hf-btn-primary">
                <i data-feather="search"></i>
                Anwenden
            </button>

            <button type="button" class="hf-btn hf-btn-soft" data-history-clear-filters>
                <i data-feather="x-circle"></i>
                Zurücksetzen
            </button>
        </div>
    </form>

    <div class="hf-analytics-card">
        <div class="hf-filter-title">
            <i data-feather="bar-chart-2"></i>
            Analyse
        </div>

        <div class="hf-analytics-grid">
            <div class="hf-stat">
                <div class="hf-stat-value">{{ $analytics['total'] }}</div>
                <div class="hf-stat-label">Gesamt</div>
            </div>

            <div class="hf-stat green">
                <div class="hf-stat-value">{{ $analytics['created'] }}</div>
                <div class="hf-stat-label">Erstellt</div>
            </div>

            <div class="hf-stat orange">
                <div class="hf-stat-value">{{ $analytics['updated'] }}</div>
                <div class="hf-stat-label">Aktualisiert</div>
            </div>

            <div class="hf-stat red">
                <div class="hf-stat-value">{{ $analytics['deleted'] }}</div>
                <div class="hf-stat-label">Gelöscht</div>
            </div>

            <div class="hf-stat">
                <div class="hf-stat-value">{{ $analytics['active_users'] }}</div>
                <div class="hf-stat-label">Aktive Mitarbeiter</div>
            </div>

            <div class="hf-stat">
                <div class="hf-stat-value">{{ $analytics['total_days'] }}</div>
                <div class="hf-stat-label">
                    Tage
                    @if($analytics['first_log_date'] && $analytics['last_log_date'])
                        <br>
                        {{ $analytics['first_log_date'] }} - {{ $analytics['last_log_date'] }}
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($analytics['model_counts']) && $analytics['model_counts']->isNotEmpty())
            <div class="hf-model-pills">
                @foreach($analytics['model_counts'] as $modelLabel => $count)
                    <span class="hf-pill">
                        {{ $modelLabel }}
                        <span class="hf-pill-count">{{ $count }}</span>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    @if($groupedLogs->isEmpty())
        <div class="hf-empty">
            Keine Historie für diese Filter gefunden.
        </div>
    @else
        <div class="hf-timeline">
            @foreach($groupedLogs as $date => $items)
                <div class="hf-date-group">
                    <div class="hf-date-label">
                        <i data-feather="calendar"></i>
                        {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
                        <span class="hf-pill-count">· {{ $items->count() }}</span>
                    </div>

                    <ul class="hf-timeline-list">
                        @foreach($items as $log)
                            @php
                                $icon = $log->icon_data['icon'] ?? 'edit-2';
                                $color = $log->icon_data['color'] ?? '#f59e0b';
                                $bg = $log->icon_data['bg'] ?? 'rgba(245,158,11,.12)';
                                $firstChange = $log->display_changes[0] ?? null;
                            @endphp

                            <li class="hf-item">
                                <div class="hf-icon"
                                    style="border-color: {{ $color }}; color: {{ $color }}; background: {{ $bg }};">
                                    <i data-feather="{{ $icon }}"></i>
                                </div>

                                <div class="hf-card" data-hf-card>
                                    <button type="button" class="hf-card-toggle" data-hf-toggle>
                                        <div class="hf-card-top">
                                            <div>
                                                <div class="hf-user">
                                                    {{ $log->display_user_name }}
                                                </div>

                                                <div class="hf-meta">
                                                    <span class="hf-type-badge">
                                                        {{ $log->event_label }}
                                                    </span>

                                                    <span class="hf-type-badge">
                                                        {{ $log->model_label }}
                                                    </span>

                                                    @if(!empty($log->alternative_id))
                                                        <span>Objekt #{{ $log->alternative_id }}</span>
                                                    @endif

                                                    @if(!empty($log->product_id))
                                                        <span>Produkt #{{ $log->product_id }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="hf-card-right">
                                                <div class="hf-time">
                                                    <div>
                                                        <i data-feather="clock"></i>
                                                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}
                                                    </div>

                                                    <div>
                                                        #{{ $log->id }}
                                                    </div>
                                                </div>

                                                <span class="hf-collapse-icon">
                                                    <i data-feather="chevron-down"></i>
                                                </span>
                                            </div>
                                        </div>

                                        @if($firstChange)
                                            <div class="hf-preview">
                                                <strong>{{ $firstChange['field'] ?? 'Info' }}:</strong>

                                                @if(($firstChange['type'] ?? null) === 'change')
                                                    {{ $firstChange['from'] ?? '-' }}
                                                    →
                                                    {{ $firstChange['to'] ?? '-' }}
                                                @else
                                                    {{ \Illuminate\Support\Str::limit($firstChange['value'] ?? '-', 140) }}
                                                @endif
                                            </div>
                                        @endif
                                    </button>

                                    <div class="hf-details" data-hf-details>
                                        <table class="hf-changes">
                                            <tbody>
                                                @foreach($log->display_changes as $change)
                                                    <tr>
                                                        <td class="hf-field-name">
                                                            {{ $change['field'] ?? 'Info' }}
                                                        </td>

                                                        <td>
                                                            @if(($change['type'] ?? null) === 'change')
                                                                <span class="hf-old">
                                                                    {{ $change['from'] ?? '-' }}
                                                                </span>

                                                                <i data-feather="arrow-right" class="text-muted mx-1"
                                                                    style="width:12px;height:12px;"></i>

                                                                <span class="hf-new">
                                                                    {{ $change['to'] ?? '-' }}
                                                                </span>
                                                            @else
                                                                <div class="hf-value">
                                                                    {{ $change['value'] ?? '-' }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="hf-empty hf-end">
                Ende der Historie
            </div>
        </div>
    @endif
</div>

<script>
    (function () {
        const form = document.querySelector('[data-history-filter-form]');
        const clearBtn = document.querySelector('[data-history-clear-filters]');

        function refreshIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function getMainContent() {
            return document.getElementById('mainContent');
        }

        function getCurrentLeadId() {
            const activeBtn = document.querySelector('[data-customer-id].active, .nav-section-btn.active, .sub-nav-btn.active');
            const anyContext = document.querySelector('[data-customer-id]');

            return activeBtn?.dataset.customerId || anyContext?.dataset.customerId || "{{ request()->route('id') }}";
        }

        function executeScripts(container) {
            container.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');

                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                newScript.textContent = oldScript.textContent;
                oldScript.replaceWith(newScript);
            });
        }

        async function loadHistoryWithFilters(params) {
            const mainContent = getMainContent();

            if (!mainContent) {
                form.submit();
                return;
            }

            const leadId = getCurrentLeadId();

            mainContent.innerHTML = `
                <div style="padding:24px;color:#6b7280;font-weight:900;">
                    Historie wird geladen...
                </div>
            `;

            const url = `{{ url('/new-leads') }}/${leadId}/history-feed?${params.toString()}`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                const html = await response.text();

                mainContent.innerHTML = html;
                executeScripts(mainContent);
                refreshIcons();
            } catch (error) {
                mainContent.innerHTML = `
                    <div style="padding:24px;color:#e50656;font-weight:900;">
                        Historie konnte nicht geladen werden.
                    </div>
                `;
            }
        }

        function collectFormParams() {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            formData.forEach((value, key) => {
                if (value !== null && String(value).trim() !== '') {
                    params.set(key, value);
                }
            });

            return params;
        }

        function bindFilters() {
            if (form && form.dataset.bound !== '1') {
                form.dataset.bound = '1';

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    loadHistoryWithFilters(collectFormParams());
                });
            }

            if (clearBtn && clearBtn.dataset.bound !== '1') {
                clearBtn.dataset.bound = '1';

                clearBtn.addEventListener('click', function () {
                    form.querySelectorAll('input, select').forEach(input => {
                        if (input.type === 'hidden') {
                            return;
                        }

                        input.value = '';
                    });

                    loadHistoryWithFilters(collectFormParams());
                });
            }
        }

        function bindCollapses() {
            document.querySelectorAll('[data-hf-toggle]').forEach(button => {
                if (button.dataset.bound === '1') {
                    return;
                }

                button.dataset.bound = '1';

                button.addEventListener('click', function () {
                    const card = button.closest('[data-hf-card]');

                    if (!card) {
                        return;
                    }

                    card.classList.toggle('is-open');
                    refreshIcons();
                });
            });
        }

        bindFilters();
        bindCollapses();
        refreshIcons();
    })();
</script>