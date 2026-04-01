{{-- resources/views/admin/maintenance/contracts/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Wartungsvertrag anzeigen')

@section('style')
<style>
  :root{
    --mc-bg:#f0f0f0;
    --mc-shell-bg:#f0f0f0;
    --mc-card:#0b1120;
    --mc-card-soft:#020617;
    --mc-border:rgba(148,163,184,.35);
    --mc-border-soft:rgba(148,163,184,.18);
    --mc-text:#e5e7eb;
    --mc-muted:#424242;
    --mc-accent:#74b2d4;
    --mc-success:#93c21c;
    --mc-danger:#f97373;
    --mc-warning:#fbbf24;
    --mc-radius-lg:18px;
    --mc-radius-xl:22px;
    --mc-shadow:0 22px 70px rgba(15,23,42,.25);
  }

  .mc-page{padding:18px 12px 30px;background:var(--mc-shell-bg);min-height:calc(100vh - 80px);}
  .mc-container{max-width:100%;margin:0 auto;}
  .mc-shell{border-radius:var(--mc-radius-xl);padding:16px 18px 18px;color:#111827;}

  .mc-top{
    display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;
    margin-bottom:14px;
  }
  .mc-title{
    font-size:1.15rem;font-weight:700;letter-spacing:-.02em;margin:0;color:#111827;
    display:flex;align-items:center;gap:10px;
  }
  .mc-sub{margin:4px 0 0;font-size:.8rem;color:#6b7280;}
  .mc-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:flex-end;}
  .mc-btn{
    border-radius:999px;border:1px solid transparent;padding:7px 12px;font-size:.78rem;
    display:inline-flex;gap:8px;align-items:center;cursor:pointer;white-space:nowrap;
    text-decoration:none;
  }
  .mc-btn-ghost{border-color:var(--mc-border);background:var(--mc-accent);color:#111827;}
  .mc-btn-muted{border-color:rgba(17,24,39,.12);background:#fff;color:#111827;}
  .mc-btn-danger{border-color:rgba(248,113,113,.45);background:rgba(248,113,113,.12);color:#7f1d1d;}

  .mc-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:12px;}
  @media(max-width: 992px){.mc-grid{grid-template-columns:1fr;}}

  .mc-card{
    background:#fff;border-radius:16px;border:1px solid rgba(17,24,39,.10);
    box-shadow:0 12px 35px rgba(15,23,42,.06);
    overflow:hidden;
  }
  .mc-card-h{
    padding:12px 14px;border-bottom:1px solid rgba(17,24,39,.08);
    display:flex;align-items:center;justify-content:space-between;gap:10px;
  }
  .mc-card-h h4{margin:0;font-size:.85rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#111827;}
  .mc-card-b{padding:12px 14px;}

  .mc-kv{display:grid;grid-template-columns:160px 1fr;gap:8px 10px;font-size:.82rem;}
  @media(max-width: 480px){.mc-kv{grid-template-columns:1fr;}}
  .mc-k{color:#6b7280;font-weight:600;display:flex;align-items:center;gap:8px;}
  .mc-v{color:#111827;font-weight:600;}
  .mc-v small{font-weight:600;color:#6b7280;}

  .mc-pill{
    border-radius:999px;padding:3px 9px;font-size:.72rem;font-weight:800;
    letter-spacing:.06em;text-transform:uppercase;display:inline-flex;align-items:center;gap:6px;
    border:1px solid rgba(17,24,39,.12);
  }
  .mc-pill-draft{background:rgba(148,163,184,.20);color:#0b1120;}
  .mc-pill-active{background:rgba(147,194,28,.22);color:#2f4a00;border-color:rgba(147,194,28,.45);}
  .mc-pill-inactive{background:rgba(251,191,36,.22);color:#7c5a00;border-color:rgba(251,191,36,.45);}
  .mc-pill-cancelled{background:rgba(248,113,113,.20);color:#7f1d1d;border-color:rgba(248,113,113,.40);}

  .mc-list{display:flex;flex-direction:column;gap:10px;}
  .mc-history-item{
    border-radius:14px;border:1px solid rgba(17,24,39,.10);background:#fff;padding:10px 12px;
    display:flex;gap:10px;align-items:flex-start;justify-content:space-between;
  }
  .mc-history-left{display:flex;gap:10px;align-items:flex-start;}
  .mc-ic{
    width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;
    background:rgba(116,178,212,.22);color:#0b1120;border:1px solid rgba(116,178,212,.40);
    flex:0 0 auto;
  }
  .mc-history-title{font-weight:800;color:#111827;font-size:.82rem;margin:0;}
  .mc-history-meta{font-size:.76rem;color:#6b7280;margin:2px 0 0;}
  .mc-history-note{margin:6px 0 0;font-size:.78rem;color:#111827;white-space:pre-wrap;}
  .mc-history-right{font-size:.74rem;color:#6b7280;text-align:right;min-width:120px;}

  .mc-empty{
    border-radius:14px;border:1px dashed rgba(17,24,39,.22);background:rgba(255,255,255,.7);
    padding:14px;color:#6b7280;font-size:.82rem;
  }

  .mc-divider{height:1px;background:rgba(17,24,39,.08);margin:12px 0;}
</style>
@endsection

@section('content')
@php
  $lead  = $contract->lead ?? null;
  $alt   = $contract->alternative ?? null;
  $asset = $contract->asset ?? null;

  // Kunde
  $customerName = null;
  if ($lead) {
    $customerName = $lead->firma ?? trim(($lead->vorname ?? $lead->name ?? '').' '.($lead->nachname ?? $lead->lastname ?? ''));
    $customerName = trim((string)$customerName) !== '' ? $customerName : null;
  }

  // Adresse (prio: alternative.full_address, else asset technical_data)
  $addressText = null;
  if ($alt) {
    $addressText = $alt->full_address
      ?? trim(($alt->street ?? '').', '.($alt->postcode ?? '').' '.($alt->city ?? ''));
    $addressText = trim((string)$addressText) !== '' ? $addressText : null;
  }
  if (!$addressText && $asset && is_array($asset->technical_data ?? null)) {
    $loc = $asset->technical_data['installationLocation'] ?? null;
    if (is_array($loc)) {
      $addressText = $loc['addressText'] ?? null;
    }
  }

  // Produktanzeige
  $productParts = [];
  if ($asset && !empty($asset->manufacturer)) $productParts[] = $asset->manufacturer;
  if ($asset && !empty($asset->model))        $productParts[] = $asset->model;
  if ($asset && !empty($asset->title))        $productParts[] = $asset->title;
  $productLabel = implode(' · ', array_filter($productParts));

  // Status pill
  $status = $contract->status ?? 'draft';
  $statusPill = match($status){
    'active' => 'mc-pill-active',
    'inactive' => 'mc-pill-inactive',
    'cancelled' => 'mc-pill-cancelled',
    default => 'mc-pill-draft'
  };

  // Wartungshistorie: bevorzugt aus payload/meta/history, sonst Relation (falls du später nachziehst)
  $historyEntries = [];

  $payload = $contract->payload ?? null;
  if (is_string($payload)) {
    $tmp = json_decode($payload, true);
    if (json_last_error() === JSON_ERROR_NONE) $payload = $tmp;
  }
  if (is_array($payload)) {
    $h = $payload['history'] ?? $payload['maintenanceHistory'] ?? ($payload['meta']['history'] ?? null);
    if (is_array($h) && isset($h['entries']) && is_array($h['entries'])) {
      $historyEntries = $h['entries'];
    } elseif (is_array($h) && array_is_list($h)) {
      $historyEntries = $h;
    }
  }

  // optional: wenn du im Controller später $contract->maintenanceProtocols lädst
  $protocols = $contract->maintenanceProtocols ?? null;
@endphp

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="col-12">
        <h2 class="content-header-title float-left mb-0">
          Wartungsvertrag {{ $contract->contract_no ?? '–' }}
        </h2>
        <div class="breadcrumb-wrapper col-12">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.maintenance.contracts.index') }}">Wartungsverträge</a></li>
            <li class="breadcrumb-item active">Vertrag anzeigen</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="mc-page">
        <div class="mc-container">
          <div class="mc-shell">

            {{-- Top header --}}
            <div class="mc-top">
              <div>
                <h3 class="mc-title">
                  <i class="fa-solid fa-file-contract"></i>
                  <span>{{ $contract->contract_no ?? '–' }}</span>
                  <span class="mc-pill {{ $statusPill }}">
                    <i class="fa-solid fa-circle"></i>
                    {{ $status }}
                  </span>
                </h3>
                <p class="mc-sub">
                  <i class="fa-regular fa-pen-to-square"></i>
                  {{ $contract->title ?? 'Ohne Titel' }}
                </p>

                @if($customerName || $addressText || $productLabel)
                  <p class="mc-sub" style="margin-top:6px;">
                    @if($customerName)
                      <span style="margin-right:10px;"><i class="fa-solid fa-user"></i> {{ $customerName }}</span>
                    @endif
                    @if($addressText)
                      <span style="margin-right:10px;"><i class="fa-solid fa-location-dot"></i> {{ $addressText }}</span>
                    @endif
                    @if($productLabel)
                      <span><i class="fa-solid fa-box-open"></i> {{ $productLabel }}</span>
                    @endif
                  </p>
                @endif
              </div>

              <div class="mc-actions">
                @if(\Route::has('admin.maintenance.contracts.edit'))
                  <a href="{{ route('admin.maintenance.contracts.edit', $contract->id) }}" class="mc-btn mc-btn-ghost">
                    <i class="fa-solid fa-pen"></i>
                    Bearbeiten
                  </a>
                @endif

                <a href="{{ route('admin.maintenance.contracts.index') }}" class="mc-btn mc-btn-muted">
                  <i class="fa-solid fa-arrow-left"></i>
                  Zurück
                </a>
              </div>
            </div>

            <div class="mc-grid">

              {{-- LEFT: Details --}}
              <div class="mc-list">

                {{-- Stammdaten --}}
                <div class="mc-card">
                  <div class="mc-card-h">
                    <h4><i class="fa-solid fa-circle-info" style="margin-right:8px;"></i>Stammdaten</h4>
                    @if($contract->next_service_date)
                      <span class="mc-pill" style="background:rgba(251,191,36,.18);border-color:rgba(251,191,36,.35);color:#7c5a00;">
                        <i class="fa-solid fa-calendar-check"></i>
                        Nächste Wartung: {{ \Carbon\Carbon::parse($contract->next_service_date)->format('d.m.Y') }}
                      </span>
                    @endif
                  </div>
                  <div class="mc-card-b">
                    <div class="mc-kv">
                      <div class="mc-k"><i class="fa-solid fa-hashtag"></i> Vertragsnummer</div>
                      <div class="mc-v">{{ $contract->contract_no ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-heading"></i> Titel</div>
                      <div class="mc-v">{{ $contract->title ?? 'Ohne Titel' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-traffic-light"></i> Status</div>
                      <div class="mc-v"><span class="mc-pill {{ $statusPill }}"><i class="fa-solid fa-circle"></i>{{ $status }}</span></div>

                      <div class="mc-k"><i class="fa-regular fa-calendar"></i> Zeitraum</div>
                      <div class="mc-v">
                        {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d.m.Y') : '–' }}
                        <small>bis</small>
                        {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d.m.Y') : '–' }}
                      </div>

                      <div class="mc-k"><i class="fa-solid fa-repeat"></i> Intervall</div>
                      <div class="mc-v">
                        {{ $contract->interval_type ?? 'yearly' }}
                        @if($contract->interval_months)
                          <small>·</small> {{ $contract->interval_months }} Monate
                        @endif
                      </div>

                      <div class="mc-k"><i class="fa-solid fa-euro-sign"></i> Preis</div>
                      <div class="mc-v">
                        @if(!is_null($contract->price))
                          {{ number_format($contract->price, 2, ',', '.') }} {{ $contract->currency ?? 'EUR' }}
                        @else
                          –
                        @endif
                      </div>

                      <div class="mc-k"><i class="fa-solid fa-file-invoice"></i> Vertragstyp</div>
                      <div class="mc-v">{{ $contract->contract_type ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-receipt"></i> Abrechnung</div>
                      <div class="mc-v">{{ $contract->billing_mode ?? '–' }}</div>
                    </div>
                  </div>
                </div>

                {{-- Kunde / Anlage --}}
                <div class="mc-card">
                  <div class="mc-card-h">
                    <h4><i class="fa-solid fa-link" style="margin-right:8px;"></i>Kunde & Anlage</h4>
                  </div>
                  <div class="mc-card-b">
                    <div class="mc-kv">
                      <div class="mc-k"><i class="fa-solid fa-user"></i> Kunde</div>
                      <div class="mc-v">{{ $customerName ?? ('Kunde #' . ($contract->lead_id ?? '–')) }}</div>

                      <div class="mc-k"><i class="fa-solid fa-location-dot"></i> Adresse</div>
                      <div class="mc-v">{{ $addressText ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-box-open"></i> Anlage / Produkt</div>
                      <div class="mc-v">{{ $productLabel ?: ($asset->title ?? '–') }}</div>

                      <div class="mc-k"><i class="fa-solid fa-fingerprint"></i> Seriennummer</div>
                      <div class="mc-v">{{ $asset->serial_no ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-id-badge"></i> Anlage-Nr.</div>
                      <div class="mc-v">{{ $asset->asset_no ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-network-wired"></i> IDs</div>
                      <div class="mc-v">
                        <small>Lead:</small> {{ $contract->lead_id ?? '–' }}
                        <small>· Objekt:</small> {{ $contract->alternative_id ?? '–' }}
                        <small>· Anlage:</small> {{ $contract->asset_id ?? '–' }}
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Beschreibung --}}
                @if($contract->description)
                  <div class="mc-card">
                    <div class="mc-card-h">
                      <h4><i class="fa-solid fa-clipboard-list" style="margin-right:8px;"></i>Beschreibung / Leistungen</h4>
                    </div>
                    <div class="mc-card-b" style="font-size:.85rem;color:#111827;white-space:pre-wrap;">
                      {{ $contract->description }}
                    </div>
                  </div>
                @endif

                {{-- Interne Notizen --}}
                @if($contract->internal_notes)
                  <div class="mc-card">
                    <div class="mc-card-h">
                      <h4><i class="fa-solid fa-shield-halved" style="margin-right:8px;"></i>Interne Notizen</h4>
                    </div>
                    <div class="mc-card-b" style="font-size:.85rem;color:#111827;white-space:pre-wrap;">
                      {{ $contract->internal_notes }}
                    </div>
                  </div>
                @endif

              </div>

              {{-- RIGHT: Wartungshistorie --}}
              <div class="mc-list">

                <div class="mc-card">
                  <div class="mc-card-h">
                    <h4><i class="fa-solid fa-clock-rotate-left" style="margin-right:8px;"></i>Wartungshistorie</h4>
                    <span class="mc-pill" style="background:rgba(116,178,212,.18);border-color:rgba(116,178,212,.35);color:#0b1120;">
                      <i class="fa-solid fa-list-check"></i>
                      {{ (is_array($historyEntries) ? count($historyEntries) : 0) }} Einträge
                    </span>
                  </div>

                  <div class="mc-card-b">
                    @php
                      // If you later load protocols from DB, prefer them
                      $hasDbProtocols = $protocols && $protocols instanceof \Illuminate\Support\Collection && $protocols->isNotEmpty();
                    @endphp

                    @if($hasDbProtocols)
                      <div class="mc-list">
                        @foreach($protocols->sortByDesc('maintenance_date')->take(12) as $p)
                          @php
                            $d = $p->maintenance_date ?? $p->started_at ?? null;
                            $dateLabel = $d ? \Carbon\Carbon::parse($d)->format('d.m.Y') : '–';
                            $typeLabel = $p->type_label ?? $p->type ?? 'Wartung';
                            $techLabel = $p->technician_name ?? null;
                            $note      = $p->notes ?? $p->short_description ?? $p->internal_note ?? null;
                          @endphp
                          <div class="mc-history-item">
                            <div class="mc-history-left">
                              <div class="mc-ic"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                              <div>
                                <p class="mc-history-title">{{ $typeLabel }}</p>
                                <p class="mc-history-meta">
                                  @if($techLabel)
                                    <span><i class="fa-solid fa-user-gear"></i> {{ $techLabel }}</span>
                                  @else
                                    <span><i class="fa-solid fa-user-gear"></i> –</span>
                                  @endif
                                </p>
                                @if($note)
                                  <div class="mc-history-note">{{ $note }}</div>
                                @endif
                              </div>
                            </div>
                            <div class="mc-history-right">
                              <div><i class="fa-regular fa-calendar"></i> {{ $dateLabel }}</div>
                            </div>
                          </div>
                        @endforeach
                      </div>

                    @elseif(!empty($historyEntries))
                      <div class="mc-list">
                        @foreach(array_slice($historyEntries, 0, 12) as $h)
                          @php
                            $dateLabel = $h['date'] ?? null;
                            if ($dateLabel) {
                              try { $dateLabel = \Carbon\Carbon::parse($dateLabel)->format('d.m.Y'); } catch(\Throwable $e) {}
                            } else {
                              $dateLabel = '–';
                            }
                            $typeLabel = $h['type_label'] ?? $h['type'] ?? 'Wartung';
                            $techLabel = $h['technician_name'] ?? $h['technician'] ?? null;
                            $note      = $h['notes'] ?? $h['note'] ?? null;
                          @endphp

                          <div class="mc-history-item">
                            <div class="mc-history-left">
                              <div class="mc-ic"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                              <div>
                                <p class="mc-history-title">{{ $typeLabel }}</p>
                                <p class="mc-history-meta">
                                  @if($techLabel)
                                    <span><i class="fa-solid fa-user-gear"></i> {{ $techLabel }}</span>
                                  @else
                                    <span><i class="fa-solid fa-user-gear"></i> –</span>
                                  @endif
                                </p>
                                @if($note)
                                  <div class="mc-history-note">{{ $note }}</div>
                                @endif
                              </div>
                            </div>
                            <div class="mc-history-right">
                              <div><i class="fa-regular fa-calendar"></i> {{ $dateLabel }}</div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <div class="mc-empty">
                        <i class="fa-regular fa-circle-xmark" style="margin-right:6px;"></i>
                        Keine Wartungshistorie vorhanden. (Tipp: Wenn du Protokolle als Relation lädst, wird die Historie hier automatisch gefüllt.)
                      </div>
                    @endif

                    @if(!empty($historyEntries) && count($historyEntries) > 12)
                      <div class="mc-divider"></div>
                      <div style="font-size:.78rem;color:#6b7280;">
                        <i class="fa-solid fa-ellipsis"></i>
                        Es werden nur die letzten 12 Einträge angezeigt.
                      </div>
                    @endif
                  </div>
                </div>

                {{-- Quick summary (optional) --}}
                <div class="mc-card">
                  <div class="mc-card-h">
                    <h4><i class="fa-solid fa-gauge-high" style="margin-right:8px;"></i>Quick-Infos</h4>
                  </div>
                  <div class="mc-card-b">
                    @php
                      $assetAgeText = null;
                      $totalCount = null;
                      $lastDate = null;
                      $avgDays = null;

                      if (is_array($payload)) {
                        $h = $payload['history'] ?? null;
                        if (is_array($h)) {
                          $assetAgeText = $h['assetAgeText'] ?? null;
                          $totalCount   = $h['totalMaintenanceCount'] ?? null;
                          $lastDate     = $h['lastMaintenanceDate'] ?? null;
                          $avgDays      = $h['averageIntervalDays'] ?? null;
                        }
                      }
                    @endphp

                    <div class="mc-kv">
                      <div class="mc-k"><i class="fa-solid fa-hourglass-half"></i> Anlagenalter</div>
                      <div class="mc-v">{{ $assetAgeText ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-hammer"></i> Wartungen gesamt</div>
                      <div class="mc-v">{{ $totalCount ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-calendar-day"></i> Letzte Wartung</div>
                      <div class="mc-v">{{ $lastDate ?? '–' }}</div>

                      <div class="mc-k"><i class="fa-solid fa-arrows-rotate"></i> Ø Intervall (Tage)</div>
                      <div class="mc-v">{{ $avgDays ?? '–' }}</div>
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
@endsection
