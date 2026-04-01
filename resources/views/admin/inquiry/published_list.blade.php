@extends('admin.layouts.app')
@section('title', 'Veröffentlichte Anfragen')

@php
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Pagination\LengthAwarePaginator;

    $currentRoute = Route::currentRouteName();

    $pageTitle = 'VERÖFFENTLICHTE ANFRAGEN';
    $pageSub   = 'Verwalten Sie veröffentlichte und verifizierte Anfragen, Produkte, Zuständigkeiten und Neu-Verifizierung zentral.';

    $fs = function ($v, string $fallback = 'Nicht angegeben') {
        $vv = is_string($v) ? trim($v) : $v;
        return (isset($vv) && $vv !== '' && $vv !== null) ? $vv : $fallback;
    };

    $fsName = function ($first, $last) use ($fs) {
        $full = trim(($first ?? '') . ' ' . ($last ?? ''));
        return $fs($full, 'Unbenannt');
    };

    $fsCity = fn($city) => $fs($city, 'Unbekannte Stadt');

    $fsType = function ($row) use ($fs) {
        $label = $row->pre_type ?? $row->type_name ?? null;
        return $fs($label, 'Unbekannter Typ');
    };

    $fsImg = function (?string $relativePath, string $fallbackAsset) {
        if (!empty($relativePath) && file_exists(public_path($relativePath))) {
            return asset($relativePath);
        }
        return asset($fallbackAsset);
    };

    $isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
    $collection  = $isPaginator ? collect($data->items()) : collect($data);
    $product_list = collect($productList ?? []);

    $totalCount        = $isPaginator ? $data->total() : $collection->count();
    $publishedCount    = (int) $collection->filter(fn($item) => strtolower(trim((string)($item->status ?? ''))) === 'published')->count();
    $junkCount         = (int) $collection->filter(fn($item) => strtolower(trim((string)($item->status ?? ''))) === 'junk')->count();
    $unpublishedCount  = (int) $collection->filter(fn($item) => strtolower(trim((string)($item->status ?? ''))) === 'unpublished')->count();
    $typedCount        = (int) $collection->filter(fn($item) => !empty($item->type_name) || !empty($item->pre_type))->count();

    $allowedSorts = [
        'id',
        'name',
        'email',
        'status',
        'created_at',
        'periority',
    ];

    $currentSort = request('sort', 'id');
    if (!in_array($currentSort, $allowedSorts, true)) {
        $currentSort = 'id';
    }

    $currentDirection = strtolower(request('direction', 'desc'));
    $currentDirection = in_array($currentDirection, ['asc', 'desc'], true) ? $currentDirection : 'desc';

    $typeFilters = [
        'Lead'             => 'Lead',
        'Lieferant'        => 'Lieferant',
        'Hersteller'       => 'Hersteller',
        'Geschäftspartner' => 'Geschäftspartner',
        'Architekt'        => 'Architekt',
        'Nachunternehmer'  => 'Nachunternehmer',
        'Bank'             => 'Bank',
        'Versicherung'     => 'Versicherung',
        'Bewerber'         => 'Bewerber',
        'Kunde'            => 'Kunde',
        'others'           => 'Sonstiges',
    ];

    $servicesMap = [
        'complete'    => 'Komplettlösung',
        'montage'     => 'Montage',
        'product'     => 'Produkt',
        'plan'        => 'Planung',
        'maintenance' => 'Wartung',
        'repair'      => 'Reparatur',
        'reclaim'     => 'Reklamation',
        'emergency'   => 'Notdienst',
        'others'      => 'Sonstiges',
    ];
@endphp

@once
@push('style')
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
    --purple:#cfe09b;
    --purple-light:#eef8cf;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
}

.oc-wrap {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main);
    max-width: 1600px;
    margin: 20px auto;
    padding: 56px 48px;
    padding-right: 86px;
}

.oc-header{margin:103px 0 18px;}
.oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
}
.oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
.oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}

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
.oc-breadcrumb a:hover{color:var(--text-main)}
.oc-breadcrumb span.current{color:#111827;font-weight:800}

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
.oc-btn-ic.primary:hover{border-color:var(--primary)}
.oc-btn-ic.warning{
    color:#d97706;
    border-color:#fde7b0;
    background:#fffbeb;
}
.oc-btn-ic.warning:hover{border-color:#f59e0b}
.oc-btn-ic.success{
    color:var(--success);
    border-color:#c7f2df;
    background:var(--success-light);
}
.oc-btn-ic.success:hover{border-color:var(--success)}
.oc-btn-ic.danger{
    color:var(--danger);
    border-color:rgba(239,68,68,.18);
    background:var(--danger-light);
}
.oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}
.oc-btn-ic.purple{
    color:#84a52c;
    border-color:#d9ebb0;
    background:#f4fae7;
}
.oc-btn-ic.purple:hover{border-color:#93c21c}

.oc-analytics{
    display:grid;
    grid-template-columns:repeat(5,minmax(0,1fr));
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
.oc-stat-icon.published{background:var(--success-light);color:var(--success)}
.oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
.oc-stat-icon.type{background:var(--gray-light);color:var(--gray)}
.oc-stat-icon.junk{background:var(--danger-light);color:var(--danger)}

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
.oc-toolbar-left{flex:1}

.oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
}
.oc-filter-block.search{
    flex:1 1 320px;
    min-width:320px;
}
.oc-filter-block.wide{
    min-width:230px;
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

.oc-bulkbar{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px 16px;
    margin-bottom:16px;
    box-shadow:var(--shadow-sm);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}

.oc-chip{
    display:inline-flex;
    align-items:center;
    border-radius:999px;
    background:#eef2ff;
    color:var(--blue);
    padding:6px 12px;
    font-weight:800;
    font-size:12px;
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
    grid-template-columns:52px 88px minmax(250px,1.4fr) minmax(160px,.9fr) minmax(220px,1fr) minmax(140px,.8fr) 230px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
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
    grid-template-columns:52px 88px minmax(250px,1.4fr) minmax(160px,.9fr) minmax(220px,1fr) minmax(140px,.8fr) 230px;
}

.oc-cell{min-width:0}
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
    min-width:54px;
    height:36px;
    padding:0 12px;
    border-radius:10px;
    background:var(--blue-light);
    color:var(--blue);
    font-size:13px;
    font-weight:900;
}

.oc-checkbox{margin-top:10px}

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
.oc-subt,
.oc-subt-wrap{
    font-size:13px;
    color:var(--text-muted);
    line-height:1.45;
}
.oc-subt-wrap{white-space:normal}
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
.oc-badge.purple{background:#eef8cf;color:#6f8f1c}

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
.oc-row-dropdown.open{display:block}

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
.oc-row-dropdown-item i{
    width:16px;
    text-align:center;
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
    max-width:760px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
}
.oc-modal.oc-modal-md{max-width:620px}
.oc-modal.oc-modal-lg{max-width:980px}
.oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}

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

.oc-flow-list{
    display:flex;
    flex-direction:column;
    gap:8px;
    margin-top:2px;
}

.oc-flow-card{
    display:flex;
    flex-direction:column;
    gap:4px;
    padding:6px 0;
    border-bottom:1px dashed #e5e7eb;
}
.oc-flow-card:last-child{
    border-bottom:none;
    padding-bottom:0;
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
    width:10px;
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
    border:3px solid #93c21c;
    box-shadow:0 1px 4px rgba(15,23,42,.08);
}
.oc-flow-avatar.outside{border-color:#f0a356}

.oc-flow-meta{
    margin-top:2px;
    padding-left:1px;
}
.oc-flow-service{
    font-size:11px;
    line-height:1.15;
    font-weight:800;
    color:#5b5b5b;
    margin-top:2px;
}
.oc-flow-department{
    font-size:10px;
    line-height:1.15;
    font-weight:700;
    color:#aeb8c6;
    margin-top:2px;
}

.select2-container--default .select2-results__option img,
.select2-selection__rendered img{
    width:26px;
    height:26px;
    border-radius:50%;
    margin-right:8px;
    vertical-align:middle;
}

@media (max-width:1280px){
    .oc-list-head{display:none}
    .oc-item-row{grid-template-columns:1fr}
    .oc-cell-title{display:block}
}

@media (max-width:1200px){
    .oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr))}
}

@media (max-width:980px){
    .oc-toolbar-left,
    .oc-toolbar-right{
        width:100%;
    }

    .oc-filter-block,
    .oc-filter-block.search,
    .oc-filter-block.wide{
        min-width:100%;
        flex:1 1 100%;
    }
}

@media (max-width:700px){
    .oc-analytics{grid-template-columns:1fr}
}

@media (max-width:640px){
    .oc-flow-badge,
    .oc-flow-avatar{
        width:36px;
        height:36px;
        min-width:36px;
    }

    .oc-flow-badge{font-size:13px}
    .oc-flow-line{
        width:8px;
        height:3px;
    }
    .oc-flow-service{font-size:10px}
    .oc-flow-department{font-size:9px}
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
                <div class="oc-sub">{{ $pageSub }}</div>

                <div class="oc-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Home</a>
                    <span>›</span>
                    <span class="current">{{ $pageTitle }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="oc-analytics">
        <div class="oc-stat">
            <div class="oc-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Gesamt</div>
                <div class="oc-stat-value">{{ $totalCount }}</div>
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
                <div class="oc-stat-label">Veröffentlicht</div>
                <div class="oc-stat-value">{{ $publishedCount }}</div>
                <div class="oc-stat-sub">Aktive Datensätze</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon unpublished">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Unveröffentlicht</div>
                <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                <div class="oc-stat-sub">Noch nicht aktiv</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon type">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M7 12h10M10 17h4"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Mit Typ</div>
                <div class="oc-stat-value">{{ $typedCount }}</div>
                <div class="oc-stat-sub">Kategorisierte Einträge</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon junk">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Junk</div>
                <div class="oc-stat-value">{{ $junkCount }}</div>
                <div class="oc-stat-sub">Als Junk markiert</div>
            </div>
        </div>
    </div>

    <form action="{{ route('inquiry.published.list') }}" method="GET" class="oc-toolbar">
        <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
                <label class="oc-filter-label">Suche</label>
                <input
                    type="text"
                    class="oc-input search"
                    placeholder="Suche nach Name, Firma, Adresse, Produkt, Typ, Status"
                    name="search"
                    value="{{ request('search') }}"
                >
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Typ</label>
                <select class="oc-select" name="type">
                    <option value="">Alle Typen</option>
                    @foreach($typeFilters as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Abteilung</label>
                <select class="oc-select" name="department">
                    <option value="">Alle Abteilungen</option>
                    @foreach(($departments ?? collect()) as $department)
                        <option value="{{ $department->id }}" {{ (string)request('department') === (string)$department->id ? 'selected' : '' }}>
                            {{ $department->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Stadt</label>
                <input type="text" class="oc-input" name="city" value="{{ request('city') }}" placeholder="Stadt">
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Von</label>
                <input type="date" class="oc-input" name="from" value="{{ request('from') }}">
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Bis</label>
                <input type="date" class="oc-input" name="to" value="{{ request('to') }}">
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Sortierung</label>
                <select class="oc-select" name="sort">
                    <option value="id" {{ $currentSort === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="name" {{ $currentSort === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ $currentSort === 'email' ? 'selected' : '' }}>E-Mail</option>
                    <option value="status" {{ $currentSort === 'status' ? 'selected' : '' }}>Status</option>
                    <option value="created_at" {{ $currentSort === 'created_at' ? 'selected' : '' }}>Datum</option>
                    <option value="periority" {{ $currentSort === 'periority' ? 'selected' : '' }}>Priorität</option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Richtung</label>
                <select class="oc-select" name="direction">
                    <option value="desc" {{ $currentDirection === 'desc' ? 'selected' : '' }}>DESC</option>
                    <option value="asc" {{ $currentDirection === 'asc' ? 'selected' : '' }}>ASC</option>
                </select>
            </div>
        </div>

        <div class="oc-toolbar-right">
            <button class="oc-btn-soft" type="submit">
                <i class="fa fa-search mr-50"></i> Filtern
            </button>

            @if(
                request('search') ||
                request('type') ||
                request('department') ||
                request('city') ||
                request('from') ||
                request('to') ||
                request('sort') ||
                request('direction')
            )
                <a href="{{ route('inquiry.published.list') }}" class="oc-btn-soft">
                    <i class="fa fa-refresh mr-50"></i> Zurücksetzen
                </a>
            @endif
        </div>
    </form>

    <div class="oc-bulkbar">
        <div class="d-flex align-items-center flex-wrap" style="gap:12px;">
            <label class="mb-0 d-flex align-items-center" style="gap:8px;">
                <input type="checkbox" id="checkAllInquiries">
                <span>Alle auswählen</span>
            </label>

            <span class="oc-chip">
                Ausgewählt: <span id="selectedCount" style="margin-left:6px;">0</span>
            </span>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <button type="button" class="oc-btn-soft" id="btnBulkReverify">
                <i class="fa fa-refresh mr-50"></i> Neu verifizieren
            </button>
            <button type="button" class="oc-btn-soft" id="btnBulkDelete">
                <i class="feather icon-trash-2 mr-50"></i> Löschen
            </button>
        </div>
    </div>

    <div class="oc-card">
        <div class="oc-list-head">
            <div></div>
            <div>ID</div>
            <div>Name / Adresse / Kontakt</div>
            <div>Typ / Status / Priorität</div>
            <div>Produkte / Zuständig</div>
            <div>Verfasser</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="oc-list">
            @forelse($data as $item)
                @php
                    $safeFullName = $fsName($item->name ?? null, $item->lastname ?? null);
                    $safeCity = $fsCity($item->city ?? null);
                    $typeLabel = $fsType($item);

                    $createdAt = $item->created_at ?? now();

                    $empTitle = $fsName($item->emp_name ?? null, $item->emp_lastname ?? null);
                    $empAvatar = $fsImg(!empty($item->emp_image) ? ('images/employee/'.$item->emp_image) : null, 'images/gender/male.png');

                    $statusRaw = strtolower(trim((string)($item->status ?? '')));
                    $statusClass = match($statusRaw) {
                        'published'   => 'green',
                        'junk'        => 'red',
                        'unpublished' => 'orange',
                        default       => 'gray',
                    };

                    $statusLabel = match($statusRaw) {
                        'published'   => 'Veröffentlicht',
                        'junk'        => 'Junk',
                        'unpublished' => 'Nicht verifiziert',
                        'draft'       => 'Entwurf',
                        default       => $fs($item->status ?? null, 'Unbekannt'),
                    };

                    $firstLetter = mb_strtoupper(mb_substr($safeFullName, 0, 1));
                    $customerProducts = $product_list->where('inquiry_id', $item->id);

                    $canUpdate = true;
                    $canDelete = true;

                    $targetType = $item->pre_type ?? '';
                @endphp

                <div class="oc-item">
                    <div class="oc-item-row"
                         data-inquiry-id="{{ $item->id }}"
                         data-inquiry-name="{{ $safeFullName }}"
                         data-inquiry-type="{{ $typeLabel }}"
                         data-inquiry-city="{{ $safeCity }}"
                         data-status="{{ $fs($item->status ?? null, 'Unbekannt') }}">

                        <div class="oc-cell">
                            <div class="oc-cell-title">Auswahl</div>
                            <div class="oc-checkbox">
                                <input type="checkbox"
                                       class="inquiry-checkbox"
                                       name="selected_inquiries[]"
                                       value="{{ $item->id }}">
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">ID</div>
                            <div class="d-flex flex-column" style="gap:10px;">
                                <span class="oc-id-badge">#{{ $item->id }}</span>
                                <div class="oc-avatar">{{ $firstLetter }}</div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Name / Adresse / Kontakt</div>
                            <div class="oc-main">
                                <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:10px;">
                                    <div>
                                        <div class="oc-ttl">
                                            <a href="{{ url('inquiry_show/'.$item->id) }}" style="color:inherit;text-decoration:none;">
                                                {{ $safeFullName }}
                                            </a>
                                        </div>
                                        <div class="oc-subt-wrap">
                                            {{ $fs($item->street ?? null, 'Unbekannte Straße') }},
                                            {{ $fs($item->postcode ?? null, '—') }}
                                            {{ $safeCity }}
                                        </div>
                                        <div class="oc-subt-wrap">
                                            {{ $fs($item->email ?? null, 'Keine E-Mail') }}
                                            @if(!empty($item->phone))
                                                • {{ $item->phone }}
                                            @elseif(!empty($item->telephone))
                                                • {{ $item->telephone }}
                                            @endif
                                        </div>
                                        @if(!empty($item->firma))
                                            <div class="oc-note">
                                                <i class="feather icon-briefcase"></i>
                                                <strong>Firma:</strong> {{ $item->firma }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex" style="gap:8px;">
                                        <button type="button" class="oc-btn-ic primary" onclick="openModal('contactModal{{ $item->id }}')" title="Kontakt">
                                            <i class="feather icon-info"></i>
                                        </button>

                                        @if(!empty($item->note))
                                            <button type="button" class="oc-btn-ic purple" onclick="openModal('noteModal{{ $item->id }}')" title="Notiz">
                                                <i class="fa fa-sticky-note-o"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Typ / Status / Priorität</div>
                            <div class="oc-main">
                                <div class="oc-badges">
                                    <span class="oc-badge blue">{{ $typeLabel }}</span>
                                    <span class="oc-badge {{ $statusClass }}">{{ $statusLabel }}</span>

                                    @if(!empty($item->periority))
                                        <span class="oc-badge purple">{{ $item->periority }}</span>
                                    @endif
                                </div>

                                <div class="oc-subt-wrap mt-2">
                                    <strong>Erstellt:</strong> {{ \Carbon\Carbon::parse($createdAt)->isoFormat('DD.MM.YYYY') }}<br>
                                    <strong>Zeit:</strong> {{ \Carbon\Carbon::parse($createdAt)->isoFormat('HH:mm') }}
                                </div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Produkte / Zuständig</div>
                            <div class="oc-main">
                                @if($customerProducts->isNotEmpty())
                                    <div class="oc-flow-list">
                                        @foreach ($customerProducts->unique(fn($p) => $p->id) as $product)
                                            @php
                                                $male = asset('images/gender/male.png');
                                                $female = asset('images/gender/female.png');

                                                $serviceKey = strtolower($product->phase_section ?? '');
                                                $service = $fs($servicesMap[$serviceKey] ?? ($product->phase_section ?? null), 'Unbekannte Dienstleistung');
                                                $department = $fs($product->department_name ?? null, 'Unbekannte Abteilung');

                                                $insideExists = !empty($product->eimage) && file_exists(public_path('images/employee/'.$product->eimage));
                                                $insideImg = $insideExists
                                                    ? asset('images/employee/'.$product->eimage)
                                                    : (strtolower($product->egender ?? '') === 'female' ? $female : $male);

                                                $fieldExists = !empty($product->fimage) && file_exists(public_path('images/employee/'.$product->fimage));
                                                $fieldImg = $fieldExists
                                                    ? asset('images/employee/'.$product->fimage)
                                                    : (strtolower($product->fgender ?? '') === 'female' ? $female : $male);

                                                $productInitial = trim((string)($product->initial ?? ''));
                                                if ($productInitial === '') {
                                                    $articleGroup = trim((string)($product->article_group ?? 'PR'));
                                                    $parts = preg_split('/[\s\-\/]+/', $articleGroup, -1, PREG_SPLIT_NO_EMPTY);
                                                    if (count($parts) >= 2) {
                                                        $productInitial = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
                                                    } else {
                                                        $productInitial = mb_strtoupper(mb_substr($articleGroup, 0, 2));
                                                    }
                                                }
                                                $productInitial = $productInitial !== '' ? $productInitial : 'PR';
                                            @endphp

                                            <div class="oc-flow-card">
                                                <div class="oc-flow-top">
                                                    <div class="oc-flow-badge" title="{{ $fs($product->article_group ?? null, 'Unbekanntes Produkt') }}">
                                                        {{ $productInitial }}
                                                    </div>

                                                    <div class="oc-flow-line"></div>

                                                    <img src="{{ $insideImg }}"
                                                         alt="Innendienst"
                                                         class="oc-flow-avatar"
                                                         title="{{ $fsName($product->ename ?? null, $product->elastname ?? null) }}">

                                                    <div class="oc-flow-line"></div>

                                                    <img src="{{ $fieldImg }}"
                                                         alt="Außendienst"
                                                         class="oc-flow-avatar outside"
                                                         title="{{ $fsName($product->fname ?? null, $product->flastname ?? null) }}">
                                                </div>

                                                <div class="oc-flow-meta">
                                                    <div class="oc-flow-service">{{ $service }}</div>
                                                    <div class="oc-flow-department">{{ $department }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="oc-mini">Keine Produkte vorhanden</div>
                                @endif
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Verfasser</div>
                            <div class="oc-main">
                                <div class="d-flex align-items-center" style="gap:10px;">
                                    <img src="{{ $empAvatar }}"
                                         alt="avatar"
                                         style="width:42px;height:42px;border-radius:999px;object-fit:cover;border:1px solid #e5e7eb;">
                                    <div>
                                        <div class="oc-ttl" style="font-size:14px;">{{ $empTitle }}</div>
                                        <div class="oc-subt">{{ $fs($item->branch ?? null, 'Keine Niederlassung') }}</div>
                                        @if(!empty($item->direct_name) || !empty($item->direct_lastname))
                                            <div class="oc-subt">Zuständig: {{ trim(($item->direct_name ?? '').' '.($item->direct_lastname ?? '')) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="oc-cell">
                            <div class="oc-cell-title">Aktionen</div>
                            <div class="oc-actions">
                                <div class="oc-row-menu" data-menu>
                                    <button type="button" class="oc-btn-ic" data-menu-toggle title="Aktionen">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>

                                    <div class="oc-row-dropdown" data-menu-panel>
                                        <a href="{{ url('inquiry_show/'.$item->id) }}" class="oc-row-dropdown-item">
                                            <i class="feather icon-eye"></i>
                                            <span>Profil ansehen</span>
                                        </a>

                                        @if($canUpdate)
                                            <button type="button"
                                                    class="oc-row-dropdown-item reverify-item"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $safeFullName }}"
                                                    data-current="{{ $targetType }}">
                                                <i class="feather icon-refresh-ccw"></i>
                                                <span>Neu verifizieren</span>
                                            </button>
                                        @endif

                                        @if($canDelete)
                                            <button type="button"
                                                    class="oc-row-dropdown-item text-danger delete-item"
                                                    data-id="{{ $item->id }}">
                                                <i class="feather icon-trash-2"></i>
                                                <span>Löschen</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oc-modal-backdrop" id="contactModal{{ $item->id }}">
                    <div class="oc-modal oc-modal-md">
                        <div class="oc-modal-h">
                            <h3 class="oc-modal-ttl">{{ $safeFullName }}</h3>
                            <button class="oc-btn-ic" type="button" onclick="closeModal('contactModal{{ $item->id }}')">×</button>
                        </div>
                        <div class="oc-modal-b">
                            <p><strong>Adresse:</strong><br>{{ $fs($item->street ?? null, 'Unbekannte Straße') }}, {{ $fs($item->postcode ?? null, '—') }} {{ $safeCity }}</p>
                            <p><strong>Telefon:</strong> {{ $fs($item->telephone ?? null, 'Keine Telefonnummer') }}</p>
                            <p><strong>Mobil:</strong> {{ $fs($item->phone ?? null, 'Keine Mobilnummer') }}</p>
                            <p><strong>E-Mail:</strong> {{ $fs($item->email ?? null, 'Keine E-Mail') }}</p>
                            <p><strong>Firma:</strong> {{ $fs($item->firma ?? null, 'Keine Firma') }}</p>
                        </div>
                        <div class="oc-modal-f">
                            <button type="button" class="oc-btn-soft" onclick="closeModal('contactModal{{ $item->id }}')">Schließen</button>
                        </div>
                    </div>
                </div>

                @if(!empty($item->note))
                    <div class="oc-modal-backdrop" id="noteModal{{ $item->id }}">
                        <div class="oc-modal oc-modal-md">
                            <div class="oc-modal-h">
                                <h3 class="oc-modal-ttl">Notiz – {{ $safeFullName }}</h3>
                                <button class="oc-btn-ic" type="button" onclick="closeModal('noteModal{{ $item->id }}')">×</button>
                            </div>
                            <div class="oc-modal-b">
                                <p>{{ $item->note }}</p>
                            </div>
                            <div class="oc-modal-f">
                                <button type="button" class="oc-btn-soft" onclick="closeModal('noteModal{{ $item->id }}')">Schließen</button>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="oc-empty">Keine veröffentlichten Datensätze gefunden.</div>
            @endforelse
        </div>
    </div>

    @if($data instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="oc-pagination">
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@once
@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

document.addEventListener('click', function(e){
    if (e.target.classList.contains('oc-modal-backdrop')) {
        e.target.classList.remove('open');
    }
});

document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
});

$(document).ready(function () {
    $('.js-select2-filter').select2({
        width: '100%'
    });
});
</script>

<script>
document.addEventListener('click', function(e){
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
document.addEventListener('DOMContentLoaded', function () {
    const headMaster = document.getElementById('checkAllInquiries');
    const selectedCount = document.getElementById('selectedCount');

    function rowCheckboxes() {
        return Array.from(document.querySelectorAll('input.inquiry-checkbox[name="selected_inquiries[]"]'));
    }

    function syncBulkUI() {
        const rows = rowCheckboxes();
        const selected = rows.filter(cb => cb.checked).length;
        const allChecked = rows.length > 0 && selected === rows.length;

        if (selectedCount) selectedCount.textContent = selected;
        if (headMaster) headMaster.checked = allChecked;
    }

    if (headMaster) {
        headMaster.addEventListener('change', function () {
            rowCheckboxes().forEach(cb => cb.checked = this.checked);
            syncBulkUI();
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target.matches && e.target.matches('input.inquiry-checkbox[name="selected_inquiries[]"]')) {
            syncBulkUI();
        }
    });

    syncBulkUI();
});
</script>

<script>
$(document).on('click', '.reverify-item', function () {
    const id = $(this).data('id');
    const name = $(this).data('name') || '';
    const current = $(this).data('current') || '';

    const options = [
        "Lead","Lieferant","Hersteller","Geschäftspartner",
        "Architekt","Nachunternehmer","Bank","Versicherung",
        "Bewerber","others"
    ];

    let html = `<select id="revType" class="swal2-select" style="width:100%">`;
    options.forEach(o => html += `<option value="${o}" ${o === current ? 'selected' : ''}>${o}</option>`);
    html += `</select>`;

    Swal.fire({
        title: "Neu verifizieren",
        html: `<p><strong>${name}</strong></p>${html}`,
        width: 500,
        showCancelButton: true,
        confirmButtonText: "Speichern",
        cancelButtonText: "Abbrechen"
    }).then(res => {
        if (!res.isConfirmed) return;

        $.post(`/inquiry/${id}/reverify`, {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            type: document.getElementById('revType').value
        }).done(() => location.reload());
    });
});

$(document).on('click', '.delete-item', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Löschen?',
        text: 'Diese Anfrage löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja',
        cancelButtonText: 'Nein'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.post(`/inquiry/${id}/delete`, {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }).done(() => location.reload());
    });
});
</script>

<script>
document.getElementById('btnBulkReverify')?.addEventListener('click', function () {
    const checked = Array.from(document.querySelectorAll('.inquiry-checkbox:checked')).map(el => el.value);

    if (!checked.length) {
        Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
        return;
    }

    const options = [
        "Lead","Lieferant","Hersteller","Geschäftspartner",
        "Architekt","Nachunternehmer","Bank","Versicherung",
        "Bewerber","others"
    ];

    let html = `<select id="bulkRevType" class="swal2-select" style="width:100%">`;
    options.forEach(o => html += `<option value="${o}">${o}</option>`);
    html += `</select>`;

    Swal.fire({
        title: 'Bulk Neu-Verifizierung',
        html: `<p>${checked.length} Anfrage(n) auswählen</p>${html}`,
        showCancelButton: true,
        confirmButtonText: 'Speichern',
        cancelButtonText: 'Abbrechen'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.post(`{{ route('inquiries.bulk.verify') }}`, {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            ids: checked,
            type: document.getElementById('bulkRevType').value
        }).done(() => location.reload());
    });
});

document.getElementById('btnBulkDelete')?.addEventListener('click', function () {
    const checked = Array.from(document.querySelectorAll('.inquiry-checkbox:checked')).map(el => el.value);

    if (!checked.length) {
        Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
        return;
    }

    Swal.fire({
        title: 'Ausgewählte Anfragen löschen?',
        html: `<p><strong>${checked.length}</strong> Anfrage(n) werden gelöscht.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then(result => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: '{{ route('inquiries.bulk.delete') }}',
            method: 'POST',
            data: {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ids: checked
            },
            success: function () {
                Swal.fire('Gelöscht', 'Anfragen wurden gelöscht.', 'success').then(() => location.reload());
            },
            error: function () {
                Swal.fire('Fehler', 'Löschen fehlgeschlagen.', 'error');
            }
        });
    });
});
</script>
@endpush
@endonce