@php
    $t = $totals ?? ['customers'=>0,'offers'=>0,'deals'=>0,'projects'=>0,'tickets'=>0,'products'=>0];
@endphp

<div id="neighbor-wrapper"
     @if($hasCoords)
         data-base-lat="{{ $baseLat }}"
         data-base-lng="{{ $baseLng }}"
     @endif
     data-lead-id="{{ $lead->id }}"
     data-alt-id="{{ optional($baseAlternative)->id }}"
     data-radius="{{ $radius }}"
     data-neighbors='@json($neighborsForJs)'
     data-selected-status="{{ $selectedStatus ?? '' }}"
     data-selected-product="{{ $selectedProduct ?? '' }}"
     data-map-visible="0">

    <style>
      :root {
        --app-bg: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --primary: var(--sa-accent);
        --primary-hover: #5a99bd;
        --primary-light: var(--sa-accent-light);
        --accent: #93c21c;
        --accent-hover: #7baa18;
        --accent-light: #cfe09b;
        --blue-muted: #c0d8ea;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 10px 25px -10px rgb(0 0 0 / 0.15), 0 4px 8px -4px rgb(0 0 0 / 0.08);
        --radius: 14px;
        --transition: all 0.2s ease-in-out;
      }

      .oc-wrap {
          font-family: Inter, system-ui, -apple-system, sans-serif;
          color: var(--text-main);
          max-width: 1600px;
          margin: 0 auto;
          padding: 0px;
      }

      .oc-header { margin-bottom: 24px; margin-top:30px; }
      .oc-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
      }
      .oc-title { font-size: 26px; font-weight: 800; letter-spacing: -0.025em; color: #111827; }
      .oc-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

      .oc-analytics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 10px;
        margin-bottom: 24px;
      }

      .oc-stat {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
      }
      .oc-stat:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow); }

      .oc-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 20px;
      }
      .oc-stat-icon.primary { background: var(--primary-light); color: var(--primary); }
      .oc-stat-icon.accent { background: var(--accent-light); color: var(--accent); }
      .oc-stat-icon.muted { background: #f3f4f6; color: var(--blue-muted); }

      .oc-stat-meta { min-width: 0; }
      .oc-stat-label { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
      .oc-stat-value { font-size: 26px; font-weight: 900; color: #111827; line-height: 1.1; margin-top: 4px; }

      .oc-toolbar {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: flex-end;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
      }

      .oc-filter-block { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 180px; }
      .oc-filter-block.large { flex: 2; min-width: 280px; }
      .oc-filter-block.small { flex: 0.5; min-width: 100px; }
      .oc-filter-label { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }

      .oc-input, .oc-select {
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
        width: 100%;
        color: var(--text-main);
      }
      .oc-input:focus, .oc-select:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

      .oc-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 800;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
      }
      .oc-btn:hover { background: var(--primary-hover); color: #fff; }
      .oc-btn-accent { background: var(--accent); }
      .oc-btn-accent:hover { background: var(--accent-hover); }

      .oc-map-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 24px;
        position: relative;
      }
      #neighbor-map { width: 100%; height: 650px; }

      .oc-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 24px; }
      .oc-card-header { padding: 18px 20px; border-bottom: 1px solid var(--border); font-weight: 800; font-size: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; }

      .oc-list { display: flex; flex-direction: column; gap: 0; }
      .oc-item-row {
        padding: 16px 20px;
        display: grid;
        gap: 16px;
        align-items: center;
        grid-template-columns: 50px 2fr 2fr 1.5fr 150px;
        border-bottom: 1px solid var(--border);
        transition: var(--transition);
        cursor: pointer;
      }
      .oc-item-row:hover { background: var(--primary-light); }
      .oc-item-row:last-child { border-bottom: none; }

      .oc-cell-title { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; display: none; }
      @media(max-width: 1024px) {
          .oc-item-row { grid-template-columns: 1fr; gap: 8px; }
          .oc-cell-title { display: block; }
      }

      .oc-ttl { font-weight: 800; font-size: 15px; margin-bottom: 4px; color: #111827; }
      .oc-subt { font-size: 13px; color: var(--text-muted); }

      .oc-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        background: #f3f4f6;
        color: var(--text-muted);
        margin-right: 6px;
        margin-bottom: 6px;
        border: 1px solid var(--border);
      }
      .oc-badge.primary { background: var(--primary-light); color: var(--primary-hover); border-color: var(--blue-muted); }
      .oc-badge.accent { background: var(--accent-light); color: var(--accent-hover); border-color: var(--accent); }

      .gm-style .gm-style-iw-c { border-radius: 12px; padding: 0; box-shadow: var(--shadow); border: 1px solid var(--border); }
      .gm-style .gm-style-iw-d { overflow: hidden !important; }
      .gm-style-iw-chr { display: none; }
      .custom-info-window { padding: 16px; min-width: 260px; font-family: Inter, sans-serif; }
      .custom-info-window h4 { margin: 0 0 4px 0; font-size: 16px; font-weight: 800; color: var(--text-main); }
      .custom-info-window p { margin: 0 0 12px 0; font-size: 13px; color: var(--text-muted); }
      .custom-info-window .btn { display: block; width: 100%; text-align: center; background: var(--primary); color: #fff; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px; }

      .chart-wrapper { height: 300px; width: 100%; padding: 20px; }
    </style>

    <div class="oc-wrap">
      <div class="oc-header">
        <div class="oc-titlebar">
          <div>
            <div class="oc-title">REFERENZEN & KARTE</div>
            <div class="oc-sub">
                {{ $lead->firma ?: trim($lead->lastname.' '.$lead->name) ?: 'Lead #'.$lead->id }}
                @if($baseAlternative)
                    · Objekt: {{ $baseAlternative->object_name ?? '–' }}
                @endif
            </div>
          </div>
        </div>
      </div>

      <div class="oc-analytics">
        <div class="oc-stat">
          <div class="oc-stat-icon primary"><i class="feather icon-users"></i></div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Kunden</div>
            <div class="oc-stat-value" id="kpi-customers">{{ number_format($t['customers']) }}</div>
          </div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-icon primary"><i class="feather icon-file-text"></i></div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Angebote</div>
            <div class="oc-stat-value" id="kpi-offers">{{ number_format($t['offers']) }}</div>
          </div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-icon accent"><i class="feather icon-check-circle"></i></div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Auftrag</div>
            <div class="oc-stat-value" id="kpi-deals">{{ number_format($t['deals']) }}</div>
          </div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-icon accent"><i class="feather icon-briefcase"></i></div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Projekte</div>
            <div class="oc-stat-value" id="kpi-projects">{{ number_format($t['projects']) }}</div>
          </div>
        </div>
        <div class="oc-stat">
          <div class="oc-stat-icon muted"><i class="feather icon-box"></i></div>
          <div class="oc-stat-meta">
            <div class="oc-stat-label">Produkte</div>
            <div class="oc-stat-value" id="kpi-products">{{ number_format($t['products']) }}</div>
          </div>
        </div>
      </div>

      @if(!$hasCoords)
        <div class="alert alert-warning mb-0">
            Für diesen Kunden / dieses Objekt sind keine Koordinaten hinterlegt.
        </div>
      @else
      <div class="oc-toolbar">
        <div class="oc-filter-block small">
          <label class="oc-filter-label">Radius (km)</label>
          <input type="range" id="radiusRange" min="1" max="25" step="0.5" value="{{ $radius }}" class="oc-input" style="padding:4px 8px;">
          <span id="radiusLabel" class="badge badge-light border font-weight-semibold mt-1">
            {{ number_format($radius, 1, ',', '.') }} km
          </span>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Status Filter</label>
          <select id="filter-status" class="oc-select">
            <option value="">Alle Status</option>
            <option value="offer" {{ ($selectedStatus ?? '') === 'offer' ? 'selected' : '' }}>Angebot</option>
            <option value="deal" {{ ($selectedStatus ?? '') === 'deal' ? 'selected' : '' }}>Auftrag</option>
            <option value="project" {{ ($selectedStatus ?? '') === 'project' ? 'selected' : '' }}>Projekt</option>
            <option value="archive" {{ ($selectedStatus ?? '') === 'archive' ? 'selected' : '' }}>Archiv</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Produkt Filter</label>
          <select id="filter-product" class="oc-select">
            <option value="">Alle Produkte</option>
            @foreach(($productOptions ?? []) as $product)
                <option value="{{ $product['id'] }}" {{ (string)($selectedProduct ?? '') === (string)$product['id'] ? 'selected' : '' }}>
                    {{ $product['name'] }}
                </option>
            @endforeach
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">In Liste suchen</label>
          <input type="text" id="liveSearch" class="oc-input" placeholder="Kunde, Adresse, Objekt ...">
        </div>

        <div class="oc-filter-block small">
        <label class="oc-filter-label">Karte</label>
        <button type="button" class="oc-btn oc-btn-accent" id="toggleMapBtn">
            <i class="feather icon-map"></i>
            <span id="toggleMapBtnText">Karte einblenden</span>
        </button>
        </div>
        <div class="oc-filter-block small">
          <button type="button" class="oc-btn" id="applyServerFilterBtn">
            <i class="feather icon-search"></i> Aktualisieren
          </button>
        </div>

        
      </div>

      <div class="oc-map-card is-hidden" id="neighborMapCard">
        <div id="neighbor-map"></div>
      </div>

      <div class="row">
          <div class="col-lg-12">
            <div class="oc-card">
                <div class="oc-card-header">
                    <span>Gefundene Einträge (<span id="count">{{ $neighbors->count() }}</span>)</span>
                </div>
                <div class="oc-list" id="result-list">
                    @include('admin.new_leads.layouts.neighbor_list', ['neighbors' => $neighbors])
                </div>
            </div>
          </div>

          <div class="col-lg-4" style="display:none">
            <div class="oc-card">
                <div class="oc-card-header">Top Produkte (Auswahl)</div>
                <div class="chart-wrapper">
                    <canvas id="topProductsBarChart"></canvas>
                </div>
            </div>
            <div class="oc-card mt-3" style="display:none" >
                <div class="oc-card-header">Status Verteilung</div>
                <div class="chart-wrapper">
                    <canvas id="stagePieChart"></canvas>
                </div>
            </div>
          </div>
      </div>
      @endif
    </div>
</div>