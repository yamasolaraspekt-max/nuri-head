@php
    $pv = session('pv_data');
    $shs = session('shs_data');
    $mr = session('mr_data');
    $dr = session('dr_data')['outputs']['hourly'] ?? [];

    $lat = old('latitude') ?? '';
    $lon = old('longitude') ?? '';
    $monthly = $pv['outputs']['monthly']['fixed'] ?? [];
    $annual_total = $pv['outputs']['totals']['fixed']['E_y'] ?? 0;
    $variation = $pv['outputs']['totals']['fixed']['yearly_variation'] ?? null;
    $monthNames = ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
@endphp

<pre>
    {{ print_r($dr, true) }}
</pre>

<div class="card shadow-sm p-3">
    <h5 class="text-success font-weight-bold text-center mb-3">☀ Wetterdaten & PVGIS Analyse</h5>

    @include('admin.checklist.profitablity_calculation.partials.pvgis_form', compact('customer_id', 'alternative_id', 'postcode', 'lat', 'lon'))
    @include('admin.checklist.profitablity_calculation.partials.pvgis_output', compact('pv', 'shs', 'mr', 'dr', 'monthly', 'annual_total', 'variation', 'monthNames'))
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const months = @json($monthNames);
    const pv = @json($monthly);
    const shs = @json($shs['outputs']['monthly'] ?? []);
    const mr = @json($mr['outputs']['monthly'] ?? []);
    const dr = @json(session('dr_data')['outputs']['hourly'] ?? []);

    const ctxPv = document.getElementById('monthlyChart')?.getContext('2d');
    if (ctxPv && pv.length) {
        new Chart(ctxPv, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'PV-Produktion (kWh)',
                        data: pv.map(r => r.E_m),
                        backgroundColor: 'rgba(77, 166, 255, 0.7)',
                        borderColor: '#007bff',
                        borderWidth: 1
                    },
                    {
                        label: 'Globalstrahlung (kWh/m²)',
                        data: pv.map(r => r["H(i)_m"]),
                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Energie (kWh)' } } } }
        });
    }

    const ctxShs = document.getElementById('shsChart')?.getContext('2d');
    if (ctxShs && shs.length) {
        new Chart(ctxShs, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Tägliche Erzeugung (Wh)',
                        data: shs.map(r => r.E_d),
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 128, 0, 0.1)',
                        fill: true
                    },
                    {
                        label: 'Batterieleerstand (%)',
                        data: shs.map(r => r.f_e),
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.1)',
                        fill: true
                    }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Wert' } } } }
        });
    }

    const ctxMr = document.getElementById('mrChart')?.getContext('2d');
    if (ctxMr && mr.length) {
        new Chart(ctxMr, {
            type: 'line',
            data: {
                labels: mr.map(r => `${r.month.toString().padStart(2, '0')}/${r.year}`),
                datasets: [{
                    label: 'H(h)_m (kWh/m²)',
                    data: mr.map(r => r["H(h)_m"]),
                    borderColor: '#ffaa00',
                    backgroundColor: 'rgba(255, 170, 0, 0.1)',
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'kWh/m²' } } } }
        });
    }

    const ctxDr = document.getElementById('drChart')?.getContext('2d');
    if (ctxDr && dr.length) {
        new Chart(ctxDr, {
            type: 'line',
            data: {
                labels: dr.map(d => d.time),
                datasets: [
                    {
                        label: 'Global (G(i))',
                        data: dr.map(d => d["G(i)"]),
                        borderColor: '#007bff',
                        fill: false
                    },
                    {
                        label: 'Direkt (Gb(i))',
                        data: dr.map(d => d["Gb(i)"]),
                        borderColor: '#28a745',
                        fill: false
                    },
                    {
                        label: 'Diffus (Gd(i))',
                        data: dr.map(d => d["Gd(i)"]),
                        borderColor: '#ffc107',
                        fill: false
                    }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'W/m²' } } } }
        });
    }
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const postcodeInput = document.getElementById("postcode");
    const latInput = document.getElementById("latitude");
    const lonInput = document.getElementById("longitude");
    const form = document.getElementById("pvgis-form");

    if (postcodeInput && latInput && lonInput && !latInput.value && !lonInput.value && postcodeInput.value) {
        const postcode = postcodeInput.value;
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${postcode}&region=de&key={{ config('services.google.maps_key') }}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "OK") {
                    const loc = data.results[0].geometry.location;
                    latInput.value = loc.lat;
                    lonInput.value = loc.lng;
                    setTimeout(() => { form.submit(); }, 300);
                } else {
                    alert("Adresse konnte nicht gefunden werden.");
                }
            });
    }
});
</script>
@endpush
