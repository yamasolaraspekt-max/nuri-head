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
            margin: 0 auto 40px auto; padding: 15mm 20mm; position: relative; 
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
        .pos-header-grid { display: grid; grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem; gap: 0.75rem; font-size: 0.75rem; font-weight: 700; color: #334155; text-transform: uppercase; border-bottom: 2px solid #1e293b; padding-bottom: 0.5rem; margin-bottom: 1rem; align-items: center; }
        .pos-row-top { display: grid; grid-template-columns: 2.5rem 1fr 3rem 2.5rem 5rem 5rem 1.5rem; gap: 0.75rem; font-size: 0.8rem; font-weight: bold; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.25rem; margin-bottom: 0.25rem; align-items: center; }
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
                    <div class="relative"><label class="block text-sm font-bold text-slate-700 mb-2">1. Kunde</label><div class="relative"><input type="text" id="wiz-customer-search" oninput="App.Wizard.filterCustomers()" onfocus="App.Wizard.filterCustomers()" placeholder="Suche..." class="w-full border border-slate-300 rounded-lg p-3 pl-10 text-sm outline-none focus:border-[#93c21c]"><div id="wiz-customer-dropdown" class="absolute w-full bg-white border border-slate-200 rounded-b-lg shadow-lg z-50 hidden max-h-40 overflow-y-auto"></div></div><div id="wiz-customer-selected" class="hidden mt-2 p-3 bg-[#f7fee7] rounded-lg border border-[#93c21c]/30 flex justify-between items-center"><div><div class="font-bold text-slate-800" id="wiz-sel-cust-name"></div><div class="text-xs text-slate-500" id="wiz-sel-cust-addr"></div></div><button onclick="App.Wizard.clearCustomer()" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button></div></div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-2"><label class="block text-sm font-bold text-slate-700 mb-2">2. Objekt</label><select id="wiz-object-select" onchange="App.Wizard.selectObject()" class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c] bg-white"><option value="">-- Bitte wählen --</option></select></div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-3"><label class="block text-sm font-bold text-slate-700 mb-2">3. Datum</label><input type="date" id="wiz-date" class="w-full border border-slate-300 rounded-lg p-3 text-sm outline-none focus:border-[#93c21c]"></div>
                    <div class="relative opacity-50 pointer-events-none transition-opacity" id="wiz-step-4"><label class="block text-sm font-bold text-slate-700 mb-2">4. Typ</label><div class="flex gap-4"><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Angebot" checked class="accent-[#93c21c]"><span class="text-sm font-bold">Angebot</span></label><label class="flex-1 border p-3 rounded cursor-pointer hover:border-[#93c21c] flex items-center gap-2"><input type="radio" name="wiz-doc-type" value="Kostenvoranschlag" class="accent-[#93c21c]"><span class="text-sm font-bold">Kostenvoranschlag</span></label></div></div>
                    
                    <!-- BRANDING STEP -->
                    <div class="relative border-t pt-4"><label class="block text-sm font-bold text-slate-700 mb-2">5. Branding</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-xs text-slate-500 block mb-1">Firmenname (Logo)</label><input type="text" id="wiz-brand-name" value="SOLAR ASPEKT" oninput="App.updateBranding()" class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:border-[#93c21c] outline-none"></div>
                            <div><label class="text-xs text-slate-500 block mb-1">Hauptfarbe</label><div class="flex items-center gap-2"><input type="color" id="wiz-brand-color" value="#93c21c" oninput="App.updateBranding()" class="w-10 h-10 p-1 rounded cursor-pointer border"><span id="color-hex-label" class="text-xs text-slate-500 font-mono">#93c21c</span></div></div>
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
            <div class="flex items-center gap-3">
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
                    <div class="p-4 border-b border-slate-100"><div class="relative"><input type="text" id="sidebar-search" oninput="App.renderSidebar()" placeholder="Produkt suchen..." class="w-full pl-8 pr-2 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-[#93c21c] bg-slate-50"><i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-sm"></i></div></div>
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
                <main class="flex-1 relative flex flex-col h-full overflow-y-auto scroller items-center py-8 gap-8 bg-slate-100/50" id="document-scroll-area">
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
        <div class="h-16 bg-slate-800 flex items-center justify-between px-6 text-white shrink-0 shadow-md">
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

    <script>
        const DB = {
            customers: [{id:1,name:'Max Mustermann',street:'Musterstraße 10',city:'12345 Musterstadt',email:'max@muster.de',objects:[{id:101,name:'Einfamilienhaus',address:'Musterstraße 10'}]},{id:2,name:'Carlos von Donop',street:'An der Flurscheid 34',city:'61352 Bad Homburg',email:'carlos@donop.de',objects:[{id:201,name:'Wohnhaus',address:'An der Flurscheid 34'}]}],
            products: [
                {id:'p1',name:'SOLAR-ANLAGE MIT 13,095 kWp',type:'set',group:'Photovoltaik',desc:'27 Module mit 485 Wp.',img:'https://placehold.co/200x200/eee/999?text=PV-Modul',price:10332.81,unit:'Stk',materials:[{name:'MODUL LONGI HI-MO6',price:10332.81,unit:'Stk'}],labor:[{name:'Dach-Montage',price:85,qty:27,unit:'Stk'}]},
                {id:'p2',name:'Batteriespeicher-System',type:'set',group:'Photovoltaik',desc:'FOX ESS 10.36 kWh',img:'https://placehold.co/200x200/eee/999?text=Batterie',price:7721.80,unit:'Stk',materials:[{name:'Wechselrichter H3 PRO 15',price:4000,unit:'Stk'},{name:'Speicher EK 11',price:3721.80,unit:'Stk'}],labor:[{name:'Elektrischer Anschluss',price:450,qty:1,unit:'Psch'}]}
            ]
        };

        const State = { customer:null, object:null, projectDate:'', docType:'Angebot', sections:[], editingBadge:null, offerId:'SA-AG25342', custId:'KD-1005', toolsImages:[], placedImages:[], taxRate:19, tempPosSettings:null, companyName: 'SOLAR ASPEKT', brandColor: '#93c21c' };
        const PAGE_MAX_HEIGHT_PX = 850; 

        // SINGLE APP CONTROLLER
        window.App = {
            // -- WIZARD LOGIC --
            Wizard: {
                filterCustomers: () => {
                    const val = document.getElementById('wiz-customer-search').value.toLowerCase();
                    const drop = document.getElementById('wiz-customer-dropdown'); drop.innerHTML='';
                    const filtered = DB.customers.filter(c=>c.name.toLowerCase().includes(val));
                    if(filtered.length>0){ drop.classList.remove('hidden'); filtered.forEach(c=>drop.innerHTML+=`<div class="dropdown-item p-2 hover:bg-brand-light cursor-pointer" onclick="App.Wizard.selectCustomer(${c.id})"><div class="font-bold text-slate-800">${c.name}</div><div class="text-xs text-slate-500">${c.street}, ${c.city}</div></div>`); } else drop.classList.add('hidden');
                },
                selectCustomer: (id) => {
                    const c = DB.customers.find(x=>x.id===id); if(!c) return;
                    State.customer = c;
                    document.getElementById('wiz-customer-search').parentElement.classList.add('hidden');
                    document.getElementById('wiz-sel-cust-name').innerText = c.name; document.getElementById('wiz-sel-cust-addr').innerText = `${c.street}, ${c.city}`;
                    document.getElementById('wiz-customer-selected').classList.remove('hidden');
                    const sel = document.getElementById('wiz-object-select'); sel.innerHTML='<option value="">-- Bitte wählen --</option>';
                    c.objects.forEach(o=>sel.innerHTML+=`<option value="${o.id}">${o.name}</option>`);
                    document.getElementById('wiz-step-2').classList.remove('opacity-50','pointer-events-none');
                },
                clearCustomer: () => { State.customer=null; State.object=null; document.getElementById('wiz-customer-selected').classList.add('hidden'); document.getElementById('wiz-customer-search').parentElement.classList.remove('hidden'); document.getElementById('wiz-step-2').classList.add('opacity-50','pointer-events-none'); document.getElementById('wiz-step-3').classList.add('opacity-50','pointer-events-none'); document.getElementById('wiz-step-4').classList.add('opacity-50','pointer-events-none'); document.getElementById('wiz-btn-start').disabled=true; document.getElementById('wiz-btn-start').classList.add('btn-disabled'); },
                selectObject: () => { State.object=State.customer.objects.find(o=>o.id==document.getElementById('wiz-object-select').value); if(State.object) { document.getElementById('wiz-step-3').classList.remove('opacity-50','pointer-events-none'); document.getElementById('wiz-step-4').classList.remove('opacity-50','pointer-events-none'); document.getElementById('wiz-btn-start').disabled=false; document.getElementById('wiz-btn-start').classList.remove('btn-disabled'); } }
            },

            init: () => {
                document.getElementById('wiz-date').valueAsDate = new Date();
                // Event Listeners
                document.addEventListener('click', e => { if(!document.getElementById('wiz-customer-search').parentElement.contains(e.target)) document.getElementById('wiz-customer-dropdown').classList.add('hidden'); });
                document.getElementById('img-upload-input').onchange = e => { const f=e.target.files[0]; if(f && App.editingImage) { const r=new FileReader(); r.onload=ev=>{ State.sections[App.editingImage.sIdx].items[App.editingImage.iIdx].img=ev.target.result; App.renderQuotePage(); }; r.readAsDataURL(f); } };
                document.getElementById('badge-upload-input').onchange = e => { const f=e.target.files[0]; if(f && State.editingBadge) { const r=new FileReader(); r.onload=ev=>{ State.editingBadge.tempImg=ev.target.result; }; r.readAsDataURL(f); } };
                document.getElementById('tool-upload-input').onchange = e => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=ev=>{ State.toolsImages.push(ev.target.result); App.renderSidebarTools(); }; r.readAsDataURL(f); } };
                App.updateBranding(); // Set initial brand
            },

            switchView: (view) => { document.querySelectorAll('.view-section').forEach(v=>v.classList.remove('active')); document.getElementById('view-'+view).classList.add('active'); },
            toggleSidebar: (side) => { document.getElementById(side==='left'?'sidebar-left':'sidebar-right').classList.toggle('sidebar-collapsed'); },
            switchSidebarTab: (tab) => { document.querySelectorAll('.sidebar-tab').forEach(t=>t.classList.remove('active')); document.getElementById('tab-'+tab).classList.add('active'); if(tab==='lib'){document.getElementById('sidebar-content-lib').classList.remove('hidden');document.getElementById('sidebar-content-tools').classList.add('hidden');}else{document.getElementById('sidebar-content-lib').classList.add('hidden');document.getElementById('sidebar-content-tools').classList.remove('hidden');App.renderSidebarTools();} },
            renderSidebarTools: () => { const c=document.getElementById('tools-list'); c.innerHTML=''; [...['https://placehold.co/100x100/green/white?text=Geprüft'],...State.toolsImages].forEach(src=>{ c.innerHTML+=`<div draggable="true" ondragstart="App.dragStartTool(event,'${src}')" class="bg-white border rounded p-2 cursor-grab"><img src="${src}" class="w-full h-16 object-contain"></div>`; }); },
            
            // --- CORE LOGIC ---
            updateBranding: () => {
                const name = document.getElementById('wiz-brand-name').value || 'SOLAR ASPEKT';
                const color = document.getElementById('wiz-brand-color').value || '#93c21c';
                State.companyName = name; State.brandColor = color;
                document.documentElement.style.setProperty('--brand-color', color);
                document.getElementById('color-hex-label').innerText = color;
            },

            startQuote: () => {
                State.projectDate = document.getElementById('wiz-date').value;
                const types = document.getElementsByName('wiz-doc-type'); types.forEach(t=>{if(t.checked)State.docType=t.value});
                document.getElementById('doc-cust-name').innerText = State.customer.name;
                document.getElementById('doc-cust-lastname').innerText = State.customer.name.split(' ').pop();
                document.getElementById('doc-cust-addr').innerHTML = `${State.customer.street}<br>${State.customer.city}`;
                document.getElementById('doc-date-line').innerText = `Wehrheim, ${new Date(State.projectDate).toLocaleDateString('de-DE')}`;
                document.getElementById('editor-doc-type-label').innerText = State.docType;
                document.getElementById('doc-main-title').innerText = `Unverbindliches ${State.docType} für...`;
                document.getElementById('lbl-doc-id-name').innerText = State.docType==='Angebot'?'Angebotsnummer':'KVA-Nummer';
                
                // Update Logo Texts
                document.querySelectorAll('.pdf-logo-text, #doc-logo-text').forEach(el => el.innerText = State.companyName);
                document.getElementById('doc-team-name').innerText = `Ihr ${State.companyName}-Team`;
                document.getElementById('footer-company').innerText = `${State.companyName} GmbH`;
                document.getElementById('doc-company-header').innerText = `${State.companyName} GmbH • Am Kappengraben 10 • 61273 Wehrheim`;

                if(State.sections.length === 0) App.addSection('1. Hauptpositionen', false);
                App.renderSidebar(); App.renderQuotePage(); App.switchView('editor');
            },

            // --- PAGE RENDERER ---
            createPage: (idx, forPrint) => {
                const title = State.docType==='Angebot'?'ANGEBOT':'KOSTENVORANSCHLAG';
                const div = document.createElement('div');
                div.className = 'a4-page flex-shrink-0 dynamic-page relative';
                if(!forPrint) { div.ondragover=e=>App.allowDrop(e); div.ondrop=e=>App.dropTool(e, idx); }
                div.innerHTML = `<div class="pdf-logo-text absolute top-8 right-12 text-sm">${State.companyName}</div><div class="flex justify-between items-end border-b-2 border-slate-300 pb-2 mb-6 mt-16"><div class="font-bold text-sm text-slate-800">${title} <span class="sync-offer-id">${State.offerId}</span></div></div><div class="pos-header-grid pb-2"><div class="text-center">Pos.</div><div>Artikelbezeichnung</div><div class="text-center">Menge</div><div class="text-center">Einh.</div><div class="text-right">EP</div><div class="text-right">GP</div><div></div></div><div class="page-content flex-1 relative"></div><div class="mt-auto border-t border-slate-200 pt-2 text-[9px] text-slate-400 text-center mb-4">Seite ${idx} • ${State.docType} freibleibend</div>`;
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
                    // Use standard DOM check for height to be safe
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
                    // Flags
                    const isPauschal = sec.config.mode==='pauschal';
                    const isOpt = sec.config.type==='optional';
                    const isAlt = sec.config.type==='alternative';
                    const hidePrices = sec.config.hidePrices; 
                    
                    const header = document.createElement('div'); header.className='mb-1 mt-4';
                    let badges = isOpt ? '(Optional)' : (isAlt ? '(Alternativ)' : '');
                    let titleHtml = forPrint ? `<div class="text-lg font-bold text-brand-primary uppercase">${sec.title} ${badges}</div><div class="text-sm text-slate-600">${sec.description}</div>` : `<div class="flex items-center"><input value="${sec.title}" oninput="App.updateSectionMeta(${sIdx},'title',this.value)" class="text-lg font-bold text-brand-primary w-full bg-transparent outline-none"><span class="text-xs text-slate-400 ml-2">${badges}</span></div><textarea oninput="App.updateSectionMeta(${sIdx},'description',this.value)" class="text-sm text-slate-500 w-full bg-transparent resize-none outline-none h-auto">${sec.description}</textarea>`;
                    header.innerHTML = titleHtml; append(header);

                    if(!forPrint) {
                        let dz = document.createElement('div'); dz.className='section-drop-zone'; dz.ondragover=e=>e.preventDefault(); dz.ondrop=e=>{e.preventDefault();const id=e.dataTransfer.getData("text");if(id)App.handleItemAdd(sIdx,id);}; append(dz);
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
                                row.draggable = true; // Enable drag on row
                                row.ondragstart = e => App.dragStartPos(e, sIdx, iIdx);
                                row.ondragover=e=>{e.preventDefault(); if(App.dragState && App.dragState.type==='pos') row.classList.add('item-group', 'drag-over-sort'); else { e.stopPropagation(); row.classList.add('drag-over-sub'); }}; 
                                row.ondragleave=e=>{row.classList.remove('drag-over-sub', 'drag-over-sort');}; 
                                row.ondrop=e=>{e.preventDefault(); row.classList.remove('drag-over-sub', 'drag-over-sort'); if(App.dragState && App.dragState.type==='pos') { App.moveItem(App.dragState.sIdx, App.dragState.iIdx, sIdx, iIdx); App.dragState=null; } else { e.stopPropagation(); const id=e.dataTransfer.getData("text");const p=DB.products.find(x=>x.id===id);if(p)App.addSubItemFromDrag(sIdx,iIdx,p); } }; 
                            }

                            let badgeHtml = '';
                            if(item.badge) {
                                const posCls = item.badge.pos==='tl'?'top-0 left-0':item.badge.pos==='tr'?'top-0 right-0':item.badge.pos==='bl'?'bottom-0 left-0':'bottom-0 right-0';
                                badgeHtml = item.badge.type==='text' ? `<div class="absolute ${posCls} bg-brand-primary text-white text-[8px] font-bold px-1 rounded z-10">${item.badge.text}</div>` : `<img src="${item.badge.src}" class="absolute ${posCls} w-6 h-6 object-contain z-10">`;
                            }
                            if(!item.active) badgeHtml += `<div class="absolute top-0 right-0 bg-red-500 text-white text-[8px] px-1 rounded z-20">HIDDEN</div>`;

                            const nameVal = forPrint ? item.name : `<input class="clean-input font-bold" value="${item.name}" onchange="App.updateItemDetails(${sIdx},${iIdx},'name',this.value)">`;
                            const descVal = forPrint ? item.desc : `<div class="editable-field" contenteditable="true" onblur="App.updateItemDetails(${sIdx},${iIdx},'desc',this.innerText)">${item.desc}</div>`;
                            const tools = forPrint ? '' : `<div class="mt-1 flex gap-2 no-print"><button onclick="App.addSubItem(${sIdx},${iIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-plus"></i> Sub</button><button onclick="App.openPosSettings(${sIdx},${iIdx})" class="text-[9px] text-slate-400 hover:text-brand-primary"><i class="fa-solid fa-cog"></i></button><button onclick="App.removeItem(${sIdx},${iIdx})" class="text-[9px] text-red-300 hover:text-red-500"><i class="fa-solid fa-trash"></i></button></div>`;

                            row.innerHTML = `
                                <div>
                                    <div class="pos-row-top">
                                        <div class="text-center">${posNum}</div>
                                        <div>${nameVal}</div>
                                        <div class="text-center">${item.qty}</div>
                                        <div class="text-center text-[10px] text-slate-500">${item.unit}</div>
                                        <div class="text-right font-mono text-[10px]">${epD}</div>
                                        <div class="text-right font-mono text-[11px]">${gpD}</div>
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
                                    subRow.innerHTML = `<div class="text-right pr-2">${posNum}.${subIdx+1}</div><div>${sName}</div><div class="text-center">${sub.qty}</div><div class="text-center">${sub.unit}</div><div class="text-right font-mono text-[9px]">${(!isPauschal&&!hidePrices)?sub.price.toLocaleString('de-DE'):'-'}</div><div class="text-right font-mono text-[9px]">${subGp}</div><div>${!forPrint?`<button onclick="App.removeItem(${sIdx},${iIdx},${subIdx})" class="text-red-300 hover:text-red-500"><i class="fa-solid fa-times"></i></button>`:''}</div>`;
                                    subC.appendChild(subRow);
                                });
                            }
                        });
                    }
                    if(isPauschal) { const pr = document.createElement('div'); pr.className="flex justify-end mt-2 pr-16 font-bold text-slate-800 text-sm border-t border-slate-300 pt-2"; pr.innerHTML=`<span>Pauschalpreis:</span><span class="ml-8 font-mono">${sec.config.pauschalPrice.toLocaleString('de-DE')} €</span>`; append(pr); }
                    if(!forPrint) { const btn = document.createElement('div'); btn.className="pb-4 pl-8"; btn.innerHTML=`<button onclick="App.addManualItem(${sIdx})" class="text-[10px] font-bold text-brand-primary flex items-center gap-1 hover:bg-brand-light px-2 py-1 rounded border border-dashed border-brand-primary"><i class="fa-solid fa-plus"></i> Position</button>`; append(btn); }
                });

                // Global Drop
                let dzG = document.createElement('div'); dzG.className = 'section-drop-zone border-2 border-dashed border-slate-300 rounded flex items-center justify-center text-slate-400 text-xs py-6 mt-4'; dzG.innerText = 'Neue Sektion'; dzG.ondragover=e=>e.preventDefault(); dzG.ondrop=e=>{e.preventDefault();const id=e.dataTransfer.getData("text");if(id){const ni=App.addSection();App.handleItemAdd(ni,id);App.renderQuotePage();}}; append(dzG);

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
                const wrap = document.createElement('div'); wrap.className = "thumb-wrapper";
                const thumbBox = document.createElement('div'); thumbBox.className = "thumb-scale-box";
                
                let sourcePage;
                if(idx === 1) sourcePage = document.getElementById('page-1');
                else sourcePage = document.getElementById('position-pages-container').children[idx-2];

                if(sourcePage) {
                    const clone = sourcePage.cloneNode(true);
                    clone.removeAttribute('id');
                    clone.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
                    const srcInputs = sourcePage.querySelectorAll('input, textarea, select');
                    const dstInputs = clone.querySelectorAll('input, textarea, select');
                    srcInputs.forEach((inp, i) => { if(dstInputs[i]) { dstInputs[i].value = inp.value; if(inp.checked) dstInputs[i].checked = true; } });
                    thumbBox.appendChild(clone);
                }
                
                const lbl = document.createElement('div'); lbl.className = "thumb-label"; lbl.innerText = `Seite ${idx}`;
                wrap.appendChild(thumbBox); wrap.appendChild(lbl);
                wrap.onclick = () => { if(idx === 1) document.getElementById('page-1').scrollIntoView({behavior: 'smooth'}); else if(sourcePage) sourcePage.scrollIntoView({behavior: 'smooth'}); };
                nav.appendChild(wrap);
            },
            
            openPrintPreview: () => { document.getElementById('print-preview-modal').classList.remove('hidden'); App.renderQuotePage(true); },
            addSection: (t, l) => { State.sections.push({ id: 's'+Date.now(), title: t||`${State.sections.length+1}. Neue Sektion`, description: l?'Dienstleistungen':'Beschreibung', config: { mode: 'standard', pauschalPrice: 0, type: 'standard', hidePrices: false, margin: { value: 0, type: 'fixed' } }, items: [], isLaborSection:l }); App.renderQuotePage(); return State.sections.length-1; },
            renderSidebar: () => { const s=document.getElementById('sidebar-search').value.toLowerCase(); const c=document.getElementById('sidebar-list'); c.innerHTML=''; DB.products.filter(p=>p.name.toLowerCase().includes(s)).forEach(p=>{ const isSet=p.type==='set'; c.innerHTML+=`<div draggable="true" ondragstart="App.dragStart(event, '${p.id}')" class="bg-white border border-slate-200 p-2 rounded shadow-sm cursor-grab hover:border-brand-primary group relative flex items-center gap-2">${isSet?'<div class="absolute -top-1 -right-1 w-2 h-2 bg-brand-primary rounded-full"></div>':''}<div class="w-8 h-8 rounded bg-slate-100 flex-shrink-0 overflow-hidden"><img src="${p.img}" class="w-full h-full object-cover"></div><div class="flex-1 min-w-0"><div class="text-[10px] font-bold text-slate-800 truncate">${p.name}</div></div><button onclick="App.openSetModal('${p.id}')" class="text-slate-300 hover:text-brand-primary"><i class="fa-solid fa-info-circle"></i></button></div>`; }); },
            switchSidebarTab: (tab) => { document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active')); document.getElementById('tab-' + tab).classList.add('active'); if(tab === 'lib') { document.getElementById('sidebar-content-lib').classList.remove('hidden'); document.getElementById('sidebar-content-tools').classList.add('hidden'); } else { document.getElementById('sidebar-content-lib').classList.add('hidden'); document.getElementById('sidebar-content-tools').classList.remove('hidden'); App.renderSidebarTools(); } },
            renderSidebarTools: () => { const c = document.getElementById('tools-list'); c.innerHTML = ''; const defaults = ['https://placehold.co/100x100/green/white?text=Geprüft', 'https://placehold.co/100x100/red/white?text=Angebot', 'https://placehold.co/100x100/orange/white?text=Aktion']; [...defaults, ...State.toolsImages].forEach((src, idx) => { c.innerHTML += `<div draggable="true" ondragstart="App.dragStartTool(event, '${src}')" class="bg-white border rounded p-2 cursor-grab hover:shadow-md"><img src="${src}" class="w-full h-16 object-contain"></div>`; }); },
            dragStart: (ev, id) => ev.dataTransfer.setData("text", id),
            dragStartTool: (ev, src) => { ev.dataTransfer.setData("type", "tool"); ev.dataTransfer.setData("src", src); },
            allowDrop: (ev) => { ev.preventDefault(); ev.currentTarget.classList.add('drag-over'); },
            drop: (ev, sIdx) => { ev.preventDefault(); ev.currentTarget.classList.remove('drag-over'); const id = ev.dataTransfer.getData("text"); if(id) { App.handleItemAdd(sIdx, id); App.renderQuotePage(); } },
            dropTool: (ev, pageIndex) => { ev.preventDefault(); const type = ev.dataTransfer.getData("type"); if(type !== 'tool') return; const src = ev.dataTransfer.getData("src"); const rect = ev.currentTarget.getBoundingClientRect(); State.placedImages.push({ id: Date.now(), src, pageIndex, x: ev.clientX - rect.left, y: ev.clientY - rect.top, width: 100 }); App.renderQuotePage(); },
            removeToolImage: (id) => { State.placedImages = State.placedImages.filter(i => i.id !== id); App.renderQuotePage(); },
            renderFloatingImages: (pageEl, pageIdx, forPrint) => { const images = State.placedImages.filter(img => img.pageIndex === pageIdx); images.forEach(img => { const el = document.createElement('div'); el.className = 'floating-element'; el.style.left = img.x + 'px'; el.style.top = img.y + 'px'; el.style.width = img.width + 'px'; el.innerHTML = `<img src="${img.src}" class="w-full h-auto">` + (forPrint?'':`<div class="delete-float" onclick="App.removeToolImage(${img.id})">x</div>`); if(!forPrint) { el.onmousedown = (e) => { e.stopPropagation(); let startX = e.clientX; let startY = e.clientY; let startLeft = img.x; let startTop = img.y; const onMove = (mv) => { el.style.left = (startLeft + mv.clientX - startX) + 'px'; el.style.top = (startTop + mv.clientY - startY) + 'px'; }; const onUp = (up) => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); img.x = startLeft + up.clientX - startX; img.y = startTop + up.clientY - startY; }; document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp); }; } pageEl.appendChild(el); }); },
            syncDocData: (field, value) => { if(field === 'offerId') State.offerId = value; if(field === 'custId') State.custId = value; document.querySelectorAll('.sync-offer-id').forEach(el => el.innerText = State.offerId); },
            addManualItem: (sIdx) => { State.sections[sIdx].items.push({ name:'Neue Position', desc:'Beschreibung', price:0, ek:0, margin:0, qty:1, unit:'Stk', subItems:[] }); App.renderQuotePage(); },
            handleItemAdd: (sIdx, id) => { const p=DB.products.find(x=>x.id===id); if(!p) return; if(p.type==='set') { const it={productId:id, name:p.name, desc:p.desc, img:p.img, price:p.price, ek:p.price*0.7, margin:30, active:true, qty:1, unit:p.unit, subItems:[]}; if(p.materials) p.materials.forEach(m=>it.subItems.push({...m,qty:1,ek:m.price*0.7,margin:30,active:true})); State.sections[sIdx].items.push(it); if(p.labor) { let lIdx=State.sections.findIndex(s=>s.isLaborSection); if(lIdx===-1) { lIdx=App.addSection('Montage & Dienstleistung', true); } State.sections[lIdx].items.push({name:`Montage: ${p.name}`, desc:'Fachgerechte Installation', price:0, ek:0, margin:0, active:true, qty:1, unit:'Psch', subItems:p.labor.map(l=>({...l, ek:l.price*0.8, margin:20, active:true}))}); } } else { State.sections[sIdx].items.push({name:p.name, desc:p.desc, img:p.img, price:p.price, ek:p.price*0.7, margin:30, active:true, qty:1, unit:p.unit, subItems:[]}); } App.renderQuotePage(); },
            addSubItemFromDrag: (sIdx, iIdx, p) => { State.sections[sIdx].items[iIdx].subItems.push({name: p.name, price: p.price, qty: 1, unit: p.unit, ek: p.price*0.7, margin: 30, active: true}); App.renderQuotePage(); },
            
            // Drag Sort Handlers
            dragStartPos: (ev, sIdx, iIdx) => { 
                App.dragState = { type: 'pos', sIdx, iIdx };
                ev.dataTransfer.effectAllowed = 'move';
                // We set dummy text to ensure drag works in all browsers
                ev.dataTransfer.setData("text/plain", JSON.stringify({type:'pos', sIdx, iIdx}));
            },
            
            moveItem: (fromS, fromI, toS, toI) => {
                if(fromS === toS && fromI === toI) return;
                const item = State.sections[fromS].items[fromI];
                State.sections[fromS].items.splice(fromI, 1);
                // Adjust index if moving within same section downwards
                if (fromS === toS && fromI < toI) { toI--; }
                State.sections[toS].items.splice(toI, 0, item);
                App.renderQuotePage();
            },

            // Updates
            updateQty: (sIdx, iIdx, v, subIdx=null) => { if(subIdx!==null) State.sections[sIdx].items[iIdx].subItems[subIdx].qty=v; else State.sections[sIdx].items[iIdx].qty=v; App.renderQuotePage(); },
            updateItemDetails: (sIdx, iIdx, f, v) => { const it=State.sections[sIdx].items[iIdx]; if(f==='price') it.price=parseFloat(v); else it[f]=v; App.renderQuotePage(); },
            updateSubItemDetails: (sIdx, iIdx, subIdx, f, v) => { const s=State.sections[sIdx].items[iIdx].subItems[subIdx]; if(f==='price') s.price=parseFloat(v); else s.name=v; App.renderQuotePage(); },
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
            openSetModal: (id) => { const p=DB.products.find(x=>x.id===id); if(!p) return; document.getElementById('modal-title').innerText=p.name; document.getElementById('modal-desc').innerText=p.desc; const m=document.getElementById('modal-materials'); m.innerHTML=''; if(p.materials) p.materials.forEach(x=>m.innerHTML+=`<tr><td class="px-4 py-2">${x.name}</td><td class="px-4 py-2 text-right">${x.price}€</td></tr>`); const l=document.getElementById('modal-labor'); l.innerHTML=''; if(p.labor) p.labor.forEach(x=>l.innerHTML+=`<tr><td class="px-4 py-2">${x.name}</td><td class="px-4 py-2 text-center">${x.qty}</td><td class="px-4 py-2 text-right">${x.price}€</td></tr>`); document.getElementById('modal-add-btn').onclick=()=>{App.handleItemAdd(0, id); App.renderQuotePage(); App.closeModal();}; document.getElementById('set-modal').classList.remove('hidden'); },
            closeModal: () => document.getElementById('set-modal').classList.add('hidden'),
            save: () => alert("Angebot gespeichert"),

            // Drag & Tools
            dragStart: (ev, id) => ev.dataTransfer.setData("text", id),
            dragStartTool: (ev, src) => { ev.dataTransfer.setData("type", "tool"); ev.dataTransfer.setData("src", src); },
            allowDrop: (ev) => { ev.preventDefault(); ev.currentTarget.classList.add('drag-over'); },
            drop: (ev, sIdx) => { ev.preventDefault(); ev.currentTarget.classList.remove('drag-over'); const id = ev.dataTransfer.getData("text"); if(id) { App.handleItemAdd(sIdx, id); App.renderQuotePage(); } },
            dropTool: (ev, pageIndex) => { ev.preventDefault(); const type = ev.dataTransfer.getData("type"); if(type !== 'tool') return; const src = ev.dataTransfer.getData("src"); const rect = ev.currentTarget.getBoundingClientRect(); State.placedImages.push({ id: Date.now(), src, pageIndex, x: ev.clientX - rect.left, y: ev.clientY - rect.top, width: 100 }); App.renderQuotePage(); },
            removeToolImage: (id) => { State.placedImages = State.placedImages.filter(i => i.id !== id); App.renderQuotePage(); },
            renderFloatingImages: (pageEl, pageIdx, forPrint) => { const images = State.placedImages.filter(img => img.pageIndex === pageIdx); images.forEach(img => { const el = document.createElement('div'); el.className = 'floating-element'; el.style.left = img.x + 'px'; el.style.top = img.y + 'px'; el.style.width = img.width + 'px'; el.innerHTML = `<img src="${img.src}" class="w-full h-auto">` + (forPrint?'':`<div class="delete-float" onclick="App.removeToolImage(${img.id})">x</div>`); if(!forPrint) { el.onmousedown = (e) => { e.stopPropagation(); let startX = e.clientX; let startY = e.clientY; let startLeft = img.x; let startTop = img.y; const onMove = (mv) => { el.style.left = (startLeft + mv.clientX - startX) + 'px'; el.style.top = (startTop + mv.clientY - startY) + 'px'; }; const onUp = (up) => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); img.x = startLeft + up.clientX - startX; img.y = startTop + up.clientY - startY; }; document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp); }; } pageEl.appendChild(el); }); },
            
            // Badges
            handleImageClick: (sIdx, iIdx) => { App.editingImage = { sIdx, iIdx }; document.getElementById('img-upload-input').click(); },
            handleBadgeClick: (sIdx, iIdx) => { State.editingBadge = { sIdx, iIdx, pos: 'tl', type: '', text: '' }; document.getElementById('badge-modal').classList.remove('hidden'); },
            closeBadgeModal: () => document.getElementById('badge-modal').classList.add('hidden'),
            setBadgePos: (pos) => { if(State.editingBadge) State.editingBadge.pos = pos; },
            saveBadgeConfig: () => { if(!State.editingBadge) return; const { sIdx, iIdx, pos, tempImg } = State.editingBadge; const val = document.getElementById('badge-type-select').value; let badgeObj = null; if(val === 'image' && tempImg) badgeObj = { type: 'image', src: tempImg, pos: pos }; else if (val !== '' && val !== 'image') badgeObj = { type: 'text', text: val, pos: pos }; else if (val === 'image' && !tempImg) { document.getElementById('badge-upload-input').click(); return; } State.sections[sIdx].items[iIdx].badge = badgeObj; App.renderQuotePage(); App.closeBadgeModal(); },
            syncDocData: (field, value) => { if(field === 'offerId') State.offerId = value; if(field === 'custId') State.custId = value; document.querySelectorAll('.sync-offer-id').forEach(el => el.innerText = State.offerId); }
        };

        window.addEventListener('DOMContentLoaded', App.init);
    </script>
</body>
</html>