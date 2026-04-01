<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Employee Terminal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color:rgb(247, 247, 247); color: white; }
    .kpi-card { border-radius: 10px; padding: 20px; text-align: center; font-weight: bold; margin-bottom: 15px; }
    .kpi-card canvas { width: 100% !important; height: 100px !important; }
    .scroll-table-wrapper { overflow: hidden; height: 100%; position: relative; }
    .scroll-table { display: block; width: 100%; }
    .scroll-table thead { display: table; width: 100%; table-layout: fixed; position: sticky; top: 0; z-index: 1; }
    .scroll-table tbody { display: block; animation: scrollLoop 1000s linear infinite; }
    .scroll-table tbody tr { display: table; width: 100%; table-layout: fixed; background-color: #1e293b; animation: rowFade 5s ease-in-out infinite; }
    @keyframes scrollLoop { 0% { transform: translateY(0); } 100% { transform: translateY(-100%); } }
    @keyframes rowFade { 0%, 100% { background-color: #1e293b; } 50% { background-color: #334155; } }
    .table th, .table td { vertical-align: middle; transition: background-color 0.3s ease; }
    .profile-img { width: 36px; height: 36px; object-fit: cover; border-radius: 50%; }
  </style>

    <script>
            // Prevent logout by keeping session alive
            setInterval(() => fetch(window.location.href, { method: 'HEAD' }), 15000);

            // Reload page every 20 seconds without disrupting animation
            setInterval(() => location.reload(), 20000);
        </script>
        <script src="https://unpkg.com/feather-icons"></script>

</head>
<body>



<div class="container mt-4">

<!-- <div class="bg-dark text-white p-3 mb-4">
  <h5>Debug Output</h5>
  <pre>{{ json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
</div> -->
  <div class="row text-white mb-4">
        <!-- <div class="row mb-1" id="summaryCards">
            <div class="col-md-3">
                <div class="card text-white " style="background-color: #164194;">
                    <div class="card-body">
                        <h5 class="card-title">Gesamtarbeitszeit</h5>
                        <h3 id="summaryTotal" style="font-size: 20px;">-- h</h3>
                        <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                            <canvas id="chartTotal" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Belegte Zeit</h5>
                        <h3 id="summaryBusy" style="font-size: 20px;">-- h</h3>
                        <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                            <canvas id="chartBusy" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white " style="background-color: #93c21c;">
                    <div class="card-body">
                        <h5 class="card-title">Freie Zeit</h5>
                        <h3 id="summaryFree" style="font-size: 20px;">-- h</h3>
                        <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                            <canvas id="chartFree" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white " style="background-color: #164194;">
                    <div class="card-body">
                        <h5 class="card-title">Auslastung</h5>
                        <h3 id="summaryUtil" style="font-size: 20px;">-- %</h3>
                        <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                            <canvas id="chartUtil" height="60"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
<!--
        <div class="row text-center mb-1 mt-2" id="summaryStats">
            <div class="col-md-2">
                <div class="border rounded py-2" style="background-color: #164194;">
                    <strong>Gesamt Mitarbeiter</strong>
                    <div id="totalEmployees" class="h4">--</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded py-2  text-white" style="background-color: #93c21c;">
                    <strong>Montage</strong>
                    <div id="montagePercent" class="h4">-- %</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded py-2  text-white" style="background-color: #93c21c;">
                    <strong>Büro</strong>
                    <div id="officePercent" class="h4">-- %</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded py-2  text-white" style="background-color: #93c21c;">
                    <strong>Unbekannt</strong>
                    <div id="unknownPercent" class="h4">-- %</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded py-2  text-dark" style="background-color: #93c21c;">
                    <strong>Urlaub</strong>
                    <div id="leaveCount" class="h4">--</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded py-2   text-white" style="background-color: #93c21c;">
                    <strong>Krank</strong>
                    <div id="sickCount" class="h4">--</div>
                </div>
            </div>
        </div> -->

  </div>

    <div class="title" style="display: flex; justify-content: space-between; color: #93c21c;">
            <h1>Heutiger Plan und Termine</h1>
            <h3><i data-feather="calendar" style="font-size:20px"></i> {{ \Carbon\Carbon::parse(now())->isoFormat('DD.MM.YYYY')}}</h2>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card  text-white border-light" style="background-color:#164194">
            <div class="card-body d-flex justify-content-around align-items-center flex-wrap">
                <div class="d-flex align-items-center me-4 mb-2">
                <div class="rounded-circle bg-danger me-2" style="width: 16px; height: 16px;"></div>
                <span> Vergangene Termine</span>
                </div>
                <div class="d-flex align-items-center me-4 mb-2">
                <div class="rounded-circle bg-warning me-2" style="width: 16px; height: 16px;"></div>
                <span> Bald beginnende oder laufende Termine</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-secondary me-2" style="width: 16px; height: 16px;"></div>
                <span> Geplante zukünftige Termine</span>
                </div>
            </div>
            </div>
        </div>
        </div>


    <div class="scroll-table-wrapper">
    <div class="table-responsive">
        <table class="table table-dark table-bordered text-center scroll-table">
        <thead class="table-secondary text-dark">
            <tr>
            <th>Mitarbeiter</th>
            <th>Tätigkeit</th>
            <th>Ort</th>
            <th>Startzeit</th>
            <th>Endzeit</th>
            <th>Typ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($all as $entry)
            @php
                $isMontage = collect($entry->employees)->contains(fn($emp) => isset($emp->type) && $emp->type === 'Montage');

                $rowClass = match($entry->status_color ?? 'white') {
                'red' => 'table-danger',    // passed
                'orange' => 'table-warning', // in progress or starting soon
                default => '',
                };
            @endphp

            @if($isMontage)
                <tr class="{{ $rowClass }}">
                <td>
                    @foreach(collect($entry->employees)->unique('image') as $emp)
                    <img src="{{ asset('images/employee/' . $emp->image) }}" class="rounded-circle me-1" width="35" height="35" alt="{{ $emp->name }}">
                    @endforeach
                </td>
                <td>{{ $entry->group_key }}</td>
                <td>{{ $entry->ort }}</td>
                <td>{{ $entry->start_time }}</td>
                <td>{{ $entry->end_time }}</td>
                <td>
                    {{ $entry->type }}
                    <br>
                    <small>{{ $entry->start_date }} → {{ $entry->end_date }}</small>
                </td>
                </tr>
            @endif
            @endforeach
        </tbody>
        </table>
    </div>
</div>

</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Replace the "--" placeholders with actual values
    document.getElementById('summaryTotal').innerText = '{{ $totalHours }} h';
    document.getElementById('summaryBusy').innerText = '{{ $usedHours }} h';
    document.getElementById('summaryFree').innerText = '{{ $freeHours }} h';
    document.getElementById('summaryUtil').innerText = '{{ $loadPercentage }} %';

    document.getElementById('totalEmployees').innerText = '{{ $availableCount }}';
    document.getElementById('montagePercent').innerText = '{{ round(($montageCount / max($availableCount,1)) * 100, 1) }} %';
    document.getElementById('officePercent').innerText = '{{ round(($officeCount / max($availableCount,1)) * 100, 1) }} %';
    document.getElementById('unknownPercent').innerText = '{{ round((($availableCount - $montageCount - $officeCount) / max($availableCount,1)) * 100, 1) }} %';
    document.getElementById('leaveCount').innerText = '{{ $leaveCount ?? 0 }}';
    document.getElementById('sickCount').innerText = '{{ $sickCount ?? 0 }}';

    // Chart.js setup
    const chartConfigs = [
        { id: 'chartTotal', data: {{ $totalHours }}, label: 'Total', bg: '#0d6efd' },
        { id: 'chartBusy', data: {{ $usedHours }}, label: 'Used', bg: '#dc3545' },
        { id: 'chartFree', data: {{ $freeHours }}, label: 'Free', bg: '#198754' },
        { id: 'chartUtil', data: {{ $loadPercentage }}, label: 'Util', bg: '#ffc107' }
    ];

    chartConfigs.forEach(conf => {
        const ctx = document.getElementById(conf.id).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [conf.label],
                datasets: [{
                    data: [conf.data],
                    backgroundColor: conf.bg,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { beginAtZero: true } }
            }
        });
    });
});
</script>


<script>
    feather.replace();
</script>



</body>
</html>
