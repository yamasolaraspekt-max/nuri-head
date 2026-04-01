@extends('admin.layouts.app')

@section('title', 'Klimadashboard')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
    :root{
        --bg:#c0d8ea;
        --green:#93c21c;
        --blue:#74b2d4;
        --soft:#f8fafc;
        --text:#0f172a;
        --muted:#64748b;
        --border:#e2e8f0;
        --white:#fff;
        --radius:16px;
    }

    .climate-page{min-height:100vh;padding:24px 0 36px}
    .climate-shell{max-width:1500px;margin:0 auto;padding:0 16px;margin-top:100px}
    .card-ui{background:#fff;border:1px solid var(--border);border-radius:20px;padding:20px;box-shadow:0 2px 10px rgba(15,23,42,.04)}
    .head-grid{display:grid;grid-template-columns:1.4fr 2fr;gap:16px;margin-bottom:16px}
    .title-main{font-size:28px;font-weight:800;margin:0}
    .subtext{margin-top:8px;color:var(--muted)}
    .filter-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr)) auto auto;gap:12px;align-items:end}
    .label-ui{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:6px}
    .field-ui{width:100%;height:46px;border:1px solid var(--border);border-radius:12px;background:var(--soft);padding:10px 12px}
    .btn-ui{height:46px;border:0;border-radius:12px;padding:0 16px;color:#fff;font-weight:800;cursor:pointer}
    .btn-green{background:var(--green)}
    .btn-dark{background:#0f172a}
    .stats-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px;margin-bottom:16px}
    .stat-title{font-size:12px;font-weight:800;color:var(--muted);margin-bottom:8px}
    .stat-value{font-size:24px;font-weight:900;line-height:1.1}
    .stat-sub{font-size:12px;color:#94a3b8;margin-top:6px}
    .content-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:16px;margin-bottom:16px}
    .chart-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
    .panel-title{font-size:18px;font-weight:800;margin:0 0 14px}
    .mini-list{display:grid;gap:10px}
    .mini-row{display:flex;justify-content:space-between;gap:12px;background:var(--soft);border:1px solid var(--border);padding:12px 14px;border-radius:14px}
    .mini-row strong{font-size:14px}
    .mini-row span{font-size:14px;color:var(--muted)}
    .table-wrap{overflow:auto}
    .climate-table{width:100%;border-collapse:collapse}
    .climate-table th,.climate-table td{padding:12px;border-bottom:1px solid #edf2f7;text-align:left}
    .climate-table th{background:#f8fafc;font-size:13px}
    .badge-ui{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:800}
    .cold{background:#dbeafe;color:#1d4ed8}
    .mid{background:#f1f5f9;color:#334155}
    .hot{background:#dcfce7;color:#166534}
    .sun-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
    .sun-box{padding:18px;border-radius:18px;border:2px solid var(--border);background:linear-gradient(180deg,#fff,#f8fafc)}
    .sun-icon{font-size:28px;margin-bottom:8px}
    .sun-title{font-size:14px;font-weight:800;margin-bottom:6px}
    .sun-value{font-size:28px;font-weight:900}
    .sun-sub{font-size:12px;color:var(--muted);margin-top:6px}
    .loading,.error,.empty{padding:28px;text-align:center;border-radius:16px}
    .loading{background:#f8fafc}
    .error{background:#fef2f2;color:#991b1b}
    .empty{background:#fff7ed;color:#9a3412}
    .select2-container{width:100%!important}
    .select2-container--default .select2-selection--single{
        height:46px!important;border:1px solid var(--border)!important;border-radius:12px!important;
        background:#f8fafc!important;display:flex!important;align-items:center!important
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:44px!important}
    .select2-container--default .select2-selection--single .select2-selection__arrow{height:44px!important}
    canvas{max-height:320px}

    @media (max-width:1200px){
        .stats-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
        .filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .content-grid,.chart-grid,.head-grid,.sun-grid{grid-template-columns:1fr}
    }

    @media (max-width:768px){
        .stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }

    @media print{
        .filter-grid .btn-ui{display:none!important}
        .climate-page{background:#fff!important;padding:0!important}
        .card-ui{box-shadow:none!important}
    }
</style>
@endsection

@section('content')
<div class="climate-page">
    <div class="climate-shell">
        <div class="head-grid">
            <div class="card-ui">
                <h1 class="title-main">Klimadashboard</h1>
                <div class="subtext">
                    {{ isset($lead) ? 'Datenübersicht für Lead: '.$lead->name.' '.$lead->lastname.' - '.($alternative->object_name ?? '') : 'Klima-Datenübersicht' }}
                </div>
                <div id="locationBadge" class="subtext" style="margin-top:12px;"></div>
            </div>

            <div class="card-ui">
                <div class="filter-grid">
                    <div>
                        <div class="label-ui">Postleitzahl</div>
                        <select id="postcodeSelect" class="field-ui"></select>
                    </div>

                    <div>
                        <div class="label-ui">Land</div>
                        <select id="countrySelect" class="field-ui"></select>
                    </div>

                    <div>
                        <div class="label-ui">Ort</div>
                        <select id="citySelect" class="field-ui"></select>
                    </div>

                    <div>
                        <div class="label-ui">Station</div>
                        <select id="stationSelect" class="field-ui"></select>
                    </div>

                    <button type="button" id="refreshBtn" class="btn-ui btn-green">Daten laden</button>
                    <button type="button" id="printBtn" class="btn-ui btn-dark">PDF exportieren</button>
                </div>
            </div>
        </div>

        <div id="loadingBox" class="loading card-ui">Klimadaten werden geladen...</div>
        <div id="errorBox" class="error card-ui" style="display:none;"></div>
        <div id="emptyBox" class="empty card-ui" style="display:none;">Keine Klimadaten gefunden.</div>

        <div id="dashboardContent" style="display:none;">
            <div class="stats-grid">
                <div class="card-ui">
                    <div class="stat-title">Ø Jahrestemperatur</div>
                    <div class="stat-value" id="statAvgTemp">0 °C</div>
                    <div class="stat-sub">Langjähriger Mittelwert</div>
                </div>
                <div class="card-ui">
                    <div class="stat-title">Heiztage</div>
                    <div class="stat-value" id="statHeating">0</div>
                    <div class="stat-sub">Tage</div>
                </div>
                <div class="card-ui">
                    <div class="stat-title">Kühltage</div>
                    <div class="stat-value" id="statCooling">0</div>
                    <div class="stat-sub">Tage</div>
                </div>
                <div class="card-ui">
                    <div class="stat-title">Gradtage</div>
                    <div class="stat-value" id="statDegree">0</div>
                    <div class="stat-sub">GTZ</div>
                </div>
                <div class="card-ui">
                    <div class="stat-title">Solar Horizontal</div>
                    <div class="stat-value" id="statSolar">0</div>
                    <div class="stat-sub">kWh/m²</div>
                </div>
                <div class="card-ui">
                    <div class="stat-title">Stationshöhe</div>
                    <div class="stat-value" id="statAlt">0 m</div>
                    <div class="stat-sub" id="statLatLon">Breite: -, Länge: -</div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card-ui">
                    <h3 class="panel-title">Monatliche Klimadaten</h3>
                    <div class="table-wrap">
                        <table class="climate-table">
                            <thead>
                                <tr>
                                    <th>Monat</th>
                                    <th>Ø Temp.</th>
                                    <th>Heiztage</th>
                                    <th>Kühltage</th>
                                    <th>Gradtage</th>
                                    <th>Temp. an Heiztagen</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-ui">
                    <h3 class="panel-title">Übersicht</h3>
                    <div class="mini-list">
                        <div class="mini-row"><strong>Postleitzahl</strong><span id="sumPostcode">-</span></div>
                        <div class="mini-row"><strong>Land</strong><span id="sumCountry">-</span></div>
                        <div class="mini-row"><strong>Ort</strong><span id="sumCity">-</span></div>
                        <div class="mini-row"><strong>Station</strong><span id="sumStation">-</span></div>
                        <div class="mini-row"><strong>Region</strong><span id="sumRegion">-</span></div>
                        <div class="mini-row"><strong>Neutrale Tage</strong><span id="sumNeutral">-</span></div>
                        <div class="mini-row"><strong>Solar vertikal Süd</strong><span id="sumSolarVertical">-</span></div>
                        <div class="mini-row"><strong>Solar 45° Süd</strong><span id="sumSolarTilted">-</span></div>
                    </div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="card-ui">
                    <h3 class="panel-title">Temperatur- und Heizprofil</h3>
                    <canvas id="tempChart"></canvas>
                </div>
                <div class="card-ui">
                    <h3 class="panel-title">Solarstrahlung Vergleich</h3>
                    <canvas id="solarChart"></canvas>
                </div>
            </div>

            <div class="chart-grid">
                <div class="card-ui">
                    <h3 class="panel-title">Jahresverteilung</h3>
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="card-ui">
                    <h3 class="panel-title">Sonnen-Simulation</h3>
                    <div class="sun-grid">
                        <div class="sun-box">
                            <div class="sun-icon">☀️</div>
                            <div class="sun-title">Horizontal</div>
                            <div class="sun-value" id="sunHorizontal">0</div>
                            <div class="sun-sub">kWh/m² pro Jahr</div>
                        </div>
                        <div class="sun-box">
                            <div class="sun-icon">🧱</div>
                            <div class="sun-title">Vertikal Süd</div>
                            <div class="sun-value" id="sunVertical">0</div>
                            <div class="sun-sub">Fassade Süd</div>
                        </div>
                        <div class="sun-box">
                            <div class="sun-icon">🔆</div>
                            <div class="sun-title">45° Süd</div>
                            <div class="sun-value" id="sunTilted">0</div>
                            <div class="sun-sub">Optimierte Dachfläche</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function () {
    const endpoint = @json(route('admin.climate.data', [
        'customer_id' => $customerId,
        'alternative_id' => $alternativeId
    ]));
    const initialPostcode = @json($autoPostcode ?? '');

    const loadingBox = document.getElementById('loadingBox');
    const errorBox = document.getElementById('errorBox');
    const emptyBox = document.getElementById('emptyBox');
    const contentBox = document.getElementById('dashboardContent');

    const postcodeSelect = document.getElementById('postcodeSelect');
    const countrySelect = document.getElementById('countrySelect');
    const citySelect = document.getElementById('citySelect');
    const stationSelect = document.getElementById('stationSelect');
    const refreshBtn = document.getElementById('refreshBtn');
    const printBtn = document.getElementById('printBtn');

    let charts = {
        temp: null,
        solar: null,
        pie: null
    };

    function esc(v) {
        if (v === null || v === undefined) return '';
        return String(v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function initSelect2(selector) {
        if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
        const $el = jQuery(selector);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({
            width: '100%',
            placeholder: 'Bitte wählen',
            allowClear: true
        });
    }

    function fillSelect(el, items, selected, mapFn) {
        const rows = ['<option value=""></option>'];

        (items || []).forEach(item => {
            const data = mapFn ? mapFn(item) : { value: item, label: item };
            rows.push(`<option value="${esc(data.value)}">${esc(data.label)}</option>`);
        });

        el.innerHTML = rows.join('');
        initSelect2('#' + el.id);

        if (selected !== undefined && selected !== null && selected !== '') {
            if (typeof window.jQuery !== 'undefined') {
                jQuery(el).val(String(selected)).trigger('change.select2');
            } else {
                el.value = String(selected);
            }
        }
    }

    function showLoading() {
        loadingBox.style.display = 'block';
        errorBox.style.display = 'none';
        emptyBox.style.display = 'none';
        contentBox.style.display = 'none';
    }

    function showError(message) {
        loadingBox.style.display = 'none';
        emptyBox.style.display = 'none';
        contentBox.style.display = 'none';
        errorBox.style.display = 'block';
        errorBox.textContent = message || 'Fehler beim Laden.';
    }

    function showEmpty(message) {
        loadingBox.style.display = 'none';
        errorBox.style.display = 'none';
        contentBox.style.display = 'none';
        emptyBox.style.display = 'block';
        emptyBox.textContent = message || 'Keine Daten gefunden.';
    }

    function showContent() {
        loadingBox.style.display = 'none';
        errorBox.style.display = 'none';
        emptyBox.style.display = 'none';
        contentBox.style.display = 'block';
    }

    function tempClass(temp) {
        const t = parseFloat(temp || 0);
        if (t < 5) return 'cold';
        if (t > 15) return 'hot';
        return 'mid';
    }

    function destroyCharts() {
        Object.keys(charts).forEach(key => {
            if (charts[key]) {
                charts[key].destroy();
                charts[key] = null;
            }
        });
    }

    function renderCharts(payload) {
        destroyCharts();

        const c = payload.charts || {};
        const months = c.months || [];

        const tempCtx = document.getElementById('tempChart').getContext('2d');
        charts.temp = new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Ø Temperatur',
                        data: c.temperature || [],
                        borderColor: '#74b2d4',
                        backgroundColor: 'rgba(116,178,212,.15)',
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Heiztage',
                        data: c.heatingDays || [],
                        borderColor: '#93c21c',
                        backgroundColor: 'rgba(147,194,28,.15)',
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Gradtage',
                        data: c.degreeDays || [],
                        borderColor: '#0f172a',
                        backgroundColor: 'rgba(15,23,42,.10)',
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        const solarCtx = document.getElementById('solarChart').getContext('2d');
        charts.solar = new Chart(solarCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Horizontal',
                        data: c.solarHorizontal || [],
                        backgroundColor: 'rgba(116,178,212,.75)'
                    },
                    {
                        label: 'Vertikal Süd',
                        data: c.solarVerticalS || [],
                        backgroundColor: 'rgba(147,194,28,.75)'
                    },
                    {
                        label: '45° Süd',
                        data: c.solarTiltedS45 || [],
                        backgroundColor: 'rgba(15,23,42,.65)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        const pieCtx = document.getElementById('pieChart').getContext('2d');
        charts.pie = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: (c.pie && c.pie.labels) ? c.pie.labels : [],
                datasets: [{
                    data: (c.pie && c.pie.values) ? c.pie.values : [],
                    backgroundColor: ['#93c21c', '#74b2d4', '#cbd5e1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        const sun = c.sunSimulation || {};
        document.getElementById('sunHorizontal').textContent = Math.round(sun.horizontal || 0);
        document.getElementById('sunVertical').textContent = Math.round(sun.vertical_s || 0);
        document.getElementById('sunTilted').textContent = Math.round(sun.tilted_s_45 || 0);
    }

    function renderDashboard(payload) {
        const filters = payload.filters || {};
        const selected = filters.selected || {};
        const station = payload.station || null;
        const location = payload.location || null;
        const monthly = Array.isArray(payload.monthly) ? payload.monthly : [];
        const summary = payload.summary || {};

        fillSelect(postcodeSelect, filters.postcodes || [], selected.postcode || '');
        fillSelect(countrySelect, filters.countries || [], selected.country || '');
        fillSelect(citySelect, filters.cities || [], selected.city || '');
        fillSelect(stationSelect, filters.stations || [], selected.station_id || '', function (item) {
            return {
                value: item.station_id,
                label: `${item.name} (${item.region || 'Region'})`
            };
        });

        if (!station) {
            showEmpty(payload.message || 'Keine Station gefunden.');
            return;
        }

        document.getElementById('statAvgTemp').textContent = `${summary.avgTemp ?? 0} °C`;
        document.getElementById('statHeating').textContent = summary.totalHeatingDays ?? 0;
        document.getElementById('statCooling').textContent = summary.totalCoolingDays ?? 0;
        document.getElementById('statDegree').textContent = summary.totalDegreeDays ?? 0;
        document.getElementById('statSolar').textContent = summary.totalSolar ?? 0;
        document.getElementById('statAlt').textContent = `${station.alt ?? 0} m`;
        document.getElementById('statLatLon').textContent = `Breite: ${station.lat ?? '-'}, Länge: ${station.lon ?? '-'}`;

        document.getElementById('sumPostcode').textContent = location?.postcode || '-';
        document.getElementById('sumCountry').textContent = location?.country || '-';
        document.getElementById('sumCity').textContent = location?.city || '-';
        document.getElementById('sumStation').textContent = station.name || '-';
        document.getElementById('sumRegion').textContent = station.region || '-';
        document.getElementById('sumNeutral').textContent = summary.neutralDays ?? 0;
        document.getElementById('sumSolarVertical').textContent = summary.totalSolarVerticalS ?? 0;
        document.getElementById('sumSolarTilted').textContent = summary.totalSolarTiltedS45 ?? 0;

        const badgeText = [
            location?.postcode ? `PLZ: ${location.postcode}` : null,
            location?.city ? `Ort: ${location.city}` : null,
            location?.country ? `Land: ${location.country}` : null,
            station?.name ? `Station: ${station.name}` : null
        ].filter(Boolean).join(' | ');
        document.getElementById('locationBadge').textContent = badgeText;

        const tbody = document.getElementById('monthlyTableBody');
        if (!monthly.length) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Keine Monatsdaten vorhanden.</td></tr>`;
        } else {
            tbody.innerHTML = monthly.map(row => `
                <tr>
                    <td>${esc(row.month)}</td>
                    <td><span class="badge-ui ${tempClass(row.temp)}">${esc(row.temp)} °C</span></td>
                    <td>${esc(row.heatingDays)}</td>
                    <td>${esc(row.coolingDays)}</td>
                    <td>${esc(row.degreeDays)}</td>
                    <td>${esc(row.temp_heating_days ?? '-')}</td>
                </tr>
            `).join('');
        }

        renderCharts(payload);
        showContent();
    }

    async function loadData(params = {}) {
        showLoading();

        try {
            const query = new URLSearchParams();

            if (params.postcode) query.append('postcode', params.postcode);
            if (params.country) query.append('country', params.country);
            if (params.city) query.append('city', params.city);
            if (params.station_id) query.append('station_id', params.station_id);

            const url = query.toString() ? `${endpoint}?${query.toString()}` : endpoint;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Klimadaten konnten nicht geladen werden.');
            }

            renderDashboard(data);
        } catch (e) {
            showError(e.message || 'Klimadaten konnten nicht geladen werden.');
        }
    }

    function currentFilters() {
        const getVal = (el) => {
            if (typeof window.jQuery !== 'undefined') {
                return jQuery(el).val() || '';
            }
            return el.value || '';
        };

        return {
            postcode: getVal(postcodeSelect),
            country: getVal(countrySelect),
            city: getVal(citySelect),
            station_id: getVal(stationSelect),
        };
    }

    // Postcode = strongest auto selector
    jQuery(document).on('change', '#postcodeSelect', function () {
        loadData({
            postcode: jQuery(this).val() || ''
        });
    });

    jQuery(document).on('change', '#countrySelect', function () {
        const filters = currentFilters();
        loadData({
            postcode: filters.postcode,
            country: filters.country
        });
    });

    jQuery(document).on('change', '#citySelect', function () {
        const filters = currentFilters();
        loadData({
            postcode: filters.postcode,
            country: filters.country,
            city: filters.city
        });
    });

    jQuery(document).on('change', '#stationSelect', function () {
        const filters = currentFilters();
        loadData({
            postcode: filters.postcode,
            country: filters.country,
            city: filters.city,
            station_id: filters.station_id
        });
    });

    refreshBtn.addEventListener('click', function () {
        loadData(currentFilters());
    });

    printBtn.addEventListener('click', function () {
        window.print();
    });

    loadData({
        postcode: initialPostcode || ''
    });
})();
</script>
@endsection