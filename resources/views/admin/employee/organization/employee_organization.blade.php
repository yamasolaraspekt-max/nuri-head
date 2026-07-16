@extends('admin.layouts.app')
@section('title', 'Organisation & Positionen')

@section('style')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    :root{
      --app-bg:#f3f4f6;--card-bg:#fff;--text-main:#1f2937;--text-muted:#6b7280;--border:#e5e7eb;
      --primary:var(--sa-accent);--primary-hover:var(--sa-accent-hover);--primary-light:var(--sa-accent-light);--blue:var(--sa-accent);--blue-light:#eff6ff;
      --success:#10b981;--success-light:#ecfdf5;--warning:#f59e0b;--warning-light:#fffbeb;--danger:#ef4444;--danger-light:#fef2f2;
      --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);--shadow:0 10px 25px -10px rgb(0 0 0 / .25),0 4px 8px -4px rgb(0 0 0 / .12);
      --radius:14px;--transition:all .2s ease-in-out;
    }
    body{background:var(--app-bg)!important;}
    .oc-wrap{font-family:Inter,system-ui,-apple-system,sans-serif;color:var(--text-main);}
    .oc-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
    .oc-title{font-size:26px;font-weight:900;letter-spacing:-.025em;color:#111827;text-transform:uppercase;}
    .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px;}
    .oc-breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:10px;font-size:13px;color:var(--text-muted);}
    .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:800}.oc-breadcrumb a:hover{color:#111827}.oc-breadcrumb .current{color:#111827;font-weight:900}
    .oc-inline-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
    .oc-btn{background:var(--primary);color:#fff;border:none;padding:10px 16px;border-radius:10px;font-weight:900;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;gap:8px;text-decoration:none;}
    .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none}.oc-btn:disabled{opacity:.55;cursor:not-allowed;}
    .oc-btn-soft{background:#fff;color:var(--text-main);border:1px solid var(--border);padding:10px 14px;border-radius:10px;font-weight:850;cursor:pointer;transition:var(--transition);text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
    .oc-btn-soft:hover{background:#f9fafb;color:#111827;text-decoration:none}.oc-btn-soft:disabled{opacity:.55;cursor:not-allowed;}
    .oc-btn-ic{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;transition:var(--transition);text-decoration:none;font-weight:900;}
    .oc-btn-ic:hover{background:#f9fafb;color:#111827;border-color:#d1d5db;text-decoration:none}.oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}.oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
    .oc-analytics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}@media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:700px){.oc-analytics{grid-template-columns:1fr}}
    .oc-stat{background:#fff;border:1px solid var(--border);border-radius:16px;padding:16px;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:12px;min-height:92px}.oc-stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}.oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}.oc-stat-icon.published{background:var(--success-light);color:var(--success)}.oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}.oc-stat-icon.type{background:#f3f4f6;color:#6b7280}.oc-stat-label{font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em}.oc-stat-value{font-size:24px;font-weight:950;color:#111827;line-height:1.1;margin-top:4px}.oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px}
    .oc-toolbar{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;justify-content:space-between;margin-bottom:16px;box-shadow:var(--shadow-sm)}.oc-toolbar-left,.oc-toolbar-right{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap}.oc-toolbar-left{flex:1}.oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:170px}.oc-filter-block.search{flex:1;min-width:280px}.oc-filter-label{font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em}
    .oc-input{background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:10px 12px 10px 36px;font-size:14px;outline:none;transition:var(--transition);min-width:240px;width:100%;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center;background-size:16px}.oc-input:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
    .oc-input-form,.oc-select{width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:#fff;font-size:14px;outline:none;transition:var(--transition)}.oc-input-form:focus,.oc-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
    .oc-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow-sm);overflow:hidden}
    .org-grid{display:grid;grid-template-columns:380px minmax(0,1fr);gap:16px;align-items:start}@media(max-width:1280px){.org-grid{grid-template-columns:1fr}}
    .org-panel-head{padding:16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;background:#fff;position:sticky;top:0;z-index:3}.org-panel-title{font-size:15px;font-weight:950;color:#111827;display:flex;align-items:center;gap:8px}.org-panel-body{padding:14px;background:#fafafa;max-height:calc(100vh - 230px);overflow:auto}@media(max-width:1280px){.org-panel-body{max-height:none}}
    .employee-list{display:flex;flex-direction:column;gap:9px}.employee-card{background:#fff;border:1px solid var(--border);border-radius:14px;padding:10px;display:grid;grid-template-columns:22px 44px minmax(0,1fr);gap:10px;align-items:center;cursor:grab;transition:var(--transition)}.employee-card:hover,.employee-card.selected{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}.employee-card.sortable-ghost{opacity:.45}.employee-check{width:18px;height:18px;accent-color:var(--primary)}
    .emp-avatar,.assign-avatar{width:42px;height:42px;border-radius:12px;object-fit:cover;background:var(--primary-light);color:#6c970f;display:flex;align-items:center;justify-content:center;font-weight:950;border:1px solid var(--border);flex:0 0 auto}.assign-avatar{width:34px;height:34px;border-radius:10px}.emp-name{font-weight:900;color:#111827;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.emp-meta{font-size:12px;color:var(--text-muted);margin-top:2px}.selected-bar{background:#fff;border:1px solid var(--border);border-radius:14px;padding:12px;margin:12px 0;}.selected-title{font-weight:950;font-size:13px;color:#111827;margin-bottom:8px}.selected-tags{display:flex;flex-wrap:wrap;gap:6px}.selected-tag{display:inline-flex;align-items:center;gap:6px;background:var(--primary-light);color:#5f850e;border:1px solid rgba(147,194,28,.25);border-radius:999px;padding:5px 8px;font-size:12px;font-weight:900}.selected-tag button{border:0;background:transparent;color:inherit;font-weight:950;cursor:pointer;padding:0}
    .bulk-box{margin-top:14px;padding:12px;border:1px solid var(--border);border-radius:14px;background:#fff}.oc-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}@media(max-width:700px){.oc-form-grid{grid-template-columns:1fr}}.oc-form-group{margin-bottom:12px}.oc-label{display:block;font-size:13px;font-weight:850;color:var(--text-main);margin-bottom:6px}.oc-help{font-size:12px;color:var(--text-muted);margin-top:6px}.check-line{display:flex;align-items:center;gap:8px;font-weight:850;color:#111827;margin:4px 0 12px}.check-line input{accent-color:var(--primary)}
    .department-list{display:flex;flex-direction:column;gap:12px}.department-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow-sm);overflow:hidden;transition:var(--transition)}.department-card:hover{border-color:var(--primary);box-shadow:var(--shadow)}.department-head{padding:14px 16px;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:center;cursor:pointer}.dept-main{display:flex;gap:12px;align-items:center;min-width:0}.dept-icon{width:42px;height:42px;border-radius:14px;background:var(--blue-light);color:var(--blue);display:flex;align-items:center;justify-content:center;flex:0 0 auto}.dept-name{font-size:16px;font-weight:950;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.dept-sub{font-size:12px;color:var(--text-muted);margin-top:2px}.dept-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end}.oc-status-pill{display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:950;white-space:nowrap}.oc-status-pill.green{background:#ecfdf5;color:#047857}.oc-status-pill.blue{background:var(--blue-light);color:#2563eb}.oc-status-pill.orange{background:#fffbeb;color:#b45309}.department-body{display:none;border-top:1px solid var(--border);background:#fafafa;padding:14px}.department-card.open .department-body{display:block}.department-card.open .dept-chevron{transform:rotate(180deg)}.dept-chevron{transition:var(--transition)}
    .position-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}@media(max-width:1450px){.position-grid{grid-template-columns:1fr}}.position-card{background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden}.position-head{padding:12px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.position-title{font-weight:950;color:#111827;font-size:14px}.position-desc{font-size:12px;color:var(--text-muted);margin-top:2px}.position-actions{display:flex;gap:6px;align-items:center;flex-shrink:0}.position-dropzone{min-height:104px;padding:10px;display:flex;flex-wrap:wrap;align-items:flex-start;gap:8px;background:linear-gradient(135deg,#fff,#fafafa);transition:var(--transition)}.position-dropzone.is-hover{background:var(--primary-light);box-shadow:inset 0 0 0 2px var(--primary)}.drop-placeholder{width:100%;border:1px dashed #cbd5e1;background:#f8fafc;color:#64748b;border-radius:12px;padding:14px;text-align:center;font-size:12px;font-weight:850}
    .assignment-chip{display:flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:14px;background:#fff;padding:8px;max-width:100%;box-shadow:var(--shadow-sm)}.assignment-chip.main{border-color:var(--primary);background:var(--primary-light)}.assign-info{min-width:0}.assign-name{font-weight:950;font-size:13px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px}.assign-meta{font-size:11px;color:var(--text-muted);display:flex;gap:5px;flex-wrap:wrap;margin-top:2px}.mini-pill{background:#f3f4f6;border:1px solid var(--border);border-radius:999px;padding:2px 6px;font-weight:850}.assign-actions{display:flex;align-items:center;gap:4px;margin-left:auto}.mini-btn{width:26px;height:26px;border-radius:8px;border:1px solid var(--border);background:#fff;color:var(--text-muted);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-weight:950}.mini-btn:hover{color:#111827;border-color:#d1d5db}.mini-btn.danger:hover{color:var(--danger);background:var(--danger-light)}
    .org-empty{text-align:center;padding:36px;color:var(--text-muted);background:#fff;border:1px dashed var(--border);border-radius:16px}.org-loading{padding:60px;text-align:center;color:var(--text-muted);font-weight:900}.modal-employee-picker{max-height:210px;overflow:auto;border:1px solid var(--border);border-radius:12px;padding:8px;background:#fafafa;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}@media(max-width:760px){.modal-employee-picker{grid-template-columns:1fr}}.modal-emp-option{background:#fff;border:1px solid var(--border);border-radius:12px;padding:8px;display:flex;align-items:center;gap:8px;cursor:pointer}.modal-emp-option input{accent-color:var(--primary)}.modal-emp-option.is-selected{border-color:var(--primary);background:var(--primary-light)}
    .oc-modal-backdrop{position:fixed;inset:0;z-index:1200;background:rgba(17,24,39,.55);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .22s ease;display:flex;align-items:center;justify-content:center;padding:18px}.oc-modal-backdrop.open{opacity:1;pointer-events:auto}.oc-modal{width:100%;max-width:720px;background:#fff;border:1px solid rgba(229,231,235,.9);border-radius:16px;box-shadow:var(--shadow);transform:translateY(12px) scale(.985);transition:transform .22s ease;overflow:hidden}.oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}.oc-modal-h{display:flex;gap:12px;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa}.oc-modal-ttl{font-weight:950;font-size:16px;line-height:1.2;margin:0;color:#111827}.oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto}.oc-modal-f{padding:14px 18px;border-top:1px solid var(--border);background:#fafafa;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap}
    .oc-toast-wrap{position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}.oc-toast{pointer-events:auto;min-width:280px;max-width:360px;background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);padding:12px;display:flex;gap:10px;align-items:flex-start;animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards}@keyframes ocToastIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}.oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}.oc-toast-ic.ok{background:var(--success-light);color:var(--success)}.oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}.oc-toast-ttl{font-weight:950;font-size:13px;margin:0;color:#111827}.oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}.oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted)}
  </style>
@endsection

@section('content')
  <div class="oc-wrap" id="employeeOrgApp"
       data-data-url="{{ route('employee.organization.data') }}"
       data-assign-url="{{ route('employee.organization.assign') }}"
       data-assign-multiple-url="{{ route('employee.organization.assign-multiple') }}"
       data-bulk-url="{{ route('employee.organization.bulk-assign') }}"
       data-update-base="{{ url('/employee-organization/update') }}"
       data-remove-base="{{ url('/employee-organization/remove') }}"
       data-csrf="{{ csrf_token() }}">

    <div class="oc-titlebar">
      <div>
        <div class="oc-title">Organisation & Positionen</div>
        <div class="oc-sub">Alle aktiven Mitarbeiter in alle Abteilungen und Positionen per Drag & Drop zuordnen.</div>
        <div class="oc-breadcrumb"><a href="{{ url('/employee_dashboard') }}">Home</a><span>›</span><span class="current">Organisation</span></div>
      </div>
      <div class="oc-inline-actions">
        <button type="button" class="oc-btn-soft" id="btnExpandAll">Alle öffnen</button>
        <button type="button" class="oc-btn-soft" id="btnCollapseAll">Alle schließen</button>
        <button type="button" class="oc-btn" id="btnReloadOrg">Aktualisieren</button>
      </div>
    </div>

    <div class="oc-analytics">
      <div class="oc-stat"><div class="oc-stat-icon total"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div><div class="oc-stat-label">Mitarbeiter</div><div class="oc-stat-value" id="statEmployees">0</div><div class="oc-stat-sub">Aktive Personen</div></div></div>
      <div class="oc-stat"><div class="oc-stat-icon published"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/></svg></div><div><div class="oc-stat-label">Abteilungen</div><div class="oc-stat-value" id="statDepartments">0</div><div class="oc-stat-sub">Bereiche</div></div></div>
      <div class="oc-stat"><div class="oc-stat-icon unpublished"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M7 12h10M10 17h4"/></svg></div><div><div class="oc-stat-label">Positionen</div><div class="oc-stat-value" id="statPositions">0</div><div class="oc-stat-sub">Aus positions Tabelle</div></div></div>
      <div class="oc-stat"><div class="oc-stat-icon type"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div><div><div class="oc-stat-label">Zuordnungen</div><div class="oc-stat-value" id="statAssignments">0</div><div class="oc-stat-sub">department_positions</div></div></div>
    </div>

    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block search"><label class="oc-filter-label">Suche</label><input type="text" id="orgSearch" class="oc-input" placeholder="Mitarbeiter, Abteilung oder Position suchen"></div>
        <div class="oc-filter-block"><label class="oc-filter-label">Abteilung</label><select id="departmentFilter" class="oc-select"><option value="">Alle Abteilungen</option></select></div>
        <div class="oc-filter-block"><label class="oc-filter-label">Position</label><select id="positionFilter" class="oc-select"><option value="">Alle Positionen</option></select></div>
        <div class="oc-filter-block"><label class="oc-filter-label">Sortierung</label><select id="sortMode" class="oc-select"><option value="department_asc">Abteilung A-Z</option><option value="department_desc">Abteilung Z-A</option><option value="most_assigned">Meiste Zuordnungen</option><option value="least_assigned">Wenigste Zuordnungen</option></select></div>
      </div>
      <div class="oc-toolbar-right"><button type="button" class="oc-btn-soft" id="btnResetFilters">Zurücksetzen</button></div>
    </div>

    <div class="org-grid">
      <aside class="oc-card">
        <div class="org-panel-head"><div class="org-panel-title">Aktive Mitarbeiter</div><span class="oc-status-pill blue" id="employeeVisibleCount">0</span></div>
        <div class="org-panel-body">
          <input type="text" id="employeeSearch" class="oc-input" placeholder="Mitarbeiter suchen" style="margin-bottom:12px;">
          <div class="oc-inline-actions" style="margin-bottom:10px;">
            <button type="button" class="oc-btn-soft" id="btnSelectAllVisible">Sichtbare wählen</button>
            <button type="button" class="oc-btn-soft" id="btnClearSelected">Auswahl löschen</button>
          </div>
          <div id="employeeList" class="employee-list"><div class="org-loading">Lädt Mitarbeiter…</div></div>
          <div class="selected-bar">
            <div class="selected-title">Ausgewählte Mitarbeiter</div>
            <div id="selectedEmployeeTags" class="selected-tags"><span class="oc-help">Keine Auswahl.</span></div>
          </div>
          <div class="bulk-box">
            <div class="oc-form-group">
              <label class="oc-label">Bulk-Modus</label>
              <select id="bulkMode" class="oc-select">
                <option value="all_departments_same_position">Auswahl in alle sichtbaren Abteilungen mit einer Position</option>
                <option value="all_department_positions">Auswahl in alle sichtbaren Abteilung-Positionen</option>
              </select>
            </div>
            <div class="oc-form-group" id="bulkPositionGroup"><label class="oc-label">Position</label><select id="bulkPosition" class="oc-select"><option value="">Position wählen</option></select></div>
            <div class="oc-form-grid">
              <div class="oc-form-group"><label class="oc-label">Gesamt %</label><input type="number" id="defaultPercent" class="oc-input-form" value="100" min="0" max="100"></div>
              <div class="oc-form-group"><label class="oc-label">Stunden</label><input type="number" id="defaultHours" class="oc-input-form" value="" min="0" step="0.5" placeholder="optional"></div>
              <div class="oc-form-group"><label class="oc-label">Montage %</label><input type="number" id="defaultMontage" class="oc-input-form" value="0" min="0" max="100"></div>
              <div class="oc-form-group"><label class="oc-label">Büro %</label><input type="number" id="defaultOffice" class="oc-input-form" value="100" min="0" max="100"></div>
            </div>
            <label class="check-line"><input type="checkbox" id="defaultMain"> Hauptposition</label>
            <button type="button" class="oc-btn" id="btnBulkAssign" style="width:100%;justify-content:center;" disabled>Auswahl anwenden</button>
            <div class="oc-help">Mehrere Mitarbeiter auswählen und direkt in eine Position ziehen. Beim Ablegen öffnet sich automatisch die Prozent-Abfrage.</div>
          </div>
        </div>
      </aside>

      <section class="oc-card">
        <div class="org-panel-head"><div class="org-panel-title">Organisation</div><span class="oc-status-pill green" id="departmentVisibleCount">0 Abteilungen</span></div>
        <div class="org-panel-body"><div id="departmentList" class="department-list"><div class="org-loading">Lädt Organisation…</div></div></div>
      </section>
    </div>

    <div class="oc-modal-backdrop" id="assignmentModal">
      <div class="oc-modal">
        <div class="oc-modal-h"><h3 class="oc-modal-ttl" id="assignmentModalTitle">Mitarbeiter zuordnen</h3><button type="button" class="oc-btn-ic" data-close-modal>×</button></div>
        <form id="assignmentForm">
          <div class="oc-modal-b">
            <input type="hidden" id="modalAssignmentId"><input type="hidden" id="modalDepartmentId"><input type="hidden" id="modalPositionId">
            <div class="oc-form-group"><label class="oc-label">Abteilung / Position</label><div class="selected-bar" id="modalContextText">—</div></div>
            <div class="oc-form-group" id="modalEmployeePickerWrap"><label class="oc-label">Mitarbeiter auswählen</label><input type="text" id="modalEmployeeSearch" class="oc-input" placeholder="Mitarbeiter suchen" style="margin-bottom:8px;"><div id="modalEmployeePicker" class="modal-employee-picker"></div></div>
            <div class="oc-form-grid">
              <div class="oc-form-group"><label class="oc-label">Gesamtanteil %</label><input type="number" id="modalPercent" class="oc-input-form" min="0" max="100" value="100"></div>
              <div class="oc-form-group"><label class="oc-label">Arbeitsstunden</label><input type="number" id="modalHours" class="oc-input-form" min="0" step="0.5" placeholder="optional"></div>
              <div class="oc-form-group"><label class="oc-label">Montage %</label><input type="number" id="modalMontage" class="oc-input-form" min="0" max="100" value="0"></div>
              <div class="oc-form-group"><label class="oc-label">Büro %</label><input type="number" id="modalOffice" class="oc-input-form" min="0" max="100" value="100"></div>
            </div>
            <label class="check-line"><input type="checkbox" id="modalMain"> Als Hauptposition setzen</label>
            <div class="oc-help">Wenn Hauptposition aktiv ist, werden andere Hauptpositionen dieser Mitarbeiter automatisch entfernt.</div>
          </div>
          <div class="oc-modal-f"><button type="button" class="oc-btn-soft" data-close-modal>Abbrechen</button><button type="submit" class="oc-btn" id="btnSaveAssignment">Speichern</button></div>
        </form>
      </div>
    </div>

    <div id="toastWrap" class="oc-toast-wrap"></div>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script>
  $(function(){
    const $app = $('#employeeOrgApp');
    const urls = {
      data: $app.data('data-url'),
      assign: $app.data('assign-url'),
      assignMultiple: $app.data('assign-multiple-url'),
      bulk: $app.data('bulk-url'),
      updateBase: $app.data('update-base'),
      removeBase: $app.data('remove-base'),
      csrf: $app.data('csrf')
    };

    const state = {
      employees: [], filteredEmployees: [], positions: [], departments: [], filteredDepartments: [],
      selectedEmployeeIds: new Set(), sortables: [], modalMode: 'create', modalFixedEmployeeIds: null
    };

    bindEvents();
    loadData();

    function bindEvents(){
      $('#btnReloadOrg').on('click', loadData);
      $('#btnExpandAll').on('click', () => $('.department-card').addClass('open'));
      $('#btnCollapseAll').on('click', () => $('.department-card').removeClass('open'));
      $('#btnResetFilters').on('click', resetFilters);
      $('#orgSearch,#departmentFilter,#positionFilter,#sortMode').on('input change', applyFiltersAndRender);
      $('#employeeSearch').on('input', renderEmployees);
      $('#btnSelectAllVisible').on('click', selectAllVisibleEmployees);
      $('#btnClearSelected').on('click', clearSelectedEmployees);
      $('#bulkMode').on('change', function(){ $('#bulkPositionGroup').toggle($(this).val()==='all_departments_same_position'); });
      $('#btnBulkAssign').on('click', bulkAssignSelectedEmployees);
      $('#assignmentForm').on('submit', saveAssignmentFromModal);
      $('#modalEmployeeSearch').on('input', renderModalEmployeePicker);
      $(document).on('click','[data-close-modal]', closeAssignmentModal);
      $(document).on('click','.department-head', function(e){ if($(e.target).closest('button').length) return; $(this).closest('.department-card').toggleClass('open'); });
      $(document).on('change','.employee-check', function(e){ e.stopPropagation(); toggleEmployeeSelection(Number($(this).data('employee-id')), $(this).is(':checked')); });
      $(document).on('click','.employee-card', function(e){ if($(e.target).is('input')) return; const id=Number($(this).data('employee-id')); toggleEmployeeSelection(id, !state.selectedEmployeeIds.has(id)); });
      $(document).on('click','.selected-tag button', function(){ toggleEmployeeSelection(Number($(this).data('employee-id')), false); });
      $(document).on('click','.btnAddToPosition', function(){
        openAssignmentModal({
          departmentId: $(this).data('department-id'), departmentName: $(this).data('department-name'),
          positionId: $(this).data('position-id'), positionName: $(this).data('position-name'),
          employeeIds: Array.from(state.selectedEmployeeIds)
        });
      });
      $(document).on('click','.btnEditAssignment', function(){ const a=findAssignment($(this).data('assignment-id')); if(a) openAssignmentModal({assignment:a}); });
      $(document).on('click','.btnRemoveAssignment', function(){ removeAssignment($(this).data('assignment-id')); });
    }

    function loadData(){
      $('#departmentList').html('<div class="org-loading">Lädt Organisation…</div>');
      $('#employeeList').html('<div class="org-loading">Lädt Mitarbeiter…</div>');
      $.get(urls.data).done(function(resp){
        state.employees = resp.employees || [];
        state.positions = resp.positions || [];
        state.departments = resp.departments || [];
        state.filteredDepartments = state.departments;
        updateStats(resp.stats || {});
        buildFilters();
        renderEmployees();
        renderSelectedEmployees();
        applyFiltersAndRender();
      }).fail(function(xhr){
        toast('Fehler', xhr.responseJSON?.message || 'Organisation konnte nicht geladen werden.', 'bad');
      });
    }

    function updateStats(stats){
      $('#statEmployees').text(stats.employees || 0); $('#statDepartments').text(stats.departments || 0); $('#statPositions').text(stats.positions || 0); $('#statAssignments').text(stats.assignments || 0);
    }

    function buildFilters(){
      fillSelect('#departmentFilter', state.departments.map(d=>({id:d.id,name:d.name})), 'Alle Abteilungen');
      fillSelect('#positionFilter', state.positions.map(p=>({id:p.id,name:p.name})), 'Alle Positionen');
      fillSelect('#bulkPosition', state.positions.map(p=>({id:p.id,name:p.name})), 'Position wählen');
    }
    function fillSelect(selector, items, placeholder){
      const $s=$(selector); const current=$s.val(); $s.empty().append(`<option value="">${escapeHtml(placeholder)}</option>`);
      (items||[]).forEach(i=>$s.append(`<option value="${i.id}">${escapeHtml(i.name)}</option>`));
      if(current) $s.val(current);
    }

    function renderEmployees(){
      const q=($('#employeeSearch').val()||'').toLowerCase();
      state.filteredEmployees = state.employees.filter(emp => !q || String(emp.display_name||'').toLowerCase().includes(q));
      $('#employeeVisibleCount').text(state.filteredEmployees.length);
      if(!state.filteredEmployees.length){ $('#employeeList').html('<div class="org-empty">Keine Mitarbeiter gefunden.</div>'); return; }
      $('#employeeList').html(state.filteredEmployees.map(emp=>employeeCardHtml(emp)).join(''));
      initEmployeeSortable();
    }

    function employeeCardHtml(emp){
      const selected = state.selectedEmployeeIds.has(Number(emp.id));
      const main = emp.main_assignment ? `${emp.main_assignment.department || '-'} / ${emp.main_assignment.position || '-'}` : 'Keine Hauptposition';
      return `<div class="employee-card ${selected?'selected':''}" data-employee-id="${emp.id}">
        <input class="employee-check" type="checkbox" data-employee-id="${emp.id}" ${selected?'checked':''}>
        ${avatarHtml(emp.image, emp.display_name, 'emp-avatar')}
        <div><div class="emp-name">${escapeHtml(emp.display_name)}</div><div class="emp-meta">${emp.assignment_count || 0} Zuordnungen • ${escapeHtml(main)}</div></div>
      </div>`;
    }

    function toggleEmployeeSelection(id, selected){
      if(selected) state.selectedEmployeeIds.add(id); else state.selectedEmployeeIds.delete(id);
      renderEmployees(); renderSelectedEmployees();
    }
    function selectAllVisibleEmployees(){ state.filteredEmployees.forEach(e=>state.selectedEmployeeIds.add(Number(e.id))); renderEmployees(); renderSelectedEmployees(); }
    function clearSelectedEmployees(){ state.selectedEmployeeIds.clear(); renderEmployees(); renderSelectedEmployees(); }
    function renderSelectedEmployees(){
      const ids=Array.from(state.selectedEmployeeIds); $('#btnBulkAssign').prop('disabled', !ids.length);
      if(!ids.length){ $('#selectedEmployeeTags').html('<span class="oc-help">Keine Auswahl.</span>'); return; }
      const html=ids.map(id=>{ const emp=state.employees.find(e=>Number(e.id)===Number(id)); if(!emp) return ''; return `<span class="selected-tag">${escapeHtml(emp.display_name)} <button type="button" data-employee-id="${id}">×</button></span>`; }).join('');
      $('#selectedEmployeeTags').html(html);
    }

    function applyFiltersAndRender(){
      const q=($('#orgSearch').val()||'').toLowerCase(); const deptFilter=$('#departmentFilter').val(); const posFilter=$('#positionFilter').val(); const sort=$('#sortMode').val();
      let list = state.departments.map(dept => {
        let positions = (dept.positions || []).filter(pos => {
          const posMatch = !posFilter || String(pos.id) === String(posFilter);
          const text = `${dept.name} ${pos.name} ${(pos.assignments||[]).map(a=>a.employee_name).join(' ')}`.toLowerCase();
          const searchMatch = !q || text.includes(q);
          return posMatch && searchMatch;
        });
        return Object.assign({}, dept, { positions });
      }).filter(dept => {
        if(deptFilter && String(dept.id)!==String(deptFilter)) return false;
        const deptText = `${dept.name}`.toLowerCase();
        return dept.positions.length || (!posFilter && q && deptText.includes(q));
      });
      list.sort((a,b)=>{
        if(sort==='department_desc') return String(b.name||'').localeCompare(String(a.name||''));
        if(sort==='most_assigned') return Number(b.assignment_count||0)-Number(a.assignment_count||0);
        if(sort==='least_assigned') return Number(a.assignment_count||0)-Number(b.assignment_count||0);
        return String(a.name||'').localeCompare(String(b.name||''));
      });
      state.filteredDepartments = list;
      renderDepartments();
    }

    function renderDepartments(){
      $('#departmentVisibleCount').text(`${state.filteredDepartments.length} Abteilungen`);
      if(!state.filteredDepartments.length){ $('#departmentList').html('<div class="org-empty">Keine Abteilungen oder Positionen gefunden.</div>'); return; }
      $('#departmentList').html(state.filteredDepartments.map(dept=>departmentCardHtml(dept)).join(''));
      initDropzones();
    }

    function departmentCardHtml(dept){
      const positionsHtml = (dept.positions||[]).map(pos=>positionCardHtml(dept,pos)).join('') || '<div class="org-empty">Keine Positionen gefunden.</div>';
      return `<div class="department-card open" data-department-id="${dept.id}">
        <div class="department-head"><div class="dept-main"><div class="dept-icon">⌂</div><div><div class="dept-name">${escapeHtml(dept.name)}</div><div class="dept-sub">${dept.positions.length} Positionen sichtbar • ${dept.assignment_count||0} vorhandene Zuordnungen</div></div></div><div class="dept-actions"><span class="oc-status-pill blue">${dept.assignment_count||0} Mitarbeiter</span><span class="dept-chevron">⌄</span></div></div>
        <div class="department-body"><div class="position-grid">${positionsHtml}</div></div>
      </div>`;
    }

    function positionCardHtml(dept,pos){
      const assignments = pos.assignments || [];
      const chips = assignments.length ? assignments.map(a=>assignmentChipHtml(a)).join('') : '<div class="drop-placeholder">Mitarbeiter hier ablegen oder auf Hinzufügen klicken</div>';
      return `<div class="position-card" data-position-id="${pos.id}">
        <div class="position-head"><div><div class="position-title">${escapeHtml(pos.name)}</div><div class="position-desc">${escapeHtml(pos.description || 'Position aus positions Tabelle')} • ${assignments.length} Mitarbeiter</div></div><div class="position-actions"><button type="button" class="oc-btn-ic primary btnAddToPosition" title="Mitarbeiter hinzufügen" data-department-id="${dept.id}" data-department-name="${escapeAttr(dept.name)}" data-position-id="${pos.id}" data-position-name="${escapeAttr(pos.name)}">+</button></div></div>
        <div class="position-dropzone" data-department-id="${dept.id}" data-department-name="${escapeAttr(dept.name)}" data-position-id="${pos.id}" data-position-name="${escapeAttr(pos.name)}">${chips}</div>
      </div>`;
    }

    function assignmentChipHtml(a){
      const hours = a.working_hours ? `<span class="mini-pill">${escapeHtml(a.working_hours)}h</span>` : '';
      return `<div class="assignment-chip ${a.main==='Yes'?'main':''}">
        ${avatarHtml(a.employee_image, a.employee_name, 'assign-avatar')}
        <div class="assign-info"><div class="assign-name">${escapeHtml(a.employee_name)} ${a.main==='Yes'?'★':''}</div><div class="assign-meta"><span class="mini-pill">${a.percent||0}%</span><span class="mini-pill">M ${a.montage_percent||0}%</span><span class="mini-pill">B ${a.office_percent||0}%</span>${hours}</div></div>
        <div class="assign-actions"><button type="button" class="mini-btn btnEditAssignment" data-assignment-id="${a.id}" title="Bearbeiten">✎</button><button type="button" class="mini-btn danger btnRemoveAssignment" data-assignment-id="${a.id}" title="Entfernen">×</button></div>
      </div>`;
    }

    function initEmployeeSortable(){
      const el=document.getElementById('employeeList'); if(!el || !window.Sortable) return;
      if(el._sortable) el._sortable.destroy();
      el._sortable = new Sortable(el,{group:{name:'employees',pull:'clone',put:false},sort:false,animation:150,fallbackOnBody:true,swapThreshold:.65});
    }
    function initDropzones(){
      if(!window.Sortable) return; state.sortables.forEach(s=>s.destroy&&s.destroy()); state.sortables=[];
      document.querySelectorAll('.position-dropzone').forEach(zone=>{
        const sortable = new Sortable(zone,{group:{name:'employees',pull:false,put:true},sort:false,animation:150,
          onAdd:function(evt){
            const $item=$(evt.item); const draggedId=Number($item.data('employee-id')); const $zone=$(evt.to); $item.remove();
            let ids = state.selectedEmployeeIds.has(draggedId) ? Array.from(state.selectedEmployeeIds) : [draggedId];
            ids = ids.filter(Boolean);
            openAssignmentModal({employeeIds: ids, departmentId:$zone.data('department-id'), departmentName:$zone.data('department-name'), positionId:$zone.data('position-id'), positionName:$zone.data('position-name')});
          },
          onMove:function(){ $(zone).addClass('is-hover'); return true; },
          onEnd:function(){ $(zone).removeClass('is-hover'); },
          onUnchoose:function(){ $(zone).removeClass('is-hover'); }
        });
        state.sortables.push(sortable);
      });
    }

    function openAssignmentModal(config){
      const assignment = config.assignment || null;
      state.modalMode = assignment ? 'edit' : 'create';
      state.modalFixedEmployeeIds = assignment ? [Number(assignment.employee_id)] : (config.employeeIds || []);
      const departmentId = assignment ? assignment.department_id : config.departmentId;
      const positionId = assignment ? assignment.position_id : config.positionId;
      const departmentName = assignment ? assignment.department_name : config.departmentName;
      const positionName = assignment ? assignment.position_name : config.positionName;
      $('#assignmentModalTitle').text(assignment ? 'Zuordnung bearbeiten' : 'Mitarbeiter zuordnen');
      $('#modalAssignmentId').val(assignment ? assignment.id : ''); $('#modalDepartmentId').val(departmentId); $('#modalPositionId').val(positionId);
      $('#modalPercent').val(assignment ? assignment.percent : ($('#defaultPercent').val() || 100));
      $('#modalMontage').val(assignment ? assignment.montage_percent : ($('#defaultMontage').val() || 0));
      $('#modalOffice').val(assignment ? assignment.office_percent : ($('#defaultOffice').val() || 100));
      $('#modalHours').val(assignment ? (assignment.working_hours || '') : ($('#defaultHours').val() || ''));
      $('#modalMain').prop('checked', assignment ? assignment.main === 'Yes' : $('#defaultMain').is(':checked'));
      $('#modalContextText').html(`<strong>${escapeHtml(departmentName)}</strong> → <strong>${escapeHtml(positionName)}</strong>`);
      $('#modalEmployeeSearch').val('');
      $('#modalEmployeePickerWrap').toggle(!assignment);
      renderModalEmployeePicker();
      $('#assignmentModal').addClass('open');
    }
    function renderModalEmployeePicker(){
      const q=($('#modalEmployeeSearch').val()||'').toLowerCase(); const fixed = new Set((state.modalFixedEmployeeIds || []).map(Number));
      const list = state.employees.filter(e=>!q || String(e.display_name||'').toLowerCase().includes(q));
      if(!list.length){ $('#modalEmployeePicker').html('<div class="org-empty" style="grid-column:1/-1;padding:18px;">Keine Mitarbeiter gefunden.</div>'); return; }
      $('#modalEmployeePicker').html(list.map(emp=>{
        const checked=fixed.has(Number(emp.id));
        return `<label class="modal-emp-option ${checked?'is-selected':''}"><input type="checkbox" class="modalEmpCheck" value="${emp.id}" ${checked?'checked':''}>${avatarHtml(emp.image, emp.display_name, 'assign-avatar')}<span>${escapeHtml(emp.display_name)}</span></label>`;
      }).join(''));
      $('.modalEmpCheck').off('change').on('change', function(){
        const id=Number($(this).val()); if(!state.modalFixedEmployeeIds) state.modalFixedEmployeeIds=[];
        if($(this).is(':checked')){ if(!state.modalFixedEmployeeIds.includes(id)) state.modalFixedEmployeeIds.push(id); }
        else{ state.modalFixedEmployeeIds = state.modalFixedEmployeeIds.filter(x=>Number(x)!==id); }
        renderModalEmployeePicker();
      });
    }
    function closeAssignmentModal(){ $('#assignmentModal').removeClass('open'); $('#assignmentForm')[0].reset(); state.modalFixedEmployeeIds=null; }

    function saveAssignmentFromModal(e){
      e.preventDefault();
      const assignmentId=$('#modalAssignmentId').val();
      const payload={department_id:$('#modalDepartmentId').val(),position_id:$('#modalPositionId').val(),percent:$('#modalPercent').val(),montage_percent:$('#modalMontage').val(),office_percent:$('#modalOffice').val(),working_hours:$('#modalHours').val(),main:$('#modalMain').is(':checked')?'Yes':'No',_token:urls.csrf};
      let ajaxConfig;
      if(assignmentId){ ajaxConfig={url:`${urls.updateBase}/${assignmentId}`,method:'POST',data:payload}; }
      else{
        const employeeIds=(state.modalFixedEmployeeIds||[]).filter(Boolean);
        if(!employeeIds.length){ toast('Mitarbeiter fehlt','Bitte wählen Sie mindestens einen Mitarbeiter.','bad'); return; }
        payload.employee_ids=employeeIds; ajaxConfig={url:urls.assignMultiple,method:'POST',data:payload};
      }
      $('#btnSaveAssignment').prop('disabled',true).text('Speichern…');
      $.ajax(ajaxConfig).done(function(resp){ closeAssignmentModal(); toast('Gespeichert', resp.message || 'Zuordnung wurde gespeichert.','ok'); loadData(); })
        .fail(function(xhr){ toast('Fehler', xhr.responseJSON?.message || 'Zuordnung konnte nicht gespeichert werden.','bad'); })
        .always(function(){ $('#btnSaveAssignment').prop('disabled',false).text('Speichern'); });
    }

    function bulkAssignSelectedEmployees(){
      const ids=Array.from(state.selectedEmployeeIds); if(!ids.length) return;
      const mode=$('#bulkMode').val(); const positionId=$('#bulkPosition').val();
      if(mode==='all_departments_same_position' && !positionId){ toast('Position fehlt','Bitte wählen Sie eine Position.','bad'); return; }
      const departmentIds=state.filteredDepartments.map(d=>d.id);
      const positionIds=state.positions.filter(p=>!$('#positionFilter').val() || String(p.id)===String($('#positionFilter').val())).map(p=>p.id);
      Swal.fire({icon:'question',title:'Bulk-Zuordnung speichern?',html:`${ids.length} Mitarbeiter werden auf ${departmentIds.length} sichtbare Abteilungen angewendet.`,showCancelButton:true,confirmButtonText:'Ja, speichern',cancelButtonText:'Abbrechen'}).then(result=>{
        if(!result.isConfirmed) return;
        $('#btnBulkAssign').prop('disabled',true).text('Speichern…');
        $.ajax({url:urls.bulk,method:'POST',data:{employee_ids:ids,mode,position_id:positionId,position_ids:positionIds,department_ids:departmentIds,percent:$('#defaultPercent').val(),montage_percent:$('#defaultMontage').val(),office_percent:$('#defaultOffice').val(),working_hours:$('#defaultHours').val(),main:$('#defaultMain').is(':checked')?'Yes':'No',_token:urls.csrf}})
          .done(resp=>{ toast('Gespeichert', resp.message || 'Bulk-Zuordnung wurde gespeichert.','ok'); loadData(); })
          .fail(xhr=>toast('Fehler', xhr.responseJSON?.message || 'Bulk-Zuordnung konnte nicht gespeichert werden.','bad'))
          .always(()=>$('#btnBulkAssign').prop('disabled',!state.selectedEmployeeIds.size).text('Auswahl anwenden'));
      });
    }

    function removeAssignment(id){
      Swal.fire({icon:'warning',title:'Zuordnung entfernen?',text:'Der Mitarbeiter wird aus dieser Position entfernt.',showCancelButton:true,confirmButtonText:'Ja, entfernen',cancelButtonText:'Abbrechen',confirmButtonColor:'#ef4444'}).then(result=>{
        if(!result.isConfirmed) return;
        $.ajax({url:`${urls.removeBase}/${id}`,method:'DELETE',data:{_token:urls.csrf}}).done(resp=>{toast('Entfernt',resp.message||'Zuordnung wurde entfernt.','ok');loadData();}).fail(xhr=>toast('Fehler',xhr.responseJSON?.message||'Zuordnung konnte nicht entfernt werden.','bad'));
      });
    }
    function findAssignment(id){ for(const d of state.departments){ for(const p of (d.positions||[])){ const a=(p.assignments||[]).find(x=>Number(x.id)===Number(id)); if(a) return a; } } return null; }
    function resetFilters(){ $('#orgSearch,#departmentFilter,#positionFilter').val(''); $('#sortMode').val('department_asc'); applyFiltersAndRender(); }

    function avatarHtml(image,name,cls){ const initials=initialsFromName(name||''); return image ? `<img class="${cls}" src="/images/employee/${escapeAttr(image)}" alt="${escapeAttr(name||'')}">` : `<div class="${cls}">${escapeHtml(initials)}</div>`; }
    function initialsFromName(name){ return String(name||'#').trim().split(/\s+/).slice(0,2).map(p=>p.charAt(0).toUpperCase()).join('') || '#'; }
    function toast(title,msg,type){ const id=`toast-${Date.now()}`; $('#toastWrap').append(`<div class="oc-toast" id="${id}"><div class="oc-toast-ic ${type==='bad'?'bad':'ok'}">${type==='bad'?'!':'✓'}</div><div><p class="oc-toast-ttl">${escapeHtml(title)}</p><p class="oc-toast-msg">${escapeHtml(msg)}</p></div><button class="oc-toast-x" type="button" onclick="document.getElementById('${id}').remove()">×</button></div>`); setTimeout(()=>$('#'+id).fadeOut(250,function(){$(this).remove();}),4000); }
    function escapeHtml(v){ return String(v??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
    function escapeAttr(v){ return escapeHtml(v).replace(/`/g,'&#096;'); }
  });
  </script>
@endsection
