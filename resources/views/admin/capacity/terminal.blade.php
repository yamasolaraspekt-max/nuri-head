<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Employee Terminal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <style>
    body { background-color:rgb(247, 247, 247); color: #1f2937; }
    .kpi-card { border-radius: 10px; padding: 20px; text-align: center; font-weight: bold; margin-bottom: 15px; }
    .scroll-table-wrapper { overflow: hidden; height: 100%; position: relative; }
    .scroll-table { display: block; width: 100%; }
    .scroll-table thead { display: table; width: 100%; table-layout: fixed; position: sticky; top: 0; z-index: 1; }
    .scroll-table tbody { display: block; animation: scrollLoop 1000s linear infinite; }
    .scroll-table tbody tr { display: table; width: 100%; table-layout: fixed; background-color: #1e293b; color: white; animation: rowFade 5s ease-in-out infinite; }
    @keyframes scrollLoop { 0% { transform: translateY(0); } 100% { transform: translateY(-100%); } }
    @keyframes rowFade { 0%, 100% { background-color: #1e293b; } 50% { background-color: #334155; } }
    .profile-img { width: 36px; height: 36px; object-fit: cover; border-radius: 50%; }
  </style>
</head>
<body>
<div class="container mt-4">

    <div class="row mb-2" id="summaryCards">
      <div class="col-md-3">
          <div class="card text-white bg-primary">
              <div class="card-body">
                  <h5 class="card-title">Gesamtarbeitszeit</h5>
                  <h3 id="summaryTotal">-- h</h3>
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
                  <h3 id="summaryBusy">-- h</h3>
                  <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                      <canvas id="chartBusy" height="60"></canvas>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card text-white bg-success">
              <div class="card-body">
                  <h5 class="card-title">Freie Zeit</h5>
                  <h3 id="summaryFree">-- h</h3>
                  <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                      <canvas id="chartFree" height="60"></canvas>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card text-white bg-warning">
              <div class="card-body">
                  <h5 class="card-title">Auslastung</h5>
                  <h3 id="summaryUtil">-- %</h3>
                  <div style="overflow-x: auto; max-width: 100%;" class="chart-container">
                      <canvas id="chartUtil" height="60"></canvas>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <div class="row text-center mb-3" id="summaryStats">
  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-light">
      <strong>Gesamt Mitarbeiter</strong>
      <div id="totalEmployees" class="h4">--</div>
    </div>
  </div>

  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-danger text-white" id="montageStats">
      <strong>Montage</strong>
      <div class="h5 hours">-- h</div>
      <div class="small percent">-- %</div>
    </div>
  </div>

  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-primary text-white" id="officeStats">
      <strong>Büro</strong>
      <div class="h5 hours">-- h</div>
      <div class="small percent">-- %</div>
    </div>
  </div>

  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-secondary text-white" id="unbekanntStats">
      <strong>Unbekannt</strong>
      <div class="h5 hours">-- h</div>
      <div class="small percent">-- %</div>
    </div>
  </div>

  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-warning text-dark" id="urlaubStats">
      <strong>Urlaub</strong>
      <div class="h5 hours">-- h</div>
      <div class="small percent">-- %</div>
    </div>
  </div>

  <div class="col-md-2 mb-2">
    <div class="border rounded py-2 bg-info text-white" id="krankStats">
      <strong>Krank</strong>
      <div class="h5 hours">-- h</div>
          <div class="small percent">-- %</div>
        </div>
      </div>
    </div>

  <!-- Filter Dropdowns -->
  <form method="GET" action="{{ route('terminal') }}">
    <div class="row mb-4">
      <div class="col-md-2 col-sm-6 mb-1">
        <select name="employee_id" class="form-control select2">
          <option value="">Alle Mitarbeiter</option>
          @foreach ($employeesList as $emp)
            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} {{ $emp->lastname }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6 mb-1">
        <select name="department" class="form-control select2">
          <option value="">Alle Abteilungen</option>
          @foreach ($departments as $department)
            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6 mb-1">
        <select name="branch" class="form-control select2">
          <option value="">Alle Standorte</option>
          @foreach ($branches as $branch)
            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 col-sm-6 mb-1">
        <select name="office" class="form-control select2">
          <option value="">Alle Mitarbeiter</option>
          <option value="Montage" {{ request('office') == 'Montage' ? 'selected' : '' }}>Montage</option>
          <option value="Office" {{ request('office') == 'Office' ? 'selected' : '' }}>Büro</option>
        </select>
      </div>

      <div class="col-md-2 col-sm-6 mb-1">
        <select id="period" class="form-control select2 w-100">
          <option value="day" {{ request('period', 'day') === 'day' ? 'selected' : '' }}>Heute</option>
          <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Diese Woche</option>
          <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Diesen Monat</option>
          <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Diesen Jahr</option>

        </select>
    </div>

    <div class="col-md-2 col-sm-6 mb-1">
     <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
    </div>
      <div class="col-md-2 col-sm-6 mb-1 d-flex gap-1">
        <button type="submit" class="btn btn-primary w-100">Laden</button>
        <a href="{{ route('terminal') }}" class="btn btn-secondary w-100">Zurücksetzen</a>
      </div>
    </div>
  </form>

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
            <th>Dauer</th>
            <th>Typ</th>
          </tr>
        </thead>
        <tbody>
          @foreach($result as $entry)
            @php
              $items = $entry['today']['items'];
            @endphp
            @if($items->isEmpty())
              <tr>
                <td>
                  <img src="{{ $entry['employee']['image'] }}" class="profile-img me-1" alt="{{ $entry['employee']['name'] }}">
                </td>
                <td colspan="6">Keine Aufgaben oder Termine</td>
              </tr>
            @else
              @foreach($items as $item)
                <tr>
                  <td>
                    <img src="{{ $entry['employee']['image'] }}" class="profile-img me-1" alt="{{ $entry['employee']['name'] }}">
                  </td>
                  <td>{{ $item['title'] }}</td>
                  <td>{{ $item['location'] ?? '-' }}</td>
                  <td>{{ $item['start'] ?? '-' }}</td>
                  <td>{{ $item['end'] ?? '-' }}</td>
                  <td>{{ $item['hours'] ?? 0 }} h</td>
                  <td>{{ $item['type'] }}</td>
                </tr>
              @endforeach
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('.select2').select2();
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const total = {{ $totalHours }};

    const data = {
        Montage: {
            hours: {{ $montageHours }},
            percent: total > 0 ? (({{ $montageHours }} / total) * 100).toFixed(1) : 0
        },
        Office: {
            hours: {{ $officeHours }},
            percent: total > 0 ? (({{ $officeHours }} / total) * 100).toFixed(1) : 0
        },
        Unbekannt: {
            hours: {{ $unknownHours }},
            percent: total > 0 ? (({{ $unknownHours }} / total) * 100).toFixed(1) : 0
        },
        Urlaub: {
            hours: {{ $leaveHours }},
            percent: total > 0 ? (({{ $leaveHours }} / total) * 100).toFixed(1) : 0
        },
        Krank: {
            hours: {{ $sickHours }},
            percent: total > 0 ? (({{ $sickHours }} / total) * 100).toFixed(1) : 0
        }
    };

    // Update values in the DOM
    Object.entries(data).forEach(([type, obj]) => {
        const el = document.getElementById(`${type.toLowerCase()}Stats`);
        if (el) {
            el.querySelector('.hours').innerText = `${obj.hours.toFixed(2)} h`;
            el.querySelector('.percent').innerText = `${obj.percent} %`;
        }
    });

    // Update top summary
    document.getElementById('summaryTotal').innerText = '{{ $totalHours }} h';
    document.getElementById('summaryBusy').innerText = '{{ $usedHours }} h';
    document.getElementById('summaryFree').innerText = '{{ $freeHours }} h';
    document.getElementById('summaryUtil').innerText = '{{ $loadPercentage }} %';

    // Chart rendering
    const charts = [
        { id: 'chartTotal', value: {{ $totalHours }}, color: '#0d6efd' },
        { id: 'chartBusy', value: {{ $usedHours }}, color: '#dc3545' },
        { id: 'chartFree', value: {{ $freeHours }}, color: '#198754' },
        { id: 'chartUtil', value: {{ $loadPercentage }}, color: '#ffc107' }
    ];

    charts.forEach(c => {
        const ctx = document.getElementById(c.id).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [''],
                datasets: [{
                    data: [c.value],
                    backgroundColor: c.color,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: { display: false },
                    y: { beginAtZero: true, display: false }
                }
            }
        });
    });
});
</script>

</body>
</html>
