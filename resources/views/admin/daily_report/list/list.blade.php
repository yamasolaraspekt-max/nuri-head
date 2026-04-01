@extends('admin.layouts.app')

@section('title')
Tagesbericht
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
    :root {
        --sa-green: #93c21c;
        --sa-blue: #74b2d4;
        --sa-dark: #020617;
        --sa-muted: #e5e7eb;
        --sa-soft: #f9fafb;
    }

    .daily-shell {
        max-width: 1200px;
        margin: 0 auto;
    }

    .daily-toolbar {
        background: #ffffff; 
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.85rem 1.25rem;
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
        justify-content: space-between; 
    }

    .daily-toolbar-left {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .daily-toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    #datePicker {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.7);
        padding: .4rem .9rem;
        font-size: .9rem;
        min-width: 180px;
    }

    .daily-filter-btn .dropdown-toggle {
        border-radius: 999px;
    }

    .daily-search-group {
        max-width: 260px;
    }

    .daily-search-group .form-control {
        border-radius: 999px 0 0 999px;
    }

    .daily-search-group .btn {
        border-radius: 0 999px 999px 0;
    }

    /* ===== Employee Grid ===== */
    .employee-grid {
        margin-top: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 1rem;
    }

    .employee-card {
        position: relative;
        background: #ffffff; 
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 1rem 1.1rem 0.75rem; 
        display: flex;
        flex-direction: column;
        min-height: 220px;
        overflow: hidden;
        border-left: 4px solid var(--sa-green);
    }

    .employee-card-header {
        display: flex;
        gap: .75rem;
        align-items: center;
        margin-bottom: .75rem;
    }

    .employee-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .employee-avatar {
        width: 48px;
        height: 48px;
        border-radius: 999px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .employee-avatar-status {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        border: 2px solid #fff;
        background: var(--sa-green);
    }

    .employee-card-title {
        min-width: 0;
    }

    .employee-name {
        font-weight: 600;
        font-size: .98rem;
        color: #0f172a;
        margin-bottom: .1rem;
    }

    .employee-meta-chips {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
    }

    .chip {
        font-size: .7rem;
        padding: .16rem .5rem;
        border-radius: 999px;
        line-height: 1.1;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .chip-soft {
        background: rgba(148, 163, 184, .12);
        color: #4b5563;
        border-color: rgba(148, 163, 184, .4);
    }

    .chip-muted {
        background: #f9fafb;
        color: #6b7280;
        border-color: #e5e7eb;
    }

    .chip-status {
        background: rgba(147, 194, 28, .1);
        color: #3f6212;
        border-color: rgba(147, 194, 28, .6);
    }

    /* ===== Card Body ===== */
    .employee-card-body {
        font-size: .8rem;
        color: #4b5563;
    }

    .progress-thin {
        height: .55rem;
        border-radius: 999px;
        overflow: hidden;
        background: #e5e7eb;
    }

    .progress-thin .progress-bar {
        border-radius: 999px;
    }

    .tiny-rows span {
        font-size: .75rem;
        color: #6b7280;
    }

    .quick-stats {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-top: .65rem;
    }

    .stat-pill {
        flex: 1 1 90px;
        min-width: 90px;
        background: #f9fafb;
        border-radius: 12px;
        padding: .35rem .5rem;
        border: 1px solid rgba(209, 213, 219, 0.8);
    }

    .stat-pill .label {
        display: block;
        font-size: .68rem;
        color: #9ca3af;
    }

    .stat-pill .value {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        color: #111827;
    }

    .timeline {
        margin-top: .7rem;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: .4rem;
        margin-bottom: .25rem;
    }

    .timeline-title {
        font-size: .78rem;
        font-weight: 600;
        color: #111827;
    }

    .timeline-meta {
        font-size: .7rem;
        color: #9ca3af;
    }

    .timeline-list {
        max-height: 120px;
        overflow: auto;
        padding-right: .2rem;
    }

    .timeline-item {
        display: flex;
        gap: .45rem;
        align-items: flex-start;
        padding: .25rem 0;
    }

    .timeline-badge {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        margin-top: .25rem;
    }

    .timeline-badge-task {
        background: var(--sa-blue);
    }

    .timeline-badge-appointment {
        background: var(--sa-green);
    }

    .timeline-badge-problem {
        background: #f97373;
    }

    .timeline-badge-offer {
        background: #3b82f6;
    }

    .timeline-badge-project {
        background: #f59e0b;
    }

    .timeline-content {
        flex: 1;
        min-width: 0;
    }

    .timeline-title-row {
        display: flex;
        justify-content: space-between;
        gap: .4rem;
    }

    .timeline-label {
        font-size: .72rem;
        font-weight: 600;
        color: #111827;
    }

    .timeline-time {
        font-size: .7rem;
        color: #9ca3af;
    }

    .timeline-text {
        font-size: .74rem;
        color: #4b5563;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .timeline-address {
        font-size: .7rem;
        color: #6b7280;
    }

    /* ===== Details toggle ===== */
    .employee-details {
        display: none;
        margin-top: .7rem;
        padding-top: .55rem;
        border-top: 1px dashed rgba(209, 213, 219, .9);
        font-size: .75rem;
    }

    .employee-details.active {
        display: block;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: .5rem;
    }

    .detail-group-title {
        font-weight: 600;
        margin-bottom: .15rem;
        font-size: .75rem;
        color: #111827;
    }

    .detail-list {
        padding-left: 1rem;
        margin: 0;
    }

    .detail-list li {
        margin-bottom: .18rem;
    }

    .detail-empty {
        font-size: .72rem;
    }

    .detail-kv-row {
        display: flex;
        justify-content: space-between;
        font-size: .72rem;
        margin-bottom: .1rem;
    }

    .detail-kv-row span:first-child {
        color: #9ca3af;
    }

    .detail-kv-row span:last-child {
        font-weight: 500;
        color: #111827;
    }

    /* ===== Footer ===== */
    .employee-card-footer {
        margin-top: auto;
        padding-top: .55rem;
        border-top: 1px dashed rgba(209, 213, 219, .9);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .5rem;
    }

    .footer-left {
        display: flex;
        gap: .3rem;
        align-items: center;
    }

    .btn-icon-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .25rem .45rem;
        border-radius: 999px;
    }

    .btn-icon-ghost i {
        font-size: .9rem;
    }

    .empty-state {
        margin-top: 1.5rem;
        text-align: center;
        padding: 1.5rem;
        border-radius: 14px;
        border: 1px dashed rgba(148, 163, 184, .6);
        color: #6b7280;
        background: #f9fafb;
        font-size: .85rem;
    }

    @media (max-width: 768px) {
        .daily-toolbar {
            padding: .8rem .8rem;
        }

        .daily-toolbar-left,
        .daily-toolbar-right {
            width: 100%;
            justify-content: flex-start;
        }

        .daily-search-group {
            max-width: 100%;
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
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">TAGESBERICHT</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bericht</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="daily-shell">
                {{-- Top toolbar --}}
                <div class="daily-toolbar">
                    <div class="daily-toolbar-left">
                        <div>
                            <input type="text" id="datePicker" placeholder="Datum wählen">
                        </div>

                        <div class="btn-group daily-filter-btn">
                            <div class="dropdown">
                                <button type="button"
                                        class="btn btn-outline-secondary dropdown-toggle waves-effect waves-light"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        id="filterDropdownBtn">
                                    <i class="feather icon-filter"></i>
                                    <span class="ml-1">Täglich</span>

                                </button>
                                <div class="dropdown-menu dropdown-menu-right filter">
                                    <a class="dropdown-item filter-option active" href="#" data-value="daily">Täglich</a>
                                    <a class="dropdown-item filter-option" href="#" data-value="weekly">Wöchentlich</a>
                                    <a class="dropdown-item filter-option" href="#" data-value="monthly">Monatlich</a> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="daily-toolbar-right">
                        <div class="input-group daily-search-group">
                            <input type="text" class="form-control" name="search" placeholder="Mitarbeiter suchen">
                            <div class="input-group-append">
                               <button type="button" class="btn btn-primary waves-effect waves-light">
                                    Suchen
                                </button>

                            </div>
                        </div>

                        <button class="btn btn-warning" onclick="verifyAdmin()">🔐 Admin-Zugang</button>
                    </div>
                </div>

                {{-- Cards grid --}}
                <div class="employee-grid" id="employeeGrid"></div>
                <div id="pagination" class="mt-2 text-center"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/de.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    let currentPage    = 1;
    let selectedRange  = 'daily';
    let currentSearch  = '';
    let selectedDate   = moment().format('YYYY-MM-DD');

    // ---- Helpers ------------------------------------------------------
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function formatTimeRange(start, end) {
        if (!start && !end) return '';
        const s = (start || '').toString().substring(0, 5);
        const e = (end || '').toString().substring(0, 5);
        if (s && e) return `${s}–${e}`;
        return s || e;
    }

    function labelForType(type) {
        switch (type) {
            case 'appointment': return 'Termin';
            case 'task':        return 'Aufgabe';
            case 'problem':     return 'Ticket';
            case 'offer':       return 'Angebot';
            case 'project':     return 'Projekt';
            default:            return type || '-';
        }
    }

    function badgeClassForType(type) {
        switch (type) {
            case 'appointment': return 'timeline-badge timeline-badge-appointment';
            case 'task':        return 'timeline-badge timeline-badge-task';
            case 'problem':     return 'timeline-badge timeline-badge-problem';
            case 'offer':       return 'timeline-badge timeline-badge-offer';
            case 'project':     return 'timeline-badge timeline-badge-project';
            default:            return 'timeline-badge timeline-badge-task';
        }
    }

    function renderEventPreview(events, maxItems = 4) {
        if (!events || !events.length) return '<div class="text-muted" style="font-size:.72rem;">Keine Aktivitäten im Zeitraum.</div>';

        return events.slice(0, maxItems).map(ev => `
            <div class="timeline-item">
                <div class="${badgeClassForType(ev.type)}"></div>
                <div class="timeline-content">
                    <div class="timeline-title-row">
                        <span class="timeline-label">${labelForType(ev.type)}</span>
                        <span class="timeline-time">
                            ${formatDate(ev.start_date)} ${formatTimeRange(ev.start_time, ev.end_time)}
                        </span>
                    </div>
                    <div class="timeline-text">${ev.title ?? '-'}</div>
                    ${ev.full_address ? `<div class="timeline-address">${ev.full_address}</div>` : ''}
                </div>
            </div>
        `).join('');
    }

    function renderDetailGroup(title, events, includeAddress = false) {
        if (!events || !events.length) {
            return `
                <div>
                    <div class="detail-group-title">${title}</div>
                    <div class="detail-empty text-muted">Keine ${title.toLowerCase()} im Zeitraum.</div>
                </div>
            `;
        }

        return `
            <div>
                <div class="detail-group-title">${title}</div>
                <ul class="detail-list">
                    ${events.map(ev => `
                        <li>
                            <span>${formatDate(ev.start_date)} ${formatTimeRange(ev.start_time, ev.end_time)}</span>
                            — <strong>${ev.title ?? '-'}</strong>
                            ${includeAddress && ev.full_address ? `<br><span class="text-muted">${ev.full_address}</span>` : ''}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    // ---- Fetch + render employees -------------------------------------
    function fetchEmployees() {
        $.ajax({
            url: "{{ route('daily.report.employee.list.search') }}",
            method: "GET",
            data: {
                page:   currentPage,
                filter: selectedRange,
                search: currentSearch,
                date:   selectedDate
            },
            success: function (res) {
                Swal.close();
                if (res.data && res.data.length > 0) {
                    renderCards(res.data);
                } else {
                    $('#employeeGrid').html('<div class="empty-state">Keine Mitarbeiter für diesen Zeitraum gefunden.</div>');
                }
                renderPagination(res.current_page || 1, res.last_page || 1);
            },
            error: function () {
                Swal.close();
                alert("Fehler beim Laden der Mitarbeiterdaten.");
            }
        });
    }

    function renderCards(data) {
        const grid      = $('#employeeGrid');
        const imagePath = @json(asset('images/employee'));
        const filterLbl = $('#filterDropdownBtn span').text();

        grid.empty();

        data.forEach(emp => {
            const workedHours   = (emp.worked_minutes   / 60).toFixed(2);
            const expectedHours = (emp.expected_minutes / 60).toFixed(2);
            const remainingHours= (emp.expected_minutes - emp.worked_minutes > 0
                                    ? ( (emp.expected_minutes - emp.worked_minutes) / 60 ).toFixed(2)
                                    : '0.00');
            const percent       = Math.round(emp.progress || 0);
            const borderColor   = emp.color || '#93c21c';
            const events        = emp.events || [];
            const tasks         = events.filter(e => e.type === 'task');
            const appointments  = events.filter(e => e.type === 'appointment');
            const tickets       = events.filter(e => e.type === 'problem');
            const eventsWithCoords = events.filter(e => e.full_address && e.latitude && e.longitude);
            const canShowMap    = eventsWithCoords.length > 0;
            const eventsJson    = encodeURIComponent(JSON.stringify(eventsWithCoords));
            const dateLabel     = formatDate(selectedDate);

            const cardHtml = `
                <article class="employee-card" style="border-left-color:${borderColor}">
                    <div class="employee-card-header">
                        <div class="employee-avatar-wrap">
                            <img src="${imagePath}/${emp.image || 'avatar.png'}"
                                 class="employee-avatar"
                                 onerror="this.src='${imagePath}/avatar.png';">
                            <div class="employee-avatar-status"></div>
                        </div>
                        <div class="employee-card-title">
                            <div class="employee-name">${emp.name} ${emp.lastname}</div>
                                <div class="employee-meta-chips">
                                    <span class="chip chip-soft">Mitarbeiter-ID #${emp.employee_id}</span>

                                    ${emp.status ? `<span class="chip chip-status">${emp.status}</span>` : ''}

                                    ${emp.on_leave_today ? `
                                        <span class="chip chip-status">
                                            Heute im Urlaub
                                        </span>
                                    ` : ''}

                                    ${emp.on_sick_today ? `
                                        <span class="chip chip-soft"
                                            style="border-color:#f97373;color:#b91c1c;background:rgba(248,113,113,0.1)">
                                            Krank gemeldet
                                        </span>
                                    ` : ''}

                                    ${emp.recurring_leaves_count
                                        ? `<span class="chip chip-muted">${emp.recurring_leaves_count} feste Abwesenheit(en)</span>`
                                        : ''}
                                    <span class="chip chip-muted">${dateLabel}</span>
                                </div> 
                        </div>
                    </div>

                    <div class="employee-card-body">
                        <div>
                            <div class="d-flex justify-content-between mb-25">
                                <small class="text-muted">Arbeitszeit</small>
                                <small class="text-muted">${percent}% erledigt</small>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar ${percent < 50 ? 'bg-danger' : (percent < 80 ? 'bg-warning' : 'bg-success')}"
                                     role="progressbar"
                                     style="width:${percent}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-25 tiny-rows">
                                <span>${workedHours}h geleistet</span>
                                <span>${remainingHours}h offen</span>
                                <span>${expectedHours}h geplant</span>
                            </div>
                        </div>

                        <div class="quick-stats">
                            <div class="stat-pill">
                                <span class="label">Urlaub</span>
                                <span class="value">
                                    ${(emp.leave_used_days ?? 0)} / ${(emp.annual_leave_days ?? 0)} Tage
                                </span>
                            </div>
                            <div class="stat-pill">
                                <span class="label">Resturlaub</span>
                                <span class="value">
                                    ${(emp.leave_remaining_days ?? 0)} Tage
                                </span>
                            </div>
                            <div class="stat-pill">
                                <span class="label">Kranktage (Jahr)</span>
                                <span class="value">
                                    ${(emp.sick_days ?? 0)} Tage
                                </span>
                            </div>
                            <div class="stat-pill">
                                <span class="label">Wöchentliche Abwesenheit</span>
                                <span class="value">
                                    ${(emp.recurring_weekly_days ?? 0)} Tag(e) / Woche
                                </span>
                            </div>
                        </div>




                        <div class="timeline">
                            <div class="timeline-header">
                                <span class="timeline-title">Aktivitäten (${filterLbl})</span>
                                <span class="timeline-meta">
                                    ${tasks.length} Aufgaben • ${appointments.length} Termine • ${tickets.length} Tickets
                                </span>
                            </div>
                            <div class="timeline-list">
                                ${renderEventPreview(events)}
                            </div>
                        </div>
                    </div>

                    <div class="employee-card-footer">
                        <div class="footer-left">
                            {{-- Info button: toggles details panel --}}
                            <button type="button"
                                    class="btn btn-light btn-icon-ghost btn-sm btn-info-toggle"
                                    data-emp-id="${emp.employee_id}">
                                <i class="feather icon-info"></i>
                            </button>

                            {{-- Play button: open daily report for this date --}}
                            <a class="btn btn-success btn-icon-ghost btn-sm"
                               href="/employee_daily_report/${emp.employee_id}/${selectedDate}/${selectedDate}">
                                <i class="feather icon-play"></i>
                            </a>

                            {{-- Map button: show all Termine/Aufgaben mit Adresse im Map --}}
                            ${canShowMap ? `
                                <button type="button"
                                        class="btn btn-outline-primary btn-icon-ghost btn-sm btn-map"
                                        data-events="${eventsJson}">
                                    <i class="feather icon-map-pin"></i>
                                </button>
                            ` : ``}
                        </div>
                        <div class="text-right">
                            <small class="text-muted">Zeitraum: ${filterLbl}</small>
                        </div>
                    </div>

                    {{-- Detailed info: leave, holiday, tasks, appointments, tickets --}}
                    <div class="employee-details" id="details-${emp.employee_id}">
                        <div class="detail-kv-row">
                            <span>Urlaub</span>
                            <span>${emp.leave_used_days ?? 0} / ${emp.annual_leave_days ?? 0} Tage</span>
                        </div>
                        <div class="detail-kv-row">
                            <span>Resturlaub</span>
                            <span>${emp.leave_remaining_days ?? 0} Tage</span>
                        </div>
                        <div class="detail-kv-row">
                            <span>Kranktage (Jahr)</span>
                            <span>${emp.sick_days ?? 0} Tage</span>
                        </div>
                        <div class="detail-kv-row">
                            <span>Wöchentliche Abwesenheit</span>
                            <span>${emp.recurring_weekly_days ?? 0} Tag(e) / Woche</span>
                        </div>



                        <div class="details-grid mt-1">
                            ${renderDetailGroup('Aufgaben', tasks, false)}
                            ${renderDetailGroup('Termine', appointments, true)}
                            ${renderDetailGroup('Tickets', tickets, false)}
                        </div>
                    </div>
                </article>
            `;

            grid.append(cardHtml);
        });
    }

    function renderPagination(current, last) {
        const container = $("#pagination");
        container.html('');
        if (last <= 1) return;

        for (let i = 1; i <= last; i++) {
            container.append(
                `<button class="btn btn-sm btn-${i === current ? 'primary' : 'light'} mx-1" onclick="goToPage(${i})">${i}</button>`
            );
        }
    }

    function goToPage(page) {
        currentPage = page;
        Swal.fire({
            title: 'Lade Daten...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetchEmployees();
    }

    // ---- Map: Termine + Aufgaben mit Adresse --------------------------
    function showMap(events) {
        Swal.fire({
            title: 'Standorte der Termine / Aufgaben',
            html: `<div id="leafletMap" style="width:100%; height:500px;"></div>`,
            width: '80%',
            didOpen: () => {
                const map = L.map('leafletMap').setView([51.1657, 10.4515], 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                let bounds = [];

                events.forEach(event => {
                    if (!event.latitude || !event.longitude) return;
                    const lat = parseFloat(event.latitude);
                    const lon = parseFloat(event.longitude);
                    const marker = L.marker([lat, lon]).addTo(map);
                    marker.bindPopup(`<strong>${event.title ?? labelForType(event.type)}</strong><br>${event.full_address ?? ''}`);
                    bounds.push([lat, lon]);
                });

                if (bounds.length) {
                    map.fitBounds(bounds, {padding: [30, 30]});
                }

                // optional: current user location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        const lat = pos.coords.latitude;
                        const lon = pos.coords.longitude;
                        const userMarker = L.marker([lat, lon], {
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/4870/4870326.png',
                                iconSize: [32, 32],
                            })
                        }).addTo(map);
                        userMarker.bindPopup("Ihr Standort");

                        bounds.push([lat, lon]);
                        if (bounds.length) {
                            map.fitBounds(bounds, {padding: [30, 30]});
                        }
                    });
                }
            }
        });
    }

    // ---- UI wiring ----------------------------------------------------
    // Flatpickr
    flatpickr("#datePicker", {
        locale: "de",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d. F Y",
        defaultDate: "today",
        allowInput: true,
        onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
            currentPage = 1;
            Swal.fire({
                title: 'Lade Daten...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            fetchEmployees();
        }
    });

    // Search
    $('input[name="search"]').on('input', function () {
        currentSearch = $(this).val();
        currentPage = 1;
        fetchEmployees();
    });

    // Filter (daily / weekly / monthly)
    $('.dropdown-menu .dropdown-item').on('click', function (e) {
        e.preventDefault();
        $('.dropdown-menu .dropdown-item').removeClass('active');
        $(this).addClass('active');

        selectedRange = $(this).data('value');
        $('#filterDropdownBtn span').text($(this).text());
        currentPage = 1;

        Swal.fire({
            title: 'Lade Daten...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetchEmployees();
    });

    // Delegated: info toggle
    $('#employeeGrid').on('click', '.btn-info-toggle', function () {
        const empId = $(this).data('emp-id');
        const details = $('#details-' + empId);
        details.toggleClass('active');
    });

    // Delegated: map button
    $('#employeeGrid').on('click', '.btn-map', function () {
        const events = JSON.parse(decodeURIComponent($(this).data('events')));
        showMap(events);
    });

    // Initial load
    $(document).ready(() => {
        Swal.fire({
            title: 'Lade Daten...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetchEmployees();
    });
</script>

<script>
    // Admin check stays as in your original code
    function verifyAdmin() {
        Swal.fire({
            title: 'Admin-Zugang',
            html:
                `<input type="email" id="swal-email" class="swal2-input" placeholder="E-Mail-Adresse">` +
                `<input type="password" id="swal-password" class="swal2-input" placeholder="Passwort">`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Einloggen',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
                const email = document.getElementById('swal-email').value;
                const password = document.getElementById('swal-password').value;
                if (!email || !password) {
                    Swal.showValidationMessage('Bitte E-Mail und Passwort eingeben');
                    return false;
                }
                return { email, password };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const { email, password } = result.value;

                Swal.fire({
                    title: 'Überprüfe Zugang...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('/verify-admin', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Zugriff gewährt',
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Zugriff verweigert',
                            text: 'E-Mail oder Passwort ist falsch oder keine Admin-Rechte.',
                        });
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: 'Serverfehler beim Überprüfen des Zugangs.',
                    });
                });
            }
        });
    }
</script>
@endsection
