{{-- resources/views/admin/employee/position/position.blade.php --}}
@extends('admin.layouts.app')
@section('title') Position @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">

<style>
  :root{
    --g:#93c21c; --b:#74b2d4; --b2:#c0d8ea; --g2:#cfe09b;
    --ink:#0f172a; --muted:#64748b; --card:#ffffff;
    --danger:#ef4444;
  }

  .pos-wrap .xcard{ background:var(--card); border:1px solid rgba(15,23,42,.08); border-radius:16px; box-shadow:0 8px 24px rgba(15,23,42,.06); }
  .pos-wrap .xtab{ display:flex; gap:8px; padding:8px; flex-wrap:wrap; }
  .pos-wrap .xtab button{ border-radius:12px; padding:10px 14px; border:1px solid rgba(15,23,42,.08); background:#fff; color:var(--ink); font-weight:1000; }
  .pos-wrap .xtab button.active{ background:linear-gradient(180deg, var(--g), #7fb715); color:white; border-color:rgba(0,0,0,.05); }

  .pos-wrap .xbtn{ border-radius:12px; padding:10px 14px; font-weight:1000; border:1px solid rgba(15,23,42,.10); }
  .pos-wrap .xbtn-primary{ background:#93c21c; color:#fff; }
  .pos-wrap .xbtn-soft{ background:rgba(116,178,212,.15); color:var(--ink); }
  .pos-wrap .xbtn-danger{ background:rgba(239,68,68,.12); color:var(--ink); border-color:rgba(239,68,68,.25); }

  .pos-wrap .xfield{ border-radius:12px; border:1px solid rgba(15,23,42,.12); padding:10px 12px; width:100%; }
  .pos-wrap .xgrid{ display:grid; grid-template-columns: 1.4fr .6fr .6fr .5fr; gap:10px; }

  .pos-wrap .badge-pill{ border-radius:999px; padding:6px 10px; font-weight:1000; display:inline-flex; align-items:center; gap:6px; }
  .pos-wrap .badge-on{ background:rgba(147,194,28,.18); color:#365314; }
  .pos-wrap .badge-off{ background:rgba(100,116,139,.14); color:#334155; }

  .pos-wrap .board{ display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:14px; }
  @media(max-width: 1200px){ .pos-wrap .board{ grid-template-columns: repeat(2, minmax(0,1fr)); } }
  @media(max-width: 768px){ .pos-wrap .board{ grid-template-columns: 1fr; } .pos-wrap .xgrid{ grid-template-columns:1fr; } }

  .pos-wrap .droplist{ min-height:80px; padding:10px; border-radius:14px; border:1px dashed rgba(15,23,42,.18); background:rgba(192,216,234,.18); }
  .pos-wrap .drag-item{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 12px; margin:8px 0; background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px; cursor:grab; }

  .pos-wrap .split-2{ display:grid; grid-template-columns: 1fr 1fr; gap:14px; }
  @media(max-width: 992px){ .pos-wrap .split-2{ grid-template-columns:1fr; } }

  .pos-wrap .hint{ color:var(--muted); font-weight:900; }

  /* Info icon button */
  .pos-wrap .i-btn{
    width:34px; height:34px;
    border-radius:12px;
    border:1px solid rgba(15,23,42,.10);
    background:rgba(116,178,212,.14);
    display:inline-flex; align-items:center; justify-content:center;
    font-weight:1100;
    cursor:pointer;
    user-select:none;
    transition:transform .08s ease, background .12s ease;
  }
  .pos-wrap .i-btn:hover{ background:rgba(116,178,212,.22); transform:translateY(-1px); }
  .pos-wrap .i-btn:active{ transform:translateY(0); }

  /* Full text expand (optional usage in partials) */
  .pos-wrap .rm-clip{ max-width: 260px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .pos-wrap .rm-clip.is-open{ white-space:normal; overflow:visible; text-overflow:unset; }
  .pos-wrap .rm-clip-toggle{ cursor:pointer; border-bottom:1px dashed rgba(116,178,212,.55); }

  /* Custom popup */
  .pos-wrap .pop{
    position:fixed; inset:0;
    background:rgba(2,6,23,.55);
    z-index:10050;
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
  }
  .pos-wrap .pop.is-open{ display:flex; }
  .pos-wrap .pop-card{
    width:min(980px, 96vw);
    background:#fff;
    border-radius:18px;
    border:1px solid rgba(15,23,42,.08);
    box-shadow:0 18px 60px rgba(2,6,23,.22);
    overflow:hidden;
  }
  .pos-wrap .pop-head{
    padding:14px 16px;
    border-bottom:1px solid rgba(15,23,42,.08);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    background:linear-gradient(180deg, rgba(147,194,28,.14), rgba(255,255,255,1));
  }
  .pos-wrap .pop-title{ font-weight:1100; color:var(--ink); display:flex; align-items:center; gap:10px; }
  .pos-wrap .pop-close{
    border-radius:12px;
    padding:8px 12px;
    font-weight:1000;
    border:1px solid rgba(15,23,42,.10);
    background:rgba(15,23,42,.06);
    cursor:pointer;
  }
  .pos-wrap .pop-body{ padding:16px; }
  .pos-wrap .pop-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
  @media(max-width: 900px){ .pos-wrap .pop-grid{ grid-template-columns:1fr; } }
  .pos-wrap .pop-item{
    border:1px solid rgba(15,23,42,.08);
    border-radius:16px;
    padding:12px 12px;
    background:rgba(248,250,252,.8);
  }
  .pos-wrap .pop-item h4{
    margin:0 0 6px 0;
    font-weight:1100;
    font-size:13px;
    color:var(--ink);
    display:flex; align-items:center; gap:8px;
  }
  .pos-wrap .pop-item p{
    margin:0;
    color:var(--muted);
    font-weight:900;
    font-size:12px;
    line-height:1.55;
  }
  .pos-wrap .pop-pill{
    border-radius:999px;
    padding:4px 10px;
    font-weight:1100;
    font-size:11px;
    background:rgba(147,194,28,.18);
    color:#365314;
    border:1px solid rgba(147,194,28,.22);
  }

  /* ===========================
     ✅ NEUES KALKULATION-LAYOUT
     - linke Liste als Drawer
     - Matrix maximal breit + Scroll
     =========================== */

  .pos-wrap .cs-topbar{
    display:grid;
    grid-template-columns: 1.2fr .5fr .9fr .7fr .7fr .7fr;
    gap:10px;
  }
  @media(max-width: 1100px){
    .pos-wrap .cs-topbar{ grid-template-columns: 1fr 1fr; }
  }

  .pos-wrap .cs-shell{
    position:relative;
    display:block;
  }

  /* drawer overlay on mobile */
  .pos-wrap .cs-dim{
    position:fixed; inset:0;
    background:rgba(2,6,23,.45);
    z-index:10040;
    display:none;
  }
  .pos-wrap .cs-dim.is-open{ display:block; }

  .pos-wrap .cs-drawer{
    position:fixed;
    top:0; right:auto; left:0;
    height:100vh;
    width:min(420px, 92vw);
    background:#fff;
    z-index:10060;
    transform:translateX(-105%);
    transition:transform .18s ease;
    border-right:1px solid rgba(15,23,42,.10);
    box-shadow:0 18px 60px rgba(2,6,23,.18);
    display:flex;
    flex-direction:column;
  }
  .pos-wrap .cs-drawer.is-open{ transform:translateX(0); }
  .pos-wrap .cs-drawer-head{
    padding:12px 12px;
    border-bottom:1px solid rgba(15,23,42,.08);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    background:linear-gradient(180deg, rgba(116,178,212,.12), rgba(255,255,255,1));
  }
  .pos-wrap .cs-drawer-body{
    padding:12px;
    overflow:auto;
  }

  /* main matrix full width */
  .pos-wrap .cs-main-head{
    padding:10px 12px;
    border-bottom:1px solid rgba(15,23,42,.08);
    display:flex; align-items:center; justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
  }
  .pos-wrap .cs-badge{
    border-radius:999px;
    padding:6px 10px;
    font-weight:1100;
    font-size:12px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(15,23,42,.04);
    color:var(--ink);
    display:inline-flex;
    align-items:center;
    gap:8px;
  }

  /* matrix viewport */
  .pos-wrap .matrix-view{
    width:100%;
    overflow:auto;
    border-radius:14px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(248,250,252,.65);
  }

  .pos-wrap .matrix-view table{
    min-width: 980px; /* forces horizontal scroll instead of cutting columns */
    margin:0;
  }

  /* slightly tighter controls inside matrix */
  .pos-wrap .matrix-view .form-control{
    border-radius:10px;
    font-weight:900;
  }

</style>
@endsection

@section('content')
<div class="app-content content pos-wrap">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-1">
        <h2 class="content-header-title mb-0">POSITIONEN</h2>
      </div>
    </div>

    <div class="content-body">
      <div class="xcard p-2">
        <div class="xtab">
          <button type="button" class="active" data-tab="tab-positions">Positionen</button>
          <button type="button" data-tab="tab-qual">Qualifikationen</button>
          <button type="button" data-tab="tab-costing">Kalkulation</button>
        </div>

        <div class="p-2">
          {{-- TAB 1: Positions --}}
          <div id="tab-positions">
            <div class="xgrid mb-2">
              <input id="pos-q" class="xfield" placeholder="Suche (Position / Beschreibung)..." />
              <select id="pos-status" class="xfield">
                <option value="">Status: Alle</option>
                <option value="Published">Aktiv</option>
                <option value="Unpublished">Inaktiv</option>
              </select>
              <select id="pos-qual" class="xfield">
                <option value="">Qualifikation: Alle</option>
                @foreach($quals as $q)
                  <option value="{{ $q->id }}">{{ $q->name }} ({{ number_format($q->default_price,2,',','.') }}€)</option>
                @endforeach
              </select>
              <button id="btn-new" class="xbtn xbtn-primary" type="button">+ Erstellen</button>
            </div>

            <div id="positions-table-wrap" class="xcard p-1" style="background:rgba(255,255,255,.75)"></div>
          </div>

          {{-- TAB 2: Qualifications --}}
          <div id="tab-qual" style="display:none;">
            <div class="d-flex align-items-center justify-content-between mb-2" style="gap:10px;">
              <div class="hint">Ziehe Positionen in eine Qualifikation → setzt automatisch Qualifikation & Preis</div>
              <button id="btn-new-qual" class="xbtn xbtn-primary" type="button">+ Qualifikation</button>
            </div>
            <div id="qual-board-wrap"></div>
          </div>

          {{-- TAB 3: Kalkulation (NEU) --}}
          <div id="tab-costing" style="display:none;">
            <div class="d-flex align-items-center justify-content-between mb-2" style="gap:10px;">
              <div class="hint">Costing Set = AW + Gemeinkosten + Risiko + Provision + Rundung • Rollen-Matrix pro Qualifikation</div>
              <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button id="btn-new-costing" class="xbtn xbtn-primary" type="button">+ Costing Set</button>
                <button id="btn-cs-open-drawer" class="xbtn xbtn-soft" type="button">☰ Sets</button>
              </div>
            </div>

            <div class="xcard" style="padding:12px; margin-bottom:12px;">
              <div class="cs-topbar">
                <input id="cs-q" class="xfield" placeholder="Suche Costing Set..." />
                <button id="btn-cs-reload" class="xbtn xbtn-soft" type="button">↻ Reload</button>

                <select id="cs-selected" class="xfield">
                  <option value="">Set auswählen…</option>
                </select>

                <button id="btn-cs-sync-roles" class="xbtn xbtn-soft" type="button">Sync Rollen</button>
                <button id="btn-rm-defaults" class="xbtn xbtn-soft" type="button">Defaults anwenden</button>
                <button id="btn-cs-save-roles" class="xbtn xbtn-primary" type="button">Rollen speichern</button>
              </div>
            </div>

            <div class="cs-shell">
              {{-- Drawer dim --}}
              <div id="cs-dim" class="cs-dim" aria-hidden="true"></div>

              {{-- Drawer --}}
              <aside id="cs-drawer" class="cs-drawer" aria-hidden="true">
                <div class="cs-drawer-head">
                  <div style="font-weight:1100;">Costing Sets</div>
                  <button type="button" id="btn-cs-close-drawer" class="xbtn xbtn-soft" style="padding:8px 10px;">✕</button>
                </div>
                <div class="cs-drawer-body">
                  <div id="costing-sets-wrap"></div>
                </div>
              </aside>

              {{-- Main: Matrix --}}
              <div class="xcard p-1">
                <div class="cs-main-head">
                  <div style="font-weight:1100; display:flex; align-items:center; gap:10px;">
                    Rollen-Matrix
                    <button id="rm-info" class="i-btn" type="button" title="Was bedeuten die Spalten?">i</button>
                  </div>

                  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <div id="cs-active-badge" class="cs-badge" style="display:none;"></div>
                    <button id="btn-cs-info" class="xbtn xbtn-soft" type="button" style="padding:8px 12px;">ℹ Set-Details</button>
                  </div>
                </div>

                <div class="p-1">
                  <div id="costing-roles-wrap" class="matrix-view" style="min-height:260px; padding:10px; color:var(--muted); font-weight:900;">
                    Bitte Costing Set auswählen…
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- /TAB 3 --}}
        </div>
      </div>
    </div>
  </div>

  {{-- Global Modal --}}
  <div id="xmodal" style="display:none; position:fixed; inset:0; background:rgba(2,6,23,.6); z-index:9999;">
    <div style="max-width:760px; margin:6vh auto; background:#fff; border-radius:18px; overflow:hidden;">
      <div style="padding:14px 16px; border-bottom:1px solid rgba(15,23,42,.08); display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <div id="xmodal-title" style="font-weight:1100;"></div>
        <button type="button" id="xmodal-close" class="xbtn xbtn-soft">X</button>
      </div>
      <div id="xmodal-body" style="padding:16px;"></div>
    </div>
  </div>

  {{-- Rollen-Matrix Info --}}
  <div id="rm-pop" class="pop" aria-hidden="true">
    <div class="pop-card" role="dialog" aria-modal="true">
      <div class="pop-head">
        <div class="pop-title">
          <span class="pop-pill">ⓘ</span>
          <span>Rollen-Matrix erklärt</span>
        </div>
        <button type="button" class="pop-close" data-pop-close>Schließen</button>
      </div>
      <div class="pop-body">
        <div class="pop-grid">
          <div class="pop-item">
            <h4>Qualifikation / Rolle <span class="pop-pill">Wer?</span></h4>
            <p>Qualifikation (z.B. Geselle, Meister) ist die Basis für interne Kostenrate und Verkaufssatz.</p>
          </div>
          <div class="pop-item">
            <h4>Lohn €/h <span class="pop-pill">Kosten</span></h4>
            <p>Direkter Stundenlohn. Standard kommt aus <code>position_qualifications.default_price</code>.</p>
          </div>
          <div class="pop-item">
            <h4>Lohn-NK % <span class="pop-pill">Default</span></h4>
            <p>Kommt aus dem Costing Set: <code>default_payroll_overhead_percent</code>.</p>
          </div>
          <div class="pop-item">
            <h4>GK % <span class="pop-pill">Default</span></h4>
            <p>Kommt aus dem Costing Set: <code>default_company_overhead_percent</code>.</p>
          </div>
          <div class="pop-item">
            <h4>Vollkost €/h <span class="pop-pill">Auto</span></h4>
            <p><strong>Vollkost = Lohn × (1 + NK%/100 + GK%/100)</strong>.</p>
          </div>
          <div class="pop-item">
            <h4>VK €/h <span class="pop-pill">Auto</span></h4>
            <p><strong>VK = Vollkost × (1 + Markup%/100)</strong> (Markup aus <code>default_sell_markup_percent</code>).</p>
          </div>
          <div class="pop-item">
            <h4>AW <span class="pop-pill">Zeit</span></h4>
            <p>AW-Minuten kommen aus dem Costing Set (<code>aw_minutes</code>).</p>
          </div>
          <div class="pop-item">
            <h4>Rundung <span class="pop-pill">Regel</span></h4>
            <p>Rundungsmodus aus dem Costing Set (<code>rounding_mode</code>): keine / 0,05 / 0,10 / 1,00.</p>
          </div>
        </div>

        <div class="hint" style="margin-top:12px;">
          Tipp: Nach Änderungen im Set → „Defaults anwenden“, damit die Matrix neu berechnet wird.
        </div>
      </div>
    </div>
  </div>

  {{-- Costing Set Details Popup --}}
  <div id="cs-pop" class="pop" aria-hidden="true">
    <div class="pop-card" role="dialog" aria-modal="true">
      <div class="pop-head">
        <div class="pop-title">
          <span class="pop-pill">ⓘ</span>
          <span id="cs-pop-title">Costing Set Details</span>
        </div>
        <button type="button" class="pop-close" data-cs-pop-close>Schließen</button>
      </div>

      <div class="pop-body">
        <div class="pop-grid">
          <div class="pop-item">
            <h4>Basis <span class="pop-pill">AW & Rundung</span></h4>
            <p>
              AW: <strong id="cs-pop-aw">-</strong> Minuten<br>
              Rundung: <strong id="cs-pop-round-label">-</strong>
              <span style="display:inline-block;margin-left:6px;">
                <code id="cs-pop-round-mode" style="background:rgba(2,6,23,.06); padding:2px 6px; border-radius:8px; font-weight:1000; font-size:11px;">-</code>
              </span>
            </p>
          </div>

          <div class="pop-item">
            <h4>Gemeinkosten <span class="pop-pill">GK</span></h4>
            <p>
              Material GK: <strong id="cs-pop-gk-mat">-</strong>%<br>
              Lohn GK: <strong id="cs-pop-gk-lab">-</strong>%<br>
              Baustelle: <strong id="cs-pop-site-pct">-</strong>% + <strong id="cs-pop-site-fix">-</strong> €
            </p>
          </div>

          <div class="pop-item">
            <h4>Risiko & Gewinn <span class="pop-pill">Zuschläge</span></h4>
            <p>
              Risiko: <strong id="cs-pop-risk">-</strong>%<br>
              Gewinn: <strong id="cs-pop-profit">-</strong>%
            </p>
          </div>

          <div class="pop-item">
            <h4>Rollen-Defaults <span class="pop-pill">Matrix</span></h4>
            <p>
              Default Lohn-NK: <strong id="cs-pop-def-nk">-</strong>%<br>
              Default GK: <strong id="cs-pop-def-gk">-</strong>%<br>
              Default VK-Markup: <strong id="cs-pop-def-vk">-</strong>%
            </p>
          </div>

          <div class="pop-item">
            <h4>Einkauf <span class="pop-pill">Nebenkosten</span></h4>
            <p>
              Kleinteile: <strong id="cs-pop-small">-</strong>%<br>
              Fracht: <strong id="cs-pop-freight">-</strong> €<br>
              Handling: <strong id="cs-pop-handling">-</strong> €<br>
              Entsorgung: <strong id="cs-pop-disposal">-</strong> €
            </p>
          </div>

          <div class="pop-item">
            <h4>Provision <span class="pop-pill">Vertrieb</span></h4>
            <p>
              Modus: <strong id="cs-pop-comm-mode">-</strong><br>
              Wert: <strong id="cs-pop-comm-val">-</strong>
            </p>
          </div>
        </div>

        <div class="hint" style="margin-top:12px;">
          Tipp: Änderungen am Set speichern → dann ggf. „Defaults anwenden“.
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('script')
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

<script>
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const qs  = (s, r=document) => r.querySelector(s);
  const qsa = (s, r=document) => Array.from(r.querySelectorAll(s));
  const esc = (s='') => (''+s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

  const toNum = (v) => {
    if (v === null || v === undefined) return 0;
    v = (''+v).trim();
    if (!v) return 0;
    v = v.replace(/\s+/g, '');

    const hasComma = v.includes(',');
    const hasDot   = v.includes('.');

    if (hasComma && hasDot) {
      const lastComma = v.lastIndexOf(',');
      const lastDot   = v.lastIndexOf('.');
      if (lastComma > lastDot) v = v.replace(/\./g,'').replace(',', '.');
      else v = v.replace(/,/g,'');
    } else if (hasComma && !hasDot) {
      v = v.replace(',', '.');
    } else {
      v = v.replace(/,/g,'');
    }

    const n = parseFloat(v);
    return Number.isFinite(n) ? n : 0;
  };

  const fmt2 = (n) => {
    n = Number(n);
    if (!Number.isFinite(n)) n = 0;
    return (Math.round(n * 100) / 100).toFixed(2);
  };

  const fmtDE = (n) => {
    n = Number(n);
    if(!Number.isFinite(n)) n = 0;
    return (Math.round(n*100)/100).toFixed(2).replace('.', ',');
  };

  const modal = {
    open(title, html){
      qs('#xmodal-title').textContent = title;
      qs('#xmodal-body').innerHTML = html;
      qs('#xmodal').style.display = 'block';
    },
    close(){ qs('#xmodal').style.display='none'; }
  };
  qs('#xmodal-close')?.addEventListener('click', modal.close);
  qs('#xmodal')?.addEventListener('click', (e)=>{ if(e.target.id==='xmodal') modal.close(); });

  async function api(url, method='GET', data=null){
    const opt = { method, headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' } };
    if(method !== 'GET'){
      opt.headers['X-CSRF-TOKEN'] = CSRF;
      opt.headers['Content-Type'] = 'application/json';
      opt.body = JSON.stringify(data || {});
    }
    const res  = await fetch(url, opt);
    const text = await res.text();
    let json = {};
    try { json = JSON.parse(text); } catch(e) {}

    if(!res.ok){
      console.log('API ERROR', method, url, res.status, json, text);
      const msg =
        json?.message ||
        (json?.errors ? Object.values(json.errors).flat().join('\n') : '') ||
        `HTTP ${res.status}`;
      if (msg) alert(msg);
    }
    return json;
  }

  // -----------------------------
  // Tabs
  // -----------------------------
  function showTab(tab){
    qsa('.xtab button').forEach(b=>b.classList.remove('active'));
    qsa('.xtab button').find(b=>b.getAttribute('data-tab')===tab)?.classList.add('active');

    qs('#tab-positions').style.display = tab==='tab-positions' ? 'block' : 'none';
    qs('#tab-qual').style.display      = tab==='tab-qual'      ? 'block' : 'none';
    qs('#tab-costing').style.display   = tab==='tab-costing'   ? 'block' : 'none';

    if(tab==='tab-qual') loadBoard();
    if(tab==='tab-costing') loadCostingSets();
  }
  qsa('.xtab button').forEach(btn=>{
    btn.addEventListener('click', ()=> showTab(btn.getAttribute('data-tab')));
  });

  // -----------------------------
  // TAB 1: Positions list (AJAX)
  // -----------------------------
  let listState = { q:'', status:'', qualification_id:'', sort:'id', dir:'desc', page:1 };

  function buildListUrl(){
    const p = new URLSearchParams();
    if(listState.q) p.set('q', listState.q);
    if(listState.status) p.set('status', listState.status);
    if(listState.qualification_id) p.set('qualification_id', listState.qualification_id);
    p.set('sort', listState.sort);
    p.set('dir', listState.dir);
    p.set('page', listState.page);
    return `{{ route('position.list') }}?${p.toString()}`;
  }

  async function loadList(){
    const out = await api(buildListUrl());
    if(!out.ok) return;

    qs('#positions-table-wrap').innerHTML = out.html;

    qsa('#positions-table-wrap .pagination a').forEach(a=>{
      a.addEventListener('click', (e)=>{
        e.preventDefault();
        const url = new URL(a.href);
        listState.page = parseInt(url.searchParams.get('page') || '1', 10);
        loadList();
      });
    });

    qsa('#positions-table-wrap [data-sort]').forEach(th=>{
      th.addEventListener('click', ()=>{
        const key = th.getAttribute('data-sort');
        if(listState.sort === key) listState.dir = (listState.dir === 'asc' ? 'desc' : 'asc');
        else { listState.sort = key; listState.dir = 'asc'; }
        listState.page = 1;
        loadList();
      });
    });

    qsa('[data-action="toggle"]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        await api(`{{ url('/position') }}/${id}/toggle`, 'PATCH');
        loadList();
        loadBoard(true);
      });
    });

    qsa('[data-action="delete"]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        if(!confirm('Datensatz wirklich löschen?')) return;
        await api(`{{ url('/position') }}/${id}`, 'DELETE');
        loadList();
        loadBoard(true);
      });
    });

    qsa('[data-action="edit"]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-id');
        const pos = btn.getAttribute('data-position') || '';
        const status = btn.getAttribute('data-status') || 'Published';
        const qid = btn.getAttribute('data-qid') || '';
        const price = btn.getAttribute('data-price') || '';

        modal.open('Position bearbeiten', `
          <div class="mb-1">
            <label style="font-weight:1100;">Position</label>
            <input id="m-pos" class="xfield" value="${esc(pos)}">
          </div>
          <div class="mb-1">
            <label style="font-weight:1100;">Status</label>
            <select id="m-status" class="xfield">
              <option value="Published" ${status==='Published'?'selected':''}>Aktiv</option>
              <option value="Unpublished" ${status==='Unpublished'?'selected':''}>Inaktiv</option>
            </select>
          </div>
          <div class="mb-1">
            <label style="font-weight:1100;">Qualifikation</label>
            <select id="m-qid" class="xfield">
              <option value="">- keine -</option>
              ${qsa('#pos-qual option').filter(o=>o.value!=='').map(o=>`
                <option value="${o.value}" ${o.value===qid?'selected':''}>${esc(o.textContent)}</option>
              `).join('')}
            </select>
          </div>
          <div class="mb-2">
            <label style="font-weight:1100;">Preis (optional override)</label>
            <input id="m-price" class="xfield" value="${esc(price)}" placeholder="z.B. 69,00">
          </div>
          <div style="display:flex; gap:10px;">
            <button id="m-save" class="xbtn xbtn-primary" type="button">Speichern</button>
            <button id="m-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
          </div>
        `);

        qs('#m-cancel').addEventListener('click', modal.close);
        qs('#m-save').addEventListener('click', async ()=>{
          const payload = {
            position: qs('#m-pos').value.trim(),
            status: qs('#m-status').value,
            qualification_id: qs('#m-qid').value || null,
          };
          const p = qs('#m-price').value.trim();
          if(p !== '') payload.price = toNum(p);

          await api(`{{ url('/position') }}/${id}`, 'PUT', payload);
          modal.close();
          loadList();
          loadBoard(true);
        });
      });
    });

    qsa('[data-action="desc"]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-id');
        const title = btn.getAttribute('data-position') || 'Beschreibung';
        const html = btn.getAttribute('data-desc') || '';

        modal.open(`Arbeitsbeschreibung: ${title}`, `
          <div id="quillBox" style="height:360px;">${html}</div>
          <div class="mt-2" style="display:flex; gap:10px;">
            <button id="d-save" class="xbtn xbtn-primary" type="button">Speichern</button>
            <button id="d-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
          </div>
        `);

        const quill = new Quill('#quillBox', { theme:'snow' });
        qs('#d-cancel').addEventListener('click', modal.close);
        qs('#d-save').addEventListener('click', async ()=>{
          await api(`{{ url('/position') }}/${id}/description`, 'PATCH', { description: quill.root.innerHTML });
          modal.close();
          loadList();
        });
      });
    });
  }

  let tmr = null;
  qs('#pos-q')?.addEventListener('input', ()=>{
    clearTimeout(tmr);
    tmr = setTimeout(()=>{
      listState.q = qs('#pos-q').value.trim();
      listState.page = 1;
      loadList();
    }, 250);
  });
  qs('#pos-status')?.addEventListener('change', ()=>{
    listState.status = qs('#pos-status').value;
    listState.page = 1;
    loadList();
  });
  qs('#pos-qual')?.addEventListener('change', ()=>{
    listState.qualification_id = qs('#pos-qual').value;
    listState.page = 1;
    loadList();
  });

  qs('#btn-new')?.addEventListener('click', ()=>{
    modal.open('Neue Position', `
      <div class="mb-1">
        <label style="font-weight:1100;">Bezeichnung/Position</label>
        <input id="c-pos" class="xfield" placeholder="z.B. Monteur">
      </div>
      <div class="mb-1">
        <label style="font-weight:1100;">Qualifikation (optional)</label>
        <select id="c-qid" class="xfield">
          <option value="">- keine -</option>
          ${qsa('#pos-qual option').filter(o=>o.value!=='').map(o=>`
            <option value="${o.value}">${esc(o.textContent)}</option>
          `).join('')}
        </select>
      </div>
      <div style="display:flex; gap:10px;">
        <button id="c-save" class="xbtn xbtn-primary" type="button">Speichern</button>
        <button id="c-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
      </div>
    `);

    qs('#c-cancel').addEventListener('click', modal.close);
    qs('#c-save').addEventListener('click', async ()=>{
      await api(`{{ route('position.store.ajax') }}`, 'POST', {
        position: qs('#c-pos').value.trim(),
        qualification_id: qs('#c-qid').value || null,
      });
      modal.close();
      loadList();
      loadBoard(true);
    });
  });

  // -----------------------------
  // TAB 2: Qualification board (AJAX)
  // -----------------------------
  let boardQ = '';

  async function loadBoard(force=false){
    const p = new URLSearchParams();
    if(boardQ) p.set('q', boardQ);

    const out = await api(`{{ route('position.qual.board') }}?${p.toString()}`);
    if(!out.ok) return;

    qs('#qual-board-wrap').innerHTML = out.html;

    const sb = qs('#qual-sidebar-q');
    if(sb){
      let t=null;
      sb.addEventListener('input', ()=>{
        clearTimeout(t);
        t = setTimeout(()=>{
          boardQ = sb.value.trim();
          loadBoard(true);
        }, 200);
      });
    }

    const sidebar = qs('#sidebar-positions');
    if(sidebar){
      new Sortable(sidebar, {
        group: { name:'positionsPool', pull:'clone', put:false },
        sort:false,
        animation:150
      });
    }

    qsa('[data-qual-drop]').forEach(list=>{
      new Sortable(list, {
        group: { name:'positionsPool', pull:true, put:true },
        animation:150,
        onAdd: async (evt)=>{
          const posId = evt.item.getAttribute('data-pos-id');
          const qid = list.getAttribute('data-qual-id');

          await api(`{{ route('position.assign.qual') }}`, 'POST', {
            position_id: parseInt(posId,10),
            qualification_id: parseInt(qid,10)
          });

          loadList();
          loadBoard(true);
        }
      });
    });

    const qwrap = qs('#qual-cards');
    if(qwrap){
      new Sortable(qwrap, {
        animation:150,
        handle:'[data-qhandle]',
        onEnd: async ()=>{
          const order = qsa('[data-qcard]').map(n => parseInt(n.getAttribute('data-qcard'),10));
          await api(`{{ route('position.qual.reorder') }}`, 'POST', { order });
        }
      });
    }

    qsa('[data-qedit]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-qedit');
        const name = btn.getAttribute('data-name') || '';
        const price = btn.getAttribute('data-price') || '0';

        modal.open('Qualifikation bearbeiten', `
          <div class="mb-1">
            <label style="font-weight:1100;">Name</label>
            <input id="q-name" class="xfield" value="${esc(name)}">
          </div>
          <div class="mb-2">
            <label style="font-weight:1100;">Standard Preis</label>
            <input id="q-price" class="xfield" value="${esc(price)}">
          </div>
          <div style="display:flex; gap:10px;">
            <button id="q-save" class="xbtn xbtn-primary" type="button">Speichern</button>
            <button id="q-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
          </div>
        `);

        qs('#q-cancel').addEventListener('click', modal.close);
        qs('#q-save').addEventListener('click', async ()=>{
          await api(`{{ url('/position/qualifications') }}/${id}`, 'PUT', {
            name: qs('#q-name').value.trim(),
            default_price: toNum(qs('#q-price').value),
          });
          modal.close();
          loadBoard(true);
          loadList();
        });
      });
    });

    qsa('[data-qdelete]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-qdelete');
        if(!confirm('Qualifikation löschen? Alle Positionen werden abgehängt.')) return;
        await api(`{{ url('/position/qualifications') }}/${id}`, 'DELETE');
        loadBoard(true);
        loadList();
      });
    });
  }

  qs('#btn-new-qual')?.addEventListener('click', ()=>{
    modal.open('Neue Qualifikation', `
      <div class="mb-1">
        <label style="font-weight:1100;">Name</label>
        <input id="nq-name" class="xfield" placeholder="z.B. Geselle">
      </div>
      <div class="mb-2">
        <label style="font-weight:1100;">Standard Preis</label>
        <input id="nq-price" class="xfield" placeholder="z.B. 69,00">
      </div>
      <div style="display:flex; gap:10px;">
        <button id="nq-save" class="xbtn xbtn-primary" type="button">Speichern</button>
        <button id="nq-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
      </div>
    `);

    qs('#nq-cancel').addEventListener('click', modal.close);
    qs('#nq-save').addEventListener('click', async ()=>{
      await api(`{{ route('position.qual.store') }}`, 'POST', {
        name: qs('#nq-name').value.trim(),
        default_price: toNum(qs('#nq-price').value),
      });
      modal.close();
      loadBoard(true);
    });
  });

  // -----------------------------
  // TAB 3: Costing Sets + Matrix
  // -----------------------------
  let csState = { q:'', page:1 };
  let currentSetId = null;
  let currentSetRow = null; // used for Set-Details popup

  function buildCsListUrl(){
    const p = new URLSearchParams();
    if(csState.q) p.set('q', csState.q);
    p.set('page', csState.page);
    return `{{ route('admin.costing_sets.list') }}?${p.toString()}`;
  }

  // drawer open/close
  function openDrawer(){
    qs('#cs-drawer')?.classList.add('is-open');
    qs('#cs-dim')?.classList.add('is-open');
    qs('#cs-drawer')?.setAttribute('aria-hidden','false');
    qs('#cs-dim')?.setAttribute('aria-hidden','false');
  }
  function closeDrawer(){
    qs('#cs-drawer')?.classList.remove('is-open');
    qs('#cs-dim')?.classList.remove('is-open');
    qs('#cs-drawer')?.setAttribute('aria-hidden','true');
    qs('#cs-dim')?.setAttribute('aria-hidden','true');
  }
  qs('#btn-cs-open-drawer')?.addEventListener('click', openDrawer);
  qs('#btn-cs-close-drawer')?.addEventListener('click', closeDrawer);
  qs('#cs-dim')?.addEventListener('click', closeDrawer);

  // Costing Set popup (open via button)
  function roundLabel(mode){
    if(mode === '0.05') return 'Auf 0,05 €';
    if(mode === '0.10') return 'Auf 0,10 €';
    if(mode === '1.00') return 'Auf 1,00 €';
    return 'Keine';
  }
  function openCostingSetPopup(row){
    const pop = qs('#cs-pop');
    if(!pop) return;

    qs('#cs-pop-title').textContent = row?.name || 'Costing Set';
    qs('#cs-pop-aw').textContent = String(row?.aw_minutes ?? '-');
    qs('#cs-pop-round-label').textContent = roundLabel(row?.rounding_mode);
    qs('#cs-pop-round-mode').textContent = row?.rounding_mode || 'none';

    qs('#cs-pop-gk-mat').textContent   = fmtDE(row?.material_overhead_percent);
    qs('#cs-pop-gk-lab').textContent   = fmtDE(row?.labor_overhead_percent);
    qs('#cs-pop-site-pct').textContent = fmtDE(row?.site_overhead_percent ?? row?.project_overhead_percent);
    qs('#cs-pop-site-fix').textContent = fmtDE(row?.site_overhead_fixed ?? row?.project_overhead_fixed);

    qs('#cs-pop-risk').textContent   = fmtDE(row?.risk_percent);
    qs('#cs-pop-profit').textContent = fmtDE(row?.profit_percent);

    qs('#cs-pop-def-nk').textContent = fmtDE(row?.default_payroll_overhead_percent);
    qs('#cs-pop-def-gk').textContent = fmtDE(row?.default_company_overhead_percent);
    qs('#cs-pop-def-vk').textContent = fmtDE(row?.default_sell_markup_percent);

    qs('#cs-pop-small').textContent    = fmtDE(row?.small_parts_percent);
    qs('#cs-pop-freight').textContent  = fmtDE(row?.freight_fixed);
    qs('#cs-pop-handling').textContent = fmtDE(row?.handling_fixed);
    qs('#cs-pop-disposal').textContent = fmtDE(row?.disposal_fixed);

    qs('#cs-pop-comm-mode').textContent = row?.commission_mode || '-';
    qs('#cs-pop-comm-val').textContent  = String(row?.commission_value ?? '-');

    pop.classList.add('is-open');
    pop.setAttribute('aria-hidden','false');
  }
  function closeCostingSetPopup(){
    const pop = qs('#cs-pop');
    if(!pop) return;
    pop.classList.remove('is-open');
    pop.setAttribute('aria-hidden','true');
  }
  // bind once
  (function bindCsPopCloseOnce(){
    const pop = qs('#cs-pop');
    if(!pop || pop.__bound) return;
    pop.__bound = true;
    pop.addEventListener('click', (e)=>{ if(e.target === pop) closeCostingSetPopup(); });
    pop.querySelector('[data-cs-pop-close]')?.addEventListener('click', closeCostingSetPopup);
    document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeCostingSetPopup(); });
  })();

  qs('#btn-cs-info')?.addEventListener('click', ()=>{
    if(!currentSetRow){ alert('Bitte zuerst ein Costing Set auswählen.'); return; }
    openCostingSetPopup(currentSetRow);
  });

  async function loadCostingSets(){
    const out = await api(buildCsListUrl());
    if(!out.ok) return;

    qs('#costing-sets-wrap').innerHTML = out.html;

    // fill dropdown using data-json buttons
    const sel = qs('#cs-selected');
    const opts = ['<option value="">Set auswählen…</option>'];

    qsa('#costing-sets-wrap [data-cs-action="edit"]').forEach(btn=>{
      const id = btn.getAttribute('data-id');
      const js = btn.getAttribute('data-json') || '{}';
      try {
        const row = JSON.parse(js);
        const isSel = (String(id) === String(currentSetId));
        opts.push(`<option value="${id}" ${isSel?'selected':''}>${esc(row.name)}${row.is_default ? ' (Default)' : ''}</option>`);
      } catch(e){}
    });
    sel.innerHTML = opts.join('');

    // pagination
    qsa('#costing-sets-wrap .pagination a').forEach(a=>{
      a.addEventListener('click', (e)=>{
        e.preventDefault();
        const url = new URL(a.href);
        csState.page = parseInt(url.searchParams.get('page') || '1', 10);
        loadCostingSets();
      });
    });

    // select
    qsa('#costing-sets-wrap [data-cs-action="select"]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        currentSetId = btn.getAttribute('data-id');
        qs('#cs-selected').value = String(currentSetId);

        // also capture row json from neighbor edit button (same row)
        const tr = btn.closest('tr');
        const editBtn = tr?.querySelector('[data-cs-action="edit"]');
        if(editBtn){
          try { currentSetRow = JSON.parse(editBtn.getAttribute('data-json') || '{}'); } catch(e){ currentSetRow = null; }
        }

        // badge
        const badge = qs('#cs-active-badge');
        if(badge && currentSetRow){
          const isDef = !!currentSetRow.is_default;
          badge.style.display = 'inline-flex';
          badge.textContent = `${currentSetRow.name}${isDef ? ' • Default' : ''}`;
        }

        loadCostingRoles(currentSetId);
        closeDrawer();
      });
    });

    // make default
    qsa('#costing-sets-wrap [data-cs-action="make-default"]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        await api(`{{ url('/admin/settings/costing-sets') }}/${id}/make-default`, 'POST', {});
        await loadCostingSets();
        if(String(currentSetId) === String(id)){
          // reload selected row data + matrix
          qs('#cs-selected').value = String(currentSetId);
          await loadCostingRoles(currentSetId);
        }
      });
    });

    // delete
    qsa('#costing-sets-wrap [data-cs-action="delete"]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.getAttribute('data-id');
        if(!confirm('Costing Set wirklich löschen?')) return;
        await api(`{{ url('/admin/settings/costing-sets') }}/${id}`, 'DELETE', {});
        if(String(currentSetId) === String(id)){
          currentSetId = null;
          currentSetRow = null;
          qs('#cs-selected').value = '';
          qs('#cs-active-badge').style.display = 'none';
          qs('#costing-roles-wrap').innerHTML = 'Bitte Costing Set auswählen…';
        }
        loadCostingSets();
      });
    });

    // edit (open modal)
    qsa('#costing-sets-wrap [data-cs-action="edit"]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        let row = null;
        try { row = JSON.parse(btn.getAttribute('data-json') || '{}'); } catch(e){ row = null; }
        if(!row){ alert('Fehler: Set-Daten konnten nicht gelesen werden.'); return; }
        openCostingSetModal(row);
      });
    });
  }

  async function loadCostingRoles(setId){
    if(!setId){
      qs('#costing-roles-wrap').innerHTML = 'Bitte Costing Set auswählen…';
      return;
    }
    const out = await api(`{{ url('/admin/settings/costing-sets') }}/${setId}/roles`, 'GET');
    if(!out.ok) return;

    qs('#costing-roles-wrap').innerHTML = out.html;
    bindMatrix();
  }

  function openCostingSetModal(row=null){
    const isEdit = !!row;
    const id = row?.id;

    modal.open(isEdit ? 'Costing Set bearbeiten' : 'Neues Costing Set', `
      <div class="mb-1">
        <label style="font-weight:1100;">Name</label>
        <input id="cs-name" class="xfield" value="${esc(row?.name || '')}" placeholder="z.B. Standard 2026">
      </div>

      <div class="xgrid mb-1" style="grid-template-columns: .55fr .75fr .6fr .6fr;">
        <div>
          <label style="font-weight:1100;">AW Minuten</label>
          <input id="cs-aw" class="xfield" value="${esc(row?.aw_minutes ?? 6)}">
        </div>

        <div>
          <label style="font-weight:1100;">Rundung</label>
          <select id="cs-rounding" class="xfield">
            <option value="none" ${(row?.rounding_mode || 'none')==='none'?'selected':''}>Keine</option>
            <option value="0.05" ${(row?.rounding_mode || '')==='0.05'?'selected':''}>Auf 0,05 €</option>
            <option value="0.10" ${(row?.rounding_mode || '')==='0.10'?'selected':''}>Auf 0,10 €</option>
            <option value="1.00" ${(row?.rounding_mode || '')==='1.00'?'selected':''}>Auf 1,00 €</option>
          </select>
        </div>

        <div>
          <label style="font-weight:1100;">Default Lohn-NK %</label>
          <input id="cs-def-nk" class="xfield" value="${esc(row?.default_payroll_overhead_percent ?? 0)}">
        </div>

        <div>
          <label style="font-weight:1100;">Default GK %</label>
          <input id="cs-def-gk" class="xfield" value="${esc(row?.default_company_overhead_percent ?? 0)}">
        </div>
      </div>

      <div class="xgrid mb-1" style="grid-template-columns: .6fr .6fr .6fr;">
        <div>
          <label style="font-weight:1100;">Default VK Markup %</label>
          <input id="cs-def-vk" class="xfield" value="${esc(row?.default_sell_markup_percent ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Material GK %</label>
          <input id="cs-gk-mat" class="xfield" value="${esc(row?.material_overhead_percent ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Lohn GK %</label>
          <input id="cs-gk-lab" class="xfield" value="${esc(row?.labor_overhead_percent ?? 0)}">
        </div>
      </div>

      <div class="xgrid mb-1" style="grid-template-columns: .6fr .6fr .6fr .6fr;">
        <div>
          <label style="font-weight:1100;">Baustelle GK %</label>
          <input id="cs-site-pct" class="xfield" value="${esc(row?.site_overhead_percent ?? row?.project_overhead_percent ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Baustelle GK fix €</label>
          <input id="cs-site-fix" class="xfield" value="${esc(row?.site_overhead_fixed ?? row?.project_overhead_fixed ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Risiko %</label>
          <input id="cs-risk" class="xfield" value="${esc(row?.risk_percent ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Gewinn %</label>
          <input id="cs-profit" class="xfield" value="${esc(row?.profit_percent ?? 0)}">
        </div>
      </div>

      <div class="xgrid mb-1" style="grid-template-columns: .6fr .6fr .6fr .6fr;">
        <div>
          <label style="font-weight:1100;">Kleinteile %</label>
          <input id="cs-small" class="xfield" value="${esc(row?.small_parts_percent ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Fracht €</label>
          <input id="cs-freight" class="xfield" value="${esc(row?.freight_fixed ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Handling €</label>
          <input id="cs-handling" class="xfield" value="${esc(row?.handling_fixed ?? 0)}">
        </div>
        <div>
          <label style="font-weight:1100;">Entsorgung €</label>
          <input id="cs-disposal" class="xfield" value="${esc(row?.disposal_fixed ?? 0)}">
        </div>
      </div>

      <div class="xgrid mb-2" style="grid-template-columns: 1fr 1fr 1fr;">
        <div>
          <label style="font-weight:1100;">Provision Modus</label>
          <select id="cs-comm-mode" class="xfield">
            <option value="revenue_percent" ${(row?.commission_mode||'revenue_percent')==='revenue_percent'?'selected':''}>% vom Umsatz</option>
            <option value="fixed" ${(row?.commission_mode||'revenue_percent')==='fixed'?'selected':''}>Fix €</option>
            <option value="db_percent" ${(row?.commission_mode||'revenue_percent')==='db_percent'?'selected':''}>% von DB</option>
          </select>
        </div>
        <div>
          <label style="font-weight:1100;">Provision Wert</label>
          <input id="cs-comm-val" class="xfield" value="${esc(row?.commission_value ?? 0)}">
        </div>
        <div style="display:flex; gap:10px; align-items:end;">
          <label class="badge-pill" style="background:rgba(15,23,42,.06);">
            <input id="cs-active" type="checkbox" ${row ? (row.is_active ? 'checked' : '') : 'checked'} style="margin-right:8px;">
            Aktiv
          </label>
          <label class="badge-pill" style="background:rgba(147,194,28,.16);">
            <input id="cs-default" type="checkbox" ${row?.is_default ? 'checked' : ''} style="margin-right:8px;">
            Default
          </label>
        </div>
      </div>

      <div style="display:flex; gap:10px;">
        <button id="cs-save" class="xbtn xbtn-primary" type="button">Speichern</button>
        <button id="cs-cancel" class="xbtn xbtn-soft" type="button">Abbrechen</button>
      </div>
    `);

    qs('#cs-cancel').addEventListener('click', modal.close);

    qs('#cs-save').addEventListener('click', async ()=>{
      const payload = {
        name: qs('#cs-name').value.trim(),
        aw_minutes: toNum(qs('#cs-aw').value),

        default_payroll_overhead_percent: toNum(qs('#cs-def-nk').value),
        default_company_overhead_percent: toNum(qs('#cs-def-gk').value),
        default_sell_markup_percent: toNum(qs('#cs-def-vk').value),

        rounding_mode: qs('#cs-rounding').value || 'none',

        material_overhead_percent: toNum(qs('#cs-gk-mat').value),
        labor_overhead_percent: toNum(qs('#cs-gk-lab').value),

        site_overhead_percent: toNum(qs('#cs-site-pct').value),
        site_overhead_fixed: toNum(qs('#cs-site-fix').value),

        // backward compatibility (if controller still expects project_* for now)
        project_overhead_percent: toNum(qs('#cs-site-pct').value),
        project_overhead_fixed: toNum(qs('#cs-site-fix').value),

        risk_percent: toNum(qs('#cs-risk').value),
        profit_percent: toNum(qs('#cs-profit').value),

        small_parts_percent: toNum(qs('#cs-small').value),
        freight_fixed: toNum(qs('#cs-freight').value),
        handling_fixed: toNum(qs('#cs-handling').value),
        disposal_fixed: toNum(qs('#cs-disposal').value),

        commission_mode: qs('#cs-comm-mode').value,
        commission_value: toNum(qs('#cs-comm-val').value),

        is_active: qs('#cs-active').checked ? 1 : 0,
        is_default: qs('#cs-default').checked ? 1 : 0,
      };

      if(!payload.name){ alert('Name fehlt'); return; }

      if(isEdit) await api(`{{ url('/admin/settings/costing-sets') }}/${id}`, 'PUT', payload);
      else await api(`{{ url('/admin/settings/costing-sets') }}`, 'POST', payload);

      modal.close();
      csState.page = 1;
      await loadCostingSets();

      // refresh current set row + matrix after edit
      if(isEdit && String(currentSetId) === String(id)){
        // update cached row from the freshly re-rendered list
        const btn = qs(`#costing-sets-wrap [data-cs-action="edit"][data-id="${id}"]`);
        if(btn){
          try { currentSetRow = JSON.parse(btn.getAttribute('data-json') || '{}'); } catch(e){ currentSetRow = null; }
        }
        await loadCostingRoles(currentSetId);
      }
    });
  }

  // search/reload
  let csTmr = null;
  qs('#cs-q')?.addEventListener('input', ()=>{
    clearTimeout(csTmr);
    csTmr = setTimeout(()=>{
      csState.q = qs('#cs-q').value.trim();
      csState.page = 1;
      loadCostingSets();
    }, 250);
  });
  qs('#btn-cs-reload')?.addEventListener('click', ()=> loadCostingSets());

  qs('#cs-selected')?.addEventListener('change', ()=>{
    currentSetId = qs('#cs-selected').value || null;

    // update cached row from table edit button (if present)
    if(currentSetId){
      const btn = qs(`#costing-sets-wrap [data-cs-action="edit"][data-id="${currentSetId}"]`);
      if(btn){
        try { currentSetRow = JSON.parse(btn.getAttribute('data-json') || '{}'); } catch(e){ currentSetRow = null; }
      }
      const badge = qs('#cs-active-badge');
      if(badge && currentSetRow){
        badge.style.display = 'inline-flex';
        badge.textContent = `${currentSetRow.name}${currentSetRow.is_default ? ' • Default' : ''}`;
      }
    } else {
      currentSetRow = null;
      qs('#cs-active-badge').style.display = 'none';
    }

    loadCostingRoles(currentSetId);
  });

  qs('#btn-new-costing')?.addEventListener('click', ()=> openCostingSetModal(null));

  qs('#btn-cs-sync-roles')?.addEventListener('click', async ()=>{
    if(!currentSetId){ alert('Bitte Costing Set auswählen'); return; }
    await api(`{{ url('/admin/settings/costing-sets') }}/${currentSetId}/roles/sync`, 'POST', {});
    loadCostingRoles(currentSetId);
  });

  qs('#btn-rm-defaults')?.addEventListener('click', async ()=>{
    if(!currentSetId){ alert('Bitte Costing Set auswählen'); return; }
    await api(`{{ url('/admin/settings/costing-sets') }}/${currentSetId}/roles/apply-defaults`, 'POST', {});
    loadCostingRoles(currentSetId);
  });

  qs('#btn-cs-save-roles')?.addEventListener('click', async ()=>{
    if(!currentSetId){ alert('Bitte Costing Set auswählen'); return; }

    const rows = qsa('#costing-roles-wrap [data-role-row]').map(tr=>{
      const id = parseInt(tr.getAttribute('data-role-row'), 10);
      const get = (name) => tr.querySelector(`[data-field="${name}"]`)?.value ?? '';
      return {
        id,
        wage_cost_per_hour: toNum(get('wage_cost_per_hour')),
        payroll_overhead_percent: toNum(get('payroll_overhead_percent')),
        company_overhead_percent: toNum(get('company_overhead_percent')),
        full_cost_rate_per_hour: toNum(get('full_cost_rate_per_hour')),
        sell_rate_per_hour: toNum(get('sell_rate_per_hour')),
      };
    });

    await api(`{{ url('/admin/settings/costing-sets') }}/${currentSetId}/roles/bulk`, 'POST', { rows });

    // reload matrix from server (ensures newest DB values)
    loadCostingRoles(currentSetId);
  });

  // -----------------------------
  // Rollen-Matrix Auto-Berechnung
  // -----------------------------
  function calcFullCost(wage, nkPct, gkPct){
    return wage * (1 + (nkPct/100) + (gkPct/100));
  }

  function recalcRow(row){
    const getEl = (k) => row.querySelector(`[data-field="${k}"]`);
    const wage = toNum(getEl('wage_cost_per_hour')?.value);
    const nk   = toNum(getEl('payroll_overhead_percent')?.value);
    const gk   = toNum(getEl('company_overhead_percent')?.value);

    const full = calcFullCost(wage, nk, gk);

    const fullEl = getEl('full_cost_rate_per_hour');
    if(fullEl){
      fullEl.value = fmt2(full);
      fullEl.setAttribute('readonly','readonly');
    }

    // VK auto solange user nicht manuell überschreibt
    const sellEl = getEl('sell_rate_per_hour');
    if(sellEl){
      const userOverridden = sellEl.getAttribute('data-user') === '1';
      if(!userOverridden){
        sellEl.value = fmt2(full);
      }
    }
  }

  function bindMatrix(){
    const wrap = qs('#costing-roles-wrap');
    if(!wrap) return;

    qsa('[data-role-row]', wrap).forEach(row=>{
      if(row.__calcBound) return;
      row.__calcBound = true;

      const sellEl = row.querySelector('[data-field="sell_rate_per_hour"]');
      if(sellEl){
        sellEl.addEventListener('input', ()=> sellEl.setAttribute('data-user','1'));
        sellEl.addEventListener('blur', ()=>{
          if(!sellEl.value.trim()) sellEl.removeAttribute('data-user');
        });
      }

      ['wage_cost_per_hour','payroll_overhead_percent','company_overhead_percent'].forEach(k=>{
        const el = row.querySelector(`[data-field="${k}"]`);
        if(!el) return;
        el.addEventListener('input', ()=> recalcRow(row));
        el.addEventListener('change', ()=> recalcRow(row));
      });

      recalcRow(row);
    });
  }

  // -----------------------------
  // Popup open/close (rm-info)
  // -----------------------------
  function openRmPop(){
    const pop = qs('#rm-pop');
    if(!pop) return;
    pop.classList.add('is-open');
    pop.setAttribute('aria-hidden','false');
  }
  function closeRmPop(){
    const pop = qs('#rm-pop');
    if(!pop) return;
    pop.classList.remove('is-open');
    pop.setAttribute('aria-hidden','true');
  }

  document.addEventListener('click', (e)=>{
    if(e.target.closest('#rm-info')){ openRmPop(); return; }
    if(e.target.closest('[data-pop-close]')){ closeRmPop(); return; }

    const pop = qs('#rm-pop');
    if(pop && pop.classList.contains('is-open') && e.target === pop) closeRmPop();
  });
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeRmPop(); });

  // -----------------------------
  // Init
  // -----------------------------
  loadList();

})();
</script>
@endsection