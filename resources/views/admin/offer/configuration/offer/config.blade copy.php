<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SADESK - Smart Angebot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
     <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Inject user preferences straight from the database
        window.ServerUserPrefs = {!! auth()->user()->preference ? auth()->user()->preference->toJson() : 'null' !!};
    </script>

    <script>
        @php
            $branchProfilesForJs = ($branches ?? collect())->mapWithKeys(function ($branch) {
                $key = (string) $branch->id;
                $logoUrl = $branch->logo_url ?: ($branch->image ? asset('storage/' . ltrim($branch->image, '/')) : '');

                return [
                    $key => [
                        'id' => (int) $branch->id,
                        'slug' => (string) ($branch->slug ?? ''),
                        'name' => (string) ($branch->branch ?? ''),
                        'branch' => (string) ($branch->branch ?? ''),
                        'street' => (string) ($branch->street ?? ''),
                        'postcode' => (string) ($branch->postcode ?? ''),
                        'city' => (string) ($branch->city ?? ''),
                        'country' => (string) ($branch->country ?? ''),
                        'email' => (string) ($branch->email ?? ''),
                        'phone' => (string) ($branch->phone ?? ''),
                        'whatsapp' => (string) ($branch->whatsapp ?? ''),
                        'web' => (string) ($branch->web ?? ''),
                        'bank' => (string) ($branch->bank ?? ''),
                        'iban' => (string) ($branch->iban ?? ''),
                        'bic' => (string) ($branch->bic ?? ''),
                        'register' => (string) ($branch->register ?? ''),
                        'tax' => (string) ($branch->tax ?? ''),
                        'vat' => (string) ($branch->vat ?? ''),
                        'gf' => (string) ($branch->gf ?? ''),
                        'contactPerson' => (string) ($branch->contact_person ?? ''),
                        'logoUrl' => (string) $logoUrl,
                        'color' => (string) ($branch->color ?: '#93c21c'),
                        'secondColor' => (string) ($branch->second_color ?: ($branch->color ?: '#93c21c')),
                    ],
                ];
            });
        @endphp
        window.BranchProfiles = @json($branchProfilesForJs);
        window.DefaultBranchProfileKey = Object.keys(window.BranchProfiles || {})[0] || 'solar-aspekt';
    </script>
    <style>
    :root{
            --brand-color:#93c21c;
            --second-color:#93c21c;
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
        color:#0f0f0f;
        height:100vh;
        display:flex;
        flex-direction:column;
        overflow:hidden;
        font-family:'Inter', sans-serif;
    }

    /* Ändern Sie diesen Teil in Ihrem <style> Bereich */
    #view-start {
        display: none; /* Wird durch .active auf flex gesetzt */
        height: 100%;
        overflow-y: auto; /* Erlaubt vertikales Scrollen falls der Content zu hoch ist */
        padding: 2rem 1rem;
    }

    /* Optimierung der Wizard-Card für verschiedene Auflösungen */
    #view-start .max-w-4xl {
        min-height: auto; /* Entfernt die fixe Mindesthöhe */
        height: auto;
        margin: auto; /* Zentriert die Karte im Scroll-Bereich */
    }


    /* Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button{
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"]{
            -moz-appearance: textfield;
            appearance: textfield;
        }

    .btn-primary{ @apply bg-[#93c21c] text-white shadow hover:brightness-105 transition-all active:scale-95 px-4 py-2 rounded font-bold; }
    .btn-disabled{ @apply bg-slate-300 text-white cursor-not-allowed shadow-none; }
    .sidebar-tab{ @apply flex-1 py-3 text-center text-xs font-bold text-[#000000] hover:text-[#93c21c] border-b-2 border-transparent transition cursor-pointer; }
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

   /* Fix für die A4 Dimensionen */
   .a4-page {
        width: 210mm;
        height: 297mm;
        max-height: 297mm; /* WICHTIG: Verhindert endloses Dehnen der Seite */
        background: #fff;
        margin: 0 auto 40px auto;
        padding: 17mm 14mm 8mm 14mm;
        position: relative;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    /* Der Bereich zwischen Header und Footer */
    .page-content {
        flex: 1;
        display: flex; 
        flex-direction: column;
        width: 100%;
        overflow: visible; /* Sichtbar lassen, falls extrem große Positionen gedruckt werden */
        position: relative;
        min-height: 0; /* CRITICAL: Zwingt die Flex-Box, die max-height der .a4-page zu respektieren! */
    }

    /* Damit die Tabellen und Reihen nicht mitten im Text brechen */ 
    .item-group, .pdf-item-card, .pdf-note-box {
        page-break-inside: auto; 
        break-inside: auto;      
    }

    .page-content{
        flex:1;
        display:flex;
        flex-direction:column;
        width:100%;
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
        font-weight:600;
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
        width:112px;
        height:112px;
        overflow:hidden;
        cursor:pointer;
        background:transparent;
        flex-shrink:0;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .prod-img-container img{
        width:100%;
        height:100%;
        object-fit:cover;
    }

    .prod-img-container:hover::after{
        content:'\f030';
        font-family:"Font Awesome 6 Free";
        font-weight:600;
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
    .pos-header-grid {
            display:grid;
            /* 6 Columns: Pos | Article | Qty | Unit | EP | GP */
            grid-template-columns: 28px minmax(0, 1fr) 65px 36px 59px 141px;
            gap:14px;
            font-size:10px;
            font-weight:500;
            color:#1c1c1c;
            border-bottom:1px solid #cbd5e1;
            padding-bottom:8px;
            margin-bottom:14px;
            align-items:center;
        }

        .pos-row-top {
            display:grid;
            /* 6 Columns: Pos | Article | Qty | Unit | EP | GP */
            grid-template-columns: 52px minmax(0, 1fr) 78px 68px 100px 110px;
            gap:10px;
            font-size:13px;
            font-weight:700;
            color:#4c4c4c;
            border-bottom:1px solid var(--brand-color);
            padding-bottom:6px;
            margin-bottom:10px;
            align-items:start;
            width:100%;
        }
   .pos-row-bottom{
        display:grid;
        grid-template-columns:120px 1fr;
        gap:12px;
        padding-left:52px;
        margin-bottom:-11px;
        align-items:start;
    }

    .pdf-blue-title{
        color:var(--second-color, var(--brand-color)); /* Uses secondColor, falls back to brandColor */
        font-weight:800;
        text-transform:uppercase;
        font-size:13px;
        line-height:1.3;
        margin:0 0 8px 0;
        letter-spacing:.01em;
    }

    .pdf-main-title{
        color:#4c4c4c;
        font-weight:800;
        font-size:15px;
        line-height:1.25;
        margin-left: -20px;
    }

    .pdf-desc-block{
        font-size:10px;
        color:#0f0f0f;
        line-height:1.6;
    }

    .pdf-desc-block p{
        margin:0 0 7px 0;
    }

    .pdf-desc-block ul,
    .pdf-desc-block ol{
        margin:0 0 8px 0;
        padding-left:18px;
    }

    .pdf-desc-block li{
        margin:0 0 3px 0;
    }

    .pdf-item-card{
        margin-bottom:20px;
        position: relative;
    }

    .pdf-subitem{
        margin-left:0px;
        padding-top:8px;
        border-top:1px solid #eef2f7;
    }

    .pdf-subitem .pos-row-bottom{
        grid-template-columns:86px 1fr;
        gap:6px;
    }

    .pdf-subitem .prod-img-container{
        width:131px;
        height:131px;
    }

    .pdf-note-box {
        margin: 8px 0 18px 52px;
        padding: 10px 14px;
        border-left: 4px solid #6ea9c8;
        background: #f8fbfd;
        position: relative; /* <-- ADD THIS */
    }

    .pdf-note-title{
        color:#3b82f6;
        font-weight:800;
        margin-bottom:4px;
        font-size:13px;
    }

    .pdf-labor-table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        margin-top:10px;
        font-size:11px;
        border:1px solid #dbe4ea;
        border-radius:10px;
        overflow:hidden;
    }

    .pdf-labor-table thead th{
        background:#f8fafc;
        color:#1c1c1c;
        font-size:10px;
        text-transform:uppercase;
        letter-spacing:.04em;
        font-weight:800;
        padding:8px 10px;
        border-bottom:1px solid #e2e8f0;
    }

    .pdf-labor-table tbody td{
        padding:8px 10px;
        border-bottom:1px solid #edf2f7;
        color:#0f0f0f;
    }

    .pdf-labor-table tbody tr:last-child td{
        border-bottom:0;
    }

    .pdf-labor-table .num{
        text-align:right;
        font-variant-numeric:tabular-nums;
    }

    .pdf-labor-table .center{
        text-align:center;
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
        color:#1c1c1c;
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
        @apply flex-1 py-3 text-center text-xs font-bold text-[#000000] hover:text-[#93c21c] border-b-2 border-transparent transition cursor-pointer;
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
        font-weight:600;
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
        color:#1c1c1c;
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

    .section-drop-zone {
        display: none !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition: all .18s ease;
    }

    body.drag-active .section-drop-zone {
        display: flex !important;
        min-height: 40px !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        border: 2px dashed #e2e8f0 !important;
        padding: 0.5rem !important;
        opacity: 1 !important;
        pointer-events: auto !important;
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
        padding:2px;
        margin-bottom:5px;
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
       TOOL IMAGES (FLOATING)
    ========================= */

    /* New settings menu for text (Color & Size) */
    .text-settings-float {
        position: absolute;
        top: -38px;
        left: 0;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        display: none;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 60;
        align-items: center;
    }
    .floating-element:hover .text-settings-float {
        display: flex;
    }
    
    /* Dedicated Move Handle (Top Left) */
    .tool-handle.move-handle {
        top: -12px;
        left: -12px;
        cursor: grab;
    }
    .tool-handle.move-handle:active {
        cursor: grabbing;
    }
    .floating-element {
        position: absolute;
        cursor: grab;
        z-index: 50;
        transform-origin: center center; /* Important for rotation */
    }

    .floating-element:active {
        cursor: grabbing;
    }

    /* Show dashed outline on hover */
    .floating-element:hover {
        outline: 2px dashed #94a3b8;
    }

    /* New settings menu for text (Color & Size) */
    .text-settings-float {
        position: absolute;
        top: -38px;
        left: 0;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        display: none;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 60;
        align-items: center;
    }
    .floating-element:hover .text-settings-float {
        display: flex;
    }
    
    /* Dedicated Move Handle (Top Left) */
    .tool-handle.move-handle {
        top: -12px;
        left: -12px;
        cursor: grab;
    }
    .tool-handle.move-handle:active {
        cursor: grabbing;
    }

    /* The tiny control buttons */
    .tool-handle {
        position: absolute;
        background: #fff;
        color: #0f0f0f;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: none; /* hidden by default */
        align-items: center;
        justify-content: center;
        font-size: 10px;
        z-index: 60;
        transition: color 0.2s, border-color 0.2s;
    }

    .tool-handle:hover {
        color: #93c21c;
        border-color: #93c21c;
    }
 
   /* Show all handles ONLY when selected */
    .floating-element.is-selected .tool-handle,
    .floating-element.is-selected .delete-float,
    .floating-element.is-selected .text-settings-float {
        display: flex;
    }
    
    .floating-element.is-selected {
        outline: 2px dashed #93c21c;
        z-index: 100 !important;
    }

    .resize-handle {
        bottom: -12px;
        right: -12px;
        cursor: se-resize;
    }

    .rotate-handle {
        top: -28px;
        left: 50%;
        transform: translateX(-50%);
        cursor: grab;
    }

    .rotate-handle:active {
        cursor: grabbing;
    }

    /* Update your existing delete-float to match */
    .delete-float {
        position: absolute;
        top: -12px;
        right: -12px;
        background: #ef4444;
        color: #fff;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        z-index: 60;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .delete-float:hover {
        background: #dc2626;
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
        background:white;
    }

    .list-toolbar-title{
        font-size:.75rem;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.12em;
        color:#1c1c1c;
    }

    .list-toolbar-subtitle{
        font-size:1rem;
        font-weight:600;
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
        background:linear-gradient(180deg, #0f172a, #4c4c4c);
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
        font-weight:600;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:rgba(255,255,255,.86);
        white-space:nowrap;
    }

    .list-sec-head{
        background:#cfe09b;
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
        background:#cfe09b;
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
        font-weight:600;
        line-height:1;
        white-space:nowrap;
    }

    .list-pill-green{ background:#ecfccb; color:#4d7c0f; }
    .list-pill-blue{ background:#dbeafe; color:#1d4ed8; }
    .list-pill-orange{ background:#ffedd5; color:#c2410c; }
    .list-pill-slate{ background:#e2e8f0; color:#0f0f0f; }
    .list-pill-red{ background:#fee2e2; color:#b91c1c; }
    .list-pill-indigo{ background:#e0e7ff; color:#4338ca; }
    .list-pill-yellow{ background:#fef9c3; color:#a16207; }

    .list-section-drop{
        border-radius: 21px;
        padding: 8px;
        background: #ffffff;
        color: #738da2;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        border: 2px dashed #c0d8ea;
        margin-top: 6px;
        margin-bottom: 6px;
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
        color:#0f0f0f;
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
        color:#0f0f0f;
        font-size:.72rem;
        font-weight:600;
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
        color:#1c1c1c;
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

    /* Fix the Z-index so dropdowns don't hide behind the editor */
    .ql-snow .ql-picker-options {
        z-index: 1000 !important;
    }
 
    /* Fix Tailwind resetting the text styles inside the dropdowns */
    .ql-snow .ql-picker.ql-size .ql-picker-item::before {
        content: attr(data-value);
    }
    /* 👇 Added smaller sizes here 👇 */
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="6px"]::before { font-size: 6px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="8px"]::before { font-size: 8px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10px"]::before { font-size: 10px; }
    
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12px"]::before { font-size: 12px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14px"]::before { font-size: 14px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16px"]::before { font-size: 16px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18px"]::before { font-size: 18px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24px"]::before { font-size: 24px; }
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="32px"]::before { font-size: 32px; }
    /* Fallback for the empty "normal" option if no data-value is present */
    .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before {
        content: 'Normal';
        font-size: 13px; 
    }
    
    .ql-snow .ql-picker.ql-font .ql-picker-item::before {
        content: attr(data-value);
    }
    .ql-snow .ql-picker.ql-font .ql-picker-item:not([data-value])::before {
        content: 'Sans Serif';
    }
    .ql-snow .ql-picker.ql-lineHeight .ql-picker-label::before,
    .ql-snow .ql-picker.ql-lineHeight .ql-picker-item::before {
        content: attr(data-value) 'x Abstand';
    }
    .ql-snow .ql-picker.ql-lineHeight .ql-picker-label:not([data-value])::before,
    .ql-snow .ql-picker.ql-lineHeight .ql-picker-item:not([data-value])::before {
        content: 'Zeilenabstand';
    }

    .ql-snow .ql-picker.ql-lineHeight {
        width: 125px;
    }

    /* Optional: Ensure the labels update to reflect the selection */
    .ql-snow .ql-picker.ql-size .ql-picker-label::before {
        content: attr(data-value) !important;
    }
    .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before {
        content: 'Normal' !important;
    }
    /* --------------------------------- */

    .desc-preview-html{
        min-height:72px;
        padding:.75rem;
        border:1px dashed #dbe4ee;
        border-radius:.75rem;
        background:#fff;
        color:#0f0f0f;
        line-height:1.55;
    }

    .desc-preview-html{
        min-height:72px;
        padding:.75rem;
        border:1px dashed #dbe4ee;
        border-radius:.75rem;
        background:#fff;
        color:#0f0f0f;
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
            font-weight:600;
            text-transform:uppercase;
            letter-spacing:.16em;
            color:#94a3b8;
        }

        .sq-side-title{
            font-size:1rem;
            font-weight:600;
            color:#0f172a;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .sq-side-main-tabs{
            display:grid;
            grid-template-columns: 1fr 1fr 1fr;
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
            color:#1c1c1c;
            font-size:.78rem;
            font-weight:600;
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
            font-weight:600;
            color:#1c1c1c;
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
            color:#1c1c1c !important;
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
            font-weight:600;
            transition:all .18s ease;
            box-shadow:0 10px 18px rgba(15,23,42,.12);
        }

        .sq-upload-btn:hover{
            background:#4c4c4c;
        }

        .sq-tool-tip{
            font-size:.72rem;
            color:#1c1c1c;
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
    @media print {
        /* 1. Force backgrounds and colors to print correctly */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 2. Define physical page size to remove default browser margins */
        @page {
            size: A4 portrait;
            margin: 0 !important; 
        }

        /* 3. Hide EVERYTHING in the background so only the modal prints */
        body > *:not(#print-preview-modal) {
            display: none !important;
        }

        /* 4. Release the modal from being "fixed" */
        #print-preview-modal {
            display: block !important;
            position: relative !important;
            background: #fff !important;
            height: auto !important;
            width: auto !important;
            overflow: visible !important;
            padding: 0 !important;
        }

        #print-preview-content {
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            width: 100% !important;
            overflow: visible !important;
        }

        /* 5. A4 Page Layout - STRICT PRINT CONTEXT */
        .a4-page {
            width: 210mm !important;
            height: 297mm !important;
            max-height: 297mm !important;
            min-height: 297mm !important;
            margin: 0 !important;
            padding: 17mm 14mm 8mm 14mm !important;
            box-shadow: none !important;
            border: none !important;
            
            /* CRITICAL: Ensures absolute floating elements anchor to this specific page */
            position: relative !important; 
            
            page-break-after: always !important;
            break-after: page !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            
            display: flex !important;
            flex-direction: column !important;
            transform: none !important;
            box-sizing: border-box !important;
            overflow: visible !important; /* Prevents overflow pushing elements down */
        }

        .page-content {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            position: relative !important;
        }

        /* 6. Hide the top dark bar and other UI */
        .no-print,
        .sidebar-panel,
        .thumb-container,
        header {
            display: none !important;
        }

        /* 7. Clean up interactive elements inside the print */
        .item-group {
            border: none !important;
            background: transparent !important;
            margin-bottom: 1rem !important;
            padding: 0 !important;
            cursor: default !important;
            box-shadow: none !important;
        }

        .prod-img-container {
            position: relative;
            width: 90px; /* Changed from 112px */
            height: 90px; /* Changed from 112px */
            overflow: hidden;
            cursor: pointer;
            background: transparent;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            resize: both; /* Enables manual resizing */
            min-width: 50px;
            min-height: 50px;
            max-width: 400px;
            max-height: 400px;
        }

        @media print {
            .prod-img-container {
                resize: none !important;
            }
        }
        .section-drop-zone,
        .delete-float {
            display: none !important;
        }
        
        .pdf-labor-wrap {
            overflow: visible !important;
        }

        .pdf-labor-table {
            min-width: 100% !important;
            width: 100% !important;
        }

        /* 8. Floating Element Print Fixes */
        .floating-element {
            position: absolute !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            /* Ensures the background of textboxes prints properly */
            background: transparent !important; 
            z-index: 10 !important;
        }
    }

    .pdf-blue-title {
        color: var(--second-color, var(--brand-color)) !important; /* <--- Add !important here */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 13px;
        line-height: 1.3;
        margin: 0 0 8px 0;
        letter-spacing: .01em;
    }

    .pdf-main-title {
        color: #4c4c4c; /* <--- Set back to gray */
        font-weight: 800; /* (oder 700 bei der zweiten) */
        font-size: 15px; /* (oder 13px bei der zweiten) */
        line-height: 1.25;
        margin-left: -20px;
    }

    .pos-row-top {
        display:grid;
        /* 6 Columns: Pos | Article | Qty | Unit | EP | GP */
        grid-template-columns: 52px minmax(0, 1fr) 78px 68px 100px 110px;
        gap:10px;
        font-size:13px;
        font-weight:700;
        color:#4c4c4c;
        border-bottom:1px solid var(--brand-color);
        padding-bottom:6px;
        margin-bottom:10px;
        align-items:start;
        width:100%;
    }

    .pos-row-top.compact {
        grid-template-columns: 47px minmax(0, 1fr) 16px 68px 100px 110px;
        font-size:13px;
        padding-bottom:5px;
        margin-bottom:8px;
    }
    .pos-row-bottom {
        display: flex;
        gap: 16px;
        padding-left: 0; /* Merges image into the left position column */
        margin-bottom: 12px;
        width: 100%;
        align-items: flex-start;
    }

    .pos-row-bottom.no-image {
        display: block;
        padding-left: 39px; 
        width: 100%;
    }

    .pos-row-bottom.no-image > .flex-1,
    .pos-row-bottom.no-image .pdf-desc-block {
        width: 100%;
        min-width: 0;
    }


    .pdf-subitem .pos-row-bottom {
        gap: 12px;
    }

    .pdf-subitem .pos-row-bottom.no-image {
        padding-left:39px;
    }

    /* Slightly smaller image for sub-positions so they nest nicely */
    .pdf-subitem .prod-img-container {
        width: 90px;
        height: 90px;
    }

    .pdf-desc-block{
        width:100%;
        min-width:0;
        font-size:11px;
        line-height:1.55;
        color:#000000;
    }

    .pdf-desc-block p{
        margin:0 0 2px 0;
    }

    .pdf-desc-block p:last-child{
        margin-bottom:0;
    }

    .pdf-desc-block ul,
    .pdf-desc-block ol{
        margin:0 0 8px 18px;
        padding:0;
    }

    .pdf-desc-block li{
        margin:0 0 4px 0;
    }

    .pdf-desc-block strong,
    .pdf-desc-block b{
        font-weight:700;
        color:#1f2937;
    }

.pdf-item-card{
    width:100%;
    margin-bottom:14px;
}

.pdf-subitem{
    margin-top:2px;
}

.pdf-pos-no{
    color:#1c1c1c;
    font-weight:700;
    white-space:nowrap;
}

.pdf-labor-wrap{
    width:100%;
    max-width:100%;
    margin:10px 0 4px 0;
    overflow-x:auto;
    overflow-y:hidden;
}

.pdf-labor-table{
    width:100%;
    min-width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    font-size:11px;
    color:#0f0f0f;
    background:transparent;
}

.pdf-labor-table thead th{
    background:transparent;
    color:#374151;
    font-size:10px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.04em;
    padding:7px 8px 8px;
    border-top:0;
    border-left:0;
    border-right:0;
    border-bottom:1px solid var(--brand-color);
    vertical-align:bottom;
}

.pdf-labor-table tbody td{
    padding:7px 8px;
    border:0;
    border-bottom:1px solid #e5e7eb;
    vertical-align:top;
    background:transparent;
}

.pdf-labor-table tbody tr:nth-child(even) td{
    background:transparent;
}

.pdf-labor-table tfoot td{
    padding:8px 8px 0 8px;
    border:0;
    border-top:1px solid #cbd5e1;
    background:transparent;
    font-size:11px;
    color:#4c4c4c;
}

.pdf-labor-total-label{
    font-weight:800;
    color:#4c4c4c;
}

.pdf-labor-qual{
    font-weight:700;
    color:#1f2937;
    line-height:1.35;
    word-break:break-word;
}

.pdf-labor-col-title{ width:34%; }
.pdf-labor-col-unit{ width:10%; }
.pdf-labor-col-qty{ width:12%; }
.pdf-labor-col-ek{ width:14%; }
.pdf-labor-col-rate{ width:14%; }
.pdf-labor-col-total{ width:16%; }

.pdf-labor-table .center{
    text-align:center;
}

.pdf-labor-table .num{
    text-align:right;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
}

.pdf-note-box{
    width:100%;
    margin:8px 0 14px 0;
    padding:10px 0 2px 52px;
    border-top:1px solid #e2e8f0;
}

.pdf-note-title{
    color:var(--brand-color);
    font-weight:800;
    text-transform:uppercase;
    font-size:13px;
    line-height:1.3;
    margin:0 0 8px 0;
}

@media (max-width: 1100px){
    .pdf-labor-wrap{
        overflow-x:auto;
    }

    .pdf-labor-table{
        min-width:760px;
    }
}

@media print{
    .pdf-labor-wrap{
        overflow:visible !important;
    }

    .pdf-labor-table{
        min-width:100% !important;
        width:100% !important;
    }
}
    

/* =========================
   IDS / OCI SUPPLIER SEARCH IN OFFER LIST
========================= */
.list-mini-btn-supplier{
    border-color:rgba(147,194,28,.35) !important;
    background:linear-gradient(135deg, #93c21c 0%, #7fa916 100%) !important;
    color:#fff !important;
    box-shadow:0 10px 22px rgba(147,194,28,.22);
    position:relative;
    overflow:hidden;
}

.list-mini-btn-supplier::after{
    content:'';
    position:absolute;
    inset:-40% auto -40% -40%;
    width:36%;
    transform:rotate(18deg);
    background:linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent);
    animation:supplierBtnShine 2.8s ease-in-out infinite;
}

.list-mini-btn-supplier:hover{
    color:#fff !important;
    border-color:#93c21c !important;
    box-shadow:0 14px 28px rgba(147,194,28,.26) !important;
    transform:translateY(-1px);
}

@keyframes supplierBtnShine{
    0%, 35%{ left:-40%; opacity:0; }
    50%{ opacity:1; }
    100%{ left:120%; opacity:0; }
}

.supplier-import-row-flash{
    animation:supplierRowFlash 2.6s ease-in-out 1;
    position:relative;
}

.supplier-import-row-flash::before{
    content:'Neu vom Lieferant';
    position:absolute;
    top:6px;
    right:8px;
    z-index:30;
    padding:4px 8px;
    border-radius:999px;
    background:#93c21c;
    color:#fff;
    font-size:10px;
    font-weight:900;
    box-shadow:0 12px 24px rgba(147,194,28,.25);
}

@keyframes supplierRowFlash{
    0%{ transform:translateY(-8px) scale(.99); opacity:.35; box-shadow:0 0 0 rgba(147,194,28,0); }
    18%{ transform:translateY(0) scale(1); opacity:1; box-shadow:0 0 0 5px rgba(147,194,28,.20), 0 18px 42px rgba(147,194,28,.20); background:#f7fee7; }
    55%{ box-shadow:0 0 0 3px rgba(147,194,28,.12), 0 14px 30px rgba(147,194,28,.10); background:#fbfff1; }
    100%{ box-shadow:none; background:white; }
}

.supplier-modal-card{
    animation:supplierModalIn .22s ease-out;
}

@keyframes supplierModalIn{
    from{ transform:translateY(10px) scale(.985); opacity:0; }
    to{ transform:translateY(0) scale(1); opacity:1; }
}

.supplier-live-toast{
    position:fixed;
    right:22px;
    bottom:22px;
    z-index:10020;
    max-width:440px;
    border-radius:18px;
    border:1px solid rgba(147,194,28,.25);
    background:#fff;
    box-shadow:0 24px 60px rgba(15,23,42,.18);
    padding:14px 16px;
    display:none;
    align-items:flex-start;
    gap:12px;
    animation:supplierToastIn .22s ease-out;
}

.supplier-live-toast.is-visible{
    display:flex;
}

.supplier-live-toast-icon{
    width:38px;
    height:38px;
    border-radius:14px;
    background:#f7fee7;
    color:#6b8e12;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
}

.supplier-live-toast-title{
    font-weight:950;
    color:#0f172a;
    font-size:13px;
    margin-bottom:2px;
}

.supplier-live-toast-text{
    color:#64748b;
    font-size:12px;
    line-height:1.45;
    font-weight:700;
}

@keyframes supplierToastIn{
    from{ transform:translateY(8px) scale(.98); opacity:0; }
    to{ transform:translateY(0) scale(1); opacity:1; }
}

@media(max-width: 900px){
    .list-toolbar > .flex{ flex-wrap:wrap; justify-content:flex-start; }
    .list-mini-btn-supplier{ width:100%; }
}


/* =========================
   SUPPLIER POSITION HISTORY
========================= */
.list-mini-btn-history{
    border-color:rgba(59,130,246,.22) !important;
    background:linear-gradient(135deg, #ffffff 0%, #eff6ff 100%) !important;
    color:#1d4ed8 !important;
    box-shadow:0 8px 18px rgba(59,130,246,.10);
}

.list-mini-btn-history:hover{
    border-color:#3b82f6 !important;
    color:#1e40af !important;
    background:#eff6ff !important;
    transform:translateY(-1px);
}

.supplier-history-card{
    animation:supplierModalIn .22s ease-out;
}

.supplier-history-item{
    border:1px solid #e2e8f0;
    border-radius:18px;
    background:#fff;
    padding:12px;
    transition:all .16s ease;
}

.supplier-history-item:hover{
    border-color:rgba(147,194,28,.55);
    box-shadow:0 14px 28px rgba(15,23,42,.08);
    transform:translateY(-1px);
}

.supplier-history-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    background:#f1f5f9;
    color:#475569;
    padding:4px 8px;
    font-size:10px;
    font-weight:900;
}

.supplier-history-readd-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    padding:8px 10px;
    border-radius:13px;
    background:#93c21c;
    color:#fff;
    font-size:12px;
    font-weight:950;
    transition:all .16s ease;
}

.supplier-history-readd-btn:hover{
    filter:brightness(.96);
    transform:translateY(-1px);
}

.supplier-history-delete-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:34px;
    height:34px;
    border-radius:13px;
    border:1px solid #fecaca;
    background:#fff;
    color:#dc2626;
    transition:all .16s ease;
}

.supplier-history-delete-btn:hover{
    background:#fef2f2;
}

</style>

<style>

    /* Visual Landing Marker */
    .drag-over-sort {
        background-color: transparent !important;
        border-top: 4px solid var(--brand-color) !important; /* The landing line */
        position: relative;
        transition: border-top 0.1s ease-in-out;
    }

    /* Optional: add a little dot at the start of the line for flair */
    .drag-over-sort::before {
        content: '';
        position: absolute;
        top: -6px;
        left: -2px;
        width: 8px;
        height: 8px;
        background: var(--brand-color);
        border-radius: 50%;
    }

   /* Hide drag handle by default, show on row hover */
    .drag-handle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border: 1px solid #e2e8f0;
        border-radius: .4rem;
        background: #fff;
        color: #94a3b8;
        cursor: grab;
        transition: all .18s ease;
        opacity: 0;
        pointer-events: none;
    }
    
   .item-group:hover .drag-handle,
    .pdf-note-box:hover .drag-handle {
        opacity: 1;
        pointer-events: auto;
    }

    .drag-handle:hover {
        border-color: var(--brand-color);
        color: #7ca816;
        background: #f7fee7;
    }

    .drag-handle:active {
        cursor: grabbing;
    }
</style>
 <style>
    .material-table-shell{
    background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);
    border-radius:24px;
    overflow:hidden;
}

.material-grid-scroll{
    position:relative;
    overflow:auto;
    max-height:72vh;
    background:
        linear-gradient(180deg, rgba(248,250,252,.96) 0%, rgba(255,255,255,1) 140px);
    scrollbar-width:thin;
}

.material-sticky-head{
    position:sticky;
    top:0;
    z-index:40;
    background:linear-gradient(180deg,#f8fafc 0%,#eef4f8 100%);
    border-bottom:1px solid #cfd9e4;
    box-shadow:0 6px 18px rgba(15,23,42,.06);
    backdrop-filter:blur(10px);
}

.mat-head-row{
    min-width:max-content;
}

.mat-head-cell{
    display:flex;
    align-items:center;
    min-height:52px;
    padding:0 14px;
    font-size:11px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.09em;
    color:#4b5d73;
    background:transparent;
    border-right:1px solid #dde6ee;
    white-space:nowrap;
}

.mat-head-cell:last-child{
    border-right:none;
}

.mat-data-row{
    position:relative;
    margin:2px 2px;
    border-radius:12px;
    overflow:hidden;
    background:white;
    transition:.18s ease;
}

.mat-data-row:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 28px rgba(15,23,42,.08);
}

.mat-data-row.is-main-row{
    background:white;
}

.mat-data-row.is-sub-row{
    margin-left:3px;
    background:white;
    border-left:5px solid #73b2d3;
}

 

.mat-data-row.lv-row-labor{
    background:white;
}

.mat-data-row.lv-row-labor.is-sub-row{
    border-left-color:#93c21c;
    margin:12px 0px 12px 0px;
}

.mat-data-row.lv-row-note{
    background:linear-gradient(180deg,#f4f9ff 0%,#ebf5ff 100%);
    border-color:#b9d8ff;
}

.mat-data-grid{
    min-width:max-content;
    align-items:stretch;
}

.mat-grid-cell{
    padding:8px 4px;
    border-right:1px solid #ffffff;
    display:flex;
    align-items:center;
    min-height:46px;
    background:transparent;
}

.mat-grid-cell:last-child{
    border-right:none;
}

.mat-grid-cell-center{
    justify-content:center;
    text-align:center;
}

.mat-grid-cell-right{
    justify-content:flex-end;
    text-align:right;
}

.mat-ctrl{
    height:25px;
    min-height:25px;
    border:1px solid #d6e0ea;
    border-radius:12px;
    background:#fff;
    color:#0f0f0f;
    font-size:12px;
    font-weight:700;
    outline:none;
    transition:all .18s ease;
}

.mat-ctrl:focus{
    border-color:#93c21c;
    box-shadow:0 0 0 4px rgba(147,194,28,.14);
}

.mat-ctrl[readonly],
.mat-ctrl:disabled{
    background:#f8fafc;
    color:#94a3b8;
    cursor:not-allowed;
}

.mat-input{
    width:100%;
    padding:0 12px;
}

.mat-input-center{
    text-align:center;
    padding:0 10px;
}

.mat-input-right{
    text-align:right;
    padding:0 12px;
}

.mat-addon-wrap{
    display:flex;
    align-items:center;
    height:25px;
    border:1px solid #d6e0ea;
    border-radius:12px;
    background:#fff;
    overflow:hidden;
}

.mat-addon-wrap:focus-within{
    border-color:#93c21c;
    box-shadow:0 0 0 4px rgba(147,194,28,.14);
}

.mat-addon-input{
    border:0;
    outline:0;
    background:transparent;
    height:25px;
    width:100%;
    padding:0 10px;
    font-size:12px;
    font-weight:700;
    color:#0f0f0f;
}

.mat-addon-text{
    height:100%;
    padding:0 10px;
    display:inline-flex;
    align-items:center;
    font-size:11px;
    font-weight:800;
    color:#94a3b8;
    border-left:1px solid #e2e8f0;
    background:#f8fafc;
}

.mat-btn-icon{
    height:36px;
    width:36px;
    border:1px solid #d7e1ea;
    border-radius:12px;
    background:#fff;
    color:#1c1c1c;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:all .18s ease;
}

.mat-btn-icon:hover{
    border-color:#93c21c;
    color:#7ca816;
    background:#f8fbfe;
    box-shadow:0 8px 18px rgba(147,194,28,.12);
}

 </style>
 <style>
   .lv-pos-wrap{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    width:100%;
    flex-direction:row !important;
    flex-wrap:nowrap !important;
    white-space:nowrap;
}

.lv-pos-badge{
    min-width:56px;
    height:34px;
    padding:0 10px;
    border-radius:12px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:white;
    color:#515151;
    font-size:11px;
    font-weight:600;
    letter-spacing:.04em;
    white-space:nowrap;
    flex:0 0 auto;
}

.lv-pos-toggle,
.lv-pos-toggle-placeholder{
    width:28px;
    height:28px;
    flex:0 0 28px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}

.lv-pos-toggle{
    border-radius:10px;
    border:1px solid #c5c5c5;
    background:#cfe09b00;
    color:#1c1c1c;
    transition:.16s ease;
}

.lv-pos-toggle:hover{
    border-color:#93c21c;
    color:#6b8e12;
    box-shadow:0 6px 14px rgba(147,194,28,.16);
}
 </style>

<style>
    .pdf-offer-closing{
        margin-top:24px;
        font-size:12px;
        color:#0f0f0f;
        line-height:1.45;
    }

    .pdf-offer-closing-title{
        font-size:16px;
        font-weight:600;
        text-transform:uppercase;
        line-height:1.2;
        margin-bottom:12px;
    }

    .pdf-offer-closing-intro{
        font-size:11px;
        color:#0f0f0f;
        margin-bottom:12px;
    }

    .pdf-offer-steps{
        display:flex;
        flex-direction:column;
        gap:6px;
        margin-bottom:14px;
    }

    .pdf-offer-step{
        background:#eef3de;
        padding:6px 8px;
    }

    .pdf-offer-step.is-plain{
        background:transparent;
        padding:0;
    }

    .pdf-offer-step-head{
        font-size:11px;
        font-weight:800;
        line-height:1.2;
        margin-bottom:2px;
    }

    .pdf-offer-step-head-blue { 
        color: var(--second-color, var(--brand-color)) !important; 
    }

    .pdf-offer-step-text{
        font-size:10px;
        color:#0f0f0f;
        line-height:1.35;
    }

    .pdf-offer-info-block{
        margin-top:10px;
    }

    .pdf-offer-info-title{
        font-size:11px;
        font-weight:800;
        line-height:1.2;
        margin-bottom:2px;
    }

    .pdf-offer-info-text{
        font-size:10px;
        color:#0f0f0f;
        line-height:1.35;
    }

    .pdf-offer-sign-title{
        margin-top:18px;
        font-size:18px;
        font-weight:600;
        text-transform:uppercase;
        line-height:1.1;
        margin-bottom:6px;
    }

    .pdf-offer-sign-text{
        font-size:10px;
        color:#0f0f0f;
        line-height:1.35;
        margin-bottom:18px;
    }

    .pdf-offer-sign-row{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:20px;
        margin-top:10px;
    }

    .pdf-offer-sign-left{
        flex:1 1 auto;
        min-width:0;
    }

    .pdf-offer-sign-line{
        height:26px;
        border-bottom:1px solid #7c7c7c;
        margin-bottom:4px;
    }

    .pdf-offer-sign-caption{
        font-size:10px;
        color:#0f0f0f;
        margin-bottom:22px;
    }

    .pdf-offer-remark-label{
        font-size:10px;
        color:#0f0f0f;
    }

    .pdf-offer-sign-note{
        width:260px;
        background:#eaf1f6;
        padding:8px 10px;
        align-self:flex-end;
    }

    .pdf-offer-sign-note-head{
        font-size:10px;
        font-weight:600;
        color:#74b2d4;
        margin-bottom:2px;
    }

    .pdf-offer-sign-note-text{
        font-size:10px;
        line-height:1.3;
        font-weight:700;
        color:#74b2d4;
    }

    .pdf-offer-bottom-line{
        margin-top:34px;
        border-top:2px solid var(--brand-color);
    }

    .pdf-offer-closing-block{
        margin-top:14px;
        break-inside:avoid;
    }

    .pdf-offer-closing-title{
        font-size:22px;
        font-weight:600;
        text-transform:uppercase;
        line-height:1.1;
        margin-bottom:10px;
    }

    .pdf-offer-closing-intro{
        font-size:12px;
        line-height:1.55;
        color:#0f0f0f;
    }

    .pdf-offer-section-headline{
        font-size:18px;
        font-weight:600;
        line-height:1.2;
        margin-bottom:10px;
    }

    .pdf-offer-step-box{
        background:#eef3de;
        padding:8px 10px;
        margin-bottom:6px;
    }

    .pdf-offer-step-box-plain{
        background:transparent;
        padding:0;
    }

    .pdf-offer-step-title{
        font-size:12px;
        font-weight:800;
        line-height:1.25;
        margin-bottom:2px;
    }

    .pdf-offer-step-title-blue { 
        color: var(--second-color, var(--brand-color)) !important; 
    }

    .pdf-offer-step-desc{
        font-size:11px;
        line-height:1.45;
        color:#0f0f0f;
    }

    .pdf-offer-info-stack{
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .pdf-offer-info-item{
        display:block;
    }

    .pdf-offer-info-title{
        font-size:16px;
        font-weight:800;
        line-height:1.2;
        margin-bottom:4px;
    }

    .pdf-offer-info-text{
        font-size:12px;
        line-height:1.5;
        color:#0f0f0f;
    }

    .pdf-offer-info-highlight{
        font-size:12px;
        font-weight:800;
        line-height:1.5;
    }

    .pdf-offer-signature-block{
        margin-top:10px;
        break-inside:avoid;
    }

    .pdf-offer-signature-row{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:24px;
    }

    .pdf-offer-signature-left{
        flex:1 1 auto;
        min-width:0;
    }

    .pdf-offer-signature-line{
        height:34px;
        border-bottom:2px solid #1c1c1c;
        max-width:420px;
        width:100%;
    }

    .pdf-offer-signature-caption{
        margin-top:4px;
        font-size:12px;
        color:#0f0f0f;
    }

    .pdf-offer-remark-title{
        margin-top:28px;
        font-size:12px;
        color:#0f0f0f;
    }

    .pdf-offer-remark-line{
        height:28px;
        border-bottom:1px solid #94a3b8;
    }

    .pdf-offer-signature-note{
        width:280px;
        background:#eaf2f7;
        padding:10px 12px;
    }

    .pdf-offer-signature-note-head{
        font-size:11px;
        font-weight:600;
        color:#74b2d4;
        margin-bottom:2px;
    }

    .pdf-offer-signature-note-text{
        font-size:11px;
        line-height:1.35;
        font-weight:800;
        color:#74b2d4;
    }

    .pdf-offer-signature-bottom-line{
        margin-top:32px;
        border-top:2px solid var(--brand-color);
    }

    /* =========================
   A4 SAFE CLOSING PAGE
========================= */
.pdf-offer-closing--a4{
    width:100%;
    max-width:100%;
    color:#0f0f0f;
    font-size:11px;
    line-height:1.45;
}

.pdf-offer-closing--a4 .compact{
    margin-top:0;
    margin-bottom:0;
}

.pdf-offer-closing-intro.compact{
    font-size:11px;
    line-height:1.55;
    color:#000000;
    margin-bottom:12px;
}

.pdf-offer-steps.compact{
    display:grid;
    grid-template-columns:1fr;
    gap:8px;
    margin-bottom:12px;
}

.pdf-offer-step-box{
    padding:8px 10px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
    break-inside:avoid;
}

.pdf-offer-step-box.soft{
    background:linear-gradient(180deg,#ffffff 0%, #f8fafc 100%);
}

.pdf-offer-step-box.plain{
    background:#fff;
}

.pdf-offer-step-title{
    font-size:11px;
    font-weight:800;
    line-height:1.35;
    margin-bottom:3px;
}

.pdf-offer-step-title-alt{
    color:#1c1c1c;
}

.pdf-offer-step-desc{
    font-size:10.5px;
    line-height:1.5;
    color:#000000;
}
 
/* =========================
   FINAL OFFER PAGE
========================= */
.pdf-offer-final-page{
    width:100%;
    max-width:100%;
    margin-top:10px;
    color:#0f0f0f;
    font-size:10px;
    line-height:1.38;
    break-inside:avoid;
    page-break-inside:avoid;
}

.pdf-offer-final-title{
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    line-height:1.2;
    margin-bottom:8px;
}

.pdf-offer-final-intro{
    font-size:10px;
    line-height:1.45;
    color:#0f0f0f;
    margin-bottom:8px;
}

.pdf-offer-process{
    margin-bottom:10px;
}

.pdf-offer-process-label{
    font-size:10px;
    font-weight:800;
    color:#111827;
    margin-bottom:6px;
}

.pdf-offer-process-step{
    margin-bottom:4px;
    break-inside:avoid;
    page-break-inside:avoid;
}

.pdf-offer-process-step.is-soft{
    background:#edf1df;
    padding:6px 8px;
}

.pdf-offer-process-step.is-plain{
    background:transparent;
    padding:0;
}

.pdf-offer-process-step-title{
    font-size:10px;
    font-weight:600;
    line-height:1.25;
    margin-bottom:2px;
}

.pdf-offer-process-step-title.alt { 
    color: var(--second-color, var(--brand-color)) !important; 
}

.pdf-offer-process-step-text{
    font-size:9.6px;
    line-height:1.35;
    color:#0f0f0f;
}

/* =========================
   SUMMARY CARD
========================= */
.pdf-summary-card{
    margin:10px 0 8px 0;
    padding:10px 12px; 
    background:#fff;
    break-inside:avoid;
    page-break-inside:avoid;
}

.pdf-summary-card-title{
    font-size:13px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.06em;
    margin-bottom:8px;
    font-weight:700;
}

.pdf-summary-list{
    display:flex;
    flex-direction:column;
    gap:5px;
    margin-bottom:8px;
}

.pdf-summary-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:6px 0;
    border-bottom:1px solid #e5e7eb;
}

.pdf-summary-row:last-child{
    border-bottom:none;
}

.pdf-summary-row-left{
    display:flex;
    align-items:flex-start;
    gap:8px;
    min-width:0;
    flex:1;
}

.pdf-summary-row-no{
    min-width:18px;
    font-size:10px;
    font-weight:600;
    line-height:1.3;
}

.pdf-summary-row-label{
    font-size:13px;
    font-weight:600;
    color:#0f172a;
    line-height:1.35;
    word-break:break-word;
}

.pdf-summary-row-value{
    flex-shrink:0;
    text-align:right;
    font-size:13px;
    font-weight:600;
    color:#0f172a;
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
}

.pdf-summary-totals{
    border-top:1px solid #cbd5e1;
    padding-top:8px;
    display:flex;
    flex-direction:column;
    gap:4px;
}

.pdf-summary-total-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    font-size:13px;
    color:#000000;
}


.pdf-summary-total-row span:last-child{
    font-weight:800;
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
}

.pdf-summary-total-row.strong{
    color:#0f172a;
    font-weight:600;
}

.pdf-summary-total-row.grand{
    margin-top:4px;
    padding-top:6px;
    border-top:2px solid var(--brand-color);
    font-size:13px;
    font-weight:600;
}

/* =========================
   INFO BLOCKS
========================= */
.pdf-offer-final-info{
    margin-top:8px;
}

.pdf-offer-final-info-block{
    margin-top:8px;
}

.pdf-offer-final-info-title{
    font-size:10px;
    font-weight:600;
    line-height:1.25;
    margin-bottom:2px;
}

.pdf-offer-final-info-title.alt { 
    color: var(--second-color, var(--brand-color)) !important; 
}

.pdf-offer-final-info-text{
    font-size:9.7px;
    line-height:1.38;
    color:#0f0f0f;
}

/* =========================
   SIGNATURE
========================= */
.pdf-offer-sign-title{
    margin-top:14px;
    margin-bottom:4px;
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    line-height:1.1;
}

.pdf-offer-sign-text{
    font-size:9.7px;
    color:#0f0f0f;
    line-height:1.35;
    margin-bottom:12px;
}

.pdf-offer-sign-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:16px;
    margin-top:4px;
}

.pdf-offer-sign-left{
    flex:1 1 auto;
    min-width:0;
}

.pdf-offer-sign-line{
    height:22px;
    border-bottom:1px solid #7c7c7c;
    margin-bottom:4px;
}

.pdf-offer-sign-caption{
    font-size:9.5px;
    color:#0f0f0f;
    margin-bottom:16px;
}

.pdf-offer-remark-label{
    font-size:9.5px;
    color:#0f0f0f;
    margin-bottom:2px;
}

.pdf-offer-remark-line{
    height:20px;
    border-bottom:1px solid #cbd5e1;
}

.pdf-offer-sign-note{
    width:260px;
    background:#eaf2f7;
    padding:8px 10px;
    align-self:flex-end;
}

.pdf-offer-sign-note-head { 
    color: var(--second-color, var(--brand-color)) !important; 
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 2px;
}

.pdf-offer-sign-note-text { 
    color: var(--second-color, var(--brand-color)) !important;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 800;
}

.pdf-offer-bottom-line{
    margin-top:22px;
    border-top:2px solid var(--brand-color);
}

/* =========================
   PRINT SAFETY
========================= */
@media print{
    .pdf-offer-final-page,
    .pdf-summary-card,
    .pdf-offer-process-step,
    .pdf-offer-sign-row,
    .pdf-offer-sign-note{
        break-inside:avoid;
        page-break-inside:avoid;
    }
}

</style>
<style>
    .lv-title-wrap{
    display:flex;
    align-items:center;
    width:100%;
    min-width:0;
}

.lv-title-row{
    display:flex;
    align-items:center;
    gap:8px;
    width:100%;
    min-width:0;
    flex-wrap:nowrap !important;
    white-space:nowrap;
}

.lv-title-row .mat-input{
    flex:1 1 auto;
    min-width:0;
}

.lv-title-edit-btn{
    width:34px;
    height:34px;
    flex:0 0 34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:#fff;
    color:#000000;
    transition:.2s ease;
}

.lv-title-edit-btn:hover{
    background:#f8fafc;
    border-color:#94a3b8;
    color:#0f172a;
}

.lv-title-edit-btn:disabled{
    opacity:.5;
    cursor:not-allowed;
}

.lv-kind-badge{
    flex:0 0 auto;
    white-space:nowrap;
}
</style>
<style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #1c1c1c;
        font-weight: 700;
        margin-right: 8px;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important;
        border-color: #e2e8f0 !important;
        min-height: 42px;
        display: flex !important;
        align-items: center !important;
        background: #f8fafc !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
        font-size: 14px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
 
   /* =========================
       SNAPSHOT LOCKDOWN MODE
    ========================= */
    body.is-locked-snapshot .page-content,
    body.is-locked-snapshot #listview-root,
    body.is-locked-snapshot #sidebar-right {
        pointer-events: none !important; /* Disables all clicks/drags */
        user-select: none !important;
        opacity: 0.9;
    }
    body.is-locked-snapshot .editable-field {
        border-color: transparent !important;
    }
    body.is-locked-snapshot .drag-handle,
    body.is-locked-snapshot .section-drop-zone,
    body.is-locked-snapshot .list-action-btn,
    body.is-locked-snapshot .delete-float,
    body.is-locked-snapshot button[onclick*="addSection"],
    body.is-locked-snapshot button[onclick*="addPosition"] {
        display: none !important;
    }

    /* =========================
       CALCULATION SIDEBAR RESIZE
    ========================= */
    #sidebar-right {
        position: relative;
        width: 320px; /* Default starting width */
        transition: width 0.3s cubic-bezier(.4,0,.2,1);
        container-type: inline-size;
        container-name: calcSidebar;
    }

    #sidebar-right.sidebar-collapsed {
        width: 0 !important;
        border: none !important;
        padding: 0 !important;
        overflow: hidden !important;
    }

    .calc-resizer {
        position: absolute;
        left: -3px; /* Handle sits exactly on the left edge */
        top: 0;
        bottom: 0;
        width: 6px;
        cursor: ew-resize;
        background: transparent;
        z-index: 50;
        transition: background 0.2s;
    }

    .calc-resizer:hover, .calc-resizer.active {
        background: var(--brand-color);
    }

    /* Hidden by default, shows up when sidebar is > 400px wide */
    .sidebar-pos-img { display: none; }
    
    @container calcSidebar (min-width: 420px) {
        .sidebar-pos-img { display: block; }
    }
</style>
<style > 
    /* =========================
       ATTACHMENT SIDEBAR
    ========================= */
    .sidebar-attachments {
        width: 320px;
        transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        flex-shrink: 0;
        z-index: 45;
        order: -1; /* Zwingt die Sidebar im Flex-Container nach ganz links */
    }
    .sidebar-attachments.collapsed {
        transform: translateX(-100%); /* Fährt nun nach LINKS aus dem Bild */
        width: 0 !important;
        border: none !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    .sidebar-attachments.unpinned {
        position: absolute;
        left: 0; /* An der LINKEN Seite fixieren */
        top: 0;
        bottom: 0;
        box-shadow: 10px 0 20px rgba(15,23,42,0.1); /* Schatten fällt nun nach rechts */
    }
    .sidebar-attachments.pinned {
        position: relative;
        box-shadow: none;
    }
    .attachment-resizer {
        position: absolute;
        right: -3px; /* Anfasser ist nun an der RECHTEN Kante der Sidebar */
        left: auto;
        top: 0;
        bottom: 0;
        width: 6px;
        cursor: ew-resize;
        background: transparent;
        z-index: 50;
        transition: background 0.2s;
    }
    .attachment-resizer:hover, .attachment-resizer.active {
        background: var(--brand-color);
    }
</style>

<script>
    // IDS / OCI offer supplier bridge config.
    // This is available before Vite loads, so resources/js/ids-listener.js can subscribe to Reverb immediately.
    window.OfferSupplierConfig = window.OfferSupplierConfig || {};
    (function () {
        const params = new URLSearchParams(window.location.search);
        const folderId = Number(params.get('offer_folder_id') || params.get('folder_id') || window.OfferSupplierConfig.folderId || 0);
        const offerId = Number(params.get('offer_id') || window.OfferSupplierConfig.offerId || 0);

        window.OfferSupplierConfig.folderId = folderId || window.OfferSupplierConfig.folderId || null;
        window.OfferSupplierConfig.offerId = offerId || window.OfferSupplierConfig.offerId || null;
        window.OfferSupplierConfig.returnMode = 'review_then_reverb';
    })();
</script>

@vite(['resources/js/app.js'])

<script>
    // Store the User ID globally so Reverb can use it
    window.currentUserId = {{ auth()->id() }};
</script>

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
                    <div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 mb-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="wiz-template-mode" onchange="App.Wizard.toggleTemplateMode(this.checked)" class="w-5 h-5 accent-[#93c21c]">
                            <div>
                                <span class="block text-sm font-black text-[#000000]">Als Vorlage erstellen</span>
                                <span class="block text-[11px] text-[#000000]">Kein Kunde/Objekt notwendig. Dokument wird nur als Template gespeichert.</span>
                            </div>
                        </label>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-bold text-[#000000] mb-2">1. Kunde</label>
                        <div class="relative">
                            <input type="text" id="wiz-customer-search" oninput="App.Wizard.filterCustomers()" onfocus="App.Wizard.filterCustomers()" placeholder="Suche..." class="w-full border border-slate-300 rounded-lg p-3 pl-10 text-sm outline-none focus:border-[#93c21c]"><div id="wiz-customer-dropdown" class="absolute w-full bg-white border border-slate-200 rounded-b-lg shadow-lg z-50 hidden max-h-40 overflow-y-auto">
                            </div>
                        </div>
                        <div id="wiz-customer-selected" class="hidden mt-2 p-3 bg-[#f7fee7] rounded-lg border border-[#93c21c]/30 flex justify-between items-center">
                            <div>
                                <div class="font-bold text-[#000000]" id="wiz-sel-cust-name"></div>
                                <div class="text-xs text-[#000000]" id="wiz-sel-cust-addr"></div>
                            </div>
                            <button onclick="App.Wizard.clearCustomer()" class="text-[#000000] hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                        </div>
                    </div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-2">
                        <label class="block text-sm font-bold text-[#000000] mb-2">2. Gewerk</label>
                        <!-- ✅ 3) Replace your select markup (remove size="6") -->
                        <select id="wiz-object-select" multiple onchange="App.Wizard.selectObject()"
                        class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c] bg-white">
                        </select>

                        <div class="mt-2 text-xs text-[#000000]">
                            Ausgewählt: <span id="wiz-object-count" class="font-bold">0</span>
                        </div>
                    </div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-3"><label class="block text-sm font-bold text-[#000000] mb-2">3. Datum</label><input type="date" id="wiz-date" class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c]"></div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-4"><label class="block text-sm font-bold text-[#000000] mb-2">4. Typ</label><div class="flex gap-4"><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Angebot" checked class="accent-[#93c21c]"><span class="text-sm font-bold">Angebot</span></label><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Kostenvoranschlag" class="accent-[#93c21c]"><span class="text-sm font-bold">Kostenvoranschlag</span></label></div></div>
                     
                    <div class="relative border-t pt-4">
                        <label class="block text-sm font-bold text-[#000000] mb-2">5. Filiale / Firma wählen</label>
                        
                        <select id="wiz-company-select" onchange="App.selectBranch(this.value)" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:border-[#93c21c] outline-none mb-4 bg-white shadow-sm font-bold text-[#000000]">
                            @forelse(($branches ?? collect()) as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                            @empty
                                <option value="solar-aspekt">SOLAR ASPEKT GmbH</option>
                                <option value="werk-studio">Werk-Studio GmbH</option>
                            @endforelse
                        </select>

                        <details class="group bg-slate-50 border border-slate-200 rounded-lg">
                            <summary class="text-xs text-[#000000] font-bold p-3 cursor-pointer hover:text-[#93c21c] flex justify-between items-center outline-none">
                                <span>Erweiterte Design-Einstellungen (Farbe, Logo)</span>
                                <i class="fa-solid fa-chevron-down transition-transform group-open:rotate-180"></i>
                            </summary>
                            
                            <div class="grid grid-cols-2 gap-4 p-4 border-t border-slate-200">
                                <div class="col-span-2">
                                    <label class="text-xs text-[#000000] block mb-1">Logo-Typ</label>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="inline-flex items-center gap-2 text-sm font-bold text-[#000000] cursor-pointer">
                                            <input type="radio" name="wiz-brand-mode" value="text" checked class="accent-[#93c21c]" onchange="App.updateBranding()"> Logo-Text
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm font-bold text-[#000000] cursor-pointer">
                                            <input type="radio" name="wiz-brand-mode" value="image" class="accent-[#93c21c]" onchange="App.updateBranding()"> Logo-Bild
                                        </label>
                                    </div>
                                </div>

                                <div id="brand-text-wrap" class="col-span-2">
                                    <label class="text-xs text-[#000000] block mb-1">Firmenname (Logo)</label>
                                    <input type="text" id="wiz-brand-name" value="SOLAR ASPEKT GmbH" oninput="App.updateBranding()" class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:border-[#93c21c] outline-none bg-white">
                                </div>

                                <div id="brand-logo-wrap" class="col-span-2 hidden">
                                    <label class="text-xs text-[#000000] block mb-1">Logo-URL / Auswahl</label>
                                    <div class="flex gap-2 items-center">
                                        <select id="wiz-brand-logo" onchange="App.updateBranding()" class="flex-1 border border-slate-300 rounded-lg p-2 text-sm focus:border-[#93c21c] outline-none bg-white">
                                            <option value="{{ asset('logo/logo.png') }}">Solar-Aspekt</option>
                                            <option value="{{ asset('logo/werk-studio.png') }}">Werk-Studio</option>
                                        </select>
                                        <div class="w-10 h-10 rounded border border-slate-200 bg-white flex items-center justify-center overflow-hidden shrink-0">
                                            <img id="wiz-logo-preview" src="{{ asset('logo/logo.png') }}" class="w-full h-full object-contain" alt="logo">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-2">
                                    <label class="text-xs text-[#000000] block mb-1">Hauptfarbe</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="wiz-brand-color" value="#93c21c" oninput="App.updateBranding()" class="w-10 h-10 p-1 rounded cursor-pointer border bg-white">
                                        <span id="color-hex-label" class="text-xs font-bold text-[#000000]">#93c21c</span>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>

                </div>
                <div class="border-t border-slate-100 pt-8 mt-4 flex justify-end sticky bottom-0 bg-white pb-2">
                    <button id="wiz-btn-start" onclick="App.startQuote()"
                            class="btn-primary flex items-center gap-3 text-lg px-8 py-3 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                            disabled>
                        <span>Starten</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: EDITOR -->
    <div id="view-editor" class="view-section flex-1 overflow-hidden relative bg-slate-100">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 shrink-0 z-50 shadow-sm no-print">
            <div class="flex items-center gap-4">
                <button onclick="App.toggleSidebar('left')" class="text-[#000000] hover:text-[#93c21c] w-8 h-8 rounded hover:bg-slate-100 flex items-center justify-center border border-transparent hover:border-slate-300"><i class="fa-solid fa-bars"></i></button>
                <div class="h-6 w-px bg-slate-200"></div>
                <div class="font-bold text-[#000000] flex items-center gap-2">
                    <span id="editor-doc-type-label" class="text-[#93c21c]">Angebot</span>
                    <span class="text-xs text-[#000000] font-normal">| <span id="lbl-total-pages">1</span> Seiten</span> 
                </div>
            </div>

            <!-- MAIN TABS (A4 / LIST) -->
            <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
            <button id="main-tab-list"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-[#000000] hover:text-[#93c21c]"
                    onclick="App.Tabs.switch('list')">
                <i class="fa-solid fa-list-check mr-2"></i>Listenansicht
            </button>
            <button id="main-tab-a4"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-[#000000] hover:text-[#93c21c] bg-white shadow"
                    onclick="App.Tabs.switch('a4')"> Druckansicht
                <i class="fa-solid fa-file-lines mr-2"></i>
            </button>
            
            <button id="main-tab-settings" class="px-3 py-1.5 rounded-lg text-xs font-black text-[#000000] hover:text-[#93c21c]" onclick="App.Tabs.switch('settings')">
                <i class="fa-solid fa-sliders mr-2"></i>Einstellung
            </button>

            <button id="main-tab-templates"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-[#000000] hover:text-[#93c21c]"
                    onclick="App.Tabs.switch('templates')">
                <i class="fa-solid fa-layer-group mr-2"></i>Vorlagen
            </button>

            <button id="main-tab-bio"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-[#000000] hover:text-[#93c21c]"
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
                        class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded-xl text-sm font-bold text-[#000000] hover:text-[#93c21c] flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>Aktionen</span>
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </button>

                        <div id="editor-actions-menu"
                            class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-[80]">
                            
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#000000]">Editor Menü</div>
                                <div class="text-sm font-black text-[#000000]">Seiten & Ansicht</div>
                            </div>

                            <div class="p-2">

                                <button type="button"
                                        onclick="App.loadBackWizard(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#000000] hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                        <i class="fa-solid fa-magic-wand-sparkles"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Wizard öffnen</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Kunde & Projekt-Basis ändern</span>
                                    </span>
                                </button>
                                <button type="button"
                                        onclick="App.addPageAfterCurrent(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#000000] hover:bg-[#f7fee7] hover:text-[#7ca816] transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-[#f7fee7] text-[#7ca816] flex items-center justify-center">
                                        <i class="fa-solid fa-file-circle-plus"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Neue Seite</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Seite nach der aktuellen Seite einfügen</span>
                                    </span>
                                </button>

                                <button type="button"
                                        onclick="App.addSection(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#000000] hover:bg-[#f7fee7] hover:text-[#7ca816] transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-[#f7fee7] text-[#7ca816] flex items-center justify-center">
                                        <i class="fa-solid fa-folder-plus"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Abschnitt</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Neuen Abschnitt im Angebot anlegen</span>
                                    </span>
                                </button>

                                <div class="mx-2 my-2 h-px bg-slate-100"></div>

                                <button type="button"
                                        onclick="App.UserPrefsModal.open(); App.closeEditorActionsMenu();"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#000000] hover:bg-slate-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-[#000000] flex items-center justify-center">
                                        <i class="fa-solid fa-display"></i>
                                    </span>
                                    <span class="flex-1 text-left">
                                        <span class="block font-bold">Ansicht anpassen</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Spalten & Start-Tab einstellen</span>
                                    </span>
                                </button>
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-[#000000] flex items-center justify-center">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-bold text-[#000000]">Versteckte anzeigen</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Ausgeblendete Positionen sichtbar machen</span>
                                    </span>
                                    <input type="checkbox"
                                        id="show-hidden-toggle"
                                        onchange="App.renderQuotePage()"
                                        checked
                                        class="accent-[#93c21c] w-4 h-4">
                                </label>

                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                    <span class="w-9 h-9 rounded-xl bg-slate-100 text-[#000000] flex items-center justify-center">
                                        <i class="fa-solid fa-images"></i>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-bold text-[#000000]">Miniaturseiten anzeigen</span>
                                        <span class="block text-[11px] text-[#000000] font-medium">Linke Seitenvorschau ein-/ausblenden</span>
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
                    <button onclick="App.Navigation.exitEditor()"
                            class="bg-white border border-slate-300 text-[#000000] hover:text-red-600 hover:border-red-200 px-4 py-1.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Beenden</span>
                    </button>
                    <button onclick="App.openPrintPreview()"
                            class="bg-slate-800 text-white hover:bg-slate-700 px-3 py-1.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <i class="fa-solid fa-print"></i>
                        <span>Druck-Vorschau</span>
                    </button>
                </div>
                <button id="calc-sidebar-toggle-btn"
                        onclick="App.toggleSidebar('right')"
                        class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-[#000000] hover:text-[#93c21c] flex items-center gap-2 transition-colors">
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

                       <button id="tab-attachments" class="sq-side-main-tab relative" onclick="App.Attachments.toggle()">
                            <i class="fa-solid fa-paperclip"></i>
                            <span>Dokumente</span>
                            <span id="badge-attachments" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full hidden shadow-sm border border-white">0</span>
                        </button> 
                    </div>
                </div>

                <!-- LIBRARY -->
                <div id="sidebar-content-lib" class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
                    <div class="sq-side-toolbar">
                        <div class="relative">
                            <input type="text"
                                id="sidebar-search"
                                oninput="State.libraryPage=1; App.renderSidebar()"
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
                                <div class="text-sm font-black text-[#000000]">Sticker, Logos & freie Bilder</div>
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
                            <div class="mt-2 flex items-start gap-5">
                                <div id="doc-cust-photo-wrap"
                                    class="hidden w-24 h-24 rounded-full overflow-hidden border-4 border-slate-200 bg-slate-50 shrink-0">
                                    <img id="doc-cust-photo"
                                        src=""
                                        alt="Kunde"
                                        class="w-full h-full object-cover">
                                </div>

                                <div>
                                    <div class="text-[9px] text-[#000000] mb-6 editable-field w-fit" contenteditable="true" id="doc-company-header">
                                        SOLAR ASPEKT GmbH • Am Kappengraben 10 • 61273 Wehrheim
                                    </div>

                                    <div class="text-[13px] leading-relaxed text-[#000000]">
                                        <div class="font-bold mb-1 editable-field w-fit" contenteditable="true">Herr</div>
                                        <div id="doc-cust-name" class="font-bold mb-1 editable-field w-fit" contenteditable="true">Max Mustermann</div>
                                        <div id="doc-cust-addr" class="editable-field w-fit whitespace-pre-line" contenteditable="true">
                                            Musterstraße 10<br>12345 Musterstadt
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end">
                                <div class="text-right mb-10">
                                    <div class="text-2xl font-black tracking-tight" id="doc-logo-text" style="color:var(--brand-color);">
                                        SOLAR ASPEKT
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-[10px] font-bold mb-1 uppercase tracking-wider" style="color:var(--brand-color);">
                                        Ihr Ansprechpartner
                                    </div>
                                    <div class="pr-3 py-1" style="border-right:2px solid var(--brand-color);">
                                        <div id="doc-contact-name" class="font-bold text-sm text-[#000000] editable-field" contenteditable="true">Herr Yama Nuri</div>
                                        <div id="doc-contact-details" class="text-[11px] text-[#000000] mt-1 editable-field" contenteditable="true">
                                            Tel: 0 60 81/68 288 78<br>E-Mail: anfrage@solar-aspekt.de
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-10 flex justify-between items-end pb-4">
                            <div><div class="text-[11px] text-[#000000] uppercase tracking-wide font-bold mb-1" id="lbl-doc-id-name">Angebotsnummer</div><div class="text-lg font-bold text-[#000000]  rounded py-1 w-40"><input type="text" id="doc-offer-id" value="SA-AG25342" oninput="App.syncDocData('offerId', this.value)" class="bg-transparent outline-none w-full text-[#000000] font-bold"></div></div>
                            <div class="text-right"><div class="text-[11px] text-[#000000] uppercase tracking-wide font-bold mb-1">Kundennummer</div><div class="text-sm font-bold text-[#000000]   rounded py-1 w-32 inline-block"><input type="text" id="doc-cust-id" value="KD-1005" oninput="App.syncDocData('custId', this.value)" class="bg-transparent outline-none w-full text-right"></div><div class="text-[12px] text-[#000000] editable-field" contenteditable="true" id="doc-date-line">Wehrheim, 27.08.2025</div></div>
                        </div>
                        <div class="mb-8">
                            <div id="doc-main-title" class="p-2 -ml-2 rounded border border-dashed border-transparent hover:border-[#93c21c] hover:bg-slate-50 cursor-pointer transition editable-field" style="font-size: 20px; font-weight: bold; color: var(--brand-color); text-transform: uppercase; line-height: 1.2;" onclick="App.openMainTitleModal()">
                                 
                            </div>
                        </div> 
                        <div id="doc-cover-text"
                            class="text-[13px] text-[#000000] leading-relaxed space-y-4 p-2 hover:bg-slate-50 rounded -ml-2 border border-dashed border-transparent hover:border-[#93c21c] cursor-pointer transition"
                            onclick="App.openCoverTextModal()">
                            <p>Sehr geehrter Herr <span id="doc-cust-lastname">Mustermann</span>,</p>
                            <p>wir freuen uns, Ihnen dieses Dokument unterbreiten zu dürfen.</p>
                            <p>Mit sonnigen Grüßen<br><span class="font-bold" id="doc-team-name">Ihr SOLAR-ASPEKT-Team</span></p>
                        </div>
                        <div class="mt-auto border-t-2  pt-4 grid grid-cols-4 gap-2 text-[9px] text-[#000000] leading-tight" style="border-top-color: var(--brand-color);">
                            <div class="editable-field" contenteditable="true" id="footer-col-1"><span class="font-bold text-[#000000]" id="footer-company">SOLAR ASPEKT GmbH</span><br>Am Kappengraben 10<br>61273 Wehrheim</div>
                            <div class="editable-field" contenteditable="true" id="footer-col-2"><span class="font-bold text-[#000000]"></span><br>Tel. 0 60 81/68 288 78<br>hallo@solar-aspekt.de</div>
                            <div class="editable-field" contenteditable="true" id="footer-col-3"><span class="font-bold text-[#000000]"></span><br>Volksbank Frankfurt<br>IBAN: DE12 3456...</div>
                            <div class="editable-field" contenteditable="true" id="footer-col-4"><span class="font-bold text-[#000000]"></span><br>AG Bad Homburg HRB 12036<br>GF: Yama Nuri</div>
                        </div>
                    </div>
                    <!-- Dynamic Page Container -->
                    <div id="position-pages-container" class="flex flex-col gap-8 w-full items-center"></div>
                </main>


                  <main id="panel-list" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="w-full max-w-[96vw] mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div id="listview-root" class="p-4 bg-[#c1c8cf]"></div>
                        </div>
                    </div>
                </main>

                <main id="panel-settings" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="max-w-7xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200">
                                <div class="text-xs font-black text-[#000000] uppercase tracking-wider">Konfiguration</div>
                                <div class="text-lg font-black text-[#000000]">Kalkulations-Einstellungen</div>
                            </div>
                            <div id="settings-root" class="p-6"></div>
                        </div>
                    </div>
                </main>

                <main id="panel-templates" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="max-w-7xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200">
                                <div class="text-xs font-black text-[#000000] uppercase tracking-wider">Vorlagen</div>
                                <div class="text-lg font-black text-[#000000]">Angebot aus Vorlage erstellen</div>
                            </div>

                            <div id="templates-root" class="p-6"></div>
                        </div>
                    </div>
                </main>

                <main id="panel-bio" class="hidden flex-1 h-full overflow-y-auto scroller bg-slate-100/50 p-6">
                    <div class="max-w-6xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200">
                                <div class="text-xs font-black text-[#000000] uppercase tracking-wider">Historie</div>
                                <div class="text-lg font-black text-[#000000]">Biografie & Online-Bearbeiter</div>
                            </div>
                            <div id="bio-root" class="p-6"></div>
                        </div>
                    </div>
                </main>


            </div>

            <!-- RIGHT SIDEBAR -->
            <aside id="sidebar-right"
                class="bg-white border-l border-slate-200 flex flex-col z-20 shadow-lg flex-shrink-0 sidebar-panel sidebar-collapsed no-print">
                <div class="calc-resizer" id="calc-resizer"></div>
                <div class="p-4 border-b border-slate-200 bg-[#f7fee7] flex justify-between items-center">
                <h3 class="font-bold text-[#6b8e12] text-sm uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    Kalkulations-Sidebar
                </h3>
                <div class="flex items-center gap-1"><span class="text-[10px] font-bold text-[#000000]">MwSt</span><input type="number" id="global-tax" value="19" onchange="App.updateTaxRate(this.value)" class="w-10 text-xs border rounded text-center font-bold text-[#000000]"><span class="text-[10px] text-[#000000]">%</span></div></div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4 scroller bg-slate-50/50" id="calc-sidebar-content"></div>
                <div class="p-6 bg-white border-t border-slate-200"><div class="flex justify-between items-end mb-1"><span class="text-xs text-[#000000] uppercase font-bold">Netto</span><span class="text-sm  text-[#000000]" id="sidebar-grand-net">0,00 €</span></div><div class="flex justify-between items-end mb-4"><span class="text-xs text-[#000000] uppercase font-bold">MwSt (<span id="lbl-tax-rate">19</span>%)</span><span class="text-sm  text-[#000000]" id="sidebar-grand-gross">0,00 €</span></div><div class="pt-4 border-t border-slate-100"><div class="text-xs text-[#93c21c] font-bold uppercase mb-1">Gesamtinvestition</div><div class="text-3xl font-bold text-[#000000]  tracking-tight" id="sidebar-grand-total">0,00 €</div></div></div>
            </aside>

            <aside id="sidebar-attachments"
    class="bg-white border-l border-slate-200 flex flex-col sidebar-attachments collapsed unpinned no-print">
    <div class="attachment-resizer" id="attachment-resizer"></div>

    <div class="p-4 border-b border-slate-200 bg-[#f8fafc] flex justify-between items-center shrink-0">
        <h3 class="font-bold text-[#000000] text-sm uppercase tracking-wide flex items-center gap-2">
            <i class="fa-solid fa-paperclip text-[#93c21c]"></i> Dokumente
        </h3>
        <div class="flex items-center gap-1">
            <button onclick="App.Attachments.togglePin()" id="btn-pin-attachments"
                class="w-8 h-8 flex items-center justify-center rounded text-slate-400 hover:bg-slate-200 hover:text-[#93c21c] transition-colors"
                title="Sidebar anheften/lösen">
                <i class="fa-solid fa-thumbtack"></i>
            </button>
            <button onclick="App.Attachments.toggle()"
                class="w-8 h-8 flex items-center justify-center rounded text-slate-400 hover:bg-red-100 hover:text-red-500 transition-colors"
                title="Schließen">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    </div>

    <div id="inline-attachment-viewer" class="hidden flex-col bg-slate-800 text-white border-b border-slate-700 p-3 shrink-0 relative resize-y overflow-auto" style="min-height: 250px; max-height: 60vh;">
        <div class="flex justify-between items-center mb-2">
            <span id="inline-viewer-title" class="text-xs font-bold truncate pr-4 text-slate-200"></span>
            <button onclick="document.getElementById('inline-attachment-viewer').classList.add('hidden')" class="text-slate-400 hover:text-red-400 transition-colors bg-white/10 hover:bg-white/20 w-6 h-6 rounded-full flex items-center justify-center shrink-0">
                <i class="fa-solid fa-times text-xs"></i>
            </button>
        </div>
        <div id="inline-viewer-content" class="w-full flex-1 rounded shadow-inner bg-black/50 flex items-center justify-center overflow-hidden">
        </div>
    </div>

    <div class="p-3 border-b border-slate-100 bg-white shrink-0">
        <div class="relative">
            <input type="text" id="attachment-search" oninput="App.Attachments.filterList()" placeholder="Dokumente durchsuchen..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 pl-9 text-xs font-bold text-[#000000] outline-none focus:border-[#93c21c] transition-colors">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        </div>
    </div>

    <div class="p-4 border-b border-slate-100 bg-white shrink-0">
        <input type="file" id="attachment-file-input" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
        <div onclick="document.getElementById('attachment-file-input').click()"
            ondragover="event.preventDefault(); this.classList.add('border-[#93c21c]', 'bg-[#f7fee7]')"
                        ondragleave="this.classList.remove('border-[#93c21c]', 'bg-[#f7fee7]')"
                        ondrop="App.Attachments.handleDrop(event)"
                        class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-[#93c21c] hover:bg-[#f7fee7] transition-all">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-300 mb-2"></i>
                        <div class="text-sm font-bold text-[#000000]">Dateien ablegen oder klicken</div>
                        <div class="text-[10px] text-slate-500 mt-1">Bilder & PDFs (Max 20MB)</div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-3 scroller bg-slate-50/50" id="attachment-list">
                </div>
            </aside>


            <div id="clipboard-sidebar" class="fixed right-0 top-1/4 w-72 bg-white border border-slate-200 shadow-2xl rounded-l-2xl z-[150] transition-transform transform translate-x-full">
                <div class="bg-[#93c21c] text-white p-3 rounded-tl-2xl flex justify-between items-center cursor-pointer" onclick="App.Clipboard.toggle()">
                    <h3 class="font-bold text-sm flex items-center gap-2"><i class="fa-solid fa-paste"></i> Zwischenablage</h3>
                    <button onclick="App.Clipboard.clear()" class="text-xs bg-white/20 px-2 py-1 rounded hover:bg-white/40 transition">Leeren</button>
                </div>
                <div id="clipboard-items" class="p-3 max-h-96 overflow-y-auto space-y-2 bg-slate-50">
                    <div class="text-xs text-[#000000] text-center py-4">Zwischenablage ist leer</div>
                </div>
            </div>

            <button onclick="App.Clipboard.toggle()" class="fixed right-0 top-1/3 bg-[#93c21c] text-white w-14 h-12 flex items-center justify-center rounded-l-xl shadow-lg z-[140] hover:w-16 transition-all">
                <i class="fa-solid fa-clipboard text-xl pl-1"></i>
                
                <span id="clipboard-badge" class="absolute -top-2 -left-2 bg-red-500 text-white text-[11px] font-black min-w-[24px] h-[24px] flex items-center justify-center rounded-full hidden shadow-md border-2 border-white">
                    0
                </span>
            </button>

        </div>
    </div>

    <!-- PRINT PREVIEW OVERLAY -->
    <div id="print-preview-modal" class="fixed inset-0 z-[200] hidden bg-slate-900/95 backdrop-blur-sm flex flex-col">
        <div class="h-16 bg-slate-800 flex items-center justify-between px-6 text-white shrink-0 shadow-md no-print">
            <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-print"></i> Druckvorschau (Aktive Positionen)</h3>
            <div class="flex gap-4">
                <button onclick="window.print()" class="bg-[#93c21c] hover:brightness-110 px-6 py-2 rounded font-bold text-sm shadow transition">Drucken</button>
                <button onclick="App.closePrintPreview()" class="text-[#000000] hover:text-white transition"><i class="fa-solid fa-times text-2xl"></i></button>            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-8 flex flex-col items-center gap-8" id="print-preview-content"></div>
    </div>

    <!-- MODALS (Settings, Badges, Sets) -->
    <div id="pos-settings-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closePosSettings()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-[450px] overflow-hidden animate-fadeIn"><div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-[#000000]">Position bearbeiten</h3><button onclick="App.closePosSettings()" class="text-[#000000]"><i class="fa-solid fa-times"></i></button></div><div class="p-4 space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-[#000000] mb-1">Menge</label><input type="number" step="0.01" id="setting-qty" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div>
            <div>
                <label class="block text-xs font-bold text-[#000000] mb-1">Einheit</label>
                <select id="setting-unit" class="w-full border rounded p-2 text-sm bg-white"></select>
            </div>
        <div><label class="block text-xs font-bold text-[#000000] mb-1">Einkaufspreis (EK)</label><input type="number" id="setting-ek" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div><div><label class="block text-xs font-bold text-[#000000] mb-1">Marge (%)</label><input type="number" id="setting-margin" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div></div><div class="bg-[#f0fdf4] p-3 rounded border border-[#93c21c]"><div class="flex justify-between items-center"><span class="text-xs font-bold text-[#93c21c]">Verkaufspreis (VK) pro Einheit</span><input type="number" id="setting-vk" class="w-24 text-right bg-transparent font-bold  outline-none" oninput="App.calcPosSettings(true)"></div></div><div class="space-y-2 pt-2 border-t border-slate-100"><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-pauschal" class="accent-[#93c21c]"> <span>Als Pauschalposition</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-price" class="accent-[#93c21c]"> <span>Preise ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-numbering" class="accent-[#93c21c]"> <span>Nummerierung ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-image" class="accent-[#93c21c]"> <span>Bild ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-active" class="accent-[#93c21c]"> <span>Position Aktiv</span></label></div><button onclick="App.savePosSettings()" class="w-full bg-[#93c21c] text-white rounded py-2 font-bold text-sm">Speichern</button></div></div></div>    <!-- ✅ Set Modal (rewritten, clean + readable, same IDs/hooks kept) -->
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

                <p class="text-sm text-[#000000] mt-1 line-clamp-2" id="modal-desc">
                    Beschreibung
                </p>
                </div>

                <button
                type="button"
                onclick="App.closeModal()"
                class="shrink-0 w-9 h-9 rounded-full bg-white text-[#000000]
                        border border-slate-200 shadow-sm hover:bg-slate-50 hover:text-[#000000]
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
                <h4 class="text-sm font-bold text-[#000000]">Komponenten</h4>
                <span class="text-xs text-[#000000]">Material</span>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-[#000000] font-bold text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2">Komponenten</th>
                            <th class="px-4 py-2">Lieferant</th>
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
                <h4 class="text-sm font-bold text-[#000000]">Dienstleistung</h4>
                <span class="text-xs text-[#000000]">Arbeitszeit</span>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-[#000000] font-bold text-xs uppercase">
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
                <div class="text-xs text-[#000000]">
                Tipp: Klick außerhalb schließt das Fenster.
                </div>

                <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="App.closeModal()"
                    class="px-4 py-2 rounded-xl text-[#000000] font-semibold
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
            <div class="font-bold text-[#000000]" id="desc-modal-title">Position bearbeiten</div>
        </div>
        <button onclick="App.closeDescModal()" class="text-[#000000] hover:text-[#000000]">
            <i class="fa-solid fa-times"></i>
        </button>
        </div>

        <div class="p-4">
        <div id="desc-quill" class="bg-white"></div>
        <div class="flex items-center justify-between mt-3">
            <div class="text-xs text-[#000000]">
            Tipp: Inhalte werden als HTML gespeichert (für Angebot).
            </div>
            <div class="flex gap-2">
            <button onclick="App.closeDescModal()" class="px-4 py-2 rounded-lg text-[#000000] hover:bg-slate-100 font-bold text-sm">Abbrechen</button>
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
            <div id="toast-confirm-title" class="font-black text-[#000000]">Löschen?</div>
            <div id="toast-confirm-msg" class="text-sm text-[#000000] mt-1">
                Diese Aktion kann nicht rückgängig gemacht werden.
            </div>
            </div>
        </div>

        <div class="px-4 pb-4 flex items-center justify-end gap-2">
            <button id="toast-confirm-cancel"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#000000] font-bold text-sm">
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
                <h3 class="text-lg font-black text-[#000000] flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-[#93c21c]"></i> Dokument Speichern
                </h3>
                <button onclick="document.getElementById('save-quote-modal').classList.add('hidden')" class="text-[#000000] hover:text-[#000000]">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <div>
                        <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Speichern als...</label>
                        <div id="save-mode-notice" class="hidden text-[10px] text-orange-500 mb-2 font-bold italic">
                            * Dokument wurde ohne Kunde gestartet, Speichern nur als Vorlage möglich.
                        </div>
                        <div class="flex gap-3">
                        </div>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <label id="save-mode-offer-wrap" class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                            <input type="radio" name="save_mode" value="offer" checked class="accent-[#93c21c]" onchange="document.getElementById('template-name-wrap').classList.add('hidden')">
                            <span class="text-sm font-bold text-[#000000]">Kunden-Angebot</span>
                        </label>
                        
                        <label id="save-mode-update-wrap" class="hidden flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex flex-col justify-center gap-0.5 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="save_mode" id="radio-update-template" value="update_template" class="accent-[#93c21c]" onchange="document.getElementById('template-name-wrap').classList.add('hidden')">
                                <span class="text-sm font-bold text-[#000000]">Aktualisieren</span>
                            </div>
                            <span id="lbl-loaded-template-name" class="text-[10px] text-[#000000] font-bold ml-6 truncate w-32" title=""></span>
                        </label>

                        <label class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                            <input type="radio" name="save_mode" id="radio-new-template" value="template" class="accent-[#93c21c]" onchange="document.getElementById('template-name-wrap').classList.remove('hidden')">
                            <span class="text-sm font-bold text-[#000000]">Neu als Vorlage</span>
                        </label>
                    </div>
                </div>

                <div id="template-name-wrap" class="hidden flex flex-col gap-4 mt-4 border-t border-slate-200 pt-4">
                    <div>
                        <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Name der Vorlage *</label>
                        <input type="text" id="save-template-name" placeholder="z.B. PV-Standard Paket 10kWp" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:border-[#93c21c] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Beschreibung</label>
                        <textarea id="save-template-desc" placeholder="Kurze Beschreibung der Vorlage..." class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:border-[#93c21c] outline-none resize-none h-20"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Abteilung (Department)</label>
                            <select id="save-template-department" class="w-full template-select2">
                                <option value="">Bitte wählen...</option>
                                </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Gewerk</label>
                            <select id="save-template-article-group" class="w-full template-select2">
                                <option value="">Bitte wählen...</option>
                                </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Marke (Brand)</label>
                            <select id="save-template-brand" class="w-full template-select2">
                                <option value="">Bitte wählen...</option>
                                </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-1.5">Lieferant (Distributor)</label>
                            <select id="save-template-distributor" class="w-full template-select2">
                                <option value="">Bitte wählen...</option>
                                </select>
                        </div>
                    </div>
                </div>
                
                <div id="save-loading-indicator" class="hidden text-sm text-[#000000] flex items-center gap-2 justify-center py-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-[#93c21c]"></i> Speichervorgang läuft...
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button onclick="document.getElementById('save-quote-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl text-[#000000] font-bold hover:bg-slate-200 transition-colors text-sm">Abbrechen</button>
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
                    <div class="text-xl font-black text-[#000000] mt-1">Dieses Angebot wird bereits bearbeitet</div>
                </div>

                <div class="p-6">
                    <div id="offer-lock-user-box" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 bg-slate-50">
                        <img id="offer-lock-user-avatar" src="{{ asset('images/gender/male.png') }}" class="w-14 h-14 rounded-full object-cover border border-slate-200" alt="avatar">
                        <div>
                            <div class="text-sm text-[#000000]">Aktiver Benutzer</div>
                            <div id="offer-lock-user-name" class="text-lg font-black text-[#000000]">-</div>
                        </div>
                    </div>

                    <div class="mt-5 text-sm text-[#000000] leading-7">
                        Sie können momentan nicht arbeiten, weil
                        <span id="offer-lock-inline-name" class="font-black text-[#000000]">-</span>
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


    <div id="template-apply-modal" class="fixed inset-0 z-[310] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="App.TemplateTab.closeApplyModal()"></div>

    <div class="absolute top-1/2 left-1/2 w-[96vw] max-w-3xl -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#93c21c]">Vorlage verwenden</div>
                <div class="text-lg font-black text-[#000000]" id="template-apply-title">Vorlage übernehmen</div>
            </div>

            <button type="button"
                    onclick="App.TemplateTab.closeApplyModal()"
                    class="text-[#000000] hover:text-[#000000]">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <div class="rounded-2xl border border-[#93c21c]/20 bg-[#f7fee7] p-4">
                <div class="text-xs font-black uppercase tracking-wider text-[#6b8e12]">Ausgewählte Vorlage</div>
                <div id="template-apply-name" class="text-lg font-black text-[#000000] mt-1">—</div>
                <div id="template-apply-meta" class="text-sm text-[#000000] mt-1">—</div>
            </div>

            <!-- Kunde -->
            <div>
                <label class="block text-sm font-bold text-[#000000] mb-2">1. Kunde</label>

                <div class="relative">
                    <input type="text"
                           id="template-customer-search"
                           oninput="App.TemplateTab.filterCustomers()"
                           onfocus="App.TemplateTab.filterCustomers()"
                           placeholder="Kunde suchen..."
                           class="w-full border border-slate-300 rounded-xl p-3 pl-10 text-sm outline-none focus:border-[#93c21c] bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#000000]"></i>

                    <div id="template-customer-dropdown"
                         class="absolute w-full bg-white border border-slate-200 rounded-b-xl shadow-xl z-50 hidden max-h-52 overflow-y-auto mt-1">
                    </div>
                </div>

                <div id="template-customer-selected"
                     class="hidden mt-3 p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div id="template-sel-cust-name" class="font-black text-[#000000] truncate"></div>
                        <div id="template-sel-cust-addr" class="text-xs text-[#000000] truncate"></div>
                    </div>

                    <button type="button"
                            onclick="App.TemplateTab.clearCustomer()"
                            class="text-[#000000] hover:text-red-500 shrink-0">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Objekt -->
            <div id="template-object-step" class="opacity-50 pointer-events-none transition-opacity">
                <label class="block text-sm font-bold text-[#000000] mb-2">2. Objekt / Produkt</label>

                <select id="template-object-select"
                        multiple
                        onchange="App.TemplateTab.selectObject()"
                        class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:border-[#93c21c] bg-white">
                </select>

                <div class="mt-2 text-xs text-[#000000]">
                    Ausgewählt:
                    <span id="template-object-count" class="font-bold">0</span>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2">
            <button type="button"
                    onclick="App.TemplateTab.closeApplyModal()"
                    class="px-5 py-2.5 rounded-xl text-[#000000] font-bold hover:bg-slate-200 transition-colors text-sm">
                Abbrechen
            </button>

            <button type="button"
                    id="template-apply-confirm-btn"
                    onclick="App.TemplateTab.confirmApplyTemplate()"
                    disabled
                    class="px-6 py-2.5 rounded-xl bg-[#93c21c] text-white font-black shadow-md hover:brightness-105 transition-all text-sm flex items-center gap-2 opacity-50 cursor-not-allowed">
                <i class="fa-solid fa-check"></i>
                Vorlage übernehmen
            </button>
        </div>
    </div>
</div>


<div id="user-prefs-modal" class="fixed inset-0 z-[300] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="App.UserPrefsModal.close()"></div>
    
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-[500px] max-h-[90vh] flex flex-col animate-fadeIn">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-lg font-black text-[#000000] flex items-center gap-2">
                <i class="fa-solid fa-display text-[#93c21c]"></i> Meine Ansicht-Einstellungen
            </h3>
            <button onclick="App.UserPrefsModal.close()" class="text-[#000000] hover:text-[#000000]">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-6 space-y-6 overflow-y-auto scroller">
            
            <div>
                <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-2">Standard-Ansicht beim Start</label>
                <div class="flex gap-3">
                    <label class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                        <input type="radio" name="pref_default_tab" value="list" class="accent-[#93c21c]">
                        <span class="text-sm font-bold text-[#000000]">Listenansicht</span>
                    </label>
                    <label class="flex-1 border border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#93c21c] flex items-center gap-2 bg-white has-[:checked]:border-[#93c21c] has-[:checked]:bg-[#f7fee7] transition-all">
                        <input type="radio" name="pref_default_tab" value="a4" class="accent-[#93c21c]">
                        <span class="text-sm font-bold text-[#000000]">Druckansicht (A4)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-2">Panel-Sichtbarkeit</label>
                <div class="space-y-2">
                    <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50">
                        <span class="text-sm font-bold text-[#000000]">Miniaturansicht (Links) immer einblenden</span>
                        <input type="checkbox" id="pref_show_thumbnails" class="w-4 h-4 accent-[#93c21c]">
                    </label>
                    <label class="flex items-center justify-between p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50">
                        <span class="text-sm font-bold text-[#000000]">Kalkulations-Sidebar (Rechts) immer einblenden</span>
                        <input type="checkbox" id="pref_show_sidebar" class="w-4 h-4 accent-[#93c21c]">
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#000000] uppercase tracking-wide mb-2">Sichtbare Spalten (Listenansicht)</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-4 rounded-xl border border-slate-200" id="pref-columns-container">
                    </div>
            </div>

        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2 rounded-b-2xl">
            <button onclick="App.UserPrefsModal.close()" class="px-5 py-2.5 rounded-xl text-[#000000] font-bold hover:bg-slate-200 transition-colors text-sm">Abbrechen</button>
            <button onclick="App.UserPrefsModal.save()" class="px-6 py-2.5 rounded-xl bg-[#93c21c] text-white font-black shadow-md hover:brightness-105 transition-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Speichern
            </button>
        </div>
    </div>
</div>


<div id="unsaved-changes-modal" class="fixed inset-0 z-[1100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="App.Navigation.cancel()"></div>
    
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fadeIn">
        <div class="p-8 text-center">
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            
            <h3 class="text-2xl font-black text-[#000000] mb-2">Nicht gespeichert!</h3>
            <p class="text-[#000000] leading-relaxed">
                Sie haben Änderungen am Angebot vorgenommen. Wenn Sie jetzt gehen, werden diese <span class="font-bold text-red-500">unwiderruflich gelöscht</span>.
            </p>
        </div>

        <div class="bg-slate-50 p-6 flex flex-col gap-3">
            <button onclick="App.openSaveModal(); App.Navigation.cancel();"
                    class="w-full bg-[#93c21c] text-white font-black py-4 rounded-2xl shadow-lg hover:brightness-105 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                Änderungen jetzt speichern
            </button>
            
            <div class="flex gap-3">
                <button onclick="App.Navigation.cancel()"
                        class="flex-1 bg-white border border-slate-200 text-[#000000] font-bold py-3 rounded-2xl hover:bg-slate-100 transition-all text-sm">
                    Weiterarbeiten
                </button>
                <button id="btn-confirm-leave"
                        class="flex-1 bg-red-50 text-red-600 font-bold py-3 rounded-2xl hover:bg-red-100 transition-all text-sm">
                    Verwerfen
                </button>
            </div>
        </div>
    </div>
</div>

<div id="product-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="App.closeProductModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <div class="min-w-0">
                <div class="text-[10px] font-bold text-[#93c21c] uppercase tracking-wider mb-1">Produkt-Details</div>
                <h3 class="font-bold text-[#000000] text-lg truncate" id="pm-title">Lade...</h3>
            </div>
            <button onclick="App.closeProductModal()" class="text-[#000000] hover:text-[#000000] w-8 h-8 rounded-full border border-slate-200 bg-white shadow-sm flex items-center justify-center shrink-0">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 scroller flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-1/3 shrink-0">
                <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 aspect-square flex items-center justify-center p-2">
                    <img id="pm-image" src="" class="max-w-full max-h-full object-contain">
                </div>
            </div>
            <div class="w-full md:w-2/3 space-y-4">
                <div>
                    <div class="text-xs text-[#000000] uppercase tracking-wide font-bold">Artikelnummer</div>
                    <div class="text-sm font-bold text-[#000000]" id="pm-artno">-</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-[#000000] uppercase font-bold">Marke</div>
                        <div class="text-sm font-bold text-[#000000] truncate" id="pm-brand">-</div>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-[#000000] uppercase font-bold">Lieferant</div>
                        <div class="text-sm font-bold text-[#000000] truncate" id="pm-dist">-</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <div class="text-[10px] text-[#000000] uppercase font-bold">EK Preis</div>
                        <div class="text-lg font-black text-[#000000]" id="pm-ek">0,00 €</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-[#000000] uppercase font-bold">VK Preis (Kalkuliert)</div>
                        <div class="text-lg font-black text-[#93c21c]" id="pm-vk">0,00 €</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="App.closeProductModal()" class="px-5 py-2.5 rounded-xl text-[#000000] font-bold hover:bg-slate-200 transition-colors text-sm">Schließen</button>
            <button id="pm-add-btn" class="px-6 py-2.5 rounded-xl bg-[#93c21c] text-white font-black shadow-md hover:brightness-105 transition-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Zum Angebot hinzufügen
            </button>
        </div>
    </div>
</div>

<div id="attachment-viewer-modal" class="fixed inset-0 z-[1500] hidden flex flex-col bg-slate-900/95 backdrop-blur-sm">
    <div class="h-16 flex justify-between items-center px-6 bg-slate-900 text-white shrink-0 shadow-lg">
        <div class="font-bold flex items-center gap-3 text-lg">
            <i class="fa-solid fa-file-lines text-[#93c21c]"></i> 
            <span id="viewer-title">Dokumentenansicht</span>
        </div>
        <button onclick="App.Attachments.closeViewer()" class="text-slate-300 hover:text-red-400 transition-colors bg-white/10 hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
    </div>
    <div class="flex-1 overflow-hidden relative flex justify-center items-center p-6" id="viewer-content">
        </div>
</div>


<script>
    window.currentEmployeeId = {{ auth()->user()->name }};
</script>

<script>
    // --- CONFIGURATION ---
    const API_BASE = '/offers';

    const State = {
            libraryPage: 1,
            libraryLastPage: 1,
            selectedItems: new Set(),
            hasUnsavedChanges: false,
            docStatus: '',
            isSnapshot: false,
            customer: null,
            object: null,
            projectDate: '',
            docType: 'Angebot',
            sections: [],
            coverTextHtml: '',
            mainTitleHtml: '',
            offerId: 'NEW',
            custId: '-',
            placedImages: [],
            toolsImages: [],
            taxRate: 19, 
            companyName: 'SOLAR ASPEKT',
            brandColor: '#93c21c',
            brandMode: 'text',          // 'text' | 'image'
            brandLogoUrl: '',           // selected logo url
            selectedBranchId: null,
            selectedBranch: null,
            companyFooter: {},
            editingBadge: null,
            editingImage: null,
            dragState: null,
            showThumbnails: false,
            templateItems: [],
            templateSearch: '',
            selectedTemplate: null, 
            loadedTemplateId: null,    
            loadedTemplateName: null,   
            prefill: {
                offer_id: null,
                offer_folder_id: null,
                customer_id: null,
                alternative_id: null,
                product_id: null,
                autoApplied: false
            },

            userPrefs: {
                defaultTab: 'list', // Default is list
                showThumbnails: false,
                showCalcSidebar: false,
                columns: null // Will be loaded from getDefaults
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
            supplier: "",
            distributor_name: "",
            distributor_article_no: "",
            distributor_id: null,
            distributor_price_id: null,
            skonto: 0,
            payment_terms: 0,
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
            unit: "Stk.",
            measure: "Stk.",

            // NEW: pricing basis
            price_unit_value: 1,     // e.g. 100
            price_unit_label: "Stk.", // e.g. m
            price_unit_text: "1 Stk.",

            vpe: 1,

            active: true,
            hidePrices: false,
            hideImage: true,
            hideNumbering: false,
            isPauschal: false,
            print_hidden: (overrides.depth || 0) > 0,       
            print_hidden_labor: true,

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
        const extractDistributorMeta = (src = {}) => {
            const distributor = src?.distributor || src?.supplier_obj || null;

            const distributorName =
                src?.supplier ||
                distributor?.name ||
                src?.distributor_name ||
                src?.supplier_name ||
                "";

            const paymentTermsRaw =
                src?.payment_terms ??
                src?.payment_days ??
                src?.zahlungsziel ??
                distributor?.payment_terms ??
                distributor?.payment_days ??
                null;

            return {
                supplier: distributorName,
                distributor_name: distributorName,

                distributor_article_no:
                    src?.distributor_article_no ||
                    src?.supplier_article_no ||
                    src?.article_no_supplier ||
                    src?.lieferanten_artikelnummer ||
                    distributor?.article_no ||
                    "",

                distributor_id:
                    src?.distributor_id ??
                    distributor?.id ??
                    null,

                distributor_price_id:
                    src?.distributor_price_id ??
                    src?.price_id ??
                    null,

                skonto: Number(
                    src?.skonto ??
                    src?.supplier_discount ??
                    src?.discount ??
                    distributor?.skonto ??
                    0
                ) || 0,

                payment_terms:
                    paymentTermsRaw !== null && paymentTermsRaw !== undefined && paymentTermsRaw !== ''
                        ? Number(paymentTermsRaw) || 0
                        : null,
            };
        };

    window.State = State;

    window.App = {
 
        openProductModal: async (id) => {
            const modal = document.getElementById('product-modal');
            if (!modal) return;

            const titleEl = document.getElementById('pm-title');
            const imgEl = document.getElementById('pm-image');
            const addBtn = document.getElementById('pm-add-btn');

            titleEl.textContent = 'Lade...';
            imgEl.src = App.placeholderImg('Lade...');
            document.getElementById('pm-artno').textContent = '-';
            document.getElementById('pm-brand').textContent = '-';
            document.getElementById('pm-dist').textContent = '-';
            document.getElementById('pm-ek').textContent = '0,00 €';
            document.getElementById('pm-vk').textContent = '0,00 €';

            modal.classList.remove('hidden');

            try {
                const res = await fetch(`${API_BASE}/products/${id}?context=angebot`, { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error('HTTP Error');
                const data = await res.json();

                titleEl.textContent = data.name || data.product || 'Produkt';
                imgEl.src = data.image || data.image_url || App.placeholderImg(data.name);
                imgEl.onerror = () => { imgEl.src = App.placeholderImg(data.name); };

                document.getElementById('pm-artno').textContent = data.article_no || '-';
                document.getElementById('pm-brand').textContent = data.brand_name || '-';
                document.getElementById('pm-dist').textContent = data.distributor_name || '-';
                document.getElementById('pm-ek').textContent = App.money(data.ek || data.purchase_price || 0) + ' €';
                document.getElementById('pm-vk').textContent = App.money(data.price || data.unit_price || 0) + ' €';

                addBtn.onclick = () => {
                    let sIdx = State.sections.findIndex(s => s && !s._pageBreak && !s.isLocked);
                    if (sIdx === -1) sIdx = App.addSection();
                    App.handleItemAdd(sIdx, id, 'product');
                    App.closeProductModal();
                };
            } catch (e) {
                console.error('Failed to load product details', e);
                titleEl.textContent = 'Fehler beim Laden';
            }
        },

        closeProductModal: () => {
            document.getElementById('product-modal')?.classList.add('hidden');
        },
        isDeal: () => {
            if (State.isSnapshot) return false; 
            
            const s = String(State.docStatus || '').trim().toLowerCase();
            return s === 'deal' || s === 'auftrag';
        },
        CompanyProfiles: {
            'solar-aspekt': {
                name: 'SOLAR ASPEKT GmbH',
                street: 'Am Kappengraben 10',
                color: '#93c21c',
                secondColor: '#74b2d4',
                city: '61273 Wehrheim',
                phone: '0 60 81/68 288 78',
                whatsapp: '0 60 81/68 288 72',
                email: 'hallo@solar-aspekt.de',
                web: 'www.solar-aspekt.de',
                bank: 'Frankfurter Volksbank',
                iban: 'DE83 5019 0000 6401 4059 66',
                bic: 'FFVBDEFF',
                register: 'AG Bad Homburg HRB 12036',
                tax: '003/243/5213/0',
                vat: 'DE278340406',
                gf: 'Geschäftsführer: Yama Nuri',
                contactPerson: 'Herr Yama Nuri',
                logoUrl: "{{ asset('logo/logo.png') }}"
            },
            'werk-studio': {
                name: 'WERK STUDIO Baukonzept GmbH',
                street: 'Am Kappengraben 10', 
                city: '61273 Wehrheim',
                color: '#a79e86',
                secondColor: '#672866', 
                phone: '0 60 81 / 53 25',
                whatsapp: '',
                email: 'kontakt@werk-studio.de',
                web: 'www.werk-studio.de',
                bank: 'Frankfurter Volksbank',
                iban: 'DE52 5019 0000 6501 4015 93',
                bic: 'FFVBDEFF',
                register: 'AG Bad Homburg HRB 13039',
                tax: 'DE297503456',
                vat: '',
                gf: 'Geschäftsführerin: Kathrin Nuri',
                contactPerson: 'Frau Kathrin Nuri',
                logoUrl: "{{ asset('logo/werk-studio.png') }}"
            }
        },

        Attachments: {
            isPinned: false,
            items: [],
            folderId: null,
            _sortable: null,
            filterQuery: '',

            init() {
                // Initialize Resizer
                const resizer = document.getElementById('attachment-resizer');
                const sidebar = document.getElementById('sidebar-attachments');
                let startX, startWidth;

                resizer.addEventListener('mousedown', (e) => {
                    startX = e.clientX;
                    startWidth = sidebar.getBoundingClientRect().width;
                    document.documentElement.addEventListener('mousemove', doDrag, false);
                    document.documentElement.addEventListener('mouseup', stopDrag, false);
                    resizer.classList.add('active');
                });

                const doDrag = (e) => {
                    // Because sidebar is on the right, dragging left increases width
                    let newWidth = startWidth + (e.clientX - startX);
                    if (newWidth < 280) newWidth = 280;
                    if (newWidth > 1200) newWidth = 1200;
                    sidebar.style.width = newWidth + 'px';
                    sidebar.style.transition = 'none';
                };

                const stopDrag = () => {
                    document.documentElement.removeEventListener('mousemove', doDrag, false);
                    document.documentElement.removeEventListener('mouseup', stopDrag, false);
                    resizer.classList.remove('active');
                    sidebar.style.transition = '';
                };

                // Hidden File Input Change
                document.getElementById('attachment-file-input').addEventListener('change', (e) => {
                    if (e.target.files.length) this.uploadFiles(e.target.files);
                    e.target.value = '';
                });
            },

           toggle() {
                const el = document.getElementById('sidebar-attachments');
                if (el.classList.contains('collapsed')) {
                    el.classList.remove('collapsed');
                    if (this.isPinned) el.classList.add('pinned');
                    this.loadFolderData(); // Loads ID and fetches files!
                } else {
                    el.classList.add('collapsed');
                }
            },

            togglePin() {
                this.isPinned = !this.isPinned;
                const el = document.getElementById('sidebar-attachments');
                const btn = document.getElementById('btn-pin-attachments');

                if (this.isPinned) {
                    el.classList.add('pinned');
                    el.classList.remove('unpinned');
                    btn.classList.add('text-[#93c21c]', 'bg-slate-100');
                    btn.classList.remove('text-slate-400');
                } else {
                    el.classList.add('unpinned');
                    el.classList.remove('pinned');
                    btn.classList.remove('text-[#93c21c]', 'bg-slate-100');
                    btn.classList.add('text-slate-400');
                }
            },

            loadFolderData() {
                if (!this.folderId && State.prefill && State.prefill.offer_folder_id) {
                    this.folderId = State.prefill.offer_folder_id;
                }

                // UNCOMMENTED AND FIXED: Always fetch if we have an ID
                if (this.folderId) {
                    this.fetchAttachments();
                }
            },

            handleDrop(e) {
                e.preventDefault();
                e.currentTarget.classList.remove('border-[#93c21c]', 'bg-[#f7fee7]');
                if (e.dataTransfer.files.length) {
                    this.uploadFiles(e.dataTransfer.files);
                }
            },

            async uploadFiles(files) {
                this.loadFolderData();
                if (!this.folderId) {
                    alert("Achtung: Angebot muss erst gespeichert werden, damit ein Ordner für Dateien existiert.");
                    return;
                }

                const formData = new FormData();
                Array.from(files).forEach(f => formData.append('files[]', f));
                if (State.offerId && State.offerId !== 'NEW') {
                    formData.append('offer_id', State.prefill.offer_id);
                }

                const list = document.getElementById('attachment-list');
                const loadingHtml = `<div id="att-loading" class="text-sm font-bold text-center text-[#93c21c] py-4"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Lade Dateien hoch...</div>`;
                list.insertAdjacentHTML('afterbegin', loadingHtml);

                try {
                    const res = await fetch(`/admin/offers/folders/${this.folderId}/attachments/upload`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: formData
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.items = data.attachments || [];
                        this.renderList();
                    } else {
                        alert(data.message || 'Fehler beim Upload');
                    }
                } catch (e) {
                    console.error("Upload error", e);
                    alert("Netzwerkfehler beim Upload.");
                } finally {
                    document.getElementById('att-loading')?.remove();
                }
            },

            async fetchAttachments() {
                if (!this.folderId) return;
                try {
                    // Only fetch if your backend has an endpoint for it. If not, the list will just populate on upload.
                    const res = await fetch(`/admin/offers/folders/${this.folderId}/attachments`, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        this.items = data.attachments || data || [];
                        this.renderList();
                    }
                } catch (e) {
                    console.log("No fetch endpoint provided. Waiting for manual uploads.", e);
                }
            },

            async deleteAttachment(id) {
                if (!confirm("Möchten Sie diese Datei wirklich unwiderruflich löschen?")) return;
                try {
                    const res = await fetch(`/admin/offers/folders/${this.folderId}/attachments/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.items = data.attachments || [];
                        this.renderList();
                    }
                } catch (e) { console.error(e); }
            },

            viewFile(url, type, name) {
                // Update to target the INLINE viewer instead of the modal
                const viewer = document.getElementById('inline-attachment-viewer');
                const titleEl = document.getElementById('inline-viewer-title');
                const contentEl = document.getElementById('inline-viewer-content');

                titleEl.innerText = name;

                if (type === 'pdf') {
                    contentEl.innerHTML = `<iframe src="${url}" class="w-full h-full bg-white border-0"></iframe>`;
                } else {
                    contentEl.innerHTML = `<img src="${url}" class="max-w-full max-h-full object-contain">`;
                }

                // Reveal the inline viewer
                viewer.classList.remove('hidden');
                viewer.classList.add('flex');
            },

            closeViewer() {
                document.getElementById('attachment-viewer-modal').classList.add('hidden');
                document.getElementById('viewer-content').innerHTML = '';
            },

            updateBadge() {
                const badge = document.getElementById('badge-attachments');
                if (!badge) return;
                const count = this.items ? this.items.length : 0;

                if (count > 0) {
                    badge.innerText = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            },

            // NEW: Method triggered by the search input
            filterList() {
                const input = document.getElementById('attachment-search');
                if (input) {
                    this.filterQuery = input.value.toLowerCase();
                    this.renderList(); // Re-render with active filter
                }
            },

            renderList() {
                this.updateBadge(); // Update the counter first
                
                const list = document.getElementById('attachment-list');
                if (!this.items || this.items.length === 0) {
                    list.innerHTML = `<div class="text-sm font-bold text-center text-slate-400 py-10"><i class="fa-solid fa-folder-open text-2xl mb-2 block"></i> Ordner ist leer.</div>`;
                    return;
                }

                // Filter items based on the search input
                const filteredItems = this.items.filter(it => 
                    (it.original_name || '').toLowerCase().includes(this.filterQuery)
                );

                if (filteredItems.length === 0) {
                    list.innerHTML = `<div class="text-xs text-center text-slate-400 font-bold py-10">Keine Dokumente für "${App.escapeHtml(this.filterQuery)}" gefunden.</div>`;
                    return;
                }

                list.innerHTML = filteredItems.map(it => `
                    <div data-id="${it.id}" class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm flex items-center justify-between group cursor-pointer hover:border-[#93c21c] transition-colors" onclick="App.Attachments.viewFile('${it.file_url}', '${it.file_type}', '${it.original_name}')">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="cursor-grab text-slate-300 hover:text-[#000000] p-1"><i class="fa-solid fa-grip-vertical"></i></div>
                            <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 text-slate-500">
                                ${it.file_type === 'pdf' ? '<i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>' : '<i class="fa-solid fa-file-image text-blue-500 text-xl"></i>'}
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-[#000000] truncate" title="${it.original_name}">${it.original_name}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">${(it.file_size / 1024).toFixed(1)} KB</div>
                            </div>
                        </div>
                        <button onclick="event.stopPropagation(); App.Attachments.deleteAttachment(${it.id})" class="text-slate-300 hover:text-red-500 p-2 opacity-0 group-hover:opacity-100 transition-all rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `).join('');

                // Initialize Sortable for Drag and Drop (Only enable if NOT currently searching)
                if (this._sortable) this._sortable.destroy();
                if (!this.filterQuery) {
                    this._sortable = new Sortable(list, {
                        animation: 150,
                        handle: '.fa-grip-vertical',
                        onEnd: async (evt) => {
                            const ids = Array.from(list.children).map(el => el.dataset.id).filter(id => id);
                            await fetch(`/admin/offers/folders/${this.folderId}/attachments/sort`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                                body: JSON.stringify({ ids: ids })
                            });
                        }
                    });
                }
            }
        },
        // This must be directly under App, NOT inside CompanyProfiles
        selectBranch: function(key) {
            const profile = App.getCompanyProfile(key);
            if (!profile) return;

            State.selectedBranch = profile;
            State.selectedBranchId = profile.id || null;
            State.companyName = profile.name || profile.branch || 'SOLAR ASPEKT';
            State.brandLogoUrl = profile.logoUrl || '';
            State.brandColor = profile.color || '#93c21c';
            State.secondColor = profile.secondColor || State.brandColor;
            State.brandMode = State.brandLogoUrl ? 'image' : 'text';
            State.companyFooter = App.getCompanyFooterSnapshot(profile);

            const colorInput = document.getElementById('wiz-brand-color');
            const nameInput = document.getElementById('wiz-brand-name');
            const logoInput = document.getElementById('wiz-brand-logo');

            if (colorInput) colorInput.value = State.brandColor;
            if (nameInput) nameInput.value = State.companyName;
            if (logoInput) logoInput.value = State.brandLogoUrl;

            const modeRadio = document.querySelector(`input[name="wiz-brand-mode"][value="${State.brandMode}"]`);
            if (modeRadio) modeRadio.checked = true;

            document.documentElement.style.setProperty('--brand-color', State.brandColor);
            document.documentElement.style.setProperty('--second-color', State.secondColor);

            App.updateCompanyFooter(profile);
            App.updateBranding();
        },

        init: () => {
            // --- 1. USER PRÄFERENZEN LADEN (PRIORITÄT) ---
            App.UserPrefs.load();

            // --- 2. STANDARD INITIALISIERUNG ---
            document.getElementById('wiz-date').valueAsDate = new Date();
            App.selectBranch(document.getElementById('wiz-company-select')?.value || window.DefaultBranchProfileKey || 'solar-aspekt');
            App.updateBranding();
            App.Attachments.init();
            
            // Kurzer Timeout für Library-Modus
            setTimeout(() => App.switchLibraryMode('group_sets'), 0);
            
            if (window.jQuery && $.fn.select2) {
                App.Wizard.initObjectSelect2();
                App.Wizard.setObjectDisabled(true);
                $('#wiz-object-select').val(null).trigger('change');
            }

            const coverEl = document.getElementById('doc-cover-text');
            if (coverEl && !State.coverTextHtml) {
                State.coverTextHtml = coverEl.innerHTML.trim();
            }
            
            // --- 3. EVENT LISTENERS ---
            document.addEventListener('click', e => {
                if(!e.target.closest('#wiz-customer-search') && !e.target.closest('#wiz-customer-dropdown')) {
                    document.getElementById('wiz-customer-dropdown')?.classList.add('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                const wrap = document.getElementById('editor-actions-menu-wrap');
                const menu = document.getElementById('editor-actions-menu');
                if (wrap && menu && !wrap.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });

            document.addEventListener('mousedown', (e) => {
                // If we click outside of ANY floating element, deselect them all
                if (!e.target.closest('.floating-element')) {
                    document.querySelectorAll('.floating-element').forEach(f => {
                        f.classList.remove('is-selected');
                        f.style.zIndex = '50';
                    });
                }
            });

            document.addEventListener('click', e => {
                if (!e.target.closest('#template-customer-search') && !e.target.closest('#template-customer-dropdown')) {
                    document.getElementById('template-customer-dropdown')?.classList.add('hidden');
                }
            });

            document.addEventListener('dragend', () => App.clearDragMode());

            // --- 4. DATA LOADING ---
            App.loadLaborOptions().catch(() => {});
            
            App.applyWizardPrefillFromUrl()
                .then(() => App.loadSavedDocumentIfAvailable())
                .catch(console.error);
        },
        isLockedSnapshot: () => {
            return State.isSnapshot === true;
        },

        applyLockState: () => {
            const locked = App.isLockedSnapshot();
            const sidebarLeft = document.getElementById('sidebar-left');
            const toggleLeftBtn = document.querySelector('button[onclick="App.toggleSidebar(\'left\')"]');

            if (locked) {
                if (sidebarLeft) {
                    sidebarLeft.classList.add('hidden');
                    sidebarLeft.classList.remove('flex');
                }
                if (toggleLeftBtn) toggleLeftBtn.classList.add('hidden');
                
                document.body.classList.add('is-locked-snapshot');

                // Show Blue Warning Banner
                const header = document.querySelector('header');
                if (header && !document.getElementById('locked-banner')) {
                    const banner = document.createElement('div');
                    banner.id = 'locked-banner';
                    banner.className = 'w-full bg-blue-600 text-white text-center py-2 text-xs font-bold z-[100] shadow-md';
                    banner.innerHTML = '<i class="fa-solid fa-clock-rotate-left mr-2"></i> ANGEBOTS-ANSICHT: Dies ist eine historische Momentaufnahme des ursprünglichen Angebots. Änderungen sind nicht möglich.';
                    header.parentNode.insertBefore(banner, header.nextSibling);
                }
            } else {
                document.body.classList.remove('is-locked-snapshot');
                const banner = document.getElementById('locked-banner');
                if (banner) banner.remove();

                if (sidebarLeft) {
                    sidebarLeft.classList.remove('hidden');
                    sidebarLeft.classList.add('flex');
                }
                if (toggleLeftBtn) toggleLeftBtn.classList.remove('hidden');
            }
        },
            handlePosDragOver: function(ev, el) {
                ev.preventDefault();
                if (App.isLibraryDrag()) {
                    el.classList.add('drag-over-sub');
                    el.classList.remove('drag-over-sort');
                } else if (App.getDragState()?.type === 'pos') {
                    // This shows the "Landing Line"
                    el.classList.add('drag-over-sort');
                    el.classList.remove('drag-over-sub');
                }
            },

            handlePosDragLeave: function(el) {
                el.classList.remove('drag-over-sub', 'drag-over-sort');
            },

            handlePosDrop: function(ev, el, sIdx, iIdx, subIdx) {
                ev.preventDefault();
                el.classList.remove('drag-over-sub', 'drag-over-sort');

                const dragState = App.getDragState();
                
                // Case 1: Dropping a product from the library
                if (App.isLibraryDrag()) {
                    const id = ev.dataTransfer.getData('text');
                    const type = ev.dataTransfer.getData('itemType');
                    if (id && type) {
                        App.addLibraryItemAsSubPosition(sIdx, iIdx, id, type);
                    }
                } 
                // Case 2: Dropping a position to reorder
                else if (dragState?.type === 'pos') {
                    App.moveDraggedNode(dragState, {
                        mode: 'sort-array',
                        sIdx: sIdx,
                        iIdx: iIdx,
                        subIdx: subIdx
                    });
                }
                App.clearDragMode();
            },

       openSaveModal: async () => {
            const isTemplateMode = document.getElementById('wiz-template-mode')?.checked || false;
            const locked = App.isLockedSnapshot(); // ✅ Check if locked

            const offerRadio = document.querySelector('input[name="save_mode"][value="offer"]');
            const templateRadio = document.getElementById('radio-new-template');
            const updateRadio = document.getElementById('radio-update-template');
            
            const offerLabelWrap = document.getElementById('save-mode-offer-wrap');
            const updateLabelWrap = document.getElementById('save-mode-update-wrap');
            const templateNameWrap = document.getElementById('template-name-wrap');
            const notice = document.getElementById('save-mode-notice');
            
            // Check if we have an active template loaded
            if (State.loadedTemplateId) {
                updateLabelWrap.classList.remove('hidden');
                updateLabelWrap.classList.add('flex');
                document.getElementById('lbl-loaded-template-name').innerText = State.loadedTemplateName;
                document.getElementById('lbl-loaded-template-name').title = State.loadedTemplateName;
            } else {
                updateLabelWrap.classList.add('hidden');
                updateLabelWrap.classList.remove('flex');
            }

            if (locked) {
                // ✅ FORCE TEMPLATE MODE IF LOCKED
                if (offerLabelWrap) offerLabelWrap.style.display = 'none';
                if (updateLabelWrap) updateLabelWrap.style.display = 'none';
                if (templateRadio) templateRadio.checked = true;
                if (templateNameWrap) templateNameWrap.classList.remove('hidden');
                if (notice) {
                    notice.innerText = "* Dokument ist im Status Auftrag. Speichern nur als Vorlage möglich.";
                    notice.classList.remove('hidden');
                }
            } else if (isTemplateMode) {
                // --- TEMPLATE-MODUS ---
                if (offerLabelWrap) offerLabelWrap.style.display = 'none'; 
                
                if (State.loadedTemplateId && updateRadio) {
                    updateRadio.checked = true;
                    if (templateNameWrap) templateNameWrap.classList.add('hidden');
                } else if (templateRadio) {
                    templateRadio.checked = true;
                    if (templateNameWrap) templateNameWrap.classList.remove('hidden');
                }
            } else {
                // --- NORMALER MODUS ---
                if (offerLabelWrap) offerLabelWrap.style.display = 'flex'; 
                if (offerRadio) offerRadio.checked = true;          
                if (templateNameWrap) templateNameWrap.classList.add('hidden');
                if (notice) notice.classList.add('hidden');
            }

            // Modal anzeigen und Feld leeren
            document.getElementById('save-quote-modal')?.classList.remove('hidden');
            const templateNameInput = document.getElementById('save-template-name');
            const templateDescInput = document.getElementById('save-template-desc');
            if (templateNameInput) templateNameInput.value = '';
            if (templateDescInput) templateDescInput.value = '';
            
            // Lade-Indikator und Button zurücksetzen
            document.getElementById('save-loading-indicator')?.classList.add('hidden');
            const saveBtn = document.getElementById('btn-perform-save');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            // --- FETCH DATA AND INIT SELECT2 ---
            if (!window._templateOptionsLoaded) {
                try {
                    const res = await fetch('/offers/templates/options', {
                        headers: { 'Accept': 'application/json' }
                    });
                    
                    if (res.ok) {
                        const responseData = await res.json();
                        
                        const buildOptions = (items, valKey, labelKey) => {
                            return '<option value="">Bitte wählen...</option>' +
                                (items || []).map(i => `<option value="${i[valKey]}">${App.escapeHtml(i[labelKey] || '')}</option>`).join('');
                        };

                        $('#save-template-department').html(buildOptions(responseData.departments, 'id', 'department_name'));
                        $('#save-template-article-group').html(buildOptions(responseData.article_groups, 'id', 'article_group'));
                        $('#save-template-brand').html(buildOptions(responseData.brands, 'id', 'name'));
                        $('#save-template-distributor').html(buildOptions(responseData.distributors, 'id', 'name'));

                        window._templateOptionsLoaded = true;
                    }
                } catch (err) {
                    console.error("Failed fetching dropdown data for save modal", err);
                }
            }

            setTimeout(() => {
                if (window.jQuery && $.fn.select2) {
                    $('.template-select2').each(function() {
                        if ($(this).data('select2')) {
                            $(this).select2('destroy');
                        }
                    });

                    $('.template-select2').select2({
                        width: '100%',
                        dropdownParent: $('#save-quote-modal')
                    });
                }
            }, 50);
        },


        resolveSaveContext: () => {
            const params = new URLSearchParams(window.location.search);
            const prefill = State.prefill || {};
            const objectItem = State.object?.items?.[0] || {};

            const firstFilled = (...values) => {
                for (const value of values) {
                    if (value === null || value === undefined || value === '') continue;

                    if (typeof value === 'number' && Number.isFinite(value) && value > 0) {
                        return value;
                    }

                    const str = String(value).trim();
                    if (!str || str === 'null' || str === 'undefined' || str === '-') continue;

                    const parsed = Number(str);
                    if (Number.isFinite(parsed) && parsed > 0) {
                        return parsed;
                    }

                    return str;
                }

                return null;
            };

            const byName = (name) => document.querySelector(`[name="${name}"]`)?.value || null;
            const byId = (id) => document.getElementById(id)?.value || null;

            const customerId = firstFilled(
                State.customer?.id,
                State.customer_id,
                State.custId,
                prefill.customer_id,
                byId('customer_id'),
                byId('wiz-customer-id'),
                byId('selected_customer_id'),
                byName('customer_id'),
                params.get('customer_id')
            );

            const alternativeId = firstFilled(
                objectItem.alternative_id,
                State.object?.alternative_id,
                State.object?.id,
                State.alternative_id,
                prefill.alternative_id,
                byId('alternative_id'),
                byId('object_id'),
                byId('wiz-alternative-id'),
                byId('selected_alternative_id'),
                byName('alternative_id'),
                byName('object_id'),
                params.get('alternative_id'),
                params.get('object_id')
            );

            const productId = firstFilled(
                objectItem.product_id,
                State.object?.product_id,
                State.product_id,
                prefill.product_id,
                byId('product_id'),
                byId('wiz-product-id'),
                byId('selected_product_id'),
                byName('product_id'),
                params.get('product_id')
            );

            const offerId = firstFilled(
                State.offerId !== 'NEW' ? State.offerId : null,
                prefill.offer_id,
                byId('offer_id'),
                byId('doc-offer-id'),
                byName('offer_id'),
                params.get('offer_id')
            );

            const offerFolderId = firstFilled(
                prefill.offer_folder_id,
                byId('offer_folder_id'),
                byId('doc-offer-folder-id'),
                byName('offer_folder_id'),
                params.get('offer_folder_id')
            );

            const hasObjectInState = !!(State.object && Array.isArray(State.object.items) && State.object.items.length);

            return {
                offer_id: offerId,
                offer_folder_id: offerFolderId,
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                hasCustomer: !!customerId,
                hasObject: hasObjectInState || !!alternativeId || !!productId,
                hasStateObject: hasObjectInState
            };
        },

        syncSaveContextIntoState: () => {
            const ctx = App.resolveSaveContext();

            if (ctx.customer_id && (!State.customer || !State.customer.id)) {
                State.customer = {
                    ...(State.customer || {}),
                    id: ctx.customer_id,
                    display_name: State.customer?.display_name || State.customer?.name || 'Kunde #' + ctx.customer_id
                };
            }

            if ((ctx.alternative_id || ctx.product_id) && (!State.object || !Array.isArray(State.object.items) || !State.object.items.length)) {
                State.object = {
                    ...(State.object || {}),
                    id: ctx.alternative_id || State.object?.id || null,
                    alternative_id: ctx.alternative_id || State.object?.alternative_id || null,
                    product_id: ctx.product_id || State.object?.product_id || null,
                    items: [{
                        ...(State.object?.items?.[0] || {}),
                        alternative_id: ctx.alternative_id || State.object?.items?.[0]?.alternative_id || null,
                        product_id: ctx.product_id || State.object?.items?.[0]?.product_id || null
                    }]
                };
            }

            State.prefill = {
                ...(State.prefill || {}),
                offer_id: ctx.offer_id || State.prefill?.offer_id || null,
                offer_folder_id: ctx.offer_folder_id || State.prefill?.offer_folder_id || null,
                customer_id: ctx.customer_id || State.prefill?.customer_id || null,
                alternative_id: ctx.alternative_id || State.prefill?.alternative_id || null,
                product_id: ctx.product_id || State.prefill?.product_id || null
            };

            return App.resolveSaveContext();
        },

        performSave: async () => {
            // 1. Identify Mode and Form Data
            const saveMode = document.querySelector('input[name="save_mode"]:checked')?.value;
            const isTemplate = (saveMode === 'template');
            const isUpdatingTemplate = (saveMode === 'update_template');
            const isWizardInTemplateMode = document.getElementById('wiz-template-mode')?.checked || false;

            if (State.isSnapshot && !isTemplate && !isUpdatingTemplate) {
                alert("Manipulation blockiert: Dies ist die Momentaufnahme des Angebots. Sie können diese Ansicht nicht speichern, um den aktiven Auftrag nicht zu überschreiben.");
                return;
            }

            let templateName = document.getElementById('save-template-name')?.value || '';
            const templateDesc = document.getElementById('save-template-desc')?.value || '';

            if (isUpdatingTemplate && !templateName.trim()) {
                templateName = State.loadedTemplateName || '';
            }

            // 🛑 SECURITY BLOCK: If the document is locked (Auftrag), strictly forbid saving as an offer
            if (App.isLockedSnapshot() && !isTemplate && !isUpdatingTemplate) {
                alert("Manipulation blockiert: Ein beauftragtes Angebot darf nicht mehr verändert werden. Sie können es nur als neue Vorlage speichern.");
                return;
            }

            // 2. Validation Logic
            if ((isTemplate || isUpdatingTemplate) && templateName.trim() === '') {
                alert('Bitte geben Sie einen Namen für die Vorlage ein.');
                return;
            }

            const saveContext = App.syncSaveContextIntoState();

            if (!isTemplate && !isUpdatingTemplate && !isWizardInTemplateMode) {
                if (!saveContext.hasCustomer || !saveContext.hasObject) {
                    console.warn('[Offer Save] Missing save context', {
                        saveContext,
                        prefill: State.prefill,
                        customer: State.customer,
                        object: State.object,
                        url: window.location.href
                    });

                    alert('Fehler: Für ein Angebot muss ein Kunde und ein Objekt ausgewählt sein. IDs wurden nicht vollständig gefunden.');
                    return;
                }
            }

            // 3. UI Feedback: Lock Buttons & Show Loader
            const saveBtn = document.getElementById('btn-perform-save');
            const loader = document.getElementById('save-loading-indicator');
            
            if (loader) loader.classList.remove('hidden');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            const totals = App.computeQuoteTotals();
            
            // 4. Prepare Data Payload
            const payload = {
                is_snapshot: State.isSnapshot,  
                is_template: isTemplate || isUpdatingTemplate,
                template_name: templateName.trim(),
                template_description: templateDesc.trim(),
                
                department_id: $('#save-template-department').val() || null,
                article_group_id: $('#save-template-article-group').val() || null,
                brand_id: $('#save-template-brand').val() || null,
                distributor_id: $('#save-template-distributor').val() || null,
                
                // 🛑 CRITICAL FIX: Strip the Offer ID if we are saving as a template so the backend cannot overwrite the Auftrag!
                offer_id: (isTemplate || isUpdatingTemplate) ? null : (saveContext.offer_id || null),
                offer_folder_id: (isTemplate || isUpdatingTemplate) ? null : (saveContext.offer_folder_id || null),
                customer_id: saveContext.customer_id || null,
                product_id: saveContext.product_id || null,
                alternative_id: saveContext.alternative_id || null,
                
                service: State.docType,
                main_title: State.mainTitleHtml,
                cover_text: State.coverTextHtml,
                sections: State.sections,
                canvas_images: State.placedImages,
                biography: State.biographyItems,

                branding: {
                    color: State.brandColor,
                    mode: State.brandMode,
                    logo: State.brandLogoUrl,
                    company: State.companyName,
                    branch_id: State.selectedBranchId || document.getElementById('wiz-company-select')?.value || null,
                    footer: State.companyFooter || App.getCompanyFooterSnapshot()
                },
                total_net: totals.salesNet,
                tax_rate: State.taxRate,
                total_gross: totals.grossTotal
            };

            try {
                // 5. Determine Target API Endpoint & Method
                let url = '/offers/save-document';
                let method = 'POST';

                if (isUpdatingTemplate && State.loadedTemplateId) {
                    url = `/offers/templates/${State.loadedTemplateId}`;
                    method = 'PUT';
                }

                // 6. Execute API Request
                const response = await fetch(url, {
                    method: method,
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

                // 7. Post-Save State Cleanup
                State.biographyItems.forEach(item => {
                    if (item.isLocal) item.isLocal = false; 
                });

                if (App.Bio && typeof App.Bio.addEntry === 'function') {
                    App.Bio.addEntry('Gespeichert', 'Alle Änderungen wurden erfolgreich synchronisiert.');
                }

                State.hasUnsavedChanges = false;
                document.getElementById('save-quote-modal')?.classList.add('hidden');

                // 8. Handle UI Success Messaging
                if (isTemplate || isUpdatingTemplate) {
                    if (result.template) {
                        State.loadedTemplateId = result.template.id;
                        State.loadedTemplateName = result.template.name;
                    }

                    App.toastConfirmShow({
                        title: isUpdatingTemplate ? 'Vorlage aktualisiert' : 'Vorlage gespeichert',
                        message: isUpdatingTemplate 
                            ? 'Die bestehende Vorlage wurde erfolgreich überschrieben.' 
                            : 'Die neue Vorlage wurde erfolgreich angelegt.',
                        okText: 'OK',
                        cancelText: '',
                        onOk: () => {
                            if (App.TemplateTab) App.TemplateTab.render();
                        }
                    });
                    
                    const cancelBtn = document.getElementById('toast-confirm-cancel');
                    if (cancelBtn) cancelBtn.style.display = 'none';

                } else {
                    if (result.folder_id) {
                        App.toastConfirmShow({
                            title: 'Dokument gespeichert',
                            message: `Dieses Angebot wurde im Ordner #${result.folder_id} gespeichert.`,
                            okText: 'Zum Ordner',
                            cancelText: 'Hier bleiben',
                            onOk: () => {
                                window.location.href = `/admin/offers/folders/${result.folder_id}?new_offer=1`;
                            }
                        });
                        const cancelBtn = document.getElementById('toast-confirm-cancel');
                        if (cancelBtn) cancelBtn.style.display = '';
                    } else {
                        alert('Angebot erfolgreich gespeichert!');
                    }
                }

            } catch (error) {
                console.error('Save error:', error);
                alert('Fehler beim Speichern: ' + error.message);
            } finally {
                // 9. Re-enable UI regardless of outcome
                if (loader) loader.classList.add('hidden');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
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


        closePrintPreview: () => {
            document.getElementById('print-preview-modal').classList.add('hidden');
            App.renderQuotePage(false); // Re-render main view to sync positions
        },

        // 2. Update makeThumbnailStatic to NOT strip the handles from floating elements
        makeThumbnailStatic: (root) => {
            if (!root) return root;

            // remove ids to avoid duplicates (except for floating elements)
            root.querySelectorAll('[id]').forEach(el => {
                if (!el.classList.contains('floating-element')) {
                    el.removeAttribute('id');
                }
            });

            // remove no-print/editor-only controls (except those inside floating elements)
            root.querySelectorAll(
                'button, .no-print, .delete-float, .section-drop-zone, .list-action-btn, .drag-handle, .ql-toolbar'
            ).forEach(el => {
                if (el.closest('.floating-element')) return; // KEEP floating element controls intact!
                el.remove();
            });

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
                kind: 'note', status: 'normal', subItems: [], active: true, print_hidden: true
            });
            App.renderQuotePage();
        },

       updatePosStatus: (sIdx, iIdx, subIdx, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            
            // Sync both variables so List View and Sidebar read the exact same state
            target.status = val;
            target.lineType = (val === 'normal') ? 'standard' : val;

            // If a sub-item changes to optional/alt, the parent total needs to recalculate!
            if (subIdx !== null) {
                App.syncParentTotals(sIdx, iIdx);
            }
            
            App.renderQuotePage();
        },

        updatePosConfig: (sIdx, iIdx, subIdx, key, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            
            if (key === 'hidePrices') target.hidePrices = val;
            if (key === 'print_hidden') target.print_hidden = val;
            if (key === 'print_hidden_labor') target.print_hidden_labor = val;
            
            if (key === 'kind') {
                target.kind = val;
                // AUTO-APPLY DEFAULT MARGINS WHEN SWITCHING TYPE
                target.marginPercent = App.getDefaultMargin(val);
                target.unit = val === 'labor' ? 'Std.' : 'Stk.';
                
                // ✅ NEW: Initialize labor structure if switched to Lohn
                if (val === 'labor') {
                    target.showImage = false;
                    target.hideImage = true;
                    if (!Array.isArray(target.labor_rows) || target.labor_rows.length === 0) {
                        target.labor_rows = [{
                            id: Date.now(),
                            qualification_id: null,
                            qualification_name: 'Neue Arbeitsleistung',
                            qty: 1,
                            unit: 'Std.',
                            ek: 0,
                            margin_percent: App.getDefaultMargin('labor'),
                            rate: 0,
                            total: 0
                        }];
                    }
                    App.recalcLaborCarrier(sIdx, iIdx, subIdx);
                } else if (val === 'note') {
                    target.ek = 0; target.price = 0; target.qty = 0; target.unit = '';
                } else {
                    // If switched back to Article
                    target.hideImage = false;
                    if (target.ek > 0) {
                        target.price = App.vkFromEkMargin(target.ek, target.marginPercent);
                    }
                }
            }
            
            if (key === 'isPauschal') {
                target.isPauschal = val;
                if (val) {
                    target.unit = 'Pauschal';
                    target.qty = 1;
                } else {
                    target.unit = target.kind === 'labor' ? 'Std.' : 'Stk.';
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
                title: `${State.sections.length+1}. Abschnitt`,
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

            openMainTitleModal: () => {
                const titleEl = document.getElementById('desc-modal-title');
                if (titleEl) titleEl.innerText = 'Dokumententitel bearbeiten';

                const quill = App.initDescQuill();

                let html = (State.mainTitleHtml || '').toString().trim();

                // Fallback to reading the DOM if state is empty
                if (!html) {
                    const domEl = document.getElementById('doc-main-title');
                    html = domEl ? domEl.innerHTML.trim() : '';
                }

                quill.setContents([]);
                if (html) {
                    quill.clipboard.dangerouslyPasteHTML(html);
                } else {
                    quill.setText('');
                }

                App._richEditorMode = 'main_title';
                App._descEditing = null;

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
 
               // MAIN TITLE MODE
                if (App._richEditorMode === 'main_title') {
                    State.mainTitleHtml = html;
                    
                    // FIX: Actively push the saved HTML into the static Page 1 element!
                    const mainTitleEl = document.getElementById('doc-main-title');
                    if (mainTitleEl) {
                        mainTitleEl.innerHTML = html || 'TITEL EINGEBEN...';
                    }

                    App.closeDescModal();
                    App.renderQuotePage();
                    return;
                }

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

                // 1. Register Custom Fonts
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

                // 2. Register Custom Font Sizes
                const Size = Quill.import('attributors/style/size');
                Size.whitelist = ['6px', '8px', '10px', '12px', '14px', '16px', '18px', '24px', '32px'];
                Quill.register(Size, true);

                // 3. Register Custom Line Spacing (Line Height)
                const Parchment = Quill.import('parchment');
                const LineHeightStyle = new Parchment.Attributor.Style('lineHeight', 'line-height', {
                    scope: Parchment.Scope.BLOCK,
                    whitelist: ['1.0', '1.2', '1.5', '1.8', '2.0', '2.5', '3.0']
                });
                Quill.register(LineHeightStyle, true);

                // 4. Initialize Quill Editor
                App._descQuill = new Quill('#desc-quill', {
                    theme: 'snow',
                    placeholder: 'Text eingeben …',
                    modules: {
                        toolbar: {
                            container: [
                                [{ font: Font.whitelist }],
                                [{ size: Size.whitelist }],
                                [{ lineHeight: LineHeightStyle.whitelist }], // <-- New Line Spacing Dropdown
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

                box.innerHTML = `<div class="text-xs text-[#000000] flex items-center gap-2">
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
                                <div class="text-[11px] font-bold text-[#000000] truncate">${esc(ms.name || `Set #${ms.id}`)}</div>
                                <div class="text-[10px] text-[#000000] mt-0.5 line-clamp-2">${esc(preview(ms.description || '', 140))}</div>
                            </div>
                            <button type="button" onclick="App.openSetModal('${ms.id}')"
                                class="text-slate-300 hover:text-[#93c21c]" title="Set anzeigen">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-[#000000] mt-1">
                            <span class="text-[9px] font-black text-[#93c21c]">SET</span>
                            </div>
                        </div>
                        </div>
                    `;
                    };
 
                if (!sets.length) {
                    box.innerHTML = `<div class="text-xs text-[#000000]">Keine Sets in dieser Gruppe.</div>`;
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
                            div.innerHTML = `<div class="font-bold text-[#000000] text-sm">${c.display_name}</div><div class="text-xs text-[#000000]">${c.street || ''}, ${c.city || ''}</div>`;
                            div.onclick = () => App.Wizard.selectCustomer(c);
                            drop.appendChild(div);
                        });
                    } else {
                        drop.classList.add('hidden');
                    }
                } catch (err) { console.error("Customer search failed", err); }
            },
 
           toggleTemplateMode: (isTemplate) => {
                const custStep = document.getElementById('wiz-customer-search').parentElement.parentElement;
                const objStep = document.getElementById('wiz-step-2');
                const startBtn = document.getElementById('wiz-btn-start');

                if (isTemplate) {
                    // 1. Visual feedback in Wizard
                    custStep.classList.add('opacity-40', 'pointer-events-none');
                    objStep.classList.add('opacity-40', 'pointer-events-none');
                    startBtn.disabled = false;
                    startBtn.classList.remove('btn-disabled');

                    // 2. Automatically switch to the "Vorlagen" Tab in the Editor
                    // We trigger the tab switch logic
                    App.Tabs.switch('templates');
                    
                    // Optional: Show a small notification or log
                    console.log("Template Mode active: Switched to Templates tab.");
                } else {
                    // Return to normal mode
                    custStep.classList.remove('opacity-40', 'pointer-events-none');
                    App.Wizard.selectObject(); // Re-validate standard fields
                    
                    // Optional: Switch back to list or A4 view if you want
                    App.Tabs.switch('list');
                }
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
                const isTemplateMode = document.getElementById('wiz-template-mode')?.checked || false;

                const picked = opts.map(o => ({
                    lead_product_id: parseInt(o.value, 10),
                    alternative_id: o.dataset.altId ? parseInt(o.dataset.altId, 10) : null,
                    product_id: o.dataset.productId ? parseInt(o.dataset.productId, 10) : null,
                    label: o.text
                }));

                State.object = {
                    items: picked,
                    name: picked.length === 1 ? picked[0].label : `${picked.length} Produkte ausgewählt`
                };

                document.getElementById('wiz-object-count').innerText = picked.length;

                const startBtn = document.getElementById('wiz-btn-start');
                
                // Validierung: Wenn Vorlage-Modus ODER Objekte gewählt sind
                if (isTemplateMode || picked.length > 0) {
                    document.getElementById('wiz-step-3').classList.remove('opacity-50', 'pointer-events-none');
                    document.getElementById('wiz-step-4').classList.remove('opacity-50', 'pointer-events-none');
                    startBtn.disabled = false;
                    startBtn.classList.remove('btn-disabled');
                } else {
                    startBtn.disabled = true;
                    startBtn.classList.add('btn-disabled');
                }
            }

        },

        // --- EDITOR LOGIC ---
        renderSidebar: async (isAppending = false) => {
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
                        : (iconHtml || `<i class="fa-solid fa-box text-[#000000]"></i>`)
                    }
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-[#000000] truncate">${esc(title)}</div>
                        ${subtitle ? `<div class="text-[10px] text-[#000000] mt-0.5 line-clamp-2">${esc(subtitle)}</div>` : ``}
                    </div>
                    ${rightHtml || ``}
                    </div>

                    <div class="flex items-center gap-2 text-[10px] text-[#000000] mt-1">
                    ${badge ? `<span class="text-[9px] font-black text-[#93c21c]">${esc(badge)}</span>` : ``}
                    </div>
                </div>
                </div>
            `;

            try {
                // Nur Ladespinner anzeigen, wenn wir nicht gerade eine neue Seite anhängen
                if (!isAppending) {
                    list.innerHTML = `
                    <div class="text-xs text-[#000000] p-2">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Lade Bibliothek...
                    </div>
                    `;
                }

                // Decide endpoint based on libraryMode
               const mode = (State.libraryMode || 'group_sets'); // 'group_sets' | 'sets' | 'products'

                let endpoint = `${API_BASE}/wizard/products`; // fallback
                if (mode === 'group_sets') endpoint = `${API_BASE}/wizard/group-sets`;
                if (mode === 'sets') endpoint = `${API_BASE}/wizard/products`;      // your old mixed endpoint (sets+products grouped)
                if (mode === 'products') endpoint = `${API_BASE}/wizard/products-list`; // NEW: flat product list w/ brand+distributor+price


                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('q', q);
                url.searchParams.set('context', 'angebot');
                url.searchParams.set('page', State.libraryPage); // Send pagination page

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                
                // Track total pages available from Laravel's pagination
                State.libraryLastPage = data.last_page || 1;

                // --- Helper function to cleanly append HTML and add "Load More" button ---
                const applyHtmlWithPagination = (newHtml) => {
                    if (isAppending) {
                        const oldBtn = list.querySelector('#btn-load-more');
                        if (oldBtn) oldBtn.remove();
                        list.innerHTML += newHtml;
                    } else {
                        list.innerHTML = newHtml;
                    }

                    if (State.libraryPage < State.libraryLastPage) {
                        list.innerHTML += `
                            <button id="btn-load-more" onclick="State.libraryPage++; App.renderSidebar(true)" class="w-full mt-4 py-2 bg-slate-200 rounded text-xs font-bold text-[#000000] hover:bg-slate-300 transition-colors">
                                Mehr laden
                            </button>
                        `;
                    }
                };

                // ------------------------------
                // MODE A) GROUP SETS TAB
                // ------------------------------
                if (mode === 'group_sets') {
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
                                master_sets: Array.isArray(gs.master_sets) ? gs.master_sets : null 
                            });
                        });
                    });

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
                        if (!isAppending) list.innerHTML = `<div class="text-xs text-[#000000] p-3">Keine Treffer.</div>`;
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

                    const newHtml = filtered.map(gs => {
                        const setsBoxId = `gs-sets-${gs.id}`;
                        const hasInlineSets = Array.isArray(gs.master_sets);

                        const headRight = `
                            <div class="flex items-center gap-2">
                                ${gs.color ? `<span class="w-3 h-3 rounded-full border border-slate-200" style="background:${esc(gs.color)}"></span>` : ``}
                                <span class="text-[10px] text-[#000000]">${Number(gs.master_sets_count || 0)} Sets</span>

                                <span draggable="true"
                                    ondragstart="App.dragStart(event, '${gs.id}', 'master_set_group')"
                                    class="ml-1 inline-flex items-center justify-center w-7 h-7 rounded border border-slate-200 bg-white text-[#000000] hover:text-[#93c21c] cursor-grab"
                                    title="Ganzes Group Set ziehen">
                                    <i class="fa-solid fa-grip-vertical text-xs"></i>
                                </span>
                            </div>
                        `;

                        const groupLabel = gs.article_group
                            ? `<div class="text-[10px] text-[#000000] font-bold uppercase tracking-wide">${esc(gs.article_group)}</div>`
                            : '';

                        const bodyHtml = hasInlineSets
                            ? (
                                gs.master_sets.length
                                    ? gs.master_sets
                                        .slice()
                                        .sort((a, b) => (a.name || '').localeCompare(b.name || '', 'de'))
                                        .map(ms => renderSetCard(ms, gs.image))
                                        .join('')
                                    : `<div class="text-xs text-[#000000]">Keine Sets in dieser Gruppe.</div>`
                            )
                            : `<div class="text-xs text-[#000000] flex items-center gap-2">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Öffnen zum Laden…
                            </div>`;

                        return `
                            <details class="bg-white/60 border border-slate-200 rounded-lg overflow-hidden"
                                    data-gs-id="${gs.id}"
                                    ${hasInlineSets ? '' : `ontoggle="App.onGroupSetToggle(this)"`}>
                                <summary class="cursor-pointer select-none px-3 py-2 bg-slate-50 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        ${groupLabel}
                                        <div class="font-black text-[#000000] text-xs truncate">${esc(gs.name)}</div>
                                        ${gs.description ? `<div class="text-[10px] text-[#000000] mt-0.5 line-clamp-1">${esc(preview(gs.description, 90))}</div>` : ''}
                                    </div>
                                    ${headRight}
                                </summary>

                                <div class="p-3 space-y-2 bg-slate-50/50 max-h-[350px] overflow-y-auto scroller" id="${setsBoxId}">
                                    ${bodyHtml}
                                </div>
                            </details>
                        `;
                    }).join('');

                    applyHtmlWithPagination(newHtml);
                    return;
                }

                // ------------------------------
                // MODE C) PRODUCTS LIST 
                // ------------------------------
                if (mode === 'products') {
                    const items = Array.isArray(data.items) ? data.items : [];

                    if (!items.length) {
                        if (!isAppending) list.innerHTML = `<div class="text-xs text-[#000000] p-3">Keine Treffer.</div>`;
                        return;
                    }

                    const fmt = (n) => {
                        const v = Number(n);
                        if (!Number.isFinite(v)) return '-';
                        return v.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
                    };

                    const newHtml = items.map(p => {
                        const brand = (p.brand_name || '').trim();
                        const dist  = (p.distributor_name || '').trim();
                        const price = fmt(p.best_price);

                        const subtitleParts = [];
                        if (brand) subtitleParts.push(`Brand: ${brand}`);
                        if (dist) subtitleParts.push(`Lieferant: ${dist}`);
                        subtitleParts.push(`EK: ${price}`);

                        const rightHtml = `
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[10px] font-black text-[#000000]">${price}</span>
                            <span class="text-[9px] font-bold text-[#000000]">${dist ? dist : '—'}</span>
                            <button type="button" onclick="App.openProductModal('${p.id}')" class="text-slate-300 hover:text-[#93c21c] mt-1" title="Details">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
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

                    applyHtmlWithPagination(newHtml);
                    return;
                }

                // ------------------------------
                // MODE B) PRODUCTS TAB (fallback grouped by article_group)
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
                    if (!isAppending) list.innerHTML = `<div class="text-xs text-[#000000] p-3">Keine Treffer.</div>`;
                    return;
                }

                const newHtml = groups.map(g => {
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
                            <div class="font-bold text-[#000000] text-xs uppercase tracking-wide">${esc(g.groupName)}</div>
                            <div class="text-[10px] text-[#000000]">${totalCount}</div>
                        </summary>
                        <div class="p-3 space-y-2 bg-slate-50/50 max-h-[350px] overflow-y-auto scroller">
                            ${rows || `<div class="text-xs text-[#000000]">Keine Einträge</div>`}
                        </div>
                        </details>
                    `;
                }).join('');

                applyHtmlWithPagination(newHtml);

            } catch (err) {
                console.error("Catalog search failed", err);
                if (!isAppending) {
                    list.innerHTML = `
                    <div class="text-xs text-red-500 p-3">
                        Fehler beim Laden der Bibliothek.
                    </div>
                    `;
                }
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

        handleItemAdd: async function(sIdx, id, typeFromDrag = null) {
            ensureSection(sIdx);
            const type = resolveType(typeFromDrag);
            const rawId = id;

            const parseUnitInfo = (unitRaw, fallback = 'Stk') => {
                if (typeof App?.parsePriceUnit === 'function') return App.parsePriceUnit(unitRaw || fallback, fallback);
                const match = (unitRaw || '').toString().match(/^(\d+(?:[.,]\d+)?)\s*(.+)$/);
                if (match) return { value: parseFloat(match[1].replace(',', '.')) || 1, label: match[2].trim(), text: unitRaw };
                return { value: 1, label: unitRaw || fallback, text: `1 ${unitRaw || fallback}` };
            };

            try {
                // --- MODE: MASTER SET GROUP (Recursive) ---
                if (type === "master_set_group") {
                    const data = await fetchJson(new URL(`${API_BASE}/master-set-groups/${rawId}?context=angebot`, window.location.origin));
                    const sets = data?.master_sets || data?.sets || [];
                    for (const ms of sets) {
                        if (ms?.id) await this.handleItemAdd(sIdx, ms.id, "master_set");
                    }
                    return;
                }

                // --- MODE: MASTER SET ---
                if (type === "master_set") {
                    const resp = await fetchJson(new URL(`${API_BASE}/master-sets/${rawId}?context=angebot`, window.location.origin));
                    const data = resp?.data || resp || {};

                    const setItem = buildBaseItem({
                        item_type: "master_set",
                        productId: safeNum(data.id || rawId),
                        name: safeStr(data.name, `Set #${rawId}`),
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),
                        qty: 1,
                        unit: "Set",
                        price_unit_label: "Set",
                        img: App.pickImage(data, App.placeholderImg(data.name || 'SET')),
                        subItems: [],
                        price: 0
                    });

                    const sourceItems = Array.isArray(data.items) ? data.items : [];
                    let setVkTotal = 0;

                    sourceItems.forEach(node => {
                        if (node.type === 'component') {
                            const ek = safeNum(node.purchase_price ?? node.ek, 0);
                            let vk = safeNum(node.unit_price ?? node.price, 0);
                            let margin = parseFloat(node.margin) || 0;

                            // RULE: If record has margin, use it. Otherwise hardcode 20% (default)
                            if (margin <= 0) {
                                margin = App.getDefaultMargin('article');
                            }
                            if (ek > 0) {
                                vk = App.vkFromEkMargin(ek, margin);
                            }

                            const uInfo = parseUnitInfo(node.price_unit || node.unit);

                            const component = buildBaseItem({
                                item_type: "master_set_component",
                                component_id: node.id,
                                name: node.name || "Komponente",
                                desc_html: pickDescHtml(node),
                                qty: safeNum(node.qty, 1),
                                unit: node.unit || "Stk",
                                price_unit_value: safeNum(node.price_unit_value, uInfo.value),
                                price: vk,
                                ek: ek,
                                marginPercent: margin,
                                img: App.pickImage(node),
                                depth: 1,

                                article_no: node.article_no || '',
                                distributor_article_no: node.distributor_article_no || '',
                                distributor_name: node.distributor?.name || node.distributor_name || '',
                                distributor_id: node.distributor?.id || node.distributor_id || null,
                                skonto: node.skonto ?? node.distributor?.cash_discount ?? 0,
                                payment_terms: node.payment_terms ?? node.distributor?.payment_terms ?? 14,
                            });
                            setVkTotal += (component.qty / component.price_unit_value) * component.price;
                            setItem.subItems.push(component);

                            // Children Loop
                            if (Array.isArray(node.children) && node.children.length > 0) {
                                node.children.forEach(childNode => {
                                    const cEk = safeNum(childNode.purchase_price ?? childNode.ek, 0);
                                    let cVk = safeNum(childNode.unit_price ?? childNode.price, 0);
                                    let cMargin = parseFloat(childNode.margin) || 0;

                                    // RULE: If record has margin, use it. Otherwise hardcode 20% (default)
                                    if (cMargin <= 0) {
                                        cMargin = App.getDefaultMargin('article');
                                    }
                                    if (cEk > 0) {
                                        cVk = App.vkFromEkMargin(cEk, cMargin);
                                    }

                                    const cInfo = parseUnitInfo(childNode.price_unit || childNode.unit);

                                    const childComponent = buildBaseItem({
                                        item_type: "master_set_component",
                                        component_id: childNode.id,
                                        name: childNode.name || "Unterkomponente",
                                        desc_html: pickDescHtml(childNode),
                                        qty: safeNum(childNode.qty, 1),
                                        unit: childNode.unit || "Stk",
                                        price_unit_value: safeNum(childNode.price_unit_value, cInfo.value),
                                        price: cVk,
                                        ek: cEk,
                                        marginPercent: cMargin,
                                        img: App.pickImage(childNode),
                                        depth: 2, 

                                        article_no: childNode.article_no || '',
                                        distributor_article_no: childNode.distributor_article_no || '',
                                        distributor_name: childNode.distributor?.name || childNode.distributor_name || '',
                                        distributor_id: childNode.distributor?.id || childNode.distributor_id || null,
                                        skonto: childNode.skonto ?? childNode.distributor?.cash_discount ?? 0,
                                        payment_terms: childNode.payment_terms ?? childNode.distributor?.payment_terms ?? 14,
                                    });
                                    
                                    setVkTotal += (childComponent.qty / childComponent.price_unit_value) * childComponent.price;
                                    setItem.subItems.push(childComponent); 
                                });
                            }
                        }
                        else if (node.type === 'labor') {
                            const laborRows = Array.isArray(node.children) ? node.children : [node];
                            
                            let laborEkTotal = 0;
                            let laborVkTotal = 0;
                            let laborQtyTotal = 0;

                            const mappedRows = laborRows.map(l => {
                                const qty = safeNum(l.hours || l.qty, 1);
                                const ek = safeNum(l.ek || l.qualification_price, 0);
                                const rate = safeNum(l.hourly_rate || l.rate || l.price, 0);
                                
                                laborEkTotal += (ek * qty);
                                laborVkTotal += (rate * qty);
                                laborQtyTotal += qty;

                                return {
                                    id: Date.now() + Math.floor(Math.random() * 1000),
                                    qualification_id: l.qualification_id || null,
                                    qualification_name: l.qualification_name || l.name || 'Dienstleistung',
                                    qty: qty,
                                    unit: 'Std',
                                    ek: ek,
                                    margin_percent: safeNum(l.margin_percent, App.getDefaultMargin('labor')),
                                    rate: rate,
                                    total: safeNum(l.total, qty * rate)
                                };
                            });

                            const laborCarrier = buildBaseItem({
                                item_type: "labor", kind: "labor", name: node.name || "Arbeitsleistung",
                                qty: mappedRows.length || 1, unit: "Stk", showImage: false, depth: 1,
                                price: mappedRows.length > 0 ? (laborVkTotal / mappedRows.length) : 0,
                                ek: mappedRows.length > 0 ? (laborEkTotal / mappedRows.length) : 0,
                                labor_rows: mappedRows
                            });
                            
                            setVkTotal += laborQtyTotal > 0 ? laborVkTotal : 0;
                            setItem.subItems.push(laborCarrier);
                        }
                    });

                    setItem.price = setVkTotal;
                    pushItem(sIdx, setItem);
                }
                // --- MODE: SINGLE PRODUCT ---
                else {
                    const data = await fetchJson(new URL(`${API_BASE}/products/${rawId}?context=angebot`, window.location.origin));
                    const p = data?.data || data || {};
                    const uInfo = parseUnitInfo(p.price_unit || p.unit);
                    
                    const ek = safeNum(p.ek ?? p.purchase_price ?? 0);
                    let vk = safeNum(p.price ?? p.vk ?? p.unit_price ?? 0);
                    let margin = parseFloat(p.margin) || 0;

                    // RULE: If record has margin, use it. Otherwise hardcode 20% (default)
                    if (margin <= 0) {
                        margin = App.getDefaultMargin('article');
                    }
                    if (ek > 0) {
                        vk = App.vkFromEkMargin(ek, margin);
                    }

                    const item = buildBaseItem({
                        item_type: "product",
                        productId: safeNum(p.id || rawId),
                        name: safeStr(p.name ?? p.product, `Produkt #${rawId}`),
                        desc_html: pickDescHtml(p),
                        img: App.pickImage(p),
                        price: vk,
                        ek: ek,
                        marginPercent: margin,
                        qty: 1,
                        unit: uInfo.label,
                        price_unit_value: safeNum(p.price_unit_value, uInfo.value),
                        subItems: [],

                        article_no: p.article_no || '',
                        distributor_article_no: p.distributor_price?.article_no || '',
                        distributor_name: p.distributor_name || p.distributor?.name || '',
                        distributor_id: p.distributor_id || p.distributor?.id || null,
                        skonto: p.skonto ?? p.distributor?.cash_discount ?? 0,
                        payment_terms: p.payment_terms ?? p.distributor?.payment_terms ?? 14,
                    });
                    pushItem(sIdx, item);
                }

                App.renderQuotePage();
            } catch (err) {
                console.error("handleItemAdd failed:", err);
            }
        },

        addLibraryItemAsSubPosition: async function(targetSIdx, targetIIdx, id, typeFromDrag) {
            const sec = State.sections[targetSIdx];
            const parent = sec?.items?.[targetIIdx];
            if (!parent) return;

            if (!Array.isArray(parent.subItems)) parent.subItems = [];

            try {
                const url = typeFromDrag === 'master_set'
                    ? `${API_BASE}/master-sets/${id}?context=angebot`
                    : `${API_BASE}/products/${id}?context=angebot`;

                const resp = await fetch(url, { headers: { Accept: 'application/json' } });
                const json = await resp.json();
                const data = json?.data ?? json ?? {};

                if (typeFromDrag === 'master_set') {
                    let setMatTotal = 0;
                    let setEkTotal = 0;

                    (data.items || []).forEach(it => {
                        if (it.type === 'component') {
                            const ek = safeNum(it.purchase_price ?? it.ek, 0);
                            let vk = safeNum(it.unit_price ?? it.price, 0);
                            let margin = parseFloat(it.margin) || 0;

                            // RULE: If record has margin, use it. Otherwise hardcode 20%
                            if (margin <= 0) {
                                margin = App.getDefaultMargin('article');
                            }
                            if (ek > 0) {
                                vk = App.vkFromEkMargin(ek, margin);
                            }

                            setMatTotal += (safeNum(it.qty, 1) * vk);
                            setEkTotal += (safeNum(it.qty, 1) * ek);

                            if (Array.isArray(it.children)) {
                                it.children.forEach(child => {
                                    const cEk = safeNum(child.purchase_price ?? child.ek, 0);
                                    let cVk = safeNum(child.unit_price ?? child.price, 0);
                                    let cMargin = parseFloat(child.margin) || 0;

                                    if (cMargin <= 0) {
                                        cMargin = App.getDefaultMargin('article');
                                    }
                                    if (cEk > 0) {
                                        cVk = App.vkFromEkMargin(cEk, cMargin);
                                    }

                                    setMatTotal += (safeNum(child.qty, 1) * cVk);
                                    setEkTotal += (safeNum(child.qty, 1) * cEk);
                                });
                            }
                        } else if (it.type === 'labor') {
                            const laborRows = Array.isArray(it.children) ? it.children : [it];
                            
                            let laborEkTotal = 0;
                            let laborVkTotal = 0;
                            let laborQtyTotal = 0;

                            const mappedRows = laborRows.map(l => {
                                const qty = safeNum(l.hours || l.qty, 1);
                                const ek = safeNum(l.ek || l.qualification_price, 0);
                                const rate = safeNum(l.hourly_rate || l.rate || l.price, 0);
                                
                                laborEkTotal += (ek * qty);
                                laborVkTotal += (rate * qty);
                                laborQtyTotal += qty;

                                return {
                                    id: Date.now() + Math.floor(Math.random() * 1000),
                                    qualification_id: l.qualification_id || null,
                                    qualification_name: l.qualification_name || l.name || 'Dienstleistung',
                                    qty: qty,
                                    unit: 'Std',
                                    ek: ek,
                                    margin_percent: safeNum(l.margin_percent, App.getDefaultMargin('labor')),
                                    rate: rate,
                                    total: safeNum(l.total, qty * rate)
                                };
                            });

                            const laborCarrier = buildBaseItem({
                                item_type: "labor", kind: "labor", name: it.name || "Arbeitsleistung",
                                qty: mappedRows.length || 1, unit: "Stk", showImage: false, depth: 1,
                                price: mappedRows.length > 0 ? (laborVkTotal / mappedRows.length) : 0,
                                ek: mappedRows.length > 0 ? (laborEkTotal / mappedRows.length) : 0,
                                labor_rows: mappedRows
                            });
                            
                            setMatTotal += laborQtyTotal > 0 ? laborVkTotal : 0;
                            setEkTotal += laborQtyTotal > 0 ? laborEkTotal : 0;
                            parent.subItems.push(laborCarrier);
                        }
                    });

                    parent.subItems.push(buildBaseItem({
                        item_type: 'sub_master_set',
                        name: data.name || 'Set',
                        price: setMatTotal,
                        ek: setEkTotal,
                        qty: 1,
                        unit: 'Set',
                        depth: 1,
                        active: false,

                        article_no: data.article_no || '',
                        distributor_article_no: data.distributor_price?.article_no || '',
                        distributor_name: data.distributor_name || data.distributor?.name || '',
                        distributor_id: data.distributor_id || data.distributor?.id || null,
                        skonto: data.skonto ?? data.distributor?.cash_discount ?? 0,
                    }));
                } else {
                    // Add Single Product as Sub-Position
                    const ek = safeNum(data.ek ?? data.purchase_price ?? 0);
                    let vk = safeNum(data.price ?? data.vk ?? data.unit_price ?? 0);
                    let margin = parseFloat(data.margin) || 0;

                    // RULE: If record has margin, use it. Otherwise hardcode 20%
                    if (margin <= 0) {
                        margin = App.getDefaultMargin('article');
                    }
                    if (ek > 0) {
                        vk = App.vkFromEkMargin(ek, margin);
                    }

                    parent.subItems.push(buildBaseItem({
                        item_type: 'sub_product',
                        name: data.name || data.product || 'Produkt',
                        desc_html: pickDescHtml(data),
                        price: vk,
                        ek: ek,
                        qty: 1,
                        unit: data.unit || 'Stk',
                        depth: 1,
                        active: true,
                        marginPercent: margin,

                        article_no: data.article_no || '',
                        distributor_article_no: data.distributor_price?.article_no || '',
                        distributor_name: data.distributor_name || data.distributor?.name || '',
                        distributor_id: data.distributor_id || data.distributor?.id || null,
                        skonto: data.skonto ?? data.distributor?.cash_discount ?? 0,
                        payment_terms: data.payment_terms ?? data.distributor?.payment_terms ?? 14,
                    }));
                }

                App.syncParentTotals(targetSIdx, targetIIdx);
                App.renderQuotePage();
            } catch (err) {
                console.error("addLibraryItemAsSubPosition failed:", err);
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

        renderSidebarTools: () => { 
            const c=document.getElementById('tools-list'); 
            
            // Start by adding the Textbox button
            c.innerHTML=`
                <div draggable="true" ondragstart="App.dragStartToolText(event)" class="bg-white border rounded p-2 cursor-grab flex flex-col items-center justify-center hover:border-[#93c21c] transition-colors" style="min-height: 80px;">
                    <i class="fa-solid fa-font text-2xl text-[#000000] mb-2"></i>
                    <span class="text-xs font-bold text-[#000000]">Neues Textfeld</span>
                </div>
            `; 
            
            // Then add the images as before
            [...['https://placehold.co/100x100/green/white?text=Geprüft'],...State.toolsImages].forEach(src=>{ 
                c.innerHTML+=`<div draggable="true" ondragstart="App.dragStartTool(event,'${src}')" class="bg-white border rounded p-2 cursor-grab"><img src="${src}" class="w-full h-16 object-contain"></div>`; 
            }); 
        },        
        getCompanyProfile: (key = null) => {
            const selectedKey = key || document.getElementById('wiz-company-select')?.value || window.DefaultBranchProfileKey || 'solar-aspekt';
            return (window.BranchProfiles && window.BranchProfiles[selectedKey])
                || App.CompanyProfiles[selectedKey]
                || App.CompanyProfiles['solar-aspekt']
                || Object.values(window.BranchProfiles || {})[0]
                || null;
        },

        getCompanyFooterSnapshot: (profile = null) => {
            const p = profile || State.selectedBranch || App.getCompanyProfile();
            if (!p) return {};

            return {
                branch_id: p.id || null,
                company: p.name || p.branch || '',
                street: p.street || '',
                postcode: p.postcode || '',
                city: p.city || '',
                country: p.country || '',
                phone: p.phone || '',
                whatsapp: p.whatsapp || '',
                email: p.email || '',
                web: p.web || '',
                bank: p.bank || '',
                iban: p.iban || '',
                bic: p.bic || '',
                register: p.register || '',
                tax: p.tax || '',
                vat: p.vat || '',
                gf: p.gf || '',
                contact_person: p.contactPerson || p.contact_person || ''
            };
        },

        updateCompanyFooter: (profile = null) => {
            const p = profile || State.selectedBranch || App.getCompanyProfile();
            if (!p) return;

            const esc = App.escapeHtml || ((value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;'));

            const rows = (...items) => items.filter(v => String(v ?? '').trim() !== '').join('<br>');
            const line = (...items) => items.filter(v => String(v ?? '').trim() !== '').map(esc).join(' ');
            const company = p.name || p.branch || '';
            const cityLine = line(p.postcode, p.city);
            const gf = String(p.gf || '').replace(/^Geschäftsführer:\s*/i, '').trim();

            const fCol1 = document.getElementById('footer-col-1');
            if (fCol1) {
                fCol1.innerHTML = rows(
                    `<span class="font-bold text-[#000000]" id="footer-company" style="color: var(--brand-color);">${esc(company)}</span>`,
                    esc(p.street || ''),
                    cityLine
                );
            }

            const fCol2 = document.getElementById('footer-col-2');
            if (fCol2) {
                fCol2.innerHTML = rows(
                    p.phone ? `Tel. ${esc(p.phone)}` : '',
                    p.whatsapp ? `WhatsApp: ${esc(p.whatsapp)}` : '',
                    p.email ? esc(p.email) : '',
                    p.web ? esc(p.web) : ''
                );
            }

            const fCol3 = document.getElementById('footer-col-3');
            if (fCol3) {
                fCol3.innerHTML = rows(
                    p.bank ? esc(p.bank) : '',
                    p.iban ? `IBAN: ${esc(p.iban)}` : '',
                    p.bic ? `BIC: ${esc(p.bic)}` : ''
                );
            }

            const fCol4 = document.getElementById('footer-col-4');
            if (fCol4) {
                fCol4.innerHTML = rows(
                    p.register ? esc(p.register) : '',
                    p.tax ? `St.-Nr. ${esc(p.tax)}` : '',
                    p.vat ? `USt-IdNr. ${esc(p.vat)}` : '',
                    gf ? `Geschäftsführer: ${esc(gf)}` : ''
                );
            }

            State.selectedBranch = p;
            State.selectedBranchId = p.id || null;
            State.companyFooter = App.getCompanyFooterSnapshot(p);
        },

        updateBranding: () => {
            const color = document.getElementById('wiz-brand-color')?.value || '#93c21c';
            const mode = document.querySelector('input[name="wiz-brand-mode"]:checked')?.value || 'text';
            const name = document.getElementById('wiz-brand-name')?.value || 'SOLAR ASPEKT';
            const logoUrl = document.getElementById('wiz-brand-logo')?.value || '';

            // Pull secondColor from the selected branch/profile
            const profileKey = document.getElementById('wiz-company-select')?.value || window.DefaultBranchProfileKey || 'solar-aspekt';
            const profile = App.getCompanyProfile(profileKey);
            const secondColor = profile?.secondColor || color;

            State.brandColor = color;
            State.secondColor = secondColor; // Add to state
            State.brandMode = mode;
            State.companyName = name;
            State.brandLogoUrl = logoUrl;
            if (profile) {
                State.selectedBranch = { ...profile, name, color, logoUrl, secondColor };
                State.selectedBranchId = profile.id || null;
                App.updateCompanyFooter(State.selectedBranch);
            }

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

            // set CSS variables so entire layout updates
            document.documentElement.style.setProperty('--brand-color', color);
            document.documentElement.style.setProperty('--second-color', secondColor); // Apply second color
            document.getElementById('color-hex-label').innerText = color;

            // If already in editor, re-render to apply changes everywhere
            if (document.getElementById('view-editor')?.classList.contains('active')) {
                App.applyBrandingToCover();
                App.renderQuotePage(false);
            }
        },


        startQuote: () => {
            // 1. Detect mode
            const isTemplateMode = document.getElementById('wiz-template-mode')?.checked || false;

            // 2. Read wizard values
            State.projectDate = document.getElementById('wiz-date')?.value || '';
            const typeRadios = document.getElementsByName('wiz-doc-type');
            typeRadios.forEach((t) => {
                if (t.checked) State.docType = t.value;
            });
  
            // 3. Basic labels
            const docTypeLabel = document.getElementById('editor-doc-type-label');
            const docIdNameLabel = document.getElementById('lbl-doc-id-name');
            const mainTitleEl = document.getElementById('doc-main-title');

            // ✅ FIX: Safely check if deal is closed
            const isDeal = App.isDeal();
            const displayType = isDeal ? 'Auftrag' : State.docType;
            console.log("isDeal evaluated:", isDeal, "Status:", State.docStatus);

            if (docTypeLabel) {
                docTypeLabel.innerText = displayType;
            }

            if (docIdNameLabel) {
                if (isDeal) {
                    docIdNameLabel.innerText = 'Auftragsnummer';
                } else {
                    docIdNameLabel.innerText = State.docType === 'Angebot'
                        ? 'Angebotsnummer'
                        : 'KVA-Nummer';
                }
            }

            if (mainTitleEl) {
                // FIX: If we have a saved custom title, ALWAYS use it!
                if (State.mainTitleHtml) {
                    mainTitleEl.innerHTML = State.mainTitleHtml;
                } else {
                    // Fallback default logic
                    const txt = mainTitleEl.innerText;
                    if (txt.includes('Unverbindliches') || txt.includes('Kostenvoranschlag') || txt.includes('Auftrag für') || txt.trim() === '') {
                        if (isDeal) {
                            mainTitleEl.innerText = `Auftrag für...`;
                        } else {
                            mainTitleEl.innerText = `Unverbindliches ${State.docType} für...`;
                        }
                    }
                }
            }

            // 4. Page-1 customer references
            const custNameEl = document.getElementById('doc-cust-name');
            const custLastEl = document.getElementById('doc-cust-lastname');
            const custAddrEl = document.getElementById('doc-cust-addr');
            const custIdInput = document.getElementById('doc-cust-id');
            const custPhotoWrap = document.getElementById('doc-cust-photo-wrap');
            const custPhoto = document.getElementById('doc-cust-photo');

            // 5. Fill customer/template data
            if (isTemplateMode) {
                if (custNameEl) custNameEl.innerText = 'VORLAGE';
                if (custLastEl) custLastEl.innerText = 'MUSTERMANN';
                if (custAddrEl) custAddrEl.innerHTML = 'Musterstraße 1<br>12345 Musterstadt';
                if (custIdInput) custIdInput.value = 'TEMPLATE';

                if (custPhotoWrap) custPhotoWrap.classList.add('hidden');
                if (custPhoto) custPhoto.src = '';

                State.customer = {
                    name: 'Vorlage',
                    lastname: 'Mustermann',
                    customer_no: 'TEMPLATE'
                };

                State.object = State.object || { items: [] };
            } else {
                const customer = State.customer || {};

                const firstName =
                    customer.name ||
                    customer.firstname ||
                    customer.first_name ||
                    '';

                const lastName =
                    customer.lastname ||
                    customer.last_name ||
                    customer.surname ||
                    '';

                const fullName =
                    [firstName, lastName].filter(Boolean).join(' ').trim() ||
                    customer.display_name ||
                    '';

                if (custNameEl) {
                    custNameEl.innerText = fullName;
                }

                if (custLastEl) {
                    custLastEl.innerText = lastName || firstName || '';
                }

                if (custAddrEl) {
                    custAddrEl.innerHTML = `${customer.street || ''}<br>${customer.postcode || ''} ${customer.city || ''}`;
                }

                if (custIdInput) {
                    custIdInput.value = customer.customer_no || '';
                }

                const customerImage =
                    customer.image ||
                    customer.image_url ||
                    customer.photo ||
                    customer.photo_url ||
                    customer.avatar ||
                    customer.avatar_url ||
                    customer.profile_image ||
                    customer.profile_image_url ||
                    '';

                if (customerImage && custPhotoWrap && custPhoto) {
                    custPhoto.src = customerImage;
                    custPhotoWrap.classList.remove('hidden');

                    custPhoto.onerror = function () {
                        custPhotoWrap.classList.add('hidden');
                        custPhoto.src = '';
                    };
                } else {
                    if (custPhotoWrap) custPhotoWrap.classList.add('hidden');
                    if (custPhoto) custPhoto.src = '';
                }
            }

            // 6. Date line
            const dateLine = document.getElementById('doc-date-line');
            if (dateLine) {
                const rawDate = State.projectDate ? new Date(State.projectDate) : new Date();
                const formattedDate = rawDate.toLocaleDateString('de-DE');
                dateLine.innerText = `Wehrheim, ${formattedDate}`;
            }

            // 7. Company Profile & Branding Injection
            const profileKey = document.getElementById('wiz-company-select')?.value || window.DefaultBranchProfileKey || 'solar-aspekt';
            const profile = App.getCompanyProfile(profileKey);

            if (profile) {
                State.selectedBranch = profile;
                State.selectedBranchId = profile.id || null;
                State.companyName = profile.name || profile.branch || State.companyName || 'SOLAR ASPEKT';
                State.brandLogoUrl = profile.logoUrl || State.brandLogoUrl || '';
                State.brandColor = profile.color || State.brandColor || '#93c21c';
                State.secondColor = profile.secondColor || State.brandColor;
                State.brandMode = State.brandLogoUrl ? 'image' : State.brandMode;
                State.companyFooter = App.getCompanyFooterSnapshot(profile);
            }

            document.documentElement.style.setProperty('--brand-color', State.brandColor);
            document.documentElement.style.setProperty('--second-color', State.secondColor || State.brandColor);

            // A) Update Logo Texts (Fallback if text mode is used)
            document.querySelectorAll('.pdf-logo-text').forEach((el) => {
                el.innerText = State.companyName;
            });
            const logoTextEl = document.getElementById('doc-logo-text');
            if (logoTextEl) logoTextEl.innerText = State.companyName;

            // B) Update Header Address Line
            const compHeaderEl = document.getElementById('doc-company-header');
            if (compHeaderEl && profile) {
                const cityLine = [profile.postcode, profile.city].filter(Boolean).join(' ');
                compHeaderEl.innerText = [State.companyName, profile.street, cityLine].filter(Boolean).join(' • ');
            }

            // C) Update Contact Person Block
            const contactNameEl = document.getElementById('doc-contact-name');
            if (contactNameEl && profile) contactNameEl.innerText = profile.contactPerson || profile.contact_person || '';
            const contactDetailsEl = document.getElementById('doc-contact-details');
            if (contactDetailsEl && profile) {
                contactDetailsEl.innerHTML = [
                    profile.phone ? `Tel: ${App.escapeHtml(profile.phone)}` : '',
                    profile.email ? `E-Mail: ${App.escapeHtml(profile.email)}` : ''
                ].filter(Boolean).join('<br>');
            }

            // D) Update Footer Columns from selected Branch
            if (profile) App.updateCompanyFooter(profile);

            // E) Update Team Name in Cover Letter
            const teamNameEl = document.getElementById('doc-team-name');
            if (teamNameEl) {
                teamNameEl.innerText = `Ihr ${State.companyName}-Team`;
            }

            // 8. Apply visual branding
            App.applyBrandingToCover();

            // 9. Cover text init
            const coverEl = document.getElementById('doc-cover-text');
            if (coverEl) {
                if (!State.coverTextHtml) {
                    State.coverTextHtml = coverEl.innerHTML.trim();
                } else {
                    coverEl.innerHTML = State.coverTextHtml;
                }
            }

            // 10. Ensure at least one section
            if (State.sections.length === 0) {
                App.addSection('1. Hauptpositionen', false);
            }

            // 11. Render and switch view
            App.renderSidebar();
            App.renderQuotePage();
            App.switchView('editor');

            // 12. Open correct editor tab
            if (isTemplateMode) {
                App.Tabs.switch('templates');
            } else {
                App.Tabs.switch('list');
            }
        },  
        // --- PAGE RENDERER ---
        createPage: (idx, forPrint, isClosing = false) => {
            // 1. Clean the string to be absolutely safe
            const isDeal = App.isDeal();
            const docType = State.docType || 'Angebot';
            
            // 2. Set dynamic text variables based on the status
            let headerBadge = docType === 'Angebot' ? 'ANGEBOT' : 'KOSTENVORANSCHLAG';
            let mainTitleText = `Unverbindliches ${docType} für...`;
            let idLabelText = docType === 'Angebot' ? 'Angebotsnummer' : 'KVA-Nummer';
            
            if (isDeal) {
                headerBadge = 'AUFTRAG';
                mainTitleText = 'Auftrag für...';
                idLabelText = 'Auftragsnummer';
            }

            const div = document.createElement('div');
            div.className = 'a4-page flex-shrink-0 dynamic-page relative';
            div.id = `page-${idx}`;

            if (!forPrint) {
                div.ondragover = (e) => App.allowDrop(e);
                div.ondrop = (e) => App.dropTool(e, idx);
            }

            const companyName = App.escapeHtml(State.companyName || 'SOLAR ASPEKT');
            const offerId = App.escapeHtml(State.offerId || '');

            const logoHtml = (State.brandMode === 'image' && State.brandLogoUrl)
                ? `
                    <div class="absolute top-30 right-10">
                        <img
                            src="${State.brandLogoUrl}"
                            alt="Logo"
                            style="height:28px; max-width:180px; object-fit:contain;margin-right:13px;"
                        >
                    </div>
                `
                : `
                    <div class="pdf-logo-text absolute top-6 right-10 text-sm font-black">
                        ${companyName}
                    </div>
                `;

            const headerGridHtml = isClosing ? '' : `
                <div class="pos-header-grid" style="border-bottom-color: var(--second-color,--brand-color);">
                    <div class="text-center">Pos.</div>
                    <div>Artikelbezeichnung</div>
                    <div class="text-right">Menge</div>
                    <div class="text-left"></div>
                    <div class="text-right">EP</div>
                    <div class="text-right">GP</div>
                </div>
            `;

            // 3. Inject the dynamic variables into the HTML
            div.innerHTML = `
                ${logoHtml}

                <div class="mt-1 mb-2">
                    <div class="flex justify-between items-end pb-1" style="border-bottom:2px solid var(--brand-color);">
                        <div class="font-bold" style="color:var(--brand-color);">
                            ${headerBadge}
                            <span class="sync-offer-id text-[#000000] ml-1" style="font-size: 10px; font-weight: 300;">${offerId}</span>
                        </div>
                    </div>
                </div>

                ${headerGridHtml}

                <div class="page-content flex-1 relative"></div>

                <div class="mt-auto pt-2 text-[13px] text-[#000000] text-center mb-4" style="border-top:1px solid var(--brand-color);">
                    Seite ${idx}
                </div>
            `;
 
           // 4. Update the Letterhead content dynamically on Page 1
            if (idx === 1) {
                // We add a listener to rebuild the header so it doesn't get wiped out.
                setTimeout(() => {
                    const idLabelEl = div.querySelector('#lbl-doc-id-name');
                    if (idLabelEl) idLabelEl.innerText = idLabelText;

                    const mainTitleEl = div.querySelector('#doc-main-title');
                    if (mainTitleEl) {
                        // FIX: Use the saved title if it exists, otherwise fallback to the default text
                        mainTitleEl.innerHTML = State.mainTitleHtml ? State.mainTitleHtml : mainTitleText;
                    }
                }, 0);
            }

            return div;
        },
       // 1. Returns ONLY the Summary Table (ZUSAMMENSTELLUNG ABSCHNITTE)
        renderSectionSummaryBlock: function (forPrint = false) {
            if (State.docType !== 'Angebot') return '';

            const sum = App.computeSectionSummary(forPrint);
            const brand = 'var(--brand-color)';
            const sections = Array.isArray(sum.sections) ? sum.sections : [];

            if (sections.length === 0) return '';

            const rows = sections.map((sec, idx) => `
                <div class="pdf-summary-row">
                    <div class="pdf-summary-row-left">
                        <div class="pdf-summary-row-no" style="color:${brand};">${idx + 1}.</div>
                        <div class="pdf-summary-row-label">${App.escapeHtml(sec.label || 'Abschnitt')}</div>
                    </div>
                    <div class="pdf-summary-row-value">${App.money(sec.net)} EUR</div>
                </div>
            `).join('');

            return `
                <div class="pdf-summary-card break-inside-avoid mt-6">
                    <div class="pdf-summary-card-title" style="color:${brand};">
                        ZUSAMMENSTELLUNG ABSCHNITTE
                    </div>

                    <div class="pdf-summary-list">
                        ${rows}
                    </div>

                    <div class="pdf-summary-totals"> 

                        <div class="pdf-summary-total-row">
                            <span>Nettogesamtpreis</span>
                            <span>${App.money(sum.netTotal)} EUR</span>
                        </div>

                        <div class="pdf-summary-total-row">
                            <span>Umsatzsteuer ${Number(sum.vatRate || 0).toLocaleString('de-DE', {
                                minimumFractionDigits: 1,
                                maximumFractionDigits: 1
                            })}%</span>
                            <span>${App.money(sum.vatValue)} EUR</span>
                        </div>

                        <div class="pdf-summary-total-row grand" style="border-top-color:${brand}; color:${brand};">
                            <span>Gesamtsumme</span>
                            <span>${App.money(sum.gross)} EUR</span>
                        </div>
                    </div>
                </div>
            `;
        },

        // 2. Returns ONLY the final closing text and signature block
        renderOfferFinalPage: function () {
                if (State.docType !== 'Angebot') return '';
                const brand = 'var(--brand-color)';

                return `
                    <div class="pdf-offer-final-page break-inside-avoid">
                        <div class="pdf-offer-final-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                            IHR WEG ZUR EIGENEN PHOTOVOLTAIKANLAGE – WIR MACHEN ES EINFACH!
                        </div>

                        <div class="pdf-offer-final-intro editable-field" contenteditable="true">
                            Mit der Entscheidung für eine Photovoltaikanlage investieren Sie in eine nachhaltige Zukunft,
                            reduzieren Ihre Energiekosten und gewinnen mehr Unabhängigkeit. Wir begleiten Sie von Anfang an
                            und kümmern uns um den gesamten Prozess – von der Planung bis zur Inbetriebnahme.
                        </div>

                        <div class="pdf-offer-process">
                            <div class="pdf-offer-process-label editable-field w-fit" contenteditable="true">
                                Lehnen Sie sich entspannt zurück – wir übernehmen für Sie:
                            </div>

                            <div class="pdf-offer-process-step is-soft">
                                <div class="pdf-offer-process-step-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                                    1. Auftragserteilung & erste Abschlagszahlung (20% der Auftragssumme)
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Sobald Sie uns beauftragen, erhalten Sie eine schriftliche Auftragsbestätigung sowie die erste Abschlagsrechnung.
                                    Bitte überprüfen Sie die Rechnungsadresse auf Richtigkeit. Anschließend stellen wir eine Voranfrage beim Netzbetreiber
                                    zur Genehmigung der Stromeinspeisung. Sobald diese vorliegt und die Zahlung eingegangen ist, stellen wir die benötigten
                                    Materialien zusammen und ergänzen diese durch gezielte Bestellungen. Anschließend erhalten Sie Ihren individuellen
                                    Liefer- und Montagetermin.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-soft">
                                <div class="pdf-offer-process-step-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                                    2. Lieferung & Montagebeginn – zweite Abschlagszahlung (60% der Auftragssumme)
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Mit der Lieferung der Komponenten beginnt die fachgerechte Montage und Sie erhalten die zweite Abschlagsrechnung.
                                    Unsere erfahrenen Monteure sorgen für eine präzise Umsetzung, sodass Ihre Anlage sicher und effizient arbeitet.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-soft">
                                <div class="pdf-offer-process-step-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                                    3. Fachgerechte Montage & Inbetriebnahme
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Nach Abschluss der Installation nehmen wir Ihre Anlage in Betrieb – damit Sie so schnell wie möglich von der Sonnenenergie profitieren.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-plain">
                                <div class="pdf-offer-process-step-title alt editable-field w-fit" contenteditable="true">
                                    4. Behördliche Anmeldung – wir übernehmen das für Sie!
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Die Registrierung der Anlage beim Netzbetreiber und im Marktstammdatenregister der Bundesnetzagentur ist erforderlich –
                                    wir erledigen das für Sie. Zudem veranlassen wir den Austausch Ihres Stromzählers durch einen Zwei-Richtungs-Zähler,
                                    der die Einspeisung ins Netz ermöglicht.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-plain">
                                <div class="pdf-offer-process-step-title alt editable-field w-fit" contenteditable="true">
                                    5. Einweisung & Nutzung Ihrer Photovoltaikanlage
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Damit Sie Ihre Anlage optimal nutzen können, erklären wir Ihnen die wichtigsten Funktionen und individuellen Besonderheiten.
                                    So holen Sie das Beste aus Ihrer Investition heraus.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-soft">
                                <div class="pdf-offer-process-step-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                                    6. Schlussrechnung (20% der Auftragssumme) & fortlaufender Service
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Nach erfolgreicher Inbetriebnahme erhalten Sie die Schlussrechnung. Auch danach sind wir für Sie da –
                                    bei Fragen oder Anliegen stehen wir Ihnen mit Rat und Tat zur Seite.
                                </div>
                            </div>

                            <div class="pdf-offer-process-step is-plain">
                                <div class="pdf-offer-process-step-title alt editable-field w-fit" contenteditable="true">
                                    7. Potenzielle Anpassung Ihrer Stromabschläge
                                </div>
                                <div class="pdf-offer-process-step-text editable-field" contenteditable="true">
                                    Möglicherweise können Sie durch Ihre eigene Stromerzeugung Ihre Abschlagszahlungen beim Energieversorger reduzieren.
                                    Informieren Sie sich dazu direkt bei Ihrem Anbieter.
                                </div>
                            </div>
                        </div>

                        <div class="pdf-offer-final-info">
                            <div class="pdf-offer-final-info-block">
                                <div class="pdf-offer-final-info-title alt editable-field w-fit" contenteditable="true">
                                    Fair & Transparent – Unsere Vereinbarung
                                </div>
                                <div class="pdf-offer-final-info-text editable-field" contenteditable="true">
                                    Für Ihr Projekt gelten unsere aktuellen Allgemeinen Geschäftsbedingungen (AGB),
                                    die Sie jederzeit auf Wunsch einsehen oder in schriftlicher Form anfordern können.
                                </div>
                            </div>

                            <div class="pdf-offer-final-info-block">
                                <div class="pdf-offer-final-info-title alt editable-field w-fit" contenteditable="true">
                                    Gemeinsam zum Erfolg
                                </div>
                                <div class="pdf-offer-final-info-text editable-field" contenteditable="true">
                                    Wir setzen auf eine vertrauensvolle Zusammenarbeit und stehen Ihnen jederzeit beratend zur Seite.
                                    Falls während des Projekts Fragen aufkommen, finden wir gemeinsam die beste Lösung.
                                    Mit uns haben Sie einen zuverlässigen Partner an Ihrer Seite, der Ihr Vorhaben mit höchster
                                    Sorgfalt und Qualität realisiert.
                                </div>
                            </div>

                            <div class="pdf-offer-final-info-block">
                                <div class="pdf-offer-final-info-text editable-field" contenteditable="true">
                                    Dieses Angebot ist freibleibend. Irrtümer und technische Änderungen bleiben vorbehalten.
                                    Ein verbindlicher Vertrag kommt erst mit schriftlicher Auftragsbestätigung durch
                                    SOLAR ASPEKT GmbH zustande. Das Angebot ist vier Wochen gültig, sofern keine abweichende
                                    Vereinbarung getroffen wurde.
                                </div>
                            </div>
                        </div>

                        <div class="pdf-offer-sign-title editable-field w-fit" contenteditable="true" style="color:${brand};">
                            AUFTRAGSBESTÄTIGUNG
                        </div>

                        <div class="pdf-offer-sign-text editable-field" contenteditable="true">
                            Ich nehme das Angebot an und bestelle verbindlich die im Angebot aufgeführten Leistungen und Komponenten.
                            Die darin enthaltenen Hinweise sowie die allgemeinen Geschäftsbedingungen habe ich zur Kenntnis genommen
                            und akzeptiere sie als Vertragsbestandteil.
                        </div>

                        <div class="pdf-offer-sign-row">
                            <div class="pdf-offer-sign-left">
                                <div class="pdf-offer-sign-line"></div>
                                <div class="pdf-offer-sign-caption editable-field w-fit" contenteditable="true">Ort, Datum, Unterschrift</div>

                                <div class="pdf-offer-remark-label editable-field w-fit" contenteditable="true">Eventuelle Bemerkungen:</div>
                                <div class="pdf-offer-remark-line"></div>
                                <div class="pdf-offer-remark-line"></div>
                            </div>

                            <div class="pdf-offer-sign-note">
                                <div class="pdf-offer-sign-note-head editable-field w-fit" contenteditable="true">HINWEIS</div>
                                <div class="pdf-offer-sign-note-text editable-field" contenteditable="true">
                                    Für die Beauftragung senden Sie bitte das vollständige, unterschriebene Angebot inkl. markierter Optionen zurück.
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            },
        // 3. The Main Render Engine
        renderQuotePage: (forPrint = false) => {
            // 1. Setup Containers & State
           const container = forPrint ? document.getElementById('print-preview-content') : document.getElementById('position-pages-container');
            
            // Only clear containers if we are NOT in print mode. 
            // In print mode, App.buildPrintPreview handles the clearing!
            if (!forPrint) {
                container.innerHTML = '';
                document.getElementById('nav-pane').innerHTML = '';
            }

            const showHidden = forPrint ? false : document.getElementById('show-hidden-toggle').checked;

            // 2. Pagination State
            let pageIndex = 2; // Page 1 is the cover letter
            let currentPage = App.createPage(pageIndex, forPrint);
            container.appendChild(currentPage);
            let contentBox = currentPage.querySelector('.page-content');

            App.renderFloatingImages(currentPage, pageIndex, forPrint);

            let posCounter = 1;
  
           // 3. Helper: Add Element to Page with Overflow Check
            const addToPage = (element, isClosing = false) => {
                contentBox.appendChild(element);

                // Check for overflow (Mit 5px Puffer für sichere Berechnung)
                if (contentBox.scrollHeight > contentBox.clientHeight + 5) {
                    
                    // SMART CHECK: Ist dieses Element SO groß, dass es eine ganze Seite alleine füllt?
                    if (contentBox.children.length === 1) {
                        return false; // Muss hier bleiben, sonst entsteht eine Endlosschleife
                    }

                    // Andernfalls: Es passt hier nicht mehr, verschiebe es auf die neue Seite!
                    contentBox.removeChild(element); 

                    // Create a New Page
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint, isClosing);
                    container.appendChild(currentPage);
                    contentBox = currentPage.querySelector('.page-content');

                    App.renderFloatingImages(currentPage, pageIndex, forPrint);

                    // Append the element to the newly created page
                    contentBox.appendChild(element);
                    return true; // Zeigt an, dass ein Seitenumbruch stattfand
                }
                return false;
            };

         // 4. Recursive Row Renderer (Handles Level 1, 2, 3...)
            const createRowHtml = (item, context, level, posNumberString) => {
                const { sIdx, iIdx, subIdx } = context;
                const section = State.sections?.[sIdx];
                const isLocked = !!section?.isLocked;
                
                const subArg = (subIdx === null || subIdx === undefined || subIdx === 'null') ? 'null' : subIdx;
                const isSub = subArg !== 'null';

                let rowClasses = `item-group group relative`;
                let posColor = level === 1 ? "text-[#000000]" : (level >= 2 ? "text-[#000000]" : "text-[#000000]");
                let namePrefix = level >= 2 ? `<i class="fa-solid fa-turn-up rotate-90 mr-2 text-[8px] text-[#000000]"></i>` : "";

                const itemStatus = item.status || 'normal';
                const isItemOpt = itemStatus === 'optional';
                const isItemAlt = itemStatus === 'alternative';

                if (!item.active) rowClasses += ' pos-inactive';

                const total = App.calcItemGross(item);
                const hidePrices = (item.isPauschal || item.hidePrices || State.sections[sIdx].config.hidePrices);
                const ctxFn = (level === 0) ? `App.updateItemDetails(${sIdx},${iIdx},` : `App.updateSubItemDetails(${sIdx},${iIdx},${subArg},`;

                let inlineBadge = '';
                if (isItemOpt || isItemAlt) {
                    const labelText = isItemOpt ? 'optional' : 'alternativ';
                    inlineBadge = `<span class="text-[13px] font-bold ml-1.5" style="color: var(--brand-color);">${labelText}</span>`;
                }

                let dragAttrs = '';
                let handleHtml = '';
                if (!forPrint && !isLocked) {
                    dragAttrs = `draggable="false" ondragover="App.handlePosDragOver(event, this)" ondragleave="App.handlePosDragLeave(this)" ondrop="App.handlePosDrop(event, this, ${sIdx}, ${iIdx}, ${subArg})"`;
                    handleHtml = `<span class="drag-handle absolute -left-7 top-[-2px] no-print" draggable="true" ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx}, ${subArg})" title="Verschieben"><i class="fa-solid fa-grip-lines"></i></span>`;
                }

                const nameVal = forPrint
                    ? `<div class="flex items-baseline">${namePrefix}<span>${App.escapeHtml(item.name)}</span>${inlineBadge}</div>`
                    : `<div class="flex items-center w-full">
                        ${namePrefix}
                        <input class="clean-input font-bold text-[#000000] w-auto flex-1 min-w-[50px] border-b border-transparent hover:border-dashed hover:border-[#93c21c] focus:border-solid focus:border-[#93c21c] transition-colors" value="${App.escapeHtml(item.name)}" onchange="${ctxFn}'name',this.value)">
                        ${inlineBadge}
                    </div>`;

                // ✅ SEPARATED QUANTITY
                const qtyHtml = forPrint 
                    ? `<div class="text-right font-bold text-[#000000]">${App.escapeHtml(item.qty)}</div>`
                    : `<div class="text-right flex items-center justify-end"><input type="number" step="0.01" class="clean-input text-right font-bold text-[#000000] w-12 border-b border-transparent hover:border-dashed hover:border-[#93c21c] focus:border-solid focus:border-[#93c21c] transition-colors" value="${App.escapeHtml(item.qty)}" onchange="${ctxFn}'qty',this.value)"></div>`;
                
                // ✅ SEPARATED UNIT
                const unitHtml = forPrint
                    ? `<div class="text-left font-bold text-[#000000]">${App.escapeHtml(item.measure || item.unit || 'Stk.')}</div>`
                    : `<div class="text-left flex items-center justify-start"><select class="clean-input text-left bg-transparent text-[#000000] w-auto border-b border-transparent hover:border-dashed hover:border-[#93c21c] focus:border-solid focus:border-[#93c21c] transition-colors appearance-none cursor-pointer" onchange="${isSub ? `App.updateSubItemUnit(${sIdx},${iIdx},${subArg},this.value)` : `App.updateItemUnit(${sIdx},${iIdx},this.value)`}">${App.renderUnitOptions(item.measure || item.unit || 'Stk')}</select></div>`;
                
                const formatPrice = (val) => {
                    const str = val.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR';
                    return (isItemOpt || isItemAlt) ? `(${str})` : str;
                };

                const epDisplay = hidePrices ? '-' : formatPrice(item.price);
                const gpDisplay = hidePrices ? '-' : formatPrice(total);
 
                const descHtml = App.getItemDescHtml(item);
                const hasLaborRows = Array.isArray(item.labor_rows) && item.labor_rows.length > 0;

                // NEW: Safely evaluate the database value. Default to TRUE (hidden) if undefined/null.
                const isLaborHidden = (item.print_hidden_labor === undefined || item.print_hidden_labor === null) 
                    ? true 
                    : (item.print_hidden_labor === true || item.print_hidden_labor === '1' || item.print_hidden_labor === 1);

                let renderLabor = hasLaborRows;
                if (isLaborHidden) {
                    if (forPrint || !showHidden) {
                        renderLabor = false;
                    }
                }
                const laborTableHtml = renderLabor ? App.renderLaborRowsTable(item, forPrint, sIdx, iIdx, subIdx) : '';
                const formattedDescHtml = App.formatDescHtmlForPdf(descHtml, '');
                
                let descVal = '';
                if (forPrint) {
                    descVal = formattedDescHtml + laborTableHtml;
                } else {
                    // UX Bonus: Show a small warning in the editor if the table is hidden for print
                    const hiddenWarning = (item.print_hidden_labor === true && renderLabor) 
                        ? `<div class="text-[10px] text-red-500 font-bold mt-1 mb-1 no-print"><i class="fa-solid fa-eye-slash"></i> Lohndetails werden im Druck ausgeblendet</div>` 
                        : '';
                        
                    if (formattedDescHtml) {
                        descVal = `
                            <div class="editable-field border border-transparent hover:border-dashed hover:border-[#93c21c] cursor-pointer transition-colors" onclick="App.openDescModal(${sIdx},${iIdx},${subArg})">
                                ${formattedDescHtml}
                            </div>
                            ${hiddenWarning}
                            ${laborTableHtml}
                        `;
                    } else {
                        descVal = `
                            <div class="text-[10px] text-slate-300 italic cursor-pointer hover:text-[#93c21c] no-print mt-1 inline-block transition-colors" onclick="App.openDescModal(${sIdx},${iIdx},${subArg})">
                                <i class="fa-solid fa-plus"></i> Beschreibung hinzufügen
                            </div>
                            ${hiddenWarning}
                            ${laborTableHtml}
                        `;
                    }
                }

                let badgeHtml = '';
                if (item.badge) {
                    const p = item.badge.pos;
                    const posCls = p === 'tl' ? 'top-0 left-0' : p === 'tr' ? 'top-0 right-0' : p === 'bl' ? 'bottom-0 left-0' : 'bottom-0 right-0';
                    badgeHtml = item.badge.type === 'text'
                        ? `<div class="absolute ${posCls} text-white text-[8px] font-bold px-1 rounded z-10" style="background:var(--brand-color);">${item.badge.text}</div>`
                        : `<img src="${item.badge.src}" class="absolute ${posCls} w-6 h-6 object-contain z-10">`;
                }

               const imgW = item.imgWidth || 119;
                const imgH = item.imgHeight || 135;

                const imgHtml = (item.hideImage || item.showImage === false) ? ''
                    : `<div class="prod-img-container" style="width: ${imgW}px; height: ${imgH}px;" 
                            onclick="${!forPrint ? `App.handleImageClick(event, ${sIdx},${iIdx},${subArg})` : ''}"
                            onmouseup="${!forPrint ? `App.saveImageSize(this, ${sIdx},${iIdx},${subArg})` : ''}">
                            <img src="${item.img || 'https://placehold.co/150?text='}" class="w-full h-full object-cover bg-white">
                            ${badgeHtml}
                        </div>`;
                const isHiddenPrint = item.print_hidden !== false;
                const eyeClass = isHiddenPrint ? 'text-red-500' : 'text-green-500';
                const eyeIcon = isHiddenPrint ? 'fa-eye-slash' : 'fa-eye';

                const tools = forPrint ? '' : `
                    <div class="pos-tools-container absolute -top-4 right-0 flex gap-2 items-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white/95 px-2 py-1 rounded shadow border border-slate-200 z-10 no-print">
                        <button onclick="App.updatePosConfig(${sIdx},${iIdx},${subArg},'print_hidden', ${!isHiddenPrint})" class="text-[12px] ${eyeClass} hover:opacity-80" title="Druck-Sichtbarkeit"><i class="fa-solid ${eyeIcon}"></i></button>
                        <button onclick="App.addSubItem(${sIdx},${iIdx})" class="text-[10px] text-slate-500 hover:text-[var(--brand-color)]" title="Unterposition"><i class="fa-solid fa-plus"></i></button>
                        <button onclick="App.openPosSettings(${sIdx},${iIdx},${subArg})" class="text-[10px] text-slate-500 hover:text-[var(--brand-color)]" title="Einstellungen"><i class="fa-solid fa-cog"></i></button>
                        <button onclick="App.removeItem(${sIdx},${iIdx},${subArg})" class="text-[10px] text-red-300 hover:text-red-500" title="Löschen"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `;

                const currentKind = item.kind || (item.item_type === 'labor' ? 'labor' : 'article');
                if (currentKind === 'note') {
                    const noteNameVal = forPrint
                        ? `<div class="pdf-note-title">${App.escapeHtml(item.name)}</div>`
                        : `<input class="clean-input font-bold text-[#000000] w-full text-base mb-2 border-b border-transparent hover:border-dashed hover:border-[#93c21c] transition-colors" value="${App.escapeHtml(item.name)}" onchange="${ctxFn}'name',this.value)" placeholder="Hinweistitel...">`;

                    return `
                        <div id="a4-pos-row-${sIdx}-${iIdx}-${subArg}" class="pdf-note-box group ${item.active ? '' : 'pos-inactive'}" ${dragAttrs}>
                            <div class="flex items-center gap-2 relative">
                                ${handleHtml}
                                ${noteNameVal}
                                ${tools}
                            </div>
                            <div class="pdf-desc-block">${descVal}</div>
                        </div>
                    `;
                }

                const bodyClass = (item.hideImage || item.showImage === false) ? 'pos-row-bottom no-image' : 'pos-row-bottom';
                const wrapperClass = `pdf-item-card ${level > 0 ? 'pdf-subitem' : ''}`;

                let extensionCheckbox = '';
                if (isItemOpt || isItemAlt) {
                    const extText = isItemOpt ? 'JA, ICH WÜNSCHE DIE ERWEITERUNG' : 'JA, ICH WÜNSCHE DIE ALTERNATIVE';
                    extensionCheckbox = `
                        <div class="mt-4 mb-2 flex justify-end items-center gap-2" style="color: var(--brand-color);">
                            <div class="w-[16px] h-[16px] rounded-full border-[1.5px] flex items-center justify-center bg-transparent" style="border-color: var(--brand-color);"></div>
                            <span class="text-[9px] font-black uppercase tracking-wide">${extText}</span>
                        </div>
                    `;
                }

                // ✅ RENDER EXACTLY 6 DIVS INSIDE pos-row-top TO MATCH CSS GRID
                return `
                    <div id="a4-pos-row-${sIdx}-${iIdx}-${subArg}" class="${wrapperClass} ${rowClasses}" ${dragAttrs}>
                        <div class="pos-row-top ${level > 0 ? 'compact' : ''} relative">
                            <div class="pdf-pos-no text-left font-bold ${posColor} relative">
                                ${handleHtml}
                                <span>${posNumberString}</span>
                            </div>
                            <div class="pdf-main-title">${nameVal}</div>
                            ${qtyHtml}
                            ${unitHtml}
                            <div class="text-right font-bold text-[#000000]">${epDisplay}</div>
                            <div class="text-right font-bold text-[#000000]">${gpDisplay}</div>
                        </div>
                        <div class="${bodyClass} relative">
                            ${(item.hideImage || item.showImage === false) ? '' : imgHtml}
                            <div class="flex-1 min-w-0 relative">
                                <div class="pdf-desc-block ${hasLaborRows ? 'w-full' : ''}">
                                    ${descVal}
                                </div>
                                ${extensionCheckbox}
                                ${tools}
                            </div>
                        </div>
                    </div>
                `;
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
                header.className = 'mb-1 mt-4 relative'; // <--- Add 'relative'
                header.id = `a4-sec-head-${sIdx}`;
                header.className = 'mb-1 mt-4';
                let secBadges = isOptSection ? '(Optional)' : (isAltSection ? '(Alternativ)' : '');

                // NEW: Visual Set Label for A4 & Print
                const secQty = sec.config?.qty || 1;
                const isSet = (sec.config?.unit || '').toLowerCase() === 'set';
                const setLabel = isSet ? `<span style="color:var(--brand-color); font-weight:600; margin-right:8px;">${secQty}x Set:</span>` : '';

                if (sec && sec._pageBreak) {
                    addToPage(document.createElement('div')); // Force overflow check
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint);
                    container.appendChild(currentPage);
                    contentBox = currentPage.querySelector('.page-content');
                    App.renderFloatingImages(currentPage, pageIndex, forPrint);
                    return;
                }

               let deleteBtn = !forPrint ? `<button onclick="App.removeSection(${sIdx})" class="ml-auto text-slate-300 hover:text-red-500 p-1 rounded hover:bg-red-50 transition-colors"><i class="fa-solid fa-trash"></i></button>` : '';

                // FIX: Add "|| ''" to title and description so null becomes empty
                header.innerHTML = forPrint
                ? `<div class="text-lg font-bold text-[#4c4c4c] uppercase">${setLabel}${App.escapeHtml(sec.title || '')} ${secBadges}</div><div class="text-sm text-[#000000]">${App.escapeHtml(sec.description || '')}</div>`
                : `<div class="flex items-center">
                    ${setLabel}
                    <input value="${App.escapeHtml(sec.title || '')}" oninput="App.updateSectionMeta(${sIdx},'title',this.value)" class="text-lg font-bold text-[#4c4c4c] w-full bg-transparent outline-none">
                        <span class="text-xs text-[#000000] ml-2 whitespace-nowrap">${secBadges}</span>
                        ${deleteBtn}
                    </div>
                    <textarea oninput="App.updateSectionMeta(${sIdx},'description',this.value)" class="text-sm text-[#000000] w-full bg-transparent resize-none outline-none h-auto">${sec.description || ''}</textarea>`;
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
                const processItems = (items, level, parentIdxStr, parentContext) => {
                    let localSum = 0;
                    let localEk = 0;

                    items.forEach((item, idx) => {
                       const isHiddenPrint = item.print_hidden !== false;
                        if (!item.active && !showHidden) return;
                        if (isHiddenPrint && !showHidden && !forPrint) return; // Hide from A4 view if hidden
                        if (forPrint && isHiddenPrint) return;

                        // Calculate visual index string (e.g. 1.2.1)
                        const currentPosStr = (level === 0)
                            ? (item.hideNumbering ? '' : String(posCounter++).padStart(3, '0'))
                            : (item.hideNumbering ? '' : `${parentIdxStr}.${idx + 1}`);

                        const currentContext = {
                            sIdx: sIdx,
                            iIdx: (level === 0) ? idx : parentContext.iIdx,
                            subIdx: (level === 0) ? null : idx
                        };

                        // --- Price Calc ---
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
                        
                        // Global Totals
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
                        addToPage(rowEl.firstElementChild);

                        if (!forPrint && level === 0) {
                            const intoParentZone = document.createElement('div');
                            // Klasse 'section-drop-zone' hinzugefügt, statische border entfernt
                            intoParentZone.className = 'section-drop-zone ml-12 mb-2 px-3 py-2 text-[10px] text-[#000000] bg-slate-50 no-print';
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
                            // Klasse 'section-drop-zone' hinzugefügt, statische border entfernt
                            toMainZone.className = 'section-drop-zone mb-3 px-3 py-2 text-[10px] text-[#000000] bg-white no-print';
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
                                const subLevel = Number(sub.depth || 1);

                                if (subLevel >= 2 && sub.active === false && !showHidden) return;
                                if (sub.active === false && !showHidden) return;
                                const isSubHidden = sub.print_hidden !== false;
                                if (forPrint && isSubHidden) return;

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

                // 5e. Flat Rate (Pauschal) & Add Buttons per Section
               if (isPauschalSection) {
                    const pr = document.createElement('div');
                    pr.className = "flex justify-end mt-2 pr-16 font-bold text-[#000000] text-sm border-t border-slate-300 pt-2";
                    pr.innerHTML = `<span>Pauschalpreis:</span><span class="ml-8 ">${sec.config.pauschalPrice.toLocaleString('de-DE')} €</span>`;
                    addToPage(pr);
                    totalNet += sec.config.pauschalPrice;
                } 
            });

            // 6. Global Drop Zone & Totals
            if (!forPrint) {
                let dzG = document.createElement('div');
                dzG.className = 'section-drop-zone border-2 border-dashed border-slate-300 rounded flex items-center justify-center text-[#000000] text-xs py-6 mt-4';
                dzG.innerText = 'Abschnitt'; // "New Section"
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

            // ✅ 1. RENDER SUMMARY TABLE
            // It will be added normally after the last item. `addToPage` will automatically
            // push it to a new page ONLY if there isn't enough space left.
            const summaryHtml = App.renderSectionSummaryBlock(forPrint);
            if (summaryHtml) {
                const summaryWrap = document.createElement('div');
                summaryWrap.innerHTML = summaryHtml;
                const summaryNode = summaryWrap.firstElementChild;
                
                if (summaryNode) {
                    addToPage(summaryNode, false); // Add to current flow
                }
            }

            // ✅ 2. RENDER FINAL TEXT & SIGNATURE (FORCED NEW PAGE)
            if (State.docType === 'Angebot') {
                const finalPageHtml = App.renderOfferFinalPage();
                
                if (finalPageHtml) {
                    const finalWrap = document.createElement('div');
                    finalWrap.innerHTML = finalPageHtml;
                    const finalNode = finalWrap.firstElementChild;
                    
                    if (finalNode) {
                        // Force a new page for the final text
                        pageIndex++;
                        
                        // "true" means isClosing is true -> Hides the table headers (Pos, Qty, Price)
                        currentPage = App.createPage(pageIndex, forPrint, true);
                        container.appendChild(currentPage);
                        contentBox = currentPage.querySelector('.page-content');
                        App.renderFloatingImages(currentPage, pageIndex, forPrint);

                        // Attach the final text to the blank page
                        contentBox.appendChild(finalNode);
                    }
                }

                // If you still have extra footer blocks, append them securely
                const footerBlocks = App.renderOfferFooterBlocks();
                footerBlocks.forEach(html => {
                    const wrap = document.createElement('div');
                    wrap.innerHTML = html.trim();
                    const node = wrap.firstElementChild;
                    if (node) addToPage(node, true);
                });
            }
            
            // 7. Update UI Stats & Generate Thumbnails
            if (!forPrint) {
                const totals = App.computeSectionSummary();

                document.getElementById('sidebar-grand-net').innerText = App.money(totals.netTotal) + ' €';
                document.getElementById('sidebar-grand-gross').innerText = App.money(totals.vatValue) + ' €';
                document.getElementById('sidebar-grand-total').innerText = App.money(totals.gross) + ' €';
                document.getElementById('lbl-total-pages').innerText = pageIndex;

                App.renderCalculationSidebar();
            }

            // CRITICAL NEW PLACEMENT: Must run universally once the DOM is established
            App.renderFloatingImages(forPrint);

            if (!forPrint) {
                // Must run AFTER renderFloatingImages so thumbnails see the elements!
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
            if(!c) return;

            // 1. CAPTURE STATE BEFORE WIPING HTML
            const uiState = App.captureSidebarUiState();

            const totals = App.computeQuoteTotals();
            const totalNet = totals.salesNet;
            
            let fullHtml = '';

            const renderCard = (isSub, sIdx, iIdx, subIdx, dataObj, prefix, hasChildren) => {
                if (dataObj.active === false) return '';
                
                const subIdxArg = isSub ? subIdx : 'null';
                const focusKeyBase = `${sIdx}:${iIdx}:${subIdxArg}`; // For focus tracking

                const qty = parseFloat(dataObj.qty) || 1;
                
                let ek = parseFloat(dataObj.ek) || 0;
                let vk = parseFloat(dataObj.price) || 0;
                if (ek === 0 && vk > 0) ek = vk;
                
                const totalEK = App.calcItemCost(dataObj);
                const totalVK = App.calcItemGross(dataObj);
                const marginTotal = totalVK - totalEK;
                
                const mType = dataObj.marginType || 'percent';
                let mVal = parseFloat(dataObj.margin) || 0;
                
                if (mVal === 0 && ek > 0 && vk !== ek) {
                    mVal = mType === 'percent' ? ((vk - ek) / ek) * 100 : (vk - ek);
                }
                
                let percent = (totalNet > 0 && dataObj.status !== 'optional' && dataObj.status !== 'alternative') ? ((totalVK/totalNet)*100).toFixed(1)+'%' : '-';

                const status = dataObj.status || 'normal';
                const isPauschal = !!dataObj.isPauschal;
                const hidePrices = !!dataObj.hidePrices;

                State.unlockedParentMargins = State.unlockedParentMargins || {};
                const isParentUnlocked = State.unlockedParentMargins[`${sIdx}-${iIdx}`];

                const isEkReadonly = (hasChildren && !isPauschal) ? 'readonly disabled class="w-full border border-transparent bg-transparent text-right font-bold text-[#000000]"' : 'class="w-full border border-slate-300 rounded px-1 py-0.5 text-right  focus:border-brand-primary outline-none"';
                const isVkReadonly = (hasChildren && !isPauschal && !isParentUnlocked) ? 'readonly disabled class="w-full border border-transparent bg-transparent text-right font-bold text-[#000000]"' : 'class="w-full border border-slate-300 rounded px-1 py-0.5 text-right  focus:border-brand-primary outline-none bg-yellow-50"';
                const isSelectReadonly = (hasChildren && !isPauschal && !isParentUnlocked) ? 'disabled class="outline-none bg-transparent font-bold text-[#000000] cursor-not-allowed"' : 'class="outline-none bg-transparent font-bold text-brand-primary cursor-pointer"';

                const marginHandler = (hasChildren && !isPauschal)
                    ? `App.applyGeneralMargin(${sIdx}, ${iIdx}, this.value)`
                    : `App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'margin',this.value)`;

                const typeHandler = (hasChildren && !isPauschal)
                    ? `App.updateParentMarginType(${sIdx}, ${iIdx}, this.value)`
                    : `App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'marginType',this.value)`;

                const indentClass = isSub ? 'border-l-4 border-brand-primary/40 bg-slate-50' : 'bg-white';
                const titleSize = isSub ? 'text-[11px] text-[#000000]' : 'text-xs text-[#000000]';
                const bgClass = status !== 'normal' ? 'opacity-75' : '';

                // Extract image safely (Removed the hiding CSS class so it always shows)
                const imgSrc = App.pickImage(dataObj);
                const imageHtml = imgSrc && !dataObj.hideImage ? `<div class="w-6 h-6 rounded shrink-0 overflow-hidden bg-white border border-slate-200"><img src="${imgSrc}" class="w-full h-full object-cover"></div>` : '';

                return `
                <details id="sb-item-${sIdx}-${iIdx}-${subIdxArg}" class="group/item ${bgClass} border border-slate-200 rounded mb-3 shadow-sm ${indentClass}">
                    <summary class="cursor-pointer select-none p-2 font-bold flex justify-between items-center outline-none hover:bg-slate-100 transition-colors rounded group-open/item:rounded-b-none group-open/item:bg-slate-50 group-open/item:border-b border-slate-200">
                        <div class="flex items-center gap-2 truncate pr-2" onclick="App.focusListViewRow(${sIdx}, ${iIdx}, ${subIdxArg})">
                            <i class="fa-solid fa-chevron-right text-[10px] text-[#000000] transition-transform group-open/item:rotate-90"></i>
                            ${imageHtml}
                            <span class="truncate ${titleSize}" title="${App.escapeHtml(dataObj.name || 'Position')}">${prefix} ${App.escapeHtml(dataObj.name || 'Position')}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[10px] text-[#000000]" onclick="App.focusListViewRow(${sIdx}, ${iIdx}, ${subIdxArg})">${totalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="text-[9px] bg-brand-light px-1 rounded text-[#000000]" onclick="App.focusListViewRow(${sIdx}, ${iIdx}, ${subIdxArg})">${percent}</span>
                            
                            <div class="flex gap-1 items-center ml-1 border-l border-slate-200 pl-2">
                                <button type="button" onclick="event.stopPropagation(); App.openPosSettings(${sIdx}, ${iIdx}, ${subIdxArg})" class="text-slate-400 hover:text-[#93c21c] p-1 rounded transition-colors" title="Bearbeiten"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" onclick="event.stopPropagation(); App.removeItem(${sIdx}, ${iIdx}, ${subIdxArg})" class="text-slate-400 hover:text-red-500 p-1 rounded transition-colors" title="Löschen"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </summary>
                    
                    <div class="p-3 bg-white/50 rounded-b">
                        <div class="grid grid-cols-3 gap-2 mb-3 bg-slate-100/50 p-2 rounded text-xs">
                            <div>
                                <div class="text-[9px] text-[#000000] mb-0.5">EK / Einheit</div>
                                <input data-sb-focus="ek:${focusKeyBase}" type="number" step="0.01" value="${ek.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'ek',this.value)" ${isEkReadonly}>
                            </div>
                            <div>
                                <div class="text-[9px] text-[#000000] mb-0.5 flex justify-between items-center">
                                    <span class="flex items-center gap-1">
                                        Marge
                                        ${(hasChildren && !isPauschal) ? `
                                            <button type="button" onclick="event.stopPropagation(); App.toggleParentMarginLock(${sIdx}, ${iIdx})" class="text-[#000000] hover:text-[#93c21c] transition-colors" title="Sperre aufheben, um Marge auf alle Unterpositionen zu verteilen">
                                                <i class="fa-solid ${isParentUnlocked ? 'fa-lock-open text-[#93c21c]' : 'fa-lock'}"></i>
                                            </button>
                                        ` : ''}
                                    </span>
                                    <select data-sb-focus="marginType:${focusKeyBase}" onchange="${typeHandler}" ${isSelectReadonly}>
                                        <option value="percent" ${mType==='percent'?'selected':''}>%</option>
                                        <option value="fixed" ${mType==='fixed'?'selected':''}>€</option>
                                    </select>
                                </div>
                                <input data-sb-focus="margin:${focusKeyBase}" type="number" step="0.01" value="${mVal.toFixed(2)}" onchange="${marginHandler}" ${isVkReadonly}>
                            </div>
                            <div>
                                <div class="text-[9px] text-[#000000] mb-0.5">VK / Einheit</div>
                                <input data-sb-focus="price:${focusKeyBase}" type="number" step="0.01" value="${vk.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subIdxArg},'price',this.value)" ${isVkReadonly}>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-1 text-[10px] text-[#000000] mb-3 border-t border-slate-200 pt-2">
                            <span>EK Gesamt:</span><span class="text-right">${totalEK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span>DB1 Gesamt:</span><span class="text-right text-brand-primary">${marginTotal.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="font-bold text-xs text-[#000000]">VK Gesamt:</span><span class="text-right font-bold text-xs text-[#000000]">${totalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                        </div>
                        
                        <div class="flex items-center gap-2 mb-2 text-xs">
                            <select data-sb-focus="status:${focusKeyBase}" onchange="App.updatePosStatus(${sIdx},${iIdx},${subIdxArg},this.value)" class="flex-1 border border-slate-200 rounded text-xs p-1 outline-none focus:border-brand-primary bg-white">
                                <option value="normal" ${status==='normal'?'selected':''}>Standard Pos.</option>
                                <option value="optional" ${status==='optional'?'selected':''}>Optional</option>
                                <option value="alternative" ${status==='alternative'?'selected':''}>Alternativ</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-4">
                            ${!isSub ? `
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="accent-brand-primary" ${hidePrices?'checked':''} onchange="App.updatePosConfig(${sIdx},${iIdx},${subIdxArg},'hidePrices',this.checked); App.flashListViewRow(${sIdx},${iIdx},${subIdxArg});">
                                <span class="text-[10px]">Preise verbergen</span>
                            </label>
                            ` : ''}
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="accent-brand-primary" ${isPauschal?'checked':''} onchange="App.updatePosConfig(${sIdx},${iIdx},${subIdxArg},'isPauschal',this.checked); App.flashListViewRow(${sIdx},${iIdx},${subIdxArg});">
                                <span class="text-[10px]">Pauschal</span>
                            </label>
                        </div>
                    </div>
                </details>`;
            };

           State.sections.forEach((sec, sIdx) => {
                let secHtml = '';
                let secTotalVK = 0;
                
                // 1. Determine the Multiplier for this Section
                const secQty = sec.config?.qty || 1;
                const isSet = (sec.config?.unit || '').toLowerCase() === 'set';
                const multiplier = isSet ? secQty : 1;
                
                // 2. Build the visual label for the Sidebar Header
                const setLabel = isSet ? `<span class="text-[#93c21c] mr-1">${secQty}x Set:</span> ` : '';

                sec.items.forEach((item, iIdx) => {
                    if(item.active === false) return;
                    
                    const hasSub = item.subItems && item.subItems.length > 0;
                    
                    const itemStatus = item.status || 'normal';
                    if (itemStatus === 'normal') {
                        // Calculate the base gross for one unit of the section
                        const itemTotal = App.calcItemGross(item);
                        secTotalVK += itemTotal;
                    }
                    
                    secHtml += renderCard(false, sIdx, iIdx, null, item, `${sIdx+1}.${iIdx+1}`, hasSub);
                    
                    if (hasSub) {
                        item.subItems.forEach((sub, subIdx) => {
                            if (sub.active === false) return;
                            secHtml += renderCard(true, sIdx, iIdx, subIdx, sub, `↳ ${sIdx+1}.${iIdx+1}.${subIdx+1}`, false);
                        });
                    }
                });

                // 3. APPLY THE MULTIPLIER to the total VK of the section
                secTotalVK *= multiplier;

                // 4. Calculate the percentage of the global total
                const secPercent = (totalNet > 0) ? ((secTotalVK / totalNet) * 100).toFixed(1) + '%' : '0.0%';

                fullHtml += `
                <details id="sb-sec-${sIdx}" class="mb-4 bg-slate-50 border border-slate-200 rounded-xl shadow-sm group" open>
                    <summary class="cursor-pointer select-none p-3 font-bold text-[#000000] text-xs uppercase tracking-wide flex justify-between items-center outline-none bg-slate-100 rounded-t-xl group-open:border-b border-slate-200 transition-colors hover:bg-slate-200/50">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <i class="fa-solid fa-chevron-right transition-transform group-open:rotate-90 text-[#000000]"></i>
                            <span class="truncate">${sIdx+1}. ${setLabel}${App.escapeHtml(sec.title || 'Sektion')}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class=" text-[10px] text-[#000000]">${secTotalVK.toLocaleString('de-DE', {minimumFractionDigits:2})} €</span>
                            <span class="text-[10px] bg-brand-primary text-white px-1.5 py-0.5 rounded" style="background-color: var(--brand-color);">${secPercent}</span>
                        </div>
                    </summary>
                    <div class="p-3 bg-slate-50 border-x border-b border-slate-200 rounded-b-xl overflow-hidden">
                        ${secHtml || '<div class="text-xs text-[#000000] text-center py-4">Keine Positionen vorhanden</div>'}
                    </div>
                </details>
                `;
            });

            // 2. SET HTML ONCE
            c.innerHTML = fullHtml;

            // 3. RESTORE UI STATE (Accordions & Focus)
            App.restoreSidebarUiState(uiState);
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
 
        addSection: (t, l) => { 
            if (App.isLockedSnapshot()) {
                alert("You are in an Angebot snapshot (Status: Auftrag). You cannot change it.");
                return;
            }
            State.sections.push({ id: 's'+Date.now(), title: t||`${State.sections.length+1}. Abschnitt`, description: l?'Dienstleistungen':'Beschreibung', config: { mode: 'standard', pauschalPrice: 0, type: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' }, qty: 1, unit:'' }, items: [], isLaborSection:l }); 
            App.renderQuotePage(); 
            return State.sections.length-1; 
        },
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
                App.Bio.addEntry('Sektion gelöscht', `Sektion "${State.sections[sIdx].title}" wurde entfernt.`);
                }
            });
            },

        dragStartToolText: (ev) => {
            ev.dataTransfer.setData("type", "tool-text");
            App.dragState = { type: 'tool-text' };
            App.startDragMode();
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
            const pageEl = ev.currentTarget;
            
            // 1. Find all potential anchors on this page
            const anchors = Array.from(pageEl.querySelectorAll('[id^="a4-pos-row-"], [id^="a4-sec-head-"], #doc-cover-text'));
            let closest = null;
            let minDist = Infinity;
            
            // 2. Determine which anchor is closest to the drop point
            anchors.forEach(a => {
                const rect = a.getBoundingClientRect();
                const dist = Math.abs(ev.clientY - (rect.top + rect.height/2));
                if (dist < minDist) {
                    minDist = dist;
                    closest = a;
                }
            });

            // 3. Fallback to the page itself if dropped in empty space
            let anchorId = pageEl.id || `page-${pageIndex}`;
            let anchorX = ev.clientX - pageEl.getBoundingClientRect().left;
            let anchorY = ev.clientY - pageEl.getBoundingClientRect().top;

            // Bind to row if dropped within 300px of it
            if (closest && minDist < 300) { 
                anchorId = closest.id;
                const aRect = closest.getBoundingClientRect();
                anchorX = ev.clientX - aRect.left;
                anchorY = ev.clientY - aRect.top;
            }

            const baseObj = {
                id: Date.now(),
                pageIndex,
                x: anchorX, // legacy fallback
                y: anchorY, // legacy fallback
                anchorId,
                anchorX,
                anchorY,
                rotation: 0
            };

            if (type === 'tool-text') {
                State.placedImages.push({
                    ...baseObj,
                    type: 'text', 
                    content: '',
                    width: 250, 
                    height: 100, 
                    fontSize: 14,
                    color: '#4c4c4c'
                });
            } else if (type === 'tool') {
                State.placedImages.push({
                    ...baseObj,
                    type: 'image', 
                    src: ev.dataTransfer.getData("src"),
                    width: 100
                });
            } else {
                return; 
            }

            App.renderQuotePage();
            App.clearDragMode();
        },
        removeToolImage: (id) => { 
            State.placedImages = State.placedImages.filter(i => i.id !== id); 
            App.renderQuotePage(false); 
            
            // If we are currently inside the Print Preview, refresh it too
            if (!document.getElementById('print-preview-modal').classList.contains('hidden')) {
                App.buildPrintPreview();
            }
        },
        updateFloatingText: (id, text) => {
            const item = State.placedImages.find(i => i.id === id);
            if (item) item.content = text;
        },

       // 4. Update style sync so changing text color/size works in Print Preview
        updateFloatingTextStyle: (id, field, value) => {
            const item = State.placedImages.find(i => i.id === id);
            if (item) {
                item[field] = value;
                // Query all instances (both in editor and print preview)
                document.querySelectorAll(`#float-el-${id}`).forEach(el => {
                    const textNode = el.querySelector('.float-text-content');
                    if (textNode) {
                        if (field === 'color') textNode.style.color = value;
                        if (field === 'fontSize') textNode.style.fontSize = value + 'px';
                    }
                });
            }
        },
        renderFloatingImages: (forPrint) => {
                const scope = forPrint 
                    ? document.getElementById('print-preview-content') 
                    : document.getElementById('panel-a4');
                    
                if (!scope) return;

                scope.querySelectorAll('.floating-element').forEach(el => el.remove());

                State.placedImages.forEach(item => {
                    if (!item.anchorId) {
                        item.anchorId = `page-${item.pageIndex}`;
                        item.anchorX = item.x;
                        item.anchorY = item.y;
                    }
                });

                State.placedImages.forEach(item => {
                    let anchor = scope.querySelector(`[id="${item.anchorId}"]`);
                    
                    if (!anchor && item.anchorId.startsWith('a4-')) {
                        anchor = scope.querySelector(`[id="page-${item.pageIndex}"]`);
                    }
                    if (!anchor) anchor = scope.querySelector('[id="page-1"]');
                    if (!anchor) return;

                    anchor.style.position = 'relative';

                    const el = document.createElement('div');
                    el.className = 'floating-element';
                    el.id = `float-el-${item.id}`;
                    el.style.left = item.anchorX + 'px';
                    el.style.top = item.anchorY + 'px';
                    el.style.width = item.width + 'px';
                    el.style.transform = `rotate(${item.rotation}deg)`;
                    
                    if (item.type === 'text') el.style.height = item.height + 'px';

                    // We now include the handles in BOTH views, using `.no-print` so they don't actually print on paper.
                    let handlesHtml = `
                        <div class="delete-float no-print" title="Löschen" onclick="App.removeToolImage(${item.id})">
                            <i class="fa-solid fa-times"></i>
                        </div>
                        <div class="tool-handle resize-handle no-print" title="Größe ändern">
                            <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                        </div>
                        <div class="tool-handle rotate-handle no-print" title="Drehen">
                            <i class="fa-solid fa-rotate-right"></i>
                        </div>
                        <div class="tool-handle move-handle no-print" title="Verschieben">
                            <i class="fa-solid fa-arrows-up-down-left-right"></i>
                        </div>
                    `;

                    let menuHtml = '';
                    if (item.type === 'text') {
                        menuHtml = `
                            <div class="text-settings-float no-print">
                                <input type="color" value="${item.color}" oninput="App.updateFloatingTextStyle(${item.id}, 'color', this.value)" style="width: 24px; height: 24px; padding: 0; border: none; cursor: pointer; border-radius: 4px;" title="Textfarbe">
                                <input type="number" value="${item.fontSize}" oninput="App.updateFloatingTextStyle(${item.id}, 'fontSize', this.value)" style="width: 50px; height: 24px; font-size: 10px; border: 1px solid #cbd5e1; text-align: center; border-radius: 4px; outline: none;" title="Schriftgröße (px)">
                                <span style="font-size: 10px; color: #1c1c1c;">px</span>
                            </div>
                        `;
                    }

                    if (item.type === 'text') {
                        if (forPrint) {
                            el.innerHTML = `
                                ${menuHtml}
                                <div class="float-text-content" style="width: 100%; height: 100%; font-size: ${item.fontSize}px; line-height: 1.3; font-family: 'Inter', sans-serif; font-weight: bold; color: ${item.color}; white-space: pre-wrap; word-wrap: break-word; overflow: hidden; border: 1px dashed transparent;">
                                    ${App.escapeHtml(item.content || '').replace(/\n/g, '<br>')}
                                </div>
                                ${handlesHtml}
                            `;
                        } else {
                            el.innerHTML = `
                                ${menuHtml}
                                <textarea
                                    class="float-text-content"
                                    oninput="App.updateFloatingText(${item.id}, this.value)"
                                    style="width: 100%; height: 100%; resize: none; background: transparent; border: 1px dashed #94a3b8; outline: none; font-size: ${item.fontSize}px; font-weight: bold; color: ${item.color}; font-family: 'Inter', sans-serif; overflow: hidden; padding: 4px;"
                                    placeholder="Text eingeben..."
                                >${item.content || ''}</textarea>
                                ${handlesHtml}
                            `;
                        }
                    } else {
                        el.innerHTML = `
                            <img src="${item.src}" class="w-full h-auto pointer-events-none select-none" draggable="false">
                            ${handlesHtml}
                        `;
                    }

                    anchor.appendChild(el);

                    // Add Event listeners unconditionally for both modes!
                    el.addEventListener('mousedown', (e) => {
                        if (e.target.closest('.text-settings-float')) return; 
                        document.querySelectorAll('.floating-element').forEach(f => {
                            f.classList.remove('is-selected');
                            f.style.zIndex = '50';
                        });
                        el.classList.add('is-selected');
                        el.style.zIndex = '100'; 
                    });

                    const moveTarget = el.querySelector('.move-handle');
                    if (moveTarget) {
                        moveTarget.addEventListener('mousedown', (e) => {
                            if (e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'INPUT') { e.preventDefault(); }
                            
                            e.stopPropagation();
                            let startX = e.clientX;
                            let startY = e.clientY;
                            let startLeft = item.anchorX;
                            let startTop = item.anchorY;

                            const onMove = (mv) => {
                                item.anchorX = startLeft + mv.clientX - startX;
                                item.anchorY = startTop + mv.clientY - startY;
                                el.style.left = item.anchorX + 'px';
                                el.style.top = item.anchorY + 'px';
                            };
                            
                            const onUp = () => { 
                                document.removeEventListener('mousemove', onMove); 
                                document.removeEventListener('mouseup', onUp); 
                            };
                            
                            document.addEventListener('mousemove', onMove); 
                            document.addEventListener('mouseup', onUp);
                        });
                    }

                    const resizeHandle = el.querySelector('.resize-handle');
                    if (resizeHandle) {
                        resizeHandle.addEventListener('mousedown', (e) => {
                            e.preventDefault(); e.stopPropagation();
                            const startWidth = item.width;
                            const startHeight = item.height || 100;
                            const rect = el.getBoundingClientRect();
                            const centerX = rect.left + rect.width / 2;
                            const centerY = rect.top + rect.height / 2;
                            const startDist = Math.hypot(e.clientX - centerX, e.clientY - centerY);

                            const onMove = (mv) => {
                                const scale = Math.hypot(mv.clientX - centerX, mv.clientY - centerY) / startDist;
                                item.width = Math.max(50, startWidth * scale);
                                el.style.width = item.width + 'px';
                                if (item.type === 'text') {
                                    item.height = Math.max(30, startHeight * scale);
                                    el.style.height = item.height + 'px';
                                }
                            };
                            const onUp = () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); };
                            document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp);
                        });
                    }

                    const rotateHandle = el.querySelector('.rotate-handle');
                    if (rotateHandle) {
                        rotateHandle.addEventListener('mousedown', (e) => {
                            e.preventDefault(); e.stopPropagation();
                            const parentRect = anchor.getBoundingClientRect();
                            const actualHeight = item.type === 'text' ? item.height : el.offsetHeight;
                            const centerX = parentRect.left + item.anchorX + (item.width / 2);
                            const centerY = parentRect.top + item.anchorY + (actualHeight / 2);

                            const onMove = (mv) => {
                                const angleRads = Math.atan2(mv.clientY - centerY, mv.clientX - centerX);
                                item.rotation = Math.round(angleRads * (180 / Math.PI) + 90);
                                el.style.transform = `rotate(${item.rotation}deg)`;
                            };
                            const onUp = () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); };
                            document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp);
                        });
                    }
                });
            },
        syncDocData: (field, value) => { if(field === 'offerId') State.offerId = value; if(field === 'custId') State.custId = value; document.querySelectorAll('.sync-offer-id').forEach(el => el.innerText = State.offerId); },
        addManualItem: (sIdx) => {

            if (App.isLockedSnapshot()) {
                alert("You are in an Angebot snapshot (Status: Auftrag). You cannot change it.");
                return;
            }
            const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items.push({
                name:'Neue Position',
                desc:'',
                price:0,
                ek:0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                qty:1,
                unit:'Stk.',
                measure:'Stk.',
                price_unit_value: 1,
                price_unit_label: 'Stk.',
                price_unit_text: '1 Stk.',
                kind: 'article',
                status: 'normal',
                print_hidden: false,
                hideImage: true,
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
                print_hidden: false,
                print_hidden_labor: true,
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
                subIdx: (subIdx === null || subIdx === undefined || subIdx === 'null') ? null : Number(subIdx)
            };

            // Set drag data
            ev.dataTransfer.effectAllowed = 'move';
            ev.dataTransfer.setData("text/plain", JSON.stringify(App.dragState));

            // Optional: Make the actual row transparent while dragging
            setTimeout(() => {
                const row = ev.target.closest('.mat-data-row') || ev.target.closest('.item-group');
                if(row) row.style.opacity = '0.4';
            }, 0);

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
            App.syncParentTotals(sIdx, iIdx);
            App.renderQuotePage();
        },
        updateSectionBenefit: (sIdx, f, v) => { if(f==='value') State.sections[sIdx].benefit.value=parseFloat(v)||0; else State.sections[sIdx].benefit.type=v; App.renderQuotePage(); },
        updateSectionMeta: (sIdx, f, v) => { State.sections[sIdx][f]=v; App.renderCalculationSidebar(); },
        updateSectionConfig: (sIdx, key, val) => { 
            const conf = State.sections[sIdx].config; 
            if(key === 'type') conf.type = val; 
            else if (key === 'mode') conf.mode = val ? 'pauschal' : 'standard'; 
            else if (key === 'hidePrices') conf.hidePrices = val; 
            else if (key === 'pauschalPrice') conf.pauschalPrice = parseFloat(val) || 0; 
            else if (key === 'marginVal') conf.margin.value = parseFloat(val) || 0; 
            else if (key === 'marginType') conf.margin.type = val; 
            else if (key === 'qty') conf.qty = parseFloat(val) || 1; // <--- FIX: Saves Quantity
            else if (key === 'unit') conf.unit = val;                // <--- FIX: Saves Unit 'Set'
            
            App.renderQuotePage(); 
            if (App.Tabs && App.Tabs.current === 'list') App.ListView.render();
        },
        updateTaxRate: (v) => {
            const val = parseFloat(v) || 0;
            State.taxRate = val;
            State.config.vatMode = val; // Sync
            document.getElementById('lbl-tax-rate').innerText = val;
            App.renderQuotePage();
            // Force re-render of settings panel if open to show active button
            if(App.Tabs.current === 'settings') App.Settings.render();
        },
        removeItem: (sIdx, iIdx, subIdx=null) => {
            const item = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            const itemName = item?.name || 'Unbekannt';
            
            if(subIdx!==null) State.sections[sIdx].items[iIdx].subItems.splice(subIdx,1);
            else State.sections[sIdx].items.splice(iIdx,1);
            
            App.Bio.addEntry('Position entfernt', `"${itemName}" wurde gelöscht.`);
            App.renderQuotePage();
        },
 
        addSubItem: (sIdx, iIdx) => {
            const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items[iIdx].subItems.push({
                name:"Position",
                price:0,
                ek:0,
                marginPercent: defaultMargin,
                margin: defaultMargin,
                active:false,
                qty:1,
                unit:'Stk.',
                measure:'Stk.',
                price_unit_value: 1,
                price_unit_label: 'Stk.',
                price_unit_text: '1 Stk.',
                kind: 'article',
                hideImage: true,
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
                if (matBody) matBody.innerHTML = `<tr><td class="px-4 py-3 text-[#000000] text-sm" colspan="2">Lade Komponenten…</td></tr>`;
                if (labBody) labBody.innerHTML = `<tr><td class="px-4 py-3 text-[#000000] text-sm" colspan="3">Lade Dienstleistungen…</td></tr>`;
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
                    .flatMap(x => Array.isArray(x.children) ? x.children : [x]) // FIX: Extract the actual labor rows
                    .map(x => ({
                        name: x?.qualification_name || x?.name || 'Dienstleistung',
                        qualification_name: x?.qualification_name,
                        hours: x?.hours ?? x?.qty ?? 1,
                        hourly_rate: x?.hourly_rate ?? x?.rate ?? x?.qualification_price ?? x?.price ?? 0,
                        rate: x?.rate ?? x?.hourly_rate ?? 0,
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

                    distributor: c?.distributor || null,
                    distributor_name:
                        c?.distributor?.name ||
                        c?.distributor_name ||
                        c?.supplier ||
                        '',

                    skonto:
                        c?.skonto ??
                        c?.supplier_discount ??
                        c?.discount ??
                        c?.distributor?.skonto ??
                        0,

                    payment_terms:
                        c?.payment_terms ??
                        c?.payment_days ??
                        c?.zahlungsziel ??
                        c?.distributor?.payment_terms ??
                        0,

                    children: Array.isArray(c?.children) ? c.children.map(ch => ({
                        name: ch?.name ?? 'Unterkomponente',
                        qty: ch?.qty ?? 1,
                        unit: ch?.unit ?? 'Stk',
                        unit_price: ch?.unit_price ?? ch?.unitPrice ?? ch?.price ?? 0,
                        total: ch?.total ?? null,

                        distributor: ch?.distributor || null,
                        distributor_name:
                            ch?.distributor?.name ||
                            ch?.distributor_name ||
                            ch?.supplier ||
                            '',

                        skonto:
                            ch?.skonto ??
                            ch?.supplier_discount ??
                            ch?.discount ??
                            ch?.distributor?.skonto ??
                            0,

                        payment_terms:
                            ch?.payment_terms ??
                            ch?.payment_days ??
                            ch?.zahlungsziel ??
                            ch?.distributor?.payment_terms ??
                            0,
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
                    matBody.innerHTML = `<tr><td class="px-4 py-3 text-[#000000] text-sm" colspan="3">Keine Komponenten</td></tr>`;
                    return;
                }

                const rows = [];

                comps.forEach(c => {
                    const qty = num(c?.qty, 1);
                    const up  = num(c?.unit_price, 0);
                    const tot = num(c?.total, up * qty);

                    const distributor = escapeHtml(
                        c?.distributor?.name ||
                        c?.distributor_name ||
                        c?.supplier ||
                        '—'
                    );

                    const skonto = num(
                        c?.skonto ??
                        c?.supplier_discount ??
                        c?.discount ??
                        0
                    );

                   const paymentTermsRaw =
                        c?.payment_terms ??
                        c?.payment_days ??
                        c?.zahlungsziel ??
                        null;

                    const paymentTerms =
                        paymentTermsRaw !== null && paymentTermsRaw !== undefined && paymentTermsRaw !== ''
                            ? Number(paymentTermsRaw)
                            : null;

                    rows.push(`
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-bold text-[#000000] text-sm">${escapeHtml(c?.name || 'Komponente')}</div>
                                <div class="text-xs text-[#000000]">${escapeHtml(c?.unit || 'Stk')} • ${escapeHtml(qty)}</div>
                            </td>

                            <td class="px-4 py-3 text-sm text-[#000000]">
                                <div class="font-semibold">${distributor}</div>
                                <div class="text-[11px] text-[#000000] mt-1">
                                    Skonto: ${skonto.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%
                                    • Zahlungsziel: ${paymentTerms} Tage
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right  text-sm">${money(tot)} €</td>
                        </tr>
                    `);

                    const children = Array.isArray(c?.children) ? c.children : [];
                    children.forEach(ch => {
                        const q2 = num(ch?.qty, 1);
                        const p2 = num(ch?.unit_price, 0);
                        const t2 = num(ch?.total, p2 * q2);

                        const childDistributor = escapeHtml(
                            ch?.distributor?.name ||
                            ch?.distributor_name ||
                            ch?.supplier ||
                            '—'
                        );

                        const childSkonto = num(
                            ch?.skonto ??
                            ch?.supplier_discount ??
                            ch?.discount ??
                            0
                        );

                      const childPaymentTermsRaw =
                            ch?.payment_terms ??
                            ch?.payment_days ??
                            ch?.zahlungsziel ??
                            null;

                        const childPaymentTerms =
                            childPaymentTermsRaw !== null && childPaymentTermsRaw !== undefined && childPaymentTermsRaw !== ''
                                ? Number(childPaymentTermsRaw)
                                : null;

                        rows.push(`
                            <tr class="bg-slate-50/50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-[#000000] text-sm">↳ ${escapeHtml(ch?.name || 'Unterkomponente')}</div>
                                    <div class="text-xs text-[#000000]">${escapeHtml(ch?.unit || 'Stk')} • ${escapeHtml(q2)}</div>
                                </td>

                                <td class="px-4 py-3 text-sm text-[#000000]">
                                    <div class="font-medium">${childDistributor}</div>
                                    <div class="text-[11px] text-[#000000] mt-1">
                                        ${childSkonto > 0 ? `Skonto: ${childSkonto.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%` : ''}
                                        ${childSkonto > 0 && childPaymentTerms !== null ? ' • ' : ''}
                                        ${childPaymentTerms !== null ? `Zahlungsziel: ${childPaymentTerms} Tage` : ''}
                                        ${childSkonto <= 0 && childPaymentTerms === null ? '—' : ''}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right  text-sm">${money(t2)} €</td>
                            </tr>
                        `);
                    });
                });

                matBody.innerHTML = rows.join('');
            };

            const renderLabor = (labor) => {
                if (!labBody) return;

                if (!Array.isArray(labor) || labor.length === 0) {
                    labBody.innerHTML = `<tr><td class="px-4 py-3 text-[#000000] text-sm" colspan="4">Keine Dienstleistungen</td></tr>`;
                    return;
                }

                labBody.innerHTML = labor.map(l => {
                    const hrs  = num(l?.hours, 1);
                    const rate = num(l?.rate ?? l?.hourly_rate, 0);
                    const tot  = num(l?.total, hrs * rate);

                    return `
                        <tr>
                            <td class="px-4 py-3 font-bold text-[#000000] text-sm">${escapeHtml(l?.name || 'Dienstleistung')}</td>
                            <td class="px-4 py-3 text-sm text-[#000000]">${escapeHtml(l?.qualification_name || '-')}</td>
                            <td class="px-4 py-3 text-center  text-sm">${hrs}</td>
                            <td class="px-4 py-3 text-right  text-sm">${money(tot)} €</td>
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
        handleImageClick: (e, sIdx, iIdx, subIdx = null) => { 
            // Prevent opening the upload dialog if the user was just dragging the resize handle
            const rect = e.currentTarget.getBoundingClientRect();
            const isResize = (e.clientX > rect.right - 18) && (e.clientY > rect.bottom - 18);
            if (isResize) return;

            App.editingImage = { sIdx, iIdx, subIdx }; 
            document.getElementById('img-upload-input').click(); 
        },
        saveImageSize: (el, sIdx, iIdx, subIdx = null) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            if (!target) return;
            
            const w = el.offsetWidth;
            const h = el.offsetHeight;
            
            // Only update and mark as unsaved if dimensions actually changed
            if (target.imgWidth !== w || target.imgHeight !== h) {
                target.imgWidth = w;
                target.imgHeight = h;
                State.hasUnsavedChanges = true;
            }
        },
        handleBadgeClick: (sIdx, iIdx) => { State.editingBadge = { sIdx, iIdx, pos: 'tl', type: '', text: '' }; document.getElementById('badge-modal').classList.remove('hidden'); },
        closeBadgeModal: () => document.getElementById('badge-modal').classList.add('hidden'),
        setBadgePos: (pos) => { if(State.editingBadge) State.editingBadge.pos = pos; },
        saveBadgeConfig: () => { if(!State.editingBadge) return; const { sIdx, iIdx, pos, tempImg } = State.editingBadge; const val = document.getElementById('badge-type-select').value; let badgeObj = null; if(val === 'image' && tempImg) badgeObj = { type: 'image', src: tempImg, pos: pos }; else if (val !== '' && val !== 'image') badgeObj = { type: 'text', text: val, pos: pos }; else if (val === 'image' && !tempImg) { document.getElementById('badge-upload-input').click(); return; } State.sections[sIdx].items[iIdx].badge = badgeObj; App.renderQuotePage(); App.closeBadgeModal(); }
        
    };
    

    App.syncParentTotals = function(sIdx, iIdx) {
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (!mainItem || mainItem.isPauschal || !Array.isArray(mainItem.subItems) || mainItem.subItems.length === 0) return;

        let newVk = 0;
        let newEk = 0;
        mainItem.subItems.forEach(sub => {
            if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                newVk += App.calcItemGross(sub);
                newEk += App.calcItemCost(sub);
            }
        });
        
        mainItem.price = newVk;
        mainItem.ek = newEk;
        mainItem.purchase_price = newEk;
        
        // FIX: Prevent division by zero
        if (newEk > 0) {
            mainItem.marginPercent = ((newVk - newEk) / newEk) * 100;
        } else {
            // If EK is 0 but VK is > 0, margin is technically 100%
            mainItem.marginPercent = newVk > 0 ? 100 : 0; 
        }
        mainItem.margin = mainItem.marginPercent;
    };

   App.getWizardPrefillFromUrl = function () {
        const params = new URLSearchParams(window.location.search);

        return {
            offer_id: params.get('offer_id') ? parseInt(params.get('offer_id'), 10) : null,
            offer_folder_id: params.get('offer_folder_id') ? parseInt(params.get('offer_folder_id'), 10) : null,
            customer_id: params.get('customer_id') ? parseInt(params.get('customer_id'), 10) : null,
            alternative_id: params.get('alternative_id') ? parseInt(params.get('alternative_id'), 10) : null,
            product_id: params.get('product_id') ? parseInt(params.get('product_id'), 10) : null,
            
            // 👇 ADD THIS LINE
            load_snapshot: params.get('load_snapshot') === '1' 
        };
    };

    App.applyWizardPrefillFromUrl = async function () {
        const prefill = App.getWizardPrefillFromUrl();
        State.prefill = {
            ...State.prefill,
            ...prefill
        };

        // Keep save/update working even when the Start-Tab objects are not hydrated yet.
        if (typeof App.syncSaveContextIntoState === 'function') {
            App.syncSaveContextIntoState();
        }

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

            // Do not block save/update only because the async Start-Tab hydration failed.
            if (typeof App.syncSaveContextIntoState === 'function') {
                App.syncSaveContextIntoState();
            }
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
            const templates = document.getElementById('panel-templates');

            const btnA4 = document.getElementById('main-tab-a4');
            const btnList = document.getElementById('main-tab-list');
            const btnSettings = document.getElementById('main-tab-settings');
            const btnBio = document.getElementById('main-tab-bio');
            const btnTemplates = document.getElementById('main-tab-templates');

            if (a4) a4.classList.toggle('hidden', mode !== 'a4');
            if (list) list.classList.toggle('hidden', mode !== 'list');
            if (settings) settings.classList.toggle('hidden', mode !== 'settings');
            if (bio) bio.classList.toggle('hidden', mode !== 'bio');
            if (templates) templates.classList.toggle('hidden', mode !== 'templates');

            const setActive = (btn, isActive) => {
                if (!btn) return;
                if (isActive) {
                    btn.classList.add('bg-white', 'shadow', 'text-[#93c21c]');
                    btn.classList.remove('text-[#000000]');
                } else {
                    btn.classList.remove('bg-white', 'shadow', 'text-[#93c21c]');
                    btn.classList.add('text-[#000000]');
                }
            };

            setActive(btnA4, mode === 'a4');
            setActive(btnList, mode === 'list');
            setActive(btnSettings, mode === 'settings');
            setActive(btnBio, mode === 'bio');
            setActive(btnTemplates, mode === 'templates');

            if (mode === 'list') App.ListView.render();
            if (mode === 'settings') App.Settings.render();
            if (mode === 'bio') App.Bio.render();
            if (mode === 'templates') App.TemplateTab.render();
        }
    };


    App.formatDescHtmlForPdf = function (html, fallbackText = '') {
        const source = (html || '').toString().trim();

        if (!source) {
            return fallbackText
                ? `<div class="pdf-blue-title">${App.escapeHtml(fallbackText)}</div>`
                : '';
        }

        const box = document.createElement('div');
        box.innerHTML = source;

        const first = box.firstElementChild;

        if (first) {
            const tag = (first.tagName || '').toLowerCase();

            // first paragraph / heading becomes blue heading
            if (['p', 'div', 'h1', 'h2', 'h3', 'h4', 'strong'].includes(tag)) {
                first.classList.add('pdf-blue-title');
            }
        } else {
            box.innerHTML = `<div class="pdf-blue-title">${App.escapeHtml(source)}</div>`;
        }

        return box.innerHTML;
    };

    App.TemplateTab = {
        viewMode: 'card',

        filters: {
            q: '',
            department_id: '',
            article_group_id: '',
            brand_id: '',
            distributor_id: '',
            favorite_only: '',
            stamped_only: '',
            usage_filter: '',
            employee_id: ''
        },

        async render() {
            const root = document.getElementById('panel-templates');
            if (!root) return;

            root.innerHTML = `
                <div class="max-w-[1680px] mx-auto space-y-6 p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
                        <div>
                            <h2 class="text-xl font-black text-[#000000]">Vorlagen-Bibliothek</h2>
                            <p class="text-xs text-[#000000]">Wählen Sie ein Template aus, um dessen Positionen dem aktuellen Angebot hinzuzufügen.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                onclick="App.TemplateTab.resetFilters()"
                                class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-[#000000] hover:bg-slate-50 text-xs font-bold transition-all"
                            >
                                <i class="fa-solid fa-rotate-left mr-2"></i>Filter zurücksetzen
                            </button>

                            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                                <button
                                    type="button"
                                    onclick="App.TemplateTab.switchView('card')"
                                    id="btn-tpl-view-card"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2"
                                >
                                    <i class="fa-solid fa-grip-large"></i> Karten
                                </button>
                                <button
                                    type="button"
                                    onclick="App.TemplateTab.switchView('list')"
                                    id="btn-tpl-view-list"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2"
                                >
                                    <i class="fa-solid fa-list"></i> Liste
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.25em] font-black text-[#000000]">Filter</div>
                                <p class="text-xs text-[#000000] mt-1">Mitarbeiter, Favoriten, Stemm und Verwendung gezielt filtern.</p>
                            </div>

                            <button
                                type="button"
                                onclick="App.TemplateTab.clearFilterFields()"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold text-[#000000] transition-all hover:bg-slate-100"
                            >
                                <i class="fa-solid fa-eraser"></i>
                                Filter leeren
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-10 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Suche (Name/Text)</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="tpl-filter-q"
                                        placeholder="Suchen..."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 pl-9 text-sm focus:border-[#93c21c] outline-none transition-all"
                                    >
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#000000] text-xs"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Mitarbeiter</label>
                                <select id="tpl-filter-employee" class="tpl-select2-filter"></select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Abteilung</label>
                                <select id="tpl-filter-dept" class="tpl-select2-filter"></select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Gewerk</label>
                                <select id="tpl-filter-group" class="tpl-select2-filter"></select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Marke</label>
                                <select id="tpl-filter-brand" class="tpl-select2-filter"></select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Lieferant</label>
                                <select id="tpl-filter-dist" class="tpl-select2-filter"></select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Favoriten</label>
                                <select id="tpl-filter-favorite" class="tpl-select2-filter">
                                    <option value=""></option>
                                    <option value="1">Nur Favoriten</option>
                                    <option value="0">Ohne Favoriten</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Stamm</label>
                                <select id="tpl-filter-stamped" class="tpl-select2-filter">
                                    <option value=""></option>
                                    <option value="1">Nur Stamm</option>
                                    <option value="0">Ohne Stamm</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-[#000000] mb-1 ml-1">Verwendung</label>
                                <select id="tpl-filter-usage" class="tpl-select2-filter">
                                    <option value=""></option>
                                    <option value="most_used">Meist verwendet</option>
                                    <option value="least_used">Am wenigsten verwendet</option>
                                    <option value="unused">Nie verwendet</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="template-results-container" class="min-h-[400px]">
                        <div class="flex items-center justify-center py-20 text-[#000000]">
                            <i class="fa-solid fa-circle-notch fa-spin mr-3"></i> Lade Vorlagen...
                        </div>
                    </div>
                </div>
            `;

            this.ensureUsageModal();
            await this.initFilters();
            this.updateViewButtons();
            this.refresh();
        },

        ensureUsageModal() {
            if (document.getElementById('template-usage-modal')) return;

            const modal = document.createElement('div');
            modal.id = 'template-usage-modal';
            modal.className = 'fixed inset-0 z-[1200] hidden';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" onclick="App.TemplateTab.closeUsageModal()"></div>

                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex items-start justify-between gap-4">
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.25em] font-black text-[#000000]">Nutzungsübersicht</div>
                                <h3 id="template-usage-modal-title" class="text-xl font-black text-[#000000] mt-1">Verwendungsdaten</h3>
                                <p id="template-usage-modal-subtitle" class="text-sm text-[#000000] mt-1">Lade Daten...</p>
                            </div>
                            <button
                                type="button"
                                onclick="App.TemplateTab.closeUsageModal()"
                                class="w-10 h-10 rounded-xl border border-slate-200 text-[#000000] hover:text-[#000000] hover:bg-slate-50 transition-all"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div id="template-usage-modal-body" class="p-6 max-h-[70vh] overflow-y-auto">
                            <div class="flex items-center justify-center py-16 text-[#000000]">
                                <i class="fa-solid fa-circle-notch fa-spin mr-3"></i> Lade Verwendungsdaten...
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        },

        async initFilters() {
            try {
                const res = await fetch('/offers/templates/options', {
                    headers: { Accept: 'application/json' }
                });

                const data = await res.json();
                if (!data.success) return;

                const buildOptions = (items, valKey, labelBuilder) => {
                    return '<option value=""></option>' + (items || []).map(item => {
                        const label = typeof labelBuilder === 'function'
                            ? labelBuilder(item)
                            : (item[labelBuilder] || '');
                        return `<option value="${item[valKey]}">${App.escapeHtml(label)}</option>`;
                    }).join('');
                };

                $('#tpl-filter-dept').html(buildOptions(data.departments || [], 'id', 'department_name'));
                $('#tpl-filter-brand').html(buildOptions(data.brands || [], 'id', 'name'));
                $('#tpl-filter-dist').html(buildOptions(data.distributors || [], 'id', 'name'));
                $('#tpl-filter-group').html(buildOptions(data.article_groups || [], 'id', 'article_group'));

                const employees = Array.isArray(data.employees) ? data.employees : [];
                $('#tpl-filter-employee').html(
                    buildOptions(employees, 'id', (i) => {
                        const fullName = (i.full_name || `${i.name || ''} ${i.lastname || ''}`.trim()).trim();
                        return fullName || `Mitarbeiter #${i.id}`;
                    })
                );

                $('.tpl-select2-filter').each(function () {
                    const $el = $(this);

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    let placeholder = 'Alle anzeigen';
                    switch (this.id) {
                        case 'tpl-filter-employee':
                            placeholder = 'Mitarbeiter wählen';
                            break;
                        case 'tpl-filter-dept':
                            placeholder = 'Abteilung wählen';
                            break;
                        case 'tpl-filter-group':
                            placeholder = 'Artikelgruppe wählen';
                            break;
                        case 'tpl-filter-brand':
                            placeholder = 'Marke wählen';
                            break;
                        case 'tpl-filter-dist':
                            placeholder = 'Lieferant wählen';
                            break;
                        case 'tpl-filter-favorite':
                            placeholder = 'Favoriten wählen';
                            break;
                        case 'tpl-filter-stamped':
                            placeholder = 'Stamm wählen';
                            break;
                        case 'tpl-filter-usage':
                            placeholder = 'Verwendung wählen';
                            break;
                    }

                    $el.select2({
                        width: '100%',
                        placeholder,
                        allowClear: true
                    });
                });

                $('#tpl-filter-q')
                    .off('input')
                    .on('input', (e) => {
                        this.filters.q = e.target.value;
                        this.debouncedRefresh();
                    });

                $('.tpl-select2-filter')
                    .off('change')
                    .on('change', (e) => {
                        const id = e.target.id;
                        const val = e.target.value || '';

                        if (id === 'tpl-filter-employee') this.filters.employee_id = val;
                        if (id === 'tpl-filter-dept') this.filters.department_id = val;
                        if (id === 'tpl-filter-group') this.filters.article_group_id = val;
                        if (id === 'tpl-filter-brand') this.filters.brand_id = val;
                        if (id === 'tpl-filter-dist') this.filters.distributor_id = val;
                        if (id === 'tpl-filter-favorite') this.filters.favorite_only = val;
                        if (id === 'tpl-filter-stamped') this.filters.stamped_only = val;
                        if (id === 'tpl-filter-usage') this.filters.usage_filter = val;

                        this.refresh();
                    });
            } catch (e) {
                console.error('Filter-Init fehlgeschlagen', e);
            }
        },

        switchView(mode) {
            this.viewMode = mode;
            this.updateViewButtons();
            this.renderResults();
        },

        updateViewButtons() {
            const cardBtn = document.getElementById('btn-tpl-view-card');
            const listBtn = document.getElementById('btn-tpl-view-list');
            if (!cardBtn || !listBtn) return;

            if (this.viewMode === 'card') {
                cardBtn.className = 'px-4 py-2 rounded-lg text-xs font-bold bg-white shadow text-[#93c21c]';
                listBtn.className = 'px-4 py-2 rounded-lg text-xs font-bold text-[#000000] hover:text-[#000000]';
            } else {
                listBtn.className = 'px-4 py-2 rounded-lg text-xs font-bold bg-white shadow text-[#93c21c]';
                cardBtn.className = 'px-4 py-2 rounded-lg text-xs font-bold text-[#000000] hover:text-[#000000]';
            }
        },

        _refreshTimer: null,
        debouncedRefresh() {
            clearTimeout(this._refreshTimer);
            this._refreshTimer = setTimeout(() => this.refresh(), 400);
        },

        clearFilterFields() {
            this.filters.q = '';
            this.filters.department_id = '';
            this.filters.article_group_id = '';
            this.filters.brand_id = '';
            this.filters.distributor_id = '';
            this.filters.favorite_only = '';
            this.filters.stamped_only = '';
            this.filters.usage_filter = '';
            this.filters.employee_id = '';

            $('#tpl-filter-q').val('');

            [
                '#tpl-filter-employee',
                '#tpl-filter-dept',
                '#tpl-filter-group',
                '#tpl-filter-brand',
                '#tpl-filter-dist',
                '#tpl-filter-favorite',
                '#tpl-filter-stamped',
                '#tpl-filter-usage'
            ].forEach(selector => {
                $(selector).val(null).trigger('change');
            });

            this.refresh();
        },

        resetFilters() {
            this.clearFilterFields();
        },

        async refresh() {
            const container = document.getElementById('template-results-container');
            if (!container) return;

            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    params.append(key, value);
                }
            });

            try {
                const res = await fetch(`/offers/templates?${params.toString()}`, {
                    headers: { Accept: 'application/json' }
                });
                const data = await res.json();

                State.templateItems = data.items || [];
                this.renderResults();
            } catch (e) {
                console.error(e);
                container.innerHTML = '<div class="text-red-500 py-10 text-center">Fehler beim Laden der Vorlagen.</div>';
            }
        },

        renderResults() {
            const container = document.getElementById('template-results-container');
            if (!container) return;

            if (!State.templateItems || !State.templateItems.length) {
                container.innerHTML = `
                    <div class="py-20 text-center text-[#000000] bg-white rounded-2xl border border-dashed border-slate-200">
                        Keine Vorlagen gefunden.
                    </div>
                `;
                return;
            }

            if (this.viewMode === 'card') {
                this.renderCardView(container);
            } else {
                this.renderListView(container);
            }
        },

        getInitials(fullName) {
            const raw = String(fullName || '').trim();
            if (!raw) return '??';

            const parts = raw.split(/\s+/).filter(Boolean);
            const first = parts[0]?.charAt(0) || '';
            const last = parts.length > 1 ? parts[parts.length - 1]?.charAt(0) || '' : '';

            return `${first}${last}`.toUpperCase();
        },

        getEmployeeAvatar(fullName, mode = 'default') {
            if (!fullName) return '';

            const classes = {
                favorite: 'bg-yellow-100 text-yellow-700 border-yellow-200',
                stamp: 'bg-indigo-100 text-indigo-700 border-indigo-200',
                usage: 'bg-emerald-100 text-emerald-700 border-emerald-200',
                default: 'bg-slate-100 text-[#000000] border-slate-200'
            };

            const style = classes[mode] || classes.default;

            return `
                <span
                    title="${App.escapeHtml(fullName)}"
                    class="inline-flex items-center justify-center w-5 h-5 rounded-full border text-[9px] font-black ${style}"
                >
                    ${App.escapeHtml(this.getInitials(fullName))}
                </span>
            `;
        },

        isFavoriteLockedForCurrentUser(t) {
            return !!(t.is_favorite && t.favorite_by_employee_id && State.currentEmployeeId && Number(t.favorite_by_employee_id) !== Number(State.currentEmployeeId));
        },

        isStampLockedForCurrentUser(t) {
            return !!(t.has_stamp && t.stamped_by_employee_id && State.currentEmployeeId && Number(t.stamped_by_employee_id) !== Number(State.currentEmployeeId));
        },

        getFavoriteButtonHtml(t) {
            const locked = this.isFavoriteLockedForCurrentUser(t);

            return `
                <button
                    type="button"
                    onclick="${locked ? '' : `App.TemplateTab.toggleFavorite(${t.id})`}"
                    ${locked ? 'disabled' : ''}
                    class="p-2 rounded-xl border transition-all ${locked
                        ? 'border-slate-200 bg-slate-100 text-slate-300 cursor-not-allowed'
                        : t.is_favorite
                            ? 'border-yellow-200 bg-yellow-50 text-yellow-500 hover:bg-yellow-100'
                            : 'border-slate-200 bg-white text-[#000000] hover:text-yellow-500 hover:border-yellow-200'}"
                    title="${locked
                        ? `Favorit ist gesperrt durch ${App.escapeHtml(t.favorite_by_employee_name || 'anderen Mitarbeiter')}`
                        : t.is_favorite ? 'Favorit entfernen' : 'Als Favorit markieren'}"
                >
                    <i class="${t.is_favorite ? 'fa-solid' : 'fa-regular'} fa-star"></i>
                </button>
            `;
        },

        getStampButtonHtml(t) {
            const locked = this.isStampLockedForCurrentUser(t);

            return `
                <button
                    type="button"
                    onclick="${locked ? '' : `App.TemplateTab.toggleStamp(${t.id})`}"
                    ${locked ? 'disabled' : ''}
                    class="p-2 rounded-xl border transition-all ${locked
                        ? 'border-slate-200 bg-slate-100 text-slate-300 cursor-not-allowed'
                        : t.has_stamp
                            ? 'border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100'
                            : 'border-slate-200 bg-white text-[#000000] hover:text-indigo-600 hover:border-indigo-200'}"
                    title="${locked
                        ? `Stamm ist gesperrt durch ${App.escapeHtml(t.stamped_by_employee_name || 'anderen Mitarbeiter')}`
                        : t.has_stamp ? 'Stamm entfernen' : 'Stamm setzen'}"
                >
                    <i class="fa-solid fa-database"></i>
                </button>
            `;
        },

        getUsageButtonHtml(t) {
            return `
                <button
                    type="button"
                    onclick="App.TemplateTab.openUsageModal(${t.id})"
                    class="p-2 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all"
                    title="Verwendungsdaten anzeigen"
                >
                    <i class="fa-solid fa-chart-line"></i>
                </button>
            `;
        },

        getFavoriteBadgeHtml(t) {
            if (!t.is_favorite) return '';

            return `
                <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase bg-yellow-50 text-yellow-700 border border-yellow-200 px-2 py-1 rounded-full">
                    <i class="fa-solid fa-star"></i>
                    Favorit
                    ${this.getEmployeeAvatar(t.favorite_by_employee_name, 'favorite')}
                </span>
            `;
        },

        getStampBadgeHtml(t) {
            if (!t.has_stamp) return '';

            return `
                <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded-full">
                    <i class="fa-solid fa-database"></i>
                    ${App.escapeHtml('Stamm')}
                    ${this.getEmployeeAvatar(t.stamped_by_employee_name, 'stamp')}
                </span>
            `;
        },

        getUsageInfoHtml(t) {
            return `
                <div class="flex items-center flex-wrap gap-4 mt-5 text-[10px] font-bold text-[#000000] border-t pt-4 border-slate-50">
                    <div class="flex items-center gap-1"><i class="fa-solid fa-layer-group"></i> ${t.section_count} Sektionen</div>
                    <div class="flex items-center gap-1"><i class="fa-solid fa-box"></i> ${t.item_count} Items</div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-rotate"></i> ${t.usage_count || 0}x verwendet
                        ${t.last_used_by_employee_name ? this.getEmployeeAvatar(t.last_used_by_employee_name, 'usage') : ''}
                    </div>
                </div>
            `;
        },

        renderCardView(container) {
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    ${State.templateItems.map(t => `
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 hover:border-[#93c21c] transition-all hover:shadow-md group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[#93c21c] group-hover:bg-[#f7fee7]">
                                    <i class="fa-solid fa-file-invoice text-xl"></i>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${this.getFavoriteButtonHtml(t)}
                                    ${this.getStampButtonHtml(t)}
                                    ${this.getUsageButtonHtml(t)}
                                    <span class="text-[10px] font-black uppercase bg-slate-100 px-2 py-1 rounded text-[#000000] tracking-wider">
                                        #${t.id}
                                    </span>
                                    <button
                                        type="button"
                                        onclick="App.TemplateTab.deleteTemplate(${t.id})"
                                        class="text-slate-300 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50"
                                        title="Vorlage löschen"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                ${this.getFavoriteBadgeHtml(t)}
                                ${this.getStampBadgeHtml(t)}
                            </div>

                            <h4 class="font-black text-[#000000] text-base truncate">${App.escapeHtml(t.name)}</h4>
                            <p class="text-xs text-[#000000] mt-2 line-clamp-2 min-h-[32px]" style="display:none">${App.escapeHtml(t.preview_text || 'Keine Beschreibung')}</p>

                            <div class="mt-3 grid grid-cols-2 gap-2 text-[10px] text-[#000000] bg-slate-50 p-2 rounded-lg border border-slate-100">
                                <div class="truncate"><b class="text-[#000000]">Abt:</b> ${App.escapeHtml(t.department_name || '-')}</div>
                                <div class="truncate"><b class="text-[#000000]">Grp:</b> ${App.escapeHtml(t.article_group_name || '-')}</div>
                                <div class="truncate"><b class="text-[#000000]">Marke:</b> ${App.escapeHtml(t.brand_name || '-')}</div>
                                <div class="truncate"><b class="text-[#000000]">Lief:</b> ${App.escapeHtml(t.distributor_name || '-')}</div>
                            </div>

                            ${this.getUsageInfoHtml(t)}

                            <div class="grid grid-cols-2 gap-2 mt-5">
                                <button
                                    type="button"
                                    onclick="App.TemplateTab.previewTemplate(${t.id})"
                                    class="text-xs font-bold py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-[#000000] transition-all"
                                >
                                    Details
                                </button>
                                <button
                                    type="button"
                                    id="btn-use-tpl-${t.id}"
                                    onclick="App.TemplateTab.useTemplate(${t.id})"
                                    class="text-xs font-black py-2.5 rounded-xl bg-[#93c21c] text-white hover:brightness-105 shadow-sm shadow-[#93c21c]/20 transition-all flex items-center justify-center gap-2"
                                >
                                    <i class="fa-solid fa-download"></i> Einfügen
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        },

        renderListView(container) {
            container.innerHTML = `
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider">Name & Beschreibung</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider">Klassifizierung</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider">Umfang</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider">Erstellt von</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-[#000000] tracking-wider text-right">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            ${State.templateItems.map(t => `
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-[#000000] text-sm flex items-center gap-2">
                                            ${App.escapeHtml(t.name)}
                                            ${t.is_favorite ? '<i class="fa-solid fa-star text-yellow-500" title="Favorit"></i>' : ''}
                                            ${t.has_stamp ? '<i class="fa-solid fa-stamp text-indigo-600" title="Stamm"></i>' : ''}
                                        </div>
                                        <div class="text-xs text-[#000000] truncate max-w-xs">${App.escapeHtml(t.preview_text || '')}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-[#000000] truncate w-48" title="Abteilung & Gruppe">
                                            ${App.escapeHtml(t.department_name || '-')}
                                            <span class="text-[#000000] font-normal">| ${App.escapeHtml(t.article_group_name || '-')}</span>
                                        </div>
                                        <div class="text-[10px] text-[#000000] truncate w-48 mt-1" title="Marke & Lieferant">
                                            <i class="fa-solid fa-tag mr-1"></i>${App.escapeHtml(t.brand_name || '-')}
                                            <span class="mx-1">•</span>
                                            <i class="fa-solid fa-truck mr-1"></i>${App.escapeHtml(t.distributor_name || '-')}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="text-xs font-bold text-[#000000]">${t.section_count} Abschnitte</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="text-xs font-bold text-[#000000]">${t.item_count} Positionen</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="text-xs font-bold text-[#000000]">${t.usage_count || 0}x verwendet</span>
                                            ${t.last_used_by_employee_name ? this.getEmployeeAvatar(t.last_used_by_employee_name, 'usage') : ''}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            ${this.getFavoriteBadgeHtml(t)}
                                            ${this.getStampBadgeHtml(t)}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-[#000000]">${App.escapeHtml(t.creator_name)}</div>
                                        <div class="text-[10px] text-[#000000]">${t.updated_at}</div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            ${this.getUsageButtonHtml(t)}

                                            <button
                                                type="button"
                                                onclick="App.TemplateTab.toggleFavorite(${t.id})"
                                                class="p-2 rounded-lg ${t.is_favorite ? 'text-yellow-500 bg-yellow-50' : 'text-[#000000] hover:text-yellow-500'}"
                                                title="Favorit"
                                            >
                                                <i class="${t.is_favorite ? 'fa-solid' : 'fa-regular'} fa-star"></i>
                                            </button>

                                            <button
                                                type="button"
                                                onclick="App.TemplateTab.toggleStamp(${t.id})"
                                                class="p-2 rounded-lg ${t.has_stamp ? 'text-indigo-600 bg-indigo-50' : 'text-[#000000] hover:text-indigo-600'}"
                                                title="Stamm"
                                            >
                                                <i class="fa-solid fa-stamp"></i>
                                            </button>

                                            <button
                                                type="button"
                                                onclick="App.TemplateTab.previewTemplate(${t.id})"
                                                class="p-2 text-[#000000] hover:text-[#000000] transition-colors"
                                                title="Details"
                                            >
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <button
                                                type="button"
                                                onclick="App.TemplateTab.deleteTemplate(${t.id})"
                                                class="p-2 text-slate-300 hover:text-red-500 transition-colors"
                                                title="Löschen"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                            <button
                                                type="button"
                                                id="btn-use-tpl-list-${t.id}"
                                                onclick="App.TemplateTab.useTemplate(${t.id})"
                                                class="bg-[#93c21c] text-white text-[10px] font-black px-3 py-1.5 rounded-lg hover:brightness-105 transition-all flex items-center gap-2"
                                            >
                                                <i class="fa-solid fa-download"></i> EINFÜGEN
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        },

        async toggleFavorite(id) {
            try {
                const response = await fetch(`/offers/templates/${id}/favorite`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    alert(result.message || 'Favorit konnte nicht geändert werden.');
                    return;
                }

                const item = State.templateItems.find(x => x.id === id);
                if (item) {
                    item.is_favorite = !!result.is_favorite;
                    item.favorite_by_employee_name = result.favorite_by_employee_name || null;
                    item.favorite_at = result.favorite_at || null;
                }

                this.renderResults();
            } catch (error) {
                console.error('toggleFavorite error:', error);
                alert('Netzwerk-Fehler beim Ändern des Favoriten.');
            }
        },

        async toggleStamp(id) {
            try {
                const item = State.templateItems.find(x => x.id === id);
                let stampValue = item?.stamp || '';

                if (!item?.has_stamp) {
                    stampValue = window.prompt('Stempeltext eingeben:', 'Stamm');
                    if (stampValue === null) return;
                    stampValue = stampValue.trim() || 'Stamm';
                }

                const response = await fetch(`/offers/templates/${id}/stamp`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ stamp: stampValue })
                });

                const result = await response.json();

                if (!result.success) {
                    alert(result.message || 'Stammm konnte nicht geändert werden.');
                    return;
                }

                if (item) {
                    item.stamp = result.stamp;
                    item.has_stamp = !!result.has_stamp;
                    item.stamped_by_employee_name = result.stamped_by_employee_name || null;
                    item.stamped_at = result.stamped_at || null;
                }

                this.renderResults();
            } catch (error) {
                console.error('toggleStamp error:', error);
                alert('Netzwerk-Fehler beim Ändern des Stamm.');
            }
        },

        async markTemplateUsed(id) {
            try {
                const response = await fetch(`/offers/templates/${id}/mark-used`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const item = State.templateItems.find(x => x.id === id);
                    if (item) {
                        item.usage_count = result.usage_count ?? ((item.usage_count || 0) + 1);
                        item.last_used_by_employee_name = result.last_used_by_employee_name || item.last_used_by_employee_name || null;
                        item.last_used_at = result.last_used_at || null;
                    }
                }
            } catch (error) {
                console.error('markTemplateUsed error:', error);
            }
        },

        async openUsageModal(id) {
            const modal = document.getElementById('template-usage-modal');
            const body = document.getElementById('template-usage-modal-body');
            const title = document.getElementById('template-usage-modal-title');
            const subtitle = document.getElementById('template-usage-modal-subtitle');
            const item = State.templateItems.find(x => x.id === id);

            if (!modal || !body || !title || !subtitle) return;

            title.textContent = item?.name || `Vorlage #${id}`;
            subtitle.textContent = 'Verwendungsdaten werden geladen...';
            body.innerHTML = `
                <div class="flex items-center justify-center py-16 text-[#000000]">
                    <i class="fa-solid fa-circle-notch fa-spin mr-3"></i> Lade Verwendungsdaten...
                </div>
            `;
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/offers/templates/${id}/usage-history`, {
                    headers: { Accept: 'application/json' }
                });

                const result = await response.json();

                if (!result.success) {
                    body.innerHTML = `<div class="py-10 text-center text-red-500">${App.escapeHtml(result.message || 'Verwendungsdaten konnten nicht geladen werden.')}</div>`;
                    return;
                }

                const rows = Array.isArray(result.items) ? result.items : [];
                const total = result.total_usage_count ?? item?.usage_count ?? rows.length;

                subtitle.textContent = `${total} Verwendungen insgesamt`;

                if (!rows.length) {
                    body.innerHTML = `
                        <div class="text-center py-16">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-[#000000] flex items-center justify-center mb-4">
                                <i class="fa-solid fa-chart-line text-2xl"></i>
                            </div>
                            <div class="text-lg font-black text-[#000000]">Noch keine Verwendungsdaten</div>
                            <div class="text-sm text-[#000000] mt-1">Diese Vorlage wurde bisher nicht verwendet.</div>
                        </div>
                    `;
                    return;
                }

                body.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-[11px] font-black uppercase tracking-wider text-[#000000]">Gesamt</div>
                            <div class="text-2xl font-black text-[#000000] mt-1">${total}</div>
                            <div class="text-xs text-[#000000] mt-1">Verwendungen</div>
                        </div>
                        <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4">
                            <div class="text-[11px] font-black uppercase tracking-wider text-yellow-700">Favorit von</div>
                            <div class="text-base font-black text-[#000000] mt-1">${App.escapeHtml(item?.favorite_by_employee_name || '—')}</div>
                            <div class="text-xs text-[#000000] mt-1">${App.escapeHtml(item?.favorite_at || 'Keine Angabe')}</div>
                        </div>
                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                            <div class="text-[11px] font-black uppercase tracking-wider text-indigo-700">Stamm von</div>
                            <div class="text-base font-black text-[#000000] mt-1">${App.escapeHtml(item?.stamped_by_employee_name || '—')}</div>
                            <div class="text-xs text-[#000000] mt-1">${App.escapeHtml(item?.stamped_at || 'Keine Angabe')}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-wider text-[#000000]">#</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-wider text-[#000000]">Mitarbeiter</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-wider text-[#000000]">Datum</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-wider text-[#000000]">Zeit</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-wider text-[#000000]">Info</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                ${rows.map((row, idx) => `
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-5 py-4 text-sm font-bold text-[#000000]">${idx + 1}</td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                ${this.getEmployeeAvatar(row.employee_name, 'usage')}
                                                <div>
                                                    <div class="text-sm font-bold text-[#000000]">${App.escapeHtml(row.employee_name || 'Unbekannt')}</div>
                                                    <div class="text-[11px] text-[#000000]">${App.escapeHtml(row.employee_email || '')}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-sm font-semibold text-[#000000]">${App.escapeHtml(row.used_date || '—')}</td>
                                        <td class="px-5 py-4 text-sm text-[#000000]">${App.escapeHtml(row.used_time || '—')}</td>
                                        <td class="px-5 py-4 text-xs text-[#000000]">${App.escapeHtml(row.note || 'Vorlage im Angebot verwendet')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } catch (error) {
                console.error('usage modal error:', error);
                body.innerHTML = '<div class="py-10 text-center text-red-500">Netzwerk-Fehler beim Laden der Verwendungsdaten.</div>';
            }
        },

        closeUsageModal() {
            const modal = document.getElementById('template-usage-modal');
            if (modal) modal.classList.add('hidden');
        },

        deleteTemplate(id) {
            App.toastConfirmShow({
                title: 'Vorlage löschen?',
                message: 'Möchten Sie diese Vorlage wirklich unwiderruflich löschen?',
                okText: 'Ja, löschen',
                cancelText: 'Abbrechen',
                onOk: async () => {
                    try {
                        const response = await fetch(`/offers/templates/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            App.TemplateTab.refresh();
                        } else {
                            alert('Fehler beim Löschen: ' + (result.message || 'Unbekannt'));
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert('Netzwerk-Fehler beim Löschen der Vorlage.');
                    }
                }
            });
        },

        async previewTemplate(id) {
            const btn = window.event?.currentTarget;
            let originalContent = '';

            if (btn) {
                originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
                btn.disabled = true;
            }

            try {
                const response = await fetch(`/offers/templates/${id}`, {
                    headers: { Accept: 'application/json' }
                });

                const data = await response.json();

                if (!data.success && !data.template) {
                    alert('Fehler beim Laden der Vorlage für die Vorschau.');
                    return;
                }

                const loadedSections = data.sections || data.template?.sections || [];
                const loadedImages = data.placed_images || data.template?.placed_images || [];

                if (loadedSections.length === 0) {
                    alert('Diese Vorlage enthält keine Positionen für eine Vorschau.');
                    return;
                }

                const backupSections = State.sections;
                const backupImages = State.placedImages;

                State.sections = loadedSections;
                State.placedImages = loadedImages;

                document.getElementById('print-preview-modal').classList.remove('hidden');
                App.buildPrintPreview();

                State.sections = backupSections;
                State.placedImages = backupImages;
                App.renderQuotePage(false);
            } catch (err) {
                console.error('Vorschau konnte nicht geladen werden:', err);
                alert('Netzwerk-Fehler beim Laden der Vorschau.');
            } finally {
                if (btn) {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            }
        },

        async useTemplate(id) {
            const template = State.templateItems.find(t => t.id === id);
            if (!template) return;

            const btnCard = document.getElementById(`btn-use-tpl-${id}`);
            const btnList = document.getElementById(`btn-use-tpl-list-${id}`);
            const originalContentCard = btnCard ? btnCard.innerHTML : '';
            const originalContentList = btnList ? btnList.innerHTML : '';

            if (btnCard) {
                btnCard.disabled = true;
                btnCard.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Lade...';
            }
            if (btnList) {
                btnList.disabled = true;
                btnList.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> LADE...';
            }

            try {
                const response = await fetch(`/offers/templates/${id}`, {
                    headers: { Accept: 'application/json' }
                });

                const data = await response.json();

                if (data.success || data.sections) {
                    const loadedSections = data.sections || data.template?.sections || [];

                    State.loadedTemplateId = template.id;
                    State.loadedTemplateName = template.name;

                    if (loadedSections.length === 0) {
                        alert('Diese Vorlage enthält keine Positionen.');
                        return;
                    }

                    if (State.sections.length === 1 && State.sections[0].items.length === 0) {
                        State.sections = loadedSections;
                    } else {
                        State.sections.push(...loadedSections);
                    }

                    await this.markTemplateUsed(id);

                    App.Tabs.switch('list');

                    if (App.Bio) {
                        App.Bio.addEntry('Vorlage eingefügt', `Die Vorlage "${template.name}" wurde dem Angebot hinzugefügt.`);
                    }

                    App.toastConfirmShow({
                        title: 'Vorlage erfolgreich eingefügt',
                        message: `Die Positionen aus "${template.name}" wurden an Ihr Angebot angehängt.`,
                        okText: 'Weiter bearbeiten',
                        cancelText: '',
                        onOk: () => {}
                    });

                    const cancelBtn = document.getElementById('toast-confirm-cancel');
                    if (cancelBtn) cancelBtn.style.display = 'none';

                    this.renderResults();
                } else {
                    alert('Fehler beim Laden der Vorlage: ' + (data.message || 'Unbekannter Fehler'));
                }
            } catch (err) {
                console.error('Failed to load template', err);
                alert('Netzwerk-Fehler beim Laden der Vorlage.');
            } finally {
                if (btnCard) {
                    btnCard.disabled = false;
                    btnCard.innerHTML = originalContentCard;
                }
                if (btnList) {
                    btnList.disabled = false;
                    btnList.innerHTML = originalContentList;
                }
            }
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
                                <div class="font-bold text-[#000000] truncate">${App.escapeHtml(u.name || ('User #' + u.id))}</div>
                                <div class="text-xs text-[#000000]">aktiv im Angebot</div>
                            </div>
                        </div>

                        <div class="text-xs font-bold text-green-600">online</div>
                    </div>
                `).join('')
                : `<div class="text-sm text-[#000000]">Aktuell ist niemand online.</div>`;

             
            const bioHtml = (State.biographyItems || []).length
                ? State.biographyItems.map(item => `
                    <div class="relative pl-6 pb-6 border-l-2 ${item.isLocal ? 'border-blue-200' : 'border-slate-200'}">
                        <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full ${item.isLocal ? 'bg-blue-500 animate-pulse' : 'bg-[#93c21c]'}"></div>
                        <div class="text-sm font-black ${item.isLocal ? 'text-blue-700' : 'text-[#000000]'}">
                            ${item.isLocal ? '<i class="fa-solid fa-pen-nib mr-1 text-[10px]"></i>' : ''}
                            ${App.escapeHtml(item.title || '')}
                        </div>
                        <div class="text-sm text-[#000000] mt-1">${App.escapeHtml(item.text || '')}</div>
                        <div class="text-[10px] uppercase font-bold text-[#000000] mt-1 tracking-wider">${App.escapeHtml(item.date || '')}</div>
                    </div>
                `).join('')
                : `<div class="text-sm text-[#000000]">Keine Biografie vorhanden.</div>`;

            root.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <div class="text-xs font-black uppercase tracking-wider text-[#000000]">Angebotshistorie</div>
                                <div class="text-lg font-black text-[#000000]">Biografie</div>
                            </div>
                            <div class="p-5">
                                ${bioHtml}
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200">
                                <div class="text-xs font-black uppercase tracking-wider text-[#000000]">Live</div>
                                <div class="text-lg font-black text-[#000000]">Online Bearbeiter</div>
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
 
    App.Bio.addEntry = (title, text) => {
        const now = new Date();
        const timestamp = now.toLocaleDateString('en-GB') + ' ' +
                        now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        
        // Add new entry to the beginning of the array
        State.biographyItems.unshift({
            type: 'local_change',
            title: title,
            text: text,
            date: timestamp,
            isLocal: true // Flag for specific styling
        });

        // If the Biography tab is currently active, re-render immediately
        if (App.Tabs.current === 'bio') {
            App.Bio.render();
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

       App.sendPresenceLeave = function () {
            const offerId = State.prefill?.offer_id || null;
            const folderId = State.prefill?.offer_folder_id || null;

            if (!offerId && !folderId) return;

            const payload = JSON.stringify({
                offer_id: offerId,
                offer_folder_id: folderId
            });

            // preferred
            fetch('/offers/document/presence/leave', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin',
                body: payload,
                keepalive: true
            }).catch(() => {});

            // fallback
            try {
                navigator.sendBeacon(
                    '/offers/document/presence/leave',
                    new Blob([payload], { type: 'application/json' })
                );
            } catch (e) {}
        };

        window.addEventListener('beforeunload', App.sendPresenceLeave);
        window.addEventListener('pagehide', App.sendPresenceLeave);

        window.addEventListener('beforeunload', (e) => {
            if (State.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = ''; // Required by Chrome to show the prompt
                return ''; // Required by legacy browsers
            }
        });
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                App.sendPresenceLeave();
            }
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

    App.openPrintPreview = () => {
        // 1. Update the live editor first to save any pending visual state
        App.renderQuotePage(false);

        // 2. Unhide the modal
        document.getElementById('print-preview-modal').classList.remove('hidden');

        // 3. Build the actual print preview layout
        App.buildPrintPreview();
    };

   App.buildPrintPreview = function () {
        const printRoot = document.getElementById('print-preview-content');
        if (!printRoot) return;

        // 1. Clear current preview
        printRoot.innerHTML = '';

        // 2. Clone the Cover Page (Page 1) and add it to the print root FIRST
        const livePage1 = document.getElementById('page-1');
        if (livePage1) {
            const staticPage1 = App.buildStaticPageClone(livePage1);
            if (staticPage1) {
                // Remove live floating elements from the clone so they aren't duplicated,
                // renderFloatingImages(true) will create fresh print-ready ones on this page!
                staticPage1.querySelectorAll('.floating-element').forEach(el => el.remove());
                staticPage1.id = 'page-1'; // Ensure it has the correct ID for anchors
                printRoot.appendChild(staticPage1);
            }
        }

        // 3. Generate Pages 2+ and attach floating elements everywhere (including Page 1)
        App.renderQuotePage(true);

        // 4. Run makeThumbnailStatic to clean up the dynamically generated pages (Pages 2+)
        Array.from(printRoot.children).forEach(page => {
            if (page.id !== 'page-1') {
                 App.makeThumbnailStatic(page);
            }
        });

        // 5. Restore the live editor immediately so the background UI isn't broken
        App.renderQuotePage(false);
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
        const marginPct = avgEk > 0 ? ((avgVk - avgEk) / avgEk) * 100 : 0;

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
    // ✅ LIST VIEW (ENTERPRISE REWRITE) 
    // + Sektions-Marge (Top-Down Weighted Distribution)
    // + Rekursive Restore/Undo Funktionalität
    // + Zielpreis-Kalkulation (VK Überschreiben für Sets)
    // ============================================================
    App.ListView = {
           getDefaults() {
                return {
                    cols: {
                        checkbox: true, pos: true, image: true, articleNumber: true, title: true,
                        supplier: false, dokumente: false, type: true, status: true, qty: true, qty_total: true,
                        unit: true, pe: false, ek: true, ek_total: true, margin: true, vk: true,
                        profit: false, vk_total: true, db_total: false, weighting: false, total: false, actions: true,
                    },
                    open: {}
                };
            },

            ensureStore() {
                if (!State.listViewPrefs) State.listViewPrefs = App.ListView.getDefaults();
                if (!State.listViewPrefs.cols) State.listViewPrefs.cols = App.ListView.getDefaults().cols;
                if (!State.listViewPrefs.open) State.listViewPrefs.open = {};
                if (!State.selectedItems) State.selectedItems = new Set();
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
                        currentParent = { parent: sub, parentIndex: originalIndex, children: [] };
                        groups.push(currentParent);
                        return;
                    }

                    if (!currentParent) {
                        currentParent = { parent: { ...sub, depth: 1 }, parentIndex: originalIndex, children: [] };
                        groups.push(currentParent);
                        return;
                    }

                    currentParent.children.push({ item: sub, index: originalIndex });
                });

                return groups;
            },

            // --- TARGET PRICE LOGIC (VK Überschreiben) ---
            toggleParentVkLock(sIdx, iIdx) {
                State.unlockedParentVk = State.unlockedParentVk || {};
                const key = `${sIdx}-${iIdx}`;
                State.unlockedParentVk[key] = !State.unlockedParentVk[key];
                App.ListView.render();
            },

            applyTargetPrice(sIdx, iIdx, targetValue) {
                const mainItem = State.sections[sIdx]?.items[iIdx];
                if (!mainItem || !Array.isArray(mainItem.subItems)) return;

                const targetPrice = parseFloat(targetValue) || 0;
                let currentTotalVk = 0;

                // 1. Calculate the current total sales price (VK) of all active sub-items
                mainItem.subItems.forEach(sub => {
                    if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                        currentTotalVk += App.calcItemGross(sub);
                    }
                });

                const activeSubCount = mainItem.subItems.filter(s => s.active !== false && (s.status || 'normal') === 'normal').length;
                
                // Calculate the scaling factor (New Price / Old Price)
                const scaleFactor = currentTotalVk > 0 ? (targetPrice / currentTotalVk) : null;

                // 2. Apply the new target price (VK) using the scaling factor
                mainItem.subItems.forEach(sub => {
                    if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                        let allocatedTotalVk = 0;

                        if (scaleFactor !== null) {
                            const subVkTotal = App.calcItemGross(sub);
                            allocatedTotalVk = subVkTotal * scaleFactor;
                        } else {
                            // Fallback: If the old VK was 0, distribute the new price evenly across items
                            allocatedTotalVk = targetPrice / activeSubCount;
                        }

                        // Scale it back down to the unit price (VK / Einheit)
                        const qty = Number(sub.qty || 1);
                        const baseQty = Number(sub.price_unit_value || 1);
                        const vkPerUnit = qty > 0 ? (allocatedTotalVk * baseQty) / qty : 0;

                        // Set the new value
                        sub.price = vkPerUnit;

                        // Recalculate the margins for the sub-item
                        const ek = Number(sub.purchase_price || sub.ek || 0);
                        sub.marginType = 'percent';
                        sub.margin = ek > 0 ? ((vkPerUnit - ek) / ek) * 100 : 100;
                        sub.marginPercent = sub.margin;

                        // If it's a labor/service item, the inner rows need to be scaled too
                        if (sub.kind === 'labor' && Array.isArray(sub.labor_rows)) {
                            let totQ = 0, totVk = 0, totEk = 0;
                            sub.labor_rows.forEach(row => {
                                if (scaleFactor !== null) {
                                    row.rate = row.rate * scaleFactor;
                                } else {
                                    row.rate = vkPerUnit; // Fallback
                                }
                                row.margin_percent = Number(row.ek || 0) > 0 ? ((row.rate - Number(row.ek || 0)) / Number(row.ek || 0)) * 100 : 100;
                                row.total = Number(row.qty || 0) * row.rate;
                                
                                totQ += Number(row.qty || 0);
                                totVk += row.total;
                                totEk += Number(row.qty || 0) * Number(row.ek || 0);
                            });
                            sub.qty = totQ || 1;
                            sub.price = totQ > 0 ? totVk / totQ : 0;
                            sub.ek = totQ > 0 ? totEk / totQ : 0;
                            sub.purchase_price = sub.ek;
                            sub.marginPercent = sub.ek > 0 ? ((sub.price - sub.ek) / sub.ek) * 100 : 100;
                            sub.margin = sub.marginPercent;
                        }
                    }
                });

                // 3. Sync parent totals and re-render
                App.syncParentTotals(sIdx, iIdx);
                App.renderQuotePage();
                
                // 4. Lock the padlock again automatically
                State.unlockedParentVk[`${sIdx}-${iIdx}`] = false;
            },

            // --- ENTERPRISE SECTION MARGIN LOGIC ---
            applySectionMargin(sIdx, newMargin) {
                const val = parseFloat(newMargin);
                if (isNaN(val)) return;
                
                const sec = State.sections[sIdx];
                if (!sec || sec.isLocked) return;

                 // 1. Calculate the CURRENT section totals to determine the weighting scale factor
                let oldVk = 0, oldEk = 0;
                // FIX: No need to recurse. The main items already hold the aggregated unit price
                // of their children. Using calcItemGross at the top level automatically respects it.qty!
                (sec.items || []).forEach(it => {
                    if (!it || it.active === false || it.kind === 'note') return;
                    oldVk += App.calcItemGross(it);
                    oldEk += App.calcItemCost(it);
                });

                const oldMarginPct = oldEk > 0 ? ((oldVk - oldEk) / oldEk) * 100 : 0;
                sec._prevSectionMargin = oldMarginPct; // Save for UI

                // Target Math: Calculate the Scaling Factor required to hit the requested margin globally
                const targetVk = oldEk * (1 + val / 100);
                const scaleFactor = oldVk > 0 ? (targetVk / oldVk) : null;

                // 2. Apply the scaling factor recursively
                const applyScale = (items) => {
                    items.forEach(it => {
                        if (!it || it.active === false || it.kind === 'note') return;

                        // Memorize previous state for the Restore button
                        if (!it._prevMarginState) {
                            it._prevMarginState = {
                                marginPercent: it.marginPercent,
                                margin: it.margin,
                                marginType: it.marginType,
                                price: it.price
                            };
                            if (it.kind === 'labor' && Array.isArray(it.labor_rows)) {
                                it._prevMarginState.labor_rows = it.labor_rows.map(r => ({...r}));
                            }
                        }

                        if (Array.isArray(it.subItems) && it.subItems.length > 0 && !it.isPauschal) {
                            // It's a parent with active sub-items. Drill down first.
                            applyScale(it.subItems);
                            
                            // Recalculate parent based on its updated children
                            let pVk = 0, pEk = 0;
                            it.subItems.forEach(sub => {
                                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                                    pVk += App.calcItemGross(sub);
                                    pEk += App.calcItemCost(sub);
                                }
                            });
                            it.price = pVk;
                            it.ek = pEk;
                            it.purchase_price = pEk;
                            it.marginPercent = pEk > 0 ? ((pVk - pEk) / pEk) * 100 : (pVk > 0 ? 100 : 0);
                            it.margin = it.marginPercent;
                            it.marginType = 'percent';
                            
                        } else {
                            // It's a leaf node. Apply the scale factor.
                            if (scaleFactor !== null) {
                                it.price = it.price * scaleFactor;
                            } else {
                                // Fallback if oldVk was 0 (Edge case)
                                const ek = Number(it.purchase_price || it.ek || 0);
                                it.price = ek * (1 + val / 100);
                            }
                            
                            const ek = Number(it.purchase_price || it.ek || 0);
                            it.marginPercent = ek > 0 ? ((it.price - ek) / ek) * 100 : 100;
                            it.margin = it.marginPercent;
                            it.marginType = 'percent';

                            // If it's a labor item, scale the inner labor rows identically
                            if (it.kind === 'labor' && Array.isArray(it.labor_rows)) {
                                let totQ=0, totVk=0, totEk=0;
                                it.labor_rows.forEach(row => {
                                    if (scaleFactor !== null) {
                                        row.rate = row.rate * scaleFactor;
                                    } else {
                                        row.rate = Number(row.ek || 0) * (1 + val / 100);
                                    }
                                    row.margin_percent = Number(row.ek || 0) > 0 ? ((row.rate - Number(row.ek || 0)) / Number(row.ek || 0)) * 100 : 100;
                                    row.total = Number(row.qty || 0) * row.rate;
                                    
                                    totQ += Number(row.qty||0);
                                    totVk += row.total;
                                    totEk += Number(row.qty||0) * Number(row.ek||0);
                                });
                                it.qty = totQ || 1;
                                it.price = totQ > 0 ? totVk / totQ : 0;
                                it.ek = totQ > 0 ? totEk / totQ : 0;
                                it.purchase_price = it.ek;
                                it.marginPercent = it.ek > 0 ? ((it.price - it.ek) / it.ek) * 100 : 100;
                                it.margin = it.marginPercent;
                            }
                        }
                    });
                };

                applyScale(sec.items || []);
                
                // Clear input field visually
                const inputEl = document.getElementById(`sec-margin-input-${sIdx}`);
                if (inputEl) inputEl.value = '';
                
                App.renderQuotePage(); 
            },

            restoreSectionMargin(sIdx) {
                const sec = State.sections[sIdx];
                if (!sec || sec.isLocked) return;

                const restoreItems = (items) => {
                    items.forEach(it => {
                        if (!it) return;
                        
                        // 1. Restore exact previous values if they exist
                        if (it._prevMarginState) {
                            it.marginPercent = it._prevMarginState.marginPercent;
                            it.margin = it._prevMarginState.margin;
                            it.marginType = it._prevMarginState.marginType;
                            it.price = it._prevMarginState.price;
                            
                            if (it.kind === 'labor' && it._prevMarginState.labor_rows) {
                                it.labor_rows = it._prevMarginState.labor_rows.map(r => ({...r}));
                            }
                            
                            delete it._prevMarginState; // Clear memory after restore
                        }

                        // 2. Handle sub-items recursively
                        if (Array.isArray(it.subItems) && it.subItems.length > 0 && !it.isPauschal) {
                            restoreItems(it.subItems);
                            
                            // Recalculate parent totals based on restored children
                            let newVk = 0, newEk = 0;
                            it.subItems.forEach(sub => {
                                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                                    newVk += App.calcItemGross(sub);
                                    newEk += App.calcItemCost(sub);
                                }
                            });
                            it.price = newVk;
                            it.ek = newEk;
                            it.purchase_price = newEk;
                            it.marginPercent = newEk > 0 ? ((newVk - newEk) / newEk) * 100 : (newVk > 0 ? 100 : 0);
                            it.margin = it.marginPercent;
                        }
                    });
                };

                restoreItems(sec.items || []);
                delete sec._prevSectionMargin; // Remove saved indicator from UI
                App.renderQuotePage();
            },

            getRenderableSectionEntries() {
                const source = App.getRenderableSections() || [];
                const entries = [];
                let realIndex = 0;

                for (const sec of source) {
                    if (!sec || sec._pageBreak) continue;

                    if (sec._virtualSection) {
                        entries.push({ section: sec, stateIndex: null, isVirtual: true });
                        continue;
                    }

                    while (realIndex < (State.sections || []).length) {
                        const candidate = State.sections[realIndex];
                        if (!candidate || candidate._pageBreak) {
                            realIndex++;
                            continue;
                        }
                        if (candidate === sec) {
                            entries.push({ section: sec, stateIndex: realIndex, isVirtual: false });
                            realIndex++;
                            break;
                        }
                        realIndex++;
                    }
                }
                return entries;
            },

            getRowVisualMeta(it, isSub = false, extra = {}) {
                const currentKind = it?.kind || (it?.item_type === 'labor' ? 'labor' : 'article');
                const isNote = currentKind === 'note';
                const isLabor = currentKind === 'labor';

                if (isNote) return { rowClass: 'lv-row-note', kindClass: 'note', kindLabel: 'Hinweis' };
                if (isLabor) return { rowClass: 'lv-row-labor', kindClass: 'labor', kindLabel: 'Lohn' };
                
                return { rowClass: 'lv-row-article', kindClass: 'article', kindLabel: isSub ? 'Unter.' : 'Ark.' };
            },

            handleDropOnSection(ev, sIdx) {
                ev.preventDefault();
                ev.currentTarget.classList.remove('drag-over');
                const id = ev.dataTransfer.getData('text');
                const type = ev.dataTransfer.getData('itemType');
                if (id && type && sIdx !== null && sIdx !== undefined) App.handleItemAdd(sIdx, id, type);
                App.clearDragMode();
            },

            handleDropOnPosition(ev, sIdx, iIdx) {
                ev.preventDefault();
                ev.stopPropagation();
                const id = ev.dataTransfer.getData('text');
                const type = ev.dataTransfer.getData('itemType');
                if (id && type && sIdx !== null && sIdx !== undefined) App.addLibraryItemAsSubPosition(sIdx, iIdx, id, type);
                App.clearDragMode();
            },

            toggleRowSelection(key, isSelected) {
                App.ListView.ensureStore();
                if (isSelected) State.selectedItems.add(key);
                else State.selectedItems.delete(key);
                App.ListView.render();
            },

            toggleSelectAll(isSelected) {
                App.ListView.ensureStore();
                State.selectedItems.clear();
                if (isSelected) {
                    State.sections.forEach((sec, sIdx) => {
                        if (!sec || sec._pageBreak || sec.isLocked || sec._virtualSection) return;
                        (sec.items || []).forEach((it, iIdx) => {
                            if (it.active === false) return;
                            State.selectedItems.add(`${sIdx}:${iIdx}:null:${it.depth || 0}`);
                            (it.subItems || []).forEach((sub, subIdx) => {
                                if (sub.active === false) return;
                                State.selectedItems.add(`${sIdx}:${iIdx}:${subIdx}:${sub.depth || 1}`);
                            });
                        });
                    });
                }
                App.ListView.render();
            },

            bulkDelete() {
                App.ListView.ensureStore();
                if (State.selectedItems.size === 0) return;
                if (!confirm(`Möchten Sie ${State.selectedItems.size} markierte Positionen löschen?`)) return;

                const keys = Array.from(State.selectedItems).map(k => {
                    const parts = k.split(':');
                    return { s: parseInt(parts[0]), i: parseInt(parts[1]), sub: parts[2] === 'null' ? null : parseInt(parts[2]) };
                }).sort((a, b) => {
                    if (a.s !== b.s) return b.s - a.s;
                    if (a.i !== b.i) return b.i - a.i;
                    if (a.sub === null && b.sub !== null) return 1;
                    if (a.sub !== null && b.sub === null) return -1;
                    if (a.sub !== null && b.sub !== null) return b.sub - a.sub;
                    return 0;
                });

                keys.forEach(k => {
                    if (k.sub !== null) {
                        if (State.sections[k.s]?.items[k.i]?.subItems) {
                            State.sections[k.s].items[k.i].subItems.splice(k.sub, 1);
                            App.syncParentTotals(k.s, k.i);
                        }
                    } else {
                        if (State.sections[k.s]?.items) State.sections[k.s].items.splice(k.i, 1);
                    }
                });

                State.selectedItems.clear();
                App.renderQuotePage();
            },

            bulkMargin() {
                App.ListView.ensureStore();
                if (State.selectedItems.size === 0) return;

                const val = prompt('Neue Marge in % für alle markierten Positionen eingeben:');
                if (val === null || val.trim() === '') return;

                const newMargin = parseFloat(val);
                if (isNaN(newMargin)) return alert('Ungültige Eingabe.');

                State.selectedItems.forEach(k => {
                    const parts = k.split(':');
                    const s = parseInt(parts[0]);
                    const i = parseInt(parts[1]);
                    const sub = parts[2] === 'null' ? null : parseInt(parts[2]);

                    if (sub === null) {
                        const mainItem = State.sections[s]?.items[i];
                        if (mainItem && Array.isArray(mainItem.subItems) && mainItem.subItems.length > 0 && !mainItem.isPauschal) {
                            mainItem.marginType = 'percent';
                            App.applyGeneralMargin(s, i, newMargin);
                        } else {
                            App.updatePosPriceCalc(s, i, null, 'marginType', 'percent');
                            App.updatePosPriceCalc(s, i, null, 'marginPercent', newMargin);
                        }
                    } else {
                        App.updatePosPriceCalc(s, i, sub, 'marginType', 'percent');
                        App.updatePosPriceCalc(s, i, sub, 'marginPercent', newMargin);
                    }
                });

                State.selectedItems.clear();
                App.renderQuotePage();
            },

            getColDefs() {
                const cols = App.ListView.cols();
                const defs = [];

                if (cols.checkbox) defs.push({ key: 'checkbox', label: '<input type="checkbox" class="w-4 h-4 accent-[#93c21c] cursor-pointer" onchange="App.ListView.toggleSelectAll(this.checked)">', width: '40px', align: 'center' });
                if (cols.pos) defs.push({ key: 'pos', label: 'Pos.', width: '92px', align: 'center' });
                if (cols.image) defs.push({ key: 'image', label: 'Bild', width: '72px', align: 'center' });
                if (cols.articleNumber) defs.push({ key: 'articleNumber', label: 'Art.-Nr.', width: '170px' });
                if (cols.title) defs.push({ key: 'title', label: 'Produkttitel', width: '340px' });
                if (cols.supplier) defs.push({ key: 'supplier', label: 'Lieferant & Konditionen', width: '240px' });
                if (cols.dokumente) defs.push({ key: 'dokumente', label: 'Dokumente', width: '180px' });
                if (cols.type) defs.push({ key: 'type', label: 'Typ', width: '110px', align: 'center' });
                if (cols.status) defs.push({ key: 'status', label: 'Status', width: '120px', align: 'center' });
                if (cols.qty) defs.push({ key: 'qty', label: 'Menge', width: '130px', align: 'center' });
                if (cols.qty_total) defs.push({ key: 'qty_total', label: 'Gesamtmenge', width: '130px', align: 'center' });
                if (cols.unit) defs.push({ key: 'unit', label: 'Einheit', width: '110px', align: 'center' });
                if (cols.pe) defs.push({ key: 'pe', label: 'VPE', width: '90px', align: 'center' });
                if (cols.ek) defs.push({ key: 'ek', label: 'EK / Einheit', width: '130px', align: 'right' });
                if (cols.ek_total) defs.push({ key: 'ek_total', label: 'EK gesamt', width: '145px', align: 'right' });
                if (cols.margin) defs.push({ key: 'margin', label: 'Marge', width: '110px', align: 'right' });
                if (cols.vk) defs.push({ key: 'vk', label: 'VK / Einheit', width: '130px', align: 'right' });
                if (cols.profit) defs.push({ key: 'profit', label: 'DB / Einheit', width: '130px', align: 'right' });
                if (cols.vk_total) defs.push({ key: 'vk_total', label: 'VK gesamt', width: '145px', align: 'right' });
                if (cols.db_total) defs.push({ key: 'db_total', label: 'DB gesamt', width: '145px', align: 'right' });
                if (cols.weighting) defs.push({ key: 'weighting', label: 'Gewichtung', width: '180px' });
                if (cols.total) defs.push({ key: 'total', label: 'Gesamt', width: '120px', align: 'right' });
                if (cols.actions) defs.push({ key: 'actions', label: 'Aktionen', width: '188px', align: 'right' });

                return defs;
            },

            toolbarHtml() {
                const cols = App.ListView.cols();
                const checked = (name) => cols[name] ? 'checked' : '';
                App.ListView.ensureStore();
                const selectedCount = State.selectedItems ? State.selectedItems.size : 0;

                const bulkToolbar = selectedCount > 0 ? `
                    <div class="flex items-center gap-2 bg-[#f7fee7] px-3 py-1.5 rounded-xl border border-[#93c21c]/30 mr-2 animate-fadeIn">
                        <span class="text-xs font-black text-[#6b8e12]">${selectedCount} markiert</span>
                        
                        <button class="list-mini-btn !py-1 !text-yellow-600 hover:!border-yellow-500" onclick="App.Clipboard.copyBulk()">
                            <i class="fa-regular fa-copy"></i> Kopieren
                        </button>

                        <button class="list-mini-btn !py-1 !text-blue-600 hover:!border-blue-500" onclick="App.ListView.bulkMargin()">
                            <i class="fa-solid fa-percent"></i> Marge
                        </button>
                        <button class="list-mini-btn !py-1 !text-red-500 hover:!border-red-500" onclick="App.ListView.bulkDelete()">
                            <i class="fa-solid fa-trash"></i> Löschen
                        </button>
                    </div>
                ` : '';

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
                            <div class="text-xs text-[#000000]">Listenansicht</div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${bulkToolbar}
                            <button class="list-mini-btn list-mini-btn-supplier" onclick="if(window.App && App.SupplierSearch){App.SupplierSearch.open();}else{console.error('SupplierSearch is not ready');}" title="IDS/OCI Lieferanten-Shop öffnen und Artikel direkt übernehmen"><i class="fa-solid fa-plug-circle-bolt"></i> Lieferant suchen</button>
                            <button class="list-mini-btn" onclick="App.addSection()"><i class="fa-solid fa-folder-plus"></i> Sektion</button>
                            <button class="list-mini-btn" onclick="App.addPositionQuick()"><i class="fa-solid fa-plus"></i> Position</button>
                            <button class="list-mini-btn list-mini-btn-history" onclick="App.SupplierSearch && App.SupplierSearch.openHistory ? App.SupplierSearch.openHistory() : console.error('Supplier history is not ready')" title="Historie der übernommenen Lieferanten-Positionen anzeigen und erneut einfügen"><i class="fa-solid fa-clock-rotate-left"></i> Historie</button>
                            <details class="list-colpicker">
                                <summary class="list-mini-btn"><i class="fa-solid fa-table-columns"></i> Spalten</summary>
                                <div class="list-colpicker-menu" style="width:260px; max-height:420px; overflow-y:auto;">
                                    ${pickerItem('Checkbox', 'checkbox')} ${pickerItem('Pos.', 'pos')} ${pickerItem('Bild', 'image')}
                                    ${pickerItem('Art.-Nr.', 'articleNumber')} ${pickerItem('Produkttitel', 'title')}
                                    ${pickerItem('Lieferant & Konditionen', 'supplier')} ${pickerItem('Dokumente', 'dokumente')}
                                    ${pickerItem('Typ', 'type')} ${pickerItem('Status', 'status')} ${pickerItem('Menge', 'qty')} ${pickerItem('Gesamtmenge', 'qty_total')}
                                    ${pickerItem('Einheit', 'unit')} ${pickerItem('VPE', 'pe')} ${pickerItem('EK / Einheit', 'ek')}
                                    ${pickerItem('EK gesamt', 'ek_total')} ${pickerItem('Marge', 'margin')} ${pickerItem('VK / Einheit', 'vk')}
                                    ${pickerItem('DB / Einheit', 'profit')} ${pickerItem('VK gesamt', 'vk_total')} ${pickerItem('DB gesamt', 'db_total')}
                                    ${pickerItem('Gewichtung', 'weighting')} ${pickerItem('Gesamt', 'total')} ${pickerItem('Aktionen', 'actions')}
                                    <div class="pt-2 mt-2 border-t"><button class="list-mini-btn w-full" onclick="App.ListView.resetCols()">Standard</button></div>
                                </div>
                            </details>
                        </div>
                    </div>
                `;
            },

           sectionHeadHtml(sec, sIdx, isVirtual = false) {
                const isLocked = !!sec.isLocked || !!sec._virtualSection || isVirtual;
                const isOpen = App.ListView.isOpen(`sec:${sIdx}`, true);

                // NEW: Multiplier logic
                const secQty = sec.config?.qty || 1;
                const secUnit = sec.config?.unit || '';
                const isSet = secUnit.toLowerCase() === 'set';
                const multiplier = isSet ? secQty : 1;

                let secVK = 0, secEK = 0;
                (sec.items || []).forEach(it => {
                    if (!it || it.active === false || (it.lineType || 'standard') !== 'standard' || (it.kind || '') === 'note') return;
                    secVK += App.calcItemGross(it);
                    secEK += App.calcItemCost(it);
                });
                
                // Apply Multiplier to UI display
                secVK *= multiplier;
                secEK *= multiplier;

                const secMarginPct = secEK > 0 ? ((secVK - secEK) / secEK) * 100 : 0;
                const hasPrevState = (sec.items || []).some(it => it && it._prevMarginState);
                const prevMarginText = sec._prevSectionMargin !== undefined ? sec._prevSectionMargin.toFixed(1) : '?';

                return `
                    <div class="list-sec-head p-1 flex items-center justify-between mt-2 mb-1 rounded-[20px]">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-3xl bg-[#93c21c] text-white font-black ">${sIdx + 1}</span>
                            <button type="button" onclick="App.ListView.toggleOpen('sec:${sIdx}')" class="lv-pos-toggle shrink-0" title="${isOpen ? 'Sektion zuklappen' : 'Sektion aufklappen'}">
                                <i class="fa-solid fa-chevron-right ${isOpen ? 'rotate-90' : ''} transition-transform"></i>
                            </button>
                            <div class="flex-1 min-w-0 flex items-center gap-3">
                                ${isLocked 
                                    ? `<div class="bg-transparent outline-none font-black text-[#000000] w-auto text-[15px]">${App.escapeHtml(sec.title || '')}</div>` 
                                    : `<input value="${App.escapeHtml(sec.title || '')}" onchange="App.updateSectionMeta(${sIdx},'title',this.value)" class="bg-transparent outline-none font-black text-[#000000] w-auto flex-1 text-[15px]">`
                                }
                                
                                ${!isVirtual && !isLocked ? `
                                    <div class="flex items-center gap-1 border border-slate-300 bg-white rounded overflow-hidden">
                                        <input type="number" min="1" value="${secQty}" onchange="App.updateSectionConfig(${sIdx}, 'qty', this.value)" class="w-16 px-2 py-1 text-xs outline-none text-center font-bold" title="Sektions-Menge">
                                        <select onchange="App.updateSectionConfig(${sIdx}, 'unit', this.value)" class="px-2 py-1 text-xs outline-none cursor-pointer bg-slate-50 border-l border-slate-200">
                                            <option value="" ${!isSet ? 'selected' : ''}>Standard</option>
                                            <option value="Set" ${isSet ? 'selected' : ''}>Set</option>
                                        </select>
                                    </div>
                                ` : (isSet ? `<span class="text-xs font-bold text-[#000000] bg-white/50 px-2 py-1 rounded">${secQty} Set(s)</span>` : '')}
                            </div>

                            ${!isVirtual && !isLocked ? `
                                <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-200 px-3 py-1.5 shadow-sm mr-4">
                                    <span class="text-[10px] font-black text-[#000000] uppercase tracking-wide mr-2" title="Aktuelle durchschnittliche Marge">Ø Marge: ${secMarginPct.toFixed(1)}%</span>
                                    <div class="w-px h-5 bg-slate-200"></div>
                                    <div class="flex items-center pl-2">
                                        <input type="number" step="0.1" id="sec-margin-input-${sIdx}" class="w-14 text-center text-xs font-black text-[#000000] outline-none bg-slate-50 border border-slate-200 hover:border-[#93c21c] focus:border-[#93c21c] rounded px-1 transition-colors" placeholder="Neu %" onchange="App.ListView.applySectionMargin(${sIdx}, this.value)">
                                    </div>
                                    ${hasPrevState ? `
                                        <div class="w-px h-5 bg-slate-200 mx-1"></div>
                                        <button onclick="App.ListView.restoreSectionMargin(${sIdx})" class="text-[#000000] hover:text-red-500 px-1 transition-colors flex items-center gap-1" title="Zurücksetzen">
                                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                            <span class="text-[9px] font-bold">Zu ${prevMarginText}%</span>
                                        </button>
                                    ` : ''}
                                </div>
                            ` : ''}

                            <span class="inline-flex items-center rounded-full px-3 h-9 bg-[#cfe09b] border border-slate-200 text-dark-400 font-black shadow-sm">
                                ∑ ${App.money(secVK)} €
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            ${!isVirtual ? `<button onclick="App.toggleSectionLock(${sIdx})" class="mat-btn-icon" title="${isLocked ? 'Sektion entsperren' : 'Sektion sperren'}"><i class="fa-solid ${isLocked ? 'fa-lock' : 'fa-unlock'}"></i></button>` : ``}
                            ${(!isLocked && !isVirtual) ? `
                                <button onclick="App.addNotePosition(${sIdx})" class="mat-btn-icon" title="Hinweis hinzufügen"><i class="fa-solid fa-note-sticky"></i></button>
                                <button onclick="App.addManualItem(${sIdx})" class="mat-btn-icon" title="Position hinzufügen"><i class="fa-solid fa-plus"></i></button>
                                <button onclick="App.removeSection(${sIdx})" class="mat-btn-icon text-red-500" title="Sektion löschen"><i class="fa-solid fa-trash"></i></button>
                            ` : ``}
                        </div>
                    </div>
                `;
            },

           rowHtml(it, sIdx, iIdx, subIdx, level, posStr, defs, gridTemplate, extra = {}) {
                if (!it || it.active === false) return '';

                const isSub = subIdx !== null && subIdx !== undefined;
                const isLocked = !!State.sections?.[sIdx]?.isLocked || !!extra.isVirtualSection;
                
                // FIX: subArg konsistent definieren
                const subArg = (subIdx === null || subIdx === undefined || subIdx === 'null') ? 'null' : subIdx;
                
                const focusKeyBase = [sIdx, iIdx, subArg, level].join(':');

                const currentKind = it.kind || (it.item_type === 'labor' ? 'labor' : 'article');
                const currentLineType = it.lineType || 'standard';
                const isNote = currentKind === 'note';
                const isLabor = currentKind === 'labor';

                const hasLaborRows = isLabor && Array.isArray(it.labor_rows) && it.labor_rows.length > 0;
                const laborSummary = hasLaborRows
                    ? App.getLaborRowSummary(it)
                    : { totalHours: 0, avgVk: 0, avgEk: 0, marginPct: 0, totalVk: 0, totalEk: 0 };

                const isProtectedRow = isNote || isLabor || !!extra.isVirtualSection;

                const qty = isLabor ? Number(laborSummary.totalHours || 0) : Number(it.qty || 1);
                const vk = isLabor ? Number(laborSummary.avgVk || 0) : Number(it.price || 0);
                const ek = isLabor ? Number(laborSummary.avgEk || 0) : Number(it.purchase_price || it.ek || 0);
                const margin = isLabor ? Number(laborSummary.marginPct || 0) : Number(it.marginPercent ?? it.margin ?? 0);
                const mType = it.marginType || 'percent'; 

                const totalVK = isLabor ? Number(laborSummary.totalVk || 0) : App.calcItemGross(it);
                const totalEK = isLabor ? Number(laborSummary.totalEk || 0) : App.calcItemCost(it);
                const db1 = totalVK - totalEK;

                const pPe = Math.max(1, Number(it.price_unit_value || 1));

                const grandTotals = (App.getRenderableSections() || []).reduce((acc, sec) => {
                    (sec.items || []).forEach(row => {
                        if (!row || row.active === false || (row.kind || '') === 'note') return;
                        acc.ek += App.calcItemCost(row);
                        acc.vk += App.calcItemGross(row);
                        acc.db += (App.calcItemGross(row) - App.calcItemCost(row));
                        (row.subItems || []).forEach(sub => {
                            if (!sub || sub.active === false || (sub.kind || '') === 'note') return;
                            acc.ek += App.calcItemCost(sub);
                            acc.vk += App.calcItemGross(sub);
                            acc.db += (App.calcItemGross(sub) - App.calcItemCost(sub));
                        });
                    });
                    return acc;
                }, { ek: 0, vk: 0, db: 0 });

                const weightEK = grandTotals.ek > 0 ? (totalEK / grandTotals.ek) * 100 : 0;
                const weightDB = grandTotals.db > 0 ? (db1 / grandTotals.db) * 100 : 0;
                const weightEKText = Number.isFinite(weightEK) ? weightEK.toFixed(1) : '0.0';
                const weightDBText = Number.isFinite(weightDB) ? weightDB.toFixed(1) : '0.0';
                const safeWeightEK = Math.max(0, Math.min(100, Number(weightEKText)));
                const safeWeightDB = Math.max(0, Math.min(100, Number(weightDBText)));

                const ctxText = (field) => (
                    isSub ? `App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'${field}',this.value)` : `App.updateItemDetails(${sIdx},${iIdx},'${field}',this.value)`
                );
                const ctxCalc = (field) => `App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'${field}',this.value)`;

                // --- MARGIN LOCK LOGIC ---
                const hasChildren = Array.isArray(it.subItems) && it.subItems.length > 0;
                const isPauschal = !!it.isPauschal;
                
                State.unlockedParentMargins = State.unlockedParentMargins || {};
                const isParentUnlocked = State.unlockedParentMargins[`${sIdx}-${iIdx}`];

                const isEkReadonly = (hasChildren && !isPauschal) 
                    ? 'readonly disabled class="mat-addon-input text-right bg-transparent text-[#000000] font-bold"' 
                    : 'class="mat-addon-input text-right"';
                
                const isVkReadonly = (hasChildren && !isPauschal && !isParentUnlocked) 
                    ? 'readonly disabled class="mat-addon-input text-right bg-transparent text-[#000000] font-bold"' 
                    : 'class="mat-addon-input text-right"';
                
                const isSelectReadonly = (hasChildren && !isPauschal && !isParentUnlocked) 
                    ? 'disabled class="mat-addon-text cursor-not-allowed bg-slate-50"' 
                    : 'class="mat-addon-text cursor-pointer hover:bg-slate-100 transition-colors border-none outline-none appearance-none"';

                const marginHandler = (hasChildren && !isPauschal)
                    ? `App.applyGeneralMargin(${sIdx}, ${iIdx}, this.value)`
                    : `App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'margin',this.value)`;

                const typeHandler = (hasChildren && !isPauschal)
                    ? `App.updateParentMarginType(${sIdx}, ${iIdx}, this.value)`
                    : `App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'marginType',this.value)`;

                // --- DRAG & DROP RESTRICTION & LANDING LINE ---
                let dragAttrs = '';
                let handleHtml = '';
                if (!isLocked && !extra.isVirtualSection) {
                    dragAttrs = `
                        draggable="false" 
                        ondragover="App.handlePosDragOver(event, this)"
                        ondragleave="App.handlePosDragLeave(this)"
                        ondrop="App.handlePosDrop(event, this, ${sIdx}, ${iIdx}, ${subArg})"
                    `;
                    // Fixed handle HTML using group-hover for visibility in list view
                    handleHtml = `
                        <span class="drag-handle opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity" 
                            draggable="true" 
                            ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx}, ${subArg})" 
                            title="Hier ziehen zum Verschieben">
                            <i class="fa-solid fa-grip-vertical"></i>
                        </span>
                    `;
                }

                const cellWrap = (html, extraCls = '') => `<div class="mat-grid-cell ${extraCls}">${html}</div>`;
                const visualMeta = App.ListView.getRowVisualMeta(it, isSub, extra);
                let cells = '';

                if (isNote) {
                    cells = `
                        <div class="mat-grid-cell" style="grid-column: 1 / -1; background: transparent;">
                            <div class="flex items-start gap-4 w-full py-1 pl-2">
                                <div class="mt-1 flex items-center gap-2">
                                    ${handleHtml}
                                    <span class="lv-kind-badge note">Hinweis</span>
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col gap-3">
                                    <input data-lv-focus="name:${focusKeyBase}" value="${App.escapeHtml(it.name)}" onchange="${ctxText('name')}" class="mat-ctrl mat-input font-bold text-blue-900 w-full bg-white border-blue-200" placeholder="Hinweistitel..." ${isLocked ? 'readonly' : ''}>
                                    <button type="button" onclick="App.openDescModal(${sIdx},${iIdx},${subArg})" class="lv-desc-btn ${isLocked ? 'hidden' : ''} text-left text-xs text-slate-500 hover:text-blue-600 transition-colors"><i class="fa-solid fa-pen-to-square"></i> Beschreibung bearbeiten ${it.desc_html || it.desc ? '<span class="text-green-600 font-bold ml-1">(vorhanden)</span>' : ''}</button>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    ${(!isLocked && !extra.isVirtualSection) ? `<button onclick="App.removeItem(${sIdx},${iIdx},${subArg})" class="mat-btn-icon text-red-500 shrink-0" title="Löschen"><i class="fa-solid fa-trash"></i></button>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    defs.forEach((col) => {
                        const alignClass = col.align === 'right' ? 'mat-grid-cell-right' : col.align === 'center' ? 'mat-grid-cell-center' : '';

                        switch (col.key) {
                            case 'checkbox':
                                App.ListView.ensureStore();
                                cells += cellWrap(`<input type="checkbox" class="w-4 h-4 accent-[#93c21c] cursor-pointer" ${(State.selectedItems && State.selectedItems.has(focusKeyBase)) ? 'checked' : ''} onchange="App.ListView.toggleRowSelection('${focusKeyBase}', this.checked)" ${isLocked ? 'disabled' : ''}>`, alignClass);
                                break;

                            case 'pos':
                                const canToggle = !!extra.canToggle;
                                const isExpanded = canToggle ? App.ListView.isOpen(extra.toggleKey, true) : true;
                                cells += cellWrap(`
                                    <div class="lv-pos-wrap">
                                        ${handleHtml}
                                        <div class="lv-pos-inline flex items-center gap-1">
                                            ${canToggle ? `<button type="button" onclick="App.ListView.toggleOpen('${extra.toggleKey}')" class="lv-pos-toggle" title="${isExpanded ? 'Zuklappen' : 'Aufklappen'}"><i class="fa-solid fa-chevron-right ${isExpanded ? 'rotate-90' : ''} transition-transform"></i></button>` : `<span class="lv-pos-toggle-placeholder"></span>`}
                                            <span class="lv-pos-badge">${posStr}</span>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'image':
                                cells += cellWrap(it.hideImage || isLabor ? `<div class="w-12 h-12 mx-auto bg-transparent"></div>` : `<div class="w-12 h-12 mx-auto overflow-hidden bg-transparent cursor-pointer" onclick="App.handleImageClick(event, ${sIdx}, ${iIdx}, ${subArg})" title="Bild öffnen"><img src="${it.img || App.placeholderImg(it.name)}" class="w-full h-full object-cover"></div>`, alignClass);
                                break;

                            case 'articleNumber':
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-slate-50 flex items-center px-2 text-[#000000] w-full">—</div>` : `<input value="${App.escapeHtml(it.article_no || it.articleNumber || '')}" onchange="${ctxText('article_no')}" class="mat-ctrl mat-input w-full" placeholder="Art.-Nr." ${isLocked ? 'readonly' : ''}>`, alignClass);
                                break;

                            case 'title':
                                cells += cellWrap(`
                                    <div class="lv-title-wrap" style="padding-left:${level * 14}px">
                                        <div class="lv-title-row">
                                            ${level > 0 ? '<i class="fa-solid fa-level-up fa-rotate-90 text-slate-300 text-[10px]"></i>' : ''}
                                            <input data-lv-focus="name:${focusKeyBase}" value="${App.escapeHtml(it.name)}" onchange="${ctxText('name')}" class="mat-ctrl mat-input font-bold w-full" placeholder="Bezeichnung" ${isLocked ? 'readonly' : ''}>
                                            <button type="button" onclick="App.openDescModal(${sIdx},${iIdx},${subArg})" class="lv-title-edit-btn" title="Beschreibung bearbeiten" ${isLocked ? 'disabled' : ''}><i class="fa-solid fa-pen-to-square"></i></button>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'supplier':
                                const skontoVal = Number(it.skonto || 0);
                                const paymentDays = Number(it.payment_terms || 14);
                                cells += cellWrap(isLabor ? `<div class="text-[#000000] text-xs font-bold">—</div>` : `
                                    <div class="flex flex-col gap-2 w-full">
                                        <input value="${App.escapeHtml(it.distributor_name || it.supplier || '')}" onchange="${ctxText('distributor_name')}" placeholder="Lieferant" class="mat-ctrl mat-input w-full" ${isLocked ? 'readonly' : ''}>
                                        <input value="${App.escapeHtml(it.distributor_article_no || '')}" onchange="${ctxText('distributor_article_no')}" placeholder="Lief.-Nr." class="mat-ctrl mat-input w-full" ${isLocked ? 'readonly' : ''}>
                                        <div class="grid grid-cols-2 gap-2 w-full">
                                            <div class="mat-addon-wrap"><input type="number" step="0.01" value="${skontoVal.toFixed(2)}" onchange="${ctxText('skonto')}" class="mat-addon-input text-right" ${isLocked ? 'readonly' : ''}><span class="mat-addon-text">Skonto %</span></div>
                                            <div class="mat-addon-wrap"><input type="number" step="1" value="${paymentDays}" onchange="${ctxText('payment_terms')}" class="mat-addon-input text-right" ${isLocked ? 'readonly' : ''}><span class="mat-addon-text">Tage</span></div>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'dokumente':
                                const docs = Array.isArray(it.docs) ? it.docs : [];
                                cells += cellWrap(`
                                    <div class="flex flex-col items-start gap-2 w-full">
                                        ${docs.length ? docs.map(doc => `<button type="button" class="mat-chip w-full justify-start gap-2 text-[#78b2ce] border-[#78b2ce]/20 bg-[#78b2ce]/10"><i class="fas fa-file-alt w-3 h-3 shrink-0"></i><span class="truncate">${App.escapeHtml(doc?.name || 'Dokument')}</span></button>`).join('') : `<span class="text-[10px] text-[#000000] italic flex items-center gap-1 font-bold"><i class="fas fa-minus w-3 h-3"></i> Keine Dokumente</span>`}
                                    </div>
                                `, alignClass);
                                break;

                            case 'type':
                                cells += cellWrap(hasLaborRows ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-center font-black text-dark-400 w-full">Lohn</div>` : `
                                    <select onchange="App.updatePosConfig(${sIdx},${iIdx},${subArg},'kind',this.value)" class="mat-ctrl mat-input-center w-full" ${isLocked || !!extra.isVirtualSection ? 'disabled' : ''}>
                                        <option value="article" ${currentKind === 'article' ? 'selected' : ''}>Artikel</option>
                                        <option value="labor" ${currentKind === 'labor' ? 'selected' : ''}>Lohn</option>
                                        <option value="note" ${currentKind === 'note' ? 'selected' : ''}>Hinweis</option>
                                    </select>
                                `, alignClass);
                                break;

                            case 'status':
                                cells += cellWrap(`
                                    <select onchange="App.updatePosStatus(${sIdx},${iIdx},${subArg},this.value)" class="mat-ctrl mat-input-center w-full" ${isLocked ? 'disabled' : ''}>
                                        <option value="normal" ${currentLineType === 'standard' ? 'selected' : ''}>Standard</option>
                                        <option value="optional" ${currentLineType === 'optional' ? 'selected' : ''}>Optional</option>
                                        <option value="alternative" ${currentLineType === 'alternative' ? 'selected' : ''}>Alternativ</option>
                                    </select>
                                `, alignClass);
                                break;

                            case 'qty':
                                const decFn = isSub ? `App.updateSubItemDetails(${sIdx},${iIdx},${subArg},'qty', Math.max(1, ${qty} - 1))` : `App.updateItemDetails(${sIdx},${iIdx},'qty', Math.max(1, ${qty} - 1))`;
                                const incFn = isSub ? `App.updateSubItemDetails(${sIdx},${iIdx},${subArg},'qty', ${qty} + 1)` : `App.updateItemDetails(${sIdx},${iIdx},'qty', ${qty} + 1)`;
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-center font-black text-dark-400 w-full">${qty.toFixed(2)}</div>` : `
                                    <div class="flex items-center h-[25px] border border-[#d6e0ea] rounded-xl bg-white overflow-hidden focus-within:border-[#93c21c] focus-within:ring-4 focus-within:ring-[#93c21c]/15 w-full">
                                        <button type="button" ${isLocked ? 'disabled' : ''} onclick="${decFn}" class="w-9 h-full flex items-center justify-center text-[#000000] hover:bg-slate-50 hover:text-[#93c21c] transition-colors shrink-0" title="Verringern"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                        <input data-lv-focus="qty:${focusKeyBase}" type="number" step="0.01" value="${qty}" onchange="${ctxText('qty')}" class="flex-1 w-full h-full text-center text-xs font-black text-[#000000] outline-none bg-transparent" ${isLocked ? 'readonly' : ''}>
                                        <button type="button" ${isLocked ? 'disabled' : ''} onclick="${incFn}" class="w-9 h-full flex items-center justify-center text-[#000000] hover:bg-slate-50 hover:text-[#93c21c] transition-colors shrink-0" title="Erhöhen"><i class="fa-solid fa-plus text-[10px]"></i></button>
                                    </div>
                                `, alignClass);
                                break;

                            case 'qty_total':
                                const multiplier = Number(extra.multiplier || 1);
                                const parentQty = Number(extra.parentQty || 1); 
                                const baseQty = qty; 
                                const calculatedTotalQty = baseQty * parentQty * multiplier;
                                
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-slate-50 border-transparent flex items-center justify-center font-black text-[#000000] w-full">${calculatedTotalQty.toFixed(2)}</div>` : `
                                    <div class="flex items-center h-[25px] border border-transparent rounded-xl bg-slate-50 overflow-hidden w-full">
                                        <div class="flex-1 w-full h-full text-center text-xs font-black text-[#000000] flex items-center justify-center">${calculatedTotalQty.toFixed(2)}</div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'unit':
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-center font-black text-dark-400 w-full">Std</div>` : `
                                    <select data-lv-focus="unit:${focusKeyBase}" onchange="${isSub ? `App.updateSubItemUnit(${sIdx},${iIdx},${subArg},this.value)` : `App.updateItemUnit(${sIdx},${iIdx},this.value)`}" class="mat-ctrl mat-input-center w-full" ${isLocked ? 'disabled' : ''}>
                                        ${App.renderUnitOptions(it.measure || it.unit || 'Stk')}
                                    </select>
                                `, alignClass);
                                break;

                            case 'pe':
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-slate-50 flex items-center justify-center font-black text-[#000000] w-full">—</div>` : `<input type="number" step="1" min="1" value="${pPe}" onchange="${ctxText('price_unit_value')}" class="mat-ctrl mat-input-center w-full" ${isLocked ? 'readonly' : ''}>`, alignClass);
                                break;

                            case 'ek':
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-end px-2 font-black text-dark-400 w-full">${App.money(ek)} €</div>` : `
                                    <div class="mat-addon-wrap w-full">
                                        <input data-lv-focus="ek:${focusKeyBase}" type="number" step="0.01" value="${ek.toFixed(2)}" onchange="${ctxCalc('ek')}" class="mat-addon-input text-right" ${isEkReadonly}>
                                        <span class="mat-addon-text">€</span>
                                    </div>
                                `, alignClass);
                                break;

                            case 'ek_total':
                                cells += cellWrap(`<div class=" text-[#000000] font-black text-[13px]">${App.money(totalEK)} €</div>`, alignClass);
                                break;

                            case 'margin': 
                                const mVal = mType === 'fixed' ? (Number(it.margin) || 0) : margin; 
                                const prevMarginText = it._prevMargin !== undefined ? Number(it._prevMargin).toFixed(1) : '?';
                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-end px-2 font-black text-dark-400 w-full">${margin.toFixed(1)} %</div>` : `
                                    <div class="w-full flex flex-col justify-end">
                                        ${(hasChildren && !isPauschal && !isSub && isParentUnlocked && it._prevMargin !== undefined) ? `
                                            <div class="text-[10px] text-[#000000] flex justify-end mb-0.5 pr-1">
                                                <button type="button" onclick="event.stopPropagation(); App.resetGeneralMargin(${sIdx}, ${iIdx})" class="hover:text-red-500 transition-colors flex items-center gap-1 font-bold" title="Auf vorherigen Wert zurücksetzen">
                                                    <i class="fa-solid fa-rotate-left"></i> ${prevMarginText}%
                                                </button>
                                            </div>
                                        ` : ''}
                                        <div class="mat-addon-wrap w-full relative">
                                            ${(hasChildren && !isPauschal && !isSub) ? `
                                                <div class="absolute left-2 top-1/2 -translate-y-1/2 flex items-center z-10">
                                                    <button type="button" onclick="event.stopPropagation(); App.toggleParentMarginLock(${sIdx}, ${iIdx})" class="text-[#000000] hover:text-[#93c21c] transition-colors" title="${isParentUnlocked ? 'Wieder sperren' : 'Sperre aufheben'}">
                                                        <i class="fa-solid ${isParentUnlocked ? 'fa-lock-open text-[#93c21c]' : 'fa-lock'} text-[11px]"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                            <input data-lv-focus="margin:${focusKeyBase}" type="number" step="0.1" value="${mVal.toFixed(2)}" onchange="${marginHandler}" class="mat-addon-input text-right" style="${(hasChildren && !isPauschal && !isSub) ? 'padding-left: 1.5rem;' : ''}" ${isVkReadonly}>
                                            <select data-lv-focus="marginType:${focusKeyBase}" onchange="${typeHandler}" class="mat-addon-text cursor-pointer hover:bg-slate-100 transition-colors border-none outline-none appearance-none" ${isSelectReadonly}>
                                                <option value="percent" ${mType==='percent'?'selected':''}>%</option>
                                                <option value="fixed" ${mType==='fixed'?'selected':''}>€</option>
                                            </select>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'vk':
                                State.unlockedParentVk = State.unlockedParentVk || {};
                                const isVkUnlocked = State.unlockedParentVk[`${sIdx}-${iIdx}`];
                                
                                const isVkDisabled = (hasChildren && !isPauschal && !isVkUnlocked);
                                const vkReadonlyAttrs = isVkDisabled 
                                    ? 'readonly disabled class="mat-addon-input text-right bg-transparent text-[#000000] font-bold"' 
                                    : `class="mat-addon-input text-right ${hasChildren && !isPauschal ? 'bg-blue-50' : ''}"`;
                                
                                const prevVkText = it._prevVk !== undefined ? App.money(it._prevVk) : '?';
                                
                                const vkHandler = (hasChildren && !isPauschal)
                                    ? `App.ListView.applyTargetPrice(${sIdx}, ${iIdx}, this.value)`
                                    : `App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'price',this.value)`;

                                cells += cellWrap(isLabor ? `<div class="mat-ctrl bg-white border-dark-400 flex items-center justify-end px-2 font-black text-dark-400 w-full">${App.money(vk)} €</div>` : `
                                    <div class="w-full flex flex-col justify-end">
                                        ${(hasChildren && !isPauschal && !isSub && isVkUnlocked && it._prevVk !== undefined) ? `
                                            <div class="text-[10px] text-[#000000] flex justify-end mb-0.5 pr-1">
                                                <button type="button" onclick="event.stopPropagation(); App.resetGeneralMargin(${sIdx}, ${iIdx}); State.unlockedParentVk['${sIdx}-${iIdx}'] = false; App.ListView.render();" class="hover:text-red-500 transition-colors flex items-center gap-1 font-bold" title="Auf vorherigen Wert zurücksetzen">
                                                    <i class="fa-solid fa-rotate-left"></i> ${prevVkText} €
                                                </button>
                                            </div>
                                        ` : ''}
                                        <div class="mat-addon-wrap w-full relative">
                                            ${(hasChildren && !isPauschal && !isSub) ? `
                                                <div class="absolute left-2 top-1/2 -translate-y-1/2 flex items-center z-10">
                                                    <button type="button" onclick="event.stopPropagation(); App.ListView.toggleParentVkLock(${sIdx}, ${iIdx})" class="text-[#000000] hover:text-blue-500 transition-colors" title="${isVkUnlocked ? 'Wieder sperren' : 'Sperre aufheben'}">
                                                        <i class="fa-solid ${isVkUnlocked ? 'fa-lock-open text-blue-500' : 'fa-lock'} text-[11px]"></i>
                                                    </button>
                                                </div>
                                            ` : ''}
                                            <input data-lv-focus="vk:${focusKeyBase}" type="number" step="0.01" value="${vk.toFixed(2)}" onchange="${vkHandler}" ${vkReadonlyAttrs} style="${(hasChildren && !isPauschal && !isSub) ? 'padding-left: 1.5rem;' : ''}">
                                            <span class="mat-addon-text">€</span>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'profit':
                                const profitPerPiece = pPe > 0 ? (vk - ek) : 0;
                                cells += cellWrap(`<div class=" text-[#4f8aa7] font-black text-[13px]">${App.money(profitPerPiece)} €</div>`, alignClass);
                                break;

                            case 'vk_total':
                                cells += cellWrap(`<div class=" text-[#000000] font-black text-[13px]">${App.money(totalVK)} €</div>`, alignClass);
                                break;

                            case 'db_total':
                                const dbTone = db1 >= 0 ? 'text-emerald-700' : 'text-red-600';
                                cells += cellWrap(`<div class=" ${dbTone} font-black text-[13px]">${App.money(db1)} €</div>`, alignClass);
                                break;

                            case 'weighting':
                                cells += cellWrap(`
                                    <div class="flex flex-col gap-2 w-full">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex justify-between items-center text-[10px]"><span class="text-[#000000] font-bold">Kosten</span><span class="font-black text-[#000000]">${weightEKText}%</span></div>
                                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-slate-500 rounded-full" style="width:${safeWeightEK}%"></div></div>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <div class="flex justify-between items-center text-[10px]"><span class="text-[#000000] font-bold">DB</span><span class="font-black text-[#4f8aa7]">${weightDBText}%</span></div>
                                            <div class="w-full h-2 bg-[#78b2ce]/10 rounded-full overflow-hidden"><div class="h-full bg-[#78b2ce] rounded-full" style="width:${safeWeightDB}%"></div></div>
                                        </div>
                                    </div>
                                `, alignClass);
                                break;

                            case 'total':
                                cells += cellWrap(`<div class=" text-[#000000] font-black  text-[13px]">${App.money(totalVK)} €</div>`, alignClass);
                                break;

                            case 'actions':
                                const isHiddenPrint = it.print_hidden !== false;
                                cells += cellWrap(`
                                    <div class="flex items-center gap-1 justify-end w-full">
                                        ${(!extra.isVirtualSection) ? `<button onclick="App.updatePosConfig(${sIdx},${iIdx},${subArg},'print_hidden', ${!isHiddenPrint})" class="mat-btn-icon ${isHiddenPrint ? 'text-red-500 hover:!border-red-500 hover:bg-red-50' : 'text-green-500 hover:!border-green-500 hover:bg-green-50'}" title="Im Druck anzeigen/verbergen"><i class="fa-solid ${isHiddenPrint ? 'fa-eye-slash' : 'fa-eye'}"></i></button>` : ''}
                                        
                                        ${(!extra.isVirtualSection) ? `<button onclick="App.Clipboard.copyRow(${sIdx},${iIdx},${subArg})" class="mat-btn-icon text-yellow-500 hover:!border-yellow-500 hover:bg-yellow-50" title="Kopieren in Zwischenablage"><i class="fa-regular fa-copy"></i></button>` : ''}
                                        
                                        ${(!isLocked && !extra.isVirtualSection) ? `<button onclick="App.insertPositionAfter(${sIdx},${iIdx},${subArg})" class="mat-btn-icon text-blue-500 hover:!border-blue-500 hover:bg-blue-50" title="Neue Position darunter einfügen"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-arrow-down text-[8px] -ml-0.5"></i></button>` : ''}
                                        ${(!isLocked && !isSub && !isNote && !hasLaborRows && !extra.isVirtualSection) ? `<button onclick="App.addSubItem(${sIdx},${iIdx})" class="mat-btn-icon" title="Unterposition hinzufügen"><i class="fa-solid fa-level-down-alt"></i></button>` : ''}
                                        ${!extra.isVirtualSection ? `<button onclick="App.openPosSettings(${sIdx},${iIdx},${subArg})" class="mat-btn-icon" title="Einstellungen"><i class="fa-solid fa-sliders"></i></button>` : ''}
                                        ${(!isLocked && !extra.isVirtualSection) ? `<button onclick="App.removeItem(${sIdx},${iIdx},${subArg})" class="mat-btn-icon text-red-500" title="Löschen"><i class="fa-solid fa-trash"></i></button>` : ''}
                                    </div>
                                `, alignClass);
                                break;
                        }
                    });
                }

                let laborRowsHtml = '';
                if (isLabor && !isLocked) {
                    const isHiddenPrint = it.print_hidden_labor !== false;
                    const eyeIcon = isHiddenPrint ? 'fa-eye-slash' : 'fa-eye';

                    laborRowsHtml = `
                        <div class="mt-2 mx-4 mb-4 p-4 rounded-2xl bg-white/80">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="text-sm font-black uppercase tracking-wide text-[#93c21c]">Arbeitsleistungsdetails</div>
                                    <button onclick="App.updatePosConfig(${sIdx},${iIdx},${subArg},'print_hidden_labor', ${!isHiddenPrint}); App.ListView.render();" class="text-[#000000] hover:text-[#000000] transition-colors" title="Details im Druck anzeigen/verbergen"><i class="fa-solid ${eyeIcon}"></i></button>
                                </div>
                                <button type="button" onclick="App.addLaborRow(${sIdx},${iIdx},${subArg})" class="lv-desc-btn !bg-white !border-dark-400 !text-dark-400"><i class="fa-solid fa-plus mr-1"></i> Zeile hinzufügen</button>
                            </div>
                            <table class="w-full text-xs text-left border-collapse">
                                <thead style="border-bottom: 2px solid #93c21c; color: #93c21c;">
                                    <tr>
                                        <th class="py-2 pr-2 font-black">Person / Qualifikation</th>
                                        <th class="py-2 px-2 font-black text-center w-20">Menge</th>
                                        <th class="py-2 px-2 font-black text-center w-20">Einheit</th>
                                        <th class="py-2 px-2 font-black text-right w-24">EK-Preis</th>
                                        <th class="py-2 px-2 font-black text-right w-20">Marge %</th>
                                        <th class="py-2 px-2 font-black text-right w-24">Satz</th>
                                        <th class="py-2 pl-2 font-black text-right w-28">Gesamt</th>
                                        <th class="py-2 w-8"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-amber-100">
                                    ${(Array.isArray(it.labor_rows) ? it.labor_rows : []).map((row, rowIdx) => `
                                        <tr class="group hover:bg-white/50 transition-colors">
                                            <td class="py-1.5 pr-2"><select class="w-full bg-white outline-none border border-dark-400 hover:border-dark-800 focus:border-[#d4a72c] rounded-xl p-2 font-bold" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'qualification_id',this.value)">${App.renderLaborOptionOptions(row.qualification_id, row.qualification_name)}</select></td>
                                            <td class="py-1.5 px-2"><input type="number" step="0.01" class="w-full text-center bg-white outline-none border border-[#e7d7aa] hover:border-amber-300 focus:border-[#d4a72c] rounded-xl p-2 font-bold" value="${Number(row.qty || 0).toFixed(2)}" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'qty',this.value)"></td>
                                            <td class="py-1.5 px-2"><select class="w-full text-center bg-white outline-none border border-[#e7d7aa] hover:border-amber-300 focus:border-[#d4a72c] rounded-xl p-2 font-bold" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'unit',this.value)">${App.renderLaborUnitOptions(row.unit || 'Std')}</select></td>
                                            <td class="py-1.5 px-2"><input type="number" step="0.01" class="w-full text-right bg-white outline-none border border-[#e7d7aa] hover:border-amber-300 focus:border-[#d4a72c] rounded-xl p-2 font-bold" value="${Number(row.ek || 0).toFixed(2)}" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'ek',this.value)"></td>
                                            <td class="py-1.5 px-2"><input type="number" step="0.1" class="w-full text-right bg-white outline-none border border-[#e7d7aa] hover:border-amber-300 focus:border-[#d4a72c] rounded-xl p-2 font-bold" value="${Number(row.margin_percent ?? App.getDefaultMargin('labor')).toFixed(1)}" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'margin_percent',this.value)"></td>
                                            <td class="py-1.5 px-2"><input type="number" step="0.01" class="w-full text-right bg-white outline-none border border-[#e7d7aa] hover:border-amber-300 focus:border-[#d4a72c] rounded-xl p-2 font-bold" value="${Number(row.rate || 0).toFixed(2)}" onchange="App.updateLaborRowField(${sIdx},${iIdx},${subArg},${rowIdx},'rate',this.value)"></td>
                                            <td class="py-1.5 pl-2 text-right font-black text-[#000000]">${App.money(Number(row.qty || 0) * Number(row.rate || 0))} €</td>
                                            <td class="py-1.5 text-right"><button type="button" onclick="App.removeLaborRow(${sIdx},${iIdx},${subArg},${rowIdx})" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity p-1" title="Zeile löschen"><i class="fa-solid fa-trash"></i></button></td>
                                        </tr>
                                    `).join('')}
                                    ${(!Array.isArray(it.labor_rows) || it.labor_rows.length === 0) ? `<tr><td colspan="8" class="py-6 text-center text-[#000000] text-xs italic bg-white/40 rounded-xl">Keine Dienstleistungs-Details hinterlegt.<br>Klicken Sie oben auf <b>"Zeile hinzufügen"</b>.</td></tr>` : ''}
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                // Add 'group item-group' classes globally to ensure hover works correctly
                return `
                    <div id="lv-row-${sIdx}-${iIdx}-${subArg}" class="mat-data-row group item-group ${visualMeta.rowClass} ${it._supplierJustImported ? 'supplier-import-row-flash' : ''} ${isSub ? 'is-sub-row' : 'is-main-row'}" ${dragAttrs}>
                        <div class="mat-data-grid" style="display:grid; grid-template-columns:${gridTemplate};">
                            ${cells}
                        </div>
                        ${laborRowsHtml}
                    </div>
                `;
            },

           renderSubTree(it, sIdx, iIdx, defs, gridTemplate, extra = {}) {
                const groups = App.ListView.getStructuredSubItems(it.subItems || []);
                if (!groups.length) return '';
                let html = '';
                
                // Extract parentQty, default to 1 if not set
                const parentQty = Number(extra.parentQty || 1);

                groups.forEach((group) => {
                    const hasChildren = App.ListView.hasChildRows(group);
                    const subKey = App.ListView.subOpenKey(sIdx, iIdx, group.parentIndex);
                    const posStr = `${sIdx + 1}.${iIdx + 1}.${group.parentIndex + 1}`;
                    
                    // Render the group parent (level 1 sub-item)
                    html += App.ListView.rowHtml(group.parent, sIdx, iIdx, group.parentIndex, 1, posStr, defs, gridTemplate, { 
                        canToggle: hasChildren, 
                        toggleKey: subKey, 
                        isVirtualSection: !!extra.isVirtualSection, 
                        multiplier: extra.multiplier,
                        parentQty: parentQty // Apply parent Qty
                    });
                    
                    if (!hasChildren || App.ListView.isOpen(subKey, true)) {
                        (group.children || []).forEach((child) => {
                            const childPosStr = `${posStr}.${child.index + 1}`;
                            
                            // For deep children (level 2), calculate the combined parent qty
                            // Group Parent Qty * Main Item Qty
                            const deepParentQty = parentQty * Number(group.parent.qty || 1);
                            
                            html += App.ListView.rowHtml(child.item, sIdx, iIdx, child.index, 2, childPosStr, defs, gridTemplate, { 
                                isVirtualSection: !!extra.isVirtualSection, 
                                multiplier: extra.multiplier,
                                parentQty: deepParentQty // Apply combined parent Qty
                            });
                        });
                    }
                });
                return html;
            },

            renderTotalsRow(defs, gridTemplate) {
                const totals = (App.getRenderableSections() || []).reduce((acc, sec) => {
                    const secQty = sec.config?.qty || 1;
                    const isSet = (sec.config?.unit || '').toLowerCase() === 'set';
                    const multiplier = isSet ? secQty : 1;

                    (sec.items || []).forEach(row => {
                        if (!row || row.active === false || (row.kind || '') === 'note') return;
                        const hasSubItems = Array.isArray(row.subItems) && row.subItems.length > 0 && !row.isPauschal;
                        
                        // FIX: Grab the parent quantity to scale sub-item values
                        const parentQty = Number(row.qty || 1);

                        if (hasSubItems) {
                            row.subItems.forEach(sub => {
                                if (!sub || sub.active === false || (sub.kind || '') === 'note' || ((sub.lineType || sub.status || 'standard') !== 'standard' && (sub.status || 'normal') !== 'normal')) return;
                                
                                // FIX: Multiply by parentQty
                                const subVK = App.calcItemGross(sub) * multiplier * parentQty;
                                const subEK = App.calcItemCost(sub) * multiplier * parentQty;
                                acc.ek += subEK; acc.vk += subVK; acc.db += (subVK - subEK);
                            });
                        } else {
                            const rType = row.lineType || row.status || 'standard';
                            if (rType === 'standard' || rType === 'normal') {
                                const rowVK = App.calcItemGross(row) * multiplier;
                                const rowEK = App.calcItemCost(row) * multiplier;
                                acc.ek += rowEK; acc.vk += rowVK; acc.db += (rowVK - rowEK);
                            }
                        }
                    });
                    return acc;
                }, { ek: 0, vk: 0, db: 0 });

                const totalMarginPct = totals.ek > 0 ? ((totals.vk - totals.ek) / totals.ek) * 100 : 0;

                const cellWrap = (html, extraCls = '') => `<div class="mat-grid-cell ${extraCls}">${html}</div>`;
                let cells = '';

                defs.forEach((col) => {
                    switch (col.key) {
                        case 'pos':
                            cells += cellWrap(`<div class="font-black text-xs uppercase tracking-[0.14em] text-[#000000]">Gesamt</div>`, 'mat-grid-cell-center mat-total-cell');
                            break;
                        case 'ek_total':
                            cells += cellWrap(`<div class="w-full flex flex-col items-end"><div class="text-[10px] uppercase tracking-[0.12em] font-black text-[#000000]">EK gesamt</div><div class="text-sm font-black text-[#000000]">${App.money(totals.ek)} €</div></div>`, 'mat-grid-cell-right mat-total-cell');
                            break;
                        case 'margin': 
                            cells += cellWrap(`
                                <div class="w-full flex flex-col items-end">
                                    <div class="text-[10px] uppercase tracking-[0.12em] font-black text-[#000000]">Ø Marge</div>
                                    <div class="text-sm font-black text-[#000000]">${totalMarginPct.toFixed(1)} %</div>
                                </div>
                            `, 'mat-grid-cell-right mat-total-cell');
                            break;
                        case 'vk_total':
                        case 'total':
                            cells += cellWrap(`<div class="w-full flex flex-col items-end"><div class="text-[10px] uppercase tracking-[0.12em] font-black text-[#78b2ce]">VK gesamt</div><div class="text-sm font-black text-[#4f8aa7]">${App.money(totals.vk)} €</div></div>`, 'mat-grid-cell-right mat-total-cell');
                            break;
                        case 'db_total':
                            cells += cellWrap(`<div class="w-full flex flex-col items-end"><div class="text-[10px] uppercase tracking-[0.12em] font-black text-emerald-600">DB gesamt</div><div class="text-sm font-black text-emerald-700">${App.money(totals.db)} €</div></div>`, 'mat-grid-cell-right mat-total-cell');
                            break;
                        default:
                            cells += cellWrap('', 'mat-total-cell');
                            break;
                    }
                });

                return `<div class="mat-data-row mat-total-row"><div class="mat-data-grid" style="display:grid; grid-template-columns:${gridTemplate};">${cells}</div></div>`;
            },

            analyticsHtml: function () {
                const key = 'lv:controlling';
                const isPanelOpen = App.ListView.isOpen(key, false);
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
                const otherSales = Math.max(0, sales - (matSales + laborSales));

                const salesPerHour = safe(totals.salesPerHour);
                const profitPerHour = safe(totals.profitPerHour);

                const matShare = sales > 0 ? (matSales / sales) * 100 : 0;
                const laborShare = sales > 0 ? (laborSales / sales) * 100 : 0;
                const otherShare = sales > 0 ? (otherSales / sales) * 100 : 0;

                const targetMargin = safe(State?.config?.minProfit || 10);
                const marginTotalPct = sales > 0 ? (db3 / sales) * 100 : 0;
                const marginVsTarget = marginTotalPct - targetMargin;

                const maxAbs = Math.max(1, Math.abs(sales), Math.abs(ekSum), Math.abs(db1), Math.abs(db2), Math.abs(netProfit));
                const barHeight = (val) => `${Math.round(clamp01(Math.abs(val) / maxAbs) * 100)}%`;

                const dashA = clamp01(matShare / 100) * 100;
                const dashB = clamp01(laborShare / 100) * 100;
                const dashC = Math.max(0, 100 - dashA - dashB);

                const isProfitBad = netProfit < 0;
                const isDb3Bad = db3 < 0;
                const isMarginBad = marginTotalPct < targetMargin;
                const isProfitHourBad = profitPerHour < 0;

                const chevronCls = isPanelOpen ? 'rotate-0' : 'rotate-180';
                
                const gainBoxClass = isProfitBad ? 'text-blue-700 bg-blue-50 border-blue-100' : 'text-green-700 bg-green-50 border-green-100';
                const gainSubClass = isProfitBad ? 'text-blue-400' : 'text-green-400';
                
                const db2BarClass = db2 < 0 ? 'bg-red-500' : 'bg-slate-300';
                const netBarClass = isProfitBad ? 'bg-blue-600' : 'bg-emerald-600';
                
                const marginBarClass = isMarginBad ? 'bg-red-500' : 'bg-emerald-500';
                const marginTextClass = isMarginBad ? 'text-red-600' : 'text-emerald-700';
                
                const profitHourTextClass = isProfitHourBad ? 'text-red-600' : 'text-green-600';
                const profitHourBarClass = isProfitHourBad ? 'bg-red-500' : 'bg-green-500';

                return `
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-2 mt-2 mx-2">
                        <div class="bg-slate-100 p-3 border-b border-slate-200 flex justify-between items-center cursor-pointer hover:bg-slate-200 transition-colors"
                            onclick="App.ListView.toggleOpen('${key}')">
                            <h3 class="font-bold text-[#000000] text-sm flex items-center gap-2 uppercase tracking-wide">
                                <i class="fa-solid fa-chart-line w-4 h-4 text-blue-600"></i>
                                Analyse &amp; Controlling
                            </h3>
                            <i class="fa-solid fa-chevron-up w-4 h-4 text-[#000000] transition-transform ${chevronCls}"></i>
                        </div>

                        <div class="${isPanelOpen ? '' : 'hidden'}">
                            <div class="p-6 space-y-8">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                                        <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                            <i class="fa-solid fa-chart-pie w-4 h-4 text-blue-600"></i>
                                            Split-Analyse
                                        </h5>

                                        <div class="mb-3 pb-3 border-b border-slate-100">
                                            <div class="flex justify-between text-xs font-semibold text-[#000000] mb-1">
                                                <span>Material &amp; Sonst.</span>
                                                <span>${money(matSales + otherSales)}</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-[#000000]">
                                                <span>Anteil am Umsatz: ${pct(matShare + otherShare)}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex justify-between text-xs font-semibold text-[#000000] mb-1">
                                                <span>Lohn &amp; Montage</span>
                                                <span>${money(laborSales)}</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-[#000000]">
                                                <span>Anteil am Umsatz: ${pct(laborShare)}</span>
                                            </div>
                                        </div>

                                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                            <div class="flex justify-between text-xs font-bold text-[#000000] bg-slate-50 p-1 rounded">
                                                <span>DB 1</span>
                                                <div class="text-right">
                                                    <div>${money(db1)}</div>
                                                    <div class="text-[9px] font-normal text-[#000000]">${pct(db1Pct)}</div>
                                                </div>
                                            </div>

                                            <div class="flex justify-between text-xs font-bold ${gainBoxClass} p-1 rounded border">
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
                                        <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                            <i class="fa-solid fa-clock w-4 h-4 text-orange-600"></i>
                                            Stunden-Performance
                                        </h5>

                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center bg-orange-50 p-2 rounded">
                                                <span class="text-xs text-[#000000]">Gesamtstunden</span>
                                                <span class="font-bold">${hours.toFixed(1)} h</span>
                                            </div>

                                            <div>
                                                <div class="flex justify-between text-xs text-[#000000] mb-1">
                                                    Umsatz pro Stunde (Netto)
                                                </div>
                                                <div class="flex justify-between items-baseline">
                                                    <span class="text-xs text-[#000000]">Ø Satz</span>
                                                    <span class="font-bold text-[#000000]">${money(salesPerHour)} /h</span>
                                                </div>
                                            </div>

                                            <div class="border-t border-slate-100 pt-2">
                                                <div class="flex justify-between text-xs text-green-600 mb-1 font-medium">
                                                    Reingewinn pro Stunde (DB3)
                                                </div>
                                                <div class="flex justify-between items-baseline">
                                                    <span class="text-xs text-[#000000]">Nach Risiko/Zins</span>
                                                    <span class="font-bold ${profitHourTextClass}">
                                                        ${money(profitPerHour)} /h
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 ring-1 ring-blue-100">
                                        <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                            <i class="fa-solid fa-money-bill-wave w-4 h-4 text-green-600"></i>
                                            Finanz-Dashboard
                                        </h5>

                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-[#000000] text-xs">Umsatz Netto</span>
                                                <span class="font-medium">${money(sales)}</span>
                                            </div>

                                            <div class="flex justify-between text-xs mt-1">
                                                <span class="text-[#000000]">./. EK Listenpreis</span>
                                                <span class="text-[#000000]">-${money(ekSum)}</span>
                                            </div>

                                            <div class="border-t border-slate-100 my-1 pt-1"></div>

                                            <div class="bg-slate-50 p-2 rounded border border-slate-100 mt-2">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="font-bold text-[#000000] text-xs uppercase">DB 3 (EBIT)</span>
                                                    <span class="font-bold ${isDb3Bad ? 'text-red-600' : 'text-green-600'}">
                                                        ${money(db3)}
                                                    </span>
                                                </div>

                                                <div class="border-t border-slate-200 pt-1 mt-1">
                                                    <div class="flex justify-between font-bold text-xs mt-1 text-blue-900">
                                                        <span>Netto-Gewinn</span>
                                                        <span>${money(netProfit)}</span>
                                                    </div>
                                                    <div class="flex justify-between text-[10px] text-[#000000] mt-1">
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
                                            <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-trend-up w-3 h-3"></i> Ertrags-Wasserfall
                                            </h6>
                                            <span class="text-[10px] text-blue-600">Umsatz/Profit</span>
                                        </div>

                                        <div class="flex items-end gap-2 h-20 w-full mt-2">
                                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                                <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="Umsatz: ${money(sales)}" style="height:${barHeight(sales)};"></div>
                                                <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Umsatz</span>
                                            </div>
                                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                                <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="Kosten (EK): ${money(ekSum)}" style="height:${barHeight(ekSum)};"></div>
                                                <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Kosten</span>
                                            </div>
                                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                                <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="DB1: ${money(db1)}" style="height:${barHeight(db1)};"></div>
                                                <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">DB1</span>
                                            </div>
                                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                                <div class="w-full rounded-t transition-all duration-500 ${db2BarClass}" title="DB2: ${money(db2)}" style="height:${barHeight(db2)};"></div>
                                                <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">DB2</span>
                                            </div>
                                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                                <div class="w-full rounded-t transition-all duration-500 ${netBarClass}" title="Netto: ${money(netProfit)}" style="height:${barHeight(netProfit)};"></div>
                                                <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Netto</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm">
                                        <div class="flex items-center justify-between mb-1">
                                            <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                                <i class="fa-solid fa-box-open w-3 h-3"></i> Umsatz-Mix
                                            </h6>
                                            <i class="fa-solid fa-bolt w-3 h-3 text-yellow-500"></i>
                                        </div>

                                        <div class="relative w-24 h-24 mx-auto mt-2">
                                            <svg viewBox="0 0 32 32" class="w-full h-full transform -rotate-90">
                                                <circle cx="16" cy="16" r="14" fill="transparent" stroke="#3b82f6" stroke-width="4" stroke-dasharray="${dashA} 100" stroke-dashoffset="0"></circle>
                                                <circle cx="16" cy="16" r="14" fill="transparent" stroke="#10b981" stroke-width="4" stroke-dasharray="${dashB} 100" stroke-dashoffset="-${dashA}"></circle>
                                                <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f59e0b" stroke-width="4" stroke-dasharray="${dashC} 100" stroke-dashoffset="-${dashA + dashB}"></circle>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                                <span class="text-[10px] font-bold text-[#000000]">${pct(matShare)}</span>
                                                <span class="text-[7px] text-[#000000] uppercase">Material</span>
                                            </div>
                                        </div>

                                        <div class="mt-3 space-y-1 text-[10px] text-[#000000]">
                                            <div class="flex justify-between"><span class="text-[#000000]">Material</span><span>${money(matSales)}</span></div>
                                            <div class="flex justify-between"><span class="text-[#000000]">Lohn</span><span>${money(laborSales)}</span></div>
                                            <div class="flex justify-between"><span class="text-[#000000]">Sonst.</span><span>${money(otherSales)}</span></div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                        <div class="flex items-center justify-between mb-4">
                                            <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                                <i class="fa-solid fa-bullseye w-3 h-3"></i> Margen-Monitor
                                            </h6>
                                            <div class="w-2 h-2 rounded-full animate-pulse ${isMarginBad ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                                        </div>

                                        <div class="flex-1 flex flex-col items-center justify-center">
                                            <div class="text-2xl font-bold text-[#000000]">${pct(marginTotalPct)}</div>
                                            <div class="text-[9px] text-[#000000] text-center uppercase tracking-tighter mt-1">
                                                Gesamtmarge vs. ${targetMargin.toFixed(0)}% Ziel
                                            </div>

                                            <div class="w-full bg-slate-200 h-1.5 rounded-full mt-4 overflow-hidden">
                                                <div class="h-full transition-all duration-700 ${marginBarClass}" style="width:${Math.round(clamp01(Math.abs(marginTotalPct) / Math.max(1, targetMargin)) * 100)}%;"></div>
                                            </div>

                                            <div class="mt-2 text-[10px] ${marginTextClass} font-bold">
                                                ${isMarginBad ? 'Unter' : 'Über'} Ziel um ${pct(Math.abs(marginVsTarget))}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                        <div class="flex items-center justify-between mb-2">
                                            <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                                <i class="fa-solid fa-chart-column w-3 h-3"></i> Effizienz-Index
                                            </h6>
                                        </div>

                                        <div class="space-y-3 mt-1">
                                            <div>
                                                <div class="flex justify-between text-[9px] mb-1">
                                                    <span class="text-[#000000] uppercase">Umsatz / h</span>
                                                    <span class="font-bold">${money(salesPerHour)}</span>
                                                </div>
                                                <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                                    <div class="bg-blue-400 h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="flex justify-between text-[9px] mb-1">
                                                    <span class="text-[#000000] uppercase">Gewinn / h</span>
                                                    <span class="font-bold ${profitHourTextClass}">${money(profitPerHour)}</span>
                                                </div>
                                                <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                                    <div class="${profitHourBarClass} h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                                </div>
                                            </div>

                                            <div class="pt-2 border-t border-slate-200 text-[10px] text-[#000000]">
                                                DB3: <b class=" ${isDb3Bad ? 'text-red-600' : 'text-[#000000]'}">${money(db3)}</b>
                                                <span class="text-slate-300 mx-1">•</span>
                                                Netto: <b class=" ${isProfitBad ? 'text-blue-600' : 'text-green-600'}">${money(netProfit)}</b>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                `;
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
                                    ${defs.map(c => `<div class="mat-head-cell ${c.align === 'right' ? 'justify-end text-right' : c.align === 'center' ? 'justify-center text-center' : ''}">${c.label}</div>`).join('')}
                                </div>
                            </div>
                            <div id="material-main-body" class="flex flex-col min-w-max pb-4 bg-[#c1c8cf]">
                `;

                const sectionEntries = App.ListView.getRenderableSectionEntries();

                if (!sectionEntries.length) {
                    html += `<div class="p-12 text-center text-[#000000] font-black">Keine Sektionen vorhanden.</div>`;
                } else {
                    sectionEntries.forEach((entry, visualSectionIdx) => {
                        const sec = entry.section;
                        const realSIdx = entry.stateIndex;
                        const isVirtual = !!entry.isVirtual;

                        const secQty = sec.config?.qty || 1;
                        const secUnit = sec.config?.unit || '';
                        const isSet = secUnit.toLowerCase() === 'set';
                        const multiplier = isSet ? secQty : 1;

                        html += App.ListView.sectionHeadHtml(sec, visualSectionIdx, isVirtual);

                        if (!App.ListView.isOpen(`sec:${visualSectionIdx}`, true)) return;

                        (sec.items || []).forEach((it, iIdx) => {
                            if (!it || it.active === false) return;

                            const hasTree = App.ListView.hasSubTree(it);
                            const mainKey = App.ListView.mainOpenKey(visualSectionIdx, iIdx);
                            const posStr = `${visualSectionIdx + 1}.${iIdx + 1}`;
                            const currentKind = it.kind || (it.item_type === 'labor' ? 'labor' : 'article');
                            const isNote = currentKind === 'note';
                            const hasLaborRows = currentKind === 'labor' && Array.isArray(it.labor_rows) && it.labor_rows.length > 0;
                            const isProtectedRow = isNote || hasLaborRows || currentKind === 'labor' || isVirtual;

                            html += App.ListView.rowHtml(it, realSIdx, iIdx, null, 0, posStr, defs, gridTemplate, { canToggle: hasTree, toggleKey: mainKey, isVirtualSection: isVirtual, multiplier: multiplier });

                            if (!isVirtual && !sec.isLocked && !isProtectedRow && realSIdx !== null) {
                                html += `
                                    <div class="mx-0 mt-1 mb-1 px-3 py-2 text-[13px] text-[#617b92]  rounded-3xl bg-[#c0d8ea] text-center transition-colors"
                                        ondragover="event.preventDefault(); this.classList.add('drag-over-sub')"
                                        ondragleave="this.classList.remove('drag-over-sub')"
                                        ondrop="event.preventDefault(); this.classList.remove('drag-over-sub'); if (App.getDragState()?.type === 'pos') { App.moveDraggedNode(App.dragState, { mode: 'to-sub', sIdx: ${realSIdx}, iIdx: ${iIdx}, depth: 1 }); App.clearDragMode(); } else if (App.isLibraryDrag()) { App.ListView.handleDropOnPosition(event, ${realSIdx}, ${iIdx}); }">
                                        <i class='fa-solid fa-level-down-alt mr-1'></i> Hier ablegen, um als Unterposition hinzuzufügen
                                    </div>
                                `;
                            }

                           if (!hasTree || App.ListView.isOpen(mainKey, true)) {
                                html += App.ListView.renderSubTree(it, realSIdx, iIdx, defs, gridTemplate, { 
                                    isVirtualSection: isVirtual, 
                                    multiplier: multiplier,
                                    parentQty: Number(it.qty || 1) // <--- Pass the parent's quantity down
                                });
                            }
                        });

                        if (!isVirtual && !sec.isLocked && realSIdx !== null) {
                            html += `
                                <div class="list-section-drop"
                                    ondragover="event.preventDefault(); this.classList.add('drag-over')"
                                    ondragleave="this.classList.remove('drag-over')"
                                    ondrop="event.preventDefault(); this.classList.remove('drag-over'); if (App.isLibraryDrag()) { App.ListView.handleDropOnSection(event, ${realSIdx}); return; } if (App.getDragState()?.type === 'pos') { App.moveDraggedNode(App.dragState, { mode: 'to-main', sIdx: ${realSIdx}, iIdx: (State.sections[${realSIdx}]?.items?.length || 0) }); App.clearDragMode(); }">
                                    Produkte / Sets hier ablegen oder Position hier als Hauptposition einfügen
                                </div>
                            `;
                        }
                    });
                }

                html += `
                                ${App.ListView.renderTotalsRow(defs, gridTemplate)}
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
        
        App.Navigation = {
        pendingAction: null,

        check(targetActionOrUrl) {
            if (State.hasUnsavedChanges) {
                this.pendingAction = targetActionOrUrl;
                document.getElementById('unsaved-changes-modal').classList.remove('hidden');
                
                // Discard handler: if they click "Verwerfen", clear flag and go
                document.getElementById('btn-confirm-leave').onclick = () => {
                    State.hasUnsavedChanges = false;
                    this.proceed();
                };
                return false;
            }
            return true;
        },

        // Specific function for the new Close button
        exitEditor() {
            const destination = '/admin/offers'; // CHANGE THIS to your actual dashboard URL
            if (this.check(destination)) {
                window.location.href = destination;
            }
        },

        proceed() {
            this.hide();
            if (typeof this.pendingAction === 'function') {
                this.pendingAction();
            } else if (typeof this.pendingAction === 'string') {
                window.location.href = this.pendingAction;
            }
        },

        cancel() {
            this.hide();
            this.pendingAction = null;
        },

        hide() {
            const modal = document.getElementById('unsaved-changes-modal');
            if (modal) modal.classList.add('hidden');
        }
    };


    // --- Clipboard 

    // Ensure we have the actual user ID for the websocket channel
        const currentUserId = {{ auth()->id() }}; 

        App.Clipboard = {
            isOpen: false,
            items: [],

            init: function() {
                // 1. Initial load
                this.loadFromServer(false);

                // 2. WebSocket Listener
                setTimeout(() => {
                    if (window.Echo) {
                        window.Echo.private(`user.clipboard.${window.currentUserId}`)
                            .listen('.clipboard.updated', (e) => {
                                console.log("Clipboard ping received! Fetching fresh data...");
                                // ✅ Fetch fresh data from server instead of relying on the websocket payload
                                this.loadFromServer(true);
                            });
                    }
                }, 1000);
            },

            // ✅ NEW HELPER METHOD: Grabs the data from the backend
            loadFromServer: function(shouldFlash = false) {
                fetch('/clipboard', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        this.items = data.items;
                        this.render();
                        if (shouldFlash) this.flashSidebar();
                    }
                })
                .catch(err => console.warn("Clipboard Load Error:", err));
            },

            toggle: function() {
                this.isOpen = !this.isOpen;
                const el = document.getElementById('clipboard-sidebar');
                if (el) {
                    if (this.isOpen) el.classList.remove('translate-x-full');
                    else el.classList.add('translate-x-full');
                }
            },

            flashSidebar: function() {
                const btn = document.querySelector('button[onclick="App.Clipboard.toggle()"]');
                if(!btn) return;
                btn.classList.add('bg-yellow-400', 'scale-110');
                setTimeout(() => btn.classList.remove('bg-yellow-400', 'scale-110'), 500);
            },

            // --- NEW: Bulk Copy ---
            copyBulk: function() {
                if (!State.selectedItems || State.selectedItems.size === 0) return;

                let itemsToCopy = [];
                
                // Loop through the selected items in the List View
                State.selectedItems.forEach(key => {
                    const parts = key.split(':');
                    const s = parseInt(parts[0]);
                    const i = parseInt(parts[1]);
                    const sub = parts[2] === 'null' ? null : parseInt(parts[2]);

                    let item = sub !== null 
                        ? State.sections[s]?.items[i]?.subItems[sub] 
                        : State.sections[s]?.items[i];

                    if (item) {
                        const clone = JSON.parse(JSON.stringify(item));
                        clone.id = 'copy_' + Date.now() + Math.random();
                        itemsToCopy.push(clone);
                    }
                });

                if (itemsToCopy.length === 0) return;

                // Wrap in a Bulk object
                const bulkPayload = {
                    is_bulk: true,
                    name: `${itemsToCopy.length} Positionen (Multi-Copy)`,
                    kind: 'bulk',
                    price: itemsToCopy.reduce((sum, it) => sum + (Number(it.price) || 0), 0),
                    items: itemsToCopy
                };

                this._sendCopyRequest(bulkPayload);
                
                // Deselect items after copying
                if(App.ListView) App.ListView.toggleSelectAll(false);
            },

            copyRow: function(sIdx, iIdx, subIdx = null) {
                let item = subIdx !== null && subIdx !== 'null' && subIdx !== undefined
                    ? State.sections[sIdx].items[iIdx].subItems[subIdx]
                    : State.sections[sIdx].items[iIdx];

                if(!item) return;

                const clone = JSON.parse(JSON.stringify(item));
                clone.id = 'copy_' + Date.now();

                this._sendCopyRequest(clone);
            },

            // Helper to send data to Laravel
            _sendCopyRequest: function(payload) {
                fetch('/clipboard/copy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ item: payload })
                }).then(r => r.json()).then(data => {
                    if(data.success) {
                        this.items = data.items;
                        this.render();
                        if(!this.isOpen) this.toggle();
                    }
                });
            },

            pasteItem: function(clipboardIndex, targetSIdx = -1) {
                if (!this.items || !this.items[clipboardIndex]) return;
                const clipboardEntry = JSON.parse(JSON.stringify(this.items[clipboardIndex]));
                
                // Find valid section if target is missing
                let sIdx = parseInt(targetSIdx, 10);
                if (isNaN(sIdx) || !State.sections[sIdx] || State.sections[sIdx]._virtualSection || State.sections[sIdx].isLocked) {
                    sIdx = State.sections.findIndex(s => s && !s._pageBreak && !s._virtualSection && !s.isLocked);
                    if (sIdx === -1) sIdx = App.addSection('Eingefügte Positionen', false);
                }

                const regenerateIds = (node) => {
                    node.id = 'pasted_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
                    if (Array.isArray(node.subItems)) node.subItems.forEach(regenerateIds);
                    if (Array.isArray(node.labor_rows)) node.labor_rows.forEach(l => l.id = Date.now() + Math.floor(Math.random() * 10000));
                };

                if (!Array.isArray(State.sections[sIdx].items)) State.sections[sIdx].items = [];

                // --- NEW: Handle Bulk Paste ---
                if (clipboardEntry.is_bulk && Array.isArray(clipboardEntry.items)) {
                    clipboardEntry.items.forEach(it => {
                        regenerateIds(it);
                        State.sections[sIdx].items.push(it);
                    });
                } else {
                    // Handle Single Paste
                    regenerateIds(clipboardEntry);
                    State.sections[sIdx].items.push(clipboardEntry);
                }
                
                App.renderQuotePage();
                if (App.Tabs.current === 'list') App.ListView.render();
                this.toggle(); 
            },

            clear: function() {
                fetch('/clipboard/clear', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                }).then(() => {
                    this.items = [];
                    this.render();
                });
            },

            render: function() {
                const container = document.getElementById('clipboard-items');
                const badge = document.getElementById('clipboard-badge'); // <-- Grab the badge

                // --- NEW: Update the Badge ---
                if (badge) {
                    if (this.items.length > 0) {
                        badge.innerText = this.items.length;
                        badge.classList.remove('hidden'); // Show badge
                    } else {
                        badge.classList.add('hidden'); // Hide badge if empty
                    }
                }
                // -----------------------------

                if(!container) return;

                if (this.items.length === 0) {
                    container.innerHTML = '<div class="text-xs text-[#000000] text-center py-4">Zwischenablage ist leer</div>';
                    return;
                }

                // Generate Dropdown options for all VALID sections
                let sectionOptions = State.sections.map((s, idx) => {
                    if(s && !s._pageBreak && !s._virtualSection && !s.isLocked) {
                        return `<option value="${idx}">${App.escapeHtml(s.title || 'Abschnitt '+(idx+1))}</option>`;
                    }
                    return '';
                }).join('');

                // Fallback if there are no valid sections (e.g., a brand new empty document)
                if (sectionOptions.trim() === '') {
                    sectionOptions = `<option value="-1">+ Neuer Abschnitt</option>`;
                }

                container.innerHTML = this.items.map((item, idx) => `
                    <div class="bg-white border border-slate-200 rounded p-2 shadow-sm relative group">
                        <div class="text-xs font-bold text-[#000000] truncate pr-6">
                            ${item.is_bulk ? '<i class="fa-solid fa-layer-group text-blue-500 mr-1"></i>' : ''}
                            ${App.escapeHtml(item.name || 'Unbekannt')}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 flex justify-between">
                            <span>${App.escapeHtml(item.article_no || item.kind || 'Artikel')}</span>
                            <span class="font-bold text-[#93c21c]">${Number(item.price || 0).toLocaleString('de-DE')} €</span>
                        </div>
                        <div class="mt-2 flex gap-1">
                            <select id="paste-sec-${idx}" class="text-[10px] border border-slate-200 rounded px-1 flex-1 outline-none truncate">
                                ${sectionOptions}
                            </select>
                            <button onclick="App.Clipboard.pasteItem(${idx}, document.getElementById('paste-sec-${idx}').value)" class="bg-[#f7fee7] text-[#6b8e12] border border-[#93c21c] px-2 py-1 rounded text-[10px] font-bold hover:bg-[#93c21c] hover:text-white transition">Einfügen</button>
                        </div>
                    </div>
                `).join('');
            }
        };
        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            App.Clipboard.init();
        });


// Also update the browser-level protection to be more reliable
App.Navigation = {
    pendingAction: null,

    // Prüft auf ungespeicherte Änderungen vor der Navigation
    check(targetAction) {
        if (State.hasUnsavedChanges) {
            this.pendingAction = targetAction;
            document.getElementById('unsaved-changes-modal').classList.remove('hidden');
            
            // Handler für "Änderungen verwerfen" im Modal
            const discardBtn = document.getElementById('btn-confirm-leave') || document.getElementById('btn-nav-discard');
            if (discardBtn) {
                discardBtn.onclick = () => {
                    State.hasUnsavedChanges = false;
                    this.proceed();
                };
            }
            return false;
        }
        return true;
    },

    // Diese Funktion hat in deinem Code gefehlt!
    exitEditor() {
        const destination = '/admin/offers'; // Hier die Ziel-URL deiner Übersicht eintragen
        if (this.check(destination)) {
            window.location.href = destination;
        }
    },

    proceed() {
        this.hide();
        if (typeof this.pendingAction === 'function') {
            this.pendingAction();
        } else if (typeof this.pendingAction === 'string') {
            window.location.href = this.pendingAction;
        }
    },

    cancel() {
        this.hide();
        this.pendingAction = null;
    },

    hide() {
        const modal = document.getElementById('unsaved-changes-modal');
        if (modal) modal.classList.add('hidden');
    }
};

// Update your existing loadBackWizard function to use the new modal
App.loadBackWizard = () => {
    const navigateBack = () => {
        App.switchView('start');
        const wizTpl = document.getElementById('wiz-template-mode');
        if(wizTpl) wizTpl.checked = false;
    };

    if (App.Navigation.check(navigateBack)) {
        navigateBack();
    }
};

// Handle Browser Exit / Tab Close (Native Prompt)
window.addEventListener('beforeunload', (e) => {
    if (State.hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = ''; // Triggers the standard browser box
    }
});


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
            // 1. Extract parameters safely from State
            const { 
                offer_id: offerId, 
                offer_folder_id: folderId, 
                load_snapshot: loadSnapshot 
            } = State.prefill || {};

            if (!offerId && !folderId) return;

            try {
                // 2. Build URL with query parameters
                const url = new URL('/offers/document/load', window.location.origin);
                if (offerId) url.searchParams.set('offer_id', offerId);
                if (folderId) url.searchParams.set('offer_folder_id', folderId);
                if (loadSnapshot) url.searchParams.set('load_snapshot', '1');  

                if (State.prefill?.load_snapshot) {
                    url.searchParams.set('load_snapshot', '1');
                }
                // 3. Fetch Data
                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

                const json = await res.json();
                if (!json.success || !json.found || !json.data) return;

                const data = json.data;

                State.docStatus = String(data.document_status || data.status || data.documentStatus || '').trim().toLowerCase();
                State.isSnapshot = data.is_snapshot === true;
                // 4. Update Global State
                State.loadedSavedDetail = true;
                State.sections = Array.isArray(data.sections) ? data.sections : [];
                State.placedImages = Array.isArray(data.placed_images) ? data.placed_images : [];
                State.sections.forEach(sec => {
                    (sec.items || []).forEach(it => {
                        if (it.kind === 'labor' && it.unit === 'Pers.') {
                            it.unit = 'Stk';
                            it.measure = 'Stk';
                            it.price_unit_label = 'Stk';
                        }
                        (it.subItems || []).forEach(sub => {
                            if (sub.kind === 'labor' && sub.unit === 'Pers.') {
                                sub.unit = 'Stk';
                                sub.measure = 'Stk';
                                sub.price_unit_label = 'Stk';
                            }
                        });
                    });
                });
                State.brandColor = data.brand_color || '#93c21c';
                State.brandMode = data.brand_mode || 'text';
                State.brandLogoUrl = data.brand_logo_url || '';
                State.companyName = data.company_name || 'SOLAR ASPEKT';
                State.taxRate = Number(data.tax_rate || 19);
                State.coverTextHtml = data.cover_text_html || data.cover_text || '';  
                State.mainTitleHtml = data.main_title || data.main_title_html || ''; 

                // Helper for safe DOM updates
                const updateEl = (id, prop, value) => {
                    const el = document.getElementById(id);
                    if (el) el[prop] = value;
                };
 
               // 5. Update Document ID (AUF-XXX vs Offer No vs Offer ID)
                if (data.order_number && !State.isSnapshot) {
                    // ✅ Live deal: Use Order Number (AUF-XXX)
                    State.offerId = String(data.order_number);
                } else if (data.offer_no) {
                    // ✅ Offer/Snapshot: Prefer offer_no (SA-AGXXXX)
                    State.offerId = String(data.offer_no);
                } else if (data.offer_id) {
                    // ✅ Fallback: Just use the raw offer_id
                    State.offerId = String(data.offer_id);
                }

                // Update the input field in the DOM
                updateEl('doc-offer-id', 'value', State.offerId);

                // 6. Update Customer Information
                if (data.offer?.customer) {
                    const cust = data.offer.customer;
                    State.customer = cust;
                    State.custId = cust.customer_no || '-';

                    updateEl('doc-cust-name', 'innerText', cust.display_name || cust.name || '');
                    updateEl('doc-cust-addr', 'innerHTML', `${cust.street || ''}<br>${cust.postcode || ''} ${cust.city || ''}`);
                    updateEl('doc-cust-id', 'value', State.custId);
                }

                // 7. Update Cover Text & Branding Inputs
                if (State.coverTextHtml) {
                    updateEl('doc-cover-text', 'innerHTML', State.coverTextHtml);
                }

                if (State.mainTitleHtml) {
                    updateEl('doc-main-title', 'innerHTML', State.mainTitleHtml);
                }
                updateEl('wiz-brand-color', 'value', State.brandColor);
                updateEl('wiz-brand-name', 'value', State.companyName);
                if (State.brandLogoUrl) {
                    updateEl('wiz-brand-logo', 'value', State.brandLogoUrl);
                }

                const modeRadio = document.querySelector(`input[name="wiz-brand-mode"][value="${State.brandMode}"]`);
                if (modeRadio) modeRadio.checked = true;

                // 8. Trigger dependent UI updates
                App.updateBranding();
                App.startQuote();
                App.startPresenceTracking();
                App.applyLockState();

            } catch (error) {
                console.error('loadSavedDocumentIfAvailable failed:', error);
            }
        };

 
    App.captureListUiState = function () {
        const panel = document.getElementById('panel-list');
        const root = document.getElementById('listview-root');
        const gridScroll = root?.querySelector('.material-grid-scroll');
        const active = document.activeElement;

        const state = {
            panelScrollTop: panel ? panel.scrollTop : 0,
            panelScrollLeft: panel ? panel.scrollLeft : 0,
            gridScrollTop: gridScroll ? gridScroll.scrollTop : 0,
            gridScrollLeft: gridScroll ? gridScroll.scrollLeft : 0,
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
            const gridScroll = root?.querySelector('.material-grid-scroll');

            // Restore outer panel scroll
            if (panel) {
                panel.scrollTop = state.panelScrollTop || 0;
                panel.scrollLeft = state.panelScrollLeft || 0;
            }

            // Restore inner table scroll
            if (gridScroll) {
                gridScroll.scrollTop = state.gridScrollTop || 0;
                gridScroll.scrollLeft = state.gridScrollLeft || 0;
            }

            // Restore input focus and cursor placement
            if (state.focusKey) {
                const el = root?.querySelector(`[data-lv-focus="${CSS.escape(state.focusKey)}"]`);
                if (el) {
                    el.focus({ preventScroll: true });
                    if (
                        typeof el.setSelectionRange === 'function' &&
                        state.selectionStart !== null &&
                        state.selectionEnd !== null
                    ) {
                        try { el.setSelectionRange(state.selectionStart, state.selectionEnd); } catch (e) {}
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

    // --- UI STATE & ANIMATION HELPERS ---

    // 1. Captures what is currently open and focused in the right sidebar
    App.captureSidebarUiState = function () {
        const c = document.getElementById('calc-sidebar-content');
        if (!c) return null;
        
        const state = {
            scrollTop: c.scrollTop,
            // Find all <details> that are open and have an ID starting with sb-
            openDetails: Array.from(c.querySelectorAll('details[open]')).map(d => d.id).filter(id => id && id.startsWith('sb-')),
            focusKey: null,
            selectionStart: null,
            selectionEnd: null
        };
        
        // Check if an input is currently focused
        const active = document.activeElement;
        if (active && c.contains(active)) {
            state.focusKey = active.getAttribute('data-sb-focus') || null;
            if (active.tagName === 'INPUT' && (active.type === 'text' || active.type === 'number')) {
                try {
                    state.selectionStart = active.selectionStart;
                    state.selectionEnd = active.selectionEnd;
                } catch(e) {}
            }
        }
        return state;
    };

    // 2. Restores the sidebar state after a recalculation render
    App.restoreSidebarUiState = function (state) {
        if (!state) return;
        requestAnimationFrame(() => {
            const c = document.getElementById('calc-sidebar-content');
            if (!c) return;
            
            c.scrollTop = state.scrollTop || 0;
            
            // Re-open the accordions
            if (state.openDetails && state.openDetails.length > 0) {
                state.openDetails.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.setAttribute('open', 'true');
                });
            }
            
            // Put the cursor back exactly where the user was typing
            if (state.focusKey) {
                const el = c.querySelector(`[data-sb-focus="${CSS.escape(state.focusKey)}"]`);
                if (el) {
                    el.focus({ preventScroll: true });
                    try {
                        if (state.selectionStart !== null) {
                            el.setSelectionRange(state.selectionStart, state.selectionEnd);
                        }
                    } catch(e) {}
                }
            }
        });
    };

    // 3. Flashes the corresponding row in the List View
    App.flashListViewRow = function(sIdx, iIdx, subIdx) {
        if (App.Tabs.current !== 'list') return;
        
        const subArg = (subIdx === null || subIdx === undefined || subIdx === 'null') ? 'null' : subIdx;
        const rowId = `lv-row-${sIdx}-${iIdx}-${subArg}`;
        
        // Delay slightly to ensure List View has finished rendering
        requestAnimationFrame(() => {
            setTimeout(() => {
                const row = document.getElementById(rowId);
                if (row) {
                    const originalBg = row.style.backgroundColor || '';
                    const originalTransition = row.style.transition || '';
                    
                    row.style.transition = 'background-color 0.3s ease';
                    row.style.backgroundColor = '#d9f99d'; // Flash lime green
                    
                    setTimeout(() => {
                        row.style.backgroundColor = originalBg;
                        setTimeout(() => row.style.transition = originalTransition, 300);
                    }, 800);
                }
            }, 50);
        });
    };

    App.updatePosPriceCalc = function(sIdx, iIdx, subIdx, field, val) {
        let target = subIdx !== null
            ? State.sections[sIdx].items[iIdx].subItems[subIdx]
            : State.sections[sIdx].items[iIdx];
        
        // 1. Handle switching between % and €
        if (field === 'marginType') {
            target.marginType = val;
            let currentEk = parseFloat(target.purchase_price || target.ek) || 0;
            let currentVk = parseFloat(target.price) || 0;
            
            if (val === 'percent') {
                target.margin = currentEk > 0 ? ((currentVk - currentEk) / currentEk) * 100 : 100;
                target.marginPercent = target.margin;
            } else {
                target.margin = currentVk - currentEk;
            }
            App.renderQuotePage();
            App.flashListViewRow(sIdx, iIdx, subIdx);
            return;
        }

        // 2. Handle numeric input changes
        val = parseFloat(val) || 0;
        let ek = parseFloat(target.purchase_price || target.ek) || 0;
        let mType = target.marginType || 'percent';

        if (field === 'ek') {
            target.ek = val;
            target.purchase_price = val;
            let currentMargin = parseFloat(target.margin) || 0;
            
            if (mType === 'percent') {
                target.price = val * (1 + currentMargin / 100);
            } else {
                target.price = val + currentMargin;
            }
        }
        else if (field === 'margin' || field === 'marginPercent') {
            target.margin = val;
            
            if (mType === 'percent') {
                target.marginPercent = val;
                target.price = ek * (1 + val / 100);
            } else {
                target.price = ek + val;
                target.marginPercent = ek > 0 ? (val / ek) * 100 : 100; // Keep percent synced for warnings
            }
        }
        else if (field === 'price') {
            target.price = val;
            
            if (mType === 'percent') {
                target.margin = ek > 0 ? ((val - ek) / ek) * 100 : 100;
                target.marginPercent = target.margin;
            } else {
                target.margin = val - ek;
                target.marginPercent = ek > 0 ? ((val - ek) / ek) * 100 : 100;
            }
        }

        // 3. Warning for Minimum Profit (Checks the % regardless of mode)
        let checkPct = parseFloat(target.marginPercent) || 0;
        if ((field === 'margin' || field === 'marginPercent' || field === 'price') && checkPct < (State.config.minProfit || 10)) {
             App.toastConfirmShow({
                 title: 'Achtung: Marge zu niedrig!',
                 message: `Diese Eingabe führt zu einer Marge von ${checkPct.toFixed(1)}%, was unter dem Limit von ${State.config.minProfit}% liegt.`,
                 okText: 'Verstanden',
                 cancelText: ''
             });
             const cancelBtn = document.getElementById('toast-confirm-cancel');
             if (cancelBtn) cancelBtn.style.display = 'none';
        }

        // 4. Update Parent Totals
        if (subIdx !== null) {
            App.syncParentTotals(sIdx, iIdx);
        }
        
        // 5. Render & Trigger visual flash animation in the List View
        App.renderQuotePage();
        App.flashListViewRow(sIdx, iIdx, subIdx);
    };

    // --- GENERAL MARGIN DISTRIBUTION LOGIC --- 

    App.toggleParentMarginLock = function(sIdx, iIdx) {
        State.unlockedParentMargins = State.unlockedParentMargins || {};
        const key = `${sIdx}-${iIdx}`;
        const isUnlocking = !State.unlockedParentMargins[key];
        
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (mainItem && isUnlocking) {
            // Speichere die Parent-Marge für die Anzeige im Button
            mainItem._prevMargin = mainItem.marginPercent;
            
            // Mache ein exaktes Backup der Kinder, bevor sie überschrieben werden
            if (Array.isArray(mainItem.subItems)) {
                mainItem.subItems.forEach(sub => {
                    if (!sub._prevMarginState) {
                        sub._prevMarginState = {
                            marginPercent: sub.marginPercent,
                            margin: sub.margin,
                            marginType: sub.marginType,
                            price: sub.price
                        };
                    }
                });
            }
        }
        
        State.unlockedParentMargins[key] = isUnlocking;
        App.ListView.render();
    };

    App.ListView.toggleParentVkLock = function(sIdx, iIdx) {
        State.unlockedParentVk = State.unlockedParentVk || {};
        const key = `${sIdx}-${iIdx}`;
        const isUnlocking = !State.unlockedParentVk[key];
        
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (mainItem && isUnlocking) {
            // Speichere den Parent-VK für die Anzeige im Button
            mainItem._prevVk = mainItem.price;
            
            // Mache ein exaktes Backup der Kinder
            if (Array.isArray(mainItem.subItems)) {
                mainItem.subItems.forEach(sub => {
                    if (!sub._prevMarginState) {
                        sub._prevMarginState = {
                            marginPercent: sub.marginPercent,
                            margin: sub.margin,
                            marginType: sub.marginType,
                            price: sub.price
                        };
                    }
                });
            }
        }
        
        State.unlockedParentVk[key] = isUnlocking;
        App.ListView.render();
    };

    App.updateParentMarginType = function(sIdx, iIdx, type) {
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (!mainItem) return;
        mainItem.marginType = type;
        App.ListView.render();
    };

    App.applyGeneralMargin = function(sIdx, iIdx, marginValue) {
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (!mainItem || !Array.isArray(mainItem.subItems)) return;

        const val = parseFloat(marginValue) || 0;
        const marginType = mainItem.marginType || 'percent';
        mainItem.marginType = marginType;

        if (marginType === 'percent') {
            mainItem.subItems.forEach(sub => {
                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                    sub.marginType = 'percent';
                    sub.marginPercent = val;
                    sub.margin = val;
                    const ek = Number(sub.purchase_price || sub.ek || 0);
                    sub.price = App.vkFromEkMargin(ek, val);
                }
            });
        } else {
            let totalEkSum = 0;
            mainItem.subItems.forEach(sub => {
                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                    totalEkSum += App.calcItemCost(sub);
                }
            });

            mainItem.subItems.forEach(sub => {
                if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                    const subEkTotal = App.calcItemCost(sub);
                    const ratio = totalEkSum > 0 ? (subEkTotal / totalEkSum) : (1 / mainItem.subItems.length);
                    const allocatedTotalMargin = val * ratio;
                    const qty = Number(sub.qty || 1);
                    const baseQty = Number(sub.price_unit_value || 1);
                    const marginPerUnit = qty > 0 ? (allocatedTotalMargin * baseQty) / qty : 0;

                    sub.marginType = 'fixed';
                    sub.margin = marginPerUnit;
                    const ek = Number(sub.purchase_price || sub.ek || 0);
                    sub.price = ek + marginPerUnit;
                    sub.marginPercent = ek > 0 ? (marginPerUnit / ek) * 100 : 100;
                }
            });
        }

        App.syncParentTotals(sIdx, iIdx);
        App.renderQuotePage();
        State.unlockedParentMargins[`${sIdx}-${iIdx}`] = false;
    };

    App.resetGeneralMargin = function(sIdx, iIdx) {
        const mainItem = State.sections[sIdx]?.items[iIdx];
        if (!mainItem || !Array.isArray(mainItem.subItems)) return;

        // Stelle den exakten vorherigen Zustand wieder her
        mainItem.subItems.forEach(sub => {
            if (sub._prevMarginState) {
                sub.marginType = sub._prevMarginState.marginType;
                sub.marginPercent = sub._prevMarginState.marginPercent;
                sub.margin = sub._prevMarginState.margin;
                sub.price = sub._prevMarginState.price;
                delete sub._prevMarginState;
            }
        });

        delete mainItem._prevMargin;
        delete mainItem._prevVk;

        App.syncParentTotals(sIdx, iIdx);
        App.renderQuotePage();
        
        State.unlockedParentMargins[`${sIdx}-${iIdx}`] = false;
        State.unlockedParentVk[`${sIdx}-${iIdx}`] = false;
        
        if (App.Tabs.current === 'list') App.ListView.render();
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
                            <span class="block mb-1 text-[#000000]">Umsatzsteuer-Modus:</span>
                            <div class="flex gap-1">
                                <button onclick="App.Settings.update('vatMode', 0)" class="flex-1 py-1 rounded text-[10px] ${c.vatMode === 0 ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-300'}">0% (PV)</button>
                                <button onclick="App.Settings.update('vatMode', 19)" class="flex-1 py-1 rounded text-[10px] ${c.vatMode === 19 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300'}">19% (Std)</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center"><span>Gemeinkosten</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.overhead}" onchange="App.Settings.update('overhead', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Vertriebs-Provision</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.commission}" onchange="App.Settings.update('commission', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-2 mt-2"><span>Mindestgewinn</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.minProfit}" onchange="App.Settings.update('minProfit', this.value)"> %</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-indigo-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-percent"></i> Standard-Margen</h4>
                    <div class="space-y-2 text-xs">
                        <p class="text-[10px] text-[#000000] mb-2 italic">Standard-Vorgaben für neue Positionen.</p>
                        <div class="flex justify-between items-center"><span>Material</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.margins?.material || 20}" onchange="App.Settings.update('marginMaterial', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Lohn / Montage</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.margins?.labor || 50}" onchange="App.Settings.update('marginLabor', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Fremdleistung</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.margins?.external || 15}" onchange="App.Settings.update('marginExternal', this.value)"> %</div>
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
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.logistics?.freight?.val || 0}" onchange="App.Settings.update('freightVal', this.value)"> €</div>
                        </div>
                        <div class="flex justify-between items-center"><span class="flex items-center gap-1">Fahrzeugpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.vehicle?.active ? 'checked' : ''} onchange="App.Settings.update('vehicleActive', this.checked)"></span>
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.logistics?.vehicle?.val || 0}" onchange="App.Settings.update('vehicleVal', this.value)"> €</div>
                        </div>
                        <div class="flex justify-between items-center"><span class="flex items-center gap-1">Maschinenpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.machine?.active ? 'checked' : ''} onchange="App.Settings.update('machineActive', this.checked)"></span>
                            <div class="flex items-center gap-1"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.logistics?.machine?.val || 0}" onchange="App.Settings.update('machineVal', this.value)"> €</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-red-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i> Risiko & Wagnis</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center"><span>Kalk. Wagnis</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.risk}" onchange="App.Settings.update('risk', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center"><span>Vorfinanzierung (Zins)</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.finance}" onchange="App.Settings.update('finance', this.value)"> %</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-blue-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-landmark"></i> Steuern & Kunde</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center"><span>Kalk. Ertragssteuer</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.tax}" onchange="App.Settings.update('tax', this.value)"> %</div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-700 mt-1"><span>Kunden-Skonto</span>
                            <div class="flex items-center"><input type="number" class="w-14 bg-white border border-slate-300 rounded px-2 py-1 text-right text-[#000000]" step="any" value="${c.custDiscount}" onchange="App.Settings.update('custDiscount', this.value)"> %</div>
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

        if (App.isLockedSnapshot()) {
            alert("You are in an Angebot snapshot (Status: Auftrag). You cannot change it.");
            return;
        } 
        // 1. Find the first valid section (ignoring page breaks)
        let sectionIndex = State.sections.findIndex(s => s && !s._pageBreak);
        let isNewSection = false;

        // 2. If no valid section exists, create a new one
        if (sectionIndex === -1) {
            sectionIndex = App.addSection();
            isNewSection = true;
        }

        const targetSection = State.sections[sectionIndex];

        // 3. Ensure the items array exists
        if (!Array.isArray(targetSection.items)) {
            targetSection.items = [];
        }

        // 4. Handle Biography/History logging safely (fixes the 't is undefined' bug)
        if (App.Bio) {
            const sectionTitle = targetSection.title || 'Abschnitt';
            if (isNewSection) {
                App.Bio.addEntry('Abschnitt', `Sektion "${sectionTitle}" erstellt.`);
            } else {
                App.Bio.addEntry('Neue Position', `Schnell-Position zu "${sectionTitle}" hinzugefügt.`);
            }
        }

        // 5. Get default margin
        const defaultMargin = App.getDefaultMargin('article');

        // 6. Build the new position object
        const newItem = {
            name: 'Neue Position',
            desc: '',
            desc_html: '',
            price: 0,
            ek: 0,
            purchase_price: 0,
            marginPercent: defaultMargin,
            margin: defaultMargin,
            qty: 1,
            unit: 'Stk.',
            measure: 'Stk.',
            price_unit_value: 1,
            price_unit_label: 'Stk.',
            price_unit_text: '1 Stk.',
            kind: 'article',
            status: 'normal',
            active: true,         // Ensure it's visible
            print_hidden: false,   // Default print visibility
            subItems: []
        };

        // 7. Add the item to the section
        targetSection.items.push(newItem);

        // 8. Re-render the UI to show the new item
        App.renderQuotePage();
    };

   App.insertPositionAfter = function(sIdx, iIdx, subIdx) {

        if (App.isLockedSnapshot()) {
            alert("You are in an Angebot snapshot (Status: Auftrag). You cannot change it.");
            return;
        }
        const defaultMargin = App.getDefaultMargin('article');
        
        // 1. DEFINE isSub HERE
        const isSub = (subIdx !== null && subIdx !== 'null' && subIdx !== undefined);

        const newItem = {
            name: 'Neue Position',
            desc: '',
            desc_html: '',
            price: 0,
            ek: 0,
            purchase_price: 0,
            marginPercent: defaultMargin,
            margin: defaultMargin,
            qty: 1,
            unit: 'Stk.',
            measure: 'Stk.',
            price_unit_value: 1,
            price_unit_label: 'Stk.',
            price_unit_text: '1 Stk.',
            kind: 'article',
            status: 'normal',
            subItems: [],
            active: true,
            hideImage: true,
            print_hidden: isSub // Now it knows what isSub means!
        };

        // 2. Use the variable you just created for the if-condition
        // If subIdx is provided, insert as a sub-position
        if (isSub) {
            State.sections[sIdx].items[iIdx].subItems.splice(Number(subIdx) + 1, 0, newItem);
            App.syncParentTotals(sIdx, iIdx);
        } else {
            // Otherwise insert as a main position
            State.sections[sIdx].items.splice(Number(iIdx) + 1, 0, newItem);
        } 
        App.renderQuotePage();
    };

   const _origRenderQuotePage = App.renderQuotePage.bind(App);
    App.renderQuotePage = function (forPrint = false) {
        // ADD THIS LINE: If it's not a print preview, mark as unsaved
        if (!forPrint) State.hasUnsavedChanges = true;
        
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

 
    App.computeSectionSummary = function (forPrint = false) {
        const sections = [];
        let subtotal = 0;

        (App.getRenderableSections(forPrint) || []).forEach((sec, sIdx) => {
            if (!sec || sec._pageBreak) return;

            let sectionNet = 0;
            const secQty = sec.config?.qty || 1;
            const isSet = (sec.config?.unit || '').toLowerCase() === 'set';
            const multiplier = isSet ? secQty : 1;

            (sec.items || []).forEach((it) => {
                if (!it || it.active === false) return;

                const lineType = it.lineType || (
                    it.status === 'optional' ? 'optional' :
                    it.status === 'alternative' ? 'alternative' :
                    'standard'
                );

                if (lineType !== 'standard') return;
                if ((it.kind || '') === 'note') return;

                let itemGross = 0;
                if (it.subItems && it.subItems.length > 0 && !it.isPauschal) {
                    it.subItems.forEach(sub => {
                        if (sub.active !== false && (sub.status || 'normal') === 'normal') {
                            itemGross += App.calcItemGross(sub);
                        }
                    });
                    // FIX: Replaced the undefined 'num()' with standard 'Number()'
                    itemGross *= Number(it.qty || 1);
                } else {
                    itemGross = App.calcItemGross(it);
                }

                sectionNet += itemGross;
            });

            // Multiply the final block total
            sectionNet *= multiplier;

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

        return { sections, subtotal, netTotal: subtotal, vatRate, vatValue, gross };
    };
    
    App.renderOfferFooterBlocks = function () {
        return [];
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
                    <span class="font-bold text-[#000000]">${App.escapeHtml(sec.label)}</span>
                </div>
                <span class=" font-bold text-[#000000]">${App.money(sec.net)} €</span>
            </div>
        `).join('');

        const box = document.createElement('div');
        box.id = 'list-section-summary-block';
        box.className = 'p-4 border-t border-slate-200 bg-white';
        box.innerHTML = `
            <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-white">
                    <div class="text-xs font-black text-[#000000] uppercase tracking-wider">Zusammenstellung</div>
                    <div class="text-lg font-black text-[#000000]">Abschnitte</div>
                </div>

                <div class="p-4">
                    ${rowHtml || `<div class="text-[#000000]">Keine Abschnitte vorhanden</div>`}

                    <div class="mt-4 pt-4 border-t-2 border-slate-800 space-y-2">
                        <div class="flex justify-between font-black text-slate-900">
                            <span>Summe Zusammenstellung Abschnitte</span>
                            <span class="">${App.money(sum.subtotal)} €</span>
                        </div>
                        <div class="flex justify-between text-[#000000]">
                            <span>Nettogesamtpreis</span>
                            <span class="">${App.money(sum.netTotal)} €</span>
                        </div>
                        <div class="flex justify-between text-[#000000]">
                            <span>Umsatzsteuer ${sum.vatRate.toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%</span>
                            <span class="">${App.money(sum.vatValue)} €</span>
                        </div>
                        <div class="flex justify-between font-black text-lg text-slate-900 border-t-2 border-slate-900 pt-2 mt-2">
                            <span>Gesamtsumme</span>
                            <span class="">${App.money(sum.gross)} €</span>
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
        const m = Number(marginPercent || 0) / 100;
        if (!EK) return 0;
        return EK * (1 + m);
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

   App.pickVK = (p, ek=0) => {
        const candidates = [
            p?.vk, p?.sale_price, p?.selling_price, p?.unit_price, p?.unitPrice, p?.price, p?.best_price
        ];
        for (const c of candidates) {
            const n = App.num(c, NaN);
            if (Number.isFinite(n) && n > 0) return n;
        }
        return 0;
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
            hideImage: false,
            hideNumbering: false,
            isPauschal: false,
            print_hidden: false,

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

    App.getRenderableSections = function (forPrint = false) {
        const baseSections = (State.sections || []).filter(sec => sec && !sec._pageBreak);
        const logisticsItems = App.getActiveLogisticsItems();

        let sectionsToReturn = baseSections;

        if (logisticsItems.length) {
            sectionsToReturn = [
                ...baseSections,
                {
                    _virtualSection: true,
                    id: '__logistics__',
                    title: 'Logistik & Baustelle',
                    description: 'Automatisch aus Einstellungen übernommen',
                    config: { mode: 'standard', pauschalPrice: 0, type: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' } },
                    items: logisticsItems,
                    isLocked: true
                }
            ];
        }

        // --- EXTRACT LABOR ONLY FOR PRINT ---
        if (forPrint) {
            // Deep clone so we don't destroy the actual editor state
            sectionsToReturn = JSON.parse(JSON.stringify(sectionsToReturn));
            let allLaborItems = [];

            sectionsToReturn.forEach(sec => {
                if (sec.id === '__logistics__') return; // Skip logistics

                let nonLaborItems = [];
                (sec.items || []).forEach(it => {
                    if (!it) return;

                    // If it is a main labor position, extract it completely
                    if (it.kind === 'labor') {
                        it.depth = 0; // ensure it renders as a main item
                        allLaborItems.push(it);
                    } else {
                        // If it's an article, check its sub-items for labor
                        let nonLaborSubs = [];
                        (it.subItems || []).forEach(sub => {
                            if (!sub) return;
                            if (sub.kind === 'labor') {
                                sub.depth = 0; // promote to main item level for the summary
                                allLaborItems.push(sub);
                            } else {
                                nonLaborSubs.push(sub);
                            }
                        });
                        
                        // Replace subItems with only the non-labor ones.
                        // (The processItems loop will automatically recalculate the new lower price)
                        it.subItems = nonLaborSubs;
                        nonLaborItems.push(it);
                    }
                });
                sec.items = nonLaborItems;
            });

            // Append the consolidated Labor section at the end of the document
            if (allLaborItems.length > 0) {
                sectionsToReturn.push({
                    _virtualSection: true,
                    id: '__global_labor__',
                    title: 'Montage & Dienstleistungen',
                    description: 'Zusammenfassung aller Arbeitsleistungen',
                    config: { mode: 'standard', pauschalPrice: 0, type: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' } },
                    items: allLaborItems,
                    isLocked: true
                });
            }
        }

        return sectionsToReturn;
    };


    App.computeQuoteTotals = function (forPrint = false) {
        // Safe number parser
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

        // Helper to check if an item is labor-related
        const isLabor = (item) => {
            const kind = item.kind || (item.item_type === 'labor' ? 'labor' : 'article');
            return kind === 'labor' || item.unit === 'Std' || item.unit === 'h';
        };

     (App.getRenderableSections(forPrint) || []).forEach(sec => {
            if (!sec || sec._pageBreak) return;

            // NEW: Calculate multiplier
            const secQty = sec.config?.qty || 1;
            const isSet = (sec.config?.unit || '').toLowerCase() === 'set';
            const multiplier = isSet ? secQty : 1;

            (sec.items || []).forEach(it => {
                if (!it || it.active === false || (it.status || 'normal') !== 'normal' || (it.kind || 'article') === 'note') {
                    return;
                }

                let lineVK = 0;
                let lineEK = 0;
                let lineLaborSales = 0;
                let lineMatSales = 0;
                let lineHours = 0;

                if (Array.isArray(it.subItems) && it.subItems.length > 0 && !it.isPauschal) {
                    it.subItems.forEach(sub => {
                        if (!sub || sub.active === false || (sub.status || 'normal') !== 'normal') return;

                        const subVK = App.calcItemGross(sub);
                        const subEK = App.calcItemCost(sub);

                        lineVK += subVK;
                        lineEK += subEK;

                        if (isLabor(sub)) {
                            lineHours += num(sub.qty);
                            lineLaborSales += subVK;
                        } else {
                            lineMatSales += subVK;
                        }
                    });
                    
                    // FIX: Multiply the aggregated sub-item totals by the main item's quantity
                    const mainQty = num(it.qty, 1);
                    lineVK *= mainQty;
                    lineEK *= mainQty;
                    lineLaborSales *= mainQty;
                    lineMatSales *= mainQty;
                    lineHours *= mainQty;
                    
                } else {
                    const qty = it.isPauschal ? 1 : num(it.qty, 1);
                    const vk = num(it.price);
                    const ek = num(it.ek);

                    lineVK = it.isPauschal ? vk : (vk * qty);
                    lineEK = it.isPauschal ? ek : (ek * qty);

                    if (isLabor(it)) {
                        lineHours += qty;
                        lineLaborSales += lineVK;
                    } else {
                        lineMatSales += lineVK;
                    }
                }

                // APPLY MULTIPLIER to all global metrics
                salesNet += (lineVK * multiplier);
                sumEK += (lineEK * multiplier);
                sumLaborSales += (lineLaborSales * multiplier);
                sumMatSales += (lineMatSales * multiplier);
                totalHours += (lineHours * multiplier);
            });
        });

        // --- Financial & Controlling Math ---
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

        // --- KPI Percentages & Ratios ---
        const safeDiv = (val, divisor) => (divisor > 0 ? (val / divisor) : 0);

        const laborShare = safeDiv(sumLaborSales, salesNet) * 100;
        const matShare = safeDiv(sumMatSales, salesNet) * 100;
        const db1Pct = safeDiv(db1, salesNet) * 100;
        const db2Pct = safeDiv(db2, salesNet) * 100;
        const profitPct = safeDiv(netProfit, salesNet) * 100;
        const salesPerHour = safeDiv(salesNet, totalHours);
        const profitPerHour = safeDiv(db3, totalHours);

        const totalGlobalCosts = overheadCost + commissionCost + riskCost + financeCost + customerDiscountValue - supplierDiscountValue;
        const totalCostFactor = safeDiv(totalGlobalCosts, salesNet);

        return {
            salesNet,
            sumEK,
            sumLaborSales,
            sumMatSales,
            logisticsTotal: App.getActiveLogisticsItems().reduce((s, x) => s + num(x.price), 0),
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
        const isPanelOpen = App.ListView.isOpen(key, false);
        const totals = App.computeQuoteTotals();

        // ==========================================
        // 1. Hilfsfunktionen (Formatting & Math)
        // ==========================================
        const safe = (n) => {
            const v = Number(n);
            return Number.isFinite(v) ? v : 0;
        };
        
        const money = (n) => `${App.money(safe(n))}\u00A0€`;
        const pct = (n) => `${safe(n).toFixed(1).replace('.', ',')}\u00A0%`;
        const clamp01 = (x) => Math.max(0, Math.min(1, safe(x)));

        // ==========================================
        // 2. Basis-Metriken extrahieren
        // ==========================================
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
        const otherSales = Math.max(0, sales - (matSales + laborSales));

        const salesPerHour = safe(totals.salesPerHour);
        const profitPerHour = safe(totals.profitPerHour);

        // ==========================================
        // 3. Relationen & Prozentwerte berechnen
        // ==========================================
        const matShare = sales > 0 ? (matSales / sales) * 100 : 0;
        const laborShare = sales > 0 ? (laborSales / sales) * 100 : 0;
        const otherShare = sales > 0 ? (otherSales / sales) * 100 : 0;

        const targetMargin = safe(State?.config?.minProfit || 10);
        const marginTotalPct = sales > 0 ? (db3 / sales) * 100 : 0;
        const marginVsTarget = marginTotalPct - targetMargin;

        // ==========================================
        // 4. Chart-Logik (Balken & SVG Pie)
        // ==========================================
        const maxAbs = Math.max(1, Math.abs(sales), Math.abs(ekSum), Math.abs(db1), Math.abs(db2), Math.abs(netProfit));
        const barHeight = (val) => `${Math.round(clamp01(Math.abs(val) / maxAbs) * 100)}%`;

        // SVG Pie Chart Dashes
        const dashA = clamp01(matShare / 100) * 100;
        const dashB = clamp01(laborShare / 100) * 100;
        const dashC = Math.max(0, 100 - dashA - dashB);

        // ==========================================
        // 5. UI-Klassen & Status-Farben
        // ==========================================
        const isProfitBad = netProfit < 0;
        const isDb3Bad = db3 < 0;
        const isMarginBad = marginTotalPct < targetMargin;
        const isProfitHourBad = profitPerHour < 0;

        const chevronCls = isPanelOpen ? 'rotate-0' : 'rotate-180';
        
        // Dynamische Farbklassen
        const gainBoxClass = isProfitBad ? 'text-blue-700 bg-blue-50 border-blue-100' : 'text-green-700 bg-green-50 border-green-100';
        const gainSubClass = isProfitBad ? 'text-blue-400' : 'text-green-400';
        
        const db2BarClass = db2 < 0 ? 'bg-red-500' : 'bg-slate-300';
        const netBarClass = isProfitBad ? 'bg-blue-600' : 'bg-emerald-600';
        
        const marginBarClass = isMarginBad ? 'bg-red-500' : 'bg-emerald-500';
        const marginTextClass = isMarginBad ? 'text-red-600' : 'text-emerald-700';
        
        const profitHourTextClass = isProfitHourBad ? 'text-red-600' : 'text-green-600';
        const profitHourBarClass = isProfitHourBad ? 'bg-red-500' : 'bg-green-500';

        // ==========================================
        // 6. Template Rendering
        // ==========================================
        return `
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-2 mt-2 mx-2">
                <div class="bg-slate-100 p-3 border-b border-slate-200 flex justify-between items-center cursor-pointer hover:bg-slate-200 transition-colors"
                    onclick="App.ListView.toggleOpen('${key}')">
                    <h3 class="font-bold text-[#000000] text-sm flex items-center gap-2 uppercase tracking-wide">
                        <i class="fa-solid fa-chart-line w-4 h-4 text-blue-600"></i>
                        Analyse &amp; Controlling
                    </h3>
                    <i class="fa-solid fa-chevron-up w-4 h-4 text-[#000000] transition-transform ${chevronCls}"></i>
                </div>

                <div class="${isPanelOpen ? '' : 'hidden'}">
                    <div class="p-6 space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                                <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-chart-pie w-4 h-4 text-blue-600"></i>
                                    Split-Analyse
                                </h5>

                                <div class="mb-3 pb-3 border-b border-slate-100">
                                    <div class="flex justify-between text-xs font-semibold text-[#000000] mb-1">
                                        <span>Material &amp; Sonst.</span>
                                        <span>${money(matSales + otherSales)}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-[#000000]">
                                        <span>Anteil am Umsatz: ${pct(matShare + otherShare)}</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between text-xs font-semibold text-[#000000] mb-1">
                                        <span>Lohn &amp; Montage</span>
                                        <span>${money(laborSales)}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-[#000000]">
                                        <span>Anteil am Umsatz: ${pct(laborShare)}</span>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                    <div class="flex justify-between text-xs font-bold text-[#000000] bg-slate-50 p-1 rounded">
                                        <span>DB 1</span>
                                        <div class="text-right">
                                            <div>${money(db1)}</div>
                                            <div class="text-[9px] font-normal text-[#000000]">${pct(db1Pct)}</div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-xs font-bold ${gainBoxClass} p-1 rounded border">
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
                                <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-clock w-4 h-4 text-orange-600"></i>
                                    Stunden-Performance
                                </h5>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-orange-50 p-2 rounded">
                                        <span class="text-xs text-[#000000]">Gesamtstunden</span>
                                        <span class="font-bold">${hours.toFixed(1)} h</span>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-xs text-[#000000] mb-1">
                                            Umsatz pro Stunde (Netto)
                                        </div>
                                        <div class="flex justify-between items-baseline">
                                            <span class="text-xs text-[#000000]">Ø Satz</span>
                                            <span class="font-bold text-[#000000]">${money(salesPerHour)} /h</span>
                                        </div>
                                    </div>

                                    <div class="border-t border-slate-100 pt-2">
                                        <div class="flex justify-between text-xs text-green-600 mb-1 font-medium">
                                            Reingewinn pro Stunde (DB3)
                                        </div>
                                        <div class="flex justify-between items-baseline">
                                            <span class="text-xs text-[#000000]">Nach Risiko/Zins</span>
                                            <span class="font-bold ${profitHourTextClass}">
                                                ${money(profitPerHour)} /h
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 ring-1 ring-blue-100">
                                <h5 class="font-bold text-[#000000] mb-3 flex items-center gap-2 text-sm uppercase">
                                    <i class="fa-solid fa-money-bill-wave w-4 h-4 text-green-600"></i>
                                    Finanz-Dashboard
                                </h5>

                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-[#000000] text-xs">Umsatz Netto</span>
                                        <span class="font-medium">${money(sales)}</span>
                                    </div>

                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="text-[#000000]">./. EK Listenpreis</span>
                                        <span class="text-[#000000]">-${money(ekSum)}</span>
                                    </div>

                                    <div class="border-t border-slate-100 my-1 pt-1"></div>

                                    <div class="bg-slate-50 p-2 rounded border border-slate-100 mt-2">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-[#000000] text-xs uppercase">DB 3 (EBIT)</span>
                                            <span class="font-bold ${isDb3Bad ? 'text-red-600' : 'text-green-600'}">
                                                ${money(db3)}
                                            </span>
                                        </div>

                                        <div class="border-t border-slate-200 pt-1 mt-1">
                                            <div class="flex justify-between font-bold text-xs mt-1 text-blue-900">
                                                <span>Netto-Gewinn</span>
                                                <span>${money(netProfit)}</span>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-[#000000] mt-1">
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
                                    <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-trend-up w-3 h-3"></i> Ertrags-Wasserfall
                                    </h6>
                                    <span class="text-[10px] text-blue-600">Umsatz/Profit</span>
                                </div>

                                <div class="flex items-end gap-2 h-20 w-full mt-2">
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="Umsatz: ${money(sales)}" style="height:${barHeight(sales)};"></div>
                                        <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Umsatz</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="Kosten (EK): ${money(ekSum)}" style="height:${barHeight(ekSum)};"></div>
                                        <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Kosten</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 bg-slate-300" title="DB1: ${money(db1)}" style="height:${barHeight(db1)};"></div>
                                        <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">DB1</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 ${db2BarClass}" title="DB2: ${money(db2)}" style="height:${barHeight(db2)};"></div>
                                        <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">DB2</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-1 group">
                                        <div class="w-full rounded-t transition-all duration-500 ${netBarClass}" title="Netto: ${money(netProfit)}" style="height:${barHeight(netProfit)};"></div>
                                        <span class="text-[8px] text-[#000000] uppercase truncate w-full text-center">Netto</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-box-open w-3 h-3"></i> Umsatz-Mix
                                    </h6>
                                    <i class="fa-solid fa-bolt w-3 h-3 text-yellow-500"></i>
                                </div>

                                <div class="relative w-24 h-24 mx-auto mt-2">
                                    <svg viewBox="0 0 32 32" class="w-full h-full transform -rotate-90">
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#3b82f6" stroke-width="4" stroke-dasharray="${dashA} 100" stroke-dashoffset="0"></circle>
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#10b981" stroke-width="4" stroke-dasharray="${dashB} 100" stroke-dashoffset="-${dashA}"></circle>
                                        <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f59e0b" stroke-width="4" stroke-dasharray="${dashC} 100" stroke-dashoffset="-${dashA + dashB}"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                                        <span class="text-[10px] font-bold text-[#000000]">${pct(matShare)}</span>
                                        <span class="text-[7px] text-[#000000] uppercase">Material</span>
                                    </div>
                                </div>

                                <div class="mt-3 space-y-1 text-[10px] text-[#000000]">
                                    <div class="flex justify-between"><span class="text-[#000000]">Material</span><span>${money(matSales)}</span></div>
                                    <div class="flex justify-between"><span class="text-[#000000]">Lohn</span><span>${money(laborSales)}</span></div>
                                    <div class="flex justify-between"><span class="text-[#000000]">Sonst.</span><span>${money(otherSales)}</span></div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-bullseye w-3 h-3"></i> Margen-Monitor
                                    </h6>
                                    <div class="w-2 h-2 rounded-full animate-pulse ${isMarginBad ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                                </div>

                                <div class="flex-1 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold text-[#000000]">${pct(marginTotalPct)}</div>
                                    <div class="text-[9px] text-[#000000] text-center uppercase tracking-tighter mt-1">
                                        Gesamtmarge vs. ${targetMargin.toFixed(0)}% Ziel
                                    </div>

                                    <div class="w-full bg-slate-200 h-1.5 rounded-full mt-4 overflow-hidden">
                                        <div class="h-full transition-all duration-700 ${marginBarClass}" style="width:${Math.round(clamp01(Math.abs(marginTotalPct) / Math.max(1, targetMargin)) * 100)}%;"></div>
                                    </div>

                                    <div class="mt-2 text-[10px] ${marginTextClass} font-bold">
                                        ${isMarginBad ? 'Unter' : 'Über'} Ziel um ${pct(Math.abs(marginVsTarget))}
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                                <div class="flex items-center justify-between mb-2">
                                    <h6 class="text-[10px] font-bold text-[#000000] uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-chart-column w-3 h-3"></i> Effizienz-Index
                                    </h6>
                                </div>

                                <div class="space-y-3 mt-1">
                                    <div>
                                        <div class="flex justify-between text-[9px] mb-1">
                                            <span class="text-[#000000] uppercase">Umsatz / h</span>
                                            <span class="font-bold">${money(salesPerHour)}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                            <div class="bg-blue-400 h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between text-[9px] mb-1">
                                            <span class="text-[#000000] uppercase">Gewinn / h</span>
                                            <span class="font-bold ${profitHourTextClass}">${money(profitPerHour)}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                            <div class="${profitHourBarClass} h-full" style="width:${hours > 0 ? '100%' : '0%'};"></div>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200 text-[10px] text-[#000000]">
                                        DB3: <b class=" ${isDb3Bad ? 'text-red-600' : 'text-[#000000]'}">${money(db3)}</b>
                                        <span class="text-slate-300 mx-1">•</span>
                                        Netto: <b class=" ${isProfitBad ? 'text-blue-600' : 'text-green-600'}">${money(netProfit)}</b>
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
        // 1. Resolve source node and data
        const from = App.getItemNode(fromRef.sIdx, fromRef.iIdx, fromRef.subIdx);
        if (!from) return App.renderQuotePage();

        // 2. Prevent dropping a row into exactly itself
        const isSelfDrop =
            toRef.mode === 'sort-array' &&
            Number(fromRef.sIdx) === Number(toRef.sIdx) &&
            Number(fromRef.iIdx) === Number(toRef.iIdx) &&
            String(fromRef.subIdx) === String(toRef.subIdx);

        if (isSelfDrop) return App.renderQuotePage();

        // 3. Extract the items (the "block") from the state
        // We save the parent references to sync totals later
        const originalParentArray = from.parentArray;
        const originalParentItem = from.parentItem;
        const originalSIdx = fromRef.sIdx;
        const originalIIdx = fromRef.iIdx;

        const removedBlock = App.removeNodeFromSource(from);
        if (!removedBlock.length) return App.renderQuotePage();

        // 4. Identify Destination
        let targetArray = null;
        let insertIndex = 0;
        const targetSIdx = Number(toRef.sIdx);
        const targetIIdx = Number(toRef.iIdx);

        switch (toRef.mode) {
            case 'to-main':
                const targetSection = State.sections[targetSIdx];
                if (targetSection) {
                    targetArray = targetSection.items;
                    insertIndex = Number.isFinite(targetIIdx) ? targetIIdx : targetArray.length;
                }
                break;

            case 'to-sub':
                const targetParent = State.sections[targetSIdx]?.items[targetIIdx];
                if (targetParent) {
                    if (!Array.isArray(targetParent.subItems)) targetParent.subItems = [];
                    targetArray = targetParent.subItems;
                    insertIndex = targetArray.length; // Append to the end of sub-positions
                }
                break;

            case 'sort-array':
                const targetNode = App.getItemNode(targetSIdx, targetIIdx, toRef.subIdx);
                if (targetNode && Array.isArray(targetNode.parentArray)) {
                    targetArray = targetNode.parentArray;
                    insertIndex = targetNode.index;
                }
                break;
        }

        // 5. Final Insertion & Normalization
        if (targetArray) {
            // Offset the index if we are moving forward within the exact same array
            if (originalParentArray === targetArray && from.index < insertIndex) {
                insertIndex -= removedBlock.length;
            }

            // Fix depths and properties based on the new target type
            const normalizedBlock = App.normalizeDraggedBlockForTarget(removedBlock, targetArray, toRef.mode);
            
            targetArray.splice(Math.max(0, insertIndex), 0, ...normalizedBlock);

            // 6. Financial Synchronization
            // If we moved a sub-item, update the old parent's total
            if (originalParentItem) {
                App.syncParentTotals(originalSIdx, originalIIdx);
            }
            
            // If we moved items into a new parent, update that parent's total
            if (toRef.mode === 'to-sub' || (toRef.mode === 'sort-array' && targetArray !== State.sections[targetSIdx]?.items)) {
                App.syncParentTotals(targetSIdx, targetIIdx);
            }
        }

        // 7. Refresh UI
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

        let totalVk = 0;
        let totalEk = 0;

        rows.forEach((row) => {
            const qty = Number(row.qty || 0);
            const rate = Number(row.rate || 0);
            const ek = Number(row.ek || 0);

            totalVk += qty * rate;
            totalEk += qty * ek;

            row.total = qty * rate;
        });

        const rowCount = rows.length;
        parent.qty = rowCount || 1;
        // Setzt die Einheit dynamisch auf Personen, wenn es auf dem alten Std/Stk Fehler hing
        parent.unit = 'Stk';
        
        parent.price = rowCount > 0 ? (totalVk / rowCount) : 0;
        parent.rate = parent.price;
        parent.ek = rowCount > 0 ? (totalEk / rowCount) : 0;
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


    App.UserPrefs = {
        data: {
            defaultTab: 'list',
            showThumbnails: false,
            showCalcSidebar: false,
            columns: {}
        },

        load() {
            const db = window.ServerUserPrefs;
            
            // Ensure the List Store exists before we try to write to it
            App.ListView.ensureStore();

            if (db) {
                console.log("Loading preferences from DB...", db);
                this.data.defaultTab = db.default_tab || 'list';
                this.data.showThumbnails = !!db.show_thumbnails;
                this.data.showCalcSidebar = !!db.show_calc_sidebar;
                
                // Apply your specific JSON column configuration
                if (db.list_columns) {
                    this.data.columns = {
                        ...App.ListView.getDefaults().cols, // Keep system defaults for new columns
                        ...db.list_columns                  // Overwrite with your saved JSON
                    };
                    // Force push into the active State
                    State.listViewPrefs.cols = this.data.columns;
                }
            } else {
                // Fallback to defaults if no DB entry exists
                this.data.columns = App.ListView.getDefaults().cols;
                State.listViewPrefs.cols = this.data.columns;
            }

            // Apply UI states immediately
            State.userPrefs.defaultTab = this.data.defaultTab;
            App.toggleThumbnails(this.data.showThumbnails);
            
            // Right Sidebar Sync
            const sidebarRight = document.getElementById('sidebar-right');
            if (sidebarRight) {
                if (this.data.showCalcSidebar) sidebarRight.classList.remove('sidebar-collapsed');
                else sidebarRight.classList.add('sidebar-collapsed');
            }

            // Jump to the saved Tab
            setTimeout(() => {
                App.Tabs.switch(this.data.defaultTab);
            }, 100);
        },

        async saveToDb() {
            const rightSidebar = document.getElementById('sidebar-right');
            const payload = {
                default_tab: App.Tabs.current || 'list',
                show_thumbnails: !!State.showThumbnails,
                show_calc_sidebar: rightSidebar ? !rightSidebar.classList.contains('sidebar-collapsed') : false,
                list_columns: App.ListView.cols()
            };

            try {
                await fetch('/user/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                console.log("Prefs saved.");
            } catch (e) {
                console.error("Save failed", e);
            }
        }
    };

    // Modal Logic for User Settings
    App.UserPrefsModal = {
        colMap: {
            pos: 'Pos.', image: 'Bild', articleNumber: 'Art.-Nr.', title: 'Produkttitel',
            supplier: 'Lieferant & Konditionen', dokumente: 'Dokumente', type: 'Typ',
            status: 'Status', qty: 'Menge', qty_total: 'Gesamtmenge', unit: 'Einheit', pe: 'VPE', ek: 'EK / Einheit',
            ek_total: 'EK gesamt', margin: 'Marge', vk: 'VK / Einheit', profit: 'DB / Einheit',
            vk_total: 'VK gesamt', db_total: 'DB gesamt', weighting: 'Gewichtung',
            total: 'Gesamt', actions: 'Aktionen'
        },

        open() {
            // Sync UI with current App state
            document.querySelector(`input[name="pref_default_tab"][value="${State.userPrefs.defaultTab || 'list'}"]`).checked = true;
            document.getElementById('pref_show_thumbnails').checked = State.showThumbnails;
            
            const isRightCollapsed = document.getElementById('sidebar-right').classList.contains('sidebar-collapsed');
            document.getElementById('pref_show_sidebar').checked = !isRightCollapsed;

            // Render Columns checklist
            const cols = App.ListView.cols();
            const colContainer = document.getElementById('pref-columns-container');
            colContainer.innerHTML = Object.entries(this.colMap).map(([key, label]) => `
                <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-slate-100 rounded">
                    <input type="checkbox" id="pref_col_${key}" class="accent-[#93c21c]" ${cols[key] ? 'checked' : ''}>
                    <span class="text-xs text-[#000000]">${label}</span>
                </label>
            `).join('');

            document.getElementById('user-prefs-modal').classList.remove('hidden');
        },

        close() {
            document.getElementById('user-prefs-modal').classList.add('hidden');
        },

        async save() {
            // 1. Read UI
            const defaultTab = document.querySelector('input[name="pref_default_tab"]:checked').value;
            const showThumbs = document.getElementById('pref_show_thumbnails').checked;
            const showSidebar = document.getElementById('pref_show_sidebar').checked;
            
            // Update App internal state
            State.userPrefs.defaultTab = defaultTab;
            App.UserPrefs.data.defaultTab = defaultTab;
            
            App.UserPrefs.data.showThumbnails = showThumbs;
            App.toggleThumbnails(showThumbs);

            App.UserPrefs.data.showCalcSidebar = showSidebar;
            const rightSidebar = document.getElementById('sidebar-right');
            if (showSidebar) {
                rightSidebar.classList.remove('sidebar-collapsed');
            } else {
                rightSidebar.classList.add('sidebar-collapsed');
            }

            // Read columns and update ListView
            const cols = App.ListView.cols();
            Object.keys(this.colMap).forEach(key => {
                const cb = document.getElementById(`pref_col_${key}`);
                if (cb) cols[key] = cb.checked;
            });

            // 2. Persist to Database
            await App.UserPrefs.saveToDb();

            // 3. Re-render UI and close
            if (App.Tabs.current === 'list') App.ListView.render();
            this.close();
        }
    };

    // Modification: Hook into App.ListView.toggleCol to auto-save to DB instantly if triggered from the list view dropdown
    const _originalToggleCol = App.ListView.toggleCol.bind(App.ListView);
    App.ListView.toggleCol = function(name) {
        _originalToggleCol(name);
        App.UserPrefs.saveToDb(); // Sync instantly
    };


</script>

<script>
App.parsePriceUnit = function(priceUnit, fallbackUnit = 'Stk.'){
    const raw = (priceUnit || '').toString().trim();

    if (!raw) {
        return {
            value: 1,
            label: fallbackUnit || 'Stk.',
            text: `1 ${fallbackUnit || 'Stk.'}`
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
    'Stk.',   // Stück
    'm',
    'lfm',   // laufender Meter
    'm²',
    'm³',
    'kg',
    'g',
    't',
    'l',
    'ml',
    'Std',   // Stunden
    'Min',
    'Tag',
    'Woche',
    'Monat',
    'Pauschal',
    'Set',
    'Pers.'
];

    App.renderUnitOptions = function(selected){
        const val = (selected || 'Stk').toString();
        return App.UNIT_OPTIONS.map(u =>
            `<option value="${u}" ${u === val ? 'selected' : ''}>${u}</option>`
        ).join('');
    };

    App.renderLaborUnitOptions = function(selected) {
        const val = (selected || 'std').toString();
        // Only time/labor based units
        const opts = ['std', 'min', 'Tag', 'Pauschal'];
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

        let totalVk = 0;
        let totalEk = 0;

        carrier.labor_rows.forEach((row) => {
            const qty = Number(row.qty || 0);
            const ek = Number(row.ek || 0);
            const rate = Number(row.rate || 0);

            row.total = qty * rate;

            totalVk += qty * rate;
            totalEk += qty * ek;
        });

        const rowCount = carrier.labor_rows.length;
        carrier.kind = 'labor';
        carrier.qty = rowCount || 1;
        carrier.unit = 'Stk';
        carrier.measure = carrier.unit;

        carrier.price = rowCount > 0 ? (totalVk / rowCount) : 0;      // VK pro Person/Zeile
        carrier.rate = carrier.price;
        carrier.ek = rowCount > 0 ? (totalEk / rowCount) : 0;         // EK pro Person/Zeile
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

    App.addLaborToGlobalSection = function (laborRowsToAdd) {
        let lIdx = State.sections.findIndex((s) => !!s?.isLaborSection);
        if (lIdx === -1) {
            lIdx = App.addSection("Montage & Dienstleistungen", true);
        }
        
        const sec = State.sections[lIdx];
        if (!sec.items) sec.items = [];

        // Find existing global labor position
        let laborItem = sec.items.find(it => it.kind === 'labor');
        let isNew = false;

       if (!laborItem) {
            laborItem = {
                item_type: "labor", kind: "labor", name: "Arbeitsleistung",
                desc: "Dienstleistung / Montage", desc_html: "", qty: 1, unit: 'Std', measure: 'Std',
                price: 0, rate: 0, ek: 0, purchase_price: 0,
                active: true, showImage: false, depth: 0, labor_rows: [], subItems: [],
                print_hidden: false, print_hidden_labor: true
            };
            isNew = true;
        }

        if (!Array.isArray(laborItem.labor_rows)) laborItem.labor_rows = [];

        // Merge incoming rows into the existing table
        laborRowsToAdd.forEach(newRow => {
            const existingRow = laborItem.labor_rows.find(r =>
                (newRow.qualification_id && r.qualification_id === newRow.qualification_id) ||
                (!newRow.qualification_id && r.qualification_name === newRow.qualification_name)
            );

            if (existingRow) {
                // If qualification exists, add the hours together!
                existingRow.qty = Number(existingRow.qty || 0) + Number(newRow.qty || 0);
                existingRow.total = Number(existingRow.qty) * Number(existingRow.rate);
            } else {
                // If it's a new qualification, append it
                laborItem.labor_rows.push({
                    ...newRow,
                    id: Date.now() + Math.floor(Math.random() * 1000)
                });
            }
        });

        if (isNew) {
            sec.items.push(laborItem);
        }

        const iIdx = sec.items.indexOf(laborItem);
        App.recalcLaborCarrier(lIdx, iIdx, null);
    };


   App.renderLaborRowsTable = function(item, forPrint, sIdx, iIdx, subIdx) {
        const rows = Array.isArray(item?.labor_rows) ? item.labor_rows : [];
        if (!rows.length) return '';

        const money = (v) => {
            const n = Number(v || 0);
            return n.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' €';
        };

        const qtyText = (v) => {
            const n = Number(v || 0);
            return n.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        // Safely evaluate again for the button state
        const isLaborHidden = (item.print_hidden_labor === undefined || item.print_hidden_labor === null) 
            ? true 
            : (item.print_hidden_labor === true || item.print_hidden_labor === '1' || item.print_hidden_labor === 1);
            
        const eyeIcon = isLaborHidden ? 'fa-eye-slash' : 'fa-eye';

        let totalQty = 0;
        let totalEkAmount = 0;
        let totalAmount = 0;

        const body = rows.map((row, rowIdx) => {
            const qty   = Number(row.qty || 0);
            const ek    = Number(row.ek || 0);
            const rate  = Number(row.rate || 0);
            const total = qty * rate;

            totalQty += qty;
            totalEkAmount += qty * ek;
            totalAmount += total;

            return `
                <tr class="pdf-labor-row ${rowIdx % 2 === 1 ? 'is-alt' : ''}">
                    <td class="pdf-labor-cell pdf-labor-cell-title">
                        <div class="pdf-labor-qual">
                            ${App.escapeHtml(row.qualification_name || 'Arbeitsleistung')}
                        </div>
                    </td>
                    <td class="pdf-labor-cell pdf-labor-cell-unit center">
                        ${App.escapeHtml(row.unit || 'Std')}
                    </td>
                    <td class="pdf-labor-cell pdf-labor-cell-qty num">
                        ${qtyText(qty)}
                    </td>
                    <td class="pdf-labor-cell pdf-labor-cell-ek num">
                        ${money(ek)}
                    </td>
                    <td class="pdf-labor-cell pdf-labor-cell-rate num">
                        ${money(rate)}
                    </td>
                    <td class="pdf-labor-cell pdf-labor-cell-total num">
                        ${money(total)}
                    </td>
                </tr>
            `;
        }).join('');

        return `
            <div class="pdf-labor-wrap">
                <div class="pdf-labor-table-shell">
                    <table class="pdf-labor-table" role="table" aria-label="Arbeitsleistung">
                        <colgroup>
                            <col style="width:36%">
                            <col style="width:11%">
                            <col style="width:11%">
                            <col style="width:14%">
                            <col style="width:14%">
                            <col style="width:14%">
                        </colgroup>

                        <thead>
                            <tr>
                                <th class="pdf-labor-head pdf-labor-head-title">Qualifikation</th>
                                <th class="pdf-labor-head center">Einheit</th>
                                <th class="pdf-labor-head num">Menge</th>
                                <th class="pdf-labor-head num">EK</th>
                                <th class="pdf-labor-head num">Satz</th>
                                <th class="pdf-labor-head num">Gesamt</th>
                            </tr>
                        </thead>

                        <tbody>
                            ${body}
                        </tbody>

                        <tfoot>
                            <tr class="pdf-labor-total-row">
                                <td class="pdf-labor-total-label">Summe Arbeitsleistung</td>
                                <td class="pdf-labor-total-empty"></td>
                                <td class="pdf-labor-total-value num">${qtyText(totalQty)}</td>
                                <td class="pdf-labor-total-value num">${money(totalEkAmount)}</td>
                                <td class="pdf-labor-total-empty"></td>
                                <td class="pdf-labor-total-value num">${money(totalAmount)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;
    };

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // 1. Handle Position Image Upload
        const imgInput = document.getElementById('img-upload-input');
        // --- Kalkulations-Sidebar Resizer ---
        const calcResizer = document.getElementById('calc-resizer');
        const calcSidebar = document.getElementById('sidebar-right');
        let cStartX, cStartWidth;

        if (calcResizer && calcSidebar) {
            calcResizer.addEventListener('mousedown', (e) => {
                cStartX = e.clientX;
                cStartWidth = calcSidebar.getBoundingClientRect().width;
                document.documentElement.addEventListener('mousemove', doCalcDrag, false);
                document.documentElement.addEventListener('mouseup', stopCalcDrag, false);
                calcResizer.classList.add('active');
            });

            const doCalcDrag = (e) => {
                // Dragging to the left increases the width because the sidebar is pinned to the right
                let newWidth = cStartWidth - (e.clientX - cStartX);
                if (newWidth < 280) newWidth = 280;
                if (newWidth > 800) newWidth = 800; // Max width limit
                calcSidebar.style.width = newWidth + 'px';
                calcSidebar.style.transition = 'none'; // Disable smooth transition while actively dragging
            };

            const stopCalcDrag = () => {
                document.documentElement.removeEventListener('mousemove', doCalcDrag, false);
                document.documentElement.removeEventListener('mouseup', stopCalcDrag, false);
                calcResizer.classList.remove('active');
                calcSidebar.style.transition = ''; // Restore smooth transitions
            };
        }
        if (imgInput) {
            imgInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file || !App.editingImage) return;

                const reader = new FileReader();
                reader.onload = function(evt) {
                    // Find the item we are currently editing
                    const { sIdx, iIdx, subIdx } = App.editingImage;
                    const subArg = (subIdx === 'null' || subIdx === null) ? null : Number(subIdx);

                    let target = subArg !== null 
                        ? State.sections[sIdx].items[iIdx].subItems[subArg] 
                        : State.sections[sIdx].items[iIdx];

                    if (target) {
                        // Apply the new image and ensure it's visible
                        target.img = evt.target.result;
                        target.hideImage = false;
                        target.showImage = true;

                        // Re-render the UI
                        App.renderQuotePage();
                        if (App.Tabs.current === 'list') App.ListView.render();
                    }
                };
                // Read the file as a data URL
                reader.readAsDataURL(file);
                
                // Clear the input so you can upload the same file twice if needed
                this.value = ''; 
            });
        }

        // 2. Handle Tool / Floating Image Upload (Drag & Drop Stickers)
        const toolInput = document.getElementById('tool-upload-input');
        if (toolInput) {
            toolInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    // Add the uploaded image to the tools sidebar array
                    State.toolsImages = State.toolsImages || [];
                    State.toolsImages.unshift(event.target.result); // Add to the top
                    
                    // Re-render the tools sidebar
                    App.renderSidebarTools();
                };
                reader.readAsDataURL(file);
                this.value = ''; 
            });
        }

    });

    // Add this inside document.addEventListener('DOMContentLoaded', () => { ... });

    document.addEventListener('keydown', function(e) {
        // 1. Ignore shortcuts if the user is typing inside an input, textarea, or Quill editor
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        if (
            activeTag === 'input' || 
            activeTag === 'textarea' || 
            activeTag === 'select' || 
            document.activeElement.isContentEditable
        ) {
            return; // Let the browser do native text copy/paste
        }

        // 2. Check for Ctrl (Windows) or Cmd (Mac)
        const isCmdOrCtrl = e.ctrlKey || e.metaKey;

        // --- COPY (Ctrl + C) ---
        if (isCmdOrCtrl && e.key.toLowerCase() === 'c') {
            // If there are checked checkboxes in the List View, do a bulk copy
            if (State.selectedItems && State.selectedItems.size > 0) {
                e.preventDefault();
                App.Clipboard.copyBulk();
            }
        }

        // --- PASTE (Ctrl + V) ---
        if (isCmdOrCtrl && e.key.toLowerCase() === 'v') {
            // If the clipboard has items, paste the most recent one (index 0)
            if (App.Clipboard.items && App.Clipboard.items.length > 0) {
                e.preventDefault();
                // Pass -1 so it automatically pastes into the first valid section
                App.Clipboard.pasteItem(0, -1);
                
                // Show brief visual feedback on the clipboard icon
                App.Clipboard.flashSidebar();
            }
        }
    });


</script>



<!-- IDS / OCI Lieferanten-Suche Modal -->
<div id="supplier-search-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
    <div class="supplier-modal-card bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border border-slate-200">
        <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-4 bg-gradient-to-br from-white to-[#f7fee7]">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-10 h-10 rounded-2xl bg-[#93c21c] text-white inline-flex items-center justify-center shadow-lg shadow-lime-200">
                        <i class="fa-solid fa-plug-circle-bolt"></i>
                    </span>
                    Lieferant suchen
                </h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    IDS/OCI Shop im neuen Tab öffnen. Nach der Lieferanten-Auswahl erscheint zuerst eine Prüfseite für Brand, Artikelgruppe, Untergruppe, Distributor und Einheit.
                </p>
            </div>

            <button type="button"
                    onclick="App.SupplierSearch.close()"
                    class="w-10 h-10 rounded-2xl bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-5 space-y-4">
            <div id="supplier-search-alert" class="hidden rounded-2xl p-3 text-sm font-bold"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-2">Lieferant / IDS-Shop</label>
                    <select id="supplier-search-connection" class="w-full border border-slate-300 rounded-2xl px-3 py-3 text-sm outline-none focus:border-[#93c21c] bg-white">
                        <option value="">Lieferanten werden geladen...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 mb-2">Ziel-Sektion</label>
                    <select id="supplier-search-section" class="w-full border border-slate-300 rounded-2xl px-3 py-3 text-sm outline-none focus:border-[#93c21c] bg-white"></select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-700 mb-2">Suchbegriff</label>
                <div class="flex gap-2">
                    <input id="supplier-search-query"
                           type="text"
                           class="flex-1 border border-slate-300 rounded-2xl px-3 py-3 text-sm outline-none focus:border-[#93c21c]"
                           placeholder="z.B. Wärmepumpe, Rohr, Modul, Kabel..."
                           onkeydown="if(event.key === 'Enter'){ event.preventDefault(); App.SupplierSearch.forward(); }">
                    <button type="button"
                            onclick="App.SupplierSearch.forward()"
                            class="px-4 py-3 rounded-2xl bg-[#93c21c] text-white font-black text-sm hover:brightness-95 shadow whitespace-nowrap">
                        Shop öffnen
                    </button>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600 leading-relaxed">
                <div class="font-black text-slate-800 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-rotate text-[#93c21c]"></i>
                    Rückgabe mit Prüfung + Reverb
                </div>
                Nach dem Rücksprung öffnet Laravel eine Prüfseite. Dort wählst du Brand, Artikelgruppe, Untergruppe, Distributor und Einheit. Nach dem Speichern werden die Artikel per Reverb live in diese Liste eingefügt.
            </div>
        </div>

        <div class="p-5 border-t border-slate-100 flex justify-between gap-2 bg-slate-50">
            <button type="button"
                    onclick="App.SupplierSearch.showWaitInfo()"
                    class="px-4 py-2 rounded-2xl border border-[#93c21c]/30 bg-[#f7fee7] font-black text-sm text-[#6b8e12] hover:bg-white">
                <i class="fa-solid fa-satellite-dish"></i> Reverb wartet
            </button>

            <button type="button"
                    onclick="App.SupplierSearch.close()"
                    class="px-4 py-2 rounded-2xl border border-slate-200 font-black text-sm text-slate-700 hover:bg-white">
                Schließen
            </button>
        </div>
    </div>
</div>

<div id="supplier-live-toast" class="supplier-live-toast">
    <div class="supplier-live-toast-icon"><i class="fa-solid fa-circle-check"></i></div>
    <div>
        <div id="supplier-live-toast-title" class="supplier-live-toast-title">Lieferantenartikel eingefügt</div>
        <div id="supplier-live-toast-text" class="supplier-live-toast-text">Die Positionen wurden übernommen.</div>
    </div>
</div>

<!-- Lieferanten-Positionen Historie Modal -->
<div id="supplier-history-modal" class="fixed inset-0 z-[10010] hidden items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
    <div class="supplier-history-card bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden border border-slate-200">
        <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-4 bg-gradient-to-br from-white to-blue-50">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-10 h-10 rounded-2xl bg-blue-600 text-white inline-flex items-center justify-center shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    Lieferanten-Positionen Historie
                </h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Alle über Lieferanten/IDS/OCI übernommenen Positionen in diesem Angebotsordner. Du kannst sie jederzeit erneut in die aktuelle Ziel-Sektion einfügen.
                </p>
            </div>

            <button type="button"
                    onclick="App.SupplierSearch.closeHistory()"
                    class="w-10 h-10 rounded-2xl bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-5 border-b border-slate-100 bg-slate-50 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input id="supplier-history-search"
                       type="text"
                       oninput="App.SupplierSearch.renderHistory()"
                       class="w-full border border-slate-300 rounded-2xl pl-10 pr-4 py-3 text-sm outline-none focus:border-[#93c21c] bg-white"
                       placeholder="Suche nach Name, Artikelnummer, Lieferant...">
            </div>

            <div class="flex items-center gap-2">
                <select id="supplier-history-section" class="border border-slate-300 rounded-2xl px-3 py-3 text-sm outline-none focus:border-[#93c21c] bg-white min-w-[220px]"></select>
                <button type="button"
                        onclick="App.SupplierSearch.clearHistory()"
                        class="px-4 py-3 rounded-2xl border border-red-200 bg-white text-red-600 font-black text-sm hover:bg-red-50">
                    <i class="fa-solid fa-trash"></i> Leeren
                </button>
            </div>
        </div>

        <div id="supplier-history-list" class="p-5 max-h-[62vh] overflow-y-auto bg-slate-50 space-y-3"></div>
    </div>
</div>

<script>
(function () {
    function supplierUrlFolderId() {
        try {
            const params = new URLSearchParams(window.location.search);
            return params.get('offer_folder_id') || params.get('folder_id') || null;
        } catch (e) {
            return null;
        }
    }

    function bootSupplierSearch() {
        if (!window.App || typeof State === 'undefined') {
            setTimeout(bootSupplierSearch, 150);
            return;
        }

        window.OfferSupplierConfig = window.OfferSupplierConfig || {};

        App.SupplierSearch = {
            connectionsLoaded: false,
            pollTimer: null,
            lastLogId: null,
            popupRef: null,
            processedImportKeys: new Set(),
            processedItemKeys: new Set(),
            historyReaddMode: false,

            folderId() {
                return State?.prefill?.offer_folder_id
                    || window.OfferSupplierConfig?.folderId
                    || supplierUrlFolderId()
                    || null;
            },

            offerId() {
                return State?.prefill?.offer_id
                    || window.OfferSupplierConfig?.offerId
                    || (new URLSearchParams(window.location.search)).get('offer_id')
                    || null;
            },

            syncConfig() {
                const folderId = this.folderId();
                const offerId = this.offerId();

                window.OfferSupplierConfig = window.OfferSupplierConfig || {};
                window.OfferSupplierConfig.folderId = folderId ? Number(folderId) : null;
                window.OfferSupplierConfig.offerId = offerId ? Number(offerId) : null;
                window.OfferSupplierConfig.returnMode = 'review_then_reverb';

                return window.OfferSupplierConfig;
            },

            baseUrl() {
                const folderId = this.folderId();
                return folderId ? `/admin/offers/folders/${encodeURIComponent(folderId)}/supplier` : null;
            },

            open: async function () {
                this.syncConfig();
                this.bootReverbListener();

                if (typeof App.isLockedSnapshot === 'function' && App.isLockedSnapshot()) {
                    this.toast('Dieses Dokument ist gesperrt.', 'Im Auftrag/Snapshot können keine Lieferantenartikel eingefügt werden.', 'error');
                    return;
                }

                if (!this.folderId()) {
                    this.toast('Angebot zuerst speichern', 'Bitte speichere das Angebot zuerst, damit ein Angebotsordner existiert.', 'error');
                    return;
                }

                const modal = document.getElementById('supplier-search-modal');
                if (!modal) return;

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                this.clearAlert();
                this.renderSectionOptions();

                if (!this.connectionsLoaded) {
                    await this.loadConnections();
                }

                setTimeout(() => document.getElementById('supplier-search-query')?.focus(), 80);
            },

            close: function () {
                const modal = document.getElementById('supplier-search-modal');
                if (!modal) return;

                modal.classList.add('hidden');
                modal.classList.remove('flex');
            },

            alert: function (message, type = 'error') {
                const box = document.getElementById('supplier-search-alert');
                if (!box) return;

                box.classList.remove('hidden');
                box.textContent = message;
                box.className = type === 'success'
                    ? 'rounded-2xl p-3 text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-200'
                    : 'rounded-2xl p-3 text-sm font-bold bg-red-50 text-red-700 border border-red-200';
            },

            clearAlert: function () {
                const box = document.getElementById('supplier-search-alert');
                if (!box) return;
                box.classList.add('hidden');
                box.textContent = '';
            },

            toast: function (title, text, type = 'success') {
                const toast = document.getElementById('supplier-live-toast');
                if (!toast) return;

                const icon = toast.querySelector('.supplier-live-toast-icon');
                const titleEl = document.getElementById('supplier-live-toast-title');
                const textEl = document.getElementById('supplier-live-toast-text');

                if (titleEl) titleEl.textContent = title || '';
                if (textEl) textEl.textContent = text || '';

                if (icon) {
                    icon.style.background = type === 'error' ? '#fef2f2' : '#f7fee7';
                    icon.style.color = type === 'error' ? '#dc2626' : '#6b8e12';
                    icon.innerHTML = type === 'error'
                        ? '<i class="fa-solid fa-triangle-exclamation"></i>'
                        : '<i class="fa-solid fa-circle-check"></i>';
                }

                toast.classList.add('is-visible');
                clearTimeout(this._toastTimer);
                this._toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 5200);
            },

            showWaitInfo: function () {
                this.toast(
                    'Reverb wartet',
                    'Nach dem Speichern auf der Rückgabe-Prüfseite wird die Position automatisch live eingefügt.'
                );
            },

            loadConnections: async function () {
                const select = document.getElementById('supplier-search-connection');
                const baseUrl = this.baseUrl();

                if (!select || !baseUrl) return;

                select.innerHTML = '<option value="">Lieferanten werden geladen...</option>';

                try {
                    const res = await fetch(`${baseUrl}/connections`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Lieferanten konnten nicht geladen werden.');
                    }

                    const connections = data.connections || [];

                    if (!connections.length) {
                        select.innerHTML = '<option value="">Keine aktive IDS/OCI Schnittstelle gefunden</option>';
                        this.alert('Keine aktive IDS/OCI Schnittstelle gefunden. Prüfe is_active=1 und connector_type=ids/oci.');
                        return;
                    }

                    select.innerHTML = '<option value="">Lieferant auswählen...</option>';

                    connections.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;

                        let label = `${c.name} · ${c.connector_type || 'IDS/OCI'}`;

                        if (c.distributor_name) {
                            label += ` · ${c.distributor_name}`;
                        }

                        if (!c.has_endpoint) {
                            label += ' · Endpoint fehlt';
                            opt.disabled = true;
                        }

                        if (!c.has_distributor) {
                            label += ' · Distributor wird auf Prüfseite gewählt';
                        }

                        opt.textContent = label;
                        opt.dataset.hasEndpoint = c.has_endpoint ? '1' : '0';
                        opt.dataset.hasDistributor = c.has_distributor ? '1' : '0';
                        opt.dataset.warning = c.warning || '';
                        select.appendChild(opt);
                    });

                    this.connectionsLoaded = true;
                } catch (error) {
                    console.error(error);
                    select.innerHTML = '<option value="">Fehler beim Laden</option>';
                    this.alert(error.message || 'Lieferanten konnten nicht geladen werden.');
                }
            },

            renderSectionOptions: function () {
                const select = document.getElementById('supplier-search-section');
                if (!select) return;

                select.innerHTML = '';
                const sections = Array.isArray(State.sections) ? State.sections : [];

                sections.forEach((section, index) => {
                    if (!section || section._pageBreak || section._virtualSection || section.isLocked) return;

                    const opt = document.createElement('option');
                    opt.value = index;
                    opt.textContent = section.title || section.name || `Sektion ${index + 1}`;
                    select.appendChild(opt);
                });

                if (!select.options.length) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Neue Sektion: Lieferantenartikel';
                    select.appendChild(opt);
                }
            },

            forward: function () {
                this.clearAlert();
                this.syncConfig();
                this.bootReverbListener();

                const baseUrl = this.baseUrl();
                const connectionSelect = document.getElementById('supplier-search-connection');
                const selectedOption = connectionSelect?.selectedOptions?.[0];
                const connectionId = connectionSelect?.value;
                const query = document.getElementById('supplier-search-query')?.value?.trim();
                const sectionValue = document.getElementById('supplier-search-section')?.value;

                if (!baseUrl) {
                    this.alert('Bitte Angebot zuerst speichern, damit ein Angebotsordner existiert.');
                    return;
                }

                if (!connectionId) {
                    this.alert('Bitte zuerst einen Lieferanten auswählen.');
                    return;
                }

                if (selectedOption && selectedOption.dataset.hasEndpoint !== '1') {
                    this.alert('Dieser IDS/OCI Lieferant hat keine Endpoint URL.');
                    return;
                }

                if (!query) {
                    this.alert('Bitte einen Suchbegriff eingeben.');
                    return;
                }

                const params = new URLSearchParams();
                params.set('query', query);

                if (sectionValue !== '' && sectionValue !== null && sectionValue !== undefined) {
                    params.set('target_section_index', sectionValue);
                }

                const url = `${baseUrl}/${encodeURIComponent(connectionId)}/forward?${params.toString()}`;

                this.popupRef = window.open(
                    url,
                    `offer_supplier_${connectionId}_${Date.now()}`,
                    'width=1320,height=880,scrollbars=yes,resizable=yes'
                );

                if (!this.popupRef) {
                    this.alert('Popup wurde blockiert. Bitte Popups für diese Seite erlauben.');
                    return;
                }

                this.alert('Lieferanten-Shop wurde geöffnet. Nach der Auswahl erscheint eine Prüfseite; nach dem Speichern kommt der Artikel per Reverb hierher.', 'success');
                this.toast('Lieferanten-Shop geöffnet', 'Warenkorb zurückgeben, Daten prüfen und speichern. Das Angebot bleibt offen.');
            },

            startPolling: function () {
                // New flow uses review page + Reverb. Kept as no-op for backward compatibility.
                this.showWaitInfo();
            },

            checkLatestImport: async function (showMessage = false) {
                if (showMessage) {
                    this.showWaitInfo();
                }
            },

            makeImportKey: function (payload = {}, items = []) {
                const folderId = payload.folder_id || this.folderId() || '';
                const logId = payload.log_id || payload.supplier_import_log_id || '';

                if (logId) {
                    return `log:${folderId}:${logId}`;
                }

                const itemKey = items
                    .map(item => [
                        item?._supplier_import_log_id || '',
                        item?.distributor_price_id || '',
                        item?.product_id || item?.productId || '',
                        item?.distributor_article_no || '',
                        item?.article_no || '',
                        item?.name || ''
                    ].join('|'))
                    .join('::');

                return `items:${folderId}:${itemKey}`;
            },

            hasProcessedImport: function (key) {
                if (!key) return false;

                window.__offerSupplierProcessedImportKeys = window.__offerSupplierProcessedImportKeys || new Set();

                return this.processedImportKeys.has(key) || window.__offerSupplierProcessedImportKeys.has(key);
            },

            markProcessedImport: function (key) {
                if (!key) return;

                window.__offerSupplierProcessedImportKeys = window.__offerSupplierProcessedImportKeys || new Set();

                this.processedImportKeys.add(key);
                window.__offerSupplierProcessedImportKeys.add(key);

                try {
                    const storageKey = 'offer_supplier_processed_' + this.folderId();
                    const existing = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
                    if (!existing.includes(key)) {
                        existing.push(key);
                        sessionStorage.setItem(storageKey, JSON.stringify(existing.slice(-80)));
                    }
                } catch (e) {}
            },

            loadProcessedImports: function () {
                try {
                    const storageKey = 'offer_supplier_processed_' + this.folderId();
                    const existing = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
                    existing.forEach(key => this.markProcessedImport(key));
                } catch (e) {}
            },

            makeItemKey: function (item = {}) {
                return [
                    item._supplier_import_log_id || '',
                    item.distributor_price_id || '',
                    item.product_id || item.productId || '',
                    item.distributor_article_no || '',
                    item.article_no || '',
                    item.name || ''
                ].join('|');
            },

            historyKey: function () {
                return 'offer_supplier_position_history_' + this.folderId();
            },

            escape: function (value) {
                if (window.App && typeof App.escapeHtml === 'function') {
                    return App.escapeHtml(value);
                }

                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            },

            loadHistory: function () {
                try {
                    const raw = localStorage.getItem(this.historyKey());
                    const history = JSON.parse(raw || '[]');
                    return Array.isArray(history) ? history : [];
                } catch (e) {
                    console.error('[Offer Supplier History] load failed:', e);
                    return [];
                }
            },

            saveHistory: function (history) {
                try {
                    localStorage.setItem(this.historyKey(), JSON.stringify((history || []).slice(0, 250)));
                } catch (e) {
                    console.error('[Offer Supplier History] save failed:', e);
                }
            },

            pushHistory: function (items, meta = {}) {
                if (!Array.isArray(items) || !items.length || meta.fromHistory) return;

                const history = this.loadHistory();
                const now = new Date();

                const normalized = items.map(raw => this.normalizeOfferItem(raw)).filter(Boolean);

                normalized.forEach(item => {
                    const key = this.makeItemKey(item);

                    const entry = {
                        id: 'hist_' + Date.now() + '_' + Math.floor(Math.random() * 100000),
                        key: key,
                        item: item,
                        folder_id: this.folderId(),
                        offer_id: this.offerId(),
                        log_id: meta.logId || item._supplier_import_log_id || null,
                        import_key: meta.importKey || null,
                        supplier: item.distributor_name || item.supplier || '',
                        name: item.name || 'Lieferantenartikel',
                        article_no: item.article_no || '',
                        distributor_article_no: item.distributor_article_no || '',
                        price: Number(item.price || item.unit_price || 0) || 0,
                        ek: Number(item.ek || item.purchase_price || 0) || 0,
                        unit: item.unit || item.measure || 'Stk',
                        created_at: now.toISOString(),
                        created_at_text: now.toLocaleString('de-DE')
                    };

                    const existingIndex = history.findIndex(row => row.key === key && String(row.log_id || '') === String(entry.log_id || ''));

                    if (existingIndex >= 0) {
                        history.splice(existingIndex, 1);
                    }

                    history.unshift(entry);
                });

                this.saveHistory(history);
            },

            openHistory: function () {
                const modal = document.getElementById('supplier-history-modal');
                if (!modal) {
                    console.error('[Offer Supplier History] Modal not found.');
                    return;
                }

                this.fillHistorySections();
                this.renderHistory();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            },

            closeHistory: function () {
                const modal = document.getElementById('supplier-history-modal');
                if (!modal) return;

                modal.classList.add('hidden');
                modal.classList.remove('flex');
            },

            fillHistorySections: function () {
                const select = document.getElementById('supplier-history-section');
                if (!select) return;

                const current = select.value;
                select.innerHTML = '';

                (State.sections || []).forEach((section, index) => {
                    if (!section || section._pageBreak || section._virtualSection || section.isLocked) return;

                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = section.title || section.name || ('Sektion ' + (index + 1));
                    select.appendChild(option);
                });

                if (current && [...select.options].some(option => option.value === current)) {
                    select.value = current;
                }
            },

            renderHistory: function () {
                const list = document.getElementById('supplier-history-list');
                if (!list) return;

                const query = String(document.getElementById('supplier-history-search')?.value || '').toLowerCase().trim();
                const history = this.loadHistory();

                const filtered = query
                    ? history.filter(row => [
                        row.name,
                        row.article_no,
                        row.distributor_article_no,
                        row.supplier,
                        row.created_at_text
                    ].join(' ').toLowerCase().includes(query))
                    : history;

                if (!filtered.length) {
                    list.innerHTML = `
                        <div class="text-center py-14 bg-white border border-dashed border-slate-300 rounded-3xl">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 inline-flex items-center justify-center mb-3">
                                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            </div>
                            <div class="font-black text-slate-700">Keine Lieferanten-Historie gefunden</div>
                            <div class="text-xs text-slate-500 mt-1">Sobald ein Lieferantenartikel übernommen wird, erscheint er hier.</div>
                        </div>
                    `;
                    return;
                }

                list.innerHTML = filtered.map(row => {
                    const name = this.escape(row.name || 'Lieferantenartikel');
                    const supplier = this.escape(row.supplier || 'Lieferant');
                    const art = this.escape(row.distributor_article_no || row.article_no || 'Keine Art.-Nr.');
                    const date = this.escape(row.created_at_text || '');
                    const price = Number(row.price || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const ek = Number(row.ek || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const unit = this.escape(row.unit || 'Stk');

                    return `
                        <div class="supplier-history-item" data-history-id="${this.escape(row.id)}">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="font-black text-slate-900 truncate" title="${name}">${name}</div>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span class="supplier-history-pill"><i class="fa-solid fa-barcode"></i> ${art}</span>
                                        <span class="supplier-history-pill"><i class="fa-solid fa-truck"></i> ${supplier}</span>
                                        <span class="supplier-history-pill"><i class="fa-solid fa-tag"></i> VK ${price} € / ${unit}</span>
                                        <span class="supplier-history-pill"><i class="fa-solid fa-coins"></i> EK ${ek} €</span>
                                        <span class="supplier-history-pill"><i class="fa-regular fa-clock"></i> ${date}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="supplier-history-readd-btn" onclick="App.SupplierSearch.reAddHistory('${this.escape(row.id)}')">
                                        <i class="fa-solid fa-plus"></i> Wieder einfügen
                                    </button>
                                    <button type="button" class="supplier-history-delete-btn" onclick="App.SupplierSearch.deleteHistory('${this.escape(row.id)}')" title="Eintrag löschen">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            },

            selectedHistorySectionIndex: function () {
                const select = document.getElementById('supplier-history-section');
                const value = select ? Number(select.value) : NaN;
                return Number.isInteger(value) ? value : null;
            },

            reAddHistory: function (historyId) {
                const history = this.loadHistory();
                const entry = history.find(row => String(row.id) === String(historyId));

                if (!entry || !entry.item) {
                    this.toast('Historie', 'Dieser Eintrag konnte nicht gefunden werden.', 'error');
                    return;
                }

                const item = JSON.parse(JSON.stringify(entry.item));
                item.id = `supplier_history_${item.product_id || item.productId || 'x'}_${Date.now()}_${Math.floor(Math.random() * 100000)}`;
                item._source = 'supplier_history_readd';
                item._supplierJustImported = true;

                this.appendItems([item], this.selectedHistorySectionIndex(), {
                    fromHistory: true,
                    importKey: 'history:' + historyId + ':' + Date.now()
                });

                if (typeof App.markDirty === 'function') App.markDirty();
                else State.hasUnsavedChanges = true;

                this.toast('Position wieder eingefügt', entry.name || 'Lieferantenartikel wurde erneut eingefügt.');
                this.closeHistory();
            },

            deleteHistory: function (historyId) {
                const history = this.loadHistory().filter(row => String(row.id) !== String(historyId));
                this.saveHistory(history);
                this.renderHistory();
            },

            clearHistory: function () {
                if (!confirm('Möchtest du die komplette Lieferanten-Positionen-Historie für diesen Angebotsordner löschen?')) return;
                this.saveHistory([]);
                this.renderHistory();
            },

            receiveImport: function (payload) {
                if (!payload) return;

                const hasOfferSupplierPayload =
                    payload.type === 'offer_supplier_import_done' ||
                    Array.isArray(payload.items);

                if (!hasOfferSupplierPayload) return;

                if (payload.folder_id && Number(payload.folder_id) !== Number(this.folderId())) return;

                const items = Array.isArray(payload.items) ? payload.items : [];

                if (!items.length) {
                    this.alert(payload.message || 'Keine Artikel wurden übernommen.');
                    return;
                }

                const importKey = this.makeImportKey(payload, items);

                if (this.hasProcessedImport(importKey)) {
                    console.warn('[Offer Supplier] Duplicate import ignored:', importKey);
                    return;
                }

                /*
                 * IMPORTANT:
                 * Mark BEFORE appendItems(), because the same payload can arrive almost at the same time
                 * via Reverb, postMessage, localStorage and ids-listener.js.
                 */
                this.markProcessedImport(importKey);
                this.lastLogId = payload.log_id || this.lastLogId;

                this.appendItems(items, payload.target_section_index, {
                    importKey: importKey,
                    fromHistory: false,
                    alreadyMarked: true,
                    logId: payload.log_id || null
                });

                this.close();

                clearInterval(this.pollTimer);
                this.toast('Lieferantenartikel eingefügt', `${items.length} Position(en) wurden live in die Liste eingefügt.`);
            },

            appendItems: function (items, targetSectionIndex = null, options = {}) {
                if (!Array.isArray(items) || !items.length) return;

                const importKey = options.importKey || this.makeImportKey({}, items);

                if (!options.fromHistory && !options.alreadyMarked && this.hasProcessedImport(importKey)) {
                    console.warn('[Offer Supplier] Duplicate append ignored:', importKey);
                    return;
                }

                if (!options.fromHistory && !options.alreadyMarked) {
                    this.markProcessedImport(importKey);
                }

                if (!Array.isArray(State.sections)) State.sections = [];

                const hadUnsavedChanges = !!State.hasUnsavedChanges;

                let sIdx = Number.isInteger(Number(targetSectionIndex)) ? Number(targetSectionIndex) : -1;

                if (
                    sIdx < 0 ||
                    !State.sections[sIdx] ||
                    State.sections[sIdx]._pageBreak ||
                    State.sections[sIdx]._virtualSection ||
                    State.sections[sIdx].isLocked
                ) {
                    sIdx = State.sections.findIndex(s => s && !s._pageBreak && !s._virtualSection && !s.isLocked);
                }

                if (sIdx === -1) {
                    sIdx = typeof App.addSection === 'function'
                        ? App.addSection('Lieferantenartikel', false)
                        : -1;

                    if (sIdx === -1) {
                        State.sections.push({
                            id: 'supplier_section_' + Date.now(),
                            title: 'Lieferantenartikel',
                            name: 'Lieferantenartikel',
                            description: 'Automatisch über Lieferanten-Schnittstelle eingefügt',
                            config: { mode: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' }, qty: 1, unit: '' },
                            items: []
                        });
                        sIdx = State.sections.length - 1;
                    }
                }

                if (!Array.isArray(State.sections[sIdx].items)) State.sections[sIdx].items = [];

                const createdIds = [];

                const insertedItems = [];

                items.forEach(raw => {
                    const item = this.normalizeOfferItem(raw);
                    const itemKey = this.makeItemKey(item);

                    if (!options.fromHistory) {
                        window.__offerSupplierProcessedItemKeys = window.__offerSupplierProcessedItemKeys || new Set();

                        if (this.processedItemKeys.has(itemKey) || window.__offerSupplierProcessedItemKeys.has(itemKey)) {
                            console.warn('[Offer Supplier] Duplicate item ignored:', itemKey);
                            return;
                        }

                        this.processedItemKeys.add(itemKey);
                        window.__offerSupplierProcessedItemKeys.add(itemKey);
                    }

                    item._supplierJustImported = true;
                    State.sections[sIdx].items.push(item);
                    createdIds.push(item.id);
                    insertedItems.push(item);
                });

                if (!options.fromHistory && insertedItems.length) {
                    this.pushHistory(insertedItems, {
                        importKey: importKey,
                        logId: options.logId || insertedItems[0]?._supplier_import_log_id || null,
                        fromHistory: false
                    });
                }

                // Backend already saved these supplier items. Preserve existing dirty state only.
                State.hasUnsavedChanges = hadUnsavedChanges;

                if (typeof App.renderQuotePage === 'function') App.renderQuotePage();
                if (App.ListView && typeof App.ListView.render === 'function') App.ListView.render();
                if (typeof App.rebuildThumbnails === 'function') App.rebuildThumbnails();

                setTimeout(() => {
                    createdIds.forEach(id => {
                        State.sections.forEach(sec => (sec.items || []).forEach(it => {
                            if (it && it.id === id) it._supplierJustImported = false;
                        }));
                    });
                    document.querySelectorAll('.supplier-import-row-flash').forEach(el => el.classList.remove('supplier-import-row-flash'));
                }, 3200);
            },

            normalizeOfferItem: function (raw) {
                const now = Date.now();
                const title = raw.name || raw.product || raw.title || raw.product_title || 'Lieferantenartikel';
                const unit = raw.unit || raw.measure || raw.measure_unit || raw.price_unit_label || 'Stk';
                const vk = Number(raw.price ?? raw.unit_price ?? raw.rate ?? raw.vk ?? 0) || 0;
                const ek = Number(raw.purchase_price ?? raw.ek ?? raw.cost ?? 0) || 0;
                const margin = ek > 0 ? ((vk - ek) / ek) * 100 : Number(raw.margin ?? raw.marginPercent ?? 20);

                return {
                    id: raw.id || `supplier_${raw.product_id || raw.productId || 'x'}_${now}_${Math.floor(Math.random() * 100000)}`,
                    item_type: raw.item_type || 'product',
                    kind: raw.kind || 'article',
                    status: raw.status || 'normal',
                    active: raw.active !== false,

                    product_id: raw.product_id || raw.productId || null,
                    productId: raw.productId || raw.product_id || null,

                    name: title,
                    desc_html: raw.desc_html || raw.description || raw.short_description || '',
                    description: raw.description || raw.short_description || raw.desc_html || '',

                    article_no: raw.article_no || raw.manufacturer_article_no || '',
                    manufacturer_article_no: raw.manufacturer_article_no || raw.article_no || '',
                    distributor_article_no: raw.distributor_article_no || raw.supplier_article_no || '',

                    distributor_id: raw.distributor_id || null,
                    distributor_price_id: raw.distributor_price_id || null,
                    distributor_name: raw.distributor_name || raw.supplier || '',
                    supplier: raw.supplier || raw.distributor_name || '',

                    qty: Number(raw.qty || raw.quantity || 1) || 1,
                    unit: unit,
                    measure: unit,
                    vpe: raw.vpe || raw.package_unit || '',

                    price: vk,
                    unit_price: vk,
                    ek: ek,
                    purchase_price: ek,
                    margin: Number.isFinite(margin) ? Number(margin.toFixed(2)) : 20,
                    marginPercent: Number.isFinite(margin) ? Number(margin.toFixed(2)) : 20,
                    marginType: raw.marginType || 'percent',

                    availability: raw.availability || '',
                    skonto: Number(raw.skonto || 0) || 0,
                    payment_terms: raw.payment_terms || '',

                    price_unit_value: Number(raw.price_unit_value || 1) || 1,
                    price_unit_label: raw.price_unit_label || unit,
                    price_unit_text: raw.price_unit_text || `1 ${unit}`,

                    img: raw.img || raw.image_url || raw.image || '',
                    image_url: raw.image_url || raw.img || raw.image || '',

                    subItems: Array.isArray(raw.subItems) ? raw.subItems : [],
                    labor_rows: Array.isArray(raw.labor_rows) ? raw.labor_rows : [],
                    hideImage: !!raw.hideImage,
                    hidePrices: !!raw.hidePrices,
                    hideNumbering: !!raw.hideNumbering,

                    _source: raw._source || 'supplier_import',
                    _supplier_connection_id: raw._supplier_connection_id || null,
                    _supplier_import_log_id: raw._supplier_import_log_id || null
                };
            },

            bootReverbListener: function () {
                const folderId = Number(this.folderId() || 0);

                if (!folderId) return;

                window.OfferSupplierConfig = window.OfferSupplierConfig || {};
                window.OfferSupplierConfig.folderId = folderId;
                window.OfferSupplierConfig.offerId = this.offerId() ? Number(this.offerId()) : window.OfferSupplierConfig.offerId;
                window.OfferSupplierConfig.returnMode = 'review_then_reverb';

                // Same guard as resources/js/ids-listener.js, so there will be no duplicate listener.
                if (window.__offerSupplierIdsListenerStarted === folderId) return;

                if (!window.Echo) {
                    setTimeout(() => this.bootReverbListener(), 500);
                    return;
                }

                window.__offerSupplierIdsListenerStarted = folderId;

                console.log('[Offer Supplier] Listening on private channel offer-folder.' + folderId);

                window.Echo.private('offer-folder.' + folderId)
                    .listen('.supplier.products.imported', (payload) => {
                        console.log('[Offer Supplier] Reverb payload:', payload);
                        App.SupplierSearch.receiveImport(payload);
                    })
                    .error((error) => {
                        console.error('[Offer Supplier] Reverb channel error:', error);
                    });
            }
        };

        App.SupplierSearch.syncConfig();
        App.SupplierSearch.loadProcessedImports();
        App.SupplierSearch.bootReverbListener();
        // Ensure the history button is always available even if an older toolbar template is cached/re-rendered.
        App.SupplierSearch.ensureHistoryToolbarButton = function () {
            setTimeout(() => {
                const toolbar = document.querySelector('.list-toolbar .flex.items-center.gap-2');
                if (!toolbar || toolbar.querySelector('.list-mini-btn-history')) return;

                const positionBtn = [...toolbar.querySelectorAll('button')].find(btn => (btn.textContent || '').trim().includes('Position'));
                if (!positionBtn) return;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-mini-btn list-mini-btn-history';
                btn.title = 'Historie der übernommenen Lieferanten-Positionen anzeigen und erneut einfügen';
                btn.innerHTML = '<i class="fa-solid fa-clock-rotate-left"></i> Historie';
                btn.onclick = () => App.SupplierSearch.openHistory();
                positionBtn.insertAdjacentElement('afterend', btn);
            }, 50);
        };

        if (App.ListView && typeof App.ListView.render === 'function' && !App.ListView.__historyButtonPatched) {
            const originalRender = App.ListView.render.bind(App.ListView);
            App.ListView.render = function () {
                const result = originalRender(...arguments);
                App.SupplierSearch.ensureHistoryToolbarButton();
                return result;
            };
            App.ListView.__historyButtonPatched = true;
        }

        App.SupplierSearch.ensureHistoryToolbarButton();


        window.addEventListener('message', function (event) {
            if (event.origin !== window.location.origin) return;
            App.SupplierSearch.receiveImport(event.data);
        });

        window.addEventListener('storage', function (event) {
            if (event.key !== 'offer_supplier_import_' + App.SupplierSearch.folderId()) return;
            try {
                App.SupplierSearch.receiveImport(JSON.parse(event.newValue || '{}'));
            } catch (e) {
                console.error(e);
            }
        });

        try {
            const cached = localStorage.getItem('offer_supplier_import_' + App.SupplierSearch.folderId());
            if (cached) App.SupplierSearch.receiveImport(JSON.parse(cached));
        } catch (e) {
            console.error(e);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSupplierSearch);
    } else {
        bootSupplierSearch();
    }
})();
</script>

</body>
</html>
