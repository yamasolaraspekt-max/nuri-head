@extends('admin.layouts.app')
@section('title', 'Hersteller')

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$currentRoute = Route::currentRouteName();

$pageTitle = match ($currentRoute) {
  'brand.index' => 'HERSTELLER',
  'brand.architect' => 'ARCHITEKT',
  'brand.sub.contractor' => 'NACH-UNTERNEHMER',
  'brand.bank' => 'BANK',
  'brand.insurance' => 'VERSICHERUNG',
  'brand.contractor' => 'GESCHÄFTSPARTNER',
  'brand.other' => 'WEITERE PARTNER',
  default => 'HERSTELLER',
};

$purposes = [
  'PHOTOVOLTAIK',
  'BATTERIESPEICHER',
  'WÄRMEPUMPE',
  'WALLBOX',
  'ELEKTRO',
  'SANITÄR',
  'BAD',
  'BAUELEMENTE',
  'KÜCHE',
  'SOLAR CARPORT',
  'SOFTWARE',
  'HARDWARE',
];

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
$items = $isPaginator ? collect($data->items()) : collect($data);

$totalCount = $isPaginator ? $data->total() : $items->count();
$publishedCount = (int) $items->where('status', 'Published')->count();
$unpublishedCount = (int) $items->filter(fn($item) => ($item->status ?? '') !== 'Published')->count();
$typedCount = (int) $items->filter(fn($item) => !empty($item->type))->count();

$typeOptions = ['brand', 'architect', 'sub_contractor', 'contractor', 'bank', 'insurance', 'other'];

// Anzeige-Labels (technischer Wert bleibt 'brand' etc., angezeigt wird Deutsch)
$typeLabels = [
  'brand' => 'Hersteller',
  'sub_contractor' => 'Nachunternehmer',
  'architect' => 'Architekt',
  'bank' => 'Bank',
  'insurance' => 'Versicherung',
  'contractor' => 'Geschäftspartner',
  'other' => 'Weitere Partner',
];
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
    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-light:#f4fae7;
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
  }

  .oc-header{margin-bottom:18px;}
  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }
  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }
  .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
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
  .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

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
  }
  .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

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
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

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

  .oc-toolbar-left,.oc-toolbar-right{
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

  .oc-list-head{
    display:grid;
    grid-template-columns:80px 90px minmax(240px,1.5fr) minmax(140px,.9fr) minmax(140px,.9fr) 130px 130px 210px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px 16px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  @media(max-width:1280px){ .oc-list-head{display:none;} }

  .oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px 0;
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
    grid-template-columns:80px 90px minmax(240px,1.5fr) minmax(140px,.9fr) minmax(140px,.9fr) 130px 130px 210px;
  }
  @media(max-width:1280px){ .oc-item-row{grid-template-columns:1fr;} }

  .oc-cell{min-width:0}
  .oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
  }
  @media(max-width:1280px){ .oc-cell-title{display:block;} }

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

  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

  .oc-logo{
    width:54px;
    height:54px;
    border-radius:14px;
    border:1px solid var(--border);
    background:#fff;
    object-fit:contain;
    padding:6px;
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

  .oc-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-modal-backdrop{
    position:fixed;
    inset:0;
    z-index:1200;
    background:rgba(17,24,39,.55);
    backdrop-filter:blur(3px);
    opacity:0;
    pointer-events:none;
    transition:opacity .22s ease;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:18px;
  }
  .oc-modal-backdrop.open{
    opacity:1;
    pointer-events:auto;
  }

  .oc-modal{
    width:100%;
    max-width:620px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }
  .oc-modal.oc-modal-lg{max-width:760px;}
  .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}

  .oc-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }
  .oc-modal-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
  }
  .oc-modal-b{
    padding:20px 18px;
    max-height:72vh;
    overflow-y:auto;
  }
  .oc-modal-f{
    padding:14px 18px;
    border-top:1px solid var(--border);
    background:#fafafa;
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }
  @media(max-width:760px){ .oc-form-grid{grid-template-columns:1fr;} }

  .oc-form-group{margin-bottom:16px;}
  .oc-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }
  .oc-input-form,.oc-select{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }
  .oc-input-form:focus,.oc-select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }
  .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}
  .oc-thumb-preview{
    margin-top:10px;
    width:72px;
    height:72px;
    border-radius:12px;
    border:1px solid var(--border);
    object-fit:contain;
    padding:6px;
    background:#fff;
  }
  .oc-image-large{
    width:100%;
    max-height:70vh;
    object-fit:contain;
    border-radius:12px;
    border:1px solid var(--border);
    background:#fff;
    padding:10px;
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

  .oc-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-pagination .pagination{
    margin:0;
    display:flex;
    flex-wrap:wrap;
    gap:6px;
  }

  .oc-pagination .page-item .page-link{
    border-radius:10px !important;
    border:1px solid var(--border);
    color:var(--text-main);
    padding:8px 12px;
    line-height:1.1;
    box-shadow:none !important;
  }

  .oc-pagination .page-item.active .page-link{
    background:var(--primary);
    border-color:var(--primary);
    color:#fff;
  }

  .oc-pagination .page-item.disabled .page-link{
    color:#9ca3af;
    background:#f9fafb;
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
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">{{ $pageTitle }}</div>
        <div class="oc-sub">Verwalten Sie Hersteller, Logos, Kategorien, Status und Ansprechpartner zentral.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <span class="current">{{ $pageTitle }}</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <button type="button" class="oc-btn" onclick="openModal('createBrandModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
          </svg>
          Neue hinzufügen
        </button>
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
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value">{{ $totalCount }}</div>
        <div class="oc-stat-sub">Einträge insgesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Veröffentlicht</div>
        <div class="oc-stat-value">{{ $publishedCount }}</div>
        <div class="oc-stat-sub">Aktive Datensätze</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Unveröffentlicht</div>
        <div class="oc-stat-value">{{ $unpublishedCount }}</div>
        <div class="oc-stat-sub">Noch nicht aktiv</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M7 12h10M10 17h4"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Mit Typ</div>
        <div class="oc-stat-value">{{ $typedCount }}</div>
        <div class="oc-stat-sub">Kategorisierte Einträge</div>
      </div>
    </div>
  </div>

  <form action="{{ url()->current() }}" method="GET" class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          placeholder="Suche nach Name, Initial, Adresse, Zweck oder Typ"
          name="search"
          value="{{ request('search') }}"
        >
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="submit">Suchen</button>
      @if(request('search'))
        <a href="{{ url()->current() }}" class="oc-btn-soft">Zurücksetzen</a>
      @endif
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-list-head">
      <div>ID</div>
      <div>Logo</div>
      <div>Hersteller</div>
      <div>Typ</div>
      <div>Zweck</div>
      <div>Kontakt</div>
      <div>Status</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list">
      @forelse($data as $item)
        @php
  $logo = $item->image ? asset('images/brand/' . $item->image) : asset('images/icons/placeholder.svg');
  $statusClass = ($item->status ?? '') === 'Published' ? 'green' : 'orange';
  $statusLabel = ($item->status ?? '') === 'Published' ? 'Veröffentlicht' : 'Unveröffentlicht';
        @endphp

        <div class="oc-item">
          <div class="oc-item-row">
            <div class="oc-cell">
              <div class="oc-cell-title">ID</div>
              <span class="oc-id-badge">#{{ $item->id }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Logo</div>
              <button
                type="button"
                class="oc-btn-ic js-open-image"
                data-name="{{ $item->name }}"
                data-logo="{{ $logo }}"
                title="Logo anzeigen"
                style="width:auto;height:auto;padding:0;border:none;background:transparent;"
              >
                <img src="{{ $logo }}" alt="{{ $item->name }}" class="oc-logo">
              </button>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Hersteller</div>
              <div class="oc-main">
                <div class="oc-ttl">{{ $item->name }}</div>
                <div class="oc-subt">
                  {{ $item->initial ?: 'Kein Kürzel' }}
                  @if($item->address)
                    • {{ $item->address }}
                  @endif
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Typ</div>
              <div class="oc-main">
                <div class="oc-ttl" style="font-size:14px;">{{ $typeLabels[$item->type ?: 'brand'] ?? ($item->type ?: 'Hersteller') }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Zweck</div>
              <div class="oc-main">
                <div class="oc-ttl" style="font-size:14px;">{{ $item->purpose ?: '—' }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Kontakt</div>
              <div class="oc-main">
                <a href="{{ route('brand.department.index', $item->id) }}" class="oc-btn-ic primary" title="Abteilungen & Kontakte">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
                  </svg>
                </a>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Status</div>
              <span class="oc-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Aktionen</div>
              <div class="oc-actions">
                @if(($item->status ?? '') === 'Published')
                  <a href="{{ route('brand.unpublish', $item->id) }}" class="oc-btn-ic warning" title="Unpublish">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                  </a>
                @else
                  <a href="{{ route('brand.publish', $item->id) }}" class="oc-btn-ic success" title="Veröffentlichen">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 6L9 17l-5-5"/>
                    </svg>
                  </a>
                @endif

                <button
                  type="button"
                  class="oc-btn-ic primary js-open-edit"
                  title="Bearbeiten"
                  data-action="{{ route('brand.update') }}"
                  data-id="{{ $item->id }}"
                  data-name="{{ $item->name }}"
                  data-initial="{{ $item->initial }}"
                  data-type="{{ $item->type ?: 'brand' }}"
                  data-purpose="{{ $item->purpose }}"
                  data-address="{{ $item->address }}"
                  data-logo="{{ $logo }}"
                >
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>

                <a href="{{ route('brand.destroy', $item->id) }}"
                   class="oc-btn-ic danger"
                   title="Löschen"
                   onclick="return confirm('Möchten Sie diesen Datensatz wirklich löschen?')">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="oc-empty">Keine Datensätze gefunden.</div>
      @endforelse
    </div>
  </div>

  @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
    <div class="oc-pagination">
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
        <div style="font-size:12px;color:#6b7280;">
          Zeige <strong>{{ $data->firstItem() ?? 0 }}</strong>
          bis <strong>{{ $data->lastItem() ?? 0 }}</strong>
          von <strong>{{ $data->total() }}</strong> Einträgen
        </div>
        <div>
          {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  @endif
</div>

<div class="oc-modal-backdrop" id="globalImageModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl" id="globalImageTitle">Logo</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('globalImageModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b">
      <img src="" alt="" id="globalImagePreview" class="oc-image-large">
    </div>
  </div>
</div>

<div class="oc-modal-backdrop" id="globalEditBrandModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Bearbeiten</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('globalEditBrandModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('brand.update') }}" enctype="multipart/form-data" id="globalEditBrandForm">
      @csrf
      <div class="oc-modal-b">
        <input type="hidden" name="id" id="edit_brand_id">

        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Hersteller *</label>
            <input type="text" class="oc-input-form" name="name" id="edit_brand_name" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Initial</label>
            <input type="text" class="oc-input-form" name="initial" id="edit_brand_initial">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Typ</label>
            <select name="type" class="oc-select" id="edit_brand_type">
              @foreach($typeOptions as $type)
                <option value="{{ $type }}">{{ $typeLabels[$type] ?? $type }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zweckkategorie</label>
            <select name="purpose" class="oc-select" id="edit_brand_purpose">
              <option value="">Bitte wählen</option>
              @foreach($purposes as $purpose)
                <option value="{{ $purpose }}">{{ $purpose }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Adresse</label>
            <input type="text" class="oc-input-form" name="address" id="edit_brand_address">
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Logo</label>
            <input type="file" class="oc-input-form" name="image">
            <img src="" alt="" id="edit_brand_logo_preview" class="oc-thumb-preview">
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('globalEditBrandModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-modal-backdrop" id="createBrandModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Neuen Eintrag anlegen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('createBrandModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('brand.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Hersteller *</label>
            <input type="text" class="oc-input-form" name="name" value="{{ old('name') }}" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Initial</label>
            <input type="text" class="oc-input-form" name="initial" value="{{ old('initial') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Typ</label>
            <select name="type" class="oc-select">
              @foreach($typeOptions as $type)
                <option value="{{ $type }}">{{ $typeLabels[$type] ?? $type }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Zweckkategorie</label>
            <select name="purpose" class="oc-select">
              <option value="">Bitte wählen</option>
              @foreach($purposes as $purpose)
                <option value="{{ $purpose }}">{{ $purpose }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Adresse</label>
            <input type="text" class="oc-input-form" name="address" value="{{ old('address') }}">
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Logo</label>
            <input type="file" class="oc-input-form" name="image">
            <div class="oc-help">PNG, JPG, JPEG, WEBP oder SVG</div>
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('createBrandModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

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
    setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  document.addEventListener('click', function(e){
    if (e.target.classList.contains('oc-modal-backdrop')) {
      e.target.classList.remove('open');
    }
  });

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
      document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
  });

  document.addEventListener('click', function(e){
    const imageBtn = e.target.closest('.js-open-image');
    if (imageBtn) {
      const name = imageBtn.dataset.name || 'Logo';
      const logo = imageBtn.dataset.logo || '';
      const img = document.getElementById('globalImagePreview');
      const title = document.getElementById('globalImageTitle');

      img.src = logo;
      img.alt = name;
      title.textContent = `${name} — Logo`;
      openModal('globalImageModal');
      return;
    }

    const editBtn = e.target.closest('.js-open-edit');
    if (editBtn) {
      document.getElementById('edit_brand_id').value = editBtn.dataset.id || '';
      document.getElementById('edit_brand_name').value = editBtn.dataset.name || '';
      document.getElementById('edit_brand_initial').value = editBtn.dataset.initial || '';
      document.getElementById('edit_brand_type').value = editBtn.dataset.type || 'brand';
      document.getElementById('edit_brand_purpose').value = editBtn.dataset.purpose || '';
      document.getElementById('edit_brand_address').value = editBtn.dataset.address || '';
      document.getElementById('edit_brand_logo_preview').src = editBtn.dataset.logo || '';
      document.getElementById('edit_brand_logo_preview').alt = editBtn.dataset.name || '';
      openModal('globalEditBrandModal');
    }
  });

  @if(session('update_msg'))
    toast('ok', 'Aktualisiert', @json(session('update_msg')));
  @endif

  @if(session('save_msg'))
    toast('ok', 'Gespeichert', @json(session('save_msg')));
  @endif

  @if(session('delete_msg'))
    toast('bad', 'Gelöscht', @json(session('delete_msg')));
  @endif
</script>
@endpush
@endonce

@push('scripts')
  <script>
    window.GlobalBreadcrumbs =[
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Hersteller',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush