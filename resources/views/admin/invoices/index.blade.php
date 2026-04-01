{{-- resources/views/admin/invoices/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Rechnungen & Aufträge')

@section('style')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    .inv-wrap{
      --inv-bg:#f6f8fb;
      --inv-surface:#ffffff;
      --inv-border:#e2e8f0;
      --inv-text:#0f172a;
      --inv-muted:#64748b;

      /* CTA / Button-Farbe */
      --inv-primary:#74b2d4;
      --inv-primary-h:#5fa2c6;

      --inv-success:#16a34a;
      --inv-success-h:#15803d;

      --inv-danger:#dc2626;
      --inv-danger-h:#b91c1c;

      --inv-light:#f8fafc;

      font-family:'Outfit',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      padding:20px;
      background:var(--inv-bg);
      color:var(--inv-text);
    }
    .inv-wrap *{box-sizing:border-box}
    .inv-container{max-width:1400px;margin:0 auto}
    .inv-flex{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .inv-gap-2{gap:.5rem}
    .inv-mb-2{margin-bottom:.5rem}
    .inv-mb-4{margin-bottom:1.5rem}
    .inv-muted{color:var(--inv-muted)}
    .inv-small{font-size:.875rem}
    .inv-fw-700{font-weight:700}
    .inv-fw-600{font-weight:600}
    .inv-right{text-align:right}
    .inv-center{text-align:center}
    .inv-wrap h3{font-size:1.5rem;font-weight:800;margin:0;color:var(--inv-text)}

    .inv-stats{
      display:grid;
      grid-template-columns:repeat(5,minmax(220px,1fr));
      gap:1rem;
      margin-bottom:1.25rem;
    }
    @media(max-width:1200px){.inv-stats{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}}
    .inv-stat{
      background:var(--inv-surface);
      border:1px solid rgba(226,232,240,.9);
      border-radius:16px;
      padding:1.25rem;
      box-shadow:0 10px 30px rgba(2,6,23,.06);
      transition:transform .2s,box-shadow .2s;
    }
    .inv-stat:hover{transform:translateY(-2px);box-shadow:0 14px 35px rgba(2,6,23,.09)}
    .inv-stat-head{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:.75rem}
    .inv-stat-icon{
      width:46px;height:46px;border-radius:12px;
      display:flex;align-items:center;justify-content:center;
      font-size:1.15rem;
      background:rgba(116,178,212,.18);
      color:var(--inv-primary);
    }
    .inv-stat-val{font-size:1.65rem;font-weight:900;line-height:1;color:var(--inv-text);margin-bottom:.25rem}
    .inv-stat-lbl{font-size:.85rem;color:var(--inv-muted);font-weight:700}

    .inv-main{
      background:var(--inv-surface);
      border-radius:20px;
      border:1px solid rgba(226,232,240,.9);
      overflow:hidden;
      box-shadow:0 10px 35px rgba(2,6,23,.06);
    }

    .inv-filter{
      background:var(--inv-light);
      border-bottom:1px solid var(--inv-border);
      padding:1rem 1.25rem;
    }
    .inv-filter-row{
      display:flex;align-items:end;gap:.75rem;flex-wrap:nowrap;
      overflow-x:auto;padding-bottom:.25rem;
    }
    .inv-group{display:flex;flex-direction:column;min-width:0}
    .inv-label{
      font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;
      font-weight:900;color:#94a3b8;margin-bottom:6px;white-space:nowrap;
    }
    .inv-input-wrap{position:relative;display:flex;align-items:center}
    .inv-ico{position:absolute;left:.9rem;color:var(--inv-muted);pointer-events:none}
    .inv-control,.inv-select{
      width:100%;
      border:1px solid var(--inv-border);
      border-radius:10px;
      padding:.6rem .95rem;
      font-size:.95rem;
      font-family:inherit;
      background:#fff;
      outline:none;
      transition:border-color .2s,box-shadow .2s;
      height:42px;
      color:var(--inv-text);
    }
    .inv-has-ico{padding-left:2.4rem}
    .inv-control:focus,.inv-select:focus{
      border-color:var(--inv-primary);
      box-shadow:0 0 0 3px rgba(116,178,212,.22);
    }

    .inv-w-search{min-width:340px;flex:1 1 420px}
    .inv-w-type{min-width:190px}
    .inv-w-status{min-width:170px}
    .inv-w-from,.inv-w-to{min-width:160px}
    .inv-w-per{min-width:140px}
    .inv-w-btn{min-width:150px}

    /* Buttons */
    .inv-btn{
      border:none;border-radius:10px;
      padding:.55rem 1rem;
      font-weight:800;
      cursor:pointer;
      font-family:inherit;
      display:inline-flex;align-items:center;justify-content:center;
      transition:transform .15s, filter .15s, background .15s, color .15s, border-color .15s;
      text-decoration:none;
      height:42px;
      font-size:.95rem;
      white-space:nowrap;
      user-select:none;
    }
    .inv-btn:active{transform:translateY(1px)}
    .inv-btn-sm{height:38px;padding:.45rem .85rem;font-size:.9rem;border-radius:10px}

    .inv-btn-primary{background:var(--inv-primary);color:#fff}
    .inv-btn-primary:hover{background:var(--inv-primary-h)}

    .inv-btn-success{background:var(--inv-success);color:#fff}
    .inv-btn-success:hover{background:var(--inv-success-h)}

    .inv-btn-danger{background:var(--inv-danger);color:#fff}
    .inv-btn-danger:hover{background:var(--inv-danger-h)}

    .inv-btn-light{
      background:#fff;
      border:1px solid var(--inv-border);
      color:var(--inv-text);
    }
    .inv-btn-light:hover{background:#f1f5f9}

    .inv-btn-icon{
      width:36px;height:36px;border-radius:10px;
      display:inline-flex;align-items:center;justify-content:center;
      color:var(--inv-muted);
      background:#fff;
      border:1px solid var(--inv-border);
      cursor:pointer;
      transition:all .15s;
      text-decoration:none;
    }
    .inv-btn-icon:hover{border-color:var(--inv-primary);color:var(--inv-primary);background:rgba(116,178,212,.10)}

    .inv-table-wrap{overflow-x:auto;width:100%}
    .inv-table{width:100%;border-collapse:collapse;min-width:980px}
    .inv-table th{
      background:#fff;color:var(--inv-muted);font-weight:900;font-size:.78rem;
      text-transform:uppercase;letter-spacing:.5px;
      padding:1.1rem 1.25rem;border-bottom:2px solid var(--inv-border);
      text-align:left;white-space:nowrap;
    }
    .inv-table td{
      padding:1.05rem 1.25rem;vertical-align:middle;
      border-bottom:1px solid var(--inv-border);
      background:#fff;color:var(--inv-text);
    }
    .inv-row{cursor:pointer}
    .inv-row:hover td{background:#fbfdff}
    .inv-sort{cursor:pointer;user-select:none}
    .inv-sort i{margin-left:8px;opacity:.55}

    .inv-footer{
      padding:1.1rem 1.25rem;
      border-top:1px solid var(--inv-border);
      display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;
      background:#fff;
    }

    .inv-badge{
      padding:.38em .75em;border-radius:999px;font-size:.75rem;font-weight:900;
      display:inline-flex;align-items:center;gap:7px;
      border:1px solid rgba(226,232,240,.9);
      white-space:nowrap;
    }
    .inv-b-draft{background:rgba(116,178,212,.18);color:#0b4e68}
    .inv-b-sent{background:rgba(2,132,199,.10);color:#075985}
    .inv-b-paid{background:rgba(22,163,74,.12);color:#166534}
    .inv-b-overdue{background:rgba(220,38,38,.10);color:#991b1b}
    .inv-b-cancelled{background:rgba(100,116,139,.12);color:#334155}

    .inv-hide{display:none!important}

    /* Drawer */
    .inv-backdrop{
      position:fixed;inset:0;background:rgba(2,6,23,.35);
      z-index:9998;opacity:0;visibility:hidden;transition:opacity .2s;
    }
    .inv-backdrop.active{opacity:1;visibility:visible}
    .inv-drawer{
      position:fixed;top:0;right:0;width:100%;max-width:720px;height:100%;
      background:#fff;z-index:9999;
      box-shadow:-10px 0 40px rgba(2,6,23,.18);
      transform:translateX(100%);transition:transform .25s ease-in-out;
      display:flex;flex-direction:column;
    }
    .inv-drawer.active{transform:translateX(0)}
    .inv-drawer-head{
      padding:1.25rem 1.5rem;border-bottom:1px solid var(--inv-border);
      display:flex;justify-content:space-between;align-items:center;gap:1rem;
      background:linear-gradient(135deg, rgba(116,178,212,.18), rgba(22,163,74,.08));
    }
    .inv-drawer-body{padding:1.5rem;overflow:auto;flex:1}
    .inv-close{background:none;border:none;font-size:1.25rem;color:var(--inv-muted);cursor:pointer}

    .inv-steps{display:flex;gap:.5rem;margin-bottom:1rem;align-items:center}
    .inv-step-pill{
      font-size:12px;padding:6px 10px;border-radius:999px;
      border:1px solid var(--inv-border);background:#fff;color:var(--inv-muted);
      font-weight:900;letter-spacing:.04em;text-transform:uppercase;
      cursor:pointer;
    }
    .inv-step-pill.active{background:rgba(116,178,212,.18);border-color:rgba(116,178,212,.35);color:#0b4e68}
    .inv-step-pill.meta{margin-left:auto;cursor:default}

    .inv-section{
      border:1px solid var(--inv-border);
      border-radius:14px;padding:12px;background:#fff;margin-bottom:1rem;
    }
    .inv-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.75rem}
    .inv-col-12{grid-column:span 12}
    .inv-col-9{grid-column:span 9}
    .inv-col-6{grid-column:span 6}
    .inv-col-4{grid-column:span 4}
    .inv-col-3{grid-column:span 3}
    @media(max-width:720px){.inv-col-9,.inv-col-6,.inv-col-4,.inv-col-3{grid-column:span 12}}

    /* Select2 */
    .inv-wrap .select2-container{width:100%!important;min-width:0!important}
    .inv-wrap .select2-container--default .select2-selection--single,
    .inv-wrap .select2-container--default .select2-selection--multiple{
      height:42px;border:1px solid var(--inv-border);border-radius:10px;
      display:flex;align-items:center;padding-left:8px;background:#fff;
    }
    .inv-wrap .select2-container--default.select2-container--focus .select2-selection--single,
    .inv-wrap .select2-container--default.select2-container--focus .select2-selection--multiple{
      border-color:var(--inv-primary);
      box-shadow:0 0 0 3px rgba(116,178,212,.22);
    }
    .inv-wrap .select2-container--open{z-index:10050!important}
    .inv-wrap .select2-dropdown{z-index:10050!important}

    /* Items */
    .inv-items-table{width:100%;border-collapse:collapse;min-width:700px}
    .inv-items-table th,.inv-items-table td{padding:.75rem;border-bottom:1px solid var(--inv-border)}
    .inv-items-table th{font-size:.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;font-weight:900}
    .inv-total-bar{display:flex;justify-content:flex-end;gap:1.25rem;margin-top:.75rem;flex-wrap:wrap}
    .inv-total-bar b{font-weight:900}

    /* Pricing mode pills */
    .inv-mode{
      display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;
      background:#f8fafc;border:1px solid var(--inv-border);border-radius:14px;
      padding:.6rem;
    }
    .inv-mode input{display:none}
    .inv-mode label{
      padding:.45rem .75rem;border-radius:999px;
      border:1px solid var(--inv-border);
      background:#fff;
      font-weight:900;
      font-size:.85rem;
      color:var(--inv-muted);
      cursor:pointer;
      user-select:none;
      display:inline-flex;align-items:center;gap:.45rem;
    }
    .inv-mode input:checked + label{
      background:rgba(116,178,212,.18);
      border-color:rgba(116,178,212,.35);
      color:#0b4e68;
    }

    /* Upload / Gallery */
    .inv-drop{
      border:2px dashed rgba(116,178,212,.45);
      background:rgba(116,178,212,.12);
      border-radius:14px;
      padding:1rem;
      text-align:center;
    }
    .inv-file-grid{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:.75rem;
      margin-top:.75rem;
    }
    @media(max-width:520px){.inv-file-grid{grid-template-columns:1fr}}
    .inv-file-card{
      border:1px solid var(--inv-border);
      border-radius:14px;
      padding:.9rem;
      display:flex;
      gap:.75rem;
      align-items:flex-start;
      background:#fff;
      cursor:pointer;
      transition:box-shadow .15s, transform .15s;
    }
    .inv-file-card:hover{box-shadow:0 10px 25px rgba(2,6,23,.08); transform:translateY(-1px)}
    .inv-file-ico{
      width:40px;height:40px;border-radius:12px;
      display:flex;align-items:center;justify-content:center;
      background:rgba(220,38,38,.10);
      color:var(--inv-danger);
      flex:0 0 auto;
    }
    .inv-trunc{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

    /* PDF Modal */
    .inv-modal-backdrop{
      position:fixed; inset:0;
      background:rgba(2,6,23,.55);
      z-index:10060;
      opacity:0; visibility:hidden;
      transition:opacity .18s;
    }
    .inv-modal-backdrop.active{opacity:1;visibility:visible}

    .inv-pdf-modal{
      position:fixed;
      inset: 5vh 4vw;
      background:#fff;
      border-radius:18px;
      box-shadow:0 30px 80px rgba(2,6,23,.35);
      z-index:10061;
      display:flex;
      flex-direction:column;
      transform:scale(.98);
      opacity:0;
      visibility:hidden;
      transition:opacity .18s, transform .18s;
      overflow:hidden;
    }
    .inv-pdf-modal.active{opacity:1;visibility:visible;transform:scale(1)}
    .inv-pdf-head{
      padding:.9rem 1rem;
      border-bottom:1px solid var(--inv-border);
      background:#f8fafc;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1rem;
    }
    .inv-pdf-title{
      font-weight:900;
      min-width:0;
      display:flex;
      gap:.6rem;
      align-items:center;
    }
    .inv-pdf-title span{min-width:0}
    .inv-pdf-body{flex:1;background:#0b1220}
    .inv-pdf-body iframe{width:100%;height:100%;border:0;background:#0b1220}

    /* ✅ CUSTOMER ACCORDION */
    .inv-acc{display:flex;flex-direction:column;gap:.75rem}
    .inv-acc details{
      border:1px solid var(--inv-border);
      border-radius:16px;
      background:#fff;
      box-shadow:0 10px 30px rgba(2,6,23,.05);
      overflow:hidden;
    }
    .inv-acc summary{
      list-style:none;
      cursor:pointer;
      padding:1rem 1.1rem;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1rem;
    }
    .inv-acc summary::-webkit-details-marker{display:none}
    .inv-acc-title{
      min-width:0;
      display:flex;
      align-items:center;
      gap:.7rem;
    }
    .inv-acc-title .inv-chev{
      width:34px;height:34px;border-radius:12px;
      display:flex;align-items:center;justify-content:center;
      border:1px solid var(--inv-border);
      color:var(--inv-muted);
      background:#fff;
    }
    details[open] .inv-acc-title .inv-chev{
      color:var(--inv-primary);
      border-color:rgba(116,178,212,.45);
      background:rgba(116,178,212,.10)
    }
    .inv-acc-name{font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .inv-acc-sub{font-size:.85rem;color:var(--inv-muted);font-weight:700;margin-top:2px}
    .inv-acc-meta{
      display:flex;gap:.4rem;flex-wrap:wrap;justify-content:flex-end;align-items:center
    }
    .inv-pill{
      border:1px solid rgba(226,232,240,.9);
      background:#fff;
      border-radius:999px;
      padding:.35rem .65rem;
      font-size:.75rem;
      font-weight:900;
      display:inline-flex;align-items:center;gap:.4rem;
      white-space:nowrap;
    }
    .inv-pill-ok{background:rgba(22,163,74,.10);color:#166534}
    .inv-pill-warn{background:rgba(220,38,38,.08);color:#991b1b}
    .inv-pill-info{background:rgba(2,132,199,.10);color:#075985}
    .inv-pill-muted{background:rgba(100,116,139,.10);color:#334155}
    .inv-acc-body{border-top:1px solid var(--inv-border);padding:1rem 1.1rem;background:#fbfdff}
    .inv-mini-table{width:100%;border-collapse:collapse;min-width:980px}
    .inv-mini-table th{
      font-size:.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;font-weight:900;
      padding:.75rem .75rem;border-bottom:1px solid var(--inv-border);text-align:left;white-space:nowrap
    }
    .inv-mini-table td{
      padding:.75rem .75rem;border-bottom:1px solid var(--inv-border);background:transparent;vertical-align:middle
    }
    .inv-mini-table tr:hover td{background:#f8fbff}

    /* ✅ compact items mode (Nur Preis) */
    .inv-compact-items .col-qty,
    .inv-compact-items .col-unit,
    .inv-compact-items .col-unitprice,
    .inv-compact-items .col-sum,
    .inv-compact-items .cell-qty,
    .inv-compact-items .cell-unit,
    .inv-compact-items .cell-unitprice,
    .inv-compact-items .cell-sum{
      display:none!important;
    }
    .inv-compact-items .col-price,
    .inv-compact-items .cell-price{
      display:table-cell!important;
    }

    .inv-status-quick{
      min-width: 140px;
      height: 34px;
      border: 1px solid var(--inv-border);
      border-radius: 999px;
      padding: .35rem 2rem .35rem .8rem;
      font-size: .82rem;
      font-weight: 800;
      font-family: inherit;
      background: #fff;
      color: var(--inv-text);
      outline: none;
      cursor: pointer;
      transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .inv-status-quick:focus{
      border-color: var(--inv-primary);
      box-shadow: 0 0 0 3px rgba(116,178,212,.18);
    }
    .inv-status-quick.is-saving{
      opacity: .7;
      pointer-events: none;
    }
    .inv-status-wrap{
      display:inline-flex;
      align-items:center;
      gap:.5rem;
    }
    .inv-status-dot{
      width:10px;
      height:10px;
      border-radius:999px;
      display:inline-block;
    }
    .inv-status-dot.draft{background:#74b2d4;}
    .inv-status-dot.sent{background:#0284c7;}
    .inv-status-dot.paid{background:#16a34a;}
    .inv-status-dot.overdue{background:#dc2626;}
    .inv-status-dot.cancelled{background:#64748b;}
  </style>
@endsection

@section('content')
  @php($types = ['Rechnung','Teilrechnung','Abschlagsrechnung','Schlussrechnung','Anzahlung','Zahlungserinnerung','Mahnung','Gutschrift','Stornorechnung','Proforma','Angebot','Auftrag','Lieferschein','Quittung'])

  <div class="inv-wrap" id="invApp"
       data-list-url="{{ route('admin.invoices.list') }}"
       data-store-url="{{ route('admin.invoices.store') }}"
       data-show-url="{{ route('admin.invoices.show', ['invoice' => '__ID__']) }}"
       data-update-url="{{ route('admin.invoices.update', ['invoice' => '__ID__']) }}"
       data-destroy-url="{{ route('admin.invoices.destroy', ['invoice' => '__ID__']) }}"
       data-upload-url="{{ route('admin.invoices.files.upload', ['invoice' => '__ID__']) }}"
       data-delete-file-url="{{ route('admin.invoices.files.delete', ['file' => '__ID__']) }}"
       data-download-file-url="{{ route('admin.invoices.files.download', ['file' => '__ID__']) }}"
       data-view-file-url="{{ route('admin.invoices.files.view', ['file' => '__ID__']) }}"
       data-sel-customers="{{ route('admin.invoices.select.customers') }}"
       data-sel-objects="{{ route('admin.invoices.select.objects') }}"
       data-status-url="{{ route('admin.invoices.status', ['invoice' => '__ID__']) }}"
       data-sel-products="{{ route('admin.invoices.select.products') }}">

    <div class="inv-container" style="margin-top:78px">
      <div class="inv-flex inv-mb-4">
        <div>
          <h3 class="inv-mb-2">Rechnungen & Aufträge</h3>
          <div class="inv-muted inv-small inv-fw-600">Klick auf eine Zeile öffnet rechts direkt die Galerie.</div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:flex-end">
          <button class="inv-btn inv-btn-light" id="inv-view-table" type="button">
            <i class="fa-solid fa-table"></i>&nbsp; Tabelle
          </button>
          <button class="inv-btn inv-btn-light" id="inv-view-cards" type="button">
            <i class="fa-solid fa-grip"></i>&nbsp; Karten
          </button>
          <button class="inv-btn inv-btn-light" id="inv-view-customers" type="button">
            <i class="fa-solid fa-users"></i>&nbsp; Kunden
          </button>

          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="inv-new" type="button">
            <i class="fa-solid fa-plus"></i>&nbsp; Neue Rechnung
          </button>
        </div>
      </div>

      {{-- ✅ Analytics cards stay, but pagination/meta must NOT show analytics text --}}
      <div class="inv-stats" id="inv-kpis"></div>

      <div class="inv-main">
        <div class="inv-filter">
          <div class="inv-filter-row">
            <div class="inv-group inv-w-search">
              <label class="inv-label">Suche</label>
              <div class="inv-input-wrap">
                <i class="fa-solid fa-magnifying-glass inv-ico"></i>
                <input id="inv-q" class="inv-control inv-has-ico" placeholder="Rechnungsnr, Kunde, Objekt, Typ..." />
              </div>
            </div>

            <div class="inv-group inv-w-type">
              <label class="inv-label">Typ</label>
              <select id="inv-type" class="inv-select">
                <option value="">Alle</option>
                @foreach($types as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
              </select>
            </div>

            <div class="inv-group inv-w-status">
              <label class="inv-label">Status</label>
              <select id="inv-status" class="inv-select">
                <option value="">Alle</option>
                <option value="draft">Entwurf</option>
                <option value="sent">Gesendet</option>
                <option value="paid">Bezahlt</option>
                <option value="overdue">Überfällig</option>
                <option value="cancelled">Storniert</option>
              </select>
            </div>

            <div class="inv-group inv-w-from">
              <label class="inv-label">Von</label>
              <input id="inv-from" type="date" class="inv-control">
            </div>

            <div class="inv-group inv-w-to">
              <label class="inv-label">Bis</label>
              <input id="inv-to" type="date" class="inv-control">
            </div>

            <div class="inv-group inv-w-per">
              <label class="inv-label">Pro Seite</label>
              <select id="inv-per" class="inv-select">
                <option value="12">12</option>
                <option value="24">24</option>
                <option value="36">36</option>
                <option value="50">50</option>
              </select>
            </div>

            <div class="inv-group inv-w-btn">
              <label class="inv-label">&nbsp;</label>
              <button id="inv-reset" class="inv-btn inv-btn-light" type="button">
                <i class="fa-solid fa-rotate-left"></i>&nbsp; Zurücksetzen
              </button>
            </div>

            <div class="inv-group inv-w-btn">
              <label class="inv-label">&nbsp;</label>
              <button id="inv-refresh" class="inv-btn inv-btn-light" type="button">
                <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Aktualisieren
              </button>
            </div>
          </div>
        </div>

        {{-- VIEW 1: TABLE --}}
        <div class="inv-table-wrap inv-hide" id="inv-table-wrap">
          <table class="inv-table">
            <thead>
            <tr>
              <th class="inv-sort" data-sort="issue_date" style="padding-left:1.5rem;">Datum <i class="fa-solid fa-sort"></i></th>
              <th class="inv-sort" data-sort="invoice_no">Rechnungsnr <i class="fa-solid fa-sort"></i></th>
              <th>Kunde</th>
              <th>Objekt</th>
              <th class="inv-sort" data-sort="type">Typ <i class="fa-solid fa-sort"></i></th>
              <th class="inv-sort" data-sort="status">Status <i class="fa-solid fa-sort"></i></th>
              <th class="inv-sort inv-right" data-sort="total_amount">Gesamt <i class="fa-solid fa-sort"></i></th>
              <th class="inv-right" style="padding-right:1.5rem;">Aktion</th>
            </tr>
            </thead>
            <tbody id="inv-tbody">
              <tr>
                <td colspan="8" class="inv-center" style="padding:2rem;color:var(--inv-muted);">Lade Daten...</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- VIEW 2: CARDS --}}
        <div class="inv-table-wrap inv-hide" id="inv-card-wrap" style="padding:1.25rem;">
          <div id="inv-cards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;"></div>
        </div>

        {{-- VIEW 3: CUSTOMER COLLAPSE --}}
        <div class="inv-table-wrap" id="inv-customer-wrap" style="padding:1.25rem;">
          <div id="inv-customers"></div>
        </div>

        <div class="inv-footer">
          {{-- ✅ pagination meta: ONLY numbers, no analytics --}}
          <div class="inv-muted inv-small inv-fw-600" id="inv-meta">Lade Daten...</div>
          <div class="inv-flex inv-gap-2" style="justify-content:flex-end;">
            <button class="inv-btn inv-btn-light inv-btn-sm" id="inv-prev" type="button">Zurück</button>
            <button class="inv-btn inv-btn-light inv-btn-sm" id="inv-next" type="button">Weiter</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Drawer --}}
  <div class="inv-backdrop" id="invBackdrop"></div>
  <div class="inv-drawer" id="invDrawer">
    <div class="inv-drawer-head">
      <div>
        <div class="inv-fw-700" style="font-size:1.15rem;" id="inv-drawer-title">Neue Rechnung</div>
        <div class="inv-muted inv-small" id="inv-drawer-sub">Schritt 1: Daten</div>
      </div>
      <button class="inv-close" id="invDrawerClose" type="button"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="inv-drawer-body">
      <div class="inv-steps">
        <button class="inv-step-pill active" id="invStep1Pill" type="button">1) Daten</button>
        <button class="inv-step-pill" id="invStep2Pill" type="button">2) Dateien</button>
        <span class="inv-step-pill meta" id="invActiveId">—</span>
      </div>

      <div id="invStep1">
        <div class="inv-section">
          <div class="inv-grid">
            <div class="inv-col-6">
              <label class="inv-label">Kunde</label>
              <select id="mCustomer" class="inv-select"></select>
            </div>
            <div class="inv-col-6">
              <label class="inv-label">Objekt</label>
              <select id="mObject" class="inv-select"></select>
            </div>

            <div class="inv-col-4">
              <label class="inv-label">Rechnungsnr</label>
              <input id="mInvoiceNo" class="inv-control" placeholder="Automatisch, wenn leer">
            </div>
            <div class="inv-col-4">
              <label class="inv-label">Typ</label>
              <select id="mType" class="inv-select">
                @foreach($types as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
              </select>
            </div>
            <div class="inv-col-4">
              <label class="inv-label">Status</label>
              <select id="mStatus" class="inv-select">
                <option value="draft">Entwurf</option>
                <option value="sent">Gesendet</option>
                <option value="paid">Bezahlt</option>
                <option value="overdue">Überfällig</option>
                <option value="cancelled">Storniert</option>
              </select>
            </div>

            <div class="inv-col-3">
              <label class="inv-label">Ausgestellt am</label>
              <input id="mIssueDate" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Fällig am</label>
              <input id="mDueDate" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Leistung von</label>
              <input id="mServiceFrom" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Leistung bis</label>
              <input id="mServiceTo" type="date" class="inv-control">
            </div>

            <div class="inv-col-3">
              <label class="inv-label">MwSt (%)</label>
              <input id="mTaxRate" type="number" step="0.001" min="0" max="100" class="inv-control" value="0">
            </div>
            <div class="inv-col-9">
              <label class="inv-label">Notiz</label>
              <input id="mNotes" class="inv-control" placeholder="Optional...">
            </div>
          </div>
        </div>

        {{-- Positionen --}}
        <div class="inv-section">
          <div class="inv-flex inv-gap-2" style="justify-content:space-between; align-items:flex-start;">
            <div>
              <div class="inv-fw-700"><i class="fa-solid fa-list"></i>&nbsp; Positionen</div>
              <div class="inv-muted inv-small inv-fw-600" style="margin-top:4px;">
                Modus wählen: Einzelpreise oder nur Gesamtbetrag.
              </div>
            </div>

            <div class="inv-flex inv-gap-2" style="justify-content:flex-end; margin-top:.6rem;">
              <label class="inv-pill inv-pill-muted" style="cursor:pointer;">
                <input type="checkbox" id="toggleCompactColumns" style="margin-right:.45rem;">
                Nur Preis (ohne Menge/Einheit/Summe)
              </label>
            </div>

            <div style="min-width: 320px;">
              <div class="inv-mode" id="priceMode">
                <input type="radio" name="price_mode" id="pm_items" value="items" checked>
                <label for="pm_items"><i class="fa-solid fa-receipt"></i> Einzelpreise</label>

                <input type="radio" name="price_mode" id="pm_total" value="total">
                <label for="pm_total"><i class="fa-solid fa-euro-sign"></i> Nur Gesamt</label>
              </div>
            </div>
          </div>

          {{-- Gesamtbetrag-Block --}}
          <div id="totalOnlyBlock" class="inv-hide" style="margin-top:.9rem;">
            <div class="inv-grid">
              <div class="inv-col-9">
                <label class="inv-label">Titel (optional)</label>
                <input id="mTotalTitle" class="inv-control" placeholder="z.B. Pauschale / Gesamtbetrag">
              </div>
              <div class="inv-col-3">
                <label class="inv-label">Gesamt (Netto)</label>
                <input id="mTotalNet" type="number" step="0.01" min="0" class="inv-control" placeholder="0.00">
              </div>
            </div>

            <div class="inv-muted inv-small inv-fw-600" style="margin-top:.6rem;">
              Hinweis: Es wird automatisch eine Position mit Menge=1 und Einzelpreis=Gesamt (Netto) gespeichert.
            </div>
          </div>

          {{-- Positions-Block --}}
          <div id="itemsBlock" style="margin-top:.9rem;">
            <div class="inv-flex inv-gap-2" style="justify-content:flex-end;">
              <select id="mProductPicker" class="inv-select" style="min-width:320px;"></select>
              <button class="inv-btn inv-btn-light" id="btnAddManualItem" type="button">
                <i class="fa-solid fa-plus"></i>&nbsp; Position
              </button>
            </div>

            <div class="inv-table-wrap" style="margin-top:.75rem;">
              <table class="inv-items-table">
                <thead>
                <tr>
                  <th style="min-width:220px;">Titel</th>

                  <th class="col-qty" style="width:110px;">Menge</th>
                  <th class="col-unit" style="width:120px;">Einheit</th>

                  <th class="col-unitprice inv-right" style="width:150px;">Einzelpreis</th>
                  <th class="col-price inv-right" style="width:150px; display:none;">Preis</th>

                  <th class="col-sum inv-right" style="width:140px;">Summe</th>
                  <th class="inv-right" style="width:140px;"></th>
                </tr>
                </thead>
                <tbody id="itemsBody">
                  <tr><td colspan="7" class="inv-center" style="padding:1.5rem;color:var(--inv-muted);">Keine Positionen</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="inv-total-bar">
            <div class="inv-muted inv-small inv-fw-600">Zwischensumme: <b><span id="sumSubtotal">0.00</span> €</b></div>
            <div class="inv-muted inv-small inv-fw-600">MwSt: <b><span id="sumTax">0.00</span> €</b></div>
            <div class="inv-fw-700">Gesamt: <b><span id="sumTotal">0.00</span> €</b></div>
          </div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:space-between;margin-top:1rem;">
          <div class="inv-muted inv-small inv-fw-600" id="saveHint"></div>
          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnSaveStep1" type="button">
            <i class="fa-solid fa-floppy-disk"></i>&nbsp; Speichern & Weiter
          </button>
        </div>
      </div>

      {{-- Schritt 2 --}}
      <div id="invStep2" class="inv-hide">
        <div class="inv-drop" id="invDrop">
          <div class="inv-fw-700" style="font-size:1.05rem;">PDF-Upload</div>
          <div class="inv-muted inv-small inv-fw-600" style="margin-top:6px;">Mehrere PDFs ablegen oder klicken.</div>

          <input type="file" id="mFiles" accept="application/pdf" multiple style="display:none;">

          <div class="inv-flex inv-gap-2" style="justify-content:center;margin-top:10px;">
            <button class="inv-btn inv-btn-light" id="btnPickFiles" type="button">
              <i class="fa-solid fa-paperclip"></i>&nbsp; Dateien wählen
            </button>
            <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnUploadFiles" type="button">
              <i class="fa-solid fa-cloud-arrow-up"></i>&nbsp; Hochladen
            </button>
          </div>

          <div id="pickedFilesHint" class="inv-muted inv-small inv-fw-600" style="margin-top:10px;"></div>
        </div>

        <div class="inv-section" style="margin-top:1rem;">
          <div class="inv-flex" style="justify-content:space-between;">
            <div class="inv-fw-700"><i class="fa-solid fa-folder-open"></i>&nbsp; Galerie</div>
            <button class="inv-btn inv-btn-light inv-btn-sm" id="btnReloadFiles" type="button">
              <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Neu laden
            </button>
          </div>

          <div id="filesList" class="inv-file-grid"></div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:space-between;margin-top:1rem;">
          <button class="inv-btn inv-btn-light" id="btnBackToStep1" type="button">
            <i class="fa-solid fa-arrow-left"></i>&nbsp; Zurück
          </button>
          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnFinish" type="button">
            <i class="fa-solid fa-check"></i>&nbsp; Fertig
          </button>
        </div>
      </div>

    </div>
  </div>

  {{-- PDF Viewer Modal --}}
  <div class="inv-modal-backdrop" id="pdfBackdrop"></div>
  <div class="inv-pdf-modal" id="pdfModal">
    <div class="inv-pdf-head">
      <div class="inv-pdf-title">
        <i class="fa-solid fa-file-pdf" style="color: var(--inv-danger);"></i>
        <span class="inv-trunc" id="pdfTitle">PDF</span>
      </div>
      <div class="inv-flex inv-gap-2" style="justify-content:flex-end; flex-wrap:nowrap;">
        <a class="inv-btn inv-btn-light inv-btn-sm" id="pdfOpenNew" href="#" target="_blank" rel="noopener">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>&nbsp; Neuer Tab
        </a>
        <a class="inv-btn inv-btn-light inv-btn-sm" id="pdfDownload" href="#">
          <i class="fa-solid fa-download"></i>&nbsp; Herunterladen
        </a>
        <button class="inv-btn inv-btn-light inv-btn-sm" id="pdfClose" type="button">
          <i class="fa-solid fa-xmark"></i>&nbsp; Schließen
        </button>
      </div>
    </div>
    <div class="inv-pdf-body">
      <iframe id="pdfFrame" src="about:blank"></iframe>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const app = document.getElementById('invApp');
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

     const API = {
        list: app.dataset.listUrl,
        store: app.dataset.storeUrl,
        show: app.dataset.showUrl,
        update: app.dataset.updateUrl,
        status: app.dataset.statusUrl,
        destroy: app.dataset.destroyUrl,
        upload: app.dataset.uploadUrl,
        deleteFile: app.dataset.deleteFileUrl,
        downloadFile: app.dataset.downloadFileUrl,
        viewFile: app.dataset.viewFileUrl,
        selCustomers: app.dataset.selCustomers,
        selObjects: app.dataset.selObjects,
        selProducts: app.dataset.selProducts,
      };

      const els = {
        kpis: document.getElementById('inv-kpis'),

        q: document.getElementById('inv-q'),
        type: document.getElementById('inv-type'),
        status: document.getElementById('inv-status'),
        from: document.getElementById('inv-from'),
        to: document.getElementById('inv-to'),
        per: document.getElementById('inv-per'),
        reset: document.getElementById('inv-reset'),
        refresh: document.getElementById('inv-refresh'),

        viewTable: document.getElementById('inv-view-table'),
        viewCards: document.getElementById('inv-view-cards'),
        viewCustomers: document.getElementById('inv-view-customers'),

        tbody: document.getElementById('inv-tbody'),
        meta: document.getElementById('inv-meta'),
        prev: document.getElementById('inv-prev'),
        next: document.getElementById('inv-next'),

        tableWrap: document.getElementById('inv-table-wrap'),
        cardWrap: document.getElementById('inv-card-wrap'),
        cards: document.getElementById('inv-cards'),

        customerWrap: document.getElementById('inv-customer-wrap'),
        customers: document.getElementById('inv-customers'),

        backdrop: document.getElementById('invBackdrop'),
        drawer: document.getElementById('invDrawer'),
        closeDrawer: document.getElementById('invDrawerClose'),
        newBtn: document.getElementById('inv-new'),

        drawerTitle: document.getElementById('inv-drawer-title'),
        drawerSub: document.getElementById('inv-drawer-sub'),
        step1Pill: document.getElementById('invStep1Pill'),
        step2Pill: document.getElementById('invStep2Pill'),
        activeId: document.getElementById('invActiveId'),

        step1: document.getElementById('invStep1'),
        step2: document.getElementById('invStep2'),

        mCustomer: $('#mCustomer'),
        mObject: $('#mObject'),
        mInvoiceNo: document.getElementById('mInvoiceNo'),
        mType: document.getElementById('mType'),
        mStatus: document.getElementById('mStatus'),
        mIssueDate: document.getElementById('mIssueDate'),
        mDueDate: document.getElementById('mDueDate'),
        mServiceFrom: document.getElementById('mServiceFrom'),
        mServiceTo: document.getElementById('mServiceTo'),
        mTaxRate: document.getElementById('mTaxRate'),
        mNotes: document.getElementById('mNotes'),

        pmItems: document.getElementById('pm_items'),
        pmTotal: document.getElementById('pm_total'),
        itemsBlock: document.getElementById('itemsBlock'),
        totalOnlyBlock: document.getElementById('totalOnlyBlock'),
        mTotalTitle: document.getElementById('mTotalTitle'),
        mTotalNet: document.getElementById('mTotalNet'),

        toggleCompact: document.getElementById('toggleCompactColumns'),

        mProductPicker: $('#mProductPicker'),
        itemsBody: document.getElementById('itemsBody'),
        sumSubtotal: document.getElementById('sumSubtotal'),
        sumTax: document.getElementById('sumTax'),
        sumTotal: document.getElementById('sumTotal'),
        saveHint: document.getElementById('saveHint'),

        drop: document.getElementById('invDrop'),
        mFiles: document.getElementById('mFiles'),
        filesList: document.getElementById('filesList'),
        pickFiles: document.getElementById('btnPickFiles'),
        uploadFiles: document.getElementById('btnUploadFiles'),
        pickedHint: document.getElementById('pickedFilesHint'),
        reloadFiles: document.getElementById('btnReloadFiles'),

        addManualItem: document.getElementById('btnAddManualItem'),
        saveStep1: document.getElementById('btnSaveStep1'),
        backTo1: document.getElementById('btnBackToStep1'),
        finish: document.getElementById('btnFinish'),

        pdfBackdrop: document.getElementById('pdfBackdrop'),
        pdfModal: document.getElementById('pdfModal'),
        pdfTitle: document.getElementById('pdfTitle'),
        pdfFrame: document.getElementById('pdfFrame'),
        pdfClose: document.getElementById('pdfClose'),
        pdfOpenNew: document.getElementById('pdfOpenNew'),
        pdfDownload: document.getElementById('pdfDownload'),
      };

      const state = {
        view: 'customer',
        page: 1,
        perPage: 12,
        total: 0,
        hasMore: false,
        loading: false,
        timer: null,

        sortBy: 'issue_date',
        sortDir: 'desc',

        editingId: null,
        activeInvoiceId: null,
        items: [],
        uploadedFiles: [],
        pickedFiles: [],

        priceMode: 'items',

        // ✅ NEW: compact columns mode
        compactColumns: false,
      };

      function escapeHtml(s) {
        return String(s ?? '')
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'","&#039;");
      }

      function money(n){
        const x = Number(n || 0);
        return x.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      function fmtDate(val){
        if (!val) return '—';
        const s = String(val);
        const d = s.slice(0, 10);
        if (!/^\d{4}-\d{2}-\d{2}$/.test(d)) return d || '—';
        const [y,m,dd] = d.split('-');
        return `${dd}.${m}.${y}`;
      }
      function dateOnly(val){
        if (!val) return '';
        return String(val).slice(0,10);
      }

      function statusLabel(status){
        const s = String(status || 'draft').toLowerCase();
        return (s === 'paid') ? 'Bezahlt' :
          (s === 'overdue') ? 'Überfällig' :
          (s === 'sent') ? 'Gesendet' :
          (s === 'cancelled') ? 'Storniert' : 'Entwurf';
      }

      function statusBadge(status){
        const s = String(status || 'draft').toLowerCase();
        const map = {
          draft: 'inv-b-draft',
          sent: 'inv-b-sent',
          paid: 'inv-b-paid',
          overdue: 'inv-b-overdue',
          cancelled: 'inv-b-cancelled',
        };
        const cls = map[s] || 'inv-b-draft';
        const icon =
          (s === 'paid') ? 'fa-circle-check' :
          (s === 'overdue') ? 'fa-triangle-exclamation' :
          (s === 'sent') ? 'fa-paper-plane' :
          (s === 'cancelled') ? 'fa-ban' : 'fa-pen';

        return `<span class="inv-badge ${cls}"><i class="fa-solid ${icon}"></i>${escapeHtml(statusLabel(s))}</span>`;
      }

      function statusOptions(selected){
        const current = String(selected || 'draft').toLowerCase();

        const options = [
          { value: 'draft', label: 'Entwurf' },
          { value: 'sent', label: 'Gesendet' },
          { value: 'paid', label: 'Bezahlt' },
          { value: 'overdue', label: 'Überfällig' },
          { value: 'cancelled', label: 'Storniert' },
        ];

        return `
          <span class="inv-status-wrap">
            <span class="inv-status-dot ${escapeHtml(current)}"></span>
            <select class="inv-status-quick inv-quick-status" data-current="${escapeHtml(current)}">
              ${options.map(opt => `
                <option value="${opt.value}" ${opt.value === current ? 'selected' : ''}>
                  ${escapeHtml(opt.label)}
                </option>
              `).join('')}
            </select>
          </span>
        `;
      }


      function customerLabel(c){
        if (!c) return '—';
        const firma = (c.firma || '').trim();
        const ln = (c.lastname || '').trim();
        const n = (c.name || '').trim();
        return (firma + ' ' + ln + ' ' + n).trim() || ('Lead #' + c.id);
      }

      function objectLabel(o){
        if (!o) return '—';
        const name = (o.object_name || '').trim();
        const addr = (o.full_address || `${o.street||''} ${o.postcode||''} ${o.city||''}`.trim()).trim();
        return `${name ? name + ' — ' : ''}${addr}`.trim() || ('Objekt #' + o.id);
      }

      function kpiCard(label, value, sub, icon){
        return `
          <div class="inv-stat">
            <div class="inv-stat-head">
              <div>
                <div class="inv-stat-val">${escapeHtml(value)}</div>
                <div class="inv-stat-lbl">${escapeHtml(label)}</div>
              </div>
              <div class="inv-stat-icon"><i class="fa-solid ${icon}"></i></div>
            </div>
            <div class="inv-small inv-muted inv-fw-600">${escapeHtml(sub || '')}</div>
          </div>
        `;
      }

      function renderKpis(a){
        const countAll = Number(a?.count_all || 0);
        const sumTotal = Number(a?.sum_total || 0);
        const paidSum = Number(a?.paid_sum || 0);
        const unpaidCount = Number(a?.unpaid_count || 0);
        const draftCount = Number(a?.draft_count || 0);

        els.kpis.innerHTML = [
          kpiCard('Treffer gesamt', String(countAll), 'Aktueller Filter', 'fa-file-invoice'),
          kpiCard('Summe gesamt', money(sumTotal) + ' €', 'Summe aller Beträge', 'fa-euro-sign'),
          kpiCard('Bezahlt', money(paidSum) + ' €', 'Status: Bezahlt', 'fa-circle-check'),
          kpiCard('Offen', String(unpaidCount), 'Gesendet + Überfällig', 'fa-triangle-exclamation'),
          kpiCard('Entwürfe', String(draftCount), 'Status: Entwurf', 'fa-pen'),
        ].join('');
      }

      async function apiJson(url, opts = {}){
        const res = await fetch(url, {
          ...opts,
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            ...(opts.headers || {})
          }
        });

        const ct = (res.headers.get('content-type') || '').toLowerCase();
        let j = {};
        if (ct.includes('application/json')) j = await res.json().catch(() => ({}));

        if (!res.ok || j?.ok === false) {
          const msg =
            j?.message ||
            (j?.errors ? Object.values(j.errors).flat().join(' ') : '') ||
            `Anfrage fehlgeschlagen (${res.status})`;
          throw new Error(msg);
        }
        return j;
      }

      function buildListUrl(){
        const u = new URL(API.list, window.location.origin);
        const params = {
          page: state.page,
          per_page: state.perPage,
          search: (els.q.value || '').trim(),
          type: els.type.value || '',
          status: els.status.value || '',
          from: els.from.value || '',
          to: els.to.value || '',
          sort_by: state.sortBy,
          sort_dir: state.sortDir,
        };
        Object.entries(params).forEach(([k,v]) => { if (v !== '' && v != null) u.searchParams.set(k, v); });
        return u.toString();
      }

      function renderEmpty(msg){
        els.tbody.innerHTML = `<tr><td colspan="8" class="inv-center" style="padding:2rem;color:var(--inv-muted);">${escapeHtml(msg)}</td></tr>`;
        els.cards.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">${escapeHtml(msg)}</div>`;
        els.customers.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">${escapeHtml(msg)}</div>`;
      }

      function isSchluss(inv){ return String(inv?.type || '').toLowerCase() === 'schlussrechnung'; }
      function hasPdf(inv){
        if (typeof inv?.files_count !== 'undefined') return Number(inv.files_count || 0) > 0;
        if (Array.isArray(inv?.files)) return inv.files.length > 0;
        return false;
      }
      function getPaidAmount(inv){
        const total = Number(inv?.total_amount || 0);
        const s = String(inv?.status || '').toLowerCase();
        return (s === 'paid') ? total : Number(inv?.paid_amount || 0);
      }
      function getUnpaidAmount(inv){
        const total = Number(inv?.total_amount || 0);
        const paid = getPaidAmount(inv);
        return Math.max(0, Math.round((total - paid) * 100) / 100);
      }


      function normType(t){
        return String(t || '').trim().toLowerCase();
      }

      function isCreditType(inv){
        const t = normType(inv?.type);
        // treat as negative documents
        return t === 'stornorechnung' || t === 'gutschrift';
      }

      function isFinancialType(inv){
        // exclude non-invoice docs if you want (optional; keep if you want them counted)
        const t = normType(inv?.type);
        const nonFinancial = new Set(['angebot','auftrag','lieferschein','quittung','proforma']); // adjust if needed
        return !nonFinancial.has(t);
      }

      function signedTotal(inv){
        const total = Number(inv?.total_amount || 0);
        const sign  = isCreditType(inv) ? -1 : 1;
        return sign * total;
      }

      function signedPaid(inv){
        const total = Number(inv?.total_amount || 0);
        const s = String(inv?.status || '').toLowerCase();

        // your current logic: if status paid => full amount, else paid_amount (if exists)
        const paidRaw = (s === 'paid') ? total : Number(inv?.paid_amount || 0);

        const sign = isCreditType(inv) ? -1 : 1;
        return sign * paidRaw;
      }

      function signedOpen(inv){
        // open = total - paid, both signed consistently
        const tot = signedTotal(inv);
        const paid = signedPaid(inv);
        const open = tot - paid;

        // avoid -0.00
        return Math.abs(open) < 0.00001 ? 0 : open;
      }

      function moneySigned(n){
        const x = Number(n || 0);
        const abs = money(Math.abs(x));
        return (x < 0) ? `- ${abs}` : abs;
      }

      function pillToneForOpen(open){
        // if open > 0 => customer owes money (warn)
        // if open < 0 => you owe money / credit (info)
        // if 0 => muted
        if (open > 0.00001) return { cls:'inv-pill-warn', icon:'fa-triangle-exclamation', label:'Offen' };
        if (open < -0.00001) return { cls:'inv-pill-info', icon:'fa-circle-info', label:'Guthaben' };
        return { cls:'inv-pill-muted', icon:'fa-circle-minus', label:'Offen' };
      }



      function renderTable(rows){
        const data = Array.isArray(rows) ? rows : [];
        if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

        els.tbody.innerHTML = data.map(inv => {
          const id = inv.id;
          const date = fmtDate(inv.issue_date);
          const no = inv.invoice_no || '—';
          const type = inv.type || '—';
          const total = money(inv.total_amount || 0);
          const c = customerLabel(inv.customer);
          const o = objectLabel(inv.object);

          return `
            <tr class="inv-row" data-id="${id}">
              <td style="padding-left:1.5rem;">${escapeHtml(date)}</td>
              <td class="inv-fw-700">${escapeHtml(no)}</td>
              <td class="inv-fw-600">${escapeHtml(c)}</td>
              <td class="inv-muted inv-fw-600">${escapeHtml(o)}</td>
              <td>${escapeHtml(type)}</td>
              <td>
                <div data-id="${id}">
                  ${statusOptions(inv.status)}
                </div>
              </td>
              <td class="inv-right inv-fw-700">${escapeHtml(total)} €</td>
              <td class="inv-right" style="padding-right:1.5rem;">
                <a href="javascript:void(0)" class="inv-btn-icon inv-open-files" data-id="${id}" title="Dateien öffnen"><i class="fa-solid fa-folder-open"></i></a>
                <a href="javascript:void(0)" class="inv-btn-icon inv-edit" data-id="${id}" title="Bearbeiten" style="margin-left:6px;"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="javascript:void(0)" class="inv-btn-icon inv-del" data-id="${id}" title="Löschen" style="margin-left:6px;border-color:rgba(220,38,38,.25);color:var(--inv-danger);"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>
          `;
        }).join('');
      }

      function renderCards(rows){
        const data = Array.isArray(rows) ? rows : [];
        if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

        els.cards.innerHTML = data.map(inv => {
          const id = inv.id;
          const date = fmtDate(inv.issue_date);
          const no = inv.invoice_no || '—';
          const type = inv.type || '—';
          const total = money(inv.total_amount || 0);
          const c = customerLabel(inv.customer);
          const o = objectLabel(inv.object);

          return `
            <div class="inv-stat inv-row" data-id="${id}" style="border-radius:18px;">
              <div class="inv-flex" style="align-items:flex-start;">
                <div style="min-width:0;">
                  <div class="inv-muted inv-small inv-fw-700" style="text-transform:uppercase;letter-spacing:.05em;">${escapeHtml(type)}</div>
                  <div class="inv-fw-700" style="font-size:1.2rem;margin-top:4px;">${escapeHtml(no)}</div>
                  <div class="inv-small inv-fw-600" style="margin-top:6px;">${escapeHtml(c)}</div>
                  <div class="inv-small inv-muted inv-fw-600 inv-trunc" style="margin-top:4px;">${escapeHtml(o)}</div>
                </div>
                <div class="inv-right">
                  <div data-id="${id}">
                    ${statusOptions(inv.status)}
                  </div>
                  <div class="inv-small inv-muted inv-fw-600" style="margin-top:8px;">${escapeHtml(date)}</div>
                </div>
              </div>

              <div class="inv-flex" style="margin-top:14px;">
                <div class="inv-muted inv-small inv-fw-600">Gesamt</div>
                <div class="inv-fw-700" style="font-size:1.35rem;">${escapeHtml(total)} €</div>
              </div>

              <div class="inv-flex inv-gap-2" style="justify-content:flex-end;margin-top:14px;">
                <button class="inv-btn inv-btn-light inv-open-files" data-id="${id}" type="button"><i class="fa-solid fa-folder-open"></i>&nbsp; Dateien</button>
                <button class="inv-btn inv-btn-light inv-edit" data-id="${id}" type="button"><i class="fa-solid fa-pen"></i>&nbsp; Bearbeiten</button>
                <button class="inv-btn inv-btn-danger inv-del" data-id="${id}" type="button"><i class="fa-solid fa-trash"></i>&nbsp; Löschen</button>
              </div>
            </div>
          `;
        }).join('');
      }

     function renderCustomers(rows){
        const data = Array.isArray(rows) ? rows : [];
        if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

        // Group by customer
        const map = new Map();
        for (const inv of data){
          const cid = inv.customer_id || inv.customer?.id || '0';
          if (!map.has(cid)) map.set(cid, { customer: inv.customer, invoices: [] });
          map.get(cid).invoices.push(inv);
        }

        const customers = Array.from(map.entries())
          .map(([cid, g]) => ({
            cid,
            label: customerLabel(g.customer),
            customer: g.customer,
            invoices: g.invoices || []
          }))
          .sort((a,b) => a.label.localeCompare(b.label, 'de'));

        els.customers.innerHTML = `
          <div class="inv-acc">
            ${customers.map((cg, idx) => {
              const invs = cg.invoices.slice().sort((a,b) => {
                const as = isSchluss(a) ? 1 : 0;
                const bs = isSchluss(b) ? 1 : 0;
                if (as !== bs) return as - bs;
                return String(b.issue_date || '').localeCompare(String(a.issue_date || ''));
              });

              // Aggregates (SIGNED)
              let sumTotal = 0, sumPaid = 0, sumOpen = 0;

              // Details
              let pdfCount = 0;

              let schlussCount = 0, schlussTotal = 0, schlussOpen = 0;
              let stornoCount  = 0, stornoTotal  = 0, stornoOpen  = 0;

              invs.forEach(inv => {
                if (hasPdf(inv)) pdfCount++;

                // exclude non-financial docs from sums (offers etc.)
                if (!isFinancialType(inv)) return;

                const tot  = signedTotal(inv);
                const paid = signedPaid(inv);
                const open = signedOpen(inv);

                sumTotal += tot;
                sumPaid  += paid;
                sumOpen  += open;

                if (isSchluss(inv)){
                  schlussCount++;
                  schlussTotal += tot;
                  schlussOpen  += open;
                }

                if (isCreditType(inv)){
                  stornoCount++;
                  stornoTotal += tot; // negative
                  stornoOpen  += open; // negative if unpaid credit note
                }
              });

              const round2 = (n) => Math.round((Number(n || 0)) * 100) / 100;

              sumTotal = round2(sumTotal);
              sumPaid  = round2(sumPaid);
              sumOpen  = round2(sumOpen);

              schlussTotal = round2(schlussTotal);
              schlussOpen  = round2(schlussOpen);

              stornoTotal = round2(stornoTotal);
              stornoOpen  = round2(stornoOpen);

              const openTone = pillToneForOpen(sumOpen);
              const openAttr = idx === 0 ? 'open' : '';

              return `
                <details ${openAttr}>
                  <summary>
                    <div class="inv-acc-title" style="min-width:0;">
                      <div class="inv-chev"><i class="fa-solid fa-chevron-down"></i></div>
                      <div style="min-width:0;">
                        <div class="inv-acc-name">${escapeHtml(cg.label)}</div>
                        <div class="inv-acc-sub">${invs.length} Rechnung(en)</div>
                      </div>
                    </div>

                    <div class="inv-acc-meta">
                      <span class="inv-pill inv-pill-info">
                        <i class="fa-solid fa-euro-sign"></i>
                        Gesamt (Netto): ${escapeHtml(moneySigned(sumTotal))} €
                      </span>

                      <span class="inv-pill inv-pill-ok">
                        <i class="fa-solid fa-circle-check"></i>
                        Bezahlt: ${escapeHtml(moneySigned(sumPaid))} €
                      </span>

                      <span class="inv-pill ${openTone.cls}">
                        <i class="fa-solid ${openTone.icon}"></i>
                        ${escapeHtml(openTone.label)}: ${escapeHtml(moneySigned(sumOpen))} €
                      </span>

                      ${schlussCount ? `
                        <span class="inv-pill inv-pill-muted">
                          <i class="fa-solid fa-flag-checkered"></i>
                          Schluss: ${escapeHtml(moneySigned(schlussTotal))} € · Offen: ${escapeHtml(moneySigned(schlussOpen))} €
                        </span>
                      ` : `
                        <span class="inv-pill inv-pill-warn">
                          <i class="fa-solid fa-flag"></i>
                          Schlussrechnung: Nein
                        </span>
                      `}

                      ${stornoCount ? `
                        <span class="inv-pill inv-pill-info">
                          <i class="fa-solid fa-rotate-left"></i>
                          Storno/Gutschrift: ${escapeHtml(moneySigned(stornoTotal))} € · Offen: ${escapeHtml(moneySigned(stornoOpen))} €
                        </span>
                      ` : ``}

                      <span class="inv-pill ${pdfCount>0?'inv-pill-ok':'inv-pill-warn'}">
                        <i class="fa-solid fa-file-pdf"></i>
                        PDF: ${pdfCount}/${invs.length}
                      </span>
                    </div>
                  </summary>

                  <div class="inv-acc-body">
                    <div class="inv-table-wrap">
                      <table class="inv-mini-table">
                        <thead>
                          <tr>
                            <th>Datum</th>
                            <th>Rechnungsnr</th>
                            <th>Typ</th>
                            <th>Status</th>
                            <th class="inv-right">Gesamt</th>
                            <th class="inv-right">Bezahlt</th>
                            <th class="inv-right">Offen</th>
                            <th>PDF</th>
                            <th class="inv-right">Aktion</th>
                          </tr>
                        </thead>
                        <tbody>
                          ${invs.map(inv => {
                            const id = inv.id;
                            const date = fmtDate(inv.issue_date);
                            const no = inv.invoice_no || '—';
                            const type = inv.type || '—';
                            const pdfOk = hasPdf(inv);

                            const fin = isFinancialType(inv);
                            const total = fin ? signedTotal(inv) : 0;
                            const paid  = fin ? signedPaid(inv)  : 0;
                            const open  = fin ? signedOpen(inv)  : 0;

                            const openIsWarn   = open > 0.00001;
                            const openIsCredit = open < -0.00001;

                            return `
                              <tr class="inv-row" data-id="${id}">
                                <td>${escapeHtml(date)}</td>
                                <td class="inv-fw-700">${escapeHtml(no)}</td>
                                <td>
                                  ${escapeHtml(type)}
                                  ${isSchluss(inv) ? `<span class="inv-pill inv-pill-muted" style="margin-left:.4rem;"><i class="fa-solid fa-flag-checkered"></i> Ende</span>` : ``}
                                  ${isCreditType(inv) ? `<span class="inv-pill inv-pill-muted" style="margin-left:.4rem;"><i class="fa-solid fa-rotate-left"></i> Storno</span>` : ``}
                                </td>
                                <td>
                                  <div data-id="${id}">
                                    ${statusOptions(inv.status)}
                                  </div>
                                </td>

                                <td class="inv-right inv-fw-700" style="${total<0?'color:var(--inv-danger);':''}">
                                  ${escapeHtml(moneySigned(total))} €
                                </td>

                                <td class="inv-right" style="${paid<0?'color:var(--inv-danger);':''}">
                                  ${escapeHtml(moneySigned(paid))} €
                                </td>

                                <td class="inv-right ${openIsWarn?'inv-fw-700':''}"
                                    style="${openIsWarn?'color:var(--inv-danger);':(openIsCredit?'color:#075985;':'')}">
                                  ${escapeHtml(moneySigned(open))} €
                                </td>

                                <td>
                                  <span class="inv-pill ${pdfOk?'inv-pill-ok':'inv-pill-warn'}">
                                    <i class="fa-solid fa-file-pdf"></i>${pdfOk?'Upload':'Fehlt'}
                                  </span>
                                </td>

                                <td class="inv-right">
                                  <a href="javascript:void(0)" class="inv-btn-icon inv-open-files" data-id="${id}" title="Dateien öffnen"><i class="fa-solid fa-folder-open"></i></a>
                                  <a href="javascript:void(0)" class="inv-btn-icon inv-edit" data-id="${id}" title="Bearbeiten" style="margin-left:6px;"><i class="fa-solid fa-pen-to-square"></i></a>
                                  <a href="javascript:void(0)" class="inv-btn-icon inv-del" data-id="${id}" title="Löschen" style="margin-left:6px;border-color:rgba(220,38,38,.25);color:var(--inv-danger);"><i class="fa-solid fa-trash"></i></a>
                                </td>
                              </tr>
                            `;
                          }).join('')}
                        </tbody>
                      </table>
                    </div>
                  </div>
                </details>
              `;
            }).join('')}
          </div>
        `;
      }

      async function load(){
        if (state.loading) return;
        state.loading = true;

        renderEmpty('Lade Daten...');
        els.meta.textContent = 'Lade Daten...';

        try{
          const j = await apiJson(buildListUrl());
          const rows = j.data || [];
          const meta = j.meta || {};

          state.total = meta.total || 0;
          state.hasMore = (meta.current_page || 1) < (meta.last_page || 1);

          // ✅ analytics only used for KPI cards, NOT for pagination/meta text
          renderKpis(j.analytics || {});

          const start = meta.total ? (((meta.current_page - 1) * meta.per_page) + 1) : 0;
          const end = meta.total ? Math.min(meta.current_page * meta.per_page, meta.total) : 0;

          els.meta.textContent = meta.total
            ? `Zeige ${start}-${end} von ${meta.total} Einträgen`
            : '0 Einträge';

          els.prev.disabled = (meta.current_page || 1) <= 1;
          els.next.disabled = !state.hasMore;

          if (state.view === 'table') renderTable(rows);
          else if (state.view === 'card') renderCards(rows);
          else renderCustomers(rows);

        }catch(e){
          renderEmpty(e.message || 'Fehler beim Laden');
          els.meta.textContent = '';
        }finally{
          state.loading = false;
        }
      }

      function debouncedReload(){
        clearTimeout(state.timer);
        state.timer = setTimeout(() => { state.page = 1; load(); }, 250);
      }

      function applyView(view){
        state.view = view;

        els.tableWrap.classList.toggle('inv-hide', state.view !== 'table');
        els.cardWrap.classList.toggle('inv-hide', state.view !== 'card');
        els.customerWrap.classList.toggle('inv-hide', state.view !== 'customer');

        state.page = 1;
        load();
      }

      applyView(state.view);

      els.viewTable.addEventListener('click', () => applyView('table'));
      els.viewCards.addEventListener('click', () => applyView('card'));
      els.viewCustomers.addEventListener('click', () => applyView('customer'));

      els.q.addEventListener('input', debouncedReload);
      els.type.addEventListener('change', debouncedReload);
      els.status.addEventListener('change', debouncedReload);
      els.from.addEventListener('change', debouncedReload);
      els.to.addEventListener('change', debouncedReload);

      els.per.addEventListener('change', () => {
        state.perPage = Number(els.per.value || 12);
        state.page = 1;
        load();
      });

      els.reset.addEventListener('click', () => {
        els.q.value = '';
        els.type.value = '';
        els.status.value = '';
        els.from.value = '';
        els.to.value = '';
        els.per.value = '12';
        state.perPage = 12;
        state.sortBy = 'issue_date';
        state.sortDir = 'desc';
        state.page = 1;
        load();
      });

      els.refresh.addEventListener('click', () => load());

      document.querySelectorAll('.inv-sort').forEach(th => {
        th.addEventListener('click', () => {
          const s = th.getAttribute('data-sort');
          if (!s) return;
          if (state.sortBy === s) state.sortDir = (state.sortDir === 'asc') ? 'desc' : 'asc';
          else { state.sortBy = s; state.sortDir = 'desc'; }
          state.page = 1;
          load();
        });
      });

      els.prev.addEventListener('click', () => { if (state.page > 1) { state.page--; load(); } });
      els.next.addEventListener('click', () => { if (state.hasMore) { state.page++; load(); } });

      function openDrawer(){
        els.drawer.classList.add('active');
        els.backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
      function closeDrawer(){
        els.drawer.classList.remove('active');
        els.backdrop.classList.remove('active');
        document.body.style.overflow = '';
        resetForm();
      }
      els.closeDrawer.addEventListener('click', closeDrawer);
      els.backdrop.addEventListener('click', closeDrawer);

      function setStep(n){
        const s1 = (n === 1);
        els.step1.classList.toggle('inv-hide', !s1);
        els.step2.classList.toggle('inv-hide', s1);
        els.step1Pill.classList.toggle('active', s1);
        els.step2Pill.classList.toggle('active', !s1);
        els.drawerSub.textContent = s1 ? 'Schritt 1: Daten' : 'Schritt 2: Dateien';
      }
      els.step1Pill.addEventListener('click', () => setStep(1));
      els.step2Pill.addEventListener('click', () => setStep(2));

      function setPriceMode(mode){
        state.priceMode = (mode === 'total') ? 'total' : 'items';
        els.itemsBlock.classList.toggle('inv-hide', state.priceMode !== 'items');
        els.totalOnlyBlock.classList.toggle('inv-hide', state.priceMode !== 'total');
        if (state.priceMode === 'total') {
          if (!els.mTotalTitle.value) els.mTotalTitle.value = 'Pauschale / Gesamtbetrag';
        }
        calcTotals();
      }

      els.pmItems.addEventListener('change', () => setPriceMode('items'));
      els.pmTotal.addEventListener('change', () => setPriceMode('total'));
      els.mTotalNet.addEventListener('input', () => calcTotals());

      function applyCompactColumns(on){
        state.compactColumns = !!on;

        // apply class to items block wrapper
        els.itemsBlock.classList.toggle('inv-compact-items', state.compactColumns);

        // if compact: force qty=1, unit empty
        if (state.compactColumns) {
          state.items = (state.items || []).map(it => ({ ...it, qty: 1, unit: '' }));
        }
        renderItems();
      }

      if (els.toggleCompact){
        els.toggleCompact.addEventListener('change', () => applyCompactColumns(els.toggleCompact.checked));
      }

      function resetForm(){
        state.editingId = null;
        state.activeInvoiceId = null;
        state.items = [];
        state.uploadedFiles = [];
        state.pickedFiles = [];
        state.priceMode = 'items';
        state.compactColumns = false;

        els.drawerTitle.textContent = 'Neue Rechnung';
        els.activeId.textContent = '—';
        els.saveHint.textContent = '';
        els.pickedHint.textContent = '';

        els.mInvoiceNo.value = '';
        els.mType.value = 'Rechnung';
        els.mStatus.value = 'draft';

        const today = new Date().toISOString().slice(0,10);
        els.mIssueDate.value = today;
        els.mDueDate.value = '';
        els.mServiceFrom.value = '';
        els.mServiceTo.value = '';
        els.mTaxRate.value = '0';
        els.mNotes.value = '';

        els.mTotalTitle.value = '';
        els.mTotalNet.value = '';

        els.pmItems.checked = true;
        els.pmTotal.checked = false;
        setPriceMode('items');

        if (els.toggleCompact) els.toggleCompact.checked = false;
        els.itemsBlock.classList.remove('inv-compact-items');

        els.mCustomer.val(null).trigger('change');
        els.mObject.val(null).trigger('change');
        els.mProductPicker.val(null).trigger('change');

        els.mFiles.value = '';
        renderItems();
        renderFiles();
        setStep(1);
      }

      (function initSelect2(){
        const $drawer = $('#invDrawer');

        els.mCustomer.select2({
          placeholder: 'Kunde wählen...',
          allowClear: true,
          width: '100%',
          dropdownParent: $drawer,
          ajax: {
            url: API.selCustomers,
            dataType: 'json',
            delay: 250,
            data: params => ({ term: params.term || '' }),
            processResults: data => ({ results: data.results || [] }),
            cache: true
          }
        });

        els.mObject.select2({
          placeholder: 'Objekt wählen...',
          allowClear: true,
          width: '100%',
          dropdownParent: $drawer,
          ajax: {
            url: API.selObjects,
            dataType: 'json',
            delay: 250,
            data: params => ({ term: params.term || '', customer_id: els.mCustomer.val() || '' }),
            processResults: data => ({ results: data.results || [] }),
            cache: true
          }
        });

        els.mProductPicker.select2({
          placeholder: 'Produkt hinzufügen...',
          allowClear: true,
          width: '100%',
          dropdownParent: $drawer,
          ajax: {
            url: API.selProducts,
            dataType: 'json',
            delay: 250,
            data: params => ({
              term: params.term || '',
              customer_id: els.mCustomer.val() || '',
              object_id: els.mObject.val() || '',
            }),
            processResults: data => ({ results: data.results || [] }),
            cache: false
          }
        });

        els.mCustomer.on('change', () => {
          els.mObject.val(null).trigger('change');
        });

        els.mProductPicker.on('select2:select', (e) => {
          const d = e.params.data || {};
          addItem({ product_id: d.id, title: d.text || 'Produkt', qty: 1, unit: '', unit_price: 0 });
          els.mProductPicker.val(null).trigger('change');
        });
      })();

      function addItem(it){
        state.items.push({
          product_id: it.product_id ?? null,
          title: it.title ?? 'Position',
          qty: Number(it.qty ?? 1),
          unit: it.unit ?? '',
          unit_price: Number(it.unit_price ?? 0),
          tax_rate: 0,
          sort_order: state.items.length,
        });

        // enforce compact rules
        if (state.compactColumns) {
          state.items[state.items.length - 1].qty = 1;
          state.items[state.items.length - 1].unit = '';
        }

        renderItems();
      }

      function calcTotals(){
        let subtotal = 0;

        if (state.priceMode === 'total') subtotal = Number(els.mTotalNet.value || 0);
        else state.items.forEach(it => subtotal += (Number(it.qty||0) * Number(it.unit_price||0)));

        subtotal = Math.round(subtotal * 100) / 100;

        const taxRate = Number(els.mTaxRate.value || 0);
        const tax = Math.round((subtotal * (taxRate/100)) * 100) / 100;
        const total = Math.round((subtotal + tax) * 100) / 100;

        els.sumSubtotal.textContent = money(subtotal);
        els.sumTax.textContent = money(tax);
        els.sumTotal.textContent = money(total);
      }

      function renderItems(){
        // keep wrapper class in sync
        els.itemsBlock.classList.toggle('inv-compact-items', !!state.compactColumns);

        if (state.priceMode === 'total') {
          els.itemsBody.innerHTML = `<tr><td colspan="7" class="inv-center" style="padding:1.25rem;color:var(--inv-muted);">Gesamtbetrag-Modus aktiv</td></tr>`;
          calcTotals();
          return;
        }

        if (!state.items.length){
          els.itemsBody.innerHTML = `<tr><td colspan="7" class="inv-center" style="padding:1.25rem;color:var(--inv-muted);">Keine Positionen</td></tr>`;
          calcTotals();
          return;
        }

        els.itemsBody.innerHTML = state.items.map((it, idx) => {
          const qty = Number(it.qty||0);
          const up  = Number(it.unit_price||0);
          const line = Math.round((qty * up) * 100) / 100;

          return `
            <tr data-idx="${idx}">
              <td><input class="inv-control" data-k="title" value="${escapeHtml(it.title)}"></td>

              <td class="cell-qty"><input class="inv-control" data-k="qty" type="number" step="0.01" min="0.01" value="${it.qty}"></td>
              <td class="cell-unit"><input class="inv-control" data-k="unit" value="${escapeHtml(it.unit||'')}"></td>

              <td class="cell-unitprice inv-right">
                <input class="inv-control inv-right" data-k="unit_price" type="number" step="0.01" min="0" value="${it.unit_price}">
              </td>

              <td class="cell-price inv-right" style="display:none;">
                <input class="inv-control inv-right" data-k="price_only" type="number" step="0.01" min="0" value="${it.unit_price}">
              </td>

              <td class="cell-sum inv-right inv-fw-700"><span class="inv-line-sum">${money(line)} €</span></td>

              <td class="inv-right">
                <button class="inv-btn inv-btn-light inv-remove-item inv-btn-sm" data-idx="${idx}" type="button" style="border-color:rgba(220,38,38,.25);color:var(--inv-danger);">
                  <i class="fa-solid fa-xmark"></i>&nbsp; Entfernen
                </button>
              </td>
            </tr>
          `;
        }).join('');

        calcTotals();
      }

      els.itemsBody.addEventListener('input', (e) => {
        if (state.priceMode !== 'items') return;

        const tr = e.target.closest('tr[data-idx]');
        if (!tr) return;

        const idx = Number(tr.getAttribute('data-idx'));
        const k = e.target.getAttribute('data-k');
        if (!k || !state.items[idx]) return;

        if (k === 'qty' || k === 'unit_price' || k === 'price_only') {
          const raw = String(e.target.value ?? '').trim();
          const val = raw === '' ? 0 : Number(raw);

          if (k === 'price_only') {
            state.items[idx].unit_price = val;
            if (state.compactColumns) {
              state.items[idx].qty = 1;
              state.items[idx].unit = '';
            }
          } else {
            state.items[idx][k] = val;
            if (state.compactColumns) {
              state.items[idx].qty = 1;
              state.items[idx].unit = '';
            }
          }
        } else {
          state.items[idx][k] = String(e.target.value ?? '');
        }

        const it = state.items[idx];
        const line = Math.round((Number(it.qty || 0) * Number(it.unit_price || 0)) * 100) / 100;
        const sumEl = tr.querySelector('.inv-line-sum');
        if (sumEl) sumEl.textContent = `${money(line)} €`;

        calcTotals();
      });

      els.itemsBody.addEventListener('click', (e) => {
        if (state.priceMode !== 'items') return;
        const btn = e.target.closest('.inv-remove-item');
        if (!btn) return;
        const idx = Number(btn.getAttribute('data-idx'));
        state.items.splice(idx, 1);
        renderItems();
      });

      els.addManualItem.addEventListener('click', () => addItem({ title: 'Manuelle Position', qty: 1, unit: '', unit_price: 0 }));
      els.mTaxRate.addEventListener('input', () => calcTotals());

      function buildPayload(){
        let items = [];

        if (state.priceMode === 'total') {
          const net = Number(els.mTotalNet.value || 0);
          items = [{
            product_id: null,
            title: String(els.mTotalTitle.value || 'Pauschale / Gesamtbetrag').trim(),
            description: null,
            qty: 1,
            unit: null,
            unit_price: net,
            tax_rate: 0,
            sort_order: 0,
          }];
        } else {
          items = state.items.map((it, i) => ({
            product_id: it.product_id ?? null,
            title: String(it.title || 'Position').trim(),
            description: null,
            qty: state.compactColumns ? 1 : Number(it.qty || 1),
            unit: state.compactColumns ? null : (String(it.unit || '').trim() || null),
            unit_price: Number(it.unit_price || 0),
            tax_rate: 0,
            sort_order: i,
          }));
        }

        return {
          customer_id: Number(els.mCustomer.val() || 0),
          object_id: els.mObject.val() ? Number(els.mObject.val()) : null,
          invoice_no: (els.mInvoiceNo.value || '').trim() || null,
          type: els.mType.value,
          status: els.mStatus.value,
          issue_date: els.mIssueDate.value,
          due_date: els.mDueDate.value || null,
          service_from: els.mServiceFrom.value || null,
          service_to: els.mServiceTo.value || null,
          currency: 'EUR',
          tax_rate: Number(els.mTaxRate.value || 0),
          notes: (els.mNotes.value || '').trim() || null,
          items,
        };
      }

      async function saveStep1(){
        const payload = buildPayload();

        if (!payload.customer_id) throw new Error('Kunde ist erforderlich.');
        if (!payload.issue_date) throw new Error('Ausstellungsdatum ist erforderlich.');
        if (!payload.type) throw new Error('Typ ist erforderlich.');
        if (!payload.status) throw new Error('Status ist erforderlich.');
        if (!payload.items.length) throw new Error('Mindestens 1 Position hinzufügen.');
        if (state.priceMode === 'total' && Number(els.mTotalNet.value || 0) <= 0) throw new Error('Gesamt (Netto) muss größer als 0 sein.');

        els.saveHint.textContent = 'Speichere...';

        if (state.editingId){
          await apiJson(API.update.replace('__ID__', state.editingId), {
            method: 'PUT',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload),
          });
          state.activeInvoiceId = state.editingId;
        }else{
          const res = await apiJson(API.store, {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload),
          });
          state.editingId = res.invoice_id;
          state.activeInvoiceId = res.invoice_id;
          els.drawerTitle.textContent = 'Rechnung #' + res.invoice_id;
        }

        els.activeId.textContent = 'Rechnung-ID: ' + state.activeInvoiceId;
        els.saveHint.textContent = 'Gespeichert.';
        setStep(2);
        await load();
      }

      els.saveStep1.addEventListener('click', async () => {
        try{ await saveStep1(); }
        catch(e){ alert(e.message || 'Speichern fehlgeschlagen'); }
      });

      function openPdfModal(file){
        const name = file?.stored_name || file?.original_name || 'datei.pdf';
        const viewUrl = API.viewFile.replace('__ID__', file.id);
        const downloadUrl = API.downloadFile.replace('__ID__', file.id);

        els.pdfTitle.textContent = name;
        els.pdfFrame.src = viewUrl;
        els.pdfOpenNew.href = viewUrl;
        els.pdfDownload.href = downloadUrl;

        els.pdfBackdrop.classList.add('active');
        els.pdfModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
      function closePdfModal(){
        els.pdfBackdrop.classList.remove('active');
        els.pdfModal.classList.remove('active');
        els.pdfFrame.src = 'about:blank';
        document.body.style.overflow = '';
      }
      els.pdfClose.addEventListener('click', closePdfModal);
      els.pdfBackdrop.addEventListener('click', closePdfModal);
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePdfModal(); });

      function renderFiles(){
        const files = Array.isArray(state.uploadedFiles) ? state.uploadedFiles : [];
        if (!files.length){
          els.filesList.innerHTML = `<div class="inv-muted inv-small inv-fw-600" style="grid-column:1/-1;">Keine Dateien vorhanden.</div>`;
          return;
        }

        els.filesList.innerHTML = files.map(f => {
          const name = f.stored_name || f.original_name || 'datei.pdf';
          const kb = Math.round((Number(f.size||0)/1024));
          const downloadUrl = API.downloadFile.replace('__ID__', f.id);

          return `
            <div class="inv-file-card inv-open-pdf" data-id="${f.id}">
              <div class="inv-file-ico"><i class="fa-solid fa-file-pdf"></i></div>
              <div style="min-width:0;flex:1;">
                <div class="inv-fw-700 inv-trunc" title="${escapeHtml(name)}">${escapeHtml(name)}</div>
                <div class="inv-muted inv-small inv-fw-600" style="margin-top:2px;">${kb} KB</div>
                <div class="inv-flex inv-gap-2" style="justify-content:flex-end;margin-top:.6rem;">
                  <a class="inv-btn inv-btn-light inv-btn-sm" href="${downloadUrl}" onclick="event.stopPropagation();">
                    <i class="fa-solid fa-download"></i>&nbsp; Herunterladen
                  </a>
                  <button class="inv-btn inv-btn-danger inv-btn-sm inv-del-file" data-id="${f.id}" type="button" onclick="event.stopPropagation();">
                    <i class="fa-solid fa-trash"></i>&nbsp; Löschen
                  </button>
                </div>
              </div>
            </div>
          `;
        }).join('');
      }

      function setPickedFiles(filesArr){
        state.pickedFiles = (filesArr || []).filter(f => f && String(f.type || '').includes('pdf'));
        if (!state.pickedFiles.length){
          els.pickedHint.textContent = '';
          return;
        }
        const names = state.pickedFiles.slice(0, 4).map(f => f.name).join(', ');
        const more = state.pickedFiles.length > 4 ? ` +${state.pickedFiles.length - 4} weitere` : '';
        els.pickedHint.textContent = `Ausgewählt: ${names}${more}`;
      }

      els.mFiles.addEventListener('change', (e) => {
        e.stopPropagation();
        setPickedFiles(Array.from(els.mFiles.files || []));
      });

      async function reloadActiveInvoice(){
        if (!state.activeInvoiceId) return;
        const j = await apiJson(API.show.replace('__ID__', state.activeInvoiceId));
        const inv = j.invoice || {};
        state.uploadedFiles = (inv.files || []).map(f => ({
          id: f.id,
          stored_name: f.stored_name,
          original_name: f.original_name,
          size: f.size,
        }));
        renderFiles();
      }

      async function uploadFiles(){
        if (!state.activeInvoiceId) throw new Error('Bitte zuerst Schritt 1 speichern.');

        const files = state.pickedFiles.length ? state.pickedFiles : Array.from(els.mFiles.files || []);
        if (!files.length) throw new Error('Bitte PDFs auswählen.');

        const fd = new FormData();
        for (const f of files) fd.append('files[]', f);

        await apiJson(API.upload.replace('__ID__', state.activeInvoiceId), {
          method: 'POST',
          body: fd
        });

        state.pickedFiles = [];
        els.mFiles.value = '';
        els.pickedHint.textContent = '';
        await reloadActiveInvoice();
        await load();
      }

      async function deleteFile(id){
        if (!confirm('Datei löschen?')) return;
        await apiJson(API.deleteFile.replace('__ID__', id), { method: 'DELETE' });
        state.uploadedFiles = state.uploadedFiles.filter(x => String(x.id) !== String(id));
        renderFiles();
      }

      els.pickFiles.addEventListener('click', (e) => { e.stopPropagation(); els.mFiles.click(); });
      els.uploadFiles.addEventListener('click', async (e) => {
        e.stopPropagation();
        try{ await uploadFiles(); }catch(err){ alert(err.message || 'Upload fehlgeschlagen'); }
      });

      els.drop.addEventListener('click', (e) => {
        if (e.target.closest('button, a, input')) return;
        els.mFiles.click();
      });
      els.drop.addEventListener('dragover', (e) => { e.preventDefault(); els.drop.style.borderColor = 'rgba(22,163,74,.55)'; });
      els.drop.addEventListener('dragleave', () => { els.drop.style.borderColor = 'rgba(116,178,212,.45)'; });
      els.drop.addEventListener('drop', (e) => {
        e.preventDefault();
        els.drop.style.borderColor = 'rgba(116,178,212,.45)';
        const dropped = Array.from(e.dataTransfer?.files || []);
        if (dropped.length) setPickedFiles(dropped);
      });

      els.filesList.addEventListener('click', (e) => {
        const delBtn = e.target.closest('.inv-del-file');
        if (delBtn){
          e.stopPropagation();
          const id = delBtn.getAttribute('data-id');
          deleteFile(id).catch(err => alert(err.message || 'Löschen fehlgeschlagen'));
          return;
        }

        const card = e.target.closest('.inv-open-pdf');
        if (card){
          e.stopPropagation();
          const id = card.getAttribute('data-id');
          const f = (state.uploadedFiles || []).find(x => String(x.id) === String(id));
          if (f) openPdfModal(f);
        }
      });

      els.reloadFiles.addEventListener('click', async (e) => {
        e.stopPropagation();
        try{ await reloadActiveInvoice(); }catch(err){ alert(err.message || 'Neu laden fehlgeschlagen'); }
      });

      els.backTo1.addEventListener('click', () => setStep(1));
      els.finish.addEventListener('click', () => { closeDrawer(); load(); });

      els.newBtn.addEventListener('click', () => { resetForm(); openDrawer(); });

      async function openInvoice(id, startStep = 1){
        resetForm();
        state.editingId = id;
        state.activeInvoiceId = id;
        els.drawerTitle.textContent = 'Rechnung #' + id;
        els.activeId.textContent = 'Rechnung-ID: ' + id;
        openDrawer();

        const j = await apiJson(API.show.replace('__ID__', id));
        const inv = j.invoice || {};

        els.mInvoiceNo.value = inv.invoice_no || '';
        els.mType.value = inv.type || 'Rechnung';
        els.mStatus.value = inv.status || 'draft';

        els.mIssueDate.value = dateOnly(inv.issue_date || '');
        els.mDueDate.value = inv.due_date ? dateOnly(inv.due_date) : '';
        els.mServiceFrom.value = inv.service_from ? dateOnly(inv.service_from) : '';
        els.mServiceTo.value = inv.service_to ? dateOnly(inv.service_to) : '';
        els.mTaxRate.value = Number(inv.tax_rate || 0);
        els.mNotes.value = inv.notes || '';

        if (inv.customer){
          const opt = new Option(customerLabel(inv.customer), inv.customer.id, true, true);
          els.mCustomer.append(opt).trigger('change');
        }
        if (inv.object){
          const opt = new Option(objectLabel(inv.object), inv.object.id, true, true);
          els.mObject.append(opt).trigger('change');
        }

        const invItems = (inv.items || []).map((it, i) => ({
          product_id: it.product_id ?? null,
          title: it.title ?? 'Position',
          qty: Number(it.qty ?? 1),
          unit: it.unit ?? '',
          unit_price: Number(it.unit_price ?? 0),
          sort_order: i,
        }));

        state.items = invItems;

        // ✅ if compact is ON, enforce
        if (state.compactColumns) {
          state.items = state.items.map(it => ({ ...it, qty: 1, unit: '' }));
        }

        state.uploadedFiles = (inv.files || []).map(f => ({
          id: f.id,
          stored_name: f.stored_name,
          original_name: f.original_name,
          size: f.size,
        }));

        renderItems();
        renderFiles();
        setStep(startStep);
      }

      async function doDelete(id){
        if (!confirm('Rechnung löschen?')) return;
        await apiJson(API.destroy.replace('__ID__', id), { method: 'DELETE' });
        await load();
      }

      async function updateInvoiceStatus(id, status, selectEl = null){
        const oldValue = selectEl ? selectEl.getAttribute('data-current') || selectEl.value : null;

        try{
          if (selectEl) selectEl.classList.add('is-saving');

          await apiJson(API.status.replace('__ID__', id), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
          });

          if (selectEl) {
            selectEl.setAttribute('data-current', status);
            const wrap = selectEl.closest('.inv-status-wrap');
            const dot = wrap ? wrap.querySelector('.inv-status-dot') : null;
            if (dot) {
              dot.className = `inv-status-dot ${status}`;
            }
          }

          if (state.editingId && String(state.editingId) === String(id)) {
            els.mStatus.value = status;
          }

          await load();
        } catch (e) {
          if (selectEl && oldValue !== null) {
            selectEl.value = oldValue;
          }
          alert(e.message || 'Status konnte nicht geändert werden');
        } finally {
          if (selectEl) selectEl.classList.remove('is-saving');
        }
      }

      document.addEventListener('click', (e) => {
        const openFiles = e.target.closest('.inv-open-files');
        const edit = e.target.closest('.inv-edit');
        const del = e.target.closest('.inv-del');

        if (openFiles){
          e.stopPropagation();
          openInvoice(openFiles.getAttribute('data-id'), 2).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
          return;
        }
        if (edit){
          e.stopPropagation();
          openInvoice(edit.getAttribute('data-id'), 1).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
          return;
        }
        if (del){
          e.stopPropagation();
          doDelete(del.getAttribute('data-id')).catch(err => alert(err.message || 'Löschen fehlgeschlagen'));
          return;
        }

        const row = e.target.closest('.inv-row[data-id]');
        if (row){
          openInvoice(row.getAttribute('data-id'), 2).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
        }
      });


      document.addEventListener('change', (e) => {
        const select = e.target.closest('.inv-quick-status');
        if (!select) return;

        e.stopPropagation();

        const holder = select.closest('[data-id]');
        const id = holder ? holder.getAttribute('data-id') : null;
        if (!id) return;

        updateInvoiceStatus(id, select.value, select);
      });


      document.addEventListener('click', (e) => {
          const quick = e.target.closest('.inv-quick-status');
          if (quick) {
            e.stopPropagation();
          }
        });


        document.addEventListener('mousedown', (e) => {
          const quick = e.target.closest('.inv-quick-status');
          if (quick) {
            e.stopPropagation();
          }
        });



      resetForm();
      load();
    });
  </script>
@endsection
