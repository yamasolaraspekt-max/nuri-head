<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solar Aspekt - Feinaufmaß & Material</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        .custom-cb {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.25rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            flex-shrink: 0;
        }
        
        /* Sticky Header Offset for scrolling */
        .scroll-mt-offset { scroll-margin-top: 180px; }

        /* Smooth sidebar transition */
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        /* App-style quick menu header + sider */
        .sa-app-header{
            background:linear-gradient(135deg,#ffffff 0%,#f8fafc 58%,#eef7d6 100%);
            border:1px solid #e2e8f0;
            box-shadow:0 20px 55px rgba(15,23,42,.08);
        }
        .sa-header-logo{
            width:48px;height:48px;border-radius:16px;
            display:flex;align-items:center;justify-content:center;
            background:linear-gradient(135deg,#74b2d4,#93c11c);
            color:#fff;box-shadow:0 12px 28px rgba(116,178,212,.28);
            flex:0 0 auto;
        }
        .sa-header-kicker{font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:#64748b;}
        .sa-quick-btn{
            display:inline-flex;align-items:center;justify-content:center;gap:10px;
            min-height:46px;padding:0 16px;border-radius:16px;
            border:1px solid #dbe3ef;background:#fff;color:#0f172a;
            box-shadow:0 8px 20px rgba(15,23,42,.07);
            font-weight:900;transition:all .18s ease;
        }
        .sa-quick-btn:hover{border-color:#74b2d4;color:#256f91;transform:translateY(-1px);box-shadow:0 14px 30px rgba(15,23,42,.10);}
        .sa-quick-btn.primary{background:#93c11c;color:#fff;border-color:#93c11c;box-shadow:0 12px 28px rgba(147,193,28,.25);}
        .sa-quick-btn.primary:hover{background:#7baa18;color:#fff;border-color:#7baa18;}
        .sa-quick-overlay{position:fixed;inset:0;background:rgba(15,23,42,.58);backdrop-filter:blur(4px);z-index:80;display:none;opacity:0;transition:opacity .22s ease;}
        .sa-quick-overlay.show{display:block;opacity:1;}
        .sa-quick-sider{
            position:fixed;top:0;right:0;height:100%;width:min(420px,100vw);
            background:#2c3e50;color:#fff;z-index:90;transform:translateX(100%);
            display:flex;flex-direction:column;box-shadow:-18px 0 55px rgba(15,23,42,.35);
            transition:transform .28s cubic-bezier(.16,1,.3,1);
        }
        .sa-quick-sider.show{transform:translateX(0);}
        .sa-quick-head{padding:20px;border-bottom:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:12px;}
        .sa-quick-title{font-size:18px;font-weight:950;letter-spacing:-.02em;}
        .sa-quick-sub{font-size:12px;color:rgba(255,255,255,.62);margin-top:3px;}
        .sa-quick-close{width:40px;height:40px;border-radius:999px;background:rgba(255,255,255,.10);display:flex;align-items:center;justify-content:center;transition:.18s;}
        .sa-quick-close:hover{background:rgba(255,255,255,.18);}
        .sa-quick-grid{padding:18px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;overflow-y:auto;}
        .sa-quick-tile{
            min-height:94px;padding:13px 9px;border-radius:16px;background:#314b62;
            border:1px solid rgba(255,255,255,.10);color:#fff;text-align:center;
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
            font-size:11px;font-weight:900;line-height:1.25;transition:all .18s ease;
        }
        .sa-quick-tile:hover{transform:translateY(-3px);background:#38566f;color:#cfe09b;box-shadow:0 12px 26px rgba(0,0,0,.24);}
        .sa-quick-tile i{font-size:24px;}
        .sa-quick-section{padding:0 18px 8px;color:rgba(255,255,255,.58);font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;}
        @media(max-width:700px){.sa-quick-grid{grid-template-columns:repeat(2,minmax(0,1fr));}.sa-app-header{padding:18px!important;}.sa-header-actions{width:100%;}.sa-quick-btn{flex:1;}}

        .sa-team-strip{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .sa-team-avatar{width:30px;height:30px;border-radius:999px;object-fit:cover;border:2px solid #fff;box-shadow:0 3px 10px rgba(15,23,42,.16);background:#f1f5f9;}
        .sa-team-chip{display:inline-flex;align-items:center;gap:7px;padding:5px 9px;border-radius:999px;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-size:11px;font-weight:800;}
        .sa-team-chip-role{font-size:9px;text-transform:uppercase;color:#64748b;font-weight:900;}
        .sa-note-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:14px;box-shadow:0 1px 2px rgba(15,23,42,.04);}
        .sa-note-card + .sa-note-card{margin-top:10px;}
        .sa-note-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px;}
        .sa-note-author{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:900;color:#0f172a;}
        .sa-note-author img{width:28px;height:28px;border-radius:999px;object-fit:cover;border:1px solid #e2e8f0;}
        .sa-note-date{font-size:11px;color:#64748b;font-weight:700;}
        .sa-note-body{white-space:pre-wrap;color:#334155;font-size:13px;line-height:1.6;}
    </style>
    <style>
        .select2-container {
                width: 100% !important;
            }

            .select2-container--default .select2-selection--single {
                height: 46px !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 0.75rem !important;
                background: #f8fafc !important;
                display: flex !important;
                align-items: center !important;
                padding: 0 10px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #334155 !important;
                font-weight: 600 !important;
                padding-left: 0 !important;
                line-height: 44px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 44px !important;
                right: 10px !important;
            }

            .sa-radio-original-hidden {
                display: none !important;
            }

            .sa-select2-field {
                width: 100%;
            }

            .sa-select2-label {
                display: block;
                font-size: 11px;
                font-weight: 800;
                color: #475569;
                text-transform: uppercase;
                margin-bottom: 6px;
                letter-spacing: .03em;
            }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">

    <!-- MAIN CONTENT AREA -->
    <main class="h-full overflow-y-auto relative w-full scroll-smooth">
        <div id="saQuickOverlay" class="sa-quick-overlay" onclick="toggleSaQuickMenu(false)"></div>
        <aside id="saQuickSider" class="sa-quick-sider" aria-hidden="true">
            <div class="sa-quick-head">
                <div>
                    <div class="sa-quick-title">Quick Menu</div>
                    <div class="sa-quick-sub">Schnellnavigation wie im App-Layout</div>
                </div>
                <button type="button" class="sa-quick-close" onclick="toggleSaQuickMenu(false)" aria-label="Quick Menu schließen">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <div class="sa-quick-grid">
                <a href="{{ url('/') }}" class="sa-quick-tile"><i class="ph-bold ph-house"></i><span>Dashboard</span></a>
                <a href="{{ url('deal_details') }}" class="sa-quick-tile"><i class="ph-bold ph-briefcase"></i><span>Aufträge</span></a>
                <a href="{{ url('new_leads') }}" class="sa-quick-tile"><i class="ph-bold ph-users-three"></i><span>Kunden</span></a>
                <a href="{{ url('tasks/calendar/personal') }}" class="sa-quick-tile"><i class="ph-bold ph-calendar-blank"></i><span>Kalender</span></a>
                <a href="{{ url('customer/appointments') }}" class="sa-quick-tile"><i class="ph-bold ph-calendar-check"></i><span>Termine</span></a>
                <a href="{{ url('lead/kanban') }}" class="sa-quick-tile"><i class="ph-bold ph-kanban"></i><span>Lead Kanban</span></a>
                <a href="{{ url('admin/todo/personal?tab=my') }}" class="sa-quick-tile"><i class="ph-bold ph-check-square"></i><span>Meine Aufgaben</span></a>
                <a href="{{ Route::has('general-tasks.index') ? route('general-tasks.index') : url('general-tasks') }}" class="sa-quick-tile"><i class="ph-bold ph-list-checks"></i><span>Team Aufgaben</span></a>
                <a href="{{ url('/all-contacts') }}" class="sa-quick-tile"><i class="ph-bold ph-address-book"></i><span>Kontakte</span></a>
            </div>

            <div class="sa-quick-section">Werkzeuge</div>
            <div class="sa-quick-grid" style="padding-top:0;">
                <a href="{{ Route::has('deal.measurements.index') ? route('deal.measurements.index') : url('deal-measurements') }}" class="sa-quick-tile"><i class="ph-bold ph-ruler"></i><span>Feinaufmaß</span></a>
                <a href="{{ Route::has('employee.capacity.view') ? route('employee.capacity.view') : '#' }}" class="sa-quick-tile"><i class="ph-bold ph-gauge"></i><span>Kapazität</span></a>
                <a href="{{ Route::has('lead.reference') ? route('lead.reference') : '#' }}" class="sa-quick-tile"><i class="ph-bold ph-map-trifold"></i><span>Karte</span></a>
                <a href="{{ Route::has('ai.chats') ? route('ai.chats') : url('ai/chats') }}" class="sa-quick-tile"><i class="ph-bold ph-robot"></i><span>KI Chat</span></a>
                <a href="{{ Route::has('breaking-news.index') ? route('breaking-news.index') : url('breaking-news') }}" class="sa-quick-tile"><i class="ph-bold ph-newspaper"></i><span>News</span></a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form')?.submit();" class="sa-quick-tile"><i class="ph-bold ph-sign-out"></i><span>Logout</span></a>
            </div>
        </aside>
        
        <!-- ==================== VIEW: LIST (DEFAULT) ==================== -->
        <section id="view-list" class="view-section active p-4 md:p-8 max-w-5xl mx-auto pb-24">
            <header class="sa-app-header flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 p-6 rounded-3xl gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="sa-header-logo"><i class="ph-fill ph-ruler text-2xl"></i></div>
                    <div class="min-w-0">
                        <div class="sa-header-kicker">SA-DESK · Feinaufmaß & Material</div>
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            Solar Aspekt
                        </h1>
                        <p class="text-slate-500 text-sm md:text-base mt-1">Alle Aufmaße im Überblick · Team, Material, Fotos, Notizen und Historie</p>
                    </div>
                </div>

                <div class="sa-header-actions flex flex-wrap items-center gap-2 justify-end">
                    <a href="{{ url('/') }}" class="sa-quick-btn" title="Dashboard">
                        <i class="ph-bold ph-house text-lg"></i><span>Dashboard</span>
                    </a>
                    <a href="{{ url('deal_details') }}" class="sa-quick-btn" title="Aufträge">
                        <i class="ph-bold ph-arrow-left text-lg"></i><span>Aufträge</span>
                    </a>
                    <button type="button" onclick="toggleSaQuickMenu()" class="sa-quick-btn primary" title="Quick Menu öffnen">
                        <i class="ph-bold ph-squares-four text-lg"></i><span>Quick Menu</span>
                    </button>
                </div>
            </header>

            <!-- SEARCH BAR -->
            <div class="mb-6 relative">
                <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-xl"></i>
                <input type="text" id="search-input" oninput="currentPage = 1; renderList()" placeholder="Suchen nach Name, Ort, Straße, Firma..." class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-brand-blue outline-none transition text-slate-700 font-medium">            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="full-list-container">
                <!-- Wird durch JS befüllt -->
            </div>

            <div id="pagination-container" class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
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

        <!-- ==================== MODAL: NOTES ==================== -->
        <div id="modal-notes" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[75] flex items-center justify-center p-4 md:p-8 opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-3xl w-full max-w-2xl h-[82vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300" id="modal-notes-content">
                <div class="p-5 md:p-6 rounded-t-3xl border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="ph-fill ph-note-pencil text-brand-green"></i> Notizen
                        </h2>
                        <p class="text-sm text-slate-500 mt-1" id="notes-project-name">Lade...</p>
                    </div>
                    <button onclick="closeNotes()" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>

                <div class="p-4 md:p-6 border-b border-slate-100 bg-slate-50 shrink-0">
                    <form id="note-create-form" onsubmit="saveMeasurementNote(event)" class="space-y-3">
                        <textarea id="note-create-text" rows="4" class="w-full p-4 bg-white border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-brand-green resize-none text-sm" placeholder="Neue interne Notiz zu diesem Feinaufmaß schreiben..."></textarea>
                        <div class="flex justify-end">
                            <button type="submit" id="note-create-btn" class="bg-brand-green hover:opacity-90 text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition shadow-sm">
                                <i class="ph-bold ph-floppy-disk text-lg"></i> Notiz speichern
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4 md:p-6 overflow-y-auto flex-1 bg-slate-50" id="notes-list-container">
                    <!-- Notes injected here -->
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
                        <div class="p-2 rounded-lg bg-brand-orange text-white">
                            <i class="ph-fill ph-solar-panel text-xl"></i>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800">PV Feinaufmaß</h2>
                    </div>

                    <button type="button" onclick="document.getElementById('btn-submit-pv').click()" class="bg-brand-orange hover:opacity-90 text-white px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition shadow-md">
                        <i class="ph-bold ph-floppy-disk text-lg"></i>
                        <span class="hidden md:inline">Speichern</span>
                    </button>
                </div>

                <div class="w-full bg-slate-200 h-2 mt-2 rounded-full overflow-hidden">
                    <div id="pv-progress-fill" class="bg-brand-orange h-full w-0 transition-all duration-500 ease-out"></div>
                </div>

                <div class="flex justify-between text-xs text-slate-500 mt-1 font-bold">
                    <span class="flex items-center gap-1">
                        <i class="ph-bold ph-info"></i> Formular Fortschritt
                    </span>
                    <span id="pv-progress-text">0%</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mt-6 items-start relative">

                <!-- COLLAPSIBLE SIDEBAR -->
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
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">PV & Komponenten</span>
                                <span id="pv-navcount-anlage" class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 transition-colors">0/0</span>
                            </div>
                        </li>

                        <li class="relative flex items-center gap-3 cursor-pointer group w-full" onclick="scrollToSection('pv-sec-daecher')">
                            <div id="pv-nav-daecher" class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors"></div>
                            <div id="pv-navtext-daecher" class="flex-1 flex justify-between items-center transition-all duration-300 opacity-0 hidden">
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Dachflächen</span>
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
                                <span class="text-sm text-slate-500 group-hover:text-slate-800 whitespace-nowrap">Energie & Elektrik</span>
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
                <form id="form-pv"
                    onsubmit="saveRecord(event, 'PV')"
                    class="flex-1 space-y-4 w-full min-w-0"
                    data-section="pv_object">

                    @csrf

                    <input type="hidden" name="id" id="pv-id" value="{{ $alternative->id ?? '' }}">

                    <div class="mb-2 text-sm text-slate-500 flex items-center gap-2">
                        <span class="text-red-500 font-bold">*</span> markiert Pflichtfelder
                    </div>

                    <!-- KUNDENDATEN -->
                    <div id="pv-sec-kunden" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="kunden">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-kunden', 'pv-icon-kunden')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-user text-brand-orange"></i> Angaben des Kunden
                            </span>
                            <i id="pv-icon-kunden" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="pv-content-kunden" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Firma</label>
                                    <input type="text" name="firma" value="{{ old('firma', $alternative->firma ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                                        Vorname <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name', $alternative->name ?? '') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                                        Nachname <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="lastname" value="{{ old('lastname', $alternative->lastname ?? '') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                                        Straße & Nr. <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="street" value="{{ old('street', $alternative->street ?? '') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                                        PLZ <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="postcode" value="{{ old('postcode', $alternative->postcode ?? '') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
                                        Ort <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="city" value="{{ old('city', $alternative->city ?? '') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Telefon</label>
                                    <input type="text" name="telephone" value="{{ old('telephone', $alternative->telephone ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Mobil</label>
                                    <input type="text" name="phone" value="{{ old('phone', $alternative->phone ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">E-Mail</label>
                                    <input type="email" name="email" value="{{ old('email', $alternative->email ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 mt-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Feinaufmaß durch</label>
                                        <input type="text" name="contact_person" value="{{ old('contact_person', $alternative->contact_person ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">Datum</label>
                                        <input type="date" name="request_date" value="{{ old('request_date', $alternative->request_date ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PV & KOMPONENTEN -->
                    <div id="pv-sec-anlage" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="anlage">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-anlage', 'pv-icon-anlage')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-lightning text-brand-orange"></i> PV & Komponenten
                            </span>
                            <i id="pv-icon-anlage" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="pv-content-anlage" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Projektart <span class="text-red-500">*</span>
                                    </label>
                                    <select name="object_remark" required class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="PV Neuanlage" @selected(old('object_remark', $alternative->object_remark ?? '') == 'PV Neuanlage')>Neuanlage</option>
                                        <option value="PV Erweiterung" @selected(old('object_remark', $alternative->object_remark ?? '') == 'PV Erweiterung')>Erweiterung</option>
                                        <option value="PV Demontage alt" @selected(old('object_remark', $alternative->object_remark ?? '') == 'PV Demontage alt')>Demontage alt</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bei Demontage</label>
                                    <select name="note_demontageVerbleib" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Kunde">Module beim Kunden lassen</option>
                                        <option value="Lager">Mitnehmen zu uns ins Lager</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kabelführung ausreichend?</label>
                                    <select name="note_kabelAusreichend" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-4 border-t border-slate-100 pt-4">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">
                                    Zusatz-Komponenten <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Batteriespeicher -->
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition">
                                        <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer">
                                            <input type="checkbox" name="storage_preference" value="Ja" class="custom-cb focus:ring-brand-orange">
                                            Batteriespeicher
                                        </label>

                                        <div class="grid grid-cols-1 gap-2">
                                            <input type="text" name="note_battery_type" placeholder="Hersteller/Typ" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                            <input type="text" name="note_battery_size" placeholder="geplante Größe" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                            <input type="text" name="note_battery_location" placeholder="Aufstellort" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                <input type="number" name="note_batteryDistWrZs" placeholder="WR -> ZS (m)" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                                <input type="number" name="note_batteryDistBaWr" placeholder="BA -> WR (m)" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wärmepumpe Integration -->
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition">
                                        <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer">
                                            <input type="checkbox" name="note_wp_integration" value="Ja" class="custom-cb focus:ring-brand-orange">
                                            Wärmepumpe PV-Integration
                                        </label>

                                        <div class="grid grid-cols-1 gap-2">
                                            <input type="text" name="note_wp_type" placeholder="Hersteller/Typ" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">

                                            <select name="note_wpStatus" class="select2-field w-full p-2 border rounded-lg text-sm outline-none focus:ring-brand-orange">
                                                <option value="">Status wählen...</option>
                                                <option value="vorhanden">Vorhanden</option>
                                                <option value="geplant">Geplant</option>
                                            </select>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                                <label class="flex items-center gap-2 text-sm cursor-pointer bg-white border rounded-lg p-2">
                                                    <input type="checkbox" name="note_wp_heizstab" value="Ja" class="custom-cb focus:ring-brand-orange">
                                                    Heizstab
                                                </label>

                                                <label class="flex items-center gap-2 text-sm cursor-pointer bg-white border rounded-lg p-2">
                                                    <input type="checkbox" name="enwg_14a_ready" value="1" class="custom-cb focus:ring-brand-orange">
                                                    SG Ready
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wallbox -->
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-brand-lightBlue transition md:col-span-2">
                                        <label class="flex items-center gap-2 font-bold mb-3 cursor-pointer">
                                            <input type="checkbox" name="wallbox_desired" value="1" class="custom-cb focus:ring-brand-orange">
                                            Wallbox gewünscht
                                        </label>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <input type="text" name="wallbox_location" value="{{ old('wallbox_location', $alternative->wallbox_location ?? '') }}" placeholder="Aufstellort" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">

                                                <div class="flex items-center gap-2">
                                                    <input type="number" name="note_wallbox_distance" placeholder="Entfernung zum ZS" class="flex-1 p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">
                                                    <span class="text-sm">m</span>
                                                </div>

                                                <label class="flex items-center gap-2 text-sm cursor-pointer bg-white border rounded-lg p-2">
                                                    <input type="checkbox" name="note_wallboxKernbohrung" value="Ja" class="custom-cb focus:ring-brand-orange">
                                                    Kernbohrung Außenwand / WU-Beton
                                                </label>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Erdarbeiten nötig?</label>
                                                <select name="note_wbErdarbeiten" class="select2-field w-full p-2 border rounded-lg text-sm outline-none focus:ring-brand-orange">
                                                    <option value="">Bitte wählen...</option>
                                                    <option value="Ja">Ja</option>
                                                    <option value="Nein">Nein</option>
                                                </select>

                                                <input type="text" name="note_wbErdarbeitenLaenge" placeholder="Länge in m" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-orange outline-none">

                                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Erdarbeiten durch</label>
                                                <select name="groundwork" class="select2-field w-full p-2 border rounded-lg text-sm outline-none focus:ring-brand-orange">
                                                    <option value="">Bitte wählen...</option>
                                                    <option value="Solar Aspekt" @selected(old('groundwork', $alternative->groundwork ?? '') == 'Solar Aspekt')>Durch uns / Gala Bauer</option>
                                                    <option value="Kunde" @selected(old('groundwork', $alternative->groundwork ?? '') == 'Kunde')>Kunde / Gala-Bauer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Sonstige Kundenwünsche</label>
                                        <textarea name="energy_remark" rows="2" class="w-full p-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-orange outline-none resize-none">{{ old('energy_remark', $alternative->energy_remark ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DÄCHER -->
                    <div id="pv-sec-daecher" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="daecher">
                        <div class="flex justify-between items-center p-5 border-b border-slate-100 rounded-t-2xl hover:bg-slate-50 transition cursor-pointer" onclick="toggleSection('pv-content-daecher', 'pv-icon-daecher')">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <i class="ph-fill ph-house text-brand-orange"></i> Dachflächen
                            </h3>

                            <div class="flex items-center gap-4">
                                <button type="button" onclick="event.stopPropagation(); addRoofUI()" class="text-brand-orange bg-brand-orange/10 hover:bg-brand-orange/20 px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-1 transition">
                                    <i class="ph-bold ph-plus"></i> Dach hinzufügen
                                </button>
                                <i id="pv-icon-daecher" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                            </div>
                        </div>

                        <div id="pv-content-daecher" class="p-5">
                            <p class="text-xs text-slate-500 mb-4">
                                Mindestens ein Dach muss angelegt werden <span class="text-red-500">*</span>
                            </p>

                            <div id="roofs-container" class="space-y-6">
                                @if(isset($roofs) && count($roofs))
                                    @foreach($roofs as $index => $roof)
                                        @include('admin.new_leads.layouts.partials.roof-fields', [
            'index' => $index,
            'roof' => $roof
        ])
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ABSICHERUNG -->
                    <div id="pv-sec-absicherung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="absicherung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-absicherung', 'pv-icon-absicherung')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-shield-check text-brand-orange"></i> Absicherung
                            </span>
                            <i id="pv-icon-absicherung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="pv-content-absicherung" class="p-5 space-y-4">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fangschutzgitter</label>
                                        <select name="note_fangschutz" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                            <option value="">Bitte wählen...</option>
                                            <option value="möglich">Möglich</option>
                                            <option value="teilweise">Teilweise</option>
                                            <option value="nicht möglich">Nicht möglich</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung</label>
                                        <input type="text" name="note_fangschutz_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h4 class="font-bold text-sm mb-3 text-slate-700">Gerüst</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer">
                                        <input type="checkbox" name="scaffold_usage" value="1" class="custom-cb focus:ring-brand-orange">
                                        <span class="text-sm font-medium">Muss gestellt werden</span>
                                    </label>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Machbarkeit</label>
                                        <select name="note_geruestMachbar" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                            <option value="">Bitte wählen...</option>
                                            <option value="möglich">Möglich</option>
                                            <option value="teilweise">Teilweise</option>
                                            <option value="nicht möglich">Nicht möglich</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung</label>
                                        <input type="text" name="note_scaffold_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung Machbarkeit</label>
                                        <input type="text" name="note_geruestMachbar_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h4 class="font-bold text-sm mb-3 text-slate-700">Aufzug</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer">
                                        <input type="checkbox" name="note_aufzugMuss" value="1" class="custom-cb focus:ring-brand-orange">
                                        <span class="text-sm font-medium">Muss gestellt werden</span>
                                    </label>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Machbarkeit</label>
                                        <select name="note_aufzugMachbar" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                            <option value="">Bitte wählen...</option>
                                            <option value="möglich">Möglich</option>
                                            <option value="teilweise">Teilweise</option>
                                            <option value="nicht möglich">Nicht möglich</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung</label>
                                        <input type="text" name="note_aufzug_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung Machbarkeit</label>
                                        <input type="text" name="note_aufzugMachbar_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h4 class="font-bold text-sm mb-3 text-slate-700">Kran</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer">
                                        <input type="checkbox" name="note_kranMuss" value="1" class="custom-cb focus:ring-brand-orange">
                                        <span class="text-sm font-medium">Muss gestellt werden</span>
                                    </label>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Machbarkeit</label>
                                        <select name="note_kranMachbar" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                            <option value="">Bitte wählen...</option>
                                            <option value="möglich">Möglich</option>
                                            <option value="teilweise">Teilweise</option>
                                            <option value="nicht möglich">Nicht möglich</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung</label>
                                        <input type="text" name="note_kran_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Begründung Machbarkeit</label>
                                        <input type="text" name="note_kranMachbar_reason" placeholder="Begründung..." class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ENERGIE & ELEKTRIK -->
                    <div id="pv-sec-elektro" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="elektro">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-elektro', 'pv-icon-elektro')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-box-arrow-down text-brand-orange"></i> Energie & Elektrik
                            </span>
                            <i id="pv-icon-elektro" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="pv-content-elektro" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center justify-between p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-lightBlue transition">
                                    <span class="font-medium text-sm">AC-Überspannungsschutz vorhanden</span>
                                    <input type="checkbox" name="ac_surge_protection" value="1" class="custom-cb focus:ring-brand-orange">
                                </label>

                                <label class="flex items-center justify-between p-3 border rounded-xl bg-slate-50 cursor-pointer hover:border-brand-lightBlue transition">
                                    <span class="font-medium text-sm">SLS Schalter vorhanden</span>
                                    <input type="checkbox" name="sls_switch" value="1" class="custom-cb focus:ring-brand-orange">
                                </label>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Anzahl WE</label>
                                    <input type="number" name="number_we" value="{{ old('number_we', $alternative->number_we ?? '') }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Mieterstrommodell</label>
                                    <select name="tenant_model" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="zentral" @selected(old('tenant_model', $alternative->tenant_model ?? '') == 'zentral')>Zentral</option>
                                        <option value="individuell" @selected(old('tenant_model', $alternative->tenant_model ?? '') == 'individuell')>Individuell</option>
                                        <option value="nicht-vorhanden" @selected(old('tenant_model', $alternative->tenant_model ?? '') == 'nicht-vorhanden')>Nicht vorhanden</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Zählerschrank</label>
                                    <input type="text" name="meter_cabinet" value="{{ old('meter_cabinet', $alternative->meter_cabinet ?? '') }}" placeholder="Zustand / Ort / Hinweis" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Zählerschrank Aktion</label>
                                    <select name="meter_cabinet_action" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="neuer Zählerschrank notwendig">Neuer Zählerschrank notwendig</option>
                                        <option value="alter Zählerschrank wird zur Unterverteilung">Alter Zählerschrank wird zur Unterverteilung</option>
                                        <option value="zusätzliche Unterverteilung">Zusätzliche Unterverteilung</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Neuer ZS Größe</label>
                                    <select name="cabinet_size" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="550">550</option>
                                        <option value="800">800</option>
                                        <option value="1100">1100</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Aufstellungsort Technik</label>
                                    <select name="installation_location_power" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="KG" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'KG')>Keller (KG)</option>
                                        <option value="EG" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'EG')>Erdgeschoss (EG)</option>
                                        <option value="OG" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'OG')>Obergeschoss (OG)</option>
                                        <option value="DG" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'DG')>Dachgeschoss (DG)</option>
                                        <option value="garage" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'garage')>Garage</option>
                                        <option value="sonstiges" @selected(old('installation_location_power', $alternative->installation_location_power ?? '') == 'sonstiges')>Sonstiges</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3 text-slate-700">Zwischenzähler</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Gewünscht?</label>
                                            <select name="note_zwischenzaehler" class="select2-field w-full p-2 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-brand-orange">
                                                <option value="">Bitte wählen...</option>
                                                <option value="Ja">Ja</option>
                                                <option value="Nein">Nein</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Anzahl Zähler</label>
                                            <input type="number" name="meter_count" value="{{ old('meter_count', $alternative->meter_count ?? '') }}" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-brand-orange">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Für Wärmepumpe?</label>
                                            <select name="note_zwischenzaehlerWp" class="select2-field w-full p-2 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-brand-orange">
                                                <option value="">Bitte wählen...</option>
                                                <option value="Ja">Ja</option>
                                                <option value="Nein">Nein</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">WP Anzahl</label>
                                            <input type="number" name="note_zwischenzaehlerWpCount" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-brand-orange">
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Netzwerk / WLAN</label>
                                    <select name="network_wlan" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="">Bitte wählen...</option>
                                        <option value="vorhanden" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'vorhanden')>Vorhanden</option>
                                        <option value="nicht-vorhanden" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'nicht-vorhanden')>Nicht vorhanden</option>
                                        <option value="geplant" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'geplant')>Geplant</option>
                                        <option value="WLAN" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'WLAN')>WLAN</option>
                                        <option value="LAN" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'LAN')>LAN</option>
                                        <option value="Powerline" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'Powerline')>Powerline</option>
                                        <option value="Dongle" @selected(old('network_wlan', $alternative->network_wlan ?? '') == 'Dongle')>Dongle</option>
                                    </select>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-3 rounded-xl border border-slate-200 mt-3">
                                        <label class="flex items-center gap-2 text-sm cursor-pointer font-medium">
                                            <input type="checkbox" name="note_internetSteckdose" value="1" class="custom-cb focus:ring-brand-orange">
                                            Steckdose setzen
                                        </label>

                                        <div class="md:col-span-2">
                                            <input type="text" name="note_internetSteckdoseDist" class="w-full p-2 border rounded-lg outline-none focus:ring-brand-orange" placeholder="Entfernung zur nächsten Steckdose, z.B. 2m">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NOTIZEN -->
                    <div id="pv-sec-notizen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="notizen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('pv-content-notizen', 'pv-icon-notizen')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-notebook text-brand-orange"></i> Zusätzliche Notizen
                            </span>
                            <i id="pv-icon-notizen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="pv-content-notizen" class="p-5">
                            <div class="relative group">
                                <textarea id="pv-notes"
                                        name="energy_remark"
                                        rows="4"
                                        class="w-full p-4 pr-14 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange resize-none transition-shadow"
                                        placeholder="Tippen oder auf das Mikrofon klicken zum Diktieren...">{{ old('energy_remark', $alternative->energy_remark ?? '') }}</textarea>

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

                    <div class="mb-2 text-sm text-slate-500 flex items-center gap-2">
                        <span class="text-red-500 font-bold">*</span> markiert Pflichtfelder
                    </div>

                    <!-- KUNDENDATEN -->
                    <div id="wp-sec-kunden" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="kunden">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-kunden', 'wp-icon-kunden')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-users text-brand-green"></i> Kunde & Berater
                            </span>
                            <i id="wp-icon-kunden" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-kunden" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Firma <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
                                    <input type="text" name="firma" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Vorname <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Nachname <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="lastname" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Straße & Nr. <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="street" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        PLZ <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="postcode" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Ort <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="city" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Telefon <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
                                    <input type="text" name="telephone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Mobil <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
                                    <input type="text" name="phone" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        E-Mail <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
                                    <input type="email" name="email" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Berater / Monteur <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
                                    <input type="text" name="contact_person" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                            </div>

                            <!-- Abweichender Standort -->
                            <div class="mt-5 pt-5 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">
                                    Standort der Anlage, falls abweichend
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input type="text" name="alt_street" placeholder="Straße/Nr." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                    <input type="text" name="alt_postcode" placeholder="PLZ" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                    <input type="text" name="alt_city" placeholder="Ort" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GEBÄUDE -->
                    <div id="wp-sec-gebaeude" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="gebaeude">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-gebaeude', 'wp-icon-gebaeude')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-buildings text-brand-green"></i> Gebäudeeigenschaften
                            </span>
                            <i id="wp-icon-gebaeude" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-gebaeude" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Gebäudeart <span class="text-red-500">*</span>
                                    </label>
                                    <select name="building_type" required class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Einfamilienhaus">Einfamilienhaus</option>
                                        <option value="Reihenmittelhaus">Reihenmittelhaus</option>
                                        <option value="Doppelhaushälfte">Doppelhaushälfte</option>
                                        <option value="Mehrfamilienhaus">Mehrfamilienhaus</option>
                                        <option value="Gewerbe">Gewerbe</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Wohneinheiten</label>
                                    <input type="number" name="number_we" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Baujahr <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="house_year" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Wohnfläche m² <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="living_space" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Nutzfläche m² <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                                    </label>
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 pt-5 border-t border-slate-100">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3">Badewanne</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vorhanden?</label>
                                            <select name="note_bathtub" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="Nein">Nein</option>
                                                <option value="Ja">Ja</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Anzahl</label>
                                            <input type="number" name="bathtub_count" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Abmessung</label>
                                            <input type="text" name="note_bathtubDim" placeholder="z.B. 180 x 80 cm" class="w-full p-2 border rounded-lg text-sm focus:ring-brand-green outline-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3">Schwimmbad</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vorhanden?</label>
                                            <select name="note_pool" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="Nein">Nein</option>
                                                <option value="Ja">Ja</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Volumen m³</label>
                                            <input type="number" name="note_poolVolume" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AKTUELLE HEIZUNG -->
                    <div id="wp-sec-heizung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="heizung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-heizung', 'wp-icon-heizung')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-fire text-brand-green"></i> Aktuelle Heizung
                            </span>
                            <i id="wp-icon-heizung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-heizung" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kamin vorhanden?</label>
                                    <select name="fireplace" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="1">Ja</option>
                                        <option value="0">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Heizungsart <span class="text-red-500">*</span>
                                    </label>
                                    <select name="heating_system_type" required class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Öl">Öl</option>
                                        <option value="Gas">Gas</option>
                                        <option value="Pellets">Pellets</option>
                                        <option value="Sonstiges">Sonstiges</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Leistung kW <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="old_heating_power" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Aufstellort Geschoss</label>
                                    <select name="installation_location" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
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

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Heizung Material</label>
                                        <select name="pipe_system_material" class="select2-field w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Kupfer">Kupfer</option>
                                            <option value="Kunststoff">Kunststoff</option>
                                            <option value="Stahl">Stahl</option>
                                            <option value="Mehrschichtverbund">Mehrschichtverbund</option>
                                            <option value="Sonstiges">Sonstiges</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Heizung Dimension</label>
                                        <input type="text" name="heating_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">KW / WW Dimension</label>
                                        <input type="text" name="water_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zirkulation Dimension</label>
                                        <input type="text" name="circulation_pipe_dimension" placeholder="Dimension" class="w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ein-Rohr-System vorhanden?</label>
                                        <select name="note_einRohr" class="select2-field w-full p-2 border rounded-lg outline-none focus:border-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Ja">Ja</option>
                                            <option value="Nein">Nein</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Solar & Warmwasser -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3 text-slate-700">Thermische Solaranlage</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vorhanden?</label>
                                            <select name="solar_thermal" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="1">Ja</option>
                                                <option value="0">Nein</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Module / Fläche</label>
                                            <input type="number" name="solar_thermal_area" class="w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h4 class="font-bold text-sm mb-3 text-slate-700">Warmwasser Aufbereitung</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Art</label>
                                            <select name="hot_water_generation" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="direkt">Direkt</option>
                                                <option value="indirekt">Indirekt</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fassungsvermögen Liter</label>
                                            <input type="number" name="hot_water_tank_liters" class="w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ETAGEN & ZUSTAND -->
                    <div id="wp-sec-etagen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="etagen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-etagen', 'wp-icon-etagen')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-thermometer-hot text-brand-green"></i> Heizkreise & Zustand
                            </span>
                            <i id="wp-icon-etagen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-etagen" class="p-5">
                            <h4 class="font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Heizungen pro Etage</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- KG -->
                                <div class="p-4 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-3 text-brand-blue">Kellergeschoss (KG)</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                                            <select name="note_kgHeiz" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="beheizt">Beheizt</option>
                                                <option value="nicht beheizt">Nicht beheizt</option>
                                            </select>
                                        </div>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_kgFbh" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Fußbodenheizung</span>
                                        </label>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_kgHk" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Heizkörper</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- EG -->
                                <div class="p-4 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-3 text-brand-blue">Erdgeschoss (EG)</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                                            <select name="note_egHeiz" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="beheizt">Beheizt</option>
                                                <option value="nicht beheizt">Nicht beheizt</option>
                                            </select>
                                        </div>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_egFbh" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Fußbodenheizung</span>
                                        </label>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_egHk" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Heizkörper</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- OG -->
                                <div class="p-4 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-3 text-brand-blue">Obergeschoss (OG)</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                                            <select name="note_ogHeiz" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="beheizt">Beheizt</option>
                                                <option value="nicht beheizt">Nicht beheizt</option>
                                            </select>
                                        </div>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_ogFbh" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Fußbodenheizung</span>
                                        </label>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_ogHk" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Heizkörper</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- DG -->
                                <div class="p-4 border rounded-xl bg-slate-50">
                                    <h4 class="font-bold text-sm mb-3 text-brand-blue">Dachgeschoss (DG)</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                                            <select name="note_dgHeiz" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                                <option value="">Bitte wählen...</option>
                                                <option value="beheizt">Beheizt</option>
                                                <option value="nicht beheizt">Nicht beheizt</option>
                                            </select>
                                        </div>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_dgFbh" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Fußbodenheizung</span>
                                        </label>

                                        <label class="flex items-center gap-2 p-2 bg-white border rounded-lg cursor-pointer">
                                            <input type="checkbox" name="note_dgHk" value="1" class="custom-cb focus:ring-brand-green">
                                            <span class="text-sm font-medium">Heizkörper</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Heizkreis 1 Vorlauf °C</label>
                                    <input type="number" name="flow_temperature" placeholder="Vorlauf °C" class="w-full p-3 border rounded-xl focus:ring-brand-green outline-none bg-slate-50">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Heizkreis 2 Vorlauf °C</label>
                                    <input type="number" name="note_flow_temperature_2" placeholder="Vorlauf °C" class="w-full p-3 border rounded-xl focus:ring-brand-green outline-none bg-slate-50">
                                </div>
                            </div>

                            <h4 class="font-bold text-slate-700 mb-3 border-b border-slate-100 pb-2">Zustand Fußbodenheizung / Heizkörper</h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Regler für Kühlung geeignet?</label>
                                    <select name="note_reglerKuehlung" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">HKV für hydr. Abgleich geeignet?</label>
                                    <select name="note_hkvAbgleich" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Stellantriebe geeignet?</label>
                                    <select name="note_stellantriebAbgleich" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NEUE ANLAGE -->
                    <div id="wp-sec-anlage" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="anlage">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-anlage', 'wp-icon-anlage')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-package text-brand-green"></i> Neue Anlage / Aufstellmöglichkeit
                            </span>
                            <i id="wp-icon-anlage" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-anlage" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                                        Neue Wärmequelle <span class="text-red-500">*</span>
                                    </label>
                                    <select name="objective" required class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Luft-Wasser Wärmepumpe">Luft-Wasser Wärmepumpe</option>
                                        <option value="Sole-Wasser Wärmepumpe">Sole-Wasser Wärmepumpe</option>
                                        <option value="Abluft-Wärmepumpe">Abluft-Wärmepumpe</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Interesse an Passiv-Kühlung?</label>
                                    <select name="note_passivKuehlung" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Lüftung</label>
                                    <select name="ventilation_type" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="vorhanden Ja">Vorhanden Ja</option>
                                        <option value="Nein">Nein</option>
                                        <option value="geplant zentral">Geplant zentral</option>
                                        <option value="geplant dezentral">Geplant dezentral</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Platz für VVM 500 vorhanden?</label>
                                    <select name="note_platzVvm500" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Platz für WM S320 vorhanden?</label>
                                    <select name="note_platzWm320" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Müssen Einzelkomponenten verwendet werden?</label>
                                    <select name="note_einzelKomponenten" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EINBRINGMASSE & ZUWEGUNG -->
                    <div id="wp-sec-einbringung" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="einbringung">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-einbringung', 'wp-icon-einbringung')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-ruler text-brand-green"></i> Einbringmaße & Zuwegung
                            </span>
                            <i id="wp-icon-einbringung" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-einbringung" class="p-5">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
                                <h4 class="font-bold text-sm mb-3">Einbringmaße Zuwegung Heizraum</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zuwegung Heizraum</label>
                                        <select name="note_zuwegungHeizraum" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="KG">KG</option>
                                            <option value="EG">EG</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min. Breite zur Installation cm</label>
                                        <input type="number" name="door_width_for_installation" placeholder="Breite" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppen vorhanden?</label>
                                        <select name="note_treppen" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Nein">Nein</option>
                                            <option value="Ja">Ja</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 1</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_t1Breite" placeholder="Breite" class="w-1/2 p-1.5 border rounded outline-none">
                                            <input type="number" name="note_t1Hoehe" placeholder="Höhe" class="w-1/2 p-1.5 border rounded outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 2</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_t2Breite" placeholder="Breite" class="w-1/2 p-1.5 border rounded outline-none">
                                            <input type="number" name="note_t2Hoehe" placeholder="Höhe" class="w-1/2 p-1.5 border rounded outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 3</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_t3Breite" placeholder="Breite" class="w-1/2 p-1.5 border rounded outline-none">
                                            <input type="number" name="note_t3Hoehe" placeholder="Höhe" class="w-1/2 p-1.5 border rounded outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 4</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_t4Breite" placeholder="Breite" class="w-1/2 p-1.5 border rounded outline-none">
                                            <input type="number" name="note_t4Hoehe" placeholder="Höhe" class="w-1/2 p-1.5 border rounded outline-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-200 pt-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppenart</label>
                                        <select name="note_treppenArt" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="gradeläufig">Gradeläufig</option>
                                            <option value="L-Form">L-Form</option>
                                            <option value="U-Form">U-Form</option>
                                            <option value="Wendel">Wendel</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppenbreite cm</label>
                                        <input type="number" name="note_treppenBreite" placeholder="Breite" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Länge AE zu IE</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="heat_pump_pipe_length" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none">
                                        <span class="text-sm">m</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Anschluss außen</label>
                                    <select name="note_anschlussAussen" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Wand">Wand</option>
                                        <option value="Boden">Boden</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border border-brand-orange/30 bg-brand-orange/5 p-4 rounded-xl">
                                <h4 class="font-bold text-sm mb-4 text-brand-orange">Alternative Aufstellmöglichkeit</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vorhanden?</label>
                                        <select name="note_alternativeAufstellung" class="select2-field w-full p-2 border rounded-lg outline-none bg-white focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Ja">Ja</option>
                                            <option value="Nein">Nein</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zuwegung Breite cm</label>
                                        <input type="number" name="note_altBreite" placeholder="Breite" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zuwegung Höhe cm</label>
                                        <input type="number" name="note_altHoehe" placeholder="Höhe" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none bg-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 1</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_altT1Breite" placeholder="Breite" class="w-1/2 p-2 border rounded outline-none bg-white">
                                            <input type="number" name="note_altT1Hoehe" placeholder="Höhe" class="w-1/2 p-2 border rounded outline-none bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <span class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Türmaße 2</span>
                                        <div class="flex gap-1">
                                            <input type="number" name="note_altT2Breite" placeholder="Breite" class="w-1/2 p-2 border rounded outline-none bg-white">
                                            <input type="number" name="note_altT2Hoehe" placeholder="Höhe" class="w-1/2 p-2 border rounded outline-none bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-slate-200/60 pt-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppen vorhanden?</label>
                                        <select name="note_altTreppen" class="select2-field w-full p-2 border rounded-lg outline-none bg-white focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Nein">Nein</option>
                                            <option value="Ja">Ja</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppenart</label>
                                        <select name="note_altTreppenArt" class="select2-field w-full p-2 border rounded-lg outline-none bg-white focus:ring-brand-green">
                                            <option value="">Bitte wählen...</option>
                                            <option value="Wendeltreppe">Wendeltreppe</option>
                                            <option value="geradeläufige Treppe">Geradeläufige Treppe</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Treppenbreite cm</label>
                                        <input type="number" name="note_altTreppenBreite" placeholder="Breite" class="w-full p-2 border rounded-lg focus:ring-brand-green outline-none bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ELEKTRO -->
                    <div id="wp-sec-elektro" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="elektro">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-elektro', 'wp-icon-elektro')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-plug text-brand-green"></i> Elektroinstallation
                            </span>
                            <i id="wp-icon-elektro" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-elektro" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">SG Ready / EnWG 14a</label>
                                    <select name="enwg_14a_ready" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="1">Ja</option>
                                        <option value="0">Nein</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Internet am Aufstellort</label>
                                    <select name="network_wlan" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                        <option value="WLAN">WLAN</option>
                                        <option value="LAN">LAN</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Stromzähler Anzahl</label>
                                    <input type="number" name="meter_count" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SONSTIGE ARBEITEN -->
                    <div id="wp-sec-sonstiges" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="sonstiges">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-sonstiges', 'wp-icon-sonstiges')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-wrench text-brand-green"></i> Sonstige Arbeiten & Elemente
                            </span>
                            <i id="wp-icon-sonstiges" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-sonstiges" class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fundament & Erdarbeiten durch</label>
                                    <select name="groundwork" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Solar Aspekt">Solar Aspekt</option>
                                        <option value="Kunde">Kunde</option>
                                    </select>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kondenswasser AE</label>
                                    <select name="note_kondenswasser" class="select2-field w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Sickergrube">Sickergrube</option>
                                        <option value="Abflussrohr ins Erdreich">Abflussrohr ins Erdreich</option>
                                        <option value="Anschluss im Haus">Anschluss im Haus</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SCHALLBERECHNUNG -->
                    <div id="wp-sec-schall" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="schall">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-schall', 'wp-icon-schall')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-waves text-brand-green"></i> Infos zur Schallberechnung
                            </span>
                            <i id="wp-icon-schall" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-schall" class="p-5">
                            <p class="text-xs text-slate-400 mb-4 uppercase tracking-wide">
                                Speziell für Kunden in Bad Homburg bzw. nach regionalen Vorgaben
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Aufstellgebiet</label>
                                    <select name="note_schallGebiet" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Industriegebiet">Industriegebiet</option>
                                        <option value="urbanes Gebiet">Urbanes Gebiet</option>
                                        <option value="Allg. Wohngebiet">Allg. Wohngebiet / Kleinsiedlung</option>
                                        <option value="Gewerbegebiet">Gewerbegebiet</option>
                                        <option value="Kern-, Dorf-, Mischgebiet">Kern-, Dorf-, Mischgebiet</option>
                                        <option value="reines Wohngebiet">Reines Wohngebiet</option>
                                        <option value="Kurgebiet">Kurgebiet</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Aufstellort</label>
                                    <select name="note_schallOrt" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Freistehend >3m">Freistehend &gt;3m von Wand</option>
                                        <option value="Wand <3m">An Wand &lt;3m</option>
                                        <option value="Ecke <3m">In Ecke &lt;3m</option>
                                        <option value="Wand <5m">An Wand &lt;5m</option>
                                        <option value="Zwischen Wänden <5m">Zwischen Wänden &lt;5m</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Abschirmung</label>
                                    <select name="note_schallAbschirmung" class="select2-field w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Sichtkontakt">Sichtkontakt</option>
                                        <option value="kein Sichtkontakt">Kein Sichtkontakt</option>
                                        <option value="auf abgewandter Seite">Auf abgewandter Seite</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Maßgeblicher Immissionsort m</label>
                                    <input type="number" name="note_schallImmissionOrt" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NOTIZEN -->
                    <div id="wp-sec-notizen" class="bg-white rounded-2xl shadow-sm border border-slate-200 scroll-mt-offset" data-section="notizen">
                        <h3 class="font-bold text-slate-800 text-lg p-5 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition rounded-t-2xl border-b border-slate-100" onclick="toggleSection('wp-content-notizen', 'wp-icon-notizen')">
                            <span class="flex items-center gap-2">
                                <i class="ph-fill ph-notebook text-brand-green"></i> Zusätzliche Notizen
                            </span>
                            <i id="wp-icon-notizen" class="ph-bold ph-caret-up text-slate-400 transition-transform"></i>
                        </h3>

                        <div id="wp-content-notizen" class="p-5">
                            <div class="relative group">
                                <textarea id="wp-notes" name="note" rows="4" class="w-full p-4 pr-14 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-green resize-none transition-shadow" placeholder="Tippen oder auf das Mikrofon klicken zum Diktieren..."></textarea>

                                <button type="button" onclick="toggleDictation('wp-notes', 'wp-mic-icon')" class="absolute top-3 right-3 p-2 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-brand-green hover:border-brand-green/30 transition active:scale-95" title="Spracheingabe starten/stoppen">
                                    <i id="wp-mic-icon" class="ph-bold ph-microphone text-2xl"></i>
                                </button>
                            </div>

                            <p class="text-xs text-slate-400 mt-2">
                                <i class="ph-fill ph-info"></i> Nutze das Mikrofon, um Besonderheiten schnell einzusprechen.
                            </p>
                        </div>
                    </div>

                    <button id="btn-submit-wp" type="submit" class="w-full mt-4 bg-brand-green hover:opacity-90 text-white p-4 rounded-2xl font-bold flex justify-center items-center gap-2 shadow-lg shadow-brand-green/30 transition active:scale-95 text-lg">
                        <i class="ph-bold ph-floppy-disk text-2xl"></i> WP Aufmaß Speichern
                    </button>
                </form>
            </div>
        </section>


        <div id="modal-product-picker" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[80] flex items-center justify-center p-4 md:p-8 opacity-0 transition-opacity duration-300">
            <div id="modal-product-picker-content" class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl transform scale-95 transition-transform duration-300 overflow-hidden">

                <div class="p-5 md:p-6 border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="ph-fill ph-magnifying-glass text-brand-blue"></i>
                            Produkt hinzufügen
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">Produkt suchen, Lagerbestand prüfen oder manuell anlegen</p>
                    </div>

                    <button type="button" onclick="closeProductPicker()" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-2 rounded-full hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>

                <div class="p-4 md:p-6 bg-slate-50 overflow-y-auto flex-1">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-4">
                        <label class="block text-xs font-black text-slate-500 uppercase mb-2">Produkt suchen</label>

                        <div class="relative">
                            <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                            <input
                                type="text"
                                id="product-picker-search"
                                oninput="searchProductsForMaterial()"
                                placeholder="Artikelname, Art.-Nr, SKU, EAN..."
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-blue font-semibold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase mb-1">Plan-Menge</label>
                                <input id="product-picker-plan" type="number" step="0.1" min="0" value="0" class="w-full p-2.5 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase mb-1">Verbrauch / benötigt</label>
                                <input id="product-picker-verbrauch" type="number" step="0.1" min="0" value="1" class="w-full p-2.5 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase mb-1">Einheit</label>
                                <input id="product-picker-unit" type="text" value="Stk" class="w-full p-2.5 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>
                        </div>
                    </div>

                    <div id="product-picker-results" class="space-y-3">
                        <div class="text-center py-10 text-slate-400">
                            <i class="ph ph-package text-5xl"></i>
                            <p class="mt-2 font-semibold">Suche ein Produkt oder lege es manuell an.</p>
                        </div>
                    </div>

                    <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-4 mt-5">
                        <h3 class="font-black text-slate-800 flex items-center gap-2 mb-3">
                            <i class="ph-bold ph-pencil-simple text-brand-orange"></i>
                            Produkt nicht gefunden?
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-black text-slate-400 uppercase mb-1">Manueller Produktname</label>
                                <input id="manual-product-name" type="text" placeholder="z.B. Spezialhalterung..." class="w-full p-2.5 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase mb-1">Art.-Nr optional</label>
                                <input id="manual-product-article-no" type="text" placeholder="Optional" class="w-full p-2.5 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                            </div>

                            <div class="flex items-end">
                                <button type="button" onclick="addManualProductToMaterial()" class="w-full px-4 py-2.5 rounded-xl bg-brand-orange text-white font-black hover:opacity-90 transition flex items-center justify-center gap-2">
                                    <i class="ph-bold ph-plus"></i>
                                    Manuell hinzufügen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@php
/*
|--------------------------------------------------------------------------
| Basic helpers
|--------------------------------------------------------------------------
*/

$normalizeImage = function ($image) {
    if (empty($image)) {
        return asset('images/icons/placeholder.svg');
    }

    if (
        str_starts_with($image, 'http://') ||
        str_starts_with($image, 'https://') ||
        str_starts_with($image, 'data:')
    ) {
        return $image;
    }

    return asset(ltrim($image, '/'));
};

$normalizeEmployeeImage = function ($image) {
    if (empty($image)) {
        return asset('images/icons/placeholder.svg');
    }

    if (
        str_starts_with($image, 'http://') ||
        str_starts_with($image, 'https://') ||
        str_starts_with($image, 'data:')
    ) {
        return $image;
    }

    return asset('images/employee/' . ltrim($image, '/'));
};

$decodeArray = function ($value) {
    if (is_array($value)) {
        return $value;
    }

    if ($value instanceof \Illuminate\Support\Collection) {
        return $value->toArray();
    }

    if (is_string($value) && trim($value) !== '') {
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    return [];
};

$normalizeBool = function ($value) {
    if ($value === null || $value === '') {
        return null;
    }

    if (is_bool($value)) {
        return $value;
    }

    $value = mb_strtolower(trim((string) $value), 'UTF-8');

    if (in_array($value, ['1', 'true', 'ja', 'yes', 'on', 'vorhanden'], true)) {
        return true;
    }

    if (in_array($value, ['0', 'false', 'nein', 'no', 'off', 'none', 'nicht vorhanden'], true)) {
        return false;
    }

    return null;
};

$boolToJaNein = function ($value) use ($normalizeBool) {
    $bool = $normalizeBool($value);

    if ($bool === null) {
        return null;
    }

    return $bool ? 'Ja' : 'Nein';
};

$boolToOneZero = function ($value) use ($normalizeBool) {
    $bool = $normalizeBool($value);

    if ($bool === null) {
        return null;
    }

    return $bool ? 1 : 0;
};

$firstFilled = function (...$values) {
    foreach ($values as $value) {
        if ($value !== null && $value !== '') {
            return $value;
        }
    }

    return null;
};

$cleanData = function (array $data) {
    return collect($data)
        ->filter(function ($value) {
            if ($value === null || $value === '') {
                return false;
            }

            if (is_array($value) && empty($value)) {
                return false;
            }

            return true;
        })
        ->toArray();
};

/*
|--------------------------------------------------------------------------
| Employee helpers
|--------------------------------------------------------------------------
| Important:
| auth()->user()->name is the employee ID in this app.
|--------------------------------------------------------------------------
*/

$employeeMap = $employeeMap ?? [];

$currentEmployeeId = (string) (auth()->user()->name ?? auth()->id());

$currentEmployee = $employeeMap[$currentEmployeeId] ?? [
    'id' => $currentEmployeeId,
    'name' => $currentEmployeeId !== '' ? ('Mitarbeiter #' . $currentEmployeeId) : 'System',
    'image' => asset('images/icons/placeholder.svg'),
];

$getEmployeeInfo = function ($employeeId) use ($employeeMap) {
    $employeeId = (string) ($employeeId ?? '');

    if ($employeeId !== '' && isset($employeeMap[$employeeId])) {
        return [
            'id' => (string) ($employeeMap[$employeeId]['id'] ?? $employeeId),
            'name' => $employeeMap[$employeeId]['name'] ?? ('Mitarbeiter #' . $employeeId),
            'image' => $employeeMap[$employeeId]['image'] ?? asset('images/icons/placeholder.svg'),
        ];
    }

    return [
        'id' => $employeeId,
        'name' => $employeeId !== '' ? ('Mitarbeiter #' . $employeeId) : 'System',
        'image' => asset('images/icons/placeholder.svg'),
    ];
};

/*
|--------------------------------------------------------------------------
| Material helpers
|--------------------------------------------------------------------------
*/

$mapSubItem = function ($item) use (&$mapSubItem, $normalizeImage) {
    if (($item['kind'] ?? null) === 'labor' || ($item['item_type'] ?? null) === 'labor') {
        return null;
    }

    $planQty = (float) ($item['qty_total'] ?? $item['qty'] ?? $item['qty_offer'] ?? 0);

    return [
        'id' => (string) ($item['component_id'] ?? $item['productId'] ?? $item['product_id'] ?? uniqid('sub_', true)),
        'item_type' => $item['item_type'] ?? null,
        'kind' => $item['kind'] ?? null,
        'name' => $item['name'] ?? 'Unbenannt',
        'description' => $item['desc_html'] ?? $item['desc'] ?? null,
        'img' => $normalizeImage($item['img'] ?? $item['image'] ?? null),

        'plan_qty' => $planQty,
        'verbrauch_qty' => (float) ($item['qty_measurement'] ?? $item['qty_final'] ?? $planQty),
        'qty' => $planQty,

        'unit' => $item['unit'] ?? $item['measure'] ?? 'Stk',
        'article_no' => $item['article_no'] ?? null,
        'distributor' => $item['distributor_name'] ?? $item['supplier'] ?? null,
        'distributor_no' => $item['distributor_article_no'] ?? null,
        'approved' => (bool) ($item['approved'] ?? $item['is_checked'] ?? false),
        'subItems' => [],
    ];
};

$mapMainItem = function ($item) use ($mapSubItem, $normalizeImage) {
    if (($item['kind'] ?? null) === 'labor' || ($item['item_type'] ?? null) === 'labor') {
        return null;
    }

    $planQty = (float) ($item['qty_total'] ?? $item['qty'] ?? $item['qty_offer'] ?? 0);

    $subItems = [];

    foreach (($item['subItems'] ?? []) as $subItem) {
        $mappedSub = $mapSubItem($subItem);

        if ($mappedSub) {
            $subItems[] = $mappedSub;
        }
    }

    return [
        'id' => (string) ($item['component_id'] ?? $item['productId'] ?? $item['product_id'] ?? uniqid('mat_', true)),
        'item_type' => $item['item_type'] ?? null,
        'kind' => $item['kind'] ?? null,
        'name' => $item['name'] ?? 'Unbenannt',
        'description' => $item['desc_html'] ?? $item['desc'] ?? null,
        'img' => $normalizeImage($item['img'] ?? $item['image'] ?? null),

        'plan_qty' => $planQty,
        'verbrauch_qty' => (float) ($item['qty_measurement'] ?? $item['qty_final'] ?? $planQty),
        'qty' => $planQty,

        'unit' => $item['unit'] ?? $item['measure'] ?? 'Stk',
        'article_no' => $item['article_no'] ?? null,
        'distributor' => $item['distributor_name'] ?? $item['supplier'] ?? null,
        'distributor_no' => $item['distributor_article_no'] ?? null,
        'approved' => (bool) ($item['approved'] ?? $item['is_checked'] ?? false),
        'subItems' => $subItems,
    ];
};

$buildMaterialsFromSections = function ($sections) use ($decodeArray, $mapMainItem) {
    $sections = $decodeArray($sections);
    $result = [];

    foreach ($sections as $section) {
        $items = [];

        foreach (($section['items'] ?? []) as $item) {
            $mapped = $mapMainItem($item);

            if ($mapped) {
                $items[] = $mapped;
            }
        }

        if (!empty($items)) {
            $result[] = [
                'id' => $section['id'] ?? uniqid('section_', true),
                'title' => $section['title'] ?? 'Ohne Bereich',
                'items' => $items,
            ];
        }
    }

    return $result;
};

$buildMaterialsFromItems = function ($items) use ($normalizeImage) {
    $groups = [];

    foreach ($items as $item) {
        if (($item->kind ?? null) === 'labor' || ($item->item_type ?? null) === 'labor') {
            continue;
        }

        $sectionTitle = $item->section_title ?: 'Ohne Bereich';

        if (!isset($groups[$sectionTitle])) {
            $groups[$sectionTitle] = [
                'id' => 'section_' . md5($sectionTitle),
                'title' => $sectionTitle,
                'items' => [],
            ];
        }

        $row = [
            'id' => (string) $item->id,
            'item_type' => $item->item_type,
            'kind' => $item->kind,
            'name' => $item->name ?: 'Unbenannt',
            'description' => $item->description,
            'img' => $normalizeImage($item->image),

            'plan_qty' => (float) ($item->qty_offer ?? 0),
            'verbrauch_qty' => (float) ($item->qty_measurement ?? $item->qty_final ?? $item->qty_offer ?? 0),
            'qty' => (float) ($item->qty_offer ?? 0),

            'unit' => $item->unit ?: $item->measure ?: 'Stk',
            'article_no' => $item->article_no,
            'distributor' => $item->distributor_name,
            'distributor_no' => $item->distributor_article_no,
            'subItems' => [],
            'approved' => (bool) ($item->is_checked ?? false),
            '_depth' => (int) ($item->depth ?? 0),
        ];

        if ($row['_depth'] === 0) {
            unset($row['_depth']);
            $groups[$sectionTitle]['items'][] = $row;
        } else {
            $lastIndex = count($groups[$sectionTitle]['items']) - 1;

            unset($row['_depth']);

            if ($lastIndex >= 0) {
                $groups[$sectionTitle]['items'][$lastIndex]['subItems'][] = $row;
            } else {
                $groups[$sectionTitle]['items'][] = $row;
            }
        }
    }

    return array_values($groups);
};

/*
|--------------------------------------------------------------------------
| Roof / form data builders
|--------------------------------------------------------------------------
*/

$buildRoofData = function ($alternative) use ($cleanData, $boolToOneZero) {
    if (!$alternative || !$alternative->relationLoaded('roofs')) {
        return [];
    }

    return $alternative->roofs
        ->map(function ($roof) use ($cleanData, $boolToOneZero) {
            return $cleanData([
                'roof_type' => $roof->roof_type ?: $roof->roof,
                'roof_height' => $roof->roof_height,
                'roof_pitch' => $roof->roof_pitch,
                'roof_orientation' => $roof->roof_orientation,
                'roof_azimuth' => $roof->roof_azimuth,
                'roof_area' => $roof->roof_area,
                'roof_age' => $roof->roof_age,

                'roof_covering' => $roof->roof_covering_name ?: $roof->roof_covering,
                'roof_covering_company' => $roof->roof_covering_company,
                'roof_covering_model' => $roof->roof_covering_model,
                'roof_covering_dimensions_cm' => $roof->roof_covering_dimensions_cm,

                'solar_holding_tile_desired' => $boolToOneZero($roof->solar_holding_tile_desired),
                'rafter_reinforcement_needed' => $boolToOneZero($roof->rafter_reinforcement_needed),
                'rafter_thickness' => $roof->rafter_thickness,

                'between_rafter_insulation' => $roof->between_rafter_insulation,
                'thickness_between_rafter' => $roof->thickness_between_rafter,
                'roof_insulation' => $roof->roof_insulation,
                'thickness_roof_insulation' => $roof->thickness_roof_insulation,
                'insulation_material' => $roof->insulation_material,

                'dc_cable_route' => $roof->dc_cable_route,
                'scaffold_usage' => $boolToOneZero($roof->scaffold_usage),

                'kwp_size' => $roof->kwp_size,
                'module_count' => $roof->module_count,
                'module_power' => $roof->module_power,

                'shading' => $roof->shading,
                'storage_preference' => $roof->storage_preference,
                'backup_power' => $roof->backup_power,
                'pv_investment_costs' => $roof->pv_investment_costs,
            ]);
        })
        ->values()
        ->toArray();
};

$buildAlternativeData = function ($customer, $alternative) use ($cleanData, $boolToOneZero, $firstFilled) {
    if (!$alternative) {
        return [];
    }

    return $cleanData([
        'alternative_id' => $alternative->id,

        'object_name' => $alternative->object_name,

        'street' => $firstFilled($alternative->street, optional($customer)->street),
        'postcode' => $firstFilled($alternative->postcode, optional($customer)->postcode),
        'city' => $firstFilled($alternative->city, optional($customer)->city),
        'full_address' => $alternative->full_address,

        'request_date' => optional($alternative->request_date)->format('Y-m-d'),
        'project_date' => optional($alternative->project_date)->format('Y-m-d'),

        'building_type' => $firstFilled(
            $alternative->building_type,
            $alternative->object_type,
            $alternative->objective
        ),

        'house_year' => $firstFilled(
            $alternative->house_year,
            $alternative->building_year
        ),

        'living_space' => $firstFilled(
            $alternative->living_space,
            $alternative->heated_area
        ),

        'number_people' => $firstFilled(
            $alternative->number_people,
            $alternative->person_count
        ),

        'number_we' => $firstFilled(
            $alternative->number_we,
            $alternative->owner_count
        ),

        'unusable_space' => $alternative->unusable_space,
        'bathroom_count' => $alternative->bathroom_count,
        'building_condition' => $alternative->building_condition,

        'building_length' => $alternative->building_length,
        'building_width' => $alternative->building_width,
        'facade_height' => $alternative->facade_height,
        'total_window_area' => $alternative->total_window_area,

        'roof_type' => $alternative->roof_type,
        'roof_age' => $alternative->roof_age,
        'roof_pitch' => $alternative->roof_pitch,
        'roof_direction' => $alternative->roof_direction,
        'roof_covering' => $alternative->roof_covering,
        'roof_remark' => $alternative->roof_remark,

        'annual_consumption' => $alternative->annual_consumption,
        'annual_heating_energy_consumption' => $alternative->annual_heating_energy_consumption,
        'annual_heating_energy_consumption_kwh' => $alternative->annual_heating_energy_consumption_kwh,
        'heating_energy_unit' => $alternative->heating_energy_unit,
        'total_heat_consumption' => $alternative->total_heat_consumption,
        'total_electricity_consumption' => $alternative->total_electricity_consumption,

        'heating_system_type' => $alternative->heating_system_type,
        'heating_type' => $alternative->heating_type,
        'heating_system_age' => $alternative->heating_system_age,
        'heating_system_year' => $alternative->heating_system_year,
        'heating_age_group' => $alternative->heating_age_group,
        'old_heating_power' => $alternative->old_heating_power,
        'heat_distribution' => $alternative->heat_distribution,
        'flow_temperature' => $alternative->flow_temperature,
        'heating_load_calculation' => $alternative->heating_load_calculation,
        'heating_notes' => $alternative->heating_notes,
        'heating_remark' => $alternative->heating_remark,

        'installation_location' => $alternative->installation_location,
        'installation_location_extra' => $alternative->installation_location_extra,
        'installation_location_power' => $alternative->installation_location_power,

        'fireplace' => $boolToOneZero($alternative->fireplace),
        'wood_consumption' => $alternative->wood_consumption,
        'fireplace_value' => $alternative->fireplace_value,

        'quantity' => $alternative->quantity,
        'consumption' => $alternative->consumption,
        'bathtub_count' => $alternative->bathtub_count,
        'hot_water_generation' => $alternative->hot_water_generation,
        'hot_water_tank_liters' => $alternative->hot_water_tank_liters,
        'heat_pump_pipe_length' => $alternative->heat_pump_pipe_length,
        'basement_ceiling_height' => $alternative->basement_ceiling_height,
        'door_width_for_installation' => $alternative->door_width_for_installation,

        'heating_circuits_count' => $alternative->heating_circuits_count,
        'pipe_system_count' => $alternative->pipe_system_count,
        'pipe_system_material' => $alternative->pipe_system_material,
        'circulation_line' => $alternative->circulation_line,
        'heating_pipe_dimension' => $alternative->heating_pipe_dimension,
        'water_pipe_dimension' => $alternative->water_pipe_dimension,
        'circulation_pipe_dimension' => $alternative->circulation_pipe_dimension,

        'power_household' => $alternative->power_household,
        'power_heatpump' => $alternative->power_heatpump,
        'power_electric_car' => $alternative->power_electric_car,
        'power_other' => $alternative->power_other,
        'power_total' => $alternative->power_total,

        'meter_cabinet' => $alternative->meter_cabinet,
        'meter_cabinet_action' => $alternative->meter_cabinet_action,
        'meter_count' => $alternative->meter_count,

        'sls_switch' => $boolToOneZero($alternative->sls_switch),
        'ac_surge_protection' => $boolToOneZero($alternative->ac_surge_protection),
        'enwg_14a_ready' => $boolToOneZero($alternative->enwg_14a_ready),
        'tenant_model' => $boolToOneZero($alternative->tenant_model),
        'load_management' => $boolToOneZero($alternative->load_management),

        'apz_field' => $alternative->apz_field,
        'grid_reserve' => $alternative->grid_reserve,
        'cabinet_size' => $alternative->cabinet_size,
        'network_wlan' => $alternative->network_wlan,

        'electric_car' => $alternative->electric_car,
        'electric_car_plan' => $alternative->electric_car_plan,
        'electric_car_count' => $alternative->electric_car_count,
        'car_kilo' => $alternative->car_kilo,
        'company_vehicle' => $alternative->company_vehicle,
        'bidirectional_car' => $alternative->bidirectional_car,

        'wallbox_count' => $alternative->wallbox_count,
        'wallbox_location' => $alternative->wallbox_location,
        'charging_power' => $alternative->charging_power,
        'access_control' => $alternative->access_control,

        'solar_module_kwp' => $alternative->solar_module_kwp,
        'kwp_size' => $alternative->solar_module_kwp,
        'solar_tile_kwp' => $alternative->solar_tile_kwp,
        'battery_kwh' => $alternative->battery_kwh,
        'balcony_modules' => $alternative->balcony_modules,

        'has_pump_upgrade' => $boolToOneZero($alternative->has_pump_upgrade),
        'hydraulic_only' => $boolToOneZero($alternative->hydraulic_only),
        'solar_thermal' => $boolToOneZero($alternative->solar_thermal),
        'solar_thermal_area' => $alternative->solar_thermal_area,
        'solar_thermal_simulation' => $alternative->solar_thermal_simulation,

        'object_remark' => $alternative->object_remark,
        'energy_remark' => $alternative->energy_remark,
        'car_remark' => $alternative->car_remark,
        'stage' => $alternative->stage,
        'note' => $alternative->note,
        'info' => $alternative->note,
    ]);
};

$buildPvWpDetailData = function ($pvWp) use ($cleanData, $boolToJaNein, $boolToOneZero) {
    if (!$pvWp) {
        return [];
    }

    return $cleanData([
        'note_kabelAusreichend' => $boolToJaNein($pvWp->cables_sufficient),
        'note_demontageVerbleib' => $pvWp->dismantling_remain_at_customer,

        'storage_preference' => (
            $pvWp->battery_type ||
            $pvWp->battery_size ||
            $pvWp->battery_location
        ) ? 'Ja' : null,

        'note_battery_type' => $pvWp->battery_type,
        'note_battery_size' => $pvWp->battery_size,
        'note_battery_location' => $pvWp->battery_location,
        'note_batteryDistWrZs' => $pvWp->battery_dist_inverter_meter,
        'note_batteryDistBaWr' => $pvWp->battery_dist_battery_inverter,

        'note_wp_integration' => $boolToJaNein($pvWp->wp_integration),
        'note_wp_type' => $pvWp->wp_type,
        'note_wpStatus' => $pvWp->wp_status,
        'note_wp_heizstab' => $boolToJaNein($pvWp->wp_heating_rod),

        'note_wallbox_distance' => $pvWp->wallbox_distance_meter,
        'note_wallboxKernbohrung' => $boolToJaNein($pvWp->wallbox_core_drilling),
        'note_wbErdarbeiten' => $boolToJaNein($pvWp->earthworks_required),
        'note_wbErdarbeitenLaenge' => $pvWp->earthworks_length,
        'note_wbErdarbeitenDurch' => $pvWp->earthworks_by,
        'note_sonstigeWunsche' => $pvWp->other_customer_wishes,

        'note_zwischenzaehler' => $boolToJaNein($pvWp->meter_cabinet_submeter_required),
        'meter_count' => $pvWp->meter_cabinet_submeter_count,
        'note_zwischenzaehlerWp' => $boolToJaNein($pvWp->meter_cabinet_wp_submeter_required),
        'note_zwischenzaehlerWpCount' => $pvWp->meter_cabinet_wp_submeter_count,

        'note_internetSteckdose' => $boolToOneZero($pvWp->internet_socket_required),
        'note_internetSteckdoseDist' => $pvWp->internet_socket_distance,

        'note_bathtub' => $boolToJaNein($pvWp->has_bathtub),
        'note_bathtubDim' => $pvWp->bathtub_dimensions,
        'note_pool' => $boolToJaNein($pvWp->has_pool),
        'note_poolVolume' => $pvWp->pool_volume,

        'note_einRohr' => $boolToJaNein($pvWp->single_pipe_system),
        'solar_thermal' => $boolToOneZero($pvWp->solar_thermal_keep),
        'solar_thermal_area' => $pvWp->solar_thermal_modules,

        'note_kgHeiz' => $pvWp->kg_heated,
        'note_egHeiz' => $pvWp->eg_heated,
        'note_ogHeiz' => $pvWp->og_heated,
        'note_dgHeiz' => $pvWp->dg_heated,

        'note_kgFbh' => $boolToOneZero($pvWp->kg_underfloor),
        'note_egFbh' => $boolToOneZero($pvWp->eg_underfloor),
        'note_ogFbh' => $boolToOneZero($pvWp->og_underfloor),
        'note_dgFbh' => $boolToOneZero($pvWp->dg_underfloor),

        'note_kgHk' => $boolToOneZero($pvWp->kg_radiator),
        'note_egHk' => $boolToOneZero($pvWp->eg_radiator),
        'note_ogHk' => $boolToOneZero($pvWp->og_radiator),
        'note_dgHk' => $boolToOneZero($pvWp->dg_radiator),

        'flow_temperature' => $pvWp->hk1_flow_temp,
        'note_flow_temperature_2' => $pvWp->hk2_flow_temp,

        'note_reglerKuehlung' => $boolToJaNein($pvWp->controller_cooling_suitable),
        'note_hkvAbgleich' => $boolToJaNein($pvWp->hkv_balancing_suitable),
        'note_stellantriebAbgleich' => $boolToJaNein($pvWp->actuator_balancing_suitable),

        'note_passivKuehlung' => $boolToJaNein($pvWp->passive_cooling_interest),
        'note_platzVvm500' => $boolToJaNein($pvWp->space_vvm500),
        'note_platzWm320' => $boolToJaNein($pvWp->space_wm320),
        'note_einzelKomponenten' => $boolToJaNein($pvWp->individual_components_required),

        'note_zuwegungHeizraum' => $pvWp->access_heating_room,
        'note_t1Breite' => $pvWp->door1_width,
        'note_t1Hoehe' => $pvWp->door1_height,
        'note_t2Breite' => $pvWp->door2_width,
        'note_t2Hoehe' => $pvWp->door2_height,
        'note_t3Breite' => $pvWp->door3_width,
        'note_t3Hoehe' => $pvWp->door3_height,
        'note_t4Breite' => $pvWp->door4_width,
        'note_t4Hoehe' => $pvWp->door4_height,

        'note_treppen' => $boolToJaNein($pvWp->stairs_present),
        'note_treppenArt' => $pvWp->stairs_type,
        'note_treppenBreite' => $pvWp->stairs_width,

        'note_anschlussAussen' => $pvWp->outdoor_unit_connection,
        'heat_pump_pipe_length' => $pvWp->outdoor_connection_length ?: $pvWp->indoor_connection_length,

        'note_alternativeAufstellung' => $boolToJaNein($pvWp->alternative_placement_possible),
        'note_altBreite' => $pvWp->alt_access_width,
        'note_altHoehe' => $pvWp->alt_access_height,
        'note_altT1Breite' => $pvWp->alt_door1_width,
        'note_altT1Hoehe' => $pvWp->alt_door1_height,
        'note_altT2Breite' => $pvWp->alt_door2_width,
        'note_altT2Hoehe' => $pvWp->alt_door2_height,
        'note_altTreppen' => $boolToJaNein($pvWp->alt_stairs_present),
        'note_altTreppenArt' => $pvWp->alt_stairs_type,
        'note_altTreppenBreite' => $pvWp->alt_stairs_width,

        'length_ae_zs' => $pvWp->length_ae_zs,
        'length_ae_ie' => $pvWp->length_ae_ie,
        'length_ie_zs' => $pvWp->length_ie_zs,

        'wp_meter_present' => $boolToOneZero($pvWp->wp_meter_present),
        'wp_tariff_planned' => $boolToOneZero($pvWp->wp_tariff_planned),
        'lockout_time_1_start' => $pvWp->lockout_time_1_start,
        'lockout_time_1_end' => $pvWp->lockout_time_1_end,
        'lockout_time_2_start' => $pvWp->lockout_time_2_start,
        'lockout_time_2_end' => $pvWp->lockout_time_2_end,

        'note_kondenswasser' => $pvWp->condensate_ae,
        'drip_line_ie' => $pvWp->drip_line_ie,
        'trace_heating_cable_length' => $pvWp->trace_heating_cable_length,
        'groundwork' => $pvWp->foundation_by ?: $pvWp->earthworks_by_wp,
        'earthworks_by_wp' => $pvWp->earthworks_by_wp,
        'soakaway_by' => $pvWp->soakaway_by,

        'element_buffer' => $pvWp->element_buffer,
        'element_dhw' => $pvWp->element_dhw,
        'element_hkv' => $pvWp->element_hkv,
        'element_circulation' => $pvWp->element_circulation,

        'note_schallGebiet' => $pvWp->noise_area,
        'note_schallOrt' => $pvWp->noise_location,
        'note_schallAbschirmung' => $pvWp->noise_shielding,
        'note_schallImmissionOrt' => $pvWp->noise_immission_distance,
    ]);
};

/*
|--------------------------------------------------------------------------
| Main JS records
|--------------------------------------------------------------------------
*/

$measurementRecords = $measurements->map(function ($m) use ($decodeArray, $buildMaterialsFromSections, $buildMaterialsFromItems, $buildAlternativeData, $buildPvWpDetailData, $buildRoofData, $cleanData, $getEmployeeInfo) {
    $customer = $m->customer;
    $alternative = $m->alternative;
    $pvWp = optional($alternative)->pvWpDetail;

    $productText = strtolower(
        ($m->product_name ?? '') . ' ' .
        (optional($m->product)->article_group ?? '') . ' ' .
        (optional($m->product)->name ?? '') . ' ' .
        (optional($m->product)->title ?? '') . ' ' .
        (optional($m->product)->product ?? '') . ' ' .
        (optional($m->product)->model ?? '')
    );

    $productTextNormalized = mb_strtolower(strip_tags($productText), 'UTF-8');

    $productTextNormalized = str_replace(
        ['ä', 'ö', 'ü', 'ß'],
        ['ae', 'oe', 'ue', 'ss'],
        $productTextNormalized
    );

    $type = 'OTHER';

    if (
        str_contains($productTextNormalized, 'pv') ||
        str_contains($productTextNormalized, 'photovoltaik') ||
        str_contains($productTextNormalized, 'photovoltaic') ||
        str_contains($productTextNormalized, 'solar')
    ) {
        $type = 'PV';
    }

    if (
        str_contains($productTextNormalized, 'waermepumpe') ||
        str_contains($productTextNormalized, 'wärmepumpe') ||
        str_contains($productTextNormalized, 'heatpump') ||
        str_contains($productTextNormalized, 'heat pump') ||
        preg_match('/\bwp\b/u', $productTextNormalized)
    ) {
        $type = 'WP';
    }

    $customerName = trim(
        (optional($customer)->firma ?? '') . ' ' .
        (optional($customer)->name ?? '') . ' ' .
        (optional($customer)->lastname ?? '')
    );

    $productName = trim(
        ($m->product_name ?? '') . ' ' .
        (optional($m->product)->article_group ?? '') . ' ' .
        (optional($m->product)->name ?? '') . ' ' .
        (optional($m->product)->title ?? '') . ' ' .
        (optional($m->product)->product ?? '') . ' ' .
        (optional($m->product)->model ?? '')
    );

    $materials = [];

    if (!empty($m->materials_snapshot)) {
        $materials = $decodeArray($m->materials_snapshot);
    }

    if (empty($materials)) {
        $materials = $buildMaterialsFromItems($m->items);
    }

    if (empty($materials)) {
        $materials = $buildMaterialsFromSections($m->sections_snapshot);
    }

    if (empty($materials) && $m->detail) {
        $materials = $buildMaterialsFromSections($m->detail->sections);
    }

    $baseCustomerData = $cleanData([
        'firma' => optional($customer)->firma,
        'name' => optional($customer)->name,
        'lastname' => optional($customer)->lastname,
        'street' => optional($customer)->street,
        'postcode' => optional($customer)->postcode,
        'city' => optional($customer)->city,
        'email' => optional($customer)->email,
        'phone' => optional($customer)->phone,
        'telephone' => optional($customer)->telephone,

        'measurement_no' => $m->measurement_no ?? $m->number ?? null,
        'order_no' => $m->order_number ?? $m->auftrag_nr ?? null,
        'offer_no' => $m->offer_no ?? optional($m->offer)->offer_no ?? null,
        'product_name' => $productName,
    ]);

    $alternativeData = $buildAlternativeData($customer, $alternative);
    $pvWpData = $buildPvWpDetailData($pvWp);
    $roofData = $buildRoofData($alternative);

    $savedDetail = $m->editDetail;

    $savedFormData = $savedDetail?->form_data ?? [];
    $savedRoofData = $savedDetail?->roof_data ?? [];

    if (!is_array($savedFormData)) {
        $savedFormData = [];
    }

    if (!is_array($savedRoofData)) {
        $savedRoofData = [];
    }

    $formData = array_merge(
        $baseCustomerData,
        $alternativeData,
        $pvWpData,
        [
            'roofs' => $roofData,
        ],
        $savedFormData
    );

    if (!empty($savedRoofData)) {
        $formData['roofs'] = $savedRoofData;
    } elseif (!empty($savedFormData['roofs']) && is_array($savedFormData['roofs'])) {
        $formData['roofs'] = $savedFormData['roofs'];
    }

    $responsibleId = $savedDetail?->updated_by
        ?: $m->updated_by
        ?: $m->sent_by
        ?: $m->created_by;

    $responsible = $getEmployeeInfo($responsibleId);
    $creator = $getEmployeeInfo($m->created_by);
    $updater = $getEmployeeInfo($m->updated_by);

    /*
     * Responsible team:
     * - responsible employee from assignment
     * - employees attached to appointment
     * - employees attached to personal task
     * - fallback creator/updater
     */
    $teamMembers = collect();

    $pushTeamMember = function ($employeeId, string $role) use (&$teamMembers, $getEmployeeInfo) {
        if (blank($employeeId)) {
            return;
        }

        $employee = $getEmployeeInfo($employeeId);

        $teamMembers->push([
            'id' => (string) $employeeId,
            'name' => $employee['name'] ?? ('Mitarbeiter #' . $employeeId),
            'image' => $employee['image'] ?? asset('images/icons/placeholder.svg'),
            'role' => $role,
        ]);
    };

    $pushTeamMember($m->responsible_employee_id ?? null, 'Verantwortlich');
    $pushTeamMember($responsibleId, 'Bearbeitet');

    $appointment = null;

    if (!empty($m->appointment_id)) {
        $appointment = \App\Models\MainAppointment::query()
            ->with('employees')
            ->whereKey($m->appointment_id)
            ->first();
    }

    if (!$appointment) {
        $appointment = \App\Models\MainAppointment::query()
            ->with('employees')
            ->where('source', 'deal_measurement')
            ->where('other_id', $m->id)
            ->latest('id')
            ->first();
    }

    if ($appointment) {
        foreach ($appointment->employees ?? [] as $appointmentEmployee) {
            $pushTeamMember($appointmentEmployee->id ?? null, 'Termin');
        }
    }

    if (!empty($m->personal_task_id)) {
        $taskEmployees = \App\Models\EmployeesPersonalTask::query()
            ->where('task_id', $m->personal_task_id)
            ->pluck('employee_id');

        foreach ($taskEmployees as $taskEmployeeId) {
            $pushTeamMember($taskEmployeeId, 'Aufgabe');
        }
    }

    $pushTeamMember($m->created_by ?? null, 'Erstellt');

    $team = $teamMembers
        ->filter(fn ($member) => !empty($member['id']))
        ->unique('id')
        ->values()
        ->all();

    $notes = collect();

    if (filled($m->note ?? null)) {
        $notes->push([
            'id' => 'measurement-note-' . $m->id,
            'text' => (string) $m->note,
            'user' => $creator['name'] ?? 'System',
            'userId' => $m->created_by,
            'userImage' => $creator['image'] ?? asset('images/icons/placeholder.svg'),
            'date' => optional($m->created_at)->toISOString(),
        ]);
    }

    try {
        $historyNotes = $m->histories()
            ->where(function ($query) {
                $query->where('section', 'Notiz')
                    ->orWhere('action', 'like', '%Notiz%');
            })
            ->latest()
            ->limit(50)
            ->get();

        foreach ($historyNotes as $historyNote) {
            $changes = is_array($historyNote->changes) ? $historyNote->changes : [];
            $noteText = $changes['note']['new'] ?? $changes['note'] ?? $historyNote->new_value ?? null;

            if (is_array($noteText)) {
                $noteText = json_encode($noteText, JSON_UNESCAPED_UNICODE);
            }

            if (filled($noteText)) {
                $noteUser = $getEmployeeInfo($historyNote->created_by ?: $historyNote->user_id);

                $notes->push([
                    'id' => 'history-note-' . $historyNote->id,
                    'text' => (string) $noteText,
                    'user' => $noteUser['name'] ?? 'System',
                    'userId' => $historyNote->created_by ?: $historyNote->user_id,
                    'userImage' => $noteUser['image'] ?? asset('images/icons/placeholder.svg'),
                    'date' => optional($historyNote->created_at)->toISOString(),
                ]);
            }
        }
    } catch (\Throwable $e) {
        // keep page render safe even if history table is temporarily unavailable
    }

    $notes = $notes
        ->unique('id')
        ->values()
        ->all();

    $lastSavedAt = $savedDetail?->saved_at
        ?: $m->materials_saved_at
        ?: $m->updated_at
        ?: $m->created_at;

    $images = \App\Models\Image::query()
        ->where('stage', 'Feisnaufmass')
        ->when($m->customer_id, function ($query) use ($m) {
            $query->where('customer_id', $m->customer_id);
        })
        ->when($m->alternative_id, function ($query) use ($m) {
            $query->where('alternative_id', $m->alternative_id);
        })
        ->latest()
        ->get()
        ->map(function ($img) use ($getEmployeeInfo) {
            $uploader = $getEmployeeInfo($img->created_by);

            return [
                'id' => $img->id,
                'url' => asset($img->image),
                'path' => $img->image,
                'name' => $img->image_name,

                'uploadedById' => $img->created_by,
                'uploadedBy' => $uploader['name'],
                'uploadedByImage' => $uploader['image'],

                'uploadedAt' => optional($img->created_at)->toISOString(),
                'fileType' => $img->file_type,
                'status' => $img->status,
            ];
        })
        ->values();

    return [
        'id' => (string) $m->id,
        'dealId' => $m->deal_id,
        'type' => $type,
        'date' => optional($m->created_at)->toISOString(),
        'status' => $m->status ?? 'open',

        'measurementNo' => $m->measurement_no ?? $m->number ?? null,
        'orderNo' => $m->order_number ?? $m->auftrag_nr ?? null,
        'offerNo' => $m->offer_no ?? optional($m->offer)->offer_no ?? null,

        'customerName' => $customerName,
        'productName' => $productName,

        'customerId' => $m->customer_id ?: optional($customer)->id,
        'alternativeId' => $m->alternative_id ?: optional($alternative)->id,
        'productId' => $m->product_id ?: optional($m->product)->id,

        'createdById' => $m->created_by,
        'createdByName' => $creator['name'],
        'createdByImage' => $creator['image'],

        'updatedById' => $m->updated_by,
        'updatedByName' => $updater['name'],
        'updatedByImage' => $updater['image'],

        'responsibleId' => $responsibleId,
        'responsibleName' => $responsible['name'],
        'responsibleImage' => $responsible['image'],

        'team' => $team,
        'notes' => $notes,
        'noteCount' => count($notes),

        'lastSavedAt' => optional($lastSavedAt)->toISOString(),

        'materials' => $materials,
        'images' => $images,

        'history' => [
            [
                'action' => 'Feinaufmaß aus Controller geladen',
                'user' => 'System',
                'userId' => null,
                'userImage' => asset('images/icons/placeholder.svg'),
                'date' => optional($m->created_at)->toISOString(),
            ],
        ],

        'data' => $formData,
    ];
})->values();
@endphp
    <!-- JAVASCRIPT LOGIC -->
     <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    (() => {
        "use strict";

        // ---------------------------------------------------------------------
        // DATA STATE
        // ---------------------------------------------------------------------
        const currentUser = @json($currentEmployee['name'] ?? 'System');
        const currentUserId = @json($currentEmployee['id'] ?? null);
        const currentUserImage = @json($currentEmployee['image'] ?? asset('images/icons/placeholder.svg'));
        const employeeMap = @json($employeeMap ?? []);

        function getEmployeeFromMap(employeeId, fallbackName = "System") {
            const id = String(employeeId ?? "").trim();

            if (!id) {
                return {
                    id: null,
                    name: fallbackName || "System",
                    image: PLACEHOLDER_IMAGE
                };
            }

            if (employeeMap[id]) {
                return {
                    id: employeeMap[id].id ?? id,
                    name: employeeMap[id].name ?? `Mitarbeiter #${id}`,
                    image: employeeMap[id].image ?? PLACEHOLDER_IMAGE
                };
            }

            return {
                id,
                name: isNaN(Number(id)) ? id : `Mitarbeiter #${id}`,
                image: PLACEHOLDER_IMAGE
            };
        }

        const sampleMaterialData = [
            {
                id: "s1776933342479",
                title: "1. Hauptpositionen",
                items: [
                    {
                        name: "MODUL LONGI HI-MOS10 LR7-54HJD MIT 495 W",
                        img: "https://placehold.co/150x150/74b2d4/fff?text=Modul",
                        qty: 3,
                        unit: "Set",
                        subItems: [
                            {
                                name: "alpex T-Stück reduziert 32 x 20 x 32",
                                img: "https://placehold.co/80x80/cde8ea/000?text=T-Stueck",
                                qty: 3,
                                unit: "cm"
                            },
                            {
                                name: "alpex Übergang mit IG 20mm x 3/4&quot;",
                                img: "https://placehold.co/80x80/cde8ea/000?text=Uebergang",
                                qty: 2,
                                unit: "cm"
                            },
                            {
                                name: "Bosch Membran-Ausdehnungsgefäß 50 l MAC50",
                                img: "https://placehold.co/80x80/cde8ea/000?text=MAG50",
                                qty: 2,
                                unit: "cm"
                            }
                        ]
                    },
                    {
                        name: "Set Previums",
                        img: "https://placehold.co/150x150/74b2d4/fff?text=Set%20Previums",
                        qty: 2,
                        unit: "Set",
                        subItems: [
                            {
                                name: "NIBE Ladepumpe CPD 11-25/75",
                                img: "https://placehold.co/80x80/cde8ea/000?text=Ladepumpe",
                                qty: 2,
                                unit: "Stk"
                            },
                            {
                                name: "COSMO Hochleistungsspeicher HL300",
                                img: "https://placehold.co/80x80/cde8ea/000?text=HL300",
                                qty: 2,
                                unit: "Stk"
                            },
                            {
                                name: "COSMO Pufferspeicher Typ CPS 200",
                                img: "https://placehold.co/80x80/cde8ea/000?text=CPS200",
                                qty: 2,
                                unit: "Stk"
                            }
                        ]
                    }
                ]
            }
        ];

        const MATERIAL_SAVE_URL_TEMPLATE = "{{ route('deal-measurements.materials.save', ['measurement' => '__ID__']) }}";
        const DETAIL_SAVE_URL_TEMPLATE = "{{ route('deal-measurements.details.save', ['measurement' => '__ID__']) }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";
        const IMAGE_UPLOAD_URL_TEMPLATE = "{{ route('deal-measurements.images.upload', ['measurement' => '__ID__']) }}";
        const IMAGE_INDEX_URL_TEMPLATE = "{{ route('deal-measurements.images.index', ['measurement' => '__ID__']) }}";
        const IMAGE_DELETE_URL_TEMPLATE = "{{ route('deal-measurements.images.destroy', ['image' => '__ID__']) }}";
        const HISTORY_URL_TEMPLATE = "{{ route('deal-measurements.history', ['measurement' => '__ID__']) }}";
        const NOTE_STORE_URL_TEMPLATE = "{{ url('/deal-measurements/__ID__/notes') }}";
        const COMPLETE_URL_TEMPLATE = "{{ route('deal.measurements.complete', ['measurement' => '__ID__']) }}";  
        const UNLOCK_URL_TEMPLATE = "{{ route('deal.measurements.unlock', ['measurement' => '__ID__']) }}";
        const DELETE_URL_TEMPLATE = "{{ route('deal.measurements.destroy', ['measurement' => '__ID__']) }}";

        function getUnlockUrl(recordId) {
            return UNLOCK_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function getDeleteUrl(recordId) {
            return DELETE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }
        
        function getHistoryUrl(recordId) {
            return HISTORY_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function getNoteStoreUrl(recordId) {
            return NOTE_STORE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }   

        let materialSaveTimer = null;
        let materialSaving = false;


        const PRODUCT_SEARCH_URL = "{{ route('measurement-material.products.search') }}";
        const PLACEHOLDER_IMAGE = "{{ asset('images/icons/placeholder.svg') }}";
        const SELECT2_PLACEHOLDER = "Bitte wählen...";

        let records = @json($measurementRecords);

        let productPickerTarget = {
            catIdx: null,
            itemIdx: null,
            mode: "main"
        };

        let productSearchTimer = null;
        let currentPage = 1;
        const recordsPerPage = 10;

        let currentRoofIndex = 0;
        let currentRecordIdForMaterials = null;
        let currentRecordIdForImages = null;
        let currentRecordIdForHistory = null;
        let currentRecordIdForNotes = null;

        let recognition = null;
        let isRecording = false;
        let activeInputId = null;

        // ---------------------------------------------------------------------
        // SMALL HELPERS
        // ---------------------------------------------------------------------

        function getImageUploadUrl(recordId) {
            return IMAGE_UPLOAD_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function getImageIndexUrl(recordId) {
            return IMAGE_INDEX_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function getImageDeleteUrl(imageId) {
            return IMAGE_DELETE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(imageId));
        }
        

        function esc(value) {
            return String(value ?? "")
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#039;");
        }
        function cleanHistoryText(value, fallback = "") {
            if (value === null || value === undefined) {
                return fallback;
            }

            if (typeof value === "object") {
                try {
                    return JSON.stringify(value);
                } catch (error) {
                    return fallback;
                }
            }

            return String(value)
                .replace(/<[^>]*>/g, "")
                .replace(/&nbsp;/g, " ")
                .replace(/&amp;/g, "&")
                .replace(/&lt;/g, "<")
                .replace(/&gt;/g, ">")
                .replace(/&quot;/g, '"')
                .replace(/&#039;/g, "'")
                .trim();
        }

        function historyValue(value, fallback = "") {
            const cleaned = cleanHistoryText(value, fallback);

            if (!cleaned) {
                return esc(fallback);
            }

            return esc(cleaned);
        }
        function cssEscape(value) {
            if (window.CSS && typeof window.CSS.escape === "function") {
                return window.CSS.escape(value);
            }

            return String(value).replace(/["\\]/g, "\\$&");
        }

        function toNumber(value) {
            const num = parseFloat(String(value ?? "").replace(",", "."));
            return Number.isFinite(num) ? num : 0;
        }

        function formatQty(value) {
            const num = toNumber(value);

            if (Number.isInteger(num)) {
                return String(num);
            }

            return num.toFixed(2).replace(/\.?0+$/, "");
        }

        function getRecord(id) {
            return records.find(record => String(record.id) === String(id));
        }

        function getFormType(formId) {
            return formId === "form-pv" ? "PV" : "WP";
        }

        function dispatchNativeEvents(el) {
            el.dispatchEvent(new Event("input", { bubbles: true }));
            el.dispatchEvent(new Event("change", { bubbles: true }));
        }

        // ---------------------------------------------------------------------
        // SELECT2
        // ---------------------------------------------------------------------
        function hasSelect2() {
            return typeof window.$ !== "undefined" && !!$.fn && !!$.fn.select2;
        }

        function getSelect2DropdownParent(select) {
            const fixedParent = select.closest(".fixed");
            const modalParent = select.closest('[id^="modal-"]');

            if (fixedParent) {
                return $(fixedParent);
            }

            if (modalParent) {
                return $(modalParent);
            }

            return $(document.body);
        }

        function initNormalSelect2(scope = document) {
            if (!hasSelect2()) {
                return;
            }

            const root = scope instanceof HTMLElement ? scope : document;

            root.querySelectorAll("select.select2-field, select.sa-native-select2").forEach(select => {
                const $select = $(select);

                if ($select.hasClass("select2-hidden-accessible")) {
                    return;
                }

                $select.select2({
                    width: "100%",
                    placeholder: select.getAttribute("data-placeholder") || SELECT2_PLACEHOLDER,
                    allowClear: !select.required,
                    dropdownParent: getSelect2DropdownParent(select)
                });

                $select.on("change", function () {
                    const form = this.closest("form");
                    if (form) {
                        updateFormProgress(getFormType(form.id));
                    }
                });
            });
        }

        function getCleanRadioLabel(input) {
            const label = input.closest("label");

            if (!label) {
                return input.value;
            }

            const clone = label.cloneNode(true);
            const clonedInput = clone.querySelector("input");

            if (clonedInput) {
                clonedInput.remove();
            }

            return clone.textContent.trim() || input.value;
        }

        function getRadioGroupTitle(groupName, radios) {
            const firstRadio = radios[0];

            const fieldWrapper = firstRadio.closest(
                ".mb-4, .bg-slate-50, .bg-white, .p-4, .flex, .grid, .space-y-3, .space-y-4, .border, .rounded-xl"
            );

            if (fieldWrapper) {
                const title = fieldWrapper.querySelector(
                    "label.block, h4, h5, span.font-bold, span.font-medium, .text-xs.font-bold"
                );

                if (title) {
                    return title.textContent
                        .replace("*", "")
                        .replace(":", "")
                        .trim();
                }
            }

            return groupName
                .replaceAll("_", " ")
                .replace(/^note /, "")
                .replace(/\b\w/g, char => char.toUpperCase());
        }

        function initRadioGroupsAsSelect2(scope = document) {
            if (!hasSelect2()) {
                console.warn("Select2 wurde nicht geladen.");
                return;
            }

            const root = scope instanceof HTMLElement ? scope : document;

            const radios = Array.from(
                root.querySelectorAll('input[type="radio"]:not([data-keep-radio]):not([data-select2-ready])')
            );

            const groups = {};

            radios.forEach(radio => {
                if (!radio.name) return;

                const form = radio.closest("form");
                const formKey = form ? form.id : "global";
                const key = `${formKey}::${radio.name}`;

                if (!groups[key]) {
                    groups[key] = [];
                }

                groups[key].push(radio);
            });

            Object.values(groups).forEach(groupRadios => {
                if (!groupRadios.length) return;

                const firstRadio = groupRadios[0];
                const groupName = firstRadio.name;
                const form = firstRadio.closest("form");
                const selected = groupRadios.find(radio => radio.checked);
                const required = groupRadios.some(radio => radio.required);
                const title = getRadioGroupTitle(groupName, groupRadios);

                const existingWrapper = form
                    ? form.querySelector(`[data-radio-select-name="${cssEscape(groupName)}"]`)
                    : document.querySelector(`[data-radio-select-name="${cssEscape(groupName)}"]`);

                if (existingWrapper) {
                    groupRadios.forEach(radio => {
                        radio.dataset.select2Ready = "1";
                    });
                    return;
                }

                const options = groupRadios.map(radio => ({
                    value: radio.value,
                    text: getCleanRadioLabel(radio)
                }));

                const wrapper = document.createElement("div");
                wrapper.className = "sa-select2-radio-wrapper my-2";
                wrapper.setAttribute("data-radio-select-name", groupName);

                wrapper.innerHTML = `
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                        ${esc(title)} ${required ? '<span class="text-red-500">*</span>' : ""}
                    </label>

                    <select
                        class="sa-radio-select2 w-full"
                        ${required ? "required" : ""}
                        data-radio-target="${esc(groupName)}">
                        <option value="">${SELECT2_PLACEHOLDER}</option>
                        ${options.map(option => `
                            <option value="${esc(option.value)}" ${selected && String(selected.value) === String(option.value) ? "selected" : ""}>
                                ${esc(option.text)}
                            </option>
                        `).join("")}
                    </select>
                `;

                const visualParent =
                    firstRadio.closest(".grid, .flex, .flex-wrap, .space-y-3, .space-y-4") ||
                    firstRadio.parentElement;

                if (!visualParent) {
                    return;
                }

                visualParent.insertAdjacentElement("beforebegin", wrapper);
                visualParent.classList.add("sa-radio-original-hidden");

                const select = wrapper.querySelector("select");

                $(select).select2({
                    width: "100%",
                    placeholder: SELECT2_PLACEHOLDER,
                    allowClear: !required,
                    dropdownParent: getSelect2DropdownParent(select)
                });

                $(select).on("change", function () {
                    const value = this.value;

                    groupRadios.forEach(radio => {
                        radio.checked = String(radio.value) === String(value);
                        radio.dispatchEvent(new Event("change", { bubbles: true }));
                    });

                    const relatedForm = this.closest("form");

                    if (relatedForm) {
                        updateFormProgress(getFormType(relatedForm.id));
                    }
                });

                groupRadios.forEach(radio => {
                    radio.dataset.select2Ready = "1";
                });
            });
        }

        function refreshRadioSelect2Mirrors(scope = document) {
            if (!hasSelect2()) {
                return;
            }

            const root = scope instanceof HTMLElement ? scope : document;

            root.querySelectorAll(".sa-select2-radio-wrapper select[data-radio-target]").forEach(select => {
                const groupName = select.dataset.radioTarget;
                const form = select.closest("form");

                const checkedRadio = form
                    ? form.querySelector(`input[type="radio"][name="${cssEscape(groupName)}"]:checked`)
                    : document.querySelector(`input[type="radio"][name="${cssEscape(groupName)}"]:checked`);

                const value = checkedRadio ? checkedRadio.value : "";

                $(select).val(value).trigger("change.select2");
            });
        }

        function refreshNormalSelect2(scope = document) {
            if (!hasSelect2()) {
                return;
            }

            const root = scope instanceof HTMLElement ? scope : document;

            root.querySelectorAll("select.select2-field, select.sa-native-select2").forEach(select => {
                if ($(select).hasClass("select2-hidden-accessible")) {
                    $(select).trigger("change.select2");
                }
            });
        }

        function initAllSelect2(scope = document) {
            initNormalSelect2(scope);
            initRadioGroupsAsSelect2(scope);
            refreshRadioSelect2Mirrors(scope);
            refreshNormalSelect2(scope);
        }

        window.addEventListener("offline", () => {
            showImageUploadStatus("Keine Internetverbindung. Neue Bilder werden lokal gespeichert.", "warning");
        });

        window.addEventListener("online", async () => {
            showImageUploadStatus("Internet ist zurück. Offline-Bilder werden hochgeladen...", "info");
            await processOfflineImageQueue();
        });

        async function processOfflineImageQueue() {
    if (!navigator.onLine) {
        return;
    }

    let queuedImages = [];

    try {
        queuedImages = await getOfflineImages();
    } catch (error) {
        console.error(error);
        return;
    }

    if (!queuedImages.length) {
        return;
    }

    let uploadedCount = 0;

    for (const queued of queuedImages) {
        try {
            const fileKey = `offline_${queued.id}`;

            const uploadedImage = await uploadImageWithProgress(
                queued.recordId,
                queued.file,
                fileKey
            );

            await deleteOfflineImage(queued.id);

            const record = getRecord(queued.recordId);

            if (record) {
                record.images = Array.isArray(record.images) ? record.images : [];

                record.images = record.images.filter(img => {
                    return !(img.offline && img.name === queued.fileName);
                });

                record.images.unshift(uploadedImage);

                addHistory(queued.recordId, `Offline-Foto automatisch hochgeladen: ${queued.fileName}`);

                if (String(currentRecordIdForImages) === String(queued.recordId)) {
                    renderImages();
                }
            }

            uploadedCount++;
        } catch (error) {
            console.error("Offline upload failed:", error);
        }
    }

    if (uploadedCount > 0) {
        showImageUploadStatus(`${uploadedCount} Offline-Bild(er) wurden hochgeladen.`, "success");
    }
}
        // ---------------------------------------------------------------------
        // INIT
        // ---------------------------------------------------------------------
        document.addEventListener("DOMContentLoaded", () => {
            renderList();
            initProgressListeners();

            initAllSelect2(document.getElementById("form-pv"));
            initAllSelect2(document.getElementById("form-wp"));

            initSpeechRecognition();
            processOfflineImageQueue();
        });

        // ---------------------------------------------------------------------
        // HISTORY
        // ---------------------------------------------------------------------

        function normalizeRecordNotes(record) {
            record.notes = Array.isArray(record.notes) ? record.notes : [];
            record.noteCount = record.notes.length;
            return record.notes;
        }

        function renderNoteList(record) {
            const container = document.getElementById("notes-list-container");

            if (!container) {
                return;
            }

            const notes = normalizeRecordNotes(record);

            if (!notes.length) {
                container.innerHTML = `
                    <div class="text-center py-12 text-slate-400">
                        <i class="ph ph-note text-5xl"></i>
                        <p class="mt-2 font-semibold">Noch keine Notizen vorhanden.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = notes.map(note => {
                const dateStr = note.date ? new Date(note.date).toLocaleString("de-DE") : "-";
                const employee = getEmployeeFromMap(note.userId, note.user || "System");
                const author = note.user || employee.name || "System";
                const image = note.userImage || employee.image || PLACEHOLDER_IMAGE;

                return `
                    <div class="sa-note-card">
                        <div class="sa-note-head">
                            <div class="sa-note-author">
                                <img src="${esc(image)}" onerror="this.onerror=null; this.src='${PLACEHOLDER_IMAGE}'" alt="">
                                <span>${esc(author)}</span>
                            </div>
                            <div class="sa-note-date">${esc(dateStr)}</div>
                        </div>
                        <div class="sa-note-body">${esc(note.text || note.note || "")}</div>
                    </div>
                `;
            }).join("");
        }

        window.openNotes = function openNotes(id) {
            currentRecordIdForNotes = id;

            const record = getRecord(id);
            if (!record) return;

            const fullName = `${record.data?.firma || ""} ${record.data?.name || ""} ${record.data?.lastname || ""}`.trim()
                || record.customerName
                || "Unbenannt";

            const title = document.getElementById("notes-project-name");
            if (title) {
                title.innerText = `Projekt: ${fullName}`;
            }

            const textArea = document.getElementById("note-create-text");
            if (textArea) {
                textArea.value = "";
            }

            renderNoteList(record);
            openModal("modal-notes", "modal-notes-content");
        };

        window.closeNotes = function closeNotes() {
            closeModal("modal-notes", "modal-notes-content");
        };

        window.saveMeasurementNote = async function saveMeasurementNote(event) {
            event.preventDefault();

            const record = getRecord(currentRecordIdForNotes);
            const textArea = document.getElementById("note-create-text");
            const button = document.getElementById("note-create-btn");

            if (!record || !textArea) {
                return;
            }

            const note = (textArea.value || "").trim();

            if (!note) {
                alert("Bitte zuerst eine Notiz schreiben.");
                return;
            }

            const oldHtml = button ? button.innerHTML : "";

            if (button) {
                button.disabled = true;
                button.innerHTML = `<i class="ph-bold ph-spinner-gap animate-spin text-lg"></i> Speichern...`;
            }

            try {
                const response = await fetch(getNoteStoreUrl(record.id), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    },
                    body: JSON.stringify({ note })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Notiz konnte nicht gespeichert werden.");
                }

                const newNote = data.note || {
                    id: `local-${Date.now()}`,
                    text: note,
                    user: currentUser,
                    userId: currentUserId,
                    userImage: currentUserImage,
                    date: new Date().toISOString()
                };

                record.notes = Array.isArray(record.notes) ? record.notes : [];
                record.notes.unshift(newNote);
                record.noteCount = record.notes.length;

                addHistory(record.id, "Notiz hinzugefügt");

                textArea.value = "";
                renderNoteList(record);
                renderList();
            } catch (error) {
                console.error(error);
                alert(error.message || "Notiz konnte nicht gespeichert werden.");
            } finally {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = oldHtml;
                }
            }
        };

        function addHistory(recordId, action) {
            const record = getRecord(recordId);

            if (!record) return;

            record.history = record.history || [];

            record.history.unshift({
                action: cleanHistoryText(action),
                user: currentUser,
                userId: currentUserId,
                userImage: currentUserImage,
                date: new Date().toISOString()
            });
        }

        window.openHistory = async function openHistory(id) {
            currentRecordIdForHistory = id;

            const record = getRecord(id);
            if (!record) return;

            const fullName = `${record.data?.firma || ""} ${record.data?.name || ""} ${record.data?.lastname || ""}`.trim();

            document.getElementById("history-project-name").innerText = `Projekt: ${fullName || "Unbenannt"}`;

            const listContainer = document.getElementById("history-list-container");

            listContainer.innerHTML = `
                <div class="text-center py-10 text-slate-400">
                    <i class="ph ph-spinner-gap text-5xl animate-spin"></i>
                    <p class="mt-2 font-semibold">Historie wird geladen...</p>
                </div>
            `;

            openModal("modal-history", "modal-history-content");

            try {
                const response = await fetch(getHistoryUrl(id), {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Historie konnte nicht geladen werden.");
                }

                record.history = data.history || [];
                renderHistoryList(record.history);
            } catch (error) {
                console.error(error);

                listContainer.innerHTML = `
                    <div class="text-center py-10 text-red-400">
                        <i class="ph ph-warning-circle text-5xl"></i>
                        <p class="mt-2 font-semibold">${esc(error.message || "Historie konnte nicht geladen werden.")}</p>
                    </div>
                `;
            }
        };

        function normalizeHistoryChange(change) {
            if (change === null || change === undefined) {
                return { old: "-", new: "-" };
            }

            if (typeof change !== "object" || Array.isArray(change)) {
                return { old: "-", new: change };
            }

            if (Object.prototype.hasOwnProperty.call(change, "old") || Object.prototype.hasOwnProperty.call(change, "new")) {
                return {
                    old: change.old ?? "-",
                    new: change.new ?? "-"
                };
            }

            if (Object.prototype.hasOwnProperty.call(change, "oldValue") || Object.prototype.hasOwnProperty.call(change, "newValue")) {
                return {
                    old: change.oldValue ?? "-",
                    new: change.newValue ?? "-"
                };
            }

            return {
                old: "-",
                new: change
            };
        }

        function renderHistoryChanges(item) {
            const parts = [];

            if (item && item.changes && typeof item.changes === "object" && !Array.isArray(item.changes)) {
                Object.entries(item.changes).forEach(([field, change]) => {
                    if (field === "old" && item.changes.new && typeof item.changes.new === "object") {
                        Object.entries(item.changes.new).forEach(([nestedField, newValue]) => {
                            const oldValue = item.changes.old && typeof item.changes.old === "object"
                                ? item.changes.old[nestedField]
                                : "-";

                            parts.push({
                                field: nestedField,
                                old: oldValue ?? "-",
                                new: newValue ?? "-"
                            });
                        });
                        return;
                    }

                    if (field === "new" && item.changes.old && typeof item.changes.old === "object") {
                        return;
                    }

                    if (field === "description") {
                        parts.push({
                            field: "Beschreibung",
                            old: "-",
                            new: change ?? "-"
                        });
                        return;
                    }

                    const normalized = normalizeHistoryChange(change);

                    parts.push({
                        field,
                        old: normalized.old,
                        new: normalized.new
                    });
                });
            } else if (item && item.field) {
                parts.push({
                    field: item.field,
                    old: item.oldValue ?? item.old_value ?? "-",
                    new: item.newValue ?? item.new_value ?? "-"
                });
            }

            if (!parts.length) {
                return "";
            }

            return `
                <div class="mt-2 space-y-1">
                    ${parts.map(change => `
                        <div class="bg-white border border-slate-200 rounded-xl p-2 text-xs">
                            <p class="font-black text-slate-700">${historyValue(change.field)}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                                <div class="bg-red-50 border border-red-100 rounded-lg p-2">
                                    <span class="font-bold text-red-500">Alt:</span>
                                    <span class="text-slate-600">${historyValue(change.old, "-")}</span>
                                </div>
                                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2">
                                    <span class="font-bold text-emerald-600">Neu:</span>
                                    <span class="text-slate-600">${historyValue(change.new, "-")}</span>
                                </div>
                            </div>
                        </div>
                    `).join("")}
                </div>
            `;
        }

        function renderHistoryList(history) {
            const listContainer = document.getElementById("history-list-container");

            if (!listContainer) {
                return;
            }

            listContainer.innerHTML = "";

            if (!Array.isArray(history) || history.length === 0) {
                listContainer.innerHTML = `<p class="text-center text-slate-500 py-10">Keine Historie vorhanden.</p>`;
                return;
            }

            let html = '<div class="relative border-l-2 border-brand-blue/30 ml-3 space-y-6 my-4">';

            history.forEach(item => {
                item = item || {};

                const dateStr = item.date ? new Date(item.date).toLocaleString("de-DE") : "-";
                const rawUserId = item.userId || item.employee_id || item.updated_by || item.created_by || item.user;
                const employee = getEmployeeFromMap(rawUserId, item.user || "System");

                const historyUser = cleanHistoryText(
                    item.user_name ||
                    item.employeeName ||
                    item.updatedByName ||
                    employee.name ||
                    "System"
                ) || "System";

                const changesHtml = renderHistoryChanges(item);

                html += `
                    <div class="relative pl-6">
                        <div class="absolute -left-[9px] top-1 w-4 h-4 bg-brand-blue rounded-full border-2 border-white shadow-sm"></div>

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <p class="text-sm font-black text-slate-800">${historyValue(item.action, "Änderung")}</p>
                                ${item.section ? `<span class="text-[10px] px-2 py-0.5 rounded-full bg-brand-lightBlue/50 text-brand-blue font-black border border-brand-blue/20">${historyValue(item.section)}</span>` : ""}
                            </div>

                            <p class="text-xs text-slate-500 mt-0.5">
                                <i class="ph-fill ph-user"></i> ${historyValue(historyUser, "System")}
                                &nbsp;&bull;&nbsp;
                                <i class="ph-fill ph-clock"></i> ${esc(dateStr)}
                            </p>

                            ${changesHtml}
                        </div>
                    </div>
                `;
            });

            html += "</div>";
            listContainer.innerHTML = html;
        }
        window.closeHistory = function closeHistory() {
            closeModal("modal-history", "modal-history-content");
        };

        // ---------------------------------------------------------------------
        // MODALS
        // ---------------------------------------------------------------------
        function openModal(modalId, contentId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(contentId);

            if (!modal || !content) return;

            modal.classList.remove("hidden");
            void modal.offsetWidth;
            modal.classList.remove("opacity-0");
            modal.classList.add("opacity-100");
            content.classList.remove("scale-95");
            content.classList.add("scale-100");
        }

        function closeModal(modalId, contentId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(contentId);

            if (!modal || !content) return;

            modal.classList.remove("opacity-100");
            modal.classList.add("opacity-0");
            content.classList.remove("scale-100");
            content.classList.add("scale-95");

            setTimeout(() => {
                modal.classList.add("hidden");
            }, 300);
        }

        window.showTypeSelection = function showTypeSelection() {
            openModal("modal-select-type", "modal-content");
        };

        window.closeTypeSelection = function closeTypeSelection() {
            closeModal("modal-select-type", "modal-content");
        };

        // ---------------------------------------------------------------------
        // SPEECH TO TEXT
        // ---------------------------------------------------------------------
        function initSpeechRecognition() {
            if (!("webkitSpeechRecognition" in window) && !("SpeechRecognition" in window)) {
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = false;
            recognition.lang = "de-DE";

            recognition.onresult = function (event) {
                let finalTranscript = "";

                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript + " ";
                    }
                }

                if (activeInputId && finalTranscript) {
                    const input = document.getElementById(activeInputId);

                    if (input) {
                        input.value += (input.value ? " " : "") + finalTranscript.trim();
                        dispatchNativeEvents(input);
                    }
                }
            };

            recognition.onend = function () {
                resetMicIcons();
                isRecording = false;
                activeInputId = null;
            };

            recognition.onerror = function (event) {
                console.error("Spracherkennung Fehler:", event.error);
                resetMicIcons();
                isRecording = false;
                activeInputId = null;
            };
        }

        window.toggleDictation = function toggleDictation(inputId, iconId) {
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
                    icon.classList.remove("ph-microphone", "text-slate-400");
                    icon.classList.add("ph-stop-circle", "text-red-500", "animate-pulse");
                }
            } catch (error) {
                console.error("Konnte Spracherkennung nicht starten:", error);
            }
        };

        function resetMicIcons() {
            ["pv-mic-icon", "wp-mic-icon"].forEach(id => {
                const icon = document.getElementById(id);

                if (!icon) return;

                icon.classList.remove("ph-stop-circle", "text-red-500", "animate-pulse");
                icon.classList.add("ph-microphone", "text-slate-400");
            });
        }

        // ---------------------------------------------------------------------
        // SIDEBAR / UI
        // ---------------------------------------------------------------------
        window.toggleSidebar = function toggleSidebar(type) {
            const sidebar = document.getElementById(`${type}-sidebar`);
            const title = document.getElementById(`${type}-sidebar-title`);
            const icon = document.getElementById(`${type}-sidebar-icon`);

            if (!sidebar || !title || !icon) return;

            const texts = sidebar.querySelectorAll(`div[id^="${type}-navtext-"]`);

            if (sidebar.classList.contains("w-64")) {
                sidebar.classList.replace("w-64", "w-20");
                title.classList.add("opacity-0", "hidden");
                icon.classList.replace("ph-caret-left", "ph-caret-right");
                texts.forEach(text => text.classList.add("opacity-0", "hidden"));
            } else {
                sidebar.classList.replace("w-20", "w-64");
                title.classList.remove("opacity-0", "hidden");
                icon.classList.replace("ph-caret-right", "ph-caret-left");
                texts.forEach(text => text.classList.remove("opacity-0", "hidden"));
            }
        };

        window.toggleSection = function toggleSection(contentId, iconId) {
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);

            if (!content || !icon) return;

            if (content.classList.contains("hidden")) {
                content.classList.remove("hidden");
                icon.classList.remove("rotate-180");
            } else {
                content.classList.add("hidden");
                icon.classList.add("rotate-180");
            }
        };

        window.scrollToSection = function scrollToSection(id) {
            const el = document.getElementById(id);
            if (!el) return;

            const contentId = id.replace("-sec-", "-content-");
            const iconId = id.replace("-sec-", "-icon-");
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);

            if (content && content.classList.contains("hidden")) {
                content.classList.remove("hidden");
                if (icon) icon.classList.remove("rotate-180");
            }

            el.scrollIntoView({ behavior: "smooth", block: "start" });
        };

        // ---------------------------------------------------------------------
        // PROGRESS
        // ---------------------------------------------------------------------
        function initProgressListeners() {
            ["form-pv", "form-wp"].forEach(formId => {
                const form = document.getElementById(formId);

                if (!form) return;

                form.addEventListener("input", () => updateFormProgress(getFormType(formId)));
                form.addEventListener("change", () => updateFormProgress(getFormType(formId)));
            });
        }

        function updateFormProgress(type) {
            const form = document.getElementById(`form-${type.toLowerCase()}`);

            if (!form) return;

            const sections = form.querySelectorAll("[data-section]");
            let totalReqFields = 0;
            let filledReqFields = 0;

            sections.forEach(section => {
                const secId = section.getAttribute("data-section");
                const reqElements = Array.from(
                    section.querySelectorAll("input[required], select[required], textarea[required]")
                ).filter(el => !el.closest(".sa-radio-original-hidden"));

                const reqGroups = {};

                reqElements.forEach(el => {
                    if (!el.name) return;

                    if (!reqGroups[el.name]) {
                        reqGroups[el.name] = [];
                    }

                    reqGroups[el.name].push(el);
                });

                const secTotal = Object.keys(reqGroups).length;
                let secFilled = 0;

                Object.keys(reqGroups).forEach(name => {
                    const elements = reqGroups[name];
                    let isFilled = false;

                    elements.forEach(el => {
                        if (el.type === "radio" || el.type === "checkbox") {
                            if (el.checked) isFilled = true;
                        } else if (String(el.value ?? "").trim() !== "") {
                            isFilled = true;
                        }
                    });

                    if (isFilled) {
                        secFilled++;
                    }
                });

                totalReqFields += secTotal;
                filledReqFields += secFilled;

                const navDot = document.getElementById(`${type.toLowerCase()}-nav-${secId}`);
                const navCount = document.getElementById(`${type.toLowerCase()}-navcount-${secId}`);

                if (navDot) {
                    if (secTotal > 0 && secFilled === secTotal) {
                        const color = type === "PV" ? "bg-brand-orange" : "bg-brand-green";
                        navDot.className = `w-6 h-6 rounded-full ${color} text-white border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors shadow-sm`;
                        navDot.innerHTML = '<i class="ph-bold ph-check text-xs"></i>';
                    } else {
                        navDot.className = "w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center z-10 shrink-0 transition-colors";
                        navDot.innerHTML = "";
                    }
                }

                if (navCount) {
                    if (secTotal > 0) {
                        navCount.textContent = `${secFilled}/${secTotal}`;

                        if (secFilled === secTotal) {
                            const bgClass = type === "PV" ? "bg-brand-orange/10" : "bg-brand-green/10";
                            const textClass = type === "PV" ? "text-brand-orange" : "text-brand-green";
                            const borderClass = type === "PV" ? "border-brand-orange/30" : "border-brand-green/30";

                            navCount.className = `text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors ${bgClass} ${textClass} ${borderClass}`;
                        } else {
                            navCount.className = "text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors bg-slate-100 text-slate-500 border-slate-200";
                        }
                    } else {
                        navCount.textContent = "Opt";
                        navCount.className = "text-[10px] font-bold px-1.5 py-0.5 rounded border transition-colors bg-slate-50 text-slate-400 border-slate-100";
                    }
                }
            });

            const percentage = totalReqFields === 0
                ? 100
                : Math.round((filledReqFields / totalReqFields) * 100);

            const fillEl = document.getElementById(`${type.toLowerCase()}-progress-fill`);
            const textEl = document.getElementById(`${type.toLowerCase()}-progress-text`);

            if (fillEl) fillEl.style.width = `${percentage}%`;
            if (textEl) textEl.innerText = `${percentage}%`;
        }

        // expose because inline handlers use it
        window.updateFormProgress = updateFormProgress;

        // ---------------------------------------------------------------------
        // DYNAMIC ROOFS
        // ---------------------------------------------------------------------
        window.addRoofUI = function addRoofUI(roofData = null, isCompleted = false) {
            const container = document.getElementById("roofs-container");

            if (!container) return;

            const idx = currentRoofIndex++;

            const html = `
                <div class="roof-entry border border-brand-lightBlue/50 rounded-2xl p-4 bg-slate-50 relative" data-index="${idx}">
                    <button type="button"
                            onclick="this.closest('.roof-entry').remove(); updateFormProgress('PV');"
                            class="absolute top-4 right-4 text-red-500 hover:bg-red-100 p-2 rounded-lg transition ${isCompleted ? 'hidden' : ''}"
                            title="Dach löschen">
                        <i class="ph-bold ph-trash text-lg"></i>
                    </button>
                    <h4 class="font-bold text-brand-blue mb-4 text-lg border-b border-brand-lightBlue/30 pb-2 pr-10">
                        Dachfläche ${idx + 1}
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dachform <span class="text-red-500">*</span></label>
                            <select name="roof_${idx}_roof_type" required class="select2-field w-full p-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                <option value="">Bitte wählen...</option>
                                <option value="Satteldach">Satteldach</option>
                                <option value="Walmdach">Walmdach</option>
                                <option value="Flachdach">Flachdach</option>
                                <option value="Pultdach">Pultdach</option>
                                <option value="Carport">Carport</option>
                                <option value="mehrere">Mehrere Dachflächen</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Höhe Traufe (m) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.1" name="roof_${idx}_roof_height" required class="w-full p-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                        </div>
                    </div>

                    <div class="mt-4 bg-white p-4 border border-slate-200 rounded-xl">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dacheindeckung</label>
                                <select name="roof_${idx}_roof_covering" class="select2-field w-full p-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-brand-orange">
                                    <option value="">Bitte wählen...</option>
                                    <option value="Ziegel">Ziegel</option>
                                    <option value="Schiefer">Schiefer</option>
                                    <option value="Biberschwanz">Biberschwanz</option>
                                    <option value="Trapezblech">Trapezblech</option>
                                    <option value="Stehfalz">Stehfalz</option>
                                    <option value="Welleternit">Welleternit</option>
                                    <option value="Beton">Beton</option>
                                    <option value="Ton">Ton</option>
                                    <option value="Bitumen">Bitumen</option>
                                    <option value="Folie">Folie</option>
                                    <option value="Kies">Kies</option>
                                    <option value="Gründach">Gründach</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <label class="w-full flex items-center gap-2 cursor-pointer border rounded-xl bg-slate-50 p-3">
                                    <input type="checkbox" name="roof_${idx}_solar_holding_tile_desired" value="1" class="custom-cb focus:ring-brand-orange">
                                    <span class="text-sm font-bold text-slate-700">Solarhalteziegel geplant</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 border border-brand-orange/30 p-3 rounded-lg bg-brand-orange/5">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-3">Details Ziegel / Pfanne</h5>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Modell</label>
                                    <select name="roof_${idx}_roof_covering_model" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-white">
                                        <option value="">Bitte wählen...</option>
                                        <option value="2 Wellen">2 Wellen</option>
                                        <option value="1 Welle">1 Welle</option>
                                        <option value="Flachziegel">Flachziegel</option>
                                        <option value="Schiefer">Schiefer</option>
                                        <option value="Biberschwanz">Biberschwanz</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Farbe</label>
                                    <select name="roof_${idx}_note_ziegelFarbe" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-white">
                                        <option value="">Bitte wählen...</option>
                                        <option value="schwarz">Schwarz</option>
                                        <option value="anthrazit">Anthrazit</option>
                                        <option value="hellgrau">Hellgrau</option>
                                        <option value="rot">Rot</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Finish</label>
                                    <select name="roof_${idx}_note_ziegelFinish" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-white">
                                        <option value="">Bitte wählen...</option>
                                        <option value="glasiert">Glasiert</option>
                                        <option value="matt">Matt</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Eindeckmaß B/H</label>
                                    <input type="text" name="roof_${idx}_roof_covering_dimensions_cm" placeholder="B x H in cm" class="w-full p-2 border rounded-lg text-sm outline-none bg-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Hersteller / Bezeichnung</label>
                                    <input type="text" name="roof_${idx}_roof_covering_company" placeholder="Bezeichnung / Hersteller" class="w-full p-2 border rounded-lg text-sm outline-none bg-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Vorrätige Ziegel</label>
                                    <input type="number" name="roof_${idx}_note_ziegelVorrat" class="w-full p-2 border rounded-lg text-sm outline-none bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 border border-slate-200 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-3">Sparren & Dämmung</h5>

                            <div class="grid grid-cols-1 gap-3">
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 border rounded-lg p-2">
                                    <input type="checkbox" name="roof_${idx}_rafter_reinforcement_needed" value="1" class="custom-cb focus:ring-brand-orange">
                                    <span class="text-sm">Verstärkung nötig</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 border rounded-lg p-2">
                                    <input type="checkbox" name="roof_${idx}_note_denkmalschutz" value="1" class="custom-cb focus:ring-brand-orange">
                                    <span class="text-sm">Denkmalschutz</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 border rounded-lg p-2">
                                    <input type="checkbox" name="roof_${idx}_note_natursparren" value="1" class="custom-cb focus:ring-brand-orange">
                                    <span class="text-sm">Natur-Sparren</span>
                                </label>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Sparrenstärke</label>
                                    <input type="number" step="0.1" name="roof_${idx}_rafter_thickness" class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Dämmung</label>
                                    <select name="roof_${idx}_between_rafter_insulation" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-slate-50">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Zwischensparren">Zwischensparrendämmung</option>
                                        <option value="Aufdach">Aufdachdämmung</option>
                                        <option value="Beides">Beides</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 border border-slate-200 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-500 uppercase mb-3">Verlegung & Flachdach</h5>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Verlegung Solarkabel bis WR</label>
                                    <select name="roof_${idx}_dc_cable_route" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-slate-50">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Kabelkanal Fassade">Kabelkanal / Fallrohr Fassade</option>
                                        <option value="Leerrohr">Vorhandenes Leerrohr</option>
                                        <option value="Kamin">Durch Kamin</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Mit Kunden abgestimmt?</label>
                                    <select name="roof_${idx}_note_kabelAbgestimmt" class="select2-field w-full p-2 border rounded-lg outline-none focus:ring-brand-orange bg-slate-50">
                                        <option value="">Bitte wählen...</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Attika Höhe</label>
                                        <input type="number" name="roof_${idx}_note_attikaHoehe" class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Attika Breite</label>
                                        <input type="number" name="roof_${idx}_note_attikaBreite" class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 bg-white p-4 border border-slate-200 rounded-xl">
                        <h5 class="text-xs font-bold text-slate-500 uppercase mb-3">Dachaufbauten</h5>

                        <div class="space-y-3">
                            ${renderRoofActionRow(idx, "SAT-Schüssel", "sat")}
                            ${renderRoofActionRow(idx, "Antenne", "antenne")}
                            ${renderRoofActionRow(idx, "Trittstufen", "trittstufen", true)}
                            ${renderRoofActionRow(idx, "Sanitärlüfter", "luefter", true)}
                            ${renderRoofActionRow(idx, "Solarthermie", "thermie", true)}
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Schneefanggitter</label>
                                <select name="roof_${idx}_note_schneeAktion" class="select2-field w-full p-2 border rounded-lg outline-none bg-slate-50">
                                    <option value="">Bitte wählen...</option>
                                    <option value="bleibt">Bleibt</option>
                                    <option value="Demontage">Demontage</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Äußerer Blitzschutz vorhanden?</label>
                                <select name="roof_${idx}_note_blitzschutz" class="select2-field w-full p-2 border rounded-lg outline-none bg-slate-50">
                                    <option value="">Bitte wählen...</option>
                                    <option value="Ja">Ja</option>
                                    <option value="Nein">Nein</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Internet per Satellit vorhanden?</label>
                                <select name="roof_${idx}_note_satInternet" class="select2-field w-full p-2 border rounded-lg outline-none bg-slate-50">
                                    <option value="">Bitte wählen...</option>
                                    <option value="Ja">Ja</option>
                                    <option value="Nein">Nein</option>
                                </select>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kabelführung über Dach sorgt für Verschattung?</label>
                                <select name="roof_${idx}_note_kabelVerschattung" class="select2-field w-full p-2 border rounded-lg outline-none bg-slate-50">
                                    <option value="">Bitte wählen...</option>
                                    <option value="Ja">Ja</option>
                                    <option value="Nein">Nein</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML("beforeend", html);

            const newRoof = container.querySelector(`.roof-entry[data-index="${idx}"]`);

            if (roofData && newRoof) {
                const form = document.getElementById("form-pv");

                Object.keys(roofData).forEach(key => {
                    const fieldName = `roof_${idx}_${key}`;
                    const field = form.elements[fieldName];

                    if (field) {
                        setFormFieldValue(field, roofData[key]);
                    }
                });
            }

            if (newRoof) {
                initAllSelect2(newRoof);
            }

            updateFormProgress("PV");
        };

        function renderRoofActionRow(idx, label, key, hasCount = false) {
            const countInput = hasCount
                ? `
                    <input
                        type="number"
                        name="roof_${idx}_note_${key}Anzahl"
                        placeholder="Anz."
                        class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                `
                : "";

            const extraInput = key === "luefter"
                ? `
                    <input
                        type="text"
                        name="roof_${idx}_note_luefterNeuAnzahl"
                        placeholder="Anzahl neue Lüftungsziegel"
                        class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                `
                : `
                    <input
                        type="text"
                        name="roof_${idx}_note_${key}Ort"
                        placeholder="Neuer Montageort"
                        class="w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                `;

            return `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">${label}</label>
                        ${countInput || `<div class="hidden md:block h-[38px]"></div>`}
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Aktion</label>
                        <select name="roof_${idx}_note_${key}Aktion" class="select2-field w-full p-2 border rounded-lg text-sm outline-none bg-slate-50">
                            <option value="">Wählen...</option>
                            <option value="bleibt">Bleibt</option>
                            <option value="versetzen">Versetzen</option>
                            <option value="Demontage">Demontage</option>
                            ${key === "luefter" ? '<option value="kürzen">Kürzen</option><option value="neuer Lüftungsziegel">Neuer Lüftungsziegel einbauen</option>' : ""}
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Details</label>
                        ${extraInput}
                    </div>
                </div>
            `;
        }

        window.clearRoofs = function clearRoofs() {
            const container = document.getElementById("roofs-container");

            if (container) {
                container.innerHTML = "";
            }

            currentRoofIndex = 0;
            updateFormProgress("PV");
        };

        // ---------------------------------------------------------------------
        // IMAGES
        // ---------------------------------------------------------------------
        window.openImages = async function openImages(id) {
            currentRecordIdForImages = id;

            const record = getRecord(id);
            if (!record) return;

            record.images = Array.isArray(record.images) ? record.images : [];

            const fullName = `${record.data?.firma || ""} ${record.data?.name || ""} ${record.data?.lastname || ""}`.trim();
            document.getElementById("images-project-name").innerText = `Projekt: ${fullName || "Unbenannt"}`;

            renderImages();
            openModal("modal-images", "modal-images-content");

            if (!navigator.onLine) {
                showImageUploadStatus("Keine Internetverbindung. Gespeicherte Fotos werden angezeigt, neue Fotos werden lokal zwischengespeichert.", "warning");
                return;
            }

            try {
                const response = await fetch(getImageIndexUrl(id), {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                const data = await response.json();

                if (data.success) {
                    record.images = data.images || [];
                    renderImages();
                }
            } catch (error) {
                console.error(error);
                showImageUploadStatus("Fotos konnten nicht vom Server geladen werden.", "error");
            }
        };

        window.closeImages = function closeImages() {
            closeModal("modal-images", "modal-images-content");
        };

        const OFFLINE_DB_NAME = "feinaufmass_offline_uploads";
        const OFFLINE_DB_STORE = "queued_images";

        function openOfflineDb() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(OFFLINE_DB_NAME, 1);

                request.onupgradeneeded = function () {
                    const db = request.result;

                    if (!db.objectStoreNames.contains(OFFLINE_DB_STORE)) {
                        db.createObjectStore(OFFLINE_DB_STORE, {
                            keyPath: "id",
                            autoIncrement: true
                        });
                    }
                };

                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async function saveOfflineImage(payload) {
            const db = await openOfflineDb();

            return new Promise((resolve, reject) => {
                const tx = db.transaction(OFFLINE_DB_STORE, "readwrite");
                tx.objectStore(OFFLINE_DB_STORE).add(payload);
                tx.oncomplete = resolve;
                tx.onerror = () => reject(tx.error);
            });
        }

        async function getOfflineImages() {
            const db = await openOfflineDb();

            return new Promise((resolve, reject) => {
                const tx = db.transaction(OFFLINE_DB_STORE, "readonly");
                const request = tx.objectStore(OFFLINE_DB_STORE).getAll();

                request.onsuccess = () => resolve(request.result || []);
                request.onerror = () => reject(request.error);
            });
        }

        async function deleteOfflineImage(id) {
            const db = await openOfflineDb();

            return new Promise((resolve, reject) => {
                const tx = db.transaction(OFFLINE_DB_STORE, "readwrite");
                tx.objectStore(OFFLINE_DB_STORE).delete(id);
                tx.oncomplete = resolve;
                tx.onerror = () => reject(tx.error);
            });
        }

        function showImageUploadStatus(text, type = "info") {
            let badge = document.getElementById("image-upload-status");

            if (!badge) {
                badge = document.createElement("div");
                badge.id = "image-upload-status";
                document.body.appendChild(badge);
            }

            const baseClass = "fixed bottom-5 left-5 z-[120] px-4 py-2 rounded-xl shadow-lg text-sm font-black transition";

            const colorClass = type === "success"
                ? "bg-emerald-600 text-white"
                : type === "error"
                    ? "bg-red-600 text-white"
                    : type === "warning"
                        ? "bg-amber-500 text-white"
                        : "bg-slate-900 text-white";

            badge.className = `${baseClass} ${colorClass}`;
            badge.textContent = text;

            clearTimeout(badge._timer);

            badge._timer = setTimeout(() => {
                badge.remove();
            }, 3500);
        }

        function updateImageProgress(fileKey, percent, label = "Upload") {
            let box = document.getElementById("image-upload-progress-box");

            if (!box) {
                box = document.createElement("div");
                box.id = "image-upload-progress-box";
                box.className = "fixed bottom-20 left-5 z-[120] w-[320px] max-w-[calc(100vw-40px)] bg-white border border-slate-200 shadow-2xl rounded-2xl p-4 space-y-3";
                document.body.appendChild(box);
            }

            let row = document.getElementById(`upload-row-${fileKey}`);

            if (!row) {
                row = document.createElement("div");
                row.id = `upload-row-${fileKey}`;
                row.innerHTML = `
                    <div class="flex justify-between text-xs font-black text-slate-600 mb-1">
                        <span class="truncate pr-2 upload-label">${esc(label)}</span>
                        <span class="upload-percent">0%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="upload-bar h-full bg-brand-blue transition-all" style="width:0%"></div>
                    </div>
                `;
                box.appendChild(row);
            }

            row.querySelector(".upload-percent").textContent = `${percent}%`;
            row.querySelector(".upload-bar").style.width = `${percent}%`;

            if (percent >= 100) {
                setTimeout(() => {
                    row.remove();

                    if (!box.children.length) {
                        box.remove();
                    }
                }, 1200);
            }
        }

        function compressImage(file, options = {}) {
            const maxWidth = options.maxWidth || 1600;
            const maxHeight = options.maxHeight || 1600;
            const quality = options.quality || 0.72;
            const outputType = options.outputType || "image/jpeg";

            return new Promise((resolve, reject) => {
                if (!file.type.startsWith("image/")) {
                    reject(new Error("Datei ist kein Bild."));
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    const img = new Image();

                    img.onload = function () {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth || height > maxHeight) {
                            const ratio = Math.min(maxWidth / width, maxHeight / height);
                            width = Math.round(width * ratio);
                            height = Math.round(height * ratio);
                        }

                        const canvas = document.createElement("canvas");
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext("2d");
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(blob => {
                            if (!blob) {
                                reject(new Error("Bild konnte nicht komprimiert werden."));
                                return;
                            }

                            const originalName = file.name.replace(/\.[^/.]+$/, "");
                            const compressedFile = new File(
                                [blob],
                                `${originalName}.jpg`,
                                {
                                    type: outputType,
                                    lastModified: Date.now()
                                }
                            );

                            resolve(compressedFile);
                        }, outputType, quality);
                    };

                    img.onerror = () => reject(new Error("Bild konnte nicht gelesen werden."));
                    img.src = event.target.result;
                };

                reader.onerror = () => reject(new Error("Datei konnte nicht gelesen werden."));
                reader.readAsDataURL(file);
            });
        }

         function uploadImageWithProgress(recordId, file, fileKey) {
            return new Promise((resolve, reject) => {
                const record = getRecord(recordId);

                const formData = new FormData();
                formData.append("image", file);
                formData.append("image_name", file.name);

                if (record?.customerId) {
                    formData.append("customer_id", record.customerId);
                }

                if (record?.alternativeId) {
                    formData.append("alternative_id", record.alternativeId);
                }

                if (record?.productId) {
                    formData.append("product_id", record.productId);
                }

                const xhr = new XMLHttpRequest();

                xhr.open("POST", getImageUploadUrl(recordId), true);

                xhr.setRequestHeader("X-CSRF-TOKEN", CSRF_TOKEN);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                xhr.setRequestHeader("Accept", "application/json");

                xhr.upload.onprogress = function (event) {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        updateImageProgress(fileKey, percent, file.name);
                    }
                };

                xhr.onload = function () {
                    let response = {};

                    try {
                        response = JSON.parse(xhr.responseText || "{}");
                    } catch (error) {
                        reject(new Error("Server-Antwort konnte nicht gelesen werden."));
                        return;
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && response.success) {
                        updateImageProgress(fileKey, 100, file.name);
                        resolve(response.image);
                    } else {
                        reject(new Error(response.message || "Bild konnte nicht hochgeladen werden."));
                    }
                };

                xhr.onerror = function () {
                    reject(new Error("Keine Verbindung zum Server."));
                };

                xhr.send(formData);
            });
        }

        async function uploadOrQueueImage(recordId, file) {
            const compressed = await compressImage(file);
            const fileKey = `${Date.now()}_${Math.random().toString(16).slice(2)}`;

            if (!navigator.onLine) {
                await saveOfflineImage({
                    recordId,
                    file: compressed,
                    fileName: compressed.name,
                    createdAt: new Date().toISOString()
                });

                showImageUploadStatus("Offline: Bild wurde lokal gespeichert und wird später hochgeladen.", "warning");
                return {
                    queued: true,
                    image: {
                        id: `offline_${fileKey}`,
                        url: URL.createObjectURL(compressed),
                        name: compressed.name,
                        uploadedBy: currentUser,
                        uploadedAt: new Date().toISOString(),
                        offline: true
                    }
                };
            }

            const uploadedImage = await uploadImageWithProgress(recordId, compressed, fileKey);

            return {
                queued: false,
                image: uploadedImage
            };
        }

        window.handleImageUpload = async function handleImageUpload(event) {
            const files = Array.from(event.target.files || []);

            if (!files.length || !currentRecordIdForImages) {
                return;
            }

            const record = getRecord(currentRecordIdForImages);

            if (!record) {
                return;
            }

            record.images = Array.isArray(record.images) ? record.images : [];

            showImageUploadStatus("Bilder werden komprimiert...", "info");

            for (const file of files) {
                try {
                    const result = await uploadOrQueueImage(currentRecordIdForImages, file);

                    record.images.unshift(result.image);

                    if (result.queued) {
                        addHistory(currentRecordIdForImages, `Foto offline gespeichert: ${file.name}`);
                    } else {
                        addHistory(currentRecordIdForImages, `Foto hochgeladen: ${file.name}`);
                    }

                    renderImages();
                } catch (error) {
                    console.error(error);
                    showImageUploadStatus(error.message || "Bild konnte nicht hochgeladen werden.", "error");
                }
            }

            showImageUploadStatus("Upload abgeschlossen.", "success");

            event.target.value = "";
        };


        window.deleteImage = async function deleteImage(imgIndex) {
            const record = getRecord(currentRecordIdForImages);
            if (!record) return;

            const image = record.images[imgIndex];

            if (!image) return;

            if (!confirm("Foto wirklich löschen?")) return;

            if (image.offline || String(image.id || "").startsWith("offline_")) {
                record.images.splice(imgIndex, 1);
                addHistory(currentRecordIdForImages, "Offline-Foto wurde entfernt");
                renderImages();
                return;
            }

            try {
                const response = await fetch(getImageDeleteUrl(image.id), {
                    method: "DELETE",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Foto konnte nicht gelöscht werden.");
                }

                record.images.splice(imgIndex, 1);
                addHistory(currentRecordIdForImages, "Ein Foto wurde gelöscht");
                renderImages();

                showImageUploadStatus("Foto gelöscht.", "success");
            } catch (error) {
                console.error(error);
                showImageUploadStatus(error.message || "Foto konnte nicht gelöscht werden.", "error");
            }
        };

        function renderImages() {
            const record = getRecord(currentRecordIdForImages);
            const grid = document.getElementById("image-grid");

            if (!record || !grid) return;

            grid.innerHTML = "";

            if (!record.images || record.images.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-10 text-slate-400">
                        <i class="ph ph-images text-5xl"></i>
                        <p class="mt-2 font-semibold">Noch keine Fotos vorhanden.</p>
                    </div>
                `;
                return;
            }

            record.images.forEach((img, idx) => {
                const offlineBadge = img.offline
                    ? `
                        <div class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] px-2 py-1 rounded-full font-black shadow">
                            Offline gespeichert
                        </div>
                    `
                    : "";

                grid.insertAdjacentHTML("beforeend", `
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200 shadow-sm aspect-square bg-slate-100 flex flex-col">
                        ${offlineBadge}

                        <img
                            src="${esc(img.url)}"
                            class="w-full h-full object-cover flex-1"
                            onerror="this.onerror=null; this.src='${PLACEHOLDER_IMAGE}'">

                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/90 to-transparent p-3 pt-8 pointer-events-none">
                            <p class="text-white text-xs font-bold truncate">
                                <i class="ph-fill ph-user"></i> ${esc(img.uploadedBy || "Unbekannt")}
                            </p>
                            <p class="text-white/80 text-[10px]">
                                <i class="ph-fill ph-clock"></i> ${img.uploadedAt ? new Date(img.uploadedAt).toLocaleString("de-DE") : "-"}
                            </p>
                            ${img.name ? `
                                <p class="text-white/70 text-[10px] truncate">
                                    <i class="ph-fill ph-file-image"></i> ${esc(img.name)}
                                </p>
                            ` : ""}
                        </div>

                        <button onclick="deleteImage(${idx})" class="absolute top-2 right-2 bg-white/90 hover:bg-red-50 text-red-500 p-2 rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition z-10">
                            <i class="ph-bold ph-trash"></i>
                        </button>
                    </div>
                `);
            });
        }

        // ---------------------------------------------------------------------
        // MATERIALS
        // ---------------------------------------------------------------------
        function getMaterialSaveUrl(recordId) {
            return MATERIAL_SAVE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function getDetailSaveUrl(recordId) {
            return DETAIL_SAVE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(recordId));
        }

        function markMaterialsDirty(action = "Material geändert") {
            clearTimeout(materialSaveTimer);

            materialSaveTimer = setTimeout(() => {
                saveMaterialState(action);
            }, 500);
        }

        async function saveMaterialState(action = "Material gespeichert") {
            const record = getRecord(currentRecordIdForMaterials);

            if (!record || !record.id) {
                return;
            }

            normalizeMaterialState(record);

            materialSaving = true;

            try {
                const response = await fetch(getMaterialSaveUrl(record.id), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        materials: record.materials,
                        history_action: action
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Material konnte nicht gespeichert werden.");
                }

                record.materials = data.materials || record.materials;

                if (!record.history) {
                    record.history = [];
                }

                record.history.unshift({
                    action,
                    user: currentUser,
                    date: new Date().toISOString()
                });

                showMaterialSaveStatus("Gespeichert", "success");
            } catch (error) {
                console.error(error);
                showMaterialSaveStatus("Nicht gespeichert", "error");
            } finally {
                materialSaving = false;
            }
        }

        function showMaterialSaveStatus(text, type = "success") {
            const container = document.getElementById("materials-list-container");

            if (!container) {
                return;
            }

            let badge = document.getElementById("material-save-status");

            if (!badge) {
                badge = document.createElement("div");
                badge.id = "material-save-status";
                badge.className = "fixed bottom-5 right-5 z-[100] px-4 py-2 rounded-xl shadow-lg text-sm font-black transition";
                document.body.appendChild(badge);
            }

            badge.textContent = text;
            badge.className = type === "success"
                ? "fixed bottom-5 right-5 z-[100] px-4 py-2 rounded-xl shadow-lg text-sm font-black transition bg-emerald-600 text-white"
                : "fixed bottom-5 right-5 z-[100] px-4 py-2 rounded-xl shadow-lg text-sm font-black transition bg-red-600 text-white";

            clearTimeout(badge._timer);

            badge._timer = setTimeout(() => {
                badge.remove();
            }, 1800);
        }
        window.openMaterials = function openMaterials(id) {
            currentRecordIdForMaterials = id;

            const record = getRecord(id);
            if (!record) return;

            const fullName = `${record.data?.firma || ""} ${record.data?.name || ""} ${record.data?.lastname || ""}`.trim();
            document.getElementById("material-project-name").innerText = `Projekt: ${fullName || "Unbenannt"}`;

            renderMaterials();
            openModal("modal-materials", "modal-materials-content");
        };

        window.closeMaterials = function closeMaterials() {
            closeModal("modal-materials", "modal-materials-content");
        };

        function normalizeMaterialItem(item) {
            const plan = toNumber(item.plan_qty ?? item.qty ?? item.qty_offer ?? 0);

            if (item.plan_qty === undefined || item.plan_qty === null) {
                item.plan_qty = plan;
            }

            if (item.verbrauch_qty === undefined || item.verbrauch_qty === null) {
                item.verbrauch_qty = toNumber(item.qty_measurement ?? item.qty_final ?? item.qty ?? plan);
            }

            if (item.approved === undefined || item.approved === null) {
                item.approved = false;
            }

            // Reason for material delta
            if (item.delta_reason === undefined || item.delta_reason === null) {
                item.delta_reason = item.reason ?? item.qty_reason ?? "";
            }

            item.subItems = Array.isArray(item.subItems) ? item.subItems : [];
            item.subItems.forEach(sub => normalizeMaterialItem(sub));

            return item;
        }

        function normalizeMaterialState(record) {
            record.materials = Array.isArray(record.materials) ? record.materials : [];

            record.materials.forEach(category => {
                category.items = Array.isArray(category.items) ? category.items : [];
                category.items.forEach(item => normalizeMaterialItem(item));
            });
        }

        function getAllMaterialItems(record) {
            const all = [];

            normalizeMaterialState(record);

            record.materials.forEach(category => {
                category.items.forEach(item => {
                    all.push(item);
                    item.subItems.forEach(sub => all.push(sub));
                });
            });

            return all;
        }

        function getMaterialDelta(item) {
            const plan = toNumber(item.plan_qty ?? item.qty ?? 0);
            const verbrauch = toNumber(item.verbrauch_qty ?? item.qty_measurement ?? item.qty_final ?? plan);

            return verbrauch - plan;
        }

        function getMaterialStats(record) {
            const all = getAllMaterialItems(record);

            const total = all.length;
            const approved = all.filter(item => item.approved).length;
            const totalPlan = all.reduce((sum, item) => sum + toNumber(item.plan_qty ?? item.qty ?? 0), 0);
            const totalVerbrauch = all.reduce((sum, item) => sum + toNumber(item.verbrauch_qty ?? 0), 0);

            return {
                total,
                approved,
                open: total - approved,
                totalPlan,
                totalVerbrauch,
                totalDelta: totalVerbrauch - totalPlan
            };
        }

        window.updateMatConsumption = function updateMatConsumption(catIdx, itemIdx, subIdx, value) {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            normalizeMaterialState(record);

            const item = subIdx !== null
                ? record.materials[catIdx]?.items?.[itemIdx]?.subItems?.[subIdx]
                : record.materials[catIdx]?.items?.[itemIdx];

            if (!item) return;

            item.verbrauch_qty = toNumber(value);
            item.approved = false;

            const action = `Verbrauch geändert: ${item.name} auf ${formatQty(item.verbrauch_qty)} ${item.unit || ""}, Delta ${formatQty(getMaterialDelta(item))}`;

            addHistory(currentRecordIdForMaterials, action);
            renderMaterials();
            markMaterialsDirty(action);
        };


        window.updateMatDeltaReason = function updateMatDeltaReason(catIdx, itemIdx, subIdx, value) {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            normalizeMaterialState(record);

            const item = subIdx !== null
                ? record.materials[catIdx]?.items?.[itemIdx]?.subItems?.[subIdx]
                : record.materials[catIdx]?.items?.[itemIdx];

            if (!item) return;

            item.delta_reason = String(value ?? "").trim();

            markMaterialsDirty(`Abweichungsgrund aktualisiert: ${item.name}`);
        };

        window.toggleMatApproved = function toggleMatApproved(catIdx, itemIdx, subIdx) {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            normalizeMaterialState(record);

            const item = subIdx !== null
                ? record.materials[catIdx]?.items?.[itemIdx]?.subItems?.[subIdx]
                : record.materials[catIdx]?.items?.[itemIdx];

            if (!item) return;

            const delta = getMaterialDelta(item);

            if (!item.approved && delta !== 0 && !String(item.delta_reason || "").trim()) {
                alert("Bitte zuerst einen Grund für die Material-Abweichung eingeben.");
                return;
            }

            item.approved = !item.approved;

            const action = item.approved
                ? `Material freigegeben: ${item.name}${delta !== 0 ? ` | Grund: ${item.delta_reason}` : ""}`
                : `Material-Freigabe entfernt: ${item.name}`;

            addHistory(currentRecordIdForMaterials, action);
            renderMaterials();
            saveMaterialState(action);
        };

        window.approveAllMaterials = function approveAllMaterials() {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            const missingReasons = getAllMaterialItems(record).filter(item => {
                const delta = getMaterialDelta(item);
                return delta !== 0 && !String(item.delta_reason || "").trim();
            });

            if (missingReasons.length > 0) {
                alert(`Es fehlen noch Begründungen für ${missingReasons.length} Material-Abweichung(en).`);
                return;
            }

            getAllMaterialItems(record).forEach(item => {
                item.approved = true;
            });

            const action = "Alle Materialien wurden freigegeben";

            addHistory(currentRecordIdForMaterials, action);
            renderMaterials();
            saveMaterialState(action);
        };

        window.unapproveAllMaterials = function unapproveAllMaterials() {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            getAllMaterialItems(record).forEach(item => {
                item.approved = false;
            });

            const action = "Alle Material-Freigaben wurden entfernt";

            addHistory(currentRecordIdForMaterials, action);
            renderMaterials();
            saveMaterialState(action);
        };

        window.deleteMat = function deleteMat(catIdx, itemIdx, subIdx) {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            normalizeMaterialState(record);

            let itemName = "";

            if (subIdx !== null) {
                const item = record.materials[catIdx]?.items?.[itemIdx]?.subItems?.[subIdx];
                if (!item) return;

                itemName = item.name;
                record.materials[catIdx].items[itemIdx].subItems.splice(subIdx, 1);
            } else {
                const item = record.materials[catIdx]?.items?.[itemIdx];
                if (!item) return;

                itemName = item.name;
                record.materials[catIdx].items.splice(itemIdx, 1);
            }

            const action = `Material gelöscht/abgewählt: ${itemName}`;

            addHistory(currentRecordIdForMaterials, action);
            renderMaterials();
            saveMaterialState(action);
        };

        window.openProductPicker = function openProductPicker(catIdx, itemIdx = null, mode = "main") {
            productPickerTarget = { catIdx, itemIdx, mode };

            const fields = {
                "product-picker-search": "",
                "product-picker-plan": "0",
                "product-picker-verbrauch": "1",
                "product-picker-unit": "Stk",
                "manual-product-name": "",
                "manual-product-article-no": ""
            };

            Object.entries(fields).forEach(([id, value]) => {
                const field = document.getElementById(id);
                if (field) field.value = value;
            });

            const results = document.getElementById("product-picker-results");

            if (results) {
                results.innerHTML = `
                    <div class="text-center py-10 text-slate-400">
                        <i class="ph ph-package text-5xl"></i>
                        <p class="mt-2 font-semibold">Suche ein Produkt oder lege es manuell an.</p>
                    </div>
                `;
            }

            openModal("modal-product-picker", "modal-product-picker-content");

            setTimeout(() => {
                document.getElementById("product-picker-search")?.focus();
            }, 150);
        };

        window.closeProductPicker = function closeProductPicker() {
            closeModal("modal-product-picker", "modal-product-picker-content");
        };

        window.searchProductsForMaterial = function searchProductsForMaterial() {
            clearTimeout(productSearchTimer);

            productSearchTimer = setTimeout(async () => {
                const q = document.getElementById("product-picker-search")?.value.trim() || "";
                const resultBox = document.getElementById("product-picker-results");

                if (!resultBox) return;

                if (q.length < 2) {
                    resultBox.innerHTML = `
                        <div class="text-center py-10 text-slate-400">
                            <i class="ph ph-keyboard text-5xl"></i>
                            <p class="mt-2 font-semibold">Mindestens 2 Zeichen eingeben.</p>
                        </div>
                    `;
                    return;
                }

                resultBox.innerHTML = `
                    <div class="text-center py-10 text-slate-400">
                        <i class="ph ph-spinner-gap text-5xl animate-spin"></i>
                        <p class="mt-2 font-semibold">Produkte werden geladen...</p>
                    </div>
                `;

                try {
                    const url = `${PRODUCT_SEARCH_URL}?q=${encodeURIComponent(q)}`;

                    const response = await fetch(url, {
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });

                    const data = await response.json();

                    if (!data.success || !Array.isArray(data.products) || data.products.length === 0) {
                        resultBox.innerHTML = `
                            <div class="text-center py-10 text-slate-400 bg-white border border-slate-200 rounded-2xl">
                                <i class="ph ph-package-x text-5xl"></i>
                                <p class="mt-2 font-semibold">Kein Produkt gefunden.</p>
                                <p class="text-xs mt-1">Du kannst es unten manuell hinzufügen.</p>
                            </div>
                        `;
                        return;
                    }

                    renderProductPickerResults(data.products);
                } catch (error) {
                    console.error(error);

                    resultBox.innerHTML = `
                        <div class="text-center py-10 text-red-400 bg-white border border-red-100 rounded-2xl">
                            <i class="ph ph-warning-circle text-5xl"></i>
                            <p class="mt-2 font-semibold">Produkte konnten nicht geladen werden.</p>
                        </div>
                    `;
                }
            }, 300);
        };

        function renderProductPickerResults(products) {
            const resultBox = document.getElementById("product-picker-results");
            if (!resultBox) return;

            resultBox.innerHTML = products.map(product => {
                const stockQty = toNumber(product.stock_qty);
                const hasStock = stockQty > 0;

                const stockClass = hasStock
                    ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                    : "bg-red-50 text-red-600 border-red-200";

                const locationText = [
                    product.location_label,
                    product.room_name,
                    product.rack_name ? `Regal: ${product.rack_name}` : "",
                    product.shelf ? `Fach: ${product.shelf}` : ""
                ].filter(Boolean).join(" · ");

                const json = esc(JSON.stringify(product));

                return `
                    <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm hover:border-brand-blue transition">
                        <div class="flex flex-col md:flex-row gap-4 md:items-center">
                            <img
                                src="${esc(product.image || PLACEHOLDER_IMAGE)}"
                                class="w-full md:w-20 h-32 md:h-20 object-cover rounded-xl border border-slate-100 bg-slate-50"
                                onerror="this.onerror=null; this.src='${PLACEHOLDER_IMAGE}'">

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h4 class="font-black text-slate-800 truncate">${esc(product.name)}</h4>

                                    <span class="px-2 py-0.5 rounded-full border text-xs font-black ${stockClass}">
                                        ${hasStock ? "Auf Lager" : "Nicht auf Lager"}: ${formatQty(stockQty)}
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2 text-[11px] text-slate-500">
                                    ${product.article_no ? `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded-md">Art.-Nr: ${esc(product.article_no)}</span>` : ""}
                                    ${product.sku ? `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded-md">SKU: ${esc(product.sku)}</span>` : ""}
                                    ${product.ean ? `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded-md">EAN: ${esc(product.ean)}</span>` : ""}
                                    ${product.unit ? `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded-md">Einheit: ${esc(product.unit)}</span>` : ""}
                                </div>

                                ${locationText ? `
                                    <p class="text-xs text-slate-400 mt-1">
                                        <i class="ph-fill ph-map-pin"></i> ${esc(locationText)}
                                    </p>
                                ` : ""}

                                ${product.short_description ? `
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">${esc(product.short_description)}</p>
                                ` : ""}
                            </div>

                            <button
                                type="button"
                                data-product-json="${json}"
                                onclick="selectProductForMaterialFromButton(this)"
                                class="px-4 py-2.5 rounded-xl bg-brand-blue text-white font-black hover:opacity-90 transition flex items-center justify-center gap-2 shrink-0">
                                <i class="ph-bold ph-plus"></i>
                                Hinzufügen
                            </button>
                        </div>
                    </div>
                `;
            }).join("");
        }

        window.selectProductForMaterialFromButton = function selectProductForMaterialFromButton(button) {
            try {
                const product = JSON.parse(button.dataset.productJson || "{}");
                selectProductForMaterial(product);
            } catch (error) {
                console.error("Produkt konnte nicht gelesen werden:", error);
                alert("Produkt konnte nicht hinzugefügt werden.");
            }
        };

        function selectProductForMaterial(product) {
            const planQty = toNumber(document.getElementById("product-picker-plan")?.value);
            const verbrauchQty = toNumber(document.getElementById("product-picker-verbrauch")?.value);
            const unitInput = document.getElementById("product-picker-unit")?.value.trim();

            const material = {
                id: `product_${product.id}_${Date.now()}`,
                product_id: product.id,
                name: product.name || "Unbenannt",
                img: product.image || PLACEHOLDER_IMAGE,
                plan_qty: planQty,
                verbrauch_qty: verbrauchQty,
                qty: planQty,
                unit: unitInput || product.unit || "Stk",
                article_no: product.article_no || null,
                sku: product.sku || null,
                ean: product.ean || null,
                stock_qty: toNumber(product.stock_qty),
                stock_checked: true,
                approved: false,
                subItems: []
            };

            pushPickedMaterial(material);
        }

        window.addManualProductToMaterial = function addManualProductToMaterial() {
            const name = document.getElementById("manual-product-name")?.value.trim();

            if (!name) {
                alert("Bitte Produktname eingeben.");
                return;
            }

            const articleNo = document.getElementById("manual-product-article-no")?.value.trim();
            const planQty = toNumber(document.getElementById("product-picker-plan")?.value);
            const verbrauchQty = toNumber(document.getElementById("product-picker-verbrauch")?.value);
            const unit = document.getElementById("product-picker-unit")?.value.trim() || "Stk";

            pushPickedMaterial({
                id: `manual_${Date.now()}`,
                product_id: null,
                name,
                img: PLACEHOLDER_IMAGE,
                plan_qty: planQty,
                verbrauch_qty: verbrauchQty,
                qty: planQty,
                unit,
                article_no: articleNo || null,
                stock_qty: null,
                stock_checked: false,
                approved: false,
                subItems: []
            });
        };

        function pushPickedMaterial(material) {
            const record = getRecord(currentRecordIdForMaterials);
            if (!record) return;

            normalizeMaterialState(record);

            const { catIdx, itemIdx, mode } = productPickerTarget;

            if (!record.materials[catIdx]) {
                record.materials[catIdx] = {
                    id: `section_${Date.now()}`,
                    title: "Ohne Bereich",
                    items: []
                };
            }

            if (mode === "sub") {
                const parent = record.materials[catIdx]?.items?.[itemIdx];

                if (!parent) {
                    alert("Hauptartikel wurde nicht gefunden.");
                    return;
                }

                parent.subItems = Array.isArray(parent.subItems) ? parent.subItems : [];
                parent.subItems.push(material);

                addHistory(currentRecordIdForMaterials, `Unter-Material hinzugefügt: ${material.name}`);
            } else {
                record.materials[catIdx].items.push(material);
                addHistory(currentRecordIdForMaterials, `Material hinzugefügt: ${material.name}`);
            }

            closeProductPicker();
            renderMaterials();
            saveMaterialState(mode === "sub"
                ? `Unter-Material hinzugefügt: ${material.name}`
                : `Material hinzugefügt: ${material.name}`
            );  
        }

        function renderMaterials() {
            const record = getRecord(currentRecordIdForMaterials);
            const listContainer = document.getElementById("materials-list-container");

            if (!listContainer) return;

            listContainer.innerHTML = "";

            if (!record) {
                listContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="ph ph-warning-circle text-6xl mb-4 text-red-300"></i>
                        <p class="font-medium">Aufmaß wurde nicht gefunden.</p>
                    </div>
                `;
                return;
            }

            normalizeMaterialState(record);

            if (record.materials.length === 0) {
                listContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="ph ph-package-x text-6xl mb-4 text-slate-300"></i>
                        <p class="font-medium">Keine Materialien geplant.</p>
                    </div>
                `;
                return;
            }

            const fallbackMainImage = "https://placehold.co/150x150?text=No+Image";
            const fallbackSubImage = "https://placehold.co/80x80?text=Img";
            const stats = getMaterialStats(record);

            const deltaTotalClass = stats.totalDelta > 0
                ? "text-red-600 bg-red-50 border-red-200"
                : stats.totalDelta < 0
                    ? "text-amber-700 bg-amber-50 border-amber-200"
                    : "text-emerald-700 bg-emerald-50 border-emerald-200";

            let html = `
                <div class="sticky top-0 z-10 bg-slate-50/95 backdrop-blur pb-4 mb-5 border-b border-slate-200">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                        <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm">
                            <p class="text-[11px] uppercase font-bold text-slate-400">Artikel</p>
                            <p class="text-xl font-black text-slate-800">${stats.total}</p>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm">
                            <p class="text-[11px] uppercase font-bold text-slate-400">Freigegeben</p>
                            <p class="text-xl font-black text-emerald-600">${stats.approved}/${stats.total}</p>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm">
                            <p class="text-[11px] uppercase font-bold text-slate-400">Plan gesamt</p>
                            <p class="text-xl font-black text-brand-blue">${formatQty(stats.totalPlan)}</p>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl p-3 shadow-sm">
                            <p class="text-[11px] uppercase font-bold text-slate-400">Verbrauch gesamt</p>
                            <p class="text-xl font-black text-slate-800">${formatQty(stats.totalVerbrauch)}</p>
                        </div>

                        <div class="border rounded-2xl p-3 shadow-sm ${deltaTotalClass}">
                            <p class="text-[11px] uppercase font-bold opacity-70">Delta gesamt</p>
                            <p class="text-xl font-black">${stats.totalDelta > 0 ? "+" : ""}${formatQty(stats.totalDelta)}</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-2 md:items-center md:justify-between">
                        <div class="text-sm text-slate-500 font-semibold">
                            <i class="ph-fill ph-info"></i>
                            Delta = Verbrauch minus Plan. Beispiel: Plan 2, Verbrauch 12 = Delta +10.
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="approveAllMaterials()" class="px-3 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-1">
                                <i class="ph-bold ph-checks"></i> Alle freigeben
                            </button>

                            <button type="button" onclick="unapproveAllMaterials()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-100 transition flex items-center gap-1">
                                <i class="ph-bold ph-arrow-counter-clockwise"></i> Zurücksetzen
                            </button>
                        </div>
                    </div>
                </div>
            `;

            record.materials.forEach((category, cIdx) => {
                category.items = Array.isArray(category.items) ? category.items : [];

                html += `
                    <div class="mb-8">
                        <div class="flex justify-between items-center border-b-2 border-brand-lightBlue pb-2 mb-4">
                            <h3 class="text-lg font-bold text-brand-blue">${esc(category.title || "Ohne Bereich")}</h3>

                            <button type="button" onclick="openProductPicker(${cIdx}, null, 'main')" class="text-brand-blue bg-brand-lightBlue/30 hover:bg-brand-lightBlue p-1.5 rounded-lg text-sm font-bold flex items-center gap-1 transition">
                                <i class="ph-bold ph-plus"></i> Artikel
                            </button>
                        </div>

                        <div class="space-y-4 main-item-list" id="mat-cat-${cIdx}">
                `;

                if (category.items.length === 0) {
                    html += `<p class="text-slate-500 text-sm italic">Keine Artikel in dieser Kategorie.</p>`;
                }

                category.items.forEach((item, iIdx) => {
                    normalizeMaterialItem(item);

                    const itemName = esc(item.name || "Unbenannt");
                    const itemImg = esc(item.img || fallbackMainImage);
                    const itemPlan = toNumber(item.plan_qty ?? item.qty ?? 0);
                    const itemVerbrauch = toNumber(item.verbrauch_qty ?? itemPlan);
                    const itemDelta = getMaterialDelta(item);
                    const itemUnit = esc(item.unit || "Stk");
                    const itemApproved = !!item.approved;

                    const itemDeltaClass = itemDelta > 0
                        ? "bg-red-50 text-red-600 border-red-200"
                        : itemDelta < 0
                            ? "bg-amber-50 text-amber-700 border-amber-200"
                            : "bg-emerald-50 text-emerald-700 border-emerald-200";

                    const approvedCardClass = itemApproved
                        ? "border-emerald-300 bg-emerald-50/40 ring-1 ring-emerald-200"
                        : "bg-white border-slate-200";

                    const approvedButtonClass = itemApproved
                        ? "bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700"
                        : "bg-white text-slate-600 border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300";

                    const meta = [
                        item.article_no ? `Art.-Nr: ${esc(item.article_no)}` : "",
                        item.distributor ? esc(item.distributor) : "",
                        item.distributor_no ? `Lieferant-Nr: ${esc(item.distributor_no)}` : ""
                    ].filter(Boolean);

                    const itemMetaHtml = meta.length
                        ? `
                            <div class="flex flex-wrap gap-2 mt-1 text-[11px] text-slate-500">
                                ${meta.map(text => `<span class="px-2 py-0.5 bg-slate-100 rounded-md border border-slate-200">${text}</span>`).join("")}
                            </div>
                        `
                        : "";

                    const stockInfoHtml = item.stock_checked
                        ? `
                            <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 rounded-lg border text-xs font-black ${
                                toNumber(item.stock_qty) > 0
                                    ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                                    : "bg-red-50 text-red-600 border-red-200"
                            }">
                                <i class="ph-bold ph-warehouse"></i>
                                Lager: ${formatQty(item.stock_qty)}
                            </div>
                        `
                        : "";

                    const subItemsHtml = item.subItems.length > 0
                        ? item.subItems.map((sub, sIdx) => renderSubMaterialRow(sub, cIdx, iIdx, sIdx, fallbackSubImage)).join("")
                        : '<p class="text-xs text-slate-400 italic">Keine Unterartikel</p>';

                    html += `
                        <div class="border rounded-2xl shadow-sm overflow-hidden hover:border-brand-lightBlue transition ${approvedCardClass}">
                            <div class="flex flex-col sm:flex-row gap-4 p-4">
                                <div class="shrink-0 flex items-center sm:items-start gap-3">
                                    <i class="ph-bold ph-dots-six-vertical text-2xl text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-main mt-6 hidden sm:block"></i>

                                    <img
                                        src="${itemImg}"
                                        class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-slate-100 shadow-sm"
                                        onerror="this.onerror=null; this.src='${fallbackMainImage}'">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2 mb-3">
                                        <div class="flex items-start gap-2 min-w-0">
                                            <i class="ph-bold ph-dots-six-vertical text-xl text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-main sm:hidden mt-1"></i>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="font-bold text-slate-800 text-base md:text-lg leading-tight break-words">${itemName}</h4>

                                                    ${itemApproved ? `
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-black">
                                                            <i class="ph-bold ph-check-circle"></i> OK
                                                        </span>
                                                    ` : ""}
                                                </div>

                                                ${itemMetaHtml}
                                                ${stockInfoHtml}
                                            </div>
                                        </div>

                                        <div class="flex gap-1 shrink-0">
                                            <button type="button" onclick="toggleMatApproved(${cIdx}, ${iIdx}, null)" class="px-3 py-2 rounded-lg border text-sm font-bold transition flex items-center gap-1 ${approvedButtonClass}" title="Artikel freigeben">
                                                <i class="ph-bold ${itemApproved ? "ph-check-circle" : "ph-circle"}"></i>
                                                ${itemApproved ? "Freigegeben" : "Freigeben"}
                                            </button>

                                            <button type="button" onclick="deleteMat(${cIdx}, ${iIdx}, null)" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition" title="Löschen / Markieren">
                                                <i class="ph-bold ph-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                        <div>
                                            <label class="text-[11px] font-black text-slate-400 uppercase">Plan</label>
                                            <div class="h-11 px-3 flex items-center justify-center rounded-xl bg-brand-lightBlue/20 border border-brand-lightBlue text-brand-blue font-black">
                                                ${formatQty(itemPlan)} ${itemUnit}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-black text-slate-400 uppercase">Verbrauch / Benötigt</label>
                                            <input type="number" min="0" step="0.1" value="${formatQty(itemVerbrauch)}" class="h-11 w-full px-3 text-center font-black text-slate-800 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-blue" onchange="updateMatConsumption(${cIdx}, ${iIdx}, null, this.value)">
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-black text-slate-400 uppercase">Delta / Differenz</label>
                                            <div class="h-11 px-3 flex items-center justify-center rounded-xl border font-black ${itemDeltaClass}">
                                                ${itemDelta > 0 ? "+" : ""}${formatQty(itemDelta)} ${itemUnit}
                                            </div>
                                        </div>
                                    </div>

                                    ${itemDelta !== 0 ? `
                                        <div class="mb-3 rounded-2xl border border-amber-200 bg-amber-50 p-3">
                                            <label class="flex items-center gap-2 text-[11px] font-black text-amber-700 uppercase mb-2">
                                                <i class="ph-bold ph-warning-circle"></i>
                                                Begründung für Abweichung erforderlich
                                            </label>

                                            <div class="relative">
                                                <i class="ph-bold ph-note-pencil absolute left-3 top-1/2 -translate-y-1/2 text-amber-500 text-lg"></i>
                                                <input
                                                    type="text"
                                                    value="${esc(item.delta_reason || "")}"
                                                    placeholder="${itemDelta > 0 ? "Warum wird mehr Material benötigt?" : "Warum wird weniger Material benötigt?"}"
                                                    class="w-full pl-10 pr-3 py-3 bg-white border border-amber-200 rounded-xl outline-none focus:ring-2 focus:ring-amber-300 text-sm font-semibold text-slate-700"
                                                    oninput="updateMatDeltaReason(${cIdx}, ${iIdx}, null, this.value)">
                                            </div>
                                        </div>
                                    ` : ""}     

                                    <div class="mt-4 pt-3 border-t border-slate-100">
                                        <div class="flex justify-between items-center mb-2">
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Beinhaltet:</p>

                                            <button type="button" onclick="openProductPicker(${cIdx}, ${iIdx}, 'sub')" class="text-brand-green bg-brand-lightGreen/30 hover:bg-brand-lightGreen/50 px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 transition">
                                                <i class="ph-bold ph-plus"></i> Hinzufügen
                                            </button>
                                        </div>

                                        <div class="space-y-2 sub-item-list" id="mat-sub-${cIdx}-${iIdx}">
                                            ${subItemsHtml}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            listContainer.innerHTML = html;

            initMaterialSorting(record);
        }

        function renderSubMaterialRow(sub, cIdx, iIdx, sIdx, fallbackSubImage) {
            normalizeMaterialItem(sub);

            const subName = esc(sub.name || "Unbenannt");
            const subImg = esc(sub.img || fallbackSubImage);
            const subPlan = toNumber(sub.plan_qty ?? sub.qty ?? 0);
            const subVerbrauch = toNumber(sub.verbrauch_qty ?? subPlan);
            const subDelta = getMaterialDelta(sub);
            const subUnitRaw = sub.unit || "Stk";
            const subUnit = esc(subUnitRaw);
            const subApproved = !!sub.approved;
            const subDeltaReason = esc(sub.delta_reason || "");

            const subDeltaClass = subDelta > 0
                ? "bg-red-50 text-red-600 border-red-200"
                : subDelta < 0
                    ? "bg-amber-50 text-amber-700 border-amber-200"
                    : "bg-emerald-50 text-emerald-700 border-emerald-200";

            const subRowClass = subApproved
                ? "bg-emerald-50 border-emerald-200"
                : "bg-slate-50 border-slate-100";

            const subApprovedButtonClass = subApproved
                ? "bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700"
                : "bg-white text-slate-500 border-slate-200 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50";

            const metaHtml = (sub.article_no || sub.distributor)
                ? `
                    <p class="text-[10px] text-slate-400 truncate">
                        ${sub.article_no ? `Art.-Nr: ${esc(sub.article_no)}` : ""}
                        ${sub.article_no && sub.distributor ? " · " : ""}
                        ${sub.distributor ? esc(sub.distributor) : ""}
                    </p>
                `
                : "";

            const reasonHtml = subDelta !== 0
                ? `
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-2">
                        <label class="flex items-center gap-1 text-[10px] font-black text-amber-700 uppercase mb-1">
                            <i class="ph-bold ph-warning-circle"></i>
                            Grund für Abweichung
                        </label>

                        <input
                            type="text"
                            value="${subDeltaReason}"
                            placeholder="${subDelta > 0 ? "Warum mehr Verbrauch?" : "Warum weniger Verbrauch?"}"
                            class="w-full px-3 py-2 bg-white border border-amber-200 rounded-lg outline-none focus:ring-2 focus:ring-amber-300 text-xs font-semibold text-slate-700"
                            oninput="updateMatDeltaReason(${cIdx}, ${iIdx}, ${sIdx}, this.value)">
                    </div>
                `
                : "";

            return `
                <div class="p-2 rounded-lg border ${subRowClass}">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <i class="ph-bold ph-dots-six-vertical text-lg text-slate-300 cursor-grab active:cursor-grabbing hover:text-brand-blue drag-handle-sub shrink-0"></i>

                            <img
                                src="${subImg}"
                                class="w-9 h-9 object-cover rounded-md border border-slate-200 shrink-0"
                                onerror="this.onerror=null; this.src='${esc(fallbackSubImage)}'">

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate" title="${subName}">
                                    ${subName}
                                </p>
                                ${metaHtml}
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 lg:w-[330px]">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Plan</label>
                                <div class="h-9 px-2 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-sm font-bold text-brand-blue">
                                    ${formatQty(subPlan)} ${subUnit}
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Verbrauch</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.1"
                                    value="${formatQty(subVerbrauch)}"
                                    class="h-9 w-full px-2 text-center text-sm border rounded-lg outline-none focus:border-brand-blue font-bold"
                                    onchange="updateMatConsumption(${cIdx}, ${iIdx}, ${sIdx}, this.value)">
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Delta</label>
                                <div class="h-9 px-2 flex items-center justify-center rounded-lg border text-sm font-black ${subDeltaClass}">
                                    ${subDelta > 0 ? "+" : ""}${formatQty(subDelta)} ${subUnit}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <button
                                type="button"
                                onclick="toggleMatApproved(${cIdx}, ${iIdx}, ${sIdx})"
                                class="h-9 px-2 rounded-lg border text-xs font-bold transition ${subApprovedButtonClass}"
                                title="${subApproved ? "Freigabe entfernen" : "Freigeben"}">
                                <i class="ph-bold ${subApproved ? "ph-check-circle" : "ph-circle"}"></i>
                            </button>

                            <button
                                type="button"
                                onclick="deleteMat(${cIdx}, ${iIdx}, ${sIdx})"
                                class="h-9 px-2 text-red-400 hover:text-red-600 bg-white border border-slate-200 hover:border-red-200 rounded-lg transition"
                                title="Unterartikel löschen">
                                <i class="ph-bold ph-trash"></i>
                            </button>
                        </div>
                    </div>

                    ${reasonHtml}
                </div>
            `;
        }
         
        

        function initMaterialSorting(record) {
            if (typeof Sortable === "undefined") {
                console.warn("SortableJS wurde nicht geladen.");
                return;
            }

            record.materials.forEach((category, cIdx) => {
                const catEl = document.getElementById(`mat-cat-${cIdx}`);

                if (catEl) {
                    new Sortable(catEl, {
                        handle: ".drag-handle-main",
                        animation: 150,
                        onEnd: function (evt) {
                            if (
                                evt.oldIndex === undefined ||
                                evt.newIndex === undefined ||
                                evt.oldIndex === evt.newIndex
                            ) {
                                return;
                            }

                            const movedItem = category.items.splice(evt.oldIndex, 1)[0];
                            if (!movedItem) return;

                            category.items.splice(evt.newIndex, 0, movedItem);
                            addHistory(currentRecordIdForMaterials, "Sortierung Hauptartikel geändert");
                            saveMaterialState("Sortierung Hauptartikel geändert");
                        }
                    });
                }

                category.items.forEach((item, iIdx) => {
                    item.subItems = Array.isArray(item.subItems) ? item.subItems : [];

                    const subEl = document.getElementById(`mat-sub-${cIdx}-${iIdx}`);

                    if (subEl) {
                        new Sortable(subEl, {
                            handle: ".drag-handle-sub",
                            animation: 150,
                            onEnd: function (evt) {
                                if (
                                    evt.oldIndex === undefined ||
                                    evt.newIndex === undefined ||
                                    evt.oldIndex === evt.newIndex
                                ) {
                                    return;
                                }

                                const movedSub = item.subItems.splice(evt.oldIndex, 1)[0];
                                if (!movedSub) return;

                                item.subItems.splice(evt.newIndex, 0, movedSub);
                                addHistory(currentRecordIdForMaterials, "Sortierung Unterartikel geändert");
                                saveMaterialState("Sortierung Unterartikel geändert");
                            }
                        });
                    }
                });
            });
        }

        // ---------------------------------------------------------------------
        // NAVIGATION / CRUD
        // ---------------------------------------------------------------------
        window.navigate = function navigate(viewId) {
            document.querySelectorAll(".view-section").forEach(el => el.classList.remove("active"));

            const view = document.getElementById(`view-${viewId}`);

            if (view) {
                view.classList.add("active");
            }

            if (viewId === "list") {
                renderList();
            }

            document.querySelector("main")?.scrollTo(0, 0);
        };

        window.createNew = function createNew(type) {
            closeTypeSelection();

            const formId = type === "PV" ? "form-pv" : "form-wp";
            const form = document.getElementById(formId);

            if (!form) return;

            form.reset();
            document.getElementById(`${type.toLowerCase()}-id`).value = "";

            if (hasSelect2()) {
                $(form).find("select").val(null).trigger("change.select2");
            }

            if (type === "PV") {
                clearRoofs();
                addRoofUI();
            }

            form.querySelectorAll(".hidden").forEach(el => {
                if (el.id.includes("content")) {
                    el.classList.remove("hidden");

                    const iconId = el.id.replace("-content-", "-icon-");
                    const icon = document.getElementById(iconId);

                    if (icon) {
                        icon.classList.remove("rotate-180");
                    }
                }
            });

            initAllSelect2(form);
            refreshRadioSelect2Mirrors(form);
            refreshNormalSelect2(form);
            updateFormProgress(type);
            navigate(`form-${type.toLowerCase()}`);
        };

        function setFormFieldValue(field, value) {
            if (value === null || value === undefined) return;

            if (field.length && !field.tagName) {
                Array.from(field).forEach(input => {
                    if (input.type === "radio") {
                        input.checked = String(input.value) === String(value);
                    }

                    if (input.type === "checkbox") {
                        input.checked = ["1", "true", "ja", "yes", "on"].includes(String(value).toLowerCase());
                    }
                });

                return;
            }

            if (field.type === "checkbox") {
                field.checked = ["1", "true", "ja", "yes", "on"].includes(String(value).toLowerCase());
                return;
            }

            if (field.type === "radio") {
                field.checked = String(field.value) === String(value);
                return;
            }

            field.value = value;

            if (hasSelect2() && field.tagName === "SELECT" && $(field).hasClass("select2-hidden-accessible")) {
                $(field).trigger("change.select2");
            }
        }

        window.editRecord = function editRecord(id) {
            const record = getRecord(id);
            if (!record) return;

            const type = record.type;
            const formId = type === "PV" ? "form-pv" : "form-wp";
            const form = document.getElementById(formId);

            if (!form) return;

            form.reset();

            if (hasSelect2()) {
                $(form).find("select").val(null).trigger("change.select2");
            }

            document.getElementById(`${type.toLowerCase()}-id`).value = record.id;

            // Check if the measurement is completed
            const isCompleted = record.status === 'completed';

            if (type === "PV") {
                clearRoofs();
            }

            const data = record.data || {};

            Object.keys(data).forEach(key => {
                if (key === "roofs" && type === "PV") {
                    if (Array.isArray(data.roofs) && data.roofs.length > 0) {
                        // Pass 'isCompleted' to the roof function
                        data.roofs.forEach(roofData => addRoofUI(roofData, isCompleted));
                    } else {
                        addRoofUI(null, isCompleted);
                    }
                    return;
                }

                const field = form.elements[key];

                if (!field) {
                    return;
                }

                setFormFieldValue(field, data[key]);
            });

            if (type === "PV") {
                const roofContainer = document.getElementById("roofs-container");

                if (!roofContainer || roofContainer.children.length === 0) {
                    addRoofUI(null, isCompleted);
                }
            }

            initAllSelect2(form);
            refreshRadioSelect2Mirrors(form);
            refreshNormalSelect2(form);
            updateFormProgress(type);
            
            // --- READ-ONLY LOGIC FOR COMPLETED MEASUREMENTS ---
            
            // 1. Disable all inputs, selects, and textareas
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });

            // Then apply readonly mode only if completed
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = isCompleted;
            });

            // 2. Hide "Save" buttons (bottom and sticky top)
            const submitBtn = document.getElementById(`btn-submit-${type.toLowerCase()}`);
            if (submitBtn) submitBtn.style.display = isCompleted ? 'none' : 'flex';

            const topSaveBtn = document.querySelector(`#view-form-${type.toLowerCase()} .sticky button[onclick*="btn-submit"]`);
            if (topSaveBtn) topSaveBtn.style.display = isCompleted ? 'none' : 'flex';

            // 3. Hide "+ Add roof" button
            const addRoofBtn = document.querySelector(`#view-form-${type.toLowerCase()} button[onclick*="addRoofUI"]`);
            if (addRoofBtn) addRoofBtn.style.display = isCompleted ? 'none' : 'flex';

            // 4. Hide dictation microphones
            form.querySelectorAll('button[onclick*="toggleDictation"]').forEach(btn => {
                btn.style.display = isCompleted ? 'none' : 'block';
            });
            
            // Visually update Select2 to "disabled"
            if (hasSelect2()) {
                $(form).find("select").trigger("change.select2");
            }
            // ----------------------------------------------------

            navigate(`form-${type.toLowerCase()}`);
        };

        window.saveRecord = async function saveRecord(event, type) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            if (type === "PV") {
                const roofEntries = document.querySelectorAll("#roofs-container .roof-entry");

                if (roofEntries.length === 0) {
                    alert("Bitte lege mindestens eine Dachfläche an.");
                    return;
                }
            }

            const id = formData.get("id");

            if (!id) {
                alert("Dieses Aufmaß hat noch keine Datenbank-ID. Bitte zuerst ein bestehendes Aufmaß öffnen.");
                return;
            }

            const customerNameFallback = `${formData.get("name") || ""} ${formData.get("lastname") || ""}`.trim();

            const dataObj = {};

            if (type === "PV") {
                dataObj.roofs = [];
            }

            for (const [key, value] of formData.entries()) {
                if (key === "id") continue;

                if (type === "PV" && key.startsWith("roof_")) {
                    const parts = key.split("_");
                    const idx = parseInt(parts[1], 10);
                    const field = parts.slice(2).join("_");

                    if (!dataObj.roofs[idx]) {
                        dataObj.roofs[idx] = {};
                    }

                    dataObj.roofs[idx][field] = value;
                } else {
                    dataObj[key] = value;
                }
            }

            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (!cb.name) return;

                if (type === "PV" && cb.name.startsWith("roof_")) {
                    const parts = cb.name.split("_");
                    const idx = parseInt(parts[1], 10);
                    const field = parts.slice(2).join("_");

                    if (!dataObj.roofs[idx]) {
                        dataObj.roofs[idx] = {};
                    }

                    dataObj.roofs[idx][field] = cb.checked ? 1 : 0;
                } else {
                    dataObj[cb.name] = cb.checked ? 1 : 0;
                }
            });

            if (type === "PV") {
                dataObj.roofs = dataObj.roofs.filter(Boolean);
            }

            const submitButton = type === "PV"
                ? document.getElementById("btn-submit-pv")
                : document.getElementById("btn-submit-wp");

            const oldButtonHtml = submitButton ? submitButton.innerHTML : "";

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <i class="ph-bold ph-spinner-gap text-2xl animate-spin"></i>
                    Wird gespeichert...
                `;
            }

            try {
                const response = await fetch(getDetailSaveUrl(id), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        type,
                        data: dataObj
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Aufmaß konnte nicht gespeichert werden.");
                }

                const index = records.findIndex(record => String(record.id) === String(id));

                if (index > -1) {
                    records[index].customerName = customerNameFallback;
                    records[index].data = {
                        ...(records[index].data || {}),
                        ...dataObj
                    };

                    addHistory(id, `${type} Formular gespeichert`);
                }   

                navigate("list");
            } catch (error) {
                console.error(error);
                alert(error.message || "Fehler beim Speichern.");
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = oldButtonHtml;
                }
            }
        };

        window.deleteRecord = async function deleteRecord(id) {
            const record = getRecord(id);

            if (!record) {
                return;
            }

            if (record.status === "completed") {
                alert("Dieses Aufmaß ist abgeschlossen und gesperrt. Zum Löschen muss es zuerst entsperrt werden.");
                return;
            }

            if (!confirm("Möchten Sie dieses Aufmaß wirklich löschen?")) {
                return;
            }

            try {
                const response = await fetch(getDeleteUrl(id), {
                    method: "DELETE",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Aufmaß konnte nicht gelöscht werden.");
                }

                records = records.filter(record => String(record.id) !== String(id));

                const searchTerm = (document.getElementById("search-input")?.value || "").toLowerCase().trim();
                const filteredCount = getFilteredRecords(searchTerm).length;
                const totalPages = Math.max(1, Math.ceil(filteredCount / recordsPerPage));

                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }

                renderList();
            } catch (error) {
                console.error(error);
                alert(error.message || "Fehler beim Löschen.");
            }
        };

        // ---------------------------------------------------------------------
        // PAGINATION / LIST
        // ---------------------------------------------------------------------
        function getFilteredRecords(searchTerm = "") {
            if (!searchTerm) {
                return records;
            }

            return records.filter(record => {
                const searchStr = `
                    ${record.customerName || ""}
                    ${record.data?.city || ""}
                    ${record.data?.street || ""}
                    ${record.data?.firma || ""}
                    ${record.type || ""}
                    ${record.measurementNo || ""}
                    ${record.orderNo || ""}
                    ${record.offerNo || ""}
                    ${record.productName || ""}
                `.toLowerCase();

                return searchStr.includes(searchTerm);
            });
        }

        window.goToPage = function goToPage(page) {
            currentPage = page;
            renderList();

            const main = document.querySelector("main");
            const listView = document.getElementById("view-list");

            if (main && listView) {
                main.scrollTo({
                    top: listView.offsetTop,
                    behavior: "smooth"
                });
            }
        };

        function renderPagination(totalItems) {
            const paginationContainer = document.getElementById("pagination-container");
            if (!paginationContainer) return;

            const totalPages = Math.ceil(totalItems / recordsPerPage);

            if (totalPages <= 1) {
                paginationContainer.innerHTML = "";
                return;
            }

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            const startItem = ((currentPage - 1) * recordsPerPage) + 1;
            const endItem = Math.min(currentPage * recordsPerPage, totalItems);

            let pagesHtml = "";
            const maxVisibleButtons = 5;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

            if (endPage - startPage < maxVisibleButtons - 1) {
                startPage = Math.max(1, endPage - maxVisibleButtons + 1);
            }

            if (startPage > 1) {
                pagesHtml += `
                    <button onclick="goToPage(1)" class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 transition">1</button>
                `;

                if (startPage > 2) {
                    pagesHtml += `<span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold">...</span>`;
                }
            }

            for (let page = startPage; page <= endPage; page++) {
                const activeClass = page === currentPage
                    ? "bg-slate-900 text-white border-slate-900 shadow-md"
                    : "bg-white text-slate-700 border-slate-200 hover:bg-slate-100";

                pagesHtml += `
                    <button onclick="goToPage(${page})" class="w-10 h-10 rounded-xl border font-bold transition ${activeClass}">
                        ${page}
                    </button>
                `;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    pagesHtml += `<span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold">...</span>`;
                }

                pagesHtml += `
                    <button onclick="goToPage(${totalPages})" class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 transition">
                        ${totalPages}
                    </button>
                `;
            }

            paginationContainer.innerHTML = `
                <div class="text-sm text-slate-500 font-semibold text-center md:text-left">
                    Zeige <span class="text-slate-900 font-bold">${startItem}</span> bis
                    <span class="text-slate-900 font-bold">${endItem}</span> von
                    <span class="text-slate-900 font-bold">${totalItems}</span> Aufmaßen
                </div>

                <div class="flex items-center justify-center md:justify-end gap-2 flex-wrap">
                    <button
                        onclick="goToPage(${currentPage - 1})"
                        ${currentPage <= 1 ? "disabled" : ""}
                        class="h-10 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="ph-bold ph-caret-left"></i>
                    </button>

                    ${pagesHtml}

                    <button
                        onclick="goToPage(${currentPage + 1})"
                        ${currentPage >= totalPages ? "disabled" : ""}
                        class="h-10 px-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="ph-bold ph-caret-right"></i>
                    </button>
                </div>
            `;
        }

        window.completeMeasurement = async function completeMeasurement(id, btn) {
            if (!confirm("Möchten Sie dieses Aufmaß wirklich als erledigt markieren? Die Daten werden im zugehörigen Deal aktualisiert.")) {
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="ph-bold ph-spinner-gap animate-spin text-lg"></i> Lädt...`;

            try {
                const url = COMPLETE_URL_TEMPLATE.replace("__ID__", encodeURIComponent(id));
                
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Fehler beim Abschließen des Aufmaßes.");
                }

                // Update local state
                const recordIndex = records.findIndex(r => String(r.id) === String(id));
                if (recordIndex > -1) {
                    records[recordIndex].status = 'completed';
                    addHistory(id, "Aufmaß als erledigt markiert.");
                }

                alert("Aufmaß erfolgreich abgeschlossen!");
                renderList(); // Re-render to show the updated badge
                
            } catch (error) {
                console.error(error);
                alert(error.message);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        };

        window.unlockMeasurement = async function unlockMeasurement(id, btn) {
            if (!confirm("Möchten Sie dieses abgeschlossene Aufmaß wirklich entsperren? Diese Aktion wird in der Historie gespeichert.")) {
                return;
            }

            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `<i class="ph-bold ph-spinner-gap animate-spin text-lg"></i> Entsperrt...`;

            try {
                const response = await fetch(getUnlockUrl(id), {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (response.status === 409) {
                    // M5: Aufmaß war nicht gesperrt (Doppelklick/Stale-Tab) -> Status still re-syncen, kein Error-Alert.
                    const idx409 = records.findIndex(record => String(record.id) === String(id));
                    if (idx409 > -1) {
                        records[idx409].status = data.status || "open";
                    }
                    renderList();
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    return;
                }

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Aufmaß konnte nicht entsperrt werden.");
                }

                const recordIndex = records.findIndex(record => String(record.id) === String(id));

                if (recordIndex > -1) {
                    records[recordIndex].status = data.status || "open";
                    addHistory(id, "Aufmaß wurde entsperrt");
                }

                renderList();
            } catch (error) {
                console.error(error);
                alert(error.message || "Fehler beim Entsperren.");

                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        };


        function renderList() {
            const listContainer = document.getElementById("full-list-container");
            const paginationContainer = document.getElementById("pagination-container");

            if (!listContainer) return;

            listContainer.innerHTML = "";
            if (paginationContainer) paginationContainer.innerHTML = "";

            const searchTerm = (document.getElementById("search-input")?.value || "").toLowerCase().trim();
            const filteredRecords = getFilteredRecords(searchTerm);

            const totalItems = filteredRecords.length;
            const totalPages = Math.ceil(totalItems / recordsPerPage);

            if (currentPage > totalPages && totalPages > 0) {
                currentPage = totalPages;
            }

            if (currentPage < 1) {
                currentPage = 1;
            }

            if (totalItems === 0) {
                listContainer.innerHTML = `
                    <div class="col-span-full text-center py-16 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                        <i class="ph ph-clipboard-text text-slate-300 text-5xl mb-3"></i>
                        <p class="text-slate-500 font-medium">Keine Aufmaße gefunden.</p>
                        ${searchTerm ? "" : `<button onclick="showTypeSelection()" class="mt-4 text-brand-blue text-sm font-semibold hover:underline">Neues Aufmaß anlegen</button>`}
                    </div>
                `;
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage;
            const paginatedRecords = filteredRecords.slice(startIndex, startIndex + recordsPerPage);

            paginatedRecords.forEach(record => {
                const isPV = record.type === "PV";
                const isWP = record.type === "WP";

                const dateStr = record.date ? new Date(record.date).toLocaleDateString("de-DE") : "-";

                const icon = isPV
                    ? "ph-solar-panel text-brand-orange"
                    : isWP
                        ? "ph-thermometer text-brand-green"
                        : "ph-clipboard-text text-brand-blue";

                const bg = isPV
                    ? "bg-brand-orange/20"
                    : isWP
                        ? "bg-brand-lightGreen/40"
                        : "bg-brand-lightBlue/40";

                const tagBg = isPV
                    ? "bg-brand-orange text-white"
                    : isWP
                        ? "bg-brand-green text-white"
                        : "bg-brand-blue text-white";

                const imgCount = record.images ? record.images.length : 0;
                const notesCount = Array.isArray(record.notes) ? record.notes.length : (record.noteCount || 0);
                const employeeName = record.responsibleName || record.updatedByName || record.createdByName || "System";
                const employeeImage = record.responsibleImage || record.updatedByImage || record.createdByImage || PLACEHOLDER_IMAGE;
                const team = Array.isArray(record.team) && record.team.length
                    ? record.team
                    : [{
                        id: record.responsibleId || record.updatedById || record.createdById || "system",
                        name: employeeName,
                        image: employeeImage,
                        role: "Verantwortlich"
                    }];

                const teamHtml = team.slice(0, 5).map(member => `
                    <span class="sa-team-chip" title="${esc(member.name || "Mitarbeiter")} · ${esc(member.role || "Team")}">
                        <img src="${esc(member.image || PLACEHOLDER_IMAGE)}" class="sa-team-avatar" onerror="this.onerror=null; this.src='${PLACEHOLDER_IMAGE}'" alt="">
                        <span class="truncate max-w-[130px]">${esc(member.name || "Mitarbeiter")}</span>
                        <span class="sa-team-chip-role">${esc(member.role || "Team")}</span>
                    </span>
                `).join("") + (team.length > 5 ? `<span class="text-xs font-black text-slate-500">+${team.length - 5}</span>` : "");

                const fullName = `${record.data?.firma || ""} ${record.data?.name || ""} ${record.data?.lastname || ""}`.trim()
                    || record.customerName
                    || "Unbenannt";

                let detailsHtml = "";

                if (record.measurementNo) {
                    detailsHtml += `<span class="col-span-2 truncate flex gap-2 items-center"><i class="ph ph-hash"></i> <strong>${esc(record.measurementNo)}</strong></span>`;
                }

                if (record.orderNo) {
                    detailsHtml += `<span class="truncate flex gap-2 items-center"><i class="ph ph-briefcase"></i> Auftrag: ${esc(record.orderNo)}</span>`;
                }

                if (record.offerNo) {
                    detailsHtml += `<span class="truncate flex gap-2 items-center"><i class="ph ph-file-text"></i> Angebot: ${esc(record.offerNo)}</span>`;
                }

                if (record.data?.street || record.data?.city) {
                    detailsHtml += `
                        <span class="col-span-2 truncate flex gap-2 items-center">
                            <i class="ph ph-map-pin"></i>
                            ${esc(record.data?.street || "")}${record.data?.street && record.data?.city ? ", " : ""}${esc(record.data?.city || "")}
                        </span>
                    `;
                }

                if (record.productName) {
                    detailsHtml += `<span class="col-span-2 truncate flex gap-2 items-center"><i class="ph ph-package"></i> ${esc(record.productName)}</span>`;
                }

                if (!detailsHtml) {
                    detailsHtml = `<span class="col-span-2 text-slate-400 italic">Keine Details vorhanden</span>`;
                }

                // --- THIS IS WHERE THE VARIABLES BELONG ---
                const isCompleted = record.status === 'completed';
                const editBtnText = isCompleted ? 'Ansehen' : 'Bearbeiten';
                const editBtnIcon = isCompleted ? 'ph-eye' : 'ph-pencil-simple';
                const editBtnColor = isCompleted ? 'bg-brand-blue text-white hover:bg-blue-600' : 'bg-slate-900 text-white hover:bg-slate-800';

                listContainer.insertAdjacentHTML("beforeend", `
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-4 hover:shadow-md transition relative">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-xl ${bg}">
                                    <i class="ph-fill ${icon} text-2xl"></i>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">${esc(fullName)}</h3>

                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-500 flex items-center gap-1">
                                            <i class="ph ph-calendar-blank"></i> ${esc(dateStr)}
                                        </span>

                                        <span class="text-xs text-slate-500 flex items-center gap-1">
                                            <img
                                                src="${esc(employeeImage)}"
                                                class="w-5 h-5 rounded-full object-cover border border-slate-200 bg-white"
                                                onerror="this.onerror=null; this.src='${PLACEHOLDER_IMAGE}'">
                                            ${esc(employeeName)}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <span class="text-xs font-bold px-2.5 py-1 rounded-md shadow-sm ${tagBg}">
                                ${esc(record.type)}
                            </span>
                            ${record.status === 'completed' 
                                ? `<span class="text-xs font-bold px-2.5 py-1 rounded-md shadow-sm bg-emerald-100 text-emerald-700 border border-emerald-200 ml-2 flex items-center gap-1"><i class="ph-fill ph-check-circle"></i> Erledigt</span>`
                                : `<span class="text-xs font-bold px-2.5 py-1 rounded-md shadow-sm bg-slate-100 text-slate-600 border border-slate-200 ml-2">Offen</span>`
                            }
                        </div>

                        <div class="text-sm text-slate-600 grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            ${detailsHtml}
                        </div>

                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-black uppercase tracking-wide text-slate-500 flex items-center gap-1">
                                    <i class="ph-fill ph-users-three text-brand-green"></i> Verantwortliches Team
                                </span>
                                <span class="text-[10px] font-black text-slate-400">${team.length} Person(en)</span>
                            </div>
                            <div class="sa-team-strip">${teamHtml}</div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-2 pt-2 border-t border-slate-100">
                            ${record.status !== 'completed' ? `
                                <button onclick="deleteRecord('${esc(record.id)}')" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Löschen">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            ` : `
                                <button type="button" disabled class="p-2 text-slate-300 bg-slate-50 rounded-lg cursor-not-allowed" title="Gesperrt">
                                    <i class="ph-bold ph-lock-key text-lg"></i>
                                </button>
                            `}  

                            <button onclick="openHistory('${esc(record.id)}')" class="px-3 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-clock-counter-clockwise text-lg text-slate-500"></i> Historie
                            </button>

                            <button onclick="openNotes('${esc(record.id)}')" class="px-3 py-2 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-note-pencil text-lg text-emerald-600"></i> Notizen
                                ${notesCount > 0 ? `<span class="bg-emerald-600 text-white text-[10px] px-1.5 py-0.5 rounded-full">${notesCount}</span>` : ""}
                            </button>

                            <button onclick="openImages('${esc(record.id)}')" class="px-3 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-camera text-lg text-brand-blue"></i> Fotos
                                ${imgCount > 0 ? `<span class="bg-brand-blue text-white text-[10px] px-1.5 py-0.5 rounded-full">${imgCount}</span>` : ""}
                            </button>

                            <button onclick="openMaterials('${esc(record.id)}')" class="px-3 py-2 bg-brand-lightBlue/30 text-brand-blue text-sm font-semibold rounded-xl hover:bg-brand-lightBlue/50 transition flex items-center gap-1.5">
                                <i class="ph-bold ph-package text-lg"></i> Material
                            </button>

                            <button onclick="editRecord('${esc(record.id)}')" class="px-4 py-2 ${editBtnColor} text-sm font-semibold rounded-xl transition flex items-center gap-1.5">
                                <i class="ph-bold ${editBtnIcon} text-lg"></i> ${editBtnText}
                            </button>                    
                            ${record.status !== 'completed' ? `
                                    <button onclick="completeMeasurement('${esc(record.id)}', this)" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20 flex items-center gap-1.5 ml-auto">
                                        <i class="ph-bold ph-check text-lg"></i> Abschließen
                                    </button>
                                ` : `
                                    <button onclick="unlockMeasurement('${esc(record.id)}', this)" class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition shadow-md shadow-amber-500/20 flex items-center gap-1.5 ml-auto">
                                        <i class="ph-bold ph-lock-open text-lg"></i> Entsperren
                                    </button>
                                `}  
                        </div>
                    </div>
                `);
            });

            renderPagination(totalItems);
        }
         
        window.renderList = renderList;
    })();
</script>

<script>
    function toggleSaQuickMenu(force){
        const sider = document.getElementById('saQuickSider');
        const overlay = document.getElementById('saQuickOverlay');
        if(!sider || !overlay) return;
        const shouldOpen = typeof force === 'boolean' ? force : !sider.classList.contains('show');
        sider.classList.toggle('show', shouldOpen);
        overlay.classList.toggle('show', shouldOpen);
        sider.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
        document.body.classList.toggle('overflow-hidden', shouldOpen);
    }
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') toggleSaQuickMenu(false);
    });
</script>
</body>
</html>

 