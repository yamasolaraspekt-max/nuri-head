@extends('admin.layouts.app')

@section('title') MITARBEITERPROFIL @endsection

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --ep-bg: #f3f4f6;
            --ep-card-bg: #ffffff;
            --ep-border: #e5e7eb;
            --ep-primary: var(--sa-accent);
            --ep-primary-soft: var(--sa-accent-light);
            --ep-muted: #6b7280;
            --ep-heading: #111827;
            --ep-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            --ep-radius: 18px;
        }

        .app-content .content-wrapper {
            background: var(--ep-bg);
            border-radius: 20px;
            padding-top: 1.5rem;
        }

        .content-header-title {
            font-weight: 600;
            letter-spacing: .12em;
        }

        .breadcrumb .breadcrumb-item a,
        .breadcrumb .breadcrumb-item.active {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .ep-card {
            background: var(--ep-card-bg);
            border-radius: var(--ep-radius);
            border: 1px solid var(--ep-border);
            box-shadow: var(--ep-shadow);
            padding: 1.5rem 1.75rem;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ep-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .ep-card-title {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--ep-muted);
        }

        .ep-card-title-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--ep-primary);
        }

        .ep-card-body {
            flex: 1;
        }

        .ep-card-footer-link {
            font-size: 0.78rem;
            font-weight: 500;
            color: #4b5563;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .ep-card-footer-link:hover {
            color: var(--ep-primary);
            text-decoration: none;
        }

        .ep-card-footer-link::before {
            content: "›";
            font-size: 0.9rem;
            margin-top: -1px;
        }

        .ep-edit-btn {
            position: absolute;
            top: 1.1rem;
            right: 1.1rem;
        }

        .ep-edit-btn .btn {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ep-card-profile {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }

        @media (max-width: 991.98px) {
            .ep-card-profile {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }

        .ep-avatar-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .ep-avatar-img {
            width: 150px;
            height: 150px;
            border-radius: 999px;
            object-fit: cover;
            box-shadow: 0 0 0 4px var(--ep-primary-soft);
        }

        .ep-profile-main {
            flex: 1;
        }

        .ep-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--ep-heading);
        }

        .ep-meta-line {
            font-size: 0.82rem;
            color: var(--ep-muted);
        }

        .ep-tag {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--ep-primary-soft);
            color: var(--ep-primary);
        }

        .ep-keylist {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            font-size: 0.85rem;
        }

        .ep-keylist li {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .ep-keylist-label {
            color: var(--ep-muted);
            min-width: 120px;
        }

        .ep-keylist-value {
            color: #111827;
            font-weight: 500;
            text-align: right;
        }

        .ep-keylist-value span {
            display: block;
        }

        .ep-pill-metric {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 0.35rem 0.6rem;
            border-radius: 0.65rem;
            background: #f3f4f6;
            font-size: 0.74rem;
            margin-bottom: 0.25rem;
        }

        .ep-pill-metric span:first-child {
            font-size: 0.7rem;
            color: var(--ep-muted);
        }

        .ep-pill-metric span:last-child {
            font-weight: 600;
            color: #111827;
        }

        .ep-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.4rem;
        }

        .ep-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }

        .ep-table thead th {
            border-bottom: 1px solid #e5e7eb;
            padding: 0.35rem 0.35rem 0.5rem;
            font-weight: 600;
            color: var(--ep-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.75rem;
            background: #f9fafb;
        }

        .ep-table tbody td {
            border-bottom: 1px solid #f1f5f9;
            padding: 0.4rem 0.35rem;
            vertical-align: top;
        }

        .ep-chip-muted {
            display: inline-flex;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            font-size: 0.72rem;
            background: #f3f4f6;
            color: #4b5563;
        }

        .ep-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.18rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            background: #eff6ff;
            color: #1d4ed8;
            margin-top: 0.15rem;
        }

        .ep-badge i {
            font-size: 0.85rem;
        }

        .ep-skills-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .ep-skills-list li {
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 0.8rem;
            color: #111827;
        }

        /* Calendar card */
        .ep-calendar-card {
            background: var(--ep-card-bg);
            border-radius: var(--ep-radius);
            border: 1px solid var(--ep-border);
            box-shadow: var(--ep-shadow);
            padding: 1rem 1.25rem 0.6rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .ep-calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--ep-border);
        }

        .ep-calendar-header-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--ep-muted);
            font-weight: 600;
        }

        .ep-calendar-header-sub {
            font-size: 0.78rem;
            color: #9ca3af;
        }

        #calendar {
            max-width: 100%;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 0.35rem !important;
        }

        .fc-toolbar-title {
            font-size: 0.85rem !important;
            color: #4b5563 !important;
            text-transform: capitalize;
        }

        .fc-button {
            background: var(--ep-primary) !important;
            border: 0 !important;
            border-radius: 999px !important;
            width: 30px;
            height: 30px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .fc-button .fc-icon {
            font-size: 1.25rem;
            top: 0 !important;
        }

        .fc-col-header-cell {
            text-transform: uppercase;
            font-weight: 600;
            color: var(--ep-muted);
            border: 0 !important;
            padding: 4px 0 !important;
            font-size: 0.75rem;
        }

        .fc-scrollgrid {
            border: 0 !important;
        }

        .fc-daygrid-day {
            border: 0 !important;
            padding: 0 !important;
        }

        .fc-daygrid-day-frame {
            align-content: center !important;
            padding: 1px !important;
        }

        .fc-daygrid-day-top {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .fc-day-sat .fc-daygrid-day-top,
        .fc-day-sun .fc-daygrid-day-top {
            color: #cbd5f5 !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            position: relative;
            padding: 0 6px !important;
            border-radius: 999px;
            border: 1px solid #a2a2a2;
            color: var(--ep-primary) !important;
        }

        .fc-daygrid-day-number {
            position: relative;
            padding: 5px;
        }

        .fc-daygrid-day-number::before {
            content: '';
            display: none;
        }

        .fc-daygrid-day-number[data-event='true']::before {
            display: block;
            background-color: var(--event-bg-color, var(--ep-primary));
            position: absolute;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            z-index: -1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .events-list {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--ep-border);
            max-height: 220px;
            overflow-y: auto;
        }

        .event-line {
            border-top: 1px solid #e5e7eb;
            margin: 0.4rem 0;
        }

        .card-event {
            display: flex;
            align-items: flex-start;
            padding: 0.25rem 0;
            gap: 0.75rem;
        }

        .card-event .time {
            text-align: right;
            color: var(--ep-muted);
            font-size: 0.8rem;
            min-width: 72px;
        }

        .card-event .time .date {
            font-weight: 500;
        }

        .card-event .time .hour {
            font-size: 0.8rem;
        }

        .card-event .separator {
            width: 3px;
            border-radius: 999px;
            background-color: #e5e7eb;
            min-height: 42px;
        }

        .card-event .details {
            flex: 1;
            text-align: left;
        }

        .card-event .details .title a {
            font-size: 0.85rem;
            font-weight: 600;
            color: #111827;
        }

        .card-event .details .title a:hover {
            color: var(--ep-primary);
        }

        .card-event .details .description {
            margin-top: 0.2rem;
        }

        .users-list li img {
            box-shadow: none !important;
        }

        .ep-permission-block {
            margin-top: 0.75rem;
            border-top: 1px dashed #e5e7eb;
            padding-top: 0.75rem;
        }

        .ep-permission-module-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .ep-permission-chip {
            display: inline-flex;
            padding: 0.1rem 0.5rem;
            margin: 0 0.25rem 0.25rem 0;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 0.72rem;
            color: #4b5563;
        }

        .ep-role-chip {
            display: inline-flex;
            padding: 0.1rem 0.5rem;
            margin-right: 0.3rem;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.75rem;
        }

        .select2-dropdown {
            width: 220px !important;
        }

        .select2-container--default .select2-selection--single {
            border: 0 !important;
            background-color: transparent !important;
            text-align: right !important;
        }

        .select2-selection__arrow {
            display: none;
        }

        /* --- PERSONAL NOTES ("Windows sticky") ---------------------------------- */
        .ep-notes-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .ep-note-add-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .ep-note-add-row input[type="text"],
        .ep-note-add-row textarea,
        .ep-note-add-row select {
            font-size: 0.8rem;
        }

        .ep-note-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .ep-note {
            width: 210px;
            min-height: 120px;
            border-radius: 16px;
            padding: 0.75rem;
            background: #fef9c3;
            position: relative;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            font-size: 0.8rem;
        }

        .ep-note.done {
            opacity: 0.6;
            text-decoration: line-through;
        }

        .ep-note-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .ep-note-body {
            font-size: 0.78rem;
            max-height: 80px;
            overflow: hidden;
            white-space: pre-line;
        }

        .ep-note-meta {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.35rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ep-note-color-pill {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 0.25rem;
        }

        .ep-note-actions {
            position: absolute;
            top: 6px;
            right: 6px;
            display: flex;
            gap: 0.25rem;
        }

        .ep-note-actions button {
            border: 0;
            background: transparent;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0;
        }

                /* --- EFFICIENCY GAUGE ------------------------------------------------ */
        .ep-profile-main {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .ep-efficiency-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: stretch;
            margin-top: 0.5rem;
        }

        .ep-eff-gauge-box {
            flex: 0 0 180px;
            max-width: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ep-eff-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.35rem;
            align-self: flex-start;
        }

        .ep-eff-gauge-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(#e5e7eb 0deg, #e5e7eb 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 0 6px #e5f0ff;
        }

        .ep-eff-gauge-inner {
            width: 104px;
            height: 104px;
            border-radius: 50%;
            background: #e3f9d4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .ep-eff-gauge-inner::before {
            content: "";
            position: absolute;
            width: 160%;
            height: 160%;
            background: radial-gradient(circle at 50% 0, rgba(255,255,255,0.9), transparent 60%),
                        linear-gradient(180deg, #c4f1b4 0%, #90d46a 60%, #74b83e 100%);
            top: 40%;
            left: -30%;
            transform: translateY(0);
            animation: ep-water 4s ease-in-out infinite alternate;
        }

        @keyframes ep-water {
            0%   { transform: translateY(10%); }
            100% { transform: translateY(-6%); }
        }

        .ep-eff-gauge-inner span {
            position: relative;
            z-index: 2;
        }

        .ep-eff-percent {
            font-size: 1.8rem;
            font-weight: 700;
            color: #111827;
        }

        .ep-eff-percent-symbol {
            font-size: 0.9rem;
            color: #4b5563;
            margin-left: 2px;
        }

        .ep-eff-small-text {
            font-size: 0.72rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .ep-eff-tiles {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
            min-width: 220px;
        }

        .ep-eff-tile {
            border-radius: 18px;
            padding: 0.75rem 0.95rem;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.15);
        }

        .ep-eff-tile span {
            display: block;
        }

        .ep-eff-tile-title {
            font-size: 0.78rem;
            opacity: 0.9;
        }

        .ep-eff-tile-sub {
            font-size: 0.7rem;
            opacity: 0.85;
        }

        .ep-eff-tile-value {
            font-size: 1.6rem;
            font-weight: 600;
            text-align: right;
        }

        .ep-eff-tile-blue {
            background: linear-gradient(135deg, #4facfe, #35a0ff);
        }

        .ep-eff-tile-green {
            background: linear-gradient(135deg, #a0e636, #6ac322);
        }

        .ep-eff-tile-red {
            background: linear-gradient(135deg, #ff6b6b, #f43f5e);
        }

        .ep-eff-tile-muted {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        @media (max-width: 991.98px) {
            .ep-efficiency-wrap {
                flex-direction: column;
            }
            .ep-eff-tile-value {
                font-size: 1.4rem;
            }
        }

                .ep-note-tabs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--ep-border);
        }

        .ep-note-tab {
            border: none;
            background: transparent;
            outline: 0;
            cursor: pointer;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--ep-muted);
            padding: 0.25rem 0.85rem;
            border-radius: 999px 999px 0 0;
            position: relative;
            top: 1px;
        }

        .ep-note-tab-count {
            font-weight: 600;
            margin-left: 0.25rem;
        }

        .ep-note-tab.is-active {
            color: #111827;
            background: #ffffff;
            box-shadow: 0 -1px 0 #ffffff inset;
        }

        .ep-note-grid-group.d-none {
            display: none !important;
        }


    </style>
@endsection

@section('content')
    <div class="app-content"> 

        <div class="content-wrapper">
             
            <a href="{{ url('employee_cv/' . $data->id) }}"
               class="btn btn-primary mr-1 mb-2 waves-effect waves-light">
                <i class="feather icon-printer"></i> Lebenslauf
            </a>

            <div class="content-body">
                <div class="row">
                    <!-- LEFT COLUMN -->
                    <div class="col-xl-8 col-lg-7 col-md-12">
                        <div class="row">
                            <!-- PROFILE HEADER -->
                            <div class="col-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Profil</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="ep-card-profile">
                                            <div>
                                                <div class="ep-avatar-wrapper">
                                                    @if($data->gender == "Male" && !$data->image)
                                                        <img src="{{ asset('images/gender/male.png') }}" class="ep-avatar-img" alt="profile image">
                                                    @elseif($data->gender == "Female" && !$data->image)
                                                        <img src="{{ asset('images/gender/Female.png') }}" class="ep-avatar-img" alt="profile image">
                                                    @else
                                                        <img src="{{ asset('images/employee/' . $data->image) }}" class="ep-avatar-img" alt="profile image">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="ep-profile-main">
                                                {{-- Basic identity --}}
                                                <div>
                                                    <div class="ep-name">{{ $data->name }} {{ $data->lastname }}</div>
                                                    <div class="ep-meta-line mt-25">
                                                        {{ $data->branch ?? '' }}
                                                    </div>
                                                    <div class="ep-meta-line">
                                                        {{ $data->contract_type ?? '' }}
                                                    </div>

                                                    <div class="mt-50">
                                                        <span class="ep-tag">{{ $data->status ?? '–' }}</span>
                                                    </div>

                                                    <div class="mt-50" style="font-size:0.8rem;color:#6b7280;">
                                                        @if($data->dob)
                                                            Geboren am {{ \Carbon\Carbon::parse($data->dob)->isoFormat('DD.MM.YYYY') }}
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Efficiency area --}}
                                                <div class="ep-efficiency-wrap">
                                                    <div class="ep-eff-gauge-box">
                                                        <div class="ep-eff-label">Effizienz</div>
                                                        <div class="ep-eff-gauge-circle" id="epEffGauge">
                                                            <div class="ep-eff-gauge-inner">
                                                                <span class="ep-eff-percent" id="epEffPercent">--</span>
                                                                <span class="ep-eff-percent-symbol">%</span>
                                                                <div class="ep-eff-small-text" id="epEffRange">Letzte 30 Tage</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="ep-eff-tiles">
                                                        <div class="ep-eff-tile ep-eff-tile-blue">
                                                            <span class="ep-eff-tile-title">Total in Arbeit</span>
                                                            <span class="ep-eff-tile-sub">Aufgaben & Termine</span>
                                                            <span class="ep-eff-tile-value" id="epEffTotalInProgress">0</span>
                                                        </div>

                                                        <div class="ep-eff-tile ep-eff-tile-green">
                                                            <span class="ep-eff-tile-title">Abgeschlossen</span>
                                                            <span class="ep-eff-tile-sub">Tasks & Tickets</span>
                                                            <span class="ep-eff-tile-value" id="epEffTasksCompleted">0</span>
                                                        </div>

                                                        <div class="ep-eff-tile ep-eff-tile-red">
                                                            <span class="ep-eff-tile-title">Mit Einwänden</span>
                                                            <span class="ep-eff-tile-sub">Objections / Probleme</span>
                                                            <span class="ep-eff-tile-value" id="epEffTasksObjections">0</span>
                                                        </div>

                                                        <div class="ep-eff-tile ep-eff-tile-muted">
                                                            <span class="ep-eff-tile-title">Aktive Tage</span>
                                                            <span class="ep-eff-tile-sub" id="epEffActiveDaysLabel">0 von 30</span>
                                                            <span class="ep-eff-tile-value" id="epEffActiveDays">0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> {{-- /.ep-profile-main --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <!-- PERSONAL NOTES -->
                                                        <!-- PERSONAL NOTES -->
                            <div class="col-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Persönliche Notizen</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="ep-notes-wrap">
                                            {{-- Add note --}}
                                            <form method="POST"
                                                action="{{ route('employee-notes.store', $data->id) }}"
                                                class="ep-note-add-row">
                                                @csrf

                                                <input type="text"
                                                    name="title"
                                                    class="form-control form-control-sm"
                                                    placeholder="Titel (optional)">

                                                <input type="text"
                                                    name="note"
                                                    class="form-control form-control-sm"
                                                    placeholder="Kurze Notiz">

                                                <select name="category_id"
                                                        class="form-control form-control-sm"
                                                        style="max-width: 160px;">
                                                    <option value="">Kategorie</option>
                                                    @foreach($noteCategories as $cat)
                                                        <option value="{{ $cat->id }}">
                                                            {{ $cat->category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit"
                                                        class="btn btn-sm btn-primary">
                                                    + Notiz
                                                </button>
                                            </form>

                                            @php
$openNotes = $personalNotes->filter(function ($n) {
    return !$n->is_done;
});
$doneNotes = $personalNotes->filter(function ($n) {
    return $n->is_done;
});
                                            @endphp

                                            {{-- Tabs --}}
                                            <div class="ep-note-tabs">
                                                <button type="button"
                                                        class="ep-note-tab is-active"
                                                        data-target="epNotesOpen">
                                                    Offen
                                                    <span class="ep-note-tab-count">{{ $openNotes->count() }}</span>
                                                </button>

                                                <button type="button"
                                                        class="ep-note-tab"
                                                        data-target="epNotesDone">
                                                    Erledigt
                                                    <span class="ep-note-tab-count">{{ $doneNotes->count() }}</span>
                                                </button>
                                            </div>

                                            {{-- OPEN notes --}}
                                            <div id="epNotesOpen" class="ep-note-grid-group">
                                                <div class="ep-note-grid">
                                                    @forelse($openNotes as $note)
                                                        @php
    $bgColor = $note->color ?: ($note->category_color ?: '#fef9c3');
                                                        @endphp
                                                        <div class="ep-note"
                                                            style="background: {{ $bgColor }};">
                                                            <div class="ep-note-actions">
                                                                {{-- toggle done --}}
                                                                <form method="POST"
                                                                    action="{{ route('employee-notes.toggle-done', $note->id) }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" title="Als erledigt markieren">
                                                                        ✔
                                                                    </button>
                                                                </form>

                                                                {{-- delete --}}
                                                                <form method="POST"
                                                                    action="{{ route('employee-notes.destroy', $note->id) }}"
                                                                    onsubmit="return confirm('Notiz löschen?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="Löschen">
                                                                        ✕
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <div class="ep-note-title">
                                                                {{ $note->title ?: 'Neue Notiz' }}
                                                            </div>

                                                            <div class="ep-note-body">
                                                                {{ $note->note }}
                                                            </div>

                                                            <div class="ep-note-meta">
                                                                <div>
                                                                    @if($note->category_name)
                                                                        <span class="ep-note-color-pill"
                                                                            style="background: {{ $note->category_color ?: '#fbbf24' }}"></span>
                                                                        {{ $note->category_name }}
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    @if($note->deadline)
                                                                        Fällig: {{ \Carbon\Carbon::parse($note->deadline)->isoFormat('DD.MM.YYYY') }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div style="font-size:0.8rem;color:#6b7280;">
                                                            Noch keine offenen Notizen.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                            {{-- DONE notes --}}
                                            <div id="epNotesDone" class="ep-note-grid-group d-none">
                                                <div class="ep-note-grid">
                                                    @forelse($doneNotes as $note)
                                                        @php
    $bgColor = $note->color ?: ($note->category_color ?: '#e5e7eb');
                                                        @endphp
                                                        <div class="ep-note done"
                                                            style="background: {{ $bgColor }};">
                                                            <div class="ep-note-actions">
                                                                {{-- toggle back to open --}}
                                                                <form method="POST"
                                                                    action="{{ route('employee-notes.toggle-done', $note->id) }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" title="Wieder öffnen">
                                                                        ↺
                                                                    </button>
                                                                </form>

                                                                {{-- delete --}}
                                                                <form method="POST"
                                                                    action="{{ route('employee-notes.destroy', $note->id) }}"
                                                                    onsubmit="return confirm('Notiz löschen?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="Löschen">
                                                                        ✕
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <div class="ep-note-title">
                                                                {{ $note->title ?: 'Erledigte Notiz' }}
                                                            </div>

                                                            <div class="ep-note-body">
                                                                {{ $note->note }}
                                                            </div>

                                                            <div class="ep-note-meta">
                                                                <div>
                                                                    @if($note->category_name)
                                                                        <span class="ep-note-color-pill"
                                                                            style="background: {{ $note->category_color ?: '#9ca3af' }}"></span>
                                                                        {{ $note->category_name }}
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    @if($note->done_date)
                                                                        Erledigt: {{ \Carbon\Carbon::parse($note->done_date)->isoFormat('DD.MM.YYYY') }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div style="font-size:0.8rem;color:#6b7280;">
                                                            Noch keine erledigten Notizen.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PERSONAL DATA + LEAVE / SICKNESS -->
                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Persönliche Daten</span>
                                        </div>

                                        @if($data->id == auth()->id())
                                            <div class="ep-edit-btn">
                                                <button type="button"
                                                        class="btn btn-flat-primary waves-effect waves-light"
                                                        data-toggle="modal"
                                                        data-target="#profile">
                                                    <i class="feather icon-edit"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ep-card-body">
                                        <ul class="ep-keylist">
                                            <li>
                                                <span class="ep-keylist-label">Telefon</span>
                                                <span class="ep-keylist-value">{{ $data->home_phone ?: '–' }}</span>
                                            </li>
                                            <li>
                                                <span class="ep-keylist-label">Handy</span>
                                                <span class="ep-keylist-value">{{ $data->phone ?: '–' }}</span>
                                            </li>
                                            <li>
                                                <span class="ep-keylist-label">E-Mail</span>
                                                <span class="ep-keylist-value">{{ $data->email ?: '–' }}</span>
                                            </li>
                                            <li>
                                                <span class="ep-keylist-label">Angestellt seit</span>
                                                <span class="ep-keylist-value">
                                                    {{ \Carbon\Carbon::parse($data->created_at)->isoFormat('DD.MM.YYYY') }}
                                                </span>
                                            </li>
                                            @if(isset($department) && $department->count())
                                                <li>
                                                    <span class="ep-keylist-label">Abteilung</span>
                                                    <span class="ep-keylist-value">
                                                        {{ $department->pluck('department_name')->join(', ') }}
                                                    </span>
                                                </li>
                                            @endif
                                            @if(isset($language) && $language->count())
                                                <li>
                                                    <span class="ep-keylist-label">Sprachen</span>
                                                    <span class="ep-keylist-value">
                                                        {{ $language->pluck('language')->join(', ') }}
                                                    </span>
                                                </li>
                                            @endif
                                            <li>
                                                <span class="ep-keylist-label">Kundenbeteiligung</span>
                                                <span class="ep-keylist-value">
                                                    {{ $customerInvolvementCount ?? 0 }} Kunden
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Urlaub / Krankheit</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="ep-pill-row">
                                            <div class="ep-pill-metric">
                                                <span>Gesamte Urlaubstage</span>
                                                <span>{{ $leaveStats['total_leave_days'] ?? 0 }}</span>
                                            </div>
                                            <div class="ep-pill-metric">
                                                <span>Krankheitstage gesamt</span>
                                                <span>{{ $leaveStats['total_sick_days'] ?? 0 }}</span>
                                            </div>
                                            <div class="ep-pill-metric">
                                                <span>Bevorstehende Abwesenheiten</span>
                                                <span>
                                                    {{ $leaveStats['upcoming_count'] ?? 0 }}
                                                    @if(($leaveStats['upcoming_leave_days'] ?? 0) > 0)
                                                        ({{ $leaveStats['upcoming_leave_days'] }} Tage)
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <ul class="ep-keylist mt-50">
                                            @if($latestLeave ?? false)
                                                <li>
                                                    <span class="ep-keylist-label">Letzter Urlaub</span>
                                                    <span class="ep-keylist-value">
                                                        {{ \Carbon\Carbon::parse($latestLeave->start_date)->isoFormat('DD.MM.YYYY') }}
                                                        – {{ \Carbon\Carbon::parse($latestLeave->end_date)->isoFormat('DD.MM.YYYY') }}
                                                    </span>
                                                </li>
                                            @endif

                                            @if($nextLeave ?? false)
                                                <li>
                                                    <span class="ep-keylist-label">Nächster Urlaub</span>
                                                    <span class="ep-keylist-value">
                                                        {{ \Carbon\Carbon::parse($nextLeave->start_date)->isoFormat('DD.MM.YYYY') }}
                                                        – {{ \Carbon\Carbon::parse($nextLeave->end_date)->isoFormat('DD.MM.YYYY') }}
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- QUALIFICATION + COMPETENCES -->
                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Qualifikation</span>
                                        </div>

                                        @if($data->id == auth()->id())
                                            <div class="ep-edit-btn">
                                                <button type="button"
                                                        class="btn btn-flat-primary waves-effect waves-light"
                                                        data-toggle="modal"
                                                        data-target="#qualificationModal">
                                                    <i class="feather icon-edit"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="table-responsive">
                                            <table class="ep-table">
                                                <thead>
                                                <tr>
                                                    <th>Abschluss</th>
                                                    <th>Institution</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($qualifications as $qualification)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $qualification->degree }}</strong>
                                                            @if($qualification->major)
                                                                <br>
                                                                <span class="ep-chip-muted">{{ $qualification->major }}</span>
                                                            @endif
                                                            <br>
                                                            <span class="text-muted">
                                                                {{ \Carbon\Carbon::parse($qualification->q_start_year)->isoFormat('YYYY') }}
                                                                –
                                                                {{ \Carbon\Carbon::parse($qualification->q_end_year)->isoFormat('YYYY') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $qualification->institution }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div>
                                        <a href="#" class="ep-card-footer-link">Mehr anzeigen</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Kompetenzen</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <ul class="ep-skills-list">
                                            @foreach ($feducation as $fedu)
                                                <li>{{ $fedu->skill }}</li>
                                            @endforeach
                                            @foreach ($otherskill as $oskill)
                                                <li>{{ $oskill->skills }} ({{ $oskill->proficiency }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- POSITIONS + HANDOVER -->
                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Positionen & Abteilungen</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="table-responsive">
                                            <table class="ep-table">
                                                <thead>
                                                    <tr>
                                                        <th>Abteilung</th>
                                                        <th>Position</th>
                                                        <th>FTE %</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($roleAllocations as $alloc)
                                                        <tr>
                                                            <td>{{ $alloc->department_name ?? '–' }}</td>
                                                            <td>
                                                                {{ $alloc->position ?? '–' }}
                                                                @if($alloc->main === 'yes' || $alloc->main === 1)
                                                                    <br><span class="ep-chip-muted">Hauptfunktion</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(!is_null($alloc->percent))
                                                                    {{ number_format($alloc->percent, 0) }} %
                                                                @else
                                                                    –
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3">Keine Zuordnung gefunden.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Übergabeliste</span>
                                        </div>
                                    </div>

                                    <div class="ep-card-body">
                                        <div class="table-responsive">
                                            <table class="ep-table">
                                                <tbody>
                                                @foreach($handover as $item)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $item->item }}</strong>
                                                            <br>
                                                            <span class="ep-badge">
                                                                <i class="feather icon-hash"></i>
                                                                Seriennummer: {{ $item->serial_no }}
                                                            </span>
                                                            <br>
                                                            <span class="ep-badge mt-25">
                                                                <i class="feather icon-package"></i>
                                                                Artikelnummer: {{ $item->article_no }}
                                                            </span>
                                                        </td>
                                                        <td style="text-align:right; white-space:nowrap;">
                                                            x {{ $item->quantity }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if($handover->isEmpty())
                                                    <tr>
                                                        <td colspan="2">Keine Übergaben erfasst.</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RECURRING LEAVES -->
                            <div class="col-12 mb-1">
                                <div class="ep-card">
                                    <div class="ep-card-header">
                                        <div class="ep-card-title">
                                            <span class="ep-card-title-dot"></span>
                                            <span>Wiederkehrende Abwesenheiten</span>
                                        </div>
                                    </div>
                                    <div class="ep-card-body">
                                        @forelse($recurringLeaves as $rl)
                                            @php
    $weekdays = [];
    if ($rl->weekdays) {
        $raw = json_decode($rl->weekdays, true);
        $names = [
            1 => 'Mo',
            2 => 'Di',
            3 => 'Mi',
            4 => 'Do',
            5 => 'Fr',
            6 => 'Sa',
            0 => 'So',
            7 => 'So'
        ];
        foreach ($raw as $w) {
            $weekdays[] = $names[$w] ?? $w;
        }
    }
                                            @endphp
                                            <div style="margin-bottom:0.75rem;">
                                                <strong>{{ $rl->title ?: 'Serien-Abwesenheit' }}</strong>
                                                <div style="font-size:0.8rem;color:#6b7280;">
                                                    {{ ucfirst($rl->type) }}
                                                    @if($rl->frequency)
                                                        • {{ $rl->frequency }}
                                                    @endif
                                                    @if(count($weekdays))
                                                        • {{ implode(', ', $weekdays) }}
                                                    @endif
                                                </div>
                                                <div style="font-size:0.78rem;color:#6b7280;">
                                                    Von {{ \Carbon\Carbon::parse($rl->start_date)->isoFormat('DD.MM.YYYY') }}
                                                    @if($rl->end_date)
                                                        bis {{ \Carbon\Carbon::parse($rl->end_date)->isoFormat('DD.MM.YYYY') }}
                                                    @else
                                                        (offen)
                                                    @endif
                                                </div>
                                                @if(!$rl->all_day && $rl->start_time && $rl->end_time)
                                                    <div style="font-size:0.78rem;color:#6b7280;">
                                                        {{ \Illuminate\Support\Str::substr($rl->start_time, 0, 5) }} – {{ \Illuminate\Support\Str::substr($rl->end_time, 0, 5) }} Uhr
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div style="font-size:0.8rem;color:#6b7280;">
                                                Keine wiederkehrenden Abwesenheiten hinterlegt.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: CALENDAR + PERMISSIONS -->
                    <div class="col-xl-4 col-lg-5 col-md-12">
                        <div class="ep-calendar-card">
                            <div class="ep-calendar-header">
                                <div>
                                    <div class="ep-calendar-header-title">Arbeitskalender</div>
                                    <div class="ep-calendar-header-sub">Aufgaben & Termine</div>
                                </div>
                            </div>

                            <input type="hidden" id="emp_id" value="{{ $data->id }}">

                            <div id="calendar"></div>

                            <div class="events-list">
                                <!-- Filled by JS -->
                            </div>

                            <!-- ROLES & PERMISSIONS -->
                            <div class="ep-permission-block">
                                <div class="ep-calendar-header-title" style="border-bottom:none;">
                                    Rollen & Berechtigungen
                                </div>
                                <div class="mt-50">
                                    @if(($userRoles ?? collect())->count())
                                        @foreach($userRoles as $roleName)
                                            <span class="ep-role-chip">{{ $roleName }}</span>
                                        @endforeach
                                    @else
                                        <span style="font-size:0.78rem;color:#9ca3af;">Keine Rollen zugewiesen.</span>
                                    @endif
                                </div>

                                @if(isset($userPermissionsByModule) && $userPermissionsByModule->count())
                                    @foreach($userPermissionsByModule as $module => $perms)
                                        <div class="mt-50">
                                            <div class="ep-permission-module-title">
                                                {{ $module }}
                                            </div>
                                            <div>
                                                @foreach($perms as $permLabel)
                                                    <span class="ep-permission-chip">
                                                        {{ $permLabel }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="mt-50" style="font-size:0.78rem;color:#9ca3af;">
                                        Keine Berechtigungen gefunden oder nicht konfiguriert.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div> {{-- .row --}}
            </div>
        </div>

        {{-- MODALS --}}
        {{-- Qualification Modal --}}
        <div class="modal fade text-left" id="qualificationModal" tabindex="-1" role="dialog" aria-labelledby="qualificationLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width:1143px !important">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="qualificationLabel">Qualifikation</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form novalidate
                          action="{{ route('emp.qualification') }}"
                          method="post"
                          class="custom-file-upload"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table" id="qualification_table">
                                    <thead>
                                    <tr>
                                        <th>Degree</th>
                                        <th>Datum</th>
                                        <th>#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <input type="hidden" name="qual[0][emp_id]" value="{{ $data->id }}">
                                        <td>
                                            <input type="text" class="form-control mb-1 required" placeholder="Degree" name="qual[0][degree]">
                                            <input type="text" class="form-control mb-1 required" placeholder="Major" name="qual[0][major]">
                                            <input type="text" class="form-control mb-1 required" placeholder="Institution" name="qual[0][institution]">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control mb-1" placeholder="Grade" name="qual[0][grade]">
                                            <input type="date" class="form-control mb-1 required" placeholder="Startjahr" name="qual[0][q_start_year]">
                                            <input type="date" class="form-control mb-1 required" placeholder="Abschlussdatum" name="qual[0][q_end_year]">
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"
                                                    id="add_qualification">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Profile Modal --}}
        <div class="modal fade text-left" id="profile" tabindex="-1" role="dialog" aria-labelledby="profileLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="profileLabel">Persönliche Daten</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form method="post" action="#">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="email">E-Mail</label>
                                        <input type="text" name="email" value="{{ old('email', $data->email) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="phone">Handy</label>
                                        <input type="text" name="phone" value="{{ old('phone', $data->phone) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="home_phone">Telefon privat</label>
                                        <input type="text" name="home_phone" value="{{ old('home_phone', $data->home_phone) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="work_phone">Telefon geschäftlich</label>
                                        <input type="text" name="work_phone" value="{{ old('work_phone', $data->work_phone) }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    {{-- Dynamic qualification rows --}}
    <script>
        (function () {
            let i = 0;
            const addBtn = document.getElementById('add_qualification');
            const table = document.getElementById('qualification_table');

            if (addBtn && table) {
                addBtn.addEventListener('click', function () {
                    i++;
                    const tbody = table.querySelector('tbody');
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <input type="hidden" name="qual[${i}][emp_id]" value="{{ $data->id }}">
                        <td>
                            <input type="text" class="form-control mb-1 required" placeholder="Degree" name="qual[${i}][degree]">
                            <input type="text" class="form-control mb-1 required" placeholder="Major" name="qual[${i}][major]">
                            <input type="text" class="form-control mb-1 required" placeholder="Institution" name="qual[${i}][institution]">
                        </td>
                        <td>
                            <input type="text" class="form-control mb-1" placeholder="Grade" name="qual[${i}][grade]">
                            <input type="date" class="form-control mb-1 required" placeholder="Startjahr" name="qual[${i}][q_start_year]">
                            <input type="date" class="form-control mb-1 required" placeholder="Abschlussdatum" name="qual[${i}][q_end_year]">
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon rounded-circle btn-outline-danger remove_qualification">
                                <i class="feather icon-minus"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                table.addEventListener('click', function (e) {
                    if (e.target.closest('.remove_qualification')) {
                        e.target.closest('tr').remove();
                    }
                });
            }
        })();
    </script>

    {{-- Calendar + events --}}
    <script>
        $(document).ready(function () {
            feather.replace();

            const calendarEl = document.getElementById('calendar');
            const empIdInput = document.getElementById('emp_id');

            if (!calendarEl || !empIdInput) {
                return;
            }

            const empId = empIdInput.value.trim();

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                selectMirror: false,
                height: 'auto',
                locale: 'de',
                firstDay: 1,
                buttonText: { today: 'Heute' },
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                dateClick: function (info) {
                    document.querySelectorAll('.fc-day-selected').forEach(day => day.classList.remove('fc-day-selected'));
                    info.dayEl.classList.add('fc-day-selected');
                    loadEventsForDate(info.dateStr);
                },
                dayHeaderContent: function (args) {
                    return args.text.charAt(0);
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: '/get_employee_calendar/' + empId,
                        method: 'GET',
                        success: function (response) {
                            const events = (response.data || []).map(item => ({
                                id: item.id,
                                title: `${item.type}: ${item.title}`,
                                start: item.start_date,
                                end: item.end_date ? item.end_date : item.start_date,
                                backgroundColor: item.taskColor,
                                borderColor: item.taskColor,
                                extendedProps: {
                                    employees: item.employees,
                                    type: item.type,
                                    priority: item.priority,
                                    status: item.status,
                                    public_view: item.public_view
                                }
                            }));
                            successCallback(events);
                        },
                        error: function () {
                            failureCallback();
                        }
                    });
                },
                eventDidMount: function (info) {
                    if (info.event.backgroundColor) {
                        const dayStr = info.event.startStr.split('T')[0];
                        const dayEls = document.querySelectorAll(
                            ".fc-day[data-date='" + dayStr + "'] .fc-daygrid-day-number"
                        );
                        dayEls.forEach(eventDay => {
                            eventDay.style.setProperty('--event-bg-color', info.event.backgroundColor);
                            eventDay.setAttribute('data-event', 'true');
                        });
                    }
                },
                eventClick: function (info) {
                    loadEventsForDate(info.event.startStr.split('T')[0]);
                }
            });

            calendar.render();

            const today = new Date().toISOString().split('T')[0];
            loadEventsForDate(today);

            function loadEventsForDate(date) {
                $.ajax({
                    url: '/get_employee_calendar/' + empId,
                    method: 'GET',
                    success: function (response) {
                        if (!response || !Array.isArray(response.data)) {
                            console.error('Invalid response structure:', response);
                            return;
                        }

                        const filteredEvents = response.data.filter(item =>
                            date >= item.start_date && date <= (item.end_date || item.start_date)
                        );

                        const $list = $('.events-list');
                        $list.empty();

                        filteredEvents.forEach(event => {
                            function formatDate(dateString) {
                                const d = new Date(dateString);
                                const day = String(d.getDate()).padStart(2, '0');
                                const month = d.toLocaleString('de-DE', { month: 'short' });
                                return `${day}. ${month}`;
                            }

                            function formatTime(timeString) {
                                if (!timeString) return '';
                                const [hour, minute] = timeString.split(':');
                                return `${hour}:${minute} Uhr`;
                            }

                            const employeesHtml = (event.employees || []).map(employee => `
                                <li class="avatar" data-toggle="tooltip" data-popup="tooltip-custom"
                                    data-placement="bottom" title="${employee.name} ${employee.lastname}">
                                    <img class="media-object rounded-circle"
                                         src="/images/employee/${employee.image}"
                                         alt="Avatar" style="height:26px; width:26px">
                                </li>
                            `).join('');

                            const urlBase = event.type === 'Aufgabe'
                                ? '/personal_task_details/'
                                : '/appointment_details/';

                            const eventHtml = `
                                <div class="event-line"></div>
                                <div class="card-event mb-25">
                                    <div class="time">
                                        <div class="date">${formatDate(event.start_date)}</div>
                                        <div class="hour">${formatTime(event.start_time)}</div>
                                    </div>
                                    <div class="separator" style="background-color: ${event.taskColor || '#e5e7eb'} !important;"></div>
                                    <div class="details">
                                        <div class="title">
                                            <a href="${urlBase}${event.id}">
                                                ${event.title}
                                            </a>
                                        </div>
                                        <div class="description">
                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                ${employeesHtml}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $list.append(eventHtml);
                        });
                    },
                    error: function (xhr) {
                        console.error('Error fetching tasks:', xhr.responseText);
                    }
                });
            }
        });
    </script>

    <script>
    $(document).ready(function () {

        // ------------------------------------------------------
        // Efficiency widget
        // ------------------------------------------------------
        const effGauge          = document.getElementById('epEffGauge');
        const effPercentEl      = document.getElementById('epEffPercent');
        const effRangeEl        = document.getElementById('epEffRange');
        const effTotalProgEl    = document.getElementById('epEffTotalInProgress');
        const effTasksDoneEl    = document.getElementById('epEffTasksCompleted');
        const effTasksObjEl     = document.getElementById('epEffTasksObjections');
        const effActiveDaysEl   = document.getElementById('epEffActiveDays');
        const effActiveDaysLbl  = document.getElementById('epEffActiveDaysLabel');

        if (effGauge) {
            const url = "";

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    const percent       = parseInt(res.efficiency ?? 0, 10);
                    const rangeDays     = parseInt(res.range_days ?? 30, 10);
                    const activeDays    = parseInt(res.active_days ?? 0, 10);
                    const tiles         = res.tiles || {};
                    const totalProgress = parseInt(tiles.total_in_progress ?? 0, 10);
                    const tasksDone     = parseInt(tiles.tasks_completed ?? 0, 10);
                    const tasksObj      = parseInt(tiles.tasks_with_objection ?? 0, 10);

                    effRangeEl.textContent       = 'Letzte ' + rangeDays + ' Tage';
                    effTotalProgEl.textContent   = totalProgress;
                    effTasksDoneEl.textContent   = tasksDone;
                    effTasksObjEl.textContent    = tasksObj;
                    effActiveDaysEl.textContent  = activeDays;
                    effActiveDaysLbl.textContent = activeDays + ' von ' + rangeDays;

                    animateEfficiencyGauge(percent);
                },
                error: function (xhr) {
                    console.error('Efficiency request error', xhr.responseText);
                    // fallback: show 0% without animation
                    animateEfficiencyGauge(0);
                }
            });
        }

        function animateEfficiencyGauge(targetPercent) {
            targetPercent = Math.max(0, Math.min(100, targetPercent || 0));

            let current = 0;
            const step  = targetPercent > 0 ? Math.max(1, Math.round(targetPercent / 40)) : 1;

            const interval = setInterval(function () {
                current += step;
                if (current >= targetPercent) {
                    current = targetPercent;
                    clearInterval(interval);
                }
                updateGauge(current);
            }, 25);
        }

        function updateGauge(percent) {
            if (!effGauge || !effPercentEl) return;

            const angle = percent * 3.6;
            effGauge.style.background = 'conic-gradient(#4facfe ' + angle + 'deg, #e5e7eb 0deg)';
            effPercentEl.textContent = percent;
        }

    });
</script>
    <script>
        $(function () {
            $('.ep-note-tab').on('click', function () {
                var target = $(this).data('target');

                $('.ep-note-tab').removeClass('is-active');
                $(this).addClass('is-active');

                $('.ep-note-grid-group').addClass('d-none');
                $('#' + target).removeClass('d-none');
            });
        });
    </script>

@endsection



@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Mitarbeiter-Profil',
                url: "{{ url()->current() }}",
                clickable: false
            },
            {
                label: '{{ $data->name }} {{ $data->lastname }}',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush
