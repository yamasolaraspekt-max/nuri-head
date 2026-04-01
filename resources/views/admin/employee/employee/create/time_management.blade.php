<div id="tm-embed-root"
     data-employee-id="{{ $employeeId }}"
     data-load-url="{{ route('time_managements.load') }}"
     data-save-url="{{ route('time_managements.save') }}"
     data-submit-url="{{ route('time_managements.submit') }}"
     data-status-url-tpl="{{ route('time_managements.status', ['plan' => 'PLAN_ID']) }}">

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h4 class="card-title mb-0">Arbeitszeit – Monatsübersicht</h4>
                <small class="text-muted">
                    Klicke einen Tag im Kalender, um Start-/Endzeit, Pause und Stunden zu setzen.
                </small>
            </div>
            <div class="d-flex flex-wrap align-items-center mt-1 mt-md-0">
                <input type="month" id="tmEmbedMonth" class="form-control mr-50 mb-50 mb-sm-0"
                       value="{{ now()->format('Y-m') }}">
                <button type="button" id="tmEmbedReload" class="btn btn-outline-secondary btn-sm mr-25 mb-50 mb-sm-0">
                    Neu laden
                </button>
                <button type="button" id="tmEmbedSaveMonth" class="btn btn-primary btn-sm mr-25 mb-50 mb-sm-0">
                    Änderungen speichern
                </button>
                <button type="button" id="tmEmbedSubmit" class="btn btn-warning btn-sm">
                    Zur Genehmigung senden
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Plan summary --}}
            <div class="row mb-1">
                <div class="col-md-8">
                    <div class="tm-embed-summary-grid">
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Geplante Stunden</div>
                            <div class="tm-embed-summary-value" id="tmEmbedTotalHours">0.00 h</div>
                        </div>
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Zielstunden</div>
                            <div class="tm-embed-summary-value" id="tmEmbedTargetHours">0.00 h</div>
                        </div>
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Differenz</div>
                            <div class="tm-embed-summary-value" id="tmEmbedDiffHours">0.00 h</div>
                        </div>
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Stundenlohn</div>
                            <div class="tm-embed-summary-value" id="tmEmbedHourlyRate">0,00 €</div>
                        </div>
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Vorauss. Monatslohn</div>
                            <div class="tm-embed-summary-value" id="tmEmbedEstimatedPay">0,00 €</div>
                        </div>
                        <div class="tm-embed-summary-item">
                            <div class="tm-embed-summary-label">Plan-Status</div>
                            <div>
                                <span id="tmEmbedStatusBadge"
                                      class="badge badge-light-secondary">
                                    <span id="tmEmbedStatusText">Entwurf</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status change in profile --}}
                <div class="col-md-4">
                    <div class="form-group mb-50">
                        <label for="tmEmbedStatusSelect">Status ändern</label>
                        <select id="tmEmbedStatusSelect" class="form-control form-control-sm">
                            <option value="">– unverändert –</option>
                            <option value="approved">Genehmigen</option>
                            <option value="rejected">Ablehnen</option>
                            <option value="pending">Zur Prüfung</option>
                        </select>
                    </div>
                    <div class="form-group mb-50">
                        <label for="tmEmbedComment">Kommentar</label>
                        <textarea id="tmEmbedComment" rows="2"
                                  class="form-control form-control-sm"
                                  placeholder="Optionaler Kommentar für den Mitarbeiter …"></textarea>
                    </div>
                    <button type="button" id="tmEmbedSaveStatus" class="btn btn-sm btn-success btn-block">
                        Status speichern
                    </button>
                </div>
            </div>

            <hr>

            {{-- WEEKLY PATTERN (simple employee 07–16 etc.) --}}
            <div class="mb-1">
                <h6 class="mb-50">Schnellplanung – Wochenmuster</h6>
                <div class="row align-items-end">
                    <div class="col-sm-3 mb-50">
                        <label for="tmEmbedPatternStart">Start</label>
                        <input type="time"
                               id="tmEmbedPatternStart"
                               class="form-control form-control-sm"
                               value="07:00">
                    </div>
                    <div class="col-sm-3 mb-50">
                        <label for="tmEmbedPatternEnd">Ende</label>
                        <input type="time"
                               id="tmEmbedPatternEnd"
                               class="form-control form-control-sm"
                               value="16:00">
                    </div>
                    <div class="col-sm-2 mb-50">
                        <label for="tmEmbedPatternBreak">Pause (min)</label>
                        <input type="number"
                               id="tmEmbedPatternBreak"
                               class="form-control form-control-sm"
                               value="30" min="0" max="600">
                    </div>
                    <div class="col-sm-4 mb-50">
                        <label>Wochentage</label>
                        <div class="d-flex flex-wrap align-items-center">
                            {{-- 1=Mo, 2=Di, 3=Mi, 4=Do, 5=Fr, 6=Sa, 7=So --}}
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay1" data-weekday="1" checked>
                                <label class="custom-control-label" for="tmPatternDay1">Mo</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay2" data-weekday="2" checked>
                                <label class="custom-control-label" for="tmPatternDay2">Di</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay3" data-weekday="3" checked>
                                <label class="custom-control-label" for="tmPatternDay3">Mi</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay4" data-weekday="4" checked>
                                <label class="custom-control-label" for="tmPatternDay4">Do</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay5" data-weekday="5" checked>
                                <label class="custom-control-label" for="tmPatternDay5">Fr</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-25">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay6" data-weekday="6">
                                <label class="custom-control-label" for="tmPatternDay6">Sa</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input tm-embed-pattern-day"
                                       id="tmPatternDay7" data-weekday="7">
                                <label class="custom-control-label" for="tmPatternDay7">So</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap">
                    <button type="button" id="tmEmbedPatternPresetSimple"
                            class="btn btn-sm btn-outline-primary mr-50 mb-50">
                        Standard: Mo–Fr 07–16 (30 Min Pause)
                    </button>
                    <button type="button" id="tmEmbedApplyPattern"
                            class="btn btn-sm btn-outline-secondary mb-50">
                        Muster auf Monat anwenden
                    </button>
                </div>

                <small class="text-muted d-block">
                    Beispiel: “Einfacher Mitarbeiter” – Mo–Fr 07:00–16:00, 30 Minuten Pause.
                    Wochenenden bleiben leer (frei), falls nicht ausgewählt.
                </small>
            </div>

            <hr>

            {{-- CALENDAR (monthly view like main time management) --}}
            <div id="tmEmbedCalendarWrapper">
                <div class="tm-embed-calendar-header">
                    <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div>
                    <div>Fr</div><div>Sa</div><div>So</div>
                </div>
                <div id="tmEmbedCalendarDays" class="tm-embed-calendar-days">
                    {{-- filled by JS --}}
                </div>
            </div>
            <small class="text-muted d-block mt-50">
                Klicke auf einen Tag, um Start-/Endzeit und Pause festzulegen.
            </small>
        </div>
    </div>
</div>

{{-- Day modal for embed --}}
<div class="modal fade" id="tmEmbedDayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header py-50 px-1">
                <h5 class="modal-title mb-0" id="tmEmbedModalDayLabel">Tag bearbeiten</h5>
                <button type="button" class="close" aria-label="Close" onclick="window.tmEmbedCloseDayModal && window.tmEmbedCloseDayModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-50 px-1">
                <input type="hidden" id="tmEmbedModalDate">
                <div class="form-group mb-50">
                    <label for="tmEmbedModalStart">Start</label>
                    <input type="time" id="tmEmbedModalStart" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-50">
                    <label for="tmEmbedModalEnd">Ende</label>
                    <input type="time" id="tmEmbedModalEnd" class="form-control form-control-sm">
                </div>
                <div class="form-group mb-50">
                    <label for="tmEmbedModalBreak">Pause (Minuten)</label>
                    <input type="number" id="tmEmbedModalBreak" class="form-control form-control-sm"
                           min="0" max="600" value="30">
                </div>
                <div class="form-group mb-0">
                    <label>Berechnete Stunden</label>
                    <div class="font-weight-bold" id="tmEmbedModalHoursValue">0.00</div>
                </div>
            </div>
            <div class="modal-footer py-50 px-1 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="tmEmbedModalDeleteDay">
                    Löschen
                </button>
                <div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mr-50"
                            onclick="window.tmEmbedCloseDayModal && window.tmEmbedCloseDayModal()">
                        Abbrechen
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="tmEmbedModalSaveDay">
                        Speichern
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles for the embed --}}
<style>
    #tm-embed-root .tm-embed-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-column-gap: 0.75rem;
        grid-row-gap: 0.5rem;
    }
    #tm-embed-root .tm-embed-summary-item {
        padding: 0.45rem 0.55rem;
        border-radius: 0.5rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }
    #tm-embed-root .tm-embed-summary-label {
        font-size: 0.7rem;
        color: #6b7280;
        margin-bottom: 0.1rem;
    }
    #tm-embed-root .tm-embed-summary-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
    }
    @media (max-width: 991.98px) {
        #tm-embed-root .tm-embed-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 575.98px) {
        #tm-embed-root .tm-embed-summary-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Calendar styling (same logic as main time management) */
    #tmEmbedCalendarWrapper {
        border: 1px solid #e4e7ed;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .tm-embed-calendar-header {
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
    .tm-embed-calendar-header > div {
        border-right: 1px solid #e5e7eb;
    }
    .tm-embed-calendar-header > div:last-child {
        border-right: none;
    }
    .tm-embed-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }
    .tm-embed-day-cell {
        min-height: 84px;
        border-top: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        padding: 0.25rem 0.35rem;
        cursor: pointer;
        position: relative;
        background: #ffffff;
        transition: background 0.15s ease, box-shadow 0.15s ease;
    }
    .tm-embed-day-cell:nth-child(7n) {
        border-right: none;
    }
    .tm-embed-day-cell:hover {
        background: #f8fafc;
        box-shadow: inset 0 0 0 1px #d1d5db;
    }
    .tm-embed-day-empty {
        background: #f9fafb;
        cursor: default;
    }
    .tm-embed-day-number {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
    }
    .tm-embed-day-info {
        margin-top: 0.15rem;
        font-size: 0.7rem;
        line-height: 1.3;
        color: #6b7280;
    }
    .tm-embed-day-hours-badge {
        position: absolute;
        bottom: 0.3rem;
        right: 0.3rem;
        font-size: 0.7rem;
        padding: 0.1rem 0.35rem;
        border-radius: 999px;
        background: #e5f2ff;
        color: #1d4ed8;
    }
    .tm-embed-day-today {
        box-shadow: inset 0 0 0 2px #3b82f6;
    }

    @media (max-width: 767.98px) {
        .tm-embed-day-cell { min-height: 72px; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('tm-embed-root');
    if (!root) return;

    const employeeId   = Number(root.dataset.employeeId || '0');
    const loadUrl      = root.dataset.loadUrl;
    const saveUrl      = root.dataset.saveUrl;
    const submitUrl    = root.dataset.submitUrl;
    const statusUrlTpl = root.dataset.statusUrlTpl;

    const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const elMonth         = document.getElementById('tmEmbedMonth');
    const elReload        = document.getElementById('tmEmbedReload');
    const elSaveMonth     = document.getElementById('tmEmbedSaveMonth');
    const elSubmit        = document.getElementById('tmEmbedSubmit');

    const elCalendarDays  = document.getElementById('tmEmbedCalendarDays');

    const elTotalHours    = document.getElementById('tmEmbedTotalHours');
    const elTargetHours   = document.getElementById('tmEmbedTargetHours');
    const elDiffHours     = document.getElementById('tmEmbedDiffHours');
    const elHourlyRate    = document.getElementById('tmEmbedHourlyRate');
    const elEstimatedPay  = document.getElementById('tmEmbedEstimatedPay');

    const elStatusBadge   = document.getElementById('tmEmbedStatusBadge');
    const elStatusText    = document.getElementById('tmEmbedStatusText');
    const elStatusSelect  = document.getElementById('tmEmbedStatusSelect');
    const elComment       = document.getElementById('tmEmbedComment');
    const elSaveStatus    = document.getElementById('tmEmbedSaveStatus');

    // pattern controls
    const elPatternStart  = document.getElementById('tmEmbedPatternStart');
    const elPatternEnd    = document.getElementById('tmEmbedPatternEnd');
    const elPatternBreak  = document.getElementById('tmEmbedPatternBreak');
    const elPatternDays   = document.querySelectorAll('.tm-embed-pattern-day');
    const elPatternPreset = document.getElementById('tmEmbedPatternPresetSimple');
    const elApplyPattern  = document.getElementById('tmEmbedApplyPattern');

    // Day modal elements
    const elDayModal    = document.getElementById('tmEmbedDayModal');
    const elModalDayLbl = document.getElementById('tmEmbedModalDayLabel');
    const elModalDate   = document.getElementById('tmEmbedModalDate');
    const elModalStart  = document.getElementById('tmEmbedModalStart');
    const elModalEnd    = document.getElementById('tmEmbedModalEnd');
    const elModalBreak  = document.getElementById('tmEmbedModalBreak');
    const elModalHours  = document.getElementById('tmEmbedModalHoursValue');
    const elModalSave   = document.getElementById('tmEmbedModalSaveDay');
    const elModalDelete = document.getElementById('tmEmbedModalDeleteDay');

    function showAlert(icon, title, text) {
        if (window.Swal && Swal.fire) {
            Swal.fire({ icon: icon || 'info', title: title || '', text: text || '' });
        } else {
            alert(text || title || '');
        }
    }

    function logError(tag, err) {
        console.error('[TM-Embed][' + tag + ']', err);
    }

    const state = {
        planId: null,
        year: null,
        month: null,
        targetHours: 0,
        totalHours: 0,
        hourlyRate: 0,
        status: 'draft',
        days: {} // iso => {start,end,breakMinutes,hours}
    };

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

    function setStatusUI(status) {
        state.status = status || 'draft';

        let label = 'Entwurf';
        let cls   = 'badge-light-secondary';

        if (state.status === 'pending') {
            label = 'Zur Prüfung';
            cls   = 'badge-light-warning';
        } else if (state.status === 'approved') {
            label = 'Genehmigt';
            cls   = 'badge-light-success';
        } else if (state.status === 'rejected') {
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

        let total = 0;
        Object.keys(state.days).forEach(date => {
            if (date.startsWith(monthKey)) {
                total += state.days[date].hours || 0;
            }
        });

        state.totalHours = total;
        const target = Number(state.targetHours || 0);
        const diff   = total - target;

        if (elTotalHours)   elTotalHours.textContent   = total.toFixed(2) + ' h';
        if (elTargetHours)  elTargetHours.textContent  = target.toFixed(2) + ' h';
        if (elDiffHours)    elDiffHours.textContent    = diff.toFixed(2) + ' h';

        const rate = Number(state.hourlyRate || 0);
        if (elHourlyRate)   elHourlyRate.textContent   = rate.toFixed(2).replace('.', ',') + ' €';
        if (elEstimatedPay) elEstimatedPay.textContent = (total * rate).toFixed(2).replace('.', ',') + ' €';
    }

    function renderCalendar(year, month) {
        state.year = year;
        state.month = month;

        if (!elCalendarDays) return;
        elCalendarDays.innerHTML = '';

        const first = new Date(year, month - 1, 1);
        const firstWeekday = (first.getDay() + 6) % 7; // Monday = 0
        const daysInMonth = new Date(year, month, 0).getDate();

        // Empty cells before first
        for (let i = 0; i < firstWeekday; i++) {
            const empty = document.createElement('div');
            empty.className = 'tm-embed-day-cell tm-embed-day-empty';
            elCalendarDays.appendChild(empty);
        }

        const today = new Date();
        const todayStr = today.toISOString().slice(0, 10);

        for (let d = 1; d <= daysInMonth; d++) {
            const dateObj = new Date(year, month - 1, d);
            const iso = dateObj.toISOString().slice(0, 10);
            const entry = state.days[iso];

            const cell = document.createElement('div');
            cell.className = 'tm-embed-day-cell';
            cell.dataset.date = iso;
            if (iso === todayStr) cell.classList.add('tm-embed-day-today');

            const num = document.createElement('div');
            num.className = 'tm-embed-day-number';
            num.textContent = d;

            const info = document.createElement('div');
            info.className = 'tm-embed-day-info';

            if (entry) {
                info.innerHTML =
                    (entry.start || '') + '–' + (entry.end || '') +
                    (entry.breakMinutes
                        ? '<br>Pause: ' + entry.breakMinutes + ' min'
                        : '');

                const badge = document.createElement('div');
                badge.className = 'tm-embed-day-hours-badge';
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

        recalcSummary();
    }

    // ---- DAY MODAL ----------------------------------------------------------
    function updateModalPreview() {
        const start = elModalStart ? elModalStart.value : '';
        const end   = elModalEnd ? elModalEnd.value : '';
        const brMin = elModalBreak ? parseInt(elModalBreak.value || '0', 10) : 0;
        const h = computeHours(start, end, isNaN(brMin) ? 0 : brMin);
        if (elModalHours) elModalHours.textContent = h.toFixed(2);
    }

    function openDayModal(iso) {
        if (!elDayModal) return;

        const entry = state.days[iso];
        if (elModalDate) elModalDate.value = iso;

        const dateObj = new Date(iso);
        const opt = { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' };
        if (elModalDayLbl) {
            elModalDayLbl.textContent = dateObj.toLocaleDateString('de-DE', opt);
        }

        if (entry) {
            if (elModalStart) elModalStart.value = entry.start || '';
            if (elModalEnd)   elModalEnd.value   = entry.end   || '';
            if (elModalBreak) elModalBreak.value = entry.breakMinutes != null ? entry.breakMinutes : 30;
            if (elModalHours) elModalHours.textContent = entry.hours.toFixed(2);
        } else {
            if (elModalStart) elModalStart.value = '';
            if (elModalEnd)   elModalEnd.value   = '';
            if (elModalBreak) elModalBreak.value = 30;
            if (elModalHours) elModalHours.textContent = '0.00';
        }

        // simple manual show (no Bootstrap JS dependency)
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

    // expose close for inline onclick
    window.tmEmbedCloseDayModal = closeDayModal;

    if (elModalStart) elModalStart.addEventListener('change', updateModalPreview);
    if (elModalEnd)   elModalEnd.addEventListener('change', updateModalPreview);
    if (elModalBreak) elModalBreak.addEventListener('input', updateModalPreview);

    if (elModalSave) {
        elModalSave.addEventListener('click', function () {
            const date = elModalDate ? elModalDate.value : '';
            if (!date) {
                closeDayModal();
                return;
            }
            const start = elModalStart ? elModalStart.value : '';
            const end   = elModalEnd ? elModalEnd.value : '';
            const brMin = elModalBreak ? parseInt(elModalBreak.value || '0', 10) : 0;
            const h = computeHours(start, end, isNaN(brMin) ? 0 : brMin);

            if (!start || !end || h <= 0) {
                showAlert('warning', 'Ungültige Zeit', 'Bitte gültige Start- und Endzeit eingeben.');
                return;
            }

            state.days[date] = {
                start: start,
                end: end,
                breakMinutes: isNaN(brMin) ? 0 : brMin,
                hours: h
            };

            closeDayModal();
            renderCalendar(state.year, state.month);
        });
    }

    if (elModalDelete) {
        elModalDelete.addEventListener('click', function () {
            const date = elModalDate ? elModalDate.value : '';
            if (date && state.days[date]) delete state.days[date];
            closeDayModal();
            renderCalendar(state.year, state.month);
        });
    }

    // ---- WEEK PATTERN LOGIC (simple employee) --------------------------------
    function buildPattern() {
        const pattern = {}; // weekday(1–7) => {start,end,breakMinutes}

        elPatternDays.forEach(cb => {
            if (!cb.checked) return;
            const weekday = Number(cb.dataset.weekday || '0');
            if (!weekday) return;

            const start = elPatternStart ? elPatternStart.value : '';
            const end   = elPatternEnd ? elPatternEnd.value : '';
            const brMin = elPatternBreak ? parseInt(elPatternBreak.value || '0', 10) : 0;

            if (!start || !end) return;

            pattern[weekday] = {
                start: start,
                end: end,
                breakMinutes: isNaN(brMin) ? 0 : brMin
            };
        });

        return pattern;
    }

    function applyPatternToMonth() {
        if (!elMonth) return;
        const { year, month } = parseMonthInput(elMonth.value);
        const pattern = buildPattern();
        const daysInMonth = new Date(year, month, 0).getDate();

        for (let d = 1; d <= daysInMonth; d++) {
            const dateObj = new Date(year, month - 1, d);
            // JS getDay(): 0=Sun..6=Sat -> convert to 1=Mo..7=So
            const weekday = ((dateObj.getDay() + 6) % 7) + 1;
            const p = pattern[weekday];
            const iso = dateObj.toISOString().slice(0, 10);

            if (!p) continue;

            const hours = computeHours(p.start, p.end, p.breakMinutes);
            if (hours <= 0) {
                delete state.days[iso];
            } else {
                state.days[iso] = {
                    start: p.start,
                    end: p.end,
                    breakMinutes: p.breakMinutes,
                    hours: hours
                };
            }
        }

        renderCalendar(year, month);
        showAlert('success', 'Muster angewendet', 'Das Wochenmuster wurde auf den Monat angewendet.');
    }

    function applySimplePreset() {
        if (elPatternStart) elPatternStart.value = '07:00';
        if (elPatternEnd)   elPatternEnd.value   = '16:00';
        if (elPatternBreak) elPatternBreak.value = '30';

        // Mo–Fr checked, Sa/So unchecked
        elPatternDays.forEach(cb => {
            const weekday = Number(cb.dataset.weekday || '0');
            cb.checked = (weekday >= 1 && weekday <= 5);
        });
    }

    // ---- AJAX ---------------------------------------------------------------
    async function getJson(url) {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const text = await res.text();
        let json;
        try {
            json = text ? JSON.parse(text) : {};
        } catch (e) {
            json = { raw: text };
        }
        if (!res.ok) {
            throw new Error('HTTP ' + res.status + ': ' + text);
        }
        return json;
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
            throw new Error('HTTP ' + res.status + ': ' + text);
        }
        return json;
    }

    async function loadMonth() {
        const { year, month } = parseMonthInput(elMonth ? elMonth.value : null);
        renderCalendar(year, month); // empty first

        try {
            const url = loadUrl +
                '?employee_id=' + encodeURIComponent(employeeId) +
                '&year=' + encodeURIComponent(year) +
                '&month=' + encodeURIComponent(month);

            const res = await getJson(url);

            state.days = {};
            let target = 0;
            let rate   = 0;

            if (res.employee) {
                if (res.employee.working_hour != null) target = res.employee.working_hour;
                if (res.employee.salary_per_hour != null) rate = res.employee.salary_per_hour;
            }
            if (res.plan) {
                if (res.plan.target_hours != null) target = res.plan.target_hours;
                if (res.plan.hourly_rate != null)  rate   = res.plan.hourly_rate;
            }

            state.targetHours = Number(target || 0);
            state.hourlyRate  = Number(rate   || 0);

            if (res.plan) {
                state.planId = res.plan.id;
                setStatusUI(res.plan.status || 'draft');
                if (elComment && res.plan.comment != null) {
                    elComment.value = res.plan.comment;
                }
            } else {
                state.planId = null;
                setStatusUI('draft');
                if (elComment) elComment.value = '';
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
        } catch (err) {
            logError('loadMonth', err);
            showAlert('error', 'Fehler', 'Fehler beim Laden der Arbeitszeiten.');
        }
    }

    async function saveMonth(showSuccess = true) {
        if (!elMonth) return;
        const { year, month } = parseMonthInput(elMonth.value);

        const daysArr = Object.keys(state.days).map(date => {
            const d = state.days[date];
            return {
                date: date,
                start_time: d.start,
                end_time: d.end,
                break_minutes: d.breakMinutes
            };
        });

        try {
            const res = await postJson(saveUrl, {
                employee_id: employeeId,
                year: year,
                month: month,
                target_hours: state.targetHours,
                days: daysArr
            });

            if (res.plan_id) state.planId = res.plan_id;
            if (typeof res.target_hours !== 'undefined') {
                state.targetHours = res.target_hours;
            }
            if (typeof res.hourly_rate !== 'undefined') {
                state.hourlyRate = res.hourly_rate;
            }
            recalcSummary();
            if (showSuccess) {
                showAlert('success', 'Gespeichert', 'Zeitplan wurde erfolgreich gespeichert.');
            }
        } catch (err) {
            logError('saveMonth', err);
            showAlert('error', 'Fehler', 'Fehler beim Speichern des Zeitplans.');
        }
    }

    async function submitMonth() {
        try {
            if (!state.planId) {
                await saveMonth(false); // first save
            }
            if (!state.planId) {
                showAlert('error', 'Fehler', 'Kein Plan vorhanden, konnte nicht gesendet werden.');
                return;
            }

            const res = await postJson(submitUrl, { plan_id: state.planId });
            setStatusUI(res.status || 'pending');
            showAlert('success', 'Gesendet', 'Zeitplan wurde zur Genehmigung gesendet.');
        } catch (err) {
            logError('submitMonth', err);
            showAlert('error', 'Fehler', 'Fehler beim Senden zur Genehmigung.');
        }
    }

    async function saveStatus() {
        if (!state.planId) {
            showAlert('warning', 'Kein Plan', 'Es ist noch kein Plan für diesen Monat gespeichert.');
            return;
        }
        const newStatus = elStatusSelect ? elStatusSelect.value : '';
        const comment   = elComment ? elComment.value : '';

        if (!newStatus) {
            showAlert('info', 'Hinweis', 'Bitte zuerst einen Status auswählen.');
            return;
        }

        const url = statusUrlTpl.replace('PLAN_ID', state.planId);

        try {
            const res = await postJson(url, { status: newStatus, comment: comment });
            setStatusUI(res.status || newStatus);
            showAlert('success', 'Status aktualisiert', 'Der Status wurde erfolgreich gespeichert.');
        } catch (err) {
            logError('saveStatus', err);
            showAlert('error', 'Fehler', 'Fehler beim Speichern des Status.');
        }
    }

    // event bindings
    if (elReload)     elReload.addEventListener('click', loadMonth);
    if (elSaveMonth)  elSaveMonth.addEventListener('click', () => saveMonth(true));
    if (elSubmit)     elSubmit.addEventListener('click', submitMonth);
    if (elSaveStatus) elSaveStatus.addEventListener('click', saveStatus);

    if (elPatternPreset) elPatternPreset.addEventListener('click', applySimplePreset);
    if (elApplyPattern)  elApplyPattern.addEventListener('click', applyPatternToMonth);

    // initial load
    loadMonth();
});
</script>
