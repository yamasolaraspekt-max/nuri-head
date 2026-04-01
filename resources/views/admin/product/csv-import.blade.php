@extends('admin.layouts.app')
@section('title', 'Produkt CSV Import')

@once
@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    --blue:#2563eb;
    --blue-light:#eff6ff;

    --success:#10b981;
    --success-light:#ecfdf5;

    --warning:#f59e0b;
    --warning-light:#fffbeb;

    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;

    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --transition:all .2s ease-in-out;
  }

  .oc-wrap{
    font-family:Inter,system-ui,-apple-system,sans-serif;
    color:var(--text-main);
    max-width:1550px;
    margin:20px auto;
    padding:20px;
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
  .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}
  .oc-btn.success{background:var(--success)}
  .oc-btn.success:hover{background:#0d9668}
  .oc-btn.danger{background:var(--danger)}
  .oc-btn.danger:hover{background:var(--danger-hover)}

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
  .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(5, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){
    .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));}
  }
  @media(max-width:700px){
    .oc-analytics{grid-template-columns:repeat(1, minmax(0,1fr));}
  }

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
  .oc-stat-icon.updated{background:#eef2ff;color:#4f46e5}
  .oc-stat-icon.images{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.prices{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.fail{background:var(--danger-light);color:var(--danger)}

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

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    margin-bottom:16px;
    overflow:hidden;
  }
  .oc-card-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    flex-wrap:wrap;
  }
  .oc-card-title{
    font-size:16px;
    font-weight:900;
    color:#111827;
    margin:0;
  }
  .oc-card-body{
    padding:18px;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(5, minmax(0,1fr));
    gap:16px;
  }
  @media(max-width:1200px){
    .oc-form-grid{grid-template-columns:repeat(2, minmax(0,1fr));}
  }
  @media(max-width:760px){
    .oc-form-grid{grid-template-columns:1fr;}
  }

  .oc-form-group{margin-bottom:0;}
  .oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
  .oc-input-form{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }
  .oc-input-form:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}
  .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

  .oc-select{
    padding:10px 34px 10px 12px;border-radius:8px;border:1px solid var(--border);
    background-color:#fff;font-size:13px;cursor:pointer;outline:none;appearance:none;min-height:42px;
  }

  .oc-row-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-top:16px;
    flex-wrap:wrap;
  }

  .oc-note{
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #fee2e2;
    background:#fff7f7;
    color:#991b1b;
    margin-bottom:16px;
    font-size:13px;
  }
  .oc-note ul{margin:0;padding-left:18px;}

  .oc-success{
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #bbf7d0;
    background:#f0fdf4;
    color:#166534;
    margin-bottom:16px;
    font-size:13px;
  }

  .oc-badge{
    display:inline-flex;
    align-items:center;
    padding:5px 10px;
    border-radius:999px;
    border:1px solid var(--border);
    background:#fff;
    font-size:12px;
    font-weight:800;
    color:var(--text-muted);
  }

  .oc-badge.ok{
    background:#ecfdf5;
    color:#047857;
    border-color:#a7f3d0;
  }

  .oc-badge.muted{
    background:#f8fafc;
    color:#64748b;
    border-color:#e2e8f0;
  }

  .oc-badge.warn{
    background:#fff7ed;
    color:#b45309;
    border-color:#fed7aa;
  }

  .oc-chip-wrap{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
  }

  .oc-chip{
    display:inline-flex;
    align-items:center;
    padding:6px 10px;
    border-radius:999px;
    background:#0f172a;
    color:#fff;
    font-size:12px;
    font-weight:800;
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
  }

  .oc-thumb{
    width:74px;
    height:74px;
    border-radius:14px;
    object-fit:cover;
    border:1px solid #e5e7eb;
    background:#f8fafc;
  }

  .oc-thumb-empty{
    width:74px;
    height:74px;
    border-radius:14px;
    border:1px solid #e5e7eb;
    background:#f8fafc;
    color:#94a3b8;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:800;
    text-align:center;
    line-height:1.2;
  }

  .oc-table-wrap{
    overflow:auto;
    width:100%;
  }

  .oc-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    min-width:1350px;
  }
  .oc-table thead th{
    text-align:left;
    padding:12px 12px;
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--text-muted);
    border-bottom:1px solid var(--border);
    background:#fafafa;
    white-space:nowrap;
  }
  .oc-table tbody td{
    padding:14px 12px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
  }
  .oc-table tbody tr:hover{
    background:#fcfcfc;
  }

  .oc-price-stack{
    display:flex;
    flex-direction:column;
    gap:6px;
  }

  .oc-price-line{
    font-size:13px;
    color:#334155;
    white-space:nowrap;
  }

  .oc-table-title{
    font-weight:800;
    color:#111827;
  }

  .oc-table-text{
    color:#475569;
  }

  .oc-upload-box{
    border:1px dashed #cbd5e1;
    background:#f8fafc;
    border-radius:12px;
    padding:12px;
  }

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
  }

  .select2-container{width:100% !important;}
  .select2-container .select2-selection--single{
    height:42px !important;
    border:1px solid var(--border) !important;
    border-radius:8px !important;
    background:#fff !important;
    display:flex !important;
    align-items:center !important;
    box-shadow:none !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:40px !important;
    padding-left:12px !important;
    padding-right:36px !important;
    font-size:13px !important;
    color:var(--text-main) !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow{
    height:40px !important;
    right:8px !important;
  }
  .select2-container--default.select2-container--focus .select2-selection--single{
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px var(--primary-light) !important;
  }
</style>
@endpush
@endonce

@section('content')
@php
  $summary = session('import_summary', []);
  $createdCount = (int)($summary['created'] ?? 0);
  $updatedCount = (int)($summary['updated'] ?? 0);
  $imagesCount = (int)($summary['images_downloaded'] ?? 0);
  $pricesCount = (int)($summary['prices_created'] ?? 0);
  $failedCount = (int)($summary['failed_rows'] ?? 0);
  $productsCount = isset($products) ? $products->count() : 0;

  $previewRows = $previewRows ?? [];
  $previewHeaders = $previewHeaders ?? [];
  $previewMeta = $previewMeta ?? [];
  $config = $config ?? [];
  $previewAvailable = !empty($previewRows);
@endphp

<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">Produkt CSV Import</div>
        <div class="oc-sub">CSV erst laden und prüfen, danach gezielt importieren.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ url('/admin/products') }}">Produkte</a>
          <span>›</span>
          <span class="current">CSV Import</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <a href="{{ url('/admin/products') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
          </svg>
          Zurück zu Produkten
        </a>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M4 12h16M4 17h16"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Neu erstellt</div>
        <div class="oc-stat-value">{{ $createdCount }}</div>
        <div class="oc-stat-sub">Produkte aus dem letzten Import</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon updated">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12a9 9 0 1 1-2.64-6.36"></path>
          <path d="M21 3v6h-6"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Aktualisiert</div>
        <div class="oc-stat-value">{{ $updatedCount }}</div>
        <div class="oc-stat-sub">Bestehende Datensätze aktualisiert</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon images">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="18" height="18" rx="2"></rect>
          <circle cx="8.5" cy="8.5" r="1.5"></circle>
          <path d="M21 15l-5-5L5 21"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Bilder</div>
        <div class="oc-stat-value">{{ $imagesCount }}</div>
        <div class="oc-stat-sub">Heruntergeladene Produktbilder</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon prices">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 1v22"></path>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Preiszeilen</div>
        <div class="oc-stat-value">{{ $pricesCount }}</div>
        <div class="oc-stat-sub">Verknüpfte Lieferantenpreise</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon fail">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M15 9l-6 6"></path>
          <path d="M9 9l6 6"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Fehlerhafte Zeilen</div>
        <div class="oc-stat-value">{{ $failedCount }}</div>
        <div class="oc-stat-sub">Beim letzten Import übersprungen</div>
      </div>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">CSV Datei laden</h3>
      <span class="oc-badge">{{ $productsCount }} importierte Produkte sichtbar</span>
    </div>

    <div class="oc-card-body">
      @if ($errors->any())
        <div class="oc-note">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if(session('success'))
        <div class="oc-success">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('admin.products.csv-import.preview') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">CSV Datei</label>
            <div class="oc-upload-box">
              <input type="file" name="file" accept=".csv,text/csv" required class="oc-input-form">
            </div>
            <div class="oc-help">Datei zuerst laden, damit die Inhalte unten geprüft werden können.</div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Trennzeichen</label>
            <select name="delimiter" class="oc-select">
              <option value="," {{ old('delimiter', $config['delimiter'] ?? ',') === ',' ? 'selected' : '' }}>Komma ,</option>
              <option value=";" {{ old('delimiter', $config['delimiter'] ?? ',') === ';' ? 'selected' : '' }}>Semikolon ;</option>
              <option value="|" {{ old('delimiter', $config['delimiter'] ?? ',') === '|' ? 'selected' : '' }}>Pipe |</option>
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Kopfzeile vorhanden</label>
            <select name="has_header" class="oc-select">
              <option value="1" {{ (string) old('has_header', $config['has_header'] ?? '1') === '1' ? 'selected' : '' }}>Ja</option>
              <option value="0" {{ (string) old('has_header', $config['has_header'] ?? '1') === '0' ? 'selected' : '' }}>Nein</option>
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Lieferant / Distributor</label>
            <select name="distributor_id" class="oc-select oc-select2" data-placeholder="-- Optional --">
              <option value="">-- Optional --</option>
              @foreach($distributors as $distributor)
                <option value="{{ $distributor->id }}" {{ (string) old('distributor_id', $config['distributor_id'] ?? '') === (string) $distributor->id ? 'selected' : '' }}>
                  #{{ $distributor->id }} - {{ $distributor->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Hersteller / Brand</label>
            <select name="brand_id" class="oc-select oc-select2" data-placeholder="-- Optional --">
              <option value="">-- Optional --</option>
              @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ (string) old('brand_id', $config['brand_id'] ?? '') === (string) $brand->id ? 'selected' : '' }}>
                  #{{ $brand->id }} - {{ $brand->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="oc-row-toolbar">
          <div>
            <div class="oc-label" style="margin-bottom:8px;">Erwartete CSV Spalten</div>
            <div class="oc-chip-wrap">
              <span class="oc-chip">Artikelnummer</span>
              <span class="oc-chip">Herstellernummer</span>
              <span class="oc-chip">Bezeichnung</span>
              <span class="oc-chip">Beschreibung</span>
              <span class="oc-chip">Bild</span>
              <span class="oc-chip">Quelle / Source URL</span>
              <span class="oc-chip">SVG</span>
              <span class="oc-chip">price</span>
            </div>
          </div>

          <div class="oc-inline-actions">
            <button type="submit" class="oc-btn">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <path d="M7 10l5 5 5-5"></path>
                <path d="M12 15V3"></path>
              </svg>
              CSV laden & prüfen
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

@if(!empty($previewRows))
    <div class="oc-card">
      <div class="oc-card-head">
        <h3 class="oc-card-title">CSV Vorschau</h3>
        <span class="oc-badge">{{ $previewMeta['preview_count'] ?? 0 }} Zeilen in der Vorschau</span>
      </div>

      <div class="oc-card-body">
        <div class="oc-row-toolbar" style="margin-top:0; margin-bottom:16px;">
          <div class="oc-table-text">
            Datei: <strong>{{ $config['original_name'] ?? '—' }}</strong>
          </div>

          <div class="oc-inline-actions">
            <form action="{{ route('admin.products.csv-import.reset') }}" method="POST">
              @csrf
              <button type="submit" class="oc-btn-soft">Vorschau verwerfen</button>
            </form>

            <form action="{{ route('admin.products.csv-import.confirm') }}" method="POST">
              @csrf
              <button type="submit" class="oc-btn success">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2z"></path>
                  <path d="M9 11l3 3L22 4"></path>
                </svg>
                Jetzt importieren
              </button>
            </form>
          </div>
        </div>

        <div class="oc-table-wrap">
          <table class="oc-table">
            <thead>
              <tr>
                <th>Zeile</th>
                <th>Artikel-Nr.</th>
                <th>Hersteller-Nr.</th>
                <th>Produkt</th>
                <th>Beschreibung</th>
               <th>Bild</th>
                <th>Bild Rohwert</th>
                <th>Source URL</th>
                <th>Status</th>
                <th>SVG</th>
                <th>Preis</th>
              </tr>
            </thead>
            <tbody>
              @forelse($previewRows as $row)
                <tr>
                  <td class="oc-table-text">{{ $row['row_number'] }}</td>
                  <td class="oc-table-text">{{ $row['article_no'] ?: '—' }}</td>
                  <td class="oc-table-text">{{ $row['manufacturer_no'] ?: '—' }}</td>
                  <td><div class="oc-table-title">{{ $row['product'] ?: '—' }}</div></td>
                  <td class="oc-table-text">{{ \Illuminate\Support\Str::limit($row['description'] ?: '—', 80) }}</td>
                  <td>
                        @if(!empty($row['image_preview_url']))
                            <img
                            src="{{ $row['image_preview_url'] }}"
                            alt="{{ $row['product'] ?: 'Produktbild' }}"
                            class="oc-thumb"
                            loading="lazy"
                            referrerpolicy="no-referrer"
                            >
                        @else
                            <div class="oc-thumb-empty">Kein Bild</div>
                        @endif
                        </td>

                        <td class="oc-table-text" style="max-width:260px;">
                        {{ \Illuminate\Support\Str::limit($row['image_raw'] ?: '—', 60) }}
                        </td>

                        <td class="oc-table-text" style="max-width:260px;">
                        {{ \Illuminate\Support\Str::limit($row['source_url'] ?: '—', 60) }}
                        </td>

                        <td>
                        @if(!empty($row['image_preview_url']))
                            <span class="oc-badge ok">Bild erkannt</span>
                        @else
                            <span class="oc-badge warn">Kein Bild erkannt</span>
                        @endif
                        </td>
                  <td>
                    @if($row['svg_loaded'])
                      <span class="oc-badge ok">SVG</span>
                    @else
                      <span class="oc-badge muted">Kein SVG</span>
                    @endif
                  </td>
                  <td class="oc-table-text">
                    {{ $row['price'] !== null ? number_format((float)$row['price'], 2) . ' €' : '—' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="oc-table-text">Keine Vorschauzeilen gefunden.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">Importierte Produkte</h3>
      <span class="oc-badge">{{ $productsCount }} Einträge</span>
    </div>

    <div class="oc-card-body">
      @if($products->count())
        <div class="oc-table-wrap">
          <table class="oc-table">
            <thead>
              <tr>
                <th>Bild</th>
                <th>Artikel-Nr.</th>
                <th>Produkt</th>
                <th>Modell</th>
                <th>Hersteller</th>
                <th>SVG</th>
                <th>Beschreibung</th>
                <th>Preiszeilen</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $product)
                <tr>
                  <td>
                    @if($product->image_path)
                      <img
                        src="{{ asset('images/products/' . $product->image_path) }}"
                        alt="{{ $product->product }}"
                        class="oc-thumb"
                      >
                    @else
                      <div class="oc-thumb-empty">Kein Bild</div>
                    @endif
                  </td>

                  <td class="oc-table-text">{{ $product->article_no ?? '—' }}</td>
                  <td><div class="oc-table-title">{{ $product->product }}</div></td>
                  <td class="oc-table-text">{{ $product->model ?? '—' }}</td>
                  <td class="oc-table-text">{{ $product->brand?->name ?? '—' }}</td>

                  <td>
                    @if($product->svg_content)
                      <span class="oc-badge ok">SVG geladen</span>
                    @else
                      <span class="oc-badge muted">Kein SVG</span>
                    @endif
                  </td>

                  <td class="oc-table-text" style="max-width:360px;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 120) }}
                  </td>

                  <td>
                    @if($product->distributorPrices->count())
                      <div class="oc-price-stack">
                        @foreach($product->distributorPrices as $price)
                          <div class="oc-price-line">
                            VK {{ number_format((float)$price->price, 2) }} €
                            <span style="color:#94a3b8;">/</span>
                            EK {{ number_format((float)$price->purchase_price, 2) }} €
                          </div>
                        @endforeach
                      </div>
                    @else
                      <span class="oc-table-text">Keine Preise</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="oc-empty">Noch keine Produkte aus diesem Import vorhanden.</div>
      @endif
    </div>
  </div>
</div>
@endsection

@once
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    $('.oc-select2').each(function () {
      const $el = $(this);
      $el.select2({
        width: '100%',
        allowClear: true,
        placeholder: $el.data('placeholder') || '-- Optional --'
      });
    });
  });
</script>
@endpush
@endonce