<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA-DESK - Einsatzplanung</title> 
    <meta name="planner-base-url" content="{{ url('/planner') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brandDark: '#164191',
                        sky: '#74b2d4',
                        actionGreen: '#93c21c',
                        background: '#f8fafc',
                    }
                }
            }
        }
    </script>

    <!-- Styles -->
    <style>
        body { background-color: #f8fafc; background-image: radial-gradient(#74b2d4 0.5px, transparent 0.5px), radial-gradient(#74b2d4 0.5px, #f8fafc 0.5px); background-size: 20px 20px; }
        .glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07); }
        .glass-card { background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s; }
        .glass-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        
        /* Select2 Customization */
        .select2-container--default .select2-selection--single { height: 42px; border-radius: 0.5rem; border: 1px solid #e2e8f0; display:flex; align-items:center; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 8px; right: 10px; }
        .select2-container--default .select2-selection--multiple { min-height: 42px; border-radius: 0.5rem; border: 1px solid #e2e8f0; }
        .select2-dropdown { border-radius: 0.5rem; border: 1px solid #e2e8f0; z-index: 9999999 !important; }
        
        .gantt-bar {cursor: grab; position: absolute; height: 36px; top: 6px; border-radius: 6px; display: flex; align-items: center; padding: 0 10px; font-size: 11px; font-weight: 600; cursor: pointer; z-index: 10; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
        .gantt-bar:hover { z-index: 20; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .gantt-bar:active {
              cursor: grabbing;
              z-index: 50; /* Bring to front while dragging */
              box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
          }
        .sortable-ghost { opacity: 0.4; background: #cfe09b; border: 2px dashed #93c21c; }
        .tab-btn.active { border-bottom-color: #164191; color: #164191; font-weight: 700; border-bottom-width: 2px; }
        .nav-link.active { background-color: #f1f5f9; color: #164191; font-weight: 700; }
        .backlog-filter.active { background-color: #cbd5e1; color: #1e293b; border-color: #94a3b8; }
        
        /* Date Picker Reset */
        input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; }
        
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }

        /* Gantt Specific Styles */
        #gantt-svg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Let clicks pass through to bars */
            z-index: 10;
        }

        .dependency-line {
            fill: none;
            stroke: #94a3b8;
            stroke-width: 2px;
            opacity: 0.6;
            transition: stroke 0.2s;
        }

        .dependency-line:hover {
            stroke: #164191;
            stroke-width: 3px;
            opacity: 1;
            cursor: pointer;
            pointer-events: stroke;
        }

        .gantt-bar-handle {
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background: #fff;
            border: 2px solid #164191;
            border-radius: 50%;
            cursor: crosshair;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 30;
        }

        .gantt-bar:hover .gantt-bar-handle {
            opacity: 1;
        }

        .gantt-bar-handle i {
            font-size: 8px;
            color: #164191;
        }

        /* List View Transitions */
        details > summary {
            list-style: none;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
        details[open] summary ~ * {
            animation: sweep .3s ease-in-out;
        }
        @keyframes sweep {
            0%    {opacity: 0; transform: translateY(-10px)}
            100%  {opacity: 1; transform: translateY(0)}
        }
        .list-type-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }


        /* Gantt Employee Avatars */
      .gantt-avatars {
          display: flex;
          margin-right: 8px;
          margin-left: 4px;
      }
      .gantt-avatars img {
          width: 20px;
          height: 20px;
          border-radius: 50%;
          border: 2px solid white;
          margin-left: -8px; /* Overlap effect */
          object-fit: cover;
      }
      .gantt-avatars img:first-child {
          margin-left: 0;
      }

      /* Status Styles in Gantt */
      .gantt-bar.status-done {
          background-color: #dcfce7 !important; /* Green-100 */
          border-color: #22c55e !important;    /* Green-500 */
      }
      .gantt-bar.status-done .gantt-title {
          color: #15803d !important;           /* Green-700 */
          text-decoration: line-through;
      }

    </style>
</head>
<body class="text-slate-800 h-screen overflow-hidden flex flex-col font-sans">

    <!-- Header -->
    <header class="glass-panel z-50 px-6 py-3 flex items-center justify-between sticky top-0 shadow-sm">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brandDark rounded-xl flex items-center justify-center text-white shadow-lg shadow-brandDark/20">
                    <i class="fa-solid fa-bolt text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-brandDark tracking-tight">Nuri <span class="text-sky">Head</span></h1>
            </div>

            <div class="hidden md:flex items-center gap-4 border-l border-slate-300 pl-6"> 
                <div class="relative min-w-[260px]" id="customer-select-container">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-user-tie"></i></span>
                        <input type="text" id="customer-search-input" placeholder="Kunde suchen..." class="w-full pl-10 pr-9 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brandDark/20 transition hover:bg-white" autocomplete="off" onfocus="showCustomerDropdown()" oninput="filterCustomerDropdown()">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-chevron-down text-xs transition-transform" id="customer-chevron"></i></span>
                    </div>
                    <div id="customer-dropdown-list" class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto hidden z-50"></div>
                </div>

                <div class="relative min-w-[300px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-brandDark z-10"><i class="fa-solid fa-building"></i></span>
                    <select id="project-selector" class="w-full pl-10" disabled><option value="">Produkt & Objekt wählen...</option></select>
                </div>
                <button id="btn-object-data" onclick="openObjectDataModal()" disabled 
                    class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-400 cursor-not-allowed shadow-sm transition-all">
                    <i class="feather icon-home fa-solid fa-house-chimney"></i> 
                    Objektdaten
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div id="plan-status-indicator" class="hidden px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                Sync Aktiv
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <div class="px-6 py-2 bg-white border-b border-slate-200 flex justify-between items-center">
        <!-- View Switcher -->
        <div class="flex gap-2">
            <button onclick="switchMainTab('planning')" id="nav-planning" class="nav-link active px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-table-columns"></i> Planungstafel
            </button>
            <button onclick="switchMainTab('attendance')" id="nav-attendance" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-users-viewfinder"></i> Anwesenheit
            </button>
        </div>

        <!-- NEW: Time Filter & Navigation -->
        <div class="flex items-center gap-3 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
            <!-- Navigation Arrows -->
            <div class="flex items-center">
                <button onclick="changeTime(-1)" class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-sm text-slate-500 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                
                <!-- Date Picker / Display -->
                <div class="relative mx-1 group">
                    <input type="date" id="nav-date-picker" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="setDateManually(this.value)">
                    <button class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white shadow-sm border border-slate-200 text-xs font-bold text-slate-700 group-hover:border-sky-300 transition-colors">
                        <i class="fa-regular fa-calendar text-sky-500"></i>
                        <span id="nav-date-display">Heute</span>
                    </button>
                </div>

                <button onclick="changeTime(1)" class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-sm text-slate-500 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>

            <div class="h-6 w-px bg-slate-300 mx-1"></div>

            <!-- Presets -->
            <div class="flex gap-1">
                <select id="time-mode-select" class="text-xs font-bold bg-transparent border-none text-slate-600 focus:ring-0 cursor-pointer" onchange="setFilterMode(this.value)">
                    <option value="day" selected>Heute / Tag</option>
                    <option value="tomorrow">Morgen</option>
                    <option value="week">Diese Woche</option>
                    <option value="month">Diesen Monat</option>
                    <option value="next_4_weeks">Nächste 4 Wochen</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Workspace -->
    <main class="flex-1 p-4 md:p-6 overflow-hidden flex gap-6">
        
        <!-- VIEW: PLANNING -->
        <div id="main-tab-planning" class="main-tab flex h-full w-full gap-6">
            
            <!-- LEFT SIDEBAR -->
            <section class="w-1/3 max-w-sm flex flex-col gap-4 h-full">
                <div class="flex items-center gap-2 bg-white/70 rounded-2xl p-2 shadow-sm">
                    <button type="button" class="wf-tab px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide bg-slate-900 text-white flex-1" data-tab="templates">
                        <i class="fa-solid fa-layer-group mr-2"></i> Vorlagen
                    </button>
                    <button type="button" class="wf-tab px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide text-slate-600 hover:bg-slate-100 flex-1" data-tab="backlog">
                        <i class="fa-solid fa-inbox mr-2"></i> Backlog
                    </button>
                </div>

                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" id="task-search" placeholder="Suche..." class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none" oninput="filterSidebar()">
                </div>

                <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto relative">
                    <!-- TEMPLATES (VORLAGEN) -->
                   <div id="wf-tab-templates" class="wf-tab-panel">

                    <!-- SUB TABS: Aufgaben / Material -->
                    <div class="mb-3">
                        <div class="flex gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
                            <button type="button"
                                    class="wf-template-subtab flex-1 px-3 py-2 rounded-lg text-xs font-extrabold bg-white shadow-sm text-brandDark"
                                    data-subtab="tasks">
                                <i class="fa-solid fa-list-check mr-1"></i> Aufgaben
                            </button>
                            <button type="button"
                                    class="wf-template-subtab flex-1 px-3 py-2 rounded-lg text-xs font-extrabold text-slate-500 hover:text-brandDark"
                                    data-subtab="materials">
                                <i class="fa-solid fa-cubes mr-1"></i> Material
                            </button>
                        </div>
                    </div>

                    <!-- SUB PANEL: Aufgaben -->
                    <div id="wf-templates-subtab-tasks" class="wf-templates-subpanel">
                        <div id="templates-analytics" class="grid grid-cols-2 gap-2 mb-4"></div>

                        <div id="templates-list" class="flex flex-col gap-2 pb-10 min-h-[100px]">
                            <div class="text-xs text-slate-400 p-2 italic">Bitte zuerst Projekt wählen.</div>
                        </div>

                        <button onclick="window.addManualTask()"
                                class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus"></i> Manuelle Aufgabe
                        </button>
                    </div>

                    <!-- SUB PANEL: Material (Master Sets Browser) -->
                    <div id="wf-templates-subtab-materials" class="wf-templates-subpanel hidden">

                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <div class="relative">
                                <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                                <input id="wf-masterset-browser-q"
                                    type="text"
                                    class="w-full pl-9 pr-3 py-2 bg-slate-100 border-none rounded-lg text-sm font-semibold focus:ring-2 focus:ring-brandDark/20 outline-none"
                                    placeholder="Master Set suchen...">
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3">
                                <!-- LIST -->
                                <div>
                                    <div class="text-[11px] uppercase font-extrabold text-slate-400 tracking-wider mb-2">
                                        Master Sets
                                    </div>
                                    <div id="wf-masterset-browser-list" class="space-y-2">
                                        <div class="text-xs text-slate-400 italic p-2">Tippe oben zum Suchen…</div>
                                    </div>
                                </div>

                                <!-- DETAILS -->
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">Details</div>
                                        <div id="wf-masterset-browser-totals" class="text-xs font-bold text-slate-600">—</div>
                                    </div>

                                    <div id="wf-masterset-browser-details" class="space-y-3">
                                        <div class="text-xs text-slate-400 italic">Wähle ein Master Set aus der Liste…</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>


                    <!-- BACKLOG (OPEN TASKS) -->
                    <div id="wf-tab-backlog" class="wf-tab-panel hidden">
                       <div id="backlog-analytics" class="grid grid-cols-2 gap-2 mb-4">
                          </div>  
                    </div>
                </div>
            </section>

            <!-- RIGHT PANEL: Board -->
            <section class="flex-1 flex flex-col gap-4 h-full relative min-w-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-brandDark rounded-full"></span>
                        Ressourcenplan
                    </h2>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex bg-white/60 p-1 rounded-lg border border-slate-200">
                            <button onclick="switchView('board')" id="btn-view-board" class="px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark"><i class="fa-solid fa-table-columns"></i></button>
                            <button onclick="switchView('gantt')" id="btn-view-gantt" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark"><i class="fa-solid fa-timeline"></i></button>
                            <button onclick="switchView('list')" id="btn-view-list" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark"><i class="fa-solid fa-list-check"></i></button>
                        </div>
                        <div class="flex items-center gap-2 bg-white px-2 py-1 rounded-xl border border-slate-200 shadow-sm">
                            <div class="flex -space-x-2" id="active-crew-avatars"></div>
                            <button onclick="openCrewModal()" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-brandDark hover:text-white transition-colors cursor-pointer z-10" title="Mitarbeiter hinzufügen">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="view-board" class="view-container grid grid-cols-1 md:grid-cols-3 gap-4 h-full overflow-y-auto pb-4">
                    <!-- Columns injected via JS -->
                </div>
                
                <div id="view-gantt" class="view-container hidden h-full glass-panel rounded-[2rem] flex flex-col overflow-hidden relative">
                    <div class="h-10 bg-white/50 border-b flex items-center pl-48 pr-4 relative z-20 overflow-hidden">
                        <div id="time-scale" class="relative h-full flex items-center"></div>
                    </div>
                    <div id="gantt-body" class="flex-1 overflow-y-auto relative bg-slate-50/30">
                        <div id="gantt-tasks-container" class="relative z-10 min-h-full"></div>
                    </div>
                </div>

                <div id="view-list" class="view-container hidden h-full glass-panel rounded-[2rem] overflow-hidden flex flex-col">
                    <div id="list-body" class="overflow-y-auto flex-1 p-2 space-y-1"></div>
                </div>
            </section>
        </div>

        <!-- VIEW: ATTENDANCE -->
        <div id="main-tab-attendance" class="main-tab hidden h-full w-full gap-6 p-2">
            <div class="w-1/2 glass-panel rounded-3xl flex flex-col overflow-hidden border border-green-200/50">
                <div class="p-4 border-b border-slate-100 bg-green-50/30"><h3 class="font-bold text-green-800">Anwesend</h3></div>
                <div id="list-present-container" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
            </div>
            <div class="w-1/2 glass-panel rounded-3xl flex flex-col overflow-hidden border border-red-200/50">
                <div class="p-4 border-b border-slate-100 bg-red-50/30"><h3 class="font-bold text-red-800">Abwesend / Fertig</h3></div>
                <div id="list-absent-container" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
            </div>
        </div>

    </main>

    <!-- DnD Modal with Select2 -->
    <div id="wf-dnd-assign-modal" class="hidden fixed inset-0 z-[99999]">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="wf-dnd-assign-card" class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl overflow-hidden p-6 space-y-4">
                <h3 class="text-xl font-bold text-slate-800">Planung übernehmen</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block">Projektleiter</label>
                        <select id="wf-dnd-pm" class="w-full border rounded-lg p-2" style="width: 100%"></select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block">Team</label>
                        <select id="wf-dnd-crew" class="w-full border rounded-lg p-2" multiple style="width: 100%"></select>
                    </div>
                    <div><label class="text-xs font-bold text-slate-500 mb-1 block">Datum</label><input type="date" id="wf-dnd-date" class="w-full border rounded-lg p-2 border-slate-200"></div>
                    <div><label class="text-xs font-bold text-slate-500 mb-1 block">Zeit</label><input type="time" id="wf-dnd-time" class="w-full border rounded-lg p-2 border-slate-200"></div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button id="wf-dnd-cancel" class="px-4 py-2 border rounded-lg font-bold text-slate-600 hover:bg-slate-50">Abbrechen</button>
                    <button id="wf-dnd-save" class="px-4 py-2 bg-brandDark text-white rounded-lg font-bold hover:bg-blue-800">Speichern</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Task Detail Modal -->
    <div id="task-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        
        <div id="task-modal-content" class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform duration-300 flex flex-col translate-x-full">
            
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <div class="flex justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aufgabendetails</span>
                    <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-times text-lg"></i></button>
                </div>
                
                <input type="hidden" id="modal-active-item-id">
                
                <input type="text" id="modal-edit-title" class="text-xl md:text-2xl font-bold w-full bg-transparent border-none outline-none mb-3 text-slate-800 placeholder-slate-300 focus:ring-0 px-0" placeholder="Titel der Aufgabe...">
                
                <div class="flex flex-wrap gap-2 mb-4" id="modal-badges"></div>
                
                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 bg-white border border-slate-200 p-3 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-brandDark"></i> 
                        <span id="modal-date-display">—</span>
                    </div>
                    <div class="h-4 w-px bg-slate-200"></div>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock text-brandDark"></i> 
                        <span id="modal-time-display">—</span>
                    </div>
                </div>
            </div>

            <div class="flex border-b border-slate-200 px-4">
                <button onclick="switchModalTab('info')" id="tab-btn-info" class="tab-btn active px-4 py-3 text-sm font-bold text-brandDark border-b-2 border-brandDark transition-colors">Info & Team</button>
                <button onclick="switchModalTab('checklist')" id="tab-btn-checklist" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-brandDark transition-colors border-b-2 border-transparent">Checkliste</button>
            </div>

            <div class="p-6 flex-1 overflow-y-auto bg-white space-y-6">
                
                <div id="tab-info" class="tab-content active space-y-6">
                    
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase mb-3">Zugewiesenes Team</h3>
                        <div id="modal-team-list" class="flex flex-wrap gap-2">
                            </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Beschreibung</h3>
                        <textarea id="modal-edit-description" rows="6" class="w-full p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark/50 outline-none transition-all placeholder-slate-400" placeholder="Keine Beschreibung vorhanden..."></textarea>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Master Set</h3>

                        <button type="button"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 hover:border-brandDark/40 text-brandDark font-extrabold text-sm flex items-center justify-between"
                                onclick="window.MasterSetDrawer?.open?.('item', document.getElementById('modal-active-item-id').value)">
                            Master Set Details öffnen
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                </div>

                <div id="tab-checklist" class="tab-content hidden space-y-4">
                    <div id="modal-subtasks-list" class="space-y-2 text-sm text-slate-500 text-center italic py-4">
                        Keine Checkliste verfügbar.
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                <button onclick="deleteActiveTask()" class="text-red-500 hover:text-red-700 font-bold text-sm flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                    <i class="fa-solid fa-trash"></i> Löschen
                </button>
                <button onclick="saveActiveTask()" class="bg-brandDark hover:bg-blue-800 text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-brandDark/20 transition-all transform hover:scale-105">
                    Speichern
                </button>
            </div>
        </div>
    </div>
        <!-- Crew Modal -->
        <div id="crew-modal" class="fixed inset-0 z-[110] hidden">
            <div class="absolute inset-0 bg-slate-900/40" onclick="closeCrewModal()"></div>
            <div class="absolute inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl p-6 flex flex-col z-50">
                <div class="flex justify-between border-b pb-4 mb-4"><h2 class="text-xl font-bold">Manager verwalten</h2><button onclick="closeCrewModal()"><i class="fa-solid fa-xmark"></i></button></div>
                <input type="text" id="crew-search" placeholder="Suchen..." class="w-full mb-3 p-2 border rounded-lg">
                <div id="crew-list-container" class="flex-1 overflow-y-auto space-y-2"></div>
                <div class="pt-4 border-t mt-2">
                    <button onclick="saveCrewSelection()" class="w-full bg-brandDark text-white font-bold py-2 rounded-lg">Speichern</button>
                </div>
            </div>
        </div>


    <div id="dependency-modal" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closeDependencyModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white shadow-2xl rounded-2xl p-6 z-50">
            <h3 class="text-lg font-bold mb-4">Abhängigkeit hinzufügen</h3>
            <input type="hidden" id="dep-from-id">
            <input type="hidden" id="dep-to-id">
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Grund (Optional)</label>
                <input type="text" id="dep-reason" class="w-full border border-slate-200 rounded-lg p-2 text-sm" placeholder="z.B. Muss vorher fertig sein">
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeDependencyModal()" class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-lg">Abbrechen</button>
                <button onclick="saveDependency()" class="px-4 py-2 bg-brandDark text-white font-bold rounded-lg hover:bg-blue-800">Verbinden</button>
            </div>
        </div>
    </div>


    <div id="manual-task-modal" class="hidden fixed inset-0 z-[110]">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeManualTaskModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-plus-circle text-brandDark mr-2"></i> Neue Aufgabe erstellen</h3>
                    <button onclick="closeManualTaskModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Titel</label>
                            <input type="text" id="manual-title" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:ring-2 focus:ring-brandDark/20 outline-none" placeholder="Aufgabe eingeben...">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Datum</label>
                            <input type="date" id="manual-date" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zeit</label>
                                <input type="time" id="manual-time" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm" value="08:00">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dauer (Min)</label>
                                <input type="number" id="manual-duration" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm" value="60">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Zuweisung</label>
                        <select id="manual-employees" class="w-full" multiple="multiple" style="width: 100%"></select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Beschreibung</label>
                        <textarea id="manual-description" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brandDark/20 outline-none" placeholder="Details..."></textarea>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="manual-is-bulk" class="w-4 h-4 accent-brandDark" onchange="toggleBulkMode()">
                                <label for="manual-is-bulk" class="text-sm font-bold text-slate-700 cursor-pointer">Bulk Aufgabe (Mehrere Schritte)</label>
                            </div>
                            <button id="btn-add-step" class="text-xs bg-white border border-slate-200 px-2 py-1 rounded-lg text-slate-600 font-bold hover:text-brandDark hidden" onclick="addBulkStep()">
                                <i class="fa-solid fa-plus mr-1"></i> Schritt
                            </button>
                        </div>

                        <div id="bulk-steps-container" class="space-y-2 hidden">
                            </div>
                    </div>

                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button onclick="closeManualTaskModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-white transition-colors">Abbrechen</button>
                    <button onclick="submitManualTask()" class="px-5 py-2.5 rounded-xl bg-brandDark text-white font-bold shadow-lg shadow-brandDark/20 hover:bg-blue-800 transition-colors">Erstellen</button>
                </div>
            </div>
        </div>
    </div>

        <!-- =============== MASTER SET DRAWER =============== -->
    <div id="masterSetDrawerOverlay"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-[1px] z-[140] hidden"
        onclick="window.MasterSetDrawer?.close?.()"></div>

    <aside id="masterSetDrawer"
        class="fixed top-0 right-0 h-full w-full max-w-xl bg-white shadow-2xl z-[150] translate-x-full transition-transform duration-300">
    <div class="h-full flex flex-col">

        <!-- Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <div class="text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">Master Set</div>
            <div id="masterSetDrawerTitle" class="text-lg font-bold text-slate-800">Details</div>
        </div>
        <button id="masterSetDrawerClose"
                class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
            <i class="fa-solid fa-xmark"></i>
        </button>
        </div>

        <!-- Tabs -->
        <div class="px-4 pt-3">
        <div class="flex gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
            <button type="button"
                    data-tab="search"
                    class="masterset-tab flex-1 px-3 py-2 rounded-lg text-xs font-extrabold bg-white shadow-sm text-brandDark">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Suche
            </button>
            <button type="button"
                    data-tab="linked"
                    class="masterset-tab flex-1 px-3 py-2 rounded-lg text-xs font-extrabold text-slate-500 hover:text-brandDark">
            <i class="fa-solid fa-link mr-1"></i> Verknüpft
            </button>
        </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
        <!-- Search/List -->
        <div id="masterSetSearchPanel">
            <div class="relative">
            <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            <input id="masterSetSearchInput"
                    type="text"
                    class="w-full pl-9 pr-3 py-2 bg-slate-100 border-none rounded-lg text-sm font-semibold focus:ring-2 focus:ring-brandDark/20 outline-none"
                    placeholder="Master Set suchen...">
            </div>

            <div id="master-set-list-container" class="mt-3 space-y-2">
            <!-- list injected by JS -->
            </div>
        </div>

        <!-- Linked -->
        <div id="masterSetLinkedPanel" class="hidden">
            <div id="master-set-linked-container" class="space-y-2">
            <!-- linked injected by JS -->
            </div>
        </div>

        <!-- Details -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between mb-2">
            <div class="text-xs font-extrabold text-slate-400 uppercase">Zusammenfassung</div>
            <div id="masterSetTotalsMini" class="text-xs font-bold text-slate-600">—</div>
            </div>

            <div id="master-set-details-container" class="space-y-3">
            <div class="text-xs text-slate-400 italic">Wähle ein Master Set aus der Liste…</div>
            </div>
        </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex gap-2">
        <button id="masterSetUnlinkBtn"
                class="flex-1 px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-extrabold text-sm hover:border-brandDark/40">
            Entfernen
        </button>
        <button id="masterSetLinkBtn"
                class="flex-1 px-4 py-2 rounded-xl bg-brandDark text-white font-extrabold text-sm hover:bg-blue-800">
            Verknüpfen
        </button>
        </div>

    </div>
    </aside>


    <div id="object-data-modal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeObjectDataModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">
                    <i class="fa-solid fa-house-chimney text-brandDark mr-2"></i> Energieverbrauch & Objektdaten
                </h3>
                <button onclick="closeObjectDataModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="px-6 pt-4 pb-2 bg-white">
                <div class="flex justify-between items-center relative">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -z-0"></div>

                    <button onclick="switchObjectTab(1)" class="obj-tab-btn active relative z-10 flex flex-col items-center group" data-step="1">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-brandDark shadow-md flex items-center justify-center mb-2 transition-colors group-hover:bg-slate-50">
                            <img src="https://nuri-head.de/images/icons/dokumente.svg" class="w-8">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-brandDark bg-white px-1">Objektdaten</span>
                    </button>

                    <button onclick="switchObjectTab(2)" class="obj-tab-btn relative z-10 flex flex-col items-center group" data-step="2">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center mb-2 transition-colors group-hover:border-brandDark/50">
                            <img src="https://nuri-head.de/images/icons/haus_schraeg.svg" class="w-8">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 bg-white px-1">Dach</span>
                    </button>

                    <button onclick="switchObjectTab(3)" class="obj-tab-btn relative z-10 flex flex-col items-center group" data-step="3">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center mb-2 transition-colors group-hover:border-brandDark/50">
                            <img src="https://nuri-head.de/images/articles/warmpumpe.png" class="w-10">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 bg-white px-1">Heizung</span>
                    </button>

                    <button onclick="switchObjectTab(4)" class="obj-tab-btn relative z-10 flex flex-col items-center group" data-step="4">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center mb-2 transition-colors group-hover:border-brandDark/50">
                            <img src="https://nuri-head.de/images/articles/battery.png" class="w-10">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 bg-white px-1">E-Mobilität</span>
                    </button>

                    <button onclick="switchObjectTab(5)" class="obj-tab-btn relative z-10 flex flex-col items-center group" data-step="5">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center mb-2 transition-colors group-hover:border-brandDark/50">
                            <img src="https://nuri-head.de/images/icons/zaehler.svg" class="w-8">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 bg-white px-1">Energie</span>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50" id="object-data-form-container">
                
                <div id="obj-step-1" class="obj-tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="form-group"><label>Objektart</label><input type="text" name="object_type" class="std-input" readonly></div>
                        <div class="form-group"><label>Zustand</label><input type="text" name="building_condition" class="std-input" readonly></div>
                        <div class="form-group"><label>Nutzungsart</label><input type="text" name="usage_type" class="std-input" readonly></div>
                        <div class="form-group"><label>Eigentümer</label><input type="text" name="owner_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Wohneinheiten</label><input type="text" name="number_we" class="std-input" readonly></div>
                        <div class="form-group"><label>Personen</label><input type="text" name="person_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Baujahr</label><input type="text" name="building_year" class="std-input" readonly></div>
                        <div class="form-group"><label>Geschosse</label><input type="text" name="story_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Wohnfläche (m²)</label><input type="text" name="heated_area" class="std-input" readonly></div>
                        <div class="form-group"><label>Dämmung</label><input type="text" name="external_insulation_thickness" class="std-input" readonly></div>
                        <div class="form-group"><label>Mauerwerk</label><input type="text" name="masonry" class="std-input" readonly></div>
                        <div class="form-group"><label>Verglasung</label><input type="text" name="window_glazing" class="std-input" readonly></div>
                        <div class="form-group"><label>Rahmen</label><input type="text" name="window_frame" class="std-input" readonly></div>
                        <div class="form-group"><label>Fenster Bj.</label><input type="text" name="window_year" class="std-input" readonly></div>
                        <div class="form-group"><label>Tür Bj.</label><input type="text" name="door_year" class="std-input" readonly></div>
                        <div class="form-group"><label>Tür Zustand</label><input type="text" name="door_condition" class="std-input" readonly></div>
                        <div class="col-span-full"><label>Bemerkung</label><textarea name="object_remark" class="std-input" rows="2" readonly></textarea></div>
                    </div>
                </div>

                <div id="obj-step-2" class="obj-tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="form-group"><label>Dachart</label><input type="text" name="roof_type" class="std-input" readonly></div>
                        <div class="form-group"><label>Eindeckung</label><input type="text" name="roof_covering" class="std-input" readonly></div>
                        <div class="form-group"><label>Neigung</label><input type="text" name="roof_pitch" class="std-input" readonly></div>
                        <div class="form-group"><label>Ausrichtung</label><input type="text" name="roof_direction" class="std-input" readonly></div>
                        <div class="form-group"><label>Ziegelname</label><input type="text" name="tile_name" class="std-input" readonly></div>
                        <div class="form-group"><label>Dachalter</label><input type="text" name="roof_age" class="std-input" readonly></div>
                        <div class="col-span-full"><label>Bemerkung</label><textarea name="roof_remark" class="std-input" rows="2" readonly></textarea></div>
                    </div>
                </div>

                <div id="obj-step-3" class="obj-tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="form-group"><label>Heiztechnik</label><input type="text" name="heating_system_type" class="std-input" readonly></div>
                        <div class="form-group"><label>Heizsystem</label><input type="text" name="heating_type" class="std-input" readonly></div>
                        <div class="form-group"><label>Kamin</label><input type="text" name="chimney" class="std-input" readonly></div>
                        <div class="form-group"><label>Heizkreise</label><input type="text" name="heating_circuits_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Holzverbrauch</label><input type="text" name="wood_consumption" class="std-input" readonly></div>
                        <div class="form-group"><label>Rohranzahl</label><input type="text" name="pipe_system_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Heizung Alter</label><input type="text" name="heating_system_age" class="std-input" readonly></div>
                        <div class="form-group"><label>Menge</label><input type="text" name="quantity" class="std-input" readonly></div>
                        <div class="form-group"><label>Rohrmaterial</label><input type="text" name="pipe_system_material" class="std-input" readonly></div>
                        <div class="form-group"><label>Verbrauch</label><input type="text" name="consumption" class="std-input" readonly></div>
                        <div class="form-group"><label>Solarthermie</label><input type="text" name="solar_thermal" class="std-input" readonly></div>
                        <div class="form-group"><label>Bäderanzahl</label><input type="text" name="bathroom_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Aufstellungsort</label><input type="text" name="installation_location" class="std-input" readonly></div>
                        <div class="form-group"><label>Warmwasser</label><input type="text" name="hot_water_generation" class="std-input" readonly></div>
                        <div class="form-group"><label>Badewannen</label><input type="text" name="bathtub_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Wärmeverbrauch</label><input type="text" name="total_heat_consumption" class="std-input" readonly></div>
                        <div class="form-group"><label>Stromverbrauch</label><input type="text" name="total_electricity_consumption" class="std-input" readonly></div>
                        <div class="form-group"><label>Heizlast</label><input type="text" name="heating_load_calculation" class="std-input" readonly></div>
                        <div class="col-span-full"><label>Bemerkung</label><textarea name="heating_remark" class="std-input" rows="2" readonly></textarea></div>
                    </div>
                </div>

                <div id="obj-step-4" class="obj-tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="form-group"><label>Elektroauto</label><input type="text" name="electric_car" class="std-input" readonly></div>
                        <div class="form-group"><label>Anzahl</label><input type="text" name="electric_car_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Fahrleistung (km)</label><input type="text" name="car_kilo" class="std-input" readonly></div>
                        <div class="form-group"><label>Wallboxen</label><input type="text" name="wallbox_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Montageort</label><input type="text" name="wallbox_location" class="std-input" readonly></div>
                        <div class="form-group"><label>Starkstrom</label><input type="text" name="heavy_current_cable" class="std-input" readonly></div>
                        <div class="form-group"><label>Netzwerk</label><input type="text" name="network_cable" class="std-input" readonly></div>
                        <div class="form-group"><label>Erdarbeiten</label><input type="text" name="groundwork" class="std-input" readonly></div>
                        <div class="form-group"><label>Bidirektional</label><input type="text" name="bidirectional_car" class="std-input" readonly></div>
                        <div class="col-span-full"><label>Bemerkung</label><textarea name="car_remark" class="std-input" rows="2" readonly></textarea></div>
                    </div>
                </div>

                <div id="obj-step-5" class="obj-tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="form-group"><label>Haushaltsstrom</label><input type="text" name="power_household" class="std-input" readonly></div>
                        <div class="form-group"><label>WP-Strom</label><input type="text" name="power_heatpump" class="std-input" readonly></div>
                        <div class="form-group"><label>E-Auto-Strom</label><input type="text" name="power_electric_car" class="std-input" readonly></div>
                        <div class="form-group"><label>Sonstiges</label><input type="text" name="power_other" class="std-input" readonly></div>
                        <div class="form-group col-span-2"><label class="text-sky-600">Gesamtverbrauch</label><input type="text" name="power_total" class="std-input font-bold" readonly></div>
                        <div class="form-group"><label>Zählerschrank</label><input type="text" name="meter_cabinet" class="std-input" readonly></div>
                        <div class="form-group"><label>Anzahl Zähler</label><input type="text" name="meter_count" class="std-input" readonly></div>
                        <div class="form-group"><label>Mieterstrom</label><input type="text" name="tenant_model" class="std-input" readonly></div>
                        <div class="form-group"><label>Aufstellungsort</label><input type="text" name="installation_location_power" class="std-input" readonly></div>
                        <div class="form-group"><label>Netzwerk / WLAN</label><input type="text" name="network_wlan" class="std-input" readonly></div>
                        <div class="col-span-full"><label>Bemerkung</label><textarea name="energy_remark" class="std-input" rows="2" readonly></textarea></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Helper styles for the modal inputs */
    .std-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 600; color: #334155; background-color: #f8fafc; outline: none; }
    .form-group label { display: block; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; margin-bottom: 0.25rem; }
    .obj-tab-btn.active div { border-color: #164191; background-color: #eff6ff; }
    .obj-tab-btn.active span { color: #164191; }
</style>


    <!-- Toast -->
    <div id="toast" class="fixed bottom-10 right-10 bg-brandDark text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-3 z-50">
        <i class="fa-solid fa-circle-check text-actionGreen text-xl"></i><div><h4 class="font-bold text-sm">Erfolgreich</h4><p class="text-xs text-slate-300">Aktion ausgeführt.</p></div>
    </div>


    <!-- =============== SCRIPTS =============== -->
    <script>
    // --- Configuration & State ---
    window.__WF_CONFIG = @json($plannerConfig);
    window.__WF = window.__WF || {};
    const WF = window.__WF;
    
    WF.api = window.__WF_CONFIG.endpoints;
    WF.state = { 
        customer: null, 
        project: null, 
        planId: null, 
        items: [], 
        managers: [], 
        employeesActive: [],
        currentView: 'board', 
        backlogFilter: 'all',
        filterDate: new Date(),
        filterMode: 'day'
    };

    // --- Helpers ---
    WF.escapeHtml = (s) => String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    WF.escapeJs = (s) => String(s || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ').replace(/\r/g, '');

    // --- HTTP Helpers ---
    WF.httpGet = async (url, params={}) => {
        const u = new URL(url, window.location.origin);
        Object.entries(params).forEach(([k,v]) => u.searchParams.append(k,v));
        const res = await fetch(u);
        return res.json();
    };

    WF.postJson = async (url, data) => {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return res.json();
    };

    WF.patchJson = async (url, data) => {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return res.json();
    };

    WF.toast = (msg) => {
        const t = document.getElementById('toast');
        t.querySelector('h4').innerText = msg;
        t.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => t.classList.add('translate-y-20', 'opacity-0'), 3000);
    };

    // --- Customer Selection ---
    const custInput = document.getElementById('customer-search-input');
    const custDrop = document.getElementById('customer-dropdown-list');
    
    async function fetchCustomers(q) {
        const res = await WF.httpGet(WF.api.customers, {q});
        return res.data || [];
    }

    window.showCustomerDropdown = () => custDrop.classList.remove('hidden');
    window.filterCustomerDropdown = async () => {
        const rows = await fetchCustomers(custInput.value);
        renderCustomers(rows);
    };

    function renderCustomers(rows) {
        custDrop.innerHTML = rows.map(c => {
            const nameEscaped = WF.escapeJs(c.firma || c.name);
            return `
            <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer text-sm" onclick="selectCustomer(${c.id}, '${nameEscaped}')">
                <div class="font-bold">${WF.escapeHtml(c.firma || c.name)} ${WF.escapeHtml(c.lastname || '')}</div>
                <div class="text-xs text-slate-500">${WF.escapeHtml(c.customer_no)}</div>
            </div>
        `}).join('');
        if(!rows.length) custDrop.innerHTML = '<div class="p-3 text-slate-400 text-xs">Keine Ergebnisse</div>';
    }

    window.selectCustomer = async (id, name) => {
        custInput.value = name;
        custDrop.classList.add('hidden');
        WF.state.customer = {id};
        
        const url = WF.api.leadProducts.replace('___ID___', id);
        const res = await WF.httpGet(url);
        
        const prodSel = document.getElementById('project-selector');
        prodSel.innerHTML = '<option value="">Produkt wählen...</option>';
        prodSel.disabled = false;
        
        (res.lead_product_lists || []).forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.text = `${p.product_name || 'Produkt'} (${p.lead_product_status_de || 'Offen'})`;
            opt.dataset.productId = p.product_id; 
            prodSel.appendChild(opt);
        });
    };

    document.getElementById('project-selector').addEventListener('change', async (e) => {
        const pid = e.target.value;
        if(!pid) return;

        const opt = e.target.selectedOptions[0];
        const productId = opt.dataset.productId;

        document.getElementById('plan-status-indicator').classList.remove('hidden');
        
        try {
            const res = await WF.httpGet(WF.api.syncAndLoad, {
                customer_id: WF.state.customer.id,
                project_id: pid,
                product_id: productId
            });

            if(res.ok) {
                loadPlanData(res.data);
                WF.toast('Plan synchronisiert.');
            }
        } catch(err) {
            console.error(err);
            alert("Fehler beim Laden des Plans.");
        } finally {
            document.getElementById('plan-status-indicator').classList.add('hidden');
        }
    });

    // --- Core Loading & Rendering ---
    function loadPlanData(data) {
        WF.state.planId = data.plan.id;
        WF.state.items = data.items;
        WF.state.managers = data.managers;
        WF.state.employeesActive = data.employees_active || [];
        WF.state.pm = data.project_manager;
        
        loadPhases(data.plan.project_id); 
        renderAllViews();
        updateTimeDisplay();
        renderBacklogAnalytics();
    }

    // --- Backlog Analytics ---
    function renderBacklogAnalytics() {
        const container = document.getElementById('backlog-analytics');
        if(!container) return;
        
        const selectedDate = new Date(WF.state.filterDate);
        const dateStr = selectedDate.toISOString().split('T')[0];
        
        const dailyItems = WF.state.items.filter(i => {
            if (!i.planned_date) return false;
            return i.planned_date.startsWith(dateStr);
        });

        const counts = { appointment: 0, ticket: 0, phase_activity: 0, personal_task: 0 };

        dailyItems.forEach(i => {
            const type = i.source_type || 'personal_task';
            if (counts.hasOwnProperty(type)) counts[type]++;
            else counts.personal_task++;
        });

        container.innerHTML = `
            <div class="bg-purple-50 border border-purple-100 p-2 rounded-xl flex flex-col items-center justify-center">
                <div class="text-[10px] uppercase font-bold text-purple-400 mb-1">Termine</div>
                <div class="flex items-center gap-2 text-purple-700"><i class="fa-regular fa-calendar"></i><span class="text-lg font-bold">${counts.appointment}</span></div>
            </div>
            <div class="bg-rose-50 border border-rose-100 p-2 rounded-xl flex flex-col items-center justify-center">
                <div class="text-[10px] uppercase font-bold text-rose-400 mb-1">Tickets</div>
                <div class="flex items-center gap-2 text-rose-700"><i class="fa-solid fa-ticket"></i><span class="text-lg font-bold">${counts.ticket}</span></div>
            </div>
            <div class="bg-slate-100 border border-slate-200 p-2 rounded-xl flex flex-col items-center justify-center">
                <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Phasen</div>
                <div class="flex items-center gap-2 text-slate-700"><i class="fa-solid fa-layer-group"></i><span class="text-lg font-bold">${counts.phase_activity}</span></div>
            </div>
            <div class="bg-blue-50 border border-blue-100 p-2 rounded-xl flex flex-col items-center justify-center">
                <div class="text-[10px] uppercase font-bold text-blue-400 mb-1">Tasks</div>
                <div class="flex items-center gap-2 text-blue-700"><i class="fa-solid fa-list-check"></i><span class="text-lg font-bold">${counts.personal_task}</span></div>
            </div>
            <div class="col-span-2 text-center mt-1">
                <span class="text-[10px] font-bold text-slate-400 bg-white px-2 py-1 rounded-full border border-slate-100 shadow-sm">
                    Gesamt am ${selectedDate.toLocaleDateString('de-DE')}: <span class="text-slate-800">${dailyItems.length}</span>
                </span>
            </div>
        `;
    }

    // --- Templates & Phases Logic ---
  async function loadPhases(projectId) {
        if (!projectId && WF.state.project) projectId = WF.state.project.project_id;
        let prodId = document.querySelector('#project-selector option:checked')?.dataset?.productId;
        if (!prodId && WF.state.plan && WF.state.plan.meta) prodId = WF.state.plan.meta.product_id;

        if (!projectId || !prodId) return;

        const res = await WF.httpGet(WF.api.phases, {
            customer_id: WF.state.customer.id,
            project_id: projectId,
            product_id: prodId
        });

        const container = document.getElementById('templates-list');
        const analyticsContainer = document.getElementById('templates-analytics');

        let totalActivities = 0;
        let doneActivities = 0;
        let openActivities = 0;

        if (res.data && res.data.length > 0) {
            let html = '';
            res.data.forEach((stage, stageIndex) => {
                html += `
                    <details class="group" ${stageIndex === 0 ? 'open' : ''}>
                        <summary class="flex items-center gap-2 cursor-pointer p-2 bg-slate-100 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-200 transition-colors">
                            <i class="fa-solid fa-chevron-right text-[10px] transition-transform group-open:rotate-90"></i>
                            ${WF.escapeHtml(stage.stage)}
                        </summary>
                        <div class="pl-2 pt-2 space-y-2">
                `;
                stage.phases.forEach((phase, phaseIndex) => {
                    const phaseContainerId = `phase-container-${stage.id}-${phase.id}`;
                    html += `
                        <details class="group/phase" ${phaseIndex === 0 ? 'open' : ''}>
                            <summary class="flex items-center gap-2 cursor-pointer p-1.5 text-[11px] font-bold text-slate-500 hover:text-sky-600 transition-colors">
                                <i class="fa-solid fa-angle-right text-[9px] transition-transform group-open/phase:rotate-90"></i>
                                ${WF.escapeHtml(phase.phase_name)}
                            </summary>
                            <div id="${phaseContainerId}" class="pl-3 space-y-2 border-l border-slate-200 ml-1.5 mt-1 pb-1 phase-sortable-container">
                    `;
                    
                    phase.activities.forEach(act => {
                        if (act.is_done || act.status === 'done') {
                            doneActivities++;
                        } else {
                            openActivities++;
                            totalActivities++; 
                            
                            const badge = act.is_planned 
                                ? '<span class="text-[9px] bg-sky-100 text-sky-700 px-1 rounded ml-auto">Im Plan</span>' 
                                : '';

                            // --- MASTER SET RENDERING LOGIC (Fixed) ---
                            const set = act.linked_master_set;
                            let masterSetHtml = '';

                            if (set) {
                                // 1. Labor Section (Position Name + Hours)
                                let laborHtml = '';
                                if (set.labor && set.labor.length > 0) {
                                    laborHtml = '<div class="flex flex-wrap gap-1 mt-1">';
                                    set.labor.forEach(l => {
                                        // Use position_name from backend, fallback to 'Arbeit'
                                        const label = l.position_name || l.employee_name || 'Arbeit';
                                        
                                        laborHtml += `
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-100" title="${label} (${Number(l.hours)}h)">
                                                <i class="fa-solid fa-user-hard-hat mr-1 opacity-70"></i> 
                                                ${WF.escapeHtml(label)} 
                                                <span class="ml-1 opacity-60 font-normal">(${Number(l.hours)}h)</span>
                                            </span>`;
                                    });
                                    laborHtml += '</div>';
                                }

                                // 2. Components Section (Material + Price)
                                let compsHtml = '';
                                if (set.components && set.components.length > 0) {
                                    compsHtml = '<div class="mt-2 pt-2 border-t border-slate-200/50 space-y-1">';
                                    
                                    // Show first 3 items
                                    set.components.slice(0, 3).forEach(c => {
                                        // Format Price (e.g. "120,50 €")
                                        const priceFormatted = Number(c.unit_price).toLocaleString('de-DE', {
                                            style: 'currency', 
                                            currency: 'EUR'
                                        });

                                        compsHtml += `
                                            <div class="flex justify-between items-center text-[10px] text-slate-600">
                                                <div class="flex items-center truncate pr-2 min-w-0">
                                                    <span class="font-bold bg-white text-slate-600 px-1.5 py-0.5 rounded border border-slate-100 mr-1.5 shrink-0">
                                                        ${Number(c.qty)}x
                                                    </span>
                                                    <span class="truncate" title="${WF.escapeHtml(c.name)}">
                                                        ${WF.escapeHtml(c.name)}
                                                    </span>
                                                </div>
                                                <span class="text-[9px] text-slate-400 font-mono shrink-0">
                                                    ${priceFormatted}
                                                </span>
                                            </div>`;
                                    });

                                    // Show "More" indicator
                                    if(set.components.length > 3) {
                                        compsHtml += `<div class="text-[9px] text-slate-400 italic pl-1 pt-0.5">+ ${set.components.length - 3} weitere...</div>`;
                                    }
                                    compsHtml += '</div>';
                                }

                                // Assemble the Grey Box
                                masterSetHtml = `
                                    <div class="mt-2 bg-slate-50 rounded-lg p-2 border border-slate-100 group-hover/card:border-slate-200 transition-colors">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">
                                                <i class="fa-solid fa-layer-group mr-1"></i> Master Set #${set.id}
                                            </span>
                                            <i class="fa-solid fa-link text-[10px] text-brandDark/40"></i>
                                        </div>
                                        ${laborHtml}
                                        ${compsHtml}
                                    </div>
                                `;
                            }

                            // Render the Card HTML
                            html += `
                                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm cursor-grab hover:border-sky-300 hover:shadow-md transition-all template-item mb-2 group/card relative" 
                                    data-source-type="phase_activity" 
                                    data-source-id="${act.id}" 
                                    data-title="${WF.escapeHtml(act.title)}" 
                                    data-planned-time="${act.duration || ''}"
                                    ${set ? `data-master-set-id="${set.id}"` : ''} 
                                >
                                    
                                    <div class="flex justify-between items-start">
                                        <div class="font-bold text-xs text-slate-800 leading-snug">
                                            ${WF.escapeHtml(act.title)}
                                        </div>
                                        ${badge} 
                                    </div>

                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                                        <span><i class="fa-regular fa-clock mr-1"></i>${WF.escapeHtml(act.duration || '0')} Min</span>
                                    </div>

                                    ${masterSetHtml}

                                </div>
                            `;
                        }
                    });
                    html += `</div></details>`;
                });
                html += `</div></details>`;
            });
            container.innerHTML = html;
            
            // Initialize Sortable for each phase
            document.querySelectorAll('.phase-sortable-container').forEach(el => {
                new Sortable(el, {
                    group: { name: 'planner', pull: 'clone', put: false },
                    sort: false,
                    draggable: ".template-item",
                    onClone: evt => {
                        evt.clone.dataset.sourceType = 'phase_activity';
                        evt.clone.dataset.sourceId = evt.item.dataset.sourceId;
                        evt.clone.dataset.title = evt.item.dataset.title;
                        if(evt.item.dataset.plannedTime) {
                             evt.clone.dataset.plannedTime = evt.item.dataset.plannedTime;
                        }
                        // Important: Carry over the Master Set ID
                        if(evt.item.dataset.masterSetId) {
                            evt.clone.dataset.masterSetId = evt.item.dataset.masterSetId;
                        }
                    }
                });
            });

        } else {
            container.innerHTML = '<div class="text-xs text-slate-400 p-2 italic">Keine Vorlagen verfügbar.</div>';
        }

        // Render Template Analytics
        const completionRate = totalActivities > 0 ? Math.round((doneActivities / totalActivities) * 100) : 0;
        if(analyticsContainer) {
            analyticsContainer.innerHTML = `
                <div class="bg-blue-50 border border-blue-100 p-2 rounded-xl flex flex-col items-center justify-center"><div class="text-[10px] uppercase font-bold text-blue-400 mb-1">Offen</div><div class="flex items-center gap-2 text-blue-700"><i class="fa-regular fa-circle"></i><span class="text-lg font-bold">${openActivities}</span></div></div>
                <div class="bg-emerald-50 border border-emerald-100 p-2 rounded-xl flex flex-col items-center justify-center"><div class="text-[10px] uppercase font-bold text-emerald-400 mb-1">Erledigt</div><div class="flex items-center gap-2 text-emerald-700"><i class="fa-solid fa-check-circle"></i><span class="text-lg font-bold">${doneActivities}</span></div></div>
                <div class="col-span-2 mt-1"><div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1 px-1"><span>Fortschritt</span><span>${completionRate}%</span></div><div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden"><div class="bg-gradient-to-r from-blue-400 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: ${completionRate}%"></div></div></div>
            `;
        }
    }
    // --- Backlog & Filtering ---
    window.filterBacklog = (type) => {
        WF.state.backlogFilter = type;
        document.querySelectorAll('.backlog-filter').forEach(btn => {
            if(btn.dataset.filter === type) {
                btn.classList.add('bg-slate-200', 'text-slate-700', 'font-bold', 'active');
                btn.classList.remove('bg-white', 'text-slate-600');
            } else {
                btn.classList.remove('bg-slate-200', 'text-slate-700', 'font-bold', 'active');
                btn.classList.add('bg-white', 'text-slate-600');
            }
        }); 
    };

    
    // --- Board Rendering ---
    function renderBoard() {
        const board = document.getElementById('view-board');
        board.innerHTML = '';
        
        const visibleItems = applyFilters(WF.state.items);
        const rangeLabel = getRangeLabel(WF.state.filterDate, WF.state.filterMode);

        WF.state.managers.forEach(mgr => {
            const col = document.createElement('div');
            col.className = 'glass-panel rounded-[2.5rem] flex flex-col h-[600px] overflow-hidden min-w-[340px] border-8 border-white/50 bg-white/30 shadow-2xl relative';
            
            const mgrItems = visibleItems.filter(i => {
                if (i.status === 'open') return false;
                const isLead = i.lead && i.lead.id == mgr.id;
                const isMember = i.members && i.members.some(m => m.id == mgr.id);
                return isLead || isMember;
            });
            
            const countTicket = mgrItems.filter(i => i.source_type === 'ticket').length;
            const countAppt = mgrItems.filter(i => i.source_type === 'appointment').length;
            const countTask = mgrItems.filter(i => ['personal_task', 'phase_activity', 'custom'].includes(i.source_type)).length;
            const doneCount = mgrItems.filter(i => ['done', 'completed', 'finished'].includes(i.status)).length;
            const percent = mgrItems.length > 0 ? Math.round((doneCount / mgrItems.length) * 100) : 0;
            
            col.innerHTML = `
                <div class="bg-white p-5 pt-6 pb-2 rounded-t-[2rem] border-b border-slate-100 z-10 relative">
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-4"><div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-full rounded-full transition-all duration-500" style="width: ${percent}%"></div></div>
                    <div class="flex justify-between items-center mb-4">
                        <div><div class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">${getModeLabel(WF.state.filterMode)}</div><div class="text-lg font-bold text-slate-800 leading-tight">${rangeLabel}</div></div>
                        <img src="${mgr.photo_url || 'https://ui-avatars.com/api/?name='+mgr.name}" class="w-12 h-12 rounded-full border-2 border-slate-100 shadow-sm object-cover">
                    </div>
                    <div class="flex gap-2 mb-4">
                        <div class="flex-1 bg-rose-50 rounded-xl p-2 flex flex-col items-center justify-center border border-rose-100"><i class="fa-solid fa-ticket text-rose-500 text-xs mb-1"></i><span class="font-bold text-slate-700 text-sm">${countTicket}</span></div>
                        <div class="flex-1 bg-purple-50 rounded-xl p-2 flex flex-col items-center justify-center border border-purple-100"><i class="fa-regular fa-calendar text-purple-500 text-xs mb-1"></i><span class="font-bold text-slate-700 text-sm">${countAppt}</span></div>
                        <div class="flex-1 bg-blue-50 rounded-xl p-2 flex flex-col items-center justify-center border border-blue-100"><i class="fa-solid fa-list-check text-blue-500 text-xs mb-1"></i><span class="font-bold text-slate-700 text-sm">${countTask}</span></div>
                    </div>
                     <div class="flex justify-between mt-1 text-[10px] font-bold text-slate-400"><span>Fortschritt</span><span>${percent}%</span></div>
                </div>
                <div class="p-3 flex-1 overflow-y-auto space-y-3 bg-slate-50/50 min-h-[200px] drop-zone pb-10" data-manager-id="${mgr.id}">
                    ${mgrItems.length ? '' : '<div class="text-center py-10 text-xs text-slate-400 font-medium">Keine Aufgaben</div>'}
                </div>
                <div class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-slate-100 to-transparent pointer-events-none"></div>
            `;

            board.appendChild(col);
            const dropZone = col.querySelector('.drop-zone');
            mgrItems.sort((a,b) => (a.planned_time || '00:00').localeCompare(b.planned_time || '00:00'));
            mgrItems.forEach(i => dropZone.innerHTML += renderBoardItem(i));
            
            new Sortable(dropZone, {
                group: { name: 'planner', pull: true, put: ['planner'] },
                animation: 150,
                onAdd: handleDrop,

                onMove: (evt) => {
                    // dragged master set?
                    const kind = evt.dragged?.dataset?.kind;
                    if (kind === 'masterset') {
                    WF._msWarnTs = WF._msWarnTs || 0;
                    const now = Date.now();
                    if (now - WF._msWarnTs > 800) {
                        WF._msWarnTs = now;
                        WF.toast('Nur in einer Aufgabe ablegen'); // your warning text
                    }
                    return false; // deny drop
                    }
                    return true;
                }
                });

        });
    }

    function renderBoardItem(item) {
        // --- 1. Determine Colors & Icons based on Source Type ---
        let typeColor = 'bg-slate-100 text-slate-600';
        let typeIcon  = '<i class="fa-solid fa-check"></i>';
        
        switch(item.source_type) {
            case 'ticket':
                typeColor = 'bg-rose-100 text-rose-600';
                typeIcon = '<i class="fa-solid fa-ticket"></i>';
                break;
            case 'appointment':
                typeColor = 'bg-purple-100 text-purple-600';
                typeIcon = '<i class="fa-regular fa-calendar"></i>';
                break;
            case 'phase_activity':
                typeColor = 'bg-blue-100 text-blue-600';
                typeIcon = '<i class="fa-solid fa-layer-group"></i>';
                break;
            case 'personal_task':
                typeColor = 'bg-orange-100 text-orange-600';
                typeIcon = '<i class="fa-solid fa-user-check"></i>';
                break;
        }

        // --- 2. Status Indicator & Buttons ---
        let statusIcon = '<div class="w-2 h-2 rounded-full bg-slate-300"></div>'; // Default open
        let btnState = '';

        if (item.status === 'done' || item.status === 'completed') {
            statusIcon = '<div class="w-2 h-2 rounded-full bg-emerald-500"></div>';
        } else if (item.status === 'in_progress') {
            statusIcon = '<div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>';
            btnState = `<button onclick="updateItemStatus(${item.id}, 'paused', event)" class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 hover:bg-amber-200 flex items-center justify-center transition-colors" title="Pause"><i class="fa-solid fa-pause text-[10px]"></i></button>`;
        } else {
            // Open or Scheduled
            btnState = `<button onclick="updateItemStatus(${item.id}, 'in_progress', event)" class="w-7 h-7 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors" title="Start"><i class="fa-solid fa-play text-[10px]"></i></button>`;
        }

        // --- 3. Date & Time Formatting ---
        let dateDay = '', dateNum = '', dateTime = '--:--';
        if (item.planned_date) {
            const d = new Date(item.planned_date);
            dateDay = d.toLocaleDateString('de-DE', { weekday: 'short' });
            dateNum = d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' });
        }
        if (item.planned_time) {
            dateTime = item.planned_time.substring(0, 5);
        }

        // --- 4. Team Avatars ---
        let teamHtml = '';
        if (item.lead) {
             const photo = item.lead.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.lead.full_name)}`;
             teamHtml += `<img src="${photo}" class="w-6 h-6 rounded-full border-2 border-slate-100 object-cover z-20" title="Lead: ${WF.escapeHtml(item.lead.full_name)}">`;
        }
        if (item.members && item.members.length > 0) {
            item.members.forEach(m => {
                const photo = m.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(m.full_name)}`;
                teamHtml += `<img src="${photo}" class="w-6 h-6 rounded-full border-2 border-white object-cover -ml-2 z-10" title="${WF.escapeHtml(m.full_name)}">`;
            });
        }
        if (!teamHtml) teamHtml = '<span class="text-[9px] text-slate-300 italic">No Team</span>';


        // --- 5. MATERIALS CART LOGIC (The New Part) ---
        let materialsHtml = '';
        
        // Check if materials exist (handled by the updated controller logic)
        if (item.materials && item.materials.length > 0) {
            materialsHtml = '<div class="mt-2 pt-2 border-t border-slate-100">';
            
            // Header
            materialsHtml += `
                <div class="flex items-center gap-1 text-[9px] font-extrabold text-slate-400 uppercase mb-1">
                    <i class="fa-solid fa-cart-shopping text-xs"></i> Material (${item.materials.length})
                </div>
            `;

            // Items Loop (Max 3)
            // Items Loop (Max 3)
            item.materials.slice(0, 3).forEach(mat => {
                const price = Number(mat.unit_price).toLocaleString('de-DE', { style: 'currency', currency: 'EUR' });
                
                materialsHtml += `
                    <div class="flex justify-between items-center text-[10px] text-slate-600 mb-0.5 group/mat pointer-events-auto">
                        <div class="truncate pr-2 flex items-center gap-1.5 min-w-0">
                            
                            <div class="hidden group-hover/mat:flex items-center gap-0.5 bg-slate-100 rounded mr-1">
                                <button onclick="updateMaterialQty(${mat.id}, ${Number(mat.qty) - 1}, event)" class="px-1 hover:text-red-500 hover:bg-red-50 rounded-l">-</button>
                                <span class="font-bold px-1 text-slate-700">${Number(mat.qty)}</span>
                                <button onclick="updateMaterialQty(${mat.id}, ${Number(mat.qty) + 1}, event)" class="px-1 hover:text-green-500 hover:bg-green-50 rounded-r">+</button>
                            </div>

                            <span class="font-bold bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 text-[9px] group-hover/mat:hidden">${Number(mat.qty)}x</span> 
                            
                            <span class="truncate" title="${WF.escapeHtml(mat.name)}">${WF.escapeHtml(mat.name)}</span>
                        </div>

                        <div class="flex items-center gap-2">
                             <div class="font-mono text-slate-400 text-[9px] shrink-0 group-hover/mat:hidden">${price}</div>
                             
                             <button onclick="deleteMaterial(${mat.id}, event)" class="hidden group-hover/mat:block text-slate-400 hover:text-red-500 transition-colors" title="Löschen">
                                <i class="fa-solid fa-trash text-[9px]"></i>
                             </button>
                        </div>
                    </div>
                `;
            });

            // "Show More" Indicator
            if (item.materials.length > 3) {
                materialsHtml += `<div class="text-[9px] text-slate-400 italic pl-1 mt-0.5">+ ${item.materials.length - 3} weitere Positionen...</div>`;
            }
            materialsHtml += '</div>';
        } else {
            // Drop Zone Hint (Hidden by default, visible on hover/drag via CSS potentially, or just stays empty)
            materialsHtml = `<div class="mt-2 pt-2 border-t border-slate-50 text-[9px] text-slate-300 italic text-center hidden group-hover:block pointer-events-none">Material hier ablegen</div>`;
        }


        // --- 6. Final HTML Assembly ---
        // Added 'planner-item-card' class and drag/drop event handlers
        return `
            <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-100 cursor-pointer hover:border-sky-200 hover:shadow-md transition-all group relative planner-item-card" 
                 onclick="openTaskModal(${item.id})" 
                 data-item-id="${item.id}"
                 ondragover="window.handleDragOver(event)"
                 ondragleave="window.handleDragLeave(event)"
                 ondrop="window.handleDropOnCard(event)"> 
                
                <div class="flex items-start gap-3">
                    <div class="flex flex-col items-center min-w-[45px] pt-1 text-slate-500 pointer-events-none">
                        <span class="text-[10px] font-bold uppercase leading-none text-slate-400">${dateDay}</span>
                        <span class="text-xs font-extrabold text-slate-700 leading-tight">${dateNum}</span>
                        <span class="text-[10px] font-medium mt-1 text-slate-500">${dateTime}</span>
                        <div class="h-full w-px bg-slate-200 my-1"></div>
                    </div>

                    <div class="flex-1 min-w-0 pb-1 pointer-events-none"> <div class="flex justify-between items-start mb-1">
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wide flex items-center gap-1 ${typeColor}">
                                ${typeIcon} ${WF.escapeHtml(item.source_type?.replace('_', ' ') || 'Task')}
                            </span>
                            ${statusIcon}
                        </div>

                        <h4 class="font-bold text-sm text-slate-800 leading-snug mb-1 truncate" title="${WF.escapeHtml(item.title)}">
                            ${WF.escapeHtml(item.title)}
                        </h4>
                        
                        <p class="text-[10px] text-slate-400 truncate mb-2">
                             ${WF.escapeHtml(item.description || '')}
                        </p>

                        ${materialsHtml}
                        
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50 pointer-events-auto"> <div class="flex items-center">
                                ${teamHtml}
                            </div>
                            
                            <div class="flex gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                ${btnState}
                                <button onclick="updateItemStatus(${item.id}, 'canceled', event)" class="w-7 h-7 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors" title="Abbrechen">
                                    <i class="fa-solid fa-ban text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        `;
    }

    // --- Material Actions ---

    window.updateMaterialQty = async (materialId, newQty, event) => {
        if (event) event.stopPropagation(); // Prevent opening the task modal

        if (newQty < 1) {
            // If quantity goes below 1, ask to delete
            return deleteMaterial(materialId, event);
        }

        const url = `/planner/materials/${materialId}`; // We need a route for this
        
        // Optimistic UI Update (Optional but recommended for speed)
        // Find item and update locally before request to feel instant
        // ... (Skipping complex local state update for brevity, relying on re-render after success)

        try {
            const res = await WF.patchJson(url, { qty: newQty });
            if (res.ok) {
                // Update Local State
                // We need to find which item this material belongs to
                // Since we don't have parent ID easily here, we can either:
                // A. Reload the whole board (easiest but slower)
                // B. Loop through all items to find and update the material
                
                updateLocalMaterialState(materialId, (mat) => { mat.qty = newQty; });
                renderAllViews();
                // WF.toast('Menge aktualisiert');
            }
        } catch (e) {
            console.error(e);
            WF.toast('Fehler beim Aktualisieren');
        }
    };

    window.deleteMaterial = async (materialId, event) => {
        if (event) event.stopPropagation();
        
        if(!confirm("Material wirklich entfernen?")) return;

        const url = `/planner/materials/${materialId}`;
        
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const res = await response.json();

            if (res.ok) {
                // Update Local State: Remove material from its parent item
                removeLocalMaterial(materialId);
                renderAllViews();
                WF.toast('Material entfernt');
            }
        } catch (e) {
            console.error(e);
            WF.toast('Fehler beim Löschen');
        }
    };

    // Helper to update local state without full reload
    function updateLocalMaterialState(matId, callback) {
        for (let item of WF.state.items) {
            if (item.materials) {
                const mat = item.materials.find(m => m.id === matId);
                if (mat) {
                    callback(mat);
                    return;
                }
            }
        }
    }

    function removeLocalMaterial(matId) {
        for (let item of WF.state.items) {
            if (item.materials) {
                const idx = item.materials.findIndex(m => m.id === matId);
                if (idx !== -1) {
                    item.materials.splice(idx, 1);
                    return;
                }
            }
        }
    }
    
    function initMasterSetBrowserDnD() {
        const list = document.getElementById('wf-masterset-browser-list');
        if (!list || list.dataset.sortableInit === '1') return;
        list.dataset.sortableInit = '1';

        new Sortable(list, {
            group: { name: 'mastersets', pull: 'clone', put: false },
            sort: false,
            animation: 150,
            draggable: '.wf-masterset-drag',
            onClone: (evt) => {
            // keep dataset on clone
            evt.clone.dataset.kind = 'masterset';
            evt.clone.dataset.setId = evt.item.dataset.setId;
            evt.clone.dataset.setName = evt.item.dataset.setName || '';
            }
        });
        }

        function initItemCartsSortable() {
            document.querySelectorAll('.wf-item-cart').forEach(cart => {
                if (cart.dataset.sortableInit === '1') return;
                cart.dataset.sortableInit = '1';

                new Sortable(cart, {
                group: { name: 'mastersets', pull: false, put: true },
                sort: false,
                animation: 150,

                onAdd: async (evt) => {
                    evt.originalEvent?.stopPropagation?.();

                    const itemId = Number(evt.to.dataset.itemId);
                    const setId  = Number(evt.item.dataset.setId);
                    const setName = evt.item.dataset.setName || '';

                    // remove dropped clone element from DOM (cart is not a list)
                    evt.item.remove();

                    if (!itemId || !setId) return;

                    try {
                    await window.linkMasterSetToItem(itemId, setId);

                    // update local state so summary shows immediately
                    const it = WF.state.items.find(x => Number(x.id) === Number(itemId));
                    if (it) {
                        it.master_set_id = setId;
                        it.master_set = { id: setId, name: setName || ('Master Set #' + setId) };
                    }

                    renderAllViews();
                    WF.toast('Master Set verknüpft');

                    // open editor directly (units/hours)
                    window.openItemMaterialsEditor(itemId);

                    } catch (e) {
                    console.error(e);
                    WF.toast('Fehler beim Verknüpfen');
                    }
                }
                });
            });
            }



    // --- Placeholders (Gantt, List) ---
    // --- GANTT GLOBAL STATE ---
    let isDraggingConnector = false;
    let connectStartId = null;
    let tempLine = null;

    // New: Bar Dragging State
    let isDraggingBar = false;
    let dragBarId = null;
    let dragStartX = 0;
    let dragOriginalLeft = 0;

    // Configuration
    const GANTT_CONFIG = {
        startHour: 8,
        endHour: 18,
        pxPerHour: 120, // 1 hour = 120px
        snapMinutes: 15 // Snap to 15 minute grid
    };

   function renderGantt() {
        const container = document.getElementById('gantt-tasks-container');
        const scaleContainer = document.getElementById('time-scale');
        
        container.innerHTML = '';
        scaleContainer.innerHTML = '';

        // 1. Calculate Dimensions
        const totalWidth = (GANTT_CONFIG.endHour - GANTT_CONFIG.startHour) * GANTT_CONFIG.pxPerHour;

        container.style.width = `${totalWidth}px`;
        scaleContainer.style.width = `${totalWidth}px`;

        // 2. Render Time Header & Grid
        for (let h = GANTT_CONFIG.startHour; h <= GANTT_CONFIG.endHour; h++) {
            // Header Marker
            const marker = document.createElement('div');
            marker.className = 'absolute top-0 bottom-0 border-l border-slate-200 pl-1 text-xs text-slate-400 font-bold';
            marker.style.left = `${(h - GANTT_CONFIG.startHour) * GANTT_CONFIG.pxPerHour}px`;
            marker.innerText = `${h}:00`;
            scaleContainer.appendChild(marker);
            
            // Grid Line in Body
            const gridLine = document.createElement('div');
            gridLine.className = 'absolute top-0 bottom-0 border-l border-slate-100 h-full pointer-events-none';
            gridLine.style.left = `${(h - GANTT_CONFIG.startHour) * GANTT_CONFIG.pxPerHour}px`;
            container.appendChild(gridLine);
        }

        // 3. SVG Layer for Connections
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.id = 'gantt-svg-layer';
        svg.style.width = `${totalWidth}px`;
        // Dynamic height based on items (min 600px)
        svg.style.height = `${Math.max(600, WF.state.items.length * 60 + 100)}px`; 
        container.appendChild(svg);

        // 4. Prepare Items
        const visibleItems = applyFilters(WF.state.items).filter(i => i.status !== 'open');
        visibleItems.sort((a,b) => (a.planned_time || '00:00').localeCompare(b.planned_time || '00:00'));

        visibleItems.forEach((item, index) => {
            if(!item.planned_time) return;

            // Calculate Position
            const [h, m] = item.planned_time.split(':').map(Number);
            const timeDec = h + (m/60);
            
            // Calculate coordinates
            const left = (timeDec - GANTT_CONFIG.startHour) * GANTT_CONFIG.pxPerHour;
            const width = (item.duration_minutes / 60) * GANTT_CONFIG.pxPerHour;
            const top = index * 50 + 20; // 50px row height, 20px padding top

            // Create Bar
            const bar = document.createElement('div');
            bar.className = 'gantt-bar bg-white border border-slate-200 shadow-sm rounded-lg absolute flex items-center px-2 z-20 hover:border-brandDark transition-colors cursor-grab active:cursor-grabbing active:z-50';
            
            // Add Status Class
            if (item.status === 'done' || item.status === 'completed') {
                bar.classList.add('status-done');
            }

            bar.style.left = `${left}px`;
            bar.style.width = `${Math.max(width, 50)}px`; // Min width 50px
            bar.style.top = `${top}px`;
            bar.style.height = '36px';
            
            // Store data for logic/drawing
            bar.dataset.id = item.id;
            bar.dataset.left = left;
            bar.dataset.top = top;
            bar.dataset.width = Math.max(width, 50);

            // Color Coding based on Source Type (Border)
            if(item.source_type === 'ticket') bar.classList.add('border-l-4', 'border-l-rose-400');
            else if(item.source_type === 'appointment') bar.classList.add('border-l-4', 'border-l-purple-400');
            else bar.classList.add('border-l-4', 'border-l-blue-400');

            // --- Employee Images Logic ---
            let avatarsHtml = '';
            const allEmployees = [];
            if(item.lead) allEmployees.push(item.lead);
            if(item.members) allEmployees.push(...item.members);
            
            // Unique employees
            const uniqueEmps = Array.from(new Set(allEmployees.map(e => e.id)))
                .map(id => allEmployees.find(e => e.id === id));

            if(uniqueEmps.length > 0) {
                avatarsHtml = `<div class="gantt-avatars">`;
                uniqueEmps.slice(0, 3).forEach(emp => { // Show max 3
                    const photo = emp.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.full_name || 'User')}&background=random`;
                    avatarsHtml += `<img src="${photo}" title="${WF.escapeHtml(emp.full_name)}" alt="emp">`;
                });
                if(uniqueEmps.length > 3) {
                    avatarsHtml += `<span class="flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-[9px] font-bold text-slate-500 border-2 border-white -ml-2">+${uniqueEmps.length - 3}</span>`;
                }
                avatarsHtml += `</div>`;
            }

            // --- Date Display ---
            // Format planned_date if needed (YYYY-MM-DD -> DD.MM)
            let dateDisplay = '';
            if(item.planned_date) {
                const d = new Date(item.planned_date);
                dateDisplay = `<span class="text-[9px] text-slate-400 mr-2 font-mono">${d.toLocaleDateString('de-DE', {day:'2-digit', month:'2-digit'})}</span>`;
            }

            // Bar Content
            bar.innerHTML = `
                ${avatarsHtml}
                <div class="flex-1 min-w-0 flex flex-col justify-center leading-none">
                    <div class="truncate text-xs font-bold text-slate-700 pointer-events-none gantt-title">${WF.escapeHtml(item.title)}</div>
                    <div class="flex items-center mt-0.5">
                        ${dateDisplay}
                        <span class="text-[9px] text-slate-400">${item.planned_time || ''}</span>
                    </div>
                </div>
                <div class="gantt-bar-handle" onmousedown="startConnection(event, ${item.id})">
                    <i class="fa-solid fa-plus"></i>
                </div>
            `;
            
            // EVENTS:
            
            // 1. Click to Open Modal (only if not dragging)
            bar.onclick = (e) => {
                if(!isDraggingConnector && !isDraggingBar) openTaskModal(item.id);
            };

            // 2. Mouse Down to Move Bar (Time Shift)
            bar.onmousedown = (e) => {
                // Ignore if clicking the connector handle
                if (e.target.closest('.gantt-bar-handle')) return;
                startGanttDrag(e, item.id);
            };

            container.appendChild(bar);
        });

        // 5. Draw Dependencies
        drawDependencies(visibleItems);
    }
    // --- LOGIC: BAR DRAGGING (TIME SHIFT) ---

    function startGanttDrag(e, itemId) {
        e.preventDefault(); // Stop text selection
        isDraggingBar = true;
        dragBarId = itemId;

        const bar = document.querySelector(`.gantt-bar[data-id="${itemId}"]`);
        dragStartX = e.clientX;
        dragOriginalLeft = parseFloat(bar.style.left || 0);

        // Attach global listeners
        document.addEventListener('mousemove', onGanttDragMove);
        document.addEventListener('mouseup', onGanttDragEnd);
    }

    function onGanttDragMove(e) {
        if (!isDraggingBar || !dragBarId) return;

        const deltaX = e.clientX - dragStartX;
        let newLeft = dragOriginalLeft + deltaX;

        // Snap Calculation
        const pxPerMinute = GANTT_CONFIG.pxPerHour / 60;
        const snapPixels = pxPerMinute * GANTT_CONFIG.snapMinutes;
        
        // Round to nearest snap grid
        newLeft = Math.round(newLeft / snapPixels) * snapPixels;

        // Boundaries
        const container = document.getElementById('gantt-tasks-container');
        if (newLeft < 0) newLeft = 0;
        // Optional: Max boundary logic here if needed

        // Apply new visual position
        const bar = document.querySelector(`.gantt-bar[data-id="${dragBarId}"]`);
        if (bar) {
            bar.style.left = `${newLeft}px`;
            
            // Update dataset so dependency lines follow in real-time
            bar.dataset.left = newLeft;

            // Update Text to show preview time
            const newTime = pixelsToTime(newLeft);
            const titleEl = bar.querySelector('.gantt-title');
            if(titleEl) titleEl.innerText = `${newTime} (Verschieben...)`;

            // Redraw lines immediately
            drawDependencies(WF.state.items);
        }
    }

    function onGanttDragEnd(e) {
        if (!isDraggingBar) return;

        isDraggingBar = false;
        document.removeEventListener('mousemove', onGanttDragMove);
        document.removeEventListener('mouseup', onGanttDragEnd);

        const bar = document.querySelector(`.gantt-bar[data-id="${dragBarId}"]`);
        const finalLeft = parseFloat(bar.style.left || 0);
        const newTime = pixelsToTime(finalLeft);

        // Save to Backend
        saveGanttPosition(dragBarId, newTime);
        dragBarId = null;
    }

    function pixelsToTime(px) {
        const hoursFromStart = px / GANTT_CONFIG.pxPerHour;
        let totalHours = GANTT_CONFIG.startHour + hoursFromStart;
        
        let h = Math.floor(totalHours);
        let m = Math.round((totalHours - h) * 60);
        
        if (m === 60) { h++; m = 0; }
        
        const hStr = h.toString().padStart(2, '0');
        const mStr = m.toString().padStart(2, '0');
        return `${hStr}:${mStr}`;
    }

    async function saveGanttPosition(itemId, timeStr) {
        const dateStr = WF.state.filterDate.toISOString().split('T')[0];
        const url = `/planner/plans/${WF.state.planId}/items/${itemId}/move-gantt`;

        try {
            const res = await WF.patchJson(url, {
                planned_date: dateStr,
                planned_time: timeStr
            });

            if (res.ok) {
                // Update local item
                const item = WF.state.items.find(i => i.id == itemId);
                if(item) {
                    item.planned_time = timeStr;
                    item.planned_start_at = res.item.planned_start_at;
                    item.planned_end_at = res.item.planned_end_at;
                }
                WF.toast(`Verschoben auf ${timeStr}`);
                renderGantt(); // Full Re-render to sanitize state
            }
        } catch (err) {
            console.error(err);
            alert('Fehler beim Speichern der Zeit.');
            renderGantt(); // Revert visual change
        }
    }

    // --- LOGIC: DEPENDENCY DRAWING ---

    function drawDependencies(items) {
        const svg = document.getElementById('gantt-svg-layer');
        // Clear existing lines (except the temp line if dragging connector)
        Array.from(svg.children).forEach(child => {
            if (child !== tempLine) child.remove();
        });

        // Map items for coordinate access
        const itemMap = {};
        document.querySelectorAll('.gantt-bar').forEach(el => {
            itemMap[el.dataset.id] = {
                x: parseFloat(el.dataset.left),
                y: parseFloat(el.dataset.top),
                w: parseFloat(el.dataset.width),
                h: 36
            };
        });

        items.forEach(item => {
            if(item.dependencies && item.dependencies.length) {
                item.dependencies.forEach(dep => {
                    const parent = itemMap[dep.id]; // The prerequisite
                    const me = itemMap[item.id];    // The dependent

                    if(parent && me) {
                        drawCurve(svg, parent, me, dep.reason, item.id, dep.id);
                    }
                });
            }
        });
    }

    function drawCurve(svg, startObj, endObj, title, itemId, depId) {
        const startX = startObj.x + startObj.w;
        const startY = startObj.y + (startObj.h / 2);
        const endX = endObj.x;
        const endY = endObj.y + (endObj.h / 2);

        // Bezier Control Points Logic
        const cpOffset = 30; 
        const d = `M ${startX} ${startY} C ${startX + cpOffset} ${startY}, ${endX - cpOffset} ${endY}, ${endX} ${endY}`;

        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
        path.setAttribute("d", d);
        path.setAttribute("class", "dependency-line"); // Relies on CSS class
        
        const titleEl = document.createElementNS("http://www.w3.org/2000/svg", "title");
        titleEl.textContent = title || 'Abhängigkeit';
        path.appendChild(titleEl);

        path.onclick = (e) => {
            e.stopPropagation();
            if(confirm('Abhängigkeit löschen?')) {
                deleteDependency(itemId, depId);
            }
        };

        svg.appendChild(path);
    }

    // --- LOGIC: CONNECTOR DRAGGING (DEPENDENCIES) ---

    window.startConnection = (e, itemId) => {
        e.stopPropagation();
        e.preventDefault();
        
        isDraggingConnector = true;
        connectStartId = itemId;

        const svg = document.getElementById('gantt-svg-layer');
        const startEl = document.querySelector(`.gantt-bar[data-id="${itemId}"]`);
        
        const startX = parseFloat(startEl.dataset.left) + parseFloat(startEl.dataset.width);
        const startY = parseFloat(startEl.dataset.top) + 18;

        tempLine = document.createElementNS("http://www.w3.org/2000/svg", "path");
        tempLine.setAttribute("stroke", "#164191");
        tempLine.setAttribute("stroke-width", "2");
        tempLine.setAttribute("stroke-dasharray", "4");
        tempLine.setAttribute("fill", "none");
        tempLine.dataset.startX = startX;
        tempLine.dataset.startY = startY;
        
        svg.appendChild(tempLine);

        document.addEventListener('mousemove', updateTempLine);
        document.addEventListener('mouseup', endConnection);
    };

    function updateTempLine(e) {
        if(!tempLine) return;
        const container = document.getElementById('gantt-tasks-container');
        const rect = container.getBoundingClientRect();
        
        const mouseX = e.clientX - rect.left + container.scrollLeft;
        const mouseY = e.clientY - rect.top + container.scrollTop;
        
        const startX = parseFloat(tempLine.dataset.startX);
        const startY = parseFloat(tempLine.dataset.startY);

        const d = `M ${startX} ${startY} L ${mouseX} ${mouseY}`;
        tempLine.setAttribute("d", d);
    }

    function endConnection(e) {
        document.removeEventListener('mousemove', updateTempLine);
        document.removeEventListener('mouseup', endConnection);
        
        if(tempLine) tempLine.remove();
        tempLine = null;
        isDraggingConnector = false;

        const target = e.target.closest('.gantt-bar');
        if(target) {
            const targetId = parseInt(target.dataset.id);
            if(targetId !== connectStartId) {
                openDependencyModal(targetId, connectStartId);
            }
        }
    }

    // --- DEPENDENCY MODAL API ---

    function openDependencyModal(itemId, dependsOnId) {
        document.getElementById('dep-from-id').value = itemId;
        document.getElementById('dep-to-id').value = dependsOnId;
        document.getElementById('dep-reason').value = '';
        document.getElementById('dependency-modal').classList.remove('hidden');
    }

    window.closeDependencyModal = () => document.getElementById('dependency-modal').classList.add('hidden');

    window.saveDependency = async () => {
        const itemId = document.getElementById('dep-from-id').value;
        const dependsOnId = document.getElementById('dep-to-id').value;
        const reason = document.getElementById('dep-reason').value;

        const url = `/planner/plans/${WF.state.planId}/dependencies`;
        const res = await WF.postJson(url, { item_id: itemId, depends_on_id: dependsOnId, reason: reason });

        if(res.ok) {
            const item = WF.state.items.find(i => i.id == itemId);
            if(!item.dependencies) item.dependencies = [];
            item.dependencies.push({ id: parseInt(dependsOnId), reason: reason });
            
            closeDependencyModal();
            renderGantt();
            WF.toast('Verbindung hergestellt');
        }
    };

    window.deleteDependency = async (itemId, dependsOnId) => {
        const url = `/planner/plans/${WF.state.planId}/dependencies/delete`;
        const res = await WF.postJson(url, { item_id: itemId, depends_on_id: dependsOnId });
        
        if(res.ok) {
            const item = WF.state.items.find(i => i.id == itemId);
            if(item && item.dependencies) {
                item.dependencies = item.dependencies.filter(d => d.id != dependsOnId);
            }
            renderGantt();
            WF.toast('Verbindung entfernt');
        }
    };
 

    // --- LIST VIEW STATE & LOGIC ---

    let listViewSearch = '';
    let listViewTypeFilter = 'all';

    function renderList() {
        const container = document.getElementById('list-body');
        
        // 1. Prepare Data
        // Apply Global Date Filters first
        let items = applyFilters(WF.state.items);
        
        // Apply Local List Search
        if (listViewSearch) {
            const lowerQ = listViewSearch.toLowerCase();
            items = items.filter(i => 
                (i.title && i.title.toLowerCase().includes(lowerQ)) || 
                (i.description && i.description.toLowerCase().includes(lowerQ))
            );
        }

        // Apply Local Type Filter
        if (listViewTypeFilter !== 'all') {
            items = items.filter(i => i.source_type === listViewTypeFilter);
        }

        // Sort: Time then Title
        items.sort((a,b) => (a.planned_time || '00:00').localeCompare(b.planned_time || '00:00'));

        // 2. Calculate Analytics
        const analytics = {
            total: items.length,
            done: items.filter(i => ['done', 'completed'].includes(i.status)).length,
            tickets: items.filter(i => i.source_type === 'ticket').length,
            appointments: items.filter(i => i.source_type === 'appointment').length,
            tasks: items.filter(i => !['ticket', 'appointment'].includes(i.source_type)).length
        };
        
        const progress = analytics.total > 0 ? Math.round((analytics.done / analytics.total) * 100) : 0;

        // 3. Build HTML Structure (Skeleton + Content)
        // We rebuild entire innerHTML to ensure state updates reflect immediately
        container.innerHTML = `
            <div class="sticky top-0 bg-white/95 backdrop-blur z-30 pb-4 border-b border-slate-200">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div class="text-[10px] uppercase font-bold text-slate-400">Gesamt</div>
                        <div class="text-xl font-bold text-slate-700">${analytics.total}</div>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                        <div class="text-[10px] uppercase font-bold text-emerald-500">Erledigt</div>
                        <div class="text-xl font-bold text-emerald-700">${analytics.done} <span class="text-xs text-emerald-500/70">(${progress}%)</span></div>
                    </div>
                    <div class="bg-rose-50 p-3 rounded-xl border border-rose-100 hidden md:block">
                        <div class="text-[10px] uppercase font-bold text-rose-400">Tickets</div>
                        <div class="text-xl font-bold text-rose-700">${analytics.tickets}</div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-xl border border-purple-100 hidden md:block">
                        <div class="text-[10px] uppercase font-bold text-purple-400">Termine</div>
                        <div class="text-xl font-bold text-purple-700">${analytics.appointments}</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 hidden md:block">
                        <div class="text-[10px] uppercase font-bold text-blue-400">Tasks</div>
                        <div class="text-xl font-bold text-blue-700">${analytics.tasks}</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <input type="text" value="${WF.escapeHtml(listViewSearch)}" 
                            oninput="updateListSearch(this.value)" 
                            placeholder="In Liste suchen..." 
                            class="w-full pl-9 pr-3 py-2 bg-slate-100 border-none rounded-lg text-sm font-semibold focus:ring-2 focus:ring-brandDark/20 outline-none">
                    </div>
                    <select onchange="updateListType(this.value)" class="bg-slate-100 border-none rounded-lg text-xs font-bold text-slate-600 px-3 cursor-pointer focus:ring-2 focus:ring-brandDark/20 outline-none">
                        <option value="all" ${listViewTypeFilter === 'all' ? 'selected' : ''}>Alle Typen</option>
                        <option value="ticket" ${listViewTypeFilter === 'ticket' ? 'selected' : ''}>Tickets</option>
                        <option value="appointment" ${listViewTypeFilter === 'appointment' ? 'selected' : ''}>Termine</option>
                        <option value="phase_activity" ${listViewTypeFilter === 'phase_activity' ? 'selected' : ''}>Phasen</option>
                        <option value="personal_task" ${listViewTypeFilter === 'personal_task' ? 'selected' : ''}>Manual Tasks</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3 pt-2 pb-10">
                ${items.length === 0 ? '<div class="text-center py-10 text-slate-400 italic">Keine Einträge gefunden.</div>' : items.map(item => renderListItem(item)).join('')}
            </div>
        `;
    }

    // --- List Item Renderer ---
    function renderListItem(item) {
        // Icons & Colors based on type
        let icon = '<i class="fa-solid fa-check"></i>';
        let iconBg = 'bg-slate-100 text-slate-500';
        let typeLabel = 'Task';

        if(item.source_type === 'ticket') {
            icon = '<i class="fa-solid fa-ticket"></i>';
            iconBg = 'bg-rose-100 text-rose-600';
            typeLabel = 'Ticket';
        } else if(item.source_type === 'appointment') {
            icon = '<i class="fa-regular fa-calendar"></i>';
            iconBg = 'bg-purple-100 text-purple-600';
            typeLabel = 'Termin';
        } else if(item.source_type === 'phase_activity') {
            icon = '<i class="fa-solid fa-layer-group"></i>';
            iconBg = 'bg-blue-100 text-blue-600';
            typeLabel = 'Phase';
        } else {
            icon = '<i class="fa-solid fa-clipboard-check"></i>';
            iconBg = 'bg-orange-100 text-orange-600';
            typeLabel = 'Task';
        }

        // Status Badge
        let statusBadge = '';
        if(item.status === 'done' || item.status === 'completed') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Erledigt</span>';
        else if(item.status === 'in_progress') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">In Arbeit</span>';
        else statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">Geplant</span>';

        // Dependencies Logic
        let depHtml = '';
        if(item.dependencies && item.dependencies.length > 0) {
            depHtml = `<div class="mt-3 pt-3 border-t border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 block">Abhängigkeiten</span>
                <div class="flex flex-wrap gap-2">`;
            
            item.dependencies.forEach(dep => {
                // Find parent title
                const parent = WF.state.items.find(i => i.id == dep.id);
                const parentTitle = parent ? parent.title : `Item #${dep.id}`;
                
                depHtml += `
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                        <i class="fa-solid fa-link text-[10px] text-slate-400"></i>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">${WF.escapeHtml(parentTitle)}</span>
                            ${dep.reason ? `<span class="text-[9px] text-slate-400 italic">${WF.escapeHtml(dep.reason)}</span>` : ''}
                        </div>
                    </div>
                `;
            });
            depHtml += `</div></div>`;
        }

        // Team Avatars
        let teamHtml = '';
        if(item.members && item.members.length > 0) {
            teamHtml = `<div class="flex -space-x-1.5 ml-2">`;
            item.members.forEach(m => {
                teamHtml += `<img src="${m.photo_url || 'https://ui-avatars.com/api/?name='+m.full_name}" class="w-5 h-5 rounded-full border border-white" title="${m.full_name}">`;
            });
            teamHtml += `</div>`;
        }

        return `
            <details class="group bg-white border border-slate-200 rounded-xl shadow-sm hover:border-brandDark/30 transition-colors overflow-hidden">
                <summary class="flex items-center gap-3 p-3 cursor-pointer select-none">
                    <div class="list-type-icon ${iconBg} shrink-0">
                        ${icon}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">${typeLabel}</span>
                            ${statusBadge}
                        </div>
                        <div class="text-sm font-bold text-slate-800 truncate">${WF.escapeHtml(item.title)}</div>
                    </div>

                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-700">${item.planned_time || '--:--'}</div>
                        <div class="text-[10px] text-slate-400">Dauer: ${item.duration_minutes}m</div>
                    </div>

                    <div class="flex items-center gap-2">
                        ${item.lead ? `<img src="${item.lead.photo_url || 'https://ui-avatars.com/api/?name='+item.lead.full_name}" class="w-7 h-7 rounded-full border border-slate-100" title="Lead: ${item.lead.full_name}">` : ''}
                        ${teamHtml}
                    </div>

                    <div class="w-6 h-6 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 group-open:bg-brandDark group-open:text-white transition-colors">
                        <i class="fa-solid fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                    </div>
                </summary>
                
                <div class="bg-slate-50/50 p-4 border-t border-slate-100 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <div class="text-xs font-bold text-slate-400 uppercase mb-1">Beschreibung</div>
                            <p class="text-slate-600 leading-relaxed">${WF.escapeHtml(item.description) || '<span class="italic text-slate-400">Keine Beschreibung</span>'}</p>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase mb-1">Aktionen</div>
                            <button onclick="openTaskModal(${item.id})" class="w-full text-left px-3 py-2 bg-white border border-slate-200 rounded-lg hover:border-brandDark text-brandDark font-bold text-xs flex items-center justify-between mb-2">
                                Bearbeiten <i class="fa-solid fa-pen"></i>
                            </button>
                            ${item.status !== 'done' ? 
                                `<button onclick="updateItemStatus(${item.id}, 'done')" class="w-full text-left px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-between">
                                    Abschließen <i class="fa-solid fa-check"></i>
                                </button>` : ''
                            }
                        </div>
                    </div>
                    ${depHtml}
                </div>
            </details>
        `;
    }

    // --- List View Actions ---

    window.updateListSearch = (val) => {
        listViewSearch = val;
        renderList(); // Re-renders immediately
    };

    window.updateListType = (val) => {
        listViewTypeFilter = val;
        renderList(); // Re-renders immediately
    };

    function renderAllViews() {
        renderBoard();
        renderList();
        renderGantt();
    }

    // --- Item Actions (Update Status/Delete) ---
    window.updateItemStatus = async (itemId, status, event) => {
        if(event) event.stopPropagation(); 
        const url = WF.api.planItemUpdate.replace('___PLAN___', WF.state.planId).replace('___ITEM___', itemId);
        try {
            const res = await WF.patchJson(url, { status: status });
            if(res.ok) {
                const item = WF.state.items.find(i => i.id == itemId);
                if(item) { 
                    item.status = status;
                    renderAllViews();
                    if(status === 'open')  
                    renderBacklogAnalytics();
                }
                WF.toast('Status aktualisiert');
            }
        } catch(e) { console.error(e); }
    };

    // --- Handle Drop (FIXED: Update local state immediately) ---
    function handleDrop(evt) {
          const itemEl = evt.item;
          const toManagerId = evt.to.dataset.managerId;
          const isNew = !itemEl.dataset.itemId;
          
          // Default to current date/time if dragging from sidebar
          const preDate = itemEl.dataset.plannedDate || new Date().toISOString().split('T')[0];
          const preTime = itemEl.dataset.plannedTime || '08:00';
          
          let preTeamIds = [];
          if(itemEl.dataset.employees) {
              try {
                  const emps = JSON.parse(itemEl.dataset.employees);
                  preTeamIds = emps.map(e => e.id);
              } catch(e) {}
          }

          openDnDModal({
              managerId: toManagerId,
              date: preDate,
              time: preTime,
              teamIds: preTeamIds, 
              onSave: (data) => {
                  const endpoint = isNew ? WF.api.dnd.add : WF.api.dnd.move;
                  const payload = {
                      plan_id: WF.state.planId,
                      to_manager_id: toManagerId,
                      planned_date: data.date,
                      planned_time: data.time,
                      crew_ids: data.team, 
                      title: itemEl.dataset.title,
                      item_id: itemEl.dataset.itemId, 
                      source_type: itemEl.dataset.sourceType, 
                      source_id: itemEl.dataset.sourceId 
                  };

                  WF.postJson(endpoint, payload).then(res => {
                      if(res.ok) {
                          if(isNew) {
                              // --- FIX START ---
                              // 1. Assign Lead for Board filtering
                              res.item.lead = WF.state.managers.find(m => m.id == toManagerId);
                              
                              // 2. Assign Members if returned or default empty
                              res.item.members = []; 
                              if(data.team && data.team.length > 0) {
                                  // Map selected IDs back to employee objects from state for display
                                  res.item.members = WF.state.employeesActive
                                      .filter(e => data.team.includes(e.id) && e.id != toManagerId)
                                      .map(e => ({
                                          id: e.id,
                                          full_name: e.full_name,
                                          photo_url: e.photo_url
                                      }));
                              }

                              // 3. Map Backend fields to Frontend format
                              res.item.planned_date = data.date;
                              res.item.planned_time = data.time;
                              res.item.status = 'planned'; // Ensure status matches filter (status !== 'open')
                              
                              // 4. Push to State
                              WF.state.items.push(res.item);
                              // --- FIX END ---
                          } else {
                              // Logic for moving existing items (unchanged)
                              const it = WF.state.items.find(i => i.id == itemEl.dataset.itemId);
                              if(it) {
                                  it.lead = WF.state.managers.find(m => m.id == toManagerId);
                                  it.status = 'planned';
                                  it.planned_date = data.date;
                                  it.planned_time = data.time;
                                  
                                  // Update members for existing item
                                  if(data.team) {
                                      it.members = WF.state.employeesActive
                                          .filter(e => data.team.includes(e.id) && e.id != toManagerId)
                                          .map(e => ({
                                              id: e.id,
                                              full_name: e.full_name,
                                              photo_url: e.photo_url
                                          }));
                                  }
                              }
                          }
                          
                          itemEl.remove(); // Remove the "ghost" dragged element
                          renderAllViews(); // Re-render the board with new state
                          WF.toast('Gespeichert');
                          renderBacklogAnalytics();
                      }
                  });
              },
              onCancel: () => renderAllViews() // Re-render to revert drag if cancelled
          });
      }
    // --- Time & Filter Logic ---
    function setFilterMode(mode) {
        WF.state.filterMode = mode;
        if (mode === 'tomorrow') {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            WF.state.filterDate = tomorrow;
            WF.state.filterMode = 'day'; 
        } else if (mode === 'day') {
            WF.state.filterDate = new Date();
        }
        updateTimeDisplay();
        renderAllViews();
        renderBacklogAnalytics();
    }

    function changeTime(delta) {
        const d = new Date(WF.state.filterDate);
        const mode = WF.state.filterMode;
        
        if (mode === 'day') {
            d.setDate(d.getDate() + delta);
        } else if (mode === 'week') {
            d.setDate(d.getDate() + (delta * 7));
        } else if (mode === 'month') {
            d.setMonth(d.getMonth() + delta);
        }
        WF.state.filterDate = d;
        updateTimeDisplay();
        renderAllViews();
        renderBacklogAnalytics();
    }

    function setDateManually(val) {
        if(!val) return;
        WF.state.filterDate = new Date(val);
        WF.state.filterMode = 'day'; 
        document.getElementById('time-mode-select').value = 'day';
        updateTimeDisplay();
        renderAllViews();
        renderBacklogAnalytics();
    }

    function updateTimeDisplay() {
        const d = WF.state.filterDate;
        const mode = WF.state.filterMode;
        const el = document.getElementById('nav-date-display');
        const picker = document.getElementById('nav-date-picker');
        
        let text = '';
        const opts = { day: '2-digit', month: '2-digit', year: 'numeric' };
        
        if (mode === 'day') text = d.toLocaleDateString('de-DE', { weekday: 'short', ...opts });
        else if (mode === 'week') text = `KW ${getWeekNumber(d)}`;
        else if (mode === 'month') text = d.toLocaleDateString('de-DE', { month: 'long', year: 'numeric' });
        
        el.innerText = text;
        picker.value = d.toISOString().split('T')[0];
    }

    function getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
    }

    function getRangeLabel(date, mode) {
        const anchor = new Date(date);
        anchor.setHours(0,0,0,0);
        
        if (mode === 'day') {
            return anchor.toLocaleDateString('de-DE', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' });
        } else if (mode === 'week') {
            const day = anchor.getDay() || 7; 
            const start = new Date(anchor);
            start.setHours(-24 * (day - 1));
            const end = new Date(start);
            end.setDate(end.getDate() + 6);
            return `${start.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' })} - ${end.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' })}`;
        } else if (mode === 'month') {
            return anchor.toLocaleDateString('de-DE', { month: 'long', year: 'numeric' });
        } else if (mode === 'next_4_weeks') {
             const end = new Date(anchor);
             end.setDate(end.getDate() + 28);
             return `${anchor.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' })} - ${end.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' })}`;
        } else if (mode === 'next_3_months') {
            const end = new Date(anchor);
            end.setMonth(end.getMonth() + 3);
            return `${anchor.toLocaleDateString('de-DE', { month: 'short' })} - ${end.toLocaleDateString('de-DE', { month: 'short', year: 'numeric' })}`;
        }
        return 'Alle Termine';
    }

    function getModeLabel(mode) {
         const map = { 'day': 'Tagesplan', 'week': 'Wochenplan', 'month': 'Monatsplan', 'next_4_weeks': '4-Wochen-Vorschau', 'next_3_months': 'Quartalsplan', 'all': 'Gesamtübersicht' };
         return map[mode] || 'Plan';
    }

    function applyFilters(items) {
        const mode = WF.state.filterMode;
        if (mode === 'all') return items;
        
        const anchor = new Date(WF.state.filterDate);
        anchor.setHours(0,0,0,0);
        
        let start, end;

        if (mode === 'day') {
            start = new Date(anchor);
            end = new Date(anchor);
            end.setHours(23,59,59,999);
        } else if (mode === 'week') {
            const day = anchor.getDay() || 7; 
            start = new Date(anchor);
            start.setHours(-24 * (day - 1));
            end = new Date(start);
            end.setDate(end.getDate() + 6);
            end.setHours(23,59,59,999);
        } else if (mode === 'month') {
            start = new Date(anchor.getFullYear(), anchor.getMonth(), 1);
            end = new Date(anchor.getFullYear(), anchor.getMonth() + 1, 0, 23, 59, 59, 999);
        } else if (mode === 'next_4_weeks') {
            start = new Date(anchor);
            end = new Date(anchor);
            end.setDate(end.getDate() + 28);
            end.setHours(23,59,59,999);
        } else if (mode === 'next_3_months') {
            start = new Date(anchor);
            end = new Date(anchor);
            end.setMonth(end.getMonth() + 3);
            end.setHours(23,59,59,999);
        }

        return items.filter(i => {
            if(!i.planned_date) return false; 
            const d = new Date(i.planned_date);
            return d >= start && d <= end;
        });
    }

    // --- Manager Modal Logic (Keep as is) ---
    window.openCrewModal = () => {
        document.getElementById('crew-modal').classList.remove('hidden');
        const list = document.getElementById('crew-list-container');
        const currentManagerIds = WF.state.managers.map(m => m.id);
        const search = document.getElementById('crew-search');

        const render = (filter = '') => {
            const filtered = WF.state.employeesActive.filter(e => e.full_name.toLowerCase().includes(filter.toLowerCase()));
            list.innerHTML = filtered.map(e => `
                <label class="flex items-center gap-3 p-3 rounded-lg border hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 accent-brandDark crew-checkbox" value="${e.id}" ${currentManagerIds.includes(e.id) ? 'checked' : ''}>
                    <img src="${e.photo_url || 'https://ui-avatars.com/api/?name='+e.full_name}" class="w-8 h-8 rounded-full border border-slate-200 object-cover">
                    <div class="text-sm font-bold text-slate-800">${WF.escapeHtml(e.full_name)}</div>
                </label>
            `).join('');
        };
        render();
        search.oninput = (e) => render(e.target.value);
    };

    window.closeCrewModal = () => { document.getElementById('crew-modal').classList.add('hidden'); };

    window.saveCrewSelection = () => {
        const checkboxes = document.querySelectorAll('.crew-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
        const url = `/planner/plans/${WF.state.planId}/managers`;
        WF.postJson(url, { manager_ids: selectedIds }).then(res => {
            if(res.ok) {
                WF.state.managers = WF.state.employeesActive.filter(e => selectedIds.includes(e.id));
                if(WF.state.pm && !WF.state.managers.find(m => m.id == WF.state.pm.id)) {
                    WF.state.managers.unshift(WF.state.pm);
                }
                renderAllViews();
                window.closeCrewModal();
                WF.toast('Manager aktualisiert');
            }
        });
    };

    // --- TAB SWITCHING LOGIC (RESTORED) ---
    
    // 1. Main View Switcher
    window.switchMainTab = (tabName) => {
        document.querySelectorAll('.main-tab').forEach(el => el.classList.add('hidden'));
        document.getElementById('main-tab-' + tabName).classList.remove('hidden');
        
        document.querySelectorAll('.nav-link').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-100', 'text-brandDark');
            btn.classList.add('text-slate-500', 'hover:bg-slate-50');
        });
        
        const activeBtn = document.getElementById('nav-' + tabName);
        if(activeBtn) {
            activeBtn.classList.add('active', 'bg-slate-100', 'text-brandDark');
            activeBtn.classList.remove('text-slate-500', 'hover:bg-slate-50');
        }

        if (tabName === 'attendance') {
            loadAttendanceData();
        }
    };
    function loadAttendanceData() { console.log("Loading Attendance Data..."); }

    // 2. Sidebar Tab Switcher
    document.querySelectorAll('.wf-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            document.querySelectorAll('.wf-tab-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('wf-tab-' + target).classList.remove('hidden');
            
            document.querySelectorAll('.wf-tab').forEach(b => {
                b.classList.remove('bg-slate-900', 'text-white');
                b.classList.add('text-slate-600', 'hover:bg-slate-100');
            });
            
            btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
            btn.classList.add('bg-slate-900', 'text-white');
        });
    });

    // 3. Modal Tab Switcher
    window.switchModalTab = (tabName) => {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('tab-btn-' + tabName).classList.add('active');
    };

    // 4. Board View Switcher
    window.switchView = (viewName) => {
        WF.state.currentView = viewName;
        document.querySelectorAll('.view-container').forEach(el => el.classList.add('hidden'));
        document.getElementById('view-' + viewName).classList.remove('hidden');
        ['board', 'gantt', 'list'].forEach(v => {
            const btn = document.getElementById('btn-view-' + v);
            if (v === viewName) {
                btn.className = 'px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark';
            } else {
                btn.className = 'px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark';
            }
        });
        if (viewName === 'board') renderBoard();
        if (viewName === 'gantt') renderGantt();
        if (viewName === 'list') renderList();
    };

    // --- OTHER MODAL LOGIC (RESTORED) ---
    const dndModal = document.getElementById('wf-dnd-assign-modal');
    async function openDnDModal({managerId, date, time, teamIds, onSave, onCancel}) {
        dndModal.classList.remove('hidden');
        const pmSel = $('#wf-dnd-pm');   
        const teamSel = $('#wf-dnd-crew'); 
        
        if(!pmSel.hasClass("select2-hidden-accessible")) {
            pmSel.select2({ width: '100%', placeholder: 'Projektleiter', dropdownParent: $('#wf-dnd-assign-card') });
            teamSel.select2({ width: '100%', placeholder: 'Team wählen', multiple: true, dropdownParent: $('#wf-dnd-assign-card') });
        }

        pmSel.empty();
        teamSel.empty();
        
        if(!WF.state.employeesActive.length) {
             const res = await WF.httpGet(WF.api.employees);
             WF.state.employeesActive = res.data || [];
        }
        
        WF.state.employeesActive.forEach(e => {
            const optPm = new Option(e.full_name, e.id, false, e.id == managerId);
            pmSel.append(optPm);
            const isSelected = teamIds && teamIds.includes(e.id);
            const optTeam = new Option(e.full_name, e.id, false, isSelected);
            teamSel.append(optTeam);
        });

        pmSel.trigger('change');
        teamSel.trigger('change');

        document.getElementById('wf-dnd-date').value = date;
        document.getElementById('wf-dnd-time').value = time;

        const saveBtn = document.getElementById('wf-dnd-save');
        const newSave = saveBtn.cloneNode(true);
        saveBtn.parentNode.replaceChild(newSave, saveBtn);
        
        newSave.onclick = () => {
            onSave({
                date: document.getElementById('wf-dnd-date').value,
                time: document.getElementById('wf-dnd-time').value,
                team: teamSel.val().map(Number) 
            });
            dndModal.classList.add('hidden');
        };

        const cancelBtn = document.getElementById('wf-dnd-cancel');
         const newCancel = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);
        newCancel.onclick = () => {
            dndModal.classList.add('hidden');
            if(onCancel) onCancel();
        };
    }

   // --- Modal Logic ---

      /**
       * Opens the modal and populates it with data from WF.state.items
       */
      window.openTaskModal = (id) => {
          // 1. Find the item in the local state
          const item = WF.state.items.find(i => i.id == id);
          if (!item) return;

          // 2. Populate Basic Fields
          document.getElementById('modal-active-item-id').value = item.id;
          document.getElementById('modal-edit-title').value = item.title || '';
          document.getElementById('modal-edit-description').value = item.description || '';

          // 3. Populate Date & Time
          let dateText = 'Nicht geplant';
          let timeText = '--:--';
          
          if (item.planned_date) {
              const d = new Date(item.planned_date);
              dateText = d.toLocaleDateString('de-DE', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
          }
          
          if (item.planned_time) {
              timeText = item.planned_time + ' Uhr';
              if (item.duration_minutes) {
                  timeText += ` (${item.duration_minutes} Min)`;
              }
          }

          document.getElementById('modal-date-display').innerText = dateText;
          document.getElementById('modal-time-display').innerText = timeText;

          // 4. Populate Badges (Type & Status)
          const badgesContainer = document.getElementById('modal-badges');
          let typeClass = 'bg-slate-100 text-slate-600';
          let typeIcon = '<i class="fa-solid fa-check"></i>';
          let typeLabel = 'Task';

          // Determine Type Styles
          switch(item.source_type) {
              case 'ticket':
                  typeClass = 'bg-rose-100 text-rose-600 border border-rose-200';
                  typeIcon = '<i class="fa-solid fa-ticket"></i>';
                  typeLabel = 'Ticket';
                  break;
              case 'appointment':
                  typeClass = 'bg-purple-100 text-purple-600 border border-purple-200';
                  typeIcon = '<i class="fa-regular fa-calendar"></i>';
                  typeLabel = 'Termin';
                  break;
              case 'phase_activity':
                  typeClass = 'bg-blue-100 text-blue-600 border border-blue-200';
                  typeIcon = '<i class="fa-solid fa-layer-group"></i>';
                  typeLabel = 'Phase';
                  break;
              case 'personal_task':
                  typeClass = 'bg-orange-100 text-orange-600 border border-orange-200';
                  typeIcon = '<i class="fa-solid fa-user-check"></i>';
                  typeLabel = 'Persönlich';
                  break;
          }

          // Determine Status Text
          let statusLabel = 'Geplant';
          let statusClass = 'bg-slate-100 text-slate-500';
          if(item.status === 'done' || item.status === 'completed') {
              statusLabel = 'Erledigt';
              statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
          } else if (item.status === 'in_progress') {
              statusLabel = 'In Arbeit';
              statusClass = 'bg-amber-100 text-amber-700 border border-amber-200';
          }

          badgesContainer.innerHTML = `
              <span class="px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-2 ${typeClass}">
                  ${typeIcon} ${typeLabel}
                  ${item.source_id ? `<span class="opacity-50">#${item.source_id}</span>` : ''}
              </span>
              <span class="px-3 py-1 rounded-lg text-xs font-bold border ${statusClass}">
                  ${statusLabel}
              </span>
          `;

          // 5. Populate Team List
          const teamContainer = document.getElementById('modal-team-list');
          let teamHtml = '';

          // Add Lead (Project Manager)
          if (item.lead) {
              const photo = item.lead.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.lead.full_name)}`;
              teamHtml += `
                  <div class="flex items-center gap-2 pr-4 border-r border-slate-100">
                      <img src="${photo}" class="w-8 h-8 rounded-full border-2 border-brandDark" title="Verantwortlich">
                      <div class="leading-tight">
                          <div class="text-xs font-bold text-slate-800">${WF.escapeHtml(item.lead.full_name)}</div>
                          <div class="text-[10px] text-brandDark font-bold uppercase">Lead</div>
                      </div>
                  </div>
              `;
          }

          // Add Members
          if (item.members && item.members.length > 0) {
              item.members.forEach(m => {
                  const photo = m.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(m.full_name)}`;
                  teamHtml += `
                      <div class="flex items-center gap-2">
                          <img src="${photo}" class="w-8 h-8 rounded-full border border-slate-200" title="Mitarbeiter">
                          <div class="leading-tight">
                              <div class="text-xs font-bold text-slate-700">${WF.escapeHtml(m.full_name)}</div>
                              <div class="text-[10px] text-slate-400">Team</div>
                          </div>
                      </div>
                  `;
              });
          } else if (!item.lead) {
              teamHtml = '<span class="text-sm text-slate-400 italic">Kein Team zugewiesen.</span>';
          }
 

          // ============================================================
          // CHECKLIST TAB LOGIC
          // ============================================================
          const checklistContainer = document.getElementById('modal-subtasks-list');

          if (item.checklist && item.checklist.length > 0) {
              checklistContainer.innerHTML = item.checklist.map(task => `
                  <div class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-white hover:border-sky-200 transition-colors group/task">
                      <div class="pt-0.5">
                          <input type="checkbox" 
                                class="w-5 h-5 rounded-md border-slate-300 text-brandDark focus:ring-brandDark/20 cursor-pointer transition-all"
                                ${task.is_completed ? 'checked' : ''} 
                                onchange="toggleChecklist(this, ${task.id})"> </div>
                      <div class="flex-1">
                          <div class="text-sm font-semibold text-slate-700 transition-all ${task.is_completed ? 'line-through text-slate-400' : ''}">
                              ${WF.escapeHtml(task.title)}
                          </div>
                      </div>
                  </div>
              `).join('');
          } else {
              // Show Empty State based on Type
              let emptyMsg = "Keine Checkliste verfügbar.";
              if(item.source_type === 'appointment') emptyMsg = "Termine haben keine Standard-Checkliste.";
              
              checklistContainer.innerHTML = `
                  <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                      <i class="fa-solid fa-clipboard-list text-2xl mb-2 opacity-50"></i>
                      <span class="text-xs italic">${emptyMsg}</span>
                  </div>
              `;
          }

          // 8. Show Modal (Existing code)
          const modal = document.getElementById('task-modal');
          const content = document.getElementById('task-modal-content');
          
          modal.classList.remove('hidden');
          setTimeout(() => {
              content.classList.remove('translate-x-full');
          }, 10);
      };
 

      // --- Object Data Modal Logic ---

    // 1. Enable Button on Project Selection
    document.getElementById('project-selector').addEventListener('change', (e) => {
        const btn = document.getElementById('btn-object-data');
        if(e.target.value) {
            btn.disabled = false;
            btn.classList.remove('text-slate-400', 'cursor-not-allowed');
            btn.classList.add('text-brandDark', 'hover:bg-slate-50', 'cursor-pointer');
        } else {
            btn.disabled = true;
            btn.classList.add('text-slate-400', 'cursor-not-allowed');
            btn.classList.remove('text-brandDark', 'hover:bg-slate-50', 'cursor-pointer');
        }
    });

    window.openObjectDataModal = async () => {
        const modal = document.getElementById('object-data-modal');
        modal.classList.remove('hidden');
        switchObjectTab(1); // Reset to first tab
        
        // Fetch Data
        await fetchAndPopulateObjectData();
    };

    window.closeObjectDataModal = () => {
        document.getElementById('object-data-modal').classList.add('hidden');
    };

    window.switchObjectTab = (step) => {
        // Update Buttons
        document.querySelectorAll('.obj-tab-btn').forEach(btn => {
            const isActive = btn.dataset.step == step;
            btn.classList.toggle('active', isActive);
            // Toggle Border/Color classes manually if needed, or rely on CSS .active class
        });

        // Show Content
        document.querySelectorAll('.obj-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('obj-step-' + step).classList.remove('hidden');
    };

    async function fetchAndPopulateObjectData() {
            const container = document.getElementById('object-data-form-container');
            if (!container) return;

            // 1. Set Loading State
            container.querySelectorAll('input, textarea').forEach(el => el.value = 'Lade...');

            // 2. Get IDs
            const projectSelect = document.getElementById('project-selector');
            const projectId = projectSelect ? projectSelect.value : null;
            
            // Ensure we have a customer from the global state
            const customerId = WF.state.customer ? WF.state.customer.id : null;

            if (!customerId) {
                WF.toast('Kein Kunde ausgewählt');
                container.querySelectorAll('input, textarea').forEach(el => el.value = '');
                return;
            }

            try {
                // 3. Fetch Data using the configured endpoint and helper
                const res = await WF.httpGet(WF.api.objectData, {
                    customer_id: customerId,
                    project_id: projectId
                });
                
                if (res.ok && res.data) {
                    const data = res.data;
                    
                    // 4. Auto-populate inputs
                    container.querySelectorAll('input, textarea').forEach(el => {
                        const key = el.getAttribute('name');
                        
                        // Only update if the key exists in the response data
                        if (key && data[key] !== undefined && data[key] !== null) {
                            el.value = data[key];
                        } else if (key) {
                            el.value = ''; // Clear if no data
                        }
                    });

                    // 5. Special Mapping Handling
                    // Solar Thermal: Map database boolean/int (1) to human readable 'Ja'
                    if (data.solar_thermal == 1 || data.solar_thermal === '1' || data.solar_thermal === true) {
                        const stInput = container.querySelector('input[name="solar_thermal"]');
                        if (stInput) stInput.value = 'Ja';
                    } else if (data.solar_thermal == 0 || data.solar_thermal === '0') {
                        const stInput = container.querySelector('input[name="solar_thermal"]');
                        if (stInput) stInput.value = 'Nein';
                    }
                    
                    // Income Level Mapping (Example based on your Schema comments)
                    if (data.income_level === '0') {
                        const incInput = container.querySelector('input[name="income_level"]');
                        if (incInput) incInput.value = 'Über 40000';
                    }

                } else {
                    WF.toast(res.message || 'Keine Daten gefunden');
                    container.querySelectorAll('input, textarea').forEach(el => el.value = '-');
                }
            } catch (e) {
                console.error('Object Data Load Error:', e);
                WF.toast('Fehler beim Laden der Daten');
                container.querySelectorAll('input, textarea').forEach(el => el.value = 'Fehler');
            }
        }
      // --- Checklist Actions ---
    window.toggleChecklist = async (checkbox, checklistId) => {
        const isChecked = checkbox.checked;
        const textEl = checkbox.closest('.flex').querySelector('.text-sm');

        // 1. Optimistic UI Update (Immediate feedback)
        if (isChecked) {
            textEl.classList.add('line-through', 'text-slate-400');
        } else {
            textEl.classList.remove('line-through', 'text-slate-400');
        }

        // 2. Send to Backend
        try {
            const url = `/planner/checklist/${checklistId}/toggle`; 
            const res = await WF.postJson(url, { is_completed: isChecked });

            if (res.ok) {
                // Update local state item so if we close/reopen modal, it remembers
                WF.state.items.forEach(i => {
                    if(i.checklist) {
                        const found = i.checklist.find(c => c.id === checklistId);
                        if(found) found.is_completed = isChecked;
                    }
                });
                WF.toast('Status gespeichert');
            } else {
                throw new Error('Save failed');
            }
        } catch (e) {
            console.error(e);
            WF.toast('Fehler beim Speichern');
            // Revert UI
            checkbox.checked = !isChecked;
            if (!isChecked) textEl.classList.add('line-through', 'text-slate-400');
            else textEl.classList.remove('line-through', 'text-slate-400');
        }
    };

      // --- Save Function ---
      window.saveActiveTask = async () => {
          const id = document.getElementById('modal-active-item-id').value;
          const title = document.getElementById('modal-edit-title').value;
          const desc = document.getElementById('modal-edit-description').value;

          if (!id) return;

          // Prepare URL
          const url = WF.api.planItemUpdate
              .replace('___PLAN___', WF.state.planId)
              .replace('___ITEM___', id);

          try {
              const res = await WF.patchJson(url, {
                  title: title,
                  description: desc
              });

              if (res.ok) {
                  // Update local state
                  const item = WF.state.items.find(i => i.id == id);
                  if (item) {
                      item.title = title;
                      item.description = desc;
                  }
                  
                  // Re-render views to show changes
                  renderAllViews();
                  WF.toast('Änderungen gespeichert.');
                  closeModal();
              }
          } catch (e) {
              console.error(e);
              alert('Fehler beim Speichern.');
          }
      };

      // --- Delete Function ---
      window.deleteActiveTask = async () => {
          const id = document.getElementById('modal-active-item-id').value;
          if (!id) return;

          if (!confirm('Möchten Sie diese Aufgabe wirklich löschen?')) return;

          const url = WF.api.planItemDelete
              .replace('___PLAN___', WF.state.planId)
              .replace('___ITEM___', id);

          try {
              const response = await fetch(url, {
                  method: 'DELETE',
                  headers: {
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                      'Accept': 'application/json'
                  }
              });
              const res = await response.json();

              if (res.ok) {
                  // Remove from local state
                  WF.state.items = WF.state.items.filter(i => i.id != id);
                  
                  renderAllViews();
                  renderBacklogAnalytics(); // Update counters
                  WF.toast('Aufgabe gelöscht.');
                  closeModal();
              }
          } catch (e) {
              console.error(e);
              alert('Fehler beim Löschen.');
          }
      };

      // --- Helper: Tab Switching inside Modal ---
      window.switchModalTab = (tabName) => {
          // Hide all contents
          document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
          // Show selected content
          document.getElementById('tab-' + tabName).classList.remove('hidden');
          
          // Reset buttons
          document.querySelectorAll('.tab-btn').forEach(btn => {
              btn.classList.remove('active', 'text-brandDark', 'border-brandDark');
              btn.classList.add('text-slate-500', 'border-transparent');
          });
          
          // Activate button
          const btn = document.getElementById('tab-btn-' + tabName);
          btn.classList.add('active', 'text-brandDark', 'border-brandDark');
          btn.classList.remove('text-slate-500', 'border-transparent');
      };
    window.closeModal = () => {
        document.getElementById('task-modal-content').classList.add('translate-x-full');
        setTimeout(() => document.getElementById('task-modal').classList.add('hidden'), 200);
    }

     // --- MANUAL TASK LOGIC ---

    // 1. Open Modal
    window.addManualTask = () => {
        // Reset Form
        document.getElementById('manual-title').value = '';
        document.getElementById('manual-description').value = '';
        document.getElementById('manual-date').value = new Date().toISOString().split('T')[0]; // Default today
        document.getElementById('manual-time').value = '08:00';
        document.getElementById('manual-duration').value = '60';
        document.getElementById('manual-is-bulk').checked = false;
        document.getElementById('bulk-steps-container').innerHTML = '';
        
        // Reset Select2
        $('#manual-employees').val(null).trigger('change');
        
        toggleBulkMode(); // Ensure UI state matches checkbox
        
        // Show Modal
        document.getElementById('manual-task-modal').classList.remove('hidden');
        
        // Init Select2 if not already done
        initManualSelect2();
    };

    window.closeManualTaskModal = () => {
        document.getElementById('manual-task-modal').classList.add('hidden');
    };

    // 2. Initialize Select2 for Manual Modal
    function initManualSelect2() {
        const sel = $('#manual-employees');
        if (sel.hasClass("select2-hidden-accessible")) return;

        sel.select2({
            dropdownParent: $('#manual-task-modal'),
            placeholder: 'Mitarbeiter wählen...',
            width: '100%'
        });

        // Populate from State (Employees Active)
        sel.empty();
        WF.state.employeesActive.forEach(e => {
            const option = new Option(e.full_name, e.id, false, false);
            sel.append(option);
        });
    }

    // 3. Toggle Bulk Mode UI
    window.toggleBulkMode = () => {
        const isBulk = document.getElementById('manual-is-bulk').checked;
        const container = document.getElementById('bulk-steps-container');
        const btn = document.getElementById('btn-add-step');
        const durationInput = document.getElementById('manual-duration');

        if (isBulk) {
            container.classList.remove('hidden');
            btn.classList.remove('hidden');
            durationInput.disabled = true; // Duration calculated by sum of steps
            durationInput.classList.add('bg-slate-100', 'text-slate-400');
            if(container.children.length === 0) addBulkStep(); // Add first row
        } else {
            container.classList.add('hidden');
            btn.classList.add('hidden');
            durationInput.disabled = false;
            durationInput.classList.remove('bg-slate-100', 'text-slate-400');
        }
    };

    // 4. Add Bulk Step Row
    window.addBulkStep = () => {
        const container = document.getElementById('bulk-steps-container');
        const id = Date.now(); // Temp ID
        
        const html = `
            <div class="flex items-center gap-2 animate-pulse-once" id="step-${id}">
                <div class="flex-1">
                    <input type="text" class="step-title w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-brandDark" placeholder="Schritt...">
                </div>
                <div class="w-24">
                    <input type="number" class="step-duration w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-center" placeholder="Min" value="30">
                </div>
                <button onclick="document.getElementById('step-${id}').remove()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    };

    // 5. Submit to Backend
    window.submitManualTask = async () => {
        const title = document.getElementById('manual-title').value;
        if(!title) { alert('Bitte Titel eingeben'); return; }

        const isBulk = document.getElementById('manual-is-bulk').checked;
        const employees = $('#manual-employees').val();
        
        const payload = {
            plan_id: WF.state.planId,
            title: title,
            description: document.getElementById('manual-description').value,
            start_date: document.getElementById('manual-date').value,
            due_time: document.getElementById('manual-time').value,
            employees: employees ? employees.map(Number) : [],
            priority: 'medium'
        };

        if (isBulk) {
            const steps = [];
            document.querySelectorAll('#bulk-steps-container > div').forEach(row => {
                const t = row.querySelector('.step-title').value;
                const d = row.querySelector('.step-duration').value;
                if(t) steps.push({ task: t, duration: parseInt(d) || 0 });
            });
            
            if(steps.length === 0) { alert('Bitte mindestens einen Schritt hinzufügen.'); return; }
            payload.subtasks = steps;
        } else {
            payload.duration = parseInt(document.getElementById('manual-duration').value) || 60;
        }

        // Send Request
        try {
            const res = await WF.postJson(WF.api.dnd.add.replace('dnd/add', 'planner/manual/store'), payload); // Use new route or adjust helper
            // NOTE: Ensure WF.api.manualStore exists or manually construct URL
            // Temporary manual URL construction for safety:
            const url = window.location.origin + '/planner/manual/store';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            
            const json = await response.json();

            if (json.ok) {
                WF.state.items.push(json.item);
                renderAllViews();
                renderBacklogAnalytics();
                closeManualTaskModal();
                WF.toast('Aufgabe erstellt');
            } else {
                alert('Fehler: ' + (json.message || 'Unbekannt'));
            }
        } catch (e) {
            console.error(e);
            alert('Ein Fehler ist aufgetreten.');
        }
    };
    
  
</script>
 <!-- =========================================================
     0) Shared Core (USED BY BOTH: Drawer + Vorlage Browser)
     ========================================================= -->
<script>
(() => {
  if (window.__WF_MASTERSET_CORE__) return;
  window.__WF_MASTERSET_CORE__ = true;

  // -----------------------------
  // Config helpers
  // -----------------------------
  const baseUrl = () =>
    (document.querySelector('meta[name="planner-base-url"]')?.content || '').replace(/\/$/, '');

  const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content || '';

  const escapeHtml = (s) => String(s ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;');

  const money = (v) => {
    if (v === null || v === undefined || v === '') return '';
    const n = Number(v);
    if (Number.isNaN(n)) return escapeHtml(v);
    return n.toLocaleString('de-DE') + ' €';
  };

  // -----------------------------
  // Fetch helpers
  // -----------------------------
  async function safeJSON(res) {
    const ct = (res.headers.get('content-type') || '').toLowerCase();
    const text = await res.text();

    if (ct.includes('application/json') || text.trim().startsWith('{') || text.trim().startsWith('[')) {
      try { return JSON.parse(text); } catch (e) {}
    }
    return { ok: false, message: 'Non-JSON response', _raw: text, _status: res.status };
  }

  async function getJSON(url, params = {}) {
    const u = new URL(url, window.location.origin);
    Object.entries(params).forEach(([k, v]) => {
      if (v !== null && v !== undefined && String(v).length) u.searchParams.set(k, v);
    });

    const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' } });
    const data = await safeJSON(res);

    if (!res.ok || data?.ok === false) {
      throw new Error(data?.message || data?.error || `Request failed (${res.status})`);
    }
    return data;
  }

  async function postJSON(url, payload = {}) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify(payload),
    });

    const data = await safeJSON(res);

    if (!res.ok || data?.ok === false) {
      throw new Error(data?.message || data?.error || `Request failed (${res.status})`);
    }
    return data;
  }

  // -----------------------------
  // Data normalizers
  // -----------------------------
  // Your API: component.product.product
  function componentTitle(c) {
    return (
      c?.product?.product ||     // ✅ your payload
      c?.product?.name ||
      c?.product_name ||
      c?.name ||
      c?.title ||
      'Komponente'
    );
  }

  // Your API: labor has hours + hourly_rate (not minutes + rate)
  function laborMinutes(l) {
    const m = l?.minutes ?? l?.duration_minutes ?? l?.min ?? null;
    if (m !== null && m !== undefined && String(m) !== '') return Number(m);

    const h = l?.hours ?? null;
    if (h !== null && h !== undefined && String(h) !== '') {
      const n = Number(h);
      if (!Number.isNaN(n)) return Math.round(n * 60);
    }
    return null;
  }

  function laborRate(l) {
    return l?.hourly_rate ?? l?.rate ?? null;
  }

  // ✅ NEW: build "Position · Department · Employee" labels
  function laborTitle(l) {
    // from eager-load:
    const pos = l?.position?.position || null; // positions.position
    return pos || l?.name || l?.title || 'Arbeit';
  }

  function laborSubtitle(l) {
    const dep = l?.department?.department_name || null; // departments.department_name

    const emp = [
      l?.employee?.title,
      l?.employee?.name,
      l?.employee?.midname,
      l?.employee?.lastname,
    ].filter(Boolean).join(' ') || null;

    const parts = [dep, emp].filter(Boolean);
    return parts.length ? parts.join(' · ') : '';
  }

  // Totals can come from backend or we compute a light fallback
  function computeTotalsFallback(set) {
    const comps = set?.components || set?.items || [];
    const labor = set?.labor || set?.work || [];

    let cost = 0;
    for (const c of comps) {
      const qty = Number(c?.qty ?? c?.quantity ?? 0);
      const price = Number(c?.unit_price ?? 0);
      if (!Number.isNaN(qty) && !Number.isNaN(price)) cost += qty * price;
    }

    let minutes = 0;
    for (const l of labor) {
      const m = laborMinutes(l);
      if (m != null && !Number.isNaN(Number(m))) minutes += Number(m);
    }

    return {
      labor_minutes: minutes || null,
      cost_total: cost || null,
    };
  }

  function formatTotalsMini(totals) {
    if (!totals) return '—';

    const laborMin = totals.labor_minutes ?? totals.laborMin ?? null;
    const cost     = totals.cost_total ?? totals.cost ?? null;

    const parts = [
      laborMin != null ? `${Number(laborMin)} Min` : null,
      cost != null ? `${Number(cost).toLocaleString('de-DE')} €` : null
    ].filter(Boolean);

    return parts.join(' · ') || '—';
  }

  // -----------------------------
  // UI renderers
  // -----------------------------
function renderSetRow(set, opts = {}) {
    const { selectedId = null, badgeHtml = '' } = opts;
    const active = Number(selectedId) === Number(set?.id);

    return `
      <button type="button"
        draggable="true"
        ondragstart="window.handleMasterSetDragStart(event, ${set.id})"
        class="wf-masterset-drag w-full text-left p-3 rounded-xl border ${active ? 'border-brandDark bg-blue-50/40' : 'border-slate-200 bg-white'}
               hover:border-brandDark/40 transition flex items-start gap-3 cursor-grab active:cursor-grabbing group"
        data-set-id="${set.id}">
        
        <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center text-slate-600 shrink-0 transition-colors">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        
        <div class="min-w-0 flex-1 pointer-events-none"> <div class="flex items-center gap-2">
            <div class="font-extrabold text-slate-800 text-sm truncate">
                ${escapeHtml(set.name || set.title || ('Master Set #' + set.id))}
            </div>
            ${badgeHtml || ''}
          </div>
          <div class="text-xs text-slate-500 mt-0.5 truncate">
            ${escapeHtml(set.description || '')}
          </div>
        </div>
        
        <div class="text-xs font-bold text-slate-400 pointer-events-none">#${set.id}</div>
      </button>
    `;
  }

  function buildDetailsHTML(set, opts = {}) {
    const {
      showComponentMeta = false,
      showLaborRate     = false,
      showHeaders       = true,
      showLaborMeta     = true,  // ✅ NEW: show department/employee line
    } = opts;

    const comps  = set?.components || set?.items || [];
    const labor  = set?.labor || set?.work || [];

    let html = '';

    // Components
    if (Array.isArray(comps) && comps.length) {
      if (showHeaders) html += `<div class="text-xs font-extrabold text-slate-400 uppercase">Komponenten</div>`;
      html += `<div class="space-y-2">` + comps.map(c => {
        const title = componentTitle(c);

        const qty  = c?.qty ?? c?.quantity ?? '';
        const sku  = c?.sku ?? c?.product?.sku ?? '';
        const unit = c?.unit_price;

        return `
          <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/40">
            <div class="flex justify-between gap-3">
              <div class="font-bold text-sm text-slate-800 truncate">${escapeHtml(title)}</div>
              <div class="text-xs font-extrabold text-slate-500">${escapeHtml(qty)}</div>
            </div>

            ${c?.note || c?.description ? `<div class="text-xs text-slate-500 mt-1">${escapeHtml(c.note || c.description)}</div>` : ''}

            ${showComponentMeta ? `
              <div class="text-[11px] text-slate-500 mt-1">
                ${sku ? `<span class="font-bold text-slate-400">SKU:</span> ${escapeHtml(sku)}${(unit != null ? ' · ' : '')}` : ''}
                ${unit != null ? `<span class="font-bold text-slate-400">Preis:</span> ${money(unit)}` : ''}
              </div>
            ` : ''}
          </div>
        `;
      }).join('') + `</div>`;
    } else {
      html += `<div class="text-xs text-slate-400 italic">Keine Komponenten.</div>`;
    }

    html += `<div class="pt-3"></div>`;

    // Labor (✅ now shows Position + Department/Employee)
    if (Array.isArray(labor) && labor.length) {
      if (showHeaders) html += `<div class="text-xs font-extrabold text-slate-400 uppercase">Arbeitszeit</div>`;
      html += `<div class="space-y-2">` + labor.map(l => {
        const mins = laborMinutes(l);
        const rate = laborRate(l);

        const title = laborTitle(l);
        const sub   = laborSubtitle(l);

        return `
          <div class="p-3 rounded-xl border border-slate-200 bg-white">
            <div class="flex justify-between gap-3">
              <div class="min-w-0">
                <div class="font-bold text-sm text-slate-800 truncate">${escapeHtml(title)}</div>
                ${showLaborMeta && sub ? `<div class="text-[11px] text-slate-500 truncate">${escapeHtml(sub)}</div>` : ''}
              </div>
              <div class="text-xs font-extrabold text-slate-500">${mins != null ? escapeHtml(mins) + ' Min' : '—'}</div>
            </div>

            ${l?.note ? `<div class="text-xs text-slate-500 mt-1">${escapeHtml(l.note)}</div>` : ''}

            ${showLaborRate && rate != null ? `
              <div class="text-[11px] text-slate-500 mt-1">
                <span class="font-bold text-slate-400">Satz:</span> ${money(rate)}
              </div>
            ` : ''}
          </div>
        `;
      }).join('') + `</div>`;
    } else {
      html += `<div class="text-xs text-slate-400 italic">Kein Labor.</div>`;
    }

    return html;
  }

  window.handleMasterSetDragStart = (e, setId) => {
        e.dataTransfer.setData("type", "masterset");
        e.dataTransfer.setData("setId", setId);
        e.dataTransfer.effectAllowed = "copy";
        // Optional: Add visual feedback
    };

    window.handleDragOver = (e) => {
        e.preventDefault(); // Necessary to allow dropping
        e.stopPropagation();
        // Add visual highlight
        const card = e.currentTarget;
        card.classList.add('border-brandDark', 'bg-blue-50/50');
    };

    window.handleDragLeave = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const card = e.currentTarget;
        card.classList.remove('border-brandDark', 'bg-blue-50/50');
    };

    window.handleDropOnCard = async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const card = e.currentTarget;
        card.classList.remove('border-brandDark', 'bg-blue-50/50');

        const type = e.dataTransfer.getData("type");
        const setId = e.dataTransfer.getData("setId");
        const itemId = card.dataset.itemId;

        if (type === 'masterset' && setId && itemId) {
            // Call Backend
            const url = `/planner/plans/${WF.state.planId}/items/${itemId}/master-sets/link-materials`; // Use route name if possible
            
            try {
                // Show loading state or toast
                WF.toast('Füge Materialien hinzu...');
                
                const res = await WF.postJson(url, { master_set_id: setId });
                
                if (res.ok) {
                    // Update Local State
                    const item = WF.state.items.find(i => i.id == itemId);
                    if (item) {
                        // Backend should return the updated materials array in res.materials
                        item.materials = res.materials; 
                        renderAllViews(); // Re-render to show the new cart list
                        WF.toast('Material erfolgreich hinzugefügt');
                    }
                }
            } catch (err) {
                console.error(err);
                alert('Fehler beim Hinzufügen des Materials');
            }
        }
    };

  // -----------------------------
  // Public API
  // -----------------------------
  window.WFMasterSetCore = {
    baseUrl,
    csrfToken,
    escapeHtml,
    getJSON,
    postJSON,

    computeTotalsFallback,
    formatTotalsMini,

    renderSetRow,
    buildDetailsHTML,

    // optional exports
    componentTitle,
    laborTitle,
    laborSubtitle,
    laborMinutes,
    laborRate,
  };
})();


</script>

<!-- =========================================================
     1) MASTER SET DRAWER (ONLY DRAWER)
     ========================================================= -->
<script>
(() => {
  if (window.__MASTER_SET_DRAWER_BOOTSTRAPPED__) return;
  window.__MASTER_SET_DRAWER_BOOTSTRAPPED__ = true;

  const C = window.WFMasterSetCore;
  if (!C) return;

  const $drawer  = document.getElementById('masterSetDrawer');
  const $overlay = document.getElementById('masterSetDrawerOverlay');
  const $close   = document.getElementById('masterSetDrawerClose');

  const $title   = document.getElementById('masterSetDrawerTitle');
  const $search  = document.getElementById('masterSetSearchInput');

  const $tabBtns = Array.from(document.querySelectorAll('.masterset-tab'));
  const $panelSearch = document.getElementById('masterSetSearchPanel');
  const $panelLinked = document.getElementById('masterSetLinkedPanel');

  const $list    = document.getElementById('master-set-list-container');
  const $linked  = document.getElementById('master-set-linked-container');
  const $details = document.getElementById('master-set-details-container');
  const $totalsMini = document.getElementById('masterSetTotalsMini');

  const $btnLink   = document.getElementById('masterSetLinkBtn');
  const $btnUnlink = document.getElementById('masterSetUnlinkBtn');

  if (
    !$drawer || !$overlay || !$close || !$title || !$search ||
    !$panelSearch || !$panelLinked || !$list || !$linked || !$details ||
    !$totalsMini || !$btnLink || !$btnUnlink
  ) return;

  const base = C.baseUrl();
  const API = {
    search:  () => `${base}/master-sets`,
    linked:  (planId, itemId) => `${base}/plans/${planId}/items/${itemId}/master-sets`,
    details: (setId) => `${base}/master-sets/${setId}`,
    link:    (planId, itemId) => `${base}/plans/${planId}/items/${itemId}/master-sets/link`,
    unlink:  (planId, itemId) => `${base}/plans/${planId}/items/${itemId}/master-sets/unlink`,
  };

  const state = {
    type: null,
    targetId: null,
    planId: null,
    q: '',
    activeTab: 'search',
    selectedSetId: null,
    linkedSetId: null,
  };

  function openDrawer() {
    $overlay.classList.remove('hidden');
    $drawer.classList.remove('translate-x-full');
    document.documentElement.style.overflow = 'hidden';
  }

  function closeDrawer() {
    $drawer.classList.add('translate-x-full');
    setTimeout(() => $overlay.classList.add('hidden'), 150);
    document.documentElement.style.overflow = '';
  }

  function setTab(tab) {
    state.activeTab = tab;

    $tabBtns.forEach(b => {
      const active = b.dataset.tab === tab;
      b.classList.toggle('bg-white', active);
      b.classList.toggle('shadow-sm', active);
      b.classList.toggle('text-brandDark', active);
      b.classList.toggle('text-slate-500', !active);
    });

    $panelSearch.classList.toggle('hidden', tab !== 'search');
    $panelLinked.classList.toggle('hidden', tab !== 'linked');
  }

  function setButtonsState() {
    const canLink   = !!state.selectedSetId && state.selectedSetId !== state.linkedSetId;
    const canUnlink = !!state.linkedSetId;

    $btnLink.disabled = !canLink;
    $btnUnlink.disabled = !canUnlink;

    $btnLink.classList.toggle('opacity-50', !canLink);
    $btnLink.classList.toggle('cursor-not-allowed', !canLink);

    $btnUnlink.classList.toggle('opacity-50', !canUnlink);
    $btnUnlink.classList.toggle('cursor-not-allowed', !canUnlink);
  }

  async function loadSearch() {
    $list.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Lade…</div>`;
    try {
      const json = await C.getJSON(API.search(), { q: state.q });
      const rows = json.data || json.items || json.results || [];
      $list.innerHTML = rows.length
        ? rows.map(s => C.renderSetRow(s, { selectedId: state.selectedSetId })).join('')
        : `<div class="text-xs text-slate-400 italic p-2">Keine Treffer.</div>`;
    } catch (e) {
      $list.innerHTML = `<div class="text-xs text-rose-600 p-2">Fehler: ${C.escapeHtml(e.message)}</div>`;
    }
  }

  async function loadLinked() {
    $linked.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Lade…</div>`;

    if (!state.planId || !state.targetId) {
      $linked.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Kein Item gewählt.</div>`;
      return;
    }

    try {
      const json = await C.getJSON(API.linked(state.planId, state.targetId));
      const linkedSet = json.data || json.item || json.master_set || null;

      if (!linkedSet) {
        state.linkedSetId = null;
        $linked.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Nichts verknüpft.</div>`;
        setButtonsState();
        return;
      }

      state.linkedSetId = linkedSet.id;
      $linked.innerHTML = C.renderSetRow(linkedSet, {
        selectedId: state.selectedSetId,
        badgeHtml: `<span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded">VERKNÜPFT</span>`
      });

      await selectSet(linkedSet.id);
      setButtonsState();
    } catch (e) {
      state.linkedSetId = null;
      setButtonsState();
      $linked.innerHTML = `<div class="text-xs text-rose-600 p-2">Fehler: ${C.escapeHtml(e.message)} (linked)</div>`;
    }
  }

  async function selectSet(setId) {
    state.selectedSetId = Number(setId);

    $details.innerHTML = `<div class="text-xs text-slate-400 italic">Lade Details…</div>`;
    $totalsMini.textContent = '—';

    [...$list.querySelectorAll('[data-set-id]'), ...$linked.querySelectorAll('[data-set-id]')].forEach(btn => {
      const active = Number(btn.dataset.setId) === state.selectedSetId;
      btn.classList.toggle('border-brandDark', active);
      btn.classList.toggle('bg-blue-50/40', active);
      btn.classList.toggle('border-slate-200', !active);
      btn.classList.toggle('bg-white', !active);
    });

    try {
      const json = await C.getJSON(API.details(state.selectedSetId));
      const set = json.data || json.item || json;

      $title.textContent = set.name || set.title || 'Details';

      const totals = set.totals || set.total || null;
      $totalsMini.textContent = C.formatTotalsMini(totals || C.computeTotalsFallback(set));

      $details.innerHTML = C.buildDetailsHTML(set, {
        showComponentMeta: false,
        showLaborRate: false,
      }) || `<div class="text-xs text-slate-400 italic">Keine Details vorhanden.</div>`;

      setButtonsState();
    } catch (e) {
      $details.innerHTML = `<div class="text-xs text-rose-600">Fehler: ${C.escapeHtml(e.message)} (details)</div>`;
      $totalsMini.textContent = '—';
      setButtonsState();
    }
  }

  async function linkSelected() {
    if (!state.planId || !state.targetId || !state.selectedSetId) return;
    try {
      await C.postJSON(API.link(state.planId, state.targetId), { master_set_id: state.selectedSetId });
      await loadLinked();
      setTab('linked');
    } catch (e) {
      alert(e.message || 'Link fehlgeschlagen');
    }
  }

  async function unlink() {
    if (!state.planId || !state.targetId) return;
    try {
      await C.postJSON(API.unlink(state.planId, state.targetId), {});
      state.linkedSetId = null;
      setButtonsState();
      await loadLinked();
    } catch (e) {
      alert(e.message || 'Unlink fehlgeschlagen');
    }
  }

  window.MasterSetDrawer = {
    open: async (type, targetId) => {
      state.type = type;
      state.targetId = Number(targetId || 0) || null;
      state.planId = window.__WF?.state?.planId || null;

      state.selectedSetId = null;
      state.linkedSetId = null;

      setTab('search');
      openDrawer();

      await loadSearch();
      await loadLinked();
      setButtonsState();
    },
    close: closeDrawer
  };

  $overlay.addEventListener('click', closeDrawer);
  $close.addEventListener('click', closeDrawer);

  $tabBtns.forEach(btn => btn.addEventListener('click', async () => {
    setTab(btn.dataset.tab);
    if (btn.dataset.tab === 'search') await loadSearch();
    if (btn.dataset.tab === 'linked') await loadLinked();
  }));

  let t = null;
  $search.addEventListener('input', () => {
    state.q = $search.value.trim();
    clearTimeout(t);
    t = setTimeout(loadSearch, 250);
  });

  document.addEventListener('click', (e) => {
    const row = e.target.closest('[data-set-id]');
    if (!row) return;
    if (!row.closest('#master-set-list-container') && !row.closest('#master-set-linked-container')) return;
    e.preventDefault();
    selectSet(row.dataset.setId);
  });

  $btnLink.addEventListener('click', linkSelected);
  $btnUnlink.addEventListener('click', unlink);
})();
</script>

<!-- =========================================================
     2) VORLAGE / TEMPLATE AREA (Subtabs + Inline MasterSet Browser)
     ========================================================= -->
<script>
(() => {
  if (window.__WF_TEMPLATES_BOOTSTRAPPED__) return;
  window.__WF_TEMPLATES_BOOTSTRAPPED__ = true;

  const C = window.WFMasterSetCore;
  if (!C) return;

  // -----------------------------
  // Templates Sub Tabs (Aufgaben / Material)
  // -----------------------------
  const subBtns = Array.from(document.querySelectorAll('.wf-template-subtab'));
  const panelTasks = document.getElementById('wf-templates-subtab-tasks');
  const panelMat   = document.getElementById('wf-templates-subtab-materials');

  function setSubtab(name) {
    if (!panelTasks || !panelMat) return;

    const isTasks = name === 'tasks';
    panelTasks.classList.toggle('hidden', !isTasks);
    panelMat.classList.toggle('hidden', isTasks);

    subBtns.forEach(b => {
      const active = b.dataset.subtab === name;
      b.classList.toggle('bg-white', active);
      b.classList.toggle('shadow-sm', active);
      b.classList.toggle('text-brandDark', active);
      b.classList.toggle('text-slate-500', !active);
    });

    if (!isTasks) window.WF_MasterSetBrowser?.load?.();
  }

  if (subBtns.length && panelTasks && panelMat) {
    subBtns.forEach(btn => btn.addEventListener('click', () => setSubtab(btn.dataset.subtab)));
    setSubtab('tasks');
  }

  // -----------------------------
  // Master Set Browser (Inline)
  // -----------------------------
  const $q       = document.getElementById('wf-masterset-browser-q');
  const $list    = document.getElementById('wf-masterset-browser-list');
  const $details = document.getElementById('wf-masterset-browser-details');
  const $totals  = document.getElementById('wf-masterset-browser-totals');

  if (!$q || !$list || !$details || !$totals) return;

  const base = C.baseUrl();
  const API = {
    search:  () => `${base}/master-sets`,
    details: (id) => `${base}/master-sets/${id}`,
  };

  const state = { q: '', selectedId: null, loadedOnce: false, loading: false };

  function currentProductId() {
    const opt = document.querySelector('#project-selector option:checked');
    return opt?.dataset?.productId || null;
  }

  async function loadList() {
    if (state.loading) return;
    state.loading = true;

    $list.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Lade…</div>`;

    try {
      const json = await C.getJSON(API.search(), { q: state.q, product_id: currentProductId() || '' });
      const rows = json.data || json.items || json.results || [];
      $list.innerHTML = rows.length
        ? rows.map(s => C.renderSetRow(s, { selectedId: state.selectedId })).join('')
        : `<div class="text-xs text-slate-400 italic p-2">Keine Treffer.</div>`;
    } catch (e) {
      $list.innerHTML = `<div class="text-xs text-rose-600 p-2">Fehler: ${C.escapeHtml(e.message)}</div>`;
    } finally {
      state.loading = false;
      state.loadedOnce = true;
    }
  }

  async function selectSet(id) {
    state.selectedId = Number(id);

    $totals.textContent = '—';
    $details.innerHTML = `<div class="text-xs text-slate-400 italic">Lade Details…</div>`;

    $list.querySelectorAll('[data-set-id]').forEach(btn => {
      const active = Number(btn.dataset.setId) === state.selectedId;
      btn.classList.toggle('border-brandDark', active);
      btn.classList.toggle('bg-blue-50/40', active);
      btn.classList.toggle('border-slate-200', !active);
      btn.classList.toggle('bg-white', !active);
    });

    try {
      const json = await C.getJSON(API.details(state.selectedId));
      const set = json.data || json.item || json;

      const totals = set.totals || set.total || null;
      $totals.textContent = C.formatTotalsMini(totals || C.computeTotalsFallback(set));

      $details.innerHTML = C.buildDetailsHTML(set, {
        showComponentMeta: true,
        showLaborRate: true,
      });
    } catch (e) {
      $details.innerHTML = `<div class="text-xs text-rose-600">Fehler: ${C.escapeHtml(e.message)}</div>`;
    }
  }

  $list.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-set-id]');
    if (!btn) return;
    selectSet(btn.dataset.setId);
  });

  let t = null;
  $q.addEventListener('input', () => {
    state.q = $q.value.trim();
    clearTimeout(t);
    t = setTimeout(loadList, 250);
  });

  window.WF_MasterSetBrowser = {
    load: () => { if (!state.loadedOnce) loadList(); },
    reload: () => loadList(),
    select: (id) => selectSet(id)
  };
})();
</script>


</body>
</html>