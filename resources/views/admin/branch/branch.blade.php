@extends('admin.layouts.app')
@section('title', 'Filialen')

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
$items = $isPaginator ? collect($data->items()) : collect($data);

$totalCount = $isPaginator ? $data->total() : $items->count();
$publishedCount = (int) $items->where('status', 'published')->count();
$unpublishedCount = (int) $items->filter(fn($item) => ($item->status ?? 'published') !== 'published')->count();
$withWebsiteCount = (int) $items->filter(fn($item) => !empty($item->web))->count();
$withFinanceCount = (int) $items->filter(fn($item) => !empty($item->iban) || !empty($item->bank) || !empty($item->bic))->count();

$defaultImage = asset('images/icons/placeholder.svg');
@endphp

@section('style')
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
    --danger-light:#fef2f2;
    --dark:#111827;
    --gray-light:#f9fafb;
    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap{
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color:var(--text-main); 
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
  .oc-stat-icon.type{background:#f3f4f6;color:#6b7280}
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
    grid-template-columns:80px 86px minmax(260px,1.7fr) minmax(180px,1fr) minmax(180px,1fr) 130px 220px;
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
    grid-template-columns:80px 86px minmax(260px,1.7fr) minmax(180px,1fr) minmax(180px,1fr) 130px 220px;
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
  .oc-subt{font-size:13px;color:var(--text-muted);line-height:1.5}

  .oc-logo{
    width:54px;
    height:54px;
    border-radius:14px;
    border:1px solid var(--border);
    background:#fff;
    object-fit:cover;
    padding:4px;
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

  .oc-color-pair{
    display:flex;
    gap:8px;
    align-items:center;
  }
  .oc-color-dot{
    width:18px;
    height:18px;
    border-radius:999px;
    border:1px solid rgba(17,24,39,.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.35);
  }

  .oc-link{
    color:#2563eb;
    text-decoration:none;
    word-break:break-word;
  }
  .oc-link:hover{text-decoration:underline;}

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
    max-width:900px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }
  .oc-modal.oc-modal-lg{max-width:980px;}
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

  .oc-form-group{margin-bottom:0;}
  .oc-form-group.full{grid-column:1 / -1;}
  .oc-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }
  .oc-input-form,.oc-select,.oc-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }
  .oc-textarea{min-height:90px;resize:vertical;}
  .oc-input-form:focus,.oc-select:focus,.oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-color-row{
    display:flex;
    gap:10px;
    align-items:center;
  }
  .oc-color-picker{
    width:52px;
    height:42px;
    border:none;
    background:transparent;
    padding:0;
    border-radius:10px;
    overflow:hidden;
    cursor:pointer;
  }

  .oc-thumb-preview{
    margin-top:10px;
    width:84px;
    height:84px;
    border-radius:12px;
    border:1px solid var(--border);
    object-fit:cover;
    padding:4px;
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
@endsection

@section('content')
<div class="oc-wrap">
  {{-- CI-Vereinheitlichung 2026-07-15: Alt-Kopf (oc-titlebar) durch das gemeinsame Bauteil ersetzt. --}}
  <x-page-head title="Filialen"
      sub="Verwalten Sie Filialen, Kontaktdaten, Farbwelt, Bankdaten und Ansprechpartner zentral."
      current="Filialen">
      <x-slot:actions>
          @if(auth()->user()->hasPermission('Employee', 'add'))
              <button type="button" class="oc-btn" onclick="openModal('createBranchModal')">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14"></path>
                  </svg>
                  Neue Filiale
              </button>
          @endif
      </x-slot:actions>
  </x-page-head>

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
        <div class="oc-stat-sub">Filialen insgesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Aktiv</div>
        <div class="oc-stat-value">{{ $publishedCount }}</div>
        <div class="oc-stat-sub">Veröffentlichte Einträge</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Inaktiv</div>
        <div class="oc-stat-value">{{ $unpublishedCount }}</div>
        <div class="oc-stat-sub">Nicht veröffentlichte Einträge</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M7 12h10M10 17h4"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Web / Finanzen</div>
        <div class="oc-stat-value">{{ $withWebsiteCount }}/{{ $withFinanceCount }}</div>
        <div class="oc-stat-sub">Mit Website / mit Bankdaten</div>
      </div>
    </div>
  </div>

  <form action="{{ route('branch.info') }}" method="GET" class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          placeholder="Suche nach Filiale, Kürzel, Stadt, E-Mail, IBAN, GF oder Website"
          name="search"
          value="{{ request('search') }}"
        >
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="submit">Suchen</button>
      @if(request('search'))
        <a href="{{ route('branch.info') }}" class="oc-btn-soft">Zurücksetzen</a>
      @endif
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-list-head">
      <div>ID</div>
      <div>Logo</div>
      <div>Filiale</div>
      <div>Kontakt</div>
      <div>Finanzen</div>
      <div>Status</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list">
      @forelse($data as $branch)
        @php
  $logo = $branch->image ? asset('storage/branches/' . $branch->image) : (!empty($branch->logo_url) ? $branch->logo_url : $defaultImage);
  $statusClass = ($branch->status ?? 'published') === 'published' ? 'green' : 'orange';
  $statusLabel = ($branch->status ?? 'published') === 'published' ? 'Aktiv' : 'Inaktiv';

  $chairmanName = !empty($branch->chairman) && isset($employeeMap[$branch->chairman]) ? $employeeMap[$branch->chairman] : null;
  $contactName = !empty($branch->contact_person) && isset($employeeMap[$branch->contact_person]) ? $employeeMap[$branch->contact_person] : null;
        @endphp

        <div class="oc-item">
          <div class="oc-item-row">
            <div class="oc-cell">
              <div class="oc-cell-title">ID</div>
              <span class="oc-id-badge">#{{ $branch->id }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Logo</div>
              <button
                type="button"
                class="oc-btn-ic js-open-image"
                data-name="{{ $branch->branch }}"
                data-logo="{{ $logo }}"
                title="Logo anzeigen"
                style="width:auto;height:auto;padding:0;border:none;background:transparent;"
              >
                <img src="{{ $logo }}" alt="{{ $branch->branch }}" class="oc-logo">
              </button>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Filiale</div>
              <div class="oc-main">
                <div class="oc-ttl">
                  <a href="{{ route('branch.profile', $branch->id) }}" style="color:inherit;text-decoration:none;">
                    {{ $branch->branch }}
                  </a>
                </div>
                <div class="oc-subt">
                  {{ $branch->initial ?: '—' }}
                  @if($branch->slug) • {{ $branch->slug }} @endif
                  @if($branch->street || $branch->postcode || $branch->city)
                    <br>{{ $branch->street }}{{ $branch->street ? ',' : '' }} {{ $branch->postcode }} {{ $branch->city }}{{ $branch->country ? ', ' . $branch->country : '' }}
                  @endif
                  @if($branch->web)
                    <br><a href="{{ $branch->web }}" target="_blank" class="oc-link">{{ $branch->web }}</a>
                  @endif
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Kontakt</div>
              <div class="oc-main">
                <div class="oc-subt">
                  @if($branch->email) {{ $branch->email }} @endif
                  @if($branch->phone) <br>{{ $branch->phone }} @endif
                  @if($branch->whatsapp) <br>WhatsApp: {{ $branch->whatsapp }} @endif
                  @if($chairmanName) <br>Vorsitz: {{ $chairmanName }} @endif
                  @if($contactName) <br>Kontakt: {{ $contactName }} @endif
                  @if($branch->gf) <br>GF: {{ $branch->gf }} @endif
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Finanzen</div>
              <div class="oc-main">
                <div class="oc-subt">
                  @if($branch->bank) {{ $branch->bank }} @endif
                  @if($branch->iban) <br>IBAN: {{ $branch->iban }} @endif
                  @if($branch->bic) <br>BIC: {{ $branch->bic }} @endif
                  @if($branch->vat) <br>USt-IdNr.: {{ $branch->vat }} @endif
                  @if($branch->tax) <br>Steuer-Nr.: {{ $branch->tax }} @endif
                  @if($branch->employee_count) <br>Mitarbeiter: {{ $branch->employee_count }} @endif
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Status</div>
              <div class="oc-main">
                <span class="oc-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                <div class="oc-color-pair" style="margin-top:10px;">
                  <span class="oc-color-dot" style="background:{{ $branch->color ?? '#93c21c' }}"></span>
                  <span class="oc-color-dot" style="background:{{ $branch->second_color ?? '#1f2937' }}"></span>
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Aktionen</div>
              <div class="oc-actions">
                <a href="{{ route('branch.profile', $branch->id) }}" class="oc-btn-ic" title="Profil">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                  </svg>
                </a>

                @if(auth()->user()->hasPermission('Employee', 'update'))
                  <button
                    type="button"
                    class="oc-btn-ic primary js-open-edit"
                    title="Bearbeiten"
                    data-id="{{ $branch->id }}"
                    data-branch="{{ $branch->branch }}"
                    data-initial="{{ $branch->initial }}"
                    data-slug="{{ $branch->slug }}"
                    data-chairman="{{ $branch->chairman }}"
                    data-contact_person="{{ $branch->contact_person }}"
                    data-street="{{ $branch->street }}"
                    data-postcode="{{ $branch->postcode }}"
                    data-city="{{ $branch->city }}"
                    data-country="{{ $branch->country }}"
                    data-email="{{ $branch->email }}"
                    data-phone="{{ $branch->phone }}"
                    data-whatsapp="{{ $branch->whatsapp }}"
                    data-web="{{ $branch->web }}"
                    data-color="{{ $branch->color ?? '#93c21c' }}"
                    data-second_color="{{ $branch->second_color ?? '#1f2937' }}"
                    data-status="{{ $branch->status ?? 'published' }}"
                    data-employee_count="{{ $branch->employee_count }}"
                    data-total_expense="{{ $branch->total_expense }}"
                    data-bank="{{ $branch->bank }}"
                    data-iban="{{ $branch->iban }}"
                    data-bic="{{ $branch->bic }}"
                    data-register="{{ $branch->register }}"
                    data-tax="{{ $branch->tax }}"
                    data-vat="{{ $branch->vat }}"
                    data-gf="{{ $branch->gf }}"
                    data-logo_url="{{ $branch->logo_url }}"
                    data-image="{{ $logo }}"
                  >
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                @endif

                @if(($branch->status ?? 'published') === 'published')
                  <a href="{{ route('branch.profile.active', $branch->id) }}" class="oc-btn-ic warning" title="Deaktivieren">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                  </a>
                @else
                  <a href="{{ route('branch.profile.active', $branch->id) }}" class="oc-btn-ic success" title="Aktivieren">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 6L9 17l-5-5"/>
                    </svg>
                  </a>
                @endif

                @if(auth()->user()->hasPermission('Employee', 'delete'))
                  <a href="{{ route('branch.destroy', $branch->id) }}"
                     class="oc-btn-ic danger"
                     title="Löschen"
                     onclick="return confirm('Möchten Sie diese Filiale wirklich löschen?')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                    </svg>
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="oc-empty">Keine Filialen gefunden.</div>
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

<div class="oc-modal-backdrop" id="createBranchModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Neue Filiale anlegen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('createBranchModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('branch.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="oc-modal-b">
        @if ($errors->any())
          <div class="alert alert-danger mb-2">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Filialname *</label>
            <input type="text" class="oc-input-form" name="branch" value="{{ old('branch') }}" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kürzel</label>
            <input type="text" class="oc-input-form" name="initial" value="{{ old('initial') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Slug</label>
            <input type="text" class="oc-input-form" name="slug" value="{{ old('slug') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Geschäftsführer</label>
            <input type="text" class="oc-input-form" name="gf" value="{{ old('gf') }}">
          </div>

          <div class="oc-form-group full">
            <label class="oc-label">Straße / Nr.</label>
            <input type="text" class="oc-input-form" name="street" value="{{ old('street') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">PLZ</label>
            <input type="text" class="oc-input-form" name="postcode" value="{{ old('postcode') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Ort</label>
            <input type="text" class="oc-input-form" name="city" value="{{ old('city') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Land</label>
            <input type="text" class="oc-input-form" name="country" value="{{ old('country') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Mitarbeiterzahl</label>
            <input type="number" min="0" class="oc-input-form" name="employee_count" value="{{ old('employee_count') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">E-Mail</label>
            <input type="email" class="oc-input-form" name="email" value="{{ old('email') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Telefon</label>
            <input type="text" class="oc-input-form" name="phone" value="{{ old('phone') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">WhatsApp</label>
            <input type="text" class="oc-input-form" name="whatsapp" value="{{ old('whatsapp') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Website</label>
            <input type="text" class="oc-input-form" name="web" value="{{ old('web') }}" placeholder="https://example.com">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Vorsitzender</label>
            <select name="chairman" class="oc-select">
              <option value="">Bitte wählen</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected(old('chairman') == $emp->id)>
                  {{ $emp->name }} {{ $emp->lastname }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kontaktperson</label>
            <select name="contact_person" class="oc-select">
              <option value="">Bitte wählen</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected(old('contact_person') == $emp->id)>
                  {{ $emp->name }} {{ $emp->lastname }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Primärfarbe</label>
            <div class="oc-color-row">
              <input type="color" class="oc-color-picker" id="create_color" value="{{ old('color', '#93c21c') }}">
              <input type="text" class="oc-input-form" name="color" id="create_color_text" value="{{ old('color', '#93c21c') }}">
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Sekundärfarbe</label>
            <div class="oc-color-row">
              <input type="color" class="oc-color-picker" id="create_second_color" value="{{ old('second_color', '#1f2937') }}">
              <input type="text" class="oc-input-form" name="second_color" id="create_second_color_text" value="{{ old('second_color', '#1f2937') }}">
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bank</label>
            <input type="text" class="oc-input-form" name="bank" value="{{ old('bank') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">IBAN</label>
            <input type="text" class="oc-input-form" name="iban" value="{{ old('iban') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">BIC</label>
            <input type="text" class="oc-input-form" name="bic" value="{{ old('bic') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Gesamtausgaben</label>
            <input type="number" step="0.01" min="0" class="oc-input-form" name="total_expense" value="{{ old('total_expense') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Register</label>
            <input type="text" class="oc-input-form" name="register" value="{{ old('register') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Steuer-Nr.</label>
            <input type="text" class="oc-input-form" name="tax" value="{{ old('tax') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">USt-IdNr.</label>
            <input type="text" class="oc-input-form" name="vat" value="{{ old('vat') }}">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select name="status" class="oc-select">
              <option value="published" @selected(old('status', 'published') === 'published')>Aktiv</option>
              <option value="unpublished" @selected(old('status') === 'unpublished')>Inaktiv</option>
            </select>
          </div>

          <div class="oc-form-group full">
            <label class="oc-label">Logo URL</label>
            <input type="text" class="oc-input-form" name="logo_url" value="{{ old('logo_url') }}">
          </div>

          <div class="oc-form-group full">
            <label class="oc-label">Bild / Logo Upload</label>
            <input type="file" class="oc-input-form" name="image">
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('createBranchModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-modal-backdrop" id="editBranchModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Filiale bearbeiten</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('editBranchModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('branch.update') }}" enctype="multipart/form-data" id="editBranchForm">
      @csrf
      <div class="oc-modal-b">
        <input type="hidden" name="id" id="edit_id">

        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Filialname *</label>
            <input type="text" class="oc-input-form" name="branch" id="edit_branch" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kürzel</label>
            <input type="text" class="oc-input-form" name="initial" id="edit_initial">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Slug</label>
            <input type="text" class="oc-input-form" name="slug" id="edit_slug">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Geschäftsführer</label>
            <input type="text" class="oc-input-form" name="gf" id="edit_gf">
          </div>

          <div class="oc-form-group full">
            <label class="oc-label">Straße / Nr.</label>
            <input type="text" class="oc-input-form" name="street" id="edit_street">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">PLZ</label>
            <input type="text" class="oc-input-form" name="postcode" id="edit_postcode">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Ort</label>
            <input type="text" class="oc-input-form" name="city" id="edit_city">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Land</label>
            <input type="text" class="oc-input-form" name="country" id="edit_country">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Mitarbeiterzahl</label>
            <input type="number" min="0" class="oc-input-form" name="employee_count" id="edit_employee_count">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">E-Mail</label>
            <input type="email" class="oc-input-form" name="email" id="edit_email">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Telefon</label>
            <input type="text" class="oc-input-form" name="phone" id="edit_phone">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">WhatsApp</label>
            <input type="text" class="oc-input-form" name="whatsapp" id="edit_whatsapp">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Website</label>
            <input type="text" class="oc-input-form" name="web" id="edit_web">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Vorsitzender</label>
            <select name="chairman" class="oc-select" id="edit_chairman">
              <option value="">Bitte wählen</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kontaktperson</label>
            <select name="contact_person" class="oc-select" id="edit_contact_person">
              <option value="">Bitte wählen</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Primärfarbe</label>
            <div class="oc-color-row">
              <input type="color" class="oc-color-picker" id="edit_color_picker" value="#93c21c">
              <input type="text" class="oc-input-form" name="color" id="edit_color">
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Sekundärfarbe</label>
            <div class="oc-color-row">
              <input type="color" class="oc-color-picker" id="edit_second_color_picker" value="#1f2937">
              <input type="text" class="oc-input-form" name="second_color" id="edit_second_color">
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Bank</label>
            <input type="text" class="oc-input-form" name="bank" id="edit_bank">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">IBAN</label>
            <input type="text" class="oc-input-form" name="iban" id="edit_iban">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">BIC</label>
            <input type="text" class="oc-input-form" name="bic" id="edit_bic">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Gesamtausgaben</label>
            <input type="number" step="0.01" min="0" class="oc-input-form" name="total_expense" id="edit_total_expense">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Register</label>
            <input type="text" class="oc-input-form" name="register" id="edit_register">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Steuer-Nr.</label>
            <input type="text" class="oc-input-form" name="tax" id="edit_tax">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">USt-IdNr.</label>
            <input type="text" class="oc-input-form" name="vat" id="edit_vat">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Logo URL</label>
            <input type="text" class="oc-input-form" name="logo_url" id="edit_logo_url">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select name="status" class="oc-select" id="edit_status">
              <option value="published">Aktiv</option>
              <option value="unpublished">Inaktiv</option>
            </select>
          </div>

          <div class="oc-form-group full">
            <label class="oc-label">Bild / Logo Upload</label>
            <input type="file" class="oc-input-form" name="image">
            <img src="" alt="" id="edit_image_preview" class="oc-thumb-preview">
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('editBranchModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
  <script>
    function openModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.add('open');
    }

    function closeModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.remove('open');
    }

    function setValue(id, value) {
      const el = document.getElementById(id);
      if (el) el.value = value || '';
    }

    function setColor(id, value, fallback) {
      const el = document.getElementById(id);
      if (!el) return;

      const color = value && /^#[0-9A-Fa-f]{6}$/.test(value) ? value : fallback;
      el.value = color;
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.addEventListener('click', function (e) {
        const imageBtn = e.target.closest('.js-open-image');

        if (imageBtn) {
          const preview = document.getElementById('globalImagePreview');
          const title = document.getElementById('globalImageTitle');

          if (preview) {
            preview.src = imageBtn.dataset.logo || '';
            preview.alt = imageBtn.dataset.name || 'Logo';
          }

          if (title) {
            title.textContent = `${imageBtn.dataset.name || 'Logo'} — Logo`;
          }

          openModal('globalImageModal');
          return;
        }

        const editBtn = e.target.closest('.js-open-edit');

        if (editBtn) {
          setValue('edit_id', editBtn.dataset.id);
          setValue('edit_branch', editBtn.dataset.branch);
          setValue('edit_initial', editBtn.dataset.initial);
          setValue('edit_slug', editBtn.dataset.slug);
          setValue('edit_chairman', editBtn.dataset.chairman);
          setValue('edit_contact_person', editBtn.dataset.contact_person);
          setValue('edit_street', editBtn.dataset.street);
          setValue('edit_postcode', editBtn.dataset.postcode);
          setValue('edit_city', editBtn.dataset.city);
          setValue('edit_country', editBtn.dataset.country);
          setValue('edit_email', editBtn.dataset.email);
          setValue('edit_phone', editBtn.dataset.phone);
          setValue('edit_whatsapp', editBtn.dataset.whatsapp);
          setValue('edit_web', editBtn.dataset.web);
          setValue('edit_employee_count', editBtn.dataset.employee_count);
          setValue('edit_total_expense', editBtn.dataset.total_expense);
          setValue('edit_bank', editBtn.dataset.bank);
          setValue('edit_iban', editBtn.dataset.iban);
          setValue('edit_bic', editBtn.dataset.bic);
          setValue('edit_register', editBtn.dataset.register);
          setValue('edit_tax', editBtn.dataset.tax);
          setValue('edit_vat', editBtn.dataset.vat);
          setValue('edit_gf', editBtn.dataset.gf);
          setValue('edit_logo_url', editBtn.dataset.logo_url);
          setValue('edit_status', editBtn.dataset.status || 'published');

          setColor('edit_color', editBtn.dataset.color, '#93c21c');
          setColor('edit_color_picker', editBtn.dataset.color, '#93c21c');
          setColor('edit_second_color', editBtn.dataset.second_color, '#1f2937');
          setColor('edit_second_color_picker', editBtn.dataset.second_color, '#1f2937');

          const preview = document.getElementById('edit_image_preview');
          if (preview) {
            preview.src = editBtn.dataset.image || '';
          }

          openModal('editBranchModal');
        }
      });

      document.addEventListener('click', function (e) {
        if (e.target.classList.contains('oc-modal-backdrop')) {
          e.target.classList.remove('open');
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => {
            el.classList.remove('open');
          });
        }
      });

      const colorPairs = [
        ['create_color', 'create_color_text'],
        ['create_second_color', 'create_second_color_text'],
        ['edit_color_picker', 'edit_color'],
        ['edit_second_color_picker', 'edit_second_color'],
      ];

      colorPairs.forEach(function (pair) {
        const picker = document.getElementById(pair[0]);
        const text = document.getElementById(pair[1]);

        if (!picker || !text) return;

        picker.addEventListener('input', function () {
          text.value = picker.value;
        });

        text.addEventListener('input', function () {
          if (/^#[0-9A-Fa-f]{6}$/.test(text.value)) {
            picker.value = text.value;
          }
        });
      });
    });
  </script>
@endsection