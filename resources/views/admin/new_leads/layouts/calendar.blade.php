<style>
  :root {
    --app-bg: #f3f4f6;
    --card-bg: #ffffff;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border: #e5e7eb;
    --primary: #93c21c;
    --primary-hover: #7baa18;
    --primary-light: #f4fae7;
    --blue: #74b2d4;
    --blue-light: #eff6ff;
    --success: #10b981;
    --success-light: #ecfdf5;
    --warning: #f59e0b;
    --warning-light: #fffbeb;
    --danger: #ef4444;
    --danger-light: #fef2f2;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius: 14px;
    --transition: all .2s ease-in-out;
  }

  .oc-wrap {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main);
    padding: 10px;
    max-width: 100%;
    overflow-x: hidden;
  }

  .oc-header {
    margin-bottom: 18px;
  }

  .oc-titlebar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .oc-title {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -.025em;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .oc-sub {
    font-size: 14px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .oc-breadcrumb {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    font-size: 13px;
    color: var(--text-muted);
  }

  .oc-breadcrumb a {
    color: var(--text-muted);
    text-decoration: none;
    font-weight: 700;
  }

  .oc-breadcrumb a:hover {
    color: var(--text-main);
  }

  .oc-breadcrumb span.current {
    color: #111827;
    font-weight: 800;
  }

  .oc-btn,
  .oc-btn-soft {
    border-radius: 10px;
    font-weight: 900;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .oc-btn {
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 10px 16px;
    cursor: pointer;
  }

  .oc-btn:hover {
    background: var(--primary-hover);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .oc-btn-soft {
    background: #fff;
    color: var(--text-main);
    border: 1px solid var(--border);
    padding: 10px 14px;
    cursor: default;
  }

  .oc-analytics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
  }

  .oc-stat {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 92px;
  }

  .oc-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
  }

  .oc-stat-icon.total {
    background: var(--blue-light);
    color: var(--blue);
  }

  .oc-stat-icon.upcoming {
    background: var(--primary-light);
    color: var(--primary);
  }

  .oc-stat-icon.done {
    background: var(--success-light);
    color: var(--success);
  }

  .oc-stat-icon.type {
    background: #f3f4f6;
    color: #6b7280;
  }

  .oc-stat-meta {
    min-width: 0;
  }

  .oc-stat-label {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .oc-stat-value {
    font-size: 24px;
    font-weight: 900;
    color: #111827;
    line-height: 1.1;
    margin-top: 4px;
  }

  .oc-stat-sub {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .oc-toolbar {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
  }

  .oc-toolbar-left,
  .oc-toolbar-right,
  .oc-tabs {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .oc-tabs {
    gap: 8px;
  }

  .oc-tab {
    border: 1px solid var(--border);
    background: #fff;
    color: var(--text-main);
    padding: 9px 14px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
  }

  .oc-tab:hover {
    background: #f9fafb;
    color: var(--text-main);
    text-decoration: none;
  }

  .oc-tab.active {
    background: var(--primary-light);
    border-color: #d8edaa;
    color: #5f8412;
  }

  .oc-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .oc-card-head {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border);
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
  }

  .oc-card-title {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
    color: #111827;
  }

  .oc-card-sub {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  .oc-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    max-width: 100%;
  }

  .oc-item {
    position: relative;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    transition: var(--transition);
    overflow: hidden;
    max-width: 100%;
    cursor: pointer;
  }

  .oc-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow);
    transform: translateY(-1px);
  }

  .oc-item-row {
    padding: 16px;
    display: grid;
    gap: 16px;
    align-items: start;
    grid-template-columns:
      minmax(78px, 90px) minmax(220px, 1.25fr) minmax(180px, 1fr) minmax(140px, .75fr) minmax(220px, .95fr);
    max-width: 100%;
  }

  .oc-cell {
    min-width: 0;
    max-width: 100%;
  }

  .oc-cell-title {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 6px;
    display: none;
  }

  .oc-date-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--primary-light);
    border: 1px solid #dceeb5;
    min-height: 82px;
    padding: 8px;
    text-align: center;
  }

  .oc-date-day {
    font-size: 26px;
    line-height: 1;
    font-weight: 900;
    color: #5e8213;
  }

  .oc-date-month {
    margin-top: 4px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .06em;
    color: #769922;
    text-transform: uppercase;
  }

  .oc-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .oc-ttl {
    font-weight: 800;
    font-size: 15px;
    margin-bottom: 6px;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    min-width: 0;
  }

  .oc-title-link {
    color: #111827;
    text-decoration: none;
    font-weight: 900;
    max-width: 100%;
    overflow-wrap: anywhere;
  }

  .oc-title-link:hover {
    color: var(--primary);
    text-decoration: underline;
  }

  .oc-subt {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.45;
    overflow-wrap: anywhere;
  }

  .oc-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    white-space: nowrap;
  }

  .oc-badge.type {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .oc-badge.priority-high {
    background: #fef2f2;
    color: #b91c1c;
  }

  .oc-badge.priority-normal {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .oc-badge.priority-low {
    background: #ecfdf5;
    color: #047857;
  }

  .oc-note {
    margin-top: 10px;
    padding: 10px 12px;
    border-radius: 12px;
    background: #f9fafb;
    border: 1px solid #edf0f3;
    color: #4b5563;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
  }

  .oc-note svg {
    width: 14px;
    height: 14px;
    margin-right: 6px;
  }

  .oc-meta-stack {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
  }

  .oc-meta-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #374151;
    background: #f9fafb;
    border: 1px solid #edf0f3;
    border-radius: 10px;
    padding: 8px 10px;
    min-width: 0;
    overflow-wrap: anywhere;
  }

  .oc-meta-pill:hover {
    color: #111827;
    border-color: #d1d5db;
  }

  .oc-meta-pill svg {
    width: 14px;
    height: 14px;
    flex: 0 0 auto;
  }

  .oc-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }

  .oc-profile-link,
  .oc-report-link {
    min-height: 38px;
    padding: 0 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    font-size: 12px;
    font-weight: 900;
    white-space: nowrap;
    border: 1px solid transparent;
  }

  .oc-profile-link {
    border-color: #dceeb5;
    background: var(--primary-light);
    color: #5f8412;
  }

  .oc-profile-link:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .oc-report-link {
    border-color: #bfdbfe;
    background: #eff6ff;
    color: #1d4ed8;
  }

  .oc-report-link:hover {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .oc-profile-link svg,
  .oc-report-link svg {
    width: 15px;
    height: 15px;
  }

  .oc-report-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    border-radius: 999px;
    background: #dbeafe;
    color: #1d4ed8;
    font-size: 11px;
    font-weight: 900;
    padding: 0 6px;
  }

  .oc-report-link:hover .oc-report-count {
    background: rgba(255, 255, 255, .22);
    color: #fff;
  }

  .oc-empty {
    text-align: center;
    padding: 60px;
    color: var(--text-muted);
    background: #fff;
    border: 1px dashed var(--border);
    border-radius: 16px;
    margin: 16px;
  }

  .oc-calendar-wrap {
    padding: 16px;
  }

  #fullCalendar {
    min-height: 680px;
    background: #fff;
    border-radius: 14px;
  }

  .fc {
    --fc-border-color: #e5e7eb;
    --fc-page-bg-color: #fff;
    --fc-neutral-bg-color: #f9fafb;
    --fc-list-event-hover-bg-color: #f9fafb;
    --fc-today-bg-color: #f4fae7;
  }

  .fc .fc-toolbar {
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px !important;
  }

  .fc .fc-toolbar-title {
    font-size: 20px;
    font-weight: 900;
    color: #111827;
  }

  .fc .fc-button {
    background: #fff !important;
    color: #374151 !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 10px !important;
    box-shadow: none !important;
    padding: .5rem .8rem !important;
    font-weight: 800 !important;
    text-transform: capitalize !important;
  }

  .fc .fc-button:hover {
    background: #f9fafb !important;
    border-color: #d1d5db !important;
  }

  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:not(:disabled):active {
    background: var(--primary-light) !important;
    border-color: #dceeb5 !important;
    color: #5f8412 !important;
  }

  .fc .fc-daygrid-day-number,
  .fc .fc-col-header-cell-cushion {
    color: #374151;
    text-decoration: none;
    font-weight: 700;
  }

  .fc .fc-event {
    cursor: pointer;
    border: none !important;
    border-radius: 10px !important;
    padding: 2px 4px;
    font-weight: 700;
    box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
  }

  .select2-container {
    width: 100% !important;
  }

  .xmodal,
  .report-modal {
    position: fixed;
    inset: 0;
    display: none;
    z-index: 9999;
  }

  .report-modal {
    z-index: 10000;
  }

  .xmodal.is-open,
  .report-modal.is-open {
    display: block;
  }

  .xmodal__backdrop,
  .report-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(17, 24, 39, .55);
    backdrop-filter: blur(3px);
  }

  .xmodal__panel,
  .report-modal__panel {
    position: relative;
    width: min(980px, calc(100% - 32px));
    max-height: calc(100vh - 80px);
    margin: 40px auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    border: 1px solid rgba(229, 231, 235, .9);
    display: flex;
    flex-direction: column;
  }

  .report-modal__panel {
    width: min(1120px, calc(100% - 32px));
    border-radius: 22px;
  }

  .xmodal__header,
  .report-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    background: #fafafa;
    border-bottom: 1px solid var(--border);
    color: #111827;
    flex: 0 0 auto;
    gap: 12px;
  }

  .report-modal__header {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    align-items: flex-start;
  }

  .xmodal__title,
  .report-modal__title {
    display: flex;
    gap: 10px;
    align-items: center;
    font-weight: 900;
    font-size: 16px;
  }

  .report-modal__title {
    font-size: 18px;
  }

  .report-modal__sub {
    margin-top: 5px;
    color: rgba(255, 255, 255, .72);
    font-size: 13px;
    font-weight: 700;
  }

  .xmodal__close,
  .report-modal__close {
    border: 1px solid var(--border);
    background: #fff;
    color: #374151;
    font-size: 18px;
    line-height: 1;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    cursor: pointer;
    flex: 0 0 auto;
  }

  .report-modal__close {
    border-color: rgba(255, 255, 255, .18);
    background: rgba(255, 255, 255, .10);
    color: #fff;
    border-radius: 13px;
  }

  .xmodal__close:hover {
    background: #f3f4f6;
  }

  .report-modal__close:hover {
    background: rgba(255, 255, 255, .18);
  }

  .xmodal__body,
  .report-modal__body {
    padding: 18px;
    overflow: auto;
    flex: 1 1 auto;
  }

  .xmodal__footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 18px;
    background: #fafafa;
    border-top: 1px solid var(--border);
    flex: 0 0 auto;
  }

  .xlbl {
    font-weight: 800;
    color: #111827;
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
  }

  .xreq {
    color: #e11d48;
  }

  .xinput {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 12px;
    outline: none;
    background: #fff;
    transition: var(--transition);
    min-height: 42px;
  }

  .xinput:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-light);
  }

  .xbtn {
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 10px 14px;
    cursor: pointer;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .xbtn--primary {
    background: var(--primary);
    color: #fff;
  }

  .xbtn--primary:hover {
    background: var(--primary-hover);
  }

  .xbtn--ghost {
    background: #fff;
    border-color: var(--border);
    color: #111827;
  }

  .xbtn--ghost:hover {
    background: #f3f4f6;
  }

  .report-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .report-card {
    border: 1px solid var(--border);
    border-radius: 18px;
    background: #fff;
    overflow: hidden;
  }

  .report-card__top {
    padding: 14px 15px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .report-card__type {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 999px;
    padding: 6px 10px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 12px;
    font-weight: 900;
  }

  .report-card__meta {
    margin-top: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
  }

  .report-card__pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    padding: 6px 9px;
  }

  .report-card__pill svg,
  .report-card__type svg {
    width: 14px;
    height: 14px;
  }

  .report-card__body {
    padding: 15px;
  }

  .report-text {
    white-space: pre-line;
    line-height: 1.65;
    color: #334155;
    background: #f9fafb;
    border: 1px solid #eef2f7;
    border-radius: 14px;
    padding: 13px;
    font-size: 14px;
    overflow-wrap: anywhere;
  }

  .report-grid {
    margin-top: 12px;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .report-info-box {
    border: 1px solid #eef2f7;
    border-radius: 14px;
    padding: 12px;
    background: #fff;
    min-width: 0;
  }

  .report-info-label {
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: .06em;
  }

  .report-info-value {
    margin-top: 5px;
    color: #334155;
    font-size: 13px;
    font-weight: 750;
    line-height: 1.45;
    overflow-wrap: anywhere;
  }

  .report-empty {
    border: 1px dashed #cbd5e1;
    border-radius: 18px;
    padding: 35px 18px;
    text-align: center;
    background: #f8fafc;
    color: #64748b;
  }

  .report-comments {
    margin-top: 12px;
  }

  .report-comment {
    border: 1px solid #eef2f7;
    background: #fff;
    border-radius: 13px;
    padding: 10px 12px;
    margin-top: 8px;
  }

  .report-comment-text {
    color: #334155;
    font-size: 13px;
    line-height: 1.55;
    overflow-wrap: anywhere;
  }

  .report-comment-date {
    margin-top: 4px;
    color: #94a3b8;
    font-size: 11px;
    font-weight: 700;
  }

  body.modal-open {
    overflow: hidden;
  }

  @media(max-width: 1400px) {
    .oc-item-row {
      grid-template-columns:
        minmax(78px, 90px) minmax(220px, 1.3fr) minmax(180px, 1fr);
    }

    .oc-cell:nth-child(4),
    .oc-cell:nth-child(5) {
      grid-column: span 1;
    }
  }

  @media(max-width: 1200px) {
    .oc-analytics {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .oc-item-row {
      grid-template-columns:
        minmax(78px, 90px) minmax(220px, 1fr);
    }

    .oc-cell:nth-child(3),
    .oc-cell:nth-child(4),
    .oc-cell:nth-child(5) {
      grid-column: 1 / -1;
    }

    .oc-actions {
      justify-content: flex-start;
    }
  }

  @media(max-width: 768px) {
    .oc-wrap {
      padding: 12px;
    }

    .oc-title {
      font-size: 22px;
    }

    .oc-toolbar,
    .oc-card-head,
    .oc-titlebar {
      align-items: stretch;
    }

    .oc-toolbar-left,
    .oc-toolbar-right,
    .oc-tabs,
    .oc-btn,
    .oc-btn-soft {
      width: 100%;
    }

    .oc-tab,
    .oc-btn,
    .oc-btn-soft {
      justify-content: center;
    }

    .oc-item-row {
      grid-template-columns: 1fr;
      padding: 14px;
    }

    .oc-cell-title {
      display: block;
    }

    .oc-date-badge {
      min-height: 72px;
      align-items: flex-start;
      text-align: left;
      padding: 12px;
    }

    .oc-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .oc-profile-link,
    .oc-report-link {
      width: 100%;
    }

    .xmodal__panel,
    .report-modal__panel {
      width: min(100% - 16px, 980px);
      max-height: calc(100vh - 32px);
      margin: 16px auto;
    }

    .report-modal__body,
    .xmodal__body {
      padding: 12px;
    }

    .report-grid {
      grid-template-columns: 1fr;
    }
  }

  @media(max-width: 700px) {
    .oc-analytics {
      grid-template-columns: 1fr;
    }
  }
</style>

@php
  use Carbon\Carbon;

  $appointments = collect($appointments ?? []);

  $cid = $cid ?? request('cid');
  $aid = $aid ?? request('aid');
  $pid = $pid ?? request('pid');

  $totalAppointments = $appointments->count();

  $upcomingCount = $appointments->filter(function ($app) {
    try {
      if (empty($app->start_date)) {
        return false;
      }

      $date = Carbon::parse($app->start_date);

      return $date->isToday() || $date->isFuture();
    } catch (\Throwable $e) {
      return false;
    }
  })->count();

  $doneCount = $appointments->filter(function ($app) {
    try {
      if (empty($app->start_date)) {
        return false;
      }

      $date = Carbon::parse($app->start_date);

      return $date->isPast() && !$date->isToday();
    } catch (\Throwable $e) {
      return false;
    }
  })->count();

  $totalReports = $appointments->sum(function ($app) {
    return $app->reports ? $app->reports->count() : 0;
  });

  $pName = $product_name ?? 'Allgemein';

  $jsonArray = [
    $pName => [
      (int) ($cid ?? 0),
      (int) ($pid ?? 0),
      (string) ($aid ?? 0),
    ],
  ];
@endphp

<div class="oc-wrap" data-calendar-cid="{{ $cid }}" data-calendar-aid="{{ $aid }}" data-calendar-pid="{{ $pid }}">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">
          <i data-feather="calendar"></i>
          <span>TERMINE & PLANUNG</span>
        </div>

        <div class="oc-sub">
          Verwalten Sie alle Termine für diesen Kunden und das ausgewählte Produkt zentral.
        </div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <span class="current">Termine & Planung</span>
        </div>
      </div>

      <div>
        <button type="button" class="oc-btn" onclick="CalendarAppointments.open()">
          <i data-feather="plus"></i>
          Neuer Termin
        </button>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <i data-feather="calendar"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value">{{ $totalAppointments }}</div>
        <div class="oc-stat-sub">Termine insgesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon upcoming">
        <i data-feather="clock"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Anstehend</div>
        <div class="oc-stat-value">{{ $upcomingCount }}</div>
        <div class="oc-stat-sub">Heute und zukünftig</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon done">
        <i data-feather="check-circle"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Vergangen</div>
        <div class="oc-stat-value">{{ $doneCount }}</div>
        <div class="oc-stat-sub">Bereits abgeschlossene Termine</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <i data-feather="file-text"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Reports</div>
        <div class="oc-stat-value">{{ $totalReports }}</div>
        <div class="oc-stat-sub">Gespeicherte Terminberichte</div>
      </div>
    </div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-tabs nav" id="pills-tab" role="tablist">
        <a class="oc-tab active" id="pills-list-tab" data-toggle="pill" href="#pills-list" role="tab"
          aria-controls="pills-list" aria-selected="true">
          <i data-feather="list"></i>
          Liste
        </a>

        <a class="oc-tab" id="pills-calendar-tab" data-toggle="pill" href="#pills-calendar" role="tab"
          aria-controls="pills-calendar" aria-selected="false">
          <i data-feather="calendar"></i>
          Kalender
        </a>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <div class="oc-btn-soft">
        <i data-feather="user"></i>
        Kunde: {{ $cid }}
      </div>

      <div class="oc-btn-soft">
        <i data-feather="package"></i>
        Produkt: {{ $product_name ?? '-' }}
      </div>
    </div>
  </div>

  <div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-list" role="tabpanel" aria-labelledby="pills-list-tab">
      <div class="oc-card">
        <div class="oc-card-head">
          <div>
            <h3 class="oc-card-title">Terminliste</h3>
            <div class="oc-card-sub">Alle gefundenen Termine für den aktuellen Kunden- und Produktkontext.</div>
          </div>
        </div>

        @if($appointments->isEmpty())
          <div class="oc-empty">
            <i data-feather="alert-triangle" style="width:32px;height:32px;margin-bottom:10px;"></i>
            <div>Keine Termine für dieses Produkt gefunden.</div>
          </div>
        @else
          <div class="oc-list">
            @foreach($appointments as $app)
              @php
                try {
                  $startDate = $app->start_date ? Carbon::parse($app->start_date) : null;
                } catch (\Throwable $e) {
                  $startDate = null;
                }

                try {
                  $endDate = $app->end_date ? Carbon::parse($app->end_date) : null;
                } catch (\Throwable $e) {
                  $endDate = null;
                }

                try {
                  $startTime = $app->start_time ? Carbon::parse($app->start_time)->format('H:i') : null;
                } catch (\Throwable $e) {
                  $startTime = $app->start_time ?? null;
                }

                try {
                  $endTime = $app->end_time ? Carbon::parse($app->end_time)->format('H:i') : null;
                } catch (\Throwable $e) {
                  $endTime = $app->end_time ?? null;
                }

                $priority = strtolower($app->priority ?? 'normal');

                $priorityClass = match ($priority) {
                  'high' => 'priority-high',
                  'low' => 'priority-low',
                  default => 'priority-normal',
                };

                $priorityLabel = match ($priority) {
                  'high' => 'Hoch',
                  'low' => 'Niedrig',
                  default => 'Normal',
                };

                $profileUrl = url('customer/appointments/' . $app->id);
                $reports = $app->reports ?? collect();
                $reportCount = $reports->count();
              @endphp

              <div class="oc-item" data-appointment-profile-url="{{ $profileUrl }}">
                <div class="oc-item-row">
                  <div class="oc-cell">
                    <div class="oc-cell-title">Datum</div>

                    <div class="oc-date-badge">
                      <div class="oc-date-day">{{ $startDate ? $startDate->format('d') : '--' }}</div>
                      <div class="oc-date-month">{{ $startDate ? $startDate->translatedFormat('M') : '---' }}</div>
                    </div>
                  </div>

                  <div class="oc-cell">
                    <div class="oc-cell-title">Termin</div>

                    <div class="oc-main">
                      <div class="oc-ttl">
                        <a href="{{ $profileUrl }}" class="oc-title-link" title="Terminprofil öffnen"
                          onclick="event.stopPropagation();">
                          {{ $app->name ?: 'Termin #' . $app->id }}
                        </a>

                        @if($app->appointment_type)
                          <span class="oc-badge type">{{ $app->appointment_type }}</span>
                        @endif

                        <span class="oc-badge {{ $priorityClass }}">{{ $priorityLabel }}</span>
                      </div>

                      <div class="oc-subt">
                        @if($startDate)
                          {{ $startDate->format('d.m.Y') }}
                        @endif

                        @if($startTime)
                          • {{ $startTime }}
                        @endif

                        @if($endDate || $endTime)
                          &nbsp;–&nbsp;

                          @if($endDate)
                            {{ $endDate->format('d.m.Y') }}
                          @elseif($startDate)
                            {{ $startDate->format('d.m.Y') }}
                          @endif

                          @if($endTime)
                            {{ $endTime }}
                          @endif
                        @endif
                      </div>

                      @if($app->note)
                        <div class="oc-note">
                          <i data-feather="file-text"></i>
                          {!! nl2br(e($app->note)) !!}
                        </div>
                      @endif
                    </div>
                  </div>

                  <div class="oc-cell">
                    <div class="oc-cell-title">Details</div>

                    <div class="oc-meta-stack">
                      @if(!empty($app->execution_type))
                        <div class="oc-meta-pill">
                          <i data-feather="map-pin"></i>
                          <span>
                            @if($app->execution_type === 'vor_ort')
                              Vor Ort
                            @elseif($app->execution_type === 'online')
                              Online
                            @elseif($app->execution_type === 'telefon')
                              Telefon
                            @else
                              {{ $app->execution_type }}
                            @endif
                          </span>
                        </div>
                      @endif

                      @if(!empty($app->public))
                        <div class="oc-meta-pill">
                          <i data-feather="globe"></i>
                          <span>Öffentlich</span>
                        </div>
                      @endif

                      <div class="oc-meta-pill">
                        <i data-feather="{{ $reportCount > 0 ? 'file-text' : 'file-x' }}"></i>
                        <span>{{ $reportCount > 0 ? $reportCount . ' Report(s) vorhanden' : 'Keine Reports' }}</span>
                      </div>

                      @if(!empty($app->problem_id))
                        <a href="{{ url('problem/profile/' . $app->problem_id) }}" class="oc-meta-pill"
                          style="text-decoration:none;" title="Ticket öffnen" onclick="event.stopPropagation();">
                          <i data-feather="alert-circle"></i>
                          <span>Ticket #{{ $app->problem_id }}</span>
                        </a>
                      @endif
                    </div>
                  </div>

                  <div class="oc-cell">
                    <div class="oc-cell-title">Status</div>

                    <div class="oc-meta-stack">
                      <div class="oc-meta-pill">
                        <i data-feather="calendar"></i>
                        <span>
                          @if($startDate && $startDate->isToday())
                            Heute
                          @elseif($startDate && $startDate->isFuture())
                            Geplant
                          @elseif($startDate)
                            Vergangen
                          @else
                            Ohne Datum
                          @endif
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="oc-cell">
                    <div class="oc-cell-title">Aktionen</div>

                    <div class="oc-actions">
                      <button type="button" class="oc-report-link"
                        onclick="event.preventDefault(); event.stopPropagation(); CalendarReports.open({{ $app->id }});"
                        title="Reports anzeigen">
                        <i data-feather="file-text"></i>
                        <span>Reports</span>
                        <span class="oc-report-count">{{ $reportCount }}</span>
                      </button>

                      <a href="{{ $profileUrl }}" class="oc-profile-link" title="Terminprofil öffnen"
                        onclick="event.stopPropagation();">
                        <i data-feather="external-link"></i>
                        <span>Profil öffnen</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="tab-pane fade" id="pills-calendar" role="tabpanel" aria-labelledby="pills-calendar-tab">
      <div class="oc-card">
        <div class="oc-card-head">
          <div>
            <h3 class="oc-card-title">Kalenderansicht</h3>
            <div class="oc-card-sub">Monats-, Wochen- oder Tagesansicht Ihrer Termine.</div>
          </div>
        </div>

        <div class="oc-calendar-wrap">
          <div id="fullCalendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@foreach($appointments as $app)
  @php
    $reports = $app->reports ?? collect();
    $appointmentTitle = $app->name ?: 'Termin #' . $app->id;
  @endphp

  <div id="appointmentReportsModal-{{ $app->id }}" class="report-modal" aria-hidden="true">
    <div class="report-modal__backdrop" onclick="CalendarReports.close({{ $app->id }})"></div>

    <div class="report-modal__panel" role="dialog" aria-modal="true">
      <div class="report-modal__header">
        <div>
          <div class="report-modal__title">
            <i data-feather="file-text"></i>
            <span>Appointment Reports</span>
          </div>

          <div class="report-modal__sub">
            {{ $appointmentTitle }} · {{ $reports->count() }} Report(s)
          </div>
        </div>

        <button type="button" class="report-modal__close" onclick="CalendarReports.close({{ $app->id }})"
          aria-label="Close">
          ✕
        </button>
      </div>

      <div class="report-modal__body">
        @if($reports->isEmpty())
          <div class="report-empty">
            <i data-feather="file-x" style="width:32px;height:32px;margin-bottom:10px;"></i>
            <div style="font-weight:900;color:#334155;">Keine Reports vorhanden</div>
            <div style="margin-top:4px;font-size:13px;">Für diesen Termin wurde noch kein Report gespeichert.</div>
          </div>
        @else
          <div class="report-list">
            @foreach($reports as $report)
              @php
                $author = $report->author ?: $report->reporter ?: $report->employee;

                $authorName = trim(($author->name ?? '') . ' ' . ($author->lastname ?? ''));
                $ownerName = trim(($report->employee->name ?? '') . ' ' . ($report->employee->lastname ?? ''));

                try {
                  $reportDate = $report->report_date ? Carbon::parse($report->report_date)->format('d.m.Y') : null;
                } catch (\Throwable $e) {
                  $reportDate = $report->report_date ?? null;
                }

                try {
                  $dueDate = $report->due_date ? Carbon::parse($report->due_date)->format('d.m.Y') : null;
                } catch (\Throwable $e) {
                  $dueDate = $report->due_date ?? null;
                }

                $commentItems = $report->comment_items ?? [];

                $likesCount = $report->likes_count ?? ($report->likes ?? 0);
                $dislikesCount = $report->dislikes_count ?? ($report->dislikes ?? 0);
              @endphp

              <div class="report-card">
                <div class="report-card__top">
                  <div>
                    <div class="report-card__type">
                      <i data-feather="clipboard"></i>
                      <span>{{ $report->type ?: 'Report' }}</span>
                    </div>

                    <div class="report-card__meta">
                      <span class="report-card__pill">
                        <i data-feather="user"></i>
                        {{ $authorName ?: 'Unbekannt' }}
                      </span>

                      @if($reportDate)
                        <span class="report-card__pill">
                          <i data-feather="calendar"></i>
                          {{ $reportDate }}
                        </span>
                      @endif

                      <span class="report-card__pill">
                        <i data-feather="thumbs-up"></i>
                        {{ $likesCount }}
                      </span>

                      <span class="report-card__pill">
                        <i data-feather="thumbs-down"></i>
                        {{ $dislikesCount }}
                      </span>
                    </div>
                  </div>

                  <div style="font-size:12px;color:#64748b;font-weight:800;">
                    Report #{{ $report->id }}
                  </div>
                </div>

                <div class="report-card__body">
                  <div class="report-text">{{ $report->report ?: 'Kein Report-Text vorhanden.' }}</div>

                  <div class="report-grid">
                    <div class="report-info-box">
                      <div class="report-info-label">Gehört zu Mitarbeiter</div>
                      <div class="report-info-value">{{ $ownerName ?: '-' }}</div>
                    </div>

                    <div class="report-info-box">
                      <div class="report-info-label">Fällig am</div>
                      <div class="report-info-value">{{ $dueDate ?: '-' }}</div>
                    </div>

                    <div class="report-info-box">
                      <div class="report-info-label">Nächster Schritt</div>
                      <div class="report-info-value">{{ $report->next_step ?: '-' }}</div>
                    </div>

                    <div class="report-info-box">
                      <div class="report-info-label">Erstellt</div>
                      <div class="report-info-value">
                        {{ optional($report->created_at)->format('d.m.Y H:i') ?: '-' }}
                      </div>
                    </div>
                  </div>

                  @if(!empty($commentItems))
                    <div class="report-comments">
                      <div class="report-info-label">Kommentare</div>

                      @foreach($commentItems as $comment)
                        <div class="report-comment">
                          <div class="report-comment-text">
                            {{ $comment['text'] ?? '' }}
                          </div>

                          @if(!empty($comment['created_at']))
                            <div class="report-comment-date">
                              {{ Carbon::parse($comment['created_at'])->format('d.m.Y H:i') }}
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
@endforeach

<div id="createAppModal" class="xmodal" aria-hidden="true">
  <div class="xmodal__backdrop" data-xmodal-close></div>

  <div class="xmodal__panel" role="dialog" aria-modal="true" aria-labelledby="xmodalTitle">
    <div class="xmodal__header">
      <div class="xmodal__title" id="xmodalTitle">
        <i data-feather="calendar"></i>
        <span>Neuen Termin erstellen</span>
      </div>

      <button type="button" class="xmodal__close" aria-label="Close" data-xmodal-close>
        ✕
      </button>
    </div>

    <form action="{{ route('main_appointments.customer-modal') }}" method="POST" id="calApp_form">
      @csrf

      <input type="hidden" name="customer_id" value="{{ $cid }}">
      <input type="hidden" name="alternative_id" value="{{ $aid }}">
      <input type="hidden" name="public" value="1">
      <input type="hidden" name="type" value="appointment">
      <input type="hidden" name="contact_mode" value="new">
      <input type="hidden" name="products" value='@json($jsonArray)'>

      <div class="xmodal__body">
        <div class="row" style="gap:12px;">
          <div class="col-12">
            <label class="xlbl">
              Titel / Betreff <span class="xreq">*</span>
            </label>

            <input type="text" name="name" class="xinput" required placeholder="z.B. Vor-Ort Begehung">
          </div>

          <div class="col-12">
            <label class="xlbl">
              Mitarbeiter <span class="xreq">*</span>
            </label>

            <select name="employee_id[]" id="calApp_employee_select" class="xinput" multiple required
              style="width:100%">
              @foreach(($calenderEmployees ?? []) as $e)
                <option value="{{ $e->emp_id }}">
                  {{ $e->name }} {{ $e->lastname }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <hr>
          </div>

          <div class="col-md-3 col-12">
            <label class="xlbl">
              Startdatum <span class="xreq">*</span>
            </label>

            <input type="date" name="start_date" id="calApp_start_date" class="xinput" required>
          </div>

          <div class="col-md-3 col-12">
            <label class="xlbl">Startzeit</label>
            <input type="time" name="start_time" class="xinput" value="09:00">
          </div>

          <div class="col-md-3 col-12">
            <label class="xlbl">Enddatum</label>
            <input type="date" name="end_date" id="calApp_end_date" class="xinput">
          </div>

          <div class="col-md-3 col-12">
            <label class="xlbl">Endzeit</label>
            <input type="time" name="end_time" class="xinput" value="10:00">
          </div>

          <div class="col-12">
            <hr>
          </div>

          <div class="col-md-6 col-12">
            <label class="xlbl">Priorität</label>

            <select name="priority" class="xinput">
              <option value="normal" selected>🔵 Normal</option>
              <option value="high">🔴 Hoch</option>
              <option value="low">🟢 Niedrig</option>
            </select>
          </div>

          <div class="col-md-6 col-12">
            <label class="xlbl">Durchführung</label>

            <select name="execution_type" class="xinput">
              <option value="vor_ort" selected>📍 Vor Ort</option>
              <option value="online">💻 Online</option>
              <option value="telefon">📞 Telefon</option>
            </select>
          </div>

          <div class="col-12">
            <label class="xlbl">Notiz</label>
            <textarea name="note" class="xinput" rows="3" placeholder="Zusätzliche Informationen..."></textarea>
          </div>
        </div>
      </div>

      <div class="xmodal__footer">
        <button type="button" class="xbtn xbtn--ghost" data-xmodal-close>
          Abbrechen
        </button>

        <button type="submit" class="xbtn xbtn--primary">
          <i data-feather="save"></i>
          Termin speichern
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  (function () {
    "use strict";

    const SELECT_ID = '#calApp_employee_select';
    const MODAL_ID = '#createAppModal';
    const FORM_ID = '#calApp_form';

    const qs = (selector, root = document) => root.querySelector(selector);
    const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    function getContext() {
      const wrap = qs('.oc-wrap');

      return {
        cid: wrap?.dataset?.calendarCid || "{{ $cid }}",
        aid: wrap?.dataset?.calendarAid || "{{ $aid }}",
        pid: wrap?.dataset?.calendarPid || "{{ $pid }}"
      };
    }

    function replaceIcons() {
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    }

    function openModal(dateStr) {
      const modal = qs(MODAL_ID);
      if (!modal) return;

      const form = qs(FORM_ID);
      if (form) form.reset();

      const today = new Date().toISOString().split('T')[0];
      const targetDate = dateStr || today;

      const startDateInput = qs('#calApp_start_date');
      const endDateInput = qs('#calApp_end_date');

      if (startDateInput) startDateInput.value = targetDate;
      if (endDateInput) endDateInput.value = targetDate;

      if (window.jQuery && jQuery(SELECT_ID).length) {
        jQuery(SELECT_ID).val(null).trigger('change');
      }

      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');

      replaceIcons();
    }

    function closeModal() {
      const modal = qs(MODAL_ID);
      if (!modal) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');

      if (!document.querySelector('.report-modal.is-open')) {
        document.body.classList.remove('modal-open');
      }
    }

    function bindModalClose() {
      const modal = qs(MODAL_ID);
      if (!modal || modal.dataset.closeBound === '1') return;

      modal.dataset.closeBound = '1';

      modal.addEventListener('click', function (event) {
        if (event.target.matches('[data-xmodal-close]')) {
          closeModal();
        }
      });
    }

    function initEmployeeSelect2() {
      if (!window.jQuery) return;

      const $select = jQuery(SELECT_ID);
      const $modal = jQuery(MODAL_ID);

      if (!$select.length || !$modal.length) return;

      if ($select.data('select2')) {
        $select.select2('destroy');
      }

      $select.select2({
        dropdownParent: $modal,
        placeholder: 'Mitarbeiter suchen...',
        allowClear: true,
        width: '100%'
      });
    }

    function bindProfileCardClicks() {
      qsa('[data-appointment-profile-url]').forEach(function (item) {
        if (item.dataset.profileBound === '1') return;

        item.dataset.profileBound = '1';

        item.addEventListener('click', function (event) {
          const ignored = event.target.closest(
            'a, button, input, select, textarea, label, .select2-container, .report-modal, .xmodal'
          );

          if (ignored) return;

          const url = item.dataset.appointmentProfileUrl;

          if (url) {
            window.location.href = url;
          }
        });
      });
    }

    function initAjaxForm(calendarInstance) {
      const form = qs(FORM_ID);
      if (!form || form.dataset.bound === '1') return;

      form.dataset.bound = '1';

      form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonHtml = submitButton ? submitButton.innerHTML : '';

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
            Speichern...
          `;
        }

        try {
          const formData = new FormData(form);

          const response = await fetch(form.action, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
          });

          let data = {};

          try {
            data = await response.json();
          } catch (jsonError) {
            data = {};
          }

          if (!response.ok || !(data.success || data.id)) {
            throw new Error(data.message || 'Termin konnte nicht gespeichert werden.');
          }

          closeModal();

          if (typeof Swal !== 'undefined') {
            Swal.fire('Gespeichert!', 'Termin wurde erfolgreich angelegt.', 'success');
          }

          if (calendarInstance && typeof calendarInstance.refetchEvents === 'function') {
            calendarInstance.refetchEvents();
          }

          const context = getContext();

          if (typeof window.loadCalendar === 'function') {
            window.loadCalendar(context.cid, context.aid, context.pid);
          }
        } catch (error) {
          console.error(error);

          if (typeof Swal !== 'undefined') {
            Swal.fire('Fehler', error.message || 'Termin konnte nicht gespeichert werden.', 'error');
          } else {
            alert(error.message || 'Termin konnte nicht gespeichert werden.');
          }
        } finally {
          if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
          }
        }
      });
    }

    function initFullCalendarFromPartial() {
      const el = qs('#fullCalendar');
      const context = getContext();

      if (!el || typeof FullCalendar === 'undefined') {
        return null;
      }

      if (el.dataset.initialized === '1') {
        return null;
      }

      el.dataset.initialized = '1';

      const calendar = new FullCalendar.Calendar(el, {
        locale: 'de',
        initialView: 'dayGridMonth',

        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },

        buttonText: {
          today: 'Heute',
          month: 'Monat',
          week: 'Woche',
          day: 'Tag',
          list: 'Liste'
        },

        height: 'auto',
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        nowIndicator: true,

        events: {
          url: '/ajax/calendar-events',
          method: 'GET',
          extraParams: {
            customer_id: context.cid,
            cid: context.cid,
            aid: context.aid,
            pid: context.pid
          },
          failure: function () {
            if (typeof Swal !== 'undefined') {
              Swal.fire('Fehler', 'Fehler beim Laden der Termine.', 'error');
            } else {
              alert('Fehler beim Laden der Termine!');
            }
          }
        },

        dateClick: function (info) {
          openModal(info.dateStr);
        },

        eventClick: function (info) {
          info.jsEvent.preventDefault();

          const eventId =
            info.event.id ||
            info.event.extendedProps.appointment_id ||
            info.event.extendedProps.appointmentId ||
            null;

          if (!eventId) {
            if (typeof Swal !== 'undefined') {
              Swal.fire('Fehler', 'Keine Termin-ID gefunden.', 'error');
            } else {
              alert('Keine Termin-ID gefunden.');
            }

            return;
          }

          window.location.href = `/customer/appointments/${eventId}`;
        },

        eventDidMount: function (info) {
          info.el.setAttribute('title', 'Terminprofil öffnen');
        }
      });

      calendar.render();

      setTimeout(function () {
        calendar.updateSize();
      }, 150);

      return calendar;
    }

    function initCalendarAppointmentsPartial() {
      const calendar = initFullCalendarFromPartial();

      bindModalClose();
      initEmployeeSelect2();
      bindProfileCardClicks();
      initAjaxForm(calendar);
      replaceIcons();

      return calendar;
    }

    window.CalendarAppointments = {
      open: openModal,
      close: closeModal,
      initAfterPartialLoad: function (calendarInstance) {
        bindModalClose();
        initEmployeeSelect2();
        bindProfileCardClicks();
        initAjaxForm(calendarInstance);
        replaceIcons();
      }
    };

    window.CalendarReports = {
      open: function (appointmentId) {
        const modal = document.getElementById(`appointmentReportsModal-${appointmentId}`);
        if (!modal) return;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        replaceIcons();
      },

      close: function (appointmentId) {
        const modal = document.getElementById(`appointmentReportsModal-${appointmentId}`);
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        if (!document.querySelector('.xmodal.is-open')) {
          document.body.classList.remove('modal-open');
        }
      }
    };

    window.openAppointmentModal = function (dateStr) {
      openModal(dateStr);
    };

    window.initFullCalendar = window.initFullCalendar || function () {
      return initFullCalendarFromPartial();
    };

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') return;

      const createModal = qs(MODAL_ID);

      if (createModal && createModal.classList.contains('is-open')) {
        closeModal();
      }

      qsa('.report-modal.is-open').forEach(function (modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
      });

      document.body.classList.remove('modal-open');
    });

    initCalendarAppointmentsPartial();
  })();
</script>



                                <script>
                                    function initFullCalendar(customerId) {
                                        const el = document.getElementById('fullCalendar');

                                        if (!el || typeof FullCalendar === 'undefined') {
                                            return null;
                                        }

                                        if (el.dataset.initialized === '1') {
                                            return null;
                                        }

                                        el.dataset.initialized = '1';

                                        const calendar = new FullCalendar.Calendar(el, {
                                            locale: 'de',
                                            initialView: 'dayGridMonth',

                                            headerToolbar: {
                                                left: 'prev,next today',
                                                center: 'title',
                                                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                                            },

                                            buttonText: {
                                                today: 'Heute',
                                                month: 'Monat',
                                                week: 'Woche',
                                                day: 'Tag',
                                                list: 'Liste'
                                            },

                                            height: 'auto',
                                            navLinks: true,
                                            editable: false,
                                            dayMaxEvents: true,
                                            nowIndicator: true,

                                            events: {
                                                url: '/ajax/calendar-events',
                                                method: 'GET',
                                                extraParams: {
                                                    customer_id: customerId
                                                },
                                                failure: function () {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire('Fehler', 'Fehler beim Laden der Termine.', 'error');
                                                    } else {
                                                        alert('Fehler beim Laden der Termine!');
                                                    }
                                                }
                                            },

                                            dateClick: function (info) {
                                                if (window.CalendarAppointments) {
                                                    window.CalendarAppointments.open(info.dateStr);
                                                }
                                            },

                                            eventClick: function (info) {
                                                info.jsEvent.preventDefault();

                                                const eventId = info.event.id || info.event.extendedProps.appointment_id;

                                                if (!eventId) {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire('Fehler', 'Keine Termin-ID gefunden.', 'error');
                                                    } else {
                                                        alert('Keine Termin-ID gefunden.');
                                                    }

                                                    return;
                                                }

                                                window.location.href = `/customer/appointments/${eventId}`;
                                            },

                                            eventDidMount: function (info) {
                                                info.el.setAttribute('title', 'Terminprofil öffnen');

                                                const eventId = info.event.id || info.event.extendedProps.appointment_id;

                                                if (eventId) {
                                                    info.el.dataset.profileUrl = `/customer/appointments/${eventId}`;
                                                }
                                            }
                                        });

                                        calendar.render();

                                        setTimeout(() => {
                                            calendar.updateSize();
                                        }, 150);

                                        return calendar;
                                    }
                                </script>

                                <script>
                                    /**
                                    * Backward compatibility for old onclick="openAppointmentModal()"
                                    */
                                    window.openAppointmentModal = function (dateStr) {
                                        if (window.CalendarAppointments) {
                                            window.CalendarAppointments.open(dateStr);
                                        }
                                    };
                                </script>

                                <script>
                                    (function () {
                                        "use strict";

                                        const SELECT_ID = '#calApp_employee_select';
                                        const MODAL_ID = '#createAppModal';
                                        const FORM_ID = '#calApp_form';

                                        const qs = (selector, root = document) => root.querySelector(selector);
                                        const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

                                        function openModal(dateStr) {
                                            const modal = qs(MODAL_ID);

                                            if (!modal) return;

                                            const form = qs(FORM_ID);

                                            if (form) {
                                                form.reset();
                                            }

                                            const today = new Date().toISOString().split('T')[0];
                                            const targetDate = dateStr || today;

                                            const startDateInput = qs('#calApp_start_date');
                                            const endDateInput = qs('#calApp_end_date');

                                            if (startDateInput) {
                                                startDateInput.value = targetDate;
                                            }

                                            if (endDateInput) {
                                                endDateInput.value = targetDate;
                                            }

                                            if (window.jQuery && jQuery(SELECT_ID).length) {
                                                jQuery(SELECT_ID).val(null).trigger('change');
                                            }

                                            modal.classList.add('is-open');
                                            modal.setAttribute('aria-hidden', 'false');
                                            document.body.classList.add('modal-open');

                                            if (typeof feather !== 'undefined') {
                                                feather.replace();
                                            }
                                        }

                                        function closeModal() {
                                            const modal = qs(MODAL_ID);

                                            if (!modal) return;

                                            modal.classList.remove('is-open');
                                            modal.setAttribute('aria-hidden', 'true');
                                            document.body.classList.remove('modal-open');
                                        }

                                        function bindModalClose() {
                                            const modal = qs(MODAL_ID);

                                            if (!modal || modal.dataset.closeBound === '1') return;

                                            modal.dataset.closeBound = '1';

                                            modal.addEventListener('click', function (event) {
                                                if (event.target.matches('[data-xmodal-close]')) {
                                                    closeModal();
                                                }
                                            });

                                            document.addEventListener('keydown', function (event) {
                                                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                                                    closeModal();
                                                }
                                            });
                                        }

                                        function formatEmployeeAvatar(employee) {
                                            if (!employee.id) {
                                                return employee.text;
                                            }

                                            let imageUrl = "{{ asset('images/gender/male.png') }}";

                                            if (employee.image && employee.image !== '') {
                                                imageUrl = "{{ asset('images/employee') }}/" + employee.image;
                                            }

                                            return jQuery(`
                                                <div style="display:flex;align-items:center;gap:8px;">
                                                    <img
                                                        src="${imageUrl}"
                                                        style="width:24px;height:24px;object-fit:cover;border-radius:999px;border:1px solid #ddd;"
                                                        alt=""
                                                    >
                                                    <span>${employee.text || ''}</span>
                                                </div>
                                            `);
                                        }

                                        function initEmployeeSelect2() {
                                            if (!window.jQuery) return;

                                            const $select = jQuery(SELECT_ID);
                                            const $modal = jQuery(MODAL_ID);

                                            if (!$select.length || !$modal.length) return;

                                            if ($select.data('select2')) {
                                                $select.select2('destroy');
                                            }

                                            $select.select2({
                                                dropdownParent: $modal,
                                                placeholder: 'Mitarbeiter suchen...',
                                                allowClear: true,
                                                width: '100%'
                                            });
                                        }

                                        function initAjaxForm(calendarInstance) {
                                            const form = qs(FORM_ID);

                                            if (!form || form.dataset.bound === '1') return;

                                            form.dataset.bound = '1';

                                            form.addEventListener('submit', async function (event) {
                                                event.preventDefault();

                                                const submitButton = form.querySelector('button[type="submit"]');
                                                const originalButtonHtml = submitButton ? submitButton.innerHTML : '';

                                                if (submitButton) {
                                                    submitButton.disabled = true;
                                                    submitButton.innerHTML = `
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                        Speichern...
                                                    `;
                                                }

                                                try {
                                                    const formData = new FormData(form);

                                                    const response = await fetch(form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                                        },
                                                        body: formData
                                                    });

                                                    let data = {};

                                                    try {
                                                        data = await response.json();
                                                    } catch (jsonError) {
                                                        data = {};
                                                    }

                                                    if (!response.ok || !(data.success || data.id)) {
                                                        throw new Error(data.message || 'Termin konnte nicht gespeichert werden.');
                                                    }

                                                    closeModal();

                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire('Gespeichert!', 'Termin wurde erfolgreich angelegt.', 'success');
                                                    }

                                                    if (calendarInstance && typeof calendarInstance.refetchEvents === 'function') {
                                                        calendarInstance.refetchEvents();
                                                    }

                                                    /**
                                                    * Optional:
                                                    * Reload the full calendar partial after save so the list also updates.
                                                    */
                                                    const cid = form.querySelector('input[name="customer_id"]')?.value;
                                                    const aid = form.querySelector('input[name="alternative_id"]')?.value;
                                                    const pid = "{{ $pid ?? '' }}";

                                                    if (cid && aid && pid && typeof loadCalendar === 'function') {
                                                        loadCalendar(cid, aid, pid);
                                                    }

                                                } catch (error) {
                                                    console.error(error);

                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire('Fehler', error.message || 'Termin konnte nicht gespeichert werden.', 'error');
                                                    } else {
                                                        alert(error.message || 'Termin konnte nicht gespeichert werden.');
                                                    }
                                                } finally {
                                                    if (submitButton) {
                                                        submitButton.disabled = false;
                                                        submitButton.innerHTML = originalButtonHtml;
                                                    }
                                                }
                                            });
                                        }

                                        function bindProfileLinks() {
                                            qsa('[data-appointment-profile-url]').forEach(function (item) {
                                                if (item.dataset.profileBound === '1') return;

                                                item.dataset.profileBound = '1';

                                                item.addEventListener('click', function (event) {
                                                    const ignored = event.target.closest('a, button, input, select, textarea, label');

                                                    if (ignored) return;

                                                    const url = item.dataset.appointmentProfileUrl;

                                                    if (url) {
                                                        window.location.href = url;
                                                    }
                                                });
                                            });
                                        }

                                        window.CalendarAppointments = {
                                            open: openModal,
                                            close: closeModal,

                                            initAfterPartialLoad: function (calendarInstance) {
                                                bindModalClose();
                                                initEmployeeSelect2();
                                                initAjaxForm(calendarInstance);
                                                bindProfileLinks();

                                                if (typeof feather !== 'undefined') {
                                                    feather.replace();
                                                }
                                            }
                                        };
                                    })();
                                </script>
