  
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

    function deleteActiveTask() {
      const s = window.__WF?.state;
      if (!s?.activeTaskId) return;

      const task = (s.currentTasks || []).find(t => t.id === s.activeTaskId);
      if (!task) return;

      const ask = window.WF?.confirm
        ? window.WF.confirm({ ... })
        : Promise.resolve(confirm(`Delete "${task.title}"?`)); // fallback

      ask.then((ok) => {
        if (!ok) return;

        const next = (s.currentTasks || []).filter(t => t.id !== s.activeTaskId);
        window.__WF.setCurrentTasks(next);
        window.__WF.setActiveTaskId(null);

        window.renderAllViews?.();
        closeModal?.();
        window.showToast?.("Task deleted.");
      });
    }


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


 <script>
(() => {
  "use strict";

  const modal    = () => document.getElementById("wf-confirm");
  const titleEl  = () => document.getElementById("wf-confirm-title");
  const subEl    = () => document.getElementById("wf-confirm-subtitle");
  const msgEl    = () => document.getElementById("wf-confirm-message");
  const detEl    = () => document.getElementById("wf-confirm-details");
  const footerEl = () => document.getElementById("wf-confirm-footer");
  const okBtn    = () => document.getElementById("wf-confirm-ok");
  const cancelBtn= () => document.getElementById("wf-confirm-cancel");
  const xBtn     = () => document.getElementById("wf-confirm-x");
  const backdrop = () => document.getElementById("wf-confirm-backdrop");
  const iconWrap = () => document.getElementById("wf-confirm-icon");

  let resolver = null;

  function close(result=false) {
    const m = modal();
    if (!m) return;
    m.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");

    const ok = okBtn();
    const cancel = cancelBtn();
    ok && (ok.disabled = false);
    cancel && (cancel.disabled = false);

    if (resolver) {
      resolver(result);
      resolver = null;
    }
  }

  function setVariant(variant) {
    const icon = iconWrap();
    const ok = okBtn();
    if (!icon || !ok) return;

    // default: danger
    icon.className = "w-10 h-10 rounded-2xl flex items-center justify-center bg-red-50 text-red-600";
    icon.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i>`;
    ok.className   = "px-4 py-2.5 rounded-xl font-extrabold bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/20";

    if (variant === "info") {
      icon.className = "w-10 h-10 rounded-2xl flex items-center justify-center bg-blue-50 text-blue-600";
      icon.innerHTML = `<i class="fa-solid fa-circle-info"></i>`;
      ok.className   = "px-4 py-2.5 rounded-xl font-extrabold bg-brandDark hover:bg-blue-800 text-white shadow-lg shadow-brandDark/20";
    }

    if (variant === "warning") {
      icon.className = "w-10 h-10 rounded-2xl flex items-center justify-center bg-orange-50 text-orange-600";
      icon.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i>`;
      ok.className   = "px-4 py-2.5 rounded-xl font-extrabold bg-orange-600 hover:bg-orange-700 text-white shadow-lg shadow-orange-600/20";
    }
  }

  /**
   * WF.confirm({
   *  title, subtitle, message,
   *  okText, cancelText,
   *  details, footer,
   *  variant: "danger"|"warning"|"info"
   * })
   */
  function confirmModal(opts={}) {
    const m = modal();
    if (!m) return Promise.resolve(false);

    titleEl()  && (titleEl().textContent = opts.title ?? "Confirm");
    subEl()    && (subEl().textContent   = opts.subtitle ?? "");
    msgEl()    && (msgEl().textContent   = opts.message ?? "Are you sure?");

    const det = detEl();
    if (det) {
      if (opts.details) {
        det.classList.remove("hidden");
        det.textContent = String(opts.details);
      } else {
        det.classList.add("hidden");
        det.textContent = "";
      }
    }

    const f = footerEl();
    if (f) {
      if (opts.footer) {
        f.classList.remove("hidden");
        f.textContent = String(opts.footer);
      } else {
        f.classList.add("hidden");
        f.textContent = "";
      }
    }

    const ok = okBtn();
    const cancel = cancelBtn();

    if (ok) ok.textContent = opts.okText ?? "OK";
    if (cancel) cancel.textContent = opts.cancelText ?? "Cancel";

    setVariant(opts.variant ?? "danger");

    document.body.classList.add("overflow-hidden");
    m.classList.remove("hidden");

    // bind events (one-shot)
    const onOk = () => close(true);
    const onCancel = () => close(false);

    ok?.addEventListener("click", onOk, { once:true });
    cancel?.addEventListener("click", onCancel, { once:true });
    xBtn()?.addEventListener("click", onCancel, { once:true });
    backdrop()?.addEventListener("click", onCancel, { once:true });

    // escape to close
    const onEsc = (e) => {
      if (e.key === "Escape") {
        document.removeEventListener("keydown", onEsc);
        close(false);
      }
    };
    document.addEventListener("keydown", onEsc);

    return new Promise((resolve) => {
      resolver = (result) => {
        document.removeEventListener("keydown", onEsc);
        resolve(result);
      };
    });
  }

  window.WF = window.WF || {};
  window.WF.confirm = confirmModal;
})();
</script>


<script>
(() => {
  "use strict";

  function initProjectSelect2() {
    const el = document.getElementById("project-selector");
    if (!el || !window.jQuery || !jQuery.fn.select2) return;

    const $el = jQuery(el);

    // Re-init safe
    if ($el.hasClass("select2-hidden-accessible")) {
      $el.select2("destroy");
    }

    $el.select2({
      width: "resolve",
      placeholder: "Select Product & Site...",
      allowClear: true,
      dropdownAutoWidth: true,
      // Keep dropdown inside header stacking context:
      dropdownParent: jQuery(el).closest("header"),
    });
  }

  // make callable from your other scripts
  window.initProjectSelect2 = initProjectSelect2;

  window.addEventListener("DOMContentLoaded", () => {
    initProjectSelect2();
  });
})();
</script>