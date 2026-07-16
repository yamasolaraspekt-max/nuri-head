@php
    $employeeId = $data->id;
    $employeeName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
    $employeeName = $employeeName !== '' ? $employeeName : 'Mitarbeiter #' . $employeeId;
@endphp

@once
    @push('style')
        <style>
            :root {
                --sk-card: #ffffff;
                --sk-text: #1f2937;
                --sk-muted: #6b7280;
                --sk-border: #e5e7eb;
                --sk-primary: var(--sa-accent);
                --sk-primary-dark: var(--sa-accent-hover);
                --sk-primary-light: var(--sa-accent-light);
                --sk-blue: #74b2d4;
                --sk-blue-light: #eff6ff;
                --sk-success: #10b981;
                --sk-success-light: #ecfdf5;
                --sk-warning: #f59e0b;
                --sk-warning-light: #fffbeb;
                --sk-danger: #ef4444;
                --sk-danger-light: #fef2f2;
                --sk-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
                --sk-shadow: 0 18px 45px rgba(15, 23, 42, .16);
                --sk-radius: 16px;
                --sk-transition: all .2s ease;
            }

            .sk-wrap {
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: var(--sk-text);
            }

            .sk-header {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 14px;
                flex-wrap: wrap;
                margin-bottom: 18px;
            }

            .sk-title {
                font-size: 26px;
                font-weight: 900;
                letter-spacing: -.03em;
                color: #111827;
                margin: 0;
            }

            .sk-sub {
                font-size: 14px;
                color: var(--sk-muted);
                margin-top: 4px;
            }

            .sk-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                align-items: center;
            }

            .sk-btn {
                border: none;
                border-radius: 12px;
                padding: 10px 15px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                text-decoration: none;
                transition: var(--sk-transition);
                height: 42px;
                white-space: nowrap;
            }

            .sk-btn-primary {
                background: var(--sk-primary);
                color: #fff;
                box-shadow: 0 10px 22px rgba(147, 194, 28, .22);
            }

            .sk-btn-primary:hover {
                background: var(--sk-primary-dark);
                color: #fff;
                transform: translateY(-1px);
            }

            .sk-btn-soft {
                background: #fff;
                color: var(--sk-text);
                border: 1px solid var(--sk-border);
            }

            .sk-btn-soft:hover {
                background: #f9fafb;
                color: var(--sk-text);
                text-decoration: none;
            }

            .sk-btn-icon {
                width: 38px;
                height: 38px;
                border-radius: 11px;
                border: 1px solid var(--sk-border);
                background: #fff;
                color: var(--sk-muted);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--sk-transition);
            }

            .sk-btn-icon:hover {
                border-color: var(--sk-blue);
                color: var(--sk-blue);
                background: #f0f7fb;
            }

            .sk-stats {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            @media(max-width:1200px) {
                .sk-stats {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:720px) {
                .sk-stats {
                    grid-template-columns: 1fr;
                }
            }

            .sk-stat {
                background: #fff;
                border: 1px solid var(--sk-border);
                border-radius: 18px;
                padding: 16px;
                box-shadow: var(--sk-shadow-sm);
                display: flex;
                gap: 12px;
                align-items: center;
                min-height: 92px;
            }

            .sk-stat-ic {
                width: 48px;
                height: 48px;
                border-radius: 15px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .sk-stat-ic.total {
                background: var(--sk-blue-light);
                color: var(--sk-blue);
            }

            .sk-stat-ic.days {
                background: var(--sk-primary-light);
                color: #4d7c0f;
            }

            .sk-stat-ic.hours {
                background: var(--sk-warning-light);
                color: #d97706;
            }

            .sk-stat-ic.docs {
                background: var(--sk-success-light);
                color: var(--sk-success);
            }

            .sk-stat-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--sk-muted);
            }

            .sk-stat-value {
                font-size: 24px;
                font-weight: 900;
                color: #111827;
                line-height: 1.1;
                margin-top: 4px;
            }

            .sk-stat-sub {
                font-size: 12px;
                color: var(--sk-muted);
                margin-top: 4px;
            }

            .sk-toolbar {
                background: #fff;
                border: 1px solid var(--sk-border);
                border-radius: var(--sk-radius);
                padding: 14px 16px;
                display: flex;
                gap: 12px;
                justify-content: space-between;
                align-items: flex-end;
                flex-wrap: wrap;
                box-shadow: var(--sk-shadow-sm);
                margin-bottom: 16px;
            }

            .sk-filter-left,
            .sk-filter-right {
                display: flex;
                gap: 12px;
                align-items: flex-end;
                flex-wrap: wrap;
            }

            .sk-filter-left {
                flex: 1;
            }

            .sk-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
                min-width: 170px;
            }

            .sk-field.search {
                flex: 1;
                min-width: 260px;
            }

            .sk-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--sk-muted);
            }

            .sk-input,
            .sk-select,
            .sk-textarea {
                width: 100%;
                border: 1px solid var(--sk-border);
                border-radius: 11px;
                background: #fff;
                padding: 10px 12px;
                font-size: 14px;
                outline: none;
                transition: var(--sk-transition);
            }

            .sk-input:focus,
            .sk-select:focus,
            .sk-textarea:focus {
                border-color: var(--sk-primary);
                box-shadow: 0 0 0 3px var(--sk-primary-light);
            }

            .sk-textarea {
                resize: vertical;
                min-height: 96px;
            }

            .sk-card {
                background: #fff;
                border: 1px solid var(--sk-border);
                border-radius: 18px;
                box-shadow: var(--sk-shadow-sm);
                overflow: visible !important;
                position: relative;
            }

            .sk-list-head {
                display: grid;
                grid-template-columns: 90px minmax(190px, 1fr) 120px 130px 140px 150px 130px;
                gap: 14px;
                align-items: center;
                padding: 16px 18px 10px;
                color: var(--sk-muted);
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .sk-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 0 0 90px;
                overflow: visible !important;
            }

            .sk-row {
                margin: 0 16px;
                border: 1px solid var(--sk-border);
                border-radius: 16px;
                background: #fff;
                transition: var(--sk-transition);
                overflow: visible !important;
                position: relative;
            }

            .sk-row:hover {
                border-color: var(--sk-primary);
                box-shadow: var(--sk-shadow);
            }

            .sk-row.menu-open {
                z-index: 9999;
            }

            .sk-row-inner {
                display: grid;
                grid-template-columns: 90px minmax(190px, 1fr) 120px 130px 140px 150px 130px;
                gap: 14px;
                align-items: center;
                padding: 16px;
            }

            @media(max-width:1280px) {
                .sk-list-head {
                    display: none;
                }

                .sk-row-inner {
                    grid-template-columns: 1fr;
                }

                .sk-mobile-title {
                    display: block !important;
                }

                .sk-row {
                    margin: 0 12px;
                }
            }

            .sk-mobile-title {
                display: none;
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                color: var(--sk-muted);
                letter-spacing: .06em;
                margin-bottom: 5px;
            }

            .sk-id {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 34px;
                min-width: 58px;
                padding: 0 10px;
                border-radius: 10px;
                background: var(--sk-blue-light);
                color: var(--sk-blue);
                font-weight: 900;
                font-size: 13px;
            }

            .sk-main-title {
                font-weight: 900;
                color: #111827;
                font-size: 15px;
                margin-bottom: 4px;
            }

            .sk-main-sub {
                font-size: 13px;
                color: var(--sk-muted);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 420px;
            }

            .sk-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-radius: 999px;
                padding: 6px 10px;
                font-size: 12px;
                font-weight: 900;
                white-space: nowrap;
            }

            .sk-pill.ok {
                background: var(--sk-success-light);
                color: #047857;
            }

            .sk-pill.warn {
                background: var(--sk-warning-light);
                color: #b45309;
            }

            .sk-pill.bad {
                background: var(--sk-danger-light);
                color: #b91c1c;
            }

            .sk-pill.gray {
                background: #f3f4f6;
                color: #374151;
            }

            .sk-pill.blue {
                background: var(--sk-blue-light);
                color: #2563eb;
            }

            .sk-doc-btn {
                border: 1px solid var(--sk-border);
                background: #f8fafc;
                border-radius: 12px;
                padding: 9px 10px;
                font-size: 13px;
                color: #374151;
                line-height: 1.35;
                max-width: 260px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                font-weight: 800;
            }

            .sk-doc-btn:hover {
                background: #f0f7fb;
                border-color: rgba(116, 178, 212, .5);
            }

            .sk-actions-cell {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                align-items: center;
                position: relative;
                overflow: visible !important;
            }

            @media(max-width:1280px) {
                .sk-actions-cell {
                    justify-content: flex-start;
                }
            }

            .sk-action-menu {
                position: relative;
                display: inline-flex;
                overflow: visible !important;
            }

            .sk-action-list {
                position: absolute;
                right: 0;
                top: calc(100% + 8px);
                min-width: 220px;
                background: #fff;
                border: 1px solid var(--sk-border);
                border-radius: 14px;
                box-shadow: 0 24px 60px rgba(15, 23, 42, .25);
                padding: 7px;
                z-index: 10000;
                display: none;
            }

            .sk-action-list.show {
                display: block;
            }

            .sk-action-item {
                width: 100%;
                border: 0;
                background: transparent;
                text-decoration: none;
                color: #374151;
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 9px 10px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 800;
                cursor: pointer;
                text-align: left;
            }

            .sk-action-item:hover {
                background: #f8fafc;
                color: #111827;
                text-decoration: none;
            }

            .sk-action-item.danger {
                color: #dc2626;
            }

            .sk-empty {
                margin: 16px;
                padding: 46px 20px;
                text-align: center;
                color: var(--sk-muted);
                border: 1px dashed var(--sk-border);
                border-radius: 16px;
                background: #fff;
            }

            .sk-loading {
                margin: 16px;
                padding: 34px 20px;
                text-align: center;
                color: var(--sk-muted);
                border: 1px dashed var(--sk-border);
                border-radius: 16px;
                background: #fff;
                font-weight: 800;
            }

            .sk-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1250;
                background: rgba(17, 24, 39, .55);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 18px;
                opacity: 0;
                visibility: hidden;
                transition: opacity .22s ease, visibility .22s ease;
            }

            .sk-modal-backdrop.open {
                opacity: 1;
                visibility: visible;
            }

            .sk-modal {
                width: min(760px, 100%);
                max-height: 90vh;
                background: #fff;
                border-radius: 20px;
                border: 1px solid rgba(229, 231, 235, .95);
                box-shadow: var(--sk-shadow);
                overflow: hidden;
                transform: translateY(14px) scale(.985);
                transition: transform .22s ease;
                display: flex;
                flex-direction: column;
            }

            .sk-modal-backdrop.open .sk-modal {
                transform: translateY(0) scale(1);
            }

            .sk-modal-header {
                display: grid;
                grid-template-columns: 50px 1fr 38px;
                gap: 12px;
                align-items: flex-start;
                padding: 18px 20px;
                border-bottom: 1px solid var(--sk-border);
                background: linear-gradient(135deg, #fff, #f8fcff);
            }

            .sk-modal-icon {
                width: 50px;
                height: 50px;
                border-radius: 16px;
                background: var(--sk-primary-light);
                color: #4d7c0f;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .sk-modal-title {
                margin: 0;
                font-size: 18px;
                font-weight: 900;
                color: #111827;
            }

            .sk-modal-sub {
                font-size: 13px;
                color: var(--sk-muted);
                margin-top: 4px;
            }

            .sk-modal-body {
                padding: 20px;
                overflow: auto;
            }

            .sk-modal-footer {
                border-top: 1px solid var(--sk-border);
                padding: 14px 20px;
                background: #fafafa;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .sk-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            @media(max-width:760px) {
                .sk-form-grid {
                    grid-template-columns: 1fr;
                }

                .sk-modal-header {
                    grid-template-columns: 42px 1fr 36px;
                }

                .sk-modal-icon {
                    width: 42px;
                    height: 42px;
                    border-radius: 14px;
                }

                .sk-modal-footer .sk-btn {
                    width: 100%;
                }
            }

            .sk-help {
                font-size: 12px;
                color: var(--sk-muted);
                margin-top: 5px;
            }

            .sk-error {
                display: none;
                margin-top: 8px;
                border-radius: 12px;
                background: var(--sk-danger-light);
                color: #991b1b;
                padding: 10px 12px;
                font-size: 13px;
                font-weight: 800;
                white-space: pre-wrap;
            }

            .sk-error.show {
                display: block;
            }

            .sk-doc-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .sk-doc-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border: 1px solid var(--sk-border);
                border-radius: 13px;
                padding: 10px 12px;
                background: #fff;
            }

            .sk-doc-row a {
                color: #2563eb;
                font-weight: 900;
                text-decoration: none;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .sk-doc-row a:hover {
                text-decoration: underline;
            }
        </style>
    @endpush
@endonce

<div class="sk-wrap" id="sick-app" data-employee-id="{{ $employeeId }}">
    <div class="sk-header">
        <div>
            <h2 class="sk-title">Krankmeldungen</h2>
            <div class="sk-sub">Krankmeldungen von {{ $employeeName }} werden per AJAX geladen.</div>
        </div>

        <div class="sk-actions">
            <button type="button" class="sk-btn sk-btn-soft" id="sk-refresh-btn">
                <i class="feather icon-refresh-cw"></i> Aktualisieren
            </button>

            <button type="button" class="sk-btn sk-btn-primary" id="sk-open-create">
                <i class="feather icon-plus"></i> Neue Krankmeldung
            </button>
        </div>
    </div>

    <div class="sk-stats">
        <div class="sk-stat">
            <div class="sk-stat-ic total"><i class="feather icon-layers"></i></div>
            <div>
                <div class="sk-stat-label">Gesamt</div>
                <div class="sk-stat-value" id="sk-stat-total">0</div>
                <div class="sk-stat-sub">Krankmeldungen</div>
            </div>
        </div>

        <div class="sk-stat">
            <div class="sk-stat-ic days"><i class="feather icon-calendar"></i></div>
            <div>
                <div class="sk-stat-label">Tage</div>
                <div class="sk-stat-value" id="sk-stat-days">0</div>
                <div class="sk-stat-sub">Arbeitstage</div>
            </div>
        </div>

        <div class="sk-stat">
            <div class="sk-stat-ic hours"><i class="feather icon-clock"></i></div>
            <div>
                <div class="sk-stat-label">Stunden</div>
                <div class="sk-stat-value" id="sk-stat-hours">0</div>
                <div class="sk-stat-sub">Gesamtzeit</div>
            </div>
        </div>

        <div class="sk-stat">
            <div class="sk-stat-ic docs"><i class="feather icon-file-text"></i></div>
            <div>
                <div class="sk-stat-label">Dokumente</div>
                <div class="sk-stat-value" id="sk-stat-docs">0</div>
                <div class="sk-stat-sub">Mit Nachweis</div>
            </div>
        </div>
    </div>

    <div class="sk-toolbar">
        <div class="sk-filter-left">
            <div class="sk-field search">
                <label class="sk-label">Suche</label>
                <input class="sk-input" id="sk-search" placeholder="Suche nach Status, Beschreibung, Jahr oder Datum">
            </div>

            <div class="sk-field">
                <label class="sk-label">Jahr</label>
                <select class="sk-select" id="sk-filter-year">
                    <option value="">Alle Jahre</option>
                </select>
            </div>

            <div class="sk-field">
                <label class="sk-label">Dokumente</label>
                <select class="sk-select" id="sk-filter-docs">
                    <option value="">Alle</option>
                    <option value="with">Mit Dokumenten</option>
                    <option value="without">Ohne Dokumente</option>
                </select>
            </div>
        </div>

        <div class="sk-filter-right">
            <button type="button" class="sk-btn sk-btn-soft" id="sk-reset-filter">
                <i class="feather icon-x"></i> Filter löschen
            </button>
        </div>
    </div>

    <div class="sk-card">
        <div class="sk-list-head">
            <div>ID</div>
            <div>Zeitraum</div>
            <div>Tage</div>
            <div>Stunden</div>
            <div>Status</div>
            <div>Dokumente</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="sk-list" id="sk-list">
            <div class="sk-loading">Krankmeldungen werden geladen...</div>
        </div>
    </div>

    <div class="sk-modal-backdrop" id="sk-form-modal">
        <div class="sk-modal">
            <div class="sk-modal-header">
                <div class="sk-modal-icon"><i class="feather icon-thermometer"></i></div>

                <div>
                    <h3 class="sk-modal-title" id="sk-form-title">Neue Krankmeldung</h3>
                    <div class="sk-modal-sub" id="sk-form-sub">Zeitraum eintragen. Dokumente sind optional.</div>
                </div>

                <button type="button" class="sk-btn-icon" data-close-modal="sk-form-modal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <form id="sk-form" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id" id="sk-id">
                <input type="hidden" name="emp_id" value="{{ $employeeId }}">

                <div class="sk-modal-body">
                    <div class="sk-form-grid">
                        <div class="sk-field">
                            <label class="sk-label">Startdatum</label>
                            <input type="date" name="start_date" id="sk-start-date" class="sk-input" required>
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Enddatum</label>
                            <input type="date" name="end_date" id="sk-end-date" class="sk-input">
                            <div class="sk-help">Leer lassen, wenn es nur ein Tag ist.</div>
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Startzeit optional</label>
                            <input type="time" name="start_time" id="sk-start-time" class="sk-input">
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Endzeit optional</label>
                            <input type="time" name="end_time" id="sk-end-time" class="sk-input">
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Tage gesamt</label>
                            <input type="number" id="sk-total-days" class="sk-input" readonly>
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Stunden gesamt</label>
                            <input type="number" id="sk-total-hours" class="sk-input" readonly>
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Status</label>
                            <select name="status" id="sk-status" class="sk-select" required>
                                <option value="employee_applied">Mitarbeiter gemeldet</option>
                                <option value="admin_applied">Admin eingetragen</option>
                                <option value="confirmed">Bestätigt</option>
                                <option value="rejected">Abgelehnt</option>
                            </select>
                        </div>

                        <div class="sk-field">
                            <label class="sk-label">Dokumente optional</label>
                            <input type="file" name="documents[]" id="sk-documents" class="sk-input"
                                accept=".pdf,.jpg,.jpeg,.png,.webp" multiple>
                            <div class="sk-help">PDF, JPG, PNG oder WEBP. Mehrere Dateien möglich.</div>
                        </div>
                    </div>

                    <div class="sk-field" style="margin-top:14px;">
                        <label class="sk-label">Beschreibung</label>
                        <textarea name="status_msg" id="sk-status-msg" class="sk-textarea"
                            placeholder="Beschreibung optional"></textarea>
                    </div>

                    <div id="sk-current-documents-wrap" style="display:none;margin-top:16px;">
                        <label class="sk-label">Bestehende Dokumente</label>
                        <div class="sk-doc-list" id="sk-current-documents"></div>
                    </div>

                    <div class="sk-error" id="sk-form-error"></div>
                </div>

                <div class="sk-modal-footer">
                    <button type="button" class="sk-btn sk-btn-soft" data-close-modal="sk-form-modal">Abbrechen</button>
                    <button type="submit" class="sk-btn sk-btn-primary" id="sk-submit">
                        <i class="feather icon-save"></i> Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="sk-modal-backdrop" id="sk-doc-modal">
        <div class="sk-modal">
            <div class="sk-modal-header">
                <div class="sk-modal-icon"><i class="feather icon-file-text"></i></div>

                <div>
                    <h3 class="sk-modal-title">Dokumente</h3>
                    <div class="sk-modal-sub" id="sk-doc-sub"></div>
                </div>

                <button type="button" class="sk-btn-icon" data-close-modal="sk-doc-modal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="sk-modal-body">
                <div class="sk-doc-list" id="sk-doc-body"></div>
            </div>

            <div class="sk-modal-footer">
                <button type="button" class="sk-btn sk-btn-primary" data-close-modal="sk-doc-modal">OK</button>
            </div>
        </div>
    </div>

    <div class="sk-modal-backdrop" id="sk-desc-modal">
        <div class="sk-modal">
            <div class="sk-modal-header">
                <div class="sk-modal-icon"><i class="feather icon-message-square"></i></div>

                <div>
                    <h3 class="sk-modal-title">Beschreibung</h3>
                    <div class="sk-modal-sub" id="sk-desc-sub"></div>
                </div>

                <button type="button" class="sk-btn-icon" data-close-modal="sk-desc-modal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="sk-modal-body">
                <div id="sk-desc-body" style="white-space:pre-wrap;line-height:1.6;color:#111827;"></div>
            </div>

            <div class="sk-modal-footer">
                <button type="button" class="sk-btn sk-btn-primary" data-close-modal="sk-desc-modal">OK</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            const app = document.getElementById('sick-app');

            if (!app || app.dataset.ready === '1') return;

            app.dataset.ready = '1';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const employeeId = app.dataset.employeeId;

            const API = {
                list: @json(url('/employee-sick/employee')) + '/' + employeeId,
                store: @json(route('employee.sick.store')),
                editBase: @json(url('/employee-sick/edit')),
                updateBase: @json(url('/employee-sick/update')),
                deleteBase: @json(url('/employee-sick/destroy')),
                deleteDocBase: @json(url('/employee-sick')),
            };

            const state = {
                sicks: [],
                loaded: false,
                mode: 'create',
            };

            const el = {
                list: document.getElementById('sk-list'),
                search: document.getElementById('sk-search'),
                yearFilter: document.getElementById('sk-filter-year'),
                docsFilter: document.getElementById('sk-filter-docs'),
                resetFilter: document.getElementById('sk-reset-filter'),
                refresh: document.getElementById('sk-refresh-btn'),

                statTotal: document.getElementById('sk-stat-total'),
                statDays: document.getElementById('sk-stat-days'),
                statHours: document.getElementById('sk-stat-hours'),
                statDocs: document.getElementById('sk-stat-docs'),

                createBtn: document.getElementById('sk-open-create'),
                formModal: document.getElementById('sk-form-modal'),
                form: document.getElementById('sk-form'),
                formTitle: document.getElementById('sk-form-title'),
                formSub: document.getElementById('sk-form-sub'),
                formError: document.getElementById('sk-form-error'),
                submit: document.getElementById('sk-submit'),

                id: document.getElementById('sk-id'),
                startDate: document.getElementById('sk-start-date'),
                endDate: document.getElementById('sk-end-date'),
                startTime: document.getElementById('sk-start-time'),
                endTime: document.getElementById('sk-end-time'),
                totalDays: document.getElementById('sk-total-days'),
                totalHours: document.getElementById('sk-total-hours'),
                status: document.getElementById('sk-status'),
                statusMsg: document.getElementById('sk-status-msg'),
                documents: document.getElementById('sk-documents'),
                currentDocsWrap: document.getElementById('sk-current-documents-wrap'),
                currentDocs: document.getElementById('sk-current-documents'),

                docModal: document.getElementById('sk-doc-modal'),
                docSub: document.getElementById('sk-doc-sub'),
                docBody: document.getElementById('sk-doc-body'),

                descModal: document.getElementById('sk-desc-modal'),
                descSub: document.getElementById('sk-desc-sub'),
                descBody: document.getElementById('sk-desc-body'),
            };

            const esc = s => String(s ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const norm = s => String(s || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/ä/g, 'a')
                .replace(/ö/g, 'o')
                .replace(/ü/g, 'u')
                .replace(/ß/g, 'ss')
                .trim();

            function openModal(modal) {
                modal?.classList.add('open');
            }

            function closeModal(modal) {
                modal?.classList.remove('open');
            }

            document.addEventListener('click', e => {
                const close = e.target.closest('[data-close-modal]');

                if (close) {
                    closeModal(document.getElementById(close.dataset.closeModal));
                }

                if (e.target.classList.contains('sk-modal-backdrop')) {
                    closeModal(e.target);
                }
            });

            function dateDE(value) {
                if (!value) return '—';

                const date = new Date(String(value).replace(' ', 'T'));

                if (Number.isNaN(date.getTime())) return value;

                return date.toLocaleDateString('de-DE');
            }

            function fileName(path) {
                return String(path || '').split('/').pop() || 'Dokument';
            }

            function docsOf(item) {
                return Array.isArray(item.documents) ? item.documents : [];
            }

            function docUrlsOf(item) {
                return Array.isArray(item.document_urls) ? item.document_urls : [];
            }

            function statusBadge(status) {
                const value = String(status || '').toLowerCase();

                if (value === 'confirmed') {
                    return '<span class="sk-pill ok"><i class="feather icon-check"></i> Bestätigt</span>';
                }

                if (value === 'rejected') {
                    return '<span class="sk-pill bad"><i class="feather icon-x"></i> Abgelehnt</span>';
                }

                if (value === 'admin_applied') {
                    return '<span class="sk-pill blue"><i class="feather icon-user-check"></i> Admin</span>';
                }

                return '<span class="sk-pill warn"><i class="feather icon-clock"></i> Gemeldet</span>';
            }

            async function jsonFetch(url, options = {}) {
                const headers = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                    ...(options.headers || {}),
                };

                const res = await fetch(url, {
                    ...options,
                    headers,
                });

                const text = await res.text();

                let data = {};

                try {
                    data = JSON.parse(text || '{}');
                } catch (_) {
                    data = { message: text };
                }

                if (!res.ok) {
                    const error = new Error(data.message || 'Fehler');
                    error.data = data;
                    error.status = res.status;
                    throw error;
                }

                return data;
            }

            async function loadEmployeeSicks() {
                el.list.innerHTML = '<div class="sk-loading">Krankmeldungen werden geladen...</div>';

                try {
                    const data = await jsonFetch(API.list);

                    state.sicks = Array.isArray(data.sicks) ? data.sicks : [];
                    state.loaded = true;

                    render();
                } catch (error) {
                    el.list.innerHTML = `
                            <div class="sk-empty">
                                Krankmeldungen konnten nicht geladen werden.<br>
                                <small>${esc(error.data?.message || error.message || '')}</small>
                            </div>
                        `;
                }
            }

            function fillYears() {
                const now = new Date().getFullYear();
                const oldValue = el.yearFilter.value;
                const years = new Set(state.sicks.map(s => Number(s.year)).filter(Boolean));

                for (let y = now - 5; y <= now + 1; y++) {
                    years.add(y);
                }

                el.yearFilter.innerHTML = '<option value="">Alle Jahre</option>' +
                    Array.from(years)
                        .sort((a, b) => b - a)
                        .map(y => `<option value="${y}">${y}</option>`)
                        .join('');

                el.yearFilter.value = oldValue;
            }

            function filteredSicks() {
                const q = norm(el.search.value);
                const year = el.yearFilter.value;
                const docs = el.docsFilter.value;

                return state.sicks.filter(item => {
                    const documents = docsOf(item);

                    const hay = norm([
                        item.id,
                        item.year,
                        item.start_date,
                        item.end_date,
                        item.status,
                        item.status_msg,
                        documents.join(' '),
                    ].join(' '));

                    if (q && !hay.includes(q)) return false;
                    if (year && String(item.year) !== String(year)) return false;
                    if (docs === 'with' && documents.length === 0) return false;
                    if (docs === 'without' && documents.length > 0) return false;

                    return true;
                }).sort((a, b) => String(b.start_date).localeCompare(String(a.start_date)));
            }

            function renderStats(items) {
                el.statTotal.textContent = items.length;
                el.statDays.textContent = items.reduce((sum, item) => sum + Number(item.total_days || 0), 0);
                el.statHours.textContent = items.reduce((sum, item) => sum + Number(item.total_hours || 0), 0);
                el.statDocs.textContent = items.filter(item => docsOf(item).length > 0).length;
            }

            function render() {
                fillYears();

                const items = filteredSicks();

                renderStats(items);

                if (!items.length) {
                    el.list.innerHTML = '<div class="sk-empty">Keine Krankmeldungen gefunden.</div>';
                    return;
                }

                el.list.innerHTML = items.map(item => {
                    const docs = docsOf(item);
                    const docCount = docs.length;

                    return `
                            <div class="sk-row" data-id="${item.id}">
                                <div class="sk-row-inner">
                                    <div>
                                        <div class="sk-mobile-title">ID</div>
                                        <span class="sk-id">#${item.id}</span>
                                    </div>

                                    <div>
                                        <div class="sk-mobile-title">Zeitraum</div>
                                        <div class="sk-main-title">${esc(dateDE(item.start_date))} → ${esc(dateDE(item.end_date || item.start_date))}</div>
                                        <div class="sk-main-sub">
                                            Jahr ${esc(item.year || '—')}
                                            ${item.start_time && item.end_time ? ` · ${esc(item.start_time)} - ${esc(item.end_time)}` : ''}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="sk-mobile-title">Tage</div>
                                        <span class="sk-pill gray">${Number(item.total_days || 0)} Tag(e)</span>
                                    </div>

                                    <div>
                                        <div class="sk-mobile-title">Stunden</div>
                                        <span class="sk-pill gray">${Number(item.total_hours || 0)} Std.</span>
                                    </div>

                                    <div>
                                        <div class="sk-mobile-title">Status</div>
                                        ${statusBadge(item.status)}
                                    </div>

                                    <div>
                                        <div class="sk-mobile-title">Dokumente</div>
                                        ${docCount
                            ? `<button type="button" class="sk-doc-btn" data-action="docs" data-id="${item.id}">
                                                        <i class="feather icon-file-text"></i> ${docCount} Dokument(e)
                                                   </button>`
                            : `<span class="sk-pill gray"><i class="feather icon-file-minus"></i> Keine</span>`
                        }
                                    </div>

                                    <div class="sk-actions-cell">
                                        <button type="button" class="sk-btn-icon" data-action="desc" data-id="${item.id}" title="Beschreibung">
                                            <i class="feather icon-message-square"></i>
                                        </button>

                                        <div class="sk-action-menu">
                                            <button type="button" class="sk-btn-icon" data-action="menu">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>

                                            <div class="sk-action-list">
                                                <button type="button" class="sk-action-item" data-action="edit" data-id="${item.id}">
                                                    <i class="feather icon-edit"></i> Bearbeiten
                                                </button>

                                                <button type="button" class="sk-action-item" data-action="docs" data-id="${item.id}">
                                                    <i class="feather icon-file-text"></i> Dokumente anzeigen
                                                </button>

                                                <button type="button" class="sk-action-item danger" data-action="delete" data-id="${item.id}">
                                                    <i class="feather icon-trash-2"></i> Löschen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                }).join('');

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function calcWorkingDays(startValue, endValue) {
                if (!startValue) return 0;

                const start = new Date(startValue);
                const end = endValue ? new Date(endValue) : new Date(startValue);

                if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) {
                    return 0;
                }

                let days = 0;
                const current = new Date(start);

                while (current <= end) {
                    const day = current.getDay();

                    if (day !== 0 && day !== 6) {
                        days++;
                    }

                    current.setDate(current.getDate() + 1);
                }

                return days;
            }

            function calcHoursFromTime() {
                if (!el.startDate.value || !el.startTime.value || !el.endTime.value) {
                    return null;
                }

                const start = new Date(`${el.startDate.value}T${el.startTime.value}`);
                let end = new Date(`${el.startDate.value}T${el.endTime.value}`);

                if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
                    return null;
                }

                if (end < start) {
                    end.setDate(end.getDate() + 1);
                }

                return Math.round(((end - start) / 36e5) * 100) / 100;
            }

            function calculateTotals() {
                const timeHours = calcHoursFromTime();

                if (timeHours !== null) {
                    el.totalDays.value = 1;
                    el.totalHours.value = timeHours;
                    return;
                }

                const days = calcWorkingDays(el.startDate.value, el.endDate.value);
                el.totalDays.value = days;
                el.totalHours.value = days * 24;
            }

            function resetForm(mode = 'create', item = null) {
                state.mode = mode;

                el.form.reset();
                el.formError.classList.remove('show');
                el.formError.textContent = '';
                el.id.value = '';
                el.currentDocsWrap.style.display = 'none';
                el.currentDocs.innerHTML = '';
                el.totalDays.value = '';
                el.totalHours.value = '';

                if (mode === 'edit' && item) {
                    el.formTitle.textContent = 'Krankmeldung bearbeiten';
                    el.formSub.textContent = `Krankmeldung #${item.id} aktualisieren. Neue Dokumente werden zusätzlich gespeichert.`;

                    el.id.value = item.id;
                    el.startDate.value = item.start_date || '';
                    el.endDate.value = item.end_date || '';
                    el.startTime.value = item.start_time || '';
                    el.endTime.value = item.end_time || '';
                    el.totalDays.value = item.total_days || 0;
                    el.totalHours.value = item.total_hours || 0;
                    el.status.value = item.status || 'employee_applied';
                    el.statusMsg.value = item.status_msg || '';

                    renderCurrentDocuments(item);
                } else {
                    el.formTitle.textContent = 'Neue Krankmeldung';
                    el.formSub.textContent = 'Zeitraum eintragen. Dokumente sind optional.';
                    el.status.value = 'employee_applied';
                }
            }

            function renderCurrentDocuments(item) {
                const docs = docsOf(item);
                const urls = docUrlsOf(item);

                if (!docs.length) {
                    el.currentDocsWrap.style.display = 'none';
                    el.currentDocs.innerHTML = '';
                    return;
                }

                el.currentDocsWrap.style.display = 'block';

                el.currentDocs.innerHTML = docs.map((path, index) => `
                        <div class="sk-doc-row">
                            <a href="${esc(urls[index] || path)}" target="_blank">
                                <i class="feather icon-external-link"></i> ${esc(fileName(path))}
                            </a>

                            <button type="button" class="sk-btn-icon" data-action="delete-doc" data-id="${item.id}" data-index="${index}" title="Dokument löschen">
                                <i class="feather icon-trash-2"></i>
                            </button>
                        </div>
                    `).join('');

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function upsertSick(item) {
                const index = state.sicks.findIndex(s => Number(s.id) === Number(item.id));

                if (index >= 0) {
                    state.sicks[index] = item;
                } else {
                    state.sicks.unshift(item);
                }

                render();
            }

            async function submitForm(e) {
                e.preventDefault();

                calculateTotals();

                const formData = new FormData(el.form);
                const id = el.id.value;
                const url = id ? `${API.updateBase}/${id}` : API.store;

                el.submit.disabled = true;
                el.formError.classList.remove('show');
                el.formError.textContent = '';

                try {
                    const data = await jsonFetch(url, {
                        method: 'POST',
                        body: formData,
                    });

                    upsertSick(data.sick);
                    closeModal(el.formModal);

                    Swal.fire({
                        icon: 'success',
                        title: 'Gespeichert',
                        text: data.message || 'Krankmeldung wurde gespeichert.',
                        timer: 1600,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    const errors = error.data?.errors || {};
                    const lines = [];

                    Object.keys(errors).forEach(key => {
                        lines.push(...errors[key]);
                    });

                    el.formError.textContent = lines.length
                        ? lines.join('\n')
                        : (error.data?.message || error.message || 'Fehler beim Speichern.');

                    el.formError.classList.add('show');
                } finally {
                    el.submit.disabled = false;
                }
            }

            async function editSick(id) {
                try {
                    const data = await jsonFetch(`${API.editBase}/${id}`);
                    resetForm('edit', data.sick);
                    openModal(el.formModal);
                } catch (error) {
                    Swal.fire('Fehler', error.data?.message || 'Datensatz konnte nicht geladen werden.', 'error');
                }
            }

            async function deleteSick(id) {
                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Krankmeldung löschen?',
                    text: 'Diese Aktion löscht auch die gespeicherten Dokumente.',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, löschen',
                    cancelButtonText: 'Abbrechen',
                });

                if (!result.isConfirmed) return;

                try {
                    const data = await jsonFetch(`${API.deleteBase}/${id}`, {
                        method: 'DELETE',
                    });

                    state.sicks = state.sicks.filter(item => Number(item.id) !== Number(id));
                    render();

                    Swal.fire({
                        icon: 'success',
                        title: 'Gelöscht',
                        text: data.message || 'Krankmeldung wurde gelöscht.',
                        timer: 1600,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire('Fehler', error.data?.message || 'Krankmeldung konnte nicht gelöscht werden.', 'error');
                }
            }

            async function deleteDocument(id, index) {
                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Dokument löschen?',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, löschen',
                    cancelButtonText: 'Abbrechen',
                });

                if (!result.isConfirmed) return;

                try {
                    const data = await jsonFetch(`${API.deleteDocBase}/${id}/document/${index}`, {
                        method: 'DELETE',
                    });

                    upsertSick(data.sick);

                    if (Number(el.id.value) === Number(id)) {
                        renderCurrentDocuments(data.sick);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Dokument gelöscht',
                        timer: 1400,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire('Fehler', error.data?.message || 'Dokument konnte nicht gelöscht werden.', 'error');
                }
            }

            function showDocuments(id) {
                const item = state.sicks.find(s => Number(s.id) === Number(id));

                if (!item) return;

                const docs = docsOf(item);
                const urls = docUrlsOf(item);

                el.docSub.textContent = `Krankmeldung #${item.id} · ${dateDE(item.start_date)} bis ${dateDE(item.end_date || item.start_date)}`;

                if (!docs.length) {
                    el.docBody.innerHTML = '<div class="sk-empty" style="margin:0;">Keine Dokumente vorhanden.</div>';
                } else {
                    el.docBody.innerHTML = docs.map((path, index) => `
                            <div class="sk-doc-row">
                                <a href="${esc(urls[index] || path)}" target="_blank">
                                    <i class="feather icon-external-link"></i> ${esc(fileName(path))}
                                </a>

                                <button type="button" class="sk-btn-icon" data-action="delete-doc" data-id="${item.id}" data-index="${index}" title="Dokument löschen">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </div>
                        `).join('');
                }

                if (window.feather) {
                    window.feather.replace();
                }

                openModal(el.docModal);
            }

            function showDescription(id) {
                const item = state.sicks.find(s => Number(s.id) === Number(id));

                if (!item) return;

                el.descSub.textContent = `Krankmeldung #${item.id}`;
                el.descBody.textContent = item.status_msg || 'Keine Beschreibung vorhanden.';

                openModal(el.descModal);
            }

            document.addEventListener('click', e => {
                const menuBtn = e.target.closest('[data-action="menu"]');

                document.querySelectorAll('.sk-action-list.show').forEach(menu => {
                    if (!menuBtn || !menu.closest('.sk-action-menu').contains(menuBtn)) {
                        menu.classList.remove('show');
                        menu.closest('.sk-row')?.classList.remove('menu-open');
                    }
                });

                if (menuBtn) {
                    e.preventDefault();
                    e.stopPropagation();

                    const row = menuBtn.closest('.sk-row');
                    const list = menuBtn.closest('.sk-action-menu')?.querySelector('.sk-action-list');

                    if (list) {
                        list.classList.toggle('show');
                        row?.classList.toggle('menu-open', list.classList.contains('show'));
                    }

                    return;
                }

                const action = e.target.closest('[data-action]');

                if (!action) return;

                const type = action.dataset.action;
                const id = action.dataset.id;

                if (type === 'edit') editSick(id);
                if (type === 'delete') deleteSick(id);
                if (type === 'docs') showDocuments(id);
                if (type === 'desc') showDescription(id);
                if (type === 'delete-doc') deleteDocument(id, action.dataset.index);
            });

            el.createBtn.addEventListener('click', () => {
                resetForm('create');
                openModal(el.formModal);
            });

            el.form.addEventListener('submit', submitForm);

            [el.startDate, el.endDate, el.startTime, el.endTime].forEach(input => {
                input.addEventListener('change', calculateTotals);
                input.addEventListener('input', calculateTotals);
            });

            [el.search, el.yearFilter, el.docsFilter].forEach(input => {
                input.addEventListener('input', render);
                input.addEventListener('change', render);
            });

            el.resetFilter.addEventListener('click', () => {
                el.search.value = '';
                el.yearFilter.value = '';
                el.docsFilter.value = '';
                render();
            });

            el.refresh.addEventListener('click', loadEmployeeSicks);

            // Load when Blade is included
            loadEmployeeSicks();

            // Load again when sick tab is opened, useful if tab was hidden on page load
            document.querySelector('#sick-tabs')?.addEventListener('click', () => {
                if (!state.loaded) {
                    loadEmployeeSicks();
                }
            });
        })();
    </script>
@endpush