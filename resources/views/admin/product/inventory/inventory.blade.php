@extends('admin.layouts.app')
@section('title', 'Inventory')

@php
    $productsJson = collect($products ?? [])->map(function ($p) {
        return [
            'id' => $p->id,
            'product' => $p->product,
            'model' => $p->model,
            'article_no' => $p->article_no,
            'ean' => $p->ean,
            'brand_name' => $p->brand?->name ?? $p->brand_name ?? null,
            'image_url' => !empty($p->firstImage?->image)
                ? asset('images/products/' . ltrim($p->firstImage->image, '/'))
                : ($p->image_url ?? null),
        ];
    })->values();

    $responsibleJson = collect($responsible ?? [])->map(function ($r) {
        return [
            'id' => $r->id,
            'name' => trim(($r->name ?? '') . ' ' . ($r->lastname ?? '')),
        ];
    })->values();

    $branchesJson = collect($branches ?? [])->map(function ($b) {
        return [
            'id' => $b->id,
            'branch' => $b->branch,
        ];
    })->values();

    $customersJson = collect($customers ?? [])->map(function ($c) {
        return [
            'id' => $c->id,
            'name' => trim(($c->firma ?? '') . ' ' . ($c->name ?? '') . ' ' . ($c->lastname ?? '')),
        ];
    })->values();

    $inventoryCategories = collect($inventoryCategories ?? [])->filter()->values();
    $quantityUnits = collect($quantityUnits ?? [
        'Stk',
        'Pcs',
        'm',
        'cm',
        'mm',
        'm²',
        'm³',
        'kg',
        'g',
        't',
        'l',
        'ml',
        'Pack',
        'Set',
        'Rolle',
        'Karton',
        'Palette',
    ])->filter()->values();
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

.iv-wrap{
    font-family:Inter,system-ui,-apple-system,sans-serif;
    color:var(--text-main);
    max-width:1600px;
    margin:20px auto;
    padding:56px 32px;
}

.iv-header{margin:95px 0 18px;}
.iv-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
}
.iv-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
.iv-sub{font-size:14px;color:var(--text-muted);margin-top:4px}

.iv-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
}
.iv-breadcrumb a{
    color:var(--text-muted);
    text-decoration:none;
    font-weight:700;
}
.iv-breadcrumb a:hover{color:var(--text-main)}
.iv-breadcrumb span.current{color:#111827;font-weight:800}

.iv-btn,
.iv-btn-soft,
.iv-btn-ic{
    transition:var(--transition);
    text-decoration:none;
}

.iv-btn{
    background:var(--primary);
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    white-space:nowrap;
}
.iv-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

.iv-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}
.iv-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

.iv-btn-ic{
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
    flex:0 0 auto;
}
.iv-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db;}
.iv-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light);}
.iv-btn-ic.primary{color:var(--blue);border-color:#dbeafe;background:var(--blue-light);}
.iv-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light);}
.iv-btn-ic.purple{color:var(--purple);border-color:#e9d5ff;background:var(--purple-light);}

.iv-tabs-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    padding:14px 16px;
    margin-bottom:16px;
}
.iv-tabs{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.iv-analytics{
    display:grid;
    grid-template-columns:repeat(5,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
}
.iv-stat{
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
.iv-stat-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
}
.iv-stat-icon.total{background:var(--blue-light);color:var(--blue)}
.iv-stat-icon.qty{background:var(--success-light);color:var(--success)}
.iv-stat-icon.products{background:var(--gray-light);color:var(--gray)}
.iv-stat-icon.low{background:var(--warning-light);color:#d97706}
.iv-stat-icon.resp{background:var(--danger-light);color:var(--danger)}
.iv-stat-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
}
.iv-stat-value{
    font-size:24px;
    font-weight:900;
    color:#111827;
    line-height:1.1;
    margin-top:4px;
}
.iv-stat-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
}

.iv-toolbar{
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
.iv-toolbar-left,
.iv-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
}
.iv-toolbar-left{flex:1}

.iv-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
}
.iv-filter-block.search{
    flex:1 1 380px;
    min-width:320px;
}
.iv-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
}

.iv-input,
.iv-select,
.iv-textarea{
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
.iv-textarea{min-height:110px;resize:vertical;}
.iv-input.search{
    padding-left:36px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
}
.iv-input:focus,
.iv-select:focus,
.iv-textarea:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
}

.iv-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
}

.iv-list-head{
    display:grid;
    grid-template-columns:84px 96px minmax(300px,1.45fr) minmax(170px,.8fr) minmax(300px,1.15fr) minmax(170px,.8fr) minmax(150px,190px);
    gap:14px;
    align-items:center;
    padding:16px 16px 10px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
}
.iv-history-head{
    display:grid;
    grid-template-columns:84px minmax(250px,1.3fr) minmax(170px,.9fr) minmax(160px,.9fr) minmax(220px,1fr) minmax(160px,.8fr);
    gap:14px;
    align-items:center;
    padding:16px 16px 10px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.iv-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px;
}
.iv-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    transition:var(--transition);
    overflow:hidden;
    margin:0 16px;
}
.iv-item:hover{border-color:var(--primary);box-shadow:var(--shadow);}
.iv-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:start;
    grid-template-columns:84px 96px minmax(300px,1.45fr) minmax(170px,.8fr) minmax(300px,1.15fr) minmax(170px,.8fr) minmax(150px,190px);
}
.iv-history-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:start;
    grid-template-columns:84px minmax(250px,1.3fr) minmax(170px,.9fr) minmax(160px,.9fr) minmax(220px,1fr) minmax(160px,.8fr);
}

.iv-cell{min-width:0;}
.iv-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
}
.iv-id-badge{
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
.iv-image{
    width:76px;
    height:76px;
    border-radius:16px;
    object-fit:cover;
    border:1px solid #e5e7eb;
    background:#fff;
    display:block;
}
.iv-image-empty{
    width:76px;
    height:76px;
    border-radius:16px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#9ca3af;
    font-size:11px;
    font-weight:800;
    text-align:center;
    padding:6px;
}

.iv-main{display:flex;flex-direction:column;min-width:0;}
.iv-ttl{
    font-weight:800;
    font-size:15px;
    margin-bottom:4px;
    color:#111827;
}
.iv-subt,
.iv-subt-wrap{
    font-size:13px;
    color:var(--text-muted);
    line-height:1.45;
}
.iv-subt-wrap{white-space:normal;word-break:break-word;}

.iv-note{
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:10px;
    padding:6px 8px;
    margin-top:8px;
    width:fit-content;
}

.iv-badges{
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}
.iv-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
    max-width:100%;
}
.iv-badge.green{background:#ecfdf5;color:#047857}
.iv-badge.orange{background:#fffbeb;color:#b45309}
.iv-badge.red{background:#fef2f2;color:#b91c1c}
.iv-badge.blue{background:#eff6ff;color:var(--blue)}
.iv-badge.gray{background:#f3f4f6;color:#4b5563}
.iv-badge.purple{background:#f5f3ff;color:var(--purple)}

.iv-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
    width:100%;
    max-width:100%;
    overflow:hidden;
}
.iv-actions .iv-btn-ic,
.iv-actions a.iv-btn-ic{
    flex:0 0 auto;
}

.iv-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    margin:16px;
}

.iv-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}
.iv-pagination-btn{
    min-width:38px;
    height:38px;
    border-radius:10px;
    border:1px solid var(--border);
    background:#fff;
    font-weight:800;
    cursor:pointer;
}
.iv-pagination-btn:disabled{opacity:.45;cursor:not-allowed;}

.iv-modal-backdrop{
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
.iv-modal-backdrop.open{opacity:1;pointer-events:auto;}
.iv-modal{
    width:100%;
    max-width:1080px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
}
.iv-modal.md{max-width:760px;}
.iv-modal-backdrop.open .iv-modal{transform:translateY(0) scale(1);}
.iv-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
}
.iv-modal-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
}
.iv-modal-b{
    padding:20px 18px;
    max-height:72vh;
    overflow-y:auto;
}
.iv-modal-f{
    padding:14px 18px;
    border-top:1px solid var(--border);
    background:#fafafa;
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
}

.iv-form-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
}
.iv-form-group{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:0;
}
.iv-form-group.full{grid-column:1/-1;}
.iv-label{
    font-size:12px;
    font-weight:800;
    color:var(--text-muted);
}

.iv-product-preview{
    display:grid;
    grid-template-columns:96px 1fr;
    gap:14px;
    align-items:center;
    padding:14px;
    border:1px solid var(--border);
    border-radius:14px;
    background:#fafafa;
    margin-bottom:14px;
}
.iv-product-preview-image{
    width:96px;
    height:96px;
    border-radius:18px;
    object-fit:cover;
    border:1px solid #e5e7eb;
    background:#fff;
}
.iv-product-preview-empty{
    width:96px;
    height:96px;
    border-radius:18px;
    border:1px solid #e5e7eb;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#9ca3af;
    font-size:11px;
    font-weight:800;
    text-align:center;
    padding:8px;
}
.iv-product-preview-title{
    font-size:15px;
    font-weight:900;
    color:#111827;
}
.iv-product-preview-sub{
    margin-top:6px;
    font-size:13px;
    color:#6b7280;
    line-height:1.55;
}
.iv-product-preview-badges{
    margin-top:10px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.iv-location-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.iv-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:10px;
    pointer-events:none;
}
.iv-toast{
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
}

.select2-container{width:100% !important;}
.select2-container .select2-selection--single,
.select2-container .select2-selection--multiple{
    min-height:42px;
    border:1px solid var(--border) !important;
    border-radius:8px !important;
    background:#f9fafb !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:40px !important;
    padding-left:12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow{
    height:40px !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple,
.select2-container--default.select2-container--open .select2-selection--single{
    border-color:var(--primary) !important;
}
.select2-dropdown{
    border:1px solid var(--border) !important;
    border-radius:10px !important;
    overflow:hidden;
}

@media (max-width:1280px){
    .iv-list-head,
    .iv-history-head{display:none}
    .iv-item-row,
    .iv-history-row{grid-template-columns:1fr}
    .iv-cell-title{display:block}
    .iv-actions{justify-content:flex-start;}
}
@media (max-width:1200px){
    .iv-analytics{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:980px){
    .iv-toolbar-left,.iv-toolbar-right{width:100%}
    .iv-filter-block,.iv-filter-block.search{min-width:100%;flex:1 1 100%}
}
@media (max-width:700px){
    .iv-analytics{grid-template-columns:1fr}
    .iv-form-grid{grid-template-columns:1fr}
    .iv-product-preview{grid-template-columns:1fr}
    .iv-wrap{padding:40px 16px}
}
</style>
@endpush
@endonce

@section('content')
<div
    class="iv-wrap"
    id="inventory-app"
    data-list-url="{{ route('inventory.list.ajax') }}"
    data-analytics-url="{{ route('inventory.analytics') }}"
    data-store-url="{{ route('inventory.store.ajax') }}"
    data-update-base-url="{{ url('/inventory/update-ajax') }}"
    data-delete-base-url="{{ url('/inventory/delete-ajax') }}"
    data-product-data-base-url="{{ url('/inventory/product-data') }}"
    data-history-url="{{ route('inventory.history.ajax') }}"
    data-use-base-url="{{ url('/inventory/use-product-ajax') }}"
    data-find-by-product-url="{{ url('/inventory/find-by-product') }}"
>
    <div class="iv-header">
        <div class="iv-titlebar">
            <div>
                <div class="iv-title">INVENTORY</div>
                <div class="iv-sub">Verwalten Sie Lagerorte, Kategorien, Räume, Regale, Reihen, Spalten, Maßeinheiten, Produktbilder, Hersteller, Quantitäten, Verantwortliche, Verbrauch und exakte Standortdaten zentral.</div>

                <div class="iv-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Home</a>
                    <span>›</span>
                    <span class="current">Inventory</span>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <button type="button" class="iv-btn" onclick="openCreateModal()">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Neuer Inventareintrag
                </button>
            </div>
        </div>
    </div>

    <div class="iv-tabs-card">
        <div class="iv-tabs">
            <button type="button" class="iv-btn" id="tab-inventory-btn" onclick="switchInventoryTab('inventory')">Inventory</button>
            <button type="button" class="iv-btn-soft" id="tab-history-btn" onclick="switchInventoryTab('history')">Verbrauch / Historie</button>
        </div>
    </div>

    <div id="tab-panel-inventory">
        <div class="iv-analytics">
            <div class="iv-stat">
                <div class="iv-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                </div>
                <div>
                    <div class="iv-stat-label">Einträge</div>
                    <div class="iv-stat-value" id="stat-total-entries">0</div>
                    <div class="iv-stat-sub">Inventarsätze gesamt</div>
                </div>
            </div>

            <div class="iv-stat">
                <div class="iv-stat-icon qty">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v18M7 8l5-5 5 5M7 16l5 5 5-5"/>
                    </svg>
                </div>
                <div>
                    <div class="iv-stat-label">Gesamtmenge</div>
                    <div class="iv-stat-value" id="stat-total-quantity">0</div>
                    <div class="iv-stat-sub">Alle Stückzahlen</div>
                </div>
            </div>

            <div class="iv-stat">
                <div class="iv-stat-icon products">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <path d="M3.3 7l8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </div>
                <div>
                    <div class="iv-stat-label">Produkte</div>
                    <div class="iv-stat-value" id="stat-unique-products">0</div>
                    <div class="iv-stat-sub">Unterschiedliche Produkte</div>
                </div>
            </div>

            <div class="iv-stat">
                <div class="iv-stat-icon low">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <path d="M12 9v4"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
                <div>
                    <div class="iv-stat-label">Niedrig</div>
                    <div class="iv-stat-value" id="stat-low-stock">0</div>
                    <div class="iv-stat-sub">Bestand ≤ 5</div>
                </div>
            </div>

            <div class="iv-stat">
                <div class="iv-stat-icon resp">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <div class="iv-stat-label">Ohne Verantwortlich</div>
                    <div class="iv-stat-value" id="stat-no-responsible">0</div>
                    <div class="iv-stat-sub">Noch nicht zugewiesen</div>
                </div>
            </div>
        </div>

        <div class="iv-toolbar">
            <div class="iv-toolbar-left">
                <div class="iv-filter-block search">
                    <label class="iv-filter-label">Suche</label>
                    <input
                        type="text"
                        class="iv-input search"
                        id="filter-search"
                        placeholder="Suche nach Produktname, Modell, Artikel-Nr., EAN, Seriennummer, Hersteller, Kategorie, Raum, Regal, Reihe, Spalte, Mitarbeiter"
                    >
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Produkt</label>
                    <select class="iv-select js-select2" id="filter-product">
                        <option value="">Alle Produkte</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->product }}{{ $p->model ? ' - '.$p->model : '' }}{{ $p->article_no ? ' - '.$p->article_no : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Niederlassung</label>
                    <select class="iv-select js-select2" id="filter-branch">
                        <option value="">Alle Niederlassungen</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->branch }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Verantwortlich</label>
                    <select class="iv-select js-select2" id="filter-responsible">
                        <option value="">Alle Verantwortlichen</option>
                        @foreach($responsible as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} {{ $r->lastname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Bestand</label>
                    <select class="iv-select" id="filter-stock">
                        <option value="">Alle</option>
                        <option value="available">Verfügbar</option>
                        <option value="low">Niedrig (≤ 5)</option>
                        <option value="zero">0 Bestand</option>
                    </select>
                </div>
            </div>

            <div class="iv-toolbar-right">
                <div class="iv-filter-block">
                    <label class="iv-filter-label">Sortierung</label>
                    <select class="iv-select" id="filter-sort">
                        <option value="id">ID</option>
                        <option value="product">Produkt</option>
                        <option value="model">Modell</option>
                        <option value="article_no">Artikel-Nr.</option>
                        <option value="serial_no">Seriennummer</option>
                        <option value="location">Lagerung</option>
                        <option value="quantity">Quantität</option>
                        <option value="responsible">Verantwortlich</option>
                        <option value="brand">Hersteller</option>
                        <option value="created_at">Erstellt</option>
                    </select>
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Richtung</label>
                    <select class="iv-select" id="filter-direction">
                        <option value="desc">Absteigend</option>
                        <option value="asc">Aufsteigend</option>
                    </select>
                </div>

                <div class="iv-filter-block">
                    <label class="iv-filter-label">Pro Seite</label>
                    <select class="iv-select" id="filter-per-page">
                        <option value="12">12</option>
                        <option value="20" selected>20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <button type="button" class="iv-btn-soft" onclick="resetInventoryFilters()">Zurücksetzen</button>
            </div>
        </div>

        <div class="iv-card">
            <div class="iv-list-head">
                <div>ID</div>
                <div>Bild</div>
                <div>Produkt / Details</div>
                <div>Bestand</div>
                <div>Lagerort / Struktur</div>
                <div>Verantwortlich</div>
                <div style="text-align:right;">Aktionen</div>
            </div>

            <div class="iv-list" id="inventory-list-wrap">
                <div class="iv-empty">Lade Inventardaten...</div>
            </div>
        </div>

        <div class="iv-pagination" id="inventory-pagination-wrap" style="display:none;">
            <div class="iv-sub" id="inventory-pagination-info">Zeige 0 bis 0 von 0 Einträgen</div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="iv-pagination-btn" id="btn-prev-page">‹</button>
                <span id="inventory-page-label" class="iv-sub" style="font-weight:800;">Seite 1</span>
                <button type="button" class="iv-pagination-btn" id="btn-next-page">›</button>
            </div>
        </div>
    </div>

    <div id="tab-panel-history" style="display:none;">
        <div class="iv-toolbar">
            <div class="iv-toolbar-left">
                <div class="iv-filter-block search">
                    <label class="iv-filter-label">Historie Suche</label>
                    <input
                        type="text"
                        class="iv-input search"
                        id="history-search"
                        placeholder="Suche nach Produkt, Artikel-Nr., EAN, Kunde, Ort, Notiz"
                    >
                </div>
            </div>
        </div>

        <div class="iv-card">
            <div class="iv-history-head">
                <div>ID</div>
                <div>Produkt / Kunde</div>
                <div>Mengen</div>
                <div>Ort</div>
                <div>Notiz</div>
                <div>Benutzer / Datum</div>
            </div>

            <div class="iv-list" id="inventory-history-wrap">
                <div class="iv-empty">Lade Historie...</div>
            </div>
        </div>

        <div class="iv-pagination" id="inventory-history-pagination-wrap" style="display:none;">
            <div class="iv-sub" id="inventory-history-pagination-info">Zeige 0 bis 0 von 0 Einträgen</div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="iv-pagination-btn" id="btn-history-prev-page">‹</button>
                <span id="inventory-history-page-label" class="iv-sub" style="font-weight:800;">Seite 1</span>
                <button type="button" class="iv-pagination-btn" id="btn-history-next-page">›</button>
            </div>
        </div>
    </div>
</div>

<div class="iv-modal-backdrop" id="inventory-modal">
    <div class="iv-modal">
        <div class="iv-modal-h">
            <h3 class="iv-modal-ttl" id="inventory-modal-title">Inventareintrag</h3>
            <button class="iv-btn-ic" type="button" onclick="closeInventoryModal()">×</button>
        </div>

        <div class="iv-modal-b">
            <form id="inventory-form">
                @csrf
                <input type="hidden" id="inventory-id">
                <input type="hidden" id="form-latitude" name="latitude">
                <input type="hidden" id="form-longitude" name="longitude">

                <div class="iv-product-preview">
                    <div id="product-preview-image-wrap">
                        <div class="iv-product-preview-empty">Kein Produktbild</div>
                    </div>

                    <div>
                        <div class="iv-product-preview-title" id="product-preview-title">Noch kein Produkt gewählt</div>
                        <div class="iv-product-preview-sub" id="product-preview-sub">Wählen Sie zuerst ein Produkt aus, damit Artikel-Nr., EAN und Hersteller automatisch geladen werden.</div>
                        <div class="iv-product-preview-badges" id="product-preview-badges">
                            <span class="iv-badge gray">Artikel-Nr.: -</span>
                            <span class="iv-badge gray">EAN: -</span>
                            <span class="iv-badge gray">Hersteller: -</span>
                        </div>
                    </div>
                </div>

                <div class="iv-form-grid">
                    <div class="iv-form-group full">
                        <label class="iv-label">Produkt</label>
                        <select class="iv-select js-select2-modal-inventory" id="form-product-id" name="product_id" required>
                            <option value="">Produkt wählen</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->product }}{{ $p->model ? ' - '.$p->model : '' }}{{ $p->article_no ? ' - '.$p->article_no : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Seriennummer</label>
                        <input type="text" class="iv-input" id="form-serial-no" name="serial_no">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Artikel-nummer</label>
                        <input type="text" class="iv-input" id="form-article-no" name="article_no">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">EAN-nummer</label>
                        <input type="text" class="iv-input" id="form-ean" name="ean">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Hersteller / Brand</label>
                        <input type="text" class="iv-input" id="form-brand-name" readonly>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Handbuch</label>
                        <input type="text" class="iv-input" id="form-manual-no" name="manual_no">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Lagerung / Niederlassung</label>
                        <select class="iv-select js-select2-modal-inventory" id="form-location" name="location" required>
                            <option value="">Niederlassung wählen</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->branch }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Kategorie</label>
                        <select class="iv-select js-select2-category" id="form-inventory-category" name="inventory_category">
                            <option value="">Kategorie wählen / eingeben</option>
                            @foreach($inventoryCategories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Raumname</label>
                        <input type="text" class="iv-input" id="form-room-name" name="room_name" placeholder="z. B. Lagerraum, Technikraum, PV Raum">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Raumnummer</label>
                        <input type="text" class="iv-input" id="form-room-number" name="room_number" placeholder="z. B. 2">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Regal / Board / Rack</label>
                        <input type="text" class="iv-input" id="form-rack-name" name="rack_name" placeholder="z. B. Regal 1 / Board A">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Fach / Shelf</label>
                        <input type="text" class="iv-input" id="form-shelf" name="shelf" placeholder="z. B. Fach 3">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Reihe / Row</label>
                        <input type="text" class="iv-input" id="form-row" name="row" required placeholder="z. B. 2">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Spalte / Column</label>
                        <input type="text" class="iv-input" id="form-column" name="column" placeholder="z. B. 2">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Quantität</label>
                        <input type="number" step="0.01" min="0" class="iv-input" id="form-quantity" name="quantity">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Mengeneinheit</label>
                        <select class="iv-select js-select2-modal-inventory" id="form-quantity-unit" name="quantity_unit">
                            <option value="">Einheit wählen</option>
                            @foreach($quantityUnits as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group full">
                        <label class="iv-label">Verantwortlich</label>
                        <select class="iv-select js-select2-modal-inventory" id="form-responsible-id" name="responsible_id">
                            <option value="">Verantwortlich wählen</option>
                            @foreach($responsible as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} {{ $r->lastname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group full">
                        <label class="iv-label">Exakter Standort</label>
                        <div class="iv-location-actions">
                            <button type="button" class="iv-btn-soft" onclick="captureCurrentLocation()">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v4"></path>
                                    <path d="M12 18v4"></path>
                                    <path d="M4.93 4.93l2.83 2.83"></path>
                                    <path d="M16.24 16.24l2.83 2.83"></path>
                                    <path d="M2 12h4"></path>
                                    <path d="M18 12h4"></path>
                                    <path d="M4.93 19.07l2.83-2.83"></path>
                                    <path d="M16.24 7.76l2.83-2.83"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Aktuellen Standort übernehmen
                            </button>

                            <button type="button" class="iv-btn-soft" onclick="openLocationInGoogle()" id="btn-open-google-location" disabled>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                In Google Maps öffnen
                            </button>
                        </div>
                        <div class="iv-subt-wrap" id="location-coordinates-info" style="margin-top:8px;">
                            Noch keine Koordinaten gespeichert.
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="iv-modal-f">
            <button type="button" class="iv-btn-soft" onclick="closeInventoryModal()">Abbrechen</button>
            <button type="button" class="iv-btn" onclick="submitInventoryForm()">Speichern</button>
        </div>
    </div>
</div>

<div class="iv-modal-backdrop" id="use-product-modal">
    <div class="iv-modal md">
        <div class="iv-modal-h">
            <h3 class="iv-modal-ttl">Produkt verwenden</h3>
            <button class="iv-btn-ic" type="button" onclick="closeUseProductModal()">×</button>
        </div>

        <div class="iv-modal-b">
            <form id="use-product-form">
                <input type="hidden" id="use-inventory-id">

                <div class="iv-form-grid">
                    <div class="iv-form-group full">
                        <label class="iv-label">Produkt</label>
                        <input type="text" class="iv-input" id="use-product-name" readonly>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Aktueller Bestand</label>
                        <input type="text" class="iv-input" id="use-current-qty" readonly>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Verwendete Menge</label>
                        <input type="number" step="0.01" min="0.01" class="iv-input" id="use-quantity-used">
                    </div>

                    <div class="iv-form-group full">
                        <label class="iv-label">Kunde</label>
                        <select class="iv-select js-select2-modal-use" id="use-customer-id">
                            <option value="">Kunde wählen</option>
                            @foreach(($customers ?? collect()) as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ trim(($customer->firma ?? '') . ' ' . ($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Wo verwendet</label>
                        <input type="text" class="iv-input" id="use-usage-location" placeholder="z. B. Baustelle Dortmund">
                    </div>

                    <div class="iv-form-group">
                        <label class="iv-label">Datum / Zeit</label>
                        <input type="datetime-local" class="iv-input" id="use-used-at">
                    </div>

                    <div class="iv-form-group full">
                        <label class="iv-label">Notiz</label>
                        <textarea class="iv-textarea" id="use-note" placeholder="Zusätzliche Notiz"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="iv-modal-f">
            <button type="button" class="iv-btn-soft" onclick="closeUseProductModal()">Abbrechen</button>
            <button type="button" class="iv-btn" onclick="submitUseProductForm()">Verbrauch speichern</button>
        </div>
    </div>
</div>

<div class="iv-modal-backdrop" id="duplicate-product-modal">
    <div class="iv-modal md">
        <div class="iv-modal-h">
            <h3 class="iv-modal-ttl">Inventareintrag bereits vorhanden</h3>
            <button class="iv-btn-ic" type="button" onclick="closeDuplicateProductModal()">×</button>
        </div>

        <div class="iv-modal-b">
            <div class="iv-product-preview">
                <div id="duplicate-product-image-wrap">
                    <div class="iv-product-preview-empty">Kein Bild</div>
                </div>

                <div>
                    <div class="iv-product-preview-title" id="duplicate-product-title">Produkt</div>
                    <div class="iv-product-preview-sub" id="duplicate-product-sub">
                        Für dieses Produkt existiert bereits ein Inventareintrag.
                    </div>

                    <div class="iv-product-preview-badges" id="duplicate-product-badges">
                        <span class="iv-badge blue">Art.-Nr.: -</span>
                        <span class="iv-badge gray">EAN: -</span>
                        <span class="iv-badge purple">Hersteller: -</span>
                    </div>

                    <div class="iv-subt-wrap" id="duplicate-product-location" style="margin-top:12px;">-</div>
                </div>
            </div>
        </div>

        <div class="iv-modal-f">
            <button type="button" class="iv-btn-soft" onclick="continueAddingDuplicateProduct()">Neu anlegen</button>
            <button type="button" class="iv-btn" onclick="editDuplicateProduct()">Zum Eintrag wechseln</button>
        </div>
    </div>
</div>

<div class="iv-toast-wrap" id="iv-toast-wrap"></div>
@endsection

@once
@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
const INVENTORY_PRODUCTS = @json($productsJson);
const INVENTORY_RESPONSIBLE = @json($responsibleJson);
const INVENTORY_BRANCHES = @json($branchesJson);
const INVENTORY_CUSTOMERS = @json($customersJson);
const INVENTORY_QUANTITY_UNITS = @json($quantityUnits->values());
</script>

<script>
(function () {
    const app = document.getElementById('inventory-app');
    if (!app) return;

    const state = {
        page: 1,
        per_page: 20,
        search: '',
        product_id: '',
        branch_id: '',
        responsible_id: '',
        stock_filter: '',
        sort: 'id',
        direction: 'desc',
        items: [],
        meta: null,
        editingId: null,
        selectedProductData: null,
        currentTab: 'inventory',
        historyPage: 1,
        historySearch: '',
        historyMeta: null,
        duplicateInventoryItem: null,
        suppressDuplicateCheck: false
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function esc(v) {
        return String(v ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function toast(title, message, type = 'ok') {
        const wrap = document.getElementById('iv-toast-wrap');
        if (!wrap) return;

        const el = document.createElement('div');
        el.className = 'iv-toast';
        el.innerHTML = `
            <div style="width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:${type === 'bad' ? '#fef2f2' : '#ecfdf5'};color:${type === 'bad' ? '#ef4444' : '#10b981'};">
                ${type === 'bad' ? '✕' : '✓'}
            </div>
            <div style="flex:1;">
                <div style="font-weight:900;font-size:13px;color:#111827;">${esc(title)}</div>
                <div style="font-size:12px;color:#374151;margin-top:4px;">${esc(message)}</div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="border:none;background:transparent;color:#6b7280;cursor:pointer;">×</button>
        `;
        wrap.appendChild(el);
        setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
    }

    async function fetchJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {})
            }
        });

        let json = {};
        try {
            json = await response.json();
        } catch (e) {
            throw new Error('Ungültige Server-Antwort.');
        }

        if (!response.ok) {
            throw new Error(json.message || 'Fehler beim Laden.');
        }

        return json;
    }

    function buildGoogleMapsUrl(lat, lng) {
        if (!lat || !lng) return null;
        return `https://www.google.com/maps?q=${encodeURIComponent(lat)},${encodeURIComponent(lng)}`;
    }

    function buildQuantityBadge(qty) {
        qty = Number(qty || 0);
        if (qty <= 0) return `<span class="iv-badge red">0 Bestand</span>`;
        if (qty <= 5) return `<span class="iv-badge orange">${qty}</span>`;
        return `<span class="iv-badge green">${qty}</span>`;
    }

    function buildLocationPath(item) {
        const bits = [];
        if (item.inventory_category) bits.push(`Kategorie: ${item.inventory_category}`);
        if (item.branch_name) bits.push(`Niederlassung: ${item.branch_name}`);
        else if (item.location_label) bits.push(`Ort: ${item.location_label}`);
        if (item.room_name || item.room_number) bits.push(`Raum: ${(item.room_name || '-')} ${(item.room_number ? '(' + item.room_number + ')' : '')}`.trim());
        if (item.rack_name) bits.push(`Regal: ${item.rack_name}`);
        if (item.shelf) bits.push(`Fach: ${item.shelf}`);
        if (item.row) bits.push(`Reihe: ${item.row}`);
        if (item.column) bits.push(`Spalte: ${item.column}`);
        return bits.join('<br>');
    }

    async function loadAnalytics() {
        const json = await fetchJson(app.dataset.analyticsUrl);
        document.getElementById('stat-total-entries').textContent = json.analytics.total_entries ?? 0;
        document.getElementById('stat-total-quantity').textContent = json.analytics.total_quantity ?? 0;
        document.getElementById('stat-unique-products').textContent = json.analytics.unique_products ?? 0;
        document.getElementById('stat-low-stock').textContent = json.analytics.low_stock_count ?? 0;
        document.getElementById('stat-no-responsible').textContent = json.analytics.no_responsible_count ?? 0;
    }

    function renderList() {
        const wrap = document.getElementById('inventory-list-wrap');
        const pager = document.getElementById('inventory-pagination-wrap');
        const pagerInfo = document.getElementById('inventory-pagination-info');
        const pageLabel = document.getElementById('inventory-page-label');
        const prevBtn = document.getElementById('btn-prev-page');
        const nextBtn = document.getElementById('btn-next-page');

        if (!state.items.length) {
            wrap.innerHTML = `<div class="iv-empty">Keine Inventardaten gefunden.</div>`;
            if (pager) pager.style.display = 'none';
            return;
        }

        wrap.innerHTML = state.items.map(item => {
            const googleUrl = buildGoogleMapsUrl(item.latitude, item.longitude);

            return `
                <div class="iv-item">
                    <div class="iv-item-row">
                        <div class="iv-cell">
                            <div class="iv-cell-title">ID</div>
                            <div class="iv-id-badge">#${esc(item.id)}</div>
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Bild</div>
                            ${
                                item.product_image_url
                                    ? `<img src="${esc(item.product_image_url)}" class="iv-image" alt="${esc(item.product_name || 'Produkt')}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                       <div class="iv-image-empty" style="display:none;">Kein Bild</div>`
                                    : `<div class="iv-image-empty">Kein Bild</div>`
                            }
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Produkt / Details</div>
                            <div class="iv-main">
                                <div class="iv-ttl">${esc(item.product_name || 'Unbekanntes Produkt')}</div>
                                <div class="iv-subt-wrap">
                                    ${esc(item.product_model || '-')}
                                    ${item.brand_name ? ` · ${esc(item.brand_name)}` : ''}
                                </div>

                                <div class="iv-badges" style="margin-top:8px;">
                                    <span class="iv-badge blue">Art.-Nr.: ${esc(item.article_no || item.product_article_no || '-')}</span>
                                    <span class="iv-badge gray">EAN: ${esc(item.ean || item.product_ean || '-')}</span>
                                    <span class="iv-badge purple">Hersteller: ${esc(item.brand_name || '-')}</span>
                                    ${item.inventory_category ? `<span class="iv-badge orange">${esc(item.inventory_category)}</span>` : ''}
                                </div>

                                <div class="iv-subt-wrap" style="margin-top:8px;">
                                    <strong>Seriennummer:</strong> ${esc(item.serial_no || '-')}<br>
                                    <strong>Handbuch:</strong> ${esc(item.manual_no || '-')}
                                </div>
                            </div>
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Bestand</div>
                            <div class="iv-main">
                                <div class="iv-badges">
                                    ${buildQuantityBadge(item.quantity)}
                                    ${item.quantity_unit ? `<span class="iv-badge gray">${esc(item.quantity_unit)}</span>` : ''}
                                </div>
                                <div class="iv-subt-wrap" style="margin-top:8px;">
                                    <strong>Quantität:</strong> ${esc(item.quantity)} ${esc(item.quantity_unit || '')}
                                </div>
                            </div>
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Lagerort / Struktur</div>
                            <div class="iv-main">
                                <div class="iv-subt-wrap">${buildLocationPath(item) || '-'}</div>
                                ${
                                    googleUrl
                                        ? `<div class="iv-note">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            Standort gespeichert
                                           </div>`
                                        : ''
                                }
                            </div>
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Verantwortlich</div>
                            <div class="iv-main">
                                <div class="iv-ttl" style="font-size:14px;">${esc(item.responsible_name || 'Nicht zugewiesen')}</div>
                                <div class="iv-subt-wrap">
                                    <strong>Erstellt:</strong> ${esc(item.created_at || '-')}<br>
                                    <strong>Aktualisiert:</strong> ${esc(item.updated_at || '-')}
                                </div>
                            </div>
                        </div>

                        <div class="iv-cell">
                            <div class="iv-cell-title">Aktionen</div>
                            <div class="iv-actions">
                                <button type="button" class="iv-btn-ic success" onclick="openUseProductModal(${item.id})" title="Produkt verwenden">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6L9 17l-5-5"></path>
                                    </svg>
                                </button>

                                ${
                                    googleUrl
                                        ? `<a href="${esc(googleUrl)}" target="_blank" class="iv-btn-ic purple" title="In Google Maps öffnen">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                           </a>`
                                        : ''
                                }

                                <button type="button" class="iv-btn-ic primary" onclick="editInventory(${item.id})" title="Bearbeiten">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </button>

                                <button type="button" class="iv-btn-ic danger" onclick="deleteInventory(${item.id})" title="Löschen">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4h8v2"></path>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        pager.style.display = 'flex';
        pagerInfo.textContent = `Zeige ${state.meta.from ?? 0} bis ${state.meta.to ?? 0} von ${state.meta.total ?? 0} Einträgen`;
        pageLabel.textContent = `Seite ${state.meta.current_page} von ${state.meta.last_page}`;
        prevBtn.disabled = state.meta.current_page <= 1;
        nextBtn.disabled = state.meta.current_page >= state.meta.last_page;
    }

    function renderHistory(items, meta) {
        state.historyMeta = meta;
        const wrap = document.getElementById('inventory-history-wrap');
        const pager = document.getElementById('inventory-history-pagination-wrap');
        const pagerInfo = document.getElementById('inventory-history-pagination-info');
        const pageLabel = document.getElementById('inventory-history-page-label');
        const prevBtn = document.getElementById('btn-history-prev-page');
        const nextBtn = document.getElementById('btn-history-next-page');

        if (!items.length) {
            wrap.innerHTML = `<div class="iv-empty">Keine Historie vorhanden.</div>`;
            if (pager) pager.style.display = 'none';
            return;
        }

        wrap.innerHTML = items.map(item => `
            <div class="iv-item">
                <div class="iv-history-row">
                    <div class="iv-cell">
                        <div class="iv-cell-title">ID</div>
                        <div class="iv-id-badge">#${esc(item.id)}</div>
                    </div>

                    <div class="iv-cell">
                        <div class="iv-cell-title">Produkt / Kunde</div>
                        <div class="iv-main">
                            <div class="iv-ttl">${esc(item.product_name || '-')}</div>
                            <div class="iv-subt-wrap">${esc(item.product_model || '-')}</div>
                            <div class="iv-badges" style="margin-top:8px;">
                                <span class="iv-badge blue">Art.-Nr.: ${esc(item.article_no || '-')}</span>
                                <span class="iv-badge gray">EAN: ${esc(item.ean || '-')}</span>
                            </div>
                            <div class="iv-subt-wrap" style="margin-top:8px;">
                                <strong>Kunde:</strong> ${esc(item.customer_name || '-')}
                            </div>
                        </div>
                    </div>

                    <div class="iv-cell">
                        <div class="iv-cell-title">Mengen</div>
                        <div class="iv-main">
                            <div class="iv-badges">
                                <span class="iv-badge gray">Vorher: ${esc(item.quantity_before)}</span>
                                <span class="iv-badge orange">Verwendet: ${esc(item.quantity_used)}</span>
                                <span class="iv-badge green">Nachher: ${esc(item.quantity_after)}</span>
                            </div>
                        </div>
                    </div>

                    <div class="iv-cell">
                        <div class="iv-cell-title">Ort</div>
                        <div class="iv-main">
                            <div class="iv-ttl" style="font-size:14px;">${esc(item.usage_location || '-')}</div>
                            <div class="iv-badges" style="margin-top:8px;">
                                <span class="iv-badge purple">${esc(item.type || 'used')}</span>
                            </div>
                        </div>
                    </div>

                    <div class="iv-cell">
                        <div class="iv-cell-title">Notiz</div>
                        <div class="iv-main">
                            <div class="iv-subt-wrap">${esc(item.note || '-')}</div>
                        </div>
                    </div>

                    <div class="iv-cell">
                        <div class="iv-cell-title">Benutzer / Datum</div>
                        <div class="iv-main">
                            <div class="iv-ttl" style="font-size:14px;">${esc(item.employee_name || '-')}</div>
                            <div class="iv-subt-wrap">${esc(item.used_at || '-')}</div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        pager.style.display = 'flex';
        pagerInfo.textContent = `Zeige ${meta.from ?? 0} bis ${meta.to ?? 0} von ${meta.total ?? 0} Einträgen`;
        pageLabel.textContent = `Seite ${meta.current_page} von ${meta.last_page}`;
        prevBtn.disabled = meta.current_page <= 1;
        nextBtn.disabled = meta.current_page >= meta.last_page;
    }

    async function loadList() {
        const params = new URLSearchParams({
            page: state.page,
            per_page: state.per_page,
            search: state.search,
            product_id: state.product_id,
            branch_id: state.branch_id,
            responsible_id: state.responsible_id,
            stock_filter: state.stock_filter,
            sort: state.sort,
            direction: state.direction,
        });

        const json = await fetchJson(`${app.dataset.listUrl}?${params.toString()}`);
        state.items = json.data || [];
        state.meta = json.meta || null;
        renderList();
    }

    async function loadHistory() {
        const params = new URLSearchParams({
            page: state.historyPage,
            per_page: 20,
            search: state.historySearch || ''
        });

        const json = await fetchJson(`${app.dataset.historyUrl}?${params.toString()}`);
        renderHistory(json.data || [], json.meta || null);
    }

    function openModal(id) {
        document.getElementById(id)?.classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.remove('open');
    }

    function updateLocationInfo() {
        const lat = document.getElementById('form-latitude').value;
        const lng = document.getElementById('form-longitude').value;
        const info = document.getElementById('location-coordinates-info');
        const btn = document.getElementById('btn-open-google-location');

        if (lat && lng) {
            info.textContent = `Gespeicherte Koordinaten: ${lat}, ${lng}`;
            btn.disabled = false;
        } else {
            info.textContent = 'Noch keine Koordinaten gespeichert.';
            btn.disabled = true;
        }
    }

    function updateProductPreview(product = null) {
        const imageWrap = document.getElementById('product-preview-image-wrap');
        const title = document.getElementById('product-preview-title');
        const sub = document.getElementById('product-preview-sub');
        const badges = document.getElementById('product-preview-badges');

        if (!product) {
            imageWrap.innerHTML = `<div class="iv-product-preview-empty">Kein Produktbild</div>`;
            title.textContent = 'Noch kein Produkt gewählt';
            sub.textContent = 'Wählen Sie zuerst ein Produkt aus, damit Artikel-Nr., EAN und Hersteller automatisch geladen werden.';
            badges.innerHTML = `
                <span class="iv-badge gray">Artikel-Nr.: -</span>
                <span class="iv-badge gray">EAN: -</span>
                <span class="iv-badge gray">Hersteller: -</span>
            `;
            return;
        }

        imageWrap.innerHTML = product.image_url
            ? `<img src="${esc(product.image_url)}" class="iv-product-preview-image" alt="${esc(product.product || 'Produkt')}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
               <div class="iv-product-preview-empty" style="display:none;">Kein Produktbild</div>`
            : `<div class="iv-product-preview-empty">Kein Produktbild</div>`;

        title.textContent = [product.product || '', product.model || ''].filter(Boolean).join(' - ') || 'Produkt';
        sub.textContent = 'Produktdaten wurden automatisch geladen.';
        badges.innerHTML = `
            <span class="iv-badge blue">Artikel-Nr.: ${esc(product.article_no || '-')}</span>
            <span class="iv-badge gray">EAN: ${esc(product.ean || '-')}</span>
            <span class="iv-badge purple">Hersteller: ${esc(product.brand_name || '-')}</span>
        `;
    }

    async function loadProductData(productId, forceFill = false) {
        if (!productId) {
            state.selectedProductData = null;
            updateProductPreview(null);
            if (forceFill) {
                document.getElementById('form-article-no').value = '';
                document.getElementById('form-ean').value = '';
                document.getElementById('form-brand-name').value = '';
            }
            return;
        }

        const json = await fetchJson(`${app.dataset.productDataBaseUrl}/${productId}`);
        if (!json.success || !json.product) return;

        state.selectedProductData = json.product;
        updateProductPreview(json.product);

        const articleField = document.getElementById('form-article-no');
        const eanField = document.getElementById('form-ean');
        const brandField = document.getElementById('form-brand-name');

        if (forceFill || !articleField.value) articleField.value = json.product.article_no || '';
        if (forceFill || !eanField.value) eanField.value = json.product.ean || '';
        brandField.value = json.product.brand_name || '';
    }

    function resetForm() {
        state.editingId = null;
        state.selectedProductData = null;
        state.suppressDuplicateCheck = false;
        state.duplicateInventoryItem = null;

        document.getElementById('inventory-modal-title').textContent = 'Neuer Inventareintrag';
        document.getElementById('inventory-id').value = '';
        document.getElementById('inventory-form').reset();
        document.getElementById('form-brand-name').value = '';
        document.getElementById('form-latitude').value = '';
        document.getElementById('form-longitude').value = '';

        if (window.jQuery) {
            $('#form-product-id').val('').trigger('change');
            $('#form-location').val('').trigger('change');
            $('#form-responsible-id').val('').trigger('change');
            $('#form-inventory-category').val('').trigger('change');
            $('#form-quantity-unit').val('').trigger('change');
        }

        updateProductPreview(null);
        updateLocationInfo();
    }

    function renderDuplicateProductModal(item) {
        document.getElementById('duplicate-product-image-wrap').innerHTML = item.product_image_url
            ? `<img src="${esc(item.product_image_url)}" class="iv-product-preview-image" alt="${esc(item.product_name || 'Produkt')}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
               <div class="iv-product-preview-empty" style="display:none;">Kein Bild</div>`
            : `<div class="iv-product-preview-empty">Kein Bild</div>`;

        document.getElementById('duplicate-product-title').textContent =
            [item.product_name || '', item.product_model || ''].filter(Boolean).join(' - ') || 'Produkt';

        document.getElementById('duplicate-product-sub').textContent =
            `Dieses Produkt wurde bereits als Inventar #${item.id} angelegt.`;

        document.getElementById('duplicate-product-badges').innerHTML = `
            <span class="iv-badge blue">Art.-Nr.: ${esc(item.article_no || '-')}</span>
            <span class="iv-badge gray">EAN: ${esc(item.ean || '-')}</span>
            <span class="iv-badge purple">Hersteller: ${esc(item.brand_name || '-')}</span>
            <span class="iv-badge green">Bestand: ${esc(item.quantity || 0)} ${esc(item.quantity_unit || '')}</span>
            ${item.inventory_category ? `<span class="iv-badge orange">${esc(item.inventory_category)}</span>` : ''}
        `;

        document.getElementById('duplicate-product-location').innerHTML = buildLocationPath(item) || '-';
    }

    async function checkDuplicateInventoryForProduct(productId) {
        if (!productId || state.editingId || state.suppressDuplicateCheck) return;

        try {
            const json = await fetchJson(`${app.dataset.findByProductUrl}/${productId}`);
            if (json.success && json.exists && json.inventory) {
                state.duplicateInventoryItem = json.inventory;
                renderDuplicateProductModal(json.inventory);
                openModal('duplicate-product-modal');
            }
        } catch (e) {}
    }

    window.switchInventoryTab = function (tab) {
        state.currentTab = tab;
        document.getElementById('tab-panel-inventory').style.display = tab === 'inventory' ? '' : 'none';
        document.getElementById('tab-panel-history').style.display = tab === 'history' ? '' : 'none';
        document.getElementById('tab-inventory-btn').className = tab === 'inventory' ? 'iv-btn' : 'iv-btn-soft';
        document.getElementById('tab-history-btn').className = tab === 'history' ? 'iv-btn' : 'iv-btn-soft';
        if (tab === 'history') loadHistory();
    };

    window.openCreateModal = function () {
        resetForm();
        openModal('inventory-modal');
    };

    window.closeInventoryModal = function () {
        closeModal('inventory-modal');
    };

    window.editInventory = async function (id) {
        const item = state.items.find(row => Number(row.id) === Number(id));
        if (!item) return;

        state.editingId = item.id;

        document.getElementById('inventory-modal-title').textContent = `Inventareintrag #${item.id} bearbeiten`;
        document.getElementById('inventory-id').value = item.id;
        document.getElementById('form-product-id').value = item.product_id || '';
        document.getElementById('form-serial-no').value = item.serial_no || '';
        document.getElementById('form-article-no').value = item.article_no || '';
        document.getElementById('form-ean').value = item.ean || '';
        document.getElementById('form-manual-no').value = item.manual_no || '';
        document.getElementById('form-location').value = item.location || '';
        document.getElementById('form-inventory-category').value = item.inventory_category || '';
        document.getElementById('form-room-name').value = item.room_name || '';
        document.getElementById('form-room-number').value = item.room_number || '';
        document.getElementById('form-rack-name').value = item.rack_name || '';
        document.getElementById('form-shelf').value = item.shelf || '';
        document.getElementById('form-row').value = item.row || '';
        document.getElementById('form-column').value = item.column || '';
        document.getElementById('form-quantity').value = item.quantity || '';
        document.getElementById('form-quantity-unit').value = item.quantity_unit || '';
        document.getElementById('form-responsible-id').value = item.responsible_id || '';
        document.getElementById('form-latitude').value = item.latitude || '';
        document.getElementById('form-longitude').value = item.longitude || '';
        document.getElementById('form-brand-name').value = item.brand_name || '';

        if (window.jQuery) {
            $('#form-product-id').trigger('change');
            $('#form-location').trigger('change');
            $('#form-responsible-id').trigger('change');
            $('#form-inventory-category').val(item.inventory_category || '').trigger('change');
            $('#form-quantity-unit').val(item.quantity_unit || '').trigger('change');
        }

        await loadProductData(item.product_id, false);
        updateLocationInfo();
        openModal('inventory-modal');
    };

    window.deleteInventory = async function (id) {
        if (!window.confirm('Soll dieser Inventareintrag wirklich gelöscht werden?')) return;

        try {
            const json = await fetchJson(`${app.dataset.deleteBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf }
            });

            toast('Gelöscht', json.message || 'Inventareintrag wurde gelöscht.', 'ok');
            await Promise.all([loadAnalytics(), loadList()]);
        } catch (error) {
            toast('Fehler', error.message || 'Löschen fehlgeschlagen.', 'bad');
        }
    };

    window.submitInventoryForm = async function () {
        const form = document.getElementById('inventory-form');
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        const isEdit = !!state.editingId;
        const url = isEdit ? `${app.dataset.updateBaseUrl}/${state.editingId}` : app.dataset.storeUrl;

        try {
            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(payload)
            });

            closeModal('inventory-modal');
            resetForm();
            toast('Gespeichert', json.message || 'Inventareintrag wurde gespeichert.', 'ok');
            await Promise.all([loadAnalytics(), loadList()]);
        } catch (error) {
            toast('Fehler', error.message || 'Speichern fehlgeschlagen.', 'bad');
        }
    };

    window.resetInventoryFilters = function () {
        state.page = 1;
        state.per_page = 20;
        state.search = '';
        state.product_id = '';
        state.branch_id = '';
        state.responsible_id = '';
        state.stock_filter = '';
        state.sort = 'id';
        state.direction = 'desc';

        document.getElementById('filter-search').value = '';
        document.getElementById('filter-stock').value = '';
        document.getElementById('filter-sort').value = 'id';
        document.getElementById('filter-direction').value = 'desc';
        document.getElementById('filter-per-page').value = '20';

        if (window.jQuery) {
            $('#filter-product').val('').trigger('change');
            $('#filter-branch').val('').trigger('change');
            $('#filter-responsible').val('').trigger('change');
        }

        loadList();
    };

    window.captureCurrentLocation = function () {
        if (!navigator.geolocation) {
            toast('Fehler', 'Geolocation wird von diesem Browser nicht unterstützt.', 'bad');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                document.getElementById('form-latitude').value = position.coords.latitude;
                document.getElementById('form-longitude').value = position.coords.longitude;
                updateLocationInfo();
                toast('Standort übernommen', 'Die aktuellen Koordinaten wurden gespeichert.', 'ok');
            },
            function (error) {
                let msg = 'Standort konnte nicht gelesen werden.';
                if (error.code === 1) msg = 'Standortfreigabe wurde verweigert.';
                if (error.code === 2) msg = 'Standort ist nicht verfügbar.';
                if (error.code === 3) msg = 'Zeitüberschreitung bei der Standortabfrage.';
                toast('Fehler', msg, 'bad');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    window.openLocationInGoogle = function () {
        const lat = document.getElementById('form-latitude').value;
        const lng = document.getElementById('form-longitude').value;
        const url = buildGoogleMapsUrl(lat, lng);
        if (!url) {
            toast('Hinweis', 'Es sind noch keine Koordinaten vorhanden.', 'bad');
            return;
        }
        window.open(url, '_blank');
    };

    window.openUseProductModal = function (id) {
        const item = state.items.find(row => Number(row.id) === Number(id));
        if (!item) return;

        document.getElementById('use-inventory-id').value = item.id;
        document.getElementById('use-product-name').value = `${item.product_name || ''}${item.product_model ? ' - ' + item.product_model : ''}`;
        document.getElementById('use-current-qty').value = `${item.quantity ?? 0} ${item.quantity_unit || ''}`.trim();
        document.getElementById('use-quantity-used').value = '';
        document.getElementById('use-usage-location').value = '';
        document.getElementById('use-note').value = '';

        const now = new Date();
        const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        document.getElementById('use-used-at').value = local;

        if (window.jQuery) $('#use-customer-id').val('').trigger('change');

        openModal('use-product-modal');
    };

    window.closeUseProductModal = function () {
        closeModal('use-product-modal');
    };

    window.submitUseProductForm = async function () {
        const inventoryId = document.getElementById('use-inventory-id').value;

        const payload = {
            customer_id: document.getElementById('use-customer-id').value,
            quantity_used: document.getElementById('use-quantity-used').value,
            usage_location: document.getElementById('use-usage-location').value,
            note: document.getElementById('use-note').value,
            used_at: document.getElementById('use-used-at').value,
        };

        try {
            const json = await fetchJson(`${app.dataset.useBaseUrl}/${inventoryId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(payload)
            });

            closeModal('use-product-modal');
            toast('Gespeichert', json.message || 'Verbrauch wurde gespeichert.', 'ok');

            await Promise.all([loadAnalytics(), loadList()]);
            state.historyPage = 1;
            if (state.currentTab === 'history') await loadHistory();
        } catch (error) {
            toast('Fehler', error.message || 'Verbrauch konnte nicht gespeichert werden.', 'bad');
        }
    };

    window.closeDuplicateProductModal = function () {
        closeModal('duplicate-product-modal');
    };

    window.continueAddingDuplicateProduct = function () {
        state.suppressDuplicateCheck = true;
        closeModal('duplicate-product-modal');
        toast('Hinweis', 'Sie können trotzdem einen neuen Eintrag für dieses Produkt anlegen.', 'ok');
    };

    window.editDuplicateProduct = function () {
        const item = state.duplicateInventoryItem;
        closeModal('duplicate-product-modal');
        if (!item) return;

        state.suppressDuplicateCheck = true;
        if (!state.items.find(row => Number(row.id) === Number(item.id))) {
            state.items.unshift(item);
        }
        editInventory(item.id);
    };

    function bindFilters() {
        const search = document.getElementById('filter-search');
        const stock = document.getElementById('filter-stock');
        const sort = document.getElementById('filter-sort');
        const direction = document.getElementById('filter-direction');
        const perPage = document.getElementById('filter-per-page');
        const prevBtn = document.getElementById('btn-prev-page');
        const nextBtn = document.getElementById('btn-next-page');
        const historySearch = document.getElementById('history-search');
        const historyPrevBtn = document.getElementById('btn-history-prev-page');
        const historyNextBtn = document.getElementById('btn-history-next-page');

        let searchTimer = null;
        let historyTimer = null;

        search?.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.search = this.value.trim();
                state.page = 1;
                loadList();
            }, 300);
        });

        historySearch?.addEventListener('input', function () {
            clearTimeout(historyTimer);
            historyTimer = setTimeout(() => {
                state.historySearch = this.value.trim();
                state.historyPage = 1;
                loadHistory();
            }, 300);
        });

        stock?.addEventListener('change', function () {
            state.stock_filter = this.value;
            state.page = 1;
            loadList();
        });

        sort?.addEventListener('change', function () {
            state.sort = this.value;
            state.page = 1;
            loadList();
        });

        direction?.addEventListener('change', function () {
            state.direction = this.value;
            state.page = 1;
            loadList();
        });

        perPage?.addEventListener('change', function () {
            state.per_page = this.value;
            state.page = 1;
            loadList();
        });

        prevBtn?.addEventListener('click', function () {
            if (state.page > 1) {
                state.page--;
                loadList();
            }
        });

        nextBtn?.addEventListener('click', function () {
            if (state.meta && state.page < state.meta.last_page) {
                state.page++;
                loadList();
            }
        });

        historyPrevBtn?.addEventListener('click', function () {
            if (state.historyPage > 1) {
                state.historyPage--;
                loadHistory();
            }
        });

        historyNextBtn?.addEventListener('click', function () {
            if (state.historyMeta && state.historyPage < state.historyMeta.last_page) {
                state.historyPage++;
                loadHistory();
            }
        });

        if (window.jQuery) {
            $('#filter-product').on('change', function () {
                state.product_id = $(this).val() || '';
                state.page = 1;
                loadList();
            });

            $('#filter-branch').on('change', function () {
                state.branch_id = $(this).val() || '';
                state.page = 1;
                loadList();
            });

            $('#filter-responsible').on('change', function () {
                state.responsible_id = $(this).val() || '';
                state.page = 1;
                loadList();
            });

            $('#form-product-id').on('change', async function () {
                const productId = $(this).val() || '';
                await loadProductData(productId, true);
                await checkDuplicateInventoryForProduct(productId);
            });
        }
    }

    function initSelect2() {
        if (!window.jQuery || !$.fn.select2) return;

        $('.js-select2').select2({ width: '100%' });

        $('.js-select2-modal-inventory').select2({
            width: '100%',
            dropdownParent: $('#inventory-modal')
        });

        $('.js-select2-modal-use').select2({
            width: '100%',
            dropdownParent: $('#use-product-modal')
        });

        $('.js-select2-category').select2({
            width: '100%',
            tags: true,
            dropdownParent: $('#inventory-modal'),
            placeholder: 'Kategorie wählen / eingeben'
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target === document.getElementById('inventory-modal')) closeModal('inventory-modal');
        if (e.target === document.getElementById('use-product-modal')) closeModal('use-product-modal');
        if (e.target === document.getElementById('duplicate-product-modal')) closeModal('duplicate-product-modal');
    });

    document.addEventListener('DOMContentLoaded', async function () {
        initSelect2();
        bindFilters();
        updateProductPreview(null);
        updateLocationInfo();

        try {
            await Promise.all([loadAnalytics(), loadList()]);
        } catch (error) {
            toast('Fehler', error.message || 'Daten konnten nicht geladen werden.', 'bad');
        }
    });
})();
</script>
@endpush
@endonce