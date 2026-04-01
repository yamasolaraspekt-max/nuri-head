@extends('ai.layout.app')
@section('title') Nuri Head Ai @endsection
@section('content')

<main class="max-w-7xl mx-auto px-4 py-6">
    <!-- Centered search -->
    <div class="w-full flex justify-center">
      <div class="w-full max-w-2xl">
        <label for="search" class="sr-only">Kunden suchen</label>
        <div class="relative">
          <input
            id="search"
            type="text"
            placeholder="Search by name, lastname, address…"
            class="focus-ring w-full rounded-2xl border border-slate-300 bg-white/90 px-4 py-3 pr-10 shadow-sm placeholder:text-slate-400"
            autocomplete="off"
          />
          <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
            <path d="M20 20l-3.5-3.5" stroke-width="2" stroke-linecap="round"></path>
          </svg>
        </div>
        <div class="mt-2 flex items-center justify-between text-sm text-slate-600">
          <span id="resultCount">Geben Sie mindestens 2 Zeichen ein…</span>
          <button id="clearBtn" class="hidden text-slate-500 hover:text-slate-700">Löschen</button>
        </div>
      </div>
    </div>

    <!-- Grid of folder cards -->
    <section class="mt-6">
      <div id="grid" class="grid gap-4 sm:gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        <!-- cards injected here -->
      </div>

      <!-- Empty state -->
      <div id="emptyState" class="hidden text-center py-16">
        <div class="mx-auto mb-4 h-16 w-16 rounded-full bg-slate-200 grid place-items-center">
          <svg class="h-7 w-7 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
            <path d="M20 20l-3.5-3.5" stroke-width="2" stroke-linecap="round"></path>
          </svg>
        </div>
        <p class="text-slate-700 font-medium">Keine Kunden gefunden</p>
        <p class="text-slate-500">Versuchen Sie es mit einem anderen Namen, Nachnamen oder einer anderen Adresse.</p>
      </div>

          <section class="mt-10">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-slate-800">Gespeicherte Chats</h2>
              <div class="text-sm text-slate-600">
                <span id="savedCount">0</span> total
                <button id="refreshChatsBtn" class="ml-3 rounded-md border px-2 py-1 text-xs hover:bg-slate-50">Refresh</button>
              </div>
            </div>

            <div id="savedEmpty" class="hidden text-slate-500 text-sm mt-4">
                You don’t have any chats yet.
            </div>

            <div id="chatsGrid"
                class="mt-4 grid gap-4 sm:gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
              <!-- chat cards injected -->
            </div>
          </section>
    </section>
  </main>

  <!-- Action bar -->
  <div id="actionBar" class="pointer-events-none fixed inset-x-0 bottom-4 flex justify-center">
    <div class="pointer-events-auto hidden items-center gap-2 rounded-2xl border border-slate-200 bg-white/90 px-3 py-2 shadow-lg">
      <span id="selectedLabel" class="text-sm text-slate-700"></span>
      <div class="flex items-center gap-2">
        <button id="startBtn"
                class="hidden rounded-lg bg-indigo-600 px-3 py-2 text-white text-sm hover:bg-indigo-700 active:bg-indigo-800">
          Start Chat
        </button>
        <button id="openBtn"
                class="hidden rounded-lg border border-indigo-600 text-indigo-700 px-3 py-2 text-sm hover:bg-indigo-50">
          Open Chat
        </button>
        <button id="deleteBtn"
                class="hidden rounded-lg bg-rose-600 px-3 py-2 text-white text-sm hover:bg-rose-700 active:bg-rose-800">
          Delete Chat
        </button>
      </div>
    </div>
  </div>

  <!-- Toasts -->
  <div id="toast" class="fixed left-1/2 -translate-x-1/2 bottom-5 hidden">
    <div class="rounded-lg bg-emerald-600 text-white px-4 py-2 shadow-lg text-sm">Chat erstellt!</div>
  </div>
  <div id="toastDel" class="fixed left-1/2 -translate-x-1/2 bottom-5 hidden">
    <div class="rounded-lg bg-rose-600 text-white px-4 py-2 shadow-lg text-sm">Chat gelöscht!</div>
  </div>
  <div id="toastErr" class="fixed left-1/2 -translate-x-1/2 bottom-5 hidden">
    <div class="rounded-lg bg-rose-600 text-white px-4 py-2 shadow-lg text-sm">Etwas ist schiefgelaufen. Rufen Sie Ramin an</div>
  </div>

  <!-- Confirm Delete Modal -->
  <div id="confirmModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" aria-hidden="true"></div>
    <div class="relative mx-auto mt-24 w-[92%] max-w-md rounded-2xl bg-white shadow-xl">
      <div class="p-5">
        <h3 class="text-lg font-semibold text-slate-800">Chat löschen</h3>
        <p id="confirmText" class="mt-1 text-sm text-slate-600">
          Dadurch wird der Chat entfernt für <span id="confirmName" class="font-medium"></span>.
        </p>
        <div class="mt-5 flex justify-end gap-2">
          <button id="cancelDel" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancel</button>
          <button id="confirmDel" class="rounded-lg bg-rose-600 px-3 py-2 text-sm text-white hover:bg-rose-700">Delete</button>
        </div>
      </div>
    </div>
  </div>
  @endsection

  @section('scripts')


  <script>
    // ========= CONFIGURE THESE TO MATCH YOUR ROUTES =========
    const CONFIG = {
      DASHBOARD_URL: '/home',
      CUSTOMERS_SEARCH: '/customers/search',
      AI_CHATS_STORE: '/ai/chats',
      AI_CHATS_SHOW_BASE: '/ai/chats',
      AI_CHATS_DESTROY_BASE: '/ai/chats',          
      AI_CHATS_BY_CUSTOMER: '/api/ai/chats/by-customer',
       AI_CHATS_INDEX: '/api/ai/chats'
    }; 

    // --- Saved chats state ---
    let savedChats = []; // [{id, title, last_activity_at, customer:{name,lastname,city}, customer_id}]

    // Elements for saved chats
    const chatsGrid = document.getElementById('chatsGrid');
    const savedCount = document.getElementById('savedCount');
    const savedEmpty = document.getElementById('savedEmpty');
    const refreshChatsBtn = document.getElementById('refreshChatsBtn');


    function chatCard(row) {
        const cust = row.customer || {};
        const who  = [cust.name||'', cust.lastname||''].join(' ').trim() || 'Unknown customer';
        const city = cust.city ? ` · ${cust.city}` : '';
        const last = row.last_activity_at ? `Last: ${row.last_activity_at}` : '';
        const title = row.title || `Chat ${row.id}`;

        return `
          <div class="group relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md">
            <div class="mb-1 text-xs text-slate-500">${last}</div>
            <div class="font-semibold text-slate-800 truncate">${title}</div>
            <div class="text-sm text-slate-600 truncate">${who}${city}</div>

            <div class="mt-3 flex items-center gap-2">
              <button data-open="${row.id}"
                      class="rounded-md bg-indigo-600 text-white px-2.5 py-1.5 text-xs hover:bg-indigo-700">Open</button>
              <button data-del="${row.id}"
                      class="rounded-md border border-rose-600 text-rose-700 px-2.5 py-1.5 text-xs hover:bg-rose-50">Delete</button>
            </div>

            <div class="absolute top-2 right-2 opacity-70 group-hover:opacity-100">
              <svg class="h-5 w-5 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 3l3 3-3 3-3-3 3-3zM4 15l3 3M20 15l-3 3" stroke-width="2"/>
              </svg>
            </div>
          </div>
        `;
      }


      async function loadSavedChats() {
        try {
          const data = await http(CONFIG.AI_CHATS_INDEX);
          savedChats = Array.isArray(data) ? data : [];
          renderSavedChats();
        } catch (e) {
          console.error('Load chats failed:', e);
          savedChats = [];
          renderSavedChats();
        }
      }

      function renderSavedChats() {
        savedCount.textContent = String(savedChats.length);
        savedEmpty.classList.toggle('hidden', savedChats.length > 0);
        chatsGrid.innerHTML = savedChats.map(chatCard).join('');
      }


      // Open/Delete handlers on the grid (event delegation)
        chatsGrid.addEventListener('click', async (e) => {
          const openBtn = e.target.closest('button[data-open]');
          const delBtn  = e.target.closest('button[data-del]');
          if (openBtn) {
            const chatId = openBtn.getAttribute('data-open');
            if (chatId) location.href = urlWithId(CONFIG.AI_CHATS_SHOW_BASE, chatId);
            return;
          }
          if (delBtn) {
            const chatId = delBtn.getAttribute('data-del');
            if (!chatId) return;
            try {
              await http(urlWithId(CONFIG.AI_CHATS_DESTROY_BASE, chatId), { method: 'DELETE' });
            } catch (_) {
              // fallback to spoofed POST if needed
              const fd = new FormData(); fd.append('_method','DELETE');
              const token = csrf();
              await fetch(urlWithId(CONFIG.AI_CHATS_DESTROY_BASE, chatId), {
                method: 'POST', body: fd, credentials: 'same-origin',
                headers: token ? { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } : { 'Accept':'application/json' }
              });
            }
            // remove from local state & re-render
            savedChats = savedChats.filter(c => String(c.id) !== String(chatId));
            renderSavedChats();

            // Also update the customer grid badge if present
            const victim = Object.keys(chatByCustomer).find(cid => chatByCustomer[cid]?.id == chatId);
            if (victim) { delete chatByCustomer[victim]; render(); }
          }
        });

        // Manual refresh button
        refreshChatsBtn.addEventListener('click', loadSavedChats);

        // Initial load
        loadSavedChats();


    function urlWithId(base, id) {
      return `${String(base).replace(/\/+$/,'')}/${encodeURIComponent(String(id))}`;
    }

    // CSRF helper (works when served via Blade; otherwise omit)
    function csrf() {
      const m = document.querySelector('meta[name="csrf-token"]');
      return m ? m.getAttribute('content') : null;
    }

    // Minimal fetch wrapper
   async function http(url, opts = {}) {
      const headers = opts.headers || {};
      if (!headers['Content-Type'] && opts.body && !(opts.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
      }
      headers['Accept'] = headers['Accept'] || 'application/json';
      const token = csrf();
      if (token) headers['X-CSRF-TOKEN'] = token;
      const res = await fetch(url, { credentials: 'same-origin', ...opts, headers });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const ct = res.headers.get('content-type') || '';
      return ct.includes('application/json') ? res.json() : res.text();
    }

    // Debounce
    const debounce = (fn, ms=300) => {
      let t; return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), ms); };
    };

    // State
    let customers = [];           // results from search
    let chatByCustomer = {};      // {customer_id: {id, title, last_activity_at}}
    let selectedCustomerId = null;
    let selectedCustomerName = '';

    // Elements
    const grid = document.getElementById('grid');
    const resultCount = document.getElementById('resultCount');
    const emptyState = document.getElementById('emptyState');
    const search = document.getElementById('search');
    const clearBtn = document.getElementById('clearBtn');
    const actionBar = document.querySelector('#actionBar > div');
    const selectedLabel = document.getElementById('selectedLabel');
    const startBtn = document.getElementById('startBtn');
    const openBtn = document.getElementById('openBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const toast = document.getElementById('toast');
    const toastDel = document.getElementById('toastDel');
    const toastErr = document.getElementById('toastErr');
    const confirmModal = document.getElementById('confirmModal');
    const confirmDel = document.getElementById('confirmDel');
    const cancelDel = document.getElementById('cancelDel');
    const confirmName = document.getElementById('confirmName');

    // Render
    function folderCard(c) {
      const hasChat = !!chatByCustomer[c.id];
      const isSelected = c.id === selectedCustomerId;

      const border = isSelected ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-slate-200';
      const tabColor = isSelected ? 'bg-amber-300 border-amber-400' : 'bg-amber-200 border-amber-300';
      const mainColor = isSelected ? 'bg-amber-100' : 'bg-amber-50';

      const badge = hasChat
        ? `<span class="ml-auto inline-flex items-center gap-1 rounded-md bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs">
             <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2"/></svg> Chat
           </span>`
        : `<span class="ml-auto inline-flex items-center rounded-md bg-slate-100 text-slate-600 px-2 py-0.5 text-xs">No chat</span>`;

      const lastAct = hasChat ? (chatByCustomer[c.id].last_activity_at || '') : '';

      return `
      <button data-id="${c.id}" data-name="${(c.name||'')+' '+(c.lastname||'')}"
        class="group relative text-left rounded-xl ${mainColor} ${border} border p-4 shadow-sm hover:shadow-md transition focus-ring w-full">
        <div class="absolute -top-2 left-5 h-3 w-12 rounded-t-md border ${tabColor}"></div>
        <div class="mb-2 flex items-center gap-2">
          <svg class="h-6 w-6 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4l2 2h6a2 2 0 012 2v1H4V6a2 2 0 012-2h4z"></path><path d="M4 9h18v7a2 2 0 01-2 2H6a2 2 0 01-2-2V9z"></path></svg>
          <div class="font-semibold truncate">${(c.name||'')} ${(c.lastname||'')}</div>
          ${badge}
        </div>
        <div class="text-sm text-slate-600 truncate">${c.address || ''}</div>
        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
          <span>${c.city || ''}</span>
          <span>${lastAct ? 'Last: ' + lastAct : ''}</span>
        </div>
        ${hasChat ? `
          <div class="absolute top-2 right-2 opacity-70 group-hover:opacity-100">
            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M3 6h18" stroke-width="2" />
              <path d="M8 6v12a2 2 0 002 2h4a2 2 0 002-2V6" stroke-width="2"/>
              <path d="M10 11v6M14 11v6" stroke-width="2"/>
            </svg>
          </div>` : ''}
      </button>`;
    }

    function render() {
      grid.innerHTML = customers.map(folderCard).join('');
      resultCount.textContent = customers.length
        ? `Showing ${customers.length} ${customers.length === 1 ? 'customer' : 'customers'}`
        : (search.value.trim().length >= 2 ? 'Showing 0 customers' : 'Type at least 2 characters…');
      emptyState.classList.toggle('hidden', customers.length > 0);

      const sel = customers.find(c => c.id === selectedCustomerId);
      const hasSelection = !!sel;
      actionBar.classList.toggle('hidden', !hasSelection);
      startBtn.classList.add('hidden');
      openBtn.classList.add('hidden');
      deleteBtn.classList.add('hidden');

      if (sel) {
        selectedCustomerName = `${sel.name||''} ${sel.lastname||''}`.trim();
        selectedLabel.textContent = `Selected: ${selectedCustomerName} ${sel.city ? '('+sel.city+')' : ''}`;
        if (chatByCustomer[sel.id]) {
          openBtn.classList.remove('hidden');
          deleteBtn.classList.remove('hidden');
        } else {
          startBtn.classList.remove('hidden');
        }
      }
    }

    // Load chats for a set of customer IDs
    async function loadChatsFor(ids) {
      chatByCustomer = {};
      if (!ids.length) return;

      // Try the suggested endpoint format: /api/ai/chats/by-customer?ids=1,2,3
      try {
        const data = await http(`${CONFIG.AI_CHATS_BY_CUSTOMER}?ids=${ids.join(',')}`);
        (Array.isArray(data) ? data : []).forEach(row => {
          if (row && row.customer_id && row.id) {
            chatByCustomer[row.customer_id] = {
              id: row.id,
              title: row.title,
              last_activity_at: row.last_activity_at || row.lastActivity || ''
            };
          }
        });
      } catch (e) {
        // Fallback: ignore if endpoint not available; UI will just show "No chat"
        console.warn('Chats lookup failed:', e.message);
      }
    }

    // Search customers from backend
    const doSearch = debounce(async (q) => {
      const t = q.trim();
      if (t.length < 2) {
        customers = [];
        selectedCustomerId = null;
        render();
        return;
      }
      try {
        const res = await http(`${CONFIG.CUSTOMERS_SEARCH}?q=${encodeURIComponent(t)}`);
        customers = Array.isArray(res) ? res : (res.data || []);
        // Normalize expected fields
        customers = customers.map(r => ({
          id: r.id,
          name: r.name || r.first_name || '',
          lastname: r.lastname || r.last_name || '',
          city: r.city || r.town || '',
          address: r.full_address || r.address || ''
        }));
        await loadChatsFor(customers.map(c => c.id));
        // Keep selection if still visible
        if (!customers.some(c => c.id === selectedCustomerId)) selectedCustomerId = null;
        render();
      } catch (e) {
        console.error(e);
        customers = [];
        selectedCustomerId = null;
        showToast(toastErr);
        render();
      }
    }, 300);

    // Helpers
    function showToast(el, ms=1400) {
      el.classList.remove('hidden');
      setTimeout(() => el.classList.add('hidden'), ms);
    }

    // Events
    search.addEventListener('input', (e) => {
      const val = e.target.value;
      clearBtn.classList.toggle('hidden', val.length === 0);
      doSearch(val);
    });

    clearBtn.addEventListener('click', () => {
      search.value = '';
      clearBtn.classList.add('hidden');
      customers = [];
      selectedCustomerId = null;
      render();
      search.focus();
    });

    document.getElementById('refreshBtn').addEventListener('click', () => {
      if (search.value.trim().length >= 2) doSearch(search.value);
    });

    

    grid.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-id]');
      if (!btn) return;
      selectedCustomerId = Number(btn.getAttribute('data-id'));
      selectedCustomerName = btn.getAttribute('data-name') || '';
      render();
      if (window.innerWidth < 640) window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    });

    startBtn.addEventListener('click', async () => {
      if (!selectedCustomerId) return;
      try {
        const res = await http(CONFIG.AI_CHATS_STORE, {
          method: 'POST',
          body: JSON.stringify({ customer_id: selectedCustomerId })
        });
        const id = res?.id || res?.chat?.id || res?.data?.id;
        if (!id) throw new Error('No chat ID in response');
        showToast(toast);
        // reflect existence in UI immediately
        chatByCustomer[selectedCustomerId] = { id, title: res?.title, last_activity_at: 'now' };
        render();
        // Navigate to chat
        location.href = urlWithId(CONFIG.AI_CHATS_SHOW_BASE, id);

      } catch (e) {
        console.error(e);
        showToast(toastErr);
      }
    });

    openBtn.addEventListener('click', () => {
      const row = chatByCustomer[selectedCustomerId];
      if (row?.id) location.href = urlWithId(CONFIG.AI_CHATS_SHOW_BASE, row.id);
    });

    deleteBtn.addEventListener('click', () => {
      const sel = customers.find(c => c.id === selectedCustomerId);
      if (!sel) return;
      confirmName.textContent = `${sel.name||''} ${sel.lastname||''}`.trim();
      confirmModal.classList.remove('hidden');
      document.body.classList.add('modal-open');
    });

    cancelDel.addEventListener('click', () => {
      confirmModal.classList.add('hidden');
      document.body.classList.remove('modal-open');
    });

    confirmDel.addEventListener('click', async () => {
      const row = chatByCustomer[selectedCustomerId];
      const chatId = row?.id;
      if (!chatId) { console.warn('No chatId for selected customer'); showToast(toastErr); return; }

      const delUrl = urlWithId(CONFIG.AI_CHATS_DESTROY_BASE, chatId);
      console.log('Deleting chat', { chatId, delUrl }); // <-- see exact URL in console

      try {
        // Try real DELETE first
        await http(delUrl, { method: 'DELETE' });

        // Success: update UI
        delete chatByCustomer[selectedCustomerId];
        confirmModal.classList.add('hidden');
        document.body.classList.remove('modal-open');
        showToast(toastDel);
        render();
      } catch (e) {
        // Fallback: method spoof if server/proxy blocks DELETE (405/403/419)
        try {
          const fd = new FormData();
          fd.append('_method', 'DELETE');
          const token = csrf();
          await fetch(delUrl, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: token ? { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } : { 'Accept': 'application/json' }
          });

          delete chatByCustomer[selectedCustomerId];
          confirmModal.classList.add('hidden');
          document.body.classList.remove('modal-open');
          showToast(toastDel);
          render();
        } catch (e2) {
          console.error('Delete failed:', e2);
          showToast(toastErr);
        }
      }
    });

    // Initial paint
    render();
  </script>
  @endsection