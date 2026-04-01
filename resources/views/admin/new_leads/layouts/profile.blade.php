<style>
  
/* Global Resets */
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
}

.container-flex {
    display: flex;
    height: 100vh;
    overflow: hidden;
}


/* Layout Container */
.customer-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

/* Top Nav */
 

.customer-navs {
  background-color: #2c3e4f;
  padding: 1rem 1.5rem;
  margin-bottom: 0.4rem; 
  color: white;
  font-size: 14px;
}


.customer-nav .text-uppercase {
  letter-spacing: 0.5px;
  font-size: 20px;
  font-weight: bold;
}

.customer-nav .row {
  display: flex;
  flex-wrap: nowrap;
  align-items: stretch;
  overflow-x: auto;
}

.customer-nav .col {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.customer-nav .inner-col {
  height: 100%;
  padding-left: 1rem;
  padding-right: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.customer-nav .inner-col.border-start {
  border-left: 1px solid white;
}



.customer-nav-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.customer-nav-title {
  font-weight: bold;
  font-size: 1.1rem;
  color: #202020;
}
.customer-nav-icons {
  display: flex;
  gap: 1rem;
  align-items: center;
  color: #2d3e4f;
}
.customer-nav-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
  font-size: 0.9rem;
  color: #333;
}
.customer-nav-tabs {
  margin-top: 0.25rem;
}

/* Flex Main Layout */
.layout {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  width: 100%;
  height: 100%;
}


/* Sidebar Left */
.customerSidebar {
  width: 300px;
  background-color: #2d3e4f;
  color: #fff;
  padding: 1rem;
  overflow-y: auto;
  height: 99%;
  flex-shrink: 0;
  transition: width 0.3s ease;
}
.customerSidebar.minimized {
  width: 60px;
  padding: 1rem 0.3rem;
}
.customerSidebar.minimized .text,
.customerSidebar.minimized .sub-nav,
.customerSidebar.minimized .object-address,
.customerSidebar.minimized .customer-summary {
  display: none !important;
}

/* Sidebar Scrollbar */
.customerSidebar::-webkit-scrollbar {
  width: 6px;
}
.customerSidebar::-webkit-scrollbar-thumb {
  background-color: #666;
  border-radius: 4px;
}
.customerSidebar::-webkit-scrollbar-track {
  background: transparent;
}

.product-price-edit {
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.product-price-edit .feather {
    width: 14px;
    height: 14px;
}

/* Minimize/Dashboard Buttons */
.minimize-btn,
.dashboard-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1rem;
  width: 100%;
  margin-bottom: 1rem;
  text-align: left;
  cursor: pointer;
}
.minimize-btn:hover,
.dashboard-btn:hover {
  color: #0d6efd;
}

/* Object & Address */
.object-header {
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-radius: 0.3rem;
  transition: background-color 0.2s ease;
}
.object-header:hover {
  background-color: #3a4b5d;
}
.object-address {
  font-size: 0.8rem;
  margin-left: 2rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #fff;
}

/* Product Link */
.project-link {
  cursor: pointer;
  background: #fff;
  color: #000;
  margin-bottom: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.4rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.project-link:hover {
  background-color: #e2e6ea;
}

/* Status Badges */
.status-badge {
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 0.4rem;
  color: #fff;
}
.bg-planung { background-color: #95c120; }
.bg-lead    { background-color: #74b2d4; }
.bg-stopp   { background-color: #ff5733; }

/* Sub Navigation */
.sub-nav {
  display: none;
  margin-left: 1rem;
}
.sub-nav.show {
  display: block;
}
.sub-nav button {
  background: none;
  border: none;
  padding: 0.4rem 0.5rem;
  color: #fff;
  width: 100%;
  text-align: left;
  font-size: 0.9rem;
  cursor: pointer;
}
.sub-nav button:hover {
  background-color: #3a4b5d;
  border-radius: 0.3rem;
}

.contentStation {
  background: #bfbfbf;
}
.contentStation {
  flex: 1;
  min-width: 0; /* 🛠 ensures it shrinks correctly */
  overflow: hidden;
  position: relative;
}

.right-panel {
  width: 350px;
  flex-shrink: 0;
  background: #f1f0f0;
  border-left: 1px solid #ccc;
  overflow: hidden;
}

/* Main Content */
.main-content {
  flex: 1;
  overflow-y: auto;
  flex-grow: 1; 
  padding: 1rem;
  background:rgb(191 191 191);
  transition: all 0.3s ease;

}

/* Right Panel */
 

.panel-controls {
  position: relative;
  z-index: 1000;
}

.floating-show-btn {
  position: fixed;
  top: 110px;
  z-index: 1000;
  background: #fff;
  border: 1px solid #ccc;
  padding: 6px 10px;
  border-radius: 5px;
  color: #8fc73e;
  /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
}

.floating-show-btn.start {
  left: 10px;
}

.floating-show-btn.end {
  right: 10px;
}

.main-hidden {
  display: none !important;
}

 
/* Project row */
.project-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    transition: all .18s ease;
    margin-bottom: 6px;
}

.project-link:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
}

/* small colored dot before product name */
.product-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #93c119;
    flex-shrink: 0;
}

/* product name */
.product-title {
    font-weight: 600;
    color: #111827;
    font-size: 13px;
}

/* meta line */
.product-meta {
    font-size: 11.5px;
    line-height: 1.3;
}

.product-meta .meta-sep {
    margin: 0 4px;
    opacity: 0.6;
}

/* right side wrapper */
.project-meta {
    gap: 4px;
}

/* status badge – just refine size */
.status-badge {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 999px;
}

/* price badge, no edit icon */
.price-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    color: #1f2933;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid #d0d7df;
    cursor: pointer;
    transition: all .18s ease;
    font-size: 11.5px;
    font-weight: 600;
}

.price-badge:hover {
    background: #e6f4ea;
    border-color: #93c119;
    color: #111827;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

/* small muted "Preis" label before value */
.price-label {
    text-transform: uppercase;
    letter-spacing: .04em;
    font-size: 10px;
    color: #6b7280;
}

/* keep price-value for JS; just style it slightly */
.price-value {
    font-weight: 700;
}

 

.right-fullscreen {
  width: 100% !important;
  position: relative;
  z-index: 999;
  background: #ececec;
}

.right-hidden {
  display: none !important;
}

.main-hidden,
.sidebar-hidden {
  display: none !important;
}

 

.badge-danger {
    background-color: #dc3545 !important;
}

.badge-primary {
    background-color: #007bff !important;
}
 
.collapse {
  transition: height 0.3s ease, opacity 0.3s ease;
  overflow: hidden;
}


  </style>
 
<style>
  .sidebar-gallery {
    position: fixed;
    top: 0;
    right: -200%;
    width: 80%;
    height: 100%;
    background: #fff;
    /* box-shadow: -2px 0 5px rgba(0,0,0,0.2); */
    padding: 10px;
    z-index: 9999;
    overflow-y: auto;
    transition: right 0.3s ease-in-out;
}
.sidebar-gallery.active {
    right: 0;
}
.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}



</style>


<!-- Task Style  -->
 <style>
.new_task {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.3);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
.new_task.active {
    display: flex !important;
}

 
 </style>


<style>
.scroll-wrapper {
    max-height: 80vh;
    overflow-y: auto;
    padding-right: 8px;

    /* Hide scrollbar for all browsers */
    scrollbar-width: none;           /* Firefox */
    -ms-overflow-style: none;        /* IE/Edge */
}
.scroll-wrapper::-webkit-scrollbar {
    display: none;                   /* Chrome/Safari/Opera */
}

.nav-section-btn.active {
    background-color: #e6f4ea;
    color: #155724;
    border: 1px solid #c3e6cb;
}



:root{
  --bg-dark:#2d3e4f; --bg-muted:#f5f7fb; --card:#ffffff; --line:#e9edf3;
  --text:#1f2937; --muted:#6b7280; --brand:#0d6efd; --ok:#22c55e; --warn:#f59e0b;
}
.customer-nav, .customer-navs{ margin-bottom:.5rem; }
.customer-strip{
  display:grid; gap:.75rem;
  grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
  align-items:stretch;
}
.customer-chip{
  background:linear-gradient(180deg,#324558,#2d3e4f);
  color:#fff; border-radius:12px; padding:14px 16px; height:100%;
  box-shadow:0 4px 14px rgba(0,0,0,.08);
}
.customer-chip .inner-col small{ color:#cfd6de }
.customer-chip .inner-col{ line-height:1.15 }
.customer-chip .note{
  max-height:80px; overflow:auto; white-space:pre-wrap; word-wrap:break-word;
  cursor:pointer; border:1px dashed rgba(255,255,255,.25); border-radius:8px; padding:8px
}

/* product summary chip */
.product-chip{
  background:#fff; color:var(--text); border:1px solid var(--line); border-radius:12px;
  padding:14px 16px; height:100%; box-shadow:0 4px 20px rgba(2,6,23,.04);
}
.product-chip .sub{ color:var(--muted); font-size:.85rem }

/* avatar stack + role ring */
.avatar-stack{ display:flex; align-items:center; gap:6px; flex-wrap:wrap }
.avatar-ring{
  width:28px; height:28px; border-radius:999px; object-fit:cover; display:block;
  border:2px solid #d1d5db; /* will be overridden inline by role color */
}

/* progress (soft, readable label) */
.progress{ height:1.8rem; background:#eef2f7; border-radius:999px; overflow:hidden }
.progress .progress-bar{
  display:flex; align-items:center; justify-content:center; font-weight:600;
}

/* stage card */
.card.stage-card{ border:1px solid var(--line); border-radius:14px; overflow:hidden }
.stage-head{
  background:linear-gradient(180deg,#f9fbff,#eef5ff);
  padding:14px 16px; display:grid; gap:16px;
  grid-template-columns: 1.2fr 1fr 1.4fr auto;
  align-items:center; cursor:pointer;
}
.stage-head.active{ background:linear-gradient(180deg,#e8f2ff,#dfefff) }
.stage-title{ color:#0b63ce; font-weight:700; letter-spacing:.4px }
.stage-meta{ color:var(--muted); font-size:.9rem }
.stage-next .title{ font-weight:600; color:var(--text) }
.stage-next .desc{
  color:var(--muted); max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap
}
.stage-actions .btn{ margin-left:8px }

/* phase summary right column */
#phase-summary small{ color:#333 }
#phase-summary .smiley{ font-size:1.05rem }

/* table area */
.stage-body{ background:#fff; }
.table thead th{ background:#f5f7fb; border-color:var(--line); white-space:nowrap }
.table td, .table th{ vertical-align:middle }
.table-responsive{ border-top:1px solid var(--line) }

/* sidebar + layout (kept from yours but streamlined) */
.layout{ display:flex; width:100%; height:100% }
.customerSidebar{
  width:300px; background-color:var(--bg-dark); color:#fff; padding:1rem; overflow-y:auto;
  height:99%; flex-shrink:0; transition:width .3s ease, padding .3s ease
}
.customerSidebar.minimized{ width:64px; padding:1rem .35rem }
.customerSidebar::-webkit-scrollbar{ width:6px }
.customerSidebar::-webkit-scrollbar-thumb{ background:#666; border-radius:4px }
.main-content{ flex:1; overflow-y:auto; padding:1rem; background:#f3f5f9; transition:all .3s ease }
.right-panel{ width:360px; flex-shrink:0; background:#f9fafb; border-left:1px solid var(--line); overflow:hidden }

/* utilities */
.text-muted-700{ color:#4b5563 }
.badge-primary{ background:#0d6efd !important }
.badge-danger{ background:#dc3545 !important }
.collapse{ transition:height .25s ease, opacity .25s ease; }
.gold-icon svg { width: 48px; height: auto; display: block; }


</style>


<style>
  .badge-icon { display:none; width:100px; height:auto; }
  .badge-icons[data-tier="bronze"]   .badge-bronze   { display:inline-block; }
  .badge-icons[data-tier="silver"]   .badge-silver   { display:inline-block; }
  .badge-icons[data-tier="gold"]     .badge-gold     { display:inline-block; }
  .badge-icons[data-tier="platinum"] .badge-platinum { display:inline-block; }
  .badge-trigger[disabled] { opacity: .4; cursor: not-allowed; }
  .project-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    transition: all .2s ease;
}

.project-link:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.project-link .product-details .product {
    font-weight: 600;
    color: #111827;
}

.project-link small span.text {
    opacity: 0.9;
}


/* Better badge spacing */
.status-badge {
    margin-bottom: 6px !important;
}


/* Price badge improvements (uses your class names) */
.price-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    color: #2d3748;
    padding: 0px 2px;
    border-radius: 8px;
    border: 1px solid #d0d7df;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 9px;
    font-weight: 300;
}

.price-badge:hover {
    background: #e6f4ea;
    border-color: #93c119;
    color: #1a2d08;
    box-shadow: 0 1px 6px rgba(0,0,0,0.08);
}

.price-badge svg {
    width: 14px;
    height: 14px;
}

.price-icon {
    color: #93c119;
}

.price-badge:hover .price-icon {
    color: #7da416;
}

.price-edit-icon {
    opacity: .6;
    color: #556270;
    transition: opacity .2s ease, color .2s ease;
}

.price-badge:hover .price-edit-icon {
    opacity: 1;
    color: #1a2d08;
}

</style>

<style>
  /* Umsatz card */
 

.umsatz-main {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    cursor: pointer;
}

.umsatz-label {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-weight: 600;
    color: #6b7280;
}
 

.umsatz-total {
    font-size: 16px;
    font-weight: 700;
}

.umsatz-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    font-size: 11px;
    color: #6b7280;
}

.umsatz-meta strong {
    font-weight: 600;
    color: #111827;
}

.umsatz-hint {
    opacity: 0.85;
}

/* actions (buttons + badges) */
.umsatz-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-pill-sm {
    border-radius: 999px;
    padding: 0.3rem 0.9rem;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.11em;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    box-shadow: 0 9px 25px rgba(15, 23, 42, 0.25);
}

.btn-pill-sm i {
    font-size: 14px;
}

/* green pill (Preis-Info) */
.btn-pill-info {
    background: linear-gradient(135deg, #93c21c, #cfe09b);
    color: #ecfdf5;
}

/* neutral pill (edit) */
.btn-pill-edit {
    background: #f3f4f6;
    color: #111827;
}

/* smaller badge icons */
.badge-icons {
    display: flex;
    align-items: center;
    gap: 0.15rem;
}

.badge-icon {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    object-fit: contain;
    opacity: 0.4;
    filter: grayscale(0.2);
    transition: opacity 0.15s ease, transform 0.15s ease, filter 0.15s ease;
}

.badge-icons[data-tier="bronze"]   .badge-bronze,
.badge-icons[data-tier="silver"]   .badge-silver,
.badge-icons[data-tier="gold"]     .badge-gold,
.badge-icons[data-tier="platinum"] .badge-platinum {
    opacity: 1;
    filter: none;
    transform: translateY(-1px);
}

/* PRICE HISTORY DRAWER */

.ph-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.35);
    backdrop-filter: blur(6px);
    opacity: 0;
    pointer-events: none;
    z-index: 1090;
    transition: opacity 0.2s ease-out;
}

.ph-backdrop.is-open {
    opacity: 1;
    pointer-events: auto;
}

.ph-drawer {
    position: absolute;
    top: 0;
    right: 0;
    height: 100vh;
    width: min(420px, 100vw);
    background: radial-gradient(circle at 0 0, rgba(96, 165, 250, 0.25), transparent 50%),
                #020617;
    color: #e5e7eb;
    padding: 1.25rem 1.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: -24px 0 40px rgba(15, 23, 42, 0.6);
    transform: translateX(100%);
    transition: transform 0.25s cubic-bezier(0.22, 0.61, 0.36, 1);
    border-radius: 0 0 0 24px;
}

.ph-backdrop.is-open .ph-drawer {
    transform: translateX(0);
}

.ph-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.ph-title {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #93c21c;
}

.ph-subtitle {
    font-size: 0.85rem;
    color: #9ca3af;
}

.ph-close-btn {
    border: none;
    background: rgba(15, 23, 42, 0.7);
    border-radius: 999px;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #e5e7eb;
}

.ph-meta-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    font-size: 0.8rem;
    color: #9ca3af;
}

.ph-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.65rem;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(148, 163, 184, 0.4);
    margin-left: 0.25rem;
    font-weight: 500;
    color: #e5e7eb;
}

.ph-body {
    flex: 1;
    margin-top: 0.75rem;
    padding-right: 0.25rem;
    overflow-y: auto;
    font-size: 0.82rem;
}

/* entries */
.ph-entry {
    border-radius: 14px;
    padding: 0.65rem 0.8rem;
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(75, 85, 99, 0.6);
    margin-bottom: 0.5rem;
}

.ph-entry-head {
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    font-size: 0.78rem;
    color: #9ca3af;
}

.ph-entry-title {
    font-weight: 600;
    color: #e5e7eb;
}

.ph-entry-prices {
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.ph-entry-prices span {
    white-space: nowrap;
}

.ph-entry-meta {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #9ca3af;
}

.ph-loading,
.ph-error,
.ph-empty {
    padding: 0.5rem 0;
    color: #9ca3af;
}
.customer-nav-wrap {
    padding: 0.25rem 0;
}

.customer-nav {
    border-radius: 24px;
    background: radial-gradient(circle at 0% 0%, #cfe09b, transparent 55%), 
    radial-gradient(circle at 100% 100%, #cfe09b, transparent 55%), #f3f4f6;
    box-shadow:
        0 24px 60px rgba(15, 23, 42, 0.14),
        0 0 0 1px rgba(148, 163, 184, 0.25);
}

.customer-nav-inner {
    padding: 1.1rem 1.4rem;
}

/* LEFT */

.cn-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.cn-avatar {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    background: linear-gradient(135deg, #74b2d4, #c0d8ea);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ecfdf5;
    font-weight: 700;
    font-size: 20px;
    box-shadow: 0 16px 40px rgba(16, 185, 129, 0.45);
    flex-shrink: 0;
}

.cn-avatar-initials {
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.cn-welcome-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    color: #6b7280;
    margin-bottom: 2px;
}

.cn-name-line {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.cn-name {
    font-size: 17px;
    font-weight: 700;
    color: #111827;
}

.cn-firma {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #6b7280;
    margin-top: 2px;
}

.cn-meta-line {
    margin-top: 4px;
    font-size: 11px;
    color: #6b7280;
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.cn-dot {
    opacity: 0.6;
}

.cn-meta-pills {
    margin-top: 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.cn-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(209, 213, 219, 0.9);
    font-size: 11px;
    color: #374151;
}

.cn-icon {
    font-size: 13px;
}

/* edit button */

.btn-xs.cn-edit-btn {
    border-radius: 999px;
    padding: 0.1rem 0.45rem;
    font-size: 11px;
    background: #111827;
    color: #f9fafb;
    border: none;
}

/* MIDDLE */

.cn-middle .cn-block {
    padding: 0.35rem 0.5rem;
}

.cn-block-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-bottom: 2px;
}

.cn-block-text {
    font-size: 13px;
    color: #111827;
}

.cn-notes {
    max-height: 70px;
    overflow-y: auto;
    overflow-x: hidden;
    word-wrap: break-word;
    white-space: pre-wrap;
    font-size: 13px;
    text-align: left;
    cursor: pointer;
    padding: 0.3rem 0.4rem;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.75);
    border: 1px solid rgba(209, 213, 219, 0.7);
}

/* RIGHT (Umsatz) – existing styles reused + small tweaks */

.inner-col-umsatz {
    padding: 0.6rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #edf4ff, #f4fbf6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.9rem; 
    color: #0f172a;
}

.umsatz-main {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    cursor: pointer;
}

.umsatz-label {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-weight: 600;
    color: #6b7280;
}

.umsatz-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: linear-gradient(135deg, #74b2d4, #c0d8ea);
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
}

.umsatz-total {
    font-size: 16px;
    font-weight: 700;
}

.umsatz-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    font-size: 11px;
    color: #6b7280;
}

.umsatz-meta strong {
    font-weight: 600;
    color: #111827;
}

.umsatz-hint {
    opacity: 0.85;
}

.umsatz-actions {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-pill-sm {
    border-radius: 999px;
    padding: 0.25rem 0.8rem;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.11em;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    box-shadow: 0 9px 25px rgba(15, 23, 42, 0.25);
}

 

.btn-pill-edit {
    background: #f3f4f6;
    color: #111827;
}

.btn-pill-sm i {
    font-size: 14px;
}

/* smaller badges */
.badge-icons {
    display: flex;
    align-items: center;
    gap: 0.15rem;
}

.badge-icon {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    object-fit: contain;
    opacity: 0.35;
    filter: grayscale(0.2);
    transition: opacity 0.15s ease, transform 0.15s ease, filter 0.15s ease;
}

.badge-icons[data-tier="bronze"]   .badge-bronze,
.badge-icons[data-tier="silver"]   .badge-silver,
.badge-icons[data-tier="gold"]     .badge-gold,
.badge-icons[data-tier="platinum"] .badge-platinum {
    opacity: 1;
    filter: none;
    transform: translateY(-1px);
}

/* responsive tweaks */
@media (max-width: 991.98px) {
    .inner-col-umsatz {
        border-radius: 18px;
    }
}

@media (max-width: 767.98px) {
    .customer-nav-inner {
        padding: 0.9rem;
    }
}

</style>

<style>
.time-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eef2ff;
    color: #1e293b;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1px solid #cbd5f5;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    transition: all .18s ease;
    margin-bottom: 4px;
}
.time-badge:hover {
    background: #e0f2fe;
    border-color: #60a5fa;
    color: #0f172a;
    box-shadow: 0 1px 4px rgba(15,23,42,.18);
}
.time-badge i {
    width: 14px;
    height: 14px;
}

/* CARD SHELL */
.project-link.project-card {
    display: flex;
    flex-direction: column;
    padding: 8px 10px;
    border-radius: 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    gap: 6px;
}

/* TOP AREA */
.project-card-main {
    display: flex;
    align-items: flex-start;
}

.project-card-title-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    width: 100%;
}

.project-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #93c119;
    margin-top: 4px;
    flex-shrink: 0;
}

.project-card-title-block {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.project-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.project-card-meta {
    font-size: 11px;
    color: #6b7280;
}

.project-card-meta .meta-sep {
    margin: 0 4px;
    opacity: 0.6;
}

/* address line */
.project-card-meta-address {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #9ca3af;
    line-height: 1.3;
}

.project-card-meta-address .feather {
    width: 12px;
    height: 12px;
}

/* BOTTOM STRIP */
.project-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: .5rem;
}

.project-footer-right {
    display: flex;
    gap: .35rem;
    align-items: center;
}

.project-metric {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    padding: .25rem .6rem;
    font-size: .76rem;
    line-height: 1.1;
    cursor: pointer;
}

.project-metric .metric-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
}

.project-metric .metric-icon i {
    width: 16px;
    height: 16px;
    font-size: 14px;
}

.project-metric .metric-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.project-metric .metric-label {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #9ca3af;
}

.project-metric .metric-value {
    font-size: .8rem;
    font-weight: 600;
    color: #111827;
}

.project-metric--time {
    border-color: #bfdbfe;
    background: #eff6ff;
}

.project-metric--price {
    border-color: #facc15;
    background: #fffbeb;
}


.project-status-pill {
    font-size: 10px;
    padding: 9px 10px;
    border-radius: 999px;
    background: #e5e7eb;
    color: #111827;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-right: 4px;
}

/* metrics on the right */
.project-footer-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* METRIC PILLS (ZEIT / PREIS) */
.project-card .project-metric {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 9px;
    border-radius: 18px !important;
    border: 1px solid #d1d5db;
    background: #ffffff;
    font-size: 10px;
    cursor: pointer;
    min-height: 32px;
    line-height: 1.1;
    transition: background 0.15s ease, border-color 0.15s ease,
                box-shadow 0.15s ease, transform 0.05s ease;
}

/* override old price-badge look only inside card */
.project-card .price-badge {
    border-radius: 6px;
    border-width: 1px;
    padding: 4px 9px;
    background: #ffffff;
    box-shadow: none;
}

/* hover */
.project-card .project-metric:hover {
    box-shadow: 0 1px 5px rgba(15, 23, 42, 0.12);
    transform: translateY(-1px);
}

/* ZEIT */
.project-metric--time {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #166534;
}

/* PREIS */
 

/* icon */
.project-card .metric-icon {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    border: 1.5px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.project-card .metric-icon .feather {
    width: 12px;
    height: 12px;
}

/* label + value */
.metric-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    line-height: 1.1;
}

.metric-label {
    font-size: 8px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #6b7280;
}

.project-metric--time .metric-label {
    color: #15803d;
}

.metric-value {
    font-size: 11px;
    font-weight: 700;
    color: #111827;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .project-link.project-card {
        padding: 8px;
    }

    .project-card-footer {
        flex-direction: column;
        align-items: flex-start;
    }

    .project-footer-right {
        width: 100%;
        flex-wrap: wrap;
        justify-content: flex-start;
    }
}
.ph-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    z-index: 1050;
    display: none;
}

.ph-backdrop.is-open {
    display: flex;
    justify-content: flex-end;
}

.ph-drawer {
    width: 971px;
    max-width: 100%;
    background: #ffffff;
    box-shadow: -24px 0 60px rgba(15, 23, 42, 0.30);
    display: flex;
    flex-direction: column;
    max-height: 100vh;
}

.ph-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ph-title {
    font-weight: 600;
    font-size: 1.1rem;
}

.ph-subtitle {
    font-size: 0.8rem;
    color: #6b7280;
}

.ph-close-btn {
    border: 0;
    background: transparent;
    padding: 0.25rem;
    border-radius: 999px;
}

.ph-close-btn:hover {
    background: #f3f4f6;
}

.ph-body {
    padding: 1rem 1.5rem 1.25rem;
    overflow-y: auto;
}

/* Info grid */
.pt-info-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    grid-gap: 0.75rem;
    margin-bottom: 1rem;
}

.pt-info-card {
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 0.65rem 0.75rem;
    border: 1px solid #e5e7eb;
}

.pt-info-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #6b7280;
    display: flex;
    align-items: center;
}

.pt-info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #111827;
    margin-top: 0.15rem;
}

.pt-info-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.1rem;
}

.pt-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.1rem 0.5rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    background: #eff6ff;
    color: #1d4ed8;
}

/* Stats row */
.pt-stats-row {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.5fr);
    grid-gap: 0.9rem;
    margin-bottom: 1.1rem;
}

.pt-chart-card {
    border-radius: 0.9rem;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 0.6rem 0.75rem 0.75rem;
}

.pt-section-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: #111827;
}

.pt-chart-legend {
    font-size: 0.7rem;
    color: #6b7280;
}

.pt-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    margin-right: 0.25rem;
    vertical-align: middle;
}

.pt-dot-used {
    background: #ef4444;
}

.pt-dot-remaining {
    background: #10b981;
}

.pt-chart-wrapper {
    margin-top: 0.25rem;
    min-height: 160px;
}

/* KPI cards */
.pt-stat-cards {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    grid-gap: 0.5rem;
}

.pt-stat-card {
    border-radius: 0.8rem;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    padding: 0.55rem 0.7rem;
}

.pt-stat-label {
    font-size: 0.7rem;
    color: #6b7280;
}

.pt-stat-value {
    font-size: 0.95rem;
    font-weight: 700;
    margin-top: 0.15rem;
    color: #111827;
}

.pt-stat-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    font-size: 0.65rem;
    margin-top: 0.25rem;
}

.pt-pill-base {
    background: #eff6ff;
    color: #1d4ed8;
}

.pt-pill-extra {
    background: #fef3c7;
    color: #92400e;
}

.pt-pill-used {
    background: #fee2e2;
    color: #b91c1c;
}

.pt-pill-remaining {
    background: #dcfce7;
    color: #166534;
}

/* Sections */
.pt-section {
    margin-bottom: 1rem;
}

.pt-section-head {
    margin-bottom: 0.4rem;
}

.pt-timeline .pt-node {
    border-left: 2px solid #e5e7eb;
    padding-left: 0.6rem;
    margin-bottom: 0.75rem;
    position: relative;
}

.pt-timeline .pt-node::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 3px;
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: #10b981;
}

.pt-node-head {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #4b5563;
    margin-bottom: 0.1rem;
}

.pt-node-time {
    font-weight: 600;
}

.pt-node-body {
    font-size: 0.78rem;
    color: #374151;
}

.pt-section-split {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.1fr);
    grid-gap: 0.75rem;
}

/* Empty / loading */
.ph-empty,
.ph-loading,
.ph-error {
    font-size: 0.78rem;
    color: #6b7280;
}

/* Responsive */
@media (max-width: 991.98px) {
    .ph-drawer {
        width: 100%;
    }
}

@media (max-width: 767.98px) {
    .pt-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .pt-stats-row {
        grid-template-columns: minmax(0, 1fr);
    }
    .pt-section-split {
        grid-template-columns: minmax(0, 1fr);
    }
}

</style>

<!-- feed style  -->
  <style>
    /* ================== COMPACT CUSTOMER FEED STRIP ================== */

.customer-feed-strip {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  padding: 4px 8px;
  margin-top: 0.6rem;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.6);
  background: linear-gradient(90deg, #f9fafb, #eef2ff);
  font-size: 0.8rem;
  color: #111827;
  min-height: 40px;
}

/* left icon */

.customer-feed-strip .cfs-icon {
  flex: 0 0 auto;
  width: 30px;
  height: 30px;
  border-radius: 999px;
  background: #0f172a;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.customer-feed-strip .cfs-icon i {
  font-size: 15px;
  color: #f9fafb;
}

/* middle */

.customer-feed-strip .cfs-main {
  flex: 1 1 auto;
  min-width: 0;
}

.customer-feed-strip .cfs-line {
  display: block;
}

.customer-feed-strip .cfs-line-top {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 1px;
}

/* pill (status) */

.customer-feed-strip .cfs-pill {
  display: inline-flex;
  align-items: center;
  padding: 0.08rem 0.6rem;
  border-radius: 999px;
  border: 1px solid rgba(59, 130, 246, 0.6);
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 0.72rem;
  white-space: nowrap;
}

/* title */

.customer-feed-strip .cfs-title {
  font-weight: 600;
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* main text line */

.customer-feed-strip .cfs-text {
  font-size: 0.78rem;
  color: #ffffffff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* bottom line: time + counter */

.customer-feed-strip .cfs-bottom {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 1px;
}

.customer-feed-strip .cfs-time {
  font-size: 0.72rem;
  color: #ffffffff;
  white-space: nowrap;
}

.customer-feed-strip .cfs-counter {
  font-size: 0.72rem;
  color: #9ca3af;
}

/* empty state */

.customer-feed-strip .cfs-empty {
  display: none;
  flex-direction: column;
  font-size: 0.78rem;
}

.customer-feed-strip .cfs-empty-label {
  font-weight: 600;
}

.customer-feed-strip .cfs-empty-sub {
  color: #9ca3af;
  font-size: 0.75rem;
}

/* error text */

.customer-feed-strip .cfs-error {
  margin-top: 2px;
}

/* controls (right) */

.customer-feed-strip .cfs-controls {
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  gap: 0.1rem;
}

.customer-feed-strip .cfs-btn {
     border: none;
    background: #ffffff;
    border-radius: 1000px !important;
    padding: 6px 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.12s 
ease, transform 0.08s 
ease;
}

.customer-feed-strip .cfs-btn i {
  font-size: 13px;
  color: #4b5563;
}

.customer-feed-strip .cfs-btn:hover {
  background: rgba(15, 23, 42, 0.06);
}

.customer-feed-strip .cfs-btn:active {
  transform: translateY(1px);
}

/* expand button a bit stronger */

.customer-feed-strip .cfs-btn-expand {
  margin-left: 2px;
  background: #0f172a;
}

.customer-feed-strip .cfs-btn-expand i {
  color: #f9fafb;
}

.customer-feed-strip .cfs-btn-expand:hover {
  background: #020617;
}

/* integration with your JS: when empty */

.customer-feed-strip.is-empty .cfs-line {
  display: none;
}

.customer-feed-strip.is-empty .cfs-empty {
  display: flex;
}

/* responsive */

@media (max-width: 768px) {
  .customer-feed-strip {
    flex-wrap: wrap;
    row-gap: 0.25rem;
  }
}

  </style>

  <style>
/* --------------------------------------------------
   COMPACT CUSTOMER FEED STRIP
-------------------------------------------------- */

.customer-live-feed {
  display: flex;
  align-items: stretch;
  padding: 0.55rem 0.7rem;
  border-radius: 16px;
  background:#2d3e4f; 
  color: #e5e7eb;
  font-size: 0.82rem;
  column-gap: 0.6rem;
  max-width: 100%;
}

.customer-live-feed.is-empty {
  opacity: 0.9;
}

.customer-live-feed .live-feed-icon {
  flex: 0 0 auto;
  width: 34px;
  height: 34px;
  border-radius: 999px;
  background: rgba(15,23,42,.7);
  border: 1px solid rgba(148,163,184,.45);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.1rem;
}

.customer-live-feed .live-feed-icon i {
  font-size: 16px;
}

/* main text area */
.customer-live-feed .live-feed-main {
  flex: 1 1 auto;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.customer-live-feed .live-feed-line {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.customer-live-feed .live-feed-title {
  font-weight: 600;
  font-size: 0.85rem;
  color: #f9fafb;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

.customer-live-feed .live-feed-text {
  font-size: 0.8rem;
  color: #cbd5f5;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.customer-live-feed .live-feed-meta {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  margin-top: 0.1rem;
  font-size: 0.75rem;
  color: #9ca3af;
}

.customer-live-feed .live-feed-pill {
  border-radius: 999px;
  padding: 0.1rem 0.55rem;
  font-size: 0.72rem;
  font-weight: 500;
  border: none;
  background: rgba(34,197,94,.14);
  color: #bbf7d0;
}

.customer-live-feed .live-feed-time {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.customer-live-feed .live-feed-time i {
  font-size: 0.8rem;
}

/* small counter "1 / 5" */
.customer-live-feed .live-feed-counter {
  margin-left: auto;
  font-size: 0.7rem;
  opacity: .8;
}

/* avatars row inside strip (optional) */
.live-feed-employees {
  margin-top: 0.15rem;
  display: flex;
  align-items: center;
}

.live-feed-avatar {
  width: 18px;
  height: 18px;
  border-radius: 999px;
  overflow: hidden;
  border: 1px solid rgba(148,163,184,0.8);
  margin-right: -4px;
  background: #e5e7eb;
}

.live-feed-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.live-feed-employees-more {
  margin-left: 6px;
  font-size: 0.7rem;
  color: #9ca3af;
}

/* empty + error messages inside strip */
.customer-live-feed .live-feed-empty {
  font-size: 0.78rem;
  color: #cbd5f5;
}

.customer-live-feed .live-feed-error {
  margin-top: 0.1rem;
  font-size: 0.75rem;
}

/* right side: control buttons */
.customer-live-feed .live-feed-controls {
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  column-gap: 0.2rem;
  margin-left: 0.25rem;
}

.customer-live-feed .live-feed-btn {
  width: 28px;
  height: 28px;
  border-radius: 999px;
  border: 1px solid rgba(148,163,184,.55);
  background: radial-gradient(circle at 30% 0%, rgba(148,163,184,.25), transparent 70%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin: 0;
  color: #e5e7eb;
  cursor: pointer;
  transition:
    background-color .15s ease,
    transform .1s ease,
    box-shadow .15s ease,
    border-color .15s ease;
}

.customer-live-feed .live-feed-btn i {
  font-size: 13px;
}

.customer-live-feed .live-feed-btn:hover {
  background: rgba(30,64,175,.95);
  box-shadow: 0 0 0 1px rgba(129,140,248,.9);
  transform: translateY(-1px);
}

.customer-live-feed .live-feed-btn:active {
  transform: translateY(0);
  box-shadow: none;
}

/* expand button special style */
.customer-live-feed .live-feed-btn[data-feed-expand] {
  background: linear-gradient(135deg, #22c55e, #3b82f6);
  border-color: transparent;
}

.customer-live-feed .live-feed-btn[data-feed-expand]:hover {
  background: linear-gradient(135deg, #16a34a, #2563eb);
}

/* hide strip on small screens if you want tighter layout
@media (max-width: 576px) {
  .customer-live-feed {
    border-radius: 12px;
    padding: 0.6rem 0.7rem;
  }
}
*/

/* --------------------------------------------------
   MODAL FOR FULL FEED LIST
-------------------------------------------------- */

.feed-modal .modal-content {
  border-radius: 18px;
  overflow: hidden;
  border: none;
  box-shadow:
    0 18px 40px rgba(15,23,42,.38),
    0 0 0 1px rgba(15,23,42,.1);
}

/* header */
.feed-modal-header {
  background: radial-gradient(circle at 0 0, #d1e1a1, transparent 55%),
              radial-gradient(circle at 100% 100%, #d1e1a1, transparent 55%),
              #0f172a;
  color: #e5e7eb;
  padding: 0.8rem 1.2rem;
  border-bottom: 1px solid rgba(15,23,42,.5);
}

.feed-modal-title-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 999px;
  background-color: rgba(15, 23, 42, 0.7);
  border: 1px solid rgba(148,163,184,.6);
  margin-right: 0.5rem;
}

.feed-modal-title-icon i {
  font-size: 15px;
}

.feed-modal-header .modal-title {
  font-size: 0.98rem;
  font-weight: 600;
  line-height: 1.2;
}

.feed-modal-header .small {
  font-size: 0.78rem;
  opacity: .85;
}

/* close */
.feed-modal-close {
  color: #cbd5f5;
  opacity: .8;
}

.feed-modal-close span {
  font-size: 26px;
  line-height: 1;
}

.feed-modal-close:hover {
  opacity: 1;
}

/* body */
.feed-modal-body {
  background: #f3f4f6;
  padding: 1rem 1.2rem 1.1rem;
}

/* toolbar */
.feed-modal-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  row-gap: 0.4rem;
  column-gap: 0.5rem;
  margin-bottom: 0.7rem;
}

.feed-modal-toolbar .btn-group .btn {
  font-size: 0.75rem;
  padding: 0.25rem 0.6rem;
  border-radius: 999px !important;
}

.feed-modal-toolbar .btn-group .btn.active {
  background: #0f172a;
  color: #f9fafb;
  border-color: #0f172a;
}

/* search + sort */
.feed-modal-search {
  flex: 1 1 230px;
  min-width: 180px;
}

.feed-modal-search .input-group-text {
  background: #e5e7eb;
  border-color: #d1d5db;
  padding: 0.2rem 0.4rem;
}

.feed-modal-search input.form-control {
  border-color: #d1d5db;
  font-size: 0.78rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

.feed-modal-body select[data-feed-modal-sort] {
  width: 170px;
  font-size: 0.78rem;
}

/* list container */
.feed-modal-list {
  max-height: 60vh;
  overflow-y: auto;
  padding-right: 0.3rem;
  margin-right: -0.2rem;
}

/* custom scrollbar (optional) */
.feed-modal-list::-webkit-scrollbar {
  width: 6px;
}
.feed-modal-list::-webkit-scrollbar-thumb {
  background: rgba(148,163,184,.9);
  border-radius: 999px;
}
.feed-modal-list::-webkit-scrollbar-track {
  background: transparent;
}

/* empty state */
.feed-modal-empty {
  padding: 1.1rem 0.5rem;
  text-align: center;
  color: #6b7280;
  font-size: 0.86rem;
}

/* list item */
.feed-modal-item {
  display: flex;
  padding: 0.7rem 0.25rem;
  border-bottom: 1px solid rgba(148,163,184,.45);
}

.feed-modal-item:last-child {
  border-bottom: none;
}

/* left icon pill */
.feed-modal-item-icon {
  flex: 0 0 40px;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  margin-right: 0.5rem;
  margin-top: 0.1rem;
}

.feed-modal-icon-pill {
  width: 30px;
  height: 30px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  background: #e5e7eb;
  color: #111827;
}

.feed-modal-icon-pill--product     { background: rgba(34,197,94, .16);  color: #15803d; }
.feed-modal-icon-pill--appointment { background: rgba(59,130,246,.16);  color: #1d4ed8; }
.feed-modal-icon-pill--task        { background: rgba(245,158,11, .16); color: #b45309; }
.feed-modal-icon-pill--ticket      { background: rgba(236,72,153, .16); color: #be185d; }
.feed-modal-icon-pill--history     { background: rgba(107,114,128, .18); color: #374151; }

/* main content */
.feed-modal-item-main {
  flex: 1 1 auto;
  min-width: 0;
}

.feed-modal-item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.1rem;
}

.feed-modal-item-title {
  font-weight: 600;
  color: #111827;
  font-size: 0.94rem;
  margin-right: 0.5rem;
}

.feed-modal-item-time {
  font-size: 0.78rem;
  color: #6b7280;
  white-space: nowrap;
}

.feed-modal-item-time i {
  font-size: 0.78rem;
  margin-right: 2px;
}

.feed-modal-item-text {
  font-size: 0.84rem;
  color: #374151;
  margin-bottom: 0.2rem;
  word-break: break-word;
}

/* avatars in modal */
.feed-modal-avatars {
  margin-top: 0.25rem;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.18rem;
}

.feed-modal-avatar {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  overflow: hidden;
  border: 1px solid rgba(148,163,184,0.85);
  background: #e5e7eb;
}

.feed-modal-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.feed-modal-avatars-more {
  font-size: 0.75rem;
  color: #6b7280;
  margin-left: 1px;
}

/* meta line */
.feed-modal-item-meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.3rem;
  margin-top: 0.15rem;
}

.feed-modal-pill {
  font-size: 0.72rem;
  border-radius: 999px;
  padding: 0.15rem 0.6rem;
  background: #e5e7eb;
  color: #111827;
}

.feed-modal-kind-label {
  font-size: 0.75rem;
  color: #6b7280;
}

/* footer count text */
[data-feed-modal-count] {
  font-size: 0.78rem;
}

/* responsive tweaks */
@media (max-width: 768px) {
  .feed-modal-item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.1rem;
  }
  .feed-modal-item-time {
    margin-top: 1px;
  }
  .feed-modal-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .feed-modal-body select[data-feed-modal-sort] {
    width: 100%;
  }
}
</style>

<style>
.ccp-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.ccp-modal-backdrop.is-open {
    display: flex;
}

.ccp-modal-panel {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.25);
    max-width: 960px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: inherit;
}

.ccp-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ccp-modal-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #111827;
}

.ccp-modal-close-btn {
    border: 0;
    background: transparent;
    padding: 4px;
    border-radius: 999px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.ccp-modal-close-btn:hover {
    background: #f3f4f6;
}

.ccp-modal-body {
    padding: 14px 18px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow: auto;
}

.ccp-modal-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ccp-modal-table-wrap {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 8px;
    background: #f9fafb;
}

.ccp-modal-form-wrap {
    border-top: 1px solid #e5e7eb;
    padding-top: 10px;
}

.ccp-modal-actions {
    margin-top: 10px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
</style>

<style>
    /* --- NEW & UPDATED STYLES FOR HEADER --- */

    /* 1. Product Initial Badges */
    .product-initials-row {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 8px;
        margin-bottom: 8px;
    }

    .product-mini-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: help; /* Shows '?' cursor to indicate hover */
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .product-mini-badge:hover {
        transform: scale(1.1);
        border-color: #93c119;
        color: #93c119;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* 2. Smaller Notes (Moved to Left) */
    .cn-notes-small {
        font-size: 11px;
        line-height: 1.3;
        color: #4b5563;
        background: rgba(255,255,255,0.6);
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 6px 8px;
        max-height: 60px; /* Smaller height */
        overflow-y: auto;
        cursor: pointer;
        margin-top: 6px;
    }
    .cn-notes-small:hover {
        background: #fff;
        border-color: #93c119;
    }

    /* 3. Compact Middle Column (Stacked Contact/Address) */
    .cn-info-block {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 6px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .cn-info-block:last-child {
        border-bottom: none;
    }
    .cn-info-icon {
        color: #9ca3af;
        font-size: 14px;
        margin-top: 2px;
    }
    .cn-info-content {
        font-size: 12px;
        color: #1f2937;
        line-height: 1.4;
    }

    /* 4. Compact Umsatz Box */
    .inner-col-umsatz.compact {
        padding: 0.5rem 0.8rem; /* Reduced padding */
        gap: 0.5rem;
    }
    .inner-col-umsatz.compact .umsatz-total {
        font-size: 14px; /* Smaller font */
    }
    .inner-col-umsatz.compact .umsatz-label {
        font-size: 10px;
        margin-bottom: 0;
    }
    .inner-col-umsatz.compact .btn-pill-sm {
        padding: 0.2rem 0.6rem;
        font-size: 10px;
    }
</style>

    <div class="customer-wrapper">
      <div class="customer-nav-wrap mb-1">
          @php
            $tier           = $customer->tier; // null if not explicitly set
            $hasPurchase    = (float) $customer->total_purchase > 0;

            $purchaseDate   = $customer->purchase_date
                ? \Carbon\Carbon::parse($customer->purchase_date)->format('d.m.Y')
                : '–';

            $totalPurchase  = number_format((float)$customer->total_purchase, 2, ',', '.');

            $created        = \Carbon\Carbon::parse($customer->created_at);
            $initials       = trim(mb_substr($customer->name ?? '', 0, 1) . mb_substr($customer->lastname ?? '', 0, 1));

            // Tier-Label-Logik:
            // - Wenn KEIN Umsatz: "Kein Kauf"
            // - Wenn Umsatz > 0 und tier gesetzt: "Bronze Kunde" / "Gold Kunde" etc.
            // - Wenn Umsatz > 0 und tier NICHT gesetzt: einfach "Kunde"
            if (!$hasPurchase) {
                $tierLabel = 'Kein Kauf';
            } else {
                $tierLabel = $tier
                    ? ucfirst($tier).' Kunde'
                    : 'Kunde';
            }

            $purchaseStatus = $customer->purchase_status ?? 'unbekannt';
        @endphp


             <div class="customer-nav shadow-sm">
                <div class="customer-nav-inner">
                    <div class="row align-items-start gx-3 gy-3"> 

                        {{-- LEFT: Avatar, Name, Badges, Product Initials, Notes --}}
                        <div class="col-xl-4 col-lg-5 col-md-6">
                            <div class="d-flex gap-3">
                                <div class="cn-avatar">
                                    <span class="cn-avatar-initials">{{ $initials ?: 'K' }}</span>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="cn-welcome-label">Kundenprofil</div>
                                    
                                    <div class="cn-name-line d-flex align-items-center flex-wrap gap-1">
                                        <div class="cn-name">
                                            {{ $customer->title ?? '' }} {{ $customer->academic_title ?? '' }}  {{ $customer->name }} {{ $customer->lastname }}
                                        </div>
                                        
                                        <button type="button" class="btn btn-xs cn-edit-btn customer-edit-trigger"
                                                data-customer-id="{{ $customer->id }}" title="Bearbeiten">
                                            <i class="feather icon-edit-2"></i>
                                        </button>

                                        <button type="button" class="btn btn-xs cn-edit-btn cn-contact-people-trigger ms-1"
                                                data-customer-id="{{ $customer->id }}" title="Kontaktpersonen">
                                            <i class="feather icon-users"></i>
                                            <span class="ms-1" id="contactPeopleCountBadge-{{ $customer->id }}">0</span>
                                        </button>
                                    </div>

                                    @if($customer->firma)
                                        <div class="cn-firma">{{ $customer->firma }}</div>
                                    @endif

                                    <div class="cn-meta-line">
                                        <span>ID #{{ $customer->id }}</span>
                                        <span class="cn-dot">•</span>
                                        <span>{{ $created->format('d.m.Y') }}</span>
                                    </div>

                                    <div class="cn-meta-pills">
                                        <span class="cn-pill">
                                            <i class="feather icon-tag cn-icon"></i>
                                            {{ $customer->source ?: 'unbekannt' }}
                                        </span>
                                        <span class="cn-pill">
                                            <i class="feather icon-award cn-icon"></i>
                                            {{ $tierLabel }}
                                        </span>
                                    </div>

                                    @if(isset($customer->leadProductLists) && $customer->leadProductLists->count() > 0)
                                        <div class="product-initials-row">
                                            @foreach($customer->leadProductLists as $lp)
                                                @php
                                                    // 1. Get Initials (First 2 letters of article_group)
                                                    $pInitial = $lp->product->initial;
                                                    
                                                    // 2. Map Status to German
                                                    $statusMap = [
                                                        'open'      => 'Offen',
                                                        'lead'      => 'Lead (Anfrage)',
                                                        'offer'     => 'Angebot',
                                                        'project'   => 'Projekt/Montage',
                                                        'archive'   => 'Archiv',
                                                        'junk'      => 'Junk',
                                                        'feedback'  => 'Feedback',
                                                        'completed' => 'Abgeschlossen'
                                                    ];
                                                    $germanStatus = $statusMap[$lp->status] ?? ucfirst($lp->status);
                                                    
                                                    // Tooltip Text
                                                    $tooltipText = ($lp->product->article_group ?? 'Produkt') . ": " . $germanStatus;
                                                @endphp
                                                
                                                <span class="product-mini-badge" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top" 
                                                    title="{{ $tooltipText }}">
                                                    {{ $pInitial }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="cn-notes-small" 
                                        onclick="showFullNote(this)" 
                                        data-note="{{ $customer->info ?? '' }}"
                                        title="Klicken für vollständige Notiz">
                                        <i class="feather icon-file-text me-1"></i>
                                        @if(!empty($customer->info))
                                            {{ Str::limit($customer->info, 80) }}
                                        @else
                                            <span class="text-muted font-italic">Keine Notizen hinterlegt...</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- MIDDLE: Address & Contact (Stacked) --}}
                        <div class="col-xl-4 col-lg-4 col-md-6 border-start-md">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                
                                <div class="cn-info-block">
                                    <div class="cn-info-icon"><i class="feather icon-map-pin"></i></div>
                                    <div class="cn-info-content">
                                        <div class="fw-bold text-muted text-uppercase" style="font-size:10px; letter-spacing:1px;">Adresse</div>
                                        <div>{{ $customer->street }}</div>
                                        <div>{{ $customer->postcode }} {{ $customer->city }}</div>
                                    </div>
                                </div>

                                <div class="cn-info-block">
                                    <div class="cn-info-icon"><i class="feather icon-phone"></i></div>
                                    <div class="cn-info-content">
                                        <div class="fw-bold text-muted text-uppercase" style="font-size:10px; letter-spacing:1px;">Kontakt</div>
                                        @if($customer->phone) <div>{{ $customer->phone }}</div> @endif
                                        @if($customer->telephone) <div>{{ $customer->telephone }}</div> @endif
                                        <div class="text-truncate" style="max-width: 220px;">{{ $customer->email }}</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- RIGHT: Compact Umsatz --}}
                        <div class="col-xl-4 col-lg-3 col-md-12">
                            <div class="inner-col inner-col-umsatz compact h-100 justify-content-center">
                                
                                <div class="umsatz-main total-purchase-trigger"
                                    role="button" tabindex="0"
                                    data-customer-id="{{ $customer->id }}"
                                    data-total-purchase-raw="{{ (float)$customer->total_purchase }}"
                                    data-customer-name="{{ $customer->name }} {{ $customer->lastname }}">
                                    
                                    <div class="umsatz-label">
                                        <span class="umsatz-dot"></span>
                                        <span>Umsatz</span>
                                    </div>

                                    <div class="umsatz-total tp-display" id="customerTotalPurchase">
                                        {{ $totalPurchase }} €
                                    </div>

                                    <div class="umsatz-meta">
                                        <span class="umsatz-purchase-date" style="font-size:10px;">
                                            Letzter: <strong>{{ $purchaseDate }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-1">
                                    <button type="button"
                                            class="btn btn-pill-sm btn-pill-info btn-price-info w-100 justify-content-center"
                                            data-customer-id="{{ $customer->id }}"
                                            data-customer-name="{{ $customer->name }} {{ $customer->lastname }}">
                                        <i class="feather icon-activity"></i> Info
                                    </button>

                                    <button type="button"
                                            class="btn btn-pill-sm btn-pill-edit total-purchase-trigger w-100 justify-content-center"
                                            data-customer-id="{{ $customer->id }}"
                                            data-total-purchase-raw="{{ (float)$customer->total_purchase }}"
                                            title="Bearbeiten">
                                        <i class="feather icon-edit-2"></i> Edit
                                    </button>

                                    <button type="button"
                                            class="btn p-0 border-0 bg-transparent badge-trigger w-100 mt-1"
                                            data-customer-id="{{ $customer->id }}"
                                            @if(!$hasPurchase && !$tier) disabled @endif
                                            title="{{ $tierLabel }}">
                                        <div class="badge-icons justify-content-center" data-tier="{{ $tier ?: ($hasPurchase ? 'bronze' : '') }}">
                                            <img src="{{ asset('icons/bronze.png')}}"   alt="Bronze"   class="badge-icon badge-bronze">
                                            <img src="{{ asset('icons/silver.png')}}"   alt="Silver"   class="badge-icon badge-silver">
                                            <img src="{{ asset('icons/gold.png')}}"      alt="Gold"     class="badge-icon badge-gold">
                                            <img src="{{ asset('icons/platinum.png')}}" alt="Platinum" class="badge-icon badge-platinum">
                                        </div>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div> 
                </div>
            </div>
 
        {{-- COMPACT CUSTOMER FEED STRIP --}}
        <div class="customer-feed-strip customer-live-feed"
            data-feed-root
            data-customer-id="{{ $customer->id }}"
            data-feed-limit="10"
            data-customer-title="{{ $customer->title }} {{ $customer->name }} {{ $customer->lastname }}">

            {{-- left: icon --}}
            <div class="cfs-icon">
                <i class="feather icon-activity"></i>
            </div>

            {{-- middle: one-line "newsletter" --}}
            <div class="cfs-main">

                {{-- when there IS an item --}}
                <div class="cfs-line" data-feed-line>
                    <div class="cfs-line-top">
                        <span class="cfs-pill" data-feed-pill>Info</span>
                        <span class="cfs-title" data-feed-title>Aktivität</span>
                    </div>

                    <div class="cfs-text" data-feed-text></div>

                    <div class="cfs-bottom">
                        <span class="cfs-time" data-feed-time>–</span>
                        <span class="cfs-counter" data-feed-counter></span>
                    </div>
                </div>

                {{-- when there is NO item --}}
                <div class="cfs-empty" data-feed-empty>
                    <span class="cfs-empty-label">Keine Aktivitäten</span>
                    <span class="cfs-empty-sub">Noch keine Produkte, Termine oder Aufgaben.</span>
                </div>

                {{-- error text (optional) --}}
                <div class="cfs-error text-danger small d-none" data-feed-error></div>
            </div>

            {{-- right: small controls + expand --}}
            <div class="cfs-controls">
                <button type="button"
                        class="cfs-btn"
                        title="Zurück"
                        data-feed-prev>
                    <i class="feather icon-chevrons-left"></i>
                </button>

                <button type="button"
                        class="cfs-btn"
                        title="Pause / Abspielen"
                        data-feed-toggle>
                    <i class="feather icon-pause" data-feed-icon-pause></i>
                    <i class="feather icon-play d-none" data-feed-icon-play></i>
                </button>

                <button type="button"
                        class="cfs-btn"
                        title="Weiter"
                        data-feed-next>
                    <i class="feather icon-chevrons-right"></i>
                </button>

                {{-- expand → opens your modal with full list + filters --}}
                <button type="button"
                        class="cfs-btn cfs-btn-expand"
                        title="Liste öffnen"
                        data-feed-expand>
                    <i class="feather icon-maximize-2"></i>
                </button>
            </div>
        </div> 
      </div>


      {{-- CUSTOMER FEED MODAL --}}
        <div class="modal fade feed-modal" id="customerFeedModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">

                    <div class="feed-modal-header modal-header">
                        <div class="d-flex align-items-center">
                            <span class="feed-modal-title-icon mr-2">
                                <i class="feather icon-activity"></i>
                            </span>
                            <div>
                                <h5 class="modal-title mb-0" data-feed-modal-title>Aktivitäten</h5>
                                <div class="small text-black" data-feed-modal-subtitle></div>
                            </div>
                        </div>
                        <button type="button"
                                class="close feed-modal-close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="feed-modal-body modal-body">
                        {{-- Toolbar: kind filter + search + sort --}}
                        <div class="feed-modal-toolbar mb-2">
                            <div class="btn-group btn-group-sm mb-2 mb-sm-0" role="group">
                                <button type="button" class="btn btn-outline-secondary active"
                                        data-feed-modal-kind="all">
                                    Alle
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-feed-modal-kind="product">
                                    Produkte
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-feed-modal-kind="appointment">
                                    Termine
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-feed-modal-kind="task">
                                    Aufgaben
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-feed-modal-kind="ticket">
                                    Tickets
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        data-feed-modal-kind="history">
                                    Historie
                                </button>
                            </div>

                            <div class="d-flex align-items-center">
                                <div class="feed-modal-search input-group input-group-sm mr-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="feather icon-search"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="Suchen..."
                                        data-feed-modal-search>
                                </div>

                                <select class="form-control form-control-sm"
                                        data-feed-modal-sort>
                                    <option value="desc">Neueste zuerst</option>
                                    <option value="asc">Älteste zuerst</option>
                                </select>
                            </div>
                        </div>

                        {{-- List --}}
                        <div class="feed-modal-list" data-feed-modal-list></div>

                        <div class="feed-modal-empty d-none" data-feed-modal-empty>
                            Keine Einträge gefunden.
                        </div>

                        <div class="small text-muted mt-2" data-feed-modal-count></div>
                    </div>

                </div>
            </div>
        </div>


  
      <div class="layout">
        <div class="customerSidebar" id="customerSidebar"> 
          <button class="minimize-btn" onclick="togglecustomerSidebar()">
              <i data-feather="chevrons-left"></i>
          </button>
            <button class="dashboard-btn"
                    onclick="showDashboard(this)"
                    data-customer-id="{{ request()->id }}"
                    data-alternative-id="{{ $alternative->first()->id }}">
                <i data-feather="grid"></i> <span class="text">Dashboard</span>
            </button>

            @foreach ($alternative as $key => $object)
              <div class="object-section">
                {{-- Object Header --}}
                <div class="object-header d-flex justify-content-between align-items-center" onclick="toggleObject('object{{ $key }}')">
                    <div class="d-flex align-items-center">
                        <i data-feather="home" class="mr-2"></i>
                        <div class="d-flex flex-column">
                            <span class="text font-weight-bold">{{ $object->object_name ?? 'Object' }}</span>
                            <small class="text-muted">
                                {{ $object->street }} {{ $object->postcode }} {{ $object->city }}
                            </small>
                        </div>
                    </div>

                    <!-- Picture placeholder -->

                     @php
                            $firstImage   = $screenshots->where('alternative_id', $object->id)->first();
                            $fullAddress  = trim($object->street . ', ' . $object->postcode . ' ' . $object->city);
                        @endphp

                        @if ($firstImage && !empty($firstImage->image))
                            {{-- Use the secure-image-byFilename route --}}
                            <img src="{{ asset('uploads/'.$firstImage->image) }}"
                                alt="{{ $firstImage->image_name ?? 'Screenshot' }}"
                                style="width: 100px; height: auto; object-fit: cover; cursor: pointer;"
                                onclick="openSidebarGallery(this)"
                                data-customer-id="{{ $customer->id }}"
                                data-alternative-id="{{ $object->id }}"
                                data-address="{{ $fullAddress }}">
                        @else
                            {{-- Placeholder image --}}
                            <img src="{{ asset('images/icons/placeholder.svg') }}"
                                alt="Object Image"
                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; cursor: pointer;"
                                onclick="openSidebarGallery(this)"
                                data-customer-id="{{ $customer->id }}"
                                data-alternative-id="{{ $object->id }}"
                                data-address="{{ $fullAddress }}">
                        @endif




                    {{-- Sidebar --}}
                    <div id="sidebarGallery{{ $object->id }}" class="sidebar-gallery p-3">
                      {{-- Header --}}
                      <div class="sidebar-header d-flex justify-content-between align-items-center mb-3">
                          <div>
                              <strong>{{ $object->street }} {{ $object->postcode }} {{ $object->city }}</strong>
                          </div>
                          <button onclick="closeSidebarGallery({{ $object->id }})" class="btn btn-sm btn-outline-secondary" title="Schließen">
                              &times;
                          </button>
                      </div>

                      {{-- Gallery Section --}}
                      <div class="gallery-wrapper mb-3" id="galleryImages{{ $object->id }}">
                          <span class="text-muted">📂 Bilder werden geladen...</span>
                      </div>

                      {{-- Screenshot Mode Select --}}
                      <div class="form-group mb-3">
                          <label for="screenshotMode{{ $object->id }}" class="font-weight-bold">Ansichtsmodus wählen:</label>
                          <select id="screenshotMode{{ $object->id }}" class="form-control form-control-sm">
                              <option value="satellite">Satellit</option>
                              <option value="roadmap">Karte</option>
                              <option value="terrain">Gelände</option>
                              <option value="streetview">Street View</option>
                          </select>
                      </div>

                      {{-- Google Map Container --}}
                      <div id="mapScreenshotWrapper{{ $object->id }}" class="mb-3">
                          <div id="mapContainer{{ $object->id }}"
                              class="google-map border"
                              style="width: 100%; height: 300px; background: #f9f9f9; border-radius: 6px; overflow: hidden;">
                          </div>
                      </div>

                      {{-- Screenshot Button --}}
                      <div class="text-right">
                          <button class="btn btn-sm btn-primary"
                                  onclick="triggerScreenshot({{ $customer->id }}, {{ $object->id }})">
                              📷 Screenshot speichern
                          </button>
                      </div>
                  </div>

                </div>  

                {{-- Collapsible Product List --}}
                <div id="object{{ $key }}" class="product-list" style="padding: 0px; display: none;">
                    @foreach ($products->where('alternative_id', $object->id) as $i => $product)
                        @php
                            $productId = "product{$key}_{$i}";
                            $cid = $product->customer_id;
                            $aid = $product->alternative_id;
                            $pid = $product->product_id;
                            $pl_id = $product->p_id;
                            $serviceId = $product->service_id;
                        @endphp

                        {{-- Product Row --}}
                        <div class="project-link project-card"
                            data-product-key="{{ $productId }}"
                            data-object-customer-id="{{ $cid }}"
                            data-object-alternative-id="{{ $aid }}"
                            data-object-product="{{ $pid }}"
                            data-pl-id="{{ $product->p_list_id ?? $product->p_id ?? '' }}">

                            {{-- TOP: Produktname + Meta + Adresse --}}
                            <div class="project-card-main">
                                <div class="project-card-title-row">
                                    <span class="project-status-dot"></span>

                                    <div class="project-card-title-block">
                                        <div class="project-card-title">
                                            {{ $product->article_group }}
                                        </div>

                                        <div class="project-card-meta">
                                            <span>{{ $product->department_name }}</span>
                                            <span class="meta-sep">•</span>
                                            <span>
                                                {{
                                                    [
                                                        'complete'    => 'Komplettlösung',
                                                        'montage'     => 'Montage',
                                                        'product'     => 'Produkt',
                                                        'plan'        => 'Planung',
                                                        'maintenance' => 'Wartung',
                                                        'repair'      => 'Reparatur',
                                                        'emergency'   => 'Notdienst',
                                                        'others'      => 'Sonstiges',
                                                    ][$product->phase_section] ?? ucfirst($product->phase_section)
                                                }}
                                            </span>
                                        </div>

                                        @if(!empty($object->street) || !empty($object->postcode) || !empty($object->city))
                                            <div class="project-card-meta project-card-meta-address">
                                                <i class="feather icon-map-pin"></i>
                                                <span>
                                                    {{ trim(($object->street ?? '').' '.($object->postcode ?? '').' '.($object->city ?? '')) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                       <div class="project-footer-left">
                                            <span class="project-status-pill">
                                                {{
                                                    [
                                                        'lead'      => 'Lead',
                                                        'inquiry'   => 'Anfrage',
                                                        'deal'      => 'Auftrag',
                                                        'project'   => 'Montage',
                                                        'ticket'    => 'Ticket',
                                                        'pause'     => 'Pausiert',
                                                        'completed' => 'Abschluss',
                                                        'junk'      => 'Junk',
                                                        'offer'     => 'Angebot',
                                                        'accept'    => 'Offen',
                                                    ][$product->status] ?? ucfirst($product->status)
                                                }}
                                            </span>
                                        </div>
                                </div>
                            </div>
 
                            {{-- BOTTOM: Status + ZEIT + PREIS --}}
                            <div class="project-card-footer"> 

                                <div class="project-footer-right">

                                    {{-- ZEIT: zeigt Restzeit --}}
                                    <button type="button"
                                            class="project-metric project-metric--time project-time-trigger"
                                            data-customer-id="{{ $cid }}"
                                            data-alternative-id="{{ $aid }}"
                                            data-product-id="{{ $pid }}"
                                            data-toggle="tooltip"
                                            title="Verbrauchte Zeit & Restbudget ansehen"> 
                                            <i class="feather icon-clock"></i>

                                        <span class="metric-text"> 
                                            <span class="metric-value js-project-time-display"
                                                data-product-key="{{ $productId }}">
                                                {{ $product->remaining_hm ?? '–:–' }} h
                                            </span>
                                        </span>
                                    </button>

                                    {{-- PREIS --}}
                                    <button type="button"
                                            class="project-metric project-metric--price price-badge price-edit-trigger"
                                            data-pl-id="{{ $product->p_list_id ?? $product->p_id ?? '' }}"
                                            data-current-price="{{ $product->price ?? 0 }}"
                                            data-toggle="tooltip"
                                            title="Preis bearbeiten"> 
                                            <i class="feather icon-tag"></i>

                                        <span class="metric-text"> 
                                            <span class="metric-value">
                                                {{ number_format($product->price ?? 0, 2, ',', '.') }} €
                                            </span>
                                        </span>
                                    </button>

                                    <button type="button"
                                            class="project-metric project-metric--calendar"
                                            onclick="loadCalendar({{ $cid }}, {{ $aid }}, {{ $pid }})"
                                            data-toggle="tooltip"
                                            title="Termin ansehen">
                                        <i class="feather icon-calendar"></i>
                                    </button>

                                </div>
                            </div>

                        </div>



                        {{-- Sub Nav Section (Hidden by default) --}}
                        <div id="{{ $productId }}" class="sub-nav" style="display: none;">
                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadkanban({{ $cid }}, {{ $aid }}, {{ $pid }}, 'kanban')">
                                <i data-feather="columns"></i> Kanban
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadFullAlternativeObject(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}">
                                <i data-feather="layers"></i> Objektdaten
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadDocuments(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="file-plus"></i> Bilder & Dokumente
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadNeighbor(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="file-plus"></i> Nachbarschaft
                            </button>
                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadChecklist(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="check-square"></i> Checkliste
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadTask(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="clipboard"></i> Aufgaben
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'angebote')">
                                <i data-feather="file-text"></i> Angebote
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'auftraege')">
                                <i data-feather="briefcase"></i> Auftrag
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'projekte')">
                                <i data-feather="settings"></i> Montage
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadInvoice({{ $cid }}, {{ $aid }}, {{ $pid }})">
                                <i data-feather="file-invoice"></i> Rechnungen
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); leadProduct(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}">
                                <i data-feather="shopping-bag"></i> Produkt
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadCalendar({{ $cid }}, {{ $aid }}, {{ $pid }})">
                                <i data-feather="calendar"></i> Termin
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); LoadCustomerTicket({{ $cid }}, {{ $aid }}, {{ $pid }}, 'tickets')">
                                <i data-feather="tag"></i> Tickets
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'bewertungen')">
                                <i data-feather="star"></i> Bewertungen
                            </button>

                            <button class="nav-section-btn btn btn-outline-secondary" 
                                    onclick="setActiveSubNav(this); loadHistory({{ $cid }}, {{ $aid }}, {{ $pid }})">
                                <i data-feather="book-open"></i> Historie
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadStages({{ $cid }}, {{ $aid }}, {{ $pid }}, {{ $serviceId }})">
                                <i data-feather="git-branch"></i> Arbeitsprozess
                            </button>
                        </div>


                    @endforeach
                </div>
            </div> 
            @endforeach

        </div>  
        <div class="contentStation   position-relative p-0 pt-2 m-0">
            <button id="mainContentToggle"  
                  style="    position: absolute;
                              right: 0px;
                              top: -1px;
                              background: white;"
                  class="btn btn-icon btn-icon  btn-flat-primary  waves-effect waves-light">
                  <i class="feather icon-maximize-2"></i>

            </button> 
            <div class="main-content" >  
                <div class="main" id="mainContent">
                    @include('admin.new_leads.layouts.dashboard') 
                </div>
            </div>
        </div>  
        <div class="right-panel  d-flex flex-column p-0">
            <div style="border-bottom: 1px solid #ddd; flex-shrink: 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 style="font-size: 1.1rem; font-weight: bold; color: #94c11f;" class="mb-0 mr-1 ml-1" id="note_title">NOTIZEN</h4>
                    <div class="search d-flex align-items-center">
                        <fieldset class="form-group position-relative mb-0">
                            <input type="text" class="form-control" id="searchNote" placeholder="Suchen">
                            <div class="form-control-position">
                                <i class="feather icon-search"></i>
                            </div>
                        </fieldset>
                        <div class="btn-group" role="group">
                            <button id="toggleNewNoteBtn" onclick="toggleNewNoteArea()" 
                                class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light" 
                                title="Neue Notiz">
                                <i class="feather icon-plus"></i>
                            </button>

                            <button id="btnToggleRightPanelFullscreen" 
                                class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light" 
                                title="Vollbild umschalten">
                                <i class="feather icon-maximize-2"></i>
                            </button>

                            <button id="noteDeletedModal" onclick="loadAllDeletedNotes()" 
                                class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light" 
                                title="Gelöschte Notizen anzeigen">
                                <i class="feather icon-trash-2"></i>
                            </button>
                        </div>


                    </div>
                </div>
            </div>

            <div id="note-scroll-wrapper " class="flex-grow-1 overflow-auto p-0 scroll-wrapper">
                <div id="note-list" class="scroll-wrapper"></div>
            </div>

               

        </div> 
      </div>
  </div>


    <div id="projectTimeBackdrop" class="ph-backdrop">
        <div class="ph-drawer">
            {{-- HEADER --}}
            <div class="ph-header">
                <div>
                    <div class="ph-title">Projektzeit</div>
                    <div class="ph-subtitle">
                        <span id="ptProductTitle"></span>
                    </div>
                </div>
                <button type="button" class="ph-close-btn" id="ptCloseBtn" aria-label="Schließen">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="ph-body" id="ptBody">

                {{-- TOP: CUSTOMER / ALTERNATIVE / PRODUCT / STATUS --}}
                <div class="pt-info-grid">
                    <div class="pt-info-card">
                        <div class="pt-info-label">
                            <i class="feather icon-user mr-25"></i>Kunde
                        </div>
                        <div class="pt-info-value" id="ptCustomerName">–</div>
                        <div class="pt-info-meta" id="ptCustomerAddress">–</div>
                    </div>

                    <div class="pt-info-card">
                        <div class="pt-info-label">
                            <i class="feather icon-layers mr-25"></i>Alternative
                        </div>
                        <div class="pt-info-value" id="ptAlternativeLabel">–</div>
                        <div class="pt-info-meta" id="ptAlternativeInfo">–</div>
                    </div>

                    <div class="pt-info-card">
                        <div class="pt-info-label">
                            <i class="feather icon-package mr-25"></i>Produkt
                        </div>
                        <div class="pt-info-value" id="ptProductName">–</div>
                        <div class="pt-info-meta" id="ptProductInfo">–</div>
                    </div>

                    <div class="pt-info-card">
                        <div class="pt-info-label d-flex align-items-center justify-content-between">
                            <span><i class="feather icon-flag mr-25"></i>Status</span>
                            <span class="pt-status-pill" id="ptStatusBadge">–</span>
                        </div>
                        <div class="pt-info-meta">
                            Zeitraum: <span id="ptDurationLabel"></span>
                        </div>
                    </div>
                </div>

                {{-- SECOND ROW: CHART + KPI BADGES --}}
                <div class="pt-stats-row">
                    <div class="pt-chart-card">
                        <div class="pt-section-title d-flex justify-content-between align-items-center">
                            <span><i class="feather icon-pie-chart mr-25"></i>Budget-Nutzung</span>
                            <span class="pt-chart-legend">
                                <span class="pt-dot pt-dot-used"></span>Verbraucht
                                <span class="pt-dot pt-dot-remaining ml-50"></span>Rest
                            </span>
                        </div>
                        <div class="pt-chart-wrapper">
                            <canvas id="ptPieChart"></canvas>
                        </div>
                    </div>

                    <div class="pt-stat-cards">
                        <div class="pt-stat-card">
                            <div class="pt-stat-label">Geplante Zeit</div>
                            <div class="pt-stat-value" id="ptBaseTime">--:--</div>
                            <div class="pt-stat-pill pt-pill-base">Basis</div>
                        </div>
                        <div class="pt-stat-card">
                            <div class="pt-stat-label">Erweiterungen</div>
                            <div class="pt-stat-value" id="ptExtraTime">00:00</div>
                            <div class="pt-stat-pill pt-pill-extra">+ Anfragen</div>
                        </div>
                        <div class="pt-stat-card">
                            <div class="pt-stat-label">Verbraucht</div>
                            <div class="pt-stat-value" id="ptUsedTime">00:00</div>
                            <div class="pt-stat-pill pt-pill-used">Aktiv</div>
                        </div>
                        <div class="pt-stat-card">
                            <div class="pt-stat-label">Restbudget</div>
                            <div class="pt-stat-value" id="ptRemainingTime">00:00</div>
                            <div class="pt-stat-pill pt-pill-remaining">Verfügbar</div>
                        </div>
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="pt-section">
                    <div class="pt-section-head">
                        <div class="pt-section-title">
                            <i class="feather icon-activity mr-25"></i>Zeitleiste & Einträge
                        </div>
                    </div>
                    <div id="ptTimeline" class="pt-timeline">
                        <!-- filled by JS -->
                    </div>
                </div>

                {{-- FORM + HISTORY SIDE BY SIDE --}}
                <div class="pt-section pt-section-split">
                    {{-- LEFT: REQUEST MORE TIME --}}
                    <div class="pt-section-col">
                        <div class="pt-section-head">
                            <div class="pt-section-title">
                                <i class="feather icon-clock mr-25"></i>Mehr Zeit anfragen
                            </div>
                        </div>

                        <form id="ptRequestForm">
                            @csrf
                            <input type="hidden" name="customer_id" id="ptCustomerId">
                            <input type="hidden" name="alternative_id" id="ptAlternativeId">
                            <input type="hidden" name="product_id" id="ptProductId">
                            <input type="hidden" name="section_id" id="ptSectionId">

                            <div class="form-row">
                                <div class="col-6">
                                    <label for="ptExtraHours">Stunden</label>
                                    <input type="number" min="0"
                                        class="form-control form-control-sm"
                                        id="ptExtraHours" name="extra_hours" value="1">
                                </div>
                                <div class="col-6">
                                    <label for="ptExtraMinutes">Minuten</label>
                                    <input type="number" min="0" max="59"
                                        class="form-control form-control-sm"
                                        id="ptExtraMinutes" name="extra_minutes" value="0">
                                </div>
                            </div>

                            <div class="form-group mt-50">
                                <label for="ptReason">Begründung</label>
                                <textarea id="ptReason" name="reason" rows="3"
                                        class="form-control form-control-sm"
                                        placeholder="Warum wird mehr Zeit benötigt?"></textarea>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="feather icon-send mr-25"></i> Anfrage senden
                            </button>

                            <div id="ptRequestMessage" class="mt-50 text-xs text-muted"></div>
                        </form>
                    </div>

                    {{-- RIGHT: REQUEST HISTORY --}}
                    <div class="pt-section-col">
                        <div class="pt-section-head d-flex justify-content-between align-items-center">
                            <div class="pt-section-title">
                                <i class="feather icon-list mr-25"></i>Zeit-Historie
                            </div>
                        </div>
                        <div id="ptRequestHistory"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>


  <!-- PRICE HISTORY DRAWER -->
  <div id="priceHistoryBackdrop" class="ph-backdrop">
      <div class="ph-drawer">
          <div class="ph-header">
              <div>
                  <div class="ph-title">Preisverlauf</div>
                  <div class="ph-subtitle">
                      <span id="phCustomerName"></span>
                  </div>
              </div>
              <button type="button" class="ph-close-btn" aria-label="Schließen">
                  <i class="feather icon-x"></i>
              </button>
          </div>

          <div class="ph-meta-strip">
              <div>
                  Letzter Kauf:
                  <span id="phPurchaseDate" class="ph-pill"></span>
              </div>
              <div>
                  Gesamt:
                  <span id="phTotalPurchase" class="ph-pill"></span>
              </div>
          </div>

          <div id="priceHistoryContent" class="ph-body">
              <!-- filled by JS -->
          </div>
      </div>
  </div>

      


  <div id="phaseSidebar" class="phase-sidebar"> 
    <div class="phase-sidebar-body"
        data-customer-id=""
      data-alternative-id=""
      data-product-id=""
      data-service-id=""> 

      <p>Lade...</p>
    </div>
  </div>

  <!-- Modal Purchase -->
<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kaufübersicht</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="purchaseModalBody" class="modal-body">
        <div class="text-muted">Laden…</div>
      </div>
    </div>
  </div>
</div>
  

<div class="modal fade" id="noteDeletedModalWrapper" tabindex="-1" role="dialog" aria-labelledby="noteDeletedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="noteDeletedModalLabel">Gelöschte Notizen</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="noteDeletedModalBody">
        <div class="text-muted">Lade gelöschte Notizen...</div>
      </div>
    </div>
  </div>
</div>


    <div id="newNoteComposer" class="note-composer">
        <textarea id="newNoteText" class="form-control my-2" rows="3" placeholder="Write a new note..."></textarea>

        <!-- ✅ Hidden fields: dynamically filled from dataset -->
        <input type="hidden" id="noteType" name="type" value="">
        <input type="hidden" id="noteProductId" name="product_id" value="">

        <button onclick="submitNote()" class="btn btn-success float-end mb-2">
            <i class="feather icon-send me-1"></i> Send
        </button>
        
    </div>
    <div id="noteBackdrop" class="note-backdrop" onclick="toggleNewNoteArea()" style="display: none;"></div>

@include('admin.new_leads.layouts.taskModal')




 