<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>WorkForce Pro 2.0 - Dispatcher View</title>
    
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
        .gantt-grid-line {
            border-right: 1px dashed #e2e8f0;
            height: 100%;
            position: absolute;
            top: 0;
        }
        .gantt-bar {
            position: absolute;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 10;
        }
        .gantt-bar:hover {
            transform: scale(1.02);
            z-index: 20;
        }
         

        /* Modal Tabs */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { border-bottom-color: #164191; color: #164191; font-weight: 700; border-bottom-width: 2px; }
        .tab-btn { border-bottom-width: 2px; border-color: transparent; }
        
        /* Avatar Stack */
        .avatar-stack { display: flex; -space-x: 0.5rem; }
        .avatar-stack img { border: 2px solid white; border-radius: 9999px; }
        .avatar-stack { display:flex; }
        .avatar-stack > * { margin-left: -0.5rem; }
        .avatar-stack > *:first-child { margin-left: 0; }


        /* Calendar Grid */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #e2e8f0;
        }
        .calendar-day {
            background-color: white;
            min-height: 120px;
            position: relative;
        }
        .calendar-day.today {
            background-color: #f0f9ff;
        }
    </style>
</head>
<body class="text-slate-800 h-screen overflow-hidden flex flex-col font-sans">

    <!-- Top Navigation / Context Bar -->
      <header class="glass-panel z-50 px-6 py-4 flex items-center justify-between sticky top-0">
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
                
                <!-- Level 1: Customer Selector (Searchable) -->
                <div class="relative group min-w-[250px]" id="customer-select-container">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                   <input
                    type="text"
                    id="customer-search-input"
                    placeholder="Search Customer..."
                    class="w-full pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white"
                    autocomplete="off"
                    />

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
                    <select id="project-selector" class="appearance-none pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white min-w-[280px]" disabled>
                        <option value="">Select Product & Site...</option>
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
                    <select id="stage-selector"
                            class="appearance-none pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-actionGreen/20 focus:border-actionGreen transition-all cursor-pointer hover:bg-white">
                          <option value="">Bitte zuerst Kunde & Projekt wählen…</option>
                    </select>

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
                
                <!-- Date Display -->
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium bg-white/30 px-3 py-2 rounded-lg">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Today, Oct 24</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button onclick="openPlanWizard()" class="bg-gradient-to-r from-sky-500 to-brandDark hover:from-sky-600 hover:to-blue-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-500/20 transition-all active:scale-95 flex items-center gap-2 border border-white/20">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Create Plan</span>
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
                <span>Publish</span>
            </button>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 p-4 md:p-6 overflow-hidden flex gap-6">
        
        <!-- LEFT PANEL: The Backlog (Source) -->
        <section class="w-1/3 flex flex-col gap-4 h-full">
            <!-- Panel Header with Tabs -->
            <div class="flex flex-col gap-3">
                <div class="flex bg-white/50 rounded-xl p-1 border border-slate-200">
                    <button onclick="switchLeftTab('backlog')" id="left-btn-backlog" class="flex-1 py-2 rounded-lg text-sm font-bold bg-white text-slate-800 shadow-sm transition-all">
                        Checklist <span class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 rounded-md" id="task-count">0</span>
                    </button>
                    <button onclick="switchLeftTab('unplanned')" id="left-btn-unplanned" class="flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all">
                        Unplanned <span class="ml-1 text-xs bg-orange-100 text-orange-600 px-1.5 rounded-md" id="unplanned-count">0</span>
                    </button>
                </div>
                
                <!-- Search & Filters -->
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" id="task-search" placeholder="Search items..." 
                        class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none">
                </div>
            </div>

            <!-- Scrollable List Container -->
            <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto overflow-x-hidden relative">
                <div class="absolute top-0 left-0 w-full h-4 bg-gradient-to-b from-white/50 to-transparent z-10 pointer-events-none"></div>
                
                <!-- TAB CONTENT: BACKLOG -->
                <div id="left-tab-backlog" class="flex flex-col gap-3 min-h-[200px] pb-10">
                    <div id="checklist-source" class="flex flex-col gap-3">
                        <!-- Items injected via JS -->
                    </div>
                    <!-- Add Manual Task Button -->
                    <button onclick="addManualTask()" class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Add Manual Task
                    </button>
                </div>

                <!-- TAB CONTENT: UNPLANNED -->
                <div id="left-tab-unplanned" class="hidden flex flex-col gap-3 min-h-[200px] pb-10">
                    <div id="unplanned-source" class="flex flex-col gap-3">
                        <!-- Items injected via JS -->
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT PANEL: Resource Scheduler (Target) -->
        <section class="w-2/3 flex flex-col gap-4 h-full relative">
             <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-brandDark rounded-full"></span>
                        Resource Schedule
                    </h2>
                    
                    <!-- View Switcher -->
                    <div class="flex bg-white/60 p-1 rounded-lg border border-slate-200">
                        <button onclick="switchView('board')" id="btn-view-board" class="px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-solid fa-table-columns"></i> Board
                        </button>
                        <button onclick="switchView('gantt')" id="btn-view-gantt" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-solid fa-timeline"></i> Gantt
                        </button>
                        <button onclick="switchView('calendar')" id="btn-view-calendar" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-regular fa-calendar-days"></i> Calendar
                        </button>
                        <button onclick="switchView('list')" id="btn-view-list" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-solid fa-list-check"></i> List
                        </button>
                        <button onclick="switchView('history')" id="btn-view-history" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left"></i> History
                        </button>
                    </div>
                </div>

                <!-- Active Crew Widget -->
                <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Active Crew</span>
                    <div class="flex -space-x-2" id="active-crew-avatars">
                        <!-- JS Injected Active Crew Avatars -->
                    </div>
                    <button onclick="openCrewModal()" class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-brandDark hover:text-white transition-colors border border-slate-200 border-dashed" title="Manage Crew">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- VIEW 1: Board (Columns) -->
            <div id="view-board" class="view-container grid grid-cols-1 md:grid-cols-3 gap-4 h-full overflow-hidden">
                <!-- JS Generated Columns based on Active Crew -->
            </div>

            <!-- VIEW 2: Gantt (Timeline) -->
            <div id="view-gantt" class="view-container hidden h-full glass-panel rounded-[2rem] flex flex-col overflow-hidden relative">
                <!-- Timeline Header -->
                <div class="h-12 bg-white/50 border-b flex items-center pl-48 pr-4">
                    <div id="time-scale" class="flex-1 flex justify-between text-xs text-slate-400 font-bold uppercase tracking-wider relative h-full items-center">
                        <!-- JS generated time markers -->
                    </div>
                </div>
                
                <!-- Gantt Body -->
                <div id="gantt-body" class="flex-1 overflow-y-auto relative bg-slate-50/30">
                    <!-- SVG Overlay for dependencies -->
                    <svg id="gantt-lines" class="absolute top-0 left-0 w-full h-full pointer-events-none z-0 overflow-visible"></svg>
                    <!-- JS generated rows -->
                </div>
            </div>

            <!-- VIEW 3: List (Table) -->
            <div id="view-list" class="view-container hidden h-full glass-panel rounded-[2rem] overflow-hidden flex flex-col">
                <div class="bg-white/50 border-b px-6 py-3 grid grid-cols-12 gap-4 text-xs font-bold text-slate-500 uppercase">
                    <div class="col-span-4">Task</div>
                    <div class="col-span-2">Schedule</div>
                    <div class="col-span-3">Travel & Route</div>
                    <div class="col-span-1">Dur.</div>
                    <div class="col-span-2 text-right">Actions</div>
                </div>
                <div id="list-body" class="overflow-y-auto flex-1 p-2 space-y-1">
                    <!-- JS generated rows -->
                </div>
            </div>

            <!-- VIEW 4: Calendar -->
            <div id="view-calendar" class="view-container hidden h-full glass-panel rounded-[2rem] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between p-4 border-b border-white/50">
                    <h3 class="text-lg font-bold text-slate-800">October 2023</h3>
                    <div class="flex gap-2">
                        <button class="w-8 h-8 rounded-full bg-white text-slate-500 hover:bg-slate-100 flex items-center justify-center"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="w-8 h-8 rounded-full bg-white text-slate-500 hover:bg-slate-100 flex items-center justify-center"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="grid grid-cols-7 text-center py-2 bg-white/30 text-xs font-bold text-slate-500 uppercase">
                    <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                </div>
                <div id="calendar-body" class="calendar-grid flex-1 overflow-y-auto">
                    <!-- JS Generated Calendar Cells -->
                </div>
            </div>

            <!-- VIEW 5: History -->
            <div id="view-history" class="view-container hidden h-full glass-panel rounded-[2rem] overflow-hidden flex flex-col p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Activity & Change Log</h3>
                <div id="history-list" class="overflow-y-auto flex-1 space-y-4 pr-2">
                    <!-- JS Generated History -->
                </div>
            </div>

        </section>
    </main>

    <!-- PLAN WIZARD MODAL --> 
    <div id="plan-wizard-modal" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closePlanWizard()"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-4xl h-[80vh] rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">

            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-800 truncate">
                    Plan: <span id="wizard-plan-name" class="text-brandDark">—</span>
                    </h2>

                    <span id="wizard-plan-badge"
                        class="hidden text-[11px] font-extrabold px-3 py-1 rounded-full bg-brandDark/10 text-brandDark">
                    #<span id="wizard-plan-id">—</span>
                    </span>

                    <span id="wizard-stage-badge"
                        class="hidden text-[11px] font-extrabold px-3 py-1 rounded-full bg-actionGreen/10 text-actionGreen">
                    <span id="wizard-stage-name">—</span>
                    </span>
                </div>

                <p class="text-sm text-slate-500 mt-1">
                    Kunde:
                    <span id="wizard-customer-name" class="font-bold text-brandDark">—</span>
                    <span class="mx-2 text-slate-300">·</span>
                    Produkt:
                    <span id="wizard-product-name" class="font-bold text-slate-700">—</span>
                </p>
                </div>

                <button onclick="closePlanWizard()"
                        class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Sidebar Choices -->
                <div class="w-1/3 bg-slate-50 p-6 border-r border-slate-100 flex flex-col gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="plan-type" value="project" class="peer hidden" checked onchange="toggleWizardType('project')">
                    <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mb-3"><i class="fa-solid fa-layer-group text-xl"></i></div>
                    <h3 class="font-bold text-slate-800">Project Phase</h3>
                    <p class="text-xs text-slate-500 mt-1">Load checklist from customer product (e.g. PV Montage).</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="plan-type" value="appointments" class="peer hidden" onchange="toggleWizardType('appointments')">
                    <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-3"><i class="fa-regular fa-calendar-check text-xl"></i></div>
                    <h3 class="font-bold text-slate-800">Open Appointments</h3>
                    <p class="text-xs text-slate-500 mt-1">Resolve calendar entries missing specific task definitions.</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="plan-type" value="custom" class="peer hidden" onchange="toggleWizardType('custom')">
                    <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mb-3"><i class="fa-solid fa-pen-to-square text-xl"></i></div>
                    <h3 class="font-bold text-slate-800">Custom Task</h3>
                    <p class="text-xs text-slate-500 mt-1">Create a standalone task or group of ad-hoc tasks.</p>
                    </div>
                </label>
                </div>

                <!-- Main Content Area -->
                <div class="w-2/3 p-8 overflow-y-auto" id="wizard-content-area">
                <!-- Project Task Form -->
                <div id="wizard-form-project" class="space-y-6">
                    <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Phase: Montage</h3>
                    <span class="text-xs font-bold bg-actionGreen/10 text-actionGreen px-3 py-1 rounded-full">
                        Product: PV System
                    </span>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-3 flex items-center gap-3">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400"></i>
                        <input id="wizard-task-search"
                            type="text"
                            placeholder="Search tasks (title, description, category, stage)..."
                            class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark"
                            autocomplete="off"/>
                    </div>

                    <button type="button"
                            onclick="window.__wfWizardCollapseAll?.(true)"
                            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200"
                            title="Collapse all stages">
                        Collapse
                    </button>

                    <button type="button"
                            onclick="window.__wfWizardCollapseAll?.(false)"
                            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200"
                            title="Expand all stages">
                        Expand
                    </button>
                    </div>

                    <div class="space-y-2">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">Already Planned</h4>
                    <div id="wizard-planned-list" class="space-y-3 opacity-90"></div>
                    </div>

                    <div class="space-y-2 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-brandDark uppercase tracking-wide">Remaining / Unplanned</h4>
                    <div id="wizard-remaining-list" class="space-y-3"></div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4 mt-6">
                    <h4 class="font-bold text-slate-800 text-sm">
                        <i class="fa-solid fa-user-plus mr-2 text-sky-500"></i>Bulk Assignment
                    </h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Project Manager (Lead)</label>
                        <select id="wizard-pm-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                            <option value="">Select Lead...</option>
                        </select>
                        </div>

                        <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Date &amp; Time</label>
                        <input type="datetime-local" id="wizard-date" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Crew Members</label>
                        <div class="flex flex-wrap gap-2" id="wizard-crew-select"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Required Assets (Optional)</label>
                        <div class="bg-white border border-slate-300 rounded-lg p-2">
                        <div class="relative mb-2">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            <input id="wizard-assets-search"
                                type="text"
                                placeholder="Search assets (item/model)..."
                                class="w-full pl-8 pr-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20"
                                autocomplete="off"/>
                        </div>
                        <div id="wizard-project-assets" class="max-h-48 overflow-y-auto grid grid-cols-2 gap-2"></div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Appointments -->
                <div id="wizard-form-appointments" class="hidden space-y-6">
                    <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Resolve Unplanned Appointments</h3>
                        <p class="text-sm text-slate-500">
                        These calendar entries have date/time (and possibly crew) but are not converted into plan items yet.
                        </p>
                    </div>
                    <div class="text-xs font-bold bg-orange-100 text-orange-700 px-3 py-1 rounded-full">
                        Unplanned only
                    </div>
                    </div>

                    <div class="space-y-3" id="wizard-appointments-list"></div>

                    <div id="wizard-appointment-resolution"
                        class="hidden mt-6 pt-6 border-t border-slate-200 animate-in fade-in slide-in-from-top-4 space-y-5">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-slate-800">Define Plan Item for Selected Appointment</h4>
                        <div class="text-xs text-slate-500">
                        <span class="font-bold text-brandDark" id="wizard-selected-appt-title">—</span>
                        <span class="mx-1">·</span>
                        <span id="wizard-selected-appt-time">—</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Project Manager (Lead)</label>
                            <select id="appt-pm-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                            <option value="">Select Lead...</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">
                            Will be saved into plan meta and also assigned to this appointment-item.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Crew Members</label>
                            <div id="appt-crew-select"
                                class="flex flex-wrap gap-2 bg-white border border-slate-300 rounded-lg p-2 max-h-28 overflow-y-auto"></div>
                            <p class="text-[11px] text-slate-400 mt-1">
                            Auto-filled from appointment crew when available (editable).
                            </p>
                        </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                        <input type="radio" name="appt-resolve-type" value="link" class="peer hidden" checked
                                onchange="toggleApptResolveType('link')">
                        <div class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-brandDark peer-checked:bg-blue-50 peer-checked:text-brandDark transition-all text-center text-sm font-bold">
                            Link to Checklist Item
                        </div>
                        </label>

                        <label class="cursor-pointer">
                        <input type="radio" name="appt-resolve-type" value="manual" class="peer hidden"
                                onchange="toggleApptResolveType('manual')">
                        <div class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-brandDark peer-checked:bg-blue-50 peer-checked:text-brandDark transition-all text-center text-sm font-bold">
                            Define Manually
                        </div>
                        </label>
                    </div>

                    <div id="appt-resolve-link" class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Select Checklist Item</label>
                        <select id="appt-checklist-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm"></select>
                        <p class="text-[11px] text-slate-400">
                        The appointment will be converted into a plan item using the chosen checklist task title/category/duration.
                        </p>
                    </div>

                    <div id="appt-resolve-manual" class="hidden space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Task Title</label>
                            <input type="text" id="appt-manual-title" class="w-full p-2 rounded-lg border border-slate-300 text-sm"
                                placeholder="e.g. On-site inspection">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Category</label>
                            <select id="appt-manual-category" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                            <option>General</option>
                            <option>Electric</option>
                            <option>Roof</option>
                            <option>Cleanup</option>
                            </select>
                        </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Duration (minutes)</label>
                            <input type="number" id="appt-manual-duration" class="w-full p-2 rounded-lg border border-slate-300 text-sm" value="60" min="1">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Description (optional)</label>
                            <input type="text" id="appt-manual-desc" class="w-full p-2 rounded-lg border border-slate-300 text-sm"
                                placeholder="Short notes...">
                        </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500">Required Assets (Optional)</label>

                        <div class="bg-white border border-slate-300 rounded-lg p-2">
                        <div class="relative mb-2">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            <input id="wizard-appt-assets-search"
                                type="text"
                                placeholder="Search assets (item/model)..."
                                class="w-full pl-8 pr-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20"
                                autocomplete="off"/>
                        </div>
                        <div class="max-h-40 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-appt-assets"></div>
                        </div>

                        <p class="text-[11px] text-slate-400">
                        These assets will be stored on the created planner item (planner_item_assets).
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button"
                                onclick="window.__wfCancelApptResolve?.()"
                                class="px-4 py-2 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-100 border border-slate-200">
                        Cancel
                        </button>
                        <button type="button"
                                onclick="window.__wfSaveApptResolve?.()"
                                class="px-5 py-2 rounded-lg text-sm font-bold bg-brandDark text-white hover:bg-blue-800 shadow-lg shadow-brandDark/20">
                        Convert Appointment to Plan Item
                        </button>
                    </div>
                    </div>
                </div>

                <!-- Custom Task -->
                <div id="wizard-form-custom" class="hidden space-y-6">
                     <!-- Put this INSIDE your existing #wizard-form-custom (the “Custom” wizard tab) -->
                  <div class="bg-white border border-slate-200 rounded-2xl p-4">
                    <div class="flex items-start justify-between gap-4">
                      <div>
                        <div class="font-extrabold text-slate-800">Custom Tasks</div>
                        <div class="text-xs text-slate-500 mt-1">
                          Create a single task or a task with multiple checklist steps + times. You can also mark “Image required” and “Customer consent required”.
                        </div>
                      </div>

                      <button type="button"
                        class="shrink-0 px-4 py-2 rounded-xl bg-brandDark text-white text-xs font-extrabold hover:bg-blue-800"
                        onclick="window.openCustomTaskModal()"
                      >
                        <i class="fa-solid fa-plus mr-2"></i>Add Custom Task
                      </button>
                    </div>

                    <!-- Preview of custom tasks already added to draft -->
                    <div id="custom-tasks-preview" class="mt-4 space-y-2"></div>
                  </div>

                  <!-- =======================================================================
                      CUSTOM TASK MODAL
                      ======================================================================= -->
                  <div id="custom-task-modal" class="fixed inset-0 z-[80] hidden">
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="window.closeCustomTaskModal()"></div>

                    <div class="relative mx-auto mt-10 w-[min(980px,calc(100%-24px))]">
                      <div class="bg-white rounded-[2rem] shadow-xl border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                          <div>
                            <div class="text-lg font-extrabold text-slate-800">Add Custom Task</div>
                            <div class="text-xs text-slate-500 mt-1">Single task or multi-checklist task with times.</div>
                          </div>
                          <button type="button" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200"
                            onclick="window.closeCustomTaskModal()">
                            <i class="fa-solid fa-xmark text-slate-700"></i>
                          </button>
                        </div>

                        <div class="p-6 grid grid-cols-12 gap-6">
                          <!-- LEFT -->
                          <div class="col-span-12 lg:col-span-7 space-y-5">
                            <!-- Mode -->
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                              <div class="text-xs font-extrabold text-slate-700 mb-3">Mode</div>

                              <div class="flex flex-wrap gap-3">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white cursor-pointer">
                                  <input type="radio" name="custom-task-mode" value="single" checked
                                    onchange="window.__wfCustomTaskSetMode('single')">
                                  <span class="text-xs font-bold text-slate-700">Single task</span>
                                </label>

                                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white cursor-pointer">
                                  <input type="radio" name="custom-task-mode" value="bundle"
                                    onchange="window.__wfCustomTaskSetMode('bundle')">
                                  <span class="text-xs font-bold text-slate-700">Task with checklist steps</span>
                                </label>

                                <label id="custom-split-wrap" class="hidden flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white cursor-pointer">
                                  <input id="custom-split-tasks" type="checkbox" onchange="window.__wfCustomTaskToggleSplit(this.checked)">
                                  <span class="text-xs font-bold text-slate-700">Create as multiple tasks</span>
                                </label>
                              </div>
                            </div>

                            <!-- Main fields -->
                            <div class="grid grid-cols-12 gap-4">
                              <div class="col-span-12">
                                <label class="text-xs font-extrabold text-slate-700">Title</label>
                                <input id="custom-title" type="text"
                                  class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brandDark/30"
                                  placeholder="e.g. Additional Work / Special Request">
                              </div>

                              <div class="col-span-12 sm:col-span-6">
                                <label class="text-xs font-extrabold text-slate-700">Category</label>
                                <select id="custom-category"
                                  class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brandDark/30">
                                  <option value="General">General</option>
                                  <option value="Electric">Electric</option>
                                  <option value="Roof">Roof</option>
                                  <option value="Manual">Manual</option>
                                </select>
                              </div>

                              <div class="col-span-12 sm:col-span-3">
                                <label class="text-xs font-extrabold text-slate-700">Start time</label>
                                <input id="custom-start-time" type="time"
                                  class="mt-1 w-full px-3 py-3 rounded-xl border border-slate-200 bg-white"
                                  value="08:00">
                              </div>

                              <div class="col-span-12 sm:col-span-3" id="custom-single-duration-wrap">
                                <label class="text-xs font-extrabold text-slate-700">Duration (min)</label>
                                <input id="custom-duration-min" type="number" min="5" step="5"
                                  class="mt-1 w-full px-3 py-3 rounded-xl border border-slate-200 bg-white"
                                  value="60">
                              </div>

                              <div class="col-span-12">
                                <label class="text-xs font-extrabold text-slate-700">Description</label>
                                <textarea id="custom-desc" rows="3"
                                  class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brandDark/30"
                                  placeholder="Notes…"></textarea>
                              </div>
                            </div>

                            <!-- Flags (single defaults; bundle can override per row too) -->
                            <div id="custom-single-flags" class="bg-white border border-slate-200 rounded-2xl p-4">
                              <div class="text-xs font-extrabold text-slate-700 mb-3">Requirements</div>
                              <div class="flex flex-wrap gap-3">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                                  <input id="custom-require-image" type="checkbox">
                                  <span class="text-xs font-bold text-slate-700">Image required</span>
                                </label>
                                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                                  <input id="custom-require-consent" type="checkbox">
                                  <span class="text-xs font-bold text-slate-700">Customer consent required</span>
                                </label>
                              </div>
                            </div>

                            <!-- Bundle checklist editor -->
                            <div id="custom-bundle-wrap" class="hidden bg-white border border-slate-200 rounded-2xl p-4">
                              <div class="flex items-center justify-between gap-3">
                                <div>
                                  <div class="text-xs font-extrabold text-slate-700">Checklist steps</div>
                                  <div class="text-[11px] text-slate-500 mt-1">Each step can have its own time + requirements.</div>
                                </div>
                                <button type="button"
                                  class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold hover:bg-slate-800"
                                  onclick="window.__wfCustomTaskAddRow()">
                                  <i class="fa-solid fa-plus mr-2"></i>Add step
                                </button>
                              </div>

                              <div id="custom-bundle-rows" class="mt-4 space-y-3"></div>
                            </div>
                          </div>

                          <!-- RIGHT: Assignment + Assets -->
                          <div class="col-span-12 lg:col-span-5 space-y-5">
                            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                              <div class="text-xs font-extrabold text-slate-700 mb-3">Assignment</div>

                              <label class="text-xs font-bold text-slate-600">Lead (PM)</label>
                              <select id="custom-lead-select"
                                class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-white">
                                <option value="">Select Lead…</option>
                              </select>

                              <div class="mt-4">
                                <div class="text-xs font-bold text-slate-600 mb-2">Crew</div>
                                <div id="custom-crew-select" class="flex flex-wrap gap-2"></div>
                              </div>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                              <div class="text-xs font-extrabold text-slate-700 mb-3">Assets</div>
                              <input id="custom-assets-search" type="text"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white"
                                placeholder="Search assets…">
                              <div id="custom-assets-grid" class="mt-3 grid grid-cols-2 gap-2 max-h-[260px] overflow-auto pr-1"></div>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                              <div class="text-xs font-extrabold text-slate-700">Uses wizard bulk assignment</div>
                              <div class="text-[11px] text-slate-500 mt-1">
                                If you already selected Lead/Crew/Assets in the wizard, this modal will prefill from that.
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="px-6 py-5 border-t border-slate-100 flex items-center justify-between gap-3">
                          <button type="button" class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold"
                            onclick="window.closeCustomTaskModal()">
                            Cancel
                          </button>

                          <button type="button" class="px-5 py-3 rounded-xl bg-brandDark hover:bg-blue-800 text-white text-xs font-extrabold"
                            onclick="window.__wfCustomTaskSave()">
                            <i class="fa-solid fa-check mr-2"></i>Add to Plan (draft)
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button onclick="closePlanWizard()"
                        class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition-colors">
                Cancel
                </button>
                <button onclick="savePlanWizard()"
                        class="px-8 py-3 rounded-xl font-bold bg-brandDark text-white shadow-lg shadow-brandDark/20 hover:bg-blue-800 transition-transform active:scale-95">
                Save Plan
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- TASK INSPECTOR MODAL -->
    <div id="task-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform duration-300 flex flex-col translate-x-full" id="task-modal-content">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <div class="flex justify-between items-start mb-4">
                    <!-- Category Select -->
                    <select id="modal-edit-category" class="bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded uppercase tracking-wide px-2 py-1 outline-none focus:ring-2 focus:ring-brandDark/20">
                        <option value="General">General</option>
                        <option value="Prep">Prep</option>
                        <option value="Roof">Roof</option>
                        <option value="Electric">Electric</option>
                        <option value="Cleanup">Cleanup</option>
                        <option value="Manual">Manual</option>
                    </select>
                    <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <!-- Title Input -->
                <input type="text" id="modal-edit-title" class="text-2xl font-bold text-slate-800 mb-2 w-full bg-transparent border-b border-transparent hover:border-slate-300 focus:border-brandDark focus:outline-none transition-colors" value="Task Title">
                
                <div class="flex items-center gap-4 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i> 
                        <input type="number" id="modal-edit-duration" class="w-12 bg-transparent border-b border-slate-300 text-center focus:border-brandDark outline-none" value="2">
                        <span>h Estimate</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users"></i> 
                        <span id="modal-assignee-text">Unassigned</span>
                    </div>
                </div>
            </div>

            <!-- Modal Tabs -->
            <div class="flex border-b border-slate-200 px-6">
                <button onclick="switchModalTab('overview')" id="tab-btn-overview" class="tab-btn active px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Overview</button>
                <button onclick="switchModalTab('field-report')" id="tab-btn-field-report" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Field Report</button>
                <button onclick="switchModalTab('files')" id="tab-btn-files" class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Files</button>
            </div>

            <!-- Modal Content Area -->
            <div class="p-6 flex-1 overflow-y-auto bg-white space-y-6">
                
                <!-- TAB: OVERVIEW -->
                <div id="tab-overview" class="tab-content active space-y-6">
                    
                    <!-- Schedule & Travel Info -->
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-bold block mb-1">Schedule</span>
                            <div class="text-sm font-bold text-slate-800" id="modal-schedule">--</div>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-bold block mb-1">Travel</span>
                            <div class="text-sm font-bold text-slate-800" id="modal-travel">--</div>
                        </div>
                    </div>

                    <!-- Task Crew Editor -->
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-bold text-blue-900">Task Crew</h3>
                            <button onclick="toggleTaskCrewEditor()" class="text-xs bg-white text-blue-700 px-2 py-1 rounded border border-blue-200 font-bold hover:bg-blue-50">
                                <i class="fa-solid fa-pen mr-1"></i> Edit Crew
                            </button>
                        </div>
                        
                        <!-- List of assignees for THIS specific task -->
                        <div id="modal-task-assignees" class="flex flex-wrap gap-2">
                            <!-- JS Injected -->
                        </div>

                        <!-- Task Crew Selection Area (Hidden by default) -->
                        <div id="modal-task-crew-select" class="hidden mt-3 pt-3 border-t border-blue-200 space-y-2">
                            <p class="text-xs text-blue-600 mb-2 font-medium">Select multiple technicians:</p>
                            <div id="modal-task-crew-checkboxes" class="grid grid-cols-2 gap-2">
                                <!-- JS Injected checkboxes -->
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Description</h3>
                        <textarea id="modal-edit-description" rows="3" class="w-full text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brandDark/20 outline-none resize-none"></textarea>
                    </div>
                    
                    <!-- Assigned Assets Section -->
                    <div id="modal-assigned-assets-container">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-slate-900">Required Assets</h3>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-xl p-3">
                            <div class="relative mb-2">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                            <input
                                id="modal-assets-search"
                                type="text"
                                placeholder="Search assets (item/model)..."
                                class="w-full pl-8 pr-3 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20"
                                autocomplete="off"
                            />
                            </div>

                            <!-- FIXED HEIGHT (prevents modal height changes) -->
                            <div id="modal-assets-picker" class="max-h-56 overflow-y-auto grid grid-cols-2 gap-2"></div>
                        </div>
                    </div>


                    <!-- Dependencies -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Dependencies</h3>
                        <div class="space-y-2">
                            <div id="modal-dependency-list" class="space-y-2">
                                <!-- Dependencies injected here via JS -->
                            </div>
                            <div class="flex gap-2 mt-2">
                                <select id="modal-dependency-select" class="flex-1 text-sm border border-slate-200 rounded-lg p-2 bg-slate-50 focus:ring-2 focus:ring-brandDark/20 outline-none">
                                    <!-- Options injected here via JS -->
                                </select>
                                <button onclick="addDependency()" class="bg-slate-100 hover:bg-brandDark hover:text-white border border-slate-200 text-slate-600 px-3 py-2 rounded-lg text-sm font-bold transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-tasks -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-bold text-slate-900">Sub-tasks / Checklist</h3>
                            <button onclick="addTaskSubtask()" class="text-xs text-blue-600 hover:underline"><i class="fa-solid fa-plus"></i> Add Step</button>
                        </div>
                        <div id="modal-subtasks-list" class="space-y-2">
                            <!-- JS Injected Subtasks -->
                        </div>
                    </div>
                </div>

                <!-- TAB: FIELD REPORT -->
                <div id="tab-field-report" class="tab-content space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-3">Checklist Execution Report</h3>
                        <div class="space-y-3" id="modal-checklist-report">
                            <!-- JS Injected -->
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-3">Activity Log</h3>
                        <div class="space-y-4 relative border-l-2 border-slate-200 ml-2 pl-6" id="modal-activity-log">
                            <!-- JS Injected -->
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-3">Expenses & Extra Work</h3>
                        <div class="space-y-2" id="modal-financials">
                            <!-- JS Injected -->
                        </div>
                    </div>
                </div>

                <!-- TAB: FILES -->
                <div id="tab-files" class="tab-content space-y-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-sm font-bold text-slate-900">Project Documents</h3>
                        <button class="text-xs bg-brandDark text-white px-3 py-1.5 rounded hover:bg-blue-800"><i class="fa-solid fa-upload mr-1"></i> Upload</button>
                    </div>
                    <div class="space-y-2" id="modal-files-list">
                        <!-- JS Injected -->
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t bg-slate-50 flex justify-between">
                <button onclick="deleteActiveTask()" class="text-red-500 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
                <div class="flex gap-2">
                    <button onclick="closeModal()" class="text-slate-500 hover:bg-slate-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Cancel</button>
                    <button onclick="saveActiveTask()" class="bg-brandDark text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition-colors shadow-lg shadow-brandDark/20">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-10 right-10 bg-brandDark text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-3 z-50">
        <i class="fa-solid fa-circle-check text-actionGreen text-xl"></i>
        <div>
            <h4 class="font-bold text-sm">Action Successful</h4>
            <p class="text-xs text-slate-300">System updated.</p>
        </div>
    </div>
 
    <!-- CREW MODAL (required for openCrewModal) -->
    <div id="crew-modal" class="fixed inset-0 z-[130] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCrewModal()"></div>

    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl transform transition-transform duration-300 translate-x-full"
        id="crew-modal-content">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <div class="text-lg font-bold text-slate-800">Manage Crew</div>
            <div class="text-xs text-slate-500">Select technicians for this schedule</div>
        </div>
        <button onclick="closeCrewModal()"
                class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500">
            <i class="fa-solid fa-xmark"></i>
        </button>
        </div>

        <div class="p-4 overflow-y-auto" style="max-height: calc(100vh - 80px);">
        <div id="crew-list-container" class="space-y-2">
            <!-- injected -->
        </div>
        </div>
    </div>
    </div>
     
<script>
(() => {
  "use strict";

  /* =========================================================================
   * WF Runtime (global)
   * ========================================================================= */
  window.__WF = window.__WF || {};
  const WFRT = window.__WF;

  WFRT.$ = WFRT.$ || ((id) => document.getElementById(id));
  const $ = WFRT.$;

  WFRT.START_HOUR = WFRT.START_HOUR ?? 8;
  WFRT.PIXELS_PER_HOUR = WFRT.PIXELS_PER_HOUR ?? 90;

  WFRT.allEmployees = Array.isArray(WFRT.allEmployees) ? WFRT.allEmployees : [];
  WFRT.assetInventory = Array.isArray(WFRT.assetInventory) ? WFRT.assetInventory : [];

  WFRT.state = WFRT.state || {
    checklistTasks: [],
    planTasks: [],
    currentTasks: [],
    visibleEmployeeIds: [],
    unplannedAppointments: [],
    historyLog: [],
    activeTaskId: null,

    // wizard
    tempWizardSelectedActivityIds: [],
    lastBootstrapPhaseItems: [],
    hideChecklistSource: true,
    wizardUi: { query: "", collapsed: { planned: {}, remaining: {} } },
  };

  WFRT.setActiveTaskId =
    WFRT.setActiveTaskId || ((id) => (WFRT.state.activeTaskId = id ? String(id) : null));
  WFRT.setVisibleEmployeeIds =
    WFRT.setVisibleEmployeeIds ||
    ((ids) => (WFRT.state.visibleEmployeeIds = Array.isArray(ids) ? ids : []));
  WFRT.setUnplannedAppointments =
    WFRT.setUnplannedAppointments ||
    ((items) => (WFRT.state.unplannedAppointments = Array.isArray(items) ? items : []));

  /* =========================================================================
   * Blade API (routes)
   * ========================================================================= */
  window.WF_API = window.WF_API || {
    customers: @json(route('wf.customers')),
    customerProjects: (customerId) =>
      @json(url('/admin/workforce-planner/customers')) + "/" + encodeURIComponent(customerId) + "/projects",
    checklist: @json(route('wf.checklist')),
    planGet: @json(route('wf.plan.get')),
    bootstrap: @json(route('wf.bootstrap')),
    planSave: @json(route('wf.plan.save')),
    employees: @json(route('wf.employees')),
    assets: @json(route('wf.assets')),
  };

  /* =========================================================================
   * Selector state (global WF)
   * ========================================================================= */
  window.WF = window.WF || {};
  const WF = window.WF;
  WF.state = WF.state || { customerId: null, projectId: null, planId: null };

  const CSRF =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  /* =========================================================================
   * Utils
   * ========================================================================= */
  const isNonEmpty = (v) => v !== undefined && v !== null && String(v).trim().length > 0;

  function debounce(fn, ms = 250) {
    let t = null;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

  async function httpGet(url, params = {}) {
    const u = new URL(url, window.location.origin);
    Object.entries(params || {}).forEach(([k, v]) => {
      if (isNonEmpty(v)) u.searchParams.set(k, String(v));
    });

    const res = await fetch(u.toString(), {
      method: "GET",
      headers: { Accept: "application/json" },
      credentials: "same-origin",
    });

    if (!res.ok) throw new Error(`GET ${u.pathname} failed (${res.status})`);
    return res.json();
  }

  function normalizeArrayPayload(payload, keys = ["items", "data"]) {
    if (Array.isArray(payload)) return payload;
    if (!payload) return [];
    for (const k of keys) if (Array.isArray(payload?.[k])) return payload[k];
    return [];
  }

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function slugKey(str) {
    return String(str || "")
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, "_")
      .replace(/^_+|_+$/g, "");
  }

  function stageValue() {
    return $("stage-selector")?.value || "montage";
  }

  function minutesToHours(min) {
    const m = Number(min || 0);
    return Math.max(0.25, Math.round((m / 60) * 4) / 4);
  }

  function formatMinutes(min) {
    const m = Math.max(0, parseInt(min || 0, 10));
    const h = Math.floor(m / 60);
    const r = m % 60;
    if (h <= 0) return `${r}m`;
    if (r === 0) return `${h}h`;
    return `${h}h ${r}m`;
  }

  function nowLabel() {
    return "Just now";
  }

  function cryptoRandomId(prefix = "tmp") {
    try {
      if (window.crypto?.getRandomValues) {
        const a = new Uint32Array(3);
        window.crypto.getRandomValues(a);
        return `${prefix}_${Date.now()}_${[...a].map((n) => n.toString(16)).join("")}`;
      }
    } catch (_) {}
    return `${prefix}_${Date.now()}_${Math.random().toString(16).slice(2)}`;
  }

  /* =========================================================================
   * Merge logic (plan overrides checklist by meta.source_type/source_id)
   * ========================================================================= */
  const taskKey = (t) => {
    const st = t?.meta?.source_type;
    const sid = t?.meta?.source_id;
    if (st && sid !== undefined && sid !== null) return `${st}:${sid}`;
    return String(t?.id ?? "");
  };

  function mergeTasks(base, incoming, preferIncoming = true) {
    const map = new Map();
    (Array.isArray(base) ? base : []).forEach((t) => map.set(taskKey(t), t));
    (Array.isArray(incoming) ? incoming : []).forEach((t) => {
      const k = taskKey(t);
      if (!map.has(k)) map.set(k, t);
      else if (preferIncoming) map.set(k, { ...map.get(k), ...t });
    });
    return [...map.values()];
  }

  function recomputeCurrentTasks() {
    WFRT.state.currentTasks = mergeTasks(WFRT.state.checklistTasks, WFRT.state.planTasks, true);
  }

  /* =========================================================================
   * Customer dropdown
   * ========================================================================= */
  function showCustomerDropdown() {
    $("customer-dropdown-list")?.classList.remove("hidden");
    $("customer-chevron")?.classList.add("rotate-180");
  }

  function hideCustomerDropdown() {
    $("customer-dropdown-list")?.classList.add("hidden");
    $("customer-chevron")?.classList.remove("rotate-180");
  }

  function renderCustomerDropdown(items) {
    const list = $("customer-dropdown-list");
    if (!list) return;

    list.innerHTML = "";
    if (!items.length) {
      list.innerHTML = `<div class="px-4 py-3 text-sm text-slate-400">No customers found</div>`;
      return;
    }

    items.forEach((c) => {
      const id = c.id ?? c.customer_id;
      const name = c.name ?? c.display_name ?? c.title ?? c.firma ?? `#${id}`;

      const row = document.createElement("div");
      row.className =
        "px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 flex flex-col";
      row.innerHTML = `<span class="font-bold">${escapeHtml(name)}</span>`;
      row.onclick = () => WF.selectCustomer(String(id), String(name));
      list.appendChild(row);
    });
  }

  async function loadCustomers(term = "") {
    const payload = await httpGet(window.WF_API.customers, { q: term });
    renderCustomerDropdown(normalizeArrayPayload(payload, ["items", "data"]));
  }

  async function loadProjects(customerId) {
    const select = $("project-selector");
    if (!select) return;

    select.innerHTML = `<option value="">Select Product &amp; Site...</option>`;
    select.disabled = true;

    if (!customerId) return;

    const payload = await httpGet(window.WF_API.customerProjects(customerId));
    const projects = normalizeArrayPayload(payload, ["items", "data", "projects"]);

    projects.forEach((p) => {
      const id = p.id ?? p.project_id;
      const product = p.product ?? p.product_name ?? p.article ?? p.title ?? "Product";
      const object = p.object ?? p.object_name ?? p.site ?? "";
      const address = p.address ?? p.full_address ?? "";

      const label = `${product}${object ? " - " + object : ""}${address ? " (" + address + ")" : ""}`;
      const opt = document.createElement("option");
      opt.value = String(id);
      opt.textContent = label;
      select.appendChild(opt);
    });

    select.disabled = false;

    if (projects.length) {
      select.selectedIndex = 1;
      WF.state.projectId = String(select.value);
      await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);
    } else {
      WF.state.projectId = null;
      WF.state.planId = null;
      WFRT.state.planTasks = [];
      WFRT.state.checklistTasks = [];
      WFRT.state.lastBootstrapPhaseItems = [];
      recomputeCurrentTasks();
      renderAllViews();
      updateUnplannedCount();
      renderWizardFromBootstrap([]);
    }
  }

  WF.selectCustomer = async (id, name) => {
    const input = $("customer-search-input");
    if (input) {
      input.value = name;
      input.setAttribute("data-selected-id", String(id));
    }
    WF.state.customerId = String(id);
    hideCustomerDropdown();
    await loadProjects(WF.state.customerId);
  };

  function bindCustomerDropdown() {
    const input = $("customer-search-input");
    if (!input) return;

    input.addEventListener("focus", () => {
      showCustomerDropdown();
      loadCustomers(input.value.trim()).catch(() => {});
    });

    input.addEventListener(
      "input",
      debounce(() => {
        showCustomerDropdown();
        loadCustomers(input.value.trim()).catch(() => {});
      }, 200)
    );

    document.addEventListener("click", (e) => {
      const box = $("customer-select-container");
      if (box && !box.contains(e.target)) hideCustomerDropdown();
    });
  }

  function bindProjectSelector() {
    const sel = $("project-selector");
    if (!sel) return;
    sel.addEventListener("change", async () => {
      WF.state.projectId = sel.value ? String(sel.value) : null;
      await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);
    });
  }

  /* =========================================================================
   * Bootstrap -> Checklist task mapping
   * ========================================================================= */
  function mapBootstrapActivityToChecklistTask(row) {
    const id = row?.id ?? 0;
    const isDone = !!row?.is_done;
    const isPlanned = !!row?.is_planned;

    return {
      id: `pa_${id}`,
      title: row?.title || "Task",
      duration: minutesToHours(row?.duration_minutes ?? 60),
      duration_minutes: parseInt(row?.duration_minutes ?? 60, 10),
      category: row?.section_name || "General",
      phase_id: row?.phase_id ?? null,
      phase_name: row?.phase_name || "",
      section_id: row?.section_id ?? null,
      section_name: row?.section_name || "General",
      status: isDone ? "done" : "open",
      assignees: [],
      startHour: 0,
      predecessors: [],
      assets: [],
      subtasks: [],
      description: row?.description || row?.notes || "",
      is_done: isDone,
      is_planned: isPlanned,
      sort_order: row?.sort_order ?? row?.sortOrder ?? null,
      meta: {
        source_type: "phase_activity",
        source_id: id,
        phase_id: row?.phase_id ?? null,
        section_id: row?.section_id ?? null,
        stage: stageValue(),
      },
    };
  }

    WF.loadBootstrapChecklist = async function loadBootstrapChecklist() {
        const customerId = WF.state.customerId;
        const projectId  = WF.state.projectId;
        const stage      = stageValue();

        if (!customerId || !projectId) {
            WFRT.state.checklistTasks = [];
            WFRT.state.unplannedAppointments = [];
            WFRT.state.lastBootstrapPhaseItems = [];
            recomputeCurrentTasks();
            renderAllViews();
            updateUnplannedCount();
            renderWizardFromBootstrap([]); 
            if (!$("plan-wizard-modal")?.classList.contains("hidden")) {
            renderWizardAppointmentsList();
            }

            return;
        }

        const payload = await httpGet(window.WF_API.bootstrap, {
            customer_id: customerId,
            project_id: projectId,
            stage,
        });

        // If your bootstrap endpoint returns the active draft plan, capture it here
        if (payload?.plan?.id) {
            WF.state.planId = String(payload.plan.id);
        }

        const phaseItems = normalizeArrayPayload(payload?.phase, ["items"]);
        WFRT.state.lastBootstrapPhaseItems = Array.isArray(phaseItems) ? phaseItems : [];

        const checklistFlat = normalizeArrayPayload(payload, ["checklist", "items", "data"]);
        WFRT.state.checklistTasks = (checklistFlat || []).map(mapBootstrapActivityToChecklistTask);

        const unplanned = normalizeArrayPayload(payload, ["unplanned_appointments", "unplannedAppointments"]);
        WFRT.setUnplannedAppointments(unplanned);

        recomputeCurrentTasks();
        renderAllViews();
        updateUnplannedCount();
        renderWizardFromBootstrap(WFRT.state.lastBootstrapPhaseItems);
    };

  /* =========================================================================
   * Plan loader (planner items)
   * ========================================================================= */
  async function loadEmployees(term = "") {
    const payload = await httpGet(window.WF_API.employees, { q: term });
    WFRT.allEmployees = normalizeArrayPayload(payload, ["items", "data"]);
  }

  async function loadAssets(term = "") {
    const payload = await httpGet(window.WF_API.assets, { q: term });
    WFRT.assetInventory = normalizeArrayPayload(payload, ["items", "data"]);
  }

  function mapPlanItemToUiTask(it) {
    const sourceType = it?.source_type ?? it?.sourceType ?? it?.meta?.source_type ?? null;
    const sourceId = it?.source_id ?? it?.sourceId ?? it?.meta?.source_id ?? null;

    return {
      id: String(it?.id ?? it?.item_id ?? it?.uuid ?? cryptoRandomId("task")),
      title: it?.title ?? it?.name ?? it?.label ?? "Task",
      duration: minutesToHours(it?.duration_minutes ?? it?.duration ?? it?.duration_hours ?? it?.estimated_hours ?? 60),
      category: it?.category ?? it?.type ?? "General",
      status: it?.status ?? "scheduled",
      assignees: Array.isArray(it?.employees)
        ? it.employees.map((e) => e.id)
        : Array.isArray(it?.assignees)
          ? it.assignees
          : Array.isArray(it?.crew_ids)
            ? it.crew_ids
            : [],
      startHour: Number(it?.start_hour ?? it?.startHour ?? 0),
      predecessors: Array.isArray(it?.dependencies)
        ? it.dependencies.map((d) => d.id)
        : Array.isArray(it?.predecessors)
          ? it.predecessors
          : [],
      assets: Array.isArray(it?.assets) ? it.assets : [],
      subtasks: Array.isArray(it?.subtasks) ? it.subtasks : [],
      description: it?.description ?? "",
      startDate: it?.planned_start_at ?? it?.start_date ?? it?.startDate,
      dueDate: it?.planned_end_at ?? it?.due_date ?? it?.dueDate,
      travelTime: it?.travel_time ?? it?.travelTime,
      arrivalTime: it?.arrival_time ?? it?.arrivalTime,
      origin: it?.origin,
      meta: it?.meta || (sourceType && sourceId !== undefined && sourceId !== null
        ? { source_type: sourceType, source_id: sourceId }
        : null),
    };
  }

  WF.loadPlan = async function loadPlanOnly() {
    const customerId = WF.state.customerId;
    const projectId = WF.state.projectId;
    const stage = stageValue();

    if (!customerId || !projectId) {
      WF.state.planId = null;
      WFRT.state.planTasks = [];
      WFRT.setVisibleEmployeeIds([]);
      recomputeCurrentTasks();
      renderActiveCrewWidget();
      renderAllViews();
      return;
    }

    const planPayload = await httpGet(window.WF_API.planGet, {
      customer_id: customerId,
      project_id: projectId,
      stage,
      status: "draft",
    }).catch(() => null);

    const plan = planPayload?.plan || null;
    const planItems = normalizeArrayPayload(planPayload, ["items", "plan_items", "data"]);

    WF.state.planId = plan?.id ? String(plan.id) : null;
    WFRT.state.planTasks = planItems.map(mapPlanItemToUiTask);

    const crewIds = plan?.meta?.crew_ids || plan?.meta?.crewIds || [];
    WFRT.setVisibleEmployeeIds(Array.isArray(crewIds) ? crewIds : []);

    recomputeCurrentTasks();
    renderActiveCrewWidget();
    renderAllViews();
  };

  /* =========================================================================
   * Left tab switch
   * ========================================================================= */
  window.switchLeftTab = function switchLeftTab(tab) {
    $("left-tab-backlog")?.classList.add("hidden");
    $("left-tab-unplanned")?.classList.add("hidden");

    const backBtn = $("left-btn-backlog");
    const unplBtn = $("left-btn-unplanned");

    if (backBtn)
      backBtn.className =
        "flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all";
    if (unplBtn)
      unplBtn.className =
        "flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all";

    if (tab === "unplanned") {
      $("left-tab-unplanned")?.classList.remove("hidden");
      if (unplBtn)
        unplBtn.className =
          "flex-1 py-2 rounded-lg text-sm font-bold bg-white text-slate-800 shadow-sm transition-all";
      renderUnplannedPanel();
    } else {
      $("left-tab-backlog")?.classList.remove("hidden");
      if (backBtn)
        backBtn.className =
          "flex-1 py-2 rounded-lg text-sm font-bold bg-white text-slate-800 shadow-sm transition-all";
      renderChecklist();
    }
  };

  /* =========================================================================
   * Rendering helpers
   * ========================================================================= */
  const START_HOUR = WFRT.START_HOUR;
  const PIXELS_PER_HOUR = WFRT.PIXELS_PER_HOUR;

  function getAvatarStack(assigneeIds) {
    const allEmployees = WFRT.allEmployees || [];
    if (!assigneeIds || assigneeIds.length === 0) return "";
    let html = '<div class="flex -space-x-2">';
    assigneeIds.forEach((id) => {
      const emp = allEmployees.find((e) => String(e.id) === String(id));
      if (emp?.avatar) {
        html += `<img src="${emp.avatar}" class="w-6 h-6 rounded-full border-2 border-white" title="${escapeHtml(emp.name)}">`;
      }
    });
    html += "</div>";
    return html;
  }

  function getStatusBadge(status, reason) {
    if (status === "scheduled")
      return '<span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold">Scheduled</span>';
    if (status === "in-progress")
      return '<span class="bg-green-100 text-green-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold animate-pulse">In Progress</span>';
    if (status === "paused")
      return `<span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold" title="${escapeHtml(reason || "")}">Paused</span>`;
    if (status === "done" || status === "completed" || status === "finished")
      return '<span class="bg-slate-200 text-slate-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold">Done</span>';
    if (status === "open")
      return '<span class="bg-slate-100 text-slate-500 text-[10px] px-2 py-1 rounded-full uppercase font-bold">Open</span>';
    return "";
  }

  function assetLabel(a) {
    const item = String(a?.item ?? "").trim();
    const model = String(a?.model ?? "").trim();
    return model ? `${item} — ${model}` : item;
  }

  function renderAssetsGrid(hostEl, assets, selectedIds, onToggle) {
    if (!hostEl) return;

    const selected = new Set((selectedIds || []).map(String));
    hostEl.innerHTML = "";

    if (!assets?.length) {
      hostEl.innerHTML = `<div class="col-span-2 text-xs text-slate-400 italic p-2">No assets found.</div>`;
      return;
    }

    assets.forEach((a) => {
      const id = String(a.id);
      const checked = selected.has(id);

      const el = document.createElement("label");
      el.className =
        "flex items-center gap-2 p-2 rounded-lg border cursor-pointer hover:bg-slate-50 " +
        (checked ? "bg-blue-50 border-blue-200" : "border-slate-200");

      el.innerHTML = `
        <input type="checkbox" class="mt-0.5" ${checked ? "checked" : ""} />
        ${a.image ? `<img src="${a.image}" class="w-8 h-8 rounded-md border border-slate-200 object-cover">` : ""}
        <div class="min-w-0">
          <div class="text-xs font-bold text-slate-700 truncate" title="${escapeHtml(assetLabel(a))}">
            ${escapeHtml(assetLabel(a))}
          </div>
        </div>
      `;

      el.querySelector("input").addEventListener("change", (e) => onToggle(id, e.target.checked));
      hostEl.appendChild(el);
    });
  }

  function filterAssetsByQuery(q) {
    const term = String(q || "").trim().toLowerCase();
    const all = WFRT.assetInventory || [];
    if (!term) return all;
    return all.filter((a) => (`${a.item || ""} ${a.model || ""}`).toLowerCase().includes(term));
  }

    /* =========================================================================
   * Wizard: Bulk Assignment (PM + Crew + Assets)
   * ========================================================================= */
  WFRT.state.wizardAssign = WFRT.state.wizardAssign || {
    pm_id: "",
    crew_ids: [],
    asset_ids: [],
  };

  let __wizardBound = false;

  async function ensureEmployeesAndAssetsLoaded() {
    const needEmp = !Array.isArray(WFRT.allEmployees) || WFRT.allEmployees.length === 0;
    const needAst = !Array.isArray(WFRT.assetInventory) || WFRT.assetInventory.length === 0;

    const jobs = [];
    if (needEmp) jobs.push(loadEmployees(""));
    if (needAst) jobs.push(loadAssets(""));

    if (jobs.length) await Promise.allSettled(jobs);
  }

  function renderWizardPmSelect() {
    const sel = $("wizard-pm-select");
    if (!sel) return;

    const employees = WFRT.allEmployees || [];
    const current = String(WFRT.state.wizardAssign.pm_id || "");

    sel.innerHTML = `<option value="">Select Lead...</option>`;
    employees.forEach((e) => {
      const opt = document.createElement("option");
      opt.value = String(e.id);
      opt.textContent = e.name || (`#${e.id}`);
      if (String(e.id) === current) opt.selected = true;
      sel.appendChild(opt);
    });
  }

  function renderWizardCrewSelect() {
    const host = $("wizard-crew-select");
    if (!host) return;

    const employees = WFRT.allEmployees || [];
    const selected = new Set((WFRT.state.wizardAssign.crew_ids || []).map(String));

    host.innerHTML = "";

    employees.forEach((e) => {
      const id = String(e.id);
      const checked = selected.has(id);

      const label = document.createElement("label");
      label.className =
        "flex items-center gap-2 px-2 py-1.5 rounded-lg border cursor-pointer select-none " +
        (checked ? "bg-blue-50 border-blue-200" : "bg-white border-slate-200 hover:bg-slate-50");

      label.innerHTML = `
        <input type="checkbox" class="mt-0.5" ${checked ? "checked" : ""} />
        <img src="${e.avatar || ""}" class="w-6 h-6 rounded-full border border-slate-200 object-cover" onerror="this.style.display='none'">
        <span class="text-xs font-bold text-slate-700 truncate max-w-[140px]" title="${escapeHtml(e.name || "")}">
          ${escapeHtml(e.name || `#${e.id}`)}
        </span>
      `;

      label.querySelector("input").addEventListener("change", (ev) => {
        const isOn = !!ev.target.checked;
        const cur = new Set((WFRT.state.wizardAssign.crew_ids || []).map(String));
        if (isOn) cur.add(id);
        else cur.delete(id);
        WFRT.state.wizardAssign.crew_ids = [...cur];
      });

      host.appendChild(label);
    });
  }

  function renderWizardAssetsPicker() {
    const host = $("wizard-project-assets");
    const input = $("wizard-assets-search");
    if (!host || !input) return;

    const q = String(input.value || "");
    const filtered = filterAssetsByQuery(q);

    renderAssetsGrid(
      host,
      filtered,
      WFRT.state.wizardAssign.asset_ids || [],
      (id, checked) => {
        const cur = new Set((WFRT.state.wizardAssign.asset_ids || []).map(String));
        if (checked) cur.add(String(id));
        else cur.delete(String(id));
        WFRT.state.wizardAssign.asset_ids = [...cur];
      }
    );
  }

  function bindWizardBulkAssignmentOnce() {
    if (__wizardBound) return;
    __wizardBound = true;

    $("wizard-pm-select")?.addEventListener("change", (e) => {
      WFRT.state.wizardAssign.pm_id = String(e.target.value || "");
    });

    $("wizard-assets-search")?.addEventListener(
      "input",
      debounce(() => renderWizardAssetsPicker(), 120)
    );
  }

  async function renderWizardBulkAssignmentUI() {
    await ensureEmployeesAndAssetsLoaded();
    bindWizardBulkAssignmentOnce();
    renderWizardPmSelect();
    renderWizardCrewSelect();
    renderWizardAssetsPicker();
  }

  
  /* =========================================================================
   * Wizard: Appointments (render + resolve)
   * ========================================================================= */
  WFRT.state.wizardAppt = WFRT.state.wizardAppt || {
    selected_id: null,
    pm_id: "",
    crew_ids: [],
    asset_ids: [],
    resolve_type: "link", // link|manual
    checklist_activity_id: "",
    manual: { title: "", category: "General", duration_minutes: 60, desc: "" },
  };

  function apptTimeLabel(appt) {
    return String(appt?.start_at || appt?.start || appt?.date || appt?.planned_start_at || "").trim();
  }

  function apptCrewIds(appt) {
    const raw =
      appt?.crew_ids ||
      appt?.employee_ids ||
      appt?.employees ||
      appt?.crew ||
      appt?.crewIds ||
      [];
    if (!Array.isArray(raw)) return [];
    return raw.map((x) => (typeof x === "object" ? x?.id : x)).filter(Boolean).map((v) => String(v));
  }

  function renderApptPmSelect() {
    const sel = $("appt-pm-select");
    if (!sel) return;

    const employees = WFRT.allEmployees || [];
    const current = String(WFRT.state.wizardAppt.pm_id || "");

    sel.innerHTML = `<option value="">Select Lead...</option>`;
    employees.forEach((e) => {
      const opt = document.createElement("option");
      opt.value = String(e.id);
      opt.textContent = e.name || `#${e.id}`;
      if (String(e.id) === current) opt.selected = true;
      sel.appendChild(opt);
    });

    sel.onchange = (e) => {
      WFRT.state.wizardAppt.pm_id = String(e.target.value || "");
    };
  }

  function renderApptCrewSelect() {
    const host = $("appt-crew-select");
    if (!host) return;

    const employees = WFRT.allEmployees || [];
    const selected = new Set((WFRT.state.wizardAppt.crew_ids || []).map(String));

    host.innerHTML = "";
    employees.forEach((e) => {
      const id = String(e.id);
      const checked = selected.has(id);

      const label = document.createElement("label");
      label.className =
        "flex items-center gap-2 px-2 py-1.5 rounded-lg border cursor-pointer select-none " +
        (checked ? "bg-blue-50 border-blue-200" : "bg-white border-slate-200 hover:bg-slate-50");

      label.innerHTML = `
        <input type="checkbox" class="mt-0.5" ${checked ? "checked" : ""}/>
        <img src="${e.avatar || ""}" class="w-6 h-6 rounded-full border border-slate-200 object-cover" onerror="this.style.display='none'">
        <span class="text-xs font-bold text-slate-700 truncate max-w-[160px]">${escapeHtml(e.name || `#${e.id}`)}</span>
      `;

      label.querySelector("input").addEventListener("change", (ev) => {
        const on = !!ev.target.checked;
        const cur = new Set((WFRT.state.wizardAppt.crew_ids || []).map(String));
        if (on) cur.add(id);
        else cur.delete(id);
        WFRT.state.wizardAppt.crew_ids = [...cur];
      });

      host.appendChild(label);
    });
  }

  function renderApptChecklistSelect() {
    const sel = $("appt-checklist-select");
    if (!sel) return;

    const items = Array.isArray(WFRT.state.lastBootstrapPhaseItems) ? WFRT.state.lastBootstrapPhaseItems : [];
    const current = String(WFRT.state.wizardAppt.checklist_activity_id || "");

    sel.innerHTML = "";
    if (!items.length) {
      sel.innerHTML = `<option value="">No checklist available</option>`;
      return;
    }

    sel.innerHTML = `<option value="">Select checklist item...</option>`;
    items.forEach((it) => {
      const opt = document.createElement("option");
      opt.value = String(it.id);
      opt.textContent = `${it.phase_name ? it.phase_name + " · " : ""}${it.title || "Task"} (${formatMinutes(it.duration_minutes || 60)})`;
      if (String(it.id) === current) opt.selected = true;
      sel.appendChild(opt);
    });

    sel.onchange = (e) => {
      WFRT.state.wizardAppt.checklist_activity_id = String(e.target.value || "");
    };
  }

  function renderApptAssetsPicker() {
    const host = $("wizard-appt-assets");
    const input = $("wizard-appt-assets-search");
    if (!host || !input) return;

    const filtered = filterAssetsByQuery(String(input.value || ""));
    renderAssetsGrid(host, filtered, WFRT.state.wizardAppt.asset_ids || [], (id, checked) => {
      const cur = new Set((WFRT.state.wizardAppt.asset_ids || []).map(String));
      if (checked) cur.add(String(id));
      else cur.delete(String(id));
      WFRT.state.wizardAppt.asset_ids = [...cur];
    });
  }

  function bindApptAssetsSearchOnce() {
    if (WFRT.__apptAssetsBound) return;
    WFRT.__apptAssetsBound = true;

    $("wizard-appt-assets-search")?.addEventListener(
      "input",
      debounce(() => renderApptAssetsPicker(), 120)
    );
  }

  window.__wfCancelApptResolve = function __wfCancelApptResolve() {
    WFRT.state.wizardAppt.selected_id = null;
    $("wizard-appointment-resolution")?.classList.add("hidden");
  };

  window.__wfSelectAppointment = async function __wfSelectAppointment(apptId) {
    await ensureEmployeesAndAssetsLoaded();

    const appt = (WFRT.state.unplannedAppointments || []).find((a) => String(a.id) === String(apptId));
    if (!appt) return;

    WFRT.state.wizardAppt.selected_id = String(appt.id);

    // prefill crew from appointment
    const crew = apptCrewIds(appt);
    WFRT.state.wizardAppt.crew_ids = crew;

    // default PM (keep if already selected)
    if (!WFRT.state.wizardAppt.pm_id) WFRT.state.wizardAppt.pm_id = "";

    // header meta
    if ($("wizard-selected-appt-title")) $("wizard-selected-appt-title").innerText = appt.title || "Appointment";
    if ($("wizard-selected-appt-time")) $("wizard-selected-appt-time").innerText = apptTimeLabel(appt) || "—";

    renderApptPmSelect();
    renderApptCrewSelect();
    renderApptChecklistSelect();

    bindApptAssetsSearchOnce();
    renderApptAssetsPicker();

    $("wizard-appointment-resolution")?.classList.remove("hidden");
  };

  window.__wfSaveApptResolve = function __wfSaveApptResolve() {
    const apptId = String(WFRT.state.wizardAppt.selected_id || "");
    if (!apptId) return;

    const appt = (WFRT.state.unplannedAppointments || []).find((a) => String(a.id) === apptId);
    if (!appt) return;

    const pmId = String(WFRT.state.wizardAppt.pm_id || "").trim();
    const crewIds = (WFRT.state.wizardAppt.crew_ids || []).map(String);
    const assetIds = (WFRT.state.wizardAppt.asset_ids || []).map(String);

    const employeeIds = [...new Set([pmId, ...crewIds].filter((v) => v && v !== "0"))]
      .map((v) => parseInt(v, 10))
      .filter((n) => Number.isFinite(n) && n > 0);

    // Build task from chosen resolve type
    const resolveType =
      document.querySelector('input[name="appt-resolve-type"]:checked')?.value || "link";

    let title = appt.title || "Appointment";
    let category = "General";
    let minutes = 60;
    let desc = appt.description || "";

    if (resolveType === "link") {
      const actId = String(WFRT.state.wizardAppt.checklist_activity_id || "");
      const it = (WFRT.state.lastBootstrapPhaseItems || []).find((x) => String(x.id) === actId);
      if (!it) {
        window.showToast?.("Select a checklist item first.");
        return;
      }
      title = it.title || title;
      category = it.section_name || "General";
      minutes = parseInt(it.duration_minutes || 60, 10);
      desc = it.description || desc;
    } else {
      title = $("appt-manual-title")?.value?.trim() || title;
      category = $("appt-manual-category")?.value || "General";
      minutes = parseInt($("appt-manual-duration")?.value || "60", 10);
      desc = $("appt-manual-desc")?.value?.trim() || desc;
    }

    const newTask = {
      id: `plan_appointment_${apptId}_${Date.now()}`,
      title,
      duration: minutesToHours(minutes),
      duration_minutes: minutes,
      category,
      status: "scheduled",
      lead_id: pmId ? parseInt(pmId,10) : null,
      crew_ids: employeeIds.map(n => parseInt(n,10)),
      assignees: employeeIds.map(n => parseInt(n,10)), // only for avatars if you keep it

      startHour: 0,
      predecessors: [],
      asset_ids: assetIds.map((v) => parseInt(v, 10)).filter((n) => Number.isFinite(n) && n > 0),
      description: desc,
      startDate: appt.start_at || appt.start || appt.date || null,
      meta: { source_type: "appointment", source_id: parseInt(apptId, 10) },
    };

    WFRT.state.planTasks = mergeTasks(WFRT.state.planTasks, [newTask], true);

    // remove from unplanned list locally
    WFRT.state.unplannedAppointments = (WFRT.state.unplannedAppointments || []).filter(
      (a) => String(a.id) !== apptId
    );

    WFRT.state.historyLog.unshift({
      date: nowLabel(),
      text: `Appointment converted to plan item: "${title}".`,
      type: "success",
    });

    renderAllViews();
    renderWizardAppointmentsList();
    window.__wfCancelApptResolve();
    window.showToast?.("Appointment converted (draft).");
  };

  function renderWizardAppointmentsList() {
    const host = $("wizard-appointments-list");
    if (!host) return;

    const items = Array.isArray(WFRT.state.unplannedAppointments) ? WFRT.state.unplannedAppointments : [];
    host.innerHTML = "";

    if (!items.length) {
      host.innerHTML = `<div class="text-sm text-slate-400 italic p-3">No unplanned appointments.</div>`;
      return;
    }

    items.forEach((appt) => {
      const el = document.createElement("div");
      el.className = "bg-white border border-slate-200 rounded-2xl p-4 hover:shadow-sm transition-all";

      el.innerHTML = `
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="font-extrabold text-slate-800 truncate">${escapeHtml(appt.title || "Appointment")}</div>
            <div class="text-xs text-slate-500 mt-1">
              <i class="fa-regular fa-clock mr-1"></i>${escapeHtml(apptTimeLabel(appt) || "—")}
            </div>
            ${
              appt.description
                ? `<div class="text-xs text-slate-400 mt-2 line-clamp-2">${escapeHtml(appt.description)}</div>`
                : ""
            }
          </div>

          <button type="button"
            class="shrink-0 px-3 py-2 rounded-xl bg-brandDark text-white text-xs font-bold hover:bg-blue-800"
            onclick="window.__wfSelectAppointment('${escapeHtml(String(appt.id))}')"
          >
            Resolve
          </button>
        </div>
      `;

      host.appendChild(el);
    });
  }



  function renderTaskCard(task, isBoard = true) {
    const div = document.createElement("div");
    const title = String(task?.title ?? "Task");
    const category = String(task?.category ?? "General");
    const duration = task?.duration ?? 1;

    const isDone = !!(
      task?.is_done ||
      task?.status === "done" ||
      task?.status === "completed" ||
      task?.status === "finished"
    );

    if (!isBoard) {
      div.className = isDone
        ? "glass-card p-3 rounded-xl flex items-center justify-between opacity-60 cursor-not-allowed"
        : "glass-card p-3 rounded-xl flex items-center justify-between cursor-grab group active:cursor-grabbing hover:border-sky/50";
    } else {
      div.className = isDone
        ? "bg-white shadow-md border-l-4 border-l-slate-300 p-3 rounded-xl mb-3 relative opacity-70 cursor-pointer group"
        : "bg-white shadow-md border-l-4 border-l-actionGreen p-3 rounded-xl mb-3 cursor-grab relative group";
    }

    div.setAttribute("data-id", String(task?.id ?? ""));
    div.setAttribute("data-title", title.toLowerCase());
    div.setAttribute("data-done", isDone ? "1" : "0");
    div.addEventListener("click", () => window.openModal?.(String(task?.id ?? "")));

    let catColor = "bg-slate-200 text-slate-600";
    if (category === "Electric") catColor = "bg-brandDark/10 text-brandDark";
    if (category === "Roof") catColor = "bg-sky/20 text-sky-700";

    const avatarHtml = task?.assignees?.length ? getAvatarStack(task.assignees) : "";
    const statusHtml = getStatusBadge(task?.status, task?.pauseReason);

    if (!isBoard) {
      div.innerHTML = `
        <div class="flex items-center gap-3 w-full">
          <div class="text-slate-300 group-hover:text-brandDark transition-colors drag-handle ${isDone ? "pointer-events-none" : ""}">
            <i class="fa-solid fa-grip-vertical"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <h4 class="font-semibold text-slate-700 text-sm leading-tight ${isDone ? "line-through decoration-slate-400 decoration-2" : ""}">
                ${escapeHtml(title)}
              </h4>
              ${statusHtml}
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[10px] font-bold ${catColor} px-1.5 py-0.5 rounded">${escapeHtml(category)}</span>
              <span class="text-[10px] text-slate-400 font-medium">${escapeHtml(duration)}h</span>
              ${
                task?.phase_name
                  ? `<span class="text-[10px] bg-white border border-slate-200 text-slate-500 px-1.5 py-0.5 rounded truncate max-w-[120px]" title="${escapeHtml(task.phase_name)}">${escapeHtml(task.phase_name)}</span>`
                  : ""
              }
            </div>
          </div>
        </div>`;
    } else {
      div.innerHTML = `
        <div class="flex justify-between items-start mb-1">
          <div class="flex-1 min-w-0 pr-2">
            <h4 class="font-bold text-slate-700 text-sm leading-tight truncate ${isDone ? "line-through decoration-slate-400 decoration-2" : ""}">
              ${escapeHtml(title)}
            </h4>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[10px] font-bold ${catColor} px-1.5 py-0.5 rounded">${escapeHtml(category)}</span>
              <span class="text-[10px] text-slate-400 font-medium">
                <i class="fa-regular fa-clock mr-1"></i>${escapeHtml(duration)}h
              </span>
            </div>
          </div>
          ${avatarHtml}
        </div>
        <div class="mt-3 flex justify-between items-center border-t border-slate-100 pt-2">
          ${statusHtml}
          <span class="text-[10px] text-slate-400">${task?.phase_name ? escapeHtml(task.phase_name) : ""}</span>
        </div>`;
    }

    return div;
  }

  /* =========================================================================
   * Checklist rendering
   * ========================================================================= */
  function renderChecklist() {
    const host = $("checklist-source");
    if (!host) return;

    if (WFRT.state.hideChecklistSource) {
      host.innerHTML = `
        <div class="text-sm text-slate-400 italic p-3">
          Checklist is hidden here. Use <b>Create Plan</b> wizard to pick tasks.
        </div>`;
      updateTaskCount();
      initSortables();
      return;
    }

    host.innerHTML = "";
    const tasks = WFRT.state.checklistTasks || [];
    const openOrDone = tasks.filter((t) => t.status === "open" || t.status === "done");

    if (!openOrDone.length) {
      host.innerHTML = `<div class="text-sm text-slate-400 italic p-3">No checklist items for this stage/product.</div>`;
      updateTaskCount();
      initSortables();
      return;
    }

    const map = new Map();
    openOrDone.forEach((t) => {
      const key = t.phase_id != null ? `p:${t.phase_id}` : `n:${slugKey(t.phase_name || "Other")}`;
      if (!map.has(key)) map.set(key, { phase_id: t.phase_id ?? null, phase_name: t.phase_name || "Other", items: [] });
      map.get(key).items.push(t);
    });

    const groups = [...map.entries()]
      .sort((A, B) => {
        const aKey = A[0], bKey = B[0];
        const aPid = aKey.startsWith("p:") ? parseInt(aKey.slice(2), 10) : NaN;
        const bPid = bKey.startsWith("p:") ? parseInt(bKey.slice(2), 10) : NaN;
        if (!Number.isNaN(aPid) && !Number.isNaN(bPid) && aPid !== bPid) return aPid - bPid;
        return A[1].phase_name.localeCompare(B[1].phase_name);
      })
      .map(([, v]) => {
        v.items.sort((a, b) => {
          const sa = String(a.section_name || a.category || "").localeCompare(String(b.section_name || b.category || ""));
          if (sa !== 0) return sa;
          const oa = a.sort_order ?? 999999;
          const ob = b.sort_order ?? 999999;
          if (oa !== ob) return oa - ob;
          return String(a.id).localeCompare(String(b.id));
        });
        return v;
      });

    groups.forEach((g) => {
      const wrap = document.createElement("div");
      wrap.className = "wf-phase-group";

      const header = document.createElement("div");
      header.className =
        "wf-phase-header sticky top-0 z-10 bg-white/70 backdrop-blur border border-slate-200 rounded-xl px-3 py-2 flex items-center justify-between";
      header.innerHTML = `
        <div class="flex items-center gap-2 min-w-0">
          <span class="w-2 h-2 rounded-full bg-brandDark"></span>
          <div class="font-extrabold text-slate-700 text-sm truncate">${escapeHtml(g.phase_name || "Phase")}</div>
        </div>
        <div class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-full">${g.items.length}</div>
      `;
      wrap.appendChild(header);

      const list = document.createElement("div");
      list.className = "wf-phase-list flex flex-col gap-3 mt-3";
      list.setAttribute("data-phase", g.phase_name || "");
      g.items.forEach((task) => list.appendChild(renderTaskCard(task, false)));

      wrap.appendChild(list);
      host.appendChild(wrap);
    });

    updateTaskCount();
    initSortables();
  }

  function renderUnplannedPanel() {
    const container = $("unplanned-source");
    if (!container) return;

    container.innerHTML = "";
    (WFRT.state.unplannedAppointments || []).forEach((appt) => {
      const el = document.createElement("div");
      el.className =
        "glass-card p-3 rounded-xl cursor-grab group active:cursor-grabbing hover:border-orange-300 border-l-4 border-l-orange-400";
      el.innerHTML = `
        <div class="flex justify-between items-start">
          <h4 class="font-bold text-slate-700 text-sm leading-tight">${escapeHtml(appt.title || "Appointment")}</h4>
          <span class="text-[10px] bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded font-bold">Unplanned</span>
        </div>
        <div class="text-xs text-slate-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> ${escapeHtml(appt.start_at || appt.date || "")}</div>
        <p class="text-xs text-slate-400 mt-2 italic line-clamp-2">${escapeHtml(appt.description || "")}</p>
      `;
      container.appendChild(el);
    });
  }

  function updateUnplannedCount() {
    const el = $("unplanned-count");
    if (el) el.innerText = String((WFRT.state.unplannedAppointments || []).length);
  }

  function updateTaskCount() {
    const count = (WFRT.state.checklistTasks || []).filter((t) => t.status === "open").length;
    const el = $("task-count");
    if (el) el.innerText = String(count);
  }

  function renderActiveCrewWidget() {
    const container = $("active-crew-avatars");
    if (!container) return;

    container.innerHTML = "";
    const ids = WFRT.state.visibleEmployeeIds || [];

    if (!ids.length) {
      container.innerHTML = '<span class="text-xs text-slate-400 italic pl-2">No crew selected</span>';
      return;
    }

    ids.forEach((id) => {
      const emp = (WFRT.allEmployees || []).find((e) => String(e.id) === String(id));
      if (!emp) return;
      const img = document.createElement("img");
      img.src = emp.avatar;
      img.className =
        "w-6 h-6 rounded-full border border-white ring-1 ring-slate-200 cursor-pointer hover:scale-110 transition-transform";
      img.title = emp.name;
      container.appendChild(img);
    });
  }

  /* =========================================================================
   * Views
   * ========================================================================= */
  function destroySortables(list) {
    (list || []).forEach((inst) => {
      try { inst.destroy(); } catch (_) {}
    });
  }

  function renderBoard() {
    const container = $("view-board");
    if (!container) return;

    container.innerHTML = "";

    WFRT._boardSortables = WFRT._boardSortables || [];
    destroySortables(WFRT._boardSortables);
    WFRT._boardSortables = [];

    const employees = WFRT.allEmployees || [];
    const visible = Array.isArray(WFRT.state.visibleEmployeeIds) ? WFRT.state.visibleEmployeeIds : [];

    visible.forEach((empId) => {
      const emp = employees.find((e) => String(e.id) === String(empId));
      if (!emp) return;

      const col = document.createElement("div");
      col.className = "glass-panel rounded-[2rem] flex flex-col h-full overflow-hidden";
      col.innerHTML = `
        <div class="p-4 bg-white/40 border-b border-white/20 flex items-center gap-3">
          <img src="${emp.avatar}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
          <div>
            <h3 class="font-bold text-slate-800 leading-tight">${escapeHtml(emp.name)}</h3>
            <p class="text-xs text-slate-500">${escapeHtml(emp.role || "")}</p>
          </div>
        </div>
        <div id="emp-col-${emp.id}" class="p-3 flex-1 overflow-y-auto space-y-3 bg-slate-50/30 min-h-[100px]"></div>
      `;
      container.appendChild(col);

      const dropZone = col.querySelector(`#emp-col-${emp.id}`);
      if (dropZone && window.Sortable) {
        const inst = Sortable.create(dropZone, {
          group: "shared",
          animation: 150,
          ghostClass: "sortable-ghost",
          onAdd: function (evt) {
            const taskId = evt.item.getAttribute("data-id");
            setTaskLead(taskId, emp.id);      // change manager/column
            setTaskStatus(taskId, "scheduled");
          },
        });
        WFRT._boardSortables.push(inst);
      }
    });

    WFRT.state.currentTasks
      .filter((t) => t.status !== "open" && t.status !== "done")
      .forEach((task) => {
        if (!task.assignees?.length) return;
        const leadId = task.lead_id;
        const zone = document.getElementById(`emp-col-${leadId}`);
        if (zone) zone.appendChild(renderTaskCard(task, true));
      });
  }

  function renderList() {
    const container = $("list-body");
    if (!container) return;

    container.innerHTML = "";
    (WFRT.state.currentTasks || []).forEach((task) => {
      const row = document.createElement("div");
      row.className =
        "grid grid-cols-12 gap-4 p-3 border-b border-slate-100 bg-white rounded-lg hover:shadow-sm transition-all items-center cursor-pointer";
      row.onclick = () => window.openModal?.(task.id);

      const statusBadge = getStatusBadge(task.status, task.pauseReason);

      let actionBtn = "";
      if (task.status === "in-progress") {
        actionBtn = `<button onclick="toggleTaskStatus('${escapeHtml(task.id)}', event)" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-pause"></i></button>`;
      } else if (task.status === "scheduled" || task.status === "paused") {
        actionBtn = `<button onclick="toggleTaskStatus('${escapeHtml(task.id)}', event)" class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-play"></i></button>`;
      }

      row.innerHTML = `
        <div class="col-span-4 flex items-center gap-3">
          <div class="w-2 h-2 rounded-full ${task.category === "Electric" ? "bg-brandDark" : "bg-sky-400"}"></div>
          <div class="min-w-0">
            <div class="font-semibold text-sm text-slate-700 truncate">${escapeHtml(task.title)}</div>
            <div class="text-xs text-slate-400 flex items-center gap-1">${getAvatarStack(task.assignees || [])}</div>
            ${task.phase_name ? `<div class="text-[10px] text-slate-400 truncate">${escapeHtml(task.phase_name)}</div>` : ""}
          </div>
        </div>
        <div class="col-span-2 text-xs text-slate-500">
          ${task.startDate ? `<div>${escapeHtml(String(task.startDate))}</div><div class="text-red-400">Due: ${escapeHtml(String(task.dueDate || ""))}</div>` : "-"}
        </div>
        <div class="col-span-3"><span class="text-xs text-slate-400">-</span></div>
        <div class="col-span-1 text-xs text-slate-500 font-mono">${escapeHtml(task.duration)}h</div>
        <div class="col-span-2 flex justify-end items-center gap-3">
          ${statusBadge}
          ${actionBtn}
        </div>`;
      container.appendChild(row);
    });
  }

  function renderCalendar() {
    const container = $("calendar-body");
    if (!container) return;

    container.innerHTML = "";
    const startDayOffset = 6;
    for (let i = 0; i < startDayOffset; i++) {
      container.innerHTML += `<div class="calendar-day bg-slate-100"></div>`;
    }
    for (let day = 1; day <= 31; day++) {
      container.innerHTML += `
        <div class="calendar-day p-2">
          <div class="text-right text-xs font-bold text-slate-400 mb-1">${day}</div>
          <div class="space-y-1"></div>
        </div>`;
    }
  }

  function renderHistory() {
    const list = $("history-list");
    if (!list) return;

    list.innerHTML = "";
    (WFRT.state.historyLog || []).forEach((log) => {
      list.innerHTML += `
        <div class="flex gap-4 p-3 rounded-xl border border-slate-100 hover:bg-white transition-colors">
          <div class="flex-1">
            <p class="text-sm font-medium text-slate-800">${escapeHtml(log.text || "")}</p>
            <span class="text-xs text-slate-400">${escapeHtml(log.date || "")}</span>
          </div>
        </div>`;
    });
  }

  function renderGantt() {
    const container = $("gantt-body");
    const timeHeader = $("time-scale");
    const svgLayer = $("gantt-lines");
    if (!container || !timeHeader || !svgLayer) return;

    container.innerHTML = "";
    container.appendChild(svgLayer);
    timeHeader.innerHTML = "";
    svgLayer.innerHTML = "";

    for (let i = START_HOUR; i <= 18; i++) {
      const marker = document.createElement("div");
      marker.className =
        "absolute top-0 bottom-0 border-l border-slate-200 pl-1 text-[10px] h-full flex items-center";
      marker.style.left = `${(i - START_HOUR) * PIXELS_PER_HOUR}px`;
      marker.innerText = `${i}:00`;
      timeHeader.appendChild(marker);
    }

    const allEmployees = WFRT.allEmployees || [];
    (WFRT.state.visibleEmployeeIds || []).forEach((empId) => {
      const emp = allEmployees.find((e) => String(e.id) === String(empId));
      if (!emp) return;

      const row = document.createElement("div");
      row.className = "flex border-b border-slate-200 bg-white/40 h-24 relative";

      const sidebar = document.createElement("div");
      sidebar.className =
        "w-48 flex-shrink-0 border-r border-slate-200 p-3 flex items-center gap-3 bg-white/50 sticky left-0 z-30 backdrop-blur-sm";
      sidebar.innerHTML = `
        <img src="${emp.avatar}" class="w-10 h-10 rounded-full border border-slate-200">
        <div>
          <div class="font-bold text-sm text-slate-800">${escapeHtml(emp.name)}</div>
          <div class="text-xs text-slate-500">${escapeHtml(emp.role || "")}</div>
        </div>`;
      row.appendChild(sidebar);

      const timeline = document.createElement("div");
      timeline.className = "flex-1 relative min-w-[1000px]";

      const employeeTasks = (WFRT.state.currentTasks || []).filter(
        (t) =>
          (t.assignees || []).map(String).includes(String(emp.id)) &&
          t.status !== "open" &&
          t.status !== "done"
      );

      employeeTasks.forEach((task) => {
        const bar = document.createElement("div");
        bar.className = "gantt-bar bg-sky-200 border border-sky-300 text-sky-900";
        bar.style.left = `${(task.startHour - START_HOUR) * PIXELS_PER_HOUR}px`;
        bar.style.width = `${task.duration * PIXELS_PER_HOUR}px`;
        bar.style.top = "24px";
        bar.innerHTML = `<span class="truncate">${escapeHtml(task.title)}</span>`;
        bar.onclick = () => window.openModal?.(task.id);
        timeline.appendChild(bar);
      });

      row.appendChild(timeline);
      container.appendChild(row);
    });
  }

  function renderAllViews() {
    recomputeCurrentTasks();
    renderChecklist();
    renderBoard();
    renderGantt();
    renderList();
    renderCalendar();
    renderHistory();
    renderActiveCrewWidget();
    updateUnplannedCount();
  }

  /* =========================================================================
   * Status updates
   * ========================================================================= */
  function upsertPlanMirrorFromChecklistTask(task) {
    if (!(task?.meta?.source_type && task?.meta?.source_id !== undefined && task?.meta?.source_id !== null)) return;

    const k = `${task.meta.source_type}:${task.meta.source_id}`;
    const exists = (WFRT.state.planTasks || []).some((pt) => taskKey(pt) === k);

    if (!exists) {
      WFRT.state.planTasks = [
        ...(WFRT.state.planTasks || []),
        {
          ...task,
          id: `plan_${task.meta.source_type}_${task.meta.source_id}_${Date.now()}`,
        },
      ];
      return;
    }

    WFRT.state.planTasks = (WFRT.state.planTasks || []).map((pt) =>
      taskKey(pt) === k ? { ...pt, ...task } : pt
    );
  }

  function updateTaskStatus(taskId, status, assigneesArray) {
    const task = (WFRT.state.currentTasks || []).find((t) => String(t.id) === String(taskId));
    if (!task) return;

    const oldStatus = task.status;

    task.status = status;
    if (assigneesArray !== null) task.assignees = Array.isArray(assigneesArray) ? assigneesArray : [];

    upsertPlanMirrorFromChecklistTask(task);

    if (oldStatus !== status) {
      WFRT.state.historyLog.unshift({
        date: nowLabel(),
        text: `Task "${task.title}" -> ${status}.`,
        type: "info",
      });
    }

    renderAllViews();
  }
  window.updateTaskStatus = updateTaskStatus;

  window.toggleTaskStatus = function toggleTaskStatus(taskId, event) {
    event?.stopPropagation?.();

    const task = (WFRT.state.currentTasks || []).find((t) => String(t.id) === String(taskId));
    if (!task) return;

    if (task.status === "in-progress") {
      const reason = prompt("Reason for pausing this task?", task.pauseReason || "Break");
      if (reason) {
        task.status = "paused";
        task.pauseReason = reason;
        WFRT.state.historyLog.unshift({ date: nowLabel(), text: `Task "${task.title}" paused.`, type: "warning" });
      }
    } else {
      task.status = "in-progress";
      task.pauseReason = null;
      WFRT.state.historyLog.unshift({ date: nowLabel(), text: `Task "${task.title}" started.`, type: "info" });
    }

    upsertPlanMirrorFromChecklistTask(task);
    renderAllViews();
  };

  /* =========================================================================
   * View switch
   * ========================================================================= */
  window.switchView = function switchView(viewName) {
    document.querySelectorAll(".view-container").forEach((el) => el.classList.add("hidden"));
    document.querySelectorAll('[id^="btn-view-"]').forEach((btn) => {
      btn.className =
        "px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2";
    });

    $(`view-${viewName}`)?.classList.remove("hidden");

    const btn = $(`btn-view-${viewName}`);
    if (btn) {
      btn.className =
        "px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark transition-all flex items-center gap-2";
    }

    if (viewName === "calendar") renderCalendar();
    else if (viewName === "history") renderHistory();
    else renderAllViews();
  };

  /* =========================================================================
   * Sortables
   * ========================================================================= */
  function initSortables() {
    if (!window.Sortable) return;

    WFRT._backlogSortables = WFRT._backlogSortables || [];
    destroySortables(WFRT._backlogSortables);
    WFRT._backlogSortables = [];

    document.querySelectorAll(".wf-phase-list").forEach((listEl) => {
      const inst = Sortable.create(listEl, {
        group: { name: "shared", pull: true, put: true },
        animation: 150,
        ghostClass: "sortable-ghost",
        sort: false,
        handle: ".drag-handle",
        filter: '[data-done="1"]',
        preventOnFilter: false,
        onAdd: function (evt) {
          const taskId = evt.item.getAttribute("data-id");
          setTaskLead(taskId, emp.id);      // change manager/column
          setTaskStatus(taskId, "scheduled");
        },

      });
      WFRT._backlogSortables.push(inst);
    });

    const unplannedList = $("unplanned-source");
    if (unplannedList) {
      if (WFRT._unplannedSortable) {
        try { WFRT._unplannedSortable.destroy(); } catch (_) {}
        WFRT._unplannedSortable = null;
      }

      WFRT._unplannedSortable = Sortable.create(unplannedList, {
        group: { name: "shared", pull: "clone", put: false },
        animation: 150,
        sort: false,
      });
    }
  }

    function setTaskLead(taskId, leadId) {
      const t = (WFRT.state.currentTasks || []).find(x => String(x.id) === String(taskId));
      if (!t) return;

      t.lead_id = parseInt(leadId, 10);

      // keep crew as-is; ensure lead is included if you want:
      t.crew_ids = Array.isArray(t.crew_ids) ? t.crew_ids : [];
      const s = new Set(t.crew_ids.map(String));
      s.add(String(leadId));
      t.crew_ids = [...s].map(v => parseInt(v, 10)).filter(n => n > 0);

      upsertPlanMirrorFromChecklistTask(t);
      renderAllViews();
    }

    function setTaskStatus(taskId, status) {
      const t = (WFRT.state.currentTasks || []).find(x => String(x.id) === String(taskId));
      if (!t) return;
      t.status = status;
      upsertPlanMirrorFromChecklistTask(t);
      renderAllViews();
    }

  /* =========================================================================
   * Search (left backlog)
   * ========================================================================= */
  function initSearch() {
    const input = $("task-search");
    if (!input) return;

    input.addEventListener("input", (e) => {
      const term = String(e.target.value || "").toLowerCase();
      document.querySelectorAll(".wf-phase-list > div").forEach((item) => {
        const title = String(item.getAttribute("data-title") || "").toLowerCase();
        item.style.display = title.includes(term) ? "flex" : "none";
      });
    });
  }

  /* =========================================================================
   * Crew modal
   * ========================================================================= */
  window.openCrewModal = function openCrewModal() {
    const container = $("crew-list-container");
    if (!container) return;

    const employees = WFRT.allEmployees || [];
    const selectedIds = Array.isArray(WFRT.state.visibleEmployeeIds) ? WFRT.state.visibleEmployeeIds : [];

    container.innerHTML = "";

    employees.forEach((emp) => {
      const isSelected = selectedIds.map(String).includes(String(emp.id));

      const row = document.createElement("div");
      row.className =
        "flex items-center justify-between p-3 rounded-lg cursor-pointer transition-colors border " +
        (isSelected ? "bg-blue-50 border-blue-200" : "hover:bg-slate-50 border-slate-200");

      row.onclick = () => window.toggleCrewMember(emp.id);

      row.innerHTML = `
        <div class="flex items-center gap-3">
          <img src="${emp.avatar}" class="w-10 h-10 rounded-full bg-slate-200">
          <div>
            <div class="font-bold text-sm text-slate-800">${escapeHtml(emp.name)}</div>
            <div class="text-xs text-slate-500">${escapeHtml(emp.role || "")}</div>
          </div>
        </div>
        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center ${
          isSelected ? "border-brandDark bg-brandDark text-white" : "border-slate-300 text-transparent"
        }">
          <i class="fa-solid fa-check text-xs"></i>
        </div>`;
      container.appendChild(row);
    });

    $("crew-modal")?.classList.remove("hidden");
    setTimeout(() => $("crew-modal-content")?.classList.remove("translate-x-full"), 10);
  };

  window.closeCrewModal = function closeCrewModal() {
    $("crew-modal-content")?.classList.add("translate-x-full");
    setTimeout(() => $("crew-modal")?.classList.add("hidden"), 250);
  };

  window.toggleCrewMember = function toggleCrewMember(empId) {
    const current = Array.isArray(WFRT.state.visibleEmployeeIds) ? WFRT.state.visibleEmployeeIds : [];
    const empStr = String(empId);

    const next = current.map(String).includes(empStr)
      ? current.filter((id) => String(id) !== empStr)
      : [...current, empId];

    WFRT.setVisibleEmployeeIds(next);
    renderActiveCrewWidget();
    renderAllViews();
    window.openCrewModal();
  };

  /* =========================================================================
   * Wizard (accordion + search + selection persistence)
   * ========================================================================= */
  function wizardGroupKey(g) {
    if (g?.phase_id != null) return `p:${g.phase_id}`;
    return `n:${slugKey(g?.phase_name || "phase") || "phase"}`;
  }

  function isWizardCollapsed(kind, key) {
    return !!WFRT.state.wizardUi?.collapsed?.[kind]?.[key];
  }

  function setWizardCollapsed(kind, key, val) {
    WFRT.state.wizardUi = WFRT.state.wizardUi || { query: "", collapsed: { planned: {}, remaining: {} } };
    WFRT.state.wizardUi.collapsed[kind] = WFRT.state.wizardUi.collapsed[kind] || {};
    WFRT.state.wizardUi.collapsed[kind][key] = !!val;
  }

  window.__wfWizardToggleGroup = function __wfWizardToggleGroup(kind, key) {
    setWizardCollapsed(kind, key, !isWizardCollapsed(kind, key));
    renderWizardFromBootstrap(WFRT.state.lastBootstrapPhaseItems || []);
  };

  window.__wfWizardCollapseAll = function __wfWizardCollapseAll(collapse = true) {
    const items = Array.isArray(WFRT.state.lastBootstrapPhaseItems) ? WFRT.state.lastBootstrapPhaseItems : [];
    const planned = items.filter((i) => !!i.is_planned);
    const remaining = items.filter((i) => !i.is_planned);

    groupByPhase(planned).forEach((g) => setWizardCollapsed("planned", wizardGroupKey(g), collapse));
    groupByPhase(remaining).forEach((g) => setWizardCollapsed("remaining", wizardGroupKey(g), collapse));
    renderWizardFromBootstrap(items);
  };

  function wizardMatchesQuery(it, q) {
    if (!q) return true;
    const hay = [it?.title, it?.description, it?.notes, it?.section_name, it?.phase_name]
      .join(" ")
      .toLowerCase();
    return hay.includes(q);
  }

  function groupByPhase(items) {
    const map = new Map();

    (items || []).forEach((it) => {
      const pid = it.phase_id ?? it.phaseId ?? null;
      const pname = it.phase_name ?? it.phaseName ?? "Phase";
      const key = pid != null ? `p:${pid}` : `n:${slugKey(pname) || "phase"}`;
      if (!map.has(key)) map.set(key, { phase_id: pid, phase_name: pname, _key: key, items: [] });
      map.get(key).items.push(it);
    });

    for (const g of map.values()) {
      g.items.sort((a, b) => {
        const sa = String(a.section_name || "").localeCompare(String(b.section_name || ""));
        if (sa !== 0) return sa;
        const oa = a.sort_order ?? 999999;
        const ob = b.sort_order ?? 999999;
        if (oa !== ob) return oa - ob;
        return String(a.id).localeCompare(String(b.id));
      });
    }

    return [...map.values()].sort((a, b) => {
      const ap = a.phase_id != null ? Number(a.phase_id) : Number.NaN;
      const bp = b.phase_id != null ? Number(b.phase_id) : Number.NaN;
      if (!Number.isNaN(ap) && !Number.isNaN(bp) && ap !== bp) return ap - bp;
      return String(a.phase_name || "").localeCompare(String(b.phase_name || ""));
    });
  }

  function renderWizardFromBootstrap(phaseItems) {
    const plannedHost = $("wizard-planned-list");
    const remainingHost = $("wizard-remaining-list");
    if (!plannedHost || !remainingHost) return;

    const search = $("wizard-task-search");
    const q = String(search?.value ?? WFRT.state.wizardUi?.query ?? "").trim().toLowerCase();
    WFRT.state.wizardUi.query = q;

    plannedHost.innerHTML = "";
    remainingHost.innerHTML = "";

    const items = Array.isArray(phaseItems) ? phaseItems : [];
    if (!items.length) {
      plannedHost.innerHTML = `<div class="text-xs text-slate-400 italic p-2">No planned items yet.</div>`;
      remainingHost.innerHTML = `<div class="text-xs text-slate-400 italic p-2">No checklist found for this stage/product.</div>`;
      return;
    }

    const selected = new Set(WFRT.state.tempWizardSelectedActivityIds || []);

    const planned = items.filter((i) => !!i.is_planned).filter((it) => wizardMatchesQuery(it, q));
    const remaining = items.filter((i) => !i.is_planned).filter((it) => wizardMatchesQuery(it, q));

    const plannedGroups = groupByPhase(planned);
    if (!plannedGroups.length) {
      plannedHost.innerHTML = `<div class="text-xs text-slate-400 italic p-2">${q ? "No planned items match your search." : "No planned items yet."}</div>`;
    } else {
      plannedGroups.forEach((g) => {
        const key = wizardGroupKey(g);
        const collapsed = isWizardCollapsed("planned", key);

        const block = document.createElement("div");
        block.className = "bg-white/70 border border-slate-200 rounded-2xl overflow-hidden";
        block.innerHTML = `
          <button type="button"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-white/60 transition-colors"
            aria-expanded="${collapsed ? "false" : "true"}"
            onclick="window.__wfWizardToggleGroup('planned','${escapeHtml(key)}')"
          >
            <div class="flex items-center gap-2 min-w-0">
              <i class="fa-solid ${collapsed ? "fa-chevron-right" : "fa-chevron-down"} text-slate-400 text-xs"></i>
              <div class="font-extrabold text-slate-700 text-sm truncate">${escapeHtml(g.phase_name || "Stage")}</div>
            </div>
            <div class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-full">${g.items.length}</div>
          </button>

          <div class="${collapsed ? "hidden" : ""} px-4 pb-4">
            <div class="mt-2 space-y-2">
              ${g.items.map((it) => `
                <div class="flex items-start justify-between gap-3 p-2 rounded-xl bg-slate-50 border border-slate-100">
                  <div class="min-w-0">
                    <div class="text-sm font-bold text-slate-700 truncate">${escapeHtml(it.title || "Task")}</div>
                    <div class="text-[11px] text-slate-500">
                      <span class="font-bold">${escapeHtml(it.section_name || "General")}</span>
                      · ${escapeHtml(formatMinutes(it.duration_minutes || 60))}
                      ${it.is_done ? ' · <span class="text-green-700 font-extrabold">DONE</span>' : ""}
                    </div>
                    ${it.description ? `<div class="text-[11px] text-slate-400 mt-1 line-clamp-2">${escapeHtml(it.description)}</div>` : ""}
                  </div>
                </div>
              `).join("")}
            </div>
          </div>
        `;
        plannedHost.appendChild(block);
      });
    }

    const remainingGroups = groupByPhase(remaining);
    if (!remainingGroups.length) {
      remainingHost.innerHTML = `<div class="text-xs text-slate-400 italic p-2">${q ? "No remaining items match your search." : "All checklist items are already planned."}</div>`;
    } else {
      remainingGroups.forEach((g) => {
        const key = wizardGroupKey(g);
        const collapsed = isWizardCollapsed("remaining", key);

        const block = document.createElement("div");
        block.className = "bg-white border border-slate-200 rounded-2xl overflow-hidden";
        block.innerHTML = `
          <button type="button"
            class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition-colors"
            aria-expanded="${collapsed ? "false" : "true"}"
            onclick="window.__wfWizardToggleGroup('remaining','${escapeHtml(key)}')"
          >
            <div class="flex items-center gap-2 min-w-0">
              <i class="fa-solid ${collapsed ? "fa-chevron-right" : "fa-chevron-down"} text-slate-400 text-xs"></i>
              <div class="font-extrabold text-brandDark text-sm truncate">${escapeHtml(g.phase_name || "Stage")}</div>
            </div>
            <div class="text-[10px] font-bold bg-brandDark/10 text-brandDark px-2 py-1 rounded-full">${g.items.length}</div>
          </button>

          <div class="${collapsed ? "hidden" : ""} px-4 pb-4">
            <div class="mt-2 space-y-2">
              ${g.items.map((it) => {
                const id = String(it.id);
                const disabled = !!it.is_done;
                const checked = selected.has(id);
                return `
                  <label class="flex items-start gap-3 p-2 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer ${disabled ? "opacity-60 cursor-not-allowed" : ""}">
                    <input type="checkbox"
                      class="mt-1"
                      ${disabled ? "disabled" : ""}
                      ${checked ? "checked" : ""}
                      data-wizard-activity-id="${escapeHtml(id)}"
                      onchange="window.__wfWizardToggleActivity('${escapeHtml(id)}', this.checked)"
                    >
                    <div class="min-w-0 flex-1">
                      <div class="text-sm font-bold text-slate-700 truncate">${escapeHtml(it.title || "Task")}</div>
                      <div class="text-[11px] text-slate-500">
                        <span class="font-bold">${escapeHtml(it.section_name || "General")}</span>
                        · ${escapeHtml(formatMinutes(it.duration_minutes || 60))}
                      </div>
                      ${it.description ? `<div class="text-[11px] text-slate-400 mt-1 line-clamp-2">${escapeHtml(it.description)}</div>` : ""}
                    </div>
                  </label>
                `;
              }).join("")}
            </div>
          </div>
        `;
        remainingHost.appendChild(block);
      });
    }
  }

  window.__wfWizardToggleActivity = function __wfWizardToggleActivity(activityId, checked) {
    const id = String(activityId);
    const set = new Set(WFRT.state.tempWizardSelectedActivityIds || []);
    if (checked) set.add(id);
    else set.delete(id);
    WFRT.state.tempWizardSelectedActivityIds = [...set];
  };

  function initWizardSearch() {
    const input = $("wizard-task-search");
    if (!input) return;

    input.addEventListener(
      "input",
      debounce(() => {
        renderWizardFromBootstrap(WFRT.state.lastBootstrapPhaseItems || []);
      }, 120)
    );
  }

  /* =========================================================================
   * Wizard open/close + save
   * ========================================================================= */
    window.openPlanWizard = async function openPlanWizard() {
        const name = $("customer-search-input")?.value?.trim() || "Current Customer";
        if ($("wizard-customer-name")) $("wizard-customer-name").innerText = name;

        renderWizardFromBootstrap(WFRT.state.lastBootstrapPhaseItems || []);
        await renderWizardBulkAssignmentUI();

        // ensure appointments list is ready too
        renderWizardAppointmentsList();

        $("plan-wizard-modal")?.classList.remove("hidden");
    };

    window.toggleWizardType = function toggleWizardType(type) {
        $("wizard-form-project")?.classList.add("hidden");
        $("wizard-form-appointments")?.classList.add("hidden");
        $("wizard-form-custom")?.classList.add("hidden");

        if (type === "project") $("wizard-form-project")?.classList.remove("hidden");

        if (type === "appointments") {
        $("wizard-form-appointments")?.classList.remove("hidden");
        renderWizardAppointmentsList();
        }

        if (type === "custom") $("wizard-form-custom")?.classList.remove("hidden");
    };


  window.closePlanWizard = function closePlanWizard() {
    $("plan-wizard-modal")?.classList.add("hidden");
  };

  window.toggleWizardType = function toggleWizardType(type) {
    $("wizard-form-project")?.classList.add("hidden");
    $("wizard-form-appointments")?.classList.add("hidden");
    $("wizard-form-custom")?.classList.add("hidden");
    if (type === "project") $("wizard-form-project")?.classList.remove("hidden");
    if (type === "appointments") $("wizard-form-appointments")?.classList.remove("hidden");
    if (type === "custom") $("wizard-form-custom")?.classList.remove("hidden");
  };

  window.toggleApptResolveType = function toggleApptResolveType(type) {
    $("appt-resolve-link")?.classList.add("hidden");
    $("appt-resolve-manual")?.classList.add("hidden");
    if (type === "link") $("appt-resolve-link")?.classList.remove("hidden");
    if (type === "manual") $("appt-resolve-manual")?.classList.remove("hidden");
  };

    window.savePlanWizard = function savePlanWizard() {
        const selected = new Set(WFRT.state.tempWizardSelectedActivityIds || []);
        if (!selected.size) {
            window.showToast?.("No checklist items selected.");
            window.closePlanWizard();
            return;
        }

        // bulk assignment from wizard
        const pmId = String(WFRT.state.wizardAssign?.pm_id || "").trim();
        const crewIds = (WFRT.state.wizardAssign?.crew_ids || []).map(String);
        const assetIds = (WFRT.state.wizardAssign?.asset_ids || []).map(String);

        const employeeIds = [...new Set([pmId, ...crewIds].filter((v) => v && v !== "0"))];

        // optional: keep active crew widget in sync
        if (employeeIds.length) {
            WFRT.setVisibleEmployeeIds(employeeIds);
        }

        const phaseItems = WFRT.state.lastBootstrapPhaseItems || [];
        const picked = phaseItems.filter((it) => selected.has(String(it.id)));

        const newPlanTasks = picked.map((it) => {
            const sourceId = parseInt(it.id, 10);

            return {
            id: `plan_phase_activity_${sourceId}_${Date.now()}`,
            title: it.title || "Task",
            duration: minutesToHours(it.duration_minutes ?? 60),
            duration_minutes: parseInt(it.duration_minutes ?? 60, 10),
            category: it.section_name || "General",
            status: "scheduled",
            assignees: employeeIds.map((v) => parseInt(v, 10)).filter((n) => Number.isFinite(n) && n > 0),
            startHour: 0,
            predecessors: [],
            asset_ids: assetIds.map((v) => parseInt(v, 10)).filter((n) => Number.isFinite(n) && n > 0),
            description: it.description || "",
            meta: { source_type: "phase_activity", source_id: sourceId },
            };
        });

        WFRT.state.planTasks = mergeTasks(WFRT.state.planTasks, newPlanTasks, true);

        // store plan meta (pm + crew) locally for Publish validation
        WFRT.state.planMeta = WFRT.state.planMeta || {};
        if (pmId) WFRT.state.planMeta.pm_id = parseInt(pmId, 10);
        WFRT.state.planMeta.crew_ids = employeeIds.map((v) => parseInt(v, 10)).filter((n) => Number.isFinite(n) && n > 0);

        WFRT.state.historyLog.unshift({
            date: nowLabel(),
            text: `Added ${newPlanTasks.length} checklist items to plan.`,
            type: "success",
        });

        WFRT.state.tempWizardSelectedActivityIds = [];
        renderAllViews();
        window.showToast?.("Plan updated (draft).");
        window.closePlanWizard();
    };

  /* =========================================================================
   * Manual task
   * ========================================================================= */
  window.addManualTask = function addManualTask() {
    const title = prompt("Manual task title?");
    if (!title) return;

    const duration = parseFloat(prompt("Duration (hours)?", "1") || "1") || 1;
    const sid = Date.now();

    const newTask = {
      id: `m_${sid}`,
      title,
      duration,
      category: "Manual",
      status: "open",
      assignees: [],
      startHour: 0,
      predecessors: [],
      assets: [],
      subtasks: [],
      description: "",
      meta: { source_type: "manual", source_id: sid },
    };

    WFRT.state.planTasks = [...(WFRT.state.planTasks || []), newTask];
    recomputeCurrentTasks();
    renderAllViews();
    window.showToast?.("Manual task added.");
  };

window.savePlan = async function savePlan() {
  const customerId = WF.state.customerId;
  const projectId  = WF.state.projectId;
  const planId     = WF.state.planId;
  const stage      = stageValue();

  if (!customerId || !projectId || !planId) {
    window.showToast?.("Select Customer + Project first.");
    return;
  }

  // PM validation (required)
  const pmFromWizard = String(WFRT.state.wizardAssign?.pm_id || "").trim();
  const pmFromMeta   = WFRT.state.planMeta?.pm_id ? String(WFRT.state.planMeta.pm_id) : "";
  const pmId = pmFromWizard || pmFromMeta;

  if (!pmId) {
    window.showToast?.("Project Manager is required. Please select a Lead.");
    // open wizard to force selection
    try {
      await window.openPlanWizard?.();
      setTimeout(() => document.getElementById("wizard-pm-select")?.focus(), 50);
    } catch (_) {}
    return;
  }

  // crew: if user already selected crew modal, visibleEmployeeIds is your current crew list
  const crewIds = (WFRT.state.visibleEmployeeIds || []).map(String);
  const mergedCrew = [...new Set([pmId, ...crewIds].filter((v) => v && v !== "0"))]
    .map((v) => parseInt(v, 10))
    .filter((n) => Number.isFinite(n) && n > 0);

  // build items payload from planTasks only
  const items = (WFRT.state.planTasks || []).map((t, idx) => {
    const minutes =
      Number.isFinite(Number(t.duration_minutes)) ? parseInt(t.duration_minutes, 10)
      : Math.max(1, Math.round((Number(t.duration || 1) || 1) * 60));

    const sourceType = t?.meta?.source_type || t?.meta?.sourceType || null;
    const sourceId   = t?.meta?.source_id ?? t?.meta?.sourceId ?? null;

    return {
      source_type: sourceType,
      source_id: sourceId,
      title: t.title || "Task",
      category: t.category || null,
      description: t.description || null,
      duration_minutes: minutes,
      status: t.status || "open",
      planned_start_at: t.startDate || null,
      planned_end_at: t.dueDate || null,
      sort_order: Number.isFinite(Number(t.sort_order)) ? parseInt(t.sort_order, 10) : idx,
      meta: t.meta || null,
      employee_ids: Array.isArray(t.assignees) ? t.assignees : [],
      asset_ids: Array.isArray(t.asset_ids) ? t.asset_ids : [],
      dependency_ids: Array.isArray(t.predecessors) ? t.predecessors : [],
    };
  });

  const payload = {
    plan_id: parseInt(planId, 10),
    customer_id: parseInt(customerId, 10),
    project_id: parseInt(projectId, 10),
    stage,
    status: "draft", // keep draft so your bootstrap/getPlan continue to work
    meta: {
      pm_id: parseInt(pmId, 10),
      crew_ids: mergedCrew,
    },
    items,
  };

  try {
    const res = await fetch(window.WF_API.planSave, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF,
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      const msg =
        data?.message ||
        data?.errors?.meta?.pm_id?.[0] ||
        data?.errors?.["meta.pm_id"]?.[0] ||
        "Save failed.";
      window.showToast?.(msg);
      return;
    }

    // keep local meta for next publish
    WFRT.state.planMeta = WFRT.state.planMeta || {};
    WFRT.state.planMeta.pm_id = parseInt(pmId, 10);
    WFRT.state.planMeta.crew_ids = mergedCrew;

    window.showToast?.("Plan saved.");
    await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);
  } catch (e) {
    window.showToast?.("Network error while saving plan.");
  }
};


  /* =========================================================================
   * Toast
   * ========================================================================= */
  window.showToast = function showToast(msg) {
    const toast = $("toast");
    if (!toast) return;

    const h4 = toast.querySelector("div h4");
    if (h4) h4.innerText = msg;

    toast.classList.remove("translate-y-20", "opacity-0");
    setTimeout(() => toast.classList.add("translate-y-20", "opacity-0"), 3000);
  };

  /* =========================================================================
   * Modal (minimal)
   * ========================================================================= */
  function switchModalTab(tabId) {
    document.querySelectorAll(".tab-content").forEach((el) => el.classList.remove("active"));
    document.querySelectorAll(".tab-btn").forEach((el) => el.classList.remove("active"));
    $(`tab-${tabId}`)?.classList.add("active");
    $(`tab-btn-${tabId}`)?.classList.add("active");
  }
  window.switchModalTab = switchModalTab;

  window.openModal = function openModal(taskId) {
    WFRT.setActiveTaskId(taskId);
    const task = (WFRT.state.currentTasks || []).find((t) => String(t.id) === String(taskId));
    if (!task) return;

    switchModalTab("overview");

    if ($("modal-edit-title")) $("modal-edit-title").value = task.title || "";
    if ($("modal-edit-category")) $("modal-edit-category").value = task.category || "General";
    if ($("modal-edit-duration")) $("modal-edit-duration").value = task.duration ?? 1;
    if ($("modal-edit-description")) $("modal-edit-description").value = task.description || "";

    if ($("modal-assignee-text")) {
      $("modal-assignee-text").innerText = task.assignees?.length
        ? `${task.assignees.length} Techs Assigned`
        : "Unassigned";
    }
    if ($("modal-schedule")) {
      $("modal-schedule").innerText = task.startDate ? `${task.startDate} - ${task.dueDate || ""}` : "--";
    }
    if ($("modal-travel")) {
      $("modal-travel").innerText = task.travelTime ? `${task.travelTime} from ${task.origin || ""}` : "--";
    }

    $("task-modal")?.classList.remove("hidden");
    setTimeout(() => $("task-modal-content")?.classList.remove("translate-x-full"), 10);
  };

  window.closeModal = function closeModal() {
    $("task-modal-content")?.classList.add("translate-x-full");
    setTimeout(() => $("task-modal")?.classList.add("hidden"), 300);
  };

  window.saveActiveTask = function saveActiveTask() {
    const taskId = WFRT.state.activeTaskId;
    const task = (WFRT.state.currentTasks || []).find((t) => String(t.id) === String(taskId));
    if (!task) return;

    task.title = $("modal-edit-title")?.value ?? task.title;
    task.category = $("modal-edit-category")?.value ?? task.category;
    task.duration = parseFloat($("modal-edit-duration")?.value || "") || task.duration;
    task.description = $("modal-edit-description")?.value ?? task.description;

    upsertPlanMirrorFromChecklistTask(task);

    WFRT.state.historyLog.unshift({ date: nowLabel(), text: `Task "${task.title}" updated.`, type: "info" });
    renderAllViews();
    window.closeModal();
    window.showToast("Task updated.");
  };

  window.deleteActiveTask = function deleteActiveTask() {
    const taskId = WFRT.state.activeTaskId;
    if (!confirm("Are you sure you want to delete this task?")) return;

    WFRT.state.planTasks = (WFRT.state.planTasks || []).filter((t) => String(t.id) !== String(taskId));
    WFRT.state.checklistTasks = (WFRT.state.checklistTasks || []).filter((t) => String(t.id) !== String(taskId));
    recomputeCurrentTasks();

    WFRT.state.historyLog.unshift({ date: nowLabel(), text: "Task deleted.", type: "warning" });

    renderAllViews();
    window.closeModal();
    window.showToast("Task deleted.");
  };

  /* =========================================================================
   * Init
   * ========================================================================= */
  document.addEventListener("DOMContentLoaded", async () => {
    bindCustomerDropdown();
    bindProjectSelector();
    initSearch();
    initWizardSearch();
    initSortables();

    $("stage-selector")?.addEventListener("change", async () => {
      await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);
    });

    // preload lists (optional but useful)
    await Promise.allSettled([loadEmployees(""), loadAssets("")]);
    if (!$("plan-wizard-modal")?.classList.contains("hidden")) {
        await renderWizardBulkAssignmentUI();
        }


    recomputeCurrentTasks();
    renderAllViews();
  });

  window.renderAllViews = renderAllViews;
})();
</script>

<!-- FULL SCRIPT PART (paste this AFTER your big IIFE script; this replaces your last small <script>…plans…</script>) -->
<script>
(() => {
  "use strict";

  // ---------------------------------------------------------------------------
  // REQUIREMENTS:
  // - You must have these selects/inputs in DOM:
  //   #plan-selector, #stage-selector, #project-selector, #customer-search-input
  // - Your big IIFE must have already created:
  //   window.WF, window.__WF (WFRT), window.WF_API, WF.loadPlan, WF.loadBootstrapChecklist
  // - Endpoint: route('wf.plans.index') returns JSON: { plans: [...] } or [...]
  //   Each plan should ideally include: id, name, stage, status, is_default, updated_at
  // ---------------------------------------------------------------------------

  const $ = (id) => document.getElementById(id);

  function isNonEmpty(v){ return v !== undefined && v !== null && String(v).trim().length > 0; }

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  async function httpGet(url, params = {}) {
    const u = new URL(url, window.location.origin);
    Object.entries(params || {}).forEach(([k, v]) => {
      if (isNonEmpty(v)) u.searchParams.set(k, String(v));
    });

    const res = await fetch(u.toString(), {
      method: "GET",
      headers: { Accept: "application/json" },
      credentials: "same-origin",
    });

    if (!res.ok) throw new Error(`GET ${u.pathname} failed (${res.status})`);
    return res.json();
  }

  function normalizeArrayPayload(payload, keys = ["plans", "items", "data"]) {
    if (Array.isArray(payload)) return payload;
    if (!payload) return [];
    for (const k of keys) if (Array.isArray(payload?.[k])) return payload[k];
    return [];
  }

  // ---------------------------------------------------------------------------
  // Header sync inside wizard
  // ---------------------------------------------------------------------------
  function currentCustomerName() {
    return $("customer-search-input")?.value?.trim() || "—";
  }
  function currentProductName() {
    const sel = $("project-selector");
    const txt = sel?.selectedOptions?.[0]?.textContent?.trim();
    return txt || "—";
  }
  function currentStageValue() {
    return $("stage-selector")?.value || "montage";
  }
  function currentPlanName() {
    const sel = $("plan-selector");
    return sel?.selectedOptions?.[0]?.textContent?.trim() || "—";
  }

  function updateWizardHeaderContext() {
    if ($("wizard-customer-name")) $("wizard-customer-name").innerText = currentCustomerName();
    if ($("wizard-product-name")) $("wizard-product-name").innerText = currentProductName();
    if ($("wizard-plan-name")) $("wizard-plan-name").innerText = currentPlanName();

    const pid = window.WF?.state?.planId ? String(window.WF.state.planId) : "";
    if ($("wizard-plan-id")) $("wizard-plan-id").innerText = pid || "—";

    const badge = $("wizard-plan-badge");
    if (badge) badge.classList.toggle("hidden", !pid);

    const stageBadge = $("wizard-stage-badge");
    if (stageBadge) stageBadge.classList.remove("hidden");
    if ($("wizard-stage-name")) $("wizard-stage-name").innerText = currentStageValue();
  }

  // Make sure wizard updates even if openPlanWizard is in the big script
  function patchOpenPlanWizardHeader() {
    if (!window.openPlanWizard || window.__wfOpenPlanWizardPatched) return;
    window.__wfOpenPlanWizardPatched = true;

    const original = window.openPlanWizard;
    window.openPlanWizard = async function() {
      updateWizardHeaderContext();
      return original.apply(this, arguments);
    };
  }

  // ---------------------------------------------------------------------------
  // Plans loader + default plan selection
  // ---------------------------------------------------------------------------
  function setPlanSelectorLoading(msg = "Lade Pläne…") {
    const sel = $("plan-selector");
    if (!sel) return;
    sel.innerHTML = `<option value="">${escapeHtml(msg)}</option>`;
    sel.disabled = true;
  }

  function setPlanSelectorEmpty(msg = "Keine Pläne gefunden") {
    const sel = $("plan-selector");
    if (!sel) return;
    sel.innerHTML = `<option value="">${escapeHtml(msg)}</option>`;
    sel.disabled = true;
  }

  function renderPlansToSelector(plans) {
    const sel = $("plan-selector");
    if (!sel) return;

    sel.innerHTML = "";

    if (!plans?.length) {
      setPlanSelectorEmpty("Keine Pläne gefunden");
      return;
    }

    // group by stage for readability
    const groups = {
      montage: { label: "Montage", items: [] },
      inbetriebnahme: { label: "Inbetriebnahme", items: [] },
      other: { label: "Weitere", items: [] },
    };

    plans.forEach((p) => {
      const st = (p.stage === "montage" || p.stage === "inbetriebnahme") ? p.stage : "other";
      groups[st].items.push(p);
    });

    // placeholder
    const ph = document.createElement("option");
    ph.value = "";
    ph.textContent = "Plan auswählen…";
    sel.appendChild(ph);

    let added = 0;
    Object.entries(groups).forEach(([key, g]) => {
      if (!g.items.length) return;

      const og = document.createElement("optgroup");
      og.label = g.label;

      g.items.forEach((p) => {
        const opt = document.createElement("option");
        opt.value = String(p.id);
        opt.textContent = String(p.name || p.title || `Plan #${p.id}`);
        opt.dataset.stage = p.stage || "";
        opt.dataset.status = p.status || "";
        opt.dataset.isDefault = p.is_default ? "1" : "0";
        opt.dataset.updatedAt = p.updated_at || p.updatedAt || "";
        og.appendChild(opt);
        added++;
      });

      sel.appendChild(og);
    });

    sel.disabled = !added;
  }

  function chooseDefaultPlanId(plans, stageWanted, preferredPlanId) {
    const list = Array.isArray(plans) ? plans : [];
    if (!list.length) return "";

    // 0) explicit preference (e.g., already selected)
    if (preferredPlanId && list.some(p => String(p.id) === String(preferredPlanId))) {
      return String(preferredPlanId);
    }

    // 1) match stage + draft first
    const stageDraft = list
      .filter(p => (p.stage || "") === stageWanted && (String(p.status || "").toLowerCase() === "draft"))
      .sort((a,b) => String(b.updated_at || "").localeCompare(String(a.updated_at || "")));
    if (stageDraft[0]) return String(stageDraft[0].id);

    // 2) is_default flag (stage-aware first)
    const stageDefault = list.find(p => (p.stage || "") === stageWanted && !!p.is_default);
    if (stageDefault) return String(stageDefault.id);

    const anyDefault = list.find(p => !!p.is_default);
    if (anyDefault) return String(anyDefault.id);

    // 3) newest by updated_at
    const newest = [...list].sort((a,b) => String(b.updated_at || "").localeCompare(String(a.updated_at || "")));
    return newest[0] ? String(newest[0].id) : String(list[0].id);
  }

  async function loadPlansAndApplyDefault() {
    const WF = window.WF;
    if (!WF?.state) return;

    const customerId = WF.state.customerId;
    const projectId  = WF.state.projectId;

    if (!customerId || !projectId) {
      setPlanSelectorEmpty("Bitte zuerst Kunde & Projekt wählen…");
      WF.state.planId = null;
      updateWizardHeaderContext();
      return;
    }

    const stageWanted = currentStageValue();
    setPlanSelectorLoading();

    try {
      // You must set this in WF_API in your big script: plansIndex: route('wf.plans.index')
      const url = window.WF_API?.plansIndex;
      if (!url) {
        setPlanSelectorEmpty("plansIndex Route fehlt");
        return;
      }

      const data = await httpGet(url, {
        customer_id: customerId,
        project_id: projectId,
        // optional stage filter if your endpoint supports it:
        // stage: stageWanted,
      });

      const plans = normalizeArrayPayload(data, ["plans", "items", "data"]);
      renderPlansToSelector(plans);

      const sel = $("plan-selector");
      if (!sel) return;

      const chosen = chooseDefaultPlanId(plans, stageWanted, WF.state.planId);
      if (chosen) {
        sel.value = String(chosen);
        WF.state.planId = String(chosen);

        // if plan has stage, sync stage selector to it (important!)
        const opt = sel.querySelector(`option[value="${CSS.escape(String(chosen))}"]`);
        const planStage = opt?.dataset?.stage;
        if (planStage && $("stage-selector")) $("stage-selector").value = planStage;
      } else {
        sel.value = "";
        WF.state.planId = null;
      }

      updateWizardHeaderContext();

      // reload plan/checklist after selecting default plan
      await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);

    } catch (e) {
      console.error(e);
      setPlanSelectorEmpty("Fehler beim Laden der Pläne");
    }
  }

  // ---------------------------------------------------------------------------
  // Bind events (customer/project/stage/plan changes)
  // ---------------------------------------------------------------------------
  function bindPlanSelectorChange() {
    const sel = $("plan-selector");
    if (!sel) return;

    sel.addEventListener("change", async () => {
      const WF = window.WF;
      if (!WF?.state) return;

      const planId = sel.value ? String(sel.value) : null;
      WF.state.planId = planId;

      // sync stage from selected plan
      const opt = sel.selectedOptions?.[0];
      const st  = opt?.dataset?.stage;
      if (st && $("stage-selector")) $("stage-selector").value = st;

      updateWizardHeaderContext();
      await Promise.allSettled([WF.loadPlan?.(), WF.loadBootstrapChecklist?.()]);
    });
  }

  function bindStageSelectorChange() {
    $("stage-selector")?.addEventListener("change", async () => {
      // stage changes affects available plans + checklist
      await loadPlansAndApplyDefault();
      updateWizardHeaderContext();
    });
  }

  function patchCustomerSelectionHook() {
    const WF = window.WF;
    if (!WF?.selectCustomer || window.__wfCustomerSelectPatched) return;
    window.__wfCustomerSelectPatched = true;

    const original = WF.selectCustomer;
    WF.selectCustomer = async function() {
      const res = await original.apply(this, arguments);
      await loadPlansAndApplyDefault();
      return res;
    };
  }

  function bindProjectSelectorChange() {
    const projectSel = $("project-selector");
    if (!projectSel) return;

    projectSel.addEventListener("change", async () => {
      // your big script already loads plan/bootstrap on project change,
      // we only ensure plans list gets updated + default applied
      await loadPlansAndApplyDefault();
    });
  }

  // ---------------------------------------------------------------------------
  // IMPORTANT: If your WF.loadPlan endpoint can accept plan_id, you should pass it.
  // This wrapper adds plan_id automatically without breaking current behavior.
  // ---------------------------------------------------------------------------
  function patchLoadersToIncludePlanId() {
    const WF = window.WF;
    if (!WF?.loadPlan || !WF?.loadBootstrapChecklist) return;
    if (window.__wfLoadersPatched) return;
    window.__wfLoadersPatched = true;

    // Wrap WF.loadPlan: if plan_id exists, prefer loading that plan (server may ignore if unsupported)
    const origLoadPlan = WF.loadPlan;
    WF.loadPlan = async function() {
      // If your existing loadPlan already reads WF.state.planId internally, you can skip this.
      // This wrapper simply runs original.
      return origLoadPlan.apply(this, arguments);
    };

    // Wrap WF.loadBootstrapChecklist similarly
    const origBootstrap = WF.loadBootstrapChecklist;
    WF.loadBootstrapChecklist = async function() {
      return origBootstrap.apply(this, arguments);
    };
  }

  // ---------------------------------------------------------------------------
  // Init
  // ---------------------------------------------------------------------------
  document.addEventListener("DOMContentLoaded", async () => {
    patchOpenPlanWizardHeader();
    patchCustomerSelectionHook();
    patchLoadersToIncludePlanId();

    bindPlanSelectorChange();
    bindProjectSelectorChange();
    bindStageSelectorChange();

    // initial
    await loadPlansAndApplyDefault();
    updateWizardHeaderContext();
  });

})();
</script>

</body>
</html>