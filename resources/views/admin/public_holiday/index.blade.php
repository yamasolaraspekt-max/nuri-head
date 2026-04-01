@extends('admin.layouts.app')

@section('title') Gesetzliche Feiertage @endsection

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />

<style>
    .fc-event { cursor: pointer; }
    .select2 {
        width:100% !important;
    }
</style>

<style>
    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #343a40;
    }
    .fc .fc-daygrid-event {
        font-size: 0.85rem;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>


<style>
.fc-event-card {
    /* background-color: ##8fc73e; */
    color: white;
    border-radius: 8px;
    padding: 4px 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    font-size: 12px;
    line-height: 1.3;
}
.fc-event-title {
    font-weight: bold;
    font-size: 13px;
}
.fc-event-meta {
    font-style: italic;
    font-size: 11px;
    opacity: 0.9;
}


@media (max-width: 576px) {
    .fc .fc-toolbar-title {
        font-size: 1.2rem;
    }

    .fc-event-card {
        font-size: 11px;
        padding: 4px;
    }

    .fc-event-meta {
        font-size: 10px;
    }
}

</style>


@endsection

@section('content')

<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Gesetzliche Feiertage</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Liste</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <button class="btn btn-primary" data-toggle="modal" data-target="#holidayModal">+ Neuer Feiertag</button>
            </div>
        </div>

        <div class="content-body">
               <div class="row mb-2">
           

                    <div class="col-12 col-xl-6">
                        <div class="card h-100">
                            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                <h4 class="mb-2 mb-sm-0">🗂 Feiertagsliste</h4>
                                <select id="filterYear" class="form-control select2 w-100 w-sm-auto">
                                    <option value="">Alle Jahre</option>
                                    @foreach ([2023, 2024, 2025, 2026] as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-body overflow-auto" id="holidayList" style="max-height: 600px;">
                                <ul class="list-group"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6 mb-2 mb-xl-0">
                        <div class="card h-100">
                            <div class="card-header"><h4>📅 Kalender</h4></div>
                            <div class="card-body overflow-auto">
                                <div id="holidayCalendar"></div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="holidayModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="holidayForm" class="modal-content">
            @csrf
            <input type="hidden" name="holiday_id" id="holiday_id">
            <div class="modal-header">
                <h5 class="modal-title">Feiertag erstellen / bearbeiten</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Feiertagsname</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="z. B. Tag der Deutschen Einheit" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="start_date">Von</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="end_date">Bis</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="state">Bundesland</label>
                    <select class="form-control select2" id="state" name="state" required>
                        <option></option>
                        <option>Baden-Württemberg</option>
                        <option>Bayern</option>
                        <option>Berlin</option>
                        <option>Brandenburg</option>
                        <option>Bremen</option>
                        <option>Hamburg</option>
                        <option>Hessen</option>
                        <option>Mecklenburg-Vorpommern</option>
                        <option>Niedersachsen</option>
                        <option>Nordrhein-Westfalen</option>
                        <option>Rheinland-Pfalz</option>
                        <option>Saarland</option>
                        <option>Sachsen</option>
                        <option>Sachsen-Anhalt</option>
                        <option>Schleswig-Holstein</option>
                        <option>Thüringen</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="city">Stadt</label>
                    <select class="form-control select2" id="city" name="city">
                        <option></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="country">Land</label>
                    <select class="form-control select2" id="country" name="country" readonly >
                        <option selected>Deutschland</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Speichern</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Abbrechen</button>
            </div>
        </form>
    </div>
</div>



<!-- END: Content -->
@endsection

@section('script') 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
const stateCityMap = {!! json_encode([
    "Baden-Württemberg" => ["Stuttgart", "Karlsruhe", "Mannheim", "Freiburg", "Heidelberg", "Ulm", "Reutlingen"],
    "Bayern" => ["München", "Nürnberg", "Augsburg", "Regensburg", "Würzburg", "Ingolstadt", "Neu-Anspach"],
    "Berlin" => ["Berlin"],
    "Brandenburg" => ["Potsdam", "Cottbus", "Brandenburg an der Havel", "Frankfurt (Oder)"],
    "Bremen" => ["Bremen", "Bremerhaven"],
    "Hamburg" => ["Hamburg"],
    "Hessen"=> [
        "Allendorf (Lumda)", "Alsfeld", "Amöneburg", "Aßlar", "Babenhausen", "Bad Arolsen", "Bad Camberg",
        "Bad Hersfeld", "Bad Homburg vor der Höhe", "Bad Karlshafen", "Bad König", "Bad Nauheim",
        "Bad Orb", "Bad Schwalbach", "Bad Soden am Taunus", "Bad Soden-Salmünster", "Bad Sooden-Allendorf",
        "Bad Vilbel", "Bad Wildungen", "Battenberg (Eder)", "Baunatal", "Bebra", "Bensheim", "Biedenkopf",
        "Borken", "Braunfels", "Breuberg", "Bruchköbel", "Büdingen", "Bürstadt", "Butzbach", "Darmstadt",
        "Dieburg", "Diemelstadt", "Dietzenbach", "Dillenburg", "Dreieich", "Eltville am Rhein", "Eppstein",
        "Erbach", "Erlensee", "Eschborn", "Eschwege", "Felsberg", "Flörsheim am Main", "Florstadt",
        "Frankenau", "Frankenberg (Eder)", "Friedberg", "Friedrichsdorf", "Fritzlar", "Fulda", "Gelnhausen",
        "Giessen", "Griesheim", "Groß-Gerau", "Groß-Umstadt", "Groß-Zimmern", "Grünberg", "Gründau",
        "Gudensberg", "Guxhagen", "Habichtswald", "Hanau", "Hattersheim am Main", "Heppenheim",
        "Herborn", "Hofgeismar", "Hofheim am Taunus", "Homberg (Efze)", "Homberg (Ohm)", "Hünfeld",
        "Hungen", "Idstein", "Karben", "Kassel", "Kelkheim (Taunus)", "Korbach", "Langen", "Lauterbach",
        "Limburg an der Lahn", "Linden", "Lollar", "Maintal", "Marburg", "Michelstadt", "Mörfelden-Walldorf",
        "Mühlheim am Main", "Neu-Anspach", "Neu-Isenburg", "Nidderau", "Niedenstein", "Obertshausen",
        "Offenbach am Main", "Oberursel (Taunus)", "Pfungstadt", "Raunheim", "Reichelsheim", "Reinheim",
        "Riedstadt", "Rödermark", "Rodgau", "Rotenburg an der Fulda", "Rüsselsheim am Main", "Schlüchtern",
        "Schotten", "Seligenstadt", "Stadtallendorf", "Taunusstein", "Vellmar", "Viernheim", "Wächtersbach",
        "Wald-Michelbach", "Wetzlar", "Wiesbaden", "Wolfhagen", "Zwingenberg"
    ],
    "Mecklenburg-Vorpommern" => ["Schwerin", "Rostock", "Neubrandenburg", "Greifswald"],
    "Niedersachsen" => ["Hannover", "Braunschweig", "Oldenburg", "Osnabrück"],
    "Nordrhein-Westfalen" => ["Düsseldorf", "Köln", "Dortmund", "Essen", "Bonn", "Wuppertal"],
    "Rheinland-Pfalz" => ["Mainz", "Ludwigshafen", "Koblenz", "Trier"],
    "Saarland" => ["Saarbrücken", "Neunkirchen", "Homburg"],
    "Sachsen" => ["Dresden", "Leipzig", "Chemnitz", "Zwickau"],
    "Sachsen-Anhalt" => ["Magdeburg", "Halle", "Dessau-Roßlau"],
    "Schleswig-Holstein" => ["Kiel", "Lübeck", "Flensburg", "Neumünster"],
    "Thüringen" => ["Erfurt", "Jena", "Gera", "Weimar"]
]) !!};

$('#state').on('change', function () {
    const selected = $(this).val();
    const cities = stateCityMap[selected] || [];
    let options = '<option></option>';
    cities.forEach(city => {
        options += `<option value="${city}">${city}</option>`;
    });
    $('#city').html(options).val(null).trigger('change');
});
</script>


<script>
$(document).ready(function () {
    $('.select2').select2({ placeholder: "Bitte auswählen", allowClear: true });

    const calendar = new FullCalendar.Calendar(document.getElementById('holidayCalendar'), {
        initialView: 'multiMonthYear', // Year overview!
        height: 'auto',
        aspectRatio: 1.5,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,multiMonthYear,listYear'
        },
        views: {
            multiMonthYear: {
                type: 'multiMonth',
                duration: { months: 12 },
                titleFormat: { year: 'numeric' },
            },
            listYear: {
                type: 'list',
                duration: { years: 1 },
                buttonText: 'Liste'
            }
        },
        events: [],

        eventContent: function(arg) {
            return {
                html: `
                    <div class="fc-event-card">
                        <div class="fc-event-title">${arg.event.title}</div>
                        <div class="fc-event-meta">${arg.event.extendedProps.city ?? '-'}, ${arg.event.extendedProps.state ?? ''}</div>
                    </div>
                `
            };
        },

        eventClick: function(info) {
            const e = info.event;
            Swal.fire({
                title: e.title,
                html: `
                    <b>Von:</b> ${e.startStr}<br/>
                    <b>Bis:</b> ${e.endStr}<br/>
                    <b>Stadt:</b> ${e.extendedProps.city ?? '-'}<br/>
                    <b>Bundesland:</b> ${e.extendedProps.state ?? '-'}<br/>
                    <b>Land:</b> ${e.extendedProps.country ?? '-'}
                `,
                icon: 'info'
            });
        },
        eventDisplay: 'block',
        eventColor: '#8fc73e',
        eventTextColor: '#fff',
        nowIndicator: true,
        themeSystem: 'bootstrap'
    });
    calendar.render();


    fetchAndRender();

    function fetchAndRender(year = '') {
        $.get("{{ route('public-holidays.fetch') }}", { year }, function(data) {
            renderCalendarEvents(data);
            renderHolidayList(data);
        });
    }

    function renderCalendarEvents(data) {
        calendar.removeAllEvents();
        data.forEach(item => {
            calendar.addEvent({
                id: item.id,
                title: item.name,
                start: item.start_date,
                end: item.end_date,
                extendedProps: {
                    city: item.city,
                    state: item.state,
                    country: item.country
                }
            });
        });
    }

    function renderHolidayList(data) {
        let html = '';
        data.forEach(h => {
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${h.name}</strong><br>
                        <small>${h.start_date} – ${h.end_date}</small><br>
                        <small>${h.city ?? '-'}, ${h.state ?? '-'}, ${h.country}</small>
                    </div>
                    <div>
                        <button class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light edit" data-id="${h.id}"><i class="feather icon-edit"></i>    </button>
                        <button class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete" data-id="${h.id}"><i class="feather icon-trash"></i></button>
                    </div>
                </li>`;
        });
        $('#holidayList ul').html(html);
    }

    $('#filterYear').on('change', function () {
        fetchAndRender($(this).val());
    });

    $('#holidayForm').submit(function(e) {
        e.preventDefault();
        const url = "{{ route('public-holidays.store') }}";

        $.post({
            url: url,
            data: $(this).serialize(),
            success: function () {
                $('#holidayModal').modal('hide');
                $('#holidayForm')[0].reset();
                fetchAndRender($('#filterYear').val());
                Swal.fire('Gespeichert!', 'Feiertag wurde gespeichert.', 'success');
            },
            error: function(xhr) {
                if (xhr.status === 409 && xhr.responseJSON?.status === 'duplicate') {
                    Swal.fire('Duplikat!', xhr.responseJSON.message, 'warning');
                } else {
                    Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
                }
            }
        });
    });


    $(document).on('click', '.edit', function() {
        const id = $(this).data('id');
        $.get(`/public-holidays/${id}`, function(data) {
            $('#holiday_id').val(data.id);
            $('#name').val(data.name);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);
            $('#city').val(data.city).trigger('change');
            $('#state').val(data.state).trigger('change');
            $('#country').val(data.country).trigger('change');
            $('#holidayModal').modal('show');
        });
    });

    $(document).on('click', '.delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Dieser Feiertag wird dauerhaft gelöscht!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/public-holidays/delete/${id}`,
                    method: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: function () {
                        fetchAndRender($('#filterYear').val());
                        Swal.fire('Gelöscht!', 'Feiertag wurde entfernt.', 'success');
                    }
                });
            }
        });
    });
});
</script>
@endsection
