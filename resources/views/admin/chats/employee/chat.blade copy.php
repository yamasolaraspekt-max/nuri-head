@extends('admin.layouts.app')
@section('title') CHAT @endsection
@section('style')
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- Vendors --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
  <link  href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link  href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --header-h: 56px;       /* conversation header */
      --search-h: 56px;       /* message search row */
      --composer-h: 64px;     /* input row */
      --safe-bottom: env(safe-area-inset-bottom, 0px);
    }

    /* -- Viewport utility for reliable full-height on mobile -- */
    .screen-block{
      height: 100vh;
      height: 100dvh;            /* modern mobile */
      min-height: 100dvh;
      contain: layout paint size; /* cheaper reflow when resizing */
    }

    /* left & right slide-in panes on small screens (overlay) */
    #chatSidebar,
    #rightSidebar{
      position: absolute;
      top: 0; bottom: 0;
      background: #fff;
      will-change: transform;
      transition: transform .28s ease;
      z-index: 30;               /* above center column */
    }
    #chatSidebar{ left: 0; width: 18rem; transform: translateX(-100%); }
    #chatSidebar.visible{ transform: translateX(0); }

    #rightSidebar{ right: 0; width: 20rem; transform: translateX(100%); }
    #rightSidebar.visible{ transform: translateX(0); }

    /* give center column a stacking context so sticky areas render right */
    .conversation-col{ position: relative; z-index: 10; }

    /* On desktop: panes become static columns */
    @media (min-width: 1024px){
      #chatSidebar, #rightSidebar{
        position: static !important;
        transform: none !important;
        width: auto;
      }
    }

    /* Sticky header + fixed composer; scroll area flexes between them */
    .conv-header{ position: sticky; top: 0; z-index: 5; }
    .composer-bar{
      position: sticky;
      bottom: 0;
      z-index: 5;
      padding-bottom: var(--safe-bottom);
    }

    /* Message list: smooth scroll & no rubber-band overscroll into parents */
    #chatScroll{
      overscroll-behavior: contain;
      -webkit-overflow-scrolling: touch;
    }

    /* Tiny helpers & polish */
    .hover-buttons{ top:6px; right:14px }
    .bg-mine{ background:#c0d8ea; color:#111 }
    .emoji-list{
      position:absolute; bottom:120%; left:0;
      display:flex; flex-wrap:wrap; gap:.125rem;
      background:#fff; border:1px solid #e5e7eb; border-radius:.5rem;
      padding:.25rem; max-width:240px;
      box-shadow:0 2px 8px rgba(0,0,0,.08); z-index:50
    }
    .emoji-list.hidden{ display:none !important }
    .emoji-list span{ padding:6px; cursor:pointer; font-size:20px }

    @keyframes mic-pulse{0%{box-shadow:0 0 0 0 rgba(239,68,68,.45)}70%{box-shadow:0 0 0 10px rgba(239,68,68,0)}100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}}
    .recording-pulse{ animation:mic-pulse 1.2s infinite; border-radius:9999px }
    #waveCanvas{ display:block; width:200px; height:28px }

    /* Dark mode tweaks (optional) */
    :root[data-theme="dark"] body{ background:#0f172a; color:#e5e7eb }
    :root[data-theme="dark"] .bg-white{ background:#0b1222 !important }
    :root[data-theme="dark"] .text-gray-600,
    :root[data-theme="dark"] .text-gray-500{ color:#9aa3b2 !important }
    :root[data-theme="dark"] input,
    :root[data-theme="dark"] textarea{ background:#0f172a; color:#fff }

    .sentinel{ height:1px }
  </style>

  <style>
    /* === ChatGPT-style Composer ============================================== */
:root{
  --bg: #ffffff;
  --fg: #0f172a;
  --muted: #6b7280;
  --border: #e5e7eb;
  --hover: #f3f4f6;
  --brand: #10a37f;         /* ChatGPT-ish green */
  --ring: rgba(16,163,127,.35);
}

/* Dark mode (supports either .dark on <html> or system preference) */
html.dark, [data-theme="dark"]{
  --bg: #0b0f13;
  --fg: #e5e7eb;
  --muted: #9ca3af;
  --border: #1f2937;
  --hover: #111827;
  --ring: rgba(16,163,127,.45);
}

/* Bar container: sticky bottom, soft blur, subtle top border */
.composer-bar{
  position: sticky;
  bottom: 0;
  z-index: 40;
  background: var(--bg);
  border-top: 1px solid var(--border) !important;
  backdrop-filter: saturate(1.1) blur(6px);
  -webkit-backdrop-filter: saturate(1.1) blur(6px);
  padding: 12px 14px !important;
  gap: .5rem !important;
  color: var(--fg);
  padding-bottom: calc(12px + env(safe-area-inset-bottom, 0));
}

/* Icon buttons (emoji / attach / mic) */
#emojiBtn,
#attachBtn,
#voiceBtn{
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border-radius: 9999px;
  border: none;
  background: transparent;
  color: var(--muted);
  transition: background-color .15s ease, transform .12s ease;
}
#emojiBtn:hover,
#attachBtn:hover,
#voiceBtn:hover{
  background: var(--hover);
}
#emojiBtn:active,
#attachBtn:active,
#voiceBtn:active{
  transform: scale(.98);
}

/* Input: large rounded pill, subtle border, clean focus ring */
#messageInput{
  display: block;
  width: 100%;
  border: 1px solid var(--border) !important;
  border-radius: 22px !important;
  padding: 10px 14px !important;
  line-height: 1.4;
  background: var(--bg);
  color: var(--fg);
  resize: none !important;
  outline: none;
  box-shadow: 0 1px 0 rgba(0,0,0,.02) inset;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  max-height: 10rem;            /* keep it tidy */
  scrollbar-width: thin;
  scrollbar-color: var(--border) transparent;
}
#messageInput::placeholder{
  color: var(--muted);
}
#messageInput:focus{
  border-color: rgba(16,163,127,.45) !important;
  box-shadow: 0 0 0 3px var(--ring);
}
#messageInput::-webkit-scrollbar{
  width: 8px;
}
#messageInput::-webkit-scrollbar-thumb{
  background: var(--border);
  border-radius: 999px;
}

/* Send button: circular, floating-feel, arrow icon via pseudo-element */
#sendButton{
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 9999px;
  border: none;
  background: var(--brand) !important;
  color: #fff !important;
  display: grid;
  place-items: center;
  box-shadow: 0 6px 18px rgba(16,163,127,.28);
  transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  font-size: 0;                 /* hide "Senden" text */
  padding: 0 !important;
}
#sendButton::before{
  content: "➤";                 /* minimalist arrow */
  font-size: 16px;
  line-height: 1;
  transform: translateX(1px);   /* optical nudge */
}
#sendButton:hover{
  transform: translateY(-1px);
  box-shadow: 0 10px 22px rgba(16,163,127,.33);
}
#sendButton:active{
  transform: translateY(0);
}

/* Recording HUD: subtle chip */
#recHUD{
  border-color: var(--border) !important;
  background: color-mix(in oklab, var(--brand) 6%, var(--bg));
}

/* Emoji popover: tidy grid, soft shadow */
.emoji-list{
  position: absolute;
  bottom: 52px;
  left: 0;
  display: grid;
  grid-template-columns: repeat(8, 1.6rem);
  gap: .35rem;
  background: var(--bg);
  color: var(--fg);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 8px;
  box-shadow: 0 12px 28px rgba(0,0,0,.18), 0 2px 6px rgba(0,0,0,.12);
  max-height: 220px;
  overflow: auto;
  scrollbar-width: thin;
  scrollbar-color: var(--border) transparent;
}
.emoji-list::-webkit-scrollbar{
  height: 8px; width: 8px;
}
.emoji-list::-webkit-scrollbar-thumb{
  background: var(--border);
  border-radius: 999px;
}
.emoji-list span{
  font-size: 20px;
  line-height: 1;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  transition: transform .12s ease, background-color .12s ease;
}
.emoji-list span:hover{
  background: var(--hover);
  transform: scale(1.15);
}

/* Minor polish for feather icons in icon buttons */
#attachBtn i,
#voiceBtn i{
  width: 18px;
  height: 18px;
  stroke-width: 2.2;
}

/* Optional: constrain to a central column (uncomment if desired) */
/*
.chat-shell{
  max-width: 880px;
  margin-inline: auto;
  padding-inline: 12px;
}
*/

  </style>
@endsection

@section('content')
@php
  // ✅ use auth()->id() to fetch employee row
  $emp = DB::table('employees')
    ->select('name','lastname','image')
    ->where('id', auth()->user()->name)
    ->first();

  $empImage = $emp->image ?? 'users.png';
  $fullname = trim(($emp->name ?? '').' '.($emp->lastname ?? '')) ?: 'Ich';
@endphp

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-body mt-4">

      {{-- Offline banner --}}
      <div id="offlineBanner"
           class="hidden bg-amber-100 text-amber-900 px-4 py-2 text-sm">
        ⚠️ Keine Internetverbindung. Nachrichten werden gesendet, sobald du wieder online bist.
      </div>

      <main class="relative flex screen-block overflow-hidden mt-2">

        {{-- LEFT: Chats/Groups (slides in on mobile) --}}
        <aside id="chatSidebar"
               class="bg-white border-r flex flex-col z-40 lg:w-72"
               aria-label="Chatliste">
          <div class="p-3 flex items-center justify-between border-b">
            <div class="flex items-center gap-2 min-w-0">
              <img src="{{ asset('images/employee/'.$empImage) }}"
                   class="w-8 h-8 rounded-full object-cover"
                   alt="Avatar {{ $fullname }}" />
              <div class="font-semibold truncate max-w-[10rem]"
                   title="{{ $fullname }}">{{ $fullname }}</div>
            </div>
            <div class="flex items-center gap-2">
              <button id="createGroupBtn"
                      class="bg-primary text-white px-2 py-1 rounded shadow hover:bg-blue-700"
                      title="Gruppe erstellen">➕</button>
            </div>
          </div>

          <div class="p-2">
            <input id="searchInput"
                   placeholder="Benutzer & Gruppen suchen…"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" />
          </div>

          <div id="listSkeleton" class="px-3 space-y-2" hidden>
            @for($i=0;$i<6;$i++)
              <div class="animate-pulse h-10 bg-gray-100 rounded"></div>
            @endfor
          </div>

          <ul id="userList"
              class="divide-y p-2 overflow-y-auto flex-1"
              aria-live="polite" aria-busy="true"></ul>
        </aside>

        {{-- MIDDLE: Conversation --}}
        <section class="conversation-col flex-1 flex flex-col border-r bg-gradient-to-b from-gray-100 to-white"
                 aria-label="Nachrichtenbereich">

          {{-- Sticky header --}}
          <div class="conv-header p-2 bg-white border-b flex items-center gap-3" style="height: var(--header-h);">
            <button onclick="toggleContact()"
                    class="lg:hidden text-sm text-blue-600 bg-white px-2 py-1 rounded shadow"
                    aria-label="Kontakte öffnen">
              <i class="feather icon-users"></i> Kontakte
            </button>

            <h2 id="chatTitle" class="text-lg font-semibold truncate">Wähle einen Kontakt</h2>

            <div class="ml-auto flex items-center gap-2">
              <span id="typingIndicator" class="text-sm text-gray-400 hidden">
                <i class="fa fa-spinner"></i> schreibt…
              </span>
              <button onclick="toggleRightSidebar()"
                      class="lg:hidden text-sm text-blue-600 bg-white px-2 py-1 rounded shadow"
                      aria-label="Einstellungen öffnen">
                <i class="feather icon-settings"></i> Einstellungen
              </button>
            </div>
          </div>

          {{-- Sticky search bar --}}
          <div class="p-2 bg-white border-b" style="height: var(--search-h);">
            <input id="messageSearchInput"
                   placeholder="Nachrichten durchsuchen…"
                   class="w-full h-full px-3 py-2 border rounded" />
          </div>

          {{-- Scroll container fills the rest --}}
          <div id="chatScroll" class="flex-1 overflow-y-auto">
            <div id="topSentinel" class="sentinel"></div>
            <div id="chatBox" class="p-4 space-y-4"></div>
            <div id="bottomSentinel" class="sentinel"></div>
          </div>

          {{-- Reply preview --}}
          <div id="replyBox" class="px-4 py-2 bg-gray-50 text-sm text-gray-600 hidden"></div>

          {{-- Sticky composer (safe-area aware) --}}
          <div class="composer-bar p-2 bg-white border-t flex items-center gap-2 relative"
               style="min-height: var(--composer-h);">

            {{-- Emoji --}}
            <div class="relative">
              <button id="emojiBtn" class="text-2xl leading-none"
                      aria-haspopup="dialog" aria-expanded="false" title="Emoji">😊</button>
              <div id="emojiList" class="emoji-list hidden" role="dialog" aria-label="Emoji-Auswahl">
                <span>😀</span><span>😁</span><span>😂</span><span>🤣</span><span>😎</span><span>😍</span>
                <span>😢</span><span>😭</span><span>😡</span><span>👍</span><span>👎</span><span>🙏</span>
              </div>
            </div>

            {{-- Attach --}}
            <button id="attachBtn" class="p-2 rounded-full hover:bg-gray-100" title="Datei anhängen" aria-label="Datei anhängen">
              <i data-feather="paperclip" aria-hidden="true"></i>
            </button>
            <input id="fileInput" type="file" class="hidden" multiple>

            {{-- Voice --}}
            <button id="voiceBtn" class="p-2 rounded-full hover:bg-gray-100" title="Sprachnachricht" aria-label="Sprachnachricht aufnehmen">
              <i data-feather="mic" aria-hidden="true"></i>
            </button>
            <div id="recHUD" class="hidden ml-2 items-center gap-2 rounded-full border px-3 py-1 bg-gray-50">
              <canvas id="waveCanvas" width="200" height="28"></canvas>
              <span id="recTimer" class="text-xs tabular-nums text-gray-700">00:00</span>
            </div>

            {{-- Input --}}
            <div class="flex-1">
              <textarea id="messageInput" rows="1" placeholder="Nachricht eingeben…"
                        class="w-full max-h-40 resize-none border rounded-2xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 overflow-y-auto"
                        aria-label="Nachricht"></textarea>
            </div>

            {{-- Send --}}
            <button id="sendButton"
                    class="bg-primary hover:bg-blue-700 text-white px-5 py-2 rounded-full shadow"
                    title="Senden (Strg/⌘+Enter)">
              Senden
            </button>
          </div>
        </section>

        {{-- RIGHT: Details / Settings (slides in on mobile) --}}
        <aside id="rightSidebar"
               class="w-80 flex flex-col border-l bg-white"
               aria-label="Chat Einstellungen">
          <div class="p-3 border-b">
            <h3 class="text-xl font-semibold">Chat Einstellungen</h3>
          </div>

          <div class="p-3 border-b">
            <h2 id="activeChatName" class="text-lg font-semibold">Chat Name</h2>
            <p id="activeChatMeta" class="text-sm text-gray-500">Chat Info</p>
          </div>

          <div class="px-4 py-3 border-b space-y-3">
            <label class="flex justify-between items-center">
              <span class="text-sm font-medium">Sound</span>
              <input id="soundToggle" type="checkbox" class="toggle toggle-sm" checked>
            </label>
            <label class="flex justify-between items-center">
              <span class="text-sm font-medium">Auto-Löschen</span>
              <input id="autoDeleteToggle" type="checkbox" class="toggle toggle-sm">
            </label>
            <label class="flex justify-between items-center">
              <span class="text-sm font-medium">Dark Mode</span>
              <input id="darkModeToggle" type="checkbox" class="toggle toggle-sm">
            </label>
          </div>

          <div class="p-4 border-b">
            <h4 class="text-sm font-semibold mb-2">Mitglieder</h4>
            <ul id="groupMembers" class="space-y-2 text-sm text-gray-700"></ul>
            <button id="addMemberBtn"
                    class="mt-3 px-3 py-1 text-xs bg-primary text-white rounded hover:bg-blue-600">
              ➕ Mitglied hinzufügen
            </button>
          </div>

          <div class="p-4 border-b">
            <h4 class="text-sm font-semibold mb-2">Dateien</h4>
            <ul id="groupFiles" class="space-y-2 text-sm text-blue-600"></ul>
            <p class="text-sm text-gray-500">Keine Dateien oder Medien vorhanden.</p>
          </div>

          <div class="p-4 text-sm text-gray-500">
            <p>Keine Aufgaben.</p>
          </div>
        </aside>
      </main>
    </div>
  </div>
</div>

{{-- Toast --}}
<div id="toast"
     class="fixed bottom-4 right-4 bg-black text-white px-4 py-2 rounded shadow-lg opacity-0 transition-opacity duration-300 z-50">
</div>

{{-- Group Modal --}}
<div id="groupModal"
     class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center"
     role="dialog" aria-modal="true" aria-label="Gruppe erstellen oder bearbeiten">
  <div class="bg-white p-6 rounded w-full max-w-lg shadow space-y-4">
    <h2 class="text-lg font-bold">Gruppe erstellen / bearbeiten</h2>
    <input id="groupName" type="text" placeholder="Gruppenname"
           class="w-full border rounded px-3 py-2 focus:ring" />
    <div id="dropzone" class="border-dashed border-2 rounded px-3 py-6 text-center cursor-pointer">
      <p class="text-sm text-gray-600">🖼 Avatar hier ablegen oder klicken</p>
      <input type="hidden" id="groupAvatarPath" />
    </div>
    <div id="userCheckboxList" class="max-h-64 overflow-y-auto border rounded p-3 text-sm space-y-2"></div>
    <div class="flex justify-end gap-2 pt-4">
      <button id="cancelGroupBtn" class="px-4 py-2 border rounded">Abbrechen</button>
      <button id="submitGroupBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Speichern</button>
    </div>
  </div>
</div>

{{-- Add members modal --}}
<div id="addMemberModal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
     role="dialog" aria-modal="true" aria-label="Mitglieder hinzufügen">
  <div class="bg-white rounded-lg shadow p-4 w-full max-w-md">
    <div class="flex justify-between items-center mb-2">
      <h3 class="text-lg font-bold">Mitglieder hinzufügen</h3>
      <button onclick="closeAddMemberModal()" aria-label="Schließen">✖</button>
    </div>
    <div class="mb-4">
      <label for="addUserSelect" class="text-sm mb-1 block">Benutzer auswählen:</label>
      <select id="addUserSelect" class="w-full border rounded p-1 select2" multiple="multiple" style="width: 100%"></select>
    </div>
    <div class="text-right">
      <button class="bg-blue-500 text-white px-4 py-1 text-sm rounded" onclick="submitAddMembers()">Hinzufügen</button>
    </div>
  </div>
</div>

{{-- Sound --}}
<audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
@endsection


@section('script')
  <script>
    // Deactivate stray Echo (if any)
    if (window.Echo?.connector?.pusher?.disconnect) { try { window.Echo.connector.pusher.disconnect(); } catch(e){} }
    window.Echo = window.Echo;

    // Feather icons
    document.addEventListener('DOMContentLoaded', () => { if (window.feather) window.feather.replace(); });

    // Mobile viewport fix for older iOS: keep 100dvh accurate on rotate/resize
    function setVHVar(){
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    setVHVar();
    window.addEventListener('resize', setVHVar);
    window.addEventListener('orientationchange', setVHVar);

    // Slide-in panes
    const chatSidebar  = document.getElementById('chatSidebar');
    const rightSidebar = document.getElementById('rightSidebar');

    window.toggleContact = function(){
      chatSidebar.classList.toggle('visible');
      // close the other to avoid overlap on tiny screens
      rightSidebar.classList.remove('visible');
    }
    window.toggleRightSidebar = function(){
      rightSidebar.classList.toggle('visible');
      chatSidebar.classList.remove('visible');
    }

    // Close panes when tapping outside (mobile)
    document.addEventListener('click', (e) => {
      const isInsideLeft  = chatSidebar.contains(e.target)  || e.target.closest('[onclick="toggleContact()"]');
      const isInsideRight = rightSidebar.contains(e.target) || e.target.closest('[onclick="toggleRightSidebar()"]');
      if (!isInsideLeft)  chatSidebar.classList.remove('visible');
      if (!isInsideRight) rightSidebar.classList.remove('visible');
    });

    // ESC closes any open pane
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        chatSidebar.classList.remove('visible');
        rightSidebar.classList.remove('visible');
      }
    });

    // Make Select2 responsive in modals
    $(function(){
      $('.select2').select2({ width: '100%' });
    });
  </script>

  {{-- App config passed to JS (unchanged) --}}
  <script>
    window.userId            = {{ auth()->id() }};
    window.csrfToken         = "{{ csrf_token() }}";
    window.employeeLocation  = "{{ asset('images/employee') }}";
    window.defaultPic        = "{{ asset('images/gender/users.png') }}";
    window.assetImages       = "{{ asset('images') }}";
    window.notificationSound = "{{ asset('notification/notification.mp3') }}";
  </script>

  @vite(['resources/js/chat.js'])
@endsection
