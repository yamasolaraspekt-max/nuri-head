{{-- resources/views/admin/maintenance/contracts/show.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Wartungsvertrag anzeigen')

@php
$lead = $contract->lead ?? null;
$alt = $contract->alternative ?? null;
$asset = $contract->asset ?? null;

$customerName = null;
if ($lead) {
  $customerName = $lead->firma ?? trim(($lead->vorname ?? $lead->name ?? '') . ' ' . ($lead->nachname ?? $lead->lastname ?? ''));
  $customerName = trim((string) $customerName) !== '' ? $customerName : null;
}

$addressText = null;
if ($alt) {
  $addressText = $alt->full_address
    ?? trim(($alt->street ?? '') . ', ' . ($alt->postcode ?? '') . ' ' . ($alt->city ?? ''));
  $addressText = trim((string) $addressText) !== '' ? $addressText : null;
}

if (!$addressText && $asset && is_array($asset->technical_data ?? null)) {
  $loc = $asset->technical_data['installationLocation'] ?? null;
  if (is_array($loc)) {
    $addressText = $loc['addressText'] ?? $loc['notes'] ?? null;
  }
}

if (!$addressText && $lead) {
  $addressText = trim(($lead->street ?? '') . ', ' . ($lead->postcode ?? '') . ' ' . ($lead->city ?? ''));
  $addressText = trim((string) $addressText) !== '' ? $addressText : null;
}

$productParts = [];
if ($asset && !empty($asset->manufacturer))
  $productParts[] = $asset->manufacturer;
if ($asset && !empty($asset->model))
  $productParts[] = $asset->model;
if ($asset && !empty($asset->title))
  $productParts[] = $asset->title;
$productLabel = implode(' · ', array_filter($productParts));

$status = $contract->status ?? 'draft';

$statusClass = match ($status) {
  'active' => 'green',
  'inactive' => 'orange',
  'cancelled' => 'red',
  default => 'gray',
};

$statusLabel = match ($status) {
  'active' => 'Aktiv',
  'inactive' => 'Inaktiv',
  'cancelled' => 'Gekündigt',
  default => 'Entwurf',
};

$payload = $contract->payload ?? null;
if (is_string($payload)) {
  $tmp = json_decode($payload, true);
  if (json_last_error() === JSON_ERROR_NONE) {
    $payload = $tmp;
  }
}

$historyEntries = [];
if (is_array($payload)) {
  $h = $payload['history'] ?? $payload['maintenanceHistory'] ?? ($payload['meta']['history'] ?? null);
  if (is_array($h) && isset($h['entries']) && is_array($h['entries'])) {
    $historyEntries = $h['entries'];
  } elseif (is_array($h) && array_is_list($h)) {
    $historyEntries = $h;
  }
}

$protocols = $contract->maintenanceProtocols ?? null;

$assetAgeText = null;
$totalCount = null;
$lastDate = null;
$avgDays = null;

if (is_array($payload)) {
  $h = $payload['history'] ?? null;
  if (is_array($h)) {
    $assetAgeText = $h['assetAgeText'] ?? null;
    $totalCount = $h['totalMaintenanceCount'] ?? null;
    $lastDate = $h['lastMaintenanceDate'] ?? null;
    $avgDays = $h['averageIntervalDays'] ?? null;
  }
}
@endphp

@once
@push('style')
<style>
  :root {
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

  .oc-wrap {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    color: var(--text-main); 
  }

  .oc-header{margin-bottom:18px;}
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
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .oc-btn-ic.warning:hover{border-color:#f59e0b}
  .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .oc-btn-ic.success:hover{border-color:var(--success)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

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
  .oc-stat-icon.status{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.next{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.history{background:var(--gray-light);color:var(--gray)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

  .oc-grid{
    display:grid;
    grid-template-columns:1.2fr .8fr;
    gap:16px;
  }
  @media(max-width:1100px){ .oc-grid{grid-template-columns:1fr;} }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-card-head{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    flex-wrap:wrap;
  }

  .oc-card-head-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:16px;
    font-weight:900;
    color:#111827;
  }

  .oc-card-body{
    padding:18px;
  }

  .oc-card-stack{
    display:flex;
    flex-direction:column;
    gap:16px;
  }

  .oc-kv{
    display:grid;
    grid-template-columns:180px 1fr;
    gap:10px 14px;
    font-size:14px;
  }
  @media(max-width:640px){ .oc-kv{grid-template-columns:1fr;} }

  .oc-k{
    color:var(--text-muted);
    font-weight:800;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .oc-v{
    color:#111827;
    font-weight:700;
    line-height:1.5;
  }
  .oc-v small{color:var(--text-muted);font-weight:700;}

  .oc-status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
    gap:7px;
  }
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-status-pill.red{background:#fef2f2;color:#b91c1c;}
  .oc-status-pill.gray{background:#f3f4f6;color:#4b5563;}

  .oc-chip-row{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 10px;
    border-radius:999px;
    background:var(--blue-light);
    color:var(--blue);
    font-size:12px;
    font-weight:800;
    border:1px solid rgba(116,178,212,.18);
  }

  .oc-history-list{
    display:flex;
    flex-direction:column;
    gap:12px;
  }

  .oc-history-item{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
  }

  .oc-history-left{
    display:flex;
    gap:12px;
    align-items:flex-start;
    min-width:0;
  }

  .oc-history-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    background:var(--blue-light);
    color:var(--blue);
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-history-main{min-width:0}
  .oc-history-title{
    margin:0;
    font-size:14px;
    font-weight:900;
    color:#111827;
  }
  .oc-history-meta{
    margin:4px 0 0;
    font-size:12px;
    color:var(--text-muted);
    line-height:1.45;
  }
  .oc-history-note{
    margin:8px 0 0;
    font-size:13px;
    color:#111827;
    white-space:pre-wrap;
    line-height:1.5;
  }
  .oc-history-right{
    font-size:12px;
    color:var(--text-muted);
    text-align:right;
    min-width:120px;
    font-weight:800;
  }

  .oc-empty{
    text-align:center;
    padding:36px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
  }

  .oc-note-box{
    padding:14px;
    border:1px solid var(--border);
    border-radius:14px;
    background:#fff;
    line-height:1.65;
    color:#111827;
    white-space:pre-wrap;
    font-size:14px;
  }

  .oc-divider{
    height:1px;
    background:var(--border);
    margin:14px 0;
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">WARTUNGSVERTRAG {{ $contract->contract_no ?? '–' }}</div>
        <div class="oc-sub">Detailansicht für Vertragsdaten, Kundenbezug, Anlage und Wartungshistorie.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ route('admin.maintenance.contracts.index') }}">Wartungsverträge</a>
          <span>›</span>
          <span class="current">Vertrag anzeigen</span>
        </div>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @if(\Route::has('admin.maintenance.contracts.edit'))
          <a href="{{ route('admin.maintenance.contracts.edit', $contract->id) }}" class="oc-btn">
            <i class="fa-solid fa-pen"></i>
            Bearbeiten
          </a>
        @endif

        <a href="{{ route('admin.maintenance.contracts.index') }}" class="oc-btn-soft">
          <i class="fa-solid fa-arrow-left"></i>
          Zurück
        </a>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <i class="fa-solid fa-file-contract"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Vertragsnummer</div>
        <div class="oc-stat-value" style="font-size:20px;">{{ $contract->contract_no ?? '–' }}</div>
        <div class="oc-stat-sub">{{ $contract->title ?? 'Ohne Titel' }}</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon status">
        <i class="fa-solid fa-traffic-light"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Status</div>
        <div class="oc-stat-value" style="font-size:20px;">{{ $statusLabel }}</div>
        <div class="oc-stat-sub">{{ $customerName ?? 'Kein Kunde' }}</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon next">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Nächste Wartung</div>
        <div class="oc-stat-value" style="font-size:20px;">
          {{ $contract->next_service_date ? \Carbon\Carbon::parse($contract->next_service_date)->format('d.m.Y') : '–' }}
        </div>
        <div class="oc-stat-sub">{{ $contract->interval_type ?? 'yearly' }} @if($contract->interval_months) · {{ $contract->interval_months }} Monate @endif</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon history">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Historie</div>
        <div class="oc-stat-value">
          {{ ($protocols && $protocols instanceof \Illuminate\Support\Collection) ? $protocols->count() : count($historyEntries) }}
        </div>
        <div class="oc-stat-sub">Wartungseinträge verfügbar</div>
      </div>
    </div>
  </div>

  <div class="oc-grid">
    <div class="oc-card-stack">
      <div class="oc-card">
        <div class="oc-card-head">
          <div class="oc-card-head-title">
            <i class="fa-solid fa-circle-info"></i>
            <span>Stammdaten</span>
          </div>

          <span class="oc-status-pill {{ $statusClass }}">
            <i class="fa-solid fa-circle"></i>
            {{ $statusLabel }}
          </span>
        </div>

        <div class="oc-card-body">
          <div class="oc-kv">
            <div class="oc-k"><i class="fa-solid fa-hashtag"></i> Vertragsnummer</div>
            <div class="oc-v">{{ $contract->contract_no ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-heading"></i> Titel</div>
            <div class="oc-v">{{ $contract->title ?? 'Ohne Titel' }}</div>

            <div class="oc-k"><i class="fa-regular fa-calendar"></i> Zeitraum</div>
            <div class="oc-v">
              {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d.m.Y') : '–' }}
              <small>bis</small>
              {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d.m.Y') : '–' }}
            </div>

            <div class="oc-k"><i class="fa-solid fa-repeat"></i> Intervall</div>
            <div class="oc-v">
              {{ $contract->interval_type ?? 'yearly' }}
              @if($contract->interval_months)
                <small>·</small> {{ $contract->interval_months }} Monate
              @endif
            </div>

            <div class="oc-k"><i class="fa-solid fa-euro-sign"></i> Preis</div>
            <div class="oc-v">
              @if(!is_null($contract->price))
                {{ number_format($contract->price, 2, ',', '.') }} {{ $contract->currency ?? 'EUR' }}
              @else
                –
              @endif
            </div>

            <div class="oc-k"><i class="fa-solid fa-file-invoice"></i> Vertragstyp</div>
            <div class="oc-v">{{ $contract->contract_type ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-receipt"></i> Abrechnung</div>
            <div class="oc-v">{{ $contract->billing_mode ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-calendar-day"></i> Nächste Wartung</div>
            <div class="oc-v">
              {{ $contract->next_service_date ? \Carbon\Carbon::parse($contract->next_service_date)->format('d.m.Y') : '–' }}
            </div>
          </div>
        </div>
      </div>

      <div class="oc-card">
        <div class="oc-card-head">
          <div class="oc-card-head-title">
            <i class="fa-solid fa-link"></i>
            <span>Kunde & Anlage</span>
          </div>
        </div>

        <div class="oc-card-body">
          <div class="oc-kv">
            <div class="oc-k"><i class="fa-solid fa-user"></i> Kunde</div>
            <div class="oc-v">{{ $customerName ?? ('Kunde #' . ($contract->lead_id ?? '–')) }}</div>

            <div class="oc-k"><i class="fa-solid fa-location-dot"></i> Adresse</div>
            <div class="oc-v">{{ $addressText ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-box-open"></i> Anlage / Produkt</div>
            <div class="oc-v">{{ $productLabel ?: ($asset->title ?? '–') }}</div>

            <div class="oc-k"><i class="fa-solid fa-fingerprint"></i> Seriennummer</div>
            <div class="oc-v">{{ $asset->serial_no ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-id-badge"></i> Anlage-Nr.</div>
            <div class="oc-v">{{ $asset->asset_no ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-network-wired"></i> Zugehörige IDs</div>
            <div class="oc-v">
              <div class="oc-chip-row">
                <span class="oc-chip">Lead: {{ $contract->lead_id ?? '–' }}</span>
                <span class="oc-chip">Objekt: {{ $contract->alternative_id ?? '–' }}</span>
                <span class="oc-chip">Anlage: {{ $contract->asset_id ?? '–' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      @if($contract->description)
        <div class="oc-card">
          <div class="oc-card-head">
            <div class="oc-card-head-title">
              <i class="fa-solid fa-clipboard-list"></i>
              <span>Beschreibung / Leistungen</span>
            </div>
          </div>

          <div class="oc-card-body">
            <div class="oc-note-box">{{ $contract->description }}</div>
          </div>
        </div>
      @endif

      @if($contract->internal_notes)
        <div class="oc-card">
          <div class="oc-card-head">
            <div class="oc-card-head-title">
              <i class="fa-solid fa-shield-halved"></i>
              <span>Interne Notizen</span>
            </div>
          </div>

          <div class="oc-card-body">
            <div class="oc-note-box">{{ $contract->internal_notes }}</div>
          </div>
        </div>
      @endif
    </div>

    <div class="oc-card-stack">
      <div class="oc-card">
        <div class="oc-card-head">
          <div class="oc-card-head-title">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Wartungshistorie</span>
          </div>

          <span class="oc-chip">
            <i class="fa-solid fa-list-check"></i>
            {{ ($protocols && $protocols instanceof \Illuminate\Support\Collection) ? $protocols->count() : count($historyEntries) }} Einträge
          </span>
        </div>

        <div class="oc-card-body">
          @php
$hasDbProtocols = $protocols && $protocols instanceof \Illuminate\Support\Collection && $protocols->isNotEmpty();
          @endphp

          @if($hasDbProtocols)
            <div class="oc-history-list">
              @foreach($protocols->sortByDesc('maintenance_date')->take(12) as $p)
                @php
    $d = $p->maintenance_date ?? $p->started_at ?? null;
    $dateLabel = $d ? \Carbon\Carbon::parse($d)->format('d.m.Y') : '–';
    $typeLabel = $p->type_label ?? $p->type ?? 'Wartung';
    $techLabel = $p->technician_name ?? null;
    $note = $p->notes ?? $p->short_description ?? $p->internal_note ?? null;
                @endphp

                <div class="oc-history-item">
                  <div class="oc-history-left">
                    <div class="oc-history-icon">
                      <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div class="oc-history-main">
                      <p class="oc-history-title">{{ $typeLabel }}</p>
                      <p class="oc-history-meta">
                        <i class="fa-solid fa-user-gear"></i>
                        {{ $techLabel ?: '–' }}
                      </p>
                      @if($note)
                        <div class="oc-history-note">{{ $note }}</div>
                      @endif
                    </div>
                  </div>

                  <div class="oc-history-right">
                    <i class="fa-regular fa-calendar"></i>
                    {{ $dateLabel }}
                  </div>
                </div>
              @endforeach
            </div>

          @elseif(!empty($historyEntries))
            <div class="oc-history-list">
              @foreach(array_slice($historyEntries, 0, 12) as $h)
                @php
    $dateLabel = $h['date'] ?? null;
    if ($dateLabel) {
      try {
        $dateLabel = \Carbon\Carbon::parse($dateLabel)->format('d.m.Y');
      } catch (\Throwable $e) {
      }
    } else {
      $dateLabel = '–';
    }

    $typeLabel = $h['type_label'] ?? $h['type'] ?? 'Wartung';
    $techLabel = $h['technician_name'] ?? $h['technician'] ?? null;
    $note = $h['notes'] ?? $h['note'] ?? null;
                @endphp

                <div class="oc-history-item">
                  <div class="oc-history-left">
                    <div class="oc-history-icon">
                      <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div class="oc-history-main">
                      <p class="oc-history-title">{{ $typeLabel }}</p>
                      <p class="oc-history-meta">
                        <i class="fa-solid fa-user-gear"></i>
                        {{ $techLabel ?: '–' }}
                      </p>
                      @if($note)
                        <div class="oc-history-note">{{ $note }}</div>
                      @endif
                    </div>
                  </div>

                  <div class="oc-history-right">
                    <i class="fa-regular fa-calendar"></i>
                    {{ $dateLabel }}
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="oc-empty">
              Keine Wartungshistorie vorhanden.
            </div>
          @endif

          @if(!empty($historyEntries) && count($historyEntries) > 12)
            <div class="oc-divider"></div>
            <div style="font-size:12px;color:#6b7280;">
              <i class="fa-solid fa-ellipsis"></i>
              Es werden nur die letzten 12 Einträge angezeigt.
            </div>
          @endif
        </div>
      </div>

      <div class="oc-card">
        <div class="oc-card-head">
          <div class="oc-card-head-title">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Quick-Infos</span>
          </div>
        </div>

        <div class="oc-card-body">
          <div class="oc-kv">
            <div class="oc-k"><i class="fa-solid fa-hourglass-half"></i> Anlagenalter</div>
            <div class="oc-v">{{ $assetAgeText ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-hammer"></i> Wartungen gesamt</div>
            <div class="oc-v">{{ $totalCount ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-calendar-day"></i> Letzte Wartung</div>
            <div class="oc-v">{{ $lastDate ?? '–' }}</div>

            <div class="oc-k"><i class="fa-solid fa-arrows-rotate"></i> Ø Intervall (Tage)</div>
            <div class="oc-v">{{ $avgDays ?? '–' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
    <script>
      window.GlobalBreadcrumbs = [
        {
          label: 'Dashboard',
          url: "{{ url('/') }}"
        },
        {
          label: 'Wartungsverträge',
          url: "{{ route('admin.maintenance.contracts.index') }}", 
        },
         {
          label: 'Vertragsnummer {{ $contract->contract_no ?? '–' }}',
          url: "{{ url()->current() }}",
          clickable: false
        }
      ];

      if (window.setGlobalBreadcrumbs) {
        window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
      }
    </script>
@endpush