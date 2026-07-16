@extends('admin.layouts.app')
@section('title', 'Kaufanfragen')

@section('style')
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">

<style>
.oc-wrap {
    max-width: 1500px;
    margin: 20px auto;
    padding: 96px 8px;
    font-family: Inter, system-ui, sans-serif;
    color: #1f2937;
}
.oc-titlebar{display:flex;justify-content:space-between;gap:16px;align-items:flex-end;flex-wrap:wrap;margin-bottom:18px}
.oc-title{font-size:28px;font-weight:900;color:#111827}
.oc-sub{font-size:14px;color:#6b7280;margin-top:4px}
.oc-btn{background:#93c21c;color:#fff;border:0;border-radius:10px;padding:10px 16px;font-weight:900;cursor:pointer;text-decoration:none;display:inline-flex;gap:8px;align-items:center}
.oc-btn:hover{background:#7baa18;color:#fff}
.oc-btn-soft{background:#fff;color:#1f2937;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-weight:800;cursor:pointer;text-decoration:none;display:inline-flex;gap:8px;align-items:center}
.oc-btn-ic{width:36px;height:36px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer}
.oc-btn-ic.primary{color:#93c21c;background:#f4fae7;border-color:#f4fae7}
.oc-btn-ic.danger{color:#ef4444;background:#fef2f2;border-color:#fee2e2}
.oc-analytics{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:18px}
@media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,1fr)}}@media(max-width:700px){.oc-analytics{grid-template-columns:1fr}}
.oc-stat{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:16px;display:flex;gap:12px;align-items:center;box-shadow:0 1px 2px rgb(0 0 0/.05)}
.oc-stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:900;background:#eff6ff;color:#74b2d4}
.oc-stat-label{font-size:11px;font-weight:900;color:#6b7280;text-transform:uppercase}
.oc-stat-value{font-size:24px;font-weight:900;color:#111827}
.oc-stat-sub{font-size:12px;color:#6b7280}
.oc-toolbar{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:14px 16px;margin-bottom:16px;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap}
.oc-toolbar-left,.oc-toolbar-right{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.oc-toolbar-left{flex:1}
.oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:170px}
.oc-filter-block.search{flex:1;min-width:280px}
.oc-filter-label{font-size:11px;font-weight:900;color:#6b7280;text-transform:uppercase}
.oc-input,.oc-select{width:100%;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;outline:none}
.oc-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden}
.oc-list-head,.oc-item-row{display:grid;grid-template-columns:70px minmax(220px,1.3fr) 120px 150px 150px 160px 130px 150px;gap:14px;align-items:center}
.oc-list-head{padding:16px 16px 10px;font-size:11px;font-weight:900;color:#6b7280;text-transform:uppercase}
.oc-list{display:flex;flex-direction:column;gap:12px;padding-bottom:16px}
.oc-item{margin:0 16px;border:1px solid #e5e7eb;border-radius:14px;background:#fff;transition:.2s}
.oc-item:hover{border-color:#93c21c;box-shadow:0 10px 25px -10px rgb(0 0 0/.25)}
.oc-item-row{padding:16px}
@media(max-width:1280px){.oc-list-head{display:none}.oc-item-row{grid-template-columns:1fr}.oc-cell-title{display:block!important}}
.oc-cell-title{display:none;font-size:11px;font-weight:900;color:#6b7280;text-transform:uppercase;margin-bottom:4px}
.oc-id-badge{display:inline-flex;min-width:54px;height:36px;align-items:center;justify-content:center;border-radius:10px;background:#eff6ff;color:#74b2d4;font-weight:900}
.oc-ttl{font-weight:900;color:#111827;font-size:15px;margin-bottom:4px}
.oc-subt{font-size:13px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.oc-pill{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900}
.oc-pill.green{background:#ecfdf5;color:#047857}.oc-pill.orange{background:#fffbeb;color:#b45309}.oc-pill.red{background:#fef2f2;color:#b91c1c}.oc-pill.gray{background:#f3f4f6;color:#374151}
.oc-actions{display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap}
.oc-empty{text-align:center;padding:50px;color:#6b7280;border:1px dashed #e5e7eb;border-radius:16px;margin:16px}
.oc-pagination{margin-top:18px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:14px 16px}
.oc-modal-backdrop{position:fixed;inset:0;z-index:1200;background:rgba(17,24,39,.55);opacity:0;pointer-events:none;display:flex;align-items:center;justify-content:center;padding:18px}
.oc-modal-backdrop.open{opacity:1;pointer-events:auto}
.oc-modal{width:100%;max-width:980px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgb(0 0 0/.25)}
.oc-modal-h{display:flex;justify-content:space-between;align-items:center;padding:16px 18px;border-bottom:1px solid #e5e7eb;background:#fafafa}
.oc-modal-ttl{font-weight:900;font-size:16px;margin:0}
.oc-modal-b{padding:20px;max-height:72vh;overflow:auto}
.oc-modal-f{display:flex;justify-content:flex-end;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb;background:#fafafa}
.oc-form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
@media(max-width:760px){.oc-form-grid{grid-template-columns:1fr}}
.oc-form-group{margin-bottom:14px}
.oc-label{display:block;font-size:13px;font-weight:800;margin-bottom:6px}
.oc-input-form,.oc-select-form{width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;background:#fff}
.oc-toast-wrap{position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;flex-direction:column;gap:10px}
.oc-toast{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:12px;box-shadow:0 10px 25px -10px rgb(0 0 0/.25);min-width:280px;display:flex;gap:10px}
.oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:900}
.oc-toast-ic.ok{background:#ecfdf5;color:#10b981}.oc-toast-ic.bad{background:#fef2f2;color:#ef4444}
.oc-toast-ttl{font-weight:900;margin:0;font-size:13px}.oc-toast-msg{font-size:12px;margin:4px 0 0;color:#374151}
.d-none{display:none!important}
.img-flag{width:20px;height:20px;object-fit:contain;margin-right:6px}
</style>
@endsection

@section('content')
<div class="oc-wrap">
  <div class="oc-titlebar">
    <div>
      <div class="oc-title">KAUFANFRAGEN</div>
      <div class="oc-sub">Kaufanfragen erstellen, suchen, filtern, sortieren und per AJAX verwalten.</div>
    </div>

    <button class="oc-btn" type="button" onclick="openModal('createModal')">+ Neue Kaufanfrage</button>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat"><div class="oc-stat-icon">#</div><div><div class="oc-stat-label">Gesamt</div><div class="oc-stat-value" id="statTotal">0</div><div class="oc-stat-sub">Alle Kaufanfragen</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon">T</div><div><div class="oc-stat-label">Heute</div><div class="oc-stat-value" id="statToday">0</div><div class="oc-stat-sub">Neue Anfragen</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon">✓</div><div><div class="oc-stat-label">Published</div><div class="oc-stat-value" id="statPublished">0</div><div class="oc-stat-sub">Veröffentlicht</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon">!</div><div><div class="oc-stat-label">Unpublished</div><div class="oc-stat-value" id="statUnpublished">0</div><div class="oc-stat-sub">Offen</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon">IMG</div><div><div class="oc-stat-label">Bilder</div><div class="oc-stat-value" id="statWithImage">0</div><div class="oc-stat-sub">Mit Datei</div></div></div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input id="searchInput" class="oc-input" placeholder="Produkt, Modell, Hersteller, Lieferant, Status">
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Status</label>
        <select id="statusFilter" class="oc-select">
          <option value="">Alle</option>
          <option value="Unpublished">Unpublished</option>
          <option value="Published">Published</option>
          <option value="Done">Done</option>
          <option value="Canceled">Canceled</option>
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Benennung</label>
        <select id="usedFilter" class="oc-select">
          <option value="">Alle</option>
          <option value="Kunden">Kunden</option>
          <option value="Mitarbeiter">Mitarbeiter</option>
          <option value="Problem">Problem/Ticket</option>
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Sortierung</label>
        <select id="sortSelect" class="oc-select">
          <option value="created_at">Erstellt</option>
          <option value="id">ID</option>
          <option value="product">Produkt</option>
          <option value="model">Modell</option>
          <option value="quantity">Menge</option>
          <option value="status">Status</option>
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Richtung</label>
        <select id="directionSelect" class="oc-select">
          <option value="desc">Absteigend</option>
          <option value="asc">Aufsteigend</option>
        </select>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button type="button" class="oc-btn-soft" id="resetBtn">Zurücksetzen</button>
      <button type="button" class="oc-btn" id="searchBtn">Suchen</button>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-list-head">
      <div>ID</div>
      <div>Produkt</div>
      <div>Menge</div>
      <div>Hersteller</div>
      <div>Lieferant</div>
      <div>Anfrage</div>
      <div>Status</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div id="listWrap" class="oc-list">
      <div class="oc-empty">Lade Kaufanfragen...</div>
    </div>
  </div>

  <div id="paginationWrap" class="oc-pagination" style="display:none;"></div>
</div>

<div class="oc-modal-backdrop" id="createModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Neue Kaufanfrage</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('createModal')">×</button>
    </div>

    <form id="createForm" enctype="multipart/form-data">
      @csrf

      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Hersteller *</label>
            <select id="brand" name="brand_id" class="oc-select-form" required>
              <option value="new" data-image="{{ asset('images/icons/new.png') }}">Neuer Hersteller</option>
              @foreach($brands as $brand)
                @if(($brand->status ?? '') === 'Published')
                  <option value="{{ $brand->id }}" data-image="{{ asset('images/brand/'.$brand->image) }}">{{ $brand->name }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div class="oc-form-group" id="newBrandWrap">
            <label class="oc-label">Neuer Hersteller</label>
            <input name="new_brand" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Produkt *</label>
            <input name="product" class="oc-input-form" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Modell *</label>
            <input name="model" class="oc-input-form" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Lieferant *</label>
            <select id="distributor" name="distributor" class="oc-select-form" required>
              <option value="new" data-image="{{ asset('images/icons/new.png') }}">Neuer Lieferant</option>
              @foreach($distributors as $distributor)
                @if(($distributor->status ?? '') === 'Published')
                  <option value="{{ $distributor->id }}" data-image="{{ asset('images/distributor/'.$distributor->image) }}">{{ $distributor->name }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div class="oc-form-group" id="newDistributorWrap">
            <label class="oc-label">Neuer Lieferant</label>
            <input name="new_distributor" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Benennung *</label>
            <select name="used" id="used" class="oc-select-form" required>
              <option value="Kunden">Kunden</option>
              <option value="Mitarbeiter">Mitarbeiter</option>
              <option value="Problem">Problem/Ticket</option>
            </select>
          </div>

          <div class="oc-form-group" id="customerWrap">
            <label class="oc-label">Kunde</label>
            <select name="customer_id" id="customer_id" class="oc-select-form">
              <option value="">Nicht auswählen</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group d-none" id="employeeWrap">
            <label class="oc-label">Mitarbeiter</label>
            <select name="employee_id" id="employee_id" class="oc-select-form">
              <option value="">Nicht auswählen</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group d-none" id="problemWrap">
            <label class="oc-label">Ticket</label>
            <select name="problem_id" id="problem_id" class="oc-select-form">
              <option value="">Nicht auswählen</option>
              @foreach($problems as $problem)
                <option value="{{ $problem->id }}">
                  {{ $problem->ticket_no }} | {{ $problem->customer_name }} {{ $problem->customer_lastname }} | {{ $problem->product_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Farbe</label>
            <select name="color" id="color" class="oc-select-form">
              <option value="Schwarz">Schwarz</option>
              <option value="Grau">Grau</option>
              <option value="Braun">Braun</option>
              <option value="Beige">Beige</option>
              <option value="Gold">Gold</option>
              <option value="Blau">Blau</option>
              <option value="Gelb">Gelb</option>
              <option value="Lila">Lila</option>
              <option value="Silver">Silver</option>
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Mengeneinheit</label>
            <select name="measure_unit" id="measure_unit" class="oc-select-form">
              <option value="">Bitte wählen</option>
              @foreach($measures as $measure)
                <option value="{{ $measure->id }}">{{ $measure->measure }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Preiseinheit</label>
            <input name="price_unit" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">UVP</label>
            <input type="number" step="0.01" name="retail_price" id="retail_price" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Rabatt Typ</label>
            <select name="retail_discount_type" id="retail_drop" class="oc-select-form">
              <option value="Percent">Percent</option>
              <option value="Euro">Euro</option>
            </select>
          </div>

          <div class="oc-form-group" id="percentWrap">
            <label class="oc-label">Rabatt %</label>
            <input type="number" step="0.01" name="retail_discount_p" id="r_discount_p" class="oc-input-form">
          </div>

          <div class="oc-form-group d-none" id="euroWrap">
            <label class="oc-label">Rabatt €</label>
            <input type="number" step="0.01" name="retail_discount_e" id="r_discount_e" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Einkaufspreis</label>
            <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Menge</label>
            <input type="number" step="0.01" name="quantity" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Anfrage von</label>
            <select name="request_from" id="request_from" class="oc-select-form">
              <option value="">Bitte wählen</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Anfrage zu</label>
            <select name="request_to" id="request_to" class="oc-select-form">
              <option value="">Bitte wählen</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Online-Link</label>
            <input name="link" class="oc-input-form">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Foto</label>
            <input type="file" name="image" class="oc-input-form">
          </div>

          <div class="oc-form-group" style="grid-column:1/-1;">
            <label class="oc-label">Kurze Beschreibung</label>
            <div id="editor" style="height:220px;background:#fff;"></div>
            <textarea name="editor_text" id="editor_text" hidden></textarea>
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('createModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="oc-modal-backdrop" id="showModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl" id="showTitle">Kaufanfrage</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('showModal')">×</button>
    </div>

    <div class="oc-modal-b" id="showBody"></div>

    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeModal('showModal')">Schließen</button>
    </div>
  </div>
</div>

<div class="oc-toast-wrap" id="toastWrap"></div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>

<script>
const routes = {
  list: @json(route('purchase.request.list')),
  analytics: @json(route('purchase.request.analytics')),
  store: @json(route('purchase.request.save')),
  showBase: @json(url('/purchase_request_show')),
  deleteBase: @json(url('/purchase_request_delete')),
};

let currentPage = 1;
let quill = null;

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
}

function openModal(id) {
  document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
  document.getElementById(id)?.classList.remove('open');
}

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function toast(kind, title, msg) {
  const wrap = document.getElementById('toastWrap');

  const el = document.createElement('div');
  el.className = 'oc-toast';
  el.innerHTML = `
    <div class="oc-toast-ic ${kind}">${kind === 'ok' ? '✓' : '!'}</div>
    <div style="flex:1;">
      <p class="oc-toast-ttl">${escapeHtml(title)}</p>
      <p class="oc-toast-msg">${escapeHtml(msg)}</p>
    </div>
    <button type="button" onclick="this.parentElement.remove()" style="border:0;background:transparent;cursor:pointer;">×</button>
  `;

  wrap.appendChild(el);
  setTimeout(() => el.remove(), 4000);
}

function formatOption(option) {
  if (!option.id) return option.text;

  const image = $(option.element).data('image');
  if (!image) return option.text;

  return $(`<span><img src="${image}" class="img-flag"> ${option.text}</span>`);
}

function loadAnalytics() {
  fetch(routes.analytics, { headers: { Accept: 'application/json' } })
    .then(res => res.json())
    .then(res => {
      if (!res.status) return;

      document.getElementById('statTotal').textContent = res.analytics.total ?? 0;
      document.getElementById('statToday').textContent = res.analytics.today ?? 0;
      document.getElementById('statPublished').textContent = res.analytics.published ?? 0;
      document.getElementById('statUnpublished').textContent = res.analytics.unpublished ?? 0;
      document.getElementById('statWithImage').textContent = res.analytics.with_image ?? 0;
    });
}

function loadList(page = 1) {
  currentPage = page;

  const params = new URLSearchParams({
    page,
    search: document.getElementById('searchInput').value || '',
    status: document.getElementById('statusFilter').value || '',
    used: document.getElementById('usedFilter').value || '',
    sort: document.getElementById('sortSelect').value || 'created_at',
    direction: document.getElementById('directionSelect').value || 'desc',
  });

  document.getElementById('listWrap').innerHTML = `<div class="oc-empty">Lade Kaufanfragen...</div>`;

  fetch(`${routes.list}?${params.toString()}`, { headers: { Accept: 'application/json' } })
    .then(res => res.json())
    .then(res => {
      if (!res.status) {
        toast('bad', 'Fehler', 'Daten konnten nicht geladen werden.');
        return;
      }

      renderList(res.data);
    })
    .catch(() => toast('bad', 'Fehler', 'Serverfehler beim Laden.'));
}

function renderList(paginator) {
  const items = paginator.data || [];
  const wrap = document.getElementById('listWrap');

  if (!items.length) {
    wrap.innerHTML = `<div class="oc-empty">Keine Kaufanfragen gefunden.</div>`;
    renderPagination(paginator);
    return;
  }

  wrap.innerHTML = items.map(item => {
    const requestFrom = `${item.requestf_name || ''} ${item.requestf_lastname || ''}`.trim() || '—';
    const requestTo = `${item.requestt_name || ''} ${item.requestt_lastname || ''}`.trim() || '—';

    return `
      <div class="oc-item">
        <div class="oc-item-row">
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
            <div class="oc-ttl">${escapeHtml(item.quantity || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Hersteller</div>
            <div class="oc-ttl">${escapeHtml(item.brand_name || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Lieferant</div>
            <div class="oc-ttl">${escapeHtml(item.distributor_name || '—')}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Anfrage</div>
            <div class="oc-ttl">${escapeHtml(requestFrom)}</div>
            <div class="oc-subt">→ ${escapeHtml(requestTo)}</div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Status</div>
            <span class="oc-pill ${statusClass(item.status)}">${escapeHtml(item.status || '—')}</span>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Aktionen</div>
            <div class="oc-actions">
              <button type="button" class="oc-btn-ic primary" onclick="showItem(${item.id})">👁</button>
              <button type="button" class="oc-btn-ic danger" onclick="deleteItem(${item.id})">🗑</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');

  renderPagination(paginator);
}

function statusClass(status) {
  if (status === 'Published' || status === 'Done') return 'green';
  if (status === 'Canceled') return 'red';
  if (status === 'Unpublished') return 'orange';
  return 'gray';
}

function renderPagination(paginator) {
  const wrap = document.getElementById('paginationWrap');

  if (!paginator.last_page || paginator.last_page <= 1) {
    wrap.style.display = 'none';
    wrap.innerHTML = '';
    return;
  }

  let buttons = '';

  for (let i = 1; i <= paginator.last_page; i++) {
    buttons += `
      <button type="button" class="${i === paginator.current_page ? 'oc-btn' : 'oc-btn-soft'}" onclick="loadList(${i})">
        ${i}
      </button>
    `;
  }

  wrap.style.display = 'block';
  wrap.innerHTML = `
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
      <div style="font-size:12px;color:#6b7280;">
        Zeige <strong>${paginator.from || 0}</strong> bis <strong>${paginator.to || 0}</strong> von <strong>${paginator.total || 0}</strong>
      </div>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">${buttons}</div>
    </div>
  `;
}

function showItem(id) {
  fetch(`${routes.showBase}/${id}`, { headers: { Accept: 'application/json' } })
    .then(res => res.json())
    .then(res => {
      if (!res.status) {
        toast('bad', 'Fehler', res.message || 'Datensatz wurde nicht gefunden.');
        return;
      }

      const item = res.item;
      document.getElementById('showTitle').textContent = `Kaufanfrage #${item.id}`;

      document.getElementById('showBody').innerHTML = `
        <div class="oc-form-grid">
          ${showField('Produkt', item.product)}
          ${showField('Modell', item.model)}
          ${showField('Hersteller', item.brand_name)}
          ${showField('Lieferant', item.distributor_name)}
          ${showField('Farbe', item.color)}
          ${showField('Benennung', item.used)}
          ${showField('Menge', item.quantity)}
          ${showField('Mengeneinheit', item.measure_unit)}
          ${showField('Preiseinheit', item.price_unit)}
          ${showField('UVP', item.retail_price)}
          ${showField('Rabatt', item.retail_discount_type + ' ' + (item.retail_discount || ''))}
          ${showField('Einkaufspreis', item.purchase_price)}
          ${showField('Anfrage von', `${item.requestf_name || ''} ${item.requestf_lastname || ''}`.trim())}
          ${showField('Anfrage zu', `${item.requestt_name || ''} ${item.requestt_lastname || ''}`.trim())}
          ${showField('Status', item.status)}
          ${showField('Link', item.link)}
          <div class="oc-form-group" style="grid-column:1/-1;">
            <label class="oc-label">Beschreibung</label>
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;min-height:80px;">
              ${item.short_description || '—'}
            </div>
          </div>
          ${
            item.image_url
              ? `<div class="oc-form-group" style="grid-column:1/-1;">
                   <label class="oc-label">Bild</label>
                   <img src="${item.image_url}" style="max-width:100%;border-radius:12px;border:1px solid #e5e7eb;">
                 </div>`
              : ''
          }
        </div>
      `;

      openModal('showModal');
    });
}

function showField(label, value) {
  return `
    <div class="oc-form-group">
      <label class="oc-label">${escapeHtml(label)}</label>
      <div class="oc-input-form" style="background:#f9fafb;">${escapeHtml(value || '—')}</div>
    </div>
  `;
}

function deleteItem(id) {
  if (!confirm('Möchten Sie diese Kaufanfrage wirklich löschen?')) return;

  fetch(`${routes.deleteBase}/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      Accept: 'application/json',
    },
  })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    })
    .then(data => {
      toast('ok', 'Gelöscht', data.message || 'Datensatz gelöscht.');
      loadList(currentPage);
      loadAnalytics();
    })
    .catch(err => toast('bad', 'Fehler', err.message || 'Löschen fehlgeschlagen.'));
}

document.getElementById('createForm').addEventListener('submit', function(e) {
  e.preventDefault();

  if (quill) {
    document.getElementById('editor_text').value = quill.root.innerHTML;
  }

  fetch(routes.store, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      Accept: 'application/json',
    },
    body: new FormData(this),
  })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw data;
      return data;
    })
    .then(data => {
      toast('ok', 'Gespeichert', data.message || 'Kaufanfrage gespeichert.');
      closeModal('createModal');
      this.reset();

      if (quill) quill.root.innerHTML = '';

      $('#brand,#distributor,#used,#customer_id,#employee_id,#problem_id,#color,#measure_unit,#request_from,#request_to').trigger('change');

      loadList(1);
      loadAnalytics();
    })
    .catch(err => toast('bad', 'Fehler', err.message || 'Speichern fehlgeschlagen.'));
});

document.getElementById('searchBtn').addEventListener('click', () => loadList(1));

document.getElementById('resetBtn').addEventListener('click', () => {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('usedFilter').value = '';
  document.getElementById('sortSelect').value = 'created_at';
  document.getElementById('directionSelect').value = 'desc';
  loadList(1);
});

document.getElementById('searchInput').addEventListener('keyup', e => {
  if (e.key === 'Enter') loadList(1);
});

document.getElementById('brand').addEventListener('change', function() {
  document.getElementById('newBrandWrap').classList.toggle('d-none', this.value !== 'new');
});

document.getElementById('distributor').addEventListener('change', function() {
  document.getElementById('newDistributorWrap').classList.toggle('d-none', this.value !== 'new');
});

document.getElementById('used').addEventListener('change', function() {
  document.getElementById('customerWrap').classList.toggle('d-none', this.value !== 'Kunden');
  document.getElementById('employeeWrap').classList.toggle('d-none', this.value !== 'Mitarbeiter');
  document.getElementById('problemWrap').classList.toggle('d-none', this.value !== 'Problem');
});

document.getElementById('retail_drop').addEventListener('change', function() {
  document.getElementById('percentWrap').classList.toggle('d-none', this.value !== 'Percent');
  document.getElementById('euroWrap').classList.toggle('d-none', this.value !== 'Euro');
});

function calculatePurchasePrice() {
  const retail = parseFloat(document.getElementById('retail_price').value || 0);
  const type = document.getElementById('retail_drop').value;
  const percent = parseFloat(document.getElementById('r_discount_p').value || 0);
  const euro = parseFloat(document.getElementById('r_discount_e').value || 0);

  let result = retail;

  if (type === 'Percent') result = retail - ((retail / 100) * percent);
  if (type === 'Euro') result = retail - euro;

  document.getElementById('purchase_price').value = Number(result || 0).toFixed(2);
}

document.getElementById('r_discount_p').addEventListener('input', calculatePurchasePrice);
document.getElementById('r_discount_e').addEventListener('input', calculatePurchasePrice);
document.getElementById('retail_price').addEventListener('input', calculatePurchasePrice);

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('oc-modal-backdrop')) {
    e.target.classList.remove('open');
  }
});

$(document).ready(function() {
  $('#brand,#distributor').select2({
    templateResult: formatOption,
    templateSelection: formatOption,
    dropdownParent: $('#createModal')
  });

  $('#used,#customer_id,#employee_id,#problem_id,#color,#measure_unit,#request_from,#request_to').select2({
    dropdownParent: $('#createModal')
  });

  quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ header: [1, 2, 3, false] }],
        ['link', 'clean']
      ]
    }
  });

  loadAnalytics();
  loadList(1);
});
</script>
@endsection