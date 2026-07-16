<section class="w-1/3 flex flex-col gap-4 h-full">
    <div class="flex flex-col gap-3">
        <div class="flex bg-white/50 rounded-xl p-1 border border-slate-200">
            <button onclick="switchLeftTab('backlog')" id="left-btn-backlog" class="flex-1 py-2 rounded-lg text-sm font-bold bg-white text-slate-800 shadow-sm transition-all">
                Checklist <span class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 rounded-md" id="task-count">0</span>
            </button>
            <button onclick="switchLeftTab('unplanned')" id="left-btn-unplanned" class="flex-1 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-brandDark transition-all">
                Unplanned <span class="ml-1 text-xs bg-orange-100 text-orange-600 px-1.5 rounded-md" id="unplanned-count">0</span>
            </button>
        </div>
        
        <div class="relative">
            <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
            <input type="text" id="task-search" placeholder="Search items..." 
                class="w-full bg-white border-none rounded-2xl py-3 pl-11 pr-4 shadow-sm text-sm focus:ring-2 focus:ring-sky/50 outline-none">
        </div>
    </div>

    <div class="glass-panel flex-1 rounded-[2rem] p-4 overflow-y-auto overflow-x-hidden relative">
        <div class="absolute top-0 left-0 w-full h-4 bg-gradient-to-b from-white/50 to-transparent z-10 pointer-events-none"></div>
        
        <div id="left-tab-backlog" class="flex flex-col gap-3 min-h-[200px] pb-10">
            <div id="checklist-source" class="flex flex-col gap-3"></div>
            <button onclick="addManualTask()" class="mt-4 w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 font-semibold hover:border-sky hover:text-sky hover:bg-sky/5 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i> Add Manual Task
            </button>
        </div>

        <div id="left-tab-unplanned" class="hidden flex flex-col gap-3 min-h-[200px] pb-10">
            <div id="unplanned-source" class="flex flex-col gap-3"></div>
        </div>
    </div>
</section>