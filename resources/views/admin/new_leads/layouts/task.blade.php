@php
  $statusGroups = [
    'open'      => 'Offen',
    'on_going'  => 'In Bearbeitung',
    'completed' => 'Erledigt',
    'pause'     => 'Pausiert',
    'cancel'    => 'Storniert',
  ];

  // Palette request:
  // #74b2d4 (primary blue)
  // #c0d8ea (soft blue)
  // #93c21c (primary green)
  // #cfe09b (soft green)

  $statusMeta = [
    'open'      => ['accent' => '#74b2d4', 'chipBg' => '#c0d8ea', 'chipText' => '#1f5a79'],
    'on_going'  => ['accent' => '#74b2d4', 'chipBg' => '#c0d8ea', 'chipText' => '#1f5a79'],
    'completed' => ['accent' => '#93c21c', 'chipBg' => '#cfe09b', 'chipText' => '#3f5f09'],
    'pause'     => ['accent' => '#74b2d4', 'chipBg' => '#c0d8ea', 'chipText' => '#1f5a79'],
    'cancel'    => ['accent' => '#74b2d4', 'chipBg' => '#c0d8ea', 'chipText' => '#1f5a79'],
  ];
@endphp

<style>
  :root{
    --pri: #74b2d4;
    --pri-soft: #c0d8ea;
    --grn: #93c21c;
    --grn-soft: #cfe09b;

    --kb-bg: #f6fbff;
    --kb-card: #ffffff;
    --kb-border: rgba(15, 23, 42, .10);
    --kb-border2: rgba(15, 23, 42, .08);
    --kb-text: #0f172a;
    --kb-muted: rgba(15, 23, 42, .62);
    --kb-shadow: 0 12px 30px rgba(15, 23, 42, .10);
    --kb-shadow2: 0 16px 38px rgba(15, 23, 42, .14);
    --kb-radius: 18px;
  }

  /* ===== Surface ===== */
  .kb-surface{  
    padding: 8px; 
  }

  /* ===== Toolbar ===== */
  .kb-toolbar{
    display:flex; gap:12px; align-items:center; justify-content:space-between;
    padding: 12px;
    background: rgba(255,255,255,.78); 
    margin-bottom: 14px;
  }
  .kb-search{
    flex:1; display:flex; align-items:center; gap:10px;
    background: #fff;
    border: 1px solid var(--kb-border);
    border-radius: 14px;
    padding: 10px 12px;
  }
  .kb-search i{ color: rgba(15,23,42,.55); font-size: 16px; }
  .kb-search input{
    width:100%;
    border:0; outline:0;
    background: transparent;
    color: var(--kb-text);
    font-size: 14px;
  }
  .kb-search input::placeholder{ color: rgba(15,23,42,.45); }

  .kb-btn{
    border:0;
    border-radius: 14px;
    padding: 10px 14px;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, var(--pri), #5aa4ca);
    box-shadow: 0 12px 24px rgba(116,178,212,.30);
    display:inline-flex; align-items:center; gap:10px;
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    white-space: nowrap;
  }
  .kb-btn:hover{ transform: translateY(-1px); filter: brightness(1.02); box-shadow: 0 16px 30px rgba(116,178,212,.34); }
  .kb-btn:active{ transform: translateY(0); }

  /* ===== Board ===== */
  .kb-board{
    display:flex;
    gap: 14px;
    overflow-x:auto;
    padding-bottom: 8px;
    scroll-snap-type: x mandatory;
  }
  .kb-board::-webkit-scrollbar{ height: 10px; }
  .kb-board::-webkit-scrollbar-thumb{ background: rgba(15,23,42,.16); border-radius: 999px; }
  .kb-board::-webkit-scrollbar-track{ background: rgba(15,23,42,.06); border-radius: 999px; }

  /* ===== Column ===== */
  .kb-col{
    width: 330px;
    flex-shrink: 0;
    scroll-snap-align: start;
    border-radius: var(--kb-radius);
    border: 1px solid var(--kb-border2);
    background: rgba(255,255,255,.72);
    box-shadow: 0 12px 28px rgba(15,23,42,.06);
    overflow: hidden;
  }
  .kb-col__head{
    padding: 12px 12px 10px 12px;
    display:flex; align-items:center; justify-content:space-between;
    background:
      linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.70));
    border-bottom: 1px solid var(--kb-border2);
  }
  .kb-col__title{
    display:flex; align-items:center; gap:10px;
    color: var(--kb-text);
    font-weight: 900;
    font-size: 14px;
    letter-spacing: .2px;
  }
  .kb-dot{
    width:10px; height:10px; border-radius:999px;
    background: var(--accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 20%, transparent);
  }
  .kb-count{
    font-size: 12px;
    font-weight: 900;
    color: var(--kb-text);
    background: rgba(15,23,42,.06);
    border: 1px solid rgba(15,23,42,.08);
    padding: 6px 10px;
    border-radius: 999px;
  }

  .kb-col__body{
    padding: 12px;
    max-height: 75vh;
    overflow-y:auto;
    background: rgba(246,251,255,.70);
  }
  .kb-col__body::-webkit-scrollbar{ width: 10px; }
  .kb-col__body::-webkit-scrollbar-thumb{ background: rgba(15,23,42,.14); border-radius: 999px; }
  .kb-col__body::-webkit-scrollbar-track{ background: rgba(15,23,42,.05); border-radius: 999px; }

  /* ===== Card ===== */
  .kb-card{
    position: relative;
    border-radius: 16px;
    background: var(--kb-card);
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: 0 10px 18px rgba(15,23,42,.08);
    padding: 12px;
    margin-bottom: 12px;
    overflow: hidden;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }
  .kb-card::before{
    content:"";
    position:absolute; inset: 0 auto 0 0;
    width: 4px;
    background: var(--accent);
  }
  .kb-card:hover{
    transform: translateY(-1px);
    box-shadow: var(--kb-shadow2);
    border-color: rgba(15,23,42,.14);
  }

  .kb-card__top{
    display:flex; align-items:flex-start; justify-content:space-between; gap: 10px;
  }
  .kb-card__title{
    color: var(--kb-text);
    font-weight: 950;
    font-size: 14px;
    line-height: 1.25;
    margin: 0;
  }
  .kb-eye{
    color: rgba(15,23,42,.65);
    text-decoration:none;
    display:inline-flex; align-items:center; justify-content:center;
    width: 30px; height: 30px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(116,178,212,.10);
    transition: transform .15s ease, background .15s ease;
    flex-shrink:0;
  }
  .kb-eye:hover{ transform: translateY(-1px); background: rgba(116,178,212,.18); }

  .kb-desc{
    margin: 8px 0 10px 0;
    color: rgba(15,23,42,.72);
    font-size: 13px;
    line-height: 1.35;
  }

  .kb-meta{
    display:flex; flex-wrap:wrap; gap: 8px;
    margin-bottom: 10px;
  }
  .kb-chip{
    display:inline-flex; align-items:center; gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--chipBg);
    color: var(--chipText);
    border: 1px solid color-mix(in srgb, var(--chipText) 22%, transparent);
    font-size: 12px;
    font-weight: 900;
  }
  .kb-chip i{ font-size: 14px; opacity: .9; }

  .kb-people{
    display:flex; align-items:center;
    margin: 2px 0 10px 0;
  }
  .kb-people .avatar{
    list-style:none;
    margin: 0;
    width: 30px; height: 30px;
    border-radius: 999px;
    border: 2px solid rgba(255,255,255,.95);
    overflow:hidden;
    box-shadow: 0 10px 18px rgba(15,23,42,.12);
  }
  .kb-people .avatar + .avatar{ margin-left: -8px; }
  .kb-people img{ width:100%; height:100%; object-fit:cover; display:block; }

  .kb-actions{
    display:flex; align-items:center; justify-content:space-between; gap: 10px;
  }
  .kb-comment-toggle{
    cursor:pointer;
    display:inline-flex; align-items:center; gap: 8px;
    color: #1f5a79;
    font-size: 12.5px;
    font-weight: 950;
    padding: 7px 10px;
    border-radius: 12px;
    border: 1px solid rgba(116,178,212,.28);
    background: rgba(116,178,212,.10);
    transition: background .15s ease, transform .15s ease;
    user-select:none;
  }
  .kb-comment-toggle:hover{ background: rgba(116,178,212,.16); transform: translateY(-1px); }

  .kb-note{
    margin-top: 10px;
    border-top: 1px dashed rgba(15,23,42,.18);
    padding-top: 10px;
  }
  .kb-note .input-group .form-control{
    background: #fff;
    border: 1px solid rgba(15,23,42,.12);
    color: var(--kb-text);
  }
  .kb-note .input-group .form-control::placeholder{ color: rgba(15,23,42,.42); }
  .kb-note .btn{
    border-radius: 12px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--pri), #5aa4ca);
    border: 0;
  }

  .kb-empty{
    color: rgba(15,23,42,.55);
    font-weight: 800;
    font-size: 13px;
    text-align:center;
    padding: 18px 10px;
  }
</style>

<div class="kb-surface">
  {{-- Toolbar --}}
  <div class="kb-toolbar">
    <div class="kb-search">
      <i class="feather icon-search"></i>
      <input type="text" id="taskSearchInput" placeholder="Aufgabe suchen..." onkeyup="filterTasks()">
    </div>

    <button class="kb-btn create_new_task" type="button"
      data-toggle="modal"
      data-target="#taskModal"
      data-customer-id=""
      data-product-id=""
      data-alternative-id="">
      <i class="feather icon-plus"></i>
      Erstellen
    </button>
  </div>

  {{-- Board --}}
  <div class="kb-board" id="kanban-board">
    @foreach ($statusGroups as $statusKey => $statusLabel)
      @php
        $count = $tasks->where('task_status', $statusKey)->count();
        $m = $statusMeta[$statusKey] ?? ['accent'=> '#74b2d4', 'chipBg'=> '#c0d8ea', 'chipText'=> '#1f5a79'];
      @endphp

      <div class="kb-col" style="--accent: {{ $m['accent'] }}; --chipBg: {{ $m['chipBg'] }}; --chipText: {{ $m['chipText'] }};">
        <div class="kb-col__head">
          <div class="kb-col__title">
            <span class="kb-dot"></span>
            <span>{{ $statusLabel }}</span>
          </div>
          <span class="kb-count">{{ $count }}</span>
        </div>

        <div class="kb-col__body kanban-column" id="kanban-{{ $statusKey }}" data-status="{{ $statusKey }}">
          @foreach ($tasks->where('task_status', $statusKey) as $task)
            @php
              $start = $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d.m.Y') : null;
              $due   = $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') : null;

              // per-task accent: completed uses green palette even if column is blue
              $cardAccent = ($statusKey === 'completed') ? '#93c21c' : '#74b2d4';
              $chipBg     = ($statusKey === 'completed') ? '#cfe09b' : '#c0d8ea';
              $chipText   = ($statusKey === 'completed') ? '#3f5f09' : '#1f5a79';
            @endphp

            <div class="kb-card task-card"
                 style="--accent: {{ $cardAccent }}; --chipBg: {{ $chipBg }}; --chipText: {{ $chipText }};"
                 data-task-id="{{ $task->id }}"
                 data-title="{{ strtolower($task->task_title) }}">

              <div class="kb-card__top">
                <h6 class="kb-card__title">{{ $task->task_title ?? 'Keine Titel' }}</h6>

                <a class="kb-eye" href="{{ url('personal-tasks/'.$task->id.'/profile/') }}" title="Details anzeigen">
                  <i class="feather icon-eye"></i>
                </a>
              </div>

              <div class="kb-desc">
                {{ \Illuminate\Support\Str::limit($task->description, 90) }}
              </div>

              <div class="kb-meta">
                <span class="kb-chip">
                  <i class="feather icon-calendar"></i>
                  {{ $start ?: '—' }} – {{ $due ?: '—' }}
                </span>
              </div>

              <div class="kb-people">
                @foreach ($task->employees as $emp)
                  <li
                    data-toggle="tooltip"
                    data-popup="tooltip-custom"
                    data-placement="bottom"
                    data-original-title="{{ $emp->name ?? 'Unbekannt' }}"
                    class="avatar pull-up">
                    <img src="{{ asset('images/employee/'.$emp->image) }}" alt="Avatar">
                  </li>
                @endforeach
              </div>

              <div class="kb-actions">
                <span class="kb-comment-toggle" onclick="toggleTaskNote({{ $task->id }})">
                  <i class="feather icon-message-square"></i>
                  Kommentare (<span id="note-count-{{ $task->id }}">{{ $task->comments->count() }}</span>)
                </span>
              </div>

              <div id="task-note-wrapper-{{ $task->id }}" style="display:none" class="kb-note">
                <form onsubmit="submitTaskNote(event, {{ $task->id }})">
                  <div class="input-group input-group-sm mb-2">
                    <input type="text" name="comment" class="form-control" placeholder="Kommentar schreiben..." required>
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="submit">Senden</button>
                    </div>
                  </div>
                </form>

                <div id="comment-list-{{ $task->id }}"></div>
              </div>
            </div>
          @endforeach

          @if ($count === 0)
            <div class="kb-empty">Keine Aufgaben</div>
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
