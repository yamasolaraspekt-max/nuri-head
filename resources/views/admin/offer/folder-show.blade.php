@extends('admin.layouts.app')

@section('title', 'Ordner-Workspace')

@php
    $offer = $folder->offer;
    $detail = $folder->detail ?? $offer?->detail;

    $customerName = trim(
        ($offer?->customer?->firma ?? '') . ' ' .
        ($offer?->customer?->name ?? '') . ' ' .
        ($offer?->customer?->lastname ?? '')
    );

    $employeeName = trim(
        ($folder?->creator?->name ?? '') . ' ' .
        ($folder?->creator?->lastname ?? '')
    );

    $initialSections = is_array($detail?->sections) ? $detail->sections : [];
    $initialPlacedImages = is_array($detail?->placed_images) ? $detail->placed_images : [];

    $wizardParams = array_filter([
        'offer_id'        => $offer?->id ?? $folder->offer_id,
        'offer_folder_id' => $folder->id,
        'customer_id'     => $offer?->customer_id ?? $offer?->customer?->id ?? null,
        'alternative_id'  => $offer?->alternative_id ?? $offer?->alternative?->id ?? null,
        'product_id'      => $offer?->product_id ?? $offer?->product?->id ?? null,
    ], fn ($value) => !is_null($value) && $value !== '');

    $wizardUrl = url('offers/wizard') . '?' . http_build_query($wizardParams);
    $initialAttachments = $folder->attachments ?? collect();

    $resolvedAgbTitle =
        $folderAgb['title']
        ?? $detail?->agb_title
        ?? $defaultAgb['title']
        ?? 'Allgemeine Geschäftsbedingungen';

    $resolvedAgbText =
        $folderAgb['text']
        ?? $detail?->agb_text
        ?? $defaultAgb['text']
        ?? '';
@endphp

@once
@push('style')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
    :root{
        --of-bg:#f3f4f6;
        --of-card:#ffffff;
        --of-card-soft:#f8fafc;
        --of-text:#111827;
        --of-muted:#6b7280;
        --of-line:#e5e7eb;
        --of-primary:#93c21c;
        --of-primary-hover:#7baa18;
        --of-primary-soft:#f4fae7;
        --of-blue:#2563eb;
        --of-success:#10b981;
        --of-success-soft:#ecfdf5;
        --of-warning:#f59e0b;
        --of-warning-soft:#fffbeb;
        --of-danger:#ef4444;
        --of-danger-hover:#dc2626;
        --of-danger-soft:#fef2f2;
        --of-shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
        --of-shadow:0 10px 25px -10px rgb(0 0 0 / .20), 0 4px 10px -4px rgb(0 0 0 / .08);
        --of-radius:18px;
        --of-radius-lg:24px;
        --of-transition:all .2s ease-in-out;
    }

    .of-wrap{
        max-width:1680px;
        margin:0 auto;
        padding:38px 64px 22px 38px;
        font-family:Inter,system-ui,-apple-system,sans-serif;
        color:var(--of-text);
    }

    .of-header{
        margin:108px 0 18px;
        background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);
        border:1px solid var(--of-line);
        border-radius:var(--of-radius-lg);
        box-shadow:var(--of-shadow);
        overflow:hidden;
    }

    .of-header-inner{ padding:22px; }

    .of-top{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:18px;
        flex-wrap:wrap;
    }

    .of-head-left{
        display:flex;
        align-items:flex-start;
        gap:16px;
        min-width:0;
    }

    .of-icon-box{
        width:68px;
        height:68px;
        border-radius:18px;
        background:linear-gradient(135deg,var(--of-primary-soft),#ffffff);
        border:1px solid #d9ef9d;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
        box-shadow:var(--of-shadow-sm);
    }

    .of-title{
        font-size:30px;
        font-weight:900;
        letter-spacing:-.03em;
        color:#111827;
        line-height:1.1;
        margin:0;
    }

    .of-sub{
        color:var(--of-muted);
        font-size:14px;
        margin-top:8px;
        line-height:1.7;
    }

    .of-meta-row{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:14px;
    }

    .of-meta-pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:999px;
        border:1px solid var(--of-line);
        background:#fff;
        font-size:12px;
        font-weight:800;
        color:#374151;
        box-shadow:var(--of-shadow-sm);
    }

    .of-presence{
        margin-top:14px;
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        padding:10px 12px;
        border:1px solid var(--of-line);
        background:#fff;
        border-radius:16px;
        box-shadow:var(--of-shadow-sm);
    }

    .of-presence-label{
        font-size:12px;
        font-weight:900;
        color:#374151;
        display:flex;
        align-items:center;
        gap:8px;
    }

    .of-presence-list{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:10px;
    }

    .of-presence-empty{
        font-size:12px;
        color:var(--of-muted);
        font-weight:700;
    }

    .of-presence-user{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:#f8fafc;
        border:1px solid var(--of-line);
        border-radius:999px;
        padding:6px 10px 6px 6px;
    }

    .of-presence-avatar-wrap{
        position:relative;
        width:30px;
        height:30px;
        flex:0 0 auto;
    }

    .of-presence-avatar{
        width:30px;
        height:30px;
        border-radius:999px;
        object-fit:cover;
        border:2px solid #fff;
        display:block;
        background:#e5e7eb;
    }

    .of-presence-dot{
        position:absolute;
        right:-1px;
        bottom:-1px;
        width:10px;
        height:10px;
        border-radius:999px;
        background:#10b981;
        border:2px solid #fff;
    }

    .of-presence-name{
        font-size:12px;
        font-weight:800;
        color:#111827;
        max-width:180px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .of-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        border:none;
        background:var(--of-primary);
        color:#fff;
        padding:10px 14px;
        border-radius:12px;
        font-weight:800;
        cursor:pointer;
        text-decoration:none;
        transition:var(--of-transition);
        box-shadow:var(--of-shadow-sm);
    }

    .of-btn:hover{
        background:var(--of-primary-hover);
        color:#fff;
    }

    .of-btn.soft{
        background:#fff;
        color:var(--of-text);
        border:1px solid var(--of-line);
    }

    .of-btn.soft:hover{ background:#f9fafb; }

    .of-btn.danger{
        color:white !important;
        background:var(--of-danger);
    }

    .of-btn.danger:hover{ background:var(--of-danger-hover); }

    .of-btn[disabled]{
        opacity:.55;
        cursor:not-allowed;
        pointer-events:none;
    }

    .of-stats{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:16px;
        margin-bottom:18px;
    }

    @media(max-width:1200px){
        .of-stats{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media(max-width:640px){
        .of-stats{ grid-template-columns:1fr; }
        .of-wrap{ padding:24px 16px; }
    }

    .of-stat{
        background:var(--of-card);
        border:1px solid var(--of-line);
        border-radius:18px;
        box-shadow:var(--of-shadow-sm);
        padding:18px;
    }

    .of-stat-label{
        font-size:12px;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:var(--of-muted);
        font-weight:900;
    }

    .of-stat-value{
        margin-top:8px;
        font-size:28px;
        line-height:1.1;
        font-weight:900;
        color:#111827;
    }

    .of-stat-sub{
        margin-top:6px;
        font-size:13px;
        color:#6b7280;
    }

    .of-shell{
        background:var(--of-card);
        border:1px solid var(--of-line);
        border-radius:var(--of-radius-lg);
        box-shadow:var(--of-shadow);
        overflow:hidden;
    }

    .of-shell-head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:14px;
        flex-wrap:wrap;
        padding:18px 20px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
    }

    .of-tabs{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-tab{
        border:1px solid var(--of-line);
        background:#fff;
        color:#374151;
        border-radius:14px;
        padding:10px 14px;
        font-size:13px;
        font-weight:800;
        cursor:pointer;
        transition:var(--of-transition);
        display:inline-flex;
        align-items:center;
        gap:10px;
        min-height:46px;
        box-shadow:var(--of-shadow-sm);
    }

    .of-tab:hover{
        background:#f9fafb;
        border-color:#d1d5db;
        transform:translateY(-1px);
    }

    .of-tab.active{
        background:var(--of-primary-soft);
        border-color:#d8ec9d;
        color:#55720d;
    }

    .of-tab-icon{
        width:18px;
        height:18px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .of-tab-label{
        display:inline-flex;
        align-items:center;
        gap:8px;
        line-height:1;
    }

    .of-tab-count{
        min-width:24px;
        height:24px;
        padding:0 8px;
        border-radius:999px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:11px;
        font-weight:900;
        background:#f3f4f6;
        color:#374151;
        border:1px solid #e5e7eb;
        line-height:1;
    }

    .of-tab.active .of-tab-count{
        background:#fff;
        border-color:#d9ef9d;
        color:#55720d;
    }

    .of-shell-body{ padding:20px; }
    .of-panel{ display:none; }
    .of-panel.active{ display:block; }

    .of-grid-2{
        display:grid;
        grid-template-columns:420px minmax(0,1fr);
        gap:18px;
    }

    @media(max-width:1200px){
        .of-grid-2{ grid-template-columns:1fr; }
    }

    .of-card{
        background:var(--of-card); 
        overflow:visible;
    }

   .of-card-h{
        padding:16px 18px; 
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:#fafafa;
        flex-wrap:wrap;
        position:relative;
        z-index:1;
    }

    .of-card-title{
        font-size:16px;
        font-weight:900;
        color:#111827;
        margin:0;
    }

    .of-card-b{ padding:18px; }

    .of-info-list{
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .of-info-item{
        display:flex;
        justify-content:space-between;
        gap:14px;
        padding-bottom:12px;
        border-bottom:1px dashed #e5e7eb;
    }

    .of-info-item:last-child{
        border-bottom:none;
        padding-bottom:0;
    }

    .of-info-key{
        color:#6b7280;
        font-size:13px;
        font-weight:800;
    }

    .of-info-val{
        color:#111827;
        font-size:13px;
        font-weight:900;
        text-align:right;
        word-break:break-word;
    }

    .of-cover{
        min-height:180px;
        border:1px dashed #d1d5db;
        border-radius:14px;
        background:#fafafa;
        padding:16px;
        color:#374151;
        font-size:14px;
        line-height:1.7;
    }

    .of-cover.empty{
        display:flex;
        align-items:center;
        justify-content:center;
        text-align:center;
        color:#9ca3af;
    }

    .of-status-overview{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:16px;
        margin-top:18px;
    }

    @media(max-width:1100px){
        .of-status-overview{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media(max-width:640px){
        .of-status-overview{ grid-template-columns:1fr; }
    }

    .of-status-card{
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        padding:18px;
    }

    .of-status-name{
        font-size:13px;
        color:#6b7280;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .of-status-value{
        margin-top:8px;
        font-size:28px;
        font-weight:900;
        color:#111827;
    }

    .of-kanban{
        display:grid;
        grid-template-columns:repeat(5,minmax(320px,1fr));
        gap:18px;
        align-items:start;
    }

    @media(max-width:1700px){
        .of-kanban{ grid-template-columns:repeat(3,minmax(320px,1fr)); }
    }

    @media(max-width:1180px){
        .of-kanban{ grid-template-columns:repeat(2,minmax(320px,1fr)); }
    }

    @media(max-width:760px){
        .of-kanban{ grid-template-columns:1fr; }
    }

    .of-col{
        background:linear-gradient(180deg,#f8fafc 0%,#f3f4f6 100%);
        border:1px solid var(--of-line);
        border-radius:22px;
        min-height:420px;
        display:flex;
        flex-direction:column;
        overflow:hidden;
        box-shadow:var(--of-shadow-sm);
    }

    .of-col-h{
        padding:16px 18px;
        border-bottom:1px solid var(--of-line);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        background:#fff;
    }

    .of-col-name{
        font-size:15px;
        font-weight:900;
        color:#111827;
        letter-spacing:-.01em;
    }

    .of-col-count{
        min-width:30px;
        height:30px;
        border-radius:999px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        background:var(--of-primary-soft);
        border:1px solid #d8ec9d;
        font-size:12px;
        font-weight:900;
        color:#55720d;
    }

    .of-col[data-status="draft"] .of-col-h{
        background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
    }
    .of-col[data-status="draft"] .of-col-count{
        background:#f3f4f6;
        border-color:#d1d5db;
        color:#4b5563;
    }

    .of-col[data-status="sent"] .of-col-count{
        background:#eff6ff;
        border-color:#bfdbfe;
        color:#74b2d4;
    }

    .of-col[data-status="negotiation"] .of-col-count{
        background:#fffbeb;
        border-color:#fde68a;
        color:#b45309;
    }

    .of-col[data-status="final"] .of-col-count{
        background:#93c21c; 
        color:white;
    }

    .of-col[data-status="cancel"] .of-col-count{
        background:#fef2f2;
        border-color:#fecaca;
        color:#b91c1c;
    }

    .of-list{
        padding:16px;
        display:flex;
        flex-direction:column;
        gap:14px;
        min-height:220px;
        flex:1;
    }

    .of-item{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:20px;
        padding:0;
        box-shadow:0 8px 24px rgba(15,23,42,.06);
        transition:var(--of-transition);
        cursor:grab;
        overflow:hidden;
    }

    .of-item:hover{
        border-color:#cbd5e1;
        transform:translateY(-2px);
        box-shadow:0 12px 28px rgba(15,23,42,.10);
    }

    .of-kanban-offer{
        display:flex;
        flex-direction:column;
    }

    .of-kanban-offer-top{
        padding:18px 18px 16px;
        border-bottom:1px solid #eef2f7;
        background:
            radial-gradient(circle at top right, rgba(147,194,28,.10), transparent 30%),
            linear-gradient(180deg,#ffffff 0%,#fafafa 100%);
    }

    .of-kanban-brand{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:14px;
        margin-bottom:16px;
    }

    .of-kanban-brand-left{
        min-width:0;
        flex:1;
    }

    .of-kanban-company-mini{
        font-size:10px;
        font-weight:800;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.08em;
    }

    .of-kanban-doc-title{
        margin-top:8px;
        font-size:20px;
        line-height:1.1;
        font-weight:900;
        color:#7baa18;
        text-transform:uppercase;
    }

    .of-kanban-status-chip{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:7px 10px;
        border-radius:999px;
        font-size:11px;
        font-weight:900;
        border:1px solid #e5e7eb;
        white-space:nowrap;
    }

    .of-kanban-status-chip.draft{
        background:#f3f4f6;
        color:#4b5563;
        border-color:#d1d5db;
    }
    .of-kanban-status-chip.sent{
        background:#eff6ff;
        color:#74b2d4;
        border-color:#bfdbfe;
    }
    .of-kanban-status-chip.negotiation{
        background:#fffbeb;
        color:#b45309;
        border-color:#fde68a;
    }
    .of-kanban-status-chip.final{
        background:#ecfdf5;
        color:#047857;
        
    }
    .of-kanban-status-chip.cancel{
        background:#fef2f2;
        color:#b91c1c;
        border-color:#fecaca;
    }

    .of-kanban-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:14px;
    }

    .of-kanban-block{
        min-width:0;
    }

    .of-kanban-block-label{
        font-size:10px;
        font-weight:900;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.08em;
        margin-bottom:6px;
    }

    .of-kanban-block-value{
        font-size:13px;
        line-height:1.65;
        color:#111827;
        font-weight:800;
        word-break:break-word;
    }

    .of-kanban-body{
        padding:16px 18px;
    }

    .of-kanban-meta{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:10px;
        margin-bottom:14px;
    }

    .of-kanban-meta-card{
        border:1px solid #eef2f7;
        border-radius:14px;
        background:#fafafa;
        padding:12px;
    }

    .of-kanban-meta-label{
        font-size:10px;
        font-weight:900;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.08em;
    }

    .of-kanban-meta-value{
        margin-top:6px;
        font-size:14px;
        font-weight:900;
        color:#111827;
        line-height:1.4;
    }

    .of-kanban-product{
        border:1px dashed #dbe3ea;
        border-radius:14px;
        background:#fcfcfd;
        padding:12px 14px;
    }

    .of-kanban-product-label{
        font-size:10px;
        font-weight:900;
        color:#94a3b8;
        text-transform:uppercase;
        letter-spacing:.08em;
    }

    .of-kanban-product-value{
        margin-top:6px;
        font-size:13px;
        font-weight:900;
        color:#111827;
        line-height:1.55;
    }

    .of-kanban-note{
        margin-top:12px;
        font-size:12px;
        color:#6b7280;
        line-height:1.65;
    }

    .of-item-actions{
        display:flex;
        justify-content:flex-end;
        gap:8px;
        padding:14px 18px 18px;
    }

    .of-item-action{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        min-height:38px;
        padding:0 12px;
        border-radius:10px;
        border:1px solid var(--of-line);
        background:#fff;
        color:#111827;
        font-size:12px;
        font-weight:800;
        cursor:pointer;
        text-decoration:none;
        transition:var(--of-transition);
    }

    .of-item-action:hover{
        background:#f8fafc;
        border-color:#cbd5e1;
    }

    .of-item-action.primary{
        background:var(--of-primary-soft);
        border-color:#d9ef9d;
        color:#55720d;
    }

    .of-item-action.primary:hover{
        background:#edf8d2;
    }

    
    .of-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 10px;
        border-radius:999px;
        background:#f8fafc;
        border:1px solid var(--of-line);
        font-size:11px;
        font-weight:900;
        color:#4b5563;
    }

    .of-empty{
        text-align:center;
        padding:28px 18px;
        border:1px dashed var(--of-line);
        border-radius:14px;
        color:var(--of-muted);
        font-size:13px;
        background:#fff;
    }

    .of-sections{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
        gap:16px;
    }

    .of-section-card{
        border:1px solid var(--of-line);
        border-radius:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
    }

    .of-section-head{
        padding:14px 16px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
    }

    .of-section-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
    }

    .of-section-body{ padding:16px; }

    .of-section-desc{
        font-size:13px;
        color:#6b7280;
        line-height:1.6;
    }

    .of-section-stats{
        margin-top:14px;
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .of-history-placeholder{
        min-height:320px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        gap:10px;
        border:1px dashed var(--of-line);
        border-radius:18px;
        background:#fafafa;
        color:#9ca3af;
        padding:28px;
        text-align:center;
    }

    .of-table-wrap{
        width:100%;
        overflow:auto;
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
    }

    .of-table{
        width:100%;
        border-collapse:collapse;
        min-width:980px;
    }

    .of-table th,
    .of-table td{
        padding:12px 14px;
        border-bottom:1px solid #eef2f7;
        text-align:left;
        vertical-align:top;
        font-size:13px;
    }

    .of-table th{
        background:#f8fafc;
        color:#374151;
        font-weight:900;
        white-space:nowrap;
    }

    .of-table td{ color:#111827; }

    .of-table tr:hover td{ background:#fcfcfd; }

    .of-table .num{
        text-align:right;
        white-space:nowrap;
        font-variant-numeric:tabular-nums;
    }

    .of-table .muted{ color:#6b7280; }

    .of-inline-actions{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-print-sheet{
        background:#fff;
        border:1px solid var(--of-line);
        border-radius:18px;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
    }

    .of-print-head{
        padding:18px 20px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:16px;
        flex-wrap:wrap;
    }

    .of-print-title{
        font-size:20px;
        font-weight:900;
        color:#111827;
        margin:0;
    }

    .of-print-sub{
        margin-top:6px;
        color:#6b7280;
        font-size:13px;
        line-height:1.6;
    }

    .of-print-meta{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:12px;
        padding:18px 20px;
        border-bottom:1px solid var(--of-line);
        background:#fff;
    }

    @media(max-width:900px){
        .of-print-meta{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media(max-width:560px){
        .of-print-meta{ grid-template-columns:1fr; }
    }

    .of-print-stat{
        border:1px solid #eef2f7;
        border-radius:14px;
        padding:14px;
        background:#fafafa;
    }

    .of-print-stat-label{
        font-size:11px;
        font-weight:900;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:#6b7280;
    }

    .of-print-stat-value{
        margin-top:8px;
        font-size:20px;
        font-weight:900;
        color:#111827;
    }

    .of-print-body{ padding:20px; }

    .of-print-only{ display:none; }

    .of-table-check{
        width:42px;
        text-align:center !important;
    }

    .of-check{
        width:18px;
        height:18px;
        accent-color:var(--of-primary);
        cursor:pointer;
    }

    .of-table tr.is-selected td{ background:#f4fae7 !important; }
    .of-table tr.is-selected:hover td{ background:#edf8d2 !important; }

    .of-mat-name{
        display:flex;
        flex-direction:column;
        gap:6px;
    }

    .of-mat-title{
        font-weight:900;
        color:#111827;
        line-height:1.45;
    }

    .of-mat-meta{
        display:flex;
        flex-wrap:wrap;
        gap:6px;
    }

    .of-mat-chip{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:4px 8px;
        border-radius:999px;
        background:#f8fafc;
        border:1px solid #e5e7eb;
        font-size:11px;
        font-weight:800;
        color:#4b5563;
    }

    .of-mat-desc{
        color:#6b7280;
        font-size:12px;
        line-height:1.55;
        display:none;
    }

    .of-material-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:14px;
    }

    .of-material-toolbar-left,
    .of-material-toolbar-right{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-selected-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:999px;
        background:var(--of-primary-soft);
        border:1px solid #d9ef9d;
        color:#55720d;
        font-size:12px;
        font-weight:900;
    }

    .of-file-list{
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .of-file-row{
        border:1px solid var(--of-line);
        border-radius:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        padding:14px 16px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:14px;
        flex-wrap:wrap;
    }

    .of-file-left{
        min-width:0;
        flex:1;
    }

    .of-file-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
        word-break:break-word;
    }

    .of-file-meta{
        margin-top:6px;
        display:flex;
        gap:8px;
        flex-wrap:wrap;
    }

    .of-file-actions{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-file-preview{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:10px;
        border:1px solid var(--of-line);
        background:#fff;
        text-decoration:none;
        color:#111827;
        font-weight:800;
    }
</style>
<style>
    .of-file-list.sortable-enabled .of-file-row{ cursor:grab; }

    .of-file-type-badge.pdf{
        background:#eff6ff;
        border-color:#bfdbfe;
        color:#74b2d4;
    }

    .of-file-type-badge.image{
        background:#ecfdf5;
        
        color:#047857;
    }

    .of-dropzone-over{
        border-color:#93c21c !important;
        background:#f4fae7 !important;
    }

    #agb-editor{
        min-height:360px;
        border:1px solid var(--of-line);
        border-radius:12px;
        background:#fff;
    }

    #agb-editor .ql-toolbar.ql-snow{
        border:none;
        border-bottom:1px solid var(--of-line);
        background:#f8fafc;
    }

    #agb-editor .ql-container.ql-snow{
        border:none;
        min-height:300px;
        font-size:14px;
        line-height:1.7;
    }

    #agb-editor .ql-editor{
        min-height:300px;
        color:#111827;
    }

    .of-smart-side{
        position:fixed;
        top:140px;
        right:24px;
        width:515px;
        max-width:calc(100vw - 32px);
        z-index:10020;
        display:none;
    }

    .of-smart-side.show{ display:block; }

    .of-smart-card{
        background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
        border:1px solid var(--of-line);
        border-radius:24px;
        box-shadow:0 24px 70px rgba(15,23,42,.18);
        overflow:hidden;
    }

    .of-smart-head{
        padding:18px 18px 14px;
        border-bottom:1px solid var(--of-line);
        background:
            radial-gradient(circle at top right, rgba(147,194,28,.15), transparent 34%),
            linear-gradient(180deg,#ffffff 0%,#fafafa 100%);
    }

    .of-smart-head-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
    }

    .of-smart-icon{
        width:48px;
        height:48px;
        border-radius:16px;
        background:var(--of-primary-soft);
        border:1px solid #d9ef9d;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .of-smart-title{
        font-size:16px;
        font-weight:900;
        color:#111827;
        line-height:1.3;
        margin:0;
    }

    .of-smart-sub{
        margin-top:6px;
        font-size:12px;
        color:#6b7280;
        line-height:1.6;
    }

    .of-smart-close{
        width:36px;
        height:36px;
        border:none;
        border-radius:12px;
        background:#fff;
        border:1px solid var(--of-line);
        color:#6b7280;
        cursor:pointer;
        flex:0 0 auto;
    }

    .of-smart-body{
        padding:16px;
        display:flex;
        flex-direction:column;
        gap:14px;
    }

    .of-smart-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }

    .of-smart-metric{
        border:1px solid #eef2f7;
        border-radius:18px;
        padding:14px;
        background:#fff;
    }

    .of-smart-metric-label{
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:#6b7280;
    }

    .of-smart-metric-value{
        margin-top:8px;
        font-size:20px;
        font-weight:900;
        color:#111827;
        line-height:1.2;
    }

    .of-smart-metric-value.success{ color:#74b2d4; }
    .of-smart-metric-value.muted{ color:#6b7280; }

    .of-smart-list{
        border:1px solid #eef2f7;
        border-radius:18px;
        background:#fff;
        overflow:hidden;
    }

    .of-smart-list-head{
        padding:12px 14px;
        border-bottom:1px solid #eef2f7;
        background:#fafafa;
        font-size:12px;
        font-weight:900;
        color:#374151;
    }

    .of-smart-list-body{
        max-height:260px;
        overflow:auto;
    }

    .of-smart-row{
        padding:12px 14px;
        border-bottom:1px solid #f1f5f9;
    }

    .of-smart-row:last-child{ border-bottom:none; }

    .of-smart-row-title{
        font-size:13px;
        font-weight:800;
        color:#111827;
        line-height:1.45;
    }

    .of-smart-row-sub{
        margin-top:5px;
        font-size:12px;
        color:#6b7280;
        line-height:1.6;
    }

    .of-smart-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    .of-smart-empty{
        padding:18px 16px;
        font-size:12px;
        color:#6b7280;
        text-align:center;
    }

    @media(max-width:1200px){
        .of-smart-side{
            position:static;
            width:100%;
            max-width:none;
            margin-top:16px;
        }
    }

    .of-modal-backdrop{
        position:fixed;
        inset:0;
        background:rgba(15,23,42,.48);
        z-index:9999;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:24px;
        backdrop-filter:blur(6px);
    }

    .of-modal{
        width:min(1400px,100%);
        max-height:90vh;
        overflow:hidden;
        background:#fff;
        border-radius:24px;
        box-shadow:0 25px 80px rgba(0,0,0,.22);
        display:flex;
        flex-direction:column;
    }

    .of-modal-head{
        padding:18px 20px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
    }

    .of-modal-body{
        padding:20px;
        overflow:auto;
    }

    .of-compare-layout{
        display:grid;
        grid-template-columns:minmax(0,1.25fr) minmax(460px,.75fr);
        gap:18px;
        align-items:start;
    }

    @media(max-width:1180px){
        .of-compare-layout{ grid-template-columns:1fr; }
    }

    .of-compare-left,
    .of-compare-right{
        min-width:0;
    }

    .of-compare-stats{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:14px;
        margin-bottom:16px;
    }

    @media(max-width:760px){
        .of-compare-stats{ grid-template-columns:1fr; }
    }

    .of-compare-card,
    .of-compare-chart{
        border:1px solid var(--of-line);
        border-radius:18px;
        padding:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        margin-bottom:18px;
    }

    .of-chart-box{
        position:relative;
        width:100%;
        height:320px;
        min-height:320px;
        max-height:320px;
    }

    .of-chart-box canvas{
        display:block !important;
        width:100% !important;
        height:100% !important;
    }

    .of-compare-side{
        border:1px solid var(--of-line);
        border-radius:20px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
        position:sticky;
        top:0;
        max-height:calc(90vh - 40px);
        display:flex;
        flex-direction:column;
    }

    .of-compare-side-head{
        padding:16px 18px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
        flex:0 0 auto;
    }

    .of-compare-search{
        margin-top:12px;
        position:relative;
    }

    .of-compare-search input{
        width:100%;
        height:44px;
        border:1px solid var(--of-line);
        border-radius:12px;
        padding:0 14px;
        outline:none;
        font-size:13px;
        font-weight:700;
        background:#fff;
        color:#111827;
    }

    .of-compare-search input:focus{
        border-color:#cfe09b;
        box-shadow:0 0 0 4px rgba(147,194,28,.12);
    }

    .of-compare-side-body{
        padding:16px;
        overflow:auto;
        display:flex;
        flex-direction:column;
        gap:14px;
        min-height:0;
        flex:1 1 auto;
    }

    .of-dist-card{
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
        display:flex;
        flex-direction:column;
    }

    .of-dist-card.is-best{
        border-color:#b7df56;
        box-shadow:0 0 0 3px rgba(147,194,28,.10), var(--of-shadow-sm);
    }

    .of-dist-card.is-worst{
        border-color:#fecaca;
        box-shadow:0 0 0 3px rgba(239,68,68,.07), var(--of-shadow-sm);
    }

    .of-dist-card-head{
        padding:14px 16px;
        border-bottom:1px solid var(--of-line);
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        background:#fcfcfd;
        flex:0 0 auto;
    }

    .of-dist-title{
        font-size:15px;
        font-weight:900;
        color:#111827;
        line-height:1.35;
        word-break:break-word;
    }

    .of-dist-sub{
        margin-top:4px;
        font-size:12px;
        color:#6b7280;
        line-height:1.55;
    }

    .of-dist-rank{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 10px;
        border-radius:999px;
        font-size:11px;
        font-weight:900;
        white-space:nowrap;
        flex:0 0 auto;
    }

    .of-dist-rank.best{
        background:var(--of-primary-soft);
        color:#55720d;
        border:1px solid #d9ef9d;
    }

    .of-dist-rank.worst{
        background:var(--of-danger-soft);
        color:#b91c1c;
        border:1px solid #fecaca;
    }

    .of-dist-card-body{
        padding:14px 16px 16px;
        display:flex;
        flex-direction:column;
        gap:12px;
        min-height:0;
    }

    .of-dist-metrics{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:10px;
        flex:0 0 auto;
    }

    @media(max-width:640px){
        .of-dist-metrics{ grid-template-columns:1fr; }
    }

    .of-dist-metric{
        border:1px solid #eef2f7;
        border-radius:14px;
        padding:10px 12px;
        background:#fafafa;
        min-width:0;
    }

    .of-dist-metric-label{
        font-size:11px;
        font-weight:900;
        color:#6b7280;
        text-transform:uppercase;
        letter-spacing:.04em;
    }

    .of-dist-metric-value{
        margin-top:6px;
        font-size:15px;
        font-weight:900;
        color:#111827;
        word-break:break-word;
    }

    .of-dist-items{
        display:flex;
        flex-direction:column;
        gap:8px;
        max-height:230px;
        overflow:auto;
        padding-right:4px;
        min-height:0;
    }

    .of-dist-item{
        border:1px solid #eef2f7;
        border-radius:14px;
        padding:12px;
        background:#fff;
    }

    .of-dist-item-top{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
    }

    .of-dist-item-name{
        font-size:13px;
        font-weight:800;
        color:#111827;
        line-height:1.5;
        word-break:break-word;
        flex:1 1 auto;
        min-width:0;
    }

    .of-dist-item-sub{
        margin-top:8px;
        font-size:12px;
        color:#6b7280;
        line-height:1.7;
        word-break:break-word;
    }

    .of-dist-actions{
        margin-top:4px;
        display:flex;
        justify-content:flex-end;
        flex:0 0 auto;
        background:#fff;
        padding-top:6px;
    }

    .of-compare-filters{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        margin-top:12px;
    }

    .of-filter-chip{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:999px;
        border:1px solid #d9ef9d;
        background:var(--of-primary-soft);
        color:#55720d;
        font-size:12px;
        font-weight:900;
    }

    .of-filter-chip input{
        width:16px;
        height:16px;
        accent-color:var(--of-primary);
        cursor:pointer;
    }

    .of-toast-wrap{
        position:fixed;
        right:22px;
        bottom:22px;
        z-index:10050;
        display:flex;
        flex-direction:column;
        gap:10px;
    }

    .of-toast{
        min-width:320px;
        max-width:440px;
        background:#111827;
        color:#fff;
        border-radius:16px;
        box-shadow:0 20px 50px rgba(15,23,42,.28);
        padding:14px 16px;
        display:flex;
        gap:12px;
        align-items:flex-start;
        animation:ofToastIn .22s ease;
    }

    .of-toast.success{
        background:linear-gradient(135deg,#0f172a 0%,#14532d 100%);
    }

    .of-toast-icon{
        width:34px;
        height:34px;
        border-radius:999px;
        background:rgba(255,255,255,.12);
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .of-toast-title{
        font-size:13px;
        font-weight:900;
        margin:0;
    }

    .of-toast-text{
        margin-top:4px;
        font-size:12px;
        color:rgba(255,255,255,.88);
        line-height:1.6;
    }

    @keyframes ofToastIn{
        from{ opacity:0; transform:translateY(10px); }
        to{ opacity:1; transform:translateY(0); }
    }

    .of-material-details-modal .of-modal{
        width:min(1050px,96vw);
    }

    .of-material-detail-grid{
        display:grid;
        grid-template-columns:minmax(0,1fr) minmax(0,1fr);
        gap:18px;
    }

    @media(max-width:960px){
        .of-material-detail-grid{ grid-template-columns:1fr; }
    }

    .of-material-detail-card{
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
    }

    .of-material-detail-head{
        padding:14px 16px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
    }

    .of-material-detail-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
    }

    .of-material-detail-body{
        padding:16px;
    }

    .of-material-kv{
        display:grid;
        grid-template-columns:140px 1fr;
        gap:10px 14px;
    }

    .of-material-kv-label{
        font-size:12px;
        font-weight:900;
        color:#6b7280;
    }

    .of-material-kv-value{
        font-size:13px;
        font-weight:800;
        color:#111827;
        word-break:break-word;
    }

    .of-material-savings{
        margin-top:14px;
        border:1px solid #d9ef9d;
        background:var(--of-primary-soft);
        color:#55720d;
        border-radius:16px;
        padding:14px 16px;
        font-weight:900;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
    }

    .of-material-option-list{
        display:flex;
        flex-direction:column;
        gap:10px;
        max-height:420px;
        overflow:auto;
    }

    .of-material-option{
        border:1px solid var(--of-line);
        border-radius:16px;
        background:#fff;
        padding:14px;
        cursor:pointer;
        transition:var(--of-transition);
    }

    .of-material-option:hover{
        border-color:#cdd5df;
        background:#fcfcfd;
    }

    .of-material-option.active{
        border-color:#b7df56;
        box-shadow:0 0 0 3px rgba(147,194,28,.10);
        background:#f9fdf0;
    }

    .of-material-option-top{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
    }

    .of-material-option-name{
        font-size:14px;
        font-weight:900;
        color:#111827;
        line-height:1.4;
    }

    .of-material-option-price{
        font-size:14px;
        font-weight:900;
        color:#111827;
        white-space:nowrap;
    }

    .of-material-option-sub{
        margin-top:8px;
        font-size:12px;
        color:#6b7280;
        line-height:1.65;
    }

    .of-material-row-click{
        cursor:pointer;
    }

    .of-material-row-click:hover td{
        background:#f8fbf0 !important;
    }

    @media print{
        body *{ visibility:hidden !important; }

        #panel-material-print,
        #panel-material-print *{
            visibility:visible !important;
        }

        #panel-material-print{
            display:block !important;
            position:absolute !important;
            left:0 !important;
            top:0 !important;
            width:100% !important;
            background:#fff !important;
            padding:0 !important;
            margin:0 !important;
        }

        .of-print-only{ display:block !important; }
        .of-no-print{ display:none !important; }

        .of-print-sheet,
        .of-table-wrap{
            border:none !important;
            box-shadow:none !important;
        }

        .of-table{ min-width:100% !important; }

        .of-table th,
        .of-table td{
            font-size:12px !important;
            padding:8px 10px !important;
        }

        .of-print-head{
            padding:0 0 16px 0 !important;
            background:#fff !important;
        }

        .of-print-meta{
            padding:0 0 16px 0 !important;
            border-bottom:1px solid #ddd !important;
        }

        .of-print-body{ padding:16px 0 0 0 !important; }
    }
</style>
<style>
   .of-colpicker{
        position:relative;
        z-index:50;
    }

    .of-colpicker summary{
        list-style:none;
    }

    .of-colpicker summary::-webkit-details-marker{
        display:none;
    }

    .of-colpicker[open]{
        z-index:9999;
    }

    .of-colpicker-menu{
        position:absolute;
        right:0;
        top:calc(100% + 8px);
        width:280px;
        max-height:70vh;
        overflow:auto;
        background:#fff;
        border:1px solid var(--of-line);
        border-radius:16px;
        box-shadow:0 20px 50px rgba(15,23,42,.18);
        padding:12px;
        z-index:99999;
    }

    .of-colpicker-grid{
        display:grid;
        grid-template-columns:1fr;
        gap:8px;
    }

    .of-colpicker-item{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:8px 10px;
        border:1px solid #eef2f7;
        border-radius:12px;
        background:#fafafa;
        font-size:12px;
        font-weight:800;
        color:#374151;
    }

    .of-colpicker-item input{
        width:16px;
        height:16px;
        accent-color:var(--of-primary);
        cursor:pointer;
    }

    .of-smart-compare{
        display:grid;
        grid-template-columns:1fr auto 1fr;
        gap:10px;
        align-items:stretch;
    }

    .of-smart-compare-card{
        border:1px solid #eef2f7;
        border-radius:18px;
        background:#fff;
        padding:14px;
    }

    .of-smart-compare-label{
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:#6b7280;
        margin-bottom:8px;
    }

    .of-smart-compare-name{
        font-size:13px;
        font-weight:900;
        color:#111827;
        line-height:1.45;
    }

    .of-smart-compare-sub{
        margin-top:6px;
        font-size:12px;
        color:#6b7280;
        line-height:1.6;
    }

    .of-smart-compare-price{
        margin-top:10px;
        font-size:18px;
        font-weight:900;
        color:#111827;
    }

    .of-smart-compare-price.success{
        color:#74b2d4;
    }

    .of-smart-compare-arrow{
        display:flex;
        align-items:center;
        justify-content:center;
        color:#93c21c;
        font-weight:900;
        font-size:18px;
    }

    .of-smart-row-head{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:10px;
    }

    .of-smart-row-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:5px 8px;
        border-radius:999px;
        font-size:11px;
        font-weight:900;
        white-space:nowrap;
        border:1px solid #d9ef9d;
        background:var(--of-primary-soft);
        color:#55720d;
    }

    .of-smart-row-badge.same{
        border-color:#e5e7eb;
        background:#f8fafc;
        color:#6b7280;
    }

    .of-smart-savings-bar{
        border:1px solid #d9ef9d;
        background:linear-gradient(135deg,#f4fae7 0%,#ffffff 100%);
        border-radius:18px;
        padding:14px 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
    }

    .of-smart-savings-value{
        font-size:18px;
        font-weight:900;
        color:#74b2d4;
    }

    .of-status-overview{
        display:grid;
        grid-template-columns:repeat(5,minmax(0,1fr));
        gap:16px;
        margin-top:18px;
    }

    .of-history-list{
        display:flex;
        flex-direction:column;
        gap:14px;
    }

    .of-history-item{
        position:relative;
        display:grid;
        grid-template-columns:56px 1fr;
        gap:14px;
        align-items:flex-start;
    }

    .of-history-dot-wrap{
        display:flex;
        justify-content:center;
        position:relative;
    }

    .of-history-dot-wrap::after{
        content:"";
        position:absolute;
        top:32px;
        bottom:-18px;
        width:2px;
        background:#e5e7eb;
    }

    .of-history-item:last-child .of-history-dot-wrap::after{
        display:none;
    }

    .of-history-dot{
        width:16px;
        height:16px;
        border-radius:999px;
        background:var(--of-primary);
        border:4px solid #f4fae7;
        box-shadow:0 0 0 2px #d9ef9d;
        margin-top:6px;
    }

    .of-history-card{
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        padding:16px 18px;
    }

    .of-history-top{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
    }

    .of-history-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
        line-height:1.45;
    }

    .of-history-date{
        font-size:12px;
        font-weight:800;
        color:#6b7280;
        white-space:nowrap;
    }

    .of-history-meta{
        margin-top:8px;
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .of-history-text{
        margin-top:10px;
        font-size:13px;
        line-height:1.7;
        color:#374151;
    }

    .of-history-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 10px;
        border-radius:999px;
        background:#f8fafc;
        border:1px solid #e5e7eb;
        font-size:11px;
        font-weight:900;
        color:#4b5563;
    }

    @media(max-width:1100px){
        .of-status-overview{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    .of-header-compact{
        margin:108px 0 18px;
    }

    .of-banner{
        display:flex;
        flex-direction:column;
        gap:16px;
    }

    .of-banner-main{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:18px;
        flex-wrap:wrap;
    }

    .of-head-content{
        min-width:0;
        flex:1;
    }

    .of-title-row{
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }

    .of-status-chip{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:7px 12px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        border:1px solid var(--of-line);
        white-space:nowrap;
        line-height:1;
    }

    .of-status-chip-draft{
        background:#f3f4f6;
        color:#4b5563;
        border-color:#d1d5db;
    }

    .of-status-chip-sent{
        background:#eff6ff;
        color:#74b2d4;
        border-color:#bfdbfe;
    }

    .of-status-chip-negotiation{
        background:#fffbeb;
        color:#b45309;
        border-color:#fde68a;
    }

    .of-status-chip-final{
        background:#ecfdf5;
        color:#047857;
        
    }

    .of-status-chip-cancel{
        background:#fef2f2;
        color:#b91c1c;
        border-color:#fecaca;
    }

    .of-presence-compact{
        margin-top:10px;
        padding:8px 12px;
    }

    .of-banner-stats{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:12px;
        padding-top:14px;
        border-top:1px solid var(--of-line);
    }

    .of-banner-stat{
        background:#fff;
        border:1px solid #eef2f7;
        border-radius:16px;
        padding:12px 14px;
        min-width:0;
    }

    .of-banner-stat-label{
        font-size:11px;
        font-weight:900;
        letter-spacing:.07em;
        text-transform:uppercase;
        color:#6b7280;
    }

    .of-banner-stat-value{
        margin-top:6px;
        font-size:20px;
        line-height:1.15;
        font-weight:900;
        color:#111827;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }


    .of-modal-tabs{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:16px;
        padding-bottom:14px;
        border-bottom:1px solid var(--of-line);
    }

    .of-modal-tab{
        border:1px solid var(--of-line);
        background:#fff;
        color:#374151;
        border-radius:12px;
        padding:10px 14px;
        font-size:13px;
        font-weight:800;
        cursor:pointer;
        transition:var(--of-transition);
        display:inline-flex;
        align-items:center;
        gap:8px;
        box-shadow:var(--of-shadow-sm);
    }

    .of-modal-tab:hover{
        background:#f9fafb;
        border-color:#d1d5db;
    }

    .of-modal-tab.active{
        background:var(--of-primary-soft);
        border-color:#d8ec9d;
        color:#55720d;
    }

    .of-modal-tab-panel{
        display:none;
    }

    .of-modal-tab-panel.active{
        display:block;
    }

    .of-history-inline-list{
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .of-history-inline-item{
        border:1px solid var(--of-line);
        border-radius:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        padding:14px 16px;
    }

    .of-history-inline-title{
        font-size:13px;
        font-weight:900;
        color:#111827;
        line-height:1.45;
    }

    .of-history-inline-sub{
        margin-top:6px;
        font-size:12px;
        color:#6b7280;
        line-height:1.7;
    }

    .of-doc-switch-wrap{
        margin-top:14px;
        display:flex;
        flex-direction:column;
        gap:10px;
    }

    .of-doc-switch-label{
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
    }

    .of-doc-switch{
        display:inline-flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
        padding:8px;
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
    }

    .of-doc-toggle{
        min-width:120px;
        border:1px solid var(--of-line);
        background:#fff;
        color:#374151;
        border-radius:14px;
        padding:11px 16px;
        font-size:13px;
        font-weight:900;
        cursor:pointer;
        transition:var(--of-transition);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
    }

    .of-doc-toggle:hover{
        background:#f9fafb;
        border-color:#d1d5db;
    }

    .of-doc-toggle.active{
        background:var(--of-primary-soft);
        border-color:#d8ec9d;
        color:#55720d;
    }

    .of-doc-toggle.offer.active{
        background:#eff6ff;
        border-color:#bfdbfe;
        color:#74b2d4;
    }

    .of-doc-toggle.deal.active{
        background:#93c21c; 
        color:white;
    }

    

    .of-doc-switch-note{
        font-size:12px;
        line-height:1.65;
        color:#6b7280;
        padding:10px 12px;
        border:1px dashed #d1d5db;
        border-radius:14px;
        background:#fafafa;
    }

    .of-doc-switch-note.warning{
        background:#fffbeb;
        border-color:#fde68a;
        color:#92400e;
    }

    .of-doc-status-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        border:1px solid var(--of-line);
        background:#fff;
        color:#374151;
    }

    .of-doc-status-badge.offer{
        background:#eff6ff;
        border-color:#bfdbfe;
        color:#74b2d4;
    }

    .of-doc-status-badge.deal{
        background:#93c21c; 
        color:white;
    }

    .of-doc-status-badge.auftrag{
        background:#ecfdf5;
        
        color:#047857;
    }
    @media(max-width:1200px){
        .of-banner-main{
            flex-direction:column;
            align-items:stretch;
        }

        .of-actions{
            width:100%;
        }

        .of-banner-stats{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
    }

    @media(max-width:640px){
        .of-title{
            font-size:24px;
        }

        .of-banner-stats{
            grid-template-columns:1fr 1fr;
        }

        .of-banner-stat-value{
            font-size:17px;
        }

        .of-meta-row{
            gap:8px;
        }

        .of-meta-pill{
            padding:7px 10px;
            font-size:11px;
        }
    }


</style>

<style>
    .of-header-slim{
    margin:108px 0 16px;
    border-radius:20px;
    overflow:hidden;
}

.of-header-inner-slim{
    padding:16px 18px;
}

.of-banner-slim{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.of-banner-slim-main{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    flex-wrap:wrap;
}

.of-banner-slim-left{
    display:flex;
    align-items:flex-start;
    gap:14px;
    min-width:0;
    flex:1;
}

.of-icon-box-slim{
    width:52px;
    height:52px;
    border-radius:16px;
}

.of-head-content-slim{
    min-width:0;
    flex:1;
}

.of-title-row-slim{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:4px;
}

.of-title-slim{
    font-size:24px;
    line-height:1.05;
    margin:0;
}

.of-sub-slim{
    margin-top:0;
    font-size:13px;
    line-height:1.5;
}

.of-banner-inline-row{
    margin-top:10px;
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.of-doc-switch-slim{
    padding:5px;
    border-radius:14px;
    gap:6px;
}

.of-doc-toggle{
    min-width:104px;
    padding:9px 14px;
    font-size:12px;
    border-radius:11px;
}

.of-doc-toggle.offer.active{
    background:#eff6ff;
    border-color:#bfdbfe;
    color:#74b2d4;
}

 

 

.of-doc-status-badge{
    padding:6px 10px;
    font-size:11px;
    line-height:1;
}

.of-doc-status-badge.offer{
    background:#eff6ff;
    border-color:#bfdbfe;
    color:#74b2d4;
}
 

.of-meta-row-slim{
    margin-top:0;
    gap:8px;
}

.of-meta-row-slim .of-meta-pill{
    padding:7px 10px;
    font-size:11px;
}

.of-doc-switch-note-slim{
    margin-top:10px;
    padding:8px 10px;
    font-size:11px;
    line-height:1.55;
    border-radius:12px;
}

.of-presence-slim{
    margin-top:10px;
    padding:8px 10px;
    border-radius:14px;
}

.of-actions-slim{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

.of-actions-slim .of-btn{
    min-height:40px;
    padding:9px 12px;
    border-radius:11px;
    font-size:12px;
}

.of-banner-stats-slim{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:10px;
    padding-top:0;
    border-top:none;
}

.of-banner-stat-slim{
    padding:12px 14px;
    border-radius:14px;
}

.of-banner-stat-slim .of-banner-stat-label{
    font-size:10px;
}

.of-banner-stat-slim .of-banner-stat-value{
    margin-top:4px;
    font-size:17px;
    line-height:1.1;
}

.of-status-chip-pending{
    background:#fff7ed;
    color:#c2410c;
    border-color:#fdba74;
}

.of-status-chip-viewed{
    background:#ecfeff;
    color:#0f766e;
    border-color:#99f6e4;
}

.of-status-chip-revised{
    background:#f5f3ff;
    color:#6d28d9;
    border-color:#c4b5fd;
}

.of-status-chip-expired{
    background:#f3f4f6;
    color:#374151;
    border-color:#d1d5db;
}

.of-workflow-list-wrap{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.of-workflow-list-card{
    border:1px solid var(--of-line);
    border-radius:22px;
    background:#fff;
    box-shadow:var(--of-shadow-sm);
    overflow:hidden;
}

.of-workflow-list-head{
    padding:16px 18px;
    border-bottom:1px solid var(--of-line);
    background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
}

.of-workflow-list-head-left{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.of-workflow-list-title{
    font-size:15px;
    font-weight:900;
    color:#111827;
    margin:0;
}

.of-workflow-list-sub{
    font-size:12px;
    color:#6b7280;
    line-height:1.6;
    margin-top:4px;
}

.of-workflow-status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:7px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    border:1px solid var(--of-line);
    white-space:nowrap;
    line-height:1;
}

.of-workflow-status-pill.draft{
    background:#f3f4f6;
    color:#4b5563;
    border-color:#d1d5db;
}

.of-workflow-status-pill.pending{
    background:#fff7ed;
    color:#c2410c;
    border-color:#fdba74;
}

.of-workflow-status-pill.sent{
    background:#eff6ff;
    color:#74b2d4;
    border-color:#bfdbfe;
}

.of-workflow-status-pill.viewed{
    background:#ecfeff;
    color:#0f766e;
    border-color:#99f6e4;
}

.of-workflow-status-pill.negotiation{
    background:#fffbeb;
    color:#b45309;
    border-color:#fde68a;
}

.of-workflow-status-pill.revised{
    background:#f5f3ff;
    color:#6d28d9;
    border-color:#c4b5fd;
}

.of-workflow-status-pill.final{
    background:#ecfdf5;
    color:#047857; 
}

.of-workflow-status-pill.cancel{
    background:#fef2f2;
    color:#b91c1c;
    border-color:#fecaca;
}

.of-workflow-status-pill.expired{
    background:#f3f4f6;
    color:#374151;
    border-color:#d1d5db;
}

.of-workflow-list-body{
    padding:18px;
}

.of-workflow-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:16px;
}

@media(max-width:1100px){
    .of-workflow-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media(max-width:640px){
    .of-workflow-grid{
        grid-template-columns:1fr;
    }
}

.of-workflow-box{
    border:1px solid #eef2f7;
    border-radius:16px;
    background:#fafafa;
    padding:14px;
}

.of-workflow-box-label{
    font-size:10px;
    font-weight:900;
    color:#94a3b8;
    text-transform:uppercase;
    letter-spacing:.08em;
    margin-bottom:6px;
}

.of-workflow-box-value{
    font-size:13px;
    line-height:1.65;
    color:#111827;
    font-weight:800;
    word-break:break-word;
}

.of-workflow-note{
    border:1px dashed #dbe3ea;
    border-radius:16px;
    background:#fcfcfd;
    padding:14px 16px;
    font-size:12px;
    line-height:1.7;
    color:#6b7280;
    margin-bottom:16px;
}

.of-workflow-actions{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    padding-top:14px;
    border-top:1px solid #eef2f7;
}

.of-workflow-status-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.of-workflow-status-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:36px;
    padding:0 12px;
    border-radius:10px;
    border:1px solid var(--of-line);
    background:#fff;
    color:#111827;
    font-size:12px;
    font-weight:800;
    cursor:pointer;
    transition:var(--of-transition);
}

.of-workflow-status-btn:hover{
    background:#f8fafc;
    border-color:#cbd5e1;
}

.of-workflow-status-btn.active{
    background:var(--of-primary-soft);
    border-color:#d9ef9d;
    color:#55720d;
}

.of-workflow-status-btn[disabled]{
    opacity:.45;
    cursor:not-allowed;
    pointer-events:none;
}

@media(max-width:1200px){
    .of-banner-slim-main{
        flex-direction:column;
        align-items:stretch;
    }

    .of-actions-slim{
        width:100%;
    }
}

@media(max-width:760px){
    .of-header-inner-slim{
        padding:14px;
    }

    .of-banner-slim-left{
        gap:12px;
    }

    .of-icon-box-slim{
        width:46px;
        height:46px;
    }

    .of-title-slim{
        font-size:20px;
    }

    .of-banner-stats-slim{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .of-doc-toggle{
        min-width:96px;
        padding:8px 12px;
    }
}

@media(max-width:520px){
    .of-banner-stats-slim{
        grid-template-columns:1fr 1fr;
    }

    .of-actions-slim .of-btn{
        flex:1 1 auto;
    }
}

.of-stepper-wrap{
    display:flex;
    flex-direction:column;
    gap:1px;
    min-width:0;
}

.of-stepper-card{
    border:1px solid var(--of-line);
    border-radius:22px;
    background:#fff;
    box-shadow:var(--of-shadow-sm);
    overflow:hidden;
    min-width:0;
}

.of-stepper-head{
    padding:18px 20px 14px;
    border-bottom:1px solid var(--of-line);
    background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:14px;
    flex-wrap:wrap;
}

.of-stepper-title{
    margin:0;
    font-size:16px;
    font-weight:900;
    color:#111827;
}

.of-stepper-sub{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
    line-height:1.6;
}

.of-stepper-body{
    padding:18px 20px 20px;
    min-width:0;
    overflow:hidden;
}

.of-stepper{
    display:flex;
    flex-wrap:nowrap;
    gap:1px;
    align-items:center;
    width:max-content;
    min-width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    padding-bottom:6px;
    scrollbar-width:thin;
    -webkit-overflow-scrolling:touch;
}

.of-step{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:48px;
    padding:0 22px 0 18px;
    border:none;
    color:#fff;
    font-size:12px;
    font-weight:900;
    letter-spacing:.02em;
    text-transform:uppercase;
    cursor:pointer;
    transition:var(--of-transition);
    clip-path:polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%, 12px 50%);
    box-shadow:0 6px 18px rgba(15,23,42,.08);
    flex:0 0 auto;
    white-space:nowrap;
}

.of-step:first-child{
    clip-path:polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
    padding-left:14px;
}

.of-step-label{
    display:inline-flex;
    align-items:center;
    gap:8px;
    white-space:nowrap;
}

.of-step-index{
    width:22px;
    height:22px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.28);
    font-size:11px;
    font-weight:900;
    flex:0 0 auto;
}

.of-step-meta{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:12px;
    margin-top:18px;
}

@media(max-width:1100px){
    .of-step{
        min-height:44px;
        font-size:11px;
        padding:0 18px 0 15px;
    }

    .of-step-meta{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media(max-width:640px){
    .of-stepper{
        flex-direction:column;
        align-items:stretch;
    }

    .of-step,
    .of-step:first-child{
        width:100%;
        clip-path:none;
        border-radius:14px;
        padding:12px 14px;
        justify-content:flex-start;
    }
}

/* Stepper phase colors by progress state */
.of-step{
    background:#cfe09b;
    color:#3f4f18;
}

.of-step .of-step-index{
    background:rgba(255,255,255,.72);
    border:1px solid rgba(255,255,255,.9);
    color:#3f4f18;
}

.of-step.is-past{
    background:#74b2d4;
    color:#ffffff;
}

.of-step.is-past .of-step-index{
    background:rgba(255,255,255,.22);
    border-color:rgba(255,255,255,.35);
    color:#ffffff;
}

.of-step.is-current{
    background:#93c21c;
    color:#ffffff;
    outline:3px solid rgba(147,194,28,.18);
    transform:translateY(-1px);
    box-shadow:0 10px 24px rgba(147,194,28,.28);
}

.of-step.is-current .of-step-index{
    background:#ffffff;
    color:#6b8d12;
    border-color:#ffffff;
}

.of-step.is-future{
    background:#cfe09b;
    color:#4b5f1d;
}

.of-step.is-future .of-step-index{
    background:rgba(255,255,255,.8);
    border-color:rgba(255,255,255,.95);
    color:#4b5f1d;
}

.of-step:hover{
    transform:translateY(-1px);
    filter:brightness(.98);
}

.of-step[disabled]{
    cursor:default;
    opacity:1;
}

.of-stepper-title{
    margin:0;
    font-size:16px;
    font-weight:900;
    color:#111827;
}

.of-stepper-sub{
    margin-top:6px;
    font-size:12px;
    color:#6b7280;
    line-height:1.6;
}

.of-stepper-body{
    padding:18px 20px 20px;
}

.of-stepper{
    display:flex;
    flex-wrap:wrap;
    gap:1px;
    align-items:center;
}

.of-step{
    position:relative;
    display:flex;
    align-items:center;
    min-height:48px;
    padding:0 22px 0 18px;
    border:none;
    color:#fff;
    font-size:12px;
    font-weight:900;
    letter-spacing:.02em;
    text-transform:uppercase;
    cursor:pointer;
    transition:var(--of-transition);
    clip-path:polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%, 12px 50%);
    box-shadow:0 6px 18px rgba(15,23,42,.08);
}

.of-step:first-child{
    clip-path:polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
    padding-left:2px;
}

.of-step:hover{
    transform:translateY(-1px);
    filter:brightness(.98);
}

.of-step[disabled]{
    cursor:not-allowed;
    opacity:1;
}

.of-step-label{
    display:inline-flex;
    align-items:center;
    gap:8px;
    white-space:nowrap;
}

.of-step-index{
    width:22px;
    height:22px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.28);
    font-size:11px;
    font-weight:900;
    flex:0 0 auto;
}

 
.of-step-meta{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:12px;
    margin-top:18px;
}

@media(max-width:1100px){
    .of-stepper{
        gap:1px;
    }

    .of-step{
        min-height:44px;
        font-size:11px;
        padding:0 18px 0 15px;
    }

    .of-step-meta{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media(max-width:640px){
    .of-stepper{
        flex-direction:column;
        align-items:stretch;
    }

    .of-step,
    .of-step:first-child{
        width:100%;
        clip-path:none;
        border-radius:14px;
        padding:12px 14px;
        justify-content:flex-start;
    }

    .of-step-meta{
        grid-template-columns:1fr;
    }
}

/* Stepper phase colors by progress state */
.of-step{
    background:#cfe09b;
    color:#3f4f18;
}

.of-step .of-step-index{
    background:rgba(255,255,255,.72);
    border:1px solid rgba(255,255,255,.9);
    color:#3f4f18;
}

.of-step.is-past{
    background:#74b2d4;
    color:#ffffff;
}

.of-step.is-past .of-step-index{
    background:rgba(255,255,255,.22);
    border-color:rgba(255,255,255,.35);
    color:#ffffff;
}

.of-step.is-current{
    background:#93c21c;
    color:#ffffff;
    outline:3px solid rgba(147,194,28,.18);
    transform:translateY(-1px);
    box-shadow:0 10px 24px rgba(147,194,28,.28);
}

.of-step.is-current .of-step-index{
    background:#ffffff;
    color:#6b8d12;
    border-color:#ffffff;
}

.of-step.is-future{
    background:#cfe09b;
    color:#4b5f1d;
}

.of-step.is-future .of-step-index{
    background:rgba(255,255,255,.8);
    border-color:rgba(255,255,255,.95);
    color:#4b5f1d;
}

.of-step:hover{
    transform:translateY(-1px);
    filter:brightness(.98);
}

.of-step[disabled]{
    cursor:default;
    opacity:1;
}

/* ===== Compact workspace override ===== */
.of-wrap{
    max-width:1500px;
    padding:18px 62px 18px 18px;
}

.of-header.of-header-slim{
    margin:100px 0 12px;
    border-radius:16px;
}

.of-header-inner.of-header-inner-slim{
    padding:12px 14px;
}

.of-banner-slim{
    gap:10px;
}

.of-banner-slim-main{
    gap:12px;
}

.of-banner-slim-left{
    gap:10px;
}

.of-icon-box.of-icon-box-slim{
    width:42px;
    height:42px;
    border-radius:12px;
}

.of-icon-box.of-icon-box-slim svg{
    width:18px;
    height:18px;
}

.of-title.of-title-slim{
    font-size:18px;
    line-height:1.05;
}

.of-sub.of-sub-slim{
    font-size:12px;
    line-height:1.4;
}

.of-title-row.of-title-row-slim{
    gap:8px;
    margin-bottom:2px;
}

.of-status-chip,
.of-doc-status-badge{
    padding:5px 9px;
    font-size:10px;
}

.of-meta-row.of-meta-row-slim{
    gap:6px;
}

.of-meta-row.of-meta-row-slim .of-meta-pill{
    padding:5px 8px;
    font-size:10px;
    gap:6px;
}

.of-doc-switch.of-doc-switch-slim{
    padding:4px;
    gap:4px;
    border-radius:12px;
}

.of-doc-toggle{
    min-width:86px;
    padding:7px 10px;
    font-size:11px;
    border-radius:10px;
}

.of-doc-switch-note.of-doc-switch-note-slim{
    margin-top:8px;
    padding:7px 9px;
    font-size:10px;
    line-height:1.45;
}

.of-presence.of-presence-slim{
    margin-top:8px;
    padding:6px 8px;
    gap:8px;
    border-radius:12px;
}

.of-presence-label,
.of-presence-empty,
.of-presence-name{
    font-size:10px;
}

.of-presence-avatar-wrap,
.of-presence-avatar{
    width:24px;
    height:24px;
}

.of-presence-dot{
    width:8px;
    height:8px;
}

.of-actions.of-actions-slim{
    gap:6px;
}

.of-actions.of-actions-slim .of-btn{
    min-height:34px;
    padding:7px 10px;
    font-size:11px;
    border-radius:10px;
}

.of-actions.of-actions-slim .of-btn svg{
    width:14px;
    height:14px;
}

.of-banner-stats.of-banner-stats-slim{
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:8px;
}

.of-banner-stat.of-banner-stat-slim{
    padding:9px 10px;
    border-radius:12px;
}

.of-banner-stat.of-banner-stat-slim .of-banner-stat-label{
    font-size:9px;
    letter-spacing:.05em;
}

.of-banner-stat.of-banner-stat-slim .of-banner-stat-value{
    margin-top:3px;
    font-size:14px;
    line-height:1.05;
}

.of-shell-head{
    padding:12px 14px;
}

.of-tabs{
    gap:6px;
}

.of-tab{
    min-height:36px;
    padding:7px 10px;
    font-size:11px;
    border-radius:10px;
    gap:6px;
}

.of-tab-icon{
    width:14px;
    height:14px;
}

.of-tab-icon svg{
    width:14px;
    height:14px;
}

.of-tab-count{
    min-width:18px;
    height:18px;
    padding:0 5px;
    font-size:10px;
}

.of-shell-body{
    padding:14px;
}

.of-card-h{
    padding:12px 14px;
}

.of-card-title{
    font-size:14px;
}

.of-card-b{
    padding:14px;
}

.of-badge{
    padding:5px 8px;
    font-size:10px;
}

.of-btn{
    padding:8px 11px;
    font-size:11px;
    border-radius:10px;
}

.of-inline-actions{
    gap:6px;
}

.of-status-overview{
    gap:10px;
    margin-top:12px;
}

.of-status-card{
    padding:12px;
    border-radius:14px;
}

.of-status-name{
    font-size:11px;
}

.of-status-value{
    margin-top:5px;
    font-size:20px;
}

.of-stepper-head{
    padding:12px 14px 10px;
}

.of-stepper-title{
    font-size:14px;
}

.of-stepper-sub{
    font-size:11px;
    line-height:1.45;
}

.of-stepper-body{
    padding:12px 14px 14px;
}

.of-step{
    min-height:38px;
    padding:0 14px 0 12px;
    font-size:10px;
}

.of-step:first-child{
    padding-left:10px;
}

.of-step-index{
    width:18px;
    height:18px;
    font-size:10px;
}

.of-step-meta{
    gap:8px;
    margin-top:12px;
}

.of-workflow-box{
    padding:10px;
    border-radius:12px;
}

.of-workflow-box-label{
    font-size:9px;
}

.of-workflow-box-value{
    font-size:11px;
    line-height:1.45;
}

.of-table th,
.of-table td{
    padding:9px 10px;
    font-size:12px;
}

@media (max-width: 1100px){
    .of-banner-stats.of-banner-stats-slim{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .of-step-meta{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media (max-width: 640px){
    .of-wrap{
        padding:12px;
    }

    .of-title.of-title-slim{
        font-size:16px;
    }

    .of-banner-stats.of-banner-stats-slim{
        grid-template-columns:1fr 1fr;
    }

    .of-step,
    .of-step:first-child{
        min-height:34px;
        padding:10px 12px;
        font-size:10px;
    }
}
</style>
@endpush
@endonce

@section('content')
<div
    class="of-wrap"
    id="folder-app"
    data-folder-id="{{ $folder->id }}"
    data-data-url="{{ route('admin.offers.folders.data', $folder) }}"
    data-document-status-url="{{ route('admin.offers.folders.document-status', $folder) }}"
    data-offer-id="{{ $offer?->id }}"
    data-offer-destroy-url="{{ $offer ? route('admin.offers.destroy', $offer->id) : '' }}"
    data-material-comparison-url="{{ route('admin.offers.folders.material-comparison', $folder) }}"
    data-material-status-url="{{ route('admin.offers.folders.material-order-status', $folder) }}"
    data-material-change-url="{{ route('admin.offers.folders.material-change', $folder) }}"
    data-kanban-move-url="{{ route('admin.offers.folders.kanban.move', $folder) }}"
    data-agb-save-url="{{ route('admin.offers.folders.agb.save', $folder) }}"
    data-attachments-upload-url="{{ route('admin.offers.folders.attachments.upload', $folder) }}"
    data-attachments-sort-url="{{ route('admin.offers.folders.attachments.sort', $folder) }}"
    data-material-final-url="{{ route('admin.offers.folders.material-final-status', $folder) }}"
    data-presence-channel="offer-folder.{{ $folder->id }}"
    data-default-avatar="{{ asset('images/gender/male.png') }}"
>
    <div class="of-header of-header-slim">
        <div class="of-header-inner of-header-inner-slim">
            <div class="of-banner-slim">
                <div class="of-banner-slim-main">
                    <div class="of-banner-slim-left">
                        <div class="of-icon-box of-icon-box-slim">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#6b8d12" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"></path>
                                <path d="M14 3v5h5"></path>
                                <path d="M9 13h6"></path>
                                <path d="M9 17h6"></path>
                                <path d="M9 9h2"></path>
                            </svg>
                        </div>

                        <div class="of-head-content of-head-content-slim">
                            <div class="of-title-row of-title-row-slim">
                                <h1 class="of-title of-title-slim">{{ $folder->name ?: 'Ordner' }}</h1>

                               @php
                                    $workflowStatus = $folder->workflow_status ?? ($detail?->document_status === 'deal'
                                        ? ($folder->deal_status ?? 'open')
                                        : ($folder->offer_status ?? 'draft'));

                                    $workflowStatusLabel = $folder->workflow_status_label ?? ($detail?->document_status === 'deal'
                                        ? ($folder->deal_status_label ?? 'Offen')
                                        : ($folder->offer_status_label ?? 'Entwurf'));

                                    $statusChipClass = 'secondary';

                                    if (($detail?->document_status ?? 'offer') === 'deal') {
                                        $statusChipClass = match($workflowStatus) {
                                            'open' => 'draft',
                                            'qualified' => 'sent',
                                            'proposal' => 'viewed',
                                            'negotiation' => 'negotiation',
                                            'won' => 'final',
                                            'lost' => 'cancel',
                                            'on_hold' => 'pending',
                                            default => 'draft',
                                        };
                                    } else {
                                        $statusChipClass = match($workflowStatus) {
                                            'draft' => 'draft',
                                            'pending_approval' => 'pending',
                                            'sent' => 'sent',
                                            'viewed' => 'viewed',
                                            'negotiation' => 'negotiation',
                                            'revised' => 'revised',
                                            'accepted' => 'final',
                                            'rejected' => 'cancel',
                                            'expired' => 'expired',
                                            'cancelled' => 'cancel',
                                            default => 'draft',
                                        };
                                    }
                                @endphp

                                <span class="of-status-chip of-status-chip-{{ $statusChipClass }}" id="workflow-status-chip">
                                    <span id="workflow-status-label">{{ $workflowStatusLabel }}</span>
                                </span>

                               <span
                                    class="of-doc-status-badge {{ ($detail?->document_status === 'deal') ? 'deal' : 'offer' }}"
                                    id="document-status-badge"
                                >
                                    <span id="document-status-badge-label">
                                        {{ ($detail?->document_status === 'deal') ? 'Auftrag' : 'Angebot' }}
                                    </span>
                                </span>
                            </div>

                            <div class="of-sub of-sub-slim">
                                Angebot #{{ $offer?->id ?? $folder->offer_id ?? '-' }}
                                · Kunde: {{ $customerName ?: 'Unbekannt' }}
                                · Produkt: {{ $offer?->product?->article_group ?? 'Unbekannt' }}
                            </div>

                            <div class="of-banner-inline-row">
                                <div class="of-doc-switch of-doc-switch-slim" id="document-status-switch">
                                    <button
                                        type="button"
                                        class="of-doc-toggle offer {{ ($detail?->document_status ?? 'offer') === 'offer' ? 'active' : '' }}"
                                        data-doc-status="offer"
                                    >
                                        Angebot
                                    </button>

                                    <button
                                        type="button"
                                        class="of-doc-toggle deal {{ ($detail?->document_status ?? 'offer') === 'deal' ? 'active' : '' }}"
                                        data-doc-status="deal"
                                    >
                                        Auftrag
                                    </button>
                                </div>

                                <div class="of-meta-row of-meta-row-slim">
                                    <span class="of-meta-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        {{ $employeeName ?: 'Nicht zugewiesen' }}
                                    </span>

                                    <span class="of-meta-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                            <path d="M16 2v4M8 2v4M3 10h18"></path>
                                        </svg>
                                        {{ optional($detail?->created_at ?? $folder->created_at)->format('d.m.Y H:i') }}
                                    </span>

                                    <span class="of-meta-pill">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                        </svg>
                                        {{ optional($detail?->updated_at ?? $folder->updated_at)->format('d.m.Y H:i') }}
                                    </span>
                                </div>
                            </div>

                            <div class="of-doc-switch-note of-doc-switch-note-slim" id="document-status-note">
                                Im Status <strong>Angebot</strong> und <strong>Auftrag</strong> sind Änderungen an Material, Bezug, Lager, Bestellen, Offen und Kommissionen erlaubt.
                            </div>

                            <div class="of-presence of-presence-compact of-presence-slim">
                                <div class="of-presence-label">
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    Aktuell im Ordner
                                </div>

                                <div class="of-presence-list" id="presence-users">
                                    <div class="of-presence-empty">Keine weiteren Benutzer sichtbar.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="of-actions of-actions-slim">
                        <a href="{{ $wizardUrl }}" class="of-btn" id="btn-load-offer">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h6"></path>
                                <path d="M15 12h6"></path>
                                <path d="M12 3v6"></path>
                                <path d="M12 15v6"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            @if($detail && $detail->document_status === 'deal')
                                Auftrag laden
                            @elseif($detail)
                                Angebot laden
                            @else
                                Neu erstellen
                            @endif
                        </a>

                        @if($offer?->id)
                            <button type="button" class="of-btn danger" onclick="deleteOffer()">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4h8v2"></path>
                                    <path d="M19 6l-1 14H6L5 6"></path>
                                </svg>
                                Löschen
                            </button>
                        @endif

                        <a href="{{ route('admin.offers.index') }}" class="of-btn soft">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"></path>
                            </svg>
                            Zurück
                        </a>
                    </div>
                </div>

                <div class="of-banner-stats of-banner-stats-slim">
                    <div class="of-banner-stat of-banner-stat-slim">
                        <div class="of-banner-stat-label">Netto</div>
                        <div class="of-banner-stat-value" id="stat-total-net">
                            {{ number_format((float) ($detail?->total_net ?? 0), 2, ',', '.') }} €
                        </div>
                    </div>

                    <div class="of-banner-stat of-banner-stat-slim">
                        <div class="of-banner-stat-label">Steuer</div>
                        <div class="of-banner-stat-value" id="stat-tax-rate">
                            {{ number_format((float) ($detail?->tax_rate ?? 19), 2, ',', '.') }} %
                        </div>
                    </div>

                    <div class="of-banner-stat of-banner-stat-slim">
                        <div class="of-banner-stat-label">Brutto</div>
                        <div class="of-banner-stat-value" id="stat-total-gross">
                            {{ number_format((float) ($detail?->total_gross ?? 0), 2, ',', '.') }} €
                        </div>
                    </div>

                    <div class="of-banner-stat of-banner-stat-slim">
                        <div class="of-banner-stat-label">Einträge</div>
                        <div class="of-banner-stat-value" id="stat-items-count">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-shell">
        <div class="of-shell-head">
            <div class="of-tabs">
                <button type="button" class="of-tab active" data-tab="uebersicht">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 13h8V3H3z"></path>
                            <path d="M13 21h8v-6h-8z"></path>
                            <path d="M13 3h8v8h-8z"></path>
                            <path d="M3 21h8v-4H3z"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Übersicht
                        <span class="of-tab-count" id="tab-count-uebersicht">1</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="kanban">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="6" height="16" rx="1"></rect>
                            <rect x="15" y="4" width="6" height="10" rx="1"></rect>
                            <rect x="9" y="4" width="6" height="6" rx="1"></rect>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Kanban
                        <span class="of-tab-count" id="tab-count-kanban">1</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="material">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.3 7l8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Materialliste
                        <span class="of-tab-count" id="tab-count-material">0</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="labor">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.3-3.3a1 1 0 0 0 0-1.4L19.4 3a1 1 0 0 0-1.4 0z"></path>
                            <path d="m16 2 6 6"></path>
                            <path d="M8.7 15.3 3 21l5.7-5.7"></path>
                            <path d="m14 7-8 8"></path>
                            <path d="m5 14 5 5"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Lohnliste
                        <span class="of-tab-count" id="tab-count-labor">0</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="material-print">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Materialdruck
                        <span class="of-tab-count" id="tab-count-material-print">0</span>
                    </span>
                </button> 

                <button type="button" class="of-tab" data-tab="print-files">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Druckdateien
                        <span class="of-tab-count" id="tab-count-print-files">0</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="agb">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M8 13h8"></path>
                            <path d="M8 17h8"></path>
                            <path d="M8 9h3"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        AGB
                        <span class="of-tab-count" id="tab-count-agb">1</span>
                    </span>
                </button>

                <button type="button" class="of-tab" data-tab="historie">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8v4l3 3"></path>
                            <path d="M3.05 11a9 9 0 1 1 .5 4"></path>
                            <path d="M3 16v5h5"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Historie
                        <span class="of-tab-count" id="tab-count-historie">0</span>
                    </span>
                </button>
            </div>
        </div>

        <div class="of-shell-body">
            <div class="of-panel active" id="panel-uebersicht">
                <div class="of-grid-2">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Angebotsdaten</h3>
                        </div>

                        <div class="of-card-b">
                            <div class="of-info-list">
                                <div class="of-info-item">
                                    <div class="of-info-key">Firma</div>
                                    <div class="of-info-val" id="info-company-name">{{ $detail?->company_name ?: '-' }}</div>
                                </div>

                                <div class="of-info-item">
                                    <div class="of-info-key">Markenmodus</div>
                                    <div class="of-info-val" id="info-brand-mode">{{ $detail?->brand_mode ?: 'Text' }}</div>
                                </div>

                                <div class="of-info-item">
                                    <div class="of-info-key">Markenfarbe</div>
                                    <div class="of-info-val" id="info-brand-color">{{ $detail?->brand_color ?: '-' }}</div>
                                </div>

                                <div class="of-info-item">
                                    <div class="of-info-key">Logo-URL</div>
                                    <div class="of-info-val" id="info-brand-logo">{{ $detail?->brand_logo_url ?: '-' }}</div>
                                </div>

                                <div class="of-info-item">
                                    <div class="of-info-key">Sektionen</div>
                                    <div class="of-info-val" id="info-sections-count">{{ count($initialSections) }}</div>
                                </div>

                                <div class="of-info-item">
                                    <div class="of-info-key">Platzierte Bilder</div>
                                    <div class="of-info-val" id="info-images-count">{{ count($initialPlacedImages) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Decktext</h3>
                        </div>

                        <div class="of-card-b">
                            <div class="of-cover {{ blank($detail?->cover_text_html) && blank($detail?->cover_text) ? 'empty' : '' }}" id="cover-box">
                                @if(!blank($detail?->cover_text_html))
                                    {!! $detail->cover_text_html !!}
                                @elseif(!blank($detail?->cover_text))
                                    {!! nl2br(e($detail->cover_text)) !!}
                                @else
                                    Kein Decktext vorhanden.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-status-overview" style="display:none;">
                    <div class="of-status-card">
                        <div class="of-status-name">Entwurf</div>
                        <div class="of-status-value" id="status-card-draft">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Gesendet</div>
                        <div class="of-status-value" id="status-card-sent">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Verhandlung</div>
                        <div class="of-status-value" id="status-card-negotiation">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Abgeschlossen</div>
                        <div class="of-status-value" id="status-card-final">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Storniert</div>
                        <div class="of-status-value" id="status-card-cancel">0</div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-kanban">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">Workflow Liste</h3>

                        <div class="of-inline-actions">
                            <span class="of-badge" id="kanban-list-badge">1 Eintrag</span>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div id="kanban-columns">
                            <div class="of-empty">Lade Workflow...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-material">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">Materialliste</h3>

                        <div class="of-inline-actions">
                            <span class="of-badge" id="material-count-badge">0 Positionen</span>

                            <button type="button" class="of-btn soft" id="material-compare-btn" onclick="openMaterialComparisonModal()">
                                Preisvergleich
                            </button>

                            <button type="button" class="of-btn soft" onclick="switchTab('material-print')">
                                Materialdruck öffnen
                            </button>
                        </div>
                    </div>

                   <div class="of-card-b">
                        <div class="of-tabs" style="margin-bottom:14px;">
                            <button type="button" class="of-tab active material-subtab-btn" data-material-filter="all">
                                Alle <span class="of-tab-count" id="mat-subcount-all">0</span>
                            </button>
                            <button type="button" class="of-tab material-subtab-btn" data-material-filter="offen">
                                Offen <span class="of-tab-count" id="mat-subcount-offen">0</span>
                            </button>
                            <button type="button" class="of-tab material-subtab-btn" data-material-filter="lager">
                                Lager <span class="of-tab-count" id="mat-subcount-lager">0</span>
                            </button>
                            <button type="button" class="of-tab material-subtab-btn" data-material-filter="bestellen">
                                Bestellen <span class="of-tab-count" id="mat-subcount-bestellen">0</span>
                            </button>

                            <button type="button" class="of-tab material-subtab-btn" data-material-filter="final">
                                Kommissionen Materialliste <span class="of-tab-count" id="mat-subcount-final">0</span>
                            </button>
                        </div>

                        <div id="material-list-wrap">
                            <div class="of-empty">Lade Materialliste...</div>
                        </div>
                    </div>

                    <div id="smart-material-sidebar" class="of-smart-side">
                        <div class="of-smart-card">
                            <div class="of-smart-head">
                                <div class="of-smart-head-row">
                                    <div style="display:flex; gap:12px; min-width:0;">
                                        <div class="of-smart-icon">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#6b8d12" stroke-width="2">
                                                <path d="M12 3v18"></path>
                                                <path d="M17 8l-5-5-5 5"></path>
                                                <path d="M17 16l-5 5-5-5"></path>
                                            </svg>
                                        </div>
                                        <div style="min-width:0;">
                                            <h3 class="of-smart-title">Günstigste Alternative</h3>
                                            <div class="of-smart-sub">
                                                Zeigt den besten Preis für die aktuell ausgewählten Materialpositionen.
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="of-smart-close" onclick="hideSmartMaterialSidebar()">×</button>
                                </div>
                            </div>

                            <div class="of-smart-body" id="smart-material-sidebar-body">
                                <div class="of-smart-empty">Bitte Materialpositionen auswählen.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-labor">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">Lohnliste</h3>
                        <div class="of-inline-actions">
                            <span class="of-badge" id="labor-count-badge">0 Lohnzeilen</span>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div id="labor-list-wrap">
                            <div class="of-empty">Lade Lohnliste...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-material-print">
                <div class="of-print-sheet">
                    <div class="of-print-head">
                        <div>
                            <h3 class="of-print-title">Materialübersicht</h3>
                            <div class="of-print-sub">
                                Angebot #{{ $offer?->id ?? $folder->offer_id ?? '-' }}
                                · Kunde: {{ $customerName ?: 'Unbekannt' }}
                                · Ordner: {{ $folder->name ?: 'Ordner' }}
                            </div>
                        </div>

                        <div class="of-inline-actions of-no-print">
                            <button type="button" class="of-btn" onclick="printMaterialSheet()">
                                Material drucken
                            </button>
                        </div>
                    </div>

                    <div class="of-print-meta">
                        <div class="of-print-stat">
                            <div class="of-print-stat-label">Materialpositionen</div>
                            <div class="of-print-stat-value" id="print-material-count">0</div>
                        </div>

                        <div class="of-print-stat">
                            <div class="of-print-stat-label">Gesamtmenge</div>
                            <div class="of-print-stat-value" id="print-material-qty-total">0,00</div>
                        </div>

                        <div class="of-print-stat">
                            <div class="of-print-stat-label">Firma</div>
                            <div class="of-print-stat-value" id="print-company-name">{{ $detail?->company_name ?: '-' }}</div>
                        </div>

                        <div class="of-print-stat">
                            <div class="of-print-stat-label">Datum</div>
                            <div class="of-print-stat-value">{{ now()->format('d.m.Y') }}</div>
                        </div>
                    </div>

                    <div class="of-print-body">
                        <div id="material-print-wrap">
                            <div class="of-empty">Lade Druckansicht...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="of-panel" id="panel-print-files">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">PDF- und Bilddateien für den Druck</h3>
                        <div class="of-inline-actions">
                            <span class="of-badge" id="print-files-count-badge">0 Dateien</span>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div class="of-card" style="margin-bottom:16px;">
                            <div class="of-card-b">
                                <div
                                    id="attachment-dropzone"
                                    style="border:2px dashed #cbd5e1; border-radius:16px; padding:22px; background:#f8fafc; text-align:center;"
                                >
                                    <div style="font-weight:900; color:#111827; margin-bottom:8px;">Dateien hier hineinziehen</div>
                                    <div class="of-sub" style="margin:0 0 14px 0;">PDF, JPG, JPEG, PNG, WEBP</div>

                                    <form id="print-files-upload-form">
                                        <input
                                            type="file"
                                            id="print-files-input"
                                            name="files[]"
                                            multiple
                                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                                            style="display:none;"
                                        >
                                        <button type="button" class="of-btn soft" id="pick-files-btn">Dateien auswählen</button>
                                        <button type="submit" class="of-btn">Hochladen</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
                            <input
                                type="text"
                                id="attachment-search-input"
                                placeholder="Dateien suchen ..."
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700;"
                            >
                        </div>

                        <div id="print-files-list-wrap">
                            <div class="of-empty">Keine Druckdateien vorhanden.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-agb">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">AGB für dieses Angebot</h3>
                        <div class="of-inline-actions">
                            <button type="button" class="of-btn" onclick="saveAgbForFolder()">AGB speichern</button>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div class="of-info-list" style="gap:16px;">
                            <div>
                                <label class="of-info-key" style="display:block; margin-bottom:8px;">Titel</label>
                                <input
                                    type="text"
                                    id="agb-title-input"
                                    value="{{ $resolvedAgbTitle }}"
                                    style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700;"
                                >
                            </div>

                            <div>
                                <label class="of-info-key" style="display:block; margin-bottom:8px;">AGB Text</label>
                                <input type="hidden" id="agb-text-input" value="{{ e($resolvedAgbText) }}">
                                <div id="agb-editor" style="background:#fff; border-radius:12px; overflow:hidden;">
                                    {!! $resolvedAgbText !!}
                                </div>
                            </div>

                            <div class="of-sub">
                                Dieser AGB-Text ist nur für diesen Angebotsordner gespeichert.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 

            <div class="of-panel" id="panel-historie">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">Historie</h3>
                        <div class="of-inline-actions">
                            <span class="of-badge" id="history-count-badge">0 Einträge</span>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div id="history-list-wrap">
                            <div class="of-empty">Lade Historie...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="material-comparison-modal" style="display:none;">
    <div class="of-modal">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" style="margin:0;">Material Preisvergleich</h3>
                <div class="of-sub" style="margin-top:6px;">Vergleich der ausgewählten Produkte über alle verfügbaren Distributoren.</div>
            </div>

            <button type="button" class="of-btn soft" onclick="closeMaterialComparisonModal()">Schließen</button>
        </div>

        <div class="of-modal-body" id="material-comparison-body">
            <div class="of-empty">Keine Daten geladen.</div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop of-material-details-modal" id="material-detail-modal" style="display:none;">
    <div class="of-modal">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" id="material-detail-title" style="margin:0;">Materialdetails</h3>
                <div class="of-sub" id="material-detail-sub" style="margin-top:6px;">Preisvergleich und Historie</div>
            </div>

            <div class="of-inline-actions">
                <button type="button" class="of-btn soft" onclick="closeMaterialDetailModal()">Schließen</button>
            </div>
        </div>

        <div class="of-modal-body">
            <div class="of-modal-tabs">
                <button type="button" class="of-modal-tab active" data-material-modal-tab="vergleich">
                    Preisvergleich
                </button>

                <button type="button" class="of-modal-tab" data-material-modal-tab="historie">
                    Historie
                </button>
            </div>

            <div class="of-modal-tab-panel active" id="material-modal-panel-vergleich">
                <div id="material-detail-compare-body">
                    <div class="of-empty">Lade Vergleich...</div>
                </div>
            </div>

            <div class="of-modal-tab-panel" id="material-modal-panel-historie">
                <div id="material-detail-history-body">
                    <div class="of-empty">Lade Historie...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="status-reason-modal" style="display:none; z-index:10060;">
    <div class="of-modal" style="width:min(560px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" style="margin:0;">Statusänderung bestätigen</h3>
                <div class="of-sub" id="status-reason-sub" style="margin-top:6px;">
                    Bitte geben Sie den Grund für die Statusänderung an.
                </div>
            </div>

            <button type="button" class="of-btn soft" onclick="closeStatusReasonModal()">Schließen</button>
        </div>

        <div class="of-modal-body">
            <div style="display:flex; flex-direction:column; gap:14px;">
                <div>
                    <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                        Neuer Status
                    </label>
                    <input
                        type="text"
                        id="status-reason-status-label"
                        readonly
                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;"
                    >
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                        Grund *
                    </label>
                    <textarea
                        id="status-reason-text"
                        rows="5"
                        placeholder="Bitte schreiben Sie hier den Grund für die Statusänderung..."
                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:140px;"
                    ></textarea>
                </div>

                <div id="status-reason-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                    Bitte geben Sie einen Grund ein.
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="of-btn soft" onclick="closeStatusReasonModal()">Abbrechen</button>
                    <button type="button" class="of-btn" id="status-reason-confirm-btn">Status ändern</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="material-move-modal" style="display:none; z-index:10070;">
    <div class="of-modal" style="width:min(680px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" id="material-move-modal-title" style="margin:0;">Menge verschieben</h3>
                <div class="of-sub" id="material-move-modal-sub" style="margin-top:6px;">
                    Bitte geben Sie die Menge an, die verschoben werden soll.
                </div>
            </div>

            <button type="button" class="of-btn soft" onclick="closeMaterialMoveModal()">Schließen</button>
        </div>

        <div class="of-modal-body">
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div id="material-move-modal-summary" class="of-smart-list">
                    <div class="of-smart-list-head">Auswahl</div>
                    <div class="of-smart-list-body" style="padding:14px;">
                        Keine Auswahl vorhanden.
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Zielstatus
                        </label>
                        <input
                            type="text"
                            id="material-move-target-label"
                            readonly
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;"
                        >
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Menge *
                        </label>
                        <input
                            type="number"
                            id="material-move-qty"
                            min="0.0001"
                            step="0.0001"
                            placeholder="z. B. 10"
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800;"
                        >
                    </div>
                </div>

                <div class="of-sub">
                    Hinweis: Bei mehreren markierten Positionen wird die eingegebene Menge auf **jede ausgewählte Position** angewendet.
                </div>

                <div id="material-move-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                    Bitte eine gültige Menge größer als 0 eingeben.
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="of-btn soft" onclick="closeMaterialMoveModal()">Abbrechen</button>
                    <button type="button" class="of-btn" id="material-move-confirm-btn">Verschieben</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="material-final-modal" style="display:none; z-index:10080;">
    <div class="of-modal" style="width:min(760px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" id="material-final-modal-title" style="margin:0;">
                    Kommissionen Materialliste bestätigen
                </h3>
                <div class="of-sub" id="material-final-modal-sub" style="margin-top:6px;">
                    Bitte bestätigen Sie die finale Menge.
                </div>
            </div>

            <button type="button" class="of-btn soft" onclick="closeMaterialFinalModal()">Schließen</button>
        </div>

        <div class="of-modal-body">
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div id="material-final-modal-summary" class="of-smart-list">
                    <div class="of-smart-list-head">Auswahl</div>
                    <div class="of-smart-list-body" style="padding:14px;">
                        Keine Auswahl vorhanden.
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Quellstatus
                        </label>
                        <input
                            type="text"
                            id="material-final-source-label"
                            readonly
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;"
                        >
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Verfügbar
                        </label>
                        <input
                            type="text"
                            id="material-final-available-label"
                            readonly
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;"
                        >
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Final-Menge *
                        </label>
                        <input
                            type="number"
                            id="material-final-qty"
                            min="0.0001"
                            step="0.0001"
                            placeholder="z. B. 10"
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800;"
                        >
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Restmenge verschieben nach *
                        </label>
                        <select
                            id="material-final-remaining-to"
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#fff;"
                        ></select>
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                        Grund *
                    </label>
                    <textarea
                        id="material-final-reason"
                        rows="4"
                        placeholder="z. B. Physisch bestätigt"
                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:120px;"
                    >Physisch bestätigt</textarea>
                </div>

                <div id="material-final-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                    Bitte prüfen Sie Menge, Reststatus und Grund.
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="of-btn soft" onclick="closeMaterialFinalModal()">Abbrechen</button>
                    <button type="button" class="of-btn" id="material-final-confirm-btn">Final bestätigen</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="document-status-modal" style="display:none; z-index:10090;">
    <div class="of-modal" style="width:min(720px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" id="document-status-modal-title" style="margin:0;">
                    Dokumentstatus ändern
                </h3>
                <div class="of-sub" id="document-status-modal-sub" style="margin-top:6px;">
                    Bitte bestätigen Sie die Änderung.
                </div>
            </div>

            <button type="button" class="of-btn soft" onclick="closeDocumentStatusModal()">Schließen</button>
        </div>

        <div class="of-modal-body">
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div
                    id="document-status-modal-warning"
                    class="of-doc-switch-note warning"
                    style="display:none;"
                ></div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                        Änderung
                    </label>
                    <input
                        type="text"
                        id="document-status-modal-change"
                        readonly
                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;"
                    >
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                        Grund *
                    </label>
                    <textarea
                        id="document-status-modal-reason"
                        rows="5"
                        placeholder="Bitte Grund für die Änderung eingeben ..."
                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:140px;"
                    ></textarea>
                </div>

                <div id="document-status-modal-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                    Bitte einen Grund eingeben.
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="of-btn soft" onclick="closeDocumentStatusModal()">Abbrechen</button>
                    <button type="button" class="of-btn" id="document-status-modal-confirm-btn">Ändern</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="of-modal-backdrop" id="clone-prompt-modal" style="display:none; z-index:10100;">
    <div class="of-modal" style="width:min(500px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" style="margin:0;">Angebot bearbeiten oder klonen?</h3>
                <div class="of-sub" style="margin-top:6px;">
                    Dieses Angebot wurde bereits bearbeitet / versendet. Möchten Sie das aktuelle Angebot weiter ändern oder lieber einen neuen Ordner als Kopie im selben Angebot erstellen?
                </div>
            </div>
            <button type="button" class="of-btn soft" onclick="document.getElementById('clone-prompt-modal').style.display='none'">Schließen</button>
        </div>
        <div class="of-modal-body">
            <p style="font-size:14px; margin-bottom: 20px; line-height: 1.6;">
                Möchten Sie das aktuelle Angebot weiter verändern oder für weitere Anpassungen eine Kopie <b>als neuen Ordner im selben Angebot</b> erstellen?
            </p>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <a href="{{ $wizardUrl }}" class="of-btn soft">Aktuelles ändern</a>
                <button type="button" class="of-btn" id="btn-confirm-clone">Klonen (Neu) - Empfohlen</button>
            </div>
        </div>
    </div>
</div>


<div class="of-modal-backdrop" id="version-prompt-modal" style="display:none; z-index:10100;">
    <div class="of-modal" style="width:min(500px,96vw);">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" style="margin:0;">Welche Version möchten Sie laden?</h3>
                <div class="of-sub" style="margin-top:6px;">
                    Dieses Dokument befindet sich bereits in der Auftragsphase (Deal).
                </div>
            </div>
            <button type="button" class="of-btn soft" onclick="document.getElementById('version-prompt-modal').style.display='none'">Schließen</button>
        </div>
        <div class="of-modal-body">
            <p style="font-size:14px; margin-bottom: 20px; line-height: 1.6;">
                Möchten Sie den aktuellen <b>Auftrag weiterbearbeiten</b> oder eine schreibgeschützte Momentaufnahme des <b>ursprünglichen Angebots ansehen</b>?
            </p>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="of-btn soft" id="btn-load-snapshot">Angebot ansehen (Read-Only)</button>
                <button type="button" class="of-btn" id="btn-load-current">Auftrag bearbeiten</button>
            </div>
        </div>
    </div>
</div>


<div id="of-toast-wrap" class="of-toast-wrap"></div>
@endsection

@once
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
 <script>
(() => {
    const folderApp = document.getElementById('folder-app');
    if (!folderApp) return;

   const OFFER_STATUS_KEYS = [
        'draft',
        'pending_approval',
        'sent',
        'viewed',
        'negotiation',
        'revised',
        'accepted',
        'rejected',
        'expired',
        'cancelled'
    ];

    const DEAL_STATUS_KEYS = [
        'open',
        'qualified',
        'proposal',
        'negotiation',
        'won',
        'lost',
        'on_hold'
    ];

    const OFFER_STATUS_LABELS = {
        draft: 'Entwurf',
        pending_approval: 'Wartet auf Freigabe',
        sent: 'Gesendet',
        viewed: 'Gesehen',
        negotiation: 'Verhandlung',
        revised: 'Überarbeitet',
        accepted: 'Akzeptiert',
        rejected: 'Abgelehnt',
        expired: 'Abgelaufen',
        cancelled: 'Storniert'
    };

    const DEAL_STATUS_LABELS = {
        open: 'Offen',
        qualified: 'Qualifiziert',
        proposal: 'Angebotsphase',
        negotiation: 'Verhandlung',
        won: 'Gewonnen',
        lost: 'Verloren',
        on_hold: 'Pausiert'
    };

    const DOCUMENT_STATUS_LABELS = {
        offer: 'Angebot', 
        deal: 'Auftrag'
    };

    function getWorkflowStatusKeys() {
        return getDocumentStatus() === 'deal'
            ? DEAL_STATUS_KEYS
            : OFFER_STATUS_KEYS;
    }

    function getWorkflowStatusLabels() {
        return getDocumentStatus() === 'deal'
            ? DEAL_STATUS_LABELS
            : OFFER_STATUS_LABELS;
    }

    function buildWorkflowStatusLabel(status) {
        const labels = getWorkflowStatusLabels();
        return labels[String(status || '').toLowerCase()] || status || '-';
    }

    function buildStatusLabel(status) {
        return buildWorkflowStatusLabel(status);
    }

    function getStatusVisualClass(status) {
        const key = String(status || '').toLowerCase();

        const map = {
            draft: 'draft',
            pending_approval: 'pending',
            sent: 'sent',
            viewed: 'viewed',
            negotiation: 'negotiation',
            revised: 'revised',
            accepted: 'final',
            rejected: 'cancel',
            expired: 'expired',
            cancelled: 'cancel',

            open: 'draft',
            qualified: 'sent',
            proposal: 'viewed',
            won: 'final',
            lost: 'cancel',
            on_hold: 'pending'
        };

        return map[key] || 'draft';
    }

    function getWorkflowStatus() {
        if (getDocumentStatus() === 'deal') {
            return String(state.folder?.deal_status || 'open').toLowerCase();
        }
        return String(state.folder?.offer_status || 'draft').toLowerCase();
    }

    function getDocumentStatus() {
        return String(state.detail?.document_status || 'offer').toLowerCase();
    }

    function getDocumentStatusLabel(status) {
        return DOCUMENT_STATUS_LABELS[String(status || 'offer').toLowerCase()] || 'Angebot';
    }

    function isExecutionDocumentStatus() {
        const status = getDocumentStatus();
        return status === 'offer' || status === 'deal';
    }

    const initialAttachments = @json($initialAttachments);

    const state = {
        folder: @json($folder),
        offer: @json($offer),
        detail: @json($detail),
        sections: [],
        distributors: {},
        currentTab: 'uebersicht',
        materialFilter: 'all',
        materialTableCols: {
            image: true,
            position: true,
            article_no: true,
            distributor_article_no: true,
            distributor: true,
            type: false,
            status: false,
            qty: true,
            qty_total: true,
            unit: true,
            ek_price: false,
            ek_total: false,
            unit_price: false,
            total: false,
            margin: false,
            db_total: false
        },
        materialMove: {
            rows: [],
            moveTo: null,
            mode: 'single'
        },

        materialFinal: {
            rows: [],
            sourceStatus: null,
            availableQty: 0
        },


        presenceUsers: [],
        comparisonCharts: [],
        attachments: Array.isArray(initialAttachments) ? initialAttachments : [],
        smartSidebar: {
            visible: false,
            summary: null
        },
        materialDetail: {
            rowIndex: null,
            rowData: null,
            comparison: null,
            selectedOption: null
        }
    };

    function getOfferWorkflowStatus() {
        return String(
            state.folder?.offer_status ||
            state.folder?.workflow_status ||
            state.offer?.offer_status ||
            state.offer?.status ||
            state.folder?.status ||
            'draft'
        ).toLowerCase();
    }

    function isOfferLockedByWorkflow() {
        const documentStatus = getDocumentStatus();
        const offerWorkflowStatus = getOfferWorkflowStatus();

        if (documentStatus !== 'offer') {
            return false;
        }

        return ['accepted', 'cancelled'].includes(offerWorkflowStatus);
    }

    function getOfferLockReason() {
        const status = getOfferWorkflowStatus();

        if (status === 'accepted') {
            return 'Dieses Angebot ist abgeschlossen und gesperrt, weil der Status auf „Akzeptiert“ steht.';
        }

        if (status === 'cancelled') {
            return 'Dieses Angebot ist gesperrt, weil der Status auf „Storniert“ steht.';
        }

        return '';
    }

    let documentStatusResolver = null;

    function openDocumentStatusModal(fromStatus, toStatus) {
        const modal = document.getElementById('document-status-modal');
        const title = document.getElementById('document-status-modal-title');
        const sub = document.getElementById('document-status-modal-sub');
        const change = document.getElementById('document-status-modal-change');
        const reason = document.getElementById('document-status-modal-reason');
        const warning = document.getElementById('document-status-modal-warning');
        const error = document.getElementById('document-status-modal-error');
        const confirmBtn = document.getElementById('document-status-modal-confirm-btn');

        if (!modal || !change || !reason || !confirmBtn) {
            return Promise.resolve(null);
        }

        const fromLabel = getDocumentStatusLabel(fromStatus);
        const toLabel = getDocumentStatusLabel(toStatus);

        title.textContent = 'Dokumentstatus ändern';
        sub.textContent = `Bitte bestätigen Sie die Änderung von "${fromLabel}" auf "${toLabel}".`;
        change.value = `${fromLabel} → ${toLabel}`;
        reason.value = '';
        if (error) error.style.display = 'none';

        if (fromStatus !== 'offer' && toStatus === 'offer') {
            warning.style.display = 'block';
            warning.innerHTML = `
                Achtung: Beim Zurückwechseln auf <strong>Angebot</strong> wird der aktuelle Vorgang fachlich als
                <strong>storniert</strong> behandelt. Zusätzlich muss backend-seitig ein neues Angebot mit neuer ID
                erzeugt werden, wenn Sie wirklich eine neue Angebotsnummer möchten.
            `;
        } else {
            warning.style.display = 'none';
            warning.innerHTML = '';
        }

        modal.style.display = 'flex';

        return new Promise(resolve => {
            documentStatusResolver = resolve;

            confirmBtn.onclick = () => {
                const value = String(reason.value || '').trim();
                if (!value) {
                    if (error) error.style.display = 'block';
                    reason.focus();
                    return;
                }

                closeDocumentStatusModal({
                    from_status: fromStatus,
                    to_status: toStatus,
                    reason: value,
                    revert_to_offer: fromStatus !== 'offer' && toStatus === 'offer'
                });
            };
        });
    }

    function closeDocumentStatusModal(result = null) {
        const modal = document.getElementById('document-status-modal');
        const reason = document.getElementById('document-status-modal-reason');
        const error = document.getElementById('document-status-modal-error');

        if (modal) modal.style.display = 'none';
        if (reason) reason.value = '';
        if (error) error.style.display = 'none';

        if (typeof documentStatusResolver === 'function') {
            documentStatusResolver(result);
        }

        documentStatusResolver = null;
    }

    window.closeDocumentStatusModal = closeDocumentStatusModal;


    

   async function changeDocumentStatusRequest(targetStatus) {
        const currentStatus = getDocumentStatus();
        const url = folderApp.dataset.documentStatusUrl;

        if (!url) {
            showCustomToast('Fehler', 'Route für Dokumentstatus nicht gefunden.', 'error');
            return;
        }

        if (targetStatus === currentStatus) {
            return;
        }

        const modalResult = await openDocumentStatusModal(currentStatus, targetStatus);
        if (!modalResult) {
            renderDocumentStatusToggle();
            return;
        }

        try {
            const json = await fetchJson(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    document_status: modalResult.to_status,
                    reason: modalResult.reason,
                    revert_to_offer: modalResult.revert_to_offer ? 1 : 0
                })
            });

            if (!json.success) {
                throw new Error(json.message || 'Dokumentstatus konnte nicht geändert werden.');
            }

            // 🟢 IF BACKEND RETURNS A REDIRECT (BECAUSE IT CLONED), GO TO NEW CLONE
            if (json.redirect_url) {
                window.location.href = json.redirect_url;
                return;
            }

            await loadFolderData();

            showCustomToast(
                'Dokumentstatus geändert',
                `Status wurde auf "${getDocumentStatusLabel(modalResult.to_status)}" gesetzt.`
            );
        } catch (error) {
            renderDocumentStatusToggle();
            showCustomToast('Fehler', error.message || 'Dokumentstatus konnte nicht geändert werden.', 'error');
        }
    }


    function renderOfferLockState() {
        const locked = isOfferLockedByWorkflow();
        const reason = getOfferLockReason();

        const loadBtn = document.getElementById('btn-load-offer');
        const note = document.getElementById('document-status-note');

        // Angebot laden must stay usable
        if (loadBtn) {
            loadBtn.disabled = false;
            loadBtn.style.pointerEvents = '';
            loadBtn.style.opacity = '';
            loadBtn.title = '';
        }

        // document toggle can stay usable too unless you want only this blocked
        document.querySelectorAll('.of-doc-toggle').forEach(btn => {
            btn.disabled = false;
            btn.style.pointerEvents = '';
            btn.style.opacity = '';
            btn.title = '';
        });

        if (note) {
            if (locked) {
                note.className = 'of-doc-switch-note warning of-doc-switch-note-slim';
                note.innerHTML = `
                    <strong>Hinweis:</strong> ${esc(reason)}
                    Sie können den Ordner weiterhin öffnen, ansehen, klonen und Material-/Lohnlisten prüfen.
                `;
            } else {
                note.className = 'of-doc-switch-note of-doc-switch-note-slim';
                note.innerHTML = `
                    Im Status <strong>Angebot</strong> und <strong>Auftrag</strong> sind Änderungen an Material, Bezug, Lager, Bestellen, Offen und Kommissionen erlaubt.
                `;
            }
        }
    }
 
    function renderDocumentStatusToggle() {
        const status = getDocumentStatus();
        const badge = document.getElementById('document-status-badge');
        const badgeLabel = document.getElementById('document-status-badge-label');
        const note = document.getElementById('document-status-note');

        document.querySelectorAll('.of-doc-toggle').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.docStatus === status);
        });

        if (badge) {
            badge.classList.remove('offer', 'deal');
            badge.classList.add(status);
        }

        if (badgeLabel) {
            badgeLabel.textContent = getDocumentStatusLabel(status);
        }

        if (note) {
            if (status === 'offer') {
                note.className = 'of-doc-switch-note';
                note.innerHTML = `
                    Im Status <strong>Angebot</strong> sind Materialänderungen, Preisvergleich,
                    Lager / Bestellen / Offen und Kommissionen erlaubt.
                `;
            } else if (status === 'deal') {
                note.className = 'of-doc-switch-note';
                note.innerHTML = `
                    Im Status <strong>Auftrag</strong> sind Materialänderungen, Preisvergleich,
                    Lager / Bestellen / Offen und Kommissionen ebenfalls erlaubt.
                `;
            }
        }
        renderOfferLockState();
    }
    let agbQuill = null;

    function getRowTotalQty(row) {
        return Number(row.qty_total || row.qty || 0);
    }

    function getRowAllocation(row) {
        const total = getRowTotalQty(row);

        const raw = row.stock_allocation && typeof row.stock_allocation === 'object'
            ? row.stock_allocation
            : null;

        let allocation = {
            offen: 0,
            lager: 0,
            bestellen: 0,
            final: 0
        };

        if (raw) {
            allocation.offen = Number(raw.offen || 0);
            allocation.lager = Number(raw.lager || 0);
            allocation.bestellen = Number(raw.bestellen || 0);
            allocation.final = Number(raw.final || 0);
        } else {
            const status = String(row.order_status || 'offen').toLowerCase();
            if (allocation.hasOwnProperty(status)) {
                allocation[status] = total;
            } else {
                allocation.offen = total;
            }
        }

        const sum = allocation.offen + allocation.lager + allocation.bestellen + allocation.final;

        if (sum <= 0 && total > 0) {
            allocation.offen = total;
        }

        if (sum > total) {
            const factor = total / sum;
            allocation.offen *= factor;
            allocation.lager *= factor;
            allocation.bestellen *= factor;
            allocation.final *= factor;
        }

        const roundedSum = allocation.offen + allocation.lager + allocation.bestellen + allocation.final;
        const diff = total - roundedSum;

        if (Math.abs(diff) > 0.0001) {
            allocation.offen += diff;
        }

        return allocation;
    }

    function getRowQtyForFilter(row, filter) {
        const allocation = getRowAllocation(row);

        if (filter === 'all') {
            return getRowTotalQty(row);
        }

        return Number(allocation[filter] || 0);
    }

    function safeHistory(value) {
        if (Array.isArray(value)) return value;
        return [];
    }

    function normalizeMaterialHistoryEntries(rawHistory) {
        const list = Array.isArray(rawHistory) ? rawHistory : [];

        const statusLabelMap = {
            offen: 'Offen',
            lager: 'Lager',
            bestellen: 'Bestellen',
            final: 'Final',
            draft: 'Entwurf',
            sent: 'Gesendet',
            negotiation: 'Verhandlung',
            cancel: 'Storniert'
        };

        const normalizeStatus = (value) => {
            const key = String(value || '').trim().toLowerCase();
            return statusLabelMap[key] || (value ? String(value) : '-');
        };

        const normalizeType = (entry) => {
            const raw = String(
                entry?.type ||
                entry?.action ||
                entry?.event ||
                entry?.kind ||
                ''
            ).trim().toLowerCase();

            const map = {
                created: 'Erstellt',
                create: 'Erstellt',
                moved: 'Verschoben',
                move: 'Verschoben',
                status_changed: 'Status geändert',
                allocation_changed: 'Verteilung geändert',
                final_confirmed: 'Finale bestätigt',
                distributor_changed: 'Lieferant geändert',
                updated: 'Aktualisiert',
                update: 'Aktualisiert'
            };

            return map[raw] || (raw ? raw.replaceAll('_', ' ') : 'Änderung');
        };

        const normalizeUserName = (entry) => {
            const rawName =
                entry?.changed_by_name ||
                entry?.user_name ||
                entry?.employee_name ||
                entry?.changed_by?.name ||
                entry?.changed_by?.full_name ||
                entry?.creator_name ||
                entry?.by ||
                '';

            const first =
                entry?.changed_by?.name ||
                entry?.user?.name ||
                '';

            const last =
                entry?.changed_by?.lastname ||
                entry?.user?.lastname ||
                '';

            const combined = `${first} ${last}`.replace(/\s+/g, ' ').trim();

            const name = String(rawName || combined || '').trim();
            return name || 'Unbekannt';
        };

        return list
            .map((entry, index) => {
                const qty =
                    entry?.qty ??
                    entry?.move_qty ??
                    entry?.final_qty ??
                    entry?.quantity ??
                    entry?.amount ??
                    0;

                const fromRaw =
                    entry?.from_status ??
                    entry?.from ??
                    entry?.old_status ??
                    entry?.source_status ??
                    '';

                const toRaw =
                    entry?.to_status ??
                    entry?.to ??
                    entry?.new_status ??
                    entry?.target_status ??
                    '';

                return {
                    _index: index,
                    type_label: normalizeType(entry),
                    from_label: normalizeStatus(fromRaw),
                    to_label: normalizeStatus(toRaw),
                    from_raw: fromRaw || '',
                    to_raw: toRaw || '',
                    qty: Number(qty || 0),
                    reason: String(
                        entry?.reason ||
                        entry?.note ||
                        entry?.message ||
                        entry?.comment ||
                        ''
                    ).trim(),
                    changed_by_name: normalizeUserName(entry),
                    created_at:
                        entry?.created_at ||
                        entry?.changed_at ||
                        entry?.date ||
                        entry?.datetime ||
                        entry?.at ||
                        null,
                    raw: entry
                };
            })
            .sort((a, b) => {
                const aTime = a.created_at ? new Date(a.created_at).getTime() : 0;
                const bTime = b.created_at ? new Date(b.created_at).getTime() : 0;
                return bTime - aTime;
            });
    }

    function switchMaterialModalTab(tab) {
        document.querySelectorAll('[data-material-modal-tab]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.materialModalTab === tab);
        });

        document.querySelectorAll('.of-modal-tab-panel').forEach(panel => {
            panel.classList.toggle('active', panel.id === `material-modal-panel-${tab}`);
        });
    }

    function initMaterialModalTabs() {
        document.querySelectorAll('[data-material-modal-tab]').forEach(btn => {
            if (btn.dataset.ready === '1') return;

            btn.addEventListener('click', () => {
                switchMaterialModalTab(btn.dataset.materialModalTab || 'vergleich');
            });

            btn.dataset.ready = '1';
        });
    }

    function buildMaterialHistoryHtml(materialHistory) {
        if (!materialHistory.length) {
            return `<div class="of-empty">Keine Material-Historie vorhanden.</div>`;
        }

        return `
            <div class="of-history-inline-list">
                ${materialHistory.map(entry => `
                    <div class="of-history-inline-item">
                        <div class="of-history-inline-title">
                            ${esc(entry.type_label)}
                        </div>

                        <div class="of-history-inline-sub">
                            ${entry.from_raw ? `Von: <strong>${esc(entry.from_label)}</strong>` : 'Von: <strong>-</strong>'}
                            ${entry.to_raw ? ` · Nach: <strong>${esc(entry.to_label)}</strong>` : ''}
                            ${entry.qty > 0 ? ` · Menge: <strong>${esc(entry.qty.toFixed(2))}</strong>` : ''}
                            <br>
                            Grund: <strong>${esc(entry.reason || '-')}</strong>
                            <br>
                            Benutzer: <strong>${esc(entry.changed_by_name)}</strong>
                            <br>
                            Datum: <strong>${esc(formatDateTimeValue(entry.created_at))}</strong>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
      

    function normalizeHistoryEntries() {
        const folderHistory = safeHistory(state.folder?.history).map(entry => ({
            ...entry,
            __source: 'folder'
        }));

        const detailHistory = safeHistory(state.detail?.biography_data).map(entry => ({
            ...entry,
            __source: 'detail'
        }));

        const merged = [...folderHistory, ...detailHistory];

        return merged
            .map((entry, index) => {
                const rawChangedByName =
                    entry?.changed_by_name ??
                    entry?.user_name ??
                    entry?.employee_name ??
                    entry?.changed_by?.name ??
                    entry?.creator_name ??
                    '';

                const changedById =
                    entry?.changed_by_id ||
                    entry?.user_id ||
                    entry?.employee_id ||
                    entry?.changed_by?.id ||
                    null;

                let changedByName = String(rawChangedByName || '').trim();

                if (!changedByName || /^\d+$/.test(changedByName)) {
                    if (
                        Number(changedById) &&
                        Number(changedById) === Number(state.folder?.creator?.id)
                    ) {
                        changedByName = [
                            state.folder?.creator?.name || '',
                            state.folder?.creator?.lastname || ''
                        ].join(' ').replace(/\s+/g, ' ').trim();
                    }
                }

                if (!changedByName) {
                    changedByName = 'Unbekannt';
                }

                const fromStatus = normalisiereStatus(
                    entry?.from_status ||
                    entry?.old_status ||
                    entry?.previous_status ||
                    entry?.from ||
                    ''
                );

                const toStatus = normalisiereStatus(
                    entry?.to_status ||
                    entry?.new_status ||
                    entry?.status ||
                    entry?.to ||
                    state.folder?.status ||
                    'draft'
                );

                const reason =
                    entry?.reason ||
                    entry?.reason_text ||
                    entry?.note ||
                    entry?.message ||
                    '';

                const action =
                    entry?.action ||
                    entry?.type ||
                    (fromStatus && toStatus && fromStatus !== toStatus ? 'status_changed' : 'updated');

                const createdAt =
                    entry?.created_at ||
                    entry?.date ||
                    entry?.datetime ||
                    entry?.changed_at ||
                    entry?.at ||
                    null;

                return {
                    _index: index,
                    action,
                    from_status: fromStatus,
                    to_status: toStatus,
                    reason,
                    changed_by_name: changedByName,
                    changed_by_id: changedById,
                    created_at: createdAt,
                    source: entry.__source || null
                };
            })
            .sort((a, b) => {
                const aTime = a.created_at ? new Date(a.created_at).getTime() : 0;
                const bTime = b.created_at ? new Date(b.created_at).getTime() : 0;
                return bTime - aTime;
            });
    }

    function buildHistoryTitle(entry) {
        if (entry.action === 'document_status_changed') {
            return `Dokumentstatus geändert: ${getDocumentStatusLabel(entry.from_status || 'offer')} → ${getDocumentStatusLabel(entry.to_status || 'offer')}`;
        }

        if (entry.action === 'document_reverted_to_offer') {
            return 'Zurück auf Angebot';
        }

        if (entry.action === 'status_changed' || (entry.from_status && entry.to_status && entry.from_status !== entry.to_status)) {
            return `Status geändert: ${buildStatusLabel(entry.from_status || 'draft')} → ${buildStatusLabel(entry.to_status || 'draft')}`;
        }

        if (entry.action === 'folder_created') return 'Ordner erstellt';
        if (entry.action === 'folder_updated') return 'Ordner aktualisiert';
        if (entry.action === 'offer_loaded') return 'Angebot geladen';
        if (entry.action === 'material_changed') return 'Material geändert';
        if (entry.action === 'attachments_uploaded') return 'Dateien hochgeladen';
        if (entry.action === 'attachment_deleted') return 'Datei gelöscht';

        return 'Änderung gespeichert';
    }
    function renderHistory() {
        const wrap = document.getElementById('history-list-wrap');
        const badge = document.getElementById('history-count-badge');
        if (!wrap) return;

        const entries = normalizeHistoryEntries();

        if (badge) {
            badge.textContent = `${entries.length} Einträge`;
        }

        if (!entries.length) {
            wrap.innerHTML = `
                <div class="of-empty">
                    Noch keine Historie vorhanden.
                </div>
            `;
            renderTabCounts();
            return;
        }

        wrap.innerHTML = `
            <div class="of-history-list">
                ${entries.map(entry => `
                    <div class="of-history-item">
                        <div class="of-history-dot-wrap">
                            <div class="of-history-dot"></div>
                        </div>

                        <div class="of-history-card">
                            <div class="of-history-top">
                                <div class="of-history-title">
                                    ${esc(buildHistoryTitle(entry))}
                                </div>

                                <div class="of-history-date">
                                    ${esc(formatDateTimeValue(entry.created_at))}
                                </div>
                            </div>

                            <div class="of-history-meta">
                                <span class="of-history-badge">
                                    Benutzer: ${esc(entry.changed_by_name || 'Unbekannt')}
                                </span>

                                ${entry.to_status ? `
                                    <span class="of-history-badge">
                                        Neuer Status: ${esc(buildStatusLabel(entry.to_status))}
                                    </span>
                                ` : ''}

                                ${entry.from_status && entry.from_status !== entry.to_status ? `
                                    <span class="of-history-badge">
                                        Vorher: ${esc(buildStatusLabel(entry.from_status))}
                                    </span>
                                ` : ''}
                            </div>

                            <div class="of-history-text">
                                ${entry.reason && String(entry.reason).trim()
                                    ? esc(entry.reason)
                                    : 'Kein Grund hinterlegt.'}
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        renderTabCounts();
    }

    function esc(v) {
        return String(v ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatDateValue(value) {
        if (!value) return '-';

        try {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return '-';
        }
    }

    function formatDateTimeValue(value) {
        if (!value) return '-';

        try {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }) + ', ' + date.toLocaleTimeString('de-DE', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return '-';
        }
    }

    function getOfferCustomerLines() {
        const customer = state.offer?.customer || {};

        const line1 = [
            customer.firma || '',
            customer.name || '',
            customer.lastname || ''
        ].join(' ').replace(/\s+/g, ' ').trim();

        const line2 = customer.street || customer.address || '';
        const line3 = [
            customer.postal_code || customer.zip || '',
            customer.city || ''
        ].join(' ').trim();

        return [line1 || 'Unbekannt', line2, line3].filter(Boolean);
    }

    function getContactName() {
        const creator = state.folder?.creator || state.offer?.creator || {};
        const full = [
            creator.name || '',
            creator.lastname || ''
        ].join(' ').replace(/\s+/g, ' ').trim();

        return full || 'Nicht zugewiesen';
    }

    function getContactPhone() {
        const creator = state.folder?.creator || state.offer?.creator || {};
        return creator.phone || creator.tel || creator.mobile || '-';
    }

    function getContactEmail() {
        const creator = state.folder?.creator || state.offer?.creator || {};
        return creator.email || '-';
    }

    function getProductLabel() {
        return state.offer?.product?.article_group
            || state.offer?.product?.product
            || 'Unbekannt';
    }

    function getObjectLabel() {
        const alternative = state.offer?.alternative || {};
        const parts = [
            alternative.street || '',
            alternative.city || ''
        ].filter(Boolean);

        return parts.length ? parts.join(', ') : '-';
    }


    function safeArray(value) {
        return Array.isArray(value) ? value : [];
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function money(value) {
        const n = Number(value || 0);
        return new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(n) + ' €';
    }

    function getMaterialCols() {
        return state.materialTableCols || {};
    }

    function toggleMaterialColumn(col) {
        if (!state.materialTableCols || !(col in state.materialTableCols)) return;
        state.materialTableCols[col] = !state.materialTableCols[col];
        renderMaterialList();
    }

    function setMaterialColumnPreset(mode) {
        if (mode === 'standard') {
            state.materialTableCols = {
                image: true,
                position: true,
                article_no: true,
                distributor_article_no: true,
                distributor: true,
                type: false,
                status: false,
                qty: true,
                qty_total: true,
                unit: true,
                ek_price: false,
                ek_total: false,
                unit_price: false,
                total: false,
                margin: false,
                db_total: false
            };
        } else if (mode === 'all') {
            state.materialTableCols = {
                image: true,
                position: true, 
                article_no: true,
                distributor_article_no: true,
                distributor: true,
                type: true,
                status: true,
                qty: true,
                qty_total: true,
                unit: true,
                ek_price: true,
                ek_total: true,
                unit_price: true,
                total: true,
                margin: true,
                db_total: true
            };
        }

        renderMaterialList();
    }

    window.setMaterialColumnPreset = setMaterialColumnPreset;

    function getRowImage(row) {
        return row?.image || row?.img || row?.image_url || row?.product_image || row?.photo || '';
    }

    function materialPickerKeepOpen(event) {
        event.stopPropagation();
    }

    window.materialPickerKeepOpen = materialPickerKeepOpen;


    window.toggleMaterialColumn = toggleMaterialColumn;

    function materialColEnabled(col) {
        return !!getMaterialCols()[col];
    }

    function formatBytes(bytes) {
        const value = Number(bytes || 0);
        if (value < 1024) return `${value} B`;
        if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`;
        return `${(value / (1024 * 1024)).toFixed(2)} MB`;
    }

    function stripHtml(value) {
        const div = document.createElement('div');
        div.innerHTML = String(value || '');
        return (div.textContent || div.innerText || '').trim();
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
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

        const text = await response.text();
        let json = {};

        try {
            json = text ? JSON.parse(text) : {};
        } catch (error) {
            throw new Error(`Ungültige Server-Antwort (${response.status})`);
        }

        if (!response.ok) {
            throw new Error(json.message || `HTTP-Fehler ${response.status}`);
        }

        return json;
    }

    function showCustomToast(title, text, type = 'success') {
        const wrap = document.getElementById('of-toast-wrap');
        if (!wrap) return;

        const toast = document.createElement('div');
        toast.className = `of-toast ${type}`;
        toast.innerHTML = `
            <div class="of-toast-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6 9 17l-5-5"></path>
                </svg>
            </div>
            <div>
                <div class="of-toast-title">${esc(title)}</div>
                <div class="of-toast-text">${esc(text)}</div>
            </div>
        `;

        wrap.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            toast.style.transition = 'all .2s ease';
            setTimeout(() => toast.remove(), 220);
        }, 3200);
    }

    function normalisiereStatus(status) {
        const raw = String(status || '').trim().toLowerCase();

        const map = {
            draft: 'draft',
            entwurf: 'draft',

            sent: 'sent',
            gesendet: 'sent',

            negotiation: 'negotiation',
            verhandlung: 'negotiation',

            final: 'final',
            abgeschlossen: 'final',
            abgeschlosseen: 'final',

            cancel: 'cancel',
            storniert: 'cancel'
        };

        return map[raw] || 'draft';
    }

    function getFolderStatus() {
        return getWorkflowStatus();
    }

    function distributorName(distributorId) {
        if (!distributorId) return '-';
        return state.distributors?.[String(distributorId)] || state.distributors?.[Number(distributorId)] || `Lieferant #${distributorId}`;
    }

    function isContainerMaterialRow(row) {
        if (!row) return true;

        const itemType = String(row.item_type || '').toLowerCase();
        const level = String(row.level || '').toLowerCase();
        const hierarchyLevel = Number(row.hierarchy_level || 0);

        if (itemType === 'master_set') return true;
        if (itemType === 'section') return true;
        if (level.includes('hauptposition')) return true;

        if (hierarchyLevel <= 1) return true;

        return false;
    }

    function isStatusEditableMaterialRow(row) {
        if (!row) return false;
        if (isContainerMaterialRow(row)) return false;

        const hasProductId = Number(row.product_id || 0) > 0;
        const hasComponentId = Number(row.component_id || 0) > 0;
        const hasArticleNo = String(row.article_no || '').trim() !== '';

        return hasProductId || hasComponentId || hasArticleNo;
    }


    function buildCoverHtml(detail) {
        const html = detail?.cover_text_html;
        const text = detail?.cover_text;

        if (html && String(html).trim() !== '') {
            return { html, isEmpty: false };
        }

        if (text && String(text).trim() !== '') {
            return {
                html: esc(String(text)).replace(/\n/g, '<br>'),
                isEmpty: false
            };
        }

        return {
            html: 'Kein Decktext vorhanden.',
            isEmpty: true
        };
    }

   function switchTab(tab) {
        if (!tab) return;

        state.currentTab = tab;

        document.querySelectorAll('.of-tab[data-tab]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });

        document.querySelectorAll('.of-panel').forEach(panel => {
            panel.classList.toggle('active', panel.id === `panel-${tab}`);
        });
    }
    window.switchTab = switchTab;

    let statusReasonResolver = null;
    let pendingKanbanRevert = null;

    function openStatusReasonModal(newStatus, oldStatus) {
        const modal = document.getElementById('status-reason-modal');
        const label = document.getElementById('status-reason-status-label');
        const sub = document.getElementById('status-reason-sub');
        const text = document.getElementById('status-reason-text');
        const error = document.getElementById('status-reason-error');
        const confirmBtn = document.getElementById('status-reason-confirm-btn');

        if (!modal || !label || !text || !confirmBtn) {
            return Promise.resolve(null);
        }

        label.value = `${buildStatusLabel(oldStatus)} → ${buildStatusLabel(newStatus)}`;
        sub.textContent = `Bitte geben Sie den Grund für die Statusänderung von "${buildStatusLabel(oldStatus)}" auf "${buildStatusLabel(newStatus)}" an.`;
        text.value = '';
        text.focus();
        if (error) error.style.display = 'none';

        modal.style.display = 'flex';

        return new Promise((resolve) => {
            statusReasonResolver = resolve;

            const submit = () => {
                const value = String(text.value || '').trim();

                if (!value) {
                    if (error) error.style.display = 'block';
                    text.focus();
                    return;
                }

                closeStatusReasonModal(value);
            };

            const handleKeydown = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', handleKeydown);
                    closeStatusReasonModal(null);
                }

                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    submit();
                }
            };

            document.addEventListener('keydown', handleKeydown);

            confirmBtn.onclick = () => {
                document.removeEventListener('keydown', handleKeydown);
                submit();
            };

            modal.dataset.keydownBound = '1';
        });
    }

    function closeStatusReasonModal(result = null) {
        const modal = document.getElementById('status-reason-modal');
        const text = document.getElementById('status-reason-text');
        const error = document.getElementById('status-reason-error');

        if (modal) modal.style.display = 'none';
        if (text) text.value = '';
        if (error) error.style.display = 'none';

        if (typeof statusReasonResolver === 'function') {
            statusReasonResolver(result);
        }

        statusReasonResolver = null;
    }

    window.closeStatusReasonModal = closeStatusReasonModal;

    let materialMoveResolver = null;

    function getMoveStatusLabel(status) {
        const map = {
            offen: 'Offen',
            lager: 'Lager',
            bestellen: 'Bestellen'
        };

        return map[String(status || '').toLowerCase()] || 'Offen';
    }

    function openMaterialMoveModal(rows, moveTo, mode = 'single') {
        const modal = document.getElementById('material-move-modal');
        const title = document.getElementById('material-move-modal-title');
        const sub = document.getElementById('material-move-modal-sub');
        const summary = document.querySelector('#material-move-modal-summary .of-smart-list-body');
        const targetLabel = document.getElementById('material-move-target-label');
        const qtyInput = document.getElementById('material-move-qty');
        const error = document.getElementById('material-move-error');
        const confirmBtn = document.getElementById('material-move-confirm-btn');

        if (!modal || !targetLabel || !qtyInput || !confirmBtn || !summary) {
            return Promise.resolve(null);
        }

        const safeRows = Array.isArray(rows) ? rows.filter(Boolean) : [];
        state.materialMove.rows = safeRows;
        state.materialMove.moveTo = moveTo;
        state.materialMove.mode = mode;

        title.textContent = mode === 'bulk' ? 'Mengen gesammelt verschieben' : 'Menge verschieben';
        sub.textContent = mode === 'bulk'
            ? `Sie verschieben die Menge für ${safeRows.length} markierte Position(en) nach "${getMoveStatusLabel(moveTo)}".`
            : `Bitte geben Sie die Menge an, die nach "${getMoveStatusLabel(moveTo)}" verschoben werden soll.`;

        targetLabel.value = getMoveStatusLabel(moveTo);
        qtyInput.value = '';
        if (error) error.style.display = 'none';

        summary.innerHTML = safeRows.length
            ? safeRows.map(row => `
                <div style="padding:10px 0; border-bottom:1px solid #eef2f7;">
                    <div style="font-weight:900; color:#111827;">${esc(row.name || '-')}</div>
                    <div class="of-sub" style="margin-top:4px;">
                        Pos.: ${esc(row.position_no || '-')}
                        · Art.-Nr.: ${esc(row.article_no || '-')}
                        · Gesamtmenge: ${esc(Number(getRowTotalQty(row)).toFixed(2))}
                    </div>
                </div>
            `).join('')
            : 'Keine Auswahl vorhanden.';

        modal.style.display = 'flex';

        setTimeout(() => qtyInput.focus(), 30);

        return new Promise(resolve => {
            materialMoveResolver = resolve;

            confirmBtn.onclick = () => {
                const qty = Number(qtyInput.value || 0);

                if (!(qty > 0)) {
                    if (error) error.style.display = 'block';
                    qtyInput.focus();
                    return;
                }

               const sourceStatus = (state.materialFilter && state.materialFilter !== 'all')
                    ? state.materialFilter
                    : null;

                closeMaterialMoveModal({
                    move_qty: qty,
                    move_to: moveTo,
                    source_status: sourceStatus,
                    rows: safeRows,
                    mode
                });
            };
        });
    }

    function closeMaterialMoveModal(result = null) {
        const modal = document.getElementById('material-move-modal');
        const qtyInput = document.getElementById('material-move-qty');
        const error = document.getElementById('material-move-error');

        if (modal) modal.style.display = 'none';
        if (qtyInput) qtyInput.value = '';
        if (error) error.style.display = 'none';

        state.materialMove = {
            rows: [],
            moveTo: null,
            mode: 'single'
        };

        if (typeof materialMoveResolver === 'function') {
            materialMoveResolver(result);
        }

        materialMoveResolver = null;
    }
 
    window.closeMaterialMoveModal = closeMaterialMoveModal;

    let materialFinalResolver = null;

    function getFinalStatusLabel(status) {
        const map = {
            offen: 'Offen',
            lager: 'Lager',
            bestellen: 'Bestellen',
            final: 'Final'
        };

        return map[String(status || '').toLowerCase()] || 'Unbekannt';
    }

    function openMaterialFinalModal(rows, sourceStatus) {
        const modal = document.getElementById('material-final-modal');
        const title = document.getElementById('material-final-modal-title');
        const sub = document.getElementById('material-final-modal-sub');
        const summary = document.querySelector('#material-final-modal-summary .of-smart-list-body');
        const sourceLabel = document.getElementById('material-final-source-label');
        const availableLabel = document.getElementById('material-final-available-label');
        const qtyInput = document.getElementById('material-final-qty');
        const remainingSelect = document.getElementById('material-final-remaining-to');
        const reasonInput = document.getElementById('material-final-reason');
        const error = document.getElementById('material-final-error');
        const confirmBtn = document.getElementById('material-final-confirm-btn');

        if (!modal || !summary || !sourceLabel || !availableLabel || !qtyInput || !remainingSelect || !reasonInput || !confirmBtn) {
            return Promise.resolve(null);
        }

        const safeRows = Array.isArray(rows) ? rows.filter(Boolean) : [];
        if (!safeRows.length) return Promise.resolve(null);

        const firstRow = safeRows[0];
        const allocation = getRowAllocation(firstRow);
        const availableQty = Number(allocation[sourceStatus] || 0);

        if (!(availableQty > 0)) {
            showCustomToast('Keine Menge verfügbar', `In "${getFinalStatusLabel(sourceStatus)}" ist keine Menge vorhanden.`, 'error');
            return Promise.resolve(null);
        }

        state.materialFinal.rows = safeRows;
        state.materialFinal.sourceStatus = sourceStatus;
        state.materialFinal.availableQty = availableQty;

        const remainingOptions = ['offen', 'lager', 'bestellen'].filter(v => v !== sourceStatus);

        title.textContent = safeRows.length > 1 ? 'Final List gesammelt bestätigen' : 'Final List bestätigen';
        sub.textContent = safeRows.length > 1
            ? `Sie bestätigen ${safeRows.length} markierte Position(en) aus "${getFinalStatusLabel(sourceStatus)}" für die Final List.`
            : `Bitte bestätigen Sie die finale Menge aus "${getFinalStatusLabel(sourceStatus)}".`;

        sourceLabel.value = getFinalStatusLabel(sourceStatus);
        availableLabel.value = availableQty.toFixed(2);

        qtyInput.value = availableQty.toFixed(2);
        reasonInput.value = 'Physisch bestätigt';
        error.style.display = 'none';

        remainingSelect.innerHTML = remainingOptions.map(status => `
            <option value="${status}">${getFinalStatusLabel(status)}</option>
        `).join('');

        summary.innerHTML = safeRows.map(row => {
            const rowAllocation = getRowAllocation(row);
            const rowAvailable = Number(rowAllocation[sourceStatus] || 0);

            return `
                <div style="padding:10px 0; border-bottom:1px solid #eef2f7;">
                    <div style="font-weight:900; color:#111827;">${esc(row.name || '-')}</div>
                    <div class="of-sub" style="margin-top:4px;">
                        Pos.: ${esc(row.position_no || '-')}
                        · Art.-Nr.: ${esc(row.article_no || '-')}
                        · Verfügbar in ${esc(getFinalStatusLabel(sourceStatus))}: ${esc(rowAvailable.toFixed(2))}
                        · Gesamtmenge: ${esc(Number(getRowTotalQty(row)).toFixed(2))}
                    </div>
                </div>
            `;
        }).join('');

        modal.style.display = 'flex';

        setTimeout(() => qtyInput.focus(), 30);

        return new Promise(resolve => {
            materialFinalResolver = resolve;

            confirmBtn.onclick = () => {
                const finalQty = Number(String(qtyInput.value || '0').replace(',', '.'));
                const remainingTo = String(remainingSelect.value || '').trim().toLowerCase();
                const reason = String(reasonInput.value || '').trim();

                if (!(finalQty > 0) || finalQty > availableQty || !remainingOptions.includes(remainingTo) || !reason) {
                    error.style.display = 'block';
                    return;
                }

                closeMaterialFinalModal({
                    rows: safeRows,
                    source_status: sourceStatus,
                    final_qty: finalQty,
                    remaining_to: remainingTo,
                    reason
                });
            };
        });
    }

    function closeMaterialFinalModal(result = null) {
        const modal = document.getElementById('material-final-modal');
        const qtyInput = document.getElementById('material-final-qty');
        const reasonInput = document.getElementById('material-final-reason');
        const error = document.getElementById('material-final-error');
        const remainingSelect = document.getElementById('material-final-remaining-to');

        if (modal) modal.style.display = 'none';
        if (qtyInput) qtyInput.value = '';
        if (reasonInput) reasonInput.value = 'Physisch bestätigt';
        if (remainingSelect) remainingSelect.innerHTML = '';
        if (error) error.style.display = 'none';

        state.materialFinal = {
            rows: [],
            sourceStatus: null,
            availableQty: 0
        };

        if (typeof materialFinalResolver === 'function') {
            materialFinalResolver(result);
        }

        materialFinalResolver = null;
    }

    window.closeMaterialFinalModal = closeMaterialFinalModal;


    function printMaterialSheet() {
        switchTab('material-print');
        window.print();
    }

    window.printMaterialSheet = printMaterialSheet;

    function initAgbEditor() {
        const editorEl = document.getElementById('agb-editor');
        const hiddenInput = document.getElementById('agb-text-input');

        if (!editorEl || !hiddenInput) return;
        if (typeof Quill === 'undefined') return;
        if (agbQuill) return;

        agbQuill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: 'AGB hier eingeben ...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['blockquote'],
                    ['link'],
                    ['clean']
                ]
            }
        });

        agbQuill.root.innerHTML = hiddenInput.value || '';

        agbQuill.on('text-change', () => {
            hiddenInput.value = agbQuill.root.innerHTML;
        });
    }

    function setAgbEditorHtml(html) {
        const hiddenInput = document.getElementById('agb-text-input');
        if (hiddenInput) hiddenInput.value = html || '';

        if (agbQuill) {
            agbQuill.root.innerHTML = html || '';
        }
    }

    function syncAgbInputs() {
        const titleInput = document.getElementById('agb-title-input');

        const folderAgb = window.folderAgb || {};
        const defaultAgb = window.folderDefaultAgb || {};
        const detail = state.detail || {};

        const title =
            folderAgb.title ||
            detail.agb_title ||
            defaultAgb.title ||
            'Allgemeine Geschäftsbedingungen';

        const text =
            folderAgb.text ||
            detail.agb_text ||
            defaultAgb.text ||
            '';

        if (titleInput) titleInput.value = title;
        setAgbEditorHtml(text);
    }

    async function saveAgbForFolder() {
        const url = folderApp.dataset.agbSaveUrl;
        const offerId = folderApp.dataset.offerId || '';

        if (!url) {
            alert('Keine AGB-URL gefunden.');
            return;
        }

        try {
            const payload = {
                offer_id: offerId || null,
                agb_title: document.getElementById('agb-title-input')?.value || '',
                agb_text: document.getElementById('agb-text-input')?.value || ''
            };

            const json = await fetchJson(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    document_status: modalResult.to_status,
                    reason: modalResult.reason,
                    revert_to_offer: modalResult.revert_to_offer ? 1 : 0
                })
            });

            if (!json.success) {
                throw new Error(json.message || 'AGB konnte nicht gespeichert werden.');
            }

            state.detail = json.detail || state.detail;
            window.folderAgb = json.agb || {
                title: payload.agb_title,
                text: payload.agb_text
            };

            syncAgbInputs();
            showCustomToast('AGB gespeichert', 'Die AGB für dieses Angebot wurden gespeichert.');
        } catch (error) {
            alert(error.message || 'AGB konnte nicht gespeichert werden.');
        }
    }

    window.saveAgbForFolder = saveAgbForFolder;

    function getAllStructureCounts() {
        let total = 0;

        safeArray(state.sections).forEach(section => {
            safeArray(section?.items).forEach(item => {
                total++;

                safeArray(item?.subItems).forEach(sub => {
                    total++;
                    if (sub?.kind === 'labor' && Array.isArray(sub?.labor_rows)) {
                        total += sub.labor_rows.length;
                    }
                });
            });
        });

        return total;
    }

    function getStructureRows() {
        const materialRows = [];
        const laborRows = [];

        safeArray(state.sections).forEach((section, sectionIndex) => {
            const sectionTitle = section?.title || `Sektion ${sectionIndex + 1}`;
            const sectionNo = `${sectionIndex + 1}`;

            const sectionQty = Number(section?.config?.qty || 1);
            const sectionUnit = String(section?.config?.unit || '').toLowerCase();
            const sectionMultiplier = sectionUnit === 'set' ? sectionQty : 1;

            safeArray(section?.items).forEach((item, itemIndex) => {
                const itemNo = `${sectionNo}.${itemIndex + 1}`;
                const parentTitle = item?.name || item?.title || `Position ${itemIndex + 1}`;
                const parentQty = Number(item?.qty || 0);
                const parentHasChildren = safeArray(item?.subItems).length > 0;

                if (item?.kind !== 'labor') {
                    const qty = Number(item?.qty || 0);
                    const qtyTotal = qty * sectionMultiplier;

                    const unitPrice = Number(item?.price || item?.rate || 0);
                    const ekPrice = Number(item?.purchase_price || item?.ek || 0);
                    const vkTotal = Number(item?.total ?? (qty * unitPrice)) * sectionMultiplier;
                    const ekTotal = (qty * ekPrice) * sectionMultiplier;
                    const dbTotal = vkTotal - ekTotal;
                    const marginPercent = ekTotal > 0 ? ((vkTotal - ekTotal) / ekTotal) * 100 : 0;

                    materialRows.push({
                        position_no: itemNo,
                        hierarchy_level: 1,
                        section_title: sectionTitle,
                        parent_title: parentTitle,
                        level: 'Hauptposition',
                        type_label: item?.kind === 'labor' ? 'Lohn' : 'Artikel',
                        status_label: item?.lineType || item?.status || 'standard',
                        order_status: item?.order_status || 'offen',
                        stock_allocation: item?.stock_allocation || null,
                        material_history: Array.isArray(item?.material_history) ? item.material_history : [],
                        product_id: item?.product_id ?? item?.productId ?? item?.product?.id ?? null,
                        component_id: item?.component_id ?? null,
                        distributor_price_id: item?.distributor_price_id || null,
                        article_no: item?.article_no || '-',
                        distributor_article_no: item?.distributor_article_no || '-',
                        name: parentTitle,
                        image: item?.img || item?.image || item?.image_url || item?.product_image || '',
                        description: stripHtml(item?.desc_html || item?.desc || item?.description || ''),
                        qty: qty,
                        qty_total: qtyTotal,
                        unit: item?.unit || item?.measure || '-',
                        unit_price: unitPrice,
                        ek_price: ekPrice,
                        total: vkTotal,
                        ek_total: ekTotal,
                        db_total: dbTotal,
                        margin_percent: marginPercent,
                        distributor_id: item?.distributor_id || null,
                        distributor_name: item?.distributor_name || distributorName(item?.distributor_id),
                        supplier_article_no: item?.distributor_article_no || '-',
                        item_type: item?.item_type || 'Position',
                        depth: Number(item?.depth || 0),
                        section_multiplier: sectionMultiplier,
                        parent_qty: 1,
                        is_container: parentHasChildren || String(item?.item_type || '').toLowerCase() === 'master_set',
                        has_children: parentHasChildren
                    });
                }

                safeArray(item?.subItems).forEach((subItem, subIndex) => {
                    const subItemNo = `${itemNo}.${subIndex + 1}`;

                    if (subItem?.kind === 'labor') {
                        const laborRowsData = safeArray(subItem?.labor_rows);

                        if (laborRowsData.length) {
                            laborRowsData.forEach((row, rowIndex) => {
                                laborRows.push({
                                    position_no: `${subItemNo}.${rowIndex + 1}`,
                                    section_title: sectionTitle,
                                    parent_title: parentTitle,
                                    labor_title: subItem?.name || 'Arbeitsleistung',
                                    qualification_name: row?.qualification_name || `Lohnzeile ${rowIndex + 1}`,
                                    qty: Number(row?.qty || 0),
                                    unit: row?.unit || subItem?.unit || 'Std',
                                    rate: Number(row?.rate || 0),
                                    total: Number(row?.total ?? (Number(row?.qty || 0) * Number(row?.rate || 0)))
                                });
                            });
                        } else {
                            laborRows.push({
                                position_no: subItemNo,
                                section_title: sectionTitle,
                                parent_title: parentTitle,
                                labor_title: subItem?.name || 'Arbeitsleistung',
                                qualification_name: subItem?.name || 'Arbeitsleistung',
                                qty: Number(subItem?.qty || 0),
                                unit: subItem?.unit || subItem?.measure || 'Std',
                                rate: Number(subItem?.rate || subItem?.price || 0),
                                total: Number(subItem?.total ?? (Number(subItem?.qty || 0) * Number(subItem?.rate || subItem?.price || 0)))
                            });
                        }

                        return;
                    }

                    const qty = Number(subItem?.qty || 0);
                    const parentQtyFactor = parentQty > 0 ? parentQty : 1;
                    const qtyTotal = qty * parentQtyFactor * sectionMultiplier;

                    const unitPrice = Number(subItem?.price || subItem?.rate || 0);
                    const ekPrice = Number(subItem?.purchase_price || subItem?.ek || 0);
                    const vkTotal = Number(subItem?.total ?? (qty * unitPrice)) * parentQtyFactor * sectionMultiplier;
                    const ekTotal = (qty * ekPrice) * parentQtyFactor * sectionMultiplier;
                    const dbTotal = vkTotal - ekTotal;
                    const marginPercent = ekTotal > 0 ? ((vkTotal - ekTotal) / ekTotal) * 100 : 0;
                    const subHasChildren = safeArray(subItem?.subItems).length > 0;

                    materialRows.push({
                        position_no: subItemNo,
                        hierarchy_level: 2,
                        section_title: sectionTitle,
                        parent_title: parentTitle,
                        level: subItem?.isChildNode ? 'Unterartikel' : 'Komponente',
                        type_label: subItem?.kind === 'labor' ? 'Lohn' : 'Artikel',
                        status_label: subItem?.lineType || subItem?.status || 'standard',
                        order_status: subItem?.order_status || 'offen',
                        stock_allocation: subItem?.stock_allocation || null,
                        material_history: Array.isArray(subItem?.material_history) ? subItem.material_history : [],
                        product_id: subItem?.product_id ?? subItem?.productId ?? subItem?.product?.id ?? null,
                        component_id: subItem?.component_id ?? null,
                        distributor_price_id: subItem?.distributor_price_id || null,
                        article_no: subItem?.article_no || '-',
                        distributor_article_no: subItem?.distributor_article_no || '-',
                        name: subItem?.name || subItem?.title || `Unterposition ${subIndex + 1}`,
                        image: subItem?.img || subItem?.image || subItem?.image_url || subItem?.product_image || '',
                        description: stripHtml(subItem?.desc_html || subItem?.desc || subItem?.description || ''),
                        qty: qty,
                        qty_total: qtyTotal,
                        unit: subItem?.unit || subItem?.measure || '-',
                        unit_price: unitPrice,
                        ek_price: ekPrice,
                        total: vkTotal,
                        ek_total: ekTotal,
                        db_total: dbTotal,
                        margin_percent: marginPercent,
                        distributor_id: subItem?.distributor_id || null,
                        distributor_name: subItem?.distributor_name || distributorName(subItem?.distributor_id),
                        supplier_article_no: subItem?.distributor_article_no || '-',
                        item_type: subItem?.item_type || 'Komponente',
                        depth: Number(subItem?.depth || 0),
                        section_multiplier: sectionMultiplier,
                        parent_qty: parentQtyFactor,
                        is_container: false,
                        has_children: subHasChildren
                    });

                    safeArray(subItem?.subItems).forEach((childItem, childIndex) => {
                        if (childItem?.kind === 'labor') return;

                        const childNo = `${subItemNo}.${childIndex + 1}`;
                        const childQty = Number(childItem?.qty || 0);
                        const childQtyTotal = childQty * qty * parentQtyFactor * sectionMultiplier;

                        const childUnitPrice = Number(childItem?.price || childItem?.rate || 0);
                        const childEkPrice = Number(childItem?.purchase_price || childItem?.ek || 0);
                        const childVkTotal = Number(childItem?.total ?? (childQty * childUnitPrice)) * qty * parentQtyFactor * sectionMultiplier;
                        const childEkTotal = (childQty * childEkPrice) * qty * parentQtyFactor * sectionMultiplier;
                        const childDbTotal = childVkTotal - childEkTotal;
                        const childMarginPercent = childEkTotal > 0 ? ((childVkTotal - childEkTotal) / childEkTotal) * 100 : 0;

                        materialRows.push({
                            position_no: childNo,
                            hierarchy_level: 3,
                            section_title: sectionTitle,
                            parent_title: subItem?.name || parentTitle,
                            level: 'Unterkomponente',
                            type_label: childItem?.kind === 'labor' ? 'Lohn' : 'Artikel',
                            status_label: childItem?.lineType || childItem?.status || 'standard',
                            stock_allocation: childItem?.stock_allocation || null,
                            order_status: childItem?.order_status || 'offen',
                            material_history: Array.isArray(childItem?.material_history) ? childItem.material_history : [],
                            product_id: childItem?.product_id ?? childItem?.productId ?? childItem?.product?.id ?? null,
                            component_id: childItem?.component_id ?? null,
                            distributor_price_id: childItem?.distributor_price_id || null,
                            article_no: childItem?.article_no || '-',
                            distributor_article_no: childItem?.distributor_article_no || '-',
                            name: childItem?.name || childItem?.title || `Unterkomponente ${childIndex + 1}`,
                            image: childItem?.img || childItem?.image || childItem?.image_url || childItem?.product_image || '',
                            description: stripHtml(childItem?.desc_html || childItem?.desc || childItem?.description || ''),
                            qty: childQty,
                            qty_total: childQtyTotal,
                            unit: childItem?.unit || childItem?.measure || '-',
                            unit_price: childUnitPrice,
                            ek_price: childEkPrice,
                            total: childVkTotal,
                            ek_total: childEkTotal,
                            db_total: childDbTotal,
                            margin_percent: childMarginPercent,
                            distributor_id: childItem?.distributor_id || null,
                            distributor_name: childItem?.distributor_name || distributorName(childItem?.distributor_id),
                            supplier_article_no: childItem?.distributor_article_no || '-',
                            item_type: childItem?.item_type || 'Unterkomponente',
                            depth: Number(childItem?.depth || 0),
                            section_multiplier: sectionMultiplier,
                            parent_qty: qty * parentQtyFactor,
                            is_container: false,
                            has_children: false
                        });
                    });
                });
            });
        });

        return { materialRows, laborRows };
    }
    function getTabMetrics() {
        const { materialRows, laborRows } = getStructureRows();

        const realMaterialRows = materialRows.filter(row => !isContainerMaterialRow(row));

        return {
            overview: 1,
            kanban: 1,
            material: realMaterialRows.length,
            labor: laborRows.length,
            materialPrint: realMaterialRows.length,
            historie: normalizeHistoryEntries().length,
            printFiles: safeArray(state.attachments).length,
            agb: 1
        };
    }

    function renderTabCounts() {
        const metrics = getTabMetrics();

        setText('tab-count-uebersicht', String(metrics.overview));
        setText('tab-count-kanban', String(metrics.kanban));
        setText('tab-count-material', String(metrics.material));
        setText('tab-count-labor', String(metrics.labor));
        setText('tab-count-material-print', String(metrics.materialPrint)); 
        setText('tab-count-historie', String(metrics.historie));
        setText('tab-count-print-files', String(metrics.printFiles));
        setText('tab-count-agb', String(metrics.agb));
    }

    function renderStats() {
        const detail = state.detail || {};
        const currentStatus = getFolderStatus();

        setText('stat-total-net', money(detail.total_net || 0));
        setText('stat-tax-rate', new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(detail.tax_rate || 19)) + ' %');
        setText('stat-total-gross', money(detail.total_gross || 0));
        setText('stat-items-count', String(getAllStructureCounts()));

        setText('info-company-name', detail.company_name || '-');
        setText('info-brand-mode', detail.brand_mode || 'Text');
        setText('info-brand-color', detail.brand_color || '-');
        setText('info-brand-logo', detail.brand_logo_url || '-');
        setText('info-sections-count', String(safeArray(state.sections).length || 0));
        setText('info-images-count', String(safeArray(detail.placed_images).length || 0));

        const coverBox = document.getElementById('cover-box');
        if (coverBox) {
            const cover = buildCoverHtml(detail);
            coverBox.innerHTML = cover.html;
            coverBox.classList.toggle('empty', cover.isEmpty);
        }

        if (getDocumentStatus() === 'deal') {
            setText('status-card-draft', currentStatus === 'open' ? '1' : '0');
            setText('status-card-sent', currentStatus === 'proposal' ? '1' : '0');
            setText('status-card-negotiation', currentStatus === 'negotiation' ? '1' : '0');
            setText('status-card-final', currentStatus === 'won' ? '1' : '0');
            setText('status-card-cancel', currentStatus === 'lost' ? '1' : '0');
        } else {
            setText('status-card-draft', currentStatus === 'draft' ? '1' : '0');
            setText('status-card-sent', currentStatus === 'sent' ? '1' : '0');
            setText('status-card-negotiation', currentStatus === 'negotiation' ? '1' : '0');
            setText('status-card-final', currentStatus === 'accepted' ? '1' : '0');
            setText('status-card-cancel', currentStatus === 'cancelled' ? '1' : '0');
        }
        renderDocumentStatusToggle();
        renderTabCounts();
        renderOfferLockState();
    }

    function renderPresenceUsers() {
        const el = document.getElementById('presence-users');
        if (!el) return;

        const users = safeArray(state.presenceUsers);
        const defaultAvatar = folderApp.dataset.defaultAvatar || '';

        if (!users.length) {
            el.innerHTML = `<div class="of-presence-empty">Keine weiteren Benutzer sichtbar.</div>`;
            return;
        }

        el.innerHTML = users.map(user => `
            <div class="of-presence-user">
                <div class="of-presence-avatar-wrap">
                    <img
                        src="${esc(user?.avatar || defaultAvatar)}"
                        alt="${esc(user?.name || 'Benutzer')}"
                        class="of-presence-avatar"
                        onerror="this.src='${esc(defaultAvatar)}'"
                    >
                    <span class="of-presence-dot"></span>
                </div>
                <span class="of-presence-name">${esc(user?.name || `User #${user?.id || ''}`)}</span>
            </div>
        `).join('');
    }

     
    function renderKanban() {
    const kanbanRoot = document.getElementById('kanban-columns');
    const badge = document.getElementById('kanban-list-badge');
    if (!kanbanRoot) return;

    const currentStatus = getFolderStatus();
    const statusKeys = getWorkflowStatusKeys();
    const labels = getWorkflowStatusLabels();

    const customerLines = getOfferCustomerLines();
    const contactName = getContactName();
    const contactPhone = getContactPhone();
    const contactEmail = getContactEmail();
    const productLabel = getProductLabel();
    const objectLabel = getObjectLabel();

    const net = money(state.detail?.total_net || 0);
    const gross = money(state.detail?.total_gross || 0);
    const createdAt = formatDateTimeValue(state.detail?.created_at || state.folder?.created_at);
    const updatedAt = formatDateTimeValue(state.detail?.updated_at || state.folder?.updated_at);

    if (badge) {
        badge.textContent = '1 Eintrag';
    }

    const currentIndex = statusKeys.indexOf(currentStatus);

    const statusColorClass = (status) => {
        const map = {
            draft: 'color-draft',
            pending_approval: 'color-pending',
            sent: 'color-sent',
            viewed: 'color-viewed',
            negotiation: 'color-negotiation',
            revised: 'color-revised',
            accepted: 'color-final',
            rejected: 'color-cancel',
            expired: 'color-expired',
            cancelled: 'color-cancel',

            open: 'color-open',
            qualified: 'color-qualified',
            proposal: 'color-proposal',
            won: 'color-won',
            lost: 'color-lost',
            on_hold: 'color-onhold'
        };

        if (status === 'negotiation' && getDocumentStatus() === 'deal') {
            return 'color-deal-negotiation';
        }

        return map[status] || 'color-draft';
    };

    kanbanRoot.innerHTML = `
        <div class="of-stepper-wrap">
            <div class="of-stepper-card">
                <div class="of-stepper-head">
                    <div>
                        <h3 class="of-stepper-title">
                            ${getDocumentStatus() === 'deal' ? 'Auftrag / Deal Workflow' : 'Angebots Workflow'}
                        </h3>
                        <div class="of-stepper-sub">
                            Ordner: ${esc(state.folder?.name || 'Ordner')}
                            · Nummer: #${esc(state.offer?.id || state.folder?.offer_id || '-')}
                            · Status: ${esc(buildStatusLabel(currentStatus))}
                        </div>
                    </div>

                    <div class="of-inline-actions">
                        <button type="button" class="of-item-action primary" onclick="switchTab('uebersicht')">
                            Übersicht öffnen
                        </button>
                    </div>
                </div>

                <div class="of-stepper-body">
                    <div class="of-stepper">
                        ${statusKeys.map((status, index) => {
                            const isCurrent = status === currentStatus;
                            const isPast = currentIndex >= 0 && index < currentIndex;
                            const isFuture = currentIndex === -1 || index > currentIndex;

                            let stepStateClass = 'is-future';
                            if (isCurrent) stepStateClass = 'is-current';
                            else if (isPast) stepStateClass = 'is-past';

                            return `
                                <button
                                    type="button"
                                    class="of-step ${stepStateClass}"
                                    data-workflow-status="${esc(status)}"
                                    ${isCurrent ? 'disabled' : ''}
                                    title="${esc(labels[status] || status)}"
                                >
                                    <span class="of-step-label">
                                        <span class="of-step-index">${index + 1}</span>
                                        <span>${esc(labels[status] || status)}</span>
                                    </span>
                                </button>
                            `;
                        }).join('')}
                    </div>

                    <div class="of-step-meta">
                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Kunde</div>
                            <div class="of-workflow-box-value">
                                ${customerLines.map(line => esc(line)).join('<br>')}
                            </div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Ansprechpartner</div>
                            <div class="of-workflow-box-value">
                                ${esc(contactName)}<br>
                                Tel: ${esc(contactPhone)}<br>
                                E-Mail: ${esc(contactEmail)}
                            </div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Objekt / Produkt</div>
                            <div class="of-workflow-box-value">
                                ${esc(objectLabel)}<br>
                                ${esc(productLabel)}
                            </div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Werte</div>
                            <div class="of-workflow-box-value">
                                Netto: ${esc(net)}<br>
                                Brutto: ${esc(gross)}
                            </div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Erstellt</div>
                            <div class="of-workflow-box-value">${esc(createdAt)}</div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Letzte Änderung</div>
                            <div class="of-workflow-box-value">${esc(updatedAt)}</div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Sektionen</div>
                            <div class="of-workflow-box-value">${esc(safeArray(state.sections).length)}</div>
                        </div>

                        <div class="of-workflow-box">
                            <div class="of-workflow-box-label">Dokumenttyp</div>
                            <div class="of-workflow-box-value">
                                ${esc(getDocumentStatusLabel(getDocumentStatus()))}
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        initKanbanSortable();
    }
    async function saveKanbanMove(payload) {
        const url = folderApp.dataset.kanbanMoveUrl;

        return await fetchJson(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });
    }

    function initKanbanSortable() {
        document.querySelectorAll('[data-workflow-status]').forEach(btn => {
            if (btn.dataset.ready === '1') return;

            btn.addEventListener('click', async function () {
                const zielStatus = String(this.dataset.workflowStatus || '').trim().toLowerCase();
                const alterStatus = getFolderStatus();

                const allowedStatuses = getWorkflowStatusKeys();

                if (isOfferLockedByWorkflow()) {
                    showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                    return;
                }

                if (!zielStatus || !allowedStatuses.includes(zielStatus)) {
                    await loadFolderData();
                    return;
                }

                if (zielStatus === alterStatus) {
                    return;
                }

                const reasonText = await openStatusReasonModal(zielStatus, alterStatus);

                if (!reasonText || !String(reasonText).trim()) {
                    await loadFolderData();
                    return;
                }

                try {
                    const json = await saveKanbanMove({
                        status: zielStatus,
                        reason: String(reasonText).trim()
                    });

                    if (!json.success) {
                        throw new Error(json.message || 'Status konnte nicht gespeichert werden.');
                    }

                    state.folder = json.folder || state.folder;
                    renderStats();
                    renderKanban();
                    renderHistory();

                    showCustomToast(
                        'Status aktualisiert',
                        `Status wurde auf "${buildStatusLabel(zielStatus)}" geändert.`
                    );
                } catch (error) {
                    await loadFolderData();
                    showCustomToast('Fehler', error.message || 'Status konnte nicht gespeichert werden.', 'error');
                }
            });

            btn.dataset.ready = '1';
        });
    }

   function getSelectedMaterialRows(requireProductId = true) {
        const { materialRows } = getStructureRows();
        const selectedIndexes = Array.from(document.querySelectorAll('.material-row-check:checked'))
            .map(cb => Number(cb.dataset.rowIndex))
            .filter(index => !Number.isNaN(index));

        let rows = selectedIndexes
            .map(index => materialRows[index])
            .filter(Boolean)
            .filter(row => !isContainerMaterialRow(row));

        if (requireProductId) {
            rows = rows.filter(row => isStatusEditableMaterialRow(row));
        }

        return rows;
    }

    function hideSmartMaterialSidebar() {
        const el = document.getElementById('smart-material-sidebar');
        if (el) el.classList.remove('show');
        state.smartSidebar.visible = false;
    }

    function showSmartMaterialSidebar() {
        const el = document.getElementById('smart-material-sidebar');
        if (el) el.classList.add('show');
        state.smartSidebar.visible = true;
    }

    window.hideSmartMaterialSidebar = hideSmartMaterialSidebar;

    function buildCheapestSelectionFromComparison(data, selectedRows) {
        const items = Array.isArray(data?.items) ? data.items : [];

        let currentTotal = 0;
        let cheapestTotal = 0;
        const cheapestRows = [];

        items.forEach((item, index) => {
            const currentRow = selectedRows[index];
            const options = Array.isArray(item?.options) ? item.options : [];

            const currentLineTotal = Number(currentRow?.total || 0);
            currentTotal += currentLineTotal;

            if (!options.length) {
                cheapestTotal += currentLineTotal;
                return;
            }

            const cheapest = [...options].sort((a, b) => Number(a.effective_price || 0) - Number(b.effective_price || 0))[0];
            const cheapestLineTotal = Number(cheapest?.line_total || 0);

            cheapestTotal += cheapestLineTotal;

           cheapestRows.push({
                product_id: currentRow.product_id,
                article_no: currentRow.article_no,
                name: currentRow.name,
                qty: currentRow.qty,
                unit: currentRow.unit,

                current_distributor_id: currentRow.distributor_id,
                current_distributor_name: currentRow.distributor_name || distributorName(currentRow.distributor_id),
                current_distributor_price_id: currentRow.distributor_price_id,
                current_total: currentLineTotal,

                target_distributor_id: cheapest.distributor_id,
                target_distributor_name: cheapest.distributor_name,
                target_distributor_price_id: cheapest.distributor_price_id,
                target_total: cheapestLineTotal,

                current_price: Number(currentRow.unit_price || 0),
                target_price: Number(cheapest.effective_price || 0),
                target_article_no: cheapest.article_no || currentRow.article_no || '-',
                availability: cheapest.availability || '-',

                changed:
                    Number(currentRow.distributor_id || 0) !== Number(cheapest.distributor_id || 0) ||
                    Number(currentRow.distributor_price_id || 0) !== Number(cheapest.distributor_price_id || 0)
            });
        });

        return {
            count: selectedRows.length,
            current_total: currentTotal,
            cheapest_total: cheapestTotal,
            savings: currentTotal - cheapestTotal,
            rows: cheapestRows
        };
    }

    function renderSmartMaterialSidebar(summary) {
        const root = document.getElementById('smart-material-sidebar-body');
        if (!root) return;

        const rows = Array.isArray(summary?.rows) ? summary.rows : [];
        const changedRows = rows.filter(row => row.changed);

        root.innerHTML = `
            <div class="of-smart-grid">
                <div class="of-smart-metric">
                    <div class="of-smart-metric-label">Vorher</div>
                    <div class="of-smart-metric-value">${esc(money(summary.current_total || 0))}</div>
                </div>

                <div class="of-smart-metric">
                    <div class="of-smart-metric-label">Nachher</div>
                    <div class="of-smart-metric-value">${esc(money(summary.cheapest_total || 0))}</div>
                </div>

                <div class="of-smart-metric">
                    <div class="of-smart-metric-label">Ersparnis</div>
                    <div class="of-smart-metric-value success">${esc(money(summary.savings || 0))}</div>
                </div>

                <div class="of-smart-metric">
                    <div class="of-smart-metric-label">Umstellungen</div>
                    <div class="of-smart-metric-value muted">${changedRows.length} / ${summary.count}</div>
                </div>
            </div>

            <div class="of-smart-savings-bar">
                <div>
                    <div class="of-smart-metric-label" style="margin-bottom:4px;">Gesamtvergleich</div>
                    <div class="of-smart-row-sub" style="margin-top:0;">
                        Aktueller Einkauf gegenüber vorgeschlagener günstigster Alternative
                    </div>
                </div>
                <div class="of-smart-savings-value">${esc(money(summary.savings || 0))}</div>
            </div>

            <div class="of-smart-list">
                <div class="of-smart-list-head">Vorher / Nachher je Position</div>
                <div class="of-smart-list-body">
                    ${
                        rows.length
                            ? rows.map(row => `
                                <div class="of-smart-row">
                                    <div class="of-smart-row-head">
                                        <div class="of-smart-row-title">${esc(row.name || 'Material')}</div>
                                        <span class="of-smart-row-badge ${row.changed ? '' : 'same'}">
                                            ${row.changed ? 'Wechsel empfohlen' : 'Bereits optimal'}
                                        </span>
                                    </div>

                                    <div class="of-smart-row-sub" style="margin-bottom:10px;">
                                        Art.-Nr.: ${esc(row.article_no || '-')}
                                    </div>

                                    <div class="of-smart-compare">
                                        <div class="of-smart-compare-card">
                                            <div class="of-smart-compare-label">Vorher</div>
                                            <div class="of-smart-compare-name">${esc(distributorName(row.current_distributor_id))}</div>
                                            <div class="of-smart-compare-sub">
                                                Aktuelle Auswahl
                                            </div>
                                            <div class="of-smart-compare-price">
                                                ${esc(money(row.current_total || 0))}
                                            </div>
                                        </div>

                                        <div class="of-smart-compare-arrow">→</div>

                                        <div class="of-smart-compare-card">
                                            <div class="of-smart-compare-label">Nachher</div>
                                            <div class="of-smart-compare-name">${esc(row.target_distributor_name || '-')}</div>
                                            <div class="of-smart-compare-sub">
                                                Vorgeschlagene günstigste Alternative
                                            </div>
                                            <div class="of-smart-compare-price success">
                                                ${esc(money(row.target_total || 0))}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="of-smart-row-sub" style="margin-top:10px;">
                                        Ersparnis für diese Position:
                                        <strong>${esc(money((row.current_total || 0) - (row.target_total || 0)))}</strong>
                                        · Verfügbarkeit: ${esc(row.availability || '-')}
                                    </div>

                                    ${
                                        row.changed
                                            ? `
                                                <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                                                    <button
                                                        type="button"
                                                        class="of-btn"
                                                        onclick="applySuggestedSingleChange(${Number(row.product_id)}, ${Number(row.current_distributor_id || 0)}, ${Number(row.current_distributor_price_id || 0)}, ${Number(row.target_distributor_id || 0)}, '${esc(String(row.article_no || '').replaceAll("'", "\\'"))}', '${esc(String(row.name || '').replaceAll("'", "\\'"))}', '${esc(String(row.unit || '').replaceAll("'", "\\'"))}', ${Number(row.qty || 0)})"
                                                    >
                                                        Jetzt übernehmen
                                                    </button>
                                                </div>
                                            `
                                            : ''
                                    }
                                </div>
                            `).join('')
                            : `<div class="of-smart-empty">Keine Daten vorhanden.</div>`
                    }
                </div>
            </div>

            <div class="of-smart-actions">
                <button type="button" class="of-btn" onclick="confirmApplyCheapestAlternative()" ${changedRows.length ? '' : 'disabled'}>
                    Alle Vorschläge übernehmen
                </button>

                <button type="button" class="of-btn soft" onclick="refreshSmartMaterialSidebar()">
                    Neu berechnen
                </button>
            </div>
        `;

        showSmartMaterialSidebar();
    }



    async function refreshSmartMaterialSidebar() {
        const selectedRows = getSelectedMaterialRows(true);

        if (!selectedRows.length) {
            hideSmartMaterialSidebar();
            return;
        }

        const root = document.getElementById('smart-material-sidebar-body');
        if (root) {
            root.innerHTML = `<div class="of-smart-empty">Günstigste Alternative wird berechnet...</div>`;
        }

        showSmartMaterialSidebar();

        try {
            const url = folderApp.dataset.materialComparisonUrl;
            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    items: selectedRows.map(row => ({
                        product_id: row.product_id,
                        name: row.name,
                        qty: row.qty,
                        unit: row.unit,
                        article_no: row.article_no,
                        current_distributor_id: row.distributor_id,
                        current_distributor_price_id: row.distributor_price_id,
                        current_price: row.unit_price
                    }))
                })
            });

            if (!json.success) {
                throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
            }

            const summary = buildCheapestSelectionFromComparison(json, selectedRows);
            state.smartSidebar.summary = summary;
            renderSmartMaterialSidebar(summary);
        } catch (error) {
            if (root) {
                root.innerHTML = `<div class="of-smart-empty">${esc(error.message || 'Berechnung fehlgeschlagen.')}</div>`;
            }
        }
    }

    window.refreshSmartMaterialSidebar = refreshSmartMaterialSidebar;

    async function confirmApplyCheapestAlternative() {
        const summary = state.smartSidebar.summary;

        if (!summary || !Array.isArray(summary.rows) || !summary.rows.length) {
            alert('Keine Alternativen vorhanden.');
            return;
        }

        const changedRows = summary.rows.filter(row => row.changed);

        if (!changedRows.length) {
            alert('Es gibt keine günstigeren Alternativen zum Übernehmen.');
            return;
        }

        const confirmed = window.confirm(
            `Möchten Sie wirklich ${changedRows.length} Materialposition(en) auf die günstigste Alternative umstellen?\n\n` +
            `Vorher: ${money(summary.current_total)}\n` +
            `Nachher: ${money(summary.cheapest_total)}\n` +
            `Ersparnis: ${money(summary.savings)}`
        );

        if (!confirmed) return;

        await applyCheapestAlternativeBulk(changedRows);
    }

    window.confirmApplyCheapestAlternative = confirmApplyCheapestAlternative;

    async function applyCheapestAlternativeBulk(rows) {
        const url = folderApp.dataset.materialChangeUrl;
        if (!url) {
            alert('Keine Änderungs-URL gefunden.');
            return;
        }

        if (isOfferLockedByWorkflow()) {
            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
            return;
        }

        try {
            const payload = {
                items: rows.map(row => ({
                    product_id: row.product_id,
                    article_no: row.article_no,
                    name: row.name,
                    qty: row.qty,
                    unit: row.unit,
                    current_distributor_id: row.current_distributor_id,
                    current_distributor_price_id: row.current_distributor_price_id,
                    target_distributor_id: row.target_distributor_id
                }))
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Bulk-Änderung konnte nicht übernommen werden.');
            }

            await loadFolderData();
            hideSmartMaterialSidebar();

            showCustomToast(
                'Günstigste Alternative übernommen',
                `${rows.length} Materialposition(en) wurden erfolgreich aktualisiert.`
            );
        } catch (error) {
            alert(error.message || 'Bulk-Änderung konnte nicht übernommen werden.');
        }
    }

    async function applySuggestedSingleChange(productId, currentDistributorId, currentDistributorPriceId, targetDistributorId, articleNo, name, unit, qty) {
        const url = folderApp.dataset.materialChangeUrl;
        if (!url) {
            alert('Keine Änderungs-URL gefunden.');
            return;
        }

        try {
            const payload = {
                items: [{
                    product_id: Number(productId),
                    article_no: articleNo || '',
                    name: name || '',
                    qty: Number(qty || 0),
                    unit: unit || '',
                    current_distributor_id: currentDistributorId ? Number(currentDistributorId) : null,
                    current_distributor_price_id: currentDistributorPriceId ? Number(currentDistributorPriceId) : null,
                    target_distributor_id: Number(targetDistributorId)
                }]
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Änderung konnte nicht übernommen werden.');
            }

            await loadFolderData();
            await refreshSmartMaterialSidebar();

            showCustomToast(
                'Position aktualisiert',
                `${name || 'Materialposition'} wurde auf die vorgeschlagene Alternative umgestellt.`
            );
        } catch (error) {
            alert(error.message || 'Änderung konnte nicht übernommen werden.');
        }
    }

    window.applySuggestedSingleChange = applySuggestedSingleChange;



    async function openMaterialComparisonModal() {
        const allSelected = getSelectedMaterialRows(false);


        if (!allSelected.length) {
            alert('Bitte wählen Sie zuerst mindestens eine Materialposition aus.');
            return;
        }

        const selectedRows = getSelectedMaterialRows(true);

        if (!selectedRows.length) {
            alert('Die ausgewählten Positionen sind manuelle Einträge oder Sets ohne Katalogverknüpfung.');
            return;
        }
 


        const url = folderApp.dataset.materialComparisonUrl;
        if (!url) {
            alert('Keine Vergleichs-URL gefunden.');
            return;
        }

        const modal = document.getElementById('material-comparison-modal');
        const body = document.getElementById('material-comparison-body');

        if (!modal || !body) return;

        modal.style.display = 'flex';
        body.innerHTML = `<div class="of-empty">Vergleichsdaten werden geladen...</div>`;

        try {
            const payload = {
                items: selectedRows.map(row => ({
                    product_id: parseInt(row.product_id, 10),
                    name: row.name,
                    qty: parseFloat(row.qty || 0),
                    unit: row.unit,
                    article_no: row.article_no,
                    current_distributor_id: row.distributor_id ? parseInt(row.distributor_id, 10) : null,
                    current_distributor_price_id: row.distributor_price_id ? parseInt(row.distributor_price_id, 10) : null,
                    current_price: parseFloat(row.unit_price || 0)
                }))
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Vergleich konnte nicht geladen werden.');
            }

            renderMaterialComparisonModal(json);
        } catch (error) {
            body.innerHTML = `<div class="of-empty">${esc(error.message || 'Vergleich konnte nicht geladen werden.')}</div>`;
        }
    }

    window.openMaterialComparisonModal = openMaterialComparisonModal;

    function closeMaterialComparisonModal() {
        const modal = document.getElementById('material-comparison-modal');
        if (modal) modal.style.display = 'none';

        if (Array.isArray(state.comparisonCharts)) {
            state.comparisonCharts.forEach(chart => {
                try { chart.destroy(); } catch (e) {}
            });
        }

        state.comparisonCharts = [];
    }

    window.closeMaterialComparisonModal = closeMaterialComparisonModal;

    function initDistributorSearch() {
        const input = document.getElementById('distributor-search-input');
        const cards = document.querySelectorAll('#distributor-card-list .of-dist-card');
        if (!input) return;

        input.addEventListener('input', function () {
            const query = String(this.value || '').trim().toLowerCase();

            cards.forEach(card => {
                const haystack = String(card.dataset.distributorSearch || '');
                card.style.display = !query || haystack.includes(query) ? '' : 'none';
            });
        });
    }
 
    async function refreshMaterialComparisonSidebar() {
        const selectedRows = getSelectedMaterialRows(true);

        if (!selectedRows.length) {
            hideSmartMaterialSidebar();
            return;
        }

        const root = document.getElementById('smart-material-sidebar-body');
        if (root) {
            root.innerHTML = `<div class="of-smart-empty">Preisvergleich wird geladen...</div>`;
        }

        showSmartMaterialSidebar();

        try {
            const url = folderApp.dataset.materialComparisonUrl;

            const payload = {
                items: selectedRows.map(row => ({
                    product_id: Number(row.product_id),
                    name: row.name,
                    qty: Number(row.qty || 0),
                    unit: row.unit,
                    article_no: row.article_no,
                    current_distributor_id: row.distributor_id ? Number(row.distributor_id) : null,
                    current_distributor_price_id: row.distributor_price_id ? Number(row.distributor_price_id) : null,
                    current_price: Number(row.unit_price || 0)
                }))
            };

            console.log('Sidebar comparison payload:', payload);

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
            }

            renderAllDistributorOptionsInSidebar(json, selectedRows);
        } catch (error) {
            if (root) {
                root.innerHTML = `<div class="of-smart-empty">${esc(error.message || 'Preisvergleich fehlgeschlagen.')}</div>`;
            }
        }
    }

    function renderAllDistributorOptionsInSidebar(data, selectedRows) {
        const root = document.getElementById('smart-material-sidebar-body');
        if (!root) return;

        const items = Array.isArray(data?.items) ? data.items : [];
        const summary = Array.isArray(data?.summary) ? data.summary : [];

        root.innerHTML = `
            <div class="of-smart-list">
                <div class="of-smart-list-head">Alle möglichen Distributor-Preise</div>
                <div class="of-smart-list-body" style="max-height:520px;">
                    ${
                        summary.length
                            ? summary.map(distributor => {
                                const distributorItems = items.map(item => {
                                    const option = (item.options || []).find(
                                        opt => Number(opt.distributor_id) === Number(distributor.distributor_id)
                                    );

                                    if (!option) return '';

                                    return `
                                        <div class="of-smart-row" style="border-bottom:1px solid #eef2f7;">
                                            <div class="of-smart-row-title">${esc(item.product_name || '-')}</div>
                                            <div class="of-smart-row-sub">
                                                Art.-Nr.: ${esc(option.article_no || '-')}
                                                <br>Menge: ${esc(item.qty)} ${esc(item.unit || '')}
                                                <br>Preis: ${esc(money(option.price || 0))}
                                                <br>EK: ${esc(money(option.purchase_price || 0))}
                                                <br>Effektiv: ${esc(money(option.effective_price || 0))}
                                                <br>Gesamt: ${esc(money(option.line_total || 0))}
                                                <br>Verfügbarkeit: ${esc(option.availability || '-')}
                                            </div>
                                        </div>
                                    `;
                                }).filter(Boolean).join('');

                                return `
                                    <div class="of-smart-list" style="margin-bottom:12px;">
                                        <div class="of-smart-list-head">
                                            ${esc(distributor.distributor_name)} · Gesamt: ${esc(money(distributor.total_effective || 0))}
                                        </div>
                                        <div class="of-smart-list-body">
                                            ${distributorItems || `<div class="of-smart-empty">Keine passenden Artikel.</div>`}
                                        </div>
                                    </div>
                                `;
                            }).join('')
                            : `<div class="of-smart-empty">Keine Distributorpreise gefunden.</div>`
                    }
                </div>
            </div>
        `;

        showSmartMaterialSidebar();
    }
    async function applyDistributorChange(distributorId, distributorNameText) {
        const selectedRows = getSelectedMaterialRows(true);

        if (!selectedRows.length) {
            alert('Bitte zuerst Materialpositionen auswählen.');
            return;
        }

        if (isOfferLockedByWorkflow()) {
            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
            return;
        }

        const url = folderApp.dataset.materialChangeUrl;
        if (!url) {
            alert('Keine Änderungs-URL gefunden.');
            return;
        }

        try {
            const payload = {
                distributor_id: distributorId,
                items: selectedRows.map(row => ({
                    product_id: row.product_id,
                    article_no: row.article_no,
                    name: row.name,
                    qty: row.qty,
                    unit: row.unit,
                    current_distributor_id: row.distributor_id,
                    current_distributor_price_id: row.distributor_price_id
                }))
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Distributor konnte nicht übernommen werden.');
            }

            closeMaterialComparisonModal();
            await loadFolderData();

            showCustomToast(
                'Distributor übernommen',
                `${distributorNameText} wurde in der Angebotsvorlage und im Ordner aktualisiert.`
            );
        } catch (error) {
            alert(error.message || 'Distributor konnte nicht übernommen werden.');
        }
    }

    window.applyDistributorChange = applyDistributorChange;

    function renderComparisonCharts(summary) {
        if (typeof Chart === 'undefined') return;

        if (Array.isArray(state.comparisonCharts)) {
            state.comparisonCharts.forEach(chart => {
                try { chart.destroy(); } catch (e) {}
            });
        }

        state.comparisonCharts = [];

        const totalCanvas = document.getElementById('comparison-chart-total');
        const termsCanvas = document.getElementById('comparison-chart-terms');
        if (!totalCanvas || !termsCanvas) return;

        const labels = summary.map(row => row.distributor_name);
        const totals = summary.map(row => Number(row.total_effective || 0));
        const paymentTerms = summary.map(row => Number(row.avg_payment_terms || 0));
        const cashDiscounts = summary.map(row => Number(row.avg_cash_discount || 0));
        const availability = summary.map(row => Number(row.availability_ratio || 0));

        const totalChart = new Chart(totalCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Gesamtpreis', data: totals }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false
            }
        });

        const termsChart = new Chart(termsCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Zahlungsziel (Tage)', data: paymentTerms },
                    { label: 'Skonto %', data: cashDiscounts },
                    { label: 'Verfügbarkeit %', data: availability }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false
            }
        });

        state.comparisonCharts.push(totalChart, termsChart);
    }

    function initComparisonFilters(summary) {
        const availabilityInput = document.getElementById('filter-availability');
        const skontoInput = document.getElementById('filter-skonto');
        const paymentTermsInput = document.getElementById('filter-payment-terms');

        const applyFilters = () => {
            const filteredSummary = summary.map(row => ({
                ...row,
                avg_payment_terms: paymentTermsInput?.checked ? row.avg_payment_terms : 0,
                avg_cash_discount: skontoInput?.checked ? row.avg_cash_discount : 0,
                availability_ratio: availabilityInput?.checked ? row.availability_ratio : 0
            }));

            renderComparisonCharts(filteredSummary);

            document.querySelectorAll('.of-dist-card').forEach(card => {
                const paymentEl = card.querySelector('[data-metric="payment_terms"]');
                const skontoEl = card.querySelector('[data-metric="skonto"]');
                const availabilityEl = card.querySelector('[data-metric="availability"]');

                if (paymentEl) paymentEl.style.display = paymentTermsInput?.checked ? '' : 'none';
                if (skontoEl) skontoEl.style.display = skontoInput?.checked ? '' : 'none';
                if (availabilityEl) availabilityEl.style.display = availabilityInput?.checked ? '' : 'none';
            });
        };

        [availabilityInput, skontoInput, paymentTermsInput].forEach(input => {
            if (input) input.addEventListener('change', applyFilters);
        });

        applyFilters();
    }

    function renderMaterialComparisonModal(data) {
        const body = document.getElementById('material-comparison-body');
        if (!body) return;

        const summary = Array.isArray(data.summary) ? data.summary : [];
        const items = Array.isArray(data.items) ? data.items : [];

        const bestDistributorId = summary.length ? summary[0].distributor_id : null;
        const worstDistributorId = summary.length ? summary[summary.length - 1].distributor_id : null;

        body.innerHTML = `
            <div class="of-compare-layout">
                <div class="of-compare-left">
                    <div class="of-compare-stats">
                        <div class="of-compare-card">
                            <div class="of-stat-label">Ausgewählte Produkte</div>
                            <div class="of-stat-value">${items.length}</div>
                            <div class="of-stat-sub">Verglichene Materialpositionen</div>
                        </div>

                        <div class="of-compare-card">
                            <div class="of-stat-label">Bester Preis</div>
                            <div class="of-stat-value">${summary.length ? esc(money(summary[0].total_effective)) : '-'}</div>
                            <div class="of-stat-sub">${summary.length ? esc(summary[0].distributor_name) : 'Keine Daten'}</div>
                        </div>

                        <div class="of-compare-card">
                            <div class="of-stat-label">Schlechtester Preis</div>
                            <div class="of-stat-value">${summary.length ? esc(money(summary[summary.length - 1].total_effective)) : '-'}</div>
                            <div class="of-stat-sub">${summary.length ? esc(summary[summary.length - 1].distributor_name) : 'Keine Daten'}</div>
                        </div>
                    </div>

                    <div class="of-compare-chart">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
                            <div>
                                <h4 class="of-card-title" style="margin:0 0 6px 0;">Gesamtpreis nach Distributor</h4>
                                <div class="of-sub" style="margin:0;">Preisvergleich aller verfügbaren Anbieter.</div>
                            </div>
                        </div>
                        <div class="of-chart-box">
                            <canvas id="comparison-chart-total"></canvas>
                        </div>
                    </div>

                    <div class="of-compare-chart">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
                            <div>
                                <h4 class="of-card-title" style="margin:0 0 6px 0;">Zahlungsziel / Skonto / Verfügbarkeit</h4>
                                <div class="of-sub" style="margin:0;">Aktiviere oder deaktiviere die Kriterien für den Vergleich.</div>
                            </div>

                            <div class="of-compare-filters">
                                <label class="of-filter-chip">
                                    <input type="checkbox" id="filter-availability" checked>
                                    Verfügbarkeit
                                </label>
                                <label class="of-filter-chip">
                                    <input type="checkbox" id="filter-skonto" checked>
                                    Skonto
                                </label>
                                <label class="of-filter-chip">
                                    <input type="checkbox" id="filter-payment-terms" checked>
                                    Zahlungsziel
                                </label>
                            </div>
                        </div>

                        <div class="of-chart-box">
                            <canvas id="comparison-chart-terms"></canvas>
                        </div>
                    </div>
                </div>

                <div class="of-compare-right">
                    <div class="of-compare-side">
                        <div class="of-compare-side-head">
                            <div>
                                <h4 class="of-card-title" style="margin:0;">Distributor auswählen</h4>
                                <div class="of-sub" style="margin-top:6px;">
                                    Wähle einen Anbieter und übernehme ihn direkt in die Angebotsvorlage.
                                </div>
                            </div>

                            <div class="of-compare-search">
                                <input type="text" id="distributor-search-input" placeholder="Distributor suchen ...">
                            </div>
                        </div>

                        <div class="of-compare-side-body" id="distributor-card-list">
                            ${summary.map(row => {
                                const isBest = Number(row.distributor_id) === Number(bestDistributorId);
                                const isWorst = Number(row.distributor_id) === Number(worstDistributorId);

                                const matchingItems = items.map(item => {
                                    const option = (item.options || []).find(opt => Number(opt.distributor_id) === Number(row.distributor_id));
                                    if (!option) return '';

                                    return `
                                        <div class="of-dist-item">
                                            <div class="of-dist-item-top">
                                                <div class="of-dist-item-name">${esc(item.product_name)}</div>
                                                <div class="of-badge">${esc(money(option.line_total))}</div>
                                            </div>
                                            <div class="of-dist-item-sub">
                                                <div><strong>Art.-Nr.:</strong> ${esc(option.article_no || '-')}</div>
                                                <div><strong>Menge:</strong> ${esc(item.qty)} ${esc(item.unit)}</div>
                                                <div><strong>Preis:</strong> ${esc(money(option.price))}</div>
                                                <div><strong>EK:</strong> ${esc(money(option.purchase_price))}</div>
                                                <div><strong>Verfügbarkeit:</strong> ${esc(option.availability || '-')}</div>
                                            </div>
                                        </div>
                                    `;
                                }).filter(Boolean).join('');

                                return `
                                    <div class="of-dist-card ${isBest ? 'is-best' : ''} ${isWorst ? 'is-worst' : ''}" data-distributor-search="${esc((row.distributor_name || '').toLowerCase())}">
                                        <div class="of-dist-card-head">
                                            <div>
                                                <div class="of-dist-title">${esc(row.distributor_name)}</div>
                                                <div class="of-dist-sub">Vergleich für ${items.length} ausgewählte Positionen</div>
                                            </div>
                                            ${isBest ? '<span class="of-dist-rank best">Bester Preis</span>' : (isWorst ? '<span class="of-dist-rank worst">Höchster Preis</span>' : '')}
                                        </div>

                                        <div class="of-dist-card-body">
                                            <div class="of-dist-metrics">
                                                <div class="of-dist-metric" data-metric="total">
                                                    <div class="of-dist-metric-label">Gesamtpreis</div>
                                                    <div class="of-dist-metric-value">${esc(money(row.total_effective))}</div>
                                                </div>

                                                <div class="of-dist-metric" data-metric="payment_terms">
                                                    <div class="of-dist-metric-label">Zahlungsziel</div>
                                                    <div class="of-dist-metric-value">${esc(row.avg_payment_terms)} Tage</div>
                                                </div>

                                                <div class="of-dist-metric" data-metric="skonto">
                                                    <div class="of-dist-metric-label">Skonto</div>
                                                    <div class="of-dist-metric-value">${esc(row.avg_cash_discount)} %</div>
                                                </div>

                                                <div class="of-dist-metric" data-metric="availability">
                                                    <div class="of-dist-metric-label">Verfügbarkeit</div>
                                                    <div class="of-dist-metric-value">${esc(row.availability_ratio)} %</div>
                                                </div>
                                            </div>

                                            <div class="of-dist-items">
                                                ${matchingItems || '<div class="of-empty">Keine passenden Artikel für diesen Distributor.</div>'}
                                            </div>

                                            <div class="of-dist-actions">
                                                <button type="button" class="of-btn" data-distributor-id="${Number(row.distributor_id)}" data-distributor-name="${esc(row.distributor_name || '')}">
                                                    Übernehmen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.querySelectorAll('#distributor-card-list [data-distributor-id]').forEach(btn => {
            btn.addEventListener('click', () => {
                applyDistributorChange(
                    Number(btn.dataset.distributorId),
                    String(btn.dataset.distributorName || '')
                );
            });
        });

        initDistributorSearch();
        initComparisonFilters(summary);
        renderComparisonCharts(summary);
    }

   function closeMaterialDetailModal() {
        const modal = document.getElementById('material-detail-modal');
        const compareBody = document.getElementById('material-detail-compare-body');
        const historyBody = document.getElementById('material-detail-history-body');

        state.materialDetail = {
            rowIndex: null,
            rowData: null,
            comparison: null,
            selectedOption: null
        };

        if (modal) modal.style.display = 'none';
        if (compareBody) compareBody.innerHTML = `<div class="of-empty">Lade Vergleich...</div>`;
        if (historyBody) historyBody.innerHTML = `<div class="of-empty">Lade Historie...</div>`;

        switchMaterialModalTab('vergleich');
    }

    window.closeMaterialDetailModal = closeMaterialDetailModal;

    async function applySingleMaterialOption(option) {
        const row = state.materialDetail.rowData;
        if (!row || !option) {
            alert('Keine Materialdaten vorhanden.');
            return;
        }

        const url = folderApp.dataset.materialChangeUrl;
        if (!url) {
            alert('Keine Änderungs-URL gefunden.');
            return;
        }

        if (isOfferLockedByWorkflow()) {
            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
            return;
        }

        try {
            const payload = {
                distributor_id: Number(option.distributor_id),
                items: [{
                    product_id: row.product_id,
                    article_no: row.article_no,
                    name: row.name,
                    qty: row.qty,
                    unit: row.unit,
                    current_distributor_id: row.distributor_id,
                    current_distributor_price_id: row.distributor_price_id
                }]
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Material konnte nicht aktualisiert werden.');
            }

            closeMaterialDetailModal();
            await loadFolderData();

            showCustomToast(
                'Material aktualisiert',
                `${option.distributor_name || 'Distributor'} wurde übernommen.`
            );
        } catch (error) {
            alert(error.message || 'Material konnte nicht aktualisiert werden.');
        }
    }

    window.applySingleMaterialOption = applySingleMaterialOption;

    function renderMaterialDetailModal(row, comparisonData) {
        const modal = document.getElementById('material-detail-modal');
        const title = document.getElementById('material-detail-title');
        const sub = document.getElementById('material-detail-sub');
        const compareBody = document.getElementById('material-detail-compare-body');
        const historyBody = document.getElementById('material-detail-history-body');

        if (!modal || !title || !sub || !compareBody || !historyBody) return;

        const item = Array.isArray(comparisonData?.items) ? comparisonData.items[0] : null;
        const options = Array.isArray(item?.options) ? item.options : [];
        const materialHistory = normalizeMaterialHistoryEntries(row?.material_history);

        title.textContent = row.name || 'Materialdetails';
        sub.textContent = `${row.article_no || '-'} · ${row.section_title || '-'} · ${row.parent_title || '-'}`;

        historyBody.innerHTML = buildMaterialHistoryHtml(materialHistory);

        if (!options.length) {
            compareBody.innerHTML = `
                <div class="of-empty">Keine Preisvergleichsdaten für diese Position gefunden.</div>
            `;

            initMaterialModalTabs();
            switchMaterialModalTab('historie');
            modal.style.display = 'flex';
            return;
        }

        const currentOption =
            options.find(opt =>
                Number(opt.distributor_id || 0) === Number(row.distributor_id || 0) ||
                Number(opt.distributor_price_id || 0) === Number(row.distributor_price_id || 0)
            ) || null;

        const cheapestOption = [...options].sort((a, b) => Number(a.effective_price || 0) - Number(b.effective_price || 0))[0];
        const currentLineTotal = Number(row.total || 0);
        const cheapestLineTotal = Number(cheapestOption?.line_total || 0);
        const savings = currentLineTotal - cheapestLineTotal;

        compareBody.innerHTML = `
            <div class="of-material-detail-grid">
                <div class="of-material-detail-card">
                    <div class="of-material-detail-head">
                        <div class="of-material-detail-title">Aktueller Stand</div>
                        <span class="of-badge">Aktuell</span>
                    </div>
                    <div class="of-material-detail-body">
                        <div class="of-material-kv">
                            <div class="of-material-kv-label">Material</div>
                            <div class="of-material-kv-value">${esc(row.name || '-')}</div>

                            <div class="of-material-kv-label">Art.-Nr.</div>
                            <div class="of-material-kv-value">${esc(row.article_no || '-')}</div>

                            <div class="of-material-kv-label">Lieferant</div>
                            <div class="of-material-kv-value">${esc(row.distributor_name || '-')}</div>

                            <div class="of-material-kv-label">Menge</div>
                            <div class="of-material-kv-value">${esc(row.qty)} ${esc(row.unit || '')}</div>

                            <div class="of-material-kv-label">Einzelpreis</div>
                            <div class="of-material-kv-value">${esc(money(row.unit_price || 0))}</div>

                            <div class="of-material-kv-label">Gesamt</div>
                            <div class="of-material-kv-value">${esc(money(currentLineTotal))}</div>
                        </div>
                    </div>
                </div>

                <div class="of-material-detail-card">
                    <div class="of-material-detail-head">
                        <div class="of-material-detail-title">Günstigste Alternative</div>
                        <span class="of-badge">${cheapestOption ? 'Empfehlung' : 'Keine Alternative'}</span>
                    </div>
                    <div class="of-material-detail-body">
                        <div class="of-material-kv">
                            <div class="of-material-kv-label">Lieferant</div>
                            <div class="of-material-kv-value">${esc(cheapestOption?.distributor_name || '-')}</div>

                            <div class="of-material-kv-label">Art.-Nr.</div>
                            <div class="of-material-kv-value">${esc(cheapestOption?.article_no || '-')}</div>

                            <div class="of-material-kv-label">Verfügbarkeit</div>
                            <div class="of-material-kv-value">${esc(cheapestOption?.availability || '-')}</div>

                            <div class="of-material-kv-label">Preis</div>
                            <div class="of-material-kv-value">${esc(money(cheapestOption?.price || 0))}</div>

                            <div class="of-material-kv-label">EK</div>
                            <div class="of-material-kv-value">${esc(money(cheapestOption?.purchase_price || 0))}</div>

                            <div class="of-material-kv-label">Gesamt</div>
                            <div class="of-material-kv-value">${esc(money(cheapestLineTotal))}</div>
                        </div>

                        <div class="of-material-savings">
                            <span>Vorher: ${esc(money(currentLineTotal))}</span>
                            <span>Nachher: ${esc(money(cheapestLineTotal))}</span>
                            <span>Ersparnis: ${esc(money(savings))}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="of-material-detail-card" style="margin-top:18px;">
                <div class="of-material-detail-head">
                    <div class="of-material-detail-title">Alle verfügbaren Alternativen</div>
                    <span class="of-badge">${options.length} Optionen</span>
                </div>
                <div class="of-material-detail-body">
                    <div class="of-material-option-list">
                        ${options.map(option => {
                            const isCurrent =
                                Number(option.distributor_id || 0) === Number(row.distributor_id || 0) ||
                                Number(option.distributor_price_id || 0) === Number(row.distributor_price_id || 0);

                            const isBest =
                                Number(option.distributor_id || 0) === Number(cheapestOption?.distributor_id || 0) &&
                                Number(option.distributor_price_id || 0) === Number(cheapestOption?.distributor_price_id || 0);

                            return `
                                <div class="of-material-option ${isBest ? 'active' : ''}">
                                    <div class="of-material-option-top">
                                        <div class="of-material-option-name">
                                            ${esc(option.distributor_name || 'Distributor')}
                                            ${isCurrent ? ' · Aktuell' : ''}
                                            ${isBest ? ' · Beste Wahl' : ''}
                                        </div>
                                        <div class="of-material-option-price">${esc(money(option.line_total || 0))}</div>
                                    </div>
                                    <div class="of-material-option-sub">
                                        Art.-Nr.: ${esc(option.article_no || '-')}
                                        <br>Preis: ${esc(money(option.price || 0))}
                                        <br>EK: ${esc(money(option.purchase_price || 0))}
                                        <br>Verfügbarkeit: ${esc(option.availability || '-')}
                                    </div>
                                    <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                                        <button
                                            type="button"
                                            class="of-btn ${isCurrent ? 'soft' : ''}"
                                            data-single-option='${JSON.stringify(option).replaceAll("'", '&#039;')}'
                                            ${isCurrent ? 'disabled' : ''}
                                        >
                                            ${isCurrent ? 'Aktuell gesetzt' : 'Übernehmen'}
                                        </button>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            </div>
        `;

        compareBody.querySelectorAll('[data-single-option]').forEach(btn => {
            btn.addEventListener('click', () => {
                try {
                    const raw = btn.getAttribute('data-single-option').replaceAll('&#039;', "'");
                    const option = JSON.parse(raw);
                    applySingleMaterialOption(option);
                } catch (e) {
                    alert('Option konnte nicht gelesen werden.');
                }
            });
        });

        initMaterialModalTabs();
        switchMaterialModalTab('vergleich');
        modal.style.display = 'flex';
    }
    
   
    async function openMaterialDetailModal(rowIndex) {
        const { materialRows } = getStructureRows();
        const row = materialRows[rowIndex];

        if (!row) return;

        if (!row.product_id) {
            alert('Für diese Position ist kein Preisvergleich verfügbar.');
            return;
        }

        state.materialDetail.rowIndex = rowIndex;
        state.materialDetail.rowData = row;

        const modal = document.getElementById('material-detail-modal');
        const compareBody = document.getElementById('material-detail-compare-body');
        const historyBody = document.getElementById('material-detail-history-body');
        const title = document.getElementById('material-detail-title');
        const sub = document.getElementById('material-detail-sub');

        if (title) title.textContent = row.name || 'Materialdetails';
        if (sub) sub.textContent = 'Preisvergleich wird geladen...';
        if (compareBody) compareBody.innerHTML = `<div class="of-empty">Vergleichsdaten werden geladen...</div>`;
        if (historyBody) historyBody.innerHTML = `<div class="of-empty">Historie wird geladen...</div>`;
        switchMaterialModalTab('vergleich');
        if (modal) modal.style.display = 'flex';

        try {
            const url = folderApp.dataset.materialComparisonUrl;
            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    items: [{
                        product_id: row.product_id,
                        name: row.name,
                        qty: row.qty,
                        unit: row.unit,
                        article_no: row.article_no,
                        current_distributor_id: row.distributor_id,
                        current_distributor_price_id: row.distributor_price_id,
                        current_price: row.unit_price
                    }]
                })
            });

            if (!json.success) {
                throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
            }

            state.materialDetail.comparison = json;
            renderMaterialDetailModal(row, json);
        } catch (error) {
            if (compareBody) {
                compareBody.innerHTML = `<div class="of-empty">${esc(error.message || 'Vergleich konnte nicht geladen werden.')}</div>`;
            }

            if (historyBody) {
                const materialHistory = normalizeMaterialHistoryEntries(row?.material_history);
                historyBody.innerHTML = buildMaterialHistoryHtml(materialHistory);
            }

            initMaterialModalTabs();
            switchMaterialModalTab('historie');
        }
    }

    window.openMaterialDetailModal = openMaterialDetailModal;

    function updateMaterialSelectionState() {
        const rowChecks = Array.from(document.querySelectorAll('.material-row-check'));
        const checkedRows = rowChecks.filter(cb => cb.checked);
        const allChecked = rowChecks.length > 0 && checkedRows.length === rowChecks.length;

        rowChecks.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.toggle('is-selected', cb.checked);
        });

        const selectAllA = document.getElementById('material-select-all');
        const selectAllB = document.getElementById('material-select-all-head');
        const selectedInfo = document.getElementById('material-selected-info');

        if (selectAllA) selectAllA.checked = allChecked;
        if (selectAllB) selectAllB.checked = allChecked;
        if (selectedInfo) selectedInfo.textContent = `${checkedRows.length} ausgewählt`;

       if (checkedRows.length) {
            refreshSmartMaterialSidebar();
        } else {
            hideSmartMaterialSidebar();
        }
    }
 

    function initMaterialStatusListeners() {
        const { materialRows } = getStructureRows();

        document.querySelectorAll('.material-move-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.stopPropagation();

                const rowIndex = Number(this.dataset.rowIndex);
                const moveTo = String(this.dataset.moveTo || 'offen');
                const row = materialRows[rowIndex];

                if (!row) {
                    await loadFolderData();
                    return;
                }

                if (!isStatusEditableMaterialRow(row)) {
                    renderMaterialList();
                    return;
                }

                const result = await openMaterialMoveModal([row], moveTo, 'single');
                if (!result) return;

                await updateMaterialOrderStatus(
                    result.rows,
                    result.move_to,
                    result.move_qty,
                    result.source_status
                );
            });
        });

        document.querySelectorAll('.material-final-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.stopPropagation();

                const rowIndex = Number(this.dataset.rowIndex);
                const sourceStatus = String(this.dataset.sourceStatus || '').toLowerCase();
                const row = materialRows[rowIndex];

                if (!row) {
                    await loadFolderData();
                    return;
                }

                if (!isStatusEditableMaterialRow(row)) {
                    renderMaterialList();
                    return;
                }

                if (!['lager', 'bestellen'].includes(sourceStatus)) {
                    showCustomToast('Nicht möglich', 'Final ist nur aus Lager oder Bestellen möglich.', 'error');
                    return;
                }

                const result = await openMaterialFinalModal([row], sourceStatus);
                if (!result) return;

                await updateMaterialFinalStatus(
                    result.rows,
                    result.source_status,
                    result.final_qty,
                    result.remaining_to,
                    result.reason
                );
            });
        });

        const bulkSelect = document.getElementById('bulk-status-select');
        const bulkApply = document.getElementById('bulk-status-apply');

        if (bulkSelect && bulkApply) {
            bulkSelect.addEventListener('click', e => e.stopPropagation());

            bulkSelect.addEventListener('change', () => {
                bulkApply.style.display = bulkSelect.value ? 'inline-flex' : 'none';
            });

            bulkApply.addEventListener('click', async (e) => {
                e.stopPropagation();

                const selectedRows = getSelectedMaterialRows(true);

                if (!selectedRows.length) {
                    alert('Bitte wählen Sie zuerst echte Produktpositionen aus.');
                    return;
                }

                const moveTo = bulkSelect.value;
                if (!moveTo) {
                    alert('Bitte zuerst ein Ziel auswählen.');
                    return;
                }

                const result = await openMaterialMoveModal(selectedRows, moveTo, 'bulk');
                if (!result) return;

                await updateMaterialOrderStatus(
                    result.rows,
                    result.move_to,
                    result.move_qty,
                    result.source_status
                );

                bulkSelect.value = '';
                bulkApply.style.display = 'none';
            });
        }
    }
    function setAllMaterialRows(checked) {
        document.querySelectorAll('.material-row-check').forEach(cb => {
            cb.checked = checked;
        });

        updateMaterialSelectionState();
    }

    function initMaterialSelection() {
        const selectAllA = document.getElementById('material-select-all');
        const selectAllB = document.getElementById('material-select-all-head');
        const rowChecks = document.querySelectorAll('.material-row-check');

        if (selectAllA) {
            selectAllA.addEventListener('change', function () {
                setAllMaterialRows(this.checked);
            });
        }

        if (selectAllB) {
            selectAllB.addEventListener('change', function () {
                setAllMaterialRows(this.checked);
            });
        }

        rowChecks.forEach(cb => {
            cb.addEventListener('change', updateMaterialSelectionState);

            const row = cb.closest('tr');
            if (row) {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('input, button, a, label, select, option, textarea')) return;

                    const rowIndex = Number(cb.dataset.rowIndex);
                    if (!Number.isNaN(rowIndex)) {
                        openMaterialDetailModal(rowIndex);
                    }
                });
            }
        });

        updateMaterialSelectionState();
    }

    async function updateMaterialFinalStatus(rowsToUpdate, sourceStatus, finalQty, remainingTo, reason) {
        const url = folderApp.dataset.materialFinalUrl;
        if (!url) {
            showCustomToast('Fehler', 'Route für Final-Update nicht gefunden.', 'error');
            return;
        }

        if (isOfferLockedByWorkflow()) {
            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
            return;
        }

        const validRows = (Array.isArray(rowsToUpdate) ? rowsToUpdate : []).filter(row => isStatusEditableMaterialRow(row));

        if (!validRows.length) {
            showCustomToast('Fehler', 'Keine gültigen Produktpositionen ausgewählt.', 'error');
            return;
        }

        const qty = Number(finalQty || 0);
        if (!(qty > 0)) {
            showCustomToast('Fehler', 'Bitte eine gültige Final-Menge größer als 0 eingeben.', 'error');
            return;
        }

        try {
            const payload = {
                items: validRows.map(row => ({
                    product_id: Number(row.product_id || 0) || null,
                    component_id: Number(row.component_id || 0) || null,
                    article_no: row.article_no || '',
                    source_status: sourceStatus,
                    final_qty: qty,
                    remaining_to: remainingTo,
                    reason: reason || ''
                }))
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Finale Materialliste konnte nicht aktualisiert werden.');
            }

            await loadFolderData();

            showCustomToast(
                'Final List aktualisiert',
                `${validRows.length} Position(en) wurden final bestätigt.`
            );
        } catch (error) {
            showCustomToast(
                'Fehler',
                error.message || 'Finale Materialliste konnte nicht aktualisiert werden.',
                'error'
            );
            await loadFolderData();
        }
    }

    function renderMaterialList() {
        const wrap = document.getElementById('material-list-wrap');
        const printWrap = document.getElementById('material-print-wrap');
        const badge = document.getElementById('material-count-badge');

        if (!wrap || !printWrap) return;

        const { materialRows } = getStructureRows();
        const baseCols = getMaterialCols();
        const documentStatus = getDocumentStatus();
        const isOfferStatus = documentStatus === 'offer';
        const isDealStatus = documentStatus === 'deal';

        const cols = {
            ...baseCols,
            bezug: !isOfferStatus
        };

        const numberFormatter = new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const formatQty = (value) => numberFormatter.format(Number(value || 0));

        const filterLabelMap = {
            all: 'Alle',
            offen: 'Offen',
            lager: 'Lager',
            bestellen: 'Bestellen',
            final: 'Kommissionen Materialliste'
        };

        const normalizedRows = materialRows.map((row, originalIndex) => ({
            ...row,
            __originalIndex: originalIndex,
            order_status: row?.order_status || 'offen',
        }));

        const realRows = normalizedRows.filter(row => !isContainerMaterialRow(row));

        const activeFilterBefore = state.materialFilter || 'all';

        if (!['all', 'offen', 'lager', 'bestellen', 'final'].includes(activeFilterBefore)) {
            state.materialFilter = 'all';
        }

        if (isOfferStatus && state.materialFilter !== 'all') {
            state.materialFilter = 'all';
        }

        const activeFilter = state.materialFilter || 'all';
        const allowExecutionActions = (isOfferStatus || isDealStatus);
        const showBezugColumn = cols.bezug === true;

        const qtyOffen = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'offen'), 0);
        const qtyLager = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'lager'), 0);
        const qtyBestellen = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'bestellen'), 0);
        const qtyFinal = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'final'), 0);

        const filteredRows = realRows.filter(row => {
            if (activeFilter === 'all') return true;
            return getRowQtyForFilter(row, activeFilter) > 0;
        });

        const quantityTotalAll = realRows.reduce((sum, row) => sum + getRowTotalQty(row), 0);
        const quantityTotalFiltered = filteredRows.reduce((sum, row) => {
            return sum + Number(
                activeFilter === 'all'
                    ? getRowTotalQty(row)
                    : getRowQtyForFilter(row, activeFilter)
            );
        }, 0);

        const compareBtn = document.getElementById('material-compare-btn');
        if (compareBtn) {
            compareBtn.disabled = !allowExecutionActions;
            compareBtn.title = allowExecutionActions
                ? ''
                : 'Preisvergleich ist für diesen Dokumentstatus nicht erlaubt.';
        }

        const subCountAll = document.getElementById('mat-subcount-all');
        const subCountOffen = document.getElementById('mat-subcount-offen');
        const subCountLager = document.getElementById('mat-subcount-lager');
        const subCountBestellen = document.getElementById('mat-subcount-bestellen');
        const subCountFinal = document.getElementById('mat-subcount-final');

        if (subCountAll) subCountAll.textContent = formatQty(quantityTotalAll);
        if (subCountOffen) subCountOffen.textContent = formatQty(qtyOffen);
        if (subCountLager) subCountLager.textContent = formatQty(qtyLager);
        if (subCountBestellen) subCountBestellen.textContent = formatQty(qtyBestellen);
        if (subCountFinal) subCountFinal.textContent = formatQty(qtyFinal);

        document.querySelectorAll('.material-subtab-btn').forEach(btn => {
            const filter = btn.dataset.materialFilter || 'all';
            const shouldHide = isOfferStatus && filter !== 'all';

            btn.style.display = shouldHide ? 'none' : '';
            btn.classList.toggle('active', filter === activeFilter);
        });

        if (badge) {
            badge.innerHTML = `
                ${formatQty(quantityTotalFiltered)} / ${formatQty(quantityTotalAll)} Gesamtmenge
                <span style="margin-left:8px; color:#6b7280;">Ansicht: ${esc(filterLabelMap[activeFilter] || 'Alle')}</span>
                <span style="color:#10b981; margin-left:8px;">Lager: ${formatQty(qtyLager)}</span> |
                <span style="color:#ef4444;">Bestellen: ${formatQty(qtyBestellen)}</span> |
                <span style="color:#f59e0b;">Offen: ${formatQty(qtyOffen)}</span> |
                <span style="color:#2563eb; margin-left:8px;">Kommissionen Materialliste: ${formatQty(qtyFinal)}</span>
            `;
        }

        if (!normalizedRows.length) {
            wrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;
            printWrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;

            setText('print-material-count', '0');
            setText('print-material-qty-total', '0,00');

            renderTabCounts();
            hideSmartMaterialSidebar();
            return;
        }

        const buildImageCell = (row) => {
            const imageUrl = getRowImage(row);

            if (!cols.image) return '';

            return `
                <td style="width:76px;">
                    ${
                        imageUrl
                            ? `<img src="${esc(imageUrl)}" style="width:52px;height:52px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;">`
                            : `<div style="width:52px;height:52px;border-radius:12px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px;font-weight:800;">Kein Bild</div>`
                    }
                </td>
            `;
        };

        function buildStatusSelect(row, rowIndex) {
            if (!showBezugColumn) return '';

            if (!isStatusEditableMaterialRow(row)) {
                return `
                    <td>
                        <span class="of-badge" style="opacity:.75;">
                            Ordner / Gruppe
                        </span>
                    </td>
                `;
            }

            const allocation = getRowAllocation(row);
            const currentFilter = state.materialFilter || 'all';

            const canFinalizeFromCurrentTab =
                (currentFilter === 'lager' || currentFilter === 'bestellen') &&
                Number(allocation[currentFilter] || 0) > 0;

            return `
                <td>
                    <div style="display:flex; flex-direction:column; gap:8px; min-width:220px;">
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            <button
                                type="button"
                                class="of-btn soft material-move-btn"
                                data-row-index="${rowIndex}"
                                data-move-to="offen"
                                style="padding:6px 10px; font-size:12px;"
                            >
                                Offen (${Number(allocation.offen || 0).toFixed(2)})
                            </button>

                            <button
                                type="button"
                                class="of-btn soft material-move-btn"
                                data-row-index="${rowIndex}"
                                data-move-to="lager"
                                style="padding:6px 10px; font-size:12px;"
                            >
                                Lager (${Number(allocation.lager || 0).toFixed(2)})
                            </button>

                            <button
                                type="button"
                                class="of-btn soft material-move-btn"
                                data-row-index="${rowIndex}"
                                data-move-to="bestellen"
                                style="padding:6px 10px; font-size:12px;"
                            >
                                Bestellen (${Number(allocation.bestellen || 0).toFixed(2)})
                            </button>

                            ${
                                canFinalizeFromCurrentTab
                                    ? `
                                        <button
                                            type="button"
                                            class="of-btn soft material-final-btn"
                                            data-row-index="${rowIndex}"
                                            data-source-status="${currentFilter}"
                                            style="padding:6px 10px; font-size:12px;"
                                        >
                                            Final (${Number(allocation.final || 0).toFixed(2)})
                                        </button>
                                    `
                                    : ''
                            }
                        </div>

                        <div class="of-sub" style="margin:0;">
                            Status: ${esc(String(row.order_status || 'offen'))}
                        </div>
                    </div>
                </td>
            `;
        }

        const buildRow = (row) => {
            const cells = [];
            const rowIndex = row.__originalIndex;
            const hierarchyPadding = Math.max(0, (Number(row.hierarchy_level || 1) - 1) * 22);
            const visibleQtyTotal = activeFilter === 'all'
                ? getRowTotalQty(row)
                : getRowQtyForFilter(row, activeFilter);

            cells.push(`
                <td class="of-table-check">
                    ${
                        isContainerMaterialRow(row)
                            ? ''
                            : `<input type="checkbox" class="of-check material-row-check" data-row-index="${rowIndex}">`
                    }
                </td>
            `);

            if (cols.image) {
                cells.push(buildImageCell(row));
            }

            if (cols.position) {
                cells.push(`<td style="white-space:nowrap;font-weight:900;">${esc(row.position_no || '-')}</td>`);
            }

            cells.push(`
                <td>
                    <div class="of-mat-name" style="padding-left:${hierarchyPadding}px;">
                        <div class="of-mat-title">${esc(row.name || '-')}</div>
                        <div class="of-mat-meta">
                            <span class="of-mat-chip">${esc(row.level || '-')}</span>
                            ${row.parent_title && row.parent_title !== row.name ? `<span class="of-mat-chip">${esc(row.parent_title)}</span>` : ''}
                            ${row.section_title ? `<span class="of-mat-chip">${esc(row.section_title)}</span>` : ''}
                        </div>
                        ${row.description ? `<div class="of-mat-desc">${esc(row.description)}</div>` : ''}
                    </div>
                </td>
            `);

            if (cols.article_no) {
                cells.push(`<td>${esc(row.article_no || '-')}</td>`);
            }

            if (cols.distributor_article_no) {
                cells.push(`<td>${esc(row.distributor_article_no || '-')}</td>`);
            }

            if (cols.distributor) {
                cells.push(`<td>${esc(row.distributor_name || '-')}</td>`);
            }

            if (cols.type) {
                cells.push(`<td>${esc(row.type_label || '-')}</td>`);
            }

            if (showBezugColumn) {
                cells.push(buildStatusSelect(row, rowIndex));
            }

            if (cols.qty) {
                cells.push(`<td class="num">${esc(Number(row.qty || 0).toFixed(2))}</td>`);
            }

            if (cols.qty_total) {
                cells.push(`<td class="num">${esc(Number(visibleQtyTotal || 0).toFixed(2))}</td>`);
            }

            if (cols.unit) {
                cells.push(`<td>${esc(row.unit || '-')}</td>`);
            }

            if (cols.ek_price) {
                cells.push(`<td class="num">${esc(money(row.ek_price || 0))}</td>`);
            }

            if (cols.ek_total) {
                cells.push(`<td class="num">${esc(money(row.ek_total || 0))}</td>`);
            }

            if (cols.unit_price) {
                cells.push(`<td class="num">${esc(money(row.unit_price || 0))}</td>`);
            }

            if (cols.total) {
                cells.push(`<td class="num">${esc(money(row.total || 0))}</td>`);
            }

            return `<tr class="of-material-row-click" data-material-row="${rowIndex}">${cells.join('')}</tr>`;
        };

        const th = [];
        th.push(`<th class="of-table-check"><input type="checkbox" class="of-check" id="material-select-all-head"></th>`);
        if (cols.image) th.push(`<th>Bild</th>`);
        if (cols.position) th.push(`<th>Pos.</th>`);
        th.push(`<th>Material</th>`);
        if (cols.article_no) th.push(`<th>Hersteller-Nr.</th>`);
        if (cols.distributor_article_no) th.push(`<th>Lieferant-Nr.</th>`);
        if (cols.distributor) th.push(`<th>Lieferant</th>`);
        if (cols.type) th.push(`<th>Typ</th>`);
        if (showBezugColumn) th.push(`<th>Bezug</th>`);
        if (cols.qty) th.push(`<th class="num">Menge</th>`);
        if (cols.qty_total) th.push(`<th class="num">Gesamtmenge</th>`);
        if (cols.unit) th.push(`<th>Einheit</th>`);
        if (cols.ek_price) th.push(`<th class="num">EK / Einheit</th>`);
        if (cols.ek_total) th.push(`<th class="num">EK gesamt</th>`);
        if (cols.unit_price) th.push(`<th class="num">VK / Einheit</th>`);
        if (cols.total) th.push(`<th class="num">VK gesamt</th>`);

        const currentFilterLabel = filterLabelMap[activeFilter] || 'Alle';

        const materialTableHtml = `
            <div class="of-material-toolbar">
                <div class="of-material-toolbar-left" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <label class="of-selected-badge" style="${allowExecutionActions ? '' : 'opacity:.6; pointer-events:none;'}">
                        <input type="checkbox" class="of-check" id="material-select-all" ${allowExecutionActions ? '' : 'disabled'}>
                        Alle auswählen
                    </label>

                    <span class="of-selected-badge" id="material-selected-info">0 ausgewählt</span>

                    <span class="of-badge">
                        Ansicht: ${esc(currentFilterLabel)}
                    </span>

                    <span class="of-badge">
                        Gesamtmenge: ${esc(formatQty(quantityTotalFiltered))}
                    </span>

                    ${
                        showBezugColumn
                            ? `
                                <select id="bulk-status-select" class="of-btn soft" style="height:36px;">
                                    <option value="" selected disabled>Markierte ändern in...</option>
                                    <option value="lager">Lager</option>
                                    <option value="bestellen">Bestellen</option>
                                    <option value="offen">Offen</option>
                                </select>

                                <button type="button" class="of-btn" id="bulk-status-apply" style="height:36px; display:none;">
                                    Anwenden
                                </button>
                            `
                            : `
                                <span class="of-badge" style="background:#eff6ff;border-color:#bfdbfe;color:#74b2d4;">
                                    Bezug ist im Angebot ausgeblendet
                                </span>
                            `
                    }
                </div>

                <div class="of-material-toolbar-right">
                    <details class="of-colpicker" id="material-colpicker">
                        <summary class="of-btn soft">Spalten</summary>
                        <div class="of-colpicker-menu" onclick="materialPickerKeepOpen(event)">
                            ...
                        </div>
                    </details>
                </div>
            </div>

            ${
                filteredRows.length
                    ? `
                        <div class="of-table-wrap">
                            <table class="of-table" id="material-table">
                                <thead>
                                    <tr>${th.join('')}</tr>
                                </thead>
                                <tbody>
                                    ${filteredRows.map(row => buildRow(row)).join('')}
                                </tbody>
                            </table>
                        </div>
                    `
                    : `
                        <div class="of-empty">
                            Keine Materialpositionen in der Ansicht „${esc(currentFilterLabel)}“ vorhanden.
                        </div>
                    `
            }
        `;

        wrap.innerHTML = materialTableHtml;

        const printRowsBase = normalizedRows.filter(row => !isContainerMaterialRow(row));
        const printRows = activeFilter === 'all'
            ? printRowsBase
            : printRowsBase.filter(row => getRowQtyForFilter(row, activeFilter) > 0);

        const printRowsHtml = printRows.length
            ? `
                <div class="of-table-wrap">
                    <table class="of-table">
                        <thead>
                            <tr>
                                <th>Pos.</th>
                                <th>Bild</th>
                                <th>Material</th>
                                <th>Hersteller-Nr.</th>
                                <th>Lieferant-Nr.</th>
                                <th>Menge</th>
                                <th>Gesamtmenge</th>
                                <th>Einheit</th>
                                ${showBezugColumn ? '<th>Bezug</th>' : ''}
                                <th class="of-table-check">✓</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${printRows.map(row => {
                                const imageUrl = getRowImage(row);
                                const hierarchyPadding = Math.max(0, (Number(row.hierarchy_level || 1) - 1) * 22);
                                const printQtyTotal = activeFilter === 'all'
                                    ? getRowTotalQty(row)
                                    : getRowQtyForFilter(row, activeFilter);

                                return `
                                    <tr>
                                        <td style="white-space:nowrap;font-weight:900;">${esc(row.position_no || '-')}</td>
                                        <td style="width:76px;">
                                            ${
                                                imageUrl
                                                    ? `<img src="${esc(imageUrl)}" style="width:52px;height:52px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;">`
                                                    : `<div style="width:52px;height:52px;border-radius:12px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px;font-weight:800;">Kein Bild</div>`
                                            }
                                        </td>
                                        <td>
                                            <div class="of-mat-name" style="padding-left:${hierarchyPadding}px;">
                                                <div class="of-mat-title">${esc(row.name || '-')}</div>
                                                <div class="of-mat-meta">
                                                    <span class="of-mat-chip">${esc(row.level || '-')}</span>
                                                    ${row.parent_title && row.parent_title !== row.name ? `<span class="of-mat-chip">${esc(row.parent_title)}</span>` : ''}
                                                </div>
                                                ${row.description ? `<div class="of-mat-desc">${esc(row.description)}</div>` : ''}
                                            </div>
                                        </td>
                                        <td>${esc(row.article_no || '-')}</td>
                                        <td>${esc(row.distributor_article_no || '-')}</td>
                                        <td class="num">${esc(Number(row.qty || 0).toFixed(2))}</td>
                                        <td class="num">${esc(Number(printQtyTotal || 0).toFixed(2))}</td>
                                        <td>${esc(row.unit || '-')}</td>
                                        ${
                                            showBezugColumn
                                                ? `<td>${esc(activeFilter === 'all' ? 'Gemischt' : (filterLabelMap[activeFilter] || 'Offen'))}</td>`
                                                : ''
                                        }
                                        <td class="of-table-check">
                                            <input type="checkbox" class="of-check">
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `
            : `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;

        printWrap.innerHTML = printRowsHtml;

        setText('print-material-count', String(printRows.length));
        setText(
            'print-material-qty-total',
            numberFormatter.format(
                printRows.reduce((sum, row) => {
                    return sum + Number(
                        activeFilter === 'all'
                            ? getRowTotalQty(row)
                            : getRowQtyForFilter(row, activeFilter)
                    );
                }, 0)
            )
        );

        if (filteredRows.length) {
            initMaterialSelection();

            if (showBezugColumn) {
                initMaterialStatusListeners();
            }
        } else {
            hideSmartMaterialSidebar();
        }

        renderTabCounts();
    }
    async function updateMaterialOrderStatus(rowsToUpdate, moveTo, moveQty, sourceStatus = null) {
        const url = folderApp.dataset.materialStatusUrl;
        if (!url) {
            alert('Fehler: Route für Status-Update nicht gefunden.');
            return;
        }

        

        const validRows = (Array.isArray(rowsToUpdate) ? rowsToUpdate : []).filter(row => isStatusEditableMaterialRow(row));

        if (!validRows.length) {
            alert('Keine gültigen Produktpositionen ausgewählt.');
            return;
        }

        const qty = Number(moveQty || 0);
        if (!(qty > 0)) {
            alert('Bitte eine gültige Menge größer als 0 eingeben.');
            return;
        }

        const allSelects = Array.from(document.querySelectorAll('.material-status-select'));
        const bulkSelect = document.getElementById('bulk-status-select');
        const bulkApply = document.getElementById('bulk-status-apply');

        try {
            allSelects.forEach(el => el.disabled = true);
            if (bulkSelect) bulkSelect.disabled = true;
            if (bulkApply) bulkApply.disabled = true;

            const payload = {
                items: validRows.map(row => ({
                    product_id: Number(row.product_id || 0) || null,
                    component_id: Number(row.component_id || 0) || null,
                    article_no: row.article_no || '',
                    move_to: moveTo,
                    move_qty: qty,
                    source_status: sourceStatus
                }))
            };

            const json = await fetchJson(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!json.success) {
                throw new Error(json.message || 'Materialverteilung konnte nicht aktualisiert werden.');
            }

            await loadFolderData();

            showCustomToast(
                'Materialverteilung aktualisiert',
                `${validRows.length} Position(en) wurden nach "${getMoveStatusLabel(moveTo)}" verschoben.`
            );
        } catch (error) {
            alert('Fehler beim Aktualisieren der Materialverteilung: ' + (error.message || 'Unbekannter Fehler'));
            await loadFolderData();
        } finally {
            allSelects.forEach(el => el.disabled = false);
            if (bulkSelect) bulkSelect.disabled = false;
            if (bulkApply) bulkApply.disabled = false;
        }
    }

    function renderLaborList() {
        const wrap = document.getElementById('labor-list-wrap');
        const badge = document.getElementById('labor-count-badge');

        if (!wrap) return;

        const { laborRows } = getStructureRows();
        if (badge) badge.textContent = `${laborRows.length} Lohnzeilen`;

        if (!laborRows.length) {
            wrap.innerHTML = `<div class="of-empty">Keine Lohnpositionen vorhanden.</div>`;
            renderTabCounts();
            return;
        }

        wrap.innerHTML = `
            <div class="of-table-wrap">
                <table class="of-table">
                    <thead>
                        <tr>
                            <th>Sektion</th>
                            <th>Hauptposition</th>
                            <th>Leistung</th>
                            <th>Qualifikation</th>
                            <th class="num">Menge</th>
                            <th>Einheit</th>
                            <th class="num">Satz</th>
                            <th class="num">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${laborRows.map(row => `
                            <tr>
                                <td>${esc(row.section_title)}</td>
                                <td>${esc(row.parent_title)}</td>
                                <td>${esc(row.labor_title)}</td>
                                <td>${esc(row.qualification_name)}</td>
                                <td class="num">${esc(row.qty)}</td>
                                <td>${esc(row.unit)}</td>
                                <td class="num">${esc(money(row.rate))}</td>
                                <td class="num">${esc(money(row.total))}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        renderTabCounts();
    }

    function initAttachmentSearch() {
        const input = document.getElementById('attachment-search-input');
        if (!input || input.dataset.ready === '1') return;

        input.addEventListener('input', () => {
            renderPrintFiles();
        });

        input.dataset.ready = '1';
    }

    function initAttachmentDropzone() {
        const dz = document.getElementById('attachment-dropzone');
        const input = document.getElementById('print-files-input');
        const pickBtn = document.getElementById('pick-files-btn');
        const uploadForm = document.getElementById('print-files-upload-form');

        if (!dz || !input || !uploadForm) return;

        if (pickBtn && !pickBtn.dataset.ready) {
            pickBtn.addEventListener('click', () => input.click());
            pickBtn.dataset.ready = '1';
        }

        ['dragenter', 'dragover'].forEach(evt => {
            dz.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.add('of-dropzone-over');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dz.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('of-dropzone-over');
            });
        });

        dz.addEventListener('drop', e => {
            const files = e.dataTransfer?.files;
            if (!files || !files.length) return;
            input.files = files;
        });
    }

    async function uploadPrintFiles(event) {
        event.preventDefault();

        const url = folderApp.dataset.attachmentsUploadUrl;
        const input = document.getElementById('print-files-input');
        const offerId = folderApp.dataset.offerId || '';

        if (!url) {
            alert('Keine Upload-URL gefunden.');
            return;
        }

        if (!input || !input.files || !input.files.length) {
            alert('Bitte zuerst Dateien auswählen.');
            return;
        }

        try {
            const formData = new FormData();
            if (offerId) formData.append('offer_id', offerId);

            Array.from(input.files).forEach(file => {
                formData.append('files[]', file);
            });

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: formData
            });

            const json = await response.json();

            if (!response.ok || !json.success) {
                throw new Error(json.message || 'Dateien konnten nicht hochgeladen werden.');
            }

            state.attachments = safeArray(json.attachments);
            input.value = '';
            renderPrintFiles();

            showCustomToast('Upload erfolgreich', 'Die Dateien wurden hochgeladen.');
        } catch (error) {
            alert(error.message || 'Dateien konnten nicht hochgeladen werden.');
        }
    }

    async function deleteAttachment(attachmentId) {
        const folderId = folderApp.dataset.folderId;
        const urlTemplate = `/admin/offers/folders/${folderId}/attachments/${attachmentId}`;

        const confirmed = window.confirm('Soll diese Datei wirklich gelöscht werden?');
        if (!confirmed) return;

        try {
            const json = await fetchJson(urlTemplate, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (!json.success) {
                throw new Error(json.message || 'Datei konnte nicht gelöscht werden.');
            }

            state.attachments = safeArray(json.attachments);
            renderPrintFiles();

            showCustomToast('Datei gelöscht', 'Die Datei wurde entfernt.');
        } catch (error) {
            alert(error.message || 'Datei konnte nicht gelöscht werden.');
        }
    }

    window.deleteAttachment = deleteAttachment;

    async function saveAttachmentOrder(ids) {
        const url = folderApp.dataset.attachmentsSortUrl;

        const json = await fetchJson(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ ids })
        });

        if (!json.success) {
            throw new Error(json.message || 'Sortierung konnte nicht gespeichert werden.');
        }

        state.attachments = safeArray(json.attachments);
        renderPrintFiles();
    }

    function initAttachmentSorting() {
        const list = document.getElementById('attachment-sortable-list');
        if (!list || list.dataset.sortableReady === '1') return;
        if (typeof Sortable === 'undefined') return;

        new Sortable(list, {
            animation: 180,
            ghostClass: 'of-drag-ghost',
            chosenClass: 'of-drag-chosen',
            dragClass: 'of-drag-chosen',
            onEnd: async function () {
                try {
                    const ids = Array.from(list.querySelectorAll('[data-attachment-id]'))
                        .map(el => Number(el.dataset.attachmentId))
                        .filter(Boolean);

                    await saveAttachmentOrder(ids);
                } catch (error) {
                    alert(error.message || 'Sortierung konnte nicht gespeichert werden.');
                    renderPrintFiles();
                }
            }
        });

        list.dataset.sortableReady = '1';
    }

    function renderPrintFiles() {
        const wrap = document.getElementById('print-files-list-wrap');
        const badge = document.getElementById('print-files-count-badge');
        const q = (document.getElementById('attachment-search-input')?.value || '').trim().toLowerCase();

        if (!wrap) return;

        const files = safeArray(state.attachments)
            .slice()
            .sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0));

        const filtered = files.filter(file => {
            const text = [
                file.title || '',
                file.original_name || '',
                file.mime_type || '',
                file.file_type || ''
            ].join(' ').toLowerCase();

            return !q || text.includes(q);
        });

        if (badge) badge.textContent = `${files.length} Dateien`;

        if (!files.length) {
            wrap.innerHTML = `<div class="of-empty">Keine Druckdateien vorhanden.</div>`;
            renderTabCounts();
            return;
        }

        wrap.innerHTML = `
            <div class="of-file-list sortable-enabled" id="attachment-sortable-list">
                ${filtered.map((file, index) => `
                    <div class="of-file-row" data-attachment-id="${file.id}">
                        <div class="of-file-left">
                            <div class="of-file-title">${index + 1}. ${esc(file.title || file.original_name || 'Datei')}</div>
                            <div class="of-file-meta">
                                <span class="of-badge of-file-type-badge ${esc(file.file_type || 'other')}">${esc((file.file_type || 'other').toUpperCase())}</span>
                                <span class="of-badge">${esc(file.original_name || '-')}</span>
                                <span class="of-badge">${esc(file.mime_type || '-')}</span>
                                <span class="of-badge">${esc(formatBytes(file.file_size || 0))}</span>
                                <span class="of-badge">Sortierung: ${esc(file.sort_order || index + 1)}</span>
                            </div>
                        </div>

                        <div class="of-file-actions">
                            <a href="${esc(file.file_url || '#')}" target="_blank" class="of-file-preview">Öffnen</a>
                            <button type="button" class="of-btn danger" onclick="deleteAttachment(${Number(file.id)})">Löschen</button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        initAttachmentSorting();
        renderTabCounts();
    }

    async function deleteOffer() {
        const destroyUrl = folderApp.dataset.offerDestroyUrl;

        if (!destroyUrl) {
            alert('Keine Lösch-URL für das Angebot gefunden.');
            return;
        }

        const bestaetigt = window.confirm('Möchten Sie dieses Angebot wirklich löschen?');
        if (!bestaetigt) return;

        try {
            const json = await fetchJson(destroyUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (!json.success) {
                throw new Error(json.message || 'Angebot konnte nicht gelöscht werden.');
            }

            window.location.href = @json(route('admin.offers.index'));
        } catch (error) {
            alert(error.message || 'Angebot konnte nicht gelöscht werden.');
        }
    }

    window.deleteOffer = deleteOffer;

    async function loadFolderData() {
        try {
            const url = folderApp.dataset.dataUrl;
            const json = await fetchJson(url);

            if (!json.success) {
                throw new Error(json.message || 'Daten konnten nicht geladen werden.');
            }

            state.folder = json.folder || state.folder;
            state.folder.document_status = json.document_status || state.folder.document_status;
            state.folder.offer_status = json.offer_status || state.folder.offer_status;
            state.folder.deal_status = json.deal_status || state.folder.deal_status;
            state.offer = json.offer || state.offer;
            state.detail = json.detail || state.detail;
            state.sections = safeArray(state.detail?.sections);
            state.distributors = json.distributors || {};
            state.attachments = safeArray(json.attachments || []);

            if (json.agb) {
                window.folderAgb = json.agb;
            }

            renderDocumentStatusToggle();
            renderStats();
            renderKanban();
            renderMaterialList();
            renderLaborList();
            renderHistory();
            renderPrintFiles();
            renderDocumentStatusToggle();
            renderTabCounts();
            syncAgbInputs();
            renderOfferLockState();
        } catch (error) {
            console.error(error);
        }
    }
    function initPresenceChannel() {
        if (typeof window.Echo === 'undefined') {
            console.warn('Echo ist nicht verfügbar. Presence-Liste wird nicht geladen.');
            return;
        }

        const channelName = folderApp.dataset.presenceChannel;
        if (!channelName) return;

        try {
            window.Echo.join(channelName)
                .here((users) => {
                    state.presenceUsers = safeArray(users);
                    renderPresenceUsers();
                })
                .joining((user) => {
                    const exists = state.presenceUsers.some(u => Number(u.id) === Number(user.id));
                    if (!exists) {
                        state.presenceUsers.push(user);
                        renderPresenceUsers();
                    }
                })
                .leaving((user) => {
                    state.presenceUsers = state.presenceUsers.filter(u => Number(u.id) !== Number(user.id));
                    renderPresenceUsers();
                })
                .error((error) => {
                    console.error('Presence-Fehler:', error);
                });
        } catch (error) {
            console.error('Presence-Channel konnte nicht initialisiert werden:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        state.sections = safeArray(state.detail?.sections);

        renderStats();
        renderKanban(); 
        renderMaterialList();
        renderLaborList();
        renderHistory();
        renderPresenceUsers();
        renderTabCounts();
        switchTab('uebersicht');
        initAttachmentSearch();
        initAttachmentDropzone();
        initAgbEditor();
        syncAgbInputs();


        // ----------------------------------------------------
        // ADD THIS JAVASCRIPT AT THE END OF THE DOMContentLoaded BLOCK
        // ----------------------------------------------------
       const btnLoadOffer = document.getElementById('btn-load-offer');
        const clonePromptModal = document.getElementById('clone-prompt-modal');
        const versionPromptModal = document.getElementById('version-prompt-modal');
        const btnConfirmClone = document.getElementById('btn-confirm-clone');
        const btnLoadSnapshot = document.getElementById('btn-load-snapshot');
        const btnLoadCurrent = document.getElementById('btn-load-current');

        const OFFER_STATUSES_REQUIRE_CLONE_PROMPT = [
            'sent', 'viewed', 'negotiation', 'revised', 'accepted'
        ];

        function getCurrentOfferWorkflowStatus() {
            const possibleStatuses = [
                state.folder?.offer_status,
                state.folder?.workflow_status,
                state.offer?.offer_status,
                state.offer?.status,
                state.folder?.status
            ];

            for (const value of possibleStatuses) {
                const normalized = String(value || '').trim().toLowerCase();
                if (normalized) return normalized;
            }
            return 'draft';
        }

        function shouldShowClonePromptBeforeLoad() {
            const documentStatus = String(
                state.detail?.document_status ||
                state.folder?.document_status ||
                'offer'
            ).toLowerCase();

            if (documentStatus !== 'offer') return false;

            const workflowStatus = getCurrentOfferWorkflowStatus();
            return OFFER_STATUSES_REQUIRE_CLONE_PROMPT.includes(workflowStatus);
        }

        function openClonePromptModal() {
            if (clonePromptModal) clonePromptModal.style.display = 'flex';
        }

        function closeClonePromptModal() {
            if (clonePromptModal) clonePromptModal.style.display = 'none';
        }
        window.closeClonePromptModal = closeClonePromptModal;

        if (clonePromptModal) {
            clonePromptModal.addEventListener('click', function (e) {
                if (e.target === clonePromptModal) closeClonePromptModal();
            });
        }

        // Close Version Prompt Modal on outside click
        if (versionPromptModal) {
            versionPromptModal.addEventListener('click', function (e) {
                if (e.target === versionPromptModal) versionPromptModal.style.display = 'none';
            });
        }
 
       

        // 1. Intercept "Angebot laden" click
        // Helper to handle loading logic after version is decided
        function executeLoadOffer(loadSnapshot = false) {
            // 1. Build the target URL
            let targetUrl = btnLoadOffer.href;
            if (loadSnapshot) {
                targetUrl += '&load_snapshot=1';
            }

            // 2. Check if we need to show the Clone Prompt first
            if (shouldShowClonePromptBeforeLoad()) {
                // Update the "Aktuelles ändern" button in the clone modal to use our target URL
                const cloneEditBtn = clonePromptModal.querySelector('a.of-btn.soft');
                if (cloneEditBtn) cloneEditBtn.href = targetUrl;
                
                openClonePromptModal();
            } else {
                // 3. Otherwise, directly navigate to the Editor
                window.location.href = targetUrl;
            }
        }

        // 1. Intercept the Main Button click
        if (btnLoadOffer && !btnLoadOffer.dataset.ready) {
            btnLoadOffer.addEventListener('click', function (e) {
                e.preventDefault(); // Stop default navigation
                
                const docStatus = String(state.detail?.document_status || 'offer').toLowerCase();

                // If it's a deal, ask which version they want
                if (docStatus === 'deal') {
                    versionPromptModal.style.display = 'flex';
                } else {
                    // Normal Offer behavior
                    executeLoadOffer(false);
                } 
            });
            btnLoadOffer.dataset.ready = '1';
        }
 
       // 2. User chooses "Angebot ansehen" (Snapshot Read-Only)
        if (btnLoadSnapshot) {
            btnLoadSnapshot.addEventListener('click', function() {
                versionPromptModal.style.display = 'none';
                
                // Redirect to the Editor URL with the snapshot flag
                window.location.href = btnLoadOffer.href + '&load_snapshot=1';
            });
        }

        // 3. User chooses "Auftrag bearbeiten" (Current Deal Sections)
        if (btnLoadCurrent) {
            btnLoadCurrent.addEventListener('click', function() {
                versionPromptModal.style.display = 'none';
                // Pass false so it loads the active sections, not the snapshot
                executeLoadOffer(false);
            });
        }
 
       // 2. User chooses "Ursprüngliches Angebot" (Snapshot)
        if (btnLoadSnapshot) {
            btnLoadSnapshot.addEventListener('click', function() {
                versionPromptModal.style.display = 'none';
                
                // Redirect to the Editor URL and attach the trigger
                window.location.href = btnLoadOffer.href + '&load_snapshot=1';
            });
        }

        // 3. User chooses "Aktueller Auftrag" (Current Sections)
        if (btnLoadCurrent) {
            btnLoadCurrent.addEventListener('click', function() {
                versionPromptModal.style.display = 'none';
                executeLoadOffer(false);
            });
        }

        // Existing Clone Confirm Logic
        if (btnConfirmClone && !btnConfirmClone.dataset.ready) {
            btnConfirmClone.addEventListener('click', async function () {
                this.disabled = true;
                this.textContent = 'Klone...';

                try {
                    const cloneUrl = `/admin/offers/folders/${state.folder.id}/clone`;

                    const json = await fetchJson(cloneUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            name: (state.folder?.name || 'Ordner') + ' (Kopie)',
                            clone_everything: true,
                            create_new_offer: false
                        })
                    });

                    if (!json.success) {
                        throw new Error(json.message || 'Klonen fehlgeschlagen.');
                    }

                    closeClonePromptModal();

                    if (json.redirect_url) {
                        window.location.href = json.redirect_url;
                        return;
                    }

                    if (json.folder_id) {
                        window.location.href = `/admin/offers/folders/${json.folder_id}`;
                        return;
                    }

                    throw new Error('Neue Ordner-ID wurde nicht zurückgegeben.');
                } catch (error) {
                    alert('Fehler beim Klonen: ' + (error.message || 'Unbekannter Fehler'));
                    this.disabled = false;
                    this.textContent = 'Klonen (Neu) - Empfohlen';
                }
            });

            btnConfirmClone.dataset.ready = '1';
        }
        const statusReasonModal = document.getElementById('status-reason-modal');
        if (statusReasonModal) {
            statusReasonModal.addEventListener('click', function (e) {
                if (e.target === statusReasonModal) {
                    closeStatusReasonModal(null);
                }
            });
        }
        const uploadForm = document.getElementById('print-files-upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', uploadPrintFiles);
        }

        document.querySelectorAll('.of-tab[data-tab]').forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(btn.dataset.tab);
            });
        });

        const materialFinalModal = document.getElementById('material-final-modal');
        if (materialFinalModal) {
            materialFinalModal.addEventListener('click', function (e) {
                if (e.target === materialFinalModal) {
                    closeMaterialFinalModal(null);
                }
            });
        }

        const materialDetailModal = document.getElementById('material-detail-modal');
        if (materialDetailModal) {
            materialDetailModal.addEventListener('click', function (e) {
                if (e.target === materialDetailModal) {
                    closeMaterialDetailModal();
                }
            });
        }

        document.querySelectorAll('.material-subtab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                state.materialFilter = btn.dataset.materialFilter || 'all';

                document.querySelectorAll('.material-subtab-btn').forEach(x => {
                    x.classList.toggle('active', x === btn);
                });

                renderMaterialList();
            });
        });

        const comparisonModal = document.getElementById('material-comparison-modal');
        if (comparisonModal) {
            comparisonModal.addEventListener('click', function (e) {
                if (e.target === comparisonModal) {
                    closeMaterialComparisonModal();
                }
            });
        }


        document.querySelectorAll('.of-doc-toggle').forEach(btn => {
            btn.addEventListener('click', async () => {
                const targetStatus = String(btn.dataset.docStatus || 'offer').toLowerCase();
                await changeDocumentStatusRequest(targetStatus);
            });
        });

        const documentStatusModal = document.getElementById('document-status-modal');
        if (documentStatusModal) {
            documentStatusModal.addEventListener('click', function (e) {
                if (e.target === documentStatusModal) {
                    closeDocumentStatusModal(null);
                }
            });
        }


        const materialMoveModal = document.getElementById('material-move-modal');
            if (materialMoveModal) {
                materialMoveModal.addEventListener('click', function (e) {
                    if (e.target === materialMoveModal) {
                        closeMaterialMoveModal(null);
                    }
                });
            }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMaterialComparisonModal();
                closeMaterialDetailModal();
                closeMaterialMoveModal(null);
                closeMaterialFinalModal(null);
            }
        });

        initPresenceChannel();
        await loadFolderData();
        syncAgbInputs();
    });
})();
</script>
@endpush
@endonce
