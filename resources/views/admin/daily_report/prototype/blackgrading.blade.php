<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkForce Pro 2.0 - Einsatzplanung</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SortableJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brandDark: '#164191',
                        sky: '#74b2d4',
                        actionGreen: '#93c21c',
                        lightGreen: '#cfe09b',
                        background: '#f8fafc',
                    },
                    borderRadius: {
                        '3xl': '1.5rem',
                        '4xl': '2rem',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f8fafc;
            background-image: radial-gradient(#74b2d4 0.5px, transparent 0.5px), radial-gradient(#74b2d4 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        /* Glassmorphism Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .sortable-ghost {
            opacity: 0.4;
            background: #cfe09b;
            border: 2px dashed #93c21c;
        }
        
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }

        /* Gantt Specifics */
        .gantt-grid-line { border-right: 1px dashed #e2e8f0; height: 100%; position: absolute; top: 0; }
        
        .gantt-bar { 
            position: absolute; height: 42px; border-radius: 8px; display: flex; align-items: center; padding: 0 10px; 
            font-size: 12px; font-weight: 600; cursor: grab; transition: box-shadow 0.2s; z-index: 10; 
            white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .gantt-bar:active { cursor: grabbing; z-index: 40 !important; }
        .gantt-bar:hover { z-index: 20; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        
        /* Connection Handles */
        .gantt-handle {
            width: 16px; height: 16px; border-radius: 50%; 
            position: absolute; top: 50%; transform: translateY(-50%); 
            z-index: 30; display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: white; font-weight: bold;
            transition: all 0.2s; cursor: crosshair;
            opacity: 0; pointer-events: none;
        }
        .gantt-bar:hover .gantt-handle { opacity: 1; pointer-events: auto; }
        .gantt-handle:hover { transform: translateY(-50%) scale(1.2); }
        
        .gantt-handle.left { left: -8px; background: #64748b; border: 2px solid white; box-shadow: -2px 0 5px rgba(0,0,0,0.1); } /* Input/Target */
        .gantt-handle.left:hover { background: #ef4444; } /* Minus color on hover */
        
        .gantt-handle.right { right: -8px; background: #164191; border: 2px solid white; box-shadow: 2px 0 5px rgba(0,0,0,0.1); } /* Output/Source */
        .gantt-handle.right:hover { background: #93c21c; } /* Plus color on hover */

        .dependency-line { 
            stroke: #cbd5e1; stroke-width: 2; fill: none; transition: stroke 0.2s; cursor: pointer; pointer-events: stroke;
        }
        .dependency-line:hover { stroke: #ef4444; stroke-width: 3; }
        
        /* Modal Tabs */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { border-bottom-color: #164191; color: #164191; font-weight: 700; border-bottom-width: 2px; }
        .tab-btn { border-bottom-width: 2px; border-color: transparent; }
        
        /* Main Tabs */
        .main-tab { display: none; }
        .main-tab.active { display: flex; }
        .nav-link.active { background-color: #f1f5f9; color: #164191; font-weight: 700; }

        /* Avatar Stack */
        .avatar-stack { display: flex; -space-x: 0.5rem; }
        .avatar-stack img { border: 2px solid white; border-radius: 9999px; }
    </style>
</head>
<body class="text-slate-800 h-screen overflow-hidden flex flex-col font-sans">

    <!-- Top Navigation / Context Bar -->
    <header class="glass-panel z-50 px-6 py-3 flex items-center justify-between sticky top-0 shadow-sm">
        <div class="flex items-center gap-8">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brandDark rounded-xl flex items-center justify-center text-white shadow-lg shadow-brandDark/20">
                    <i class="fa-solid fa-bolt text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-brandDark tracking-tight">WorkForce <span class="text-sky">Pro 2.0</span></h1>
            </div>

            <!-- Context Selectors -->
            <div class="hidden md:flex items-center gap-4 border-l border-slate-300 pl-6">
                
                <!-- Level 1: Customer Selector -->
                <div class="relative group min-w-[200px]" id="customer-select-container">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <input type="text" id="customer-search-input" placeholder="Kunde suchen..." 
                        class="w-full pl-10 pr-8 py-2.5 rounded-lg bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white"
                        autocomplete="off"
                        onfocus="showCustomerDropdown()"
                        oninput="filterCustomerDropdown()"
                        onblur="setTimeout(hideCustomerDropdown, 200)"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" id="customer-chevron"></i>
                    </div>
                    
                    <!-- Dropdown List -->
                    <div id="customer-dropdown-list" class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto hidden z-50">
                        <!-- Items injected via JS -->
                    </div>
                </div>

                <!-- Level 2: Product/Object Selector -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-brandDark">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <select id="project-selector" onchange="changeProject(this.value)" class="appearance-none pl-10 pr-8 py-2.5 rounded-lg bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white min-w-[280px]" disabled>
                        <option value="">Objekt & Produkt wählen...</option>
                        <!-- Populated via JS based on Customer -->
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Stage Selector -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-actionGreen">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <select id="stage-selector" class="appearance-none pl-10 pr-8 py-2.5 rounded-lg bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-actionGreen/20 focus:border-actionGreen transition-all cursor-pointer hover:bg-white">
                        <option value="montage">Phase: Montage</option>
                        <option value="inbetriebnahme">Phase: Inbetriebnahme</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
                
                <!-- Date Display -->
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium bg-white/30 px-3 py-2 rounded-lg">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Heute, 24. Okt</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button onclick="openPlanWizard()" class="bg-gradient-to-r from-sky-500 to-brandDark hover:from-sky-600 hover:to-blue-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-500/20 transition-all active:scale-95 flex items-center gap-2 border border-white/20">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Planen</span>
            </button>
            <div class="h-8 w-px bg-slate-300 mx-2"></div>
             <button class="w-10 h-10 rounded-full bg-white text-slate-500 hover:text-brandDark hover:bg-sky/10 transition-colors flex items-center justify-center relative">
                <i class="fa-solid fa-bell"></i>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-brandDark to-sky flex items-center justify-center text-white font-bold shadow-md ring-2 ring-white">
                OM
            </div>
            <button onclick="savePlan()" class="bg-white hover:bg-slate-50 text-brandDark border border-slate-200 px-5 py-2.5 rounded-xl font-semibold shadow-sm transition-all active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Veröffentlichen</span>
            </button>
        </div>
    </header>

    <!-- Main Navigation Tabs -->
    <div class="px-6 py-2 bg-white border-b border-slate-200 flex gap-2 overflow-x-auto">
        <button onclick="switchMainTab('planning')" id="nav-planning" class="nav-link active px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-table-columns"></i> Planungstafel
        </button>
        <button onclick="switchMainTab('calendar')" id="nav-calendar" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-regular fa-calendar-days"></i> Kalender & Ungeplant
        </button>
        <button onclick="switchMainTab('delegated')" id="nav-delegated" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-handshake"></i> Delegiert
        </button>
        <button onclick="switchMainTab('history')" id="nav-history" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left"></i> Verlauf & Aktivität
        </button>
    </div>

    <!-- Main Workspace -->
    <main class="flex-1 p-4 md:p-6 overflow-hidden flex gap-6">
        
        <!-- VIEW 1: PLANNING (Split Screen) -->
        <div id="main-tab-planning" class="main-tab active h-full w-full flex gap-6">
            <!-- LEFT PANEL: The Backlog (Source) -->
            <section class="w-1/3 flex flex-col gap-4 h-full">
                <!-- Panel Header -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-6 bg-actionGreen rounded-full"></span>
                            Master-Checkliste
                        </h2>
                        <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded-md" id="task-count">0 Aufgaben</span>
                    </div>
                    <!-- Search & Filters -->
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                        <input type="text" id="task-search" placeholder="Aufgaben suchen..." 
                            class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none">
                    </div>
                </div>

                <!-- Scrollable List Container -->
                <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto overflow-x-hidden relative">
                    <div id="checklist-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                        <!-- Items injected via JS -->
                    </div>
                    <!-- Add Manual Task Button -->
                    <button onclick="addManualTask()" class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Manuelle Aufgabe
                    </button>
                </div>
            </section>

            <!-- RIGHT PANEL: Resource Scheduler (Target) -->
            <section class="w-2/3 flex flex-col gap-4 h-full relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-6 bg-brandDark rounded-full"></span>
                            Ressourcenplan
                        </h2>
                        <!-- View Switcher -->
                        <div class="flex bg-white/60 p-1 rounded-lg border border-slate-200">
                            <button onclick="switchView('board')" id="btn-view-board" class="px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark transition-all flex items-center gap-2">
                                <i class="fa-solid fa-table-columns"></i> Tafel
                            </button>
                            <button onclick="switchView('gantt')" id="btn-view-gantt" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                                <i class="fa-solid fa-timeline"></i> Gantt
                            </button>
                            <button onclick="switchView('list')" id="btn-view-list" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                                <i class="fa-solid fa-list-check"></i> Liste
                            </button>
                        </div>
                    </div>
                    <!-- Active Crew Widget -->
                    <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Aktives Team</span>
                        <div class="flex -space-x-2" id="active-crew-avatars"></div>
                        <button onclick="openCrewModal()" class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-brandDark hover:text-white transition-colors border border-slate-200 border-dashed" title="Team verwalten">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- SUB-VIEWS -->
                <div id="view-board" class="view-container grid grid-cols-1 md:grid-cols-3 gap-4 h-full overflow-hidden">
                    <!-- Board Columns JS Injected -->
                </div>
                
                <!-- GANTT VIEW (Updated) -->
                <div id="view-gantt" class="view-container hidden h-full glass-panel rounded-[2rem] flex flex-col overflow-hidden relative select-none">
                    <div class="h-12 bg-white/50 border-b flex items-center pl-48 pr-4 relative z-20">
                        <div id="time-scale" class="flex-1 flex justify-between text-xs text-slate-400 font-bold uppercase tracking-wider relative h-full items-center"></div>
                    </div>
                    <div id="gantt-body" class="flex-1 overflow-y-auto relative bg-slate-50/30">
                        <!-- SVG Layer for Dependencies -->
                        <svg id="gantt-lines" class="absolute top-0 left-0 w-full h-full z-0 overflow-visible pointer-events-none">
                            <defs>
                                <marker id="arrowhead" markerWidth="8" markerHeight="6" refX="8" refY="3" orient="auto">
                                    <polygon points="0 0, 8 3, 0 6" fill="#cbd5e1"/>
                                </marker>
                            </defs>
                            <g id="lines-container"></g>
                            <path id="temp-line" class="stroke-sky-400 stroke-2 fill-none stroke-dashed hidden" stroke-dasharray="4"/>
                        </svg>
                        <!-- Tasks container -->
                        <div id="gantt-tasks-container" class="relative z-10"></div>
                    </div>
                </div>

                <div id="view-list" class="view-container hidden h-full glass-panel rounded-[2rem] overflow-hidden flex flex-col">
                    <div class="bg-white/50 border-b px-6 py-3 grid grid-cols-12 gap-4 text-xs font-bold text-slate-500 uppercase">
                        <div class="col-span-4">Aufgabe</div><div class="col-span-2">Zeitplan</div><div class="col-span-3">Anfahrt</div><div class="col-span-1">Dauer</div><div class="col-span-2 text-right">Aktionen</div>
                    </div>
                    <div id="list-body" class="overflow-y-auto flex-1 p-2 space-y-1"></div>
                </div>
            </section>
        </div>

        <!-- VIEW 2: CALENDAR & UNPLANNED -->
        <div id="main-tab-calendar" class="main-tab hidden h-full flex w-full gap-6">
            <div class="w-1/4 glass-panel rounded-3xl p-4 flex flex-col">
                <h3 class="font-bold border-b pb-2 mb-4 text-slate-700">Ungeplante Aufträge</h3>
                <div id="calendar-unplanned-list" class="space-y-3 overflow-y-auto flex-1"></div>
            </div>
            <div class="flex-1 bg-white rounded-3xl shadow-sm border p-6 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-slate-800">Oktober 2026</h2>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 rounded border hover:bg-slate-50"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="px-3 py-1 rounded border hover:bg-slate-50">Heute</button>
                        <button class="px-3 py-1 rounded border hover:bg-slate-50"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden flex-1" id="calendar-grid">
                    <!-- Calendar Grid Generated via JS -->
                </div>
            </div>
        </div>

        <!-- VIEW 3: DELEGATED -->
        <div id="main-tab-delegated" class="main-tab hidden h-full w-full">
            <div class="bg-white rounded-2xl shadow-sm border h-full p-6 flex flex-col">
                <h2 class="text-xl font-bold text-slate-800 mb-4">Externe Partneraufträge</h2>
                <div class="flex-1 overflow-y-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <tr><th class="p-3">Aufgabe</th><th class="p-3">Partner</th><th class="p-3">Status</th><th class="p-3">Fällig</th><th class="p-3 text-right">Aktionen</th></tr>
                        </thead>
                        <tbody id="delegated-list" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- VIEW 4: HISTORY & ACTIVITY -->
        <div id="main-tab-history" class="main-tab hidden h-full w-full flex gap-6">
            <div class="w-1/3 glass-panel rounded-3xl p-6 flex flex-col">
                <h3 class="font-bold mb-4 text-slate-700">Aktivitätenprotokoll</h3>
                <div id="global-activity-log" class="space-y-4 overflow-y-auto flex-1 pr-2"></div>
            </div>
            <div class="w-2/3 glass-panel rounded-3xl p-6 flex flex-col">
                <h3 class="font-bold mb-4 text-slate-700">Abgeschlossene Aufträge</h3>
                <div id="completed-jobs-list" class="space-y-2 overflow-y-auto flex-1"></div>
            </div>
        </div>
    </main>

    <!-- PLAN WIZARD SIDEBAR -->
    <div id="plan-wizard-modal" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closePlanWizard()"></div>
        <div id="plan-wizard-content" class="absolute inset-y-0 right-0 w-full max-w-4xl bg-white shadow-2xl transform transition-transform duration-300 translate-x-full flex flex-col">
             <!-- Header -->
             <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Plan erstellen</h2>
                    <p class="text-sm text-slate-500">Aufgaben für <span id="wizard-customer-name" class="font-bold text-brandDark">Kunde</span></p>
                </div>
                <button onclick="closePlanWizard()" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <!-- Body -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Sidebar Choices -->
                <div class="w-1/3 bg-slate-50 p-6 border-r border-slate-100 flex flex-col gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="plan-type" value="project" class="peer hidden" checked onchange="toggleWizardType('project')">
                        <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mb-3"><i class="fa-solid fa-layer-group text-xl"></i></div>
                            <h3 class="font-bold text-slate-800">Projektphase</h3>
                            <p class="text-xs text-slate-500 mt-1">Checkliste aus Produkt laden.</p>
                        </div>
                    </label>

                    <!-- NEW: Tickets Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="plan-type" value="tickets" class="peer hidden" onchange="toggleWizardType('tickets')">
                        <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-3"><i class="fa-solid fa-ticket text-xl"></i></div>
                            <h3 class="font-bold text-slate-800">Service Ticket</h3>
                            <p class="text-xs text-slate-500 mt-1">Aus Störungsmeldungen.</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="plan-type" value="appointments" class="peer hidden" onchange="toggleWizardType('appointments')">
                        <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-3"><i class="fa-regular fa-calendar-check text-xl"></i></div>
                            <h3 class="font-bold text-slate-800">Offene Termine</h3>
                            <p class="text-xs text-slate-500 mt-1">Kalendereinträge ohne Aufgaben.</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="plan-type" value="custom" class="peer hidden" onchange="toggleWizardType('custom')">
                        <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mb-3"><i class="fa-solid fa-pen-to-square text-xl"></i></div>
                            <h3 class="font-bold text-slate-800">Manuelle Aufgabe</h3>
                            <p class="text-xs text-slate-500 mt-1">Einzelne Aufgabe erstellen.</p>
                        </div>
                    </label>
                </div>

                <!-- Main Content Area -->
                <div class="w-2/3 p-8 overflow-y-auto" id="wizard-content-area">
                    
                    <!-- Plan Title Input Section -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Plan Titel / Referenz</label>
                        <input type="text" id="wizard-plan-title-input" class="w-full text-xl font-bold border-b-2 border-slate-200 py-2 outline-none focus:border-brandDark bg-transparent transition-colors placeholder-slate-300 text-slate-800" placeholder="Bezeichnung eingeben (z.B. Montage Bauabschnitt A)">
                    </div>

                    <!-- Project Task Form -->
                    <div id="wizard-form-project" class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800">Phase: Montage</h3>
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">Bereits geplant</h4>
                            <div id="wizard-planned-list" class="space-y-2 opacity-70"></div>
                        </div>
                        <div class="space-y-2 pt-4 border-t border-slate-100">
                            <h4 class="text-xs font-bold text-brandDark uppercase tracking-wide">Verbleibend / Offen</h4>
                            <div id="wizard-remaining-list" class="space-y-2"></div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4 mt-6">
                            <h4 class="font-bold text-slate-800 text-sm">Massenzuweisung</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-xs font-bold text-slate-500 mb-1">Projektleiter</label><select id="wizard-pm-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm"><option value="">Wählen...</option></select></div>
                                <div><label class="block text-xs font-bold text-slate-500 mb-1">Datum</label><input type="datetime-local" id="wizard-date" class="w-full p-2 rounded-lg border border-slate-300 text-sm"></div>
                            </div>
                            <div><label class="block text-xs font-bold text-slate-500 mb-1">Team</label><div class="flex flex-wrap gap-2" id="wizard-crew-select"></div></div>
                            <div><label class="block text-xs font-bold text-slate-500 mb-1">Assets</label><div class="bg-white border border-slate-300 rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-project-assets"></div></div>
                        </div>
                    </div>

                    <!-- Tickets Form -->
                    <div id="wizard-form-tickets" class="hidden space-y-6">
                        <h3 class="text-lg font-bold text-slate-800">Offene Tickets wählen</h3>
                        <div class="space-y-3" id="wizard-tickets-list"></div>
                        <div id="wizard-ticket-resolution" class="hidden mt-6 pt-6 border-t border-slate-200">
                             <h4 class="font-bold text-slate-800 mb-3">Zuweisung & Details</h4>
                             <div class="grid grid-cols-2 gap-4">
                                 <div><label class="block text-xs font-bold text-slate-500 mb-1">Priorität</label><input type="text" id="ticket-prio-display" class="w-full p-2 bg-slate-100 rounded-lg text-sm" disabled></div>
                                 <div><label class="block text-xs font-bold text-slate-500 mb-1">Kategorie</label><select id="ticket-category-select" class="w-full p-2 rounded-lg border text-sm"><option value="Service">Service</option><option value="Electric">Electric</option></select></div>
                             </div>
                             <div class="mt-4"><label class="block text-xs font-bold text-slate-500 mb-1">Benötigte Assets</label><div class="bg-white border rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-ticket-assets"></div></div>
                        </div>
                    </div>

                    <!-- Unplanned Form -->
                    <div id="wizard-form-appointments" class="hidden space-y-6">
                         <h3 class="text-lg font-bold text-slate-800">Offene Termine zuweisen</h3>
                         <div class="space-y-3" id="wizard-appointments-list"></div>
                         <div id="wizard-appointment-resolution" class="hidden mt-6 pt-6 border-t border-slate-200">
                             <h4 class="font-bold text-slate-800 mb-3">Aufgabe definieren</h4>
                             <div class="grid grid-cols-2 gap-4 mb-4">
                                 <label class="cursor-pointer"><input type="radio" name="appt-resolve-type" value="link" class="peer hidden" checked onchange="toggleApptResolveType('link')"><div class="p-3 rounded-lg border bg-white peer-checked:border-brandDark peer-checked:bg-blue-50 text-center text-sm font-bold">Verknüpfen</div></label>
                                 <label class="cursor-pointer"><input type="radio" name="appt-resolve-type" value="manual" class="peer hidden" onchange="toggleApptResolveType('manual')"><div class="p-3 rounded-lg border bg-white peer-checked:border-brandDark peer-checked:bg-blue-50 text-center text-sm font-bold">Manuell</div></label>
                             </div>
                             <div id="appt-resolve-link"><select id="appt-checklist-select" class="w-full p-2 rounded-lg border text-sm"></select></div>
                             <div id="appt-resolve-manual" class="hidden space-y-3">
                                 <input type="text" id="appt-manual-title" class="w-full p-2 rounded-lg border text-sm" placeholder="Titel">
                                 <select id="appt-manual-category" class="w-full p-2 rounded-lg border text-sm"><option>General</option><option>Electric</option></select>
                             </div>
                             <div class="mt-4"><label class="block text-xs font-bold text-slate-500 mb-1">Assets</label><div class="bg-white border rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-appt-assets"></div></div>
                         </div>
                    </div>

                    <!-- Custom Task Form -->
                    <div id="wizard-form-custom" class="hidden space-y-6">
                        <h3 class="text-lg font-bold text-slate-800">Manuelle Aufgabe</h3>
                        <div class="space-y-4">
                            <input type="text" id="wizard-custom-title" class="w-full p-3 rounded-xl border outline-none" placeholder="Titel">
                            <textarea id="wizard-custom-desc" rows="3" class="w-full p-3 rounded-xl border outline-none" placeholder="Details..."></textarea>
                            <!-- Steps -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Checklisten-Schritte</label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" id="wizard-custom-step-input" class="flex-1 p-2.5 rounded-xl border text-sm outline-none" placeholder="Schritt hinzufügen...">
                                    <button onclick="addWizardStep()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 rounded-xl font-bold"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                <div id="wizard-custom-steps-list" class="space-y-2 max-h-32 overflow-y-auto"><div class="text-xs text-slate-400 italic p-2">Keine Schritte.</div></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="number" id="wizard-custom-duration" class="w-full p-3 rounded-xl border" value="1">
                                <select id="wizard-custom-category" class="w-full p-3 rounded-xl border"><option>General</option><option>Electric</option><option>Roof</option></select>
                            </div>
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Assets</label><div class="bg-white border rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-custom-assets"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button onclick="closePlanWizard()" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition-colors">Abbrechen</button>
                <button onclick="savePlanWizard()" class="px-8 py-3 rounded-xl font-bold bg-brandDark text-white shadow-lg hover:bg-blue-800 transition-transform active:scale-95">Speichern</button>
            </div>
        </div>
    </div>

    <!-- TASK INSPECTOR MODAL -->
    <div id="task-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform duration-300 flex flex-col translate-x-full" id="task-modal-content">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <div class="flex justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Details</span>
                    <button onclick="closeModal()"><i class="fa-solid fa-times text-slate-400 hover:text-slate-600"></i></button>
                </div>
                <input type="text" id="modal-edit-title" class="text-2xl font-bold w-full bg-transparent border-none outline-none mb-2" value="Titel">
                
                <!-- Category Select -->
                <div class="flex items-center gap-4 text-sm text-slate-500 mt-1">
                     <select id="modal-edit-category" class="bg-transparent font-bold text-xs uppercase tracking-wider text-slate-400 outline-none hover:text-brandDark transition-colors">
                        <option value="General">General</option>
                        <option value="Electric">Electric</option>
                        <option value="Roof">Roof</option>
                        <option value="Prep">Prep</option>
                        <option value="Manual">Manual</option>
                        <option value="Service">Service</option>
                    </select>
                    <div class="w-px h-3 bg-slate-300"></div>
                    <div class="flex items-center gap-2"><i class="fa-regular fa-clock"></i> <input type="number" id="modal-edit-duration" class="w-8 bg-transparent text-center font-bold outline-none" value="2">h</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-users"></i> <span id="modal-assignee-text">--</span></div>
                </div>

            </div>
            <div class="flex border-b px-2 overflow-x-auto">
                <button onclick="switchModalTab('info')" id="tab-btn-info" class="tab-btn active px-4 py-3 text-sm font-bold border-b-2 border-brandDark text-brandDark whitespace-nowrap">Info & Team</button>
                <button onclick="switchModalTab('checklist')" id="tab-btn-checklist" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">Checkliste</button>
                <button onclick="switchModalTab('report')" id="tab-btn-report" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">Bericht</button>
                <button onclick="switchModalTab('history')" id="tab-btn-history" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">Verlauf</button>
            </div>
            <div class="p-6 flex-1 overflow-y-auto bg-white space-y-6">
                
                <!-- TAB 1: INFO & TEAM -->
                <div id="tab-info" class="tab-content active space-y-6">
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div><span class="text-[10px] text-slate-400 font-bold uppercase">Zeitplan</span><div class="text-sm font-bold text-slate-800" id="modal-schedule">--</div></div>
                        <div><span class="text-[10px] text-slate-400 font-bold uppercase">Anfahrt</span><div class="text-sm font-bold text-slate-800" id="modal-travel">--</div></div>
                    </div>
                    <div><h3 class="text-sm font-bold mb-2">Beschreibung</h3><textarea id="modal-edit-description" class="w-full text-sm p-3 bg-slate-50 rounded-lg border border-slate-200 outline-none resize-none" rows="3"></textarea></div>
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <div class="flex justify-between items-center mb-2"><h3 class="text-sm font-bold text-blue-900">Team verwalten</h3><button onclick="toggleTaskCrewEditor()" class="text-xs font-bold text-blue-600"><i class="fa-solid fa-pen"></i> Editieren</button></div>
                        <div id="modal-task-assignees" class="flex flex-wrap gap-2"></div>
                        <div id="modal-task-crew-select" class="hidden mt-3 pt-3 border-t border-blue-200"><p class="text-xs text-blue-500 mb-2">Techniker hinzufügen/entfernen:</p><div id="modal-task-crew-checkboxes" class="grid grid-cols-2 gap-2"></div></div>
                    </div>
                    <div id="modal-assigned-assets-container"><h3 class="text-sm font-bold mb-2">Assets</h3><div id="modal-assigned-assets" class="grid grid-cols-2 gap-2"></div></div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Abhängigkeiten</h3>
                        <div class="space-y-2"><div id="modal-dependency-list" class="space-y-2"></div><div class="flex gap-2 mt-2"><select id="modal-dependency-select" class="flex-1 text-sm border rounded-lg p-2"></select><button onclick="addDependency()" class="bg-slate-100 px-3 py-2 rounded-lg text-sm"><i class="fa-solid fa-plus"></i></button></div></div>
                    </div>
                </div>

                <!-- TAB 2: CHECKLIST -->
                <div id="tab-checklist" class="tab-content space-y-4">
                     <div class="flex justify-between items-center mb-2"><h3 class="text-sm font-bold text-slate-900">Aufgabenliste</h3><button onclick="addTaskSubtask()" class="text-xs text-blue-600 font-bold">+ Schritt hinzufügen</button></div>
                     <div id="modal-subtasks-list" class="space-y-2"></div>
                </div>

                <!-- TAB 3: REPORT -->
                <div id="tab-report" class="tab-content space-y-6">
                    <div><h3 class="text-sm font-bold mb-3">Erledigte Schritte</h3><div id="modal-checklist-report" class="space-y-3"></div></div>
                    <div><h3 class="text-sm font-bold mb-3">Ausgaben & Extras</h3><div class="space-y-2" id="modal-financials"></div></div>
                    <div><h3 class="text-sm font-bold mb-3">Dokumente</h3><div id="modal-files-list" class="space-y-2"></div></div>
                </div>

                <!-- TAB 4: HISTORY -->
                <div id="tab-history" class="tab-content space-y-4">
                    <h3 class="text-sm font-bold mb-3">Aktivitätenprotokoll</h3>
                    <div id="modal-activity-log" class="space-y-4 pl-4 border-l-2 border-slate-100"></div>
                </div>

            </div>
            <div class="p-4 border-t bg-slate-50 flex justify-between">
                <button onclick="deleteActiveTask()" class="text-red-500 font-bold text-sm flex items-center gap-2"><i class="fa-solid fa-trash"></i> Löschen</button>
                <div class="flex gap-2">
                    <button onclick="closeModal()" class="text-slate-500 font-bold text-sm px-4">Abbrechen</button>
                    <button onclick="saveActiveTask()" class="bg-brandDark text-white px-6 py-2 rounded-lg font-bold text-sm shadow-lg">Speichern</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Crew Modal -->
    <div id="crew-modal" class="fixed inset-0 z-[110] hidden"><div class="absolute inset-0 bg-slate-900/40" onclick="closeCrewModal()"></div><div class="absolute inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl p-6 flex flex-col" id="crew-modal-content"><div class="flex justify-between border-b pb-4 mb-4"><h2 class="text-xl font-bold">Team wählen</h2><button onclick="closeCrewModal()"><i class="fa-solid fa-xmark"></i></button></div><div id="crew-list-container" class="flex-1 overflow-y-auto"></div></div></div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-10 right-10 bg-brandDark text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-3 z-50">
        <i class="fa-solid fa-circle-check text-actionGreen text-xl"></i><div><h4 class="font-bold text-sm">Erfolgreich</h4><p class="text-xs text-slate-300">Änderungen gespeichert.</p></div>
    </div>

    <script>
        // --- DATA ---
        const START_HOUR = 8; 
        const PIXELS_PER_HOUR = 100;
        
        const allEmployees = [
            { id: 'emp_1', name: 'Sadid', role: 'Leitung', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sadid' },
            { id: 'emp_2', name: 'Nuri', role: 'Elektriker', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Nuri' },
            { id: 'emp_3', name: 'Rasuli', role: 'Azubi', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Rasuli' },
        ];
        
        const assetInventory = [
            { id: 'a1', name: 'Bohrhammer', location: 'Bus 1' }, { id: 'a2', name: 'Leiter', location: 'Bus 1' },
            { id: 'a3', name: 'Messgerät', location: 'Persönlich' }
        ];

        const customerDatabase = [
            { id: 'c1', name: 'Schmidt Solartechnik', projects: [{id: 'p1', product: 'PV Anlage 10kWp', object: 'Lagerhalle A', address: 'Berlin'}] },
            { id: 'c2', name: 'Müller GmbH', projects: [{id: 'p2', product: 'Wallbox', object: 'Hauptbüro', address: 'München'}] }
        ];

        const checklistTemplate = [
            { title: 'Gerüstaufbau', duration: 2, category: 'Prep' }, { title: 'Dachhaken', duration: 4, category: 'Roof' },
            { title: 'Verkabelung', duration: 3, category: 'Electric' }
        ];

        const mockTickets = [
            { id: 101, ticket_no: 4002, error_type: 'Wechselrichter Fehler', error_code: 'E-504', priority: 'High', problem: 'Anlage produziert nicht. Fehlercode am Display.', date: '2023-10-23', status: 'Open' },
            { id: 102, ticket_no: 4005, error_type: 'Internetverbindung', error_code: 'NET-01', priority: 'Medium', problem: 'Keine Datenübertragung seit 3 Tagen.', date: '2023-10-24', status: 'Open' }
        ];

        let activityLog = [
            { time: "08:00", user: "Sadid", text: "Schicht gestartet", type: "checkin" },
            { time: "09:30", user: "Nuri", text: "Sicherheitscheck erledigt", type: "update" }
        ];
        
        let unplannedAppointments = [
            { id: 'appt1', title: 'Wartung WP', date: '25. Okt, 08:00', crew: ['emp_1'] },
            { id: 'appt2', title: 'Besichtigung', date: '26. Okt, 14:00', crew: ['emp_2'] }
        ];

        let currentTasks = []; 
        const mockTasksP1 = [
            { 
                id: 't1', title: 'Gerüstaufbau', duration: 2, category: 'Prep', status: 'in-progress', 
                assignees: ['emp_1', 'emp_3'], startHour: 8, predecessors: [], assets: ['a2', 'a3'],
                startDate: '24. Okt', dueDate: '25. Okt',
                travelTime: '45m', arrivalTime: '07:45', origin: 'Büro',
                description: 'Vollständiges Sicherheitsgerüst auf Süd- und Westseite aufbauen.',
                log: [{ time: '08:00', text: 'Ankunft am Standort.', type: 'info' }],
                expenses: [{ item: 'Parkticket', cost: '12.50' }],
                files: [{ name: 'Sicherheitsplan_v2.pdf', size: '2.4MB' }],
                subtasks: [
                    { text: 'LKW entladen', completed: true, completedBy: 'emp_1', time: '08:15', note: 'Zufahrt war eng.', photo: 'https://images.unsplash.com/photo-1535732820275-9ffd998cac22?auto=format&fit=crop&w=150&q=80' },
                    { text: 'Grundplatten sichern', completed: true, completedBy: 'emp_3', time: '09:30', note: 'Alle Platten fest.' },
                    { text: 'Sicherheitsnetz installieren', completed: false }
                ]
            },
            { id: 't2', title: 'Dachhaken setzen', duration: 4, category: 'Roof', status: 'scheduled', assignees: ['emp_3'], startHour: 10, predecessors: ['t1'], assets: [], subtasks: [] },
            { id: 't3', title: 'Schienenmontage', duration: 3, category: 'Roof', status: 'open', assignees: [], startHour: 0, predecessors: [], assets: [], subtasks: [] }
        ];

        let activeTaskId = null;
        let wizardType = 'project'; 
        let activeAppointmentId = null;
        let activeTicketId = null;
        let visibleEmployeeIds = ['emp_1', 'emp_2', 'emp_3'];
        let tempWizardSteps = [];
        
        // --- DRAG CONNECTION STATE ---
        let dragSourceId = null;
        let isDraggingConnection = false;
        
        // --- TASK DRAG STATE ---
        let isDraggingTask = false;
        let dragTaskStartX = 0;
        let dragTaskInitialLeft = 0;
        let draggedTaskId = null;
        let didDragTask = false; // Flag to prevent click event after drag

        // --- INIT & CORE ---
        function initData() {
            populateCustomerDropdown();
            renderActiveCrewWidget();
            switchMainTab('planning');
            initSortables();
        }

        // --- HELPERS ---
        function showToast(msg) { 
            const t = document.getElementById('toast'); 
            t.querySelector('div h4').innerText = msg; 
            t.classList.remove('translate-y-20', 'opacity-0'); 
            setTimeout(() => t.classList.add('translate-y-20', 'opacity-0'), 3000); 
        }

        function switchMainTab(tabId) {
            document.querySelectorAll('.main-tab').forEach(el => el.classList.remove('active', 'hidden'));
            document.querySelectorAll('.main-tab').forEach(el => el.classList.add('hidden'));
            document.getElementById(`main-tab-${tabId}`).classList.remove('hidden');
            document.getElementById(`main-tab-${tabId}`).classList.add('active');
            
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active', 'bg-slate-100', 'text-brandDark'));
            document.getElementById(`nav-${tabId}`).classList.add('active', 'bg-slate-100', 'text-brandDark');

            if(tabId === 'calendar') renderCalendarUnplanned();
            if(tabId === 'delegated') renderDelegatedTasks();
            if(tabId === 'history') renderHistoryLog();
        }

        function populateCustomerDropdown() {
            const list = document.getElementById('customer-dropdown-list');
            list.innerHTML = customerDatabase.map(c => `
                <div class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm font-bold border-b border-slate-50" 
                     onclick="selectCustomer('${c.id}', '${c.name}')" data-name="${c.name.toLowerCase()}">
                    ${c.name}
                </div>
            `).join('');
        }
        function showCustomerDropdown() { document.getElementById('customer-dropdown-list').classList.remove('hidden'); }
        function hideCustomerDropdown() { setTimeout(() => document.getElementById('customer-dropdown-list').classList.add('hidden'), 200); }
        function filterCustomerDropdown() {
            const val = document.getElementById('customer-search-input').value.toLowerCase();
            const items = document.getElementById('customer-dropdown-list').children;
            Array.from(items).forEach(i => i.classList.toggle('hidden', !i.dataset.name.includes(val)));
        }
        function selectCustomer(id, name) {
            document.getElementById('customer-search-input').value = name;
            const pSelect = document.getElementById('project-selector');
            pSelect.disabled = false;
            pSelect.innerHTML = '<option>Projekt wählen...</option>' + 
                customerDatabase.find(c => c.id === id).projects.map(p => `<option value="${p.id}">${p.product} - ${p.address}</option>`).join('');
            pSelect.selectedIndex = 1; changeProject('p1');
        }

        function changeProject(pid) {
            if(pid === 'p1') { currentTasks = JSON.parse(JSON.stringify(mockTasksP1)); } else { currentTasks = []; }
            renderPlanningViews();
        }

        function renderActiveCrewWidget() { 
            document.getElementById('active-crew-avatars').innerHTML = visibleEmployeeIds.map(id => {
                const emp = allEmployees.find(e=>e.id==id);
                return emp ? `<img src="${emp.avatar}" class="w-8 h-8 rounded-full border-2 border-white">` : '';
            }).join(''); 
        }

        function renderPlanningViews() {
            const cl = document.getElementById('checklist-source');
            cl.innerHTML = '';
            currentTasks.filter(t => t.status === 'open').forEach(t => cl.appendChild(renderTaskCard(t, false)));

            const board = document.getElementById('view-board');
            board.innerHTML = '';
            visibleEmployeeIds.forEach(eid => {
                const emp = allEmployees.find(e => e.id === eid);
                if(!emp) return;
                const col = document.createElement('div');
                col.className = 'glass-panel rounded-[2rem] flex flex-col h-full overflow-hidden';
                col.innerHTML = `<div class="p-4 bg-white/40 border-b border-white/20 font-bold flex gap-2 items-center"><img src="${emp.avatar}" class="w-6 h-6 rounded-full">${emp.name}</div><div id="col-${eid}" class="p-3 flex-1 overflow-y-auto space-y-3 bg-slate-50/30 min-h-[100px]" data-emp="${eid}"></div>`;
                board.appendChild(col);
                Sortable.create(col.querySelector(`#col-${eid}`), {
                     group: 'shared', animation: 150, ghostClass: 'sortable-ghost',
                     onAdd: function(evt) { const tid = evt.item.dataset.id; updateTaskStatus(tid, 'scheduled', [eid]); }
                });
            });
            currentTasks.filter(t => t.status !== 'open').forEach(t => {
                if(t.assignees && t.assignees.length > 0 && document.getElementById(`col-${t.assignees[0]}`)) {
                    document.getElementById(`col-${t.assignees[0]}`).appendChild(renderTaskCard(t, true));
                }
            });

            const listBody = document.getElementById('list-body');
            listBody.innerHTML = currentTasks.map(t => `
                <div class="grid grid-cols-12 gap-4 p-3 border-b hover:bg-slate-50 cursor-pointer items-center text-sm" onclick="openModal('${t.id}')">
                    <div class="col-span-4 font-bold text-slate-700 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full ${t.category==='Electric'?'bg-brandDark':'bg-sky-400'}"></div> ${t.title}
                    </div>
                    <div class="col-span-2 text-xs text-slate-500">${t.startDate || '-'}</div>
                    <div class="col-span-3 text-xs text-slate-500">${t.travelTime ? t.travelTime + ' Ankunft: ' + t.arrivalTime : '-'}</div>
                    <div class="col-span-1 text-xs font-mono">${t.duration}h</div>
                    <div class="col-span-2 flex justify-end gap-2">${renderActionButtons(t)}</div>
                </div>
            `).join('');
            
            document.getElementById('task-count').innerText = `${currentTasks.filter(t => t.status === 'open').length}`;
            renderActiveCrewWidget();
        }

        function renderTaskCard(t, isBoard) {
            const div = document.createElement('div');
            div.dataset.id = t.id; div.onclick = () => openModal(t.id);
            const statusBorder = t.status === 'in-progress' ? 'border-green-500' : (t.status === 'paused' ? 'border-orange-400' : 'border-brandDark');
            div.className = isBoard ? `bg-white shadow-md p-3 rounded-xl border-l-4 ${statusBorder} cursor-grab relative group` : 'bg-white p-3 rounded-xl border border-slate-200 cursor-grab';
            
            let content = `<div class="font-bold text-sm text-slate-700">${t.title}</div><div class="text-xs text-slate-500 mt-1 flex items-center gap-1">${t.category} • ${t.duration}h</div>`;
            if(isBoard) {
                if(t.travelTime) content += `<div class="mt-2 bg-slate-50 p-1.5 rounded text-[10px] text-slate-500 flex justify-between"><span><i class="fa-solid fa-car"></i> ${t.travelTime}</span><span>${t.arrivalTime}</span></div>`;
                content += `<div class="mt-2 flex justify-between items-center border-t pt-2">${renderAvatarStack(t.assignees)} <div class="flex gap-1">${renderActionButtons(t)}</div></div>`;
            }
            div.innerHTML = content;
            return div;
        }

        function renderActionButtons(t) {
            if(t.status === 'open') return '';
            if(t.status === 'scheduled' || t.status === 'paused') return `<button onclick="toggleStatus('${t.id}','in-progress', event)" class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center hover:bg-green-200"><i class="fa-solid fa-play text-[10px]"></i></button>`;
            if(t.status === 'in-progress') return `<button onclick="toggleStatus('${t.id}','paused', event)" class="w-6 h-6 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center hover:bg-orange-200"><i class="fa-solid fa-pause text-[10px]"></i></button> <button onclick="toggleStatus('${t.id}','completed', event)" class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200"><i class="fa-solid fa-check text-[10px]"></i></button>`;
            return '';
        }

        function renderAvatarStack(ids) {
            return `<div class="flex -space-x-1">${(ids||[]).map(id => `<img src="${allEmployees.find(e=>e.id==id)?.avatar}" class="w-5 h-5 rounded-full border border-white">`).join('')}</div>`;
        }

        function toggleStatus(id, status, e) {
            e.stopPropagation();
            const t = currentTasks.find(x => x.id == id);
            if(status === 'paused') {
                const r = prompt("Grund für Pause:");
                if(r) { t.status = status; t.pauseReason = r; }
            } else {
                t.status = status;
                if(status === 'completed') {
                    showToast("Aufgabe erledigt!");
                }
            }
            renderPlanningViews();
        }

        function updateTaskStatus(id, status, assignees) {
            const t = currentTasks.find(x => x.id == id);
            if(t) { t.status = status; if(assignees) t.assignees = assignees; renderPlanningViews(); }
        }

        function switchView(viewName) {
            ['board', 'gantt', 'list'].forEach(v => {
                document.getElementById(`view-${v}`).classList.add('hidden');
                document.getElementById(`btn-view-${v}`).classList.replace('bg-slate-100', 'hover:text-brandDark');
                document.getElementById(`btn-view-${v}`).classList.replace('font-bold', 'font-medium');
            });
            document.getElementById(`view-${viewName}`).classList.remove('hidden');
            const btn = document.getElementById(`btn-view-${viewName}`);
            btn.classList.replace('hover:text-brandDark', 'bg-slate-100');
            btn.classList.replace('font-medium', 'font-bold');
            
            if(viewName === 'gantt') renderGantt();
        }

        // --- GANTT & CONNECTIONS ---

        function renderGantt() {
            const container = document.getElementById('gantt-tasks-container');
            const timeHeader = document.getElementById('time-scale');
            const svgLayer = document.getElementById('gantt-lines');
            if(!container) return;
            container.innerHTML = ''; 
            timeHeader.innerHTML = '';
            
            // Render Grid & Time Scale
            for(let i=START_HOUR; i<=18; i++) {
                const m = document.createElement('div');
                m.className = 'absolute top-0 bottom-0 border-l border-slate-200 pl-1 text-[10px] h-full flex items-center';
                m.style.left = `${(i-START_HOUR)*PIXELS_PER_HOUR}px`; m.innerText = `${i}:00`;
                timeHeader.appendChild(m);
            }
            
            // Render Tasks
            let currentTop = 24; // Initial margin
            const ROW_HEIGHT = 80;

            visibleEmployeeIds.forEach(eid => {
                const emp = allEmployees.find(e=>e.id==eid);
                // Render Row Header
                const row = document.createElement('div');
                row.className = 'flex border-b h-20 w-full absolute left-0';
                row.style.top = `${currentTop}px`;
                row.style.pointerEvents = 'none'; // Click through to canvas
                
                // Add label to gantt-body directly to avoid nesting complexities with absolute bars
                const label = document.createElement('div');
                label.className = 'w-48 border-r p-2 flex items-center gap-2 bg-white/80 sticky left-0 z-20 backdrop-blur-sm absolute';
                label.style.top = `${currentTop}px`;
                label.style.height = '80px';
                label.innerHTML = `<img src="${emp.avatar}" class="w-8 h-8 rounded-full">${emp.name}`;
                container.appendChild(label);
                
                // Render Bars
                currentTasks.filter(t => t.assignees.includes(eid) && t.status!=='open').forEach(t => {
                    const bar = document.createElement('div');
                    bar.className = 'gantt-bar bg-sky-200 border-sky-300 text-sky-900 absolute flex items-center justify-center select-none';
                    bar.style.top = `${currentTop + 20}px`;
                    bar.style.left = `${(t.startHour-START_HOUR)*PIXELS_PER_HOUR}px`;
                    bar.style.width = `${t.duration*PIXELS_PER_HOUR}px`;
                    bar.innerHTML = `
                        <div class="gantt-handle left" onmousedown="endConnection(event, '${t.id}')">-</div>
                        <span class="truncate px-1 pointer-events-none">${t.title}</span>
                        <div class="gantt-handle right" onmousedown="startConnection(event, '${t.id}')">+</div>
                    `;
                    bar.setAttribute('id', `gantt-task-${t.id}`);
                    
                    // Attach Task Drag Handler
                    bar.onmousedown = (e) => startTaskDrag(e, t.id);
                    bar.onclick = (e) => {
                         if(!e.target.classList.contains('gantt-handle') && !didDragTask) {
                             openModal(t.id);
                         }
                    };

                    container.appendChild(bar);
                });
                currentTop += ROW_HEIGHT;
            });
            
            // Adjust Container Height
            container.style.height = `${currentTop + 100}px`;
            
            setTimeout(drawGanttLines, 50);
        }
        
        // --- TASK DRAGGING IMPLEMENTATION ---
        function startTaskDrag(e, taskId) {
            // Prevent if clicking on handles
            if(e.target.classList.contains('gantt-handle')) return;
            
            e.stopPropagation(); // Stop connection drag
            isDraggingTask = true;
            draggedTaskId = taskId;
            dragTaskStartX = e.clientX;
            didDragTask = false;
            
            const bar = document.getElementById(`gantt-task-${taskId}`);
            // Use inline style left or calculate from offset if needed, but we set inline in render
            dragTaskInitialLeft = parseFloat(bar.style.left || 0);
            
            document.addEventListener('mousemove', onDragTask);
            document.addEventListener('mouseup', endTaskDrag);
        }

        function onDragTask(e) {
            if(!isDraggingTask) return;
            
            const dx = e.clientX - dragTaskStartX;
            
            // Determine if user has "dragged" enough to count as a drag vs a click
            if(Math.abs(dx) > 5) didDragTask = true;
            
            const bar = document.getElementById(`gantt-task-${draggedTaskId}`);
            let newLeft = dragTaskInitialLeft + dx;
            
            // Basic bounds checking (optional, keep > 0)
            if(newLeft < 0) newLeft = 0;
            
            bar.style.left = `${newLeft}px`;
            
            // Optional: Live update dependency lines? expensive but cool
            // For now, let's update lines on endDrag to save perf
        }

        function endTaskDrag(e) {
            if(!isDraggingTask) return;
            
            isDraggingTask = false;
            document.removeEventListener('mousemove', onDragTask);
            document.removeEventListener('mouseup', endTaskDrag);
            
            if(didDragTask) {
                const bar = document.getElementById(`gantt-task-${draggedTaskId}`);
                const finalLeft = parseFloat(bar.style.left);
                
                // Snap to nearest hour (grid)
                // Calculate new start hour: pixels / PIXELS_PER_HOUR + START_HOUR
                let newStartHour = Math.round(finalLeft / PIXELS_PER_HOUR) + START_HOUR;
                
                // Bounds Check for Logic (e.g. 8am to 6pm)
                if(newStartHour < START_HOUR) newStartHour = START_HOUR;
                if(newStartHour > 18) newStartHour = 18;
                
                // Update Model
                const t = currentTasks.find(x => x.id === draggedTaskId);
                if(t) {
                    t.startHour = newStartHour;
                    showToast(`Verschoben auf ${newStartHour}:00 Uhr`);
                }
                
                // Re-render to snap visually and update lines
                renderGantt();
            }
            draggedTaskId = null;
        }

        // INTERACTIVE DRAWING (Connections)
        function startConnection(e, taskId) {
            e.stopPropagation();
            e.preventDefault();
            dragSourceId = taskId;
            isDraggingConnection = true;
            
            const tempLine = document.getElementById('temp-line');
            tempLine.classList.remove('hidden');
            
            // Initial coordinate
            const rect = e.target.getBoundingClientRect();
            const containerRect = document.getElementById('gantt-body').getBoundingClientRect();
            const scrollLeft = document.getElementById('gantt-body').scrollLeft;
            const scrollTop = document.getElementById('gantt-body').scrollTop;
            
            const startX = rect.left + rect.width/2 - containerRect.left + scrollLeft;
            const startY = rect.top + rect.height/2 - containerRect.top + scrollTop;
            
            tempLine.setAttribute('d', `M ${startX} ${startY} L ${startX} ${startY}`);
            
            // Attach temporary listeners
            document.addEventListener('mousemove', onDragConnection);
            document.addEventListener('mouseup', stopDragConnection);
        }

        function onDragConnection(e) {
            if(!isDraggingConnection) return;
            const container = document.getElementById('gantt-body');
            const rect = container.getBoundingClientRect();
            
            // Calculate Mouse Pos Relative to Container (Canvas)
            const endX = e.clientX - rect.left + container.scrollLeft;
            const endY = e.clientY - rect.top + container.scrollTop;
            
            const pathData = document.getElementById('temp-line').getAttribute('d');
            const startPart = pathData.split(' L ')[0];
            
            // Simple Curve
            const startCoords = startPart.replace('M ', '').split(' ');
            const startX = parseFloat(startCoords[0]);
            const startY = parseFloat(startCoords[1]);
            
            const cp1X = startX + 50; 
            const cp2X = endX - 50;
            
            // Update Temp Line with Curve
            document.getElementById('temp-line').setAttribute('d', `M ${startX} ${startY} C ${cp1X} ${startY}, ${cp2X} ${endY}, ${endX} ${endY}`);
        }

        function endConnection(e, targetId) {
            e.stopPropagation(); // Prevent modal opening
            if(isDraggingConnection && dragSourceId && dragSourceId !== targetId) {
                 // Add Dependency
                 const task = currentTasks.find(t => t.id === targetId);
                 if(task && !task.predecessors.includes(dragSourceId)) {
                     task.predecessors.push(dragSourceId);
                     showToast("Abhängigkeit erstellt");
                     drawGanttLines();
                 }
            }
        }
        
        function stopDragConnection() {
            isDraggingConnection = false;
            dragSourceId = null;
            document.getElementById('temp-line').classList.add('hidden');
            document.removeEventListener('mousemove', onDragConnection);
            document.removeEventListener('mouseup', stopDragConnection);
        }

        function drawGanttLines() {
            const container = document.getElementById('gantt-body');
            const linesContainer = document.getElementById('lines-container');
            if(!container || !linesContainer) return;
            
            linesContainer.innerHTML = '';
            
            const containerRect = container.getBoundingClientRect();
            const scrollLeft = container.scrollLeft;
            const scrollTop = container.scrollTop;
            
            currentTasks.forEach(targetTask => {
                if(targetTask.predecessors && targetTask.predecessors.length > 0) {
                    const targetEl = document.getElementById(`gantt-task-${targetTask.id}`);
                    if(!targetEl) return;
                    
                    const targetRect = targetEl.getBoundingClientRect();
                    // Target Handle is on the LEFT
                    const targetX = targetRect.left - containerRect.left + scrollLeft; 
                    const targetY = targetRect.top + targetRect.height/2 - containerRect.top + scrollTop;
                    
                    targetTask.predecessors.forEach(sourceId => {
                        const sourceEl = document.getElementById(`gantt-task-${sourceId}`);
                        if(!sourceEl) return;
                        
                        const sourceRect = sourceEl.getBoundingClientRect();
                        // Source Handle is on the RIGHT
                        const sourceX = sourceRect.right - containerRect.left + scrollLeft;
                        const sourceY = sourceRect.top + sourceRect.height/2 - containerRect.top + scrollTop;
                        
                        // Draw Curve
                        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                        const cpOffset = Math.abs(targetX - sourceX) / 2;
                        
                        path.setAttribute('d', `M ${sourceX} ${sourceY} C ${sourceX + 50} ${sourceY}, ${targetX - 50} ${targetY}, ${targetX} ${targetY}`);
                        path.setAttribute('class', 'dependency-line');
                        path.setAttribute('marker-end', 'url(#arrowhead)');
                        
                        // Click to Delete
                        path.onclick = (e) => {
                            e.stopPropagation();
                            if(confirm(`Abhängigkeit entfernen zwischen "${currentTasks.find(x=>x.id==sourceId).title}" und "${targetTask.title}"?`)) {
                                targetTask.predecessors = targetTask.predecessors.filter(pid => pid !== sourceId);
                                drawGanttLines();
                            }
                        };
                        
                        linesContainer.appendChild(path);
                    });
                }
            });
        }

        // --- CALENDAR TAB ---
        function renderCalendarUnplanned() {
            const list = document.getElementById('calendar-unplanned-list');
            list.innerHTML = unplannedAppointments.map(a => `
                <div class="p-3 bg-orange-50 border border-orange-100 rounded-lg cursor-pointer hover:shadow-md">
                    <div class="font-bold text-sm text-slate-800">${a.title}</div>
                    <div class="text-xs text-orange-600 mt-1 flex justify-between"><span>${a.date}</span><span>${a.crew.join(', ')}</span></div>
                </div>
            `).join('') || '<div class="text-center italic text-slate-400">Keine ungeplanten Jobs</div>';
            
            const grid = document.getElementById('calendar-grid');
            grid.innerHTML = ['Mo','Di','Mi','Do','Fr','Sa','So'].map(d => `<div class="bg-slate-50 p-2 text-center text-xs font-bold text-slate-500">${d}</div>`).join('') + 
                             Array(31).fill(0).map((_, i) => `<div class="bg-white p-2 min-h-[80px] border-t border-r text-xs text-slate-300 font-bold">${i+1}</div>`).join('');
        }

        function renderDelegatedTasks() {
            const list = document.getElementById('delegated-list');
            list.innerHTML = `<tr><td class="p-3">Spezial-Verkabelung</td><td class="p-3">ElektroFix GmbH</td><td class="p-3"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Wartend</span></td><td class="p-3">28. Okt</td><td class="p-3 text-right"><button class="text-blue-500 font-bold">Details</button></td></tr>`;
        }

        function renderHistoryLog() {
            document.getElementById('global-activity-log').innerHTML = activityLog.map(l => `
                <div class="flex gap-3 text-xs mb-3">
                    <span class="font-bold text-brandDark min-w-[50px]">${l.time}</span>
                    <span class="text-slate-600"><strong>${l.user}</strong>: ${l.text}</span>
                </div>
            `).join('');
            document.getElementById('completed-jobs-list').innerHTML = `<div class="p-3 border rounded-lg bg-green-50/50"><div class="font-bold text-sm">Hausanschluss</div><div class="text-xs text-green-600 font-bold">Gestern erledigt</div></div>`;
        }

        // --- MODAL & WIZARD ---
        function openModal(id) {
            activeTaskId = id;
            const t = currentTasks.find(x => x.id == id);
            if(!t) return;
            switchModalTab('info'); // Default to first tab
            
            document.getElementById('modal-edit-title').value = t.title;
            document.getElementById('modal-edit-category').value = t.category; 
            document.getElementById('modal-edit-duration').value = t.duration;
            document.getElementById('modal-edit-description').value = t.description || '';
            document.getElementById('modal-assignee-text').innerText = t.assignees.length > 0 ? `${t.assignees.length} Techs` : 'Keine';
            document.getElementById('modal-schedule').innerText = t.startDate || '--';
            document.getElementById('modal-travel').innerText = t.travelTime || '--';
            
            renderModalTaskAssignees(t);
            renderDependencyList(t);
            renderModalSubtasks(t);
            
            const repDiv = document.getElementById('modal-checklist-report');
            repDiv.innerHTML = (t.subtasks||[]).filter(s=>s.completed).map(s => `
                <div class="bg-slate-50 p-2 rounded border border-slate-100 mb-2">
                    <div class="flex justify-between text-xs"><span class="font-bold line-through">${s.text}</span><span>${s.time}</span></div>
                    <div class="text-[10px] text-slate-500 italic">${s.note || ''}</div>
                </div>
            `).join('') || '<div class="text-xs italic text-slate-400">Keine Daten.</div>';

            document.getElementById('task-modal').classList.remove('hidden');
            setTimeout(() => document.getElementById('task-modal-content').classList.remove('translate-x-full'), 10);
        }

        function switchModalTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active', 'border-b-2', 'border-brandDark', 'text-brandDark'));
            document.getElementById(`tab-${tabId}`).classList.add('active');
            document.getElementById(`tab-btn-${tabId}`).classList.add('active', 'border-b-2', 'border-brandDark', 'text-brandDark');
        }
        
        function closeModal() {
            document.getElementById('task-modal-content').classList.add('translate-x-full');
            setTimeout(() => document.getElementById('task-modal').classList.add('hidden'), 300);
        }

        function saveActiveTask() {
            const t = currentTasks.find(x => x.id == activeTaskId);
            if(t) {
                t.title = document.getElementById('modal-edit-title').value;
                t.description = document.getElementById('modal-edit-description').value;
                t.duration = document.getElementById('modal-edit-duration').value;
                t.category = document.getElementById('modal-edit-category').value; 
                renderPlanningViews(); closeModal(); showToast("Gespeichert.");
            }
        }
        
        function deleteActiveTask() {
            if(confirm("Löschen?")) {
                currentTasks = currentTasks.filter(x => x.id != activeTaskId);
                renderPlanningViews(); closeModal();
            }
        }

        function toggleTaskCrewEditor() { document.getElementById('modal-task-crew-select').classList.remove('hidden'); document.getElementById('modal-task-crew-checkboxes').innerHTML = visibleEmployeeIds.map(id => { const emp = allEmployees.find(e=>e.id==id); const t = currentTasks.find(x=>x.id==activeTaskId); return `<label class="text-xs flex gap-2"><input type="checkbox" ${t.assignees.includes(id)?'checked':''} onchange="updateTaskAssignee('${id}', this.checked)"> ${emp.name}</label>`; }).join(''); }
        function updateTaskAssignee(id, val) { const t = currentTasks.find(x=>x.id==activeTaskId); if(val) { if(!t.assignees.includes(id)) t.assignees.push(id); } else { t.assignees = t.assignees.filter(x=>x!==id); } renderPlanningViews(); openModal(activeTaskId); }
        function addTaskSubtask() { const txt = prompt("Schritt:"); if(txt) { const t = currentTasks.find(x=>x.id==activeTaskId); if(!t.subtasks) t.subtasks=[]; t.subtasks.push({text:txt, completed:false}); openModal(activeTaskId); } }
        function addManualTask() { const t = prompt("Aufgabenname:"); if(t) { currentTasks.push({id:'man_'+Date.now(), title:t, status:'open', assignees:[], duration:1, category:'Manual', subtasks:[]}); renderPlanningViews(); } }

        function openCrewModal() { document.getElementById('crew-modal').classList.remove('hidden'); document.getElementById('crew-list-container').innerHTML = allEmployees.map(e => `<div class="flex justify-between p-2 hover:bg-slate-50 cursor-pointer" onclick="toggleCrew('${e.id}')"><span>${e.name}</span>${visibleEmployeeIds.includes(e.id)?'<i class="fa-solid fa-check text-green-500"></i>':''}</div>`).join(''); setTimeout(()=>document.getElementById('crew-modal-content').classList.remove('translate-x-full'),10); }
        function closeCrewModal() { document.getElementById('crew-modal-content').classList.add('translate-x-full'); setTimeout(()=>document.getElementById('crew-modal').classList.add('hidden'),300); }
        function toggleCrew(id) { if(visibleEmployeeIds.includes(id)) visibleEmployeeIds = visibleEmployeeIds.filter(x=>x!==id); else visibleEmployeeIds.push(id); renderActiveCrewWidget(); renderPlanningViews(); openCrewModal(); }

        function initSortables() { Sortable.create(document.getElementById('checklist-source'), { group: 'tasks', animation: 150, onAdd: (evt)=>{ updateTaskStatus(evt.item.dataset.id, 'open', []); } }); }

        // --- WIZARD POPUP ---
        function openPlanWizard() {
            document.getElementById('plan-wizard-modal').classList.remove('hidden');
            document.getElementById('plan-wizard-content').classList.remove('translate-x-full');
            document.getElementById('wizard-plan-title-input').value = '';
            renderWizardProjectList();
            renderWizardAssetSelect();
            renderWizardTicketList();
        }
        function closePlanWizard() {
            document.getElementById('plan-wizard-content').classList.add('translate-x-full');
            setTimeout(() => document.getElementById('plan-wizard-modal').classList.add('hidden'), 300);
        }
        function toggleWizardType(t) {
            wizardType = t;
            ['project','appointments','custom', 'tickets'].forEach(x => document.getElementById(`wizard-form-${x}`).classList.add('hidden'));
            document.getElementById(`wizard-form-${t}`).classList.remove('hidden');
        }
        function renderWizardProjectList() {
            document.getElementById('wizard-planned-list').innerHTML = '<div class="text-xs italic">Keine Aufgaben geplant</div>';
            document.getElementById('wizard-remaining-list').innerHTML = checklistTemplate.map((item,i) => `
                <label class="flex gap-2 p-2 border bg-white rounded mb-2"><input type="checkbox" class="wizard-task-checkbox" value="${i}"> <div><div class="font-bold text-sm">${item.title}</div><div class="text-xs text-slate-400">${item.category}</div></div></label>
            `).join('');
            document.getElementById('wizard-pm-select').innerHTML = '<option value="">Wählen...</option>' + allEmployees.map(e=>`<option value="${e.id}">${e.name}</option>`).join('');
            document.getElementById('wizard-crew-select').innerHTML = allEmployees.map(e=>`<label class="text-xs border p-1 rounded"><input type="checkbox" class="wizard-crew-checkbox" value="${e.id}"> ${e.name}</label>`).join('');
            document.getElementById('wizard-appointments-list').innerHTML = unplannedAppointments.map(a => `<label class="block p-2 border rounded hover:bg-slate-50 cursor-pointer"><input type="radio" name="appt" value="${a.id}" onchange="selectAppointment('${a.id}')"> <span class="font-bold text-sm">${a.title}</span> <span class="text-xs text-orange-500">${a.date}</span></label>`).join('');
            document.getElementById('appt-checklist-select').innerHTML = checklistTemplate.map(t=>`<option value="${t.title}">${t.title}</option>`).join('');
        }
        function renderWizardAssetSelect() {
             const html = assetInventory.map(a=>`<label class="text-xs flex gap-1"><input type="checkbox" class="wizard-asset-checkbox" value="${a.id}"> ${a.name}</label>`).join('');
             document.getElementById('wizard-project-assets').innerHTML = html;
             document.getElementById('wizard-custom-assets').innerHTML = html;
             document.getElementById('wizard-appt-assets').innerHTML = html;
             document.getElementById('wizard-ticket-assets').innerHTML = html;
        }

        // --- TICKET FUNCTIONS ---
        function renderWizardTicketList() {
            document.getElementById('wizard-tickets-list').innerHTML = mockTickets.map(t => `
                <label class="block p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="ticket-selection" value="${t.id}" class="mt-1" onchange="selectTicket(${t.id})">
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-slate-800 text-sm">#${t.ticket_no} - ${t.error_type}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${t.priority==='High'?'bg-red-100 text-red-600':'bg-slate-100 text-slate-500'}">${t.priority}</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2">${t.problem}</p>
                            <div class="mt-2 text-[10px] text-slate-400 font-bold flex gap-3">
                                <span><i class="fa-solid fa-calendar"></i> ${t.date}</span>
                                <span><i class="fa-solid fa-circle-exclamation"></i> ${t.error_code}</span>
                            </div>
                        </div>
                    </div>
                </label>
            `).join('');
        }
        function selectTicket(id) {
            activeTicketId = id;
            const t = mockTickets.find(x => x.id === id);
            if(t) {
                document.getElementById('ticket-prio-display').value = t.priority;
                document.getElementById('wizard-ticket-resolution').classList.remove('hidden');
            }
        }

        function getSelectedAssets(id) { return Array.from(document.querySelectorAll(`#${id} input:checked`)).map(c=>c.value); }
        function selectAppointment(id) { activeAppointmentId = id; document.getElementById('wizard-appointment-resolution').classList.remove('hidden'); }
        function toggleApptResolveType(t) { document.getElementById('appt-resolve-link').classList.toggle('hidden', t!=='link'); document.getElementById('appt-resolve-manual').classList.toggle('hidden', t!=='manual'); }
        function addWizardStep() { const val = document.getElementById('wizard-custom-step-input').value; if(val) { tempWizardSteps.push({text:val, completed:false}); renderWizardSteps(); document.getElementById('wizard-custom-step-input').value=''; } }
        function renderWizardSteps() { document.getElementById('wizard-custom-steps-list').innerHTML = tempWizardSteps.map((s,i) => `<div class="text-xs border p-1 rounded flex justify-between"><span>${i+1}. ${s.text}</span><button onclick="tempWizardSteps.splice(${i},1); renderWizardSteps()">x</button></div>`).join(''); }
        
        function savePlanWizard() {
            if(wizardType === 'custom') {
                 const title = document.getElementById('wizard-custom-title').value;
                 if(title) currentTasks.push({ id: 'cust_'+Date.now(), title: title, status: 'open', assignees: [], duration: 1, category: 'Manual', subtasks: tempWizardSteps, assets: getSelectedAssets('wizard-custom-assets') });
                 tempWizardSteps = [];
            } else if (wizardType === 'project') {
                const selected = Array.from(document.querySelectorAll('.wizard-task-checkbox:checked')).map(cb => checklistTemplate[cb.value]);
                const pm = document.getElementById('wizard-pm-select').value;
                const crew = Array.from(document.querySelectorAll('.wizard-crew-checkbox:checked')).map(cb=>cb.value);
                if(pm) crew.push(pm);
                const uniqueCrew = [...new Set(crew)];
                const assets = getSelectedAssets('wizard-project-assets');
                selected.forEach((item, i) => {
                    const status = uniqueCrew.length > 0 ? 'scheduled' : 'open';
                    currentTasks.push({ id: 'nw_'+Date.now()+'_'+i, title: item.title, category: item.category, duration: item.duration, status: status, assignees: uniqueCrew, assets: assets, startHour: 8, predecessors: [], subtasks: [] });
                });
            } else if(wizardType === 'appointments' && activeAppointmentId) {
                 const a = unplannedAppointments.find(x=>x.id==activeAppointmentId);
                 currentTasks.push({id: 'apt_'+Date.now(), title: a.title, status: 'scheduled', assignees: a.crew, duration: 2, category: 'General', startHour: 8, assets: getSelectedAssets('wizard-appt-assets'), subtasks:[] });
                 unplannedAppointments = unplannedAppointments.filter(x=>x.id!==activeAppointmentId);
            } else if (wizardType === 'tickets' && activeTicketId) {
                const t = mockTickets.find(x => x.id === activeTicketId);
                const cat = document.getElementById('ticket-category-select').value;
                currentTasks.push({
                    id: 'ticket_' + t.ticket_no,
                    title: `Ticket #${t.ticket_no}: ${t.error_type}`,
                    description: t.problem,
                    category: cat,
                    status: 'open', 
                    assignees: [],
                    duration: 2, 
                    startHour: 8,
                    assets: getSelectedAssets('wizard-ticket-assets'),
                    subtasks: []
                });
            }
            renderPlanningViews(); closePlanWizard(); showToast("Plan gespeichert.");
        }

        // --- DEPENDENCIES & SUBTASKS ---
        function renderDependencyList(t) {
            const el = document.getElementById('modal-dependency-list');
            el.innerHTML = (t.predecessors||[]).map(pid => {
                const pt = currentTasks.find(x=>x.id==pid);
                return pt ? `<div class="text-xs bg-blue-50 p-2 rounded text-blue-700 flex justify-between"><span>${pt.title}</span><button onclick="removeDependency('${pid}')">x</button></div>` : '';
            }).join('') || '<span class="text-xs italic text-slate-400">Keine Abhängigkeiten</span>';
            
            const sel = document.getElementById('modal-dependency-select');
            sel.innerHTML = '<option value="">Vorgänger wählen...</option>';
            currentTasks.filter(x => x.id !== t.id).forEach(x => sel.innerHTML += `<option value="${x.id}">${x.title}</option>`);
        }
        function addDependency() {
            const pid = document.getElementById('modal-dependency-select').value;
            if(pid) { currentTasks.find(x=>x.id==activeTaskId).predecessors.push(pid); openModal(activeTaskId); }
        }
        function removeDependency(pid) {
            const t = currentTasks.find(x=>x.id==activeTaskId);
            t.predecessors = t.predecessors.filter(x=>x!==pid);
            openModal(activeTaskId);
        }
        
        function renderModalSubtasks(t) {
            document.getElementById('modal-subtasks-list').innerHTML = (t.subtasks||[]).map((s,i) => `
                <div class="flex gap-2 items-center text-sm"><input type="checkbox" ${s.completed?'checked':''} onchange="toggleSubtask(${i},this.checked)"> <span class="${s.completed?'line-through text-slate-400':''}">${s.text}</span></div>
            `).join('') || '<span class="text-xs italic text-slate-400">Keine Schritte.</span>';
        }
        function toggleSubtask(i, val) {
             currentTasks.find(x=>x.id==activeTaskId).subtasks[i].completed = val;
             renderModalSubtasks(currentTasks.find(x=>x.id==activeTaskId));
        }
        function addTaskSubtask() {
            const txt = prompt("Schritt:");
            if(txt) {
                const t = currentTasks.find(x=>x.id==activeTaskId);
                if(!t.subtasks) t.subtasks=[];
                t.subtasks.push({text:txt, completed:false});
                renderModalSubtasks(t);
            }
        }
        function renderModalTaskAssignees(t) {
            document.getElementById('modal-task-assignees').innerHTML = (t.assignees||[]).map(id => {
                const emp = allEmployees.find(e=>e.id==id);
                return `<div class="bg-slate-100 px-2 py-1 rounded text-xs flex gap-1 items-center"><img src="${emp.avatar}" class="w-4 h-4 rounded-full">${emp.name}</div>`;
            }).join('');
        }
        
        window.onload = function() {
            initData();
            initSortables();
        }
        window.addEventListener('resize', () => { if(!document.getElementById('view-gantt').classList.contains('hidden')) drawGanttLines(); });
    </script>
</body>
</html>