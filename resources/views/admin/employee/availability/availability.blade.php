@extends('admin.layouts.app')
@section('title') Verfügbarkeit @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
.modal-open-scroll-lock {
    overflow: hidden;
}

.pac-container {
    z-index: 20000 !important;
    position: fixed !important; /* ✅ better than absolute inside modal context */
}


.new_task {
    display: none;
    /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* Center the div */
    background: #e7e6e6;
    z-index: 10;
    width: 30% !important;
    /* Default width */
    max-width: 3-% !important;
    max-height: 85vh;
    /* Ensures it doesn't go beyond 80% of viewport height */
    overflow-y: auto;
    /* Enables scrolling inside */

}



/* Ensure modal content area scrolls separately */
.new_task .modal-body {
    max-height: 85vh;
    /* Limit body height */
    overflow-y: auto;
    /* Enable scrolling */
    padding: 15px;
}

/* Sticky Header & Close Button */
.new_task .modal-header {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.new_task .modal-footer {
    position: sticky;
    bottom: 0;
    background: #e7e6e6 !important;
    z-index: 10;
    padding: 10px;
    border-top: 1px solid #ddd;
}

/* Responsive styles for mobile */
@media (max-width: 768px) {
    .new_task {
        width: 90% !important;
        /* 90% width on mobile */
        max-width: 90% !important;
    }
}


.new_task_close {
    position: absolute;
    z-index: 4;
    left: -135px;
    top: 16%;
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
                <h2 class="content-header-title float-left mb-0">Verfügbarkeit</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mitarbeiter</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Search and Filter -->
            <div class="row mb-2">
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Mitarbeiter suchen...">
                </div>

                <div class="col-md-3">
                    <select id="departmentFilter" class="form-control">
                        <option value="">Alle Abteilungen</option>
                        @foreach(\App\Models\Department::pluck('department_name') as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="positionFilter" class="form-control">
                        <option value="">Alle Positionen</option>
                        @foreach(\App\Models\Position::pluck('position') as $pos)
                            <option value="{{ $pos }}">{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>

 
                <div class="col-md-3 text-right">
                    <button id="clearFilters" class="btn btn-secondary w-100">❌ Filter zurücksetzen</button>
                </div>

            </div>


            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="employeeTable">
                    <thead class="thead-white">
                        <tr>
                            <th>Foto</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Abteilung</th>
                            <th>Arbeitszeit (h/Tag)</th>
                            <th>Verfügbarkeit</th> 
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            @php
                                $main = $employee->mainDepartmentPosition ?? $employee->departmentPositions->first();
                                $photo = $employee->image ? asset('images/employee/' . $employee->image) : asset('images/gender/male.png');
                            @endphp
                            <tr data-department="{{ strtolower($main?->department?->department_name ?? '-') }}"
                                data-position="{{ strtolower($main?->position?->position ?? '-') }}"
                                data-available="{{ $employee->is_available_today ? 'yes' : 'no' }}">

                                <td><img src="{{ $photo }}" class="rounded-circle" width="50" height="50" /></td>
                                <td>{{ $employee->name }} {{ $employee->lastname }}</td>
                                <td>{{ $main?->position?->position ?? '-' }}</td>
                                <td class="dept-cell">{{ $main?->department?->department_name ?? '-' }}</td>
                                <td>{{ $main?->working_hours ?? '-' }}</td>
                                <td>
                                    @if(!empty($employee->best_slots))
                                        <ul class="list-unstyled mb-0">
                                            @foreach($employee->best_slots as $slot)
                                                <li><i class="feather icon-clock"></i> {{ $slot }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-danger">Keine freien Termine</span>
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-availability"
                                            data-id="{{ $employee->id }}"
                                           >
                                        Verfügbarkeit anzeigen
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {!! $employees->links('pagination::bootstrap-4') !!}
                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="availabilityModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="feather icon-calendar mr-1"></i>
                                Verfügbare Termine (3 Tage)
                            </h5>
                            <div class="ml-auto d-flex align-items-center">
                                <input type="date" id="checkDate" class="form-control form-control-sm mr-2" style="max-width: 200px;">
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" style="font-size: 1.4rem;">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body" id="availabilityContent" style="max-height: 400px; overflow-y: auto;">
                            <div class="text-center text-muted">Verfügbarkeit wird geladen...</div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="cards new_task_card new_task" style="display:none">
                <div class="card-header"
                    style="  border: 0;  background: transparent;  padding: 0;     justify-items: anchor-center;">
                    <h3 class="title mt-1 ml-2"
                        style="    color: #8fc73e !important; font-weight: bold;  justify-items: left;"> TERMIN
                        ERSTELLEN</h3>
                    <div class="line" style="    border-bottom: 2px solid #8fc73e; width:90% !important"></div>
                </div>
                <div class="card-body p-0">
                    <form id="task-store-form">
                        @csrf
                        <input type="hidden" name="id" id="appointment_id">

                        <div class="modal-body pt-0 pb-0">
                            <div class="cards p-1">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-10 col-10">
                                            <label for="task_title">Titel / Name *</label>
                                            <input type="text" id="name" class="form-control" name="name">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="hidden" name="color" id="color" value="#8fc73e">
                                            <div class="btn-group dropup dropdown-icon-wrapper mt-1 "
                                                id="color_drop_down">
                                                <button type="button" class="btn btn-icon    waves-effect waves-light"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <i class="fa fa-square" id="colorIcon" style="color: #8fc73e;"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <span class="dropdown-item" data-value="#8fc73e">
                                                        <i class="fa fa-square" style="color: #8fc73e;"></i> Grün
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ff0000">
                                                        <i class="fa fa-square" style="color: #ff0000;"></i> Rot
                                                    </span>
                                                    <span class="dropdown-item" data-value="#0000ff">
                                                        <i class="fa fa-square" style="color: #0000ff;"></i> Blau
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ffff00">
                                                        <i class="fa fa-square" style="color: #ffff00;"></i> Gelb
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ff00ff">
                                                        <i class="fa fa-square" style="color: #ff00ff;"></i> Magenta
                                                    </span>
                                                    <span class="dropdown-item" data-value="#00ffff">
                                                        <i class="fa fa-square" style="color: #00ffff;"></i> Cyan
                                                    </span>
                                                    <span class="dropdown-item" data-value="#000000">
                                                        <i class="fa fa-square" style="color: #000000;"></i> Schwarz
                                                    </span>
                                                    <span class="dropdown-item" data-value="#808080">
                                                        <i class="fa fa-square" style="color: #808080;"></i> Grau
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ffa500">
                                                        <i class="fa fa-square" style="color: #ffa500;"></i> Orange
                                                    </span>
                                                    <span class="dropdown-item" data-value="#800080">
                                                        <i class="fa fa-square" style="color: #800080;"></i> Lila
                                                    </span>
                                                    <span class="dropdown-item" data-value="#8b4513">
                                                        <i class="fa fa-square" style="color: #8b4513;"></i> Braun
                                                    </span>
                                                    <span class="dropdown-item" data-value="#4682b4">
                                                        <i class="fa fa-square" style="color: #4682b4;"></i> Stahlblau
                                                    </span>
                                                    <span class="dropdown-item" data-value="#5f9ea0">
                                                        <i class="fa fa-square" style="color: #5f9ea0;"></i>
                                                        Kadettenblau
                                                    </span>
                                                    <span class="dropdown-item" data-value="#d2691e">
                                                        <i class="fa fa-square" style="color: #d2691e;"></i>
                                                        Schokoladenbraun
                                                    </span>
                                                    <span class="dropdown-item" data-value="#2e8b57">
                                                        <i class="fa fa-square" style="color: #2e8b57;"></i> Seegrün
                                                    </span>
                                                    <span class="dropdown-item" data-value="#dc143c">
                                                        <i class="fa fa-square" style="color: #dc143c;"></i> Karmesinrot
                                                    </span>
                                                    <span class="dropdown-item" data-value="#7fffd4">
                                                        <i class="fa fa-square" style="color: #7fffd4;"></i> Aquamarin
                                                    </span>
                                                    <span class="dropdown-item" data-value="#9932cc">
                                                        <i class="fa fa-square" style="color: #9932cc;"></i> Dunkles
                                                        Lila
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ff6347">
                                                        <i class="fa fa-square" style="color: #ff6347;"></i> Tomate
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Startdatum *</label>
                                            <input type="date" id="start_date" class="form-control" name="start_date"
                                                value="">

                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Enddatum *</label>
                                            <input type="date" id="end_date" class="form-control" name="end_date"
                                                value="">

                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label for="start_time">Startzeit *</label>
                                            <input type="time" id="start_time" class="form-control" name="start_time"
                                                value="">
                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="end_time">Endzeit </label>
                                            <input type="time" id="end_time" class="form-control" name="end_time">
                                        </div>
                                        <div class="col-md-5 col-12 ">
                                            <label for="total_time"> Dauer </label>
                                            <input type="number" id="total_time" class="form-control" name="total_time">
                                        </div>

                                        <div class="col-md-4">
                                            <div class="row">
                                                <!-- Öffentlich Switch -->
                                                <div class="col-md-6">
                                                    <label for="switchPublic">Öffentlich</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="switchPublic" name="public" checked>
                                                        <label class="custom-control-label" for="switchPublic">
                                                            <span class="switch-icon-left"><i
                                                                    class="feather icon-unlock"></i></span>
                                                            <span class="switch-icon-right"><i
                                                                    class="feather icon-lock"></i></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Kontakt Switch -->
                                                <div class="col-md-6">
                                                    <label for="switchContact">Kontakt</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="switchContact" name="is_contact">
                                                        <label class="custom-control-label" for="switchContact">
                                                            <span class="switch-icon-left"><i
                                                                    class="feather icon-user"></i></span>
                                                            <span class="switch-icon-right"><i
                                                                    class="feather icon-user-x"></i></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- pre_type Dropdown (Shown only if is_contact is ON) -->
                                            <div class="form-group mt-2" id="preTypeBox" style="display: none;">
                                                <label for="pre_type">Typ</label>
                                                <select name="pre_type" id="pre_type" class="form-control select2">
                                                    <option value="">Auswählen</option>
                                                    <option value="Lead">Lead</option>
                                                    <option value="Lieferant">Lieferant</option>
                                                    <option value="Hersteller">Hersteller</option>
                                                    <option value="Kooperationspartner">Kooperationspartner</option>
                                                    <option value="Architekt">Architekt</option>
                                                    <option value="Nachunternehmer">Nachunternehmer</option>
                                                    <option value="Bank">Bank</option>
                                                    <option value="Versicherung">Versicherung</option>
                                                    <option value="Bewerber">Bewerber</option>
                                                    <option value="Sonstige">Sonstige</option>
                                                </select>
                                            </div>

                                            <div class="form-group mt-2" id="sourceBox" style="display: none;">
                                                <label for="pre_type">Quelle</label>
                                                <select name="source" id="source" class="form-control"
                                                    style="width: 100%">
                                                    <option></option>
                                                    <option value="Telefonisch">Telefonisch</option>
                                                    <option value="Persönlich">Persönlich</option>
                                                    <option value="Mail">Mail</option>
                                                    <option value="Nachbar">Nachbar</option>
                                                    <option value="Empfehlung">Empfehlung</option>
                                                    <option value="Solarrechner">Solarrechner</option>
                                                    <option value="Herstellerlead">Herstellerlead</option>
                                                    <option value="Event">Event</option>
                                                    <option value="Messe">Messe</option>
                                                    <option value="Hausmesse">Hausmesse</option>
                                                    <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit
                                                    </option>
                                                </select>
                                            </div>
                                        </div>





                                        <div class="col-md-12 col-12">
                                            <label for="task_title">Teilnehmer *</label>
                                            <select name="employee[]" id="employee" class="employee" multiple
                                                style="width:100%">
                                                @foreach ($employeesDrop as $emp)
                                                <option value="{{ $emp->id }}"
                                                    data-image="{{asset('images/employee/'.$emp->image) }}">
                                                    {{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="task_title">Kontakt</label>
                                            <select name="customer_id" id="customer_id" class="contact_list"
                                                style="width:100%">

                                            </select>
                                            <input type="hidden" name="contact_type" id="contact_type" value="">
                                        </div>

                                        <div class="col-md-6" style="display:none;" id="link_section">
                                            <span>Link </span>
                                            <input type="text" class="form-control" value="{{ old('link') }}" id="link"
                                                name="link">
                                        </div>

                                        <div class="col-md-6" id="intern" style="display: none;">
                                            <label for="task_title">Adress </label>
                                            <select name="branch_address_id" class="form-control">
                                                <option></option>
                                                @foreach ($branch_addresses as $address)
                                                <option value="{{ $address->id }}" data-street="{{ $address->street }}"
                                                    data-latitude="{{ $address->latitude }}"
                                                    data-longitude="{{ $address->longitude }}"
                                                    data-city="{{ $address->city }}"
                                                    data-postcode="{{ $address->postcode }}">
                                                    {{ $address->branch_initial }} - {{ $address->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6" id="extern">
                                            <label for="task_title">Adress </label>
                                            <input id="full_address" type="text" class="form-control form-element"
                                                placeholder="Adresse eingeben" name="full_address" value="">

                                            <input type="hidden" id="street-input" name="street" value="">
                                            <input type="hidden" id="city-input" name="city" value="">
                                            <input type="hidden" id="latitude-input" name="latitude" value="">
                                            <input type="hidden" id="longitude-input" name="longitude" value="">
                                            <input type="hidden" id="postal_code-input" name="postcode" value="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="task_title">Telefon</label>
                                            <input type="text" class="form-control phone" value="{{ old('phone') }}"
                                                name="phone" id="phone">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="task_title">Email <small>Optional</small></label>
                                            <input type="email" class="form-control email" value="{{ old('email') }}"
                                                name="email" id="email">
                                        </div>




                                        <div class="col-md-6 col-12">
                                            <label for="task_title">Zweck</label>
                                            <input type="text" class="form-control"
                                                value="{{ old('appointment_type') }}" id="appointment_type"
                                                name="appointment_type">
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <label for="task_title">Ort des Termin </label>
                                            <select name="execution_type" id="execution_type" class="form-control">
                                                <option value="internal">Intern</option>
                                                <option value="external" selected>Extern</option>
                                                <option value="online">Online</option>
                                                <option value="telephone">Telefon</option>
                                            </select>
                                        </div>


                                        <div class="col-md-12 col-12 mb-1">
                                            <label for="task_title">Beschreibung</label>

                                            <textarea name="description" class="form-control" rows="1"></textarea>
                                        </div>
 

                                        <div class="col-md-4">
                                            <label for="task_title">Betrieb</label>
                                            <select name="branch_id" id="branch_id" class="selectables"
                                                style="width:100%">
                                                <option></option>
                                                @foreach($branches as $br)
                                                <option value="{{ $br->id}}">{{$br->branch}} </option>
                                                @endforeach
                                            </select>
                                        </div>
 

                                        <div class="col-md-6 col-12 ">
                                            <label for="priority">Priorität</label>
                                            <select name="priority" class="form-control" id="priority">
                                                <option value="normal" data-icon="fa fa-battery-empty">Keiner</option>
                                                <option value="medium" data-icon="fa fa-battery-half">Medium</option>
                                                <option value="high" data-icon="fa fa-battery-full">Hoch</option>
                                                <option value="very high" data-icon="fa fa-fire warning">Sehr Wichtig
                                                </option>

                                            </select>
                                        </div>
  
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer" style="border:0;">
                            <button type="button"
                                class="btn btn-danger mr-1 waves-effect waves-light btn-sm close_task_window"
                                data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                            <button type="button" class="btn btn-primary save-task btn-sm"><i
                                    class="feather icon-save"></i> speichern</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
$(document).ready(function() {
    $('.selectables').select2({
        tags: true,
        placeholder: "Wählen",
        allowClear: true
    });
});
</script>
<script>
    // ⛳ Load holidays from PHP backend (Laravel controller)
    const rawPublicHolidays = @json($public_holidays);
    const publicHolidayDates = [];

    rawPublicHolidays.forEach(holiday => {
        let start = new Date(holiday.start_date);
        let end = new Date(holiday.end_date);
        while (start <= end) {
            publicHolidayDates.push(start.toISOString().split('T')[0]);
            start.setDate(start.getDate() + 1);
        }
    });

    $(document).ready(function () {
        // ✅ SweetAlert on session messages
        @if(session('save_msg'))
            Swal.fire('Gespeichert', '{{ session('save_msg') }}', 'success');
        @endif
        @if(session('update_msg'))
            Swal.fire('Aktualisiert', '{{ session('update_msg') }}', 'success');
        @endif
        @if(session('delete_msg'))
            Swal.fire('Gelöscht', '{{ session('delete_msg') }}', 'error');
        @endif

        // ✅ Modal scroll lock
        $('#availabilityModal').on('shown.bs.modal', () => $('body').addClass('modal-open-scroll-lock'));
        $('#availabilityModal').on('hidden.bs.modal', () => $('body').removeClass('modal-open-scroll-lock'));

        // ✅ Availability modal trigger
        $('.view-availability').on('click', function () {
            const id = $(this).data('id');
            $('#availabilityContent').html('<div class="text-center text-muted">Verfügbarkeit wird geladen...</div>');
            $('#availabilityModal').modal('show');
            fetchAvailability(id, null);

            $('#checkDate').off().on('change', function () {
                fetchAvailability(id, $(this).val());
            });
        });

        // ✅ Load employee availability via AJAX
        function fetchAvailability(id, date) {
            $.post(`/employee-availability/${id}`, {
                _token: '{{ csrf_token() }}',
                date: date
            }, function (res) {
                let html = '';
                let found = false;

                Object.entries(res.availability).forEach(([day, times]) => {
                    const morning = times.filter(t => parseInt(t.split(':')[0]) < 12);
                    const afternoon = times.filter(t => parseInt(t.split(':')[0]) >= 12);

                    html += `<div class="mb-4 border-bottom pb-3">
                                <h6 class="text-primary font-weight-bold">${day}</h6>`;

                    if (morning.length) {
                        html += `<p><strong>☀️ Vormittag</strong></p><div class="d-flex flex-wrap gap-1 mb-2">`;
                        morning.forEach(t => {
                            html += `<button class="btn btn-outline-success btn-sm book-slot" data-time="${t}" data-date="${day}" data-employee="${id}">🕒 ${t}</button>`;
                        });
                        html += `</div>`;
                        found = true;
                    }

                    if (afternoon.length) {
                        html += `<p><strong>🌤️ Nachmittag</strong></p><div class="d-flex flex-wrap gap-1">`;
                        afternoon.forEach(t => {
                            html += `<button class="btn btn-outline-success btn-sm book-slot" data-time="${t}" data-date="${day}" data-employee="${id}">🕒 ${t}</button>`;
                        });
                        html += `</div>`;
                        found = true;
                    }

                    if (!morning.length && !afternoon.length) {
                        html += `<p class="text-danger mt-2">Kein freier Termin</p>`;
                    }

                    html += `</div>`;
                });

                if (!found) {
                    html = '<p class="text-warning text-center mt-3">Keine freien Zeitfenster in den nächsten 3 Tagen.</p>';
                }

                $('#availabilityContent').html(html);
            });
        }

        // ✅ Book time slot → prefill form
        $(document).on('click', '.book-slot', function () {
            const time = $(this).data('time');
            const date = $(this).data('date');
            const employeeId = $(this).data('employee');

            $('#availabilityModal').modal('hide');

            $('#start_date').val(date);
            $('#end_date').val(date);
            $('#start_time').val(time);

            const [h, m] = time.split(':');
            const endHour = (parseInt(h) + 1).toString().padStart(2, '0');
            $('#end_time').val(`${endHour}:${m}`);

            $('#employee').val([employeeId]).trigger('change');

            $('.new_task_card.new_task').slideDown('fast');
            $('html, body').animate({
                scrollTop: $('.new_task_card.new_task').offset().top - 80
            }, 500);
        });

        // ✅ Booking submission
        $('.save-task').on('click', function(e) {
            e.preventDefault();

            const form = $('#task-store-form');
            const formData = form.serialize();

            const title = $('#name').val();
            const employee = $('#employee').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const appointmentId = $('#appointment_id').val();

            let errors = [];

            if (!title) errors.push('Der Titel darf nicht leer sein.');
            if (!employee || employee.length === 0) errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
            if (!startDate) errors.push('Das Startdatum darf nicht leer sein.');
            if (!endDate) errors.push('Das Enddatum darf nicht leer sein.');
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
            }

            if (startDate && endDate) {
                let current = new Date(startDate);
                let last = new Date(endDate);
                while (current <= last) {
                    let dateStr = current.toISOString().split('T')[0];
                    if (publicHolidayDates.includes(dateStr)) {
                        errors.push(`Datum ${dateStr} ist ein Feiertag.`);
                    }
                    current.setDate(current.getDate() + 1);
                }
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehlerhafte Eingabe',
                    html: `<ul style="text-align: left;">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                });
                return;
            }

            let method = appointmentId ? 'PUT' : 'POST';
            let actionUrl = appointmentId
                ? `/main-appointments/${appointmentId}`
                : `{{ route('main.appointments.store') }}`;

            $.ajax({
                url: actionUrl,
                type: method,
                data: formData,
                beforeSend: function() {
                    $('.save-task').prop('disabled', true).text('speichern...');
                },
                success: function(response) {
                    $('.save-task').prop('disabled', false).text('speichern');
                    $('.new_task_card').hide();
                    form.trigger('reset');
                    $('#appointment_id').val('');

                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolg',
                        text: appointmentId ? 'Termin erfolgreich aktualisiert!' : 'Termin erfolgreich gespeichert!',
                    });

                    let currentView = calendar.view.type;
                    let currentDate = calendar.getDate();
                    loadCalendarTasks(() => {
                        calendar.changeView(currentView);
                        calendar.gotoDate(currentDate);
                    });
                },
                error: function(xhr) {
                    $('.save-task').prop('disabled', false).text('speichern');
                    let serverErrors = xhr.responseJSON?.errors || {};
                    let errorMessages = Object.values(serverErrors).flat().map(msg => `<li>${msg}</li>`).join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        html: `<ul>${errorMessages || 'Unbekannter Fehler aufgetreten.'}</ul>`,
                    });
                }
            });
        });

        // ✅ Filter search
        $('#searchInput').on('keyup', function () {
            const val = $(this).val().toLowerCase();
            $('#employeeTable tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().includes(val));
            });
        });

        $('#departmentFilter').on('change', function () {
            const val = $(this).val().toLowerCase();
            $('#employeeTable tbody tr').each(function () {
                const dept = $(this).find('.dept-cell').text().toLowerCase();
                $(this).toggle(val === '' || dept.includes(val));
            });
        });

        // ✅ Google Autocomplete
        setTimeout(() => {
            const input = document.getElementById('autocompleteAddress') || document.getElementById('full_address');
            if (!input) return;

            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'],
                componentRestrictions: { country: "de" }
            });

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                if (!place.address_components) return;

                let street = '', city = '', postcode = '';
                place.address_components.forEach(comp => {
                    const type = comp.types[0];
                    if (type === 'route') street = comp.long_name;
                    if (type === 'postal_code') postcode = comp.long_name;
                    if (type === 'locality') city = comp.long_name;
                });

                $('#street, #street-input').val(street);
                $('#postcode, #postal_code-input').val(postcode);
                $('#city, #city-input').val(city);
                $('#latitude, #latitude-input').val(place.geometry.location.lat());
                $('#longitude, #longitude-input').val(place.geometry.location.lng());
            });
        }, 300);

        // ✅ Init Select2
        initSelect2();
        function initSelect2() {
            $('.employee').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                escapeMarkup: m => m
            });
        }

        function formatEmployee(employee) {
            if (!employee.id) return employee.text;
            const imageUrl = $(employee.element).data('image');
            return `<div style="display:flex;align-items:center;">
                        <img src="${imageUrl}" style="width:20px;height:20px;border-radius:50%;margin-right:10px;">
                        <span>${employee.text}</span>
                    </div>`;
        }

        $('.close_task_window').on('click', function () {
            $('#task-store-form').trigger('reset');
            $('#appointment_id').val('');
            $('.new_task_card').hide();
            $('.title').text('TERMIN ERSTELLEN');
        });
    });
</script>


<script>
$(document).ready(function () {
    function applyFilters() {
        let search = $('#searchInput').val().toLowerCase();
        let dept = $('#departmentFilter').val().toLowerCase();
        let pos = $('#positionFilter').val().toLowerCase();
        let avail = $('#availabilityFilter').val().toLowerCase();

        $('#employeeTable tbody tr').each(function () {
            let text = $(this).text().toLowerCase();
            let d = $(this).data('department');
            let p = $(this).data('position');
            let a = $(this).data('available');

            let show = true;

            if (search && !text.includes(search)) show = false;
            if (dept && d !== dept) show = false;
            if (pos && p !== pos) show = false;
            if (avail && a !== avail) show = false;

            $(this).toggle(show);
        });
    }

    $('#searchInput, #departmentFilter, #positionFilter, #availabilityFilter').on('input change', applyFilters);
});
</script>

<script>
        $('#clearFilters').on('click', function () {
        $('#searchInput').val('');
        $('#departmentFilter').val('');
        $('#positionFilter').val('');
        $('#availabilityFilter').val('');
        $('#employeeTable tbody tr').show();
    });

</script>



<script>
$(document).ready(function() {
    // Initialize Select2
    $('.contact_list').select2({
        placeholder: "Wählen", // Optional Placeholder
        allowClear: true,
        minimumInputLength: 0, // ✅ Allow default full list without typing
        ajax: {
            url: "{{ route('get.contact.list') }}",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function(params) {
                return {
                    search: params.term || '' // Pass search term if available, otherwise load all
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.main_id, // Contact ID
                            text: item.name + " " + item.lastname + " - " + item
                            .type, // Display name in dropdown
                            type: item.type, // Contact type
                            phone: item.phone || "",
                            email: item.email || "",
                            street: item.street || "",
                            postcode: item.postcode || "",
                            city: item.city || "",
                            longitude: item.longitude || "",
                            latitude: item.latitude || "",
                            full_address: (item.street && item.city && item.postcode) ?
                                item.street + ", " + item.postcode + " " + item.city : ""
                        };
                    })
                };
            },
            cache: true
        }
    });

    // ✅ On select, update all related input fields
    $('.contact_list').on('select2:select', function(e) {
        var selectedData = e.params.data;

        $('#contact_type').val(selectedData.type); // Set contact type
        $('.phone').val(selectedData.phone); // Set phone number
        $('.email').val(selectedData.email); // Set email address
        $('#full_address').val(selectedData.full_address); // Set full address
        $('#street-input').val(selectedData.street); // Set street
        $('#city-input').val(selectedData.city); // Set city
        $('#postal_code-input').val(selectedData.postcode); // Set postal code
        $('#latitude-input').val(selectedData.latitude); // Set latitude
        $('#longitude-input').val(selectedData.longitude); // Set longitude
    });

    // ✅ Clear fields when dropdown is cleared
    $('.contact_list').on('select2:clear', function() {
        $('#contact_type, .phone, .email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input')
            .val('');
    });

    // ✅ Load full list when Select2 opens
    $('.contact_list').on('select2:open', function() {
        $(".select2-search__field").attr("placeholder",
        "Tippen Sie, um zu suchen..."); // Set search placeholder
    });
});
</script>



<script>
$(document).ready(function() {
    $('#source').select2({
        tags: true,
        placeholder: "Quelle auswählen",
        allowClear: true
    });
});
</script>

<script>
function togglePreTypeAndSource() {
    const contactSwitch = document.getElementById('switchContact');
    const preTypeBox = document.getElementById('preTypeBox');
    const sourceBox = document.getElementById('sourceBox');

    const show = contactSwitch.checked;
    preTypeBox.style.display = show ? 'block' : 'none';
    sourceBox.style.display = show ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const contactSwitch = document.getElementById('switchContact');
    contactSwitch.addEventListener('change', togglePreTypeAndSource);
    togglePreTypeAndSource(); // Run on page load
});
</script>


<!-- Priority Script  -->
<script>
$(document).ready(function() {
    // Add click event listener to each dropdown-item
    $('#color_drop_down .dropdown-item').on('click', function() {
        // Get the selected color value from the data-value attribute
        const selectedColor = $(this).data('value');

        // Update the hidden input value
        $('#color').val(selectedColor);

        // Update the icon's color
        $('#colorIcon').css('color', selectedColor);
    });


});
</script>

<!-- Priority Script end  -->


@endsection
