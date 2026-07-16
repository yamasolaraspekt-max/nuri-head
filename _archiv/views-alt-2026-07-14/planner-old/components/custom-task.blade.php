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
