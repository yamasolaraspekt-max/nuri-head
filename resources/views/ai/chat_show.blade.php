<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>AI Chat — Customer-Aware</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- OPTIONAL (Laravel will fill these if this is a Blade view) -->

  @php
        // Use the *user ID*, not the name, to look up the employee row
        $emp = \Illuminate\Support\Facades\DB::table('employees')
            ->where('id', auth()->user()->name)
            ->select('name','lastname','image')
            ->first();

        $username = $emp->name ?? auth()->user()->name;
        $avatarJpg   = $emp && $emp->image
            ? asset('images/employee/'.$emp->image)
            : asset('images/employee/default.png');
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta id="chat-config"
          data-post-url="{{ route('ai.chats.ask', $chat, false) }}"
          data-username="{{ $username }}"
          data-avatar="{{ $avatarJpg }}"
          data-title="{{ $chat->title ?? ('Chat '.$chat->id) }}">

  <!-- Tailwind via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .focus-ring:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,.35); }
    @keyframes blink { 0%,100%{opacity:.25} 50%{opacity:1} }
    @keyframes pulseRow { 0%{transform:translateX(-10%)} 100%{transform:translateX(110%)} }
    @keyframes dots { 0%{content:"."} 33%{content:".."} 66%{content:"..."} 100%{content:"."} }
    .dotblink::after{ content:"."; animation: dots 1.2s infinite steps(1,end); }
    .token-cursor{ width:10px; height:1.1em; background: currentColor; opacity:.25; display:inline-block; animation: blink 1s infinite; vertical-align: -0.1em; }
    .thinking-bar::before{
      content:""; position:absolute; inset:0; background: linear-gradient(90deg, transparent 0%, rgba(99,102,241,.15) 40%, transparent 80%);
      transform: translateX(-60%); animation: pulseRow 1.8s infinite linear;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 text-slate-800 flex flex-col">
  <!-- Top bar (minimal; actions moved to composer per your spec) -->
    <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/85 backdrop-blur" role="navigation" aria-label="Primary">
      <div class="max-w-5xl mx-auto px-3 py-3 flex items-center gap-3">
        <!-- Dashboard -->
        <a href="{{ url('/home') }}" aria-label="Dashboard" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3"  y="3"  width="7" height="7" rx="1.5"></rect>
            <rect x="14" y="3"  width="7" height="7" rx="1.5"></rect>
            <rect x="3"  y="14" width="7" height="7" rx="1.5"></rect>
            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
          </svg>
          <span class="text-sm">Dashboard</span>
        </a>

        <!-- Back (Zurück) -->
        <a href="{{ url('ai/chats') }}" aria-label="Zurück" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 12H6"></path>
            <path d="M12 6l-6 6 6 6"></path>
          </svg>
          <span class="text-sm">Zurück</span>
        </a>

        <!-- Title -->
        <div class="ml-2 font-semibold truncate" id="chatTitle">Nuri Head AI Chat</div>
      </div>
    </header>


  <!-- Thinking status lane -->
  <section id="thinkingWrap" class="sticky top-[54px] z-10 max-w-5xl mx-auto w-full px-3 pt-2 hidden">
    <div class="relative overflow-hidden rounded-xl border border-indigo-200 bg-white shadow-sm">
      <div class="thinking-bar absolute pointer-events-none"></div>
      <div class="px-3 py-2 flex items-center gap-2">
        <div class="h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></div>
        <div class="text-sm font-medium text-indigo-700" id="thinkingTitle">Thinking</div>
        <div class="ml-auto text-xs text-indigo-600/80">live</div>
      </div>
      <div id="thinkingSteps" class="px-3 pb-2 flex flex-wrap items-center gap-2 text-[12px]">
        <!-- steps injected -->
      </div>
    </div>
  </section>

  <!-- Chat log -->
  <main id="log" class="max-w-5xl mx-auto w-full px-3 py-4 flex-1 overflow-y-auto">
    <!-- messages injected -->
       <!-- Jump/Autoscroll control -->
    <button id="jumpLatest"
            class="hidden fixed top-[70px] right-3 z-20 rounded-full bg-indigo-600 text-white text-sm
                  px-3 py-1.5 shadow-lg hover:bg-indigo-700 focus-ring"
            title="Scroll to latest">
      <span>Zum neuesten</span> <span aria-hidden="true">↓</span>
      <span data-badge
            class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 rounded-full
                  bg-white/20 text-[11px] px-1"></span>
    </button>
  </main>



  <!-- Composer (all controls inside) -->
  <footer class="sticky bottom-0 z-10 border-t border-slate-200 bg-white/90 backdrop-blur">
    <div class="max-w-5xl mx-auto w-full px-3 py-3">
      <div class="rounded-2xl border border-slate-300 bg-white shadow-sm p-2 relative">
        <div class="flex items-end gap-2">
          <!-- Left icon cluster -->
          <div class="flex items-center gap-1 pl-1 pb-1">
            <button id="attachBtn" class="p-2 rounded-lg hover:bg-slate-100 focus-ring" title="Attach">
              <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15V7a5 5 0 00-10 0v10a3 3 0 006 0V8" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <button id="imageBtn" class="p-2 rounded-lg hover:bg-slate-100 focus-ring" title="Image">
              <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/><path d="M3 17l5-5 4 4 3-3 6 6" stroke-width="2"/></svg>
            </button>
            <button id="micBtn" class="p-2 rounded-lg hover:bg-slate-100 focus-ring" title="Voice">
              <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="2" width="6" height="12" rx="3" stroke-width="2"/><path d="M5 10a7 7 0 0014 0M12 19v3" stroke-width="2"/></svg>
            </button>

            <button id="suggestBtn" class="p-2 rounded-lg hover:bg-slate-100 focus-ring" title="Suggestions">
              <!-- sparkles icon -->
              <svg class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M5 3l2 4 4 2-4 2-2 4-2-4-4-2 4-2 2-4zM19 13l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2z" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>

          <!-- Textarea -->
          <div class="flex-1">
            <textarea id="msgInput" rows="1" placeholder="Stellen Sie eine kluge und ungewöhnlich konkrete Frage …"
              class="w-full resize-none focus-ring rounded-xl px-3 py-2 leading-6 placeholder:text-slate-400"
              style="max-height:160px"></textarea>

            <!-- Inline actions row -->
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
              <button id="shareBtn" class="rounded-md border px-2 py-1 hover:bg-slate-50">Share</button>
              <button id="resetMemoryBtn" class="rounded-md border px-2 py-1 hover:bg-slate-50">Reset memory</button>
              <button id="regenBtn" class="rounded-md border px-2 py-1 hover:bg-slate-50">Regenerate</button>
              <button id="copyLastBtn" class="rounded-md border px-2 py-1 hover:bg-slate-50">Copy last</button>
              <div class="ml-auto flex items-center gap-2">
                <button id="stopBtn" class="hidden rounded-md bg-rose-600 text-white px-3 py-1 hover:bg-rose-700">Stop</button>
                <button id="sendBtn" class="rounded-md bg-indigo-600 text-white px-3 py-1 hover:bg-indigo-700">Send</button>
              </div>
            </div>
          </div>
        </div>
        <!-- tiny hint -->
        <div class="mt-1 text-[11px] text-slate-500 px-1">Enter to send · Shift+Enter for newline</div>
      </div>
    </div>
  </footer>

  <!-- NEW: Suggestions popover -->
<div id="suggestPanel" style="left: 199px; bottom: 101px;"
     class="hidden absolute bottom-16 left-2 z-20 w-[min(42rem,calc(100vw-2rem))] max-h-72 overflow-y-auto
            rounded-xl border border-slate-200 bg-white shadow-xl p-3">
                <div class="flex items-center gap-2 mb-2">
                <input id="suggestSearch" type="text" placeholder="Search prompts… / Prompts suchen…"
                      class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus-ring"
                      autocomplete="off" />

                <!-- Language toggle -->
                <div class="flex items-center rounded-lg border border-slate-300 overflow-hidden">
                  <button type="button" class="sugg-lang px-2 py-1 text-xs" data-lang="en">EN</button>
                  <button type="button" class="sugg-lang px-2 py-1 text-xs" data-lang="de">DE</button>
                </div>

                <button id="suggestClose" class="rounded-lg border px-2 py-1 text-slate-600 hover:bg-slate-50">Close</button>
              </div>


  <!-- Quick hint -->
  <div class="text-[11px] text-slate-500 mb-2">
    Tip: Click to paste; <kbd class="px-1 rounded border">Ctrl</kbd>/<kbd class="px-1 rounded border">⌘</kbd>+Click to send immediately.
  </div>

  <div id="suggestList" class="flex flex-wrap gap-2"></div>
</div>


<script id="chat-seed" type="application/json">
  {!! $chat->messages->map(function($m){
      return [
          'role'    => $m->role,
          'content' => $m->content,
          'at'      => optional($m->created_at)->toIso8601String(),
      ];
  })->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
  </script>

  <script>
  window.CSRF = document.querySelector('meta[name="csrf-token"]')?.content || null;
</script>
  <script>
    // ---------- Config ----------
    const cfgEl = document.getElementById('chat-config');
    const CONFIG = {
      POST_URL : cfgEl?.dataset.postUrl || null,   // if missing, we'll mock
      USERNAME : cfgEl?.dataset.username || 'You',
      AVATAR   : cfgEl?.dataset.avatar   || '',
      TITLE    : cfgEl?.dataset.title    || 'AI Chat'
    };
    document.getElementById('chatTitle').textContent = CONFIG.TITLE;

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || null;

    // ---------- Elements ----------
    const log = document.getElementById('log');
    const msgInput = document.getElementById('msgInput');
    const sendBtn = document.getElementById('sendBtn');
    const stopBtn = document.getElementById('stopBtn');
    const regenBtn = document.getElementById('regenBtn');
    const copyLastBtn = document.getElementById('copyLastBtn');
    const thinkingWrap = document.getElementById('thinkingWrap');
    const thinkingSteps = document.getElementById('thinkingSteps');
    const thinkingTitle = document.getElementById('thinkingTitle');

    // ---------- Utilities ----------
    function el(html){ const t=document.createElement('template'); t.innerHTML=html.trim(); return t.content.firstElementChild; }

      // Auto-scroll state
      let autoScroll = true;
      let unreadCount = 0;
      // ---------- Chat state ---------- 
        let emittedFinal = false; // NEW


      // Elements used by auto-scroll UI
      const jumpBtn = document.getElementById('jumpLatest');

      function isNearBottom() {
        // within 80px of the bottom counts as “at bottom”
        return (log.scrollHeight - log.scrollTop - log.clientHeight) < 80;
      }
 
      // Respect auto-scroll unless forced, but wait for layout first
          function scrollToBottom(force = false){
            if (force || autoScroll){
              requestAnimationFrame(() => {
                log.scrollTop = log.scrollHeight;
              });
            }
          }


      function updateJumpBtn(){
        if (!jumpBtn) return;

        // Badge only when there are unseen tokens
        const badgeEl = jumpBtn.querySelector('[data-badge]');
        if (badgeEl) {
          if (unreadCount > 0) {
            badgeEl.textContent = String(unreadCount);
            badgeEl.classList.remove('hidden');
          } else {
            badgeEl.textContent = '';
            badgeEl.classList.add('hidden');
          }
        }

        // Show the button whenever you're not near the bottom
        if (!autoScroll) {
          jumpBtn.classList.remove('hidden');
        } else {
          jumpBtn.classList.add('hidden');
        }
      }


      // Track user scroll position to toggle auto-scroll
      log.addEventListener('scroll', () => {
        const near = isNearBottom();
        autoScroll = near;
        if (near) unreadCount = 0;
        updateJumpBtn();
      });

      // Clicking the pill jumps to bottom and re-enables auto-scroll
      jumpBtn?.addEventListener('click', () => {
        autoScroll = true;
        unreadCount = 0;
        updateJumpBtn();
        scrollToBottom(true);
      });

      // Keep existing helpers:
      function autosize(el){ el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,160)+'px'; }
      function sanitize(s){ return (s||'').replace(/[<>&]/g, m => ({'<':'&lt;','>':'&gt;','&':'&amp;'}[m])); }
      function mdLite(s){
        let out = sanitize(s);

        // images: ![alt](url)
        out = out.replace(
          /!\[([^\]]*)\]\((https?:\/\/[^\s)]+|\/[^\s)]+)\)/g,
          '<img src="$2" alt="$1" class="mt-2 max-w-full max-h-40 rounded-lg border border-slate-200 shadow-sm" loading="lazy">'
        );

        // links: [text](url)
        out = out.replace(
          /\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g,
          '<a href="$2" target="_blank" rel="noopener" class="text-indigo-600 underline">$1</a>'
        );

        // code / bold / italic / line breaks
        out = out
          .replace(/`([^`]+)`/g,'<code class="px-1 py-0.5 rounded bg-slate-100 text-slate-700">$1</code>')
          .replace(/\*\*([^*]+)\*\*/g,'<strong>$1</strong>')
          .replace(/\*([^*]+)\*/g,'<em>$1</em>')
          .replace(/\n/g,'<br>');

        return out;
      }

      function copy(text){ navigator.clipboard?.writeText(text).catch(()=>{}); }
    function urlPost(url, body, signal){
      const headers = { 'Accept':'text/event-stream' };
      if (CSRF) headers['X-CSRF-TOKEN'] = CSRF;
      headers['Content-Type'] = 'application/json';
      return fetch(url, { method:'POST', headers, body: JSON.stringify(body), credentials:'same-origin', signal });
    }

    // ---------- Thinking lane ----------
    const STEP_LABELS = [
      'Reading customer context',
      'Retrieving related docs',
      'Reasoning about constraints',
      'Composing answer'
    ];
    function resetThinking(){
      thinkingSteps.innerHTML = '';
      STEP_LABELS.forEach((t,i)=>{
        thinkingSteps.appendChild(el(`
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full border border-indigo-300 text-[10px] text-indigo-700 bg-indigo-50">${i+1}</span>
            <span>${t}</span>
          </div>
        `));
      });
    }
    function showThinking(){ resetThinking(); thinkingTitle.textContent='Thinking'; thinkingWrap.classList.remove('hidden'); }
    function markStepDone(i){
      const chip = thinkingSteps.children[i]?.querySelector('span:first-child');
      if (chip){ chip.textContent='✓'; chip.className='inline-flex items-center justify-center h-5 w-5 rounded-full bg-emerald-500 text-white text-[10px]'; }
    }
    function hideThinking(){ thinkingWrap.classList.add('hidden'); }

    // ---------- Bubbles ----------
   function assistantBubble(contentHTML, opts = {}) {
      const when = opts.when || new Date().toLocaleString();
      return el(`
        <div class="flex gap-3 mb-4">
          <div class="flex-shrink-0">
            <div class="h-9 w-9 rounded-lg border bg-white grid place-items-center text-indigo-600 shadow-sm">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 2l4 4-4 4-4-4 4-4zM4 14l4 4M20 14l-4 4" stroke-width="2"/>
              </svg>
            </div>
          </div>
          <div class="max-w-[80%] md:max-w-[70%] rounded-2xl border bg-white shadow-sm p-3">
            <div class="text-[11px] text-slate-500 mb-1">Assistant · ${when}</div>
            <div class="prose prose-slate max-w-none text-sm leading-6">${contentHTML}</div>
          </div>
        </div>
      `);
    }

    function userBubble(text, opts = {}) {
      const when = opts.when || new Date().toLocaleString();
      const avatar = CONFIG.AVATAR
        ? `<img src="${sanitize(CONFIG.AVATAR)}" class="h-9 w-9 rounded-lg object-cover border bg-white"/>`
        : `<div class="h-9 w-9 rounded-lg border bg-white grid place-items-center">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 12a5 5 0 100-10 5 5 0 000 10zM2 22a10 10 0 0120 0" stroke-width="2"/>
            </svg>
          </div>`;
      return el(`
        <div class="flex gap-3 mb-4 justify-end">
          <div class="max-w-[80%] md:max-w-[70%] rounded-2xl bg-indigo-600 text-white shadow-sm p-3">
            <div class="text-[11px] text-indigo-100/90 mb-1">${sanitize(CONFIG.USERNAME)} · ${when}</div>
            <div class="text-sm leading-6">${mdLite(text)}</div>
          </div>
          <div class="flex-shrink-0">${avatar}</div>
        </div>
      `);
    }


    function renderSeedHistory() {
        const raw = document.getElementById('chat-seed')?.textContent || '[]';
        let items = [];
        try { items = JSON.parse(raw) || []; } catch (_) { items = []; }

        if (!Array.isArray(items) || items.length === 0) {
          // Show your hello only for empty chats
          log.appendChild(assistantBubble('Hallo! Ich habe Ihren Kundenkontext geladen. Fordern Sie ein Angebot, eine Materialliste oder einen Zeitplan an..'));
          scrollToBottom();
          return;
        }

        for (const m of items) {
          const when = m.at ? new Date(m.at).toLocaleString() : new Date().toLocaleString();
          if (m.role === 'user') {
            log.appendChild(userBubble(m.content || '', { when }));
          } else if (m.role === 'assistant') {
            log.appendChild(assistantBubble(mdLite(m.content || ''), { when }));
          } // ignore 'system' etc.
        }
       scrollToBottom(true);
      }

      // Call it once the DOM helpers exist:
      renderSeedHistory();
      updateJumpBtn();

      window.addEventListener('scroll', updateJumpBtn);


    function typingBubble(){
      const id = 'typing-'+Date.now();
      const node = el(`
        <div class="flex gap-3 mb-4" id="${id}">
          <div class="flex-shrink-0">
            <div class="h-9 w-9 rounded-lg border bg-white grid place-items-center text-indigo-600 shadow-sm">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2l4 4-4 4-4-4 4-4zM4 14l4 4M20 14l-4 4" stroke-width="2"/></svg>
            </div>
          </div>
          <div class="max-w-[80%] md:max-w-[70%] rounded-2xl border bg-white shadow-sm p-3">
            <div class="text-[11px] text-slate-500 mb-1">Assistant · typing<span class="dotblink"></span></div>
            <div class="text-sm leading-6" id="${id}-stream"></div>
          </div>
        </div>
      `);
      log.appendChild(node);
      scrollToBottom(true);
      return {
        id,
        set(html){ const s = document.getElementById(id+'-stream'); if (s) s.innerHTML = html + ' <span class="token-cursor"></span>'; scrollToBottom(); },
        remove(){ document.getElementById(id)?.remove(); }
      };
    }

    // ---------- Chat state ----------
    let lastUserMsg = '';
    let lastAssistantText = '';
    let controller = null; // AbortController for streaming

  

    // ---------- Events ----------
    msgInput.addEventListener('input', () => autosize(msgInput));
    msgInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey){
        e.preventDefault();
        sendBtn.click();
      }
    });

    sendBtn.addEventListener('click', () => {
      const txt = msgInput.value.trim();
      if (!txt) return;
      msgInput.value = '';
      autosize(msgInput);
      streamResponse(txt);
    });

    stopBtn.addEventListener('click', () => {
      if (controller) controller.abort();
      stopBtn.classList.add('hidden');
      sendBtn.classList.remove('hidden');
      hideThinking();
      log.appendChild(assistantBubble('⏹️ Stopped.'));
      scrollToBottom();
    });

    regenBtn.addEventListener('click', () => {
      if (!lastUserMsg) return;
      streamResponse(lastUserMsg);
    });

    copyLastBtn.addEventListener('click', () => {
      if (!lastAssistantText) return;
      copy(lastAssistantText);
      copyLastBtn.textContent = 'Copied!';
      setTimeout(()=>copyLastBtn.textContent='Copy last', 900);
    });

    // Mic demo (dictation to input)
    (function mic(){
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
      const btn = document.getElementById('micBtn');
      if (!SR){ btn.title = 'Speech not supported in this browser'; return; }
      let rec=null, listening=false;
      btn.addEventListener('click', () => {
        if (listening){ try{rec.stop();}catch{} return; }
        rec = new SR(); rec.lang='de-DE'; rec.interimResults=true; listening=true;
        const orig = btn.innerHTML;
        btn.innerHTML='<svg class="h-5 w-5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="5" width="14" height="14" rx="1" stroke-width="2"/></svg>';
        rec.onresult=(e)=>{ let final=''; let interim=''; for(let i=e.resultIndex;i<e.results.length;i++){const t=e.results[i][0].transcript; e.results[i].isFinal?final+=t:interim+=t;} msgInput.value=(msgInput.value+' '+final+' '+interim).trim(); autosize(msgInput); };
        const cleanup=()=>{ listening=false; btn.innerHTML=orig; };
        rec.onerror=cleanup; rec.onend=cleanup; try{rec.start();}catch{cleanup();}
      });
    })();


    function readCookie(name){
      return document.cookie.split('; ').find(r => r.startsWith(name + '='))?.split('=')[1] || '';
    }
 

  async function postSSE(url, body, signal) {
    const xsrfCookie = decodeURIComponent(readCookie('XSRF-TOKEN') || '');
    const headers = {
      'Accept'           : 'text/event-stream',
      'Content-Type'     : 'application/json',
      'X-Requested-With' : 'XMLHttpRequest',
    };
    // Laravel accepts either one of these:
    if (window.CSRF) headers['X-CSRF-TOKEN'] = window.CSRF;
    if (xsrfCookie)   headers['X-XSRF-TOKEN'] = xsrfCookie;

    const res = await fetch(url, {
      method: 'POST',
      headers,
      body: JSON.stringify(body),
      // 🔑 includes cookies even if the URL accidentally becomes absolute
      credentials: 'include',
      redirect: 'manual',
      signal,
    });

    if (res.type === 'opaqueredirect' || res.status === 302) throw new Error('AUTH_REDIRECT');
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const ct = (res.headers.get('content-type') || '').toLowerCase();
    if (!ct.includes('text/event-stream')) throw new Error('NOT_SSE');
    if (!res.body) throw new Error('NO_STREAM');
    return res;
  }

 
    // ---------- Streaming ----------
       // ---- SSE utils (robust) ----
          function parseSSEFrames(buffer) {
            // Split on blank line (handles \n\n and \r\n\r\n)
            const parts = buffer.split(/\r?\n\r?\n/);
            const frames = parts.slice(0, -1);
            const rest = parts[parts.length - 1] || '';
            const out = [];

            for (const f of frames) {
              let event = null;
              const dataLines = [];
              for (const rawLine of f.split(/\r?\n/)) {
                const line = rawLine.trimEnd();
                if (!line || line.startsWith(':')) continue;        // comment/heartbeat
                if (line.startsWith('event:')) {
                  event = line.slice(6).trim();
                  continue;
                }
                if (line.startsWith('data:')) {
                  dataLines.push(line.slice(5).trimStart());
                  continue;
                }
              }
              if (dataLines.length) {
                const dataStr = dataLines.join('\n');
                let data;
                try { data = JSON.parse(dataStr); } catch { data = dataStr; }
                out.push({ event, data });
              }
            }
            return { frames: out, rest };
          }

          const STATUS_TO_STEP = {
            starting: 0,
            reading_customer: 0,
            workflow: 1,
            roof_area: 1,
            weather: 1,
            normtemp: 1,
            memory: 2,
            generating: 3,
          };

          // ---------- Streaming ----------
          async function streamResponse(message) {
            
            if (!CONFIG.POST_URL) {
              log.appendChild(assistantBubble('⚠️ No POST_URL configured.'));
              return;
            }

            // UI kickoff
            emittedFinal = false;   // NEW
            lastUserMsg = message;
            log.appendChild(userBubble(message));
            scrollToBottom(true);
            showThinking();
            thinkingTitle.textContent = 'Thinking';
            markStepDone(0);

            const typer = typingBubble();
            sendBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            scrollToBottom();

            controller = new AbortController();
            let gotFirst = false;
            let hadError = false;

            // if we get literally nothing for 30s, abort to surface the problem
            const watchdog = setTimeout(() => {
              if (!gotFirst && controller) controller.abort();
            }, 30000);

            try {
              const res = await postSSE(CONFIG.POST_URL, { message }, controller.signal);

              const reader  = res.body.getReader();
              const decoder = new TextDecoder();
              let buffer = '';
              let firstToken = true;
              lastAssistantText = '';

              // gentle progress animations
              setTimeout(() => markStepDone(1), 400);
              setTimeout(() => markStepDone(2), 1000);

              while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                gotFirst = true;
                buffer += decoder.decode(value, { stream: true });

                const { frames, rest } = parseSSEFrames(buffer);
                buffer = rest;

                for (const { event, data } of frames) {
                  // status events from backend: {status: "..."} with event: status
                  if (event === 'status' && data && typeof data === 'object' && data.status) {
                    const s = String(data.status);
                    const step = STATUS_TO_STEP[s];
                    if (typeof step === 'number') markStepDone(step);
                    if (s === 'generating') { thinkingTitle.textContent = 'Composing'; }
                    continue;
                  }

                  // data frames (may be JSON or plain text)
                  if (data && typeof data === 'object') {
                    if (data.chunk) {
                      if (firstToken) { markStepDone(3); thinkingTitle.textContent = 'Composing'; firstToken = false; }
                      lastAssistantText += data.chunk;
                      typer.set(mdLite(lastAssistantText));
                      if (autoScroll) {
                          scrollToBottom();    // gentle, only if near bottom
                        } else {
                          unreadCount++;
                          updateJumpBtn();
                        }
                    }
                    if (data.done) {
                      clearTimeout(watchdog);
                      typer.remove();
                      hideThinking();
                      if (lastAssistantText) {
                        log.appendChild(assistantBubble(mdLite(lastAssistantText)));
                          emittedFinal = true;  
                         if (autoScroll) scrollToBottom(true);
                      } else {
                        log.appendChild(assistantBubble('⚠️ Stream ended with no content.'));
                      }
                      sendBtn.classList.remove('hidden');
                      stopBtn.classList.add('hidden');
                      scrollToBottom();
                    }
                  } else if (typeof data === 'string' && data.trim() !== '') {
                    // plain text fallback (e.g., from a different backend)
                    if (firstToken) { markStepDone(3); thinkingTitle.textContent = 'Composing'; firstToken = false; }
                    lastAssistantText += data;
                    typer.set(mdLite(lastAssistantText));
                    if (autoScroll) {
                        scrollToBottom();
                      } else {
                        unreadCount++;
                        updateJumpBtn();
                      }
                  }
                }
              }
            } catch (e) {
              hadError = true;
              const msg = String(e?.message || e);
              typer.remove();
              hideThinking();
              log.appendChild(assistantBubble(
                msg === 'AUTH_REDIRECT' ? '🔒 Session expired. Log in again.' :
                msg === 'NOT_SSE'       ? '⚠️ Server did not send SSE (check Content-Type and gzip).' :
                msg === 'NO_STREAM'     ? '⚠️ Browser/proxy blocked the stream.' :
                                          '⚠️ Request failed: ' + msg
              ));
              scrollToBottom();
            } finally {
              clearTimeout(watchdog);
              controller = null;
              stopBtn.classList.add('hidden');
              sendBtn.classList.remove('hidden');

              // If we received tokens but didn’t emit final bubble yet (e.g., no explicit {done}),
              // finish gracefully.
             if (!hadError && !emittedFinal && lastAssistantText) {   // CHANGED
                hideThinking();
                log.appendChild(assistantBubble(mdLite(lastAssistantText)));
                scrollToBottom(true);
              }
            }
          }

     
    // Seed one assistant hello (optional)
    log.appendChild(assistantBubble('Hallo! Ich habe Ihren Kundenkontext geladen. Fordern Sie ein Angebot, eine Materialliste oder einen Zeitplan an.'));
    scrollToBottom();
  </script>

<script>
// ---------- Bilingual Suggestions ----------
const SUGGESTIONS = {
  en: [
    // Quick starters
    { cat:'Quick', q:'Give me a 5-bullet TL;DR of the customer’s situation and next steps.' },
    { cat:'Quick', q:'What are the 3 fastest wins we can deliver for this customer?' },
    { cat:'Quick', q:'Summarize last activity and what’s blocking progress.' },

    // PV & roof sizing
    { cat:'PV', q:'Estimate PV kWp and module count. Show assumptions.' },
    { cat:'PV', q:'Roof fit check: max modules given area/pitch/shading; note constraints.' },
    { cat:'PV', q:'Pick an inverter size and DC/AC oversizing, with reasoning.' },
    { cat:'PV', q:'Create a PV stringing plan (series/parallel) for the recommended layout.' },

    // Heat load & heat pump
    { cat:'Heizlast', q:'Estimate design heat load (Heizlast) using context. If a key input is missing, ask only for that.' },
    { cat:'Heizlast', q:'Suggest a heat pump size and flow temperatures for existing radiators.' },
    { cat:'Heizlast', q:'List the 3 hydraulic checks before commissioning.' },

    // Battery
    { cat:'Battery', q:'Recommend a battery size from annual kWh and usage profile; include min/typical/max.' },
    { cat:'Battery', q:'Simulate self-consumption and self-sufficiency with and without a 5 kWh battery.' },
    { cat:'Battery', q:'Should we prioritize battery or more PV? Argue with numbers.' },

    // Workflow / CRM
    { cat:'Workflow', q:'Where are we in the workflow? What’s the next action?' },
    { cat:'Workflow', q:'Show the last 5 CRM history items as terse bullets with dates.' },
    { cat:'Workflow', q:'What risks can you infer from recent activity?' },

    // Appointments
    { cat:'Appointments', q:'Do we have an upcoming appointment? Propose a prep checklist.' },
    { cat:'Appointments', q:'Draft a confirmation note for the site visit on {date}, 3 short sentences.' },

    // Problems / tickets
    { cat:'Problems', q:'Summarize open problems by severity and likely fix paths.' },
    { cat:'Problems', q:'Give a one-liner status per ticket with the next step.' },

    // Tasks
    { cat:'Tasks', q:'List my open tasks for this customer, ordered by urgency; propose deadlines.' },
    { cat:'Tasks', q:'Turn this note into 3 actionable tasks: {paste note}.' },

    // Contact
    { cat:'Contact', q:'Validate contact details; tell me only what’s missing or inconsistent.' },
    { cat:'Contact', q:'Draft one specific question to fill the most critical missing field.' },

    // Email
    { cat:'Email', q:'Write a concise email updating the customer about PV sizing and lead time. Subject + body.' },
    { cat:'Email', q:'Follow-up email after an unanswered proposal 7 days ago—friendly but firm.' },

    // Weather
    { cat:'Weather', q:'What’s the weather today at the customer location? Any roof-work constraints?' },

    // Pricing / TCO
    { cat:'Pricing', q:'Create a quote skeleton: items, quantities, unit prices (placeholders), and notes.' },
    { cat:'Pricing', q:'Ballpark TCO with payback years for PV+battery recommendation.' },

    // BOM
    { cat:'BOM', q:'Produce a BOM (modules, inverter, rails, clamps, cables).' },
    { cat:'BOM', q:'List mounting accessories with counts derived from roof area.' },

    // Memory
    { cat:'Memory', q:'Add a short memory: {one-sentence fact we’ll want later}.' },
    { cat:'Memory', q:'Summarize today’s conversation into 5 bullets for the CRM note.' },

    // Diagnostics
    { cat:'Diagnostics', q:'What single data point would most improve your estimate right now?' },
    { cat:'Diagnostics', q:'Sensitivity: how does PV recommendation change if annual use is ±20%?' },
  ],

  de: [
    // Schnellstart
    { cat:'Schnellstart', q:'Gib mir eine 5-Punkte-Zusammenfassung (TL;DR) zur Kundensituation und den nächsten Schritten.' },
    { cat:'Schnellstart', q:'Was sind die 3 schnellsten Quick-Wins für diesen Kunden?' },
    { cat:'Schnellstart', q:'Fasse die letzte Aktivität zusammen und nenne den größten Blocker.' },

    // PV & Dach
    { cat:'PV', q:'Schätze PV-kWp und Modulanzahl. Zeige Annahmen.' },
    { cat:'PV', q:'Dach-Fit: maximale Modulzahl nach Fläche/Neigung/Verschattung; nenne Grenzen.' },
    { cat:'PV', q:'Wähle eine Wechselrichtergröße inkl. DC/AC-Oversizing und begründe.' },
    { cat:'PV', q:'Erstelle einen String-Plan (Serie/Parallel) für das empfohlene Layout.' },

    // Heizlast & WP
    { cat:'Heizlast', q:'Schätze die Auslegungsheizlast auf Basis des Kontexts. Fehlt ein Pflichtwert, frage nur danach.' },
    { cat:'Heizlast', q:'Empfiehl eine Wärmepumpen-Leistung und Vorlauftemperaturen für bestehende Heizkörper.' },
    { cat:'Heizlast', q:'Liste die 3 hydraulischen Checks vor Inbetriebnahme.' },

    // Batterie
    { cat:'Batterie', q:'Empfiehl eine Speichergröße aus Jahresverbrauch und Profil; gib min/typisch/max an.' },
    { cat:'Batterie', q:'Simuliere Eigenverbrauch und Autarkie mit und ohne 5 kWh Speicher.' },
    { cat:'Batterie', q:'Besser Speicher oder mehr PV? Begründe mit Zahlen.' },

    // Workflow / CRM
    { cat:'Workflow', q:'Wo stehen wir im Workflow? Was ist der nächste Schritt?' },
    { cat:'Workflow', q:'Zeige die letzten 5 CRM-Historie-Einträge knapp mit Datum.' },
    { cat:'Workflow', q:'Welche Risiken lassen sich aus den letzten Aktivitäten ableiten?' },

    // Termine
    { cat:'Termine', q:'Gibt es einen nächsten Termin? Schlage eine kurze Vorbereitungsliste vor.' },
    { cat:'Termine', q:'Formuliere eine Terminbestätigung für den Vor-Ort-Besuch am {Datum} in 3 Sätzen.' },

    // Probleme / Tickets
    { cat:'Probleme', q:'Fasse offene Probleme nach Schweregrad und wahrscheinlichem Fix zusammen.' },
    { cat:'Probleme', q:'Gib pro Ticket eine Einzeiler-Statusmeldung mit nächstem Schritt.' },

    // Aufgaben
    { cat:'Aufgaben', q:'Liste meine offenen Aufgaben zu diesem Kunden nach Dringlichkeit; schlage Fristen vor.' },
    { cat:'Aufgaben', q:'Wandle diese Notiz in 3 umsetzbare Aufgaben um: {Notiz einfügen}.' },

    // Kontakt
    { cat:'Kontakt', q:'Prüfe Kontaktdaten; nenne nur fehlende oder inkonsistente Felder.' },
    { cat:'Kontakt', q:'Formuliere genau eine Frage, um das kritischste fehlende Feld zu klären.' },

    // E-Mail
    { cat:'E-Mail', q:'Schreibe eine kurze E-Mail zum PV-Sizing und zur Lieferzeit. Betreff + Text.' },
    { cat:'E-Mail', q:'Follow-up nach 7 Tagen ohne Rückmeldung zum Angebot – freundlich aber bestimmt.' },

    // Wetter
    { cat:'Wetter', q:'Wie ist das Wetter heute am Kundenstandort? Einschränkungen für Dacharbeiten?' },

    // Kalkulation / TCO
    { cat:'Kalkulation', q:'Erstelle ein Angebotsgerüst: Positionen, Mengen, Platzhalter-Einzelpreise, Notizen.' },
    { cat:'Kalkulation', q:'Überschlage TCO und Amortisation für die empfohlene PV+Speicher-Lösung.' },

    // Stückliste
    { cat:'Stückliste', q:'Erzeuge eine BOM (Module, WR, Schienen, Klemmen, Kabel).' },
    { cat:'Stückliste', q:'Liste Montagematerial mit Stückzahlen abgeleitet aus der Dachfläche.' },

    // Gedächtnis
    { cat:'Gedächtnis', q:'Füge eine kurze Memory-Notiz hinzu: {ein Satz, der später nützlich ist}.' },
    { cat:'Gedächtnis', q:'Fasse das heutige Gespräch in 5 Bulletpoints für die CRM-Notiz zusammen.' },

    // Diagnostik
    { cat:'Diagnostik', q:'Welche einzelne Information würde die Schätzung jetzt am stärksten verbessern?' },
    { cat:'Diagnostik', q:'Sensitivität: Wie ändert sich die PV-Empfehlung bei ±20 % Jahresverbrauch?' },
  ],
};

let SUGG_LANG = (navigator.language || '').toLowerCase().startsWith('de') ? 'de' : 'en';

const suggestBtn     = document.getElementById('suggestBtn');
const suggestPanel   = document.getElementById('suggestPanel');
const suggestSearch  = document.getElementById('suggestSearch');
const suggestList    = document.getElementById('suggestList');
const suggestClose   = document.getElementById('suggestClose');
const suggLangBtns   = () => Array.from(document.querySelectorAll('.sugg-lang'));

function chipHTML(item) {
  return `
    <button data-q="${item.q.replace(/"/g,'&quot;')}"
            class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200
                   bg-white hover:bg-indigo-50 hover:border-indigo-300 text-sm shadow-sm">
      <span class="inline-flex items-center text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">${item.cat}</span>
      <span class="text-left">${item.q}</span>
      <span class="ml-1 hidden group-hover:inline text-indigo-600 text-xs">↗</span>
    </button>`;
}

function activeList() { return SUGGESTIONS[SUGG_LANG] || []; }

function renderSuggestions(filter = '') {
  const f = filter.trim().toLowerCase();
  const items = activeList().filter(s =>
    !f || s.q.toLowerCase().includes(f) || (s.cat && s.cat.toLowerCase().includes(f))
  );
  suggestList.innerHTML = items.length
    ? items.map(chipHTML).join('')
    : `<div class="text-sm text-slate-500 px-1 py-2">Keine Treffer / No matches.</div>`;
}

function setLang(lang) {
  if (!SUGGESTIONS[lang]) return;
  SUGG_LANG = lang;
  // button styles
  suggLangBtns().forEach(b => {
    const is = b.dataset.lang === lang;
    b.className = 'sugg-lang px-2 py-1 text-xs ' + (is
      ? 'bg-indigo-600 text-white'
      : 'bg-white text-slate-700');
  });
  renderSuggestions(suggestSearch.value);
}

function openSuggestions() {
  suggestPanel.classList.remove('hidden');
  setLang(SUGG_LANG);           // sync styles + render
  suggestSearch.value = '';
  suggestSearch.placeholder = SUGG_LANG === 'de'
    ? 'Prompts suchen…'
    : 'Search prompts…';
  setTimeout(()=> suggestSearch.focus(), 0);
}

function closeSuggestions() {
  suggestPanel.classList.add('hidden');
}

// events
suggestBtn?.addEventListener('click', (e) => {
  e.preventDefault();
  if (suggestPanel.classList.contains('hidden')) openSuggestions(); else closeSuggestions();
});
suggestClose?.addEventListener('click', closeSuggestions);
suggestSearch?.addEventListener('input', (e) => renderSuggestions(e.target.value));
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.sugg-lang');
  if (btn) { setLang(btn.dataset.lang); }
});

// paste / send
suggestList?.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-q]');
  if (!btn) return;
  const text = btn.getAttribute('data-q') || '';
  msgInput.value = text;
  msgInput.dispatchEvent(new Event('input'));
  msgInput.focus();

  // Ctrl/Cmd + click → send immediately
  if (e.ctrlKey || e.metaKey) {
    closeSuggestions();
    sendBtn.click();
  } else {
    closeSuggestions();
  }
});

// close on ESC / outside
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSuggestions(); });
document.addEventListener('click', (e) => {
  if (suggestPanel.classList.contains('hidden')) return;
  const within = e.target.closest('#suggestPanel') || e.target.closest('#suggestBtn');
  if (!within) closeSuggestions();
});

// initialize toggle look (respect browser language on first open)
setLang(SUGG_LANG);
</script>


</body>
</html>
