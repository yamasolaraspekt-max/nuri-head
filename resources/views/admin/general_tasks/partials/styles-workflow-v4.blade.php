<style>
  .gt-modal.gt-task-create-modal { max-width: min(1320px, calc(100vw - 26px)); width: 100%; }
  .gt-task-modal-layout { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:18px; align-items:start; background:#f8fafc; }
  .gt-task-modal-main { background:#fff; border:1px solid var(--gt-border); border-radius:18px; padding:18px; min-width:0; }
  .gt-task-modal-sidebar { position:sticky; top:0; display:flex; flex-direction:column; gap:12px; max-height:70vh; overflow:auto; padding-right:3px; }
  .gt-sidebar-card { background:#fff; border:1px solid var(--gt-border); border-radius:18px; padding:14px; box-shadow:var(--gt-shadow-sm); }
  .gt-sidebar-card-title { font-weight:950; color:#111827; margin-bottom:12px; }
  .gt-sidebar-toggle { width:100%; border:none; background:transparent; padding:0; display:flex; align-items:center; justify-content:space-between; gap:10px; cursor:pointer; font-weight:950; color:#111827; }
  .gt-sidebar-toggle span { display:inline-flex; align-items:center; gap:8px; }
  .gt-sidebar-toggle-body { padding-top:14px; }
  .gt-switch-line { display:flex; align-items:flex-start; gap:9px; padding:9px 0; font-size:13px; font-weight:800; color:#374151; }
  .gt-switch-line input { margin-top:3px; accent-color:var(--gt-primary); }
  .gt-task-mode-hero { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
  .gt-mode-card { min-height:98px; align-items:flex-start; }
  .gt-mode-card span { display:flex; flex-direction:column; gap:6px; }
  .gt-mode-card strong { font-size:15px; color:#111827; }
  .gt-mode-card small { font-size:12px; color:var(--gt-muted); line-height:1.35; }
  .gt-single-only.is-hidden { display:none !important; }
  .gt-bulk-only { display:none; }
  .gt-bulk-only.active { display:block; }
  .gt-step-builder-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; }
  .gt-step-row { border:1px solid #dde7f0; background:#fff; border-radius:18px; overflow:hidden; margin-bottom:12px; box-shadow:0 10px 25px -22px rgb(15 23 42 / .65); }
  .gt-step-row.is-collapsed .gt-step-row-body { display:none; }
  .gt-step-row-head { margin:0; padding:14px 15px; background:linear-gradient(135deg,#f8fafc 0%,#f4fae7 100%); border-bottom:1px solid var(--gt-border); display:flex; justify-content:space-between; align-items:center; gap:12px; }
  .gt-step-head-left { min-width:0; display:flex; align-items:center; gap:10px; }
  .gt-step-collapse-btn { width:34px; height:34px; border-radius:11px; border:1px solid var(--gt-border); background:#fff; color:#111827; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
  .gt-step-row-title { font-size:13px; font-weight:950; color:#4d7c0f; letter-spacing:.04em; text-transform:uppercase; }
  .gt-step-row-summary { font-size:12px; color:var(--gt-muted); margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .gt-step-row-actions { display:flex; gap:7px; align-items:center; }
  .gt-step-row-body { padding:15px; }
  .gt-step-grid { display:grid; grid-template-columns:1.5fr .75fr .75fr; gap:12px; }
  .gt-step-grid .full { grid-column:1/-1; }
  .gt-step-time-help { font-size:11px; color:var(--gt-muted); margin-top:5px; }
  .gt-card-v4 { border-radius:18px; border:1px solid #e2e8f0; background:linear-gradient(180deg,#fff 0%,#fbfdff 100%); padding:14px; box-shadow:0 12px 28px -24px rgb(15 23 42 / .6); }
  .gt-card-v4:hover { transform:translateY(-1px); box-shadow:0 20px 45px -28px rgb(15 23 42 / .65); }
  .gt-task-top-actions { display:flex; gap:6px; flex:0 0 auto; }
  .gt-info-btn { color:#2563eb; background:#eff6ff; border-color:#bfdbfe; }
  .gt-task-title { font-size:15px; }
  .gt-card-compact-meta { display:flex; flex-wrap:wrap; gap:6px; margin-top:9px; }
  .gt-card-detail-strip { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; margin-top:10px; }
  .gt-card-detail-pill { background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; padding:8px; min-width:0; }
  .gt-card-detail-label { font-size:10px; font-weight:950; color:var(--gt-muted); text-transform:uppercase; letter-spacing:.04em; }
  .gt-card-detail-value { font-size:12px; font-weight:950; color:#111827; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .gt-task-actions { display:flex; gap:7px; align-items:center; }
  .gt-card-archive-btn { color:#6b7280; }
  .gt-card-archive-btn:hover { color:#b45309; background:#fffbeb; border-color:#fed7aa; }
  .gt-step-preview { margin-top:12px; background:#f8fafc; border:1px solid #eef2f7; border-radius:14px; padding:9px; }
  .gt-step-mini { background:#fff; border-color:#e5e7eb; }
  .gt-step-mini-check-btn { width:26px; height:26px; border-radius:999px; border:1px solid #dbeafe; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex:0 0 auto; }
  .gt-step-mini-check-btn.done { background:#ecfdf5; color:#059669; border-color:#bbf7d0; }
  .gt-detail-modal { max-width:min(1120px,calc(100vw - 26px)); }
  .gt-detail-layout { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:16px; }
  .gt-detail-main, .gt-detail-side { background:#fff; border:1px solid var(--gt-border); border-radius:18px; padding:15px; }
  .gt-detail-side { background:#f8fafc; }
  .gt-detail-title { font-size:20px; font-weight:950; color:#111827; margin-bottom:6px; }
  .gt-detail-desc { color:#4b5563; line-height:1.55; font-size:13px; white-space:pre-wrap; }
  .gt-detail-step-list { display:flex; flex-direction:column; gap:10px; margin-top:14px; }
  .gt-detail-step { border:1px solid #e5e7eb; border-radius:16px; background:#fff; overflow:hidden; }
  .gt-detail-step-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:12px; background:#f8fafc; }
  .gt-detail-step-title { font-weight:950; color:#111827; }
  .gt-detail-step-meta { font-size:12px; color:var(--gt-muted); margin-top:4px; }
  .gt-detail-step-body { padding:12px; font-size:13px; color:#374151; }
  .gt-detail-check-btn { border:none; border-radius:12px; padding:9px 11px; font-weight:950; cursor:pointer; display:inline-flex; align-items:center; gap:7px; background:#ecfdf5; color:#047857; }
  .gt-detail-check-btn.undo { background:#fff7ed; color:#b45309; }
  .gt-detail-empty { border:1px dashed #cbd5e1; background:#f8fafc; border-radius:16px; padding:18px; text-align:center; color:var(--gt-muted); }
  .gt-move-step-list { display:flex; flex-direction:column; gap:8px; margin-top:10px; max-height:230px; overflow:auto; padding-right:3px; }
  .gt-move-step-row { display:flex; gap:9px; align-items:flex-start; padding:10px; border:1px solid #e5e7eb; border-radius:13px; background:#fff; }
  .gt-move-step-row input { margin-top:3px; accent-color:var(--gt-primary); }
  .gt-move-step-title { font-weight:900; font-size:13px; color:#111827; }
  .gt-move-step-meta { font-size:11px; color:var(--gt-muted); margin-top:2px; }
  .gt-gantt-panel-v4 { overflow:hidden; }
  .gt-gantt-timeline-wrap { overflow:auto; background:#f8fafc; }
  .gt-gantt-stage { position:relative; min-width:980px; padding:16px; }
  .gt-gantt-dependency-svg { position:absolute; inset:0; width:100%; height:100%; pointer-events:none; overflow:visible; z-index:4; }
  .gt-gantt-row-v4 { display:grid; grid-template-columns:280px minmax(620px,1fr); gap:12px; align-items:center; min-height:54px; position:relative; z-index:2; border-bottom:1px solid #e5e7eb; }
  .gt-gantt-line-v4 { height:38px; border-radius:16px; background:repeating-linear-gradient(90deg,#eef2f7 0,#eef2f7 1px,#f8fafc 1px,#f8fafc 80px); position:relative; overflow:visible; }
  .gt-gantt-bar-v4 { position:absolute; top:7px; height:24px; border-radius:999px; background:#dbeafe; border:1px solid #93c5fd; overflow:hidden; box-shadow:0 6px 14px -12px #1d4ed8; z-index:8; }
  .gt-gantt-bar-v4.has-parent { border-color:#f59e0b; background:#fff7ed; }
  .gt-gantt-bar-v4.has-child { box-shadow:0 0 0 3px rgba(37,99,235,.1),0 6px 14px -12px #1d4ed8; }
  .gt-gantt-bar-fill { height:100%; background:linear-gradient(90deg,var(--gt-primary),#22c55e); }
  .gt-gantt-percent { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:950; color:#111827; }
  .gt-gantt-dependency-list { padding:14px 16px; border-top:1px solid var(--gt-border); background:#fff; display:flex; flex-direction:column; gap:8px; }
  .gt-dependency-line-item { display:flex; align-items:center; gap:9px; flex-wrap:wrap; padding:9px 10px; background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; font-size:12px; font-weight:900; }
  .gt-dependency-arrow { color:#2563eb; }
  .gt-gantt-dep-path { stroke:#2563eb; stroke-width:2.25; fill:none; marker-end:url(#gtArrowV4); filter:drop-shadow(0 1px 1px rgba(37,99,235,.25)); }
  @media(max-width:1100px){ .gt-task-modal-layout,.gt-detail-layout{grid-template-columns:1fr}.gt-task-modal-sidebar{position:relative;max-height:none}.gt-step-grid{grid-template-columns:1fr}.gt-card-detail-strip{grid-template-columns:1fr} }
</style>
<style>
/* Workflow v5 overrides: compact cards, full-width drawer modal, practical Gantt */
#gtTaskModal.gt-modal-backdrop { align-items: stretch; justify-content: flex-end; padding: 0; }
#gtTaskModal .gt-modal.gt-task-create-modal {
  width: min(1540px, 96vw) !important;
  max-width: none !important;
  height: 100vh;
  border-radius: 26px 0 0 26px;
  display: flex;
  flex-direction: column;
}
#gtTaskModal .gt-modal-h { flex: 0 0 auto; padding: 20px 24px; }
#gtTaskModal form { flex: 1; min-height: 0; display: flex; flex-direction: column; }
#gtTaskModal .gt-modal-b.gt-task-modal-layout {
  flex: 1;
  min-height: 0;
  max-height: none;
  overflow: hidden;
  display: grid;
  grid-template-columns: minmax(700px, 1fr) 420px;
  gap: 18px;
  padding: 18px;
}
#gtTaskModal .gt-task-modal-main,
#gtTaskModal .gt-task-modal-sidebar {
  max-height: calc(100vh - 152px);
  overflow: auto;
}
#gtTaskModal .gt-task-modal-main { padding: 20px; }
#gtTaskModal .gt-input-lg { min-height: 48px; font-size: 15px; }
#gtTaskModal .gt-textarea-lg { min-height: 150px; }
#gtTaskModal .gt-modal-f { flex: 0 0 auto; padding: 15px 24px; }

.gt-card-v5 {
  padding: 10px 11px !important;
  border-radius: 13px !important;
  background: #fff !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, .06) !important;
}
.gt-card-v5:hover { transform: none !important; box-shadow: 0 12px 30px -26px rgb(15 23 42 / .65) !important; }
.gt-card-v5-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
.gt-card-v5-title-wrap { min-width: 0; flex: 1; }
.gt-card-v5 .gt-task-title { font-size: 13px !important; line-height: 1.3; }
.gt-card-v5 .gt-task-desc { font-size: 11px !important; margin-top: 3px; -webkit-line-clamp: 1; }
.gt-card-v5-meta { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 7px; }
.gt-card-v5 .gt-badge { padding: 3px 6px; font-size: 10px; gap: 3px; }
.gt-card-v5 .gt-btn-ic { width: 30px; height: 30px; border-radius: 9px; }
.gt-card-v5-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #eef2f7; }
.gt-card-v5 .gt-mini-avatar,
.gt-card-v5 .gt-mini-avatar-img,
.gt-card-v5 .gt-more-avatar { width: 24px; height: 24px; font-size: 9px; }
.gt-card-progress-compact { margin-top: 8px !important; padding: 7px !important; border-radius: 10px !important; }
.gt-card-progress-compact .gt-progress-head { font-size: 10px !important; }
.gt-card-progress-compact .gt-progress-track { height: 6px !important; margin-top: 5px !important; }
.gt-card-v5 .gt-card-detail-strip,
.gt-card-v5 .gt-step-preview { display: none !important; }

.gt-gantt-pro { background:#fff; border:1px solid var(--gt-border); border-radius:18px; overflow:hidden; box-shadow:var(--gt-shadow-sm); }
.gt-gantt-pro-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--gt-border); background:#fafafa; }
.gt-gantt-pro-title { font-weight:950; color:#111827; }
.gt-gantt-pro-sub { font-size:12px; color:var(--gt-muted); margin-top:3px; }
.gt-gantt-pro-scroll { overflow:auto; background:#fff; }
.gt-gantt-pro-inner { min-width:1180px; position:relative; }
.gt-gantt-pro-head,
.gt-gantt-pro-row { display:grid; grid-template-columns:360px 1fr; }
.gt-gantt-left-head { display:grid; grid-template-columns:1fr 112px; border-right:1px solid #dbe3ed; background:#f8fafc; font-weight:950; font-size:12px; color:#111827; }
.gt-gantt-left-head div { padding:10px 12px; border-bottom:1px solid #dbe3ed; }
.gt-gantt-months { display:grid; grid-template-columns:repeat(6, minmax(160px, 1fr)); background:#f8fafc; border-bottom:1px solid #dbe3ed; }
.gt-gantt-month { padding:10px 12px; text-align:center; font-size:12px; font-weight:950; color:#111827; border-right:1px solid #e5e7eb; }
.gt-gantt-pro-row { min-height:42px; border-bottom:1px solid #e5e7eb; }
.gt-gantt-pro-row:hover { background:#f8fafc; }
.gt-gantt-task-left { display:grid; grid-template-columns:1fr 112px; border-right:1px solid #dbe3ed; min-width:0; }
.gt-gantt-task-name { display:flex; align-items:center; gap:8px; padding:8px 10px; min-width:0; font-size:12px; font-weight:800; color:#111827; }
.gt-gantt-task-name .gt-tree-dot { width:16px; height:16px; border-radius:4px; border:1px solid #cbd5e1; background:#fff; flex:0 0 auto; }
.gt-gantt-task-title { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.gt-gantt-state { display:flex; align-items:center; gap:6px; padding:8px 10px; font-size:11px; color:#374151; border-left:1px solid #eef2f7; }
.gt-gantt-state-dot { width:10px; height:10px; border-radius:999px; background:#94a3b8; }
.gt-gantt-state-dot.done { background:#10b981; }
.gt-gantt-state-dot.progress { background:#9fca23; }
.gt-gantt-state-dot.open { background:#f59e0b; }
.gt-gantt-state-dot.review { background:#3b82f6; }
.gt-gantt-timeline-cell { position:relative; min-height:42px; background:repeating-linear-gradient(90deg,#fff 0,#fff 39px,#f8fafc 39px,#f8fafc 80px); }
.gt-gantt-timeline-cell:before { content:""; position:absolute; inset:0; background:repeating-linear-gradient(90deg,transparent 0,transparent 159px,#dbe3ed 159px,#dbe3ed 160px); pointer-events:none; }
.gt-gantt-bar-pro { position:absolute; top:10px; height:22px; border-radius:5px; background:#38bdf8; border:1px solid #0284c7; overflow:hidden; z-index:5; box-shadow:0 1px 2px rgba(15,23,42,.12); }
.gt-gantt-bar-pro.has-parent { border-color:#4f46e5; }
.gt-gantt-bar-pro-fill { height:100%; background:#9fca23; }
.gt-gantt-bar-pro-label { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:950; color:#0f172a; }
.gt-gantt-link-svg { position:absolute; left:360px; top:0; width:calc(100% - 360px); height:100%; pointer-events:none; z-index:8; overflow:visible; }
.gt-gantt-link-path { stroke:#ef4444; stroke-width:1.6; fill:none; marker-end:url(#gtGanttArrowPro); }
.gt-gantt-link-dot { fill:#f59e0b; stroke:#ef4444; stroke-width:1; }
.gt-gantt-dependency-list { padding:12px 16px; border-top:1px solid var(--gt-border); background:#fff; display:flex; gap:8px; flex-wrap:wrap; }
.gt-dependency-line-item { padding:7px 9px; border-radius:10px; background:#f8fafc; border:1px solid #e5e7eb; font-size:11px; font-weight:900; }
@media(max-width:1200px){
  #gtTaskModal .gt-modal.gt-task-create-modal { width:100vw !important; border-radius:0; }
  #gtTaskModal .gt-modal-b.gt-task-modal-layout { grid-template-columns:1fr; overflow:auto; }
  #gtTaskModal .gt-task-modal-main,#gtTaskModal .gt-task-modal-sidebar { max-height:none; overflow:visible; }
}

.gt-card-delete-btn { color:#b91c1c; border-color:#fecaca; background:#fef2f2; }
.gt-card-delete-btn:hover { color:#fff; background:#ef4444; border-color:#ef4444; }

.gt-org-panel { background:#fff; border:1px solid var(--gt-border); border-radius:22px; overflow:hidden; box-shadow:var(--gt-shadow-sm); }
.gt-org-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; padding:18px 20px; border-bottom:1px solid var(--gt-border); background:linear-gradient(135deg,#fff 0%,#f8fafc 100%); }
.gt-org-title-main { font-size:18px; font-weight:950; color:#111827; }
.gt-org-sub-main { font-size:13px; color:var(--gt-muted); margin-top:4px; }
.gt-org-legend { display:flex; gap:8px; flex-wrap:wrap; }
.gt-org-legend .priority { display:inline-flex; align-items:center; padding:6px 10px; border-radius:999px; font-size:11px; font-weight:950; }
.gt-org-legend .urgent { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
.gt-org-legend .important { background:#fff7ed; color:#b45309; border:1px solid #fed7aa; }
.gt-org-legend .normal { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.gt-org-legend .low { background:#f3f4f6; color:#4b5563; border:1px solid #e5e7eb; }
.gt-org-scroll { overflow:auto; min-height:620px; background:#f8fafc; padding:32px; }
.gt-org-board { min-width:980px; display:flex; flex-direction:column; gap:38px; align-items:center; }
.gt-org-node-wrap { display:flex; flex-direction:column; align-items:center; position:relative; }
.gt-org-node { width:260px; min-height:138px; background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:12px; box-shadow:0 14px 40px -32px rgba(15,23,42,.7); position:relative; overflow:hidden; }
.gt-org-node:before { content:""; position:absolute; inset:0 0 auto; height:5px; background:#3b82f6; }
.gt-org-node.priority-urgent:before { background:#ef4444; }
.gt-org-node.priority-important:before { background:#f59e0b; }
.gt-org-node.priority-normal:before { background:#3b82f6; }
.gt-org-node.priority-low:before { background:#9ca3af; }
.gt-org-node.status-done { opacity:.82; }
.gt-org-node.status-done:after { content:"✓"; position:absolute; right:12px; top:34px; width:28px; height:28px; border-radius:999px; background:#ecfdf5; color:#047857; display:flex; align-items:center; justify-content:center; font-weight:950; }
.gt-org-node-top { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:5px; }
.gt-org-priority, .gt-org-status { font-size:10px; font-weight:950; padding:4px 7px; border-radius:999px; background:#f8fafc; color:#475569; border:1px solid #e5e7eb; }
.gt-org-title { margin-top:12px; font-size:13px; line-height:1.25; font-weight:950; color:#111827; text-align:center; }
.gt-org-sub { margin-top:6px; text-align:center; color:#64748b; font-size:11px; font-weight:800; }
.gt-org-progress { height:7px; border-radius:999px; background:#e5e7eb; overflow:hidden; margin-top:12px; }
.gt-org-progress span { display:block; height:100%; background:linear-gradient(90deg,var(--gt-primary),#22c55e); border-radius:999px; }
.gt-org-foot { margin-top:9px; display:flex; justify-content:space-between; gap:8px; color:#64748b; font-size:11px; }
.gt-org-foot strong { color:#111827; }
.gt-org-children { margin-top:42px; display:flex; justify-content:center; gap:28px; align-items:flex-start; position:relative; }
.gt-org-children:before { content:""; position:absolute; top:-22px; left:50%; width:1px; height:22px; background:#cbd5e1; }
.gt-org-children > .gt-org-node-wrap:before { content:""; position:absolute; top:-20px; left:50%; width:1px; height:20px; background:#cbd5e1; }
.gt-org-children > .gt-org-node-wrap:after { content:""; position:absolute; top:-20px; height:1px; background:#cbd5e1; left:-14px; right:-14px; }
.gt-org-children > .gt-org-node-wrap:first-child:after { left:50%; }
.gt-org-children > .gt-org-node-wrap:last-child:after { right:50%; }
.gt-org-children > .gt-org-node-wrap:only-child:after { display:none; }
@media(max-width:900px){ .gt-org-scroll{padding:18px}.gt-org-board{align-items:flex-start}.gt-org-children{flex-direction:column;align-items:center}.gt-org-children:before,.gt-org-children>.gt-org-node-wrap:before,.gt-org-children>.gt-org-node-wrap:after{display:none} }



/* Realtime/org-chart fix v2: dependency hierarchy by levels, not one column */
.gt-org-panel-v2 { min-height: 640px; }
.gt-org-scroll-v2 { overflow:auto; min-height:680px; background:#f8fafc; padding:36px; }
.gt-org-stage { position:relative; min-width:1180px; display:flex; flex-direction:column; gap:76px; align-items:center; padding:36px 28px 60px; }
.gt-org-level { position:relative; z-index:2; display:flex; justify-content:center; align-items:flex-start; gap:34px; width:max-content; min-width:100%; }
.gt-org-link-svg { position:absolute; inset:0; z-index:1; pointer-events:none; overflow:visible; }
.gt-org-link-path { fill:none; stroke:#94a3b8; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.gt-org-link-dot { fill:#8b5cf6; stroke:#fff; stroke-width:2; }
.gt-org-node { flex:0 0 250px; width:250px; min-height:132px; }
.gt-org-node.priority-urgent { border-color:#fecaca; }
.gt-org-node.priority-important { border-color:#fed7aa; }
.gt-org-node.priority-normal { border-color:#bfdbfe; }
.gt-org-node.priority-low { border-color:#e5e7eb; }
.gt-org-node[data-org-parent-ids]:not([data-org-parent-ids=""]) { box-shadow:0 16px 40px -30px rgba(79,70,229,.8), 0 0 0 3px rgba(139,92,246,.06); }
@media(max-width:900px){ .gt-org-scroll-v2{padding:18px}.gt-org-stage{min-width:980px;align-items:flex-start}.gt-org-level{justify-content:flex-start} }
</style>