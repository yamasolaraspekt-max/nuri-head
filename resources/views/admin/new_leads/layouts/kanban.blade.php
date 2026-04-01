@php
  $stages = [
    'lead'      => 'Kunde',
    'offer'     => 'Angebot',
    'deal'      => 'Auftrag',
    'project'   => 'Montage',
    'completed' => 'Abschluss',
    'review'    => 'Auswertung',
    'archive'   => 'Archiv',
    'ticket'    => 'Ticket',
    'junk'      => 'Junk',
  ];

  // ✅ your palette
  $brand = [
    'pri'      => '#74b2d4',
    'priSoft'  => '#c0d8ea',
    'grn'      => '#93c21c',
    'grnSoft'  => '#cfe09b',
  ];

  // ✅ stage accents using your palette (no bootstrap)
  $stageMeta = [
    'lead'      => ['accent' => $brand['pri'],     'chipBg' => 'rgba(116,178,212,.18)'],
    'offer'     => ['accent' => $brand['grn'],     'chipBg' => 'rgba(147,194,28,.18)'],
    'deal'      => ['accent' => $brand['pri'],     'chipBg' => 'rgba(116,178,212,.18)'],
    'project'   => ['accent' => $brand['grn'],     'chipBg' => 'rgba(147,194,28,.18)'],
    'completed' => ['accent' => $brand['grn'],     'chipBg' => 'rgba(207,224,155,.35)'],
    'review'    => ['accent' => $brand['pri'],     'chipBg' => 'rgba(192,216,234,.45)'],
    'archive'   => ['accent' => '#94a3b8',         'chipBg' => 'rgba(148,163,184,.18)'],
    'ticket'    => ['accent' => '#fb7185',         'chipBg' => 'rgba(251,113,133,.16)'],
    'junk'      => ['accent' => '#94a3b8',         'chipBg' => 'rgba(148,163,184,.18)'],
  ];
@endphp

<style>
  :root{
    --pri:#74b2d4; --priSoft:#c0d8ea; --grn:#93c21c; --grnSoft:#cfe09b;
    --txt:#0f172a; --muted:rgba(15,23,42,.62);
    --line:rgba(15,23,42,.10);
    --card:#ffffff;
    --shadow:0 16px 40px rgba(15,23,42,.12);
    --shadow2:0 10px 26px rgba(15,23,42,.10);
    --r:18px;
  }

  .kb-wrap{
    background:
      radial-gradient(1200px 520px at 10% 0%, rgba(116,178,212,.22), transparent 60%),
      radial-gradient(900px 480px at 90% 10%, rgba(147,194,28,.18), transparent 58%),
      linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.82));
    border: 1px solid rgba(255,255,255,.6);
    border-radius: 22px;
    box-shadow: var(--shadow);
    padding: 14px;
  }

  .kb-toolbar{
    display:flex; gap:12px; align-items:center; justify-content:space-between;
    padding: 12px;
    border: 1px solid rgba(15,23,42,.08);
    border-radius: var(--r);
    background: rgba(255,255,255,.75);
    margin-bottom: 12px;
  }
  .kb-title{
    display:flex; flex-direction:column; gap:3px;
  }
  .kb-title h3{ margin:0; font-size:14px; font-weight:950; color:var(--txt); }
  .kb-title p{ margin:0; font-size:12px; font-weight:800; color:var(--muted); }

  .kb-board{
    display:flex;
    gap: 14px;
    overflow-x: auto;
    padding-bottom: 10px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
  }
  .kb-board::-webkit-scrollbar{ height: 10px; }
  .kb-board::-webkit-scrollbar-thumb{ background: rgba(15,23,42,.18); border-radius: 999px; }
  .kb-board::-webkit-scrollbar-track{ background: rgba(15,23,42,.06); border-radius: 999px; }

  .kb-col{
    width: 330px;
    flex: 0 0 330px;
    scroll-snap-align: start;
    border-radius: 20px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(255,255,255,.78);
    box-shadow: var(--shadow2);
    overflow: hidden;
    display:flex;
    flex-direction: column;
    min-height: 420px;
  }

  .kb-col__head{
    padding: 12px 12px 10px 12px;
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    background:
      radial-gradient(900px 160px at 10% 0%, color-mix(in srgb, var(--accent) 22%, transparent), transparent 60%),
      rgba(255,255,255,.75);
    border-bottom: 1px solid rgba(15,23,42,.08);
  }
  .kb-col__left{ display:flex; align-items:center; gap:10px; min-width:0; }
  .kb-dot{
    width:10px; height:10px; border-radius:999px;
    background: var(--accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 18%, transparent);
    flex:0 0 auto;
  }
  .kb-col__name{
    font-weight: 950;
    font-size: 13px;
    color: var(--txt);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .kb-count{
    font-size: 12px;
    font-weight: 950;
    color: var(--txt);
    background: rgba(15,23,42,.05);
    border: 1px solid rgba(15,23,42,.08);
    padding: 6px 10px;
    border-radius: 999px;
    flex:0 0 auto;
  }

  .kb-col__body{
    padding: 12px;
    overflow-y: auto;
    max-height: 72vh;
    min-height: 0;
    background: rgba(255,255,255,.60);
  }
  .kb-col__body::-webkit-scrollbar{ width: 10px; }
  .kb-col__body::-webkit-scrollbar-thumb{ background: rgba(15,23,42,.14); border-radius: 999px; }
  .kb-col__body::-webkit-scrollbar-track{ background: rgba(15,23,42,.05); border-radius: 999px; }

  .kb-card{
    position: relative;
    border-radius: 16px;
    background: var(--card);
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: 0 12px 26px rgba(15,23,42,.10);
    padding: 12px;
    margin-bottom: 12px;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    cursor: grab;
    overflow:hidden;
  }
  .kb-card:active{ cursor: grabbing; }
  .kb-card::before{
    content:"";
    position:absolute; inset:0 auto 0 0;
    width: 4px;
    background: var(--accent);
  }
  .kb-card:hover{
    transform: translateY(-1px);
    box-shadow: 0 16px 34px rgba(15,23,42,.14);
    border-color: rgba(15,23,42,.14);
  }

  .kb-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--chip);
    color: var(--txt);
    border: 1px solid rgba(15,23,42,.08);
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 10px;
  }

  .kb-name{
    margin: 0 0 10px 0;
    font-weight: 950;
    color: var(--txt);
    font-size: 13px;
    letter-spacing: .2px;
    text-transform: uppercase;
  }

  .kb-emp{
    display:flex; align-items:center; gap:10px;
    padding-top: 10px;
    border-top: 1px dashed rgba(15,23,42,.12);
  }
  .kb-emp img{
    width: 30px; height: 30px;
    border-radius: 999px;
    object-fit: cover;
    border: 2px solid rgba(116,178,212,.25);
  }
  .kb-emp span{
    font-size: 12.5px;
    font-weight: 900;
    color: rgba(15,23,42,.85);
  }

  .kb-empty{
    text-align:center;
    font-size: 12.5px;
    font-weight: 900;
    color: rgba(15,23,42,.55);
    padding: 18px 10px;
    border: 1px dashed rgba(15,23,42,.18);
    border-radius: 16px;
    background: rgba(255,255,255,.55);
  }

  /* Optional: sortable drop hint */
  .kb-col__body.kb-drop-active{
    outline: 2px dashed rgba(116,178,212,.55);
    outline-offset: -6px;
    border-radius: 16px;
  }
</style>

<div class="kb-wrap">
  <div class="kb-toolbar">
    <div class="kb-title">
      <h3>Kanban</h3>
      <p>Drag & Drop zwischen Spalten</p>
    </div>
  </div>

  <div class="kb-board" id="kanban-board">
    @foreach ($stages as $key => $label)
      @php
        $list = $leads->where('stage', $key);
        $count = $list->count();
        $m = $stageMeta[$key] ?? ['accent'=>$brand['pri'], 'chipBg'=>'rgba(116,178,212,.18)'];
      @endphp

      <div class="kb-col" data-stage="{{ $key }}" style="--accent: {{ $m['accent'] }}; --chip: {{ $m['chipBg'] }};">
        <div class="kb-col__head">
          <div class="kb-col__left">
            <span class="kb-dot"></span>
            <div class="kb-col__name">{{ $label }}</div>
          </div>
          <div class="kb-count">{{ $count }}</div>
        </div>

        <div class="kb-col__body kanban-dropzone" id="kanban-{{ $key }}">
          @forelse ($list as $lead)
            <div class="kb-card"
              data-id="{{ $lead->lead_product_id }}"
              data-customer-id="{{ $lead->customer_id }}"
              data-alternative-id="{{ $lead->alternative_id }}"
              data-product-id="{{ $lead->product_id }}"
              data-employee-id="{{ $lead->employee->employee_id ?? '' }}"
              data-service="{{ $lead->service }}"
              data-service-id="{{ $lead->service_id }}"
              data-department-id="{{ $lead->department_id }}">

              <div class="kb-chip">
                {{ $stages[$lead->stage] ?? ucfirst($lead->stage) }}
              </div>

              <div class="kb-name">
                {{ $lead->customer_name ?? '-' }} {{ $lead->customer_lastname ?? '' }}
              </div>

              @if(isset($lead->employee))
                <div class="kb-emp">
                  <img src="{{ asset('images/employee/' . $lead->employee->image) }}" alt="Avatar">
                  <span>{{ $lead->employee->name }} {{ $lead->employee->lastname }}</span>
                </div>
              @endif
            </div>
          @empty
            <div class="kb-empty">Keine Einträge</div>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>
</div>
