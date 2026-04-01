{{-- resources/views/admin/maintenance/contracts/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Wartungsverträge')

@section('style')
  {{-- FullCalendar --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

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
    }

    .mc-page{ padding:18px 12px 30px; background:var(--mc-shell-bg); min-height:calc(100vh - 80px); }
    .mc-container{ max-width: 100%; margin:0 auto; }
    .mc-shell{ border-radius:var(--mc-radius-xl); padding:16px 18px 18px; }

    .mc-header{ display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:14px; }
    .mc-title{ font-size:1.15rem; font-weight:700; letter-spacing:-0.02em; color:var(--mc-text); }
    .mc-subtitle{ font-size:0.78rem; color:var(--mc-muted); }

    .mc-header-right{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; justify-content:flex-end; }
    .mc-view-toggle{ display:inline-flex; border-radius:999px; border:1px solid var(--mc-border); overflow:hidden; font-size:0.75rem; background:#fff; }
    .mc-view-toggle button{ border:none; background:transparent; color:var(--mc-muted); padding:6px 12px; cursor:pointer; display:inline-flex; gap:6px; align-items:center; }
    .mc-view-toggle button.is-active{ background:var(--mc-success); color:#111827; }
    .mc-btn{ border-radius:999px; border:1px solid transparent; padding:7px 14px; font-size:0.78rem; display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:transparent; color:var(--mc-text); white-space:nowrap; }
    .mc-btn-ghost{ border-color:var(--mc-border); background:#fff; color:var(--mc-muted); }
    .mc-btn-primary{ border-color:var(--mc-accent); background:var(--mc-accent); color:#0b1120; }
    .mc-btn-danger{ border-color:var(--mc-danger); background:rgba(249,115,115,0.12); color:#7f1d1d; }

    .mc-toolbar{ display:grid; grid-template-columns: 2fr 1.7fr auto; gap:10px; margin-bottom:12px; align-items:center; }
    @media (max-width: 992px){ .mc-toolbar{ grid-template-columns: 1fr; } }
    .mc-input,.mc-select{ width:100%; border-radius:999px; border:1px solid var(--mc-border-soft); background:#fff; padding:7px 10px; font-size:0.78rem; color:var(--mc-muted); outline:none; }
    .mc-input:focus,.mc-select:focus{ border-color:var(--mc-accent); }

    .mc-views{ margin-top: 8px; }
    .mc-view{ display:none; }
    .mc-view.is-active{ display:block; }

    /* List */
    .mc-table-shell{ border-radius:var(--mc-radius-lg); border:1px solid var(--mc-border-soft); overflow:hidden; background:#fff; }
    .mc-table{ width:100%; border-collapse:collapse; font-size:0.78rem; }
    .mc-table th,.mc-table td{ padding:8px 10px; border-bottom:1px solid rgba(2,6,23,0.08); vertical-align:top; }
    .mc-table th{ text-transform:uppercase; letter-spacing:0.08em; font-size:0.7rem; color:var(--mc-muted); white-space:nowrap; background:rgba(2,6,23,0.03); }
    .mc-table tbody tr:hover{ background: rgba(147,194,28,0.10); }

    .mc-row-title{ font-weight:700; color:#0b1120; }
    .mc-row-sub{ font-size:0.73rem; color:var(--mc-muted); margin-top:2px; line-height:1.3; }

    .mc-status-pill{ border-radius:999px; padding:2px 8px; font-size:0.68rem; text-transform:uppercase; letter-spacing:0.06em; display:inline-flex; align-items:center; gap:6px; }
    .mc-status-draft{ background:rgba(148,163,184,0.22); color:#334155; }
    .mc-status-active{ background:rgba(147,194,28,0.20); color:#365314; }
    .mc-status-inactive{ background:rgba(251,191,36,0.20); color:#7c2d12; }
    .mc-status-cancelled{ background:rgba(249,115,115,0.20); color:#7f1d1d; }

    .mc-tag{ border-radius:999px; padding:2px 8px; font-size:0.68rem; border:1px solid var(--mc-border-soft); color:var(--mc-muted); background:#fff; display:inline-flex; gap:6px; align-items:center; }
    .mc-dot{ width:7px; height:7px; border-radius:99px; background:var(--mc-accent); display:inline-block; }

    .mc-pagination{ padding:8px 10px 4px; font-size:0.74rem; color:var(--mc-muted); display:flex; justify-content:space-between; align-items:center; gap:6px; }

    /* Layout for Calendar + Right Sidebar */
    .mc-calendar-grid{
      display:grid;
      grid-template-columns: 1fr 360px;
      gap: 12px;
      align-items:start;
    }
    @media(max-width: 1200px){
      .mc-calendar-grid{ grid-template-columns:1fr; }
    }

    .mc-card{
      border-radius: var(--mc-radius-lg);
      border: 1px solid var(--mc-border-soft);
      background:#fff;
      overflow:hidden;
    }
    .mc-card-hd{
      padding:10px 12px;
      border-bottom:1px solid rgba(2,6,23,0.08);
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:8px;
    }
    .mc-card-hd strong{ color:#0b1120; font-size:0.85rem; }
    .mc-card-bd{ padding:10px 12px; }

    .mw-upcoming-item{
      border: 1px solid rgba(2,6,23,0.08);
      border-radius: 14px;
      padding: 10px;
      background: #fff;
      margin-bottom: 10px;
      cursor:pointer;
    }
    .mw-upcoming-item:hover{ background: rgba(116,178,212,0.10); }
    .mw-up-title{ font-weight:700; color:#0b1120; font-size:0.78rem; }
    .mw-up-meta{ font-size:0.72rem; color:var(--mc-muted); margin-top:4px; line-height:1.35; }
    .mw-badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      border-radius:999px;
      padding:2px 8px;
      font-size:0.68rem;
      border:1px solid rgba(2,6,23,0.10);
      background:#fff;
      margin-top:6px;
    }
    .mw-badge.is-soon{ border-color: rgba(251,191,36,0.55); }
    .mw-badge.is-overdue{ border-color: rgba(249,115,115,0.55); }
    .mw-badge .mw-dot{ width:7px; height:7px; border-radius:99px; background: var(--mc-warning); }
    .mw-badge.is-overdue .mw-dot{ background: var(--mc-danger); }

    /* Modal */
    .mw-modal{
      position:fixed; inset:0;
      display:none;
      background: rgba(2,6,23,0.55);
      z-index: 9999;
      padding: 18px;
      align-items:center;
      justify-content:center;
    }
    .mw-modal.is-open{ display:flex; }
    .mw-modal-card{
      width: min(720px, 100%);
      border-radius: 18px;
      background:#fff;
      border:1px solid rgba(2,6,23,0.12);
      overflow:hidden;
    }
    .mw-modal-hd{
      padding:12px 14px;
      border-bottom:1px solid rgba(2,6,23,0.08);
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
    }
    .mw-modal-hd strong{ color:#0b1120; }
    .mw-modal-bd{ padding: 12px 14px; }
    .mw-close{
      border:none;
      background: rgba(2,6,23,0.06);
      border-radius: 999px;
      padding: 6px 10px;
      cursor:pointer;
      color:#0b1120;
    }
    .mw-modal-row{ display:grid; grid-template-columns: 150px 1fr; gap:10px; padding:6px 0; border-bottom:1px dashed rgba(2,6,23,0.10); }
    .mw-modal-row:last-child{ border-bottom:none; }
    .mw-k{ color:var(--mc-muted); font-size:0.72rem; }
    .mw-v{ color:#0b1120; font-size:0.78rem; font-weight:600; }
  </style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="col-12">
        <h2 class="content-header-title float-left mb-0">Wartungsverträge</h2>
        <div class="breadcrumb-wrapper col-12">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Verträge &amp; Wartung</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="mc-page">
        <div class="mc-container">
          <div class="mc-shell">

            {{-- Header --}}
            <div class="mc-header">
              <div>
                <div class="mc-title">Wartungsverträge</div>
                <div class="mc-subtitle">Liste, Kalender und anstehende Wartungen.</div>
              </div>

              <div class="mc-header-right">
                <div class="mc-view-toggle" id="mc-view-toggle">
                  <button type="button" data-view="list" class="is-active">
                    <i class="fa fa-list-ul"></i><span>Liste</span>
                  </button>
                  <button type="button" data-view="calendar">
                    <i class="fa fa-calendar"></i><span>Kalender</span>
                  </button>
                  <button type="button" data-view="kanban">
                    <i class="fa fa-columns"></i><span>Kanban</span>
                  </button>
                </div>
              </div>
            </div>

            {{-- Toolbar --}}
            <form method="GET" action="{{ route('admin.maintenance.contracts.index') }}">
              <div class="mc-toolbar">
                <div>
                  <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="mc-input"
                         placeholder="Suche nach Vertragsnr., Titel, Kunde, Adresse, Produkt...">
                </div>

                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                  <select name="status" class="mc-select" style="min-width: 180px;">
                    <option value="">Status (alle)</option>
                    @php $statusOptions = ['draft'=>'Entwurf','active'=>'Aktiv','inactive'=>'Inaktiv','cancelled'=>'Gekündigt']; @endphp
                    @foreach($statusOptions as $value => $label)
                      <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                  </select>

                  <select name="interval_type" class="mc-select" style="min-width: 180px;">
                    <option value="">Intervall (alle)</option>
                    <option value="yearly" @selected(($filters['intervalType'] ?? '') === 'yearly')>Jährlich</option>
                    <option value="monthly" @selected(($filters['intervalType'] ?? '') === 'monthly')>Monatlich</option>
                    <option value="custom" @selected(($filters['intervalType'] ?? '') === 'custom')>Individuell</option>
                  </select>

                  <select name="sort" class="mc-select" style="min-width: 220px;">
                    <option value="next_service_asc"  @selected(($filters['sort'] ?? '') === 'next_service_asc')>Sortierung: Nächste Wartung ↑</option>
                    <option value="next_service_desc" @selected(($filters['sort'] ?? '') === 'next_service_desc')>Nächste Wartung ↓</option>
                    <option value="start_asc"         @selected(($filters['sort'] ?? '') === 'start_asc')>Vertragsbeginn ↑</option>
                    <option value="start_desc"        @selected(($filters['sort'] ?? '') === 'start_desc')>Vertragsbeginn ↓</option>
                    <option value="created_desc"      @selected(($filters['sort'] ?? '') === 'created_desc')>Neueste zuerst</option>
                    <option value="created_asc"       @selected(($filters['sort'] ?? '') === 'created_asc')>Älteste zuerst</option>
                  </select>
                </div>

                <div style="display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap;">
                  <button type="submit" class="mc-btn mc-btn-ghost"><i class="fa fa-filter"></i><span>Filter</span></button>
                  <a href="{{ route('admin.maintenance.contracts.index') }}" class="mc-btn mc-btn-ghost"><i class="fa fa-rotate-right"></i><span>Reset</span></a>
                  <a href="{{ route('admin.maintenance.contracts.create') }}" class="mc-btn mc-btn-primary"><i class="fa fa-plus-circle"></i><span>Vertrag anlegen</span></a>
                </div>
              </div>
            </form>

            {{-- Views --}}
            <div class="mc-views">

              {{-- LIST --}}
              <div class="mc-view is-active" id="mc-view-list">
                <div class="mc-table-shell">
                  <table class="mc-table">
                    <thead>
                      <tr>
                        <th>Vertrag</th>
                        <th>Kunde</th>
                        <th>Verantwortlich</th>
                        <th>Anlage / Adresse</th>
                        <th>Intervall</th>
                        <th>Nächste Wartung</th>
                        <th>Status</th>
                        <th>Preis</th>
                        <th style="width:70px;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($contracts as $contract)
                        @php
                          $lead  = $contract->lead;
                          $alt   = $contract->alternative;
                          $asset = $contract->asset;

                          // RESPONSIBLE (Contract -> responsibleEmployee)
                          $resp = $contract->responsibleEmployee ?? null;
                          $respName = $resp?->full_name ?: $resp?->name ?: null;

                          // Kunde
                          $customerName = null;
                          if ($lead) {
                            $customerName = $lead->firma
                              ?? trim(($lead->name ?? $lead->vorname ?? '') . ' ' . ($lead->lastname ?? $lead->nachname ?? ''));
                            $customerName = trim((string)$customerName) !== '' ? $customerName : null;
                          }

                          // Adresse
                          $addressText = null;
                          if ($alt) {
                            $addressText = $alt->full_address
                              ?? trim(($alt->street ?? '') . ', ' . ($alt->postcode ?? '') . ' ' . ($alt->city ?? ''));
                            $addressText = trim((string)$addressText) !== '' ? $addressText : null;
                          }

                          if (!$addressText && $asset && is_array($asset->technical_data ?? null)) {
                            $addressText = $asset->technical_data['installationAddressText']
                              ?? ($asset->technical_data['installationLocation']['notes'] ?? null);
                            $addressText = trim((string)$addressText) !== '' ? $addressText : null;
                          }

                          if (!$addressText && $lead) {
                            $addressText = trim(($lead->street ?? '') . ', ' . ($lead->postcode ?? '') . ' ' . ($lead->city ?? ''));
                            $addressText = trim((string)$addressText) !== '' ? $addressText : null;
                          }

                          // Titel
                          $contractTitle = trim((string)($contract->title ?? ''));
                          if ($contractTitle === '' && $asset) $contractTitle = trim((string)($asset->title ?? ''));
                          if ($contractTitle === '') $contractTitle = $contract->contract_no ?? 'Wartungsvertrag';

                          // Produktlabel
                          $productParts = [];
                          if ($asset && $asset->manufacturer_attach) $productParts[] = $asset->manufacturer_attach;
                          if ($asset && $asset->manufacturer) $productParts[] = $asset->manufacturer;
                          if ($asset && $asset->model) $productParts[] = $asset->model;
                          if ($asset && $asset->title) $productParts[] = $asset->title;
                          $productLabel = trim(implode(' · ', array_filter($productParts)));

                          // Next service
                          $nextService = $contract->next_service_date ?? null;
                          if (!$nextService) $nextService = $contract->end_date ?? null;

                          $nextServiceFmt = $nextService ? \Carbon\Carbon::parse($nextService)->format('d.m.Y') : '–';
                          $status = $contract->status ?? 'draft';
                          $statusClass = 'mc-status-' . $status;

                          $daysTo = null;
                          if ($nextService) {
                            $daysTo = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($nextService)->startOfDay(), false);
                          }
                        @endphp

                        <tr
                          data-id="{{ $contract->id }}"
                          data-title="{{ e($contractTitle) }}"
                          data-customer="{{ e($customerName ?? '') }}"
                          data-responsible="{{ e($respName ?? '') }}"
                          data-address="{{ e($addressText ?? '') }}"
                          data-product="{{ e($productLabel) }}"
                          data-next-service="{{ $nextService ? \Carbon\Carbon::parse($nextService)->toDateString() : '' }}"
                          data-status="{{ e($status) }}"
                        >
                          <td>
                            <div class="mc-row-title">{{ $contractTitle }}</div>
                            <div class="mc-row-sub">
                              <span class="mc-tag"><span class="mc-dot"></span>Nr.: {{ $contract->contract_no ?? '–' }}</span>
                            </div>
                          </td>

                          <td>
                            <div class="mc-row-title">{{ $customerName ?? '–' }}</div>
                            @if($lead && ($lead->customer_no ?? null))
                              <div class="mc-row-sub">Kundennr.: {{ $lead->customer_no }}</div>
                            @endif
                          </td>

                          <td>
                            @if($respName)
                              <div class="mc-row-title">{{ $respName }}</div>
                              <div class="mc-row-sub">Mitarbeiter</div>
                            @else
                              <span class="mc-row-sub">–</span>
                            @endif
                          </td>

                          <td>
                            @if($addressText)
                              <div class="mc-row-sub">{{ $addressText }}</div>
                            @endif
                            @if($productLabel)
                              <div class="mc-row-sub"><strong>Produkt:</strong> {{ $productLabel }}</div>
                            @endif
                          </td>

                          <td>
                            <span class="mc-tag">
                              {{ $contract->interval_type ?? 'yearly' }}
                              @if($contract->interval_months) · {{ $contract->interval_months }} Mon. @endif
                            </span>
                          </td>

                          <td>
                            <div class="mc-row-title">{{ $nextServiceFmt }}</div>
                            @if(!is_null($daysTo))
                              <div class="mc-row-sub">
                                @if($daysTo < 0)
                                  <span class="mw-badge is-overdue"><span class="mw-dot"></span>Überfällig ({{ abs($daysTo) }} Tage)</span>
                                @elseif($daysTo <= 14)
                                  <span class="mw-badge is-soon"><span class="mw-dot"></span>Bald ({{ $daysTo }} Tage)</span>
                                @else
                                  <span class="mw-badge"><span class="mw-dot" style="background: var(--mc-success);"></span>Geplant</span>
                                @endif
                              </div>
                            @endif
                          </td>

                          <td>
                            <span class="mc-status-pill {{ $statusClass }}"><i class="fa fa-circle"></i>{{ $status }}</span>
                          </td>

                          <td>
                            @if(!is_null($contract->price))
                              {{ number_format($contract->price, 2, ',', '.') }} {{ $contract->currency ?? 'EUR' }}
                            @else
                              <span class="mc-row-sub">–</span>
                            @endif
                          </td>

                          <td style="text-align:right;">
                            <a href="{{ route('admin.maintenance.contracts.show', $contract->id) }}" class="mc-btn" style="padding:4px 0;">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.maintenance.contracts.edit', $contract->id) }}" class="mc-btn" style="padding:4px 0;">
                              <i class="fa fa-pen"></i>
                            </a>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="9" style="text-align:center; padding:20px 0; color: var(--mc-muted);">
                            Keine Verträge gefunden.
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>

                  <div class="mc-pagination">
                    <div>
                      @if($contracts->total() > 0)
                        Zeige {{ $contracts->firstItem() }}–{{ $contracts->lastItem() }} von {{ $contracts->total() }}
                      @endif
                    </div>
                    <div>{{ $contracts->links('pagination::bootstrap-4') }}</div>
                  </div>
                </div>
              </div>

              {{-- CALENDAR --}}
              <div class="mc-view" id="mc-view-calendar">
                <div class="mc-calendar-grid">
                  <div class="mc-card">
                    <div class="mc-card-hd">
                      <strong>Kalenderansicht</strong>
                      <span class="mc-row-sub">Nächste Wartungen (FullCalendar)</span>
                    </div>
                    <div class="mc-card-bd">
                      <div id="mw-calendar" style="min-height: 720px;"></div>
                    </div>
                  </div>

                  <div class="mc-card">
                    <div class="mc-card-hd">
                      <strong>Incoming Wartung</strong>
                      <span class="mc-row-sub">nächste 30 Tage</span>
                    </div>
                    <div class="mc-card-bd" id="mw-upcoming-list">
                      <div class="mc-row-sub">Lade…</div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- KANBAN --}}
              <div class="mc-view" id="mc-view-kanban">
                <div class="mc-card">
                  <div class="mc-card-hd">
                    <strong>Kanban</strong>
                    <span class="mc-row-sub">Drag &amp; Drop Status</span>
                  </div>
                  <div class="mc-card-bd">
                    <div style="color: var(--mc-muted); font-size:0.78rem;">
                      Kanban-Ansicht unverändert lassen oder deinen bisherigen Kanban-Code hier einsetzen.
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Near-wartung modal --}}
<div class="mw-modal" id="mw-near-modal" aria-hidden="true">
  <div class="mw-modal-card">
    <div class="mw-modal-hd">
      <strong id="mw-near-modal-title">Wartung</strong>
      <button type="button" class="mw-close" id="mw-near-modal-close">Schließen</button>
    </div>
    <div class="mw-modal-bd" id="mw-near-modal-body"></div>
  </div>
</div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <script>
  (function () {
    "use strict";

    const NEAR_DAYS = 14;
    const UPCOMING_DAYS = 30;

    const viewToggle = document.getElementById("mc-view-toggle");
    const viewList   = document.getElementById("mc-view-list");
    const viewCal    = document.getElementById("mc-view-calendar");
    const viewKanban = document.getElementById("mc-view-kanban");

    function setView(view) {
      if (viewToggle) {
        viewToggle.querySelectorAll('button[data-view]').forEach(b => {
          b.classList.toggle("is-active", b.getAttribute("data-view") === view);
        });
      }
      viewList.classList.toggle("is-active", view === "list");
      viewCal.classList.toggle("is-active", view === "calendar");
      viewKanban.classList.toggle("is-active", view === "kanban");
      if (view === "calendar") initCalendarOnce();
    }

    if (viewToggle) {
      viewToggle.addEventListener("click", (e) => {
        const btn = e.target.closest('button[data-view]');
        if (!btn) return;
        setView(btn.getAttribute("data-view"));
      });
    }

    function readContractsFromTable() {
      const rows = Array.from(document.querySelectorAll("#mc-view-list tbody tr[data-id]"));
      return rows.map(row => {
        const id = row.getAttribute("data-id");
        const title = row.getAttribute("data-title") || "Wartung";
        const customer = row.getAttribute("data-customer") || "";
        const responsible = row.getAttribute("data-responsible") || "";
        const address = row.getAttribute("data-address") || "";
        const product = row.getAttribute("data-product") || "";
        const status = row.getAttribute("data-status") || "draft";
        const dateStr = row.getAttribute("data-next-service") || "";
        const date = dateStr ? new Date(dateStr + "T00:00:00") : null;
        return { id, title, customer, responsible, address, product, status, dateStr, date };
      }).filter(x => !!x.dateStr);
    }

    function daysDiffFromToday(dateStr) {
      if (!dateStr) return null;
      const today = new Date(); today.setHours(0,0,0,0);
      const d = new Date(dateStr + "T00:00:00"); d.setHours(0,0,0,0);
      return Math.round((d.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    }

    function escapeHtml(s) {
      return String(s || "")
        .replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
        .replace(/"/g,"&quot;").replace(/'/g,"&#039;");
    }

    function formatDE(iso) {
      if (!iso) return "–";
      const d = new Date(iso + "T00:00:00");
      return String(d.getDate()).padStart(2,"0") + "." + String(d.getMonth()+1).padStart(2,"0") + "." + d.getFullYear();
    }

    function renderUpcomingSidebar() {
      const box = document.getElementById("mw-upcoming-list");
      if (!box) return;

      const items = readContractsFromTable()
        .map(x => ({...x, daysTo: daysDiffFromToday(x.dateStr)}))
        .filter(x => x.daysTo !== null && x.daysTo <= UPCOMING_DAYS)
        .sort((a,b) => (a.daysTo - b.daysTo));

      if (!items.length) {
        box.innerHTML = '<div class="mc-row-sub">Keine anstehenden Wartungen in den nächsten ' + UPCOMING_DAYS + ' Tagen.</div>';
        return;
      }

      box.innerHTML = items.map(item => {
        const badgeClass = item.daysTo < 0 ? "is-overdue" : (item.daysTo <= NEAR_DAYS ? "is-soon" : "");
        const badgeText  = item.daysTo < 0 ? ("Überfällig (" + Math.abs(item.daysTo) + " Tage)") : ("In " + item.daysTo + " Tagen");
        return (
          '<div class="mw-upcoming-item" data-open-contract="' + item.id + '">' +
            '<div class="mw-up-title">' + escapeHtml(item.title) + '</div>' +
            '<div class="mw-up-meta">' +
              (item.customer ? ('<div><strong>Kunde:</strong> ' + escapeHtml(item.customer) + '</div>') : '') +
              (item.responsible ? ('<div><strong>Verantwortlich:</strong> ' + escapeHtml(item.responsible) + '</div>') : '') +
              (item.address  ? ('<div><strong>Adresse:</strong> ' + escapeHtml(item.address) + '</div>') : '') +
              (item.product  ? ('<div><strong>Produkt:</strong> ' + escapeHtml(item.product) + '</div>') : '') +
              '<div><strong>Datum:</strong> ' + formatDE(item.dateStr) + '</div>' +
            '</div>' +
            '<div class="mw-badge ' + badgeClass + '"><span class="mw-dot"></span>' + badgeText + '</div>' +
          '</div>'
        );
      }).join("");

      box.querySelectorAll("[data-open-contract]").forEach(el => {
        el.addEventListener("click", () => {
          const id = el.getAttribute("data-open-contract");
          window.location.href = "{{ url('/admin/maintenance/contracts') }}/" + id;
        });
      });
    }

    const modal = document.getElementById("mw-near-modal");
    const modalTitle = document.getElementById("mw-near-modal-title");
    const modalBody  = document.getElementById("mw-near-modal-body");
    const modalClose = document.getElementById("mw-near-modal-close");

    function openModal(item) {
      if (!modal) return;
      modalTitle.textContent = item.title || "Wartung";
      modalBody.innerHTML =
        '<div class="mw-modal-row"><div class="mw-k">Datum</div><div class="mw-v">' + formatDE(item.dateStr) + '</div></div>' +
        '<div class="mw-modal-row"><div class="mw-k">Kunde</div><div class="mw-v">' + escapeHtml(item.customer || "–") + '</div></div>' +
        '<div class="mw-modal-row"><div class="mw-k">Verantwortlich</div><div class="mw-v">' + escapeHtml(item.responsible || "–") + '</div></div>' +
        '<div class="mw-modal-row"><div class="mw-k">Adresse</div><div class="mw-v">' + escapeHtml(item.address || "–") + '</div></div>' +
        '<div class="mw-modal-row"><div class="mw-k">Produkt</div><div class="mw-v">' + escapeHtml(item.product || "–") + '</div></div>' +
        '<div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">' +
          '<a class="mc-btn mc-btn-primary" href="{{ url('/admin/maintenance/contracts') }}/' + item.id + '"><i class="fa fa-eye"></i><span>Öffnen</span></a>' +
          '<button type="button" class="mc-btn mc-btn-ghost" id="mw-near-modal-dismiss"><i class="fa fa-bell-slash"></i><span>Heute nicht mehr anzeigen</span></button>' +
        '</div>';

      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");

      const dismiss = document.getElementById("mw-near-modal-dismiss");
      if (dismiss) {
        dismiss.addEventListener("click", () => {
          rememberDismiss(item.id);
          closeModal();
        }, { once: true });
      }
    }

    function closeModal() {
      if (!modal) return;
      modal.classList.remove("is-open");
      modal.setAttribute("aria-hidden", "true");
    }

    if (modalClose) modalClose.addEventListener("click", closeModal);
    if (modal) modal.addEventListener("click", (e) => { if (e.target === modal) closeModal(); });

    function rememberDismiss(contractId) {
      try {
        const key = "mw_wartung_dismissed_" + contractId;
        const today = new Date();
        const stamp = today.getFullYear() + "-" + String(today.getMonth()+1).padStart(2,"0") + "-" + String(today.getDate()).padStart(2,"0");
        localStorage.setItem(key, stamp);
      } catch(e) {}
    }

    function isDismissedToday(contractId) {
      try {
        const key = "mw_wartung_dismissed_" + contractId;
        const v = localStorage.getItem(key);
        if (!v) return false;
        const today = new Date();
        const stamp = today.getFullYear() + "-" + String(today.getMonth()+1).padStart(2,"0") + "-" + String(today.getDate()).padStart(2,"0");
        return v === stamp;
      } catch(e) {
        return false;
      }
    }

    function emitNearEvent(item, daysTo) {
      const ev = new CustomEvent("mw:wartung-near", { detail: { item, daysTo } });
      window.dispatchEvent(ev);
    }

    window.addEventListener("mw:wartung-near", (e) => {
      const item = e.detail && e.detail.item ? e.detail.item : null;
      const daysTo = e.detail && typeof e.detail.daysTo === "number" ? e.detail.daysTo : null;
      if (!item || daysTo === null) return;
      if (window.__mwNearModalShown) return;
      window.__mwNearModalShown = true;
      openModal(item);
    });

    let calendarInited = false;
    function initCalendarOnce() {
      if (calendarInited) return;
      calendarInited = true;

      const el = document.getElementById("mw-calendar");
      if (!el || !window.FullCalendar) return;

      const items = readContractsFromTable();

      const events = items.map(item => ({
        id: String(item.id),
        title: item.title,
        start: item.dateStr,
        allDay: true,
        extendedProps: {
          customer: item.customer,
          responsible: item.responsible,
          address: item.address,
          product: item.product,
          status: item.status,
        }
      }));

      const calendar = new FullCalendar.Calendar(el, {
        initialView: "dayGridMonth",
        height: "auto",
        locale: "de",
        firstDay: 1,
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,listWeek"
        },
        events: events,
        eventClick: function(info) {
          const id = info.event.id;
          window.location.href = "{{ url('/admin/maintenance/contracts') }}/" + id;
        },
        eventDidMount: function(info) {
          const p = info.event.extendedProps || {};
          const lines = [];
          if (p.customer) lines.push("Kunde: " + p.customer);
          if (p.responsible) lines.push("Verantwortlich: " + p.responsible);
          if (p.address)  lines.push("Adresse: " + p.address);
          if (p.product)  lines.push("Produkt: " + p.product);
          if (p.status)   lines.push("Status: " + p.status);
          info.el.title = lines.join("\n");
        }
      });

      calendar.render();
    }

    function checkNearWartung() {
      const items = readContractsFromTable()
        .map(x => ({...x, daysTo: daysDiffFromToday(x.dateStr)}))
        .filter(x => x.daysTo !== null);

      items.sort((a,b) => a.daysTo - b.daysTo);

      const candidate = items.find(x => (x.daysTo <= NEAR_DAYS));
      if (!candidate) return;
      if (isDismissedToday(candidate.id)) return;

      emitNearEvent(candidate, candidate.daysTo);
    }

    document.addEventListener("DOMContentLoaded", function () {
      renderUpcomingSidebar();
      checkNearWartung();

      const url = new URL(window.location.href);
      const view = url.searchParams.get("view");
      if (view === "calendar" || view === "kanban" || view === "list") {
        setView(view);
      }
    });
  })();
  </script>
@endsection
