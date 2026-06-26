@extends('admin.layouts.app')
@section('title', 'PRODUKT WP')

@php
    $productTitle = $product->product ?? 'WP';
@endphp

@once
@push('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

  .wp-wrap{
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main);
    max-width: 1500px;
    margin: 20px auto;
    padding: 39px;
    padding-right: 79px;
  }

  .wp-header{margin-bottom:18px;margin-top:103px;}
  .wp-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }
  .wp-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .wp-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
  .wp-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }
  .wp-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
  .wp-breadcrumb a:hover{color:var(--text-main);}
  .wp-breadcrumb span.current{color:#111827;font-weight:800;}

  .wp-btn{
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
  .wp-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

  .wp-btn-soft{
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
  .wp-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

  .wp-btn-ic{
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
  .wp-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
  }
  .wp-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .wp-btn-ic.primary:hover{border-color:var(--primary)}
  .wp-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .wp-btn-ic.warning:hover{border-color:#f59e0b}
  .wp-btn-ic.info{color:var(--blue);border-color:#dbeafe;background:#eff6ff}
  .wp-btn-ic.info:hover{border-color:var(--blue)}
  .wp-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .wp-btn-ic.success:hover{border-color:var(--success)}
  .wp-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .wp-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .wp-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){ .wp-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .wp-analytics{grid-template-columns:1fr;} }

  .wp-stat{
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

  .wp-stat-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }
  .wp-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .wp-stat-icon.models{background:var(--success-light);color:var(--success)}
  .wp-stat-icon.temps{background:var(--warning-light);color:#d97706}
  .wp-stat-icon.form{background:var(--gray-light);color:var(--gray)}

  .wp-stat-meta{min-width:0}
  .wp-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .wp-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .wp-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

  .wp-grid{
    display:grid;
    grid-template-columns:1.15fr .85fr;
    gap:18px;
    margin-bottom:18px;
  }
  @media(max-width:1100px){ .wp-grid{grid-template-columns:1fr;} }

  .wp-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .wp-card-h{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    flex-wrap:wrap;
  }
  .wp-card-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
  }
  .wp-card-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
  }
  .wp-card-b{padding:18px;}

  .wp-form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }
  @media(max-width:760px){ .wp-form-grid{grid-template-columns:1fr;} }

  .wp-form-group{margin-bottom:16px;}
  .wp-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }
  .wp-input,.wp-select{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }
  .wp-input:focus,.wp-select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .wp-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

  .wp-inline{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
  }

  .wp-table-wrap{
    width:100%;
    overflow:auto;
    border:1px solid var(--border);
    border-radius:14px;
    background:#fff;
  }
  .wp-table{
    width:100%;
    min-width:760px;
    border-collapse:separate;
    border-spacing:0;
  }
  .wp-table thead th{
    white-space:nowrap;
    text-align:left;
    background:#f9fafb;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    padding:14px 12px;
    border-bottom:1px solid var(--border);
  }
  .wp-table tbody td{
    padding:12px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
  }
  .wp-table tbody tr:last-child td{border-bottom:none;}
  .wp-table tbody tr:hover{background:#fafafa;}

  .wp-table .input-xs{
    height:40px;
    padding:.5rem .75rem;
    font-size:.875rem;
    border:1px solid var(--border);
    border-radius:10px;
    width:100%;
    outline:none;
    transition:var(--transition);
    background:#fff;
  }
  .wp-table .input-xs:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .wp-chip{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
    background:var(--blue-light);
    color:var(--blue);
  }

  .wp-actions{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
  }

  .wp-empty{
    text-align:center;
    padding:50px 20px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
  }

  .wp-alert{
    background:var(--danger-light);
    color:#991b1b;
    border:1px solid rgba(239,68,68,.18);
    border-radius:14px;
    padding:14px 16px;
    margin-bottom:18px;
  }
  .wp-alert ul{margin:0;padding-left:18px;}

  .wp-modal-backdrop{
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
  .wp-modal-backdrop.open{
    opacity:1;
    pointer-events:auto;
  }

  .wp-modal{
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
  .wp-modal-backdrop.open .wp-modal{transform:translateY(0) scale(1)}

  .wp-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }
  .wp-modal-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
  }
  .wp-modal-b{
    padding:20px 18px;
    max-height:72vh;
    overflow-y:auto;
  }
  .wp-modal-f{
    padding:14px 18px;
    border-top:1px solid var(--border);
    background:#fafafa;
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
  }

  .wp-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:10px;
    pointer-events:none;
  }
  .wp-toast{
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
    animation:wpToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
  }
  @keyframes wpToastIn{
    from{transform:translateX(100%);opacity:0}
    to{transform:translateX(0);opacity:1}
  }
  .wp-toast-ic{
    width:34px;
    height:34px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }
  .wp-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .wp-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .wp-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
  .wp-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
  .wp-toast-x{
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
<div class="wp-wrap">
  <div class="wp-header">
    <div class="wp-titlebar">
      <div>
        <div class="wp-title">PRODUKT WP</div>
        <div class="wp-sub">Wärmepumpen-Konfigurator für Temperaturpunkte, Leistungsdaten und Variantenpflege.</div>

        <div class="wp-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ url('/product_details/'.$product_id) }}">{{ $productTitle }}</a>
          <span>›</span>
          <span class="current">WP</span>
        </div>
      </div>

      <div class="wp-inline">
        <button type="button" class="wp-btn" id="btnAddRowTop">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
          </svg>
          Zeile hinzufügen
        </button>

        <a href="{{ url('/product_details/'.$product_id) }}" class="wp-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6"></path>
          </svg>
          Zurück
        </a>
      </div>
    </div>
  </div>

  <div class="wp-analytics">
    <div class="wp-stat">
      <div class="wp-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
      </div>
      <div class="wp-stat-meta">
        <div class="wp-stat-label">Gesamt</div>
        <div class="wp-stat-value" id="statTotalRows">0</div>
        <div class="wp-stat-sub">Gespeicherte Temperaturpunkte</div>
      </div>
    </div>

    <div class="wp-stat">
      <div class="wp-stat-icon models">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="wp-stat-meta">
        <div class="wp-stat-label">Varianten</div>
        <div class="wp-stat-value" id="statTypes">0</div>
        <div class="wp-stat-sub">Unterschiedliche Typen</div>
      </div>
    </div>

    <div class="wp-stat">
      <div class="wp-stat-icon temps">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4 4 0 1 0 5 0Z"/>
        </svg>
      </div>
      <div class="wp-stat-meta">
        <div class="wp-stat-label">Temperaturen</div>
        <div class="wp-stat-value" id="statTempRange">—</div>
        <div class="wp-stat-sub">Min. und max. Außentemperatur</div>
      </div>
    </div>

    <div class="wp-stat">
      <div class="wp-stat-icon form">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M7 12h10M10 17h4"/>
        </svg>
      </div>
      <div class="wp-stat-meta">
        <div class="wp-stat-label">Formular</div>
        <div class="wp-stat-value" id="statPendingRows">1</div>
        <div class="wp-stat-sub">Aktuelle Zeilen vor dem Speichern</div>
      </div>
    </div>
  </div>

  @if ($errors->any())
    <div class="wp-alert">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="wp-grid">
    <div class="wp-card">
      <div class="wp-card-h">
        <div>
          <h3 class="wp-card-ttl">WP KONFIGURATOR</h3>
          <div class="wp-card-sub">Erfassen Sie manuelle Einträge oder laden Sie Standardwerte für Varianten.</div>
        </div>
      </div>

      <div class="wp-card-b">
        <div class="wp-form-grid">
          <div class="wp-form-group">
            <label class="wp-label">Liste durchsuchen</label>
            <input id="listSearch" type="text" class="wp-input" placeholder="Produkt / Typ / Temperatur / kW durchsuchen">
            <div class="wp-help">Client-seitiges Live-Filtering ohne Reload.</div>
          </div>

          <div class="wp-form-group">
            <label class="wp-label">Defaults laden</label>
            <div class="wp-inline">
              <select id="variantSelect" class="wp-select" style="min-width:220px;">
                <option value="" disabled selected>Variante wählen…</option>
                <option value="8er">8er</option>
                <option value="9er">9er</option>
                <option value="10er">10er</option>
                <option value="ALL">Alle Varianten</option>
              </select>

              <button id="btnLoadDefaults" type="button" class="wp-btn-soft">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <path d="M7 10l5 5 5-5"/>
                  <path d="M12 15V3"/>
                </svg>
                Laden
              </button>

              <button id="btnClearForm" type="button" class="wp-btn-soft">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 4v6h6"/>
                  <path d="M23 20v-6h-6"/>
                  <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15"/>
                </svg>
                Formular leeren
              </button>
            </div>
            <div class="wp-help">Wählt eine Variante und füllt alle Temperaturpunkte von −20 bis +15 °C.</div>
          </div>
        </div>

        <form id="product_form">
          <div class="wp-table-wrap">
            <table class="wp-table" id="add_d">
              <thead>
                <tr>
                  <th>Produkt</th>
                  <th>Typ</th>
                  <th>Außen Temp. in °C</th>
                  <th>Maximale Leistung in kW</th>
                  <th>Minimale Leistung in kW</th>
                  <th style="width:70px;">Aktion</th>
                </tr>
              </thead>
              <tbody>
                {{-- rows inserted by JS --}}
              </tbody>
            </table>
          </div>

          <div class="wp-inline" style="margin-top:16px;">
            <button id="btnAddRow" type="button" class="wp-btn-soft">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"></path>
              </svg>
              Zeile hinzufügen
            </button>

            <button type="button" class="wp-btn" id="submit_form">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <path d="M17 21v-8H7v8M7 3v5h8"/>
              </svg>
              Datensatz speichern
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="wp-card">
      <div class="wp-card-h">
        <div>
          <h3 class="wp-card-ttl">Schnellinfos</h3>
          <div class="wp-card-sub">Aktuelle Produktbasis und Hilfen für die Datenerfassung.</div>
        </div>
      </div>

      <div class="wp-card-b">
        <div class="wp-form-group">
          <label class="wp-label">Produkt</label>
          <div class="wp-chip" id="wpProductName">{{ $productTitle }}</div>
        </div>

        <div class="wp-form-group">
          <label class="wp-label">Produkt-ID</label>
          <div class="wp-chip">#{{ $product_id }}</div>
        </div>

        <div class="wp-form-group">
          <label class="wp-label">Empfohlene Varianten</label>
          <div class="wp-inline">
            <span class="wp-chip">8er</span>
            <span class="wp-chip">9er</span>
            <span class="wp-chip">10er</span>
          </div>
        </div>

        <div class="wp-form-group">
          <label class="wp-label">Hinweis</label>
          <div class="wp-help" style="font-size:13px;line-height:1.7;">
            Nach dem Laden von Defaults werden alle Temperaturpunkte automatisch in die Eingabetabelle eingefügt.
            Anschließend genügt ein Klick auf <strong>Datensatz speichern</strong>.
          </div>
        </div>

        <div class="wp-inline" style="margin-top:16px;">
          <button id="btnReload" type="button" class="wp-btn-soft">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M23 4v6h-6"/>
              <path d="M1 20v-6h6"/>
              <path d="M3.51 9a9 9 0 0 1 14.13-3.36L23 10"/>
              <path d="M1 14l5.36 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Neu laden
          </button>

          <button id="btnClearSearch" type="button" class="wp-btn-soft">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
            Suche löschen
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="wp-card">
    <div class="wp-card-h">
      <div>
        <h3 class="wp-card-ttl">Gespeicherte Daten</h3>
        <div class="wp-card-sub">Bestehende WP-Leistungsdaten für dieses Produkt.</div>
      </div>
    </div>

    <div class="wp-card-b">
      <div class="wp-table-wrap">
        <table class="wp-table" id="brand_table">
          <thead>
            <tr>
              <th>Produkt</th>
              <th>Typ</th>
              <th>Außen Temp. in °C</th>
              <th>Maximale Leistung in kW</th>
              <th>Minimale Leistung in kW</th>
              <th style="width:120px;">Aktion</th>
            </tr>
          </thead>
          <tbody>
            {{-- filled by fetchProductData() --}}
          </tbody>
        </table>
      </div>

      <div id="wpEmptyState" class="wp-empty" style="display:none;margin-top:16px;">
        Keine Datensätze gefunden.
      </div>
    </div>
  </div>
</div>

<div class="wp-modal-backdrop" id="editModalWrap">
  <div class="wp-modal">
    <div class="wp-modal-h">
      <h3 class="wp-modal-ttl">Eintrag bearbeiten</h3>
      <button class="wp-btn-ic" type="button" onclick="closeWpModal('editModalWrap')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form id="edit_form">
      <div class="wp-modal-b">
        <input type="hidden" name="product_wp_id" id="product_wp_id">

        <div class="wp-form-group">
          <label class="wp-label" for="edit_type">Typ</label>
          <input type="text" class="wp-input" id="edit_type" name="type">
        </div>

        <div class="wp-form-group">
          <label class="wp-label" for="edit_temp_celsius">Außen Temp. in °C</label>
          <input type="text" class="wp-input decimal-input" id="edit_temp_celsius" name="temp_celsius">
        </div>

        <div class="wp-form-group">
          <label class="wp-label" for="edit_max_kw">Maximale Leistung in kW</label>
          <input type="text" class="wp-input decimal-input" id="edit_max_kw" name="max_kw">
        </div>

        <div class="wp-form-group" style="margin-bottom:0;">
          <label class="wp-label" for="edit_min_kw">Minimale Leistung in kW</label>
          <input type="text" class="wp-input decimal-input" id="edit_min_kw" name="min_kw">
        </div>
      </div>

      <div class="wp-modal-f">
        <button type="button" class="wp-btn-soft" onclick="closeWpModal('editModalWrap')">Abbrechen</button>
        <button type="submit" class="wp-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="wp-toast-wrap" id="wp-toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
  const PRODUCT_ID   = @json($product_id);
  const PRODUCT_NAME = @json($product->product);

  const DEFAULT_PROFILES = {
    product: "Heatpump",
    variants: [
      {
        model: "8er",
        performance: [
          { aussen_temp_c: -20, max_kw: 7.8,  min_kw: 1.8 },
          { aussen_temp_c: -15, max_kw: 8.5,  min_kw: 2.0 },
          { aussen_temp_c: -10, max_kw: 8.8,  min_kw: 2.1 },
          { aussen_temp_c:  -7, max_kw: 8.9,  min_kw: 2.2 },
          { aussen_temp_c:  -2, max_kw: 9.0,  min_kw: 2.4 },
          { aussen_temp_c:   0, max_kw: 9.2,  min_kw: 2.5 },
          { aussen_temp_c:   2, max_kw: 9.6,  min_kw: 2.7 },
          { aussen_temp_c:   7, max_kw: 10.0, min_kw: 3.0 },
          { aussen_temp_c:  10, max_kw: 10.3, min_kw: 3.2 },
          { aussen_temp_c:  15, max_kw: 10.7, min_kw: 3.5 },
        ]
      },
      {
        model: "9er",
        performance: [
          { aussen_temp_c: -20, max_kw: 8.6,  min_kw: 2.2 },
          { aussen_temp_c: -15, max_kw: 9.5,  min_kw: 2.5 },
          { aussen_temp_c: -10, max_kw: 9.9,  min_kw: 2.7 },
          { aussen_temp_c:  -7, max_kw: 10.2, min_kw: 2.8 },
          { aussen_temp_c:  -2, max_kw: 10.4, min_kw: 3.0 },
          { aussen_temp_c:   0, max_kw: 10.5, min_kw: 3.0 },
          { aussen_temp_c:   2, max_kw: 10.9, min_kw: 3.2 },
          { aussen_temp_c:   7, max_kw: 11.5, min_kw: 3.5 },
          { aussen_temp_c:  10, max_kw: 11.8, min_kw: 3.7 },
          { aussen_temp_c:  15, max_kw: 12.2, min_kw: 4.0 },
        ]
      },
      {
        model: "10er",
        performance: [
          { aussen_temp_c: -20, max_kw: 9.6,  min_kw: 2.6 },
          { aussen_temp_c: -15, max_kw: 10.5, min_kw: 3.0 },
          { aussen_temp_c: -10, max_kw: 10.9, min_kw: 3.2 },
          { aussen_temp_c:  -7, max_kw: 11.2, min_kw: 3.3 },
          { aussen_temp_c:  -2, max_kw: 11.4, min_kw: 3.4 },
          { aussen_temp_c:   0, max_kw: 11.5, min_kw: 3.5 },
          { aussen_temp_c:   2, max_kw: 11.9, min_kw: 3.7 },
          { aussen_temp_c:   7, max_kw: 12.5, min_kw: 4.0 },
          { aussen_temp_c:  10, max_kw: 12.9, min_kw: 4.2 },
          { aussen_temp_c:  15, max_kw: 13.3, min_kw: 4.5 },
        ]
      }
    ]
  };

  function openWpModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeWpModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

  function wpToast(kind, title, msg) {
    const wrap = document.getElementById('wp-toast-wrap');
    if (!wrap) return;

    const icons = {
      ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
    };

    const el = document.createElement('div');
    el.className = 'wp-toast';
    el.innerHTML = `
      <div class="wp-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
      <div style="flex:1;">
        <p class="wp-toast-ttl">${title}</p>
        <p class="wp-toast-msg">${msg}</p>
      </div>
      <button class="wp-toast-x" onclick="this.parentElement.remove()">×</button>
    `;
    wrap.appendChild(el);
    setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  function decCommaToDot(v){ return String(v ?? '').replace(',', '.'); }
  function toFixedTrim(n){ return Number(n).toString(); }

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function updatePendingRows() {
    const count = document.querySelectorAll('#add_d tbody tr').length;
    const stat = document.getElementById('statPendingRows');
    if (stat) stat.textContent = count;
  }

  function updateStats(rows) {
    const total = Array.isArray(rows) ? rows.length : 0;
    const typeSet = new Set();
    const temps = [];

    rows.forEach(item => {
      if (item.type) typeSet.add(String(item.type).trim());
      const temp = parseFloat(String(item.temp_celsius).replace(',', '.'));
      if (!Number.isNaN(temp)) temps.push(temp);
    });

    document.getElementById('statTotalRows').textContent = total;
    document.getElementById('statTypes').textContent = typeSet.size;

    if (temps.length) {
      const min = Math.min(...temps);
      const max = Math.max(...temps);
      document.getElementById('statTempRange').textContent = `${min}° bis ${max}°`;
    } else {
      document.getElementById('statTempRange').textContent = '—';
    }
  }

  function makeRow(idx, model, perf){
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <input type="hidden" name="d[${idx}][product_id]" value="${PRODUCT_ID}">
      <td><input type="text" class="input-xs" value="${escapeHtml(PRODUCT_NAME)}" disabled></td>
      <td><input type="text" class="input-xs" name="d[${idx}][type]" value="${escapeHtml(model)}" placeholder="Typ"></td>
      <td><input type="text" class="input-xs decimal-input" name="d[${idx}][temp_celsius]" value="${toFixedTrim(perf.aussen_temp_c)}" placeholder="Außen Temp. in °C"></td>
      <td><input type="text" class="input-xs decimal-input" name="d[${idx}][max_kw]" value="${toFixedTrim(perf.max_kw)}" placeholder="Max kW"></td>
      <td><input type="text" class="input-xs decimal-input" name="d[${idx}][min_kw]" value="${toFixedTrim(perf.min_kw)}" placeholder="Min kW"></td>
      <td>
        <button type="button" class="wp-btn-ic danger" data-action="remove-row" title="Zeile entfernen">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
          </svg>
        </button>
      </td>
    `;
    return tr;
  }

  document.addEventListener('click', function(e){
    if (e.target.classList.contains('wp-modal-backdrop')) {
      e.target.classList.remove('open');
    }
  });

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
      document.querySelectorAll('.wp-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
  });

  $(function(){
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const $tbody = $('#add_d tbody');
    const $brandTableBody = $('#brand_table tbody');
    const $emptyState = $('#wpEmptyState');
    const $listSearch = $('#listSearch');

    let rowIndex = 0;

    function clearFormRows(){
      $tbody.empty();
      rowIndex = 0;
      updatePendingRows();
    }

    function addEmptyRow(){
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <input type="hidden" name="d[${rowIndex}][product_id]" value="${PRODUCT_ID}">
        <td><input type="text" class="input-xs" value="${escapeHtml(PRODUCT_NAME)}" disabled></td>
        <td><input type="text" class="input-xs" name="d[${rowIndex}][type]" placeholder="Typ"></td>
        <td><input type="text" class="input-xs decimal-input" name="d[${rowIndex}][temp_celsius]" placeholder="Außen Temp. in °C"></td>
        <td><input type="text" class="input-xs decimal-input" name="d[${rowIndex}][max_kw]" placeholder="Max kW"></td>
        <td><input type="text" class="input-xs decimal-input" name="d[${rowIndex}][min_kw]" placeholder="Min kW"></td>
        <td>
          <button type="button" class="wp-btn-ic danger" data-action="remove-row" title="Zeile entfernen">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
            </svg>
          </button>
        </td>
      `;
      $tbody.append(tr);
      rowIndex++;
      updatePendingRows();
    }

    function loadVariant(model){
      const variant = DEFAULT_PROFILES.variants.find(v => v.model === model);
      if(!variant){ wpToast('bad', 'Fehler', 'Variante nicht gefunden.'); return; }
      variant.performance.forEach(p=>{
        const tr = makeRow(rowIndex, model, p);
        $tbody.append(tr);
        rowIndex++;
      });
      updatePendingRows();
    }

    function loadAllVariants(){
      DEFAULT_PROFILES.variants.forEach(v=>{
        v.performance.forEach(p=>{
          const tr = makeRow(rowIndex, v.model, p);
          $tbody.append(tr);
          rowIndex++;
        });
      });
      updatePendingRows();
    }

    function normalizeDecimalsIn($root){
      $root.find('.decimal-input').each(function(){
        $(this).val(decCommaToDot($(this).val()));
      });
    }

    function filterList(){
      const q = $listSearch.val().toLowerCase().trim();
      let visibleCount = 0;

      $brandTableBody.find('tr').each(function(){
        const text = $(this).text().toLowerCase();
        const matched = !q || text.indexOf(q) !== -1;
        $(this).toggle(matched);
        if (matched) visibleCount++;
      });

      $emptyState.toggle(visibleCount === 0);
    }

    function fetchProductData(){
      $.get(@json(route('product_wp.get', $product_id)))
        .done(function(response){
          const rows = Array.isArray(response) ? response : (response.data ?? []);
          $brandTableBody.empty();

          if (!rows.length) {
            $emptyState.show();
          } else {
            $emptyState.hide();
          }

          rows.forEach(item=>{
            const tr = `
              <tr>
                <td>${escapeHtml(item.product)}</td>
                <td>${escapeHtml(item.type)}</td>
                <td>${escapeHtml(item.temp_celsius)}</td>
                <td>${escapeHtml(item.max_kw)}</td>
                <td>${escapeHtml(item.min_kw)}</td>
                <td>
                  <div class="wp-actions">
                    <button type="button" class="wp-btn-ic info editBtn"
                      data-id="${escapeHtml(item.id)}"
                      data-type="${escapeHtml(item.type)}"
                      data-temp="${escapeHtml(item.temp_celsius)}"
                      data-max="${escapeHtml(item.max_kw)}"
                      data-min="${escapeHtml(item.min_kw)}"
                      title="Bearbeiten">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>

                    <button type="button" class="wp-btn-ic danger deleteBtn" data-id="${escapeHtml(item.id)}" title="Löschen">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            `;
            $brandTableBody.append(tr);
          });

          updateStats(rows);
          filterList();
        })
        .fail(function(xhr){
          wpToast('bad', 'Fehler', 'Fehler beim Laden der Daten.');
          console.error(xhr.responseText);
        });
    }

    addEmptyRow();

    $('#btnAddRow, #btnAddRowTop').on('click', addEmptyRow);

    $('#btnClearForm').on('click', function(){
      clearFormRows();
      addEmptyRow();
    });

    $('#btnLoadDefaults').on('click', function(){
      const val = $('#variantSelect').val();
      if(!val){
        wpToast('bad', 'Hinweis', 'Bitte eine Variante wählen.');
        return;
      }
      clearFormRows();
      if(val === 'ALL'){ loadAllVariants(); }
      else { loadVariant(val); }
      wpToast('ok', 'Defaults geladen', 'Die Standardwerte wurden eingefügt. Jetzt speichern.');
    });

    $tbody.on('click', '[data-action="remove-row"]', function(){
      $(this).closest('tr').remove();
      updatePendingRows();
      if (!$tbody.find('tr').length) addEmptyRow();
    });

    $('#submit_form').on('click', function(e){
      e.preventDefault();
      normalizeDecimalsIn($('#product_form'));
      const payload = $('#product_form').serialize();

      $.post({ url: @json(route('product_wp.save')), data: payload })
        .done(function(){
          wpToast('ok', 'Gespeichert', 'Record has been saved successfully!');
          fetchProductData();
          clearFormRows();
          addEmptyRow();
        })
        .fail(function(xhr){
          wpToast('bad', 'Fehler', 'Fehler beim Speichern.');
          console.error(xhr.responseText);
        });
    });

    $listSearch.on('input', filterList);

    $('#btnClearSearch').on('click', function(){
      $listSearch.val('');
      filterList();
    });

    $('#btnReload').on('click', fetchProductData);

    $(document).on('click', '.editBtn', function(){
      const $btn = $(this);
      $('#product_wp_id').val($btn.data('id'));
      $('#edit_type').val($btn.data('type'));
      $('#edit_temp_celsius').val($btn.data('temp'));
      $('#edit_max_kw').val($btn.data('max'));
      $('#edit_min_kw').val($btn.data('min'));
      openWpModal('editModalWrap');
    });

    $('#edit_form').on('submit', function(e){
      e.preventDefault();
      normalizeDecimalsIn($('#edit_form'));
      const id = $('#product_wp_id').val();
      const payload = $(this).serialize();

      $.post(`/product_wp/${id}/update`, payload)
        .done(function(resp){
          closeWpModal('editModalWrap');
          wpToast('ok', 'Aktualisiert', resp?.success ?? 'Aktualisiert.');
          fetchProductData();
        })
        .fail(function(xhr){
          wpToast('bad', 'Fehler', 'Fehler beim Aktualisieren.');
          console.error(xhr.responseText);
        });
    });

    $(document).on('click', '.deleteBtn', function(){
      const id = $(this).data('id');
      Swal.fire({
        title: 'Sicher löschen?',
        text: 'Dieser Vorgang kann nicht rückgängig gemacht werden.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#93c21c',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ja, löschen!',
        cancelButtonText: 'Abbrechen'
      }).then(result=>{
        if(result.isConfirmed){
          $.ajax({ url: `/product_wp/${id}/delete`, type: 'DELETE' })
            .done(function(resp){
              wpToast('ok', 'Gelöscht', resp?.success ?? 'Gelöscht.');
              fetchProductData();
            })
            .fail(function(xhr){
              wpToast('bad', 'Fehler', 'Fehler beim Löschen.');
              console.error(xhr.responseText);
            });
        }
      });
    });

    fetchProductData();

    @if(Session::has('updated_msg'))
      wpToast('ok', 'Aktualisiert', @json(session('updated_msg')));
    @endif

    @if(Session::has('save_msg'))
      wpToast('ok', 'Gespeichert', @json(session('save_msg')));
    @endif

    @if(Session::has('delete_msg'))
      wpToast('bad', 'Gelöscht', @json(session('delete_msg')));
    @endif
  });
</script>
@endpush
@endonce