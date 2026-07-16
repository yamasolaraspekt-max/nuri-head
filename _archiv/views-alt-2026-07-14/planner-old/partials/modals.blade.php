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