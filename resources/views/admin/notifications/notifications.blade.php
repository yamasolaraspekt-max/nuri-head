@extends('admin.layouts.app')

@section('title')
MEIN ANTRÄGE
@endsection

@section('style')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link href="{{ asset('css/custom-menu.css') }}" rel='stylesheet' />
<link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

<style>
    /* ORIGINAL STYLES (KEPT) ------------------------------------------- */
    #deadline_area, .end_time_area, .repeated_area, .reminder_area ,.add_calendar_area{
        display: none;
    }
    #calendar {
        width: 100%;
        min-height: 600px;
        background: #f4f4f4;
    }
    .fc .fc-daygrid-day {
        min-width: auto;
        height: auto !important;
        max-width: 100%;
    }
    .fc-scrollgrid {
        width: 100%;
    }
    .fc .fc-daygrid-day-frame {
        display: flex !important;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        height: auto !important;
        padding: 10px !important;
    }
    .fc-daygrid-event-harness {
        width: 100% !important;
        margin: 5px 0 !important;
    }
    .fc-daygrid-day-events {
        width: 100% !important;
    }
    .fc-event-custom {
        padding: 8px !important;
        background: white;
        border-left: 4px solid #007bff;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        text-align: left;
        transition: all 0.3s ease;
        height: 118px;
    }
    @media (max-width: 768px) {
        #calendar {
            min-height: 400px;
        }
        .fc-daygrid-day {
            min-width: 100px !important;
        }
        .fc-event-custom {
            padding: 6px;
            font-size: 12px;
        }
        .custom-event-header {
            font-size: 12px !important;
        }
        .custom-event-time {
            font-size: 11px !important;
        }
        .fc .fc-daygrid-day-frame {
            padding: 5px !important;
        }
    }
    @media (max-width: 576px) {
        #calendar {
            min-height: 300px;
        }
        .fc-daygrid-day {
            min-width: 80px !important;
        }
        .fc-event-custom {
            padding: 4px;
            font-size: 10px;
        }
        .custom-event-header {
            font-size: 10px;
        }
        .custom-event-time {
            font-size: 9px;
        }
        .fc .fc-daygrid-day-frame {
            padding: 3px !important;
        }
    }
    .fc-event-mobile {
        font-size: 14px;
        font-weight: bold;
        color: white;
        background-color: #007bff;
        border-radius: 4px;
        padding: 5px;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    @media (max-width: 768px) {
        .fc-daygrid-day-frame {
            padding: 5px !important;
        }
    }
    .time {
        color: #545454;
        font-size: 10px;
    }
    .accept_request {
        border: 3px solid #8fc73e !important;
    }
    .reject_request {
        border: 3px solid #ea5555 !important;
    }
    .send_request {
        border: 3px solid rgb(222, 158, 47) !important;
    }
    .new_task {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #f1f1f1;
        z-index: 10;
        width: 30% !important;
        max-width: 30% !important;
        max-height: 85vh;
        overflow-y: auto;
    }
    .new_task .modal-body {
        max-height: 85vh;
        overflow-y: auto;
        padding: 15px;
    }
    .new_task .modal-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .new_task .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-top: 1px solid #ddd;
    }
    @media (max-width: 768px) {
        .new_task {
            width: 90% !important;
            max-width: 90% !important;
        }
    }
    .new_task_close {
        position: absolute;
        z-index: 4;
        left: -135px;
        top: 16%;
    }
    .card {
        box-shadow: 0 0 !important;
    }
    .odd_color {
        background: #e8e8e8;
    }
    .mark-complete {
        text-decoration: line-through 3px black;
        color: #ff0000;
    }
    .progress {
        height: 23px !important;
        border: 1px solid gray !important;
        border-radius: 6px !important;
    }
    .progress-bar {
        width: 60%;
        height: 23px;
        border-radius: 0 !important;
        background-color: #e50056 !important;
    }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
    .feather.icon-bell.warning.out-date {
        animation: blink 1s infinite;
    }
    .nav.nav-tabs .nav-item .nav-link.active {
        border: none;
        position: relative;
        color: #efffd8 !important;
        transition: all .2s ease;
        background-color: #8fc73e !important;
    }
    .dropup{
        position: absolute !important;
    }
    .form-control {
        display: block;
        width: 100%;
        height: calc(1.25em + 18px + 1px);
        padding: -1.3rem 0.7rem;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.25;
        color: #4e5154;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-selection__choice {
        border:0 !important;
    }
    .line {
        width: 90%;
        border-bottom: 2px solid #b8b8b8;
        margin-top: 6px;
        margin-bottom: 6px;
    }

    tr.edited {
        background-color: #ffcc00 !important;
        transition: background-color 0.5s ease;
    }
    .task {
        font-size:13px !important;
        margin-bottom:0;
        color:#3a3a3a;
        font-weight: bold;
    }
    .task_description {
        font-size:11px !important;
        color:#3a3a3a  !important;
    }
    .task_date {
        font-size:11px !important;
        color:#74b2d4  !important;
    }
    .table-responsive  {
        overflow: visible !important;
    }
    .appointment_menu {
        top: -52px !important;
    }

    .leave-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        transition: right 0.3s ease-in-out;
        overflow-y: auto;
    }
    .leave-sidebar.active {
        right: 0;
    }
    #mentionSuggestions li {
        padding: 5px 10px;
        cursor: pointer;
    }
    #mentionSuggestions li:hover {
        background-color: #f1f1f1;
    }
    .note-item p span.mention {
        background: #e6f3ff;
        color: #007bff;
        font-weight: bold;
    }

    /* NEW UI STYLES ---------------------------------------------------- */

    /* --- TOP METRICS --- */
    .leave-metric-section {
        margin-bottom: 1rem;
    }
    .leave-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }
    .leave-metric-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem 1.1rem;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.35);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .leave-metric-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        margin-bottom: .25rem;
    }
    .leave-metric-value {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        line-height: 1.1;
    }
    .leave-metric-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: .25rem;
    }
    .leave-metric-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .pill-green { background: #ecfdf5; color: #16a34a; }
    .pill-amber { background: #fffbeb; color: #d97706; }
    .pill-sky { background: #eff6ff; color: #2563eb; }

    /* --- HEADER / FILTER BAR --- */
    .leave-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }
    .leave-header-title {
        display: flex;
        flex-direction: column;
    }
    .leave-header-title h4 {
        margin: 0;
        font-weight: 700;
    }
    .leave-header-title small {
        font-size: 12px;
        color: #6b7280;
    }
    .leave-filters {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }
    .leave-search-group {
        position: relative;
        min-width: 220px;
    }
    .leave-search-group input {
        padding-left: 32px;
    }
    .leave-search-icon {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 13px;
        color: #9ca3af;
    }
    .leave-sort-select {
        min-width: 130px;
    }
    .btn-outline-soft {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.7);
        background: #fff;
        padding: .35rem .75rem;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .btn-outline-soft i {
        font-size: 14px;
    }
    .nav.nav-tabs .nav-item .nav-link {
        border-radius: 999px !important;
        margin: .25rem .25rem .25rem 0;
        padding: .35rem .9rem;
        font-size: 13px;
        border: 1px solid transparent;
        color: #4b5563;
    }
    .notifications-wrapper .card-body {
        padding: 0;
    }
    .notifications-wrapper .tab-pane {
        padding: .75rem .75rem 1rem;
    }
    @media (max-width: 768px) {
        .leave-header {
            align-items: flex-start;
        }
        .leave-header-title h4 {
            font-size: 16px;
        }
        .leave-metric-card {
            padding: .85rem .9rem;
        }
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
                        <h2 class="content-header-title float-left mb-0">MEIN ANTRÄGE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Notifications</a></li> 
                            </ol>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div class="content-body">  

            <!-- METRIC CARDS -->
            <section class="leave-metric-section">
                <div class="leave-metrics">
                    <div class="leave-metric-card">
                        <div>
                            <div class="leave-metric-label">Meine Anträge gesamt</div>
                            <div class="leave-metric-value">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="leave-metric-sub">
                            <span class="leave-metric-pill pill-sky">
                                <i class="feather icon-layers mr-25"></i> alle Urlaubsanträge
                            </span>
                        </div>
                    </div>

                    <div class="leave-metric-card">
                        <div>
                            <div class="leave-metric-label">Offene Anträge</div>
                            <div class="leave-metric-value">{{ $stats['open'] ?? 0 }}</div>
                        </div>
                        <div class="leave-metric-sub">
                            <span class="leave-metric-pill pill-amber">
                                <i class="feather icon-clock mr-25"></i> noch in Bearbeitung
                            </span>
                        </div>
                    </div>

                    <div class="leave-metric-card">
                        <div>
                            <div class="leave-metric-label">Heute im Urlaub</div>
                            <div class="leave-metric-value">{{ $stats['today_on_leave'] ?? 0 }}</div>
                        </div>
                        <div class="leave-metric-sub">
                            <span class="leave-metric-pill pill-green">
                                <i class="feather icon-sun mr-25"></i> aktueller Tag
                            </span>
                        </div>
                    </div>

                    <div class="leave-metric-card">
                        <div>
                            <div class="leave-metric-label">Anfragen an mich</div>
                            <div class="leave-metric-value">{{ $stats['pending_to_me'] ?? 0 }}</div>
                        </div>
                        <div class="leave-metric-sub">
                            <span class="leave-metric-pill pill-amber">
                                <i class="feather icon-inbox mr-25"></i> noch zu beantworten
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MAIN CARD WITH FILTER + TABS -->
            <section id="nav-justified" class="notifications-wrapper">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header leave-header">
                                <div class="leave-header-title">
                                    <h4 class="card-title mb-25">MEIN ANTRÄGE</h4>
                                    <small>Suche nach Mitarbeiter, Datum, Status, Grund, Beschreibung usw.</small>
                                </div>

                                <div class="leave-filters">
                                    <div class="leave-search-group">
                                        <span class="leave-search-icon">
                                            <i class="feather icon-search"></i>
                                        </span>
                                        <input id="search-input"
                                               type="text"
                                               class="form-control"
                                               placeholder="Global suchen … (Mitarbeiter, Datum, Status)">
                                    </div>

                                    <select id="sort-order" class="form-control leave-sort-select">
                                        <option value="desc" selected>Neueste zuerst</option>
                                        <option value="asc">Älteste zuerst</option>
                                    </select>

                                    <button id="search-btn" class="btn btn-outline-soft">
                                        <i class="feather icon-filter"></i>
                                        Anwenden
                                    </button>

                                    <button id="search-reset" class="btn btn-outline-soft">
                                        <i class="feather icon-refresh-ccw"></i>
                                        Zurücksetzen
                                    </button>

                                    @if(DB::table('user_rolls')
                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                        ->where('user_rolls.item_id', '=', 'Administrator')
                                        ->where('user_rolls.is_add', '=', 'on')
                                        ->first())
                                        <button type="button"
                                                class="btn btn-outline-primary new_leave"
                                                id="create_leave_btn">
                                            <i class="feather icon-plus"></i> Urlaub erstellen
                                        </button>
                                    @endif 
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <ul class="nav nav-tabs nav-justified px-1 pt-1" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="open-tab" data-toggle="tab" href="#open-page" role="tab">
                                            Meine Anträge ({{ $stats['open'] ?? 0 }} offen)
                                        </a>
                                    </li>
                                    @if($hasAdminAccess)
                                        <li class="nav-item">
                                            <a class="nav-link" id="answer-tab" data-toggle="tab" href="#answer-page" role="tab">
                                                Anfragen an mich ({{ $stats['pending_to_me'] ?? 0 }})
                                            </a>
                                        </li>
                                    @endif
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="open-page" role="tabpanel">
                                        <div id="search-results"></div>
                                    </div>
                                    <div class="tab-pane" id="answer-page" role="tabpanel">
                                        <div id="answer-results"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> 

            <!-- MODAL: NEW LEAVE (original) -->
            <div class="modal fade" id="new_leave_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Urlaub erstellen</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" method="POST" action="{{ route('leave.store') }}">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="active_tab" value="leave">
                                <input type="hidden" name="department_id" id="department_id">

                                <div class="form-group">
                                    <label>Mitarbeiter</label>
                                    <select name="emp_id" id="emp_select" class="form-control" style="width: 100%"></select>
                                </div>

                                <div class="form-group">
                                    <label>Jahr</label>
                                    <select name="year" id="yearSelect" class="form-control">
                                        <option value="">Jahr wählen</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Ab Datum</label>
                                    <input type="date" class="form-control leave_start_date" name="start_date">
                                </div>
                                <div class="form-group">
                                    <label>Bis Datum</label>
                                    <input type="date" class="form-control leave_end_date" name="end_date">
                                </div>

                                <div class="form-group">
                                    <label>Urlaubstage</label>
                                    <input type="number" class="form-control leave_day" name="leave_day">
                                </div>
                                <div class="form-group">
                                    <label>Resturlaubstage</label>
                                    <input type="number" class="form-control remaining_day" name="remaining_day">
                                </div>
                                <div class="form-group">
                                    <label>Urlaubstage letztes Jahr</label>
                                    <input type="number" class="form-control last_year_remainings" name="last_year_remainings" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Eingereichte Urlaubstage</label>
                                    <input type="number" class="form-control leave_duration" name="duration">
                                    <label class="duration_label" style="color:red; display:none;">Die Dauer überschreitet die zulässigen Urlaubstage</label>
                                </div>

                                <div class="form-group">
                                    <label>Grund</label>
                                    <select class="form-control" name="reason">
                                        <option value="Urlaub" selected>Urlaub</option>
                                        <option value="Freizeitausgleich">Freizeitausgleich</option>
                                        <option value="Vorjahresurlaub">Vorjahresurlaub</option>
                                        <option value="Elternzeit">Elternzeit</option>
                                        <option value="Schulung">Schulung</option>
                                        <option value="Schule">Schule</option>
                                        <option value="Unbezahte Urlaub">Unbezahte Urlaub</option>
                                        <option value="Freigeschtilt">Freigeschtilt</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Anfrage an (Abteilungsleiter)</label>
                                    <select class="form-control request_to" id="employee_leader_select" name="request_to" style="width:100%"></select>
                                </div>

                                <div class="form-group">
                                    <label>Beschreibung</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary save_button">Speichern</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- NOTES SIDEBAR (shared for both tabs) -->
            <div id="leaveNotesSidebar" class="leave-sidebar p-3">
                <div class="sidebar-header d-flex justify-content-between align-items-center mb-2" style="background:#8fc73e; padding:.5rem .75rem;">
                    <h5 class="mb-0"><i class="feather icon-edit-3 mr-25"></i> Notizen</h5>
                    <button onclick="closeLeaveSidebar()" class="btn btn-sm btn-danger">×</button>
                </div>

                <div id="leaveNotesContent" class="mb-3"></div>

                <div class="position-relative">
                    <textarea id="newNoteText" class="form-control mb-2" rows="3" placeholder="Neue Notiz… @Mitarbeiter"></textarea>
                    <ul id="mentionSuggestions" class="list-group position-absolute bg-white border" style="top: 100%; left: 0; width: 100%; z-index: 9999; display: none;"></ul>
                </div>

                <button class="btn btn-primary btn-block mt-2" onclick="saveLeaveNote()">
                    <i class="feather icon-save mr-25"></i> Speichern
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')

{{-- GLOBAL TAB + FILTER + NOTES LOGIC --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentLeaveId = null;
    let employeesList = [];
    let activeTab = 'open'; // 'open' | 'answer'
    const baseUrl = window.location.origin;

    const routes = {
        open:   "{{ route('employee.notification.view') }}",
        answer: "{{ route('employee.notification.response') }}"
    };

    const searchInput = document.getElementById('search-input');
    const sortSelect  = document.getElementById('sort-order');
    const searchBtn   = document.getElementById('search-btn');
    const resetBtn    = document.getElementById('search-reset');

    // usernames for @mention
    fetch('/get-employee-usernames')
        .then(res => res.json())
        .then(data => employeesList = data || [])
        .catch(() => employeesList = []);

    function loadTab(page = 1) {
        const search = searchInput.value || '';
        const sort   = sortSelect.value || 'desc';
        const url    = activeTab === 'open' ? routes.open : routes.answer;
        const target = activeTab === 'open' ? '#search-results' : '#answer-results';

        $.get(url, { search, sort, page }, function (html) {
            $(target).html(html);
        });
    }

    // initial load
    loadTab();

    // tab switching
    $('#open-tab').on('click', function (e) {
        e.preventDefault();
        activeTab = 'open';
        loadTab();
        $(this).tab('show');
    });

    $('#answer-tab').on('click', function (e) {
        e.preventDefault();
        activeTab = 'answer';
        loadTab();
        $(this).tab('show');
    });

    // search / sort
    let searchTimer = null;

    searchInput.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            loadTab();
            return;
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadTab, 350);
    });

    sortSelect.addEventListener('change', function () {
        loadTab();
    });

    searchBtn.addEventListener('click', function (e) {
        e.preventDefault();
        loadTab();
    });

    resetBtn.addEventListener('click', function () {
        searchInput.value = '';
        sortSelect.value = 'desc';
        loadTab();
    });

    // pagination for both tabs
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const href = $(this).attr('href') || '';
        const page = href.split('page=')[1] || 1;
        loadTab(page);
    });

    // NOTES SIDEBAR
    window.closeLeaveSidebar = function () {
        document.getElementById('leaveNotesSidebar').classList.remove('active');
        currentLeaveId = null;
    };

    function renderLeaveNotes(notes) {
        const content = document.getElementById('leaveNotesContent');
        content.innerHTML = '';
        if (!Array.isArray(notes)) notes = [];

        notes.forEach((note, index) => {
            const image = note.image
                ? `${baseUrl}/images/employee/${note.image}`
                : `${baseUrl}/images/gender/male.png`;

            content.innerHTML += `
                <div class="note-item border p-2 mb-2 d-flex">
                    <img src="${image}"
                         alt="${note.employee}"
                         class="rounded-circle mr-2"
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <small><strong>${note.employee}</strong> – ${note.date}</small>
                        <p class="mb-1">${note.text}</p>
                        <button class="btn btn-sm btn-warning" onclick="editLeaveNote(${index})">
                            <i class="feather icon-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLeaveNote(${index})">
                            <i class="feather icon-trash"></i>
                        </button>
                    </div>
                </div>`;
        });
    }

    function loadLeaveNotes() {
        if (!currentLeaveId) return;
        fetch(`/leaves/${currentLeaveId}/notes`)
            .then(res => res.json())
            .then(data => renderLeaveNotes(data || []))
            .catch(err => console.error('Fehler beim Laden der Notizen:', err));
    }

    // open notes from any table
    $(document).on('click', '.leave-notes', function () {
        currentLeaveId = this.dataset.id;
        document.getElementById('leaveNotesSidebar').classList.add('active');
        loadLeaveNotes();
    });

    window.saveLeaveNote = function () {
        const text = document.getElementById('newNoteText').value;
        if (!text.trim() || !currentLeaveId) return;

        fetch(`/leaves/${currentLeaveId}/notes/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('newNoteText').value = '';
            renderLeaveNotes(data.notes || []);
        });
    };

    window.deleteLeaveNote = function (index) {
        if (!currentLeaveId) return;
        Swal.fire({
            title: 'Löschen?',
            text: 'Diese Notiz wirklich entfernen?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`/leaves/${currentLeaveId}/notes/delete/${index}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => renderLeaveNotes(data.notes || []));
        });
    };

    window.editLeaveNote = function (index) {
        if (!currentLeaveId) return;
        const newText = prompt("Neue Notiz eingeben:");
        if (!newText) return;

        fetch(`/leaves/${currentLeaveId}/notes/update/${index}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text: newText })
        })
        .then(res => res.json())
        .then(data => renderLeaveNotes(data.notes || []));
    };

    // @mention suggestions
    const noteInput = document.getElementById('newNoteText');
    noteInput.addEventListener('input', function () {
        const val = this.value;
        const caretPos = this.selectionStart;
        const match = val.substring(0, caretPos).match(/@([\w\.]*)$/);
        const suggestionBox = document.getElementById('mentionSuggestions');

        if (match) {
            const term = match[1].toLowerCase();
            const matches = employeesList
                .filter(name => name.toLowerCase().includes(term))
                .slice(0, 5);

            suggestionBox.innerHTML = '';
            matches.forEach(name => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = name;
                li.onclick = () => {
                    noteInput.value = val.substring(0, caretPos - match[0].length) + `@${name} ` + val.substring(caretPos);
                    noteInput.focus();
                    suggestionBox.style.display = 'none';
                };
                suggestionBox.appendChild(li);
            });

            const rect = this.getBoundingClientRect();
            suggestionBox.style.top  = `${rect.top + window.scrollY + this.offsetHeight}px`;
            suggestionBox.style.left = `${rect.left}px`;
            suggestionBox.style.display = 'block';
        } else {
            suggestionBox.style.display = 'none';
        }
    });
});
</script>

{{-- ACCEPT / REJECT (request_answer) --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const acceptLeaveRoute = "{{ route('accept.leave.date') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    function sendLeaveRequest(leaveId, employeeId, type, startDate = null, endDate = null) {
        if (!csrfToken) {
            console.error("❌ CSRF Token not found!");
            return;
        }

        fetch(acceptLeaveRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                leave_id: leaveId,
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate,
                type: type
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Erfolg!",
                    text: "Die Anfrage wurde erfolgreich bearbeitet.",
                    icon: "success",
                    confirmButtonText: "OK",
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.error || "Fehler beim Verarbeiten der Anfrage.");
            }
        })
        .catch(error => {
            Swal.fire({
                title: "Fehler!",
                text: error.message,
                icon: "error",
                confirmButtonText: "OK",
            });
            console.error("Fehler:", error);
        });
    }

    // Accept
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("accept-btn")) {
            let leaveId = event.target.getAttribute("data-leave-id");
            let employeeId = event.target.getAttribute("data-employee-id");

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie diesen Urlaub wirklich akzeptieren?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ja, akzeptieren!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    sendLeaveRequest(leaveId, employeeId, "accept");
                }
            });
        }
    });

    // Reject with new date range
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("reject-btn")) {
            let leaveId = event.target.getAttribute("data-leave-id");
            let employeeId = event.target.getAttribute("data-employee-id");
            let startDate = event.target.getAttribute("data-start");
            let endDate = event.target.getAttribute("data-end");

            Swal.fire({
                title: "Urlaub ablehnen",
                html: `
                    <label for="start_date">Neues Startdatum:</label>
                    <input type="date" id="start_date_input" class="swal2-input" value="${startDate}">
                    <label for="end_date">Neues Enddatum:</label>
                    <input type="date" id="end_date_input" class="swal2-input" value="${endDate}">
                `,
                showCancelButton: true,
                confirmButtonText: "Ablehnen",
                cancelButtonText: "Abbrechen",
                preConfirm: () => {
                    let newStartDate = document.getElementById("start_date_input").value;
                    let newEndDate = document.getElementById("end_date_input").value;

                    if (!newStartDate || !newEndDate) {
                        Swal.showValidationMessage("Bitte geben Sie Start- und Enddatum ein.");
                        return false;
                    }

                    return { newStartDate, newEndDate };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    sendLeaveRequest(leaveId, employeeId, "reject", result.value.newStartDate, result.value.newEndDate);
                }
            });
        }
    });
});
</script>

{{-- APPROVE / CHANGE (approved flag) --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const changeLeaveRoute = "{{ route('change.leave.date') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    function sendLeaveRequestChange(leaveId, employeeId, type, startDate = null, endDate = null) {
        if (!csrfToken) {
            console.error("❌ CSRF Token not found!");
            return;
        }

        fetch(changeLeaveRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                leave_id: leaveId,
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate,
                type: type
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Erfolg!",
                    text: "Die Anfrage wurde erfolgreich bearbeitet.",
                    icon: "success",
                    confirmButtonText: "OK",
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.error || "Fehler beim Verarbeiten der Anfrage.");
            }
        })
        .catch(error => {
            Swal.fire({
                title: "Fehler!",
                text: error.message,
                icon: "error",
                confirmButtonText: "OK",
            });
            console.error("Fehler:", error);
        });
    }

    // Approve
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("approve-btn")) {
            let leaveId = event.target.getAttribute("data-leave-id");
            let employeeId = event.target.getAttribute("data-employee-id");

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie diesen Mitarbeiterurlaub wirklich genehmigen?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ja, akzeptieren!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    sendLeaveRequestChange(leaveId, employeeId, "accept");
                }
            });
        }
    });

    // Change dates (reject)
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("change-btn")) {
            let leaveId = event.target.getAttribute("data-leave-id");
            let employeeId = event.target.getAttribute("data-employee-id");
            let startDate = event.target.getAttribute("data-start");
            let endDate = event.target.getAttribute("data-end");

            Swal.fire({
                title: "Urlaub ablehnen",
                html: `
                    <label for="start_date">Neues Startdatum:</label>
                    <input type="date" id="start_date_input2" class="swal2-input" value="${startDate}">
                    <label for="end_date">Neues Enddatum:</label>
                    <input type="date" id="end_date_input2" class="swal2-input" value="${endDate}">
                `,
                showCancelButton: true,
                confirmButtonText: "Ablehnen",
                cancelButtonText: "Abbrechen",
                preConfirm: () => {
                    let newStartDate = document.getElementById("start_date_input2").value;
                    let newEndDate = document.getElementById("end_date_input2").value;

                    if (!newStartDate || !newEndDate) {
                        Swal.showValidationMessage("Bitte geben Sie Start- und Enddatum ein.");
                        return false;
                    }

                    return { newStartDate, newEndDate };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    sendLeaveRequestChange(leaveId, employeeId, "reject", result.value.newStartDate, result.value.newEndDate);
                }
            });
        }
    });
});
</script>

{{-- CONFLICT CHECK --}}
<script>
$(document).on('click', '.check-leave', function () {
    const employeeId = $(this).data('employee-id');
    const startDate = $(this).data('start-date');
    const endDate = $(this).data('end-date');

    $.ajax({
        url: `/check/department-holidays/${employeeId}/${startDate}/${endDate}`,
        type: 'GET',
        success: function (data) {
            let html = `
            <div class="row" style="display:flex; gap:10px;">
                <div class="col" style="flex:1; max-height:420px; overflow-y:auto;">
                    <h6 class="text-danger"><strong>${data.conflict_count}</strong> im Urlaub</h6>
                    <ul class="list-group">`;

            data.conflicts.forEach(item => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex align-items-start">
                            <img src="/images/employee/${item.image}" class="rounded-circle mr-2" width="50" height="50">
                            <div>
                                <strong>${item.name} ${item.lastname}</strong><br>
                                <small>${item.position} – ${item.department_name}</small><br>
                                <small>📅 ${item.start_date} → ${item.end_date}</small><br>
                                <span class="badge badge-${getStatusColor(item.status)}">${item.status}</span>
                            </div>
                        </div>
                    </li>`;
            });

            html += `</ul></div>`;

            html += `
                <div class="col" style="flex:1; max-height:420px; overflow-y:auto;">
                    <h6 class="text-success"><strong>${data.present_count}</strong> anwesend</h6>
                    <ul class="list-group">`;

            data.present.forEach(item => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex align-items-start">
                            <img src="/images/employee/${item.image}" class="rounded-circle mr-2" width="50" height="50">
                            <div>
                                <strong>${item.name} ${item.lastname}</strong><br>`;
                item.departments.forEach(dep => {
                    html += `<small>${dep.position} – ${dep.department_name}</small><br>`;
                });
                html += `</div></div></li>`;
            });

            html += `</ul></div>`;

            html += `
                <div class="col text-center" style="flex:1;">
                    <h6 class="mb-2">Kalender</h6>
                    <div id="leave-calendar"></div>
                </div>
            </div>`;

            Swal.fire({
                title: 'Abteilungsübersicht',
                html: html,
                width: '95%',
                didOpen: () => {
                    new Litepicker({
                        element: document.getElementById('leave-calendar'),
                        inlineMode: true,
                        singleMode: false,
                        showTooltip: false,
                        startDate: startDate,
                        endDate: endDate,
                        numberOfMonths: 1,
                        numberOfColumns: 1
                    });
                },
                confirmButtonText: 'Schließen'
            });
        },
        error: function () {
            Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
        }
    });

    function getStatusColor(status) {
        switch(status.toLowerCase()) {
            case 'approved': return 'success';
            case 'pending': return 'warning';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }
});
</script>

{{-- NEW LEAVE MODAL + SELECT2 + CALC --}}
<script>
const path_image = "{{ asset('images/employee') }}";

$(document).ready(function () {

    // Select2
    $('#emp_select').select2({
        placeholder: "Mitarbeiter auswählen",
        allowClear: true,
        width: '100%',
        templateResult: formatEmployee,
        templateSelection: formatEmployee
    });

    $('#employee_leader_select').select2({
        placeholder: "Abteilungsleiter auswählen",
        allowClear: true,
        width: '100%',
        templateResult: formatEmployee,
        templateSelection: formatEmployee
    });

    function formatEmployee(employee) {
        if (!employee.id) return employee.text;
        const image = $(employee.element).data('img') || "/default-avatar.png";
        return $(`<span><img src="${image}" class="rounded-circle" width="30" height="30" style="margin-right:10px;"> ${employee.text}</span>`);
    }

    function populateYearDropdown() {
        const currentYear = new Date().getFullYear();
        const yearSelect = document.getElementById("yearSelect");
        yearSelect.innerHTML = "";
        for (let year = currentYear - 5; year <= currentYear + 1; year++) {
            yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
        }
        yearSelect.value = currentYear;
    }

    function calculateWorkingDays(startDate, endDate) {
        let start = new Date(startDate), end = new Date(endDate), count = 0;
        while (start <= end) {
            const day = start.getDay();
            if (day !== 0 && day !== 6) count++;
            start.setDate(start.getDate() + 1);
        }
        return count;
    }

    function updateDuration(modal) {
        const start = modal.querySelector(".leave_start_date").value;
        const end = modal.querySelector(".leave_end_date").value;
        const leaveDays = parseInt(modal.querySelector(".leave_day").value) || 0;
        const durationInput = modal.querySelector(".leave_duration");
        const remainingInput = modal.querySelector(".remaining_day");
        const label = modal.querySelector(".duration_label");
        const saveBtn = modal.querySelector(".save_button");

        if (start && end) {
            const workDays = calculateWorkingDays(start, end);
            durationInput.value = workDays;
            remainingInput.value = Math.max(leaveDays - workDays, 0);

            if (workDays > leaveDays) {
                label.style.display = "block";
                saveBtn.style.display = "none";
                Swal.fire("Achtung!", "Sie haben mehr Urlaubstage beantragt als verfügbar!", "warning");
            } else {
                label.style.display = "none";
                saveBtn.style.display = "inline-block";
            }
        }
    }

    function fetchLeaveDays(empId, year) {
        if (!empId) return;
        fetch(`/employee/remaining/days/${empId}?year=${year}`)
            .then(res => res.json())
            .then(data => {
                $('.leave_day').val(data.total_leave_days || 0);
                $('.remaining_day').val(data.remaining_days || 0);
                $('.last_year_remainings').val(data.last_year_remainings || 0);
            });
    }

    function loadLeaders(departmentId) {
        $.get(`/getDepartment/leader/${departmentId}`, function (leaders) {
            const $select = $('#employee_leader_select');
            $select.empty().append('<option value="">Leiter auswählen</option>');
            leaders.forEach(leader => {
                const img = leader.image ? `${path_image}/${leader.image}` : '/default-avatar.png';
                const option = new Option(`${leader.name} ${leader.lastname}`, leader.emp_id, false, false);
                $(option).attr('data-img', img);
                $select.append(option);
            });
            $select.trigger('change');
        });
    }

    function getDepartment(empId) {
        return fetch(`/employee/${empId}/main-department`).then(res => res.json());
    }

    $('#emp_select').on('change', function () {
        const empId = $(this).val();
        const year = $('#yearSelect').val();
        if (!empId) return;
        fetchLeaveDays(empId, year);
        getDepartment(empId).then(data => {
            $('#department_id').val(data.department_id);
            loadLeaders(data.department_id);
        });
    });

    $('.new_leave').on('click', function () {
        $('#new_leave_modal').modal('show');
        populateYearDropdown();
        $.post('/getEmployees', {_token: '{{ csrf_token() }}'}, function (employees) {
            const $empSelect = $('#emp_select');
            $empSelect.empty().append('<option value="">Mitarbeiter auswählen</option>');
            employees.forEach(emp => {
                const img = `${path_image}/${emp.image || 'default-avatar.png'}`;
                const opt = new Option(`${emp.name} ${emp.lastname}`, emp.id, false, false);
                $(opt).attr('data-img', img);
                $empSelect.append(opt);
            });
        });
    });

    $('#new_leave_modal').on('change', '.leave_start_date, .leave_end_date', function () {
        updateDuration(document.querySelector('#new_leave_modal'));
    });

    $('.save_button').on('click', function (e) {
        e.preventDefault();
        const modal = document.querySelector("#new_leave_modal");
        const form = modal.querySelector("form");
        const formData = new FormData(form);
        const json = Object.fromEntries(formData.entries());

        if (!json.emp_id || !json.start_date || !json.end_date || !json.request_to) {
            Swal.fire("Fehlende Angaben", "Bitte alle Pflichtfelder ausfüllen!", "warning");
            return;
        }

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Content-Type": "application/json"
            },
            body: JSON.stringify(json)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Erfolgreich", "Urlaub wurde gespeichert.", "success").then(() => {
                    $('#new_leave_modal').modal('hide');
                    form.reset();
                    location.reload();
                });
            } else {
                Swal.fire("Fehler", "Speichern fehlgeschlagen.", "error");
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire("Fehler", "Serverfehler aufgetreten.", "error");
        });
    });

    $('.delete-leave').on('click', function () {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        Swal.fire({
            title: "Bist du sicher?",
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ja, löschen!"
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/leave_delete/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        Swal.fire("Gelöscht!", res.message, "success");
                        row.fadeOut(500, () => row.remove());
                    },
                    error: function () {
                        Swal.fire("Fehler!", "Fehler beim Löschen!", "error");
                    }
                });
            }
        });
    });

    populateYearDropdown();
});
</script>

@endsection
