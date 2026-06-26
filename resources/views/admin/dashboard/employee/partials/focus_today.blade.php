<div class="focus-today-module" id="focusTodayModule" data-load-url="{{ route('my.due.today') }}"
    data-done-url="{{ route('my.mark.done') }}">

    <div class="focus-today-toolbar">
        <input type="date" id="focusTodayDate" value="{{ now()->format('Y-m-d') }}">

        <select id="focusTodayType">
            <option value="all">Alle</option>
            <option value="personal_task">Aufgaben</option>
            <option value="appointment">Termine</option>
            <option value="problem">Tickets</option>
            <option value="ticket_task">Ticket-Tasks</option>
            <option value="lead">Leads</option>
            <option value="inquiry">Anfragen</option>
            <option value="leave">Urlaub / Antrag</option>
        </select>

        <button type="button" id="focusTodayRefresh" title="Aktualisieren">
            <i data-lucide="refresh-cw"></i>
        </button>
    </div>

    <div class="focus-today-search-row">
        <input type="search" id="focusTodaySearch" placeholder="Suchen nach Kunde, Aufgabe, Ticket...">
    </div>

    <div class="focus-today-summary">
        <div>
            <strong id="focusTodayTotal">0</strong>
            <span>Gesamt</span>
        </div>
        <div>
            <strong id="focusTodayAppointments">0</strong>
            <span>Termine</span>
        </div>
        <div>
            <strong id="focusTodayTasks">0</strong>
            <span>Aufgaben</span>
        </div>
        <div>
            <strong id="focusTodayTickets">0</strong>
            <span>Tickets</span>
        </div>
    </div>

    <div class="focus-today-progress">
        <span id="focusTodayProgressBar"></span>
    </div>

    <div class="focus-today-list" id="focusTodayList">
        <div class="focus-today-empty">
            <i data-lucide="loader-2"></i>
            <span>Wird geladen...</span>
        </div>
    </div>
</div>

<div class="focus-report-modal" id="focusReportModal" aria-hidden="true" role="dialog" aria-modal="true" tabindex="-1">
    <div class="focus-report-backdrop" data-focus-report-close></div>

    <div class="focus-report-card">
        <div class="focus-report-header">
            <div>
                <h3 id="focusReportTitle">Abschließen</h3>
                <p id="focusReportSubtitle">Bitte Bericht eintragen.</p>
            </div>

            <button type="button" data-focus-report-close>
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="focusReportForm">
            @csrf

            <input type="hidden" id="focusReportItemId">
            <input type="hidden" id="focusReportItemType">
            <input type="hidden" id="focusReportAction" value="done">

            <label class="focus-report-label">
                Bericht / Kommentar
                <textarea id="focusReportText" rows="5"
                    placeholder="Was wurde erledigt? Was ist der nächste Schritt?"></textarea>
            </label>

            <label class="focus-report-label">
                Nächster Schritt
                <input type="text" id="focusReportNextStep" placeholder="Optional">
            </label>

            <label class="focus-report-label">
                Wiedervorlage / Fälligkeitsdatum
                <input type="date" id="focusReportDueDate">
            </label>

            <div class="focus-report-actions">
                <button type="button" class="btn" data-focus-report-close>Abbrechen</button>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check"></i>
                    Speichern & abschließen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .focus-today-module {
        height: 100%;
        min-height: 0;
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .focus-today-toolbar {
        display: grid;
        grid-template-columns: 135px minmax(0, 1fr) 42px;
        gap: .55rem;
    }

    .focus-today-toolbar input,
    .focus-today-toolbar select,
    .focus-today-toolbar button,
    .focus-today-search-row input {
        height: 40px;
        border-radius: .85rem;
        padding: 0 .75rem;
        border: 1px solid var(--color-border);
        background: var(--color-surface-2);
        color: var(--color-text);
        font-size: .78rem;
        font-weight: 800;
    }

    .focus-today-toolbar button {
        display: grid;
        place-items: center;
        color: var(--color-blue-dark);
    }

    .focus-today-search-row input {
        width: 100%;
    }

    .focus-today-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .45rem;
    }

    .focus-today-summary div {
        padding: .55rem;
        border-radius: .8rem;
        border: 1px solid var(--color-border);
        background: var(--color-surface-2);
        text-align: center;
    }

    .focus-today-summary strong {
        display: block;
        font-size: 1rem;
        font-weight: 900;
        line-height: 1;
    }

    .focus-today-summary span {
        display: block;
        margin-top: .2rem;
        font-size: .62rem;
        color: var(--color-text-muted);
        font-weight: 900;
        text-transform: uppercase;
    }

    .focus-today-progress {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--color-surface-2);
        border: 1px solid var(--color-border);
    }

    .focus-today-progress span {
        display: block;
        height: 100%;
        width: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
        transition: width .25s ease;
    }

    .focus-today-list {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: .65rem;
        padding-right: .2rem;
    }

    .focus-today-card {
        border: 1px solid var(--color-border);
        background: var(--color-surface-2);
        border-radius: 1rem;
        padding: .8rem;
        display: flex;
        flex-direction: column;
        gap: .55rem;
        transition: transform .18s ease, border .18s ease, box-shadow .18s ease;
    }

    .focus-today-card:hover {
        transform: translateY(-1px);
        border-color: var(--color-border-strong);
        box-shadow: var(--shadow-xs);
    }

    .focus-today-card-top {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        align-items: flex-start;
    }

    .focus-today-main {
        min-width: 0;
    }

    .focus-today-title {
        font-size: .88rem;
        font-weight: 900;
        color: var(--color-text);
        line-height: 1.25;
    }

    .focus-today-desc {
        margin-top: .25rem;
        color: var(--color-text-muted);
        font-size: .74rem;
        font-weight: 700;
        line-height: 1.4;
    }

    .focus-today-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-top: .35rem;
    }

    .focus-chip {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .2rem .45rem;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 900;
        background: #fff;
        color: var(--color-text-muted);
        border: 1px solid var(--color-border);
    }

    .focus-chip.type-personal_task {
        background: #eff6ff;
        color: #2563eb;
    }

    .focus-chip.type-appointment {
        background: #ecfdf5;
        color: #059669;
    }

    .focus-chip.type-problem {
        background: #fff7ed;
        color: #ea580c;
    }

    .focus-chip.type-ticket_task {
        background: #fef2f2;
        color: #dc2626;
    }

    .focus-chip.type-lead {
        background: #eef2ff;
        color: #4f46e5;
    }

    .focus-chip.type-inquiry {
        background: #fffbeb;
        color: #d97706;
    }

    .focus-chip.type-leave {
        background: #fdf2f8;
        color: #db2777;
    }

    .focus-priority {
        flex: 0 0 auto;
        padding: .24rem .48rem;
        border-radius: .65rem;
        font-size: .63rem;
        font-weight: 900;
        text-transform: uppercase;
        background: var(--color-blue-soft);
        color: var(--color-blue-dark);
    }

    .focus-priority.high,
    .focus-priority.urgent,
    .focus-priority.dringend,
    .focus-priority.very-high {
        background: var(--color-danger-soft);
        color: var(--color-danger);
    }

    .focus-priority.low,
    .focus-priority.niedrig {
        background: var(--color-green-soft);
        color: var(--color-green-dark);
    }

    .focus-today-actions {
        display: flex;
        gap: .45rem;
        flex-wrap: wrap;
        margin-top: .1rem;
    }

    .focus-action-btn {
        min-height: 32px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .65rem;
        border-radius: .75rem;
        border: 1px solid var(--color-border);
        background: #fff;
        color: var(--color-text-soft);
        font-size: .72rem;
        font-weight: 900;
        text-decoration: none;
    }

    .focus-action-btn:hover {
        border-color: var(--color-blue);
        color: var(--color-blue-dark);
    }

    .focus-action-btn.done {
        background: var(--color-green-soft);
        color: var(--color-green-dark);
        border-color: rgba(139, 195, 74, .35);
    }

    .focus-action-btn.map {
        background: var(--color-blue-soft);
        color: var(--color-blue-dark);
        border-color: rgba(116, 178, 212, .35);
    }

    .focus-action-btn.measurement {
        background: var(--color-green-soft);
        color: var(--color-green-dark);
        border-color: rgba(139, 195, 74, .38);
    }

    .focus-chip.measurement {
        background: var(--color-green-soft);
        color: var(--color-green-dark);
        border-color: rgba(139, 195, 74, .38);
    }

    .focus-action-btn.danger {
        background: var(--color-danger-soft);
        color: var(--color-danger);
        border-color: rgba(229, 6, 86, .25);
    }

    .focus-today-empty {
        min-height: 150px;
        display: grid;
        place-items: center;
        text-align: center;
        color: var(--color-text-muted);
        font-size: .82rem;
        font-weight: 800;
        gap: .35rem;
    }

    .focus-today-empty i {
        animation: focusSpin 1s linear infinite;
    }

    @keyframes focusSpin {
        to {
            transform: rotate(360deg);
        }
    }

    .focus-report-modal {
        position: fixed !important;
        inset: 0 !important;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        z-index: 12050 !important;
        pointer-events: none;
        isolation: isolate;
    }

    .focus-report-modal.show {
        display: flex !important;
        pointer-events: auto;
    }

    body.focus-report-open {
        overflow: hidden !important;
    }

    .focus-report-backdrop {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, .62);
        backdrop-filter: blur(5px);
        z-index: 0;
    }

    .focus-report-card {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: min(560px, calc(100vw - 1.5rem));
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
        transform: none !important;
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 1.25rem;
        box-shadow: 0 30px 90px rgba(15, 23, 42, .28);
        padding: 1.15rem;
        z-index: 1;
        pointer-events: auto;
        animation: focusReportIn .16s ease-out both;
        will-change: transform, opacity;
    }

    @keyframes focusReportIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .focus-report-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: .85rem;
        margin-bottom: .85rem;
        border-bottom: 1px solid var(--color-border);
    }

    .focus-report-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 900;
    }

    .focus-report-header p {
        margin: .15rem 0 0;
        color: var(--color-text-muted);
        font-size: .82rem;
        font-weight: 700;
        word-break: break-word;
    }

    .focus-report-header button {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: .9rem;
        background: var(--color-surface-2);
        border: 1px solid var(--color-border);
        flex: 0 0 auto;
    }

    .focus-report-label {
        display: block;
        margin-bottom: .75rem;
        color: var(--color-text-muted);
        font-size: .76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .focus-report-label textarea,
    .focus-report-label input {
        margin-top: .35rem;
        width: 100%;
        border-radius: .9rem;
        padding: .75rem;
        border: 1px solid var(--color-border);
        background: var(--color-surface-2);
        color: var(--color-text);
        font-size: .85rem;
        font-weight: 700;
        text-transform: none;
        letter-spacing: 0;
    }

    .focus-report-label textarea {
        resize: vertical;
        min-height: 120px;
    }

    .focus-report-actions {
        display: flex;
        justify-content: flex-end;
        gap: .55rem;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .focus-today-toolbar {
            grid-template-columns: 1fr;
        }

        .focus-today-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .focus-report-actions {
            flex-direction: column;
        }

        .focus-report-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    window.FocusTodayWidget = window.FocusTodayWidget || (function () {
        let items = [];
        let activeItem = null;
        let booted = false;

        function init() {
            if (booted) return;
            booted = true;

            ensureReportModalPortal();
            bindEvents();
            load();

            if (window.lucide) {
                lucide.createIcons();
            }
        }

        function ensureReportModalPortal() {
            const modal = document.getElementById('focusReportModal');
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        }

        function moduleEl() {
            return document.getElementById('focusTodayModule');
        }

        function csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        }

        function bindEvents() {
            document.getElementById('focusTodayRefresh')?.addEventListener('click', load);
            document.getElementById('focusTodayDate')?.addEventListener('change', load);
            document.getElementById('focusTodayType')?.addEventListener('change', render);
            document.getElementById('focusTodaySearch')?.addEventListener('input', debounce(render, 250));

            document.addEventListener('click', function (event) {
                const doneBtn = event.target.closest('[data-focus-done]');
                if (doneBtn) {
                    event.preventDefault();
                    event.stopPropagation();
                    openDoneModal(doneBtn);
                    return;
                }

                const closeBtn = event.target.closest('[data-focus-report-close]');
                if (closeBtn) {
                    event.preventDefault();
                    event.stopPropagation();
                    closeDoneModal();
                    return;
                }
            });

            document.getElementById('focusReportForm')?.addEventListener('submit', submitDone);
        }

        async function load() {
            const root = moduleEl();
            if (!root) return;

            const url = new URL(root.dataset.loadUrl, window.location.origin);
            const selectedDate = document.getElementById('focusTodayDate')?.value;

            if (selectedDate) {
                url.searchParams.set('date', selectedDate);
            }

            setLoading();

            try {
                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                items = Array.isArray(result.items) ? result.items : [];

                updateCounters(result);
                render();

            } catch (error) {
                console.error(error);
                setError('Fokus Heute konnte nicht geladen werden.');
            }
        }

        function render() {
            const list = document.getElementById('focusTodayList');
            if (!list) return;

            const type = document.getElementById('focusTodayType')?.value || 'all';
            const search = (document.getElementById('focusTodaySearch')?.value || '').trim().toLowerCase();

            const filtered = items.filter(item => {
                const matchesType = type === 'all' || item.type === type;

                const haystack = [
                    item.title,
                    item.description,
                    item.customer_name,
                    item.product_info,
                    item.full_address,
                    item.ticket_no,
                    item.label,
                    item.priority,
                    item.feinaufmass_label,
                    item.feinaufmass_id,
                    item.deal_measurement_id
                ].filter(Boolean).join(' ').toLowerCase();

                const matchesSearch = !search || haystack.includes(search);

                return matchesType && matchesSearch;
            });

            const counter = document.getElementById('focusTodayCounter');
            if (counter) {
                counter.textContent = `${filtered.length} Offen`;
            }

            if (!filtered.length) {
                list.innerHTML = `
                    <div class="focus-today-empty">
                        <div>
                            <i data-lucide="check-circle"></i>
                            <br>
                            Keine offenen Einträge gefunden.
                        </div>
                    </div>
                `;

                if (window.lucide) lucide.createIcons();
                return;
            }

            list.innerHTML = filtered.map(item => renderItem(item)).join('');

            if (window.lucide) {
                lucide.createIcons();
            }
        }

        function renderItem(item) {
            const priorityClass = normalizePriority(item.priority);
            const hasDetail = item.detail_url && item.detail_url !== '#';
            const hasMap = item.map_url;
            const hasReportBadge = item.report_badge_text;
            const measurementId = item.feinaufmass_id || item.deal_measurement_id || null;
            const measurementUrl = item.feinaufmass_url || null;
            const hasMeasurement = !!(measurementId && measurementUrl && measurementUrl !== '#');

            return `
                <div class="focus-today-card"
                     data-focus-item-id="${escapeHtml(item.id)}"
                     data-focus-item-type="${escapeHtml(item.type)}">
                    <div class="focus-today-card-top">
                        <div class="focus-today-main">
                            <div class="focus-today-title">${escapeHtml(item.title || item.label || 'Eintrag')}</div>

                            ${item.description ? `
                                <div class="focus-today-desc">${escapeHtml(item.description)}</div>
                            ` : ''}

                            <div class="focus-today-meta">
                                <span class="focus-chip type-${escapeHtml(item.type)}">
                                    ${escapeHtml(item.label || item.type)}
                                </span>

                                ${item.due_date ? `
                                    <span class="focus-chip">
                                        <i data-lucide="calendar" style="width:12px;"></i>
                                        ${formatDate(item.due_date)}
                                    </span>
                                ` : ''}

                                ${item.due_time ? `
                                    <span class="focus-chip">
                                        <i data-lucide="clock" style="width:12px;"></i>
                                        ${escapeHtml(item.due_time)}
                                    </span>
                                ` : ''}

                                ${item.customer_name ? `
                                    <span class="focus-chip">
                                        <i data-lucide="user" style="width:12px;"></i>
                                        ${escapeHtml(item.customer_name)}
                                    </span>
                                ` : ''}

                                ${item.product_info ? `
                                    <span class="focus-chip">
                                        <i data-lucide="package" style="width:12px;"></i>
                                        ${escapeHtml(item.product_info)}
                                    </span>
                                ` : ''}

                                ${hasReportBadge ? `
                                    <span class="focus-chip">
                                        <i data-lucide="file-text" style="width:12px;"></i>
                                        ${escapeHtml(item.report_badge_text)}
                                    </span>
                                ` : ''}

                                ${hasMeasurement ? `
                                    <span class="focus-chip measurement">
                                        <i data-lucide="ruler" style="width:12px;"></i>
                                        ${escapeHtml(item.feinaufmass_label || ('Feinaufmaß #' + measurementId))}
                                    </span>
                                ` : ''}
                            </div>
                        </div>

                        <div class="focus-priority ${priorityClass}">
                            ${escapeHtml(item.priority || 'normal')}
                        </div>
                    </div>

                    <div class="focus-today-actions">
                        ${hasDetail ? `
                            <a class="focus-action-btn" href="${escapeAttr(item.detail_url)}">
                                <i data-lucide="external-link" style="width:14px;"></i>
                                Details
                            </a>
                        ` : ''}

                        ${hasMeasurement ? `
                            <a class="focus-action-btn measurement" href="${escapeAttr(measurementUrl)}">
                                <i data-lucide="ruler" style="width:14px;"></i>
                                Feinaufmaß
                            </a>
                        ` : ''}

                        ${hasMap ? `
                            <a class="focus-action-btn map" href="${escapeAttr(item.map_url)}" target="_blank" rel="noopener">
                                <i data-lucide="map-pin" style="width:14px;"></i>
                                Karte
                            </a>
                        ` : ''}

                        ${renderDoneButton(item)}
                    </div>
                </div>
            `;
        }

        function renderDoneButton(item) {
            if (item.type === 'leave') {
                return `
                    <button class="focus-action-btn done"
                            type="button"
                            data-focus-done
                            data-id="${escapeHtml(item.id)}"
                            data-type="${escapeHtml(item.type)}"
                            data-title="${escapeAttr(item.title || '')}"
                            data-action="approve">
                        <i data-lucide="check" style="width:14px;"></i>
                        Genehmigen
                    </button>

                    <button class="focus-action-btn danger"
                            type="button"
                            data-focus-done
                            data-id="${escapeHtml(item.id)}"
                            data-type="${escapeHtml(item.type)}"
                            data-title="${escapeAttr(item.title || '')}"
                            data-action="reject">
                        <i data-lucide="x" style="width:14px;"></i>
                        Ablehnen
                    </button>
                `;
            }

            return `
                <button class="focus-action-btn done"
                        type="button"
                        data-focus-done
                        data-id="${escapeHtml(item.id)}"
                        data-type="${escapeHtml(item.type)}"
                        data-title="${escapeAttr(item.title || '')}"
                        data-action="done">
                    <i data-lucide="check-circle" style="width:14px;"></i>
                    Erledigt
                </button>
            `;
        }

        function updateCounters(result) {
            const counters = result.counters || {};

            setText('focusTodayTotal', counters.total?.count || items.length || 0);
            setText('focusTodayAppointments', counters.appointment?.count || 0);
            setText('focusTodayTasks', counters.aufgabe?.count || 0);

            const tickets =
                items.filter(item => ['problem', 'ticket_task'].includes(item.type)).length;

            setText('focusTodayTickets', tickets);

            const percent = Number(result.percent || 0);
            const bar = document.getElementById('focusTodayProgressBar');

            if (bar) {
                bar.style.width = `${Math.max(0, Math.min(100, percent))}%`;
            }
        }

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        }

        function setLoading() {
            const list = document.getElementById('focusTodayList');

            if (!list) return;

            list.innerHTML = `
                <div class="focus-today-empty">
                    <div>
                        <i data-lucide="loader-2"></i>
                        <br>
                        Wird geladen...
                    </div>
                </div>
            `;

            if (window.lucide) lucide.createIcons();
        }

        function setError(message) {
            const list = document.getElementById('focusTodayList');

            if (!list) return;

            list.innerHTML = `
                <div class="focus-today-empty">
                    <div>
                        <i data-lucide="alert-circle"></i>
                        <br>
                        ${escapeHtml(message)}
                    </div>
                </div>
            `;

            if (window.lucide) lucide.createIcons();
        }

        function openDoneModal(button) {
            activeItem = {
                id: button.dataset.id,
                type: button.dataset.type,
                title: button.dataset.title || 'Eintrag',
                action: button.dataset.action || 'done'
            };

            document.getElementById('focusReportItemId').value = activeItem.id;
            document.getElementById('focusReportItemType').value = activeItem.type;
            document.getElementById('focusReportAction').value = activeItem.action;
            document.getElementById('focusReportText').value = '';
            document.getElementById('focusReportNextStep').value = '';
            document.getElementById('focusReportDueDate').value = '';

            const title = document.getElementById('focusReportTitle');
            const subtitle = document.getElementById('focusReportSubtitle');

            if (activeItem.type === 'leave' && activeItem.action === 'reject') {
                title.textContent = 'Antrag ablehnen';
                subtitle.textContent = activeItem.title;
                document.getElementById('focusReportText').placeholder = 'Grund für Ablehnung eintragen...';
            } else if (activeItem.type === 'leave' && activeItem.action === 'approve') {
                title.textContent = 'Antrag genehmigen';
                subtitle.textContent = activeItem.title;
                document.getElementById('focusReportText').placeholder = 'Optionaler Kommentar...';
            } else {
                title.textContent = 'Eintrag abschließen';
                subtitle.textContent = activeItem.title;
                document.getElementById('focusReportText').placeholder = 'Was wurde erledigt? Was ist der nächste Schritt?';
            }

            ensureReportModalPortal();
            const modal = document.getElementById('focusReportModal');
            modal?.classList.add('show');
            modal?.setAttribute('aria-hidden', 'false');
            document.body.classList.add('focus-report-open');

            window.requestAnimationFrame(() => {
                document.getElementById('focusReportText')?.focus({ preventScroll: true });
            });

            if (window.lucide) lucide.createIcons();
        }

        function closeDoneModal() {
            const modal = document.getElementById('focusReportModal');
            modal?.classList.remove('show');
            modal?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('focus-report-open');
            activeItem = null;
        }

        async function submitDone(event) {
            event.preventDefault();

            const root = moduleEl();
            if (!root) return;

            const id = document.getElementById('focusReportItemId').value;
            const type = document.getElementById('focusReportItemType').value;
            const action = document.getElementById('focusReportAction').value || 'done';
            const report = document.getElementById('focusReportText').value.trim();
            const nextStep = document.getElementById('focusReportNextStep').value.trim();
            const dueDate = document.getElementById('focusReportDueDate').value;

            if (!id || !type) {
                alert('Eintrag fehlt.');
                return;
            }

            if (action === 'reject' && report === '') {
                alert('Bitte Ablehnungsgrund eintragen.');
                return;
            }

            if (!['leave', 'ticket_task'].includes(type) && report === '') {
                alert('Bitte kurzen Bericht eintragen.');
                return;
            }

            try {
                const response = await fetch(root.dataset.doneUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: id,
                        type: type,
                        action: action,
                        report: report,
                        reason: report,
                        next_step: nextStep,
                        due_date: dueDate || null
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw result;
                }

                closeDoneModal();
                load();

                if (window.showToast) {
                    showToast('Eintrag aktualisiert');
                }

            } catch (error) {
                console.error(error);
                alert(error.message || error.error || 'Aktion fehlgeschlagen.');
            }
        }

        function normalizePriority(priority) {
            return String(priority || 'normal')
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/_/g, '-');
        }

        function formatDate(value) {
            if (!value) return '';

            try {
                const date = new Date(value);
                return date.toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (e) {
                return value;
            }
        }

        function debounce(callback, delay = 300) {
            let timer = null;

            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => callback.apply(this, args), delay);
            };
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeAttr(value) {
            return escapeHtml(value).replace(/`/g, '&#096;');
        }

        return {
            init,
            load
        };
    })();

    document.addEventListener('DOMContentLoaded', function () {
        window.FocusTodayWidget.init();
    });
</script>