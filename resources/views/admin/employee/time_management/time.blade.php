@extends('admin.layouts.app')

@section('title') Mein Zeitmanagement @endsection

@section('content')
<div class="app-content"> 

    <div class="content-wrapper"> 

        <div class="content-body">
            <section id="employee-plan-builder">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h4 class="card-title mb-0">
                                        Meinen Plan erstellen / ändern
                                    </h4>
                                    <div class="small text-muted">
                                        Erstelle deinen Wochen- und Monatsplan. Änderungen werden zur Freigabe gesendet.
                                    </div>
                                </div>
                                <div class="mt-50 mt-md-0">
                                    <button
                                        type="button"
                                        id="tm-toggle-builder"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="feather icon-edit-2 mr-25"></i>
                                        Plan bearbeiten
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- BUILDER CONTENT --}}
                                <div id="tm-root"
                                     data-employee-id="{{ $employee->id }}"
                                     data-hourly-rate="{{ $employee->salary_per_hour ?? 0 }}"
                                     data-default-monthly-hours="{{ $employee->working_hour ?? 0 }}"
                                     data-working-type="{{ $employee->working_type ?? '' }}">

                                    <div class="row">
                                        {{-- LEFT: calendar + controls --}}
                                        <div class="col-xl-8 col-lg-7 col-12 mb-2 mb-xl-0">
                                            <div class="tm-panel card">
                                                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                                    <div>
                                                        <h4 class="card-title mb-0">Monat auswählen</h4>
                                                        <small class="text-muted">
                                                            Wähle den Monat und trage deine täglichen Arbeitszeiten ein.
                                                        </small>
                                                    </div>
                                                    <div class="d-flex align-items-center mt-1 mt-md-0">
                                                        <input type="month" id="tmMonth" class="form-control mr-1"
                                                               value="{{ now()->format('Y-m') }}">
                                                        <button id="btnReloadMonth" class="btn btn-outline-secondary btn-sm ml-1">
                                                            Neu laden
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <div class="tm-chips mb-2">
                                                        <span class="badge badge-light-primary mr-50">
                                                            Arbeitsmodell: {{ $employee->working_type ?? '–' }}
                                                        </span>
                                                        <span class="badge badge-light-info mr-50">
                                                            Vertragliche Std./Monat: {{ $employee->working_hour ?? '–' }}
                                                        </span>
                                                        <span class="badge badge-light-success">
                                                            Lohn pro Stunde: {{ number_format($employee->salary_per_hour ?? 0, 2, ',', '.') }} €
                                                        </span>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="tmTargetHours">Zielstunden für diesen Monat</label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.25" min="0"
                                                                id="tmTargetHours"
                                                                class="form-control"
                                                                value="{{ $employee->working_hour ?? 0 }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Stunden</span>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Beispiel: 100 Stunden für einen Vertrag mit 100h im Monat.
                                                        </small>
                                                    </div>

                                                    {{-- WEEKLY PATTERN --}}
                                                    <hr>
                                                    <h6 class="mb-50">Schnellplanung – Wochenmuster</h6>
                                                    <div class="tm-week-grid mb-1">
                                                        {{-- Monday --}}
                                                        <div class="tm-week-row" data-weekday="1">
                                                            <span class="tm-week-label">Mo</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="07:00">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="17:00">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="30" min="0" max="600" style="max-width:80px"
                                                                placeholder="Pause">
                                                        </div>
                                                        {{-- Tuesday --}}
                                                        <div class="tm-week-row" data-weekday="2">
                                                            <span class="tm-week-label">Di</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="07:00">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="17:00">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="30" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                        {{-- Wednesday --}}
                                                        <div class="tm-week-row" data-weekday="3">
                                                            <span class="tm-week-label">Mi</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="07:00">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="17:00">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="30" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                        {{-- Thursday --}}
                                                        <div class="tm-week-row" data-weekday="4">
                                                            <span class="tm-week-label">Do</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="07:00">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="17:00">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="30" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                        {{-- Friday – special 09–12 --}}
                                                        <div class="tm-week-row" data-weekday="5">
                                                            <span class="tm-week-label">Fr</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="09:00">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="12:00">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="0" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                        {{-- Saturday – empty by default (nicht arbeiten) --}}
                                                        <div class="tm-week-row" data-weekday="6">
                                                            <span class="tm-week-label">Sa</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="0" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                        {{-- Sunday – empty by default --}}
                                                        <div class="tm-week-row" data-weekday="7">
                                                            <span class="tm-week-label">So</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-start" value="">
                                                            <span class="mx-25">bis</span>
                                                            <input type="time" class="form-control form-control-sm tm-week-end" value="">
                                                            <input type="number" class="form-control form-control-sm tm-week-break"
                                                                value="0" min="0" max="600" style="max-width:80px">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted d-block mb-50">
                                                        Lege dein Wochenmuster fest (z.&nbsp;B. Mo–Do 07–17 Uhr, Fr 09–12 Uhr). Leere Zeiten = frei.
                                                    </small>
                                                    <button type="button" id="tmApplyWeekPattern" class="btn btn-sm btn-outline-secondary mb-2">
                                                        Wochenmuster auf Monat anwenden
                                                    </button>

                                                    {{-- Calendar --}}
                                                    <div id="tmCalendarWrapper">
                                                        <div class="tm-calendar-header">
                                                            <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div>
                                                            <div>Fr</div><div>Sa</div><div>So</div>
                                                        </div>
                                                        <div id="tmCalendarDays" class="tm-calendar-days">
                                                            {{-- filled by JS --}}
                                                        </div>
                                                    </div>


                                                    <div id="tmCalendarWrapper">
                                                        <div class="tm-calendar-header">
                                                            <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div>
                                                            <div>Fr</div><div>Sa</div><div>So</div>
                                                        </div>
                                                        <div id="tmCalendarDays" class="tm-calendar-days">
                                                            {{-- filled by JS --}}
                                                        </div>
                                                    </div>

                                                    <small class="text-muted d-block mt-1">
                                                        Klicke auf einen Tag, um Start-, Endzeit und Pause zu setzen.
                                                    </small>
                                                </div>

                                                <div class="card-footer d-flex justify-content-between flex-wrap">
                                                    <div class="mb-1 mb-md-0">
                                                        <span class="badge badge-pill badge-light-secondary mr-50" id="tmPlanStatusBadge">
                                                            Status: <span id="tmPlanStatusText">Entwurf</span>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <button id="btnSaveDraft" class="btn btn-outline-primary mr-50">
                                                            Entwurf speichern
                                                        </button>
                                                        <button id="btnSubmitPlan" class="btn btn-primary">
                                                            Zur Genehmigung senden
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RIGHT: summary + approval --}}
                                        <div class="col-xl-4 col-lg-5 col-12">
                                            <div class="tm-panel card">
                                                <div class="card-header">
                                                    <h4 class="card-title mb-0">Übersicht & Prognose</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="tm-summary-grid mb-2">
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Geplante Stunden</div>
                                                            <div class="tm-summary-value" id="tmTotalHours">0.00 h</div>
                                                        </div>
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Zielstunden</div>
                                                            <div class="tm-summary-value" id="tmTargetHoursText">0.00 h</div>
                                                        </div>
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Differenz</div>
                                                            <div class="tm-summary-value" id="tmDiffHours">0.00 h</div>
                                                        </div>
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Ø notwendige Std / Resttag</div>
                                                            <div class="tm-summary-value" id="tmAvgRemaining">0.00 h</div>
                                                        </div>
                                                    </div>

                                                    <div class="tm-progress-wrapper mb-2">
                                                        <label class="tm-progress-label">
                                                            Fortschritt zum Ziel
                                                            <span id="tmProgressPercentText" class="float-right">0 %</span>
                                                        </label>
                                                        <div class="tm-progress-bar">
                                                            <div id="tmProgressFill"></div>
                                                        </div>
                                                    </div>

                                                    <div class="tm-summary-grid mb-2">
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Stundenlohn</div>
                                                            <div class="tm-summary-value" id="tmHourlyRate">
                                                                {{ number_format($employee->salary_per_hour ?? 0, 2, ',', '.') }} €
                                                            </div>
                                                        </div>
                                                        <div class="tm-summary-item">
                                                            <div class="tm-summary-label">Vorauss. Monatslohn</div>
                                                            <div class="tm-summary-value" id="tmEstimatedPay">0.00 €</div>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <h6 class="mb-1">Genehmigung</h6>
                                                    <div class="form-group">
                                                        <label for="tmApproveStatus">Status ändern</label>
                                                        <select id="tmApproveStatus" class="form-control form-control-sm">
                                                            <option value="">– auswählen –</option>
                                                            <option value="approved">Genehmigen</option>
                                                            <option value="rejected">Ablehnen</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="tmApproveComment">Kommentar</label>
                                                        <textarea id="tmApproveComment" rows="2"
                                                                  class="form-control form-control-sm"></textarea>
                                                    </div>
                                                    <button id="btnApprovePlan" class="btn btn-sm btn-success">
                                                        Status speichern
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- /tm-root --}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Day modal --}}
            @include('admin.employee.time_management.time-management-day-modal')
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    #tmCalendarWrapper {
        border: 1px solid #e4e7ed;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .tm-calendar-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e4e7ed;
        text-align: center;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.4rem 0;
        color: #6b7280;
    }
    .tm-calendar-header > div {
        border-right: 1px solid #e5e7eb;
    }
    .tm-calendar-header > div:last-child {
        border-right: none;
    }
    .tm-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }
    .tm-day-cell {
        min-height: 84px;
        border-top: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        padding: 0.25rem 0.35rem;
        cursor: pointer;
        position: relative;
        background: #ffffff;
        transition: background 0.15s ease, box-shadow 0.15s ease;
    }
    .tm-day-cell:nth-child(7n) {
        border-right: none;
    }
    .tm-day-cell:hover {
        background: #f8fafc;
        box-shadow: inset 0 0 0 1px #d1d5db;
    }
    .tm-day-number {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
    }
    .tm-day-info {
        margin-top: 0.15rem;
        font-size: 0.7rem;
        line-height: 1.3;
        color: #6b7280;
    }
    .tm-day-hours-badge {
        position: absolute;
        bottom: 0.3rem;
        right: 0.3rem;
        font-size: 0.7rem;
        padding: 0.1rem 0.35rem;
        border-radius: 999px;
        background: #e5f2ff;
        color: #1d4ed8;
    }
    .tm-day-today {
        box-shadow: inset 0 0 0 2px #3b82f6;
    }

    .tm-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-row-gap: 0.75rem;
        grid-column-gap: 0.75rem;
    }
    .tm-summary-item {
        padding: 0.5rem 0.6rem;
        border-radius: 0.5rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }
    .tm-summary-label {
        font-size: 0.7rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .tm-summary-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
    }
    .tm-progress-wrapper {
        margin-top: 0.5rem;
    }
    .tm-progress-label {
        font-size: 0.75rem;
        color: #4b5563;
        display: block;
        margin-bottom: 0.2rem;
    }
    .tm-progress-bar {
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
        position: relative;
    }
    .tm-progress-bar > div {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 0%;
        border-radius: 999px;
        background: linear-gradient(90deg, #60a5fa, #22c55e);
        transition: width 0.25s ease;
    }

    .tm-chips .badge {
        font-size: 0.7rem;
        padding: 0.35rem 0.6rem;
    }

    .tm-panel.card {
        border-radius: 0.75rem;
        border-color: #e5e7eb;
    }

    @media (max-width: 767.98px) {
        .tm-day-cell { min-height: 72px; }
        .tm-summary-grid { grid-template-columns: 1fr; }
    }

    .tm-week-grid {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .tm-week-row {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .tm-week-label {
        width: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
    }
    .tm-week-row .form-control-sm {
        font-size: 0.75rem;
        padding: 0.2rem 0.35rem;
    }

</style>
@endsection
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // === Helper: simple log if something goes wrong ===
    function logError(tag, err) {
        console.error('[TimeManagement][' + tag + ']', err);
    }

    // SweetAlert wrapper (fallback to native alert if Swal not loaded)
    function showAlert(icon, title, text) {
        if (window.Swal && Swal.fire) {
            Swal.fire({
                icon: icon || 'info',
                title: title || '',
                text: text || ''
            });
        } else {
            alert(text || title || '');
        }
    }

    const elApplyWeekPattern = document.getElementById('tmApplyWeekPattern');
    const weekRows = document.querySelectorAll('.tm-week-row');

    // === Toggle builder (optional) ============================================
    const toggleBtn = document.getElementById('tm-toggle-builder');
    const tmRootCardBody = document.querySelector('#tm-root')?.closest('.card-body');

    if (toggleBtn && tmRootCardBody) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = tmRootCardBody.style.display === 'none';
            tmRootCardBody.style.display = isHidden ? '' : 'none';
        });
    }

    // === Basic element references ============================================
    const rootEl = document.getElementById('tm-root');
    if (!rootEl) {
        logError('init', 'tm-root not found');
        return;
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const employeeId          = Number(rootEl.dataset.employeeId || '0');
    const defaultHourlyRate   = parseFloat(rootEl.dataset.hourlyRate || '0');
    const defaultMonthlyHours = parseFloat(rootEl.dataset.defaultMonthlyHours || '0');

    const routes = {
        load:      "{{ route('time_management.load') }}",
        save:      "{{ route('time_management.save') }}",
        submit:    "{{ route('time_management.submit') }}",
        statusTpl: "{{ route('time_management.status', ['plan' => 'PLAN_ID']) }}",
    };

    const elMonth        = document.getElementById('tmMonth');
    const elTargetHours  = document.getElementById('tmTargetHours');
    const elCalendarDays = document.getElementById('tmCalendarDays');

    const elStatusText  = document.getElementById('tmPlanStatusText');
    const elStatusBadge = document.getElementById('tmPlanStatusBadge');

    const elTotalHours   = document.getElementById('tmTotalHours');
    const elTargetText   = document.getElementById('tmTargetHoursText');
    const elDiffHours    = document.getElementById('tmDiffHours');
    const elAvgRemaining = document.getElementById('tmAvgRemaining');
    const elProgressPct  = document.getElementById('tmProgressPercentText');
    const elProgressFill = document.getElementById('tmProgressFill');
    const elEstimatedPay = document.getElementById('tmEstimatedPay');

    const elBtnReload   = document.getElementById('btnReloadMonth');
    const elBtnSave     = document.getElementById('btnSaveDraft');
    const elBtnSubmit   = document.getElementById('btnSubmitPlan');
    const elBtnApprove  = document.getElementById('btnApprovePlan');
    const elApproveSel  = document.getElementById('tmApproveStatus');
    const elApproveNote = document.getElementById('tmApproveComment');

    // Modal elements
    const elDayModal     = document.getElementById('tmDayModal');
    const elModalDayLbl  = document.getElementById('tmModalDayLabel');
    const elModalDate    = document.getElementById('tmModalDate');
    const elModalStart   = document.getElementById('tmModalStart');
    const elModalEnd     = document.getElementById('tmModalEnd');
    const elModalBreak   = document.getElementById('tmModalBreak');
    const elModalHours   = document.getElementById('tmModalHoursValue');
    const elModalSaveBtn = document.getElementById('tmModalSaveDay');
    const elModalDelBtn  = document.getElementById('tmModalDeleteDay');

    // Close modal buttons (Bootstrap markup has data-dismiss="modal")
    if (elDayModal) {
        const closeButtons = elDayModal.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', closeDayModal);
        });
    }

    // === State =================================================================
    const state = {
        planId: null,
        year: null,
        month: null,
        targetHours: defaultMonthlyHours || 0,
        totalHours: 0,
        status: 'draft',
        days: {} // { 'YYYY-MM-DD': { start, end, breakMinutes, hours } }
    };

    // === Helpers ===============================================================
    function parseMonthInput(value) {
        if (!value || !value.includes('-')) {
            const now = new Date();
            return { year: now.getFullYear(), month: now.getMonth() + 1 };
        }
        const [y, m] = value.split('-');
        return { year: Number(y), month: Number(m) };
    }

    function computeHours(startStr, endStr, breakMinutes) {
        if (!startStr || !endStr) return 0;
        const [sh, sm] = startStr.split(':').map(Number);
        const [eh, em] = endStr.split(':').map(Number);
        const start = sh * 60 + sm;
        const end   = eh * 60 + em;
        let diff = end - start - (breakMinutes || 0);
        if (diff <= 0) return 0;
        return Math.round((diff / 60) * 100) / 100;
    }

    function buildWeekPattern() {
        const pattern = {}; // weekday(1–7) => {start,end,breakMinutes}

        weekRows.forEach(row => {
            const weekday = Number(row.dataset.weekday || '0');
            if (!weekday) return;

            const startInput = row.querySelector('.tm-week-start');
            const endInput   = row.querySelector('.tm-week-end');
            const breakInput = row.querySelector('.tm-week-break');

            if (!startInput || !endInput || !breakInput) return;

            const start = startInput.value;
            const end   = endInput.value;
            const brMin = parseInt(breakInput.value || '0', 10);

            // Empty start OR end => this weekday is free
            if (!start || !end) return;

            pattern[weekday] = {
                start: start,
                end: end,
                breakMinutes: isNaN(brMin) ? 0 : brMin
            };
        });

        return pattern;
    }

    function applyWeekPatternToMonth() {
        if (!elMonth) return;

        const { year, month } = parseMonthInput(elMonth.value);
        const pattern = buildWeekPattern();
        const daysInMonth = new Date(year, month, 0).getDate();

        // For each day of month, check weekday and apply pattern
        for (let d = 1; d <= daysInMonth; d++) {
            const dateObj = new Date(year, month - 1, d);
            // JS: getDay() => 0=Sun,...,6=Sat; we want 1=Mon,...,7=Sun
            const weekday = ((dateObj.getDay() + 6) % 7) + 1;

            const p = pattern[weekday];
            if (!p) continue; // no working time for this weekday

            const hours = computeHours(p.start, p.end, p.breakMinutes);
            if (hours <= 0) continue;

            const iso = dateObj.toISOString().slice(0, 10);
            state.days[iso] = {
                start: p.start,
                end: p.end,
                breakMinutes: p.breakMinutes,
                hours: hours
            };
        }

        renderCalendar(year, month);
        recalcSummary();
    }

    function setStatus(status) {
        state.status = status;
        let label = 'Entwurf';
        let cls   = 'badge-light-secondary';

        if (status === 'pending') {
            label = 'Zur Genehmigung gesendet';
            cls   = 'badge-light-warning';
        } else if (status === 'approved') {
            label = 'Genehmigt';
            cls   = 'badge-light-success';
        } else if (status === 'rejected') {
            label = 'Abgelehnt';
            cls   = 'badge-light-danger';
        }

        if (elStatusText)  elStatusText.textContent = label;
        if (elStatusBadge) {
            elStatusBadge.classList.remove(
                'badge-light-secondary','badge-light-warning',
                'badge-light-success','badge-light-danger'
            );
            elStatusBadge.classList.add(cls);
        }
    }

    function recalcSummary() {
        if (!state.year || !state.month) return;

        const monthKey = state.year + '-' + String(state.month).padStart(2, '0');
        const daysInMonth = new Date(state.year, state.month, 0).getDate();

        let total = 0;
        let usedDays = 0;

        Object.keys(state.days).forEach(date => {
            if (date.startsWith(monthKey)) {
                total += state.days[date].hours || 0;
                usedDays++;
            }
        });

        state.totalHours = total;
        const target = Number(state.targetHours || 0);
        const diff   = total - target;
        const remainingHours = target - total;
        const remainingDays  = Math.max(daysInMonth - usedDays, 0);
        const avgNeeded = (remainingHours > 0 && remainingDays > 0)
            ? remainingHours / remainingDays
            : 0;

        if (elTotalHours)   elTotalHours.textContent   = total.toFixed(2)  + ' h';
        if (elTargetText)   elTargetText.textContent   = target.toFixed(2) + ' h';
        if (elDiffHours)    elDiffHours.textContent    = diff.toFixed(2)   + ' h';
        if (elAvgRemaining) elAvgRemaining.textContent = avgNeeded.toFixed(2) + ' h';

        let percent = 0;
        if (target > 0) percent = Math.round((total / target) * 100);
        percent = Math.max(0, Math.min(percent, 200));

        if (elProgressPct)  elProgressPct.textContent  = percent + ' %';
        if (elProgressFill) elProgressFill.style.width = Math.min(percent, 100) + '%';

        const rate = (typeof window.tmHourlyRateOverride !== 'undefined')
            ? Number(window.tmHourlyRateOverride || 0)
            : defaultHourlyRate || 0;

        const estimatedPay = total * rate;
        if (elEstimatedPay) {
            elEstimatedPay.textContent = estimatedPay.toFixed(2).replace('.', ',') + ' €';
        }
    }

    // === Calendar rendering ====================================================
    function renderCalendar(year, month) {
        state.year = year;
        state.month = month;

        if (!elCalendarDays) return;
        elCalendarDays.innerHTML = '';

        const first = new Date(year, month - 1, 1);
        const firstWeekday = (first.getDay() + 6) % 7; // Monday = 0
        const daysInMonth = new Date(year, month, 0).getDate();

        // empty cells before first
        for (let i = 0; i < firstWeekday; i++) {
            const empty = document.createElement('div');
            empty.className = 'tm-day-cell tm-day-empty';
            elCalendarDays.appendChild(empty);
        }

        const today = new Date();
        const todayStr = today.toISOString().slice(0, 10);

        for (let d = 1; d <= daysInMonth; d++) {
            const dateObj = new Date(year, month - 1, d);
            const iso = dateObj.toISOString().slice(0, 10);
            const entry = state.days[iso];

            const cell = document.createElement('div');
            cell.className = 'tm-day-cell';
            cell.dataset.date = iso;
            if (iso === todayStr) cell.classList.add('tm-day-today');

            const num = document.createElement('div');
            num.className = 'tm-day-number';
            num.textContent = d;

            const info = document.createElement('div');
            info.className = 'tm-day-info';

            if (entry) {
                info.innerHTML =
                    (entry.start || '') + '–' + (entry.end || '') +
                    (entry.breakMinutes
                        ? '<br>Pause: ' + entry.breakMinutes + ' min'
                        : '');

                const badge = document.createElement('div');
                badge.className = 'tm-day-hours-badge';
                badge.textContent = entry.hours.toFixed(2) + ' h';
                cell.appendChild(badge);
            } else {
                info.textContent = 'Klicken zum Planen';
            }

            cell.appendChild(num);
            cell.appendChild(info);
            cell.addEventListener('click', function () {
                openDayModal(iso);
            });

            elCalendarDays.appendChild(cell);
        }
    }

    // === Modal open / close ====================================================
    function openDayModal(isoDate) {
        if (!elDayModal) return;

        const entry = state.days[isoDate];
        elModalDate.value = isoDate;

        const dateObj = new Date(isoDate);
        const opt = { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' };
        if (elModalDayLbl) {
            elModalDayLbl.textContent = dateObj.toLocaleDateString('de-DE', opt);
        }

        if (entry) {
            elModalStart.value = entry.start;
            elModalEnd.value   = entry.end;
            elModalBreak.value = entry.breakMinutes;
            if (elModalHours) elModalHours.textContent = entry.hours.toFixed(2);
        } else {
            elModalStart.value = '';
            elModalEnd.value   = '';
            elModalBreak.value = 0;
            if (elModalHours) elModalHours.textContent = '0.00';
        }

        // Show modal without Bootstrap JS
        elDayModal.classList.add('show');
        elDayModal.style.display = 'block';
        elDayModal.removeAttribute('aria-hidden');
        elDayModal.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
    }

    function closeDayModal() {
        if (!elDayModal) return;
        elDayModal.classList.remove('show');
        elDayModal.style.display = 'none';
        elDayModal.setAttribute('aria-hidden', 'true');
        elDayModal.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
    }

    function updateModalPreview() {
        const start = elModalStart.value;
        const end   = elModalEnd.value;
        const breakMin = parseInt(elModalBreak.value || '0', 10);
        const h = computeHours(start, end, breakMin);
        if (elModalHours) elModalHours.textContent = h.toFixed(2);
    }

    if (elModalStart) elModalStart.addEventListener('change', updateModalPreview);
    if (elModalEnd)   elModalEnd.addEventListener('change', updateModalPreview);
    if (elModalBreak) elModalBreak.addEventListener('input', updateModalPreview);

    if (elModalSaveBtn) {
        elModalSaveBtn.addEventListener('click', function () {
            const date = elModalDate.value;
            const start = elModalStart.value;
            const end   = elModalEnd.value;
            const breakMin = parseInt(elModalBreak.value || '0', 10);
            const h = computeHours(start, end, breakMin);

            if (!start || !end || h <= 0) {
                showAlert(
                    'warning',
                    'Ungültige Zeiten',
                    'Bitte gültige Start- und Endzeit eingeben.'
                );
                return;
            }

            state.days[date] = {
                start: start,
                end: end,
                breakMinutes: breakMin,
                hours: h
            };

            closeDayModal();
            renderCalendar(state.year, state.month);
            recalcSummary();
        });
    }

    if (elModalDelBtn) {
        elModalDelBtn.addEventListener('click', function () {
            const date = elModalDate.value;
            if (state.days[date]) delete state.days[date];
            closeDayModal();
            renderCalendar(state.year, state.month);
            recalcSummary();
        });
    }

    // === AJAX using fetch =======================================================
    async function getJson(url) {
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return await res.json();
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });

        const text = await res.text();
        let json;
        try {
            json = text ? JSON.parse(text) : {};
        } catch (e) {
            json = { raw: text };
        }

        if (!res.ok) {
            const err = new Error('HTTP ' + res.status);
            err.status = res.status;
            err.body   = json;
            throw err;
        }

        return json;
    }


    async function loadMonthData() {
        const { year, month } = parseMonthInput(elMonth ? elMonth.value : null);

        // Always render something locally
        renderCalendar(year, month);
        recalcSummary();

        try {
            const url = routes.load +
                '?employee_id=' + encodeURIComponent(employeeId) +
                '&year=' + encodeURIComponent(year) +
                '&month=' + encodeURIComponent(month);

            const res = await getJson(url);

            state.days = {};
            let target = defaultMonthlyHours || 0;
            let rate   = defaultHourlyRate || 0;

            if (res.employee) {
                if (res.employee.working_hour != null) target = res.employee.working_hour;
                if (res.employee.salary_per_hour != null) rate = res.employee.salary_per_hour;
            }

            if (res.plan) {
                if (res.plan.target_hours != null) target = res.plan.target_hours;
                if (res.plan.hourly_rate != null) rate = res.plan.hourly_rate;
            }

            state.targetHours = target;
            if (elTargetHours) elTargetHours.value = target;
            window.tmHourlyRateOverride = rate;

            if (res.plan) {
                state.planId = res.plan.id;
                setStatus(res.plan.status);
            } else {
                state.planId = null;
                setStatus('draft');
            }

            (res.entries || []).forEach(e => {
                state.days[e.date] = {
                    start: e.start_time,
                    end: e.end_time,
                    breakMinutes: e.break_minutes,
                    hours: parseFloat(e.hours || 0)
                };
            });

            renderCalendar(year, month);
            recalcSummary();
        } catch (err) {
            logError('loadMonth', err);
            // optional: showAlert('error','Fehler','Fehler beim Laden des Monats.');
        }
    }

    async function saveDraft(callback) {
        const { year, month } = parseMonthInput(elMonth ? elMonth.value : null);

        const daysArr = Object.keys(state.days).map(date => {
            const d = state.days[date];
            return {
                date: date,
                start_time: d.start,
                end_time: d.end,
                break_minutes: d.breakMinutes
            };
        });

        const target = elTargetHours ? parseFloat(elTargetHours.value || '0') : 0;
        state.targetHours = target;

        try {
            const res = await postJson(routes.save, {
                employee_id: employeeId,
                year: year,
                month: month,
                target_hours: target,
                days: daysArr
            });

            if (res.plan_id) state.planId = res.plan_id;
            if (typeof res.target_hours !== 'undefined') {
                state.targetHours = res.target_hours;
                if (elTargetHours) elTargetHours.value = res.target_hours;
            }
            recalcSummary();
            if (callback) callback(true);
        } catch (err) {
            logError('saveDraft', err);
            showAlert(
                'error',
                'Fehler',
                'Fehler beim Speichern des Entwurfs.'
            );
            if (callback) callback(false);
        }
    }

    async function submitPlan() {
        // do not submit again if already approved
        if (state.status === 'approved') {
            showAlert('info', 'Hinweis', 'Der Zeitplan ist bereits genehmigt.');
            return;
        }

        if (!state.planId) {
            // first save, then submit
            return saveDraft(ok => {
                if (ok && state.planId) doSubmitExisting();
            });
        }

        doSubmitExisting();
    }


    async function doSubmitExisting() {
        try {
            const res = await postJson(routes.submit, {
                plan_id: state.planId
            });

            setStatus(res.status || 'pending');
            showAlert('success', 'Gesendet', 'Zeitplan wurde zur Genehmigung gesendet.');
        } catch (err) {
            // If backend says "already approved", show that info instead of generic error
            if (err.status === 422 && err.body && err.body.message) {
                // optional: backend could return current_status, sync it:
                if (err.body.current_status) {
                    setStatus(err.body.current_status);
                } else {
                    // in your case it's already approved
                    setStatus('approved');
                }

                showAlert('info', 'Hinweis', err.body.message);
                return;
            }

            logError('submitPlan', err);
            showAlert('error', 'Fehler', 'Fehler beim Senden zur Genehmigung.');
        }
    }


    async function approvePlan() {
        if (!state.planId) {
            showAlert(
                'warning',
                'Kein Plan',
                'Kein Plan vorhanden.'
            );
            return;
        }
        const status = elApproveSel ? elApproveSel.value : '';
        const comment = elApproveNote ? elApproveNote.value : '';
        if (!status) {
            showAlert(
                'warning',
                'Hinweis',
                'Bitte einen Status auswählen.'
            );
            return;
        }

        const url = routes.statusTpl.replace('PLAN_ID', state.planId);

        try {
            const res = await postJson(url, { status, comment });
            setStatus(res.status || status);
            showAlert(
                'success',
                'Aktualisiert',
                'Status aktualisiert.'
            );
        } catch (err) {
            logError('approvePlan', err);
            showAlert(
                'error',
                'Fehler',
                'Fehler beim Aktualisieren des Status.'
            );
        }
    }

    // === Event bindings ========================================================
    if (elBtnReload)  elBtnReload.addEventListener('click', loadMonthData);
    if (elBtnSave)    elBtnSave.addEventListener('click', () => saveDraft());
    if (elBtnSubmit)  elBtnSubmit.addEventListener('click', submitPlan);
    if (elBtnApprove) elBtnApprove.addEventListener('click', approvePlan);

    if (elApplyWeekPattern) {
        elApplyWeekPattern.addEventListener('click', applyWeekPatternToMonth);
    }

    if (elTargetHours) {
        elTargetHours.addEventListener('change', function () {
            state.targetHours = parseFloat(this.value || '0');
            recalcSummary();
        });
    }

    // === Initial load ==========================================================
    loadMonthData();
});

function showAlert(icon, title, text) {
    if (window.Swal && Swal.fire) {
        Swal.fire({
            icon: icon || 'info',
            title: title || '',
            text: text || ''
        });
    } else {
        alert(text || title || '');
    }
}

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
                label: 'Mein Zeitmanagement',
                url: "{{ url()->current() }}",
                clickable: false
            },

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush