@extends('admin.layouts.app')

@section('title', 'PVGIS Dashboard')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root{
        --bg:#c0d8ea;
        --green:#93c21c;
        --green-dark:#7fa818;
        --blue:#74b2d4;
        --text:#0f172a;
        --muted:#64748b;
        --border:#e2e8f0;
    }

    .pv-page{min-height:100vh;padding:24px 0 36px}
    .pv-shell{max-width:1550px;margin:0 auto;padding:0 16px;margin-top:100px}
    .pv-card{background:#fff;border:1px solid var(--border);border-radius:20px;padding:20px;box-shadow:0 2px 10px rgba(15,23,42,.04)}
    .pv-head{display:grid;grid-template-columns:1.15fr 2fr;gap:16px;margin-bottom:16px}
    .pv-title{margin:0;font-size:28px;font-weight:900;color:var(--text)}
    .pv-sub{margin-top:8px;color:var(--muted)}
    .pv-badge{margin-top:10px;color:#334155;font-weight:700}
    .pv-filter-grid{display:grid;grid-template-columns:1.15fr .8fr .8fr .8fr .8fr .8fr .8fr .8fr auto auto;gap:12px;align-items:end}
    .pv-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:6px}
    .pv-input{
        width:100%;height:46px;border:1px solid var(--border);border-radius:12px;
        background:#f8fafc;padding:10px 12px;outline:none
    }
    .pv-input[readonly]{background:#eef6fb;font-weight:800;color:#0f172a}
    .pv-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(116,178,212,.18)}
    .pv-btn{height:46px;border:0;border-radius:12px;padding:0 16px;color:#fff;font-weight:800;cursor:pointer}
    .pv-btn-green{background:var(--green)}
    .pv-btn-green:hover{background:var(--green-dark)}
    .pv-btn-dark{background:#0f172a}
    .pv-stats{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:14px;margin-bottom:16px}
    .pv-stat-title{font-size:12px;font-weight:800;color:var(--muted);margin-bottom:8px}
    .pv-stat-value{font-size:24px;font-weight:900;line-height:1.1}
    .pv-stat-sub{font-size:12px;color:#94a3b8;margin-top:6px}
    .pv-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:16px;margin-bottom:16px}
    .pv-chart-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
    .pv-panel-title{font-size:18px;font-weight:800;margin:0 0 14px}
    .pv-table-wrap{overflow:auto}
    .pv-table{width:100%;border-collapse:collapse}
    .pv-table th,.pv-table td{padding:12px;border-bottom:1px solid #edf2f7;text-align:left}
    .pv-table th{background:#f8fafc;font-size:13px}
    .pv-mini-list{display:grid;gap:10px}
    .pv-mini-row{display:flex;justify-content:space-between;gap:12px;background:#f8fafc;border:1px solid var(--border);padding:12px 14px;border-radius:14px}
    .pv-mini-row strong{font-size:14px}
    .pv-mini-row span{font-size:14px;color:var(--muted)}
    .loading,.error,.empty{padding:28px;text-align:center;border-radius:16px}
    .loading{background:#f8fafc}
    .error{background:#fef2f2;color:#991b1b}
    .empty{background:#fff7ed;color:#9a3412}
    canvas{max-height:320px}

    @media (max-width:1400px){
        .pv-head,.pv-grid,.pv-chart-grid{grid-template-columns:1fr}
        .pv-stats{grid-template-columns:repeat(4,minmax(0,1fr))}
        .pv-filter-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    }

    @media (max-width:900px){
        .pv-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
        .pv-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }

    @media print{
        .pv-btn{display:none!important}
        .pv-page{background:#fff!important;padding:0!important}
        .pv-card{box-shadow:none!important}
    }
</style>
@endsection

@section('content')
<div class="pv-page">
    <div class="pv-shell">
        <div class="pv-head">
            <div class="pv-card">
                <h1 class="pv-title">PVGIS Ertragsdashboard</h1>
                <div class="pv-sub">PLZ eingeben, Standort wird über Google ermittelt. kWp wird automatisch aus Modulanzahl × Modulleistung berechnet.</div>
                <div id="locationLabel" class="pv-badge">Ort: - | Land: -</div>
            </div>

            <div class="pv-card">
                <div class="pv-filter-grid">
                    <div>
                        <div class="pv-label">Postleitzahl</div>
                        <input type="text" id="postcodeInput" class="pv-input" placeholder="z. B. 61267">
                    </div>

                    <div>
                        <div class="pv-label">PV Module (Anzahl)</div>
                        <input type="number" id="moduleCountInput" class="pv-input" value="100" min="1" step="1">
                    </div>

                    <div>
                        <div class="pv-label">Modulleistung (Wp)</div>
                        <input type="number" id="modulePowerInput" class="pv-input" value="450" min="1" step="1">
                    </div>

                    <div>
                        <div class="pv-label">Anlage (kWp)</div>
                        <input type="number" id="peakpowerInput" class="pv-input" value="45.0" min="0" step="0.01" readonly>
                    </div>

                    <div>
                        <div class="pv-label">Batterie (kWh)</div>
                        <input type="number" id="batteryInput" class="pv-input" value="20" min="0" step="0.1">
                    </div>

                    <div>
                        <div class="pv-label">Verlust (%)</div>
                        <input type="number" id="lossInput" class="pv-input" value="14" min="0" step="0.1">
                    </div>

                    <div>
                        <div class="pv-label">Neigung (°)</div>
                        <input type="number" id="angleInput" class="pv-input" value="35" min="0" max="90" step="1">
                    </div>

                    <div>
                        <div class="pv-label">Azimut (°)</div>
                        <input type="number" id="aspectInput" class="pv-input" value="0" min="-180" max="180" step="1">
                    </div>

                    <button type="button" id="loadBtn" class="pv-btn pv-btn-green">Daten laden</button>
                    <button type="button" id="printBtn" class="pv-btn pv-btn-dark">PDF exportieren</button>
                </div>
            </div>
        </div>

        <div id="loadingBox" class="loading pv-card" style="display:none;">PVGIS-Daten werden geladen...</div>
        <div id="errorBox" class="error pv-card" style="display:none;"></div>
        <div id="emptyBox" class="empty pv-card" style="display:none;">Keine Daten gefunden.</div>

        <div id="contentBox" style="display:none;">
            <div class="pv-stats">
                <div class="pv-card">
                    <div class="pv-stat-title">Breitengrad</div>
                    <div class="pv-stat-value" id="statLat">-</div>
                    <div class="pv-stat-sub">Geografische Lage</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Längengrad</div>
                    <div class="pv-stat-value" id="statLon">-</div>
                    <div class="pv-stat-sub">Geografische Lage</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Jahresertrag</div>
                    <div class="pv-stat-value" id="statYearEnergy">-</div>
                    <div class="pv-stat-sub">kWh/Jahr</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Jährliche Einstrahlung</div>
                    <div class="pv-stat-value" id="statYearIrradiation">-</div>
                    <div class="pv-stat-sub">kWh/m²/Jahr</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Spezifischer Ertrag</div>
                    <div class="pv-stat-value" id="statSpecific">-</div>
                    <div class="pv-stat-sub">kWh/kWp</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Ø Tagesertrag</div>
                    <div class="pv-stat-value" id="statDailyAvg">-</div>
                    <div class="pv-stat-sub">kWh/Tag</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">Batterie-Zyklen/Jahr</div>
                    <div class="pv-stat-value" id="statBatteryCycles">-</div>
                    <div class="pv-stat-sub">Theoretisch</div>
                </div>
                <div class="pv-card">
                    <div class="pv-stat-title">PLZ</div>
                    <div class="pv-stat-value" id="statPostcode">-</div>
                    <div class="pv-stat-sub">Suchwert</div>
                </div>
            </div>

            <div class="pv-grid">
                <div class="pv-card">
                    <h3 class="pv-panel-title">Monatliche Ertrags- und Strahlungsdaten</h3>
                    <div class="pv-table-wrap">
                        <table class="pv-table">
                            <thead>
                                <tr>
                                    <th>Monat</th>
                                    <th>Tagesertrag<br><small>(kWh/Tag)</small></th>
                                    <th>Monatsertrag<br><small>(kWh/Monat)</small></th>
                                    <th>Tägliche Einstrahlung<br><small>(kWh/m²/Tag)</small></th>
                                    <th>Monatliche Einstrahlung<br><small>(kWh/m²/Monat)</small></th>
                                    <th>Ertrags-Schwankung<br><small>(± kWh)</small></th>
                                </tr>
                            </thead>
                            <tbody id="monthlyTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="pv-card">
                    <h3 class="pv-panel-title">Standort & Anlagen-Einstellungen</h3>
                    <div class="pv-mini-list">
                        <div class="pv-mini-row">
                            <strong>Postleitzahl</strong>
                            <span id="sumPostcode">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Ort</strong>
                            <span id="sumCity">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Land</strong>
                            <span id="sumCountry">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>PV Module</strong>
                            <span id="sumModuleCount">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Modulleistung</strong>
                            <span id="sumModulePower">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Anlagengröße</strong>
                            <span id="sumPeak">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Batteriespeicher</strong>
                            <span id="sumBattery">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Systemverluste</strong>
                            <span id="sumLoss">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Dachneigung</strong>
                            <span id="sumAngle">-</span>
                        </div>
                        <div class="pv-mini-row">
                            <strong>Azimut</strong>
                            <span id="sumAspect">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pv-chart-grid">
                <div class="pv-card">
                    <h3 class="pv-panel-title">Monatlicher Energieertrag</h3>
                    <canvas id="energyChart"></canvas>
                </div>

                <div class="pv-card">
                    <h3 class="pv-panel-title">Monatliche Einstrahlung</h3>
                    <canvas id="irradiationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    const endpoint = @json(route('admin.pvgis.fetch'));

    const postcodeInput    = document.getElementById('postcodeInput');
    const moduleCountInput = document.getElementById('moduleCountInput');
    const modulePowerInput = document.getElementById('modulePowerInput');
    const peakpowerInput   = document.getElementById('peakpowerInput');
    const batteryInput     = document.getElementById('batteryInput');
    const lossInput        = document.getElementById('lossInput');
    const angleInput       = document.getElementById('angleInput');
    const aspectInput      = document.getElementById('aspectInput');
    const loadBtn          = document.getElementById('loadBtn');
    const printBtn         = document.getElementById('printBtn');

    const loadingBox = document.getElementById('loadingBox');
    const errorBox   = document.getElementById('errorBox');
    const emptyBox   = document.getElementById('emptyBox');
    const contentBox = document.getElementById('contentBox');

    let energyChart = null;
    let irradiationChart = null;
    let postcodeTimer = null;

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

    function num(v, digits = 1) {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n.toFixed(digits) : '-';
    }

    function calculatePeakpower() {
        const count = parseFloat(moduleCountInput.value || 0);
        const watt  = parseFloat(modulePowerInput.value || 0);

        if (count > 0 && watt > 0) {
            const kwp = (count * watt) / 1000;
            peakpowerInput.value = kwp.toFixed(2);
            return kwp;
        }

        peakpowerInput.value = '0.00';
        return 0;
    }

    function destroyCharts() {
        if (energyChart) {
            energyChart.destroy();
            energyChart = null;
        }
        if (irradiationChart) {
            irradiationChart.destroy();
            irradiationChart = null;
        }
    }

    async function lookupAndLoad() {
        const postcode = (postcodeInput.value || '').trim();
        const calculatedPeak = calculatePeakpower();

        if (!postcode) {
            showEmpty('Bitte zuerst eine Postleitzahl eingeben.');
            return;
        }

        if (calculatedPeak <= 0) {
            showEmpty('Bitte gültige Modulanzahl und Modulleistung eingeben.');
            return;
        }

        showLoading();

        try {
            const query = new URLSearchParams({
                postcode: postcode,
                module_count: moduleCountInput.value || '0',
                module_power_watt: modulePowerInput.value || '0',
                peakpower: peakpowerInput.value || '0',
                battery_size: batteryInput.value || '0',
                loss: lossInput.value || '14',
                angle: angleInput.value || '35',
                aspect: aspectInput.value || '0',
            });

            const response = await fetch(`${endpoint}?${query.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'PVGIS-Daten konnten nicht geladen werden.');
            }

            render(data);
        } catch (e) {
            showError(e.message || 'PVGIS-Daten konnten nicht geladen werden.');
        }
    }

    async function lookupLabelOnly() {
        const postcode = (postcodeInput.value || '').trim();

        if (!postcode) {
            document.getElementById('locationLabel').textContent = 'Ort: - | Land: -';
            return;
        }

        try {
            const query = new URLSearchParams({
                postcode: postcode,
                module_count: moduleCountInput.value || '0',
                module_power_watt: modulePowerInput.value || '0',
                peakpower: peakpowerInput.value || '0',
                battery_size: batteryInput.value || '0',
            });

            const response = await fetch(`${endpoint}?${query.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const data = await response.json();

            if (response.ok && data.success && data.location) {
                document.getElementById('locationLabel').textContent =
                    `Ort: ${data.location.city || '-'} | Land: ${data.location.country || '-'}`;
            } else {
                document.getElementById('locationLabel').textContent = 'Ort: - | Land: -';
            }
        } catch (e) {
            document.getElementById('locationLabel').textContent = 'Ort: - | Land: -';
        }
    }

    function render(payload) {
        const location = payload.location || {};
        const monthly  = Array.isArray(payload.monthly) ? payload.monthly : [];
        const totals   = payload.totals || {};
        const input    = payload.input || {};
        const derived  = payload.derived || {};

        if (!monthly.length) {
            showEmpty('PVGIS hat keine Monatsdaten zurückgegeben.');
            return;
        }

        document.getElementById('locationLabel').textContent =
            `Ort: ${location.city || '-'} | Land: ${location.country || '-'}`;

        document.getElementById('statLat').textContent = num(location.lat, 4);
        document.getElementById('statLon').textContent = num(location.lon, 4);
        document.getElementById('statPostcode').textContent = location.postcode || '-';

        const yearEnergy = parseFloat(totals['E_y'] ?? 0);
        const yearIrr    = parseFloat(totals['H(i)_y'] ?? 0);
        const peak       = parseFloat(input.peakpower ?? 0);
        const specific   = peak > 0 ? (yearEnergy / peak) : 0;

        document.getElementById('statYearEnergy').textContent      = num(yearEnergy, 0);
        document.getElementById('statYearIrradiation').textContent = num(yearIrr, 0);
        document.getElementById('statSpecific').textContent        = num(specific, 0);
        document.getElementById('statDailyAvg').textContent        = num(derived.avg_daily_energy ?? 0, 1);
        document.getElementById('statBatteryCycles').textContent   = num(derived.battery_cycles_per_year ?? 0, 1);

        document.getElementById('sumPostcode').textContent    = location.postcode || '-';
        document.getElementById('sumCity').textContent        = location.city || '-';
        document.getElementById('sumCountry').textContent     = location.country || '-';
        document.getElementById('sumModuleCount').textContent = `${num(input.module_count, 0)} Stück`;
        document.getElementById('sumModulePower').textContent = `${num(input.module_power_watt, 0)} Wp`;
        document.getElementById('sumPeak').textContent        = `${num(input.peakpower, 2)} kWp`;
        document.getElementById('sumBattery').textContent     = `${num(input.battery_size, 1)} kWh`;
        document.getElementById('sumLoss').textContent        = `${num(input.loss, 1)} %`;
        document.getElementById('sumAngle').textContent       = input.angle !== undefined ? `${num(input.angle, 0)}°` : 'Auto';
        document.getElementById('sumAspect').textContent      = input.aspect !== undefined ? `${num(input.aspect, 0)}°` : '0°';

        const tbody = document.getElementById('monthlyTableBody');
        tbody.innerHTML = monthly.map(row => `
            <tr>
                <td>${row.month}</td>
                <td>${num(row['E_d'], 2)}</td>
                <td>${num(row['E_m'], 2)}</td>
                <td>${num(row['H(i)_d'], 2)}</td>
                <td>${num(row['H(i)_m'], 2)}</td>
                <td>${num(row['SD_m'], 2)}</td>
            </tr>
        `).join('');

        const labels = monthly.map(row => `M${row.month}`);
        const energyData = monthly.map(row => parseFloat(row['E_m'] || 0));
        const irrData    = monthly.map(row => parseFloat(row['H(i)_m'] || 0));

        destroyCharts();

        energyChart = new Chart(document.getElementById('energyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'E_m (kWh/Monat)',
                    data: energyData,
                    backgroundColor: 'rgba(147,194,28,.75)'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        irradiationChart = new Chart(document.getElementById('irradiationChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'H(i)_m (kWh/m²/Monat)',
                    data: irrData,
                    borderColor: '#74b2d4',
                    backgroundColor: 'rgba(116,178,212,.15)',
                    tension: 0.35,
                    fill: false
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        showContent();
    }

    [moduleCountInput, modulePowerInput].forEach(el => {
        el.addEventListener('input', calculatePeakpower);
    });

    postcodeInput.addEventListener('input', function () {
        clearTimeout(postcodeTimer);
        calculatePeakpower();
        postcodeTimer = setTimeout(lookupLabelOnly, 350);
    });

    loadBtn.addEventListener('click', lookupAndLoad);

    printBtn.addEventListener('click', function () {
        window.print();
    });

    calculatePeakpower();
})();
</script>
@endsection