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

            <!-- MAIN TABS (A4 / LIST) -->
            <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
            <button id="main-tab-a4"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c] bg-white shadow"
                    onclick="App.Tabs.switch('a4')">
                <i class="fa-solid fa-file-lines mr-2"></i>A4 Angebot
            </button>
            <button id="main-tab-list"
                    class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c]"
                    onclick="App.Tabs.switch('list')">
                <i class="fa-solid fa-list-check mr-2"></i>List View
            </button>
            <button id="main-tab-settings" class="px-3 py-1.5 rounded-lg text-xs font-black text-slate-600 hover:text-[#93c21c]" onclick="App.Tabs.switch('settings')">
                <i class="fa-solid fa-sliders mr-2"></i>Einstellung
            </button>
            </div>

            <div class="flex items-center gap-3" >
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
                </div>
            </aside>

            <!-- NAV & CANVAS -->
            <div class="flex flex-1 overflow-hidden relative">
                <div id="nav-pane" class="thumb-container no-print scroller"></div> <!-- Thumbnails -->
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
                        <div class="text-[13px] text-slate-700 leading-relaxed space-y-4 editable-field p-2 hover:bg-slate-50 rounded -ml-2" contenteditable="true"><p>Sehr geehrter Herr <span id="doc-cust-lastname">Mustermann</span>,</p><p>wir freuen uns, Ihnen dieses Dokument unterbreiten zu dürfen.</p><p>Mit sonnigen Grüßen<br><span class="font-bold" id="doc-team-name">Ihr SOLAR-ASPEKT-Team</span></p></div>
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
                    <div class="max-w-7xl mx-auto space-y-4">
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-black text-slate-500 uppercase tracking-wider">Kalkulation (List View)</div>
                                <div class="text-lg font-black text-slate-800">Sektionen, Positionen, Sets & DB1</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="bg-white border border-slate-300 hover:border-[#93c21c] px-3 py-2 rounded-xl text-xs font-black text-slate-700"
                                        onclick="App.addSection()">
                                <i class="fa-solid fa-folder-plus mr-2"></i>Sektion
                                </button>
                                <button class="bg-[#93c21c] text-white px-3 py-2 rounded-xl text-xs font-black shadow hover:brightness-105"
                                        onclick="App.ListView.addPositionQuick()">
                                <i class="fa-solid fa-plus mr-2"></i>Position
                                </button>
                            </div>
                            </div>

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
        <div id="pos-settings-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 modal-overlay" onclick="App.closePosSettings()"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-[450px] overflow-hidden animate-fadeIn"><div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="font-bold text-slate-800">Position bearbeiten</h3><button onclick="App.closePosSettings()" class="text-slate-400"><i class="fa-solid fa-times"></i></button></div><div class="p-4 space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-slate-500 mb-1">Menge</label><input type="number" step="0.01" id="setting-qty" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Einheit</label><input type="text" id="setting-unit" class="w-full border rounded p-2 text-sm"></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Einkaufspreis (EK)</label><input type="number" id="setting-ek" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div><div><label class="block text-xs font-bold text-slate-500 mb-1">Marge (%)</label><input type="number" id="setting-margin" class="w-full border rounded p-2 text-sm" oninput="App.calcPosSettings()"></div></div><div class="bg-[#f0fdf4] p-3 rounded border border-[#93c21c]"><div class="flex justify-between items-center"><span class="text-xs font-bold text-[#93c21c]">Verkaufspreis (VK) pro Einheit</span><input type="number" id="setting-vk" class="w-24 text-right bg-transparent font-bold font-mono outline-none" oninput="App.calcPosSettings(true)"></div></div><div class="space-y-2 pt-2 border-t border-slate-100"><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-pauschal" class="accent-[#93c21c]"> <span>Als Pauschalposition</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-price" class="accent-[#93c21c]"> <span>Preise ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-numbering" class="accent-[#93c21c]"> <span>Nummerierung ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-hide-image" class="accent-[#93c21c]"> <span>Bild ausblenden</span></label><label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" id="setting-active" class="accent-[#93c21c]"> <span>Position Aktiv</span></label></div><button onclick="App.savePosSettings()" class="w-full bg-[#93c21c] text-white rounded py-2 font-bold text-sm">Speichern</button></div></div></div>    <!-- ✅ Set Modal (rewritten, clean + readable, same IDs/hooks kept) -->
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
                    freight: { active: true, val: 150 },
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
            item_type: "product", productId: null, component_id: null, name: "", desc_html: "", desc: "",
            img: "", showImage: true, price: 0, ek: 0, margin: 0, active: true, hidePrices: false,
            qty: 1, unit: "Stk", subItems: [], ...overrides,
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

        updatePosStatus: (sIdx, iIdx, subIdx, val) => {
            let target = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx];
            target.status = val;
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
        },

        handleItemAdd: async (sIdx, id, typeFromDrag = null) => {
        ensureSection(sIdx);
        const type = resolveType(typeFromDrag);
        const rawId = id;
        const origin = window.location.origin;
        const apiUrl = (path, params = {}) => {
            const url = new URL(path, origin);
            Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, String(v)));
            return url;
        };

        const safeRender = () => { if (typeof App?.renderQuotePage === "function") App.renderQuotePage(); };

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
                        unit: "Stk",
                        qty: 1,
                        showImage: false,
                        img: App.pickImage(data),
                        subItems: [],
                        price: 0, 
                    });

                    const itemsArray = Array.isArray(data?.items) ? data.items : [];
                    let setSum = 0;

                    itemsArray.forEach(itemData => {
                        // 1. Add the Parent Component (Level 2)
                        if (itemData.type === 'component') {
                            const parentLine = buildBaseItem({
                                item_type: "master_set_component",
                                component_id: itemData.id,
                                name: itemData.name || "Komponente",
                                qty: itemData.qty || 1,
                                unit: itemData.unit || "Stk",
                                price: itemData.unit_price || 0,
                                ek: itemData.ek || 0,
                                desc_html: pickDescHtml(itemData),
                                desc: pickDescText(itemData),
                                img: App.pickImage(itemData),
                                showImage: true,
                                depth: 1 // ✅ Level 1 inside the set
                            });
                            
                            setSum += calcLineTotal(parentLine);
                            setItem.subItems.push(parentLine);

                            // 2. ✅ Check for Sub-Components (Level 3 / Children)
                            if (Array.isArray(itemData.children) && itemData.children.length > 0) {
                                itemData.children.forEach(childData => {
                                    const childLine = buildBaseItem({
                                        item_type: "master_set_component_child",
                                        component_id: childData.id,
                                        name: childData.name || "Unterkomponente",
                                        qty: childData.qty || 1,
                                        unit: childData.unit || "Stk",
                                        price: childData.unit_price || 0,
                                        ek: childData.ek || 0, // Ensure your backend sends EK for children if needed
                                        desc_html: pickDescHtml(childData),
                                        desc: pickDescText(childData),
                                        img: App.pickImage(childData),
                                        showImage: true,
                                        depth: 2, // ✅ Level 2 inside the set (Indented more)
                                        isChildNode: true // Marker for UI
                                    });

                                    // Add child price to set total
                                    setSum += calcLineTotal(childLine);
                                    setItem.subItems.push(childLine);
                                });
                            }
                        } 
                        else if (itemData.type === 'labor') {
                            // ... (Your existing labor logic logic remains here) ...
                            // Copy your existing labor HTML generation code here
                            
                            let laborHtml = "";
                            if (itemData.children && itemData.children.length > 0) {
                                laborHtml = `<table class="w-full text-xs mt-2 border-collapse"><thead class="border-b border-slate-200 text-slate-500"><tr><th class="text-left py-1">Qualifikation</th><th class="text-center py-1">Stunden</th><th class="text-right py-1">Satz</th><th class="text-right py-1">Gesamt</th></tr></thead><tbody>`;
                                itemData.children.forEach(ch => {
                                    laborHtml += `<tr><td class="py-1">${ch.qualification_name}</td><td class="text-center py-1">${ch.hours}</td><td class="text-right py-1">${ch.hourly_rate} €</td><td class="text-right py-1 font-bold">${ch.total} €</td></tr>`;
                                });
                                laborHtml += `</tbody></table>`;
                            }

                            const line = buildBaseItem({
                                item_type: "labor",
                                name: itemData.name || "Montage / Arbeitsleistung",
                                qty: itemData.qty || 1,
                                unit: itemData.unit || "Std",
                                price: itemData.total || 0,
                                ek: 0,
                                desc_html: laborHtml,
                                showImage: false,
                                depth: 1
                            });
                            setSum += line.price;
                            setItem.subItems.push(line);
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
                const sets = Array.isArray(data?.master_sets) ? data.master_sets : Array.isArray(data?.sets) ? data.sets : [];

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
                try { data = await fetchJson(url); } catch (_) { data = null; }
                const p = data?.data ?? data ?? {};

                const item = buildBaseItem({
                    item_type: "product",
                    productId: safeNum(p?.id ?? rawId),
                    name: safeStr(p?.name ?? p?.product, `Produkt ID: ${rawId}`),
                    desc_html: pickDescHtml(p),
                    desc: pickDescText(p),
                    img: App.pickImage(p),
                    showImage: true,
                    price: safeNum(p?.price ?? p?.vk ?? 0),
                    ek: safeNum(p?.ek ?? p?.cost ?? 0),
                    margin: safeNum(p?.margin ?? 0),
                    qty: 1,
                    unit: safeStr(p?.unit ?? "Stk"),
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
                    (data.items || []).forEach(itemData => {
                        if (itemData.type === 'component') {
                            totalSet += safeNum(itemData.unit_price, 0) * safeNum(itemData.qty, 1);
                        } else if (itemData.type === 'labor') {
                            totalSet += safeNum(itemData.total, 0);
                        }
                    });

                    pushSub({
                        item_type: 'sub_master_set',
                        productId: safeNum(data.id),
                        name: data.name || `MasterSet #${id}`,
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),
                        qty: 1, unit: 'Stk',
                        price: safeNum(data.total_price ?? data.price ?? totalSet),
                        img: App.pickImage(data),
                    });
                    App.renderQuotePage();
                    return;
                }

                if (typeFromDrag === 'product') {
                    const url = new URL(`${API_BASE}/products/${id}`, window.location.origin);
                    url.searchParams.set('context', 'angebot');
                    let resp = null;
                    try { resp = await fetchJson(url); } catch (_) { resp = null; }
                    const data = resp?.data ?? resp ?? {};

                    pushSub({
                        item_type: 'sub_product',
                        productId: safeNum(data?.id ?? id),
                        name: (data?.name || data?.product || `Produkt #${id}`).toString(),
                        desc_html: pickDescHtml(data),
                        desc: pickDescText(data),
                        qty: 1, unit: (data?.unit || 'Stk').toString(),
                        price: safeNum(data?.price ?? data?.best_price ?? 0),
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

            document.getElementById('doc-team-name').innerText = `Ihr ${State.companyName}-Team`;
            document.getElementById('footer-company').innerText = `${State.companyName} GmbH`;
            document.getElementById('doc-company-header').innerText = `${State.companyName} GmbH • Am Kappengraben 10 • 61273 Wehrheim`;

            if(State.sections.length === 0) App.addSection('1. Hauptpositionen', false);
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

            // Initialize Thumbs
            if (!forPrint) {
                App.createThumbnail(1, 'Anschreiben');
                App.createThumbnail(pageIndex, 'Positionen 1');
            }
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

                    // Add Thumb & Floaters
                    if (!forPrint) App.createThumbnail(pageIndex, `Positionen ${pageIndex - 1}`);
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
                const isLocked = State.sections[sIdx].isLocked;
                
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

                const total = item.price * item.qty;
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
                const unitVal = forPrint ? App.escapeHtml(item.unit) : `<input type="text" class="clean-input text-center" value="${App.escapeHtml(item.unit)}" onchange="${ctxFn}'unit',this.value)">`;
                
                const epDisplay = hidePrices ? '-' : item.price.toLocaleString('de-DE') + ' €';
                const gpDisplay = hidePrices ? '-' : ((isItemOpt || isItemAlt) ? `(${total.toLocaleString('de-DE')} €)` : total.toLocaleString('de-DE') + ' €');

                // --- Description ---
                const descHtml = (item.desc_html || '').toString().trim();
                const descFallback = App.escapeHtml(item.desc || '');
                const descVal = forPrint
                    ? (descHtml || descFallback)
                    : `<div class="editable-field p-2 rounded bg-slate-50 border border-dashed border-slate-200 hover:border-[#93c21c] cursor-pointer min-h-[1.5rem]" 
                       onclick="App.openDescModal(${sIdx},${iIdx},${subIdx !== null ? subIdx : 'null'})">
                       ${descHtml || descFallback || `<span class="text-slate-400">Beschreibung...</span>`}
                       </div>`;

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
                if (!forPrint && !isLocked) {
                    if (level === 0) {
                        dragAttrs = `draggable="true" ondragstart="App.dragStartPos(event, ${sIdx}, ${iIdx})" ondragover="event.preventDefault(); this.classList.add('drag-over-sort');" ondragleave="this.classList.remove('drag-over-sort')" ondrop="event.preventDefault(); this.classList.remove('drag-over-sort'); if(App.dragState?.type==='pos') App.moveItem(App.dragState.sIdx, App.dragState.iIdx, ${sIdx}, ${iIdx});"`;
                    }
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
                            <div>${!forPrint ? `<i class="fa-solid fa-grip-lines text-slate-300 cursor-grab no-print"></i>` : ''}</div>
                        </div>
                        <div class="pos-row-bottom ${indentStyles}">
                            ${imgHtml}
                            <div class="flex-1"><div class="text-[11px] text-slate-500 leading-relaxed">${descVal}</div>${tools}</div>
                        </div>
                    </div>`;
            };

            // 5. Main Render Loop
            let totalNet = 0;
            let activeTotal = 0;

            State.sections.forEach((sec, sIdx) => {
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
                    if (!forPrint) App.createThumbnail(pageIndex, `Positionen ${pageIndex - 1}`);
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
                                    aggSum += (parseFloat(sub.price) || 0) * (parseFloat(sub.qty) || 1);
                                    aggEk += (parseFloat(sub.ek) || 0) * (parseFloat(sub.qty) || 1);
                                }
                            });
                            if (!item.isPauschal) item.price = aggSum;
                            item.ek = aggEk;
                        }

                        const total = item.price * item.qty;
                        
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

                        // --- Render Children (If they exist in a nested array - standard recursive approach) ---
                        // If your data structure is nested (item.children or item.subItems)
                        if (item.subItems && item.subItems.length > 0) {
                            // Create a container to force them to stay together if possible
                            // But `addToPage` handles breaks, so we loop linear.
                            const parentStrForChildren = (level === 0) ? String(posCounter - 1).padStart(3, '0') : currentPosStr;
                            
                            // Important: If you FLATTENED the list in Step 1, we don't recurse here.
                            // If you have `subItems` on Level 0, we loop them.
                            // The `createRowHtml` handles indentation based on `item.depth`.
                            
                            item.subItems.forEach((sub, sIdx2) => {
                                if (!sub.active && !showHidden) return;
                                
                                const subLevel = sub.depth || 1; // 1 or 2
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
                    btn.className = "pb-4 pl-8";
                    btn.innerHTML = `<button onclick="App.addManualItem(${sIdx})" class="text-[10px] font-bold text-brand-primary flex items-center gap-1 hover:bg-brand-light px-2 py-1 rounded border border-dashed border-brand-primary"><i class="fa-solid fa-plus"></i> Position</button>`;
                    addToPage(btn);
                }
            });

            // 6. Global Drop & Totals
            if (!forPrint) {
                let dzG = document.createElement('div');
                dzG.className = 'section-drop-zone border-2 border-dashed border-slate-300 rounded flex items-center justify-center text-slate-400 text-xs py-6 mt-4';
                dzG.innerText = 'Neue Sektion';
                dzG.ondragover = e => e.preventDefault();
                dzG.ondrop = e => {
                    e.preventDefault();
                    const id = e.dataTransfer.getData("text");
                    const type = e.dataTransfer.getData("itemType");
                    if (id) { const ni = App.addSection(); App.handleItemAdd(ni, id, type); App.renderQuotePage(); }
                };
                addToPage(dzG);
            }

            const tax = totalNet * (State.taxRate / 100);
            const gross = totalNet + tax;
            const sum = document.createElement('div');
            sum.className = "mt-8 p-6 bg-slate-50 rounded-lg border border-slate-200 break-inside-avoid";
            sum.innerHTML = `<div class="flex justify-between mb-2 text-sm font-bold text-slate-600"><span>Gesamtpreis netto</span><span>${totalNet.toLocaleString('de-DE', { minimumFractionDigits: 2 })} EUR</span></div><div class="flex justify-between mb-2 text-sm text-slate-500"><span>Umsatzsteuer ${State.taxRate}%</span><span>${tax.toLocaleString('de-DE', { minimumFractionDigits: 2 })} EUR</span></div><div class="flex justify-between mt-4 pt-4 border-t border-slate-300 text-xl font-black text-brand-primary"><span>Gesamtinvestitionskosten</span><span>${gross.toLocaleString('de-DE', { minimumFractionDigits: 2 })} EUR</span></div>`;
            addToPage(sum);

            // 7. Update UI Stats
            if (!forPrint) {
                document.getElementById('sidebar-grand-net').innerText = totalNet.toLocaleString('de-DE', { minimumFractionDigits: 2 }) + ' €';
                document.getElementById('sidebar-grand-gross').innerText = tax.toLocaleString('de-DE', { minimumFractionDigits: 2 }) + ' €';
                document.getElementById('sidebar-grand-total').innerText = gross.toLocaleString('de-DE', { minimumFractionDigits: 2 }) + ' €';
                document.getElementById('lbl-total-pages').innerText = pageIndex;

                App.renderCalculationSidebar(activeTotal);
                App.initThumbObserver();
                App.setActiveThumb(1);
                App.initThumbSortable();
            }
        },
       renderCalculationSidebar: (totalNet) => {
            const c = document.getElementById('calc-sidebar-content'); 
            c.innerHTML='';

            const renderCard = (isSub, sIdx, iIdx, subIdx, dataObj, prefix, hasChildren) => {
                if (dataObj.active === false) return '';
                
                const qty = parseFloat(dataObj.qty) || 1;
                
                // Fallback EK to VK if EK is 0 visually
                let ek = parseFloat(dataObj.ek) || 0;
                let vk = parseFloat(dataObj.price) || 0;
                if (ek === 0 && vk > 0) ek = vk; 
                
                const totalEK = ek * qty;
                const totalVK = dataObj.isPauschal ? vk : vk * qty;
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
                    <summary class="cursor-pointer select-none p-2 font-bold flex justify-between items-center outline-none hover:bg-slate-100 transition-colors rounded group-open/item:rounded-b-none group-open/item:bg-slate-50 group-open/item:border-b border-slate-200">
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
        addManualItem: (sIdx) => { 
            const defaultMargin = App.getDefaultMargin('article');
            State.sections[sIdx].items.push({ 
                name:'Neue Position', desc:'Beschreibung', price:0, ek:0, 
                marginPercent: defaultMargin, margin: defaultMargin, 
                qty:1, unit:'Stk', kind: 'article', status: 'normal', subItems:[] 
            }); 
            App.renderQuotePage(); 
        },            
        
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
                name:"Position", price:0, ek:0, 
                marginPercent: defaultMargin, margin: defaultMargin, 
                active:true, qty:1, unit:'Stk', kind: 'article', status: 'normal'
            }); 
            App.renderQuotePage(); 
        },
        // Settings
        openPosSettings: (sIdx, iIdx, subIdx = null) => { 
            const item = subIdx !== null ? State.sections[sIdx].items[iIdx].subItems[subIdx] : State.sections[sIdx].items[iIdx]; 
            State.tempPosSettings = { sIdx, iIdx, subIdx }; 
            document.getElementById('setting-qty').value = item.qty || 1;
            document.getElementById('setting-unit').value = item.unit || 'Stk';
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
                    if (sub.active !== false) newSetSum += (sub.price || 0) * (sub.qty || 1);
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
                labBody.innerHTML = `<tr><td class="px-4 py-3 text-slate-400 text-sm" colspan="3">Keine Dienstleistungen</td></tr>`;
                return;
                }

                labBody.innerHTML = labor.map(l => {
                const hrs  = num(l?.hours, 1);
                const rate = num(l?.hourly_rate, 0);
                const tot  = num(l?.total, hrs * rate);

                return `
                    <tr>
                    <td class="px-4 py-3 font-bold text-slate-800 text-sm">${escapeHtml(l?.name || 'Dienstleistung')}</td>
                    <td class="px-4 py-3 text-center font-mono text-sm">${escapeHtml(hrs)}</td>
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
                    if (typeof handleItemAdd === 'function') {
                        await handleItemAdd(sIdx, String(setId), 'master_set');
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

     
  App.Tabs = {
        current: 'a4',
        switch(mode) {
            this.current = mode;

            const a4 = document.getElementById('panel-a4');
            const list = document.getElementById('panel-list');
            const settings = document.getElementById('panel-settings');
            
            const btnA4 = document.getElementById('main-tab-a4');
            const btnList = document.getElementById('main-tab-list');
            const btnSettings = document.getElementById('main-tab-settings');

            // Toggle Panels
            if (a4) a4.classList.toggle('hidden', mode !== 'a4');
            if (list) list.classList.toggle('hidden', mode !== 'list');
            if (settings) settings.classList.toggle('hidden', mode !== 'settings');

            // Helper for button classes
            const setActive = (btn, isActive) => {
                if(!btn) return;
                if(isActive) {
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

            if (mode === 'list') App.ListView.render();
            if (mode === 'settings') App.Settings.render();
        }
    };
    App.Tabs.switch('a4');

    // ============================================================
    // ✅ LIST VIEW (FULL REWRITE) — with EK (€), Marge %, DB1 (€)
    // + two dropdowns per position: Artikel/Lohn + Normal/Alt/Opt
    // + subitems supported
    // + keeps your existing App.updateItemDetails / updateSubItemDetails API
    // ============================================================   
    App.ListView = {
        toggleCol: (colName) => {
            if (!State.listCols) State.listCols = { bild: true, type: true, ekMarge: true, anteil: true, gkNetto: true };
            State.listCols[colName] = !State.listCols[colName];
            App.renderQuotePage();
        },

        // --- Drag & Drop Handlers ---
        handleDropOnSection: async (ev, sIdx) => {
            ev.preventDefault();
            ev.currentTarget.classList.remove('bg-brand-light');
            const id = ev.dataTransfer.getData("text");
            const type = ev.dataTransfer.getData("itemType");
            if (id && type) await App.handleItemAdd(sIdx, id, type);
        },

        handleDropOnPosition: async (ev, sIdx, iIdx) => {
            ev.preventDefault();
            ev.stopPropagation();
            ev.currentTarget.classList.remove('border-l-4', 'border-brand-primary');
            const id = ev.dataTransfer.getData("text");
            const type = ev.dataTransfer.getData("itemType");
            if (id && type) await App.addLibraryItemAsSubPosition(sIdx, iIdx, id, type);
        },

        render() {
            const root = document.getElementById('listview-root');
            if (!root) return;

            const esc = typeof App.escapeHtml === 'function' ? App.escapeHtml : (str) => str;
            const fmtMoney = (n) => (parseFloat(n) || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
            const fmtPct = (n) => (parseFloat(n) || 0).toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' %';
            const num = (v) => parseFloat(v) || 0;

            // 0. SYNC PASS: Aggregate Sub-Items
            State.sections.forEach(s => {
                (s.items || []).forEach(it => {
                    if (it.subItems && it.subItems.length > 0 && !it.isPauschal && it.kind !== 'note') {
                        let aggPrice = 0; let aggEk = 0;
                        it.subItems.forEach(sub => {
                            if (sub.active !== false && sub.status === 'normal') { 
                                const sq = num(sub.qty);
                                aggPrice += num(sub.price) * sq;
                                aggEk += num(sub.ek) * sq;
                            }
                        });
                        it.price = aggPrice;
                        it.ek = aggEk;
                        if (it.ek > 0) {
                            it.marginPercent = ((it.price - it.ek) / it.ek) * 100;
                        } else {
                            it.marginPercent = 100;
                        }
                    }
                });
            });

            // 1. GLOBAL TOTALS & DASHBOARD MATH
            const cfg = State.config || {
                overhead: 15.0, commission: 1.0, risk: 1.5, finance: 0.5, tax: 30.0, suppDiscount: 2.0, minProfit: 10.0, custDiscount: 2.0,
                logistics: { freight: {active:false, val:0}, vehicle: {active:false, val:0}, machine: {active:false, val:0} }
            };

            let sumSales = 0; let sumEK = 0; let sumLaborSales = 0; let sumMatSales = 0; let totalHours = 0;

            State.sections.forEach(s => {
                (s.items || []).forEach(it => {
                    if (it.active === false || it.status === 'optional' || it.status === 'alternative' || it.kind === 'note') return;

                    const q = num(it.qty);
                    const p = num(it.price);
                    const e = num(it.ek);
                    const rowSales = it.isPauschal ? p : (p * q);
                    const rowEK = it.isPauschal ? e : (e * q); 

                    sumSales += rowSales;
                    sumEK += rowEK;

                    if (it.kind === 'labor' || it.unit === 'Std' || it.unit === 'h') {
                        sumLaborSales += rowSales;
                        if (it.unit === 'Std' || it.unit === 'h') totalHours += q;
                    } else {
                        sumMatSales += rowSales;
                    }
                    
                    if (it.subItems && it.subItems.length > 0) {
                        it.subItems.forEach(sub => {
                            if (sub.active !== false && (sub.unit === 'Std' || sub.unit === 'h')) totalHours += num(sub.qty);
                        });
                    }
                });
            });

            // Add Logistics to Totals
            let logTotal = 0;
            if(cfg.logistics?.freight?.active) logTotal += num(cfg.logistics.freight.val);
            if(cfg.logistics?.vehicle?.active) logTotal += num(cfg.logistics.vehicle.val);
            if(cfg.logistics?.machine?.active) logTotal += num(cfg.logistics.machine.val);
            sumSales += logTotal; 
            sumMatSales += logTotal;

            // Waterfall Math
            const db1 = sumSales - sumEK; 
            const overheadCost = sumSales * (num(cfg.overhead) / 100);
            const commCost = sumSales * (num(cfg.commission) / 100);
            const db2 = db1 - overheadCost - commCost; 
            const suppDiscVal = sumEK * (num(cfg.suppDiscount) / 100); 
            const riskVal = sumEK * (num(cfg.risk) / 100);             
            const financeVal = sumEK * (num(cfg.finance) / 100);      
            const custDiscVal = sumSales * (num(cfg.custDiscount) / 100);
            const db3 = db2 + suppDiscVal - riskVal - financeVal - custDiscVal; 
            const taxVal = Math.max(0, db3 * (num(cfg.tax) / 100));
            const netProfit = db3 - taxVal;

            const totalGlobalCosts = overheadCost + commCost + riskVal + financeVal + custDiscVal - suppDiscVal;
            const totalCostFactor = sumSales > 0 ? (totalGlobalCosts / sumSales) : 0;

            const laborShare = sumSales > 0 ? (sumLaborSales / sumSales) * 100 : 0;
            const matShare = sumSales > 0 ? (sumMatSales / sumSales) * 100 : 0;
            const db1Pct = sumSales > 0 ? (db1 / sumSales) * 100 : 0;
            const db2Pct = sumSales > 0 ? (db2 / sumSales) * 100 : 0;
            const profitPct = sumSales > 0 ? (netProfit / sumSales) * 100 : 0;
            const salesPerHour = totalHours > 0 ? (sumSales / totalHours) : 0;
            const profitPerHour = totalHours > 0 ? (db3 / totalHours) : 0;
            const maxBar = Math.max(sumSales, 1);

            // Dynamic Dashboard Output
            let html = `
            <details class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6 group/dash" open>
                <summary class="bg-slate-100 p-3 border-b border-slate-200 flex justify-between items-center cursor-pointer hover:bg-slate-200 transition-colors list-none">
                    <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2 uppercase tracking-wide">
                        <i class="fa-solid fa-chart-line text-blue-600"></i> Analyse & Controlling
                    </h3>
                    <i class="fa-solid fa-chevron-up text-slate-500 transition-transform group-open/dash:rotate-180"></i>
                </summary>
                
                <div class="p-6 space-y-8 bg-slate-50/50 group-[.is-collapsed]/dash:hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                            <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase"><i class="fa-solid fa-chart-pie text-blue-600"></i> Split-Analyse</h5>
                            <div class="mb-3 pb-3 border-b border-slate-100">
                                <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1"><span>Material & Sonst.</span><span>${fmtMoney(sumMatSales)}</span></div>
                                <div class="flex justify-between text-[10px] text-slate-400"><span>Anteil am Umsatz: ${fmtPct(matShare)}</span></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1"><span>Lohn & Montage</span><span>${fmtMoney(sumLaborSales)}</span></div>
                                <div class="flex justify-between text-[10px] text-slate-400"><span>Anteil am Umsatz: ${fmtPct(laborShare)}</span></div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                <div class="flex justify-between text-xs text-slate-500"><span>Grenzkosten (EK)</span><span>${fmtMoney(sumEK)}</span></div>
                                <div class="flex justify-between text-xs font-bold text-slate-700 bg-slate-50 p-1 rounded"><span>DB 1 (Marge)</span><div class="text-right"><div>${fmtMoney(db1)}</div><div class="text-[9px] font-normal text-slate-400">${fmtPct(db1Pct)}</div></div></div>
                                <div class="flex justify-between text-xs font-bold text-blue-700 bg-blue-50 p-1 rounded border border-blue-100"><span>Gesamtgewinn</span><div class="text-right"><div>${fmtMoney(netProfit)}</div><div class="text-[9px] font-normal text-blue-400">${fmtPct(profitPct)}</div></div></div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                            <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase"><i class="fa-solid fa-clock text-orange-600"></i> Stunden-Performance</h5>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-orange-50 p-2 rounded"><span class="text-xs text-slate-600">Gesamtstunden</span><span class="font-mono font-bold">${totalHours.toFixed(1)} h</span></div>
                                <div><div class="flex justify-between text-xs text-slate-500 mb-1">Umsatz pro Stunde (Netto)</div><div class="flex justify-between items-baseline"><span class="text-xs text-slate-400">Ø Satz</span><span class="font-bold text-slate-800">${fmtMoney(salesPerHour)} /h</span></div></div>
                                <div class="border-t border-slate-100 pt-2"><div class="flex justify-between text-xs text-green-600 mb-1 font-medium">Reingewinn pro Stunde (DB3)</div><div class="flex justify-between items-baseline"><span class="text-xs text-slate-400">Nach Risiko/Zins</span><span class="font-bold ${profitPerHour >= 0 ? 'text-green-600' : 'text-red-600'}">${fmtMoney(profitPerHour)} /h</span></div></div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 ring-1 ring-blue-100">
                            <h5 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-sm uppercase"><i class="fa-solid fa-money-bill-wave text-green-600"></i> Finanz-Dashboard</h5>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between"><span class="text-slate-500 text-xs">Umsatz Netto</span><span class="font-medium">${fmtMoney(sumSales)}</span></div>
                                <div class="flex justify-between text-xs mt-1"><span class="text-slate-400">./. EK Listenpreis</span><span class="text-slate-400">-${fmtMoney(sumEK)}</span></div>
                                <div class="border-t border-slate-100 my-1 pt-1"></div>
                                <div class="flex justify-between text-red-400 text-xs"><span>./. Gemeink. + Prov.</span><span>- ${fmtMoney(overheadCost + commCost)}</span></div>
                                <div class="flex justify-between text-orange-400 text-xs"><span>./. Wagnis, Zins, Skonto</span><span>- ${fmtMoney(riskVal + financeVal + custDiscVal)}</span></div>
                                <div class="bg-slate-50 p-2 rounded border border-slate-100 mt-2">
                                    <div class="flex justify-between items-center mb-1"><span class="font-bold text-slate-700 text-xs uppercase">DB 3 (EBIT)</span><span class="font-bold font-mono ${db3 >= 0 ? 'text-green-600' : 'text-red-600'}">${fmtMoney(db3)}</span></div>
                                    <div class="border-t border-slate-200 pt-1 mt-1">
                                        <div class="flex justify-between text-[10px] text-red-400"><span>./. Steuern (${num(cfg.tax)}%)</span><span>- ${fmtMoney(taxVal)}</span></div>
                                        <div class="flex justify-between font-bold text-xs mt-1 text-blue-900"><span>Netto-Gewinn</span><span>${fmtMoney(netProfit)}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <h6 class="text-[10px] font-bold text-slate-500 uppercase flex items-center gap-1"><i class="fa-solid fa-bullseye"></i> Margen-Monitor</h6>
                                <div class="w-2 h-2 rounded-full animate-pulse ${profitPct >= num(cfg.minProfit) ? 'bg-green-500' : 'bg-red-500'}"></div>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-center">
                                <div class="text-2xl font-bold text-slate-800">${fmtPct(profitPct)}</div>
                                <div class="text-[9px] text-slate-400 text-center mt-1">Ziel: ${num(cfg.minProfit)}%</div>
                                <div class="w-full bg-slate-200 h-1.5 rounded-full mt-4 overflow-hidden">
                                    <div class="h-full transition-all duration-700 ${profitPct >= num(cfg.minProfit) ? 'bg-green-500' : 'bg-red-500'}" style="width: ${Math.min(100, Math.max(0, (profitPct / num(cfg.minProfit)) * 100))}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </details>
            `;

            // Table Settings & Header
            if (!State.listCols) State.listCols = { bild: true, type: true, ekMarge: true, anteil: true, gkNetto: true };
            const c = State.listCols;
            
            let gridTpl = "2rem 3rem";
            if (c.bild) gridTpl += " 3rem";
            gridTpl += " 1fr"; 
            if (c.type) gridTpl += " 6rem";
            gridTpl += " 4rem 3rem 5rem 5rem";
            if (c.ekMarge) gridTpl += " 7rem";
            if (c.anteil) gridTpl += " 4rem";
            if (c.gkNetto) gridTpl += " 6rem";
            gridTpl += " 2rem";

            html += `
                <div class="bg-slate-800 text-white text-[9px] font-bold uppercase tracking-wider p-2 rounded-t-xl grid items-center gap-2 sticky top-0 z-20 shadow-md" style="grid-template-columns: ${gridTpl};">
                    <div></div><div class="text-center">Pos</div>
                    ${c.bild ? `<div class="text-center"><i class="fa-solid fa-image"></i></div>` : ''}
                    <div>Beschreibung</div>
                    ${c.type ? `<div>Typ & Status</div>` : ''}
                    <div class="text-center">Menge</div><div class="text-center">Einh.</div>
                    <div class="text-right text-brand-primary">E-Preis</div><div class="text-right text-brand-primary">Gesamt</div>
                    ${c.ekMarge ? `<div class="text-right text-blue-300">EK & Marge</div>` : ''}
                    ${c.anteil ? `<div class="text-right text-slate-400">Anteil</div>` : ''}
                    ${c.gkNetto ? `<div class="text-right text-green-400">Netto / DB</div>` : ''}
                    <div class="text-right"><i class="fa-solid fa-wrench"></i></div>
                </div>
                <div class="bg-white border-x border-b border-slate-200 rounded-b-xl pb-8 shadow-sm">
            `;

            // THE ROW RENDERER (Includes Red Warning if below Minimum Margin)
            const renderRow = (it, sIdx, iIdx, subIdx, isSub, isLocked) => {
                if(it.active === false) return '';

                const isNote = it.kind === 'note';
                
                let ek = parseFloat(it.ek) || 0;
                let vk = parseFloat(it.price) || 0;
                let qty = parseFloat(it.qty) || 0;
                if (isNote) { ek = 0; vk = 0; qty = 0; } 
                if (ek > 0 && vk === 0) vk = ek; 
                
                let gp = it.isPauschal ? vk : vk * qty;
                let ekT = ek * qty;
                let marginPct = (ek > 0) ? ((vk - ek) / ek) * 100 : parseFloat(it.marginPercent) || 0;
                
                let anteil = (sumSales > 0 && it.status !== 'optional' && it.status !== 'alternative' && !isNote) ? (gp / sumSales) * 100 : 0;
                
                let db1_row = gp - ekT;
                let rowOverhead = gp * totalCostFactor; 
                let netMarginRow = db1_row - rowOverhead;

                const subArg = isSub ? subIdx : 'null';
                const ctxText = (field) => isSub ? `App.updateSubItemDetails(${sIdx},${iIdx},${subIdx},'${field}',this.value)` : `App.updateItemDetails(${sIdx},${iIdx},'${field}',this.value)`;
                
                let rowCls = isSub ? 'bg-slate-50/70 border-t border-slate-100 pl-2' : 'border-t border-slate-300 mt-2 pt-2 bg-white';
                if (it.status === 'optional') rowCls += ' opacity-75 italic bg-blue-50/50';
                if (it.status === 'alternative') rowCls += ' opacity-75 bg-orange-50/50';
                
                let posNum = isSub ? `${sIdx+1}.${iIdx+1}.${subIdx+1}` : `${sIdx+1}.${iIdx+1}`;
                if (isNote) { posNum = '<i class="fa-solid fa-message text-yellow-500 text-lg"></i>'; rowCls = 'bg-yellow-50 border-t border-yellow-200 mt-1'; }

                const lockCls = isLocked ? 'opacity-60 cursor-not-allowed bg-slate-50' : 'bg-transparent hover:bg-slate-50 focus:bg-white border-transparent focus:border-brand-primary';

                // ==========================================
                // THE MINIMUM MARGIN WARNING LOGIC IS HERE!
                // ==========================================
                const minProfit = num(cfg.minProfit);
                const isBelowMin = marginPct < minProfit && !isNote && ek > 0 && it.status === 'normal';
                
                const marginInputClass = isBelowMin 
                    ? "w-14 text-[9px] border border-red-500 bg-red-50 text-red-600 rounded px-1 py-0.5 text-right font-mono outline-none" 
                    : "w-14 text-[9px] border border-slate-200 rounded px-1 py-0.5 text-right font-mono outline-none";
                    
                const warningIcon = isBelowMin ? `<i class="fa-solid fa-triangle-exclamation text-red-500 ml-1 text-[10px]" title="Achtung: Marge unter ${minProfit}%"></i>` : '';

                return `
                <div class="grid items-start gap-2 p-2 hover:bg-blue-50/30 transition-colors group/row ${rowCls}" style="grid-template-columns: ${gridTpl};" 
                    data-id="${it.id || iIdx}" ondragover="event.preventDefault();" ondrop="if(!${isLocked}){ App.ListView.handleDropOnPosition(event, ${sIdx}, ${iIdx}); }">
                    
                    <div class="flex justify-center items-center pt-2 text-slate-300 ${!isLocked ? 'drag-handle cursor-grab hover:text-slate-600' : ''}"><i class="fa-solid ${isLocked ? 'fa-lock text-slate-200' : 'fa-grip-vertical'}"></i></div>
                    <div class="text-[10px] font-black text-slate-500 text-center pt-2 font-mono flex flex-col">${posNum}</div>
                    
                    ${c.bild ? `<div class="w-9 h-9 bg-slate-100 rounded border border-slate-200 overflow-hidden mt-0.5 relative cursor-pointer group/img" onclick="App.handleImageClick(${sIdx},${iIdx},${subArg})">${!isNote ? `<img src="${it.img||App.placeholderImg(it.name)}" class="w-full h-full object-cover">` : ''}</div>` : ''}
                    
                    <div class="flex flex-col gap-1 min-w-0">
                        <input class="w-full font-bold text-slate-800 text-[11px] rounded px-1 py-0.5 outline-none border ${lockCls}" value="${esc(it.name)}" onchange="${ctxText('name')}" ${isLocked?'readonly':''}>
                        <textarea class="w-full text-[10px] text-slate-500 resize-none outline-none border rounded px-1 py-0.5 ${lockCls}" rows="1" onchange="${ctxText('desc')}">${esc(it.desc)}</textarea>
                    </div>

                    ${c.type ? `<div class="flex flex-col gap-1"><select onchange="App.updatePosConfig(${sIdx},${iIdx},${subArg},'kind',this.value)" class="text-[9px] font-bold border border-slate-200 rounded px-1 py-0.5 bg-white text-slate-600 outline-none" ${isLocked?'disabled':''}><option value="article" ${it.kind==='article'?'selected':''}>📦 Artikel</option><option value="labor" ${it.kind==='labor'?'selected':''}>🔨 Lohn</option><option value="note" ${it.kind==='note'?'selected':''}>💬 Hinweis</option></select>${!isNote ? `<select onchange="App.updatePosStatus(${sIdx},${iIdx},${subArg},this.value)" class="text-[9px] font-bold border border-slate-200 rounded px-1 py-0.5 bg-white text-slate-600 outline-none" ${isLocked?'disabled':''}><option value="normal" ${it.status==='normal'?'selected':''}>Standard</option><option value="optional" ${it.status==='optional'?'selected':''}>Optional</option><option value="alternative" ${it.status==='alternative'?'selected':''}>Alternativ</option></select>` : ''}</div>` : ''}

                    <div>${!isNote ? `<input type="number" step="0.01" value="${qty}" onchange="${ctxText('qty')}" class="w-full text-[11px] border rounded px-1 py-1 text-center font-bold outline-none ${lockCls}" ${isLocked?'readonly':''}>` : ''}</div>
                    <div>${!isNote ? `<input type="text" value="${esc(it.unit)}" onchange="${ctxText('unit')}" class="w-full text-[11px] border rounded px-1 py-1 text-center outline-none ${lockCls}">` : ''}</div>
                    <div>${!isNote ? `<input type="number" step="0.01" value="${vk.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'price',this.value)" class="w-full text-[11px] border rounded px-1 py-1 text-right font-mono font-bold text-brand-primary outline-none focus:border-brand-primary ${lockCls}">` : ''}</div>
                    <div class="text-right text-[11px] font-bold font-mono text-slate-700 pt-1 truncate">${isNote || it.hidePrices ? '-' : fmtMoney(gp)}</div>

                    ${c.ekMarge ? `<div class="flex flex-col gap-1">${!isNote ? `<div class="flex items-center gap-1 justify-end"><span class="text-[8px] font-bold text-blue-400 w-4">EK</span><input type="number" step="0.01" value="${ek.toFixed(2)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'ek',this.value)" class="w-16 text-[9px] border border-blue-200 rounded px-1 py-0.5 text-right font-mono bg-blue-50 outline-none"></div><div class="flex items-center gap-1 justify-end"><span class="text-[8px] font-bold text-slate-400 w-4">MG</span><input type="number" step="0.1" value="${marginPct.toFixed(1)}" onchange="App.updatePosPriceCalc(${sIdx},${iIdx},${subArg},'marginPercent',this.value)" class="${marginInputClass}">${warningIcon}</div>` : ''}</div>` : ''}

                    ${c.anteil ? `<div class="text-right pt-1">${!isNote ? `<span class="text-[10px] font-mono font-bold text-slate-600">${anteil.toFixed(1)}%</span>` : ''}</div>` : ''}

                    ${c.gkNetto ? `<div class="text-right flex flex-col gap-0.5 pt-1">${!isNote ? `<span class="text-[9px] font-mono text-red-400 truncate" title="Gemeinkosten">-${fmtMoney(rowOverhead)}</span><span class="text-[10px] font-mono font-bold ${netMarginRow>=0?'text-green-600':'text-red-600'} border-t border-slate-200 mt-0.5 pt-0.5 truncate" title="Netto DB3">${fmtMoney(netMarginRow)}</span>` : ''}</div>` : ''}

                    <div class="flex flex-col items-end gap-1 opacity-0 group-hover/row:opacity-100 transition-opacity pt-1">
                        ${!isLocked ? `<button onclick="App.removeItem(${sIdx},${iIdx},${subArg})" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>` : ''}
                        ${!isSub && !isLocked && !isNote ? `<div class="flex items-center gap-1 mt-1"><label class="text-[8px] text-slate-500 cursor-pointer flex items-center gap-0.5"><input type="checkbox" ${it.isPauschal?'checked':''} onchange="App.updatePosConfig(${sIdx},${iIdx},null,'isPauschal',this.checked)"> Psch.</label></div>` : ''}
                    </div>
                </div>`;
            };

            // RENDER SECTIONS
            if (State.sections.length === 0) {
                html += `<div class="p-12 text-center text-slate-400 text-sm">Keine Sektionen vorhanden.</div>`;
            } else {
                State.sections.forEach((sec, sIdx) => {
                    const isLocked = !!sec.isLocked;
                    
                    let secTotal = 0;
                    sec.items.forEach(it => {
                        if(it.active !== false && it.status === 'normal' && it.kind !== 'note') {
                            secTotal += it.isPauschal ? (parseFloat(it.price)||0) : (parseFloat(it.price)||0) * (parseFloat(it.qty)||1);
                        }
                    });

                    html += `
                    <details class="group/sec mb-4" open ondrop="App.ListView.handleDropOnSection(event, ${sIdx})" ondragover="event.preventDefault();">
                        <summary class="bg-slate-100 p-3 border-y border-slate-200 cursor-pointer flex items-center justify-between outline-none hover:bg-slate-200 transition-colors list-none">
                            <div class="flex items-center gap-3 w-1/2">
                                <i class="fa-solid fa-chevron-right transition-transform group-open/sec:rotate-90 text-slate-400 text-xs"></i>
                                <span class="font-bold text-sm text-slate-500">${sIdx+1}.</span>
                                <input value="${esc(sec.title)}" onchange="App.updateSectionMeta(${sIdx},'title',this.value)" class="font-bold text-sm text-slate-800 bg-transparent outline-none w-full" onclick="event.preventDefault()">
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-xs font-bold text-slate-600 bg-white px-2 py-1 rounded shadow-sm border border-slate-200">∑ ${fmtMoney(secTotal)}</span>
                                <div class="flex items-center gap-1 ml-2">
                                    <button onclick="App.toggleSectionLock(${sIdx}); event.preventDefault();" class="${isLocked ? 'text-red-500 hover:text-red-700' : 'text-slate-400 hover:text-green-600'} p-1 rounded transition">
                                        <i class="fa-solid ${isLocked ? 'fa-lock' : 'fa-unlock'}"></i>
                                    </button>
                                    ${!isLocked ? `
                                    <button onclick="App.addNotePosition(${sIdx}); event.preventDefault();" class="text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-300 px-2 py-1 rounded hover:bg-yellow-200 flex items-center gap-1"><i class="fa-solid fa-comment-dots"></i> Hinweis</button>
                                    <button onclick="App.addManualItem(${sIdx}); event.preventDefault();" class="text-[10px] font-bold bg-brand-light text-brand-primary border border-brand-primary px-2 py-1 rounded hover:bg-brand-primary hover:text-white flex items-center gap-1"><i class="fa-solid fa-plus"></i> Artikel</button>
                                    <button onclick="App.removeSection(${sIdx}); event.preventDefault();" class="text-slate-400 hover:text-red-500 p-1 ml-1"><i class="fa-solid fa-trash"></i></button>
                                    ` : ''}
                                </div>
                            </div>
                        </summary>
                        <div class="section-body pb-2 min-h-[30px]" data-sidx="${sIdx}">`;

                    sec.items.forEach((it, iIdx) => {
                        html += `<div class="main-item-wrapper" data-iidx="${iIdx}">`;
                        html += renderRow(it, sIdx, iIdx, null, false, isLocked);
                        if (it.subItems && it.subItems.length > 0) {
                            html += `<div class="sub-items-container">`;
                            it.subItems.forEach((sub, subIdx) => html += renderRow(sub, sIdx, iIdx, subIdx, true, isLocked));
                            html += `</div>`;
                        } else if (!isLocked && it.kind !== 'note') {
                            html += `<div class="pl-[90px] py-1 border-t border-slate-100 bg-slate-50 opacity-0 hover:opacity-100 transition-opacity"><button onclick="App.addSubItem(${sIdx},${iIdx})" class="text-[9px] font-bold text-slate-400 hover:text-brand-primary uppercase flex items-center gap-1"><i class="fa-solid fa-level-down-alt"></i> Unterposition hinzufügen</button></div>`;
                        }
                        html += `</div>`;
                    });
                    html += `</div></details>`;
                });
            }
            html += `</div>`;
            root.innerHTML = html;

            // DRAG AND DROP
            if (typeof Sortable !== 'undefined') {
                document.querySelectorAll('.section-body').forEach(el => {
                    new Sortable(el, {
                        group: 'shared', handle: '.drag-handle', animation: 150, ghostClass: 'bg-blue-50',
                        onEnd: function (evt) {
                            const fromSIdx = parseInt(evt.from.dataset.sidx), toSIdx = parseInt(evt.to.dataset.sidx);
                            if (fromSIdx === toSIdx && evt.oldIndex === evt.newIndex) return;
                            const item = State.sections[fromSIdx].items.splice(evt.oldIndex, 1)[0];
                            State.sections[toSIdx].items.splice(evt.newIndex, 0, item);
                            App.renderQuotePage();
                        }
                    });
                });
            }
        }
    };
    // ------------------------------------------------------------
    // Quick add + auto sync (FULL REWRITE / FIXED braces)
    // ------------------------------------------------------------
    App.addPositionQuick = function () {
    let sIdx = State.sections.findIndex(s => s && !s._pageBreak);
    if (sIdx === -1) sIdx = App.addSection();
    App.addManualItem(sIdx);
    App.ListView.render();
    };


    
    // ------------------------------------------------------------
    // Keep list view synced with A4 render + tab switching
    // ------------------------------------------------------------
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

    /* ✅ 4) Add these helpers anywhere inside your <script> (window.App scope) */

        App.money = function (n) {
            const v = Number(n);
            if (!Number.isFinite(v)) return '0,00';
            return v.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        App.clamp = function(n, a, b){
            n = Number(n);
            if (!Number.isFinite(n)) n = 0;
            return Math.max(a, Math.min(b, n));
        };

        // VK (price) from EK and margin percent (like your React reference)
        App.vkFromEkMargin = function(ek, marginPercent){
            const EK = Number(ek || 0);
            const m = App.clamp(marginPercent ?? 0, 0, 99.9) / 100;
            if (!EK) return 0;
            return EK / (1 - m);
        };

        App.ensureItemCalcDefaults = function(it){
            if (!it) return it;
            if (it.kind !== 'article' && it.kind !== 'labor') it.kind = 'article'; // dropdown #1
            if (!it.status) it.status = 'normal'; // dropdown #2: normal|alternative|optional
            if (it.ek == null) it.ek = 0;
            if (it.marginPercent == null) it.marginPercent = 20;

            // keep unit sensible
            if (it.kind === 'labor') {
                if (!it.unit) it.unit = 'Std';
            } else {
                if (!it.unit) it.unit = 'Stk';
            }

            // If price is empty/0 but EK exists -> auto compute VK from EK+margin
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
<script>
    // default tab
App.Tabs?.switch('a4');

// whenever A4 re-renders, List View should still be consistent
// (optional: only if list tab is visible)
const _oldRender = App.renderQuotePage;
App.renderQuotePage = function(...args){
  const r = _oldRender?.apply(App, args);
  const listVisible = !document.getElementById('panel-list')?.classList.contains('hidden');
  if (listVisible) App.ListView?.render();
  return r;
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
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.overhead}" onchange="App.Settings.update('overhead', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center"><span>Vertriebs-Provision</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.commission}" onchange="App.Settings.update('commission', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200 pt-2 mt-2"><span>Mindestgewinn</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.minProfit}" onchange="App.Settings.update('minProfit', this.value)"> %</div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-indigo-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-percent"></i> Standard-Margen</h4>
                <div class="space-y-2 text-xs">
                    <p class="text-[10px] text-slate-400 mb-2 italic">Standard-Vorgaben für neue Positionen.</p>
                    <div class="flex justify-between items-center"><span>Material</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.margins?.material || 20}" onchange="App.Settings.update('marginMaterial', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center"><span>Lohn / Montage</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.margins?.labor || 50}" onchange="App.Settings.update('marginLabor', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center"><span>Fremdleistung</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.margins?.external || 15}" onchange="App.Settings.update('marginExternal', this.value)"> %</div>
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
                        <div class="flex items-center gap-1"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.logistics?.freight?.val || 0}" onchange="App.Settings.update('freightVal', this.value)"> €</div>
                    </div>
                    <div class="flex justify-between items-center"><span class="flex items-center gap-1">Fahrzeugpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.vehicle?.active ? 'checked' : ''} onchange="App.Settings.update('vehicleActive', this.checked)"></span>
                        <div class="flex items-center gap-1"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.logistics?.vehicle?.val || 0}" onchange="App.Settings.update('vehicleVal', this.value)"> €</div>
                    </div>
                    <div class="flex justify-between items-center"><span class="flex items-center gap-1">Maschinenpauschale <input type="checkbox" class="accent-green-500" ${c.logistics?.machine?.active ? 'checked' : ''} onchange="App.Settings.update('machineActive', this.checked)"></span>
                        <div class="flex items-center gap-1"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.logistics?.machine?.val || 0}" onchange="App.Settings.update('machineVal', this.value)"> €</div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-red-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i> Risiko & Wagnis</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center"><span>Kalk. Wagnis</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.risk}" onchange="App.Settings.update('risk', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center"><span>Vorfinanzierung (Zins)</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.finance}" onchange="App.Settings.update('finance', this.value)"> %</div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-blue-400 text-sm mb-2 flex items-center gap-2"><i class="fa-solid fa-landmark"></i> Steuern & Kunde</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center"><span>Kalk. Ertragssteuer</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.tax}" onchange="App.Settings.update('tax', this.value)"> %</div>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-700 mt-1"><span>Kunden-Skonto</span>
                        <div class="flex items-center"><input type="number" class="w-12 bg-slate-800 border border-slate-600 rounded px-1 text-right text-white" step="any" value="${c.custDiscount}" onchange="App.Settings.update('custDiscount', this.value)"> %</div>
                    </div>
                </div>
            </div>

        </div>
        `;
    },
    
    update: (key, val) => {
        const c = State.config;
        const v = (val === true || val === false) ? val : parseFloat(val);

        if(key === 'vatMode') c.vatMode = v;
        else if(key === 'overhead') c.overhead = v;
        else if(key === 'commission') c.commission = v;
        else if(key === 'minProfit') c.minProfit = v;
        else if(key === 'suppDiscount') c.suppDiscount = v;
        else if(key === 'risk') c.risk = v;
        else if(key === 'finance') c.finance = v;
        else if(key === 'tax') c.tax = v;
        else if(key === 'custDiscount') c.custDiscount = v;

        // Apply Logistics Checkboxes/Values
        else if(key === 'freightActive') c.logistics.freight.active = v;
        else if(key === 'freightVal') c.logistics.freight.val = v;
        else if(key === 'vehicleActive') c.logistics.vehicle.active = v;
        else if(key === 'vehicleVal') c.logistics.vehicle.val = v;
        else if(key === 'machineActive') c.logistics.machine.active = v;
        else if(key === 'machineVal') c.logistics.machine.val = v;

        // Immediately update all existing rows if default margins are changed!
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
        App.renderCalculationSidebar();
        if(App.Tabs.current === 'list') App.ListView.render();
    }
};

// Global Margin updater helper
App.applyGlobalMarginUpdate = function(kind, newMargin) {
    State.sections.forEach(sec => {
        if(sec.isLocked) return;
        (sec.items || []).forEach(it => {
            if (it.kind === kind && !it.isPauschal) {
                it.marginPercent = newMargin;
                if (it.ek > 0) it.price = App.vkFromEkMargin(it.ek, newMargin);
            }
            (it.subItems || []).forEach(sub => {
                if (sub.kind === kind && !sub.isPauschal) {
                    sub.marginPercent = newMargin;
                    if (sub.ek > 0) sub.price = App.vkFromEkMargin(sub.ek, newMargin);
                }
            });
        });
    });
    App.renderQuotePage();
};
App.getDefaultMargin = function(kind) {
    if (!State.config || !State.config.margins) return 20;
    if (kind === 'labor') return State.config.margins.labor;
    if (kind === 'external') return State.config.margins.external;
    return State.config.margins.material;
};

    App.applyGlobalMarginUpdate = function(kind, newMargin) {
        State.sections.forEach(sec => {
            if(sec.isLocked) return; // Don't change locked sections
            (sec.items || []).forEach(it => {
                if (it.kind === kind && !it.isPauschal) {
                    it.marginPercent = newMargin;
                    if (it.ek > 0) it.price = App.vkFromEkMargin(it.ek, newMargin);
                }
                (it.subItems || []).forEach(sub => {
                    if (sub.kind === kind && !sub.isPauschal) {
                        sub.marginPercent = newMargin;
                        if (sub.ek > 0) sub.price = App.vkFromEkMargin(sub.ek, newMargin);
                    }
                });
            });
        });
        App.renderQuotePage();
    };



</script>
</body>
</html>