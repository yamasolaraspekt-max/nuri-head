<style>
:root {
    --gt-bg: #f3f4f6;
    --gt-card: #ffffff;
    --gt-text: #1f2937;
    --gt-muted: #6b7280;
    --gt-border: #e5e7eb;
    --gt-primary: #93c21c;
    --gt-primary-hover: #7baa18;
    --gt-primary-light: #f4fae7;
    --gt-blue: #74b2d4;
    --gt-blue-light: #eff6ff;
    --gt-success: #10b981;
    --gt-success-light: #ecfdf5;
    --gt-warning: #f59e0b;
    --gt-warning-light: #fffbeb;
    --gt-danger: #ef4444;
    --gt-danger-light: #fef2f2;
    --gt-gray: #6b7280;
    --gt-gray-light: #f3f4f6;
    --gt-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
    --gt-shadow: 0 18px 45px -20px rgb(15 23 42 / .35), 0 8px 18px -10px rgb(15 23 42 / .18);
    --gt-radius: 14px;
    --gt-transition: all .2s ease-in-out;
}

.gt-wrap { font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--gt-text); }
.gt-titlebar { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.gt-title { font-size:26px; font-weight:900; letter-spacing:-.025em; color:#111827; }
.gt-sub { font-size:14px; color:var(--gt-muted); margin-top:4px; }
.gt-breadcrumb { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:10px; font-size:13px; color:var(--gt-muted); }
.gt-breadcrumb a { color:var(--gt-muted); text-decoration:none; font-weight:800; }
.gt-breadcrumb .current { color:#111827; font-weight:900; }
.gt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }

.gt-btn, .gt-btn-soft, .gt-btn-ic { border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; transition:var(--gt-transition); white-space:nowrap; }
.gt-btn { background:var(--gt-primary); color:#fff; padding:10px 16px; border-radius:10px; font-weight:900; }
.gt-btn:hover { background:var(--gt-primary-hover); color:#fff; text-decoration:none; }
.gt-btn.danger { background:var(--gt-danger); }
.gt-btn.blue { background:var(--gt-blue); }
.gt-btn-soft { background:#fff; color:var(--gt-text); border:1px solid var(--gt-border); padding:10px 14px; border-radius:10px; font-weight:800; box-shadow:var(--gt-shadow-sm); }
.gt-btn-soft:hover { background:#f9fafb; color:var(--gt-text); text-decoration:none; }
.gt-btn-ic { width:36px; height:36px; border-radius:10px; border:1px solid var(--gt-border); background:#fff; color:var(--gt-muted); }
.gt-btn-ic:hover { background:#f9fafb; color:#111827; }
.gt-btn-ic.primary { color:var(--gt-primary); border-color:#d9ef9d; background:var(--gt-primary-light); }
.gt-btn-ic.danger { color:var(--gt-danger); border-color:rgba(239,68,68,.18); background:var(--gt-danger-light); }

.gt-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:18px; }
.gt-stat { background:#fff; border:1px solid var(--gt-border); border-radius:16px; padding:16px; box-shadow:var(--gt-shadow-sm); display:flex; align-items:center; gap:12px; min-height:92px; }
.gt-stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
.gt-stat-icon.open { background:var(--gt-blue-light); color:var(--gt-blue); }
.gt-stat-icon.progress { background:var(--gt-warning-light); color:#d97706; }
.gt-stat-icon.done { background:var(--gt-success-light); color:var(--gt-success); }
.gt-stat-icon.archive { background:var(--gt-gray-light); color:var(--gt-gray); }
.gt-stat-label { font-size:11px; font-weight:900; color:var(--gt-muted); text-transform:uppercase; letter-spacing:.06em; }
.gt-stat-value { font-size:24px; font-weight:900; color:#111827; line-height:1.1; margin-top:4px; }
.gt-stat-sub { font-size:12px; color:var(--gt-muted); margin-top:4px; }

.gt-toolbar { background:#fff; border:1px solid var(--gt-border); border-radius:var(--gt-radius); padding:14px 16px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; justify-content:space-between; margin-bottom:16px; box-shadow:var(--gt-shadow-sm); }
.gt-toolbar-left, .gt-toolbar-right { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
.gt-filter { display:flex; flex-direction:column; gap:6px; min-width:170px; }
.gt-filter.search { min-width:280px; flex:1; }
.gt-filter-label { font-size:11px; font-weight:900; color:var(--gt-muted); text-transform:uppercase; letter-spacing:.06em; }
.gt-input, .gt-select, .gt-textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--gt-border); background:#fff; font-size:14px; outline:none; transition:var(--gt-transition); }
.gt-textarea { min-height:104px; resize:vertical; }
.gt-input:focus, .gt-select:focus, .gt-textarea:focus { border-color:var(--gt-primary); box-shadow:0 0 0 3px var(--gt-primary-light); }

.gt-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
.gt-tab { border:1px solid var(--gt-border); background:#fff; border-radius:999px; padding:9px 14px; font-weight:900; color:var(--gt-muted); cursor:pointer; box-shadow:var(--gt-shadow-sm); display:inline-flex; align-items:center; gap:7px; }
.gt-tab.active { background:var(--gt-primary-light); color:#4d7c0f; border-color:#d9ef9d; }
.gt-tab-count { min-width:22px; height:22px; padding:0 7px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; font-size:11px; font-weight:950; }

.gt-view { display:none; }
.gt-view.active { display:block; }
.gt-board { display:grid; grid-template-columns:repeat(4,minmax(300px,1fr)); gap:14px; overflow-x:auto; padding-bottom:6px; }
.gt-column { background:#fff; border:1px solid var(--gt-border); border-radius:16px; box-shadow:var(--gt-shadow-sm); min-height:620px; display:flex; flex-direction:column; min-width:300px; }
.gt-column-h { padding:14px; border-bottom:1px solid var(--gt-border); display:flex; align-items:flex-start; justify-content:space-between; gap:10px; background:#fafafa; }
.gt-column-title { font-weight:900; color:#111827; }
.gt-column-hint { font-size:12px; color:var(--gt-muted); margin-top:3px; }
.gt-count { background:var(--gt-primary-light); color:#4d7c0f; border-radius:999px; padding:4px 9px; font-size:12px; font-weight:900; display:inline-flex; align-items:center; justify-content:center; min-width:24px; }
.gt-dropzone { padding:12px; display:flex; flex-direction:column; gap:12px; min-height:540px; flex:1; }
.gt-dropzone.drag-over { background:var(--gt-primary-light); outline:2px dashed var(--gt-primary); outline-offset:-8px; }

.gt-task { position:relative; background:#fff; border:1px solid var(--gt-border); border-radius:16px; padding:13px; box-shadow:var(--gt-shadow-sm); cursor:grab; transition:var(--gt-transition); overflow:hidden; }
.gt-task:hover { border-color:var(--gt-primary); box-shadow:var(--gt-shadow); }
.gt-task.dragging { opacity:.55; transform:rotate(1deg); }
.gt-task.is-overdue { border-color:rgba(239,68,68,.65); animation:gtOverduePulse 1.8s ease-in-out infinite; }
.gt-task.is-overdue:before { content:""; position:absolute; inset:0 0 auto; height:4px; background:linear-gradient(90deg,#ef4444,#f59e0b,#ef4444); }
@keyframes gtOverduePulse { 0%,100% { box-shadow:var(--gt-shadow-sm); } 50% { box-shadow:0 0 0 4px rgba(239,68,68,.12), var(--gt-shadow); } }
.gt-task-top { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
.gt-task-title { font-weight:950; color:#111827; font-size:14px; line-height:1.35; }
.gt-task-desc { font-size:12px; color:var(--gt-muted); line-height:1.45; margin-top:6px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.gt-badges { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
.gt-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 8px; border-radius:999px; font-size:11px; font-weight:900; }
.gt-badge.gray { background:var(--gt-gray-light); color:var(--gt-gray); }
.gt-badge.green { background:var(--gt-success-light); color:#047857; }
.gt-badge.orange { background:var(--gt-warning-light); color:#b45309; }
.gt-badge.red { background:var(--gt-danger-light); color:#b91c1c; }
.gt-badge.blue { background:var(--gt-blue-light); color:#2563eb; }

.gt-progress-box { margin-top:12px; border:1px solid #eef2f7; border-radius:14px; padding:10px; background:linear-gradient(135deg,#f8fafc 0%, #fff 100%); }
.gt-progress-head { display:flex; justify-content:space-between; gap:8px; font-size:11px; font-weight:950; color:#111827; }
.gt-progress-track { height:9px; border-radius:999px; background:#e5e7eb; overflow:hidden; margin-top:8px; }
.gt-progress-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,var(--gt-primary),#22c55e); transition:width .3s ease; }
.gt-progress-meta { display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-top:7px; font-size:11px; color:var(--gt-muted); font-weight:800; }

.gt-step-preview { margin-top:10px; display:flex; flex-direction:column; gap:6px; }
.gt-step-mini { display:flex; align-items:flex-start; gap:8px; border:1px solid #edf2f7; background:#fff; border-radius:12px; padding:8px; }
.gt-step-mini input { margin-top:2px; accent-color:var(--gt-primary); }
.gt-step-mini-main { min-width:0; flex:1; }
.gt-step-mini-title { font-size:12px; font-weight:900; color:#111827; line-height:1.3; }
.gt-step-mini-meta { font-size:11px; color:var(--gt-muted); margin-top:2px; }
.gt-step-mini.done .gt-step-mini-title { text-decoration:line-through; color:#047857; }
.gt-step-mini-users { display:flex; align-items:center; margin-top:5px; }

.gt-task-foot { margin-top:12px; padding-top:10px; border-top:1px solid var(--gt-border); display:flex; align-items:center; justify-content:space-between; gap:8px; }
.gt-mini-users { display:flex; align-items:center; }
.gt-mini-avatar, .gt-mini-avatar-img { width:28px; height:28px; border-radius:999px; border:2px solid #fff; background:var(--gt-primary-light); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:900; color:#4d7c0f; margin-left:-7px; box-shadow:var(--gt-shadow-sm); object-fit:cover; }
.gt-mini-avatar:first-child, .gt-mini-avatar-img:first-child { margin-left:0; }
.gt-more-avatar { width:28px; height:28px; border-radius:999px; border:2px solid #fff; background:#111827; color:#fff; font-size:10px; font-weight:950; display:flex; align-items:center; justify-content:center; margin-left:-7px; }
.gt-task-actions { display:flex; gap:6px; }
.gt-empty { text-align:center; padding:24px 12px; color:var(--gt-muted); border:1px dashed var(--gt-border); border-radius:14px; background:#fff; }

.gt-archive-list, .gt-gantt-panel, .gt-recurring-panel { background:#fff; border:1px solid var(--gt-border); border-radius:16px; box-shadow:var(--gt-shadow-sm); overflow:hidden; }
.gt-archive-row { display:grid; grid-template-columns:minmax(240px,1.4fr) 150px 150px 170px 120px; gap:12px; align-items:center; padding:14px 16px; border-bottom:1px solid var(--gt-border); }
.gt-gantt-head { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; padding:14px 16px; border-bottom:1px solid var(--gt-border); background:#fafafa; }
.gt-gantt-title { font-weight:950; color:#111827; }
.gt-gantt-body { overflow:auto; padding:14px; }
.gt-gantt-row { display:grid; grid-template-columns:260px minmax(620px,1fr); gap:12px; align-items:center; padding:9px 0; border-bottom:1px solid #f1f5f9; }
.gt-gantt-name { font-weight:900; font-size:12px; color:#111827; }
.gt-gantt-meta { font-size:11px; color:var(--gt-muted); margin-top:3px; }
.gt-gantt-line { height:30px; background:#f1f5f9; border-radius:999px; position:relative; overflow:hidden; }
.gt-gantt-bar { position:absolute; top:5px; height:20px; border-radius:999px; background:#dbeafe; min-width:28px; overflow:hidden; border:1px solid #bfdbfe; }
.gt-gantt-bar-fill { height:100%; background:linear-gradient(90deg,var(--gt-primary),#22c55e); }
.gt-gantt-percent { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:950; color:#111827; }

.gt-sidebar-overlay { position:fixed; inset:0; z-index:1290; background:rgba(15,23,42,.48); backdrop-filter:blur(3px); opacity:0; visibility:hidden; transition:opacity .22s ease, visibility .22s ease; }
.gt-sidebar-overlay.open { opacity:1; visibility:visible; }
.gt-team-drawer { position:fixed; top:0; right:0; z-index:1300; width:min(430px, calc(100vw - 18px)); height:100vh; background:#f8fafc; border-left:1px solid rgba(226,232,240,.9); box-shadow:-22px 0 50px -30px rgb(15 23 42 / .6); transform:translateX(110%); transition:transform .26s cubic-bezier(.2,.8,.2,1); display:flex; flex-direction:column; overflow:hidden; }
.gt-team-drawer.open { transform:translateX(0); }
.gt-drawer-head { padding:18px; background:linear-gradient(135deg,#fff 0%, #f4fae7 100%); border-bottom:1px solid var(--gt-border); display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.gt-drawer-title { font-size:20px; font-weight:950; color:#111827; }
.gt-drawer-sub { font-size:13px; color:var(--gt-muted); margin-top:4px; line-height:1.45; }
.gt-drawer-tabs { display:flex; gap:8px; padding:14px 18px 10px; }
.gt-drawer-tab { flex:1; border:1px solid var(--gt-border); background:#fff; color:var(--gt-muted); padding:10px 12px; border-radius:12px; font-weight:900; cursor:pointer; transition:var(--gt-transition); }
.gt-drawer-tab.active { background:var(--gt-primary-light); border-color:#d9ef9d; color:#4d7c0f; }
.gt-drawer-body { flex:1; overflow-y:auto; padding:0 18px 18px; }
.gt-drawer-panel { display:none; }
.gt-drawer-panel.active { display:block; }
.gt-panel { background:#fff; border:1px solid var(--gt-border); border-radius:16px; box-shadow:var(--gt-shadow-sm); overflow:hidden; }
.gt-panel-h { padding:14px 16px; border-bottom:1px solid var(--gt-border); display:flex; align-items:center; justify-content:space-between; gap:8px; background:#fafafa; }
.gt-panel-title { font-weight:900; color:#111827; }
.gt-panel-b { padding:14px 16px; }
.gt-team-list { display:flex; flex-direction:column; gap:10px; }
.gt-person { display:flex; align-items:center; gap:10px; padding:10px; border:1px solid var(--gt-border); border-radius:12px; background:#fff; transition:var(--gt-transition); }
.gt-avatar { width:38px; height:38px; border-radius:999px; object-fit:cover; background:var(--gt-primary-light); display:flex; align-items:center; justify-content:center; font-weight:900; color:#4d7c0f; border:2px solid #fff; flex:0 0 auto; }
.gt-person-name { font-weight:900; color:#111827; font-size:13px; line-height:1.35; }
.gt-person-meta { font-size:12px; color:var(--gt-muted); line-height:1.4; }
.gt-online, .gt-offline { width:9px; height:9px; border-radius:999px; margin-left:auto; flex:0 0 auto; }
.gt-online { background:var(--gt-success); box-shadow:0 0 0 3px var(--gt-success-light); }
.gt-offline { background:#d1d5db; }
.gt-floating-sidebar-btn { position:fixed; right:20px; bottom:92px; z-index:1180; width:48px; height:48px; border-radius:999px; border:1px solid #d9ef9d; background:var(--gt-primary); color:#fff; box-shadow:var(--gt-shadow); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:var(--gt-transition); }

.gt-modal-backdrop { position:fixed; inset:0; z-index:1200; background:rgba(17,24,39,.55); backdrop-filter:blur(3px); opacity:0; pointer-events:none; transition:opacity .22s ease; display:flex; align-items:center; justify-content:center; padding:18px; }
.gt-modal-backdrop.open { opacity:1; pointer-events:auto; }
.gt-modal { width:100%; max-width:720px; background:#fff; border:1px solid rgba(229,231,235,.9); border-radius:16px; box-shadow:var(--gt-shadow); transform:translateY(12px) scale(.985); transition:transform .22s ease; overflow:hidden; }
.gt-modal.lg { max-width:980px; }
.gt-modal-backdrop.open .gt-modal { transform:translateY(0) scale(1); }
.gt-modal-h { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:16px 18px; border-bottom:1px solid var(--gt-border); background:#fafafa; }
.gt-modal-ttl { font-weight:900; font-size:16px; margin:0; color:#111827; }
.gt-modal-b { padding:20px 18px; max-height:72vh; overflow-y:auto; }
.gt-modal-f { padding:14px 18px; border-top:1px solid var(--gt-border); background:#fafafa; display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap; }
.gt-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
.gt-form-group { margin-bottom:16px; }
.gt-label { display:block; font-size:13px; font-weight:800; color:var(--gt-text); margin-bottom:6px; }
.gt-help { font-size:12px; color:var(--gt-muted); margin-top:6px; }
.gt-section-box { grid-column:1/-1; border:1px solid var(--gt-border); border-radius:16px; background:#fbfdf8; padding:14px; }
.gt-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; }
.gt-section-title { font-weight:950; color:#111827; }
.gt-mode-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
.gt-mode-card { border:1px solid var(--gt-border); background:#fff; border-radius:14px; padding:12px; cursor:pointer; display:flex; gap:10px; align-items:flex-start; }
.gt-mode-card input { margin-top:3px; accent-color:var(--gt-primary); }
.gt-mode-card:has(input:checked) { border-color:var(--gt-primary); background:var(--gt-primary-light); }
.gt-step-builder { display:none; }
.gt-step-builder.active { display:block; }
.gt-step-row { border:1px solid var(--gt-border); background:#fff; border-radius:16px; padding:12px; margin-bottom:12px; }
.gt-step-row-head { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px; }
.gt-step-row-title { font-size:12px; font-weight:950; color:#4d7c0f; text-transform:uppercase; letter-spacing:.05em; }
.gt-step-grid { display:grid; grid-template-columns:1.3fr .8fr .8fr; gap:10px; }
.gt-step-grid .full { grid-column:1/-1; }
.gt-step-done-box { display:flex; align-items:center; gap:8px; font-size:13px; font-weight:800; }
.gt-step-done-box input { accent-color:var(--gt-primary); }
.gt-assignee-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
.gt-assignee-card { position:relative; display:flex; flex-direction:column; align-items:center; gap:7px; border:1px solid var(--gt-border); border-radius:14px; padding:11px 8px; background:#fff; cursor:pointer; transition:var(--gt-transition); min-height:112px; }
.gt-assignee-card input { position:absolute; opacity:0; pointer-events:none; }
.gt-assignee-avatar { width:52px; height:52px; border-radius:999px; object-fit:cover; background:var(--gt-primary-light); border:3px solid #fff; box-shadow:var(--gt-shadow-sm); display:inline-flex; align-items:center; justify-content:center; font-size:14px; font-weight:950; color:#4d7c0f; }
.gt-assignee-name { font-size:11px; font-weight:900; color:#111827; max-width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; text-align:center; }
.gt-assignee-check { position:absolute; top:8px; right:8px; width:22px; height:22px; border-radius:999px; display:none; align-items:center; justify-content:center; background:var(--gt-primary); color:#fff; }
.gt-assignee-card:has(input:checked) { border-color:var(--gt-primary); background:var(--gt-primary-light); }
.gt-assignee-card:has(input:checked) .gt-assignee-check { display:inline-flex; }
.gt-detail-box { border:1px solid var(--gt-border); border-radius:14px; padding:14px; background:#fff; margin-bottom:12px; }
.gt-comment { border:1px solid var(--gt-border); border-radius:12px; padding:10px; margin-bottom:10px; background:#fff; }
.gt-comment-head { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:12px; color:var(--gt-muted); margin-bottom:6px; }
.gt-comment-body { font-size:13px; color:#374151; line-height:1.45; }

.gt-toast-wrap { position:fixed; right:20px; bottom:20px; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
.gt-toast { pointer-events:auto; min-width:280px; max-width:380px; background:#fff; border:1px solid var(--gt-border); border-radius:14px; box-shadow:var(--gt-shadow); padding:12px; display:flex; gap:10px; align-items:flex-start; animation:gtToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards; }
@keyframes gtToastIn { from { transform:translateX(100%); opacity:0; } to { transform:translateX(0); opacity:1; } }
.gt-toast-ic { width:34px; height:34px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
.gt-toast-ic.ok { background:var(--gt-success-light); color:var(--gt-success); }
.gt-toast-ic.info { background:var(--gt-blue-light); color:#2563eb; }
.gt-toast-ic.bad { background:var(--gt-danger-light); color:var(--gt-danger); }
.gt-toast-ttl { font-weight:900; font-size:13px; margin:0; color:#111827; }
.gt-toast-msg { font-size:12px; color:#374151; margin:4px 0 0; line-height:1.4; }
.gt-toast-x { margin-left:auto; background:transparent; border:none; cursor:pointer; color:var(--gt-muted); }

.select2-container { width:100% !important; }
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple { min-height:44px; border:1px solid var(--gt-border); border-radius:12px; background:#fff; padding:5px 8px; }
.select2-container--default .select2-selection--multiple .select2-selection__choice { background:var(--gt-primary-light); border:1px solid #d9ef9d; color:#4d7c0f; border-radius:999px; padding:4px 8px; font-weight:800; }
.select2-dropdown { border:1px solid var(--gt-border); border-radius:12px; overflow:hidden; z-index:99999; }
.select2-search__field { border-radius:8px !important; border:1px solid var(--gt-border) !important; padding:8px 10px !important; }

body.gt-drawer-lock { overflow:hidden; }

@media(max-width:1180px) { .gt-board { grid-template-columns:repeat(4,minmax(300px,1fr)); } }
@media(max-width:1000px) {
    .gt-stats { grid-template-columns:repeat(2,1fr); }
    .gt-archive-row, .gt-gantt-row { grid-template-columns:1fr; }
    .gt-step-grid { grid-template-columns:1fr; }
    .gt-assignee-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
}
@media(max-width:760px) {
    .gt-titlebar { align-items:flex-start; }
    .gt-actions { width:100%; }
    .gt-actions .gt-btn, .gt-actions .gt-btn-soft { flex:1; }
    .gt-toolbar-left, .gt-toolbar-right, .gt-filter, .gt-filter.search { width:100%; min-width:100%; }
    .gt-toolbar-right .gt-btn-soft { flex:1; }
    .gt-form-grid, .gt-mode-grid { grid-template-columns:1fr; }
    .gt-team-drawer { width:100vw; }
    .gt-floating-sidebar-btn { right:16px; bottom:84px; }
}
@media(max-width:640px) {
    .gt-stats { grid-template-columns:1fr; }
    .gt-assignee-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
}

/* Schritt due date + overdue animation */
.gt-step-row.is-step-overdue,
.gt-detail-step.is-step-overdue,
.gt-step-mini.is-step-overdue,
.gt-move-step-row.is-step-overdue {
    border-color: rgba(239, 68, 68, .65) !important;
    background: linear-gradient(135deg, #fff 0%, #fef2f2 100%) !important;
    animation: gtStepOverduePulse 1.7s ease-in-out infinite;
}

.gt-step-row.is-step-overdue .gt-step-row-title,
.gt-detail-step.is-step-overdue .gt-detail-step-title,
.gt-step-mini.is-step-overdue .gt-step-mini-title {
    color: #b91c1c !important;
}

.gt-step-due-overdue {
    color: #b91c1c !important;
    font-weight: 950 !important;
}

@keyframes gtStepOverduePulse {
    0%, 100% { box-shadow: 0 1px 2px rgba(15, 23, 42, .05); }
    50% { box-shadow: 0 0 0 4px rgba(239, 68, 68, .12), 0 14px 26px -18px rgba(239, 68, 68, .55); }
}


/* Drag & drop ordering for tasks and Schritte */
.gt-dropzone.order-saving {
    position: relative;
}
.gt-dropzone.order-saving::after {
    content: "Reihenfolge wird gespeichert...";
    position: sticky;
    bottom: 10px;
    display: block;
    margin: 10px auto 0;
    width: fit-content;
    padding: 7px 12px;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    font-size: 11px;
    font-weight: 900;
    box-shadow: var(--gt-shadow-sm);
}
.gt-task.dragging {
    opacity: .62;
    transform: scale(.985);
    border-style: dashed;
}
.gt-step-drag-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--gt-border);
    background: #fff;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: grab;
    color: var(--gt-muted);
    transition: var(--gt-transition);
}
.gt-step-drag-btn:hover {
    color: #111827;
    background: #f9fafb;
    border-color: var(--gt-primary);
}
.gt-step-row.step-dragging {
    opacity: .58;
    transform: scale(.99);
    border-style: dashed;
    box-shadow: 0 14px 34px -22px rgb(15 23 42 / .6);
}
.gt-step-head-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
</style>