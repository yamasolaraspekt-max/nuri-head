<style>
    :root {
        --tk-green: #93c21c;
        --tk-green-soft: #cfe09b;
        --tk-blue: #74b2d4;
        --tk-blue-soft: #c0d8ea;
        --tk-orange: #f8ac00;
        --tk-pink: #e50656;

        --tk-bg: #ffffff;
        --tk-card: #ffffff;
        --tk-text: #374151;
        --tk-muted: #6b7280;
        --tk-border: #c0d8ea;

        --tk-radius: 16px;
        --tk-radius-lg: 22px;
        --tk-transition: all .18s ease;
    }

    .tk-wrap {
        color: var(--tk-text);
        background: var(--tk-bg);
        padding: 10px;
        max-width: 100%;
        overflow-x: hidden;
    }

    .tk-wrap *,
    .tk-wrap *::before,
    .tk-wrap *::after {
        box-shadow: none !important;
        box-sizing: border-box;
    }

    .tk-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .tk-title {
        font-size: 24px;
        font-weight: 900;
        color: var(--tk-blue);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        text-transform: uppercase;
    }

    .tk-sub {
        font-size: 14px;
        color: var(--tk-text);
        margin-top: 4px;
        line-height: 1.45;
    }

    .tk-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        font-size: 13px;
        color: var(--tk-muted);
    }

    .tk-breadcrumb a {
        color: var(--tk-muted);
        text-decoration: none;
        font-weight: 800;
    }

    .tk-breadcrumb a:hover {
        color: var(--tk-blue);
        text-decoration: none;
    }

    .tk-breadcrumb .current {
        color: var(--tk-blue);
        font-weight: 900;
    }

    .tk-btn {
        background: var(--tk-blue);
        color: #ffffff;
        border: 0;
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 900;
        cursor: pointer;
        transition: var(--tk-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        min-height: 42px;
        white-space: nowrap;
    }

    .tk-btn:hover {
        background: var(--tk-green);
        color: #ffffff;
        text-decoration: none;
    }

    .tk-btn-soft {
        background: #ffffff;
        color: var(--tk-text);
        border: 1px solid var(--tk-border);
        padding: 10px 14px;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        max-width: 100%;
        overflow-wrap: anywhere;
    }

    .tk-analytics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .tk-stat {
        background: var(--tk-card);
        border: 1px solid var(--tk-border);
        border-radius: var(--tk-radius-lg);
        padding: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        min-height: 92px;
        min-width: 0;
    }

    .tk-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #ffffff;
    }

    .tk-stat-icon.total {
        background: var(--tk-blue);
    }

    .tk-stat-icon.open {
        background: var(--tk-orange);
    }

    .tk-stat-icon.process {
        background: var(--tk-green);
    }

    .tk-stat-icon.done {
        background: var(--tk-pink);
    }

    .tk-stat-meta {
        min-width: 0;
    }

    .tk-stat-label {
        font-size: 9px;
        font-weight: 900;
        color: var(--tk-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .tk-stat-value {
        font-size: 24px;
        font-weight: 900;
        color: var(--tk-blue);
        line-height: 1.1;
        margin-top: 4px;
    }

    .tk-stat-sub {
        font-size: 9px;
        color: var(--tk-text);
        margin-top: 4px;
        line-height: 1.35;
    }

    .tk-contextbar {
        background: #ffffff;
        border: 1px solid var(--tk-border);
        border-radius: var(--tk-radius-lg);
        padding: 14px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: flex-end;
        margin-bottom: 16px;
    }

    .tk-filter-card {
        background: #ffffff;
        border: 1px solid var(--tk-border);
        border-radius: var(--tk-radius-lg);
        padding: 14px 16px;
        margin-bottom: 16px;
    }

    .tk-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .tk-label {
        font-size: 12px;
        font-weight: 900;
        color: var(--tk-blue);
        margin-bottom: 6px;
        display: block;
    }

    .tk-input {
        width: 100%;
        border: 1px solid var(--tk-border) !important;
        border-radius: 999px !important;
        padding: 10px 12px !important;
        outline: none;
        background: #ffffff;
        min-height: 42px;
        color: var(--tk-text);
        max-width: 100%;
    }

    .tk-input:focus {
        border-color: var(--tk-orange) !important;
        outline: 3px solid rgba(248, 172, 0, .25);
        outline-offset: 1px;
    }

    .tk-card {
        background: #ffffff; 
        overflow: hidden;
        max-width: 100%;
    }

    .tk-card-head {
        padding: 2px; 
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .tk-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: var(--tk-blue);
    }

    .tk-card-sub {
        font-size: 12px;
        color: var(--tk-text);
        margin-top: 4px;
    }

    .tk-list {
        display: flex;
        flex-direction: column;
        gap: 12px; 
        max-width: 100%;
    }

    .tk-item {
        background: #ffffff;
        border: 1px solid var(--tk-border);
        border-radius: var(--tk-radius-lg);
        transition: var(--tk-transition);
        overflow: hidden;
        max-width: 100%;
        cursor: pointer;
    }

    .tk-item:hover {
        border-color: var(--tk-green);
        background: #ffffff;
        transform: translateY(-1px);
    }

    .tk-item-row {
        padding: 16px;
        display: grid;
        gap: 7px;
        align-items: start;
        grid-template-columns:
            minmax(86px, 100px) minmax(220px, 1.5fr) minmax(190px, 1fr) minmax(200px, .95fr) minmax(92px, .45fr);
        max-width: 100%;
    }

    .tk-cell {
        min-width: 0;
        max-width: 100%;
    }

    .tk-cell-title {
        font-size: 9px;
        font-weight: 900;
        color: var(--tk-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: none;
    }

    .tk-ticket-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: var(--tk-blue-soft);
        border: 1px solid var(--tk-border);
        min-height: 82px;
        padding: 8px;
        text-align: center;
    }

    .tk-ticket-no {
        font-size: 12px;
        line-height: 1.25;
        font-weight: 900;
        color: var(--tk-blue);
        overflow-wrap: anywhere;
    }

    .tk-ticket-label {
        margin-top: 5px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .06em;
        color: var(--tk-text);
        text-transform: uppercase;
    }

    .tk-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .tk-ttl {
        font-weight: 900;
        font-size: 9px;
        margin-bottom: 6px;
        color: var(--tk-blue);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .tk-ttl span:first-child {
        overflow-wrap: anywhere;
    }

    .tk-subt {
        font-size: 13px;
        color: var(--tk-text);
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .tk-note {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 14px;
        background: var(--tk-blue-soft);
        color: var(--tk-text);
        font-size: 13px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .tk-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        color: var(--tk-text);
    }

    .tk-badge.status-open {
        background: var(--tk-orange);
        color: #ffffff;
    }

    .tk-badge.status-process {
        background: var(--tk-green);
        color: #ffffff;
    }

    .tk-badge.status-end {
        background: var(--tk-blue);
        color: #ffffff;
    }

    .tk-badge.priority-high {
        background: var(--tk-pink);
        color: #ffffff;
    }

    .tk-badge.priority-normal {
        background: var(--tk-blue-soft);
        color: var(--tk-text);
    }

    .tk-badge.priority-low {
        background: var(--tk-green-soft);
        color: var(--tk-text);
    }

    .tk-meta-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .tk-meta-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--tk-text);
        background: #ffffff;
        border: 1px solid var(--tk-border);
        border-radius: 999px;
        padding: 8px 10px;
        min-width: 0;
        max-width: 100%;
    }

    .tk-meta-pill svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }

    .tk-meta-pill span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .tk-task-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
        max-width: 100%;
    }

    .tk-task {
        background: #ffffff;
        border: 1px solid var(--tk-border);
        border-radius: 14px;
        padding: 10px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        min-width: 0;
    }

    .tk-task-title {
        font-size: 13px;
        font-weight: 900;
        color: var(--tk-blue);
        overflow-wrap: anywhere;
    }

    .tk-task-sub {
        margin-top: 3px;
        font-size: 12px;
        color: var(--tk-text);
        overflow-wrap: anywhere;
    }

    .tk-avatar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .tk-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        background: var(--tk-blue-soft);
    }

    .tk-assign-form {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        align-items: center;
        max-width: 100%;
    }

    .tk-assign-form .tk-input {
        min-width: 0;
    }

    .tk-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tk-btn-ic {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: 1px solid var(--tk-border);
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--tk-blue);
        cursor: pointer;
        transition: var(--tk-transition);
        text-decoration: none;
        flex: 0 0 auto;
    }

    .tk-btn-ic:hover {
        background: var(--tk-blue);
        color: #ffffff;
        border-color: var(--tk-blue);
        text-decoration: none;
    }

    .tk-profile-btn {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--tk-blue);
        background: var(--tk-blue);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: var(--tk-transition);
        text-decoration: none;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .tk-profile-btn:hover {
        background: var(--tk-green);
        border-color: var(--tk-green);
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .tk-profile-btn svg {
        width: 15px;
        height: 15px;
    }

    @media(max-width: 768px) {
        .tk-profile-btn {
            width: 100%;
        }
    }

    .tk-btn-ic.primary {
        color: #ffffff;
        border-color: var(--tk-blue);
        background: var(--tk-blue);
    }

    .tk-empty {
        text-align: center;
        padding: 60px;
        color: var(--tk-muted);
        background: #ffffff;
        border: 1px dashed var(--tk-border);
        border-radius: 16px;
        margin: 16px;
    }

    @media(max-width: 1400px) {
        .tk-item-row {
            grid-template-columns:
                minmax(86px, 100px) minmax(220px, 1.3fr) minmax(180px, 1fr);
        }

        .tk-cell:nth-child(4),
        .tk-cell:nth-child(5) {
            grid-column: span 1;
        }
    }

    @media(max-width: 1200px) {
        .tk-analytics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .tk-item-row {
            grid-template-columns:
                minmax(86px, 100px) minmax(220px, 1fr);
        }

        .tk-cell:nth-child(3),
        .tk-cell:nth-child(4),
        .tk-cell:nth-child(5) {
            grid-column: 1 / -1;
        }

        .tk-cell-title {
            display: block;
        }

        .tk-actions {
            justify-content: flex-start;
        }
    }

    @media(max-width: 992px) {
        .tk-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 768px) {
        .tk-wrap {
            padding: 8px;
        }

        .tk-title {
            font-size: 22px;
            align-items: flex-start;
        }

        .tk-titlebar,
        .tk-contextbar,
        .tk-card-head {
            align-items: stretch;
        }

        .tk-contextbar {
            justify-content: stretch;
        }

        .tk-btn,
        .tk-btn-soft {
            width: 100%;
            justify-content: center;
        }

        .tk-list {
            padding: 12px;
        }

        .tk-item-row {
            grid-template-columns: 1fr;
            padding: 14px;
        }

        .tk-ticket-badge {
            min-height: 72px;
            align-items: flex-start;
            text-align: left;
            padding: 12px;
        }

        .tk-actions {
            justify-content: stretch;
        }

        .tk-actions .tk-btn-ic {
            width: 100%;
            border-radius: 999px;
        }

        .tk-task {
            flex-direction: column;
            align-items: stretch;
        }

        .tk-task .tk-badge {
            align-self: flex-start;
        }

        .tk-empty {
            padding: 40px 16px;
        }
    }

    @media(max-width: 700px) {
        .tk-analytics {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 576px) {
        .tk-filter-grid {
            grid-template-columns: 1fr;
        }

        .tk-assign-form {
            flex-direction: column;
            align-items: stretch;
        }

        .tk-assign-form .tk-btn {
            width: 100%;
        }

        .tk-meta-pill {
            border-radius: 16px;
            align-items: flex-start;
        }

        .tk-meta-pill span {
            white-space: normal;
            overflow-wrap: anywhere;
        }
    }
</style>

@php
$tickets = collect($tickets ?? []);
$employees = collect($employees ?? []);

$totalTickets = $tickets->count();
$openTickets = $tickets->where('status', 'offen')->count();
$processTickets = $tickets->where('status', 'process')->count();
$doneTickets = $tickets->where('status', 'end')->count();

$statusLabel = function ($status) {
    return [
        'offen' => 'Neu',
        'process' => 'In Bearbeitung',
        'end' => 'Abgeschlossen',
    ][$status] ?? ucfirst((string) $status);
};

$statusClass = function ($status) {
    return [
        'offen' => 'status-open',
        'process' => 'status-process',
        'end' => 'status-end',
    ][$status] ?? 'status-open';
};

$priorityClass = function ($priority) {
    return match (strtolower((string) $priority)) {
        'high', 'hoch' => 'priority-high',
        'low', 'niedrig' => 'priority-low',
        default => 'priority-normal',
    };
};

$problemUrl = function ($ticket) {
    return url('problem/profile/' . ($ticket->problem_id ?? $ticket->id));
};

$employeeImage = function ($emp) {
    if (!empty($emp->image)) {
        return asset('images/employee/' . $emp->image);
    }

    return asset('images/gender/male.png');
};
@endphp

<div class="tk-wrap contentTicket">
    <div id="filterContext" data-customer="{{ $customer_id }}" data-alternative="{{ $alternative_id }}"
        data-product="{{ $product_id }}"></div>

    <div class="tk-titlebar">
        <div>
            <h2 class="tk-title">
                <i data-feather="life-buoy"></i>
                <span>Tickets & Support</span>
            </h2>

            <div class="tk-sub">
                Alle Tickets, Zuständigkeiten und Aufgaben für diesen Kunden- und Produktkontext.
            </div> 
        </div>
    </div>

    <div class="tk-analytics">
        <div class="tk-stat">
            <div class="tk-stat-icon total">
                <i data-feather="inbox"></i>
            </div>

            <div class="tk-stat-meta">
                <div class="tk-stat-label">Gesamt</div>
                <div class="tk-stat-value">{{ $totalTickets }}</div>
                <div class="tk-stat-sub">Tickets insgesamt</div>
            </div>
        </div>

        <div class="tk-stat">
            <div class="tk-stat-icon open">
                <i data-feather="alert-circle"></i>
            </div>

            <div class="tk-stat-meta">
                <div class="tk-stat-label">Neu</div>
                <div class="tk-stat-value">{{ $openTickets }}</div>
                <div class="tk-stat-sub">Noch nicht bearbeitet</div>
            </div>
        </div>

        <div class="tk-stat">
            <div class="tk-stat-icon process">
                <i data-feather="loader"></i>
            </div>

            <div class="tk-stat-meta">
                <div class="tk-stat-label">In Bearbeitung</div>
                <div class="tk-stat-value">{{ $processTickets }}</div>
                <div class="tk-stat-sub">Aktive Supportfälle</div>
            </div>
        </div>

        <div class="tk-stat">
            <div class="tk-stat-icon done">
                <i data-feather="check-circle"></i>
            </div>

            <div class="tk-stat-meta">
                <div class="tk-stat-label">Abgeschlossen</div>
                <div class="tk-stat-value">{{ $doneTickets }}</div>
                <div class="tk-stat-sub">Erledigte Tickets</div>
            </div>
        </div>
    </div>

 
    <div class="tk-filter-card">
        <div class="tk-filter-grid">
            <div>
                <label class="tk-label" for="filterDate">Datum</label>
                <input type="date" id="filterDate" class="tk-input form-control" placeholder="Datum">
            </div>

            <div>
                <label class="tk-label" for="filterStatus">Status</label>
                <select id="filterStatus" class="tk-input form-control">
                    <option value="">Alle Stati</option>
                    <option value="offen">Neu</option>
                    <option value="process">In Bearbeitung</option>
                    <option value="end">Abgeschlossen</option>
                </select>
            </div>

            <div>
                <label class="tk-label" for="filterEmployee">Mitarbeiter</label>
                <select id="filterEmployee" class="tk-input form-control">
                    <option value="">Alle Mitarbeiter</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->name }} {{ $emp->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="tk-label">&nbsp;</label>
                <button onclick="filterTickets()" class="tk-btn w-100" type="button">
                    <i data-feather="filter"></i>
                    Filtern
                </button>
            </div>
        </div>
    </div>

    <div class="tk-card">
        <div class="tk-card-head">
            <div>
                <h3 class="tk-card-title">Ticketliste</h3>
                <div class="tk-card-sub">{{ $tickets->count() }} Tickets gefunden</div>
            </div>
        </div>

        @if($tickets->isEmpty())
            <div class="tk-empty">
                <i data-feather="alert-triangle" style="width:32px;height:32px;margin-bottom:10px;"></i>
                <div>Keine Tickets für diesen Kunden/Alternative/Produkt gefunden.</div>
            </div>
        @else
            <div class="tk-list">
                @foreach ($tickets as $ticket)
                                @php
        $createdDate = $ticket->created_at ? $ticket->created_at->format('d') : '--';
        $createdMonth = $ticket->created_at ? $ticket->created_at->translatedFormat('M') : '---';

        $ticketStatus = $ticket->status ?? 'offen';
        $ticketStatusText = $statusLabel($ticketStatus);
        $ticketStatusClass = $statusClass($ticketStatus);

        $ticketProfileUrl = $problemUrl($ticket);
                                @endphp

                                <div class="tk-item" data-ticket-profile-url="{{ $ticketProfileUrl }}">
                                    <div class="tk-item-row">
                                        <div class="tk-cell">
                                            <div class="tk-cell-title">Ticket</div>

                                            <div class="tk-ticket-badge">
                                                <div class="tk-ticket-no">#{{ $ticket->ticket_no }}</div>
                                                <div class="tk-ticket-label">{{ $createdDate }} {{ $createdMonth }}</div>
                                            </div>
                                        </div>

                                        <div class="tk-cell">
                                            <div class="tk-cell-title">Übersicht</div>

                                            <div class="tk-main">
                                                <div class="tk-ttl">
                                                    <span>Ticket Nr. #{{ $ticket->ticket_no }}</span>
                                                    <span class="tk-badge {{ $ticketStatusClass }}">
                                                        {{ $ticketStatusText }}
                                                    </span>
                                                </div>

                                                <div class="tk-subt">
                                                    <strong>Kunde:</strong>
                                                    {{ $ticket->customer->name ?? '' }} {{ $ticket->customer->lastname ?? '' }}
                                                    <br>

                                                    <strong>Produkt:</strong>
                                                    {{ $ticket->product->article_group ?? '-' }}
                                                    <br>

                                                    <strong>Fehlercode:</strong>
                                                    {{ $ticket->error_code ?? 'Nicht angegeben' }}
                                                </div>

                                                @if ($ticket->ticket_tasks->count())
                                                    <div class="tk-task-list">
                                                        @foreach ($ticket->ticket_tasks as $task)
                                                            <div class="tk-task">
                                                                <div>
                                                                    <div class="tk-task-title">{{ $task->title }}</div>

                                                                    <div class="tk-task-sub">
                                                                        Status: {{ $task->status ?: '–' }}
                                                                        @if($task->due_date)
                                                                            | Bis: {{ $task->due_date }}
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <span class="tk-badge {{ $priorityClass($task->priority) }}">
                                                                    {{ $task->priority ?: 'Normal' }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="tk-cell">
                                            <div class="tk-cell-title">Details</div>

                                            <div class="tk-meta-stack">
                                                <div class="tk-meta-pill">
                                                    <i data-feather="calendar"></i>
                                                    <span>
                                                        Erstellt:
                                                        {{ $ticket->created_at ? $ticket->created_at->format('d.m.Y') : '–' }}
                                                    </span>
                                                </div>

                                                <div class="tk-meta-pill">
                                                    <i data-feather="tag"></i>
                                                    <span>{{ $ticket->error_code ?? 'Kein Fehlercode' }}</span>
                                                </div>

                                                <div class="tk-meta-pill">
                                                    <i data-feather="package"></i>
                                                    <span>{{ $ticket->product->article_group ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tk-cell">
                                            <div class="tk-cell-title">Mitarbeiter</div>

                                            @if ($ticket->employees->count())
                                                <div class="tk-avatar-row">
                                                    @foreach ($ticket->employees as $emp)
                                                        <img src="{{ $employeeImage($emp) }}" class="tk-avatar"
                                                            title="{{ $emp->name }} {{ $emp->lastname }}"
                                                            alt="{{ $emp->name }} {{ $emp->lastname }}"
                                                            onerror="this.src='{{ asset('images/gender/male.png') }}'">
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="tk-meta-pill">
                                                    <i data-feather="user-x"></i>
                                                    <span>Nicht zugewiesen</span>
                                                </div>
                                            @endif

                                            <form method="POST" action="{{ route('ticket.assign', $ticket->id) }}" class="tk-assign-form"
                                                onclick="event.stopPropagation();" onsubmit="event.stopPropagation();">
                                                @csrf

                                                <select name="responsible" class="tk-input form-select form-select-sm"
                                                    onclick="event.stopPropagation();">
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}" @selected($ticket->responsible == $emp->id)>
                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="tk-btn" style="padding:8px 12px;min-height:38px;"
                                                    onclick="event.stopPropagation();">
                                                    <i data-feather="user-check"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="tk-cell">
                                            <div class="tk-cell-title">Aktionen</div>

                                            <div class="tk-actions">
                                                <a href="{{ $ticketProfileUrl }}" class="tk-profile-btn" title="Ticketprofil öffnen"
                                                    onclick="event.stopPropagation();">
                                                    <i data-feather="external-link"></i>
                                                    <span>Profil öffnen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        "use strict";

        function replaceIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function bindTicketCardClicks() {
            document.querySelectorAll('[data-ticket-profile-url]').forEach(function (card) {
                if (card.dataset.bound === '1') return;

                card.dataset.bound = '1';

                card.addEventListener('click', function (event) {
                    const ignored = event.target.closest('a, button, input, select, textarea, label, form');

                    if (ignored) return;

                    const url = card.dataset.ticketProfileUrl;

                    if (url) {
                        window.location.href = url;
                    }
                });
            });
        }

        replaceIcons();
        bindTicketCardClicks();
    })();
</script>