<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professioneller Mobiler Kalender</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Configure Tailwind for Custom Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#164194',   // Dunkelblau
                            light: '#74b2d4',  // Hellblau
                            accent: '#93c21c', // Limettengrün
                            pale: '#cfe09b',   // Helles Limettengrün
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .animate-slide-up { animation: slideUp 0.3s ease-out forwards; }
        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .tab-active { border-bottom: 2px solid #164194; color: #164194; }
        .tab-inactive { border-bottom: 2px solid transparent; color: #64748b; }
    </style>
@vite(['resources/js/bootstrap.js', 'resources/js/notification.js','resources/js/chat.js'])

</head>
<body class="bg-slate-50 h-screen w-full flex flex-col overflow-hidden text-slate-800">

    <!-- Header Section -->
    <header class="bg-white shadow-sm p-4 z-20 shrink-0 relative">
        <div class="flex justify-between items-center mb-4">
            
            <div class="flex items-center gap-3">
                <!-- Back Button -->
                <a href="{{ url('/') }}" class="p-2 -ml-2 text-slate-400 hover:text-brand-dark hover:bg-slate-100 rounded-full transition-colors" title="Zurück zum Dashboard">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>

                <!-- Employee Dropdown Trigger -->
                <div class="relative">
                    <button onclick="toggleUserDropdown()" class="flex items-center gap-3 focus:outline-none group">
                        <div id="current-user-avatar" class="w-10 h-10 rounded-full bg-brand-light/20 flex items-center justify-center text-brand-dark border border-brand-light/30 overflow-hidden relative">
                           <i data-lucide="loader" class="w-5 h-5 animate-spin"></i>
                        </div>
                        <div class="text-left">
                            <h1 id="current-user-name" class="text-sm font-bold text-slate-800">Laden...</h1>
                            <p class="text-xs text-brand-light font-bold flex items-center gap-1 group-hover:text-brand-dark transition-colors">
                                Team filtern <i data-lucide="chevron-down" class="w-3 h-3"></i>
                            </p>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown" class="absolute top-14 left-0 w-72 bg-white rounded-xl shadow-xl border border-slate-100 hidden overflow-hidden animate-slide-up origin-top-left z-50">
                        <div class="bg-slate-50 px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-500 uppercase">Mitarbeiter wählen</span>
                            <button onclick="toggleUserDropdown()" class="text-brand-dark text-xs font-bold">Fertig</button>
                        </div>
                        <div id="user-list-container" class="py-2 max-h-64 overflow-y-auto">
                            <!-- Injected via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="openFilterModal()" class="p-2 bg-slate-100 rounded-full text-slate-600 hover:bg-slate-200" title="Datumsfilter">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                <button onclick="goToToday()" class="text-xs bg-brand-light/10 text-brand-dark px-3 py-1.5 rounded-full font-bold hover:bg-brand-light/20 transition-colors flex items-center">
                    Heute
                </button>
            </div>
        </div>

        <!-- Dynamic Header Content -->
        <div id="header-nav-container">
            <div class="flex items-center justify-between mb-2">
                <span id="week-display" class="text-xs font-bold text-slate-400 uppercase tracking-wider">KW --</span>
                <div class="flex gap-2">
                    <button onclick="changeWeek(-1)" class="p-1 hover:bg-slate-100 rounded transition-colors"><i data-lucide="chevron-left" class="w-4 h-4 text-slate-400"></i></button>
                    <button onclick="changeWeek(1)" class="p-1 hover:bg-slate-100 rounded transition-colors"><i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i></button>
                </div>
            </div>
            <div id="day-slider" class="flex overflow-x-auto pb-2 gap-3 no-scrollbar snap-x scroll-smooth"></div>
        </div>

        <!-- Range Filter View -->
        <div id="range-filter-display" class="hidden mb-2">
            <div class="bg-brand-light/10 border border-brand-light/30 rounded-lg p-3 flex justify-between items-center">
                <div class="flex items-center gap-2 text-brand-dark">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    <span id="range-text" class="text-sm font-semibold"></span>
                </div>
                <button onclick="clearDateFilter()" class="text-xs text-brand-light hover:text-brand-dark font-bold">Löschen</button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-4 pb-24 relative">
        <h2 id="list-header" class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">Laden...</h2>
        <div id="appointments-list" class="space-y-4"></div>
    </main>

    <!-- FAB -->
    <button onclick="openCreateModal()" class="fixed bottom-6 right-6 w-14 h-14 bg-brand-dark text-white rounded-full shadow-lg shadow-brand-dark/40 flex items-center justify-center active:scale-90 transition-transform z-20 hover:bg-blue-900" title="Neuen Termin erstellen">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- Modal Overlay -->
    <div id="modal-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4 transition-opacity"></div>

<script>
(() => {
  "use strict";

  /* --------------------------------------------------------------------------
   * CONFIG
   * -------------------------------------------------------------------------- */
  const API = {
    employees: "{{ route('mobile.mobile_calendar.employees') }}",
    events: "{{ route('mobile.mobile_calendar.events') }}",
    create: "{{ route('mobile.mobile_calendar.appointments.store') }}",
  };

  const CSRF_TOKEN =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const CUSTOMERS = @json($customers);

  const COLORS = {
    darkBlue: "#164194",
    lightBlue: "#74b2d4",
    lime: "#93c21c",
    paleLime: "#cfe09b",
  };

  /* --------------------------------------------------------------------------
   * STATE
   * -------------------------------------------------------------------------- */
  let employeesList = [];
  let eventsList = [];

  const state = {
    viewMode: "day",
    selectedDate: new Date(),
    rangeStart: null,
    rangeEnd: null,

    selectedEmployeeIds: ["all"],

    // create modal
    selectedCustomerId: "",
    createSelectedEmployees: [],
    createSelectedColor: COLORS.darkBlue,

    // detail modal
    activeModalTab: "details",
  };

  /* --------------------------------------------------------------------------
   * DOM + TEXT HELPERS
   * -------------------------------------------------------------------------- */
  const $ = (id) => document.getElementById(id);

  function safeText(v) {
    return String(v ?? "");
  }

  function escapeHtml(s) {
    return safeText(s)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function normBool(v) {
    return v === true || v === 1 || v === "1" || String(v).toLowerCase() === "true";
  }

  function setLoadingList(msg = "Lade Termine...") {
    const list = $("appointments-list");
    if (!list) return;
    list.innerHTML = `<div class="text-center p-4 text-slate-400">${escapeHtml(msg)}</div>`;
  }

  function ensureIcons() {
    try {
      if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      }
    } catch (_) {}
  }

  /* --------------------------------------------------------------------------
   * DATE HELPERS
   * -------------------------------------------------------------------------- */
  function getWeekNumber(d) {
    const date = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    date.setUTCDate(date.getUTCDate() + 4 - (date.getUTCDay() || 7));
    const yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
    return Math.ceil((((date.getTime() - yearStart.getTime()) / 86400000) + 1) / 7);
  }

  function isSameDay(d1, d2) {
    return (
      d1.getDate() === d2.getDate() &&
      d1.getMonth() === d2.getMonth() &&
      d1.getFullYear() === d2.getFullYear()
    );
  }

  function ymd(date) {
    return date.toISOString().split("T")[0];
  }

  function formatTime(str) {
    if (!str) return "";
    if (String(str).includes("T")) {
      return new Date(str).toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" });
    }
    const [h, m] = String(str).split(":");
    return `${h}:${m}`;
  }

  function formatDateDetails(date) {
    return date.toLocaleDateString("de-DE", { weekday: "long", month: "long", day: "numeric" });
  }

  function pickDesc(ev) {
    return (ev.note ?? ev.description ?? "").toString().trim();
  }

  function shortText(s, max = 160) {
    s = safeText(s).trim();
    if (!s) return "";
    return s.length > max ? s.slice(0, max) + "…" : s;
  }

  function pickMainType(ev) {
    return (ev.appointment_type || ev.appointmentType || ev.type || "event").toString();
  }

  function pickExecType(ev) {
    return (ev.execution_type || ev.executionType || "").toString();
  }

  /* --------------------------------------------------------------------------
   * FETCH
   * -------------------------------------------------------------------------- */
  async function fetchJson(url, options = {}) {
    const res = await fetch(url, options);
    const ct = res.headers.get("content-type") || "";
    const data = ct.includes("application/json") ? await res.json() : await res.text();

    if (!res.ok) {
      const msg =
        typeof data === "object" && data && data.message ? data.message : `HTTP ${res.status}`;
      throw new Error(msg);
    }
    return data;
  }

  async function fetchEmployees() {
    try {
      const data = await fetchJson(API.employees);
      employeesList = Array.isArray(data) ? data : [];

      if (!employeesList.find((e) => String(e.id) === "all")) {
        employeesList.unshift({ id: "all", name: "Alle Mitarbeiter", avatar: null });
      }

      renderHeaderUser();
    } catch (e) {
      console.error("Fehler beim Laden der Mitarbeiter", e);
    }
  }

  async function fetchEvents() {
    try {
      setLoadingList();

      const query = new URLSearchParams({ date: ymd(state.selectedDate) });
      (state.selectedEmployeeIds || []).forEach((id) => query.append("employee_ids[]", id));

      const data = await fetchJson(`${API.events}?${query.toString()}`);
      eventsList = (Array.isArray(data) ? data : []).map((ev) => ({
        ...ev,
        id: String(ev.id),
        startObj: new Date(ev.start),
        endObj: new Date(ev.end),
      }));

      renderAppointments();
    } catch (e) {
      console.error("Fehler beim Laden der Termine", e);
      setLoadingList("Fehler beim Laden der Termine.");
    }
  }

  /* --------------------------------------------------------------------------
   * RENDER: HEADER USER (EMPLOYEE FILTER)
   * -------------------------------------------------------------------------- */
  function renderHeaderUser() {
    const avatarContainer = $("current-user-avatar");
    const nameEl = $("current-user-name");
    const list = $("user-list-container");
    if (!avatarContainer || !nameEl || !list) return;

    const selectedIds = state.selectedEmployeeIds || ["all"];

    if (selectedIds.includes("all")) {
      nameEl.textContent = "Alle Mitarbeiter";
      avatarContainer.innerHTML = '<i data-lucide="users" class="w-5 h-5"></i>';
    } else if (selectedIds.length === 1) {
      const emp = employeesList.find((e) => String(e.id) === String(selectedIds[0]));
      nameEl.textContent = emp ? emp.name : "Unbekannt";
      if (emp?.avatar) {
        avatarContainer.innerHTML = `<img src="${escapeHtml(emp.avatar)}" alt="${escapeHtml(
          emp.name
        )}" class="w-full h-full object-cover">`;
      } else {
        avatarContainer.innerHTML = `<span class="text-xs font-bold">${
          emp ? escapeHtml(emp.name.charAt(0)) : "?"
        }</span>`;
      }
    } else {
      nameEl.textContent = `${selectedIds.length} Ausgewählt`;
      avatarContainer.innerHTML = `<div class="flex items-center justify-center bg-brand-dark text-white w-full h-full font-bold text-xs">${selectedIds.length}</div>`;
    }

    list.innerHTML = employeesList
      .map((e) => {
        const id = String(e.id);
        const isSelected = selectedIds.includes(id);
        const checkboxClass = isSelected
          ? "bg-brand-dark border-brand-dark text-white"
          : "bg-white border-slate-300 text-transparent";

        let avatarHtml = "";
        if (id === "all") avatarHtml = '<i data-lucide="users" class="w-4 h-4 text-slate-500"></i>';
        else if (e.avatar) avatarHtml = `<img src="${escapeHtml(e.avatar)}" class="w-full h-full object-cover">`;
        else avatarHtml = `<span class="text-xs font-bold text-slate-500">${escapeHtml(
          (e.name || "?").charAt(0)
        )}</span>`;

        return `
          <button onclick="toggleEmployee('${escapeHtml(id)}')"
                  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors text-left group">
            <div class="w-5 h-5 rounded border ${checkboxClass} flex items-center justify-center transition-all">
              <i data-lucide="check" class="w-3 h-3"></i>
            </div>
            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 shrink-0">
              ${avatarHtml}
            </div>
            <span class="text-sm font-medium ${isSelected ? "text-brand-dark" : "text-slate-700"}">${escapeHtml(
              e.name
            )}</span>
          </button>
        `;
      })
      .join("");

    ensureIcons();
  }

  /* --------------------------------------------------------------------------
   * RENDER: HEADER VIEW (DAY / RANGE)
   * -------------------------------------------------------------------------- */
  function renderHeaderView() {
    const navContainer = $("header-nav-container");
    const rangeDisplay = $("range-filter-display");
    const listHeader = $("list-header");

    if (!navContainer || !rangeDisplay || !listHeader) return;

    if (state.viewMode === "range" && state.rangeStart && state.rangeEnd) {
      navContainer.classList.add("hidden");
      rangeDisplay.classList.remove("hidden");

      const s = state.rangeStart.toLocaleDateString("de-DE", { month: "short", day: "numeric" });
      const e = state.rangeEnd.toLocaleDateString("de-DE", { month: "short", day: "numeric" });

      const rangeText = $("range-text");
      if (rangeText) rangeText.textContent = `${s} - ${e}`;

      listHeader.textContent = `Termine (${s} - ${e})`;
      return;
    }

    navContainer.classList.remove("hidden");
    rangeDisplay.classList.add("hidden");
    listHeader.textContent = isSameDay(state.selectedDate, new Date())
      ? "Heutiger Zeitplan"
      : formatDateDetails(state.selectedDate);

    renderWeekAndDays();
  }

  function renderWeekAndDays() {
    const weekDisplay = $("week-display");
    const slider = $("day-slider");
    if (!weekDisplay || !slider) return;

    weekDisplay.textContent = `KW ${getWeekNumber(state.selectedDate)}`;
    slider.innerHTML = "";

    const start = new Date(state.selectedDate);
    start.setDate(start.getDate() - start.getDay());
    const initial = new Date(start);
    initial.setDate(initial.getDate() - 7);

    for (let i = 0; i < 21; i++) {
      const day = new Date(initial);
      day.setDate(initial.getDate() + i);

      const isSelected = isSameDay(day, state.selectedDate);
      const isToday = isSameDay(day, new Date());
      const dayNum = day.getDay();
      const isWknd = dayNum === 0 || dayNum === 6;

      let bgClass = isSelected
        ? "bg-brand-dark text-white shadow-lg shadow-brand-dark/30"
        : "bg-white text-slate-600 border-slate-100";

      if (!isSelected && isWknd) bgClass = "bg-brand-accent/10 border-brand-accent/20";

      const div = document.createElement("div");
      div.className =
        `snap-center flex-shrink-0 w-14 h-20 rounded-2xl flex flex-col items-center justify-center cursor-pointer ` +
        `transition-all duration-200 border ${bgClass}`;

      div.onclick = () => selectDate(day);

      div.innerHTML = `
        <span class="text-xs font-medium mb-1 ${
          isSelected ? "text-white/60" : isWknd ? "text-brand-accent" : "text-slate-400"
        }">${escapeHtml(day.toLocaleDateString("de-DE", { weekday: "short" }))}</span>
        <span class="text-lg font-bold ${isSelected ? "text-white" : "text-slate-800"}">${day.getDate()}</span>
        ${isToday && !isSelected ? '<div class="w-1 h-1 bg-brand-dark rounded-full mt-1"></div>' : ""}
      `;

      slider.appendChild(div);

      if (isSelected) {
        setTimeout(() => div.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "center" }), 10);
      }
    }
  }

  /* --------------------------------------------------------------------------
   * RENDER: APPOINTMENTS LIST
   * -------------------------------------------------------------------------- */
  function getFilteredEvents() {
    let filtered = eventsList.filter((ev) => {
      if (state.viewMode === "range" && state.rangeStart && state.rangeEnd) {
        const start = new Date(ev.startObj);
        start.setHours(0, 0, 0, 0);

        const s = new Date(state.rangeStart);
        s.setHours(0, 0, 0, 0);

        const e = new Date(state.rangeEnd);
        e.setHours(0, 0, 0, 0);

        return start >= s && start <= e;
      }

      return isSameDay(ev.startObj, state.selectedDate);
    });

    if (state.viewMode === "range") filtered.sort((a, b) => a.startObj - b.startObj);
    return filtered;
  }

  function renderAppointments() {
    const list = $("appointments-list");
    if (!list) return;

    list.innerHTML = "";
    const filtered = getFilteredEvents();

    if (filtered.length === 0) {
      list.innerHTML = `
        <div class="flex flex-col items-center justify-center h-64 text-slate-400">
          <i data-lucide="calendar" class="w-12 h-12 mb-4 text-slate-300"></i>
          <p>Keine Termine gefunden.</p>
          <button onclick="openCreateModal()"
                  class="mt-4 text-brand-dark font-medium text-sm hover:underline">+ Jetzt einen erstellen</button>
        </div>
      `;
      ensureIcons();
      return;
    }

    filtered.forEach((apt) => {
      const owner = employeesList.find((e) => String(e.id) === String(apt.ownerId));
      const ownerName = owner ? owner.name : "Unbekannt";
      const ownerAvatar =
        owner?.avatar
          ? `<img src="${escapeHtml(owner.avatar)}" class="w-6 h-6 rounded-full border border-slate-200 object-cover">`
          : `<div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500">${escapeHtml(
              ownerName.charAt(0)
            )}</div>`;

      const cardColor = apt.color || COLORS.darkBlue;
      const badgeStyle = `background-color: ${cardColor}20; color: ${cardColor};`;
      const borderStyle = `background-color: ${cardColor};`;

      const mainType = pickMainType(apt);
      const execType = pickExecType(apt);
      const desc = pickDesc(apt);
      const descShort = shortText(desc, 160);

      const dateHeader =
        state.viewMode === "range"
          ? `<div class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">${escapeHtml(
              formatDateDetails(apt.startObj)
            )}</div>`
          : "";

      let attendeesHtml = "";
      if (Array.isArray(apt.attendees) && apt.attendees.length > 0) {
        const maxShow = 3;
        apt.attendees.slice(0, maxShow).forEach((att) => {
          if (att.avatar) {
            attendeesHtml += `<img src="${escapeHtml(att.avatar)}" class="w-6 h-6 rounded-full border border-white shadow-sm -ml-2 first:ml-0 bg-white">`;
          } else {
            attendeesHtml += `<div class="w-6 h-6 rounded-full border border-white shadow-sm bg-slate-200 text-[8px] flex items-center justify-center -ml-2 first:ml-0">${escapeHtml(
              (att.name || "?").charAt(0)
            )}</div>`;
          }
        });

        if (apt.attendees.length > maxShow) {
          attendeesHtml += `<div class="w-6 h-6 rounded-full border border-white shadow-sm bg-slate-100 text-[8px] flex items-center justify-center -ml-2">+${
            apt.attendees.length - maxShow
          }</div>`;
        }
      } else {
        attendeesHtml = '<span class="text-[10px] text-slate-400 italic">Keine weiteren Teilnehmer</span>';
      }

      const wrapper = document.createElement("div");
      wrapper.className = "relative";
      wrapper.innerHTML = `
        ${dateHeader}
        <div class="relative bg-white p-4 rounded-xl shadow-sm border border-slate-100 active:scale-[0.98] transition-transform cursor-pointer overflow-hidden group hover:shadow-md"
             onclick="openDetailModal('${escapeHtml(apt.id)}')">
          <div class="absolute left-0 top-0 bottom-0 w-1" style="${borderStyle}"></div>

          <div class="flex justify-between items-start mb-2 pl-2">
            <div class="flex flex-wrap gap-2">
              <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1 inline-block"
                    style="${badgeStyle}">${escapeHtml(mainType)}</span>

              ${execType ? `
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1 inline-block"
                      style="background-color: ${cardColor}10; color: ${cardColor}; border: 1px solid ${cardColor}25;">
                  ${escapeHtml(execType)}
                </span>
              ` : ""}
            </div>

            ${normBool(apt.needsReport) ? '<i data-lucide="alert-circle" class="w-4 h-4 text-brand-accent"></i>' : ""}
          </div>

          <h3 class="font-semibold text-slate-800 text-sm mb-2 pl-2 leading-tight">${escapeHtml(
            apt.title || ""
          )}</h3>

          ${descShort ? `
            <p class="text-xs text-slate-500 pl-2 mb-2 leading-snug">
              ${escapeHtml(descShort)}
            </p>
          ` : ""}

          <div class="flex flex-col gap-1 mb-3 pl-2">
            <div class="flex items-center gap-2 text-slate-500 text-xs">
              <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
              ${escapeHtml(formatTime(apt.start))} - ${escapeHtml(formatTime(apt.end))}
            </div>
            <div class="flex items-center gap-2 text-slate-400 text-xs">
              <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
              <span class="truncate">${escapeHtml(apt.address || "Kein Ort")}</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-3 border-t border-slate-50 pl-2 mt-1">
            <div class="flex items-center gap-2">
              ${ownerAvatar}
              <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Verantwortlich</span>
                <span class="text-xs font-semibold text-slate-700 truncate max-w-[100px]">${escapeHtml(
                  ownerName
                )}</span>
              </div>
            </div>

            <div class="flex items-center -space-x-2">
              ${attendeesHtml}
            </div>
          </div>
        </div>
      `;
      list.appendChild(wrapper);
    });

    ensureIcons();
  }

  /* --------------------------------------------------------------------------
   * MODAL: DETAILS + REPORT
   * -------------------------------------------------------------------------- */
  function openDetailModal(aptId, defaultTab = "details") {
    const apt = eventsList.find((a) => String(a.id) === String(aptId));
    if (!apt) return;

    state.activeModalTab = defaultTab;

    const overlay = $("modal-overlay");
    if (!overlay) return;
    overlay.classList.remove("hidden");

    const cardColor = apt.color || COLORS.darkBlue;
    const publicBadgeStyle = `background-color: ${cardColor}15; color: ${cardColor};`;
    const owner = employeesList.find((e) => String(e.id) === String(apt.ownerId));

    const phone = apt.phone || apt.customerPhone || "";
    const email = apt.email || apt.customerEmail || "";

    const mainType = pickMainType(apt);
    const execType = pickExecType(apt);
    const desc = pickDesc(apt);

    const mapUrl = apt.address
      ? `https://maps.google.com/maps?q=${encodeURIComponent(apt.address)}&z=15&output=embed`
      : "";

    const attendeesListHtml =
      Array.isArray(apt.attendees) && apt.attendees.length > 0
        ? apt.attendees
            .map((att) => {
              const nm = att.name || "Teammitglied";
              return `
                <div class="flex items-center gap-3 p-2 rounded hover:bg-slate-50">
                  ${
                    att.avatar
                      ? `<img src="${escapeHtml(att.avatar)}" class="w-8 h-8 rounded-full border border-slate-200 object-cover">`
                      : `<div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-300">${escapeHtml(
                          nm.charAt(0)
                        )}</div>`
                  }
                  <div class="flex flex-col">
                    <span class="text-sm font-semibold text-slate-700 leading-tight">${escapeHtml(nm)}</span>
                    <span class="text-[10px] text-slate-400">Teammitglied</span>
                  </div>
                </div>
              `;
            })
            .join("")
        : '<div class="text-sm text-slate-400 italic p-2">Keine weiteren Teilnehmer</div>';

    overlay.innerHTML = `
      <div class="bg-white w-full max-w-md h-[90vh] sm:h-[85vh] sm:rounded-2xl rounded-t-3xl overflow-hidden relative animate-slide-up shadow-2xl flex flex-col">
        <div class="h-48 bg-slate-100 relative shrink-0">
          ${
            mapUrl
              ? `<iframe width="100%" height="100%" frameborder="0" style="border:0" src="${mapUrl}" allowfullscreen></iframe>`
              : `<div class="absolute inset-0 flex items-center justify-center text-slate-400 flex-col gap-2">
                   <i data-lucide="map-off" class="w-8 h-8"></i>
                   <span class="text-xs font-medium">Keine Adresse angegeben</span>
                 </div>`
          }

          <button onclick="closeModal()"
                  class="absolute top-4 right-4 w-8 h-8 bg-black/30 backdrop-blur-md text-white rounded-full flex items-center justify-center hover:bg-black/50 z-10 transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>

          <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 via-black/40 to-transparent text-white">
            <div class="flex justify-between items-end">
              <div>
                <h2 class="text-xl font-bold leading-tight shadow-sm">${escapeHtml(apt.title || "")}</h2>
                <p class="text-xs opacity-90 font-light truncate max-w-[250px]">${escapeHtml(apt.address || "Kein Ort")}</p>
              </div>
              ${owner?.avatar ? `<img src="${escapeHtml(owner.avatar)}" class="w-10 h-10 rounded-full border-2 border-white shadow-md">` : ""}
            </div>
          </div>
        </div>

        <div class="flex border-b border-slate-100 bg-white shrink-0 shadow-sm z-10">
          <button onclick="switchTab('${escapeHtml(apt.id)}', 'details')" id="tab-btn-details"
                  class="flex-1 py-3 text-sm font-semibold text-center transition-colors ${
                    state.activeModalTab === "details" ? "tab-active" : "tab-inactive"
                  }">Details</button>
          <button onclick="switchTab('${escapeHtml(apt.id)}', 'report')" id="tab-btn-report"
                  class="flex-1 py-3 text-sm font-semibold text-center transition-colors ${
                    state.activeModalTab === "report" ? "tab-active" : "tab-inactive"
                  }">Bericht</button>
        </div>

        <div class="flex-1 overflow-y-auto bg-white p-6 relative">

          <!-- DETAILS TAB -->
          <div id="tab-content-details" class="${
            state.activeModalTab === "details" ? "" : "hidden"
          } space-y-6 animate-slide-up">

            <div class="flex gap-2 flex-wrap">
              <span class="px-2 py-1 rounded text-xs font-semibold uppercase" style="${publicBadgeStyle}">
                ${normBool(apt.isPublic) ? "Öffentlich" : "Privat"}
              </span>

              ${
                apt.customerName
                  ? `<span class="px-2 py-1 rounded text-xs font-semibold uppercase bg-brand-light/10 text-brand-dark">
                       Kunde: ${escapeHtml(apt.customerName)}
                     </span>`
                  : ""
              }
            </div>

            <div>
              <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Terminart</h4>
              <div class="flex flex-wrap gap-2">
                <span class="px-2 py-1 rounded text-xs font-semibold uppercase" style="${publicBadgeStyle}">
                  ${escapeHtml(mainType)}
                </span>
                ${execType ? `
                  <span class="px-2 py-1 rounded text-xs font-semibold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                    ${escapeHtml(execType)}
                  </span>
                ` : ""}
              </div>
            </div>

            ${
              phone || email
                ? `
              <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kontakt</h4>
                ${
                  phone
                    ? `<div class="flex items-center gap-2 text-slate-600 text-sm mb-1">
                         <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                         <a href="tel:${escapeHtml(phone)}" class="font-semibold hover:underline">${escapeHtml(phone)}</a>
                       </div>`
                    : ""
                }
                ${
                  email
                    ? `<div class="flex items-center gap-2 text-slate-600 text-sm">
                         <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                         <a href="mailto:${escapeHtml(email)}" class="font-semibold hover:underline">${escapeHtml(email)}</a>
                       </div>`
                    : ""
                }
              </div>
            `
                : ""
            }

            <div>
              <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Beschreibung</h4>
              <p class="text-slate-600 text-sm leading-relaxed">${escapeHtml(desc || "Keine Beschreibung vorhanden.")}</p>
            </div>

            <div class="grid grid-cols-1 gap-3">
              <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-brand-light shadow-sm">
                  <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 font-bold uppercase">Zeit</p>
                  <p class="text-sm font-semibold text-slate-700">${escapeHtml(
                    formatDateDetails(apt.startObj)
                  )}, ${escapeHtml(formatTime(apt.start))}</p>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Beteiligte Personen</h4>
              <div class="space-y-1">${attendeesListHtml}</div>
            </div>

            <button onclick="switchTab('${escapeHtml(apt.id)}', 'report')"
                    class="w-full mt-4 bg-brand-light/10 text-brand-dark py-3.5 rounded-xl font-bold text-sm hover:bg-brand-light/20 transition-colors flex items-center justify-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4"></i> Zum Bericht
            </button>
          </div>

          <!-- REPORT TAB -->
          <div id="tab-content-report" class="${
            state.activeModalTab === "report" ? "" : "hidden"
          } h-full flex flex-col animate-slide-up">
            <label class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Inhalt des Berichts</label>

            <div class="relative flex-1 mb-4">
              <textarea id="inp-report-text"
                        class="w-full h-full p-4 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-dark resize-none text-slate-800 leading-relaxed bg-slate-50 placeholder-slate-400"
                        placeholder="Geben Sie Ihren Bericht hier ein oder nutzen Sie das Mikrofon...">${escapeHtml(
                          apt.reportText || ""
                        )}</textarea>
              <button id="btn-mic" onclick="toggleMic()"
                      class="absolute bottom-4 right-4 w-12 h-12 rounded-full bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-all shadow-md border border-slate-100">
                <i data-lucide="mic" class="w-5 h-5"></i>
              </button>
            </div>

            <button onclick="saveReport('${escapeHtml(apt.id)}')"
                    class="w-full bg-brand-dark text-white py-3.5 rounded-xl font-bold shadow-lg shadow-brand-dark/20 active:scale-[0.98] transition-all hover:bg-blue-900 flex items-center justify-center gap-2 shrink-0">
              <i data-lucide="save" class="w-4 h-4"></i>
              ${String(apt.status || "") === "reported" ? "Bericht aktualisieren" : "Bericht speichern"}
            </button>

            ${
              String(apt.status || "") === "reported"
                ? `<p class="text-center text-xs text-brand-accent mt-2 flex items-center justify-center gap-1">
                     <i data-lucide="check-circle" class="w-3 h-3"></i> Bericht gesendet
                   </p>`
                : ""
            }
          </div>
        </div>
      </div>
    `;

    ensureIcons();
  }

  function switchTab(aptId, tabName) {
    state.activeModalTab = tabName;

    const btnDetails = $("tab-btn-details");
    const btnReport = $("tab-btn-report");
    const contentDetails = $("tab-content-details");
    const contentReport = $("tab-content-report");

    if (!btnDetails || !btnReport || !contentDetails || !contentReport) return;

    if (tabName === "details") {
      btnDetails.className = btnDetails.className.replace("tab-inactive", "tab-active");
      btnReport.className = btnReport.className.replace("tabE", "E"); // no-op safeguard
      btnReport.className = btnReport.className.replace("tab-active", "tab-inactive");
      contentDetails.classList.remove("hidden");
      contentReport.classList.add("hidden");
    } else {
      btnReport.className = btnReport.className.replace("tab-inactive", "tab-active");
      btnDetails.className = btnDetails.className.replace("tab-active", "tab-inactive");
      contentReport.classList.remove("hidden");
      contentDetails.classList.add("hidden");
    }
  }

  /* --------------------------------------------------------------------------
   * SPEECH (REPORT)
   * -------------------------------------------------------------------------- */
  let recognition;
  let isRecording = false;

  if ("webkitSpeechRecognition" in window || "SpeechRecognition" in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = "de-DE";

    recognition.onresult = (event) => {
      let finalTranscript = "";
      for (let i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) finalTranscript += event.results[i][0].transcript;
      }
      const textarea = $("inp-report-text");
      if (textarea && finalTranscript) {
        const spacer = textarea.value.length > 0 && !textarea.value.endsWith(" ") ? " " : "";
        textarea.value += spacer + finalTranscript;
      }
    };

    recognition.onend = () => {
      if (isRecording) {
        isRecording = false;
        updateMicButtonUI();
      }
    };
  }

  function toggleMic() {
    if (!recognition) {
      alert("Spracheingabe wird nicht unterstützt.");
      return;
    }
    if (isRecording) {
      recognition.stop();
      isRecording = false;
    } else {
      recognition.start();
      isRecording = true;
    }
    updateMicButtonUI();
  }

  function updateMicButtonUI() {
    const btn = $("btn-mic");
    if (!btn) return;

    if (isRecording) {
      btn.classList.remove("bg-white", "text-slate-600");
      btn.classList.add("bg-red-500", "text-white", "animate-pulse");
    } else {
      btn.classList.add("bg-white", "text-slate-600");
      btn.classList.remove("bg-red-500", "text-white", "animate-pulse");
    }
  }

  function saveReport(aptId) {
    const textarea = $("inp-report-text");
    const text = textarea ? textarea.value : "";
    const apt = eventsList.find((a) => String(a.id) === String(aptId));
    if (!apt) return;

    apt.reportText = text;
    apt.status = "reported";
    openDetailModal(aptId, "report");
    renderAppointments();
  }

  /* --------------------------------------------------------------------------
   * ACTIONS
   * -------------------------------------------------------------------------- */
  function selectDate(date) {
    state.viewMode = "day";
    state.selectedDate = date;
    renderHeaderView();
    fetchEvents();
  }

  function goToToday() {
    selectDate(new Date());
  }

  function changeWeek(dir) {
    if (state.viewMode === "range") return;
    const d = new Date(state.selectedDate);
    d.setDate(d.getDate() + dir * 7);
    selectDate(d);
  }

  function toggleUserDropdown() {
    $("user-dropdown")?.classList.toggle("hidden");
  }

  function toggleEmployee(id) {
    id = String(id);

    if (id === "all") {
      state.selectedEmployeeIds = ["all"];
    } else {
      if (state.selectedEmployeeIds.includes("all")) state.selectedEmployeeIds = [id];
      else {
        if (state.selectedEmployeeIds.includes(id)) {
          state.selectedEmployeeIds = state.selectedEmployeeIds.filter((x) => x !== id);
          if (state.selectedEmployeeIds.length === 0) state.selectedEmployeeIds = ["all"];
        } else {
          state.selectedEmployeeIds.push(id);
        }
      }
    }

    renderHeaderUser();
    fetchEvents();
  }

  function clearDateFilter() {
    state.viewMode = "day";
    state.rangeStart = null;
    state.rangeEnd = null;
    renderHeaderView();
    renderAppointments();
  }

  /* --------------------------------------------------------------------------
   * FILTER MODAL
   * -------------------------------------------------------------------------- */
  function openFilterModal() {
    const overlay = $("modal-overlay");
    if (!overlay) return;

    overlay.classList.remove("hidden");
    const today = ymd(new Date());

    overlay.innerHTML = `
      <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl animate-slide-up">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Nach Datum filtern</h3>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Von</label>
            <input type="date" id="filter-start"
                   class="w-full p-3 bg-slate-50 rounded-xl border-none focus:ring-2 focus:ring-brand-dark"
                   value="${today}">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Bis</label>
            <input type="date" id="filter-end"
                   class="w-full p-3 bg-slate-50 rounded-xl border-none focus:ring-2 focus:ring-brand-dark"
                   value="${today}">
          </div>

          <div class="flex gap-3 mt-6">
            <button onclick="closeModal()"
                    class="flex-1 py-3 text-slate-600 font-bold text-sm bg-slate-100 rounded-xl">Abbrechen</button>
            <button onclick="applyFilter()"
                    class="flex-1 py-3 text-white font-bold text-sm bg-brand-dark rounded-xl shadow-lg shadow-brand-dark/20">Anwenden</button>
          </div>
        </div>
      </div>
    `;
    ensureIcons();
  }

  function closeModal() {
    $("modal-overlay")?.classList.add("hidden");
    if (isRecording && recognition) {
      recognition.stop();
      isRecording = false;
    }
  }

  function applyFilter() {
    const s = $("filter-start")?.valueAsDate;
    const e = $("filter-end")?.valueAsDate;
    if (!s || !e) return;

    state.rangeStart = s;
    state.rangeEnd = e;
    state.viewMode = "range";

    renderHeaderView();
    renderAppointments();
    closeModal();
  }

  /* --------------------------------------------------------------------------
   * CREATE MODAL
   * -------------------------------------------------------------------------- */
  function openCreateModal() {
    const overlay = $("modal-overlay");
    if (!overlay) return;

    overlay.classList.remove("hidden");

    state.selectedCustomerId = "";
    state.createSelectedEmployees = [];
    state.createSelectedColor = state.createSelectedColor || COLORS.darkBlue;

    renderCreateForm(overlay);
  }

  function renderCreateForm(container) {
    const empList = employeesList.filter((e) => String(e.id) !== "all");

    const selectedCount = state.createSelectedEmployees.length;
    const teamBtnText = selectedCount > 0 ? `${selectedCount} Mitglied(er) ausgewählt` : "Team auswählen";

    const teamListHtml = empList
      .map((e) => {
        const id = String(e.id);
        const isSelected = state.createSelectedEmployees.includes(id);
        return `
          <div class="team-member-option flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-slate-50 border ${
            isSelected ? "border-brand-dark bg-brand-light/10" : "border-slate-100"
          } mb-2" data-id="${escapeHtml(id)}">
            <div class="flex items-center gap-3">
              ${
                e.avatar
                  ? `<img src="${escapeHtml(e.avatar)}" class="w-8 h-8 rounded-full">`
                  : `<div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold">${escapeHtml(
                      (e.name || "?").charAt(0)
                    )}</div>`
              }
              <span class="text-sm font-medium text-slate-700">${escapeHtml(e.name)}</span>
            </div>
            <div class="w-5 h-5 rounded-full border ${
              isSelected ? "bg-brand-dark border-brand-dark" : "border-slate-300"
            } flex items-center justify-center text-white">
              ${isSelected ? '<i data-lucide="check" class="w-3 h-3"></i>' : ""}
            </div>
          </div>
        `;
      })
      .join("");

    const colorOptionsHtml = Object.values(COLORS)
      .map((color) => {
        const isSelected = state.createSelectedColor === color;
        return `
          <button type="button"
                  class="color-option w-8 h-8 rounded-full relative transition-transform hover:scale-110 focus:outline-none"
                  style="background-color: ${color};"
                  data-color="${escapeHtml(color)}">
            ${
              isSelected
                ? '<span class="absolute inset-0 flex items-center justify-center text-white"><i data-lucide="check" class="w-4 h-4"></i></span>'
                : ""
            }
          </button>
        `;
      })
      .join("");

    container.innerHTML = `
      <div class="bg-white w-full max-w-md h-[95vh] sm:h-auto sm:max-h-[90vh] sm:rounded-2xl rounded-t-3xl flex flex-col overflow-hidden animate-slide-up shadow-2xl">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="font-bold text-slate-800">Neuer Termin</h3>
          <button type="button" onclick="closeModal()"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
        </div>

        <div class="p-4 overflow-y-auto flex-1">
          <form id="createForm" onsubmit="submitCreate(event)" class="space-y-4">

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Titel</label>
              <input type="text" id="inp-title" required
                     class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-dark text-slate-800">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Haupt-Terminart</label>
              <input type="text" id="inp-appointment-type"
                     placeholder="z.B. Angebot, Montage, Besichtigung..."
                     class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-dark text-slate-800">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Ausführungstyp (optional)</label>
              <input type="text" id="inp-execution-type"
                     placeholder="z.B. Vor-Ort, Telefon, Online..."
                     class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-brand-dark text-slate-800">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Kunde</label>
              <select id="inp-customer" class="w-full p-3 bg-brand-light/10 border-none rounded-xl text-sm text-slate-800">
                <option value="">-- Kein Kunde --</option>
                ${CUSTOMERS.map((c) => {
                  const label = `${c.firma || ""} ${c.lastname || ""} ${c.name || ""}`.trim();
                  return `<option value="${escapeHtml(c.id)}">${escapeHtml(label)}</option>`;
                }).join("")}
              </select>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-semibold text-slate-500">Farbe</label>
              <div class="flex gap-4 items-center" id="color-container">${colorOptionsHtml}</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500">Start</label>
                <input type="time" id="inp-start" value="09:00" required class="w-full p-3 border border-slate-200 rounded-xl text-sm">
              </div>
              <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500">Ende</label>
                <input type="time" id="inp-end" value="10:00" required class="w-full p-3 border border-slate-200 rounded-xl text-sm">
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Adresse</label>
              <input type="text" id="inp-address" class="w-full p-3 border border-slate-200 rounded-xl text-sm">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Team zuweisen</label>
              <div class="relative">
                <button type="button" onclick="toggleCreateTeamDropdown()"
                        class="w-full p-3 border border-slate-200 rounded-xl text-sm text-left flex justify-between bg-white">
                  <span id="team-btn-text">${escapeHtml(teamBtnText)}</span>
                  <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>

                <div id="create-team-dropdown"
                     class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 shadow-xl rounded-xl z-10 max-h-48 overflow-y-auto p-2">
                  ${teamListHtml}
                </div>
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-semibold text-slate-500">Beschreibung / Notiz</label>
              <textarea id="inp-desc" rows="3" class="w-full p-3 border border-slate-200 rounded-xl text-sm"></textarea>
            </div>

            <div class="flex justify-between items-center py-2">
              <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" id="inp-public" class="w-4 h-4 rounded text-brand-dark"> Öffentlich
              </label>
              <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" id="inp-report" class="w-4 h-4 rounded text-brand-dark"> Bericht
              </label>
            </div>

          </form>
        </div>

        <div class="p-4 border-t border-slate-100 bg-white">
          <button form="createForm" type="submit" class="w-full bg-brand-dark text-white py-3.5 rounded-xl font-bold shadow-lg">Erstellen</button>
        </div>
      </div>
    `;

    // bind dropdown item clicks
    container.querySelectorAll(".team-member-option").forEach((el) => {
      el.addEventListener("click", () => {
        const id = String(el.dataset.id);
        if (state.createSelectedEmployees.includes(id)) {
          state.createSelectedEmployees = state.createSelectedEmployees.filter((x) => x !== id);
        } else {
          state.createSelectedEmployees.push(id);
        }
        renderCreateForm(container);
        setTimeout(() => $("create-team-dropdown")?.classList.remove("hidden"), 0);
      });
    });

    // bind color clicks
    container.querySelectorAll(".color-option").forEach((el) => {
      el.addEventListener("click", () => {
        state.createSelectedColor = el.dataset.color;
        renderCreateForm(container);
      });
    });

    ensureIcons();
  }

  function toggleCreateTeamDropdown() {
    $("create-team-dropdown")?.classList.toggle("hidden");
  }

  async function submitCreate(e) {
    e.preventDefault();

    const submitBtn = e.target.querySelector('button[type="submit"], button[form="createForm"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerText = "Speichert...";
    }

    const payload = {
      title: $("inp-title")?.value || "",

      // IMPORTANT: map to DB columns
      appointment_type: $("inp-appointment-type")?.value || "",
      execution_type: $("inp-execution-type")?.value || "",

      // note/description
      description: $("inp-desc")?.value || "",

      start_date: ymd(state.selectedDate),
      start_time: $("inp-start")?.value || "09:00",
      end_time: $("inp-end")?.value || "10:00",
      address: $("inp-address")?.value || "",
      customer_id: $("inp-customer")?.value || "",
      color: state.createSelectedColor,
      public: !!$("inp-public")?.checked,
      needs_report: !!$("inp-report")?.checked,
      attendees: state.createSelectedEmployees,
    };

    try {
      const json = await fetchJson(API.create, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF_TOKEN,
        },
        body: JSON.stringify(payload),
      });

      if (json && json.success) {
        closeModal();
        fetchEvents();
      } else {
        alert("Fehler: " + (json?.message || "Unbekannter Fehler"));
      }
    } catch (err) {
      console.error(err);
      alert("Ein Fehler ist aufgetreten.");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = "Erstellen";
      }
    }
  }

  /* --------------------------------------------------------------------------
   * GLOBAL CLICK HANDLER (CLOSE DROPDOWNS)
   * -------------------------------------------------------------------------- */
  document.addEventListener("click", (event) => {
    // header employee dropdown
    const userDropdown = $("user-dropdown");
    if (userDropdown) {
      const clickedToggle = event.target.closest('[onclick="toggleUserDropdown()"]');
      const inside = userDropdown.contains(event.target);
      if (!clickedToggle && !inside) userDropdown.classList.add("hidden");
    }

    // create team dropdown
    const teamDropdown = $("create-team-dropdown");
    if (teamDropdown && !teamDropdown.classList.contains("hidden")) {
      const clickedToggle = event.target.closest('[onclick="toggleCreateTeamDropdown()"]');
      const inside = teamDropdown.contains(event.target);
      const isItem = event.target.closest(".team-member-option");
      if (!clickedToggle && !inside && !isItem) teamDropdown.classList.add("hidden");
    }
  });

  /* --------------------------------------------------------------------------
   * INIT
   * -------------------------------------------------------------------------- */
  async function init() {
    await fetchEmployees();
    renderHeaderView();
    fetchEvents();
    ensureIcons();
  }

  window.addEventListener("DOMContentLoaded", init);

  /* --------------------------------------------------------------------------
   * EXPOSE
   * -------------------------------------------------------------------------- */
  window.openDetailModal = openDetailModal;
  window.switchTab = switchTab;

  window.toggleUserDropdown = toggleUserDropdown;
  window.toggleEmployee = toggleEmployee;

  window.selectDate = selectDate;
  window.goToToday = goToToday;
  window.changeWeek = changeWeek;

  window.openFilterModal = openFilterModal;
  window.applyFilter = applyFilter;
  window.clearDateFilter = clearDateFilter;

  window.openCreateModal = openCreateModal;
  window.toggleCreateTeamDropdown = toggleCreateTeamDropdown;
  window.submitCreate = submitCreate;

  window.closeModal = closeModal;

  window.toggleMic = toggleMic;
  window.saveReport = saveReport;
})();
</script>


</body>
</html>