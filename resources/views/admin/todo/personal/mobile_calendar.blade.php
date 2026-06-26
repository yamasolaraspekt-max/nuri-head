@extends('admin.layouts.app')
@section('title')
Mein Kalendar
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<style>
:root{
    --brand:#8fc73e;
    --brand-2:#74b2d4;
    --accent:#00aaff;
    --fc-day-bg:#f8f9fa;
    --muted:#626262;
}

/* -------------------------
   FullCalendar tweaks
   (mini calendar + legacy)
-------------------------- */

.fc-h-event {
    border-top: 1px solid #e8eaec !important;
    border-bottom: 1px solid #e8eaec !important;
    border-right: 1px solid #e8eaec !important;
}
.fc-v-event {
    background-color: #ffffff !important;
}
.fc-button {
    background: var(--brand) !important;
    border: 0 !important;
    margin-right: 3px !important;
}
.fc-button-active {
    background: var(--brand-2) !important;
}
.fc-day-today {
    background: #f1f1f1 !important;
}
.fc-toolbar-title {
    color: var(--muted);
}
.fc-timeGridWeek-view,
.fc-timeGridDay-view,
.fc-listWeek-view {
    background: #ffffff !important;
    height: auto !important;
    overflow-y: auto;
}
.fc-timegrid-slot-minor {
    display: none !important;
}
.fc-daygrid {
    background: #ffffff;
}
.fc-daygrid-event {
    display: block;
    width: 100%;
    background-color: #f8f9fa;
    border-left: 4px solid var(--accent);
    padding: 8px 10px;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.2s ease;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.fc-daygrid-event:hover {
    background-color: #f2f2f2;
}
.fc-popover {
    position: absolute !important;
}
.fc-license-message{
    display:none !important;
}
.fc-timegrid-slots table tr {
    height: 34px !important;
}
.fc-timegrid-slots {
    overflow-y: auto;
    max-height: 100%;
}
.fc-timegrid-event {
    background-color: inherit !important;
    color: inherit !important;
}

/* all-day events */
.fc .fc-all-day-event {
    background-color: #e3f2fd !important;
    border-left: 4px solid #2196f3 !important;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 6px;
}
.custom-all-day {
    background-color: #ffedcc !important;
    border-left: 4px solid #ff9800 !important;
    color: #333 !important;
    font-weight: 600;
    padding: 4px 6px;
}
.custom-all-day .custom-event-header {
    display:none !important;
}

/* mini calendar */
#mini_calendar .fc-daygrid-day-events{
    display:none !important;
}
#mini_calendar .fc-dayGridMonth-view{
    background:#f1f1f1;
}
#mini_calendar .fc-daygrid-day-bottom{
    display:none !important;
}
#mini_calendar .fc-day-selected .fc-daygrid-day-frame::after{
    content:"";
    position:absolute;
    top:50%;
    left:50%;
    width:30px;
    height:30px;
    background:#d4d4e4 !important;
    border-radius:50%;
    transform:translate(-50%,-50%);
    z-index:-1;
}
#mini_calendar .fc-day-selected .fc-daygrid-day-frame{
    background:#d4d4e4 !important;
    border-radius:50%;
}
#mini_calendar .fc-day{
    padding:0 !important;
    justify-items:center;
}
#mini_calendar .fc-toolbar-title{
    font-size:19px;
}

/* public holiday cells */
.public-holiday-cell,
.fc .public-holiday-cell{
    background-color:#f8f9fa !important;
}

/* -------------------------
   Custom event markup
-------------------------- */

.custom-event {
    display:flex;
    flex-direction:column;
    gap:2px;
}
.custom-event-title {
    font-size:14px;
    font-weight:700;
    margin-bottom:2px;
}
.custom-event-product{
    display:flex;
    justify-content:space-between;
    font-size:12px;
    color:#007bff;
}
.custom-event-time{
    font-size:11px;
    color:#666;
}
.custom-event-product ul{
    margin:0;
    padding:0;
    display:flex;
    gap:5px;
}
.custom-event-product ul li img{
    border-radius:50%;
}

/* -------------------------
   Top horizontal week scroller
-------------------------- */

.week-row {
    min-width: 320px;
}
.week-label {
    width:100%;
    font-size:12px;
    color:#6b7280;
}
.week-days > div{
    min-width:3.2rem; /* ~w-13 */
}
#calendarWrapper{
    scrollbar-width:thin;
}
#scrollableCalendar{
    transition:transform .15s ease-out;
}

/* -------------------------
   Appointment cards
-------------------------- */

#todayAppointments{
    margin-top:.25rem;
}
.app-card{
    border-radius:0.85rem;
    border:1px solid #e5e7eb;
    background:#ffffff;
    padding:.6rem .8rem;
    margin-bottom:.5rem;
    box-shadow:0 4px 12px rgba(15,23,42,.06);
    transition:transform .12s ease, box-shadow .12s ease, background-color .12s ease;
}
.app-card:hover{
    transform:translateY(-1px);
    box-shadow:0 7px 18px rgba(15,23,42,.1);
    background:#f9fafb;
}
.app-card-border{
    border-left-width:6px !important;
    border-left-style:solid !important;
}
.app-card-header-icons i{
    font-size:13px;
}
.app-badge{
    display:inline-flex;
    align-items:center;
    padding:2px 7px;
    border-radius:999px;
    font-size:10px;
    font-weight:600;
}

/* skeleton loading cards */
.skeleton-card{
    border-radius:0.85rem;
    border:1px solid #e5e7eb;
    background:#ffffff;
    padding:.6rem .8rem;
    margin-bottom:.5rem;
    overflow:hidden;
    position:relative;
}
.skeleton-line{
    height:10px;
    border-radius:999px;
    background:linear-gradient(90deg,#e5e7eb 0,#f3f4f6 45%,#e5e7eb 80%);
    background-size:200% 100%;
    animation:skeleton-shimmer 1.2s ease-in-out infinite;
    margin-bottom:6px;
}
@keyframes skeleton-shimmer{
    0%{background-position:200% 0}
    100%{background-position:-200% 0}
}

/* -------------------------
   Sidebar details panel
-------------------------- */

#sidebar{
    max-width:24rem;
}
@media (min-width:640px){
    #sidebar{
        border-left:1px solid #e5e7eb;
        box-shadow:-12px 0 30px rgba(15,23,42,.25);
    }
}

/* -------------------------
   Modals (filter + create)
-------------------------- */

.modal-backdrop{
    background:rgba(15,23,42,.4);
    backdrop-filter:blur(4px);
}
.modal-panel{
    border-radius:1rem;
    background:#ffffff;
    max-height:95vh;
    overflow-y:auto;
}
@media (max-width:640px){
    .modal-panel{
        width:100%;
        max-width:none;
        height:100vh;
        max-height:100vh;
        border-radius:1.25rem 1.25rem 0 0;
        margin:0;
        align-self:flex-end;
        padding:1.25rem;
    }
}

/* hide big side widgets on smaller desktop */
@media (max-width:1394px){
    #calendar_icons,
    #calendar_times,
    #mini_calendar{
        display:none !important;
    }
}

/* spacing helpers for search inputs on very small screens */
@media (max-width:576px){
    .employee_search_input,
    .task_search_input,
    .appointment_search_input{
        margin-bottom:10px;
    }
}

/* small dropdown menu over cards */
.relative .dropdown-menu{
    z-index:9999;
}

/* warning / bell animations */
#bellIcon{
    animation:zoomAndColorChange 1s ease-in-out infinite;
}
.warning_text{
    animation:zoomAndColorChange 1s ease-in-out infinite;
}
@keyframes zoomAndColorChange{
    0%{transform:scale(1);color:inherit;}
    100%{transform:scale(1.2);color:#ff9f43;}
}

/* smooth slider section scroll */
#slider_section{
    overflow:hidden;
    height:100%;
    transition:all .2s ease;
}

/* hide fullcalendar event inner DOM when using custom rendering */
.fc-event-main-frame,
.fc-event-main{
    display:none !important;
}

/* select2 pills */
.select2-selection__choice{
    border:0 !important;
}

/* small "more" link */
.fc-more-link{
    width:45px;
    background:#f1f1f1;
}
.fc-more-link .fc-timegrid-more-link-inner{
    font-size:22px;
}

/* mobile tweaks for daygrid */
@media (max-width:768px){
    .fc-header-toolbar{
        flex-direction:column;
        gap:.25rem;
    }
    .fc-daygrid-day{
        min-height:90px !important;
    }
    .fc-daygrid-day-events{
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .fc-daygrid-event{
        font-size:13px !important;
        padding:6px 8px !important;
        min-width:80%;
        text-align:center;
        display:inline-block;
    }
}
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-body">
            <div class="w-full min-h-screen p-2 sm:p-6 flex flex-col mt-3 bg-slate-50">

                {{-- Top bar: quick actions --}}
                <div class="sticky top-0 z-30 bg-slate-50/90 backdrop-blur-sm mb-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border border-gray-200 bg-white rounded-xl px-2 sm:px-3 py-2 shadow-sm">
                        <div class="flex items-center gap-2">
                            <button id="goToToday"
                                    class="text-xs sm:text-sm inline-flex items-center gap-1 bg-gray-700 text-white px-2 py-1.5 rounded-full hover:bg-gray-800">
                                <i class="feather icon-calendar"></i>
                                Heute
                            </button>

                            <button id="openFilterModal"
                                    class="text-xs sm:text-sm inline-flex items-center gap-1 bg-gray-600 text-white px-2 py-1.5 rounded-full hover:bg-gray-700">
                                <i class="feather icon-filter"></i>
                            </button>

                            <button type="button"
                                    onclick="openAppointmentModal()"
                                    class="text-xs sm:text-sm inline-flex items-center gap-1 bg-emerald-600 text-white px-2 py-1.5 rounded-full hover:bg-emerald-700">
                                <i class="feather icon-plus"></i>
                                <span class="hidden sm:inline">Termin</span>
                            </button>

                            <button id="clearFilters"
                                    class="text-xs sm:text-sm inline-flex items-center gap-1 bg-red-500 text-white px-2 py-1.5 rounded-full hover:bg-red-600">
                                <i class="feather icon-corner-down-left"></i>
                            </button>
                        </div>

                        <div class="flex items-center gap-2 text-[11px] sm:text-xs text-gray-500">
                            <i class="feather icon-info text-blue-500"></i>
                            <span class="hidden sm:inline">Tip: Wähle eine Woche oder streiche horizontal, um schnell zu navigieren.</span>
                            <span class="sm:hidden">Swipe Wochenleiste →</span>
                        </div>
                    </div>
                </div>

                {{-- Month / Week selectors --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <select id="monthSelect"
                                class="border border-gray-300 bg-white px-2 py-1.5 rounded-lg text-xs sm:text-sm shadow-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <!-- Filled dynamically -->
                        </select>

                        <select id="weekSelect"
                                class="border border-gray-300 bg-white px-2 py-1.5 rounded-lg text-xs sm:text-sm shadow-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Woche wählen…</option>
                        </select>
                    </div>

                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Termine der ausgewählten Woche</span>
                    </div>
                </div>

                {{-- Month label --}}
                <div id="calendarMonth"
                     class="text-center text-sm sm:text-lg font-semibold text-gray-700 mb-1">
                </div>

                {{-- Horizontal week scroller --}}
                <div id="calendarWrapper"
                     class="overflow-x-auto mb-1 border-b border-gray-200 pb-1">
                    <div id="scrollableCalendar"
                         class="flex space-x-2 w-max px-1 py-1"></div>
                </div>

                {{-- Today / week appointments list --}}
                <div id="todayAppointments" class="p-1">
                    {{-- populated by JS --}}
                </div>

                {{-- Detail Sidebar --}}
                <div id="sidebar"
                     class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col">
                    <div class="flex items-center justify-between px-4 py-3 border-b bg-slate-50">
                        <h4 class="text-base font-semibold text-gray-800">Details</h4>
                        <button id="closeSidebarBtn"
                                class="text-gray-500 hover:text-red-600 text-lg leading-none">
                            ✕
                        </button>
                    </div>
                    <div class="p-4 space-y-4 overflow-y-auto">
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm">Mitarbeiter</h5>
                            <p id="detailEmployee" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm">Zeit</h5>
                            <p id="detailTime" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm">Projekt Nr.</h5>
                            <p id="detailCode" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm">Status</h5>
                            <p id="detailStatus" class="text-xs text-indigo-600 font-semibold mt-1"></p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm">Beschreibung</h5>
                            <p id="detailDescription" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-700 text-sm mb-1">Bilder</h5>
                            <div class="flex space-x-2 overflow-x-auto">
                                <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                                <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                                <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Modal --}}
                <div id="filterModal"
                     class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop">
                    <div class="modal-panel w-full max-w-md mx-2 sm:mx-0 shadow-xl relative">
                        <h2 class="text-base sm:text-lg font-semibold mb-3 flex items-center gap-2">
                            <i class="feather icon-search"></i>
                            Filter Termine
                        </h2>

                        <div class="space-y-3 text-sm">
                            {{-- Employee --}}
                            <div>
                                <label for="filterEmployee" class="block text-xs font-medium text-gray-700 mb-1">
                                    Mitarbeiter
                                </label>
                                <select id="filterEmployee"
                                        class="form-select select2 w-full"
                                        multiple
                                        style="width:100%">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date --}}
                            <div>
                                <label for="filterDate" class="block text-xs font-medium text-gray-700 mb-1">
                                    Datum
                                </label>
                                <input type="date"
                                       id="filterDate"
                                       class="form-input w-full border-gray-300 rounded-md text-sm" />
                            </div>

                            {{-- Keyword --}}
                            <div>
                                <label for="filterKeyword" class="block text-xs font-medium text-gray-700 mb-1">
                                    Suchbegriff
                                </label>
                                <input type="text"
                                       id="filterKeyword"
                                       class="form-input w-full border-gray-300 rounded-md text-sm"
                                       placeholder="z. B. Projektname, Adresse..." />
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex justify-end gap-2 mt-5">
                            <button id="closeFilterModal"
                                    class="bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-300">
                                Abbrechen
                            </button>
                            <button id="applyFilters"
                                    class="bg-gray-700 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-900">
                                Anwenden
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Add Appointment Modal --}}
                <div id="createAppointmentModal"
                     class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop">
                    <div class="modal-panel w-full max-w-2xl mx-2 sm:mx-0 shadow-xl relative">
                        <h2 class="text-base sm:text-lg font-semibold mb-3 flex items-center gap-2">
                            <i class="feather icon-calendar"></i>
                            Neuen Termin erstellen
                        </h2>

                        <form id="mobileAppointmentForm" class="space-y-3 text-sm">
                            @csrf
                            <input type="hidden" name="contact_mode" id="contact_mode" value="new">

                            {{-- Title --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Titel</label>
                                <input type="text" name="name"
                                       class="form-input w-full border-gray-300 rounded-md"
                                       placeholder="Termin Titel" required>
                            </div>

                            {{-- Phone / Email --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Telefon</label>
                                    <input type="text" name="phone" id="contactPhone"
                                           class="form-input w-full border-gray-300 rounded-md"
                                           placeholder="Telefon">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">E-Mail</label>
                                    <input type="email" name="email" id="contactEmail"
                                           class="form-input w-full border-gray-300 rounded-md"
                                           placeholder="E-Mail">
                                </div>
                            </div>

                            {{-- Address --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Adresse</label>
                                <input type="text" id="autocomplete"
                                       placeholder="Adresse suchen"
                                       class="form-input w-full border-gray-300 rounded-md">
                                <input type="hidden" name="street">
                                <input type="hidden" name="postcode">
                                <input type="hidden" name="city">
                                <input type="hidden" name="latitude">
                                <input type="hidden" name="longitude">
                            </div>

                            {{-- Contact --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kontakt</label>
                                <select id="contactSelect" name="selected_contact"
                                        class="form-select select2 w-full border-gray-300 rounded-md"
                                        style="width:100%">
                                    <!-- AJAX -->
                                </select>
                            </div>

                            {{-- Product --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Produkt</label>
                                <select id="productSelect" name="product_selection"
                                        class="form-select w-full border-gray-300 rounded-md"
                                        disabled>
                                    <!-- AJAX -->
                                </select>
                            </div>

                            {{-- Employees --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Mitarbeiter</label>
                                <select name="employees[]" id="employeeSelect"
                                        class="form-select select2 w-full border-gray-300 rounded-md"
                                        multiple
                                        style="width:100%">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-image="{{ asset('images/employee/' . $employee->image) }}">
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Start / End Date + Time --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Datum</label>
                                    <input type="date" name="start_date"
                                           class="form-input w-full border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Uhrzeit</label>
                                    <input type="time" name="start_time"
                                           class="form-input w-full border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Ende Datum</label>
                                    <input type="date" name="end_date"
                                           class="form-input w-full border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Ende Uhrzeit</label>
                                    <input type="time" name="end_time"
                                           class="form-input w-full border-gray-300 rounded-md">
                                </div>
                            </div>

                            {{-- Priority --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Priorität</label>
                                    <select name="priority"
                                            class="form-select w-full border-gray-300 rounded-md">
                                        <option value="normal">Normal</option>
                                        <option value="high">Hoch</option>
                                        <option value="very high">Sehr Hoch</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-2">
                                <button type="button"
                                        onclick="closeAppointmentModal()"
                                        class="bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-gray-300">
                                    Abbrechen
                                </button>
                                <button type="submit"
                                        class="bg-emerald-600 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm hover:bg-emerald-700"
                                        id="saveAppointment">
                                    Speichern
                                </button>
                            </div>

                            <button type="button"
                                    onclick="closeAppointmentModal()"
                                    class="absolute top-2 right-3 text-gray-400 hover:text-red-500 text-xl leading-none">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>

            </div> {{-- /page wrapper --}}
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('app-assets/js/scripts/tooltip/tooltip.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scrollable = document.getElementById('scrollableCalendar');
    const calendarWrapper = document.getElementById('calendarWrapper');
    const sidebar = document.getElementById('sidebar');
    const today = new Date();
    const currentYear = today.getFullYear();

    let baseDate = new Date();
    baseDate.setHours(0, 0, 0, 0);

    let selectedDayEl = null;
    let currentFilters = { employees: [], keyword: '', date: '' };
    const monthSelect = document.getElementById('monthSelect');
    const weekSelect = document.getElementById('weekSelect');

    // helpers
    function formatDateLocal(date){
        const y = date.getFullYear();
        const m = String(date.getMonth()+1).padStart(2,'0');
        const d = String(date.getDate()).padStart(2,'0');
        return `${y}-${m}-${d}`;
    }
    function parseLocalDate(ymd){
        const [y,m,d] = ymd.split('-').map(Number);
        return new Date(y, m-1, d);
    }
    function getISOWeekNumber(date){
        const temp = new Date(date.getTime());
        temp.setHours(0,0,0,0);
        temp.setDate(temp.getDate() + 3 - ((temp.getDay()+6)%7));
        const firstThursday = new Date(temp.getFullYear(), 0, 4);
        const diff = temp - firstThursday + ((firstThursday.getDay()+6)%7)*86400000;
        return 1 + Math.floor(diff/604800000);
    }
    function getMonday(date){
        const d = new Date(date.getTime());
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        d.setDate(diff);
        d.setHours(0,0,0,0);
        return d;
    }
    function updateCalendarMonthLabel(date){
        const monthNames = ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
        const label = `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        const labelEl = document.getElementById('calendarMonth');
        if(labelEl) labelEl.innerText = label;
    }

    // day box
    function addDayBox(date){
        const weekdays = ['So','Mo','Di','Mi','Do','Fr','Sa'];
        const dayName = weekdays[date.getDay()];
        const dateStr = formatDateLocal(date);
        const isToday = dateStr === formatDateLocal(today);

        const dayBox = document.createElement('div');
        dayBox.className = 'flex-shrink-0 w-14 rounded-lg text-center text-xs sm:text-sm font-medium cursor-pointer py-1 px-1 border border-gray-200 bg-gray-100 text-gray-700 hover:bg-emerald-500 hover:text-white transition';
        dayBox.innerHTML = `${dayName}<br><span class="block text-sm sm:text-base">${date.getDate()}</span>`;
        dayBox.dataset.date = dateStr;
        if(isToday) dayBox.id = 'todayDateBox';

        if(isToday){
            dayBox.classList.remove('bg-gray-100','text-gray-700');
            dayBox.classList.add('bg-emerald-600','text-white');
            selectedDayEl = dayBox;
        }

        dayBox.addEventListener('click', () => {
            if(selectedDayEl){
                selectedDayEl.classList.remove('bg-emerald-600','text-white');
                selectedDayEl.classList.add('bg-gray-100','text-gray-700');
            }
            dayBox.classList.remove('bg-gray-100','text-gray-700');
            dayBox.classList.add('bg-emerald-600','text-white');
            selectedDayEl = dayBox;

            currentFilters.date = dateStr;
            loadAppointments();
        });

        return dayBox;
    }

    // weekly range
    let currentRange = { start: -14, end: 14 };

    function renderWeeklyRange(startWeekOffset, endWeekOffset){
        for(let weekOffset=startWeekOffset; weekOffset<=endWeekOffset; weekOffset++){
            const monday = getMonday(baseDate);
            monday.setDate(monday.getDate() + weekOffset*7);
            const weekNumber = getISOWeekNumber(monday);
            const monthName = monday.toLocaleDateString('de-DE',{month:'long',year:'numeric'});

            const weekId = `week-${formatDateLocal(monday)}`;
            if(document.getElementById(weekId)) continue;

            const weekRow = document.createElement('div');
            weekRow.className = 'week-row mb-2';
            weekRow.id = weekId;

            const weekLabel = document.createElement('div');
            weekLabel.className = 'week-label text-center mb-1';
            weekLabel.innerText = `KW ${weekNumber} · ${monthName}`;
            weekRow.appendChild(weekLabel);

            const weekDays = document.createElement('div');
            weekDays.className = 'week-days flex gap-1';
            for(let i=0;i<7;i++){
                const day = new Date(monday.getTime());
                day.setDate(day.getDate()+i);
                const dayBox = addDayBox(day);
                weekDays.appendChild(dayBox);
            }
            weekRow.appendChild(weekDays);
            scrollable.appendChild(weekRow);
        }
    }

    // selectors
    function populateMonthDropdown(year){
        const months = [];
        for(let m=0; m<12; m++){
            const date = new Date(year, m, 1);
            months.push({
                value: `${year}-${String(m+1).padStart(2,'0')}`,
                label: date.toLocaleDateString('de-DE',{month:'long',year:'numeric'})
            });
        }
        monthSelect.innerHTML = months.map(m => `<option value="${m.value}">${m.label}</option>`).join('');
        monthSelect.value = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}`;
    }

    function populateWeeksOfMonth(year, month){
        const weeks = [];
        const firstDay = new Date(year, month-1, 1);
        const lastDay = new Date(year, month, 0);

        let monday = getMonday(firstDay);
        while(monday <= lastDay){
            const isoWeek = getISOWeekNumber(monday);
            const label = `KW ${isoWeek} (${monday.toLocaleDateString('de-DE',{day:'2-digit',month:'short'})})`;
            weeks.push({
                value: formatDateLocal(monday),
                label: label
            });
            monday.setDate(monday.getDate()+7);
        }

        weekSelect.innerHTML = `<option value="">Woche wählen…</option>` +
            weeks.map(w => `<option value="${w.value}">${w.label}</option>`).join('');
    }

    monthSelect.addEventListener('change', function(){
        const [year,month] = this.value.split('-');
        populateWeeksOfMonth(parseInt(year,10), parseInt(month,10));
        updateCalendarMonthLabel(parseLocalDate(`${year}-${month}-01`));
    });

    weekSelect.addEventListener('change', function(){
        if(!this.value) return;
        const weekStart = parseLocalDate(this.value);
        const dates = [];
        for(let i=0;i<7;i++){
            const d = new Date(weekStart.getTime());
            d.setDate(weekStart.getDate()+i);
            dates.push(formatDateLocal(d));
        }

        currentFilters.date = this.value;
        loadAppointmentsForWeek(dates);

        const weekRow = document.getElementById(`week-${this.value}`);
        if(weekRow){
            weekRow.scrollIntoView({behavior:'smooth', block:'start', inline:'center'});
        }else{
            Swal.fire('Nicht geladen','Diese Woche ist noch nicht sichtbar. Bitte scrollen oder laden Sie mehr Daten.','warning');
        }
    });

    // infinite scroll
    renderWeeklyRange(currentRange.start, currentRange.end);

    calendarWrapper.addEventListener('scroll', () => {
        const maxScrollLeft = scrollable.scrollWidth - calendarWrapper.clientWidth;
        const buffer = 100;

        if(calendarWrapper.scrollLeft < buffer){
            currentRange.start -= 1;
            renderWeeklyRange(currentRange.start, currentRange.start);
        }
        if(calendarWrapper.scrollLeft > maxScrollLeft - buffer){
            currentRange.end += 1;
            renderWeeklyRange(currentRange.end, currentRange.end);
        }
    });

    // today button
    document.getElementById('goToToday').addEventListener('click', () => {
        const todayEl = document.getElementById('todayDateBox');
        if(todayEl){
            todayEl.scrollIntoView({behavior:'smooth', inline:'center'});
            todayEl.click();
        }
    });

    // sidebar close
    document.getElementById('closeSidebarBtn')?.addEventListener('click', () => {
        sidebar.classList.add('translate-x-full');
    });

    // skeleton helpers
    function showSkeleton(){
        const container = document.querySelector('#todayAppointments');
        if(!container) return;
        container.innerHTML = '';
        for(let i=0;i<3;i++){
            const card = document.createElement('div');
            card.className = 'skeleton-card';
            card.innerHTML = `
                <div class="skeleton-line" style="width:60%;"></div>
                <div class="skeleton-line" style="width:40%;"></div>
                <div class="skeleton-line" style="width:80%;"></div>
            `;
            container.appendChild(card);
        }
    }

    function buildCardHTML(app, galleryKey){
        const priorityIcon = app.priority === "very high"
            ? '<span class="app-badge bg-red-50 text-red-600 border border-red-100 mr-1"><i class="fa fa-fire mr-1"></i>Sehr hoch</span>'
            : app.priority === "high"
                ? '<span class="app-badge bg-amber-50 text-amber-600 border border-amber-100 mr-1"><i class="fa fa-bell mr-1"></i>Hoch</span>'
                : '';

        const reportIcon = app.is_report === "1"
            ? '<span class="app-badge bg-sky-50 text-sky-600 border border-sky-100"><i class="feather icon-file-text mr-1"></i>Bericht</span>'
            : '';

        const mapIcon = `
            <i class="feather icon-map-pin text-green-600 ml-1 cursor-pointer open-map"
                data-lat="${app.latitude || ''}"
                data-lng="${app.longitude || ''}"
                data-name="${app.name || 'Ziel'}"
                title="In Google Maps anzeigen">
            </i>
        `;

        const imageBlock = Array.isArray(app.images) && app.images.length > 0 ? `
            <div class="overflow-x-auto mt-2">
                <div class="flex gap-2 min-w-max">
                    ${app.images.map((img,i) => `
                        <a href="${img}" class="glightbox" data-gallery="${galleryKey}" data-glightbox="title: Bild ${i+1}">
                            <img src="${img}" class="h-16 w-24 object-cover rounded-md" />
                        </a>
                    `).join('')}
                </div>
            </div>
        ` : '';

        return `
            <div class="flex justify-between items-start mb-1">
                <div class="flex items-start space-x-2">
                    <div class="relative custom-menu-wrapper">
                        <button class="text-gray-400 hover:text-gray-700 focus:outline-none custom-menu-button" data-id="${app.id}">
                            <i class="feather icon-more-vertical"></i>
                        </button>
                        <div class="absolute left-0 mt-2 z-50 w-40 bg-white rounded-lg shadow border text-xs custom-menu hidden">
                            <a href="/appointment_details/${app.id}" class="block px-3 py-2 hover:bg-gray-100 text-gray-700">Details</a>
                            <button class="block w-full text-left px-3 py-2 hover:bg-blue-100 text-blue-600 custom-edit" data-id="${app.id}">Bearbeiten</button>
                            <button class="block w-full text-left px-3 py-2 hover:bg-yellow-100 text-yellow-600 custom-duplicate" data-id="${app.id}">Duplizieren</button>
                            <button class="block w-full text-left px-3 py-2 hover:bg-red-100 text-red-600 custom-delete" data-id="${app.id}">Löschen</button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 app-card-header-icons">
                        <div>${priorityIcon} ${reportIcon}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    ${app.employee_image ? `<img src="${app.employee_image}" class="h-7 w-7 rounded-full object-cover border border-white shadow-sm" alt="Avatar">` : ''}
                </div>
            </div>

            <div class="text-sm font-medium text-gray-800">${app.name || ''}</div>
            <div class="text-[11px] text-indigo-600 font-semibold mb-1">
                ${app.status || ''} · ${app.description || 'Keine Beschreibung'}
            </div>
            <div class="text-[11px] text-gray-600 flex items-center">
                <i class="feather icon-clock mr-1"></i>
                ${app.start_time || ''} - ${app.end_time || ''} ${mapIcon}
            </div>
            ${imageBlock}
        `;
    }

    // load appointments for single day
    function loadAppointments(){
        const params = new URLSearchParams();
        if(currentFilters.date) params.append('date', currentFilters.date);
        if(currentFilters.keyword) params.append('keyword', currentFilters.keyword);
        if(currentFilters.employees.length){
            currentFilters.employees.forEach(e => params.append('employees[]', e));
        }

        showSkeleton();

        fetch(`/get-appointments?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector('#todayAppointments');
                if(!container) return;
                container.innerHTML = '';

                if(!Array.isArray(data) || data.length === 0){
                    container.innerHTML = `<div class="text-gray-500 text-xs sm:text-sm mt-2 flex items-center gap-2">
                        <i class="feather icon-info text-blue-400"></i>
                        Keine Termine gefunden.
                    </div>`;
                    return;
                }

                data.forEach(app => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'app-card app-card-border cursor-pointer open-sidebar';
                    wrapper.dataset.employee = app.employee_name || '';
                    wrapper.dataset.time = app.start_time || '';
                    wrapper.dataset.code = app.code || '';
                    wrapper.dataset.status = app.status || '';
                    wrapper.dataset.description = app.description || '';
                    wrapper.style.setProperty('border-left-color', app.employee_color || '#8fc73e', 'important');
                    wrapper.innerHTML = buildCardHTML(app, `day-${currentFilters.date}`);
                    container.appendChild(wrapper);
                });

                if(window.glightboxInstance) window.glightboxInstance.destroy();
                window.glightboxInstance = GLightbox({
                    selector: '.glightbox',
                    loop: true,
                    touchNavigation: true,
                    closeButton: true,
                });

                bindMapIcons();
            });
    }

    // load appointments for week
    function loadAppointmentsForWeek(dates){
        const params = new URLSearchParams();
        dates.forEach(date => params.append('week_dates[]', date));
        if(currentFilters.employees.length){
            currentFilters.employees.forEach(e => params.append('employees[]', e));
        }
        if(currentFilters.keyword){
            params.append('keyword', currentFilters.keyword);
        }

        showSkeleton();

        fetch(`/get-appointments?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector('#todayAppointments');
                if(!container) return;
                container.innerHTML = '';

                if(!Array.isArray(data) || data.length === 0){
                    container.innerHTML = `<div class="text-gray-500 text-xs sm:text-sm mt-2 flex items-center gap-2">
                        <i class="feather icon-info text-blue-400"></i>
                        Keine Termine in dieser Woche gefunden.
                    </div>`;
                    return;
                }

                data.forEach(app => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'app-card app-card-border cursor-pointer open-sidebar';
                    wrapper.dataset.employee = app.employee_name || '';
                    wrapper.dataset.time = app.start_time || '';
                    wrapper.dataset.code = app.code || '';
                    wrapper.dataset.status = app.status || '';
                    wrapper.dataset.description = app.description || '';
                    wrapper.style.setProperty('border-left-color', app.employee_color || '#8fc73e', 'important');
                    wrapper.innerHTML = buildCardHTML(app, `week-${currentFilters.date || 'kw'}`);
                    container.appendChild(wrapper);
                });

                if(window.glightboxInstance) window.glightboxInstance.destroy();
                window.glightboxInstance = GLightbox({
                    selector: '.glightbox',
                    loop: true,
                    touchNavigation: true,
                    closeButton: true,
                });

                bindMapIcons();
            });
    }

    function bindMapIcons(){
        document.querySelectorAll('.open-map').forEach(icon => {
            icon.addEventListener('click', () => {
                const lat = icon.getAttribute('data-lat');
                const lng = icon.getAttribute('data-lng');
                const name = icon.getAttribute('data-name') || 'Ziel';

                if(!lat || !lng){
                    Swal.fire({
                        icon:'warning',
                        title:'Keine Adresse vorhanden',
                        text:'Für diesen Termin ist keine gültige Position hinterlegt.',
                    });
                    return;
                }

                navigator.geolocation.getCurrentPosition(position => {
                    const currentLat = position.coords.latitude;
                    const currentLng = position.coords.longitude;

                    const mapUrl = `https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${lat},${lng}&travelmode=driving`;
                    const embedUrl = `https://www.google.com/maps/embed/v1/directions?key={{ config('services.google.maps_key') }}&origin=${currentLat},${currentLng}&destination=${lat},${lng}&mode=driving`;

                    Swal.fire({
                        title: `${name}`,
                        html: `
                            <div style="width:100%;height:300px;margin-bottom:10px;">
                                <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen
                                    src="${embedUrl}">
                                </iframe>
                            </div>
                            <a href="${mapUrl}" target="_blank" class="swal2-confirm swal2-styled" style="background:#3085d6;">
                                In Google Maps öffnen
                            </a>
                        `,
                        width: 600,
                        showConfirmButton: false
                    });

                }, () => {
                    Swal.fire({
                        icon:'error',
                        title:'Standort nicht verfügbar',
                        text:'Bitte aktivieren Sie den Standortzugriff im Browser.',
                    });
                });
            });
        });
    }

    // clear filters
    document.getElementById('clearFilters').addEventListener('click', () => {
        currentFilters = { employees: [], keyword: '', date: '' };
        $('#filterEmployee').val([]).trigger('change');
        document.getElementById('filterKeyword').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterModal').classList.add('hidden');
        loadAppointments();
    });

    // menu + actions
    document.body.addEventListener('click', function(e){
        document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));

        if(e.target.closest('.custom-menu-button')){
            e.stopPropagation();
            const wrapper = e.target.closest('.custom-menu-wrapper');
            const menu = wrapper.querySelector('.custom-menu');
            if(menu) menu.classList.toggle('hidden');
            return;
        }

        if(e.target.closest('.custom-delete')){
            const id = e.target.dataset.id;
            Swal.fire({
                title:'Löschen bestätigen?',
                icon:'warning',
                showCancelButton:true,
                confirmButtonText:'Ja, löschen',
            }).then(result => {
                if(result.isConfirmed){
                    fetch(`/appointments/delete/${id}`, {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(() => {
                        loadAppointments();
                        Swal.fire('Gelöscht!','', 'success');
                    });
                }
            });
            return;
        }

        if(e.target.closest('.custom-edit')){
            const id = e.target.dataset.id;
            window.location.href = `/appointments/edit/${id}`;
            return;
        }

        if(e.target.closest('.custom-duplicate')){
            const id = e.target.dataset.id;
            fetch(`/appointments/duplicate/${id}`, {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                loadAppointments();
                Swal.fire('Dupliziert!','', 'success');
            });
            return;
        }
    });

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('createAppointmentModal').classList.add('hidden');
        }
    });
    window.addEventListener('scroll', () => {
        document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));
    });

    // filter modal
    document.getElementById('openFilterModal').addEventListener('click', () => {
        document.getElementById('filterModal').classList.remove('hidden','opacity-0');
        document.getElementById('filterModal').classList.add('flex');
    });
    document.getElementById('closeFilterModal').addEventListener('click', () => {
        document.getElementById('filterModal').classList.add('hidden');
        document.getElementById('filterModal').classList.remove('flex');
    });
    document.getElementById('applyFilters').addEventListener('click', () => {
        currentFilters.employees = $('#filterEmployee').val() || [];
        currentFilters.keyword = document.getElementById('filterKeyword').value.trim();
        currentFilters.date = document.getElementById('filterDate').value || currentFilters.date;
        document.getElementById('filterModal').classList.add('hidden');
        document.getElementById('filterModal').classList.remove('flex');
        loadAppointments();
    });

    // kick-off
    document.getElementById('goToToday').click();
    populateMonthDropdown(currentYear);
    monthSelect.dispatchEvent(new Event('change'));
});
</script>

<script>
function openAppointmentModal(){
    const modal = document.getElementById('createAppointmentModal');
    modal.classList.remove('hidden','opacity-0');
    modal.classList.add('flex');
}
function closeAppointmentModal(){
    const modal = document.getElementById('createAppointmentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('mobileAppointmentForm');

    // Google Places Autocomplete
    window.initMap = function () {
        const input = document.getElementById("autocomplete");
        if (!input) return;

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            componentRestrictions: { country: 'de' }
        });

        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            const components = {
                street_number: '',
                route: '',
                locality: '',
                postal_code: ''
            };

            place.address_components.forEach(component => {
                const types = component.types;
                if (types.includes('street_number')) components.street_number = component.long_name;
                if (types.includes('route')) components.route = component.long_name;
                if (types.includes('locality')) components.locality = component.long_name;
                if (types.includes('postal_code')) components.postal_code = component.long_name;
            });

            form.querySelector('[name="street"]').value = `${components.route} ${components.street_number}`.trim();
            form.querySelector('[name="postcode"]').value = components.postal_code;
            form.querySelector('[name="city"]').value = components.locality;
            form.querySelector('[name="latitude"]').value = place.geometry.location.lat();
            form.querySelector('[name="longitude"]').value = place.geometry.location.lng();
        });
    };

    // Submit Form via AJAX
    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("/appointments/store/mobile", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Gespeichert', text: 'Termin erfolgreich erstellt.' });
                form.reset();
                closeAppointmentModal();
                document.getElementById('goToToday')?.click();
            } else {
                Swal.fire({ icon: 'error', title: 'Fehler', text: data.message || 'Speichern fehlgeschlagen.' });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({ icon: 'error', title: 'Serverfehler', text: 'Ein Fehler ist aufgetreten.' });
        });
    });

    // Contact Select2
    $('#contactSelect').select2({
        ajax: {
            url: '/get/contact/list',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: `${item.type}:${item.main_id}:${item.sub_id}`,
                    text: `${item.name} ${item.lastname} (${item.type})`,
                    phone: item.phone,
                    email: item.email,
                    street: item.street,
                    city: item.city,
                    postcode: item.postcode,
                    lat: item.latitude,
                    lon: item.longitude,
                    isCustomer: item.type === 'Kunde',
                    customerId: item.type === 'Kunde' ? item.main_id : null
                }))
            })
        },
        placeholder: 'Kontakt auswählen',
        allowClear: true
    });

    $('#contactSelect').on('select2:select', function (e) {
        const d = e.params.data;
        document.getElementById('contactPhone').value = d.phone || '';
        document.getElementById('contactEmail').value = d.email || '';
        form.querySelector('[name="street"]').value = d.street || '';
        form.querySelector('[name="postcode"]').value = d.postcode || '';
        form.querySelector('[name="city"]').value = d.city || '';
        form.querySelector('[name="latitude"]').value = d.lat || '';
        form.querySelector('[name="longitude"]').value = d.lon || '';

        if (d.isCustomer) {
            $('#productSelect').prop('disabled', false).empty();
            loadProductsByCustomer(d.customerId);
        } else {
            $('#productSelect').empty().prop('disabled', true);
        }
    });

    function loadProductsByCustomer(customerId) {
        $.get(`/api/products-by-customer?customer_id=${customerId}`, function (data) {
            const $select = $('#productSelect');
            $select.empty();
            data.forEach(group => {
                const optgroup = $('<optgroup>', { label: group.text });
                group.children.forEach(p => {
                    optgroup.append(`<option value="${p.customer_id}_${p.alternative_id}_${p.product_id}">${p.product_name}</option>`);
                });
                $select.append(optgroup);
            });
        });
    }
});
</script>

<script>
$(document).ready(function () {
    $('#filterEmployee').select2({
        placeholder: "Mitarbeiter auswählen",
        width:'100%'
    });

    $('#employeeSelect').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: m => m,
        width:'100%'
    });

    function formatEmployee(employee){
        if(!employee.id) return employee.text;
        const img = $(employee.element).data('image');
        const name = employee.text;
        return `
            <div class="flex items-center">
                <img src="${img}" class="w-6 h-6 rounded-full mr-2 object-cover" />
                <span>${name}</span>
            </div>
        `;
    }

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
