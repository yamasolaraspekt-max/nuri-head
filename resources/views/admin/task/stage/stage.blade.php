 @extends('admin.layouts.app')

@section('title') Arbeitsschritte Details @stop

@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
  :root{
    --g1:#93c21c;      /* primary */
    --g2:#cfe09b;      /* soft green */
    --g3:#74bc2d4;     /* as requested (kept) */
    --b1:#c0d8ea;      /* soft blue */

    --ink:#0f172a;
    --muted:#64748b;
    --line:rgba(15,23,42,.10);
    --panel:#ffffff;
    --bg:#f7fafc;
    --shadow: 0 18px 55px rgba(15,23,42,.10);
    --r12:12px;
    --r16:16px;
    --r20:20px;
  }

  /* --- Remove bootstrap dependencies visually --- */
  .row, .col-12, .col-md-3, .col-md-9{ all: unset; }
  /* keep content wrapper structure from layout, only restyle inside */
  .stage-page{ padding: 10px 0 24px; }
  .stage-shell{
    background: radial-gradient(circle at top left, rgba(192,216,234,.35) 0, rgba(207,224,155,.28) 35%, rgba(255,255,255,.9) 70%);
    border-radius: 22px;
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 10px 40px rgba(15,23,42,.06);
    padding: 16px;
  }

  .stage-header{
    display:flex; align-items:flex-end; justify-content:space-between;
    gap: 12px; margin-bottom: 14px;
  }
  .stage-title{
    display:flex; flex-direction:column; gap:6px;
  }
  .stage-title h2{ margin:0; font-size:20px; font-weight:800; color:var(--ink); }
  .stage-breadcrumbs{ font-size:12px; color:var(--muted); }
  .stage-breadcrumbs a{ color:var(--ink); text-decoration:none; border-bottom:1px dashed rgba(15,23,42,.35); }
  .stage-breadcrumbs a:hover{ border-bottom-color: var(--g1); }

  /* grid */
  .grid{
    display:grid;
    gap: 12px;
  }
  .grid.filters{ grid-template-columns: repeat(12, 1fr); }
  .grid.tools{ grid-template-columns: repeat(12, 1fr); }
  .g-3{ grid-column: span 3; }
  .g-9{ grid-column: span 9; }
  .g-12{ grid-column: span 12; }
  @media (max-width: 980px){
    .grid.filters, .grid.tools{ grid-template-columns: 1fr; }
    .g-3, .g-9, .g-12{ grid-column: span 12; }
  }

  /* controls */
  .field{ display:flex; flex-direction:column; gap:6px; }
  .label{ font-size:12px; font-weight:700; color:var(--ink); }
  .control, .control input, .control select{
    width:100%;
  }
  .input, select{
    height: 42px;
    border-radius: 12px;
    border: 1px solid var(--line);
    background: rgba(255,255,255,.95);
    padding: 0 12px;
    color: var(--ink);
    outline: none;
    transition: .18s ease;
  }
  .input:focus, select:focus{
    border-color: rgba(147,194,28,.55);
    box-shadow: 0 0 0 4px rgba(147,194,28,.16);
  }

  .btn{
    height: 42px;
    border-radius: 12px;
    border: 1px solid transparent;
    padding: 0 14px;
    font-weight: 800;
    font-size: 13px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    cursor:pointer;
    user-select:none;
    transition: .18s ease;
    white-space: nowrap;
  }
  .btn:disabled{ opacity:.45; cursor:not-allowed; }
  .btn-primary{ background: var(--g1); color:#fff; box-shadow: 0 10px 24px rgba(147,194,28,.22); }
  .btn-primary:hover{ transform: translateY(-1px); filter: brightness(.98); }
  .btn-soft{ background: rgba(207,224,155,.55); color: var(--ink); border-color: rgba(15,23,42,.08); }
  .btn-soft:hover{ background: rgba(207,224,155,.72); }
  .btn-ghost{ background: rgba(192,216,234,.55); color: var(--ink); border-color: rgba(15,23,42,.08); }
  .btn-ghost:hover{ background: rgba(192,216,234,.75); }
  .btn-outline{ background: transparent; color: var(--ink); border-color: rgba(15,23,42,.14); }
  .btn-outline:hover{ border-color: rgba(147,194,28,.55); box-shadow: 0 0 0 4px rgba(147,194,28,.12); }
  .btn-danger{ background: rgba(239,68,68,.12); color:#b91c1c; border-color: rgba(239,68,68,.22); }
  .btn-danger:hover{ background: rgba(239,68,68,.18); }

  /* hint bar */
  .hint-bar{
    background: rgba(255,255,255,.9);
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 16px;
    padding: 10px 12px;
    display:flex; align-items:center; justify-content:space-between; gap:10px;
  }
  .hint-left{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .hint-pill{
    background: rgba(192,216,234,.55);
    border: 1px solid rgba(15,23,42,.08);
    color: var(--ink);
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 900;
  }
  .hint-text{ font-size: 13px; color: var(--muted); font-weight: 700; }

  /* toolbar */
  .toolbar{
    display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;
  }

  /* stage items (list partial uses these classes) */
  .step-wrapper{
    display:flex; flex-wrap:wrap; gap:12px; padding: 8px 0;
    align-items:flex-start; min-height:80px; position:relative;
  }
  .stage-wrapper{
    width:auto; max-width:240px;
    display:flex; flex-direction:column;
    align-items:center;
    position:relative;
    cursor: move;
    z-index: 1;
  }
  .stage-meta{
    font-size: 11px; margin-bottom: 6px; opacity:.85; width:100%;
    text-align:center; color: var(--muted); font-weight: 800;
  }
  .stage-select-wrap{ display:none; width:100%; text-align:left; margin-bottom: 6px; }
  .stage-select-wrap label{ font-size:12px; font-weight:800; color: var(--ink); display:flex; gap:8px; align-items:center; }
  .stage-select-wrap input{ width:16px; height:16px; accent-color: var(--g1); }

  .stage-arrow{
    padding: 12px 18px;
    color: #fff;
    font-weight: 900;
    clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
    min-width: 170px;
    text-align: center;
    transition: .18s ease;
    user-select: none;
    border-radius: 12px; /* subtle */
    box-shadow: 0 10px 26px rgba(15,23,42,.10);
  }
  .stage-wrapper:hover .stage-arrow{ transform: translateY(-1px); }

  .action-buttons{
    display:none;
    justify-content:center;
    gap:8px;
    margin-top:8px;
  }
  .stage-wrapper:hover .action-buttons{ display:flex; }
  .icon-btn{
    width: 36px; height: 34px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(255,255,255,.92);
    cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition: .18s ease;
  }
  .icon-btn:hover{ box-shadow: 0 0 0 4px rgba(147,194,28,.12); border-color: rgba(147,194,28,.35); }
  .icon-btn.danger{ background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.18); }
  .icon-btn.danger:hover{ box-shadow: 0 0 0 4px rgba(239,68,68,.10); }

  .sortable-placeholder{
    background: rgba(192,216,234,.40);
    border: 2px dashed rgba(15,23,42,.25);
    min-width: 170px;
    height: 60px;
    border-radius: 14px;
  }

  .is-disabled{ opacity:.45; filter: grayscale(1); }
  .is-disabled .stage-arrow{ background: #94a3b8 !important; }

  /* group header */
  .group-card{
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.88);
    border-radius: 18px;
    padding: 12px;
    box-shadow: 0 14px 36px rgba(15,23,42,.07);
    margin-top: 14px;
  }
  .group-top{
    display:flex; align-items:flex-start; justify-content:space-between; gap: 12px;
    padding: 10px;
    border-radius: 14px;
    background: linear-gradient(90deg, rgba(147,194,28,.18), rgba(192,216,234,.25));
    border: 1px solid rgba(15,23,42,.06);
  }
  .group-left{ display:flex; flex-direction:column; gap:6px; }
  .group-title{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    font-weight: 900; color: var(--ink);
  }
  .badge{
    display:inline-flex;
    align-items:center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(255,255,255,.75);
    color: var(--ink);
  }
  .badge.published{ background: rgba(147,194,28,.20); border-color: rgba(147,194,28,.25); }
  .badge.draft{ background: rgba(192,216,234,.35); border-color: rgba(192,216,234,.45); }

  .group-sub{ font-size: 12px; color: var(--muted); font-weight: 800; }
  .group-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }

  /* --- Custom Modal --- */
  .xmodal-backdrop{
    position: fixed; inset: 0;
    background: rgba(15,23,42,.55);
    display:none;
    align-items:center;
    justify-content:center;
    padding: 18px;
    z-index: 9999;
  }
  .xmodal-backdrop.show{ display:flex; }
  .xmodal{
    width: min(920px, 100%);
    border-radius: 18px;
    background: rgba(255,255,255,.94);
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: var(--shadow);
    overflow:hidden;
    transform: translateY(8px);
    opacity: 0;
    transition: .18s ease;
  }
  .xmodal-backdrop.show .xmodal{
    transform: translateY(0);
    opacity: 1;
  }
  .xmodal-head{
    padding: 12px 14px;
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    background: linear-gradient(90deg, rgba(207,224,155,.55), rgba(192,216,234,.35));
    border-bottom: 1px solid rgba(15,23,42,.08);
  }
  .xmodal-title{ margin:0; font-size: 14px; font-weight: 950; color: var(--ink); }
  .xmodal-close{
    width: 38px; height: 36px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(255,255,255,.85);
    cursor:pointer;
    font-weight: 900;
  }
  .xmodal-body{ padding: 14px; }
  .xmodal-foot{
    padding: 12px 14px;
    border-top: 1px solid rgba(15,23,42,.08);
    display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;
    background: rgba(255,255,255,.9);
  }

  /* Select2 fit our inputs */
  .select2-container{ width:100% !important; }
  .select2-container--default .select2-selection--single{
    height: 42px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: rgba(255,255,255,.95);
    padding: 6px 10px;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height: 28px;
    padding-left: 2px;
    font-weight: 800;
    color: var(--ink);
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow{
    height: 40px;
    right: 10px;
  }
</style>
@endsection

@section('content')
<div class="app-content content stage-page">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-body">
      <div class="stage-shell">

        <div class="stage-header">
          <div class="stage-title">
            <h2>Projektablauf</h2>
            <div class="stage-breadcrumbs">
              <a href="{{ url('/') }}">Dashboard</a> <span style="opacity:.6;">/</span> Projekt-Struktur
            </div>
          </div>
        </div>

        {{-- Filters --}}
        <div class="grid filters" style="margin-bottom:12px;">
          <div class="field g-3">
            <div class="label">Artikelgruppe</div>
            <div class="control">
              <select id="productFilter" class="js-s2-filter">
                <option value="">Artikelgruppe wählen</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="field g-3">
            <div class="label">Version</div>
            <div class="control">
              <select id="versionFilter" class="js-s2-filter">
                <option value="">Version wählen</option>
              </select>
            </div>
          </div>

          <div class="field g-3">
            <div class="label">Dienstleistung</div>
            <div class="control">
              <select id="sectionFilter" class="js-s2-filter" disabled>
                <option value="">Dienstleistung wählen</option>
              </select>
            </div>
          </div>

          <div class="field g-3" style="display:flex; justify-content:flex-end;">
            <button class="btn btn-primary" type="button" id="btnNewStage">
              <i class="feather icon-plus"></i> Neuer Schritt
            </button>
          </div>
        </div>

        {{-- Search + toolbar --}}
        <div class="grid tools" style="margin-bottom:12px;">
          <div class="field g-9">
            <div class="label">Suchen</div>
            <input type="text" id="searchInput" class="input" placeholder="Suchen...">
          </div>

          <div class="field g-3">
            <div class="label">Aktionen</div>
            <div class="toolbar">
              <button type="button" class="btn btn-outline" id="btnToggleSelect">Mehrfachauswahl</button>
              <button type="button" class="btn btn-primary" id="btnBulkDuplicate" disabled>
                Duplizieren (<span id="selectedCount">0</span>)
              </button>
              <button type="button" class="btn btn-ghost" id="btnClearSelection" disabled>
                Auswahl löschen
              </button>
            </div>
          </div>
        </div>

        {{-- Hint --}}
        <div class="hint-bar" style="margin-bottom:12px;">
          <div class="hint-left">
            <span class="hint-pill" id="hintSectionPill">Sektion: —</span>
            <span class="hint-text" id="hintText">Bitte Produkt wählen.</span>
          </div>

          <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
            <button class="btn btn-ghost" type="button" id="btnDuplicateFromSection" disabled>
              <i class="feather icon-copy"></i> Aus anderer Sektion übernehmen
            </button>

            {{-- EASY: copy whole product/version bucket to another product/section/version --}}
            <button class="btn btn-soft" type="button" id="btnCopyWholeBucket" disabled>
              <i class="feather icon-repeat"></i> Ganze Liste kopieren
            </button>
          </div>
        </div>

        <div id="stageListContainer"></div>
      </div>
    </div>
  </div>
</div>

{{-- ===========================
     Custom Modal: Stage CRUD
     =========================== --}}
<div class="xmodal-backdrop" id="xStageModal">
  <div class="xmodal" role="dialog" aria-modal="true">
    <div class="xmodal-head">
      <h3 class="xmodal-title" id="xStageTitle">Schritt bearbeiten</h3>
      <button class="xmodal-close" type="button" data-xclose>×</button>
    </div>

    <form id="stageForm" autocomplete="off">
      <input type="hidden" name="id">

      <div class="xmodal-body">
        <div class="grid filters">
          <div class="field g-12">
            <div class="label">Schritt / Stage</div>
            <input type="text" name="stage" class="input" placeholder="z.B. Vorbereitung">
          </div>

          <div class="field g-6">
            <div class="label">Produkt</div>
            <select name="product_id" class="js-s2-modal" id="modalProductSelect">
              <option value="">Bitte wählen</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->article_group }}</option>
              @endforeach
            </select>
          </div>

          <div class="field g-6">
            <div class="label">Sektion</div>
            <select name="phase_section_id" id="modalSectionSelect" class="js-s2-modal">
              <option value="">Sektion wählen</option>
            </select>
          </div>

          <div class="field g-6">
            <div class="label">Version</div>
            <select name="version" id="versionSelect" class="js-s2-modal"></select>
          </div>

          <div class="field g-6">
            <div class="label">Status</div>
            <select name="status" class="js-s2-modal" id="modalStatusSelect">
              <option value="Published">Veröffentlicht</option>
              <option value="Draft">Entwurf</option>
            </select>
          </div>
        </div>
      </div>

      <div class="xmodal-foot">
        <button class="btn btn-outline" type="button" data-xclose>Abbrechen</button>
        <button class="btn btn-primary" type="submit">Speichern</button>
      </div>
    </form>
  </div>
</div>

{{-- ===========================
     Custom Modal: Multi Target Duplicate (selected stages)
     =========================== --}}
<div class="xmodal-backdrop" id="xMultiTargetModal">
  <div class="xmodal" role="dialog" aria-modal="true">
    <div class="xmodal-head">
      <h3 class="xmodal-title">Auswahl in mehrere Ziele duplizieren</h3>
      <button class="xmodal-close" type="button" data-xclose>×</button>
    </div>

    <form id="multiTargetForm" autocomplete="off">
      <div class="xmodal-body">
        <div class="hint-bar" style="margin-bottom:12px;">
          <div class="hint-left">
            <span class="hint-pill">Quelle</span>
            <span class="hint-text">
              <strong id="srcProductLabel">—</strong> ·
              <strong id="srcSectionLabel">—</strong> ·
              <strong id="srcVersionLabel">—</strong>
              <span style="opacity:.75;">(Auswahl: <span id="srcSelectedCount">0</span>)</span>
            </span>
          </div>
          <button type="button" class="btn btn-outline" id="btnAddTargetRow">+ Ziel hinzufügen</button>
        </div>

        <div id="targetsWrap"></div>

        <template id="targetRowTpl">
          <div class="group-card target-row" style="margin-top:0;">
            <div class="grid filters">
              <div class="field g-4">
                <div class="label">Zielprodukt</div>
                <select class="js-s2-target target-product" required>
                  @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                  @endforeach
                </select>
              </div>

              <div class="field g-4">
                <div class="label">Ziel-Sektion</div>
                <select class="js-s2-target target-section" required>
                  <option value="">Sektion wählen</option>
                </select>
              </div>

              <div class="field g-3">
                <div class="label">Ziel-Version</div>
                <input type="text" class="input target-version" placeholder="z.B. V2" required>
              </div>

              <div class="field g-1" style="display:flex;align-items:flex-end;justify-content:flex-end;">
                <button type="button" class="btn btn-danger btnRemoveTarget" style="height:42px;">×</button>
              </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-top:8px;">
              <small style="color:var(--muted);font-weight:800;">Produkt wählen → Sektionen werden geladen.</small>
              <button type="button" class="btn btn-ghost btnCopyFromFirst" style="height:38px;">Von erstem Ziel übernehmen</button>
            </div>
          </div>
        </template>
      </div>

      <div class="xmodal-foot">
        <button class="btn btn-outline" type="button" data-xclose>Abbrechen</button>
        <button class="btn btn-primary" type="submit">Duplizieren</button>
      </div>
    </form>
  </div>
</div>

{{-- ===========================
     Custom Modal: Copy whole bucket (entire product+version+section)
     =========================== --}}
<div class="xmodal-backdrop" id="xWholeBucketModal">
  <div class="xmodal" role="dialog" aria-modal="true">
    <div class="xmodal-head">
      <h3 class="xmodal-title">Ganze Liste kopieren</h3>
      <button class="xmodal-close" type="button" data-xclose>×</button>
    </div>

    <form id="wholeBucketForm" autocomplete="off">
      <div class="xmodal-body">
        <div class="hint-bar" style="margin-bottom:12px;">
          <div class="hint-left">
            <span class="hint-pill">Quelle</span>
            <span class="hint-text">
              <strong id="wbSrcProduct">—</strong> ·
              <strong id="wbSrcSection">—</strong> ·
              <strong id="wbSrcVersion">—</strong>
            </span>
          </div>
        </div>

        <div class="grid filters">
          <div class="field g-4">
            <div class="label">Zielprodukt</div>
            <select id="wbTargetProduct" class="js-s2-wb" required>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->article_group }}</option>
              @endforeach
            </select>
          </div>

          <div class="field g-4">
            <div class="label">Ziel-Sektion</div>
            <select id="wbTargetSection" class="js-s2-wb" required>
              <option value="">Sektion wählen</option>
            </select>
          </div>

          <div class="field g-4">
            <div class="label">Ziel-Version</div>
            <input type="text" id="wbTargetVersion" class="input" placeholder="z.B. V2" required>
          </div>
        </div>

        <div style="margin-top:10px;color:var(--muted);font-size:12px;font-weight:800;">
          Hinweis: Kopiert alle Schritte aus der aktuellen Liste (Produkt+Version+Sektion) ins Ziel.
        </div>
      </div>

      <div class="xmodal-foot">
        <button class="btn btn-outline" type="button" data-xclose>Abbrechen</button>
        <button class="btn btn-primary" type="submit">Kopieren</button>
      </div>
    </form>
  </div>
</div>
@stop

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(() => {
  // ============================================================
  // State
  // ============================================================
  const StageUI = { selectMode:false, selected:new Set() };

  const SECTION_LABELS_DE = {
    complete: 'Komplettlösung',
    montage: 'Montage',
    product: 'Verkauf',
    plan: 'Planung',
    maintenance: 'Wartung',
    repair: 'Reparatur',
    others: 'Sonstiges',
    ticket: 'Ticket',
  };

  const normalizeSectionKey = (k) => (k ?? '').toString().trim().toLowerCase();
  const sectionLabelDE = (k) => SECTION_LABELS_DE[normalizeSectionKey(k)] || (k ?? 'Sektion');

  let defaultSectionId = null;

  // ============================================================
  // Custom modal (no bootstrap)
  // ============================================================
  const Modal = {
    open(id){
      const b = document.getElementById(id);
      if (!b) return;
      b.classList.add('show');
      document.documentElement.style.overflow = 'hidden';
    },
    close(id){
      const b = document.getElementById(id);
      if (!b) return;
      b.classList.remove('show');
      document.documentElement.style.overflow = '';
    },
    closeAll(){
      document.querySelectorAll('.xmodal-backdrop.show').forEach(b => b.classList.remove('show'));
      document.documentElement.style.overflow = '';
    },
    bind(){
      document.addEventListener('click', (e) => {
        const closeBtn = e.target.closest('[data-xclose]');
        if (closeBtn){
          const backdrop = e.target.closest('.xmodal-backdrop') || closeBtn.closest('.xmodal-backdrop');
          if (backdrop) Modal.close(backdrop.id);
          else Modal.closeAll();
          return;
        }
        // click on backdrop closes
        const b = e.target.classList?.contains('xmodal-backdrop') ? e.target : null;
        if (b) Modal.close(b.id);
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') Modal.closeAll();
      });
    }
  };

  // ============================================================
  // Helpers
  // ============================================================
  const $el = (sel) => $(sel);

  function readFilters(){
    return {
      productId: $el('#productFilter').val(),
      version:   $el('#versionFilter').val(),
      sectionId: ($el('#sectionFilter').val() || defaultSectionId || ''),
      search:    $el('#searchInput').val(),
    };
  }

  function updateHintBar(stageCount = null){
    const pid = $('#productFilter').val();
    const sid = ($('#sectionFilter').val() || defaultSectionId || '');
    const sText = $('#sectionFilter option:selected').text() || '—';

    $('#hintSectionPill').text(`Sektion: ${sText && sText !== 'Dienstleistung wählen' ? sText : '—'}`);

    if (!pid){
      $('#hintText').text('Bitte Produkt wählen.');
      $('#btnDuplicateFromSection').prop('disabled', true);
      $('#btnCopyWholeBucket').prop('disabled', true);
      return;
    }
    if (!sid){
      $('#hintText').text('Bitte Sektion wählen.');
      $('#btnDuplicateFromSection').prop('disabled', true);
      $('#btnCopyWholeBucket').prop('disabled', true);
      return;
    }
    if (stageCount === null){
      $('#hintText').text('Lade Schritte...');
      $('#btnDuplicateFromSection').prop('disabled', true);
      $('#btnCopyWholeBucket').prop('disabled', true);
      return;
    }
    if (stageCount < 1){
      $('#hintText').text('Diese Sektion ist leer. Du kannst Schritte aus einer anderen Sektion übernehmen.');
      $('#btnDuplicateFromSection').prop('disabled', false);
      $('#btnCopyWholeBucket').prop('disabled', false);
      return;
    }

    $('#hintText').text(`Schritte in dieser Sektion: ${stageCount}`);
    $('#btnDuplicateFromSection').prop('disabled', false);
    $('#btnCopyWholeBucket').prop('disabled', false);
  }

  // ============================================================
  // Select2 init
  // ============================================================
  function initSelect2Filters(){
    $('#productFilter, #versionFilter, #sectionFilter').each(function(){
      if ($(this).data('select2')) $(this).select2('destroy');
    });

    $('#productFilter').select2({ width:'100%', placeholder:'Produkt wählen', allowClear:true });
    $('#versionFilter').select2({ width:'100%', placeholder:'Version wählen', allowClear:true });
    $('#sectionFilter').select2({ width:'100%', placeholder:'Sektion wählen', allowClear:true });
  }

  function initSelect2StageModal(){
    $('#modalProductSelect, #modalSectionSelect, #modalStatusSelect, #versionSelect').each(function(){
      if ($(this).data('select2')) $(this).select2('destroy');
    });

    $('#modalProductSelect').select2({ width:'100%', placeholder:'Bitte wählen', allowClear:true, dropdownParent: $('#xStageModal') });
    $('#modalSectionSelect').select2({ width:'100%', placeholder:'Sektion wählen', allowClear:true, dropdownParent: $('#xStageModal') });
    $('#modalStatusSelect').select2({ width:'100%', minimumResultsForSearch: Infinity, dropdownParent: $('#xStageModal') });
    $('#versionSelect').select2({ placeholder:'Version wählen oder neu eingeben', tags:true, width:'100%', allowClear:true, dropdownParent: $('#xStageModal') });
  }

  function initSelect2MultiTargetModal(){
    // rows are dynamic; init per-row in addTargetRow()
  }

  function initSelect2WholeBucket(){
    $('#wbTargetProduct, #wbTargetSection').each(function(){
      if ($(this).data('select2')) $(this).select2('destroy');
    });

    $('#wbTargetProduct').select2({ width:'100%', dropdownParent: $('#xWholeBucketModal') });
    $('#wbTargetSection').select2({ width:'100%', placeholder:'Sektion wählen', allowClear:true, dropdownParent: $('#xWholeBucketModal') });
  }

  // ============================================================
  // Sections
  // ============================================================
  async function loadSectionsForProduct(productId) {
    defaultSectionId = null;

    const $sel = $('#sectionFilter');
    $sel.prop('disabled', true).trigger('change.select2');
    $sel.html('<option value="">Dienstleistung wählen</option>');

    if (!productId) { updateHintBar(); return; }

    const res = await $.get("{{ route('stages.get_sections') }}", { product_id: productId });
    const sections = (res.sections || []);
    defaultSectionId = res.default_section_id ? String(res.default_section_id) : null;

    sections.forEach(s => $sel.append(`<option value="${s.id}">${sectionLabelDE(s.phase_section)}</option>`));

    $sel.prop('disabled', false).trigger('change.select2');
    if (defaultSectionId) $sel.val(defaultSectionId).trigger('change');
    updateHintBar();
  }

  async function loadSectionsIntoSelect($select, productId, selectedId = null, dropdownParent = null) {
    $select.html('<option value="">Sektion wählen</option>');
    if (!productId) { $select.trigger('change.select2'); return; }

    const res = await $.get("{{ route('stages.get_sections') }}", { product_id: productId });
    const sections = (res.sections || []);
    const defId = res.default_section_id ? String(res.default_section_id) : null;

    sections.forEach(s => $select.append(`<option value="${s.id}">${sectionLabelDE(s.phase_section)}</option>`));

    if (selectedId) $select.val(String(selectedId));
    else if (defId) $select.val(defId);

    // select2 may not be attached yet (for dynamic rows); just trigger if exists
    $select.trigger('change.select2');
  }

  // ============================================================
  // Versions
  // ============================================================
  async function loadVersions(productId){
    const $vf = $('#versionFilter');
    $vf.html('<option value="">Version wählen</option>');

    if (!productId) { $vf.trigger('change.select2'); return; }

    const data = await $.get("{{ route('stages.get_versions') }}", { product_id: productId });
    (data.versions || []).forEach(v => $vf.append(`<option value="${v}">${v}</option>`));
    $vf.trigger('change.select2');
  }

  // ============================================================
  // Load stages
  // ============================================================
  async function loadStages(page = 1) {
    const f = readFilters();
    updateHintBar(null);

    $.get("{{ route('stages.fetch') }}", {
      page,
      product_id: f.productId,
      version: f.version,
      phase_section_id: f.sectionId,
      search: f.search
    }, function (data) {
      $('#stageListContainer').html(data.html);

      $('.stage-select-wrap').toggle(StageUI.selectMode);

      const stageCount = $('#stageListContainer').find('.stage-wrapper[data-id]').length;
      updateHintBar(stageCount);

      syncSelectionUI();
      initSortable();
    });
  }

  // ============================================================
  // Sortable
  // ============================================================
  function initSortable() {
    $('.step-wrapper').each(function () {
      const wrapper = $(this);

      wrapper.sortable({
        items: '.stage-wrapper[data-id]',
        connectWith: '.step-wrapper',
        placeholder: 'sortable-placeholder',
        handle: '.stage-arrow',
        tolerance: 'pointer',

        start: function (e, ui) {
          ui.placeholder.height(ui.item.outerHeight());
          ui.placeholder.width(ui.item.outerWidth());

          const $origin = ui.item.closest('.step-wrapper');
          ui.item.data('originWrapper', $origin);
          ui.item.data('originProduct', $origin.data('product'));
          ui.item.data('originVersion', $origin.data('version'));
          ui.item.data('originSection', $origin.data('section'));
        },

        stop: function (e, ui) {
          const $origin = ui.item.data('originWrapper');
          const srcP = String(ui.item.data('originProduct'));
          const srcV = String(ui.item.data('originVersion'));
          const srcS = String(ui.item.data('originSection'));

          const $target = ui.item.closest('.step-wrapper');
          const tgtP = String($target.data('product'));
          const tgtV = String($target.data('version'));
          const tgtS = String($target.data('section'));

          if (srcP !== tgtP || srcV !== tgtV || srcS !== tgtS) {
            if ($origin && $origin.length) $origin.sortable('cancel');
            else loadStages();
            return;
          }

          persistOrder($target);
        }
      });
    });
  }

  function persistOrder($wrapper) {
    const ids = $wrapper.find('.stage-wrapper[data-id]').map(function () {
      return $(this).data('id');
    }).get();

    $.post('{{ route("stages.reorder") }}', { _token: '{{ csrf_token() }}', ids });
  }

  // ============================================================
  // Stage modal helpers
  // ============================================================
  function setVersionSelect(version) {
    const v = (version ?? '').toString().trim();
    const $vs = $('#versionSelect');
    $vs.data('desired', v);

    if (!v) { $vs.val(null).trigger('change'); return; }

    const safe = v.replace(/"/g,'\\"');
    const has = $vs.find(`option[value="${safe}"]`).length > 0;
    if (!has) $vs.append(new Option(v, v, true, true));

    $vs.val(v).trigger('change');
  }

  function openCreateModal() {
    $('#stageForm')[0].reset();
    $('#stageForm input[name=id]').val('');
    $('#versionSelect').data('desired', '').empty().trigger('change');
    $('#modalStatusSelect').val('Published').trigger('change.select2');
    $('#xStageTitle').text('Neuen Schritt erstellen');

    const f = readFilters();
    $('#modalProductSelect').val(f.productId).trigger('change');
    setVersionSelect(f.version);

    if (f.productId) loadSectionsIntoSelect($('#modalSectionSelect'), f.productId, f.sectionId || null);
    initSelect2StageModal();
    Modal.open('xStageModal');
  }

  function openCreateStageModal(productId, version) {
    $('#stageForm')[0].reset();
    $('#stageForm input[name=id]').val('');
    $('#modalStatusSelect').val('Published').trigger('change.select2');
    $('#xStageTitle').text('Neuen Schritt erstellen');

    const f = readFilters();
    const sectionId = f.sectionId || '';

    $('#modalProductSelect').val(productId).trigger('change');
    setVersionSelect(version);

    if (productId) loadSectionsIntoSelect($('#modalSectionSelect'), productId, sectionId || null);
    initSelect2StageModal();
    Modal.open('xStageModal');
  }

  function editStage(id) {
    $.get(`/admin/stages/edit/${id}`, async function (data) {
      $('#stageForm input[name=id]').val(data.id);
      $('#stageForm input[name=stage]').val(data.stage);
      $('#modalStatusSelect').val(data.status).trigger('change.select2');
      $('#xStageTitle').text('Schritt bearbeiten');

      $('#modalProductSelect').val(data.product_id).trigger('change');

      setVersionSelect(data.version);
      setTimeout(() => setVersionSelect(data.version), 120);

      await loadSectionsIntoSelect($('#modalSectionSelect'), data.product_id, data.phase_section_id);
      initSelect2StageModal();
      Modal.open('xStageModal');
    });
  }

  function saveStage() {
    const id  = $('#stageForm input[name=id]').val();
    const url = id ? `/admin/stages/update/${id}` : '/admin/stages/store';

    const formData = $('#stageForm').serializeArray();

    const v = ($('#versionSelect').val() ?? '').toString().trim();
    const cleaned = formData.filter(x => x.name !== 'version');
    cleaned.push({ name: 'version', value: v });

    const secIdx = cleaned.findIndex(x => x.name === 'phase_section_id');
    const secVal = secIdx >= 0 ? cleaned[secIdx].value : '';
    if (!secVal) {
      const fallback = ($('#sectionFilter').val() || defaultSectionId || '');
      if (fallback) {
        if (secIdx >= 0) cleaned[secIdx].value = fallback;
        else cleaned.push({ name:'phase_section_id', value: fallback });
      }
    }

    $.ajax({
      url,
      method: 'POST',
      data: $.param(cleaned),
      success: function () {
        Swal.fire({ icon:'success', title:'Gespeichert', toast:true, position:'top-end', timer:1500, showConfirmButton:false });
        Modal.close('xStageModal');
        loadStages();
      },
      error: function (xhr) {
        Swal.fire({ icon:'error', title:'Fehler beim Speichern', text: xhr.responseJSON?.message || 'Bitte überprüfe deine Eingaben.' });
      }
    });
  }

  function deleteStage(id) {
    Swal.fire({
      title: 'Bist du sicher?',
      text: 'Dieser Schritt wird gelöscht!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, löschen!',
      cancelButtonText: 'Abbrechen',
    }).then((result) => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: `/admin/stages/destroy/${id}`,
        method: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function () {
          Swal.fire({ icon:'success', title:'Gelöscht', toast:true, position:'top-end', timer:1500, showConfirmButton:false });
          loadStages();
        },
        error: function (xhr) {
          Swal.fire({ icon:'info', title:'Nicht möglich', text: xhr.responseJSON?.message || 'Ein Fehler ist aufgetreten.' });
        }
      });
    });
  }

  // ============================================================
  // Duplicate from other section (same product + version)
  // ============================================================
  async function duplicateFromOtherSection(){
    const f = readFilters();
    if (!f.productId || !f.sectionId){
      Swal.fire('Hinweis', 'Bitte Produkt und Sektion wählen.', 'info');
      return;
    }

    try{
      const res = await $.get("{{ route('stages.section_stats') }}", {
        product_id: f.productId,
        version: f.version
      });

      const sections = (res.sections || []);
      if (!sections.length){
        Swal.fire('Hinweis', 'Keine Sektionen gefunden.', 'info');
        return;
      }

      const optionsHtml = sections
        .filter(s => String(s.id) !== String(f.sectionId))
        .map(s => {
          const label = sectionLabelDE(s.phase_section);
          const count = Number(s.stage_count || 0);
          const extra = count > 0 ? `(${count} Schritte)` : '(leer)';
          return `<option value="${s.id}">${label} ${extra}</option>`;
        })
        .join('');

      if (!optionsHtml){
        Swal.fire('Hinweis', 'Keine andere Sektion verfügbar.', 'info');
        return;
      }

      const targetLabel = $('#sectionFilter option:selected').text() || 'Ziel';

      const { value: sourceSectionId } = await Swal.fire({
        title: 'Schritte übernehmen',
        html: `
          <div style="text-align:left;">
            <div style="font-weight:900;margin-bottom:8px;">Ziel-Sektion</div>
            <div style="margin-bottom:12px;color:#64748b;font-weight:800;">${targetLabel}</div>

            <div style="font-weight:900;margin-bottom:8px;">Quelle wählen</div>
            <select id="swalSourceSection" class="swal2-input" style="width:100%;margin:0;">
              ${optionsHtml}
            </select>

            <div style="font-size:12px;opacity:.75;margin-top:10px;">
              Es werden alle Schritte aus der Quelle in die Ziel-Sektion dupliziert (gleiche Version).
            </div>
          </div>
        `,
        didOpen: () => {
          $('#swalSourceSection').select2({ width:'100%', dropdownParent: $('.swal2-container') });
        },
        willClose: () => {
          if ($('#swalSourceSection').data('select2')) $('#swalSourceSection').select2('destroy');
        },
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Übernehmen',
        cancelButtonText: 'Abbrechen',
        preConfirm: () => $('#swalSourceSection').val()
      });

      if (!sourceSectionId) return;

      await $.post("{{ route('stages.duplicate_section') }}", {
        _token: '{{ csrf_token() }}',
        product_id: f.productId,
        version: f.version,
        source_section_id: sourceSectionId,
        target_section_id: f.sectionId,
      });

      Swal.fire({ icon:'success', title:'Übernommen', toast:true, position:'top-end', timer:1400, showConfirmButton:false });
      loadStages();

    }catch(e){
      Swal.fire('Fehler', e?.responseJSON?.message || 'Aktion fehlgeschlagen.', 'error');
    }
  }

  // ============================================================
  // EASY: Copy whole bucket (entire product+version+section) to another bucket
  // Uses your backend route stages.duplicate_section? If that route is ONLY for sections within same product,
  // then create a new route stages.copy_bucket that accepts source product/version/section + target product/version/section.
  // ============================================================
  function openWholeBucketModal(){
    const f = readFilters();
    if (!f.productId || !f.sectionId){
      Swal.fire('Hinweis', 'Bitte Produkt und Sektion wählen.', 'info');
      return;
    }

    $('#wbSrcProduct').text($('#productFilter option:selected').text() || '—');
    $('#wbSrcSection').text($('#sectionFilter option:selected').text() || '—');
    $('#wbSrcVersion').text(f.version || '—');

    // default target = same product (user can change)
    $('#wbTargetProduct').val(f.productId).trigger('change');
    $('#wbTargetVersion').val((f.version || '').toString());

    initSelect2WholeBucket();
    Modal.open('xWholeBucketModal');

    // load sections for default target product
    loadSectionsIntoSelect($('#wbTargetSection'), f.productId, null);
  }

  async function submitWholeBucketCopy(){
    const f = readFilters();
    const targetProductId = $('#wbTargetProduct').val();
    const targetSectionId = $('#wbTargetSection').val();
    const targetVersion   = ($('#wbTargetVersion').val() || '').toString().trim();

    if (!targetProductId || !targetSectionId || !targetVersion){
      Swal.fire('Hinweis', 'Bitte Zielprodukt, Ziel-Sektion und Ziel-Version auswählen.', 'info');
      return;
    }

    // Requires backend route that copies whole bucket:
    // POST stages.copy_bucket {source_product_id, source_version, source_section_id, target_product_id, target_version, target_section_id}
    $.post('{{ route("stages.copy_bucket") }}', {
      _token: '{{ csrf_token() }}',
      source_product_id: f.productId,
      source_version: f.version,
      source_section_id: f.sectionId,
      target_product_id: targetProductId,
      target_version: targetVersion,
      target_section_id: targetSectionId
    }, function(res){
      Modal.close('xWholeBucketModal');
      Swal.fire({ icon:'success', title:'Kopiert', text:`${res.copied || 0} Schritt(e) kopiert.` });
      loadStages();
    }).fail((xhr) => {
      Swal.fire('Fehler', xhr.responseJSON?.message || 'Kopieren fehlgeschlagen.', 'error');
    });
  }

  // ============================================================
  // Multi target duplicate (selected stages)
  // ============================================================
  function openMultiTargetModal(){
    const f = readFilters();
    if (!f.productId || !f.sectionId){
      Swal.fire('Hinweis', 'Bitte Produkt und Sektion wählen.', 'info');
      return;
    }
    if (StageUI.selected.size < 1){
      Swal.fire('Hinweis', 'Bitte mindestens einen Schritt auswählen.', 'info');
      return;
    }

    $('#srcProductLabel').text($('#productFilter option:selected').text() || '—');
    $('#srcSectionLabel').text($('#sectionFilter option:selected').text() || '—');
    $('#srcVersionLabel').text(f.version || '—');
    $('#srcSelectedCount').text(StageUI.selected.size);

    $('#targetsWrap').empty();
    addTargetRow();
    Modal.open('xMultiTargetModal');
  }

  function addTargetRow(prefill = null){
    const tpl = document.getElementById('targetRowTpl');
    const node = tpl.content.cloneNode(true);
    const $row = $(node).find('.target-row');

    $('#targetsWrap').append($row);

    // init select2 for row
    $row.find('.target-product').select2({ width:'100%', dropdownParent: $('#xMultiTargetModal') });
    $row.find('.target-section').select2({ width:'100%', allowClear:true, placeholder:'Sektion wählen', dropdownParent: $('#xMultiTargetModal') });

    if (prefill?.product_id) $row.find('.target-product').val(String(prefill.product_id)).trigger('change');
    if (prefill?.version) $row.find('.target-version').val(prefill.version);

    $row.on('change', '.target-product', async function(){
      const pid = $(this).val();
      await loadSectionsIntoSelect($row.find('.target-section'), pid, null);
      if (prefill?.section_id) $row.find('.target-section').val(String(prefill.section_id)).trigger('change.select2');
    });

    $row.find('.target-product').trigger('change');
  }

  function submitMultiTargetDuplicate(){
    const targets = [];
    $('#targetsWrap .target-row').each(function(){
      const product_id = $(this).find('.target-product').val();
      const section_id = $(this).find('.target-section').val();
      const version    = ($(this).find('.target-version').val() || '').toString().trim();
      if (!product_id || !section_id || !version) return;
      targets.push({ product_id, section_id, version });
    });

    if (!targets.length){
      Swal.fire('Hinweis', 'Bitte mindestens ein Ziel vollständig auswählen.', 'info');
      return;
    }

    $.post('{{ route("stages.bulk_transfer_multi_targets") }}', {
      _token: '{{ csrf_token() }}',
      ids: Array.from(StageUI.selected),
      targets,
      mode: 'duplicate'
    }, function(res){
      Modal.close('xMultiTargetModal');
      Swal.fire({ icon:'success', title:'Dupliziert', text:`${res.copied || 0} Kopien erstellt.` });

      StageUI.selected.clear();
      $('.stage-select').prop('checked', false);
      syncSelectionUI();
      loadStages();
    }).fail((xhr) => {
      Swal.fire('Fehler', xhr.responseJSON?.message || 'Aktion fehlgeschlagen.', 'error');
    });
  }

  // ============================================================
  // Selection
  // ============================================================
  function syncSelectionUI(){
    const count = StageUI.selected.size;
    $('#selectedCount').text(count);
    $('#btnBulkDuplicate').prop('disabled', count < 1);
    $('#btnClearSelection').prop('disabled', count < 1);
  }

  // ============================================================
  // Events
  // ============================================================
  Modal.bind();

  $('#btnNewStage').on('click', openCreateModal);

  $('#productFilter').on('change', async function () {
    const productId = $(this).val();
    await loadVersions(productId);
    await loadSectionsForProduct(productId);
    loadStages();
  });

  $('#versionFilter, #sectionFilter').on('change', function () { loadStages(); });
  $('#searchInput').on('keyup', function () { loadStages(); });

  // stage modal product change: load versions + sections
  $(document).on('change', '#modalProductSelect', async function () {
    const productId = $(this).val();
    const $vs = $('#versionSelect');
    const desired = ($vs.data('desired') || '').toString().trim();

    $vs.empty();

    if (!productId) {
      $vs.trigger('change.select2');
      await loadSectionsIntoSelect($('#modalSectionSelect'), null, null);
      return;
    }

    $.get("{{ route('stages.get_versions') }}", { product_id: productId }, function (data) {
      (data.versions || []).forEach(function (version) {
        $vs.append(new Option(version, version, false, false));
      });
      $vs.trigger('change.select2');
      if (desired) setVersionSelect(desired);
    });

    await loadSectionsIntoSelect($('#modalSectionSelect'), productId, null);
  });

  // whole bucket modal: target product change => sections
  $(document).on('change', '#wbTargetProduct', function(){
    loadSectionsIntoSelect($('#wbTargetSection'), $(this).val(), null);
  });

  // selection
  $(document).on('change', '.stage-select', function () {
    const id = String($(this).val());
    if (this.checked) StageUI.selected.add(id);
    else StageUI.selected.delete(id);
    syncSelectionUI();
  });
  $(document).on('mousedown', '.stage-select', function (e) { e.stopPropagation(); });

  $('#btnToggleSelect').on('click', function () {
    StageUI.selectMode = !StageUI.selectMode;

    if (!StageUI.selectMode) {
      StageUI.selected.clear();
      $('.stage-select').prop('checked', false);
    }
    $('.stage-select-wrap').toggle(StageUI.selectMode);
    syncSelectionUI();
  });

  $('#btnClearSelection').on('click', function () {
    StageUI.selected.clear();
    $('.stage-select').prop('checked', false);
    syncSelectionUI();
  });

  $('#btnBulkDuplicate').on('click', openMultiTargetModal);

  // easy whole bucket copy
  $('#btnCopyWholeBucket').on('click', openWholeBucketModal);

  // duplicate from other section (same product)
  $('#btnDuplicateFromSection').on('click', duplicateFromOtherSection);

  // multi target modal events
  $(document).on('click', '#btnAddTargetRow', () => addTargetRow());
  $(document).on('click', '.btnRemoveTarget', function(){ $(this).closest('.target-row').remove(); });
  $(document).on('click', '.btnCopyFromFirst', function(){
    const $first = $('#targetsWrap .target-row').first();
    if (!$first.length) return;

    const p = $first.find('.target-product').val();
    const s = $first.find('.target-section').val();
    const v = $first.find('.target-version').val();

    const $row = $(this).closest('.target-row');
    $row.find('.target-product').val(p).trigger('change');

    setTimeout(() => {
      $row.find('.target-section').val(s).trigger('change.select2');
    }, 120);

    $row.find('.target-version').val(v);
  });

  // submit forms
  $('#stageForm').on('submit', function(e){ e.preventDefault(); saveStage(); });
  $('#multiTargetForm').on('submit', function(e){ e.preventDefault(); submitMultiTargetDuplicate(); });
  $('#wholeBucketForm').on('submit', function(e){ e.preventDefault(); submitWholeBucketCopy(); });

  // pagination (kept)
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    $.get($(this).attr('href'), function (data) {
      $('#stageListContainer').html(data.html);
      $('.stage-select-wrap').toggle(StageUI.selectMode);

      const stageCount = $('#stageListContainer').find('.stage-wrapper[data-id]').length;
      updateHintBar(stageCount);

      syncSelectionUI();
      initSortable();
    });
  });

  // expose for partial buttons
  window.StagePage = {
    loadStages,
    openCreateModal,
    openCreateStageModal,
    editStage,
    deleteStage,
    openMultiTargetModal,
    openWholeBucketModal
  };

  window.openCreateStageModal = openCreateStageModal;
  window.editStage = editStage;
  window.deleteStage = deleteStage;

  // init
  $(document).ready(function () {
    initSelect2Filters();
    initSelect2StageModal();
    initSelect2WholeBucket();
    updateHintBar();
    loadStages();
  });
})();
</script>
@endsection
