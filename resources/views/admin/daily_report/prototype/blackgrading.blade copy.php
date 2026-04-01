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
            <!-- Panel Header -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-actionGreen rounded-full"></span>
                        Master Checklist
                    </h2>
                    <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded-md" id="task-count">0 Tasks</span>
                </div>
                
                <!-- Search & Filters -->
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" id="task-search" placeholder="Search checklist items..." 
                        class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none">
                </div>
            </div>

            <!-- Scrollable List Container -->
            <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto overflow-x-hidden relative">
                <div class="absolute top-0 left-0 w-full h-4 bg-gradient-to-b from-white/50 to-transparent z-10 pointer-events-none"></div>
                
                <!-- The Actual List -->
                <div id="checklist-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                    <!-- Items injected via JS -->
                </div>

                <!-- Add Manual Task Button -->
                <button onclick="addManualTask()" class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Add Manual Task
                </button>
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
                        <button onclick="switchView('list')" id="btn-view-list" class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2">
                            <i class="fa-solid fa-list-check"></i> List
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

    <script>
        // --- DATA MODEL ---
        const START_HOUR = 8; 
        const PIXELS_PER_HOUR = 100;

        const allEmployees = [
            { id: 'emp_1', name: 'Sadid', role: 'Senior Tech', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sadid&backgroundColor=b6e3f4' },
            { id: 'emp_2', name: 'Nuri', role: 'Electrician', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Nuri&backgroundColor=c0aede' },
            { id: 'emp_3', name: 'Rasuli', role: 'Apprentice', avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Rasuli&backgroundColor=ffdfbf' },
        ];

        const assetInventory = [
            { id: 'a1', name: 'Hammer Drill (Bosch)', type: 'Tool', location: 'Van 1' },
            { id: 'a2', name: 'Ladder (Extension)', type: 'Tool', location: 'Van 1' },
            { id: 'a3', name: 'Safety Harness Kit', type: 'Safety', location: 'Personal' },
            { id: 'a4', name: 'Multimeter', type: 'Tool', location: 'Personal' },
            { id: 'a5', name: 'Crimping Tool', type: 'Tool', location: 'Personal' },
            { id: 'a6', name: 'Label Printer', type: 'Admin', location: 'Van 1' }
        ];

        let visibleEmployeeIds = ['emp_1', 'emp_2', 'emp_3']; 
        let tempWizardSteps = []; 
        
        const customerDatabase = [
            {
                id: 'c1',
                name: 'Schmidt Solartechnik',
                projects: [
                    { id: 'p1', product: 'PV System 10kWp', object: 'Warehouse A', address: 'Industriestr. 5, Berlin' },
                    { id: 'p1_2', product: 'Wallbox Installation', object: 'Office Parking', address: 'Industriestr. 5, Berlin' }
                ]
            },
            {
                id: 'c2',
                name: 'Müller GmbH',
                projects: [
                    { id: 'p2', product: 'Heat Pump Retrofit', object: 'Main Residence', address: 'Bachweg 2, Munich' }
                ]
            },
            {
                id: 'c3',
                name: 'Bäckerei Meyer',
                projects: [
                    { id: 'p3', product: 'HVAC Maintenance', object: 'Bakery Shop', address: 'Dorfplatz 1, Hamburg' }
                ]
            }
        ];

        let currentTasks = []; 
        const mockTasksP1 = [
            { 
                id: 't1', title: 'Gerüstaufbau', duration: 2, category: 'Prep', status: 'in-progress', 
                assignees: ['emp_1', 'emp_3'], startHour: 8, predecessors: [], assets: ['a2', 'a3'],
                startDate: 'Oct 24', dueDate: 'Oct 25',
                travelTime: '45m', arrivalTime: '07:45 AM', origin: 'Office',
                description: 'Setup full safety scaffolding on South and West side.',
                log: [{ time: '08:00', text: 'Arrived at site.', type: 'info' }],
                expenses: [{ item: 'Parking Ticket', cost: '12.50' }],
                files: [{ name: 'Safety_Plan_v2.pdf', size: '2.4MB' }],
                subtasks: [
                    { text: 'Unload truck', completed: true, completedBy: 'emp_1', time: '08:15', note: 'Site access was tight.', photo: 'https://images.unsplash.com/photo-1535732820275-9ffd998cac22?auto=format&fit=crop&w=150&q=80' },
                    { text: 'Secure base plates', completed: true, completedBy: 'emp_3', time: '09:30', note: 'All plates secured.' },
                    { text: 'Install safety net', completed: false }
                ]
            },
            { 
                id: 't2', title: 'Dachhaken setzen', duration: 4, category: 'Roof', status: 'scheduled', 
                assignees: ['emp_3'], startHour: 10, predecessors: ['t1'], assets: [],
                startDate: 'Oct 24', dueDate: 'Oct 26',
                travelTime: '15m', arrivalTime: '08:00 AM', origin: 'Site B',
                description: 'Install 45x Roof Hooks.',
                log: [], expenses: [], files: [{ name: 'Roof_Layout.pdf', size: '5MB' }],
                subtasks: [
                    { text: 'Mark positions', completed: false },
                    { text: 'Drill tiles', completed: false },
                    { text: 'Screw hooks', completed: false }
                ]
            },
             { 
                id: 't5', title: 'Verkabelung String 1', duration: 2, category: 'Electric', status: 'paused', pauseReason: 'Missing Material',
                assignees: ['emp_2'], startHour: 10, predecessors: [], assets: ['a4', 'a5'],
                startDate: 'Oct 25', dueDate: 'Oct 25',
                travelTime: '2h', arrivalTime: '10:00 AM', origin: 'Office',
                description: 'DC Cabling.',
                log: [{ time: '10:15', text: 'Started cable run through attic.', type: 'info' }],
                expenses: [{ item: 'Extra Cable Ties', cost: '5.00' }],
                files: [{ name: 'String_Plan.pdf', size: '1.2MB' }],
                subtasks: []
            },
            { id: 't3', title: 'Schienenmontage', duration: 3, category: 'Roof', status: 'open', assignees: [], startHour: 0, predecessors: [], assets: [], subtasks: [] },
            { id: 't4', title: 'Module verlegen', duration: 5, category: 'Roof', status: 'open', assignees: [], startHour: 0, predecessors: [], assets: [], subtasks: [] },
        ];

        let activeTaskId = null;
        
        const checklistTemplate = [
            { title: 'Gerüstaufbau', duration: 2, category: 'Prep' },
            { title: 'Dachhaken setzen', duration: 4, category: 'Roof' },
            { title: 'Schienenmontage', duration: 3, category: 'Roof' },
            { title: 'Module verlegen', duration: 5, category: 'Roof' },
            { title: 'Verkabelung String 1', duration: 2, category: 'Electric' },
            { title: 'Verkabelung String 2', duration: 2, category: 'Electric' },
            { title: 'Wechselrichter Montage', duration: 3, category: 'Electric' },
            { title: 'Baustelle reinigen', duration: 1, category: 'Cleanup' }
        ];
        let unplannedAppointments = [
            { id: 'appt1', title: 'Montage WP', date: 'Oct 25, 08:00', crew: ['emp_1', 'emp_2'] },
            { id: 'appt2', title: 'Site Inspection', date: 'Oct 26, 14:00', crew: ['emp_4'] }
        ];
        let wizardType = 'project'; 
        let activeAppointmentId = null;

        // --- CORE FUNCTIONS ---

        function initData() {
            renderActiveCrewWidget();
            populateCustomerDropdown();
            
            // Auto-select 'Schmidt' for demo
            const schmidt = customerDatabase.find(c => c.id === 'c1');
            if(schmidt) {
                selectCustomer(schmidt.id, schmidt.name);
            }
        }

        function populateCustomerDropdown() {
            const list = document.getElementById('customer-dropdown-list');
            list.innerHTML = '';
            
            customerDatabase.forEach(c => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 flex flex-col';
                item.innerHTML = `<span class="font-bold">${c.name}</span><span class="text-xs text-slate-400">${c.projects.length} Projects</span>`;
                item.onclick = () => selectCustomer(c.id, c.name);
                item.setAttribute('data-name', c.name.toLowerCase());
                list.appendChild(item);
            });
        }

        function showCustomerDropdown() {
            document.getElementById('customer-dropdown-list').classList.remove('hidden');
            document.getElementById('customer-chevron').classList.add('rotate-180');
        }

        function hideCustomerDropdown() {
            document.getElementById('customer-dropdown-list').classList.add('hidden');
            document.getElementById('customer-chevron').classList.remove('rotate-180');
        }

        function filterCustomerDropdown() {
            const term = document.getElementById('customer-search-input').value.toLowerCase();
            const items = document.getElementById('customer-dropdown-list').children;
            Array.from(items).forEach(item => {
                const name = item.getAttribute('data-name');
                if(name.includes(term)) item.classList.remove('hidden');
                else item.classList.add('hidden');
            });
        }

        function selectCustomer(id, name) {
            const input = document.getElementById('customer-search-input');
            input.value = name;
            input.setAttribute('data-selected-id', id); 
            handleCustomerChange(id);
            hideCustomerDropdown();
        }

        function handleCustomerChange(customerId) {
            const projectSelect = document.getElementById('project-selector');
            projectSelect.innerHTML = '<option value="">Select Product & Site...</option>';
            
            if (!customerId) {
                projectSelect.disabled = true;
                changeProject(''); 
                return;
            }

            const customer = customerDatabase.find(c => c.id === customerId);
            if (customer) {
                customer.projects.forEach(p => {
                    projectSelect.innerHTML += `<option value="${p.id}">${p.product} - ${p.object} (${p.address})</option>`;
                });
                projectSelect.disabled = false;
                
                if(customer.projects.length > 0) {
                     projectSelect.selectedIndex = 1;
                     changeProject(customer.projects[0].id);
                }
            }
        }

        function changeProject(projectId) {
            if (projectId === 'p1') {
                currentTasks = JSON.parse(JSON.stringify(mockTasksP1));
                visibleEmployeeIds = ['emp_1', 'emp_2', 'emp_3']; 
            } else if (projectId) {
                currentTasks = [];
                visibleEmployeeIds = [];
            } else {
                currentTasks = [];
                visibleEmployeeIds = [];
            }
            renderActiveCrewWidget();
            renderAllViews();
        }

        function renderAllViews() {
            renderChecklist();
            renderBoard(); 
            renderGantt(); 
            renderList(); 
            updateTaskCount();
        }

        // --- VIEWS ---

        function renderChecklist() {
            const list = document.getElementById('checklist-source');
            list.innerHTML = '';
            currentTasks.filter(t => t.status === 'open').forEach(task => {
                list.appendChild(renderTaskCard(task, false)); 
            });
        }

        function getAvatarStack(assigneeIds) {
            if (!assigneeIds || assigneeIds.length === 0) return '';
            let html = '<div class="flex -space-x-2">';
            assigneeIds.forEach(id => {
                const emp = allEmployees.find(e => e.id === id);
                if (emp) html += `<img src="${emp.avatar}" class="w-6 h-6 rounded-full border-2 border-white" title="${emp.name}">`;
            });
            html += '</div>';
            return html;
        }

        function getStatusBadge(status, reason) {
            if(status === 'scheduled') return '<span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold">Scheduled</span>';
            if(status === 'in-progress') return '<span class="bg-green-100 text-green-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold animate-pulse">In Progress</span>';
            if(status === 'paused') return `<span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-1 rounded-full uppercase font-bold" title="${reason}">Paused: ${reason}</span>`;
            return '';
        }

        function renderTaskCard(task, isBoard = true) {
            const div = document.createElement('div');
            
            if (!isBoard) {
                div.className = 'glass-card p-3 rounded-xl flex items-center justify-between cursor-grab group active:cursor-grabbing hover:border-sky/50';
            } else {
                div.className = 'bg-white shadow-md border-l-4 border-l-actionGreen p-3 rounded-xl mb-3 cursor-grab relative group';
            }

            div.setAttribute('data-id', task.id);
            div.setAttribute('onclick', `openModal('${task.id}')`);

            let catColor = 'bg-slate-200 text-slate-600';
            if(task.category === 'Electric') catColor = 'bg-brandDark/10 text-brandDark';
            if(task.category === 'Roof') catColor = 'bg-sky/20 text-sky-700';

            const avatarHtml = task.assignees.length > 0 ? getAvatarStack(task.assignees) : '';
            const statusHtml = getStatusBadge(task.status, task.pauseReason);

            let actionBtn = '';
            if (isBoard) {
                if (task.status === 'in-progress') {
                    actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-pause"></i></button>`;
                } else {
                    actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-play"></i></button>`;
                }
            }

            let travelHtml = '';
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

            let dateHtml = '';
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
            const container = document.getElementById('list-body');
            container.innerHTML = '';
            currentTasks.forEach(task => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-12 gap-4 p-3 border-b border-slate-100 bg-white rounded-lg hover:shadow-sm transition-all items-center cursor-pointer';
                row.onclick = () => openModal(task.id);
                
                const assigneeHTML = getAvatarStack(task.assignees);
                const statusBadge = getStatusBadge(task.status, task.pauseReason);
                
                let actionBtn = '';
                if(task.status !== 'open') {
                     if (task.status === 'in-progress') {
                        actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 hover:bg-orange-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-pause"></i></button>`;
                    } else {
                        actionBtn = `<button onclick="toggleTaskStatus('${task.id}', event)" class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-play"></i></button>`;
                    }
                }

                let routeInfo = '<span class="text-xs text-slate-400">-</span>';
                if(task.travelTime) {
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
                        <div class="w-2 h-2 rounded-full ${task.category === 'Electric' ? 'bg-brandDark' : 'bg-sky-400'}"></div>
                        <div>
                            <div class="font-semibold text-sm text-slate-700">${task.title}</div>
                            <div class="text-xs text-slate-400 flex items-center gap-1">${getAvatarStack(task.assignees)}</div>
                        </div>
                    </div>
                    <div class="col-span-2 text-xs text-slate-500">
                        ${task.startDate ? `<div>${task.startDate}</div><div class="text-red-400">Due: ${task.dueDate}</div>` : '-'}
                    </div>
                    <div class="col-span-3">
                        ${routeInfo}
                    </div>
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
            const container = document.getElementById('view-board');
            container.innerHTML = ''; 

            visibleEmployeeIds.forEach(empId => {
                const emp = allEmployees.find(e => e.id === empId);
                if(!emp) return;

                const col = document.createElement('div');
                col.className = 'glass-panel rounded-[2rem] flex flex-col h-full overflow-hidden';
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

                Sortable.create(col.querySelector(`#${emp.id}`), {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onAdd: function (evt) {
                        const taskId = evt.item.getAttribute('data-id');
                        updateTaskStatus(taskId, 'scheduled', [empId]);
                    },
                    onRemove: function(evt) {
                         // handled by updateStatus, visual handled by renderBoard refresh
                    }
                });
            });

            currentTasks.filter(t => t.status !== 'open').forEach(task => {
                if(task.assignees.length > 0) {
                    const leadId = task.assignees[0];
                    if(visibleEmployeeIds.includes(leadId)) {
                        const col = document.getElementById(leadId);
                        if(col) col.appendChild(renderTaskCard(task));
                    }
                }
            });
        }

        function toggleTaskStatus(taskId, event) {
            event.stopPropagation();
            const task = currentTasks.find(t => t.id === taskId);
            if(!task) return;

            if (task.status === 'in-progress') {
                const reason = prompt("Reason for pausing this task?", "Break");
                if(reason) {
                    task.status = 'paused';
                    task.pauseReason = reason;
                }
            } else {
                task.status = 'in-progress';
                task.pauseReason = null;
            }
            renderAllViews();
        }

        // --- STANDARD UTILS ---
        function updateTaskCount() {
            const count = currentTasks.filter(t => t.status === 'open').length;
            document.getElementById('task-count').innerText = `${count} Unassigned`;
        }

        function updateTaskStatus(taskId, status, assigneesArray) {
            const task = currentTasks.find(t => t.id === taskId);
            if(task) {
                task.status = status;
                if(assigneesArray !== null) task.assignees = assigneesArray;
                updateTaskCount();
                renderAllViews();
            }
        }

        function switchView(viewName) {
            document.querySelectorAll('.view-container').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="btn-view-"]').forEach(btn => {
                btn.className = "px-3 py-1.5 rounded-md text-sm font-medium text-slate-500 hover:text-brandDark transition-all flex items-center gap-2";
            });
            document.getElementById(`view-${viewName}`).classList.remove('hidden');
            document.getElementById(`btn-view-${viewName}`).className = "px-3 py-1.5 rounded-md text-sm font-bold bg-white shadow-sm text-brandDark transition-all flex items-center gap-2";
            renderAllViews();
        }

        // --- CREW MANAGEMENT ---
        function renderActiveCrewWidget() {
            const container = document.getElementById('active-crew-avatars');
            container.innerHTML = '';
            if(visibleEmployeeIds.length === 0) {
                container.innerHTML = '<span class="text-xs text-slate-400 italic pl-2">No crew selected</span>';
                return;
            }
            visibleEmployeeIds.forEach(id => {
                const emp = allEmployees.find(e => e.id === id);
                if(emp) {
                    const img = document.createElement('img');
                    img.src = emp.avatar;
                    img.className = "w-6 h-6 rounded-full border border-white ring-1 ring-slate-200 cursor-pointer hover:scale-110 transition-transform";
                    img.title = emp.name;
                    container.appendChild(img);
                }
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

        // --- WIZARD FUNCTIONS ---

        function openPlanWizard() {
            const select = document.getElementById('project-selector');
            const customerName = select.options[select.selectedIndex]?.text || "Customer";
            document.getElementById('wizard-customer-name').innerText = customerName;
            
            document.querySelector('input[name="plan-type"][value="project"]').checked = true;
            toggleWizardType('project');

            renderWizardProjectList();
            renderWizardCrewSelect();
            renderWizardAppointments(); 
            renderWizardAssetSelect();
            
            document.getElementById('plan-wizard-modal').classList.remove('hidden');
        }

        function closePlanWizard() {
            document.getElementById('plan-wizard-modal').classList.add('hidden');
        }

        function toggleWizardType(type) {
            wizardType = type;
            document.getElementById('wizard-form-project').classList.add('hidden');
            document.getElementById('wizard-form-custom').classList.add('hidden');
            document.getElementById('wizard-form-appointments').classList.add('hidden');

            if(type === 'project') document.getElementById('wizard-form-project').classList.remove('hidden');
            else if(type === 'custom') document.getElementById('wizard-form-custom').classList.remove('hidden');
            else if(type === 'appointments') document.getElementById('wizard-form-appointments').classList.remove('hidden');
        }

        function renderWizardAssetSelect() {
            const renderAssets = (containerId) => {
                const container = document.getElementById(containerId);
                if (!container) return;
                container.innerHTML = '';
                assetInventory.forEach(asset => {
                    container.innerHTML += `
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-slate-50 cursor-pointer text-xs">
                            <input type="checkbox" class="wizard-asset-checkbox rounded text-brandDark focus:ring-brandDark" value="${asset.id}" data-container="${containerId}">
                            <span class="font-medium text-slate-700">${asset.name}</span>
                            <span class="ml-auto text-[10px] text-slate-400 bg-slate-100 px-1 rounded">${asset.location}</span>
                        </label>
                    `;
                });
            };

            renderAssets('wizard-project-assets');
            renderAssets('wizard-custom-assets');
            renderAssets('wizard-appt-assets');
        }

        function getSelectedAssets(containerId) {
            const checkboxes = document.querySelectorAll(`#${containerId} .wizard-asset-checkbox:checked`);
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function renderWizardProjectList() {
            const plannedList = document.getElementById('wizard-planned-list');
            const remainingList = document.getElementById('wizard-remaining-list');
            plannedList.innerHTML = '';
            remainingList.innerHTML = '';

            currentTasks.forEach(task => {
                if(task.status === 'scheduled') {
                    const assigneeNames = task.assignees.map(id => allEmployees.find(e => e.id === id)?.name).join(', ');
                    plannedList.innerHTML += `
                        <div class="flex justify-between items-center p-3 bg-white border border-green-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-actionGreen"></i>
                                <span class="font-bold text-sm text-slate-700">${task.title}</span>
                            </div>
                            <div class="text-xs text-slate-500">
                                <span class="mr-2"><i class="fa-solid fa-user-group mr-1"></i> ${assigneeNames}</span>
                                <span><i class="fa-regular fa-clock mr-1"></i> ${task.duration}h</span>
                            </div>
                        </div>
                    `;
                }
            });

            if(plannedList.innerHTML === '') plannedList.innerHTML = '<span class="text-sm text-slate-400 italic">No tasks planned yet.</span>';

            checklistTemplate.forEach((item, index) => {
                const existingTask = currentTasks.find(t => t.title === item.title);
                if (!existingTask || existingTask.status === 'open') {
                    remainingList.innerHTML += `
                        <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="checkbox" class="wizard-task-checkbox rounded text-brandDark focus:ring-brandDark w-5 h-5" value="${index}">
                            <div class="flex-1">
                                <div class="font-bold text-sm text-slate-800">${item.title}</div>
                                <div class="text-xs text-slate-500">${item.category} • ${item.duration}h Est.</div>
                            </div>
                        </label>
                    `;
                }
            });
        }

        function renderWizardAppointments() {
            const list = document.getElementById('wizard-appointments-list');
            list.innerHTML = '';
            
            if(unplannedAppointments.length === 0) {
                list.innerHTML = '<div class="text-center text-slate-400 py-8 italic">No undefined appointments found.</div>';
                document.getElementById('wizard-appointment-resolution').classList.add('hidden');
                return;
            }

            unplannedAppointments.forEach(appt => {
                const crewNames = appt.crew.map(id => allEmployees.find(e => e.id === id)?.name).join(', ');
                list.innerHTML += `
                    <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                        <input type="radio" name="wizard-appt-select" class="peer rounded-full text-brandDark focus:ring-brandDark w-5 h-5" value="${appt.id}" onchange="selectAppointment('${appt.id}')">
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <div class="font-bold text-sm text-slate-800">${appt.title}</div>
                                <div class="text-xs font-bold text-orange-500"><i class="fa-regular fa-clock mr-1"></i>${appt.date}</div>
                            </div>
                            <div class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-user-group mr-1"></i> ${crewNames}</div>
                        </div>
                    </label>
                `;
            });

            const dropdown = document.getElementById('appt-checklist-select');
            dropdown.innerHTML = '<option value="">Select a checklist item...</option>';
            checklistTemplate.forEach(item => {
                dropdown.innerHTML += `<option value="${item.title}">${item.title} (${item.duration}h)</option>`;
            });
        }

        function selectAppointment(apptId) {
            activeAppointmentId = apptId;
            document.getElementById('wizard-appointment-resolution').classList.remove('hidden');
            const appt = unplannedAppointments.find(a => a.id === apptId);
            if(appt) {
                document.getElementById('appt-manual-title').value = appt.title;
            }
        }

        function toggleApptResolveType(type) {
            if(type === 'link') {
                document.getElementById('appt-resolve-link').classList.remove('hidden');
                document.getElementById('appt-resolve-manual').classList.add('hidden');
            } else {
                document.getElementById('appt-resolve-link').classList.add('hidden');
                document.getElementById('appt-resolve-manual').classList.remove('hidden');
            }
        }

        function renderWizardCrewSelect() {
            const pmSelect = document.getElementById('wizard-pm-select');
            const crewSelect = document.getElementById('wizard-crew-select');
            pmSelect.innerHTML = '<option value="">Select Lead...</option>';
            crewSelect.innerHTML = '';

            allEmployees.forEach(emp => {
                pmSelect.innerHTML += `<option value="${emp.id}">${emp.name} (${emp.role})</option>`;
                crewSelect.innerHTML += `
                    <label class="flex items-center gap-2 bg-white border border-slate-200 px-3 py-2 rounded-lg cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" class="wizard-crew-checkbox rounded text-brandDark focus:ring-brandDark" value="${emp.id}">
                        <img src="${emp.avatar}" class="w-5 h-5 rounded-full">
                        <span class="text-sm text-slate-700">${emp.name}</span>
                    </label>
                `;
            });
        }

        function savePlanWizard() {
            if(wizardType === 'custom') {
                const title = document.getElementById('wizard-custom-title').value;
                if(!title) return alert("Enter a title");
                const selectedAssets = getSelectedAssets('wizard-custom-assets');
                
                currentTasks.push({
                    id: 'm' + Date.now(),
                    title: title,
                    duration: parseInt(document.getElementById('wizard-custom-duration').value) || 1,
                    category: document.getElementById('wizard-custom-category').value,
                    status: 'open', assignees: [], predecessors: [], startHour: 0,
                    description: document.getElementById('wizard-custom-desc').value, 
                    log: [], expenses: [], files: [],
                    assets: selectedAssets,
                    subtasks: [...tempWizardSteps] 
                });
                
                tempWizardSteps = [];
                renderWizardSteps();
            } else if (wizardType === 'appointments') {
                if(!activeAppointmentId) return alert("Select an appointment first.");
                const appt = unplannedAppointments.find(a => a.id === activeAppointmentId);
                const resolveType = document.querySelector('input[name="appt-resolve-type"]:checked').value;
                const selectedAssets = getSelectedAssets('wizard-appt-assets');
                
                let newTask = {
                    id: 'appt_task_' + Date.now(),
                    status: 'scheduled',
                    assignees: appt.crew,
                    startHour: 8, 
                    predecessors: [], log: [], expenses: [], files: [],
                    assets: selectedAssets,
                    subtasks: []
                };

                if(resolveType === 'link') {
                    const linkedTitle = document.getElementById('appt-checklist-select').value;
                    if(!linkedTitle) return alert("Select a checklist item to link.");
                    const template = checklistTemplate.find(t => t.title === linkedTitle);
                    newTask.title = template.title;
                    newTask.duration = template.duration;
                    newTask.category = template.category;
                } else {
                    newTask.title = document.getElementById('appt-manual-title').value;
                    newTask.category = document.getElementById('appt-manual-category').value;
                    newTask.duration = 2; 
                }

                currentTasks.push(newTask);
                unplannedAppointments = unplannedAppointments.filter(a => a.id !== activeAppointmentId);
                
                appt.crew.forEach(id => {
                    if(!visibleEmployeeIds.includes(id)) visibleEmployeeIds.push(id);
                });

            } else {
                const selectedIndices = Array.from(document.querySelectorAll('.wizard-task-checkbox:checked')).map(cb => cb.value);
                if(selectedIndices.length === 0) return alert("Please select at least one task.");

                const leadId = document.getElementById('wizard-pm-select').value;
                const crewIds = Array.from(document.querySelectorAll('.wizard-crew-checkbox:checked')).map(cb => cb.value);
                const selectedAssets = getSelectedAssets('wizard-project-assets');
                
                if(!leadId && crewIds.length === 0) {
                    selectedIndices.forEach(idx => {
                        const template = checklistTemplate[idx];
                        if(!currentTasks.find(t => t.title === template.title)) {
                            currentTasks.push({ 
                                id: 'p' + Date.now() + idx, ...template, 
                                status: 'open', assignees: [], startHour: 0, 
                                predecessors: [], log: [], expenses: [], files: [], assets: [], subtasks: [] 
                            });
                        }
                    });
                } else {
                    const fullCrew = new Set(crewIds);
                    if(leadId) fullCrew.add(leadId);
                    const finalCrew = Array.from(fullCrew);
                    finalCrew.forEach(id => { if(!visibleEmployeeIds.includes(id)) visibleEmployeeIds.push(id); });

                    selectedIndices.forEach(idx => {
                        const template = checklistTemplate[idx];
                        let task = currentTasks.find(t => t.title === template.title);
                        if(task) {
                            task.status = 'scheduled'; task.assignees = finalCrew; task.startHour = 8;
                            task.assets = selectedAssets; 
                        } else {
                            currentTasks.push({ 
                                id: 'p' + Date.now() + idx, ...template, 
                                status: 'scheduled', assignees: finalCrew, startHour: 8, 
                                predecessors: [], log: [], expenses: [], files: [], 
                                assets: selectedAssets, subtasks: []
                            });
                        }
                    });
                }
            }

            renderActiveCrewWidget();
            renderAllViews();
            closePlanWizard();
            showToast("Plan updated successfully.");
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.querySelector('div h4').innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
        }

        // --- GANTT RENDER ---
        function renderGantt() {
            const container = document.getElementById('gantt-body');
            const timeHeader = document.getElementById('time-scale');
            const svgLayer = document.getElementById('gantt-lines');
            container.innerHTML = ''; container.appendChild(svgLayer); 
            timeHeader.innerHTML = ''; svgLayer.innerHTML = ''; 

            for(let i=START_HOUR; i<=18; i++) {
                const marker = document.createElement('div');
                marker.className = 'absolute top-0 bottom-0 border-l border-slate-200 pl-1 text-[10px] h-full flex items-center';
                marker.style.left = `${(i - START_HOUR) * PIXELS_PER_HOUR}px`;
                marker.innerText = `${i}:00`;
                timeHeader.appendChild(marker);
            }

            visibleEmployeeIds.forEach(empId => {
                const emp = allEmployees.find(e => e.id === empId);
                if(!emp) return;
                const row = document.createElement('div');
                row.className = 'flex border-b border-slate-200 bg-white/40 h-24 relative group hover:bg-white/60 transition-colors';
                const sidebar = document.createElement('div');
                sidebar.className = 'w-48 flex-shrink-0 border-r border-slate-200 p-3 flex items-center gap-3 bg-white/50 sticky left-0 z-30 backdrop-blur-sm';
                sidebar.innerHTML = `
                    <img src="${emp.avatar}" class="w-10 h-10 rounded-full border border-slate-200">
                    <div>
                        <div class="font-bold text-sm text-slate-800">${emp.name}</div>
                        <div class="text-xs text-slate-500">${emp.role}</div>
                    </div>
                `;
                row.appendChild(sidebar);
                const timeline = document.createElement('div');
                timeline.className = 'flex-1 relative min-w-[1000px]'; 
                for(let i=0; i<=10; i++) {
                    const line = document.createElement('div');
                    line.className = 'gantt-grid-line';
                    line.style.left = `${i * PIXELS_PER_HOUR}px`;
                    timeline.appendChild(line);
                }
                const employeeTasks = currentTasks.filter(t => t.assignees.includes(emp.id) && t.status !== 'open');
                employeeTasks.forEach(task => {
                    const bar = document.createElement('div');
                    let colorClass = 'bg-sky-200 border border-sky-300 text-sky-900';
                    bar.className = `gantt-bar ${colorClass}`;
                    bar.style.left = `${(task.startHour - START_HOUR) * PIXELS_PER_HOUR}px`;
                    bar.style.width = `${task.duration * PIXELS_PER_HOUR}px`;
                    bar.style.top = '24px'; 
                    bar.innerHTML = `<span class="truncate">${task.title}</span>`;
                    bar.id = `gantt-task-${task.id}`;
                    bar.onclick = () => openModal(task.id);
                    timeline.appendChild(bar);
                });
                row.appendChild(timeline);
                container.appendChild(row);
            });
            setTimeout(drawGanttLines, 100);
        }

        function drawGanttLines() {
            const svg = document.getElementById('gantt-lines');
            const container = document.getElementById('gantt-body');
            
            svg.style.width = `${container.scrollWidth}px`;
            svg.style.height = `${container.scrollHeight}px`;

            svg.innerHTML = `
                <defs><marker id="arrowhead" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#cbd5e1" /></marker></defs>
            `;
            
            const containerRect = container.getBoundingClientRect();
            const scrollLeft = container.scrollLeft;
            const scrollTop = container.scrollTop;

            currentTasks.forEach(task => {
                if(task.predecessors && task.predecessors.length > 0 && task.status === 'scheduled') {
                    const toEl = document.getElementById(`gantt-task-${task.id}`);
                    if(!toEl) return;

                    task.predecessors.forEach(predId => {
                        const fromEl = document.getElementById(`gantt-task-${predId}`);
                        if(fromEl) {
                            const fromRect = fromEl.getBoundingClientRect();
                            const toRect = toEl.getBoundingClientRect();
                            
                            const x1 = (fromRect.right - containerRect.left) + scrollLeft;
                            const y1 = (fromRect.top + (fromRect.height/2) - containerRect.top) + scrollTop;
                            const x2 = (toRect.left - containerRect.left) + scrollLeft;
                            const y2 = (toRect.top + (toRect.height/2) - containerRect.top) + scrollTop;
                            
                            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                            path.setAttribute('d', `M ${x1} ${y1} C ${x1 + 40} ${y1}, ${x2 - 40} ${y2}, ${x2} ${y2}`);
                            path.setAttribute('stroke', '#cbd5e1');
                            path.setAttribute('stroke-width', '2');
                            path.setAttribute('fill', 'none');
                            path.setAttribute('marker-end', 'url(#arrowhead)');
                            svg.appendChild(path);
                        }
                    });
                }
            });
        }

        // --- MODAL LOGIC ---
        function switchModalTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(`tab-${tabId}`).classList.add('active');
            document.getElementById(`tab-btn-${tabId}`).classList.add('active');
        }

        function openModal(taskId) {
            activeTaskId = taskId;
            const task = currentTasks.find(t => t.id === taskId);
            if(!task) return;
            
            switchModalTab('overview');
            document.getElementById('modal-edit-title').value = task.title;
            document.getElementById('modal-edit-category').value = task.category;
            document.getElementById('modal-edit-duration').value = task.duration;
            document.getElementById('modal-edit-description').value = task.description || "";
            
            const assigneeText = task.assignees.length > 0 ? `${task.assignees.length} Techs Assigned` : 'Unassigned';
            document.getElementById('modal-assignee-text').innerText = assigneeText;

            document.getElementById('modal-schedule').innerText = task.startDate ? `${task.startDate} - ${task.dueDate}` : '--';
            document.getElementById('modal-travel').innerText = task.travelTime ? `${task.travelTime} from ${task.origin}` : '--';

            renderModalTaskAssignees(task);
            document.getElementById('modal-task-crew-select').classList.add('hidden');
            
            const assetContainer = document.getElementById('modal-assigned-assets');
            assetContainer.innerHTML = '';
            const assetsDiv = document.getElementById('modal-assigned-assets-container');
            
            if(task.assets && task.assets.length > 0) {
                assetsDiv.classList.remove('hidden');
                task.assets.forEach(assetId => {
                    const asset = assetInventory.find(a => a.id === assetId);
                    if(asset) {
                        assetContainer.innerHTML += `
                            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2 py-1.5 rounded text-xs">
                                <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>
                                <span class="font-medium text-slate-700">${asset.name}</span>
                            </div>
                        `;
                    }
                });
            } else {
                assetsDiv.classList.add('hidden');
            }

            renderDependencyList(task);
            populateDependencyDropdown(task);
            renderModalSubtasks(task);

            // Field Report Checklist
            const reportContainer = document.getElementById('modal-checklist-report');
            reportContainer.innerHTML = '';
            if(task.subtasks && task.subtasks.length > 0) {
                let completedCount = 0;
                task.subtasks.forEach(step => {
                    if(step.completed) {
                        completedCount++;
                        const emp = allEmployees.find(e => e.id === step.completedBy);
                        const empName = emp ? emp.name : 'Unknown';
                        const photoHtml = step.photo 
                            ? `<img src="${step.photo}" class="w-20 h-20 object-cover rounded-lg border border-slate-200 mt-2 cursor-pointer hover:opacity-90 shadow-sm" onclick="window.open('${step.photo}', '_blank')">` 
                            : '';
                        
                        reportContainer.innerHTML += `
                            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 text-green-500 bg-green-50 rounded-full p-1"><i class="fa-solid fa-check text-xs"></i></div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <span class="text-sm font-bold text-slate-800 line-through decoration-slate-400 decoration-2">${step.text}</span>
                                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">${step.time || '--:--'}</span>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-user-check"></i> Verified by <span class="font-semibold text-slate-700">${empName}</span>
                                        </div>
                                        ${step.note ? `<p class="text-xs text-slate-600 bg-slate-50 p-2 rounded mt-2 border border-slate-100 italic">"${step.note}"</p>` : ''}
                                        ${photoHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                
                if (completedCount === 0) reportContainer.innerHTML = `<div class="text-sm text-slate-400 italic text-center py-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">No steps completed yet.</div>`;
            } else {
                 reportContainer.innerHTML = `<div class="text-sm text-slate-400 italic text-center py-4">No checklist steps defined for this task.</div>`;
            }

            const logContainer = document.getElementById('modal-activity-log');
            logContainer.innerHTML = '';
            if(task.log && task.log.length > 0) {
                task.log.forEach(entry => {
                    const hasPhoto = entry.photo ? '<div class="mt-2 w-20 h-20 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-300 cursor-pointer"><i class="fa-solid fa-image"></i></div>' : '';
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
                logContainer.innerHTML = `<div class="text-sm text-slate-400 italic pl-2">No activity recorded yet.</div>`;
            }

            const finContainer = document.getElementById('modal-financials');
            finContainer.innerHTML = '';
            if(task.expenses && task.expenses.length > 0) {
                task.expenses.forEach(exp => {
                    finContainer.innerHTML += `
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-100 text-sm">
                            <span class="text-red-800 font-medium"><i class="fa-solid fa-receipt mr-2"></i> ${exp.item}</span>
                            <span class="font-bold text-slate-800">€${exp.cost}</span>
                        </div>
                    `;
                });
            } else {
                finContainer.innerHTML = `<div class="text-sm text-slate-400 italic">No expenses reported.</div>`;
            }

            const filesContainer = document.getElementById('modal-files-list');
            filesContainer.innerHTML = '';
            if(task.files && task.files.length > 0) {
                task.files.forEach(f => {
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
                filesContainer.innerHTML = `<div class="text-sm text-slate-400 italic">No files attached.</div>`;
            }

            document.getElementById('task-modal').classList.remove('hidden');
            setTimeout(() => document.getElementById('task-modal-content').classList.remove('translate-x-full'), 10);
        }

        function saveActiveTask() {
            const task = currentTasks.find(t => t.id === activeTaskId);
            if (task) {
                task.title = document.getElementById('modal-edit-title').value;
                task.category = document.getElementById('modal-edit-category').value;
                task.duration = parseFloat(document.getElementById('modal-edit-duration').value) || 0;
                task.description = document.getElementById('modal-edit-description').value;
                renderAllViews();
                closeModal();
                showToast("Task updated successfully.");
            }
        }

        function deleteActiveTask() {
            if (confirm("Are you sure you want to delete this task?")) {
                currentTasks = currentTasks.filter(t => t.id !== activeTaskId);
                renderAllViews();
                closeModal();
                showToast("Task deleted.");
            }
        }

        function renderDependencyList(task) {
            const list = document.getElementById('modal-dependency-list');
            list.innerHTML = '';
            if (task.predecessors.length === 0) {
                list.innerHTML = `<div class="text-xs text-slate-400 italic bg-slate-50 p-2 rounded border border-slate-100">No dependencies linked.</div>`;
            } else {
                task.predecessors.forEach(pid => {
                    const predTask = currentTasks.find(t => t.id === pid);
                    if (predTask) {
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
                    }
                });
            }
        }

        function populateDependencyDropdown(task) {
            const select = document.getElementById('modal-dependency-select');
            select.innerHTML = '<option value="">Select a predecessor task...</option>';
            currentTasks.forEach(t => {
                if (t.id !== task.id && !task.predecessors.includes(t.id)) {
                    select.innerHTML += `<option value="${t.id}">${t.title}</option>`;
                }
            });
        }

        function addDependency() {
            const select = document.getElementById('modal-dependency-select');
            const predId = select.value;
            if (!predId) return;
            const task = currentTasks.find(t => t.id === activeTaskId);
            if (task) {
                task.predecessors.push(predId);
                renderDependencyList(task);
                populateDependencyDropdown(task);
                if(!document.getElementById('view-gantt').classList.contains('hidden')) drawGanttLines();
            }
        }

        function removeDependency(predId) {
            const task = currentTasks.find(t => t.id === activeTaskId);
            if (task) {
                task.predecessors = task.predecessors.filter(id => id !== predId);
                renderDependencyList(task);
                populateDependencyDropdown(task);
                if(!document.getElementById('view-gantt').classList.contains('hidden')) drawGanttLines();
            }
        }

        function renderModalTaskAssignees(task) {
            const container = document.getElementById('modal-task-assignees');
            container.innerHTML = '';
            if (!task.assignees || task.assignees.length === 0) {
                container.innerHTML = '<span class="text-sm text-slate-400 italic">No crew assigned yet. Click Edit Crew.</span>';
                return;
            }
            task.assignees.forEach(id => {
                const emp = allEmployees.find(e => e.id === id);
                if (emp) {
                    container.innerHTML += `
                        <div class="flex items-center gap-2 bg-white px-2 py-1 rounded border border-slate-200">
                            <img src="${emp.avatar}" class="w-5 h-5 rounded-full">
                            <span class="text-xs font-bold text-slate-700">${emp.name}</span>
                        </div>
                    `;
                }
            });
        }

        function toggleTaskCrewEditor() {
            const area = document.getElementById('modal-task-crew-select');
            const isHidden = area.classList.contains('hidden');
            if(isHidden) {
                area.classList.remove('hidden');
                const container = document.getElementById('modal-task-crew-checkboxes');
                container.innerHTML = '';
                const task = currentTasks.find(t => t.id === activeTaskId);
                visibleEmployeeIds.forEach(id => {
                    const emp = allEmployees.find(e => e.id === id);
                    const isChecked = task.assignees.includes(id) ? 'checked' : '';
                    container.innerHTML += `
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                            <input type="checkbox" ${isChecked} onchange="updateTaskAssignee('${id}', this.checked)" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">${emp.name}</span>
                        </label>
                    `;
                });
            } else {
                area.classList.add('hidden');
            }
        }

        function updateTaskAssignee(empId, isChecked) {
            const task = currentTasks.find(t => t.id === activeTaskId);
            if(!task) return;
            if(isChecked) {
                if(!task.assignees.includes(empId)) task.assignees.push(empId);
            } else {
                task.assignees = task.assignees.filter(id => id !== empId);
            }
            if(task.assignees.length === 0) task.status = 'open';
            else task.status = 'scheduled';
            renderModalTaskAssignees(task); 
            renderAllViews(); 
        }

        function renderModalSubtasks(task) {
            const container = document.getElementById('modal-subtasks-list');
            container.innerHTML = '';
            if (!task.subtasks || task.subtasks.length === 0) {
                container.innerHTML = '<div class="text-xs text-slate-400 italic">No sub-tasks defined.</div>';
                return;
            }
            task.subtasks.forEach((step, idx) => {
                const isChecked = step.completed ? 'checked' : '';
                const style = step.completed ? 'line-through text-slate-400' : 'text-slate-700';
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
            const task = currentTasks.find(t => t.id === activeTaskId);
            if(task && task.subtasks[index]) {
                task.subtasks[index].completed = completed;
                renderModalSubtasks(task);
            }
        }

        function removeSubtask(index) {
            const task = currentTasks.find(t => t.id === activeTaskId);
            if(task) {
                task.subtasks.splice(index, 1);
                renderModalSubtasks(task);
            }
        }
        
        function addTaskSubtask() {
            const task = currentTasks.find(t => t.id === activeTaskId);
            if(!task) return;
            const text = prompt("New step description:");
            if(text) {
                if(!task.subtasks) task.subtasks = [];
                task.subtasks.push({ text: text, completed: false });
                renderModalSubtasks(task);
            }
        }

        function addWizardStep() {
            const input = document.getElementById('wizard-custom-step-input');
            const val = input.value.trim();
            if(val) {
                tempWizardSteps.push({ text: val, completed: false });
                input.value = '';
                renderWizardSteps();
            }
        }

        function removeWizardStep(index) {
            tempWizardSteps.splice(index, 1);
            renderWizardSteps();
        }

        function renderWizardSteps() {
            const container = document.getElementById('wizard-custom-steps-list');
            container.innerHTML = '';
            if(tempWizardSteps.length === 0) {
                 container.innerHTML = '<div class="text-xs text-slate-400 italic p-2">No steps added yet.</div>';
                 return;
            }
            tempWizardSteps.forEach((step, idx) => {
                container.innerHTML += `
                    <div class="flex items-center justify-between bg-slate-50 p-2 rounded text-sm border border-slate-100">
                        <span class="text-slate-700">${idx + 1}. ${step.text}</span>
                        <button onclick="removeWizardStep(${idx})" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                    </div>
                `;
            });
        }
        
        function closeModal() {
            document.getElementById('task-modal-content').classList.add('translate-x-full');
            setTimeout(() => document.getElementById('task-modal').classList.add('hidden'), 300);
        }

        // Init
        document.getElementById('task-search').addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('#checklist-source > div').forEach(item => {
                const title = item.getAttribute('data-title').toLowerCase();
                item.style.display = title.includes(term) ? 'flex' : 'none';
            });
        });

        function initSortables() {
            const sourceList = document.getElementById('checklist-source');
            Sortable.create(sourceList, {
                group: { name: 'shared', pull: true, put: true },
                animation: 150,
                ghostClass: 'sortable-ghost',
                sort: false,
                onAdd: function (evt) {
                    const taskId = evt.item.getAttribute('data-id');
                    updateTaskStatus(taskId, 'open', []);
                }
            });
        }

        window.addEventListener('resize', () => { if(!document.getElementById('view-gantt').classList.contains('hidden')) drawGanttLines(); });

        window.addEventListener('DOMContentLoaded', () => {
            initSortables();
            initData();
        });

    </script>
</body>
</html>