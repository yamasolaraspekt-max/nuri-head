@php
    $employeeId = $data->id ?? null;
    $employeeName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
    $employeeName = $employeeName !== '' ? $employeeName : 'Mitarbeiter #' . ($employeeId ?? '');
@endphp

@once
    @push('style')
        <style>
            :root {
                --rec-bg: #f3f4f6;
                --rec-card: #ffffff;
                --rec-text: #1f2937;
                --rec-muted: #6b7280;
                --rec-border: #e5e7eb;
                --rec-primary: #93c21c;
                --rec-primary-dark: #7baa18;
                --rec-primary-light: #f4fae7;
                --rec-blue: #74b2d4;
                --rec-blue-light: #eff6ff;
                --rec-success: #10b981;
                --rec-success-light: #ecfdf5;
                --rec-warning: #f59e0b;
                --rec-warning-light: #fffbeb;
                --rec-danger: #ef4444;
                --rec-danger-light: #fef2f2;
                --rec-note: #8b5cf6;
                --rec-note-light: #f5f3ff;
                --rec-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
                --rec-shadow: 0 18px 45px rgba(15, 23, 42, .16);
                --rec-radius: 16px;
                --rec-transition: all .2s ease;
            }

            .rec-wrap { font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--rec-text); }
            .rec-header { display:flex; align-items:flex-end; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:18px; }
            .rec-title { font-size:26px; font-weight:900; letter-spacing:-.03em; color:#111827; margin:0; }
            .rec-sub { font-size:14px; color:var(--rec-muted); margin-top:4px; }
            .rec-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
            .rec-btn { border:none; border-radius:12px; padding:10px 15px; font-weight:900; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; transition:var(--rec-transition); height:42px; white-space:nowrap; }
            .rec-btn-primary { background:var(--rec-primary); color:#fff; box-shadow:0 10px 22px rgba(147,194,28,.22); }
            .rec-btn-primary:hover { background:var(--rec-primary-dark); color:#fff; transform:translateY(-1px); }
            .rec-btn-soft { background:#fff; color:var(--rec-text); border:1px solid var(--rec-border); }
            .rec-btn-soft:hover { background:#f9fafb; color:var(--rec-text); text-decoration:none; }
            .rec-btn-danger { background:var(--rec-danger); color:#fff; }
            .rec-btn-danger:hover { background:#dc2626; color:#fff; }
            .rec-btn-icon { width:38px; height:38px; border-radius:11px; border:1px solid var(--rec-border); background:#fff; color:var(--rec-muted); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:var(--rec-transition); }
            .rec-btn-icon:hover { border-color:var(--rec-blue); color:var(--rec-blue); background:#f0f7fb; }

            .rec-stats { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:14px; margin-bottom:18px; }
            @media(max-width:1200px){ .rec-stats{grid-template-columns:repeat(2,minmax(0,1fr));} }
            @media(max-width:720px){ .rec-stats{grid-template-columns:1fr;} }
            .rec-stat { background:#fff; border:1px solid var(--rec-border); border-radius:18px; padding:16px; box-shadow:var(--rec-shadow-sm); display:flex; gap:12px; align-items:center; min-height:92px; }
            .rec-stat-ic { width:48px; height:48px; border-radius:15px; display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto; }
            .rec-stat-ic.total { background:var(--rec-blue-light); color:var(--rec-blue); }
            .rec-stat-ic.ok { background:var(--rec-success-light); color:var(--rec-success); }
            .rec-stat-ic.home { background:var(--rec-primary-light); color:#4d7c0f; }
            .rec-stat-ic.note { background:var(--rec-note-light); color:var(--rec-note); }
            .rec-stat-label { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; color:var(--rec-muted); }
            .rec-stat-value { font-size:24px; font-weight:900; color:#111827; line-height:1.1; margin-top:4px; }
            .rec-stat-sub { font-size:12px; color:var(--rec-muted); margin-top:4px; }

            .rec-toolbar { background:#fff; border:1px solid var(--rec-border); border-radius:var(--rec-radius); padding:14px 16px; display:flex; gap:12px; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; box-shadow:var(--rec-shadow-sm); margin-bottom:16px; }
            .rec-filter-left, .rec-filter-right { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
            .rec-filter-left { flex:1; }
            .rec-field { display:flex; flex-direction:column; gap:6px; min-width:170px; }
            .rec-field.search { flex:1; min-width:260px; }
            .rec-label { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; color:var(--rec-muted); }
            .rec-input, .rec-select, .rec-textarea { width:100%; border:1px solid var(--rec-border); border-radius:11px; background:#fff; padding:10px 12px; font-size:14px; outline:none; transition:var(--rec-transition); }
            .rec-input:focus, .rec-select:focus, .rec-textarea:focus { border-color:var(--rec-primary); box-shadow:0 0 0 3px var(--rec-primary-light); }
            .rec-textarea { resize:vertical; min-height:96px; }
            .rec-help { font-size:12px; color:var(--rec-muted); margin-top:5px; }
            .rec-error { display:none; margin-top:8px; border-radius:12px; background:var(--rec-danger-light); color:#991b1b; padding:10px 12px; font-size:13px; font-weight:800; white-space:pre-wrap; }
            .rec-error.show { display:block; }

            .rec-card { background:#fff; border:1px solid var(--rec-border); border-radius:18px; box-shadow:var(--rec-shadow-sm); overflow:visible; position:relative; z-index:1; }
            .rec-list-head, .rec-row-inner { display:grid; grid-template-columns:90px minmax(220px,1fr) minmax(150px,.8fr) 150px 130px 110px 130px; gap:14px; align-items:center; }
            .rec-list-head { padding:16px 18px 10px; color:var(--rec-muted); font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; }
            .rec-list { display:flex; flex-direction:column; gap:12px; padding:0 0 90px; overflow:visible; }
            .rec-row { margin:0 16px; border:1px solid var(--rec-border); border-radius:16px; background:#fff; transition:var(--rec-transition); overflow:visible; position:relative; }
            .rec-row:hover { border-color:var(--rec-primary); box-shadow:var(--rec-shadow); }
            .rec-row-inner { padding:16px; }
            @media(max-width:1280px){ .rec-list-head{display:none;} .rec-row-inner{grid-template-columns:1fr;} .rec-mobile-title{display:block!important;} .rec-row{margin:0 12px;} }
            .rec-mobile-title { display:none; font-size:11px; font-weight:900; text-transform:uppercase; color:var(--rec-muted); letter-spacing:.06em; margin-bottom:5px; }
            .rec-id { display:inline-flex; align-items:center; justify-content:center; height:34px; min-width:58px; padding:0 10px; border-radius:10px; background:var(--rec-blue-light); color:var(--rec-blue); font-weight:900; font-size:13px; }
            .rec-main-title { font-weight:900; color:#111827; font-size:15px; margin-bottom:4px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
            .rec-main-sub { font-size:13px; color:var(--rec-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:520px; }
            .rec-pill { display:inline-flex; align-items:center; gap:6px; border-radius:999px; padding:6px 10px; font-size:12px; font-weight:900; white-space:nowrap; }
            .rec-pill.absence { background:var(--rec-blue-light); color:#0369a1; }
            .rec-pill.home_office { background:var(--rec-primary-light); color:#4d7c0f; }
            .rec-pill.note { background:var(--rec-note-light); color:#6d28d9; }
            .rec-pill.ok { background:var(--rec-success-light); color:#047857; }
            .rec-pill.warn { background:var(--rec-warning-light); color:#b45309; }
            .rec-pill.bad { background:var(--rec-danger-light); color:#b91c1c; }
            .rec-pill.gray { background:#f3f4f6; color:#374151; }
            .rec-desc-preview { border:1px solid var(--rec-border); background:#f8fafc; border-radius:12px; padding:9px 10px; font-size:13px; color:#374151; line-height:1.35; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:pointer; }
            .rec-desc-preview:hover { background:#f0f7fb; border-color:rgba(116,178,212,.5); }
            .rec-actions-cell { display:flex; justify-content:flex-end; gap:8px; align-items:center; position:relative; overflow:visible; }
            @media(max-width:1280px){ .rec-actions-cell{justify-content:flex-start;} }
            .rec-empty { margin:16px; padding:46px 20px; text-align:center; color:var(--rec-muted); border:1px dashed var(--rec-border); border-radius:16px; background:#fff; }

            .rec-modal-backdrop { position:fixed; inset:0; z-index:1250; background:rgba(17,24,39,.55); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; padding:18px; opacity:0; visibility:hidden; transition:opacity .22s ease, visibility .22s ease; }
            .rec-modal-backdrop.open { opacity:1; visibility:visible; }
            .rec-modal { width:min(860px,100%); max-height:90vh; background:#fff; border-radius:20px; border:1px solid rgba(229,231,235,.95); box-shadow:var(--rec-shadow); overflow:hidden; transform:translateY(14px) scale(.985); transition:transform .22s ease; display:flex; flex-direction:column; }
            .rec-modal.xl { width:min(1180px,100%); }
            .rec-modal-backdrop.open .rec-modal { transform:translateY(0) scale(1); }
            .rec-modal-header { display:grid; grid-template-columns:50px 1fr 38px; gap:12px; align-items:flex-start; padding:18px 20px; border-bottom:1px solid var(--rec-border); background:linear-gradient(135deg,#fff,#f8fcff); }
            .rec-modal-icon { width:50px; height:50px; border-radius:16px; background:var(--rec-primary-light); color:#4d7c0f; display:inline-flex; align-items:center; justify-content:center; }
            .rec-modal-title { margin:0; font-size:18px; font-weight:900; color:#111827; }
            .rec-modal-sub { font-size:13px; color:var(--rec-muted); margin-top:4px; }
            .rec-modal-body { padding:20px; overflow:auto; }
            .rec-modal-footer { border-top:1px solid var(--rec-border); padding:14px 20px; background:#fafafa; display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap; }
            .rec-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
            .rec-form-grid.three { grid-template-columns:repeat(3,minmax(0,1fr)); }
            @media(max-width:760px){ .rec-form-grid,.rec-form-grid.three{grid-template-columns:1fr;} .rec-modal-header{grid-template-columns:42px 1fr 36px;} .rec-modal-icon{width:42px;height:42px;border-radius:14px;} .rec-modal-footer .rec-btn{width:100%;} }
            .rec-kind-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
            @media(max-width:760px){ .rec-kind-grid{grid-template-columns:1fr;} }
            .rec-kind { border:1px solid var(--rec-border); background:#fff; border-radius:15px; padding:12px; cursor:pointer; display:flex; gap:10px; align-items:flex-start; transition:var(--rec-transition); }
            .rec-kind.active { border-color:var(--rec-primary); box-shadow:0 0 0 3px var(--rec-primary-light); }
            .rec-kind input { margin-top:4px; }
            .rec-kind strong { display:block; color:#111827; font-weight:900; }
            .rec-kind span { display:block; color:var(--rec-muted); font-size:12px; margin-top:2px; }
            .rec-weekdays { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:8px; }
            @media(max-width:760px){ .rec-weekdays{grid-template-columns:repeat(4,minmax(0,1fr));} }
            .rec-day { border:1px solid var(--rec-border); background:#fff; border-radius:12px; padding:10px; text-align:center; font-weight:900; cursor:pointer; transition:var(--rec-transition); }
            .rec-day.active { background:var(--rec-primary); color:#fff; border-color:var(--rec-primary); }
            .rec-switch { display:inline-flex; align-items:center; gap:10px; cursor:pointer; user-select:none; }
            .rec-switch input { display:none; }
            .rec-switch span { width:46px; height:26px; background:#d1d5db; border-radius:999px; position:relative; transition:var(--rec-transition); }
            .rec-switch span:after { content:""; width:20px; height:20px; border-radius:50%; background:#fff; position:absolute; left:3px; top:3px; transition:var(--rec-transition); box-shadow:0 1px 3px rgba(0,0,0,.18); }
            .rec-switch input:checked + span { background:var(--rec-primary); }
            .rec-switch input:checked + span:after { transform:translateX(20px); }
            .rec-occ-table { width:100%; border-collapse:separate; border-spacing:0; }
            .rec-occ-table th { text-align:left; padding:12px 14px; background:#f8fafc; color:var(--rec-muted); font-size:11px; text-transform:uppercase; letter-spacing:.06em; font-weight:900; position:sticky; top:0; z-index:2; }
            .rec-occ-table td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
            .rec-occ-table tr:hover td { background:#f8fafc; }
            .rec-occ-date { font-weight:900; color:#111827; }
            .rec-occ-sub { font-size:12px; color:var(--rec-muted); margin-top:2px; }
            .rec-row-cancelled td { opacity:.68; background:#fff5f5; }
            .rec-row-cancelled .rec-occ-date { text-decoration:line-through; }
        </style>
    @endpush
@endonce

<div class="rec-wrap" id="recurring-event-app" data-employee-id="{{ $employeeId }}">
    @if(!$employeeId)
        <div class="alert alert-warning">
            <i class="feather icon-alert-triangle"></i> Bitte speichern Sie zuerst den Mitarbeiter, bevor Sie wiederkehrende Termine hinzufügen.
        </div>
    @else
        <div class="rec-header">
            <div>
                <h2 class="rec-title">Wiederkehrende Termine</h2>
                <div class="rec-sub">Abwesenheiten, Home Office und Notizen von {{ $employeeName }} komplett per AJAX verwalten.</div>
            </div>
            <div class="rec-actions">
                <button type="button" class="rec-btn rec-btn-soft" id="rec-refresh-btn"><i class="feather icon-refresh-cw"></i> Aktualisieren</button>
                <button type="button" class="rec-btn rec-btn-primary" id="rec-open-create"><i class="feather icon-plus"></i> Neuer Termin</button>
            </div>
        </div>

        <div class="rec-stats">
            <div class="rec-stat"><div class="rec-stat-ic total"><i class="feather icon-layers"></i></div><div><div class="rec-stat-label">Gesamt</div><div class="rec-stat-value" id="rec-stat-total">0</div><div class="rec-stat-sub">Regeln</div></div></div>
            <div class="rec-stat"><div class="rec-stat-ic ok"><i class="feather icon-check-circle"></i></div><div><div class="rec-stat-label">Aktiv</div><div class="rec-stat-value" id="rec-stat-active">0</div><div class="rec-stat-sub">Laufen aktuell</div></div></div>
            <div class="rec-stat"><div class="rec-stat-ic home"><i class="feather icon-home"></i></div><div><div class="rec-stat-label">Home Office</div><div class="rec-stat-value" id="rec-stat-home">0</div><div class="rec-stat-sub">Regeln</div></div></div>
            <div class="rec-stat"><div class="rec-stat-ic note"><i class="feather icon-file-text"></i></div><div><div class="rec-stat-label">Notizen</div><div class="rec-stat-value" id="rec-stat-note">0</div><div class="rec-stat-sub">Merker im Kalender</div></div></div>
        </div>

        <div class="rec-toolbar">
            <div class="rec-filter-left">
                <div class="rec-field search"><label class="rec-label">Suche</label><input class="rec-input" id="rec-search" placeholder="Suche nach Titel, Regel, Beschreibung..."></div>
                <div class="rec-field"><label class="rec-label">Art</label><select class="rec-select" id="rec-filter-kind"><option value="">Alle Arten</option><option value="absence">Abwesenheit</option><option value="home_office">Home Office</option><option value="note">Notiz</option></select></div>
                <div class="rec-field"><label class="rec-label">Status</label><select class="rec-select" id="rec-filter-status"><option value="">Alle Status</option><option value="active">Aktiv</option><option value="inactive">Inaktiv</option></select></div>
            </div>
            <div class="rec-filter-right"><button type="button" class="rec-btn rec-btn-soft" id="rec-reset-filter"><i class="feather icon-x"></i> Filter löschen</button></div>
        </div>

        <div class="rec-card">
            <div class="rec-list-head"><div>ID</div><div>Titel & Regel</div><div>Art</div><div>Gültigkeit</div><div>Zeit</div><div>Status</div><div style="text-align:right;">Aktionen</div></div>
            <div class="rec-list" id="rec-list"></div>
        </div>

        <div class="rec-modal-backdrop" id="rec-form-modal">
            <div class="rec-modal">
                <div class="rec-modal-header">
                    <div class="rec-modal-icon"><i class="feather icon-calendar"></i></div>
                    <div><h3 class="rec-modal-title" id="rec-form-title">Neuer wiederkehrender Termin</h3><div class="rec-modal-sub" id="rec-form-sub">Regel, Art und Zeitraum eintragen.</div></div>
                    <button type="button" class="rec-btn-icon" data-rec-close="rec-form-modal"><i class="feather icon-x"></i></button>
                </div>
                <form id="rec-form">
                    @csrf
                    <input type="hidden" id="rec-id">
                    <div class="rec-modal-body">
                        <div class="rec-field" style="margin-bottom:14px;">
                            <label class="rec-label">Termin-Art</label>
                            <div class="rec-kind-grid" id="rec-kind-grid">
                                <label class="rec-kind active"><input type="radio" name="event_kind" value="absence" checked><div><strong>Abwesenheit</strong><span>Urlaub, Schule, Krankheit oder Sperrzeit</span></div></label>
                                <label class="rec-kind"><input type="radio" name="event_kind" value="home_office"><div><strong>Home Office</strong><span>Regelmäßig von Zuhause arbeiten</span></div></label>
                                <label class="rec-kind"><input type="radio" name="event_kind" value="note"><div><strong>Notiz</strong><span>Kalender-Merker ohne Abwesenheitslogik</span></div></label>
                            </div>
                        </div>

                        <div class="rec-form-grid">
                            <div class="rec-field"><label class="rec-label">Titel <span class="text-danger">*</span></label><input type="text" id="rec-title-input" class="rec-input" required placeholder="z. B. Berufsschule / Home Office"></div>
                            <div class="rec-field"><label class="rec-label">Regel-Typ</label><select id="rec-type" class="rec-select"><option value="weekly">Wöchentlich</option><option value="monthly">Monatlich</option><option value="interval">Intervall</option><option value="one_time">Einmalig</option></select></div>
                        </div>

                        <div class="rec-field rec-type-row rec-type-weekly" style="margin-top:14px;"><label class="rec-label">Wochentage</label><div class="rec-weekdays" id="rec-weekdays"><button type="button" class="rec-day" data-day="1">Mo</button><button type="button" class="rec-day" data-day="2">Di</button><button type="button" class="rec-day" data-day="3">Mi</button><button type="button" class="rec-day" data-day="4">Do</button><button type="button" class="rec-day" data-day="5">Fr</button><button type="button" class="rec-day" data-day="6">Sa</button><button type="button" class="rec-day" data-day="7">So</button></div></div>

                        <div class="rec-form-grid rec-type-row rec-type-weekly" style="margin-top:14px;"><div class="rec-field"><label class="rec-label">Alle X Wochen</label><input type="number" id="rec-week-interval" min="1" value="1" class="rec-input"></div><div class="rec-field"><label class="rec-label">Dauer in Tagen</label><input type="number" id="rec-duration-days" min="1" value="1" class="rec-input"></div></div>
                        <div class="rec-form-grid rec-type-row rec-type-monthly" style="margin-top:14px; display:none;"><div class="rec-field"><label class="rec-label">Tag des Monats</label><input type="number" id="rec-day-of-month" min="1" max="31" class="rec-input"></div><div class="rec-field"><label class="rec-label">Alle X Monate</label><input type="number" id="rec-month-interval" min="1" value="1" class="rec-input"></div></div>
                        <div class="rec-field rec-type-row rec-type-interval" style="margin-top:14px; display:none;"><label class="rec-label">Alle X Tage</label><input type="number" id="rec-interval-days" min="1" value="14" class="rec-input"></div>

                        <div class="rec-form-grid" style="margin-top:14px;"><div class="rec-field"><label class="rec-label">Gültig ab <span class="text-danger">*</span></label><input type="date" id="rec-start-date" class="rec-input" required></div><div class="rec-field"><label class="rec-label">Gültig bis</label><input type="date" id="rec-end-date" class="rec-input"><div class="rec-help">Leer lassen = unbegrenzt.</div></div></div>
                        <div class="rec-field" style="margin-top:14px;"><label class="rec-switch"><input type="checkbox" id="rec-all-day" checked><span></span><strong>Ganztägig</strong></label></div>
                        <div class="rec-form-grid" id="rec-time-row" style="margin-top:14px; display:none;"><div class="rec-field"><label class="rec-label">Startzeit</label><input type="time" id="rec-start-time" class="rec-input"></div><div class="rec-field"><label class="rec-label">Endzeit</label><input type="time" id="rec-end-time" class="rec-input"></div></div>
                        <div class="rec-field" style="margin-top:14px;"><label class="rec-label">Beschreibung</label><textarea id="rec-description" class="rec-textarea" placeholder="Optionale Beschreibung..."></textarea></div>
                        <div class="rec-error" id="rec-form-error"></div>
                    </div>
                    <div class="rec-modal-footer"><button type="button" class="rec-btn rec-btn-soft" data-rec-close="rec-form-modal">Abbrechen</button><button type="submit" class="rec-btn rec-btn-primary" id="rec-submit"><i class="feather icon-save"></i> Speichern</button></div>
                </form>
            </div>
        </div>

        <div class="rec-modal-backdrop" id="rec-occ-modal">
            <div class="rec-modal xl">
                <div class="rec-modal-header">
                    <div class="rec-modal-icon"><i class="feather icon-calendar"></i></div>
                    <div><h3 class="rec-modal-title" id="rec-occ-title">Termin-Verwaltung</h3><div class="rec-modal-sub" id="rec-occ-sub">Einzelne Vorkommnisse verschieben, ändern, absagen oder wiederherstellen.</div></div>
                    <button type="button" class="rec-btn-icon" data-rec-close="rec-occ-modal"><i class="feather icon-x"></i></button>
                </div>
                <div class="rec-modal-body">
                    <div class="rec-toolbar" style="margin-bottom:14px;"><div class="rec-filter-left"><div class="rec-field"><label class="rec-label">Von</label><input type="date" id="rec-occ-from" class="rec-input"></div><div class="rec-field"><label class="rec-label">Bis</label><input type="date" id="rec-occ-to" class="rec-input"></div></div><div class="rec-filter-right"><button type="button" class="rec-btn rec-btn-primary" id="rec-occ-load"><i class="feather icon-search"></i> Laden</button></div></div>
                    <div style="overflow:auto; max-height:55vh;"><table class="rec-occ-table"><thead><tr><th>Datum</th><th>Zeit</th><th>Details</th><th>Art</th><th>Status</th><th style="text-align:right;">Aktionen</th></tr></thead><tbody id="rec-occ-body"></tbody></table></div>
                </div>
            </div>
        </div>

        <div class="rec-modal-backdrop" id="rec-override-modal">
            <div class="rec-modal">
                <div class="rec-modal-header">
                    <div class="rec-modal-icon"><i class="feather icon-edit"></i></div>
                    <div><h3 class="rec-modal-title">Einzeltermin bearbeiten</h3><div class="rec-modal-sub" id="rec-override-sub"></div></div>
                    <button type="button" class="rec-btn-icon" data-rec-close="rec-override-modal"><i class="feather icon-x"></i></button>
                </div>
                <form id="rec-override-form">
                    <div class="rec-modal-body">
                        <input type="hidden" id="rec-override-original-date">
                        <div class="rec-form-grid"><div class="rec-field"><label class="rec-label">Neues Datum</label><input type="date" id="rec-override-new-date" class="rec-input"></div><div class="rec-field"><label class="rec-label">Dauer in Tagen</label><input type="number" min="1" id="rec-override-duration" class="rec-input"></div></div>
                        <div class="rec-field" style="margin-top:14px;"><label class="rec-switch"><input type="checkbox" id="rec-override-all-day"><span></span><strong>Ganztägig</strong></label></div>
                        <div class="rec-form-grid" id="rec-override-time-row" style="margin-top:14px;"><div class="rec-field"><label class="rec-label">Startzeit</label><input type="time" id="rec-override-start-time" class="rec-input"></div><div class="rec-field"><label class="rec-label">Endzeit</label><input type="time" id="rec-override-end-time" class="rec-input"></div></div>
                        <div class="rec-field" style="margin-top:14px;"><label class="rec-label">Titel überschreiben</label><input type="text" id="rec-override-title" class="rec-input"></div>
                        <div class="rec-field" style="margin-top:14px;"><label class="rec-label">Beschreibung überschreiben</label><textarea id="rec-override-description" class="rec-textarea"></textarea></div>
                        <div class="rec-error" id="rec-override-error"></div>
                    </div>
                    <div class="rec-modal-footer"><button type="button" class="rec-btn rec-btn-soft" data-rec-close="rec-override-modal">Abbrechen</button><button type="button" class="rec-btn rec-btn-danger" id="rec-override-cancel-occ"><i class="feather icon-x-circle"></i> Absagen</button><button type="submit" class="rec-btn rec-btn-primary"><i class="feather icon-save"></i> Ausnahme speichern</button></div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const app = document.getElementById('recurring-event-app');
            if (!app || app.dataset.ready === '1' || !app.dataset.employeeId) return;
            app.dataset.ready = '1';

            const employeeId = app.dataset.employeeId;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
            const routeTemplate = {
                index: @json(route('admin.employees.recurring.index', ['employee' => $employeeId])),
                store: @json(route('admin.employees.recurring.store', ['employee' => $employeeId])),
                update: @json(route('admin.employees.recurring.update', ['employee' => $employeeId, 'leave' => '__ID__'])),
                destroy: @json(route('admin.employees.recurring.destroy', ['employee' => $employeeId, 'leave' => '__ID__'])),
                occurrences: @json(route('admin.employees.recurring.occurrences', ['employee' => $employeeId, 'leave' => '__ID__'])),
                exdateAdd: @json(route('admin.employees.recurring.exdate.add', ['employee' => $employeeId, 'leave' => '__ID__'])),
                exdateRemove: @json(route('admin.employees.recurring.exdate.remove', ['employee' => $employeeId, 'leave' => '__ID__'])),
                overrideUpsert: @json(route('admin.employees.recurring.override.upsert', ['employee' => $employeeId, 'leave' => '__ID__'])),
                overrideDelete: @json(route('admin.employees.recurring.override.delete', ['employee' => $employeeId, 'leave' => '__ID__'])),
            };
            const routes = Object.fromEntries(Object.entries(routeTemplate).map(([key, value]) => [key, (id) => value.replace('__ID__', id)]));
            routes.index = routeTemplate.index;
            routes.store = routeTemplate.store;

            let items = [];
            let currentLeaveId = null;
            let currentOccurrences = [];

            const $ = (id) => document.getElementById(id);
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
            const formatDate = (date) => date ? new Date(date + 'T00:00:00').toLocaleDateString('de-DE', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
            const kindIcon = (kind) => kind === 'home_office' ? 'icon-home' : (kind === 'note' ? 'icon-file-text' : 'icon-calendar');
            const notify = (type, message) => {
                if (window.toastr) return window.toastr[type === 'error' ? 'error' : 'success'](message);
                if (window.Swal) return Swal.fire({ icon: type === 'error' ? 'error' : 'success', title: message, timer: 1600, showConfirmButton: false });
                alert(message);
            };

            async function api(url, options = {}) {
                const res = await fetch(url, {
                    ...options,
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) },
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.ok === false) throw json;
                return json;
            }

            function openModal(id) { $(id)?.classList.add('open'); document.body.style.overflow = 'hidden'; }
            function closeModal(id) { $(id)?.classList.remove('open'); if (!document.querySelector('.rec-modal-backdrop.open')) document.body.style.overflow = ''; }
            document.querySelectorAll('[data-rec-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.recClose)));
            document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.rec-modal-backdrop.open').forEach(m => closeModal(m.id)); });

            function updateStats(stats = {}) {
                $('rec-stat-total').textContent = stats.total ?? items.length;
                $('rec-stat-active').textContent = stats.active ?? items.filter(i => i.is_active).length;
                $('rec-stat-home').textContent = stats.home_office ?? items.filter(i => i.event_kind === 'home_office').length;
                $('rec-stat-note').textContent = stats.note ?? items.filter(i => i.event_kind === 'note').length;
            }

            async function loadList() {
                $('rec-list').innerHTML = '<div class="rec-empty">Lade wiederkehrende Termine...</div>';
                try {
                    const json = await api(routes.index);
                    items = json.data || [];
                    updateStats(json.stats || {});
                    renderList();
                    if (window.feather) feather.replace();
                } catch (e) {
                    $('rec-list').innerHTML = '<div class="rec-empty" style="color:#dc2626;">Fehler beim Laden der Daten.</div>';
                }
            }

            function filteredItems() {
                const q = $('rec-search').value.trim().toLowerCase();
                const kind = $('rec-filter-kind').value;
                const status = $('rec-filter-status').value;
                return items.filter(item => {
                    const hay = [item.title, item.description, item.rule_human, item.period, item.time, item.event_kind_label].join(' ').toLowerCase();
                    if (q && !hay.includes(q)) return false;
                    if (kind && item.event_kind !== kind) return false;
                    if (status === 'active' && !item.is_active) return false;
                    if (status === 'inactive' && item.is_active) return false;
                    return true;
                });
            }

            function renderList() {
                const rows = filteredItems();
                const list = $('rec-list');
                if (!rows.length) {
                    list.innerHTML = '<div class="rec-empty"><strong>Keine wiederkehrenden Termine gefunden.</strong><br>Erstelle eine neue Regel oder ändere den Filter.</div>';
                    return;
                }
                list.innerHTML = rows.map(item => `
                    <div class="rec-row" data-id="${item.id}">
                        <div class="rec-row-inner">
                            <div><div class="rec-mobile-title">ID</div><span class="rec-id">#${item.id}</span></div>
                            <div><div class="rec-mobile-title">Titel & Regel</div><div class="rec-main-title"><i class="feather ${kindIcon(item.event_kind)}"></i>${escapeHtml(item.title)}</div><div class="rec-main-sub">${escapeHtml(item.rule_human)} ${item.exceptions_count ? ' · ' + item.exceptions_count + ' Ausnahmen' : ''}</div></div>
                            <div><div class="rec-mobile-title">Art</div><span class="rec-pill ${item.event_kind}">${escapeHtml(item.event_kind_label)}</span></div>
                            <div><div class="rec-mobile-title">Gültigkeit</div><span class="rec-pill gray">${escapeHtml(item.period)}</span></div>
                            <div><div class="rec-mobile-title">Zeit</div><span class="rec-pill warn">${escapeHtml(item.time)}</span></div>
                            <div><div class="rec-mobile-title">Status</div><span class="rec-pill ${item.is_active ? 'ok' : 'bad'}">${item.is_active ? 'Aktiv' : 'Inaktiv'}</span></div>
                            <div class="rec-actions-cell"><div class="rec-mobile-title">Aktionen</div><button type="button" class="rec-btn-icon rec-occ" title="Termine" data-id="${item.id}"><i class="feather icon-calendar"></i></button><button type="button" class="rec-btn-icon rec-edit" title="Bearbeiten" data-id="${item.id}"><i class="feather icon-edit-2"></i></button><button type="button" class="rec-btn-icon rec-toggle" title="Status wechseln" data-id="${item.id}"><i class="feather ${item.is_active ? 'icon-toggle-right' : 'icon-toggle-left'}"></i></button><button type="button" class="rec-btn-icon rec-delete" title="Löschen" data-id="${item.id}"><i class="feather icon-trash-2"></i></button></div>
                        </div>
                    </div>`).join('');
                list.querySelectorAll('.rec-edit').forEach(btn => btn.onclick = () => openEdit(btn.dataset.id));
                list.querySelectorAll('.rec-delete').forEach(btn => btn.onclick = () => deleteItem(btn.dataset.id));
                list.querySelectorAll('.rec-toggle').forEach(btn => btn.onclick = () => toggleActive(btn.dataset.id));
                list.querySelectorAll('.rec-occ').forEach(btn => btn.onclick = () => openOccurrences(btn.dataset.id));
                if (window.feather) feather.replace();
            }

            function resetForm() {
                $('rec-form').reset(); $('rec-id').value = ''; $('rec-form-error').classList.remove('show'); $('rec-form-error').textContent = '';
                document.querySelectorAll('.rec-day').forEach(d => d.classList.remove('active'));
                document.querySelector('[name="event_kind"][value="absence"]').checked = true;
                updateKindCards(); updateTypeRows(); updateTimeRows();
                $('rec-form-title').textContent = 'Neuer wiederkehrender Termin';
                $('rec-form-sub').textContent = 'Regel, Art und Zeitraum eintragen.';
                $('rec-submit').innerHTML = '<i class="feather icon-save"></i> Speichern';
            }

            function openCreate() { resetForm(); openModal('rec-form-modal'); }
            function openEdit(id) {
                const item = items.find(i => String(i.id) === String(id));
                if (!item) return;
                resetForm();
                $('rec-id').value = item.id; $('rec-title-input').value = item.title; $('rec-type').value = item.type; $('rec-start-date').value = item.start_date || ''; $('rec-end-date').value = item.end_date || ''; $('rec-all-day').checked = !!item.all_day; $('rec-start-time').value = item.start_time || ''; $('rec-end-time').value = item.end_time || ''; $('rec-description').value = item.description || ''; $('rec-week-interval').value = item.week_interval || 1; $('rec-month-interval').value = item.month_interval || 1; $('rec-duration-days').value = item.duration_days || 1; $('rec-day-of-month').value = item.day_of_month || ''; $('rec-interval-days').value = item.interval || 1;
                document.querySelector(`[name="event_kind"][value="${item.event_kind || 'absence'}"]`).checked = true;
                document.querySelectorAll('.rec-day').forEach(day => day.classList.toggle('active', (item.day_of_week || []).map(Number).includes(Number(day.dataset.day))));
                updateKindCards(); updateTypeRows(); updateTimeRows();
                $('rec-form-title').textContent = 'Wiederkehrenden Termin bearbeiten';
                $('rec-form-sub').textContent = 'Änderungen werden per AJAX gespeichert.';
                $('rec-submit').innerHTML = '<i class="feather icon-save"></i> Aktualisieren';
                openModal('rec-form-modal');
            }

            function payloadFromForm() {
                const type = $('rec-type').value;
                return {
                    title: $('rec-title-input').value.trim(),
                    event_kind: document.querySelector('[name="event_kind"]:checked')?.value || 'absence',
                    type,
                    start_date: $('rec-start-date').value,
                    end_date: $('rec-end-date').value || null,
                    all_day: $('rec-all-day').checked ? 1 : 0,
                    start_time: $('rec-all-day').checked ? null : ($('rec-start-time').value || null),
                    end_time: $('rec-all-day').checked ? null : ($('rec-end-time').value || null),
                    description: $('rec-description').value || null,
                    day_of_week: [...document.querySelectorAll('.rec-day.active')].map(day => Number(day.dataset.day)),
                    week_interval: Number($('rec-week-interval').value || 1),
                    month_interval: Number($('rec-month-interval').value || 1),
                    day_of_month: type === 'monthly' ? Number($('rec-day-of-month').value || 1) : null,
                    interval_days: type === 'interval' ? Number($('rec-interval-days').value || 1) : null,
                    duration_days: Number($('rec-duration-days').value || 1),
                    is_active: 1,
                };
            }

            function showValidation(targetId, error) {
                const box = $(targetId);
                const errors = error?.errors ? Object.values(error.errors).flat().join('\n') : (error?.message || 'Fehler beim Speichern.');
                box.textContent = errors; box.classList.add('show');
            }

            $('rec-form').addEventListener('submit', async e => {
                e.preventDefault();
                $('rec-form-error').classList.remove('show');
                const id = $('rec-id').value;
                try {
                    await api(id ? routes.update(id) : routes.store, { method: id ? 'PUT' : 'POST', body: JSON.stringify(payloadFromForm()) });
                    notify('success', id ? 'Termin wurde aktualisiert.' : 'Termin wurde gespeichert.');
                    closeModal('rec-form-modal');
                    await loadList();
                } catch (error) { showValidation('rec-form-error', error); }
            });

            async function deleteItem(id) {
                const ok = !window.Swal ? confirm('Diese Regel wirklich löschen?') : (await Swal.fire({ icon:'warning', title:'Regel löschen?', text:'Alle Ausnahmen dieser Regel werden ebenfalls gelöscht.', showCancelButton:true, confirmButtonText:'Ja, löschen', cancelButtonText:'Abbrechen' })).isConfirmed;
                if (!ok) return;
                try { await api(routes.destroy(id), { method:'DELETE', body:'{}' }); notify('success', 'Regel wurde gelöscht.'); await loadList(); } catch (e) { notify('error', e.message || 'Löschen fehlgeschlagen.'); }
            }

            async function toggleActive(id) {
                const item = items.find(i => String(i.id) === String(id)); if (!item) return;
                try { await api(routes.update(id), { method:'PUT', body: JSON.stringify({ is_active: item.is_active ? 0 : 1 }) }); await loadList(); } catch (e) { notify('error', 'Status konnte nicht geändert werden.'); }
            }

            function updateKindCards() { document.querySelectorAll('.rec-kind').forEach(label => label.classList.toggle('active', label.querySelector('input').checked)); }
            function updateTypeRows() { const type = $('rec-type').value; document.querySelectorAll('.rec-type-row').forEach(row => row.style.display = row.classList.contains('rec-type-' + type) ? '' : 'none'); }
            function updateTimeRows() { $('rec-time-row').style.display = $('rec-all-day').checked ? 'none' : ''; $('rec-override-time-row').style.display = $('rec-override-all-day').checked ? 'none' : ''; }
            document.querySelectorAll('[name="event_kind"]').forEach(r => r.addEventListener('change', updateKindCards));
            document.querySelectorAll('.rec-kind').forEach(label => label.addEventListener('click', () => setTimeout(updateKindCards)));
            document.querySelectorAll('.rec-day').forEach(day => day.addEventListener('click', () => day.classList.toggle('active')));
            $('rec-type').addEventListener('change', updateTypeRows);
            $('rec-all-day').addEventListener('change', updateTimeRows);
            $('rec-override-all-day').addEventListener('change', updateTimeRows);

            async function openOccurrences(id) {
                currentLeaveId = id;
                const item = items.find(i => String(i.id) === String(id));
                $('rec-occ-title').textContent = item ? item.title : 'Termin-Verwaltung';
                const now = new Date(); const future = new Date(); future.setDate(now.getDate() + 90);
                $('rec-occ-from').value = now.toISOString().slice(0,10); $('rec-occ-to').value = future.toISOString().slice(0,10);
                openModal('rec-occ-modal');
                await loadOccurrences();
            }

            async function loadOccurrences() {
                $('rec-occ-body').innerHTML = '<tr><td colspan="6" class="text-center text-muted p-3">Lade Termine...</td></tr>';
                try {
                    const params = new URLSearchParams({ from: $('rec-occ-from').value, to: $('rec-occ-to').value });
                    const json = await api(routes.occurrences(currentLeaveId) + '?' + params.toString());
                    currentOccurrences = json.data || [];
                    renderOccurrences();
                } catch (e) { $('rec-occ-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger p-3">Termine konnten nicht geladen werden.</td></tr>'; }
            }

            function renderOccurrences() {
                const body = $('rec-occ-body');
                if (!currentOccurrences.length) { body.innerHTML = '<tr><td colspan="6" class="text-center text-muted p-3">Keine Termine in diesem Zeitraum.</td></tr>'; return; }
                body.innerHTML = currentOccurrences.map((r, idx) => {
                    const statusClass = r.status === 'cancelled' || r.status === 'skipped' ? 'bad' : (r.status === 'normal' ? 'ok' : 'warn');
                    const rowClass = r.status === 'cancelled' || r.status === 'skipped' ? 'rec-row-cancelled' : '';
                    const moved = r.status === 'moved' ? `<div class="rec-occ-sub">Von ${formatDate(r.original_date)}</div>` : '';
                    const time = r.all_day ? 'Ganztägig' : `${escapeHtml((r.start_time || '').slice(0,5))} – ${escapeHtml((r.end_time || '').slice(0,5))}`;
                    const actions = (r.status === 'cancelled' || r.status === 'skipped')
                        ? `<button type="button" class="rec-btn rec-btn-soft rec-restore-occ" data-date="${r.original_date}"><i class="feather icon-rotate-ccw"></i> Wiederherstellen</button>`
                        : `<button type="button" class="rec-btn-icon rec-edit-occ" data-idx="${idx}" title="Bearbeiten"><i class="feather icon-edit"></i></button><button type="button" class="rec-btn-icon rec-skip-occ" data-date="${r.original_date}" title="Überspringen"><i class="feather icon-slash"></i></button>`;
                    return `<tr class="${rowClass}"><td><div class="rec-occ-date">${formatDate(r.date)}</div>${moved}</td><td>${time}</td><td><strong>${escapeHtml(r.title)}</strong>${r.description ? `<div class="rec-occ-sub">${escapeHtml(r.description)}</div>` : ''}</td><td><span class="rec-pill ${r.event_kind}">${escapeHtml(r.event_kind_label)}</span></td><td><span class="rec-pill ${statusClass}">${escapeHtml(r.status_label)}</span></td><td style="text-align:right; white-space:nowrap;">${actions}</td></tr>`;
                }).join('');
                body.querySelectorAll('.rec-edit-occ').forEach(btn => btn.onclick = () => openOverrideModal(currentOccurrences[Number(btn.dataset.idx)]));
                body.querySelectorAll('.rec-skip-occ').forEach(btn => btn.onclick = () => skipOccurrence(btn.dataset.date));
                body.querySelectorAll('.rec-restore-occ').forEach(btn => btn.onclick = () => restoreOccurrence(btn.dataset.date));
                if (window.feather) feather.replace();
            }

            function openOverrideModal(row) {
                $('rec-override-error').classList.remove('show');
                $('rec-override-original-date').value = row.original_date;
                $('rec-override-new-date').value = row.date;
                $('rec-override-duration').value = row.duration_days || row.duration || 1;
                $('rec-override-all-day').checked = !!row.all_day;
                $('rec-override-start-time').value = row.start_time ? row.start_time.slice(0,5) : '';
                $('rec-override-end-time').value = row.end_time ? row.end_time.slice(0,5) : '';
                $('rec-override-title').value = row.title || '';
                $('rec-override-description').value = row.description || '';
                $('rec-override-sub').textContent = 'Originaldatum: ' + formatDate(row.original_date);
                updateTimeRows();
                openModal('rec-override-modal');
            }

            $('rec-override-form').addEventListener('submit', async e => {
                e.preventDefault();
                const payload = {
                    original_date: $('rec-override-original-date').value,
                    new_date: $('rec-override-new-date').value || null,
                    new_duration_days: Number($('rec-override-duration').value || 1),
                    new_all_day: $('rec-override-all-day').checked ? 1 : 0,
                    new_start_time: $('rec-override-all-day').checked ? null : ($('rec-override-start-time').value || null),
                    new_end_time: $('rec-override-all-day').checked ? null : ($('rec-override-end-time').value || null),
                    new_title: $('rec-override-title').value || null,
                    new_description: $('rec-override-description').value || null,
                    is_cancelled: 0,
                };
                try { await api(routes.overrideUpsert(currentLeaveId), { method:'POST', body: JSON.stringify(payload) }); closeModal('rec-override-modal'); notify('success', 'Ausnahme gespeichert.'); await loadOccurrences(); await loadList(); } catch (error) { showValidation('rec-override-error', error); }
            });

            $('rec-override-cancel-occ').addEventListener('click', async () => {
                try { await api(routes.overrideUpsert(currentLeaveId), { method:'POST', body: JSON.stringify({ original_date: $('rec-override-original-date').value, is_cancelled: 1 }) }); closeModal('rec-override-modal'); notify('success', 'Termin wurde abgesagt.'); await loadOccurrences(); await loadList(); } catch (e) { showValidation('rec-override-error', e); }
            });

            async function skipOccurrence(date) { try { await api(routes.exdateAdd(currentLeaveId), { method:'POST', body: JSON.stringify({ date }) }); notify('success', 'Termin wurde übersprungen.'); await loadOccurrences(); await loadList(); } catch (e) { notify('error', e.message || 'Fehler beim Überspringen.'); } }
            async function restoreOccurrence(date) { try { await api(routes.overrideDelete(currentLeaveId), { method:'DELETE', body: JSON.stringify({ original_date: date }) }); await api(routes.exdateRemove(currentLeaveId), { method:'DELETE', body: JSON.stringify({ date }) }).catch(() => null); notify('success', 'Termin wurde wiederhergestellt.'); await loadOccurrences(); await loadList(); } catch (e) { notify('error', e.message || 'Wiederherstellung fehlgeschlagen.'); } }

            $('rec-open-create').addEventListener('click', openCreate);
            $('rec-refresh-btn').addEventListener('click', loadList);
            $('rec-occ-load').addEventListener('click', loadOccurrences);
            ['rec-search','rec-filter-kind','rec-filter-status'].forEach(id => $(id).addEventListener('input', renderList));
            $('rec-reset-filter').addEventListener('click', () => { $('rec-search').value = ''; $('rec-filter-kind').value = ''; $('rec-filter-status').value = ''; renderList(); });

            loadList();
        })();
    </script>
@endpush
 