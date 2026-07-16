@php
  use Carbon\Carbon;

  $employeeName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
  $employeeName = $employeeName !== '' ? $employeeName : 'Mitarbeiter #' . ($data->id ?? '');

  $leaveItems = collect($leaves ?? [])->map(function ($leave) {
    return [
      'id' => (int) $leave->id,
      'emp_id' => (int) $leave->emp_id,
      'year' => (int) ($leave->year ?: Carbon::parse($leave->start_date)->format('Y')),
      'start_date' => optional(Carbon::parse($leave->start_date))->format('Y-m-d'),
      'end_date' => optional(Carbon::parse($leave->end_date))->format('Y-m-d'),
      'start_date_de' => optional(Carbon::parse($leave->start_date))->format('d.m.Y'),
      'end_date_de' => optional(Carbon::parse($leave->end_date))->format('d.m.Y'),
      'duration' => (int) ($leave->duration ?? 0),
      'leave_day' => (int) ($leave->leave_day ?? 0),
      'remaining_day' => (int) ($leave->remaining_day ?? 0),
      'reason' => (string) ($leave->reason ?? ''),
      'description' => (string) ($leave->description ?? ''),
      'status' => (string) ($leave->status ?? 'Pending'),
      'approved' => (string) ($leave->approved ?? ''),
      'request_to' => $leave->request_to ?? null,
      'created_at' => !empty($leave->created_at) ? Carbon::parse($leave->created_at)->format('d.m.Y H:i') : '',
    ];
  })->values();

  $totalLeaveCount = $leaveItems->count();
  $approvedLeaveCount = $leaveItems->filter(fn($l) => ($l['approved'] ?? '') === 'Yes' || in_array(strtolower($l['status'] ?? ''), ['accept', 'accepted', 'approved']))->count();
  $pendingLeaveCount = $leaveItems->filter(fn($l) => in_array(strtolower($l['status'] ?? ''), ['pending', 'anfrage', 'zur überprüfung', 'zur ueberpruefung']))->count();
  $usedDaysCount = $leaveItems->sum('duration');

  $mainDepartmentId = DB::table('department_positions')
    ->where('employee_id', $data->id)
    ->where(function ($q) {
      $q->where('main', 'Yes')
        ->orWhere('main', 'yes')
        ->orWhere('main', '1')
        ->orWhere('main', 1)
        ->orWhere('main', 'active');
    })
    ->orderByDesc('id')
    ->value('department_id');
@endphp

@once
  @push('style')
    <style>
      :root {
        --lv-bg: #f3f4f6;
        --lv-card: #ffffff;
        --lv-text: #1f2937;
        --lv-muted: #6b7280;
        --lv-border: #e5e7eb;
        --lv-primary: var(--sa-accent);
        --lv-primary-dark: var(--sa-accent-hover);
        --lv-primary-light: var(--sa-accent-light);
        --lv-blue: #74b2d4;
        --lv-blue-light: #eff6ff;
        --lv-success: #10b981;
        --lv-success-light: #ecfdf5;
        --lv-warning: #f59e0b;
        --lv-warning-light: #fffbeb;
        --lv-danger: #ef4444;
        --lv-danger-light: #fef2f2;
        --lv-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
        --lv-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        --lv-radius: 16px;
        --lv-transition: all .2s ease;
      }

      .lv-wrap {
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--lv-text);
      }

      .lv-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
      }

      .lv-title {
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -.03em;
        color: #111827;
        margin: 0;
      }

      .lv-sub {
        font-size: 14px;
        color: var(--lv-muted);
        margin-top: 4px;
      }

      .lv-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
      }

      .lv-btn {
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
        transition: var(--lv-transition);
        height: 42px;
        white-space: nowrap;
      }

      .lv-btn-primary {
        background: var(--lv-primary);
        color: #fff;
        box-shadow: 0 10px 22px rgba(147, 194, 28, .22);
      }

      .lv-btn-primary:hover {
        background: var(--lv-primary-dark);
        color: #fff;
        transform: translateY(-1px);
      }

      .lv-btn-soft {
        background: #fff;
        color: var(--lv-text);
        border: 1px solid var(--lv-border);
      }

      .lv-btn-soft:hover {
        background: #f9fafb;
        color: var(--lv-text);
        text-decoration: none;
      }

      .lv-btn-danger {
        background: var(--lv-danger);
        color: #fff;
      }

      .lv-btn-danger:hover {
        background: #dc2626;
        color: #fff;
      }

      .lv-btn-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        border: 1px solid var(--lv-border);
        background: #fff;
        color: var(--lv-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--lv-transition);
      }

      .lv-btn-icon:hover {
        border-color: var(--lv-blue);
        color: var(--lv-blue);
        background: #f0f7fb;
      }

      .lv-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
      }

      @media(max-width:1200px) {
        .lv-stats {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }

      @media(max-width:720px) {
        .lv-stats {
          grid-template-columns: 1fr;
        }
      }

      .lv-stat {
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 18px;
        padding: 16px;
        box-shadow: var(--lv-shadow-sm);
        display: flex;
        gap: 12px;
        align-items: center;
        min-height: 92px;
      }

      .lv-stat-ic {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
      }

      .lv-stat-ic.total {
        background: var(--lv-blue-light);
        color: var(--lv-blue)
      }

      .lv-stat-ic.ok {
        background: var(--lv-success-light);
        color: var(--lv-success)
      }

      .lv-stat-ic.warn {
        background: var(--lv-warning-light);
        color: #d97706
      }

      .lv-stat-ic.days {
        background: var(--lv-primary-light);
        color: #4d7c0f
      }

      .lv-stat-label {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--lv-muted);
      }

      .lv-stat-value {
        font-size: 24px;
        font-weight: 900;
        color: #111827;
        line-height: 1.1;
        margin-top: 4px;
      }

      .lv-stat-sub {
        font-size: 12px;
        color: var(--lv-muted);
        margin-top: 4px;
      }

      .lv-toolbar {
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: var(--lv-radius);
        padding: 14px 16px;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        box-shadow: var(--lv-shadow-sm);
        margin-bottom: 16px;
      }

      .lv-filter-left,
      .lv-filter-right {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
      }

      .lv-filter-left {
        flex: 1;
      }

      .lv-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 170px;
      }

      .lv-field.search {
        flex: 1;
        min-width: 260px;
      }

      .lv-label {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--lv-muted);
      }

      .lv-input,
      .lv-select,
      .lv-textarea {
        width: 100%;
        border: 1px solid var(--lv-border);
        border-radius: 11px;
        background: #fff;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
        transition: var(--lv-transition);
      }

      .lv-input:focus,
      .lv-select:focus,
      .lv-textarea:focus {
        border-color: var(--lv-primary);
        box-shadow: 0 0 0 3px var(--lv-primary-light);
      }

      .lv-textarea {
        resize: vertical;
        min-height: 96px;
      }

      .lv-card {
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 18px;
        box-shadow: var(--lv-shadow-sm);
        overflow: hidden;
      }

      .lv-list-head {
        display: grid;
        grid-template-columns: 90px minmax(190px, 1fr) minmax(160px, .8fr) 130px 140px 150px 130px;
        gap: 14px;
        align-items: center;
        padding: 16px 18px 10px;
        color: var(--lv-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .lv-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 0 0 16px;
      }

      .lv-row {
        margin: 0 16px;
        border: 1px solid var(--lv-border);
        border-radius: 16px;
        background: #fff;
        transition: var(--lv-transition);
        overflow: visible;
      }

      .lv-row:hover {
        border-color: var(--lv-primary);
        box-shadow: var(--lv-shadow);
      }

      .lv-row-inner {
        display: grid;
        grid-template-columns: 90px minmax(190px, 1fr) minmax(160px, .8fr) 130px 140px 150px 130px;
        gap: 14px;
        align-items: center;
        padding: 16px;
      }

      @media(max-width:1280px) {
        .lv-list-head {
          display: none
        }

        .lv-row-inner {
          grid-template-columns: 1fr
        }

        .lv-mobile-title {
          display: block !important
        }

        .lv-row {
          margin: 0 12px;
        }
      }

      .lv-mobile-title {
        display: none;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        color: var(--lv-muted);
        letter-spacing: .06em;
        margin-bottom: 5px;
      }

      .lv-id {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        min-width: 58px;
        padding: 0 10px;
        border-radius: 10px;
        background: var(--lv-blue-light);
        color: var(--lv-blue);
        font-weight: 900;
        font-size: 13px;
      }

      .lv-main-title {
        font-weight: 900;
        color: #111827;
        font-size: 15px;
        margin-bottom: 4px;
      }

      .lv-main-sub {
        font-size: 13px;
        color: var(--lv-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 420px;
      }

      .lv-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
      }

      .lv-pill.ok {
        background: var(--lv-success-light);
        color: #047857;
      }

      .lv-pill.warn {
        background: var(--lv-warning-light);
        color: #b45309;
      }

      .lv-pill.bad {
        background: var(--lv-danger-light);
        color: #b91c1c;
      }

      .lv-pill.gray {
        background: #f3f4f6;
        color: #374151;
      }

      .lv-desc-preview {
        border: 1px solid var(--lv-border);
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
      }

      .lv-desc-preview:hover {
        background: #f0f7fb;
        border-color: rgba(116, 178, 212, .5);
      }

      .lv-actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        align-items: center;
        position: relative;
      }

      @media(max-width:1280px) {
        .lv-actions-cell {
          justify-content: flex-start;
        }
      }

      .lv-action-menu {
        position: relative;
        display: inline-flex;
      }

      .lv-action-list {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        min-width: 220px;
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 14px;
        box-shadow: var(--lv-shadow);
        padding: 7px;
        z-index: 50;
        display: none;
      }

      .lv-action-list.show {
        display: block;
      }

      .lv-action-item {
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

      .lv-action-item:hover {
        background: #f8fafc;
        color: #111827;
        text-decoration: none;
      }

      .lv-action-item.danger {
        color: #dc2626;
      }

      .lv-action-item.success {
        color: #047857;
      }

      .lv-empty {
        margin: 16px;
        padding: 46px 20px;
        text-align: center;
        color: var(--lv-muted);
        border: 1px dashed var(--lv-border);
        border-radius: 16px;
        background: #fff;
      }

      .lv-modal-backdrop {
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

      .lv-modal-backdrop.open {
        opacity: 1;
        visibility: visible;
      }

      .lv-modal {
        width: min(760px, 100%);
        max-height: 90vh;
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(229, 231, 235, .95);
        box-shadow: var(--lv-shadow);
        overflow: hidden;
        transform: translateY(14px) scale(.985);
        transition: transform .22s ease;
        display: flex;
        flex-direction: column;
      }

      .lv-modal-backdrop.open .lv-modal {
        transform: translateY(0) scale(1);
      }

      .lv-modal-header {
        display: grid;
        grid-template-columns: 50px 1fr 38px;
        gap: 12px;
        align-items: flex-start;
        padding: 18px 20px;
        border-bottom: 1px solid var(--lv-border);
        background: linear-gradient(135deg, #fff, #f8fcff);
      }

      .lv-modal-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        background: var(--lv-primary-light);
        color: #4d7c0f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      .lv-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #111827;
      }

      .lv-modal-sub {
        font-size: 13px;
        color: var(--lv-muted);
        margin-top: 4px;
      }

      .lv-modal-body {
        padding: 20px;
        overflow: auto;
      }

      .lv-modal-footer {
        border-top: 1px solid var(--lv-border);
        padding: 14px 20px;
        background: #fafafa;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
      }

      .lv-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
      }

      @media(max-width:760px) {
        .lv-form-grid {
          grid-template-columns: 1fr
        }

        .lv-modal-header {
          grid-template-columns: 42px 1fr 36px
        }

        .lv-modal-icon {
          width: 42px;
          height: 42px;
          border-radius: 14px
        }

        .lv-modal-footer .lv-btn {
          width: 100%;
        }
      }

      .lv-help {
        font-size: 12px;
        color: var(--lv-muted);
        margin-top: 5px;
      }

      .lv-error {
        display: none;
        margin-top: 8px;
        border-radius: 12px;
        background: var(--lv-danger-light);
        color: #991b1b;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 800;
        white-space: pre-wrap;
      }

      .lv-error.show {
        display: block;
      }

      .lv-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        width: min(460px, 100%);
        height: 100%;
        background: #fff;
        z-index: 1300;
        box-shadow: -10px 0 40px rgba(15, 23, 42, .18);
        transform: translateX(105%);
        transition: transform .25s ease;
        display: flex;
        flex-direction: column;
      }

      .lv-sidebar.open {
        transform: translateX(0);
      }

      .lv-sidebar-header {
        padding: 18px 20px;
        border-bottom: 1px solid var(--lv-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #fff, #f8fcff);
      }

      .lv-sidebar-body {
        padding: 18px 20px;
        overflow: auto;
        flex: 1;
      }

      .lv-note-item {
        display: flex;
        gap: 10px;
        border: 1px solid var(--lv-border);
        border-radius: 14px;
        padding: 10px;
        margin-bottom: 10px;
        background: #fff;
      }

      .lv-note-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        flex: 0 0 auto;
      }

      .lv-note-text {
        font-size: 13px;
        color: #374151;
        line-height: 1.45;
        white-space: pre-wrap;
      }

      .mention {
        background: #e6f3ff;
        color: #007bff;
        font-weight: 800;
        border-radius: 5px;
        padding: 1px 4px;
      }

      #lvMentionSuggestions {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        z-index: 1400;
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 12px;
        box-shadow: var(--lv-shadow);
        padding: 6px;
        list-style: none;
        margin: 4px 0 0;
        max-height: 220px;
        overflow: auto;
      }

      #lvMentionSuggestions li {
        padding: 8px 10px;
        border-radius: 9px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
      }

      #lvMentionSuggestions li:hover {
        background: #f8fafc;
      }

      .lv-card {
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 18px;
        box-shadow: var(--lv-shadow-sm);
        overflow: visible !important;
        position: relative;
        z-index: 1;
      }

      .lv-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 0 0 90px;
        overflow: visible !important;
      }

      .lv-row {
        margin: 0 16px;
        border: 1px solid var(--lv-border);
        border-radius: 16px;
        background: #fff;
        transition: var(--lv-transition);
        overflow: visible !important;
        position: relative;
      }

      .lv-row.menu-open {
        z-index: 9999;
      }

      .lv-actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        align-items: center;
        position: relative;
        overflow: visible !important;
      }

      .lv-action-menu {
        position: relative;
        display: inline-flex;
        overflow: visible !important;
      }

      .lv-action-list {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        min-width: 220px;
        background: #fff;
        border: 1px solid var(--lv-border);
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .25);
        padding: 7px;
        z-index: 10000;
        display: none;
      }

      .lv-action-list.show {
        display: block;
      }
    </style>
  @endpush
@endonce

<div class="lv-wrap" id="leave-app" data-employee-id="{{ $data->id }}"
  data-department-id="{{ $mainDepartmentId ?? '' }}">
  <div class="lv-header">
    <div>
      <h2 class="lv-title">Urlaub & Abwesenheiten</h2>
      <div class="lv-sub">Anträge von {{ $employeeName }} verwalten, prüfen und ohne Seitenreload aktualisieren.</div>
    </div>
    <div class="lv-actions">
      <button type="button" class="lv-btn lv-btn-soft" id="lv-refresh-btn"><i class="feather icon-refresh-cw"></i>
        Aktualisieren</button>
      <button type="button" class="lv-btn lv-btn-primary" id="lv-open-create"><i class="feather icon-plus"></i> Neue
        Anfrage</button>
    </div>
  </div>

  <div class="lv-stats">
    <div class="lv-stat">
      <div class="lv-stat-ic total"><i class="feather icon-layers"></i></div>
      <div>
        <div class="lv-stat-label">Gesamt</div>
        <div class="lv-stat-value" id="lv-stat-total">{{ $totalLeaveCount }}</div>
        <div class="lv-stat-sub">Urlaubsanträge</div>
      </div>
    </div>
    <div class="lv-stat">
      <div class="lv-stat-ic ok"><i class="feather icon-check-circle"></i></div>
      <div>
        <div class="lv-stat-label">Genehmigt</div>
        <div class="lv-stat-value" id="lv-stat-approved">{{ $approvedLeaveCount }}</div>
        <div class="lv-stat-sub">Freigegebene Anträge</div>
      </div>
    </div>
    <div class="lv-stat">
      <div class="lv-stat-ic warn"><i class="feather icon-clock"></i></div>
      <div>
        <div class="lv-stat-label">Offen</div>
        <div class="lv-stat-value" id="lv-stat-pending">{{ $pendingLeaveCount }}</div>
        <div class="lv-stat-sub">Warten auf Prüfung</div>
      </div>
    </div>
    <div class="lv-stat">
      <div class="lv-stat-ic days"><i class="feather icon-calendar"></i></div>
      <div>
        <div class="lv-stat-label">Genutzte Tage</div>
        <div class="lv-stat-value" id="lv-stat-days">{{ $usedDaysCount }}</div>
        <div class="lv-stat-sub">Aktuelle Liste</div>
      </div>
    </div>
  </div>

  <div class="lv-toolbar">
    <div class="lv-filter-left">
      <div class="lv-field search"><label class="lv-label">Suche</label><input class="lv-input" id="lv-search"
          placeholder="Suche nach Grund, Beschreibung, Status oder Jahr"></div>
      <div class="lv-field"><label class="lv-label">Jahr</label><select class="lv-select" id="lv-filter-year">
          <option value="">Alle Jahre</option>
        </select></div>
      <div class="lv-field"><label class="lv-label">Status</label><select class="lv-select" id="lv-filter-status">
          <option value="">Alle Status</option>
          <option value="pending">Offen</option>
          <option value="approved">Genehmigt</option>
          <option value="rejected">Abgelehnt</option>
        </select></div>
    </div>
    <div class="lv-filter-right">
      <button type="button" class="lv-btn lv-btn-soft" id="lv-reset-filter"><i class="feather icon-x"></i> Filter
        löschen</button>
    </div>
  </div>

  <div class="lv-card">
    <div class="lv-list-head">
      <div>ID</div>
      <div>Zeitraum</div>
      <div>Grund</div>
      <div>Dauer</div>
      <div>Status</div>
      <div>Beschreibung</div>
      <div style="text-align:right;">Aktionen</div>
    </div>
    <div class="lv-list" id="lv-list"></div>
  </div>

  <div class="lv-modal-backdrop" id="lv-form-modal">
    <div class="lv-modal">
      <div class="lv-modal-header">
        <div class="lv-modal-icon"><i class="feather icon-calendar"></i></div>
        <div>
          <h3 class="lv-modal-title" id="lv-form-title">Neue Urlaubsanfrage</h3>
          <div class="lv-modal-sub" id="lv-form-sub">Urlaubszeitraum und Anfrageempfänger eintragen.</div>
        </div>
        <button type="button" class="lv-btn-icon" data-close-modal="lv-form-modal"><i
            class="feather icon-x"></i></button>
      </div>
      <form id="lv-form">
        @csrf
        <input type="hidden" name="id" id="lv-id">
        <input type="hidden" name="active_tab" value="leave">
        <input type="hidden" name="emp_id" value="{{ $data->id }}">
        <input type="hidden" name="department_id" value="{{ $mainDepartmentId ?? '' }}">
        <div class="lv-modal-body">
          <div class="lv-form-grid">
            <div class="lv-field"><label class="lv-label">Jahr</label><select name="year" id="lv-year"
                class="lv-select"></select></div>
            <div class="lv-field"><label class="lv-label">Anfrage an</label><select name="request_to" id="lv-request-to"
                class="lv-select" data-department="{{ $mainDepartmentId ?? '' }}">
                <option value="">Abteilungsleiter wählen</option>
              </select></div>
            <div class="lv-field"><label class="lv-label">Ab Datum</label><input type="date" name="start_date"
                id="lv-start" class="lv-input" required></div>
            <div class="lv-field"><label class="lv-label">Bis Datum</label><input type="date" name="end_date"
                id="lv-end" class="lv-input" required></div>
            <div class="lv-field"><label class="lv-label">Urlaubstage</label><input type="number" name="leave_day"
                id="lv-leave-day" class="lv-input" readonly></div>
            <div class="lv-field"><label class="lv-label">Rest nach Antrag</label><input type="number"
                name="remaining_day" id="lv-remaining" class="lv-input" readonly></div>
            <div class="lv-field"><label class="lv-label">Vorjahresurlaub</label><input type="number"
                name="last_year_remainings" id="lv-last-year" class="lv-input" readonly></div>
            <div class="lv-field"><label class="lv-label">Eingereichte Tage</label><input type="number" name="duration"
                id="lv-duration" class="lv-input" readonly>
              <div class="lv-help" id="lv-duration-help">Wochenenden werden nicht mitgerechnet.</div>
            </div>
          </div>
          <div class="lv-field" style="margin-top:14px;"><label class="lv-label">Grund</label><select name="reason"
              id="lv-reason" class="lv-select">
              <option value="Urlaub">Urlaub</option>
              <option value="Freizeitausgleich">Freizeitausgleich</option>
              <option value="Vorjahresurlaub">Vorjahresurlaub</option>
              <option value="Elternzeit">Elternzeit</option>
              <option value="Schulung">Schulung</option>
              <option value="Schule">Schule</option>
              <option value="Unbezahlter Urlaub">Unbezahlter Urlaub</option>
              <option value="Freigestellt">Freigestellt</option>
              <option value="Persönlicher Urlaub">Persönlicher Urlaub</option>
              <option value="Jahresurlaub">Jahresurlaub</option>
              <option value="Trauerurlaub">Trauerurlaub</option>
            </select></div>
          <div class="lv-field" style="margin-top:14px;"><label class="lv-label">Beschreibung</label><textarea
              name="description" id="lv-description" class="lv-textarea" placeholder="Beschreibung optional"></textarea>
          </div>
          <div class="lv-error" id="lv-form-error"></div>
        </div>
        <div class="lv-modal-footer"><button type="button" class="lv-btn lv-btn-soft"
            data-close-modal="lv-form-modal">Abbrechen</button><button type="submit" class="lv-btn lv-btn-primary"
            id="lv-submit"><i class="feather icon-save"></i> Speichern</button></div>
      </form>
    </div>
  </div>

  <div class="lv-modal-backdrop" id="lv-desc-modal">
    <div class="lv-modal">
      <div class="lv-modal-header">
        <div class="lv-modal-icon"><i class="feather icon-file-text"></i></div>
        <div>
          <h3 class="lv-modal-title">Beschreibung</h3>
          <div class="lv-modal-sub" id="lv-desc-sub"></div>
        </div><button type="button" class="lv-btn-icon" data-close-modal="lv-desc-modal"><i
            class="feather icon-x"></i></button>
      </div>
      <div class="lv-modal-body">
        <div id="lv-desc-body" style="white-space:pre-wrap;line-height:1.6;color:#111827;"></div>
      </div>
      <div class="lv-modal-footer"><button type="button" class="lv-btn lv-btn-primary"
          data-close-modal="lv-desc-modal">OK</button></div>
    </div>
  </div>

  <div class="lv-sidebar" id="lv-notes-sidebar">
    <div class="lv-sidebar-header">
      <div>
        <h5 style="margin:0;font-weight:900;">Notizen</h5><small class="text-muted">Interne Hinweise zum
          Urlaubsantrag</small>
      </div><button type="button" class="lv-btn-icon" id="lv-notes-close"><i class="feather icon-x"></i></button>
    </div>
    <div class="lv-sidebar-body">
      <div id="lv-notes-content"></div>
      <div style="position:relative;margin-top:14px;"><textarea id="lv-new-note" class="lv-textarea" rows="3"
          placeholder="Neue Notiz mit @Mention..."></textarea>
        <ul id="lvMentionSuggestions"></ul>
      </div>
      <button type="button" class="lv-btn lv-btn-primary" id="lv-save-note" style="width:100%;margin-top:10px;"><i
          class="feather icon-save"></i> Notiz speichern</button>
    </div>
  </div>
</div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    (function () {
      const app = document.getElementById('leave-app');
      if (!app || app.dataset.ready === '1') return;
      app.dataset.ready = '1';

      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
      const employeeId = app.dataset.employeeId;
      const departmentId = app.dataset.departmentId;
      const imageBase = @json(asset('images/employee'));
      const defaultAvatar = @json(asset('images/gender/male.png'));
      const initialLeaves = @json($leaveItems);

      const API = {
        store: @json(route('leave.store')),
        update: @json(route('leave.update')),
        deleteBase: @json(url('/leave_delete')),
        approveBase: @json(url('/leave_approve')),
        remainingBase: @json(url('/employee/remaining/days')),
        leaderBase: @json(url('/getDepartment/leader')),
        employeeNames: @json(route('leave.get.employee')),
        checkBase: @json(url('/check/department-holidays')),
        notesBase: @json(url('/leaves')),
      };

      const el = {
        list: document.getElementById('lv-list'), search: document.getElementById('lv-search'), yearFilter: document.getElementById('lv-filter-year'), statusFilter: document.getElementById('lv-filter-status'), resetFilter: document.getElementById('lv-reset-filter'), refresh: document.getElementById('lv-refresh-btn'),
        statTotal: document.getElementById('lv-stat-total'), statApproved: document.getElementById('lv-stat-approved'), statPending: document.getElementById('lv-stat-pending'), statDays: document.getElementById('lv-stat-days'),
        createBtn: document.getElementById('lv-open-create'), formModal: document.getElementById('lv-form-modal'), form: document.getElementById('lv-form'), formTitle: document.getElementById('lv-form-title'), formSub: document.getElementById('lv-form-sub'), formError: document.getElementById('lv-form-error'), submit: document.getElementById('lv-submit'),
        id: document.getElementById('lv-id'), year: document.getElementById('lv-year'), requestTo: document.getElementById('lv-request-to'), start: document.getElementById('lv-start'), end: document.getElementById('lv-end'), leaveDay: document.getElementById('lv-leave-day'), remaining: document.getElementById('lv-remaining'), lastYear: document.getElementById('lv-last-year'), duration: document.getElementById('lv-duration'), reason: document.getElementById('lv-reason'), description: document.getElementById('lv-description'),
        descModal: document.getElementById('lv-desc-modal'), descSub: document.getElementById('lv-desc-sub'), descBody: document.getElementById('lv-desc-body'), notesSidebar: document.getElementById('lv-notes-sidebar'), notesClose: document.getElementById('lv-notes-close'), notesContent: document.getElementById('lv-notes-content'), newNote: document.getElementById('lv-new-note'), saveNote: document.getElementById('lv-save-note'), mentionBox: document.getElementById('lvMentionSuggestions')
      };

      const state = { leaves: initialLeaves, mode: 'create', currentNoteId: null, employees: [] };

      const esc = s => String(s ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
      const norm = s => String(s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/ä/g, 'a').replace(/ö/g, 'o').replace(/ü/g, 'u').replace(/ß/g, 'ss').trim();
      const fmtDE = s => { if (!s) return '—'; const d = new Date(String(s).replace(' ', 'T')); return Number.isNaN(d.getTime()) ? s : d.toLocaleDateString('de-DE'); };
      const statusKey = l => { const a = String(l.approved || '').toLowerCase(); const s = String(l.status || '').toLowerCase(); if (a === 'yes' || ['accept', 'accepted', 'approved'].includes(s)) return 'approved'; if (['reject', 'rejected', 'declined'].includes(s)) return 'rejected'; return 'pending'; };
      const statusBadge = l => { const k = statusKey(l); if (k === 'approved') return '<span class="lv-pill ok"><i class="feather icon-check"></i> Genehmigt</span>'; if (k === 'rejected') return '<span class="lv-pill bad"><i class="feather icon-x"></i> Abgelehnt</span>'; return '<span class="lv-pill warn"><i class="feather icon-clock"></i> Offen</span>'; };
      const descShort = s => { const clean = String(s || '').replace(/\s+/g, ' ').trim(); return clean ? (clean.length > 80 ? clean.slice(0, 80) + '...' : clean) : 'Keine Beschreibung'; };

      function openModal(modal) { modal?.classList.add('open'); }
      function closeModal(modal) { modal?.classList.remove('open'); }
      document.addEventListener('click', e => {
        const menuBtn = e.target.closest('[data-lv-action="menu"]');

        document.querySelectorAll('.lv-action-list.show').forEach(menu => {
          if (!menuBtn || !menu.closest('.lv-action-menu').contains(menuBtn)) {
            menu.classList.remove('show');
            menu.closest('.lv-row')?.classList.remove('menu-open');
          }
        });

        if (menuBtn) {
          e.preventDefault();
          e.stopPropagation();

          const row = menuBtn.closest('.lv-row');
          const list = menuBtn.closest('.lv-action-menu')?.querySelector('.lv-action-list');

          if (list) {
            list.classList.toggle('show');
            row?.classList.toggle('menu-open', list.classList.contains('show'));
          }
        }
      });

      function fillYears() {
        const now = new Date().getFullYear();
        const years = new Set(state.leaves.map(l => Number(l.year)).filter(Boolean));
        for (let y = now - 5; y <= now + 1; y++) years.add(y);
        const sorted = Array.from(years).sort((a, b) => b - a);
        el.year.innerHTML = sorted.sort((a, b) => a - b).map(y => `<option value="${y}">${y}</option>`).join('');
        el.year.value = now;
        el.yearFilter.innerHTML = '<option value="">Alle Jahre</option>' + Array.from(years).sort((a, b) => b - a).map(y => `<option value="${y}">${y}</option>`).join('');
      }

      function calcWorkingDays(start, end) {
        if (!start || !end) return 0;
        const s = new Date(start); const e = new Date(end); let c = 0;
        if (Number.isNaN(s.getTime()) || Number.isNaN(e.getTime()) || e < s) return 0;
        while (s <= e) { const d = s.getDay(); if (d !== 0 && d !== 6) c++; s.setDate(s.getDate() + 1); }
        return c;
      }

      async function jsonFetch(url, options = {}) {
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) }, ...options });
        const txt = await res.text(); let data = {}; try { data = JSON.parse(txt || '{}'); } catch (_) { data = { message: txt }; }
        if (!res.ok) { const err = new Error(data.message || 'Fehler'); err.data = data; err.status = res.status; throw err; }
        return data;
      }



      async function fetchRemaining() {
        if (!employeeId || !el.year.value) return;
        try {
          const d = await jsonFetch(`${API.remainingBase}/${employeeId}?year=${encodeURIComponent(el.year.value)}`);
          el.leaveDay.value = d.total_leave_days ?? 0;
          el.lastYear.value = d.last_year_remainings ?? 0;
          calculateDates();
        } catch (e) { console.error(e); }
      }

      function calculateDates() {
        const days = calcWorkingDays(el.start.value, el.end.value);
        const total = parseInt(el.leaveDay.value || '0', 10);
        el.duration.value = days;
        el.remaining.value = Math.max(total - days, 0);
        el.submit.disabled = days > total || days <= 0;
        document.getElementById('lv-duration-help').textContent = days > total ? 'Die Dauer überschreitet die verfügbaren Urlaubstage.' : 'Wochenenden werden nicht mitgerechnet.';
      }

      function normalizeLeaveFromResponse(data, fallback) {
        const l = data.leave || data.data || {};
        const base = { ...fallback, ...l };
        base.id = Number(base.id || data.leave_id || fallback.id || 0);
        base.emp_id = Number(base.emp_id || employeeId);
        base.year = Number(base.year || (base.start_date ? new Date(base.start_date).getFullYear() : el.year.value));
        base.duration = Number(base.duration || fallback.duration || 0);
        base.leave_day = Number(base.leave_day || fallback.leave_day || 0);
        base.remaining_day = Number(base.remaining_day || fallback.remaining_day || 0);
        base.start_date_de = fmtDE(base.start_date);
        base.end_date_de = fmtDE(base.end_date);
        return base;
      }

      function renderStats(items) {
        el.statTotal.textContent = items.length;
        el.statApproved.textContent = items.filter(l => statusKey(l) === 'approved').length;
        el.statPending.textContent = items.filter(l => statusKey(l) === 'pending').length;
        el.statDays.textContent = items.reduce((s, l) => s + Number(l.duration || 0), 0);
      }

      function filteredLeaves() {
        const q = norm(el.search.value); const y = el.yearFilter.value; const st = el.statusFilter.value;
        return state.leaves.filter(l => {
          const hay = norm(`${l.id} ${l.year} ${l.reason} ${l.description} ${l.status} ${l.approved}`);
          if (q && !hay.includes(q)) return false;
          if (y && String(l.year) !== String(y)) return false;
          if (st && statusKey(l) !== st) return false;
          return true;
        }).sort((a, b) => String(b.start_date).localeCompare(String(a.start_date)));
      }

      function render() {
        fillYears();
        const items = filteredLeaves();
        renderStats(items);
        if (!items.length) { el.list.innerHTML = '<div class="lv-empty">Keine Urlaubsanträge gefunden.</div>'; return; }
        el.list.innerHTML = items.map(l => `
          <div class="lv-row" data-id="${l.id}">
            <div class="lv-row-inner">
              <div><div class="lv-mobile-title">ID</div><span class="lv-id">#${l.id}</span></div>
              <div><div class="lv-mobile-title">Zeitraum</div><div class="lv-main-title">${esc(fmtDE(l.start_date))} → ${esc(fmtDE(l.end_date))}</div><div class="lv-main-sub">Jahr ${esc(l.year || '—')}</div></div>
              <div><div class="lv-mobile-title">Grund</div><div class="lv-main-title">${esc(l.reason || '—')}</div><div class="lv-main-sub">${esc(l.created_at || '')}</div></div>
              <div><div class="lv-mobile-title">Dauer</div><span class="lv-pill gray">${Number(l.duration || 0)} Tag(e)</span></div>
              <div><div class="lv-mobile-title">Status</div>${statusBadge(l)}</div>
              <div><div class="lv-mobile-title">Beschreibung</div><button type="button" class="lv-desc-preview" data-lv-action="desc" data-id="${l.id}">${esc(descShort(l.description))}</button></div>
              <div class="lv-actions-cell"><div class="lv-action-menu"><button type="button" class="lv-btn-icon" data-lv-action="menu"><i class="feather icon-more-vertical"></i></button><div class="lv-action-list">
                <button type="button" class="lv-action-item" data-lv-action="edit" data-id="${l.id}"><i class="feather icon-edit"></i> Bearbeiten</button>
                ${statusKey(l) !== 'approved' ? `<button type="button" class="lv-action-item success" data-lv-action="approve" data-id="${l.id}"><i class="feather icon-check-square"></i> Genehmigen</button>` : ''}
                <button type="button" class="lv-action-item" data-lv-action="check" data-id="${l.id}"><i class="feather icon-calendar"></i> Konflikt prüfen</button>
                <button type="button" class="lv-action-item" data-lv-action="notes" data-id="${l.id}"><i class="feather icon-file-text"></i> Notizen</button>
                <button type="button" class="lv-action-item danger" data-lv-action="delete" data-id="${l.id}"><i class="feather icon-trash-2"></i> Löschen</button>
              </div></div></div>
            </div>
          </div>`).join('');
      }

      async function loadLeaders() {
        if (!departmentId) return;
        try {
          const rows = await jsonFetch(`${API.leaderBase}/${departmentId}`);
          const list = Array.isArray(rows) ? rows : [];
          el.requestTo.innerHTML = '<option value="">Abteilungsleiter wählen</option>' + list.map(emp => `<option value="${esc(emp.emp_id || emp.id)}" data-img="${esc(emp.image ? imageBase + '/' + emp.image : defaultAvatar)}">${esc((emp.name || '') + ' ' + (emp.lastname || ''))}</option>`).join('');
          if (list.length === 1) el.requestTo.value = list[0].emp_id || list[0].id;
          if (window.jQuery && jQuery.fn.select2 && !jQuery(el.requestTo).data('select2')) jQuery(el.requestTo).select2({ width: '100%', dropdownParent: jQuery('#lv-form-modal'), placeholder: 'Abteilungsleiter' });
        } catch (e) { console.error(e); }
      }

      function resetForm(mode, leave) {
        state.mode = mode;
        el.form.reset(); el.formError.classList.remove('show'); el.formError.textContent = ''; el.id.value = '';
        const now = new Date().getFullYear(); el.year.value = now;
        if (mode === 'edit' && leave) {
          el.formTitle.textContent = 'Urlaubsantrag bearbeiten'; el.formSub.textContent = `Antrag #${leave.id} aktualisieren.`;
          el.id.value = leave.id; el.year.value = leave.year || now; el.start.value = leave.start_date || ''; el.end.value = leave.end_date || ''; el.leaveDay.value = leave.leave_day || ''; el.remaining.value = leave.remaining_day || ''; el.duration.value = leave.duration || ''; el.reason.value = leave.reason || 'Urlaub'; el.description.value = leave.description || ''; if (leave.request_to) el.requestTo.value = leave.request_to;
        } else {
          el.formTitle.textContent = 'Neue Urlaubsanfrage'; el.formSub.textContent = 'Urlaubszeitraum und Anfrageempfänger eintragen.';
          fetchRemaining();
        }
      }

      el.createBtn.addEventListener('click', async () => { resetForm('create'); await loadLeaders(); openModal(el.formModal); });
      [el.start, el.end].forEach(x => x.addEventListener('change', calculateDates));
      el.year.addEventListener('change', fetchRemaining);
      [el.search, el.yearFilter, el.statusFilter].forEach(x => x.addEventListener('input', render));
      el.resetFilter.addEventListener('click', () => { el.search.value = ''; el.yearFilter.value = ''; el.statusFilter.value = ''; render(); });
      el.refresh.addEventListener('click', render);

      el.form.addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(el.form); const payload = Object.fromEntries(fd.entries());
        const url = state.mode === 'edit' ? API.update : API.store;
        el.submit.disabled = true;
        try {
          const data = await jsonFetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
          const fallback = { ...payload, id: payload.id, duration: el.duration.value, leave_day: el.leaveDay.value, remaining_day: el.remaining.value, status: 'Pending', approved: state.mode === 'edit' ? 'Pending' : '' };
          const fresh = normalizeLeaveFromResponse(data, fallback);
          const idx = state.leaves.findIndex(l => Number(l.id) === Number(fresh.id));
          if (idx >= 0) state.leaves[idx] = { ...state.leaves[idx], ...fresh }; else state.leaves.unshift(fresh);

          closeModal(el.formModal); render();
          Swal.fire({ icon: 'success', title: 'Gespeichert', text: data.message || 'Urlaub wurde gespeichert.', timer: 1600, showConfirmButton: false });
        } catch (err) {
          const errors = err.data?.errors ? Object.values(err.data.errors).flat().join('\n') : (err.data?.error || err.data?.message || 'Speichern fehlgeschlagen.');
          el.formError.textContent = errors; el.formError.classList.add('show');
        } finally { el.submit.disabled = false; }
      });

      el.list.addEventListener('click', async e => {
        const btn = e.target.closest('[data-lv-action]'); if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const action = btn.dataset.lvAction;
        const row = btn.closest('.lv-row'); const id = Number(btn.dataset.id || row?.dataset.id || 0);
        const leave = state.leaves.find(l => Number(l.id) === id);
        if (action === 'menu') { e.stopPropagation(); const m = btn.nextElementSibling; document.querySelectorAll('.lv-action-list.show').forEach(x => { if (x !== m) x.classList.remove('show'); }); m?.classList.toggle('show'); return; }
        document.querySelectorAll('.lv-action-list.show').forEach(x => x.classList.remove('show'));
        if (action === 'desc' && leave) { el.descSub.textContent = `${fmtDE(leave.start_date)} → ${fmtDE(leave.end_date)}`; el.descBody.textContent = leave.description || 'Keine Beschreibung vorhanden.'; openModal(el.descModal); }
        if (action === 'edit' && leave) { resetForm('edit', leave); await loadLeaders(); openModal(el.formModal); }
        if (action === 'delete' && leave) {
          const r = await Swal.fire({
            title: 'Löschen?',
            text: 'Diese Urlaubsanfrage wirklich löschen?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
          });

          if (r.isConfirmed) {
            try {
              await jsonFetch(`${API.deleteBase}/${id}`, { method: 'DELETE' });
              state.leaves = state.leaves.filter(l => Number(l.id) !== id);
              render();
              Swal.fire({ icon: 'success', title: 'Gelöscht', text: 'Urlaubsanfrage wurde gelöscht.', timer: 1400, showConfirmButton: false });
            } catch (err) {
              Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: err.data?.message || err.message || 'Urlaubsanfrage konnte nicht gelöscht werden.'
              });
            }
          }
        }
        if (action === 'approve') { window.location.href = `${API.approveBase}/${id}`; }
        if (action === 'check' && leave) { checkConflict(leave); }
        if (action === 'notes' && leave) { openNotes(id); }
      });

      document.addEventListener('click', e => { if (!e.target.closest('.lv-action-menu')) document.querySelectorAll('.lv-action-list.show').forEach(x => x.classList.remove('show')); });

      async function checkConflict(leave) {
        try {
          const data = await jsonFetch(`${API.checkBase}/${employeeId}/${leave.start_date}/${leave.end_date}`);
          let html = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;text-align:left"><div><h6>Im Urlaub (${data.conflict_count || 0})</h6><ul class="list-group">${(data.conflicts || []).map(x => `<li class="list-group-item"><b>${esc(x.name)} ${esc(x.lastname)}</b><br><small>${esc(x.department_name || '')}</small><br><small>${esc(x.start_date)} → ${esc(x.end_date)}</small></li>`).join('') || '<li class="list-group-item">Keine Konflikte</li>'}</ul></div><div><h6>Anwesend (${data.present_count || 0})</h6><ul class="list-group">${(data.present || []).map(x => `<li class="list-group-item"><b>${esc(x.name)} ${esc(x.lastname)}</b></li>`).join('') || '<li class="list-group-item">Keine Daten</li>'}</ul></div></div>`;
          Swal.fire({ title: 'Abteilungsübersicht', html, width: '900px', confirmButtonText: 'Schließen' });
        } catch (_) { Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error'); }
      }

      async function loadEmployees() { try { const d = await jsonFetch(API.employeeNames); state.employees = Array.isArray(d) ? d : (d.employees || []); } catch (_) { } }
      async function openNotes(id) { state.currentNoteId = id; el.notesSidebar.classList.add('open'); await loadNotes(); }
      el.notesClose.addEventListener('click', () => el.notesSidebar.classList.remove('open'));
      async function loadNotes() { if (!state.currentNoteId) return; const notes = await jsonFetch(`${API.notesBase}/${state.currentNoteId}/notes`); renderNotes(Array.isArray(notes) ? notes : []); }
      function renderNotes(notes) { el.notesContent.innerHTML = notes.length ? notes.map((n, i) => `<div class="lv-note-item"><img src="${esc(n.image ? imageBase + '/' + n.image : defaultAvatar)}" class="lv-note-avatar"><div style="flex:1"><div style="font-size:12px;color:#6b7280"><b>${esc(n.employee || 'Mitarbeiter')}</b> · ${esc(n.date || '')}</div><div class="lv-note-text">${n.text || ''}</div><div style="margin-top:7px"><button class="lv-btn-icon" onclick="window.lvEditNote(${i})"><i class="feather icon-edit"></i></button> <button class="lv-btn-icon" onclick="window.lvDeleteNote(${i})"><i class="feather icon-trash-2"></i></button></div></div></div>`).join('') : '<div class="lv-empty" style="margin:0;padding:25px">Keine Notizen vorhanden.</div>'; }
      el.saveNote.addEventListener('click', async () => { const text = el.newNote.value.trim(); if (!text || !state.currentNoteId) return; const d = await jsonFetch(`${API.notesBase}/${state.currentNoteId}/notes/store`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ text }) }); el.newNote.value = ''; renderNotes(d.notes || []); });
      window.lvDeleteNote = async idx => { const r = await Swal.fire({ title: 'Löschen?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ja' }); if (r.isConfirmed) { const d = await jsonFetch(`${API.notesBase}/${state.currentNoteId}/notes/delete/${idx}`, { method: 'DELETE' }); renderNotes(d.notes || []); } };
      window.lvEditNote = async idx => { const text = prompt('Neue Notiz eingeben:'); if (!text) return; const d = await jsonFetch(`${API.notesBase}/${state.currentNoteId}/notes/update/${idx}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ text }) }); renderNotes(d.notes || []); };
      el.newNote.addEventListener('input', function () { const value = this.value; const caret = this.selectionStart; const match = value.substring(0, caret).match(/@([\w.\-]*)$/); if (!match) { el.mentionBox.style.display = 'none'; return; } const q = norm(match[1]); const names = state.employees.map(x => typeof x === 'string' ? x : `${x.name || ''} ${x.lastname || ''}`.trim()).filter(n => norm(n).includes(q)).slice(0, 6); el.mentionBox.innerHTML = names.map(n => `<li>${esc(n)}</li>`).join(''); el.mentionBox.style.display = names.length ? 'block' : 'none'; Array.from(el.mentionBox.children).forEach(li => li.onclick = () => { this.value = value.substring(0, caret - match[0].length) + '@' + li.textContent + ' ' + value.substring(caret); el.mentionBox.style.display = 'none'; this.focus(); }); });

      fillYears(); render(); loadLeaders(); loadEmployees();
    })();
  </script>
@endpush