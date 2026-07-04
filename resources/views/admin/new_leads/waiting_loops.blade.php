@extends('admin.layouts.app')

@section('title', 'Warteschleife Leads')

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

$fs = function ($v, string $fallback = 'Nicht angegeben') {
    $vv = is_string($v) ? trim($v) : $v;
    return (isset($vv) && $vv !== '' && $vv !== null) ? $vv : $fallback;
};

$fsName = function ($first, $last) use ($fs) {
    $full = trim(($first ?? '') . ' ' . ($last ?? ''));
    return $fs($full, 'Unbenannt');
};

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;

$currentSort = request('sort_by', $sortBy ?? 'lead_product_lists.created_at');
$currentDirection = strtolower(request('sort_order', $sortOrder ?? 'desc'));
$currentDirection = in_array($currentDirection, ['asc', 'desc'], true) ? $currentDirection : 'desc';

$allowedSorts = [
    'lead_product_lists.id',
    'new_leads.id',
    'new_leads.name',
    'new_leads.lastname',
    'lead_product_lists.created_at',
    'article_groups.article_group',
    'alt.city',
    'alt.postcode',
    'status',
];

if (!in_array($currentSort, $allowedSorts, true)) {
    $currentSort = 'lead_product_lists.created_at';
}

$sortUrl = function (string $column) use ($currentSort, $currentDirection, $allowedSorts) {
    if (!in_array($column, $allowedSorts, true)) {
        $column = 'lead_product_lists.created_at';
    }

    $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

    return request()->fullUrlWithQuery([
        'sort_by' => $column,
        'sort_order' => $direction,
        'page' => 1,
    ]);
};

$sortIcon = function (string $column) use ($currentSort, $currentDirection) {
    if ($currentSort !== $column) {
        return 'feather icon-chevrons-up';
    }

    return $currentDirection === 'asc'
        ? 'feather icon-chevron-up'
        : 'feather icon-chevron-down';
};

$servicesMap = [
    'complete' => 'Komplettlösung',
    'montage' => 'Montage',
    'product' => 'Produkt',
    'plan' => 'Planung',
    'maintenance' => 'Wartung',
    'repair' => 'Reparatur',
    'reclaim' => 'Reklamation',
    'emergency' => 'Notdienst',
    'others' => 'Sonstiges',
];

$defaultMale = asset('images/gender/male.png');
$defaultFemale = asset('images/gender/female.png');
@endphp

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root{
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
    --purple:#7c3aed;
    --purple-light:#f5f3ff;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
}

.oc-wrap{
    font-family:Inter, system-ui, -apple-system, sans-serif;
    color:var(--text-main); 
}

.oc-header{
    margin:0 18px;
}

.oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
}

.oc-title{
    font-size:26px;
    font-weight:800;
    letter-spacing:-.025em;
    color:#111827;
}

.oc-sub{
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

.oc-breadcrumb a:hover{
    color:var(--text-main);
}

.oc-breadcrumb span.current{
    color:#111827;
    font-weight:800;
}

.oc-btn,
.oc-btn-soft,
.oc-btn-ic{
    transition:var(--transition);
    text-decoration:none;
}

.oc-btn{
    background:var(--primary);
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
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
    display:inline-flex;
    align-items:center;
    gap:8px;
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
}

.oc-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
}

.oc-btn-ic.primary{
    color:var(--primary);
    border-color:var(--primary-light);
    background:var(--primary-light);
}

.oc-btn-ic.warning{
    color:#d97706;
    border-color:#fde7b0;
    background:#fffbeb;
}

.oc-btn-ic.success{
    color:var(--success);
    border-color:#c7f2df;
    background:var(--success-light);
}

.oc-btn-ic.danger{
    color:var(--danger);
    border-color:rgba(239,68,68,.18);
    background:var(--danger-light);
}

.oc-analytics{
    display:grid;
    grid-template-columns:repeat(6,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
}

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

.oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
.oc-stat-icon.customer{background:var(--success-light);color:var(--success)}
.oc-stat-icon.product{background:var(--primary-light);color:var(--primary)}
.oc-stat-icon.overdue{background:var(--danger-light);color:var(--danger)}
.oc-stat-icon.today{background:var(--warning-light);color:#d97706}
.oc-stat-icon.city{background:var(--purple-light);color:var(--purple)}

.oc-stat-meta{min-width:0}

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

.oc-insight-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
}

.oc-insight{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow-sm);
}

.oc-insight-title{
    font-size:13px;
    font-weight:900;
    color:#111827;
    margin-bottom:12px;
    display:flex;
    align-items:center;
    gap:8px;
}

.oc-mini-bars{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.oc-mini-bar-row{
    display:grid;
    grid-template-columns:minmax(140px,1fr) 1fr 42px;
    align-items:center;
    gap:10px;
}

.oc-mini-bar-label{
    font-size:12px;
    color:#374151;
    font-weight:800;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.oc-mini-bar-track{
    height:8px;
    background:#f3f4f6;
    border-radius:999px;
    overflow:hidden;
}

.oc-mini-bar-fill{
    height:100%;
    border-radius:999px;
    background:linear-gradient(90deg, var(--primary), var(--blue));
}

.oc-mini-bar-value{
    font-size:12px;
    color:var(--text-muted);
    font-weight:900;
    text-align:right;
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

.oc-toolbar-left,
.oc-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
}

.oc-toolbar-left{
    flex:1;
}

.oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
}

.oc-filter-block.search{
    flex:1 1 420px;
    min-width:320px;
}

.oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
}

.oc-input,
.oc-select{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    min-width:180px;
    width:100%;
}

.oc-input.search{
    padding-left:36px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
}

.oc-input:focus,
.oc-select:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
}

.oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:visible;
}

.oc-list-head{
    display:grid;
    grid-template-columns:88px minmax(260px,1.2fr) minmax(220px,.9fr) minmax(280px,1fr) minmax(150px,.7fr) minmax(160px,.7fr) 140px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.oc-list-head a{
    color:inherit;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.oc-list-head a:hover{
    color:#111827;
}

.oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px;
}

.oc-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    transition:var(--transition);
    overflow:visible;
    margin:0 16px;
    position:relative;
    z-index:1;
}

.oc-item:hover{
    border-color:var(--primary);
    box-shadow:var(--shadow);
    z-index:5;
}

.oc-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:start;
    grid-template-columns:88px minmax(260px,1.2fr) minmax(220px,.9fr) minmax(280px,1fr) minmax(150px,.7fr) minmax(160px,.7fr) 140px;
}

.oc-cell{
    min-width:0;
}

.oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
}

.oc-id-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:64px;
    height:36px;
    padding:0 12px;
    border-radius:10px;
    background:var(--blue-light);
    color:var(--blue);
    font-size:13px;
    font-weight:900;
}

.oc-avatar{
    width:52px;
    height:52px;
    border-radius:14px;
    background:#eff6ff;
    color:var(--blue);
    border:1px solid #dbeafe;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:18px;
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

.oc-ttl a{
    color:inherit;
    text-decoration:none;
}

.oc-ttl a:hover{
    color:var(--blue);
    text-decoration:none;
}

.oc-subt,
.oc-subt-wrap{
    font-size:13px;
    color:var(--text-muted);
    line-height:1.45;
}

.oc-subt-wrap{
    white-space:normal;
}

.oc-mini{
    font-size:12px;
    color:var(--text-muted);
}

.oc-note{
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:10px;
    padding:6px 8px;
    margin-top:8px;
}

.oc-badges{
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}

.oc-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
}

.oc-badge.gray{background:#f3f4f6;color:#4b5563}
.oc-badge.green{background:#ecfdf5;color:#047857}
.oc-badge.orange{background:#fffbeb;color:#b45309}
.oc-badge.red{background:#fef2f2;color:#b91c1c}
.oc-badge.blue{background:#eff6ff;color:var(--blue)}
.oc-badge.purple{background:#f5f3ff;color:#7c3aed}

.oc-flow-card{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.oc-flow-top{
    display:flex;
    align-items:center;
    flex-wrap:nowrap;
    gap:0;
}

.oc-flow-badge,
.oc-flow-avatar{
    width:42px;
    height:42px;
    min-width:42px;
    border-radius:999px;
    position:relative;
    z-index:2;
}

.oc-flow-badge{
    background:#93c21c;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    font-size:15px;
    font-weight:800;
    letter-spacing:.01em;
    border:3px solid #93c21c;
    box-shadow:0 1px 4px rgba(147,194,28,.16);
}

.oc-flow-line{
    width:14px;
    height:4px;
    background:#93c21c;
    border-radius:999px;
    margin:0 -1px;
    position:relative;
    z-index:1;
}

.oc-flow-avatar{
    object-fit:cover;
    background:#fff;
    border:3px solid #f0a356;
    box-shadow:0 1px 4px rgba(15,23,42,.08);
    cursor:pointer;
}

.oc-flow-avatar:hover{
    transform:translateY(-1px);
    box-shadow:0 8px 18px rgba(15,23,42,.18);
}

.oc-flow-meta{
    margin-top:2px;
    padding-left:1px;
}

.oc-flow-service{
    font-size:12px;
    line-height:1.2;
    font-weight:800;
    color:#374151;
}

.oc-flow-department{
    font-size:11px;
    line-height:1.2;
    font-weight:700;
    color:#94a3b8;
    margin-top:2px;
}

.oc-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
    position:relative;
    z-index:30;
}

.oc-row-menu{
    position:relative;
    z-index:50;
}

.oc-row-dropdown{
    position:absolute;
    right:0;
    top:42px;
    min-width:220px;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    box-shadow:0 18px 40px rgba(15,23,42,.18);
    padding:8px;
    display:none;
    z-index:9999;
}

.oc-row-dropdown.open{
    display:block;
}

.oc-row-dropdown-item{
    width:100%;
    border:none;
    background:transparent;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border-radius:10px;
    color:#374151;
    text-decoration:none;
    font-size:13px;
    font-weight:700;
    text-align:left;
    cursor:pointer;
}

.oc-row-dropdown-item:hover{
    background:#f8fafc;
    color:#111827;
    text-decoration:none;
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

.oc-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
}

.oc-pagination-links .pagination{
    margin:0;
    display:flex;
    flex-wrap:wrap;
    gap:6px;
}

.oc-pagination-links .page-item .page-link{
    border-radius:10px !important;
    border:1px solid var(--border);
    color:var(--text-main);
    padding:8px 12px;
    line-height:1.1;
    box-shadow:none !important;
}

.oc-pagination-links .page-item.active .page-link{
    background:var(--primary);
    border-color:var(--primary);
    color:#fff;
}

.oc-modal-backdrop{
    position:fixed;
    inset:0;
    z-index:1200;
    background:rgba(17,24,39,.55);
    backdrop-filter:blur(3px);
    opacity:0;
    pointer-events:none;
    transition:opacity .22s ease;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:18px;
}

.oc-modal-backdrop.open{
    opacity:1;
    pointer-events:auto;
}

.oc-modal{
    width:100%;
    max-width:860px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
}

.oc-modal-backdrop.open .oc-modal{
    transform:translateY(0) scale(1);
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

.oc-timeline{
    list-style:none;
    margin:0;
    padding:0;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.oc-timeline-item{
    display:grid;
    grid-template-columns:42px 1fr;
    gap:12px;
    align-items:flex-start;
}

.oc-timeline-icon{
    width:42px;
    height:42px;
    border-radius:999px;
    background:var(--primary);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
}

.oc-timeline-body{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px;
}

.oc-timeline-title{
    font-size:14px;
    font-weight:900;
    color:#111827;
}

.oc-timeline-text{
    font-size:13px;
    color:#374151;
    margin-top:4px;
}

.oc-timeline-time{
    font-size:12px;
    color:var(--text-muted);
    margin-top:8px;
}

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
    from{transform:translateX(100%);opacity:0}
    to{transform:translateX(0);opacity:1}
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

.oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
.oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}

.oc-toast-ttl{
    font-weight:900;
    font-size:13px;
    margin:0;
    color:#111827;
}

.oc-toast-msg{
    font-size:12px;
    color:#374151;
    margin:4px 0 0;
    line-height:1.4;
}

.oc-toast-x{
    margin-left:auto;
    background:transparent;
    border:none;
    cursor:pointer;
    color:var(--text-muted);
}

.swal2-popup{
    font-size:16px !important;
    width:600px !important;
    max-width:92vw;
}

.swal2-select{
    width:100% !important;
    padding:10px;
    font-size:15px;
    border-radius:6px;
    border:1px solid #ced4da;
    margin-top:8px;
}

@media (max-width:1400px){
    .oc-analytics{
        grid-template-columns:repeat(3,minmax(0,1fr));
    }

    .oc-list-head{
        display:none;
    }

    .oc-item-row{
        grid-template-columns:1fr;
    }

    .oc-cell-title{
        display:block;
    }

    .oc-actions{
        justify-content:flex-start;
    }
}

@media (max-width:980px){
    .oc-insight-grid{
        grid-template-columns:1fr;
    }

    .oc-toolbar-left,
    .oc-toolbar-right{
        width:100%;
    }

    .oc-filter-block,
    .oc-filter-block.search{
        min-width:100%;
        flex:1 1 100%;
    }
}

@media (max-width:700px){
    .oc-analytics{
        grid-template-columns:1fr;
    }

    .oc-wrap{
        padding:36px 18px;
    }
}
</style>
@endsection

@section('content')
    @include('admin.new_leads._tabs')
    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">WARTESCHLEIFE LEADS</div>
                    <div class="oc-sub">
                        Leads ohne zugewiesenen Innendienst-Mitarbeiter, inklusive Status, Produkt, Objekt und Bearbeitungshistorie.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <span class="current">Warteschleife Leads</span>
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                    <a href="{{ url('/new_lead_view') }}" class="oc-btn-soft">
                        <i class="feather icon-list"></i>
                        Alle Leads
                    </a>

                    <a href="{{ route('waiting.loop.leads') }}" class="oc-btn">
                        <i class="feather icon-refresh-cw"></i>
                        Aktualisieren
                    </a>
                </div>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Warteschleife</div>
                    <div class="oc-stat-value">{{ $waitingTotal ?? 0 }}</div>
                    <div class="oc-stat-sub">Offene Positionen</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon customer">
                    <i class="feather icon-users"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Kunden</div>
                    <div class="oc-stat-value">{{ $uniqueCustomers ?? 0 }}</div>
                    <div class="oc-stat-sub">Eindeutige Leads</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon product">
                    <i class="feather icon-package"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Produkte</div>
                    <div class="oc-stat-value">{{ $uniqueProducts ?? 0 }}</div>
                    <div class="oc-stat-sub">Gewerke betroffen</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon overdue">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Über 48h</div>
                    <div class="oc-stat-value">{{ $waitingOver48 ?? 0 }}</div>
                    <div class="oc-stat-sub">Dringend prüfen</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon today">
                    <i class="feather icon-calendar"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Heute</div>
                    <div class="oc-stat-value">{{ $waitingToday ?? 0 }}</div>
                    <div class="oc-stat-sub">Heute eingegangen</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon city">
                    <i class="feather icon-map-pin"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Städte</div>
                    <div class="oc-stat-value">{{ $cityCount ?? 0 }}</div>
                    <div class="oc-stat-sub">Regionen</div>
                </div>
            </div>
        </div>

        <div class="oc-insight-grid">
            <div class="oc-insight">
                <div class="oc-insight-title">
                    <i class="feather icon-package"></i>
                    Top Gewerke in Warteschleife
                </div>

                <div class="oc-mini-bars">
                    @forelse(($topProducts ?? collect()) as $row)
                        @php
        $max = max(1, ($topProducts ?? collect())->max('count') ?? 1);
        $width = (($row['count'] ?? 0) / $max) * 100;
                        @endphp

                        <div class="oc-mini-bar-row">
                            <div class="oc-mini-bar-label">{{ $row['name'] }}</div>
                            <div class="oc-mini-bar-track">
                                <div class="oc-mini-bar-fill" style="width:{{ $width }}%;"></div>
                            </div>
                            <div class="oc-mini-bar-value">{{ $row['count'] }}</div>
                        </div>
                    @empty
                        <div class="oc-mini">Keine Produktdaten vorhanden.</div>
                    @endforelse
                </div>
            </div>

            <div class="oc-insight">
                <div class="oc-insight-title">
                    <i class="feather icon-map"></i>
                    Top Städte
                </div>

                <div class="oc-mini-bars">
                    @forelse(($topCities ?? collect()) as $row)
                        @php
        $max = max(1, ($topCities ?? collect())->max('count') ?? 1);
        $width = (($row['count'] ?? 0) / $max) * 100;
                        @endphp

                        <div class="oc-mini-bar-row">
                            <div class="oc-mini-bar-label">{{ $row['name'] }}</div>
                            <div class="oc-mini-bar-track">
                                <div class="oc-mini-bar-fill" style="width:{{ $width }}%;"></div>
                            </div>
                            <div class="oc-mini-bar-value">{{ $row['count'] }}</div>
                        </div>
                    @empty
                        <div class="oc-mini">Keine Städtedaten vorhanden.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <form action="{{ route('waiting.loop.leads') }}" method="GET" class="oc-toolbar">
            <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="oc-input search"
                        placeholder="Suche nach Name, Kundennr., Stadt, Produkt, E-Mail, Telefon ..."
                    >
                </div>
            </div>

            <div class="oc-toolbar-right">
                <input type="hidden" name="sort_by" value="{{ $currentSort }}">
                <input type="hidden" name="sort_order" value="{{ $currentDirection }}">

                <button type="submit" class="oc-btn-soft">
                    <i class="fa fa-search"></i>
                    Filtern
                </button>

                @if(request('search') || request('sort_by') || request('sort_order'))
                    <a href="{{ route('waiting.loop.leads') }}" class="oc-btn-soft">
                        <i class="fa fa-refresh"></i>
                        Zurücksetzen
                    </a>
                @endif
            </div>
        </form>

        <div class="oc-card">
            <div class="oc-list-head">
                <div>
                    <a href="{{ $sortUrl('lead_product_lists.id') }}">
                        ID
                        <i class="{{ $sortIcon('lead_product_lists.id') }}"></i>
                    </a>
                </div>

                <div>
                    <a href="{{ $sortUrl('new_leads.name') }}">
                        Kunde / Adresse
                        <i class="{{ $sortIcon('new_leads.name') }}"></i>
                    </a>
                </div>

                <div>Kontakt</div>

                <div>
                    <a href="{{ $sortUrl('article_groups.article_group') }}">
                        Gewerk / Zuweisung
                        <i class="{{ $sortIcon('article_groups.article_group') }}"></i>
                    </a>
                </div>

                <div>
                    <a href="{{ $sortUrl('status') }}">
                        Status
                        <i class="{{ $sortIcon('status') }}"></i>
                    </a>
                </div>

                <div>Verfasser</div>

                <div style="text-align:right;">Aktionen</div>
            </div>

            <div class="oc-list">
                @forelse($data as $item)
                    @php
        $safeFullName = $fsName($item->name ?? null, $item->lastname ?? null);
        $firstLetter = mb_strtoupper(mb_substr($safeFullName, 0, 1));

        $createdAt = $item->created_at ?? now();
        $createdCarbon = Carbon::parse($createdAt);
        $hoursDifference = $createdCarbon->diffInHours(now());

        $statusRaw = strtolower(trim((string) ($item->status ?? '')));
        $isRejected = $statusRaw === 'reject';

        $matchedService = null;

        foreach (($service ?? collect()) as $serv) {
            if (
                (string) $serv->alternative_id === (string) $item->alternative_id &&
                (string) $serv->customer_id === (string) $item->lead_id
            ) {
                $matchedService = $servicesMap[$serv->service] ?? $serv->service;
                break;
            }
        }

        if (!$matchedService && !empty($item->phase_section)) {
            $matchedService = $servicesMap[$item->phase_section] ?? $item->phase_section;
        }

        if (!$matchedService && !empty($item->service)) {
            $matchedService = $servicesMap[$item->service] ?? $item->service;
        }

        $defaultImage = ($item->emp_gender ?? 'Male') === 'Male'
            ? $defaultMale
            : $defaultFemale;

        $employeeImage = (!empty($item->emp_image) && file_exists(public_path('images/employee/' . $item->emp_image)))
            ? asset('images/employee/' . $item->emp_image)
            : $defaultImage;

        $contactEmployee = DB::table('employees')
            ->where('id', $item->contact_person)
            ->select('name', 'lastname', 'image', 'gender')
            ->first();

        $contactImage = (!empty($contactEmployee?->image) && file_exists(public_path('images/employee/' . $contactEmployee->image)))
            ? asset('images/employee/' . $contactEmployee->image)
            : (($contactEmployee?->gender ?? 'Male') === 'Male' ? $defaultMale : $defaultFemale);

        $contactName = trim(($contactEmployee->name ?? '') . ' ' . ($contactEmployee->lastname ?? ''));
        $contactName = $contactName !== '' ? $contactName : 'Unbekannt';

        $productInitial = trim((string) ($item->initial ?? ''));
        if ($productInitial === '') {
            $label = trim((string) ($item->article_group ?? 'PR'));
            $productInitial = mb_strtoupper(mb_substr($label, 0, 2));
        }
                    @endphp

                    <div class="oc-item">
                        <div class="oc-item-row">
                            <div class="oc-cell">
                                <div class="oc-cell-title">ID</div>
                                <div class="d-flex flex-column" style="gap:10px;">
                                    <span class="oc-id-badge">#{{ $item->id }}</span>
                                    <div class="oc-avatar">{{ $firstLetter }}</div>

                                    @if(!empty($item->customer_no))
                                        <span class="oc-badge blue">K-Nr. {{ $item->customer_no }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Kunde / Adresse</div>

                                <div class="oc-main">
                                    <div class="oc-ttl">
                                        <a href="{{ url('new_lead_profile/' . $item->lead_id) }}">
                                            {{ $safeFullName }}
                                        </a>
                                    </div>

                                    @if(!empty($item->firma))
                                        <div class="oc-note">
                                            <i class="feather icon-briefcase"></i>
                                            <strong>Firma:</strong> {{ $item->firma }}
                                        </div>
                                    @endif

                                    <div class="oc-subt-wrap mt-1">
                                        <i class="feather icon-map-pin"></i>
                                        {{ $fs($item->street ?? null, 'Unbekannte Straße') }},
                                        {{ $fs($item->postcode ?? null, '—') }}
                                        {{ $fs($item->city ?? null, 'Unbekannte Stadt') }}
                                    </div>

                                    @if(!empty($item->object_name))
                                        <div class="oc-note">
                                            <i class="feather icon-home"></i>
                                            <strong>Objekt:</strong> {{ $item->object_name }}
                                        </div>
                                    @endif

                                    <div class="oc-subt-wrap mt-2">
                                        <i class="feather icon-calendar"></i>
                                        {{ $createdCarbon->isoFormat('DD.MM.YYYY HH:mm') }}
                                        <br>
                                        <strong>{{ $createdCarbon->diffForHumans() }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Kontakt</div>

                                <div class="oc-main">
                                    @if(!empty($item->telephone))
                                        <div class="oc-subt-wrap">
                                            <i class="feather icon-phone-call"></i>
                                            {{ $item->telephone }}
                                        </div>
                                    @endif

                                    @if(!empty($item->phone))
                                        <div class="oc-subt-wrap">
                                            <i class="feather icon-smartphone"></i>
                                            {{ $item->phone }}
                                        </div>
                                    @endif

                                    @if(!empty($item->email))
                                        <div class="oc-subt-wrap">
                                            <i class="feather icon-mail"></i>
                                            {{ $item->email }}
                                        </div>
                                    @endif

                                    @if(!empty($item->source))
                                        <div class="oc-badges mt-2">
                                            <span class="oc-badge gray">Quelle: {{ $item->source }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Gewerk / Zuweisung</div>

                                <div class="oc-flow-card">
                                    <div class="oc-flow-top">
                                        <div class="oc-flow-badge" title="{{ $fs($item->article_group ?? null, 'Produkt') }}">
                                            {{ $productInitial }}
                                        </div>

                                        <div class="oc-flow-line"></div>

                                        <img
                                            src="{{ $employeeImage }}"
                                            alt="{{ $item->status }}"
                                            class="oc-flow-avatar add_employees"
                                            data-employee-id="{{ $item->employee_id ?? '' }}"
                                            data-product-id="{{ $item->product_id }}"
                                            data-new-lead-id="{{ $item->lead_id }}"
                                            data-alternative-id="{{ $item->alternative_id }}"
                                            data-id="{{ $item->id }}"
                                            title="{{ $item->emp_name && $item->emp_lastname ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}"
                                        >
                                    </div>

                                    <div class="oc-flow-meta">
                                        <div class="oc-flow-service">
                                            {{ $matchedService ?? 'Kein Service zugewiesen' }}
                                        </div>
                                        <div class="oc-flow-department">
                                            {{ $fs($item->department_name ?? null, 'Keine Abteilung') }}
                                        </div>
                                        <div class="oc-mini mt-1">
                                            {{ $fs($item->article_group ?? null, 'Unbekanntes Gewerk') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Status</div>

                                <div class="oc-main">
                                    @if($isRejected)
                                        <span class="oc-badge red">
                                            <i class="feather icon-x-circle mr-50"></i>
                                            Anfrage abgelehnt
                                        </span>
                                    @else
                                        <span class="oc-badge orange">
                                            <i class="feather icon-clock mr-50"></i>
                                            Warten
                                        </span>
                                    @endif

                                    <div class="oc-badges mt-2">
                                        @if($hoursDifference > 48)
                                            <span class="oc-badge red">Über 48h</span>
                                        @elseif($createdCarbon->isToday())
                                            <span class="oc-badge green">Heute</span>
                                        @else
                                            <span class="oc-badge gray">In Prüfung</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Verfasser</div>

                                <div class="oc-main">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <img src="{{ $contactImage }}"
                                             alt="avatar"
                                             style="width:42px;height:42px;border-radius:999px;object-fit:cover;border:1px solid #e5e7eb;">

                                        <div>
                                            <div class="oc-ttl" style="font-size:14px;">
                                                {{ $contactName }}
                                            </div>
                                            <div class="oc-subt">
                                                Ansprechpartner
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Aktionen</div>

                                <div class="oc-actions">
                                    <a href="{{ url('new_lead_profile/' . $item->lead_id) }}"
                                       class="oc-btn-ic primary"
                                       title="Profil öffnen">
                                        <i class="feather icon-user"></i>
                                    </a>

                                    <button
                                        type="button"
                                        class="oc-btn-ic warning history_modal"
                                        data-lead-id="{{ $item->lead_id }}"
                                        data-responsible-id="{{ $item->id }}"
                                        title="Historie">
                                        <i class="feather icon-fast-forward"></i>
                                    </button>

                                    <div class="oc-row-menu" data-menu>
                                        <button type="button" class="oc-btn-ic" data-menu-toggle title="Aktionen">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>

                                        <div class="oc-row-dropdown" data-menu-panel>
                                            <a href="{{ url('new_lead_profile/' . $item->lead_id) }}" class="oc-row-dropdown-item">
                                                <i class="feather icon-user"></i>
                                                <span>Profil öffnen</span>
                                            </a>

                                            <button type="button"
                                                    class="oc-row-dropdown-item history_modal"
                                                    data-lead-id="{{ $item->lead_id }}"
                                                    data-responsible-id="{{ $item->id }}">
                                                <i class="feather icon-fast-forward"></i>
                                                <span>Historie</span>
                                            </button>

                                            <button type="button"
                                                    class="oc-row-dropdown-item add_employees"
                                                    data-employee-id="{{ $item->employee_id ?? '' }}"
                                                    data-product-id="{{ $item->product_id }}"
                                                    data-new-lead-id="{{ $item->lead_id }}"
                                                    data-alternative-id="{{ $item->alternative_id }}"
                                                    data-id="{{ $item->id }}">
                                                <i class="feather icon-user-plus"></i>
                                                <span>Mitarbeiter zuweisen</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="oc-empty">
                        <i class="feather icon-inbox" style="font-size:42px;"></i>
                        <div class="mt-2">Keine Warteschleife-Leads gefunden.</div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
            <div class="oc-pagination">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                    <div class="oc-mini">
                        Zeige
                        <strong>{{ $data->firstItem() ?? 0 }}</strong>
                        bis
                        <strong>{{ $data->lastItem() ?? 0 }}</strong>
                        von
                        <strong>{{ $data->total() }}</strong>
                        Einträgen
                    </div>

                    <div class="oc-pagination-links">
                        {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="oc-modal-backdrop" id="notificationTimelineModal">
        <div class="oc-modal">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Benachrichtigungshistorie</h3>
                <button type="button" class="oc-btn-ic" onclick="closeOcModal('notificationTimelineModal')">×</button>
            </div>

            <div class="oc-modal-b">
                <ul id="timelineContainer" class="oc-timeline"></ul>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeOcModal('notificationTimelineModal')">
                    Schließen
                </button>
            </div>
        </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openOcModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
}

function closeOcModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

function toast(kind, title, msg) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const icons = {
        ok: `<i class="feather icon-check"></i>`,
        bad: `<i class="feather icon-alert-triangle"></i>`
    };

    const el = document.createElement('div');
    el.className = 'oc-toast';
    el.innerHTML = `
        <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
        <div style="flex:1;">
            <p class="oc-toast-ttl">${title}</p>
            <p class="oc-toast-msg">${msg}</p>
        </div>
        <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
    `;

    wrap.appendChild(el);

    if (window.feather) {
        feather.replace();
    }

    setTimeout(() => {
        try { el.remove(); } catch(e) {}
    }, 4200);
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('oc-modal-backdrop')) {
        e.target.classList.remove('open');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('[data-menu-panel].open').forEach(panel => panel.classList.remove('open'));
    }
});

@if(Session::has('update_msg'))
toast('ok', 'Aktualisiert', @json(session('update_msg')));
@endif

@if(Session::has('updated_msg'))
toast('ok', 'Aktualisiert', @json(session('updated_msg')));
@endif

@if(Session::has('save_msg'))
toast('ok', 'Gespeichert', @json(session('save_msg')));
@endif

@if(Session::has('delete_msg'))
toast('bad', 'Hinweis', @json(session('delete_msg')));
@endif
</script>

<script>
document.addEventListener('click', function(e) {
    const toggle = e.target.closest('[data-menu-toggle]');

    document.querySelectorAll('[data-menu-panel].open').forEach(panel => {
        if (!panel.contains(e.target) && !panel.previousElementSibling?.contains(e.target)) {
            panel.classList.remove('open');
        }
    });

    if (toggle) {
        const menu = toggle.closest('[data-menu]');
        const panel = menu ? menu.querySelector('[data-menu-panel]') : null;

        if (panel) {
            e.stopPropagation();
            panel.classList.toggle('open');
        }
    }
});
</script>

<script>
$(document).on('click', '.history_modal', function (e) {
    e.preventDefault();

    const leadId = $(this).data('lead-id');
    const responsibleId = $(this).data('responsible-id');
    const timelineContainer = $('#timelineContainer');

    timelineContainer.html(`
        <li class="oc-timeline-item">
            <div class="oc-timeline-icon">
                <i class="feather icon-loader"></i>
            </div>
            <div class="oc-timeline-body">
                <div class="oc-timeline-title">Lade Historie...</div>
                <div class="oc-timeline-text">Bitte warten.</div>
            </div>
        </li>
    `);

    openOcModal('notificationTimelineModal');

    $.ajax({
        url: `/notifications/timeline/${leadId}/${responsibleId}?t=${new Date().getTime()}`,
        method: 'GET',
        success: function (response) {
            timelineContainer.empty();

            if (response.status !== 'success') {
                timelineContainer.html(`
                    <li class="oc-timeline-item">
                        <div class="oc-timeline-icon" style="background:#ef4444;">
                            <i class="feather icon-alert-triangle"></i>
                        </div>
                        <div class="oc-timeline-body">
                            <div class="oc-timeline-title">Fehler</div>
                            <div class="oc-timeline-text">Historie konnte nicht geladen werden.</div>
                        </div>
                    </li>
                `);
                return;
            }

            const notifications = response.notifications || [];

            if (!notifications.length) {
                timelineContainer.html(`
                    <li class="oc-timeline-item">
                        <div class="oc-timeline-icon" style="background:#74b2d4;">
                            <i class="feather icon-info"></i>
                        </div>
                        <div class="oc-timeline-body">
                            <div class="oc-timeline-title">Keine Benachrichtigungen</div>
                            <div class="oc-timeline-text">Für diesen Eintrag wurde keine Historie gefunden.</div>
                        </div>
                    </li>
                `);
                return;
            }

            notifications.forEach(notification => {
                const title = notification?.data?.title || 'Benachrichtigung';
                const message = notification?.data?.message || '';
                const performedAtRaw = notification?.data?.performed_at || notification?.created_at || null;
                const performedAt = performedAtRaw
                    ? new Date(performedAtRaw).toLocaleString('de-DE')
                    : 'Unbekannt';

                timelineContainer.append(`
                    <li class="oc-timeline-item">
                        <div class="oc-timeline-icon">
                            <i class="feather icon-check"></i>
                        </div>
                        <div class="oc-timeline-body">
                            <div class="oc-timeline-title">${escapeHtml(title)}</div>
                            <div class="oc-timeline-text">${escapeHtml(message)}</div>
                            <div class="oc-timeline-time">${escapeHtml(performedAt)}</div>
                        </div>
                    </li>
                `);
            });

            if (window.feather) {
                feather.replace();
            }
        },
        error: function () {
            timelineContainer.html(`
                <li class="oc-timeline-item">
                    <div class="oc-timeline-icon" style="background:#ef4444;">
                        <i class="feather icon-alert-triangle"></i>
                    </div>
                    <div class="oc-timeline-body">
                        <div class="oc-timeline-title">Fehler</div>
                        <div class="oc-timeline-text">Ein Fehler ist aufgetreten.</div>
                    </div>
                </li>
            `);
        }
    });
});

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>

<script>
$(document).on('click', '.add_employees', function () {
    const productId = $(this).data('product-id');
    const leadId = $(this).data('new-lead-id');
    const altId = $(this).data('alternative-id');

    $.post('/getEmployees', {
        _token: '{{ csrf_token() }}'
    }, function (employees) {
        let html = '<select id="employeeSelect" class="swal2-select">';
        html += `<option value="">-- Kein Mitarbeiter --</option>`;

        employees.forEach(emp => {
            html += `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`;
        });

        html += '</select>';

        Swal.fire({
            title: 'Mitarbeiter zuweisen',
            html: html,
            showCancelButton: true,
            confirmButtonText: 'Zuweisen',
            cancelButtonText: 'Abbrechen',
            focusConfirm: false,
            didOpen: () => {
                $('#employeeSelect').select2({
                    dropdownParent: $('.swal2-container'),
                    width: '100%'
                });
            },
            preConfirm: () => {
                return $('#employeeSelect').val();
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const employeeId = result.value;

            $.ajax({
                url: '/update-lead-employee',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    employee_id: employeeId,
                    product_id: productId,
                    alternative_id: altId,
                    customer_id: leadId
                },
                success: function () {
                    Swal.fire('Erfolgreich zugewiesen!', '', 'success')
                        .then(() => location.reload());
                },
                error: function () {
                    Swal.fire('Fehler beim Speichern', '', 'error');
                }
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.feather) {
        feather.replace();
    }

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection

@push('scripts')
        <script>
            window.GlobalBreadcrumbs = [
                {
                    label: 'Dashboard',
                    url: "{{ url('/') }}"
                },
                {
                    label: 'Kundeliste',
                    url: "{{ url('/new_lead_view') }}"
                },
                {
                    label: 'Warteschleife Leads',
                    url: "{{ url()->current() }}",
                    clickable: false
                }
            ];

            if (window.setGlobalBreadcrumbs) {
                window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
            }
        </script>
@endpush