@extends('admin.layouts.app')
@section('title', 'Lagerausgabe')

@section('style')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
:root{
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
  --danger-light:#fef2f2;
  --gray:#6b7280;
  --gray-light:#f3f4f6;
  --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
  --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
  --radius:14px;
  --transition:all .2s ease-in-out;
}

.oc-wrap{
  font-family:Inter,system-ui,-apple-system,sans-serif;
  color:var(--text-main); 
}

.oc-header{margin-bottom:18px;}
.oc-titlebar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
.oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
.oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
.oc-breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:10px;font-size:13px;color:var(--text-muted);}
.oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
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
  display:inline-flex;
  align-items:center;
  gap:8px;
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
.oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
.oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
.oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
.oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}

.oc-analytics{
  display:grid;
  grid-template-columns:repeat(5,minmax(0,1fr));
  gap:14px;
  margin-bottom:18px;
}
@media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:700px){.oc-analytics{grid-template-columns:1fr;}}

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
  font-weight:900;
}
.oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
.oc-stat-icon.published{background:var(--success-light);color:var(--success)}
.oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
.oc-stat-icon.danger{background:var(--danger-light);color:var(--danger)}
.oc-stat-icon.type{background:var(--gray-light);color:var(--gray)}
.oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
.oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
.oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

.oc-tabs{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  margin-bottom:16px;
}
.oc-tab{
  background:#fff;
  border:1px solid var(--border);
  color:var(--text-main);
  padding:10px 16px;
  border-radius:999px;
  font-weight:900;
  cursor:pointer;
}
.oc-tab.active{
  background:#111827;
  color:#fff;
  border-color:#111827;
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
.oc-toolbar-left,.oc-toolbar-right{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
.oc-toolbar-left{flex:1;}
.oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:180px;}
.oc-filter-block.search{flex:1;min-width:280px;}
.oc-filter-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}

.oc-input,.oc-select{
  background:#f9fafb;
  border:1px solid var(--border);
  border-radius:8px;
  padding:10px 12px;
  font-size:14px;
  outline:none;
  transition:var(--transition);
  min-width:180px;
  width:100%;
}
.oc-input:focus,.oc-select:focus{
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
  gap:14px;
  align-items:center;
  padding:16px 16px 10px;
  color:var(--text-muted);
  font-size:11px;
  font-weight:900;
  text-transform:uppercase;
  letter-spacing:.06em;
}
.oc-products-head{
  grid-template-columns:80px minmax(220px,1.4fr) 130px 150px 150px 130px 120px 170px;
}
.oc-requests-head{
  grid-template-columns:80px minmax(220px,1.3fr) 100px minmax(180px,1fr) 170px 170px 130px 170px;
}
@media(max-width:1280px){.oc-list-head{display:none;}}

.oc-list{display:flex;flex-direction:column;gap:12px;padding:0 0 16px;}
.oc-item{
  background:var(--card-bg);
  border:1px solid var(--border);
  border-radius:var(--radius);
  transition:var(--transition);
  overflow:hidden;
  margin:0 16px;
}
.oc-item:hover{border-color:var(--primary);box-shadow:var(--shadow);}

.oc-item-row{
  padding:16px;
  display:grid;
  gap:16px;
  align-items:center;
}
.oc-product-row{
  grid-template-columns:80px minmax(220px,1.4fr) 130px 150px 150px 130px 120px 170px;
}
.oc-request-row{
  grid-template-columns:80px minmax(220px,1.3fr) 100px minmax(180px,1fr) 170px 170px 130px 170px;
}
@media(max-width:1280px){.oc-item-row{grid-template-columns:1fr;}}

.oc-cell{min-width:0}
.oc-cell-title{
  font-size:11px;
  font-weight:800;
  color:var(--text-muted);
  text-transform:uppercase;
  margin-bottom:4px;
  display:none;
}
@media(max-width:1280px){.oc-cell-title{display:block;}}

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
.oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
.oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
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
.oc-status-pill.red{background:#fef2f2;color:#b91c1c;}
.oc-status-pill.gray{background:#f3f4f6;color:#374151;}

.oc-actions{
  display:flex;
  align-items:center;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:wrap;
}
@media(max-width:1280px){.oc-actions{justify-content:flex-start;}}

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
.oc-modal-backdrop.open{opacity:1;pointer-events:auto;}
.oc-modal{
  width:100%;
  max-width:760px;
  background:#fff;
  border:1px solid rgba(229,231,235,.9);
  border-radius:16px;
  box-shadow:var(--shadow);
  overflow:hidden;
}
.oc-modal-h{
  display:flex;
  gap:12px;
  align-items:center;
  justify-content:space-between;
  padding:16px 18px;
  border-bottom:1px solid var(--border);
  background:#fafafa;
}
.oc-modal-ttl{font-weight:900;font-size:16px;line-height:1.2;margin:0;color:#111827;}
.oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto;}
.oc-modal-f{
  padding:14px 18px;
  border-top:1px solid var(--border);
  background:#fafafa;
  display:flex;
  gap:10px;
  justify-content:flex-end;
  flex-wrap:wrap;
}
.oc-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;}
@media(max-width:760px){.oc-form-grid{grid-template-columns:1fr;}}
.oc-form-group{margin-bottom:16px;}
.oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
.oc-input-form,.oc-select-form{
  width:100%;
  padding:10px 12px;
  border-radius:8px;
  border:1px solid var(--border);
  background:#fff;
  font-size:14px;
  outline:none;
}
.oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

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
}
.oc-toast-ic{
  width:34px;
  height:34px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  flex:0 0 auto;
  font-weight:900;
}
.oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
.oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
.oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
.oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0;line-height:1.4}
.oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted);}
.d-none{display:none!important;}
</style>
@endsection

@section('content')
<div class="oc-wrap">
  {{-- CI-Vereinheitlichung 2026-07-15 (Welle 2): Alt-Kopf durch das gemeinsame Bauteil ersetzt. --}}
  <x-page-head title="Lagerausgabe"
      sub="Produkte suchen, Lagerausgabe erstellen und Anfragen zentral verwalten."
      current="Lagerausgabe" />


  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">#</div>
      <div>
        <div class="oc-stat-label">Produkte</div>
        <div class="oc-stat-value" id="statTotalProducts">0</div>
        <div class="oc-stat-sub">Im Lager erfasst</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">✓</div>
      <div>
        <div class="oc-stat-label">Verfügbar</div>
        <div class="oc-stat-value" id="statAvailableProducts">0</div>
        <div class="oc-stat-sub">Mit Bestand</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon danger">!</div>
      <div>
        <div class="oc-stat-label">Leer</div>
        <div class="oc-stat-value" id="statEmptyProducts">0</div>
        <div class="oc-stat-sub">Ohne Bestand</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">↗</div>
      <div>
        <div class="oc-stat-label">Anfragen</div>
        <div class="oc-stat-value" id="statTotalRequests">0</div>
        <div class="oc-stat-sub">Gesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">T</div>
      <div>
        <div class="oc-stat-label">Heute</div>
        <div class="oc-stat-value" id="statTodayRequests">0</div>
        <div class="oc-stat-sub">Neue Anfragen</div>
      </div>
    </div>
  </div>

  <div class="oc-tabs">
    <button type="button" class="oc-tab active" data-tab="products">Produkte im Lager</button>
    <button type="button" class="oc-tab" data-tab="requests">Anfragen</button>
  </div>

  <div id="productsPanel">
    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block search">
          <label class="oc-filter-label">Suche</label>
          <input type="text" class="oc-input" id="productSearch" placeholder="Produkt, Modell, Hersteller, Lieferant, Artikelnummer">
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Produkt</label>
          <select id="productFilter" class="oc-select">
            <option value="">Alle Produkte</option>
            @foreach($products as $product)
              <option value="{{ $product->id }}">{{ $product->product }}</option>
            @endforeach
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Bestand</label>
          <select id="stockFilter" class="oc-select">
            <option value="">Alle</option>
            <option value="available">Nur verfügbar</option>
            <option value="empty">Nur leer</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Sortierung</label>
          <select id="productSort" class="oc-select">
            <option value="product">Produkt</option>
            <option value="quantity">Bestand</option>
            <option value="brand">Hersteller</option>
            <option value="distributor">Lieferant</option>
            <option value="purchase_price">EK-Preis</option>
            <option value="id">ID</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Richtung</label>
          <select id="productDirection" class="oc-select">
            <option value="asc">Aufsteigend</option>
            <option value="desc">Absteigend</option>
          </select>
        </div>
      </div>

      <div class="oc-toolbar-right">
        <button type="button" class="oc-btn-soft" id="resetProductsBtn">Zurücksetzen</button>
        <button type="button" class="oc-btn" id="loadProductsBtn">Suchen</button>
      </div>
    </div>

    <div class="oc-card">
      <div class="oc-list-head oc-products-head">
        <div>ID</div>
        <div>Produkt</div>
        <div>Modell</div>
        <div>Hersteller</div>
        <div>Lieferant</div>
        <div>EK-Preis</div>
        <div>Bestand</div>
        <div style="text-align:right;">Aktion</div>
      </div>

      <div class="oc-list" id="productsList">
        <div class="oc-empty">Lade Produkte...</div>
      </div>
    </div>

    <div class="oc-pagination" id="productsPagination" style="display:none;"></div>
  </div>

  <div id="requestsPanel" class="d-none">
    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block search">
          <label class="oc-filter-label">Suche</label>
          <input type="text" class="oc-input" id="requestSearch" placeholder="Produkt, Mitarbeiter, Grund oder Datum">
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Status</label>
          <select id="requestStatus" class="oc-select">
            <option value="">Alle</option>
            <option value="Unpublished">Unpublished</option>
            <option value="Published">Published</option>
            <option value="Done">Done</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Sortierung</label>
          <select id="requestSort" class="oc-select">
            <option value="created_at">Erstellt</option>
            <option value="id">ID</option>
            <option value="product">Produkt</option>
            <option value="quantity">Menge</option>
            <option value="status">Status</option>
            <option value="add_date">Datum</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Richtung</label>
          <select id="requestDirection" class="oc-select">
            <option value="desc">Absteigend</option>
            <option value="asc">Aufsteigend</option>
          </select>
        </div>
      </div>

      <div class="oc-toolbar-right">
        <button type="button" class="oc-btn-soft" id="resetRequestsBtn">Zurücksetzen</button>
        <button type="button" class="oc-btn" id="loadRequestsBtn">Suchen</button>
      </div>
    </div>

    <div class="oc-card">
      <div class="oc-list-head oc-requests-head">
        <div>ID</div>
        <div>Produkt</div>
        <div>Menge</div>
        <div>Grund</div>
        <div>Antragsteller</div>
        <div>Verantwortlich</div>
        <div>Status</div>
        <div style="text-align:right;">Aktion</div>
      </div>

      <div class="oc-list" id="requestsList">
        <div class="oc-empty">Lade Anfragen...</div>
      </div>
    </div>

    <div class="oc-pagination" id="requestsPagination" style="display:none;"></div>
  </div>
</div>

<div class="oc-modal-backdrop" id="requestModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Lagerausgabe erstellen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('requestModal')">×</button>
    </div>

    <form id="requestOutForm">
      @csrf
      <input type="hidden" name="product_id" id="modalProductId">

      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Produkt</label>
            <input type="text" class="oc-input-form" id="modalProductName" readonly>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Aktueller Bestand</label>
            <input type="text" class="oc-input-form" id="modalStock" readonly>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Menge *</label>
            <input type="number" class="oc-input-form" name="quantity" id="modalQuantity" min="1" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Antragsteller *</label>
            <select name="requester_id" id="modalRequester" class="oc-select-form" required>
              <option value="">Bitte wählen</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Verantwortlich</label>
            <select name="responsible_id" id="modalResponsible" class="oc-select-form">
              <option value="">Bitte wählen</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Lagerort</label>
            <input type="text" class="oc-input-form" id="modalLocation" readonly>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Grund</label>
            <textarea name="reason" id="modalReason" class="oc-input-form" rows="4"></textarea>
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('requestModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-modal-backdrop" id="editRequestModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Anfrage bearbeiten</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('editRequestModal')">×</button>
    </div>

    <form id="editRequestForm">
      @csrf
      <input type="hidden" name="id" id="editRequestId">

      <div class="oc-modal-b">
        <div class="oc-form-group">
          <label class="oc-label">Status</label>
          <select name="status" id="editStatus" class="oc-select-form" required>
            <option value="Unpublished">Unpublished</option>
            <option value="Published">Published</option>
            <option value="Done">Done</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>

        <div class="oc-form-group">
          <label class="oc-label">Grund</label>
          <textarea name="reason" id="editReason" class="oc-input-form" rows="4"></textarea>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('editRequestModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Aktualisieren</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-toast-wrap" id="toastWrap"></div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script>
const routes = {
  analytics: @json(route('request.out.analytics')),
  products: @json(route('request.out.products')),
  requests: @json(route('request.out.requests')),
  store: @json(route('request.out.store')),
  update: @json(route('request.out.update')),
  destroyBase: @json(url('/request_out_delete')),
  purchaseCreate: @json(route('purchase.request.create')),
};

let productPage = 1;
let requestPage = 1;
let activeTab = 'products';

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
}

function openModal(id) {
  document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
  document.getElementById(id)?.classList.remove('open');
}

function toast(kind, title, msg) {
  const wrap = document.getElementById('toastWrap');
  if (!wrap) return;

  const el = document.createElement('div');
  el.className = 'oc-toast';
  el.innerHTML = `
    <div class="oc-toast-ic ${kind}">${kind === 'ok' ? '✓' : '!'}</div>
    <div style="flex:1;">
      <p class="oc-toast-ttl">${escapeHtml(title)}</p>
      <p class="oc-toast-msg">${escapeHtml(msg)}</p>
    </div>
    <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
  `;

  wrap.appendChild(el);
  setTimeout(() => {
    try { el.remove(); } catch(e) {}
  }, 4000);
}

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function loadAnalytics() {
  fetch(routes.analytics, {
    headers: { 'Accept': 'application/json' }
  })
    .then(res => res.json())
    .then(res => {
      if (!res.status) return;

      document.getElementById('statTotalProducts').textContent = res.analytics.total_products ?? 0;
      document.getElementById('statAvailableProducts').textContent = res.analytics.available_products ?? 0;
      document.getElementById('statEmptyProducts').textContent = res.analytics.empty_products ?? 0;
      document.getElementById('statTotalRequests').textContent = res.analytics.total_requests ?? 0;
      document.getElementById('statTodayRequests').textContent = res.analytics.today_requests ?? 0;
    });
}

function setTab(tab) {
  activeTab = tab;

  document.querySelectorAll('.oc-tab').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.tab === tab);
  });

  document.getElementById('productsPanel').classList.toggle('d-none', tab !== 'products');
  document.getElementById('requestsPanel').classList.toggle('d-none', tab !== 'requests');

  if (tab === 'products') loadProducts(productPage);
  if (tab === 'requests') loadRequests(requestPage);
}

function loadProducts(page = 1) {
  productPage = page;

  const params = new URLSearchParams({
    page: page,
    search: document.getElementById('productSearch').value || '',
    product_id: document.getElementById('productFilter').value || '',
    stock: document.getElementById('stockFilter').value || '',
    sort: document.getElementById('productSort').value || 'product',
    direction: document.getElementById('productDirection').value || 'asc',
  });

  document.getElementById('productsList').innerHTML = `<div class="oc-empty">Lade Produkte...</div>`;

  fetch(`${routes.products}?${params.toString()}`, {
    headers: { 'Accept': 'application/json' }
  })
    .then(res => res.json())
    .then(res => {
      if (!res.status) {
        toast('bad', 'Fehler', 'Produkte konnten nicht geladen werden.');
        return;
      }

      renderProducts(res.data);
    })
    .catch(() => toast('bad', 'Fehler', 'Serverfehler beim Laden der Produkte.'));
}

function renderProducts(paginator) {
  const list = document.getElementById('productsList');
  const items = paginator.data || [];

  if (!items.length) {
    list.innerHTML = `
      <div class="oc-empty">
        Keine Produkte gefunden.
        <br><br>
        <a href="${routes.purchaseCreate}" class="oc-btn">Kaufanfrage erstellen</a>
      </div>
    `;
    renderPagination('productsPagination', paginator, 'loadProducts');
    return;
  }

  list.innerHTML = items.map(item => {
    const qty = Number(item.quantity || 0);
    const hasStock = qty > 0;

    return `
      <div class="oc-item">
        <div class="oc-item-row oc-product-row">
          <div class="oc-cell">
            <div class="oc-cell-title">ID</div>
            <span class="oc-id-badge">#${item.id}</span>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Produkt</div>
            <div class="oc-ttl">${escapeHtml(item.product || '—')}</div>
            <div class="oc-subt">${escapeHtml(item.serial_no ? 'SN: ' + item.serial_no : 'Keine Seriennummer')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Modell</div>
            <div class="oc-ttl">${escapeHtml(item.model || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Hersteller</div>
            <div class="oc-ttl">${escapeHtml(item.brandname || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Lieferant</div>
            <div class="oc-ttl">${escapeHtml(item.distributor || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">EK-Preis</div>
            <div class="oc-ttl">${escapeHtml(item.purchase_price || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Bestand</div>
            <span class="oc-status-pill ${hasStock ? 'green' : 'red'}">
              ${hasStock ? qty + ' Stück' : 'Leer'}
            </span>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Aktion</div>
            <div class="oc-actions">
              ${
                hasStock
                  ? `<button type="button" class="oc-btn-ic primary" title="Anfrage raus" onclick='openRequestModal(${JSON.stringify(item)})'>↗</button>`
                  : `<a href="${routes.purchaseCreate}" class="oc-btn-soft">Kaufanfrage</a>`
              }
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');

  renderPagination('productsPagination', paginator, 'loadProducts');
}

function openRequestModal(item) {
  const qty = Number(item.quantity || 0);

  document.getElementById('modalProductId').value = item.id || '';
  document.getElementById('modalProductName').value = item.product || '';
  document.getElementById('modalStock').value = qty;
  document.getElementById('modalQuantity').value = '';
  document.getElementById('modalQuantity').max = qty;
  document.getElementById('modalResponsible').value = item.responsible_id || '';
  document.getElementById('modalRequester').value = '';
  document.getElementById('modalReason').value = '';

  const locationParts = [
    item.location ? 'Ort: ' + item.location : '',
    item.row ? 'Reihe: ' + item.row : '',
    item.shelf ? 'Regal: ' + item.shelf : '',
    item.serial_no ? 'SN: ' + item.serial_no : '',
  ].filter(Boolean);

  document.getElementById('modalLocation').value = locationParts.join(' | ');

  $('#modalResponsible').trigger('change');
  $('#modalRequester').trigger('change');

  openModal('requestModal');
}

function loadRequests(page = 1) {
  requestPage = page;

  const params = new URLSearchParams({
    page: page,
    search: document.getElementById('requestSearch').value || '',
    status: document.getElementById('requestStatus').value || '',
    sort: document.getElementById('requestSort').value || 'created_at',
    direction: document.getElementById('requestDirection').value || 'desc',
  });

  document.getElementById('requestsList').innerHTML = `<div class="oc-empty">Lade Anfragen...</div>`;

  fetch(`${routes.requests}?${params.toString()}`, {
    headers: { 'Accept': 'application/json' }
  })
    .then(res => res.json())
    .then(res => {
      if (!res.status) {
        toast('bad', 'Fehler', 'Anfragen konnten nicht geladen werden.');
        return;
      }

      renderRequests(res.data);
    })
    .catch(() => toast('bad', 'Fehler', 'Serverfehler beim Laden der Anfragen.'));
}

function renderRequests(paginator) {
  const list = document.getElementById('requestsList');
  const items = paginator.data || [];

  if (!items.length) {
    list.innerHTML = `<div class="oc-empty">Keine Anfragen gefunden.</div>`;
    renderPagination('requestsPagination', paginator, 'loadRequests');
    return;
  }

  list.innerHTML = items.map(item => {
    const statusClass = getStatusClass(item.status);
    const requester = `${item.requestname || ''} ${item.requestlastname || ''}`.trim() || '—';
    const responsible = `${item.rname || ''} ${item.rlastname || ''}`.trim() || '—';

    return `
      <div class="oc-item">
        <div class="oc-item-row oc-request-row">
          <div class="oc-cell">
            <div class="oc-cell-title">ID</div>
            <span class="oc-id-badge">#${item.id}</span>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Produkt</div>
            <div class="oc-ttl">${escapeHtml(item.product || '—')}</div>
            <div class="oc-subt">${escapeHtml(item.model || '')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Menge</div>
            <div class="oc-ttl">${escapeHtml(item.quantity || 0)}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Grund</div>
            <div class="oc-subt">${escapeHtml(item.reason || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Antragsteller</div>
            <div class="oc-ttl">${escapeHtml(requester)}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Verantwortlich</div>
            <div class="oc-ttl">${escapeHtml(responsible)}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Status</div>
            <span class="oc-status-pill ${statusClass}">${escapeHtml(item.status || '—')}</span>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Aktion</div>
            <div class="oc-actions">
              <button type="button" class="oc-btn-ic primary" onclick='openEditModal(${JSON.stringify(item)})'>✎</button>
              <button type="button" class="oc-btn-ic danger" onclick="deleteRequest(${item.id})">🗑</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');

  renderPagination('requestsPagination', paginator, 'loadRequests');
}

function getStatusClass(status) {
  if (status === 'Published' || status === 'Done') return 'green';
  if (status === 'Canceled') return 'red';
  if (status === 'Unpublished') return 'orange';
  return 'gray';
}

function openEditModal(item) {
  document.getElementById('editRequestId').value = item.id || '';
  document.getElementById('editStatus').value = item.status || 'Unpublished';
  document.getElementById('editReason').value = item.reason || '';

  openModal('editRequestModal');
}

function renderPagination(elementId, paginator, callbackName) {
  const wrap = document.getElementById(elementId);

  if (!paginator.last_page || paginator.last_page <= 1) {
    wrap.style.display = 'none';
    wrap.innerHTML = '';
    return;
  }

  wrap.style.display = 'block';

  let buttons = '';

  for (let i = 1; i <= paginator.last_page; i++) {
    buttons += `
      <button type="button" class="${i === paginator.current_page ? 'oc-btn' : 'oc-btn-soft'}" onclick="${callbackName}(${i})">
        ${i}
      </button>
    `;
  }

  wrap.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div style="font-size:12px;color:#6b7280;">
        Zeige <strong>${paginator.from || 0}</strong>
        bis <strong>${paginator.to || 0}</strong>
        von <strong>${paginator.total || 0}</strong> Einträgen
      </div>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">${buttons}</div>
    </div>
  `;
}

document.getElementById('requestOutForm').addEventListener('submit', function(e) {
  e.preventDefault();

  fetch(routes.store, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      'Accept': 'application/json',
    },
    body: new FormData(this),
  })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    })
    .then(data => {
      toast('ok', 'Gespeichert', data.message || 'Gespeichert.');
      closeModal('requestModal');
      loadProducts(productPage);
      loadRequests(requestPage);
      loadAnalytics();
    })
    .catch(err => {
      toast('bad', 'Fehler', err.message || 'Speichern fehlgeschlagen.');
    });
});

document.getElementById('editRequestForm').addEventListener('submit', function(e) {
  e.preventDefault();

  fetch(routes.update, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      'Accept': 'application/json',
    },
    body: new FormData(this),
  })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    })
    .then(data => {
      toast('ok', 'Aktualisiert', data.message || 'Aktualisiert.');
      closeModal('editRequestModal');
      loadRequests(requestPage);
      loadAnalytics();
    })
    .catch(err => {
      toast('bad', 'Fehler', err.message || 'Aktualisierung fehlgeschlagen.');
    });
});

function deleteRequest(id) {
  if (!confirm('Möchten Sie diesen Datensatz wirklich löschen?')) return;

  fetch(`${routes.destroyBase}/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      'Accept': 'application/json',
    },
  })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    })
    .then(data => {
      toast('ok', 'Gelöscht', data.message || 'Gelöscht.');
      loadRequests(requestPage);
      loadAnalytics();
    })
    .catch(err => {
      toast('bad', 'Fehler', err.message || 'Löschen fehlgeschlagen.');
    });
}

document.querySelectorAll('.oc-tab').forEach(btn => {
  btn.addEventListener('click', () => setTab(btn.dataset.tab));
});

document.getElementById('loadProductsBtn').addEventListener('click', () => loadProducts(1));
document.getElementById('loadRequestsBtn').addEventListener('click', () => loadRequests(1));

document.getElementById('resetProductsBtn').addEventListener('click', () => {
  document.getElementById('productSearch').value = '';
  document.getElementById('productFilter').value = '';
  document.getElementById('stockFilter').value = '';
  document.getElementById('productSort').value = 'product';
  document.getElementById('productDirection').value = 'asc';
  $('#productFilter').trigger('change');
  loadProducts(1);
});

document.getElementById('resetRequestsBtn').addEventListener('click', () => {
  document.getElementById('requestSearch').value = '';
  document.getElementById('requestStatus').value = '';
  document.getElementById('requestSort').value = 'created_at';
  document.getElementById('requestDirection').value = 'desc';
  loadRequests(1);
});

document.getElementById('productSearch').addEventListener('keyup', e => {
  if (e.key === 'Enter') loadProducts(1);
});

document.getElementById('requestSearch').addEventListener('keyup', e => {
  if (e.key === 'Enter') loadRequests(1);
});

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('oc-modal-backdrop')) {
    e.target.classList.remove('open');
  }
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
  }
});

$(document).ready(function() {
  $('#productFilter').select2();
  $('#modalRequester').select2({ dropdownParent: $('#requestModal') });
  $('#modalResponsible').select2({ dropdownParent: $('#requestModal') });

  loadAnalytics();
  loadProducts(1);
});
</script>
@endsection


@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Lagerausgabe',
        url: "{{ url()->current()}}",
        clickable: false

      }

    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush