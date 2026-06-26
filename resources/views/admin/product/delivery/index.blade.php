@extends('admin.layouts.app')
@section('title', 'Lieferscheine')

@php
$pageTitle = 'LIEFERSCHEINE';
@endphp

@once
@push('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<style>
  :root {
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;
    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-light:#f4fae7;
    --blue:#74b2d4;
    --blue-light:#eff6ff;
    --success:#10b981;
    --success-light:#ecfdf5;
    --warning:#f59e0b;
    --warning-light:#fffbeb;
    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;
    --gray:#6b7280;
    --gray-light:#f3f4f6;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main); 
  }

  .oc-header { margin-bottom:18px;}

  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }

  .oc-title {
    font-size:26px;
    font-weight:800;
    letter-spacing:-.025em;
    color:#111827;
  }

  .oc-sub {
    font-size:14px;
    color:var(--text-muted);
    margin-top:4px;
  }

  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }

  .oc-breadcrumb a{
    color:var(--text-muted);
    text-decoration:none;
    font-weight:700;
  }

  .oc-breadcrumb a:hover{ color:var(--text-main); }
  .oc-breadcrumb span.current{ color:#111827; font-weight:800; }

  .oc-btn{
    background:var(--primary);
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    transition:var(--transition);
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
  }

  .oc-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
  }

  .oc-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
  }

  .oc-btn-soft:hover{
    background:#f9fafb;
    color:var(--text-main);
    text-decoration:none;
  }

  .oc-btn-ic{
    width:36px;
    height:36px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:var(--text-muted);
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
    flex:0 0 auto;
  }

  .oc-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
  }

  .oc-btn-ic.primary{ color:var(--primary); border-color:var(--primary-light); background:var(--primary-light); }
  .oc-btn-ic.primary:hover{ border-color:var(--primary); }
  .oc-btn-ic.warning{ color:#d97706; border-color:#fde7b0; background:#fffbeb; }
  .oc-btn-ic.warning:hover{ border-color:#f59e0b; }
  .oc-btn-ic.success{ color:var(--success); border-color:#c7f2df; background:var(--success-light); }
  .oc-btn-ic.success:hover{ border-color:var(--success); }
  .oc-btn-ic.danger{ color:var(--danger); border-color:rgba(239,68,68,.18); background:var(--danger-light); }
  .oc-btn-ic.danger:hover{ border-color:rgba(239,68,68,.35); }
  .oc-btn-ic.blue{ color:var(--blue); border-color:#dbeafe; background:#eff6ff; }
  .oc-btn-ic.blue:hover{ border-color:#93c5fd; }

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }

  @media(max-width:1200px){ .oc-analytics{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
  @media(max-width:700px){ .oc-analytics{ grid-template-columns:1fr; } }

  .oc-stat{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow-sm);
    display:flex;
    align-items:center;
    gap:12px;
    min-height:92px;
  }

  .oc-stat-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-stat-icon.total{ background:var(--blue-light); color:var(--blue); }
  .oc-stat-icon.published{ background:var(--success-light); color:var(--success); }
  .oc-stat-icon.unpublished{ background:var(--warning-light); color:#d97706; }
  .oc-stat-icon.type{ background:var(--gray-light); color:var(--gray); }

  .oc-stat-meta{ min-width:0; }
  .oc-stat-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  .oc-stat-value{
    font-size:24px;
    font-weight:900;
    color:#111827;
    line-height:1.1;
    margin-top:4px;
  }
  .oc-stat-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
  }

  .oc-toolbar{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:14px 16px;
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:flex-end;
    justify-content:space-between;
    margin-bottom:16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-toolbar-left,.oc-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
  }

  .oc-toolbar-left{ flex:1; }

  .oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
  }

  .oc-filter-block.search{
    flex:1;
    min-width:280px;
  }

  .oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-input{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px 10px 36px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    min-width:240px;
    width:100%;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
  }

  .oc-input:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-select,
  .oc-input-form,
  .oc-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }

  .oc-select:focus,
  .oc-input-form:focus,
  .oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-list-head{
    display:grid;
    grid-template-columns:80px minmax(210px,1.3fr) minmax(150px,.9fr) minmax(190px,1fr) 130px 140px 250px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px 16px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  @media(max-width:1280px){ .oc-list-head{ display:none; } }

  .oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px 0;
  }

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    margin:16px;
  }

  .oc-loading{
    display:inline-flex;
    align-items:center;
    gap:10px;
    font-weight:700;
    color:var(--text-muted);
  }

  .oc-loading-dot{
    width:10px;
    height:10px;
    border-radius:999px;
    background:var(--primary);
    animation: ocPulse 1.1s infinite ease-in-out;
  }

  @keyframes ocPulse{
    0%,100%{ transform:scale(.7); opacity:.5; }
    50%{ transform:scale(1); opacity:1; }
  }

  .oc-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-pagination .pagination{
    margin:0;
    display:flex;
    flex-wrap:wrap;
    gap:6px;
  }

  .oc-pagination .page-item .page-link{
    border-radius:10px !important;
    border:1px solid var(--border);
    color:var(--text-main);
    padding:8px 12px;
    line-height:1.1;
    box-shadow:none !important;
  }

  .oc-pagination .page-item.active .page-link{
    background:var(--primary);
    border-color:var(--primary);
    color:#fff;
  }

  .oc-pagination .page-item.disabled .page-link{
    color:#9ca3af;
    background:#f9fafb;
  }

  body.oc-modal-open{
  overflow:hidden;
}

.oc-modal-backdrop{
  position:fixed;
  inset:0;
  z-index:1200;
  background:rgba(17,24,39,.55);
  backdrop-filter:blur(3px);
  opacity:0;
  visibility:hidden;
  pointer-events:none;
  transition:opacity .18s ease, visibility .18s ease;
  display:flex;
  align-items:flex-start;
  justify-content:center;
  padding:28px 18px;
  overflow-y:auto;
}

.oc-modal-backdrop.open{
  opacity:1;
  visibility:visible;
  pointer-events:auto;
}

.oc-modal{
  width:100%;
  max-width:680px;
  margin:auto 0;
  background:#fff;
  border:1px solid rgba(229,231,235,.9);
  border-radius:18px;
  box-shadow:0 24px 70px rgba(15,23,42,.28);
  transform:none;
  transition:none;
  overflow:visible;
}

.oc-modal.oc-modal-lg{ max-width:1100px; }
.oc-modal.oc-modal-xl{ max-width:1240px; }

.oc-modal-b{
  padding:20px 18px;
  max-height:calc(100vh - 190px);
  overflow-y:auto;
  overflow-x:hidden;
}

.oc-modal-h,
.oc-modal-f{
  position:relative;
  z-index:2;
}

.oc-modal-f{
  position:sticky;
  bottom:0;
}

  .oc-modal{
    width:100%;
    max-width:680px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }

  .oc-modal.oc-modal-lg{ max-width:1100px; }
  .oc-modal.oc-modal-xl{ max-width:1240px; }

 .oc-form-group .select2-container,
.oc-filter-block .select2-container{
  width:100% !important;
  display:block !important;
}

.select2-container--default .select2-selection--single{
  height:44px !important;
  min-height:44px !important;
  border:1px solid var(--border) !important;
  border-radius:10px !important;
  background:#fff !important;
  box-shadow:none !important;
  outline:none !important;
}

.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--single{
  border-color:var(--primary) !important;
  box-shadow:0 0 0 3px var(--primary-light) !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered{
  height:42px !important;
  line-height:42px !important;
  padding-left:12px !important;
  padding-right:34px !important;
  color:var(--text-main) !important;
  font-size:14px !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder{
  color:#9ca3af !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow{
  height:42px !important;
  width:32px !important;
  right:4px !important;
}

.select2-container--default .select2-selection--single .select2-selection__clear{
  height:42px !important;
  line-height:42px !important;
  margin-right:22px !important;
  color:#9ca3af !important;
}

.select2-dropdown{
  z-index:1305 !important;
  border:1px solid var(--border) !important;
  border-radius:12px !important;
  overflow:hidden !important;
  box-shadow:0 18px 45px rgba(15,23,42,.18) !important;
}

.select2-container--open .select2-dropdown--below{
  margin-top:6px !important;
}

.select2-container--open .select2-dropdown--above{
  margin-top:-6px !important;
}

.select2-search--dropdown{
  padding:10px !important;
  background:#fff !important;
}

.select2-search--dropdown .select2-search__field{
  border:1px solid var(--border) !important;
  border-radius:10px !important;
  padding:9px 11px !important;
  outline:none !important;
  font-size:14px !important;
}

.select2-search--dropdown .select2-search__field:focus{
  border-color:var(--primary) !important;
  box-shadow:0 0 0 3px var(--primary-light) !important;
}

.select2-results__options{
  max-height:260px !important;
}

.select2-results__option{
  padding:10px 12px !important;
  font-size:14px !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected]{
  background:var(--primary) !important;
  color:#fff !important;
}

.select2-container--default .select2-results__option[aria-selected=true]{
  background:var(--primary-light) !important;
  color:#365314 !important;
}

.oc-modal .select2-container{
  z-index:2;
}
  .oc-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }

  .oc-modal-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
  }

  .oc-modal-b{
    padding:20px 18px;
    max-height:72vh;
    overflow-y:auto;
  }

  .oc-modal-f{
    padding:14px 18px;
    border-top:1px solid var(--border);
    background:#fafafa;
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }

  @media(max-width:760px){ .oc-form-grid{ grid-template-columns:1fr; } }

  .oc-form-group{ margin-bottom:16px; }

  .oc-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }

  .oc-help{ font-size:12px; color:var(--text-muted); margin-top:6px; }
  .oc-error{ font-size:12px; color:#dc2626; margin-top:6px; display:none; }
  .oc-error.show{ display:block; }
  .oc-field-error{ border-color:#ef4444 !important; box-shadow:0 0 0 3px rgba(239,68,68,.12) !important; }

  .oc-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:10px;
    pointer-events:none;
  }

  .oc-toast{
    pointer-events:auto;
    min-width:280px;
    max-width:360px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    box-shadow:var(--shadow);
    padding:12px;
    display:flex;
    gap:10px;
    align-items:flex-start;
    animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
  }

  @keyframes ocToastIn{
    from{ transform:translateX(100%); opacity:0; }
    to{ transform:translateX(0); opacity:1; }
  }

  .oc-toast-ic{
    width:34px;
    height:34px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-toast-ic.ok{ background:var(--success-light); color:var(--success); }
  .oc-toast-ic.bad{ background:var(--danger-light); color:var(--danger); }
  .oc-toast-ttl{ font-weight:900; font-size:13px; margin:0; color:#111827; }
  .oc-toast-msg{ font-size:12px; color:#374151; margin:4px 0 0 0; line-height:1.4; }
  .oc-toast-x{
    margin-left:auto;
    background:transparent;
    border:none;
    cursor:pointer;
    color:var(--text-muted);
  }

  .oc-scanner{
    width:100%;
    min-height:280px;
    border:1px solid var(--border);
    border-radius:14px;
    overflow:hidden;
    background:#f9fafb;
  }

  .oc-hidden{ display:none !important; }

  .oc-linked-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
  }

  @media(max-width:900px){ .oc-linked-grid{ grid-template-columns:1fr; } }

  .oc-mini-card{
    border:1px solid var(--border);
    border-radius:14px;
    background:#fff;
    padding:14px;
  }

  .oc-mini-card h4{
    margin:0 0 12px 0;
    font-size:14px;
    font-weight:900;
  }

  .oc-mini-list{
    display:flex;
    flex-direction:column;
    gap:10px;
  }

  .oc-mini-item{
    border:1px solid var(--border);
    border-radius:12px;
    padding:12px;
    background:#fafafa;
  }

  .oc-mini-item-title{
    font-size:14px;
    font-weight:800;
    margin-bottom:4px;
  }

  .oc-mini-item-sub{
    font-size:12px;
    color:var(--text-muted);
  }

  .select2-container{ width:100% !important; }
  .select2-container .select2-selection--single{
    height:44px !important;
    border:1px solid var(--border) !important;
    border-radius:8px !important;
    background:#fff !important;
    display:flex !important;
    align-items:center !important;
    padding:0 10px !important;
    font-size:14px !important;
    transition:var(--transition) !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered{
    color:var(--text-main) !important;
    line-height:42px !important;
    padding-left:0 !important;
    padding-right:28px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow{
    height:42px !important;
    right:8px !important;
  }

  .select2-container--default.select2-container--focus .select2-selection--single,
  .select2-container--default .select2-selection--single:focus{
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px var(--primary-light) !important;
  }

  .select2-dropdown{
    border:1px solid var(--border) !important;
    border-radius:10px !important;
    overflow:hidden !important;
    box-shadow:var(--shadow-sm) !important;
  }

  .select2-search--dropdown .select2-search__field{
    border:1px solid var(--border) !important;
    border-radius:8px !important;
    padding:8px 10px !important;
    outline:none !important;
  }

  .select2-results__option{
    padding:10px 12px !important;
    font-size:14px !important;
  }

  .select2-container--default .select2-results__option--highlighted[aria-selected]{
    background:var(--primary) !important;
    color:#fff !important;
  }

  .oc-overlay-loading{
    position:absolute;
    inset:0;
    background:rgba(255,255,255,.72);
    backdrop-filter:blur(1px);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:5;
  }

  .oc-overlay-loading.show{
    display:flex;
  }

  .oc-list-shell{
    position:relative;
  }

  .oc-item{
  background:#fff;
  border:1px solid var(--border);
  border-radius:var(--radius);
  margin:0 16px;
  overflow:hidden;
  transition:var(--transition);
}

.oc-item:hover{
  border-color:var(--primary);
  box-shadow:var(--shadow);
}

.oc-item-row,
.oc-child-item{
  display:grid;
  grid-template-columns:80px minmax(210px,1.3fr) minmax(220px,1fr) minmax(190px,1fr) 130px 140px 250px;
  gap:14px;
  align-items:center;
  padding:16px;
}

.oc-child-wrap{
  display:none;
  border-top:1px dashed var(--border);
  background:#fafafa;
}

.oc-child-wrap.open{
  display:block;
}

.oc-child-item{
  padding-left:42px;
  border-top:1px dashed #e5e7eb;
  background:#fafafa;
}

.oc-child-item:first-child{
  border-top:none;
}

.oc-cell{
  min-width:0;
}

.oc-cell-title{
  display:none;
  font-size:11px;
  font-weight:800;
  color:var(--text-muted);
  text-transform:uppercase;
  margin-bottom:4px;
}

.oc-id-badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:54px;
  height:36px;
  padding:0 12px;
  border-radius:10px;
  background:var(--blue-light);
  color:var(--blue);
  font-size:13px;
  font-weight:900;
}

.oc-main{
  display:flex;
  flex-direction:column;
  min-width:0;
}

.oc-ttl{
  font-weight:800;
  font-size:15px;
  margin-bottom:4px;
  color:#111827;
}

.oc-subt{
  font-size:13px;
  color:var(--text-muted);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

.oc-meta-stack{
  display:flex;
  flex-direction:column;
  gap:4px;
  font-size:13px;
  color:var(--text-main);
}

.oc-muted{
  color:var(--text-muted);
}

.oc-status-pill{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:900;
  white-space:nowrap;
}

.oc-status-pill.green{background:#ecfdf5;color:#047857;}
.oc-status-pill.orange{background:#fffbeb;color:#b45309;}
.oc-status-pill.red{background:#fef2f2;color:#b91c1c;}
.oc-status-pill.blue{background:#eff6ff;color:#1d4ed8;}

.oc-progress{
  width:100%;
  height:10px;
  border-radius:999px;
  background:#eef2f7;
  overflow:hidden;
  margin-top:8px;
}

.oc-progress-bar{
  height:100%;
  background:linear-gradient(90deg, var(--primary), #b7db58);
  border-radius:999px;
}

.oc-actions{
  display:flex;
  align-items:center;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:wrap;
}

.oc-group-summary{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-top:8px;
}

.oc-group-badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:4px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:900;
}

.oc-group-badge.total{background:#eff6ff;color:#1d4ed8;}
.oc-group-badge.complete{background:#ecfdf5;color:#047857;}
.oc-group-badge.open{background:#fffbeb;color:#b45309;}

.oc-group-toggle{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:6px 10px;
  border-radius:999px;
  border:1px solid var(--border);
  background:#fff;
  font-size:12px;
  font-weight:800;
  color:var(--text-main);
  cursor:pointer;
  transition:var(--transition);
}

.oc-group-toggle:hover{
  border-color:var(--primary);
  background:var(--primary-light);
}

.oc-group-toggle svg{
  transition:transform .2s ease;
}

.oc-group-toggle.open svg{
  transform:rotate(90deg);
}

.oc-child-label{
  display:inline-flex;
  align-items:center;
  gap:6px;
  font-size:11px;
  font-weight:900;
  color:var(--text-muted);
  text-transform:uppercase;
}

.oc-child-dot{
  width:8px;
  height:8px;
  border-radius:999px;
  background:var(--primary);
}

@media(max-width:1280px){
  .oc-list-head{
    display:none;
  }

  .oc-item-row,
  .oc-child-item{
    grid-template-columns:1fr;
  }

  .oc-child-item{
    padding-left:24px;
  }

  .oc-cell-title{
    display:block;
  }

  .oc-actions{
    justify-content:flex-start;
  }
}
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">{{ $pageTitle }}</div>
        <div class="oc-sub">Verwalten Sie Lieferscheine, Verknüpfungen, Kunden, Deals, PDFs und Bilder zentral.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <span class="current">{{ $pageTitle }}</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <button type="button" class="oc-btn" onclick="openModal('createDeliveryNoteModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
          </svg>
          Neue hinzufügen
        </button>
      </div>
    </div>
  </div>

  <div class="oc-analytics" id="analytics-wrap">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value" id="an-total">0</div>
        <div class="oc-stat-sub">Einträge insgesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Verfügbar</div>
        <div class="oc-stat-value" id="an-available">0</div>
        <div class="oc-stat-sub">Sofort verfügbar</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Nicht verfügbar</div>
        <div class="oc-stat-value" id="an-unavailable">0</div>
        <div class="oc-stat-sub">Aktuell gesperrt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M7 12h10M10 17h4"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Abgeschlossen</div>
        <div class="oc-stat-value" id="an-completed">0</div>
        <div class="oc-stat-sub">Fortschritt 100%</div>
      </div>
    </div>
  </div>

  <form id="filterForm" class="oc-toolbar" onsubmit="return false;">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          placeholder="Suche nach Lieferschein, Kunde, Deal, Zweig, Mitarbeiter ..."
          name="search"
          id="filter-search"
          autocomplete="off"
        >
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Status</label>
        <select class="oc-select" name="status" id="filter-status">
          <option value="">Alle</option>
          <option value="Verfügbar">Verfügbar</option>
          <option value="Nicht verfügbar">Nicht verfügbar</option>
          <option value="Teilweise">Teilweise</option>
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Ziel</label>
        <select class="oc-select" name="destination_type" id="filter-destination_type">
          <option value="">Alle</option>
          <option value="customer">Kunde</option>
          <option value="warehouse">Lager</option>
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Zweig</label>
        <select class="oc-select" name="branch_id" id="filter-branch_id">
          <option value="">Alle</option>
          @foreach($branches ?? [] as $branch)
            <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
          @endforeach
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Übergabe durch</label>
        <select class="oc-select" name="handover_by" id="filter-handover_by">
          <option value="">Alle</option>
          @foreach($employees ?? [] as $employee)
            <option value="{{ $employee->id }}">{{ trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) }}</option>
          @endforeach
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Kunde</label>
        <select class="oc-select js-customer-filter-select" name="customer_id" id="filter-customer_id" data-placeholder="Kunde wählen">
          <option value="">Alle</option>
          @foreach($customers ?? [] as $customer)
            <option value="{{ $customer->id }}">
              {{ $customer->display_name ?? (($customer->firma ?: trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))) ?: '#' . $customer->id) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Deal / Auftrag</label>
        <select class="oc-select js-deal-filter-select" name="deal_id" id="filter-deal_id" data-placeholder="Deal wählen">
          <option value="">Alle</option>
          @foreach($deals ?? [] as $deal)
            <option value="{{ $deal->id }}">
              {{ $deal->order_number ?: ('#' . $deal->id) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Fortschritt</label>
        <select class="oc-select" name="progress" id="filter-progress">
          <option value="">Alle</option>
          <option value="open">Offen</option>
          <option value="complete">Abgeschlossen</option>
          @for($i = 0; $i <= 100; $i += 10)
            <option value="{{ $i }}">{{ $i }}%</option>
          @endfor
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Sortierung</label>
        <select class="oc-select" name="sort" id="filter-sort">
          <option value="latest">Neueste zuerst</option>
          <option value="oldest">Älteste zuerst</option>
          <option value="delivery_note_asc">Lieferschein A-Z</option>
          <option value="delivery_note_desc">Lieferschein Z-A</option>
          <option value="date_asc">Datum aufsteigend</option>
          <option value="date_desc">Datum absteigend</option>
          <option value="progress_asc">Fortschritt aufsteigend</option>
          <option value="progress_desc">Fortschritt absteigend</option>
        </select>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="button" id="filter-apply">Suchen</button>
      <button class="oc-btn-soft" type="button" id="filter-reset">Zurücksetzen</button>
    </div>
  </form>

  <div class="oc-card oc-list-shell">
    <div class="oc-overlay-loading" id="table-overlay-loading">
      <div class="oc-loading">
        <span class="oc-loading-dot"></span>
        Daten werden geladen...
      </div>
    </div>

    <div class="oc-list-head">
      <div>ID</div>
      <div>Lieferschein</div>
      <div>Bezug</div>
      <div>Übergabe</div>
      <div>Status</div>
      <div>Fortschritt</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list" id="table-wrapper">
      <div class="oc-empty">Daten werden geladen...</div>
    </div>
  </div>

  <div class="oc-pagination" id="pagination-wrapper"></div>
</div>

{{-- Create Modal --}}
<div class="oc-modal-backdrop" id="createDeliveryNoteModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Neuen Lieferschein anlegen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('createDeliveryNoteModal')">×</button>
    </div>

    <form id="createDeliveryNoteForm" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="customer_id" id="create_customer_id">
      <input type="hidden" name="alternative_id" id="create_alternative_id">
      <input type="hidden" name="lead_product_list_id" id="create_lead_product_list_id">
      <input type="hidden" name="product_id" id="create_product_id">
      <input type="hidden" name="deal_id" id="create_deal_id">

      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Scanner</label>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
              <button type="button" class="oc-btn-soft" id="toggleQrScannerBtn">QR-Scanner</button>
              <button type="button" class="oc-btn-soft" id="toggleBarcodeScannerBtn">Barcode-Scanner</button>
              <button type="button" class="oc-btn-soft" id="stopScannerBtn">Scanner stoppen</button>
            </div>
            <div id="scannerWrap" class="oc-hidden">
              <div id="reader" class="oc-scanner"></div>
              <div class="oc-help">QR-Code oder Barcode scannen, um die Lieferscheinnummer automatisch zu füllen.</div>
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Lieferschein-Nr. *</label>
            <input type="text" class="oc-input-form" name="delivery_note" autocomplete="off">
            <div class="oc-error" data-error="delivery_note"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Geliefert von *</label>
            <input type="text" class="oc-input-form" name="delivered_from" autocomplete="off">
            <div class="oc-error" data-error="delivered_from"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zieltyp *</label>
            <select class="oc-select" name="destination_type" id="create_destination_type">
              <option value="customer">Kunde</option>
              <option value="warehouse">Lager</option>
            </select>
            <div class="oc-error" data-error="destination_type"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zweig</label>
            <select class="oc-select" name="branch_id">
              <option value="">Bitte wählen</option>
              @foreach($branches ?? [] as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
              @endforeach
            </select>
            <div class="oc-error" data-error="branch_id"></div>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Kunde / Produkt / Objekt *</label>
            <select
              class="oc-select js-customer-product-select"
              id="create_customer_product"
              data-placeholder="Kunde, Produkt oder Objekt suchen"
            ></select>
            <div class="oc-help" id="create_deal_hint">Nach Auswahl wird der passende Auftrag automatisch gesucht.</div>
            <div class="oc-error" data-error="customer_id"></div>
            <div class="oc-error" data-error="lead_product_list_id"></div>
            <div class="oc-error" data-error="product_id"></div>
            <div class="oc-error" data-error="deal_id"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Übergabe durch</label>
            <select class="oc-select" name="handover_by">
              <option value="">Bitte wählen</option>
              @foreach($employees ?? [] as $employee)
                <option value="{{ $employee->id }}">{{ trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) }}</option>
              @endforeach
            </select>
            <div class="oc-error" data-error="handover_by"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestellt von</label>
            <input type="text" class="oc-input-form" name="order_by" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestellnummer</label>
            <input type="text" class="oc-input-form" name="order_no" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kommission</label>
            <input type="text" class="oc-input-form" name="comission" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestelldatum</label>
            <input type="date" class="oc-input-form" name="order_date">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Übergabedatum</label>
            <input type="date" class="oc-input-form" name="handover_date">
            <div class="oc-error" data-error="handover_date"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select class="oc-select" name="status">
              <option value="Verfügbar">Verfügbar</option>
              <option value="Nicht verfügbar">Nicht verfügbar</option>
              <option value="Teilweise">Teilweise</option>
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Fortschritt</label>
            <select class="oc-select" name="progress">
              @for($i = 0; $i <= 100; $i += 10)
                <option value="{{ $i }}" @selected($i === 0)>{{ $i }}%</option>
              @endfor
            </select>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Verlinkter Lieferschein</label>
            <select class="oc-select js-linked-note-select" name="linked_delivery_note_id" id="create_linked_delivery_note_id" data-placeholder="Bitte Lieferschein wählen">
              <option value="">Bitte wählen</option>
              @foreach($deliveryNoteOptions ?? [] as $noteOption)
                <option value="{{ $noteOption->id }}">#{{ $noteOption->id }} — {{ $noteOption->delivery_note }}</option>
              @endforeach
            </select>
            <div class="oc-help">Optional: verknüpften Lieferschein auswählen.</div>
            <div class="oc-error" data-error="linked_delivery_note_id"></div>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Beschreibung</label>
            <textarea class="oc-textarea" name="description" rows="4"></textarea>
            <div class="oc-error" data-error="description"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">PDF-Datei</label>
            <input type="file" class="oc-input-form" name="pdf" accept="application/pdf">
            <div class="oc-error" data-error="pdf"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bild</label>
            <input type="file" class="oc-input-form" name="image" accept="image/*">
            <div class="oc-error" data-error="image"></div>
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('createDeliveryNoteModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="oc-modal-backdrop" id="editDeliveryNoteModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Lieferschein bearbeiten</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('editDeliveryNoteModal')">×</button>
    </div>

    <form id="editDeliveryNoteForm" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="id" id="edit_id">
      <input type="hidden" name="customer_id" id="edit_customer_id">
      <input type="hidden" name="alternative_id" id="edit_alternative_id">
      <input type="hidden" name="lead_product_list_id" id="edit_lead_product_list_id">
      <input type="hidden" name="product_id" id="edit_product_id">
      <input type="hidden" name="deal_id" id="edit_deal_id">

      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Lieferschein-Nr. *</label>
            <input type="text" class="oc-input-form" name="delivery_note" autocomplete="off">
            <div class="oc-error" data-error="delivery_note"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Geliefert von *</label>
            <input type="text" class="oc-input-form" name="delivered_from" autocomplete="off">
            <div class="oc-error" data-error="delivered_from"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zieltyp *</label>
            <select class="oc-select" name="destination_type" id="edit_destination_type">
              <option value="customer">Kunde</option>
              <option value="warehouse">Lager</option>
            </select>
            <div class="oc-error" data-error="destination_type"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zweig</label>
            <select class="oc-select" name="branch_id">
              <option value="">Bitte wählen</option>
              @foreach($branches ?? [] as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
              @endforeach
            </select>
            <div class="oc-error" data-error="branch_id"></div>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Kunde / Produkt / Objekt *</label>
            <select
              class="oc-select js-customer-product-select"
              id="edit_customer_product"
              data-placeholder="Kunde, Produkt oder Objekt suchen"
            ></select>
            <div class="oc-help" id="edit_deal_hint">Nach Auswahl wird der passende Auftrag automatisch gesucht.</div>
            <div class="oc-error" data-error="customer_id"></div>
            <div class="oc-error" data-error="lead_product_list_id"></div>
            <div class="oc-error" data-error="product_id"></div>
            <div class="oc-error" data-error="deal_id"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Übergabe durch</label>
            <select class="oc-select" name="handover_by">
              <option value="">Bitte wählen</option>
              @foreach($employees ?? [] as $employee)
                <option value="{{ $employee->id }}">{{ trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) }}</option>
              @endforeach
            </select>
            <div class="oc-error" data-error="handover_by"></div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestellt von</label>
            <input type="text" class="oc-input-form" name="order_by" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestellnummer</label>
            <input type="text" class="oc-input-form" name="order_no" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kommission</label>
            <input type="text" class="oc-input-form" name="comission" autocomplete="off">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bestelldatum</label>
            <input type="date" class="oc-input-form" name="order_date">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Übergabedatum</label>
            <input type="date" class="oc-input-form" name="handover_date">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select class="oc-select" name="status">
              <option value="Verfügbar">Verfügbar</option>
              <option value="Nicht verfügbar">Nicht verfügbar</option>
              <option value="Teilweise">Teilweise</option>
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Fortschritt</label>
            <select class="oc-select" name="progress">
              @for($i = 0; $i <= 100; $i += 10)
                <option value="{{ $i }}">{{ $i }}%</option>
              @endfor
            </select>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Verlinkter Lieferschein</label>
            <select class="oc-select js-linked-note-select" name="linked_delivery_note_id" id="edit_linked_delivery_note_id" data-placeholder="Bitte Lieferschein wählen">
              <option value="">Bitte wählen</option>
              @foreach($deliveryNoteOptions ?? [] as $noteOption)
                <option value="{{ $noteOption->id }}">#{{ $noteOption->id }} — {{ $noteOption->delivery_note }}</option>
              @endforeach
            </select>
            <div class="oc-help">Optional: verknüpften Lieferschein auswählen.</div>
            <div class="oc-error" data-error="linked_delivery_note_id"></div>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Beschreibung</label>
            <textarea class="oc-textarea" name="description" rows="4"></textarea>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Neue PDF-Datei</label>
            <input type="file" class="oc-input-form" name="pdf" accept="application/pdf">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Neues Bild</label>
            <input type="file" class="oc-input-form" name="image" accept="image/*">
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('editDeliveryNoteModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Aktualisieren</button>
      </div>
    </form>
  </div>
</div>

{{-- Progress Modal --}}
<div class="oc-modal-backdrop" id="progressDeliveryNoteModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Paketfortschritt aktualisieren</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('progressDeliveryNoteModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form id="progressDeliveryNoteForm">
      @csrf
      <input type="hidden" name="id" id="progress_id">
      <div class="oc-modal-b">
        <div class="oc-form-group">
          <label class="oc-label">Fortschritt</label>
          <select class="oc-select" name="progress" id="progress_value">
            @for($i = 0; $i <= 100; $i += 10)
              <option value="{{ $i }}">{{ $i }}%</option>
            @endfor
          </select>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('progressDeliveryNoteModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Aktualisieren</button>
      </div>
    </form>
  </div>
</div>

{{-- PDF Modal --}}
<div class="oc-modal-backdrop" id="pdfDeliveryNoteModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">PDF-Datei hochladen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('pdfDeliveryNoteModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form id="pdfDeliveryNoteForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="pdf_id">
      <div class="oc-modal-b">
        <div class="oc-form-group">
          <label class="oc-label">PDF-Datei *</label>
          <input type="file" class="oc-input-form" name="pdf" accept="application/pdf" required>
          <div class="oc-error" data-error="pdf"></div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('pdfDeliveryNoteModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Hochladen</button>
      </div>
    </form>
  </div>
</div>

{{-- Delete Modal --}}
<div class="oc-modal-backdrop" id="deleteDeliveryNoteModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Datensatz löschen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('deleteDeliveryNoteModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b">
      <p style="margin:0 0 10px 0;">Möchten Sie diesen Lieferschein wirklich löschen?</p>
      <p class="oc-muted" id="deleteDeliveryNoteText"></p>
    </div>
    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeModal('deleteDeliveryNoteModal')">Abbrechen</button>
      <button type="button" class="oc-btn" style="background:#ef4444;" id="confirmDeleteDeliveryNoteBtn">Löschen</button>
    </div>
  </div>
</div>

{{-- Linked Modal --}}
<div class="oc-modal-backdrop" id="linkedDeliveryNoteModal">
  <div class="oc-modal oc-modal-xl">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Verknüpfte Lieferscheine</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('linkedDeliveryNoteModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b">
      <div class="oc-linked-grid">
        <div class="oc-mini-card">
          <h4>Übergeordneter Lieferschein</h4>
          <div id="linked-parent" class="oc-mini-list">
            <div class="oc-mini-item">
              <div class="oc-mini-item-sub">Keine Daten geladen.</div>
            </div>
          </div>
        </div>

        <div class="oc-mini-card">
          <h4>Untergeordnete Lieferscheine</h4>
          <div id="linked-children" class="oc-mini-list">
            <div class="oc-mini-item">
              <div class="oc-mini-item-sub">Keine Daten geladen.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeModal('linkedDeliveryNoteModal')">Schließen</button>
    </div>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once

@php
$customerOptionsJson = collect($customers ?? [])->map(function ($customer) {
  $label = $customer->display_name
    ?? (($customer->firma ?: trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))) ?: '#' . $customer->id);

  return [
    'id' => (string) $customer->id,
    'text' => $label,
  ];
})->values();

$alternativeOptionsJson = collect($alternatives ?? [])->map(function ($alternative) {
  return [
    'id' => (string) $alternative->id,
    'text' => (($alternative->object_name ?: ('#' . $alternative->id)) . ' — ' . trim(($alternative->street ?? '') . ' ' . ($alternative->city ?? ''))),
    'customer_id' => $alternative->lead_id ? (string) $alternative->lead_id : '',
  ];
})->values();

$dealOptionsJson = collect($deals ?? [])->map(function ($deal) {
  return [
    'id' => (string) $deal->id,
    'text' => $deal->order_number ?: ('#' . $deal->id),
    'customer_id' => $deal->customer_id ? (string) $deal->customer_id : '',
    'alternative_id' => $deal->alternative_id ? (string) $deal->alternative_id : '',
  ];
})->values();

$linkedOptionsJson = collect($deliveryNoteOptions ?? [])->map(function ($note) {
  return [
    'id' => (string) $note->id,
    'text' => '#' . $note->id . ' — ' . $note->delivery_note,
  ];
})->values();
@endphp

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
(() => {
  'use strict';

  const $jq = window.jQuery;

  const routes = {
    analytics: @json(route('delivery-notes.analytics')),
    list: @json(route('delivery-notes.list')),
    store: @json(route('delivery-notes.store')),
    showBase: @json(url('/admin/delivery-notes')),
    customerSearch: @json(route('delivery-notes.customers.search')),
    customerRelatedBase: @json(url('/admin/delivery-notes/customers')),
  };

  const state = {
    listPage: 1,
    deleteId: null,
    scannerInstance: null,
    scannerStarted: false,
    searchTimer: null,
    activeListRequestId: 0,
    linkedOptions: @json($linkedOptionsJson ?? []),
  };

  const els = {
    tableWrapper: document.getElementById('table-wrapper'),
    paginationWrapper: document.getElementById('pagination-wrapper'),
    tableOverlayLoading: document.getElementById('table-overlay-loading'),
    filterForm: document.getElementById('filterForm'),
    filterApply: document.getElementById('filter-apply'),
    filterReset: document.getElementById('filter-reset'),
    filterSearch: document.getElementById('filter-search'),
    confirmDeleteBtn: document.getElementById('confirmDeleteDeliveryNoteBtn'),
    createForm: document.getElementById('createDeliveryNoteForm'),
    editForm: document.getElementById('editDeliveryNoteForm'),
    progressForm: document.getElementById('progressDeliveryNoteForm'),
    pdfForm: document.getElementById('pdfDeliveryNoteForm'),
    progressId: document.getElementById('progress_id'),
    progressValue: document.getElementById('progress_value'),
    pdfId: document.getElementById('pdf_id'),
    deleteText: document.getElementById('deleteDeliveryNoteText'),
    linkedParent: document.getElementById('linked-parent'),
    linkedChildren: document.getElementById('linked-children'),
    toastWrap: document.getElementById('toast-wrap'),
    scannerWrap: document.getElementById('scannerWrap'),
  };

  const qs = (selector, context = document) => context.querySelector(selector);
  const qsa = (selector, context = document) => Array.from(context.querySelectorAll(selector));

  function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value == null ? '' : String(value);
    return div.innerHTML;
  }

  function getCsrf() {
    return qs('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

 function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.add('open');
    document.body.classList.add('oc-modal-open');

    setTimeout(() => {
      $jq(modal).find('select.oc-select').each(function () {
        const $select = $jq(this);

        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('close');
        }
      });
    }, 30);
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    $jq(modal).find('select.select2-hidden-accessible').each(function () {
      $jq(this).select2('close');
    });

    modal.classList.remove('open');

    if (!document.querySelector('.oc-modal-backdrop.open')) {
      document.body.classList.remove('oc-modal-open');
    }
  }

  window.openModal = openModal;
  window.closeModal = closeModal;

  function toast(kind, title, msg) {
    if (!els.toastWrap) return;

    const el = document.createElement('div');
    el.className = 'oc-toast';
    el.innerHTML = `
      <div class="oc-toast-ic ${kind}">
        ${kind === 'bad' ? '×' : '✓'}
      </div>
      <div style="flex:1;">
        <p class="oc-toast-ttl">${escapeHtml(title)}</p>
        <p class="oc-toast-msg">${escapeHtml(msg)}</p>
      </div>
      <button class="oc-toast-x" type="button">×</button>
    `;

    qs('.oc-toast-x', el)?.addEventListener('click', () => el.remove());
    els.toastWrap.appendChild(el);
    setTimeout(() => el.remove(), 4000);
  }

  function showTableLoading(show = true) {
    els.tableOverlayLoading?.classList.toggle('show', !!show);
  }

  function clearFormErrors(form) {
    if (!form) return;

    qsa('.oc-error', form).forEach(el => {
      el.textContent = '';
      el.classList.remove('show');
    });

    qsa('.oc-field-error', form).forEach(el => {
      el.classList.remove('oc-field-error');
    });
  }

  function applyFormErrors(form, errors = {}) {
    clearFormErrors(form);

    Object.keys(errors).forEach(name => {
      const safe = CSS.escape(name);
      const field = qs(`[name="${safe}"]`, form);
      const errorEl = qs(`[data-error="${safe}"]`, form);
      const message = Array.isArray(errors[name]) ? errors[name][0] : errors[name];

      field?.classList.add('oc-field-error');

      if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('show');
      }
    });
  }

  async function postForm(url, form, method = 'POST') {
    const fd = new FormData(form);
    fd.append('_token', getCsrf());

    if (method !== 'POST') {
      fd.append('_method', method);
    }

    const res = await fetch(url, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      throw { status: res.status, data };
    }

    return data;
  }

  function buildQuery(page = 1) {
    const params = new URLSearchParams();
    params.set('page', page);

    [
      'search',
      'status',
      'destination_type',
      'branch_id',
      'handover_by',
      'customer_id',
      'deal_id',
      'progress',
      'sort'
    ].forEach(key => {
      const el = document.getElementById(`filter-${key}`);
      if (el && el.value !== '') params.set(key, el.value);
    });

    return params.toString();
  }

  async function loadAnalytics() {
    try {
      const res = await fetch(routes.analytics, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const data = await res.json();

      const map = {
        'an-total': data.total ?? 0,
        'an-available': data.available ?? 0,
        'an-unavailable': data.unavailable ?? 0,
        'an-completed': data.completed ?? 0,
      };

      Object.keys(map).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = map[id];
      });
    } catch (error) {
      console.error(error);
    }
  }

  async function loadList(page = 1) {
    state.listPage = page;
    state.activeListRequestId++;

    const requestId = state.activeListRequestId;

    if (els.tableWrapper) {
      els.tableWrapper.innerHTML = `<div class="oc-empty">Daten werden geladen...</div>`;
    }

    showTableLoading(true);

    try {
      const res = await fetch(`${routes.list}?${buildQuery(page)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const data = await res.json();

      if (requestId !== state.activeListRequestId) return;

      if (els.tableWrapper) {
        els.tableWrapper.innerHTML = data.html || `<div class="oc-empty">Keine Datensätze gefunden.</div>`;
      }

      if (els.paginationWrapper) {
        els.paginationWrapper.innerHTML = data.pagination || '';
      }

      bindPagination();
    } catch (error) {
      console.error(error);

      if (els.tableWrapper) {
        els.tableWrapper.innerHTML = `<div class="oc-empty">Fehler beim Laden der Daten.</div>`;
      }
    } finally {
      if (requestId === state.activeListRequestId) {
        showTableLoading(false);
      }
    }
  }

  function bindPagination() {
    qsa('#pagination-wrapper a').forEach(link => {
      link.addEventListener('click', event => {
        event.preventDefault();
        const url = new URL(link.href);
        loadList(Number(url.searchParams.get('page') || 1));
      });
    });
  }

  function getSelect2Parent($element) {
      const $modal = $element.closest('.oc-modal-backdrop');

      if ($modal.length) {
        return $modal;
      }

      return $jq(document.body);
    }

    function initSelect2Base($element, placeholder = null) {
      if (!$jq || !$element || !$element.length) return;

      if ($element.hasClass('select2-hidden-accessible')) {
        $element.select2('destroy');
      }

      $element.select2({
        width: '100%',
        placeholder: placeholder || $element.data('placeholder') || 'Bitte wählen',
        allowClear: true,
        dropdownParent: getSelect2Parent($element)
      });
    }

  function setHidden(formId, values = {}) {
    const form = document.getElementById(formId);
    if (!form) return;

    Object.keys(values).forEach(name => {
      const field = qs(`[name="${CSS.escape(name)}"]`, form);
      if (field) field.value = values[name] ?? '';
    });
  }

  function clearCustomerProductHidden(formId) {
    setHidden(formId, {
      customer_id: '',
      alternative_id: '',
      lead_product_list_id: '',
      product_id: '',
      deal_id: ''
    });

    const hint = document.getElementById(formId === 'createDeliveryNoteForm' ? 'create_deal_hint' : 'edit_deal_hint');
    if (hint) hint.textContent = 'Nach Auswahl wird der passende Auftrag automatisch gesucht.';
  }

  function formatCustomerProductResult(item) {
    if (!item.id) return item.text;

    const product = item.product_name || 'Produkt';
    const object = item.object_name || item.object_address || 'Objekt';
    const customer = item.customer_name || item.text || 'Kunde';

    return `
      <div style="display:flex;flex-direction:column;gap:2px;">
        <strong>${escapeHtml(product)} — ${escapeHtml(object)}</strong>
        <small>${escapeHtml(customer)}</small>
      </div>
    `;
  }

  function formatCustomerProductSelection(item) {
    if (!item.id) return item.text || '';

    const product = item.product_name || 'Produkt';
    const object = item.object_name || item.object_address || 'Objekt';
    const customer = item.customer_name || item.text || 'Kunde';

    return `${product} — ${object} · ${customer}`;
  }

  function initCustomerProductSelect(formId, selectId) {
    const $select = $jq(selectId);
    if (!$select.length) return;

    const $parent = $jq(`#${formId}`).closest('.oc-modal-backdrop');

    if ($select.hasClass('select2-hidden-accessible')) {
      $select.select2('destroy');
    }

    $select.select2({
      width: '100%',
      placeholder: $select.data('placeholder') || 'Kunde, Produkt oder Objekt suchen',
      allowClear: true,
      minimumInputLength: 1,
      dropdownParent: $parent.length ? $parent : $jq(document.body),
      dropdownAutoWidth: false,
      escapeMarkup: markup => markup,
      templateResult: formatCustomerProductResult,
      templateSelection: formatCustomerProductSelection,
      ajax: {
        url: routes.customerSearch,
        dataType: 'json',
        delay: 300,
        data: params => ({
          q: params.term || '',
          page: params.page || 1
        }),
        processResults: data => ({
          results: data.results || []
        }),
        cache: true
      }
    });

    $select.on('select2:select', async function (event) {
      const item = event.params.data || {};

      setHidden(formId, {
        customer_id: item.customer_id || item.id || '',
        alternative_id: item.alternative_id || '',
        lead_product_list_id: item.lead_product_list_id || '',
        product_id: item.product_id || '',
        deal_id: ''
      });

      await autoFindDeal(formId, item);
    });

    $select.on('select2:clear', function () {
      clearCustomerProductHidden(formId);
    });
  }

  async function autoFindDeal(formId, item) {
    const customerId = item.customer_id || item.id || '';
    const alternativeId = item.alternative_id || '';
    const productId = item.product_id || '';
    const hint = document.getElementById(formId === 'createDeliveryNoteForm' ? 'create_deal_hint' : 'edit_deal_hint');

    if (!customerId) return;

    if (hint) hint.textContent = 'Auftrag wird gesucht...';

    try {
      const res = await fetch(`${routes.customerRelatedBase}/${customerId}/related-data`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const data = await res.json();
      const deals = data.deals || [];

      const deal = deals.find(row =>
        String(row.alternative_id || '') === String(alternativeId || '') &&
        String(row.product_id || '') === String(productId || '')
      ) || deals.find(row =>
        String(row.product_id || '') === String(productId || '')
      ) || deals.find(row =>
        String(row.alternative_id || '') === String(alternativeId || '')
      ) || null;

      if (deal) {
        setHidden(formId, { deal_id: deal.id });
        if (hint) hint.textContent = `Auftrag gefunden: ${deal.text || '#' + deal.id}`;
      } else {
        setHidden(formId, { deal_id: '' });
        if (hint) hint.textContent = 'Kein passender Auftrag gefunden. Lieferschein kann trotzdem gespeichert werden.';
      }
    } catch (error) {
      console.error(error);
      if (hint) hint.textContent = 'Auftrag konnte nicht automatisch gesucht werden.';
    }
  }

  function buildLinkedOptionsHtml(currentId = null) {
    let html = '<option value="">Bitte wählen</option>';

    (state.linkedOptions || []).forEach(option => {
      if (currentId && String(option.id) === String(currentId)) return;
      html += `<option value="${escapeHtml(option.id)}">${escapeHtml(option.text)}</option>`;
    });

    return html;
  }

  function rebuildLinkedNoteSelect(formId, currentId = null, selectedId = '') {
    const $select = $jq(`#${formId} .js-linked-note-select`);
    if (!$select.length) return;

    $select.html(buildLinkedOptionsHtml(currentId));
    initSelect2Base($select, 'Bitte Lieferschein wählen');
    $select.val(selectedId || '').trigger('change.select2');
  }
  function syncDestinationFields(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    const destinationType = qs('[name="destination_type"]', form)?.value || 'customer';
    const isCustomer = destinationType === 'customer';

    const customerProductWrap = qs('.js-customer-product-select', form)?.closest('.oc-form-group');
    if (customerProductWrap) customerProductWrap.style.display = isCustomer ? '' : 'none';

    if (!isCustomer) {
      clearCustomerProductHidden(formId);
      $jq(form).find('.js-customer-product-select').val(null).trigger('change');
    }
  }

  function resetCreateFormState() {
    if (!els.createForm) return;

    els.createForm.reset();
    clearFormErrors(els.createForm);
    clearCustomerProductHidden('createDeliveryNoteForm');

    $jq('#create_customer_product').val(null).trigger('change');
    rebuildLinkedNoteSelect('createDeliveryNoteForm');

    syncDestinationFields('createDeliveryNoteForm');
  }

  function fillEditForm(data) {
    if (!els.editForm || !data) return;

    clearFormErrors(els.editForm);

    const fields = {
      id: data.id || '',
      delivery_note: data.delivery_note || '',
      delivered_from: data.delivered_from || '',
      destination_type: data.destination_type || 'customer',
      branch_id: data.branch_id || '',
      handover_by: data.handover_by || '',
      order_by: data.order_by || '',
      order_no: data.order_no || '',
      comission: data.comission || '',
      order_date: data.order_date || '',
      handover_date: data.handover_date || '',
      status: data.status || 'Verfügbar',
      progress: data.progress ?? 0,
      description: data.description || '',
      customer_id: data.customer_id || '',
      alternative_id: data.alternative_id || '',
      lead_product_list_id: data.lead_product_list_id || '',
      product_id: data.product_id || '',
      deal_id: data.deal_id || '',
    };

    Object.keys(fields).forEach(name => {
      const field = qs(`[name="${CSS.escape(name)}"]`, els.editForm);
      if (field) field.value = fields[name];
    });

    const productName = data.lead_product_list?.product?.name || data.deal?.product?.name || 'Produkt';
    const objectName = data.alternative?.object_name || data.alternative?.street || 'Objekt';
    const customerName = data.customer?.display_name || data.customer?.firma || `${data.customer?.name || ''} ${data.customer?.lastname || ''}`.trim() || 'Kunde';

    const selectedText = `${productName} — ${objectName} · ${customerName}`;

    const option = new Option(selectedText, data.lead_product_list_id || data.customer_id || '', true, true);
    $jq('#edit_customer_product').empty().append(option).trigger('change');

    rebuildLinkedNoteSelect(
      'editDeliveryNoteForm',
      data.id ? String(data.id) : null,
      data.linked_delivery_note_id ? String(data.linked_delivery_note_id) : ''
    );

    syncDestinationFields('editDeliveryNoteForm');
  }

  function makeLinkedItemHtml(item) {
    if (!item) {
      return `<div class="oc-mini-item"><div class="oc-mini-item-sub">Kein verknüpfter Lieferschein vorhanden.</div></div>`;
    }

    const employee = item.handover_employee
      ? `${item.handover_employee.name ?? ''} ${item.handover_employee.lastname ?? ''}`.trim()
      : '—';

    return `
      <div class="oc-mini-item">
        <div class="oc-mini-item-title">#${escapeHtml(item.id)} · ${escapeHtml(item.delivery_note || '—')}</div>
        <div class="oc-mini-item-sub">Übergabe: ${escapeHtml(employee)}</div>
        <div class="oc-mini-item-sub">Datum: ${escapeHtml(item.handover_date || '—')}</div>
      </div>
    `;
  }

  async function openEditModal(id) {
    try {
      const res = await fetch(`${routes.showBase}/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const result = await res.json();

      fillEditForm(result.data || {});
      openModal('editDeliveryNoteModal');
    } catch (error) {
      console.error(error);
      toast('bad', 'Fehler', 'Der Datensatz konnte nicht geladen werden.');
    }
  }

  async function openLinkedModal(id) {
    try {
      const res = await fetch(`${routes.showBase}/${id}/linked`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const data = await res.json();

      if (els.linkedParent) {
        els.linkedParent.innerHTML = makeLinkedItemHtml(data.parent || null);
      }

      if (els.linkedChildren) {
        els.linkedChildren.innerHTML = Array.isArray(data.children) && data.children.length
          ? data.children.map(item => makeLinkedItemHtml(item)).join('')
          : `<div class="oc-mini-item"><div class="oc-mini-item-sub">Keine untergeordneten Lieferscheine vorhanden.</div></div>`;
      }

      openModal('linkedDeliveryNoteModal');
    } catch (error) {
      console.error(error);
      toast('bad', 'Fehler', 'Verknüpfte Daten konnten nicht geladen werden.');
    }
  }

  function prepareDelete(id, label) {
    state.deleteId = id;
    if (els.deleteText) els.deleteText.textContent = `Lieferschein: ${label}`;
    openModal('deleteDeliveryNoteModal');
  }

  function prepareProgress(id, progress) {
    if (els.progressId) els.progressId.value = id;
    if (els.progressValue) els.progressValue.value = progress ?? 0;
    openModal('progressDeliveryNoteModal');
  }

  function preparePdf(id) {
    if (els.pdfId) els.pdfId.value = id;

    if (els.pdfForm) {
      els.pdfForm.reset();
      clearFormErrors(els.pdfForm);
    }

    openModal('pdfDeliveryNoteModal');
  }

  async function confirmDelete() {
    if (!state.deleteId) return;

    try {
      const fd = new FormData();
      fd.append('_token', getCsrf());
      fd.append('_method', 'DELETE');

      const res = await fetch(`${routes.showBase}/${state.deleteId}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
      });

      const data = await res.json();
      if (!res.ok) throw data;

      closeModal('deleteDeliveryNoteModal');
      toast('ok', 'Gelöscht', data.message || 'Datensatz gelöscht.');
      loadAnalytics();
      loadList(state.listPage);
    } catch (error) {
      console.error(error);
      toast('bad', 'Fehler', error?.message || 'Löschen fehlgeschlagen.');
    }
  }

  async function startScanner() {
    els.scannerWrap?.classList.remove('oc-hidden');

    try {
      if (!state.scannerInstance) {
        state.scannerInstance = new Html5QrcodeScanner('reader', {
          fps: 10,
          qrbox: { width: 250, height: 250 },
          supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        }, false);
      }

      if (!state.scannerStarted) {
        state.scannerInstance.render(decodedText => {
          const input = qs('#createDeliveryNoteForm [name="delivery_note"]');
          if (input) input.value = decodedText;
          toast('ok', 'Scanner', 'Code erfolgreich erkannt.');
        }, () => {});
        state.scannerStarted = true;
      }
    } catch (error) {
      console.error(error);
      toast('bad', 'Scanner', 'Scanner konnte nicht gestartet werden.');
    }
  }

  function stopScanner() {
    els.scannerWrap?.classList.add('oc-hidden');
  }

  function bindToolbar() {
    els.filterApply?.addEventListener('click', () => loadList(1));

    els.filterReset?.addEventListener('click', () => {
      els.filterForm?.reset();

      $jq('#filterForm select.oc-select').each(function () {
        $jq(this).val('').trigger('change.select2');
      });

      loadList(1);
    });

    els.filterSearch?.addEventListener('input', () => {
      clearTimeout(state.searchTimer);
      state.searchTimer = setTimeout(() => loadList(1), 450);
    });

    [
      'filter-status',
      'filter-destination_type',
      'filter-branch_id',
      'filter-handover_by',
      'filter-progress',
      'filter-sort'
    ].forEach(id => {
      document.getElementById(id)?.addEventListener('change', () => loadList(1));
    });
  }

  function bindGlobalClickHandler() {
    document.addEventListener('click', event => {
      if (event.target.classList.contains('oc-modal-backdrop')) {
        closeModal(event.target.id);
        return;
      }

      const collapseToggle = event.target.closest('[data-collapse-target]');
      if (collapseToggle) {
        event.preventDefault();

        const target = document.getElementById(collapseToggle.dataset.collapseTarget);
        if (!target) return;

        const isOpen = target.classList.toggle('open');
        collapseToggle.classList.toggle('open', isOpen);

        const label = qs('[data-collapse-label]', collapseToggle);
        if (label) label.textContent = isOpen ? 'Gruppe schließen' : 'Gruppe öffnen';
        return;
      }

      const editBtn = event.target.closest('[data-action="edit"]');
      if (editBtn) return openEditModal(editBtn.dataset.id);

      const deleteBtn = event.target.closest('[data-action="delete"]');
      if (deleteBtn) return prepareDelete(deleteBtn.dataset.id, deleteBtn.dataset.label || '');

      const progressBtn = event.target.closest('[data-action="progress"]');
      if (progressBtn) return prepareProgress(progressBtn.dataset.id, progressBtn.dataset.progress);

      const pdfBtn = event.target.closest('[data-action="pdf"]');
      if (pdfBtn) return preparePdf(pdfBtn.dataset.id);

      const linkedBtn = event.target.closest('[data-action="linked"]');
      if (linkedBtn) return openLinkedModal(linkedBtn.dataset.id);

      const toggleBtn = event.target.closest('[data-action="toggle-status"]');
      if (toggleBtn) {
        fetch(`${routes.showBase}/${toggleBtn.dataset.id}/toggle-status`, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrf(),
          }
        })
        .then(res => res.json())
        .then(data => {
          toast('ok', 'Status', data.message || 'Status aktualisiert.');
          loadAnalytics();
          loadList(state.listPage);
        })
        .catch(error => {
          console.error(error);
          toast('bad', 'Fehler', 'Status konnte nicht aktualisiert werden.');
        });
      }
    });
  }

  function bindForms() {
    els.confirmDeleteBtn?.addEventListener('click', confirmDelete);

    els.createForm?.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearFormErrors(this);

      try {
        const data = await postForm(routes.store, this, 'POST');

        closeModal('createDeliveryNoteModal');
        resetCreateFormState();

        toast('ok', 'Gespeichert', data.message || 'Datensatz erfolgreich gespeichert.');
        loadAnalytics();
        loadList(1);
      } catch (error) {
        if (error.status === 422) {
          applyFormErrors(this, error.data.errors || {});
        } else {
          console.error(error);
          toast('bad', 'Fehler', error?.data?.message || 'Speichern fehlgeschlagen.');
        }
      }
    });

    els.editForm?.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearFormErrors(this);

      const id = qs('[name="id"]', this)?.value;

      if (!id) {
        toast('bad', 'Fehler', 'Datensatz-ID fehlt.');
        return;
      }

      try {
        const data = await postForm(`${routes.showBase}/${id}`, this, 'POST');

        closeModal('editDeliveryNoteModal');
        toast('ok', 'Aktualisiert', data.message || 'Datensatz aktualisiert.');
        loadAnalytics();
        loadList(state.listPage);
      } catch (error) {
        if (error.status === 422) {
          applyFormErrors(this, error.data.errors || {});
        } else {
          console.error(error);
          toast('bad', 'Fehler', error?.data?.message || 'Aktualisierung fehlgeschlagen.');
        }
      }
    });

    els.progressForm?.addEventListener('submit', async function (event) {
      event.preventDefault();

      const id = els.progressId?.value;
      if (!id) return toast('bad', 'Fehler', 'Datensatz-ID fehlt.');

      try {
        const data = await postForm(`${routes.showBase}/${id}/progress`, this, 'POST');

        closeModal('progressDeliveryNoteModal');
        toast('ok', 'Aktualisiert', data.message || 'Fortschritt aktualisiert.');
        loadList(state.listPage);
        loadAnalytics();
      } catch (error) {
        console.error(error);
        toast('bad', 'Fehler', error?.data?.message || 'Fortschritt konnte nicht aktualisiert werden.');
      }
    });

    els.pdfForm?.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearFormErrors(this);

      const id = els.pdfId?.value;
      if (!id) return toast('bad', 'Fehler', 'Datensatz-ID fehlt.');

      try {
        const data = await postForm(`${routes.showBase}/${id}/pdf`, this, 'POST');

        closeModal('pdfDeliveryNoteModal');
        toast('ok', 'PDF', data.message || 'PDF-Datei gespeichert.');
        loadList(state.listPage);
        loadAnalytics();
      } catch (error) {
        if (error.status === 422) {
          applyFormErrors(this, error.data.errors || {});
        } else {
          console.error(error);
          toast('bad', 'Fehler', error?.data?.message || 'PDF konnte nicht gespeichert werden.');
        }
      }
    });
  }

  function boot() {
    if (!$jq) {
      console.error('jQuery is missing. Select2 requires jQuery.');
      return;
    }

    initCustomerProductSelect('createDeliveryNoteForm', '#create_customer_product');
    initCustomerProductSelect('editDeliveryNoteForm', '#edit_customer_product');

    rebuildLinkedNoteSelect('createDeliveryNoteForm');
    rebuildLinkedNoteSelect('editDeliveryNoteForm');
 

   qsa('.oc-select').forEach(select => {
      if (
        select.classList.contains('js-customer-product-select') ||
        select.classList.contains('js-linked-note-select')
      ) return;

      initSelect2Base($jq(select));
    });
    qs('#create_destination_type')?.addEventListener('change', () => syncDestinationFields('createDeliveryNoteForm'));
    qs('#edit_destination_type')?.addEventListener('change', () => syncDestinationFields('editDeliveryNoteForm'));

    document.getElementById('toggleQrScannerBtn')?.addEventListener('click', startScanner);
    document.getElementById('toggleBarcodeScannerBtn')?.addEventListener('click', startScanner);
    document.getElementById('stopScannerBtn')?.addEventListener('click', stopScanner);

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') {
        qsa('.oc-modal-backdrop.open').forEach(el => closeModal(el.id));
      }
    });

    bindToolbar();
    bindGlobalClickHandler();
    bindForms();

    syncDestinationFields('createDeliveryNoteForm');
    syncDestinationFields('editDeliveryNoteForm');

    loadAnalytics();
    loadList(1);
  }

  window.addEventListener('load', boot);
})();
</script>
@endpush
@endonce


@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Lieferscheine',
        url: "{{ url()->current()}}",
        clickable: false

      }

    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush