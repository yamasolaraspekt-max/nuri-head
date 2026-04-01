<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <input type="text" id="customer-search-input" placeholder="Search Customer..." 
                        class="w-full pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white"
                        autocomplete="off"
                        onfocus="showCustomerDropdown()"
                        oninput="filterCustomerDropdown()"
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
                    <select id="project-selector" onchange="changeProject(this.value)" class="appearance-none pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark transition-all cursor-pointer hover:bg-white min-w-[280px]" disabled>
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
                    <select id="stage-selector" class="appearance-none pl-10 pr-8 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-actionGreen/20 focus:border-actionGreen transition-all cursor-pointer hover:bg-white">
                        <option value="montage">Stage: Montage (Install)</option>
                        <option value="inbetriebnahme">Stage: Inbetriebnahme</option>
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
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Add Plan</h2>
                        <p class="text-sm text-slate-500">Create tasks for <span id="wizard-customer-name" class="font-bold text-brandDark">Current Customer</span></p>
                    </div>
                    <button onclick="closePlanWizard()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark"></i></button>
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
                        
                        <!-- New Tab: Open Appointments -->
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
                                <span class="text-xs font-bold bg-actionGreen/10 text-actionGreen px-3 py-1 rounded-full">Product: PV System</span>
                            </div>

                            <!-- Planned Tasks (Read Only) -->
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">Already Planned</h4>
                                <div id="wizard-planned-list" class="space-y-2 opacity-70">
                                    <!-- JS Injected -->
                                </div>
                            </div>

                            <!-- Remaining Tasks (Selectable) -->
                            <div class="space-y-2 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-brandDark uppercase tracking-wide">Remaining / Unplanned</h4>
                                <div id="wizard-remaining-list" class="space-y-2">
                                    <!-- JS Injected -->
                                </div>
                            </div>

                            <!-- Assignment Controls -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4 mt-6">
                                <h4 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-user-plus mr-2 text-sky-500"></i>Bulk Assignment</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Project Manager (Lead)</label>
                                        <select id="wizard-pm-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                                            <option value="">Select Lead...</option>
                                            <!-- JS Injected -->
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Date & Time</label>
                                        <input type="datetime-local" id="wizard-date" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Crew Members</label>
                                    <div class="flex flex-wrap gap-2" id="wizard-crew-select">
                                        <!-- JS Injected Checkboxes -->
                                    </div>
                                </div>
                                <!-- Required Assets -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Required Assets (Optional)</label>
                                    <div class="bg-white border border-slate-300 rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-project-assets">
                                        <!-- JS Injected -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Unplanned Appointments Form -->
                        <div id="wizard-form-appointments" class="hidden space-y-6">
                            <h3 class="text-lg font-bold text-slate-800">Resolve Unplanned Appointments</h3>
                            <p class="text-sm text-slate-500">These calendar entries have crew and time but lack specific task definitions.</p>
                            
                            <div class="space-y-3" id="wizard-appointments-list">
                                <!-- JS Injected List of Appointments -->
                            </div>

                            <div id="wizard-appointment-resolution" class="hidden mt-6 pt-6 border-t border-slate-200 animate-in fade-in slide-in-from-top-4">
                                <h4 class="font-bold text-slate-800 mb-3">Define Task for Selected Appointment</h4>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="appt-resolve-type" value="link" class="peer hidden" checked onchange="toggleApptResolveType('link')">
                                        <div class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-brandDark peer-checked:bg-blue-50 peer-checked:text-brandDark transition-all text-center text-sm font-bold">
                                            Link to Project Phase
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="appt-resolve-type" value="manual" class="peer hidden" onchange="toggleApptResolveType('manual')">
                                        <div class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-brandDark peer-checked:bg-blue-50 peer-checked:text-brandDark transition-all text-center text-sm font-bold">
                                            Define Manually
                                        </div>
                                    </label>
                                </div>

                                <div id="appt-resolve-link">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Select Checklist Item</label>
                                    <select id="appt-checklist-select" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                                        <!-- Populated via JS -->
                                    </select>
                                </div>

                                <div id="appt-resolve-manual" class="hidden space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Task Title</label>
                                        <input type="text" id="appt-manual-title" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Category</label>
                                        <select id="appt-manual-category" class="w-full p-2 rounded-lg border border-slate-300 text-sm">
                                            <option>General</option>
                                            <option>Electric</option>
                                            <option>Roof</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Assets for Appointments -->
                                <div class="mt-4">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Required Assets (Optional)</label>
                                    <div class="bg-white border border-slate-300 rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-appt-assets">
                                        <!-- JS Injected -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Task Form -->
                        <div id="wizard-form-custom" class="hidden space-y-6">
                            <h3 class="text-lg font-bold text-slate-800">Create Custom Task</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Task Title</label>
                                    <input type="text" id="wizard-custom-title" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brandDark/20 outline-none" placeholder="e.g. Emergency Repair">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                                    <textarea id="wizard-custom-desc" rows="3" class="w-full p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brandDark/20 outline-none" placeholder="Details..."></textarea>
                                </div>
                                
                                <!-- Task Steps / Checklist -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Task Checklist Steps</label>
                                    <div class="flex gap-2 mb-2">
                                        <input type="text" id="wizard-custom-step-input" class="flex-1 p-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brandDark/20 outline-none" placeholder="Add a step (e.g. Turn off power)..." onkeypress="if(event.key === 'Enter') addWizardStep()">
                                        <button onclick="addWizardStep()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 rounded-xl font-bold transition-colors">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                    <div id="wizard-custom-steps-list" class="space-y-2 max-h-32 overflow-y-auto">
                                        <!-- Steps injected here -->
                                        <div class="text-xs text-slate-400 italic p-2">No steps added yet.</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Duration (Hours)</label>
                                        <input type="number" id="wizard-custom-duration" class="w-full p-3 rounded-xl border border-slate-300" value="1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                                        <select id="wizard-custom-category" class="w-full p-3 rounded-xl border border-slate-300">
                                            <option>General</option>
                                            <option>Electric</option>
                                            <option>Roof</option>
                                            <option>Cleanup</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Assets for Custom Task -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Required Assets (Optional)</label>
                                    <div class="bg-white border border-slate-300 rounded-lg p-2 max-h-32 overflow-y-auto grid grid-cols-2 gap-2" id="wizard-custom-assets">
                                        <!-- JS Injected -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                    <button onclick="closePlanWizard()" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition-colors">Cancel</button>
                    <button onclick="savePlanWizard()" class="px-8 py-3 rounded-xl font-bold bg-brandDark text-white shadow-lg shadow-brandDark/20 hover:bg-blue-800 transition-transform active:scale-95">
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
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Required Assets</h3>
                        <div id="modal-assigned-assets" class="grid grid-cols-2 gap-2">
                            <!-- JS Injected -->
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

        // ---------------------------
        // HELPERS
        // ---------------------------
        const $ = (id) => document.getElementById(id);

        // ---------------------------
        // DATA MODEL
        // ---------------------------
        const START_HOUR = 8;
        const PIXELS_PER_HOUR = 100;

        const allEmployees = [
            { id: "emp_1", name: "Sadid", role: "Senior Tech", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sadid&backgroundColor=b6e3f4" },
            { id: "emp_2", name: "Nuri", role: "Electrician", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Nuri&backgroundColor=c0aede" },
            { id: "emp_3", name: "Rasuli", role: "Apprentice", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Rasuli&backgroundColor=ffdfbf" },
            // referenced by unplannedAppointments in your demo
            { id: "emp_4", name: "Khan", role: "Inspector", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Khan&backgroundColor=d1fae5" },
        ];

        const assetInventory = [
            { id: "a1", name: "Hammer Drill (Bosch)", type: "Tool", location: "Van 1" },
            { id: "a2", name: "Ladder (Extension)", type: "Tool", location: "Van 1" },
            { id: "a3", name: "Safety Harness Kit", type: "Safety", location: "Personal" },
            { id: "a4", name: "Multimeter", type: "Tool", location: "Personal" },
            { id: "a5", name: "Crimping Tool", type: "Tool", location: "Personal" },
            { id: "a6", name: "Label Printer", type: "Admin", location: "Van 1" },
        ];

        const customerDatabase = [
            {
            id: "c1",
            name: "Schmidt Solartechnik",
            projects: [
                { id: "p1", product: "PV System 10kWp", object: "Warehouse A", address: "Industriestr. 5, Berlin" },
                { id: "p1_2", product: "Wallbox Installation", object: "Office Parking", address: "Industriestr. 5, Berlin" },
            ],
            },
            {
            id: "c2",
            name: "Müller GmbH",
            projects: [{ id: "p2", product: "Heat Pump Retrofit", object: "Main Residence", address: "Bachweg 2, Munich" }],
            },
            {
            id: "c3",
            name: "Bäckerei Meyer",
            projects: [{ id: "p3", product: "HVAC Maintenance", object: "Bakery Shop", address: "Dorfplatz 1, Hamburg" }],
            },
        ];

        const mockTasksP1 = [
            {
            id: "t1",
            title: "Gerüstaufbau",
            duration: 2,
            category: "Prep",
            status: "in-progress",
            assignees: ["emp_1", "emp_3"],
            startHour: 8,
            predecessors: [],
            assets: ["a2", "a3"],
            startDate: "Oct 24",
            dueDate: "Oct 25",
            travelTime: "45m",
            arrivalTime: "07:45 AM",
            origin: "Office",
            description: "Setup full safety scaffolding on South and West side.",
            log: [{ time: "08:00", text: "Arrived at site.", type: "info" }],
            expenses: [{ item: "Parking Ticket", cost: "12.50" }],
            files: [{ name: "Safety_Plan_v2.pdf", size: "2.4MB" }],
            subtasks: [
                { text: "Unload truck", completed: true, completedBy: "emp_1", time: "08:15", note: "Site access was tight.", photo: "https://images.unsplash.com/photo-1535732820275-9ffd998cac22?auto=format&fit=crop&w=150&q=80" },
                { text: "Secure base plates", completed: true, completedBy: "emp_3", time: "09:30", note: "All plates secured." },
                { text: "Install safety net", completed: false },
            ],
            },
            {
            id: "t2",
            title: "Dachhaken setzen",
            duration: 4,
            category: "Roof",
            status: "scheduled",
            assignees: ["emp_3"],
            startHour: 10,
            predecessors: ["t1"],
            assets: [],
            startDate: "Oct 24",
            dueDate: "Oct 26",
            travelTime: "15m",
            arrivalTime: "08:00 AM",
            origin: "Site B",
            description: "Install 45x Roof Hooks.",
            log: [],
            expenses: [],
            files: [{ name: "Roof_Layout.pdf", size: "5MB" }],
            subtasks: [
                { text: "Mark positions", completed: false },
                { text: "Drill tiles", completed: false },
                { text: "Screw hooks", completed: false },
            ],
            },
            {
            id: "t5",
            title: "Verkabelung String 1",
            duration: 2,
            category: "Electric",
            status: "paused",
            pauseReason: "Missing Material",
            assignees: ["emp_2"],
            startHour: 10,
            predecessors: [],
            assets: ["a4", "a5"],
            startDate: "Oct 25",
            dueDate: "Oct 25",
            travelTime: "2h",
            arrivalTime: "10:00 AM",
            origin: "Office",
            description: "DC Cabling.",
            log: [{ time: "10:15", text: "Started cable run through attic.", type: "info" }],
            expenses: [{ item: "Extra Cable Ties", cost: "5.00" }],
            files: [{ name: "String_Plan.pdf", size: "1.2MB" }],
            subtasks: [],
            },
            { id: "t3", title: "Schienenmontage", duration: 3, category: "Roof", status: "open", assignees: [], startHour: 0, predecessors: [], assets: [], subtasks: [] },
            { id: "t4", title: "Module verlegen", duration: 5, category: "Roof", status: "open", assignees: [], startHour: 0, predecessors: [], assets: [], subtasks: [] },
        ];

        const checklistTemplate = [
            { title: "Gerüstaufbau", duration: 2, category: "Prep" },
            { title: "Dachhaken setzen", duration: 4, category: "Roof" },
            { title: "Schienenmontage", duration: 3, category: "Roof" },
            { title: "Module verlegen", duration: 5, category: "Roof" },
            { title: "Verkabelung String 1", duration: 2, category: "Electric" },
            { title: "Verkabelung String 2", duration: 2, category: "Electric" },
            { title: "Wechselrichter Montage", duration: 3, category: "Electric" },
            { title: "Baustelle reinigen", duration: 1, category: "Cleanup" },
        ];

        // ---------------------------
        // STATE
        // ---------------------------
        let visibleEmployeeIds = ["emp_1", "emp_2", "emp_3"];
        let currentTasks = [];
        let activeTaskId = null;

        let tempWizardSteps = [];
        let wizardType = "project";
        let activeAppointmentId = null;

        let unplannedAppointments = [
            { id: "appt1", title: "Montage WP", date: "Oct 25, 08:00", crew: ["emp_1", "emp_2"], description: "Heat Pump Install requiring 2 techs." },
            { id: "appt2", title: "Site Inspection", date: "Oct 26, 14:00", crew: ["emp_4"], description: "Check measurements for Roof B." },
            { id: "appt3", title: "Service Call", date: "Oct 27, 09:00", crew: [], description: "Inverter error code 404." },
        ];

        let historyLog = [
            { date: "Today, 10:45", text: 'Sadid marked "Unload Truck" as completed.', type: "success" },
            { date: "Today, 09:15", text: "Dispatcher updated schedule for Task T1.", type: "info" },
            { date: "Yesterday, 16:30", text: "Nuri reported expense: Parking Ticket (€12.50).", type: "warning" },
            { date: "Yesterday, 14:00", text: 'Project "Schmidt Solartechnik" initialized.', type: "info" },
        ];

        // expose shared globals for other segments
        window.__WF = {
            START_HOUR,
            PIXELS_PER_HOUR,
            allEmployees,
            assetInventory,
            customerDatabase,
            mockTasksP1,
            checklistTemplate,
            // state refs (mutated)
            get state() {
            return {
                visibleEmployeeIds,
                currentTasks,
                activeTaskId,
                tempWizardSteps,
                wizardType,
                activeAppointmentId,
                unplannedAppointments,
                historyLog,
            };
            },
            setVisibleEmployeeIds: (v) => (visibleEmployeeIds = v),
            setCurrentTasks: (v) => (currentTasks = v),
            setActiveTaskId: (v) => (activeTaskId = v),
            setTempWizardSteps: (v) => (tempWizardSteps = v),
            setWizardType: (v) => (wizardType = v),
            setActiveAppointmentId: (v) => (activeAppointmentId = v),
            setUnplannedAppointments: (v) => (unplannedAppointments = v),
            setHistoryLog: (v) => (historyLog = v),
            $,
        };

        // ---------------------------
        // CORE FLOW
        // ---------------------------
        function initData() {
            window.renderActiveCrewWidget?.();
            populateCustomerDropdown();

            // Auto-select Schmidt for demo
            const schmidt = customerDatabase.find((c) => c.id === "c1");
            if (schmidt) selectCustomer(schmidt.id, schmidt.name);

            window.renderUnplannedPanel?.();
            window.updateUnplannedCount?.();
        }

        function populateCustomerDropdown() {
            const list = $("customer-dropdown-list");
            if (!list) return;
            list.innerHTML = "";

            customerDatabase.forEach((c) => {
            const item = document.createElement("div");
            item.className =
                "px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 flex flex-col";
            item.innerHTML = `<span class="font-bold">${c.name}</span><span class="text-xs text-slate-400">${c.projects.length} Projects</span>`;
            item.onclick = () => selectCustomer(c.id, c.name);
            item.setAttribute("data-name", c.name.toLowerCase());
            list.appendChild(item);
            });
        }

        function showCustomerDropdown() {
            $("customer-dropdown-list")?.classList.remove("hidden");
            $("customer-chevron")?.classList.add("rotate-180");
        }

        function hideCustomerDropdown() {
            $("customer-dropdown-list")?.classList.add("hidden");
            $("customer-chevron")?.classList.remove("rotate-180");
        }

        function filterCustomerDropdown() {
            const input = $("customer-search-input");
            const list = $("customer-dropdown-list");
            if (!input || !list) return;

            const term = input.value.toLowerCase();
            const items = list.children;
            Array.from(items).forEach((item) => {
            const name = item.getAttribute("data-name") || "";
            if (name.includes(term)) item.classList.remove("hidden");
            else item.classList.add("hidden");
            });
        }

        function selectCustomer(id, name) {
            const input = $("customer-search-input");
            if (!input) return;
            input.value = name;
            input.setAttribute("data-selected-id", id);
            handleCustomerChange(id);
            hideCustomerDropdown();
        }

        function handleCustomerChange(customerId) {
            const projectSelect = $("project-selector");
            if (!projectSelect) return;

            projectSelect.innerHTML = '<option value="">Select Product & Site...</option>';

            if (!customerId) {
            projectSelect.disabled = true;
            changeProject("");
            return;
            }

            const customer = customerDatabase.find((c) => c.id === customerId);
            if (!customer) return;

            customer.projects.forEach((p) => {
            projectSelect.innerHTML += `<option value="${p.id}">${p.product} - ${p.object} (${p.address})</option>`;
            });

            projectSelect.disabled = false;

            if (customer.projects.length > 0) {
            projectSelect.selectedIndex = 1;
            changeProject(customer.projects[0].id);
            }
        }

        function changeProject(projectId) {
            if (projectId === "p1") {
            currentTasks = JSON.parse(JSON.stringify(mockTasksP1));
            visibleEmployeeIds = ["emp_1", "emp_2", "emp_3"];
            } else if (projectId) {
            currentTasks = [];
            visibleEmployeeIds = [];
            } else {
            currentTasks = [];
            visibleEmployeeIds = [];
            }

            window.renderActiveCrewWidget?.();
            window.renderAllViews?.();
        }

        // LEFT PANEL (optional tabs)
        function switchLeftTab(tabName) {
            const backlog = $("left-tab-backlog");
            const unplanned = $("left-tab-unplanned");
            const btnBacklog = $("left-btn-backlog");
            const btnUnplanned = $("left-btn-unplanned");

            if (!backlog || !unplanned || !btnBacklog || !btnUnplanned) return;

            backlog.classList.add("hidden");
            unplanned.classList.add("hidden");

            btnBacklog.className = "flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all";
            btnUnplanned.className = "flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all";

            $(`left-tab-${tabName}`)?.classList.remove("hidden");
            $(`left-btn-${tabName}`) &&
            ($(`left-btn-${tabName}`).className = "flex-1 py-2 rounded-lg text-sm font-bold bg-white text-slate-800 shadow-sm transition-all");
        }

        // expose functions used by HTML
        window.initData = initData;
        window.populateCustomerDropdown = populateCustomerDropdown;
        window.showCustomerDropdown = showCustomerDropdown;
        window.hideCustomerDropdown = hideCustomerDropdown;
        window.filterCustomerDropdown = filterCustomerDropdown;
        window.selectCustomer = selectCustomer;
        window.handleCustomerChange = handleCustomerChange;
        window.changeProject = changeProject;
        window.switchLeftTab = switchLeftTab;
        })();
    </script> 

    <script>
        (() => {
        "use strict";

        const { $, START_HOUR, PIXELS_PER_HOUR, allEmployees, assetInventory } = window.__WF;

        // ---------------------------
        // VIEW RENDERING
        // ---------------------------
        function renderAllViews() {
            renderChecklist();
            renderBoard();
            renderGantt();
            renderList();
            renderCalendar();
            renderHistory();
            updateTaskCount();
        }

        function renderUnplannedPanel() {
            const s = window.__WF.state;
            const container = $("unplanned-source");
            if (!container) return;

            container.innerHTML = "";
            s.unplannedAppointments.forEach((appt) => {
            const el = document.createElement("div");
            el.className =
                "glass-card p-3 rounded-xl cursor-grab group active:cursor-grabbing hover:border-orange-300 border-l-4 border-l-orange-400";
            el.innerHTML = `
                <div class="flex justify-between items-start">
                <h4 class="font-bold text-slate-700 text-sm leading-tight">${appt.title}</h4>
                <span class="text-[10px] bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded font-bold">Unplanned</span>
                </div>
                <div class="text-xs text-slate-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> ${appt.date}</div>
                <p class="text-xs text-slate-400 mt-2 italic line-clamp-2">${appt.description || ""}</p>
            `;
            container.appendChild(el);
            });
        }

        function updateUnplannedCount() {
            const s = window.__WF.state;
            const el = $("unplanned-count");
            if (el) el.innerText = String(s.unplannedAppointments.length);
        }

        function renderChecklist() {
            const s = window.__WF.state;
            const list = $("checklist-source");
            if (!list) return;

            list.innerHTML = "";
            s.currentTasks
            .filter((t) => t.status === "open")
            .forEach((task) => {
                list.appendChild(renderTaskCard(task, false));
            });
        }

        function getAvatarStack(assigneeIds) {
            if (!assigneeIds || assigneeIds.length === 0) return "";
            let html = '<div class="flex -space-x-2">';
            assigneeIds.forEach((id) => {
            const emp = allEmployees.find((e) => e.id === id);
            if (emp) html += `<img src="${emp.avatar}" class="w-6 h-6 rounded-full border-2 border-white" title="${emp.name}">`;
            });
            html += "</div>";
            return html;
        }

        function getStatusBadge(status, reason) {
            if (status === "scheduled") return '<span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold">Scheduled</span>';
            if (status === "in-progress")
            return '<span class="bg-green-100 text-green-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold animate-pulse">In Progress</span>';
            if (status === "paused")
            return `<span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold" title="${reason || ""}">Paused: ${
                reason || "—"
            }</span>`;
            return "";
        }

        function renderTaskCard(task, isBoard = true) {
            const div = document.createElement("div");

            if (!isBoard) {
            div.className =
                "glass-card p-3 rounded-xl flex items-center justify-between cursor-grab group active:cursor-grabbing hover:border-sky/50";
            } else {
            div.className = "bg-white shadow-md border-l-4 border-l-actionGreen p-3 rounded-xl mb-3 cursor-grab relative group";
            }

            div.setAttribute("data-id", task.id);
            div.setAttribute("data-title", (task.title || "").toLowerCase());
            div.setAttribute("onclick", `openModal('${task.id}')`);

            let catColor = "bg-slate-200 text-slate-600";
            if (task.category === "Electric") catColor = "bg-brandDark/10 text-brandDark";
            if (task.category === "Roof") catColor = "bg-sky/20 text-sky-700";

            const avatarHtml = task.assignees?.length ? getAvatarStack(task.assignees) : "";
            const statusHtml = getStatusBadge(task.status, task.pauseReason);

            let actionBtn = "";
            if (isBoard) {
            if (task.status === "in-progress") {
                actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-pause"></i></button>`;
            } else {
                actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-play"></i></button>`;
            }
            }

            let travelHtml = "";
            if (isBoard && task.travelTime) {
            travelHtml = `
                <div class="bg-slate-50 p-2 rounded-lg mt-2 border border-slate-100 flex justify-between items-center text-xs">
                <div>
                    <div class="font-bold text-slate-700 flex items-center gap-1"><i class="fa-solid fa-car-side text-slate-400"></i> ${task.travelTime} from ${task.origin}</div>
                    <div class="text-slate-400 pl-4">Arr: ${task.arrivalTime}</div>
                </div>
                <button class="text-blue-500 hover:text-blue-700 p-1" title="View Route on Map" onclick="event.stopPropagation(); alert('Opening Google Maps Route: ${task.origin} to Site')">
                    <i class="fa-solid fa-map-location-dot text-lg"></i>
                </button>
                </div>
            `;
            }

            let dateHtml = "";
            if (isBoard && task.startDate) {
            dateHtml = `<div class="text-[10px] text-slate-400 mt-2 flex items-center gap-1"><i class="fa-regular fa-calendar"></i> ${task.startDate} - ${task.dueDate}</div>`;
            }

            if (!isBoard) {
            div.innerHTML = `
                <div class="flex items-center gap-3 pointer-events-none w-full">
                <div class="text-slate-300 group-hover:text-brandDark transition-colors drag-handle pointer-events-auto">
                    <i class="fa-solid fa-grip-vertical"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-700 text-sm leading-tight">${task.title}</h4>
                    <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-bold ${catColor} px-1.5 py-0.5 rounded">${task.category}</span>
                    <span class="text-[10px] text-slate-400 font-medium">${task.duration}h</span>
                    </div>
                </div>
                </div>
            `;
            } else {
            div.innerHTML = `
                <div class="flex justify-between items-start mb-1">
                <div class="flex-1 min-w-0 pr-2">
                    <h4 class="font-bold text-slate-700 text-sm leading-tight truncate">${task.title}</h4>
                    <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-bold ${catColor} px-1.5 py-0.5 rounded">${task.category}</span>
                    <span class="text-[10px] text-slate-400 font-medium"><i class="fa-regular fa-clock mr-1"></i>${task.duration}h</span>
                    </div>
                </div>
                ${avatarHtml}
                </div>

                ${dateHtml}
                ${travelHtml}

                <div class="mt-3 flex justify-between items-center border-t border-slate-100 pt-2">
                ${statusHtml}
                ${actionBtn}
                </div>
            `;
            }

            return div;
        }

        function renderList() {
            const s = window.__WF.state;
            const container = $("list-body");
            if (!container) return;

            container.innerHTML = "";
            s.currentTasks.forEach((task) => {
            const row = document.createElement("div");
            row.className =
                "grid grid-cols-12 gap-4 p-3 border-b border-slate-100 bg-white rounded-lg hover:shadow-sm transition-all items-center cursor-pointer";
            row.onclick = () => window.openModal?.(task.id);

            const statusBadge = getStatusBadge(task.status, task.pauseReason);

            let actionBtn = "";
            if (task.status !== "open") {
                if (task.status === "in-progress") {
                actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-pause"></i></button>`;
                } else {
                actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-play"></i></button>`;
                }
            }

            let routeInfo = '<span class="text-xs text-slate-400">-</span>';
            if (task.travelTime) {
                routeInfo = `
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-route text-slate-400"></i>
                    <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-600">${task.travelTime}</span>
                    <span class="text-[10px] text-slate-400">Arr: ${task.arrivalTime}</span>
                    </div>
                </div>
                `;
            }

            row.innerHTML = `
                <div class="col-span-4 flex items-center gap-3">
                <div class="w-2 h-2 rounded-full ${task.category === "Electric" ? "bg-brandDark" : "bg-sky-400"}"></div>
                <div>
                    <div class="font-semibold text-sm text-slate-700">${task.title}</div>
                    <div class="text-xs text-slate-400 flex items-center gap-1">${getAvatarStack(task.assignees || [])}</div>
                </div>
                </div>
                <div class="col-span-2 text-xs text-slate-500">
                ${task.startDate ? `<div>${task.startDate}</div><div class="text-red-400">Due: ${task.dueDate}</div>` : "-"}
                </div>
                <div class="col-span-3">${routeInfo}</div>
                <div class="col-span-1 text-xs text-slate-500 font-mono">${task.duration}h</div>
                <div class="col-span-2 flex justify-end items-center gap-3">
                ${statusBadge}
                ${actionBtn}
                </div>
            `;
            container.appendChild(row);
            });
        }

        function renderBoard() {
            const s = window.__WF.state;
            const container = $("view-board");
            if (!container) return;

            container.innerHTML = "";

            s.visibleEmployeeIds.forEach((empId) => {
            const emp = allEmployees.find((e) => e.id === empId);
            if (!emp) return;

            const col = document.createElement("div");
            col.className = "glass-panel rounded-[2rem] flex flex-col h-full overflow-hidden";
            col.innerHTML = `
                <div class="p-4 bg-white/40 border-b border-white/20 flex items-center gap-3">
                <img src="${emp.avatar}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                <div>
                    <h3 class="font-bold text-slate-800 leading-tight">${emp.name}</h3>
                    <p class="text-xs text-slate-500">${emp.role}</p>
                </div>
                </div>
                <div id="${emp.id}" class="p-3 flex-1 overflow-y-auto space-y-3 bg-slate-50/30 min-h-[100px]"></div>
            `;
            container.appendChild(col);

            // Sortable initialized in Segment 3 (initSortables), but if you want auto:
            if (window.Sortable) {
                Sortable.create(col.querySelector(`#${emp.id}`), {
                group: "shared",
                animation: 150,
                ghostClass: "sortable-ghost",
                onAdd: function (evt) {
                    const taskId = evt.item.getAttribute("data-id");
                    updateTaskStatus(taskId, "scheduled", [empId]);
                },
                });
            }
            });

            s.currentTasks
            .filter((t) => t.status !== "open")
            .forEach((task) => {
                if (task.assignees?.length) {
                const leadId = task.assignees[0];
                if (s.visibleEmployeeIds.includes(leadId)) {
                    const col = $(leadId);
                    if (col) col.appendChild(renderTaskCard(task));
                }
                }
            });
        }

        function toggleTaskStatus(taskId, event) {
            event?.stopPropagation?.();

            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === taskId);
            if (!task) return;

            if (task.status === "in-progress") {
            const reason = prompt("Reason for pausing this task?", "Break");
            if (reason) {
                task.status = "paused";
                task.pauseReason = reason;
                s.historyLog.unshift({ date: "Just now", text: `Task "${task.title}" paused: ${reason}`, type: "warning" });
            }
            } else {
            task.status = "in-progress";
            task.pauseReason = null;
            s.historyLog.unshift({ date: "Just now", text: `Task "${task.title}" started.`, type: "info" });
            }

            renderAllViews();
        }

        // ---------------------------
        // CALENDAR
        // ---------------------------
        function renderCalendar() {
            const s = window.__WF.state;
            const container = $("calendar-body");
            if (!container) return;

            container.innerHTML = "";

            // demo: Oct (31 days), Sunday offset example
            const startDayOffset = 6;
            for (let i = 0; i < startDayOffset; i++) {
            container.innerHTML += `<div class="calendar-day bg-slate-100"></div>`;
            }

            for (let day = 1; day <= 31; day++) {
            const isToday = day === 24;
            let dayHtml = `
                <div class="calendar-day ${isToday ? "today ring-2 ring-inset ring-blue-200" : ""} p-2 hover:bg-slate-50 transition-colors group">
                <div class="text-right text-xs font-bold text-slate-400 mb-1">${day}</div>
                <div class="space-y-1">
            `;

            const dayString = `Oct ${day}`;

            s.currentTasks.forEach((task) => {
                if (task.startDate === dayString || task.dueDate === dayString) {
                let colorClass = "bg-sky-100 text-sky-700 border-sky-200";
                if (task.category === "Electric") colorClass = "bg-indigo-100 text-indigo-700 border-indigo-200";
                if (task.status === "in-progress") colorClass = "bg-green-100 text-green-700 border-green-200 animate-pulse";

                dayHtml += `
                    <div class="text-[10px] px-1.5 py-1 rounded border ${colorClass} truncate cursor-pointer hover:opacity-80" onclick="openModal('${task.id}')">
                    ${task.title}
                    </div>
                `;
                }
            });

            s.unplannedAppointments.forEach((appt) => {
                if (appt.date.includes(`Oct ${day}`)) {
                dayHtml += `
                    <div class="text-[10px] px-1.5 py-1 rounded border bg-orange-100 text-orange-700 border-orange-200 truncate cursor-pointer border-dashed">
                    <i class="fa-regular fa-clock mr-1"></i> ${appt.title}
                    </div>
                `;
                }
            });

            dayHtml += `</div></div>`;
            container.innerHTML += dayHtml;
            }
        }

        // ---------------------------
        // HISTORY
        // ---------------------------
        function renderHistory() {
            const s = window.__WF.state;
            const list = $("history-list");
            if (!list) return;

            list.innerHTML = "";
            s.historyLog.forEach((log) => {
            let icon = "fa-info-circle text-blue-500";
            let bg = "bg-blue-50";
            if (log.type === "success") {
                icon = "fa-check-circle text-green-500";
                bg = "bg-green-50";
            }
            if (log.type === "warning") {
                icon = "fa-exclamation-triangle text-orange-500";
                bg = "bg-orange-50";
            }

            list.innerHTML += `
                <div class="flex gap-4 p-3 rounded-xl border border-slate-100 hover:bg-white transition-colors">
                <div class="mt-1">
                    <div class="w-8 h-8 rounded-full ${bg} flex items-center justify-center">
                    <i class="fa-solid ${icon}"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">${log.text}</p>
                    <span class="text-xs text-slate-400">${log.date}</span>
                </div>
                </div>
            `;
            });
        }

        // ---------------------------
        // CREW WIDGET
        // ---------------------------
        function renderActiveCrewWidget() {
            const s = window.__WF.state;
            const container = $("active-crew-avatars");
            if (!container) return;

            container.innerHTML = "";
            if (s.visibleEmployeeIds.length === 0) {
            container.innerHTML = '<span class="text-xs text-slate-400 italic pl-2">No crew selected</span>';
            return;
            }

            s.visibleEmployeeIds.forEach((id) => {
            const emp = allEmployees.find((e) => e.id === id);
            if (!emp) return;
            const img = document.createElement("img");
            img.src = emp.avatar;
            img.className =
                "w-6 h-6 rounded-full border border-white ring-1 ring-slate-200 cursor-pointer hover:scale-110 transition-transform";
            img.title = emp.name;
            container.appendChild(img);
            });
        }

        function openCrewModal() {
            const container = document.getElementById('crew-list-container');
            container.innerHTML = '';
            allEmployees.forEach(emp => {
                const isSelected = visibleEmployeeIds.includes(emp.id);
                const el = document.createElement('div');
                el.className = `flex items-center justify-between p-3 rounded-lg cursor-pointer transition-colors border ${isSelected ? 'bg-blue-50 border-blue-200' : 'hover:bg-slate-50 border-transparent'}`;
                el.onclick = () => toggleCrewMember(emp.id);
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <img src="${emp.avatar}" class="w-10 h-10 rounded-full bg-slate-200">
                        <div>
                            <div class="font-bold text-sm text-slate-800">${emp.name}</div>
                            <div class="text-xs text-slate-500">${emp.role}</div>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center ${isSelected ? 'border-brandDark bg-brandDark text-white' : 'border-slate-300 text-transparent'}">
                        <i class="fa-solid fa-check text-xs"></i>
                    </div>
                `;
                container.appendChild(el);
            });
            document.getElementById('crew-modal').classList.remove('hidden');
            setTimeout(() => document.getElementById('crew-modal-content').classList.remove('translate-x-full'), 10);
        }

        function toggleCrewMember(empId) {
            if(visibleEmployeeIds.includes(empId)) {
                visibleEmployeeIds = visibleEmployeeIds.filter(id => id !== empId);
            } else {
                visibleEmployeeIds.push(empId);
            }
            openCrewModal(); 
            renderActiveCrewWidget();
            renderAllViews(); 
        }

        function closeCrewModal() {
            document.getElementById('crew-modal-content').classList.add('translate-x-full');
            setTimeout(() => document.getElementById('crew-modal').classList.add('hidden'), 300);
        }

        // ---------------------------
        // TASK COUNT + STATUS
        // ---------------------------
        function updateTaskCount() {
            const s = window.__WF.state;
            const count = s.currentTasks.filter((t) => t.status === "open").length;
            const el = $("task-count");
            if (el) el.innerText = String(count);
        }

        function updateTaskStatus(taskId, status, assigneesArray) {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === taskId);
            if (!task) return;

            const oldStatus = task.status;
            task.status = status;
            if (assigneesArray !== null) task.assignees = assigneesArray;

            if (oldStatus !== status) {
            s.historyLog.unshift({ date: "Just now", text: `Task "${task.title}" moved to ${status}.`, type: "info" });
            }

            renderAllViews();
        }

        // ---------------------------
        // VIEW SWITCH
        // ---------------------------
        function switchView(viewName) {
            document.querySelectorAll(".view-container").forEach((el) => el.classList.add("hidden"));
            document.querySelectorAll('[id^="btn-view-"]').forEach((btn) => {
            btn.className =
                "px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2";
            });

            $(`view-${viewName}`)?.classList.remove("hidden");
            $(`btn-view-${viewName}`) &&
            ($(`btn-view-${viewName}`).className =
                "px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark transition-all flex items-center gap-2");

            if (viewName === "calendar") renderCalendar();
            if (viewName === "history") renderHistory();
            else renderAllViews();
        }

        // ---------------------------
        // GANTT
        // ---------------------------
        function renderGantt() {
            const s = window.__WF.state;

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
            marker.className = "absolute top-0 bottom-0 border-l border-slate-200 pl-1 text-[10px] h-full flex items-center";
            marker.style.left = `${(i - START_HOUR) * PIXELS_PER_HOUR}px`;
            marker.innerText = `${i}:00`;
            timeHeader.appendChild(marker);
            }

            s.visibleEmployeeIds.forEach((empId) => {
            const emp = allEmployees.find((e) => e.id === empId);
            if (!emp) return;

            const row = document.createElement("div");
            row.className = "flex border-b border-slate-200 bg-white/40 h-24 relative group hover:bg-white/60 transition-colors";

            const sidebar = document.createElement("div");
            sidebar.className =
                "w-48 flex-shrink-0 border-r border-slate-200 p-3 flex items-center gap-3 bg-white/50 sticky left-0 z-30 backdrop-blur-sm";
            sidebar.innerHTML = `
                <img src="${emp.avatar}" class="w-10 h-10 rounded-full border border-slate-200">
                <div>
                <div class="font-bold text-sm text-slate-800">${emp.name}</div>
                <div class="text-xs text-slate-500">${emp.role}</div>
                </div>
            `;
            row.appendChild(sidebar);

            const timeline = document.createElement("div");
            timeline.className = "flex-1 relative min-w-[1000px]";

            for (let i = 0; i <= 10; i++) {
                const line = document.createElement("div");
                line.className = "gantt-grid-line";
                line.style.left = `${i * PIXELS_PER_HOUR}px`;
                timeline.appendChild(line);
            }

            const employeeTasks = s.currentTasks.filter((t) => (t.assignees || []).includes(emp.id) && t.status !== "open");
            employeeTasks.forEach((task) => {
                const bar = document.createElement("div");
                bar.className = "gantt-bar bg-sky-200 border border-sky-300 text-sky-900";
                bar.style.left = `${(task.startHour - START_HOUR) * PIXELS_PER_HOUR}px`;
                bar.style.width = `${task.duration * PIXELS_PER_HOUR}px`;
                bar.style.top = "24px";
                bar.innerHTML = `<span class="truncate">${task.title}</span>`;
                bar.id = `gantt-task-${task.id}`;
                bar.onclick = () => window.openModal?.(task.id);
                timeline.appendChild(bar);
            });

            row.appendChild(timeline);
            container.appendChild(row);
            });

            setTimeout(drawGanttLines, 100);
        }

        function drawGanttLines() {
            const s = window.__WF.state;
            const svg = $("gantt-lines");
            const container = $("gantt-body");
            if (!svg || !container) return;

            svg.style.width = `${container.scrollWidth}px`;
            svg.style.height = `${container.scrollHeight}px`;

            svg.innerHTML =
            '<defs><marker id="arrowhead" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#cbd5e1" /></marker></defs>';

            const containerRect = container.getBoundingClientRect();
            const scrollLeft = container.scrollLeft;
            const scrollTop = container.scrollTop;

            s.currentTasks.forEach((task) => {
            if (task.predecessors?.length && task.status === "scheduled") {
                const toEl = document.getElementById(`gantt-task-${task.id}`);
                if (!toEl) return;

                task.predecessors.forEach((predId) => {
                const fromEl = document.getElementById(`gantt-task-${predId}`);
                if (!fromEl) return;

                const fromRect = fromEl.getBoundingClientRect();
                const toRect = toEl.getBoundingClientRect();

                const x1 = fromRect.right - containerRect.left + scrollLeft;
                const y1 = fromRect.top + fromRect.height / 2 - containerRect.top + scrollTop;
                const x2 = toRect.left - containerRect.left + scrollLeft;
                const y2 = toRect.top + toRect.height / 2 - containerRect.top + scrollTop;

                const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                path.setAttribute("d", `M ${x1} ${y1} C ${x1 + 40} ${y1}, ${x2 - 40} ${y2}, ${x2} ${y2}`);
                path.setAttribute("stroke", "#cbd5e1");
                path.setAttribute("stroke-width", "2");
                path.setAttribute("fill", "none");
                path.setAttribute("marker-end", "url(#arrowhead)");
                svg.appendChild(path);
                });
            }
            });
        }

        // expose for Segment 3 + HTML handlers
        window.renderAllViews = renderAllViews;
        window.renderChecklist = renderChecklist;
        window.renderBoard = renderBoard;
        window.renderGantt = renderGantt;
        window.drawGanttLines = drawGanttLines;
        window.renderList = renderList;
        window.renderCalendar = renderCalendar;
        window.renderHistory = renderHistory;

        window.renderUnplannedPanel = renderUnplannedPanel;
        window.updateUnplannedCount = updateUnplannedCount;

        window.getAvatarStack = getAvatarStack;
        window.getStatusBadge = getStatusBadge;
        window.renderTaskCard = renderTaskCard;

        window.toggleTaskStatus = toggleTaskStatus;
        window.updateTaskStatus = updateTaskStatus;
        window.updateTaskCount = updateTaskCount;

        window.switchView = switchView;
        window.renderActiveCrewWidget = renderActiveCrewWidget;
        })();
    </script> 
    <script>
        (() => {
        "use strict";

        const { $, allEmployees, assetInventory, checklistTemplate } = window.__WF;

        // ---------------------------
        // TOAST
        // ---------------------------
        function showToast(msg) {
            const toast = $("toast");
            if (!toast) return;

            const h4 = toast.querySelector("div h4");
            if (h4) h4.innerText = msg;

            toast.classList.remove("translate-y-20", "opacity-0");
            setTimeout(() => toast.classList.add("translate-y-20", "opacity-0"), 3000);
        }

        // ---------------------------
        // MODAL TABS
        // ---------------------------
        function switchModalTab(tabId) {
            document.querySelectorAll(".tab-content").forEach((el) => el.classList.remove("active"));
            document.querySelectorAll(".tab-btn").forEach((el) => el.classList.remove("active"));
            $(`tab-${tabId}`)?.classList.add("active");
            $(`tab-btn-${tabId}`)?.classList.add("active");
        }

        // ---------------------------
        // MODAL OPEN / CLOSE
        // ---------------------------
        function openModal(taskId) {
            window.__WF.setActiveTaskId(taskId);

            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === taskId);
            if (!task) return;

            switchModalTab("overview");

            $("modal-edit-title") && ($("modal-edit-title").value = task.title);
            $("modal-edit-category") && ($("modal-edit-category").value = task.category);
            $("modal-edit-duration") && ($("modal-edit-duration").value = task.duration);
            $("modal-edit-description") && ($("modal-edit-description").value = task.description || "");

            const assigneeText = task.assignees?.length ? `${task.assignees.length} Techs Assigned` : "Unassigned";
            $("modal-assignee-text") && ($("modal-assignee-text").innerText = assigneeText);

            $("modal-schedule") && ($("modal-schedule").innerText = task.startDate ? `${task.startDate} - ${task.dueDate}` : "--");
            $("modal-travel") && ($("modal-travel").innerText = task.travelTime ? `${task.travelTime} from ${task.origin}` : "--");

            renderModalTaskAssignees(task);
            $("modal-task-crew-select")?.classList.add("hidden");

            // assets
            const assetsDiv = $("modal-assigned-assets-container");
            const assetContainer = $("modal-assigned-assets");
            if (assetsDiv && assetContainer) {
            assetContainer.innerHTML = "";
            if (task.assets?.length) {
                assetsDiv.classList.remove("hidden");
                task.assets.forEach((assetId) => {
                const asset = assetInventory.find((a) => a.id === assetId);
                if (!asset) return;
                assetContainer.innerHTML += `
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2 py-1.5 rounded text-xs">
                    <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>
                    <span class="font-medium text-slate-700">${asset.name}</span>
                    </div>
                `;
                });
            } else {
                assetsDiv.classList.add("hidden");
            }
            }

            renderDependencyList(task);
            populateDependencyDropdown(task);
            renderModalSubtasks(task);

            // checklist report
            const reportContainer = $("modal-checklist-report");
            if (reportContainer) {
            reportContainer.innerHTML = "";
            if (task.subtasks?.length) {
                let completedCount = 0;
                task.subtasks.forEach((step) => {
                if (!step.completed) return;

                completedCount++;
                const emp = allEmployees.find((e) => e.id === step.completedBy);
                const empName = emp ? emp.name : "Unknown";
                const photoHtml = step.photo
                    ? `<img src="${step.photo}" class="w-20 h-20 object-cover rounded-lg border border-slate-200 mt-2 cursor-pointer hover:opacity-90 shadow-sm" onclick="window.open('${step.photo}', '_blank')">`
                    : "";

                reportContainer.innerHTML += `
                    <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-500 bg-green-50 rounded-full p-1"><i class="fa-solid fa-check text-xs"></i></div>
                        <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-bold text-slate-800 line-through decoration-slate-400 decoration-2">${step.text}</span>
                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">${step.time || "--:--"}</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-user-check"></i> Verified by <span class="font-semibold text-slate-700">${empName}</span>
                        </div>
                        ${step.note ? `<p class="text-xs text-slate-600 bg-slate-50 p-2 rounded mt-2 border border-slate-100 italic">"${step.note}"</p>` : ""}
                        ${photoHtml}
                        </div>
                    </div>
                    </div>
                `;
                });

                if (completedCount === 0) {
                reportContainer.innerHTML =
                    '<div class="text-sm text-slate-400 italic text-center py-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">No steps completed yet.</div>';
                }
            } else {
                reportContainer.innerHTML =
                '<div class="text-sm text-slate-400 italic text-center py-4">No checklist steps defined for this task.</div>';
            }
            }

            // activity log
            const logContainer = $("modal-activity-log");
            if (logContainer) {
            logContainer.innerHTML = "";
            if (task.log?.length) {
                task.log.forEach((entry) => {
                const hasPhoto = entry.photo
                    ? '<div class="mt-2 w-20 h-20 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-300 cursor-pointer"><i class="fa-solid fa-image"></i></div>'
                    : "";
                logContainer.innerHTML += `
                    <div class="relative pb-4">
                    <div class="absolute -left-[29px] top-1 w-3 h-3 bg-actionGreen rounded-full border-2 border-white ring-2 ring-slate-100"></div>
                    <span class="text-xs font-bold text-slate-400 mb-1 block">${entry.time}</span>
                    <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100">${entry.text}</p>
                    ${hasPhoto}
                    </div>
                `;
                });
            } else {
                logContainer.innerHTML = '<div class="text-sm text-slate-400 italic pl-2">No activity recorded yet.</div>';
            }
            }

            // financials
            const finContainer = $("modal-financials");
            if (finContainer) {
            finContainer.innerHTML = "";
            if (task.expenses?.length) {
                task.expenses.forEach((exp) => {
                finContainer.innerHTML += `
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-100 text-sm">
                    <span class="text-red-800 font-medium"><i class="fa-solid fa-receipt mr-2"></i> ${exp.item}</span>
                    <span class="font-bold text-slate-800">€${exp.cost}</span>
                    </div>
                `;
                });
            } else {
                finContainer.innerHTML = '<div class="text-sm text-slate-400 italic">No expenses reported.</div>';
            }
            }

            // files
            const filesContainer = $("modal-files-list");
            if (filesContainer) {
            filesContainer.innerHTML = "";
            if (task.files?.length) {
                task.files.forEach((f) => {
                filesContainer.innerHTML += `
                    <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <div class="w-10 h-10 bg-red-100 text-red-500 rounded flex items-center justify-center"><i class="fa-solid fa-file-pdf"></i></div>
                    <div class="flex-1">
                        <div class="text-sm font-bold text-slate-800">${f.name}</div>
                        <div class="text-xs text-slate-500">${f.size}</div>
                    </div>
                    <i class="fa-solid fa-download text-slate-400"></i>
                    </div>
                `;
                });
            } else {
                filesContainer.innerHTML = '<div class="text-sm text-slate-400 italic">No files attached.</div>';
            }
            }

            $("task-modal")?.classList.remove("hidden");
            setTimeout(() => $("task-modal-content")?.classList.remove("translate-x-full"), 10);
        }

        function closeModal() {
            $("task-modal-content")?.classList.add("translate-x-full");
            setTimeout(() => $("task-modal")?.classList.add("hidden"), 300);
        }

        function saveActiveTask() {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            task.title = $("modal-edit-title")?.value ?? task.title;
            task.category = $("modal-edit-category")?.value ?? task.category;
            task.duration = parseFloat($("modal-edit-duration")?.value || "") || task.duration;
            task.description = $("modal-edit-description")?.value ?? task.description;

            s.historyLog.unshift({ date: "Just now", text: `Task "${task.title}" updated by dispatcher.`, type: "info" });

            window.renderAllViews?.();
            closeModal();
            showToast("Task updated successfully.");
        }

        function deleteActiveTask() {
            const s = window.__WF.state;
            if (!confirm("Are you sure you want to delete this task?")) return;

            window.__WF.setCurrentTasks(s.currentTasks.filter((t) => t.id !== s.activeTaskId));
            s.historyLog.unshift({ date: "Just now", text: "Task deleted.", type: "warning" });

            window.renderAllViews?.();
            closeModal();
            showToast("Task deleted.");
        }

        // ---------------------------
        // DEPENDENCIES
        // ---------------------------
        function renderDependencyList(task) {
            const list = $("modal-dependency-list");
            if (!list) return;

            list.innerHTML = "";
            if (!task.predecessors?.length) {
            list.innerHTML = '<div class="text-xs text-slate-400 italic bg-slate-50 p-2 rounded border border-slate-100">No dependencies linked.</div>';
            return;
            }

            const s = window.__WF.state;
            task.predecessors.forEach((pid) => {
            const predTask = s.currentTasks.find((t) => t.id === pid);
            if (!predTask) return;

            list.innerHTML += `
                <div class="flex items-center justify-between bg-blue-50 border border-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-link text-xs"></i>
                    <span class="font-medium">${predTask.title}</span>
                </span>
                <button onclick="removeDependency('${pid}')" class="text-blue-400 hover:text-red-500 transition-colors p-1" title="Remove dependency">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                </div>
            `;
            });
        }

        function populateDependencyDropdown(task) {
            const select = $("modal-dependency-select");
            if (!select) return;

            const s = window.__WF.state;
            select.innerHTML = '<option value="">Select a predecessor task...</option>';

            s.currentTasks.forEach((t) => {
            if (t.id !== task.id && !(task.predecessors || []).includes(t.id)) {
                select.innerHTML += `<option value="${t.id}">${t.title}</option>`;
            }
            });
        }

        function addDependency() {
            const s = window.__WF.state;
            const select = $("modal-dependency-select");
            if (!select) return;

            const predId = select.value;
            if (!predId) return;

            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            task.predecessors = task.predecessors || [];
            task.predecessors.push(predId);

            renderDependencyList(task);
            populateDependencyDropdown(task);

            if (!$("view-gantt")?.classList.contains("hidden")) window.drawGanttLines?.();
        }

        function removeDependency(predId) {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            task.predecessors = (task.predecessors || []).filter((id) => id !== predId);

            renderDependencyList(task);
            populateDependencyDropdown(task);

            if (!$("view-gantt")?.classList.contains("hidden")) window.drawGanttLines?.();
        }

        // ---------------------------
        // ASSIGNEES (modal)
        // ---------------------------
        function renderModalTaskAssignees(task) {
            const container = $("modal-task-assignees");
            if (!container) return;

            container.innerHTML = "";
            if (!task.assignees?.length) {
            container.innerHTML = '<span class="text-sm text-slate-400 italic">No crew assigned yet. Click Edit Crew.</span>';
            return;
            }

            task.assignees.forEach((id) => {
            const emp = allEmployees.find((e) => e.id === id);
            if (!emp) return;
            container.innerHTML += `
                <div class="flex items-center gap-2 bg-white px-2 py-1 rounded border border-slate-200">
                <img src="${emp.avatar}" class="w-5 h-5 rounded-full">
                <span class="text-xs font-bold text-slate-700">${emp.name}</span>
                </div>
            `;
            });
        }

        function toggleTaskCrewEditor() {
            const s = window.__WF.state;
            const area = $("modal-task-crew-select");
            const container = $("modal-task-crew-checkboxes");
            if (!area || !container) return;

            const isHidden = area.classList.contains("hidden");
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            if (isHidden) {
            area.classList.remove("hidden");
            container.innerHTML = "";

            s.visibleEmployeeIds.forEach((id) => {
                const emp = allEmployees.find((e) => e.id === id);
                if (!emp) return;

                const isChecked = (task.assignees || []).includes(id) ? "checked" : "";
                container.innerHTML += `
                <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                    <input type="checkbox" ${isChecked} onchange="updateTaskAssignee('${id}', this.checked)" class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-slate-700">${emp.name}</span>
                </label>
                `;
            });
            } else {
            area.classList.add("hidden");
            }
        }

        function updateTaskAssignee(empId, isChecked) {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            task.assignees = task.assignees || [];
            if (isChecked) {
            if (!task.assignees.includes(empId)) task.assignees.push(empId);
            } else {
            task.assignees = task.assignees.filter((id) => id !== empId);
            }

            task.status = task.assignees.length === 0 ? "open" : "scheduled";

            renderModalTaskAssignees(task);
            window.renderAllViews?.();
        }

        // ---------------------------
        // SUBTASKS (modal)
        // ---------------------------
        function renderModalSubtasks(task) {
            const container = $("modal-subtasks-list");
            if (!container) return;

            container.innerHTML = "";
            if (!task.subtasks?.length) {
            container.innerHTML = '<div class="text-xs text-slate-400 italic">No sub-tasks defined.</div>';
            return;
            }

            task.subtasks.forEach((step, idx) => {
            const isChecked = step.completed ? "checked" : "";
            const style = step.completed ? "line-through text-slate-400" : "text-slate-700";
            container.innerHTML += `
                <div class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded group">
                <input type="checkbox" ${isChecked} onchange="toggleSubtask(${idx}, this.checked)" class="rounded text-brandDark focus:ring-brandDark cursor-pointer">
                <span class="text-sm ${style} flex-1">${step.text}</span>
                <button onclick="removeSubtask(${idx})" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"><i class="fa-solid fa-times"></i></button>
                </div>
            `;
            });
        }

        function toggleSubtask(index, completed) {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task?.subtasks?.[index]) return;

            task.subtasks[index].completed = completed;
            renderModalSubtasks(task);
        }

        function removeSubtask(index) {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task?.subtasks) return;

            task.subtasks.splice(index, 1);
            renderModalSubtasks(task);
        }

        function addTaskSubtask() {
            const s = window.__WF.state;
            const task = s.currentTasks.find((t) => t.id === s.activeTaskId);
            if (!task) return;

            const text = prompt("New step description:");
            if (!text) return;

            task.subtasks = task.subtasks || [];
            task.subtasks.push({ text, completed: false });
            renderModalSubtasks(task);
        }

        // ---------------------------
        // WIZARD: custom steps
        // ---------------------------
        function addWizardStep() {
            const s = window.__WF.state;
            const input = $("wizard-custom-step-input");
            if (!input) return;

            const val = input.value.trim();
            if (!val) return;

            s.tempWizardSteps.push({ text: val, completed: false });
            input.value = "";
            renderWizardSteps();
        }

        function removeWizardStep(index) {
            const s = window.__WF.state;
            s.tempWizardSteps.splice(index, 1);
            renderWizardSteps();
        }

        function renderWizardSteps() {
            const s = window.__WF.state;
            const container = $("wizard-custom-steps-list");
            if (!container) return;

            container.innerHTML = "";
            if (!s.tempWizardSteps.length) {
            container.innerHTML = '<div class="text-xs text-slate-400 italic p-2">No steps added yet.</div>';
            return;
            }

            s.tempWizardSteps.forEach((step, idx) => {
            container.innerHTML += `
                <div class="flex items-center justify-between bg-slate-50 p-2 rounded text-sm border border-slate-100">
                <span class="text-slate-700">${idx + 1}. ${step.text}</span>
                <button onclick="removeWizardStep(${idx})" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                </div>
            `;
            });
        }
        
        // ---------------------------
        // SORTABLES
        // ---------------------------
        function initSortables() {
            if (!window.Sortable) return;

            const sourceList = $("checklist-source");
            if (sourceList) {
            Sortable.create(sourceList, {
                group: { name: "shared", pull: true, put: true },
                animation: 150,
                ghostClass: "sortable-ghost",
                sort: false,
                onAdd: function (evt) {
                const taskId = evt.item.getAttribute("data-id");
                window.updateTaskStatus?.(taskId, "open", []);
                },
            });
            }

            const unplannedList = $("unplanned-source");
            if (unplannedList) {
            Sortable.create(unplannedList, {
                group: { name: "shared", pull: "clone", put: false },
                animation: 150,
                sort: false,
            });
            }
        }

        // ---------------------------
        // SEARCH INPUT
        // ---------------------------
        function initSearch() {
            const input = $("task-search");
            if (!input) return;

            input.addEventListener("input", (e) => {
            const term = (e.target.value || "").toLowerCase();
            document.querySelectorAll("#checklist-source > div").forEach((item) => {
                const title = (item.getAttribute("data-title") || "").toLowerCase();
                item.style.display = title.includes(term) ? "flex" : "none";
            });
            });
        }

        // ---------------------------
        // EVENTS
        // ---------------------------
        window.addEventListener("resize", () => {
            if (!$("view-gantt")?.classList.contains("hidden")) window.drawGanttLines?.();
        });

        window.addEventListener("DOMContentLoaded", () => {
            initSortables();
            initSearch();
            window.initData?.();
        });

        // expose for HTML onclick
        window.showToast = showToast;

        window.switchModalTab = switchModalTab;
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.saveActiveTask = saveActiveTask;
        window.deleteActiveTask = deleteActiveTask;

        window.renderDependencyList = renderDependencyList;
        window.populateDependencyDropdown = populateDependencyDropdown;
        window.addDependency = addDependency;
        window.removeDependency = removeDependency;

        window.renderModalTaskAssignees = renderModalTaskAssignees;
        window.toggleTaskCrewEditor = toggleTaskCrewEditor;
        window.updateTaskAssignee = updateTaskAssignee;

        window.renderModalSubtasks = renderModalSubtasks;
        window.toggleSubtask = toggleSubtask;
        window.removeSubtask = removeSubtask;
        window.addTaskSubtask = addTaskSubtask;

        window.addWizardStep = addWizardStep;
        window.removeWizardStep = removeWizardStep;
        window.renderWizardSteps = renderWizardSteps;
        })();
    </script>

    <script>
(() => {
  "use strict";

  const $ = (id) => document.getElementById(id);

  // ---------------------------
  // CREW MODAL (fixes openCrewModal not defined + scope issues)
  // ---------------------------
  function openCrewModal() {
    const container = $("crew-list-container");
    if (!container) return;

    const s = window.__WF?.state;
    if (!s) return;

    container.innerHTML = "";

    (window.__WF.allEmployees || []).forEach((emp) => {
      const selectedIds = s.visibleEmployeeIds || [];
      const isSelected = selectedIds.includes(emp.id);

      const row = document.createElement("div");
      row.className =
        `flex items-center justify-between p-3 rounded-lg cursor-pointer transition-colors border ` +
        (isSelected ? "bg-blue-50 border-blue-200" : "hover:bg-slate-50 border-slate-200");

      row.onclick = () => toggleCrewMember(emp.id);

      row.innerHTML = `
        <div class="flex items-center gap-3">
          <img src="${emp.avatar}" class="w-10 h-10 rounded-full bg-slate-200">
          <div>
            <div class="font-bold text-sm text-slate-800">${emp.name}</div>
            <div class="text-xs text-slate-500">${emp.role}</div>
          </div>
        </div>
        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center ${
          isSelected ? "border-brandDark bg-brandDark text-white" : "border-slate-300 text-transparent"
        }">
          <i class="fa-solid fa-check text-xs"></i>
        </div>
      `;

      container.appendChild(row);
    });

    $("crew-modal")?.classList.remove("hidden");
    setTimeout(() => $("crew-modal-content")?.classList.remove("translate-x-full"), 10);
  }

  function closeCrewModal() {
    $("crew-modal-content")?.classList.add("translate-x-full");
    setTimeout(() => $("crew-modal")?.classList.add("hidden"), 250);
  }

  function toggleCrewMember(empId) {
    const s = window.__WF?.state;
    if (!s) return;

    const current = Array.isArray(s.visibleEmployeeIds) ? s.visibleEmployeeIds : [];
    const next = current.includes(empId)
      ? current.filter((id) => id !== empId)
      : [...current, empId];

    window.__WF.setVisibleEmployeeIds(next);

    // re-render
    window.renderActiveCrewWidget?.();
    window.renderAllViews?.();

    // keep modal open and refreshed
    openCrewModal();
  }

  // ---------------------------
  // PLAN WIZARD (fixes openPlanWizard not defined)
  // Minimal functional implementation (open/close + tab switching)
  // ---------------------------
  function openPlanWizard() {
    // optional: show selected customer name
    const input = $("customer-search-input");
    const name = input?.value?.trim() || "Current Customer";
    $("wizard-customer-name") && ($("wizard-customer-name").innerText = name);

    $("plan-wizard-modal")?.classList.remove("hidden");
  }

  function closePlanWizard() {
    $("plan-wizard-modal")?.classList.add("hidden");
  }

  function toggleWizardType(type) {
    // store
    window.__WF?.setWizardType?.(type);

    // show correct panel
    $("wizard-form-project")?.classList.add("hidden");
    $("wizard-form-appointments")?.classList.add("hidden");
    $("wizard-form-custom")?.classList.add("hidden");

    if (type === "project") $("wizard-form-project")?.classList.remove("hidden");
    if (type === "appointments") $("wizard-form-appointments")?.classList.remove("hidden");
    if (type === "custom") $("wizard-form-custom")?.classList.remove("hidden");
  }

  function toggleApptResolveType(type) {
    $("appt-resolve-link")?.classList.add("hidden");
    $("appt-resolve-manual")?.classList.add("hidden");

    if (type === "link") $("appt-resolve-link")?.classList.remove("hidden");
    if (type === "manual") $("appt-resolve-manual")?.classList.remove("hidden");
  }

  function savePlanWizard() {
    // minimal: just close + toast; extend later with real logic
    window.showToast?.("Plan saved.");
    closePlanWizard();
  }

  // ---------------------------
  // Missing buttons referenced in HTML
  // ---------------------------
  function addManualTask() {
    const title = prompt("Manual task title?");
    if (!title) return;

    const duration = parseFloat(prompt("Duration (hours)?", "1") || "1") || 1;

    const s = window.__WF?.state;
    if (!s) return;

    const newTask = {
      id: `m_${Date.now()}`,
      title,
      duration,
      category: "Manual",
      status: "open",
      assignees: [],
      startHour: 0,
      predecessors: [],
      assets: [],
      subtasks: [],
    };

    const nextTasks = [...(s.currentTasks || []), newTask];
    window.__WF.setCurrentTasks(nextTasks);

    window.renderAllViews?.();
    window.showToast?.("Manual task added.");
  }

  function savePlan() {
    window.showToast?.("Plan published.");
  }

  // ---------------------------
  // Expose globals for inline onclick=""
  // ---------------------------
  window.openCrewModal = openCrewModal;
  window.closeCrewModal = closeCrewModal;
  window.toggleCrewMember = toggleCrewMember;

  window.openPlanWizard = openPlanWizard;
  window.closePlanWizard = closePlanWizard;
  window.toggleWizardType = toggleWizardType;
  window.toggleApptResolveType = toggleApptResolveType;
  window.savePlanWizard = savePlanWizard;

  window.addManualTask = addManualTask;
  window.savePlan = savePlan;
})();
</script>


</body>
</html>