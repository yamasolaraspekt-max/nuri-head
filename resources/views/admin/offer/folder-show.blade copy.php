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
<style>
    .of-smart-side{
        position:fixed;
        top:140px;
        right:24px;
        width:360px;
        max-width:calc(100vw - 32px);
        z-index:10020;
        display:none;
    }

    .of-smart-side.show{
        display:block;
    }

    .of-smart-card{
        background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
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
            linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
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
        grid-template-columns:repeat(2, minmax(0, 1fr));
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

    .of-smart-metric-value.success{
        color:#15803d;
    }

    .of-smart-metric-value.muted{
        color:#6b7280;
    }

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

    .of-smart-row:last-child{
        border-bottom:none;
    }

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
</style>

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
        --of-blue-soft:#eff6ff;

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
        padding:22px;
        font-family:Inter,system-ui,-apple-system,sans-serif;
        color:var(--of-text);
        padding: 38px;
        padding-right: 80px;
    }

    .of-header{
        margin-bottom:18px;
        background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border:1px solid var(--of-line);
        border-radius:var(--of-radius-lg);
        box-shadow:var(--of-shadow);
        overflow:hidden;
        margin-top:108px;
    }

    .of-header-inner{
        padding:22px;
    }

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
        background:linear-gradient(135deg, var(--of-primary-soft), #ffffff);
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

    .of-btn.soft:hover{
        background:#f9fafb;
    }

    .of-btn.danger{
        color:white !important;
        background:var(--of-danger);
    }

    .of-btn.danger:hover{
        background:var(--of-danger-hover);
    }

    .of-stats{
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:16px;
        margin-bottom:18px;
    }

    @media(max-width:1200px){
        .of-stats{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
    }

    @media(max-width:640px){
        .of-stats{ grid-template-columns:1fr; }
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
        gap:8px;
        flex-wrap:wrap;
    }

    .of-tab{
        border:1px solid var(--of-line);
        background:#fff;
        color:#374151;
        border-radius:12px;
        padding:10px 14px;
        font-size:13px;
        font-weight:800;
        cursor:pointer;
        transition:var(--of-transition);
    }

    .of-tab:hover{
        background:#f9fafb;
    }

    .of-tab.active{
        background:var(--of-primary-soft);
        border-color:#d8ec9d;
        color:#55720d;
    }

    .of-shell-body{
        padding:20px;
    }

    .of-panel{ display:none; }
    .of-panel.active{ display:block; }

    .of-grid-2{
        display:grid;
        grid-template-columns:420px minmax(0, 1fr);
        gap:18px;
    }

    @media(max-width:1200px){
        .of-grid-2{ grid-template-columns:1fr; }
    }

    .of-card{
        background:var(--of-card);
        border:1px solid var(--of-line);
        border-radius:18px;
        box-shadow:var(--of-shadow-sm);
        overflow:hidden;
    }

    .of-card-h{
        padding:16px 18px;
        border-bottom:1px solid var(--of-line);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:#fafafa;
        flex-wrap:wrap;
    }

    .of-card-title{
        font-size:16px;
        font-weight:900;
        color:#111827;
        margin:0;
    }

    .of-card-b{
        padding:18px;
    }

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
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:16px;
        margin-bottom:18px;
    }

    @media(max-width:1100px){
        .of-status-overview{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
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
        grid-template-columns:repeat(4, minmax(280px, 1fr));
        gap:16px;
        align-items:start;
    }

    @media(max-width:1400px){
        .of-kanban{ grid-template-columns:repeat(2, minmax(280px, 1fr)); }
    }

    @media(max-width:760px){
        .of-kanban{ grid-template-columns:1fr; }
    }

    .of-col{
        background:#f9fafb;
        border:1px solid var(--of-line);
        border-radius:18px;
        min-height:320px;
        display:flex;
        flex-direction:column;
        overflow:hidden;
    }

    .of-col-h{
        padding:14px 16px;
        border-bottom:1px solid var(--of-line);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        background:#fff;
    }

    .of-col-name{
        font-size:14px;
        font-weight:900;
        color:#111827;
    }

    .of-col-count{
        min-width:28px;
        height:28px;
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

    .of-col[data-status="offen"] .of-col-count{
        background:#eff6ff;
        border-color:#bfdbfe;
        color:#1d4ed8;
    }

    .of-col[data-status="geprueft"] .of-col-count{
        background:#ecfdf5;
        border-color:#a7f3d0;
        color:#047857;
    }

    .of-col[data-status="verkauft"] .of-col-count{
        background:#f4fae7;
        border-color:#d9ef9d;
        color:#55720d;
    }

    .of-col[data-status="ausschuss"] .of-col-count{
        background:#fef2f2;
        border-color:#fecaca;
        color:#b91c1c;
    }

    .of-list{
        padding:14px;
        display:flex;
        flex-direction:column;
        gap:12px;
        min-height:200px;
        flex:1;
    }

    .of-item{
        background:#fff;
        border:1px solid var(--of-line);
        border-radius:14px;
        padding:14px;
        box-shadow:var(--of-shadow-sm);
        transition:var(--of-transition);
        cursor:grab;
    }

    .of-item:hover{
        border-color:#cbd5e1;
        transform:translateY(-1px);
    }

    .of-item-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
        line-height:1.4;
    }

    .of-item-desc{
        font-size:12px;
        color:var(--of-muted);
        margin-top:8px;
        line-height:1.6;
        white-space:pre-wrap;
        word-break:break-word;
    }

    .of-item-meta{
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
        margin-top:10px;
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
        grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
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

    .of-section-body{
        padding:16px;
    }

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

    .of-structure-list{
        display:flex;
        flex-direction:column;
        gap:14px;
    }

    .of-tree-section{
        border:1px solid var(--of-line);
        border-radius:18px;
        background:#fff;
        overflow:hidden;
        box-shadow:var(--of-shadow-sm);
    }

    .of-tree-section-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:16px 18px;
        background:#fafafa;
        border-bottom:1px solid var(--of-line);
        cursor:pointer;
    }

    .of-tree-section-title{
        font-size:15px;
        font-weight:900;
        color:#111827;
    }

    .of-tree-section-body{
        padding:14px 16px 16px;
        display:none;
    }

    .of-tree-section.open .of-tree-section-body{
        display:block;
    }

    .of-tree-node{
        border:1px solid #edf2f7;
        border-radius:14px;
        background:#fff;
        margin-bottom:12px;
        overflow:hidden;
    }

    .of-tree-node:last-child{
        margin-bottom:0;
    }

    .of-tree-node-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        padding:14px 16px;
        cursor:pointer;
        background:#fff;
    }

    .of-tree-node.open > .of-tree-node-body{
        display:block;
    }

    .of-tree-node-body{
        display:none;
        padding:0 16px 14px 16px;
        border-top:1px solid #f1f5f9;
        background:#fcfcfd;
    }

    .of-tree-node-left{
        min-width:0;
        flex:1;
    }

    .of-tree-node-title{
        font-size:14px;
        font-weight:900;
        color:#111827;
        line-height:1.5;
    }

    .of-tree-node-sub{
        margin-top:6px;
        font-size:12px;
        color:#6b7280;
        line-height:1.6;
        white-space:pre-wrap;
    }

    .of-tree-meta{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        margin-top:8px;
    }

    .of-tree-children{
        display:flex;
        flex-direction:column;
        gap:10px;
        margin-top:12px;
    }

    .of-tree-indent-1{ margin-left:18px; }
    .of-tree-indent-2{ margin-left:36px; }
    .of-tree-indent-3{ margin-left:54px; }

    .of-tree-row{
        border:1px solid #eef2f7;
        background:#fff;
        border-radius:12px;
        padding:12px 14px;
    }

    .of-tree-row-title{
        font-size:13px;
        font-weight:800;
        color:#111827;
    }

    .of-tree-row-sub{
        margin-top:5px;
        font-size:12px;
        color:#6b7280;
        line-height:1.6;
    }

    .of-toggle-icon{
        width:28px;
        height:28px;
        border-radius:999px;
        border:1px solid var(--of-line);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:12px;
        font-weight:900;
        color:#6b7280;
        background:#fff;
        flex-shrink:0;
    }

    .of-drag-ghost{
        opacity:.55;
        background:#f8fafc;
        border:1px dashed #94a3b8 !important;
    }

    .of-drag-chosen{
        box-shadow:0 10px 24px rgba(0,0,0,.12);
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

    .of-table td{
        color:#111827;
    }

    .of-table tr:hover td{
        background:#fcfcfd;
    }

    .of-table .num{
        text-align:right;
        white-space:nowrap;
        font-variant-numeric:tabular-nums;
    }

    .of-table .muted{
        color:#6b7280;
    }

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
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:12px;
        padding:18px 20px;
        border-bottom:1px solid var(--of-line);
        background:#fff;
    }

    @media(max-width:900px){
        .of-print-meta{
            grid-template-columns:repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width:560px){
        .of-print-meta{
            grid-template-columns:1fr;
        }
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

    .of-print-body{
        padding:20px;
    }

    .of-print-only{
        display:none;
    }

    @media print{
        body *{
            visibility:hidden !important;
        }

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

        .of-print-only{
            display:block !important;
        }

        .of-no-print{
            display:none !important;
        }

        .of-print-sheet,
        .of-table-wrap{
            border:none !important;
            box-shadow:none !important;
        }

        .of-table{
            min-width:100% !important;
        }

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

        .of-print-body{
            padding:16px 0 0 0 !important;
        }
    }


</style>

<style>
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

    .of-print-sheet .of-table thead th{
        background:#f3f4f6;
        color:#111827;
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.04em;
    }

    .of-print-sheet .of-table tbody td{
        font-size:13px;
    }

    .of-print-sheet .of-table tbody tr:nth-child(even) td{
        background:#fafafa;
    }

    .of-print-sheet .of-table-wrap{
        border-radius:14px;
    }

    .of-table-check{
        width:42px;
        text-align:center !important;
    }

    .of-check{
        width:18px;
        height:18px;
        accent-color: var(--of-primary);
        cursor:pointer;
    }

    .of-table tr.is-selected td{
        background: #f4fae7 !important;
    }

    .of-table tr.is-selected:hover td{
        background: #edf8d2 !important;
    }

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
        width:min(1400px, 100%);
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

    .of-compare-grid{
        display:grid;
        grid-template-columns:repeat(3, minmax(0, 1fr));
        gap:16px;
        margin-bottom:18px;
    }

    @media(max-width:1100px){
        .of-compare-grid{
            grid-template-columns:1fr;
        }
    }

    .of-compare-card{
        border:1px solid var(--of-line);
        border-radius:18px;
        padding:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
    }

    .of-compare-chart{
        border:1px solid var(--of-line);
        border-radius:18px;
        padding:16px;
        background:#fff;
        box-shadow:var(--of-shadow-sm);
        margin-bottom:18px;
    }

    .of-compare-table{
        margin-top:18px;
    }

    
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

    .of-compare-layout{
    display:grid;
    grid-template-columns:minmax(0, 1.15fr) minmax(380px, .85fr);
    gap:18px;
    align-items:start;
}

@media(max-width:1180px){
    .of-compare-layout{
        grid-template-columns:1fr;
    }
}

.of-compare-left,
.of-compare-right{
    min-width:0;
}

.of-compare-stats{
    display:grid;
    grid-template-columns:repeat(3, minmax(0, 1fr));
    gap:14px;
    margin-bottom:16px;
}

@media(max-width:760px){
    .of-compare-stats{
        grid-template-columns:1fr;
    }
}

.of-compare-side{
    border:1px solid var(--of-line);
    border-radius:20px;
    background:#fff;
    box-shadow:var(--of-shadow-sm);
    overflow:hidden;
    position:sticky;
    top:0;
}

.of-compare-side-head{
    padding:16px 18px;
    border-bottom:1px solid var(--of-line);
    background:#fafafa;
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
    max-height:65vh;
    overflow:auto;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.of-dist-card{
    border:1px solid var(--of-line);
    border-radius:18px;
    background:#fff;
    box-shadow:var(--of-shadow-sm);
    overflow:hidden;
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
}

.of-dist-title{
    font-size:15px;
    font-weight:900;
    color:#111827;
    line-height:1.35;
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
}

.of-dist-metrics{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:10px;
    margin-bottom:12px;
}

.of-dist-metric{
    border:1px solid #eef2f7;
    border-radius:14px;
    padding:10px 12px;
    background:#fafafa;
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
}

.of-dist-items{
    display:flex;
    flex-direction:column;
    gap:8px;
    margin-top:8px;
}

.of-dist-item{
    border:1px solid #eef2f7;
    border-radius:12px;
    padding:10px 12px;
    background:#fff;
}

.of-dist-item-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}

.of-dist-item-name{
    font-size:13px;
    font-weight:800;
    color:#111827;
}

.of-dist-item-sub{
    margin-top:5px;
    font-size:12px;
    color:#6b7280;
    line-height:1.55;
}

.of-dist-actions{
    margin-top:12px;
    display:flex;
    justify-content:flex-end;
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
    background:linear-gradient(135deg, #0f172a 0%, #14532d 100%);
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
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:10px;
    flex:0 0 auto;
}

@media(max-width:640px){
    .of-dist-metrics{
        grid-template-columns:1fr;
    }
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

.of-dist-items::-webkit-scrollbar{
    width:8px;
}

.of-dist-items::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:999px;
}

.of-dist-items::-webkit-scrollbar-track{
    background:transparent;
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

.of-dist-item-top .of-badge{
    flex:0 0 auto;
    white-space:nowrap;
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
    position:sticky;
    bottom:0;
    background:#fff;
    padding-top:6px;
}

.of-dist-item-sub div{
    margin-bottom:3px;
}

.of-dist-item-sub div:last-child{
    margin-bottom:0;
}

.of-dist-item-sub strong{
    color:#374151;
    font-weight:900;
}

.of-compare-layout{
    display:grid;
    grid-template-columns:minmax(0, 1.25fr) minmax(460px, .75fr);
    gap:18px;
    align-items:start;
}

@keyframes ofToastIn{
    from{ opacity:0; transform:translateY(10px); }
    to{ opacity:1; transform:translateY(0); }
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

#agb-editor{
    min-height: 360px;
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


.of-file-list.sortable-enabled .of-file-row{
    cursor:grab;
}

.of-file-row.is-hidden{
    display:none;
}

.of-file-type-badge.pdf{
    background:#eff6ff;
    border-color:#bfdbfe;
    color:#1d4ed8;
}

.of-file-type-badge.image{
    background:#ecfdf5;
    border-color:#a7f3d0;
    color:#047857;
}

.of-dropzone-over{
    border-color:#93c21c !important;
    background:#f4fae7 !important;
}

</style>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
    .of-context-menu{
        position:fixed;
        z-index:10080;
        min-width:280px;
        max-width:360px;
        background:#fff;
        border:1px solid var(--of-line);
        border-radius:18px;
        box-shadow:0 25px 70px rgba(15,23,42,.22);
        overflow:hidden;
        display:none;
    }

    .of-context-menu.show{
        display:block;
    }

    .of-context-menu-head{
        padding:14px 16px;
        border-bottom:1px solid var(--of-line);
        background:#fafafa;
    }

    .of-context-menu-title{
        font-size:13px;
        font-weight:900;
        color:#111827;
        line-height:1.45;
        word-break:break-word;
    }

    .of-context-menu-sub{
        margin-top:5px;
        font-size:11px;
        color:#6b7280;
        line-height:1.55;
    }

    .of-context-menu-body{
        padding:8px;
        display:flex;
        flex-direction:column;
        gap:4px;
        max-height:320px;
        overflow:auto;
    }

    .of-context-menu-item{
        width:100%;
        border:none;
        background:#fff;
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        text-align:left;
        padding:12px 14px;
        border-radius:12px;
        cursor:pointer;
        transition:var(--of-transition);
    }

    .of-context-menu-item:hover{
        background:#f8fafc;
    }

    .of-context-menu-item.is-current{
        background:var(--of-primary-soft);
        border:1px solid #d9ef9d;
    }

    .of-context-menu-item-main{
        min-width:0;
        flex:1;
    }

    .of-context-menu-item-title{
        font-size:13px;
        font-weight:900;
        color:#111827;
        line-height:1.4;
        word-break:break-word;
    }

    .of-context-menu-item-sub{
        margin-top:5px;
        font-size:11px;
        color:#6b7280;
        line-height:1.55;
        word-break:break-word;
    }

    .of-context-menu-price{
        font-size:12px;
        font-weight:900;
        color:#111827;
        white-space:nowrap;
        flex:0 0 auto;
    }

    .of-context-menu-empty{
        padding:18px 16px;
        color:#6b7280;
        font-size:12px;
        text-align:center;
    }

    .of-context-backdrop{
        position:fixed;
        inset:0;
        z-index:10070;
        display:none;
    }

    .of-context-backdrop.show{
        display:block;
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
    data-offer-id="{{ $offer?->id }}"
    data-offer-destroy-url="{{ $offer ? route('admin.offers.destroy', $offer->id) : '' }}"
    data-material-comparison-url="{{ route('admin.offers.folders.material-comparison', $folder) }}"
    data-material-change-url="{{ route('admin.offers.folders.material-change', $folder) }}"
    data-kanban-move-url="{{ route('admin.offers.folders.kanban.move', $folder) }}"
    data-agb-save-url="{{ route('admin.offers.folders.agb.save', $folder) }}"
    data-attachments-upload-url="{{ route('admin.offers.folders.attachments.upload', $folder) }}"
    data-attachments-sort-url="{{ route('admin.offers.folders.attachments.sort', $folder) }}"
    data-presence-channel="offer-folder.{{ $folder->id }}"
    data-default-avatar="{{ asset('images/gender/male.png') }}"
>
    <div class="of-header">
        <div class="of-header-inner">
            <div class="of-top">
                <div class="of-head-left">
                    <div class="of-icon-box">
                        <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="#6b8d12" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 3v5h5"></path>
                            <path d="M9 13h6"></path>
                            <path d="M9 17h6"></path>
                            <path d="M9 9h2"></path>
                        </svg>
                    </div>

                    <div>
                        <h1 class="of-title">{{ $folder->name ?: 'Ordner' }}</h1>
                        <div class="of-sub">
                            Angebot #{{ $offer?->id ?? $folder->offer_id ?? '-' }}
                            · Kunde: {{ $customerName ?: 'Unbekannt' }}
                            · Produkt: {{ $offer?->product?->article_group ?? 'Unbekannt' }}
                        </div>

                        <div class="of-meta-row">
                            <span class="of-meta-pill">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Mitarbeiter: {{ $employeeName ?: 'Nicht zugewiesen' }}
                            </span>

                            <span class="of-meta-pill">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <path d="M16 2v4M8 2v4M3 10h18"></path>
                                </svg>
                                Erstellt: {{ optional($detail?->created_at ?? $folder->created_at)->format('d.m.Y H:i') }}
                            </span>

                            <span class="of-meta-pill">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                </svg>
                                Aktualisiert: {{ optional($detail?->updated_at ?? $folder->updated_at)->format('d.m.Y H:i') }}
                            </span>
                        </div>

                        <div class="of-presence">
                            <div class="of-presence-label">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
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

                <div class="of-actions">
                  <a href="{{ $wizardUrl }}" class="of-btn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h6"></path>
                            <path d="M15 12h6"></path>
                            <path d="M12 3v6"></path>
                            <path d="M12 15v6"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        @if($detail)
                            Angebot laden & bearbeiten
                        @else
                            Neues Angebot erstellen
                        @endif
                    </a>

                    @if($offer?->id)
                        <button type="button" class="of-btn danger" onclick="deleteOffer()">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M8 6V4h8v2"></path>
                                <path d="M19 6l-1 14H6L5 6"></path>
                            </svg>
                            Angebot löschen
                        </button>
                    @endif

                    <a href="{{ route('admin.offers.index') }}" class="of-btn soft">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Zurück
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="of-stats">
        <div class="of-stat">
            <div class="of-stat-label">Netto</div>
            <div class="of-stat-value" id="stat-total-net">{{ number_format((float) ($detail?->total_net ?? 0), 2, ',', '.') }} €</div>
            <div class="of-stat-sub">Gesamter Nettowert</div>
        </div>

        <div class="of-stat">
            <div class="of-stat-label">Steuersatz</div>
            <div class="of-stat-value" id="stat-tax-rate">{{ number_format((float) ($detail?->tax_rate ?? 19), 2, ',', '.') }} %</div>
            <div class="of-stat-sub">Hinterlegte Steuer</div>
        </div>

        <div class="of-stat">
            <div class="of-stat-label">Brutto</div>
            <div class="of-stat-value" id="stat-total-gross">{{ number_format((float) ($detail?->total_gross ?? 0), 2, ',', '.') }} €</div>
            <div class="of-stat-sub">Gesamt inklusive Steuer</div>
        </div>

        <div class="of-stat">
            <div class="of-stat-label">Struktureinträge</div>
            <div class="of-stat-value" id="stat-items-count">0</div>
            <div class="of-stat-sub">Sektionen, Positionen, Komponenten und Lohn</div>
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


                <button type="button" class="of-tab" data-tab="pdfs">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        PDF Versionen
                        <span class="of-tab-count" id="tab-count-pdfs">0</span>
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

                <button type="button" class="of-tab" data-tab="sektionen">
                    <span class="of-tab-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <path d="M3.3 7 12 12l8.7-5"></path>
                        </svg>
                    </span>
                    <span class="of-tab-label">
                        Sektionen
                        <span class="of-tab-count" id="tab-count-sektionen">0</span>
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

                <div class="of-status-overview">
                    <div class="of-status-card">
                        <div class="of-status-name">Offen</div>
                        <div class="of-status-value" id="status-card-offen">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Geprüft</div>
                        <div class="of-status-value" id="status-card-geprueft">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Verkauft</div>
                        <div class="of-status-value" id="status-card-verkauft">0</div>
                    </div>
                    <div class="of-status-card">
                        <div class="of-status-name">Ausschuss</div>
                        <div class="of-status-value" id="status-card-ausschuss">0</div>
                    </div>
                </div>
            </div>

            <div class="of-panel" id="panel-kanban">
                <div class="of-kanban" id="kanban-columns">
                    <div class="of-col" data-status="offen">
                        <div class="of-col-h">
                            <div class="of-col-name">Offen</div>
                            <span class="of-col-count" id="count-offen">0</span>
                        </div>
                        <div class="of-list" id="col-offen">
                            <div class="of-empty">Lade Daten...</div>
                        </div>
                    </div>

                    <div class="of-col" data-status="geprueft">
                        <div class="of-col-h">
                            <div class="of-col-name">Geprüft</div>
                            <span class="of-col-count" id="count-geprueft">0</span>
                        </div>
                        <div class="of-list" id="col-geprueft">
                            <div class="of-empty">Lade Daten...</div>
                        </div>
                    </div>

                    <div class="of-col" data-status="verkauft">
                        <div class="of-col-h">
                            <div class="of-col-name">Verkauft</div>
                            <span class="of-col-count" id="count-verkauft">0</span>
                        </div>
                        <div class="of-list" id="col-verkauft">
                            <div class="of-empty">Lade Daten...</div>
                        </div>
                    </div>

                    <div class="of-col" data-status="ausschuss">
                        <div class="of-col-h">
                            <div class="of-col-name">Ausschuss</div>
                            <span class="of-col-count" id="count-ausschuss">0</span>
                        </div>
                        <div class="of-list" id="col-ausschuss">
                            <div class="of-empty">Lade Daten...</div>
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

                            <button type="button" class="of-btn soft" onclick="openMaterialComparisonModal()">
                                Preisvergleich
                            </button>

                            <button type="button" class="of-btn soft" onclick="switchTab('material-print')">
                                Materialdruck öffnen
                            </button>
                        </div>
                    </div>

                    <div class="of-card-b">
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

            <div class="of-panel" id="panel-pdfs">
                <div class="of-card">
                    <div class="of-card-h">
                        <h3 class="of-card-title">Erstellte PDFs & Versionen</h3>
                        <div class="of-inline-actions">
                            <span class="of-badge" id="pdf-count-badge">0 PDFs</span>
                        </div>
                    </div>

                    <div class="of-card-b">
                        <div class="of-file-list" id="pdf-list-wrap">
                            <div class="of-empty">Lade PDF Versionen...</div>
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
                            <div class="of-print-stat-label">Gesamt Netto</div>
                            <div class="of-print-stat-value" id="print-material-total">0,00 €</div>
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


            <div class="of-panel" id="panel-sektionen">
                <div class="of-sections" id="sections-grid">
                    <div class="of-empty">Lade Sektionen...</div>
                </div>
            </div>

            <div class="of-panel" id="panel-historie">
                <div class="of-history-placeholder">
                    <svg viewBox="0 0 24 24" width="46" height="46" fill="none" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 8v4l3 3"></path>
                        <path d="M3.05 11a9 9 0 1 1 .5 4"></path>
                        <path d="M3 16v5h5"></path>
                    </svg>
                    <div style="font-weight:900; color:#6b7280;">Historie ist vorbereitet</div>
                    <div>Hier werden später die Historien-Details angezeigt.</div>
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

<div id="of-toast-wrap" class="of-toast-wrap"></div>

<div id="of-context-backdrop" class="of-context-backdrop"></div>

<div id="material-context-menu" class="of-context-menu" aria-hidden="true">
    <div class="of-context-menu-head">
        <div class="of-context-menu-title" id="material-context-title">Material</div>
        <div class="of-context-menu-sub" id="material-context-sub">Preisvergleich</div>
    </div>
    <div class="of-context-menu-body" id="material-context-body">
        <div class="of-context-menu-empty">Keine Daten vorhanden.</div>
    </div>
</div>

<div class="of-modal-backdrop" id="pdf-viewer-modal" style="display:none;">
    <div class="of-modal" style="width: min(1200px, 95vw); height: 90vh;">
        <div class="of-modal-head">
            <div>
                <h3 class="of-card-title" id="pdf-viewer-title" style="margin:0;">Dokument.pdf</h3>
                <div class="of-sub" id="pdf-viewer-meta" style="margin-top:6px;">Version Info</div>
            </div>
            <div class="of-inline-actions">
                <a href="#" id="pdf-viewer-download" download class="of-btn">Herunterladen</a>
                <button type="button" class="of-btn soft" onclick="closePdfViewer()">Schließen</button>
            </div>
        </div>
        <div class="of-modal-body" style="padding: 0; flex: 1; background: #525659;">
            <iframe id="pdf-viewer-iframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>
@endsection

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    window.folderDefaultAgb = @json($defaultAgb);
    window.folderAgb = @json($folderAgb);
</script>
<script>
(() => {
    const folderApp = document.getElementById('folder-app');
    if (!folderApp) return;

    const STATUS_SCHLUESSEL = ['offen', 'geprueft', 'verkauft', 'ausschuss'];

    const STATUS_LABELS = {
        offen: 'Offen',
        geprueft: 'Geprüft',
        verkauft: 'Verkauft',
        ausschuss: 'Ausschuss'
    };

    const initialAttachments = @json($initialAttachments);

    const state = {
        folder: @json($folder),
        pdfs: [],
        offer: @json($offer),
        detail: @json($detail),
        sections: [],
        distributors: {},
        currentTab: 'uebersicht',
        presenceUsers: [],
        comparisonCharts: [],
        attachments: Array.isArray(initialAttachments) ? initialAttachments : [],
        contextMenu: {
            visible: false,
            rowIndex: null,
            rowData: null,
            comparison: null
        },

        smartSidebar: {
            visible: false,
            loading: false,
            summary: null
        },
        
    };

    let agbQuill = null;

    function initAgbEditor() {
        const editorEl = document.getElementById('agb-editor');
        const hiddenInput = document.getElementById('agb-text-input');

        if (!editorEl || !hiddenInput) return;
        if (typeof Quill === 'undefined') {
            console.error('Quill wurde nicht geladen.');
            return;
        }

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

        const initialHtml = hiddenInput.value || '';
        agbQuill.root.innerHTML = initialHtml;

        agbQuill.on('text-change', () => {
            hiddenInput.value = agbQuill.root.innerHTML;
        });
    }


    // --- PDF TAB RENDERING ---
    function renderPdfVersions() {
        const wrap = document.getElementById('pdf-list-wrap');
        const badge = document.getElementById('pdf-count-badge');
        const tabCount = document.getElementById('tab-count-pdfs');

        if (!wrap) return;

        const pdfs = safeArray(state.pdfs);

        if (badge) badge.textContent = `${pdfs.length} Dokumente`;
        if (tabCount) tabCount.textContent = String(pdfs.length);

        if (!pdfs.length) {
            wrap.innerHTML = `<div class="of-empty">Es wurden noch keine PDFs für dieses Angebot generiert.</div>`;
            return;
        }

        wrap.innerHTML = pdfs.map(pdf => {
            // Check if URL is valid or literally the string "null"
            const hasValidUrl = pdf.url && pdf.url !== 'null' && pdf.url !== '';
            
            return `
                <div class="of-file-row ${pdf.is_latest ? 'border-[#93c21c] bg-[#f7fee7]/30' : ''}">
                    <div class="of-file-left">
                        <div class="flex items-center gap-3">
                            <div class="of-file-title" style="${pdf.is_latest ? 'color:#55720d;' : ''}">
                                ${esc(pdf.title)}
                            </div>
                            ${pdf.is_latest ? '<span class="of-badge bg-[#93c21c] text-white border-none shadow-sm">AKTUELLSTE VERSION</span>' : '<span class="of-badge">Alte Version</span>'}
                        </div>
                        <div class="of-file-meta mt-2">
                            <span class="of-badge"><i class="fa-regular fa-clock mr-1"></i> ${esc(pdf.date)}</span>
                            <span class="of-badge">Status: ${esc(pdf.status || 'Unbekannt')}</span>
                            ${!hasValidUrl ? `<span class="of-badge" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;">Datei gelöscht/nicht verfügbar</span>` : ''}
                        </div>
                    </div>

                    <div class="of-file-actions">
                        ${hasValidUrl ? `
                            <button type="button" class="of-btn soft" onclick="openPdfViewer('${esc(pdf.url)}', '${esc(pdf.title)}', '${pdf.is_latest ? 'Aktuellste Version' : 'Alte Version'} • ${esc(pdf.date)}')">
                                Vorschau
                            </button>
                            <a href="${esc(pdf.url)}" download class="of-btn">Download</a>
                        ` : `
                            <button type="button" class="of-btn soft opacity-50 cursor-not-allowed" disabled>
                                Vorschau nicht möglich
                            </button>
                        `}
                    </div>
                </div>
            `;
        }).join('');
    }

    function openPdfViewer(url, title, metaInfo) {
        const modal = document.getElementById('pdf-viewer-modal');
        const iframe = document.getElementById('pdf-viewer-iframe');
        const titleEl = document.getElementById('pdf-viewer-title');
        const metaEl = document.getElementById('pdf-viewer-meta');
        const downloadBtn = document.getElementById('pdf-viewer-download');

        if (!modal || !iframe) return;

        titleEl.textContent = title;
        metaEl.textContent = metaInfo;
        downloadBtn.href = url;
        
        // Append #toolbar=0 to hide native browser PDF toolbars if preferred, 
        // but usually we want them so the user can zoom/print.
        iframe.src = url;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closePdfViewer() {
        const modal = document.getElementById('pdf-viewer-modal');
        const iframe = document.getElementById('pdf-viewer-iframe');

        if (modal) {
            modal.style.display = 'none';
        }
        if (iframe) {
            iframe.src = ''; // Clear memory
        }
        document.body.style.overflow = '';
    }

    // Attach to window so onclick handlers work
    window.openPdfViewer = openPdfViewer;
    window.closePdfViewer = closePdfViewer;

    function setAgbEditorHtml(html) {
        const hiddenInput = document.getElementById('agb-text-input');
        if (hiddenInput) {
            hiddenInput.value = html || '';
        }

        if (agbQuill) {
            agbQuill.root.innerHTML = html || '';
        }
    }


    

    function getAgbEditorHtml() {
        if (agbQuill) {
            return agbQuill.root.innerHTML || '';
        }

        return document.getElementById('agb-text-input')?.value || '';
    }

   function getSelectedMaterialRows(requireProductId = true) {
        const { materialRows } = getStructureRows();
        const selectedIndexes = Array.from(document.querySelectorAll('.material-row-check:checked'))
            .map(cb => Number(cb.dataset.rowIndex))
            .filter(index => !Number.isNaN(index));

        // Get everything the user actually checked
        let rows = selectedIndexes
            .map(index => materialRows[index])
            .filter(Boolean);

        // Filter out manual entries without a product ID only if required
        if (requireProductId) {
            rows = rows.filter(row => row.product_id && String(row.product_id) !== '0');
        }

        return rows;
    }

    function hideSmartMaterialSidebar() {
    const el = document.getElementById('smart-material-sidebar');
    if (el) el.classList.remove('show');

    state.smartSidebar.visible = false;
}

window.hideSmartMaterialSidebar = hideSmartMaterialSidebar;

function showSmartMaterialSidebar() {
    const el = document.getElementById('smart-material-sidebar');
    if (el) el.classList.add('show');

    state.smartSidebar.visible = true;
}

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
            current_distributor_price_id: currentRow.distributor_price_id,
            current_total: currentLineTotal,
            target_distributor_id: cheapest.distributor_id,
            target_distributor_name: cheapest.distributor_name,
            target_distributor_price_id: cheapest.distributor_price_id,
            target_price: Number(cheapest.price || 0),
            target_purchase_price: Number(cheapest.purchase_price || 0),
            target_total: cheapestLineTotal,
            availability: cheapest.availability || '-',
            changed: Number(currentRow.distributor_id || 0) !== Number(cheapest.distributor_id || 0)
                || Number(currentRow.distributor_price_id || 0) !== Number(cheapest.distributor_price_id || 0)
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
                <div class="of-smart-metric-label">Änderungen</div>
                <div class="of-smart-metric-value muted">${changedRows.length} / ${summary.count}</div>
            </div>
        </div>

        <div class="of-smart-list">
            <div class="of-smart-list-head">Empfohlene Änderungen</div>
            <div class="of-smart-list-body">
                ${
                    rows.length
                        ? rows.map(row => `
                            <div class="of-smart-row">
                                <div class="of-smart-row-title">${esc(row.name || 'Material')}</div>
                                <div class="of-smart-row-sub">
                                    ${esc(row.article_no || '-')}
                                    <br>Aktuell: ${esc(distributorName(row.current_distributor_id))}
                                    <br>Neu: ${esc(row.target_distributor_name || '-')}
                                    <br>Vorher: ${esc(money(row.current_total || 0))} · Nachher: ${esc(money(row.target_total || 0))}
                                </div>
                            </div>
                        `).join('')
                        : `<div class="of-smart-empty">Keine Daten vorhanden.</div>`
                }
            </div>
        </div>

        <div class="of-smart-actions">
            <button
                type="button"
                class="of-btn"
                onclick="confirmApplyCheapestAlternative()"
                ${changedRows.length ? '' : 'disabled'}
            >
                Günstigste Alternative übernehmen
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

    function closeMaterialContextMenu() {
        const menu = document.getElementById('material-context-menu');
        const backdrop = document.getElementById('of-context-backdrop');

        state.contextMenu.visible = false;
        state.contextMenu.rowIndex = null;
        state.contextMenu.rowData = null;
        state.contextMenu.comparison = null;

        if (menu) {
            menu.classList.remove('show');
            menu.setAttribute('aria-hidden', 'true');
        }

        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    function positionMaterialContextMenu(x, y) {
        const menu = document.getElementById('material-context-menu');
        if (!menu) return;

        menu.style.left = '0px';
        menu.style.top = '0px';
        menu.classList.add('show');
        menu.setAttribute('aria-hidden', 'false');

        const rect = menu.getBoundingClientRect();
        const pad = 12;

        let left = x;
        let top = y;

        if (left + rect.width > window.innerWidth - pad) {
            left = window.innerWidth - rect.width - pad;
        }

        if (top + rect.height > window.innerHeight - pad) {
            top = window.innerHeight - rect.height - pad;
        }

        if (left < pad) left = pad;
        if (top < pad) top = pad;

        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
    }

    async function applyCheapestAlternativeBulk(rows) {
        const url = folderApp.dataset.materialChangeUrl;
        if (!url) {
            alert('Keine Änderungs-URL gefunden.');
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

    function buildSingleRowComparisonPayload(row) {
        return {
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
        };
    }

    async function fetchMaterialComparisonForRow(row) {
        const url = folderApp.dataset.materialComparisonUrl;
        if (!url) {
            throw new Error('Keine Vergleichs-URL gefunden.');
        }

        return await fetchJson(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(buildSingleRowComparisonPayload(row))
        });
    }

    function renderMaterialContextMenu(row, comparisonData) {
        const menu = document.getElementById('material-context-menu');
        const body = document.getElementById('material-context-body');
        const title = document.getElementById('material-context-title');
        const sub = document.getElementById('material-context-sub');
        const backdrop = document.getElementById('of-context-backdrop');

        if (!menu || !body || !title || !sub || !backdrop) return;

        title.textContent = row?.name || 'Material';
        sub.textContent = `${row?.article_no || '-'} · Aktuell: ${row?.distributor_name || '-'}`;

        const item = Array.isArray(comparisonData?.items) ? comparisonData.items[0] : null;
        const options = Array.isArray(item?.options) ? item.options : [];

        if (!options.length) {
            body.innerHTML = `<div class="of-context-menu-empty">Keine Preisvergleichsdaten für diese Position gefunden.</div>`;
            backdrop.classList.add('show');
            return;
        }

        body.innerHTML = options.map(option => {
            const isCurrent = Number(option.distributor_id || 0) === Number(row.distributor_id || 0);

            return `
                <button
                    type="button"
                    class="of-context-menu-item ${isCurrent ? 'is-current' : ''}"
                    onclick="applyDistributorChangeFromContext(${Number(option.distributor_id)}, ${Number(state.contextMenu.rowIndex)}, '${String(option.distributor_name || '').replaceAll('\\', '\\\\').replaceAll("'", "\\'")}')"
                >
                    <div class="of-context-menu-item-main">
                        <div class="of-context-menu-item-title">
                            ${esc(option.distributor_name || 'Distributor')}
                            ${isCurrent ? ' · Aktuell' : ''}
                        </div>
                        <div class="of-context-menu-item-sub">
                            Art.-Nr.: ${esc(option.article_no || '-')}
                            <br>Verfügbarkeit: ${esc(option.availability || '-')}
                            <br>EK: ${esc(money(option.purchase_price || 0))}
                        </div>
                    </div>
                    <div class="of-context-menu-price">
                        ${esc(money(option.price || 0))}
                    </div>
                </button>
            `;
        }).join('');

        backdrop.classList.add('show');
    }

    async function openMaterialContextMenu(event, rowIndex) {
        event.preventDefault();

        const { materialRows } = getStructureRows();
        const row = materialRows[rowIndex];

        if (!row || !row.product_id) {
            return;
        }

        state.contextMenu.visible = true;
        state.contextMenu.rowIndex = rowIndex;
        state.contextMenu.rowData = row;
        state.contextMenu.comparison = null;

        const body = document.getElementById('material-context-body');
        const title = document.getElementById('material-context-title');
        const sub = document.getElementById('material-context-sub');
        const backdrop = document.getElementById('of-context-backdrop');

        if (title) title.textContent = row.name || 'Material';
        if (sub) sub.textContent = 'Preisvergleich wird geladen...';
        if (body) body.innerHTML = `<div class="of-context-menu-empty">Preisvergleich wird geladen...</div>`;
        if (backdrop) backdrop.classList.add('show');

        positionMaterialContextMenu(event.clientX, event.clientY);

        try {
            const json = await fetchMaterialComparisonForRow(row);

            if (!json.success) {
                throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
            }

            state.contextMenu.comparison = json;
            renderMaterialContextMenu(row, json);
            positionMaterialContextMenu(event.clientX, event.clientY);
        } catch (error) {
            if (body) {
                body.innerHTML = `<div class="of-context-menu-empty">${esc(error.message || 'Preisvergleich konnte nicht geladen werden.')}</div>`;
            }
            if (sub) {
                sub.textContent = `${row.article_no || '-'} · ${row.distributor_name || '-'}`;
            }
            positionMaterialContextMenu(event.clientX, event.clientY);
        }
    }

    async function applyDistributorChangeFromContext(distributorId, rowIndex, distributorName) {
        const { materialRows } = getStructureRows();
        const row = materialRows[rowIndex];

        if (!row) {
            alert('Materialzeile wurde nicht gefunden.');
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
                throw new Error(json.message || 'Distributor konnte nicht übernommen werden.');
            }

            closeMaterialContextMenu();
            await loadFolderData();

            showCustomToast(
                'Distributor übernommen',
                `${distributorName} wurde für die ausgewählte Materialposition aktualisiert.`
            );
        } catch (error) {
            alert(error.message || 'Distributor konnte nicht übernommen werden.');
        }
    }

    window.openMaterialContextMenu = openMaterialContextMenu;
    window.applyDistributorChangeFromContext = applyDistributorChangeFromContext;
    window.closeMaterialContextMenu = closeMaterialContextMenu;


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

    
   async function openMaterialComparisonModal() {
        // 1. Check if ANY row is checked at all
        const allSelected = getSelectedMaterialRows(false);

        if (!allSelected.length) {
            alert('Bitte wählen Sie zuerst mindestens eine Materialposition aus.');
            return;
        }

        // 2. Filter for rows that actually have a product_id
        const selectedRows = getSelectedMaterialRows(true);

        if (!selectedRows.length) {
            alert('Die ausgewählten Positionen sind manuelle Einträge oder Sets ohne Katalogverknüpfung. Der Preisvergleich funktioniert nur mit echten Artikeln.');
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
                    product_id: parseInt(row.product_id, 10), // Force to Integer
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



    function renderMaterialComparisonModal(data) {
        const body = document.getElementById('material-comparison-body');
        if (!body) return;

        if (Array.isArray(state.comparisonCharts)) {
            state.comparisonCharts.forEach(chart => {
                try { chart.destroy(); } catch (e) {}
            });
        }
        state.comparisonCharts = [];

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
                                    <div
                                        class="of-dist-card ${isBest ? 'is-best' : ''} ${isWorst ? 'is-worst' : ''}"
                                        data-distributor-search="${esc((row.distributor_name || '').toLowerCase())}"
                                    >
                                        <div class="of-dist-card-head">
                                            <div>
                                                <div class="of-dist-title">${esc(row.distributor_name)}</div>
                                                <div class="of-dist-sub">
                                                    Vergleich für ${items.length} ausgewählte Positionen
                                                </div>
                                            </div>

                                            ${isBest
                                                ? '<span class="of-dist-rank best">Bester Preis</span>'
                                                : (isWorst ? '<span class="of-dist-rank worst">Höchster Preis</span>' : '')
                                            }
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
                                                <button
                                                    type="button"
                                                    class="of-btn"
                                                    onclick="applyDistributorChange(${Number(row.distributor_id)}, '${String(row.distributor_name || '').replaceAll('\\', '\\\\').replaceAll("'", "\\'")}')"
                                                >
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

       initDistributorSearch();
        initComparisonFilters(summary);
    }

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

        const oldTotalChart = Chart.getChart(totalCanvas);
        if (oldTotalChart) {
            try { oldTotalChart.destroy(); } catch (e) {}
        }

        const oldTermsChart = Chart.getChart(termsCanvas);
        if (oldTermsChart) {
            try { oldTermsChart.destroy(); } catch (e) {}
        }

        const labels = summary.map(row => row.distributor_name);
        const totals = summary.map(row => Number(row.total_effective || 0));
        const paymentTerms = summary.map(row => Number(row.avg_payment_terms || 0));
        const cashDiscounts = summary.map(row => Number(row.avg_cash_discount || 0));
        const availability = summary.map(row => Number(row.availability_ratio || 0));

        const totalChart = new Chart(totalCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Gesamtpreis',
                        data: totals
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 100,
                animation: false
            }
        });

        const termsChart = new Chart(termsCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Zahlungsziel (Tage)',
                        data: paymentTerms
                    },
                    {
                        label: 'Skonto %',
                        data: cashDiscounts
                    },
                    {
                        label: 'Verfügbarkeit %',
                        data: availability
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 100,
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
            if (!Array.isArray(summary)) return;

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
            if (input) {
                input.addEventListener('change', applyFilters);
            }
        });

        applyFilters();
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

async function applyDistributorChange(distributorId, distributorName) {
    const selectedRows = getSelectedMaterialRows(true);

    if (!selectedRows.length) {
        alert('Bitte zuerst Materialpositionen auswählen.');
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
            `${distributorName} wurde in der Angebotsvorlage und im Ordner aktualisiert.`
        );
    } catch (error) {
        alert(error.message || 'Distributor konnte nicht übernommen werden.');
    }
}
window.applyDistributorChange = applyDistributorChange;


    function distributorName(distributorId) {
        if (!distributorId) return '-';
        return state.distributors?.[String(distributorId)] || state.distributors?.[Number(distributorId)] || `Lieferant #${distributorId}`;
    }

    function esc(v) {
        return String(v ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function printMaterialSheet() {
        switchTab('material-print');
        window.print();
    }

    window.printMaterialSheet = printMaterialSheet;

    function safeArray(value) {
        return Array.isArray(value) ? value : [];
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

   function getTabMetrics() {
        const { materialRows, laborRows } = getStructureRows();

        return {
            overview: 1,
            kanban: 1,
            material: materialRows.length,
            labor: laborRows.length,
            materialPrint: materialRows.length,
            sektionen: safeArray(state.sections).length,
            historie: 0,
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
        setText('tab-count-sektionen', String(metrics.sektionen));
        setText('tab-count-historie', String(metrics.historie));
        setText('tab-count-print-files', String(metrics.printFiles));
        setText('tab-count-agb', String(metrics.agb));
    }


    function formatBytes(bytes) {
        const value = Number(bytes || 0);

        if (value < 1024) return `${value} B`;
        if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`;

        return `${(value / (1024 * 1024)).toFixed(2)} MB`;
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

    if (badge) {
        badge.textContent = `${files.length} Dateien`;
    }

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
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
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

            if (offerId) {
                formData.append('offer_id', offerId);
            }

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


    window.deleteAttachment = deleteAttachment; 



    function money(value) {
        const n = Number(value || 0);
        return new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(n) + ' €';
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function normalisiereStatus(status) {
        const raw = String(status || '').trim().toLowerCase();

        const map = {
            open: 'offen',
            offen: 'offen',

            verified: 'geprueft',
            geprüft: 'geprueft',
            geprueft: 'geprueft',

            sold: 'verkauft',
            verkauft: 'verkauft',

            junk: 'ausschuss',
            ausschuss: 'ausschuss',
            ausschuß: 'ausschuss'
        };

        return map[raw] || 'offen';
    }

    function buildStatusLabel(statusKey) {
        return STATUS_LABELS[statusKey] || 'Offen';
    }

    function getFolderStatus() {
        return normalisiereStatus(state.folder?.status || 'offen');
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

    function renderStats() {
        const detail = state.detail || {};
        const currentStatus = getFolderStatus();

        document.getElementById('stat-total-net').textContent = money(detail.total_net || 0);
        document.getElementById('stat-tax-rate').textContent = new Intl.NumberFormat('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(detail.tax_rate || 19)) + ' %';
        document.getElementById('stat-total-gross').textContent = money(detail.total_gross || 0);
        document.getElementById('stat-items-count').textContent = String(getAllStructureCounts());

        document.getElementById('info-company-name').textContent = detail.company_name || '-';
        document.getElementById('info-brand-mode').textContent = detail.brand_mode || 'Text';
        document.getElementById('info-brand-color').textContent = detail.brand_color || '-';
        document.getElementById('info-brand-logo').textContent = detail.brand_logo_url || '-';
        document.getElementById('info-sections-count').textContent = String(safeArray(state.sections).length || 0);
        document.getElementById('info-images-count').textContent = String(safeArray(detail.placed_images).length || 0);

        const coverBox = document.getElementById('cover-box');
        const cover = buildCoverHtml(detail);
        coverBox.innerHTML = cover.html;
        coverBox.classList.toggle('empty', cover.isEmpty);

        document.getElementById('status-card-offen').textContent = currentStatus === 'offen' ? '1' : '0';
        document.getElementById('status-card-geprueft').textContent = currentStatus === 'geprueft' ? '1' : '0';
        document.getElementById('status-card-verkauft').textContent = currentStatus === 'verkauft' ? '1' : '0';
        document.getElementById('status-card-ausschuss').textContent = currentStatus === 'ausschuss' ? '1' : '0';
        renderTabCounts();
    }

        function stripHtml(value) {
        const div = document.createElement('div');
        div.innerHTML = String(value || '');
        return (div.textContent || div.innerText || '').trim();
    }

    function getStructureRows() {
        const materialRows = [];
        const laborRows = [];

        safeArray(state.sections).forEach((section, sectionIndex) => {
            const sectionTitle = section?.title || `Sektion ${sectionIndex + 1}`;

            safeArray(section?.items).forEach((item, itemIndex) => {
                const parentTitle = item?.name || item?.title || `Position ${itemIndex + 1}`;

                if (item?.kind !== 'labor') {
                  materialRows.push({
                        section_title: sectionTitle,
                        parent_title: parentTitle,
                        level: 'Hauptposition',
                        product_id: item?.product_id || item?.productId || item?.id || null,
                        distributor_price_id: item?.distributor_price_id || null,
                        article_no: item?.distributor_article_no || item?.article_no || '-',
                        name: parentTitle,
                        description: stripHtml(item?.desc_html || item?.desc || item?.description || ''),
                        qty: Number(item?.qty || 0),
                        unit: item?.unit || item?.measure || '-',
                        unit_price: Number(item?.price || item?.rate || 0),
                        total: Number(item?.total ?? (Number(item?.qty || 0) * Number(item?.price || item?.rate || 0))),
                        distributor_id: item?.distributor_id || null,
                        distributor_name: distributorName(item?.distributor_id),
                        item_type: item?.item_type || 'Position',
                        depth: Number(item?.depth || 0)
                    });
                }

                safeArray(item?.subItems).forEach((subItem, subIndex) => {
                    if (subItem?.kind === 'labor') {
                        const laborRowsData = safeArray(subItem?.labor_rows);

                        if (laborRowsData.length) {
                            laborRowsData.forEach((row, rowIndex) => {
                                laborRows.push({
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

                    materialRows.push({
                        section_title: sectionTitle,
                        parent_title: parentTitle,
                        level: subItem?.isChildNode ? 'Unterartikel' : 'Komponente',
                        product_id: subItem?.product_id || subItem?.productId || subItem?.id || null, // Updated
                        distributor_price_id: subItem?.distributor_price_id || null,
                        article_no: subItem?.distributor_article_no || subItem?.article_no || '-',
                        name: subItem?.name || subItem?.title || `Unterposition ${subIndex + 1}`,
                        description: stripHtml(subItem?.desc_html || subItem?.desc || subItem?.description || ''),
                        qty: Number(subItem?.qty || 0),
                        unit: subItem?.unit || subItem?.measure || '-',
                        unit_price: Number(subItem?.price || subItem?.rate || 0),
                        total: Number(subItem?.total ?? (Number(subItem?.qty || 0) * Number(subItem?.price || subItem?.rate || 0))),
                        distributor_id: subItem?.distributor_id || null,
                        distributor_name: distributorName(subItem?.distributor_id),
                        item_type: subItem?.item_type || 'Komponente',
                        depth: Number(subItem?.depth || 0)
                    });
                });
            });
        });

        return { materialRows, laborRows };
    }


    function renderLaborRow(row, indentClass = 'of-tree-indent-3') {
        const qty = row?.qty ?? 0;
        const unit = row?.unit || 'Std';
        const rate = row?.rate ?? 0;
        const total = row?.total ?? (Number(qty) * Number(rate));

        return `
            <div class="of-tree-row ${indentClass}">
                <div class="of-tree-row-title">
                    Qualifikation: ${esc(row?.qualification_name || 'Unbekannt')}
                </div>
                <div class="of-tree-row-sub">
                    Zeit: ${esc(qty)} ${esc(unit)}
                    · Satz: ${esc(money(rate))}
                    · Gesamt: ${esc(money(total))}
                </div>
            </div>
        `;
    }

    function renderSubItem(subItem, sectionIndex, itemIndex, subIndex) {
        const subTitle = subItem?.name || subItem?.title || 'Unterposition';
        const qty = subItem?.qty ?? 1;
        const unit = subItem?.unit || subItem?.measure || '-';
        const price = subItem?.price ?? subItem?.rate ?? 0;
        const total = subItem?.total ?? (Number(qty) * Number(price));
        const subType = subItem?.kind === 'labor'
            ? 'Arbeitsleistung'
            : (subItem?.item_type || 'Komponente');

        const laborRows = subItem?.kind === 'labor' ? safeArray(subItem?.labor_rows) : [];

        return `
            <div class="of-tree-node of-tree-indent-2" data-node-id="sub-${sectionIndex}-${itemIndex}-${subIndex}">
                <div class="of-tree-node-head" onclick="toggleTreeNode('sub-${sectionIndex}-${itemIndex}-${subIndex}')">
                    <div class="of-tree-node-left">
                        <div class="of-tree-node-title">${esc(subTitle)}</div>
                        <div class="of-tree-meta">
                            <span class="of-badge">${esc(subType)}</span>
                            <span class="of-badge">Menge: ${esc(qty)}</span>
                            <span class="of-badge">Einheit: ${esc(unit)}</span>
                            <span class="of-badge">Einzelpreis: ${esc(money(price))}</span>
                            <span class="of-badge">Gesamt: ${esc(money(total))}</span>
                        </div>
                        ${(subItem?.desc || subItem?.description) ? `
                            <div class="of-tree-node-sub">${esc(subItem?.desc || subItem?.description || '')}</div>
                        ` : ''}
                    </div>
                    <div class="of-toggle-icon">${laborRows.length ? '+' : '•'}</div>
                </div>

                <div class="of-tree-node-body">
                    ${laborRows.length ? `
                        <div class="of-tree-children">
                            ${laborRows.map(row => renderLaborRow(row)).join('')}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function renderMainItem(item, sectionIndex, itemIndex) {
        const title = item?.name || item?.title || 'Position';
        const qty = item?.qty ?? 1;
        const unit = item?.unit || item?.measure || '-';
        const price = item?.price ?? 0;
        const total = item?.total ?? (Number(qty) * Number(price));
        const subItems = safeArray(item?.subItems);

        return `
            <div class="of-tree-node" data-node-id="item-${sectionIndex}-${itemIndex}">
                <div class="of-tree-node-head" onclick="toggleTreeNode('item-${sectionIndex}-${itemIndex}')">
                    <div class="of-tree-node-left">
                        <div class="of-tree-node-title">${esc(title)}</div>
                        <div class="of-tree-meta">
                            <span class="of-badge">${esc(item?.item_type || 'Position')}</span>
                            <span class="of-badge">Menge: ${esc(qty)}</span>
                            <span class="of-badge">Einheit: ${esc(unit)}</span>
                            <span class="of-badge">Einzelpreis: ${esc(money(price))}</span>
                            <span class="of-badge">Gesamt: ${esc(money(total))}</span>
                        </div>
                        ${(item?.desc || item?.description) ? `
                            <div class="of-tree-node-sub">${esc(item?.desc || item?.description || '')}</div>
                        ` : ''}
                    </div>
                    <div class="of-toggle-icon">${subItems.length ? '+' : '•'}</div>
                </div>

                <div class="of-tree-node-body">
                    <div class="of-tree-children">
                        ${subItems.length
                            ? subItems.map((subItem, subIndex) => renderSubItem(subItem, sectionIndex, itemIndex, subIndex)).join('')
                            : ''
                        }
                    </div>
                </div>
            </div>
        `;
    }

    function renderList() {
        const list = document.getElementById('offer-structure-list');

        if (!safeArray(state.sections).length) {
            list.innerHTML = `<div class="of-empty">Keine Sektionen vorhanden.</div>`;
            return;
        }

        list.innerHTML = state.sections.map((section, sectionIndex) => {
            const items = safeArray(section?.items);

            return `
                <div class="of-tree-section open" id="section-${sectionIndex}">
                    <div class="of-tree-section-head" onclick="toggleSection('section-${sectionIndex}')">
                        <div>
                            <div class="of-tree-section-title">${esc(section?.title || `Sektion ${sectionIndex + 1}`)}</div>
                            <div class="of-tree-node-sub">${esc(section?.description || 'Keine Beschreibung vorhanden.')}</div>
                        </div>
                        <div class="of-toggle-icon">−</div>
                    </div>

                    <div class="of-tree-section-body">
                        ${items.length
                            ? items.map((item, itemIndex) => renderMainItem(item, sectionIndex, itemIndex)).join('')
                            : `<div class="of-empty">Keine Positionen in dieser Sektion.</div>`
                        }
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderMaterialList() {
        const wrap = document.getElementById('material-list-wrap');
        const printWrap = document.getElementById('material-print-wrap');
        const badge = document.getElementById('material-count-badge');
        const printCount = document.getElementById('print-material-count');
        const printTotal = document.getElementById('print-material-total');
        const printCompany = document.getElementById('print-company-name');

        if (!wrap || !printWrap) return;

        const { materialRows } = getStructureRows();

        if (badge) badge.textContent = `${materialRows.length} Positionen`;
        if (printCount) printCount.textContent = String(materialRows.length);
        if (printCompany) printCompany.textContent = state.detail?.company_name || '-';

        const materialTotal = materialRows.reduce((sum, row) => sum + Number(row.total || 0), 0);
        const quantityTotal = materialRows.reduce((sum, row) => sum + Number(row.qty || 0), 0);
        if (printTotal) printTotal.textContent = money(materialTotal);

        if (!materialRows.length) {
            wrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;
            printWrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;
            renderTabCounts();
            return;
        }

        const materialTableHtml = `
            <div class="of-material-toolbar">
                <div class="of-material-toolbar-left">
                    <label class="of-selected-badge">
                        <input type="checkbox" class="of-check" id="material-select-all">
                        Alle auswählen
                    </label>
                    <span class="of-selected-badge" id="material-selected-info">0 ausgewählt</span>
                </div>

                <div class="of-material-toolbar-right">
                    <span class="of-badge">${materialRows.length} Materialpositionen</span>
                    <span class="of-badge">Mehrfachauswahl zeigt günstigste Alternative rechts</span>
                </div>
            </div>

            <div class="of-table-wrap">
                <table class="of-table" id="material-table">
                    <thead>
                        <tr>
                            <th class="of-table-check">
                                <input type="checkbox" class="of-check" id="material-select-all-head">
                            </th>
                            <th>Material / Zuordnung</th>
                            <th>Art.-Nr.</th>
                            <th>Lieferant</th>
                            <th class="num">Menge</th>
                            <th>Einheit</th>
                            <th class="num">EP</th>
                            <th class="num">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${materialRows.map((row, index) => `
                            <tr
                                    data-material-row="${index}"
                                    oncontextmenu="openMaterialContextMenu(event, ${index})"
                                >
                                <td class="of-table-check">
                                    <input
                                        type="checkbox"
                                        class="of-check material-row-check"
                                        data-row-index="${index}"
                                    >
                                </td>
                                <td>
                                    <div class="of-mat-name">
                                        <div class="of-mat-title">${esc(row.name)}</div>

                                        <div class="of-mat-meta">
                                            <span class="of-mat-chip">Sektion: ${esc(row.section_title)}</span>
                                            <span class="of-mat-chip">Hauptposition: ${esc(row.parent_title)}</span>
                                            <span class="of-mat-chip">Typ: ${esc(row.level)}</span>
                                        </div>

                                        ${row.description ? `<div class="of-mat-desc">${esc(row.description)}</div>` : ''}
                                    </div>
                                </td>
                                <td>${esc(row.article_no || '-')}</td>
                                <td>${esc(row.distributor_name || '-')}</td>
                                <td class="num">${esc(row.qty)}</td>
                                <td>${esc(row.unit)}</td>
                                <td class="num">${esc(money(row.unit_price))}</td>
                                <td class="num">${esc(money(row.total))}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        const printTableHtml = `
            <div class="of-table-wrap">
                <table class="of-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Pos.</th>
                            <th>Art.-Nr.</th>
                            <th>Bezeichnung</th>
                            <th>Lieferant</th>
                            <th class="num">Menge</th>
                            <th>Einheit</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${materialRows.map((row, index) => `
                            <tr>
                                <td class="num">${index + 1}</td>
                                <td>${esc(row.article_no || '-')}</td>
                                <td>
                                    <div style="font-weight:900;">${esc(row.name)}</div>
                                    <div class="muted" style="margin-top:4px;">
                                        ${esc(row.section_title)} · ${esc(row.parent_title)} · ${esc(row.level)}
                                    </div>
                                </td>
                                <td>${esc(row.distributor_name || '-')}</td>
                                <td class="num">${esc(row.qty)}</td>
                                <td>${esc(row.unit)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 900; border-top: 2px solid var(--of-line);">
                            <td colspan="4" style="text-align: right;">Gesamt:</td>
                            <td class="num" style="color: var(--of-primary);">${quantityTotal}</td>
                            <td></td>
                            <td></td>
                            <td class="num" style="color: var(--of-primary);">${esc(money(materialTotal))}</td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        `;

        wrap.innerHTML = materialTableHtml;
        printWrap.innerHTML = printTableHtml;

        initMaterialSelection();
        renderTabCounts();
    }

   function updateMaterialSelectionState() {
        const rowChecks = Array.from(document.querySelectorAll('.material-row-check'));
        const checkedRows = rowChecks.filter(cb => cb.checked);
        const allChecked = rowChecks.length > 0 && checkedRows.length === rowChecks.length;

        rowChecks.forEach(cb => {
            const row = cb.closest('tr');
            if (row) {
                row.classList.toggle('is-selected', cb.checked);
            }
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
            selectAllA.addEventListener('change', function() {
                setAllMaterialRows(this.checked);
            });
        }

        if (selectAllB) {
            selectAllB.addEventListener('change', function() {
                setAllMaterialRows(this.checked);
            });
        }

        rowChecks.forEach(cb => {
            cb.addEventListener('change', updateMaterialSelectionState);

            const row = cb.closest('tr');
            if (row) {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('input, button, a, label')) return;
                    cb.checked = !cb.checked;
                    updateMaterialSelectionState();
                });
            }
        });

        updateMaterialSelectionState();
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
    function renderSections() {
        const grid = document.getElementById('sections-grid');

        if (!safeArray(state.sections).length) {
            grid.innerHTML = `<div class="of-empty">Keine Sektionen vorhanden.</div>`;
            return;
        }

        grid.innerHTML = state.sections.map(section => {
            const items = safeArray(section?.items);

            let materialCount = 0;
            let laborCount = 0;

            items.forEach(item => {
                materialCount++;

                safeArray(item?.subItems).forEach(sub => {
                    if (sub?.kind === 'labor') {
                        laborCount += safeArray(sub?.labor_rows).length || 1;
                    } else {
                        materialCount++;
                    }
                });
            });

            return `
                <div class="of-section-card">
                    <div class="of-section-head">
                        <div class="of-section-title">${esc(section?.title || 'Sektion')}</div>
                        <span class="of-badge">${esc(buildStatusLabel(normalisiereStatus(section?.status || 'offen')))}</span>
                    </div>
                    <div class="of-section-body">
                        <div class="of-section-desc">${esc(section?.description || 'Keine Beschreibung vorhanden.')}</div>
                        <div class="of-section-stats">
                            <span class="of-badge">Positionen: ${esc(items.length)}</span>
                            <span class="of-badge">Material/Komponenten: ${esc(materialCount)}</span>
                            <span class="of-badge">Lohnzeilen: ${esc(laborCount)}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        renderTabCounts();
    }

    function renderKanban() {
        const currentStatus = getFolderStatus();

        STATUS_SCHLUESSEL.forEach(status => {
            const col = document.getElementById(`col-${status}`);
            const count = document.getElementById(`count-${status}`);

            if (!col || !count) return;

            const isCurrent = status === currentStatus;
            count.textContent = isCurrent ? '1' : '0';

            if (!isCurrent) {
                col.innerHTML = `<div class="of-empty">Kein Angebot in diesem Status.</div>`;
                return;
            }

            col.innerHTML = `
                <div
                    class="of-item"
                    data-folder-id="${esc(state.folder?.id || '')}"
                    data-status="${esc(currentStatus)}"
                >
                    <div class="of-item-title">${esc(state.folder?.name || 'Angebot')}</div>

                    <div class="of-item-meta">
                        <span class="of-badge">Angebot #${esc(state.offer?.id || '-')}</span>
                        <span class="of-badge">${esc([
                            state.offer?.customer?.firma || '',
                            state.offer?.customer?.name || '',
                            state.offer?.customer?.lastname || ''
                        ].join(' ').trim())}</span>
                    </div>

                    <div class="of-item-meta">
                        <span class="of-badge">Status: ${esc(buildStatusLabel(currentStatus))}</span>
                        <span class="of-badge">Sektionen: ${esc(safeArray(state.sections).length)}</span>
                    </div>

                    <div class="of-item-desc">
                        Dies ist der aktuelle Status dieses Angebotsordners.
                    </div>
                </div>
            `;
        });

        initKanbanSortable();
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

    function toggleSection(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.classList.toggle('open');

        const icon = el.querySelector('.of-tree-section-head .of-toggle-icon');
        if (icon) {
            icon.textContent = el.classList.contains('open') ? '−' : '+';
        }
    }

    function toggleTreeNode(nodeId) {
        const el = document.querySelector(`[data-node-id="${nodeId}"]`);
        if (!el) return;

        const body = el.querySelector(':scope > .of-tree-node-body');
        if (!body || !body.innerHTML.trim()) return;

        el.classList.toggle('open');

        const icon = el.querySelector(':scope > .of-tree-node-head .of-toggle-icon');
        if (icon) {
            icon.textContent = el.classList.contains('open') ? '−' : '+';
        }
    }

    window.toggleSection = toggleSection;
    window.toggleTreeNode = toggleTreeNode;

   function switchTab(tab) {
        state.currentTab = tab;

        document.querySelectorAll('.of-tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });

        document.querySelectorAll('.of-panel').forEach(panel => {
            panel.classList.toggle('active', panel.id === `panel-${tab}`);
        });
    }

    window.switchTab = switchTab;

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
        document.querySelectorAll('.of-list').forEach(list => {
            if (list.dataset.sortableReady === '1') return;

            new Sortable(list, {
                group: 'angebot-folder-status',
                animation: 180,
                ghostClass: 'of-drag-ghost',
                chosenClass: 'of-drag-chosen',
                dragClass: 'of-drag-chosen',
                onEnd: async function(evt) {
                    const zielStatus = String(evt.to.id || '').replace('col-', '');

                    if (!STATUS_SCHLUESSEL.includes(zielStatus)) {
                        await loadFolderData();
                        return;
                    }

                    try {
                        const json = await saveKanbanMove({ status: zielStatus });

                        if (!json.success) {
                            throw new Error(json.message || 'Status konnte nicht gespeichert werden.');
                        }

                        state.folder = json.folder || state.folder;
                        renderStats();
                        renderKanban();
                    } catch (error) {
                        await loadFolderData();
                        alert(error.message || 'Status konnte nicht gespeichert werden.');
                    }
                }
            });

            list.dataset.sortableReady = '1';
        });
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
            state.offer = json.offer || state.offer;
            state.detail = json.detail || state.detail;
            state.sections = safeArray(state.detail?.sections);
            state.distributors = json.distributors || {};
            state.attachments = safeArray(json.attachments || []);
            
            // ✅ Load PDFs from backend response
            state.pdfs = safeArray(json.pdfs || []); 

            if (json.agb) {
                window.folderAgb = json.agb;
            }

            renderStats();
            renderKanban();
            renderSections();
            renderMaterialList();
            renderLaborList();
            renderPrintFiles();
            renderPdfVersions(); // ✅ Render the new PDF tab
            renderTabCounts();
            syncAgbInputs();
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
        renderSections();
        renderMaterialList();
        renderLaborList();
        renderPresenceUsers();
        renderTabCounts();
        switchTab('uebersicht');
        initAttachmentSearch();
        initAttachmentDropzone();
        initAgbEditor();
        syncAgbInputs();

        const uploadForm = document.getElementById('print-files-upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', uploadPrintFiles);
        }

        document.querySelectorAll('.of-tab').forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
        });

                const contextBackdrop = document.getElementById('of-context-backdrop');
        if (contextBackdrop) {
            contextBackdrop.addEventListener('click', closeMaterialContextMenu);
        }

        document.addEventListener('click', (e) => {
            const menu = document.getElementById('material-context-menu');
            if (!menu) return;

            if (!menu.contains(e.target)) {
                closeMaterialContextMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMaterialContextMenu();
            }
        });

        window.addEventListener('resize', closeMaterialContextMenu);
        window.addEventListener('scroll', closeMaterialContextMenu, true);

        initPresenceChannel();
        await loadFolderData();
        syncAgbInputs();
    });
})();
</script>
@endpush
@endonce

