<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartQuote Direct - Professional</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- ✅ ADD THIS IN <head> (Quill) -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

       <style>
    :root{
        --brand-color:#93c21c;
    }

    /* =========================
       SELECT2
    ========================= */
    .select2-container{ width:100% !important; }

    .select2-container--default .select2-selection--multiple{
        min-height:48px;
        border:1px solid rgb(203 213 225);
        border-radius:.5rem;
        padding:.5rem .75rem;
        box-shadow:none;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple{
        border-color:var(--brand-color);
        outline:none;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background:#f7fee7;
        border:1px solid rgba(147,194,28,.35);
        border-radius:.5rem;
        padding:2px 8px;
        margin-top:6px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        margin-right:6px;
    }

    .select2-dropdown{
        border-color:rgb(226 232 240);
    }

    /* =========================
       CORE
    ========================= */
    body{
        background-color:#cbd5e1;
        color:#334155;
        height:100vh;
        display:flex;
        flex-direction:column;
        overflow:hidden;
        font-family:'Inter', sans-serif;
    }

    .btn-primary{ @apply bg-[#93c21c] text-white shadow hover:brightness-105 transition-all active:scale-95 px-4 py-2 rounded font-bold; }
    .btn-disabled{ @apply bg-slate-300 text-white cursor-not-allowed shadow-none; }
    .sidebar-tab{ @apply flex-1 py-3 text-center text-xs font-bold text-slate-500 hover:text-[#93c21c] border-b-2 border-transparent transition cursor-pointer; }
    .sidebar-tab.active{ @apply text-[#93c21c] border-[#93c21c] bg-slate-50; }

    .view-section{
        display:none !important;
        height:100%;
        flex-direction:column;
        animation:fadeIn .3s ease-in-out;
    }

    .view-section.active{
        display:flex !important;
    }

    @keyframes fadeIn{
        from{ opacity:0; transform:translateY(5px); }
        to{ opacity:1; transform:translateY(0); }
    }

    .a4-page{
        width:210mm;
        height:297mm;
        background:#fff;
        box-shadow:0 10px 15px -3px rgba(0,0,0,.1);
        margin:0 auto 40px auto;
        padding:10mm 10mm;
        position:relative;
        display:flex;
        flex-direction:column;
        overflow:hidden;
        flex-shrink:0;
        box-sizing:border-box;
        transform-origin:top center;
    }

    .page-content{
        flex:1;
        display:flex;
        flex-direction:column;
        width:100%;
        overflow:hidden;
    }

    .sidebar-panel{
        transition:width .3s cubic-bezier(.4,0,.2,1), opacity .2s;
        overflow:hidden;
    }

    .sidebar-collapsed{
        width:0 !important;
        opacity:0;
        padding:0 !important;
        border:none !important;
    }

    /* =========================
       THUMBNAILS
    ========================= */
    .thumb-container{
        width:220px;
        background:#e2e8f0;
        border-right:1px solid #cbd5e1;
        overflow-y:auto;
        overflow-x:hidden;
        padding:1rem;
        flex-shrink:0;
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:1rem;
    }

    .thumb-wrapper{
        width:180px;
        display:flex;
        flex-direction:column;
        align-items:center;
        cursor:grab;
        transition:transform .2s;
        position:relative;
        border-radius:8px;
    }

    .thumb-wrapper:hover{
        transform:scale(1.02);
        z-index:10;
    }

    .thumb-wrapper:active{
        cursor:grabbing;
    }

    .thumb-wrapper.sortable-ghost{
        opacity:.35;
    }

    .thumb-wrapper.sortable-chosen{
        box-shadow:0 10px 18px rgba(0,0,0,.15);
        transform:scale(1.02);
    }

    .thumb-scale-box{
        width:170px;
        height:240px;
        background:#fff;
        box-shadow:0 2px 8px rgba(0,0,0,.12);
        border:1px solid #94a3b8;
        overflow:hidden;
        pointer-events:none;
        user-select:none;
        position:relative;
        display:flex;
        align-items:flex-start;
        justify-content:center;
    }

    .thumb-scale-box .a4-page{
        width:210mm !important;
        height:297mm !important;
        margin:0 !important;
        transform:scale(.215);
        transform-origin:top center;
        flex-shrink:0 !important;
        box-shadow:none !important;
        pointer-events:none !important;
    }

    .thumb-scale-box *{
        pointer-events:none !important;
        user-select:none !important;
    }

    .thumb-static-field{
        display:block;
        width:100%;
        min-height:1em;
        background:transparent !important;
        border:0 !important;
        box-shadow:none !important;
        outline:none !important;
    }

    .thumb-scale-box input,
    .thumb-scale-box textarea,
    .thumb-scale-box select,
    .thumb-scale-box button{
        appearance:none !important;
        border:0 !important;
        background:transparent !important;
        box-shadow:none !important;
        outline:none !important;
    }

    .thumb-scale-box .editable-field,
    .thumb-scale-box .clean-input{
        border:0 !important;
        background:transparent !important;
        outline:none !important;
    }

    .thumb-scale-box .prod-img-container::after{
        display:none !important;
    }

    .thumb-label{
        position:absolute;
        bottom:6px;
        right:10px;
        background:rgba(0,0,0,.72);
        color:#fff;
        font-size:10px;
        padding:3px 8px;
        border-radius:999px;
        font-weight:700;
        pointer-events:none;
    }

    .thumb-wrapper.is-active .thumb-label{
        background:rgba(147,194,28,.92) !important;
        color:#fff !important;
        box-shadow:0 0 0 2px rgba(147,194,28,.25);
    }

    .thumb-wrapper.is-active .thumb-scale-box{
        border-color:var(--brand-color) !important;
        box-shadow:0 0 0 2px rgba(147,194,28,.25), 0 2px 5px rgba(0,0,0,.1);
    }

    /* =========================
       PDF / BRAND
    ========================= */
    .pdf-title-blue{
        color:#5298bc;
        font-weight:700;
        text-transform:uppercase;
        font-size:.85rem;
        margin-bottom:.1rem;
    }

    .pdf-logo-text{
        font-weight:900;
        color:var(--brand-color) !important;
        letter-spacing:-.02em;
    }

    .brand-text{ color:var(--brand-color) !important; }
    .brand-border{ border-color:var(--brand-color) !important; }
    .brand-outline:focus{ outline-color:var(--brand-color) !important; }

    #doc-logo-text,
    #editor-doc-type-label{
        color:var(--brand-color) !important;
    }

    /* =========================
       EDITABLE FIELDS
    ========================= */
    .editable-field{
        border:1px dashed transparent;
        transition:all .2s;
        padding:0 2px;
        border-radius:2px;
    }

    .editable-field:hover{
        background-color:#f1f5f9;
        border-color:#cbd5e1;
        cursor:text;
    }

    .editable-field:focus{
        outline:2px solid var(--brand-color);
        background-color:#fff;
        border-color:transparent;
    }

    .clean-input{
        width:100%;
        background:transparent;
        border-bottom:1px solid transparent;
        outline:none;
        transition:border-color .2s;
        padding:0;
    }

    .clean-input:focus{
        border-bottom-color:var(--brand-color) !important;
    }

    .clean-input:hover{
        border-bottom-color:#e2e8f0;
    }

    /* =========================
       IMAGES / BADGES
    ========================= */
    .prod-img-container{
        position:relative;
        width:5rem;
        height:5rem;
        border-radius:.25rem;
        overflow:hidden;
        cursor:pointer;
        border:1px solid #e2e8f0;
        background:#f8fafc;
        flex-shrink:0;
    }

    .prod-img-container:hover::after{
        content:'\f030';
        font-family:"Font Awesome 6 Free";
        font-weight:900;
        position:absolute;
        inset:0;
        background:rgba(0,0,0,.3);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.5rem;
    }

    .item-badge{
        position:absolute;
        z-index:10;
        font-size:.5rem;
        font-weight:bold;
        text-transform:uppercase;
        padding:1px 3px;
        border-radius:2px;
        box-shadow:0 1px 2px rgba(0,0,0,.1);
    }

    .badge-tl{ top:0; left:0; }
    .badge-tr{ top:0; right:0; }
    .badge-bl{ bottom:0; left:0; }
    .badge-br{ bottom:0; right:0; }

    /* =========================
       GRID LAYOUTS
    ========================= */
    .pos-header-grid{
        display:grid;
        grid-template-columns:2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem;
        gap:.75rem;
        font-size:.75rem;
        font-weight:300;
        color:#4a4a4a;
        border-bottom:1px solid var(--brand-color);
        padding-bottom:.5rem;
        margin-bottom:1rem;
        align-items:center;
    }

    .pos-row-top{
        display:grid;
        grid-template-columns:2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem;
        gap:.75rem;
        font-size:.8rem;
        font-weight:bold;
        color:#1e293b;
        border-bottom:3px solid #74b2d4;
        padding-bottom:.25rem;
        margin-bottom:.25rem;
        align-items:center;
    }

    .pos-row-bottom{
        display:flex;
        gap:1rem;
        padding-left:2.5rem;
        margin-bottom:.5rem;
    }

    .sub-pos-container{
        margin-top:.5rem;
        padding-left:2.5rem;
    }

    .sub-pos-grid{
        display:grid;
        grid-template-columns:2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem;
        gap:.75rem;
        font-size:.75rem;
        color:#64748b;
        padding:.25rem 0;
        border-bottom:1px dotted #e2e8f0;
        align-items:center;
    }

    /* =========================
       UTILS
    ========================= */
    .btn-primary{ @apply bg-[#93c21c] text-white shadow hover:brightness-105 transition-all active:scale-95 px-4 py-2 rounded font-bold; }
    .btn-disabled{ @apply bg-slate-300 text-white cursor-not-allowed shadow-none; }

    .modal-overlay{
        background-color:rgba(15,23,42,.8);
        backdrop-filter:blur(4px);
    }

    .sidebar-tab{
        @apply flex-1 py-3 text-center text-xs font-bold text-slate-500 hover:text-[#93c21c] border-b-2 border-transparent transition cursor-pointer;
    }

    .sidebar-tab.active{
        @apply text-[#93c21c] border-[#93c21c] bg-slate-50;
    }

    .scroller{
        overflow-y:auto;
        scrollbar-width:thin;
    }

    .scroller::-webkit-scrollbar{
        width:6px;
    }

    .scroller::-webkit-scrollbar-thumb{
        background-color:#cbd5e1;
        border-radius:3px;
    }

    /* =========================
       EDITOR MAIN TABS
    ========================= */
    .editor-tab-btn{
        display:flex;
        align-items:center;
        gap:.5rem;
        padding:.5rem .75rem;
        font-size:.75rem;
        font-weight:900;
        border:1px solid rgb(226 232 240);
        border-bottom:0;
        border-top-left-radius:.75rem;
        border-top-right-radius:.75rem;
        background:rgb(248 250 252);
        color:rgb(100 116 139);
        user-select:none;
        cursor:pointer;
    }

    .editor-tab-btn.active{
        background:#fff;
        color:var(--brand-color);
        border-color:rgb(203 213 225);
        box-shadow:0 -1px 0 #fff inset;
    }

    .editor-tab-panel{
        display:none;
        height:100%;
    }

    .editor-tab-panel.active{
        display:flex;
        height:100%;
    }

    /* =========================
       LIBRARY INNER TABS
    ========================= */
    .lib-subtab-active{
        background:#fff;
        color:var(--brand-color);
        box-shadow:0 1px 3px rgba(0,0,0,.08);
    }

    .lib-subtab-inactive{
        background:transparent;
        color:#64748b;
    }

    /* =========================
       DRAG & DROP
    ========================= */
    .draggable-item{
        cursor:grab;
        user-select:none;
    }

    .draggable-item:active{
        cursor:grabbing;
    }

    .section-drop-zone{
        display:none;
        min-height:0;
        margin:0;
        padding:0;
        border:0;
        opacity:0;
        pointer-events:none;
        transition:all .18s ease;
    }

    body.drag-active .section-drop-zone{
        display:flex;
        min-height:50px;
        margin-bottom:1rem;
        border:2px dashed #e2e8f0;
        border-radius:4px;
        align-items:center;
        justify-content:center;
        opacity:1;
        pointer-events:auto;
    }

    body.drag-active .section-drop-zone.drag-over{
        background-color:#f0fdf4;
        border-color:var(--brand-color);
    }

    body.drag-active .section-drop-zone:empty::after,
    .section-drop-zone:empty::after{
        content:'Produkte hierher ziehen';
        color:#cbd5e1;
        font-size:.7rem;
    }

    .section-drop-zone.drag-over{
        background-color:#f0fdf4;
        border-color:var(--brand-color) !important;
    }

    .item-group{
        transition:all .2s;
        border:1px solid transparent;
        border-radius:4px;
        padding:.5rem;
        margin-bottom:.5rem;
        cursor:grab;
    }

    .item-group:active{
        cursor:grabbing;
    }

    .item-group:hover{
        background-color:#fafafa;
        border-color:#e2e8f0;
    }

    .item-group.drag-over-sub{
        background-color:#eff6ff;
        border-color:#74b2d4;
        box-shadow:0 4px 6px -1px rgba(116,178,212,.2);
    }

    .item-group.drag-over-sub::after{
        content:'+ Unterposition hinzufügen';
        position:absolute;
        top:.5rem;
        right:.5rem;
        font-size:.7rem;
        font-weight:bold;
        color:#74b2d4;
        background:rgba(255,255,255,.9);
        padding:2px 6px;
        border-radius:4px;
        pointer-events:none;
    }

    .item-group.drag-over-sort,
    .list-row-main.drag-over-sort,
    .list-row-sub.drag-over-sort,
    .list-row-child.drag-over-sort{
        background:#f0fdf4 !important;
        box-shadow:inset 0 3px 0 var(--brand-color);
    }

    .list-section-drop.drag-over-sub,
    .list-section-drop.drag-over,
    div.drag-over-sub{
        border-color:var(--brand-color) !important;
        background:#f7fee7 !important;
        color:#6b8e12 !important;
    }

    /* =========================
       TOOLS
    ========================= */
    .floating-element{
        position:absolute;
        cursor:grab;
        z-index:50;
    }

    .floating-element:active{
        cursor:grabbing;
        outline:1px dashed #74b2d4;
    }

    .delete-float{
        position:absolute;
        top:-10px;
        right:-10px;
        background:red;
        color:#fff;
        border-radius:50%;
        width:20px;
        height:20px;
        display:none;
        align-items:center;
        justify-content:center;
        font-size:10px;
        cursor:pointer;
    }

    .floating-element:hover .delete-float{
        display:flex;
    }

    /* =========================
       STATUS
    ========================= */
    .pos-inactive{
        opacity:.5;
        background-image:repeating-linear-gradient(45deg, transparent, transparent 10px, #f1f5f9 10px, #f1f5f9 20px);
    }

    .pos-optional .clean-input,
    .pos-optional .pdf-title-blue{
        color:#94a3b8;
        font-style:italic;
    }

    /* =========================
       LIST VIEW — PREMIUM TABLE
    ========================= */
    .list-shell{
        background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
        border:1px solid #dbe4ee;
        border-radius:1.25rem;
        box-shadow:0 14px 30px rgba(15,23,42,.06);
        overflow:hidden;
    }

    .list-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:1rem 1.25rem;
        border-bottom:1px solid #e2e8f0;
        background:
            radial-gradient(circle at top right, rgba(147,194,28,.08), transparent 30%),
            linear-gradient(180deg, #fff, #f8fafc);
    }

    .list-toolbar-title{
        font-size:.75rem;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.12em;
        color:#64748b;
    }

    .list-toolbar-subtitle{
        font-size:1rem;
        font-weight:900;
        color:#0f172a;
    }

    .list-scroll-x{
        overflow-x:auto;
        overflow-y:hidden;
        scrollbar-width:thin;
        scrollbar-color:#cbd5e1 transparent;
        background:linear-gradient(180deg, #fff, #f8fafc);
    }

    .list-scroll-x::-webkit-scrollbar{
        height:10px;
    }

    .list-scroll-x::-webkit-scrollbar-thumb{
        background:#cbd5e1;
        border-radius:999px;
    }

    .list-scroll-x::-webkit-scrollbar-track{
        background:transparent;
    }

    .list-table{
        min-width:1580px;
    }

    .list-thead{
        position:sticky;
        top:0;
        z-index:15;
        background:linear-gradient(180deg, #0f172a, #1e293b);
        color:#fff;
        border-bottom:1px solid rgba(255,255,255,.06);
    }

    .list-th,
    .list-td{
        padding:2px 2px;
        vertical-align:top;
        border-bottom:1px solid #edf2f7;
        font-size:.75rem;
    }

    

    .list-th{
        font-size:.68rem;
        font-weight:900;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:rgba(255,255,255,.86);
        white-space:nowrap;
    }

    .list-sec-head{
        background:linear-gradient(180deg, #f8fafc, #eef5fb);
        border-top:1px solid #dbe4ee;
        border-bottom:1px solid #dbe4ee;
    }

    .list-cell-input[type="number"]::-webkit-outer-spin-button,
        .list-cell-input[type="number"]::-webkit-inner-spin-button,
        .clean-input[type="number"]::-webkit-outer-spin-button,
        .clean-input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .list-cell-input[type="number"],
        .clean-input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

    .list-sec-head:hover{
        background:linear-gradient(180deg, #f1f5f9, #e7f0f9);
    }

    .list-row-main{ background:#fff; }
    .list-row-main:hover{ background:#fbfdff; }

    .list-row-sub{ background:#f8fafc; }
    .list-row-sub:hover{ background:#f1f5f9; }

    .list-row-child{ background:#eef2f7; }
    .list-row-child:hover{ background:#e6edf5; }

    .list-cell-input,
    .list-cell-select,
    .list-cell-textarea{
        width:100%;
        border:1px solid #dbe4ee;
        border-radius:.75rem;
        background:#fff;
        color:#0f172a;
        outline:none;
        transition:all .18s ease;
        font-size:.75rem;
    }

    .list-cell-input,
    .list-cell-select{
        padding:.5rem .6rem;
    }

    .list-cell-textarea{
        padding:.55rem .65rem;
        min-height:56px;
        resize:vertical;
        line-height:1.4;
    }

    .list-cell-input:focus,
    .list-cell-select:focus,
    .list-cell-textarea:focus{
        border-color:var(--brand-color);
        box-shadow:0 0 0 3px rgba(147,194,28,.12);
    }

    .list-readonly{
        background:#f8fafc !important;
        color:#94a3b8 !important;
        cursor:not-allowed !important;
    }

    .list-pill{
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        padding:.25rem .5rem;
        border-radius:999px;
        font-size:.63rem;
        font-weight:900;
        line-height:1;
        white-space:nowrap;
    }

    .list-pill-green{ background:#ecfccb; color:#4d7c0f; }
    .list-pill-blue{ background:#dbeafe; color:#1d4ed8; }
    .list-pill-orange{ background:#ffedd5; color:#c2410c; }
    .list-pill-slate{ background:#e2e8f0; color:#334155; }
    .list-pill-red{ background:#fee2e2; color:#b91c1c; }
    .list-pill-indigo{ background:#e0e7ff; color:#4338ca; }
    .list-pill-yellow{ background:#fef9c3; color:#a16207; }

    .list-section-drop{
        border:1px dashed #cbd5e1;
        border-radius:1rem;
        padding:.9rem 1rem;
        margin:.85rem 1rem 1rem;
        background:#fff;
        color:#94a3b8;
        font-size:.75rem;
        font-weight:800;
        text-align:center;
    }

    .list-section-drop.drag-over{
        border-color:var(--brand-color);
        background:#f7fee7;
        color:#6b8e12;
    }

    .list-colpicker{
        position:relative;
    }

    .list-colpicker > summary{
        list-style:none;
        cursor:pointer;
    }

    .list-colpicker > summary::-webkit-details-marker{
        display:none;
    }

    .list-colpicker-menu{
        position:absolute;
        right:0;
        top:calc(100% + .5rem);
        width:260px;
        background:#fff;
        border:1px solid #dbe4ee;
        border-radius:1rem;
        box-shadow:0 20px 40px rgba(15,23,42,.12);
        padding:.75rem;
        z-index:60;
    }

    .list-colpicker-item{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.75rem;
        padding:.45rem .25rem;
        border-bottom:1px solid #f1f5f9;
        font-size:.75rem;
        color:#334155;
    }

    .list-colpicker-item:last-child{
        border-bottom:none;
    }

    .list-mini-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.35rem;
        padding:.55rem .8rem;
        border-radius:.85rem;
        border:1px solid #dbe4ee;
        background:#fff;
        color:#334155;
        font-size:.72rem;
        font-weight:900;
        transition:all .18s ease;
    }

    .list-mini-btn:hover{
        border-color:var(--brand-color);
        color:#7ca816;
        box-shadow:0 6px 14px rgba(147,194,28,.12);
    }

    .list-action-btn{
        width:2rem;
        height:2rem;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:.75rem;
        border:1px solid #e2e8f0;
        background:#fff;
        color:#64748b;
        transition:all .18s ease;
    }

    .list-action-btn:hover{
        border-color:var(--brand-color);
        color:#7ca816;
        background:#f7fee7;
    }

    .list-action-btn.danger:hover{
        border-color:#ef4444;
        color:#dc2626;
        background:#fef2f2;
    }

    .list-row-sub .list-cell-input.list-readonly,
    .list-row-sub .list-cell-textarea.list-readonly{
        background:#f8fafc !important;
        font-weight:700;
    }

    #print-preview-content .a4-page{
        flex-shrink:0;
        margin:0 auto 32px auto;
    }

    /* =========================
       DESCRIPTION EDITOR
    ========================= */
    #desc-modal .ql-toolbar.ql-snow{
        border-color:#e2e8f0;
        border-top-left-radius:.75rem;
        border-top-right-radius:.75rem;
        background:#f8fafc;
    }

    #desc-modal .ql-container.ql-snow{
        border-color:#e2e8f0;
        border-bottom-left-radius:.75rem;
        border-bottom-right-radius:.75rem;
        min-height:320px;
        max-height:55vh;
        overflow:auto;
        font-size:.95rem;
    }

    #desc-modal .ql-editor{
        min-height:320px;
        line-height:1.6;
    }

    .desc-preview-html{
        min-height:72px;
        padding:.75rem;
        border:1px dashed #dbe4ee;
        border-radius:.75rem;
        background:#fff;
        color:#334155;
        line-height:1.55;
    }

    .desc-preview-html:empty::before{
        content:'Keine Beschreibung';
        color:#94a3b8;
    }

    .desc-preview-html p{ margin:.25rem 0; }
    .desc-preview-html ul,
    .desc-preview-html ol{
        padding-left:1.25rem;
        margin:.35rem 0;
    }

    .desc-preview-html img{
        max-width:100%;
        height:auto;
        border-radius:.5rem;
        margin:.5rem 0;
    }

    /* =========================
        LEFT SIDEBAR — NEW LAYOUT
        ========================= */
        .sq-side-top{
            padding:1rem;
            border-bottom:1px solid #e2e8f0;
            background:
                radial-gradient(circle at top right, rgba(147,194,28,.12), transparent 38%),
                linear-gradient(180deg, #ffffff, #f8fafc);
        }

        .sq-side-brand{
            display:flex;
            align-items:center;
            gap:.85rem;
            margin-bottom:1rem;
        }

        .sq-side-brand-icon{
            width:2.75rem;
            height:2.75rem;
            border-radius:1rem;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg, #93c21c, #74b2d4);
            color:#fff;
            box-shadow:0 10px 18px rgba(15,23,42,.12);
            font-size:1rem;
            flex-shrink:0;
        }

        .sq-side-kicker{
            font-size:10px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.16em;
            color:#94a3b8;
        }

        .sq-side-title{
            font-size:1rem;
            font-weight:900;
            color:#0f172a;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .sq-side-main-tabs{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:.5rem;
        }

        .sq-side-main-tab{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:.55rem;
            padding:.75rem .9rem;
            border-radius:1rem;
            border:1px solid #dbe4ee;
            background:#fff;
            color:#64748b;
            font-size:.78rem;
            font-weight:900;
            transition:all .2s ease;
            box-shadow:0 2px 6px rgba(15,23,42,.03);
        }

        .sq-side-main-tab:hover{
            border-color:#93c21c;
            color:#7ca816;
            background:#fafff4;
        }

        .sq-side-main-tab.active{
            background:linear-gradient(180deg, #93c21c, #86b21a);
            color:#fff;
            border-color:#93c21c;
            box-shadow:0 10px 20px rgba(147,194,28,.22);
        }

        .sq-side-toolbar{
            padding:1rem;
            border-bottom:1px solid #e2e8f0;
            background:#fff;
            display:flex;
            flex-direction:column;
            gap:.85rem;
        }

        .sq-side-search{
            width:100%;
            border:1px solid #dbe4ee;
            background:#f8fafc;
            color:#0f172a;
            border-radius:1rem;
            padding:.8rem .95rem .8rem 2.65rem;
            font-size:.85rem;
            outline:none;
            transition:all .2s ease;
        }

        .sq-side-search:focus{
            border-color:#93c21c;
            box-shadow:0 0 0 4px rgba(147,194,28,.10);
            background:#fff;
        }

        .sq-side-search-icon{
            position:absolute;
            left:1rem;
            top:50%;
            transform:translateY(-50%);
            color:#94a3b8;
            font-size:.85rem;
        }

        .sq-lib-mode-wrap{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:.45rem;
            background:#f8fafc;
            padding:.35rem;
            border-radius:1rem;
            border:1px solid #e2e8f0;
        }

        .sq-lib-mode-btn{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap:.3rem;
            min-height:64px;
            padding:.55rem .4rem;
            border-radius:.85rem;
            font-size:.69rem;
            font-weight:900;
            color:#64748b;
            transition:all .18s ease;
            text-align:center;
        }

        .sq-lib-mode-btn:hover{
            background:#fff;
            color:#7ca816;
        }

        .lib-subtab-active{
            background:#fff !important;
            color:#7ca816 !important;
            box-shadow:0 8px 18px rgba(15,23,42,.08);
            border:1px solid rgba(147,194,28,.18);
        }

        .lib-subtab-inactive{
            background:transparent !important;
            color:#64748b !important;
            border:1px solid transparent;
        }

        .sq-side-content{
            flex:1;
            overflow-y:auto;
            padding:1rem;
            display:flex;
            flex-direction:column;
            gap:.75rem;
            background:
                linear-gradient(180deg, rgba(248,250,252,.95), rgba(241,245,249,.95));
        }

        .sq-tool-hero{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding:.9rem 1rem;
            border-radius:1rem;
            background:linear-gradient(135deg, rgba(116,178,212,.10), rgba(147,194,28,.10));
            border:1px solid #dbe4ee;
        }

        .sq-upload-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:.5rem;
            padding:.7rem .95rem;
            border-radius:.9rem;
            background:#0f172a;
            color:#fff;
            font-size:.75rem;
            font-weight:900;
            transition:all .18s ease;
            box-shadow:0 10px 18px rgba(15,23,42,.12);
        }

        .sq-upload-btn:hover{
            background:#1e293b;
        }

        .sq-tool-tip{
            font-size:.72rem;
            color:#64748b;
            line-height:1.5;
            padding:0 .1rem;
        }

        .sq-tools-grid{
            flex:1;
            overflow-y:auto;
            padding:1rem;
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:.85rem;
            background:
                linear-gradient(180deg, rgba(248,250,252,.95), rgba(241,245,249,.95));
        }

        /* nicer tool cards */
        #tools-list > div{
            background:#fff;
            border:1px solid #dbe4ee;
            border-radius:1rem;
            padding:.7rem;
            box-shadow:0 8px 16px rgba(15,23,42,.05);
            transition:all .18s ease;
        }

        #tools-list > div:hover{
            transform:translateY(-2px);
            border-color:#93c21c;
            box-shadow:0 14px 26px rgba(15,23,42,.08);
        }

        #tools-list img{
            border-radius:.75rem;
            background:#f8fafc;
            object-fit:contain;
        }

        /* nicer library list cards */
        #sidebar-list > details,
        #sidebar-list > div{
            border-radius:1rem !important;
        }

        #sidebar-list details{
            overflow:hidden;
            border:1px solid #dbe4ee !important;
            background:#fff !important;
            box-shadow:0 8px 18px rgba(15,23,42,.05);
        }

        #sidebar-list details > summary{
            background:linear-gradient(180deg, #ffffff, #f8fafc) !important;
            padding:.85rem 1rem !important;
        }

        #doc-cover-text{
            min-height: 120px;
        }

        #doc-cover-text:hover{
            background: #f8fafc;
            box-shadow: inset 0 0 0 1px rgba(147,194,28,.18);
        }

        #sidebar-list .bg-white.border{
            border:1px solid #dbe4ee !important;
            border-radius:1rem !important;
            box-shadow:0 8px 16px rgba(15,23,42,.05);
            transition:all .18s ease;
        }

        #sidebar-list .bg-white.border:hover{
            border-color:#93c21c !important;
            box-shadow:0 14px 26px rgba(15,23,42,.08);
            transform:translateY(-1px);
        }



    /* =========================
       PRINT
    ========================= */
    @media print{
        body{
            background:#fff;
            height:auto;
            overflow:visible;
        }

        .no-print,
        .sidebar-panel,
        .thumb-container,
        header{
            display:none !important;
        }

        .a4-page{
            margin:0;
            box-shadow:none;
            page-break-after:always;
            width:100%;
            height:100%;
            border:none;
        }

        .section-drop-zone,
        .delete-float{
            display:none !important;
        }

        .item-group{
            border:none;
            padding:0;
            margin-bottom:1rem;
            cursor:default;
        }
    }
</style>

<style>
    .drag-handle{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:28px;
        height:28px;
        border:1px solid #e2e8f0;
        border-radius:.6rem;
        background:#fff;
        color:#94a3b8;
        cursor:grab;
        transition:all .18s ease;
    }
    .drag-handle:hover{
        border-color:var(--brand-color);
        color:#7ca816;
        background:#f7fee7;
    }
    .drag-handle:active{
        cursor:grabbing;
    }
</style>
<style>
     .material-table-shell {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 14px 30px rgba(15,23,42,.06);
    }
    .material-grid-scroll {
        position: relative;
        overflow: auto;
        max-height: 72vh;
        background: #fff;
        scrollbar-width: thin;
    }
    .material-sticky-head {
        position: sticky;
        top: 0;
        z-index: 40;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 4px 14px rgba(15,23,42,.06);
    }
    .mat-head-row {
        min-width: max-content;
    }
    .mat-head-cell {
        display: flex;
        align-items: center;
        min-height: 48px;
        padding: 0 14px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        background: rgba(248,250,252,.95);
        border-right: 1px solid #eef2f7;
    }
    .mat-head-cell:last-child { border-right: none; }
    .mat-data-row {
        position: relative;
        background: #fff;
        border-bottom: 1px solid #eef2f7;
        transition: background-color 0.2s;
    }
    .mat-data-row.is-main-row:hover { background: #fbfdff; }
    .mat-data-row.is-sub-row {
        background: #f8fafc;
        border-left: 6px solid #78b2ce;
    }
    .mat-data-grid {
        min-width: max-content;
        align-items: stretch;
    }
    .mat-grid-cell {
        padding: 10px 14px;
        border-right: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        min-height: 64px;
    }
    .mat-grid-cell:last-child { border-right: none; }
    .mat-grid-cell-center { justify-content: center; text-align: center; }
    .mat-grid-cell-right { justify-content: flex-end; text-align: right; }
    
    .mat-ctrl {
        height: 34px;
        min-height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        transition: all .18s ease;
    }
    .mat-ctrl:focus {
        border-color: #93c21c;
        box-shadow: 0 0 0 3px rgba(147,194,28,.14);
    }
    .mat-ctrl[readonly], .mat-ctrl:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .mat-input { width: 100%; padding: 0 10px; }
    .mat-input-center { text-align: center; padding: 0 8px; }
    .mat-input-right { text-align: right; padding: 0 10px; }
    
    .mat-addon-wrap {
        display: flex;
        align-items: center;
        height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
    }
    .mat-addon-wrap:focus-within {
        border-color: #93c21c;
        box-shadow: 0 0 0 3px rgba(147,194,28,.14);
    }
    .mat-addon-input {
        border: 0; outline: 0; background: transparent;
        height: 100%; width: 100%; padding: 0 8px;
        font-size: 12px; font-weight: 700; color: #334155;
    }
    .mat-addon-text {
        height: 100%; padding: 0 10px 0 0; display: inline-flex;
        align-items: center; font-size: 11px; font-weight: 800; color: #94a3b8;
    }
    .mat-btn-icon {
        height: 34px;
        width: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .18s ease;
    }
    .mat-btn-icon:hover {
        border-color: #93c21c;
        color: #93c21c;
        background: #f8fbfe;
    }
</style>

     

</head>
<body>

    <!-- Hidden File Inputs -->
    <input type="file" id="img-upload-input" accept="image/*" class="hidden">
    <input type="file" id="badge-upload-input" accept="image/*" class="hidden">
    <input type="file" id="tool-upload-input" accept="image/*" class="hidden">

    <!-- VIEW 1: WIZARD -->
    <div id="view-start" class="view-section active bg-slate-100 flex items-center justify-center p-6">
        <div class="max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-visible flex flex-col md:flex-row min-h-[550px] z-50">
            <div class="w-full md:w-5/12 bg-[#93c21c] p-10 text-white flex flex-col justify-between relative overflow-hidden rounded-l-2xl">
                <div class="relative z-10"><div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center text-[#93c21c] font-bold text-3xl shadow mb-8">S</div><h1 class="text-4xl font-bold mb-4">Start</h1><p class="text-white/90 text-sm leading-relaxed">Erstellen Sie professionelle Angebote und Kostenvoranschläge.</p></div>
                <div class="absolute -bottom-10 -right-10 opacity-20 transform"><i class="fa-regular fa-sun text-[250px]"></i></div>
            </div>
            <div class="w-full md:w-7/12 p-10 flex flex-col">
                <div class="flex-1 space-y-6">
                    <div class="relative">
                        <label class="block text-sm font-bold text-slate-700 mb-2">1. Kunde</label>
                        <div class="relative">
                            <input type="text" id="wiz-customer-search" oninput="App.Wizard.filterCustomers()" onfocus="App.Wizard.filterCustomers()" placeholder="Suche..." class="w-full border border-slate-300 rounded-lg p-3 pl-10 text-sm outline-none focus:border-[#93c21c]"><div id="wiz-customer-dropdown" class="absolute w-full bg-white border border-slate-200 rounded-b-lg shadow-lg z-50 hidden max-h-40 overflow-y-auto">
                            </div>
                        </div>
                        <div id="wiz-customer-selected" class="hidden mt-2 p-3 bg-[#f7fee7] rounded-lg border border-[#93c21c]/30 flex justify-between items-center">
                            <div>
                                <div class="font-bold text-slate-800" id="wiz-sel-cust-name"></div>
                                <div class="text-xs text-slate-500" id="wiz-sel-cust-addr"></div>
                            </div>
                            <button onclick="App.Wizard.clearCustomer()" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                        </div>
                    </div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">2. Objekt</label>
                        <!-- ✅ 3) Replace your select markup (remove size="6") -->
                        <select id="wiz-object-select" multiple onchange="App.Wizard.selectObject()"
                        class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c] bg-white">
                        </select>

                        <div class="mt-2 text-xs text-slate-500">
                            Ausgewählt: <span id="wiz-object-count" class="font-bold">0</span>
                        </div>
                    </div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-3"><label class="block text-sm font-bold text-slate-700 mb-2">3. Datum</label><input type="date" id="wiz-date" class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c]"></div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-4"><label class="block text-sm font-bold text-slate-700 mb-2">4. Typ</label><div class="flex gap-4"><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Angebot" checked class="accent-[#93c21c]"><span class="text-sm font-bold">Angebot</span></label><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Kostenvoranschlag" class="accent-[#93c21c]"><span class="text-sm font-bold">Kostenvoranschlag</span></label></div></div>
                     
                    <div class="relative border-t pt-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">5. Branding</label>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Brand mode -->
                        <div class="col-span-2">
                        <label class="text-xs text-slate-500 block mb-1">Logo-Typ</label>
                        <div class="flex flex-wrap gap-3">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 cursor-pointer">
                            <input type="radio" name="wiz-brand-mode" value="text" checked class="accent-[#93c21c]" onchange="App.updateBranding()">
                            Logo-Text
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700 cursor-pointer">
                            <input type="radio" name="wiz-brand-mode" value="image" class="accent-[#93c21c]" onchange="App.updateBranding()">
                            Logo aus Datenbank
                            </label>
                        </div>
                        </div>

                        <!-- Text name -->
                        <div id="brand-text-wrap">
                        <label class="text-xs text-slate-500 block mb-1">Firmenname (Logo)</label>
                        <input type="text" id="wiz-brand-name" value="SOLAR ASPEKT" oninput="App.updateBranding()"
                                class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:border-[#93c21c] outline-none">
                        </div>

                        <!-- Logo select -->
                        <div id="brand-logo-wrap" class="hidden">
                        <label class="text-xs text-slate-500 block mb-1">Logo auswählen</label>
                        <select id="wiz-brand-logo" onchange="App.updateBranding()"
                                class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:border-[#93c21c] outline-none bg-white">
                            <option value="{{ asset('logo/logo.png') }}">Solar Aspekt</option>
                            <option value="{{ asset('logo/werk.png') }}">Werkstuio</option>
                        </select>

                        <div class="mt-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded border border-slate-200 bg-white flex items-center justify-center overflow-hidden">
                            <img id="wiz-logo-preview" src="{{ asset('logo/logo.png') }}" class="w-full h-full object-contain" alt="logo">
                            </div>
                            <div class="text-xs text-slate-500">Vorschau</div>
                        </div>
                        </div>

                        <!-- Color -->
                        <div class="col-span-2">
                        <label class="text-xs text-slate-500 block mb-1">Hauptfarbe</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="wiz-brand-color" value="#93c21c" oninput="App.updateBranding()"
                                class="w-10 h-10 p-1 rounded cursor-pointer border">
                            <span id="color-hex-label" class="text-xs text-slate-500 font-mono">#93c21c</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1">
                            Diese Farbe wird auf das ganze Angebot angewendet (Header, Linien, Titel, Akzente).
                        </div>
                        </div>
                    </div>
                    </div>

                </div>
                <div class="border-t border-slate-100 pt-8 mt-4 flex justify-end"><button id="wiz-btn-start" onclick="App.startQuote()" class="btn-primary btn-disabled flex items-center gap-3 text-lg px-8 py-3 transition-all" disabled><span>Starten</span> <i class="fa-solid fa-arrow-right"></i></button></div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: EDITOR -->
    <div id="view-editor" class="view-section flex-1 overflow-hidden relative bg-slate-100">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 shrink-0 z-50 shadow-sm no-print">
            <div class="flex items-center gap-4">
                <button onclick="App.toggleSidebar('left')" class="text-slate-400 hover:text-[#93c21c] w-8 h-8 rounded hover:bg-slate-100 flex items-center justify-center border border-transparent hover:border-slate-300"><i class="fa-solid fa-bars"></i></button>
                <div class="h-6 w-px bg-slate-200"></div>
                <div class="font-bold text-slate-700 flex items-center gap-2"><span id="editor-doc-type-label" class="text-[#93c21c]">Angebot</span><span class="text-xs text-slate-400 font-normal">| <span id="lbl-total-pages">1</span> Seiten</span></div>
            </div>

            <!-- MAIN TABS (A4 / LIST) -->
            <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
            <button id="main-tab-list"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c]"
                    onclick="App.Tabs.switch('list')">
                <i class="fa-solid fa-list-check mr-2"></i>Listenansicht
            </button>
            <button id="main-tab-a4"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c] bg-white shadow"
                    onclick="App.Tabs.switch('a4')"> Druckansicht
                <i class="fa-solid fa-file-lines mr-2"></i>
            </button>
            
            <button id="main-tab-settings" class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c]" onclick="App.Tabs.switch('settings')">
                <i class="fa-solid fa-sliders mr-2"></i>Einstellung
            </button>

            <button id="main-tab-bio"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c]"
                    onclick="App.Tabs.switch('bio')">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i>Biografie
            </button>
            </div>

            <div class="flex items-center gap-3" >
                <div class="flex items-center gap-3">
                    <div class="relative" id="editor-actions-menu-wrap">
                        <button type="button"
                        id="editor-actions-menu-btn"
                        onclick="App.toggleEditorActionsMenu()"
                        class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded-xl text-sm font-bold text-slate-600 hover:text-[#93c21c] flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>Aktionen</span>
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </button>

                        <div id="editor-actions-menu"
                            class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[80]">
                            
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">Editor Menü</div>
                                <div class="text-sm font-black text-slate-800">Seiten & Ansicht</div>
                            </div>

                            <div class="p-2">
                                <button type="button"
                                        onclick="App.addPageAfterCurrent(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-[#f7fee7] hover:text-[#7ca816] transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-[#f7fee7] text-[#7ca816] flex items-center justify-center">
                                        <i class="fa-solid fa-file-circle-plus"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Neue Seite</span>
                                        <span class="block text-[11px] text-slate-400 font-medium">Seite nach der aktuellen Seite einfügen</span>
                                    </span>
                                </button>

                                <button type="button"
                                        onclick="App.addSection(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-[#f7fee7] hover:text-[#7ca816] transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-[#f7fee7] text-[#7ca816] flex items-center justify-center">
                                        <i class="fa-solid fa-folder-plus"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Neue Sektion</span>
                                        <span class="block text-[11px] text-slate-400 font-medium">Neuen Abschnitt im Angebot anlegen</span>
                                    </span>
                                </button>

                                <div class="mx-2 my-2 h-px bg-slate-100"></div>

                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-bold text-slate-700">Versteckte anzeigen</span>
                                        <span class="block text-[11px] text-slate-400 font-medium">Ausgeblendete Positionen sichtbar machen</span>
                                    </span>
                                    <input type="checkbox"
                                        id="show-hidden-toggle"
                                        onchange="App.renderQuotePage()"
                                        checked
                                        class="accent-[#93c21c] w-4 h-4">
                                </label>

                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                                        <i class="fa-solid fa-images"></i>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-bold text-slate-700">Miniaturseiten anzeigen</span>
                                        <span class="block text-[11px] text-slate-400 font-medium">Linke Seitenvorschau ein-/ausblenden</span>
                                    </span>
                                    <input type="checkbox"
                                        id="toggle-thumbnails"
                                        onchange="App.toggleThumbnails(this.checked)"
                                        class="accent-[#93c21c] w-4 h-4">
                                </label>

                                <div class="mx-2 my-2 h-px bg-slate-100"></div>

                                <button type="button"
                                        onclick="App.askDeleteCurrentPage(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                        <i class="fa-solid fa-trash"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Aktuelle Seite löschen</span>
                                        <span class="block text-[11px] text-red-400 font-medium">Entfernt die aktive Seite mit Inhalt</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>



                    <button onclick="App.openSaveModal()" 
                            class="bg-[#93c21c] text-white hover:brightness-105 px-4 py-1.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Speichern</span>
                    </button>
                    <button onclick="App.openPrintPreview()"
                            class="bg-slate-800 text-white hover:bg-slate-700 px-3 py-1.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <i class="fa-solid fa-print"></i>
                        <span>Druck-Vorschau</span>
                    </button>
                </div>
                <button id="calc-sidebar-toggle-btn"
                        onclick="App.toggleSidebar('right')"
                        class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c] flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-calculator"></i>
                    <span id="calc-sidebar-toggle-label">Kalkulation öffnen</span>
                </button>            
            </div>
        </header>

        <div class="flex h-full overflow-hidden relative">
            <!-- LEFT SIDEBAR -->
            <aside id="sidebar-left" class="w-[360px] bg-white border-r border-slate-200 flex flex-col z-20 shadow-xl flex-shrink-0 sidebar-panel no-print overflow-hidden">
    
                <!-- Sidebar Header -->
                <div class="sq-side-top">
                    <div class="sq-side-brand">
                        <div class="sq-side-brand-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="sq-side-kicker">Editor Panel</div>
                            <div class="sq-side-title">Bibliothek & Tools</div>
                        </div>
                    </div>

                    <div class="sq-side-main-tabs">
                        <button id="tab-lib" class="sq-side-main-tab active" onclick="App.switchSidebarTab('lib')">
                            <i class="fa-solid fa-books"></i>
                            <span>Bibliothek</span>
                        </button>

                        <button id="tab-tools" class="sq-side-main-tab" onclick="App.switchSidebarTab('tools')">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            <span>Tools</span>
                        </button>
                    </div>
                </div>

                <!-- LIBRARY -->
                <div id="sidebar-content-lib" class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
                    <div class="sq-side-toolbar">
                        <div class="relative">
                            <input type="text"
                                id="sidebar-search"
                                oninput="App.renderSidebar()"
                                placeholder="Bibliothek durchsuchen..."
                                class="sq-side-search">
                            <i class="fa-solid fa-magnifying-glass sq-side-search-icon"></i>
                        </div>

                        <div class="sq-lib-mode-wrap">
                            <button id="lib-subtab-group"
                                    class="sq-lib-mode-btn"
                                    onclick="App.switchLibraryMode('group_sets')">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>Group Sets</span>
                            </button>

                            <button id="lib-subtab-sets"
                                    class="sq-lib-mode-btn"
                                    onclick="App.switchLibraryMode('sets')">
                                <i class="fa-solid fa-cubes"></i>
                                <span>Sets</span>
                            </button>

                            <button id="lib-subtab-products"
                                    class="sq-lib-mode-btn"
                                    onclick="App.switchLibraryMode('products')">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Produkte</span>
                            </button>
                        </div>
                    </div>

                    <div class="sq-side-content scroller" id="sidebar-list"></div>
                </div>

                <!-- TOOLS -->
                <div id="sidebar-content-tools" class="flex-1 flex flex-col h-full overflow-hidden hidden bg-slate-50">
                    <div class="sq-side-toolbar">
                        <div class="sq-tool-hero">
                            <div>
                                <div class="sq-side-kicker">Werkzeuge</div>
                                <div class="text-sm font-black text-slate-800">Sticker, Logos & freie Bilder</div>
                            </div>

                            <button onclick="document.getElementById('tool-upload-input').click()"
                                    class="sq-upload-btn">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Upload</span>
                            </button>
                        </div>

                        <div class="sq-tool-tip">
                            Ziehen Sie Bilder oder Sticker direkt auf die Seite.
                        </div>
                    </div>

                    <div class="sq-tools-grid scroller" id="tools-list"></div>
                </div>
            </aside>

            <!-- NAV & CANVAS -->
            <div class="flex flex-1 overflow-hidden relative">
                <div id="nav-pane" class="thumb-container no-print scroller hidden"></div>
                <main id="panel-a4" class="flex-1 relative flex flex-col h-full overflow-y-auto scroller items-center py-8 gap-8 bg-slate-100/50"  >
                    <!-- Page 1 -->
                    <div class="a4-page flex-shrink-0 group flex flex-col" id="page-1" ondrop="App.dropTool(event, 1)" ondragover="App.allowDrop(event)">
                        <!-- Page 1 Content (Letterhead) -->
                        <div class="flex justify-between items-start mb-12 pt-4">
                            <div class="mt-2"><div class="text-[9px] text-slate-400 underline decoration-slate-300 underline-offset-2 mb-6 editable-field w-fit" contenteditable="true" id="doc-company-header">SOLAR ASPEKT GmbH • Am Kappengraben 10 • 61273 Wehrheim</div><div class="text-[13px] leading-relaxed text-slate-800"><div class="font-bold mb-1 editable-field w-fit" contenteditable="true">Herr</div><div id="doc-cust-name" class="font-bold mb-1 editable-field w-fit" contenteditable="true">Max Mustermann</div><div id="doc-cust-addr" class="editable-field w-fit whitespace-pre-line" contenteditable="true">Musterstraße 10<br>12345 Musterstadt</div></div></div>
                            <div class="flex flex-col items-end"><div class="text-right mb-10"><div class="text-2xl font-black text-[#93c21c] tracking-tight" id="doc-logo-text">SOLAR ASPEKT</div></div><div class="text-right"><div class="text-[10px] font-bold text-[#93c21c] mb-1 uppercase tracking-wider">Ihr Ansprechpartner</div><div class="border-r-2 border-[#93c21c] pr-3 py-1"><div class="font-bold text-sm text-slate-800 editable-field" contenteditable="true">Herr Yama Nuri</div><div class="text-[11px] text-slate-600 mt-1 editable-field" contenteditable="true">Tel: 0 60 81/68 288 78<br>E-Mail: anfrage@solar-aspekt.de</div></div></div></div>
                        </div>
                        <div class="mb-10 flex justify-between items-end border-b-2 border-slate-100 pb-4">
                            <div><div class="text-[11px] text-slate-400 uppercase tracking-wide font-bold mb-1" id="lbl-doc-id-name">Angebotsnummer</div><div class="text-lg font-bold text-slate-800 bg-slate-50 border border-dashed border-slate-300 rounded px-2 py-1 w-40"><input type="text" id="doc-offer-id" value="SA-AG25342" oninput="App.syncDocData('offerId', this.value)" class="bg-transparent outline-none w-full text-slate-800 font-bold"></div></div>
                            <div class="text-right"><div class="text-[11px] text-slate-400 uppercase tracking-wide font-bold mb-1">Kundennummer</div><div class="text-sm font-bold text-slate-600 bg-slate-50 border border-dashed border-slate-300 rounded px-2 py-1 w-32 inline-block"><input type="text" id="doc-cust-id" value="KD-1005" oninput="App.syncDocData('custId', this.value)" class="bg-transparent outline-none w-full text-right"></div><div class="text-[12px] text-slate-600 mt-2 editable-field" contenteditable="true" id="doc-date-line">Wehrheim, 27.08.2025</div></div>
                        </div>
                        <div class="mb-8"><div class="text-xl font-bold text-[#93c21c] uppercase leading-tight editable-field" contenteditable="true" id="doc-main-title">Unverbindliches Angebot...</div></div>
                        <div id="doc-cover-text"
                            class="text-[13px] text-slate-700 leading-relaxed space-y-4 p-2 hover:bg-slate-50 rounded -ml-2 border border-dashed border-transparent hover:border-[#93c21c] cursor-pointer transition"
                            onclick="App.openCoverTextModal()">
                            <p>Sehr geehrter Herr <span id="doc-cust-lastname">Mustermann</span>,</p>
                            <p>wir freuen uns, Ihnen dieses Dokument unterbreiten zu dürfen.</p>
                            <p>Mit sonnigen Grüßen<br><span class="font-bold" id="doc-team-name">Ihr SOLAR-ASPEKT-Team</span></p>
                        </div>
                        <div class="mt-auto border-t-2 border-[#93c21c] pt-4 grid grid-cols-4 gap-4 text-[9px] text-slate-500 leading-tight">
                            <div class="editable-field" contenteditable="true"><span class="font-bold text-slate-700" id="footer-company">SOLAR ASPEKT GmbH</span><br>Am Kappengraben 10<br>61273 Wehrheim</div>
                            <div class="editable-field" contenteditable="true"><span class="font-bold text-slate-700">Kontakt</span><br>Tel. 0 60 81/68 288 78<br>hallo@solar-aspekt.de</div>
                            <div class="editable-field" contenteditable="true"><span class="font-bold text-slate-700">Bankverbindung</span><br>Volksbank Frankfurt<br>IBAN: DE12 3456...</div>
                            <div class="editable-field" contenteditable="true"><span class="font-bold text-slate-700">Registergericht</span><br>AG Bad Homburg HRB 12036<br>GF: Yama Nuri</div>
                        </div>
                    </div>
                    <!-- Dynamic Page Container -->
                    <div id="position-pages-container" class="flex flex-col gap-8 w-full items-center"></div>
                </main>


                  <main id="panel-list" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="w-full max-w-[96vw] mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">  
                            <div id="listview-root" class="p-4"></div>
                        </div>
                    </div>
                </main>

                <main id="panel-settings" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="max-w-7xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200">
                                <div class="text-xs font-black text-slate-500 uppercase tracking-wider">Konfiguration</div>
                                <div class="text-lg font-black text-slate-800">Kalkulations-Einstellungen</div>
                            </div>
                            <div id="settings-root" class="p-6"></div>
                        </div>
                    </div>
                </main>

                <main id="panel-bio" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="max-w-6xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200">
                                <div class="text-xs font-black text-slate-500 uppercase tracking-wider">Historie</div>
                                <div class="text-lg font-black text-slate-800">Biografie & Online-Bearbeiter</div>
                            </div>
                            <div id="bio-root" class="p-6"></div>
                        </div>
                    </div>
                </main>


            </div>

            <!-- RIGHT SIDEBAR -->
            <aside id="sidebar-right" class="w-80 bg-white border-l border-slate-200 flex flex-col z-20 shadow-lg flex-shrink-0 sidebar-panel sidebar-collapsed no-print">
                <div class="p-4 border-b border-slate-200 bg-[#f7fee7] flex justify-between items-center">
                <h3 class="font-bold text-[#6b8e12] text-sm uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    Kalkulations-Sidebar
                </h3>
                <div class="flex items-center gap-1"><span class="text-[10px] font-bold text-slate-400">MwSt</span><input type="number" id="global-tax" value="19" onchange="App.updateTaxRate(this.value)" class="w-10 text-xs border rounded text-center font-bold text-slate-700"><span class="text-[10px] text-slate-400">%</span></div></div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4 scroller bg-slate-50/50" id="calc-sidebar-content"></div>
                <div class="p-6 bg-white border-t border-slate-200"><div class="flex justify-between items-end mb-1"><span class="text-xs text-slate-500 uppercase font-bold">Netto</span><span class="text-sm font-mono text-slate-600" id="sidebar-grand-net">0,00 €</span></div><div class="flex justify-between items-end mb-4"><span class="text-xs text-slate-500 uppercase font-bold">MwSt (<span id="lbl-tax-rate">19</span>%)</span><span class="text-sm font-mono text-slate-600" id="sidebar-grand-gross">0,00 €</span></div><div class="pt-4 border-t border-slate-100"><div class="text-xs text-[#93c21c] font-bold uppercase mb-1">Gesamtinvestition</div><div class="text-3xl font-bold text-slate-800 font-mono tracking-tight" id="sidebar-grand-total">0,00 €</div></div></div>
            </aside>
        </div>
    </div>

    <!-- PRINT PREVIEW OVERLAY -->
    <div id="print-preview-modal" class="fixed inset-0 z-[200] hidden bg-slate-900/95 backdrop-blur-sm flex flex-col">
        <div class="h-16 bg-slate-800 flex items-center justify-between px-6 text-white shrink-0 shadow-md no-print">
            <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-print"></i> Druckvorschau (Aktive Positionen)</h3>
            <div class="flex gap-4">
                <button onclick="window.print()" class="bg-[#93c21c] hover:brightness-110 px-6 py-2 rounded font-bold text-sm shadow transition">Drucken</button>
                <button onclick="document.getElementById('print-preview-modal').classList.add('hidden')" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-times text-2xl"></i></button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-8 flex flex-col items-center gap-8" id="print-preview-content"></div>
    </div>

    <!-- MODALS (Settings, Badges, Sets) -->
    <div id="pos-settings-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closePosSettings()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-[450px] overflow-hidden animate-fadeIn"><div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-slate-800">Position bearbeiten</h3><button onclick="App.closePosSettings()" class="text-slate-400"><i class="fa-solid fa-times"></i></button></div><div class="p-4 space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-slate-500 mb-1">Menge</label><input type="number" step="0.01" id="setting-qty" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Einheit</label>
                <select id="setting-unit" class="w-full border rounded p-2 text-sm bg-white"></select>
            </div>
        <div><label class="block text-xs font-bold text-slate-500 mb-1">Einkaufspreis (EK)</label><input type="number" id="setting-ek" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Marge (%)</label><input type="number" id="setting-margin" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div></div><div class="bg-[#f0fdf4] p-3 rounded border border-[#93c21c]"><div class="flex justify-between items-center"><span class="text-xs font-bold text-[#93c21c]">Verkaufspreis (VK) pro Einheit</span><input type="number" id="setting-vk" class="w-24 text-right bg-transparent font-bold font-mono outline-none" oninput="App.calcPosSettings(true)"></div></div><div class="space-y-2 pt-2 border-t border-slate-100"><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-pauschal" class="accent-[#93c21c]"> <span>Als Pauschalposition</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-price" class="accent-[#93c21c]"> <span>Preise ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-numbering" class="accent-[#93c21c]"> <span>Nummerierung ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-image" class="accent-[#93c21c]"> <span>Bild ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-active" class="accent-[#93c21c]"> <span>Position Aktiv</span></label></div><button onclick="App.savePosSettings()" class="w-full bg-[#93c21c] text-white rounded py-2 font-bold text-sm">Speichern</button></div></div></div>    <!-- ✅ Set Modal (rewritten, clean + readable, same IDs/hooks kept) -->
        <div id="set-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" onclick="App.closeModal()"></div>

        <!-- Panel -->
        <div
            class="absolute top-1/2 left-1/2 w-[calc(100%-2rem)] max-w-3xl -translate-x-1/2 -translate-y-1/2
                overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5
                flex flex-col max-h-[85vh]"
        >
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-lime-50 to-white">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                <div class="text-[10px] font-bold text-[#93c21c] uppercase tracking-wider mb-1">
                    Set-Inhalt
                </div>

                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight truncate" id="modal-title">
                    Produkt Name
                </h3>

                <p class="text-sm text-slate-600 mt-1 line-clamp-2" id="modal-desc">
                    Beschreibung
                </p>
                </div>

                <button
                type="button"
                onclick="App.closeModal()"
                class="shrink-0 w-9 h-9 rounded-full bg-white text-slate-500
                        border border-slate-200 shadow-sm hover:bg-slate-50 hover:text-slate-700
                        active:scale-[0.98] transition flex items-center justify-center"
                aria-label="Modal schließen"
                >
                <i class="fa-solid fa-times"></i>
                </button>
            </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-6 py-5 scroller">
            <!-- Materials -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-bold text-slate-800">Komponenten</h4>
                <span class="text-xs text-slate-500">Material</span>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2">Komponenten</th>
                        <th class="px-4 py-2 text-right">Wert</th>
                    </tr>
                    </thead>
                    <tbody id="modal-materials" class="divide-y divide-slate-100">
                    <!-- rows injected -->
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Labor -->
            <div>
                <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-bold text-slate-800">Dienstleistung</h4>
                <span class="text-xs text-slate-500">Arbeitszeit</span>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2">Dienstleistung</th>
                            <th class="px-4 py-2">Qualifikation</th>
                            <th class="px-4 py-2 text-center">Menge</th>
                            <th class="px-4 py-2 text-right">Wert</th>
                        </tr>
                        </thead>

                    <tbody id="modal-labor" class="divide-y divide-slate-100">
                    <!-- rows injected -->
                    </tbody>
                </table>
                </div>
            </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-5 border-t border-slate-200 bg-slate-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-slate-500">
                Tipp: Klick außerhalb schließt das Fenster.
                </div>

                <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="App.closeModal()"
                    class="px-4 py-2 rounded-xl text-slate-600 font-semibold
                        border border-slate-200 bg-white hover:bg-slate-50
                        active:scale-[0.98] transition"
                >
                    Abbrechen
                </button>

                <button
                    type="button"
                    id="modal-add-btn"
                    class="px-6 py-2 rounded-xl bg-[#93c21c] text-white font-extrabold
                        shadow-md hover:brightness-105 active:scale-[0.98] transition
                        flex items-center gap-2 justify-center"
                >
                    <i class="fa-solid fa-plus"></i>
                    Zum Angebot
                </button>
                </div>
            </div>
            </div>
        </div>
        </div>

    <!-- ✅ ADD THIS MODAL (place it near your other modals, before </body>) -->
    <div id="desc-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 modal-overlay" onclick="App.closeDescModal()"></div>

    <div class="absolute top-1/2 left-1/2 w-[96vw] max-w-5xl transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold text-[#93c21c] uppercase tracking-wider">Beschreibung</div>
            <div class="font-bold text-slate-800" id="desc-modal-title">Position bearbeiten</div>
        </div>
        <button onclick="App.closeDescModal()" class="text-slate-400 hover:text-slate-700">
            <i class="fa-solid fa-times"></i>
        </button>
        </div>

        <div class="p-4">
        <div id="desc-quill" class="bg-white"></div>
        <div class="flex items-center justify-between mt-3">
            <div class="text-xs text-slate-400">
            Tipp: Inhalte werden als HTML gespeichert (für Angebot).
            </div>
            <div class="flex gap-2">
            <button onclick="App.closeDescModal()" class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-bold text-sm">Abbrechen</button>
            <button onclick="App.saveDescModal()" class="px-5 py-2 rounded-lg bg-[#93c21c] text-white font-bold text-sm shadow hover:brightness-105">
                Speichern
            </button>
            </div>
        </div>
        </div>
    </div>
    </div>

    <!-- ✅ 2) CONFIRM "TOASTER MODAL" (place near other modals, before </body>) -->
    <div id="toast-confirm" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px]" onclick="App.toastConfirmHide()"></div>

    <!-- toaster card -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[92vw] max-w-lg">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-[fadeIn_.18s_ease-out]">
        <div class="p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0">
            <div id="toast-confirm-title" class="font-black text-slate-800">Löschen?</div>
            <div id="toast-confirm-msg" class="text-sm text-slate-600 mt-1">
                Diese Aktion kann nicht rückgängig gemacht werden.
            </div>
            </div>
        </div>

        <div class="px-4 pb-4 flex items-center justify-end gap-2">
            <button id="toast-confirm-cancel"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">
            Abbrechen
            </button>
            <button id="toast-confirm-ok"
            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-sm shadow">
            Löschen
            </button>
        </div>
        </div>
    </div>
    </div>


    <div id="save-quote-modal" class="fixed inset-0 z-[300] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('save-quote-modal').classList.add('hidden')"></div>
        
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-[420px] overflow-hidden animate-fadeIn">
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-[#93c21c]"></i> Dokument Speichern
                </h3>
                <button onclick="document.getElementById('save-quote-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Speichern als...</label>
                    <div class="flex gap-3">
                        <label class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                            <input type="radio" name="save_mode" value="offer" checked class="accent-[#93c21c]" onchange="document.getElementById('template-name-wrap').classList.add('hidden')">
                            <span class="text-sm font-bold text-slate-700">Kunden-Angebot</span>
                        </label>
                        <label class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                            <input type="radio" name="save_mode" value="template" class="accent-[#93c21c]" onchange="document.getElementById('template-name-wrap').classList.remove('hidden')">
                            <span class="text-sm font-bold text-slate-700">Als Vorlage</span>
                        </label>
                    </div>
                </div>

                <div id="template-name-wrap" class="hidden">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Name der Vorlage</label>
                    <input type="text" id="save-template-name" placeholder="z.B. PV-Standard Paket 10kWp" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:border-[#93c21c] outline-none">
                </div>
                
                <div id="save-loading-indicator" class="hidden text-sm text-slate-500 flex items-center gap-2 justify-center py-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-[#93c21c]"></i> Speichervorgang läuft...
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button onclick="document.getElementById('save-quote-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 transition-colors text-sm">Abbrechen</button>
                <button id="btn-perform-save" onclick="App.performSave()" class="px-6 py-2.5 rounded-xl bg-[#93c21c] text-white font-black shadow-md hover:brightness-105 transition-all text-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Jetzt Speichern
                </button>
            </div>
        </div>
    </div>


    <div id="offer-lock-modal" class="fixed inset-0 z-[1200] hidden">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

        <div class="absolute inset-0 flex items-center justify-center p-6">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-red-50">
                    <div class="text-[11px] font-black uppercase tracking-[0.18em] text-red-500">Zugriff gesperrt</div>
                    <div class="text-xl font-black text-slate-800 mt-1">Dieses Angebot wird bereits bearbeitet</div>
                </div>

                <div class="p-6">
                    <div id="offer-lock-user-box" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 bg-slate-50">
                        <img id="offer-lock-user-avatar" src="{{ asset('images/gender/male.png') }}" class="w-14 h-14 rounded-full object-cover border border-slate-200" alt="avatar">
                        <div>
                            <div class="text-sm text-slate-500">Aktiver Benutzer</div>
                            <div id="offer-lock-user-name" class="text-lg font-black text-slate-800">-</div>
                        </div>
                    </div>

                    <div class="mt-5 text-sm text-slate-600 leading-7">
                        Sie können momentan nicht arbeiten, weil
                        <span id="offer-lock-inline-name" class="font-black text-slate-800">-</span>
                        dieses Angebot bereits geöffnet hat.
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="/admin/offers" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700">
                            Zurück zur Übersicht
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script>
    // --- CONFIGURATION ---
    const API_BASE = '/offers'; 

    const State = {
            customer: null,
            object: null,
            projectDate: '',
            docType: 'Angebot',
            sections: [],
            coverTextHtml: '',
            offerId: 'NEW',
            custId: '-',
            placedImages: [],
            toolsImages: [],
            taxRate: 19,
            coverTextHtml: '',
            companyName: 'SOLAR ASPEKT',
            brandColor: '#93c21c',
            brandMode: 'text',          // 'text' | 'image'
            brandLogoUrl: '',           // selected logo url
            editingBadge: null,
            editingImage: null,
            dragState: null,
            showThumbnails: false,

            prefill: {
                offer_id: null,
                offer_folder_id: null,
                customer_id: null,
                alternative_id: null,
                product_id: null,
                autoApplied: false
            },

            laborOptions: [],
            laborOptionsLoaded: false,
            laborOptionsLoading: false,
            

            loadedSavedDetail: false,
            biographyItems: [],
            onlineUsers: [],
            presenceInterval: null,
            presenceLoadInterval: null,

            // ✅ NEW (pages + confirm toaster)
            currentPageNo: 1,          // active thumbnail/page (1=cover, 2..=position pages)
            toastConfirm: {            // optional, but clean if you want to store config
                isOpen: false,
                title: '',
                message: '',
                okText: 'OK',
                cancelText: 'Abbrechen',
                onOk: null,
            },

            config: {
                vatMode: 19, // 0 or 19
                overhead: 15.0,
                commission: 1.0,
                minProfit: 10.0,
                margins: {
                    material: 20.0,
                    labor: 50.0,
                    external: 15.0
                },
                supplierDiscount: 2.0,
                logistics: {
                    freight: { active: false, val: 150 },
                    vehicle: { active: false, val: 120 },
                    machine: { active: false, val: 250 }
                },
                risk: 1.5,
                finance: 0.5,
                tax: 30.0,
                custDiscount: 2.0
            }
    };

    State.libraryMode = 'group_sets'; // ✅ default active tab


    // --- PRICE HELPERS (EK/VK auto pick) ---
    
    // ============================================================
        // Helpers
        // ============================================================
        const safeNum = (v, d = 0) => {
            const n = Number(v);
            return Number.isFinite(n) ? n : d;
        };

        const safeStr = (v, d = "") => (v == null ? d : String(v));

        const ensureSection = (idx) => {
            if (!State.sections[idx]) {
            State.sections[idx] = {
                title: "Sektion", description: "",
                config: { mode: "standard", pauschalPrice: 0, type: "standard", hidePrices: false, margin: { value: 0, type: "fixed" } },
                items: [],
            };
            }
            if (!Array.isArray(State.sections[idx].items)) State.sections[idx].items = [];
        };

        const pushItem = (idx, item) => {
            ensureSection(idx);
            State.sections[idx].items.push(item);
        };

        const fetchJson = async (url) => {
            const res = await fetch(url.toString(), { method: "GET", headers: { Accept: "application/json" }, credentials: "same-origin" });
            if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}`);
            return res.json();
        };

        // ✅ Updated to support description_variants
        const pickDescHtml = (x) => {
            if (x?.description_variants?.angebot?.[0]?.html) return x.description_variants.angebot[0].html;
            if (x?.description_default?.html) return x.description_default.html;
            return safeStr(x?.description_html ?? x?.html ?? x?.description ?? "");
        };

        const pickDescText = (x) => {
            if (x?.description_variants?.angebot?.[0]?.text) return x.description_variants.angebot[0].text;
            if (x?.description_default?.text) return x.description_default.text;
            return safeStr(x?.description_text ?? x?.text ?? (typeof x?.description === "string" ? x.description : "") ?? "");
        };

        const buildBaseItem = (overrides = {}) => ({
            item_type: "product",
            productId: null,
            component_id: null,

            name: "",
            desc_html: "",
            desc: "",

            img: "",
            showImage: true,

            // prices
            price: 0,                // VK for pricing base
            ek: 0,
            purchase_price: 0,
            rate: 0,
            margin: 0,
            marginPercent: 0,

            // commercial
            skonto: 0,
            payment_terms: 14,
            availability: true,

            // classification
            componentType: "haupt",
            kind: "article",
            lineType: "standard",
            status: "normal",
            is_stammartikel: false,
            is_favorite: false,

            // quantity / measure
            qty: 1,
            unit: "Stk",
            measure: "Stk",

            // NEW: pricing basis
            price_unit_value: 1,     // e.g. 100
            price_unit_label: "Stk", // e.g. m
            price_unit_text: "1 Stk",

            vpe: 1,

            active: true,
            hidePrices: false,
            hideImage: false,
            hideNumbering: false,
            isPauschal: false,

            creator_id: null,
            creator_name: null,
            count_copy: 0,
            count_offer: 0,
            is_locked: 1,

            subItems: [],
            ...overrides,
        });
        const ensureLaborSectionIndex = () => {
            let lIdx = State.sections.findIndex((s) => !!s?.isLaborSection);
            if (lIdx === -1) lIdx = App.addSection("Montage & Dienstleistung", true);
            ensureSection(lIdx);
            return lIdx;
        };

        const resolveType = (t) => {
            const tt = safeStr(t, "").trim();
            if (tt === "master_set" || tt === "master_set_group" || tt === "product") return tt;
            return "product";
        };

        const calcLineTotal = (it) => safeNum(it.price) * safeNum(it.qty, 1);


    const PAGE_MAX_HEIGHT_PX = 850; 

    window.App = {
        init: () => {
            document.getElementById('wiz-date').valueAsDate = new Date();
            App.updateBranding();
            setTimeout(() => App.switchLibraryMode('group_sets'), 0);
            if (
                    window.jQuery &&
                    $.fn.select2 &&
                    App.Wizard &&
                    typeof App.Wizard.initObjectSelect2 === 'function' &&
                    typeof App.Wizard.setObjectDisabled === 'function'
                ) {
                    App.Wizard.initObjectSelect2();
                    App.Wizard.setObjectDisabled(true);
                    $('#wiz-object-select').val(null).trigger('change');
                }

            const coverEl = document.getElementById('doc-cover-text');
            if (coverEl && !State.coverTextHtml) {
                State.coverTextHtml = coverEl.innerHTML.trim();
            }
            
            // Event Listeners for closing dropdowns
            document.addEventListener('click', e => { 
                if(!e.target.closest('#wiz-customer-search') && !e.target.closest('#wiz-customer-dropdown')) {
                    document.getElementById('wiz-customer-dropdown').classList.add('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                const wrap = document.getElementById('editor-actions-menu-wrap');
                const menu = document.getElementById('editor-actions-menu');

                if (!wrap || !menu) return;
                if (!wrap.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });

            document.addEventListener('mousedown', (e) => {
                if (e.target.closest('input, textarea, select, [contenteditable="true"], .ql-editor')) {
                    const sel = window.getSelection?.();
                    if (sel && typeof sel.removeAllRanges === 'function') {
                        // leave text editing behavior clean
                    }
                }
            });

            App.openSaveModal = () => {
                // Reset modal state
                document.querySelector('input[name="save_mode"][value="offer"]').checked = true;
                document.getElementById('template-name-wrap').classList.add('hidden');
                document.getElementById('save-template-name').value = '';
                
                // Show modal
                document.getElementById('save-quote-modal').classList.remove('hidden');
            };

            App.performSave = async () => {
                const saveMode = document.querySelector('input[name="save_mode"]:checked').value;
                const isTemplate = (saveMode === 'template');
                const templateName = document.getElementById('save-template-name').value;

                // 1. Validation
                if (isTemplate && templateName.trim() === '') {
                    alert('Bitte geben Sie einen Namen für die Vorlage ein.');
                    return;
                }

                if (!isTemplate && (!State.customer || !State.object || !State.object.items.length)) {
                    alert('Fehler: Für ein Angebot muss ein Kunde und ein Objekt im Start-Tab ausgewählt sein.');
                    return;
                }

                // 2. Lock UI & Show Loading
                const saveBtn = document.getElementById('btn-perform-save');
                const loader = document.getElementById('save-loading-indicator');
                
                loader.classList.remove('hidden');
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');

                // 3. Prepare Data
                const totals = App.computeQuoteTotals();
                const payload = {
                    is_template: isTemplate,
                    template_name: templateName,

                    offer_id: State.prefill?.offer_id || null,
                    offer_folder_id: State.prefill?.offer_folder_id || null,

                    customer_id: State.customer ? State.customer.id : null,
                    product_id: State.object?.items?.[0]?.product_id || null,
                    alternative_id: State.object?.items?.[0]?.alternative_id || null,
                    service: State.docType,

                    branding: {
                        color: State.brandColor,
                        mode: State.brandMode,
                        logo: State.brandLogoUrl,
                        company: State.companyName
                    },
                    cover_text: State.coverTextHtml,
                    sections: State.sections,
                    canvas_images: State.placedImages,
                    total_net: totals.salesNet,
                    tax_rate: State.taxRate,
                    total_gross: totals.grossTotal
                };

                try {
                    // 4. API Request
                    const response = await fetch('/offers/save-document', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Server-Fehler beim Speichern');
                    }

                    // 5. Handle Success
                   if (isTemplate) {
                        App.toastConfirmShow({
                            title: 'Vorlage gespeichert',
                            message: 'Die Vorlage wurde erfolgreich erstellt.',
                            okText: 'OK',
                            cancelText: '',
                            onOk: () => {
                                document.getElementById('save-quote-modal').classList.add('hidden');
                            }
                        });

                        const cancelBtn = document.getElementById('toast-confirm-cancel');
                        if (cancelBtn) cancelBtn.style.display = 'none';
                    } else {
                        if (result.folder_id) {
                            App.toastConfirmShow({
                                title: 'Dokument gespeichert',
                                message: `Dieses Angebot wurde in Ordner #${result.folder_id} gespeichert.`,
                                okText: 'Zum Ordner',
                                cancelText: 'Bleiben',
                                onOk: () => {
                                    window.location.href = `/admin/offers/folders/${result.folder_id}?new_offer=1`;
                                }
                            });

                            const cancelBtn = document.getElementById('toast-confirm-cancel');
                            if (cancelBtn) cancelBtn.style.display = '';
                        } else {
                            alert('Angebot gespeichert, aber keine Ordner-ID erhalten.');
                        }
                    }

                } catch (error) {
                    console.error('Save error:', error);
                    alert('Fehler beim Speichern: ' + error.message);
                    
                    // Re-enable UI on error so user can try again
                    loader.classList.add('hidden');
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            };

            document.addEventListener('dragend', () => App.clearDragMode());

            // File Input Listeners
            document.getElementById('img-upload-input').onchange = e => { 
                const f = e.target.files[0]; 
                if (f && App.editingImage) { 
                    const r = new FileReader(); 
                    r.onload = ev => { 
                        const {sIdx, iIdx, subIdx} = App.editingImage;
                        if (subIdx !== null && subIdx !== undefined) {
                            State.sections[sIdx].items[iIdx].subItems[subIdx].img = ev.target.result;
                        } else {
                            State.sections[sIdx].items[iIdx].img = ev.target.result;
                        }
                        App.renderQuotePage(); 
                    }; 
                    r.readAsDataURL(f); 
                } 
            }; 
            document.getElementById('badge-upload-input').onchange = e => { const f=e.target.files[0]; if(f && State.editingBadge) { const r=new FileReader(); r.onload=ev=>{ State.editingBadge.tempImg=ev.target.result; }; r.readAsDataURL(f); } };
            document.getElementById('tool-upload-input').onchange = e => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=ev=>{ State.toolsImages.push(ev.target.result); App.renderSidebarTools(); }; r.readAsDataURL(f); } };
            App.loadLaborOptions().catch(() => {});
            App.toggleThumbnails(false);
            App.applyWizardPrefillFromUrl()
            .then(() => App.loadSavedDocumentIfAvailable())
            .catch(console.error);

            
        },

        toggleThumbnails: (force = null) => {
            const nav = document.getElementById('nav-pane');
            const checkbox = document.getElementById('toggle-thumbnails');
            if (!nav) return;

            const nextState = (force === null) ? !State.showThumbnails : !!force;
            State.showThumbnails = nextState;

            nav.classList.toggle('hidden', !nextState);

            if (checkbox) {
                checkbox.checked = nextState;
            }
        },

        toggleEditorActionsMenu: () => {
                document.getElementById('editor-actions-menu')?.classList.toggle('hidden');
            },

            closeEditorActionsMenu: () => {
                document.getElementById('editor-actions-menu')?.classList.add('hidden');
            },

        makeStaticNodeFromField: (el) => {
            const tag = (el.tagName || '').toLowerCase();
            const staticEl = document.createElement('div');
            staticEl.className = 'thumb-static-field';

            if (tag === 'input') {
                const type = (el.type || '').toLowerCase();

                if (type === 'checkbox') {
                    staticEl.innerHTML = el.checked ? '☑' : '☐';
                } else if (type === 'radio') {
                    staticEl.innerHTML = el.checked ? '◉' : '○';
                } else {
                    staticEl.textContent = el.value || el.getAttribute('value') || '';
                }
            }
            else if (tag === 'textarea') {
                staticEl.innerHTML = (el.value || '').replace(/\n/g, '<br>');
            }
            else if (tag === 'select') {
                const txt = el.options?.[el.selectedIndex]?.text || '';
                staticEl.textContent = txt;
            }
            else {
                staticEl.textContent = el.textContent || '';
            }

            // try to preserve basic sizing/alignment
            const cs = window.getComputedStyle(el);
            staticEl.style.minHeight = cs.height;
            staticEl.style.width = '100%';
            staticEl.style.font = cs.font;
            staticEl.style.fontWeight = cs.fontWeight;
            staticEl.style.lineHeight = cs.lineHeight;
            staticEl.style.letterSpacing = cs.letterSpacing;
            staticEl.style.color = cs.color;
            staticEl.style.textAlign = cs.textAlign;
            staticEl.style.whiteSpace = 'pre-wrap';
            staticEl.style.wordBreak = 'break-word';
            staticEl.style.background = 'transparent';
            staticEl.style.border = '0';
            staticEl.style.padding = '0';
            staticEl.style.margin = '0';

            return staticEl;
        },

        makeThumbnailStatic: (root) => {
            if (!root) return root;

            // remove ids to avoid duplicates
            root.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));

            // remove no-print/editor-only controls
            root.querySelectorAll(
                'button, .no-print, .delete-float, .section-drop-zone, .list-action-btn, .drag-handle, .ql-toolbar'
            ).forEach(el => el.remove());

            // convert form controls into plain rendered text
            root.querySelectorAll('input, textarea, select').forEach(el => {
                const replacement = App.makeStaticNodeFromField(el);
                el.replaceWith(replacement);
            });

            // convert contenteditable into plain block
            root.querySelectorAll('[contenteditable="true"]').forEach(el => {
                el.removeAttribute('contenteditable');
                el.classList.remove('editable-field');
                el.style.outline = 'none';
                el.style.border = '0';
                el.style.background = 'transparent';
                el.style.cursor = 'default';
            });

            // disable image hover camera overlay
            root.querySelectorAll('.prod-img-container').forEach(el => {
                el.style.cursor = 'default';
            });

            // remove drag/drop attributes
            root.querySelectorAll('*').forEach(el => {
                el.removeAttribute('draggable');
                el.removeAttribute('ondragstart');
                el.removeAttribute('ondragover');
                el.removeAttribute('ondragleave');
                el.removeAttribute('ondrop');
                el.removeAttribute('onclick');
                el.removeAttribute('onchange');
                el.removeAttribute('oninput');
                el.removeAttribute('onfocus');
            });

            // preserve live values for static text blocks that may still depend on DOM text
            root.querySelectorAll('.clean-input').forEach(el => {
                el.style.border = '0';
                el.style.background = 'transparent';
                el.style.pointerEvents = 'none';
            });

            return root;
        },

        startDragMode: () => {
            document.body.classList.add('drag-active');
        },

        clearDragMode: () => {
            document.body.classList.remove('drag-active');
            App.dragState = null;

            document.querySelectorAll('.drag-over-sub, .drag-over-sort, .section-drop-zone.drag-over')
                .forEach(el => el.classList.remove('drag-over-sub', 'drag-over-sort', 'drag-over'));
        },

        isLibraryDrag: () => {
            return App.dragState && App.dragState.type === 'library';
        },
 
        toggleSectionLock: (sIdx) => {
            State.sections[sIdx].isLocked = !State.sections[sIdx].isLocked;
            App.renderQuotePage();
        },

        addNotePosition: (sIdx) => {
            State.sections[sIdx].items.push({
                name: 'Wichtiger Hinweis', desc: 'Bitte beachten Sie folgende Information...',
                price: 0, ek: 0, marginPercent: 0, qty: 0, unit: '',
                kind: 'note', status: 'normal', subItems: [], active: true
            });
            App.renderQuotePage();
        },

        updatePosStatus: (sIdx, iIdx, subIdx, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            target.status = val;
            App.renderQuotePage();
        },

        updatePosConfig: (sIdx, iIdx, subIdx, key, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            
            if (key === 'hidePrices') target.hidePrices = val;
            
            if (key === 'kind') {
                target.kind = val;
                // AUTO-APPLY DEFAULT MARGINS WHEN SWITCHING TYPE
                target.marginPercent = App.getDefaultMargin(val);
                target.unit = val === 'labor' ? 'Std' : 'Stk';
                
                // Recalculate price based on new margin
                if (target.ek > 0) {
                    target.price = App.vkFromEkMargin(target.ek, target.marginPercent);
                } else if (val === 'note') {
                    target.ek = 0; target.price = 0; target.qty = 0; target.unit = '';
                }
            }
            
            if (key === 'isPauschal') {
                target.isPauschal = val;
                if (val) {
                    target.unit = 'Pauschal';
                    target.qty = 1;
                } else {
                    target.unit = target.kind === 'labor' ? 'Std' : 'Stk'; 
                }
            }
            App.renderQuotePage();
        },

        updatePosPriceCalc: (sIdx, iIdx, subIdx, field, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            
            val = parseFloat(val) || 0;
            let ek = parseFloat(target.ek) || 0;
            let vk = parseFloat(target.price) || 0;
            if (ek === 0 && vk > 0) ek = vk; 

            if (field === 'ek') {
                target.ek = val;
                let mPct = parseFloat(target.marginPercent) || 0;
                target.price = val * (1 + mPct/100);
            } 
            else if (field === 'marginPercent') {
                target.marginPercent = val;
                target.ek = ek; 
                target.price = ek * (1 + val/100);
                
                // --- WARNING: BELOW MINIMUM MARGIN ---
                if (val < (State.config.minProfit || 10)) {
                    App.toastConfirmShow({
                        title: 'Achtung: Marge zu niedrig!',
                        message: `Die eingegebene Marge von ${val.toFixed(1)}% liegt unter Ihrem definierten Mindestgewinn von ${State.config.minProfit}%.`,
                        okText: 'Verstanden',
                        cancelText: ''
                    });
                    document.getElementById('toast-confirm-cancel').style.display = 'none';
                }
            } 
            else if (field === 'price') {
                target.price = val;
                if (ek > 0) {
                    target.marginPercent = ((val - ek) / ek) * 100;
                    
                    // --- WARNING: BELOW MINIMUM MARGIN ---
                    if (target.marginPercent < (State.config.minProfit || 10)) {
                        App.toastConfirmShow({
                            title: 'Achtung: Marge zu niedrig!',
                            message: `Dieser Preis führt zu einer Marge von ${target.marginPercent.toFixed(1)}%, was unter dem Limit von ${State.config.minProfit}% liegt.`,
                            okText: 'Verstanden',
                            cancelText: ''
                        });
                        document.getElementById('toast-confirm-cancel').style.display = 'none';
                    }
                } else {
                    target.marginPercent = 100; 
                }
            }
            App.renderQuotePage();
        },
 
        switchLibraryMode: (mode) => {
            const allowed = ['group_sets', 'sets', 'products'];
            State.libraryMode = allowed.includes(mode) ? mode : 'group_sets';

            const btnG = document.getElementById('lib-subtab-group');
            const btnS = document.getElementById('lib-subtab-sets');
            const btnP = document.getElementById('lib-subtab-products');

            const setBtn = (btn, active) => {
                if (!btn) return;
                btn.classList.toggle('lib-subtab-active', active);
                btn.classList.toggle('lib-subtab-inactive', !active);
            };

            setBtn(btnG, State.libraryMode === 'group_sets');
            setBtn(btnS, State.libraryMode === 'sets');
            setBtn(btnP, State.libraryMode === 'products');

            App.renderSidebar();
        },

        // -------------------------
        // THUMB SORTING (REORDER PAGES)
        // -------------------------
        _thumbSortable: null,

        initThumbSortable: () => {
        const nav = document.getElementById('nav-pane');
        if (!nav || typeof Sortable === 'undefined') return;

        // destroy old instance
        if (App._thumbSortable) {
            try { App._thumbSortable.destroy(); } catch(e) {}
            App._thumbSortable = null;
        }

        App._thumbSortable = new Sortable(nav, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            draggable: '.thumb-wrapper',
            filter: (evt) => {
            // prevent dragging cover page (Seite 1)
            const el = evt.target.closest('.thumb-wrapper');
            return el && el.dataset.page === "1";
                },
                onMove: (evt) => {
                // don't allow dropping before cover (keep cover always first)
                const related = evt.related;
                if (related && related.dataset.page === "1") return false;
                return true;
                },
                onEnd: () => {
                App.applyThumbOrderToPages();
                }
            });
        },

        // ------------------------------------------------------------
        // ✅ Image helpers (fix: cards + modal + drag items images)
        // ------------------------------------------------------------
        placeholderImg: (label = 'IMG') => {
        const txt = encodeURIComponent((label || 'IMG').slice(0, 14));
        return `https://placehold.co/80x80?text=${txt}`;
        },

        pickImage: (obj, fallback = null) => {
        const tryKeys = [
            'image', 'image_url', 'img', 'img_url',
            'photo', 'photo_url', 'thumbnail', 'thumb',
            'logo', 'logo_url'
        ];

        const norm = (v) => (typeof v === 'string' ? v.trim() : '');
        for (const k of tryKeys) {
            const v = norm(obj?.[k]);
            if (v) return v;
        }

        const fb = norm(fallback);
        return fb || '';
        },


        /**
         * Apply thumbnail order:
         * - Cover page (1) stays first and is ignored for sorting.
         * - Reorder DOM pages in #position-pages-container to match thumbs.
         * - Rebuild State.sections with updated _pageBreak markers so order persists.
         */
        applyThumbOrderToPages: () => {
        const nav = document.getElementById('nav-pane');
        const cont = document.getElementById('position-pages-container');
        if (!nav || !cont) return;

        // thumbs -> desired order (position pages only)
        const desired = Array.from(nav.querySelectorAll('.thumb-wrapper'))
            .map(w => Number(w.dataset.page || 0))
            .filter(n => n >= 2); // exclude cover

        // current pages in DOM
        const pages = Array.from(cont.querySelectorAll('.a4-page.dynamic-page'));
        // map current pageNo -> element (pageNo is based on current DOM order: 2..)
        const byPageNo = new Map();
        pages.forEach((el, idx) => byPageNo.set(idx + 2, el));

        // 1) reorder DOM pages to match desired order
        const fragment = document.createDocumentFragment();
        desired.forEach(pageNo => {
            const el = byPageNo.get(pageNo);
            if (el) fragment.appendChild(el);
        });
        // append any leftover pages (safety)
        pages.forEach(el => { if (!fragment.contains(el)) fragment.appendChild(el); });

        cont.appendChild(fragment);

        // 2) persist order in State.sections by moving "segments" (split by _pageBreak)
        // current segments in State.sections correspond to current logical pages (2..)
        const segs = App._buildPageSegments(); // already exists in your code

        // build mapping oldPageNo -> segment
        // pageNo 2 => segs[0], pageNo 3 => segs[1], ...
        const segByPageNo = new Map();
        segs.forEach((seg, i) => segByPageNo.set(i + 2, seg));

        // create reordered segments list
        const newSegs = desired
            .map(pn => segByPageNo.get(pn))
            .filter(Boolean);

        // rebuild sections array with breaks between segments (no leading break)
        const rebuilt = [];
        newSegs.forEach((seg, idx) => {
            if (idx > 0) {
            rebuilt.push({
                _pageBreak: true,
                id: 'pb' + Date.now() + '_' + idx,
                title: '',
                description: '',
                config: { mode:'standard', pauschalPrice:0, type:'standard', hidePrices:false, margin:{ value:0, type:'fixed' } },
                items: []
            });
            }
            for (let i = seg.start; i < seg.end; i++) {
            // skip existing breaks in the segment range (shouldn't exist, but safe)
            if (State.sections[i]?._pageBreak) continue;
            rebuilt.push(State.sections[i]);
            }
        });

        State.sections = rebuilt;

        // 3) re-render to rebuild thumbnails + refresh dataset.page numbering
        // keep currently active page best-effort (fallback: 1)
        const activeThumb = nav.querySelector('.thumb-wrapper.is-active');
        const activeNo = activeThumb ? Number(activeThumb.dataset.page || 1) : 1;

        App.renderQuotePage(false);

        // restore active (clamped)
        setTimeout(() => {
            const max = Number(document.getElementById('lbl-total-pages')?.innerText || 1);
            App.setActiveThumb(Math.max(1, Math.min(activeNo, max)));
        }, 0);
        },
        
        toastConfirmShow: ({ title, message, okText='Löschen', cancelText='Abbrechen', onOk }) => {
            const root = document.getElementById('toast-confirm');
            root.classList.remove('hidden');

            document.getElementById('toast-confirm-title').innerText = title || 'Bestätigen';
            document.getElementById('toast-confirm-msg').innerHTML = (message || '').toString();

            const btnOk = document.getElementById('toast-confirm-ok');
            const btnCancel = document.getElementById('toast-confirm-cancel');

            // clear old handlers (safe)
            btnOk.onclick = null;
            btnCancel.onclick = null;

            btnCancel.innerText = cancelText;
            btnOk.innerText = okText;

            btnCancel.onclick = () => App.toastConfirmHide();
            btnOk.onclick = () => {
                App.toastConfirmHide();
                try { onOk && onOk(); } catch(e){ console.error(e); }
            };
            },

            toastConfirmHide: () => {
            document.getElementById('toast-confirm')?.classList.add('hidden');
            },

            /** track current visible page number (1 = cover, 2.. = position pages) */
            _currentPageNo: 1,
 
            setActiveThumb: (pageNo) => {
                const p = Number(pageNo || 1);

                // store current page (needed for delete button)
                App._currentPageNo = p;
                State.currentPageNo = p; // optional, if you want it in State

                const nav = document.getElementById('nav-pane');
                if (!nav) return;

                nav.querySelectorAll('.thumb-wrapper').forEach(w => w.classList.remove('is-active'));
                const target = nav.querySelector(`.thumb-wrapper[data-page="${p}"]`);
                if (target) target.classList.add('is-active');
            },


            /** ✅ add a manual page break AFTER current position page */
            addPageAfterCurrent: () => {
            // page 1 = cover, position pages start at 2
            const posPage = Math.max(2, App._currentPageNo);
            const segIndex = posPage - 2; // 0-based position page segment

            const segs = App._buildPageSegments(); // [{start,end,breakIdxBefore}]
            const insertAt = (segs[segIndex]?.end ?? State.sections.length);

            // Insert a break marker + create a new empty section on the new page
            const breakMarker = {
                _pageBreak: true,
                id: 'pb' + Date.now(),
                title: '',
                description: '',
                config: { mode:'standard', pauschalPrice:0, type:'standard', hidePrices:false, margin:{ value:0, type:'fixed' } },
                items: []
            };

            const newSection = {
                id: 's' + (Date.now()+1),
                title: `${State.sections.length+1}. Neue Sektion`,
                description: 'Beschreibung',
                config: { mode:'standard', pauschalPrice:0, type:'standard', hidePrices:false, margin:{ value:0, type:'fixed' } },
                items: []
            };

            State.sections.splice(insertAt, 0, breakMarker, newSection);
            App.renderQuotePage();

            // jump to next page thumb (best effort)
            setTimeout(() => {
                const next = App._currentPageNo + 1;
                const el = document.querySelector(`.thumb-wrapper[data-page="${next}"]`);
                if (el) el.click();
            }, 0);
            },

            /** ✅ ask delete page with toaster confirm */
            askDeleteCurrentPage: () => {
            const pageNo = App._currentPageNo;

            console.log('delete pageNo=', App._currentPageNo);

            // block deleting cover page 1
            if (pageNo <= 1) {
                App.toastConfirmShow({
                title: 'Nicht möglich',
                message: 'Die erste Seite (Anschreiben) kann nicht gelöscht werden.',
                okText: 'OK',
                cancelText: '',
                onOk: () => {}
                });
                // hide cancel button quickly
                document.getElementById('toast-confirm-cancel').style.display = 'none';
                document.getElementById('toast-confirm-ok').style.background = '#0f172a';
                document.getElementById('toast-confirm-ok').style.color = '#fff';
                document.getElementById('toast-confirm-ok').onmouseout = null;
                return;
            } else {
                // restore cancel display if previously hidden
                const c = document.getElementById('toast-confirm-cancel');
                if (c) c.style.display = '';
                const ok = document.getElementById('toast-confirm-ok');
                if (ok) { ok.style.background=''; ok.style.color=''; }
            }

            App.toastConfirmShow({
                title: `Seite ${pageNo} löschen?`,
                message: `Wenn Sie diese Seite löschen, werden <b>alle Positionen auf dieser Seite</b> ebenfalls gelöscht.<br>Fortfahren?`,
                okText: 'Ja, Seite löschen',
                cancelText: 'Abbrechen',
                onOk: () => App.deleteCurrentPage()
            });
            },

            /** delete current position page segment (and its positions) */
            deleteCurrentPage: () => {
            const pageNo = App._currentPageNo;
            if (pageNo <= 1) return;

            const segIndex = (pageNo - 2);
            const segs = App._buildPageSegments();
            const seg = segs[segIndex];
            if (!seg) return;

            // remove segment content
            State.sections.splice(seg.start, seg.end - seg.start);

            // also remove the break marker BEFORE this segment (if exists and not first segment)
            // our builder stores break index right before segment start (for non-first segments)
            if (seg.breakIdxBefore != null) {
                // after splice above, indices might shift:
                // recompute by searching the nearest pageBreak before seg.start
                const idx = App._findBreakBeforeIndex(seg.start);
                if (idx != null) State.sections.splice(idx, 1);
            }

            App.renderQuotePage();

            // activate previous page
            setTimeout(() => {
                const prev = Math.max(1, pageNo - 1);
                const el = document.querySelector(`.thumb-wrapper[data-page="${prev}"]`);
                if (el) el.click();
            }, 0);
            },

            /** build segments split by pageBreak markers (position pages only; cover is separate) */
            _buildPageSegments: () => {
            const segs = [];
            let start = 0;
            let breakIdxBefore = null;

            for (let i = 0; i < State.sections.length; i++) {
                if (State.sections[i]?._pageBreak) {
                // segment ends before this break
                segs.push({ start, end: i, breakIdxBefore });
                start = i + 1;
                breakIdxBefore = i; // marker located at i
                }
            }
            // last
            segs.push({ start, end: State.sections.length, breakIdxBefore });

            // normalize: remove empty leading/trailing segments if they are truly empty
            // (keep them if user intentionally added blank page with empty section)
            return segs.filter(s => s.start <= s.end);
            },

            _findBreakBeforeIndex: (fromIndex) => {
            for (let i = Math.min(fromIndex - 1, State.sections.length - 1); i >= 0; i--) {
                if (State.sections[i]?._pageBreak) return i;
            }
            return null;
            }, 
            // Detect which page is currently in view and highlight thumbnail badge
            initThumbObserver: () => {
                if (App._thumbObserver) {
                try { App._thumbObserver.disconnect(); } catch(e) {}
                }

                const root = document.getElementById('panel-a4');
                if (!root) return;

                const pages = [
                document.getElementById('page-1'),
                ...Array.from(document.querySelectorAll('#position-pages-container .a4-page'))
                ].filter(Boolean);

                // Map element -> page number
                const pageNoByEl = new Map();
                pages.forEach((el, idx) => pageNoByEl.set(el, idx + 1));

                // IntersectionObserver: pick the most visible page and activate its thumb
                App._thumbObserver = new IntersectionObserver((entries) => {
                const visibles = entries
                    .filter(e => e.isIntersecting)
                    .map(e => ({ el: e.target, ratio: e.intersectionRatio }))
                    .sort((a,b) => b.ratio - a.ratio);

                if (visibles.length) {
                    const pageNo = pageNoByEl.get(visibles[0].el);
                    if (pageNo) App.setActiveThumb(pageNo);
                }
                }, {
                root,
                threshold: [0.15, 0.25, 0.35, 0.5, 0.65, 0.8]
                });

                pages.forEach(el => App._thumbObserver.observe(el));

                // default
                App.setActiveThumb(State.currentPageNo || 1);
            },
            escapeHtml: (s) => (s ?? '').toString()
            .replaceAll('&','&amp;').replaceAll('<','&lt;')
            .replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;"),

            /**
             * ✅ Fix for "hidePrices is not defined"
             * Use this everywhere instead of a bare "hidePrices" variable.
             */
            shouldHidePrices: (sec, item) => !!(sec?.config?.hidePrices || item?.hidePrices),

            /**
             * ✅ Quill description editor
             */
            _descQuill: null,
            _descEditing: null, // { sIdx, iIdx, subIdx }
            _richEditorMode: null, // 'position' | 'cover'  

            openDescModal: (sIdx, iIdx, subIdx = null) => {
                const sec = State.sections?.[sIdx];
                const item = sec?.items?.[iIdx];
                if (!sec || !item) return;

                const normalizedSubIdx =
                    (subIdx === null || subIdx === undefined || subIdx === 'null')
                        ? null
                        : Number(subIdx);

                const target = normalizedSubIdx === null
                    ? item
                    : (item.subItems?.[normalizedSubIdx] || null);

                if (!target) return;

                const title = normalizedSubIdx === null
                    ? `Pos: ${item.name || 'Position'}`
                    : `Unterpos: ${target.name || 'Unterposition'}`;

                const titleEl = document.getElementById('desc-modal-title');
                if (titleEl) titleEl.innerText = title;

                const quill = App.initDescQuill();

                const html = (target.desc_html || '').toString().trim();
                const plain = (target.desc || '').toString().trim();

                quill.setContents([]);
                if (html) {
                    quill.clipboard.dangerouslyPasteHTML(html);
                } else {
                    quill.setText(plain);
                }

                App._richEditorMode = 'position';
                App._descEditing = {
                    sIdx,
                    iIdx,
                    subIdx: normalizedSubIdx
                };

                document.getElementById('desc-modal')?.classList.remove('hidden');
            },

            openCoverTextModal: () => {
                const titleEl = document.getElementById('desc-modal-title');
                if (titleEl) titleEl.innerText = 'Anschreiben / Kundentext bearbeiten';

                const quill = App.initDescQuill();

                const coverEl = document.getElementById('doc-cover-text');
                let html = (State.coverTextHtml || '').toString().trim();

                if (!html && coverEl) {
                    html = coverEl.innerHTML.trim();
                }

                quill.setContents([]);
                if (html) {
                    quill.clipboard.dangerouslyPasteHTML(html);
                } else {
                    quill.setText('');
                }

                App._richEditorMode = 'cover';
                App._descEditing = null;

                document.getElementById('desc-modal')?.classList.remove('hidden');
            },

           closeDescModal: () => {
                document.getElementById('desc-modal')?.classList.add('hidden');
                App._descEditing = null;
                App._richEditorMode = null;
            },

            saveDescModal: () => {
                if (!App._descQuill) return;

                let html = (App._descQuill.root.innerHTML || '').trim();
                if (html === '<p><br></p>') html = '';

                // COVER LETTER MODE
                if (App._richEditorMode === 'cover') {
                    State.coverTextHtml = html;

                    const coverEl = document.getElementById('doc-cover-text');
                    if (coverEl) {
                        coverEl.innerHTML = html || '<p><br></p>';
                    }

                    App.closeDescModal();
                    App.rebuildThumbnails();
                    return;
                }

                // POSITION MODE
                if (!App._descEditing) return;

                const { sIdx, iIdx, subIdx } = App._descEditing;

                const sec = State.sections?.[sIdx];
                const item = sec?.items?.[iIdx];
                if (!sec || !item) return;

                const target = subIdx === null
                    ? item
                    : (item.subItems?.[subIdx] || null);

                if (!target) return;

                target.desc_html = html;
                target.desc = App._descQuill.getText().trim();

                App.closeDescModal();
                App.renderQuotePage();
            },
            initDescQuill: () => {
                if (App._descQuill) return App._descQuill;

                const Font = Quill.import('formats/font');
                Font.whitelist = [
                    'sans-serif',
                    'serif',
                    'monospace',
                    'arial',
                    'times-new-roman',
                    'courier-new'
                ];
                Quill.register(Font, true);

                const Size = Quill.import('attributors/style/size');
                Size.whitelist = ['12px', '14px', '16px', '18px', '24px', '32px'];
                Quill.register(Size, true);

                App._descQuill = new Quill('#desc-quill', {
                    theme: 'snow',
                    placeholder: 'Text eingeben …',
                    modules: {
                        toolbar: {
                            container: [
                                [{ font: Font.whitelist }],
                                [{ size: Size.whitelist }],
                                [{ header: [1, 2, 3, 4, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ color: [] }, { background: [] }],
                                [{ script: 'sub' }, { script: 'super' }],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ indent: '-1' }, { indent: '+1' }],
                                [{ align: [] }],
                                [{ direction: 'rtl' }],
                                ['blockquote', 'code-block'],
                                ['link', 'image', 'video'],
                                ['clean']
                            ],
                            handlers: {
                                image: function () {
                                    const range = this.quill.getSelection(true);
                                    const url = window.prompt('Bild-URL eingeben');
                                    if (!url) return;

                                    this.quill.insertEmbed(range.index, 'image', url, 'user');
                                    this.quill.setSelection(range.index + 1, 0);
                                }
                            }
                        }
                    }
                });

                return App._descQuill;
            },

            
        // --- WIZARD API LOGIC ---
       
        applyBrandingToCover: () => {
            // cover logo area: switch between text and image
            const logoTextEl = document.getElementById('doc-logo-text');
            const logoBox = logoTextEl?.parentElement;

            if (!logoBox) return;

            // remove old img if exists
            const oldImg = document.getElementById('doc-logo-img');
            if (oldImg) oldImg.remove();

            if (State.brandMode === 'image' && State.brandLogoUrl) {
                // hide text and inject img
                if (logoTextEl) logoTextEl.style.display = 'none';

                const img = document.createElement('img');
                img.id = 'doc-logo-img';
                img.src = State.brandLogoUrl;
                img.alt = 'Logo';
                img.style.height = '42px';
                img.style.maxWidth = '220px';
                img.style.objectFit = 'contain';
                img.style.display = 'block';

                logoBox.prepend(img);
            } else {
                // show text
                if (logoTextEl) {
                logoTextEl.style.display = '';
                logoTextEl.innerText = State.companyName || 'SOLAR ASPEKT';
                }
            }

            // apply brand color to cover elements that used hardcoded green
            document.querySelectorAll(
                '#doc-logo-text, .pdf-logo-text, #footer-company, #doc-team-name'
            ).forEach(el => {
                el.style.color = 'var(--brand-color)';
            });
        },

         /**
         * Lazy-load sets inside a group set accordion on first open.
         * Expects endpoint: GET /offers/master-set-groups/{id}?context=angebot
         * Response example: { id, name, master_sets:[{id,name,description,...}] }
         */
        onGroupSetToggle: async (detailsEl) => {
            try {
                if (!detailsEl || !detailsEl.open) return; // only when opened
                const gsId = detailsEl.dataset.gsId;
                if (!gsId) return;

                // prevent double-load
                if (detailsEl.dataset.loaded === '1') return;

                const box = detailsEl.querySelector(`#gs-sets-${gsId}`);
                if (!box) return;

                box.innerHTML = `<div class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i> Lade Sets…
                </div>`;

                const url = new URL(`${API_BASE}/master-set-groups/${gsId}`, window.location.origin);
                url.searchParams.set('context', 'angebot');

                const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();

                const sets = Array.isArray(data.master_sets) ? data.master_sets : [];

                // Use the same "card" renderer available in renderSidebar scope?
                // Since it's scoped there, we render simply here:
                const esc = App.escapeHtml;
                const stripHtml = (html) => (html ?? '').toString().replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                const preview = (txt, max = 120) => {
                    const t = stripHtml(txt);
                    if (!t) return '';
                    return t.length > max ? (t.slice(0, max - 1) + '…') : t;
                };
                    const safeImg = (src, label='IMG') => {
                        const s = (src || '').toString().trim();
                        return s ? s : App.placeholderImg(label);
                        };


                    const cardHtml = (ms) => {
                    const msImg = App.pickImage(ms, null);
                    const imgSrc = safeImg(msImg, ms.name || 'SET');

                    return `
                        <div draggable="true"
                        ondragstart="App.dragStart(event, '${ms.id}', 'master_set')"
                        class="relative bg-white border border-slate-200 p-2 rounded shadow-sm cursor-grab hover:border-[#93c21c] flex items-start gap-2">
                        <div class="w-8 h-8 rounded bg-slate-100 flex-shrink-0 overflow-hidden mt-0.5 flex items-center justify-center">
                            <img src="${imgSrc}" class="w-full h-full object-cover"
                                 onerror="this.onerror=null;this.src='${App.placeholderImg(ms.name || 'SET')}'">
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-[11px] font-bold text-slate-800 truncate">${esc(ms.name || `Set #${ms.id}`)}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">${esc(preview(ms.description || '', 140))}</div>
                            </div>
                            <button type="button" onclick="App.openSetModal('${ms.id}')"
                                class="text-slate-300 hover:text-[#93c21c]" title="Set anzeigen">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-1">
                            <span class="text-[9px] font-black text-[#93c21c]">SET</span>
                            </div>
                        </div>
                        </div>
                    `;
                    };
 
                if (!sets.length) {
                    box.innerHTML = `<div class="text-xs text-slate-400">Keine Sets in dieser Gruppe.</div>`;
                } else {
                    box.innerHTML = sets
                        .slice()
                        .sort((a, b) => (a.name || '').localeCompare(b.name || '', 'de'))
                        .map(cardHtml)
                        .join('');
                }

                detailsEl.dataset.loaded = '1';
            } catch (e) {
                console.error(e);
                const gsId = detailsEl?.dataset?.gsId;
                const box = detailsEl?.querySelector(`#gs-sets-${gsId}`);
                if (box) box.innerHTML = `<div class="text-xs text-red-500">Fehler beim Laden der Sets.</div>`;
            }
        },
   
        Wizard: {
            filterCustomers: async () => {
                const q = document.getElementById('wiz-customer-search').value;
                const drop = document.getElementById('wiz-customer-dropdown');
                if (q.length < 2) { drop.classList.add('hidden'); return; }

                try {
                    const response = await fetch(`${API_BASE}/wizard/customers?q=${encodeURIComponent(q)}`);
                    if(!response.ok) throw new Error('API Error');
                    const data = await response.json();
                    
                    drop.innerHTML = '';
                    if(data.items && data.items.length > 0) {
                        drop.classList.remove('hidden');
                        data.items.forEach(c => {
                            const div = document.createElement('div');
                            div.className = "dropdown-item p-2 hover:bg-[#f4f9e8] cursor-pointer border-b border-slate-50 last:border-0";
                            div.innerHTML = `<div class="font-bold text-slate-800 text-sm">${c.display_name}</div><div class="text-xs text-slate-500">${c.street || ''}, ${c.city || ''}</div>`;
                            div.onclick = () => App.Wizard.selectCustomer(c);
                            drop.appendChild(div);
                        });
                    } else {
                        drop.classList.add('hidden');
                    }
                } catch (err) { console.error("Customer search failed", err); }
            },

           /* ✅ 6) Update selectCustomer + clearCustomer to refresh Select2 */

           selectCustomer: async (customer, options = {}) => {
                State.customer = customer;
                State.custId = customer.customer_no || 'KD-NEW';

                document.getElementById('wiz-customer-search').parentElement.classList.add('hidden');
                document.getElementById('wiz-sel-cust-name').innerText =
                    customer.display_name || customer.name || '';
                document.getElementById('wiz-sel-cust-addr').innerText =
                    `${customer.street || ''}, ${customer.city || ''}`;
                document.getElementById('wiz-customer-selected').classList.remove('hidden');

                try {
                    const response = await fetch(`${API_BASE}/wizard/customers/${customer.id}/objects`, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    const sel = document.getElementById('wiz-object-select');
                    sel.innerHTML = '';

                    if (data.products && data.products.length > 0) {
                        data.products.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.lead_product_id;
                            opt.text = p.label;
                            opt.dataset.altId = p.alternative_id ?? '';
                            opt.dataset.productId = p.product_id ?? '';
                            sel.appendChild(opt);
                        });
                    }

                    document.getElementById('wiz-step-2').classList.remove('opacity-50', 'pointer-events-none');

                    if (
                        window.jQuery &&
                        $.fn.select2 &&
                        App.Wizard &&
                        typeof App.Wizard.initObjectSelect2 === 'function' &&
                        typeof App.Wizard.setObjectDisabled === 'function'
                    ) {
                        App.Wizard.initObjectSelect2();
                        App.Wizard.setObjectDisabled(false);
                    }

                    if (options.autoSelect) {
                        const allOptions = Array.from(sel.options);

                        const matchedValues = allOptions
                            .filter(o => {
                                const altId = o.dataset.altId ? parseInt(o.dataset.altId, 10) : null;
                                const productId = o.dataset.productId ? parseInt(o.dataset.productId, 10) : null;

                                if (options.alternative_id && options.product_id) {
                                    return altId === Number(options.alternative_id)
                                        && productId === Number(options.product_id);
                                }

                                if (options.alternative_id) {
                                    return altId === Number(options.alternative_id);
                                }

                                if (options.product_id) {
                                    return productId === Number(options.product_id);
                                }

                                return false;
                            })
                            .map(o => o.value);

                        if (matchedValues.length > 0) {
                            $('#wiz-object-select').val(matchedValues).trigger('change');
                        } else {
                            $('#wiz-object-select').val(null).trigger('change');
                        }
                    } else {
                        $('#wiz-object-select').val(null).trigger('change');
                    }

                } catch (err) {
                    console.error("Loading objects failed", err);
                }
            },

            initObjectSelect2: () => {
                const el = $('#wiz-object-select');
                if (!el.length) return;

                if (el.hasClass('select2-hidden-accessible')) {
                    el.select2('destroy');
                }

                el.select2({
                    placeholder: 'Objekt/Produkte auswählen…',
                    width: '100%',
                    closeOnSelect: false,
                    allowClear: true
                });

                el.off('change.select2_sync').on('change.select2_sync', () => {
                    App.Wizard.selectObject();
                });
            },

            setObjectDisabled: (disabled) => {
                const el = $('#wiz-object-select');
                el.prop('disabled', !!disabled);

                if (el.hasClass('select2-hidden-accessible')) {
                    el.trigger('change.select2');
                }
            },

            clearCustomer: () => {
                State.customer = null;
                State.object = null;

                document.getElementById('wiz-customer-selected').classList.add('hidden');
                document.getElementById('wiz-customer-search').parentElement.classList.remove('hidden');
                document.getElementById('wiz-customer-search').value = '';

                document.getElementById('wiz-step-2').classList.add('opacity-50','pointer-events-none');
                document.getElementById('wiz-step-3').classList.add('opacity-50','pointer-events-none');
                document.getElementById('wiz-step-4').classList.add('opacity-50','pointer-events-none');

                document.getElementById('wiz-object-count').innerText = '0';

                const sel = document.getElementById('wiz-object-select');
                sel.innerHTML = '';

                // ✅ reset select2 + disable
                if (window.jQuery && $.fn.select2) {
                    App.Wizard.initObjectSelect2();
                    App.Wizard.setObjectDisabled(true);
                    $('#wiz-object-select').val(null).trigger('change');
                }

                document.getElementById('wiz-btn-start').disabled = true;
                document.getElementById('wiz-btn-start').classList.add('btn-disabled');
            },
 

           selectObject: () => {
                const sel = document.getElementById('wiz-object-select');
                const opts = Array.from(sel.selectedOptions);

                const picked = opts.map(o => ({
                    lead_product_id: parseInt(o.value, 10),
                    alternative_id: o.dataset.altId ? parseInt(o.dataset.altId, 10) : null,
                    product_id: o.dataset.productId ? parseInt(o.dataset.productId, 10) : null,
                    label: o.text
                }));

                State.object = {
                    items: picked,
                    // keep a main label for UI
                    name: picked.length === 1 ? picked[0].label : `${picked.length} Produkte ausgewählt`
                };

                document.getElementById('wiz-object-count').innerText = picked.length;

                if (picked.length > 0) {
                    document.getElementById('wiz-step-3').classList.remove('opacity-50', 'pointer-events-none');
                    document.getElementById('wiz-step-4').classList.remove('opacity-50', 'pointer-events-none');
                    document.getElementById('wiz-btn-start').disabled = false;
                    document.getElementById('wiz-btn-start').classList.remove('btn-disabled');
                } else {
                    document.getElementById('wiz-btn-start').disabled = true;
                    document.getElementById('wiz-btn-start').classList.add('btn-disabled');
                }
                }

        },

        // --- EDITOR LOGIC ---
        renderSidebar: async () => {
            const list = document.getElementById('sidebar-list');
            const q = (document.getElementById('sidebar-search')?.value || '').trim();

            // UX: require 2 chars for search (but allow empty => show all)
            if (q.length > 0 && q.length < 2) return;

            const esc = (s) => (s ?? '').toString()
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const img = (src) => esc(src || 'https://placehold.co/100?text=IMG');

            const stripHtml = (html) => (html ?? '').toString()
                .replace(/<[^>]*>/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            const preview = (txt, max = 120) => {
                const t = stripHtml(txt);
                if (!t) return '';
                return t.length > max ? (t.slice(0, max - 1) + '…') : t;
            };

            // one renderer for a draggable “card”
            const card = ({ type, id, title, subtitle, badge, iconHtml, imageSrc, rightHtml }) => `
                <div draggable="true"
                    ondragstart="App.dragStart(event, '${id}', '${type}')"
                    class="relative bg-white border border-slate-200 p-2 rounded shadow-sm cursor-grab hover:border-[#93c21c] flex items-start gap-2">
                <div class="w-8 h-8 rounded bg-slate-100 flex-shrink-0 overflow-hidden mt-0.5 flex items-center justify-center">
                    ${
                    imageSrc
                        ? `<img src="${img(imageSrc)}" class="w-full h-full object-cover">`
                        : (iconHtml || `<i class="fa-solid fa-box text-slate-400"></i>`)
                    }
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-800 truncate">${esc(title)}</div>
                        ${subtitle ? `<div class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">${esc(subtitle)}</div>` : ``}
                    </div>
                    ${rightHtml || ``}
                    </div>

                    <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-1">
                    ${badge ? `<span class="text-[9px] font-black text-[#93c21c]">${esc(badge)}</span>` : ``}
                    </div>
                </div>
                </div>
            `;

            try {
                list.innerHTML = `
                <div class="text-xs text-slate-400 p-2">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Lade Bibliothek...
                </div>
                `;

                // Decide endpoint based on libraryMode
               const mode = (State.libraryMode || 'group_sets'); // 'group_sets' | 'sets' | 'products'

                let endpoint = `${API_BASE}/wizard/products`; // fallback
                if (mode === 'group_sets') endpoint = `${API_BASE}/wizard/group-sets`;
                if (mode === 'sets') endpoint = `${API_BASE}/wizard/products`;      // your old mixed endpoint (sets+products grouped)
                if (mode === 'products') endpoint = `${API_BASE}/wizard/products-list`; // NEW: flat product list w/ brand+distributor+price


                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('q', q);
                url.searchParams.set('context', 'angebot');

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                // ------------------------------
                // MODE A) GROUP SETS TAB
                // Each GROUP SET is collapsible, not article_group.
                // Supports:
                // - Drag whole group set (type: master_set_group) via drag-handle
                // - Drag single set inside (type: master_set)
                // Lazy-load sets on first expand if not provided by API.
                // ------------------------------
                if (mode === 'group_sets') {
                    // Your current backend shape is:
                    // { groups: [ { article_group, image, group_sets:[ {id,name,color,description,master_sets_count, master_sets?[]} ] } ] }
                    // We'll flatten it into group_sets[] and keep article_group only as a small label.
                    const apiGroups = Array.isArray(data.groups) ? data.groups : [];

                    const flat = [];
                    apiGroups.forEach(g => {
                        const ag = (g.article_group || 'Ohne Gruppe').trim();
                        const groupImage = g.image || null;
                        const groupSets = Array.isArray(g.group_sets) ? g.group_sets : [];

                        groupSets.forEach(gs => {
                            flat.push({
                                id: gs.id,
                                name: gs.name || `Group #${gs.id}`,
                                description: gs.description || '',
                                color: gs.color || '',
                                article_group: ag,
                                image: groupImage,
                                master_sets_count: Number(gs.master_sets_count || 0),
                                master_sets: Array.isArray(gs.master_sets) ? gs.master_sets : null // may be null -> lazy load
                            });
                        });
                    });

                    // search filter (extra safety, since API also filters)
                    const qLow = (q || '').toLowerCase();
                    const filtered = qLow
                        ? flat.filter(x =>
                            (x.name || '').toLowerCase().includes(qLow) ||
                            (stripHtml(x.description || '').toLowerCase().includes(qLow)) ||
                            (x.article_group || '').toLowerCase().includes(qLow)
                        )
                        : flat;

                    filtered.sort((a, b) => (a.name || '').localeCompare(b.name || '', 'de'));

                    if (filtered.length === 0) {
                        list.innerHTML = `<div class="text-xs text-slate-400 p-3">Keine Treffer.</div>`;
                        return;
                    }

                    const renderSetCard = (ms, groupImage) => {
                        const infoBtn = `
                            <button type="button"
                                    onclick="App.openSetModal('${ms.id}')"
                                    class="text-slate-300 hover:text-[#93c21c]"
                                    title="Set anzeigen">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                        `;

                        return card({
                            type: 'master_set',
                            id: ms.id,
                            title: ms.name || `Set #${ms.id}`,
                            subtitle: preview(ms.description || '', 140),
                            badge: `SET`,
                            imageSrc: App.pickImage(ms, groupImage),

                            rightHtml: infoBtn
                        });
                    };

                    list.innerHTML = filtered.map(gs => {
                        const setsBoxId = `gs-sets-${gs.id}`;
                        const hasInlineSets = Array.isArray(gs.master_sets);

                        // header right: drag handle for whole group set
                        const headRight = `
                            <div class="flex items-center gap-2">
                                ${gs.color ? `<span class="w-3 h-3 rounded-full border border-slate-200" style="background:${esc(gs.color)}"></span>` : ``}
                                <span class="text-[10px] text-slate-400">${Number(gs.master_sets_count || 0)} Sets</span>

                                <!-- drag whole group set -->
                                <span draggable="true"
                                    ondragstart="App.dragStart(event, '${gs.id}', 'master_set_group')"
                                    class="ml-1 inline-flex items-center justify-center w-7 h-7 rounded border border-slate-200 bg-white text-slate-400 hover:text-[#93c21c] cursor-grab"
                                    title="Ganzes Group Set ziehen">
                                    <i class="fa-solid fa-grip-vertical text-xs"></i>
                                </span>
                            </div>
                        `;

                        const groupLabel = gs.article_group
                            ? `<div class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">${esc(gs.article_group)}</div>`
                            : '';

                        // initial body
                        const bodyHtml = hasInlineSets
                            ? (
                                gs.master_sets.length
                                    ? gs.master_sets
                                        .slice()
                                        .sort((a, b) => (a.name || '').localeCompare(b.name || '', 'de'))
                                        .map(ms => renderSetCard(ms, gs.image))
                                        .join('')
                                    : `<div class="text-xs text-slate-400">Keine Sets in dieser Gruppe.</div>`
                            )
                            : `<div class="text-xs text-slate-400 flex items-center gap-2">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Öffnen zum Laden…
                            </div>`;

                        return `
                            <details class="bg-white/60 border border-slate-200 rounded-lg overflow-hidden"
                                    data-gs-id="${gs.id}"
                                    ${hasInlineSets ? '' : `ontoggle="App.onGroupSetToggle(this)"`}>
                                <summary class="cursor-pointer select-none px-3 py-2 bg-slate-50 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        ${groupLabel}
                                        <div class="font-black text-slate-800 text-xs truncate">${esc(gs.name)}</div>
                                        ${gs.description ? `<div class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">${esc(preview(gs.description, 90))}</div>` : ''}
                                    </div>
                                    ${headRight}
                                </summary>

                                <div class="p-3 space-y-2 bg-slate-50/50" id="${setsBoxId}">
                                    ${bodyHtml}
                                </div>
                            </details>
                        `;
                    }).join('');

                    return;
                }

                // ------------------------------
                // MODE C) PRODUCTS LIST (flat list, with brand + distributor + price + image)
                // Expected API shape:
                // { items: [ {id, product, model, article_no, image, brand_name, distributor_name, best_price, currency} ] }
                // ------------------------------
                if (mode === 'products') {
                const items = Array.isArray(data.items) ? data.items : [];

                if (!items.length) {
                    list.innerHTML = `<div class="text-xs text-slate-400 p-3">Keine Treffer.</div>`;
                    return;
                }

                const fmt = (n) => {
                    const v = Number(n);
                    if (!Number.isFinite(v)) return '-';
                    return v.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
                };

                list.innerHTML = items.map(p => {
                    const brand = (p.brand_name || '').trim();
                    const dist  = (p.distributor_name || '').trim();
                    const price = fmt(p.best_price);

                    const subtitleParts = [];
                    if (brand) subtitleParts.push(`Brand: ${brand}`);
                    if (dist) subtitleParts.push(`Lieferant: ${dist}`);
                    subtitleParts.push(`EK: ${price}`);

                    const rightHtml = `
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[10px] font-black text-slate-700">${price}</span>
                        <span class="text-[9px] font-bold text-slate-400">${dist ? dist : '—'}</span>
                    </div>
                    `;

                    return card({
                    type: 'product',
                    id: p.id,
                    title: `${p.product || p.name || ('Produkt #' + p.id)}${p.model ? ' • ' + p.model : ''}`,
                    subtitle: subtitleParts.join(' • '),
                    badge: 'PROD',
                    imageSrc: App.pickImage(p, null),
                    rightHtml
                    });
                }).join('');

                return;
                }




                // ------------------------------
                // MODE B) PRODUCTS TAB (existing grouped by article_group)
                // Expected API shape:
                // { groups: [ { article_group, image, master_sets:[], products:[] } ] }
                // ------------------------------
                const apiGroups = Array.isArray(data.groups) ? data.groups : [];
                const groups = apiGroups.map(g => ({
                groupName: (g.article_group || 'Ohne Gruppe').trim(),
                groupImage: g.image || null,
                masterSets: Array.isArray(g.master_sets) ? g.master_sets : [],
                products: Array.isArray(g.products) ? g.products : [],
                }));

                groups.sort((a, b) => a.groupName.localeCompare(b.groupName, 'de'));

                if (groups.length === 0) {
                list.innerHTML = `<div class="text-xs text-slate-400 p-3">Keine Treffer.</div>`;
                return;
                }

                list.innerHTML = groups.map(g => {
                const masterSets = g.masterSets
                    .slice()
                    .sort((a, b) => (b.id || 0) - (a.id || 0))
                    .map(p => {
                    const infoBtn = `
                        <button type="button"
                                onclick="App.openSetModal('${p.id}')"
                                class="text-slate-300 hover:text-[#93c21c]"
                                title="Set anzeigen">
                        <i class="fa-solid fa-circle-info"></i>
                        </button>
                    `;

                    return card({
                        type: 'master_set',
                        id: p.id,
                        title: p.name,
                        subtitle: preview(p.description || '', 140),
                        badge: `SET • ${(p.components_count || 0)} Teile`,
                        imageSrc: App.pickImage(p, g.groupImage),
                        rightHtml: infoBtn
                    });
                    }).join('');

                const products = g.products
                    .slice()
                    .sort((a, b) => (b.id || 0) - (a.id || 0))
                    .map(p => card({
                    type: 'product',
                    id: p.id,
                    title: p.name,
                    subtitle: `${(p.article_no || '')}${p.model ? ` • ${p.model}` : ''}`.trim(),
                    badge: 'ART',
                    imageSrc: App.pickImage(p, g.groupImage),
                    })).join('');

                const totalCount = (g.masterSets.length + g.products.length);
                const rows = [masterSets, products].filter(Boolean).join('');

                return `
                    <details class="bg-white/60 border border-slate-200 rounded-lg overflow-hidden">
                    <summary class="cursor-pointer select-none px-3 py-2 bg-slate-50 flex items-center justify-between">
                        <div class="font-bold text-slate-700 text-xs uppercase tracking-wide">${esc(g.groupName)}</div>
                        <div class="text-[10px] text-slate-400">${totalCount}</div>
                    </summary>
                    <div class="p-3 space-y-2 bg-slate-50/50">
                        ${rows || `<div class="text-xs text-slate-400">Keine Einträge</div>`}
                    </div>
                    </details>
                `;
                }).join('');

            } catch (err) {
                console.error("Catalog search failed", err);
                list.innerHTML = `
                <div class="text-xs text-red-500 p-3">
                    Fehler beim Laden der Bibliothek.
                </div>
                `;
            }
            },
 

        dragStart: (ev, id, type) => {
            ev.dataTransfer.setData("text", id);
            ev.dataTransfer.setData("itemType", type);

            App.dragState = {
                type: 'library',
                id,
                itemType: type
            };

            App.startDragMode();
        },

        handleItemAdd: async (sIdx, id, typeFromDrag = null) => {
            ensureSection(sIdx);

            const type = resolveType(typeFromDrag);
            const rawId = id;
            const origin = window.location.origin;

            const apiUrl = (path, params = {}) => {
                const url = new URL(path, origin);
                Object.entries(params).forEach(([k, v]) => {
                    url.searchParams.set(k, String(v));
                });
                return url;
            };

            const safeRender = () => {
                if (typeof App?.renderQuotePage === "function") {
                    App.renderQuotePage();
                }
            };

            const makeParsedPriceUnit = (sourcePriceUnit, fallbackUnit = 'Stk') => {
                if (typeof App?.parsePriceUnit === 'function') {
                    return App.parsePriceUnit(sourcePriceUnit || fallbackUnit, fallbackUnit);
                }

                const raw = (sourcePriceUnit || '').toString().trim();
                if (!raw) {
                    return {
                        value: 1,
                        label: fallbackUnit || 'Stk',
                        text: `1 ${fallbackUnit || 'Stk'}`
                    };
                }

                const m = raw.match(/^(\d+(?:[.,]\d+)?)\s*(.+)$/);
                if (m) {
                    return {
                        value: parseFloat(m[1].replace(',', '.')) || 1,
                        label: (m[2] || fallbackUnit || 'Stk').trim(),
                        text: raw
                    };
                }

                return {
                    value: 1,
                    label: raw,
                    text: `1 ${raw}`
                };
            };

            const calcGross = (item) => {
                if (typeof App?.calcItemGross === 'function') {
                    return App.calcItemGross(item);
                }

                const qty = Number(item?.qty || 0);
                const price = Number(item?.price || 0);
                const baseQty = Number(item?.price_unit_value || 1);

                if (item?.isPauschal) {
                    return price;
                }

                if (!Number.isFinite(qty) || !Number.isFinite(price) || !Number.isFinite(baseQty) || baseQty <= 0) {
                    return 0;
                }

                return (qty / baseQty) * price;
            };

            try {
                if (type === "master_set") {
                    const url = apiUrl(`${API_BASE}/master-sets/${rawId}`, { context: "angebot" });
                    const resp = await fetchJson(url);
                    const data = resp?.data ?? resp ?? {};

                    const setItem = buildBaseItem({
                        item_type: "master_set",
                        productId: safeNum(data?.id ?? rawId),
                        name: safeStr(data?.name, `MasterSet #${rawId}`),
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),

                        qty: 1,
                        unit: "Set",
                        measure: "Set",

                        price_unit_value: 1,
                        price_unit_label: "Set",
                        price_unit_text: "1 Set",

                        showImage: true,
                        hideImage: false,
                        img: App.pickImage(data, App.placeholderImg(data?.name || 'SET')),

                        subItems: [],

                        creator_id: data?.creator_id ?? null,
                        creator_name: data?.creator_name ?? null,
                        count_copy: safeNum(data?.count_copy, 0),
                        count_offer: safeNum(data?.count_offer, 0),
                        is_locked: safeNum(data?.is_locked, 1),

                        price: 0,
                    });

                    const itemsArray = Array.isArray(data?.items) ? data.items : [];
                    let setSum = 0;

                    itemsArray.forEach((itemData) => {
                        if (itemData.type === 'component') {
                            const ek = safeNum(itemData.purchase_price ?? itemData.ek, 0);
                            const qty = safeNum(itemData.qty, 1);
                            const defaultMargin = App.getDefaultMargin('article');
                            const vk = ek > 0
                                ? App.vkFromEkMargin(ek, defaultMargin)
                                : safeNum(itemData.unit_price, 0);

                            const fallbackMeasure = itemData.measure || itemData.unit || "Stk";
                            const parsedPU = makeParsedPriceUnit(
                                itemData.price_unit || itemData.price_unit_text || fallbackMeasure,
                                fallbackMeasure
                            );

                            const parentLine = buildBaseItem({
                                item_type: "master_set_component",
                                component_id: itemData.id,
                                productId: safeNum(itemData.product_id, null),

                                name: itemData.name || "Komponente",
                                desc_html: pickDescHtml(itemData),
                                desc: pickDescText(itemData),

                                qty,
                                unit: fallbackMeasure,
                                measure: fallbackMeasure,

                                price_unit_value: safeNum(itemData.price_unit_value, parsedPU.value),
                                price_unit_label: itemData.price_unit_label || parsedPU.label,
                                price_unit_text: itemData.price_unit_text || parsedPU.text,

                                vpe: safeNum(itemData.vpe, 1),

                                price: vk,
                                ek,
                                purchase_price: ek,

                                margin: defaultMargin,
                                marginPercent: defaultMargin,
                                kind: 'article',

                                skonto: safeNum(itemData.skonto, 0),
                                payment_terms: safeNum(itemData.payment_terms, 14),
                                availability: itemData.availability !== false,

                                componentType: itemData.component_type || itemData.type || "haupt",
                                is_stammartikel: !!itemData.is_stammartikel,
                                is_favorite: !!itemData.is_favorite,

                                article_no: itemData.article_no || '',
                                position_id: safeNum(itemData.position_id, null),
                                position_name: safeStr(itemData.position_name, ''),
                                distributor_id: safeNum(itemData.distributor_id, null),
                                distributor_price_id: safeNum(itemData.distributor_price_id, null),

                                img: App.pickImage(itemData),
                                showImage: true,
                                depth: 1
                            });

                            setSum += calcGross(parentLine);
                            setItem.subItems.push(parentLine);

                            if (Array.isArray(itemData.children) && itemData.children.length > 0) {
                                itemData.children.forEach((childData) => {
                                    const childEk = safeNum(childData.purchase_price ?? childData.ek, 0);
                                    const childDefaultMargin = App.getDefaultMargin('article');
                                    const childVk = childEk > 0
                                        ? App.vkFromEkMargin(childEk, childDefaultMargin)
                                        : safeNum(childData.unit_price, 0);
                                    const childQty = safeNum(childData.qty, 1);

                                    const childFallbackMeasure = childData.measure || childData.unit || "Stk";
                                    const parsedChildPU = makeParsedPriceUnit(
                                        childData.price_unit || childData.price_unit_text || childFallbackMeasure,
                                        childFallbackMeasure
                                    );

                                    const childLine = buildBaseItem({
                                        item_type: "master_set_component_child",
                                        component_id: childData.id,
                                        productId: safeNum(childData.product_id, null),

                                        name: childData.name || "Unterkomponente",
                                        desc_html: pickDescHtml(childData),
                                        desc: pickDescText(childData),

                                        qty: childQty,
                                        unit: childFallbackMeasure,
                                        measure: childFallbackMeasure,

                                        price_unit_value: safeNum(childData.price_unit_value, parsedChildPU.value),
                                        price_unit_label: childData.price_unit_label || parsedChildPU.label,
                                        price_unit_text: childData.price_unit_text || parsedChildPU.text,

                                        vpe: safeNum(childData.vpe, 1),

                                        price: childVk,
                                        ek: childEk,
                                        purchase_price: childEk,

                                        margin: childDefaultMargin,
                                        marginPercent: childDefaultMargin,
                                        kind: 'article',

                                        skonto: safeNum(childData.skonto, 0),
                                        payment_terms: safeNum(childData.payment_terms, 14),
                                        availability: childData.availability !== false,

                                        componentType: childData.component_type || childData.type || "haupt",
                                        is_stammartikel: !!childData.is_stammartikel,
                                        is_favorite: !!childData.is_favorite,

                                        article_no: childData.article_no || '',
                                        position_id: safeNum(childData.position_id, null),
                                        position_name: safeStr(childData.position_name, ''),
                                        distributor_id: safeNum(childData.distributor_id, null),
                                        distributor_price_id: safeNum(childData.distributor_price_id, null),

                                        img: App.pickImage(childData),
                                        showImage: true,

                                        active: false,
                                        depth: 2,
                                        isChildNode: true
                                    });

                                    setSum += calcGross(childLine);
                                    setItem.subItems.push(childLine);
                                });
                            }
                        }
                        else if (itemData.type === 'labor') {
                            const laborChildren = Array.isArray(itemData.children) ? itemData.children : [];

                            // Keep ONE subItem only: "Arbeitsleistung"
                            // Store detailed rows inside labor_rows[]
                           const laborRows = laborChildren.length
                                ? laborChildren.map((childData, idx) => {
                                    const hours = safeNum(childData.hours ?? childData.qty, 1);
                                    const rate = safeNum(
                                        childData.rate ??
                                        childData.hourly_rate ??
                                        childData.qualification_price ??
                                        childData.price,
                                        0
                                    );
                                    const ek = safeNum(childData.ek, 0);

                                    return {
                                        id: safeNum(childData.id, idx + 1),

                                        // IMPORTANT: keep real stored qualification reference if backend sends it
                                        qualification_id: childData.qualification_id != null
                                            ? Number(childData.qualification_id)
                                            : (childData.employee_type_id != null
                                                ? Number(childData.employee_type_id)
                                                : null),

                                        qualification_name:
                                            childData.qualification_name ||
                                            childData.position_name ||
                                            childData.name ||
                                            `Mitarbeiter ${idx + 1}`,

                                        qty: hours,
                                        unit: childData.unit || 'Std',
                                        rate: rate,
                                        ek: ek,
                                        margin_percent: safeNum(
                                            childData.margin_percent ?? childData.margin,
                                            App.getDefaultMargin('labor')
                                        ),
                                        total: safeNum(childData.total, hours * rate)
                                    };
                                })
                                : [{
                                    id: safeNum(itemData.id, 1),
                                    qualification_id: itemData.qualification_id != null
                                        ? Number(itemData.qualification_id)
                                        : (itemData.employee_type_id != null
                                            ? Number(itemData.employee_type_id)
                                            : null),
                                    qualification_name:
                                        itemData.qualification_name ||
                                        itemData.position_name ||
                                        itemData.name ||
                                        'Arbeitsleistung',
                                    qty: safeNum(itemData.hours ?? itemData.qty, 1),
                                    unit: itemData.unit || 'Std',
                                    rate: safeNum(itemData.rate ?? itemData.hourly_rate, 0),
                                    ek: safeNum(itemData.ek, 0),
                                    margin_percent: safeNum(
                                        itemData.margin_percent ?? itemData.margin,
                                        App.getDefaultMargin('labor')
                                    ),
                                    total: safeNum(
                                        itemData.total,
                                        safeNum(itemData.hours ?? itemData.qty, 1) * safeNum(itemData.rate ?? itemData.hourly_rate, 0)
                                    )
                                }];

                            const laborQty = laborRows.reduce((sum, row) => sum + safeNum(row.qty, 0), 0);
                            const laborTotal = laborRows.reduce((sum, row) => sum + (safeNum(row.qty, 0) * safeNum(row.rate, 0)), 0);
                            const laborEkTotal = laborRows.reduce((sum, row) => sum + (safeNum(row.qty, 0) * safeNum(row.ek, 0)), 0);

                            const laborLine = buildBaseItem({
                                item_type: "labor",
                                kind: "labor",
                                productId: safeNum(itemData.id, null),

                                name: itemData.name || "Arbeitsleistung",
                                desc_html: "",
                                desc: "",

                                qty: laborQty || 1,
                                unit: itemData.unit || "Std",
                                measure: itemData.unit || "Std",

                                price_unit_value: 1,
                                price_unit_label: itemData.unit || "Std",
                                price_unit_text: `1 ${itemData.unit || "Std"}`,

                                // aggregated values on parent row
                                price: laborQty > 0 ? (laborTotal / laborQty) : 0,
                                rate: laborQty > 0 ? (laborTotal / laborQty) : 0,
                                ek: laborQty > 0 ? (laborEkTotal / laborQty) : 0,
                                purchase_price: laborQty > 0 ? (laborEkTotal / laborQty) : 0,

                                isPauschal: false,
                                showImage: false,
                                depth: 1,

                                // ✅ embedded inner-table rows
                                labor_rows: laborRows
                            });

                            setSum += laborTotal;
                            setItem.subItems.push(laborLine);
                        }
                    });

                    setItem.price = setSum;
                    pushItem(sIdx, setItem);
                    safeRender();
                    return;
                }

                if (type === "master_set_group") {
                    const url = apiUrl(`${API_BASE}/master-set-groups/${rawId}`, { context: "angebot" });
                    const data = await fetchJson(url);

                    const sets = Array.isArray(data?.master_sets)
                        ? data.master_sets
                        : (Array.isArray(data?.sets) ? data.sets : []);

                    for (const ms of sets) {
                        if (ms?.id == null) continue;
                        await App.handleItemAdd(sIdx, ms.id, "master_set");
                    }

                    safeRender();
                    return;
                }

                // PRODUCT
                {
                    const url = apiUrl(`${API_BASE}/products/${rawId}`, { context: "angebot" });

                    let data = null;
                    try {
                        data = await fetchJson(url);
                    } catch (_) {
                        data = null;
                    }

                    const p = data?.data ?? data ?? {};
                    const fallbackUnit = p?.unit || 'Stk';
                    const parsedPU = makeParsedPriceUnit(
                        p?.price_unit || p?.price_unit_text || fallbackUnit,
                        fallbackUnit
                    );

                    const ek = safeNum(p?.ek ?? p?.purchase_price ?? p?.cost ?? 0);
                    const defaultMargin = App.getDefaultMargin('article');
                    const vk = ek > 0
                        ? App.vkFromEkMargin(ek, defaultMargin)
                        : safeNum(p?.price ?? p?.vk ?? 0);

                    const item = buildBaseItem({
                        item_type: "product",
                        productId: safeNum(p?.id ?? rawId),

                        name: safeStr(p?.name ?? p?.product, `Produkt ID: ${rawId}`),
                        desc_html: pickDescHtml(p),
                        desc: pickDescText(p),

                        img: App.pickImage(p),
                        showImage: true,

                        kind: 'article',

                        price: vk,
                        ek: ek,
                        purchase_price: ek,
                        margin: defaultMargin,
                        marginPercent: defaultMargin,

                        qty: 1,
                        unit: parsedPU.label,
                        measure: parsedPU.label,

                        price_unit_value: safeNum(p?.price_unit_value, parsedPU.value),
                        price_unit_label: p?.price_unit_label || parsedPU.label,
                        price_unit_text: p?.price_unit_text || parsedPU.text,

                        article_no: p?.article_no || '',
                        brand_name: p?.brand_name || '',
                        distributor_name: p?.distributor_name || '',

                        subItems: [],
                    });
                    pushItem(sIdx, item);
                    safeRender();
                    return;
                }
            } catch (err) {
                console.error("handleItemAdd failed", err);
                safeRender();
            }
        },
        // ============================================================
        // 2. addLibraryItemAsSubPosition
        // ============================================================
        addLibraryItemAsSubPosition: async (targetSIdx, targetIIdx, id, typeFromDrag) => {
            const safeNum = (v, d = 0) => { const n = Number(v); return Number.isFinite(n) ? n : d; };
            const sec = State.sections[targetSIdx];
            if (!sec) return;
            const parent = sec.items?.[targetIIdx];
            if (!parent) return;

            if (!Array.isArray(parent.subItems)) parent.subItems = [];

            const fetchJson = async (urlObj) => {
                const res = await fetch(urlObj.toString(), { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            };

            const pushSub = (sub) => {
                parent.subItems.push({
                    active: true, hidePrices: false, qty: 1, unit: 'Stk', price: 0, desc: '', desc_html: '', ...sub
                });
            };

            try {
               if (typeFromDrag === 'master_set') {
                    const url = new URL(`${API_BASE}/master-sets/${id}`, window.location.origin);
                    url.searchParams.set('context', 'angebot');
                    const resp = await fetchJson(url);
                    const data = resp?.data ?? resp ?? {};

                    let totalSet = 0;
                    let totalEk = 0;

                    (data.items || []).forEach(itemData => {
                        if (itemData.type === 'component') {
                            const ek = safeNum(itemData.purchase_price ?? itemData.ek, 0);
                            const qty = safeNum(itemData.qty, 1);

                            const parsedPU = App.parsePriceUnit(
                                itemData.price_unit || itemData.price_unit_text || itemData.measure || itemData.unit || 'Stk',
                                itemData.measure || itemData.unit || 'Stk'
                            );

                            const defaultMargin = App.getDefaultMargin('article');
                            const vk = ek > 0
                                ? App.vkFromEkMargin(ek, defaultMargin)
                                : safeNum(itemData.unit_price ?? itemData.price ?? 0);

                            totalSet += App.calcScaledLineTotal(
                                qty,
                                vk,
                                safeNum(itemData.price_unit_value, parsedPU.value)
                            );

                            totalEk += App.calcScaledLineTotal(
                                qty,
                                ek,
                                safeNum(itemData.price_unit_value, parsedPU.value)
                            );
                        }
                        else if (itemData.type === 'labor') {
                            const total = safeNum(itemData.total, 0);
                            const ek = safeNum(itemData.ek_total ?? itemData.ek ?? 0);

                            totalSet += total;
                            totalEk += ek;
                        }
                    });

                    pushSub({
                        item_type: 'sub_master_set',
                        productId: safeNum(data.id ?? id),
                        name: data.name || `MasterSet #${id}`,
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),

                        qty: 1,
                        unit: 'Set',
                        measure: 'Set',

                        price_unit_value: 1,
                        price_unit_label: 'Set',
                        price_unit_text: '1 Set',

                        price: safeNum(data.total_price ?? data.price ?? totalSet),
                        ek: totalEk,
                        purchase_price: totalEk,
                        margin: 0,
                        marginPercent: 0,
                        kind: 'article',

                        img: App.pickImage(data),
                    });

                    App.renderQuotePage();
                    return;
                }
                if (typeFromDrag === 'product') {
                    const url = new URL(`${API_BASE}/products/${id}`, window.location.origin);
                    url.searchParams.set('context', 'angebot');

                    let resp = null;
                    try {
                        resp = await fetchJson(url);
                    } catch (_) {
                        resp = null;
                    }

                    const data = resp?.data ?? resp ?? {};

                    const ek = safeNum(data?.ek ?? data?.purchase_price ?? data?.cost ?? 0);
                    const defaultMargin = App.getDefaultMargin('article');

                    const parsedPU = App.parsePriceUnit(
                        data?.price_unit || data?.price_unit_text || data?.unit || 'Stk',
                        data?.unit || 'Stk'
                    );

                    const vk = ek > 0
                        ? App.vkFromEkMargin(ek, defaultMargin)
                        : safeNum(data?.price ?? data?.best_price ?? data?.vk ?? 0);

                    pushSub({
                        item_type: 'sub_product',
                        productId: safeNum(data?.id ?? id),

                        name: (data?.name || data?.product || `Produkt #${id}`).toString(),
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),

                        qty: 1,
                        unit: parsedPU.label,
                        measure: parsedPU.label,

                        price_unit_value: safeNum(data?.price_unit_value, parsedPU.value),
                        price_unit_label: data?.price_unit_label || parsedPU.label,
                        price_unit_text: data?.price_unit_text || parsedPU.text,

                        price: vk,
                        ek: ek,
                        purchase_price: ek,
                        margin: defaultMargin,
                        marginPercent: defaultMargin,
                        kind: 'article',

                        img: App.pickImage(data),
                    });

                    App.renderQuotePage();
                    return;
                }
            } catch (err) {
                console.error('addLibraryItemAsSubPosition failed', err);
                App.renderQuotePage();
            }
        },


        // --- MAIN APP LOGIC ---
        switchView: (view) => { document.querySelectorAll('.view-section').forEach(v=>v.classList.remove('active')); document.getElementById('view-'+view).classList.add('active'); },
        toggleSidebar: (side) => {
            const el = document.getElementById(side === 'left' ? 'sidebar-left' : 'sidebar-right');
            if (!el) return;

            el.classList.toggle('sidebar-collapsed');

            if (side === 'right') {
                const isCollapsed = el.classList.contains('sidebar-collapsed');
                const label = document.getElementById('calc-sidebar-toggle-label');
                const btn = document.getElementById('calc-sidebar-toggle-btn');

                if (label) {
                    label.innerText = isCollapsed ? 'Kalkulation öffnen' : 'Kalkulation schließen';
                }

                if (btn) {
                    btn.classList.toggle('border-[#93c21c]', !isCollapsed);
                    btn.classList.toggle('text-[#93c21c]', !isCollapsed);
                    btn.classList.toggle('bg-[#f7fee7]', !isCollapsed);
                }
            }
        },        
        switchSidebarTab: (tab) => {
            document.querySelectorAll('.sq-side-main-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + tab)?.classList.add('active');

            if (tab === 'lib') {
                document.getElementById('sidebar-content-lib')?.classList.remove('hidden');
                document.getElementById('sidebar-content-tools')?.classList.add('hidden');
            } else {
                document.getElementById('sidebar-content-lib')?.classList.add('hidden');
                document.getElementById('sidebar-content-tools')?.classList.remove('hidden');
                App.renderSidebarTools();
            }
        },

        renderSidebarTools: () => { const c=document.getElementById('tools-list'); c.innerHTML=''; [...['https://placehold.co/100x100/green/white?text=Geprüft'],...State.toolsImages].forEach(src=>{ c.innerHTML+=`<div draggable="true" ondragstart="App.dragStartTool(event,'${src}')" class="bg-white border rounded p-2 cursor-grab"><img src="${src}" class="w-full h-16 object-contain"></div>`; }); },
        
        updateBranding: () => {
            const color = document.getElementById('wiz-brand-color')?.value || '#93c21c';
            const mode = document.querySelector('input[name="wiz-brand-mode"]:checked')?.value || 'text';
            const name = document.getElementById('wiz-brand-name')?.value || 'SOLAR ASPEKT';
            const logoUrl = document.getElementById('wiz-brand-logo')?.value || '';

            State.brandColor = color;
            State.brandMode = mode;
            State.companyName = name;
            State.brandLogoUrl = logoUrl;

            // update UI toggles
            const textWrap = document.getElementById('brand-text-wrap');
            const logoWrap = document.getElementById('brand-logo-wrap');
            if (mode === 'image') {
                textWrap?.classList.add('hidden');
                logoWrap?.classList.remove('hidden');
            } else {
                logoWrap?.classList.add('hidden');
                textWrap?.classList.remove('hidden');
            }

            // logo preview
            const prev = document.getElementById('wiz-logo-preview');
            if (prev && logoUrl) prev.src = logoUrl;

            // set CSS variable so entire layout updates
            document.documentElement.style.setProperty('--brand-color', color);
            document.getElementById('color-hex-label').innerText = color;

            // If already in editor, re-render to apply changes everywhere
            if (document.getElementById('view-editor')?.classList.contains('active')) {
                App.applyBrandingToCover();
                App.renderQuotePage(false);
            }
            },


        startQuote: () => {
            State.projectDate = document.getElementById('wiz-date').value;
            const types = document.getElementsByName('wiz-doc-type'); types.forEach(t=>{if(t.checked)State.docType=t.value});
            
            document.getElementById('doc-cust-name').innerText = State.customer.name;
            document.getElementById('doc-cust-lastname').innerText = State.customer.lastname || '';
            document.getElementById('doc-cust-addr').innerHTML = `${State.customer.street || ''}<br>${State.customer.postcode || ''} ${State.customer.city || ''}`;
            document.getElementById('doc-date-line').innerText = `Wehrheim, ${new Date(State.projectDate).toLocaleDateString('de-DE')}`;
            document.getElementById('editor-doc-type-label').innerText = State.docType;
            document.getElementById('doc-main-title').innerText = `Unverbindliches ${State.docType} für...`;
            document.getElementById('lbl-doc-id-name').innerText = State.docType==='Angebot'?'Angebotsnummer':'KVA-Nummer';
            document.getElementById('doc-cust-id').value = State.customer.customer_no; 
            document.querySelectorAll('.pdf-logo-text').forEach(el => el.innerText = State.companyName);
            if (document.getElementById('doc-logo-text')) document.getElementById('doc-logo-text').innerText = State.companyName; 
            App.applyBrandingToCover();

            const teamNameEl = document.getElementById('doc-team-name');
                if (teamNameEl) {
                    teamNameEl.innerText = `Ihr ${State.companyName}-Team`;
                }

                const coverEl = document.getElementById('doc-cover-text');
                if (coverEl) {
                    if (!State.coverTextHtml) {
                        State.coverTextHtml = coverEl.innerHTML.trim();
                    } else {
                        coverEl.innerHTML = State.coverTextHtml;
                    }
                }
            document.getElementById('footer-company').innerText = `${State.companyName} GmbH`;
            document.getElementById('doc-company-header').innerText = `${State.companyName} GmbH • Am Kappengraben 10 • 61273 Wehrheim`;

            if(State.sections.length === 0) App.addSection('1. Hauptpositionen', false);
            App.renderSidebar();
            App.renderQuotePage();
            App.switchView('editor');
            App.Tabs.switch('list');
        },

        // --- PAGE RENDERER ---
        createPage: (idx, forPrint) => {
            const title = State.docType==='Angebot'?'ANGEBOT':'KOSTENVORANSCHLAG';
            const div = document.createElement('div');
            div.className = 'a4-page flex-shrink-0 dynamic-page relative';
            if(!forPrint) { div.ondragover=e=>App.allowDrop(e); div.ondrop=e=>App.dropTool(e, idx); }
            div.innerHTML = `${
                (State.brandMode === 'image' && State.brandLogoUrl)
                    ? `<div class="absolute top-6 right-10"><img src="${State.brandLogoUrl}" alt="Logo" style="height:28px;max-width:180px;object-fit:contain;"></div>`
                    : `<div class="pdf-logo-text absolute top-8 right-12 text-sm">${State.companyName}</div>`
                }  
            <div class="flex justify-between items-end border-b-2 border-t-2 border-[#93c21c] pb-1 mb-1 mt-5"><div class="font-bold text-sm text-[#93c21c]">${title} <span class="sync-offer-id text-[!#727272]">${State.offerId}</span></div></div><div class="pos-header-grid pb-2"><div class="text-center">Pos.</div><div>Artikelbezeichnung</div><div class="text-center">Menge</div><div class="text-center">Einh.</div><div class="text-right">EP</div><div class="text-right">GP</div><div></div></div><div class="page-content flex-1 relative"></div><div class="mt-auto border-t border-slate-200 pt-2 text-[9px] text-slate-400 text-center mb-4">Seite ${idx} • ${State.docType} freibleibend</div>`;
            return div;
        },

        renderQuotePage: (forPrint = false) => {
            // 1. Setup Containers & State
            const container = forPrint ? document.getElementById('print-preview-content') : document.getElementById('position-pages-container');
            container.innerHTML = '';
            if (!forPrint) document.getElementById('nav-pane').innerHTML = '';

            const showHidden = forPrint ? false : document.getElementById('show-hidden-toggle').checked;

            // 2. Pagination State
            let pageIndex = 2; // Page 1 is cover
            let currentPage = App.createPage(pageIndex, forPrint);
            container.appendChild(currentPage);
            let contentBox = currentPage.querySelector('.page-content');

           
            App.renderFloatingImages(currentPage, pageIndex, forPrint);

            let posCounter = 1;

            // 3. Helper: Add Element to Page with Overflow Check
            const addToPage = (element) => {
                contentBox.appendChild(element);

                // Check for overflow
                if (contentBox.scrollHeight > contentBox.clientHeight + 2) {
                    contentBox.removeChild(element); // Take it back

                    // Create New Page
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint);
                    container.appendChild(currentPage);
                    contentBox = currentPage.querySelector('.page-content');
 
                    App.renderFloatingImages(currentPage, pageIndex, forPrint);

                    // If it was a sub-item, maybe add a "Continuation" header here (optional refinement)
                    contentBox.appendChild(element); // Add to new page
                    return true; // Page break happened
                }
                return false;
            };

            // 4. Recursive Row Renderer (Handles Level 1, 2, 3...)
            // context: { sIdx, parentIndices: [iIdx, subIdx...] }
            const createRowHtml = (item, context, level, posNumberString) => {
                const { sIdx, iIdx, subIdx } = context;
                const section = State.sections?.[sIdx];
                const isLocked = !!section?.isLocked;
                if (!section) {
                    console.warn('createRowHtml: missing section for sIdx =', sIdx, { context, item });
                }
                
                // --- Visuals based on Level ---
                // Level 0 = Main Position
                // Level 1 = Component
                // Level 2 = Sub-Component
                
                let rowClasses = `item-group group`;
                let indentStyles = `mb-4`;
                let namePrefix = "";
                let nameColor = "text-[#5298bc]";
                let posColor = "text-slate-600";
                
                if (level === 1) {
                    rowClasses += ` border-t border-slate-100 bg-slate-50/50 ml-4`;
                    indentStyles = `pl-4 border-l-2 border-slate-100`;
                    nameColor = "text-slate-700";
                    posColor = "text-slate-400";
                } else if (level >= 2) {
                    rowClasses += ` border-t border-slate-100 bg-slate-100/50 ml-8`; // More indentation
                    indentStyles = `pl-2 border-l-2 border-slate-200 border-dashed`;
                    namePrefix = `<i class="fa-solid fa-turn-up rotate-90 mr-2 text-[8px] text-slate-400"></i>`;
                    nameColor = "text-slate-600";
                    posColor = "text-slate-300";
                }

                // Status & Classes
                const itemStatus = item.status || 'normal';
                const isItemOpt = itemStatus === 'optional';
                const isItemAlt = itemStatus === 'alternative';
                const itemBadges = isItemOpt ? ' (Optional)' : (isItemAlt ? ' (Alternativ)' : '');
                
                if (!item.active) rowClasses += ' pos-inactive';
                if (isItemOpt || isItemAlt) rowClasses += ' opacity-60';

                const total = App.calcItemGross(item);
                const hidePrices = (item.isPauschal || item.hidePrices || State.sections[sIdx].config.hidePrices);

                // --- Values ---
                const ctxFn = (level === 0) 
                    ? `App.updateItemDetails(${sIdx},${iIdx},`
                    : `App.updateSubItemDetails(${sIdx},${iIdx},${subIdx !== null ? subIdx : 'null'},`; // Note: deeply nested updates might need index path fix if editing deeper than level 1

                const nameVal = forPrint
                    ? `<span>${namePrefix}${App.escapeHtml(item.name)} <span class="text-xs text-slate-400 font-normal">${itemBadges}</span></span>`
                    : `<div class="flex items-center gap-1 w-full">
                         ${namePrefix}
                         <input class="clean-input font-bold ${nameColor} w-full" value="${App.escapeHtml(item.name)}" onchange="${ctxFn}'name',this.value)">
                         <span class="text-xs text-slate-400 whitespace-nowrap">${itemBadges}</span>
                       </div>`;

                const qtyVal = forPrint ? App.escapeHtml(item.qty) : `<input type="number" step="0.01" class="clean-input text-center font-bold" value="${App.escapeHtml(item.qty)}" onchange="${ctxFn}'qty',this.value)">`;
                const unitVal = forPrint
                    ? App.escapeHtml(item.measure || item.unit || 'Stk')
                    : (
                        level === 0
                            ? `<select class="clean-input text-center bg-transparent"
                                    onchange="App.updateItemUnit(${sIdx},${iIdx},this.value)">
                                    ${App.renderUnitOptions(item.measure || item.unit || 'Stk')}
                            </select>`
                            : `<select class="clean-input text-center bg-transparent"
                                    onchange="App.updateSubItemUnit(${sIdx},${iIdx},${subIdx !== null ? subIdx : 0},this.value)">
                                    ${App.renderUnitOptions(item.measure || item.unit || 'Stk')}
                            </select>`
                    );
                
                const epDisplay = hidePrices ? '-' : item.price.toLocaleString('de-DE') + ' €';
                const gpDisplay = hidePrices ? '-' : ((isItemOpt || isItemAlt) ? `(${total.toLocaleString('de-DE')} €)` : total.toLocaleString('de-DE') + ' €');

                // --- Description ---
                const descHtml = App.getItemDescHtml(item);
                const descFallback = '';
                const hasLaborRows = Array.isArray(item.labor_rows) && item.labor_rows.length > 0;
                const laborTableHtml = hasLaborRows ? App.renderLaborRowsTable(item.labor_rows, forPrint) : '';

                const descVal = forPrint
                    ? ((descHtml || descFallback || '') + laborTableHtml)
                    : `
                        <div class="space-y-2">
                            <div class="editable-field p-2 rounded bg-slate-50 border border-dashed border-slate-200 hover:border-[#93c21c] cursor-pointer min-h-[1.5rem]" 
                                onclick="App.openDescModal(${sIdx},${iIdx},${subIdx !== null ? subIdx : 'null'})">
                                ${descHtml || descFallback || `<span class="text-slate-400">Beschreibung...</span>`}
                            </div>
                            ${laborTableHtml}
                        </div>
                    `;

                // --- Image ---
                let badgeHtml = '';
                if (item.badge) {
                    const p = item.badge.pos;
                    const posCls = p === 'tl' ? 'top-0 left-0' : p === 'tr' ? 'top-0 right-0' : p === 'bl' ? 'bottom-0 left-0' : 'bottom-0 right-0';
                    badgeHtml = item.badge.type === 'text'
                        ? `<div class="absolute ${posCls} bg-brand-primary text-white text-[8px] font-bold px-1 rounded z-10">${item.badge.text}</div>`
                        : `<img src="${item.badge.src}" class="absolute ${posCls} w-6 h-6 object-contain z-10">`;
                }
                if (!item.active) badgeHtml += `<div class="absolute top-0 right-0 bg-red-500 text-white text-[8px] px-1 rounded z-20">HIDDEN</div>`;

                const imgHtml = (item.hideImage || item.showImage === false) ? ''
                    : `<div class="prod-img-container" onclick="${!forPrint ? `App.handleImageClick(${sIdx},${iIdx},${subIdx !== null ? subIdx : 'null'})` : ''}">
                         <img src="${item.img || 'https://placehold.co/150?text='}" class="w-full h-full object-contain bg-white">
                         ${badgeHtml}
                       </div>`;

                // --- Tools & Drag ---
                const tools = forPrint ? '' : `<div class="mt-1 flex gap-2 no-print"><button onclick="App.addSubItem(${sIdx},${iIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-plus"></i> Component</button><button onclick="App.openPosSettings(${sIdx},${iIdx},${subIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-cog"></i> Settings</button><button onclick="App.removeItem(${sIdx},${iIdx},${subIdx})" class="text-[9px] text-red-300 hover:text-red-500"><i class="fa-solid fa-trash"></i></button></div>`;

                // Simplified Drag Logic for Level 0/1 (Deep nesting drag logic omitted for brevity, but IDs are there)
               let dragAttrs = '';
                let handleHtml = '';

                if (!forPrint && !isLocked) {
                    const currentSubIdx = (subIdx !== null && subIdx !== undefined) ? subIdx : 'null';
                    const targetTopIndex = iIdx;

                    const dragOverJs = `
                        event.preventDefault();
                        if (App.isLibraryDrag()) {
                            this.classList.add('drag-over-sub');
                            this.classList.remove('drag-over-sort');
                        } else if (App.getDragState()?.type === 'pos') {
                            this.classList.add('drag-over-sort');
                            this.classList.remove('drag-over-sub');
                        }
                    `;

                    const dragLeaveJs = `
                        this.classList.remove('drag-over-sub');
                        this.classList.remove('drag-over-sort');
                    `;

                    const dropJs = `
                        event.preventDefault();
                        this.classList.remove('drag-over-sub');
                        this.classList.remove('drag-over-sort');

                        if (App.isLibraryDrag()) {
                            const id = event.dataTransfer.getData('text');
                            const type = event.dataTransfer.getData('itemType');
                            if (id && type) {
                                App.addLibraryItemAsSubPosition(${sIdx}, ${targetTopIndex}, id, type);
                            }
                            App.clearDragMode();
                            return;
                        }

                        if (App.getDragState()?.type === 'pos') {
                            App.moveDraggedNode(
                                App.getDragState(),
                                {
                                    mode: 'sort-array',
                                    sIdx: ${sIdx},
                                    iIdx: ${iIdx},
                                    subIdx: ${currentSubIdx}
                                }
                            );
                            App.clearDragMode();
                            return;
                        }
                    `;

                    dragAttrs = `
                        ondragover="${dragOverJs}"
                        ondragleave="${dragLeaveJs}"
                        ondrop="${dropJs}"
                    `;

                    handleHtml = `
                        <span class="drag-handle no-print"
                            draggable="true"
                            ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx}, ${currentSubIdx})"
                            title="Ziehen">
                            <i class="fa-solid fa-grip-lines"></i>
                        </span>
                    `;
                }
                return `
                    <div class="${rowClasses}" ${dragAttrs}>
                        <div class="pos-row-top" style="border-bottom: ${level > 0 ? '1px dotted #cbd5e1' : '2px solid #74b2d4'}; padding-bottom: 0.5rem; margin-bottom: 0.5rem; ${level > 0 ? 'grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem;' : ''}">
                            <div class="text-center font-bold ${posColor}">${posNumberString}</div>
                            <div>${nameVal}</div>
                            <div class="text-center">${qtyVal}</div>
                            <div class="text-center text-[10px] text-slate-500">${unitVal}</div>
                            <div class="text-right font-mono text-[10px]">${epDisplay}</div>
                            <div class="text-right font-mono text-[10px]">${gpDisplay}</div>
                            <div class="flex items-center justify-center">${handleHtml}</div>
                        </div>
                        <div class="pos-row-bottom ${indentStyles} ${hasLaborRows ? 'items-start' : ''}">
                            ${imgHtml}
                            <div class="flex-1">
                                <div class="text-[11px] text-slate-500 leading-relaxed ${hasLaborRows ? 'w-full' : ''}">
                                    ${descVal}
                                </div>
                                ${tools}
                            </div>
                        </div>
                    </div>`;
            };

            // 5. Main Render Loop
            let totalNet = 0;
            let activeTotal = 0;

            (App.getRenderableSections() || []).forEach((sec, sIdx) => {
                const isPauschalSection = sec.config.mode === 'pauschal';
                const isOptSection = sec.config.type === 'optional';
                const isAltSection = sec.config.type === 'alternative';

                // 5a. Render Section Header
                const header = document.createElement('div');
                header.className = 'mb-1 mt-4';
                let secBadges = isOptSection ? '(Optional)' : (isAltSection ? '(Alternativ)' : '');

                if (sec && sec._pageBreak) {
                    addToPage(document.createElement('div')); // Force check
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint);
                    container.appendChild(currentPage);
                    contentBox = currentPage.querySelector('.page-content'); 
                    App.renderFloatingImages(currentPage, pageIndex, forPrint);
                    return;
                }

                let deleteBtn = !forPrint ? `<button onclick="App.removeSection(${sIdx})" class="ml-auto text-slate-300 hover:text-red-500 p-1 rounded hover:bg-red-50 transition-colors"><i class="fa-solid fa-trash"></i></button>` : '';

                header.innerHTML = forPrint
                    ? `<div class="text-lg font-bold text-brand-primary uppercase">${sec.title} ${secBadges}</div><div class="text-sm text-slate-600">${sec.description}</div>`
                    : `<div class="flex items-center">
                         <input value="${sec.title}" oninput="App.updateSectionMeta(${sIdx},'title',this.value)" class="text-lg font-bold text-brand-primary w-full bg-transparent outline-none">
                         <span class="text-xs text-slate-400 ml-2 whitespace-nowrap">${secBadges}</span>
                         ${deleteBtn}
                       </div>
                       <textarea oninput="App.updateSectionMeta(${sIdx},'description',this.value)" class="text-sm text-slate-500 w-full bg-transparent resize-none outline-none h-auto">${sec.description}</textarea>`;

                addToPage(header);

                if (!forPrint) {
                    header.classList.add('rounded', 'transition-colors');

                    header.ondragover = (e) => {
                        e.preventDefault();
                        if (App.isLibraryDrag()) header.classList.add('bg-[#f4f9e8]');
                    };

                    header.ondragleave = () => {
                        header.classList.remove('bg-[#f4f9e8]');
                    };

                    header.ondrop = (e) => {
                        e.preventDefault();
                        header.classList.remove('bg-[#f4f9e8]');

                        const id = e.dataTransfer.getData('text');
                        const type = e.dataTransfer.getData('itemType');

                        if (id && type) App.handleItemAdd(sIdx, id, type);

                        App.clearDragMode();
                    };
                }

                // 5b. Drop Zone (Global Section)
                if (!forPrint) {
                    let dz = document.createElement('div');
                    dz.className = 'section-drop-zone';
                    dz.ondragover = e => e.preventDefault();
                    dz.ondrop = e => {
                        e.preventDefault();
                        const id = e.dataTransfer.getData("text");
                        const type = e.dataTransfer.getData("itemType");
                        if (id) App.handleItemAdd(sIdx, id, type);
                    };
                    addToPage(dz);
                }

                // 5c. Items Loop (Recursive Function)
                // This function calculates totals and renders rows
                const processItems = (items, level, parentIdxStr, parentContext) => {
                    let localSum = 0;
                    let localEk = 0;

                    items.forEach((item, idx) => {
                        if (!item.active && !showHidden) return;

                        // Calculate visual index string (e.g. 1.2.1)
                        const currentPosStr = (level === 0) 
                            ? (item.hideNumbering ? '' : String(posCounter++).padStart(3, '0'))
                            : (item.hideNumbering ? '' : `${parentIdxStr}.${idx + 1}`);

                        // Context for updates
                        // If Level 0: subIdx is null
                        // If Level 1: subIdx is idx
                        // If Level 2: we are inside a subIdx... note: deeply nested updates require better ID tracking in real app, 
                        // but for view purpose we map L2 to L1 parent.
                        const currentContext = {
                            sIdx: sIdx,
                            iIdx: (level === 0) ? idx : parentContext.iIdx,
                            subIdx: (level === 0) ? null : idx
                        };

                        // RECURSION: Process Children First to get Totals
                        // Check if item has subItems (Level 1 children) OR isChildNode logic from previous fix (Level 2 children)
                        // Note: In your structure, only Level 0 items usually have 'subItems'. 
                        // Level 1 items might have been flattened or need to be checked if you added a children array there.
                        // Based on previous fixes, we flattened children into subItems with `depth: 2`. 
                        // So we just iterate linear if flattened, or recursive if nested.
                        
                        // Current logic: Flattened linear list in subItems with depth property?
                        // OR: Nested items.subItems -> sub.subItems?
                        // Your `handleItemAdd` flattened them into one `subItems` array with `depth`.
                        // So we actually don't need deep recursion on the array structure, just check the `depth` property.

                        // --- Price Calc ---
                        // Only Main Items (Level 0) aggregate their children in this logic
                        if (level === 0 && item.subItems && item.subItems.length > 0) {
                            let aggSum = 0;
                            let aggEk = 0;
                            item.subItems.forEach(sub => {
                                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                                    aggSum += App.calcItemGross(sub);
                                    aggEk += App.calcItemCost(sub);
                                }
                            });
                            if (!item.isPauschal) item.price = aggSum;
                            item.ek = aggEk;
                        }

                        const total = App.calcItemGross(item);
                        
                        // Global Totals (Only count Level 0 items to avoid double counting)
                        if (level === 0) {
                            const status = item.status || 'normal';
                            if (status === 'normal' && item.active !== false && item.kind !== 'note') {
                                totalNet += item.isPauschal ? item.price : total;
                                activeTotal += item.isPauschal ? item.price : total;
                            }
                        }

                        // --- RENDER ---
                        const rowEl = document.createElement('div');
                        rowEl.innerHTML = createRowHtml(item, currentContext, (item.depth || level), currentPosStr);
                        addToPage(rowEl.firstElementChild); // Extract from wrapper

                        if (!forPrint && level === 0) {
                            const intoParentZone = document.createElement('div');
                            intoParentZone.className = 'ml-12 mb-2 px-3 py-2 text-[10px] text-slate-400 border border-dashed border-slate-200 rounded bg-slate-50';
                            intoParentZone.innerHTML = '<i class="fa-solid fa-level-down-alt mr-1"></i> Hier ablegen, um als Unterposition hinzuzufügen';

                            intoParentZone.ondragover = (e) => {
                                e.preventDefault();
                                if (App.getDragState()?.type === 'pos') intoParentZone.classList.add('drag-over-sub');
                            };

                            intoParentZone.ondragleave = () => {
                                intoParentZone.classList.remove('drag-over-sub');
                            };

                            intoParentZone.ondrop = (e) => {
                                e.preventDefault();
                                intoParentZone.classList.remove('drag-over-sub');

                                if (App.getDragState()?.type === 'pos') {
                                    App.moveDraggedNode(App.dragState, {
                                        mode: 'to-sub',
                                        sIdx,
                                        iIdx: idx,
                                        depth: 1
                                    });
                                    App.clearDragMode();
                                }
                            };

                            addToPage(intoParentZone);
                        }

                        if (!forPrint) {
                                const toMainZone = document.createElement('div');
                                toMainZone.className = 'mb-3 px-3 py-2 text-[10px] text-slate-400 border border-dashed border-slate-300 rounded bg-white';
                                toMainZone.innerHTML = '<i class="fa-solid fa-arrow-up mr-1"></i> Hier ablegen, um als Hauptposition einzufügen';

                                toMainZone.ondragover = (e) => {
                                    e.preventDefault();
                                    if (App.getDragState()?.type === 'pos') toMainZone.classList.add('drag-over-sort');
                                };

                                toMainZone.ondragleave = () => {
                                    toMainZone.classList.remove('drag-over-sort');
                                };

                                toMainZone.ondrop = (e) => {
                                    e.preventDefault();
                                    toMainZone.classList.remove('drag-over-sort');

                                    if (App.getDragState()?.type === 'pos') {
                                        App.moveDraggedNode(App.dragState, {
                                            mode: 'to-main',
                                            sIdx,
                                            iIdx: 0
                                        });
                                        App.clearDragMode();
                                    }
                                };

                                addToPage(toMainZone);
                            }


                       if (item.subItems && item.subItems.length > 0) {
                            const parentStrForChildren = (level === 0) ? String(posCounter - 1).padStart(3, '0') : currentPosStr;

                            item.subItems.forEach((sub, sIdx2) => {
                                if (sub && Array.isArray(sub.labor_rows) && sub.labor_rows.length) {
                                    // keep only the parent labor row; details render inside it
                                }
                                const subLevel = Number(sub.depth || 1);

                                // depth-2 sub-components stay hidden unless explicitly activated
                                if (subLevel >= 2 && sub.active === false && !showHidden) return;

                                // normal hidden logic for any other hidden row
                                if (sub.active === false && !showHidden) return;

                                const subPosStr = sub.hideNumbering ? '' : `${parentStrForChildren}.${sIdx2 + 1}`;
                                const subContext = { sIdx: sIdx, iIdx: idx, subIdx: sIdx2 };

                                const subRowEl = document.createElement('div');
                                subRowEl.innerHTML = createRowHtml(sub, subContext, subLevel, subPosStr);
                                addToPage(subRowEl.firstElementChild);
                            });
                        }
                    });
                };

                // Start Processing Section Items
                processItems(sec.items, 0, '', null);

                // 5e. Pauschal & Add Buttons per Section
                if (isPauschalSection) {
                    const pr = document.createElement('div');
                    pr.className = "flex justify-end mt-2 pr-16 font-bold text-slate-800 text-sm border-t border-slate-300 pt-2";
                    pr.innerHTML = `<span>Pauschalpreis:</span><span class="ml-8 font-mono">${sec.config.pauschalPrice.toLocaleString('de-DE')} €</span>`;
                    addToPage(pr);
                    totalNet += sec.config.pauschalPrice;
                }

                if (!forPrint) {
                    const btn = document.createElement('div');
                    btn.className = "pb-4 pl-8 flex gap-2"; // Added 'flex gap-2' to place them side by side
                    btn.innerHTML = `
                        <button onclick="App.addManualItem(${sIdx})" class="text-[10px] font-bold text-brand-primary flex items-center gap-1 hover:bg-brand-light px-2 py-1 rounded border border-dashed border-brand-primary">
                            <i class="fa-solid fa-box"></i> Position
                        </button>
                        <button onclick="App.addLaborItem(${sIdx})" class="text-[10px] font-bold text-blue-500 flex items-center gap-1 hover:bg-blue-50 px-2 py-1 rounded border border-dashed border-blue-500">
                            <i class="fa-solid fa-user-clock"></i> Personnel / Labor
                        </button>
                    `;
                    addToPage(btn);
                }
            });

            // 6. Global Drop & Totals
            if (!forPrint) {
                let dzG = document.createElement('div');
                dzG.className = 'section-drop-zone border-2 border-dashed border-slate-300 rounded flex items-center justify-center text-slate-400 text-xs py-6 mt-4';
                dzG.innerText = 'Neue Sektion';
               dzG.ondragover = e => {
                    e.preventDefault();
                    dzG.classList.add('drag-over');
                };

                dzG.ondragleave = () => {
                    dzG.classList.remove('drag-over');
                };
                dzG.ondrop = e => {
                    e.preventDefault();
                    dzG.classList.remove('drag-over');

                    const id = e.dataTransfer.getData("text");
                    const type = e.dataTransfer.getData("itemType");

                    if (id) {
                        const ni = App.addSection();
                        App.handleItemAdd(ni, id, type);
                    }

                    App.clearDragMode();
                };
                addToPage(dzG);
            }

             

            // New section summary block
            const summaryWrap = document.createElement('div');
            summaryWrap.innerHTML = App.renderSectionSummaryBlock(forPrint);
            addToPage(summaryWrap.firstElementChild);

            // Angebot-only footer / signature block
            if (State.docType === 'Angebot') {
                const footerBlocks = App.renderOfferFooterBlocks();

                footerBlocks.forEach(html => {
                    const wrap = document.createElement('div');
                    wrap.innerHTML = html.trim();
                    const node = wrap.firstElementChild;
                    if (node) addToPage(node);
                });
            }
            // 7. Update UI Stats
           if (!forPrint) {
                const totals = App.computeSectionSummary();

                document.getElementById('sidebar-grand-net').innerText = App.money(totals.netTotal) + ' €';
                document.getElementById('sidebar-grand-gross').innerText = App.money(totals.vatValue) + ' €';
                document.getElementById('sidebar-grand-total').innerText = App.money(totals.gross) + ' €';
                document.getElementById('lbl-total-pages').innerText = pageIndex;

                App.renderCalculationSidebar();

                // IMPORTANT:
                // build thumbs only after all pages, images, descriptions and styles are fully rendered
                App.rebuildThumbnails();
            }
        },

        focusListViewRow: (sIdx, iIdx, subIdx) => {
            // 1. Switch to the List View tab if we aren't already there
            if (App.Tabs.current !== 'list') {
                App.Tabs.switch('list');
            }

            // 2. Ensure the Section and Parent item are expanded in the List View state
            const openStore = App.ListView.openStore();
            openStore[`sec:${sIdx}`] = true;

            if (subIdx !== 'null' && subIdx !== null) {
                openStore[App.ListView.mainOpenKey(sIdx, iIdx)] = true;
                
                // If it's a deeply nested sub-item (depth >= 2), ensure its parent group is open too
                const mainItem = State.sections[sIdx]?.items[iIdx];
                if (mainItem && Array.isArray(mainItem.subItems)) {
                    const subItem = mainItem.subItems[subIdx];
                    if (subItem && subItem.depth >= 2) {
                        const groups = App.ListView.getStructuredSubItems(mainItem.subItems);
                        const group = groups.find(g => g.children.some(c => c.index == subIdx));
                        if (group) {
                            openStore[App.ListView.subOpenKey(sIdx, iIdx, group.parentIndex)] = true;
                        }
                    }
                }
            }

            // Re-render the list view to apply the expanded states
            App.ListView.render();

            // 3. Scroll to the item and highlight it temporarily
            setTimeout(() => {
                const rowId = `lv-row-${sIdx}-${iIdx}-${subIdx}`;
                const row = document.getElementById(rowId);
                
                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Save original styles
                    const originalBg = row.style.backgroundColor;
                    const originalTransition = row.style.transition;
                    
                    // Flash a nice pale green color (matching your brand)
                    row.style.transition = 'background-color 0.3s ease';
                    row.style.backgroundColor = '#d9f99d'; // Tailwind lime-200
                    
                    // Revert the color after 1.2 seconds
                    setTimeout(() => {
                        row.style.backgroundColor = originalBg;
                        // Clean up transition inline style
                        setTimeout(() => row.style.transition = originalTransition, 300);
                    }, 1200);
                }
            }, 50); // slight timeout ensures the DOM has fully rendered the new list
        },

       renderCalculationSidebar: () => {
            const c = document.getElementById('calc-sidebar-content'); 
            c.innerHTML='';
            const totals = App.computeQuoteTotals();
            const totalNet = totals.salesNet;

            const renderCard = (isSub, sIdx, iIdx, subIdx, dataObj, prefix, hasChildren) => {
                if (dataObj.active === false) return '';
                
                const qty = parseFloat(dataObj.qty) || 1;
                
                // Fallback EK to VK if EK is 0 visually
                let ek = parseFloat(dataObj.ek) || 0;
                let vk = parseFloat(dataObj.price) || 0;
                if (ek === 0 && vk > 0) ek = vk; 
                
                const totalEK = App.calcItemCost(dataObj);
                const totalVK = App.calcItemGross(dataObj);
                const marginTotal = totalVK - totalEK;
                
                const mType = dataObj.marginType || 'percent';
                let mVal = parseFloat(dataObj.margin) || 0;
                
                // Initialize margin visually if it's 0 but there is a price difference
                if (mVal === 0 && ek > 0 && vk !== ek) {
                    mVal = mType === 'percent' ? ((vk - ek) / ek) * 100 : (vk - ek);
                }
                
                let percent = (totalNet > 0 && dataObj.status !== 'optional' && dataObj.status !== 'alternative') ? ((totalVK/totalNet)*100).toFixed(1)+'%' : '-';

                const status = dataObj.status || 'normal';
                const isPauschal = !!dataObj.isPauschal;
                const hidePrices = !!dataObj.hidePrices;

                // FIX: If it is Pauschal, ALWAYS allow editing the price, even if it has sub-components!
                const isEkReadonly = hasChildren ? 'readonly disabled class="w-full border border-transparent bg-transparent text-right font-bold text-slate-600"' : 'class="w-full border border-slate-300 rounded px-1 py-0.5 text-right font-mono focus:border-brand-primary outline-none"';
                const isVkReadonly = (hasChildren && !isPauschal) ? 'readonly disabled class="w-full border border-transparent bg-transparent text-right font-bold text-slate-600"' : 'class="w-full border border-slate-300 rounded px-1 py-0.5 text-right font-mono focus:border-brand-primary outline-none bg-yellow-50"';
                const isSelectReadonly = (hasChildren && !isPauschal) ? 'disabled class="outline-none bg-transparent font-bold text-slate-400 cursor-not-allowed"' : 'class="outline-none bg-transparent font-bold text-brand-primary cursor-pointer"';

                const subIdxArg = isSub ? subIdx : 'null';
                const indentClass = isSub ? 'ml-4 border-l-4 border-brand-primary/40 bg-slate-50' : 'bg-white';
                const titleSize = isSub ? 'text-[11px] text-slate-600' : 'text-xs text-slate-800';
                const bgClass = status !== 'normal' ? 'opacity-75' : '';

                // ✅ NEU: Akkordeon-Struktur (<details> und <summary>)
                return `
                <details class="group/item ${bgClass} border border-slate-200 rounded mb-3 shadow-sm ${indentClass}">
                    <summary onclick="App.focusListViewRow(${sIdx}, ${iIdx}, ${subIdxArg})" class="cursor-pointer select-none p-2 font-bold flex justify-between items-center outline-none hover:bg-slate-100 transition-colors rounded group-open/item:rounded-b-none group-open/item:bg-slate-50 group-open/item:border-b border-slate-200">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 transition-transform group-open/item:rotate-90"></i>
                            <span class="truncate ${titleSize}" title="${App.escapeHtml(dataObj.name || 'Position')}">${prefix} ${App.escapeHtml(dataObj.name || 'Position')}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="font-mono text-[10px] text-slate-600">${totalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="text-[9px] bg-brand-light px-1 rounded text-slate-500">${percent}</span>
                        </div>
                    </summary>
                    
                    <div class="p-3 bg-white/50 rounded-b">
                        <div class="grid grid-cols-3 gap-2 mb-3 bg-slate-100/50 p-2 rounded text-xs">
                            <div>
                                <div class="text-[9px] text-slate-400 mb-0.5">EK / Einheit</div>
                                <input type="number" step="0.01" value="${ek.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'ek',this.value)" ${isEkReadonly}>
                            </div>
                            <div>
                                <div class="text-[9px] text-slate-400 mb-0.5 flex justify-between">
                                    <span>Marge</span>
                                    <select onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'marginType',this.value)" ${isSelectReadonly}>
                                        <option value="percent" ${mType==='percent'?'selected':''}>%</option>
                                        <option value="fixed" ${mType==='fixed'?'selected':''}>€</option>
                                    </select>
                                </div>
                                <input type="number" step="0.01" value="${mVal.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'margin',this.value)" ${isVkReadonly}>
                            </div>
                            <div>
                                <div class="text-[9px] text-slate-400 mb-0.5">VK / Einheit</div>
                                <input type="number" step="0.01" value="${vk.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'price',this.value)" ${isVkReadonly}>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-1 text-[10px] text-slate-500 mb-3 border-t border-slate-200 pt-2">
                            <span>EK Gesamt:</span><span class="text-right">${totalEK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span>DB1 Gesamt:</span><span class="text-right text-brand-primary">${marginTotal.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="font-bold text-xs text-slate-800">VK Gesamt:</span><span class="text-right font-bold text-xs text-slate-800">${totalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                        </div>
                        
                        <div class="flex items-center gap-2 mb-2 text-xs">
                            <select onchange="App.updatePosStatus(${sIdx},${iIdx},${subIdxArg},this.value)" class="flex-1 border border-slate-200 rounded text-xs p-1 outline-none focus:border-brand-primary bg-white">
                                <option value="normal" ${status==='normal'?'selected':''}>Standard Pos.</option>
                                <option value="optional" ${status==='optional'?'selected':''}>Optional</option>
                                <option value="alternative" ${status==='alternative'?'selected':''}>Alternativ</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-4">
                            ${!isSub ? `
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="accent-brand-primary" ${hidePrices?'checked':''} onchange="App.updatePosConfig(${sIdx},${iIdx},${subIdxArg},'hidePrices',this.checked)">
                                <span class="text-[10px]">Preise verbergen</span>
                            </label>
                            ` : ''}
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="accent-brand-primary" ${isPauschal?'checked':''} onchange="App.updatePosConfig(${sIdx},${iIdx},${subIdxArg},'isPauschal',this.checked)">
                                <span class="text-[10px]">Pauschal</span>
                            </label>
                        </div>
                    </div>
                </details>`;
            };

            State.sections.forEach((sec, sIdx) => {
                let secHtml = '';
                let secTotalVK = 0;
                
                sec.items.forEach((item, iIdx) => {
                    if(item.active === false) return; 
                    
                    const hasSub = item.subItems && item.subItems.length > 0;
                    
                    // Track section total for display
                    const itemStatus = item.status || 'normal';
                    if (itemStatus === 'normal') {
                        const itemTotal = item.isPauschal ? (parseFloat(item.price) || 0) : (parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1);
                        secTotalVK += itemTotal;
                    }
                    
                    // Render Main Item Card
                    secHtml += renderCard(false, sIdx, iIdx, null, item, `${sIdx+1}.${iIdx+1}`, hasSub);
                    
                    // Render Sub Item Cards
                    if (hasSub) {
                        item.subItems.forEach((sub, subIdx) => {
                            if (sub.active === false) return;
                            secHtml += renderCard(true, sIdx, iIdx, subIdx, sub, `↳ ${sIdx+1}.${iIdx+1}.${subIdx+1}`, false);
                        });
                    }
                });

                const secPercent = (totalNet > 0) ? ((secTotalVK / totalNet) * 100).toFixed(1) + '%' : '0.0%';

                // Wrap inside an Accordion (<details>)
                c.innerHTML += `
                <details class="mb-4 bg-slate-50 border border-slate-200 rounded-xl shadow-sm group" open>
                    <summary class="cursor-pointer select-none p-3 font-bold text-slate-700 text-xs uppercase tracking-wide flex justify-between items-center outline-none bg-slate-100 rounded-t-xl group-open:border-b border-slate-200 transition-colors hover:bg-slate-200/50">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <i class="fa-solid fa-chevron-right transition-transform group-open:rotate-90 text-slate-400"></i>
                            <span class="truncate">${sIdx+1}. ${App.escapeHtml(sec.title || 'Sektion')}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="font-mono text-[10px] text-slate-600">${secTotalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="text-[10px] bg-brand-primary text-white px-1.5 py-0.5 rounded">${secPercent}</span>
                        </div>
                    </summary>
                    <div class="p-3 bg-slate-50 border-x border-b border-slate-200 rounded-b-xl overflow-hidden">
                        ${secHtml || '<div class="text-xs text-slate-400 text-center py-4">Keine Positionen vorhanden</div>'}
                    </div>
                </details>
                `;
            });
        },
        // --- HELPERS ---
                createThumbnail: (idx, label) => {
            const nav = document.getElementById('nav-pane');
            const wrap = document.createElement('div');
            wrap.className = "thumb-wrapper";
            wrap.dataset.page = String(idx);

            const thumbBox = document.createElement('div');
            thumbBox.className = "thumb-scale-box";

            let sourcePage;
            if (idx === 1) {
                sourcePage = document.getElementById('page-1');
            } else {
                sourcePage = document.getElementById('position-pages-container').children[idx - 2];
            }

            if (sourcePage) {
                const clone = sourcePage.cloneNode(true);

                // sync live input/select/textarea values before converting to static
                const srcFields = sourcePage.querySelectorAll('input, textarea, select');
                const dstFields = clone.querySelectorAll('input, textarea, select');

                srcFields.forEach((src, i) => {
                    const dst = dstFields[i];
                    if (!dst) return;

                    const tag = (src.tagName || '').toLowerCase();
                    if (tag === 'input' || tag === 'textarea') {
                        dst.value = src.value;
                    }
                    if (tag === 'select') {
                        dst.selectedIndex = src.selectedIndex;
                    }
                    if (src.type === 'checkbox' || src.type === 'radio') {
                        dst.checked = src.checked;
                    }
                });

                // sync contenteditable live html/text
                const srcEditable = sourcePage.querySelectorAll('[contenteditable="true"]');
                const dstEditable = clone.querySelectorAll('[contenteditable="true"]');
                srcEditable.forEach((src, i) => {
                    if (dstEditable[i]) {
                        dstEditable[i].innerHTML = src.innerHTML;
                    }
                });

                App.makeThumbnailStatic(clone);
                thumbBox.appendChild(clone);
            }

            const lbl = document.createElement('div');
            lbl.className = "thumb-label";
            lbl.innerText = `Seite ${idx}`;

            wrap.appendChild(thumbBox);
            wrap.appendChild(lbl);

            wrap.onclick = () => {
                const target = (idx === 1)
                    ? document.getElementById('page-1')
                    : sourcePage;

                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    App.setActiveThumb(idx);
                }
            };

            nav.appendChild(wrap);
        },
        openPrintPreview: () => {
            // always rebuild live editor first
            App.renderQuotePage(false);

            document.getElementById('print-preview-modal').classList.remove('hidden');

            // then clone the fully rendered live pages into preview
            App.buildPrintPreview();
        },
        addSection: (t, l) => { State.sections.push({ id: 's'+Date.now(), title: t||`${State.sections.length+1}. Neue Sektion`, description: l?'Dienstleistungen':'Beschreibung', config: { mode: 'standard', pauschalPrice: 0, type: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' } }, items: [], isLaborSection:l }); App.renderQuotePage(); return State.sections.length-1; },
        
        // --- NEW DELETE SECTION FUNCTION ---
        removeSection: (sIdx) => {
            App.toastConfirmShow({
                title: 'Sektion löschen?',
                message: 'Wenn Sie diese Sektion löschen, werden alle enthaltenen Positionen ebenfalls gelöscht.<br>Fortfahren?',
                okText: 'Ja, Sektion löschen',
                cancelText: 'Abbrechen',
                onOk: () => {
                State.sections.splice(sIdx, 1);
                App.renderQuotePage();
                }
            });
            },

        dragStartTool: (ev, src) => {
            ev.dataTransfer.setData("type", "tool");
            ev.dataTransfer.setData("src", src);
            App.dragState = { type: 'tool', src };
            App.startDragMode();
        },
        allowDrop: (ev) => { ev.preventDefault(); ev.currentTarget.classList.add('drag-over'); },
        drop: (ev, sIdx) => {
            ev.preventDefault();
            ev.currentTarget.classList.remove('drag-over');

            const id = ev.dataTransfer.getData("text");
            const type = ev.dataTransfer.getData("itemType");

            if (id) App.handleItemAdd(sIdx, id, type);

            App.clearDragMode();
        },
        dropTool: (ev, pageIndex) => {
            ev.preventDefault();

            const type = ev.dataTransfer.getData("type");
            if (type !== 'tool') return;

            const src = ev.dataTransfer.getData("src");
            const rect = ev.currentTarget.getBoundingClientRect();

            State.placedImages.push({
                id: Date.now(),
                src,
                pageIndex,
                x: ev.clientX - rect.left,
                y: ev.clientY - rect.top,
                width: 100
            });

            App.renderQuotePage();
            App.clearDragMode();
        },
        removeToolImage: (id) => { State.placedImages = State.placedImages.filter(i => i.id !== id); App.renderQuotePage(); },
        renderFloatingImages: (pageEl, pageIdx, forPrint) => { const images = State.placedImages.filter(img => img.pageIndex === pageIdx); images.forEach(img => { const el = document.createElement('div'); el.className = 'floating-element'; el.style.left = img.x + 'px'; el.style.top = img.y + 'px'; el.style.width = img.width + 'px'; el.innerHTML = `<img src="${img.src}" class="w-full h-auto">` + (forPrint?'':`<div class="delete-float" onclick="App.removeToolImage(${img.id})">x</div>`); if(!forPrint) { el.onmousedown = (e) => { e.stopPropagation(); let startX = e.clientX; let startY = e.clientY; let startLeft = img.x; let startTop = img.y; const onMove = (mv) => { el.style.left = (startLeft + mv.clientX - startX) + 'px'; el.style.top = (startTop + mv.clientY - startY) + 'px'; }; const onUp = (up) => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); img.x = startLeft + up.clientX - startX; img.y = startTop + up.clientY - startY; }; document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp); }; } pageEl.appendChild(el); }); },
        syncDocData: (field, value) => { if(field === 'offerId') State.offerId = value; if(field === 'custId') State.custId = value; document.querySelectorAll('.sync-offer-id').forEach(el => el.innerText = State.offerId); },
        addManualItem: (sIdx) => { 
            const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items.push({
                name:'Neue Position',
                desc:'Beschreibung',
                price:0,
                ek:0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                qty:1,
                unit:'Stk',
                measure:'Stk',
                price_unit_value: 1,
                price_unit_label: 'Stk',
                price_unit_text: '1 Stk',
                kind: 'article',
                status: 'normal',
                subItems:[]
            });
            App.renderQuotePage(); 
        },            

        addLaborItem: async (sIdx) => {
            const defaultMargin = App.getDefaultMargin('labor');

            State.sections[sIdx].items.push({
                name: 'Arbeitsleistung',
                desc: 'Dienstleistung / Montage',
                desc_html: '',
                price: 0,
                ek: 0,
                purchase_price: 0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                qty: 1,
                unit: 'Std',
                measure: 'Std',
                price_unit_value: 1,
                price_unit_label: 'Std',
                price_unit_text: '1 Std',
                kind: 'labor',
                status: 'normal',
                subItems: [],
                active: true,
                showImage: false,
                labor_rows: [
                    {
                        id: Date.now(),
                        qualification_id: null,
                        qualification_name: '',
                        qty: 1,
                        unit: 'Std',
                        ek: 0,
                        margin_percent: defaultMargin,
                        rate: 0,
                        total: 0
                    }
                ]
            });

            App.renderQuotePage();
        },
        
        // Drag Sort Handlers
      dragStartPos: (ev, sIdx, iIdx, subIdx = null) => {
            App.dragState = {
                type: 'pos',
                sIdx,
                iIdx,
                subIdx: (subIdx === null || subIdx === undefined || subIdx === 'null')
                    ? null
                    : Number(subIdx)
            };

            ev.dataTransfer.effectAllowed = 'move';
            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    type: 'pos',
                    sIdx,
                    iIdx,
                    subIdx: (subIdx === null || subIdx === undefined || subIdx === 'null')
                        ? null
                        : Number(subIdx)
                })
            );

            App.startDragMode();
        },
        moveItem: (fromS, fromI, toS, toI) => { if(fromS === toS && fromI === toI) return; const item = State.sections[fromS].items[fromI]; State.sections[fromS].items.splice(fromI, 1); if (fromS === toS && fromI < toI) { toI--; } State.sections[toS].items.splice(toI, 0, item); App.renderQuotePage(); },

        // Updates
        updateQty: (sIdx, iIdx, v, subIdx=null) => { if(subIdx!==null) State.sections[sIdx].items[iIdx].subItems[subIdx].qty=v; else State.sections[sIdx].items[iIdx].qty=v; App.renderQuotePage(); },
        updateItemDetails: (sIdx, iIdx, f, v) => { const it=State.sections[sIdx].items[iIdx]; if(f==='price') it.price=parseFloat(v); else it[f]=v; App.renderQuotePage(); },
        updateSubItemDetails: (sIdx, iIdx, subIdx, f, v) => {
            const s = State.sections[sIdx].items[iIdx].subItems[subIdx];
            if (!s) return;

            if (f === 'price') s.price = parseFloat(v) || 0;
            else if (f === 'qty') s.qty = parseFloat(v) || 0;
            else if (f === 'unit') s.unit = v;
            else if (f === 'name') s.name = v;
            else if (f === 'desc') s.desc = v;

            App.renderQuotePage();
        },                    
        updateSectionBenefit: (sIdx, f, v) => { if(f==='value') State.sections[sIdx].benefit.value=parseFloat(v)||0; else State.sections[sIdx].benefit.type=v; App.renderQuotePage(); },
        updateSectionMeta: (sIdx, f, v) => { State.sections[sIdx][f]=v; App.renderCalculationSidebar(); },
        updateSectionConfig: (sIdx, key, val) => { const conf = State.sections[sIdx].config; if(key === 'type') conf.type = val; else if (key === 'mode') conf.mode = val ? 'pauschal' : 'standard'; else if (key === 'hidePrices') conf.hidePrices = val; else if (key === 'pauschalPrice') conf.pauschalPrice = parseFloat(val) || 0; else if (key === 'marginVal') conf.margin.value = parseFloat(val) || 0; else if (key === 'marginType') conf.margin.type = val; App.renderQuotePage(); },
        updateTaxRate: (v) => { 
            const val = parseFloat(v) || 0;
            State.taxRate = val; 
            State.config.vatMode = val; // Sync
            document.getElementById('lbl-tax-rate').innerText = val; 
            App.renderQuotePage(); 
            // Force re-render of settings panel if open to show active button
            if(App.Tabs.current === 'settings') App.Settings.render();
        },        removeItem: (sIdx, iIdx, subIdx=null) => { if(subIdx!==null) State.sections[sIdx].items[iIdx].subItems.splice(subIdx,1); else State.sections[sIdx].items.splice(iIdx,1); App.renderQuotePage(); },
 
        addSubItem: (sIdx, iIdx) => { 
            const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items[iIdx].subItems.push({
                name:"Position",
                price:0,
                ek:0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                active:true,
                qty:1,
                unit:'Stk',
                measure:'Stk',
                price_unit_value: 1,
                price_unit_label: 'Stk',
                price_unit_text: '1 Stk',
                kind: 'article',
                status: 'normal'
            });
            App.renderQuotePage(); 
        },
        // Settings
        openPosSettings: (sIdx, iIdx, subIdx = null) => { 
            const item = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx]; 
            State.tempPosSettings = { sIdx, iIdx, subIdx }; 
            document.getElementById('setting-qty').value = item.qty || 1;
            const settingUnitEl = document.getElementById('setting-unit');
            settingUnitEl.innerHTML = App.renderUnitOptions(item.measure || item.unit || 'Stk');
            document.getElementById('setting-ek').value = item.ek || 0; 
            document.getElementById('setting-margin').value = item.marginPercent || item.margin || 0; 
            document.getElementById('setting-vk').value = item.price || 0; 
            document.getElementById('setting-pauschal').checked = !!item.isPauschal; 
            document.getElementById('setting-hide-price').checked = !!item.hidePrices; 
            document.getElementById('setting-hide-numbering').checked = !!item.hideNumbering;
            document.getElementById('setting-hide-image').checked = !!item.hideImage;
            document.getElementById('setting-active').checked = item.active !== false; 
            document.getElementById('pos-settings-modal').classList.remove('hidden'); 
        },
        closePosSettings: () => { State.tempPosSettings = null; document.getElementById('pos-settings-modal').classList.add('hidden'); },
        calcPosSettings: (isVk) => { const ek = parseFloat(document.getElementById('setting-ek').value)||0; const m = parseFloat(document.getElementById('setting-margin').value)||0; if(isVk) { const vk=parseFloat(document.getElementById('setting-vk').value)||0; if(ek>0) document.getElementById('setting-margin').value = ((vk-ek)/ek*100).toFixed(2); } else { document.getElementById('setting-vk').value = (ek*(1+m/100)).toFixed(2); } },
        savePosSettings: () => { 
            if(!State.tempPosSettings) return; 
            const {sIdx, iIdx, subIdx} = State.tempPosSettings; 
            const item = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx]; 
            item.qty = parseFloat(document.getElementById('setting-qty').value)||1;
            item.unit = document.getElementById('setting-unit').value || 'Stk';
            item.ek = parseFloat(document.getElementById('setting-ek').value)||0; 
            item.margin = parseFloat(document.getElementById('setting-margin').value)||0; 
            item.marginPercent = item.margin; 
            item.price = parseFloat(document.getElementById('setting-vk').value)||0; 
            item.isPauschal = document.getElementById('setting-pauschal').checked; 
            item.hidePrices = document.getElementById('setting-hide-price').checked; 
            item.hideNumbering = document.getElementById('setting-hide-numbering').checked;
            item.hideImage = document.getElementById('setting-hide-image').checked;
            item.active = document.getElementById('setting-active').checked; 

            // Auto-calculate the parent Set's total sum
           if (subIdx !== null) {
                const mainItem = State.sections[sIdx].items[iIdx];
                let newSetSum = 0;

                mainItem.subItems.forEach(sub => {
                    if (sub.active !== false) {
                        newSetSum += App.calcItemGross(sub);
                    }
                });

                mainItem.price = newSetSum;
            }

            App.renderQuotePage(); 
            App.closePosSettings(); 
        },
        // Modal
        openSetModal: async (setId) => {
            const modal = document.getElementById('set-modal');
            if (!modal) return console.error('set-modal not found');

            // ✅ scope queries inside modal
            const $ = (sel) => modal.querySelector(sel);

            const titleEl = $('#modal-title');
            const descEl  = $('#modal-desc');
            const matBody = $('#modal-materials');
            const labBody = $('#modal-labor');
            const addBtn  = $('#modal-add-btn');

            // ✅ local helpers (no App.*)
            const escapeHtml = (v) => {
                const s = String(v ?? '');
                return s.replace(/[&<>"']/g, (m) => ({
                '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
                }[m]));
            };

            const num = (v, fb = 0) => {
                const n = Number(v);
                return Number.isFinite(n) ? n : fb;
            };

            const money = (v) =>
                num(v).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const showLoading = () => {
                modal.classList.remove('hidden');
                if (titleEl) titleEl.textContent = 'Lade…';
                if (descEl)  descEl.textContent  = '';
                if (matBody) matBody.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="2">Lade Komponenten…</td></tr>`;
                if (labBody) labBody.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="3">Lade Dienstleistungen…</td></tr>`;
                if (addBtn) addBtn.onclick = null;
            };

            // ✅ Normalize multiple backend shapes into { name, description, comps, labor }
            const normalize = (json) => {
                const data = json?.data ?? json ?? {};

                const setName = (data?.name || data?.title || `Set #${setId}`).toString();
                const setDesc = (data?.description || data?.description_text || data?.desc || '').toString();

                let comps = Array.isArray(data?.components) ? data.components : [];
                let labor = Array.isArray(data?.labor) ? data.labor : [];

                // Alternative: items:[{type:'component'|'labor', ...}]
                if ((!comps.length && !labor.length) && Array.isArray(data?.items)) {
                const items = data.items;

                comps = items
                    .filter(x => String(x?.type ?? '').toLowerCase() === 'component')
                    .map(x => ({
                    name: x?.name,
                    qty: x?.qty ?? 1,
                    unit: x?.unit ?? 'Stk',
                    unit_price: x?.unit_price ?? x?.unitPrice ?? x?.price ?? 0,
                    total: x?.total ?? null,
                    children: Array.isArray(x?.children) ? x.children : [],
                    }));

                labor = items
                    .filter(x => String(x?.type ?? '').toLowerCase() === 'labor')
                    .map(x => ({
                    name: x?.name || x?.qualification_name || 'Dienstleistung',
                    hours: x?.hours ?? x?.qty ?? 1,
                    hourly_rate: x?.hourly_rate ?? x?.qualification_price ?? x?.price ?? 0,
                    total: x?.total ?? null,
                    }));
                }

                // Normalize child rows a bit (optional)
                comps = (Array.isArray(comps) ? comps : []).map(c => ({
                name: c?.name ?? 'Komponente',
                qty: c?.qty ?? 1,
                unit: c?.unit ?? 'Stk',
                unit_price: c?.unit_price ?? c?.unitPrice ?? c?.price ?? 0,
                total: c?.total ?? null,
                children: Array.isArray(c?.children) ? c.children.map(ch => ({
                    name: ch?.name ?? 'Unterkomponente',
                    qty: ch?.qty ?? 1,
                    unit: ch?.unit ?? 'Stk',
                    unit_price: ch?.unit_price ?? ch?.unitPrice ?? ch?.price ?? 0,
                    total: ch?.total ?? null,
                })) : [],
                }));

                labor = (Array.isArray(labor) ? labor : []).map(l => ({
                name: l?.name || l?.qualification_name || 'Dienstleistung',
                hours: l?.hours ?? l?.qty ?? 1,
                hourly_rate: l?.hourly_rate ?? l?.qualification_price ?? l?.price ?? 0,
                total: l?.total ?? null,
                }));

                return { setName, setDesc, comps, labor, raw: data };
            };

            const renderComponents = (comps) => {
                if (!matBody) return;

                if (!Array.isArray(comps) || comps.length === 0) {
                matBody.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="2">Keine Komponenten</td></tr>`;
                return;
                }

                const rows = [];
                comps.forEach(c => {
                const qty = num(c?.qty, 1);
                const up  = num(c?.unit_price, 0);
                const tot = num(c?.total, up * qty);

                rows.push(`
                    <tr>
                    <td class="px-4 py-3">
                        <div class="font-bold text-slate-800 text-sm">${escapeHtml(c?.name || 'Komponente')}</div>
                        <div class="text-xs text-slate-400">${escapeHtml(c?.unit || 'Stk')} • ${escapeHtml(qty)}</div>
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-sm">${money(tot)} €</td>
                    </tr>
                `);

                const children = Array.isArray(c?.children) ? c.children : [];
                children.forEach(ch => {
                    const q2 = num(ch?.qty, 1);
                    const p2 = num(ch?.unit_price, 0);
                    const t2 = num(ch?.total, p2 * q2);

                    rows.push(`
                    <tr class="bg-slate-50/50">
                        <td class="px-4 py-3">
                        <div class="font-semibold text-slate-700 text-sm">↳ ${escapeHtml(ch?.name || 'Unterkomponente')}</div>
                        <div class="text-xs text-slate-400">${escapeHtml(ch?.unit || 'Stk')} • ${escapeHtml(q2)}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-sm">${money(t2)} €</td>
                    </tr>
                    `);
                });
                });

                matBody.innerHTML = rows.join('');
            };

            const renderLabor = (labor) => {
                if (!labBody) return;

                if (!Array.isArray(labor) || labor.length === 0) {
                    labBody.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="4">Keine Dienstleistungen</td></tr>`;
                    return;
                }

                labBody.innerHTML = labor.map(l => {
                    const hrs  = num(l?.hours, 1);
                    const rate = num(l?.rate ?? l?.hourly_rate, 0);
                    const tot  = num(l?.total, hrs * rate);

                    return `
                        <tr>
                            <td class="px-4 py-3 font-bold text-slate-800 text-sm">${escapeHtml(l?.name || 'Dienstleistung')}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">${escapeHtml(l?.qualification_name || '-')}</td>
                            <td class="px-4 py-3 text-center font-mono text-sm">${hrs}</td>
                            <td class="px-4 py-3 text-right font-mono text-sm">${money(tot)} €</td>
                        </tr>
                    `;
                }).join('');
            };

            showLoading();

            try {
                const url = new URL(`${API_BASE}/master-sets/${setId}`, window.location.origin);
                url.searchParams.set('context', 'angebot');

                const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                const { setName, setDesc, comps, labor, raw } = normalize(json);

                // Optional debug
                console.log('SET MODAL DATA', raw);
                console.log('components?', raw?.components);
                console.log('labor?', raw?.labor);

                if (titleEl) titleEl.textContent = setName;
                if (descEl)  descEl.textContent  = setDesc;

                renderComponents(comps);
                renderLabor(labor);

                if (addBtn) {
                addBtn.onclick = async () => {
                    try {
                    const sIdx = 0;
                    // ⛳ expects your own existing handler (NOT App.*). Replace as needed:
                   if (typeof App.handleItemAdd === 'function') {
                        await App.handleItemAdd(sIdx, String(setId), 'master_set');
                    }
                    // close modal (local)
                    modal.classList.add('hidden');
                    } catch (err) {
                    console.error('Add master_set failed', err);
                    }
                };
                }
            } catch (e) {
                console.error('openSetModal failed', e);
                if (titleEl) titleEl.textContent = `Set #${setId}`;
                if (descEl)  descEl.textContent  = '';
                if (matBody) matBody.innerHTML = `<tr><td class="px-4 py-3 text-red-500 text-sm" colspan="2">Fehler beim Laden</td></tr>`;
                if (labBody) labBody.innerHTML = `<tr><td class="px-4 py-3 text-red-500 text-sm" colspan="3">Fehler beim Laden</td></tr>`;
            }
            },


            closeModal: () => {
            document.getElementById('set-modal')?.classList.add('hidden');
            },
 
         save: () => alert("Angebot gespeichert (Not implemented in this demo)"),

        // Badges
        handleImageClick: (sIdx, iIdx, subIdx = null) => { 
            App.editingImage = { sIdx, iIdx, subIdx }; 
            document.getElementById('img-upload-input').click(); 
        },
        handleBadgeClick: (sIdx, iIdx) => { State.editingBadge = { sIdx, iIdx, pos: 'tl', type: '', text: '' }; document.getElementById('badge-modal').classList.remove('hidden'); },
        closeBadgeModal: () => document.getElementById('badge-modal').classList.add('hidden'),
        setBadgePos: (pos) => { if(State.editingBadge) State.editingBadge.pos = pos; },
        saveBadgeConfig: () => { if(!State.editingBadge) return; const { sIdx, iIdx, pos, tempImg } = State.editingBadge; const val = document.getElementById('badge-type-select').value; let badgeObj = null; if(val === 'image' && tempImg) badgeObj = { type: 'image', src: tempImg, pos: pos }; else if (val !== '' && val !== 'image') badgeObj = { type: 'text', text: val, pos: pos }; else if (val === 'image' && !tempImg) { document.getElementById('badge-upload-input').click(); return; } State.sections[sIdx].items[iIdx].badge = badgeObj; App.renderQuotePage(); App.closeBadgeModal(); }
    };



    App.getWizardPrefillFromUrl = function () {
        const params = new URLSearchParams(window.location.search);

        return {
            offer_id: params.get('offer_id') ? parseInt(params.get('offer_id'), 10) : null,
            offer_folder_id: params.get('offer_folder_id') ? parseInt(params.get('offer_folder_id'), 10) : null,
            customer_id: params.get('customer_id') ? parseInt(params.get('customer_id'), 10) : null,
            alternative_id: params.get('alternative_id') ? parseInt(params.get('alternative_id'), 10) : null,
            product_id: params.get('product_id') ? parseInt(params.get('product_id'), 10) : null,
        };
    };

    App.applyWizardPrefillFromUrl = async function () {
        const prefill = App.getWizardPrefillFromUrl();
        State.prefill = {
            ...State.prefill,
            ...prefill
        };

        // Save offer/folder IDs into state immediately
        if (prefill.offer_id) {
            State.offerId = String(prefill.offer_id);

            const offerInput = document.getElementById('doc-offer-id');
            if (offerInput) offerInput.value = String(prefill.offer_id);
        }

        if (!prefill.customer_id || State.prefill.autoApplied) return;

        try {
            // 1) load customer by id
            const res = await fetch(`${API_BASE}/wizard/customers/${prefill.customer_id}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                throw new Error(`Customer fetch failed: ${res.status}`);
            }

            const data = await res.json();
            const customer = data.customer || data.data || data;

            if (!customer || !customer.id) {
                throw new Error('Customer not found in response');
            }

            // 2) select customer using your existing flow
            await App.Wizard.selectCustomer(customer, {
                autoSelect: true,
                alternative_id: prefill.alternative_id,
                product_id: prefill.product_id
            });

            State.prefill.autoApplied = true;
        } catch (e) {
            console.error('applyWizardPrefillFromUrl failed', e);
        }
    };

     
   App.Tabs = {
        current: 'list',
        switch(mode) {
            this.current = mode;

            const a4 = document.getElementById('panel-a4');
            const list = document.getElementById('panel-list');
            const settings = document.getElementById('panel-settings');
            const bio = document.getElementById('panel-bio');

            const btnA4 = document.getElementById('main-tab-a4');
            const btnList = document.getElementById('main-tab-list');
            const btnSettings = document.getElementById('main-tab-settings');
            const btnBio = document.getElementById('main-tab-bio');

            if (a4) a4.classList.toggle('hidden', mode !== 'a4');
            if (list) list.classList.toggle('hidden', mode !== 'list');
            if (settings) settings.classList.toggle('hidden', mode !== 'settings');
            if (bio) bio.classList.toggle('hidden', mode !== 'bio');

            const setActive = (btn, isActive) => {
                if (!btn) return;
                if (isActive) {
                    btn.classList.add('bg-white', 'shadow', 'text-[#93c21c]');
                    btn.classList.remove('text-slate-600');
                } else {
                    btn.classList.remove('bg-white', 'shadow', 'text-[#93c21c]');
                    btn.classList.add('text-slate-600');
                }
            };

            setActive(btnA4, mode === 'a4');
            setActive(btnList, mode === 'list');
            setActive(btnSettings, mode === 'settings');
            setActive(btnBio, mode === 'bio');

            if (mode === 'list') App.ListView.render();
            if (mode === 'settings') App.Settings.render();
            if (mode === 'bio') App.Bio.render();
        }
    };

    App.Bio = {
        async loadBiography() {
            const offerId = State.prefill?.offer_id || null;
            const folderId = State.prefill?.offer_folder_id || null;

            if (!offerId && !folderId) {
                State.biographyItems = [];
                return;
            }

            try {
                const url = new URL('/offers/document/biography', window.location.origin);
                if (offerId) url.searchParams.set('offer_id', offerId);
                if (folderId) url.searchParams.set('offer_folder_id', folderId);

                const res = await fetch(url.toString(), {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const json = await res.json();
                State.biographyItems = Array.isArray(json.items) ? json.items : [];
            } catch (e) {
                console.error('loadBiography failed', e);
                State.biographyItems = [];
            }
        },

        async loadOnlineUsers() {
            const offerId = State.prefill?.offer_id || null;
            const folderId = State.prefill?.offer_folder_id || null;

            if (!offerId && !folderId) {
                State.onlineUsers = [];
                return;
            }

            try {
                const url = new URL('/offers/document/presence/users', window.location.origin);
                if (offerId) url.searchParams.set('offer_id', offerId);
                if (folderId) url.searchParams.set('offer_folder_id', folderId);

                const res = await fetch(url.toString(), {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const json = await res.json();
                State.onlineUsers = Array.isArray(json.users) ? json.users : [];
            } catch (e) {
                console.error('loadOnlineUsers failed', e);
                State.onlineUsers = [];
            }
        },

        async render() {
            await this.loadBiography();
            await this.loadOnlineUsers();

            const root = document.getElementById('bio-root');
            if (!root) return;

            const defaultAvatar = '/images/gender/male.png';

            const usersHtml = (State.onlineUsers || []).length
                ? State.onlineUsers.map(u => `
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative shrink-0">
                                <img
                                    src="${App.escapeHtml(u.avatar || '{{ asset('images/gender/male.png') }}')}"
                                    class="w-11 h-11 rounded-full object-cover border border-slate-200"
                                    alt="${App.escapeHtml(u.name || 'Benutzer')}"
                                >
                                <span class="absolute -right-0.5 -bottom-0.5 w-3.5 h-3.5 rounded-full bg-green-500 border-2 border-white animate-pulse"></span>
                            </div>

                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 truncate">${App.escapeHtml(u.name || ('User #' + u.id))}</div>
                                <div class="text-xs text-slate-400">aktiv im Angebot</div>
                            </div>
                        </div>

                        <div class="text-xs font-bold text-green-600">online</div>
                    </div>
                `).join('')
                : `<div class="text-sm text-slate-400">Aktuell ist niemand online.</div>`;

            const bioHtml = (State.biographyItems || []).length
                ? State.biographyItems.map(item => `
                    <div class="relative pl-6 pb-6 border-l-2 border-slate-200">
                        <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-[#93c21c]"></div>
                        <div class="text-sm font-black text-slate-800">${App.escapeHtml(item.title || '')}</div>
                        <div class="text-sm text-slate-600 mt-1">${App.escapeHtml(item.text || '')}</div>
                        <div class="text-xs text-slate-400 mt-1">${App.escapeHtml(item.date || '')}</div>
                    </div>
                `).join('')
                : `<div class="text-sm text-slate-400">Keine Biografie vorhanden.</div>`;

            root.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <div class="text-xs font-black uppercase tracking-wider text-slate-500">Angebotshistorie</div>
                                <div class="text-lg font-black text-slate-800">Biografie</div>
                            </div>
                            <div class="p-5">
                                ${bioHtml}
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <div class="text-xs font-black uppercase tracking-wider text-slate-500">Live</div>
                                <div class="text-lg font-black text-slate-800">Online Bearbeiter</div>
                            </div>
                            <div class="p-5 space-y-3">
                                ${usersHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    };


    App.startPresenceTracking = function () {
        if (State.presenceInterval) {
            clearInterval(State.presenceInterval);
        }

        const offerId = State.prefill?.offer_id || null;
        const folderId = State.prefill?.offer_folder_id || null;

        if (!offerId && !folderId) return;

        const statusUrl = new URL('/offers/document/presence/status', window.location.origin);
        if (offerId) statusUrl.searchParams.set('offer_id', offerId);
        if (folderId) statusUrl.searchParams.set('offer_folder_id', folderId);

        const ping = async () => {
            try {
                const res = await fetch('/offers/document/presence/ping', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        offer_id: offerId,
                        offer_folder_id: folderId
                    })
                });

                const json = await res.json();

                if (json?.users) {
                    State.onlineUsers = json.users;
                    if (App.Tabs.current === 'bio') {
                        App.Bio.render();
                    }
                }

                if (json?.locked_by_other && json?.lock_user) {
                    App.showOfferLockModal(json.lock_user);
                } else {
                    App.hideOfferLockModal();
                }
            } catch (e) {
                console.error('presence ping failed', e);
            }
        };

        const checkBeforeStart = async () => {
            try {
                const res = await fetch(statusUrl.toString(), {
                    method: 'GET',
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });

                const json = await res.json();

                if (json?.users) {
                    State.onlineUsers = json.users;
                }

                if (json?.locked_by_other && json?.lock_user) {
                    App.showOfferLockModal(json.lock_user);
                    return;
                }

                App.hideOfferLockModal();
                await ping();
                State.presenceInterval = setInterval(ping, 10000);
            } catch (e) {
                console.error('presence status failed', e);
            }
        };

        checkBeforeStart();

        window.addEventListener('beforeunload', () => {
            navigator.sendBeacon(
                '/offers/document/presence/leave',
                new Blob(
                    [JSON.stringify({
                        offer_id: offerId,
                        offer_folder_id: folderId
                    })],
                    { type: 'application/json' }
                )
            );
        });
    };
    App.updateItemUnit = function (sIdx, iIdx, value) {
        const it = State.sections?.[sIdx]?.items?.[iIdx];
        if (!it) return;

        const v = (value || 'Stk').toString();
        it.unit = v;
        it.measure = v;

        if (!it.price_unit_label || it.price_unit_label === '' || it.price_unit_label === 'Stk' || it.price_unit_label === it.unit) {
            it.price_unit_label = v;
        }

        App.renderQuotePage();
    };

     App.syncLiveDomToClone = function (sourcePage, clonePage) {
        if (!sourcePage || !clonePage) return clonePage;

        const srcFields = sourcePage.querySelectorAll('input, textarea, select');
        const dstFields = clonePage.querySelectorAll('input, textarea, select');

        srcFields.forEach((src, i) => {
            const dst = dstFields[i];
            if (!dst) return;

            const tag = (src.tagName || '').toLowerCase();

            if (tag === 'input' || tag === 'textarea') {
                dst.value = src.value;
            }

            if (tag === 'select') {
                dst.selectedIndex = src.selectedIndex;
            }

            if (src.type === 'checkbox' || src.type === 'radio') {
                dst.checked = src.checked;
            }
        });

        const srcEditable = sourcePage.querySelectorAll('[contenteditable="true"]');
        const dstEditable = clonePage.querySelectorAll('[contenteditable="true"]');

        srcEditable.forEach((src, i) => {
            if (dstEditable[i]) {
                dstEditable[i].innerHTML = src.innerHTML;
            }
        });

        return clonePage;
    };

    App.buildStaticPageClone = function (sourcePage) {
        if (!sourcePage) return null;

        const clone = sourcePage.cloneNode(true);
        App.syncLiveDomToClone(sourcePage, clone);
        App.makeThumbnailStatic(clone);

        clone.classList.remove('group');
        clone.style.transform = '';
        clone.style.margin = '0 auto 32px auto';
        clone.style.pointerEvents = 'none';

        return clone;
    };

    App.rebuildThumbnails = function () {
        const nav = document.getElementById('nav-pane');
        if (!nav) return;

        nav.innerHTML = '';

        const pages = [
            document.getElementById('page-1'),
            ...Array.from(document.querySelectorAll('#position-pages-container .a4-page.dynamic-page'))
        ].filter(Boolean);

        pages.forEach((pageEl, idx) => {
            const pageNo = idx + 1;

            const wrap = document.createElement('div');
            wrap.className = 'thumb-wrapper';
            wrap.dataset.page = String(pageNo);

            const thumbBox = document.createElement('div');
            thumbBox.className = 'thumb-scale-box';

            const staticClone = App.buildStaticPageClone(pageEl);
            if (staticClone) thumbBox.appendChild(staticClone);

            const lbl = document.createElement('div');
            lbl.className = 'thumb-label';
            lbl.innerText = `Seite ${pageNo}`;

            wrap.appendChild(thumbBox);
            wrap.appendChild(lbl);

            wrap.onclick = () => {
                pageEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                App.setActiveThumb(pageNo);
            };

            nav.appendChild(wrap);
        });

        App.initThumbSortable();
        App.initThumbObserver();
        App.setActiveThumb(State.currentPageNo || 1);
    };

    App.buildPrintPreview = function () {
        const printRoot = document.getElementById('print-preview-content');
        if (!printRoot) return;

        printRoot.innerHTML = '';

        const livePages = [
            document.getElementById('page-1'),
            ...Array.from(document.querySelectorAll('#position-pages-container .a4-page.dynamic-page'))
        ].filter(Boolean);

        livePages.forEach((pageEl) => {
            const staticClone = App.buildStaticPageClone(pageEl);
            if (staticClone) printRoot.appendChild(staticClone);
        });
    };

    App.updateSubItemUnit = function (sIdx, iIdx, subIdx, value) {
        const it = State.sections?.[sIdx]?.items?.[iIdx]?.subItems?.[subIdx];
        if (!it) return;

        const v = (value || 'Stk').toString();
        it.unit = v;
        it.measure = v;

        if (!it.price_unit_label || it.price_unit_label === '' || it.price_unit_label === 'Stk' || it.price_unit_label === it.unit) {
            it.price_unit_label = v;
        }

        App.renderQuotePage();
    };

    App.updateItemPriceUnitLabel = function (sIdx, iIdx, value) {
        const it = State.sections?.[sIdx]?.items?.[iIdx];
        if (!it) return;

        it.price_unit_label = (value || 'Stk').toString();
        it.price_unit_text = `${App.num(it.price_unit_value || 1, 1)} ${it.price_unit_label}`;
        App.renderQuotePage();
    };

    App.updateSubItemPriceUnitLabel = function (sIdx, iIdx, subIdx, value) {
        const it = State.sections?.[sIdx]?.items?.[iIdx]?.subItems?.[subIdx];
        if (!it) return;

        it.price_unit_label = (value || 'Stk').toString();
        it.price_unit_text = `${App.num(it.price_unit_value || 1, 1)} ${it.price_unit_label}`;
        App.renderQuotePage();
    };

    App.getLaborRowSummary = function (it) {
        const rows = Array.isArray(it?.labor_rows) ? it.labor_rows : [];

        let totalHours = 0;
        let totalEk = 0;
        let totalVk = 0;

        rows.forEach((row) => {
            const qty = Number(row?.qty || 0);
            const ek = Number(row?.ek || 0);
            const rate = Number(row?.rate || 0);

            totalHours += qty;
            totalEk += qty * ek;
            totalVk += qty * rate;
        });

        const avgEk = totalHours > 0 ? (totalEk / totalHours) : 0;
        const avgVk = totalHours > 0 ? (totalVk / totalHours) : 0;
        const marginPct = avgVk > 0 ? ((avgVk - avgEk) / avgVk) * 100 : 0;

        return {
            totalHours,
            totalEk,
            totalVk,
            avgEk,
            avgVk,
            marginPct
        };
    };

    // ============================================================
    // ✅ LIST VIEW (FULL REWRITE) — with EK (€), Marge %, DB1 (€)
    // + two dropdowns per position: Artikel/Lohn + Normal/Alt/Opt
    // + subitems supported
    // + keeps your existing App.updateItemDetails / updateSubItemDetails API
    // ============================================================   
    App.ListView = {
        getDefaults() {
            return {
                cols: {
                    pos: true,
                    image: true,
                    title: true,
                    type: true,
                    status: true,
                    qty: true,
                    unit: true,
                    ek: true,
                    margin: true,
                    vk: true,
                    db1: false,
                    total: true,
                    actions: true,
                },
                open: {}
            };
        },

        ensureStore() {
            if (!State.listViewPrefs) {
                State.listViewPrefs = App.ListView.getDefaults();
            }

            if (!State.listViewPrefs.cols) {
                State.listViewPrefs.cols = App.ListView.getDefaults().cols;
            }

            if (!State.listViewPrefs.open) {
                State.listViewPrefs.open = {};
            }
        },

        cols() {
            App.ListView.ensureStore();
            return State.listViewPrefs.cols;
        },

        openStore() {
            App.ListView.ensureStore();
            return State.listViewPrefs.open;
        },

        isOpen(key, fallback = true) {
            const store = App.ListView.openStore();
            return Object.prototype.hasOwnProperty.call(store, key) ? !!store[key] : fallback;
        },

        toggleOpen(key) {
            const store = App.ListView.openStore();
            store[key] = !App.ListView.isOpen(key, true);
            App.ListView.render();
        },

        toggleCol(name) {
            const cols = App.ListView.cols();
            if (!(name in cols)) return;

            cols[name] = !cols[name];
            App.ListView.render();
        },

        resetCols() {
            const open = State.listViewPrefs?.open || {};
            State.listViewPrefs = App.ListView.getDefaults();
            State.listViewPrefs.open = open;
            App.ListView.render();
        },

        activeColCount() {
            return Object.values(App.ListView.cols()).filter(Boolean).length;
        },

        hasSubTree(item) {
            return Array.isArray(item?.subItems) && item.subItems.length > 0;
        },

        hasChildRows(group) {
            return Array.isArray(group?.children) && group.children.length > 0;
        },

        mainOpenKey(sIdx, iIdx) {
            return `main:${sIdx}:${iIdx}`;
        },

        subOpenKey(sIdx, iIdx, parentIndex) {
            return `sub:${sIdx}:${iIdx}:${parentIndex}`;
        },

        getStructuredSubItems(subItems = []) {
            const groups = [];
            let currentParent = null;

            (subItems || []).forEach((sub, originalIndex) => {
                const depth = Number(sub?.depth || 1);

                if (depth <= 1) {
                    currentParent = {
                        parent: sub,
                        parentIndex: originalIndex,
                        children: []
                    };
                    groups.push(currentParent);
                    return;
                }

                if (!currentParent) {
                    currentParent = {
                        parent: { ...sub, depth: 1 },
                        parentIndex: originalIndex,
                        children: []
                    };
                    groups.push(currentParent);
                    return;
                }

                currentParent.children.push({
                    item: sub,
                    index: originalIndex
                });
            });

            return groups;
        },

        handleDropOnSection(ev, sIdx) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('drag-over');

            const id = ev.dataTransfer.getData('text');
            const type = ev.dataTransfer.getData('itemType');

            if (id && type) {
                App.handleItemAdd(sIdx, id, type);
            }

            App.clearDragMode();
        },

        handleDropOnPosition(ev, sIdx, iIdx) {
            ev.preventDefault();
            ev.stopPropagation();

            const id = ev.dataTransfer.getData('text');
            const type = ev.dataTransfer.getData('itemType');

            if (id && type) {
                App.addLibraryItemAsSubPosition(sIdx, iIdx, id, type);
            }

            App.clearDragMode();
        },

        getColDefs() {
            const cols = App.ListView.cols();
            const defs = [];

            if (cols.pos) defs.push({ key: 'pos', label: 'Pos', width: '70px', align: 'center' });
            if (cols.image) defs.push({ key: 'image', label: 'Bild', width: '60px', align: 'center' });
            if (cols.title) defs.push({ key: 'title', label: 'Bezeichnung', width: 'minmax(280px, 1fr)', align: 'left' });
            if (cols.type) defs.push({ key: 'type', label: 'Typ', width: '100px', align: 'center' });
            if (cols.status) defs.push({ key: 'status', label: 'Status', width: '110px', align: 'center' });
            if (cols.qty) defs.push({ key: 'qty', label: 'Menge', width: '90px', align: 'center' });
            if (cols.unit) defs.push({ key: 'unit', label: 'Einh.', width: '90px', align: 'center' });
            if (cols.ek) defs.push({ key: 'ek', label: 'EK / Einh.', width: '120px', align: 'right' });
            if (cols.margin) defs.push({ key: 'margin', label: 'Marge', width: '100px', align: 'right' });
            if (cols.vk) defs.push({ key: 'vk', label: 'VK / Einh.', width: '120px', align: 'right' });
            if (cols.db1) defs.push({ key: 'db1', label: 'DB1', width: '100px', align: 'right' });
            if (cols.total) defs.push({ key: 'total', label: 'Gesamt', width: '120px', align: 'right' });
            if (cols.actions) defs.push({ key: 'actions', label: 'Aktion', width: '100px', align: 'right' });

            return defs;
        },

        toolbarHtml() {
            const cols = App.ListView.cols();
            const checked = (name) => cols[name] ? 'checked' : '';

            const pickerItem = (label, key) => `
                <label class="list-colpicker-item">
                    <span>${label}</span>
                    <input type="checkbox" ${checked(key)} onchange="App.ListView.toggleCol('${key}')">
                </label>
            `;

            return `
                <div class="list-toolbar">
                    <div>
                        <div class="list-toolbar-title">Positionsübersicht</div>
                        <div class="text-xs text-slate-500">Listenansicht</div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button class="list-mini-btn" onclick="App.addSection()">
                            <i class="fa-solid fa-folder-plus"></i> Sektion
                        </button>

                        <button class="list-mini-btn" onclick="App.addPositionQuick()">
                            <i class="fa-solid fa-plus"></i> Position
                        </button>

                        <details class="list-colpicker">
                            <summary class="list-mini-btn">
                                <i class="fa-solid fa-table-columns"></i> Spalten
                            </summary>

                            <div class="list-colpicker-menu" style="width:240px; max-height:400px; overflow-y:auto;">
                                ${pickerItem('Pos', 'pos')}
                                ${pickerItem('Bild', 'image')}
                                ${pickerItem('Bezeichnung', 'title')}
                                ${pickerItem('Typ', 'type')}
                                ${pickerItem('Status', 'status')}
                                ${pickerItem('Menge', 'qty')}
                                ${pickerItem('Einheit', 'unit')}
                                ${pickerItem('EK / Einh.', 'ek')}
                                ${pickerItem('Marge', 'margin')}
                                ${pickerItem('VK / Einh.', 'vk')}
                                ${pickerItem('DB1', 'db1')}
                                ${pickerItem('Gesamt', 'total')}
                                ${pickerItem('Aktionen', 'actions')}

                                <div class="pt-2 mt-2 border-t">
                                    <button class="list-mini-btn w-full" onclick="App.ListView.resetCols()">Standard</button>
                                </div>
                            </div>
                        </details>
                    </div>
                </div>
            `;
        },

        sectionHeadHtml(sec, sIdx) {
            const isLocked = !!sec.isLocked || !!sec._virtualSection;
            const isOpen = App.ListView.isOpen(`sec:${sIdx}`, true);

            const secTotal = (sec.items || []).reduce((sum, it) => {
                if (it.active === false) return sum;
                if (it.lineType !== 'standard') return sum;
                return sum + App.calcItemGross(it);
            }, 0);

            return `
                <div class="list-sec-head p-3 flex items-center justify-between border-y border-slate-200 bg-slate-50 mt-4 mb-1 rounded-lg mx-2 shadow-sm">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <button type="button"
                            onclick="App.ListView.toggleOpen('sec:${sIdx}')"
                            class="list-action-btn border-none bg-transparent shadow-none">
                            <i class="fa-solid fa-chevron-right ${isOpen ? 'rotate-90' : ''} transition-transform"></i>
                        </button>

                        <span class="font-black text-slate-400">${sIdx + 1}.</span>

                        <input
                            value="${App.escapeHtml(sec.title || '')}"
                            onchange="App.updateSectionMeta(${sIdx},'title',this.value)"
                            class="bg-transparent outline-none font-black text-slate-800 w-full"
                            ${isLocked ? 'readonly' : ''}
                        >

                        <span class="list-pill list-pill-slate">∑ ${App.money(secTotal)} €</span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="App.toggleSectionLock(${sIdx})" class="list-mini-btn">
                            <i class="fa-solid ${isLocked ? 'fa-lock' : 'fa-unlock'}"></i>
                        </button>

                        ${!isLocked ? `
                            <button onclick="App.addNotePosition(${sIdx})" class="list-mini-btn">
                                <i class="fa-solid fa-note-sticky"></i>
                            </button>

                            <button onclick="App.addManualItem(${sIdx})" class="list-mini-btn">
                                <i class="fa-solid fa-plus"></i>
                            </button>

                            <button onclick="App.removeSection(${sIdx})" class="list-action-btn danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        ` : ``}
                    </div>
                </div>
            `;
        },

        rowHtml(it, sIdx, iIdx, subIdx, level, posStr, defs, gridTemplate, extra = {}) {
            if (!it || it.active === false) return '';

            const isSub = subIdx !== null;
            const isLocked = !!State.sections?.[sIdx]?.isLocked;
            const subArg = isSub ? subIdx : 'null';
            const focusKeyBase = [sIdx, iIdx, subArg, level].join(':');

            const currentKind = it.kind || (it.item_type === 'labor' ? 'labor' : 'article');
            const currentLineType = it.lineType || 'standard';
            const isNote = currentKind === 'note';

            const hasLaborRows = currentKind === 'labor' && Array.isArray(it.labor_rows) && it.labor_rows.length > 0;
            const laborSummary = hasLaborRows ? App.getLaborRowSummary(it) : null;

            const qty = hasLaborRows ? Number(laborSummary.totalHours) : Number(it.qty || 1);
            const vk = hasLaborRows ? Number(laborSummary.avgVk) : Number(it.price || 0);
            const ek = hasLaborRows ? Number(laborSummary.avgEk) : Number(it.purchase_price || it.ek || 0);
            const margin = hasLaborRows ? Number(laborSummary.marginPct) : Number(it.marginPercent ?? it.margin ?? 0);

            const totalVK = hasLaborRows ? Number(laborSummary.totalVk) : App.calcItemGross(it);
            const totalEK = hasLaborRows ? Number(laborSummary.totalEk) : App.calcItemCost(it);
            const db1 = totalVK - totalEK;

            const ctxText = (field) => (
                isSub
                    ? `App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'${field}',this.value)`
                    : `App.updateItemDetails(${sIdx},${iIdx},'${field}',this.value)`
            );

            const ctxCalc = (field) => `App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'${field}',this.value)`;

            const dragAttrs = !isLocked ? `
                ondragover="event.preventDefault(); this.classList.add('drag-over-sort')"
                ondragleave="this.classList.remove('drag-over-sort')"
                ondrop="
                    event.preventDefault();
                    this.classList.remove('drag-over-sort');

                    if (App.isLibraryDrag()) {
                        App.ListView.handleDropOnPosition(event, ${sIdx}, ${iIdx});
                    } else if (App.getDragState()?.type === 'pos') {
                        App.moveDraggedNode(App.dragState, {
                            mode: 'sort-array',
                            sIdx: ${sIdx},
                            iIdx: ${iIdx},
                            subIdx: ${subArg}
                        });
                        App.clearDragMode();
                    }
                "
            ` : '';

            const cellWrap = (html, extraCls = '') => `<div class="mat-grid-cell ${extraCls}">${html}</div>`;

            let cells = '';

            defs.forEach((col) => {
                const alignClass =
                    col.align === 'right'
                        ? 'mat-grid-cell-right'
                        : col.align === 'center'
                            ? 'mat-grid-cell-center'
                            : '';

                switch (col.key) {
                    case 'pos': {
                        cells += cellWrap(`
                            <div class="flex items-center gap-2">
                                ${!isLocked && !isSub ? `
                                    <span class="drag-handle" draggable="true" ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx}, ${subArg})">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </span>
                                ` : ''}

                                ${isSub && !isLocked ? `
                                    <span class="drag-handle !w-5 !h-5 !text-[10px]" draggable="true" ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx}, ${subArg})">
                                        <i class="fa-solid fa-grip-lines"></i>
                                    </span>
                                ` : ''}

                                <span class="font-mono text-[11px] font-black text-slate-500">
                                    ${isNote ? '<i class="fa-solid fa-info-circle text-blue-400"></i>' : posStr}
                                </span>
                            </div>
                        `, alignClass);
                        break;
                    }

                    case 'image': {
                        cells += cellWrap(
                            isNote || it.hideImage || currentKind === 'labor'
                                ? `<div class="w-10 h-10 mx-auto rounded border border-slate-100 bg-slate-50"></div>`
                                : `
                                    <div class="w-10 h-10 mx-auto rounded overflow-hidden bg-white border border-slate-200 cursor-pointer"
                                        onclick="App.handleImageClick(${sIdx},${iIdx},${subArg})">
                                        <img src="${it.img || App.placeholderImg(it.name)}" class="w-full h-full object-cover">
                                    </div>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'title': {
                        const canToggle = !!extra.canToggle;
                        const isExpanded = canToggle ? App.ListView.isOpen(extra.toggleKey, true) : true;

                        cells += cellWrap(`
                            <div class="flex flex-col w-full" style="padding-left:${level * 20}px">
                                <div class="flex items-center gap-2 w-full">
                                    ${canToggle ? `
                                        <button type="button"
                                            onclick="App.ListView.toggleOpen('${extra.toggleKey}')"
                                            class="text-slate-400 hover:text-[#93c21c]">
                                            <i class="fa-solid fa-chevron-right ${isExpanded ? 'rotate-90' : ''} transition-transform"></i>
                                        </button>
                                    ` : ''}

                                    ${level > 0 && !canToggle
                                        ? '<i class="fa-solid fa-level-up fa-rotate-90 text-slate-300 text-[10px]"></i>'
                                        : ''}

                                    <input
                                        data-lv-focus="name:${focusKeyBase}"
                                        value="${App.escapeHtml(it.name)}"
                                        onchange="${ctxText('name')}"
                                        class="mat-ctrl mat-input font-bold ${isNote ? 'text-blue-700' : ''} w-full"
                                        ${isLocked ? 'readonly' : ''}
                                    >
                                </div>

                                <button type="button"
                                    onclick="App.openDescModal(${sIdx},${iIdx},${subArg})"
                                    class="text-[10px] text-[#93c21c] mt-1 text-left hover:underline ml-2 ${level > 0 ? 'ml-6' : ''}"
                                    ${isLocked ? 'disabled' : ''}>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Beschreibung bearbeiten ${it.desc_html || it.desc ? '(Aktiv)' : ''}
                                </button>
                            </div>
                        `, alignClass);
                        break;
                    }

                    case 'type': {
                        cells += cellWrap(
                            isNote
                                ? '-'
                                : `
                                    <select onchange="App.updatePosConfig(${sIdx},${iIdx},${subArg},'kind',this.value)"
                                        class="mat-ctrl mat-input-center w-full"
                                        ${isLocked ? 'disabled' : ''}>
                                        <option value="article" ${currentKind === 'article' ? 'selected' : ''}>Artikel</option>
                                        <option value="labor" ${currentKind === 'labor' ? 'selected' : ''}>Lohn</option>
                                    </select>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'status': {
                        cells += cellWrap(
                            isNote
                                ? '-'
                                : `
                                    <select
                                        onchange="${
                                            isSub
                                                ? `App.updatePosStatus(${sIdx},${iIdx},${subArg},this.value)`
                                                : `App.toggleItemLineType(${sIdx},${iIdx}, this.value === 'normal' ? 'standard' : this.value)`
                                        }"
                                        class="mat-ctrl mat-input-center w-full"
                                        ${isLocked ? 'disabled' : ''}>
                                        <option value="normal" ${currentLineType === 'standard' ? 'selected' : ''}>Standard</option>
                                        <option value="optional" ${currentLineType === 'optional' ? 'selected' : ''}>Optional</option>
                                        <option value="alternative" ${currentLineType === 'alternative' ? 'selected' : ''}>Alternativ</option>
                                    </select>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'qty': {
                        cells += cellWrap(
                            isNote || hasLaborRows
                                ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-center font-bold text-slate-500 w-full">${hasLaborRows ? qty : '-'}</div>`
                                : `
                                    <input
                                        data-lv-focus="qty:${focusKeyBase}"
                                        type="number"
                                        step="0.01"
                                        value="${qty}"
                                        onchange="${ctxText('qty')}"
                                        class="mat-ctrl mat-input-center w-full"
                                        ${isLocked ? 'readonly' : ''}
                                    >
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'unit': {
                        cells += cellWrap(
                            isNote || hasLaborRows
                                ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-center font-bold text-slate-500 w-full">${hasLaborRows ? 'Std' : '-'}</div>`
                                : `
                                    <select
                                        data-lv-focus="unit:${focusKeyBase}"
                                        onchange="${
                                            isSub
                                                ? `App.updateSubItemUnit(${sIdx},${iIdx},${subArg},this.value)`
                                                : `App.updateItemUnit(${sIdx},${iIdx},this.value)`
                                        }"
                                        class="mat-ctrl mat-input-center w-full"
                                        ${isLocked ? 'disabled' : ''}>
                                        ${
                                            currentKind === 'labor'
                                                ? App.renderLaborUnitOptions(it.measure || it.unit || 'Std')
                                                : App.renderUnitOptions(it.measure || it.unit || 'Stk')
                                        }
                                    </select>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'ek': {
                        cells += cellWrap(
                            isNote || hasLaborRows
                                ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-end px-2 font-mono font-bold text-slate-500 w-full">${hasLaborRows ? App.money(ek) : '-'} €</div>`
                                : `
                                    <div class="mat-addon-wrap w-full">
                                        <input
                                            data-lv-focus="ek:${focusKeyBase}"
                                            type="number"
                                            step="0.01"
                                            value="${ek.toFixed(2)}"
                                            onchange="${ctxCalc('ek')}"
                                            class="mat-addon-input text-right"
                                            ${isLocked ? 'readonly' : ''}>
                                        <span class="mat-addon-text">€</span>
                                    </div>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'margin': {
                        cells += cellWrap(
                            isNote || hasLaborRows
                                ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-end px-2 font-mono font-bold text-slate-500 w-full">${hasLaborRows ? margin.toFixed(1) : '-'} %</div>`
                                : `
                                    <div class="mat-addon-wrap w-full">
                                        <input
                                            data-lv-focus="margin:${focusKeyBase}"
                                            type="number"
                                            step="0.1"
                                            value="${margin.toFixed(1)}"
                                            onchange="${ctxCalc('marginPercent')}"
                                            class="mat-addon-input text-right"
                                            ${isLocked ? 'readonly' : ''}>
                                        <span class="mat-addon-text">%</span>
                                    </div>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'vk': {
                        cells += cellWrap(
                            isNote || hasLaborRows
                                ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-end px-2 font-mono font-bold text-slate-500 w-full">${hasLaborRows ? App.money(vk) : '-'} €</div>`
                                : `
                                    <div class="mat-addon-wrap w-full">
                                        <input
                                            data-lv-focus="vk:${focusKeyBase}"
                                            type="number"
                                            step="0.01"
                                            value="${vk.toFixed(2)}"
                                            onchange="${ctxCalc('price')}"
                                            class="mat-addon-input text-right"
                                            ${isLocked ? 'readonly' : ''}>
                                        <span class="mat-addon-text">€</span>
                                    </div>
                                `,
                            alignClass
                        );
                        break;
                    }

                    case 'db1': {
                        cells += cellWrap(
                            isNote
                                ? '-'
                                : `<div class="font-mono text-slate-500 font-bold">${App.money(db1)} €</div>`,
                            alignClass
                        );
                        break;
                    }

                    case 'total': {
                        cells += cellWrap(
                            isNote
                                ? '-'
                                : `<div class="font-mono text-slate-800 font-bold bg-[#f7fee7] px-2 py-1 rounded">${App.money(totalVK)} €</div>`,
                            alignClass
                        );
                        break;
                    }

                    case 'actions': {
                        cells += cellWrap(`
                            <div class="flex items-center gap-1 justify-end w-full">
                                ${!isLocked && !isSub && !isNote ? `
                                    <button onclick="App.addSubItem(${sIdx},${iIdx})" class="mat-btn-icon" title="Unterposition">
                                        <i class="fa-solid fa-level-down-alt"></i>
                                    </button>
                                ` : ''}

                                <button onclick="App.openPosSettings(${sIdx},${iIdx},${subArg})" class="mat-btn-icon" title="Einstellungen">
                                    <i class="fa-solid fa-sliders"></i>
                                </button>

                                ${!isLocked ? `
                                    <button onclick="App.removeItem(${sIdx},${iIdx},${subArg})" class="mat-btn-icon text-red-500" title="Löschen">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                        `, alignClass);
                        break;
                    }
                }
            });

            let laborRowsHtml = '';

            if (hasLaborRows && !isLocked) {
                laborRowsHtml = `
                    <div class="p-4 bg-slate-50/80 shadow-inner border-t border-slate-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-xs font-black uppercase tracking-wider text-slate-600">
                                <i class="fa-solid fa-users text-brand-primary"></i> Arbeitsleistung Details
                            </div>

                            <button type="button"
                                onclick="App.addLaborRow(${sIdx},${iIdx},${subArg})"
                                class="list-mini-btn !py-1 !px-3 text-[#93c21c]">
                                <i class="fa-solid fa-plus"></i> Zeile hinzufügen
                            </button>
                        </div>

                        <table class="w-full text-xs bg-white border border-slate-200 rounded-lg shadow-sm">
                            <thead class="bg-slate-100 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left font-bold text-slate-600">Person / Qualifikation</th>
                                    <th class="px-3 py-2 text-center font-bold text-slate-600 w-24">Menge</th>
                                    <th class="px-3 py-2 text-center font-bold text-slate-600 w-28">Einheit</th>
                                    <th class="px-3 py-2 text-right font-bold text-slate-600 w-28">EK-Preis</th>
                                    <th class="px-3 py-2 text-right font-bold text-slate-600 w-24">Marge %</th>
                                    <th class="px-3 py-2 text-right font-bold text-slate-600 w-28">Verrechnungssatz</th>
                                    <th class="px-3 py-2 text-right font-bold text-slate-600 w-32">Gesamt</th>
                                    <th class="px-3 py-2 text-right w-12"></th>
                                </tr>
                            </thead>

                            <tbody>
                                ${(it.labor_rows || []).map((row, rowIdx) => `
                                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                                        <td class="p-2">
                                            <select
                                                class="mat-ctrl w-full text-xs h-8 min-h-[32px]"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'qualification_id',this.value)">
                                                ${App.renderLaborOptionOptions(row.qualification_id, row.qualification_name)}
                                            </select>
                                        </td>

                                        <td class="p-2">
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="mat-ctrl mat-input-center w-full text-xs h-8 min-h-[32px]"
                                                value="${Number(row.qty || 0).toFixed(2)}"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'qty',this.value)">
                                        </td>

                                        <td class="p-2">
                                            <select
                                                class="mat-ctrl mat-input-center w-full text-xs h-8 min-h-[32px]"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'unit',this.value)">
                                                ${App.renderLaborUnitOptions(row.unit || 'Std')}
                                            </select>
                                        </td>

                                        <td class="p-2">
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="mat-ctrl mat-input-right w-full text-xs h-8 min-h-[32px]"
                                                value="${Number(row.ek || 0).toFixed(2)}"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'ek',this.value)">
                                        </td>

                                        <td class="p-2">
                                            <input
                                                type="number"
                                                step="0.1"
                                                class="mat-ctrl mat-input-right w-full text-xs h-8 min-h-[32px]"
                                                value="${Number(row.margin_percent ?? App.getDefaultMargin('labor')).toFixed(1)}"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'margin_percent',this.value)">
                                        </td>

                                        <td class="p-2">
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="mat-ctrl mat-input-right w-full text-xs h-8 min-h-[32px]"
                                                value="${Number(row.rate || 0).toFixed(2)}"
                                                onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'rate',this.value)">
                                        </td>

                                        <td class="p-2 text-right font-mono font-bold text-slate-800">
                                            ${App.money(Number(row.qty || 0) * Number(row.rate || 0))} €
                                        </td>

                                        <td class="p-2 text-right">
                                            <button
                                                type="button"
                                                onclick="App.removeLaborRow(${sIdx},${iIdx},${subArg},${rowIdx})"
                                                class="mat-btn-icon text-red-500 h-8 w-8 min-h-[32px]">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            return `
                <div id="lv-row-${sIdx}-${iIdx}-${subArg}" class="mat-data-row ${isSub ? 'is-sub-row' : 'is-main-row'}" ${dragAttrs}>
                    <div class="mat-data-grid" style="display:grid; grid-template-columns:${gridTemplate};">
                        ${cells}
                    </div>
                    ${laborRowsHtml}
                </div>
            `;
        },

        renderSubTree(it, sIdx, iIdx, defs, gridTemplate) {
            const groups = App.ListView.getStructuredSubItems(it.subItems || []);
            if (!groups.length) return '';

            let html = '';

            groups.forEach((group) => {
                const hasChildren = App.ListView.hasChildRows(group);
                const subKey = App.ListView.subOpenKey(sIdx, iIdx, group.parentIndex);
                const posStr = `${sIdx + 1}.${iIdx + 1}.${group.parentIndex + 1}`;

                html += App.ListView.rowHtml(
                    group.parent,
                    sIdx,
                    iIdx,
                    group.parentIndex,
                    1,
                    posStr,
                    defs,
                    gridTemplate,
                    {
                        canToggle: hasChildren,
                        toggleKey: subKey
                    }
                );

                if (!hasChildren || App.ListView.isOpen(subKey, true)) {
                    (group.children || []).forEach((child) => {
                        const childPosStr = `${posStr}.${child.index + 1}`;
                        html += App.ListView.rowHtml(
                            child.item,
                            sIdx,
                            iIdx,
                            child.index,
                            2,
                            childPosStr,
                            defs,
                            gridTemplate
                        );
                    });
                }
            });

            return html;
        },

        render() {
            const root = document.getElementById('listview-root');
            if (!root) return;

            const uiState = App.captureListUiState();

            App.ListView.ensureStore();

            const defs = App.ListView.getColDefs();
            const gridTemplate = defs.map(d => d.width).join(' ');

            let html = `
                <div class="material-table-shell">
                    ${App.ListView.toolbarHtml()}
                    ${typeof App.ListView.analyticsHtml === 'function' ? App.ListView.analyticsHtml() : ''}

                    <div class="material-grid-scroll">
                        <div class="material-sticky-head">
                            <div class="mat-head-row" style="display:grid; grid-template-columns:${gridTemplate};">
                                ${defs.map(c => `
                                    <div class="mat-head-cell ${
                                        c.align === 'right'
                                            ? 'justify-end text-right'
                                            : c.align === 'center'
                                                ? 'justify-center text-center'
                                                : ''
                                    }">${c.label}</div>
                                `).join('')}
                            </div>
                        </div>

                        <div id="material-main-body" class="flex flex-col min-w-max">
            `;

            const sections = (App.getRenderableSections() || []).filter(sec => sec && !sec._pageBreak);

            if (!sections.length) {
                html += `<div class="p-10 text-center text-slate-400 font-bold">Keine Sektionen vorhanden.</div>`;
            } else {
                sections.forEach((sec, sIdx) => {
                    html += App.ListView.sectionHeadHtml(sec, sIdx);

                    if (!App.ListView.isOpen(`sec:${sIdx}`, true)) return;

                    (sec.items || []).forEach((it, iIdx) => {
                        if (it.active === false) return;

                        const hasTree = App.ListView.hasSubTree(it);
                        const mainKey = App.ListView.mainOpenKey(sIdx, iIdx);
                        const posStr = `${sIdx + 1}.${iIdx + 1}`;

                        html += App.ListView.rowHtml(
                            it,
                            sIdx,
                            iIdx,
                            null,
                            0,
                            posStr,
                            defs,
                            gridTemplate,
                            {
                                canToggle: hasTree,
                                toggleKey: mainKey
                            }
                        );

                        if (!sec.isLocked) {
                            html += `
                                <div class="mx-4 mb-2 px-3 py-2 text-[10px] text-slate-400 border border-dashed border-slate-200 rounded bg-slate-50 text-center transition-colors"
                                    ondragover="event.preventDefault(); this.classList.add('drag-over-sub')"
                                    ondragleave="this.classList.remove('drag-over-sub')"
                                    ondrop="
                                        event.preventDefault();
                                        this.classList.remove('drag-over-sub');

                                        if (App.getDragState()?.type === 'pos') {
                                            App.moveDraggedNode(
                                                App.dragState,
                                                { mode: 'to-sub', sIdx: ${sIdx}, iIdx: ${iIdx}, depth: 1 }
                                            );
                                            App.clearDragMode();
                                        } else if (App.isLibraryDrag()) {
                                            App.ListView.handleDropOnPosition(event, ${sIdx}, ${iIdx});
                                        }
                                    ">
                                    <i class='fa-solid fa-level-down-alt mr-1'></i>
                                    Hier ablegen, um als Unterposition hinzuzufügen
                                </div>
                            `;
                        }

                        if (!hasTree || App.ListView.isOpen(mainKey, true)) {
                            html += App.ListView.renderSubTree(it, sIdx, iIdx, defs, gridTemplate);
                        }
                    });

                    if (!sec.isLocked) {
                        html += `
                            <div class="list-section-drop"
                                ondragover="event.preventDefault(); this.classList.add('drag-over')"
                                ondragleave="this.classList.remove('drag-over')"
                                ondrop="
                                    event.preventDefault();
                                    this.classList.remove('drag-over');

                                    if (App.isLibraryDrag()) {
                                        App.ListView.handleDropOnSection(event, ${sIdx});
                                        return;
                                    }

                                    if (App.getDragState()?.type === 'pos') {
                                        App.moveDraggedNode(
                                            App.dragState,
                                            { mode: 'to-main', sIdx: ${sIdx}, iIdx: (State.sections[${sIdx}]?.items?.length || 0) }
                                        );
                                        App.clearDragMode();
                                    }
                                ">
                                Produkte / Sets hier ablegen oder Position hier als Hauptposition einfügen
                            </div>
                        `;
                    }
                });
            }

            html += `
                        </div>
                    </div>
                </div>
            `;

            root.innerHTML = html;

            if (typeof App.renderListSectionSummary === 'function') {
                App.renderListSectionSummary();
            }

            App.restoreListUiState(uiState);
        }
    };


    App.showOfferLockModal = function(user) {
        const modal = document.getElementById('offer-lock-modal');
        if (!modal) return;

        const name = user?.name || 'Ein anderer Benutzer';
        const avatar = user?.avatar || '{{ asset('images/gender/male.png') }}';

        const avatarEl = document.getElementById('offer-lock-user-avatar');
        const nameEl = document.getElementById('offer-lock-user-name');
        const inlineNameEl = document.getElementById('offer-lock-inline-name');

        if (avatarEl) avatarEl.src = avatar;
        if (nameEl) nameEl.textContent = name;
        if (inlineNameEl) inlineNameEl.textContent = name;

        modal.classList.remove('hidden');
    };

    App.hideOfferLockModal = function() {
        const modal = document.getElementById('offer-lock-modal');
        if (modal) modal.classList.add('hidden');
    };


    App.loadSavedDocumentIfAvailable = async function () {
        const offerId = State.prefill?.offer_id || null;
        const folderId = State.prefill?.offer_folder_id || null;

        if (!offerId && !folderId) return;

        try {
            const url = new URL('/offers/document/load', window.location.origin);
            if (offerId) url.searchParams.set('offer_id', offerId);
            if (folderId) url.searchParams.set('offer_folder_id', folderId);

            const res = await fetch(url.toString(), {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const json = await res.json();

            if (!json.success || !json.found || !json.data) {
                return;
            }

            const d = json.data;

            State.loadedSavedDetail = true;

            State.sections = Array.isArray(d.sections) ? d.sections : [];
            State.placedImages = Array.isArray(d.placed_images) ? d.placed_images : [];

            State.brandColor = d.brand_color || '#93c21c';
            State.brandMode = d.brand_mode || 'text';
            State.brandLogoUrl = d.brand_logo_url || '';
            State.companyName = d.company_name || 'SOLAR ASPEKT';
            State.taxRate = Number(d.tax_rate || 19);

            State.coverTextHtml = d.cover_text_html || d.cover_text || '';

            if (d.offer_id) {
                State.offerId = String(d.offer_id);
                const offerInput = document.getElementById('doc-offer-id');
                if (offerInput) offerInput.value = String(d.offer_id);
            }

            if (d.offer?.customer) {
                State.customer = d.offer.customer;
                State.custId = d.offer.customer.customer_no || '-';

                const nameEl = document.getElementById('doc-cust-name');
                const addrEl = document.getElementById('doc-cust-addr');
                const custIdEl = document.getElementById('doc-cust-id');

                if (nameEl) nameEl.innerText = d.offer.customer.display_name || d.offer.customer.name || '';
                if (addrEl) {
                    addrEl.innerHTML = `${d.offer.customer.street || ''}<br>${d.offer.customer.postcode || ''} ${d.offer.customer.city || ''}`;
                }
                if (custIdEl) custIdEl.value = d.offer.customer.customer_no || '-';
            }

            const coverEl = document.getElementById('doc-cover-text');
            if (coverEl && State.coverTextHtml) {
                coverEl.innerHTML = State.coverTextHtml;
            }

            const colorInput = document.getElementById('wiz-brand-color');
            const nameInput = document.getElementById('wiz-brand-name');
            const logoSelect = document.getElementById('wiz-brand-logo');

            if (colorInput) colorInput.value = State.brandColor;
            if (nameInput) nameInput.value = State.companyName;
            if (logoSelect && State.brandLogoUrl) logoSelect.value = State.brandLogoUrl;

            const modeRadio = document.querySelector(`input[name="wiz-brand-mode"][value="${State.brandMode}"]`);
            if (modeRadio) modeRadio.checked = true;

            App.updateBranding();
            App.startQuote();
            App.startPresenceTracking();

        } catch (e) {
            console.error('loadSavedDocumentIfAvailable failed', e);
        }
    };


    App.captureListUiState = function () {
        const panel = document.getElementById('panel-list');
        const root = document.getElementById('listview-root');
        const scrollX = root?.querySelector('.list-scroll-x');
        const active = document.activeElement;

        const state = {
            panelScrollTop: panel ? panel.scrollTop : 0,
            panelScrollLeft: panel ? panel.scrollLeft : 0,
            scrollXLeft: scrollX ? scrollX.scrollLeft : 0,
            focusKey: null,
            selectionStart: null,
            selectionEnd: null
        };

        if (active && root && root.contains(active)) {
            state.focusKey = active.getAttribute('data-lv-focus') || null;

            if (
                typeof active.selectionStart === 'number' &&
                typeof active.selectionEnd === 'number'
            ) {
                state.selectionStart = active.selectionStart;
                state.selectionEnd = active.selectionEnd;
            }
        }

        return state;
    };

    App.restoreListUiState = function (state) {
        if (!state) return;

        requestAnimationFrame(() => {
            const panel = document.getElementById('panel-list');
            const root = document.getElementById('listview-root');
            const scrollX = root?.querySelector('.list-scroll-x');

            if (panel) {
                panel.scrollTop = state.panelScrollTop || 0;
                panel.scrollLeft = state.panelScrollLeft || 0;
            }

            if (scrollX) {
                scrollX.scrollLeft = state.scrollXLeft || 0;
            }

            if (state.focusKey) {
                const el = root?.querySelector(`[data-lv-focus="${CSS.escape(state.focusKey)}"]`);
                if (el) {
                    el.focus({ preventScroll: true });

                    if (
                        typeof el.setSelectionRange === 'function' &&
                        state.selectionStart !== null &&
                        state.selectionEnd !== null
                    ) {
                        try {
                            el.setSelectionRange(state.selectionStart, state.selectionEnd);
                        } catch (e) {}
                    }
                }
            }
        });
    };
 
    // ------------------------------------------------------------
    // HELPER METHODS (Must be declared to fix defaults & debugging)
    // ------------------------------------------------------------
    App.getDefaultMargin = function(kind) {
        if (!State.config || !State.config.margins) return 20;
        if (kind === 'labor') return State.config.margins.labor;
        if (kind === 'external') return State.config.margins.external;
        return State.config.margins.material;
    };

   App.updatePosPriceCalc = function(sIdx, iIdx, subIdx, field, val) {
        let target = subIdx !== null
            ? State.sections[sIdx].items[iIdx].subItems[subIdx]
            : State.sections[sIdx].items[iIdx];

        val = parseFloat(val) || 0;
        let ek = parseFloat(target.purchase_price || target.ek) || 0;
        let vk = parseFloat(target.price) || 0;

        console.log(`[DEBUG] updatePosPriceCalc - Field: ${field}, Value: ${val}`);

        if (field === 'ek') {
            target.ek = val;
            target.purchase_price = val;

            let mPct = parseFloat(target.marginPercent) || 0;
            target.marginPercent = mPct;
            target.margin = mPct;
            target.price = App.vkFromEkMargin(val, mPct);
        }
        else if (field === 'marginPercent') {
            target.marginPercent = val;
            target.margin = val;

            target.ek = ek;
            target.purchase_price = ek;
            target.price = App.vkFromEkMargin(ek, val);

            if (val < (State.config.minProfit || 10)) {
                App.toastConfirmShow({
                    title: 'Achtung: Marge zu niedrig!',
                    message: `Die eingegebene Marge von ${val.toFixed(1)}% liegt unter Ihrem definierten Mindestgewinn von ${State.config.minProfit}%.`,
                    okText: 'Verstanden',
                    cancelText: ''
                });
                const cancelBtn = document.getElementById('toast-confirm-cancel');
                if (cancelBtn) cancelBtn.style.display = 'none';
            }
        }
        else if (field === 'price') {
            target.price = val;

            if (ek > 0 && val > 0) {
                target.marginPercent = (1 - (ek / val)) * 100;
                target.margin = target.marginPercent;

                if (target.marginPercent < (State.config.minProfit || 10)) {
                    App.toastConfirmShow({
                        title: 'Achtung: Marge zu niedrig!',
                        message: `Dieser Preis führt zu einer Marge von ${target.marginPercent.toFixed(1)}%, was unter dem Limit von ${State.config.minProfit}% liegt.`,
                        okText: 'Verstanden',
                        cancelText: ''
                    });
                    const cancelBtn = document.getElementById('toast-confirm-cancel');
                    if (cancelBtn) cancelBtn.style.display = 'none';
                }
            } else {
                target.marginPercent = 0;
                target.margin = 0;
            }
        }

        App.renderQuotePage();
    };
    App.Settings = {
        render: () => {
            const root = document.getElementById('settings-root');
            if (!root) return;
            
            const c = State.config;

            root.innerHTML = `
            <div class="max-w-7xl mx-auto mt-4 pt-4 border-t border-slate-200 grid grid-cols-1 md:grid-cols-5 gap-6">
                <div>
                    <h4 class="font-bold text-yellow-500 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-gear"></i> Basis-Faktoren</h4>
                    <div class="space-y-2 text-xs">
                        <div class="bg-slate-800 p-2 rounded border border-slate-600 mb-2">
                            <span class="block mb-1 text-slate-400">Umsatzsteuer-Modus:</span>
                            <div class="flex gap-1">
                                <button onclick="App.Settings.update('vatMode', 0)" class="flex-1 py-1 rounded text-[10px] ${c.vatMode === 0 ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-300'}">0% (PV)</button>
                                <button onclick="App.Settings.update('vatMode', 19)" class="flex-1 py-1 rounded text-[10px] ${c.vatMode === 19 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300'}">19% (Std)</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center"><span>Gemeinkosten</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.overhead}" onchange="App.Settings.update('overhead', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Vertriebs-Provision</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.commission}" onchange="App.Settings.update('commission', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-2 mt-2"><span>Mindestgewinn</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.minProfit}" onchange="App.Settings.update('minProfit', this.value)"> %</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-indigo-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-percent"></i> Standard-Margen</h4>
                    <div class="space-y-2 text-xs">
                        <p class="text-[10px] text-slate-400 mb-2 italic">Standard-Vorgaben für neue Positionen.</p>
                        <div class="flex justify-between items-center"><span>Material</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.margins?.material || 20}" onchange="App.Settings.update('marginMaterial', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Lohn / Montage</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.margins?.labor || 50}" onchange="App.Settings.update('marginLabor', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Fremdleistung</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.margins?.external || 15}" onchange="App.Settings.update('marginExternal', this.value)"> %</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-green-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-truck"></i> Logistik & Baustelle</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center"><span>Lieferanten-Skonto</span>
                            <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-green-400" step="any" value="${c.supplierDiscount}" onchange="App.Settings.update('supplierDiscount', this.value)"> %</div>
                        </div>
                        <div class="border-t border-slate-200 my-1 pt-1"></div>
                        <div class="flex justify-between items-center"><span class="flex items-center gap-1">Fracht/Logistik <input type="checkbox" class="accent-green-500" ${c.logistics?.freight?.active ? 'checked' : ''} onchange="App.Settings.update('freightActive', this.checked)"></span>
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.logistics?.freight?.val || 0}" onchange="App.Settings.update('freightVal', this.value)"> €</div>
                        </div>
                        <div class="flex justify-between items-center"><span class="flex items-center gap-1">Fahrzeugpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.vehicle?.active ? 'checked' : ''} onchange="App.Settings.update('vehicleActive', this.checked)"></span>
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.logistics?.vehicle?.val || 0}" onchange="App.Settings.update('vehicleVal', this.value)"> €</div>
                        </div>
                        <div class="flex justify-between items-center"><span class="flex items-center gap-1">Maschinenpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.machine?.active ? 'checked' : ''} onchange="App.Settings.update('machineActive', this.checked)"></span>
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.logistics?.machine?.val || 0}" onchange="App.Settings.update('machineVal', this.value)"> €</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-red-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i> Risiko & Wagnis</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center"><span>Kalk. Wagnis</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.risk}" onchange="App.Settings.update('risk', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Vorfinanzierung (Zins)</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.finance}" onchange="App.Settings.update('finance', this.value)"> %</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-blue-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-landmark"></i> Steuern & Kunde</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center"><span>Kalk. Ertragssteuer</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.tax}" onchange="App.Settings.update('tax', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-700 mt-1"><span>Kunden-Skonto</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-slate-800" step="any" value="${c.custDiscount}" onchange="App.Settings.update('custDiscount', this.value)"> %</div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        },
        
        update: (key, val) => {
            const c = State.config;
            const v = (val === true || val === false) ? val : parseFloat(val);

            console.log('[DEBUG] Setting Update:', key, 'to', v);

            if (key === 'vatMode') {
                c.vatMode = v;
                State.taxRate = v;

                const taxInput = document.getElementById('global-tax');
                if (taxInput) taxInput.value = v;

                const lbl = document.getElementById('lbl-tax-rate');
                if (lbl) lbl.innerText = v;
            }
            else if(key === 'overhead') c.overhead = v;
            else if(key === 'commission') c.commission = v;
            else if(key === 'minProfit') c.minProfit = v;
            else if (key === 'supplierDiscount') c.supplierDiscount = v;
            else if(key === 'risk') c.risk = v;
            else if(key === 'finance') c.finance = v;
            else if(key === 'tax') c.tax = v;
            else if(key === 'custDiscount') c.custDiscount = v;

            else if(key === 'freightActive') c.logistics.freight.active = v;
            else if(key === 'freightVal') c.logistics.freight.val = v;
            else if(key === 'vehicleActive') c.logistics.vehicle.active = v;
            else if(key === 'vehicleVal') c.logistics.vehicle.val = v;
            else if(key === 'machineActive') c.logistics.machine.active = v;
            else if(key === 'machineVal') c.logistics.machine.val = v;

            else if(key === 'marginMaterial') {
                c.margins.material = v;
                App.applyGlobalMarginUpdate('article', v);
            }
            else if(key === 'marginLabor') {
                c.margins.labor = v;
                App.applyGlobalMarginUpdate('labor', v);
            }
            else if(key === 'marginExternal') {
                c.margins.external = v;
                App.applyGlobalMarginUpdate('external', v);
            }

            App.Settings.render();
            App.renderQuotePage();
            if (App.Tabs.current === 'list') App.ListView.render();
        }
    };

    App.setDragState = function (payload) {
        App.dragState = payload || null;
        State.dragState = payload || null;
    };

    App.getDragState = function () {
        return App.dragState || State.dragState || null;
    };

    App.resetDragState = function () {
        App.dragState = null;
        State.dragState = null;
    };

    App.isPositionDrag = function () {
        const ds = App.getDragState();
        return !!ds && ds.type === 'pos';
    };

    App.isLibraryDrag = function () {
        const ds = App.getDragState();
        return !!ds && ds.type === 'library';
    };
  App.applyGlobalMarginUpdate = function(kind, newMargin) {
    State.sections.forEach((sec, sIdx) => {
        if (!sec || sec.isLocked) return;

        (sec.items || []).forEach((it, iIdx) => {
            if (!it) return;

            if (it.kind === kind && !it.isPauschal) {
                it.marginPercent = newMargin;
                it.margin = newMargin;

                if ((it.purchase_price || it.ek) > 0) {
                    const ek = Number(it.purchase_price || it.ek || 0);
                    it.ek = ek;
                    it.purchase_price = ek;
                    it.price = App.vkFromEkMargin(ek, newMargin);
                }
            }

            if (kind === 'labor' && Array.isArray(it.labor_rows)) {
                it.labor_rows.forEach((row) => {
                    row.margin_percent = newMargin;
                    App.applyLaborRowPricing(row, 'fromMargin');
                });
                App.recalcLaborCarrier(sIdx, iIdx, null);
            }

            (it.subItems || []).forEach((sub, subIdx) => {
                if (!sub) return;

                if (sub.kind === kind && !sub.isPauschal) {
                    sub.marginPercent = newMargin;
                    sub.margin = newMargin;

                    if ((sub.purchase_price || sub.ek) > 0) {
                        const ek = Number(sub.purchase_price || sub.ek || 0);
                        sub.ek = ek;
                        sub.purchase_price = ek;
                        sub.price = App.vkFromEkMargin(ek, newMargin);
                    }
                }

                if (kind === 'labor' && Array.isArray(sub.labor_rows)) {
                    sub.labor_rows.forEach((row) => {
                        row.margin_percent = newMargin;
                        App.applyLaborRowPricing(row, 'fromMargin');
                    });
                    App.recalcLaborCarrier(sIdx, iIdx, subIdx);
                }
            });
        });
    });

    App.renderQuotePage();
};

    App.addPositionQuick = function () {
        let sIdx = State.sections.findIndex(s => s && !s._pageBreak);
        if (sIdx === -1) sIdx = App.addSection();
        
        const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items.push({
                name: 'Neue Position',
                desc: 'Beschreibung',
                desc_html: '',
                price: 0,
                ek: 0,
                purchase_price: 0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                qty: 1,
                unit: 'Stk',
                measure: 'Stk',
                price_unit_value: 1,
                price_unit_label: 'Stk',
                price_unit_text: '1 Stk',
                kind: 'article',
                status: 'normal',
                subItems: []
            });
        App.renderQuotePage(); 
    };

    const _origRenderQuotePage = App.renderQuotePage.bind(App);
    App.renderQuotePage = function (forPrint = false) {
        _origRenderQuotePage(forPrint);
        if (!forPrint && App?.Tabs?.current === 'list') App.ListView.render();
    };

    const _origTabSwitch = App.Tabs.switch.bind(App.Tabs);
    App.Tabs.switch = function (mode) {
        _origTabSwitch(mode);
        if (mode === 'list') App.ListView.render();
    };

    window.addEventListener('DOMContentLoaded', App.init);

    App.money = function (n) {
        const v = Number(n);
        if (!Number.isFinite(v)) return '0,00';
        return v.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

 
    App.computeSectionSummary = function () {
        const sections = [];
        let subtotal = 0;

        (App.getRenderableSections() || []).forEach((sec, sIdx) => {
            if (!sec || sec._pageBreak) return;

            let sectionNet = 0;

            (sec.items || []).forEach((it) => {
                if (!it) return;
                if (it.active === false) return;

                const lineType = it.lineType || (
                    it.status === 'optional' ? 'optional' :
                    it.status === 'alternative' ? 'alternative' :
                    'standard'
                );

                if (lineType !== 'standard') return;
                if ((it.kind || '') === 'note') return;

                sectionNet += App.calcItemGross(it);
            });

            sections.push({
                index: sIdx,
                label: sec.title || `Sektion ${sIdx + 1}`,
                net: sectionNet
            });

            subtotal += sectionNet;
        });

        const vatRate = Number(State.taxRate || 0);
        const vatValue = subtotal * (vatRate / 100);
        const gross = subtotal + vatValue;

        return {
            sections,
            subtotal,
            netTotal: subtotal,
            vatRate,
            vatValue,
            gross
        };
    };

    App.renderSectionSummaryBlock = function (forPrint = false) {
        const sum = App.computeSectionSummary();

        const rows = sum.sections.map(sec => `
            <div class="flex items-start justify-between gap-4 py-1.5">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="font-bold text-slate-500 shrink-0">Abschnitt</div>
                    <div class="font-bold text-slate-800 break-words">${App.escapeHtml(sec.label)}</div>
                </div>
                <div class="font-mono text-right text-slate-800 shrink-0">
                    ${App.money(sec.net)} €
                </div>
            </div>
        `).join('');

        return `
            <div class="mt-8 break-inside-avoid">
                <div class="text-[18px] font-black text-slate-900 underline underline-offset-4 mb-4">
                    Zusammenstellung Abschnitte
                </div>

                <div class="space-y-0 text-[14px]">
                    ${rows || `<div class="text-slate-400 py-2">Keine Abschnitte vorhanden</div>`}
                </div>

                <div class="mt-3 border-t-2 border-slate-800 pt-2 space-y-1 text-[14px]">
                    <div class="flex justify-between gap-4 font-black text-slate-900">
                        <span>Summe Zusammenstellung Abschnitte</span>
                        <span class="font-mono">${App.money(sum.subtotal)} €</span>
                    </div>

                    <div class="flex justify-between gap-4 text-slate-700">
                        <span>Nettogesamtpreis</span>
                        <span class="font-mono">${App.money(sum.netTotal)} €</span>
                    </div>

                    <div class="flex justify-between gap-4 text-slate-700">
                        <span>Umsatzsteuer ${sum.vatRate.toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%</span>
                        <span class="font-mono">${App.money(sum.vatValue)} €</span>
                    </div>

                    <div class="flex justify-between gap-4 font-black text-[16px] text-slate-900 border-t-2 border-slate-900 pt-2 mt-2">
                        <span>Gesamtsumme</span>
                        <span class="font-mono">${App.money(sum.gross)} €</span>
                    </div>
                </div>
            </div>
        `;
    };

    App.renderOfferFooterBlocks = function () {
        if (State.docType !== 'Angebot') return [];

        const base = 'text-[12px] leading-relaxed text-slate-800';

        return [
            `
            <div class="mt-10 ${base}">
                <div class="text-[22px] font-black uppercase mb-3">
                    Beauftragen Sie uns - Wir kümmern uns um den Rest!
                </div>
                <p>
                    Mit Ihrer Entscheidung für eine Wärmepumpe setzen Sie auf eine effiziente,
                    umweltfreundliche und langfristig kostengünstige Heiztechnik. Wir begleiten
                    Sie Schritt für Schritt und übernehmen die komplette Abwicklung – von der
                    technischen Planung über die Installation bis hin zur Förderantragstellung.
                </p>
            </div>
            `,

            `
            <div class="${base}">
                <div class="text-[18px] font-black mb-2">
                    Lehnen Sie sich zurück - wir kümmern uns um alles Wichtige:
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="font-black">1. Angebotsannahme ohne Risiko - Grundlage für die KfW-Antragstellung</div>
                        <div>Sobald Sie uns die schriftliche Zusage erteilen, starten wir gemeinsam die nächsten Schritte.</div>
                    </div>

                    <div>
                        <div class="font-black">2. Auftragsbestätigung & erste Abschlagszahlung (20%)</div>
                        <div>Nach Eingang der Zuschussbewilligung erhalten Sie unsere schriftliche Auftragsbestätigung.</div>
                    </div>

                    <div>
                        <div class="font-black">3. Heizlastberechnung & Anmeldung Netzbetreiber</div>
                        <div>Wir übernehmen die technische Auslegung und die erforderlichen Anmeldungen.</div>
                    </div>
                </div>
            </div>
            `,

            `
            <div class="${base}">
                <div class="space-y-3">
                    <div>
                        <div class="font-black">4. Montagebeginn & zweite Abschlagszahlung (60%)</div>
                        <div>Mit Materiallieferung und Montagebeginn wird die zweite Abschlagsrechnung fällig.</div>
                    </div>

                    <div>
                        <div class="font-black">5. Inbetriebnahme & Einweisung</div>
                        <div>Nach erfolgreicher Montage nehmen wir Ihre Anlage in Betrieb und weisen Sie ein.</div>
                    </div>

                    <div>
                        <div class="font-black">6. Schlussrechnung & fortlaufende Unterstützung</div>
                        <div>Nach Übergabe der Anlage erhalten Sie die Schlussrechnung. Auch danach bleiben wir Ihr Ansprechpartner.</div>
                    </div>
                </div>
            </div>
            `,

            `
            <div class="${base} space-y-5">
                <div>
                    <div class="font-black text-[16px] mb-1">Transparenz & Fairness - Unsere Vereinbarung</div>
                    <p>
                        Für die Umsetzung Ihres Projekts gelten unsere Allgemeinen Geschäftsbedingungen (AGB)
                        in der jeweils aktuellen Fassung.
                    </p>
                </div>

                <div>
                    <div class="font-black text-[16px] mb-1">Partnerschaftlich zum Erfolg</div>
                    <p>
                        Wir legen großen Wert auf eine offene, kooperative und lösungsorientierte Zusammenarbeit.
                    </p>
                </div>

                <div class="pt-3">
                    <p>
                        Dieses Angebot ist freibleibend. Irrtümer und technische Änderungen bleiben vorbehalten.
                    </p>
                </div>

                <div>
                    <p>
                        Ich nehme das Angebot an und bestelle hiermit verbindlich die im Angebot aufgeführten
                        Leistungen und Komponenten.
                    </p>
                </div>

                <div class="font-black">
                    Evtl. sind einige Positionen in Ihrem Angebot als optional aufgeführt.
                    Bitte kennzeichnen Sie diese als gewünscht oder als nicht notwendig. Vielen Dank!
                </div>
            </div>
            `,

            `
            <div class="${base}">
                <div class="pt-10">
                    <div class="border-b-2 border-slate-800 h-8 w-[420px] max-w-full"></div>
                    <div class="mt-1 text-[12px]">Ort, Datum, Unterschrift</div>
                </div>

                <div class="pt-4">
                    <div class="text-[12px]">Bemerkungen:</div>
                    <div class="border-b border-slate-400 h-8"></div>
                    <div class="border-b border-slate-400 h-8"></div>
                </div>
            </div>
            `
        ];
    };

    App.renderListSectionSummary = function () {
        const root = document.getElementById('listview-root');
        if (!root) return;

        const shell = root.querySelector('.material-table-shell');
        if (!shell) return;

        const old = root.querySelector('#list-section-summary-block');
        if (old) old.remove();

        const sum = App.computeSectionSummary();

        const rowHtml = sum.sections.map(sec => `
            <div class="flex items-center justify-between gap-4 py-2 border-b border-slate-100">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="list-pill list-pill-slate">Abschnitt</span>
                    <span class="font-bold text-slate-800">${App.escapeHtml(sec.label)}</span>
                </div>
                <span class="font-mono font-bold text-slate-700">${App.money(sec.net)} €</span>
            </div>
        `).join('');

        const box = document.createElement('div');
        box.id = 'list-section-summary-block';
        box.className = 'p-4 border-t border-slate-200 bg-white';
        box.innerHTML = `
            <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-white">
                    <div class="text-xs font-black text-slate-500 uppercase tracking-wider">Zusammenstellung</div>
                    <div class="text-lg font-black text-slate-800">Abschnitte</div>
                </div>

                <div class="p-4">
                    ${rowHtml || `<div class="text-slate-400">Keine Abschnitte vorhanden</div>`}

                    <div class="mt-4 pt-4 border-t-2 border-slate-800 space-y-2">
                        <div class="flex justify-between font-black text-slate-900">
                            <span>Summe Zusammenstellung Abschnitte</span>
                            <span class="font-mono">${App.money(sum.subtotal)} €</span>
                        </div>
                        <div class="flex justify-between text-slate-700">
                            <span>Nettogesamtpreis</span>
                            <span class="font-mono">${App.money(sum.netTotal)} €</span>
                        </div>
                        <div class="flex justify-between text-slate-700">
                            <span>Umsatzsteuer ${sum.vatRate.toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%</span>
                            <span class="font-mono">${App.money(sum.vatValue)} €</span>
                        </div>
                        <div class="flex justify-between font-black text-lg text-slate-900 border-t-2 border-slate-900 pt-2 mt-2">
                            <span>Gesamtsumme</span>
                            <span class="font-mono">${App.money(sum.gross)} €</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        shell.appendChild(box);
    }; 


    App.clamp = function(n, a, b){
        n = Number(n);
        if (!Number.isFinite(n)) n = 0;
        return Math.max(a, Math.min(b, n));
    };

    App.vkFromEkMargin = function(ek, marginPercent){
        const EK = Number(ek || 0);
        const m = App.clamp(marginPercent ?? 0, 0, 99.9) / 100;
        if (!EK) return 0;
        return EK / (1 - m);
    };

    App.ensureItemCalcDefaults = function(it){
        if (!it) return it;
        if (it.kind !== 'article' && it.kind !== 'labor') it.kind = 'article'; 
        if (!it.status) it.status = 'normal'; 
        if (it.ek == null) it.ek = 0;
        if (it.marginPercent == null) it.marginPercent = 20;

        if (it.kind === 'labor') {
            if (!it.unit) it.unit = 'Std';
        } else {
            if (!it.unit) it.unit = 'Stk';
        }

        const p = Number(it.price || 0);
        const ek = Number(it.ek || 0);
        if ((!p || p <= 0) && ek > 0) {
            it.price = App.vkFromEkMargin(ek, it.marginPercent);
        }

        return it;
    };

    App.Wizard.initObjectSelect2 = () => {
        const el = $('#wiz-object-select');
        if (!el.length) return;

        if (el.hasClass('select2-hidden-accessible')) {
            el.select2('destroy');
        }

        el.select2({
            placeholder: 'Objekt/Produkte auswählen…',
            width: '100%',
            closeOnSelect: false,          
            allowClear: true
        });

        el.off('change.select2_sync').on('change.select2_sync', () => {
            App.Wizard.selectObject();
        });
    };

    App.Wizard.setObjectDisabled = (disabled) => {
        const el = $('#wiz-object-select');
        el.prop('disabled', !!disabled);
        if (el.hasClass('select2-hidden-accessible')) {
            el.trigger('change.select2');
        }
    };

    App.num = (v, d=0) => {
        const n = Number(v);
        return Number.isFinite(n) ? n : d;
    };

    App.getActiveLogisticsItems = function () {
        const cfg = State.config || {};
        const lg = cfg.logistics || {};

        const makeItem = (key, label, val) => ({
            _virtual: true,
            _virtualType: 'logistics',
            _virtualKey: key,

            name: label,
            desc: label,
            desc_html: `<p>${App.escapeHtml(label)}</p>`,

            item_type: 'logistics',
            kind: 'article',
            lineType: 'standard',
            status: 'normal',

            qty: 1,
            unit: 'Pauschal',
            measure: 'Pauschal',
            price_unit_value: 1,
            price_unit_label: 'Pauschal',
            price_unit_text: '1 Pauschal',

            ek: 0,
            purchase_price: 0,
            marginPercent: 100,
            margin: 100,

            price: Number(val || 0),

            active: true,
            hidePrices: false,
            hideImage: true,
            showImage: false,
            hideNumbering: false,
            isPauschal: true,

            img: '',
            subItems: []
        });

        const rows = [];

        if (lg.freight?.active && Number(lg.freight?.val || 0) > 0) {
            rows.push(makeItem('freight', 'Fracht / Logistik', lg.freight.val));
        }

        if (lg.vehicle?.active && Number(lg.vehicle?.val || 0) > 0) {
            rows.push(makeItem('vehicle', 'Fahrzeugpauschale', lg.vehicle.val));
        }

        if (lg.machine?.active && Number(lg.machine?.val || 0) > 0) {
            rows.push(makeItem('machine', 'Maschinenpauschale', lg.machine.val));
        }

        return rows;
    };

    App.hasActiveLogisticsItems = function () {
        return App.getActiveLogisticsItems().length > 0;
    };

    App.getRenderableSections = function () {
        const baseSections = (State.sections || []).filter(sec => sec && !sec._pageBreak);
        const logisticsItems = App.getActiveLogisticsItems();

        if (!logisticsItems.length) return baseSections;

        return [
            ...baseSections,
            {
                _virtualSection: true,
                id: '__logistics__',
                title: 'Logistik & Baustelle',
                description: 'Automatisch aus Einstellungen übernommen',
                config: {
                    mode: 'standard',
                    pauschalPrice: 0,
                    type: 'standard',
                    hidePrices: false,
                    margin: { value: 0, type: 'fixed' }
                },
                items: logisticsItems,
                isLocked: true
            }
        ];
    };


    App.computeQuoteTotals = function () {
        const num = (v, d = 0) => {
            const n = Number(v);
            return Number.isFinite(n) ? n : d;
        };

        const cfg = State.config || {};

        let salesNet = 0;
        let sumEK = 0;
        let sumLaborSales = 0;
        let sumMatSales = 0;
        let totalHours = 0;

        (App.getRenderableSections() || []).forEach(sec => {
            if (!sec || sec._pageBreak) return;

            (sec.items || []).forEach(it => {
                if (!it) return;
                if (it.active === false) return;
                if ((it.status || 'normal') !== 'normal') return;
                if ((it.kind || 'article') === 'note') return;

                const kind = it.kind || (it.item_type === 'labor' ? 'labor' : 'article');
                const qty = it.isPauschal ? 1 : num(it.qty, 1);

                let lineVK = 0;
                let lineEK = 0;

                if (Array.isArray(it.subItems) && it.subItems.length > 0 && !it.isPauschal) {
                    it.subItems.forEach(sub => {
                        if (!sub) return;
                        if (sub.active === false) return;
                        if ((sub.status || 'normal') !== 'normal') return;

                        lineVK += App.calcItemGross(sub);
                        lineEK += App.calcItemCost(sub);

                        const subKind = sub.kind || (sub.item_type === 'labor' ? 'labor' : 'article');
                        if (subKind === 'labor' || sub.unit === 'Std' || sub.unit === 'h') {
                            totalHours += Number(sub.qty || 0);
                        }
                    });
                } else {
                    const vk = num(it.price);
                    const ek = num(it.ek);
                    lineVK = it.isPauschal ? vk : (vk * qty);
                    lineEK = it.isPauschal ? ek : (ek * qty);

                    if (kind === 'labor' || it.unit === 'Std' || it.unit === 'h') {
                        totalHours += qty;
                    }
                }

                salesNet += lineVK;
                sumEK += lineEK;

                if (kind === 'labor' || it.unit === 'Std' || it.unit === 'h') {
                    sumLaborSales += lineVK;
                } else {
                    sumMatSales += lineVK;
                }
            });
        });

        const db1 = salesNet - sumEK;
        const overheadCost = salesNet * (num(cfg.overhead) / 100);
        const commissionCost = salesNet * (num(cfg.commission) / 100);
        const db2 = db1 - overheadCost - commissionCost;

        const supplierDiscountValue = sumEK * (num(cfg.supplierDiscount) / 100);
        const riskCost = sumEK * (num(cfg.risk) / 100);
        const financeCost = sumEK * (num(cfg.finance) / 100);
        const customerDiscountValue = salesNet * (num(cfg.custDiscount) / 100);

        const db3 = db2 + supplierDiscountValue - riskCost - financeCost - customerDiscountValue;

        const incomeTaxValue = Math.max(0, db3 * (num(cfg.tax) / 100));
        const netProfit = db3 - incomeTaxValue;

        const vatRate = num(State.taxRate, 19);
        const vatValue = salesNet * (vatRate / 100);
        const grossTotal = salesNet + vatValue;

        const laborShare = salesNet > 0 ? (sumLaborSales / salesNet) * 100 : 0;
        const matShare = salesNet > 0 ? (sumMatSales / salesNet) * 100 : 0;
        const db1Pct = salesNet > 0 ? (db1 / salesNet) * 100 : 0;
        const db2Pct = salesNet > 0 ? (db2 / salesNet) * 100 : 0;
        const profitPct = salesNet > 0 ? (netProfit / salesNet) * 100 : 0;
        const salesPerHour = totalHours > 0 ? (salesNet / totalHours) : 0;
        const profitPerHour = totalHours > 0 ? (db3 / totalHours) : 0;

        const totalGlobalCosts = overheadCost + commissionCost + riskCost + financeCost + customerDiscountValue - supplierDiscountValue;
        const totalCostFactor = salesNet > 0 ? (totalGlobalCosts / salesNet) : 0;

        return {
            salesNet,
            sumEK,
            sumLaborSales,
            sumMatSales,
            logisticsTotal: App.getActiveLogisticsItems().reduce((s, x) => s + Number(x.price || 0), 0),
            totalHours,
            db1,
            db2,
            db3,
            overheadCost,
            commissionCost,
            supplierDiscountValue,
            riskCost,
            financeCost,
            customerDiscountValue,
            incomeTaxValue,
            netProfit,
            vatRate,
            vatValue,
            grossTotal,
            laborShare,
            matShare,
            db1Pct,
            db2Pct,
            profitPct,
            salesPerHour,
            profitPerHour,
            totalGlobalCosts,
            totalCostFactor
        };
    };


   App.ListView.analyticsHtml = function () {
        const key = 'lv:controlling';
        const open = App.ListView.isOpen(key, false);
        const totals = App.computeQuoteTotals();

        const safe = (n) => {
            const v = Number(n);
            return Number.isFinite(v) ? v : 0;
        };

        const money = (n) => `${App.money(safe(n))}\u00A0€`;
        const pct = (n) => `${safe(n).toFixed(1).replace('.', ',')}\u00A0%`;
        const clamp01 = (x) => Math.max(0, Math.min(1, safe(x)));

        const sales = safe(totals.salesNet);
        const ekSum = safe(totals.sumEK);
        const db1 = safe(totals.db1);
        const db1Pct = safe(totals.db1Pct);
        const db2 = safe(totals.db2);
        const db3 = safe(totals.db3);
        const taxVal = safe(totals.incomeTaxValue);
        const netProfit = safe(totals.netProfit);
        const hours = safe(totals.totalHours);

        const matSales = safe(totals.sumMatSales);
        const laborSales = safe(totals.sumLaborSales);

        const matShare = sales > 0 ? (matSales / sales) * 100 : 0;
        const laborShare = sales > 0 ? (laborSales / sales) * 100 : 0;

        const otherSales = Math.max(0, sales - (matSales + laborSales));
        const otherShare = sales > 0 ? (otherSales / sales) * 100 : 0;

        const target = safe(State?.config?.minProfit || 10);
        const marginTotalPct = sales > 0 ? (db3 / sales) * 100 : 0;
        const marginVsTarget = marginTotalPct - target;

        const maxAbs = Math.max(
            1,
            Math.abs(sales),
            Math.abs(ekSum),
            Math.abs(db1),
            Math.abs(db2),
            Math.abs(netProfit)
        );
        const barHeight = (val) => `${Math.round(clamp01(Math.abs(val) / maxAbs) * 100)}%`;

        const dashA = clamp01(matShare / 100) * 100;
        const dashB = clamp01(laborShare / 100) * 100;
        const dashC = Math.max(0, 100 - dashA - dashB);

        const salesPerHour = safe(totals.salesPerHour);
        const profitPerHour = safe(totals.profitPerHour);

        const profitIsBad = netProfit < 0;
        const db3IsBad = db3 < 0;
        const marginIsBad = marginTotalPct < target;

        const chevronCls = open ? 'rotate-0' : 'rotate-180';

        const gainBoxClass = profitIsBad
            ? 'text-blue-700 bg-blue-50 border border-blue-100'
            : 'text-green-700 bg-green-50 border border-green-100';

        const gainSubClass = profitIsBad ? 'text-blue-400' : 'text-green-400';

        const db2BarClass = db2 < 0 ? 'bg-red-500' : 'bg-slate-300';
        const netBarClass = netProfit < 0 ? 'bg-blue-600' : 'bg-emerald-600';

        const marginBarClass = marginIsBad ? 'bg-red-500' : 'bg-emerald-500';
        const marginTextClass = marginIsBad ? 'text-red-600' : 'text-emerald-700';

        const profitHourTextClass = profitPerHour < 0 ? 'text-red-600' : 'text-green-600';
        const profitHourBarClass = profitPerHour < 0 ? 'bg-red-500' : 'bg-green-500';

        return `
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-100 p-3 border-b border-slate-200 flex justify-between items-center cursor-pointer hover:bg-slate-200 transition-colors"
                    onclick="App.ListView.toggleOpen('${key}')">
                    <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2 uppercase tracking-wide">
                        <i class="fa-solid fa-chart-line w-4 h-4 text-blue-600"></i>
                        Analyse &amp; Controlling
                    </h3>
                    <i class="fa-solid fa-chevron-up w-4 h-4 text-slate-500 transition-transform ${chevronCls}"></i>
                </div>

                <div class="${open ? '' : 'hidden'}">
                    <div class="p-6 space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-chart-pie w-4 h-4 text-blue-600"></i>
                                    Split-Analyse
                                </h5>

                                <div class="mb-3 pb-3 border-b border-slate-100">
                                    <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                                        <span>Material &amp; Sonst.</span>
                                        <span>${money(matSales + otherSales)}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>Anteil am Umsatz: ${pct(matShare + otherShare)}</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                                        <span>Lohn &amp; Montage</span>
                                        <span>${money(laborSales)}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-400">
                                        <span>Anteil am Umsatz: ${pct(laborShare)}</span>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                    <div class="flex justify-between text-xs font-bold text-slate-700 bg-slate-50 p-1 rounded">
                                        <span>DB 1</span>
                                        <div class="text-right">
                                            <div>${money(db1)}</div>
                                            <div class="text-[9px] font-normal text-slate-400">${pct(db1Pct)}</div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-xs font-bold ${gainBoxClass} p-1 rounded">
                                        <span>Gesamtgewinn</span>
                                        <div class="text-right">
                                            <div>${money(netProfit)}</div>
                                            <div class="text-[9px] font-normal ${gainSubClass}">
                                                ${pct(sales > 0 ? (netProfit / sales) * 100 : 0)}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-clock w-4 h-4 text-orange-600"></i>
                                    Stunden-Performance
                                </h5>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-orange-50 p-2 rounded">
                                        <span class="text-xs text-slate-600">Gesamtstunden</span>
                                        <span class="font-mono font-bold">${hours.toFixed(1)} h</span>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                                            Umsatz pro Stunde (Netto)
                                        </div>
                                        <div class="flex justify-between items-baseline">
                                            <span class="text-xs text-slate-400">Ø Satz</span>
                                            <span class="font-bold text-slate-800">${money(salesPerHour)} /h</span>
                                        </div>
                                    </div>

                                    <div class="border-t border-slate-100 pt-2">
                                        <div class="flex justify-between text-xs text-green-600 mb-1 font-medium">
                                            Reingewinn pro Stunde (DB3)
                                        </div>
                                        <div class="flex justify-between items-baseline">
                                            <span class="text-xs text-slate-400">Nach Risiko/Zins</span>
                                            <span class="font-bold ${profitHourTextClass}">
                                                ${money(profitPerHour)} /h
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 ring-1 ring-blue-100">
                                <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-money-bill-wave w-4 h-4 text-green-600"></i>
                                    Finanz-Dashboard
                                </h5>

                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500 text-xs">Umsatz Netto</span>
                                        <span class="font-medium">${money(sales)}</span>
                                    </div>

                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="text-slate-400">./. EK Listenpreis</span>
                                        <span class="text-slate-400">-${money(ekSum)}</span>
                                    </div>

                                    <div class="border-t border-slate-100 my-1 pt-1"></div>

                                    <div class="bg-slate-50 p-2 rounded border border-slate-100 mt-2">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-slate-700 text-xs uppercase">DB 3 (EBIT)</span>
                                            <span class="font-bold font-mono ${db3IsBad ? 'text-red-600' : 'text-green-600'}">
                                                ${money(db3)}
                                            </span>
                                        </div>

                                        <div class="border-t border-slate-200 pt-1 mt-1">
                                            <div class="flex justify-between font-bold text-xs mt-1 text-blue-900">
                                                <span>Netto-Gewinn</span>
                                                <span>${money(netProfit)}</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                                                <span>Ertragssteuer</span>
                                                <span>${money(taxVal)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between shadow-sm">
                                <div class="flex items-center justify-between mb-2">
                                    <h6 class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-trend-up w-3 h-3"></i>
                                        Ertrags-Wasserfall
                                    </h6>
                                    <span class="text-[10px] text-blue-600 font-mono">Umsatz/Profit</span>
                                </div>

                                <div class="flex items-end gap-2 h-20 w-full mt-2">
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300"
                                            title="Umsatz: ${money(sales)}"
                                            style="height:${barHeight(sales)};"></div>
                                        <span class="text-[8px] text-slate-400 uppercase truncate w-full text-center">Umsatz</span>
                                    </div>

                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300"
                                            title="Kosten (EK): ${money(ekSum)}"
                                            style="height:${barHeight(ekSum)};"></div>
                                        <span class="text-[8px] text-slate-400 uppercase truncate w-full text-center">Kosten</span>
                                    </div>

                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300"
                                            title="DB1: ${money(db1)}"
                                            style="height:${barHeight(db1)};"></div>
                                        <span class="text-[8px] text-slate-400 uppercase truncate w-full text-center">DB1</span>
                                    </div>

                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 ${db2BarClass}"
                                            title="DB2: ${money(db2)}"
                                            style="height:${barHeight(db2)};"></div>
                                        <span class="text-[8px] text-slate-400 uppercase truncate w-full text-center">DB2</span>
                                    </div>

                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 ${netBarClass}"
                                            title="Netto: ${money(netProfit)}"
                                            style="height:${barHeight(netProfit)};"></div>
                                        <span class="text-[8px] text-slate-400 uppercase truncate w-full text-center">Netto</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <h6 class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-box-open w-3 h-3"></i>
                                        Umsatz-Mix
                                    </h6>
                                    <i class="fa-solid fa-bolt w-3 h-3 text-yellow-500"></i>
                                </div>

                                <div class="relative w-24 h-24 mx-auto mt-2">
                                    <svg viewBox="0 0 32 32" class="w-full h-full transform -rotate-90">
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#3b82f6" stroke-width="4"
                                            stroke-dasharray="${dashA} 100" stroke-dashoffset="0"></circle>
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#10b981" stroke-width="4"
                                            stroke-dasharray="${dashB} 100" stroke-dashoffset="-${dashA}"></circle>
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f59e0b" stroke-width="4"
                                            stroke-dasharray="${dashC} 100" stroke-dashoffset="-${dashA + dashB}"></circle>
                                    </svg>

                                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                                        <span class="text-[10px] font-bold text-slate-700">${pct(matShare)}</span>
                                        <span class="text-[7px] text-slate-400 uppercase">Material</span>
                                    </div>
                                </div>

                                <div class="mt-3 space-y-1 text-[10px] text-slate-600">
                                    <div class="flex justify-between"><span class="text-slate-500">Material</span><span>${money(matSales)}</span></div>
                                    <div class="flex justify-between"><span class="text-slate-500">Lohn</span><span>${money(laborSales)}</span></div>
                                    <div class="flex justify-between"><span class="text-slate-500">Sonst.</span><span>${money(otherSales)}</span></div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <h6 class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-bullseye w-3 h-3"></i>
                                        Margen-Monitor
                                    </h6>
                                    <div class="w-2 h-2 rounded-full animate-pulse ${marginIsBad ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                                </div>

                                <div class="flex-1 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold text-slate-800">${pct(marginTotalPct)}</div>
                                    <div class="text-[9px] text-slate-400 text-center uppercase tracking-tighter mt-1">
                                        Gesamtmarge vs. ${target.toFixed(0)}% Ziel
                                    </div>

                                    <div class="w-full bg-slate-200 h-1.5 rounded-full mt-4 overflow-hidden">
                                        <div class="h-full transition-all duration-700 ${marginBarClass}"
                                            style="width:${Math.round(clamp01(Math.abs(marginTotalPct) / Math.max(1, target)) * 100)}%;"></div>
                                    </div>

                                    <div class="mt-2 text-[10px] ${marginTextClass} font-bold">
                                        ${marginIsBad ? 'Unter' : 'Über'} Ziel um ${pct(Math.abs(marginVsTarget))}
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex items-center justify-between mb-2">
                                    <h6 class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-chart-column w-3 h-3"></i>
                                        Effizienz-Index
                                    </h6>
                                </div>

                                <div class="space-y-3 mt-1">
                                    <div>
                                        <div class="flex justify-between text-[9px] mb-1">
                                            <span class="text-slate-500 uppercase">Umsatz / h</span>
                                            <span class="font-bold">${money(salesPerHour)}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                            <div class="bg-blue-400 h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-[9px] mb-1">
                                            <span class="text-slate-500 uppercase">Gewinn / h</span>
                                            <span class="font-bold ${profitHourTextClass}">${money(profitPerHour)}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                            <div class="${profitHourBarClass} h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200 text-[10px] text-slate-500">
                                        DB3: <b class="font-mono ${db3IsBad ? 'text-red-600' : 'text-slate-700'}">${money(db3)}</b>
                                        <span class="text-slate-300 mx-1">•</span>
                                        Netto: <b class="font-mono ${profitIsBad ? 'text-blue-600' : 'text-green-600'}">${money(netProfit)}</b>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        `;
    };

  App.getItemNode = function (sIdx, iIdx, subIdx = null) {
    const sec = State.sections?.[Number(sIdx)];
    if (!sec || !Array.isArray(sec.items)) return null;

    const main = sec.items[Number(iIdx)];
    if (!main) return null;

    const isMain = (subIdx === null || subIdx === undefined || subIdx === 'null' || subIdx === '');
    if (isMain) {
        return {
            level: 'main',
            item: main,
            parentArray: sec.items,
            index: Number(iIdx),
            parentItem: null
        };
    }

    if (!Array.isArray(main.subItems)) return null;

    const subIndex = Number(subIdx);
    const sub = main.subItems[subIndex];
    if (!sub) return null;

    const depth = Number(sub.depth || 1);

    return {
        level: depth >= 2 ? 'child' : 'sub',
        item: sub,
        parentArray: main.subItems,
        index: subIndex,
        parentItem: main,
        depth
    };
};

App.removeNodeFromSource = function (from) {
    if (!from || !Array.isArray(from.parentArray)) return [];

    // main row = only itself
    if (from.level === 'main') {
        return from.parentArray.splice(from.index, 1);
    }

    // child row = only itself
    if (from.level === 'child') {
        return from.parentArray.splice(from.index, 1);
    }

    // sub row = itself + following children until next depth 1 row
    if (from.level === 'sub') {
        let end = from.index + 1;

        while (end < from.parentArray.length) {
            const next = from.parentArray[end];
            const nextDepth = Number(next?.depth || 1);
            if (nextDepth <= 1) break;
            end++;
        }

        return from.parentArray.splice(from.index, end - from.index);
    }

    return [];
};
App.insertNodeToTarget = function (targetArray, index, items) {
    if (!Array.isArray(targetArray) || !items) return;

    const block = Array.isArray(items) ? items : [items];
    const safeIndex = Math.max(0, Math.min(index, targetArray.length));

    targetArray.splice(safeIndex, 0, ...block);
};

App.isMainArray = function(arr) {
    return State.sections.some(sec => sec && Array.isArray(sec.items) && sec.items === arr);
};

App.normalizeDraggedBlockForTarget = function (block, targetArray, mode) {
    if (!Array.isArray(block) || !block.length) return [];

    const cloned = block.map(x => ({ ...x }));
    const isMainArray = App.isMainArray(targetArray);

    // ✅ FIX: If targeting the main section array, reset depth to 0
    if (mode === 'to-main' || (mode === 'sort-array' && isMainArray)) {
        cloned.forEach(row => {
            row.depth = 0;
        });
        return cloned;
    }

    // ✅ FIX: If targeting a subItems array, set depth to 1
    if (mode === 'to-sub' || (mode === 'sort-array' && !isMainArray)) {
        cloned.forEach((row, idx) => {
            row.depth = idx === 0 ? 1 : Math.max(2, Number(row.depth || 2));
        });
        return cloned;
    }

    return cloned;
};

App.moveDraggedNode = function (fromRef, toRef) {
    const from = App.getItemNode(fromRef.sIdx, fromRef.iIdx, fromRef.subIdx);
    if (!from) {
        App.renderQuotePage();
        return;
    }

    // prevent dropping a row into itself
    if (
        toRef &&
        Number(fromRef.sIdx) === Number(toRef.sIdx) &&
        Number(fromRef.iIdx) === Number(toRef.iIdx) &&
        String(fromRef.subIdx) === String(toRef.subIdx) &&
        toRef.mode === 'sort-array'
    ) {
        App.renderQuotePage();
        return;
    }

    let target = null;
    if (toRef.mode === 'sort-array') {
        target = App.getItemNode(toRef.sIdx, toRef.iIdx, toRef.subIdx);
        if (!target || !Array.isArray(target.parentArray)) {
            App.renderQuotePage();
            return;
        }
    }

    const sameArray = !!target && from.parentArray === target.parentArray;
    const originalFromIndex = from.index;
    const originalTargetIndex = target ? target.index : null;

    const removedBlock = App.removeNodeFromSource(from);
    if (!removedBlock.length) {
        App.renderQuotePage();
        return;
    }

    // move to main section level
    if (toRef.mode === 'to-main') {
        const sec = State.sections?.[Number(toRef.sIdx)];
        if (!sec || !Array.isArray(sec.items)) {
            App.renderQuotePage();
            return;
        }

        let insertIndex = Number(toRef.iIdx);
        if (!Number.isFinite(insertIndex)) insertIndex = sec.items.length;

        if (from.parentArray === sec.items && originalFromIndex < insertIndex) {
            insertIndex -= removedBlock.length;
        }

        const block = App.normalizeDraggedBlockForTarget(removedBlock, sec.items, 'to-main');
        sec.items.splice(Math.max(0, Math.min(insertIndex, sec.items.length)), 0, ...block);

        App.renderQuotePage();
        return;
    }

    // move into a main row as subposition
    if (toRef.mode === 'to-sub') {
        const sec = State.sections?.[Number(toRef.sIdx)];
        const parent = sec?.items?.[Number(toRef.iIdx)];
        if (!parent) {
            App.renderQuotePage();
            return;
        }

        if (!Array.isArray(parent.subItems)) parent.subItems = [];

        const block = App.normalizeDraggedBlockForTarget(removedBlock, parent.subItems, 'to-sub');
        parent.subItems.push(...block);

        App.renderQuotePage();
        return;
    }

    // reorder inside same array
    if (toRef.mode === 'sort-array') {
        let insertIndex = Number(originalTargetIndex);
        if (!Number.isFinite(insertIndex)) {
            App.renderQuotePage();
            return;
        }

        if (sameArray && originalFromIndex < insertIndex) {
            insertIndex -= removedBlock.length;
        }

        const block = App.normalizeDraggedBlockForTarget(removedBlock, target.parentArray, 'sort-array');
        target.parentArray.splice(Math.max(0, Math.min(insertIndex, target.parentArray.length)), 0, ...block);

        App.renderQuotePage();
        return;
    }

    App.renderQuotePage();
};
    App.pickEK = (p) => {
        const candidates = [
            p?.ek, p?.ek_price, p?.purchase_price, p?.distributor_price,
            p?.net_price, p?.best_price, p?.price
        ];
        for (const c of candidates) {
            const n = App.num(c, NaN);
            if (Number.isFinite(n) && n > 0) return n;
        }
        return 0;
    };

    App.pickVK = (p, ek=0) => {
        const candidates = [
            p?.vk, p?.sale_price, p?.selling_price, p?.price,
        ];
        for (const c of candidates) {
            const n = App.num(c, NaN);
            if (Number.isFinite(n) && n > 0) return n;
        }
        return 0;
    };

    App.getLineType = (item) => (item?.lineType || 'standard');
    App.isLineIncluded = (item) => App.getLineType(item) === 'standard';

    App.toggleItemLineType = (sIdx, iIdx, toType) => {
        const it = State.sections?.[sIdx]?.items?.[iIdx];
        if (!it) return;

        const next = ((it.lineType || 'standard') === toType) ? 'standard' : toType;
        it.lineType = next;

        // keep old logic compatible
        it.status =
            next === 'optional' ? 'optional' :
            next === 'alternative' ? 'alternative' :
            'normal';

        App.renderQuotePage();
    };


    App.recalcLaborParent = function (sIdx, iIdx, subIdx) {
        const parent = State.sections?.[sIdx]?.items?.[iIdx]?.subItems?.[subIdx];
        if (!parent || !Array.isArray(parent.labor_rows)) return;

        const rows = parent.labor_rows;

        let totalQty = 0;
        let totalVk = 0;
        let totalEk = 0;

        rows.forEach((row) => {
            const qty = Number(row.qty || 0);
            const rate = Number(row.rate || 0);
            const ek = Number(row.ek || 0);

            totalQty += qty;
            totalVk += qty * rate;
            totalEk += qty * ek;

            row.total = qty * rate;
        });

        parent.qty = totalQty || 1;
        parent.price = totalQty > 0 ? (totalVk / totalQty) : 0;
        parent.rate = parent.price;
        parent.ek = totalQty > 0 ? (totalEk / totalQty) : 0;
        parent.purchase_price = parent.ek;
    };

    App.updateLaborRowField = function (sIdx, iIdx, subIdx, rowIdx, field, value) {
        const carrier = App.resolveLaborCarrier(sIdx, iIdx, subIdx);
        if (!carrier || !Array.isArray(carrier.labor_rows) || !carrier.labor_rows[rowIdx]) return;

        const row = carrier.labor_rows[rowIdx];

        if (field === 'qualification_id') {
            const opt = App.getLaborOptionById(value);

            if (opt) {
                row.qualification_id = Number(opt.id);
                row.qualification_name = opt.name;
                row.ek = Number(opt.default_price || 0);
                row.margin_percent = Number(row.margin_percent ?? App.getDefaultMargin('labor'));
                App.applyLaborRowPricing(row, 'fromMargin');
            } else {
                // keep stored custom master-set value if no matching option exists
                row.qualification_id = null;
            }
        }
        else if (field === 'qualification_name') {
            row.qualification_name = (value || '').toString();
        }
        else if (field === 'qty') {
            row.qty = parseFloat(value) || 0;
            App.applyLaborRowPricing(row, 'fromMargin');
        }
        else if (field === 'ek') {
            row.ek = parseFloat(value) || 0;
            App.applyLaborRowPricing(row, 'fromMargin');
        }
        else if (field === 'margin_percent') {
            row.margin_percent = parseFloat(value) || 0;
            App.applyLaborRowPricing(row, 'fromMargin');
        }
        else if (field === 'rate') {
            row.rate = parseFloat(value) || 0;
            App.applyLaborRowPricing(row, 'fromRate');
        }
        else if (field === 'unit') {
            row.unit = (value || 'Std').toString();
        }

        App.recalcLaborCarrier(sIdx, iIdx, subIdx);
        App.renderQuotePage();
    };

    App.addLaborRow = async function (sIdx, iIdx, subIdx = null) {
        const carrier = App.resolveLaborCarrier(sIdx, iIdx, subIdx);
        if (!carrier) return;

        if (!Array.isArray(carrier.labor_rows)) carrier.labor_rows = [];

        const options = await App.loadLaborOptions();
        const first = options[0] || null;
        const margin = App.getDefaultMargin('labor');
        const ek = Number(first?.default_price || 0);
        const rate = ek > 0 ? App.vkFromEkMargin(ek, margin) : 0;

        carrier.labor_rows.push({
            id: Date.now(),
            qualification_id: first ? Number(first.id) : null,
            qualification_name: first ? first.name : '',
            qty: 1,
            unit: 'Std',
            ek: ek,
            margin_percent: margin,
            rate: rate,
            total: rate
        });

        App.recalcLaborCarrier(sIdx, iIdx, subIdx);
        App.renderQuotePage();
    };

    App.removeLaborRow = function (sIdx, iIdx, subIdx = null, rowIdx) {
        const carrier = App.resolveLaborCarrier(sIdx, iIdx, subIdx);
        if (!carrier || !Array.isArray(carrier.labor_rows)) return;

        carrier.labor_rows.splice(rowIdx, 1);

        if (carrier.labor_rows.length === 0) {
            const margin = App.getDefaultMargin('labor');
            carrier.labor_rows.push({
                id: Date.now(),
                qualification_id: null,
                qualification_name: '',
                qty: 1,
                unit: 'Std',
                ek: 0,
                margin_percent: margin,
                rate: 0,
                total: 0
            });
        }

        App.recalcLaborCarrier(sIdx, iIdx, subIdx);
        App.renderQuotePage();
    };

    App.getItemDescHtml = function (it) {
        if (!it) return '';

        const html = (it.desc_html || '').toString().trim();
        if (html) return html;

        const raw = (it.desc || '').toString().trim();
        if (!raw) return '';

        // if old data was stored inside desc as HTML, render it
        if (/<[a-z][\s\S]*>/i.test(raw)) return raw;

        return `<p>${App.escapeHtml(raw)}</p>`;
    };


</script>

<script>
App.parsePriceUnit = function(priceUnit, fallbackUnit = 'Stk'){
    const raw = (priceUnit || '').toString().trim();

    if (!raw) {
        return {
            value: 1,
            label: fallbackUnit || 'Stk',
            text: `1 ${fallbackUnit || 'Stk'}`
        };
    }

    const m = raw.match(/^(\d+(?:[.,]\d+)?)\s*(.+)$/);
    if (m) {
        return {
            value: parseFloat(m[1].replace(',', '.')) || 1,
            label: (m[2] || fallbackUnit || 'Stk').trim(),
            text: raw
        };
    }

    return {
        value: 1,
        label: raw,
        text: `1 ${raw}`
    };
};
</script>


<script>
  App.normalizeImgUrl = (src) => {
    const s = (src ?? '').toString().trim();
    if (!s) return '';
    if (s.startsWith('data:')) return s;                 
    if (s.startsWith('http://') || s.startsWith('https://')) return s;
    if (s.startsWith('//')) return window.location.protocol + s;
    if (s.startsWith('/')) return window.location.origin + s;
    return s; 
  };

  App.pickImage = (obj, fallback = '') => {
    if (!obj) return App.normalizeImgUrl(fallback);

    const candidates = [
      obj.image, obj.image_url, obj.imageUrl, obj.img, obj.img_url,
      obj.thumbnail, obj.thumb, obj.photo, obj.photo_url, obj.logo, obj.url,
      obj.media?.url, obj.media?.original_url, obj.media?.[0]?.original_url, obj.media?.[0]?.url,
      obj.images?.[0], obj.images?.[0]?.url, obj.images?.[0]?.original_url,
      obj.files?.[0]?.url, obj.files?.[0]?.original_url,
    ];

    for (const c of candidates) {
      const v = App.normalizeImgUrl(c);
      if (v) return v;
    }
    return App.normalizeImgUrl(fallback);
  };

  App.placeholderImg = (label = 'IMG') =>
    `https://placehold.co/150x150?text=${encodeURIComponent((label || 'IMG').slice(0, 12))}`;
</script>



<script>
    App.UNIT_OPTIONS = [
        'Stk', 'm', 'lfm', 'm²', 'm³', 'kg', 'g', 't', 'l', 'ml',
        'Std', 'Min', 'Tag', 'Woche', 'Monat', 'Pauschal', 'Set'
    ];

    App.renderUnitOptions = function(selected){
        const val = (selected || 'Stk').toString();
        return App.UNIT_OPTIONS.map(u =>
            `<option value="${u}" ${u === val ? 'selected' : ''}>${u}</option>`
        ).join('');
    };

    App.renderLaborUnitOptions = function(selected) {
        const val = (selected || 'Std').toString();
        // Only time/labor based units
        const opts = ['Std', 'Min', 'Tag', 'Pauschal'];
        return opts.map(u => 
            `<option value="${u}" ${u === val ? 'selected' : ''}>${u}</option>`
        ).join('');
    };



    App.loadLaborOptions = async function (force = false) {
        if (State.laborOptionsLoaded && !force) return State.laborOptions;
        if (State.laborOptionsLoading && !force) return State.laborOptions;

        State.laborOptionsLoading = true;

        try {
            const res = await fetch('/admin/master-sets/labor/options', {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const json = await res.json();
            const rows = Array.isArray(json?.data) ? json.data : [];

            State.laborOptions = rows.map(r => ({
                id: Number(r.id),
                name: (r.name || '').toString(),
                default_price: Number(r.default_price || 0)
            }));

            State.laborOptionsLoaded = true;
            return State.laborOptions;
        } catch (e) {
            console.error('loadLaborOptions failed', e);
            State.laborOptions = [];
            State.laborOptionsLoaded = false;
            return [];
        } finally {
            State.laborOptionsLoading = false;
        }
    };

    App.getLaborOptionById = function (id) {
        const n = Number(id);
        return (State.laborOptions || []).find(x => Number(x.id) === n) || null;
    };

    App.renderLaborOptionOptions = function (selectedId = null, selectedName = '') {
        const currentId = selectedId == null ? '' : String(selectedId);
        const currentName = (selectedName || '').toString().trim();
        const opts = Array.isArray(State.laborOptions) ? State.laborOptions : [];

        let html = `<option value="">Person wählen…</option>`;

        const hasExactMatch = opts.some(opt => String(opt.id) === currentId);

        // keep stored master-set row visible even if it is not in laborOptions
        if (currentName && (!currentId || !hasExactMatch)) {
            html += `
                <option value="${App.escapeHtml(currentId || '__custom__')}" selected>
                    ${App.escapeHtml(currentName)} (aus Master-Set)
                </option>
            `;
        }

        html += opts.map(opt => `
            <option value="${opt.id}" ${String(opt.id) === currentId ? 'selected' : ''}>
                ${App.escapeHtml(opt.name)} (${App.money(opt.default_price)} €)
            </option>
        `).join('');

        return html;
    };

    App.resolveLaborCarrier = function (sIdx, iIdx, subIdx = null) {
        const sec = State.sections?.[sIdx];
        if (!sec) return null;

        const main = sec.items?.[iIdx];
        if (!main) return null;

        if (subIdx === null || subIdx === undefined || subIdx === 'null') {
            return main;
        }

        return main.subItems?.[Number(subIdx)] || null;
    };

    App.applyLaborRowPricing = function (row, mode = 'fromMargin') {
        if (!row) return;

        const ek = Number(row.ek || 0);
        const margin = Number(row.margin_percent || 0);
        const rate = Number(row.rate || 0);

        if (mode === 'fromMargin') {
            row.rate = ek > 0 ? App.vkFromEkMargin(ek, margin) : 0;
        } else if (mode === 'fromRate') {
            row.margin_percent = ek > 0 ? (((rate - ek) / ek) * 100) : 0;
        }

        row.total = (Number(row.qty || 0) * Number(row.rate || 0));
    };

    App.recalcLaborCarrier = function (sIdx, iIdx, subIdx = null) {
        const carrier = App.resolveLaborCarrier(sIdx, iIdx, subIdx);
        if (!carrier || !Array.isArray(carrier.labor_rows)) return;

        let totalQty = 0;
        let totalVk = 0;
        let totalEk = 0;

        carrier.labor_rows.forEach((row) => {
            const qty = Number(row.qty || 0);
            const ek = Number(row.ek || 0);
            const rate = Number(row.rate || 0);

            row.total = qty * rate;

            totalQty += qty;
            totalVk += qty * rate;
            totalEk += qty * ek;
        });

        carrier.kind = 'labor';
        carrier.qty = totalQty || 1;
        carrier.unit = carrier.unit || 'Std';
        carrier.measure = carrier.measure || 'Std';

        carrier.price = totalQty > 0 ? (totalVk / totalQty) : 0;      // VK per hour
        carrier.rate = carrier.price;
        carrier.ek = totalQty > 0 ? (totalEk / totalQty) : 0;         // EK per hour
        carrier.purchase_price = carrier.ek;
    };

    /**
     * qty            = actual needed quantity (e.g. 400)
     * pricePerBase   = price for the pricing base quantity (e.g. 20 €)
     * baseQty        = pricing base quantity (e.g. 100)
     *
     * Example:
     * 400 m needed, price is 20 € per 100 m
     * => (400 / 100) * 20 = 80 €
     */
    App.calcScaledLineTotal = function(qty, pricePerBase, baseQty){
        const q = Number(qty || 0);
        const p = Number(pricePerBase || 0);
        const b = Number(baseQty || 1);

        if (!Number.isFinite(q) || !Number.isFinite(p) || !Number.isFinite(b) || b <= 0) {
            return 0;
        }

        return (q / b) * p;
    };

    App.calcItemGross = function(it){
        if (!it) return 0;

        if (it.isPauschal) {
            return Number(it.price || 0);
        }

        const qty = Number(it.qty || 0);
        const vk = Number(it.price || 0);
        const baseQty = Number(it.price_unit_value || 1);

        return App.calcScaledLineTotal(qty, vk, baseQty);
    };

    App.calcItemCost = function(it){
        if (!it) return 0;

        if (it.isPauschal) {
            return Number(it.purchase_price || it.ek || 0);
        }

        const qty = Number(it.qty || 0);
        const ek = Number(it.purchase_price || it.ek || 0);
        const baseQty = Number(it.price_unit_value || 1);

        return App.calcScaledLineTotal(qty, ek, baseQty);
    };

    App.renderLaborRowsTable = function (rows, forPrint = false) {
        if (!Array.isArray(rows) || !rows.length) return '';

        const esc = App.escapeHtml;
        const num = (v) => {
            const n = Number(v);
            return Number.isFinite(n) ? n : 0;
        };

        return `
            <div class="mt-3 rounded-lg border border-slate-200 overflow-hidden bg-white">
                <table class="w-full text-[10px] border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-2 py-1.5 text-left font-bold text-slate-500 uppercase border-b border-slate-200">Qualifikation</th>
                            <th class="px-2 py-1.5 text-center font-bold text-slate-500 uppercase border-b border-slate-200">Std</th>
                            <th class="px-2 py-1.5 text-center font-bold text-slate-500 uppercase border-b border-slate-200">Einheit</th>
                            <th class="px-2 py-1.5 text-right font-bold text-slate-500 uppercase border-b border-slate-200">EK</th>
                            <th class="px-2 py-1.5 text-right font-bold text-slate-500 uppercase border-b border-slate-200">Satz</th>
                            <th class="px-2 py-1.5 text-right font-bold text-slate-500 uppercase border-b border-slate-200">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map((row) => {
                            const qty = num(row.qty);
                            const ek = num(row.ek);
                            const rate = num(row.rate);
                            const total = Number.isFinite(Number(row.total)) ? Number(row.total) : (qty * rate);

                            return `
                                <tr>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-slate-700">${esc(row.qualification_name || '')}</td>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-center font-mono">${qty.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-center">${esc(row.unit || 'Std')}</td>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-right font-mono">${ek.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €</td>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-right font-mono">${rate.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €</td>
                                    <td class="px-2 py-1.5 border-b border-slate-100 text-right font-mono font-bold">${(qty * rate).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `;
    };

</script>



</body>
</html>