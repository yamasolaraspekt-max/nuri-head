@extends('admin.layouts.app')

@section('title')  Mitarbeiterkapazitätsstatus  @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<style>
.badge-task { background-color: #007bff; }
.badge-appointment { background-color: #28a745; }
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
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kapazitätsübersicht</h4>
                    <div class="form-inline">
                        <select id="filterDepartment" class="form-control select2 mr-1">
                            <option value="">Alle Abteilungen</option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}"> {{ $department->department_name }}</option>
                            @endforeach
                        </select>
                        <select id="filterBranch" class="form-control select2 mr-1">
                            <option value="">Alle Standorte</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}"> {{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                        <select id="filterPeriod" class="form-control select2">
                            <option value="day" >Heute</option>
                            <option value="week" selected>Diese Woche</option>
                            <option value="month">Diesen Monat</option>
                            <option value="year" >Diesen Jahr</option>
                        </select>
                        <input type="date" id="filterDate" class="form-control ml-1" value="{{ date('Y-m-d') }}">
                        <button id="loadCapacity" class="btn btn-primary ml-1">Laden</button>
                        <button id="exportPdf" class="btn btn-danger ml-1">PDF Export</button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="head">
                            <tr>
                                <th>Bild</th>
                                <th>Name</th>
                                <th>Abteilung</th>
                                <th>Position</th>
                                <th>Arbeitszeit</th>
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
<script>
$(document).ready(function() {
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

    function loadCapacityData() {
        let period = $('#filterPeriod').val();
        let date = $('#filterDate').val();
        let department = $('#filterDepartment').val();
        let branch = $('#filterBranch').val();

        $.ajax({
            url: "{{ route('employee.capacity.list') }}",
            type: "GET",
            data: { period, date, department, branch },
            success: function(response) {
                let html = '';
                response.employees.forEach((emp, index) => {
                    const total = emp.calendar.reduce((sum, c) => sum + (c.working_hours || 0), 0);
                    const busy = emp.calendar.reduce((sum, c) => sum + (c.busy_hours || 0), 0);
                    const free = emp.calendar.reduce((sum, c) => sum + (c.free_hours || 0), 0);
                    const utilization = total > 0 ? ((busy / total) * 100).toFixed(2) : 0;

                    html += `
                        <tr class="bg-light" data-toggle="collapse" data-target="#calendar-${index}" aria-expanded="false" aria-controls="calendar-${index}" style="cursor:pointer">
                            <td><img src="${emp.employee.image}" class="rounded-circle" width="40" height="40"></td>
                            <td>${emp.employee.name}</td>
                            <td>${emp.employee.department ?? '-'}</td>
                            <td>${emp.employee.position ?? ''}</td>
                            <td>${total} h</td>
                            <td>${busy} h</td>
                            <td>${free} h</td>
                            <td>
                                <div class="progress position-relative" style="height: 20px" title="${utilization}% genutzt">
                                    <div class="progress-bar bg-${utilization >= 80 ? 'danger' : utilization >= 50 ? 'warning' : 'success'}" role="progressbar" style="width: ${utilization}%"></div>
                                    <div class="progress-bar-text">${utilization}%</div>
                                </div>
                            </td>
                            <td><i class="feather icon-chevron-down"></i></td>
                        </tr>
                        <tr class="collapse" id="calendar-${index}"><td colspan="9"><div class="collapse-content">`;

                    const grouped = groupByKW(emp.calendar);
                    for (const kw in grouped) {
                        const rows = grouped[kw];
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
                $('#capacityWrapper').html(html);
            },
            error: function() {
                toastr.error("Fehler beim Laden der Kapazitätsdaten.");
            }
        });
    }

    loadCapacityData();
    $('#loadCapacity').click(loadCapacityData);
    $('#exportPdf').click(() => window.print());
    $('#exportCsv').click(() => exportTableToCSV("kapazitaet-export.csv"));
});
</script>
@endsection
