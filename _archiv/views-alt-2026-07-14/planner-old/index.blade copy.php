<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA-DESK - Einsatzplanung</title> 
    <meta name="planner-base-url" content="{{ url('/planner') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <!-- Select2 (Product/Object dropdown) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />


    <style>
    /* Make Select2 look like your Tailwind selects */
    .select2-container--default .select2-selection--single{
        height: 44px;
        border-radius: 0.75rem; /* rounded-xl */
        border: 1px solid rgb(226 232 240); /* slate-200 */
        background: rgba(255,255,255,.5);
        padding: 0.35rem 2.25rem 0.35rem 2.5rem; /* match icon-left spacing */
        display:flex; align-items:center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 1.25rem;
        font-weight: 600;
        color: rgb(51 65 85); /* slate-700 */
        padding:0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 44px;
        right: 10px;
    }
    .select2-dropdown{
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        overflow:hidden;
    }
    .select2-results__option{ padding: 10px 12px; }
    </style>


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

    <style>
        .notif-filter-btn.active { background-color: white; color: #164191; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border-color: #e2e8f0; }
        
        .notif-item { transition: all 0.2s; }
        .notif-item:hover { background-color: #f8fafc; }
        .notif-item.unread { background-color: #eff6ff; border-left: 3px solid #164191; }
        .notif-item.unread:hover { background-color: #dbeafe; }
    </style>

    <style>
        .qs-sider {
            position: fixed;
            top: 0;
            right: -400px; /* Hidden by default */
            width: 350px;
            height: 100vh;
            background: #34444c; /* Adjust to match your theme */
            z-index: 1050;
            transition: right 0.3s ease-in-out;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .qs-sider.open {
            right: 0; /* Slide in */
        }

        /* Overlay for mobile if needed */
        .qs-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .qs-sider.open + .qs-overlay {
            display: block;
        }
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
                <h1 class="text-xl font-bold text-brandDark tracking-tight">Nuri <span class="text-sky">Head</span></h1>
            </div>

            <!-- Context Selectors -->
            <div class="hidden md:flex items-center gap-4 border-l border-slate-300 pl-6"> 
                <!-- Level 1: Customer Selector (Searchable) -->
                <div class="relative min-w-[260px]" id="customer-select-container">
                    <label for="customer-search-input" class="sr-only">Customer</label>

                    <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>

                    <input
                        type="text"
                        id="customer-search-input"
                        placeholder="Search customer…"
                        class="w-full pl-10 pr-9 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700
                            focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark
                            transition cursor-pointer hover:bg-white"
                        autocomplete="off"
                        onfocus="showCustomerDropdown()"
                        oninput="filterCustomerDropdown()"
                    />

                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" id="customer-chevron"></i>
                    </span>
                    </div>

                    <!-- Dropdown List -->
                    <div
                    id="customer-dropdown-list"
                    class="absolute top-full left-0 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl
                            max-h-60 overflow-y-auto hidden z-50"
                    role="listbox"
                    aria-label="Customer results"
                    >
                    <!-- Items injected via JS -->
                    </div>
                </div>

                <!-- Level 2: Product/Object Selector -->
                <div class="relative min-w-[300px]">
                    <label for="project-selector" class="sr-only">Product & Site</label>

                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-brandDark">
                    <i class="fa-solid fa-building"></i>
                    </span>

                    <select
                    id="project-selector"
                    onchange="changeProject(this.value)"
                    class="appearance-none w-full pl-10 pr-9 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700
                            focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark
                            transition cursor-pointer hover:bg-white disabled:opacity-60 disabled:cursor-not-allowed"
                    disabled
                    >
                    <option value="">Select product & site…</option>
                    <!-- Populated via JS based on Customer -->
                    </select>

                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>

                <!-- Stage Selector -->
                <div class="relative min-w-[260px]">
                    <label for="plan-selector" class="sr-only">Plan</label>

                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </span>

                    <select
                        id="plan-selector"
                        class="appearance-none w-full pl-10 pr-9 py-2.5 rounded-xl bg-white/50 border border-slate-200 text-sm font-semibold text-slate-700
                            focus:outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark
                            transition cursor-pointer hover:bg-white disabled:opacity-60 disabled:cursor-not-allowed"
                        disabled
                    >
                        <option value="">Plan wählen…</option>
                    </select>

                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
 
            </div>

        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button onclick="openPlanWizard()" class="bg-[#93c21d] from-sky-500 to-brandDark hover:from-sky-600 hover:to-blue-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-500/20 transition-all active:scale-95 flex items-center gap-2 border border-white/20">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Planen</span>
            </button>
            <div class="h-8 w-px bg-slate-300 mx-2"></div>
                <div class="relative" id="notification-dropdown-container">
                    <button onclick="toggleNotificationDropdown()" 
                            class="w-10 h-10 rounded-full bg-white text-slate-500 hover:text-brandDark hover:bg-sky/10 transition-colors flex items-center justify-center relative">
                        <i class="fa-solid fa-bell"></i>
                        <span id="notification-badge" class="hidden absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden transform origin-top-right transition-all">
                        <div class="p-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 text-sm">Benachrichtigungen</h3>
                            <button onclick="markAllNotificationsRead()" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">Alle gelesen</button>
                        </div>
                        <div id="notification-list-mini" class="max-h-64 overflow-y-auto">
                            <div class="p-4 text-center text-xs text-slate-400">Keine neuen Benachrichtigungen</div>
                        </div>
                        <div class="p-2 border-t border-slate-100 bg-slate-50/50 text-center">
                            <button onclick="openFullNotificationModal()" class="text-xs font-bold text-brandDark hover:underline">Alle anzeigen</button>
                        </div>
                    </div>
                </div>
                    <button onclick="toggleQuickSider(event)" 
                            class="h-10 w-10 rounded-full bg-gradient-to-tr from-brandDark to-sky flex items-center justify-center text-white font-bold shadow-md ring-2 ring-white hover:opacity-90 transition-opacity"
                            aria-label="Schnellzugriff">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            width="20" 
                            height="20" 
                            viewBox="0 0 24 24" 
                            fill="none" 
                            stroke="currentColor" 
                            stroke-width="2" 
                            stroke-linecap="round" 
                            stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>

                    </button>
        </div>
  
    </header>

    <!-- Main Navigation Tabs -->
    <div class="px-6 py-2 bg-white border-b border-slate-200 flex gap-2 overflow-x-auto">
        <button onclick="switchMainTab('planning')" id="nav-planning" class="nav-link active px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-table-columns"></i> Planungstafel
        </button>
        <button onclick="switchMainTab('attendance')" id="nav-attendance" class="nav-link px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-users-viewfinder"></i> 
            Anwesenheit
            <div class="flex gap-1 ml-1">
                <span id="tab-badge-present" class="bg-green-100 text-green-700 text-[10px] font-bold px-1.5 py-0.5 rounded-md hidden">0</span>
                <span id="tab-badge-absent" class="bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded-md hidden">0</span>
            </div>
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
                    <!-- Tabs (script binds .wf-tab click automatically; no inline onclick needed) -->
                    <div class="flex items-center gap-2 bg-white/70 rounded-2xl p-2 shadow-sm">
                        <button
                        type="button"
                        class="wf-tab px-4 py-2 rounded-xl text-sm font-semibold bg-slate-900 text-white"
                        data-tab="phase"
                        >
                        Phase
                        </button>

                        <button
                        type="button"
                        class="wf-tab px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        data-tab="personal_task"
                        >
                        Personal Task
                        </button>

                        <button
                        type="button"
                        class="wf-tab px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        data-tab="appointment"
                        >
                        Appointment
                        </button>

                        <button
                        type="button"
                        class="wf-tab px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        data-tab="ticket"
                        >
                        Ticket
                        </button>
                    </div>

                    <!-- Panel Header -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-6 bg-actionGreen rounded-full"></span>
                            <!-- Script updates this based on active tab -->
                            <span id="wf-sidebar-title">Phasen</span>
                        </h2>

                        <span
                            class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded-md"
                            id="task-count"
                        >0</span>
                        </div>

                        <!-- Search (script binds #task-search input and updates WFRT.sidebarState.search) -->
                        <div class="relative">
                        <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                        <input
                            type="text"
                            id="task-search"
                            placeholder="Suchen..."
                            class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none"
                            autocomplete="off"
                        />
                        </div>
                    </div>

                    <!-- Scrollable List Container -->
                    <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto overflow-x-hidden relative">
                        <!-- Phase -->
                        <div id="wf-tab-phase" class="wf-tab-panel">
                        <!-- IMPORTANT: this container is used for BOTH:
                            - Plan Backlog section (rendered by script at top)
                            - Stage -> Phase -> Activities cards (data-source-type="phase_activity") -->
                        <div id="checklist-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                            <!-- injected via JS -->
                        </div>

                        <!-- Optional manual add (kept; safe if function exists) -->
                        <button
                            type="button"
                            onclick="window.addManualTask?.()"
                            class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Manuelle Aufgabe
                        </button>
                        </div>

                        <!-- Personal Task -->
                        <div id="wf-tab-personal_task" class="wf-tab-panel hidden">
                        <div id="personal-task-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                            <!-- injected via JS -->
                        </div>

                        <button
                            type="button"
                            onclick="window.addManualPersonalTask?.()"
                            class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Personal Task
                        </button>
                        </div>

                        <!-- Appointment -->
                        <div id="wf-tab-appointment" class="wf-tab-panel hidden">
                        <div id="appointment-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                            <!-- injected via JS -->
                        </div>

                        <button
                            type="button"
                            onclick="window.addManualAppointment?.()"
                            class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Appointment
                        </button>
                        </div>

                        <!-- Ticket -->
                        <div id="wf-tab-ticket" class="wf-tab-panel hidden">
                        <div id="ticket-source" class="flex flex-col gap-3 min-h-[200px] pb-10">
                            <!-- injected via JS -->
                        </div>

                        <button
                            type="button"
                            onclick="window.addManualTicket?.()"
                            class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Ticket
                        </button>
                        </div>
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
         <div id="main-tab-attendance" class="main-tab hidden h-full w-full gap-6 p-2"> 
            <div class="w-1/2 glass-panel rounded-3xl flex flex-col overflow-hidden border border-green-200/50">
                <div class="p-4 border-b border-slate-100 bg-green-50/30 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        Anwesend (Im Dienst)
                    </h3>
                    <span id="count-present-display" class="text-2xl font-black text-green-600">0</span>
                </div>
                <div id="attendance-list-present" class="flex-1 overflow-y-auto p-4 space-y-3">
                    </div>
            </div>

            <div class="w-1/2 glass-panel rounded-3xl flex flex-col overflow-hidden border border-red-200/50">
                <div class="p-4 border-b border-slate-100 bg-red-50/30 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-400"></div>
                        Abwesend / Fertig
                    </h3>
                    <span id="count-absent-display" class="text-2xl font-black text-slate-400">0</span>
                </div>
                <div id="attendance-list-absent" class="flex-1 overflow-y-auto p-4 space-y-3">
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


    <div id="notification-full-modal" class="fixed inset-0 z-[150] hidden font-sans">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeFullNotificationModal()"></div>
        
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg h-[85vh] flex flex-col transform transition-all scale-100 overflow-hidden border border-slate-200">
                
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-white z-10">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800">Benachrichtigungen</h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Planner Updates & Aufgaben</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.markAllNotificationsRead()" class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                            Alle gelesen
                        </button>
                        <button onclick="window.closeFullNotificationModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="px-5 py-3 bg-white border-b border-slate-100 flex gap-2">
                    <button onclick="window.setNotifFilter('all')" class="notif-filter-btn active px-4 py-1.5 rounded-full text-xs font-bold border transition-all bg-slate-800 text-white border-slate-800 shadow-sm">Alle</button>
                    <button onclick="window.setNotifFilter('unread')" class="notif-filter-btn px-4 py-1.5 rounded-full text-xs font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all">Ungelesen</button>
                </div>

                <div id="notification-list-full" class="flex-1 overflow-y-auto bg-slate-50 p-2 space-y-2">
                    </div>
                
                <div id="notif-loading" class="hidden absolute inset-0 bg-white/80 z-20 flex items-center justify-center">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-500"></i>
                </div>

            </div>
        </div>
    </div>

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

                    <!-- Sidebar Choices (ADD this label) -->
                    <label class="cursor-pointer">
                    <input type="radio" name="plan-type" value="personal" class="peer hidden" onchange="toggleWizardType('personal')">
                    <div class="p-4 rounded-xl border-2 border-transparent bg-white shadow-sm peer-checked:border-brandDark peer-checked:ring-2 peer-checked:ring-brandDark/20 transition-all">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                        <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Persönliche Aufgaben</h3>
                        <p class="text-xs text-slate-500 mt-1">Offene Aufgaben aus Personal Tasks.</p>
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
                         <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">Bereits geplant</h4>
                            <div id="wizard-planned-list" class="space-y-2 opacity-70"></div>
                        </div>
                        <div class="space-y-2 pt-4 border-t border-slate-100">
                            <h4 class="text-xs font-bold text-brandDark uppercase tracking-wide">Verbleibend / Offen</h4>
                            <div id="wizard-remaining-list" class="space-y-2"></div>
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

                    <!-- Main Content Area (ADD this block) -->
                    <div id="wizard-form-personal" class="hidden space-y-6">
                    <h3 class="text-lg font-bold text-slate-800">Persönliche Aufgaben wählen</h3>
                    <div class="space-y-3" id="wizard-personal-list"></div>
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
 
        <!-- ✅ Task Details Slider (compatible with the NEW WFRT.planState items) -->
    <div id="task-modal" class="fixed inset-0 z-[100] hidden">
        <div id="task-modal-overlay"
            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
            onclick="closeModal()"></div>

        <div id="task-modal-content"
            class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform duration-300 flex flex-col translate-x-full">

            <!-- Header -->
            <div class="p-6 border-b border-slate-100 bg-slate-50">
            <div class="flex justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Details</span>
                <button type="button" onclick="closeModal()">
                <i class="fa-solid fa-times text-slate-400 hover:text-slate-600"></i>
                </button>
            </div>

            <!-- active id (runtime) -->
            <input type="hidden" id="modal-active-item-id" value="">

            <input type="text"
                    id="modal-edit-title"
                    class="text-2xl font-bold w-full bg-transparent border-none outline-none mb-2"
                    value=""
                    placeholder="Titel…"/>

            <div class="flex flex-wrap gap-2 mt-2">
                <span class="text-[11px] font-bold bg-slate-200 text-slate-700 px-2 py-1 rounded-md" id="modal-badge-source">—</span>
                <span class="text-[11px] font-bold bg-slate-200 text-slate-700 px-2 py-1 rounded-md" id="modal-badge-status">—</span>
                <span class="text-[11px] font-bold bg-slate-200 text-slate-700 px-2 py-1 rounded-md" id="modal-badge-duration">—</span>
            </div>

            <div class="mt-3 text-xs text-slate-500 space-y-1">
                <div class="flex items-center gap-2">
                <i class="fa-regular fa-calendar text-slate-400"></i>
                <span class="font-semibold">Zeitplan:</span>
                <span id="modal-schedule" class="font-bold text-slate-700">—</span>
                </div>
                <div class="flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-slate-400"></i>
                <span class="font-semibold">Adresse:</span>
                <span id="modal-address" class="font-bold text-slate-700 truncate">—</span>
                </div>
            </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b px-2 overflow-x-auto">
            <button type="button" onclick="switchModalTab('info')" id="tab-btn-info"
                    class="tab-btn px-4 py-3 text-sm font-bold border-b-2 border-brandDark text-brandDark whitespace-nowrap">
                Info & Team
            </button>
            <button type="button" onclick="switchModalTab('checklist')" id="tab-btn-checklist"
                    class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                Checkliste
            </button>
            <button type="button" onclick="switchModalTab('report')" id="tab-btn-report"
                    class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                Bericht
            </button>
            <button type="button" onclick="switchModalTab('history')" id="tab-btn-history"
                    class="tab-btn px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                Verlauf
            </button>
            </div>

            <!-- Body -->
            <div class="p-6 flex-1 overflow-y-auto bg-white space-y-6">
            <!-- TAB 1: INFO & TEAM -->
            <div id="tab-info" class="tab-content space-y-6">
                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Zeitplan</span>
                    <div class="text-sm font-bold text-slate-800" id="modal-schedule-2">—</div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Anfahrt</span>
                    <div class="text-sm font-bold text-slate-800" id="modal-travel">—</div>
                </div>
                </div>

                <div>
                <h3 class="text-sm font-bold mb-2">Beschreibung</h3>
                <textarea id="modal-edit-description"
                            class="w-full text-sm p-3 bg-slate-50 rounded-lg border border-slate-200 outline-none resize-none"
                            rows="4"
                            placeholder="Beschreibung…"></textarea>
                </div>

                <!-- ✅ Team manage (crew per job) -->
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-bold text-blue-900">Team verwalten</h3>
                    <button type="button" onclick="toggleTaskCrewEditor()" class="text-xs font-bold text-blue-600">
                    <i class="fa-solid fa-pen"></i> Editieren
                    </button>
                </div>

                <div id="modal-task-assignees" class="flex flex-wrap gap-2"></div>

                <div id="modal-task-crew-select" class="hidden mt-3 pt-3 border-t border-blue-200">
                    <p class="text-xs text-blue-500 mb-2">Techniker hinzufügen/entfernen:</p>
                    <div id="modal-task-crew-checkboxes" class="grid grid-cols-2 gap-2"></div>
                </div>
                </div>

                <!-- Optional containers -->
                <div id="modal-assigned-assets-container" class="hidden">
                <h3 class="text-sm font-bold mb-2">Assets</h3>
                <div id="modal-assigned-assets" class="grid grid-cols-2 gap-2"></div>
                </div>

                <div class="hidden" id="modal-deps-container">
                <h3 class="text-sm font-bold text-slate-900 mb-2">Abhängigkeiten</h3>
                <div id="modal-dependency-list" class="space-y-2"></div>
                </div>
            </div>

            <!-- TAB 2: CHECKLIST -->
            <div id="tab-checklist" class="tab-content space-y-4 hidden">
                <!-- ✅ REQUIRED: show current job description + date/time -->
                <div id="wf-checklist-job-meta" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                    <div class="text-[11px] font-extrabold tracking-wide text-slate-500 uppercase">Aktueller Job</div>
                    <div id="wf-checklist-job-title" class="mt-1 text-sm font-extrabold text-slate-900 truncate">—</div>
                    <div id="wf-checklist-job-desc" class="mt-1 text-xs text-slate-600 line-clamp-3">—</div>
                    </div>
                    <div class="shrink-0 text-right">
                    <div class="text-[11px] font-extrabold tracking-wide text-slate-500 uppercase">Zeit</div>
                    <div id="wf-checklist-job-dt" class="mt-1 text-sm font-extrabold text-slate-900">—</div>
                    <div id="wf-checklist-job-range" class="mt-1 text-[11px] font-semibold text-slate-600">—</div>
                    </div>
                </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-slate-900">Aufgabenliste</h3>
                <button type="button" onclick="addTaskSubtask()" class="text-xs text-blue-600 font-bold">+ Schritt hinzufügen</button>
                </div>
                <div id="modal-subtasks-list" class="space-y-2 text-xs text-slate-600">—</div>
            </div>

            <!-- TAB 3: REPORT -->
            <div id="tab-report" class="tab-content space-y-6 hidden">
                <div class="text-xs text-slate-500">—</div>
            </div>

            <!-- TAB 4: HISTORY -->
            <div id="tab-history" class="tab-content space-y-4 hidden">
                <div class="text-xs text-slate-500">—</div>
            </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t bg-slate-50 flex justify-between">
            <button type="button" onclick="deleteActiveTask()" class="text-red-500 font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> Löschen
            </button>
            <div class="flex gap-2">
                <button type="button" onclick="closeModal()" class="text-slate-500 font-bold text-sm px-4">Abbrechen</button>
                <button type="button" onclick="saveActiveTask()" class="bg-brandDark text-white px-6 py-2 rounded-lg font-bold text-sm shadow-lg">
                Speichern
                </button>
            </div>
            </div>

        </div>
        </div>



    <!-- Reason Modal (Pause / Stop) -->
    <div id="wf-reason-modal" class="fixed inset-0 z-[130] hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-close="1"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-200">
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                    <div class="font-extrabold text-slate-900" id="wf-reason-title">Grund angeben</div>
                    <div class="text-xs text-slate-500 mt-1 truncate" id="wf-reason-sub">—</div>
                    </div>
                    <button type="button" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700" data-close="1">
                    <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-3">
                    <label class="block text-xs font-bold text-slate-500">Nachricht / Begründung</label>
                    <textarea
                    id="wf-reason-text"
                    rows="4"
                    class="w-full p-3 rounded-2xl border border-slate-200 bg-white outline-none focus:ring-2 focus:ring-brandDark/20 focus:border-brandDark text-sm"
                    placeholder="Bitte Grund eingeben…"
                    ></textarea>

                    <div class="text-[11px] text-slate-400" id="wf-reason-hint">
                    Dieser Grund wird im Verlauf gespeichert.
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold" data-close="1">
                    Abbrechen
                    </button>
                    <button type="button" id="wf-reason-save" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold">
                    Speichern
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="wf-confirm-modal" class="fixed inset-0 z-[200] hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="wf-confirm-bg"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all scale-100">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900" id="wf-confirm-title">Löschen bestätigen</h3>
                    <div class="mt-2">
                        <p class="text-sm text-slate-500" id="wf-confirm-msg">
                            Möchten Sie dieses Element wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.
                        </p>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" id="wf-confirm-cancel"
                        class="w-full inline-flex justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-base font-bold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none sm:text-sm">
                        Abbrechen
                    </button>
                    <button type="button" id="wf-confirm-yes"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-4 py-2 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none sm:text-sm">
                        Löschen
                    </button>
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
 
    <style>
        /* Backdrop */
        .qs-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040; /* Behind sider, above page content */
            display: none; /* Toggled via JS */
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .qs-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Sidebar Container */
        .qs-sider {
            position: fixed;
            top: 0;
            right: -350px; /* Hidden initially */
            width: 350px;
            height: 100vh;
            background: #2c3e50; /* Dark blue-grey background */
            color: #ecf0f1;
            z-index: 1050;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
            transition: right 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .qs-sider.open {
            right: 0;
        }

        /* Header */
        .qs-header {
            padding: 15px 20px;
            background: #34495e;
            border-bottom: 1px solid #465c71;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .qs-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
        }
        .qs-header .btn-danger {
            padding: 4px 8px;
            font-size: 0.9rem;
        }

        /* Content Area */
        .qs-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            scrollbar-width: thin;
            scrollbar-color: #556b82 #2c3e50;
        }
        .qs-content::-webkit-scrollbar {
            width: 8px;
        }
        .qs-content::-webkit-scrollbar-thumb {
            background-color: #556b82;
            border-radius: 4px;
        }

        /* Grid Layout */
        .qs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns grid */
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Tiles */
        .qs-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #34495e;
            border: 1px solid #465c71;
            border-radius: 8px;
            padding: 15px 5px;
            text-align: center;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
            min-height: 90px;
        }
        .qs-tile:hover {
            background: #3e5871;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: #fff;
            text-decoration: none;
        }
        .qs-tile i, .qs-tile svg {
            font-size: 24px;
            margin-bottom: 8px;
            color: #3498db; /* Accent color for icons */
        }
        .qs-tile span {
            font-size: 0.8rem;
            line-height: 1.2;
        }

        /* Special Tile Colors */
        .qs-tile.sa-tile-present i { color: #2ecc71; }
        .qs-tile.sa-tile-absent i { color: #e74c3c; }
        .qs-tile.nav-log-off:hover { background: #c0392b; border-color: #c0392b; }
        .qs-tile.nav-log-off i { color: #e74c3c; }
        .qs-tile.nav-log-off:hover i { color: #fff; }

        /* Badges */
        .qs-badge, .sa-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
        }
        .badge-danger { background-color: #e74c3c; color: white; }
        .sa-badge-present { background-color: #2ecc71; color: white; }
        .sa-badge-absent { background-color: #e74c3c; color: white; }

        /* Expandable Sub-menus */
        .qs-has-sub {
            grid-column: span 3; /* Full width for expandable items if desired, or keep as tile */
            display: contents; /* Allows children to sit in grid */
        }
        /* If you want expandable items to look like tiles first */
        .qs-toggle {
            width: 100%;
            height: 100%;
            border: none;
            font-family: inherit;
        }
        .qs-sub {
            grid-column: 1 / -1; /* Submenu takes full row width */
            background: #233140;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #34495e;
            display: none; /* Hidden by default */
        }
        .qs-sub.show {
            display: block;
            animation: fadeIn 0.3s;
        }
        .qs-sub-item {
            display: block;
            padding: 8px 12px;
            color: #bdc3c7;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .qs-sub-item:hover {
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
        }
        .qs-caret {
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-size: 10px !important;
            opacity: 0.7;
        }

        /* Panels (Presence/Absence) */
        .sa-collapse {
            background: #fff;
            color: #333;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .sa-card-header {
            background: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .sa-card-body { padding: 10px; }
        .sa-input-group { display: flex; gap: 5px; }
        .sa-input {
            flex: 1;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .sa-search-btn {
            padding: 6px 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>


    <div id="quickSiderBackdrop" class="qs-backdrop"></div> 
    <aside id="quickSider" class="qs-sider">
        <div class="qs-header">
            <div class="d-flex align-items-center">
                <i class="feather icon-grid mr-2" style="font-size: 1.2rem; color: #3498db;"></i>
                <h5 class="mb-0">Schnellzugriff</h5>
            </div>
            <button class="btn btn-sm btn-danger" id="closeSiderBtn" aria-label="Close">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <div class="qs-content">
            
            <div class="qs-grid"> 

                <a class="qs-tile dashboard_view_icon" href="{{ url('/')}}">
                    <i class="feather icon-home"></i>
                    <span>Dashboard</span>
                </a> 

                <a class="qs-tile calendar_view_icon" href="{{ url('tasks/calendar/personal')}}">
                    <i class="feather icon-calendar"></i>
                    <span>Kalender</span>
                </a>
  

                <a class="qs-tile map_view_icon" href="{{ route('ai.chats') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:24px; height:24px; margin-bottom:8px; color: #3498db;"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="2.5" r="1.3"/>
                        <path d="M12 3.8v1.8"/>
                        <rect x="5" y="6" width="14" height="12" rx="3"/>
                        <rect x="3" y="9" width="2" height="6" rx="1"/>
                        <rect x="19" y="9" width="2" height="6" rx="1"/>
                        <circle cx="9" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="15" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                        <rect x="9" y="15" width="6" height="2" rx="1"/>
                    </svg>
                    <span>KI Chat</span>
                </a>

                <a class="qs-tile map_view_icon" href="{{ route('breaking-news.index') }}">
                    <i class="feather icon-alert-triangle" style="color: #f1c40f;"></i>
                    <span>News</span>
                </a>  
               

                <a class="qs-tile" href="{{ url('admin/todo/personal?tab=my') }}">
                    <i class="feather icon-check-square"></i>
                    <span>Aufgaben</span>
                </a>

                <a class="qs-tile" href="{{ url('/all-contacts') }}">
                    <i class="feather icon-users"></i>
                    <span>Kontakte</span>
                </a> 

                <a class="qs-tile nav-log-off" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="feather icon-power"></i>
                    <span>Logout</span>
                </a>

            </div>

            <div id="qs-sub-ticket" class="qs-sub">
                <h6 style="color:#7f8c8d; font-size:0.75rem; margin-bottom:5px; text-transform:uppercase;">Ticket Optionen</h6>
                <a class="qs-sub-item" href="{{ url('/error') }}">
                    <i class="feather icon-alert-triangle mr-2"></i> Fehler &amp; Fehlerheft
                </a>
                <a class="qs-sub-item" href="{{ url('problem_create') }}">
                    <i class="fa fa-ticket mr-2"></i> Anlegen
                </a>
                <a class="qs-sub-item" href="{{ url('problem_view') }}">
                    <i class="fa fa-wrench mr-2"></i> Liste
                </a>
            </div>

            <div id="qs-sub-prozess" class="qs-sub">
                <h6 style="color:#7f8c8d; font-size:0.75rem; margin-bottom:5px; text-transform:uppercase;">Prozess Optionen</h6>
                <a class="qs-sub-item" href="{{ url('lead/overview') }}">
                    <i class="feather icon-calendar mr-2"></i> Prozess Übersicht
                </a>
                <a class="qs-sub-item" href="{{ url('lead/kanban') }}">
                    <i class="feather icon-check-square mr-2"></i> Lead Kanban
                </a>
            </div>

            <div id="qs-sub-department" class="qs-sub">
                <h6 style="color:#7f8c8d; font-size:0.75rem; margin-bottom:5px; text-transform:uppercase;">Abteilungen</h6>
                <div class="p-2 text-center text-muted small js-dept-loading">Laden...</div>
                <div class="js-dept-error p-2 text-center text-danger small" style="display:none;"></div>
                <div class="js-dept-list"></div>
            </div>
 
            <div class="search-input-overlay d-none">
                </div>

        </div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    </aside>
    <!-- ============================================================
        0) Server config -> JS
        ============================================================ -->
    <script>
    /**
     * Planner bootstrap config from backend.
     *
     * REQUIRED:
     *  - plannerConfig.endpoints.customers
     *  - plannerConfig.endpoints.leadProducts   (string with ___ID___ placeholder)
     *
     * OPTIONAL (for plans dropdown):
     *  - plannerConfig.endpoints.plansByProject OR projectPlans OR plans
     */
    window.__WF_CONFIG = @json($plannerConfig);
    </script>

    <!-- ============================================================
        1) WF Core Runtime (state, helpers, http)
        ============================================================ -->
    <script>
    (() => {
    "use strict";

    /* ------------------------------------------------------------
    * 1.1) WF Namespace (global)
    * ------------------------------------------------------------ */
    window.__WF = window.__WF || {};
    const WF = window.__WF;

    /* ------------------------------------------------------------
    * 1.2) Config + Endpoints
    * ------------------------------------------------------------ */
    WF.cfg = window.__WF_CONFIG || {};
    WF.api = WF.cfg.endpoints || {};

    /* ------------------------------------------------------------
    * 1.3) Global State
    * ------------------------------------------------------------ */
    WF.state = WF.state || {
        customer: null,     // selected customer object
        project: null,      // normalized lead_product_lists object
        planId: null,       // selected plan id (saved plan)
        stage: null,
        phasesPayload: null,

        // You can map your tasks here later
        currentTasks: [],
    };

    /* ------------------------------------------------------------
    * 1.4) CSRF helper
    * ------------------------------------------------------------ */
    WF.csrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    /* ------------------------------------------------------------
    * 1.5) HTTP GET helper (same-origin + JSON)
    * ------------------------------------------------------------ */
    WF.httpGet = async (url, params = {}) => {
        if (!url) throw new Error("WF.httpGet: Missing URL");

        const u = new URL(url, window.location.origin);
        Object.entries(params).forEach(([k, v]) => {
        if (v === undefined || v === null || v === "") return;
        u.searchParams.set(k, String(v));
        });

        const res = await fetch(u.toString(), {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": WF.csrf(),
        },
        credentials: "same-origin",
        });

        const json = await res.json().catch(() => null);

        if (!res.ok) {
        const msg = json?.message || `HTTP ${res.status}`;
        throw new Error(msg);
        }

        return json;
    };

    /* ------------------------------------------------------------
    * 1.6) Toast helper (uses your #toast UI)
    * ------------------------------------------------------------ */
    WF.toast = (msg) => {
        const t = document.getElementById("toast");
        if (!t) return alert(msg);

        const h4 = t.querySelector("div h4");
        if (h4) h4.innerText = msg;

        t.classList.remove("translate-y-20", "opacity-0");
        setTimeout(() => t.classList.add("translate-y-20", "opacity-0"), 2500);
    };

    })();
    </script>

    <!-- ============================================================
        2) Customer Search Dropdown (input + custom listbox)
        ============================================================ -->
    <script>
    (() => {
    "use strict";
    const WF = window.__WF;

    /* ------------------------------------------------------------
    * 2.1) DOM refs
    * ------------------------------------------------------------ */
    const input = document.getElementById("customer-search-input");
    const list = document.getElementById("customer-dropdown-list");
    const chevron = document.getElementById("customer-chevron");
    const container = document.getElementById("customer-select-container");

    /* ------------------------------------------------------------
    * 2.2) Guards (if partial loaded elsewhere)
    * ------------------------------------------------------------ */
    if (!input || !list || !container) return;

    /* ------------------------------------------------------------
    * 2.3) Debounce state
    * ------------------------------------------------------------ */
    let lastQuery = "";
    let debounceId = null;

    /* ------------------------------------------------------------
    * 2.4) Customer label for dropdown + input
    * ------------------------------------------------------------ */
    function customerLabel(c) {
        const name = c?.firma
        ? c.firma
        : [c?.title, c?.academic_title, c?.name, c?.lastname].filter(Boolean).join(" ").trim();

        const sub = [c?.customer_no, c?.city, c?.postcode].filter(Boolean).join(" • ");
        return sub ? `${name} — ${sub}` : name;
    }

    /* ------------------------------------------------------------
    * 2.5) Dropdown open/close UI
    * ------------------------------------------------------------ */
    function openList() {
        list.classList.remove("hidden");
        chevron?.classList.add("rotate-180");
    }

    function closeList() {
        list.classList.add("hidden");
        chevron?.classList.remove("rotate-180");
    }

    /* ------------------------------------------------------------
    * 2.6) Render customers into listbox
    * ------------------------------------------------------------ */
    function renderCustomers(rows) {
        list.innerHTML = "";

        if (!rows.length) {
        const empty = document.createElement("div");
        empty.className = "px-4 py-3 text-sm text-slate-500";
        empty.textContent = "No customers found.";
        list.appendChild(empty);
        return;
        }

        rows.forEach((c) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className =
            "w-full text-left px-4 py-3 hover:bg-slate-50 text-sm text-slate-700 flex items-start gap-3";
        item.innerHTML = `
            <span class="mt-0.5 text-slate-400"><i class="fa-solid fa-user-tie"></i></span>
            <span class="font-semibold leading-5">${customerLabel(c)}</span>
        `;

        item.addEventListener("click", async () => {
            // Update state
            WF.state.customer = c;
            WF.state.project = null;
            WF.state.planId = null;

            // Reflect in UI
            input.value = customerLabel(c);
            closeList();

            // Load projects for this customer
            try {
            await WF.loadLeadProducts();
            } catch (e) {
            WF.toast(e?.message || "Failed loading projects");
            }
        });

        list.appendChild(item);
        });
    }

    /* ------------------------------------------------------------
    * 2.7) Fetch customers via API
    * ------------------------------------------------------------ */
    async function fetchCustomers(q) {
        if (!WF.api?.customers) throw new Error("Missing endpoint: endpoints.customers");
        const resp = await WF.httpGet(WF.api.customers, { q: q || "" });
        return Array.isArray(resp?.data) ? resp.data : [];
    }

    /* ------------------------------------------------------------
    * 2.8) Expose globals (because HTML uses inline handlers)
    * ------------------------------------------------------------ */
    window.showCustomerDropdown = async function showCustomerDropdown() {
        openList();
        try {
        const rows = await fetchCustomers(input.value || "");
        renderCustomers(rows);
        } catch {
        renderCustomers([]);
        }
    };

    window.filterCustomerDropdown = function filterCustomerDropdown() {
        lastQuery = (input.value || "").trim();
        openList();

        clearTimeout(debounceId);
        debounceId = setTimeout(async () => {
        try {
            const rows = await fetchCustomers(lastQuery);
            renderCustomers(rows);
        } catch {
            renderCustomers([]);
        }
        }, 250);
    };

    /* ------------------------------------------------------------
    * 2.9) Click outside -> close
    * ------------------------------------------------------------ */
    document.addEventListener("click", (ev) => {
        if (!container.contains(ev.target)) closeList();
    });

    })();
    </script>

<!-- ============================================================
     3) Lead Products / Project Selector (Select2 + templates)
     - Shows: article_group_name + product_name (not only IDs)
     - Fixes: recursion/stack overflow (no dispatchEvent loop)
     ============================================================ -->
<script>
(() => {
  "use strict";
  const WF = window.__WF;

  /* ------------------------------------------------------------
   * 3.1) URL builder (leadProducts uses ___ID___ placeholder)
   * ------------------------------------------------------------ */
  function leadProductsUrl(customerId) {
    const tpl = WF.api?.leadProducts || "";
    if (!tpl) throw new Error("Missing endpoint: endpoints.leadProducts");
    return tpl.replace("___ID___", String(customerId));
  }

  /* ------------------------------------------------------------
   * 3.2) Status mapping helper
   * ------------------------------------------------------------ */
  function statusToGerman(s) {
    const v = String(s || "").trim().toLowerCase();
    const map = {
      open: "Offen",
      opened: "Offen",
      new: "Neu",
      planned: "Geplant",
      scheduled: "Eingeplant",
      in_progress: "In Arbeit",
      inprogress: "In Arbeit",
      processing: "In Bearbeitung",
      waiting: "Wartet",
      pending: "Ausstehend",
      on_hold: "Pausiert",
      paused: "Pausiert",
      done: "Erledigt",
      completed: "Abgeschlossen",
      finished: "Fertig",
      canceled: "Storniert",
      cancelled: "Storniert",
      rejected: "Abgelehnt",
      archive: "Archiviert",
      archived: "Archiviert",
    };
    return map[v] || (s ? String(s) : "—");
  }

  /* ------------------------------------------------------------
   * 3.3) Labels (article group + product)
   * ------------------------------------------------------------ */
  function articleGroupLabel(r) {
    return (
      r?.article_group_name ||
      r?.article_group_title ||
      r?.article_group_label ||
      r?.article_group ||
      r?.group_name ||
      r?.group_title ||
      (r?.article_group_id ? `Produktgruppe #${r.article_group_id}` : "")
    );
  }

  function productLabel(r) {
    return (
      r?.product_title ||
      r?.product_product ||
      r?.product_name ||
      [r?.product_model, r?.product_status].filter(Boolean).join(" • ") ||
      (r?.product_id ? `Produkt #${r.product_id}` : "")
    );
  }

  function productIconLetter(r) {
    const t = String(r?.product_title || r?.product_product || r?.product_name || "").trim();
    return (t[0] || "?").toUpperCase();
  }

  function projectSelectText(r) {
    const grp = articleGroupLabel(r);
    const prod = productLabel(r);

    const statusRaw =
      r?.lead_product_status ||
      r?.status ||
      r?.project_status ||
      r?.lead_status ||
      r?.stage_status ||
      r?.article_group_status;

    const statusDe = r?.lead_product_status_de || statusToGerman(statusRaw);

    const parts = [];
    if (grp) parts.push(grp);
    if (prod) parts.push(prod);

    return `#${r?.id} — ${parts.join(" / ")} • ${statusDe}`;
  }

  /* ------------------------------------------------------------
   * 3.4) Resetters (project + plan)
   * ------------------------------------------------------------ */
  WF.resetProjectSelect = () => {
    const sel = document.getElementById("project-selector");
    if (!sel) return;

    if (window.jQuery && $(sel).data("select2")) $(sel).select2("destroy");

    sel.innerHTML = `<option value=""></option>`;
    sel.disabled = true;

    WF._leadProductsById = {};
  };

  // Plan reset is defined again below in Plans script; keep safe here too.
  WF.resetPlanSelect = WF.resetPlanSelect || (() => {
    const sel = document.getElementById("plan-selector");
    if (!sel) return;
    sel.innerHTML = `<option value="">Plan wählen…</option>`;
    sel.disabled = true;
  });

  /* ------------------------------------------------------------
   * 3.5) Project change handler (guarded)
   * ------------------------------------------------------------ */
  WF._isHandlingProjectChange = false;

  WF.handleProjectChange = async function handleProjectChange() {
    if (WF._isHandlingProjectChange) return;
    WF._isHandlingProjectChange = true;

    try {
      const sel = document.getElementById("project-selector");
      if (!sel) return;

      const id = sel.value ? String(sel.value) : "";

      // no selection -> clear
      if (!id) {
        WF.state.project = null;
        WF.state.planId = null;
        WF.resetPlanSelect();
        return;
      }

      const r = WF._leadProductsById?.[id];
      if (!r) {
        WF.toast("Fehler: Produktdaten nicht gefunden.");
        return;
      }

      const grp = articleGroupLabel(r);
      const prod = productLabel(r);

      // Normalize into WF.state.project
      WF.state.project = {
        project_id: Number(r.id),
        customer_id: Number(r.customer_id),
        alternative_id: r.alternative_id ? Number(r.alternative_id) : null,
        article_group_id: r.article_group_id ? Number(r.article_group_id) : null,
        product_id: r.product_id ? Number(r.product_id) : null,

        // requested fields
        product_name: prod,
        article_group_name: grp || "",

        // extras
        product_title: r.product_title || "",
        lead_product_status: r.lead_product_status || "",
        lead_product_status_de: r.lead_product_status_de || "",
      };

      if (!WF.state.project.article_group_id) {
        WF.toast("Fehler: article_group_id fehlt (Produktgruppe nicht gefunden).");
        WF.resetPlanSelect();
        return;
      }

      // After selecting project, load plans dropdown (if exists)
      if (typeof WF.loadPlansForSelectedProject === "function") {
        await WF.loadPlansForSelectedProject();
      }

      // If you also have downstream loader, keep it optional
      if (typeof WF.loadPlansAndPhases === "function") {
        await WF.loadPlansAndPhases();
      }
    } finally {
      WF._isHandlingProjectChange = false;
    }
  };

  /* ------------------------------------------------------------
   * 3.6) Inline HTML handler: onchange="changeProject(this.value)"
   * IMPORTANT: no dispatchEvent (prevents recursion)
   * ------------------------------------------------------------ */
  window.changeProject = function changeProject(value) {
    const sel = document.getElementById("project-selector");
    if (!sel) return;

    if (value !== undefined && value !== null && String(value) !== sel.value) {
      sel.value = String(value);
    }

    WF.handleProjectChange();
  };

  /* ------------------------------------------------------------
   * 3.7) Init Select2 for project selector
   * ------------------------------------------------------------ */
  WF.initProjectSelect2 = () => {
    const sel = document.getElementById("project-selector");
    if (!sel || !window.jQuery) return;

    if ($(sel).data("select2")) return;

    $(sel).select2({
      width: "100%",
      placeholder: "Select product & site…",
      allowClear: true,

      templateResult: (data) => {
        if (!data.id) return data.text;

        const r = WF._leadProductsById?.[String(data.id)];
        if (!r) return data.text;

        const grp = articleGroupLabel(r);
        const prod = productLabel(r);
        const letter = productIconLetter(r);

        const statusRaw =
          r.lead_product_status ||
          r.status ||
          r.project_status ||
          r.lead_status ||
          r.stage_status ||
          r.article_group_status;

        const statusDe = r.lead_product_status_de || statusToGerman(statusRaw);

        return $(`
          <div class="flex items-start gap-3">
            <div class="mt-0.5 w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-extrabold text-slate-600">
              ${letter}
            </div>
            <div class="min-w-0">
              <div class="text-sm font-bold text-slate-800 truncate">${prod}</div>
              <div class="text-xs text-slate-500 truncate">${grp ? grp + " • " : ""}${statusDe}</div>
            </div>
          </div>
        `);
      },

      templateSelection: (data) => {
        if (!data.id) return data.text || "Select product & site…";

        const r = WF._leadProductsById?.[String(data.id)];
        if (!r) return data.text;

        const grp = articleGroupLabel(r);
        const prod = productLabel(r);
        const letter = productIconLetter(r);

        return $(`
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-slate-100 border border-slate-200 flex items-center justify-center font-extrabold text-[12px] text-slate-600">
              ${letter}
            </div>
            <div class="text-sm font-semibold text-slate-700 truncate">
              ${grp ? `<span class="text-slate-500">${grp}</span> / ` : ""}${prod}
            </div>
          </div>
        `);
      },

      escapeMarkup: (m) => m,
    });

    // Single source of truth: call WF.handleProjectChange()
    $(sel).on("change", () => WF.handleProjectChange());
  };

  /* ------------------------------------------------------------
   * 3.8) Load lead products for selected customer
   * ------------------------------------------------------------ */
  WF.loadLeadProducts = async () => {
    WF.resetProjectSelect();
    WF.resetPlanSelect();

    const customer = WF.state.customer;
    if (!customer?.id) return;

    const resp = await WF.httpGet(leadProductsUrl(customer.id));
    const rows = Array.isArray(resp?.lead_product_lists) ? resp.lead_product_lists : [];

    WF._leadProductsById = {};
    rows.forEach((r) => (WF._leadProductsById[String(r.id)] = r));

    const sel = document.getElementById("project-selector");
    if (!sel) return;

    sel.disabled = false;
    sel.innerHTML = `<option value=""></option>`; // allowClear placeholder

    rows.forEach((r) => {
      const opt = document.createElement("option");
      opt.value = String(r.id);
      opt.textContent = projectSelectText(r);
      sel.appendChild(opt);
    });

    WF.initProjectSelect2();

    if (window.jQuery && $(sel).data("select2")) {
      $(sel).prop("disabled", false).trigger("change.select2");
    }
  };

})();
</script>

<!-- ============================================================
     4) Plans Dropdown (saved plans list + "create new plan")
     - Requires endpoint: plansByProject OR projectPlans OR plans
     - If no plans: auto selects "__new__" -> opens wizard
     ============================================================ -->
<script>
(() => {
  "use strict";
  const WF = window.__WF;

  /* ------------------------------------------------------------
   * 4.1) Endpoint resolver (supports multiple naming styles)
   * ------------------------------------------------------------ */
  function resolvePlansEndpoint() {
    const candidates = [
      WF.api?.plansByProject,
      WF.api?.projectPlans,
      WF.api?.plans,
      WF.api?.plannerPlans,
    ].filter(Boolean);

    if (!candidates.length) {
      throw new Error(
        "Missing plans endpoint. Add one to $plannerConfig.endpoints: plansByProject OR projectPlans OR plans."
      );
    }
    return candidates[0];
  }

  /* ------------------------------------------------------------
   * 4.2) Build URL (supports placeholder OR query params)
   * ------------------------------------------------------------ */
  function buildPlansUrl(project) {
    const tpl = resolvePlansEndpoint();
    const hasPlaceholders = /___[A-Z0-9_]+___/.test(tpl);

    if (hasPlaceholders) {
      return tpl
        .replace("___CUSTOMER_ID___", String(project?.customer_id ?? ""))
        .replace("___PROJECT_ID___", String(project?.project_id ?? project?.id ?? ""))
        .replace("___LEAD_PRODUCT_ID___", String(project?.project_id ?? project?.id ?? ""))
        .replace("___ARTICLE_GROUP_ID___", String(project?.article_group_id ?? ""))
        .replace("___ALT_ID___", String(project?.alternative_id ?? ""));
    }

    return tpl; // query params will be passed separately
  }

  /* ------------------------------------------------------------
   * 4.3) Plan label normalizer
   * ------------------------------------------------------------ */
  function planLabel(p) {
    const title =
      p?.title ||
      p?.name ||
      p?.plan_title ||
      p?.reference ||
      (p?.id ? `Plan #${p.id}` : "Plan");

    const meta = [
      p?.stage ? `Stage: ${p.stage}` : null,
      p?.created_at ? `Erstellt: ${String(p.created_at).slice(0, 10)}` : null,
    ]
      .filter(Boolean)
      .join(" • ");

    return meta ? `${title} — ${meta}` : title;
  }

  /* ------------------------------------------------------------
   * 4.4) Reset plan selector
   * ------------------------------------------------------------ */
  WF.resetPlanSelect = () => {
    const sel = document.getElementById("plan-selector");
    if (!sel) return;

    if (window.jQuery && $(sel).data("select2")) $(sel).select2("destroy");

    sel.innerHTML = `<option value="">Plan wählen…</option>`;
    sel.disabled = true;

    WF._plansById = {};
    WF._lastPlansList = [];
  };

  /* ------------------------------------------------------------
   * 4.5) Render plans
   * ------------------------------------------------------------ */
  function renderPlans(plans) {
    const sel = document.getElementById("plan-selector");
    if (!sel) return;

    sel.innerHTML = "";

    // placeholder
    const ph = document.createElement("option");
    ph.value = "";
    ph.textContent = "Plan wählen…";
    sel.appendChild(ph);

    // saved plans
    plans.forEach((p) => {
      const opt = document.createElement("option");
      opt.value = String(p.id);
      opt.textContent = planLabel(p);
      sel.appendChild(opt);
    });

    // separator
    const sep = document.createElement("option");
    sep.value = "__sep__";
    sep.textContent = "────────────";
    sep.disabled = true;
    sel.appendChild(sep);

    // always allow new plan
    const create = document.createElement("option");
    create.value = "__new__";
    create.textContent = "➕ Create new plan";
    sel.appendChild(create);

    sel.disabled = false;
  }

  /* ------------------------------------------------------------
   * 4.6) Fetch plans for selected project
   * ------------------------------------------------------------ */
  async function fetchPlansForProject(project) {
    if (!project?.project_id && !project?.id) return [];

    const endpoint = resolvePlansEndpoint();
    const hasPlaceholders = /___[A-Z0-9_]+___/.test(endpoint);

    const url = buildPlansUrl(project);

    const params = hasPlaceholders
      ? {}
      : {
          customer_id: project.customer_id ?? "",
          project_id: project.project_id ?? project.id ?? "",
          lead_product_id: project.project_id ?? project.id ?? "",
          article_group_id: project.article_group_id ?? "",
          alternative_id: project.alternative_id ?? "",
        };

    const resp = await WF.httpGet(url, params);

    // accept multiple backend shapes
    const plans =
      (Array.isArray(resp?.plans) && resp.plans) ||
      (Array.isArray(resp?.data) && resp.data) ||
      (Array.isArray(resp?.planner_plans) && resp.planner_plans) ||
      [];

    return plans;
  }

  /* ------------------------------------------------------------
   * 4.7) Public loader: load plans dropdown for selected project
   * ------------------------------------------------------------ */
  WF.loadPlansForSelectedProject = async () => {
    WF.resetPlanSelect();

    const project = WF.state.project;
    if (!project?.project_id && !project?.id) return;

    const plans = await fetchPlansForProject(project);

    WF._plansById = {};
    plans.forEach((p) => (WF._plansById[String(p.id)] = p));
    WF._lastPlansList = plans;

    renderPlans(plans);

    // If no plans, auto select "__new__" and open wizard
    if (!plans.length) {
      const sel = document.getElementById("plan-selector");
      if (sel) {
        sel.value = "__new__";
        await WF.handlePlanChange();
      }
    }
  };

  /* ------------------------------------------------------------
   * 4.8) Plan change handler (guarded)
   * ------------------------------------------------------------ */
  WF._isHandlingPlanChange = false;

  WF.handlePlanChange = async () => {
    if (WF._isHandlingPlanChange) return;
    WF._isHandlingPlanChange = true;

    try {
      const sel = document.getElementById("plan-selector");
      if (!sel) return;

      const val = String(sel.value || "");

      // placeholder
      if (!val) {
        WF.state.planId = null;
        return;
      }

      // separator
      if (val === "__sep__") return;

      // create new plan
      if (val === "__new__") {
        WF.state.planId = null;

        if (typeof window.openPlanWizard === "function") {
          window.openPlanWizard();
          return;
        }

        WF.toast("No plan exists. Please create a new plan.");
        return;
      }

      // existing plan
      WF.state.planId = Number(val);

      // optional: load phases/tasks for this plan
      if (typeof WF.loadPlansAndPhases === "function") {
        await WF.loadPlansAndPhases();
      } else {
        WF.toast("Plan selected. Implement WF.loadPlansAndPhases() to load phases/tasks.");
      }
    } finally {
      WF._isHandlingPlanChange = false;
    }
  };

  /* ------------------------------------------------------------
   * 4.9) Bind onchange once
   * ------------------------------------------------------------ */
  window.addEventListener("load", () => {
    const sel = document.getElementById("plan-selector");
    if (!sel) return;

    if (sel.dataset.bound === "1") return;
    sel.dataset.bound = "1";

    sel.addEventListener("change", () => WF.handlePlanChange());
  });

})();
</script>

<!-- ============================================================
     5) Page Boot (resets on load)
     ============================================================ -->
<script>
(() => {
  "use strict";

  window.addEventListener("load", () => {
    const WF = window.__WF;

    // Always start clean
    if (typeof WF.resetProjectSelect === "function") WF.resetProjectSelect();
    if (typeof WF.resetPlanSelect === "function") WF.resetPlanSelect();

    // Optional: other initializers can be placed here
    // initSortables();
    // switchMainTab('planning');
  });

})();
</script>


<!-- Planner Wizard  -->
<script>
(() => {
  "use strict";

  /* =========================================================================
   * Planner Wizard (routes prefixed with /planner)
   * Context from:
   *   window.__WF.state.customer + window.__WF.state.project
   *
   * Features:
   * ✅ Projectleiter (single select2) with avatar/picture
   * ✅ Team (multi select2) with avatar/picture
   * ✅ Date + Time per section (project / tickets / appointments / personal / custom)
   * ✅ Item cards show responsible + date/time
   * ✅ Selecting an item opens role modal (PM/Team/Date/Time) and auto-fills from item
   * ✅ FIXED: Select2 dropdown z-index in modals (dropdownParent + CSS)
   * ✅ FIXED: "Übernehmen" now correctly updates global PM/Team fields (seeds options + triggers change)
   *
   * Endpoints:
   *  GET  /planner/phases
   *  GET  /planner/problems
   *  GET  /planner/appointments
   *  GET  /planner/personal-tasks
   *  GET  employeesActive (your controller)  q=...
   *  POST /planner/plans/store-wizard
   * ========================================================================= */

  const BASE_URL =
    document.querySelector('meta[name="planner-base-url"]')?.getAttribute("content") ||
    (window.location.origin + "/planner");

  const CSRF =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const $ = (id) => document.getElementById(id);
  const WF = window.__WF || null;

  function url(path) {
    return `${BASE_URL}${path}`;
  }

  /* =========================================================================
   * Global z-index fix for Select2 in modals
   * ========================================================================= */
  function injectSelect2ZFixOnce() {
    if (document.getElementById("wf-select2-zfix")) return;
    const st = document.createElement("style");
    st.id = "wf-select2-zfix";
    st.textContent = `
      /* Ensure Select2 dropdown is above any modal/overlay */
      .select2-container { z-index: 100000 !important; }
      .select2-dropdown  { z-index: 100001 !important; }
      .select2-container--open { z-index: 100002 !important; }
    `;
    document.head.appendChild(st);
  }
  injectSelect2ZFixOnce();

  const Wizard = {
    open: false,
    type: "project",
    ctx: {
      customer_id: null,
      alternative_id: null,
      product_id: null,   // article_group_id
      project_id: null,   // lead_product_lists.id
      customer_name: "Kunde",
    },

    phasesResp: null,
    ticketsResp: null,
    apptsResp: null,
    personalResp: null,
    ticketTasksByTicketId: {},

    selected: {
      phase_activity_ids: new Set(),
      ticket_task_ids: new Set(),
      appointment_ids: new Set(),
      personal_task_ids: new Set(),
      custom_steps: [],

      // global assignment (synced across sections)
      pm_id: null,
      crew_ids: new Set(),

      // global planned datetime (synced across sections)
      planned_date: "",
      planned_time: "",
      planned_datetime: null,

      asset_qty: {},
    },

    // per-item meta (merged into items[].meta)
    itemMeta: {},

    employees: {
      byId: {},
      lastList: [],
      lastQuery: "",
      loadedOnce: false,
    },

    _syncingAssign: false,
    _syncingPlanDT: false,
    _roleModalOpen: false,
    _lastRoleContextKey: null,
  };

  /* =========================================================================
   * Context resolver (CUSTOMER + PROJECT)
   * ========================================================================= */
  function resolveWizardContext(passedCtx) {
    const ctx = passedCtx && typeof passedCtx === "object" ? passedCtx : {};
    const wfCustomer = WF?.state?.customer || null;
    const wfProject  = WF?.state?.project  || null;

    const headerCustomerText = ($("customer-search-input")?.value || "").trim();

    const customer_id =
      Number(ctx.customer_id || 0) ||
      Number(wfCustomer?.id || 0) ||
      null;

    const project_id =
      Number(ctx.project_id || 0) ||
      Number(wfProject?.project_id || wfProject?.id || 0) ||
      (Number($("project-selector")?.value || 0) || null);

    const alternative_id =
      Number(ctx.alternative_id || 0) ||
      Number(wfProject?.alternative_id || 0) ||
      null;

    const product_id =
      Number(ctx.product_id || 0) ||
      Number(wfProject?.article_group_id || 0) ||
      null;

    const customer_name =
      String(ctx.customer_name || "").trim() ||
      String(wfCustomer?.firma || "").trim() ||
      String(
        [wfCustomer?.title, wfCustomer?.academic_title, wfCustomer?.name, wfCustomer?.lastname]
          .filter(Boolean)
          .join(" ")
      ).trim() ||
      headerCustomerText ||
      "Kunde";

    return { customer_id, project_id, alternative_id, product_id, customer_name };
  }

  function ensureWizardContextNote() {
    if (!Wizard.ctx.customer_id) { alert("Bitte zuerst einen Kunden auswählen."); return false; }
    if (!Wizard.ctx.project_id) { alert("Bitte zuerst ein Produkt/Objekt auswählen."); return false; }
    if (!Wizard.ctx.product_id) { alert("Dieses Produkt/Objekt hat keine Produktgruppe (article_group_id)."); return false; }
    return true;
  }

  /* =========================================================================
   * UI helpers
   * ========================================================================= */
  function show(el, yes) {
    if (!el) return;
    el.classList.toggle("hidden", !yes);
  }

  function setLoading(container, yes, text = "Lade...") {
    if (!container) return;
    if (yes) {
      container.innerHTML =
        `<div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-500">${escapeHtml(text)}</div>`;
    }
  }

  function setError(container, title, details) {
    if (!container) return;
    container.innerHTML = `
      <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-700">
        <div class="font-bold mb-1">${escapeHtml(title || "Fehler")}</div>
        ${details ? `<div class="text-xs opacity-90 break-words">${escapeHtml(details)}</div>` : ""}
      </div>
    `;
  }

  /* =========================================================================
   * API
   * ========================================================================= */
  async function apiGet(pathOrUrl, params = {}, isAbsolute = false) {
    const base = window.location.origin;
    const target = isAbsolute ? pathOrUrl : url(pathOrUrl);
    const u = new URL(target, base);

    Object.entries(params).forEach(([k, v]) => {
      if (v === undefined || v === null || v === "") return;
      u.searchParams.set(k, String(v));
    });

    const res = await fetch(u.toString(), {
      method: "GET",
      headers: { "Accept": "application/json" },
      credentials: "same-origin",
    });

    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text().catch(() => "");

    if (!ct.includes("application/json")) {
      throw new Error(`Non-JSON response (status ${res.status}). Snippet: ${raw.slice(0, 180)}`);
    }

    const json = JSON.parse(raw);
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${raw.slice(0, 300)}`);
    return json;
  }

  async function apiPost(path, payload) {
    const res = await fetch(url(path), {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF,
      },
      credentials: "same-origin",
      body: JSON.stringify(payload || {}),
    });

    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text().catch(() => "");

    if (!ct.includes("application/json")) {
      throw new Error(`Non-JSON response (status ${res.status}). Snippet: ${raw.slice(0, 180)}`);
    }

    const json = JSON.parse(raw);
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${raw.slice(0, 300)}`);
    return json;
  }

  /* =========================================================================
   * Employees endpoint + avatars
   * ========================================================================= */
  function resolveEmployeesEndpoint() {
    const cfg = (window.__WF_CONFIG && window.__WF_CONFIG.endpoints) ? window.__WF_CONFIG.endpoints : null;
    const fromCfg = cfg?.employeesActive || cfg?.employees_active || cfg?.activeEmployees;
    const fromWF  = WF?.api?.employeesActive || WF?.api?.employees_active || WF?.api?.activeEmployees;
    return fromCfg || fromWF || null;
  }

  function employeeFullName(e) {
    const name = [e?.title, e?.name, e?.lastname].filter(Boolean).join(" ").trim();
    return name || (e?.email ? String(e.email) : `#${e?.id}`);
  }

  function initialsFromEmployee(e) {
    const n = String(e?.name || "").trim();
    const l = String(e?.lastname || "").trim();
    const a = (n[0] || "").toUpperCase();
    const b = (l[0] || "").toUpperCase();
    return (a + b) || (String(employeeFullName(e))[0] || "?").toUpperCase();
  }

  function dataSvgAvatar(initials) {
    const safe = String(initials || "?").slice(0, 2);
    const svg =
      `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">
        <defs>
          <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#e2e8f0"/>
            <stop offset="1" stop-color="#cbd5e1"/>
          </linearGradient>
        </defs>
        <rect width="64" height="64" rx="18" fill="url(#g)"/>
        <text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle"
              font-family="Plus Jakarta Sans, Arial, sans-serif"
              font-size="22" font-weight="800" fill="#334155">${safe}</text>
      </svg>`;
    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
  }

  function normalizeAvatarUrl(raw) {
    const v = String(raw || "").trim();
    if (!v) return "";
    if (/^data:image\//i.test(v)) return v;
    if (/^https?:\/\//i.test(v)) return v;
    if (v.startsWith("/")) return v;
    if (v.includes("/")) return `/${v}`;
    return `${BASE_URL.replace(/\/$/, "")}/storage/${v}`;
  }

  function employeeAvatarUrl(e) {
    const maybe =
      e?.photo_url || e?.avatar_url || e?.image_url || e?.profile_image_url ||
      e?.photo || e?.avatar || e?.image || e?.profile_image;

    const u = normalizeAvatarUrl(maybe);
    return u || dataSvgAvatar(initialsFromEmployee(e));
  }

  async function fetchEmployees(q) {
    const endpoint = resolveEmployeesEndpoint();
    const candidates = [];
    if (endpoint) candidates.push(endpoint);
    candidates.push(url("/employees/active"));
    candidates.push(url("/employees-active"));

    const query = String(q || "").trim();
    let lastErr = null;

    for (const ep of candidates) {
      try {
        const res = await apiGet(ep, { q: query }, true);

        const rows = Array.isArray(res?.data) ? res.data : [];
        Wizard.employees.lastQuery = query;
        Wizard.employees.lastList = rows;
        Wizard.employees.loadedOnce = true;

        for (const e of rows) {
          if (!e?.id) continue;
          Wizard.employees.byId[String(e.id)] = e;
        }
        return rows;
      } catch (e) {
        lastErr = e;
      }
    }
    throw (lastErr || new Error("Failed loading employees"));
  }

  function ensureOption(selectEl, emp, selected = false) {
    if (!selectEl || !emp?.id) return;
    const id = String(emp.id);
    let opt = Array.from(selectEl.options || []).find(o => String(o.value) === id);
    if (!opt) {
      opt = document.createElement("option");
      opt.value = id;
      opt.textContent = employeeFullName(emp);
      selectEl.appendChild(opt);
    }
    if (selected) opt.selected = true;
  }

  function seedSingleSelectOption(selectEl, id) {
    if (!selectEl) return;
    const v = id ? String(id) : "";
    if (!v) return;

    let opt = Array.from(selectEl.options || []).find(o => String(o.value) === v);
    if (!opt) {
      const emp = Wizard.employees.byId[v] || null;
      opt = document.createElement("option");
      opt.value = v;
      opt.textContent = emp ? employeeFullName(emp) : `#${v}`;
      selectEl.appendChild(opt);
    }
  }

  function seedMultiSelectOptions(selectEl, ids) {
    if (!selectEl) return;
    const arr = Array.isArray(ids) ? ids : [];
    for (const id of arr) {
      const v = id ? String(id) : "";
      if (!v) continue;

      let opt = Array.from(selectEl.options || []).find(o => String(o.value) === v);
      if (!opt) {
        const emp = Wizard.employees.byId[v] || null;
        opt = document.createElement("option");
        opt.value = v;
        opt.textContent = emp ? employeeFullName(emp) : `#${v}`;
        selectEl.appendChild(opt);
      }
    }
  }

  function select2TemplateEmployee(item) {
    const emp = item?.employee || item?._employee || Wizard.employees.byId[String(item?.id || "")] || null;
    if (!emp) return item?.text || "";

    const name = employeeFullName(emp);
    const sub = [emp?.email, emp?.phone].filter(Boolean).join(" • ");
    const img = employeeAvatarUrl(emp);

    return `
      <div class="flex items-center gap-3">
        <img src="${escapeHtml(img)}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="">
        <div class="min-w-0">
          <div class="text-sm font-bold text-slate-800 truncate">${escapeHtml(name)}</div>
          ${sub ? `<div class="text-xs text-slate-500 truncate">${escapeHtml(sub)}</div>` : ""}
        </div>
      </div>
    `;
  }

  function select2TemplateSelection(item) {
    const emp = item?.employee || item?._employee || Wizard.employees.byId[String(item?.id || "")] || null;
    if (!emp) return item?.text || "";
    const name = employeeFullName(emp);
    const img = employeeAvatarUrl(emp);

    return `
      <div class="flex items-center gap-2">
        <img src="${escapeHtml(img)}" class="w-5 h-5 rounded-full object-cover border border-slate-200" alt="">
        <span class="text-sm font-semibold text-slate-700 truncate">${escapeHtml(name)}</span>
      </div>
    `;
  }

  function destroySelect2(el) {
    if (!el || !window.jQuery) return;
    const $el = window.jQuery(el);
    if ($el.data("select2")) {
      try { $el.select2("destroy"); } catch { /* ignore */ }
    }
  }

  function initSelect2(el, { multiple = false, placeholder = "Wählen...", allowClear = true, dropdownParent = null } = {}) {
    if (!el || !window.jQuery || !window.jQuery.fn?.select2) return false;

    const $el = window.jQuery(el);

    // If already initialized, destroy so dropdownParent can be applied reliably
    if ($el.data("select2")) {
      try { $el.select2("destroy"); } catch { /* ignore */ }
    }

    $el.select2({
      width: "100%",
      placeholder,
      allowClear,
      closeOnSelect: !multiple,
      multiple,
      minimumInputLength: 0,

      // ✅ critical: keep dropdown inside the visible modal/card to avoid z-index issues
      dropdownParent: dropdownParent ? window.jQuery(dropdownParent) : window.jQuery(document.body),

      ajax: {
        delay: 250,
        transport: (params, success, failure) => {
          const term = params?.data?.term || "";
          fetchEmployees(term)
            .then((rows) => {
              const results = rows.map((e) => ({
                id: String(e.id),
                text: employeeFullName(e),
                employee: e,
              }));
              success({ results });
            })
            .catch((err) => failure(err));
        },
        processResults: (data) => data,
      },

      templateResult: (data) => {
        if (!data.id) return data.text;
        if (data.employee?.id) Wizard.employees.byId[String(data.employee.id)] = data.employee;
        return window.jQuery(select2TemplateEmployee(data));
      },

      templateSelection: (data) => {
        if (!data.id) return data.text || "";
        if (data.employee?.id) Wizard.employees.byId[String(data.employee.id)] = data.employee;
        return window.jQuery(select2TemplateSelection(data));
      },

      escapeMarkup: (m) => m,
    });

    $el.on("select2:open", () => {
      window.jQuery(".select2-container--open").css("z-index", "100002");
      window.jQuery(".select2-dropdown").css("z-index", "100001");
    });

    return true;
  }

  /* =========================================================================
   * Date/Time helpers + extraction from items
   * ========================================================================= */
  function toDatePart(v) {
    const s = String(v || "").trim();
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    return m ? `${m[1]}-${m[2]}-${m[3]}` : "";
  }

  function toTimePart(v) {
    const s = String(v || "").trim();
    const m = s.match(/(\d{2}):(\d{2})(?::\d{2})?/);
    return m ? `${m[1]}:${m[2]}` : "";
  }

  function partsToIso(date, time) {
    const d = toDatePart(date);
    if (!d) return null;
    const t = toTimePart(time) || "08:00";
    return `${d}T${t}:00`;
  }

  function extractItemDateTime(item) {
    const dateCandidates = [
      item?.planned_date, item?.date,
      item?.start_date, item?.due_date, item?.deadline,
      item?.planned_start_at, item?.start_at, item?.starts_at,
      item?.planned_datetime, item?.scheduled_at,
    ];

    const timeCandidates = [
      item?.planned_time, item?.time,
      item?.start_time, item?.due_time,
      item?.planned_start_at, item?.start_at, item?.starts_at,
      item?.planned_datetime, item?.scheduled_at,
    ];

    let date = "";
    for (const c of dateCandidates) { date = toDatePart(c); if (date) break; }

    let time = "";
    for (const c of timeCandidates) { time = toTimePart(c); if (time) break; }

    return { date, time };
  }

  function extractResponsibleIds(item) {
    const pmCandidate =
      Number(item?.pm_id || item?.project_manager_id || item?.manager_id || item?.responsible_employee_id || item?.employee_id || 0) || null;

    const crew = new Set();
    const addId = (x) => {
      const id = Number(x || 0);
      if (id) crew.add(id);
    };

    addId(item?.assigned_to_id);
    addId(item?.assignee_id);
    addId(item?.employee_id);

    const arrs = [item?.assignees, item?.employees, item?.crew, item?.team, item?.assigned_employees];
    for (const a of arrs) {
      if (Array.isArray(a)) {
        for (const v of a) {
          if (typeof v === "number" || typeof v === "string") addId(v);
          else addId(v?.id || v?.employee_id);
        }
      }
    }

    if (Array.isArray(item?.assignees?.data)) {
      for (const v of item.assignees.data) addId(v?.id || v?.employee_id);
    }

    if (crew.size === 0 && pmCandidate) crew.add(pmCandidate);

    return { pmCandidateId: pmCandidate, crewCandidateIds: Array.from(crew) };
  }

  function getEmployeeById(id) {
    return Wizard.employees.byId[String(id)] || null;
  }

  function tinyAvatar(empId) {
    if (!empId) return `<img class="w-6 h-6 rounded-full border border-slate-200" src="${escapeHtml(dataSvgAvatar("?"))}" alt="">`;
    const emp = getEmployeeById(empId);
    const img = emp ? employeeAvatarUrl(emp) : dataSvgAvatar(String(empId).slice(0, 2));
    return `<img class="w-6 h-6 rounded-full object-cover border border-slate-200" src="${escapeHtml(img)}" alt="">`;
  }

  function renderResponsibleStrip(item) {
    const { pmCandidateId, crewCandidateIds } = extractResponsibleIds(item);
    const dt = extractItemDateTime(item);

    const dateText = dt.date ? dt.date : "—";
    const timeText = dt.time ? dt.time : "";

    const pmEmp = pmCandidateId ? getEmployeeById(pmCandidateId) : null;
    const pmName = pmEmp ? employeeFullName(pmEmp) : (pmCandidateId ? `#${pmCandidateId}` : "—");

    const crewIds = crewCandidateIds.slice(0, 4);
    const crewMore = crewCandidateIds.length > 4 ? (crewCandidateIds.length - 4) : 0;

    return `
      <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-600">
        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-slate-100 border border-slate-200">
          <i class="fa-regular fa-calendar"></i>
          <span>${escapeHtml(dateText)}${timeText ? " · " + escapeHtml(timeText) : ""}</span>
        </span>

        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-slate-100 border border-slate-200">
          <i class="fa-solid fa-user-tie"></i>
          ${pmCandidateId ? tinyAvatar(pmCandidateId) : ""}
          <span class="font-semibold">PM:</span>
          <span class="truncate max-w-[180px]">${escapeHtml(pmName)}</span>
        </span>

        ${
          crewCandidateIds.length
            ? `<span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-slate-100 border border-slate-200">
                <i class="fa-solid fa-people-group"></i>
                <span class="font-semibold">Team:</span>
                <span class="inline-flex -space-x-1">
                  ${crewIds.map(id => tinyAvatar(id)).join("")}
                </span>
                ${crewMore ? `<span class="font-bold">+${crewMore}</span>` : ""}
              </span>`
            : ""
        }
      </div>
    `;
  }

  /* =========================================================================
   * Assignment + Plan Date/Time panels (synced across sections)
   * ========================================================================= */
  function assignmentPanelHtml(key) {
    return `
      <div class="wf-assign-panel bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4" data-wf-assign="1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Projektleiter</label>
            <select id="wizard-pm-select-${key}" class="w-full"></select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Team</label>
            <select id="wizard-crew-select-${key}" class="w-full" multiple></select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Geplant am (Datum)</label>
            <input id="wizard-date-${key}" type="date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Geplant um (Zeit)</label>
            <input id="wizard-time-${key}" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
          </div>
        </div>
      </div>
    `;
  }

  function ensureAssignmentUI(containerEl, key) {
    if (!containerEl) return { pm: null, crew: null, date: null, time: null };

    const legacyPm = containerEl.querySelector("#wizard-pm-select");
    const legacyCrew = containerEl.querySelector("#wizard-crew-select");
    const legacyDate = containerEl.querySelector("#wizard-date");

    if (legacyPm && legacyPm.tagName === "SELECT" && !legacyPm.id.includes("-")) {
      legacyPm.id = `wizard-pm-select-${key}`;
    }

    if (legacyCrew && legacyCrew.tagName !== "SELECT" && !legacyCrew.id.includes("-")) {
      const multi = document.createElement("select");
      multi.id = `wizard-crew-select-${key}`;
      multi.multiple = true;
      multi.className = "w-full";
      legacyCrew.replaceWith(multi);
    } else if (legacyCrew && legacyCrew.tagName === "SELECT" && !legacyCrew.id.includes("-")) {
      legacyCrew.id = `wizard-crew-select-${key}`;
      legacyCrew.multiple = true;
    }

    if (legacyDate && !legacyDate.id.includes("-")) {
      legacyDate.id = `wizard-date-${key}`;
    }

    if (!containerEl.querySelector('[data-wf-assign="1"]')) {
      const wrap = document.createElement("div");
      wrap.innerHTML = assignmentPanelHtml(key);
      const panel = wrap.firstElementChild;

      const first = containerEl.firstElementChild;
      if (first) containerEl.insertBefore(panel, first.nextSibling);
      else containerEl.appendChild(panel);
    }

    const pm = containerEl.querySelector(`#wizard-pm-select-${key}`);
    const crew = containerEl.querySelector(`#wizard-crew-select-${key}`);
    const date = containerEl.querySelector(`#wizard-date-${key}`);
    const time = containerEl.querySelector(`#wizard-time-${key}`);

    return { pm, crew, date, time };
  }

  function getWizardDropdownParent() {
    // Ensure dropdown is inside the wizard modal to avoid being behind overlay
    return $("plan-wizard-content") || $("plan-wizard-modal") || document.body;
  }

  function syncAssignStateToAll() {
    if (Wizard._syncingAssign) return;
    Wizard._syncingAssign = true;

    try {
      const pmId = Wizard.selected.pm_id ? String(Wizard.selected.pm_id) : "";
      const crewIds = Array.from(Wizard.selected.crew_ids).map(String);

      const forms = [
        { key: "project", el: $("wizard-form-project") },
        { key: "tickets", el: $("wizard-form-tickets") },
        { key: "appointments", el: $("wizard-form-appointments") },
        { key: "personal", el: $("wizard-form-personal") },
        { key: "custom", el: $("wizard-form-custom") },
      ];

      for (const f of forms) {
        const pm = f.el?.querySelector(`#wizard-pm-select-${f.key}`);
        const crew = f.el?.querySelector(`#wizard-crew-select-${f.key}`);

        if (pm && pmId) seedSingleSelectOption(pm, pmId);
        if (crew && crewIds.length) seedMultiSelectOptions(crew, crewIds);

        if (pm) {
          if (window.jQuery && window.jQuery(pm).data("select2")) window.jQuery(pm).val(pmId || null).trigger("change.select2");
          else pm.value = pmId;
        }

        if (crew) {
          if (window.jQuery && window.jQuery(crew).data("select2")) window.jQuery(crew).val(crewIds).trigger("change.select2");
          else Array.from(crew.options).forEach(o => (o.selected = crewIds.includes(String(o.value))));
        }
      }
    } finally {
      Wizard._syncingAssign = false;
    }
  }

  function syncPlanDTStateToAll() {
    if (Wizard._syncingPlanDT) return;
    Wizard._syncingPlanDT = true;

    try {
      const d = Wizard.selected.planned_date || "";
      const t = Wizard.selected.planned_time || "";

      const forms = [
        { key: "project", el: $("wizard-form-project") },
        { key: "tickets", el: $("wizard-form-tickets") },
        { key: "appointments", el: $("wizard-form-appointments") },
        { key: "personal", el: $("wizard-form-personal") },
        { key: "custom", el: $("wizard-form-custom") },
      ];

      for (const f of forms) {
        const date = f.el?.querySelector(`#wizard-date-${f.key}`);
        const time = f.el?.querySelector(`#wizard-time-${f.key}`);
        if (date) date.value = d;
        if (time) time.value = t;
      }

      Wizard.selected.planned_datetime = partsToIso(d, t);
    } finally {
      Wizard._syncingPlanDT = false;
    }
  }

  async function initAssignmentSelectsEverywhere() {
    const forms = [
      { key: "project", el: $("wizard-form-project") },
      { key: "tickets", el: $("wizard-form-tickets") },
      { key: "appointments", el: $("wizard-form-appointments") },
      { key: "personal", el: $("wizard-form-personal") },
      { key: "custom", el: $("wizard-form-custom") },
    ];

    const refs = [];
    for (const f of forms) {
      if (!f.el) continue;
      refs.push({ key: f.key, el: f.el, ...ensureAssignmentUI(f.el, f.key) });
    }

    if (!Wizard.employees.loadedOnce) {
      try { await fetchEmployees(""); } catch { /* ignore */ }
    }

    const ddParent = getWizardDropdownParent();

    for (const r of refs) {
      if (r.pm) {
        if (!Array.from(r.pm.options).some(o => o.value === "")) {
          const ph = document.createElement("option");
          ph.value = "";
          ph.textContent = "Wählen...";
          r.pm.appendChild(ph);
        }
      }

      // seed current global values for immediate display
      if (r.pm && Wizard.selected.pm_id) seedSingleSelectOption(r.pm, Wizard.selected.pm_id);
      if (r.crew && Wizard.selected.crew_ids.size) seedMultiSelectOptions(r.crew, Array.from(Wizard.selected.crew_ids));

      if (r.pm) initSelect2(r.pm, { multiple: false, placeholder: "Projektleiter wählen...", dropdownParent: ddParent });
      if (r.crew) initSelect2(r.crew, { multiple: true, placeholder: "Team wählen...", dropdownParent: ddParent });

      // PM change
      if (r.pm && !r.pm.dataset.boundPm) {
        r.pm.dataset.boundPm = "1";
        const onPmChange = () => {
          if (Wizard._syncingAssign) return;
          const v = String(r.pm.value || "");
          Wizard.selected.pm_id = v ? Number(v) : null;
          syncAssignStateToAll();
        };
        r.pm.addEventListener("change", onPmChange);
        if (window.jQuery && window.jQuery(r.pm).data("select2")) window.jQuery(r.pm).on("change", onPmChange);
      }

      // Crew change
      if (r.crew && !r.crew.dataset.boundCrew) {
        r.crew.dataset.boundCrew = "1";
        const onCrewChange = () => {
          if (Wizard._syncingAssign) return;
          const selected = (window.jQuery && window.jQuery(r.crew).data("select2"))
            ? (window.jQuery(r.crew).val() || [])
            : Array.from(r.crew.selectedOptions || []).map(o => o.value);

          Wizard.selected.crew_ids = new Set((selected || []).map(x => Number(x)).filter(Boolean));
          syncAssignStateToAll();
        };
        r.crew.addEventListener("change", onCrewChange);
        if (window.jQuery && window.jQuery(r.crew).data("select2")) window.jQuery(r.crew).on("change", onCrewChange);
      }

      // Date change
      if (r.date && !r.date.dataset.boundPlanDate) {
        r.date.dataset.boundPlanDate = "1";
        r.date.addEventListener("change", () => {
          if (Wizard._syncingPlanDT) return;
          Wizard.selected.planned_date = r.date.value || "";
          syncPlanDTStateToAll();
        });
      }

      // Time change
      if (r.time && !r.time.dataset.boundPlanTime) {
        r.time.dataset.boundPlanTime = "1";
        r.time.addEventListener("change", () => {
          if (Wizard._syncingPlanDT) return;
          Wizard.selected.planned_time = r.time.value || "";
          syncPlanDTStateToAll();
        });
      }
    }

    syncAssignStateToAll();
    syncPlanDTStateToAll();
  }

  /* =========================================================================
   * Role modal (auto-opens when selecting ticket-task/appointment/personal task)
   * ========================================================================= */
  function ensureRoleModal() {
    if ($("wf-role-modal")) return;

    const el = document.createElement("div");
    el.id = "wf-role-modal";
    el.className = "hidden fixed inset-0 z-[99999]";

    el.innerHTML = `
      <div class="absolute inset-0 bg-black/40"></div>

      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="wf-role-card" class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl overflow-hidden">
          <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="font-extrabold text-slate-900">Rollen & Planung übernehmen</div>
              <div id="wf-role-modal-sub" class="text-xs text-slate-500 mt-1 truncate"></div>
            </div>
            <button id="wf-role-close" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Projektleiter (PM)</label>
                <select id="wf-role-pm" class="w-full"></select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Team</label>
                <select id="wf-role-crew" class="w-full" multiple></select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Datum</label>
                <input id="wf-role-date" type="date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Zeit</label>
                <input id="wf-role-time" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
              </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
              <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                <input id="wf-role-apply-global" type="checkbox" class="w-4 h-4" checked>
                <span>Auch global setzen (für alle Bereiche)</span>
              </label>

              <div class="flex items-center gap-2">
                <button id="wf-role-cancel" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold">
                  Abbrechen
                </button>
                <button id="wf-role-save" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold">
                  Übernehmen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(el);

    $("wf-role-close").addEventListener("click", closeRoleModal);
    $("wf-role-cancel").addEventListener("click", closeRoleModal);

    $("wf-role-save").addEventListener("click", async () => {
      const key = Wizard._lastRoleContextKey;
      if (!key) { closeRoleModal(); return; }

      // ensure employee cache exists so we can seed names
      if (!Wizard.employees.loadedOnce) {
        try { await fetchEmployees(""); } catch { /* ignore */ }
      }

      const pmSel = $("wf-role-pm");
      const crewSel = $("wf-role-crew");
      const dateInp = $("wf-role-date");
      const timeInp = $("wf-role-time");
      const applyGlobal = $("wf-role-apply-global")?.checked !== false;

      const pm_id = pmSel?.value ? Number(pmSel.value) : null;

      const crew_ids = (window.jQuery && window.jQuery(crewSel).data("select2"))
        ? (window.jQuery(crewSel).val() || []).map(x => Number(x)).filter(Boolean)
        : Array.from(crewSel?.selectedOptions || []).map(o => Number(o.value)).filter(Boolean);

      const planned_date = dateInp?.value || "";
      const planned_time = timeInp?.value || "";

      // per-item meta
      Wizard.itemMeta[key] = Wizard.itemMeta[key] || {};
      Wizard.itemMeta[key].pm_id = pm_id;
      Wizard.itemMeta[key].crew_ids = crew_ids;
      Wizard.itemMeta[key].planned_date = planned_date;
      Wizard.itemMeta[key].planned_time = planned_time;
      Wizard.itemMeta[key].planned_datetime = partsToIso(planned_date, planned_time);

      // ✅ update global panels immediately (this is what you missed)
      if (applyGlobal) {
        Wizard.selected.pm_id = pm_id;
        Wizard.selected.crew_ids = new Set(crew_ids);
        Wizard.selected.planned_date = planned_date;
        Wizard.selected.planned_time = planned_time;

        // seed options everywhere so select2 can actually show selected values
        syncAssignStateToAll();
        syncPlanDTStateToAll();
      }

      closeRoleModal();
    });
  }

  async function openRoleModal({ key, title, suggestedPmId, suggestedCrewIds, suggestedDate, suggestedTime }) {
    ensureRoleModal();
    Wizard._roleModalOpen = true;
    Wizard._lastRoleContextKey = key;

    if (!Wizard.employees.loadedOnce) {
      try { await fetchEmployees(""); } catch { /* ignore */ }
    }

    const meta = Wizard.itemMeta[key] || {};

    const pm_id =
      Number(meta.pm_id || 0) ||
      Number(suggestedPmId || 0) ||
      Number(Wizard.selected.pm_id || 0) ||
      null;

    const crew_ids =
      Array.isArray(meta.crew_ids) ? meta.crew_ids :
      (Array.isArray(suggestedCrewIds) && suggestedCrewIds.length ? suggestedCrewIds :
        Array.from(Wizard.selected.crew_ids));

    const planned_date =
      String(meta.planned_date || "") ||
      String(suggestedDate || "") ||
      String(Wizard.selected.planned_date || "") ||
      "";

    const planned_time =
      String(meta.planned_time || "") ||
      String(suggestedTime || "") ||
      String(Wizard.selected.planned_time || "") ||
      "";

    $("wf-role-modal-sub").textContent = String(title || "").trim();

    const pmSel = $("wf-role-pm");
    const crewSel = $("wf-role-crew");
    const ddParent = $("wf-role-card") || $("wf-role-modal");

    // reset options
    pmSel.innerHTML = `<option value=""></option>`;
    crewSel.innerHTML = "";

    // seed for immediate display
    if (pm_id) seedSingleSelectOption(pmSel, pm_id);
    seedMultiSelectOptions(crewSel, crew_ids || []);

    // init select2 with dropdownParent INSIDE modal card
    initSelect2(pmSel, { multiple: false, placeholder: "Projektleiter wählen...", dropdownParent: ddParent });
    initSelect2(crewSel, { multiple: true, placeholder: "Team wählen...", dropdownParent: ddParent });

    if (window.jQuery && window.jQuery(pmSel).data("select2")) window.jQuery(pmSel).val(pm_id ? String(pm_id) : null).trigger("change.select2");
    else pmSel.value = pm_id ? String(pm_id) : "";

    const crewVals = (crew_ids || []).map(String);
    if (window.jQuery && window.jQuery(crewSel).data("select2")) window.jQuery(crewSel).val(crewVals).trigger("change.select2");
    else Array.from(crewSel.options).forEach(o => (o.selected = crewVals.includes(String(o.value))));

    $("wf-role-date").value = planned_date || "";
    $("wf-role-time").value = planned_time || "";

    $("wf-role-modal").classList.remove("hidden");
  }

  function closeRoleModal() {
    Wizard._roleModalOpen = false;
    const el = $("wf-role-modal");
    if (el) el.classList.add("hidden");
  }

  /* =========================================================================
   * When selecting an item -> take date/time + ask roles
   * ========================================================================= */
  async function onItemSelected({ source_type, source_id, title, item }) {
    const key = `${source_type}:${Number(source_id)}`;
    const dt = extractItemDateTime(item || {});
    const resp = extractResponsibleIds(item || {});

    // auto-fill global date/time from the selected item
    if (dt.date) Wizard.selected.planned_date = dt.date;
    if (dt.time) Wizard.selected.planned_time = dt.time;
    if (dt.date || dt.time) syncPlanDTStateToAll();

    await openRoleModal({
      key,
      title,
      suggestedPmId: resp.pmCandidateId,
      suggestedCrewIds: resp.crewCandidateIds,
      suggestedDate: dt.date,
      suggestedTime: dt.time,
    });
  }

  /* =========================================================================
   * Open/Close
   * ========================================================================= */
  window.openPlanWizard = async function openPlanWizard(passedCtx) {
    const resolved = resolveWizardContext(passedCtx);

    Wizard.ctx.customer_id    = resolved.customer_id;
    Wizard.ctx.project_id     = resolved.project_id;
    Wizard.ctx.product_id     = resolved.product_id;
    Wizard.ctx.alternative_id = resolved.alternative_id;
    Wizard.ctx.customer_name  = resolved.customer_name;

    if (!ensureWizardContextNote()) return;

    Wizard.open = true;

    const nameEl = $("wizard-customer-name");
    if (nameEl) nameEl.textContent = Wizard.ctx.customer_name;

    // reset
    Wizard.selected.phase_activity_ids.clear();
    Wizard.selected.ticket_task_ids.clear();
    Wizard.selected.appointment_ids.clear();
    Wizard.selected.personal_task_ids.clear();
    Wizard.selected.custom_steps = [];
    Wizard.selected.pm_id = null;
    Wizard.selected.crew_ids.clear();
    Wizard.selected.asset_qty = {};
    Wizard.selected.planned_date = "";
    Wizard.selected.planned_time = "";
    Wizard.selected.planned_datetime = null;
    Wizard.itemMeta = {};

    const modal = $("plan-wizard-modal");
    const content = $("plan-wizard-content");
    if (modal) modal.classList.remove("hidden");
    if (content) requestAnimationFrame(() => content.classList.remove("translate-x-full"));

    const checked = document.querySelector('input[name="plan-type"]:checked');
    Wizard.type = checked?.value || "project";

    await initAssignmentSelectsEverywhere();
    hydrateAssetsOnly();

    setLoading($("wizard-planned-list"), true, "Lade Phasen...");
    setLoading($("wizard-remaining-list"), true, "Lade Aufgaben...");
    setLoading($("wizard-tickets-list"), true, "Lade Tickets...");
    setLoading($("wizard-appointments-list"), true, "Lade Termine...");
    setLoading($("wizard-personal-list"), true, "Lade persönliche Aufgaben...");

    await preloadWizardData();
    window.toggleWizardType(Wizard.type);
  };

  window.closePlanWizard = function closePlanWizard() {
    Wizard.open = false;
    closeRoleModal();
    const modal = $("plan-wizard-modal");
    const content = $("plan-wizard-content");
    if (content) content.classList.add("translate-x-full");
    setTimeout(() => { if (modal) modal.classList.add("hidden"); }, 200);
  };

  function bindContextAutoRefresh() {
    const projectSel = $("project-selector");
    if (!projectSel || projectSel.dataset.wizardBound === "1") return;
    projectSel.dataset.wizardBound = "1";

    projectSel.addEventListener("change", async () => {
      if (!Wizard.open) return;

      const resolved = resolveWizardContext({});
      Wizard.ctx.customer_id    = resolved.customer_id;
      Wizard.ctx.project_id     = resolved.project_id;
      Wizard.ctx.product_id     = resolved.product_id;
      Wizard.ctx.alternative_id = resolved.alternative_id;
      Wizard.ctx.customer_name  = resolved.customer_name;

      const nameEl = $("wizard-customer-name");
      if (nameEl) nameEl.textContent = Wizard.ctx.customer_name;

      await preloadWizardData();
      window.toggleWizardType(Wizard.type);
    });
  }
  bindContextAutoRefresh();

  /* =========================================================================
   * Tabs
   * ========================================================================= */
  window.toggleWizardType = function toggleWizardType(type) {
    Wizard.type = type;

    show($("wizard-form-project"), type === "project");
    show($("wizard-form-tickets"), type === "tickets");
    show($("wizard-form-appointments"), type === "appointments");
    show($("wizard-form-personal"), type === "personal");
    show($("wizard-form-custom"), type === "custom");

    initAssignmentSelectsEverywhere().catch(() => {});

    if (type === "project") renderProjectChecklist();
    if (type === "tickets") renderTickets();
    if (type === "appointments") renderAppointments();
    if (type === "personal") renderPersonalTasks();
  };

  /* =========================================================================
   * Preload
   * ========================================================================= */
  async function preloadWizardData() {
    const ctx = Wizard.ctx;

    if (!ctx.customer_id || !ctx.project_id || !ctx.product_id) {
      Wizard.phasesResp = { ok: true, data: [] };
      Wizard.ticketsResp = { ok: true, data: [] };
      Wizard.apptsResp = { ok: true, unplanned_appointments: [] };
      Wizard.personalResp = { ok: true, data: [] };
      return;
    }

    try {
      Wizard.phasesResp = await apiGet("/phases", {
        customer_id: ctx.customer_id,
        alternative_id: ctx.alternative_id || "",
        product_id: ctx.product_id,
        project_id: ctx.project_id || "",
      });
    } catch (e) {
      console.error("PHASES ERROR:", e);
      Wizard.phasesResp = { ok: false, error: String(e?.message || e) };
    }

    try {
      Wizard.ticketsResp = await apiGet("/problems", {
        customer_id: ctx.customer_id,
        alternative_id: ctx.alternative_id || "",
        product_id: ctx.product_id || "",
        include_tasks: 1,
        limit: 200,
      });

      Wizard.ticketTasksByTicketId = {};
      if (Array.isArray(Wizard.ticketsResp?.data)) {
        for (const p of Wizard.ticketsResp.data) {
          const tid = Number(p?.id || 0);
          const tasks = p?.tasks?.data;
          if (tid && Array.isArray(tasks)) Wizard.ticketTasksByTicketId[tid] = tasks;
        }
      }
    } catch (e) {
      console.error("PROBLEMS ERROR:", e);
      Wizard.ticketsResp = { ok: false, error: String(e?.message || e), data: [] };
      Wizard.ticketTasksByTicketId = {};
    }

    try {
      Wizard.apptsResp = await apiGet("/appointments", {
        customer_id: ctx.customer_id,
        planned: 0,
        limit: 200,
      });
    } catch (e) {
      console.error("APPOINTMENTS ERROR:", e);
      Wizard.apptsResp = { ok: false, error: String(e?.message || e) };
    }

    try {
      Wizard.personalResp = await apiGet("/personal-tasks", {
        customer_id: ctx.customer_id,
        alternative_id: ctx.alternative_id || "",
        product_id: ctx.product_id || "",
        limit: 200,
      });
    } catch (e) {
      console.error("PERSONAL TASKS ERROR:", e);
      Wizard.personalResp = { ok: false, error: String(e?.message || e), data: [] };
    }
  }

  /* =========================================================================
   * Project Checklist
   * ========================================================================= */
  function renderProjectChecklist() {
    const plannedBox = $("wizard-planned-list");
    const remainingBox = $("wizard-remaining-list");
    const resp = Wizard.phasesResp;

    if (!resp || !resp.ok) {
      setError(plannedBox, "Phasen konnten nicht geladen werden.", "");
      setError(remainingBox, "Phasen konnten nicht geladen werden.", resp?.error || "");
      return;
    }

    const stages = Array.isArray(resp.data) ? resp.data : [];
    if (!stages.length) {
      plannedBox.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine geplanten Aufgaben.</div>`;
      remainingBox.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine Phasen gefunden.</div>`;
      return;
    }

    const plannedActs = [];
    for (const st of stages) {
      for (const ph of (st.phases || [])) {
        for (const a of (ph.activities || [])) {
          if (a?.is_planned) plannedActs.push({ st, ph, a });
        }
      }
    }

    plannedBox.innerHTML = plannedActs.length
      ? plannedActs.slice(0, 20).map(x => `
          <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200">
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
              <i class="fa-solid fa-check"></i>
            </div>
            <div class="flex-1">
              <div class="font-semibold text-slate-800 text-sm">${escapeHtml(x.a.title || "Aktivität")}</div>
              <div class="text-xs text-slate-400">
                ${escapeHtml(x.st.stage || "")}${x.ph.phase_name ? " · " + escapeHtml(x.ph.phase_name) : ""}
              </div>
            </div>
          </div>
        `).join("")
      : `<div class="text-xs text-slate-400 italic p-2">Keine geplanten Aufgaben.</div>`;

    remainingBox.innerHTML = stages.map((st, sIdx) => {
      const stName = st.stage || `Stage #${st.stage_id || sIdx + 1}`;
      const phases = Array.isArray(st.phases) ? st.phases : [];
      const counts = countStage(phases);

      return `
        <details class="rounded-2xl border border-slate-200 bg-white overflow-hidden" ${sIdx === 0 ? "open" : ""}>
          <summary class="cursor-pointer list-none p-4 flex items-center justify-between gap-3 bg-slate-50">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                <i class="fa-solid fa-layer-group"></i>
              </div>
              <div>
                <div class="font-bold text-slate-800">${escapeHtml(stName)}</div>
                <div class="text-xs text-slate-500">
                  ${counts.open} offen · ${counts.planned} geplant · ${counts.done} erledigt · ${counts.total} gesamt
                </div>
              </div>
            </div>
            <div class="text-slate-400"><i class="fa-solid fa-chevron-down"></i></div>
          </summary>

          <div class="p-4 space-y-3">
            ${phases.length ? phases.map((ph, pIdx) => {
              const phName = ph.phase_name || `Phase #${ph.phase_id || pIdx + 1}`;
              const acts = Array.isArray(ph.activities) ? ph.activities : [];
              const pc = countPhase(acts);

              return `
                <details class="rounded-xl border border-slate-200 bg-white" ${pIdx === 0 && sIdx === 0 ? "open" : ""}>
                  <summary class="cursor-pointer list-none px-4 py-3 flex items-center justify-between bg-white">
                    <div>
                      <div class="font-semibold text-slate-800">${escapeHtml(phName)}</div>
                      <div class="text-xs text-slate-500">${pc.open} offen · ${pc.planned} geplant · ${pc.done} erledigt · ${pc.total} gesamt</div>
                    </div>
                    <div class="text-slate-400"><i class="fa-solid fa-chevron-down"></i></div>
                  </summary>

                  <div class="px-4 pb-4 pt-2 space-y-2">
                    ${acts.length ? acts.map(a => renderActivityCard(a)).join("") : `
                      <div class="text-xs text-slate-400 italic p-2">Keine Aktivitäten.</div>
                    `}
                  </div>
                </details>
              `;
            }).join("") : `<div class="text-xs text-slate-400 italic p-2">Keine Phasen.</div>`}
          </div>
        </details>
      `;
    }).join("");

    remainingBox.querySelectorAll('input[type="checkbox"][data-activity-id]').forEach(cb => {
      cb.addEventListener("change", (e) => {
        const id = Number(e.target.getAttribute("data-activity-id"));
        if (!id) return;
        if (e.target.checked) Wizard.selected.phase_activity_ids.add(id);
        else Wizard.selected.phase_activity_ids.delete(id);
      });
    });

    fillAppointmentChecklistSelectFromRemaining();
  }

  function renderActivityCard(a) {
    const id = Number(a?.id || 0);
    const title = a?.title || "Aktivität";
    const desc = a?.description || a?.notes || "";
    const isDone = !!a?.is_done;
    const isPlanned = !!a?.is_planned;
    const checked = Wizard.selected.phase_activity_ids.has(id);

    const disabled = isDone || isPlanned;

    const badge = isDone
      ? `<span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold">DONE</span>`
      : isPlanned
        ? `<span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold">PLANNED</span>`
        : `<span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold">OPEN</span>`;

    return `
      <label class="block p-3 rounded-xl border ${disabled ? "bg-slate-50 border-slate-200 opacity-70" : "bg-white border-slate-200 hover:border-slate-400 cursor-pointer"}">
        <div class="flex items-start gap-3">
          <input
            type="checkbox"
            class="w-4 h-4 mt-1"
            data-activity-id="${id}"
            ${checked ? "checked" : ""}
            ${disabled ? "disabled" : ""}
          >
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <div class="font-semibold text-slate-800 text-sm truncate">${escapeHtml(title)}</div>
              ${badge}
            </div>
            ${desc ? `<div class="text-xs text-slate-500 mt-1 line-clamp-2">${escapeHtml(desc)}</div>` : ""}
          </div>
        </div>
      </label>
    `;
  }

  function countPhase(acts) {
    const total = acts.length;
    const done = acts.filter(x => !!x.is_done).length;
    const planned = acts.filter(x => !!x.is_planned).length;
    const open = acts.filter(x => !x.is_done && !x.is_planned).length;
    return { total, done, planned, open };
  }

  function countStage(phases) {
    let total = 0, done = 0, planned = 0, open = 0;
    for (const ph of phases) {
      const acts = Array.isArray(ph.activities) ? ph.activities : [];
      const c = countPhase(acts);
      total += c.total; done += c.done; planned += c.planned; open += c.open;
    }
    return { total, done, planned, open };
  }

  function fillAppointmentChecklistSelectFromRemaining() {
    const sel = $("appt-checklist-select");
    if (!sel) return;

    const resp = Wizard.phasesResp;
    const opts = [];
    const stages = Array.isArray(resp?.data) ? resp.data : [];

    for (const st of stages) {
      const stName = st.stage || "";
      for (const ph of (st.phases || [])) {
        const phName = ph.phase_name || "";
        for (const a of (ph.activities || [])) {
          if (a.is_done || a.is_planned) continue;
          opts.push({ id: Number(a.id), label: `${stName} — ${phName} — ${a.title || "Aktivität"}` });
        }
      }
    }

    sel.innerHTML = opts.length
      ? `<option value="">Bitte wählen...</option>` + opts.map(o => `<option value="${o.id}">${escapeHtml(o.label)}</option>`).join("")
      : `<option value="">Keine Checkliste verfügbar</option>`;
  }

  /* =========================================================================
   * Tickets
   * ========================================================================= */
  function renderTickets() {
    const box = $("wizard-tickets-list");
    const resp = Wizard.ticketsResp;
    if (!box) return;

    if (!resp || !resp.ok) {
      setError(box, "Tickets konnten nicht geladen werden.", resp?.error || "");
      return;
    }

    const tickets = Array.isArray(resp.data) ? resp.data : [];
    if (!tickets.length) {
      box.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine offenen Tickets.</div>`;
      return;
    }

    box.innerHTML = tickets.map(t => {
      const tid = Number(t.id || 0);
      const title = t.ticket_no ? `Ticket #${t.ticket_no}` : `Ticket #${tid}`;
      const prio = t.priority || "";
      const status = t.status || "";

      const tasks = Array.isArray(t?.tasks?.data) ? t.tasks.data : (Wizard.ticketTasksByTicketId[tid] || []);
      const openTasks = tasks.filter(x => !x.is_done);

      return `
        <div class="p-4 rounded-xl bg-white border border-slate-200">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="font-bold text-slate-800">${escapeHtml(title)}</div>
              <div class="text-xs text-slate-500 mt-1">
                ${prio ? `Prio: <span class="font-semibold">${escapeHtml(prio)}</span>` : ""}
                ${status ? ` · Status: <span class="font-semibold">${escapeHtml(status)}</span>` : ""}
              </div>
              ${t.texts?.problem ? `<div class="text-xs text-slate-500 mt-2 line-clamp-2">${escapeHtml(t.texts.problem)}</div>` : ""}
            </div>
            <div class="text-xs text-slate-400 whitespace-nowrap">${openTasks.length} offen</div>
          </div>

          <div class="mt-3 space-y-2">
            ${
              openTasks.length
                ? openTasks.map(tt => {
                    const id = Number(tt.id);
                    const checked = Wizard.selected.ticket_task_ids.has(id);
                    const labelTitle = tt.title || "Aufgabe";
                    return `
                      <label class="block p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:border-slate-400">
                        <div class="flex items-start gap-3">
                          <input type="checkbox" class="w-4 h-4 mt-1" data-ticket-task-id="${id}" ${checked ? "checked" : ""}>
                          <div class="flex-1 min-w-0">
                            <div class="text-sm font-extrabold text-slate-800 truncate">${escapeHtml(labelTitle)}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                              ${escapeHtml(tt.priority || "")}
                              ${tt.due_date ? ` · fällig ${escapeHtml(tt.due_date)}` : ""}
                            </div>
                            ${renderResponsibleStrip(tt)}
                          </div>
                        </div>
                      </label>
                    `;
                  }).join("")
                : `<div class="text-xs text-slate-400 italic">Keine offenen Ticket-Aufgaben.</div>`
            }
          </div>
        </div>
      `;
    }).join("");

    box.querySelectorAll('input[type="checkbox"][data-ticket-task-id]').forEach(cb => {
      cb.addEventListener("change", async (e) => {
        const id = Number(e.target.getAttribute("data-ticket-task-id"));
        if (!id) return;

        if (e.target.checked) {
          Wizard.selected.ticket_task_ids.add(id);
          const task = findTicketTaskById(id);
          await onItemSelected({
            source_type: "ticket_task",
            source_id: id,
            title: task?.title ? `Ticket-Aufgabe: ${task.title}` : `Ticket-Aufgabe #${id}`,
            item: task || {},
          });
        } else {
          Wizard.selected.ticket_task_ids.delete(id);
        }
      });
    });
  }

  function findTicketTaskById(id) {
    const tickets = Array.isArray(Wizard.ticketsResp?.data) ? Wizard.ticketsResp.data : [];
    for (const t of tickets) {
      const tasks = Array.isArray(t?.tasks?.data) ? t.tasks.data : (Wizard.ticketTasksByTicketId[Number(t?.id || 0)] || []);
      for (const tt of tasks) {
        if (Number(tt?.id || 0) === Number(id)) return tt;
      }
    }
    return null;
  }

  /* =========================================================================
   * Appointments
   * ========================================================================= */
  window.toggleApptResolveType = function toggleApptResolveType(mode) {
    show($("appt-resolve-link"), mode === "link");
    show($("appt-resolve-manual"), mode === "manual");
  };

  function renderAppointments() {
    const box = $("wizard-appointments-list");
    const resp = Wizard.apptsResp;
    if (!box) return;

    if (!resp || !resp.ok) {
      setError(box, "Termine konnten nicht geladen werden.", resp?.error || "");
      return;
    }

    const appts = Array.isArray(resp.unplanned_appointments) ? resp.unplanned_appointments : [];
    if (!appts.length) {
      box.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine offenen Termine.</div>`;
      return;
    }

    box.innerHTML = appts.map(a => {
      const id = Number(a.id);
      const name = a.name || `Termin #${id}`;
      const dt = (a.start_date ? a.start_date : "") + (a.start_time ? ` ${a.start_time}` : "");
      const addr = a.address?.full_address || a.address?.street || "";
      const checked = Wizard.selected.appointment_ids.has(id);

      return `
        <label class="block p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-slate-400">
          <div class="flex items-start gap-3">
            <input type="checkbox" class="w-4 h-4 mt-1" data-appt-id="${id}" ${checked ? "checked" : ""}>
            <div class="flex-1 min-w-0">
              <div class="font-extrabold text-slate-800 text-sm truncate">${escapeHtml(name)}</div>
              <div class="text-xs text-slate-500 mt-1">
                ${escapeHtml(dt)}${addr ? ` · ${escapeHtml(addr)}` : ""}
              </div>
              ${renderResponsibleStrip(a)}
            </div>
          </div>
        </label>
      `;
    }).join("");

    box.querySelectorAll('input[type="checkbox"][data-appt-id]').forEach(cb => {
      cb.addEventListener("change", async (e) => {
        const id = Number(e.target.getAttribute("data-appt-id"));
        if (!id) return;

        if (e.target.checked) {
          Wizard.selected.appointment_ids.add(id);
          show($("wizard-appointment-resolution"), Wizard.selected.appointment_ids.size > 0);

          const appt = appts.find(x => Number(x?.id || 0) === id) || {};
          await onItemSelected({
            source_type: "appointment",
            source_id: id,
            title: appt?.name ? `Termin: ${appt.name}` : `Termin #${id}`,
            item: appt,
          });
        } else {
          Wizard.selected.appointment_ids.delete(id);
          show($("wizard-appointment-resolution"), Wizard.selected.appointment_ids.size > 0);
        }
      });
    });

    fillAppointmentChecklistSelectFromRemaining();
  }

  /* =========================================================================
   * Personal Tasks
   * ========================================================================= */
  function renderPersonalTasks() {
    const box = $("wizard-personal-list");
    const resp = Wizard.personalResp;
    if (!box) return;

    if (!resp || !resp.ok) {
      setError(box, "Aufgaben konnten nicht geladen werden.", resp?.error || "");
      return;
    }

    const items = Array.isArray(resp.data) ? resp.data : [];
    if (!items.length) {
      box.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine offenen persönlichen Aufgaben.</div>`;
      return;
    }

    box.innerHTML = items.map(t => {
      const id = Number(t.id);
      const title = t.task_title || `Aufgabe #${id}`;
      const due = t.due_date ? ` · fällig ${t.due_date}` : "";
      const st = t.task_status ? ` · ${t.task_status}` : "";
      const checked = Wizard.selected.personal_task_ids.has(id);

      return `
        <label class="block p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-slate-400">
          <div class="flex items-start gap-3">
            <input type="checkbox" class="w-4 h-4 mt-1" data-personal-id="${id}" ${checked ? "checked" : ""}>
            <div class="flex-1 min-w-0">
              <div class="font-extrabold text-slate-800 text-sm truncate">${escapeHtml(title)}</div>
              <div class="text-xs text-slate-500 mt-1">${escapeHtml(st + due)}</div>
              ${renderResponsibleStrip(t)}
            </div>
          </div>
        </label>
      `;
    }).join("");

    box.querySelectorAll('input[type="checkbox"][data-personal-id]').forEach(cb => {
      cb.addEventListener("change", async (e) => {
        const id = Number(e.target.getAttribute("data-personal-id"));
        if (!id) return;

        if (e.target.checked) {
          Wizard.selected.personal_task_ids.add(id);

          const task = items.find(x => Number(x?.id || 0) === id) || {};
          await onItemSelected({
            source_type: "personal_task",
            source_id: id,
            title: task?.task_title ? `Persönliche Aufgabe: ${task.task_title}` : `Persönliche Aufgabe #${id}`,
            item: task,
          });
        } else {
          Wizard.selected.personal_task_ids.delete(id);
        }
      });
    });
  }

  /* =========================================================================
   * Assets only
   * ========================================================================= */
  function hydrateAssetsOnly() {
    const assets = (window.__WF && Array.isArray(window.__WF.assetInventory)) ? window.__WF.assetInventory : [];
    const targets = ["wizard-project-assets", "wizard-ticket-assets", "wizard-appt-assets", "wizard-custom-assets"]
      .map(id => $(id))
      .filter(Boolean);

    for (const target of targets) {
      target.innerHTML = assets.map(a => {
        const id = Number(a.id);
        const label = a.name || a.title || a.asset_name || `Asset #${id}`;
        const qty = Wizard.selected.asset_qty[id] || 0;
        return `
          <div class="flex items-center justify-between gap-2 p-2 rounded-lg border border-slate-200 bg-white">
            <div class="text-xs font-semibold text-slate-700 truncate">${escapeHtml(label)}</div>
            <input type="number" min="0" class="w-16 p-1 text-xs border rounded" data-asset-id="${id}" value="${qty}">
          </div>
        `;
      }).join("");

      target.querySelectorAll('input[type="number"][data-asset-id]').forEach(inp => {
        inp.addEventListener("change", (ev) => {
          const id = Number(ev.target.getAttribute("data-asset-id"));
          const v = Math.max(0, Number(ev.target.value || 0));
          if (!id) return;
          if (v === 0) delete Wizard.selected.asset_qty[id];
          else Wizard.selected.asset_qty[id] = v;
        });
      });
    }
  }

  /* =========================================================================
   * Custom steps
   * ========================================================================= */
  window.addWizardStep = function addWizardStep() {
    const inp = $("wizard-custom-step-input");
    const val = (inp?.value || "").trim();
    if (!val) return;
    Wizard.selected.custom_steps.push(val);
    inp.value = "";
    renderCustomSteps();
  };

  function renderCustomSteps() {
    const list = $("wizard-custom-steps-list");
    if (!list) return;

    if (!Wizard.selected.custom_steps.length) {
      list.innerHTML = `<div class="text-xs text-slate-400 italic p-2">Keine Schritte.</div>`;
      return;
    }

    list.innerHTML = Wizard.selected.custom_steps.map((s, idx) => `
      <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
        <div class="text-sm text-slate-700">${escapeHtml(s)}</div>
        <button class="text-xs text-red-600 font-bold" data-step-del="${idx}">Entfernen</button>
      </div>
    `).join("");

    list.querySelectorAll('button[data-step-del]').forEach(btn => {
      btn.onclick = () => {
        const i = Number(btn.getAttribute("data-step-del"));
        Wizard.selected.custom_steps.splice(i, 1);
        renderCustomSteps();
      };
    });
  }

  /* =========================================================================
   * Save Wizard
   * ========================================================================= */
  window.savePlanWizard = async function savePlanWizard() {
    const title = ($("wizard-plan-title-input")?.value || "").trim();
    const ctx = Wizard.ctx;

    if (!ensureWizardContextNote()) return;

    const items = [];

    for (const id of Wizard.selected.phase_activity_ids) {
      const key = `phase_activity:${id}`;
      items.push({
        source_type: "phase_activity",
        source_id: id,
        meta: Wizard.itemMeta[key] || undefined,
      });
    }

    for (const id of Wizard.selected.ticket_task_ids) {
      const key = `ticket_task:${id}`;
      items.push({
        source_type: "ticket_task",
        source_id: id,
        meta: Wizard.itemMeta[key] || undefined,
      });
    }

    for (const id of Wizard.selected.appointment_ids) {
      const resolveType = document.querySelector('input[name="appt-resolve-type"]:checked')?.value || "link";
      const linkedActivityId = resolveType === "link" ? Number($("appt-checklist-select")?.value || 0) || null : null;
      const manualTitle = resolveType === "manual" ? ($("appt-manual-title")?.value || "").trim() : "";
      const manualCategory = resolveType === "manual" ? ($("appt-manual-category")?.value || "General") : null;

      const key = `appointment:${id}`;
      const baseMeta = Wizard.itemMeta[key] || {};
      const apptMeta = {
        ...baseMeta,
        resolve_type: resolveType,
        linked_activity_id: linkedActivityId,
        manual_title: manualTitle || null,
        manual_category: manualCategory,
      };

      items.push({
        source_type: "appointment",
        source_id: id,
        meta: apptMeta,
      });
    }

    for (const id of Wizard.selected.personal_task_ids) {
      const key = `personal_task:${id}`;
      items.push({
        source_type: "personal_task",
        source_id: id,
        meta: Wizard.itemMeta[key] || undefined,
      });
    }

    const customTitle = ($("wizard-custom-title")?.value || "").trim();
    const customDesc = ($("wizard-custom-desc")?.value || "").trim();
    if (customTitle) {
      const dur = Number($("wizard-custom-duration")?.value || 1);
      items.push({
        source_type: "custom",
        source_id: null,
        title: customTitle,
        description: customDesc,
        duration_minutes: Math.max(1, dur) * 60,
        category: ($("wizard-custom-category")?.value || "General"),
        meta: {
          steps: Wizard.selected.custom_steps || [],
          pm_id: Wizard.selected.pm_id,
          crew_ids: Array.from(Wizard.selected.crew_ids),
          planned_date: Wizard.selected.planned_date,
          planned_time: Wizard.selected.planned_time,
          planned_datetime: Wizard.selected.planned_datetime,
        }
      });
    }

    if (!items.length) {
      alert("Bitte mindestens eine Aufgabe auswählen.");
      return;
    }

    const payload = {
      customer_id: ctx.customer_id,
      project_id: ctx.project_id,
      product_id: ctx.product_id,
      alternative_id: ctx.alternative_id,
      title: title || null,
      stage: "montage",
      status: "draft",

      planned_datetime: Wizard.selected.planned_datetime,

      assign: {
        pm_id: Wizard.selected.pm_id,
        crew_ids: Array.from(Wizard.selected.crew_ids),
        assets: Wizard.selected.asset_qty,
      },

      items
    };

    try {
      const resp = await apiPost("/plans/store-wizard", payload);
      if (!resp?.ok) throw new Error(resp?.message || "Save failed");
      window.closePlanWizard();

      if (typeof window.onPlannerPlansChanged === "function") {
        window.onPlannerPlansChanged(resp);
      }
    } catch (e) {
      console.error(e);
      alert(e?.message || "Fehler beim Speichern");
    }
  };

  /* =========================================================================
   * Utils
   * ========================================================================= */
  function escapeHtml(s) {
    return String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }
})();
</script>

 <script>
/**
 * WF Planner — SINGLE SOURCE OF TRUTH ✅
 * Merge of BOTH scripts into ONE IIFE so Board + List + Gantt always stay in sync.
 *
 * Includes:
 * - Load plan: GET /planner/plans/{plan}/json
 * - Managers (leader columns) + Crew modal (add/remove managers)
 * - Persist extra managers: POST /planner/plans/{planId}/managers  { manager_ids: [...] }
 * - Backlog ↔ Board drag/drop (SortableJS)
 * - List view grouped by manager (collapsible)
 * - Gantt view rows per manager (bars)
 *
 * IMPORTANT:
 * - Keep ONLY this script on the page (remove the other parallel WF scripts), otherwise state will be overwritten.
 * - Required DOM ids used here:
 *   plan-selector, task-search, checklist-source, task-count,
 *   view-board, view-list, view-gantt,
 *   btn-view-board, btn-view-list, btn-view-gantt,
 *   list-body,
 *   gantt-body, gantt-tasks-container, time-scale,
 *   gantt-lines, lines-container,
 *   active-crew-avatars,
 *   crew-modal, crew-list-container
 */

(() => {
  "use strict";

  // Prevent double init if accidentally included twice
  window.__WF = window.__WF || {};
  const WFRT = window.__WF;
  if (WFRT.__planRendererInitialized) return;
  WFRT.__planRendererInitialized = true;

  // ------------------------------------------------------------
  // Runtime
  // ------------------------------------------------------------
  WFRT.$ = WFRT.$ || ((id) => document.getElementById(id));
  const $ = WFRT.$;

  WFRT.START_HOUR = WFRT.START_HOUR ?? 8;
  WFRT.PIXELS_PER_HOUR = WFRT.PIXELS_PER_HOUR ?? 100;

  WFRT.planState = WFRT.planState || {
    activePlanId: null,
    payload: null,

    items: [],
    managers: [],
    managerById: {},
    team: [],
    project_manager: null,

    // for crew modal selection
    employees_active: [],
    extraManagerIds: [],

    activeView: "board",
    _sortables: [],
    collapsedManagers: {},

    ganttStartMin: null,
    ganttEndMin: null,
  };

  // ------------------------------------------------------------
  // Helpers
  // ------------------------------------------------------------
  const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const safeText = (v) => (v === null || v === undefined) ? "" : String(v);
  const escapeHtml = (s) =>
    safeText(s).replace(/[&<>"']/g, (m) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
    }[m]));

  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

  const wfEmpId = (e) => {
    const id = e?.employee_id ?? e?.id ?? null;
    return id ? parseInt(id, 10) : null;
  };

  const wfEmpLabel = (e) =>
    e?.full_name ||
    [e?.title, e?.name, e?.lastname].filter(Boolean).join(" ") ||
    (wfEmpId(e) ? `#${wfEmpId(e)}` : "—");

  const empPhoto = (e) => e?.photo_url ? String(e.photo_url) : "";

  const uniqBy = (arr, getKey) => {
    const m = new Map();
    (arr || []).forEach((x) => {
      const k = getKey(x);
      if (k !== null && k !== undefined) m.set(k, x);
    });
    return Array.from(m.values());
  };

  const asIntArray = (arr) =>
    Array.isArray(arr)
      ? arr.map((x) => parseInt(x, 10)).filter((n) => Number.isFinite(n) && n > 0)
      : [];

  const parseHHMM = (s) => {
    if (!s) return null;
    const m = String(s).trim().match(/^(\d{1,2}):(\d{2})$/);
    if (!m) return null;
    const hh = Math.min(23, Math.max(0, parseInt(m[1], 10)));
    const mm = Math.min(59, Math.max(0, parseInt(m[2], 10)));
    return hh * 60 + mm;
  };

  const formatMinToHHMM = (min) => {
    if (min === null || min === undefined) return "";
    const hh = Math.floor(min / 60);
    const mm = min % 60;
    return String(hh).padStart(2, "0") + ":" + String(mm).padStart(2, "0");
  };

  const durationToMinutes = (duration) => {
    if (!duration) return 0;
    const s = String(duration).trim();
    const m = s.match(/^(\d+):(\d{2})(?::\d{2})?$/);
    if (!m) return 0;
    return (parseInt(m[1], 10) * 60) + parseInt(m[2], 10);
  };

  // ------------------------------------------------------------
  // API helpers
  // ------------------------------------------------------------
  async function apiPostJson(url, body) {
    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        ...(csrf() ? { "X-CSRF-TOKEN": csrf() } : {}),
      },
      credentials: "same-origin",
      body: JSON.stringify(body || {}),
    });

    let json = null;
    try { json = await res.json(); } catch (_) {}

    if (!res.ok || !json || json.ok !== true) {
      throw new Error(json?.message || `HTTP ${res.status}`);
    }
    return json;
  }

  async function fetchPlanJson(planId) {
    const url = `/planner/plans/${encodeURIComponent(planId)}/json`;
    const res = await fetch(url, {
      method: "GET",
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        ...(csrf() ? { "X-CSRF-TOKEN": csrf() } : {}),
      },
      credentials: "same-origin",
    });

    let json = null;
    try { json = await res.json(); } catch (_) {}

    if (!res.ok || !json || json.ok !== true) {
      throw new Error(json?.message || `Failed to load plan #${planId}`);
    }
    return json.data;
  }

  // ------------------------------------------------------------
  // Normalize payload items
  // ------------------------------------------------------------
  function normalizeItem(it) {
    const id = Number(it?.id || 0);

    const title = safeText(it?.title || it?.details?.title || it?.details?.name || "Aufgabe");
    const category = safeText(it?.category || it?.details?.type || "");
    const desc = safeText(it?.description || it?.details?.description || "");

    const status = safeText(it?.status || "open").toLowerCase();
    const plannedDate = safeText(it?.planned_date || it?.details?.planned_date || "");
    const plannedTime = safeText(it?.planned_time || it?.details?.planned_time || it?.details?.start_time || "");

    let dur = Number(it?.duration_minutes ?? 0);
    if (!dur || isNaN(dur)) dur = 0;
    if (!dur && it?.details?.duration) dur = durationToMinutes(it.details.duration);
    if (!dur && it?.duration) {
      const n = Number(it.duration);
      if (!isNaN(n) && n > 0) dur = Math.round(n * 60);
      if (!dur) dur = durationToMinutes(it.duration);
    }
    if (!dur) dur = 60;

    const lead = it?.lead || it?.project_manager || null;
    const leadId = wfEmpId(lead);

    const members = Array.isArray(it?.members) ? it.members : (Array.isArray(it?.assignees) ? it.assignees : []);
    const address = safeText(it?.details?.full_address || it?.details?.address || it?.address || "");

    let startMin = parseHHMM(plannedTime);
    if (startMin === null) startMin = WFRT.START_HOUR * 60;

    const travelTime = safeText(it?.travel_time || it?.details?.travel_time || "");
    const arrivalTime = safeText(it?.arrival_time || it?.details?.arrival_time || "");
    const origin = safeText(it?.origin || it?.details?.origin || "");

    return {
      id,
      raw: it,

      title,
      category,
      description: desc,
      status,

      planned_date: plannedDate,
      planned_time: plannedTime,

      duration_minutes: dur,
      startMin,
      endMin: startMin + dur,

      lead,
      leadId,
      members,
      address,

      travelTime,
      arrivalTime,
      origin,

      source_type: safeText(it?.source_type || it?.details?.source_type || ""),
      source_id: it?.source_id ?? null,
      details: it?.details || null,
    };
  }

  // ------------------------------------------------------------
  // Managers + grouping
  // ------------------------------------------------------------
  function buildManagers(payload, items) {
    const pm = payload?.project_manager || null;
    const team = Array.isArray(payload?.team) ? payload.team : [];

    // Prefer backend explicit managers array (leader columns), always include PM
    let managers = Array.isArray(payload?.managers) ? payload.managers : [];

    // Ensure PM always present first
    managers = uniqBy([pm, ...managers].filter(Boolean), (e) => wfEmpId(e));

    // Defensive: ensure any item lead exists as column
    (items || []).forEach((it) => {
      if (it.lead && it.leadId) {
        if (!managers.some(m => wfEmpId(m) === it.leadId)) managers.push(it.lead);
      }
    });

    // Fallback: if still empty but PM exists
    if (!managers.length && pm) managers = [pm];

    // Sort: PM first then name
    managers.sort((a, b) => {
      const aIsPm = wfEmpId(a) && wfEmpId(pm) && wfEmpId(a) === wfEmpId(pm);
      const bIsPm = wfEmpId(b) && wfEmpId(pm) && wfEmpId(b) === wfEmpId(pm);
      if (aIsPm && !bIsPm) return -1;
      if (!aIsPm && bIsPm) return 1;
      return wfEmpLabel(a).localeCompare(wfEmpLabel(b));
    });

    const byId = {};
    managers.forEach((m) => {
      const id = wfEmpId(m);
      if (id) byId[id] = m;
    });

    return { managers, managerById: byId, pm, team };
  }

  function groupItemsByLead(items) {
    const map = {};
    (items || []).forEach((it) => {
      const inMgr = it.leadId && WFRT.planState.managerById[it.leadId];
      const key = inMgr ? it.leadId : 0; // 0 => backlog/unassigned
      map[key] = map[key] || [];
      map[key].push(it);
    });

    Object.keys(map).forEach((k) => {
      map[k].sort((a, b) => {
        const ta = a.startMin ?? 0;
        const tb = b.startMin ?? 0;
        if (ta !== tb) return ta - tb;
        return (a.id ?? 0) - (b.id ?? 0);
      });
    });

    return map;
  }

  // ------------------------------------------------------------
  // Avatars
  // ------------------------------------------------------------
  function avatarHtml(e, cls = "w-8 h-8") {
    const name = escapeHtml(wfEmpLabel(e));
    const img = empPhoto(e);
    if (img) {
      return `<img src="${escapeHtml(img)}" alt="${name}" class="${cls} rounded-full object-cover border-2 border-white" />`;
    }
    const letter = escapeHtml((safeText(e?.name || e?.full_name || "?").trim().slice(0, 1) || "?").toUpperCase());
    return `<div class="${cls} rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] font-extrabold text-slate-600">${letter}</div>`;
  }

  function avatarStackHtml(people, max = 4) {
    const arr = (people || []).filter(Boolean);
    const shown = arr.slice(0, max);
    const rest = arr.length - shown.length;

    const imgs = shown.map(p => `<div class="-ml-2 first:ml-0">${avatarHtml(p, "w-6 h-6")}</div>`).join("");
    const more = rest > 0
      ? `<div class="-ml-2 w-6 h-6 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-extrabold text-slate-600">+${rest}</div>`
      : "";

    return `<div class="flex items-center">${imgs}${more}</div>`;
  }

  // ------------------------------------------------------------
  // Managers selection (Crew modal) + persistence
  // ------------------------------------------------------------
  function pmId() {
    return wfEmpId(WFRT.planState.project_manager) || null;
  }

  function currentExtraManagerIds() {
    return asIntArray(WFRT.planState.extraManagerIds);
  }

  function findActiveEmployee(empId) {
    const id = parseInt(empId, 10);
    return (WFRT.planState.employees_active || []).find(e => wfEmpId(e) === id) || null;
  }

  function addManagerColumn(emp) {
    const id = wfEmpId(emp);
    if (!id) return;
    if (WFRT.planState.managerById[id]) return;

    WFRT.planState.managers.push(emp);
    WFRT.planState.managerById[id] = emp;

    // keep sort: PM first
    WFRT.planState.managers.sort((a, b) => {
      const aIsPm = pmId() && wfEmpId(a) === pmId();
      const bIsPm = pmId() && wfEmpId(b) === pmId();
      if (aIsPm && !bIsPm) return -1;
      if (!aIsPm && bIsPm) return 1;
      return wfEmpLabel(a).localeCompare(wfEmpLabel(b));
    });
  }

  function removeManagerColumn(empId) {
    const id = parseInt(empId, 10);
    if (!id) return;

    // never remove PM
    if (pmId() && id === pmId()) return;

    delete WFRT.planState.managerById[id];
    WFRT.planState.managers = (WFRT.planState.managers || []).filter(m => wfEmpId(m) !== id);

    // If items still point to this manager, move them to backlog
    (WFRT.planState.items || []).forEach((it) => {
      if (it.leadId && parseInt(it.leadId, 10) === id) setItemLead(it.id, null);
    });
  }

  function itemsForManager(empId) {
    const id = parseInt(empId, 10);
    return (WFRT.planState.items || []).filter(it => it.leadId && parseInt(it.leadId, 10) === id);
  }

  let _saveManagersTimer = null;
  function saveManagersDebounced() {
    clearTimeout(_saveManagersTimer);
    _saveManagersTimer = setTimeout(saveManagersNow, 300);
  }

  async function saveManagersNow() {
    const planId = WFRT.planState.activePlanId;
    if (!planId) return;

    const p = pmId();
    const extraIds = currentExtraManagerIds().filter(id => !(p && id === p));

    try {
      await apiPostJson(`/planner/plans/${encodeURIComponent(planId)}/managers`, {
        manager_ids: extraIds
      });
    } catch (e) {
      console.error("[WF] managers save failed:", e);
    }
  }

  function renderManagersWidget() {
    const box = $("active-crew-avatars");
    if (!box) return;

    const arr = uniqBy(
      [WFRT.planState.project_manager, ...(WFRT.planState.managers || [])].filter(Boolean),
      (e) => wfEmpId(e)
    );

    const max = 6;
    const shown = arr.slice(0, max);

    box.innerHTML = shown
      .map(e => `<div class="-ml-2 first:ml-0" title="${escapeHtml(wfEmpLabel(e))}">${avatarHtml(e, "w-8 h-8")}</div>`)
      .join("");

    if (arr.length > max) {
      box.insertAdjacentHTML("beforeend",
        `<div class="-ml-2 w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[11px] font-extrabold text-slate-600" title="+${arr.length - max}">
          +${arr.length - max}
         </div>`
      );
    }
  }

  function renderManagerModalList(rows, q = "") {
    const container = $("crew-list-container");
    if (!container) return;

    const p = pmId();
    const selectedExtras = new Set(currentExtraManagerIds().map(String));

    const filtered = (rows || []).filter(e => {
      if (!q) return true;
      const hay = (wfEmpLabel(e) + " " + safeText(e?.email)).toLowerCase();
      return hay.includes(q.toLowerCase());
    });

    container.innerHTML = `
      <div class="space-y-3">
        <div class="flex items-center gap-2">
          <input id="crew-search" type="text"
            value="${escapeHtml(q)}"
            placeholder="Manager suchen…"
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-200">
          <button id="crew-clear" type="button"
            class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold hover:bg-slate-50">Clear</button>
        </div>

        <div class="text-xs text-slate-500 font-semibold">
          ${filtered.length} Mitarbeiter gefunden
        </div>

        <div class="space-y-2">
          ${filtered.map((e) => {
            const id = String(wfEmpId(e) || "");
            const name = wfEmpLabel(e);
            const img = empPhoto(e);

            const isPm = p && String(p) === id;
            const checked = isPm || selectedExtras.has(id);
            const disabled = isPm ? "disabled" : "";

            const avatar = img
              ? `<img src="${escapeHtml(img)}" class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="">`
              : avatarHtml(e, "w-9 h-9");

            return `
              <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" class="w-4 h-4" data-manager-id="${escapeHtml(id)}"
                  ${checked ? "checked" : ""} ${disabled}>
                ${avatar}
                <div class="min-w-0">
                  <div class="font-bold text-sm text-slate-800 truncate">
                    ${escapeHtml(name)}
                    ${isPm ? `<span class="ml-2 text-[10px] font-extrabold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md">PM</span>` : ``}
                  </div>
                  <div class="text-xs text-slate-500 truncate">${escapeHtml(e?.email || "")}</div>
                </div>
              </label>
            `;
          }).join("")}
        </div>
      </div>
    `;

    const search = $("crew-search");
    const clear = $("crew-clear");

    let t = null;
    if (search) {
      search.addEventListener("input", () => {
        clearTimeout(t);
        t = setTimeout(() => renderManagerModalList(rows, search.value || ""), 150);
      });
      search.focus();
    }
    if (clear) clear.addEventListener("click", () => renderManagerModalList(rows, ""));

    container.querySelectorAll('input[type="checkbox"][data-manager-id]').forEach((cb) => {
      cb.addEventListener("change", () => {
        const id = parseInt(cb.getAttribute("data-manager-id") || "0", 10);
        if (!id) return;

        // PM checkbox is disabled; only handle extras
        if (cb.checked) {
          const set = new Set(currentExtraManagerIds());
          set.add(id);
          WFRT.planState.extraManagerIds = Array.from(set);

          const emp = findActiveEmployee(id) || WFRT.planState.managerById[id] || { id, employee_id: id, full_name: `#${id}` };
          addManagerColumn(emp);

          renderAll();
          saveManagersDebounced();
          return;
        }

        // Uncheck: confirm + remove
        const emp = findActiveEmployee(id) || WFRT.planState.managerById[id] || { id, employee_id: id, full_name: `#${id}` };
        const name = wfEmpLabel(emp);

        const assigned = itemsForManager(id);
        const msg = assigned.length
          ? `Manager "${name}" entfernen?\n\n${assigned.length} Jobs sind diesem Manager zugeordnet.\nDiese Jobs werden in den Backlog (unassigned) verschoben.`
          : `Manager "${name}" entfernen?`;

        const ok = window.confirm(msg);
        if (!ok) {
          cb.checked = true;
          return;
        }

        removeManagerColumn(id);

        WFRT.planState.extraManagerIds = currentExtraManagerIds().filter(x => x !== id);

        renderAll();
        saveManagersDebounced();
      });
    });
  }

  window.openCrewModal = function openCrewModal() {
    const modal = $("crew-modal");
    if (!modal) return;
    modal.classList.remove("hidden");

    const rows = Array.isArray(WFRT.planState.employees_active) ? WFRT.planState.employees_active : [];
    renderManagerModalList(rows, "");
  };

  window.closeCrewModal = function closeCrewModal() {
    const modal = $("crew-modal");
    if (!modal) return;
    modal.classList.add("hidden");
  };

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const modal = $("crew-modal");
      if (modal && !modal.classList.contains("hidden")) window.closeCrewModal();
    }
  });

  // ------------------------------------------------------------
  // Drag & drop (Backlog <-> Board columns)
  // ------------------------------------------------------------
  function destroySortables() {
    (WFRT.planState._sortables || []).forEach((s) => {
      try { s.destroy(); } catch (_) {}
    });
    WFRT.planState._sortables = [];
  }

  function setItemLead(itemId, newLeadId) {
    const items = WFRT.planState.items || [];
    const it = items.find(x => String(x.id) === String(itemId));
    if (!it) return;

    const mgr = newLeadId ? (WFRT.planState.managerById[newLeadId] || null) : null;

    it.lead = mgr;
    it.leadId = mgr ? wfEmpId(mgr) : null;

    if (!mgr) it.status = "open";
    else if (it.status === "open") it.status = "scheduled";

    if (it.raw) {
      it.raw.lead = mgr;
      it.raw.status = it.status;
      if (it.raw.lead) {
        it.raw.lead.employee_id = it.raw.lead.employee_id ?? it.raw.lead.id ?? it.leadId;
        it.raw.lead.id = it.raw.lead.id ?? it.raw.lead.employee_id;
      }
    }
  }

  function initDnD() {
    destroySortables();
    if (!window.Sortable) return;

    const backlog = $("checklist-source");
    if (backlog) {
      const s = window.Sortable.create(backlog, {
        group: "wf-plan-items",
        animation: 150,
        ghostClass: "sortable-ghost",
        draggable: "[data-item-id]",
        onAdd: (evt) => {
          const itemId = evt.item?.getAttribute("data-item-id");
          if (!itemId) return;
          setItemLead(itemId, null);
          renderAll();
        },
      });
      WFRT.planState._sortables.push(s);
    }

    document.querySelectorAll("[data-wf-manager-list]").forEach((el) => {
      const s = window.Sortable.create(el, {
        group: "wf-plan-items",
        animation: 150,
        ghostClass: "sortable-ghost",
        draggable: "[data-item-id]",
        handle: "[data-drag-handle]",
        onAdd: (evt) => {
          const itemId = evt.item?.getAttribute("data-item-id");
          const toMgrId = parseInt(evt.to?.getAttribute("data-manager-id") || "0", 10) || 0;
          if (!itemId) return;
          setItemLead(itemId, toMgrId || null);
          renderAll();
        },
        onEnd: () => {
          renderList();
          renderGantt();
        }
      });
      WFRT.planState._sortables.push(s);
    });
  }

  // ------------------------------------------------------------
  // Actions (hooks)
  // ------------------------------------------------------------
  function actionBtn(action, title, icon, cls) {
    return `
      <button type="button"
        data-action="${escapeHtml(action)}"
        class="w-8 h-8 rounded-full ${cls} flex items-center justify-center transition-colors"
        title="${escapeHtml(title)}">
        <i class="${escapeHtml(icon)} text-[11px]"></i>
      </button>
    `;
  }

  function taskActionsHtml() {
    const play  = actionBtn("play",   "Start",  "fa-solid fa-play",  "bg-green-100 text-green-700 hover:bg-green-200");
    const pause = actionBtn("pause",  "Pause",  "fa-solid fa-pause", "bg-orange-100 text-orange-700 hover:bg-orange-200");
    const stop  = actionBtn("stop",   "Stop",   "fa-solid fa-stop",  "bg-slate-100 text-slate-700 hover:bg-slate-200");
    const edit  = actionBtn("edit",   "Edit",   "fa-solid fa-pen",   "bg-sky-100 text-sky-700 hover:bg-sky-200");
    const del   = actionBtn("delete", "Delete", "fa-solid fa-trash", "bg-red-100 text-red-700 hover:bg-red-200");
    return `<div class="flex items-center gap-1.5">${play}${pause}${stop}${edit}${del}</div>`;
  }

  function managerProfileBtnHtml(mgr) {
    const id = wfEmpId(mgr) || 0;
    return `
      <button type="button"
        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white border border-slate-200 text-brandDark hover:bg-slate-50 transition"
        data-manager-profile="${escapeHtml(String(id))}"
        title="Profil">
        <i class="fa-solid fa-user text-[11px] mr-1"></i> Profil
      </button>
    `;
  }

  // ------------------------------------------------------------
  // Rendering: Backlog, Board, List, Gantt
  // ------------------------------------------------------------
  function backlogItemHtml(it) {
    const when = [it.planned_date, it.planned_time].filter(Boolean).join(" ");
    const crew = Array.isArray(it.members) ? it.members : [];
    return `
      <div class="glass-card rounded-xl p-3 border border-slate-200 cursor-grab"
           data-item-id="${escapeHtml(String(it.id))}">
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <div class="font-bold text-sm text-slate-800 truncate">${escapeHtml(it.title)}</div>
            <div class="text-xs text-slate-500 mt-1">${escapeHtml(it.category || it.source_type || "")}</div>
          </div>
          <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md">
            <i class="fa-regular fa-clock mr-1"></i>${escapeHtml(when || "—")}
          </span>
        </div>

        <div class="mt-2 flex items-center justify-between">
          <div class="text-[11px] text-slate-500 font-bold">${escapeHtml(String(it.duration_minutes || 0))}m</div>
          <div>${crew.length ? avatarStackHtml(crew, 4) : ""}</div>
        </div>
      </div>
    `;
  }

  function renderBacklog(filter = "") {
    const root = $("checklist-source");
    const cnt = $("task-count");
    if (!root) return;

    const items = WFRT.planState.items || [];
    const grouped = groupItemsByLead(items);
    const backlogItems = (grouped[0] || []).filter((it) => {
      if (!filter) return true;
      const q = filter.toLowerCase();
      return (
        safeText(it.title).toLowerCase().includes(q) ||
        safeText(it.description).toLowerCase().includes(q) ||
        safeText(it.category).toLowerCase().includes(q) ||
        safeText(it.source_type).toLowerCase().includes(q)
      );
    });

    root.innerHTML = backlogItems.map(it => backlogItemHtml(it)).join("");
    if (cnt) cnt.textContent = `${backlogItems.length} Aufgaben`;
  }

  function managerHeaderHtml(mgr, count, teamAvatars) {
    const name = escapeHtml(wfEmpLabel(mgr));
    const role = escapeHtml(mgr?.role || (wfEmpId(mgr) === pmId() ? "PM" : "Manager"));
    return `
      <div class="p-4 bg-white/40 border-b border-white/20 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          ${avatarHtml(mgr, "w-9 h-9")}
          <div class="min-w-0">
            <div class="font-bold text-slate-800 truncate" title="${name}">${name}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide flex items-center gap-2">
              <span>${role}</span>
              <span class="w-1 h-1 rounded-full bg-slate-300"></span>
              <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-md">${escapeHtml(String(count))} Jobs</span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <div class="hidden sm:block" title="Team">${teamAvatars}</div>
          ${managerProfileBtnHtml(mgr)}
        </div>
      </div>
    `;
  }

  function taskCardBoardHtml(it) {
    const when = [it.planned_date, it.planned_time].filter(Boolean).join(" ");
    const timeBadge = when
      ? `<span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md"><i class="fa-regular fa-clock mr-1"></i>${escapeHtml(when)}</span>`
      : `<span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md"><i class="fa-regular fa-clock mr-1"></i>—</span>`;

    const dur = `<span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md">${escapeHtml(String(it.duration_minutes || 0))}m</span>`;
    const cat = it.category ? `<span class="text-[10px] font-bold bg-sky/15 text-brandDark px-2 py-1 rounded-md">${escapeHtml(it.category)}</span>` : "";

    const crew = Array.isArray(it.members) ? it.members : [];
    const crewStack = crew.length ? avatarStackHtml(crew, 5) : `<div class="text-[11px] font-bold text-slate-400">Kein Team</div>`;

    const travel = (it.travelTime || it.arrivalTime)
      ? `<div class="mt-2 bg-slate-50 p-2 rounded-lg border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between">
           <span><i class="fa-solid fa-car mr-1"></i>${escapeHtml(it.travelTime || "—")}</span>
           <span class="font-bold">${escapeHtml(it.arrivalTime || "")}</span>
         </div>`
      : "";

    return `
      <div class="glass-card rounded-2xl p-3 cursor-grab relative group border border-slate-200"
           data-item-id="${escapeHtml(String(it.id))}">
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <div class="font-bold text-sm text-slate-800 truncate" title="${escapeHtml(it.title)}">${escapeHtml(it.title)}</div>
            <div class="text-xs text-slate-500 mt-1 line-clamp-2">${escapeHtml(it.description || it.address || it.source_type || "")}</div>
          </div>
          <button type="button"
            class="w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-brandDark hover:bg-slate-50 flex items-center justify-center"
            data-open-task="${escapeHtml(String(it.id))}" title="Details">
            <i class="fa-solid fa-chevron-right text-[12px]"></i>
          </button>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
          ${timeBadge}
          ${dur}
          ${cat}
        </div>

        ${travel}

        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100">
          <div class="flex items-center gap-2">${crewStack}</div>
          <div class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity"
               data-actions="${escapeHtml(String(it.id))}">
            ${taskActionsHtml()}
          </div>
        </div>
      </div>
    `;
  }

 /* ============================================================
    * FIX 1: Clean Board Rendering (Single Wrapper)
    * ============================================================ */
    function renderBoard() {
        const root = $("view-board");
        if (!root) return;

        const managers = WFRT.planState.managers || [];
        const items = WFRT.planState.items || [];
        const grouped = groupItemsByLead(items);

        root.innerHTML = managers.map((mgr) => {
            const mid = wfEmpId(mgr) || 0;
            const arr = grouped[mid] || [];

            const teamPeople = uniqBy(
                arr.flatMap(t => (Array.isArray(t.members) ? t.members : [])).filter(Boolean),
                (p) => wfEmpId(p) || (p?.id ?? null)
            );
            const teamAvatars = teamPeople.length ? avatarStackHtml(teamPeople, 4) : ``;

            // We use flex-col for the column, and ensure the list container grows
            return `
            <div class="glass-panel rounded-[2rem] flex flex-col h-full overflow-hidden min-w-[320px] w-1/3">
            ${managerHeaderHtml(mgr, arr.length, teamAvatars)}
            
            <div class="p-3 flex-1 overflow-y-auto space-y-3 bg-slate-50/30 min-h-[120px]"
                data-wf-manager-list="1"
                data-manager-id="${escapeHtml(String(mid))}">
                
                ${arr.map(it => `
                <div class="relative group"
                    data-planner-item-id="${escapeHtml(String(it.id))}"
                    data-item-id="${escapeHtml(String(it.id))}"
                    data-title="${escapeHtml(it.title)}">
                    
                    <div class="absolute top-0 right-0 left-0 bottom-0 z-0 drag-handle" data-drag-handle="1"></div>
                    
                    <div class="relative z-10 pointer-events-none">
                            ${taskCardBoardHtml(it)}
                    </div>
                </div>
                `).join("")}
                
                ${arr.length ? "" : `<div class="text-center text-xs font-bold text-slate-400 py-6 pointer-events-none">Keine Jobs</div>`}
            </div>
            </div>
        `;
        }).join("");

        // Initialize Drag and Drop on these new lists
        initDnD();
    }
  function listGroupHeaderHtml(mgr, mid, count, teamPeople, collapsed) {
    const name = escapeHtml(wfEmpLabel(mgr));
    const role = escapeHtml(mgr?.role || (wfEmpId(mgr) === pmId() ? "PM" : "Manager"));
    const chevron = collapsed ? "fa-chevron-down" : "fa-chevron-up";
    const team = teamPeople.length ? avatarStackHtml(teamPeople, 6) : `<span class="text-[11px] font-bold text-slate-400">—</span>`;

    return `
      <div class="px-4 py-3 bg-white/60 border border-slate-200 rounded-2xl flex items-center justify-between gap-3 cursor-pointer"
           data-list-toggle="${escapeHtml(String(mid))}">
        <div class="flex items-center gap-3 min-w-0">
          ${avatarHtml(mgr, "w-9 h-9")}
          <div class="min-w-0">
            <div class="font-bold text-slate-800 truncate">${name}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide flex items-center gap-2">
              <span>${role}</span>
              <span class="w-1 h-1 rounded-full bg-slate-300"></span>
              <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-md">${escapeHtml(String(count))} Jobs</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="hidden sm:block">${team}</div>
          ${managerProfileBtnHtml(mgr)}
          <button type="button" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center">
            <i class="fa-solid ${chevron} text-[12px]"></i>
          </button>
        </div>
      </div>
    `;
  }

  function listRowHtml(it) {
    const when = [it.planned_date, it.planned_time].filter(Boolean).join(" ");
    const travel = it.travelTime ? `${escapeHtml(it.travelTime)}${it.arrivalTime ? ` • Ankunft: ${escapeHtml(it.arrivalTime)}` : ""}` : "—";
    const address = it.address ? escapeHtml(it.address) : "—";

    const crew = Array.isArray(it.members) ? it.members : [];
    const crewStack = crew.length ? avatarStackHtml(crew, 5) : "";

    return `
      <div class="grid grid-cols-12 gap-4 px-4 py-3 border border-slate-200 rounded-xl bg-white hover:bg-slate-50 transition cursor-pointer items-center"
           data-open-task="${escapeHtml(String(it.id))}">
        <div class="col-span-4 font-bold text-slate-700 flex items-center gap-2 min-w-0">
          <div class="w-2 h-2 rounded-full bg-brandDark"></div>
          <div class="truncate" title="${escapeHtml(it.title)}">${escapeHtml(it.title)}</div>
          ${it.category ? `<span class="ml-2 text-[10px] font-bold bg-sky/15 text-brandDark px-2 py-0.5 rounded-md">${escapeHtml(it.category)}</span>` : ""}
        </div>

        <div class="col-span-2 text-xs text-slate-500">
          <div class="font-bold text-slate-600">${escapeHtml(when || "—")}</div>
          <div class="text-[11px] text-slate-400">${escapeHtml(formatMinToHHMM(it.startMin) || "")}</div>
        </div>

        <div class="col-span-3 text-xs text-slate-500">
          <div class="truncate" title="${address}"><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i>${address}</div>
          <div class="text-[11px] text-slate-400 mt-0.5"><i class="fa-solid fa-car mr-1"></i>${travel}</div>
        </div>

        <div class="col-span-1 text-xs font-mono font-bold text-slate-600">${escapeHtml(String(it.duration_minutes || 0))}m</div>

        <div class="col-span-2 flex items-center justify-end gap-2">
          <div class="hidden lg:block">${crewStack}</div>
          <div data-actions="${escapeHtml(String(it.id))}">${taskActionsHtml()}</div>
        </div>
      </div>
    `;
  }

  function renderList() {
    const body = $("list-body");
    if (!body) return;

    const managers = WFRT.planState.managers || [];
    const items = WFRT.planState.items || [];
    const grouped = groupItemsByLead(items);

    body.innerHTML = managers.map((mgr) => {
      const mid = wfEmpId(mgr) || 0;
      const arr = grouped[mid] || [];

      const teamPeople = uniqBy(
        arr.flatMap(t => (Array.isArray(t.members) ? t.members : [])).filter(Boolean),
        (p) => wfEmpId(p) || (p?.id ?? null)
      );

      const collapsed = !!WFRT.planState.collapsedManagers[mid];

      return `
        <div class="space-y-2">
          ${listGroupHeaderHtml(mgr, mid, arr.length, teamPeople, collapsed)}
          <div class="${collapsed ? "hidden" : ""} space-y-2 pl-2" data-list-group="${escapeHtml(String(mid))}">
            ${arr.length ? arr.map(listRowHtml).join("") : `<div class="text-center text-xs font-bold text-slate-400 py-6">Keine Jobs</div>`}
          </div>
        </div>
      `;
    }).join("");

    body.querySelectorAll("[data-list-toggle]").forEach((el) => {
      el.addEventListener("click", (e) => {
        const prof = e.target.closest("[data-manager-profile]");
        if (prof) return;
        const mid = el.getAttribute("data-list-toggle");
        WFRT.planState.collapsedManagers[mid] = !WFRT.planState.collapsedManagers[mid];
        renderList();
      });
    });
  }

  // ------------------------------------------------------------
  // Gantt
  // ------------------------------------------------------------
  function computeGanttWindow() {
    const items = WFRT.planState.items || [];
    const grouped = groupItemsByLead(items);

    let min = null, max = null;

    Object.keys(grouped).forEach((k) => {
      if (String(k) === "0") return;
      (grouped[k] || []).forEach((it) => {
        if (min === null || it.startMin < min) min = it.startMin;
        if (max === null || it.endMin > max) max = it.endMin;
      });
    });

    if (min === null) min = WFRT.START_HOUR * 60;
    if (max === null) max = min + 8 * 60;

    min = clamp(min - 30, 0, 23 * 60);
    max = clamp(max + 30, min + 60, 24 * 60);

    min = Math.floor(min / 30) * 30;
    max = Math.ceil(max / 30) * 30;

    WFRT.planState.ganttStartMin = min;
    WFRT.planState.ganttEndMin = max;
  }

  function ganttWidthPx() {
    const startMin = WFRT.planState.ganttStartMin ?? (WFRT.START_HOUR * 60);
    const endMin = WFRT.planState.ganttEndMin ?? (startMin + 8 * 60);
    const hours = (endMin - startMin) / 60;
    return Math.max(900, hours * WFRT.PIXELS_PER_HOUR);
  }

  function renderGanttScale() {
    const scale = $("time-scale");
    if (!scale) return;

    computeGanttWindow();
    const startMin = WFRT.planState.ganttStartMin;
    const endMin = WFRT.planState.ganttEndMin;

    const width = ganttWidthPx();
    scale.style.minWidth = (width + 220) + "px";

    const hours = [];
    for (let m = startMin; m <= endMin; m += 60) hours.push(m);

    scale.innerHTML = `
      <div class="absolute left-0 top-0 h-full" style="width:${width}px">
        ${hours.map((m) => {
          const x = ((m - startMin) / (endMin - startMin)) * width;
          return `
            <div class="absolute top-0 h-full flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                 style="left:${x}px">
              ${escapeHtml(formatMinToHHMM(m))}
            </div>
          `;
        }).join("")}
      </div>
    `;
  }

  function renderGantt() {
    const container = $("gantt-tasks-container");
    const linesContainer = $("lines-container");
    const svg = $("gantt-lines");
    const ganttBody = $("gantt-body");
    if (!container) return;

    computeGanttWindow();

    const startMin = WFRT.planState.ganttStartMin;
    const endMin = WFRT.planState.ganttEndMin;
    const totalMin = Math.max(1, endMin - startMin);
    const width = ganttWidthPx();

    container.style.minWidth = (width + 220) + "px";
    container.innerHTML = "";

    const managers = WFRT.planState.managers || [];
    const items = WFRT.planState.items || [];
    const grouped = groupItemsByLead(items);

    const ROW_H = 80;
    let y = 0;

    managers.forEach((mgr) => {
      const mid = wfEmpId(mgr) || 0;
      const arr = grouped[mid] || [];

      const label = document.createElement("div");
      label.className = "w-48 border-r p-3 flex items-center gap-2 bg-white/80 sticky left-0 z-20 backdrop-blur-sm absolute";
      label.style.top = `${y}px`;
      label.style.height = `${ROW_H}px`;
      label.innerHTML = `${avatarHtml(mgr, "w-9 h-9")}<div class="min-w-0"><div class="font-bold text-slate-800 truncate">${escapeHtml(wfEmpLabel(mgr))}</div><div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">${escapeHtml(mgr?.role || "Manager")}</div></div>`;
      container.appendChild(label);

      const rowBg = document.createElement("div");
      rowBg.className = "absolute left-48 right-0 border-b border-slate-200/70";
      rowBg.style.top = `${y}px`;
      rowBg.style.height = `${ROW_H}px`;
      rowBg.style.width = `${width}px`;
      container.appendChild(rowBg);

      arr.forEach((it) => {
        const leftPx = ((it.startMin - startMin) / totalMin) * width;
        const barW = Math.max(40, ((it.endMin - it.startMin) / totalMin) * width);

        const bar = document.createElement("div");
        bar.className = "gantt-bar bg-sky-200 border border-sky-300 text-sky-900";
        bar.style.top = `${y + 20}px`;
        bar.style.left = `${48 + leftPx}px`;
        bar.style.width = `${barW}px`;
        bar.setAttribute("data-open-task", String(it.id));
        bar.setAttribute("data-item-id", String(it.id));

        const crew = Array.isArray(it.members) ? it.members : [];
        const crewMini = crew.length ? `<div class="ml-2 hidden md:flex">${avatarStackHtml(crew, 3)}</div>` : "";
        const tLabel = [it.planned_time || "", `${it.duration_minutes || 0}m`].filter(Boolean).join(" • ");

        bar.innerHTML = `
          <span class="truncate pointer-events-none">${escapeHtml(it.title)}</span>
          <span class="ml-2 text-[10px] font-bold text-slate-700/70 pointer-events-none">${escapeHtml(tLabel)}</span>
          ${crewMini}
          <div class="ml-auto flex items-center gap-1 pointer-events-auto" data-actions="${escapeHtml(String(it.id))}">
            ${actionBtn("play", "Start", "fa-solid fa-play", "bg-green-100 text-green-700 hover:bg-green-200")}
            ${actionBtn("pause", "Pause", "fa-solid fa-pause", "bg-orange-100 text-orange-700 hover:bg-orange-200")}
            ${actionBtn("edit", "Edit", "fa-solid fa-pen", "bg-sky-100 text-sky-700 hover:bg-sky-200")}
          </div>
        `;
        container.appendChild(bar);
      });

      y += ROW_H;
    });

    container.style.height = `${y + 40}px`;

    if (svg && linesContainer) {
      linesContainer.innerHTML = "";
      const hourStep = 60;
      const gridCount = Math.floor((endMin - startMin) / hourStep);

      for (let i = 0; i <= gridCount; i++) {
        const m = startMin + i * hourStep;
        const x = ((m - startMin) / totalMin) * width;
        linesContainer.insertAdjacentHTML("beforeend",
          `<line x1="${48 + x}" y1="0" x2="${48 + x}" y2="${y + 40}" stroke="rgba(226,232,240,1)" stroke-width="1" stroke-dasharray="4 4"></line>`
        );
      }

      svg.setAttribute("width", String(48 + width + 240));
      svg.setAttribute("height", String(y + 60));
    }

    if (ganttBody) {
      ganttBody.onscroll = () => {
        // reserved for dependency redraw if you add it later
      };
    }
  }

  // ------------------------------------------------------------
  // View switching
  // ------------------------------------------------------------
  window.switchView = function (view) {
    WFRT.planState.activeView = view;

    ["board", "gantt", "list"].forEach((v) => {
      const el = $(`view-${v}`);
      const btn = $(`btn-view-${v}`);
      if (el) el.classList.toggle("hidden", v !== view);

      if (btn) {
        const active = v === view;
        btn.classList.toggle("bg-white", active);
        btn.classList.toggle("shadow-sm", active);
        btn.classList.toggle("text-brandDark", active);
        btn.classList.toggle("font-bold", active);

        btn.classList.toggle("text-slate-500", !active);
        btn.classList.toggle("font-medium", !active);
      }
    });

    if (view === "gantt") renderGanttScale();
  };

  // ------------------------------------------------------------
  // Root clicks (delegation)
  // ------------------------------------------------------------
  function bindRootClicks() {
    document.addEventListener("click", (e) => {
      const openTask = e.target.closest("[data-open-task]");
      if (openTask) {
        const id = parseInt(openTask.getAttribute("data-open-task") || "0", 10);
        if (id && typeof window.__WF_openPlanItem === "function") window.__WF_openPlanItem(id);
        return;
      }

      const actionBtnEl = e.target.closest("button[data-action]");
      if (actionBtnEl) {
        const action = actionBtnEl.getAttribute("data-action");
        const wrap = actionBtnEl.closest("[data-actions]");
        const itemId = wrap ? parseInt(wrap.getAttribute("data-actions") || "0", 10) : 0;
        e.stopPropagation();
        e.preventDefault();
        if (typeof window.__WF_taskAction === "function") window.__WF_taskAction(action, itemId, {});
        return;
      }

      const prof = e.target.closest("[data-manager-profile]");
      if (prof) {
        e.stopPropagation();
        e.preventDefault();
        const empId = parseInt(prof.getAttribute("data-manager-profile") || "0", 10);
        if (empId && typeof window.__WF_openEmployeeProfile === "function") window.__WF_openEmployeeProfile(empId);
      }
    }, { capture: true });
  }

  // ------------------------------------------------------------
  // Render all
  // ------------------------------------------------------------
  function renderAll() {
    renderManagersWidget();

    const q = ($("task-search")?.value || "").trim();
    renderBacklog(q);

    renderBoard();
    renderList();
    renderGantt();

    window.switchView(WFRT.planState.activeView || "board");
  }

  // ------------------------------------------------------------
  // Load plan -> state -> render
  // ------------------------------------------------------------
  async function loadPlanById(planId) {
    if (!planId) return;

    WFRT.planState.activePlanId = planId;

    const board = $("view-board");
    const list = $("list-body");
    const gantt = $("gantt-tasks-container");
    const backlog = $("checklist-source");

    const loading = `<div class="text-sm font-bold text-slate-500 p-6">Lade Plan…</div>`;
    if (board) board.innerHTML = loading;
    if (list) list.innerHTML = loading;
    if (gantt) gantt.innerHTML = loading;
    if (backlog) backlog.innerHTML = loading;

    try {
      const payload = await fetchPlanJson(planId);
      WFRT.planState.payload = payload;

      // for crew modal
      WFRT.planState.employees_active = Array.isArray(payload?.employees_active) ? payload.employees_active : [];

      // persisted extra managers
      const sel = payload?.manager_selection || {};
      WFRT.planState.extraManagerIds = asIntArray(sel?.extra_manager_ids || sel?.manager_ids || []);

      const items = (payload?.items || []).map(normalizeItem);

      const { managers, managerById, pm, team } = buildManagers(payload, items);
      WFRT.planState.managers = managers;
      WFRT.planState.managerById = managerById;
      WFRT.planState.project_manager = pm;
      WFRT.planState.team = team;

      // ensure item lead references manager instance if exists
      items.forEach((it) => {
        if (it.leadId && managerById[it.leadId]) {
          it.lead = managerById[it.leadId];
          if (it.raw) it.raw.lead = managerById[it.leadId];
        } else {
          it.lead = null;
          it.leadId = null;
          if (it.raw) it.raw.lead = null;
        }
      });

      WFRT.planState.items = items;

      renderAll();
    } catch (err) {
      const msg = err?.message || "Plan konnte nicht geladen werden.";
      const out = `<div class="text-sm font-bold text-red-600 p-6">${escapeHtml(msg)}</div>`;
      if (board) board.innerHTML = out;
      if (list) list.innerHTML = out;
      if (gantt) gantt.innerHTML = out;
      if (backlog) backlog.innerHTML = out;
    }
  }

  // ------------------------------------------------------------
  // Bindings
  // ------------------------------------------------------------
  function bindPlanSelector() {
    const sel = $("plan-selector");
    if (!sel) return;

    sel.disabled = false;
    sel.addEventListener("change", () => {
      const planId = sel.value ? parseInt(sel.value, 10) : null;
      if (planId) loadPlanById(planId);
    });

    const initial = sel.value ? parseInt(sel.value, 10) : null;
    if (initial) loadPlanById(initial);
  }

  function bindSearch() {
    const input = $("task-search");
    if (!input) return;

    let t = null;
    input.addEventListener("input", () => {
      clearTimeout(t);
      t = setTimeout(() => renderBacklog((input.value || "").trim()), 120);
    });
  }

  // expose manual loader
  window.__WF_loadPlanById = loadPlanById;

  // ------------------------------------------------------------
  // Init
  // ------------------------------------------------------------
  document.addEventListener("DOMContentLoaded", () => {
    bindRootClicks();
    bindPlanSelector();
    bindSearch();

    // auto-load first valid option if empty
    const sel = $("plan-selector");
    if (sel && !sel.value) {
      const opt = Array.from(sel.options || []).find(o => o.value && String(o.value).trim() !== "");
      if (opt) {
        sel.value = opt.value;
        const planId = parseInt(opt.value, 10);
        if (planId) loadPlanById(planId);
      }
    }
  });

})();
</script>


<!-- Play, Pause, stop function s  -->
<script>
(() => {
  "use strict";

  // ------------------------------------------------------------
  // Global runtime
  // ------------------------------------------------------------
  window.__WF = window.__WF || {};
  const WF = window.__WF;

  const $ = (id) => document.getElementById(id);

  const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
  const BASE_URL =
    document.querySelector('meta[name="planner-base-url"]')?.getAttribute("content") ||
    (window.location.origin + "/planner");

  const normalizeBaseUrl = (u) => String(u || "").replace(/\/+$/, "");

  const toast = (msg) => {
    if (typeof WF.toast === "function") return WF.toast(msg);
    console.log("[WF]", msg);
  };

  // ------------------------------------------------------------
  // Inject minimal CSS so badge is visible
  // ------------------------------------------------------------
  (() => {
    if (document.getElementById("wf-state-style")) return;
    const st = document.createElement("style");
    st.id = "wf-state-style";
    st.textContent = `
      .wf-state-badge{ z-index:9999; }
      .wf-state-badge[data-state="paused"]{ background:rgba(255,237,213,.95); border-color:rgba(251,146,60,.55); color:#9a3412; }
      .wf-state-badge[data-state="stopped"]{ background:rgba(254,226,226,.95); border-color:rgba(248,113,113,.55); color:#991b1b; }
      .wf-state-badge[data-state="running"],
      .wf-state-badge[data-state="playing"]{ background:rgba(220,252,231,.95); border-color:rgba(74,222,128,.55); color:#166534; }
    `;
    document.head.appendChild(st);
  })();

  // ------------------------------------------------------------
  // Status memory (survives re-render)
  // ------------------------------------------------------------
  WF._planItemRuntimeState = WF._planItemRuntimeState || {}; // { [itemId]: {state, reason, updated_at} }

  function rememberState(itemId, state, reason) {
    const id = String(Number(itemId || 0));
    if (!id || id === "0") return;
    WF._planItemRuntimeState[id] = {
      state: String(state || ""),
      reason: String(reason || ""),
      updated_at: Date.now()
    };
  }

  // ------------------------------------------------------------
  // Reason Modal (robust show/hide)
  // ------------------------------------------------------------
  const Reason = { open:false, action:null, itemId:null, title:"" };

  function showModal(modal) {
    if (!modal) return;
    modal.classList.remove("hidden","opacity-0","pointer-events-none","invisible");
    // in case markup relies on flex
    modal.classList.add("flex");
    modal.style.zIndex = "9999";
    modal.style.position = modal.style.position || "fixed";
  }

  function hideModal(modal) {
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.add("opacity-0","pointer-events-none","invisible");
  }

  function openReasonModal({ action, itemId, title }) {
    const modal = $("wf-reason-modal");
    if (!modal) {
      // last resort if modal missing
      const r = window.prompt("Bitte Grund eingeben:");
      if (r && String(r).trim()) {
        runAndRefresh(action, itemId, { reason: String(r).trim() }).catch(() => {});
      }
      return;
    }

    Reason.open = true;
    Reason.action = String(action || "").toLowerCase();
    Reason.itemId = Number(itemId || 0) || null;
    Reason.title = String(title || "").trim();

    const titleEl = $("wf-reason-title");
    const subEl   = $("wf-reason-sub");
    const textEl  = $("wf-reason-text");

    if (titleEl) {
      titleEl.textContent =
        Reason.action === "pause" ? "Pause: Grund angeben" :
        Reason.action === "stop"  ? "Stop: Grund angeben" :
        "Grund angeben";
    }

    if (subEl) {
      subEl.textContent = Reason.title ? Reason.title : `Aufgabe #${Reason.itemId || "—"}`;
    }

    if (textEl) {
      textEl.value = "";
      textEl.placeholder = "Grund...";
    }

    showModal(modal);
    setTimeout(() => textEl?.focus?.(), 30);
  }

  function closeReasonModal() {
    const modal = $("wf-reason-modal");
    hideModal(modal);
    Reason.open = false;
    Reason.action = null;
    Reason.itemId = null;
    Reason.title = "";
  }

  (() => {
    const modal = $("wf-reason-modal");
    if (!modal || modal.dataset.bound === "1") return;
    modal.dataset.bound = "1";

    modal.addEventListener("click", (ev) => {
      const el = ev.target;
      if (el && el.getAttribute && el.getAttribute("data-close") === "1") closeReasonModal();
    });

    document.addEventListener("keydown", (ev) => {
      if (ev.key === "Escape" && Reason.open) closeReasonModal();
    });
  })();

  // ------------------------------------------------------------
  // HTTP helpers
  // ------------------------------------------------------------
  async function postJson(url, payload) {
    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF(),
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
      body: JSON.stringify(payload || {}),
    });

    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text().catch(() => "");
    if (!ct.includes("application/json")) {
      throw new Error(`Non-JSON response (status ${res.status}). Snippet: ${raw.slice(0, 180)}`);
    }
    const json = JSON.parse(raw);
    if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);
    return json;
  }

  async function getJson(url) {
    const res = await fetch(url, {
      method: "GET",
      headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
      credentials: "same-origin",
    });

    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text().catch(() => "");
    if (!ct.includes("application/json")) {
      throw new Error(`Non-JSON response (status ${res.status}). Snippet: ${raw.slice(0, 180)}`);
    }
    const json = JSON.parse(raw);
    if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);
    return json;
  }

  function getActivePlanId() {
    // first script stores active plan here
    if (WF?.planState?.activePlanId) return Number(WF.planState.activePlanId) || null;

    const sel = $("plan-selector");
    const v = sel?.value || "";
    const n = Number(v);
    return Number.isFinite(n) && n > 0 ? n : null;
  }

  // ------------------------------------------------------------
  // Endpoints (your real routes)
  // ------------------------------------------------------------
  function resolvePlanItemActionUrl(planId, itemId, action) {
    const pid = Number(planId || 0);
    const iid = Number(itemId || 0);
    const a = String(action || "").toLowerCase();
    if (!pid) throw new Error("Missing planId");
    if (!iid) throw new Error("Missing itemId");
    if (!["play","pause","stop"].includes(a)) throw new Error("Invalid action");
    return `${normalizeBaseUrl(BASE_URL)}/plans/${pid}/items/${iid}/${a}`;
  }

  function resolveStatusesUrl(planId) {
    const pid = Number(planId || 0);
    if (!pid) return null;
    return `${normalizeBaseUrl(BASE_URL)}/plans/${pid}/items/status`;
  }

  // ------------------------------------------------------------
  // Badge apply (DOM)
  // ------------------------------------------------------------
  function ensureBadge(hostEl) {
    let badge = hostEl.querySelector?.(".wf-state-badge");
    if (badge) return badge;

    badge = document.createElement("div");
    badge.className =
      "wf-state-badge absolute top-3 right-3 text-[10px] font-extrabold px-2 py-1 rounded-lg border";
    if (getComputedStyle(hostEl).position === "static") hostEl.style.position = "relative";
    hostEl.appendChild(badge);
    return badge;
  }

  function applyStateToElement(el, state, reason) {
    const s = String(state || "").toLowerCase();
    const badge = ensureBadge(el);
    badge.setAttribute("data-state", s);

    const label =
      s === "paused" ? "Paused" :
      s === "stopped" ? "Stopped" :
      (s === "running" || s === "playing") ? "Running" :
      (s || "—");

    badge.textContent = reason ? `${label} • ${reason}` : label;
  }

  function applyRememberedStatesToDom() {
    const map = WF._planItemRuntimeState || {};
    Object.keys(map).forEach((itemId) => {
      const st = map[itemId];
      if (!st) return;

      // apply to card/bar (prefer inner elements that already have styling)
      const els = document.querySelectorAll(`[data-item-id="${itemId}"]`);
      els.forEach((el) => applyStateToElement(el, st.state, st.reason));
    });
  }

  // ------------------------------------------------------------
  // Status endpoint -> memory -> DOM
  // ------------------------------------------------------------
  function normalizeStatusesPayload(payload) {
    if (!payload) return null;
    if (payload.ok === true && payload.data !== undefined) return payload.data;
    if (payload.data !== undefined) return payload.data;
    return payload;
  }

  function digestStatusesIntoMemory(statusesRaw) {
    const statuses = normalizeStatusesPayload(statusesRaw);
    if (!statuses) return;

    // object map: { "12": {state,reason}, ... }
    if (!Array.isArray(statuses) && typeof statuses === "object") {
      Object.keys(statuses).forEach((k) => {
        const id = Number(k);
        if (!id) return;
        const v = statuses[k] || {};
        rememberState(id, v.state || v.status || v.current_state || "", v.reason || "");
      });
      return;
    }

    // array: [{item_id, state, reason}, ...]
    if (Array.isArray(statuses)) {
      statuses.forEach((v) => {
        const id = Number(v?.item_id || v?.itemId || v?.id || 0);
        if (!id) return;
        rememberState(id, v.state || v.status || v.current_state || "", v.reason || "");
      });
    }
  }

  async function fetchStatusesAndApply(planId) {
    const url = resolveStatusesUrl(planId);
    if (!url) return;
    const statuses = await getJson(url);
    digestStatusesIntoMemory(statuses);
    applyRememberedStatesToDom();
  }

  // ------------------------------------------------------------
  // Core action runner (ALSO updates badge immediately)
  // ------------------------------------------------------------
  function localStateFromAction(action) {
    const a = String(action || "").toLowerCase();
    if (a === "pause") return "paused";
    if (a === "stop") return "stopped";
    if (a === "play") return "running";
    return "";
  }

  function applyImmediateBadge(itemId, action, reason) {
    const st = localStateFromAction(action);
    if (!st) return;
    rememberState(itemId, st, reason || "");
    applyRememberedStatesToDom();
  }

  async function runAction(action, itemId, extra = {}) {
    const a = String(action || "").toLowerCase();
    const id = Number(itemId || 0);
    if (!id) throw new Error("Invalid itemId");

    const planId = getActivePlanId();
    if (!planId) throw new Error("Kein Plan ausgewählt.");

    const url = resolvePlanItemActionUrl(planId, id, a);

    const payload = {
      plan_id: planId,
      item_id: id,
      action: a,
      ...extra,
    };

    const resp = await postJson(url, payload);

    // Update UI immediately even if statuses endpoint is slow/missing
    applyImmediateBadge(id, a, extra?.reason || resp?.data?.reason || resp?.reason || "");

    return { resp, planId };
  }

  // ------------------------------------------------------------
  // Refresh after action (re-apply after re-render)
  // ------------------------------------------------------------
  async function refreshUI(planId) {
    // 1) reload plan using first script if available
    if (typeof window.__WF_loadPlanById === "function") {
      try { await window.__WF_loadPlanById(planId); } catch (e) { console.warn(e); }
    }

    // 2) re-apply remembered states AFTER render
    // (render happens async, so do a couple of ticks)
    setTimeout(applyRememberedStatesToDom, 30);
    setTimeout(applyRememberedStatesToDom, 180);

    // 3) try fetching statuses (if endpoint exists) and re-apply
    try { await fetchStatusesAndApply(planId); } catch (_) {}
  }

  // helper used by modal prompt fallback too
  async function runAndRefresh(action, itemId, extra) {
    const { planId } = await runAction(action, itemId, extra);
    await refreshUI(planId);
  }

  function actionNeedsReason(action) {
    const a = String(action || "").toLowerCase();
    return a === "pause" || a === "stop";
  }

  // ------------------------------------------------------------
  // Public API used by first script buttons
  // ------------------------------------------------------------
  window.__WF_taskAction = function __WF_taskAction(action, itemId, extra = {}) {
    const a = String(action || "").toLowerCase();
    const id = Number(itemId || 0);

    if (!id) {
      toast("Fehler: Aufgabe nicht gefunden.");
      return;
    }

    if (actionNeedsReason(a)) {
      openReasonModal({
        action: a,
        itemId: id,
        title: extra?.title || extra?.label || "",
      });
      return;
    }

    (async () => {
      try {
        const { planId } = await runAction(a, id, extra);
        toast("Fortgesetzt ✅");
        await refreshUI(planId);
      } catch (e) {
        toast(e?.message || "Aktion fehlgeschlagen");
      }
    })();
  };

  // Save reason
  (() => {
    const btn = $("wf-reason-save");
    if (!btn || btn.dataset.bound === "1") return;
    btn.dataset.bound = "1";

    btn.addEventListener("click", async () => {
      const action = Reason.action;
      const itemId = Reason.itemId;
      const reason = ($("wf-reason-text")?.value || "").trim();

      if (!itemId || !action) return closeReasonModal();
      if (!reason) {
        $("wf-reason-text")?.focus?.();
        toast("Bitte einen Grund eingeben.");
        return;
      }

      btn.disabled = true;
      btn.classList.add("opacity-60", "cursor-not-allowed");

      try {
        const { planId } = await runAction(action, itemId, { reason });
        closeReasonModal();
        toast(action === "pause" ? "Pausiert ✅" : "Gestoppt ✅");
        await refreshUI(planId);

        // keep inspector refreshed if available
        if (typeof window.__WF_openPlanItem === "function") {
          try { window.__WF_openPlanItem(itemId); } catch {}
        }
      } catch (e) {
        toast(e?.message || "Aktion fehlgeschlagen");
      } finally {
        btn.disabled = false;
        btn.classList.remove("opacity-60", "cursor-not-allowed");
      }
    });
  })();

  // ------------------------------------------------------------
  // On load / plan change: apply remembered states + try statuses
  // ------------------------------------------------------------
  document.addEventListener("DOMContentLoaded", () => {
    applyRememberedStatesToDom();

    const pid = getActivePlanId();
    if (pid) fetchStatusesAndApply(pid).catch(() => {});

    const sel = $("plan-selector");
    if (sel && sel.dataset.boundStatuses !== "1") {
      sel.dataset.boundStatuses = "1";
      sel.addEventListener("change", () => {
        const n = Number(sel.value || 0);
        if (!n) return;
        setTimeout(applyRememberedStatesToDom, 60);
        fetchStatusesAndApply(n).catch(() => {});
      });
    }
  });

})();
</script>
 
 <!-- ============================================================
     WF PLANNER — FULL SCRIPT (Fix: Sidebar loads Phase/Personal/Appointment/Ticket)
     Paste this AFTER: window.__WF_CONFIG = @json($plannerConfig);
     It replaces your scripts 1–5 (Core + Customer + Project + Plans + Boot)
     Your big Wizard script can stay BELOW this unchanged ✅
     ============================================================ -->
<script>
(() => {
  "use strict";

  /* ============================================================
   * 1) WF Core (state, helpers, http)
   * ============================================================ */
  window.__WF = window.__WF || {};
  const WF = window.__WF;

  WF.cfg = window.__WF_CONFIG || {};
  WF.api = WF.cfg.endpoints || {};

  WF.state = WF.state || {
    customer: null,
    project: null,   // normalized project (lead_product_list row)
    planId: null,
  };

  WF.csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  WF.toast = (msg) => {
    const t = document.getElementById("toast");
    if (!t) return alert(msg);
    const h4 = t.querySelector("div h4");
    if (h4) h4.innerText = msg;
    t.classList.remove("translate-y-20", "opacity-0");
    setTimeout(() => t.classList.add("translate-y-20", "opacity-0"), 2500);
  };

  WF.httpGet = async (url, params = {}) => {
    if (!url) throw new Error("WF.httpGet: Missing URL");

    const u = new URL(url, window.location.origin);
    Object.entries(params).forEach(([k, v]) => {
      if (v === undefined || v === null || v === "") return;
      u.searchParams.set(k, String(v));
    });

    const res = await fetch(u.toString(), {
      method: "GET",
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": WF.csrf(),
      },
      credentials: "same-origin",
    });

    const json = await res.json().catch(() => null);
    if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);
    return json;
  };

  /* ============================================================
   * 2) Customer Search Dropdown
   * ============================================================ */
  (() => {
    const input = document.getElementById("customer-search-input");
    const list = document.getElementById("customer-dropdown-list");
    const chevron = document.getElementById("customer-chevron");
    const container = document.getElementById("customer-select-container");

    if (!input || !list || !container) return;

    let debounceId = null;

    const customerLabel = (c) => {
      const name = c?.firma
        ? c.firma
        : [c?.title, c?.academic_title, c?.name, c?.lastname].filter(Boolean).join(" ").trim();
      const sub = [c?.customer_no, c?.city, c?.postcode].filter(Boolean).join(" • ");
      return sub ? `${name} — ${sub}` : name;
    };

    const openList = () => {
      list.classList.remove("hidden");
      chevron?.classList.add("rotate-180");
    };

    const closeList = () => {
      list.classList.add("hidden");
      chevron?.classList.remove("rotate-180");
    };

    const renderCustomers = (rows) => {
      list.innerHTML = "";

      if (!rows.length) {
        const empty = document.createElement("div");
        empty.className = "px-4 py-3 text-sm text-slate-500";
        empty.textContent = "No customers found.";
        list.appendChild(empty);
        return;
      }

      rows.forEach((c) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className =
          "w-full text-left px-4 py-3 hover:bg-slate-50 text-sm text-slate-700 flex items-start gap-3";
        item.innerHTML = `
          <span class="mt-0.5 text-slate-400"><i class="fa-solid fa-user-tie"></i></span>
          <span class="font-semibold leading-5">${customerLabel(c)}</span>
        `;

        item.addEventListener("click", async () => {
          WF.state.customer = c;
          WF.state.project = null;
          WF.state.planId = null;

          input.value = customerLabel(c);
          closeList();

          try {
            await WF.loadLeadProducts();
          } catch (e) {
            WF.toast(e?.message || "Failed loading projects");
          }
        });

        list.appendChild(item);
      });
    };

    const fetchCustomers = async (q) => {
      if (!WF.api?.customers) throw new Error("Missing endpoint: endpoints.customers");
      const resp = await WF.httpGet(WF.api.customers, { q: q || "" });
      return Array.isArray(resp?.data) ? resp.data : [];
    };

    window.showCustomerDropdown = async function () {
      openList();
      try {
        const rows = await fetchCustomers(input.value || "");
        renderCustomers(rows);
      } catch {
        renderCustomers([]);
      }
    };

    window.filterCustomerDropdown = function () {
      openList();
      clearTimeout(debounceId);
      debounceId = setTimeout(async () => {
        try {
          const rows = await fetchCustomers((input.value || "").trim());
          renderCustomers(rows);
        } catch {
          renderCustomers([]);
        }
      }, 250);
    };

    document.addEventListener("click", (ev) => {
      if (!container.contains(ev.target)) closeList();
    });
  })();

  /* ============================================================
   * 3) Lead Products / Project Selector (Select2)
   * ============================================================ */
  (() => {
    const statusToGerman = (s) => {
      const v = String(s || "").trim().toLowerCase();
      const map = {
        open: "Offen",
        opened: "Offen",
        new: "Neu",
        planned: "Geplant",
        scheduled: "Eingeplant",
        in_progress: "In Arbeit",
        inprogress: "In Arbeit",
        processing: "In Bearbeitung",
        waiting: "Wartet",
        pending: "Ausstehend",
        on_hold: "Pausiert",
        paused: "Pausiert",
        done: "Erledigt",
        completed: "Abgeschlossen",
        finished: "Fertig",
        canceled: "Storniert",
        cancelled: "Storniert",
        rejected: "Abgelehnt",
        archive: "Archiviert",
        archived: "Archiviert",
      };
      return map[v] || (s ? String(s) : "—");
    };

    const articleGroupLabel = (r) =>
      r?.article_group_name ||
      r?.article_group_title ||
      r?.article_group_label ||
      r?.article_group ||
      r?.group_name ||
      r?.group_title ||
      (r?.article_group_id ? `Produktgruppe #${r.article_group_id}` : "");

    const productLabel = (r) =>
      r?.product_title ||
      r?.product_product ||
      r?.product_name ||
      [r?.product_model, r?.product_status].filter(Boolean).join(" • ") ||
      (r?.product_id ? `Produkt #${r.product_id}` : "");

    const projectSelectText = (r) => {
      const grp = articleGroupLabel(r);
      const prod = productLabel(r);

      const statusRaw =
        r?.lead_product_status || r?.status || r?.project_status || r?.lead_status || r?.stage_status;
      const statusDe = r?.lead_product_status_de || statusToGerman(statusRaw);

      const parts = [];
      if (grp) parts.push(grp);
      if (prod) parts.push(prod);

      return `#${r?.id} — ${parts.join(" / ")} • ${statusDe}`;
    };

    const leadProductsUrl = (customerId) => {
      const tpl = WF.api?.leadProducts || "";
      if (!tpl) throw new Error("Missing endpoint: endpoints.leadProducts");
      return tpl.replace("___ID___", String(customerId));
    };

    WF.resetProjectSelect = () => {
      const sel = document.getElementById("project-selector");
      if (!sel) return;

      if (window.jQuery && window.jQuery(sel).data("select2")) window.jQuery(sel).select2("destroy");
      sel.innerHTML = `<option value=""></option>`;
      sel.disabled = true;

      WF._leadProductsById = {};
    };

    WF.resetPlanSelect = WF.resetPlanSelect || (() => {
      const sel = document.getElementById("plan-selector");
      if (!sel) return;
      sel.innerHTML = `<option value="">Plan wählen…</option>`;
      sel.disabled = true;
      WF._plansById = {};
      WF._lastPlansList = [];
    });

    WF._isHandlingProjectChange = false;

    WF.handleProjectChange = async () => {
      if (WF._isHandlingProjectChange) return;
      WF._isHandlingProjectChange = true;

      try {
        const sel = document.getElementById("project-selector");
        if (!sel) return;

        const id = sel.value ? String(sel.value) : "";

        if (!id) {
          WF.state.project = null;
          WF.state.planId = null;
          WF.resetPlanSelect();
          if (WF.sidebar?.refresh) WF.sidebar.refresh();
          return;
        }

        const r = WF._leadProductsById?.[id];
        if (!r) {
          WF.toast("Fehler: Produktdaten nicht gefunden.");
          return;
        }

        const grp = articleGroupLabel(r);
        const prod = productLabel(r);

        WF.state.project = {
          project_id: Number(r.id),
          customer_id: Number(r.customer_id),
          alternative_id: r.alternative_id ? Number(r.alternative_id) : null,
          article_group_id: r.article_group_id ? Number(r.article_group_id) : null,
          product_id: r.product_id ? Number(r.product_id) : null,
          product_name: prod,
          article_group_name: grp || "",
        };

        if (!WF.state.project.article_group_id) {
          WF.toast("Fehler: article_group_id fehlt (Produktgruppe nicht gefunden).");
          WF.resetPlanSelect();
          if (WF.sidebar?.refresh) WF.sidebar.refresh();
          return;
        }

        if (typeof WF.loadPlansForSelectedProject === "function") {
          await WF.loadPlansForSelectedProject();
        }

        if (WF.sidebar?.refresh) WF.sidebar.refresh();
      } finally {
        WF._isHandlingProjectChange = false;
      }
    };

    window.changeProject = (value) => {
      const sel = document.getElementById("project-selector");
      if (!sel) return;
      if (value !== undefined && value !== null && String(value) !== sel.value) sel.value = String(value);
      WF.handleProjectChange();
    };

    WF.initProjectSelect2 = () => {
      const sel = document.getElementById("project-selector");
      if (!sel || !window.jQuery) return;
      const $ = window.jQuery;

      if ($(sel).data("select2")) return;

      $(sel).select2({
        width: "100%",
        placeholder: "Select product & site…",
        allowClear: true,
        escapeMarkup: (m) => m,
      });

      $(sel).on("change", () => WF.handleProjectChange());
    };

    WF.loadLeadProducts = async () => {
      WF.resetProjectSelect();
      WF.resetPlanSelect();

      const customer = WF.state.customer;
      if (!customer?.id) return;

      const resp = await WF.httpGet(leadProductsUrl(customer.id));
      const rows = Array.isArray(resp?.lead_product_lists) ? resp.lead_product_lists : [];

      WF._leadProductsById = {};
      rows.forEach((r) => (WF._leadProductsById[String(r.id)] = r));

      const sel = document.getElementById("project-selector");
      if (!sel) return;

      sel.disabled = false;
      sel.innerHTML = `<option value=""></option>`;
      rows.forEach((r) => {
        const opt = document.createElement("option");
        opt.value = String(r.id);
        opt.textContent = projectSelectText(r);
        sel.appendChild(opt);
      });

      WF.initProjectSelect2();
      if (window.jQuery && window.jQuery(sel).data("select2")) {
        window.jQuery(sel).prop("disabled", false).trigger("change.select2");
      }
    };
  })();

  /* ============================================================
   * 4) Plans Dropdown
   * ============================================================ */
  (() => {
    const resolvePlansEndpoint = () => {
      const candidates = [
        WF.api?.plansByProject,
        WF.api?.projectPlans,
        WF.api?.plans,
        WF.api?.plannerPlans,
      ].filter(Boolean);
      return candidates[0] || null;
    };

    const planLabel = (p) => {
      const title = p?.title || p?.name || p?.plan_title || p?.reference || (p?.id ? `Plan #${p.id}` : "Plan");
      const meta = [
        p?.stage ? `Stage: ${p.stage}` : null,
        p?.created_at ? `Erstellt: ${String(p.created_at).slice(0, 10)}` : null,
      ].filter(Boolean).join(" • ");
      return meta ? `${title} — ${meta}` : title;
    };

    WF.resetPlanSelect = () => {
      const sel = document.getElementById("plan-selector");
      if (!sel) return;
      if (window.jQuery && window.jQuery(sel).data("select2")) window.jQuery(sel).select2("destroy");
      sel.innerHTML = `<option value="">Plan wählen…</option>`;
      sel.disabled = true;
      WF._plansById = {};
      WF._lastPlansList = [];
    };

    const fetchPlansForProject = async (project) => {
      const endpoint = resolvePlansEndpoint();
      if (!endpoint) return []; // optional

      const hasPlaceholders = /___[A-Z0-9_]+___/.test(endpoint);

      const url = hasPlaceholders
        ? endpoint
            .replace("___CUSTOMER_ID___", String(project?.customer_id ?? ""))
            .replace("___PROJECT_ID___", String(project?.project_id ?? project?.id ?? ""))
            .replace("___LEAD_PRODUCT_ID___", String(project?.project_id ?? project?.id ?? ""))
            .replace("___ARTICLE_GROUP_ID___", String(project?.article_group_id ?? ""))
            .replace("___ALT_ID___", String(project?.alternative_id ?? ""))
        : endpoint;

      const params = hasPlaceholders
        ? {}
        : {
            customer_id: project.customer_id ?? "",
            project_id: project.project_id ?? project.id ?? "",
            lead_product_id: project.project_id ?? project.id ?? "",
            article_group_id: project.article_group_id ?? "",
            alternative_id: project.alternative_id ?? "",
          };

      const resp = await WF.httpGet(url, params);
      return (
        (Array.isArray(resp?.plans) && resp.plans) ||
        (Array.isArray(resp?.data) && resp.data) ||
        (Array.isArray(resp?.planner_plans) && resp.planner_plans) ||
        []
      );
    };

    const renderPlans = (plans) => {
      const sel = document.getElementById("plan-selector");
      if (!sel) return;

      sel.innerHTML = "";

      const ph = document.createElement("option");
      ph.value = "";
      ph.textContent = "Plan wählen…";
      sel.appendChild(ph);

      plans.forEach((p) => {
        const opt = document.createElement("option");
        opt.value = String(p.id);
        opt.textContent = planLabel(p);
        sel.appendChild(opt);
      });

      const sep = document.createElement("option");
      sep.value = "__sep__";
      sep.textContent = "────────────";
      sep.disabled = true;
      sel.appendChild(sep);

      const create = document.createElement("option");
      create.value = "__new__";
      create.textContent = "➕ Create new plan";
      sel.appendChild(create);

      sel.disabled = false;
    };

    WF._isHandlingPlanChange = false;

    WF.handlePlanChange = async () => {
      if (WF._isHandlingPlanChange) return;
      WF._isHandlingPlanChange = true;

      try {
        const sel = document.getElementById("plan-selector");
        if (!sel) return;

        const val = String(sel.value || "");

        if (!val || val === "__sep__") {
          WF.state.planId = null;
          if (WF.sidebar?.refresh) WF.sidebar.refresh();
          return;
        }

        if (val === "__new__") {
          WF.state.planId = null;
          if (typeof window.openPlanWizard === "function") window.openPlanWizard();
          if (WF.sidebar?.refresh) WF.sidebar.refresh();
          return;
        }

        WF.state.planId = Number(val);
        if (WF.sidebar?.refresh) WF.sidebar.refresh();
      } finally {
        WF._isHandlingPlanChange = false;
      }
    };

    WF.loadPlansForSelectedProject = async () => {
      WF.resetPlanSelect();
      const project = WF.state.project;
      if (!project?.project_id && !project?.id) return;

      const plans = await fetchPlansForProject(project);

      WF._plansById = {};
      plans.forEach((p) => (WF._plansById[String(p.id)] = p));
      WF._lastPlansList = plans;

      renderPlans(plans);

      const sel = document.getElementById("plan-selector");
      if (sel && !sel.dataset.bound) {
        sel.dataset.bound = "1";
        sel.addEventListener("change", () => WF.handlePlanChange());
      }
    };
  })();

  /* ============================================================
   * 5) SIDEBAR (Backlog) Loader ✅ FIX: loads into your 4 tab panels
   *     - Phase -> #checklist-source
   *     - Personal -> #personal-task-source
   *     - Appointment -> #appointment-source
   *     - Ticket -> #ticket-source
   * ============================================================ */
  (() => {
    const BASE =
      document.querySelector('meta[name="planner-base-url"]')?.getAttribute("content") ||
      (window.location.origin + "/planner");

    const ep = (path) => String(BASE).replace(/\/$/, "") + path;

    const els = {
      title: document.getElementById("wf-sidebar-title"),
      count: document.getElementById("task-count"),
      search: document.getElementById("task-search"),

      tabBtns: Array.from(document.querySelectorAll(".wf-tab")),
      panels: {
        phase: document.getElementById("wf-tab-phase"),
        personal_task: document.getElementById("wf-tab-personal_task"),
        appointment: document.getElementById("wf-tab-appointment"),
        ticket: document.getElementById("wf-tab-ticket"),
      },

      lists: {
        phase: document.getElementById("checklist-source"),
        personal_task: document.getElementById("personal-task-source"),
        appointment: document.getElementById("appointment-source"),
        ticket: document.getElementById("ticket-source"),
      },
    };

    const escapeHtml = (s) =>
      String(s ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

    const ctx = () => {
      const customer_id = WF.state.customer?.id ? Number(WF.state.customer.id) : null;
      const project = WF.state.project || null;

      return {
        customer_id,
        project_id: project?.project_id ? Number(project.project_id) : null,          // lead_product_lists.id
        product_id: project?.article_group_id ? Number(project.article_group_id) : null, // article_groups.id
        alternative_id: project?.alternative_id ? Number(project.alternative_id) : null,
        plan_id: WF.state.planId ? Number(WF.state.planId) : null,
      };
    };

    WF.sidebar = WF.sidebar || {};
    WF.sidebar.state = WF.sidebar.state || {
      tab: "phase",
      search: "",
      data: { phases: null, personal: null, appts: null, tickets: null },
      loading: { phase: false, personal_task: false, appointment: false, ticket: false },
    };

    const setCount = (n) => {
      if (els.count) els.count.textContent = String(n);
    };

    const setTitle = (tab) => {
      if (!els.title) return;
      const map = {
        phase: "Phasen",
        personal_task: "Personal Tasks",
        appointment: "Appointments",
        ticket: "Tickets",
      };
      els.title.textContent = map[tab] || "Backlog";
    };

    const showLoading = (tab, text = "Lade...") => {
      const box = els.lists[tab];
      if (!box) return;
      box.innerHTML = `
        <div class="p-3 rounded-xl bg-white border border-slate-200 text-sm text-slate-500">
          <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>${escapeHtml(text)}
        </div>
      `;
      setCount(0);
    };

    const showEmpty = (tab, text = "Keine Einträge.") => {
      const box = els.lists[tab];
      if (!box) return;
      box.innerHTML = `
        <div class="p-3 rounded-xl bg-white border border-slate-200 text-sm text-slate-500 italic">
          ${escapeHtml(text)}
        </div>
      `;
      setCount(0);
    };

    const showError = (tab, text = "Fehler beim Laden.") => {
      const box = els.lists[tab];
      if (!box) return;
      box.innerHTML = `
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-700">
          <div class="font-bold mb-1">Fehler</div>
          <div class="text-xs">${escapeHtml(text)}</div>
        </div>
      `;
      setCount(0);
    };

    const matchesSearch = (text, q) => {
      const s = String(text || "").toLowerCase();
      const needle = String(q || "").trim().toLowerCase();
      if (!needle) return true;
      return s.includes(needle);
    };

    /* --------------------------
     * Fetchers
     * -------------------------- */
    const fetchPhases = async () => {
      const c = ctx();
      if (!c.customer_id || !c.project_id || !c.product_id) return { ok: true, data: [] };

      // backend: /planner/phases (same as wizard)
      return WF.httpGet(ep("/phases"), {
        customer_id: c.customer_id,
        project_id: c.project_id,
        product_id: c.product_id,
        alternative_id: c.alternative_id || "",
      });
    };

    const fetchPersonal = async () => {
      const c = ctx();
      if (!c.customer_id) return { ok: true, data: [] };

      return WF.httpGet(ep("/personal-tasks"), {
        customer_id: c.customer_id,
        product_id: c.product_id || "",
        alternative_id: c.alternative_id || "",
        limit: 200,
      });
    };

    const fetchAppointments = async () => {
      const c = ctx();
      if (!c.customer_id) return { ok: true, unplanned_appointments: [] };

      return WF.httpGet(ep("/appointments"), {
        customer_id: c.customer_id,
        planned: 0,
        limit: 200,
      });
    };

    const fetchTickets = async () => {
      const c = ctx();
      if (!c.customer_id) return { ok: true, data: [] };

      return WF.httpGet(ep("/problems"), {
        customer_id: c.customer_id,
        product_id: c.product_id || "",
        alternative_id: c.alternative_id || "",
        include_tasks: 1,
        limit: 200,
      });
    };

    /* --------------------------
     * Render helpers
     * -------------------------- */
    const badge = (type) => {
      const map = {
        open: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold">OPEN</span>`,
        planned: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold">PLANNED</span>`,
        done: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold">DONE</span>`,
        ticket: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold">TICKET</span>`,
        appt: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold">APPT</span>`,
        personal: `<span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold">PERSONAL</span>`,
      };
      return map[type] || "";
    };

    const card = ({ title, sub, right, sourceType, sourceId }) => {
      return `
        <div class="glass-card rounded-2xl p-3 border border-slate-200 bg-white cursor-grab active:cursor-grabbing wf-backlog-item"
             data-source-type="${escapeHtml(sourceType)}"
             data-source-id="${escapeHtml(sourceId)}">
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
              <div class="font-extrabold text-slate-800 text-sm truncate">${escapeHtml(title)}</div>
              ${sub ? `<div class="text-xs text-slate-500 mt-1 line-clamp-2">${escapeHtml(sub)}</div>` : ""}
            </div>
            ${right || ""}
          </div>
        </div>
      `;
    };

    const renderPhase = () => {
      const box = els.lists.phase;
      if (!box) return;

      const q = WF.sidebar.state.search;
      const resp = WF.sidebar.state.data.phases;
      const stages = Array.isArray(resp?.data) ? resp.data : [];

      const flat = [];
      for (const st of stages) {
        const stName = st?.stage || "";
        for (const ph of (st?.phases || [])) {
          const phName = ph?.phase_name || "";
          for (const a of (ph?.activities || [])) {
            // show everything in sidebar, but you can filter out planned/done if you want
            const title = a?.title || "Aktivität";
            const desc = a?.description || a?.notes || "";
            const isDone = !!a?.is_done;
            const isPlanned = !!a?.is_planned;

            const metaText = [stName, phName].filter(Boolean).join(" · ");
            const sub = [metaText, desc].filter(Boolean).join(" — ");

            if (!matchesSearch(`${title} ${sub}`, q)) continue;

            flat.push({
              id: Number(a?.id || 0),
              title,
              sub,
              isDone,
              isPlanned,
            });
          }
        }
      }

      if (!flat.length) return showEmpty("phase", q ? "Keine Treffer." : "Keine Phasen / Aktivitäten.");

      box.innerHTML = flat
        .map((a) =>
          card({
            title: a.title,
            sub: a.sub,
            right: a.isDone ? badge("done") : a.isPlanned ? badge("planned") : badge("open"),
            sourceType: "phase_activity",
            sourceId: a.id,
          })
        )
        .join("");

      setCount(flat.length);
      initBacklogDrag(box);
    };

    const renderPersonal = () => {
      const box = els.lists.personal_task;
      if (!box) return;

      const q = WF.sidebar.state.search;
      const resp = WF.sidebar.state.data.personal;
      const items = Array.isArray(resp?.data) ? resp.data : [];

      const rows = items
        .map((t) => {
          const id = Number(t?.id || 0);
          const title = t?.task_title || t?.title || `Personal Task #${id}`;
          const sub = [t?.task_status, t?.due_date].filter(Boolean).join(" · ");
          return { id, title, sub };
        })
        .filter((r) => r.id && matchesSearch(`${r.title} ${r.sub}`, q));

      if (!rows.length) return showEmpty("personal_task", q ? "Keine Treffer." : "Keine Personal Tasks.");

      box.innerHTML = rows
        .map((r) =>
          card({
            title: r.title,
            sub: r.sub,
            right: badge("personal"),
            sourceType: "personal_task",
            sourceId: r.id,
          })
        )
        .join("");

      setCount(rows.length);
      initBacklogDrag(box);
    };

    const renderAppointments = () => {
      const box = els.lists.appointment;
      if (!box) return;

      const q = WF.sidebar.state.search;
      const resp = WF.sidebar.state.data.appts;
      const items = Array.isArray(resp?.unplanned_appointments) ? resp.unplanned_appointments : [];

      const rows = items
        .map((a) => {
          const id = Number(a?.id || 0);
          const title = a?.name || a?.title || `Termin #${id}`;
          const dt = [a?.start_date, a?.start_time].filter(Boolean).join(" ");
          const addr = a?.address?.full_address || a?.address?.street || "";
          const sub = [dt, addr].filter(Boolean).join(" · ");
          return { id, title, sub };
        })
        .filter((r) => r.id && matchesSearch(`${r.title} ${r.sub}`, q));

      if (!rows.length) return showEmpty("appointment", q ? "Keine Treffer." : "Keine Appointments.");

      box.innerHTML = rows
        .map((r) =>
          card({
            title: r.title,
            sub: r.sub,
            right: badge("appt"),
            sourceType: "appointment",
            sourceId: r.id,
          })
        )
        .join("");

      setCount(rows.length);
      initBacklogDrag(box);
    };

    const renderTickets = () => {
      const box = els.lists.ticket;
      if (!box) return;

      const q = WF.sidebar.state.search;
      const resp = WF.sidebar.state.data.tickets;
      const tickets = Array.isArray(resp?.data) ? resp.data : [];

      // show ticket tasks (preferred for planning)
      const rows = [];
      for (const t of tickets) {
        const tid = Number(t?.id || 0);
        const ticketNo = t?.ticket_no ? `Ticket #${t.ticket_no}` : `Ticket #${tid}`;
        const tasks = Array.isArray(t?.tasks?.data) ? t.tasks.data : [];

        for (const task of tasks) {
          const id = Number(task?.id || 0);
          const title = task?.title || "Ticket Aufgabe";
          const sub = [ticketNo, task?.priority, task?.due_date].filter(Boolean).join(" · ");
          if (!id) continue;
          if (!matchesSearch(`${title} ${sub}`, q)) continue;
          rows.push({ id, title, sub });
        }

        // if no tasks, allow dragging the ticket itself
        if (!tasks.length && tid) {
          const title = ticketNo;
          const sub = [t?.priority, t?.status, t?.texts?.problem].filter(Boolean).join(" · ");
          if (matchesSearch(`${title} ${sub}`, q)) rows.push({ id: tid, title, sub, isTicket: true });
        }
      }

      if (!rows.length) return showEmpty("ticket", q ? "Keine Treffer." : "Keine Tickets / Aufgaben.");

      box.innerHTML = rows
        .map((r) =>
          card({
            title: r.title,
            sub: r.sub,
            right: badge("ticket"),
            sourceType: r.isTicket ? "ticket" : "ticket_task",
            sourceId: r.id,
          })
        )
        .join("");

      setCount(rows.length);
      initBacklogDrag(box);
    };

    const renderActive = () => {
      const tab = WF.sidebar.state.tab;
      setTitle(tab);

      if (tab === "phase") return renderPhase();
      if (tab === "personal_task") return renderPersonal();
      if (tab === "appointment") return renderAppointments();
      if (tab === "ticket") return renderTickets();
    };

    /* --------------------------
     * SortableJS (drag from backlog)
     * -------------------------- */
    const initBacklogDrag = (container) => {
      if (!container || !window.Sortable) return;
      if (container.dataset.sortableInit === "1") return;
      container.dataset.sortableInit = "1";

      new Sortable(container, {
        group: { name: "wf-backlog", pull: "clone", put: false },
        sort: false,
        animation: 150,
        handle: ".wf-backlog-item",
        draggable: ".wf-backlog-item",
        ghostClass: "sortable-ghost",
        onClone: (evt) => {
          // ensure cloned node keeps source metadata
          evt.clone.classList.add("ring-2", "ring-sky/30");
        },
      });
    };

    /* --------------------------
     * Loading orchestrator
     * -------------------------- */
    WF.sidebar.refresh = async () => {
      const c = ctx();
      const tab = WF.sidebar.state.tab;

      // If no context, show friendly empty
      if (!c.customer_id) {
        showEmpty(tab, "Bitte zuerst einen Kunden auswählen.");
        return;
      }
      if (!c.project_id || !c.product_id) {
        showEmpty(tab, "Bitte zuerst ein Produkt/Objekt auswählen.");
        return;
      }

      // load ONLY what is needed + keep cache
      try {
        if (tab === "phase") {
          WF.sidebar.state.loading.phase = true;
          showLoading("phase", "Lade Phasen…");
          WF.sidebar.state.data.phases = await fetchPhases();
          WF.sidebar.state.loading.phase = false;
        }

        if (tab === "personal_task") {
          WF.sidebar.state.loading.personal_task = true;
          showLoading("personal_task", "Lade Personal Tasks…");
          WF.sidebar.state.data.personal = await fetchPersonal();
          WF.sidebar.state.loading.personal_task = false;
        }

        if (tab === "appointment") {
          WF.sidebar.state.loading.appointment = true;
          showLoading("appointment", "Lade Appointments…");
          WF.sidebar.state.data.appts = await fetchAppointments();
          WF.sidebar.state.loading.appointment = false;
        }

        if (tab === "ticket") {
          WF.sidebar.state.loading.ticket = true;
          showLoading("ticket", "Lade Tickets…");
          WF.sidebar.state.data.tickets = await fetchTickets();
          WF.sidebar.state.loading.ticket = false;
        }

        renderActive();
      } catch (e) {
        showError(tab, e?.message || String(e));
      }
    };

    const switchTab = (tab) => {
      WF.sidebar.state.tab = tab;
      WF.sidebar.state.search = els.search?.value || "";

      // buttons
      els.tabBtns.forEach((b) => {
        const is = b.getAttribute("data-tab") === tab;
        b.classList.toggle("bg-slate-900", is);
        b.classList.toggle("text-white", is);
        b.classList.toggle("text-slate-700", !is);
      });

      // panels
      Object.entries(els.panels).forEach(([k, el]) => {
        if (!el) return;
        el.classList.toggle("hidden", k !== tab);
      });

      setTitle(tab);
      WF.sidebar.refresh();
    };

    // bind tab clicks
    els.tabBtns.forEach((b) => {
      if (b.dataset.bound === "1") return;
      b.dataset.bound = "1";
      b.addEventListener("click", () => switchTab(b.getAttribute("data-tab")));
    });

    // bind search
    if (els.search && els.search.dataset.bound !== "1") {
      els.search.dataset.bound = "1";
      els.search.addEventListener("input", () => {
        WF.sidebar.state.search = els.search.value || "";
        renderActive(); // filter locally (no new fetch)
      });
    }

    // initial tab = active button or default
    const initialBtn = els.tabBtns.find((b) => b.classList.contains("bg-slate-900")) || els.tabBtns[0];
    const initialTab = initialBtn?.getAttribute("data-tab") || "phase";
    WF.sidebar.state.tab = initialTab;
    setTitle(initialTab);

    // initial paint
    window.addEventListener("load", () => {
      // show initial empty state until customer/project selected
      showEmpty(initialTab, "Bitte zuerst einen Kunden auswählen.");
    });
  })();

  /* ============================================================
   * 6) Page Boot
   * ============================================================ */
  window.addEventListener("load", () => {
    if (typeof WF.resetProjectSelect === "function") WF.resetProjectSelect();
    if (typeof WF.resetPlanSelect === "function") WF.resetPlanSelect();
  });
})();
</script>

 

<!-- sidebar  -->

<script>
(() => {
  "use strict";

  // ============================================================
  // WF Runtime (single source of truth)
  // ============================================================
  window.__WF = window.__WF || {};
  const WFRT = window.__WF;

  WFRT.$ = WFRT.$ || ((id) => document.getElementById(id));
  const $ = WFRT.$;

  // ============================================================
  // DOM
  // ============================================================
  const MODAL_ID = "task-modal";
  const PANEL_ID = "task-modal-content";

  const modal = $(MODAL_ID);
  const panel = $(PANEL_ID);

  if (!modal || !panel) {
    console.error("[WF] Modal elements not found:", { MODAL_ID, PANEL_ID });
    return;
  }

  // Header fields
  const elActiveId     = $("modal-active-item-id");
  const elTitle        = $("modal-edit-title");
  const elDesc         = $("modal-edit-description");

  const elBadgeSource  = $("modal-badge-source");
  const elBadgeStatus  = $("modal-badge-status");
  const elBadgeDuration= $("modal-badge-duration");

  const elSchedule1    = $("modal-schedule");
  const elAddress      = $("modal-address");

  const elSchedule2    = $("modal-schedule-2");
  const elTravel       = $("modal-travel");

  // Team
  const elAssignees    = $("modal-task-assignees");
  const elCrewWrap     = $("modal-task-crew-select");
  const elCrewBoxes    = $("modal-task-crew-checkboxes");

  // Checklist
  const elSubtasksList = $("modal-subtasks-list");

  // Tabs
  const tabInfo        = $("tab-info");
  const tabChecklist   = $("tab-checklist");
  const tabReport      = $("tab-report");
  const tabHistory     = $("tab-history");

  // ============================================================
  // Helpers
  // ============================================================
  const toInt = (v) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  };

  const safeStr = (v, fallback="—") => {
    const s = (v === null || v === undefined) ? "" : String(v);
    return s.trim() ? s : fallback;
  };

  const normalizeDateString = (v) => {
    if (!v) return "";
    let s = String(v).trim();
    // "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss"
    if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/.test(s)) s = s.replace(" ", "T");
    return s;
  };

  const parseDate = (v) => {
    const s = normalizeDateString(v);
    if (!s) return null;
    const d = new Date(s);
    return Number.isNaN(d.getTime()) ? null : d;
  };

  const fmtDateTime = (d) => {
    if (!d) return "—";
    try {
      return new Intl.DateTimeFormat("de-DE", {
        weekday: "short",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      }).format(d);
    } catch {
      return d.toLocaleString();
    }
  };

  const minutesBetween = (a, b) => {
    if (!a || !b) return 0;
    const ms = b.getTime() - a.getTime();
    return Math.max(0, Math.round(ms / 60000));
  };

  const fmtDuration = (mins) => {
    if (!mins) return "—";
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    if (h <= 0) return `${m} min`;
    if (m <= 0) return `${h} h`;
    return `${h} h ${m} min`;
  };

  const getPlanMeta = () => {
    // expected (examples): WFRT.activePlan, WFRT.planState.plan, WFRT.planState.meta
    return WFRT.activePlan || WFRT.planState?.plan || WFRT.planState?.meta || null;
  };

  const getItems = () => {
    // expected: WFRT.planState.items (DB-loaded)
    const items = WFRT.planState?.items;
    return Array.isArray(items) ? items : [];
  };

  const findItemById = (id) => {
    const n = toInt(id);
    if (!n) return null;
    return getItems().find(it => toInt(it?.id) === n) || null;
  };

  const getActiveItemId = () => toInt(elActiveId?.value);
  const setActiveItemId = (id) => { if (elActiveId) elActiveId.value = String(toInt(id) || ""); };

  const getAllEmployees = () => Array.isArray(WFRT.allEmployees) ? WFRT.allEmployees : [];

  const getAssigneeIds = (item) => {
    // supports: item.assignee_ids OR item.assignees (array of ids/objects)
    if (Array.isArray(item?.assignee_ids)) return item.assignee_ids.map(toInt).filter(Boolean);

    if (Array.isArray(item?.assignees)) {
      return item.assignees.map(a => (typeof a === "object" ? toInt(a.id) : toInt(a))).filter(Boolean);
    }
    return [];
  };

  const setAssigneeIds = (item, ids) => {
    const clean = (Array.isArray(ids) ? ids : []).map(toInt).filter(Boolean);
    item.assignee_ids = clean;
    // keep a normalized assignees array of objects if possible
    const byId = new Map(getAllEmployees().map(e => [toInt(e.id), e]));
    item.assignees = clean.map(id => byId.get(id) || ({ id, name: `#${id}` }));
  };

  const ensurePlanRow = () => {
    // Inject a "Plan:" row into header (below schedule/address)
    const headerInfo = modal.querySelector(".mt-3.text-xs.text-slate-500.space-y-1");
    if (!headerInfo) return null;

    let row = headerInfo.querySelector("#modal-plan-row");
    if (row) return row;

    row = document.createElement("div");
    row.id = "modal-plan-row";
    row.className = "flex items-center gap-2";
    row.innerHTML = `
      <i class="fa-solid fa-layer-group text-slate-400"></i>
      <span class="font-semibold">Plan:</span>
      <span id="modal-plan-title" class="font-bold text-slate-700 truncate">—</span>
    `;
    headerInfo.appendChild(row);
    return row;
  };

  const setText = (el, v) => { if (el) el.textContent = safeStr(v); };

  // ============================================================
  // Rendering
  // ============================================================
  function renderPlanDetails() {
    ensurePlanRow();
    const plan = getPlanMeta();
    const elPlanTitle = $("modal-plan-title");
    if (!elPlanTitle) return;

    if (!plan) {
      elPlanTitle.textContent = "—";
      return;
    }

    const title =
      plan.title ||
      plan.name ||
      (plan.customer_name ? `${plan.customer_name}${plan.project_name ? " / " + plan.project_name : ""}` : "") ||
      (plan.id ? `Plan #${plan.id}` : "—");

    elPlanTitle.textContent = title;
  }

  function renderCrew(item) {
    if (!elAssignees) return;

    const ids = getAssigneeIds(item);
    const employees = getAllEmployees();
    const byId = new Map(employees.map(e => [toInt(e.id), e]));

    elAssignees.innerHTML = "";

    if (!ids.length) {
      const empty = document.createElement("div");
      empty.className = "text-xs text-blue-700/70 font-semibold";
      empty.textContent = "Kein Team zugewiesen.";
      elAssignees.appendChild(empty);
      return;
    }

    ids.forEach((id) => {
      const emp = byId.get(id) || { id, name: `#${id}`, avatar_url: null };

      const chip = document.createElement("div");
      chip.className = "flex items-center gap-2 bg-white border border-blue-200 rounded-full px-2 py-1";

      const avatar = document.createElement("div");
      avatar.className = "w-7 h-7 rounded-full bg-blue-100 overflow-hidden flex items-center justify-center text-[11px] font-bold text-blue-800";
      if (emp.avatar_url) {
        const img = document.createElement("img");
        img.src = emp.avatar_url;
        img.alt = emp.name || "";
        img.className = "w-full h-full object-cover";
        avatar.appendChild(img);
      } else {
        avatar.textContent = (emp.name || "—").trim().slice(0, 1).toUpperCase();
      }

      const name = document.createElement("div");
      name.className = "text-xs font-bold text-blue-900";
      name.textContent = emp.name || `#${id}`;

      chip.appendChild(avatar);
      chip.appendChild(name);
      elAssignees.appendChild(chip);
    });
  }

  function renderCrewEditor(item) {
    if (!elCrewBoxes) return;

    const ids = new Set(getAssigneeIds(item));
    const employees = getAllEmployees();

    elCrewBoxes.innerHTML = "";

    if (!employees.length) {
      const empty = document.createElement("div");
      empty.className = "col-span-2 text-xs text-blue-700/70 font-semibold";
      empty.textContent = "Keine Mitarbeiter geladen.";
      elCrewBoxes.appendChild(empty);
      return;
    }

    employees.forEach((emp) => {
      const id = toInt(emp.id);
      const wrap = document.createElement("label");
      wrap.className = "flex items-center gap-2 bg-white rounded-lg border border-blue-200 px-3 py-2 cursor-pointer";

      const cb = document.createElement("input");
      cb.type = "checkbox";
      cb.checked = ids.has(id);
      cb.className = "accent-blue-600";

      cb.addEventListener("change", () => {
        const current = new Set(getAssigneeIds(item));
        if (cb.checked) current.add(id);
        else current.delete(id);

        setAssigneeIds(item, Array.from(current));
        renderCrew(item);

        // optional backend hook
        if (typeof WFRT.persistPlanItemCrewUpdate === "function") {
          WFRT.persistPlanItemCrewUpdate(item).catch?.((err) => console.warn("[WF] crew update failed", err));
        }
        if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
      });

      const txt = document.createElement("div");
      txt.className = "text-xs font-bold text-blue-900 truncate";
      txt.textContent = emp.name || `#${id}`;

      wrap.appendChild(cb);
      wrap.appendChild(txt);

      elCrewBoxes.appendChild(wrap);
    });
  }

  function renderChecklist(item) {
    if (!elSubtasksList) return;

    // ✅ requirement: show current job description with time/date inside checklist tab
    // inject a job info panel at top of tab-checklist (once)
    if (tabChecklist) {
      let info = tabChecklist.querySelector("#modal-checklist-jobinfo");
      if (!info) {
        info = document.createElement("div");
        info.id = "modal-checklist-jobinfo";
        info.className = "bg-slate-50 border border-slate-200 rounded-xl p-3";
        tabChecklist.insertBefore(info, tabChecklist.firstChild);
      }

      const start = parseDate(item?.planned_start_at || item?.start || item?.date_from);
      const end   = parseDate(item?.planned_end_at || item?.end || item?.date_to);
      const scheduleTxt = (start || end)
        ? `${fmtDateTime(start)}${end ? " → " + fmtDateTime(end) : ""}`
        : safeStr(item?.schedule_text || "");

      info.innerHTML = `
        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Aktueller Job</div>
        <div class="mt-1 text-sm font-bold text-slate-900 truncate">${safeStr(item?.title || item?.name || "—")}</div>
        <div class="mt-1 text-xs text-slate-600">
          <span class="font-semibold">Zeit:</span>
          <span class="font-bold text-slate-800">${safeStr(scheduleTxt)}</span>
        </div>
        <div class="mt-1 text-xs text-slate-600">
          <span class="font-semibold">Beschreibung:</span>
          <span class="font-bold text-slate-800">${safeStr(item?.description || "—")}</span>
        </div>
      `;
    }

    const subs = Array.isArray(item?.subtasks) ? item.subtasks : [];
    if (!subs.length) {
      elSubtasksList.innerHTML = `<div class="text-xs text-slate-500">—</div>`;
      return;
    }

    elSubtasksList.innerHTML = "";
    subs.forEach((s, idx) => {
      const done = !!s?.done;
      const row = document.createElement("div");
      row.className = "flex items-center justify-between gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2";

      const left = document.createElement("div");
      left.className = "flex items-center gap-2 min-w-0";

      const cb = document.createElement("input");
      cb.type = "checkbox";
      cb.checked = done;
      cb.className = "accent-blue-600";
      cb.addEventListener("change", () => {
        item.subtasks[idx].done = cb.checked;

        if (typeof WFRT.persistPlanItemUpdate === "function") {
          WFRT.persistPlanItemUpdate(item).catch?.((err) => console.warn("[WF] subtask update failed", err));
        }
        if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
      });

      const text = document.createElement("div");
      text.className = "text-xs font-bold text-slate-800 truncate";
      text.textContent = safeStr(s?.title || s?.text || `Schritt ${idx + 1}`);

      left.appendChild(cb);
      left.appendChild(text);

      const del = document.createElement("button");
      del.className = "text-xs font-bold text-red-600 hover:text-red-700";
      del.innerHTML = `<i class="fa-solid fa-trash"></i>`;
      del.addEventListener("click", () => {
        item.subtasks.splice(idx, 1);
        renderChecklist(item);

        if (typeof WFRT.persistPlanItemUpdate === "function") {
          WFRT.persistPlanItemUpdate(item).catch?.((err) => console.warn("[WF] subtask delete failed", err));
        }
        if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
      });

      row.appendChild(left);
      row.appendChild(del);
      elSubtasksList.appendChild(row);
    });
  }

  function renderModal(item) {
    renderPlanDetails();

    // title / description
    if (elTitle) elTitle.value = item?.title || item?.name || "";
    if (elDesc)  elDesc.value  = item?.description || "";

    // badges
    setText(elBadgeSource, item?.source || item?.type || "—");
    setText(elBadgeStatus, item?.status || "—");

    // schedule + duration
    const start = parseDate(item?.planned_start_at || item?.start || item?.date_from);
    const end   = parseDate(item?.planned_end_at || item?.end || item?.date_to);

    const scheduleTxt =
      (start || end)
        ? `${fmtDateTime(start)}${end ? " → " + fmtDateTime(end) : ""}`
        : (item?.schedule_text || "—");

    setText(elSchedule1, scheduleTxt);
    setText(elSchedule2, scheduleTxt);

    const mins = (start && end) ? minutesBetween(start, end) : toInt(item?.duration_minutes || 0);
    setText(elBadgeDuration, fmtDuration(mins));

    // address / travel
    setText(elAddress, item?.address || item?.location || "—");
    setText(elTravel, item?.travel || item?.travel_time || "—");

    // team
    renderCrew(item);
    renderCrewEditor(item);

    // checklist
    renderChecklist(item);
  }

  // ============================================================
  // Open / Close (sidebar)
  // ============================================================
  function openTaskModal(itemOrId) {
    const item = (typeof itemOrId === "object" && itemOrId)
      ? itemOrId
      : findItemById(itemOrId);

    if (!item) {
      console.warn("[WF] openTaskModal: item not found", itemOrId);
      return;
    }

    setActiveItemId(item.id);

    // show + animate
    modal.classList.remove("hidden");
    panel.classList.add("translate-x-full");
    panel.getBoundingClientRect(); // reflow
    requestAnimationFrame(() => panel.classList.remove("translate-x-full"));

    // default tab
    switchModalTab("info");

    // render
    renderModal(item);
  }

  function closeModal() {
    panel.classList.add("translate-x-full");
    setTimeout(() => modal.classList.add("hidden"), 220);
  }

  // ============================================================
  // Tabs
  // ============================================================
  function setTab(btnId, tabEl, active) {
    const btn = $(btnId);
    if (btn) {
      btn.classList.toggle("active", active);
      btn.classList.toggle("font-bold", active);
      btn.classList.toggle("font-medium", !active);
      btn.classList.toggle("text-brandDark", active);
      btn.classList.toggle("text-slate-500", !active);
      btn.classList.toggle("border-brandDark", active);
      btn.classList.toggle("border-transparent", !active);
      btn.classList.toggle("border-b-2", active);
    }
    if (tabEl) tabEl.classList.toggle("hidden", !active);
    if (tabEl) tabEl.classList.toggle("active", active);
  }

  function switchModalTab(key) {
    setTab("tab-btn-info",      tabInfo,      key === "info");
    setTab("tab-btn-checklist", tabChecklist, key === "checklist");
    setTab("tab-btn-report",    tabReport,    key === "report");
    setTab("tab-btn-history",   tabHistory,   key === "history");

    // re-render checklist header info when switching
    if (key === "checklist") {
      const it = findItemById(getActiveItemId());
      if (it) renderChecklist(it);
    }
  }

  // ============================================================
  // Crew editor toggle
  // ============================================================
  function toggleTaskCrewEditor() {
    if (!elCrewWrap) return;
    elCrewWrap.classList.toggle("hidden");
    const it = findItemById(getActiveItemId());
    if (it) renderCrewEditor(it);
  }

  // ============================================================
  // CRUD: Save / Delete / Add subtask
  // ============================================================
  function saveActiveTask() {
    const id = getActiveItemId();
    const it = findItemById(id);
    if (!it) return;

    // update local state
    it.title = (elTitle?.value || "").trim();
    it.description = (elDesc?.value || "").trim();

    // re-render
    renderModal(it);

    // optional backend hook
    if (typeof WFRT.persistPlanItemUpdate === "function") {
      Promise.resolve(WFRT.persistPlanItemUpdate(it))
        .catch((err) => console.warn("[WF] persist update failed", err));
    }
    if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
  }

 /* ============================================================
 * Helper: Custom Confirmation Modal
 * ============================================================ */
function customConfirm(title, message) {
    return new Promise((resolve) => {
        const modal = document.getElementById('wf-confirm-modal');
        const titleEl = document.getElementById('wf-confirm-title');
        const msgEl = document.getElementById('wf-confirm-msg');
        const btnYes = document.getElementById('wf-confirm-yes');
        const btnCancel = document.getElementById('wf-confirm-cancel');
        const bg = document.getElementById('wf-confirm-bg');

        if (!modal) return resolve(window.confirm(message)); // Fallback

        if (title) titleEl.textContent = title;
        if (message) msgEl.textContent = message;

        modal.classList.remove('hidden');

        const cleanup = () => {
            modal.classList.add('hidden');
            btnYes.replaceWith(btnYes.cloneNode(true));
            btnCancel.replaceWith(btnCancel.cloneNode(true));
            bg.replaceWith(bg.cloneNode(true));
        };

        const handleYes = () => {
            cleanup();
            resolve(true);
        };

        const handleNo = () => {
            cleanup();
            resolve(false);
        };

        // Re-query elements after clone replacement to attach listeners
        document.getElementById('wf-confirm-yes').addEventListener('click', handleYes);
        document.getElementById('wf-confirm-cancel').addEventListener('click', handleNo);
        document.getElementById('wf-confirm-bg').addEventListener('click', handleNo);
    });
}

/* ============================================================
 * Updated Delete Function (Real-time + Custom Modal)
 * ============================================================ */
async function deleteActiveTask() {
    const id = getActiveItemId();
    // findItemById is a helper in your script that searches WFRT.planState.items
    const it = typeof findItemById === 'function' ? findItemById(id) : null; 
    
    if (!it) {
        // Fallback if item isn't in main state but sidebar is open
        closeModal();
        return;
    }

    // 1. Open Custom Modal
    const confirmed = await customConfirm(
        "Aufgabe löschen?", 
        `Möchten Sie "${it.title || 'dieses Element'}" wirklich aus dem Plan entfernen?`
    );

    if (!confirmed) return;

    // 2. Immediate UI Update (Optimistic)
    closeModal(); // Close the sidebar immediately
    
    // Remove from Board/List DOM immediately
    const domElements = document.querySelectorAll(`[data-item-id="${id}"]`);
    domElements.forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300); // Animation
    });

    // Remove from Gantt DOM
    const ganttBar = document.querySelector(`.gantt-bar[data-item-id="${id}"]`);
    if(ganttBar) ganttBar.remove();

    // 3. Send Request to Backend
    // Use the endpoints configuration or fallback URL
    const planId = WFRT.state?.planId || WFRT.planState?.activePlanId;
    
    // Fallback if WFRT.api not fully loaded
    const url = WFRT.api?.planItemDelete 
        ? WFRT.api.planItemDelete.replace('___PLAN___', planId).replace('___ITEM___', id)
        : `/planner/plans/${planId}/items/${id}`;

    try {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const json = await res.json();

        if (!res.ok || !json.ok) {
            throw new Error(json.message || "Fehler beim Löschen");
        }

        // 4. Update Global State (WFRT.planState.items)
        if(WFRT.planState && WFRT.planState.items) {
             WFRT.planState.items = WFRT.planState.items.filter(x => String(x.id) !== String(id));
        }

        // 5. Trigger Refresh (to ensure calculations/counts are correct)
        // If you have a debounced refresh, use it, otherwise this keeps state consistent
        if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
        
        // Show success toast
        const toast = document.getElementById("toast");
        if(toast) {
             toast.querySelector('h4').textContent = "Gelöscht";
             toast.querySelector('p').textContent = "Eintrag erfolgreich entfernt.";
             toast.classList.remove("translate-y-20", "opacity-0");
             setTimeout(() => toast.classList.add("translate-y-20", "opacity-0"), 2500);
        }

    } catch (e) {
        console.error(e);
        alert("Löschen fehlgeschlagen: " + e.message);
        // Optional: Reload plan if delete failed to restore item
        if (typeof window.__WF_loadPlanById === "function" && planId) {
            window.__WF_loadPlanById(planId);
        }
    }
}

  function addTaskSubtask() {
    const id = getActiveItemId();
    const it = findItemById(id);
    if (!it) return;

    const title = window.prompt("Schritt hinzufügen:");
    if (!title || !title.trim()) return;

    it.subtasks = Array.isArray(it.subtasks) ? it.subtasks : [];
    it.subtasks.push({ title: title.trim(), done: false });

    renderChecklist(it);

    if (typeof WFRT.persistPlanItemUpdate === "function") {
      Promise.resolve(WFRT.persistPlanItemUpdate(it))
        .catch((err) => console.warn("[WF] persist subtask add failed", err));
    }
    if (typeof WFRT.refreshUI === "function") WFRT.refreshUI();
  }

  // ============================================================
  // Click delegation: open modal from any job card
  // ============================================================
  document.addEventListener("click", (e) => {
    const el = e.target?.closest?.("[data-plan-item-id],[data-item-id],[data-job-id],[data-open-task-modal]");
    if (!el) return;

    // ignore clicks on interactive controls
    if (e.target?.closest?.("button,a,input,select,textarea")) return;

    const id =
      el.getAttribute("data-plan-item-id") ||
      el.getAttribute("data-item-id") ||
      el.getAttribute("data-job-id") ||
      el.getAttribute("data-open-task-modal");

    const n = toInt(id);
    if (n) openTaskModal(n);
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });

  // ============================================================
  // Export globals (must match your HTML onclick handlers)
  // ============================================================
  window.openTaskModal = openTaskModal;
  window.openModal     = openTaskModal;   // compat: your overlay uses closeModal(), some cards use openModal()

  window.closeModal        = closeModal;
  window.switchModalTab    = switchModalTab;

  window.toggleTaskCrewEditor = toggleTaskCrewEditor;

  window.saveActiveTask    = saveActiveTask;
  window.deleteActiveTask  = deleteActiveTask;
  window.addTaskSubtask    = addTaskSubtask;

  // ============================================================
  // Optional: when plan changes, re-render plan title in header
  // Call WFRT.onPlanSelected(meta) from your plan selector code.
  // ============================================================
  WFRT.onPlanSelected = function(planMeta) {
    WFRT.activePlan = planMeta || WFRT.activePlan || null;
    renderPlanDetails();

    // if modal open, refresh current item details too
    if (!modal.classList.contains("hidden")) {
      const it = findItemById(getActiveItemId());
      if (it) renderModal(it);
    }
  };

})();
</script>

 <script>
  
(() => {
  "use strict";

  // -----------------------
  // Runtime + helpers
  // -----------------------
  window.__WF = window.__WF || {};
  const WFRT = window.__WF;

  WFRT.$ = WFRT.$ || ((id) => document.getElementById(id));
  const $ = WFRT.$;

  const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const hasUI = () =>
    WFRT.ui && typeof WFRT.ui.toast === "function" && typeof WFRT.ui.confirm === "function";

  const toast = (type, title, msg, opts) => {
    if (hasUI()) return WFRT.ui.toast(type, title, msg, opts);
    console.log(`[${type}] ${title}: ${msg}`);
  };

  const confirmModal = async (cfg) => {
    if (hasUI()) return WFRT.ui.confirm(cfg);
    return window.confirm(cfg?.message || "Confirm?");
  };

  const escapeHtml = (s) =>
    String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");

  const normalizeTime = (t) => {
    if (!t) return "";
    const s = String(t).trim();
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(s)) return s.slice(0, 5);
    return s;
  };

  const uniqInts = (arr) =>
    Array.from(
      new Set((Array.isArray(arr) ? arr : []).map((x) => parseInt(x, 10)).filter((n) => Number.isFinite(n) && n > 0))
    );

  // -----------------------
  // Endpoints (from Blade config or fallback)
  // -----------------------
  // You pass this from controller as $plannerConfig. Most blades do one of:
  //   window.__WF.config = @json($plannerConfig);
  //   window.plannerConfig = @json($plannerConfig);
  // This loader supports both.
  const CFG =
    WFRT.config ||
    WFRT.plannerConfig ||
    window.plannerConfig ||
    window.__plannerConfig ||
    {};

  const cfgEndpoints = CFG?.endpoints || WFRT.endpoints || {};

  const replaceTpl = (tpl, map) => {
    let out = String(tpl || "");
    Object.keys(map).forEach((k) => {
      out = out.replaceAll(k, String(map[k]));
    });
    return out;
  };

  // Fallbacks (match your routes)
  const endpoint = {
    // GET /planner/plans/{plan}/json
    planJson: (planId) =>
      cfgEndpoints.plansJsonShow
        ? replaceTpl(cfgEndpoints.plansJsonShow, { "___PLAN___": planId })
        : `/planner/plans/${planId}/json`,

    // PATCH /planner/plans/{plan}/items/{item}
    itemUpdate: (planId, itemId) =>
      cfgEndpoints.planItemUpdate
        ? replaceTpl(cfgEndpoints.planItemUpdate, { "___PLAN___": planId, "___ITEM___": itemId })
        : `/planner/plans/${planId}/items/${itemId}`,

    // DELETE /planner/plans/{plan}/items/{item}
    itemDelete: (planId, itemId) =>
      cfgEndpoints.planItemDelete
        ? replaceTpl(cfgEndpoints.planItemDelete, { "___PLAN___": planId, "___ITEM___": itemId })
        : `/planner/plans/${planId}/items/${itemId}`,
  };

  async function api(url, { method = "GET", body = null, headers = {} } = {}) {
    const h = {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
      ...headers,
    };

    const token = csrf();
    if (token) h["X-CSRF-TOKEN"] = token;

    let payload = body;
    if (body && !(body instanceof FormData)) {
      h["Content-Type"] = "application/json";
      payload = JSON.stringify(body);
    }

    const res = await fetch(url, { method, headers: h, body: payload });
    const txt = await res.text();
    let data = null;
    try {
      data = txt ? JSON.parse(txt) : null;
    } catch (_) {
      data = { raw: txt };
    }

    if (!res.ok) {
      const msg = data?.message || `Request failed (${res.status})`;
      const err = new Error(msg);
      err.status = res.status;
      err.data = data;
      throw err;
    }
    return data;
  }

  // -----------------------
  // Sidebar Module
  // -----------------------
  WFRT.itemSidebar = WFRT.itemSidebar || {};

  const S = {
    wrap: "wf-item-sidebar",
    close: "wf-item-sidebar-close",
    titleLabel: "wf-item-title-label",
    delBtn: "wf-item-delete-btn",
    saveBtn: "wf-item-save-btn",

    // fields
    fTitle: "wf-item-title",
    fNote: "wf-item-note",
    fDate: "wf-item-date",
    fTime: "wf-item-time",
    fLead: "wf-item-lead_id",
    fStatus: "wf-item-status",

    // crew
    crewList: "wf-crew-list",
    crewSelect: "wf-crew-add-select",
    crewAddBtn: "wf-crew-add-btn",
  };

  const state = {
    open: false,
    planId: null,
    itemId: null,
    payload: null, // last loaded plan payload (data)
    item: null,    // extracted item
    busy: false,
  };

  function sidebarEl() {
    return $(S.wrap);
  }

  function setBusy(on) {
    state.busy = !!on;
    const w = sidebarEl();
    if (!w) return;
    w.classList.toggle("opacity-70", state.busy);
    w.classList.toggle("pointer-events-none", state.busy);
  }

  function openSidebar() {
    const w = sidebarEl();
    if (!w) return;
    w.classList.remove("hidden");
    w.classList.add("flex");
    state.open = true;
  }

  function closeSidebar() {
    const w = sidebarEl();
    if (!w) return;
    w.classList.add("hidden");
    w.classList.remove("flex");
    state.open = false;
    state.planId = null;
    state.itemId = null;
    state.payload = null;
    state.item = null;
  }

  function setHeader(title) {
    const t = $(S.titleLabel);
    if (t) t.textContent = title || "Planner Item";
  }

  function setField(id, v) {
    const el = $(id);
    if (!el) return;
    if ("value" in el) el.value = v ?? "";
    else el.textContent = v ?? "";
  }

  function getField(id) {
    const el = $(id);
    if (!el) return null;
    return "value" in el ? el.value : el.textContent;
  }

  function resolveCurrentPlanId() {
    return (
      state.planId ||
      WFRT.currentPlanId ||
      WFRT?.state?.currentPlanId ||
      WFRT?.plan?.id ||
      WFRT?.state?.plan?.id ||
      null
    );
  }

  function extractCrew(item) {
    // Your plan payload items include:
    //  - lead: {id,...} or null
    //  - members: [{id,...}]
    const lead = item?.lead ? [item.lead] : [];
    const members = Array.isArray(item?.members) ? item.members : [];
    return { lead: item?.lead || null, members, all: [...lead, ...members] };
  }

  function renderCrew(item) {
    const box = $(S.crewList);
    if (!box) return;

    const crew = extractCrew(item);
    const all = crew.all;

    if (!all.length) {
      box.innerHTML = `<div class="text-xs text-slate-500">No crew assigned.</div>`;
      return;
    }

    box.innerHTML = all
      .map((c) => {
        const isLead = crew.lead && String(crew.lead.id) === String(c?.id);
        const avatar = c?.photo_url
          ? `<img src="${escapeHtml(c.photo_url)}" class="w-8 h-8 rounded-2xl object-cover border border-slate-200" alt="">`
          : `<div class="w-8 h-8 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xs font-black">${escapeHtml(
              String(c?.full_name || c?.name || "?").trim().slice(0, 1).toUpperCase()
            )}</div>`;

        return `
          <div class="flex items-center justify-between gap-2 bg-white/80 border border-slate-200 rounded-2xl px-3 py-2">
            <div class="flex items-center gap-2 min-w-0">
              ${avatar}
              <div class="min-w-0">
                <div class="text-sm font-black text-slate-900 truncate">
                  ${escapeHtml(c?.full_name || c?.name || "Unnamed")}
                  ${isLead ? `<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">Lead</span>` : ""}
                </div>
                <div class="text-[11px] text-slate-500 truncate">#${escapeHtml(c?.id ?? "")}</div>
              </div>
            </div>

            ${
              isLead
                ? `<div class="text-[11px] font-bold text-slate-500 px-2 py-1">Lead</div>`
                : `<button type="button"
                      class="px-3 py-2 rounded-2xl text-xs font-black bg-rose-100 text-rose-700 border border-rose-200 hover:brightness-95"
                      data-wf-crew-remove="${escapeHtml(c?.id ?? "")}">
                    Remove
                   </button>`
            }
          </div>
        `;
      })
      .join("");
  }

  function renderItem(item) {
    state.item = item || null;

    setHeader(item?.title || `Item #${item?.id ?? ""}`);

    // Fields
    setField(S.fTitle, item?.title || "");
    setField(S.fNote, item?.description || item?.note || "");

    setField(S.fDate, item?.planned_date || (item?.planned_start_at ? String(item.planned_start_at).slice(0, 10) : "") || "");
    setField(S.fTime, normalizeTime(item?.planned_time || (item?.planned_start_at ? String(item.planned_start_at).slice(11, 16) : "")) || "");

    // lead/status (if your DOM exists)
    setField(S.fLead, item?.lead?.id ?? "");
    setField(S.fStatus, item?.status || "");

    renderCrew(item);
  }

  function extractPlanPayload(data) {
    // Your show() returns: { ok:true, data:{ plan, items, ... } }
    // But script supports a few shapes.
    if (data?.data) return data.data;
    if (data?.ok && data?.data) return data.data;
    return data;
  }

  function findItemInPayload(payload, itemId) {
    const items = Array.isArray(payload?.items) ? payload.items : [];
    return items.find((x) => String(x?.id) === String(itemId)) || null;
  }

  async function loadItem(planId, itemId) {
    if (!planId || !itemId) {
      toast("error", "Missing context", "Need plan_id + item_id to open the sidebar.", { ttl: 6000 });
      return;
    }

    setBusy(true);
    try {
      const data = await api(endpoint.planJson(planId));
      const payload = extractPlanPayload(data);
      const item = findItemInPayload(payload, itemId);

      if (!payload || !item) {
        throw new Error(`Item #${itemId} not found in plan #${planId}.`);
      }

      state.planId = parseInt(planId, 10);
      state.itemId = parseInt(itemId, 10);
      state.payload = payload;

      openSidebar();
      renderItem(item);

      toast("success", "Opened", `Item #${itemId} loaded.`, { ttl: 1200 });
    } catch (e) {
      toast("error", "Load failed", e?.message || String(e), { ttl: 8000 });
      closeSidebar();
    } finally {
      setBusy(false);
    }
  }

  function currentCrewIds(item) {
    const members = Array.isArray(item?.members) ? item.members : [];
    return uniqInts(members.map((m) => m?.id));
  }

  async function saveDetails() {
    if (!state.planId || !state.itemId) return;

    // Build planned_start_at (best-effort)
    const date = String(getField(S.fDate) || "").trim();
    const time = normalizeTime(getField(S.fTime) || "");
    const plannedStartAt = date && time ? `${date} ${time}:00` : (date ? `${date} 08:00:00` : null);

    const payload = {
      title: getField(S.fTitle),
      // your DB uses "description" on planner_items; keep note as fallback too
      description: getField(S.fNote),
      note: getField(S.fNote),

      status: getField(S.fStatus),

      // lead + crew sync (if your updateItem supports it)
      lead_id: getField(S.fLead) ? parseInt(getField(S.fLead), 10) : null,
      crew_ids: currentCrewIds(state.item),

      // date/time convenience (if your backend supports)
      planned_date: date || null,
      planned_time: time || null,
      planned_start_at: plannedStartAt,
    };

    // Clean empty
    Object.keys(payload).forEach((k) => {
      if (payload[k] === null || payload[k] === undefined || payload[k] === "") delete payload[k];
    });

    setBusy(true);
    try {
      const data = await api(endpoint.itemUpdate(state.planId, state.itemId), {
        method: "PATCH",
        body: payload,
      });

      // If backend returns updated item, use it; otherwise refresh from plan json
      const updated = data?.item || data?.planner_item || null;
      if (updated?.id) {
        renderItem({ ...(state.item || {}), ...updated });
      } else {
        await loadItem(state.planId, state.itemId);
      }

      toast("success", "Saved", "Item updated.", { ttl: 1300 });

      if (typeof WFRT.onPlannerItemUpdated === "function") {
        WFRT.onPlannerItemUpdated(state.itemId, updated || state.item);
      }
    } catch (e) {
      toast("error", "Save failed", e?.message || String(e), { ttl: 8000 });
    } finally {
      setBusy(false);
    }
  }

  async function addCrew() {
    if (!state.planId || !state.itemId || !state.item) return;

    const sel = $(S.crewSelect);
    const empId = sel ? parseInt(String(sel.value || "").trim(), 10) : 0;

    if (!empId) {
      toast("warn", "Crew", "Select an employee first.", { ttl: 2200 });
      return;
    }

    const current = currentCrewIds(state.item);
    if (current.includes(empId) || String(state.item?.lead?.id) === String(empId)) {
      toast("info", "Crew", "Already assigned.", { ttl: 1500 });
      return;
    }

    const nextCrew = uniqInts([...current, empId]);

    setBusy(true);
    try {
      const data = await api(endpoint.itemUpdate(state.planId, state.itemId), {
        method: "PATCH",
        body: {
          // 👇 this is the important part (no separate crew routes exist in your route list)
          crew_ids: nextCrew,
          lead_id: state.item?.lead?.id ?? null,
        },
      });

      const updated = data?.item || data?.planner_item || null;
      if (updated?.id) {
        renderItem({ ...(state.item || {}), ...updated });
      } else {
        await loadItem(state.planId, state.itemId);
      }

      toast("success", "Crew added", "Employee assigned.", { ttl: 1200 });
    } catch (e) {
      toast("error", "Crew add failed", e?.message || String(e), { ttl: 8000 });
    } finally {
      setBusy(false);
    }
  }

  async function removeCrew(empId) {
    if (!state.planId || !state.itemId || !state.item) return;

    const ok = await confirmModal({
      title: "Remove crew member",
      message: "Remove this employee from the item?",
      confirmText: "Remove",
      cancelText: "Cancel",
      danger: true,
    });
    if (!ok) return;

    const current = currentCrewIds(state.item);
    const nextCrew = current.filter((id) => String(id) !== String(empId));

    setBusy(true);
    try {
      const data = await api(endpoint.itemUpdate(state.planId, state.itemId), {
        method: "PATCH",
        body: {
          crew_ids: nextCrew,
          lead_id: state.item?.lead?.id ?? null,
        },
      });

      const updated = data?.item || data?.planner_item || null;
      if (updated?.id) {
        renderItem({ ...(state.item || {}), ...updated });
      } else {
        await loadItem(state.planId, state.itemId);
      }

      toast("success", "Removed", "Employee removed.", { ttl: 1200 });
    } catch (e) {
      toast("error", "Remove failed", e?.message || String(e), { ttl: 8000 });
    } finally {
      setBusy(false);
    }
  }

  async function deleteItem() {
    if (!state.planId || !state.itemId) return;

    const ok = await confirmModal({
      title: "Delete planner item",
      message: "This will permanently delete the item. Continue?",
      confirmText: "Delete",
      cancelText: "Cancel",
      danger: true,
    });
    if (!ok) return;

    setBusy(true);
    try {
      await api(endpoint.itemDelete(state.planId, state.itemId), { method: "DELETE" });

      const deletedId = state.itemId;
      toast("success", "Deleted", "Planner item deleted.", { ttl: 1300 });
      closeSidebar();

      if (typeof WFRT.onPlannerItemDeleted === "function") {
        WFRT.onPlannerItemDeleted(deletedId);
      }
    } catch (e) {
      toast("error", "Delete failed", e?.message || String(e), { ttl: 8000 });
    } finally {
      setBusy(false);
    }
  }

  // -----------------------
  // Events wiring
  // -----------------------
  function bindOnce() {
    if (WFRT.itemSidebar.__bound) return;
    WFRT.itemSidebar.__bound = true;

    // Click any planner item to open sidebar
    document.addEventListener("click", (e) => {
      const el = e.target.closest("[data-planner-item-id]");
      if (!el) return;

      const itemId = el.getAttribute("data-planner-item-id");
      if (!itemId) return;

      const planId =
        el.getAttribute("data-planner-plan-id") ||
        el.getAttribute("data-plan-id") ||
        resolveCurrentPlanId();

      if (!planId) {
        toast("error", "Plan missing", "Add data-planner-plan-id on your item DOM or set WFRT.currentPlanId.", {
          ttl: 8000,
        });
        return;
      }

      loadItem(planId, itemId);
    });

    // Close button
    $(S.close)?.addEventListener("click", (e) => {
      e.preventDefault();
      closeSidebar();
    });

    // Save button
    $(S.saveBtn)?.addEventListener("click", (e) => {
      e.preventDefault();
      saveDetails();
    });

    // Delete button
    $(S.delBtn)?.addEventListener("click", (e) => {
      e.preventDefault();
      deleteItem();
    });

    // Add crew button
    $(S.crewAddBtn)?.addEventListener("click", (e) => {
      e.preventDefault();
      addCrew();
    });

    // Crew remove (delegated)
    document.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-wf-crew-remove]");
      if (!btn) return;
      const empId = btn.getAttribute("data-wf-crew-remove");
      if (!empId) return;
      removeCrew(empId);
    });
  }

  // -----------------------
  // Public API
  // -----------------------
  WFRT.itemSidebar.openById = (planId, itemId) => loadItem(planId, itemId);
  WFRT.itemSidebar.close = () => closeSidebar();
  WFRT.itemSidebar.refresh = () => state.planId && state.itemId && loadItem(state.planId, state.itemId);

  document.addEventListener("DOMContentLoaded", bindOnce);
})();
</script>


<script>
(() => {
  "use strict";

  /* ============================================================
   * WF Planner DnD → ASSIGNMENT MODAL ✅
   * - On drop (new sidebar item OR move planned item to another manager)
   *   opens a nice modal:
   *     ✅ PM (single) + Team (multi) with avatars (Select2)
   *     ✅ Date + Time
   * - Uses your active employees endpoint (from __WF_CONFIG / WF.api)
   * - Persists after "Übernehmen" via:
   *     POST /dnd/add   (+ pm_id, crew_ids, planned_date, planned_time)
   *     POST /dnd/move  (+ pm_id, crew_ids, planned_date, planned_time)
   * - Cancel will revert the drag UI cleanly
   * ============================================================ */

  // ------------------ Config ------------------
  const BASE_URL =
    document.querySelector('meta[name="planner-base-url"]')?.getAttribute("content") ||
    (window.location.origin + "/planner");

  const CSRF =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  // WF runtime/state (your app uses __WF)
  window.__WF = window.__WF || {};
  const WF = window.__WF;
  const WFRT = window.__WF;

  // ------------------ Helpers ------------------
  const q = (sel, root = document) => root.querySelector(sel);
  const qa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const escapeHtml = (s) =>
    String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");

  const httpPost = async (path, payload) => {
    const res = await fetch(BASE_URL.replace(/\/$/, "") + path, {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF,
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
      body: JSON.stringify(payload || {}),
    });
    const json = await res.json().catch(() => null);
    if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);
    return json;
  };

  const activePlanId = () =>
    WFRT?.planState?.activePlanId ||
    WF?.state?.planId ||
    Number(q("#plan-selector")?.value || 0) ||
    null;

  function refreshPlannerUI(planId) {
    if (typeof WFRT.reloadActivePlan === "function") return WFRT.reloadActivePlan();
    window.dispatchEvent(new CustomEvent("wf:plan-updated", { detail: { plan_id: planId } }));
  }

  // ------------------ Required data attributes ------------------
  // Sidebar draggable must have:
  //  data-source-type="phase_activity|ticket_task|appointment|personal_task"
  //  data-source-id="123"
  //
  // Planned board item must have:
  //  data-planner-item-id="999" (or data-item-id / data-task-id)
  //
  // Manager column/list must have:
  //  data-manager-id="EMPLOYEE_ID"

  function normalizePlannerItemId(el) {
    return el?.dataset?.plannerItemId || el?.dataset?.plannerItemId || el?.dataset?.itemId || el?.dataset?.taskId || "";
  }

  function normalizeSource(el) {
    const srcType = el?.dataset?.sourceType || el?.getAttribute("data-source-type");
    const srcId = el?.dataset?.sourceId || el?.getAttribute("data-source-id");
    return {
      source_type: srcType ? String(srcType) : "",
      source_id: srcId ? Number(srcId) : null,
      title: (el?.dataset?.title || el?.getAttribute("data-title") || el?.innerText || "").trim().slice(0, 255),
    };
  }

  // ------------------ Find droplists (robust) ------------------
  function findManagerDropLists() {
    const board = q("#view-board");
    if (!board) return [];

    const cols = qa("[data-manager-id]", board);

    const lists = cols.map((col) => {
      let list =
        col.querySelector('[data-wf-drop="manager"]') ||
        col.querySelector('[data-wf-list]') ||
        col.querySelector(".wf-list") ||
        col.querySelector(".wf-items") ||
        col.querySelector(".planner-items") ||
        col.querySelector('[data-role="droplist"]');

      if (!list) {
        list =
          col.querySelector(".column-body") ||
          col.querySelector(".body") ||
          col.querySelector(".space-y-2") ||
          col.querySelector(".space-y-3") ||
          col;
      }

      list.dataset.managerId = col.dataset.managerId ?? "";
      return list;
    });

    return Array.from(new Set(lists)).filter(Boolean);
  }

  function findSidebarLists() {
    return [q("#checklist-source"), q("#personal-task-source"), q("#appointment-source"), q("#ticket-source")].filter(Boolean);
  }

  // ------------------ Sortable init ------------------
  function initSortable(listEl, opts) {
    if (!window.Sortable) return;
    if (listEl.dataset.wfSortable === "1") return;
    listEl.dataset.wfSortable = "1";
    new Sortable(listEl, opts);
  }

  // ============================================================
  // EMPLOYEES (Active) + AVATARS (reuse your wizard logic)
  // ============================================================
  const EMP = {
    byId: {},
    loadedOnce: false,
    lastQuery: "",
    lastList: [],
  };

  function resolveEmployeesEndpoint() {
    const cfg = window.__WF_CONFIG?.endpoints || {};
    const fromCfg = cfg?.employeesActive || cfg?.employees_active || cfg?.activeEmployees;
    const fromWF = WF?.api?.employeesActive || WF?.api?.employees_active || WF?.api?.activeEmployees;
    return fromCfg || fromWF || null;
  }

  function employeeFullName(e) {
    const name = [e?.title, e?.name, e?.lastname].filter(Boolean).join(" ").trim();
    return name || (e?.email ? String(e.email) : `#${e?.id}`);
  }

  function initialsFromEmployee(e) {
    const n = String(e?.name || "").trim();
    const l = String(e?.lastname || "").trim();
    const a = (n[0] || "").toUpperCase();
    const b = (l[0] || "").toUpperCase();
    return (a + b) || (String(employeeFullName(e))[0] || "?").toUpperCase();
  }

  function dataSvgAvatar(initials) {
    const safe = String(initials || "?").slice(0, 2);
    const svg =
      `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">
        <defs>
          <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#e2e8f0"/>
            <stop offset="1" stop-color="#cbd5e1"/>
          </linearGradient>
        </defs>
        <rect width="64" height="64" rx="18" fill="url(#g)"/>
        <text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle"
              font-family="Plus Jakarta Sans, Arial, sans-serif"
              font-size="22" font-weight="800" fill="#334155">${safe}</text>
      </svg>`;
    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
  }

  function normalizeAvatarUrl(raw) {
    const v = String(raw || "").trim();
    if (!v) return "";
    if (/^data:image\//i.test(v)) return v;
    if (/^https?:\/\//i.test(v)) return v;
    if (v.startsWith("/")) return v;
    if (v.includes("/")) return `/${v}`;
    return `${BASE_URL.replace(/\/$/, "")}/storage/${v}`;
  }

  function employeeAvatarUrl(e) {
    const maybe =
      e?.photo_url || e?.avatar_url || e?.image_url || e?.profile_image_url ||
      e?.photo || e?.avatar || e?.image || e?.profile_image;

    const u = normalizeAvatarUrl(maybe);
    return u || dataSvgAvatar(initialsFromEmployee(e));
  }

  async function fetchEmployeesActive(search = "") {
    const endpoint = resolveEmployeesEndpoint();
    const candidates = [];
    if (endpoint) candidates.push(endpoint);

    // fallbacks (keep your common routes)
    candidates.push(`${BASE_URL.replace(/\/$/, "")}/employees/active`);
    candidates.push(`${BASE_URL.replace(/\/$/, "")}/employees-active`);

    const query = String(search || "").trim();
    let lastErr = null;

    for (const ep of candidates) {
      try {
        const u = new URL(ep, window.location.origin);
        if (query) u.searchParams.set("q", query);

        const res = await fetch(u.toString(), {
          method: "GET",
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
          credentials: "same-origin",
        });

        const json = await res.json().catch(() => null);
        if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);

        const rows = Array.isArray(json?.data) ? json.data : [];
        EMP.lastQuery = query;
        EMP.lastList = rows;
        EMP.loadedOnce = true;

        for (const e of rows) {
          if (!e?.id) continue;
          EMP.byId[String(e.id)] = e;
        }

        return rows;
      } catch (e) {
        lastErr = e;
      }
    }

    throw (lastErr || new Error("Failed loading active employees"));
  }

  // ============================================================
  // NICE MODAL (PM + TEAM + DATE + TIME) ✅
  // ============================================================
  function injectSelect2ZFixOnce() {
    if (document.getElementById("wf-dnd-select2-zfix")) return;
    const st = document.createElement("style");
    st.id = "wf-dnd-select2-zfix";
    st.textContent = `
      .select2-container { z-index: 100000 !important; }
      .select2-dropdown  { z-index: 100001 !important; }
      .select2-container--open { z-index: 100002 !important; }
    `;
    document.head.appendChild(st);
  }

  function ensureDnDAssignModal() {
    if (q("#wf-dnd-assign-modal")) return;

    injectSelect2ZFixOnce();

    const wrap = document.createElement("div");
    wrap.id = "wf-dnd-assign-modal";
    wrap.className = "hidden fixed inset-0 z-[99999]";
    wrap.innerHTML = `
      <div class="absolute inset-0 bg-black/40" data-wf-dnd-close="1"></div>

      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="wf-dnd-assign-card" class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl overflow-hidden">
          <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="font-extrabold text-slate-900">Planung übernehmen</div>
              <div id="wf-dnd-assign-sub" class="text-xs text-slate-500 mt-1 truncate"></div>
            </div>
            <button type="button" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700" data-wf-dnd-close="1">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Projektleiter (PM)</label>
                <select id="wf-dnd-pm" class="w-full"></select>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Team</label>
                <select id="wf-dnd-crew" class="w-full" multiple></select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Datum</label>
                <input id="wf-dnd-date" type="date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Zeit</label>
                <input id="wf-dnd-time" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
              </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
              <div class="text-[11px] text-slate-500">
                Tipp: PM + Team kannst du direkt per Suche auswählen (mit Bildern) 😊
              </div>

              <div class="flex items-center gap-2">
                <button id="wf-dnd-cancel" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold">
                  Abbrechen
                </button>
                <button id="wf-dnd-save" type="button" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold">
                  Übernehmen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(wrap);

    // close handlers
    qa('[data-wf-dnd-close="1"]', wrap).forEach((el) => el.addEventListener("click", closeDnDAssignModal));
    q("#wf-dnd-cancel", wrap)?.addEventListener("click", closeDnDAssignModal);

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const m = q("#wf-dnd-assign-modal");
        if (m && !m.classList.contains("hidden")) closeDnDAssignModal();
      }
    });
  }

  function destroySelect2(el) {
    if (!el || !window.jQuery) return;
    const $el = window.jQuery(el);
    if ($el.data("select2")) {
      try { $el.select2("destroy"); } catch {}
    }
  }

  function select2TemplateEmployee(data) {
    const emp = data?.employee || EMP.byId[String(data?.id || "")] || null;
    if (!emp) return data?.text || "";
    const name = employeeFullName(emp);
    const sub = [emp?.email, emp?.phone].filter(Boolean).join(" • ");
    const img = employeeAvatarUrl(emp);

    return window.jQuery(`
      <div class="flex items-center gap-3">
        <img src="${escapeHtml(img)}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="">
        <div class="min-w-0">
          <div class="text-sm font-bold text-slate-800 truncate">${escapeHtml(name)}</div>
          ${sub ? `<div class="text-xs text-slate-500 truncate">${escapeHtml(sub)}</div>` : ""}
        </div>
      </div>
    `);
  }

  function select2TemplateSelection(data) {
    const emp = data?.employee || EMP.byId[String(data?.id || "")] || null;
    if (!emp) return data?.text || "";
    const name = employeeFullName(emp);
    const img = employeeAvatarUrl(emp);

    return window.jQuery(`
      <div class="flex items-center gap-2">
        <img src="${escapeHtml(img)}" class="w-5 h-5 rounded-full object-cover border border-slate-200" alt="">
        <span class="text-sm font-semibold text-slate-700 truncate">${escapeHtml(name)}</span>
      </div>
    `);
  }

  function initSelect2Employees(el, { multiple = false, placeholder = "Wählen..." } = {}) {
    if (!el || !window.jQuery || !window.jQuery.fn?.select2) return false;

    const $el = window.jQuery(el);
    destroySelect2(el);

    const dropdownParent = window.jQuery(q("#wf-dnd-assign-card") || document.body);

    $el.select2({
      width: "100%",
      placeholder,
      allowClear: true,
      closeOnSelect: !multiple,
      multiple,
      minimumInputLength: 0,
      dropdownParent,

      ajax: {
        delay: 250,
        transport: (params, success, failure) => {
          const term = params?.data?.term || "";
          fetchEmployeesActive(term)
            .then((rows) => {
              const results = rows.map((e) => ({
                id: String(e.id),
                text: employeeFullName(e),
                employee: e,
              }));
              success({ results });
            })
            .catch((err) => failure(err));
        },
        processResults: (data) => data,
      },

      templateResult: (data) => {
        if (!data.id) return data.text;
        if (data.employee?.id) EMP.byId[String(data.employee.id)] = data.employee;
        return select2TemplateEmployee(data);
      },

      templateSelection: (data) => {
        if (!data.id) return data.text || "";
        if (data.employee?.id) EMP.byId[String(data.employee.id)] = data.employee;
        return select2TemplateSelection(data);
      },

      escapeMarkup: (m) => m,
    });

    return true;
  }

  function seedOption(selectEl, id, selected = false) {
    if (!selectEl) return;
    const v = id ? String(id) : "";
    if (!v) return;

    let opt = Array.from(selectEl.options || []).find((o) => String(o.value) === v);
    if (!opt) {
      const emp = EMP.byId[v] || null;
      opt = document.createElement("option");
      opt.value = v;
      opt.textContent = emp ? employeeFullName(emp) : `#${v}`;
      selectEl.appendChild(opt);
    }
    if (selected) opt.selected = true;
  }

  function seedMulti(selectEl, ids = []) {
    if (!selectEl) return;
    (ids || []).forEach((id) => seedOption(selectEl, id, true));
  }

  let DND_MODAL_CTX = null;

  async function openDnDAssignModal({
    title,
    defaultPmId,
    defaultCrewIds,
    defaultDate,
    defaultTime,
    onSave,
    onCancel,
  }) {
    ensureDnDAssignModal();
    DND_MODAL_CTX = { onSave, onCancel };

    // preload employees once (so we can seed names/images immediately)
    if (!EMP.loadedOnce) {
      try { await fetchEmployeesActive(""); } catch {}
    }

    q("#wf-dnd-assign-sub").textContent = String(title || "").trim();

    const pmSel = q("#wf-dnd-pm");
    const crewSel = q("#wf-dnd-crew");
    const dateInp = q("#wf-dnd-date");
    const timeInp = q("#wf-dnd-time");

    // reset
    pmSel.innerHTML = `<option value=""></option>`;
    crewSel.innerHTML = "";

    // seed + init select2 with avatars
    if (defaultPmId) seedOption(pmSel, defaultPmId, true);
    seedMulti(crewSel, defaultCrewIds || []);

    initSelect2Employees(pmSel, { multiple: false, placeholder: "Projektleiter wählen..." });
    initSelect2Employees(crewSel, { multiple: true, placeholder: "Team wählen..." });

    // set values
    if (window.jQuery && window.jQuery(pmSel).data("select2")) window.jQuery(pmSel).val(defaultPmId ? String(defaultPmId) : null).trigger("change.select2");
    else pmSel.value = defaultPmId ? String(defaultPmId) : "";

    const crewVals = (defaultCrewIds || []).map(String);
    if (window.jQuery && window.jQuery(crewSel).data("select2")) window.jQuery(crewSel).val(crewVals).trigger("change.select2");
    else Array.from(crewSel.options).forEach((o) => (o.selected = crewVals.includes(String(o.value))));

    dateInp.value = defaultDate || "";
    timeInp.value = defaultTime || "";

    // bind save (rebind safely)
    const saveBtn = q("#wf-dnd-save");
    saveBtn.onclick = async () => {
      const pm_id = pmSel?.value ? Number(pmSel.value) : null;

      const crew_ids = (window.jQuery && window.jQuery(crewSel).data("select2"))
        ? (window.jQuery(crewSel).val() || []).map((x) => Number(x)).filter(Boolean)
        : Array.from(crewSel?.selectedOptions || []).map((o) => Number(o.value)).filter(Boolean);

      const planned_date = dateInp?.value || "";
      const planned_time = timeInp?.value || "";

      try {
        await DND_MODAL_CTX?.onSave?.({ pm_id, crew_ids, planned_date, planned_time });
        closeDnDAssignModal();
      } catch (e) {
        console.error(e);
        alert(e?.message || "Speichern fehlgeschlagen");
      }
    };

    q("#wf-dnd-assign-modal").classList.remove("hidden");
  }

  function closeDnDAssignModal() {
    const modal = q("#wf-dnd-assign-modal");
    if (modal) modal.classList.add("hidden");
    DND_MODAL_CTX = null;
  }

  // ============================================================
  // DnD binding
  // ============================================================ 
    function bindDnD() {
        const planId = activePlanId();
        if (!planId) return;

        const sidebars = findSidebarLists();
        const managerLists = findManagerDropLists();
        if (!managerLists.length) return;

        // Sidebar: clone-only sources
        sidebars.forEach((sb) => {
            initSortable(sb, {
                group: { name: "wf-planner-dnd", pull: "clone", put: false },
                sort: false,
                animation: 150,
                draggable: "[data-source-id][data-source-type]",
                onClone: (evt) => evt.clone.classList.add("wf-cloned"),
            });
        });

        // Manager columns: accept items + reorder
        managerLists.forEach((list) => {
            initSortable(list, {
                group: { name: "wf-planner-dnd", pull: true, put: true },
                animation: 150,
                draggable: "[data-planner-item-id], [data-item-id], [data-task-id], [data-source-id][data-source-type]",

                onAdd: async (evt) => {
                    const toManagerIdRaw = (evt.to?.dataset?.managerId ?? "");
                    const to_manager_id = toManagerIdRaw === "" ? null : Number(toManagerIdRaw);

                    const el = evt.item;
                    // Check if this is an existing planned item (has planner ID)
                    const existingId = normalizePlannerItemId(el);

                    // --- revert helper ---
                    const revert = () => {
                        try {
                            if (evt.from) {
                                evt.from.insertBefore(el, evt.from.children[evt.oldIndex] || null);
                            } else {
                                el.remove();
                            }
                        } catch {}
                    };

                    try {
                        // CASE A: EXISTING ITEM (Manager -> Manager Move)
                        if (existingId) {
                            // 1. Determine if manager actually changed
                            const fromManagerIdRaw = (evt.from?.dataset?.managerId ?? "");
                            const from_manager_id = fromManagerIdRaw === "" ? null : Number(fromManagerIdRaw);
                            const managerChanged = String(from_manager_id || "") !== String(to_manager_id || "");

                            // 2. Perform the move immediately (NO MODAL)
                            const position = evt.newIndex ?? null;

                            // Send request to backend
                            await httpPost("/dnd/move", {
                                plan_id: planId,
                                item_id: Number(existingId),
                                to_manager_id: to_manager_id,
                                position: position,
                                // We do NOT send planned_date/time here, preserving existing values
                            });

                            // 3. Update the UI locally to reflect the change visually without full reload (optional optimization)
                            // For now, we refresh to ensure state consistency
                            refreshPlannerUI(planId);
                            
                            return; // Exit, do not open modal
                        }

                        // CASE B: NEW ITEM (Sidebar -> Manager)
                        // This logic remains the same: Show Modal
                        const src = normalizeSource(el);
                        if (!src.source_type || !src.source_id) {
                            revert();
                            throw new Error("Dragged sidebar item missing data-source-type / data-source-id");
                        }

                        await openDnDAssignModal({
                            title: src.title || `${src.source_type} #${src.source_id}`,
                            defaultPmId: to_manager_id || null,
                            defaultCrewIds: to_manager_id ? [to_manager_id] : [],
                            defaultDate: "",
                            defaultTime: "",
                            onCancel: () => {
                                try { el.remove(); } catch {}
                                closeDnDAssignModal();
                            },
                            onSave: async ({ pm_id, crew_ids, planned_date, planned_time }) => {
                                const resp = await httpPost("/dnd/add", {
                                    plan_id: planId,
                                    to_manager_id,
                                    source_type: src.source_type,
                                    source_id: src.source_id,
                                    title: src.title || null,
                                    pm_id,
                                    crew_ids,
                                    planned_date,
                                    planned_time,
                                });

                                if (resp?.item?.id) {
                                    el.dataset.plannerItemId = String(resp.item.id);
                                    el.removeAttribute("data-source-id");
                                    el.removeAttribute("data-source-type");
                                }

                                refreshPlannerUI(planId);
                            },
                        });

                    } catch (e) {
                        console.error(e);
                        revert();
                        alert(e?.message || "Drag & Drop failed");
                    }
                },

                // onUpdate handles reordering within the SAME list
                onUpdate: async (evt) => {
                    const toManagerIdRaw = (evt.to?.dataset?.managerId ?? "");
                    const manager_id = toManagerIdRaw === "" ? null : Number(toManagerIdRaw);

                    try {
                        const ordered = Array.from(evt.to.children)
                            .map((ch) => normalizePlannerItemId(ch))
                            .filter(Boolean)
                            .map(Number);

                        if (!ordered.length) return;

                        await httpPost("/dnd/order", {
                            plan_id: planId,
                            manager_id,
                            ordered_item_ids: ordered,
                        });

                        // refreshPlannerUI(planId); // Optional: usually reordering doesn't need full refresh
                    } catch (e) {
                        console.error(e);
                        alert(e?.message || "Reorder failed");
                    }
                },
            });
        });
    }
  // ------------------ Boot (observe board) ------------------
  function boot() {
    bindDnD();
    const board = q("#view-board");
    if (!board) return;

    const obs = new MutationObserver(() => bindDnD());
    obs.observe(board, { childList: true, subtree: true });
  }

  window.addEventListener("load", boot);
})();
</script>

 
<script>
    function toggleQuickSider(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        const sider = document.getElementById('quickSider');
        if (sider) {
            sider.classList.toggle('open');
        }
    }

    // Close sider when clicking the close button
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('closeSiderBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('quickSider').classList.remove('open');
            });
        }
        
        // Optional: Close when clicking outside
        document.addEventListener('click', function(e) {
            const sider = document.getElementById('quickSider');
            const trigger = e.target.closest('[onclick="toggleQuickSider(event)"]');
            
            if (sider && sider.classList.contains('open') && !sider.contains(e.target) && !trigger) {
                sider.classList.remove('open');
            }
        });
    });
</script>

<script>
(() => {
    "use strict";

    // --- CONFIG ---
    const API = {
        list: '/planner/notifications/list',
        read: (id) => `/planner/notifications/${id}/read`,
        readAll: '/planner/notifications/mark-all-read'
    };
    
    // --- STATE ---
    const State = {
        notifications: [],
        filter: 'all', // 'all' | 'unread'
    };

    // --- DOM HELPERS ---
    const $ = (id) => document.getElementById(id);
    const escapeHtml = (s) => String(s || "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    
    // Time Ago Helper (e.g. "vor 5 Min")
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " Jahren";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " Monaten";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " Tagen";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " Std.";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " Min.";
        return "Gerade eben";
    }

    // --- RENDERERS ---
    
    // 1. Render a single nice Item
    function renderCard(n) {
        const isRead = !!n.read_at;
        const data = n.data || {};
        
        // Colors & Icons based on Type
        let icon = '<i class="fa-solid fa-info"></i>';
        let iconColor = 'text-blue-600 bg-blue-100';
        
        if (data.type === 'plan_created') {
            icon = '<i class="fa-solid fa-wand-magic-sparkles"></i>';
            iconColor = 'text-purple-600 bg-purple-100';
        } else if (data.type === 'item_assigned') {
            icon = '<i class="fa-solid fa-user-plus"></i>';
            iconColor = 'text-emerald-600 bg-emerald-100';
        } else if (data.type === 'item_moved') {
            icon = '<i class="fa-solid fa-arrow-right-arrow-left"></i>';
            iconColor = 'text-orange-600 bg-orange-100';
        }

        const unreadDot = !isRead 
            ? `<div class="absolute right-4 top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-500 rounded-full border-2 border-white shadow-sm"></div>` 
            : '';

        const cardClass = isRead 
            ? 'bg-white border-transparent opacity-80 hover:opacity-100' 
            : 'bg-blue-50/40 border-blue-200 shadow-sm';

        return `
            <div onclick="window.handleNotificationClick('${n.id}')" 
                 class="relative flex gap-4 p-4 mb-2 rounded-2xl border ${cardClass} cursor-pointer hover:bg-white hover:shadow-md transition-all group">
                
                <div class="flex-shrink-0 w-12 h-12 rounded-2xl ${iconColor} flex items-center justify-center text-lg">
                    ${icon}
                </div>

                <div class="flex-1 min-w-0 pr-4">
                    <div class="flex justify-between items-start">
                        <h4 class="text-sm font-extrabold text-slate-800 truncate mb-0.5">
                            ${escapeHtml(data.title || 'Benachrichtigung')}
                        </h4>
                        <span class="text-[11px] font-semibold text-slate-400 whitespace-nowrap ml-2">
                            ${timeAgo(n.created_at)}
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 leading-snug line-clamp-2">
                        ${escapeHtml(data.message || '')}
                    </p>
                    
                    ${data.plan_id ? `
                    <div class="mt-2 flex items-center gap-2">
                         <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-1 rounded-md border border-slate-200">
                            Plan #${data.plan_id}
                         </span>
                    </div>` : ''}
                </div>
                
                ${unreadDot}
            </div>
        `;
    }

    // 2. Update the Lists
    function updateUI() {
        const badge = $('notification-badge');
        const fullList = $('notification-list-full');
        const miniList = $('notification-list-mini');

        // Filter
        let displayList = State.notifications;
        const unreadCount = State.notifications.filter(n => !n.read_at).length;

        if (State.filter === 'unread') {
            displayList = State.notifications.filter(n => !n.read_at);
        }

        // Badge
        if(badge) {
            badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.toggle('hidden', unreadCount === 0);
        }

        // Mini List (Header Dropdown)
        if(miniList) {
            const top = State.notifications.slice(0, 5);
            miniList.innerHTML = top.length 
                ? top.map(n => renderMiniItem(n)).join('')
                : `<div class="p-4 text-center text-xs text-slate-400">Keine neuen Nachrichten</div>`;
        }

        // Full Modal List
        if(fullList) {
            if(displayList.length === 0) {
                fullList.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-64 text-slate-400">
                        <i class="fa-regular fa-bell-slash text-4xl mb-3 opacity-20"></i>
                        <span class="text-sm font-medium">Keine Benachrichtigungen gefunden.</span>
                    </div>
                `;
            } else {
                fullList.innerHTML = displayList.map(n => renderCard(n)).join('');
            }
        }
    }

    // Mini Item Renderer (Simplified for dropdown)
    function renderMiniItem(n) {
        const isRead = !!n.read_at;
        return `
            <div onclick="window.handleNotificationClick('${n.id}')" 
                 class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer ${isRead ? 'opacity-60' : 'bg-blue-50/30'}">
                <div class="flex justify-between mb-1">
                    <span class="font-bold text-xs text-slate-800 truncate pr-2">${escapeHtml(n.data?.title)}</span>
                    <span class="text-[10px] text-slate-400 shrink-0">${timeAgo(n.created_at)}</span>
                </div>
                <div class="text-xs text-slate-500 line-clamp-1">${escapeHtml(n.data?.message)}</div>
            </div>
        `;
    }

    // --- API CALLS ---
    
    async function fetchNotifications() {
        try {
            const res = await fetch(API.list, {
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                }
            });
            if(res.ok) {
                State.notifications = await res.json();
                updateUI();
            }
        } catch(e) { console.warn("Notif fetch error", e); }
    }

    async function apiPost(url) {
        await fetch(url, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
    }

    // --- GLOBAL ACTIONS ---

    window.toggleNotificationDropdown = () => {
        const el = $('notification-dropdown');
        if(el) el.classList.toggle('hidden');
    };

    window.openFullNotificationModal = () => {
        $('notification-dropdown')?.classList.add('hidden');
        $('notification-full-modal')?.classList.remove('hidden');
        updateUI();
    };

    window.closeFullNotificationModal = () => {
        $('notification-full-modal')?.classList.add('hidden');
    };

    window.setNotifFilter = (type) => {
        State.filter = type;
        
        // Update Buttons
        document.querySelectorAll('.notif-filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-slate-800', 'text-white', 'border-slate-800');
            btn.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
        });
        
        // This is a bit hacky, but robust enough for this snippet:
        // We find the button by the text or onclick handler in a real app, 
        // here relying on event.target if triggered by click
        if(event && event.target) {
            const btn = event.target;
            btn.classList.add('active', 'bg-slate-800', 'text-white', 'border-slate-800');
            btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
        }

        updateUI();
    };

    window.markAllNotificationsRead = async () => {
        State.notifications.forEach(n => n.read_at = new Date().toISOString());
        updateUI();
        await apiPost(API.readAll);
    };

    window.handleNotificationClick = async (id) => {
        const n = State.notifications.find(x => x.id === id);
        if(!n) return;

        // Optimistic Read
        if(!n.read_at) {
            n.read_at = new Date().toISOString();
            updateUI();
            apiPost(API.read(id));
        }

        // Action: Load Plan if ID exists
        if(n.data?.plan_id && window.__WF_loadPlanById) {
            window.closeFullNotificationModal();
            window.toggleNotificationDropdown(); // close mini too
            
            // Set dropdown value visually
            const sel = $('plan-selector');
            if(sel) sel.value = n.data.plan_id;
            
            // Load
            window.__WF_loadPlanById(n.data.plan_id);
            
            // Optional: Open Item Detail
            if(n.data.item_id && window.openTaskModal) {
                 setTimeout(() => window.openTaskModal(n.data.item_id), 600);
            }
        }
    };

    // --- REALTIME LISTENER ---
    // Make this function global so your Reverb/Echo script can call it
    window.addLocalNotification = function(notif) {
        State.notifications.unshift(notif);
        updateUI();
    };

    // --- INIT ---
    window.addEventListener('load', () => {
        fetchNotifications();
        
        // Close dropdown on outside click
        document.addEventListener('click', (e) => {
            const container = $('notification-dropdown-container');
            const dropdown = $('notification-dropdown');
            if(container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });

})();
</script>


<!-- attendance logs;  -->
<script>
(() => {
    "use strict";
    const WF = window.__WF;

    // --- State ---
    const AppState = {
        activeTab: 'planning', // planning | attendance | delegated | history
        currentPlanId: null,
        date: new Date().toISOString().split('T')[0] // today
    };

    // --- DOM Elements ---
    const els = {
        // Tabs content
        tabPlanning: document.getElementById('main-tab-planning'),
        tabAttendance: document.getElementById('main-tab-attendance'),
        tabDelegated: document.getElementById('main-tab-delegated'),
        tabHistory: document.getElementById('main-tab-history'),

        // Nav Buttons
        navPlanning: document.getElementById('nav-planning'),
        navAttendance: document.getElementById('nav-attendance'),
        navDelegated: document.getElementById('nav-delegated'),
        navHistory: document.getElementById('nav-history'),

        // Attendance Specifics
        listPresent: document.getElementById('list-present-container'),
        listAbsent: document.getElementById('list-absent-container'),
        countPresent: document.getElementById('display-count-present'),
        countAbsent: document.getElementById('display-count-absent'),
        badgePresent: document.getElementById('badge-present-count'),
        badgeAbsent: document.getElementById('badge-absent-count'),
    };

    // --- EVENT: Plan Changed ---
    // Listens for your Drag&Drop script updates or Select2 changes
    window.addEventListener("wf:plan-updated", (e) => {
        if (e.detail && e.detail.plan_id) {
            AppState.currentPlanId = e.detail.plan_id;
            // If we are currently on the attendance tab, refresh immediately
            if (AppState.activeTab === 'attendance') {
                fetchAttendanceData();
            }
        }
    });

    // --- TAB SWITCHER LOGIC ---
    window.switchMainTab = function(tabName) {
        AppState.activeTab = tabName;

        // 1. Reset Classes
        ['planning', 'attendance', 'delegated', 'history'].forEach(t => {
            const btn = document.getElementById('nav-' + t);
            const content = document.getElementById('main-tab-' + t);
            
            if (btn) {
                btn.classList.remove('active', 'bg-slate-100', 'text-brandDark');
                btn.classList.add('text-slate-500');
            }
            if (content) {
                content.classList.add('hidden');
                content.classList.remove('flex'); // remove flex display
            }
        });

        // 2. Activate Target
        const activeBtn = document.getElementById('nav-' + tabName);
        const activeContent = document.getElementById('main-tab-' + tabName);

        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-slate-100', 'text-brandDark');
            activeBtn.classList.remove('text-slate-500');
        }
        if (activeContent) {
            activeContent.classList.remove('hidden');
            activeContent.classList.add('flex'); // restore flex layout
        }

        // 3. Logic per Tab
        if (tabName === 'attendance') {
            refreshPlanId();
            fetchAttendanceData();
        } 
        else if (tabName === 'history') {
            // If you still have the history logic elsewhere, trigger it
            if(typeof window.loadHistoryData === 'function') {
                window.loadHistoryData();
            }
        }
    };

    // --- HELPER: Sync Plan ID ---
    function refreshPlanId() {
        const sel = document.getElementById('plan-selector');
        AppState.currentPlanId = WF?.state?.planId || (sel ? sel.value : null);
    }

    // --- API FETCH ---
    async function fetchAttendanceData() {
        if (!AppState.currentPlanId || AppState.currentPlanId === '__new__') {
            renderError("Bitte wählen Sie zuerst einen Plan aus.");
            return;
        }

        renderLoading();

        try {
            // Using your Controller's getHistoryData route
            const url = `/planner/history/daily?plan_id=${AppState.currentPlanId}&date=${AppState.date}`;
            const res = await WF.httpGet(url);

            if (res.ok) {
                updateUI(res);
            } else {
                renderError("Fehler beim Laden der Daten.");
            }
        } catch (e) {
            console.error(e);
            renderError("Verbindung fehlgeschlagen.");
        }
    }

    // --- RENDERERS ---

    function updateUI(data) {
        // 1. Update Counters / Badges
        const present = data.attendance_lists?.present || [];
        const absent = data.attendance_lists?.absent || [];

        const countP = present.length;
        const countA = absent.length;

        // Big Numbers in View
        if (els.countPresent) els.countPresent.innerText = countP;
        if (els.countAbsent) els.countAbsent.innerText = countA;

        // Small Badges in Tab
        if (els.badgePresent) {
            els.badgePresent.innerText = countP;
            els.badgePresent.classList.toggle('hidden', countP === 0);
        }
        if (els.badgeAbsent) {
            els.badgeAbsent.innerText = countA;
            els.badgeAbsent.classList.toggle('hidden', countA === 0);
        }

        // 2. Render Present List
        if (countP > 0) {
            els.listPresent.innerHTML = present.map(emp => {
                const img = emp.photo ? (emp.photo.includes('http') ? emp.photo : '/storage/'+emp.photo) : null;
                const avatar = img 
                    ? `<img src="${img}" class="w-10 h-10 rounded-full object-cover border-2 border-green-500 p-0.5">`
                    : `<div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs border-2 border-green-200">${emp.name[0]}${emp.lastname[0]}</div>`;

                return `
                    <div class="flex items-center gap-4 p-3 bg-white border border-green-100 rounded-xl shadow-sm hover:shadow-md transition-all">
                        ${avatar}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 truncate">${emp.name} ${emp.lastname}</h4>
                            <div class="text-xs text-slate-500 font-medium truncate">${emp.role || 'Team'}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Start</div>
                            <div class="font-mono font-bold text-green-600 text-base">${emp.check_in_time}</div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            els.listPresent.innerHTML = `<div class="text-center p-8 text-slate-400 italic">Niemand ist derzeit eingecheckt.</div>`;
        }

        // 3. Render Absent List
        if (countA > 0) {
            els.listAbsent.innerHTML = absent.map(emp => {
                const img = emp.photo ? (emp.photo.includes('http') ? emp.photo : '/storage/'+emp.photo) : null;
                const avatar = img 
                    ? `<img src="${img}" class="w-10 h-10 rounded-full object-cover grayscale opacity-70">`
                    : `<div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">${emp.name[0]}${emp.lastname[0]}</div>`;

                return `
                    <div class="flex items-center gap-4 p-3 bg-white/60 border border-slate-200 rounded-xl opacity-80 hover:opacity-100 hover:bg-white transition-all">
                        ${avatar}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-slate-600 truncate">${emp.name} ${emp.lastname}</h4>
                            <div class="text-xs text-slate-400 truncate">${emp.role || 'Team'}</div>
                        </div>
                        <span class="px-2 py-1 rounded bg-slate-100 text-slate-500 text-[10px] font-bold">
                            ${emp.status_label || 'Abwesend'}
                        </span>
                    </div>
                `;
            }).join('');
        } else {
            els.listAbsent.innerHTML = `<div class="text-center p-8 text-green-500 font-medium">Alle Mitarbeiter sind anwesend! 🎉</div>`;
        }
    }

    function renderLoading() {
        const loader = `<div class="p-6 flex justify-center text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i></div>`;
        if (els.listPresent) els.listPresent.innerHTML = loader;
        if (els.listAbsent) els.listAbsent.innerHTML = loader;
    }

    function renderError(msg) {
        const errHtml = `<div class="p-6 text-center text-slate-400 italic">${msg}</div>`;
        if (els.listPresent) els.listPresent.innerHTML = errHtml;
        if (els.listAbsent) els.listAbsent.innerHTML = "";
        if (els.countPresent) els.countPresent.innerText = "-";
        if (els.countAbsent) els.countAbsent.innerText = "-";
    }

})();
</script>
</body>
</html>