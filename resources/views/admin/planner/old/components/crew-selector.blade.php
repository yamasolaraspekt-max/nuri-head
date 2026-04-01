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