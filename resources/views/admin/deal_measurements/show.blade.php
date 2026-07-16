@extends('admin.layouts.app')

@section('title', 'Feinaufmaß')

@php
    $items = $measurement->items ?? collect();

    $totalCount = $items->count();
    $checkedCount = $items->where('is_checked', true)->count();
    $openCount = $totalCount - $checkedCount;
    $qtyTotal = (float) $items->sum(fn($item) => (float) ($item->qty_measurement ?? 0));

    $statusLabel = match($measurement->status) {
        'sent' => 'Gesendet',
        'completed' => 'Abgeschlossen',
        'draft' => 'Entwurf',
        default => ucfirst($measurement->status ?? 'Entwurf'),
    };

    $statusClass = match($measurement->status) {
        'completed' => 'green',
        'sent' => 'orange',
        default => 'orange',
    };
@endphp

@once
@push('style')
<style>
  :root {
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;
    --primary:var(--sa-accent);
    --primary-hover:var(--sa-accent-hover);
    --primary-light:var(--sa-accent-light);
    --blue:#74b2d4;
    --blue-light:#eff6ff;
    --success:#10b981;
    --success-light:#ecfdf5;
    --warning:#f59e0b;
    --warning-light:#fffbeb;
    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;
    --gray:#6b7280;
    --gray-light:#f3f4f6;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      color: var(--text-main);
      max-width: 1500px;
      margin: 20px auto;
      padding: 39px;
      padding-right: 79px;
  }

  .oc-header{margin-bottom:18px;margin-top:103px;}

  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }

  .oc-title{
    font-size:26px;
    font-weight:800;
    letter-spacing:-.025em;
    color:#111827;
  }

  .oc-sub{
    font-size:14px;
    color:var(--text-muted);
    margin-top:4px;
  }

  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }

  .oc-breadcrumb a{
    color:var(--text-muted);
    text-decoration:none;
    font-weight:700;
  }

  .oc-breadcrumb a:hover{color:var(--text-main);}
  .oc-breadcrumb span.current{color:#111827;font-weight:800;}

  .oc-btn{
    background:var(--primary);
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    transition:var(--transition);
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
  }

  .oc-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
  }

  .oc-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
  }

  .oc-btn-soft:hover{
    background:#f9fafb;
    color:var(--text-main);
    text-decoration:none;
  }

  .oc-btn-ic{
    width:36px;
    height:36px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:var(--text-muted);
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
  }

  .oc-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
  }

  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .oc-btn-ic.warning:hover{border-color:#f59e0b}
  .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .oc-btn-ic.success:hover{border-color:var(--success)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-inline-actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }

  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

  .oc-stat{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow-sm);
    display:flex;
    align-items:center;
    gap:12px;
    min-height:92px;
  }

  .oc-stat-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.published{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.type{background:var(--gray-light);color:var(--gray)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-stat-value{
    font-size:24px;
    font-weight:900;
    color:#111827;
    line-height:1.1;
    margin-top:4px;
  }

  .oc-stat-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
  }

  .oc-toolbar{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:14px 16px;
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:flex-end;
    justify-content:space-between;
    margin-bottom:16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-toolbar-left,
  .oc-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
  }

  .oc-toolbar-left{flex:1;}

  .oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
  }

  .oc-filter-block.search{
    flex:1;
    min-width:280px;
  }

  .oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-input{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px 10px 36px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    min-width:240px;
    width:100%;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
  }

  .oc-input:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-list-scroll{
    width:100%;
    overflow-x:auto;
    padding-bottom:8px;
  }

  .oc-list-scroll::-webkit-scrollbar{
    height:10px;
  }

  .oc-list-scroll::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:999px;
  }

  .oc-list-head{
    display:grid;
    grid-template-columns:70px minmax(320px,1.8fr) 150px 150px 130px 140px 120px 220px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px 16px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    min-width:1250px;
  }

  .oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px 0;
    min-width:1250px;
  }

  .oc-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    transition:var(--transition);
    overflow:hidden;
    margin:0 16px;
  }

  .oc-item:hover{
    border-color:var(--primary);
    box-shadow:var(--shadow);
  }

  .oc-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:center;
    grid-template-columns:70px minmax(320px,1.8fr) 150px 150px 130px 140px 120px 220px;
  }

  .oc-cell{min-width:0}

  .oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
  }

  .oc-id-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:54px;
    height:36px;
    padding:0 12px;
    border-radius:10px;
    background:var(--blue-light);
    color:var(--blue);
    font-size:13px;
    font-weight:900;
  }

  .oc-main{
    display:flex;
    flex-direction:column;
    min-width:0;
  }

  .oc-ttl{
    font-weight:800;
    font-size:15px;
    margin-bottom:4px;
    color:#111827;
  }

  .oc-subt{
    font-size:13px;
    color:var(--text-muted);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .oc-status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
  }

  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-status-pill.gray{background:#f3f4f6;color:#4b5563;}

  .oc-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-input-form,
  .oc-select{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }

  .oc-input-form:focus,
  .oc-select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    margin:16px;
  }

  .oc-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:10px;
    pointer-events:none;
  }

  .oc-toast{
    pointer-events:auto;
    min-width:280px;
    max-width:360px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    box-shadow:var(--shadow);
    padding:12px;
    display:flex;
    gap:10px;
    align-items:flex-start;
    animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
  }

  @keyframes ocToastIn{
    from{transform:translateX(100%);opacity:0}
    to{transform:translateX(0);opacity:1}
  }

  .oc-toast-ic{
    width:34px;
    height:34px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
  .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}

  .oc-toast-x{
    margin-left:auto;
    background:transparent;
    border:none;
    cursor:pointer;
    color:var(--text-muted);
  }

  .measurement-check{
    width:18px;
    height:18px;
    accent-color:var(--primary);
    cursor:pointer;
  }

  .measurement-row-saving{
    opacity:.65;
  }

  .measurement-row-saved{
    background:#ecfdf5 !important;
  }

  @media(max-width:1280px){
    .oc-list-scroll{
      overflow-x:visible;
    }

    .oc-list-head{
      display:none;
    }

    .oc-list{
      min-width:0;
    }

    .oc-item-row{
      grid-template-columns:1fr;
    }

    .oc-cell-title{
      display:block;
    }

    .oc-actions{
      justify-content:flex-start;
    }

    .oc-subt{
      white-space:normal;
    }
  }

  @media(max-width:700px){
    .oc-wrap{
      padding:22px 16px;
      padding-right:16px;
    }
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">FEINAUFMASS {{ $measurement->measurement_no ?? '#' . $measurement->id }}</div>

        <div class="oc-sub">
          Materialliste aus Auftrag und Angebot prüfen, Mengen erfassen und Positionen abschließen.
        </div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ route('deal.all.list') }}">Arbeitsvorbereitung</a>
          <span>›</span>
          <a href="{{ route('deal.measurements.index') }}">Feinaufmaß</a>
          <span>›</span>
          <span class="current">{{ $measurement->measurement_no ?? '#' . $measurement->id }}</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <a href="{{ route('deal.measurements.index') }}" class="oc-btn-soft">
          Zur Liste
        </a>

        @if($measurement->status !== 'completed')
          <form method="POST" action="{{ route('deal.measurements.complete', $measurement) }}" style="margin:0;">
            @csrf
            <button type="submit" class="oc-btn">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
              </svg>
              Abschließen
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Positionen</div>
        <div class="oc-stat-value">{{ $totalCount }}</div>
        <div class="oc-stat-sub">Materialpositionen</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Geprüft</div>
        <div class="oc-stat-value">{{ $checkedCount }}</div>
        <div class="oc-stat-sub">Abgehakte Positionen</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Offen</div>
        <div class="oc-stat-value">{{ $openCount }}</div>
        <div class="oc-stat-sub">Noch zu prüfen</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M7 12h10M10 17h4"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Menge</div>
        <div class="oc-stat-value">{{ number_format($qtyTotal, 0, ',', '.') }}</div>
        <div class="oc-stat-sub">Gesamt Feinaufmaß</div>
      </div>
    </div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block">
        <label class="oc-filter-label">Auftrag</label>
        <div class="oc-ttl">{{ $measurement->order_number ?? '-' }}</div>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Angebot</label>
        <div class="oc-ttl">{{ $measurement->offer_no ?? '-' }}</div>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Kunde</label>
        <div class="oc-ttl">
          {{ trim(($measurement->customer->name ?? '') . ' ' . ($measurement->customer->lastname ?? '')) ?: ($measurement->customer->firma ?? '-') }}
        </div>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Produkt</label>
        <div class="oc-ttl">{{ $measurement->product->article_group ?? '-' }}</div>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <span class="oc-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          id="measurementSearch"
          placeholder="Suche nach Artikel, Artikelnummer, Lieferant oder Bereich"
        >
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button type="button" class="oc-btn-soft" id="showAllRows">Alle</button>
      <button type="button" class="oc-btn-soft" id="showOpenRows">Offen</button>
      <button type="button" class="oc-btn-soft" id="showCheckedRows">Geprüft</button>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-list-scroll">
      <div class="oc-list-head">
        <div>Check</div>
        <div>Artikel</div>
        <div>Artikel-Nr.</div>
        <div>Lieferant</div>
        <div>Menge Angebot</div>
        <div>Menge Feinaufmaß</div>
        <div>Einheit</div>
        <div>Notiz</div>
      </div>

      <div class="oc-list" id="measurementItemList">
        @forelse($measurement->items as $item)
          @php
            $searchText = strtolower(trim(
              ($item->name ?? '') . ' ' .
              ($item->article_no ?? '') . ' ' .
              ($item->distributor_article_no ?? '') . ' ' .
              ($item->distributor_name ?? '') . ' ' .
              ($item->section_title ?? '')
            ));
          @endphp

          <div
            class="oc-item measurement-item"
            data-item-id="{{ $item->id }}"
            data-search="{{ $searchText }}"
            data-checked="{{ $item->is_checked ? '1' : '0' }}"
          >
            <div class="oc-item-row">
              <div class="oc-cell">
                <div class="oc-cell-title">Check</div>
                <input
                  type="checkbox"
                  class="measurement-check"
                  {{ $item->is_checked ? 'checked' : '' }}
                >
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Artikel</div>
                <div class="oc-main" style="padding-left:{{ (int) $item->depth * 20 }}px;">
                  <div class="oc-ttl">{{ $item->name ?? '-' }}</div>
                  <div class="oc-subt">
                    {{ $item->section_title ?? '-' }}
                    @if($item->item_type)
                      • {{ $item->item_type }}
                    @endif
                  </div>
                </div>
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Artikel-Nr.</div>
                <div class="oc-main">
                  <div class="oc-ttl" style="font-size:14px;">
                    {{ $item->article_no ?? $item->distributor_article_no ?? '-' }}
                  </div>
                </div>
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Lieferant</div>
                <div class="oc-main">
                  <div class="oc-ttl" style="font-size:14px;">{{ $item->distributor_name ?? '-' }}</div>
                </div>
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Menge Angebot</div>
                <span class="oc-id-badge">
                  {{ number_format((float) $item->qty_offer, 2, ',', '.') }}
                </span>
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Menge Feinaufmaß</div>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  class="oc-input-form measurement-qty"
                  value="{{ $item->qty_measurement }}"
                >
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Einheit</div>
                <div class="oc-main">
                  <div class="oc-ttl" style="font-size:14px;">{{ $item->unit ?? $item->measure ?? '-' }}</div>
                </div>
              </div>

              <div class="oc-cell">
                <div class="oc-cell-title">Notiz</div>
                <input
                  type="text"
                  class="oc-input-form measurement-note"
                  value="{{ $item->note }}"
                  placeholder="Notiz..."
                >
              </div>
            </div>
          </div>
        @empty
          <div class="oc-empty">Keine Materialpositionen gefunden.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
(function () {
  'use strict';

  const csrfToken = '{{ csrf_token() }}';
  let saveTimer = null;
  let activeFilter = 'all';

  function toast(kind, title, msg) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const icons = {
      ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
    };

    const el = document.createElement('div');
    el.className = 'oc-toast';
    el.innerHTML = `
      <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
      <div style="flex:1;">
        <p class="oc-toast-ttl">${title}</p>
        <p class="oc-toast-msg">${msg}</p>
      </div>
      <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
    `;

    wrap.appendChild(el);

    setTimeout(() => {
      try { el.remove(); } catch(e) {}
    }, 3000);
  }

  function updateMeasurementItem(row) {
    const itemId = row.dataset.itemId;

    if (!itemId) return;

    row.classList.add('measurement-row-saving');

    const qtyInput = row.querySelector('.measurement-qty');
    const noteInput = row.querySelector('.measurement-note');
    const checkInput = row.querySelector('.measurement-check');

    fetch(`/deal-measurement-items/${itemId}/update`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        qty_measurement: qtyInput ? qtyInput.value : 0,
        qty_final: qtyInput ? qtyInput.value : 0,
        is_checked: checkInput && checkInput.checked ? 1 : 0,
        note: noteInput ? noteInput.value : ''
      })
    })
    .then(async response => {
      const data = await response.json().catch(() => ({}));

      if (!response.ok || data.success === false) {
        throw new Error(data.message || 'Position konnte nicht gespeichert werden.');
      }

      row.dataset.checked = checkInput && checkInput.checked ? '1' : '0';

      row.classList.remove('measurement-row-saving');
      row.classList.add('measurement-row-saved');

      setTimeout(() => {
        row.classList.remove('measurement-row-saved');
      }, 700);
    })
    .catch(error => {
      row.classList.remove('measurement-row-saving');
      toast('bad', 'Fehler', error.message || 'Position konnte nicht gespeichert werden.');
    });
  }

  function applyFilters() {
    const term = (document.getElementById('measurementSearch')?.value || '').trim().toLowerCase();

    document.querySelectorAll('.measurement-item').forEach(row => {
      const search = row.dataset.search || '';
      const checked = row.dataset.checked === '1';

      const matchesSearch = term === '' || search.includes(term);

      let matchesFilter = true;

      if (activeFilter === 'open') {
        matchesFilter = !checked;
      }

      if (activeFilter === 'checked') {
        matchesFilter = checked;
      }

      row.style.display = matchesSearch && matchesFilter ? '' : 'none';
    });
  }

  document.addEventListener('change', function (e) {
    const check = e.target.closest('.measurement-check');

    if (check) {
      const row = check.closest('.measurement-item');
      updateMeasurementItem(row);
      setTimeout(applyFilters, 200);
    }
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('.measurement-qty, .measurement-note')) {
      const row = e.target.closest('.measurement-item');

      clearTimeout(saveTimer);

      saveTimer = setTimeout(function () {
        updateMeasurementItem(row);
      }, 500);
    }

    if (e.target.matches('#measurementSearch')) {
      applyFilters();
    }
  });

  document.getElementById('showAllRows')?.addEventListener('click', function () {
    activeFilter = 'all';
    applyFilters();
  });

  document.getElementById('showOpenRows')?.addEventListener('click', function () {
    activeFilter = 'open';
    applyFilters();
  });

  document.getElementById('showCheckedRows')?.addEventListener('click', function () {
    activeFilter = 'checked';
    applyFilters();
  });

  @if(session('success'))
    toast('ok', 'Gespeichert', @json(session('success')));
  @endif
})();
</script>
@endpush
@endonce