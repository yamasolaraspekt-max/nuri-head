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