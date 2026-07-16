<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartQuote Direct - Professional</title>
    
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
        /* ✅ 2) Make Select2 look like your Tailwind input */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--multiple{
            min-height: 48px;
            border: 1px solid rgb(203 213 225);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            box-shadow: none;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple{
            border-color: #93c21c;
            outline: none;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            background: #f7fee7;
            border: 1px solid rgba(147,194,28,.35);
            border-radius: 0.5rem;
            padding: 2px 8px;
            margin-top: 6px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            margin-right: 6px;
        }
        .select2-dropdown{ border-color: rgb(226 232 240); }
        </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { 
                        brand: {
                            primary: '#93c21c',
                            textBlue: '#5a8fadd',
                            light: '#f4f9e8',
                            secondary: '#74b2d4',
                            subtle: '#e2e8f0',
                            pale: '#e3effb'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* CORE STYLES */
        body { background-color: #cbd5e1; color: #334155; height: 100vh; display: flex; flex-direction: column; overflow: hidden; font-family: 'Inter', sans-serif; }
        
        /* VIEW MANAGEMENT */
        .view-section { display: none !important; height: 100%; flex-direction: column; animation: fadeIn 0.3s ease-in-out; }
        .view-section.active { display: flex !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* A4 PAPER STYLES */
        .a4-page { 
            width: 210mm; height: 297mm; background: white; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            margin: 0 auto 40px auto; padding: 10mm 10mm; position: relative; 
            display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0; box-sizing: border-box;
            transform-origin: top center;
        }
        .page-content { flex: 1; display: flex; flex-direction: column; width: 100%; overflow: hidden; }

        /* SIDEBAR TRANSITIONS */
        .sidebar-panel { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s; overflow: hidden; }
        .sidebar-collapsed { width: 0 !important; opacity: 0; padding: 0 !important; border: none !important; }

        /* THUMBNAILS */
        .thumb-container { width: 150px; background: #e2e8f0; border-right: 1px solid #cbd5e1; overflow-y: auto; padding: 1rem; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
        .thumb-wrapper { width: 100%; display: flex; justify-content: center; height: 160px; margin-bottom: 5px; cursor: pointer; transition: transform 0.2s; position: relative; overflow: hidden; border-radius: 4px; }
        .thumb-wrapper:hover { transform: scale(1.05); z-index: 10; overflow: visible; }
        .thumb-scale-box { width: 210mm; height: 297mm; transform: scale(0.13); transform-origin: top center; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); pointer-events: none; user-select: none; border: 1px solid #94a3b8; }
        .thumb-label { position: absolute; bottom: 5px; right: 10px; background: rgba(0,0,0,0.7); color: white; font-size: 9px; padding: 2px 6px; border-radius: 10px; font-weight: bold; pointer-events: none; }

        /* PDF STYLES */
        .pdf-title-blue { color: #5298bc; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 0.1rem; }
        .pdf-logo-text { font-weight: 900; color: #93c21c; letter-spacing: -0.02em; }
        
        /* EDITABLE FIELDS */
        .editable-field { border: 1px dashed transparent; transition: all 0.2s; padding: 0 2px; border-radius: 2px; }
        .editable-field:hover { background-color: #f1f5f9; border-color: #cbd5e1; cursor: text; }
        .editable-field:focus { outline: 2px solid #93c21c; background-color: white; border-color: transparent; }
        .clean-input { width: 100%; background: transparent; border-bottom: 1px solid transparent; outline: none; transition: border-color 0.2s; padding: 0; }
        .clean-input:focus { border-bottom-color: #93c21c; }
        .clean-input:hover { border-bottom-color: #e2e8f0; }

        /* IMAGES & BADGES */
        .prod-img-container { position: relative; width: 5rem; height: 5rem; border-radius: 0.25rem; overflow: hidden; cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; flex-shrink: 0; }
        .prod-img-container:hover::after { content: '\f030'; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; inset: 0; background: rgba(0,0,0,0.3); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .item-badge { position: absolute; z-index: 10; font-size: 0.5rem; font-weight: bold; text-transform: uppercase; padding: 1px 3px; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .badge-tl { top: 0; left: 0; } .badge-tr { top: 0; right: 0; } .badge-bl { bottom: 0; left: 0; } .badge-br { bottom: 0; right: 0; }

        /* GRID LAYOUTS */
        .pos-header-grid { display: grid; grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem; gap: 0.75rem; font-size: 0.75rem; font-weight: 300; color: #4a4a4a; border-bottom: 3px solid #93c21c; padding-bottom: 0.5rem; margin-bottom: 1rem; align-items: center; }
        .pos-row-top { display: grid; grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem; gap: 0.75rem; font-size: 0.8rem; font-weight: bold; color: #1e293b; border-bottom: 3px solid #74b2d4; padding-bottom: 0.25rem; margin-bottom: 0.25rem; align-items: center; }
        .pos-row-bottom { display: flex; gap: 1rem; padding-left: 2.5rem; margin-bottom: 0.5rem; }
        
        .sub-pos-container { margin-top: 0.5rem; padding-left: 2.5rem; }
        .sub-pos-grid { display: grid; grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem; gap: 0.75rem; font-size: 0.75rem; color: #64748b; padding: 0.25rem 0; border-bottom: 1px dotted #e2e8f0; align-items: center; }

        /* UTILS */
        .btn-primary { @apply bg-[#93c21c] text-white shadow hover:brightness-105 transition-all active:scale-95 px-4 py-2 rounded font-bold; }
        .btn-disabled { @apply bg-slate-300 text-white cursor-not-allowed shadow-none; }
        .modal-overlay { background-color: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); }
        .sidebar-tab { @apply flex-1 py-3 text-center text-xs font-bold text-slate-500 hover:text-[#93c21c] border-b-2 border-transparent transition cursor-pointer; }
        .sidebar-tab.active { @apply text-[#93c21c] border-[#93c21c] bg-slate-50; }
        .scroller { overflow-y: auto; scrollbar-width: thin; }
        .scroller::-webkit-scrollbar { width: 6px; }
        .scroller::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }

        /* DRAG & DROP */
        .draggable-item { cursor: grab; user-select: none; }
        .draggable-item:active { cursor: grabbing; }
        .section-drop-zone { min-height: 50px; transition: all 0.2s; margin-bottom: 1rem; border: 2px dashed #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .section-drop-zone.drag-over { background-color: #f0fdf4; border-color: #93c21c; }
        .section-drop-zone:empty::after { content: 'Produkte hierher ziehen'; color: #cbd5e1; font-size: 0.7rem; }
        
        .item-group { transition: all 0.2s; border: 1px solid transparent; border-radius: 4px; padding: 0.5rem; margin-bottom: 0.5rem; cursor: grab; }
        .item-group:active { cursor: grabbing; }
        .item-group:hover { background-color: #fafafa; border-color: #e2e8f0; }
        
        /* Drop Target Visuals */
        .item-group.drag-over-sub { background-color: #eff6ff; border-color: #74b2d4; box-shadow: 0 4px 6px -1px rgba(116, 178, 212, 0.2); }
        .item-group.drag-over-sub::after { content: '+ Unterposition hinzufügen'; position: absolute; top: 0.5rem; right: 0.5rem; font-size: 0.7rem; font-weight: bold; color: #74b2d4; background: rgba(255,255,255,0.9); padding: 2px 6px; border-radius: 4px; pointer-events: none; }
        
        .item-group.drag-over-sort { border-top: 3px solid #93c21c; background-color: #f4f9e8; opacity: 1 !important; }
        
        /* TOOLS */
        .floating-element { position: absolute; cursor: grab; z-index: 50; }
        .floating-element:active { cursor: grabbing; outline: 1px dashed #74b2d4; }
        .delete-float { position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; display: none; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; }
        .floating-element:hover .delete-float { display: flex; }

        /* STATUS STYLES */
        .pos-inactive { opacity: 0.5; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, #f1f5f9 10px, #f1f5f9 20px); }
        .pos-optional .clean-input, .pos-optional .pdf-title-blue { color: #94a3b8; font-style: italic; }

        /* PRINT */
        @media print {
            body { background: white; height: auto; overflow: visible; }
            .no-print, .sidebar-panel, .thumb-container, header { display: none !important; }
            .a4-page { margin: 0; box-shadow: none; page-break-after: always; width: 100%; height: 100%; border:none; }
            .section-drop-zone, .delete-float { display: none !important; }
            .item-group { border: none; padding: 0; margin-bottom: 1rem; cursor: default; }
        }
    </style>

    <style>
        /* Editor main tabs */
        .editor-tab-btn{
            display:flex; align-items:center; gap:.5rem;
            padding:.5rem .75rem;
            font-size:.75rem; font-weight:900;
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
            box-shadow: 0 -1px 0 #fff inset;
        }
        .editor-tab-panel{ display:none; height:100%; }
        .editor-tab-panel.active{ display:flex; height:100%; }
        </style>


    <style>
    /* Active page badge (Seite X) */
    .thumb-wrapper.is-active .thumb-label{
        background: rgba(147,194,28,0.92) !important;   /* brand green */
        color: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(147,194,28,0.25);
    }
    .thumb-wrapper.is-active .thumb-scale-box{
        border-color: #93c21c !important;
        box-shadow: 0 0 0 2px rgba(147,194,28,0.25), 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Thumbs drag UX */
    .thumb-wrapper{ cursor: grab; }
    .thumb-wrapper:active{ cursor: grabbing; }
    .thumb-wrapper.sortable-ghost{ opacity:.35; }
    .thumb-wrapper.sortable-chosen{ box-shadow: 0 10px 18px rgba(0,0,0,.15); transform: scale(1.02); }
    .thumb-wrapper .thumb-label{ pointer-events:none; } /* keeps drag easy */
    /* Bibliothek inner tabs */
    .lib-subtab-active{
    background:#fff;
    color:#93c21c;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .lib-subtab-inactive{
    background:transparent;
    color:#64748b;
    }


    :root{
        --brand-color:#93c21c;
        }

        /* use brand variable everywhere */
        .brand-text { color: var(--brand-color) !important; }
        .brand-border { border-color: var(--brand-color) !important; }
        .brand-outline:focus { outline-color: var(--brand-color) !important; }

        /* keep your old ids working without rewriting all markup */
        #doc-logo-text,
        .pdf-logo-text,
        #editor-doc-type-label{
        color: var(--brand-color) !important;
        }

        .section-drop-zone.drag-over{
        border-color: var(--brand-color) !important;
        }

        .editable-field:focus{
        outline-color: var(--brand-color) !important;
        }

        .clean-input:focus{
        border-bottom-color: var(--brand-color) !important;
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
            <div class="flex items-center gap-3"
            >
            <!-- ✅ 1) HEADER: add page buttons (put inside your <header> right-side button group, near "Sektion") -->
                <button onclick="App.addPageAfterCurrent()"
                class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c] flex items-center gap-2">
                <i class="fa-solid fa-file-circle-plus"></i> Seite
                </button>

                <button onclick="App.askDeleteCurrentPage()"
                class="bg-white border border-slate-300 hover:border-red-500 px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-red-600 flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> Seite löschen
                </button>

            <button onclick="App.addSection()" class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c] flex items-center gap-2">
            <i class="fa-solid fa-folder-plus"></i> Sektion
            </button>

                <label class="flex items-center gap-2 text-xs text-slate-500 cursor-pointer mr-4 select-none"><input type="checkbox" id="show-hidden-toggle" onchange="App.renderQuotePage()" checked class="accent-[#93c21c]"> Versteckte anzeigen</label>
                <button onclick="App.openPrintPreview()" class="bg-slate-800 text-white hover:bg-slate-700 px-3 py-1.5 rounded text-sm font-bold flex items-center gap-2 transition-colors"><i class="fa-solid fa-print"></i> Druck-Vorschau</button>
                <button onclick="App.toggleSidebar('right')" class="text-slate-400 hover:text-[#93c21c] w-8 h-8 rounded hover:bg-slate-100 flex items-center justify-center border border-transparent hover:border-slate-300"><i class="fa-solid fa-calculator"></i></button>
            </div>
        </header>

        <div class="flex h-full overflow-hidden relative">
            <!-- LEFT SIDEBAR -->
            <aside id="sidebar-left" class="w-80 bg-white border-r border-slate-200 flex flex-col z-20 shadow-lg flex-shrink-0 sidebar-panel no-print">
                <div class="flex border-b border-slate-200"><div class="sidebar-tab active" onclick="App.switchSidebarTab('lib')" id="tab-lib">Bibliothek</div><div class="sidebar-tab" onclick="App.switchSidebarTab('tools')" id="tab-tools">Tools</div></div>
                <div id="sidebar-content-lib" class="flex-1 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-slate-100 space-y-3">
                        <div class="relative">
                            <input type="text" id="sidebar-search" oninput="App.renderSidebar()" placeholder="Suchen..."
                            class="w-full pl-8 pr-2 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-[#93c21c] bg-slate-50">
                            <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-sm"></i>
                        </div>

                        <div class="flex bg-slate-100 rounded-lg p-1">
                            <button id="lib-subtab-group"
                                    class="flex-1 py-2 text-[11px] font-black rounded-md"
                                    onclick="App.switchLibraryMode('group_sets')">
                                <i class="fa-solid fa-layer-group mr-1"></i> Group Sets
                            </button>

                            <button id="lib-subtab-sets"
                                    class="flex-1 py-2 text-[11px] font-black rounded-md"
                                    onclick="App.switchLibraryMode('sets')">
                                <i class="fa-solid fa-cube mr-1"></i> Sets/Artikel
                            </button>

                            <button id="lib-subtab-products"
                                    class="flex-1 py-2 text-[11px] font-black rounded-md"
                                    onclick="App.switchLibraryMode('products')">
                                <i class="fa-solid fa-box-open mr-1"></i> Produkte
                            </button>
                            </div>

                        </div>

                   <div class="flex-1 overflow-y-auto p-4 space-y-3 scroller bg-slate-50/50" id="sidebar-list"></div>
                </div>
                <div id="sidebar-content-tools" class="flex-1 flex flex-col h-full overflow-hidden hidden">
                    <div class="p-4 border-b border-slate-100"><button onclick="document.getElementById('tool-upload-input').click()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 rounded text-xs border border-slate-300 border-dashed flex items-center justify-center gap-2"><i class="fa-solid fa-upload"></i> Bild/Sticker hochladen</button></div>
                    <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 gap-2 scroller bg-slate-50/50" id="tools-list"></div>
                    <div class="p-4 border-b border-slate-100">
                    <div class="text-xs font-black text-slate-600 uppercase mb-2">Builder</div>
                    <div id="builder-palette" class="grid grid-cols-1 gap-2"></div>
                    </div>

                </div>
            </aside>

            <!-- NAV & CANVAS --> 
            <div class="relative flex flex-1 flex-col overflow-hidden">
                <!-- Tabs bar -->
                <div class="no-print px-3 pt-3">
                    <div class="flex items-end gap-2 border-b border-slate-200">
                    <button
                        id="editor-tab-btn-canvas"
                        type="button"
                        class="editor-tab-btn active"
                        onclick="App.switchEditorTab('canvas')"
                        aria-controls="editor-tab-canvas"
                        aria-selected="true"
                        role="tab"
                    >
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Angebot (A4)</span>
                    </button>

                    <button
                        id="editor-tab-btn-empty"
                        type="button"
                        class="editor-tab-btn"
                        onclick="App.switchEditorTab('empty')"
                        aria-controls="editor-tab-empty"
                        aria-selected="false"
                        role="tab"
                    >
                        <i class="fa-solid fa-square"></i>
                        <span>Tab 2 (leer)</span>
                    </button>

                    <div class="flex-1"></div>
                    </div>
                </div>

                <!-- Panels container -->
                <div class="relative flex-1 overflow-hidden">
                    <!-- TAB 1: NAV & CANVAS -->
                    <section
                    id="editor-tab-canvas"
                    class="editor-tab-panel active relative flex flex-1 overflow-hidden"
                    role="tabpanel"
                    aria-labelledby="editor-tab-btn-canvas"
                    >
                    <aside id="nav-pane" class="thumb-container no-print scroller"></aside>

                    <main
                        id="document-scroll-area"
                        class="scroller relative flex h-full flex-1 flex-col items-center gap-8 overflow-y-auto bg-slate-100/50 py-8"
                    >
                        <!-- Page 1 -->
                        <div
                        id="page-1"
                        class="a4-page group flex flex-shrink-0 flex-col"
                        ondrop="App.dropTool(event, 1)"
                        ondragover="App.allowDrop(event)"
                        >
                        <!-- ... keep ALL your existing page-1 HTML exactly as-is ... -->
                        </div>

                        <!-- Dynamic Page Container -->
                        <div id="position-pages-container" class="flex w-full flex-col items-center gap-8"></div>
                    </main>
                    </section>

                    <!-- TAB 2: empty for now -->
                   <section id="editor-tab-empty"
                        class="editor-tab-panel relative flex flex-1 overflow-hidden bg-slate-50"
                        role="tabpanel"
                        aria-labelledby="editor-tab-btn-empty"
                        hidden
                        >
                        <div class="flex flex-1 overflow-hidden">
                            <!-- List view -->
                            <div class="flex-1 overflow-y-auto p-4">
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                                <div class="font-black text-slate-700 flex items-center gap-2">
                                    <i class="fa-solid fa-list"></i> Kalkulation (Liste)
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c]"
                                            onclick="App.addSection()">
                                    <i class="fa-solid fa-folder-plus"></i> Sektion
                                    </button>
                                    <button class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c]"
                                            onclick="App.openFinanceSidebar()">
                                    <i class="fa-solid fa-chart-line"></i> Analyse
                                    </button>
                                    <button class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-1.5 rounded text-sm font-bold text-slate-600 hover:text-[#93c21c]"
                                            onclick="App.openGlobalSettings()">
                                    <i class="fa-solid fa-gear"></i> Settings
                                    </button>
                                </div>
                                </div>

                                <!-- DROP ZONE (list) -->
                                <div id="list-drop-root" class="p-4 bg-slate-50">
                                <div class="section-drop-zone" id="list-drop-zone"></div>
                                <div id="list-sections"></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </section>

                </div>
            </div>


            <!-- RIGHT SIDEBAR -->
            <aside id="sidebar-right" class="w-80 bg-white border-l border-slate-200 flex flex-col z-20 shadow-lg flex-shrink-0 sidebar-panel no-print">
                <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-slate-700 text-sm uppercase tracking-wide">Kalkulation</h3><div class="flex items-center gap-1"><span class="text-[10px] font-bold text-slate-400">MwSt</span><input type="number" id="global-tax" value="19" onchange="App.updateTaxRate(this.value)" class="w-10 text-xs border rounded text-center font-bold text-slate-700"><span class="text-[10px] text-slate-400">%</span></div></div>
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
    <div id="pos-settings-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closePosSettings()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-96 overflow-hidden animate-fadeIn"><div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-slate-800">Position bearbeiten</h3><button onclick="App.closePosSettings()" class="text-slate-400"><i class="fa-solid fa-times"></i></button></div><div class="p-4 space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-slate-500 mb-1">Einkaufspreis (EK)</label><input type="number" id="setting-ek" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Marge (%)</label><input type="number" id="setting-margin" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div></div><div class="bg-[#f0fdf4] p-3 rounded border border-[#93c21c]"><div class="flex justify-between items-center"><span class="text-xs font-bold text-[#93c21c]">Verkaufspreis (VK)</span><input type="number" id="setting-vk" class="w-24 text-right bg-transparent font-bold font-mono outline-none" oninput="App.calcPosSettings(true)"></div></div><div class="space-y-2 pt-2 border-t border-slate-100"><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-pauschal" class="accent-[#93c21c]"> <span>Als Pauschalposition</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-price" class="accent-[#93c21c]"> <span>Preise ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-active" class="accent-[#93c21c]"> <span>Position Aktiv</span></label></div><button onclick="App.savePosSettings()" class="w-full bg-[#93c21c] text-white rounded py-2 font-bold text-sm">Speichern</button></div></div></div>
    <div id="badge-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closeBadgeModal()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-80 overflow-hidden animate-fadeIn"><div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-slate-800">Badge</h3><button onclick="App.closeBadgeModal()" class="text-slate-400"><i class="fa-solid fa-times"></i></button></div><div class="p-4 space-y-4"><div><label class="block text-xs font-bold text-slate-500 mb-1">Standard Badge</label><select id="badge-type-select" class="w-full border rounded p-2 text-sm"><option value="">-- Kein Badge --</option><option value="NEU">NEU</option><option value="BESTSELLER">BESTSELLER</option><option value="10 JAHRE GARANTIE">10 JAHRE GARANTIE</option><option value="image">Bild hochladen...</option></select></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Position</label><div class="grid grid-cols-2 gap-2"><button onclick="App.setBadgePos('tl')" class="border rounded p-2 text-xs hover:bg-slate-100">Oben Links</button><button onclick="App.setBadgePos('tr')" class="border rounded p-2 text-xs hover:bg-slate-100">Oben Rechts</button><button onclick="App.setBadgePos('bl')" class="border rounded p-2 text-xs hover:bg-slate-100">Unten Links</button><button onclick="App.setBadgePos('br')" class="border rounded p-2 text-xs hover:bg-slate-100">Unten Rechts</button></div></div><button onclick="App.saveBadgeConfig()" class="w-full bg-[#93c21c] text-white rounded py-2 font-bold text-sm">Speichern</button></div></div></div>
    <div id="set-modal" class="fixed inset-0 z-[100] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closeModal()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fadeIn flex flex-col max-h-[85vh]"><div class="p-6 border-b border-slate-100 flex justify-between items-start bg-[#f7fee7]"><div><div class="text-[10px] font-bold text-[#93c21c] uppercase tracking-wider mb-1">Set-Inhalt</div><h3 class="text-2xl font-bold text-slate-800" id="modal-title">Produkt Name</h3><p class="text-sm text-slate-500 mt-1" id="modal-desc">Beschreibung</p></div><button onclick="App.closeModal()" class="text-slate-400 hover:text-slate-600 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-sm"><i class="fa-solid fa-times"></i></button></div><div class="flex-1 overflow-y-auto p-6 scroller"><table class="w-full text-sm text-left mb-6"><thead class="bg-slate-100 text-slate-500 font-bold text-xs uppercase"><tr><th class="px-4 py-2">Komponenten</th><th class="px-4 py-2 text-right">Wert</th></tr></thead><tbody id="modal-materials" class="divide-y divide-slate-100"></tbody></table><table class="w-full text-sm text-left"><thead class="bg-slate-100 text-slate-500 font-bold text-xs uppercase"><tr><th class="px-4 py-2">Dienstleistung</th><th class="px-4 py-2 text-center">Menge</th><th class="px-4 py-2 text-right">Wert</th></tr></thead><tbody id="modal-labor" class="divide-y divide-slate-100"></tbody></table></div><div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3"><button onclick="App.closeModal()" class="px-4 py-2 rounded-lg text-slate-500 font-medium hover:bg-slate-200 transition">Abbrechen</button><button id="modal-add-btn" class="px-6 py-2 rounded-lg bg-[#93c21c] text-white font-bold shadow-md hover:brightness-105 transition flex items-center gap-2"><i class="fa-solid fa-plus"></i> Zum Angebot</button></div></div></div>

    <!-- ✅ ADD THIS MODAL (place it near your other modals, before </body>) -->
    <div id="desc-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 modal-overlay" onclick="App.closeDescModal()"></div>

    <div class="absolute top-1/2 left-1/2 w-full max-w-3xl transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl overflow-hidden">
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



<script>
    // --- CONFIGURATION ---
    const API_BASE = '/offers'; 

    const State = {
            customer: null,
            object: null,
            projectDate: '',
            docType: 'Angebot',
            sections: [],
            offerId: 'NEW',
            custId: '-',
            placedImages: [],
            toolsImages: [],
            taxRate: 19,
            companyName: 'SOLAR ASPEKT',
            brandColor: '#93c21c',
            brandMode: 'text',          // 'text' | 'image'
            brandLogoUrl: '',           // selected logo url
            editingBadge: null,
            editingImage: null,
            dragState: null,
            

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
    };

    State.libraryMode = 'group_sets'; // ✅ default active tab


    const BUILDER_PALETTE = [
        { type: 'section',  label: 'Sektion',   icon: '<i class="fa-solid fa-folder-plus"></i>' },
        { type: 'position', label: 'Position',  icon: '<i class="fa-solid fa-plus"></i>' },
        { type: 'set',      label: 'Set',       icon: '<i class="fa-solid fa-layer-group"></i>' },
        { type: 'note',     label: 'Notiz/Text',icon: '<i class="fa-solid fa-pen"></i>' },
    ];

    // --- PRICE HELPERS (EK/VK auto pick) ---
    


    const PAGE_MAX_HEIGHT_PX = 850; 

    window.App = {
        init: () => {
            document.getElementById('wiz-date').valueAsDate = new Date();
            App.updateBranding();
            setTimeout(() => App.switchLibraryMode('group_sets'), 0);
            if (window.jQuery && $.fn.select2) {
                App.Wizard.initObjectSelect2();
                App.Wizard.setObjectDisabled(true); // step 2 starts disabled
            }
            
            // Event Listeners for closing dropdowns
            document.addEventListener('click', e => { 
                if(!e.target.closest('#wiz-customer-search') && !e.target.closest('#wiz-customer-dropdown')) {
                    document.getElementById('wiz-customer-dropdown').classList.add('hidden');
                }
            });

            // File Input Listeners
            document.getElementById('img-upload-input').onchange = e => { const f=e.target.files[0]; if(f && App.editingImage) { const r=new FileReader(); r.onload=ev=>{ State.sections[App.editingImage.sIdx].items[App.editingImage.iIdx].img=ev.target.result; App.renderQuotePage(); }; r.readAsDataURL(f); } };
            document.getElementById('badge-upload-input').onchange = e => { const f=e.target.files[0]; if(f && State.editingBadge) { const r=new FileReader(); r.onload=ev=>{ State.editingBadge.tempImg=ev.target.result; }; r.readAsDataURL(f); } };
            document.getElementById('tool-upload-input').onchange = e => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=ev=>{ State.toolsImages.push(ev.target.result); App.renderSidebarTools(); }; r.readAsDataURL(f); } };

        },

        switchEditorTab: (tab) => {
            const t = (tab === 'empty') ? 'empty' : 'canvas';

            // buttons
            document.getElementById('editor-tab-btn-canvas')?.classList.toggle('active', t === 'canvas');
            document.getElementById('editor-tab-btn-empty')?.classList.toggle('active', t === 'empty');

            // panels
            document.getElementById('editor-tab-canvas')?.classList.toggle('active', t === 'canvas');
            document.getElementById('editor-tab-empty')?.classList.toggle('active', t === 'empty');

            // when coming back to canvas, refresh observers/active thumb (so highlight is correct)
            if (t === 'canvas') {
                setTimeout(() => {
                try {
                    App.initThumbObserver?.();
                    App.initThumbSortable?.();
                    App.setActiveThumb?.(App._currentPageNo || 1);
                } catch (e) { /* ignore */ }
                }, 0);
            }
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

                const root = document.getElementById('document-scroll-area');
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
                App.setActiveThumb(1);
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

            openDescModal: (sIdx, iIdx, subIdx = null) => {
            const sec = State.sections[sIdx];
            const item = sec.items[iIdx];
            const target = (subIdx === null) ? item : (item.subItems?.[subIdx] || null);
            if (!target) return;

            // title
            const title = (subIdx === null)
                ? `Pos: ${item.name || 'Position'}`
                : `Unterpos: ${target.name || 'Unterposition'}`;

            document.getElementById('desc-modal-title').innerText = title;

            // init quill once
            if (!App._descQuill) {
                App._descQuill = new Quill('#desc-quill', {
                theme: 'snow',
                modules: {
                    toolbar: [
                    [{ header: [1,2,3,false] }],
                    ['bold','italic','underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean']
                    ]
                }
                });
            }

            // load existing HTML (preferred) or fallback plain text
            const html = (target.desc_html ?? target.desc ?? '').toString();
            App._descQuill.root.innerHTML = html;

            App._descEditing = { sIdx, iIdx, subIdx };

            document.getElementById('desc-modal').classList.remove('hidden');
            },

            closeDescModal: () => {
            document.getElementById('desc-modal').classList.add('hidden');
            App._descEditing = null;
            },

            saveDescModal: () => {
            if (!App._descEditing || !App._descQuill) return;

            const { sIdx, iIdx, subIdx } = App._descEditing;
            const sec = State.sections[sIdx];
            const item = sec.items[iIdx];
            const target = (subIdx === null) ? item : item.subItems[subIdx];

            // save as HTML for Angebot (keeps formatting)
            const html = (App._descQuill.root.innerHTML || '').trim();

            target.desc_html = html;
            // optional plain text shadow (useful for search/export)
            target.desc = App._descQuill.getText().trim();

            App.closeDescModal();
            App.renderQuotePage();
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
                const img = (src, label='IMG') => esc(App.pickImage({ image: src }, App.placeholderImg(label)));

                    const cardHtml = (ms) => {
                    const msImg = App.pickImage(ms, ''); // ✅ supports ms.image, ms.image_url, ...
                    return `
                        <div draggable="true"
                        ondragstart="App.dragStart(event, '${ms.id}', 'master_set')"
                        class="relative bg-white border border-slate-200 p-2 rounded shadow-sm cursor-grab hover:border-[#93c21c] flex items-start gap-2">
                        <div class="w-8 h-8 rounded bg-slate-100 flex-shrink-0 overflow-hidden mt-0.5 flex items-center justify-center">
                            <img src="${img(msImg, ms.name || 'SET')}" class="w-full h-full object-cover"
                                onerror="this.onerror=null;this.src='${img('', ms.name || 'SET')}'">
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

            selectCustomer: async (customer) => {
            State.customer = customer;
            State.custId = customer.customer_no || 'KD-NEW';

            document.getElementById('wiz-customer-search').parentElement.classList.add('hidden');
            document.getElementById('wiz-sel-cust-name').innerText = customer.name;
            document.getElementById('wiz-sel-cust-addr').innerText = `${customer.street || ''}, ${customer.city || ''}`;
            document.getElementById('wiz-customer-selected').classList.remove('hidden');

            try {
                const response = await fetch(`${API_BASE}/wizard/customers/${customer.id}/objects`);
                const data = await response.json();

                const sel = document.getElementById('wiz-object-select');
                sel.innerHTML = ''; // Select2 will render placeholder

                if (data.products && data.products.length > 0) {
                data.products.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.lead_product_id;
                    opt.text = p.label;
                    opt.dataset.altId = p.alternative_id;
                    opt.dataset.productId = p.product_id;
                    sel.appendChild(opt);
                });
                }

                document.getElementById('wiz-step-2').classList.remove('opacity-50', 'pointer-events-none');

                // ✅ refresh Select2 with new options + enable
                if (window.jQuery && $.fn.select2) {
                App.Wizard.initObjectSelect2();
                App.Wizard.setObjectDisabled(false);
                $('#wiz-object-select').val(null).trigger('change'); // clear selection
                }
            } catch (err) {
                console.error("Loading objects failed", err);
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

            clearCustomer: () => {
                State.customer = null;
                State.object = null;
                document.getElementById('wiz-customer-selected').classList.add('hidden');
                document.getElementById('wiz-customer-search').parentElement.classList.remove('hidden');
                document.getElementById('wiz-customer-search').value = '';
                document.getElementById('wiz-step-2').classList.add('opacity-50','pointer-events-none');
                document.getElementById('wiz-step-3').classList.add('opacity-50','pointer-events-none');
                document.getElementById('wiz-step-4').classList.add('opacity-50','pointer-events-none');
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
                            imageSrc: App.pickImage(ms, gs.image),
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
        },

        handleItemAdd: async (sIdx, id, typeFromDrag = null) => {
            // -----------------------------
            // Helpers
            // -----------------------------
            const safeNum = (v, d = 0) => {
                const n = Number(v);
                return Number.isFinite(n) ? n : d;
            };

            const safeStr = (v, d = "") => (v == null ? d : String(v));

            const ensureSection = (idx) => {
                if (!State.sections[idx]) {
                State.sections[idx] = {
                    title: "Sektion",
                    description: "",
                    config: {
                    mode: "standard",
                    pauschalPrice: 0,
                    type: "standard",
                    hidePrices: false,
                    margin: { value: 0, type: "fixed" },
                    },
                    items: [],
                };
                }
                if (!Array.isArray(State.sections[idx].items)) State.sections[idx].items = [];
            };

            const pushItem = (idx, item) => {
                ensureSection(idx);
                State.sections[idx].items.push(item);
            };

            const pickDescHtml = (x) =>
                safeStr(
                x?.description_variant_html ??
                    x?.description_html ??
                    x?.description_variant_text ??
                    x?.description_text ??
                    x?.description ??
                    ""
                );

            const pickDescText = (x) =>
                safeStr(
                x?.description_variant_text ??
                    x?.description_text ??
                    (typeof x?.description === "string" ? x.description : "") ??
                    ""
                );

            const fetchJson = async (url) => {
                const res = await fetch(url.toString(), {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
                });
                if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}`);
                return res.json();
            };

            const buildBaseItem = (overrides = {}) => ({
                item_type: "product",
                productId: null,
                name: "",
                desc_html: "",
                desc: "",
                img: "",
                showImage: true,    // ✅ ADD THIS DEFAULT
                price: 0,
                ek: 0,
                margin: 0,
                active: true,
                hidePrices: false,
                qty: 1,
                unit: "Stk",
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
                if (t === "master_set" || t === "master_set_group" || t === "product") return t;
                // If draggable did not pass type, default to product
                return "product";
            };

            ensureSection(sIdx);

            // -----------------------------
            // Main
            // -----------------------------
            const type = resolveType(typeFromDrag);
            const rawId = id;

            try {
                // ============================================================
                // 1) MASTER SET
                // ============================================================
                if (type === "master_set") {
                const url = new URL(`${API_BASE}/master-sets/${rawId}`, window.location.origin);
                url.searchParams.set("context", "angebot");

                const data = await fetchJson(url);

                const item = buildBaseItem({
                item_type: "master_set",
                productId: safeNum(data?.id ?? rawId),
                name: safeStr(data?.name, `MasterSet #${rawId}`),
                desc_html: safeStr(data?.description_html ?? data?.description, ""),
                desc: safeStr(data?.description_text, ""),
                unit: "Stk",
                qty: 1,
                subItems: [],
                showImage: false,   // ✅ ADD THIS
                img: "",            // keep empty
                });

                let totalSet = 0;

                const addComponentLine = (comp, isChild = false) => {
                    const qty = safeNum(comp?.qty, 1);
                    const unitPrice = safeNum(comp?.unit_price, 0);

                    const line = buildBaseItem({
                    item_type: "master_set_component",
                    component_id: comp?.id == null ? null : safeNum(comp.id, null),
                    productId: null,
                    name: `${isChild ? "↳ " : ""}${safeStr(
                        comp?.name,
                        isChild ? "Unterkomponente" : "Komponente"
                    )}`,
                    qty,
                    unit: safeStr(comp?.unit, "Stk"),
                    price: unitPrice,
                    desc_html: pickDescHtml(comp),
                    desc: pickDescText(comp),
                    });

                    item.subItems.push(line);

                    const lineTotal = safeNum(comp?.total, unitPrice * qty);
                    totalSet += lineTotal;
                };

                const comps = Array.isArray(data?.components) ? data.components : [];
                comps.forEach((comp) => {
                    addComponentLine(comp, false);
                    const children = Array.isArray(comp?.children) ? comp.children : [];
                    children.forEach((child) => addComponentLine(child, true));
                });

                item.price = totalSet;

                pushItem(sIdx, item);

                // Labor lines -> dedicated labor section
                if (Array.isArray(data?.labor) && data.labor.length) {
                    const lIdx = ensureLaborSectionIndex();
                    data.labor.forEach((l) => {
                    pushItem(
                        lIdx,
                        buildBaseItem({
                        item_type: "labor",
                        productId: null,
                        name: safeStr(l?.name, "Dienstleistung"),
                        price: safeNum(l?.hourly_rate, 0),
                        qty: safeNum(l?.hours, 1),
                        unit: "Std",
                        desc_html: "",
                        desc: "",
                        })
                    );
                    });
                }

                App.renderQuotePage();
                return;
                }

                // ============================================================
                // 2) MASTER SET GROUP (optional)
                // ============================================================
                if (type === "master_set_group") {
                const url = new URL(`${API_BASE}/master-set-groups/${rawId}`, window.location.origin);
                url.searchParams.set("context", "angebot");

                const data = await fetchJson(url);
                const sets = Array.isArray(data?.master_sets) ? data.master_sets : [];

                // Add each set as a real master_set item
                for (const ms of sets) {
                    if (ms?.id == null) continue;
                    await App.handleItemAdd(sIdx, ms.id, "master_set");
                }

                App.renderQuotePage();
                return;
                }

                // ============================================================
                // 3) PRODUCT (default)
                // ============================================================
                {
                const url = new URL(`${API_BASE}/products/${rawId}`, window.location.origin);
                url.searchParams.set("context", "angebot");

                let data = null;
                try {
                    data = await fetchJson(url);
                } catch (_) {
                    data = null;
                }

                const item = buildBaseItem({
                    item_type: "product",
                    productId: safeNum(data?.id ?? rawId),
                    name: safeStr(data?.name ?? data?.product, `Produkt ID: ${rawId}`),
                    desc_html: safeStr(data?.description_html ?? data?.description, ""),
                    desc: safeStr(data?.description_text, ""),
                    img: safeStr(data?.image ?? data?.img, ""),
                    price: safeNum(data?.price, 0),
                    ek: safeNum(data?.ek, 0),
                    margin: safeNum(data?.margin, 0),
                    qty: 1,
                    unit: safeStr(data?.unit, "Stk"),
                });

                pushItem(sIdx, item);
                App.renderQuotePage();
                return;
                }
            } catch (err) {
                console.error("handleItemAdd failed", { sIdx, id: rawId, typeFromDrag: type, err });

                // Fallback: keep UI consistent even if API fails
                pushItem(
                sIdx,
                buildBaseItem({
                    item_type: type === "master_set" ? "master_set" : "product",
                    productId: safeNum(rawId),
                    name: type === "master_set" ? `MasterSet #${rawId}` : `Produkt ID: ${rawId}`,
                    desc: "Details konnten nicht geladen werden.",
                })
                );

                App.renderQuotePage();
            }
        },



        addLibraryItemAsSubPosition: async (targetSIdx, targetIIdx, id, typeFromDrag) => {
            const safeNum = (v, d = 0) => {
                const n = Number(v);
                return Number.isFinite(n) ? n : d;
            };

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

            const stripHtml = (html) => (html ?? '').toString().replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();

            // helper: push one sub line
            const pushSub = (sub) => {
                parent.subItems.push({
                active: true,
                hidePrices: false,
                qty: 1,
                unit: 'Stk',
                price: 0,
                desc: '',
                desc_html: '',
                ...sub
                });
            };

            try {
                // ---------------------------------------------------------
                // 1) DROP: GROUP SET  -> add each set as a sub-position
                // ---------------------------------------------------------
                if (typeFromDrag === 'master_set_group') {
                const url = new URL(`${API_BASE}/master-set-groups/${id}`, window.location.origin);
                url.searchParams.set('context', 'angebot');

                const data = await fetchJson(url);
                const sets = Array.isArray(data.master_sets) ? data.master_sets : [];

                if (!sets.length) return;

                // add each set as a single sub-position (summary)
                for (const ms of sets) {
                    pushSub({
                    item_type: 'sub_master_set',
                    productId: safeNum(ms.id),
                    name: ms.name || `Set #${ms.id}`,
                    desc_html: (ms.description_html || ms.description || '').toString(),
                    desc: stripHtml(ms.description_text || ms.description || ''),
                    qty: 1,
                    unit: 'Stk',
                    price: safeNum(ms.total_price ?? ms.price ?? 0),
                    });
                }

                App.renderQuotePage();
                return;
                }

                // ---------------------------------------------------------
                // 2) DROP: SET (master_set) -> one sub-position summary
                // ---------------------------------------------------------
                if (typeFromDrag === 'master_set') {
                const url = new URL(`${API_BASE}/master-sets/${id}`, window.location.origin);
                url.searchParams.set('context', 'angebot');

                const data = await fetchJson(url);

                // compute total as fallback if backend doesn't give total
                let totalSet = 0;
                (Array.isArray(data.components) ? data.components : []).forEach((c) => {
                    const qty = safeNum(c?.qty, 1);
                    const pr  = safeNum(c?.unit_price, 0);
                    totalSet += safeNum(c?.total, pr * qty);

                    (Array.isArray(c?.children) ? c.children : []).forEach((ch) => {
                    const q2 = safeNum(ch?.qty, 1);
                    const p2 = safeNum(ch?.unit_price, 0);
                    totalSet += safeNum(ch?.total, p2 * q2);
                    });
                });

                pushSub({
                    item_type: 'sub_master_set',
                    productId: safeNum(data.id),
                    name: data.name || `MasterSet #${id}`,
                    desc_html: (data.description_html || data.description || '').toString(),
                    desc: stripHtml(data.description_text || data.description || ''),
                    qty: 1,
                    unit: 'Stk',
                    price: safeNum(data.total_price ?? data.price ?? totalSet),
                });

                App.renderQuotePage();
                return;
                }

                // ---------------------------------------------------------
                // 3) DROP: PRODUCT -> one sub-position
                // ---------------------------------------------------------
                if (typeFromDrag === 'product') {
                const url = new URL(`${API_BASE}/products/${id}`, window.location.origin);
                url.searchParams.set('context', 'angebot');

                let data = null;
                try { data = await fetchJson(url); } catch (_) { data = null; }

                pushSub({
                    item_type: 'sub_product',
                    productId: safeNum(data?.id ?? id),
                    name: (data?.name || data?.product || `Produkt #${id}`).toString(),
                    desc_html: (data?.description_html || data?.description || '').toString(),
                    desc: stripHtml(data?.description_text || data?.description || ''),
                    qty: 1,
                    unit: (data?.unit || 'Stk').toString(),
                    price: safeNum(data?.price ?? data?.best_price ?? 0),
                });

                App.renderQuotePage();
                return;
                }
            } catch (err) {
                console.error('addLibraryItemAsSubPosition failed', { targetSIdx, targetIIdx, id, typeFromDrag, err });

                // fallback sub line so user still sees something
                pushSub({
                item_type: 'sub_fallback',
                productId: safeNum(id),
                name: `${typeFromDrag} #${id}`,
                desc: 'Details konnten nicht geladen werden.',
                price: 0
                });

                App.renderQuotePage();
            }
            },



        // --- MAIN APP LOGIC ---
        switchView: (view) => { document.querySelectorAll('.view-section').forEach(v=>v.classList.remove('active')); document.getElementById('view-'+view).classList.add('active'); },
        toggleSidebar: (side) => { document.getElementById(side==='left'?'sidebar-left':'sidebar-right').classList.toggle('sidebar-collapsed'); },
        switchSidebarTab: (tab) => { document.querySelectorAll('.sidebar-tab').forEach(t=>t.classList.remove('active')); document.getElementById('tab-'+tab).classList.add('active'); if(tab==='lib'){document.getElementById('sidebar-content-lib').classList.remove('hidden');document.getElementById('sidebar-content-tools').classList.add('hidden');}else{document.getElementById('sidebar-content-lib').classList.add('hidden');document.getElementById('sidebar-content-tools').classList.remove('hidden');App.renderSidebarTools();} },
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
            // ---- helpers (safe setters) ----
            const $id = (id) => document.getElementById(id);

            const setText = (id, value) => {
                const el = $id(id);
                if (!el) { console.warn('[startQuote] missing element:', id); return; }
                el.innerText = (value ?? '').toString();
            };

            const setHTML = (id, value) => {
                const el = $id(id);
                if (!el) { console.warn('[startQuote] missing element:', id); return; }
                el.innerHTML = (value ?? '').toString();
            };

            const setValue = (id, value) => {
                const el = $id(id);
                if (!el) { console.warn('[startQuote] missing element:', id); return; }
                el.value = (value ?? '').toString();
            };

            // ---- data ----
            State.projectDate = $id('wiz-date')?.value || new Date().toISOString().slice(0, 10);

            const types = document.getElementsByName('wiz-doc-type');
            types.forEach(t => { if (t.checked) State.docType = t.value; });

            const c = State.customer || {};
            const dateStr = new Date(State.projectDate).toLocaleDateString('de-DE');

            // ---- cover fields (guarded) ----
            setText('doc-cust-name', c.name || '');
            setText('doc-cust-lastname', c.lastname || '');
            setHTML('doc-cust-addr', `${c.street || ''}<br>${c.postcode || ''} ${c.city || ''}`.trim());
            setText('doc-date-line', `Wehrheim, ${dateStr}`);

            setText('editor-doc-type-label', State.docType);
            setText('doc-main-title', `Unverbindliches ${State.docType} für...`);
            setText('lbl-doc-id-name', State.docType === 'Angebot' ? 'Angebotsnummer' : 'KVA-Nummer');

            setValue('doc-cust-id', c.customer_no || '');

            // ---- branding texts ----
            document.querySelectorAll('.pdf-logo-text').forEach(el => {
                el.innerText = State.companyName || 'SOLAR ASPEKT';
            });

            setText('doc-logo-text', State.companyName || 'SOLAR ASPEKT');

            App.applyBrandingToCover();

            setText('doc-team-name', `Ihr ${State.companyName || 'SOLAR ASPEKT'}-Team`);
            setText('footer-company', `${State.companyName || 'SOLAR ASPEKT'} GmbH`);
            setText('doc-company-header', `${State.companyName || 'SOLAR ASPEKT'} GmbH • Am Kappengraben 10 • 61273 Wehrheim`);

            // ---- init first section ----
            if (State.sections.length === 0) App.addSection('1. Hauptpositionen', false);

            // ---- render ----
            App.renderSidebar();
            App.renderQuotePage();
            App.switchView('editor');
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
            <div class="flex justify-between items-end border-b-4 border-[#93c21c] pb-1 mb-1 mt-16"><div class="font-bold text-sm text-[#93c21c]">${title} <span class="sync-offer-id text-[!#727272]">${State.offerId}</span></div></div><div class="pos-header-grid pb-2"><div class="text-center">Pos.</div><div>Artikelbezeichnung</div><div class="text-center">Menge</div><div class="text-center">Einh.</div><div class="text-right">EP</div><div class="text-right">GP</div><div></div></div><div class="page-content flex-1 relative"></div><div class="mt-auto border-t border-slate-200 pt-2 text-[9px] text-slate-400 text-center mb-4">Seite ${idx} • ${State.docType} freibleibend</div>`;
            return div;
        },

        renderQuotePage: (forPrint = false) => {
            const container = forPrint ? document.getElementById('print-preview-content') : document.getElementById('position-pages-container');
            container.innerHTML = '';
            if(!forPrint) document.getElementById('nav-pane').innerHTML = '';
            const showHidden = forPrint ? false : document.getElementById('show-hidden-toggle').checked;
            
            let pageIndex = 2;
            let currentPage = App.createPage(pageIndex, forPrint);
            container.appendChild(currentPage);
            if(!forPrint) { App.createThumbnail(1, 'Anschreiben'); App.createThumbnail(pageIndex, 'Positionen 1'); }
            App.renderFloatingImages(currentPage, pageIndex, forPrint);

            let contentBox = currentPage.querySelector('.page-content');
            let posCounter = 1;
            
            const append = (el) => {
                contentBox.appendChild(el);
                if (contentBox.scrollHeight > contentBox.clientHeight + 2) {
                    contentBox.removeChild(el);
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint);
                    container.appendChild(currentPage);
                    if(!forPrint) App.createThumbnail(pageIndex, `Positionen ${pageIndex-1}`);
                    App.renderFloatingImages(currentPage, pageIndex, forPrint);
                    contentBox = currentPage.querySelector('.page-content');
                    contentBox.appendChild(el);
                    return true;
                }
                return false;
            };

            State.sections.forEach((sec, sIdx) => {
                const isPauschal = sec.config.mode==='pauschal';
                const isOpt = sec.config.type==='optional';
                const isAlt = sec.config.type==='alternative';
                
                const header = document.createElement('div'); header.className='mb-1 mt-4';
                let badges = isOpt ? '(Optional)' : (isAlt ? '(Alternativ)' : '');
                
                 if (sec && sec._pageBreak) {
                    pageIndex++;
                    currentPage = App.createPage(pageIndex, forPrint);
                    container.appendChild(currentPage);

                    if(!forPrint) App.createThumbnail(pageIndex, `Positionen ${pageIndex-1}`);
                    App.renderFloatingImages(currentPage, pageIndex, forPrint);
                    contentBox = currentPage.querySelector('.page-content');
                    return;
                }
                // --- SECTION HEADER & DELETE BUTTON ---
                let deleteBtn = '';
                if(!forPrint) {
                    deleteBtn = `<button onclick="App.removeSection(${sIdx})" class="ml-auto text-slate-300 hover:text-red-500 p-1 rounded hover:bg-red-50 transition-colors" title="Sektion löschen"><i class="fa-solid fa-trash"></i></button>`;
                }

                let titleHtml = forPrint 
                    ? `<div class="text-lg font-bold text-brand-primary uppercase">${sec.title} ${badges}</div><div class="text-sm text-slate-600">${sec.description}</div>` 
                    : `<div class="flex items-center">
                         <input value="${sec.title}" oninput="App.updateSectionMeta(${sIdx},'title',this.value)" class="text-lg font-bold text-brand-primary w-full bg-transparent outline-none">
                         <span class="text-xs text-slate-400 ml-2 whitespace-nowrap">${badges}</span>
                         ${deleteBtn}
                       </div>
                       <textarea oninput="App.updateSectionMeta(${sIdx},'description',this.value)" class="text-sm text-slate-500 w-full bg-transparent resize-none outline-none h-auto">${sec.description}</textarea>`;
                
                header.innerHTML = titleHtml; 
                append(header);

                if(!forPrint) {
                    let dz = document.createElement('div'); dz.className='section-drop-zone'; dz.ondragover=e=>e.preventDefault(); 
                    dz.ondrop=e=>{
                        e.preventDefault();
                        const id=e.dataTransfer.getData("text");
                        const type=e.dataTransfer.getData("itemType");
                        if(id) App.handleItemAdd(sIdx,id, type);
                    }; 
                    append(dz);
                }

                if(sec.items.length > 0) {
                    sec.items.forEach((item, iIdx) => {
                        if(!item.active && !showHidden) return;
                        
                        const total = item.price * item.qty;
                        const posNum = String(posCounter++).padStart(3,'0');
                        const opClass = !item.active ? 'pos-inactive' : ((isOpt||isAlt)?'opacity-60':'');

                        let epD = item.price.toLocaleString('de-DE') + ' €';
                        let gpD = total.toLocaleString('de-DE') + ' €';
                        if (isPauschal || item.hidePrices || sec.config.hidePrices) { epD='-'; gpD='-'; }
                        else if (isOpt || isAlt) { gpD = `(${gpD})`; }

                        const row = document.createElement('div'); row.className=`item-group group ${opClass}`;
                        if(!forPrint) { 
                            row.draggable = true; 
                            row.ondragstart = e => App.dragStartPos(e, sIdx, iIdx);
                            row.ondragover = (e) => {
                                e.preventDefault();

                                // sorting existing positions
                                if (App.dragState && App.dragState.type === 'pos') {
                                    row.classList.add('drag-over-sort');
                                    return;
                                }

                                // library drag -> sub-position
                                const t = e.dataTransfer?.getData("itemType");
                                if (t === 'master_set' || t === 'master_set_group' || t === 'product') {
                                    e.stopPropagation();
                                    row.classList.add('drag-over-sub');
                                }
                            };

                            row.ondragleave=e=>{row.classList.remove('drag-over-sub', 'drag-over-sort');}; 
                            row.ondrop = async (e) => {
                                e.preventDefault();
                                row.classList.remove('drag-over-sub', 'drag-over-sort');

                                // A) reorder existing positions
                                if (App.dragState && App.dragState.type === 'pos') {
                                    App.moveItem(App.dragState.sIdx, App.dragState.iIdx, sIdx, iIdx);
                                    App.dragState = null;
                                    return;
                                }

                                // B) library -> make sub-position under this item
                                e.stopPropagation();

                                const id = e.dataTransfer.getData("text");
                                const type = e.dataTransfer.getData("itemType"); // 'master_set' | 'master_set_group' | 'product'

                                if (!id || !type) return;

                                await App.addLibraryItemAsSubPosition(sIdx, iIdx, id, type);
                            };

                        }

                        let badgeHtml = '';
                        if(item.badge) {
                            const posCls = item.badge.pos==='tl'?'top-0 left-0':item.badge.pos==='tr'?'top-0 right-0':item.badge.pos==='bl'?'bottom-0 left-0':'bottom-0 right-0';
                            badgeHtml = item.badge.type==='text' ? `<div class="absolute ${posCls} bg-brand-primary text-white text-[8px] font-bold px-1 rounded z-10">${item.badge.text}</div>` : `<img src="${item.badge.src}" class="absolute ${posCls} w-6 h-6 object-contain z-10">`;
                        }
                        if(!item.active) badgeHtml += `<div class="absolute top-0 right-0 bg-red-500 text-white text-[8px] px-1 rounded z-20">HIDDEN</div>`;

                        const nameVal = forPrint ? item.name : `<input class="clean-input font-bold" value="${item.name}" onchange="App.updateItemDetails(${sIdx},${iIdx},'name',this.value)">`;
                        const descHtml = (item.desc_html || '').toString().trim();
                        const descFallback = App.escapeHtml(item.desc || '');
                        const descVal = forPrint
                        ? (descHtml ? descHtml : descFallback)
                        : `
                            <div class="editable-field p-2 rounded bg-slate-50 border border-dashed border-slate-200 hover:border-[#93c21c] cursor-pointer"
                                onclick="App.openDescModal(${sIdx},${iIdx},null)">
                            ${descHtml ? descHtml : (descFallback || `<span class="text-slate-400">Beschreibung bearbeiten…</span>`)}
                            </div>
                        `;

                        const tools = forPrint ? '' : `<div class="mt-1 flex gap-2 no-print"><button onclick="App.addSubItem(${sIdx},${iIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-plus"></i> Sub</button><button onclick="App.openPosSettings(${sIdx},${iIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-cog"></i></button><button onclick="App.removeItem(${sIdx},${iIdx})" class="text-[9px] text-red-300 hover:text-red-500"><i class="fa-solid fa-trash"></i></button></div>`;

                        row.innerHTML = `
                            <div>
                                <div class="pos-row-top">
                                    <div class="text-center">${posNum}</div>
                                    <div>${nameVal}</div>
                                    <div class="text-center">
                                        ${
                                            forPrint
                                            ? `${App.escapeHtml(item.qty)}`
                                            : `<input type="number" step="0.01" class="clean-input text-center font-bold"
                                                value="${App.escapeHtml(item.qty)}"
                                                onchange="App.updateItemDetails(${sIdx},${iIdx},'qty',this.value)">`
                                        }
                                        </div>

                                        <div class="text-center text-[10px] text-slate-500">
                                        ${
                                            forPrint
                                            ? `${App.escapeHtml(item.unit)}`
                                            : `<input type="text" class="clean-input text-center"
                                                value="${App.escapeHtml(item.unit)}"
                                                onchange="App.updateItemDetails(${sIdx},${iIdx},'unit',this.value)">`
                                        }
                                        </div>

                                        <div class="text-right font-mono text-[10px]">
                                        ${
                                            (isPauschal || App.shouldHidePrices(sec, item) || sec.config.hidePrices)
                                            ? `-`
                                            : (forPrint
                                                ? `${App.escapeHtml(epD)}`
                                                : `<input type="number" step="0.01" class="clean-input text-right font-mono"
                                                    value="${App.escapeHtml(item.price)}"
                                                    onchange="App.updateItemDetails(${sIdx},${iIdx},'price',this.value)"> €`)
                                        }
                                        </div>

                                    <div><i class="fa-solid fa-grip-lines text-slate-300 cursor-grab no-print"></i></div>
                                </div>
                                <div class="pos-row-bottom">
                                    <div class="prod-img-container" onclick="${!forPrint?`App.handleImageClick(${sIdx},${iIdx})`:''}"><img src="${item.img||'https://placehold.co/150?text='}" class="w-full h-full object-contain bg-white">${badgeHtml}</div>
                                    <div class="flex-1"><div class="text-[11px] text-slate-500 leading-relaxed">${descVal}</div>${tools}</div>
                                </div>
                                <div id="sub-items-${sIdx}-${iIdx}"></div>
                            </div>`;
                        
                        append(row);

                        // SUB ITEMS
                        if(item.subItems && item.subItems.length > 0) {
                            const subC = row.querySelector(`#sub-items-${sIdx}-${iIdx}`);
                            item.subItems.forEach((sub, subIdx) => {
                                if(!sub.active && !showHidden) return;
                                const subTotal = sub.price * sub.qty;
                                let subGp = subTotal.toLocaleString('de-DE') + ' €';
                                if(isPauschal || sec.config.hidePrices || item.hidePrices) subGp='-'; else if(isOpt || isAlt) subGp=`(${subGp})`;

                                const subRow = document.createElement('div');
                                subRow.className = "sub-pos-container sub-pos-grid";
                                const sName = forPrint ? sub.name : `<input class="clean-input" value="${sub.name}" onchange="App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'name',this.value)">`;
                                subRow.innerHTML = `<div class="text-right pr-2">${posNum}.${subIdx+1}</div><div>${sName}</div>
                                <div class="text-center">
                                    ${
                                        forPrint
                                        ? `${App.escapeHtml(sub.qty)}`
                                        : `<input type="number" step="0.01" class="clean-input text-center"
                                            value="${App.escapeHtml(sub.qty)}"
                                            onchange="App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'qty',this.value)">`
                                    }
                                    </div>

                                    <div class="text-center">
                                    ${
                                        forPrint
                                        ? `${App.escapeHtml(sub.unit)}`
                                        : `<input type="text" class="clean-input text-center"
                                            value="${App.escapeHtml(sub.unit)}"
                                            onchange="App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'unit',this.value)">`
                                    }
                                    </div>

                                </div><div class="text-right font-mono text-[9px]">
                                ${
                                    (isPauschal || App.shouldHidePrices(sec, item) || sec.config.hidePrices || sub.hidePrices)
                                        ? `-`
                                        : (forPrint
                                            ? `${Number(sub.price || 0).toLocaleString('de-DE')}`
                                            : `<input type="number" step="0.01" class="clean-input text-right font-mono"
                                                value="${App.escapeHtml(sub.price)}"
                                                onchange="App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'price',this.value)">`)
                                    }

                                </div><div class="text-right font-mono text-[9px]">${subGp}</div><div>${!forPrint?`<button onclick="App.removeItem(${sIdx},${iIdx},${subIdx})" class="text-red-300 hover:text-red-500"><i class="fa-solid fa-times"></i></button>`:''}</div>`;
                                subC.appendChild(subRow);
                            });
                        }
                    });
                }
                if(isPauschal) { const pr = document.createElement('div'); pr.className="flex justify-end mt-2 pr-16 font-bold text-slate-800 text-sm border-t border-slate-300 pt-2"; pr.innerHTML=`<span>Pauschalpreis:</span><span class="ml-8 font-mono">${sec.config.pauschalPrice.toLocaleString('de-DE')} €</span>`; append(pr); }
                if(!forPrint) { const btn = document.createElement('div'); btn.className="pb-4 pl-8"; btn.innerHTML=`<button onclick="App.addManualItem(${sIdx})" class="text-[10px] font-bold text-brand-primary flex items-center gap-1 hover:bg-brand-light px-2 py-1 rounded border border-dashed border-brand-primary"><i class="fa-solid fa-plus"></i> Position</button>`; append(btn); }
            });

            // Global Drop
            let dzG = document.createElement('div'); dzG.className = 'section-drop-zone border-2 border-dashed border-slate-300 rounded flex items-center justify-center text-slate-400 text-xs py-6 mt-4'; dzG.innerText = 'Neue Sektion'; dzG.ondragover=e=>e.preventDefault(); 
            dzG.ondrop=e=>{
                e.preventDefault();
                const id=e.dataTransfer.getData("text");
                const type=e.dataTransfer.getData("itemType");
                if(id){const ni=App.addSection();App.handleItemAdd(ni,id,type);App.renderQuotePage();}
            }; 
            append(dzG);

            // Totals
            let totalNet = 0; let activeTotal = 0;
            State.sections.forEach(s => {
                if (s.config.type === 'standard') {
                    let val = s.config.mode === 'pauschal' ? s.config.pauschalPrice : 0;
                    if(s.config.mode !== 'pauschal') {
                        s.items.forEach(i => { if(i.active) { val += i.price * i.qty; if(i.subItems) i.subItems.forEach(si => { if(si.active) val += si.price * si.qty; }); } });
                        val += (s.config.margin.type === 'percent' ? val * (s.config.margin.value/100) : s.config.margin.value);
                    }
                    totalNet += val; activeTotal += val;
                }
            });
            const tax = totalNet * (State.taxRate / 100); const gross = totalNet + tax;
            const sum = document.createElement('div'); sum.className="mt-8 p-6 bg-slate-50 rounded-lg border border-slate-200 break-inside-avoid";
            sum.innerHTML=`<div class="flex justify-between mb-2 text-sm font-bold text-slate-600"><span>Gesamtpreis netto</span><span>${totalNet.toLocaleString('de-DE',{minimumFractionDigits:2})} EUR</span></div><div class="flex justify-between mb-2 text-sm text-slate-500"><span>Umsatzsteuer ${State.taxRate}%</span><span>${tax.toLocaleString('de-DE',{minimumFractionDigits:2})} EUR</span></div><div class="flex justify-between mt-4 pt-4 border-t border-slate-300 text-xl font-black text-brand-primary"><span>Gesamtinvestitionskosten</span><span>${gross.toLocaleString('de-DE',{minimumFractionDigits:2})} EUR</span></div>`;
            append(sum);

            if(!forPrint) {
                document.getElementById('sidebar-grand-net').innerText = totalNet.toLocaleString('de-DE',{minimumFractionDigits:2})+' €';
                document.getElementById('sidebar-grand-gross').innerText = tax.toLocaleString('de-DE',{minimumFractionDigits:2})+' €';
                document.getElementById('sidebar-grand-total').innerText = gross.toLocaleString('de-DE',{minimumFractionDigits:2})+' €';
                document.getElementById('lbl-total-pages').innerText = pageIndex;

                App.renderCalculationSidebar(activeTotal);

                // ✅ ADD THESE:
                App.initThumbObserver();
                App.setActiveThumb(1);
                App.initThumbSortable();

            }

        },

        renderCalculationSidebar: (totalNet) => {
            const c = document.getElementById('calc-sidebar-content'); c.innerHTML='';
            State.sections.forEach((s, i) => {
                let secEK = 0; let secVK = 0;
                s.items.forEach(x => { if(x.active) { secEK+=x.ek*x.qty; secVK+=x.price*x.qty; if(x.subItems) x.subItems.forEach(y=>{if(y.active){secEK+=y.ek*y.qty; secVK+=y.price*y.qty;}}); } });
                
                let margin = s.config.margin.type==='percent' ? secVK*(s.config.margin.value/100) : s.config.margin.value;
                let finalVK = s.config.mode==='pauschal' ? s.config.pauschalPrice : secVK + margin;
                let percent = (s.config.type==='standard' && totalNet>0) ? ((finalVK/totalNet)*100).toFixed(1)+'%' : '-';

                c.innerHTML += `<div class="bg-white border border-slate-200 rounded p-3 text-xs mb-3 shadow-sm ${s.config.type!=='standard'?'opacity-75 bg-slate-50':''}"><div class="font-bold text-slate-700 mb-2 truncate border-b pb-1 flex justify-between"><span>${s.title||'Sektion'}</span><span class="text-[10px] bg-brand-light px-1 rounded text-slate-500">${percent}</span></div>${s.config.mode==='standard' ? `<div class="grid grid-cols-2 gap-y-1 text-slate-500 mb-2 border-b border-slate-50 pb-2"><span>EK:</span><span class="text-right">${secEK.toLocaleString('de-DE')}€</span><span>+Marge:</span><span class="text-right text-brand-primary">${margin.toLocaleString('de-DE')}€</span><span class="font-bold">VK:</span><span class="text-right font-bold">${finalVK.toLocaleString('de-DE')}€</span></div>` : ''}<div class="mb-2"><select onchange="App.updateSectionConfig(${i},'type',this.value)" class="w-full border rounded text-xs p-1"><option value="standard" ${s.config.type==='standard'?'selected':''}>Standard</option><option value="optional" ${s.config.type==='optional'?'selected':''}>Optional</option><option value="alternative" ${s.config.type==='alternative'?'selected':''}>Alternativ</option></select></div><div class="flex gap-4 mb-2"><label class="flex items-center gap-1 cursor-pointer"><input type="checkbox" ${s.config.hidePrices?'checked':''} onchange="App.updateSectionConfig(${i},'hidePrices',this.checked)"><span>Hide</span></label><label class="flex items-center gap-1 cursor-pointer"><input type="checkbox" ${s.config.mode==='pauschal'?'checked':''} onchange="App.updateSectionConfig(${i},'mode',this.checked)"><span>Pausch</span></label></div>${s.config.mode==='pauschal' ? `<input type="number" value="${s.config.pauschalPrice}" onchange="App.updateSectionConfig(${i},'pauschalPrice',this.value)" class="w-full border rounded p-1 font-mono text-right font-bold">` : `<div class="mt-1 flex items-center gap-2"><span class="font-bold text-brand-primary">Marge</span><input type="number" value="${s.config.margin.value}" onchange="App.updateSectionConfig(${i},'marginVal',this.value)" class="w-12 border rounded px-1 text-right"><select onchange="App.updateSectionConfig(${i},'marginType',this.value)" class="border rounded"><option value="fixed" ${s.config.margin.type==='fixed'?'selected':''}>€</option><option value="percent" ${s.config.margin.type==='percent'?'selected':''}>%</option></select></div>`}</div>`;
            });
        },

        // --- HELPERS ---
       createThumbnail: (idx, label) => {
            const nav = document.getElementById('nav-pane');
            const wrap = document.createElement('div');
            wrap.className = "thumb-wrapper";
            wrap.dataset.page = String(idx); // ✅ important

            const thumbBox = document.createElement('div');
            thumbBox.className = "thumb-scale-box";

            let sourcePage;
            if(idx === 1) sourcePage = document.getElementById('page-1');
            else sourcePage = document.getElementById('position-pages-container').children[idx-2];

            if(sourcePage) {
            const clone = sourcePage.cloneNode(true);
            clone.removeAttribute('id');
            clone.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));

            const srcInputs = sourcePage.querySelectorAll('input, textarea, select');
            const dstInputs = clone.querySelectorAll('input, textarea, select');
            srcInputs.forEach((inp, i) => {
                if(dstInputs[i]) {
                dstInputs[i].value = inp.value;
                if(inp.checked) dstInputs[i].checked = true;
                }
            });

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
                target.scrollIntoView({ behavior: 'smooth' });
                App.setActiveThumb(idx); // ✅ immediate feedback
            }
            };

            nav.appendChild(wrap);
        },
        openPrintPreview: () => { document.getElementById('print-preview-modal').classList.remove('hidden'); App.renderQuotePage(true); },
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

        dragStartTool: (ev, src) => { ev.dataTransfer.setData("type", "tool"); ev.dataTransfer.setData("src", src); },
        allowDrop: (ev) => { ev.preventDefault(); ev.currentTarget.classList.add('drag-over'); },
        drop: (ev, sIdx) => { ev.preventDefault(); ev.currentTarget.classList.remove('drag-over'); const id = ev.dataTransfer.getData("text"); const type=ev.dataTransfer.getData("itemType"); if(id) { App.handleItemAdd(sIdx, id, type); } },
        dropTool: (ev, pageIndex) => { ev.preventDefault(); const type = ev.dataTransfer.getData("type"); if(type !== 'tool') return; const src = ev.dataTransfer.getData("src"); const rect = ev.currentTarget.getBoundingClientRect(); State.placedImages.push({ id: Date.now(), src, pageIndex, x: ev.clientX - rect.left, y: ev.clientY - rect.top, width: 100 }); App.renderQuotePage(); },
        removeToolImage: (id) => { State.placedImages = State.placedImages.filter(i => i.id !== id); App.renderQuotePage(); },
        renderFloatingImages: (pageEl, pageIdx, forPrint) => { const images = State.placedImages.filter(img => img.pageIndex === pageIdx); images.forEach(img => { const el = document.createElement('div'); el.className = 'floating-element'; el.style.left = img.x + 'px'; el.style.top = img.y + 'px'; el.style.width = img.width + 'px'; el.innerHTML = `<img src="${img.src}" class="w-full h-auto">` + (forPrint?'':`<div class="delete-float" onclick="App.removeToolImage(${img.id})">x</div>`); if(!forPrint) { el.onmousedown = (e) => { e.stopPropagation(); let startX = e.clientX; let startY = e.clientY; let startLeft = img.x; let startTop = img.y; const onMove = (mv) => { el.style.left = (startLeft + mv.clientX - startX) + 'px'; el.style.top = (startTop + mv.clientY - startY) + 'px'; }; const onUp = (up) => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); img.x = startLeft + up.clientX - startX; img.y = startTop + up.clientY - startY; }; document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp); }; } pageEl.appendChild(el); }); },
        syncDocData: (field, value) => { if(field === 'offerId') State.offerId = value; if(field === 'custId') State.custId = value; document.querySelectorAll('.sync-offer-id').forEach(el => el.innerText = State.offerId); },
        addManualItem: (sIdx) => { State.sections[sIdx].items.push({ name:'Neue Position', desc:'Beschreibung', price:0, ek:0, margin:0, qty:1, unit:'Stk', subItems:[] }); App.renderQuotePage(); },
        
        // Drag Sort Handlers
        dragStartPos: (ev, sIdx, iIdx) => { App.dragState = { type: 'pos', sIdx, iIdx }; ev.dataTransfer.effectAllowed = 'move'; ev.dataTransfer.setData("text/plain", JSON.stringify({type:'pos', sIdx, iIdx})); },
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
        updateTaxRate: (v) => { State.taxRate = parseFloat(v) || 0; document.getElementById('lbl-tax-rate').innerText = State.taxRate; App.renderQuotePage(); },
        removeItem: (sIdx, iIdx, subIdx=null) => { if(subIdx!==null) State.sections[sIdx].items[iIdx].subItems.splice(subIdx,1); else State.sections[sIdx].items.splice(iIdx,1); App.renderQuotePage(); },
        addSubItem: (sIdx, iIdx) => { State.sections[sIdx].items[iIdx].subItems.push({name:"Position", price:0, ek:0, margin:0, active:true, qty:1, unit:'Stk'}); App.renderQuotePage(); },
        
        // Settings
        openPosSettings: (sIdx, iIdx) => { const item = State.sections[sIdx].items[iIdx]; State.tempPosSettings = { sIdx, iIdx }; document.getElementById('setting-ek').value = item.ek; document.getElementById('setting-margin').value = item.margin; document.getElementById('setting-vk').value = item.price; document.getElementById('setting-pauschal').checked = item.isPauschal; document.getElementById('setting-hide-price').checked = item.hidePrices; document.getElementById('setting-active').checked = item.active; document.getElementById('pos-settings-modal').classList.remove('hidden'); },
        closePosSettings: () => { State.tempPosSettings = null; document.getElementById('pos-settings-modal').classList.add('hidden'); },
        calcPosSettings: (isVk) => { const ek = parseFloat(document.getElementById('setting-ek').value)||0; const m = parseFloat(document.getElementById('setting-margin').value)||0; if(isVk) { const vk=parseFloat(document.getElementById('setting-vk').value)||0; if(ek>0) document.getElementById('setting-margin').value = ((vk-ek)/ek*100).toFixed(2); } else { document.getElementById('setting-vk').value = (ek*(1+m/100)).toFixed(2); } },
        savePosSettings: () => { if(!State.tempPosSettings) return; const {sIdx, iIdx} = State.tempPosSettings; const item = State.sections[sIdx].items[iIdx]; item.ek = parseFloat(document.getElementById('setting-ek').value)||0; item.margin = parseFloat(document.getElementById('setting-margin').value)||0; item.price = parseFloat(document.getElementById('setting-vk').value)||0; item.isPauschal = document.getElementById('setting-pauschal').checked; item.hidePrices = document.getElementById('setting-hide-price').checked; item.active = document.getElementById('setting-active').checked; App.renderQuotePage(); App.closePosSettings(); },
        
        // Modal
         openSetModal: async (id) => {
            try {
                const response = await fetch(`${API_BASE}/master-sets/${id}`, {
                headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const p = await response.json();

                const fmt = (n) => (Number(n || 0)).toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
                });

                const esc = (s) => (s ?? '').toString()
                .replaceAll('&','&amp;')
                .replaceAll('<','&lt;')
                .replaceAll('>','&gt;')
                .replaceAll('"','&quot;')
                .replaceAll("'","&#039;");

                // --- title + description ---
                document.getElementById('modal-title').innerText = p.name || `MasterSet #${id}`;

                // IMPORTANT:
                // If backend returns HTML as p.description_html or p.description,
                // show it nicely (prefer HTML, fallback to text).
                const descEl = document.getElementById('modal-desc');

                const htmlDesc = (p.description_html || p.description || '').toString().trim();
                if (htmlDesc) {
                // If it's plain text, keep it readable; if it's html, render it.
                const looksLikeHtml = /<\/?[a-z][\s\S]*>/i.test(htmlDesc);
                descEl.innerHTML = looksLikeHtml
                    ? htmlDesc
                    : esc(htmlDesc).replaceAll('\n', '<br>');
                } else {
                descEl.innerHTML = `<span class="text-slate-400">Keine Beschreibung</span>`;
                }

                // --- MATERIALS (components + children) ---
                const m = document.getElementById('modal-materials');
                m.innerHTML = '';

                const addRow = (label, qty, unit, unitPrice, total, isChild=false, desc='') => {
                const prefix = isChild ? `<span class="text-slate-400">↳</span> ` : '';
                const descLine = desc ? `<div class="text-xs text-slate-400 mt-0.5">${esc(desc)}</div>` : '';
                m.innerHTML += `
                    <tr>
                    <td class="px-4 py-2">
                        <div class="font-bold text-slate-800 text-sm">${prefix}${esc(label)}</div>
                        <div class="text-xs text-slate-400">${esc(unit || '')}</div>
                        ${descLine}
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="text-xs text-slate-500">${fmt(qty)} × ${fmt(unitPrice)} €</div>
                        <div class="font-bold text-slate-800">${fmt(total)} €</div>
                    </td>
                    </tr>
                `;
                };

                (p.components || []).forEach(comp => {
                // prefer component variant text if backend provides it
                const compDesc = (comp.description_variant_text || comp.description_text || comp.description || '').toString().trim();
                addRow(comp.name, comp.qty, comp.unit, comp.unit_price, comp.total, false, compDesc);

                (comp.children || []).forEach(ch => {
                    const childDesc = (ch.description_variant_text || ch.description_text || ch.description || '').toString().trim();
                    addRow(ch.name, ch.qty, ch.unit, ch.unit_price, ch.total, true, childDesc);
                });
                });

                if (!m.innerHTML.trim()) {
                m.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="2">Keine Komponenten</td></tr>`;
                }

                // --- LABOR ---
                const l = document.getElementById('modal-labor');
                l.innerHTML = '';

                (p.labor || []).forEach(x => {
                l.innerHTML += `
                    <tr>
                    <td class="px-4 py-2">
                        <div class="font-bold text-slate-800">${esc(x.name)}</div>
                    </td>
                    <td class="px-4 py-2 text-center font-mono">${fmt(x.hours)}</td>
                    <td class="px-4 py-2 text-right">
                        <div class="text-xs text-slate-500">${fmt(x.hourly_rate)} €/Std</div>
                        <div class="font-bold text-slate-800">${fmt(x.total)} €</div>
                    </td>
                    </tr>
                `;
                });

                if (!l.innerHTML.trim()) {
                l.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="3">Keine Dienstleistungen</td></tr>`;
                }

                // add-to-offer target: add into first section by default
                document.getElementById('modal-add-btn').onclick = () => {
                const targetSection = 0;
                App.handleItemAdd(targetSection, id, 'master_set');
                App.closeModal();
                };

                document.getElementById('set-modal').classList.remove('hidden');
            } catch (e) {
                console.error(e);
            }
            },


        closeModal: () => document.getElementById('set-modal').classList.add('hidden'),
        save: () => alert("Angebot gespeichert (Not implemented in this demo)"),

        // Badges
        handleImageClick: (sIdx, iIdx) => { App.editingImage = { sIdx, iIdx }; document.getElementById('img-upload-input').click(); },
        handleBadgeClick: (sIdx, iIdx) => { State.editingBadge = { sIdx, iIdx, pos: 'tl', type: '', text: '' }; document.getElementById('badge-modal').classList.remove('hidden'); },
        closeBadgeModal: () => document.getElementById('badge-modal').classList.add('hidden'),
        setBadgePos: (pos) => { if(State.editingBadge) State.editingBadge.pos = pos; },
        saveBadgeConfig: () => { if(!State.editingBadge) return; const { sIdx, iIdx, pos, tempImg } = State.editingBadge; const val = document.getElementById('badge-type-select').value; let badgeObj = null; if(val === 'image' && tempImg) badgeObj = { type: 'image', src: tempImg, pos: pos }; else if (val !== '' && val !== 'image') badgeObj = { type: 'text', text: val, pos: pos }; else if (val === 'image' && !tempImg) { document.getElementById('badge-upload-input').click(); return; } State.sections[sIdx].items[iIdx].badge = badgeObj; App.renderQuotePage(); App.closeBadgeModal(); }
    };

    App.renderBuilderPalette = () => {
        const box = document.getElementById('builder-palette');
        if (!box) return;

        box.innerHTML = BUILDER_PALETTE.map(t => `
            <div class="draggable-item bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:border-[#93c21c] flex items-center gap-3"
                data-template="${t.type}">
            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-600">
                ${t.icon}
            </div>
            <div class="min-w-0">
                <div class="font-black text-slate-800 text-xs">${t.label}</div>
                <div class="text-[10px] text-slate-400">Drag & Drop</div>
            </div>
            </div>
        `).join('');
    };

    App.initBuilderPaletteDnD = () => {
        const src = document.getElementById('builder-palette');
        if (!src || typeof Sortable === 'undefined') return;

        new Sortable(src, {
            group: { name: 'builder', pull: 'clone', put: false },
            sort: false,
            animation: 150,
            draggable: '.draggable-item',
            onClone: (evt) => {
            // optional: style clone
            evt.clone.style.opacity = '0.9';
            }
        });
    };

    App.initListDropZoneDnD = () => {
        const dz = document.getElementById('list-drop-zone');
        if (!dz || typeof Sortable === 'undefined') return;

        new Sortable(dz, {
            group: { name: 'builder', pull: false, put: true },
            animation: 150,
            sort: false,
            onAdd: (evt) => {
            const tpl = evt.item?.dataset?.template;
            evt.item.remove(); // remove clone from DOM
            App.applyTemplateDrop(tpl, { target: 'list' });
            }
        });
    };

    App.initA4DropZonesDnD = () => {
        document.querySelectorAll('.section-drop-zone').forEach((zone) => {
            if (zone._sortable) return;

            zone._sortable = new Sortable(zone, {
            group: { name: 'builder', pull: false, put: true },
            animation: 150,
            sort: false,
            onAdd: (evt) => {
                const tpl = evt.item?.dataset?.template;
                const pageNo = Number(zone.dataset.page || State.currentPageNo || 1);
                const secId = zone.dataset.sectionId || null;

                evt.item.remove();
                App.applyTemplateDrop(tpl, { target: 'a4', pageNo, secId });
            }
            });
        });
    };

        App.applyTemplateDrop = (tpl, ctx) => {
        if (!tpl) return;

        if (tpl === 'section') {
            App.addSection();                // you already have this
            App.renderListView?.();
            App.renderQuotePage?.();
            return;
        }

        // Ensure at least one section exists
        if (!State.sections.length) App.addSection();

        // Determine target section
        let sIdx = 0;
        if (ctx?.secId) {
            sIdx = State.sections.findIndex(s => s.id === ctx.secId);
            if (sIdx < 0) sIdx = 0;
        }

        // Create position item
        if (tpl === 'position') {
            const sec = State.sections[sIdx];
            sec.items = sec.items || [];
            sec.items.push({
            id: 'i' + Date.now(),
            name: 'Neue Position',
            qty: 1,
            unit: 'Stk',
            ek: 0,
            margin: { value: 20, type: 'percent' },
            active: true,
            desc: '',
            desc_html: '',
            subItems: []
            });

            App.renderListView?.();
            App.renderQuotePage?.();
            return;
        }

        if (tpl === 'note') {
            const sec = State.sections[sIdx];
            sec.items = sec.items || [];
            sec.items.push({
            id: 'i' + Date.now(),
            name: 'Notiz',
            qty: 1,
            unit: 'Psch',
            ek: 0,
            margin: { value: 0, type: 'fixed' },
            active: true,
            desc: 'Freitext...',
            desc_html: '<p>Freitext…</p>',
            isNote: true,
            subItems: []
            });

            App.renderListView?.();
            App.renderQuotePage?.();
            return;
        }

        if (tpl === 'set') {
            // Here you can open your existing set modal OR insert a placeholder set-root item
            App.openSetPicker?.(); // optional
        }
        };


        App.renderListView = () => {
            const root = document.getElementById('list-sections');
            if (!root) return;

            const esc = App.escapeHtml;

            root.innerHTML = State.sections
            .filter(s => !s._pageBreak)
            .map((sec, sIdx) => `
            <div class="bg-white border border-slate-200 rounded-xl mb-3 overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <input class="font-black text-slate-800 bg-transparent outline-none w-full"
                        value="${esc(sec.title || 'Sektion')}"
                        oninput="State.sections[${sIdx}].title=this.value; App.renderQuotePage();">
                <div class="flex items-center gap-2">
                    <button class="text-slate-500 hover:text-[#93c21c]" onclick="App.addItemToSection(${sIdx})">
                    <i class="fa-solid fa-plus"></i>
                    </button>
                    <button class="text-slate-400 hover:text-red-600" onclick="App.deleteSection(${sIdx})">
                    <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                </div>

                <div class="p-3">
                ${(sec.items||[]).map((it, iIdx) => `
                    <div class="flex items-start gap-3 py-2 border-b border-slate-100 last:border-0">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500">
                        <i class="fa-solid ${it.isNote ? 'fa-pen' : 'fa-cube'}"></i>
                    </div>
                    <div class="flex-1">
                        <input class="w-full font-bold bg-transparent outline-none"
                            value="${esc(it.name || '')}"
                            oninput="State.sections[${sIdx}].items[${iIdx}].name=this.value; App.renderQuotePage();">
                        <div class="text-[11px] text-slate-400 mt-1">${esc(it.desc || '')}</div>
                    </div>
                    <button class="text-slate-300 hover:text-[#93c21c]" onclick="App.openDescModal(${sIdx},${iIdx})">
                        <i class="fa-solid fa-align-left"></i>
                    </button>
                    </div>
                `).join('')}
                </div>
            </div>
            `).join('');
        };

        App.addItemToSection = (sIdx) => App.applyTemplateDrop('position', { secId: State.sections[sIdx]?.id });
        App.deleteSection = (sIdx) => {
        if (!confirm('Sektion löschen?')) return;
        State.sections.splice(sIdx, 1);
        App.renderListView();
        App.renderQuotePage();
        };






    window.addEventListener('DOMContentLoaded', App.init);

    /* ✅ 4) Add these helpers anywhere inside your <script> (window.App scope) */

    App.Wizard.initObjectSelect2 = () => {
        const el = $('#wiz-object-select');
        if (!el.length) return;

        // destroy old instance (when customer changes)
        if (el.hasClass('select2-hidden-accessible')) {
            el.select2('destroy');
        }

        el.select2({
            placeholder: 'Objekt/Produkte auswählen…',
            width: '100%',
            closeOnSelect: false,          // keep open for multi-select
            allowClear: true
        });

        // keep your existing logic working
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

    App.pickEK = (p) => {
    // EK candidates (most common DB columns)
    const candidates = [
        p?.ek,
        p?.ek_price,
        p?.purchase_price,
        p?.distributor_price,
        p?.net_price,
        p?.best_price,
        p?.price, // last fallback if only one price exists
    ];
    for (const c of candidates) {
        const n = App.num(c, NaN);
        if (Number.isFinite(n) && n > 0) return n;
    }
    return 0;
    };

    App.pickVK = (p, ek=0) => {
    // VK candidates (if you have them)
    const candidates = [
        p?.vk,
        p?.sale_price,
        p?.selling_price,
        p?.price,
    ];
    for (const c of candidates) {
        const n = App.num(c, NaN);
        if (Number.isFinite(n) && n > 0) return n;
    }
    // if no VK given, keep 0 (or derive from EK if you want)
    return 0;
    };

    // item-level types: standard | optional | alternative
    App.getLineType = (item) => (item?.lineType || 'standard');
    App.isLineIncluded = (item) => App.getLineType(item) === 'standard';

    // Toggle helper (UI)
    App.toggleItemLineType = (sIdx, iIdx, toType) => {
    const it = State.sections?.[sIdx]?.items?.[iIdx];
    if (!it) return;

    const current = App.getLineType(it);
    if (toType) {
        it.lineType = (current === toType) ? 'standard' : toType;
    } else {
        // cycle
        it.lineType = current === 'standard' ? 'optional' : (current === 'optional' ? 'alternative' : 'standard');
    }

    App.renderQuotePage();
    };
</script>


<!-- ✅ PATCH: Add these helpers ONCE (inside your <script>, near other App.* helpers) -->
<script>
  // ------------------------------------------------------------
  // ✅ IMAGE HELPERS (robust: supports many backend field names)
  // ------------------------------------------------------------
  App.normalizeImgUrl = (src) => {
    const s = (src ?? '').toString().trim();
    if (!s) return '';
    if (s.startsWith('data:')) return s;                 // base64
    if (s.startsWith('http://') || s.startsWith('https://')) return s;
    if (s.startsWith('//')) return window.location.protocol + s;
    if (s.startsWith('/')) return window.location.origin + s;
    return s; // already relative like "storage/..." or "uploads/..."
  };

  App.pickImage = (obj, fallback = '') => {
    if (!obj) return App.normalizeImgUrl(fallback);

    // common fields
    const candidates = [
      obj.image,
      obj.image_url,
      obj.imageUrl,
      obj.img,
      obj.img_url,
      obj.thumbnail,
      obj.thumb,
      obj.photo,
      obj.photo_url,
      obj.logo,
      obj.url,

      // nested media arrays/objects (common in Laravel resources)
      obj.media?.url,
      obj.media?.original_url,
      obj.media?.[0]?.original_url,
      obj.media?.[0]?.url,

      obj.images?.[0],
      obj.images?.[0]?.url,
      obj.images?.[0]?.original_url,

      obj.files?.[0]?.url,
      obj.files?.[0]?.original_url,
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

</body>
</html>