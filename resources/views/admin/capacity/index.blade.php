@extends('admin.layouts.app')

@section('title')  Mitarbeiterkapazitätsstatus  @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<style>
.badge-task { background-color: #74b2d4; }
.badge-appointment { background-color: #93c21c; }
.badge-project { background-color: #17a2b8; }
.badge-leave { background-color: #ffc107; }
.badge-sick { background-color: #dc3545; }
.collapse-content {
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #e3e3e3;
    border-top: none;
    margin-top: -1px;
}
.task-row {
    border-bottom: 1px dashed #ccc;
    padding-bottom: 10px;
    margin-bottom: 10px;
}
.utilization-low { background-color: #e0f7fa; }
.utilization-medium { background-color: #fff3cd; }
.utilization-high { background-color: #f8d7da; }
.progress-bar-text {
    position: absolute;
    width: 100%;
    text-align: center;
    font-weight: bold;
    color: #000;
}
.bg-light{
    background-color: #ffffff !important;
}
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Mitarbeiterkapazitätsstatus</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Mitarbeiterlisten</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
                <div class="text-right mb-1">
                    <button id="toggleChartView" class="btn btn-outline-secondary btn-sm">
                        Ansicht wechseln: Balken / Linie
                    </button>
                </div>

                <div class="row mb-1" id="summaryCards">
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

                <div class="row text-center mb-1" id="summaryStats">
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-light">
                            <strong>Gesamt Mitarbeiter</strong>
                            <div id="totalEmployees" class="h4">--</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-danger text-white">
                            <strong>Montage</strong>
                            <div id="montagePercent" class="h4">-- %</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-primary text-white">
                            <strong>Büro</strong>
                            <div id="officePercent" class="h4">-- %</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-secondary text-white">
                            <strong>Unbekannt</strong>
                            <div id="unknownPercent" class="h4">-- %</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-warning text-dark">
                            <strong>Urlaub</strong>
                            <div id="leaveCount" class="h4">--</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded py-2 bg-info text-white">
                            <strong>Krank</strong>
                            <div id="sickCount" class="h4">--</div>
                        </div>
                    </div>
                </div>



            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kapazitätsübersicht</h4>
                    <div class="col-12 mt-1">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 mb-1">
                                <select id="filterEmployee" class="form-control select2 w-100">
                                    <option value="">Alle Mitarbeiter</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}">
                                            {{ $emp->name }} {{ $emp->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-1">
                                <select id="filterDepartment" class="form-control select2 w-100">
                                    <option value="">Alle Abteilungen</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-1">
                                <select id="filterBranch" class="form-control select2 w-100">
                                    <option value="">Alle Standorte</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ✅ New Office Filter -->
                            <div class="col-md-2 col-sm-6 mb-1">
                                <select id="filterOffice" class="form-control select2 w-100">
                                    <option value="">Alle Mitarbeiter</option> 
                                    <option value="Montage">Montage</option>
                                    <option value="Office">Büro</option>
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-1">
                                <select id="filterPeriod" class="form-control select2 w-100">
                                    <option value="day" selected>Heute</option>
                                    <option value="week" >Diese Woche</option>
                                    <option value="month">Diesen Monat</option>
                                    <option value="year">Diesen Jahr</option>
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-6 mb-1">
                                <input type="date" id="filterDate" class="form-control w-100" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2 col-sm-6 mb-1 d-flex gap-1">
                                <button id="loadCapacity" class="btn btn-primary w-100">Laden</button>
                                <button id="exportPdf" class="btn btn-danger w-100 ml-1">PDF Export</button>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="head">
                            <tr>
                                <th>Bild</th>
                                <th>Name</th>
                                <th>Abteilungen / Positionen</th>
                                <th>Arbeitszeit Büro</th>
                                <th>Arbeitszeit Montage</th>
                                <th>Arbeitszeit (gesamt)</th>
                                <th>Belegt</th>
                                <th>Frei</th>
                                <th>Auslastung</th>
                                <th>Details</th>
                            </tr>
                        </thead>

                        <tbody id="capacityWrapper"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartCache = {};
    let chartMode = 'line'; // default mode

$(document).ready(function () {

    $('#toggleChartView').click(function () {
        chartMode = chartMode === 'line' ? 'bar' : 'line';
        loadCapacitySummary(); // re-render with new mode
    });

    $('#filterEmployee').on('change', function () {
        loadCapacitySummary();
        loadCapacityData();
    });

    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll("table tr");
        for (let row of rows) {
            const cols = row.querySelectorAll("td, th");
            const rowData = Array.from(cols).map(col => '"' + col.innerText.replace(/\n/g, ' ').trim() + '"');
            csv.push(rowData.join(","));
        }
        const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(csvFile);
        link.download = filename;
        link.click();
    }

    function groupByKW(calendar) {
        const weeks = {};
        calendar.forEach(row => {
            const kw = moment(row.date).isoWeek();
            const year = moment(row.date).isoWeekYear();
            const key = `KW ${kw} (${year})`;
            if (!weeks[key]) weeks[key] = [];
            weeks[key].push(row);
        });
        return weeks;
    }

    function renderMiniChart(id, labels, values, color) {
        const ctx = document.getElementById(id);
        if (!ctx) return;

        // Dynamically set canvas width for large data (e.g. yearly)
        ctx.width = Math.max(500, labels.length * 30);

        // Destroy previous chart if it exists
        if (chartCache[id]) {
            chartCache[id].destroy();
        }

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: color,
                    backgroundColor: 'rgba(255,255,255,0.05)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        display: true,
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 12
                        }
                    },
                    y: { display: false }
                }
            }
        });

        chartCache[id] = chart;
    }

    function loadCapacitySummary() {
        const period = $('#filterPeriod').val();
        const date = $('#filterDate').val();
        const department = $('#filterDepartment').val();
        const branch = $('#filterBranch').val();
        const employee_id = $('#filterEmployee').val();
        const office = $('#filterOffice').val();

        $.ajax({
            url: "{{ route('employee.capacity.summary') }}",
            type: "GET",
            data: { period, date, department, branch, employee_id, office },

            success: function (summary) {
                $('#summaryFree').text(summary.total_free + ' h');
                $('#summaryBusy').text(summary.total_busy + ' h');
                $('#summaryTotal').text(summary.total_hours + ' h');
                $('#summaryUtil').text(summary.utilization_percent + '%');

                const labels = summary.chart.labels;
                const totalData = summary.chart.total;
                const busyData = summary.chart.busy;
                const freeData = summary.chart.free;

                const utilizationData = totalData.map((t, i) => {
                    const busy = busyData[i] ?? 0;
                    return t > 0 ? ((busy / t) * 100).toFixed(2) : 0;
                });

                if (chartMode === 'line') {
                    renderMiniChart('chartTotal', labels, totalData, '#ffffff');
                    renderMiniChart('chartBusy', labels, busyData, '#ffffff');
                    renderMiniChart('chartFree', labels, freeData, '#ffffff');
                    renderMiniChart('chartUtil', labels, utilizationData, '#ffffff');
                } else {
                    renderBarChart('chartTotal', labels, totalData, 'Gesamtzeit', '#007bff');
                    renderBarChart('chartBusy', labels, busyData, 'Belegt', '#dc3545');
                    renderBarChart('chartFree', labels, freeData, 'Frei', '#28a745');
                    renderBarChart('chartUtil', labels, utilizationData, 'Auslastung %', '#ffc107');
                }
            },
            error: () => toastr.error("Fehler beim Laden der Zusammenfassung.")
        });
    }


    function renderBarChart(id, labels, values, label, color) {
        const ctx = document.getElementById(id);
        if (!ctx) return;

        if (chartCache[id]) {
            chartCache[id].destroy();
        }

        ctx.width = Math.max(500, labels.length * 30);

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: color
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 12
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        chartCache[id] = chart;
    }


    function loadCapacityData() {
        const period = $('#filterPeriod').val();
        const date = $('#filterDate').val();
        const department = $('#filterDepartment').val();
        const branch = $('#filterBranch').val();
        const employee_id = $('#filterEmployee').val();
        const office = $('#filterOffice').val();

        loadCapacitySummary();

        $.ajax({
            url: "{{ route('employee.capacity.list') }}",
            type: "GET",
            data: { period, date, department, branch, employee_id, office },

            success: function (response) {
                const grouped = {
                    Montage: [],
                    Office: [],
                    Unbekannt: []
                };

                response.employees.forEach(emp => {
                    const type = emp.employee.type || 'Unbekannt';
                    grouped[type]?.push(emp);
                });

                let html = '';
                let totalArbeitszeit = 0, totalBelegt = 0, totalFrei = 0;

                Object.entries(grouped).forEach(([type, list], groupIndex) => {
                    if (list.length === 0) return;

                    const headerColor = type === 'Montage' ? 'danger' :
                                        type === 'Office' ? 'primary' : 'secondary';

                    // Gruppenüberschrift
                    html += `
                        <tr class="bg-${headerColor} text-white">
                            <td colspan="10"><strong>${type} Mitarbeiter</strong></td>
                        </tr>`;

                    list.forEach((emp, index) => {
                        const total = emp.calendar.reduce((sum, c) => sum + (c.working_hours || 0), 0);
                        const busy  = emp.calendar.reduce((sum, c) => sum + (c.busy_hours || 0), 0);
                        const free  = emp.calendar.reduce((sum, c) => sum + (c.free_hours || 0), 0);
                        const utilization = total > 0 ? ((busy / total) * 100).toFixed(2) : 0;

                        totalArbeitszeit += total;
                        totalBelegt += busy;
                        totalFrei += free;

                        // All department/position assignments
                        const deptPosList = (emp.employee.department_positions || []).map(dp => {
                            const dept = dp.department || '-';
                            const pos  = dp.position || '';
                            const pct  = (dp.percent ?? 0);
                            const mPct = (dp.montage_percent ?? 0);
                            const oPct = (dp.office_percent ?? 0);
                            const isMain = dp.main && dp.main.toLowerCase() === 'active';

                            return `
                                <div>
                                    <strong>${dept}</strong> – ${pos}
                                    <small class="text-muted">
                                        (${pct}% | M: ${mPct}% / B: ${oPct}%)
                                    </small>
                                    ${isMain ? '<span class="badge badge-success badge-sm ml-25">Haupt</span>' : ''}
                                </div>
                            `;
                        }).join('');

                        // Büro/Montage split for this employee (hours in selected period)
                        const officePercentTotal   = Number(emp.employee.office_percent_total ?? 0);
                        const montagePercentTotal  = Number(emp.employee.montage_percent_total ?? 0);
                        const percentSum           = officePercentTotal + montagePercentTotal || 1;

                        const officeHours  = total * (officePercentTotal / percentSum);
                        const montageHours = total * (montagePercentTotal / percentSum);

                        const typeBadge = type === 'Montage'
                            ? '<span class="badge badge-danger">Montage</span>'
                            : type === 'Office'
                                ? '<span class="badge badge-primary">Büro</span>'
                                : '<span class="badge badge-secondary">Unbekannt</span>';

                        const uniqueId = `calendar-${groupIndex}-${index}`;

                        html += `
                            <tr class="bg-light" data-toggle="collapse" data-target="#${uniqueId}"
                                aria-expanded="false" aria-controls="${uniqueId}" style="cursor:pointer">
                                <td><img src="${emp.employee.image}" class="rounded-circle" width="40" height="40"></td>
                                <td>${emp.employee.name} ${typeBadge}</td>
                                <td>${deptPosList || (emp.employee.department ?? '-')}</td>

                                <td>
                                    ${officePercentTotal.toFixed(1)} %
                                    <br><small>${officeHours.toFixed(2)} h</small>
                                </td>
                                <td>
                                    ${montagePercentTotal.toFixed(1)} %
                                    <br><small>${montageHours.toFixed(2)} h</small>
                                </td>

                                <td>${total.toFixed(2)} h</td>
                                <td>${busy.toFixed(2)} h</td>
                                <td>${free.toFixed(2)} h</td>
                                <td>
                                    <div class="progress position-relative" style="height: 20px" title="${utilization}% genutzt">
                                        <div class="progress-bar bg-${utilization >= 80 ? 'danger' : utilization >= 50 ? 'warning' : 'success'}"
                                            role="progressbar" style="width: ${utilization}%"></div>
                                        <div class="progress-bar-text">${utilization}%</div>
                                    </div>
                                </td>
                                <td><i class="feather icon-chevron-down"></i></td>
                            </tr>
                            <tr class="collapse" id="${uniqueId}">
                                <td colspan="10">
                                    <div class="collapse-content">
                        `;

                        const groupedKW = groupByKW(emp.calendar);
                        for (const kw in groupedKW) {
                            const rows = groupedKW[kw];
                            const sumTotal = rows.reduce((s, r) => s + (r.working_hours || 0), 0);
                            const sumBusy = rows.reduce((s, r) => s + (r.busy_hours || 0), 0);
                            const sumFree = rows.reduce((s, r) => s + (r.free_hours || 0), 0);

                            html += `<div class="mb-3">
                                <h6 class="mb-1 text-primary">${kw}</h6>
                                <div><strong>Arbeitszeit:</strong> ${sumTotal}h | <strong>Belegt:</strong> ${sumBusy}h | <strong>Frei:</strong> ${sumFree}h</div>`;

                            rows.forEach(row => {
                                const items = row.items.map(item => {
                                    const icon = item.type === 'Task' ? '<i class="feather icon-file"></i>' :
                                        item.type === 'Appointment' ? '<i class="feather icon-calendar"></i>' :
                                        item.type === 'Project' ? '🏗️' :
                                        item.type === 'Leave' ? '🏖️' :
                                        item.type === 'Sick' ? '🤒' :
                                        item.type === 'Ticket' ? '🎫' : '❓';

                                    const badge = `badge-${item.type.toLowerCase()}`;
                                    const link = item.type === 'Task' ? `/personal_task_details/${item.id}` :
                                        item.type === 'Appointment' ? `/appointment_details/${item.id}` :
                                        item.type === 'Project' ? `/project_details?id=${item.id}` :
                                        item.type === 'Ticket' ? `/problem/profile/${item.id}` : '#';

                                    return `<div class="task-row">
                                        <a href="${link}" class="badge ${badge}" target="_blank">${icon} ${item.title}</a>
                                        <div><small>${item.start ?? item.date} bis ${item.end ?? item.date}</small></div>
                                    </div>`;
                                }).join('');

                                html += `<div><strong>${row.date ?? 'Datum unbekannt'}:</strong> ${items || '<small class="text-muted">Keine Einträge</small>'}</div>`;
                            });

                            html += `</div>`;
                        }

                        html += `</div></td></tr>`;
                    });
                });

                const totalUtilization = totalArbeitszeit > 0 ? ((totalBelegt / totalArbeitszeit) * 100).toFixed(2) : 0;
                html += `
                    <tr class="bg-secondary text-white font-weight-bold">
                        <td colspan="5" class="text-right">Gesamtsumme:</td>
                        <td>${totalArbeitszeit.toFixed(2)} h</td>
                        <td>${totalBelegt.toFixed(2)} h</td>
                        <td>${totalFrei.toFixed(2)} h</td>
                        <td colspan="2">${totalUtilization}%</td>
                    </tr>`;

                $('#capacityWrapper').html(html);

                const stats = response.stats;
                const total = stats.total || 1;

                $('#totalEmployees').text(stats.total);
                $('#montagePercent').text(((stats.montage / total) * 100).toFixed(1) + '%');
                $('#officePercent').text(((stats.office / total) * 100).toFixed(1) + '%');
                $('#unknownPercent').text(((stats.unknown / total) * 100).toFixed(1) + '%');
                $('#leaveCount').text(stats.leave);
                $('#sickCount').text(stats.sick);
            },

            error: () => toastr.error("Fehler beim Laden der Kapazitätsdaten.")
        });
    }

    $('#filterEmployee').select2({
        templateResult: formatEmployeeOption,
        templateSelection: formatEmployeeOption,
        width: 'resolve'
    });

    function formatEmployeeOption(option) {
        if (!option.id) return option.text;

        const imageUrl = $(option.element).data('image');
        const name = option.text;

        return $(`
            <span class="d-flex align-items-center">
                <img src="${imageUrl}" class="rounded-circle mr-1" style="width:30px;height:30px;object-fit:cover;">
                ${name}
            </span>
        `);
    }


    loadCapacityData();
    $('#loadCapacity').click(loadCapacityData);
    $('#exportPdf').click(() => window.print());
    $('#exportCsv').click(() => exportTableToCSV("kapazitaet-export.csv"));
});
</script>

 
@endsection
