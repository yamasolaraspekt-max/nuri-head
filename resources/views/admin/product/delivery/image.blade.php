@extends('admin.layouts.app')
@section('title', 'Lieferschein Bilder')

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
    --danger-light:#fef2f2;
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
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.blue{color:var(--blue);border-color:#dbeafe;background:#eff6ff}

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

  .oc-input,.oc-input-form,.oc-select,.oc-textarea{
    width:100%;
    background:#fff;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }

  .oc-input:focus,.oc-input-form:focus,.oc-select:focus,.oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
    margin-bottom:18px;
  }

  .oc-card-head{
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }

  .oc-card-title{
    font-size:16px;
    font-weight:900;
    margin:0;
  }

  .oc-card-body{
    padding:18px;
  }

  .oc-note-grid{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
  }
  @media(max-width:1100px){ .oc-note-grid{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-note-grid{grid-template-columns:1fr;} }

  .oc-stat{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px;
  }

  .oc-stat-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:6px;
  }

  .oc-stat-value{
    font-size:15px;
    font-weight:800;
    color:#111827;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(3, minmax(0,1fr));
    gap:14px;
  }
  @media(max-width:900px){ .oc-form-grid{grid-template-columns:1fr;} }

  .oc-upload-row{
    display:grid;
    grid-template-columns:180px 1fr 1fr 60px;
    gap:12px;
    align-items:end;
    margin-bottom:12px;
  }
  @media(max-width:900px){ .oc-upload-row{grid-template-columns:1fr;} }

  .oc-gallery{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:16px;
  }

  .oc-image-card{
    border:1px solid var(--border);
    border-radius:16px;
    overflow:hidden;
    background:#fff;
    box-shadow:var(--shadow-sm);
  }

  .oc-image-preview{
    width:100%;
    height:220px;
    object-fit:cover;
    background:#f9fafb;
    display:block;
  }

  .oc-image-body{
    padding:14px;
  }

  .oc-image-title{
    font-size:14px;
    font-weight:800;
    color:#111827;
    margin-bottom:8px;
  }

  .oc-image-meta{
    font-size:12px;
    color:var(--text-muted);
    margin-bottom:12px;
  }

  .oc-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
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
  .oc-modal.oc-modal-lg{max-width:900px;}
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
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">LIEFERSCHEIN BILDER</div>
        <div class="oc-sub">Bilder zum Lieferschein verwalten, hochladen und bearbeiten.</div>

        <div class="oc-breadcrumb">
          <a href="{{ route('delivery-notes.index') }}">Lieferscheine</a>
          <span>›</span>
          <span class="current">{{ $deliveryNote->delivery_note }}</span>
        </div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('delivery-notes.index') }}" class="oc-btn-soft">Zurück</a>
      </div>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">Lieferschein-Informationen</h3>
    </div>
    <div class="oc-card-body">
      <div class="oc-note-grid">
        <div class="oc-stat">
          <div class="oc-stat-label">Lieferschein-Nr.</div>
          <div class="oc-stat-value">{{ $deliveryNote->delivery_note }}</div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-label">Geliefert von</div>
          <div class="oc-stat-value">{{ $deliveryNote->delivered_from ?: '—' }}</div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-label">Zweig</div>
          <div class="oc-stat-value">{{ $deliveryNote->branch->branch ?? '—' }}</div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-label">Übergabe durch</div>
          <div class="oc-stat-value">
            {{ $deliveryNote->handoverEmployee ? trim(($deliveryNote->handoverEmployee->name ?? '').' '.($deliveryNote->handoverEmployee->lastname ?? '')) : '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <form method="GET" action="{{ route('delivery-notes.images.index', $deliveryNote->id) }}" class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          placeholder="Suche nach Bildtitel"
          name="search"
          value="{{ request('search') }}"
        >
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Sortierung</label>
        <select class="oc-select" name="sort">
          <option value="latest" @selected(request('sort') === 'latest')>Neueste zuerst</option>
          <option value="oldest" @selected(request('sort') === 'oldest')>Älteste zuerst</option>
          <option value="name_asc" @selected(request('sort') === 'name_asc')>Titel A-Z</option>
          <option value="name_desc" @selected(request('sort') === 'name_desc')>Titel Z-A</option>
        </select>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button type="submit" class="oc-btn-soft">Suchen</button>
      <a href="{{ route('delivery-notes.images.index', $deliveryNote->id) }}" class="oc-btn-soft">Zurücksetzen</a>
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">Bilder hochladen</h3>
    </div>
    <div class="oc-card-body">
      <form method="POST" action="{{ route('delivery-notes.images.store', $deliveryNote->id) }}" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
          <div style="margin-bottom:16px;padding:14px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#991b1b;">
            <strong>Bitte prüfen:</strong>
            <ul style="margin:8px 0 0 18px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div id="uploadRows">
          <div class="oc-upload-row">
            <div>
              <label class="oc-filter-label">Lieferschein</label>
              <input type="text" class="oc-input-form" value="{{ $deliveryNote->delivery_note }}" disabled>
            </div>
            <div>
              <label class="oc-filter-label">Titel</label>
              <input type="text" class="oc-input-form" name="product[0][title]" placeholder="Titel des Bildes">
            </div>
            <div>
              <label class="oc-filter-label">Bild</label>
              <input type="file" class="oc-input-form" name="product[0][image]" required>
            </div>
            <div style="display:flex;align-items:end;">
              <button type="button" class="oc-btn-ic danger remove-upload-row" style="display:none;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;">
          <button type="submit" class="oc-btn">Datensatz speichern</button>
          <button type="button" class="oc-btn-soft" id="addUploadRow">Bild hinzufügen</button>
        </div>
      </form>
    </div>
  </div>

  @if($images->count())
    <div class="oc-gallery">
      @foreach($images as $img)
        <div class="oc-image-card">
          <img
            src="{{ asset('images/delivery_note/'.$img->image) }}"
            alt="{{ $img->name }}"
            class="oc-image-preview"
          >

          <div class="oc-image-body">
            <div class="oc-image-title">{{ $img->name ?: 'Ohne Titel' }}</div>
            <div class="oc-image-meta">ID #{{ $img->id }}</div>

            <div class="oc-actions">
              <button
                type="button"
                class="oc-btn-ic blue js-open-preview"
                data-src="{{ asset('images/delivery_note/'.$img->image) }}"
                data-title="{{ $img->name ?: 'Bildvorschau' }}"
                title="Vorschau"
              >
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>

              <button
                type="button"
                class="oc-btn-ic primary js-open-edit"
                data-id="{{ $img->id }}"
                data-name="{{ $img->name }}"
                title="Bearbeiten"
              >
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>

              <form action="{{ route('delivery-notes.images.destroy', $img->id) }}" method="POST" onsubmit="return confirm('Möchten Sie dieses Bild wirklich löschen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="oc-btn-ic danger" title="Löschen">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="oc-pagination">
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
        <div style="font-size:12px;color:#6b7280;">
          Zeige <strong>{{ $images->firstItem() ?? 0 }}</strong>
          bis <strong>{{ $images->lastItem() ?? 0 }}</strong>
          von <strong>{{ $images->total() }}</strong> Einträgen
        </div>
        <div>
          {{ $images->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  @else
    <div class="oc-empty">Keine Bilder gefunden.</div>
  @endif
</div>

<div class="oc-modal-backdrop" id="previewModal">
  <div class="oc-modal oc-modal-lg">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl" id="previewTitle">Bildvorschau</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('previewModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b" style="text-align:center;">
      <img id="previewImage" src="" alt="" style="max-width:100%;max-height:65vh;border-radius:12px;border:1px solid #e5e7eb;">
    </div>
  </div>
</div>

<div class="oc-modal-backdrop" id="editImageModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Bild bearbeiten</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('editImageModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form id="editImageForm" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="oc-modal-b">
        <div style="margin-bottom:16px;">
          <label class="oc-filter-label">Titel</label>
          <input type="text" class="oc-input-form" name="name" id="editImageName">
        </div>

        <div>
          <label class="oc-filter-label">Neues Bild</label>
          <input type="file" class="oc-input-form" name="image">
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('editImageModal')">Abbrechen</button>
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

    const icon = kind === 'bad' ? '✕' : '✓';
    const el = document.createElement('div');
    el.className = 'oc-toast';
    el.innerHTML = `
      <div style="width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:${kind === 'bad' ? '#fef2f2' : '#ecfdf5'};color:${kind === 'bad' ? '#ef4444' : '#10b981'};">${icon}</div>
      <div style="flex:1;">
        <div style="font-weight:900;font-size:13px;color:#111827;">${title}</div>
        <div style="font-size:12px;color:#374151;margin-top:4px;">${msg}</div>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#6b7280;cursor:pointer;">×</button>
    `;
    wrap.appendChild(el);
    setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  document.addEventListener('click', function(e){
    if (e.target.classList.contains('oc-modal-backdrop')) {
      e.target.classList.remove('open');
    }

    const previewBtn = e.target.closest('.js-open-preview');
    if (previewBtn) {
      document.getElementById('previewImage').src = previewBtn.dataset.src || '';
      document.getElementById('previewTitle').textContent = previewBtn.dataset.title || 'Bildvorschau';
      openModal('previewModal');
      return;
    }

    const editBtn = e.target.closest('.js-open-edit');
    if (editBtn) {
      const form = document.getElementById('editImageForm');
      form.action = "{{ url('/admin/delivery-notes/images') }}/" + editBtn.dataset.id;
      document.getElementById('editImageName').value = editBtn.dataset.name || '';
      openModal('editImageModal');
    }
  });

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
      document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
  });

  let uploadIndex = 1;
  document.getElementById('addUploadRow')?.addEventListener('click', function(){
    const wrap = document.getElementById('uploadRows');
    const row = document.createElement('div');
    row.className = 'oc-upload-row';
    row.innerHTML = `
      <div>
        <label class="oc-filter-label">Lieferschein</label>
        <input type="text" class="oc-input-form" value="{{ $deliveryNote->delivery_note }}" disabled>
      </div>
      <div>
        <label class="oc-filter-label">Titel</label>
        <input type="text" class="oc-input-form" name="product[${uploadIndex}][title]" placeholder="Titel des Bildes">
      </div>
      <div>
        <label class="oc-filter-label">Bild</label>
        <input type="file" class="oc-input-form" name="product[${uploadIndex}][image]" required>
      </div>
      <div style="display:flex;align-items:end;">
        <button type="button" class="oc-btn-ic danger remove-upload-row">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    `;
    wrap.appendChild(row);
    uploadIndex++;
  });

  document.addEventListener('click', function(e){
    const removeBtn = e.target.closest('.remove-upload-row');
    if (removeBtn) {
      removeBtn.closest('.oc-upload-row')?.remove();
    }
  });

  @if(session('save_msg'))
    toast('ok', 'Gespeichert', @json(session('save_msg')));
  @endif

  @if(session('delete_msg'))
    toast('bad', 'Hinweis', @json(session('delete_msg')));
  @endif
</script>
@endpush
@endonce