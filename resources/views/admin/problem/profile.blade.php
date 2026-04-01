@extends('admin.layouts.app')

@section('title', 'Ticket')

@section('style')

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" />
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    :root {
        --ticket-bg:          #f3f4f6;
        --ticket-surface:     #ffffff;
        --ticket-border:      #e5e7eb;

        --ticket-blue:        #2563eb;
        --ticket-blue-soft:   #eff6ff;

        --ticket-green:       #16a34a;
        --ticket-green-soft:  #ecfdf3;

        --ticket-amber:       #f59e0b;
        --ticket-amber-soft:  #fffbeb;

        --ticket-text-main:   #111827;
        --ticket-text-muted:  #6b7280;
        --ticket-danger:      #dc2626;
    }

    * { box-sizing: border-box; }
    img, canvas, iframe { max-width: 100%; height: auto; display: block; }
    button, .btn { max-width: 100%; }

    body {
        background-color: var(--ticket-bg);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--ticket-text-main);
    }

    .app-content .content-wrapper {
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        padding-bottom: 1.5rem;
    }

    .content-body {
        padding: 0 0 1.5rem 0;
    }

    /* ========= GLOBAL CARDS / SHELLS ========= */

    .ticket-shell {
        background: var(--ticket-surface);
        border-radius: 18px;
        border: 1px solid var(--ticket-border);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .06);
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .info-card {
        padding: 1.1rem 1.15rem;
        background: var(--ticket-surface);
        border-radius: 14px;
        margin-bottom: 1rem;
        border: 1px solid var(--ticket-border);
    }

    /* ========= HEADER BANNER ========= */

    .ticket-header {
        margin-top: .5rem;
        margin-bottom: 1rem;
    }

    .ticket-header-banner {
        @apply d-flex;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        background: var(--ticket-surface);
        border-radius: 18px;
        border: 1px solid var(--ticket-border);
        padding: 1rem 1.25rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
    }

    .ticket-header-main {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .ticket-header-left {
        min-width: 230px;
        max-width: 40%;
    }

    .ticket-header-left .customer-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: .1rem;
    }

    .ticket-header-left .customer-company {
        font-size: .9rem;
        color: var(--ticket-text-muted);
        margin-bottom: .35rem;
    }

    .ticket-header-left .customer-address {
        font-size: .85rem;
        color: var(--ticket-text-muted);
    }

    .ticket-header-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .4rem;
    }

    .ticket-header-center .contact-label {
        font-size: .78rem;
        color: var(--ticket-text-muted);
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .ticket-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .ticket-header-actions .btn {
        font-size: .82rem;
        padding: .35rem .7rem;
        border-radius: 999px;
    }

    .ticket-header-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: .3rem;
        min-width: 220px;
    }

    .ticket-pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        justify-content: flex-end;
    }

    .ticket-pill {
        padding: .2rem .65rem;
        border-radius: 999px;
        font-size: .78rem;
        border: 1px solid var(--ticket-border);
        background: #f9fafb;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        white-space: nowrap;
    }

    .ticket-pill-primary {
        border-color: var(--ticket-blue);
        background: var(--ticket-blue-soft);
        color: var(--ticket-blue);
        font-weight: 600;
    }

    .ticket-pill-status-open {
        border-color: var(--ticket-amber);
        background: var(--ticket-amber-soft);
        color: #92400e;
    }

    .ticket-pill-status-done {
        border-color: var(--ticket-green);
        background: var(--ticket-green-soft);
        color: #166534;
    }

    .ticket-header-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: .5rem;
        margin-top: .3rem;
        border-top: 1px dashed var(--ticket-border);
        padding-top: .4rem;
    }

    .ticket-header-meta-left,
    .ticket-header-meta-right {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        font-size: .78rem;
        color: var(--ticket-text-muted);
    }

    .ticket-header-meta span i {
        margin-right: .25rem;
    }

    /* ========= TOP NAV / SECTION LINKS ========= */

    .ticket-section-nav {
        margin-top: .75rem;
    }

    .ticket-section-nav .nav {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
    }

    .ticket-section-nav .nav-link {
        padding: .35rem .75rem;
        border-radius: 999px;
        font-size: .82rem;
        border: 1px solid transparent;
        color: #374151;
        background: #f3f4f6;
    }

    .ticket-section-nav .nav-link.active,
    .ticket-section-nav .nav-link:hover {
        background: #111827;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* ========= MAIN LAYOUT (SIDEBAR + CONTENT) ========= */

    .dashboard-wrapper {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin-top: 1rem;
    }

    .ticket-sidebar {
        flex: 0 0 310px;
        max-width: 310px;
    }

    .ticket-main {
        flex: 1 1 auto;
        min-width: 0;
    }

    .sidebar-card-title {
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: .1rem;
    }

    .sidebar-card-sub {
        font-size: .78rem;
        color: var(--ticket-text-muted);
        margin-bottom: .3rem;
    }

    /* Calendar card */

    .calendar-card {
        background: var(--ticket-surface);
        border-radius: 14px;
        border: 1px solid var(--ticket-border);
        padding: .7rem .8rem .65rem;
        margin-bottom: .75rem;
        position: relative;
    }

    .calendar-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: .75rem;
        bottom: .75rem;
        width: 3px;
        border-radius: 999px;
        background: var(--ticket-blue);
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .45rem;
        padding-left: .3rem;
    }

    .calendar-icon {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--ticket-blue-soft);
        color: var(--ticket-blue);
        font-size: .9rem;
    }

    .calendar-inline {
        background: #f9fafb;
        cursor: pointer;
        border-radius: 10px;
        border: 1px solid #e5edf5;
        font-size: .8rem;
    }

    .flatpickr-calendar.inline,
    .flatpickr-calendar.open {
        position: relative !important;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 8px !important;
    }

    .flatpickr-innerContainer,
    .flatpickr-rContainer,
    .flatpickr-days,
    .flatpickr-weekdays,
    .dayContainer {
        width: 100% !important;
    }

    .flatpickr-days { overflow: visible !important; }

    .flatpickr-months,
    .flatpickr-weekdays {
        background: #eff6ff;
        border-radius: 8px 8px 0 0;
    }

    .flatpickr-day {
        border-radius: 999px !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: var(--ticket-green) !important;
        border-color: var(--ticket-green) !important;
        color: #ffffff !important;
    }

    .flatpickr-day.today {
        border-color: var(--ticket-blue) !important;
        color: #1f2933 !important;
    }

    /* Ticket type card */

    .ticket-type-card {
        background: var(--ticket-surface);
        border-radius: 14px;
        border: 1px solid var(--ticket-border);
        padding: .8rem .8rem .7rem;
    }

    .ticket-type-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: .7rem;
        bottom: .7rem;
        width: 3px;
        border-radius: 999px;
        background: var(--ticket-green);
    }

    .ticket-type-wrapper {
        position: relative;
        padding-left: .5rem;
    }

    .ticket-type-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .4rem;
    }

    .ticket-type-icon {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        background: var(--ticket-green-soft);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--ticket-green);
        font-size: .8rem;
    }

    .ticket-type-current {
        font-size: .78rem;
        color: var(--ticket-text-muted);
        margin-bottom: .35rem;
    }

    .badge-error-type {
        font-size: .75rem;
        border-radius: 999px;
        padding: .18rem .55rem;
        background: #e5f3ff;
        color: #0f172a;
    }

    .ticket-type-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
        max-height: 155px;
        overflow-y: auto;
        padding-right: .2rem;
    }

    .error-type-chip {
        border-radius: 999px;
        font-size: .72rem;
        padding: .18rem .55rem;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        transition: all .14s ease;
        cursor: pointer;
    }

    .error-type-chip i {
        font-size: .8rem;
        opacity: .8;
    }

    .error-type-chip.active {
        border-color: var(--ticket-blue);
        background: var(--ticket-blue-soft);
        color: var(--ticket-blue);
        box-shadow: 0 0 0 1px rgba(37, 99, 235, .35);
    }

    .error-type-chip.active i {
        opacity: 1;
    }

    /* Customer small badge in sidebar (label) */

    .customer-badge {
        display: inline-block;
        padding: .18rem .55rem;
        font-size: .72rem;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        color: #4b5563;
        margin-bottom: .4rem;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    /* ========= KPI WIDGETS ========= */

    .kpi-card h6 {
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--ticket-text-muted);
        margin-bottom: .3rem;
    }

    .kpi-card h3 {
        font-size: 1.4rem;
        margin: 0;
    }

    /* ========= OVERVIEW TABS ========= */

    .nav-tabs .nav-link {
        border-radius: 999px 999px 0 0;
        padding: .35rem .9rem;
        font-size: .8rem;
    }

    .nav-tabs .nav-link.active {
        background: #111827;
        color: #ffffff !important;
        border-color: transparent;
    }

    .nav-tabs .nav-link:not(.active) {
        background: #f3f4f6;
        color: #374151;
        border-color: transparent;
    }

    #ticketChart {
        max-width: 100%;
        height: auto;
    }

    /* ========= KANBAN ========= */

    .kanban-shell {
        padding: .75rem;
        border-radius: 14px;
        border: 1px solid var(--ticket-border);
        background: #f9fafb;
    }

    .kanban-board {
        display: flex;
        gap: .75rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: .25rem;
    }

    .kanban-column {
        min-width: 270px;
        max-width: 320px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        max-height: 520px;
    }

    .kanban-column-header {
        padding: .6rem .75rem .45rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .4rem;
    }

    .kanban-column-title {
        font-size: .82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }

    .kanban-column-title span.badge {
        font-size: .7rem;
        border-radius: 999px;
        padding: .15rem .45rem;
    }

    .kanban-column-body {
        padding: .55rem .55rem .4rem;
        overflow-y: auto;
        flex: 1;
    }

    .task-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: .55rem .6rem;
        margin-bottom: .6rem;
        width: 100%;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04);
        cursor: grab;
        position: relative;
    }

    .task-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: .5rem;
        bottom: .5rem;
        width: 3px;
        border-radius: 999px;
        background: #9ca3af;
    }

    .task-card.priority-High::before,
    .task-card.priority-Hoch::before {
        background: #f97316;
    }

    .task-card.priority-["Sehr hoch"]::before,
    .task-card.priority-Sehr\ hoch::before {
        background: #dc2626;
    }

    .task-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .25rem;
        font-size: .78rem;
    }

    .task-card-title {
        font-size: .82rem;
        font-weight: 500;
        margin-bottom: .15rem;
    }

    .task-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .72rem;
        color: var(--ticket-text-muted);
        margin-bottom: .25rem;
        flex-wrap: wrap;
        gap: .25rem;
    }

    .task-card-avatars img {
        border-radius: 999px;
        margin-right: .15rem;
    }

    .task-card-footer {
        margin-top: .25rem;
    }

    .task-card textarea.task-comment {
        font-size: .75rem;
    }

    /* ========= TIMELINE ========= */

    .timeline {
        position: relative;
        border-left: 3px solid var(--ticket-blue);
        padding-left: 1.25rem;
    }

    .timeline-entry {
        position: relative;
        margin-bottom: 1.2rem;
        padding-left: .4rem;
    }

    .timeline-entry::before {
        content: "";
        position: absolute;
        left: -11px;
        top: .25rem;
        width: .7rem;
        height: .7rem;
        background: var(--ticket-surface);
        border-radius: 50%;
        border: 2px solid var(--ticket-blue);
    }

    .timeline-entry h6 {
        font-size: .86rem;
        margin-bottom: .1rem;
    }

    .timeline-entry .time {
        font-size: .74rem;
        color: var(--ticket-text-muted);
    }

    /* ========= GALLERY ========= */

    .file-gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .file-gallery-grid .card {
        transition: transform .16s ease, box-shadow .16s ease;
        cursor: pointer;
        border-radius: 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid #e5e7eb;
    }

    .file-gallery-grid .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15,23,42,.12);
    }

    .file-gallery-grid img,
    .file-gallery-grid .d-flex {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .file-gallery-grid .card-body {
        background: #f9fafb;
        padding: 8px;
    }

    .file-gallery-grid input.form-control-sm {
        font-size: .8rem;
        padding: 4px 7px;
        border-radius: 5px;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .file-gallery-grid .d-flex.align-items-center.justify-content-center {
        height: 200px;
        background: #f1f5f9;
        border-bottom: 1px solid #e5e7eb;
    }

    .card .btn-danger {
        border-radius: 50%;
        font-size: .75rem;
        padding: 4px 6px;
    }

    /* ========= MODALS ========= */

    .modal-content {
        border-radius: 18px;
        border: 1px solid #d7e2f0;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #e2e8f0;
        background: #f3f4f6;
        padding: .7rem .9rem;
    }

    .modal-title {
        font-size: .96rem;
        font-weight: 600;
        color: #111827;
    }

    .modal-title-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: #111827;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: .9rem;
    }

    .modal-body {
        padding: 1rem 1rem .9rem;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: .6rem 1rem;
        background: #f9fafb;
    }

    .form-label {
        font-size: .82rem;
        font-weight: 600;
        color: #111827;
    }

    .form-text {
        font-size: .74rem;
    }

    /* ========= CHAT ========= */

    #chatBox {
        background: #f9fafb;
        border-radius: 12px;
        padding: .75rem;
    }


    /* Sidebar section header */
.sidebar-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .5rem;
    padding: .2rem 0;
}

.sidebar-section-title {
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--ticket-text-muted);
}

.sidebar-section-badge {
    font-size: .7rem;
    border-radius: 999px;
    padding: .12rem .5rem;
    background: #e5e7eb;
    color: #374151;
}

/* unify sidebar cards to look like “tool panels” */
.ticket-sidebar .calendar-card,
.ticket-sidebar .ticket-type-card {
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
}

/* fix ticket-type-card position for ::before stripe */
.ticket-type-card {
    position: relative;
}

/* hover feedback for chips */
.error-type-chip:hover {
    border-color: var(--ticket-blue);
    background: #eff6ff;
}

    /* ========= RESPONSIVE ========= */

    @media (max-width: 1199px) {
        .ticket-header-left { max-width: 100%; }
        .ticket-header-main { align-items: flex-start; }
    }

    @media (max-width: 991px) {
        .dashboard-wrapper { flex-direction: column; }
        .ticket-sidebar {
            width: 100%;
            max-width: 100%;
            flex: 0 0 auto;
            order: 2;
        }
        .ticket-main { order: 1; }
        .file-gallery-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 768px) {
        .file-gallery-grid { grid-template-columns: repeat(2, 1fr); }
        .ticket-header-main { flex-direction: column; }
        .ticket-header-right { align-items: flex-start; }
    }

    @media (max-width: 576px) {
        .file-gallery-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">TICKET</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('problem_view') }}">Ticketliste</a></li>
                                <li class="breadcrumb-item active"><a>{{ $problem->name }} {{ $problem->lastname }}</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            {{-- FULL CUSTOMER / TICKET HEADER BANNER --}}
            <div class="ticket-header" id="ticket-overview">
                <div class="ticket-header-banner">
                    <div class="ticket-header-main">

                        {{-- LEFT: CUSTOMER INFO --}}
                        <div class="ticket-header-left">
                            <div class="customer-name">
                                {{ $problem->name }} {{ $problem->lastname }}
                            </div>
                            @if($problem->firma)
                                <div class="customer-company">
                                    <i class="fa fa-building mr-25"></i> {{ $problem->firma }}
                                </div>
                            @endif
                            <div class="customer-address">
                                <i class="fa fa-map-marker-alt mr-25"></i>
                                {{ $problem->street }} · {{ $problem->postcode }} {{ $problem->alt_city }}
                            </div>
                            <div class="customer-address mt-25">
                                <i class="fa fa-envelope mr-25"></i> {{ $problem->email ?? 'Keine Email' }}<br>
                                <i class="fa fa-phone mr-25"></i> {{ $problem->phone }}
                            </div>
                        </div>

                        {{-- CENTER: QUICK ACTIONS --}}
                        <div class="ticket-header-center">
                            <span class="contact-label">Kontakt</span>
                            <div class="ticket-header-actions">
                                @if($problem->email)
                                <a href="mailto:{{ $problem->email }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-envelope"></i> E-Mail
                                </a>
                                @endif
                                <a href="tel:{{ $problem->phone }}" class="btn btn-outline-success btn-sm">
                                    <i class="fa fa-phone"></i> Anrufen
                                </a>
                                <a href="#task-section" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-tasks"></i> Aufgaben
                                </a>
                            </div>
                        </div>

                        {{-- RIGHT: TICKET META / BADGES --}}
                        <div class="ticket-header-right">
                            <div class="ticket-pill-row">
                                <span class="ticket-pill ticket-pill-primary">
                                    <i class="fa fa-ticket-alt"></i>
                                    #{{ $problem->ticket_no }}
                                </span>

                                @php
                                    $statusLower = strtolower($problem->status);
                                @endphp
                                <span class="ticket-pill {{ $statusLower === 'end' || $statusLower === 'done' ? 'ticket-pill-status-done' : 'ticket-pill-status-open' }}" id="problemStatusBadge">
                                    <i class="fa fa-circle"></i>
                                    {{ $problem->status }}
                                </span>

                                @php
                                    $errorTypesShort = [
                                        'complaint' => 'Reklamation',
                                        'emergency_service' => 'Notdienst',
                                        'repair' => 'Reparatur',
                                        'maintenance' => 'Wartung',
                                        'malfunction' => 'Störung',
                                        'installation' => 'Installation',
                                        'configuration_error' => 'Konfiguration',
                                        'system_outage' => 'Systemausfall',
                                        'security_issue' => 'Security',
                                        'user_error' => 'Bedienfehler',
                                        'network_problem' => 'Netzwerk',
                                        'software_bug' => 'Software',
                                        'hardware_defect' => 'Hardware',
                                        'spare_part_request' => 'Ersatzteil',
                                        'timeout' => 'Timeout',
                                        'communication_failure' => 'Kommunikation',
                                        'power_outage' => 'Stromausfall',
                                        'update_failure' => 'Update',
                                        'access_issue' => 'Zugriff',
                                        'other' => 'Sonstiges'
                                    ];
                                @endphp
                                <span class="ticket-pill">
                                    <i class="fa fa-exclamation-circle"></i>
                                    {{ $errorTypesShort[$problem->error_type] ?? strtoupper($problem->error_type) }}
                                </span>
                            </div>

                            <div class="ticket-header-meta-right">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($problem->date)->isoFormat('DD.MM.YYYY') }}
                                </span>
                                <span>
                                    <i class="fa fa-user-friends"></i>
                                    {{ $responsible->count() }} Zuständige
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- META ROW --}}
                    <div class="ticket-header-meta">
                        <div class="ticket-header-meta-left">
                            <span><i class="fa fa-user-edit"></i> Verfasser: {{ $problem->fname }} {{ $problem->flastname }}</span>
                            <span><i class="fa fa-map"></i> Tickettyp: {{ $errorTypesShort[$problem->error_type] ?? strtoupper($problem->error_type) }}</span>
                        </div>
                        <div class="ticket-header-meta-right">
                            <span><i class="fa fa-tasks"></i> Aufgaben: {{ $doneTasks }}/{{ $totalTasks }}</span>
                            <span><i class="fa fa-percentage"></i> Fortschritt: {{ $progressPercent }}%</span>
                        </div>
                    </div>

                    {{-- SECTION NAV --}}
                    <div class="ticket-section-nav mt-2">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="#ticket-overview">Übersicht</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#task-section">Aufgaben / Kanban</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#report-section">Serviceberichte</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#gallery-section">Dateien</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#comment-section">Kommentare</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- MAIN WRAPPER --}}
            <div class="dashboard-wrapper">
 
               {{-- SIDEBAR: TOOLS (CALENDAR + TICKETTYPE) --}}
                <aside class="ticket-sidebar"> 
                    {{-- Calendar --}}
                    <div class="calendar-card mb-3">
                        <div class="calendar-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="calendar-icon">
                                    <i class="fa fa-calendar-alt"></i>
                                </span>
                                <div>
                                    <div class="sidebar-card-title">Kalender</div>
                                    <div class="sidebar-card-sub">Aufgaben & Termine zum Ticket</div>
                                </div>
                            </div>
                        </div>
                        <input type="text" id="mini-calendar" class="form-control calendar-inline" readonly>
                    </div>

                    {{-- Ticket type chip list --}}
                    @php
                        $errorTypes = [
                            'complaint' => 'REKLAMATION',
                            'emergency_service' => 'NOTDIENST',
                            'repair' => 'REPARATUR',
                            'maintenance' => 'WARTUNG',
                            'malfunction' => 'STÖRUNG',
                            'installation' => 'INSTALLATION',
                            'configuration_error' => 'KONFIGURATION',
                            'system_outage' => 'SYSTEMAUSFALL',
                            'security_issue' => 'SICHERHEITSPROBLEM',
                            'user_error' => 'BEDIENUNGSFEHLER',
                            'network_problem' => 'NETZWERKFEHLER',
                            'software_bug' => 'SOFTWAREFEHLER',
                            'hardware_defect' => 'HARDWAREFEHLER',
                            'spare_part_request' => 'ERSATZTEILANFRAGE',
                            'timeout' => 'ZEITÜBERSCHREITUNG',
                            'communication_failure' => 'KOMMUNIKATIONSPROBLEM',
                            'power_outage' => 'ENERGIEAUSFALL',
                            'update_failure' => 'UPDATEFEHLER',
                            'access_issue' => 'ZUGRIFFSPROBLEM',
                            'other' => 'SONSTIGES'
                        ];

                        $errorTypeIcons = [
                            'complaint' => 'fa-exclamation-circle',
                            'emergency_service' => 'fa-bolt',
                            'repair' => 'fa-tools',
                            'maintenance' => 'fa-sync-alt',
                            'malfunction' => 'fa-bug',
                            'installation' => 'fa-plug',
                            'configuration_error' => 'fa-sliders-h',
                            'system_outage' => 'fa-server',
                            'security_issue' => 'fa-shield-alt',
                            'user_error' => 'fa-user-times',
                            'network_problem' => 'fa-network-wired',
                            'software_bug' => 'fa-code',
                            'hardware_defect' => 'fa-microchip',
                            'spare_part_request' => 'fa-cogs',
                            'timeout' => 'fa-hourglass-half',
                            'communication_failure' => 'fa-comments-slash',
                            'power_outage' => 'fa-plug',
                            'update_failure' => 'fa-sync',
                            'access_issue' => 'fa-lock',
                            'other' => 'fa-ellipsis-h'
                        ];
                    @endphp

                    <div class="ticket-type-card">
                        <div class="ticket-type-wrapper">
                            <div class="ticket-type-header">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="ticket-type-icon">
                                        <i class="fa fa-layer-group"></i>
                                    </span>
                                    <div>
                                        <div class="sidebar-card-title">Tickettyp</div>
                                        <div class="sidebar-card-sub">Kategorisierung dieses Tickets</div>
                                    </div>
                                </div>
                            </div>

                            <div class="ticket-type-current">
                                <span class="me-1">Aktuell:</span>
                                <span class="badge badge-error-type">
                                    {{ $errorTypes[$problem->error_type] ?? strtoupper($problem->error_type) }}
                                </span>
                            </div>

                            <div class="ticket-type-buttons">
                                @foreach ($errorTypes as $key => $label)
                                    <button
                                        type="button"
                                        class="error-type-chip {{ $problem->error_type == $key ? 'active' : '' }}"
                                        data-error-type="{{ $key }}">
                                        <i class="fa {{ $errorTypeIcons[$key] ?? 'fa-tag' }}"></i>
                                        <span>{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </aside>


                {{-- MAIN CONTENT --}}
                <section class="ticket-main">

                    <div class="ticket-shell">

                        {{-- KPI widgets --}}
                        <div class="row mb-2">
                            <div class="col-md-3 kpi-card">
                                <div class="info-card text-center">
                                    <h6><i class="fa fa-ticket-alt me-1"></i> Gesamt-Tickets</h6>
                                    <h3 class="text-primary">{{ $ticketStats['total'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 kpi-card">
                                <div class="info-card text-center">
                                    <h6><i class="fa fa-bolt me-1"></i> Dringende Probleme</h6>
                                    <h3 class="text-danger">{{ $ticketStats['urgent'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 kpi-card">
                                <div class="info-card text-center">
                                    <h6><i class="fa fa-check-circle me-1"></i> Erledigt</h6>
                                    <h3 class="text-success">{{ $ticketStats['resolved'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 kpi-card">
                                <div class="info-card text-center">
                                    <h6><i class="fa fa-user-clock me-1"></i> Ø Reaktionszeit</h6>
                                    <h3 class="text-warning">{{ $ticketStats['average_response_time'] }} Std.</h3>
                                </div>
                            </div>
                        </div>

                        {{-- Ticket overview + other tabs --}}
                        <div class="info-card mb-2">
                            <ul class="nav nav-tabs" id="ticketTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                        <i class="fa fa-info-circle me-1 text-primary"></i> Überblick
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="ticketErrors-tab" data-bs-toggle="tab" data-bs-target="#ticketErrors" type="button" role="tab">
                                        <i class="fa fa-bug me-1 text-danger"></i> Ticketfehler & Lösungen
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="otherticket-tab" data-bs-toggle="tab" data-bs-target="#otherticket" type="button" role="tab">
                                        <i class="fa fa-ticket me-1 text-warning"></i> Andere Tickets
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="ticketTabContent">

                                {{-- Overview Tab --}}
                                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <h5 class="mb-3">
                                                <i class="fa fa-info-circle me-2 text-primary"></i>Ticketübersicht
                                            </h5>
                                            <p><strong>Aktuelles Ticket:</strong> <span class="text-muted">#{{ $problem->ticket_no }} - {!! $problem->problem !!}</span></p>
                                            <p><strong>Gemeldet von:</strong> {{ $problem->name }} {{ $problem->lastname }}</p>
                                            <p><strong>Verfasser:</strong> {{ $problem->fname }} {{ $problem->flastname }}</p>

                                            <p><strong>Status:</strong>
                                                <span class="badge bg-warning text-dark" id="problemStatusBadge">{{ $problem->status }}</span>
                                            </p>

                                            <p><strong>Registrierungsdatum:</strong> {{ \Carbon\Carbon::parse($problem->date)->isoFormat('DD.MM.YYYY') }}</p>
                                            <p><strong>Zugewiesene Personen:</strong></p>
                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                @foreach ($responsible as $res)
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $res->rname }} {{ $res->rlastname}}" class="avatar pull-up">
                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$res->rimage) }}" alt="Avatar" height="30" width="30">
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="progress mt-3" style="height: 8px;">
                                                <div class="progress-bar bg-success ticket-progress-bar" style="width: {{ $progressPercent }}%;"></div>
                                            </div>
                                            <small class="text-muted ticket-progress-text">{{ $progressPercent }}% completed ({{ $doneTasks }}/{{ $totalTasks }} tasks)</small>
                                        </div>

                                        <div class="col-md-6">
                                            <canvas id="ticketChart" style="max-height: 250px;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                {{-- ticketErrors & Solutions Tab --}}
                                <div class="tab-pane fade" id="ticketErrors" role="tabpanel">
                                    <div class="accordion mt-3" id="errorAccordion">
                                        @foreach($ticketErrors as $index => $e)
                                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-4">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#error{{ $index }}">
                                                        <i class="fa fa-exclamation-circle text-danger me-2"></i> {{ $e->error_code }} - {{ $e->problem_types }}
                                                    </button>
                                                </h2>
                                                <div id="error{{ $index }}" class="accordion-collapse collapse">
                                                    <div class="accordion-body row g-3">
                                                        <div class="col-md-6">
                                                            <h6><i class="fa fa-bug me-2 text-danger"></i> Fehlerdetails</h6>
                                                            <ul class="list-unstyled">
                                                                <li><strong>Fehlercode:</strong> {{ $e->error_code }}</li>
                                                                <li><strong>Problemtyp:</strong> {{ $e->problem_types }}</li>
                                                                <li><strong>Produkt:</strong> {{ $e->product ?? 'N/A' }}</li>
                                                                <li><strong>Artikelname:</strong> {{ $e->article_name ?? 'N/A' }}</li>
                                                            </ul>
                                                            <p><strong>Grund:</strong></p>
                                                            <div>{!! $e->reason !!}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6><i class="fa fa-tools me-2 text-success"></i> Lösung</h6>
                                                            <div>{!! $e->solution !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if(count($ticketErrors) == 0)
                                            <div class="alert alert-info">Für dieses Ticket wurden keine Ticketfehler protokolliert.</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Other tickets Tab --}}
                                <div class="tab-pane fade" id="otherticket" role="tabpanel">
                                    <div class="accordion" id="accordionOtherTickets">
                                        @php
                                            $groupedOther = $other->groupBy('product');

                                            $statusLabels = [
                                                'offen' => 'Offen',
                                                'junk'  => 'Papierkorb',
                                                'done'  => 'Erledigt'
                                            ];

                                            $errorTypesOther = $errorTypes;
                                        @endphp

                                        @foreach($groupedOther as $product => $productItems)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-{{ Str::slug($product) }}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-{{ Str::slug($product) }}" aria-expanded="false"
                                                            aria-controls="collapse-{{ Str::slug($product) }}">
                                                        <i class="fa fa-box me-2"></i> {{ $product }}
                                                    </button>
                                                </h2>
                                                <div id="collapse-{{ Str::slug($product) }}" class="accordion-collapse collapse"
                                                     aria-labelledby="heading-{{ Str::slug($product) }}" data-bs-parent="#accordionOtherTickets">
                                                    <div class="accordion-body">
                                                        @foreach($productItems as $otherticket)
                                                            <div class="border rounded-3 p-3 mb-3 shadow-sm">
                                                                <h6 class="mb-2">
                                                                    <i class="fa fa-ticket-alt me-1 text-primary"></i> Ticket-Nr: <strong>#{{ $otherticket->ticket_no }}</strong>
                                                                    <span class="badge bg-secondary ms-2">
                                                                        {{ $errorTypesOther[$otherticket->error_type] ?? strtoupper($otherticket->error_type) }}
                                                                    </span>
                                                                </h6>

                                                                <p><strong>Kunde:</strong> {{ $otherticket->name }} {{ $otherticket->lastname }}</p>
                                                                <p><strong>Adresse:</strong> {{ $otherticket->street }}, {{ $otherticket->postcode }} {{ $otherticket->alt_city }}</p>
                                                                <p><strong>Telefon:</strong> {{ $otherticket->phone }}</p>
                                                                <p><strong>Email:</strong> {{ $otherticket->email }}</p>

                                                                <p><strong>Startdatum:</strong> {{ $otherticket->start_date }}</p>
                                                                <p><strong>Fortschrittsdatum:</strong> {{ $otherticket->progress_date ?? '—' }}</p>
                                                                <p><strong>Status:</strong>
                                                                    <span class="badge bg-info text-dark">
                                                                        {{ $statusLabels[$otherticket->status] ?? ucfirst($otherticket->status) }}
                                                                    </span>
                                                                </p>

                                                                <p><strong>Problem:</strong> {!! $otherticket->problem !!}</p>
                                                                <p><strong>Bearbeiter:</strong> {{ $otherticket->fname }} {{ $otherticket->flastname }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- TASKS / KANBAN / LIST / TIMELINE / COMMENTS --}}
                        <div id="task-section"></div>
                        <ul class="nav nav-tabs" id="taskTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="kanban-tab" data-bs-toggle="tab" data-bs-target="#kanban" type="button" role="tab">Kanban-Ansicht</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">Listenansicht</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">Zeitleistenansicht</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="comment-tab" data-bs-toggle="tab" data-bs-target="#comment" type="button" role="tab">Kommentare</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-2" id="taskTabContent">
                            {{-- Kanban --}}
                            <div class="tab-pane fade show active" id="kanban" role="tabpanel">
                                <div class="kanban-shell">
                                    <div class="kanban-board">
                                        @php
                                            $statusMap = [
                                                'Open' => ['label' => 'Offen', 'icon' => 'fa-inbox'],
                                                'In Progress' => ['label' => 'In Arbeit', 'icon' => 'fa-spinner'],
                                                'Done' => ['label' => 'Erledigt', 'icon' => 'fa-check-circle']
                                            ];
                                        @endphp
                                        @foreach(['Open', 'In Progress', 'Done'] as $status)
                                            <div class="kanban-column" data-status="{{ $status }}">
                                                <div class="kanban-column-header">
                                                    <div class="kanban-column-title">
                                                        <i class="fa {{ $statusMap[$status]['icon'] }}"></i>
                                                        <span>{{ $statusMap[$status]['label'] }}</span>
                                                        @php
                                                            $count = 0;
                                                            if(isset($taskCounts)) {
                                                                if($status === 'Open') $count = $taskCounts->open ?? 0;
                                                                if($status === 'In Progress') $count = $taskCounts->in_progress ?? 0;
                                                                if($status === 'Done') $count = $taskCounts->done ?? 0;
                                                            }
                                                        @endphp
                                                        <span class="badge bg-light text-muted">{{ $count }}</span>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary openTaskModalBtn">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="kanban-column-body">
                                                    {{-- JS injects .task-card here --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- List --}}
                            <div class="tab-pane fade" id="list" role="tabpanel">
                                <div class="info-card mt-3">
                                    <h5>Ticketliste</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Titel</th>
                                                    <th>Zugewiesen</th>
                                                    <th>Status</th>
                                                    <th>Priorität</th>
                                                    <th>Startdatum</th>
                                                    <th>Fälligkeitsdatum</th>
                                                    <th>Differenz</th>
                                                    <th>Fortschritt</th>
                                                    <th>Erledigt</th>
                                                    <th>Aktionen</th>
                                                </tr>
                                            </thead>
                                            <tbody class="task-table-body">
                                                {{-- Dynamic rows --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Timeline --}}
                            <div class="tab-pane fade" id="timeline" role="tabpanel">
                                <div class="info-card">
                                    <h5>Aktivitätszeitleiste</h5>
                                    <div class="timeline dynamic-timeline">
                                        {{-- AJAX entries --}}
                                    </div>
                                </div>
                            </div>

                            {{-- Comments --}}
                            <div class="tab-pane fade" id="comment" role="tabpanel">
                                <div class="info-card" id="comment-section">
                                    <div class="col-lg-12 mb-4">
                                        <div class="info-card h-100 d-flex flex-column">
                                            <h5 class="mb-3"><i class="fa fa-comments me-2 text-primary"></i> Ticketdiskussion</h5>
                                            <div class="chat-box flex-grow-1 overflow-auto mb-3" style="max-height: 500px;" id="chatBox">
                                                @foreach ($ticket->comments as $comment)
                                                    <div class="d-flex align-items-start mb-3">
                                                        <img src="{{ asset('images/employee/' . ($comment->employee->image ?? 'default.jpg')) }}" class="rounded-circle me-2" width="40" alt="User">
                                                        <div class="flex-grow-1">
                                                            <div class="p-2 rounded-3 shadow-sm bg-white">
                                                                <strong>{{ $comment->employee->name }}</strong>
                                                                <small class="text-muted">• {{ $comment->created_at->diffForHumans() }}</small>
                                                                <p class="mb-1">{{ $comment->comment }}</p>
                                                                @if(auth()->id() === $comment->employee_id)
                                                                    <div class="d-flex gap-2">
                                                                        <button class="btn btn-sm btn-outline-secondary edit-btn" data-id="{{ $comment->id }}" data-comment="{{ $comment->comment }}"><i class="fa fa-edit"></i></button>
                                                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $comment->id }}"><i class="fa fa-trash"></i></button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="input-group mt-auto">
                                                <input type="text" class="form-control comment-input" placeholder="Geben Sie Ihren Kommentar ein..." />
                                                <button class="btn btn-outline-secondary mic-comment-btn" id="mic-btn"><i class="fa fa-microphone"></i></button>
                                                <button class="btn btn-primary send-comment-btn"><i class="fa fa-paper-plane"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Task Modal --}}
                        {{-- Task Create / Edit Modal --}}
                        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel"
                            aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <form id="saveTaskForm">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                    <input type="hidden" name="add_to_calendar" id="add_to_calendar" value="0">
                                    <input type="hidden" id="task_mode" name="task_mode" value="create">
                                    <input type="hidden" id="editing_task_id" name="editing_task_id" value="">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="taskModalLabel">
                                                <span class="modal-title-icon">
                                                    <i class="fa fa-tasks"></i>
                                                </span>
                                                <span>Neue Aufgabe</span>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Schließen"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-3">

                                                {{-- Titelquelle --}}
                                                <div class="col-12">
                                                    <label class="form-label fw-bold d-block mb-1">Titelquelle</label>
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="title_mode"
                                                                id="mode_error" value="error" checked>
                                                            <label class="form-check-label" for="mode_error">
                                                                Aktueller Fehler
                                                            </label>
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="title_mode"
                                                                id="mode_custom" value="custom">
                                                            <label class="form-check-label" for="mode_custom">
                                                                Eigener Titel
                                                            </label>
                                                        </div>

                                                        <div id="error_select_wrapper" class="flex-fill">
                                                            <select name="error_id" id="error_id" class="form-control">
                                                                <option value="">-- Fehler auswählen --</option>
                                                                @foreach($ticketErrors as $error)
                                                                    <option value="{{ $error->error_id }}"
                                                                            data-problem="{{ $error->problem_types }}">
                                                                        {{ $error->problem_types }} ({{ $error->error_code }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted">
                                                                Wähle einen Fehler als Basis für den Aufgabentitel.
                                                            </small>
                                                        </div>

                                                        <div id="custom_title_wrapper" class="flex-fill" style="display:none;">
                                                            <input type="text" name="title" id="title" class="form-control"
                                                                placeholder="Eigenen Titel schreiben">
                                                            <small class="text-muted">
                                                                Freitext-Titel für individuelle Aufgaben.
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Zuweisung --}}
                                                <div class="col-12">
                                                    <label for="teams" class="form-label fw-bold">Zuweisen an</label>
                                                    <select name="teams[]" id="teams" class="form-control" multiple="multiple">
                                                        @foreach($responsible as $emp)
                                                            <option value="{{ $emp->employee_id }}">
                                                                {{ $emp->rname }} {{ $emp->rlastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">
                                                        Wähle einen oder mehrere Mitarbeiter aus, die diese Aufgabe bearbeiten.
                                                    </small>
                                                </div>

                                                {{-- Datum + Priorität --}}
                                                <div class="col-md-6">
                                                    <label for="task_due_date" class="form-label fw-bold">Datum</label>
                                                    <input type="date" name="due_date" id="task_due_date"
                                                        class="form-control" required>
                                                    <input type="hidden" name="start_date" id="task_start_date">
                                                    <small class="text-muted">
                                                        Start- und Fälligkeitsdatum sind standardmäßig identisch.
                                                    </small>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="priority" class="form-label fw-bold">Priorität</label>
                                                    <select name="priority" id="priority" class="form-control" required>
                                                        <option value="Normal">Normal</option>
                                                        <option value="Hoch">Hoch</option>
                                                        <option value="Sehr hoch">Sehr hoch</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        Priorität der Aufgabe für die Planung.
                                                    </small>
                                                </div>

                                                {{-- Kalender-Toggle --}}
                                                <div class="col-12">
                                                    <div class="form-check form-switch mb-1">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="add_to_calendar_switch">
                                                        <label class="form-check-label fw-bold" for="add_to_calendar_switch">
                                                            Zum Kalender hinzufügen
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">
                                                        Bei Aktivierung wird zusätzlich ein Kalendereintrag erstellt.
                                                    </small>
                                                </div>

                                                {{-- Zeitfelder für Kalender --}}
                                                <div id="calendar_time_fields" class="col-12" style="display:none;">
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <label for="calendar_start_time" class="form-label">Startzeit</label>
                                                            <input type="time" name="start_time" id="calendar_start_time"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="calendar_end_time" class="form-label">Endzeit</label>
                                                            <input type="time" name="end_time" id="calendar_end_time"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted">
                                                                Der Termin verwendet das oben gewählte Datum.
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>{{-- row --}}
                                        </div>{{-- modal-body --}}

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Abbrechen</button>
                                            <button type="submit" class="btn btn-success">
                                                Speichern
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Solution Modal --}}
                        {{-- Task Solution Modal (Lösung + Done) --}}
                        <div class="modal fade" id="solutionModal" tabindex="-1" aria-hidden="true"
                            data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <form id="saveSolutionForm">
                                    @csrf
                                    <input type="hidden" name="task_id" id="solution_task_id">
                                    <input type="hidden" name="solution" id="quillSolution">

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title d-flex align-items-center gap-2">
                                                <span class="modal-title-icon">
                                                    <i class="fa fa-lightbulb"></i>
                                                </span>
                                                <span>Lösung hinzufügen</span>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Schließen"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Lösung</label>
                                                <div id="quillEditor" style="height: 200px;"></div>

                                                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                                                    <select id="languageSelect"
                                                            class="form-control form-select-sm w-auto">
                                                        <option value="de-DE">German</option>
                                                        <option value="en-US">English</option>
                                                        <option value="tr-TR">Turkish</option>
                                                        <option value="ar-SA">Arabic</option>
                                                        <option value="fa-IR">Persian</option>
                                                    </select>
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                            id="startSpeechBtn">
                                                        <i class="fa fa-microphone"></i> Mikrofon
                                                    </button>
                                                    <small class="text-muted">
                                                        Per Spracheingabe direkt in die Lösung diktieren.
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="alert alert-info mt-3 mb-0">
                                                Beim Speichern wird die Aufgabe als <strong>erledigt</strong> markiert
                                                und die eingetragene Lösung am Task gespeichert.
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">
                                                Lösung speichern
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        {{-- Service reports --}}
                        <div class="row" id="report-section">
                            <div class="col-lg-12 mb-4">
                                <div class="info-card h-100">
                                    <h5 class="mb-4">Erstellen eines Serviceberichts</h5>

                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form id="serviceReportForm">
                                        @csrf
                                        <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                        <input type="hidden" name="employee_id" value="{{ auth()->user()->name}}">
                                        <input type="hidden" name="customer_id" value="{{ $problem->cid }}">
                                        <input type="hidden" name="alternative_id" value="{{ $problem->alternative_id }}">
                                        <input type="hidden" name="product_id" value="{{ $problem->product_id }}">
                                        <input type="hidden" name="report" id="hidden-report">

                                        <div class="mb-3">
                                            <select name="language" id="language-select" class="form-control" required>
                                                <option value="">Sprache auswählen</option>
                                                <option value="de-DE" selected>German</option>
                                                <option value="fa-IR">Persian</option>
                                                <option value="ar-SA">Arabic</option>
                                                <option value="en-US">English</option>
                                                <option value="tr-TR">Turkish</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" name="title" class="form-control form-control-lg" placeholder="Berichtstitel" required>
                                        </div>

                                        <div class="mb-3 position-relative">
                                            <div id="quill-editor" style="height: 200px;"></div>
                                            <button type="button" id="mic-button" class="btn btn-sm btn-outline-primary position-absolute end-0 bottom-0 me-2 mb-2">
                                                <i class="fa fa-microphone"></i>
                                            </button>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success btn-lg px-4">
                                                <i class="fa fa-paper-plane me-2"></i> Bericht senden
                                            </button>
                                        </div>
                                    </form>

                                    <h5 class="mb-3 mt-5">Serviceberichte</h5>

                                    @forelse ($ticketReports as $report)
                                        <div class="card mb-4 shadow-sm border-0 rounded-4">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">
                                                    <i class="fa fa-clipboard-list me-2"></i> {{ $report->title }}
                                                </h6>
                                                <div class="card-text text-muted">{!! $report->report !!}</div>

                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-outline-primary like-button" data-report-id="{{ $report->id }}">
                                                            <i class="fa fa-thumbs-up"></i> Like ({{ $report->likes ?? 0 }})
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#comment{{ $report->id }}">
                                                            <i class="fa fa-comment"></i> Kommentar
                                                        </button>
                                                    </div>
                                                    <small class="text-muted fst-italic">
                                                        Reported by {{ $report->employee->name ?? 'N/A' }} • {{ $report->created_at->diffForHumans() }}
                                                    </small>
                                                </div>

                                                <div class="collapse mt-3" id="comment{{ $report->id }}">
                                                    <form method="POST" action="{{ route('ticket-report-comments.store') }}" class="comment-form">
                                                        @csrf
                                                        <input type="hidden" name="ticket_id" value="{{ $report->ticket_id }}">
                                                        <input type="hidden" name="ticket_report_id" value="{{ $report->id }}">
                                                        <input type="hidden" name="customer_id" value="{{ $report->customer_id }}">
                                                        <input type="hidden" name="alternative_id" value="{{ $report->alternative_id }}">
                                                        <input type="hidden" name="product_id" value="{{ $report->product_id }}">
                                                        <input type="hidden" name="comment_by" value="{{ auth()->user()->name }}">

                                                        <textarea name="comment" class="form-control comment-textarea" rows="2" placeholder="Write a comment..." required></textarea>
                                                        <div class="text-end mt-2">
                                                            <button type="submit" class="btn btn-sm btn-outline-success">erstellen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted fst-italic">Es wurden noch keine Serviceberichte übermittelt.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Ticket gallery --}}
                        <div class="info-card mb-2" id="gallery-section">
                            <h5 class="mb-3"><i class="fa fa-image me-2 text-success"></i> Ticketgalerie</h5>

                            <form action="{{ route('ticket.upload') }}" class="dropzone border border-2 rounded-3 mb-3" id="ticketDropzone" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                <input type="hidden" name="customer_id" value="{{ $problem->customer_id }}">
                                <input type="hidden" name="alternative_id" value="{{ $problem->alternative_id }}">
                                <input type="hidden" name="product_id" value="{{ $problem->product_id }}">
                                <input type="hidden" name="employee_id" value="{{ auth()->user()->name }}">
                                <input type="hidden" name="ticket_type" value="{{ $problem->error_type }}">
                            </form>

                            <div class="btn-group mb-3" id="filterButtons">
                                <button class="btn btn-dark btn-sm active" data-filter="all">Show all</button>
                                <button class="btn btn-outline-secondary btn-sm" data-filter="image">Images</button>
                                <button class="btn btn-outline-secondary btn-sm" data-filter="pdf">PDF</button>
                                <button class="btn btn-outline-secondary btn-sm" data-filter="docx">Word</button>
                                <button class="btn btn-outline-secondary btn-sm" data-filter="xlsx">Excel</button>
                            </div>

                            <div class="row g-3" id="galleryGrid">
                                <div class="file-gallery-grid" id="fileGallery"></div>
                            </div>
                        </div>

                        {{-- Image modal --}}
                        <div class="modal fade" id="imgModal1" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-dark text-white">
                                    <div class="modal-body p-0">
                                        <img src="" class="img-fluid w-100 rounded" id="modalImage">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Generic preview modal --}}
                        <div class="modal fade" id="previewModal" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content bg-dark text-white">
                                    <div class="modal-body text-center p-0">
                                        <div id="modalPreviewContent" class="w-100 h-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- ticket-shell --}}
                </section>

            </div>{{-- dashboard-wrapper --}}
        </div>
    </div>
</div>
@endsection 

@section('script')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
    const highlightedDates = @json($taskDates);
    const allTasks         = @json($taskList);
</script>

<script>
    flatpickr("#mini-calendar", {
        inline: true,
        enable: highlightedDates,
        locale: "en",
        onChange: function(selectedDates, dateStr) {
            const tasksOnDate = allTasks.filter(task =>
                task.start_date === dateStr || task.due_date === dateStr
            );

            let message = tasksOnDate.length
                ? `<strong>${tasksOnDate.length} task(s) on ${dateStr}:</strong><ul>` +
                    tasksOnDate.map(task => `<li><strong>${task.title}</strong> - ${task.status}</li>`).join('') +
                  `</ul>`
                : `No tasks on ${dateStr}`;

            Swal.fire({
                title: 'Aufgaben am ausgewählten Datum',
                html: message,
                icon: 'info'
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Titelquelle logic
    const errorRadio = document.getElementById('mode_error');
    const customRadio = document.getElementById('mode_custom');
    const errorWrap   = document.getElementById('error_select_wrapper');
    const customWrap  = document.getElementById('custom_title_wrapper');
    const errorSelect = document.getElementById('error_id');
    const titleInput  = document.getElementById('title');

    function syncTitleFromError() {
        const opt = errorSelect?.selectedOptions?.[0];
        const txt = opt ? (opt.dataset.problem || '') : '';
        if (txt && titleInput) titleInput.value = txt;
    }

    errorRadio?.addEventListener('change', () => {
        errorWrap.style.display  = '';
        customWrap.style.display = 'none';
        syncTitleFromError();
    });

    customRadio?.addEventListener('change', () => {
        errorWrap.style.display  = 'none';
        customWrap.style.display = '';
        titleInput?.focus();
    });

    errorSelect?.addEventListener('change', syncTitleFromError);

    // Kalender toggle
    const calSwitch  = document.getElementById('add_to_calendar_switch');
    const calHidden  = document.getElementById('add_to_calendar');
    const timeFields = document.getElementById('calendar_time_fields');

    calSwitch?.addEventListener('change', function () {
        const on = this.checked;
        calHidden.value       = on ? '1' : '0';
        timeFields.style.display = on ? '' : 'none';
    });

    // Start date mirrors due date
    const due   = document.getElementById('task_due_date');
    const start = document.getElementById('task_start_date');
    const syncDates = () => { if (start && due) start.value = due.value; };

    due?.addEventListener('change', syncDates);
    syncDates();

    // Select2
    if (window.$ && $('#teams').length) {
        $('#teams').select2({
            placeholder: 'Mitarbeiter auswählen',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#taskModal')
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const due   = document.getElementById('task_due_date');
    const start = document.getElementById('task_start_date');

    if (due && start) {
        const sync = () => { start.value = due.value; };
        due.addEventListener('change', sync);

        if (!due.value) {
            const today = new Date().toISOString().split('T')[0];
            due.value   = today;
            start.value = today;
        } else {
            start.value = due.value;
        }
    }
});
</script>

<script>
const ctx = document.getElementById('ticketChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Open', 'In Progress', 'Done'],
        datasets: [{
            label: 'Task Status',
            data: [
                {{ $taskCounts->open ?? 0 }},
                {{ $taskCounts->in_progress ?? 0 }},
                {{ $taskCounts->done ?? 0 }}
            ],
            backgroundColor: ['#facc15', '#0ea5e9', '#22c55e'],
            borderWidth: 1
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 15,
                    padding: 10
                }
            }
        }
    }
});
</script>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const ticketId  = {{ $problem->id }};

Dropzone.options.ticketDropzone = {
    maxFilesize: 5,
    acceptedFiles: '.png,.jpg,.jpeg,.bmp,.pdf,.docx,.xlsx',
    init: function () {
        this.on("sending", function(file, xhr, formData) {
            formData.append("_token", csrfToken);
            formData.append("ticket_id", ticketId);
            formData.append("customer_id", "{{ $problem->customer_id }}");
            formData.append("alternative_id", "{{ $problem->alternative_id }}");
            formData.append("product_id", "{{ $problem->product_id }}");
            formData.append("employee_id", "{{ auth()->user()->id }}");
            formData.append("ticket_type", "{{ $problem->error_type }}");
        });

        this.on("success", function() {
            loadFiles(ticketId);
        });

        this.on("error", function(file, response) {
            console.error("Upload failed:", response);
        });
    }
};

function loadFiles(id) {
    if (!id) {
        console.error('No ID provided to loadFiles()');
        return;
    }

    const baseUrl = window.location.origin;
    fetch(`/fetch/ticket/files/${id}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            for (const type in data) {
                data[type].forEach(file => {
                    const typeClass = getFileTypeClass(file.file_type);
                    const icon      = getFileIcon(file.file_type);
                    const isImage   = file.file_type.includes('image');
                    const url       = `${baseUrl}/${file.image}`;
                    const name      = file.image_name ? file.image_name.replace(/"/g, '&quot;') : '';

                    html += `
                        <div class="col-12 col-md-12 filter-item ${typeClass}" data-id="${file.id}">
                            <div class="card h-100 shadow-sm border">
                                <div class="position-relative" onclick="previewFile('${url}', '${file.file_type}')">
                                    ${isImage ? `
                                        <img src="${url}" class="img-fluid rounded-top" style="object-fit:cover;height:200px;">
                                    ` : `
                                        <div class="d-flex align-items-center justify-content-center" style="height:200px;">
                                            <i class="fas ${icon} fa-4x"></i>
                                        </div>
                                    `}
                                </div>
                                <div class="card-body p-2">
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           value="${name}"
                                           onchange="renameFile(${file.id}, this.value)">
                                </div>
                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                        onclick="deleteFile(${file.id}); event.stopPropagation();">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>`;
                });
            }
            document.getElementById('fileGallery').innerHTML = html;
        })
        .catch(err => console.error("Error loading files:", err));
}

function getFileIcon(type) {
    if (type.includes('pdf')) return 'fa-file-pdf text-danger';
    if (type.includes('word') || type.includes('doc')) return 'fa-file-word text-primary';
    if (type.includes('excel') || type.includes('spreadsheet') || type.includes('xlsx')) return 'fa-file-excel text-success';
    return 'fa-file text-secondary';
}

function getFileTypeClass(type) {
    if (type.includes('image')) return 'image';
    if (type.includes('pdf')) return 'pdf';
    if (type.includes('word') || type.includes('doc')) return 'docx';
    if (type.includes('excel') || type.includes('xlsx')) return 'xlsx';
    return 'other';
}

function previewFile(url, fileType) {
    const modalContent = document.getElementById('modalPreviewContent');
    if (!modalContent) return console.error('Modal content container missing!');

    let content = '';
    if (fileType.includes('image')) {
        content = `<img src="${url}" class="img-fluid w-100 rounded" style="max-height:90vh;">`;
    } else if (fileType.includes('pdf')) {
        content = `<iframe src="${url}" class="w-100" style="height:90vh;" frameborder="0"></iframe>`;
    } else {
        content = `
            <div class="text-center p-4">
                <i class="fas ${getFileIcon(fileType)} fa-4x mb-3"></i>
                <p>Preview not available. <a href="${url}" class="btn btn-primary" target="_blank">Open File</a></p>
            </div>`;
    }

    modalContent.innerHTML = content;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function deleteFile(id) {
    if (!confirm('Delete this file?')) return;

    fetch(`/ticket/file/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                loadFiles(ticketId);
            }
        })
        .catch(err => console.error("Delete failed:", err));
}

function renameFile(id, newName) {
    fetch(`/ticket/file/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ image_name: newName })
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) console.log('Renamed successfully');
        })
        .catch(err => console.error("Rename failed:", err));
}

document.addEventListener('DOMContentLoaded', () => {
    loadFiles(ticketId);

    document.querySelectorAll('#filterButtons button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#filterButtons button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter');
            document.querySelectorAll('.filter-item').forEach(item => {
                item.style.display = (filter === 'all' || item.classList.contains(filter)) ? 'block' : 'none';
            });
        });
    });
});
</script>

<script>
$(document).ready(function () {
    const problemId = {{ $problem->id }};
    const csrf      = '{{ csrf_token() }}';
    let quillSolutionEditor;
    let recognition;
    let recognizing = false;

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        if (isNaN(date)) return '';
        const day   = ('0' + date.getDate()).slice(-2);
        const month = ('0' + (date.getMonth() + 1)).slice(-2);
        const year  = date.getFullYear();
        return `${day}.${month}.${year}`;
    }

    // Quill for solution modal
    quillSolutionEditor = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Schreibe die Lösung hier rein...'
    });

    // Speech Recognition for solution modal
    if (window.SpeechRecognition || window.webkitSpeechRecognition) {
        recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous    = false;
        recognition.interimResults = false;

        $('#languageSelect').on('change', function () {
            recognition.lang = $(this).val();
        });

        $('#startSpeechBtn').on('click', function () {
            if (recognizing) {
                recognition.stop();
                recognizing = false;
                $(this).removeClass('btn-danger').addClass('btn-secondary')
                       .html('<i class="fa fa-microphone"></i> Mikrofon');
            } else {
                recognition.lang = $('#languageSelect').val();
                recognition.start();
                recognizing = true;
                $(this).removeClass('btn-secondary').addClass('btn-danger')
                       .html('<i class="fa fa-stop"></i> Stop');
            }
        });

        recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript;
            quillSolutionEditor.clipboard.dangerouslyPasteHTML(
                quillSolutionEditor.root.innerHTML + ' ' + transcript
            );
        };

        recognition.onend = function () {
            recognizing = false;
            $('#startSpeechBtn').removeClass('btn-danger').addClass('btn-secondary')
                .html('<i class="fa fa-microphone"></i> Mikrofon');
        };

        recognition.onerror = function (event) {
            Swal.fire('Speech recognition error', event.error, 'error');
            recognizing = false;
            $('#startSpeechBtn').removeClass('btn-danger').addClass('btn-secondary')
                .html('<i class="fa fa-microphone"></i> Mikrofon');
        };
    } else {
        $('#startSpeechBtn').hide();
        $('#languageSelect').hide();
    }

    loadTasks(problemId);
    loadTimeline(problemId);
    setInterval(() => loadTimeline(problemId), 5000);

    function loadTasks(problemId) {
        $.get(`/ticket-tasks/load/${problemId}`, function (tasks) {
            $('.kanban-column .task-card').remove();
            $('tbody.task-table-body').empty();

            tasks.forEach(task => {
                const progress = task.is_done ? 100 : task.status === 'In Progress' ? 60 : 25;
                const badge    = task.priority === 'High'
                    ? 'danger'
                    : task.priority === 'Medium'
                        ? 'info'
                        : 'success';

                const startDate = formatDate(task.start_date);
                const dueDate   = formatDate(task.due_date);

                const team = Array.isArray(task.team_members) ? task.team_members : [];
                let peopleHtml = '';

                if (team.length > 0) {
                    const avatars = team.map(m => `
                        <img src="/images/employee/${m.image ?? 'default.png'}"
                             class="rounded-circle me-1"
                             width="26" height="26"
                             title="${m.name}">
                    `).join('');
                    peopleHtml = `
                        <div class="d-flex align-items-center mb-2">
                            ${avatars}
                        </div>`;
                } else {
                    peopleHtml = `
                        <div class="d-flex align-items-center mb-2">
                            <img src="/images/employee/${task.employee?.image ?? 'default.png'}"
                                 class="rounded-circle me-2" width="30" height="30">
                            <small>${task.employee?.name ?? 'Unbekannt'}</small>
                        </div>`;
                }

                const kanbanCard = `
                    <div class="task-card mb-3" draggable="true" data-task-id="${task.id}">
                        <div class="d-flex justify-content-between">
                            <strong>#${task.id}</strong>
                            <span class="badge bg-${badge}">${task.priority ?? ''}</span>
                        </div>
                        <p class="mb-1">${task.title ?? 'No title'}</p>
                        ${peopleHtml}
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-${badge}" style="width: ${progress}%"></div>
                        </div>
                        <div class="mt-2">
                            <textarea class="form-control form-control-sm mb-1 task-comment"
                                      rows="1"
                                      placeholder="Kommentar hinzufügen..."
                                      data-task-id="${task.id}"
                                      data-ticket-id="${task.ticket_id}"
                                      data-task-title="${task.title}"></textarea>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-secondary reasonTask" data-id="${task.id}">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success editTask" data-id="${task.id}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger deleteTask" data-id="${task.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;

                const teamNames = (Array.isArray(task.team_members) && task.team_members.length)
                    ? task.team_members.map(m => m.name).join(', ')
                    : (task.employee?.name ?? '');

                const tableRow = `
                    <tr>
                        <td>#${task.id}</td>
                        <td>${task.title}</td>
                        <td>${teamNames}</td>
                        <td><span class="badge bg-${badge}">${task.status}</span></td>
                        <td><span class="badge bg-${badge}">${task.priority}</span></td>
                        <td>${startDate}</td>
                        <td>${dueDate}</td>
                        <td>${task.difference ?? ''} days</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-${badge}" style="width: ${progress}%"></div>
                            </div>
                        </td>
                        <td>
                            <input type="checkbox"
                                   class="form-check-input is-done-checkbox"
                                   data-task-id="${task.id}"
                                   ${task.is_done ? 'checked' : ''}>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary reasonTask" data-id="${task.id}">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success editTask" data-id="${task.id}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger deleteTask" data-id="${task.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                $(`.kanban-column[data-status="${task.status}"]`).append(kanbanCard);
                $('tbody.task-table-body').append(tableRow);
            });
        });
    }

    $(document).on('click', '.reasonTask', function () {
        const id = $(this).data('id');
        $.get(`/ticket-tasks/${id}`, function (data) {
            Swal.fire({
                title: 'Task Solution',
                html: data.solution || '<p class="text-muted">No solution added.</p>',
                icon: 'info',
                confirmButtonText: 'Close'
            });
        });
    });

    function updateTicketProgress(ticketId) {
        $.ajax({
            url: `/ticket/${ticketId}/progress`,
            type: 'GET',
            success: function (response) {
                const percent = response.progress_percent;
                const total   = response.total_tasks;
                const done    = response.done_tasks;
                const status  = response.status;

                $('.ticket-progress-bar').css('width', percent + '%');
                $('.ticket-progress-text').text(`${percent}% completed (${done}/${total} tasks)`);

                const badge = $('#problemStatusBadge');
                badge.text(status);
                badge.removeClass('bg-warning bg-success bg-secondary')
                    .addClass(status === 'end'
                        ? 'bg-success'
                        : status === 'process'
                            ? 'bg-warning'
                            : 'bg-secondary');
            }
        });
    }

    $(document).on('click', '.deleteTask', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Aufgabe löschen?',
            text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/ticket-tasks/${id}`,
                    method: 'DELETE',
                    data: {_token: csrf},
                    success: function () {
                        loadTasks(problemId);
                    }
                });
            }
        });
    });

    $('#saveTaskForm').off('submit').on('submit', function (e) {
        e.preventDefault();
        const mode = $('#task_mode').val();
        const id   = $('#editing_task_id').val();
        const form = this;

        const formData = new FormData(form);
        formData.set('start_date', $('#task_due_date').val() || '');
        formData.set('add_to_calendar', $('#add_to_calendar_switch').is(':checked') ? '1' : '0');

        let url  = '{{ route("ticketTasks.store") }}';
        let type = 'POST';

        if (mode === 'update' && id) {
            url  = `/ticket-tasks/${id}`;
            type = 'POST';
            formData.set('_method', 'PUT');
        }

        $.ajax({
            url, type,
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $('#taskModal').modal('hide');
                $('#task_mode').val('create');
                $('#editing_task_id').val('');
                form.reset();
                $('#teams').val(null).trigger('change');
                $('#add_to_calendar_switch').prop('checked', false).trigger('change');
                loadTasks(problemId);
            },
            error: function (xhr) {
                Swal.fire(
                    'Fehler',
                    xhr.responseJSON?.message || xhr.responseText || 'Update fehlgeschlagen',
                    'error'
                );
            }
        });
    });

    $(document).on('click', '.openTaskModalBtn', function () {
        $('#task_mode').val('create');
        $('#editing_task_id').val('');
        $('#taskModalLabel span:last-child').text('Neue Aufgabe');
        $('#saveTaskForm')[0].reset();
        $('#teams').val(null).trigger('change');
        $('#add_to_calendar_switch').prop('checked', false).trigger('change');
        $('#task_start_date').val($('#task_due_date').val());
        $('#taskModal').modal('show');
    });

    $(document).on('click', '.editTask', function () {
        const id = $(this).data('id');
        $.get(`/ticket-tasks/${id}`, function (t) {
            $('#task_mode').val('update');
            $('#editing_task_id').val(t.id);
            $('#taskModalLabel span:last-child').text('Aufgabe bearbeiten');

            if (t.error_id) {
                $('#mode_error').prop('checked', true).trigger('change');
                $('#error_id').val(t.error_id);
            } else {
                $('#mode_custom').prop('checked', true).trigger('change');
                $('#title').val(t.title || '');
            }

            $('#task_due_date').val(t.due_date || '');
            $('#task_start_date').val(t.start_date || t.due_date || '');
            $('#priority').val(t.priority || 'Normal');

            const ids = Array.isArray(t.teams) ? t.teams.map(String) : [];
            $('#teams').val(ids).trigger('change');

            const calOn = !!t.add_to_calendar;
            $('#add_to_calendar_switch').prop('checked', calOn).trigger('change');
            if (calOn && t.appointment) {
                $('#calendar_start_time').val(t.appointment.start_time || '');
                $('#calendar_end_time').val(t.appointment.end_time || '');
            } else {
                $('#calendar_start_time').val('');
                $('#calendar_end_time').val('');
            }

            $('#taskModal').modal('show');
        });
    });

    $(document).on('change', '.is-done-checkbox', function () {
        const id = $(this).data('task-id');
        $('#solution_task_id').val(id);
        $('#solutionModal').modal('show');
    });

    $('#saveSolutionForm').submit(function (e) {
        e.preventDefault();
        const id = $('#solution_task_id').val();
        $('#quillSolution').val(quillSolutionEditor.root.innerHTML);

        $.post(`/ticket-tasks/${id}/toggle-done`, {
            _token: csrf,
            solution: $('#quillSolution').val()
        }, function () {
            $('#solutionModal').modal('hide');
            loadTasks(problemId);
            updateTicketProgress(problemId);
        });
    });

    let draggedTaskId = null;
    $(document).on('dragstart', '.task-card', function () {
        draggedTaskId = $(this).data('task-id');
    });

    $('.kanban-column').on('dragover', function (e) {
        e.preventDefault();
    });

    $('.kanban-column').on('drop', function (e) {
        e.preventDefault();
        const newStatus = $(this).data('status');

        if (draggedTaskId) {
            $.post(`/ticket-tasks/${draggedTaskId}/update-status`, {
                status: newStatus,
                _token: csrf
            }, function () {
                updateTicketProgress(problemId);
                if (newStatus === 'Done') {
                    $('#solution_task_id').val(draggedTaskId);
                    $('#solutionModal').modal('show');
                } else {
                    loadTasks(problemId);
                }
            });
        }
    });

    function loadTimeline(ticketId) {
        $.get(`/ticket-tasks/timeline/${ticketId}`, function(notifications) {
            const timeline = $('.timeline').empty();
            notifications.forEach(note => {
                const data = JSON.parse(note.data);
                timeline.append(`
                    <div class="timeline-entry">
                        <h6>${data.title}</h6>
                        <p class="mb-1">${data.message}</p>
                        <div class="time">${new Date(note.created_at).toLocaleString()}</div>
                    </div>
                `);
            });
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Beschreiben Sie das Problem ausführlich...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ color: [] }, { background: [] }],
                [{ align: [] }]
            ]
        }
    });

    const langSelect = document.getElementById('language-select');
    const micButton  = document.getElementById('mic-button');
    let recognition;

    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous    = false;
        recognition.interimResults = false;
        recognition.lang          = langSelect.value;

        langSelect.addEventListener('change', () => {
            recognition.lang = langSelect.value;
        });

        micButton.addEventListener('click', () => {
            recognition.start();
            micButton.classList.add('btn-danger');
            micButton.innerHTML = `<i class="fa fa-microphone-slash"></i> Listening...`;
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            const current    = quill.getText().trim();
            quill.setText(current + '\n' + transcript);
            micButton.classList.remove('btn-danger');
            micButton.innerHTML = `<i class="fa fa-microphone"></i>`;
        };

        recognition.onerror = () => {
            micButton.classList.remove('btn-danger');
            micButton.innerHTML = `<i class="fa fa-microphone"></i>`;
        };
    }

    document.getElementById('serviceReportForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const reportContent = quill.root.innerHTML;
        document.getElementById('hidden-report').value = reportContent;

        const formData = new FormData(this);
        fetch("{{ route('ticket-reports.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                this.reset();
                quill.setContents([]);
                document.getElementById('hidden-report').value = '';
            }
        }).catch(() => {
            Swal.fire('Error', 'Something went wrong.', 'error');
        });
    });

    document.querySelectorAll('.like-button').forEach(btn => {
        btn.addEventListener('click', function () {
            const reportId = this.dataset.reportId;
            fetch(`/ticket-report/${reportId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = `<i class="fa fa-thumbs-up"></i> Liked (${data.likes})`;
                }
            });
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData   = new FormData(this);
            const commentTxt = formData.get('comment');

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Kommentar gepostet', '', 'success');
                    this.reset();
                    const commentBox = this.closest('.collapse');
                    const newComment = document.createElement('div');
                    newComment.classList.add('mt-2', 'text-muted', 'ps-2');
                    newComment.innerHTML = `<strong>You:</strong> ${commentTxt}`;
                    commentBox.appendChild(newComment);
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Could not post comment.', 'error');
            });
        });
    });
});
</script>

<script>
$(document).ready(function () {
    const token          = '{{ csrf_token() }}';
    const ticket_id      = '{{ $problem->id }}';
    const currentUser    = '{{ auth()->user()->name }}';
    const chatBox        = $('#chatBox');
    const commentInput   = $('.comment-input');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': token }
    });

    // Speech recognition for ticket chat
    const micBtn = document.getElementById('mic-btn');
    let recognition;

    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous    = false;
        recognition.interimResults = false;
        recognition.lang          = 'auto';

        micBtn.addEventListener('click', () => {
            recognition.start();
            micBtn.classList.add('text-danger');
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            commentInput.val(commentInput.val() + ' ' + transcript);
        };

        recognition.onend = () => micBtn.classList.remove('text-danger');
    } else {
        micBtn.disabled = true;
    }

    $('.send-comment-btn').click(function () {
        const comment = commentInput.val().trim();
        if (!comment) return;

        $.post('{{ route("comments.store") }}', {
            comment,
            ticket_id,
            _token: token
        }, function () {
            commentInput.val('');
            loadComments();
        });
    });

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `/ticket/comments/${id}`,
            type: 'POST',
            data: {
                _token: token,
                _method: 'DELETE'
            },
            success: function () {
                loadComments();
            }
        });
    });

    $(document).on('click', '.edit-btn', function () {
        const id      = $(this).data('id');
        const current = $(this).data('comment');
        const updated = prompt('Edit your comment:', current);
        if (updated && updated !== current) {
            $.ajax({
                url: `/ticket/comments/${id}`,
                type: 'PUT',
                data: {
                    comment: updated,
                    _token: token,
                    _method: 'PUT'
                },
                success: function () {
                    loadComments();
                }
            });
        }
    });

    function loadComments() {
        $.get(`/ticket/comments/${ticket_id}`, function (comments) {
            chatBox.empty();

            comments.forEach(c => {
                const isReply       = !!c.parent_id;
                const formattedTime = new Date(c.created_at).toLocaleString('en-DE', {
                    day: '2-digit', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                chatBox.append(`
                    <div class="d-flex align-items-start mb-3 ${isReply ? 'ms-5' : ''}" data-id="${c.id}">
                        <img src="/images/employee/${c.employee?.image ?? 'default.jpg'}"
                             class="rounded-circle me-2" width="40" alt="User">
                        <div class="flex-grow-1">
                            <div class="p-2 rounded-3 shadow-sm">
                                <strong>${c.employee?.name ?? 'Unknown'}</strong>
                                <small class="text-muted">• ${formattedTime}</small>
                                <p class="mb-1">${c.comment}</p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary reply-btn" data-id="${c.id}">
                                        <i class="fa fa-reply"></i> Reply
                                    </button>
                                    <button class="btn btn-sm btn-outline-success like-btn" data-id="${c.id}">
                                        <i class="fa fa-thumbs-up"></i>
                                        Like (<span class="like-count">${c.likes ?? 0}</span>)
                                    </button>
                                    ${c.employee?.name === currentUser ? `
                                        <button class="btn btn-sm btn-outline-secondary edit-btn"
                                                data-id="${c.id}"
                                                data-comment="${c.comment}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${c.id}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });
        });
    }

    $(document).on('keypress', '.task-comment', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();

            const textarea  = $(this);
            const taskId    = textarea.data('task-id');
            const taskTitle = textarea.data('task-title') || 'Ohne Titel';
            const ticketId  = textarea.data('ticket-id');
            const employeeId = '{{ auth()->user()->id }}';

            let comment = textarea.val().trim();
            if (!comment) return;

            comment = `@#${taskId} - ${taskTitle}\n` + comment;

            $.post('{{ route("comments.store") }}', {
                ticket_id: ticketId,
                ticket_task_id: taskId,
                employee_id: employeeId,
                comment: comment,
                _token: token
            }, function () {
                textarea.val('');
                Swal.fire({
                    icon: 'success',
                    title: 'Kommentar gespeichert!',
                    toast: true,
                    position: 'top-end',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadComments();
            });
        }
    });

    $(document).on('click', '.like-btn', function () {
        const id     = $(this).data('id');
        const button = $(this);
        $.post(`/ticket/comments/${id}/like`, { _token: token }, function (res) {
            button.find('.like-count').text(res.likes);
        });
    });

    $(document).on('click', '.reply-btn', function () {
        const parentId      = $(this).data('id');
        const parentComment = $(`div[data-id="${parentId}"]`);
        const replyBox      = `
            <div class="input-group my-2">
                <input type="text" class="form-control reply-input" placeholder="Write your reply..." />
                <button class="btn btn-primary send-reply-btn" data-parent="${parentId}">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        `;
        parentComment.after(replyBox);
    });

    $(document).on('click', '.send-reply-btn', function () {
        const parent_id = $(this).data('parent');
        const input     = $(this).closest('.input-group').find('.reply-input');
        const comment   = input.val().trim();
        if (!comment) return;

        $.post('{{ route("comments.store") }}', {
            comment, ticket_id, parent_id, _token: token
        }, function () {
            loadComments();
        });
    });

    loadComments();
});
</script>

<script>
    $('.error-type-chip').on('click', function () {
        const selectedType = $(this).data('error-type');
        const ticketId     = {{ $problem->id }};
        const btn          = $(this);

        $.post(`/ticket/${ticketId}/update-type`, {
            error_type: selectedType,
            _token: '{{ csrf_token() }}'
        }, function () {
            $('.badge-error-type').text(btn.text().trim());

            $('.error-type-chip')
                .removeClass('active');

            btn.addClass('active');

            Swal.fire({
                title: 'Aktualisiert!',
                text: 'Der Tickettyp wurde geändert.',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            });
        });
    });
</script>

<script>
function updateTaskStatus(taskId, newStatus) {
    fetch('/tasks/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            task_id: taskId,
            status: newStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.querySelector('#problemStatusBadge').textContent = data.problem_status;
        }
    })
    .catch(err => console.error('Status update error:', err));
}
</script>

@endsection
