<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solar Aspekt - Feinaufmaß & Material</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Colors Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#74b2d4',
                            lightBlue: '#cde8ea',
                            green: '#93c11c',
                            lightGreen: '#cfe09b',
                            orange: '#f8ac00'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- SortableJS für Drag und Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <style>
        body { -webkit-tap-highlight-color: transparent; }
        .view-section { display: none; animation: fadeIn 0.3s ease-in-out; }
        .view-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Custom Checkbox Styling using Brand Colors */
        .custom-cb { @apply w-5 h-5 rounded border-slate-300 text-brand-blue focus:ring-brand-blue bg-white shrink-0; }
        
        /* Sticky Header Offset for scrolling */
        .scroll-mt-offset { scroll-margin-top: 180px; }

        /* Smooth sidebar transition */
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">

    <!-- MAIN CONTENT AREA -->
    <main class="h-full overflow-y-auto relative w-full scroll-smooth">
        
        <!-- ==================== VIEW: LIST (DEFAULT) ==================== -->
        <section id="view-list" class="view-section active p-4 md:p-8 max-w-5xl mx-auto pb-24">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-slate-900 text-white p-6 rounded-3xl shadow-lg border-b-4 border-brand-orange gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2"><i class="ph-fill ph-sun text-brand-orange"></i> Solar Aspekt</h1>
                    <p class="text-slate-300 text-sm md:text-base mt-1">Alle Aufmaße im Überblick</p>
                </div>
                <button onclick="showTypeSelection()" class="bg-brand-blue hover:opacity-90 text-white px-4 py-3 rounded-xl font-bold flex items-center gap-2 transition active:scale-95 shadow-md w-full md:w-auto justify-center">
                    <i class="ph-bold ph-plus text-xl"></i> <span>Neues Aufmaß</span>
                </button>
            </header>

            <!-- SEARCH BAR -->
            <div class="mb-6 relative">
                <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-xl"></i>
                <input type="text" id="search-input" oninput="renderList()" placeholder="Suchen nach Name, Ort, Straße, Firma..." class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-brand-blue outline-none transition text-slate-700 font-medium">
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="full-list-container">
                <!-- Wird durch JS befüllt -->
            </div>

            <button onclick="showTypeSelection()" class="md:hidden fixed bottom-6 right-6 bg-brand-blue text-white p-4 rounded-full shadow-lg shadow-brand-blue/30 border-4 border-slate-50 transition active:scale-95 z-40">
                <i class="ph-bold ph-plus text-2xl"></i>
            </button>
        </section>

        <!-- ==================== MODAL: TYPE SELECTION ==================== -->
        <div id="modal-select-type" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl p-6 md:p-8 w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="modal-content">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Was möchten Sie anlegen?</h2>
                    <button onclick="closeTypeSelection()" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <button onclick="createNew('PV')" class="bg-slate-50 border border-slate-200 p-4 rounded-2xl shadow-sm hover:border-brand-orange hover:shadow-md transition active:scale-95 flex items-center gap-4 text-left group">
                        <div class="p-3 bg-brand-orange/20 rounded-xl group-hover:bg-brand-orange transition">
                            <i class="ph-fill ph-solar-panel text-brand-orange group-hover:text-white text-2xl transition"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">PV Anlage</h3>
                            <p class="text-xs text-slate-500">Neues Photovoltaik Feinaufmaß</p>
                        </div>
                    </button>
                    <button onclick="createNew('WP')" class="bg-slate-50 border border-slate-200 p-4 rounded-2xl shadow-sm hover:border-brand-green hover:shadow-md transition active:scale-95 flex items-center gap-4 text-left group">
                        <div class="p-3 bg-brand-green/20 rounded-xl group-hover:bg-brand-green transition">
                            <i class="ph-fill ph-thermometer text-brand-green group-hover:text-white text-2xl transition"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Wärmepumpe</h3>
                            <p class="text-xs text-slate-500">Neues Wärmepumpen Aufmaß</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== MODAL: HISTORY ==================== -->
        <div id="modal-history" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4 md:p-8 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl w-full max-w-2xl h-[80vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="modal-history-content">
                <div class="p-5 md:p-6 rounded-t-3xl border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="ph-fill ph-clock-counter-clockwise text-brand-blue"></i> Änderungshistorie
                        </h2>
                        <p class="text-sm text-slate-500 mt-1" id="history-project-name">Lade...</p>
                    </div>
                    <button onclick="closeHistory()" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
                <div class="p-4 md:p-6 overflow-y-auto flex-1 bg-slate-50" id="history-list-container">
                    <!-- History entries injected here -->
                </div>
            </div>
        </div>

        <!-- ==================== MODAL: MATERIAL LIST EDITOR ==================== -->
        <div id="modal-materials" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4 md:p-8 opacity-0 transition-opacity duration-300">
            <div class="bg-slate-50 rounded-3xl w-full max-w-4xl h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="modal-materials-content">
                <div class="bg-white p-5 md:p-6 rounded-t-3xl border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="ph-fill ph-package text-brand-blue"></i> Material Bearbeiten
                        </h2>
                        <p class="text-sm text-slate-500 mt-1" id="material-project-name">Lade...</p>
                    </div>
                    <button onclick="closeMaterials()" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
                <div class="p-4 md:p-6 overflow-y-auto flex-1 bg-slate-50" id="materials-list-container">
                    <!-- Material items will be injected here via JS -->
                </div>
            </div>
        </div>

        <!-- ==================== MODAL: IMAGES / FOTOS ==================== -->
        <div id="modal-images" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4 md:p-8 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl w-full max-w-4xl h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="modal-images-content">
                <div class="p-5 md:p-6 rounded-t-3xl border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="ph-fill ph-camera text-brand-blue"></i> Projekt Fotos
                        </h2>
                        <p class="text-sm text-slate-500 mt-1" id="images-project-name">Lade...</p>
                    </div>
                    <button onclick="closeImages()" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
                <div class="p-4 md:p-6 overflow-y-auto flex-1 bg-slate-50">
                    <!-- Upload Input (Hidden) & Trigger Button -->
                    <input type="file" id="image-upload-input" accept="image/*" multiple capture="environment" class="hidden" onchange="handleImageUpload(event)">
                    
                    <button onclick="document.getElementById('image-upload-input').click()" class="w-full py-10 border-2 border-dashed border-brand-blue text-brand-blue rounded-2xl flex flex-col items-center justify-center hover:bg-brand-lightBlue/20 transition group bg-white shadow-sm mb-6">
                        <i class="ph-bold ph-camera-plus text-5xl mb-3 group-hover:scale-110 transition-transform"></i>
                        <span class="font-bold text-lg">Fotos aufnehmen oder hochladen</span>
                        <span class="text-sm text-slate-500 mt-1">Klicke hier, um die Kamera oder Galerie zu öffnen</span>
                    </button>

                    <div id="image-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Images injected here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== VIEW: PV FORM ==================== -->
        <section id="view-form-pv" class="view-section p-4 md:p-6 max-w-6xl mx-auto pb-24">
            
            <!-- STICKY HEADER & PROGRESS BAR -->
            <div class="sticky top-0 bg-slate-50/95 backdrop-blur z-30 pt-4 pb-4 -mx-4 px-4 md:mx-0 md:px-0 border-b border-slate-200/50">
                <div class="flex items-center gap-4 mb-3">
                    <button type="button" onclick="navigate('list')" class="p-2 bg-white rounded-full shadow-sm hover:bg-slate-100 transition">
                        <i class="ph ph-arrow-left text-xl text-slate-600"></i>
                    </button>
                    <div class="flex flex-1 items-center gap-3">
                        <div class="p-2 rounded-lg bg-brand-orange text-white"><i class="ph-fill ph-solar-panel text-xl"></i></div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800">PV Feinaufmaß</h2>
                    </div>
                    <button type="button" onclick="document.getElementById('btn-submit-pv').click()" class="bg-brand-orange hover:opacity-90 text-white px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition shadow-md">
                        <i class="ph-bold ph-floppy-disk text-lg"></i> <span class="hidden md:inline">Speichern</span>
                    </button>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-200 h-2 mt-2 rounded-full overflow-hidden">
                    <div id="pv-progress-fill" class="bg-brand-orange h-full w-0 transition-all duration-500 ease-out"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1 font-bold">
                    <span class="flex items-center gap-1"><i class="ph-bold ph-info"></i> Formular Fortschritt</span>
                    <span id="pv-progress-text">0%</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mt-6 items-start relative">
                
                <!-- COLLAPSIBLE SIDEBAR HISTORY (DESKTOP) -->
                <aside id="pv-sidebar" class="hidden md:flex flex-col w-20 shrink-0 sticky top-36 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 sidebar-transition overflow-hidden">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h4 id="pv-sidebar-title" class="font-bold text-slate-800 whitespace-nowrap transition-opacity duration-300 opacity-0 hidden">Übersicht</h4>
                        <button type="button" onclick="toggleSidebar('pv')" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1 rounded-lg transition shrink-0">
                            <i id="pv-sidebar-icon" class="ph-bold ph-caret-right text-lg"></i>
                        </button>
                    </div>
                    <ul class="space-y-4 relative before:absolute before:inset-y-0 before:left-[11px] before:w-0.5 before:bg-slate-100 before:-z-10 w-full">
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-kunden')">
                            <div id="pv-nav-kunden" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-kunden" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Kundendaten</span>
                                <span id="pv-navcount-kunden" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-anlage')">
                            <div id="pv-nav-anlage" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-anlage" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Kunde bekommt</span>
                                <span id="pv-navcount-anlage" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-daecher')">
                            <div id="pv-nav-daecher" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-daecher" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Objekt & Dächer</span>
                                <span id="pv-navcount-daecher" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-absicherung')">
                            <div id="pv-nav-absicherung" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-absicherung" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Absicherung</span>
                                <span id="pv-navcount-absicherung" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-elektro')">
                            <div id="pv-nav-elektro" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-elektro" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Zählerschrank</span>
                                <span id="pv-navcount-elektro" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-notizen')">
                            <div id="pv-nav-notizen" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-notizen" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Notizen</span>
                                <span id="pv-navcount-notizen" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                    </ul>
                </aside>

                <!-- FORM CONTENT -->
                <form id="form-pv" onsubmit="saveRecord(event, 'PV')" class="flex-1 space-y-4 w-full min-w-0">
                    <input type="hidden" name="id" id="pv-id">
                    <div class="mb-2 text-sm text-slate-500 flex items-center gap-2"><span class="text-red-500 font-bold">*</span> markiert Pflichtfelder</div>
                    
                    <!-- KUNDENDATEN -->
                    <div id="pv-sec-kunden" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="kunden">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-kunden', 'pv-icon-kunden')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-user text-brand-orange"></i> Angaben des Kunden</span>
                            <i id="pv-icon-kunden" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="pv-content-kunden" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Firma <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <input type="text" name="firma" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Vorname <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Nachname <span class="text-red-500">*</span></label>
                                    <input type="text" name="lastname" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Straße & Nr. <span class="text-red-500">*</span></label>
                                    <input type="text" name="street" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">PLZ <span class="text-red-500">*</span></label>
                                    <input type="text" name="postcode" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Ort <span class="text-red-500">*</span></label>
                                    <input type="text" name="city" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Telefon <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <input type="text" name="telephone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Mobil <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <input type="text" name="phone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">E-Mail <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <input type="email" name="email" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>

                            <div class="md:col-span-3 pt-4 border-t border-slate-100 mt-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Feinaufmaß durch (Name)</label>
                                        <input type="text" name="contact_person" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Datum</label>
                                        <input type="date" name="request_date" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KUNDE BEKOMMT -->
                    <div id="pv-sec-anlage" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="anlage">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-anlage', 'pv-icon-anlage')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-lightning text-brand-orange"></i> Kunde Bekommt</span>
                            <i id="pv-icon-anlage" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        
                        <div id="pv-content-anlage" class="p-5">
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Projektart <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-orange transition"><input type="radio" name="objective" value="Neuanlage" required class="custom-cb focus:ring-brand-orange"> Neuanlage</label>
                                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-orange transition"><input type="radio" name="objective" value="Erweiterung" required class="custom-cb focus:ring-brand-orange"> Erweiterung</label>
                                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-orange transition"><input type="radio" name="objective" value="Demontage" required class="custom-cb focus:ring-brand-orange"> Demontage alt</label>
                                </div>
                                <div class="mt-3 bg-slate-50 p-3 rounded-lg border border-slate-200 text-sm">
                                    <span class="font-bold text-slate-700">Bei Demontage:</span>
                                    <div class="flex flex-wrap gap-4 mt-2">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_demontageVerbleib" value="Kunde" class="custom-cb focus:ring-brand-orange"> Module beim Kunden lassen</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_demontageVerbleib" value="Lager" class="custom-cb focus:ring-brand-orange"> mitnehmen zu uns ins Lager</label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Anlagengröße (kWp) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.1" name="kwp_size" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Anzahl Module <span class="text-red-500">*</span></label>
                                    <input type="number" name="module_count" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kabelführung</label>
                                    <div class="flex items-center gap-2 p-3 border border-slate-200 rounded-xl bg-slate-50 h-[46px]">
                                        <span class="text-xs">Ausreichend für weitere Kabel?</span>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm ml-auto"><input type="radio" name="note_kabelAusreichend" value="Ja" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_kabelAusreichend" value="Nein" class="custom-cb focus:ring-brand-orange"> Nein</label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 border-t border-slate-100 pt-4">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Zusatz-Komponenten <span class="text-slate-400 font-normal normal-case">(Optional)</span></p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition">
                                        <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer"><input type="checkbox" name="storage_preference" value="Ja" class="custom-cb focus:ring-brand-orange"> Batteriespeicher</label>
                                        <input type="text" name="note_battery_type" placeholder="Hersteller/Typ" class="w-full p-2 mb-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                        <input type="text" name="note_battery_size" placeholder="geplante Größe" class="w-full p-2 mb-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                        <input type="text" name="note_battery_location" placeholder="Aufstellort" class="w-full p-2 mb-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                        <div class="flex gap-2">
                                            <input type="number" name="note_batteryDistWrZs" placeholder="WR -> ZS (m)" class="w-1/2 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                            <input type="number" name="note_batteryDistBaWr" placeholder="BA -> WR (m)" class="w-1/2 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                        </div>
                                    </div>

                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition">
                                        <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer"><input type="checkbox" name="note_wp_integration" value="Ja" class="custom-cb focus:ring-brand-orange"> Wärmepumpe (PV-Integration)</label>
                                        <input type="text" name="note_wp_type" placeholder="Hersteller/Typ" class="w-full p-2 mb-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                        <div class="flex gap-4 mb-2">
                                            <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wpStatus" value="vorhanden" class="custom-cb focus:ring-brand-orange"> vorhanden</label>
                                            <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wpStatus" value="geplant" class="custom-cb focus:ring-brand-orange"> geplant</label>
                                        </div>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="checkbox" name="note_wp_heizstab" value="Ja" class="focus:ring-brand-orange"> Heizstab</label>
                                            <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="checkbox" name="enwg_14a_ready" value="1" class="focus:ring-brand-orange"> SG Ready</label>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer"><input type="checkbox" name="wallbox_desired" value="1" class="custom-cb focus:ring-brand-orange"> Wallbox gewünscht</label>
                                            <input type="text" name="wallbox_location" placeholder="Aufstellort" class="w-full p-2 mb-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                            <div class="flex items-center gap-2 mb-2">
                                                <input type="number" name="note_wallbox_distance" placeholder="Entfernung zum ZS" class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                                <span class="text-sm">m</span>
                                            </div>
                                            <label class="flex items-center gap-2 text-sm cursor-pointer mt-2"><input type="checkbox" name="note_wallboxKernbohrung" value="Ja" class="custom-cb focus:ring-brand-orange"> Kernbohrung Aussenw. WU-Beton</label>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm mb-2 text-slate-700">Erdarbeiten</h4>
                                            <div class="flex items-center gap-3 mb-2">
                                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wbErdarbeiten" value="Ja" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wbErdarbeiten" value="Nein" class="custom-cb focus:ring-brand-orange"> Nein</label>
                                                <input type="text" name="note_wbErdarbeitenLaenge" placeholder="Länge (m)" class="w-24 p-1.5 border rounded outline-none focus:ring-brand-orange ml-auto">
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wbErdarbeitenDurch" value="Solar Aspekt" class="custom-cb focus:ring-brand-orange"> durch uns/Gala Bauer</label>
                                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="note_wbErdarbeitenDurch" value="Kunde" class="custom-cb focus:ring-brand-orange"> Kunde/Gala-Bauer</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="md:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Sonstige Kundenwünsche</label>
                                        <textarea name="note_sonstigeWunsche" rows="2" class="w-full p-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DÄCHER -->
                    <div id="pv-sec-daecher" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="daecher">
                        <div class="flex justify-between items-center p-5 border-b border-slate-100 rounded-t-2xl hover:bg-slate-50 transition cursor-pointer" onclick="toggleSection('pv-content-daecher', 'pv-icon-daecher')">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2"><i class="ph-fill ph-house text-brand-orange"></i> Objekt & Dächer</h3>
                            <div class="flex items-center gap-4">
                                <button type="button" onclick="event.stopPropagation(); addRoofUI()" class="text-brand-orange bg-brand-orange/10 hover:bg-brand-orange/20 px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-1 transition">
                                    <i class="ph-bold ph-plus"></i> Dach hinzufügen
                                </button>
                                <i id="pv-icon-daecher" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                            </div>
                        </div>
                        
                        <div id="pv-content-daecher" class="p-5">
                            <p class="text-xs text-slate-500 mb-4">Mindestens ein Dach muss angelegt werden <span class="text-red-500">*</span></p>
                            <div id="roofs-container" class="space-y-6">
                                <!-- Dächer werden hier dynamisch eingefügt -->
                            </div>
                        </div>
                    </div>

                    <!-- ABSICHERUNG -->
                    <div id="pv-sec-absicherung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="absicherung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-absicherung', 'pv-icon-absicherung')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-shield-check text-brand-orange"></i> Absicherung</span>
                            <i id="pv-icon-absicherung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="pv-content-absicherung" class="p-5 space-y-4">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 justify-between md:items-center">
                                <span class="font-bold text-sm w-36">Fangschutzgitter</span>
                                <div class="flex flex-wrap gap-3">
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_fangschutz" value="möglich" class="custom-cb focus:ring-brand-orange"> möglich</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_fangschutz" value="teilweise" class="custom-cb focus:ring-brand-orange"> teilweise</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_fangschutz" value="nicht möglich" class="custom-cb focus:ring-brand-orange"> nicht möglich</label>
                                </div>
                                <input type="text" name="note_fangschutz_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col gap-2">
                                <span class="font-bold text-sm">Gerüst</span>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center">
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="checkbox" name="scaffold_usage" value="1" class="custom-cb focus:ring-brand-orange"> muss gestellt werden</label>
                                    </div>
                                    <input type="text" name="note_scaffold_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center mt-2">
                                    <div class="flex flex-wrap gap-3">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_geruestMachbar" value="möglich" class="custom-cb focus:ring-brand-orange"> möglich</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_geruestMachbar" value="teilweise" class="custom-cb focus:ring-brand-orange"> teilweise</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_geruestMachbar" value="nicht möglich" class="custom-cb focus:ring-brand-orange"> nicht möglich</label>
                                    </div>
                                    <input type="text" name="note_geruestMachbar_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col gap-2">
                                <span class="font-bold text-sm">Aufzug</span>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center">
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="checkbox" name="note_aufzugMuss" value="1" class="custom-cb focus:ring-brand-orange"> muss gestellt werden</label>
                                    </div>
                                    <input type="text" name="note_aufzug_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center mt-2">
                                    <div class="flex flex-wrap gap-3">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_aufzugMachbar" value="möglich" class="custom-cb focus:ring-brand-orange"> möglich</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_aufzugMachbar" value="teilweise" class="custom-cb focus:ring-brand-orange"> teilweise</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_aufzugMachbar" value="nicht möglich" class="custom-cb focus:ring-brand-orange"> nicht möglich</label>
                                    </div>
                                    <input type="text" name="note_aufzugMachbar_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col gap-2">
                                <span class="font-bold text-sm">Kran</span>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center">
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="checkbox" name="note_kranMuss" value="1" class="custom-cb focus:ring-brand-orange"> muss gestellt werden</label>
                                    </div>
                                    <input type="text" name="note_kran_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                                <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center mt-2">
                                    <div class="flex flex-wrap gap-3">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_kranMachbar" value="möglich" class="custom-cb focus:ring-brand-orange"> möglich</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_kranMachbar" value="teilweise" class="custom-cb focus:ring-brand-orange"> teilweise</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_kranMachbar" value="nicht möglich" class="custom-cb focus:ring-brand-orange"> nicht möglich</label>
                                    </div>
                                    <input type="text" name="note_kranMachbar_reason" placeholder="Begründung..." class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none max-w-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ZÄHLERSCHRANK -->
                    <div id="pv-sec-elektro" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="elektro">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-elektro', 'pv-icon-elektro')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-box-arrow-down text-brand-orange"></i> Zählerschrank & Elektrik</span>
                            <i id="pv-icon-elektro" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="pv-content-elektro" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6">
                                <label class="flex items-center justify-between p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-lightBlue transition"><span class="font-medium text-sm">AC-Überspannungsschutz vorh.</span> <input type="checkbox" name="ac_surge_protection" value="1" class="custom-cb focus:ring-brand-orange"></label>
                                <label class="flex items-center justify-between p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-lightBlue transition"><span class="font-medium text-sm">SLS Schalter vorhanden</span> <input type="checkbox" name="sls_switch" value="1" class="custom-cb focus:ring-brand-orange"></label>
                                
                                <div class="flex items-center justify-between p-3 border rounded-xl bg-slate-50">
                                    <span class="font-medium text-sm">Anzahl WE:</span>
                                    <input type="number" name="number_we" class="w-16 p-1 border rounded outline-none focus:ring-brand-orange">
                                </div>
                                <div class="flex items-center justify-between p-3 border rounded-xl bg-slate-50">
                                    <span class="font-medium text-sm">Mieterstrommodell gew.:</span>
                                    <div class="flex gap-2">
                                        <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="tenant_model" value="1" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                        <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="tenant_model" value="0" class="custom-cb focus:ring-brand-orange"> Nein</label>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col p-3 border rounded-xl bg-slate-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-sm">Zählerschrank Aktion:</span>
                                    </div>
                                    <div class="flex flex-col gap-2 text-sm">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="meter_cabinet_action" value="neuer Zählerschrank notwendig" class="custom-cb focus:ring-brand-orange"> neuer ZS notwendig</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="meter_cabinet_action" value="alter Zählerschrank wird zur Unterverteilung" class="custom-cb focus:ring-brand-orange"> alter ZS wird zur Unterverteilung</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="meter_cabinet_action" value="zusätzliche Unterverteilung" class="custom-cb focus:ring-brand-orange"> zusätzliche Unterverteilung</label>
                                    </div>
                                </div>

                                <div class="flex flex-col p-3 border rounded-xl bg-slate-50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-sm">Neuer ZS Größe:</span>
                                    </div>
                                    <div class="flex gap-3 text-sm mt-1">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="cabinet_size" value="550" class="custom-cb focus:ring-brand-orange"> 550</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="cabinet_size" value="800" class="custom-cb focus:ring-brand-orange"> 800</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="cabinet_size" value="1100" class="custom-cb focus:ring-brand-orange"> 1100</label>
                                    </div>
                                </div>

                                <div class="md:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-4">
                                        <span class="font-medium text-sm w-full md:w-48">Zwischenzähler gewünscht:</span>
                                        <div class="flex gap-3 w-full md:w-auto">
                                            <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_zwischenzaehler" value="Ja" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                            <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_zwischenzaehler" value="Nein" class="custom-cb focus:ring-brand-orange"> Nein</label>
                                        </div>
                                        <div class="flex items-center gap-2 w-full md:w-auto"><span class="text-sm">Anz:</span><input type="number" name="meter_count" class="w-16 p-1 border rounded outline-none focus:ring-brand-orange"></div>
                                    </div>
                                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                                        <span class="font-medium text-sm w-full md:w-48 text-slate-500 pl-4 border-l-2 border-slate-200"><i class="ph-bold ph-arrow-elbow-down-right"></i> für Wärmepumpe:</span>
                                        <div class="flex gap-3 w-full md:w-auto">
                                            <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_zwischenzaehlerWp" value="Ja" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                            <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_zwischenzaehlerWp" value="Nein" class="custom-cb focus:ring-brand-orange"> Nein</label>
                                        </div>
                                        <div class="flex items-center gap-2 w-full md:w-auto"><span class="text-sm">Anz:</span><input type="number" name="note_zwischenzaehlerWpCount" class="w-16 p-1 border rounded outline-none focus:ring-brand-orange"></div>
                                    </div>
                                </div>

                                <div class="md:col-span-2 pt-4">
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Internet-Anbindung <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <select name="network_wlan" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="WLAN">WLAN (Standard)</option>
                                        <option value="LAN">LAN</option>
                                        <option value="Powerline">Powerline</option>
                                        <option value="Dongle">Dongle</option>
                                    </select>
                                    <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 mt-3">
                                        <label class="flex items-center gap-1 text-sm cursor-pointer font-medium"><input type="checkbox" name="note_internetSteckdose" class="custom-cb focus:ring-brand-orange"> Steckdose setzen</label>
                                        <span class="text-sm text-slate-500">Entfernung zur nächsten:</span>
                                        <input type="text" name="note_internetSteckdoseDist" class="w-24 p-1.5 border rounded outline-none focus:ring-brand-orange" placeholder="z.B. 2m">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ZUSÄTZLICHE NOTIZEN MIT DIKTIERFUNKTION -->
                    <div id="pv-sec-notizen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="notizen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-notizen', 'pv-icon-notizen')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-notebook text-brand-orange"></i> Zusätzliche Notizen</span>
                            <i id="pv-icon-notizen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="pv-content-notizen" class="p-5">
                            <div class="relative group">
                                <textarea id="pv-notes" name="info" rows="4" class="w-full p-4 pr-14 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange resize-none transition-shadow" placeholder="Tippen oder auf das Mikrofon klicken zum Diktieren..."></textarea>
                                <button type="button" onclick="toggleDictation('pv-notes', 'pv-mic-icon')" class="absolute top-3 right-3 p-2 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-brand-orange hover:border-brand-orange/30 transition active:scale-95" title="Spracheingabe starten/stoppen">
                                    <i id="pv-mic-icon" class="ph-bold ph-microphone text-2xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button id="btn-submit-pv" type="submit" class="w-full mt-4 bg-brand-orange hover:opacity-90 text-white p-4 rounded-2xl font-bold flex justify-center items-center gap-2 shadow-lg shadow-brand-orange/30 transition active:scale-95 text-lg">
                        <i class="ph-bold ph-floppy-disk text-2xl"></i> PV Aufmaß Speichern
                    </button>
                </form>
            </div>
        </section>

        <!-- ==================== VIEW: WP FORM ==================== -->
        <section id="view-form-wp" class="view-section p-4 md:p-6 max-w-6xl mx-auto pb-24">
            
            <!-- STICKY HEADER & PROGRESS BAR -->
            <div class="sticky top-0 bg-slate-50/95 backdrop-blur z-30 pt-4 pb-4 -mx-4 px-4 md:mx-0 md:px-0 border-b border-slate-200/50">
                <div class="flex items-center gap-4 mb-3">
                    <button type="button" onclick="navigate('list')" class="p-2 bg-white rounded-full shadow-sm hover:bg-slate-100 transition">
                        <i class="ph ph-arrow-left text-xl text-slate-600"></i>
                    </button>
                    <div class="flex flex-1 items-center gap-3">
                        <div class="p-2 rounded-lg bg-brand-green text-white"><i class="ph-fill ph-thermometer text-xl"></i></div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800">WP Aufmaß</h2>
                    </div>
                    <button type="button" onclick="document.getElementById('btn-submit-wp').click()" class="bg-brand-green hover:opacity-90 text-white px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition shadow-md">
                        <i class="ph-bold ph-floppy-disk text-lg"></i> <span class="hidden md:inline">Speichern</span>
                    </button>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-200 h-2 mt-2 rounded-full overflow-hidden">
                    <div id="wp-progress-fill" class="bg-brand-green h-full w-0 transition-all duration-500 ease-out"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1 font-bold">
                    <span class="flex items-center gap-1"><i class="ph-bold ph-info"></i> Formular Fortschritt</span>
                    <span id="wp-progress-text">0%</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mt-6 items-start relative">
                
                <!-- COLLAPSIBLE SIDEBAR HISTORY (DESKTOP) -->
                <aside id="wp-sidebar" class="hidden md:flex flex-col w-20 shrink-0 sticky top-36 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 sidebar-transition overflow-hidden">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h4 id="wp-sidebar-title" class="font-bold text-slate-800 whitespace-nowrap transition-opacity duration-300 opacity-0 hidden">Übersicht</h4>
                        <button type="button" onclick="toggleSidebar('wp')" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1 rounded-lg transition shrink-0">
                            <i id="wp-sidebar-icon" class="ph-bold ph-caret-right text-lg"></i>
                        </button>
                    </div>
                    <ul class="space-y-4 relative before:absolute before:inset-y-0 before:left-[11px] before:w-0.5 before:bg-slate-100 before:-z-10 w-full">
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-kunden')">
                            <div id="wp-nav-kunden" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-kunden" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Kunde & Berater</span>
                                <span id="wp-navcount-kunden" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-gebaeude')">
                            <div id="wp-nav-gebaeude" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-gebaeude" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Gebäude</span>
                                <span id="wp-navcount-gebaeude" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-heizung')">
                            <div id="wp-nav-heizung" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-heizung" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Aktuelle Heizung</span>
                                <span id="wp-navcount-heizung" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-etagen')">
                            <div id="wp-nav-etagen" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-etagen" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Etagen & Zustand</span>
                                <span id="wp-navcount-etagen" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-anlage')">
                            <div id="wp-nav-anlage" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-anlage" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Neue Anlage</span>
                                <span id="wp-navcount-anlage" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-einbringung')">
                            <div id="wp-nav-einbringung" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-einbringung" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Einbringmaße</span>
                                <span id="wp-navcount-einbringung" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-elektro')">
                            <div id="wp-nav-elektro" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-elektro" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Elektroinstallation</span>
                                <span id="wp-navcount-elektro" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-sonstiges')">
                            <div id="wp-nav-sonstiges" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-sonstiges" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Sonstige Arbeiten</span>
                                <span id="wp-navcount-sonstiges" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-schall')">
                            <div id="wp-nav-schall" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-schall" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Schallberechnung</span>
                                <span id="wp-navcount-schall" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('wp-sec-notizen')">
                            <div id="wp-nav-notizen" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="wp-navtext-notizen" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Notizen</span>
                                <span id="wp-navcount-notizen" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>
                    </ul>
                </aside>

                <!-- FORM CONTENT -->
                <form id="form-wp" onsubmit="saveRecord(event, 'WP')" class="flex-1 space-y-4 w-full min-w-0">
                    <input type="hidden" name="id" id="wp-id">
                    <div class="mb-2 text-sm text-slate-500 flex items-center gap-2"><span class="text-red-500 font-bold">*</span> markiert Pflichtfelder</div>
                    
                    <!-- KUNDENDATEN -->
                    <div id="wp-sec-kunden" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="kunden">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-kunden', 'wp-icon-kunden')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-users text-brand-green"></i> Kunde & Berater</span>
                            <i id="wp-icon-kunden" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-kunden" class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Kundenangaben -->
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Firma <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="text" name="firma" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Vorname <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nachname <span class="text-red-500">*</span></label>
                                <input type="text" name="lastname" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Straße & Nr. <span class="text-red-500">*</span></label>
                                <input type="text" name="street" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">PLZ <span class="text-red-500">*</span></label>
                                <input type="text" name="postcode" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ort <span class="text-red-500">*</span></label>
                                <input type="text" name="city" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Telefon <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="text" name="telephone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Mobil <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="text" name="phone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">E-Mail <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="email" name="email" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>
                            
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Berater/Monteur <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="text" name="contact_person" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>

                            <!-- Abweichender Standort -->
                            <div class="md:col-span-3 mt-2 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">Standort der Anlage (falls abweichend)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <input type="text" name="alt_street" placeholder="Straße/Nr." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                    </div>
                                    <div>
                                        <input type="text" name="alt_postcode" placeholder="PLZ" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                    </div>
                                    <div>
                                        <input type="text" name="alt_city" placeholder="Ort" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GEBÄUDE -->
                    <div id="wp-sec-gebaeude" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="gebaeude">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-gebaeude', 'wp-icon-gebaeude')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-buildings text-brand-green"></i> Gebäudeeigenschaften</span>
                            <i id="wp-icon-gebaeude" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-gebaeude" class="p-5">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Gebäudeart <span class="text-red-500">*</span></label>
                                    <select name="building_type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Einfamilienhaus">Einfamilienhaus</option>
                                        <option value="Reihenmittelhaus">Reihenmittelhaus</option>
                                        <option value="Doppelhaushälfte">Doppelhaushälfte</option>
                                        <option value="Mehrfamilienhaus">Mehrfamilienhaus</option>
                                        <option value="Gewerbe">Gewerbe</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Wohneinheiten (Anzahl)</label>
                                    <input type="number" name="number_we" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Baujahr <span class="text-red-500">*</span></label>
                                    <input type="number" name="house_year" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Wohnfläche (m²) <span class="text-red-500">*</span></label>
                                    <input type="number" name="living_space" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nutzfläche (m²) <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                    <input type="number" name="unusable_space" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Personen pro Haushalt</label>
                                    <input type="number" name="number_people" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Anzahl Bäder</label>
                                    <input type="number" name="bathroom_count" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                            </div>
                            
                            <!-- Badewanne / Schwimmbad -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-100">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-2">Badewanne</h4>
                                    <div class="flex items-center gap-4 mb-2">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_bathtub" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_bathtub" value="Ja" class="custom-cb focus:ring-brand-green"> Ja, wie viele?</label>
                                        <input type="number" name="bathtub_count" class="w-16 p-1 border rounded focus:ring-brand-green outline-none">
                                    </div>
                                    <input type="text" name="note_bathtubDim" placeholder="Abmessung" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-green outline-none">
                                </div>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-2">Schwimmbad</h4>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_pool" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_pool" value="Ja" class="custom-cb focus:ring-brand-green"> Ja, wie viel m³?</label>
                                        <input type="number" name="note_poolVolume" class="w-20 p-1 border rounded focus:ring-brand-green outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AKTUELLE HEIZUNG -->
                    <div id="wp-sec-heizung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="heizung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-heizung', 'wp-icon-heizung')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-fire text-brand-green"></i> Aktuelle Heizung</span>
                            <i id="wp-icon-heizung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-heizung" class="p-5">
                            <div class="mb-4 flex items-center gap-4">
                                <label class="text-xs font-bold text-slate-600 uppercase">Kamin vorhanden?</label>
                                <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="fireplace" value="1" class="custom-cb focus:ring-brand-green"> Ja</label>
                                <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="fireplace" value="0" class="custom-cb focus:ring-brand-green"> Nein</label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Art <span class="text-red-500">*</span></label>
                                    <select name="heating_system_type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option><option value="Öl">Öl</option><option value="Gas">Gas</option><option value="Pellets">Pellets</option><option value="Sonstiges">Sonstiges</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Leistung (kW) <span class="text-red-500">*</span></label>
                                    <input type="number" name="old_heating_power" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Aufstellort Geschoss</label>
                                    <select name="installation_location" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Wählen...</option>
                                        <option value="KG">KG</option>
                                        <option value="EG">EG</option>
                                        <option value="OG">OG</option>
                                        <option value="DG">DG</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Besonderheiten vorhanden</label>
                                <input type="text" name="heating_notes" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                            </div>

                            <!-- Leitungen -->
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 mt-4 mb-4">
                                <h4 class="font-bold text-sm mb-3 text-slate-700">Welche Leitungen sind verlegt?</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div><span class="block text-xs font-bold text-slate-500 uppercase mb-1">Heizung:</span>
                                        <select name="pipe_system_material" class="w-full p-2 border rounded-lg mb-1 outline-none focus:border-brand-green"><option value="">Material...</option><option value="Kupfer">Kupfer</option><option value="Kunststoff">Kunststoff</option></select>
                                        <input type="text" name="heating_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                    </div>
                                    <div><span class="block text-xs font-bold text-slate-500 uppercase mb-1">Kalt-Wasser / Warm-Wasser:</span>
                                        <input type="text" name="water_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green mt-1">
                                    </div>
                                    <div><span class="block text-xs font-bold text-slate-500 uppercase mb-1">Zirkulation:</span>
                                        <input type="text" name="circulation_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green mt-1">
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-4">
                                    <label class="text-xs font-bold text-slate-600 uppercase">Ein-Rohr-System vorhanden?</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_einRohr" value="Ja" class="custom-cb focus:ring-brand-green"> Ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="note_einRohr" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                </div>
                            </div>

                            <!-- Solar & Warmwasser -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3 text-slate-700">Thermische Solaranlage vorhanden?</h4>
                                    <div class="flex items-center gap-4 mb-3">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="solar_thermal" value="1" class="custom-cb focus:ring-brand-green"> Ja</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="solar_thermal" value="0" class="custom-cb focus:ring-brand-green"> Nein</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-500 uppercase">Anzahl Module/Fläche:</span>
                                        <input type="number" name="solar_thermal_area" class="w-20 p-1.5 border rounded-lg outline-none focus:ring-brand-green">
                                    </div>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3 text-slate-700">Warmwasser Aufbereitung</h4>
                                    <div class="flex items-center gap-4 mb-3">
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="hot_water_generation" value="direkt" class="custom-cb focus:ring-brand-green"> direkt</label>
                                        <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="hot_water_generation" value="indirekt" class="custom-cb focus:ring-brand-green"> indirekt</label>
                                    </div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-bold text-slate-500 uppercase">Fassungsvermögen (l):</span>
                                        <input type="number" name="hot_water_tank_liters" class="w-24 p-1.5 border rounded-lg outline-none focus:ring-brand-green">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ETAGEN & ZUSTAND -->
                    <div id="wp-sec-etagen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="etagen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-etagen', 'wp-icon-etagen')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-thermometer-hot text-brand-green"></i> Heizkreise & Zustand</span>
                            <i id="wp-icon-etagen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-etagen" class="p-5">
                            
                            <!-- ORIGINAL PDF KG/EG/OG/DG ETAGE GRID -->
                            <h4 class="font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Heizungen pro Etage</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="p-3 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-2 text-brand-blue">Kellergeschoss (KG)</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <label class="cursor-pointer"><input type="radio" name="note_kgHeiz" value="beheizt" class="custom-cb focus:ring-brand-green"> beheizt</label>
                                        <label class="cursor-pointer"><input type="radio" name="note_kgHeiz" value="nicht beheizt" class="custom-cb focus:ring-brand-green"> nicht beheizt</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_kgFbh" value="1" class="custom-cb focus:ring-brand-green"> Fussbodenheizung</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_kgHk" value="1" class="custom-cb focus:ring-brand-green"> Heizkörper</label>
                                    </div>
                                </div>
                                <div class="p-3 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-2 text-brand-blue">Erdgeschoss (EG)</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <label class="cursor-pointer"><input type="radio" name="note_egHeiz" value="beheizt" class="custom-cb focus:ring-brand-green"> beheizt</label>
                                        <label class="cursor-pointer"><input type="radio" name="note_egHeiz" value="nicht beheizt" class="custom-cb focus:ring-brand-green"> nicht beheizt</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_egFbh" value="1" class="custom-cb focus:ring-brand-green"> Fussbodenheizung</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_egHk" value="1" class="custom-cb focus:ring-brand-green"> Heizkörper</label>
                                    </div>
                                </div>
                                <div class="p-3 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-2 text-brand-blue">Obergeschoss (OG)</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <label class="cursor-pointer"><input type="radio" name="note_ogHeiz" value="beheizt" class="custom-cb focus:ring-brand-green"> beheizt</label>
                                        <label class="cursor-pointer"><input type="radio" name="note_ogHeiz" value="nicht beheizt" class="custom-cb focus:ring-brand-green"> nicht beheizt</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_ogFbh" value="1" class="custom-cb focus:ring-brand-green"> Fussbodenheizung</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_ogHk" value="1" class="custom-cb focus:ring-brand-green"> Heizkörper</label>
                                    </div>
                                </div>
                                <div class="p-3 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-2 text-brand-blue">Dachgeschoss (DG)</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <label class="cursor-pointer"><input type="radio" name="note_dgHeiz" value="beheizt" class="custom-cb focus:ring-brand-green"> beheizt</label>
                                        <label class="cursor-pointer"><input type="radio" name="note_dgHeiz" value="nicht beheizt" class="custom-cb focus:ring-brand-green"> nicht beheizt</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_dgFbh" value="1" class="custom-cb focus:ring-brand-green"> Fussbodenheizung</label>
                                        <label class="cursor-pointer"><input type="checkbox" name="note_dgHk" value="1" class="custom-cb focus:ring-brand-green"> Heizkörper</label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase w-24">Heizkreis 1 Vorlauf °C:</span>
                                    <input type="number" name="flow_temperature" placeholder="Vorlauf °C" class="w-1/2 p-2 border rounded-lg focus:ring-brand-green outline-none">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase w-24">Heizkreis 2 Vorlauf °C:</span>
                                    <input type="number" name="note_flow_temperature_2" placeholder="Vorlauf °C" class="w-1/2 p-2 border rounded-lg focus:ring-brand-green outline-none">
                                </div>
                            </div>

                            <h4 class="font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Zustand Fussbodenheizung / Heizkörper</h4>
                            <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <span class="text-sm font-medium">Regler/Thermostate für Kühlung geeignet?</span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_reglerKuehlung" value="Ja" class="custom-cb focus:ring-brand-green"> ja</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_reglerKuehlung" value="Nein" class="custom-cb focus:ring-brand-green"> nein</label>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <span class="text-sm font-medium">Heizkreisverteiler für hydr. Abgleich geeignet?</span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_hkvAbgleich" value="Ja" class="custom-cb focus:ring-brand-green"> ja</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_hkvAbgleich" value="Nein" class="custom-cb focus:ring-brand-green"> nein</label>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <span class="text-sm font-medium">Stellantriebe für hydr. Abgleich geeignet?</span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_stellantriebAbgleich" value="Ja" class="custom-cb focus:ring-brand-green"> ja</label>
                                        <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_stellantriebAbgleich" value="Nein" class="custom-cb focus:ring-brand-green"> nein</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEUE ANLAGE -->
                    <div id="wp-sec-anlage" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="anlage">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-anlage', 'wp-icon-anlage')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-package text-brand-green"></i> Neue Anlage / Aufstellmöglichkeit</span>
                            <i id="wp-icon-anlage" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-anlage" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Neue Wärmequelle <span class="text-red-500">*</span></label>
                                    <select name="objective" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Luft-Wasser Wärmepumpe">Luft-Wasser Wärmepumpe</option>
                                        <option value="Sole-Wasser Wärmepumpe">Sole-Wasser Wärmepumpe</option>
                                        <option value="Abluft-Wärmepumpe">Abluft-Wärmepumpe</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-bold text-slate-600 uppercase">Interesse an Passiv-Kühlung?</span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_passivKuehlung" value="Ja" class="custom-cb focus:ring-brand-green"> ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_passivKuehlung" value="Nein" class="custom-cb focus:ring-brand-green"> nein</label>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 mb-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="text-xs font-bold text-slate-600 uppercase w-20">Lüftung:</span>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="ventilation_type" value="vorhanden Ja" class="custom-cb focus:ring-brand-green"> vorhanden Ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="ventilation_type" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="ventilation_type" value="geplant zentral" class="custom-cb focus:ring-brand-green"> geplant zentral</label>
                                    <label class="flex items-center gap-1 cursor-pointer text-sm"><input type="radio" name="ventilation_type" value="geplant dezentral" class="custom-cb focus:ring-brand-green"> geplant dezentral</label>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-bold text-slate-600 uppercase w-48">Platz für VVM 500 vorhanden?</span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_platzVvm500" value="Ja" class="custom-cb focus:ring-brand-green"> Ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_platzVvm500" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-bold text-slate-600 uppercase w-48">Platz für WM S320 vorhanden?</span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_platzWm320" value="Ja" class="custom-cb focus:ring-brand-green"> Ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_platzWm320" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-bold text-slate-600 uppercase w-48">Müssen Einzelkomp. verwendet werden?</span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_einzelKomponenten" value="Ja" class="custom-cb focus:ring-brand-green"> Ja</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="note_einzelKomponenten" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EINBRINGMASSE & ZUWEGUNG -->
                    <div id="wp-sec-einbringung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="einbringung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-einbringung', 'wp-icon-einbringung')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-ruler text-brand-green"></i> Einbringmaße & Zuwegung</span>
                            <i id="wp-icon-einbringung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-einbringung" class="p-5">
                            
                            <!-- ORIGINAL PDF EINBRINGMASSE -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
                                <h4 class="font-bold text-sm mb-3 flex items-center gap-4">
                                    Einbringmaße Zuwegung Heizraum
                                    <div class="flex gap-2">
                                        <label class="font-normal text-sm cursor-pointer flex items-center gap-1"><input type="radio" name="note_zuwegungHeizraum" value="KG" class="custom-cb focus:ring-brand-green"> KG</label>
                                        <label class="font-normal text-sm cursor-pointer flex items-center gap-1"><input type="radio" name="note_zuwegungHeizraum" value="EG" class="custom-cb focus:ring-brand-green"> EG</label>
                                    </div>
                                </h4>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase">Min. Breite zur Installation:</span>
                                    <input type="number" name="door_width_for_installation" placeholder="Breite (cm)" class="w-1/3 p-2 border rounded-lg focus:ring-brand-green outline-none">
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-3">
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (1)</span>
                                        <div class="flex gap-1"><input type="number" name="note_t1Breite" placeholder="Br." class="w-1/2 p-1.5 border rounded outline-none"><input type="number" name="note_t1Hoehe" placeholder="H." class="w-1/2 p-1.5 border rounded outline-none"></div>
                                    </div>
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (2)</span>
                                        <div class="flex gap-1"><input type="number" name="note_t2Breite" placeholder="Br." class="w-1/2 p-1.5 border rounded outline-none"><input type="number" name="note_t2Hoehe" placeholder="H." class="w-1/2 p-1.5 border rounded outline-none"></div>
                                    </div>
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (3)</span>
                                        <div class="flex gap-1"><input type="number" name="note_t3Breite" placeholder="Br." class="w-1/2 p-1.5 border rounded outline-none"><input type="number" name="note_t3Hoehe" placeholder="H." class="w-1/2 p-1.5 border rounded outline-none"></div>
                                    </div>
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (4)</span>
                                        <div class="flex gap-1"><input type="number" name="note_t4Breite" placeholder="Br." class="w-1/2 p-1.5 border rounded outline-none"><input type="number" name="note_t4Hoehe" placeholder="H." class="w-1/2 p-1.5 border rounded outline-none"></div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 mt-4 border-t border-slate-200 pt-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase">Treppen:</span>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppen" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppen" value="Ja" class="custom-cb focus:ring-brand-green"> Ja / Art:</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppenArt" value="gradeläufig" class="custom-cb focus:ring-brand-green"> gradeläufig</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppenArt" value="L-Form" class="custom-cb focus:ring-brand-green"> L-Form</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppenArt" value="U-Form" class="custom-cb focus:ring-brand-green"> U-Form</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_treppenArt" value="Wendel" class="custom-cb focus:ring-brand-green"> Wendel</label>
                                    <input type="number" name="note_treppenBreite" placeholder="Breite (cm)" class="w-24 p-1.5 border rounded-lg focus:ring-brand-green outline-none ml-2">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex flex-col gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                                    <span class="text-xs font-bold text-slate-500 uppercase">Länge AE zu IE (Weg Länge):</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="number" name="heat_pump_pipe_length" class="w-20 p-1.5 border rounded-lg focus:ring-brand-green outline-none"><span class="text-sm">m</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-sm">Anschluss:</span>
                                        <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_anschlussAussen" value="Wand" class="custom-cb focus:ring-brand-green"> Wand</label>
                                        <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_anschlussAussen" value="Boden" class="custom-cb focus:ring-brand-green"> Boden</label>
                                    </div>
                                </div>
                            </div>

                            <div class="border border-brand-orange/30 bg-brand-orange/5 p-4 rounded-xl">
                                <h4 class="font-bold text-sm mb-3 flex items-center gap-4 text-brand-orange">
                                    Alternative Aufstellmöglichkeit
                                    <div class="flex gap-2">
                                        <label class="font-normal text-sm cursor-pointer flex items-center gap-1 text-slate-700"><input type="radio" name="note_alternativeAufstellung" value="Ja" class="custom-cb focus:ring-brand-green"> vorhanden Ja</label>
                                        <label class="font-normal text-sm cursor-pointer flex items-center gap-1 text-slate-700"><input type="radio" name="note_alternativeAufstellung" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                    </div>
                                </h4>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase">Zuwegung Dachgeschoss:</span>
                                    <input type="number" name="note_altBreite" placeholder="Breite (cm)" class="w-1/3 p-2 border rounded-lg focus:ring-brand-green outline-none bg-white">
                                    <input type="number" name="note_altHoehe" placeholder="Höhe (cm)" class="w-1/3 p-2 border rounded-lg focus:ring-brand-green outline-none bg-white">
                                </div>
                                <div class="flex gap-4 text-sm mb-3">
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (1)</span>
                                        <div class="flex gap-1"><input type="number" name="note_altT1Breite" placeholder="Br." class="w-20 p-1.5 border rounded outline-none bg-white"><input type="number" name="note_altT1Hoehe" placeholder="H." class="w-20 p-1.5 border rounded outline-none bg-white"></div>
                                    </div>
                                    <div><span class="block text-[10px] text-slate-500">Türmaße (2)</span>
                                        <div class="flex gap-1"><input type="number" name="note_altT2Breite" placeholder="Br." class="w-20 p-1.5 border rounded outline-none bg-white"><input type="number" name="note_altT2Hoehe" placeholder="H." class="w-20 p-1.5 border rounded outline-none bg-white"></div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200/50 pt-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase">Treppen:</span>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_altTreppen" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_altTreppen" value="Ja" class="custom-cb focus:ring-brand-green"> Ja / Art:</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_altTreppenArt" value="Wendeltreppe" class="custom-cb focus:ring-brand-green"> Wendeltreppe</label>
                                    <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_altTreppenArt" value="geradeläufige Treppe" class="custom-cb focus:ring-brand-green"> geradeläufige Treppe</label>
                                    <input type="number" name="note_altTreppenBreite" placeholder="Breite (cm)" class="w-24 p-1.5 border rounded-lg focus:ring-brand-green outline-none ml-2 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ELEKTRO -->
                    <div id="wp-sec-elektro" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="elektro">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-elektro', 'wp-icon-elektro')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-plug text-brand-green"></i> Elektroinstallation</span>
                            <i id="wp-icon-elektro" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-elektro" class="p-5">
                            <div class="grid grid-cols-1 gap-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <span class="text-sm font-medium">Interesse an SG Ready Schnittstelle? (EnWG 14a)</span>
                                        <div class="flex gap-3">
                                            <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="enwg_14a_ready" value="1" class="custom-cb focus:ring-brand-green"> ja</label>
                                            <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="enwg_14a_ready" value="0" class="custom-cb focus:ring-brand-green"> nein</label>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium">Internet am Aufstellort (WLAN/LAN)?</span>
                                            <div class="flex gap-3">
                                                <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="network_wlan" value="Ja" class="custom-cb focus:ring-brand-green"> Ja</label>
                                                <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="network_wlan" value="Nein" class="custom-cb focus:ring-brand-green"> Nein</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-2">
                                            <span class="text-sm font-medium w-48">Stromzähler Anzahl (WP Zähler vorh.?):</span>
                                            <input type="number" name="meter_count" class="w-16 p-1 border rounded outline-none focus:ring-brand-green">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SONSTIGE ARBEITEN & BENÖTIGTE ELEMENTE -->
                    <div id="wp-sec-sonstiges" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="sonstiges">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-sonstiges', 'wp-icon-sonstiges')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-wrench text-brand-green"></i> Sonstige Arbeiten & Elemente</span>
                            <i id="wp-icon-sonstiges" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-sonstiges" class="p-5">
                            <!-- Arbeiten -->
                            <div class="space-y-4 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <span class="text-sm font-medium">Fundament & Erdarbeiten durch:</span>
                                        <div class="flex gap-2">
                                            <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="groundwork" value="Solar Aspekt" class="custom-cb focus:ring-brand-green"> Solar Aspekt</label>
                                            <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="groundwork" value="Kunde" class="custom-cb focus:ring-brand-green"> Kunde</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3">Kondenswasser AE</h4>
                                    <div class="flex flex-wrap gap-4 mb-3">
                                        <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_kondenswasser" value="Sickergrube" class="custom-cb focus:ring-brand-green"> Sickergrube</label>
                                        <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_kondenswasser" value="Abflussrohr ins Erdreich" class="custom-cb focus:ring-brand-green"> Abflussrohr ins Erdreich</label>
                                        <label class="cursor-pointer flex items-center gap-1 text-sm"><input type="radio" name="note_kondenswasser" value="Anschluss im Haus" class="custom-cb focus:ring-brand-green"> Anschluss im Haus</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SCHALLBERECHNUNG -->
                    <div id="wp-sec-schall" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="schall">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-schall', 'wp-icon-schall')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-waves text-brand-green"></i> Infos zur Schallberechnung</span>
                            <i id="wp-icon-schall" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-schall" class="p-5">
                            <p class="text-xs text-slate-400 mb-4 uppercase tracking-wide">Speziell für Kunden in Bad Homburg bzw. nach regionalen Vorgaben</p>
                            
                            <div class="space-y-5">
                                <div>
                                    <h4 class="font-bold text-sm mb-2 text-slate-700">Aufstellgebiet:</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="Industriegebiet" class="custom-cb focus:ring-brand-green"> Industriegebiet</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="urbanes Gebiet" class="custom-cb focus:ring-brand-green"> urbanes Gebiet</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="Allg. Wohngebiet" class="custom-cb focus:ring-brand-green"> Allg. Wohngebiet / Kleinsiedlung</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="Gewerbegebiet" class="custom-cb focus:ring-brand-green"> Gewerbegebiet</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="Kern-, Dorf-, Mischgebiet" class="custom-cb focus:ring-brand-green"> Kern-, Dorf-, Mischgebiet</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallGebiet" value="reines Wohngebiet" class="custom-cb focus:ring-brand-green"> reines Wohngebiet</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm md:col-span-3"><input type="radio" name="note_schallGebiet" value="Kurgebiet" class="custom-cb focus:ring-brand-green"> Kurgebiet</label>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold text-sm mb-2 text-slate-700">Aufstellort (Distanz zu Wänden):</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallOrt" value="Freistehend >3m" class="custom-cb focus:ring-brand-green"> Freistehend (&gt;3m von Wand)</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallOrt" value="Wand <3m" class="custom-cb focus:ring-brand-green"> An Wand (&lt;3m)</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallOrt" value="Ecke <3m" class="custom-cb focus:ring-brand-green"> In Ecke (&lt;3m)</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallOrt" value="Wand <5m" class="custom-cb focus:ring-brand-green"> An Wand (&lt;5m)</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallOrt" value="Zwischen Wänden <5m" class="custom-cb focus:ring-brand-green"> Zwischen Wänden (&lt;5m)</label>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-bold text-sm mb-2 text-slate-700">Abschirmung:</h4>
                                    <div class="flex flex-wrap gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallAbschirmung" value="Sichtkontakt" class="custom-cb focus:ring-brand-green"> Sichtkontakt</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallAbschirmung" value="kein Sichtkontakt" class="custom-cb focus:ring-brand-green"> kein Sichtkontakt</label>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="radio" name="note_schallAbschirmung" value="auf abgewandter Seite" class="custom-cb focus:ring-brand-green"> auf abgewandter Seite</label>
                                        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0 md:ml-4">
                                            <span class="text-xs">Maßgeblicher Immissionsort (m):</span>
                                            <input type="number" name="note_schallImmissionOrt" class="w-20 p-1.5 border rounded outline-none focus:ring-brand-green">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ZUSÄTZLICHE NOTIZEN MIT DIKTIERFUNKTION -->
                    <div id="wp-sec-notizen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="notizen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-notizen', 'wp-icon-notizen')">
                            <span class="flex items-center gap-2"><i class="ph-fill ph-notebook text-brand-green"></i> Zusätzliche Notizen</span>
                            <i id="wp-icon-notizen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>
                        <div id="wp-content-notizen" class="p-5">
                            <div class="relative group">
                                <textarea id="wp-notes" name="note" rows="4" class="w-full p-4 pr-14 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green resize-none transition-shadow" placeholder="Tippen oder auf das Mikrofon klicken zum Diktieren..."></textarea>
                                <button type="button" onclick="toggleDictation('wp-notes', 'wp-mic-icon')" class="absolute top-3 right-3 p-2 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-brand-green hover:border-brand-green/30 transition active:scale-95" title="Spracheingabe starten/stoppen">
                                    <i id="wp-mic-icon" class="ph-bold ph-microphone text-2xl"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2"><i class="ph-fill ph-info"></i> Nutze das Mikrofon, um Besonderheiten schnell einzusprechen.</p>
                        </div>
                    </div>

                    <button id="btn-submit-wp" type="submit" class="w-full mt-4 bg-brand-green hover:opacity-90 text-white p-4 rounded-2xl font-bold flex justify-center items-center gap-2 shadow-lg shadow-brand-green/30 transition active:scale-95 text-lg">
                        <i class="ph-bold ph-floppy-disk text-2xl"></i> WP Aufmaß Speichern
                    </button>
                </form>
            </div>
        </section>

    </main>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // --- DATA STATE ---
        const currentUser = "Monteur Max"; // Simulation eines eingeloggten Benutzers

        // Mock JSON für die Materialliste
        const sampleMaterialData = [
            {
                "id":"s1776933342479",
                "title":"1. Hauptpositionen",
                "items":[
                    {
                        "name":"MODUL LONGI HI-MOS10 LR7-54HJD MIT 495 W",
                        "img":"https://placehold.co/150x150/74b2d4/fff?text=Modul",
                        "qty":3,
                        "unit":"Set",
                        "subItems":[
                            { "name":"alpex T-Stück reduziert 32 x 20 x 32", "img":"https://placehold.co/80x80/cde8ea/000?text=T-Stueck", "qty":3, "unit":"cm" },
                            { "name":"alpex Übergang mit IG 20mm x 3/4\"", "img":"https://placehold.co/80x80/cde8ea/000?text=Uebergang", "qty":2, "unit":"cm" },
                            { "name":"Bosch Membran-Ausdehnungsgefäß 50 l MAC50", "img":"https://placehold.co/80x80/cde8ea/000?text=MAG50", "qty":2, "unit":"cm" }
                        ]
                    },
                    {
                        "name":"Set Previums",
                        "img":"https://placehold.co/150x150/74b2d4/fff?text=Set%20Previums",
                        "qty":2,
                        "unit":"Set",
                        "subItems":[
                            { "name":"NIBE Ladepumpe CPD 11-25/75", "img":"https://placehold.co/80x80/cde8ea/000?text=Ladepumpe", "qty":2, "unit":"Stk" },
                            { "name":"COSMO Hochleistungsspeicher HL300", "img":"https://placehold.co/80x80/cde8ea/000?text=HL300", "qty":2, "unit":"Stk" },
                            { "name":"COSMO Pufferspeicher Typ CPS 200", "img":"https://placehold.co/80x80/cde8ea/000?text=CPS200", "qty":2, "unit":"Stk" }
                        ]
                    }
                ]
            }
        ];

        let records = [
            {
                id: '1',
                type: 'PV',
                date: '2026-04-28T10:00:00Z',
                customerName: 'Max Mustermann',
                materials: JSON.parse(JSON.stringify(sampleMaterialData)), 
                images: [],
                history: [
                    { action: 'Aufmaß erstellt', user: 'System Admin', date: '2026-04-28T10:00:00Z' }
                ],
                data: { name: 'Max', lastname: 'Mustermann', street: 'Musterstraße 1', city: 'Berlin', postcode: '12345', kwp_size: '10', module_count: '25', objective: 'Neuanlage', roofs: [
                    { roof_type: 'Satteldach', roof_covering: 'Ziegel', roof_height: '5.5', rafter_reinforcement_needed: '0' }
                ]}
            },
            {
                id: '2',
                type: 'WP',
                date: '2026-04-29T09:30:00Z',
                customerName: 'Erika Schmidt',
                materials: JSON.parse(JSON.stringify(sampleMaterialData)),
                images: [],
                history: [
                    { action: 'Aufmaß erstellt', user: 'System Admin', date: '2026-04-29T09:30:00Z' }
                ],
                data: { name: 'Erika', lastname: 'Schmidt', street: 'Waldweg 5', city: 'München', postcode: '67890', building_type: 'Einfamilienhaus', house_year: '2010', living_space: '150', objective: 'Luft-Wasser Wärmepumpe' }
            }
        ];

        let currentRoofIndex = 0;
        let currentRecordIdForMaterials = null;
        let currentRecordIdForImages = null;
        let currentRecordIdForHistory = null;

        // --- INIT ---
        document.addEventListener('DOMContentLoaded', () => {
            renderList();
            initProgressListeners();
        });

        // --- HISTORY LOGIC ---
        function addHistory(recordId, action) {
            const r = records.find(x => x.id === recordId);
            if (r) {
                r.history = r.history || [];
                r.history.unshift({ action: action, user: currentUser, date: new Date().toISOString() });
            }
        }

        function openHistory(id) {
            currentRecordIdForHistory = id;
            const record = records.find(r => r.id === id);
            if(!record) return;

            const fullName = `${record.data.firma || ''} ${record.data.name || ''} ${record.data.lastname || ''}`.trim();
            document.getElementById('history-project-name').innerText = `Projekt: ${fullName || 'Unbenannt'}`;
            
            const listContainer = document.getElementById('history-list-container');
            listContainer.innerHTML = '';

            if(!record.history || record.history.length === 0) {
                listContainer.innerHTML = `<p class="text-center text-slate-500 py-10">Keine Historie vorhanden.</p>`;
            } else {
                let html = '<div class="relative border-l-2 border-brand-blue/30 ml-3 space-y-6 my-4">';
                record.history.forEach(h => {
                    const dateStr = new Date(h.date).toLocaleString('de-DE');
                    html += `
                        <div class="relative pl-6">
                            <div class="absolute -left-[9px] top-1 w-4 h-4 bg-brand-blue rounded-full border-2 border-white shadow-sm"></div>
                            <p class="text-sm font-bold text-slate-800">${h.action}</p>
                            <p class="text-xs text-slate-500 mt-0.5"><i class="ph-fill ph-user"></i> ${h.user} &nbsp;&bull;&nbsp; <i class="ph-fill ph-clock"></i> ${dateStr}</p>
                        </div>
                    `;
                });
                html += '</div>';
                listContainer.innerHTML = html;
            }

            const modal = document.getElementById('modal-history');
            const content = document.getElementById('modal-history-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeHistory() {
            const modal = document.getElementById('modal-history');
            const content = document.getElementById('modal-history-content');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // --- SPEECH TO TEXT (DICTATION) ---
        let recognition = null;
        let isRecording = false;
        let activeInputId = null;

        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = false;
            recognition.lang = 'de-DE';

            recognition.onresult = function(event) {
                let finalTranscript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript + ' ';
                    }
                }
                if (activeInputId && finalTranscript) {
                    const input = document.getElementById(activeInputId);
                    input.value += (input.value ? ' ' : '') + finalTranscript;
                    input.dispatchEvent(new Event('input')); // trigger forms progress logic
                }
            };

            recognition.onend = function() {
                resetMicIcons();
                isRecording = false;
                activeInputId = null;
            };

            recognition.onerror = function(event) {
                console.error("Spracherkennung Fehler:", event.error);
                resetMicIcons();
                isRecording = false;
                activeInputId = null;
            };
        }

        function toggleDictation(inputId, iconId) {
            if (!recognition) {
                alert("Spracheingabe wird in diesem Browser/Gerät leider nicht unterstützt.");
                return;
            }

            if (isRecording) {
                recognition.stop();
                return;
            }

            activeInputId = inputId;
            try {
                recognition.start();
                isRecording = true;
                
                resetMicIcons();
                const icon = document.getElementById(iconId);
                if (icon) {
                    icon.classList.remove('ph-microphone', 'text-slate-400');
                    icon.classList.add('ph-stop-circle', 'text-red-500', 'animate-pulse');
                }
            } catch(e) {
                console.error("Konnte Spracherkennung nicht starten:", e);
            }
        }

        function resetMicIcons() {
            ['pv-mic-icon', 'wp-mic-icon'].forEach(id => {
                const icon = document.getElementById(id);
                if (icon) {
                    icon.classList.remove('ph-stop-circle', 'text-red-500', 'animate-pulse');
                    icon.classList.add('ph-microphone', 'text-slate-400');
                }
            });
        }

        // --- SIDEBAR TOGGLE ---
        function toggleSidebar(type) {
            const sidebar = document.getElementById(`${type}-sidebar`);
            const title = document.getElementById(`${type}-sidebar-title`);
            const icon = document.getElementById(`${type}-sidebar-icon`);
            const texts = sidebar.querySelectorAll(`div[id^="${type}-navtext-"]`);

            if(sidebar.classList.contains('w-64')) {
                // Collapse
                sidebar.classList.replace('w-64', 'w-20');
                title.classList.add('opacity-0', 'hidden');
                icon.classList.replace('ph-caret-left', 'ph-caret-right');
                texts.forEach(t => t.classList.add('opacity-0', 'hidden'));
            } else {
                // Expand
                sidebar.classList.replace('w-20', 'w-64');
                title.classList.remove('opacity-0', 'hidden');
                icon.classList.replace('ph-caret-right', 'ph-caret-left');
                texts.forEach(t => t.classList.remove('opacity-0', 'hidden'));
            }
        }

        // --- UI LOGIC (COLLAPSE & SCROLL) ---
        function toggleSection(contentId, iconId) {
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            if(content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.add('rotate-180');
            }
        }

        function scrollToSection(id) {
            const el = document.getElementById(id);
            if(el) {
                const contentId = id.replace('-sec-', '-content-');
                const iconId = id.replace('-sec-', '-icon-');
                const content = document.getElementById(contentId);
                const icon = document.getElementById(iconId);
                if(content && content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.classList.remove('rotate-180');
                }
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // --- PROGRESS ---
        function initProgressListeners() {
            ['form-pv', 'form-wp'].forEach(formId => {
                const form = document.getElementById(formId);
                if(form) {
                    form.addEventListener('input', () => updateFormProgress(formId === 'form-pv' ? 'PV' : 'WP'));
                    form.addEventListener('change', () => updateFormProgress(formId === 'form-pv' ? 'PV' : 'WP'));
                }
            });
        }

        function updateFormProgress(type) {
            const form = document.getElementById(`form-${type.toLowerCase()}`);
            const sections = form.querySelectorAll('[data-section]');
            let totalReqFields = 0;
            let filledReqFields = 0;

            sections.forEach(sec => {
                const secId = sec.getAttribute('data-section');
                const reqElements = Array.from(sec.querySelectorAll('input[required], select[required]'));
                
                const reqGroups = {};
                reqElements.forEach(el => {
                    if (!reqGroups[el.name]) reqGroups[el.name] = [];
                    reqGroups[el.name].push(el);
                });

                let secTotal = Object.keys(reqGroups).length;
                let secFilled = 0;

                for (const name in reqGroups) {
                    const elements = reqGroups[name];
                    let isFilled = false;
                    elements.forEach(el => {
                        if (el.type === 'radio' || el.type === 'checkbox') {
                            if (el.checked) isFilled = true;
                        } else {
                            if (el.value.trim() !== '') isFilled = true;
                        }
                    });
                    if (isFilled) secFilled++;
                }

                totalReqFields += secTotal;
                filledReqFields += secFilled;

                const navDot = document.getElementById(`${type.toLowerCase()}-nav-${secId}`);
                const navCount = document.getElementById(`${type.toLowerCase()}-navcount-${secId}`);
                
                if(navDot) {
                    if (secTotal === 0) {
                         navDot.className = `w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors`;
                         navDot.innerHTML = '';
                    } else if (secFilled === secTotal) {
                         const color = type === 'PV' ? 'bg-brand-orange' : 'bg-brand-green';
                         navDot.className = `w-6 h-6 rounded-full ${color} text-white border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors shadow-sm`;
                         navDot.innerHTML = '<i class="ph-bold ph-check text-xs"></i>';
                    } else {
                         navDot.className = `w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors`;
                         navDot.innerHTML = '';
                    }
                }

                if(navCount) {
                    if(secTotal > 0) {
                        navCount.textContent = `${secFilled}/${secTotal}`;
                        navCount.classList.remove('hidden');
                        if (secFilled === secTotal) {
                            const bgClass = type === 'PV' ? 'bg-brand-orange/10' : 'bg-brand-green/10';
                            const textClass = type === 'PV' ? 'text-brand-orange' : 'text-brand-green';
                            const borderClass = type === 'PV' ? 'border-brand-orange/30' : 'border-brand-green/30';
                            navCount.className = `text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors ${bgClass} ${textClass} ${borderClass}`;
                        } else {
                            navCount.className = `text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors bg-slate-100 text-slate-500 border-slate-200`;
                        }
                    } else {
                        navCount.textContent = 'Opt';
                        navCount.className = `text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors bg-slate-50 text-slate-400 border-slate-100`;
                    }
                }
            });

            const percentage = totalReqFields === 0 ? 100 : Math.round((filledReqFields / totalReqFields) * 100);
            const fillEl = document.getElementById(`${type.toLowerCase()}-progress-fill`);
            const textEl = document.getElementById(`${type.toLowerCase()}-progress-text`);
            if(fillEl) fillEl.style.width = `${percentage}%`;
            if(textEl) textEl.innerText = `${percentage}%`;
        }

        // --- DYNAMIC ROOF LOGIC ---
        function addRoofUI(roofData = null) {
            const container = document.getElementById('roofs-container');
            const idx = currentRoofIndex++;
            const html = `
                <div class="roof-entry border border-brand-lightBlue/50 rounded-2xl p-4 bg-slate-50 relative" data-index="${idx}">
                    <button type="button" onclick="this.closest('.roof-entry').remove(); updateFormProgress('PV');" class="absolute top-4 right-4 text-red-500 hover:bg-red-100 p-2 rounded-lg transition" title="Dach löschen">
                        <i class="ph-bold ph-trash text-lg"></i>
                    </button>
                    <h4 class="font-bold text-brand-blue mb-4 text-lg border-b border-brand-lightBlue/30 pb-2 pr-10">Dachfläche ${idx + 1}</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dachform <span class="text-red-500">*</span></label>
                            <select name="roof_${idx}_roof_type" required class="w-full p-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                <option value="">Bitte wählen...</option><option value="Satteldach">Satteldach</option><option value="Walmdach">Walmdach</option><option value="Flachdach">Flachdach</option><option value="Pultdach">Pultdach</option><option value="Carport">Carport</option><option value="mehrere">mehrere Dachflächen</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Höhe Traufe (m) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.1" name="roof_${idx}_roof_height" required class="w-full p-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                        </div>
                    </div>

                    <div class="mt-4 bg-white p-4 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Dacheindeckung</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 mb-3">
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Ziegel" class="custom-cb focus:ring-brand-orange"> Ziegel</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Schiefer" class="custom-cb focus:ring-brand-orange"> Schiefer</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Biberschwanz" class="custom-cb focus:ring-brand-orange"> Biberschwanz</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Trapezblech" class="custom-cb focus:ring-brand-orange"> Trapezblech</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Stehfalz" class="custom-cb focus:ring-brand-orange"> Stehfalz</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Welleternit" class="custom-cb focus:ring-brand-orange"> Welleternit</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Beton" class="custom-cb focus:ring-brand-orange"> Beton</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Ton" class="custom-cb focus:ring-brand-orange"> Ton</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Bitumen" class="custom-cb focus:ring-brand-orange"> Bitumen</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Folie" class="custom-cb focus:ring-brand-orange"> Folie</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Kies" class="custom-cb focus:ring-brand-orange"> Kies</label>
                            <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_roof_covering" value="Gründach" class="custom-cb focus:ring-brand-orange"> Gründach</label>
                        </div>
                        <label class="text-sm flex items-center gap-1 cursor-pointer border-t pt-2 mt-2"><input type="checkbox" name="roof_${idx}_solar_holding_tile_desired" value="1" class="custom-cb focus:ring-brand-orange"> Solarhalteziegel geplant</label>
                        
                        <div class="mt-4 border border-brand-orange/30 p-3 rounded-lg bg-brand-orange/5">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-2">Details Ziegel / Pfanne</h5>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-3">
                                <label class="text-xs flex flex-col items-center gap-1 cursor-pointer"><i class="ph ph-waves text-2xl text-slate-400"></i><div class="flex items-center gap-1"><input type="radio" name="roof_${idx}_roof_covering_model" value="2 Wellen" class="custom-cb focus:ring-brand-orange"> 2 Wellen</div></label>
                                <label class="text-xs flex flex-col items-center gap-1 cursor-pointer"><i class="ph ph-wave-sine text-2xl text-slate-400"></i><div class="flex items-center gap-1"><input type="radio" name="roof_${idx}_roof_covering_model" value="1 Welle" class="custom-cb focus:ring-brand-orange"> 1 Welle</div></label>
                                <label class="text-xs flex flex-col items-center gap-1 cursor-pointer"><i class="ph ph-rectangle text-2xl text-slate-400"></i><div class="flex items-center gap-1"><input type="radio" name="roof_${idx}_roof_covering_model" value="Flachziegel" class="custom-cb focus:ring-brand-orange"> Flach</div></label>
                                <label class="text-xs flex flex-col items-center gap-1 cursor-pointer"><i class="ph ph-parallelogram text-2xl text-slate-400"></i><div class="flex items-center gap-1"><input type="radio" name="roof_${idx}_roof_covering_model" value="Schiefer" class="custom-cb focus:ring-brand-orange"> Schiefer</div></label>
                                <label class="text-xs flex flex-col items-center gap-1 cursor-pointer"><i class="ph ph-shield text-2xl text-slate-400"></i><div class="flex items-center gap-1"><input type="radio" name="roof_${idx}_roof_covering_model" value="Biberschwanz" class="custom-cb focus:ring-brand-orange"> Biber</div></label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="flex gap-2 items-center w-full">
                                    <span class="text-xs font-bold">Eindeckmaß (B/H):</span>
                                    <input type="text" name="roof_${idx}_roof_covering_dimensions_cm" placeholder="B x H in cm" class="w-full p-1.5 border rounded text-xs outline-none">
                                </div>
                                <input type="text" name="roof_${idx}_roof_covering_company" placeholder="Bezeichnung / Hersteller" class="w-full p-1.5 border rounded text-xs outline-none bg-white">
                                <div class="flex items-center gap-2 text-xs flex-wrap md:col-span-2 mt-1">
                                    <span class="font-bold">Farbe:</span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFarbe" value="schwarz" class="custom-cb focus:ring-brand-orange"> schwarz</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFarbe" value="anthrazit" class="custom-cb focus:ring-brand-orange"> anthrazit</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFarbe" value="hellgrau" class="custom-cb focus:ring-brand-orange"> hellgrau</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFarbe" value="rot" class="custom-cb focus:ring-brand-orange"> rot</label>
                                    <span class="mx-1 border-l h-4"></span>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFinish" value="glasiert" class="custom-cb focus:ring-brand-orange"> glasiert</label>
                                    <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_ziegelFinish" value="matt" class="custom-cb focus:ring-brand-orange"> matt</label>
                                </div>
                                <div class="flex items-center gap-2 mt-1 md:col-span-2">
                                    <span class="text-xs font-bold">Anzahl vorrätiger Ziegel:</span>
                                    <input type="number" name="roof_${idx}_note_ziegelVorrat" class="w-20 p-1 border rounded text-xs outline-none bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 border border-slate-200 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-2">Sparren & Dämmung</h5>
                            <div class="flex items-center gap-3 mb-2">
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="checkbox" name="roof_${idx}_rafter_reinforcement_needed" value="1" class="custom-cb focus:ring-brand-orange"> Verstärkung nötig</label>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs">Sparrenstärke:</span><input type="number" step="0.1" name="roof_${idx}_rafter_thickness" class="w-20 p-1 border rounded text-xs outline-none bg-slate-50">
                            </div>
                            <div class="flex gap-3 mb-3 border-b pb-3">
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="checkbox" name="roof_${idx}_note_denkmalschutz" value="1" class="custom-cb focus:ring-brand-orange"> Denkmalschutz</label>
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="checkbox" name="roof_${idx}_note_natursparren" value="1" class="custom-cb focus:ring-brand-orange"> "Natur" Sparren</label>
                            </div>
                            <span class="text-xs font-bold">Dämmung:</span>
                            <div class="flex flex-col gap-1 mt-1">
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_between_rafter_insulation" value="Zwischensparren" class="custom-cb focus:ring-brand-orange"> Zwischensparrendämmung</label>
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_between_rafter_insulation" value="Aufdach" class="custom-cb focus:ring-brand-orange"> Aufdachdämmung</label>
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_between_rafter_insulation" value="Beides" class="custom-cb focus:ring-brand-orange"> beides</label>
                            </div>
                        </div>

                        <div class="bg-white p-4 border border-slate-200 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-2">Verlegung & Flachdach</h5>
                            <span class="text-xs font-bold">Verlegung Solarkabel bis WR:</span>
                            <div class="flex flex-col gap-1 mt-1 mb-3">
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_dc_cable_route" value="Kabelkanal Fassade" class="custom-cb focus:ring-brand-orange"> Kabelkanal/Fallrohr Fassade</label>
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_dc_cable_route" value="Leerrohr" class="custom-cb focus:ring-brand-orange"> vorhandenes Leerrohr</label>
                                <label class="text-sm flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_dc_cable_route" value="Kamin" class="custom-cb focus:ring-brand-orange"> durch Kamin</label>
                            </div>
                            <div class="flex items-center gap-3 mb-3 border-b pb-3 text-sm">
                                <span>mit Kunden abgestimmt/Freigabe?</span>
                                <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_kabelAbgestimmt" value="Ja" class="custom-cb focus:ring-brand-orange"> Ja</label>
                                <label class="flex items-center gap-1 cursor-pointer"><input type="radio" name="roof_${idx}_note_kabelAbgestimmt" value="Nein" class="custom-cb focus:ring-brand-orange"> Nein</label>
                            </div>
                            <span class="text-xs font-bold text-slate-500 uppercase">Nur bei Flachdach:</span>
                            <div class="flex gap-2 items-center mt-1">
                                <span class="text-xs">Attika Höhe:</span><input type="number" name="roof_${idx}_note_attikaHoehe" class="w-16 p-1 border rounded text-xs outline-none bg-slate-50">
                                <span class="text-xs ml-2">Breite:</span><input type="number" name="roof_${idx}_note_attikaBreite" class="w-16 p-1 border rounded text-xs outline-none bg-slate-50">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-white p-4 border border-slate-200 rounded-xl">
                        <h5 class="text-xs font-bold text-slate-500 uppercase mb-3">Dachaufbauten</h5>
                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                <span class="text-sm font-bold w-32">SAT-Schüssel:</span>
                                <select name="roof_${idx}_note_satAktion" class="p-1.5 border rounded text-sm outline-none bg-slate-50 w-full sm:w-auto"><option value="">Wählen...</option><option value="bleibt">bleibt</option><option value="versetzen">versetzen</option><option value="Demontage">Demontage</option></select>
                                <input type="text" name="roof_${idx}_note_satOrt" placeholder="neuer Montageort" class="p-1.5 border rounded text-sm outline-none flex-1">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                <span class="text-sm font-bold w-32">Antenne:</span>
                                <select name="roof_${idx}_note_antenneAktion" class="p-1.5 border rounded text-sm outline-none bg-slate-50 w-full sm:w-auto"><option value="">Wählen...</option><option value="bleibt">bleibt</option><option value="versetzen">versetzen</option><option value="Demontage">Demontage</option></select>
                                <input type="text" name="roof_${idx}_note_antenneOrt" placeholder="neuer Montageort" class="p-1.5 border rounded text-sm outline-none flex-1">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                <div class="flex items-center gap-1 w-full sm:w-32"><span class="text-sm font-bold">Trittstufen:</span><input type="number" name="roof_${idx}_note_trittstufenAnzahl" placeholder="Anz." class="w-10 p-1 text-xs border rounded ml-auto sm:ml-0"></div>
                                <select name="roof_${idx}_note_trittstufenAktion" class="p-1.5 border rounded text-sm outline-none bg-slate-50 w-full sm:w-auto"><option value="">Wählen...</option><option value="bleibt">bleibt</option><option value="versetzen">versetzen</option><option value="Demontage">Demontage</option></select>
                                <input type="text" name="roof_${idx}_note_trittstufenOrt" placeholder="neuer Montageort" class="p-1.5 border rounded text-sm outline-none flex-1">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                <div class="flex items-center gap-1 w-full sm:w-32"><span class="text-sm font-bold">Sanitärlüfter:</span><input type="number" name="roof_${idx}_note_luefterAnzahl" placeholder="Anz." class="w-10 p-1 text-xs border rounded ml-auto sm:ml-0"></div>
                                <select name="roof_${idx}_note_luefterAktion" class="p-1.5 border rounded text-sm outline-none bg-slate-50 w-full sm:w-auto"><option value="">Wählen...</option><option value="bleibt">bleibt</option><option value="versetzen">versetzen</option><option value="kürzen">kürzen</option><option value="neuer Lüftungsziegel">neuer Lüftungsziegel einbauen</option></select>
                                <input type="text" name="roof_${idx}_note_luefterNeuAnzahl" placeholder="Anzahl neue Ziegel" class="p-1.5 border rounded text-sm outline-none flex-1">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 justify-between">
                                <div class="flex items-center gap-1 w-full sm:w-32"><span class="text-sm font-bold">Solarthermie:</span><input type="number" name="roof_${idx}_note_thermieAnzahl" placeholder="Anz." class="w-10 p-1 text-xs border rounded ml-auto sm:ml-0"></div>
                                <select name="roof_${idx}_note_thermieAktion" class="p-1.5 border rounded text-sm outline-none bg-slate-50 w-full sm:w-auto"><option value="">Wählen...</option><option value="bleibt">bleibt</option><option value="versetzen">versetzen</option><option value="Demontage">Demontage</option></select>
                                <input type="text" name="roof_${idx}_note_thermieOrt" placeholder="neuer Montageort" class="p-1.5 border rounded text-sm outline-none flex-1">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 items-center">
                                <span class="text-sm font-bold w-full sm:w-32">Schneefanggitter:</span>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-1 text-sm"><input type="radio" name="roof_${idx}_note_schneeAktion" value="bleibt" class="custom-cb"> bleibt</label>
                                    <label class="flex items-center gap-1 text-sm"><input type="radio" name="roof_${idx}_note_schneeAktion" value="Demontage" class="custom-cb"> Demontage</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-x-6 gap-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold">Äußerer Blitzschutz vorh.:</span>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_blitzschutz" value="Ja" class="custom-cb focus:ring-brand-orange"> ja</label>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_blitzschutz" value="Nein" class="custom-cb focus:ring-brand-orange"> nein</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold">Internet per Satellit vorh.:</span>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_satInternet" value="Ja" class="custom-cb focus:ring-brand-orange"> ja</label>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_satInternet" value="Nein" class="custom-cb focus:ring-brand-orange"> nein</label>
                            </div>
                            <div class="flex items-center gap-2 w-full">
                                <span class="text-sm font-bold">Kabelführung über Dach sorgt für Verschattung:</span>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_kabelVerschattung" value="Ja" class="custom-cb focus:ring-brand-orange"> ja</label>
                                <label class="flex items-center gap-1 text-sm cursor-pointer"><input type="radio" name="roof_${idx}_note_kabelVerschattung" value="Nein" class="custom-cb focus:ring-brand-orange"> nein</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            if (roofData) {
                const form = document.getElementById('form-pv');
                for (const key in roofData) {
                    const elName = `roof_${idx}_${key}`;
                    if (form.elements[elName]) {
                        if(form.elements[elName].type === 'checkbox' || form.elements[elName].type === 'radio' || (form.elements[elName].length && form.elements[elName][0].type === 'radio')) {
                            if(form.elements[elName].length) { 
                                Array.from(form.elements[elName]).forEach(radio => {
                                    if(radio.value == roofData[key] || (radio.value === '1' && roofData[key] === true)) radio.checked = true;
                                });
                            } else {
                                form.elements[elName].checked = (roofData[key] == '1' || roofData[key] === true);
                            }
                        } else {
                            form.elements[elName].value = roofData[key];
                        }
                    }
                }
            }
            updateFormProgress('PV');
        }

        function clearRoofs() {
            document.getElementById('roofs-container').innerHTML = '';
            currentRoofIndex = 0;
            updateFormProgress('PV');
        }

        // --- MODALS (SELECTION, IMAGES, MATERIALS) ---
        function showTypeSelection() {
            const modal = document.getElementById('modal-select-type');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeTypeSelection() {
            const modal = document.getElementById('modal-select-type');
            const content = document.getElementById('modal-content');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // === FOTOS / IMAGES LOGIC ===
        function openImages(id) {
            currentRecordIdForImages = id;
            const record = records.find(r => r.id === id);
            if(!record) return;

            if(!record.images) record.images = [];
            
            const fullName = `${record.data.firma || ''} ${record.data.name || ''} ${record.data.lastname || ''}`.trim();
            document.getElementById('images-project-name').innerText = `Projekt: ${fullName || 'Unbenannt'}`;
            renderImages();

            const modal = document.getElementById('modal-images');
            const content = document.getElementById('modal-images-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeImages() {
            const modal = document.getElementById('modal-images');
            const content = document.getElementById('modal-images-content');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function handleImageUpload(event) {
            const files = event.target.files;
            if(!files || !currentRecordIdForImages) return;
            const record = records.find(r => r.id === currentRecordIdForImages);
            
            Array.from(files).forEach(file => {
                const url = URL.createObjectURL(file);
                // Erfasse aktuellen Benutzer und Zeitpunkt
                record.images.push({ 
                    url, 
                    name: file.name, 
                    uploadedBy: currentUser, 
                    uploadedAt: new Date().toISOString() 
                });
            });
            
            addHistory(currentRecordIdForImages, `${files.length} Foto(s) hochgeladen`);
            renderImages();
        }

        function deleteImage(imgIndex) {
            const record = records.find(r => r.id === currentRecordIdForImages);
            if(confirm("Foto wirklich löschen?")) {
                record.images.splice(imgIndex, 1);
                addHistory(currentRecordIdForImages, `Ein Foto wurde gelöscht`);
                renderImages();
            }
        }

        function renderImages() {
            const record = records.find(r => r.id === currentRecordIdForImages);
            const grid = document.getElementById('image-grid');
            grid.innerHTML = '';
            
            if(record.images.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-10 text-slate-400">Noch keine Fotos vorhanden.</div>`;
                return;
            }

            record.images.forEach((img, idx) => {
                grid.innerHTML += `
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200 shadow-sm aspect-square bg-slate-100 flex flex-col">
                        <img src="${img.url}" class="w-full h-full object-cover flex-1">
                        
                        <!-- Bild Info Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/90 to-transparent p-3 pt-8 pointer-events-none">
                            <p class="text-white text-xs font-bold truncate"><i class="ph-fill ph-user"></i> ${img.uploadedBy || 'Unbekannt'}</p>
                            <p class="text-white/80 text-[10px]"><i class="ph-fill ph-clock"></i> ${new Date(img.uploadedAt).toLocaleString('de-DE')}</p>
                        </div>

                        <button onclick="deleteImage(${idx})" class="absolute top-2 right-2 bg-white/90 hover:bg-red-50 text-red-500 p-2 rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition z-10">
                            <i class="ph-bold ph-trash"></i>
                        </button>
                    </div>
                `;
            });
        }

        // === MATERIAL EDITOR LOGIC ===
        function openMaterials(id) {
            currentRecordIdForMaterials = id;
            const record = records.find(r => r.id === id);
            if(!record) return;

            const fullName = `${record.data.firma || ''} ${record.data.name || ''} ${record.data.lastname || ''}`.trim();
            document.getElementById('material-project-name').innerText = `Projekt: ${fullName || 'Unbenannt'}`;
            renderMaterials();

            const modal = document.getElementById('modal-materials');
            const content = document.getElementById('modal-materials-content');
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeMaterials() {
            const modal = document.getElementById('modal-materials');
            const content = document.getElementById('modal-materials-content');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function updateMatQty(catIdx, itemIdx, subIdx, value) {
            const record = records.find(r => r.id === currentRecordIdForMaterials);
            const num = parseFloat(value) || 0;
            let itemName = '';
            
            if (subIdx !== null) {
                record.materials[catIdx].items[itemIdx].subItems[subIdx].qty = num;
                itemName = record.materials[catIdx].items[itemIdx].subItems[subIdx].name;
            } else {
                record.materials[catIdx].items[itemIdx].qty = num;
                itemName = record.materials[catIdx].items[itemIdx].name;
            }
            
            addHistory(currentRecordIdForMaterials, `Menge geändert: ${itemName} auf ${num}`);
        }

        function deleteMat(catIdx, itemIdx, subIdx) {
            const record = records.find(r => r.id === currentRecordIdForMaterials);
            let itemName = '';
            if (subIdx !== null) {
                itemName = record.materials[catIdx].items[itemIdx].subItems[subIdx].name;
                record.materials[catIdx].items[itemIdx].subItems.splice(subIdx, 1);
            } else {
                itemName = record.materials[catIdx].items[itemIdx].name;
                record.materials[catIdx].items.splice(itemIdx, 1);
            }
            addHistory(currentRecordIdForMaterials, `Material gelöscht/abgewählt: ${itemName}`);
            renderMaterials(); 
        }

        function addCustomMaterial(catIdx) {
            const record = records.find(r => r.id === currentRecordIdForMaterials);
            const name = prompt("Name des neuen Artikels:");
            if(!name) return;
            const qty = prompt("Menge:", "1") || "1";
            const unit = prompt("Einheit (z.B. Stk, m, Set):", "Stk") || "Stk";
            
            record.materials[catIdx].items.push({
                name: name,
                img: 'https://placehold.co/150x150/74b2d4/fff?text=Neu',
                qty: parseFloat(qty),
                unit: unit,
                subItems: []
            });
            addHistory(currentRecordIdForMaterials, `Neues Material hinzugefügt: ${name}`);
            renderMaterials();
        }

        function addCustomSubMaterial(catIdx, itemIdx) {
            const record = records.find(r => r.id === currentRecordIdForMaterials);
            const name = prompt("Name des neuen Unterartikels:");
            if(!name) return;
            const qty = prompt("Menge:", "1") || "1";
            const unit = prompt("Einheit (z.B. Stk, m):", "Stk") || "Stk";
            
            if(!record.materials[catIdx].items[itemIdx].subItems) {
                record.materials[catIdx].items[itemIdx].subItems = [];
            }

            record.materials[catIdx].items[itemIdx].subItems.push({
                name: name,
                img: 'https://placehold.co/80x80/cde8ea/000?text=Neu',
                qty: parseFloat(qty),
                unit: unit
            });
            addHistory(currentRecordIdForMaterials, `Neues Unter-Material hinzugefügt: ${name}`);
            renderMaterials();
        }

        function renderMaterials() {
            const record = records.find(r => r.id === currentRecordIdForMaterials);
            const listContainer = document.getElementById('materials-list-container');
            listContainer.innerHTML = '';

            if(!record.materials || record.materials.length === 0) {
                listContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="ph ph-package-x text-6xl mb-4 text-slate-300"></i>
                        <p class="font-medium">Keine Materialien geplant.</p>
                    </div>`;
                return;
            }

            let html = '';
            record.materials.forEach((category, cIdx) => {
                html += `
                    <div class="mb-8">
                        <div class="flex justify-between items-center border-b-2 border-brand-lightBlue pb-2 mb-4">
                            <h3 class="text-lg font-bold text-brand-blue">${category.title}</h3>
                            <button onclick="addCustomMaterial(${cIdx})" class="text-brand-blue bg-brand-lightBlue/30 hover:bg-brand-lightBlue p-1.5 rounded-lg text-sm font-bold flex items-center gap-1 transition">
                                <i class="ph-bold ph-plus"></i> Artikel
                            </button>
                        </div>
                        <div class="space-y-4 main-item-list" id="mat-cat-${cIdx}">
                `;
                
                if(category.items && category.items.length > 0) {
                    category.items.forEach((item, iIdx) => {
                        html += `
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden hover:border-brand-lightBlue transition">
                                <div class="flex flex-col sm:flex-row gap-4 p-4">
                                    <div class="shrink-0 flex items-center sm:items-start gap-3">
                                        <i class="ph-bold ph-dots-six-vertical text-2xl text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-main mt-6 hidden sm:block"></i>
                                        <div class="flex sm:flex-col justify-between items-center sm:items-start gap-4">
                                            <img src="${item.img}" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-slate-100 shadow-sm" onerror="this.src='https://placehold.co/150x150?text=No+Image'">
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start gap-2 mb-2">
                                            <div class="flex items-center gap-2">
                                                <i class="ph-bold ph-dots-six-vertical text-xl text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-main sm:hidden"></i>
                                                <h4 class="font-bold text-slate-800 text-base md:text-lg leading-tight">${item.name}</h4>
                                            </div>
                                            <button onclick="deleteMat(${cIdx}, ${iIdx}, null)" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition shrink-0" title="Löschen / Markieren">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Editable QTY -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <label class="text-xs font-bold text-slate-500 uppercase">Menge:</label>
                                            <input type="number" min="0" step="0.1" value="${item.qty}" class="w-20 p-1.5 text-center font-bold text-brand-blue bg-brand-lightBlue/20 border border-brand-lightBlue rounded-lg outline-none focus:ring-2 focus:ring-brand-blue" onchange="updateMatQty(${cIdx}, ${iIdx}, null, this.value)">
                                            <span class="text-sm font-bold text-brand-green">${item.unit}</span>
                                        </div>
                                        
                                        <!-- Sub Items -->
                                        <div class="mt-4 pt-3 border-t border-slate-100">
                                            <div class="flex justify-between items-center mb-2">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Beinhaltet:</p>
                                                <button onclick="addCustomSubMaterial(${cIdx}, ${iIdx})" class="text-brand-green bg-brand-lightGreen/30 hover:bg-brand-lightGreen/50 px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 transition">
                                                    <i class="ph-bold ph-plus"></i> Hinzufügen
                                                </button>
                                            </div>
                                            <div class="space-y-2 sub-item-list" id="mat-sub-${cIdx}-${iIdx}">
                                                ${(item.subItems && item.subItems.length > 0) ? item.subItems.map((sub, sIdx) => `
                                                    <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                                        <i class="ph-bold ph-dots-six-vertical text-lg text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-sub shrink-0"></i>
                                                        <img src="${sub.img}" class="w-8 h-8 object-cover rounded-md border border-slate-200 shrink-0" onerror="this.src='https://placehold.co/80x80?text=Img'">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-semibold text-slate-700 truncate" title="${sub.name}">${sub.name}</p>
                                                        </div>
                                                        <div class="flex items-center gap-1 shrink-0">
                                                            <input type="number" min="0" step="0.1" value="${sub.qty}" class="w-16 p-1 text-center text-sm border rounded outline-none focus:border-brand-blue" onchange="updateMatQty(${cIdx}, ${iIdx}, ${sIdx}, this.value)">
                                                            <span class="text-xs text-slate-500 w-6">${sub.unit}</span>
                                                            <button onclick="deleteMat(${cIdx}, ${iIdx}, ${sIdx})" class="text-red-400 hover:text-red-600 p-1 rounded transition"><i class="ph-bold ph-trash"></i></button>
                                                        </div>
                                                    </div>
                                                `).join('') : '<p class="text-xs text-slate-400 italic">Keine Unterartikel</p>'}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<p class="text-slate-500 text-sm italic">Keine Artikel in dieser Kategorie.</p>`;
                }
                html += `</div></div>`;
            });
            listContainer.innerHTML = html;

            // Initialize Sortable JS for Drag and Drop
            record.materials.forEach((category, cIdx) => {
                const catEl = document.getElementById(`mat-cat-${cIdx}`);
                if(catEl) {
                    new Sortable(catEl, {
                        handle: '.drag-handle-main',
                        animation: 150,
                        onEnd: function(evt) {
                            const movedItem = category.items.splice(evt.oldIndex, 1)[0];
                            category.items.splice(evt.newIndex, 0, movedItem);
                            addHistory(currentRecordIdForMaterials, `Sortierung Hauptartikel geändert`);
                        }
                    });
                }

                if(category.items) {
                    category.items.forEach((item, iIdx) => {
                        const subEl = document.getElementById(`mat-sub-${cIdx}-${iIdx}`);
                        if(subEl) {
                            new Sortable(subEl, {
                                handle: '.drag-handle-sub',
                                animation: 150,
                                onEnd: function(evt) {
                                    if(!item.subItems) item.subItems = [];
                                    const movedSub = item.subItems.splice(evt.oldIndex, 1)[0];
                                    item.subItems.splice(evt.newIndex, 0, movedSub);
                                    addHistory(currentRecordIdForMaterials, `Sortierung Unterartikel geändert`);
                                }
                            });
                        }
                    });
                }
            });
        }

        // --- NAVIGATION & CRUD ---
        function navigate(viewId) {
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            document.getElementById('view-' + viewId).classList.add('active');
            if(viewId === 'list') renderList();
            document.querySelector('main').scrollTo(0,0);
        }

        function createNew(type) {
            closeTypeSelection();
            let formId = type === 'PV' ? 'form-pv' : 'form-wp';
            document.getElementById(formId).reset();
            document.getElementById(type.toLowerCase() + '-id').value = ''; 
            if(type === 'PV') {
                clearRoofs();
                addRoofUI();
            }
            
            document.getElementById(formId).querySelectorAll('.hidden').forEach(el => {
                if(el.id.includes('content')) {
                    el.classList.remove('hidden');
                    const iconId = el.id.replace('-content-', '-icon-');
                    const icon = document.getElementById(iconId);
                    if(icon) icon.classList.remove('rotate-180');
                }
            });

            updateFormProgress(type);
            navigate('form-' + type.toLowerCase());
        }

        function editRecord(id) {
            const record = records.find(r => r.id === id);
            if(!record) return;

            let formId = record.type === 'PV' ? 'form-pv' : 'form-wp';
            let form = document.getElementById(formId);
            form.reset(); 
            
            document.getElementById(record.type.toLowerCase() + '-id').value = record.id;
            
            if(record.type === 'PV') clearRoofs();

            for (const key in record.data) {
                if (key === 'roofs' && record.type === 'PV') {
                    record.data.roofs.forEach(roofData => addRoofUI(roofData));
                } else if (form.elements[key]) {
                    if (form.elements[key].type === 'checkbox' || form.elements[key].type === 'radio' || (form.elements[key].length && form.elements[key][0].type === 'radio')) {
                        if(form.elements[key].length) { 
                            Array.from(form.elements[key]).forEach(radio => {
                                if(radio.value == record.data[key] || (radio.value === '1' && record.data[key] === true)) radio.checked = true;
                            });
                        } else {
                            form.elements[key].checked = (record.data[key] == '1' || record.data[key] === true);
                        }
                    } else {
                        form.elements[key].value = record.data[key];
                    }
                }
            }
            
            updateFormProgress(record.type);
            navigate('form-' + record.type.toLowerCase());
        }

        function saveRecord(event, type) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            if(type === 'PV' && currentRoofIndex === 0) {
                alert("Bitte lege mindestens eine Dachfläche an.");
                return;
            }

            const id = formData.get('id');
            const customerNameFallback = formData.get('name') + ' ' + formData.get('lastname');
            
            let dataObj = {};
            if(type === 'PV') dataObj.roofs = [];

            for (let [key, value] of formData.entries()) {
                if(key !== 'id') {
                    if (type === 'PV' && key.startsWith('roof_')) {
                        const parts = key.split('_');
                        const idx = parseInt(parts[1]);
                        const field = parts.slice(2).join('_');
                        if (!dataObj.roofs[idx]) dataObj.roofs[idx] = {};
                        dataObj.roofs[idx][field] = value;
                    } else {
                        dataObj[key] = value;
                    }
                }
            }
            
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (type === 'PV' && cb.name.startsWith('roof_')) {
                    const parts = cb.name.split('_');
                    const idx = parseInt(parts[1]);
                    const field = parts.slice(2).join('_');
                    if (!dataObj.roofs[idx]) dataObj.roofs[idx] = {};
                    dataObj.roofs[idx][field] = cb.checked ? 1 : 0;
                } else {
                    dataObj[cb.name] = cb.checked ? 1 : 0;
                }
            });

            if(type === 'PV') {
                dataObj.roofs = dataObj.roofs.filter(r => r !== undefined);
            }

            if (id) {
                const index = records.findIndex(r => r.id === id);
                if(index > -1) {
                    records[index].customerName = customerNameFallback;
                    records[index].data = dataObj;
                    addHistory(id, "Formular (Aufmaß) aktualisiert");
                }
            } else {
                const newId = Date.now().toString();
                records.unshift({
                    id: newId,
                    type: type,
                    date: new Date().toISOString(),
                    customerName: customerNameFallback,
                    images: [],
                    materials: JSON.parse(JSON.stringify(sampleMaterialData)), 
                    history: [{ action: "Aufmaß neu erstellt", user: currentUser, date: new Date().toISOString() }],
                    data: dataObj
                });
            }
            navigate('list');
        }

        function deleteRecord(id) {
            if(confirm('Möchten Sie dieses Aufmaß wirklich löschen?')) {
                records = records.filter(r => r.id !== id);
                renderList();
            }
        }

        // --- RENDER LIST ---
        function renderList() {
            const listContainer = document.getElementById('full-list-container');
            listContainer.innerHTML = '';
            
            // Search Filter Logic
            const searchTerm = (document.getElementById('search-input')?.value || '').toLowerCase();
            let filteredRecords = records;

            if (searchTerm) {
                filteredRecords = records.filter(r => {
                    const searchStr = `${r.customerName} ${r.data.city || ''} ${r.data.street || ''} ${r.data.firma || ''} ${r.type}`.toLowerCase();
                    return searchStr.includes(searchTerm);
                });
            }

            if(filteredRecords.length === 0) {
                listContainer.innerHTML = `
                    <div class="col-span-full text-center py-16 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                        <i class="ph ph-clipboard-text text-slate-300 text-5xl mb-3"></i>
                        <p class="text-slate-500 font-medium">Keine Aufmaße gefunden.</p>
                        ${searchTerm ? '' : `<button onclick="showTypeSelection()" class="mt-4 text-brand-blue text-sm font-semibold hover:underline">Neues Aufmaß anlegen</button>`}
                    </div>
                `;
                return;
            }

            filteredRecords.forEach(r => {
                const isPV = r.type === 'PV';
                const dateStr = new Date(r.date).toLocaleDateString('de-DE');
                
                const icon = isPV ? 'ph-solar-panel text-brand-orange' : 'ph-thermometer text-brand-green';
                const bg = isPV ? 'bg-brand-orange/20' : 'bg-brand-lightGreen/40';
                const tagBg = isPV ? 'bg-brand-orange text-white' : 'bg-brand-green text-white';
                const imgCount = r.images ? r.images.length : 0;
                const fullName = `${r.data.firma || ''} ${r.data.name || ''} ${r.data.lastname || ''}`.trim();
                
                let detailsHtml = '';
                if(r.data.street) detailsHtml += `<span class="col-span-2 truncate flex gap-2 items-center"><i class="ph ph-map-pin"></i> ${r.data.street}, ${r.data.city}</span>`;
                if(isPV && r.data.kwp_size) detailsHtml += `<span><strong>${r.data.kwp_size}</strong> kWp</span>`;
                if(isPV && r.data.roofs) detailsHtml += `<span><strong>${r.data.roofs.length}</strong> Dachfläche(n)</span>`;
                if(!isPV && r.data.building_type) detailsHtml += `<span>${r.data.building_type}</span>`;
                if(!isPV && r.data.objective) detailsHtml += `<span>${r.data.objective}</span>`;

                listContainer.innerHTML += `
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-4 hover:shadow-md transition relative">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-xl ${bg}"><i class="ph-fill ${icon} text-2xl"></i></div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">${fullName || 'Unbenannt'}</h3>
                                    <span class="text-xs text-slate-500 flex items-center gap-1"><i class="ph ph-calendar-blank"></i> ${dateStr}</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-md shadow-sm ${tagBg}">${r.type}</span>
                        </div>
                        
                        <div class="text-sm text-slate-600 grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            ${detailsHtml}
                        </div>
                        
                        <div class="flex flex-wrap justify-end gap-2 pt-2 border-t border-slate-100">
                            <button onclick="deleteRecord('${r.id}')" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Löschen">
                                <i class="ph-bold ph-trash text-lg"></i>
                            </button>
                            
                            <button onclick="openHistory('${r.id}')" class="px-3 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-clock-counter-clockwise text-lg text-slate-500"></i> Historie
                            </button>

                            <button onclick="openImages('${r.id}')" class="px-3 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-camera text-lg text-brand-blue"></i> Fotos
                                ${imgCount > 0 ? `<span class="bg-brand-blue text-white text-[10px] px-1.5 py-0.5 rounded-full">${imgCount}</span>` : ''}
                            </button>

                            <button onclick="openMaterials('${r.id}')" class="px-3 py-2 bg-brand-lightBlue/30 text-brand-blue text-sm font-semibold rounded-xl hover:bg-brand-lightBlue/50 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-package text-lg"></i> Material
                            </button>
                            
                            <button onclick="editRecord('${r.id}')" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-pencil-simple text-lg"></i> Bearbeiten
                            </button>
                        </div>
                    </div>
                `;
            });
        }
    </script>
</body>
</html>