@extends('admin.layouts.app')
@section('title')
Tagesbericht
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"> <!-- Optional dark mode -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
    tr[aria-expanded="true"] {
        background-color: #f0f8ff;
    }
</style>


<style>
    .futuristic-card {
          background: #ffffff;
            color: #d1d5db;
            border-radius: 1rem;
            padding: 1.5rem;
    }

    .progress {
        background-color: #e8e8e8;
        height: 1.2rem;
        border-radius: 2rem;
        overflow: hidden;
        position: relative;
    }

  
    .meta {
      font-size: 0.9rem;
      color: #8b949e;
    }

    .meta span {
      color: #58a6ff;
      font-weight: 500;
    }
  </style>
@endsection

@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">TAGESBERICHT</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Bericht
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="content-body"> 
                <div class="row">
                    <div class="col-md-12 mb-1">
                        <div class="d-flex justify-content-end align-items-center">

                            <div class="btn-group">
                                <input type="text" id="datePicker" placeholder="Select Date" style="padding: 8px; font-size: 16px;">

                            </div> 
                            <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="filterDropdownBtn">
                                            <i class="feather icon-filter"></i> <span class="ml-1">Daily</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right filter">
                                            <a class="dropdown-item filter-option active" href="#" data-value="daily">Daily</a>
                                            <a class="dropdown-item filter-option" href="#" data-value="weekly">Weekly</a>
                                            <a class="dropdown-item filter-option" href="#" data-value="monthly">Monthly</a>
                                        </div>
                                    </div>
                                </div>

                            <div class="input-group" style="max-width: 300px;">
                                <input type="text" class="form-control" name="search" placeholder="Search employee">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary waves-effect waves-light">
                                        Search
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-warning" onclick="verifyAdmin()">🔐 Admin-Zugang</button>

                        </div>
                    </div>
                </div>

                <div class="container">
                      <div class="row" id="daily_report_table">
                            <div class="table-responsive mt-1">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mitarbeiter</th>
                                            <th>Fortschritt</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="report_table"></tbody>
                                </table>
                                <div id="pagination" class="mt-3 text-center"></div>
                            </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->

  

@endsection



@section('script')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/de.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Initialize with German locale -->
<script>
let currentPage = 1;
let selectedRange = 'daily';
let currentSearch = '';
let selectedDate = moment().format('YYYY-MM-DD');

const reportDate = selectedDate || moment().format('YYYY-MM-DD');
const formattedDate = formatDate(reportDate);

function fetchEmployees() {
    $.ajax({
        url: "{{ route('daily.report.employee.list.search') }}",
        method: "GET",
        data: {
            page: currentPage,
            filter: selectedRange,
            search: currentSearch,
            date: selectedDate
        },
        success: function (res) {
            Swal.close();
            if (res.data && res.data.length > 0) {
                renderTable(res.data);
            } else {
                $(".report_table").html(`<tr><td colspan="3" class="text-center">Keine Ergebnisse gefunden.</td></tr>`);
            }
            renderPagination(res.current_page || 1, res.last_page || 1);
        },
        error: function () {
            Swal.close();
            alert("Fehler beim Laden der Mitarbeiterdaten.");
        }
    });
}

flatpickr("#datePicker", {
    locale: "de",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d. F Y",
    defaultDate: "today",
    allowInput: true,
    onChange: function(selectedDates, dateStr) {
        selectedDate = dateStr;
        currentPage = 1;
        Swal.fire({
            title: 'Lade Daten...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetchEmployees();
    }
});

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function renderTable(data) {
    const tbody = $(".report_table");
    tbody.html('');
    const imagePath = @json(asset('images/employee'));

    data.forEach((emp, index) => {
        const workedMinutes = Math.max(parseFloat(emp.worked_minutes) || 0, 0);
        const expectedMinutes = Math.max(parseFloat(emp.expected_minutes), 1); // force minimum 1 minute
        const remainingMinutes = Math.max(expectedMinutes - workedMinutes, 0);

        const workedHours = (workedMinutes / 60).toFixed(2);
        const expectedHours = (expectedMinutes / 60).toFixed(2);
        const remainingHours = (remainingMinutes / 60).toFixed(2);

        let percent = expectedMinutes > 0
                    ? Math.round((workedMinutes / expectedMinutes) * 100)
                    : 0;
        
                    percent = Math.min(Math.max(percent, 0), 100);

        console.log(`== ${emp.name} ${emp.lastname} ==`);
        console.log('Worked Minutes:', workedMinutes);
        console.log('Expected Minutes:', expectedMinutes);
        console.log('Progress:', percent);
        console.log('Events:', emp.events);

        const collapseId = `collapseEvents${index}`;
        const reportDateForRow = emp.date ?? selectedDate;
        const formattedDate = formatDate(reportDateForRow);

        const reportBtnColor = emp.has_report ? 'btn-success' : 'btn-danger';
        const reportText = emp.has_report
            ? '<div class="text-success mt-1 small">Tagesbericht vorhanden.</div>'
            : '<div class="text-danger mt-1 small">Kein Tagesbericht eingetragen.</div>';

        const eventsWithCoords = emp.events.filter(e => e.type === 'appointment' && e.latitude && e.longitude);
        const eventsJson = encodeURIComponent(JSON.stringify(eventsWithCoords));

        const mapButton = eventsWithCoords.length > 0
            ? `<button type="button" class="btn btn-icon rounded-circle btn-danger ms-1"
                    onclick="showMap(JSON.parse(decodeURIComponent('${eventsJson}')))">
                    <i class="feather icon-map-pin"></i>
               </button>`
            : '';

        const trMain = `
        <tr class="employee-row" data-toggle="collapse" data-target="#${collapseId}" style="cursor:pointer;">
            <td>
                <div class="d-flex align-items-center">
                    <img src="${imagePath}/${emp.image}" width="32" height="32" class="rounded-circle me-2 mr-1">
                    <div>
                        <p class="mb-0">${emp.name} ${emp.lastname}</p>
                        <p>${formattedDate}</p>
                    </div>
                </div>
            </td>
            <td>
                <div class="progress mb-1">
                    <div class="progress-bar ${percent < 50 ? 'bg-danger' : percent < 80 ? 'bg-warning' : 'bg-success'}"
                         role="progressbar" style="width: ${percent}%;">
                        ${percent}%
                    </div>
                </div>
                <div class="d-flex justify-content-between small">
                    <div><strong>Geleistet:</strong> ${workedHours}h</div>
                    <div><strong>Verbleibend:</strong> ${remainingHours}h</div>
                    <div><strong>Gesamt:</strong> ${expectedHours}h</div>
                </div>
            </td>
            <td>
                <a type="button"
                   class="btn btn-icon rounded-circle waves-effect waves-light ${reportBtnColor}"
                   href="/employee_daily_report/${emp.employee_id}/${reportDateForRow}/${reportDateForRow}">
                   <i class="feather icon-eye"></i>
                </a>
                ${reportText}
                ${mapButton}
            </td>
        </tr>`;

        const trCollapse = `
        <tr class="collapse-row">
            <td colspan="3" class="p-0 border-0">
                <div id="${collapseId}" class="collapse bg-white p-0">
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 80px;">Typ</th>
                                        <th>Titel</th>
                                        <th>Datum</th>
                                        <th>Zeit</th>
                                        <th>Adresse</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${emp.events.map(event => `
                                        <tr>
                                            <td><span class="badge bg-${event.type === 'task' ? 'info' : 'success'} text-uppercase">
                                                ${event.type === 'appointment' ? 'Termin' : 'Aufgabe'}
                                            </span></td>
                                            <td>${event.title ?? '-'}</td>
                                            <td>${formatDate(event.start_date)} – ${formatDate(event.end_date)}</td>
                                            <td>${event.start_time ?? '-'} – ${event.end_time ?? '-'}</td>
                                            <td>
                                                ${event.type === 'appointment'
                                                    ? `<div>${event.full_address ?? '-'}</div>`
                                                    : '<span class="text-muted">–</span>'}
                                            </td>
                                        </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </td>
        </tr>`;

        tbody.append(trMain + trCollapse);
    });
}


function renderPagination(current, last) {
    const container = $("#pagination");
    container.html('');
    for (let i = 1; i <= last; i++) {
        container.append(`<button class="btn btn-sm btn-${i === current ? 'primary' : 'light'} mx-1" onclick="goToPage(${i})">${i}</button>`);
    }
}

function goToPage(page) {
    currentPage = page;
    fetchEmployees();
}

function showMap(events) {
    Swal.fire({
        title: 'Standorte der Termine',
        html: `<div id="leafletMap" style="width:100%; height:500px;"></div>`,
        width: '80%',
        didOpen: () => {
            const map = L.map('leafletMap').setView([51.1657, 10.4515], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            events.forEach(event => {
                const marker = L.marker([event.latitude, event.longitude]).addTo(map);
                marker.bindPopup(`<strong>${event.title ?? 'Termin'}</strong><br>${event.full_address ?? ''}`);
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;
                    const userMarker = L.marker([lat, lon], {
                        icon: L.icon({
                            iconUrl: 'https://cdn-icons-png.flaticon.com/512/4870/4870326.png',
                            iconSize: [32, 32],
                        })
                    }).addTo(map);
                    userMarker.bindPopup("Ihr Standort").openPopup();

                    if (events.length > 0) {
                        const line = L.polyline([[lat, lon], [events[0].latitude, events[0].longitude]], {
                            color: 'blue',
                            weight: 3,
                        }).addTo(map);
                        map.fitBounds(line.getBounds());
                    } else {
                        map.setView([lat, lon], 12);
                    }
                });
            }
        }
    });
}

$('input[name="search"]').on('input', function () {
    currentSearch = $(this).val();
    currentPage = 1;
    fetchEmployees();
});

$('.dropdown-menu .dropdown-item').on('click', function (e) {
    e.preventDefault();
    selectedRange = $(this).data('value');
    $('#filterDropdownBtn span').text($(this).text());
    currentPage = 1;
    Swal.fire({ title: 'Lade Daten...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetchEmployees();
});

$(document).ready(() => fetchEmployees());
</script>

<script>
function verifyAdmin() {
  Swal.fire({
    title: 'Admin-Zugang',
    html:
      `<input type="email" id="swal-email" class="swal2-input" placeholder="E-Mail-Adresse">` +
      `<input type="password" id="swal-password" class="swal2-input" placeholder="Passwort">`,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Einloggen',
    cancelButtonText: 'Abbrechen',
    preConfirm: () => {
      const email = document.getElementById('swal-email').value;
      const password = document.getElementById('swal-password').value;
      if (!email || !password) {
        Swal.showValidationMessage('Bitte E-Mail und Passwort eingeben');
        return false;
      }
      return { email, password };
    }
  }).then((result) => {
    if (result.isConfirmed && result.value) {
      const { email, password } = result.value;

      Swal.fire({
        title: 'Überprüfe Zugang...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      fetch('/verify-admin', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
      })
      .then(res => res.json())
      .then(data => {
        Swal.close();
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Zugriff gewährt',
            showConfirmButton: false,
            timer: 1000
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Zugriff verweigert',
            text: 'E-Mail oder Passwort ist falsch oder keine Admin-Rechte.',
          });
        }
      })
      .catch(() => {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Fehler',
          text: 'Serverfehler beim Überprüfen des Zugangs.',
        });
      });
    }
  });
}


</script>

@endsection