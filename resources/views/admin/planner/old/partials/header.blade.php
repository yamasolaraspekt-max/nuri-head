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
