{{-- resources/views/admin/assets/center.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Assets & Übergaben')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<style>
  :root{
    --mc-shell-bg:#f0f0f0;
    --mc-border: rgba(148, 163, 184, 0.35);
    --mc-border-soft: rgba(148, 163, 184, 0.18);
    --mc-text:#0b1120;
    --mc-muted:#424242;
    --mc-accent:#74b2d4;
    --mc-success:#93c21c;
    --mc-danger:#f97373;
    --mc-warning:#fbbf24;
    --mc-radius-lg:18px;
    --mc-radius-xl:22px;
    --mc-shadow-sm:0 1px 2px 0 rgb(0 0 0 / .06);
    --mc-shadow:0 10px 25px -10px rgb(0 0 0 / .22), 0 4px 8px -4px rgb(0 0 0 / .10);
    --mc-tr:all .18s ease;
  }

  .mc-page{ padding:18px 12px 30px; min-height:calc(100vh - 80px); }
  .mc-container{ max-width: 100%; margin:0 auto; }
  .mc-shell{ border-radius:var(--mc-radius-xl); padding:16px 18px 18px; }

  .mc-header{ display:flex; justify-content:space-between; gap:12px; align-items:flex-end; margin-bottom:12px; }
  .mc-title{ font-size:1.15rem; font-weight:800; letter-spacing:-0.02em; color:var(--mc-text); }
  .mc-subtitle{ font-size:0.78rem; color:var(--mc-muted); margin-top:4px; }

  .mc-header-right{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; justify-content:flex-end; }
  .mc-view-toggle{ display:inline-flex; border-radius:999px; border:1px solid var(--mc-border); overflow:hidden; font-size:0.75rem; background:#fff; }
  .mc-view-toggle button{
    border:none; background:transparent; color:var(--mc-muted);
    padding:6px 12px; cursor:pointer; display:inline-flex; gap:6px; align-items:center;
    transition:var(--mc-tr);
  }
  .mc-view-toggle button.is-active{ background:var(--mc-success); color:#111827; }

  .mc-btn{
    border-radius:999px; border:1px solid transparent; padding:7px 14px; font-size:0.78rem;
    display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:transparent; color:var(--mc-text);
    white-space:nowrap; transition:var(--mc-tr);
  }
  .mc-btn-ghost{ border-color:var(--mc-border); background:#fff; color:var(--mc-muted); }
  .mc-btn-primary{ border-color:var(--mc-accent); background:var(--mc-accent); color:#0b1120; }
  .mc-btn-danger{ border-color:var(--mc-danger); background:rgba(249,115,115,0.12); color:#7f1d1d; }

  .mc-toolbar{ display:grid; grid-template-columns: 2fr 1.7fr auto; gap:10px; margin:12px 0 12px; align-items:center; }
  @media (max-width: 1100px){ .mc-toolbar{ grid-template-columns: 1fr; } }
  .mc-input,.mc-select{
    width:100%; border-radius:999px; border:1px solid var(--mc-border-soft); background:#fff;
    padding:7px 10px; font-size:0.78rem; color:var(--mc-muted); outline:none;
    transition:var(--mc-tr);
  }
  .mc-input:focus,.mc-select:focus{ border-color:var(--mc-accent); box-shadow:0 0 0 3px rgba(116,178,212,.25); }

  .mc-inline{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; justify-content:flex-start; }
  .mc-inline.right{ justify-content:flex-end; }

  /* Stats */
  .mc-stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin:10px 0 14px; }
  .mc-stat{
    background:#fff; border:1px solid var(--mc-border-soft); border-radius:var(--mc-radius-lg);
    padding:12px 12px; box-shadow:var(--mc-shadow-sm); transition:var(--mc-tr);
  }
  .mc-stat:hover{ transform:translateY(-1px); box-shadow:var(--mc-shadow); }
  .mc-stat-l{ font-size:.70rem; letter-spacing:.08em; text-transform:uppercase; color:var(--mc-muted); font-weight:900; display:flex; align-items:center; gap:8px; }
  .mc-dot{ width:8px; height:8px; border-radius:99px; background:var(--mc-accent); display:inline-block; }
  .mc-stat-v{ margin-top:6px; font-size:1.45rem; font-weight:900; color:var(--mc-text); }

  /* Views */
  .mc-views{ margin-top: 6px; }
  .mc-view{ display:none; }
  .mc-view.is-active{ display:block; }

  /* Table */
  .mc-table-shell{ border-radius:var(--mc-radius-lg); border:1px solid var(--mc-border-soft); overflow:hidden; background:#fff; }
  .mc-table{ width:100%; border-collapse:collapse; font-size:0.78rem; }
  .mc-table th,.mc-table td{ padding:9px 10px; border-bottom:1px solid rgba(2,6,23,0.08); vertical-align:top; }
  .mc-table th{ text-transform:uppercase; letter-spacing:0.08em; font-size:0.7rem; color:var(--mc-muted); white-space:nowrap; background:rgba(2,6,23,0.03); }
  .mc-table tbody tr:hover{ background: rgba(147,194,28,0.10); }

  .mc-row-title{ font-weight:900; color:#0b1120; }
  .mc-row-sub{ font-size:0.73rem; color:var(--mc-muted); margin-top:2px; line-height:1.35; }

  .mc-pill{ border-radius:999px; padding:2px 8px; font-size:0.68rem; text-transform:uppercase; letter-spacing:0.06em; display:inline-flex; align-items:center; gap:6px; border:1px solid var(--mc-border-soft); background:#fff; color:var(--mc-muted); }
  .mc-pill.ok{ background:rgba(147,194,28,0.20); color:#365314; border-color:rgba(147,194,28,.22); }
  .mc-pill.warn{ background:rgba(251,191,36,0.20); color:#7c2d12; border-color:rgba(251,191,36,.25); }
  .mc-pill.bad{ background:rgba(249,115,115,0.20); color:#7f1d1d; border-color:rgba(249,115,115,.25); }

  .mc-actions{ display:flex; gap:8px; justify-content:flex-end; }
  .mc-icbtn{
    width:34px;height:34px;border-radius:12px;border:1px solid var(--mc-border-soft);
    background:#fff;color:var(--mc-muted);display:inline-flex;align-items:center;justify-content:center;
    cursor:pointer; transition:var(--mc-tr);
  }
  .mc-icbtn:hover{ background:#f9fafb; color:#0b1120; border-color:rgba(148,163,184,.35); }
  .mc-icbtn.primary{ background:rgba(116,178,212,.18); color:#0b1120; border-color:rgba(116,178,212,.35); }
  .mc-icbtn.danger{ background:rgba(249,115,115,.16); color:#7f1d1d; border-color:rgba(249,115,115,.30); }

  .mc-pagination{
    padding:10px 12px; font-size:0.74rem; color:var(--mc-muted);
    display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
    background:#fff;
  }
  .mc-pager{ display:flex; gap:8px; align-items:center; }
  .mc-pager span{ font-weight:800; color:#0b1120; }

  /* Modal */
  .mc-modal{
    position:fixed; inset:0; display:none; z-index:9999;
    background: rgba(2,6,23,0.55); padding:18px;
    align-items:center; justify-content:center;
  }
  .mc-modal.is-open{ display:flex; }
  .mc-modal-card{
    width:min(860px, 100%); border-radius:18px; background:#fff;
    border:1px solid rgba(2,6,23,0.12); overflow:hidden; box-shadow:var(--mc-shadow);
  }
  .mc-modal-hd{
    padding:12px 14px; border-bottom:1px solid rgba(2,6,23,0.08);
    display:flex; justify-content:space-between; align-items:flex-start; gap:12px;
    background:#fafafa;
  }
  .mc-modal-hd strong{ color:#0b1120; font-weight:900; font-size:.92rem; }
  .mc-modal-sub{ font-size:.74rem; color:var(--mc-muted); margin-top:2px; }
  .mc-modal-bd{ padding: 12px 14px; }
  .mc-close{
    border:none; background: rgba(2,6,23,0.06); border-radius: 999px;
    padding: 6px 10px; cursor:pointer; color:#0b1120;
  }
  .mc-modal-f{
    padding:12px 14px; border-top:1px solid rgba(2,6,23,0.08);
    display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;
    background:#fafafa;
  }
  .mc-err{
    margin-bottom:10px;padding:10px;border-radius:14px;border:1px solid rgba(249,115,115,.30);
    background:rgba(249,115,115,0.12);color:#7f1d1d;font-size:12px;white-space:pre-wrap;
    display:none;
  }
  .mc-err.is-on{ display:block; }

  .mc-form{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }
  @media(max-width: 900px){ .mc-form{ grid-template-columns:1fr; } }
  .mc-form .full{ grid-column:1 / -1; }
  .mc-lbl{ font-size:12px;font-weight:900;color:#0b1120;margin-bottom:6px; }
  .mc-inp, .mc-ta, .mc-sel{
    width:100%; border:1px solid var(--mc-border-soft); border-radius:14px;
    padding:10px 12px; font-size:13px; outline:none; background:#fff;
    transition:var(--mc-tr);
  }
  .mc-ta{ min-height:90px; resize:vertical; }
  .mc-inp:focus, .mc-ta:focus, .mc-sel:focus{ border-color:var(--mc-accent); box-shadow:0 0 0 3px rgba(116,178,212,.25); }

  /* Toast */
  .mc-toast-wrap{
    position:fixed; right:16px; bottom:16px; z-index:10000;
    display:flex; flex-direction:column; gap:10px;
    pointer-events:none;
  }
  .mc-toast{
    pointer-events:auto;
    min-width:280px; max-width:380px;
    background:#fff; border:1px solid var(--mc-border-soft); border-radius:16px;
    box-shadow:var(--mc-shadow);
    padding:12px 12px;
    display:flex; gap:10px; align-items:flex-start;
    animation:mcToastIn .18s ease forwards;
  }
  @keyframes mcToastIn{ from{ transform:translateY(10px); opacity:0 } to{ transform:translateY(0); opacity:1 } }
  .mc-toast-ic{ width:34px;height:34px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex:0 0 auto; }
  .mc-toast-ic.ok{ background:rgba(147,194,28,0.20); color:#365314; }
  .mc-toast-ic.warn{ background:rgba(251,191,36,0.20); color:#7c2d12; }
  .mc-toast-ic.bad{ background:rgba(249,115,115,0.20); color:#7f1d1d; }
  .mc-toast-ttl{ font-weight:900;font-size:12px;margin:0;color:#0b1120; }
  .mc-toast-msg{ font-size:12px;color:#374151;margin:2px 0 0 0;line-height:1.4; }
  .mc-toast-x{ margin-left:auto; pointer-events:auto; }

  .hidden{ display:none !important; }


  /* 2) Filter bar in ONE row (replace your .mc-toolbar block with this) */
  .mc-toolbar{
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap; /* stays one row on wide screens, wraps on small */
    margin:12px 0 12px;
  }
  .mc-toolbar .mc-grow{ flex:1 1 320px; min-width:260px; }
  .mc-toolbar .mc-filters{ flex:1 1 520px; min-width:320px; }
  .mc-toolbar .mc-right{ flex:0 0 auto; margin-left:auto; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

  /* 3) Select2 styling to match your UI */
  .select2-container{ width:100% !important; }
  .select2-container--default .select2-selection--single{
    height:34px;
    border-radius:999px;
    border:1px solid var(--mc-border-soft);
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:32px;
    padding-left:12px;
    color:var(--mc-muted);
    font-size:0.78rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow{
    height:32px;
    right:8px;
  }
  .select2-container--default .select2-selection--multiple{
    min-height:38px;
    border-radius:14px;
    border:1px solid var(--mc-border-soft);
    padding:4px 8px;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    border-radius:999px;
    border:1px solid var(--mc-border-soft);
    padding:2px 8px;
    margin-top:4px;
  }
  .select2-dropdown{ border:1px solid var(--mc-border-soft); border-radius:14px; overflow:hidden; }
</style>
@endsection

@section('content')
<div class="app-content"> 

  <div class="content-wrapper"> 

    <div class="content-body">
      <div class="mc-page" id="asset-center">
        <div class="mc-container">
          <div class="mc-shell">

            {{-- Header --}}
            <div class="mc-header"> 

              <div class="mc-header-right">
                <div class="mc-view-toggle" id="ac-tabs" role="tablist" aria-label="Tabs">
                  <button type="button" class="is-active" data-tab="assets" role="tab" aria-selected="true">
                    <i class="fa fa-box"></i><span>Assets</span>
                  </button>
                  <button type="button" data-tab="handovers" role="tab" aria-selected="false">
                    <i class="fa fa-exchange-alt"></i><span>Übergaben</span>
                  </button>
                </div>

                <button class="mc-btn mc-btn-ghost" id="ac-refresh" type="button" title="Aktualisieren">
                  <i class="fa fa-rotate-right"></i><span>Refresh</span>
                </button>

                <button class="mc-btn mc-btn-primary" id="ac-new" type="button" title="Neu">
                  <i class="fa fa-plus-circle"></i><span>Neu</span>
                </button>
              </div>
            </div>

            {{-- Analytics --}}
            <div class="mc-stats" id="ac-stats" aria-live="polite"></div>

            {{-- Toolbar --}}
            <div class="mc-toolbar" role="region" aria-label="Filter">
                <div class="mc-grow">
                    <input class="mc-input" id="ac-search" placeholder="Suche nach Item, Modell, Kategorie, Serial, Mitarbeiter..." autocomplete="off">
                </div>

                <div class="mc-filters" id="ac-filters">
                    {{-- ASSETS FILTERS --}}
                    <div class="mc-inline" id="ac-filters-assets">
                    <select class="mc-select" id="ac-filter-category" aria-label="Kategorie" style="min-width: 180px;">
                        <option value="">Kategorie: Alle</option>
                        @foreach(($categories ?? []) as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>

                    {{-- status MUST be Active/Inactive --}}
                    <select class="mc-select" id="ac-filter-status" aria-label="Status" style="min-width: 180px;">
                        <option value="">Status: Alle</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>

                    <select class="mc-select" id="ac-filter-purchase" aria-label="Kaufart" style="min-width: 180px;">
                        <option value="">Kaufart: Alle</option>
                        @foreach(($purchaseTypes ?? []) as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    </div>

                    {{-- HANDOVERS FILTERS --}}
                    <div class="mc-inline hidden" id="ac-filters-handovers">
                    <select class="mc-select" id="ac-filter-hstatus" aria-label="Übergabe Status" style="min-width: 180px;">
                        <option value="">Status: Alle</option>
                        @foreach(($handoverStatuses ?? []) as $hs)
                        <option value="{{ $hs }}">{{ $hs }}</option>
                        @endforeach
                    </select>

                    <select class="mc-select" id="ac-filter-employee" aria-label="Mitarbeiter" style="min-width: 220px;">
                        <option value="0">Mitarbeiter: Alle</option>
                        @foreach(($employees ?? []) as $e)
                        <option value="{{ $e->id }}">{{ trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')) }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>

                <div class="mc-right">
                    <select class="mc-select" id="ac-sort" aria-label="Sortierung" style="min-width: 220px;"></select>
                    <select class="mc-select" id="ac-perpage" aria-label="Einträge pro Seite" style="min-width: 150px;">
                    <option value="12">12 / Seite</option>
                    <option value="24">24 / Seite</option>
                    <option value="48">48 / Seite</option>
                    </select>
                </div>
            </div>

            {{-- Views --}}
            <div class="mc-views">
              <div class="mc-view is-active" id="ac-view-assets">
                <div class="mc-table-shell">
                  <table class="mc-table">
                    <thead>
                      <tr>
                        <th>Asset</th>
                        <th>Kategorie / Status</th>
                        <th>Menge / Wert</th>
                        <th>Ort / Zuordnung</th>
                        <th style="width: 110px; text-align:right;">Aktion</th>
                      </tr>
                    </thead>
                    <tbody id="ac-tbody-assets">
                      <tr><td colspan="5" style="text-align:center;padding:22px;color:var(--mc-muted)">Lädt…</td></tr>
                    </tbody>
                  </table>

                  <div class="mc-pagination">
                    <div id="ac-count">—</div>
                    <div class="mc-pager">
                      <button class="mc-icbtn" id="ac-prev" type="button" title="Zurück" aria-label="Zurück" disabled>
                        <i class="fa fa-chevron-left"></i>
                      </button>
                      <span id="ac-page">Seite 1</span>
                      <button class="mc-icbtn" id="ac-next" type="button" title="Weiter" aria-label="Weiter" disabled>
                        <i class="fa fa-chevron-right"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mc-view" id="ac-view-handovers">
                <div class="mc-table-shell">
                  <table class="mc-table">
                    <thead>
                      <tr>
                        <th>Übergabe</th>
                        <th>Asset</th>
                        <th>Mitarbeiter</th>
                        <th>Menge / Status</th>
                        <th style="width: 110px; text-align:right;">Aktion</th>
                      </tr>
                    </thead>
                    <tbody id="ac-tbody-handovers">
                      <tr><td colspan="5" style="text-align:center;padding:22px;color:var(--mc-muted)">Lädt…</td></tr>
                    </tbody>
                  </table>

                  <div class="mc-pagination">
                    <div id="ac-count-h">—</div>
                    <div class="mc-pager">
                      <button class="mc-icbtn" id="ac-prev-h" type="button" title="Zurück" aria-label="Zurück" disabled>
                        <i class="fa fa-chevron-left"></i>
                      </button>
                      <span id="ac-page-h">Seite 1</span>
                      <button class="mc-icbtn" id="ac-next-h" type="button" title="Weiter" aria-label="Weiter" disabled>
                        <i class="fa fa-chevron-right"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

            </div>{{-- /views --}}

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
 
{{-- Modal (rewritten) --}}
<div class="mc-modal" id="ac-modal" aria-hidden="true">
  <div class="mc-modal-card" role="dialog" aria-modal="true" aria-labelledby="ac-modal-title">
    <div class="mc-modal-hd">
      <div style="min-width:0">
        <strong id="ac-modal-title">Neu</strong>
        <div class="mc-modal-sub" id="ac-modal-sub">—</div>
      </div>
      <button type="button" class="mc-close" id="ac-modal-close">Schließen</button>
    </div>

    <div class="mc-modal-bd">
      <div class="mc-err" id="ac-modal-error"></div>

      {{-- ASSET FORM --}}
      <form id="ac-form-asset" class="mc-form">
        <div>
          <div class="mc-lbl">Item *</div>
          <input class="mc-inp" name="item" required>
        </div>

        <div>
          <div class="mc-lbl">Modell *</div>
          <input class="mc-inp" name="model" required>
        </div>

        <div>
          <div class="mc-lbl">Kategorie *</div>
          {{-- Select2 + tags (existing categories + new ones) --}}
          <select class="mc-sel" name="category" id="ac-asset-category" required>
            <option value="">—</option>
            @foreach(($categories ?? []) as $c)
              <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <div class="mc-lbl">Status *</div>
          {{-- fixed values --}}
          <select class="mc-sel" name="status" id="ac-asset-status" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

        <div>
          <div class="mc-lbl">Seriennummer</div>
          <input class="mc-inp" name="serial_no">
        </div>

        <div>
          <div class="mc-lbl">Artikelnummer</div>
          <input class="mc-inp" name="article_no">
        </div>

        <div>
          <div class="mc-lbl">Kaufart *</div>
          <input class="mc-inp" name="purchase_type" required>
        </div>

        <div>
          <div class="mc-lbl">Menge *</div>
          <input class="mc-inp" name="quantity" type="number" min="0" required>
        </div>

        <div>
          <div class="mc-lbl">Kaufpreis</div>
          <input class="mc-inp" name="purchase_price" type="number" step="0.01" min="0">
        </div>

        <div>
          <div class="mc-lbl">Ort</div>
          <input class="mc-inp" name="location">
        </div>

        <div>
          <div class="mc-lbl">Filiale</div>
          <select class="mc-sel" name="branch_id" id="ac-asset-branch">
            <option value="">—</option>
            @foreach(($branches ?? []) as $b)
              <option value="{{ $b->id }}">{{ $b->branch }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <div class="mc-lbl">Übergabe an (optional)</div>
          {{-- Select2 employee + allowClear --}}
          <select class="mc-sel" name="handover_id" id="ac-asset-handover">
            <option value="">—</option>
            @foreach(($employees ?? []) as $e)
              <option value="{{ $e->id }}">{{ trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')) }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <div class="mc-lbl">Kaufdatum</div>
          {{-- JS sets today by default on create --}}
          <input class="mc-inp" name="purchase_date" id="ac-asset-purchase-date" type="date">
        </div>

        <div>
          <div class="mc-lbl">Ablaufdatum</div>
          <input class="mc-inp" name="expire_date" type="date">
        </div>

        <div class="full">
          <div class="mc-lbl">Beschreibung</div>
          <textarea class="mc-ta" name="description"></textarea>
        </div>
      </form>

      {{-- HANDOVER FORM --}}
      <form id="ac-form-handover" class="mc-form hidden">
        <div class="full" style="font-size:12px;color:var(--mc-muted);font-weight:700">
          Hinweis: <b>item_id</b> ist die Asset-ID (später kann man hier ein Select/Search machen).
        </div>

        <div class="full">
            <div class="mc-lbl">Asset *</div>
            <select class="mc-sel" name="item_id" id="ac-handover-asset" required></select>
            <div class="mc-row-sub" style="margin-top:6px">
                Zeigt nur Assets ohne Übergabe (frei) inkl. Kaufdatum &amp; Restmenge.
            </div>
        </div>


        <div>
          <div class="mc-lbl">Mitarbeiter *</div>
          {{-- Select2 employee --}}
          <select class="mc-sel" name="handover_id" id="ac-handover-employee" required>
            <option value="">—</option>
            @foreach(($employees ?? []) as $e)
              <option value="{{ $e->id }}">{{ trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')) }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <div class="mc-lbl">Menge</div>
          <input class="mc-inp" name="quantity" type="number" min="0">
        </div>

        <div>
          <div class="mc-lbl">Status</div>
          <input class="mc-inp" name="status">
        </div>

        <div class="full">
          <div class="mc-lbl">Bemerkung</div>
          <input class="mc-inp" name="remark">
        </div>
      </form>
    </div>

    <div class="mc-modal-f">
      <button class="mc-btn mc-btn-ghost" id="ac-cancel" type="button">
        <i class="fa fa-ban"></i><span>Abbrechen</span>
      </button>
      <button class="mc-btn mc-btn-danger hidden" id="ac-delete" type="button">
        <i class="fa fa-trash"></i><span>Löschen</span>
      </button>
      <button class="mc-btn mc-btn-primary" id="ac-save" type="button">
        <i class="fa fa-save"></i><span>Speichern</span>
      </button>
    </div>
  </div>
</div>

{{-- Toast --}}
<div class="mc-toast-wrap" id="ac-toast" aria-live="polite" aria-atomic="true"></div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 
<script>
(() => {
  "use strict";

  const ROOT = document.getElementById("asset-center");
  if (!ROOT) return;

  const ENDPOINTS = {
    assets_fetch:   @json(route('handover.assets.fetch')),
    assets_store:   @json(route('handover.assets.store')),
    assets_update:  @json(route('handover.assets.update', ['asset' => 0])).replace(/\/0$/, ''),
    assets_delete:  @json(route('handover.assets.destroy', ['asset' => 0])).replace(/\/0$/, ''),

    handovers_fetch:  @json(route('handover.handovers.fetch')),
    handovers_store:  @json(route('handover.handovers.store')),
    handovers_update: @json(route('handover.handovers.update', ['handover' => 0])).replace(/\/0$/, ''),
    handovers_delete: @json(route('handover.handovers.destroy', ['handover' => 0])).replace(/\/0$/, ''),
    assets_available: @json(route('handover.assets.available')),

  };

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  const esc = (s) => String(s ?? "")
    .replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;").replaceAll("'", "&#039;");

  const debounce = (fn, ms=250) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };


  function s2AssetTpl(data){
    if (!data || data.loading) return esc(data?.text || "");
    const item = esc(data.item || "");
    const model = esc(data.model || "");
    const cat = esc(data.category || "");
    const pd = esc(data.purchase_date || "—");
    const rem = esc(data.remaining ?? "");
    return `
        <div style="min-width:0">
        <div style="font-weight:900;color:#0b1120">${item} • ${model}</div>
        <div style="font-size:12px;color:var(--mc-muted);line-height:1.35">
            ${cat ? ("Kategorie: " + cat + " • ") : ""}Kauf: ${pd} • Rest: ${rem}
        </div>
        </div>
    `;
    }

  /* ===================== REQUEST ===================== */
  const controllers = new Map();
  function abortKey(key){
    const c = controllers.get(key);
    if (c) { try { c.abort(); } catch(_) {} controllers.delete(key); }
  }

  async function api(key, url, data={}, method="GET"){
    abortKey(key);
    const controller = new AbortController();
    controllers.set(key, controller);

    const headers = { Accept:"application/json", "X-Requested-With":"XMLHttpRequest" };
    const opts = { method, headers, signal: controller.signal };
    let finalUrl = url;

    if (method === "GET"){
      const qs = new URLSearchParams(data).toString();
      if (qs) finalUrl += (finalUrl.includes("?") ? "&" : "?") + qs;
    } else {
      headers["Content-Type"] = "application/json";
      if (CSRF) headers["X-CSRF-TOKEN"] = CSRF;
      opts.body = JSON.stringify(data);
    }

    let res, txt="";
    try {
      res = await fetch(finalUrl, opts);
      txt = await res.text().catch(()=> "");
    } finally {
      if (controllers.get(key) === controller) controllers.delete(key);
    }

    if (!res.ok){
      const err = new Error("HTTP " + res.status);
      err.status = res.status;
      err.raw = txt;
      throw err;
    }

    try { return JSON.parse(txt || "{}"); } catch(_) { return {}; }
  }

  function parse422(raw){
    try {
      const j = JSON.parse(raw || "{}");
      const msg = j.message || "Validierung fehlgeschlagen.";
      const errs = j.errors || {};
      const lines = [];
      Object.keys(errs).forEach(k => Array.isArray(errs[k]) && lines.push(...errs[k]));
      return lines.length ? (msg + "\n" + lines.join("\n")) : msg;
    } catch(_) {
      return (raw ? String(raw).slice(0, 900) : "Validierung fehlgeschlagen.");
    }
  }

  /* ===================== TOAST ===================== */
  const TOAST_WRAP = $("#ac-toast");
  function toast(kind, title, msg, ttl=3200){
    if (!TOAST_WRAP) return;

    const icon =
      kind === "ok"
        ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`
        : kind === "warn"
        ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;

    const el = document.createElement("div");
    el.className = "mc-toast";
    el.innerHTML = `
      <div class="mc-toast-ic ${esc(kind)}">${icon}</div>
      <div style="min-width:0">
        <p class="mc-toast-ttl">${esc(title || "")}</p>
        <p class="mc-toast-msg">${esc(msg || "")}</p>
      </div>
      <button class="mc-icbtn mc-toast-x" type="button" aria-label="Schließen" title="Schließen" style="width:28px;height:28px;border-radius:12px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    `;
    TOAST_WRAP.appendChild(el);

    const kill = () => { try { el.remove(); } catch(_) {} };
    el.querySelector(".mc-toast-x")?.addEventListener("click", kill);
    setTimeout(kill, ttl);
  }

  /* ===================== DOM ===================== */
  const DOM = {
    tabsWrap: $("#ac-tabs"),
    tabBtns: $$("#ac-tabs button[data-tab]"),

    refresh: $("#ac-refresh"),
    newBtn: $("#ac-new"),

    stats: $("#ac-stats"),

    search: $("#ac-search"),
    sort: $("#ac-sort"),
    perpage: $("#ac-perpage"),

    filtersAssets: $("#ac-filters-assets"),
    filtersHandovers: $("#ac-filters-handovers"),

    fCategory: $("#ac-filter-category"),
    fStatus: $("#ac-filter-status"),
    fPurchase: $("#ac-filter-purchase"),

    fhStatus: $("#ac-filter-hstatus"),
    fhEmployee: $("#ac-filter-employee"),

    viewAssets: $("#ac-view-assets"),
    viewHandovers: $("#ac-view-handovers"),

    tbAssets: $("#ac-tbody-assets"),
    tbHandovers: $("#ac-tbody-handovers"),

    countAssets: $("#ac-count"),
    countHandovers: $("#ac-count-h"),

    prevA: $("#ac-prev"),
    nextA: $("#ac-next"),
    pageA: $("#ac-page"),

    prevH: $("#ac-prev-h"),
    nextH: $("#ac-next-h"),
    pageH: $("#ac-page-h"),

    // modal
    modal: $("#ac-modal"),
    modalTitle: $("#ac-modal-title"),
    modalSub: $("#ac-modal-sub"),
    modalClose: $("#ac-modal-close"),
    modalErr: $("#ac-modal-error"),
    cancel: $("#ac-cancel"),
    save: $("#ac-save"),
    del: $("#ac-delete"),
    formAsset: $("#ac-form-asset"),
    formHandover: $("#ac-form-handover"),
  };

  /* ===================== STATE ===================== */
  const state = {
    tab: "assets",
    assets: { page:1, per_page:12, q:"", sort:"newest", category:"", status:"", purchase_type:"", items:[], stats:{}, pagination:{} },
    handovers: { page:1, per_page:12, q:"", sort:"newest", status:"", employee_id:0, items:[], stats:{}, pagination:{} },
    modal: { entity:"assets", mode:"create", id:null },
  };

  const SORT_OPTIONS = {
    assets: [
      ["newest","Neueste"],
      ["oldest","Älteste"],
      ["item_asc","Item A–Z"],
      ["item_desc","Item Z–A"],
      ["category","Kategorie"],
      ["status","Status"],
      ["qty_desc","Menge ↓"],
      ["value_desc","Wert ↓"],
    ],
    handovers: [
      ["newest","Neueste"],
      ["oldest","Älteste"],
      ["qty_desc","Menge ↓"],
      ["status","Status"],
      ["employee","Mitarbeiter"],
    ],
  };

  function moneyEUR(v){
    const n = Number(v || 0);
    try { return new Intl.NumberFormat("de-DE", { style:"currency", currency:"EUR" }).format(n); }
    catch(_) { return (Math.round(n*100)/100).toFixed(2) + " €"; }
  }

  function statCard(label, value, color){
    return `
      <div class="mc-stat">
        <div class="mc-stat-l"><span class="mc-dot" style="background:${esc(color)}"></span>${esc(label)}</div>
        <div class="mc-stat-v">${esc(value)}</div>
      </div>
    `;
  }

  function renderStats(){
    if (state.tab === "assets"){
      const s = state.assets.stats || {};
      DOM.stats.innerHTML = [
        statCard("Assets", Number(s.total_assets||0), "var(--mc-accent)"),
        statCard("Gesamtmenge", Number(s.total_qty||0), "var(--mc-success)"),
        statCard("Gesamtwert", moneyEUR(s.total_value||0), "var(--mc-warning)"),
        statCard("Leasing", Number(s.leasing_count||0), "#0ea5e9"),
        statCard("Ablauf ≤ 30T", Number(s.expiring_30||0), "var(--mc-danger)"),
      ].join("");
    } else {
      const s = state.handovers.stats || {};
      DOM.stats.innerHTML = [
        statCard("Übergaben", Number(s.total_handovers||0), "var(--mc-success)"),
        statCard("Gesamtmenge", Number(s.total_qty||0), "var(--mc-accent)"),
      ].join("");
    }
  }

  function setSortSelect(){
    const opts = SORT_OPTIONS[state.tab] || [];
    DOM.sort.innerHTML = opts.map(([v,l]) => `<option value="${esc(v)}">${esc(l)}</option>`).join("");
    DOM.sort.value = state[state.tab].sort || (opts[0]?.[0] || "newest");
  }

  function setTab(tab){
    state.tab = tab;

    DOM.tabBtns.forEach(b => {
      const on = b.getAttribute("data-tab") === tab;
      b.classList.toggle("is-active", on);
      b.setAttribute("aria-selected", on ? "true" : "false");
    });

    DOM.viewAssets.classList.toggle("is-active", tab === "assets");
    DOM.viewHandovers.classList.toggle("is-active", tab === "handovers");

    DOM.filtersAssets.classList.toggle("hidden", tab !== "assets");
    DOM.filtersHandovers.classList.toggle("hidden", tab !== "handovers");

    setSortSelect();
    load();
  }

  function pagerInfo(p){
    const page = Number(p?.page || 1);
    const per = Number(p?.per_page || 12);
    const total = Number(p?.total || 0);
    const pages = Math.max(1, Math.ceil(total / Math.max(1, per)));
    const first = total ? ((page - 1) * per + 1) : 0;
    const last = total ? Math.min(total, page * per) : 0;
    return { page, per, total, pages, first, last, has_more: !!p?.has_more };
  }

  function setPagerAssets(p){
    const info = pagerInfo(p);
    DOM.pageA.textContent = `Seite ${info.page} von ${info.pages}`;
    DOM.prevA.disabled = info.page <= 1;
    DOM.nextA.disabled = !info.has_more;
    DOM.countAssets.textContent = info.total ? `Zeige ${info.first}–${info.last} von ${info.total}` : "—";
  }

  function setPagerHandovers(p){
    const info = pagerInfo(p);
    DOM.pageH.textContent = `Seite ${info.page} von ${info.pages}`;
    DOM.prevH.disabled = info.page <= 1;
    DOM.nextH.disabled = !info.has_more;
    DOM.countHandovers.textContent = info.total ? `Zeige ${info.first}–${info.last} von ${info.total}` : "—";
  }

  function pillTone(v){
    const s = String(v ?? "").trim().toLowerCase();
    if (!s) return "";
    const ok = ["active","ok","done","completed","returned","verfügbar","available"];
    const warn = ["pending","in_progress","leasing","reserved","soon","bald"];
    const bad = ["lost","broken","inactive","expired","defect","overdue","defekt","abgelaufen"];
    if (ok.includes(s)) return "ok";
    if (warn.includes(s)) return "warn";
    if (bad.includes(s)) return "bad";
    return "";
  }

  function emptyRow(cols, msg){
    return `<tr><td colspan="${cols}" style="text-align:center;padding:22px;color:var(--mc-muted)">${esc(msg || "Keine Einträge.")}</td></tr>`;
  }

  function renderAssets(){
    const items = state.assets.items || [];
    if (!items.length){
      DOM.tbAssets.innerHTML = emptyRow(5, "Keine Assets gefunden.");
      return;
    }

    DOM.tbAssets.innerHTML = items.map(a => {
      const title = `${a.item || "—"} • ${a.model || "—"}`;
      const sub = [
        a.serial_no ? `SN: ${a.serial_no}` : null,
        a.article_no ? `Art: ${a.article_no}` : null,
        a.purchase_type ? `Kaufart: ${a.purchase_type}` : null,
      ].filter(Boolean).join(" • ");

      const statusTone = pillTone(a.status);
      const statusPill = a.status
        ? `<span class="mc-pill ${statusTone}"><i class="fa fa-circle" style="font-size:8px"></i>${esc(a.status)}</span>`
        : `<span class="mc-pill">—</span>`;

      const cat = a.category ? `<div class="mc-row-sub"><b>Kategorie:</b> ${esc(a.category)}</div>` : `<div class="mc-row-sub">—</div>`;

      const qty = Number(a.quantity ?? 0);
      const totalVal = moneyEUR(a.total_value ?? (Number(a.purchase_price ?? 0) * qty));

      const assign = [
        a.branch_name ? `Branch: ${a.branch_name}` : null,
        a.handover_name ? `MA: ${a.handover_name}` : null,
        a.location ? `Ort: ${a.location}` : null,
      ].filter(Boolean);

      return `
        <tr>
          <td>
            <div class="mc-row-title">${esc(title)}</div>
            <div class="mc-row-sub">${esc(sub || "—")}</div>
          </td>
          <td>
            ${cat}
            <div style="margin-top:6px">${statusPill}</div>
          </td>
          <td>
            <div class="mc-row-title">${esc(qty)}</div>
            <div class="mc-row-sub">Wert: ${esc(totalVal)}</div>
            <div class="mc-row-sub">Ablauf: ${esc(a.expire_date || "—")}</div>
          </td>
          <td>
            <div class="mc-row-sub">${esc(assign.length ? assign.join(" • ") : "—")}</div>
            <div class="mc-row-sub">${a.parent_item ? ("Parent: " + esc(a.parent_item)) : ""}</div>
          </td>
          <td style="text-align:right">
            <div class="mc-actions">
              <button class="mc-icbtn primary" type="button" data-action="edit" data-entity="assets" data-id="${esc(a.id)}" title="Bearbeiten" aria-label="Bearbeiten">
                <i class="fa fa-pen"></i>
              </button>
              <button class="mc-icbtn danger" type="button" data-action="delete" data-entity="assets" data-id="${esc(a.id)}" title="Löschen" aria-label="Löschen">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join("");
  }

  function renderHandovers(){
    const items = state.handovers.items || [];
    if (!items.length){
      DOM.tbHandovers.innerHTML = emptyRow(5, "Keine Übergaben gefunden.");
      return;
    }

    DOM.tbHandovers.innerHTML = items.map(h => {
      const statusTone = pillTone(h.status);
      const st = h.status
        ? `<span class="mc-pill ${statusTone}"><i class="fa fa-circle" style="font-size:8px"></i>${esc(h.status)}</span>`
        : `<span class="mc-pill">—</span>`;

      return `
        <tr>
          <td>
            <div class="mc-row-title">#${esc(h.id)}</div>
            <div class="mc-row-sub">${esc(h.created_at || "—")}</div>
          </td>
          <td>
            <div class="mc-row-title">${esc(h.asset_item || "—")}</div>
            <div class="mc-row-sub">${esc(h.asset_model ? ("Modell: " + h.asset_model) : (h.item_id ? ("Asset-ID: " + h.item_id) : "—"))}</div>
            <div class="mc-row-sub">${esc(h.asset_category ? ("Kategorie: " + h.asset_category) : "")}</div>
          </td>
          <td>
            <div class="mc-row-title">${esc(h.employee_name || "—")}</div>
            <div class="mc-row-sub">${esc(h.handover_id ? ("MA-ID: " + h.handover_id) : "")}</div>
          </td>
          <td>
            <div class="mc-row-title">${esc(Number(h.quantity ?? 0))}</div>
            <div style="margin-top:6px">${st}</div>
            <div class="mc-row-sub">${esc(h.remark ? ("Bem.: " + h.remark) : "")}</div>
          </td>
          <td style="text-align:right">
            <div class="mc-actions">
              <button class="mc-icbtn primary" type="button" data-action="edit" data-entity="handovers" data-id="${esc(h.id)}" title="Bearbeiten" aria-label="Bearbeiten">
                <i class="fa fa-pen"></i>
              </button>
              <button class="mc-icbtn danger" type="button" data-action="delete" data-entity="handovers" data-id="${esc(h.id)}" title="Löschen" aria-label="Löschen">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join("");
  }

  /* ===================== LOAD ===================== */
  async function load(){
    if (state.tab === "assets"){
      DOM.tbAssets.innerHTML = emptyRow(5, "Lädt…");
    } else {
      DOM.tbHandovers.innerHTML = emptyRow(5, "Lädt…");
    }

    try {
      if (state.tab === "assets"){
        const s = state.assets;
        const res = await api("fetch_assets", ENDPOINTS.assets_fetch, {
          page: s.page,
          per_page: s.per_page,
          q: s.q,
          sort: s.sort,
          category: s.category,
          status: s.status,
          purchase_type: s.purchase_type,
        }, "GET");

        s.items = Array.isArray(res?.items) ? res.items : [];
        s.stats = res?.stats || {};
        s.pagination = res?.pagination || {};

        renderStats();
        renderAssets();
        setPagerAssets(s.pagination);
      } else {
        const s = state.handovers;
        const res = await api("fetch_handovers", ENDPOINTS.handovers_fetch, {
          page: s.page,
          per_page: s.per_page,
          q: s.q,
          sort: s.sort,
          status: s.status,
          employee_id: s.employee_id,
        }, "GET");

        s.items = Array.isArray(res?.items) ? res.items : [];
        s.stats = res?.stats || {};
        s.pagination = res?.pagination || {};

        renderStats();
        renderHandovers();
        setPagerHandovers(s.pagination);
      }
    } catch (err){
      renderStats();
      if (state.tab === "assets") DOM.tbAssets.innerHTML = emptyRow(5, "Fehler beim Laden.");
      else DOM.tbHandovers.innerHTML = emptyRow(5, "Fehler beim Laden.");
      toast("bad", "Fehler", "Daten konnten nicht geladen werden.");
    }
  }

  /* ===================== MODAL ===================== */
  function modalError(msg){
    DOM.modalErr.textContent = msg || "";
    DOM.modalErr.classList.toggle("is-on", !!msg);
  }

  function todayISO(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  }

  function initSelect2(){
    if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;

    // toolbar filters
    jQuery("#ac-filter-category,#ac-filter-status,#ac-filter-purchase,#ac-filter-hstatus,#ac-filter-employee").each(function(){
      const $el = jQuery(this);
      if ($el.data("select2")) return;
      $el.select2({ width:"resolve" });
    });

    const $parent = jQuery("#ac-modal .mc-modal-card");

    // handover asset select (AJAX)
    jQuery("#ac-handover-asset").each(function(){
    const $el = jQuery(this);
    const $parent = jQuery("#ac-modal .mc-modal-card");

    if ($el.data("select2")) return;

    $el.select2({
        width: "100%",
        placeholder: "Asset wählen…",
        allowClear: true,
        dropdownParent: $parent,
        ajax: {
        url: ENDPOINTS.assets_available,
        dataType: "json",
        delay: 250,
        data: (params) => ({
            q: params.term || "",
            page: params.page || 1
        }),
        processResults: (data, params) => {
            params.page = params.page || 1;
            return {
            results: data?.results || [],
            pagination: { more: !!data?.pagination?.more }
            };
        }
        },
        templateResult: (d) => s2AssetTpl(d),
        templateSelection: (d) => esc(d?.text || ""),
        escapeMarkup: (m) => m
    });
    });

    // handover employee (optional)
    jQuery("#ac-asset-handover").each(function(){
      const $el = jQuery(this);
      if ($el.data("select2")) return;
      $el.select2({
        width:"100%",
        allowClear:true,
        placeholder:"—",
        dropdownParent: $parent
      });
    });

    // category with tags
    jQuery("#ac-asset-category").each(function(){
      const $el = jQuery(this);
      if ($el.data("select2")) return;
      $el.select2({
        width:"100%",
        tags:true,
        placeholder:"Kategorie wählen…",
        allowClear:true,
        tokenSeparators:[",",";"],
        dropdownParent: $parent
      });
    });

    // handover form employee
    jQuery("#ac-handover-employee").each(function(){
      const $el = jQuery(this);
      if ($el.data("select2")) return;
      $el.select2({
        width:"100%",
        dropdownParent: $parent
      });
    });
  }

  function openModal(entity, mode, row){
    state.modal = { entity, mode, id: row?.id || null };

    modalError("");

    const isAsset = entity === "assets";
    DOM.formAsset.classList.toggle("hidden", !isAsset);
    DOM.formHandover.classList.toggle("hidden", isAsset);

    DOM.del.classList.toggle("hidden", mode !== "edit");

    DOM.modalTitle.textContent = (mode === "edit") ? "Bearbeiten" : "Neu";
    DOM.modalSub.textContent = isAsset ? "Asset" : "Übergabe";

    DOM.formAsset.reset?.();
    DOM.formHandover.reset?.();
    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        jQuery("#ac-handover-asset").val(null).trigger("change");
    }


    if (mode === "edit" && row){
      const form = isAsset ? DOM.formAsset : DOM.formHandover;
      Object.keys(row).forEach(k => {
        const el = form.querySelector(`[name="${CSS.escape(k)}"]`);
        if (!el) return;
        el.value = row[k] ?? "";

        if (entity === "handovers" && mode === "edit" && row && window.jQuery && jQuery.fn && jQuery.fn.select2) {
        const $sel = jQuery("#ac-handover-asset");
        const label = `${row.asset_item || "—"} • ${row.asset_model || "—"} — Kauf: ${row.purchase_date || "—"} — Rest: ${row.remaining ?? ""}`;
        const opt = new Option(label, row.item_id, true, true);
        $sel.append(opt).trigger("change");
        }

      });
    }

    initSelect2();

    if (mode === "create" && entity === "assets") {
      const dS = DOM.formAsset.querySelector('#ac-asset-purchase-date');
      if (dS && !dS.value) dS.value = todayISO();

      const st = DOM.formAsset.querySelector('#ac-asset-status');
      if (st && !st.value) st.value = "Active";
    }

    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
      jQuery("#ac-asset-category,#ac-asset-handover,#ac-handover-employee").trigger("change.select2");
    }

    DOM.modal.classList.add("is-open");
    DOM.modal.setAttribute("aria-hidden","false");
  }

  function closeModal(){
    DOM.modal.classList.remove("is-open");
    DOM.modal.setAttribute("aria-hidden","true");
    modalError("");
  }

  function formToJson(form){
    const fd = new FormData(form);
    const obj = {};
    fd.forEach((v,k) => obj[k] = (typeof v === "string" ? v.trim() : v));
    return obj;
  }

  async function saveModal(){
    const { entity, mode, id } = state.modal;
    const isAsset = entity === "assets";
    const form = isAsset ? DOM.formAsset : DOM.formHandover;

    modalError("");

    DOM.save.disabled = true;
    const prevHtml = DOM.save.innerHTML;
    DOM.save.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Speichere…</span>';

    try {
      const payload = formToJson(form);
      Object.keys(payload).forEach(k => { if (payload[k] === "") payload[k] = null; });

      ["quantity","purchase_price","item_id","handover_id","branch_id"].forEach(k => {
        if (payload[k] !== null && payload[k] !== undefined) {
          const n = Number(payload[k]);
          if (!Number.isNaN(n)) payload[k] = n;
        }
      });

      if (isAsset){
        if (mode === "create"){
          await api("assets_store", ENDPOINTS.assets_store, payload, "POST");
          toast("ok","Gespeichert","Asset wurde erstellt.");
        } else {
          await api("assets_update", ENDPOINTS.assets_update + "/" + id, payload, "PUT");
          toast("ok","Gespeichert","Asset wurde aktualisiert.");
        }
      } else {
        if (mode === "create"){
          await api("hand_store", ENDPOINTS.handovers_store, payload, "POST");
          toast("ok","Gespeichert","Übergabe wurde erstellt.");
        } else {
          await api("hand_update", ENDPOINTS.handovers_update + "/" + id, payload, "PUT");
          toast("ok","Gespeichert","Übergabe wurde aktualisiert.");
        }
      }

      closeModal();
      await load();
    } catch (err){
      if (err?.status === 422){
        modalError(parse422(err.raw || ""));
        toast("bad","422 Validierung","Bitte Eingaben prüfen.");
      } else {
        modalError(String(err?.raw || err?.message || "Fehler").slice(0, 900));
        toast("bad","Fehler","Speichern fehlgeschlagen.");
      }
    } finally {
      DOM.save.disabled = false;
      DOM.save.innerHTML = prevHtml;
    }
  }

  async function deleteModal(){
    const { entity, id } = state.modal;
    if (!id) return;

    DOM.del.disabled = true;
    const prevHtml = DOM.del.innerHTML;
    DOM.del.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Lösche…</span>';

    try {
      if (entity === "assets"){
        await api("assets_delete", ENDPOINTS.assets_delete + "/" + id, {}, "DELETE");
        toast("ok","Gelöscht","Asset wurde gelöscht.");
      } else {
        await api("hand_delete", ENDPOINTS.handovers_delete + "/" + id, {}, "DELETE");
        toast("ok","Gelöscht","Übergabe wurde gelöscht.");
      }
      closeModal();
      await load();
    } catch (err){
      toast("bad","Fehler","Löschen fehlgeschlagen.");
    } finally {
      DOM.del.disabled = false;
      DOM.del.innerHTML = prevHtml;
    }
  }

  /* ===================== EVENTS ===================== */
  DOM.tabsWrap?.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-tab]");
    if (!btn) return;
    setTab(btn.getAttribute("data-tab"));
  });

  DOM.refresh?.addEventListener("click", () => load());

  DOM.newBtn?.addEventListener("click", () => {
    openModal(state.tab, "create", null);
  });

  DOM.search?.addEventListener("input", debounce((e) => {
    const s = state[state.tab];
    s.q = e.target.value || "";
    s.page = 1;
    load();
  }, 300));

  DOM.sort?.addEventListener("change", (e) => {
    const s = state[state.tab];
    s.sort = e.target.value || "newest";
    s.page = 1;
    load();
  });

  DOM.perpage?.addEventListener("change", (e) => {
    const n = parseInt(e.target.value, 10) || 12;
    state.assets.per_page = n;
    state.handovers.per_page = n;
    state.assets.page = 1;
    state.handovers.page = 1;
    load();
  });

  DOM.fCategory?.addEventListener("change", () => { state.assets.category = DOM.fCategory.value || ""; state.assets.page=1; if(state.tab==="assets") load(); });
  DOM.fStatus?.addEventListener("change",   () => { state.assets.status = DOM.fStatus.value || ""; state.assets.page=1; if(state.tab==="assets") load(); });
  DOM.fPurchase?.addEventListener("change", () => { state.assets.purchase_type = DOM.fPurchase.value || ""; state.assets.page=1; if(state.tab==="assets") load(); });

  DOM.fhStatus?.addEventListener("change", () => { state.handovers.status = DOM.fhStatus.value || ""; state.handovers.page=1; if(state.tab==="handovers") load(); });
  DOM.fhEmployee?.addEventListener("change", () => { state.handovers.employee_id = parseInt(DOM.fhEmployee.value,10) || 0; state.handovers.page=1; if(state.tab==="handovers") load(); });

  DOM.prevA?.addEventListener("click", () => { state.assets.page = Math.max(1, state.assets.page - 1); if(state.tab==="assets") load(); });
  DOM.nextA?.addEventListener("click", () => { state.assets.page += 1; if(state.tab==="assets") load(); });

  DOM.prevH?.addEventListener("click", () => { state.handovers.page = Math.max(1, state.handovers.page - 1); if(state.tab==="handovers") load(); });
  DOM.nextH?.addEventListener("click", () => { state.handovers.page += 1; if(state.tab==="handovers") load(); });

  function findRow(entity, id){
    const list = (entity === "assets") ? state.assets.items : state.handovers.items;
    return (list || []).find(x => Number(x.id) === Number(id)) || null;
  }

  DOM.tbAssets?.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-action]");
    if (!btn) return;
    const action = btn.getAttribute("data-action");
    const entity = btn.getAttribute("data-entity");
    const id = Number(btn.getAttribute("data-id"));
    const row = findRow(entity, id);

    if (action === "edit") openModal(entity, "edit", row);
    if (action === "delete"){
      openModal(entity, "edit", row);
      DOM.modalTitle.textContent = "Löschen?";
      DOM.modalSub.textContent = "Bestätigen über „Löschen“.";
    }
  });

  DOM.tbHandovers?.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-action]");
    if (!btn) return;
    const action = btn.getAttribute("data-action");
    const entity = btn.getAttribute("data-entity");
    const id = Number(btn.getAttribute("data-id"));
    const row = findRow(entity, id);

    if (action === "edit") openModal(entity, "edit", row);
    if (action === "delete"){
      openModal(entity, "edit", row);
      DOM.modalTitle.textContent = "Löschen?";
      DOM.modalSub.textContent = "Bestätigen über „Löschen“.";
    }
  });

  DOM.modalClose?.addEventListener("click", closeModal);
  DOM.cancel?.addEventListener("click", closeModal);
  DOM.modal?.addEventListener("click", (e) => { if (e.target === DOM.modal) closeModal(); });

  DOM.save?.addEventListener("click", saveModal);
  DOM.del?.addEventListener("click", deleteModal);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && DOM.modal?.classList.contains("is-open")) closeModal();
  });

  /* ===================== INIT ===================== */
  setSortSelect();
  initSelect2();
  load();
})();
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
        label: 'Vermögensbestand/Übergaben',
        url: "{{ url()->current()}}",
        clickable: false

      }

    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush