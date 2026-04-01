@extends('admin.layouts.app')
@section('title')
Mein Kalendar
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
  <style>
/* Theme */
:root{
  --brand:#8fc73e;
  --brand-2:#74b2d4;
  --accent:#00aaff;
  --fc-day-bg:#f8f9fa;
  --muted:#626262;
}

/* FullCalendar base */
.fc .fc-button{background:var(--brand)!important;border:0!important;margin-right:3px!important}
.fc .fc-button-active{background:var(--brand-2)!important}
.fc .fc-toolbar-title{color:var(--muted)}
.fc .fc-view,.fc-daygrid{background:#fff}
.fc .fc-day-today{background:#f1f1f1!important}
.fc-h-event{border:1px solid #e8eaec!important;border-left-width:0}
.fc-v-event{background:#fff!important}
.fc-timegrid-slot-minor{display:none!important}
.fc-license-message{display:none!important}
.fc-popover{position:absolute!important}
.fc-timeGridWeek-view,.fc-timeGridDay-view,.fc-listWeek-view{background:#fff!important;height:auto!important;overflow-y:auto}

/* DayGrid events */
.fc-daygrid-event{
  display:block;width:100%;
  background:var(--fc-day-bg);
  border-left:4px solid var(--accent);
  padding:10px;border-radius:6px;
  color:#333;text-decoration:none;
  transition:background-color .3s ease;
  white-space:normal!important;word-wrap:break-word!important;
  overflow:hidden!important;text-overflow:ellipsis!important
}
.fc-daygrid-event:hover{background:#f2f2f2}

/* TimeGrid events */
.fc-timeGridWeek-view .fc-timegrid-event{max-height:600px;overflow-y:auto}
.fc-popover .fc-timegrid-event{
  display:flex!important;position:relative!important;min-height:20px!important;
  width:auto!important;white-space:normal;font-size:12px;padding:4px
}
.fc-popover .fc-timegrid-slot{height:50px}
.fc-timegrid-event{background-color:inherit!important;color:inherit!important}

/* Custom event content */
.custom-event{display:flex;flex-direction:column;gap:0}
.custom-event-status{display:flex;align-items:center;font-size:.9rem;color:#28a745;font-weight:600}
.custom-event-status i{margin-right:5px}
.custom-event-title{font-size:1rem;font-weight:700;color:#333;margin:0 0 5px}
.custom-event-product{display:flex;justify-content:space-between;font-size:.9rem;color:#007bff}
.custom-event-product ul{margin:0;padding:0;display:flex;gap:5px}
.custom-event-product ul li img{border-radius:50%}
.custom-event-product-status{font-weight:600}
.custom-event-time{font-size:.8rem;color:#666}

/* Dropdown */
.custom-dropdown-menu{
  display:none;position:absolute;background:#fff;box-shadow:0 4px 8px rgba(0,0,0,.1);
  border-radius:5px;z-index:100;margin-top:-116px;margin-left:249px;padding:10px
}
.custom-dropdown-menu ul{list-style:none;margin:0;padding:0}
.custom-dropdown-menu ul li{padding:8px 15px;cursor:pointer}
.custom-dropdown-menu ul li:hover{background:#f0f0f0}
.event_drop_down{cursor:pointer;position:relative}

/* Utilities */
.emp_active{border:3px solid var(--brand)}
.task-bg,.task-event{background:#D6EAF9!important}
.appointmetn-bg,.appointment-event{background:#E5F0D5!important}
.calendar{height:100%!important;overflow-y:auto}
.fc-more-link{width:45px;background:#f1f1f1}
.fc-more-link .fc-timegrid-more-link-inner{font-size:22px;justify-self:anchor-center}
.fc-timegrid-slots table tr{height:34px!important}
.fc-timegrid-slots{overflow-y:auto;max-height:100%}
.fc-event-main-frame,.fc-event-main{display:none!important}
.select2-selection__choice{border:0!important}
.line{width:90%;border-bottom:2px solid #b8b8b8;margin:6px 0}
.fc-ticket-link:hover{opacity:.8}
.mobile_view_event{font-family:Arial,sans-serif;font-size:11px;line-height:1.3;word-wrap:break-word;overflow-wrap:break-word}

/* Public holidays */
.public-holiday-cell{background:#d3d3d3!important}
.fc .public-holiday-cell{background:#f8f9fa!important}

/* All-day styles */
.fc .fc-all-day-event{
  background:#e3f2fd!important;border-left:4px solid #2196f3!important;
  font-size:12px;font-weight:700;padding:4px
}
.custom-all-day{background:#ffedcc!important;border-left:4px solid #ff9800!important;color:#333!important;font-weight:700;padding:4px 6px}
.custom-all-day .custom-event-header{display:none!important}
.custom-all-day .custom-event-header .custom-event-title>p{margin:0;padding:0}

/* Recurring leave */ 
.fc-event.recurring-leave{
  background:repeating-linear-gradient(45deg,#6c757d,#6c757d 5px,#9ca3af 5px,#9ca3af 10px);
  color:#000!important; /* <--- Changed to Black */
  border:1px solid #6c757d!important;
  border-radius:6px;
  font-size:11px;
  padding:4px;
  font-weight: 600; /* Added bold for better visibility */
}
.fc-event.recurring-leave::before{content:"🔁 ";margin-right:3px}

/* Animations */
@keyframes pulseScale{
  0%{transform:scale(1);color:currentColor}
  100%{transform:scale(1.2);color:var(--pulse-color,currentColor)}
}
#bellIcon{--pulse-color:red;animation:pulseScale 1s ease-in-out infinite}
.warning_text{--pulse-color:#ff9f43;animation:pulseScale 1s ease-in-out infinite}
.edited-event{animation:blink-effect 1s ease-in-out 3;border:3px solid red!important}
@keyframes blink-effect{0%,100%{opacity:1}50%{opacity:.2}}

/* Mini calendar */
#mini_calendar .fc-daygrid-day-events{display:none!important}
#mini_calendar .fc-dayGridMonth-view{background:#f1f1f1}
#mini_calendar .fc-daygrid-day-bottom{display:none!important}
#mini_calendar .fc-day-selected .fc-daygrid-day-frame::after{
  content:"";position:absolute;top:50%;left:50%;width:30px;height:30px;background:#d4d4e4!important;
  border-radius:50%;transform:translate(-50%,-50%);z-index:-1
}
#mini_calendar .fc-day-selected .fc-daygrid-day-frame{background:#d4d4e4!important;border-radius:50%}
#mini_calendar .fc-day{padding:0!important;justify-items:center}
#mini_calendar .fc-toolbar-title{font-size:19px}

/* Sidebar */
#slider_section{overflow:hidden;height:100%;transition:all .2s ease}
.employee_lists{overflow-y:auto;max-height:calc(100dvh - 300px);padding-right:4px}
.employee_lists::-webkit-scrollbar,#slider_section::-webkit-scrollbar{width:6px}
.employee_lists::-webkit-scrollbar-thumb,#slider_section::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px}
#slider_section::-webkit-scrollbar-thumb{border-radius:4px}

/* Calendar menu */
.calendar_menu{position:absolute!important;bottom:173px!important;left:86%!important}
.calendar_menu button{color:#fff;font-size:18px}

/* SweetAlert tuning */
.custom-swal-popup{color:#fff!important;border-radius:10px;text-align:left;background:#2d3e50}
.custom-confirm-btn{background:#c23a1c!important;color:#fff!important;font-weight:700;border-radius:5px}
.custom-cancel-btn{background:var(--brand-2)!important;color:#fff!important;font-weight:700;border-radius:5px}
.swal2-html-container .custom-event a{font-size:14px;color:#2c3e50!important}
.swal2-html-container .custom-event p{font-size:12px;color:var(--brand-2)!important}
.swal2-title,.swal2-html-container{text-align:left!important}
.swal2-close{color:#fff!important}
.swal2-close:hover{color:red!important}

/* Picker UI */
.picker-avatar{width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid #dee2e6}
.picker-card{border:1px solid #e9ecef;border-radius:.5rem;padding:.5rem}
.picker-chip{display:flex;align-items:center;gap:.5rem;padding:.35rem .5rem;border:1px solid #e9ecef;border-radius:9999px;cursor:pointer}
.picker-chip.active{border-color:var(--brand);background:#f6fff1}
.picker-list-item{padding:.35rem .5rem;cursor:pointer;border-bottom:1px dashed #eee}
.picker-list-item:hover{background:#f8f9fa}

/* Modal: TERMIN ERSTELLEN */
.new_task{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  z-index:10;width:60%!important;max-width:1100px!important;max-height:85vh;overflow-y:auto;
  background:#f5f6f8;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:0
}
.new_task .modal-body{max-height:85vh;overflow-y:auto;padding:15px}
.new_task .modal-header{position:sticky;top:0;background:#fff;z-index:10;padding:10px;border-bottom:1px solid #ddd}
.new_task .modal-footer{position:sticky;bottom:0;background:#e7e6e6!important;z-index:10;padding:10px;border-top:1px solid #ddd}
.new_task .card-header{border-bottom:1px solid #dee2e6;padding:12px 20px}
.new_task .card-body{padding:16px 20px}
.new_task .modal-body .row>[class^="col-"],.new_task .modal-body .row>[class*=" col-"]{margin-bottom:10px}
@media (min-width:992px){
  .new_task .form-body .row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));grid-column-gap:16px}
  .new_task .form-body .row>.col-md-12,.new_task .form-body .row>.col-12{grid-column:1/-1}
}
@media (max-width:991.98px){
  .new_task{width:95%!important;max-width:95%!important;top:52%}
  .new_task .card-body{padding:12px 12px 80px}
  .new_task label,
  .new_task .form-control,
  .new_task .select2-container--default .select2-selection--single,
  .new_task .select2-container--default .select2-selection--multiple{font-size:13px}
  .new_task .form-control,
  .new_task .select2-container--default .select2-selection--single,
  .new_task .select2-container--default .select2-selection--multiple{min-height:34px}
}
.new_task_close{position:absolute;z-index:4;left:-135px;top:16%}
#inquiryPreviewWrapper{border-radius:8px;border:1px solid #dee2e6;padding:8px;background:#fff}
#inquiryPreviewTable th,#inquiryPreviewTable td{vertical-align:middle;font-size:12px}
#inquiryPreviewTable th{background:#f8f9fa;white-space:nowrap}
#participantsBlock.hidden-by-inquiry{display:none!important}

/* View-specific helpers */
.fc-timeGridWeek-view .mobile_title{transform:rotate(90deg)!important;color:gray}
.fc-timeGridWeek-view .mobile_view{display:flex;align-items:center;flex-direction:column}

/* Responsive tweaks */
@media (max-width:768px){
  .fc-header-toolbar{flex-direction:column}
  .fc-daygrid-day{min-height:100px!important}
  .fc-daygrid-day-frame{height:100%;display:flex;flex-direction:column;justify-content:center}
  .fc-daygrid-day-events{flex-grow:1;display:flex;align-items:center;justify-content:center}
  .fc-daygrid-event{font-size:14px!important;padding:8px!important;min-width:80%;text-align:center;display:inline-block}
}
@media (max-width:1394px){
  #calendar_icons,#calendar_times,#mini_calendar{display:none!important}
}
@media (max-width:576px){
  .employee_search_input,.task_search_input,.appointment_search_input{margin-bottom:10px}
}

</style>

<style>
  .customer-products-box{
    margin-top:12px;
    padding:12px;
    border-radius:12px;
    background:#f8fafc;
    border:1px solid #e5e7eb;
  }

  .customer-products-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:10px;
    flex-wrap:wrap;
  }

  .customer-products-title{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    font-weight:700;
    color:#1f2937;
  }

  .customer-products-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 12px;
    border-radius:999px;
    background:linear-gradient(135deg,#ecfdf5,#d1fae5);
    color:#065f46;
    border:1px solid #a7f3d0;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
    transition:all .2s ease;
  }

  .customer-products-badge:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 14px rgba(16,185,129,.15);
  }

  .customer-products-summary{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-top:8px;
  }

  .customer-products-chip{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:5px 10px;
    border-radius:999px;
    background:#ffffff;
    border:1px solid #dbeafe;
    color:#1e3a8a;
    font-size:11px;
    font-weight:600;
  }

  .customer-products-empty{
    padding:10px 0;
    color:#6b7280;
    font-size:12px;
  }

  .swal-products-list{
    max-height:420px;
    overflow:auto;
    padding-right:4px;
    text-align:left;
  }

  .swal-product-card{
    display:flex;
    gap:12px;
    align-items:flex-start;
    padding:12px;
    margin-bottom:10px;
    border:1px solid #e5e7eb;
    border-radius:14px;
    background:#fff;
    box-shadow:0 4px 12px rgba(15,23,42,.05);
  }

  .swal-product-icon{
    width:42px;
    height:42px;
    min-width:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#eff6ff,#dbeafe);
    color:#1d4ed8;
    border:1px solid #bfdbfe;
  }

  .swal-product-content{
    flex:1;
    min-width:0;
  }

  .swal-product-name{
    font-size:14px;
    font-weight:700;
    color:#111827;
    margin-bottom:5px;
    word-break:break-word;
  }

  .swal-product-meta{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
  }

  .swal-product-pill{
    display:inline-flex;
    align-items:center;
    padding:4px 8px;
    border-radius:999px;
    background:#f9fafb;
    border:1px solid #e5e7eb;
    color:#4b5563;
    font-size:11px;
    font-weight:600;
  }
</style>

@endsection 
@section('content') 

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12" style=" display: flex;">
                <h2 class="content-header-title float-left mb-0">KALENDER</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashbaord</a></li> 
                        <li class="breadcrumb-item active">Kalender</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-body">

        <div class="text-right mb-2">
             <button class="btn"    data-toggle="modal" data-target="#calendarSettingsModal">
               <i class="feather icon-settings"></i> Einstellungen
            </button>

            <!-- button here -->
        </div>

            <div class="row">
                <!-- Sidebar (Filters, Search, Employees) -->
                <div class="col-md-2 col-12" id="slider_section" style="overflow-y: auto;">
                    <div class="cards">
                        <div id="mini_calendar"></div>
                    </div>
                    <div class="cards mt-1">
                        <div class="card-body">
                            <div class="col-12 p-0">
                                <!-- Search Inputs -->
                                <div class="col-12 employee_search_input">
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="searchEmployee" id="employee_get"
                                            placeholder="Vorname, nachname,...">
                                        <div class="form-control-position"><i class="feather icon-search"></i></div>
                                    </fieldset>
                                </div>

                                <!-- Employee List -->
                                <div class="employee_lists" id="search_emp_result">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Section -->
                <div class="col-md-10 col-12 calender_section">
                    <div class="calendar"></div>
                </div>
            </div>

          <div class="cards new_task_card new_task" style="display:none">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="title">TERMIN ERSTELLEN</h3>
                <button type="button" class="btn btn-sm btn-outline-secondary close_task_window">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="card-body">
                <form id="task-store-form">
                    @csrf
                    <input type="hidden" name="id" id="appointment_id">
                    <input type="hidden" name="contact_mode" id="contact_mode" value="new">
                    <input type="hidden" id="problem_id" name="problem_id">
                    <input type="hidden" id="problem_task_id" name="problem_task_id">
                    <input type="hidden" id="ticket_mode" name="ticket_mode" value="new">
                    <input type="hidden" id="ticket_auto_create" name="ticket_auto_create" value="0">
                    <input type="hidden" id="products" name="products">

                    <div class="modal-body">

                        {{-- SECTION: Kontakt / Ticketwahl --}}
                        <div class="section-title">Kontakt</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-12 mb-1">
                                    <label>Typ</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input contact-type-toggle" type="radio"
                                            name="contact_mode_radio" id="newContact" value="new" checked>
                                        <label class="form-check-label" for="newContact">Neu</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input contact-type-toggle" type="radio"
                                            name="contact_mode_radio" id="selectContact" value="select">
                                        <label class="form-check-label" for="selectContact">Kontakt</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input contact-type-toggle" type="radio"
                                            name="contact_mode_radio" id="ticketMode" value="ticket">
                                        <label class="form-check-label" for="ticketMode">Ticket</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Ticket-Block --}}
                            <div class="ticket-block d-none">
                                <div class="form-row">
                                    <div class="col-md-12 mb-1">
                                        <label>Kunde *</label>
                                        <select id="ticket_customer_id" class="form-control" style="width:100%"></select>
                                    </div>
                                    <div class="col-md-12 mb-1">
                                        <label>Problem (Ticket)</label>
                                        <select id="ticket_problem_id" class="form-control" style="width:100%"></select>
                                    </div>
                                    <div class="col-md-12 mb-1">
                                        <label>Ticket Task</label>
                                        <select id="ticket_task_id" class="form-control" style="width:100%"></select>
                                    </div>
                                    <div class="col-md-12 mb-1">
                                        <label>Leistung/Service (optional)</label>
                                        <select id="ticket_service_id" class="form-control" style="width:100%"></select>
                                    </div>
                                </div>
                            </div>

                            {{-- Neu / Kontakt-Auswahl --}}
                            <div class="form-row">
                                <div class="col-md-12 contact-name-block">
                                    <label for="name">Kunde/Kontakt *</label>
                                    <input type="text" id="name" class="form-control name" name="name">
                                </div>

                                <div class="col-md-12 contact-select-block d-none">
                                    <label for="customer_id">Kunde/Kontakt *</label>
                                    <select name="customer_id" id="customer_id" class="contact_list" style="width:100%"></select>
                                    <input type="hidden" name="contact_type" id="contact_type" value="">
                                </div>

                                <div class="col-md-12 product-select-block d-none">
                                    <label for="productSelect">Object/Produkt</label>
                                    <select id="productSelect" name="productSelect[]" class="form-control" multiple style="width:100%"></select>
                                   
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: Termin-Daten --}}
                        <div class="section-title">Termin</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-10 col-10 mb-1">
                                    <label for="appointment_type">Art des Termins</label>
                                    <input type="text" class="form-control" id="appointment_type" name="appointment_type"
                                        value="{{ old('appointment_type') }}">
                                </div>
                                <div class="col-md-2 col-2 mb-1 d-flex align-items-end">
                                    <input type="hidden" name="color" id="color" value="#8fc73e">
                                    <div class="btn-group dropup dropdown-icon-wrapper w-100" id="color_drop_down">
                                        <button type="button" class="btn btn-light btn-block" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="true">
                                            <i class="fa fa-square" id="colorIcon" style="color:#8fc73e;"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <span class="dropdown-item" data-value="#8fc73e"><i class="fa fa-square" style="color:#8fc73e;"></i> Grün</span>
                                            <span class="dropdown-item" data-value="#ff0000"><i class="fa fa-square" style="color:#ff0000;"></i> Rot</span>
                                            <span class="dropdown-item" data-value="#0000ff"><i class="fa fa-square" style="color:#0000ff;"></i> Blau</span>
                                            <span class="dropdown-item" data-value="#ffff00"><i class="fa fa-square" style="color:#ffff00;"></i> Gelb</span>
                                            <span class="dropdown-item" data-value="#ff00ff"><i class="fa fa-square" style="color:#ff00ff;"></i> Magenta</span>
                                            <span class="dropdown-item" data-value="#00ffff"><i class="fa fa-square" style="color:#00ffff;"></i> Cyan</span>
                                            <span class="dropdown-item" data-value="#000000"><i class="fa fa-square" style="color:#000000;"></i> Schwarz</span>
                                            <span class="dropdown-item" data-value="#808080"><i class="fa fa-square" style="color:#808080;"></i> Grau</span>
                                            <span class="dropdown-item" data-value="#ffa500"><i class="fa fa-square" style="color:#ffa500;"></i> Orange</span>
                                            <span class="dropdown-item" data-value="#800080"><i class="fa fa-square" style="color:#800080;"></i> Lila</span>
                                            <span class="dropdown-item" data-value="#8b4513"><i class="fa fa-square" style="color:#8b4513;"></i> Braun</span>
                                            <span class="dropdown-item" data-value="#4682b4"><i class="fa fa-square" style="color:#4682b4;"></i> Stahlblau</span>
                                            <span class="dropdown-item" data-value="#5f9ea0"><i class="fa fa-square" style="color:#5f9ea0;"></i> Kadettenblau</span>
                                            <span class="dropdown-item" data-value="#d2691e"><i class="fa fa-square" style="color:#d2691e;"></i> Schokoladenbraun</span>
                                            <span class="dropdown-item" data-value="#2e8b57"><i class="fa fa-square" style="color:#2e8b57;"></i> Seegrün</span>
                                            <span class="dropdown-item" data-value="#dc143c"><i class="fa fa-square" style="color:#dc143c;"></i> Karmesinrot</span>
                                            <span class="dropdown-item" data-value="#7fffd4"><i class="fa fa-square" style="color:#7fffd4;"></i> Aquamarin</span>
                                            <span class="dropdown-item" data-value="#9932cc"><i class="fa fa-square" style="color:#9932cc;"></i> Dunkles Lila</span>
                                            <span class="dropdown-item" data-value="#ff6347"><i class="fa fa-square" style="color:#ff6347;"></i> Tomate</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12 mb-1">
                                    <label for="start_date">Startdatum *</label>
                                    <input type="date" id="start_date" class="form-control" name="start_date">
                                </div>
                                <div class="col-md-6 col-12 mb-1">
                                    <label for="end_date">Enddatum *</label>
                                    <input type="date" id="end_date" class="form-control" name="end_date">
                                </div>

                                <div class="col-md-4 col-12 mb-1">
                                    <label for="start_time">Startzeit *</label>
                                    <input type="time" id="start_time" class="form-control" name="start_time">
                                </div>
                                <div class="col-md-4 col-12 mb-1">
                                    <label for="end_time">Endzeit</label>
                                    <input type="time" id="end_time" class="form-control" name="end_time">
                                </div>
                                <div class="col-md-4 col-12 mb-1">
                                    <label for="total_time">Dauer</label>
                                    <input type="number" id="total_time" class="form-control" name="total_time">
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: Einstellungen / Anfrage --}}
                        <div class="section-title">Einstellungen</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-4 mb-1">
                                    <label for="switchPublic">Öffentlich</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="switchPublic" name="public" checked>
                                        <label class="custom-control-label" for="switchPublic">
                                            <span class="switch-icon-left"><i class="feather icon-unlock"></i></span>
                                            <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="switchContact">Anfrage</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="switchContact" name="is_contact">
                                        <label class="custom-control-label" for="switchContact">
                                            <span class="switch-icon-left"><i class="feather icon-user"></i></span>
                                            <span class="switch-icon-right"><i class="feather icon-user-x"></i></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label for="switchReport">Report</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="switchReport" name="is_report">
                                        <label class="custom-control-label" for="switchReport">
                                            <span class="switch-icon-left"><i class="feather icon-file-text"></i></span>
                                            <span class="switch-icon-right"><i class="feather icon-file"></i></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-1" id="preTypeBox" style="display:none;">
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

                                <div class="col-md-6 mb-1" id="sourceBox" style="display:none;">
                                    <label for="source">Quelle</label>
                                    <select name="source" id="source" class="form-control" style="width:100%">
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
                                        <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2 d-none" id="inquiryPreviewWrapper">
                                <label>Anfrage-Übersicht</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0" id="inquiryPreviewTable">
                                        <thead>
                                        <tr>
                                            <th>Produkt</th>
                                            <th>Abteilung</th>
                                            <th>Leistung/Service</th>
                                            <th>Innendienst</th>
                                            <th>Außendienst</th>
                                        </tr>
                                        </thead>
                                        <tbody id="inquiryPreviewBody">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-1 text-right">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addInquiryRow">
                                        <i class="feather icon-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: Teilnehmer --}}
                        <div class="section-title">Teilnehmer</div>
                        <div class="section-box" id="participantsBlock">
                            <div class="d-flex align-items-center mb-1">
                                <button type="button" id="btnClearEmployees" class="btn btn-sm btn-light mr-1">
                                    <i class="feather icon-x-circle"></i> Auswahl leeren
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="openPickerBtn">
                                    <i class="feather icon-users"></i> Auswahl öffnen
                                </button> 
                            </div>

                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}">
                                        {{ $emp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SECTION: Ort & Kontakt --}}
                        <div class="section-title">Ort & Kontakt</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-6 mb-1" id="intern" style="display:none;">
                                    <label for="branch_address_id">Adresse (Betrieb)</label>
                                    <select name="branch_address_id" class="form-control">
                                        <option></option>
                                        @foreach ($branch_addresses as $address)
                                            <option value="{{ $address->id }}"
                                                    data-street="{{ $address->street }}"
                                                    data-latitude="{{ $address->latitude }}"
                                                    data-longitude="{{ $address->longitude }}"
                                                    data-city="{{ $address->city }}"
                                                    data-postcode="{{ $address->postcode }}">
                                                {{ $address->branch_initial }} - {{ $address->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-1" id="extern">
                                    <label for="full_address">Adresse</label>
                                    <input id="full_address" type="text" class="form-control form-element"
                                        placeholder="Adresse eingeben" name="full_address">

                                    <input type="hidden" id="street-input" name="street">
                                    <input type="hidden" id="city-input" name="city">
                                    <input type="hidden" id="latitude-input" name="latitude">
                                    <input type="hidden" id="longitude-input" name="longitude">
                                    <input type="hidden" id="postal_code-input" name="postcode">
                                </div>

                                <div class="col-md-6 mb-1">
                                    <label for="execution_type">Ort des Termins</label>
                                    <select name="execution_type" id="execution_type" class="form-control">
                                        <option value="internal">Intern</option>
                                        <option value="external" selected>Extern</option>
                                        <option value="online">Online</option>
                                        <option value="telephone">Telefon</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-1">
                                    <label for="phone">Telefon</label>
                                    <input type="text" class="form-control phone" name="phone" id="phone" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-6 mb-1">
                                    <label for="email">Email <small>Optional</small></label>
                                    <input type="email" class="form-control email" name="email" id="email" value="{{ old('email') }}">
                                </div>

                                <div class="col-md-6 mb-1" id="link_section" style="display:none;">
                                    <label for="link">Link</label>
                                    <input type="text" class="form-control" id="link" name="link" value="{{ old('link') }}">
                                </div>

                                <div class="col-md-6 mb-1">
                                    <label for="branch_id">Betrieb</label>
                                    <select name="branch_id" id="branch_id" class="selectables" style="width:100%">
                                        <option></option>
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-1">
                                    <label for="description">Beschreibung</label>
                                    <textarea name="description" class="form-control" id="description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: Nachfass / Nächste Schritte --}}
                        <div class="section-title">Nachfass & nächste Schritte</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-4 col-12 mb-1">
                                    <label for="reminder_date">Nachfasstermin</label>
                                    <input type="date" name="reminder_date" class="form-control" id="reminder_date">
                                </div>
                                <div class="col-md-4 col-12 mb-1">
                                    <label for="next_step">Nächster Schritt</label>
                                    <select name="next_step" class="form-control select2" id="next_step" style="width:100%">
                                        <option value="">Bitte wählen</option>
                                        <option value="Rückruf erledigen">Rückruf erledigen</option>
                                        <option value="Problem klären">Problem klären</option>
                                        <option value="E-Mail senden">E-Mail senden</option>
                                        <option value="Angebot nachfassen">Angebot nachfassen</option>
                                        <option value="Projektbesprechung vorbereiten">Projektbesprechung vorbereiten</option>
                                        <option value="Kein weiterer Schritt">Kein weiterer Schritt</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-12 mb-1">
                                    <label for="report_responsible">Verantwortlicher</label>
                                    <select name="report_responsible[]" class="form-control select2" id="report_responsible" style="width:100%">
                                        <option value="">Bitte wählen</option>
                                        @foreach ($allEmployees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: Priorität & Wiederholung --}}
                        <div class="section-title">Priorität & Wiederholung</div>
                        <div class="section-box">
                            <div class="form-row">
                                <div class="col-md-6 col-12 mb-1">
                                    <label for="priority">Priorität</label>
                                    <select name="priority" class="form-control" id="priority">
                                        <option value="normal">Keiner</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">Hoch</option>
                                        <option value="very high">Sehr Wichtig</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12 mb-1">
                                    <label for="date_type">Wiederholung</label>
                                    <select name="date_type" id="date_type" class="form-control">
                                        <option>Wählen</option>
                                        <option value="day">Ganzer Tag</option>
                                        <option value="week">7 Tage (Eine Woche)</option>
                                        <option value="daily">Täglich</option>
                                        <option value="weekly">Wochen</option>
                                        <option value="monthly">Monatlich</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12 mb-1" id="week_dropdown_container" style="display:none;">
                                    <label for="week_select">Wähle Woche(n)</label>
                                    <select id="week_select" name="week_select[]" class="form-control" style="width:100%;"></select>
                                </div>

                                <div class="col-md-6 col-12 mb-1 from_day">
                                    <label for="from_day">Von (Wochentag)</label>
                                    <select name="from_day" id="from_day" class="form-control">
                                        <option value="monday">Montag</option>
                                        <option value="tuesday">Dienstag</option>
                                        <option value="wednesday">Mittwoch</option>
                                        <option value="thursday">Donnerstag</option>
                                        <option value="friday">Freitag</option>
                                        <option value="saturday">Samstag</option>
                                        <option value="sunday">Sonntag</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12 mb-1 to_day">
                                    <label for="to_day">Zu (Wochentag)</label>
                                    <select name="to_day" id="to_day" class="form-control">
                                        <option value="monday">Montag</option>
                                        <option value="tuesday">Dienstag</option>
                                        <option value="wednesday">Mittwoch</option>
                                        <option value="thursday">Donnerstag</option>
                                        <option value="friday">Freitag</option>
                                        <option value="saturday">Samstag</option>
                                        <option value="sunday">Sonntag</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12 mb-1 from_month">
                                    <label for="from_month">Von (Monat)</label>
                                    <select name="from_month" id="from_month" class="form-control">
                                        <option value="january">Januar</option>
                                        <option value="february">Februar</option>
                                        <option value="march">März</option>
                                        <option value="april">April</option>
                                        <option value="may">Mai</option>
                                        <option value="june">Juni</option>
                                        <option value="july">Juli</option>
                                        <option value="august">August</option>
                                        <option value="september">September</option>
                                        <option value="october">Oktober</option>
                                        <option value="november">November</option>
                                        <option value="december">Dezember</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12 mb-1 to_month">
                                    <label for="to_month">Zu (Monat)</label>
                                    <select name="to_month" id="to_month" class="form-control">
                                        <option value="january">Januar</option>
                                        <option value="february">Februar</option>
                                        <option value="march">März</option>
                                        <option value="april">April</option>
                                        <option value="may">Mai</option>
                                        <option value="june">Juni</option>
                                        <option value="july">Juli</option>
                                        <option value="august">August</option>
                                        <option value="september">September</option>
                                        <option value="october">Oktober</option>
                                        <option value="november">November</option>
                                        <option value="december">Dezember</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm close_task_window">
                            <i class="feather icon-x"></i> abbrechen
                        </button>
                        <button type="button" class="btn btn-primary btn-sm save-task">
                            <i class="feather icon-save"></i> speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>

        </div>
    </div>
</div> 
<!-- Team/Employee Picker Modal -->
<div class="modal fade" id="pickerModal" tabindex="-1" role="dialog" aria-labelledby="pickerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="pickerModalLabel">Teilnehmer auswählen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0">
        <ul class="nav nav-tabs px-2 pt-2" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="tab-employees" data-toggle="tab" href="#pane-employees" role="tab">Mitarbeiter</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="tab-teams" data-toggle="tab" href="#pane-teams" role="tab">Teams</a>
          </li>
        </ul>

        <div class="tab-content p-2">
          <!-- Tab 1: Employees -->
          <div class="tab-pane fade show active" id="pane-employees" role="tabpanel" aria-labelledby="tab-employees">
            <div class="form-group mb-2">
              <input type="text" class="form-control" id="pickerEmployeeSearch" placeholder="Mitarbeiter suchen…">
            </div>
            <div id="pickerEmployeeGrid" class="d-flex flex-wrap" style="gap:10px;"></div>
          </div>

          <!-- Tab 2: Teams -->
          <div class="tab-pane fade" id="pane-teams" role="tabpanel" aria-labelledby="tab-teams">
            <div class="row no-gutters">
              <div class="col-md-4 border-right">
                <div class="form-group px-2">
                  <input type="text" class="form-control" id="pickerTeamSearch" placeholder="Team suchen…">
                </div>
                <div id="pickerTeamList" style="max-height: 60vh; overflow:auto;"></div>
              </div>
              <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center px-2">
                  <h6 class="m-0"><span id="pickerTeamTitle">Team</span></h6>
                  <div>
                    <button class="btn btn-sm btn-light" id="pickerSelectAllTeam">Alle markieren</button>
                    <button class="btn btn-sm btn-light" id="pickerClearTeam">Leeren</button>
                    <button class="btn btn-sm btn-success" id="pickerApplyTeam"><i class="feather icon-check"></i> Übernehmen</button>
                  </div>
                </div>
                <div id="pickerTeamMembers" class="p-2"></div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /modal-body -->

      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="pickerApplyAll"><i class="feather icon-save"></i> Auswahl übernehmen</button>
      </div>
    </div>
  </div>
</div> 
<!-- Calendar Settings Modal -->
<div class="modal fade" id="calendarSettingsModal" tabindex="-1" role="dialog" aria-labelledby="calendarSettingsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="calendarSettingsForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="calendarSettingsLabel">Einstellungen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Favorite Employees -->
                <div class="form-group">
                    <label for="favoriteEmployees">Favoriten Mitarbeiter</label>
                    <select id="favoriteEmployees" class="form-control employee" multiple style="width:100%;">
                        @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" data-image="/images/employee/{{ $emp->image }}">
                            {{ $emp->name }} {{ $emp->lastname }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden Views -->
                <div class="form-group">
                    <label>Ausgeblendete Ansichten</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="year"> Jahr</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="month"> Monat</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="week"> Woche</label><br>
                </div>

                <!-- Calendar Color -->
                <div class="form-group">
                    <label for="calendarColorPicker">Kalenderfarbe</label>
                    <select id="calendarColorPicker" class="form-control">
                        <option value="default">Standard</option>
                        <option value="black">Schwarz</option>
                        <option value="red">Rot</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Speichern</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
            </div>
        </form>
    </div>
</div> 
@endsection 

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}"></script> 
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
const baseUrl = "{{ asset('images/employee/')}}";
</script>
<script>
    const settings = {
        favorite_employee_ids: @json($favorite_employee_ids)
    };
    window.favoriteEmployeeIds = settings.favorite_employee_ids || [];

    (function normalizeFavoriteIds(){
    const fromBlade = (settings.favorite_employee_ids || settings.favorite_employees || []);
    window.favoriteEmployeeIds = fromBlade.map(String);
    })();
</script>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        // Define the mobile/tablet breakpoint
        const maxWidthForMobile = 1024;

        // Check screen width and redirect if it's mobile or tablet
        if (window.innerWidth < maxWidthForMobile) {
            window.location.href = "{{ route('mobile.mobile_calendar.index') }}";

        }
    });
</script>
 



<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
@php
    $userKey = auth()->id();
    $isAdmin = \DB::table('user_rolls')
        ->where('user_id', $userKey)
        ->where('item_id', 'Administrator')
        ->where('is_read', 'on')
        ->where('is_update', 'on')
        ->where('is_delete', 'on')
        ->where('is_add', 'on')
        ->exists();
@endphp

 
<script>
    // Normalize to true/false in JS
    window.calendarHasAdminAccess = Boolean(@json($hasAdminAccess ? 1 : 0));
    console.log('calendarHasAdminAccess =', window.calendarHasAdminAccess);
</script>

<script>
/*
  CalendarApp — lean rewrite (same routes/IDs/UI)
  - Single IIFE, minimal globals
  - Centralized fetch + CSRF
  - Deterministic Select2 init/destroy
  - One event mapper reused everywhere
  - Integrated "leave_request" events with modal + approve/reject/not_responsible
*/
(() => {
  "use strict";

  // =========================
  // Config
  // =========================
  const AUTH_EMPLOYEE_ID = String("{{ auth()->user()->name }}"); // name holds employee_id
  const IS_ADMIN = Boolean(window.calendarHasAdminAccess);
  console.log('IS_ADMIN =', IS_ADMIN);
 
  const CSRF = () => $('meta[name="csrf-token"]').attr('content') || '';

  const ROUTE = {
    searchSuggest: "{{ route('calendar.search.suggest') }}",
    getCalendar: "/get_personal_task_calendar",
    duplicateAppointment: "{{ route('appointment.duplicate') }}",
    changeAppointment: "{{ route('personal.task.change.appointment') }}",
    deleteAppointment: id => `${location.origin}/calendar/appointments/destroy/${id}`,
    deleteTask:       id => `${location.origin}/calendar/personal_task_delete/${id}`,
    appointmentDetails: id => `/customer/appointments/${id}`,
    taskDetails:        id => `/personal_task_details/${id}`,
    getCustomerDetails: id => `/get_customer_details/${id}`,
    getEmployees: (q, f) => `/getEmployees?search=${encodeURIComponent(q)}&filter=${encodeURIComponent(f)}`,
    getProblems: cid => `/tickets/${cid}/problems`,
    getTasks:    pid => `/problems/${pid}/tasks`,
    ticketCustomers: "{{ route('ticket.customer.search') }}",
    contactList: "{{ route('get.contact.list') }}",
    productsByCustomer: "{{ route('get.products.by.customer') }}",
    inquiryDeptEmployees: "{{ route('calender.department.employees') }}", // kept for compatibility
    storeMainAppointment: "{{ route('main.appointments.store') }}",
    fetchMainAppointment: id => `/main-appointments/${id}/fetch`,
    updateMainAppointment: id => `/main-appointments/${id}`,
    calendarSettingsGet: "/calendar-settings",
    calendarSettingsSave: "/calendar-settings/save",
  };

  // =========================
  // State
  // =========================
  const S = {
    fc: null,
    mini: null,
    currentSearch: "",
    publicHolidayDates: new Set(),                     // yyyy-mm-dd
    favoriteEmployeeIds: (window.favoriteEmployeeIds || []).map(String),
    selectedEmployeeIds: new Set((window.favoriteEmployeeIds || []).map(String)),
    empAbort: null,
    didAutoselectFavorites: false,
    productMap: {},                                    // uid -> {product_id, alternative_id, product_name, customer_id, city}
  };

  // limited window surface
  window.authEmployeeId = AUTH_EMPLOYEE_ID;
  window.selectedEmployeeIds = S.selectedEmployeeIds;

  // =========================
  // DOM
  // =========================
  const D = {
    cal:  document.querySelector(".calendar"),
    mini: document.getElementById("mini_calendar"),
    newTaskCard: document.querySelector(".new_task"),
  };

  // =========================
  // Utils
  // =========================
  const U = {
    q: (s, ctx=document)=>ctx.querySelector(s),
    qa: (s, ctx=document)=>Array.from(ctx.querySelectorAll(s)),
    pad2: n => String(n).padStart(2,"0"),
    isoDate(d){ return `${d.getFullYear()}-${this.pad2(d.getMonth()+1)}-${this.pad2(d.getDate())}`; },
    isoDT(d){ return `${this.isoDate(d)}T${this.pad2(d.getHours())}:${this.pad2(d.getMinutes())}:00`; },
    shortHM(t){ return (!t || t==="null" || t==="undefined") ? "N/A" : t.split(":").slice(0,2).join(":"); },
    hexRGBA(hex="#006400", a=1){
      hex = hex.replace(/^#/,""); if(hex.length===3) hex = hex.split("").map(c=>c+c).join("");
      const r=parseInt(hex.slice(0,2),16), g=parseInt(hex.slice(2,4),16), b=parseInt(hex.slice(4,6),16);
      return `rgba(${r}, ${g}, ${b}, ${a})`;
    },
    trunc(s,n){ return s && s.length>n ? s.slice(0,n)+"…" : (s||""); },
    weekStart(dateStr, firstDay=1){
      const d=new Date(dateStr), s=new Date(d), dw=d.getDay(); s.setDate(d.getDate()-((dw+7-firstDay)%7)); return U.isoDate(s);
    },
    isMobile: ()=> window.innerWidth<=768,
    makeTicketUrl(problemId, taskId){ if(!problemId) return null; return taskId ? `/problems/${problemId}/tasks/${taskId}` : `/problems/${problemId}`; },
    selectedFromDOM(){
      const checks = U.qa(".employee_check:checked").map(cb=>String(cb.dataset.id));
      const sel    = ($("#employee").val()||[]).map(String);
      return new Set([...checks, ...sel, ...Array.from(S.selectedEmployeeIds)]);
    },
    ensureOption($sel, id, text){
      if ($sel.find(`option[value="${id}"]`).length) return;
      const opt = new Option(text || `#${id}`, id, false, false);
      $sel.append(opt);
    },
    extractHolidayDates(events){
      S.publicHolidayDates = new Set(
        events.filter(e => e.extendedProps?.type === "public_holiday").map(e => (e.start||"").split("T")[0])
      );
    },
    hasHolidayBetween(startStr, endStr){
      if(!startStr || !endStr) return null;
      const s=new Date(startStr), e=new Date(endStr);
      for(let d=new Date(s); d<=e; d.setDate(d.getDate()+1)){
        const iso=U.isoDate(d); if(S.publicHolidayDates.has(iso)) return iso;
      }
      return null;
    },
    // fetch helpers
    async getJSON(url, data){
      const qs = data ? (url.includes('?')?'&':'?') + new URLSearchParams(data) : '';
      const r = await fetch(url+qs, { headers: { Accept:"application/json" } });
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    },
    async postJSON(url, body){
      const r = await fetch(url, {
        method:"POST",
        headers:{ "Content-Type":"application/json", Accept:"application/json", "X-CSRF-TOKEN": CSRF() },
        body: JSON.stringify(body||{}),
      });
      const text = await r.text();
      try { return { ok:r.ok, status:r.status, json: JSON.parse(text) }; }
      catch { return { ok:r.ok, status:r.status, json: { raw:text } }; }
    },
    async send(method, url, formData){
      const r = await fetch(url, { method, headers: { "X-CSRF-TOKEN": CSRF() }, body: formData });
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    }
  };

  // =========================
  // Select2 lifecycle
  // =========================
  function initSelect2Singleton($el, options){
    if (!$el?.length) return;
    if ($el.data('select2')) { $el.off(); $el.select2('destroy'); }
    $el.select2(Object.assign({ width:'100%', allowClear:true, placeholder:'Bitte wählen' }, options||{}));
  }

  // =========================
  // Tickets module
  // =========================
  const TICKETS = {
    svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M3 7a2 2 0 0 1 2-2h5l1 2h3a2 2 0 1 0 0 4h-3l-1 2H5a2 2 0 0 1-2-2V7zM14 5h5a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2h-5l-2-4 2-4z"/></svg>`,
    setHidden({ problemId=null, problemTaskId=null, mode="new", autoCreate=0 }={}){
      $("#problem_id").val(problemId||"");
      $("#problem_task_id").val(problemTaskId||"");
      $("#ticket_mode").val(mode||"new");
      $("#ticket_auto_create").val(String(autoCreate||0));
    },
    currentMode(){
      if ($("#ticketMode").is(":checked")) return "ticket";
      const explicit = $("#contact_mode").val();
      return explicit || "new";
    },
    async loadProblems(customerId, preselectId=null){
      const list = await U.getJSON(ROUTE.getProblems(customerId));
      const $sel = $("#ticket_problem_id"); $sel.empty();
      (list||[]).forEach(p=>$sel.append(new Option(p.text||p.title||`#${p.id}`, p.id)));
      if (preselectId) $sel.val(String(preselectId)).trigger("change");
    },
    async loadTasks(problemId, preselectId=null){
      const list = await U.getJSON(ROUTE.getTasks(problemId));
      const $sel = $("#ticket_task_id"); $sel.empty();
      (list||[]).forEach(t=>$sel.append(new Option(t.text||t.title||`#${t.id}`, t.id)));
      if (preselectId) $sel.val(String(preselectId)).trigger("change");
    },
    initSelects(){
      const $cust = $("#ticket_customer_id");
      if ($cust.length && !$cust.data("select2")) {
        $cust.select2({
          placeholder:"Kunde wählen…", allowClear:true,
          ajax:{
            url: ROUTE.ticketCustomers, dataType:"json", delay:200,
            data:(p)=>({ q:p.term||"" }),
            processResults:(data)=>({ results:(data.results||data||[]).map(x=>({ id:x.id, text:x.text || `${x.name} ${x.lastname}` })) })
          }
        }).on("select2:select", async (e)=>{
          const id = e.params.data.id;
          $("#ticket_problem_id, #ticket_task_id").val(null).trigger("change");
          await TICKETS.loadProblems(id);
        }).on("select2:clear", ()=>{
          $("#ticket_problem_id, #ticket_task_id").val(null).trigger("change");
        });
      }
      if ($("#ticket_problem_id").length && !$("#ticket_problem_id").data("select2")) {
        $("#ticket_problem_id").select2({ placeholder:"Ticket/Problem wählen…", allowClear:true })
          .on("change", function(){ const pid=$(this).val(); $("#ticket_task_id").val(null).trigger("change"); if(pid) TICKETS.loadTasks(pid); });
      }
      if ($("#ticket_task_id").length && !$("#ticket_task_id").data("select2")) {
        $("#ticket_task_id").select2({ placeholder:"Ticket-Task wählen…", allowClear:true });
      }
      $("#ticket_create_task").off("change").on("change", function(){ $("#ticket_auto_create").val(this.checked?"1":"0"); });
    }
  };

  // =========================
  // Event mapping
  // =========================
 /**
 * Fully rewritten mapper to handle cancellations and modified recurring leaves.
 */
function mapServerItemToEvents(item) {
  const out = [];
  
  // Standardize dates
  const startDT = new Date(`${item.start_date}T${item.start_time}`);
  const endDT = new Date(`${item.end_date || item.start_date}T${item.end_time}`);

  // Loop through days (for multi-day spans)
  for (let d = new Date(startDT); d <= endDT; d.setDate(d.getDate() + 1)) {
    const dateStr = U.isoDate(d);
    
    // Determine the specific times for this day of the event
    const sTime = dateStr === item.start_date ? item.start_time : "07:30:00";
    const eTime = (dateStr === (item.end_date || item.start_date)) ? item.end_time : "16:00:00";
    
    const endObj = new Date(`${dateStr}T${eTime}`);
    
    // FullCalendar fix for exact hour/half-hour markers
    if (endObj.getSeconds() === 0 && endObj.getMilliseconds() === 0 && (endObj.getMinutes() === 0 || endObj.getMinutes() === 30)) {
      endObj.setMinutes(endObj.getMinutes() + 1);
    }

    // --- CANCELLATION LOGIC ---
    // Check if the backend flagged this specific occurrence as cancelled
    const isCancelled = item.is_cancelled === true || item.status === 'cancelled';
    
    // If it's cancelled, we can either:
    // 1. Return empty (hides it from calendar)
    // 2. Push it with a strikethrough title (shows it was removed)
    // Here we push it so the user sees the "Cancelled" status you wanted.
    
    let displayTitle = item.title || "-";
    if (isCancelled && !displayTitle.includes('(ABGESAGT)')) {
        displayTitle = `🚫 ${displayTitle} (ABGESAGT)`;
    }

    out.push({
      id: `${item.id}-${dateStr}-${sTime}`,
      title: displayTitle,
      start: `${dateStr}T${sTime}`,
      end: U.isoDT(endObj),
      // If cancelled, force a soft grey or red color override
      color: isCancelled ? "#ffcccc" : (item.taskColor || "#cccccc"),
      textColor: isCancelled ? "#cc0000" : null,
      allDay: ["public_holiday", "holiday", "sick", "recurring_leave", "leave_request"].includes(item.type),
      
      extendedProps: {
        // --- Added Debugging Props ---
        is_cancelled: isCancelled,
        status: item.status || (isCancelled ? 'cancelled' : 'normal'),
        
        // --- Existing Props ---
        employees: item.employees || [],
        priority: item.priority || "-",
        public: item.public_view || "-",
        report: item.is_report || "-",
        type: item.type || "-",
        start_time: sTime,
        end_time: eTime,
        city: item.city || "-",
        phone: item.phone || "-",
        email: item.email || "-",
        full_address: item.full_address || [item.street, item.postcode, item.city].filter(Boolean).join(" ") || "-",
        appointment_type: item.appointment_type || "-",
        description: item.description || "-",
        customer_id: item.customer_id ?? null,
        contact_id: item.contact_id ?? null,
        next_step: item.next_step || null,
        responsible_report: item.responsible_report || [],
        has_ticket: !!item.has_ticket,
        ticket_problem_id: item.ticket_problem_id || null,
        ticket_task_id: item.ticket_task_id || null,
        emp_personal_id: item.emp_personal_id || null,
        
        // leave specific
        leave_type: item.leave_type ?? null,
        leave_reason: item.leave_reason ?? null,
        leave_status: item.leave_status ?? null,
        products: item.product_json ?? item.products ?? null,
      }
    });
  }
  return out;
}
  // =========================
  // Calendar UI
  // =========================
  async function loadCalendarTasks(cb){
    const employeeData = getSelectedEmployeeData();
    try{
      const res = await U.getJSON(ROUTE.getCalendar, { employee_data: JSON.stringify(employeeData), search: S.currentSearch||"" });
      const rows = Array.isArray(res?.data) ? res.data : [];
      const events = rows.flatMap(mapServerItemToEvents);
      U.extractHolidayDates(events);
      initializeCalendar(events);
      initializeMiniCalendar(events);
      cb && cb(events);
    }catch(err){
      console.error("loadCalendarTasks:", err);
      initializeCalendar([]);
      initializeMiniCalendar([]);
      cb && cb([]);
    }
  }

  function mountCalendarSearch(){
    if (document.getElementById("calendarSearch")) {
      const stray = document.querySelector(".fc-searchBox-button"); if (stray) stray.remove(); return;
    }
    const btn = document.querySelector(".fc-searchBox-button"); if (!btn) return;
    const wrap = document.createElement("div");
    wrap.style.display="inline-block"; wrap.style.minWidth="280px";
    wrap.innerHTML = `<select id="calendarSearch" style="width:280px"></select>`;
    btn.replaceWith(wrap);

    const $sel = $("#calendarSearch"); if ($sel.data("bound")) return;
    $sel.data("bound", true).select2({
      placeholder:"Suchen… (Termin, Aufgabe, Mitarbeiter, Stadt)",
      minimumInputLength:1, allowClear:true, width:"style",
      ajax:{
        url: ROUTE.searchSuggest, dataType:"json", delay:200,
        data:(p)=>({ q:p.term }),
        processResults:(data)=>({ results:(data.results||[]).map(i=>({ id:i.id, text:i.label, type:i.type, date:i.date, image:i.image||null })) })
      },
      templateResult(item){
        if (!item.id) return item.text;
        const pill = { appointment:"Termin", task:"Aufgabe", employee:"Mitarbeiter", city:"Ort" }[item.type] || item.type;
        const d = item.date ? ` <small style="opacity:.7">(${item.date})</small>` : "";
        return $(`<div><strong>${item.text}</strong> <span class="badge badge-light">${pill}</span>${d}</div>`);
      },
      templateSelection:(i)=> i.text || i.id,
    }).on("select2:select", (e)=>{
      const sel = e.params.data;
      S.currentSearch = sel.text || "";
      reloadCalendarWithSearch(async ()=>{
        if (sel.date) S.fc.gotoDate(sel.date);
        if (sel.type === "employee") {
          const id = String(sel.id);
          const cb = document.querySelector(`.employee_check[data-id="${id}"]`);
          if (cb) {
            if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event("change", { bubbles:true })); }
          } else {
            S.selectedEmployeeIds.add(id);
            U.ensureOption($("#employee"), id, sel.text);
            $("#employee").val(Array.from(S.selectedEmployeeIds)).trigger("change");
            const $m = $("#mobileEmployeeSelect");
            if ($m.length) {
              const vals = ($m.val()||[]).map(String);
              if (!vals.includes(id)) $m.val([...vals, id]).trigger("change.select2");
            }
            loadCalendarTasks();
          }
        }
      });
    }).on("select2:clear", ()=>{
      S.currentSearch = "";
      reloadCalendarWithSearch();
    });
  }

  function initializeCalendar(events){
    if (S.fc) {
      S.fc.getEventSources().forEach(s=>s.remove());
      S.fc.addEventSource(events);
      S.fc.refetchEvents();
      return;
    }
    S.fc = new FullCalendar.Calendar(D.cal, {
      initialView: U.isMobile()? "listWeek":"timeGridWeek",
      locale:"de",
      firstDay:1,
      weekNumbers:true,
      weekNumberCalculation:"ISO",
      allDaySlot:true,
      allDayText:"Ganztägig",
      dayHeaderFormat:{ weekday:"short", day:"numeric" },
      eventDisplay:"block",
      slotMinTime:"05:00:00",
      slotMaxTime:"23:59:59",
      slotDuration:"00:30:00",
      slotLabelInterval:"01:00:00",
      nowIndicator:true,
      displayEventTime:true,
      eventTimeFormat:{ hour:"2-digit", minute:"2-digit", hour12:false },
      height:"auto",
      expandRows:true,
      dayMaxEvents: 6,
      dayMaxEventRows: 6,
      slotLabelFormat:{ hour:"2-digit", minute:"2-digit", omitZeroMinute:false, meridiem:false },
      headerToolbar:{
        left:"prev,next today toggleSlider verfgBtn searchBox",
        center:"title",
        right:"year,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
      },
      views: {
        year: {
          type: "dayGridMonth",
          duration: { months: 12 },
          buttonText: "Jahr",
        },
      },

      editable:true,
      eventResizableFromStart:true,
      events,
      customButtons:{
        toggleSlider:{ text:"⇔", click(){
          const $slider=$("#slider_section"), $cal=$(".calender_section");
          const hidden = $slider.hasClass("d-none");
          if (hidden){ $slider.removeClass("d-none"); $cal.removeClass("col-md-12").addClass("col-md-9"); setTimeout(()=>S.mini && S.mini.render(), 10); }
          else { $slider.addClass("d-none"); $cal.removeClass("col-md-9").addClass("col-md-12"); }
          S.fc.updateSize();
        }},
        verfgBtn:{ text:"Verfügbarkeit", click:()=> (window.location.href="/employee-availability") },
        searchBox:{ text:"Suche", click(){} },
      },
      buttonText:{ today:"Heute", year:"Jahr", month:"Monat", week:"Woche", day:"Tag", list:"Übersicht" },
      windowResize(){
        if (U.isMobile() && S.fc.view.type!=="listWeek") S.fc.changeView("listWeek");
        else if (!U.isMobile() && S.fc.view.type!=="timeGridWeek") S.fc.changeView("timeGridWeek");
      },
      dayCellDidMount(info){
        const ds = info.date.toISOString().split("T")[0];
        if (S.publicHolidayDates.has(ds)) {
          info.el.style.backgroundColor="#f8f9fa";
          info.el.classList.add("public-holiday-cell");
          const badge=document.createElement("div"); badge.innerText="🇩🇪";
          Object.assign(badge.style,{position:"absolute",top:"2px",right:"2px"});
          info.el.appendChild(badge);
        }
      },
     dateClick(info){
        // 1. Fully reset the form and hidden ID before showing it
        document.getElementById("task-store-form").reset();
        $("#appointment_id").val("");
        $(".title").text("TERMIN ERSTELLEN"); 
        
        // 2. Clear all Select2 dropdowns
        $("#customer_id, #ticket_customer_id, #ticket_problem_id, #ticket_task_id, #productSelect, #employee, #report_responsible").val(null).trigger("change");

        // 3. Set the clicked dates
        const date = info.dateStr.split("T")[0];
        const time = info.dateStr.includes("T") ? info.dateStr.split("T")[1].slice(0,5) : "00:00";
        $("#start_date").val(date); 
        $("#end_date").val(date); 
        $("#start_time").val(time);
        
        // 4. Show the modal
        if (D.newTaskCard) D.newTaskCard.style.display="block";
      },
      moreLinkClick(info){
        const d=info.date, list=S.fc.getEvents().filter(ev=>ev.start.toDateString()===d.toDateString());
        showDayEventsModal(list, d); return false;
      },
      eventClick(info){
        const t = info.event.extendedProps.type;
        if (["holiday","sick","recurring_leave"].includes(t)) return;
        showEventDetailsModal(info.event);
      },
      eventDidMount: decorateEventEl,
      eventDrop: handleEventUpdate,
      eventResize: handleEventUpdate,
    });

    // anchor to ?task_id=
    const taskId = new URLSearchParams(location.search).get("task_id");
    if (taskId){
      const ev = events.find(e=> e.id.split("-")[0]===taskId);
      if (ev) S.fc.gotoDate(ev.start);
    }

    S.fc.render();
    mountCalendarSearch();
    S.fc.on("datesSet", mountCalendarSearch);
  }

  function initializeMiniCalendar(events){
    if (S.mini) S.mini.destroy();
    let lastClick = 0;
    S.mini = new FullCalendar.Calendar(D.mini, {
      initialView:"dayGridMonth", locale:"de", selectable:true,
      headerToolbar:{ left:"title", center:"", right:"prev,next" },
      events,
      dateClick(info){
        const now=Date.now(), delta=now-lastClick;
        if (delta<300){ S.fc.changeView("timeGridDay"); filterMainCalendarByDate(info.dateStr); }
        else { S.fc.changeView("timeGridWeek"); filterMainCalendarByDate(U.weekStart(info.dateStr, S.fc.getOption("firstDay")||1)); }
        lastClick = now;
      }
    });
    S.mini.render();
    setTimeout(()=> $(".fc-toggleSlider-button").html("<i class='feather icon-sidebar'></i>"),0);
  }

  function reloadCalendarWithSearch(done){
    const view = S.fc.view.type, date=S.fc.getDate();
    loadCalendarTasks(()=>{ S.fc.changeView(view); S.fc.gotoDate(date); if (typeof done==="function") done(); });
  }

  // =========================
  // Event rendering & modals
  // =========================
  function decorateEventEl(info){
    const ev=info.event, el=info.el, xp=ev.extendedProps||{}, { type, employees, priority, public:isPublic, start_time, end_time } = xp;
    const taskIdFromUrl = new URLSearchParams(location.search).get("task_id");
    if (taskIdFromUrl && ev.id.split("-")[0]===taskIdFromUrl) {
      el.classList.add("edited-event");
      el.scrollIntoView({behavior:"smooth", block:"center"});
      setTimeout(()=>el.classList.remove("edited-event"), 3000);
    }

      if (type === "recurring_leave") {
          
          // 1. Check if the event is cancelled
          const isCancelled = ev.extendedProps.is_cancelled === true;
          
          // 2. Define Styles based on status
          let bgColor = "#74b2d4"; // Default Blue
          let textColor = "#000";
          let textDecoration = "none";
          let opacity = "1";
          let label = "Wiederkehrender Urlaub";

          if (isCancelled) {
              bgColor = "#ffcccc"; // Light Red background
              textColor = "#cc0000"; // Red text
              textDecoration = "line-through"; // Cross out text
              opacity = "0.7";
              label = "Abgesagt (Storniert)"; // Change label
          }

          Object.assign(el.style, {
              pointerEvents: isCancelled ? "none" : "auto", // Optional: clickable or not
              background: bgColor,
              color: textColor,
              border: "1px solid #6c757d",
              padding: "4px",
              fontSize: "11px",
              borderRadius: "6px",
              textDecoration: textDecoration,
              opacity: opacity
          });

          // Get Employee Name
          const empName = (employees && employees.length > 0) 
              ? employees[0].name + " " + employees[0].lastname 
              : "";

          el.innerHTML = `
            <div style="display:flex;gap:4px;align-items:center;">
                <i class="feather icon-repeat"></i>
                <b>${ev.title}</b>
            </div>
            <div style="font-weight:bold; font-size:11px; margin-top:2px;">${empName}</div>
            <div style="font-size:10px; opacity:.9; font-style:italic;">${label}</div>
          `;
          return;
      }

    if (type === "leave_request") {
      Object.assign(el.style, {
        background: "linear-gradient(135deg,#8fc73e,#cdeac0)",
        color: "#1b4332",
        border: "1px solid #6c757d",
        padding: "4px",
        fontSize: "11px",
        borderRadius: "6px",
      });

      const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
            viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            style="margin-right:4px;">
          <path d="M3 18a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4" />
          <path d="M21 8a3 3 0 0 0-6 0v4" />
          <line x1="6" y1="12" x2="6" y2="22" />
        </svg>`;

      const requester = (xp.employees && xp.employees.length)
        ? `${xp.employees[0].name} ${xp.employees[0].lastname}`
        : "";
      const startLabel = ev.start
        ? new Date(ev.start).toLocaleDateString("de-DE")
        : "";
      const endLabel = ev.end
        ? new Date(ev.end).toLocaleDateString("de-DE")
        : startLabel;
      const range = startLabel && endLabel
        ? (startLabel === endLabel ? startLabel : `${startLabel} – ${endLabel}`)
        : "";

      el.innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:4px;">
          ${svg}
          <div>
            <div style="font-weight:600;">Urlaubsantrag${requester ? " – " + requester : ""}</div>
            ${range ? `<div style="font-size:10px;opacity:.8;">${range}</div>` : ""}
          </div>
        </div>
      `;
      return;
    }

    if (["public_holiday","holiday","sick"].includes(type)){
      const names=(employees||[]).map(e=>`${e.name} ${e.lastname}`).join(", ");
      Object.assign(el.style,{ pointerEvents:"none", backgroundColor:"#999", border:"none", color:"#fff", padding:"3px 6px", fontSize:"11px", borderRadius:"4px" });
      el.innerHTML = `<div style="font-size:11px;font-weight:bold;">${ev.title}</div>${names?`<div style="font-size:10px;opacity:.8;">${names}</div>`:""}`;
      return;
    }

    if (window.innerWidth<=500 && S.fc.view.type==="timeGridWeek"){
      const bg = ev.backgroundColor || ev.extendedProps?.color || "#006400";
      el.setAttribute("style",`background-color:${bg}!important;color:#fff!important;border-left:4px solid ${bg}!important;border-radius:6px!important;padding:5px!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;font-size:11px!important;text-align:left!important;max-width:100px!important;`);
      el.innerHTML = `<div><strong>${U.trunc(ev.title,20)}</strong></div><div style="font-size:10px;">${U.shortHM(start_time)} - ${U.shortHM(end_time)}</div>`;
      return;
    }

    el.classList.add("fc-daygrid-dot-event","fc-event");
    el.innerHTML = "";
    const bg = ev.backgroundColor || ev.extendedProps?.color || "#006400";
    el.setAttribute("style", `white-space:normal!important;border:0!important;border-left:5px solid ${bg}!important;background-color:${U.hexRGBA(bg, .4)}!important;`);

    const { has_ticket, ticket_problem_id, ticket_task_id } = xp;
    const ticketUrl = has_ticket ? U.makeTicketUrl(ticket_problem_id, ticket_task_id) : null;
    const ticketBtn = ticketUrl ? `<a href="${ticketUrl}" class="fc-ticket-link" title="Ticket öffnen" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;margin-left:6px;text-decoration:none;color:#444">${TICKETS.svg}</a>` : "";

    const names=(employees||[]).map(e=>`${e.name} ${e.lastname}`).join(", ");
    el.innerHTML = `
      <div class="custom-event">
        <div class="custom-event-header d-flex align-items-center" id="calendar_icons">
          <i class="fa ${isPublic!=="1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
          <i class="fa ${priority==="very high" ? "fa-fire warning mr-1" : (priority==="high" ? "fa-bell important mr-1" : "")}"></i>
          <p class="p-0 m-0" id="calendar_times" style="font-size:10px;color:${type==="task" ? "#74b2d4" : "#4c4c4c"};">${U.shortHM(start_time)} - ${U.shortHM(end_time)}</p>
          ${ticketBtn}
        </div>
        <div class="custom-event-title m-0">
          <p style="font-size:10px;margin:0;color:${type==="task" ? "#74b2d4" : "#4c4c4c"};font-weight:bold;">${U.trunc(ev.title,20)}</p>
          <p style="font-size:8px;color:${type==="task" ? "#74b2d4" : "#4c4c4c"};">${names}</p>
        </div>
      </div>`;
    if (ticketUrl) el.querySelector(".fc-ticket-link")?.addEventListener("click", (evt)=> evt.stopPropagation());
  }


  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }


  async function fetchSelectedProductsForEvent(cleanId, xp) {
    let products = extractCustomerProductsFromEvent(xp);

    if (products.length) {
      return products;
    }

    try {
      const data = await U.getJSON(ROUTE.fetchMainAppointment(cleanId));
      const fromDetail = {
        products: data.product_json ?? data.products ?? null
      };
      return extractCustomerProductsFromEvent(fromDetail);
    } catch (e) {
      console.error('Could not fetch selected products for event:', e);
      return [];
    }
  }

  function extractCustomerProductsFromEvent(xp) {
  let raw = xp.products ?? xp.product_json ?? xp.products_json ?? null;

  try {
    if (typeof raw === 'string') raw = JSON.parse(raw);
    if (typeof raw === 'string') raw = JSON.parse(raw);
  } catch (e) {
    raw = null;
  }

  if (!raw) return [];

  if (Array.isArray(raw)) {
    return raw.map(item => ({
      uid: item.uid || `${item.name || item.product_name || 'Produkt'}_${item.alternative_id || ''}`,
      name: item.product_name || item.name || item.text || 'Produkt',
      alternative_id: item.alternative_id || item.alt_id || null,
      product_id: item.product_id || null,
      customer_id: item.customer_id || null,
      city: item.city || null
    }));
  }

  if (typeof raw === 'object') {
    return Object.entries(raw).map(([name, tuple]) => ({
      uid: `${name}_${Array.isArray(tuple) ? tuple[0] : ''}`,
      name: name || 'Produkt',
      alternative_id: Array.isArray(tuple) ? tuple[0] : null,
      product_id: Array.isArray(tuple) ? tuple[1] : null,
        customer_id: Array.isArray(tuple) ? tuple[2] : null,
        city: null
      }));
    }

    return [];
  }
  function renderInlineCustomerProducts(products) {
  if (!products || !products.length) {
    return `<div class="customer-products-empty">Keine gespeicherten Produkte gefunden.</div>`;
  }

  const seen = new Set();
  const uniqueProducts = products.filter(product => {
    const key = product.uid || `${product.name}_${product.alternative_id}_${product.product_id}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });

  const chips = uniqueProducts.slice(0, 6).map(product => `
    <span class="customer-products-chip">
      <svg viewBox="0 0 24 24" width="12" height="12" fill="none">
        <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
      </svg>
      ${escapeHtml(product.name)}
    </span>
    `).join('');

    const extra = uniqueProducts.length > 6
      ? `<span class="customer-products-chip">+${uniqueProducts.length - 6} weitere</span>`
      : '';

    return `<div class="customer-products-summary">${chips}${extra}</div>`;
  }

  function renderCustomerProductsPopup(products, customerName) {
    if (!products || !products.length) {
      return `
        <div class="swal-products-list">
          <div class="customer-products-empty">Für ${escapeHtml(customerName)} wurden keine Produkte gefunden.</div>
        </div>
      `;
    }

    return `
      <div class="swal-products-list">
        ${products.map(product => `
          <div class="swal-product-card">
            <div class="swal-product-icon">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
                <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M3 16.5L12 21l9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="swal-product-content">
              <div class="swal-product-name">${escapeHtml(product.name)}</div>
              <div class="swal-product-meta">
                ${product.product_id ? `<span class="swal-product-pill">Produkt-ID: ${escapeHtml(product.product_id)}</span>` : ''}
                ${product.alternative_id ? `<span class="swal-product-pill">Alternative-ID: ${escapeHtml(product.alternative_id)}</span>` : ''}
                ${product.city ? `<span class="swal-product-pill">Ort: ${escapeHtml(product.city)}</span>` : ''}
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
  }

  function showCustomerProductsPopup(customerName, products = []) {
    Swal.fire({
      title: `Ausgewählte Produkte von ${escapeHtml(customerName || 'Kunde')}`,
      html: renderCustomerProductsPopup(products, customerName || 'Kunde'),
      width: 760,
      confirmButtonText: 'Schließen',
      customClass: {
        popup: 'custom-swal-popup',
        confirmButton: 'custom-confirm-btn'
      }
    });
  }
  function loadCustomerProductsPreview(products = []) {
    const previewEl = document.getElementById('customerProductsPreview');
    if (!previewEl) return;
    previewEl.innerHTML = renderInlineCustomerProducts(products);
  }


  function showEventDetailsModal(event){
    const xp = event.extendedProps || {};
    const cleanId = String(event.id || "").split("-")[0];
    const type = xp.type;

    if (type === "leave_request") {
      showLeaveRequestModal(event);
      return;
    }

    if (type === "holiday" || type === "sick") return;

    const detailUrl = type === "appointment"
      ? ROUTE.appointmentDetails(cleanId)
      : ROUTE.taskDetails(cleanId);

    const ticketUrl = xp.has_ticket ? U.makeTicketUrl(xp.ticket_problem_id, xp.ticket_task_id) : null;
    const ticketAnchor = ticketUrl
      ? `<a href="${ticketUrl}" target="_blank" rel="noopener" title="Ticket öffnen" style="margin-left:8px;display:inline-flex;align-items:center;color:#fff">${TICKETS.svg}</a>`
      : "";

    const hasCustomer = !!xp.customer_id && xp.customer_id !== "Null" && xp.customer_id !== "-";
    const hasContact  = !!xp.contact_id && xp.contact_id !== "Null" && xp.contact_id !== "-";
    const customerLink = hasCustomer ? `/new_lead_profile/${xp.customer_id}` : (hasContact ? `/inquiry_show/${xp.contact_id}` : "#");
    const customerIcon = hasCustomer || hasContact ? '<i class="feather icon-users white"></i>' : '<i class="feather icon-user-x white"></i>';
    const priorityIcon = xp.priority === "very high"
      ? '<i class="fa fa-fire warning mr-1"></i>'
      : (xp.priority === "high" ? '<i class="fa fa-bell important mr-1"></i>' : "");
    const reportIcon = xp.report === "1" ? '<i class="feather icon-file-text warning mr-1"></i>' : "";
    const typeIcon = type === "appointment" ? '<i class="feather icon-calendar"></i>' : '<i class="fa fa-tasks"></i>';
    const displayAddress = xp.full_address && xp.full_address !== "-" && xp.full_address !== "null" ? xp.full_address : "-";

    const employeeList = (xp.employees || []).map(e => `
      <li data-toggle="tooltip" title="${escapeHtml((e.name || '') + ' ' + (e.lastname || ''))}">
        <img src="/images/employee/${e.image || 'default-avatar.png'}" alt="Avatar" height="30" width="30" class="rounded-circle">
      </li>
    `).join("");

    const actionMenu = `
      <div class="dropdown" style="position:absolute; top:112px;">
        <button class="btn btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="feather icon-more-vertical"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item duplicate-event" data-event-id="${cleanId}"><i class="feather icon-copy"></i> Duplizieren</a>
          <a class="dropdown-item edit-event" data-event-id="${cleanId}"><i class="feather icon-edit"></i> Bearbeiten</a>
          <a class="dropdown-item text-danger" href="#" id="delete_event" data-event-type="${type}" data-event-id="${cleanId}"><i class="feather icon-trash"></i> Löschen</a>
        </div>
      </div>`;

    let fallbackProducts = extractCustomerProductsFromEvent(xp);
    const customerDisplayName = `${xp.customerName || ''} ${xp.customerLastname || ''}`.trim() || event.title || 'Kunde';

    const html = `
      <div class="custom-event">
        <div class="custom-event-header d-flex align-items-center">
          <i class="fa ${xp.public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
          ${priorityIcon}
          ${reportIcon}
          ${ticketAnchor}
          <span class="custom-event-status-text">
            ${typeIcon}
            <i class="feather icon-info warning info_popup" data-id="${cleanId}" data-type="${type}"></i>
            ${type === "appointment" ? `<i class="feather icon-map show_map" data-id="${cleanId}"></i>` : ""}
            <span class="calendar_menu">${actionMenu}</span>
            <a href="${customerLink}" target="_blank" style="margin-left:8px;">${customerIcon}</a>
          </span>
        </div>

        <div class="custom-event-title mt-1">
          <a href="${detailUrl}" style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
            ${escapeHtml(xp.description || event.title)}
          </a>
          ${xp.appointment_type && xp.appointment_type !== "-" ? `<p style="font-size:12px;color:#fff;"><strong>Typ:</strong> ${escapeHtml(xp.appointment_type)}</p>` : ""}
          <p style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
            <i class="feather icon-calendar"></i> ${new Date(event.end || event.start).toLocaleDateString("de-DE",{day:"numeric",month:"short",year:"numeric"})}
          </p>
          <p style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
            <i class="feather icon-clock"></i> ${U.shortHM(xp.start_time)} - ${U.shortHM(xp.end_time)}
          </p>
        </div>

        <div class="mt-2">
          ${xp.phone && xp.phone !== "-" ? `<p style="font-size:13px;"><i class="feather icon-phone"></i> ${escapeHtml(xp.phone)}</p>` : ""}
          ${xp.email && xp.email !== "-" ? `<p style="font-size:13px;"><i class="feather icon-mail"></i> ${escapeHtml(xp.email)}</p>` : ""}
          ${displayAddress && displayAddress !== "-" ? `<p style="font-size:13px;"><i class="feather icon-map-pin"></i> ${escapeHtml(displayAddress)}</p>` : ""}
        </div>

        <ul class="list-unstyled users-list m-0 d-flex align-items-center mt-3">
          ${employeeList}
        </ul>
 

        ${hasCustomer ? `
          <div class="customer-products-box">
            <div class="customer-products-head">
              <div class="customer-products-title">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                  <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                  <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                  <path d="M3 16.5L12 21l9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Kundenprodukte
              </div>

              <button
                type="button"
                class="customer-products-badge"
                id="openCustomerProductsBtn"
                data-customer-id="${escapeHtml(xp.customer_id)}"
                data-customer-name="${escapeHtml(customerDisplayName)}"
              >
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none">
                  <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                  <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Alle Produkte anzeigen
              </button>
            </div>

            <div id="customerProductsPreview">
              ${renderInlineCustomerProducts(fallbackProducts)}
            </div>
          </div>
        ` : ""}
      </div>`;

    Swal.fire({
      title: event.title,
      html,
      showCloseButton: true,
      confirmButtonText: "abbrechen",
      cancelButtonText: "weitere Details anzeigen",
      showCancelButton: true,
      confirmButtonColor: "#d92127",
      cancelButtonColor: "#93c21c",
      customClass: {
        popup: "custom-swal-popup",
        confirmButton: "custom-confirm-btn",
        cancelButton: "custom-cancel-btn"
      },
      didOpen: async () => {
        $('[data-toggle="tooltip"]').tooltip();
        document.querySelector(".swal2-popup").style.background = "#2c3e50";

        if (hasCustomer) {
          fallbackProducts = await fetchSelectedProductsForEvent(cleanId, xp);

          loadCustomerProductsPreview(fallbackProducts);

          const btn = document.getElementById('openCustomerProductsBtn');
          if (btn) {
            btn.addEventListener('click', function() {
              showCustomerProductsPopup(
                this.getAttribute('data-customer-name'),
                fallbackProducts
              );
            });
          }
        }
      }
    }).then(res => {
      if (res.dismiss === Swal.DismissReason.cancel) {
        window.location.href = detailUrl;
      }
    });
  }
  async function handleLeaveActionFromCalendar(id, action) {
    // reject needs reason textarea
    if (action === 'reject') {
      const res = await Swal.fire({
        title: 'Urlaub ablehnen',
        html: '<textarea id="leave_reject_reason" class="swal2-textarea" placeholder="Bitte Grund angeben"></textarea>',
        showCancelButton: true,
        confirmButtonText: 'Ablehnen',
        cancelButtonText: 'Abbrechen',
        preConfirm: () => {
          const txt = document.getElementById('leave_reject_reason').value.trim();
          if (!txt) {
            Swal.showValidationMessage('Bitte einen Grund eingeben.');
            return false;
          }
          return txt;
        }
      });

      if (!res.isConfirmed) return;

      const noteText = res.value;

      const { ok, json } = await U.postJSON('/my/mark-done', {
        id,
        type: 'leave',
        action: 'reject',
        note_text: noteText,
      });

      if (!ok || json?.success === false) {
        throw new Error(json?.message || 'Fehler beim Ablehnen des Urlaubs.');
      }

      Swal.fire('Abgelehnt', 'Der Urlaubsantrag wurde abgelehnt.', 'success');
      const view = S.fc.view.type;
      const date = S.fc.getDate();
      await loadCalendarTasks(() => {
        S.fc.changeView(view);
        S.fc.gotoDate(date);
      });
      return;
    }

    // approve / not_responsible
    const { ok, json } = await U.postJSON('/my/mark-done', {
      id,
      type: 'leave',
      action,
    });

    if (!ok || json?.success === false) {
      throw new Error(json?.message || 'Fehler beim Aktualisieren des Urlaubs.');
    }

    let msg = 'Urlaubsantrag aktualisiert.';
    if (action === 'approve') msg = 'Urlaub genehmigt.';
    if (action === 'not_responsible') msg = 'Antrag aus deiner Zuständigkeit entfernt.';

    Swal.fire('Erfolg', msg, 'success');

    const view = S.fc.view.type;
    const date = S.fc.getDate();
    await loadCalendarTasks(() => {
      S.fc.changeView(view);
      S.fc.gotoDate(date);
    });
  }

  function showLeaveRequestModal(event) {
    const xp = event.extendedProps || {};
    const cleanId = String(event.id || '').split('-')[0];

    const employees = xp.employees || [];
    const empLabel = employees.length
      ? `${employees[0].name || ''} ${employees[0].lastname || ''}`.trim()
      : 'Mitarbeiter';

    const leaveType = xp.leave_type || 'Urlaub';
    const reason    = xp.description || xp.leave_reason || '-';
    const isAdmin   = IS_ADMIN;

    const start = event.start ? new Date(event.start) : null;
    const end   = event.end   ? new Date(event.end)   : start;

    const dateRange = start
      ? `${start.toLocaleDateString('de-DE', { day:'2-digit', month:'2-digit', year:'numeric' })}${
          end
            ? ' – ' + end.toLocaleDateString('de-DE', { day:'2-digit', month:'2-digit', year:'numeric' })
            : ''
        }`
      : '-';

    const svgIcon = `
      <svg width="40" height="40" viewBox="0 0 24 24" aria-hidden="true">
        <path fill="#8fc73e" d="M12 2C8 2 4.7 4.7 3.6 8.4c-.2.6.2 1.1.8 1.1H11v11a1 1 0 0 0 2 0V9.5h6.6c.6 0 1-.5.8-1.1C19.3 4.7 16 2 12 2z"/>
        <path fill="#4b5563" d="M10 22h4v1a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1z"/>
      </svg>
    `;

    const adminButtonsHtml = isAdmin
      ? `
        <div class="mt-3" style="display:flex; justify-content:flex-end; gap:0.5rem; flex-wrap:wrap;">
          <button type="button" class="swal2-confirm swal2-styled"
                  data-leave-action="approve"
                  style="background:#10b981;border:none;">
            Genehmigen
          </button>
          <button type="button" class="swal2-confirm swal2-styled"
                  data-leave-action="reject"
                  style="background:#f97373;border:none;">
            Ablehnen
          </button>
          <button type="button" class="swal2-confirm swal2-styled"
                  data-leave-action="not_responsible"
                  style="background:#6b7280;border:none;">
            Nicht zuständig
          </button>
        </div>
      `
      : '';

    const html = `
      <div style="display:flex; align-items:flex-start; gap:1rem;">
        <div>${svgIcon}</div>
        <div style="flex:1; min-width:0;">
          <div style="font-size:14px; font-weight:bold; margin-bottom:0.25rem;">
            ${leaveType} – ${empLabel}
          </div>
          <div style="font-size:13px; margin-bottom:0.25rem;">
            <i class="feather icon-calendar"></i>
            <strong> Zeitraum:</strong> ${dateRange}
          </div>
          <div style="font-size:13px; margin-bottom:0.25rem;">
            <i class="feather icon-info"></i>
            <strong> Grund:</strong> ${reason || '-'}
          </div>
          ${
            xp.leave_status
              ? `<div style="font-size:12px; color:#9ca3af;">
                  Status: ${xp.leave_status}
                </div>`
              : ''
          }
        </div>
      </div>
      ${adminButtonsHtml}
    `;

    Swal.fire({
      title: 'Urlaubsantrag',
      html,
      showCloseButton: true,
      showConfirmButton: !isAdmin,
      confirmButtonText: 'Schließen',
      background: '#2c3e50',
      customClass: {
        popup: 'custom-swal-popup',
        confirmButton: 'custom-cancel-btn',
      },
      didOpen: () => {
        if (!isAdmin) return;
        const popup = Swal.getPopup();
        popup.querySelectorAll('[data-leave-action]').forEach(btn => {
          btn.addEventListener('click', async () => {
            const action = btn.getAttribute('data-leave-action');
            try {
              await handleLeaveActionFromCalendar(cleanId, action);
            } catch (err) {
              Swal.fire('Fehler', err.message || String(err), 'error');
            }
          });
        });
      },
    });
  }

  function showDayEventsModal(events, date){
    const dateLabel = new Date(date).toLocaleDateString("de-DE",{weekday:"long",day:"numeric",month:"long"});
    const html = events.map(ev=>{
      const title=ev.title||"-";
      const start=ev.start ? new Date(ev.start).toLocaleTimeString("de-DE",{hour:"2-digit",minute:"2-digit"}) : "-";
      const color=ev.backgroundColor||"#ccc";
      return `<div class="clickable-event" data-event-id="${ev.id}" style="border-left:5px solid ${color};padding:5px 10px;margin-bottom:5px;cursor:pointer;"><strong>${title}</strong><br><small>${start}</small></div>`;
    }).join("");

    Swal.fire({
      title:`Alle Termine am ${dateLabel}`, html, showCloseButton:true, confirmButtonText:"Schließen", width:"600px", background:"#f9f9f9",
      didOpen:()=>{
        U.qa(".clickable-event").forEach(el=> el.addEventListener("click", function(){
          const id=this.getAttribute("data-event-id");
          let clicked=S.fc.getEventById(id);
          if (!clicked && id.includes("-")) {
            const base=id.split("-")[0];
            clicked=S.fc.getEvents().find(e=> e.id && e.id.toString().split("-")[0]===base);
          }
          if (clicked){ Swal.close(); setTimeout(()=> showEventDetailsModal(clicked), 100); }
        }));
      }
    });
  }

  // =========================
  // CRUD: drag/resize, delete, duplicate, edit
  // =========================
  function handleEventUpdate(info){
    const t=info.event.extendedProps.type;
    if (["public_holiday","holiday","sick","leave_request","recurring_leave"].includes(t)) return info.revert();

    Swal.fire({
      title:"Geben Sie einen Grund für die Änderung an",
      html:`<textarea id="change_reason" class="swal2-textarea" placeholder="Geben Sie einen Grund für die Änderung an"></textarea>`,
      showCancelButton:true, confirmButtonText:"Speichern", cancelButtonText:"Abbrechen",
      preConfirm:()=>{
        const txt=document.getElementById("change_reason").value.trim();
        if(!txt) Swal.showValidationMessage("Änderungsgrund ist erforderlich."); return txt;
      }
    }).then(async (r)=>{
      if (!r.isConfirmed) return info.revert();
      const taskId = info.event.id.split("-")[0];
      const start  = new Date(info.event.start);
      const end    = info.event.end ? new Date(info.event.end) : start;

      const { ok, json } = await U.postJSON(ROUTE.changeAppointment, {
        task_id: taskId,
        emp_personal_id: info.event.extendedProps.emp_personal_id || null,
        start_date: U.isoDate(start),
        end_date: U.isoDate(end),
        start_time: `${U.pad2(start.getHours())}:${U.pad2(start.getMinutes())}`,
        end_time:   `${U.pad2(end.getHours())}:${U.pad2(end.getMinutes())}`,
        change_reason: r.value,
        type: t,
      });

      if (ok && json?.success){
        Swal.fire("Success!","Veranstaltung erfolgreich aktualisiert.","success").then(loadCalendarTasks);
      } else {
        Swal.fire("Error!", json?.message || "Failed to update event.","error");
        info.revert();
      }
    });
  }

  document.addEventListener("click", async (e)=>{
    const btn = e.target.closest("#delete_event"); if (!btn) return;
    e.preventDefault();
    const id   = btn.getAttribute("data-event-id");
    const type = btn.getAttribute("data-event-type");

    if (["holiday","sick","public_holiday","recurring_leave","leave_request"].includes(type)){
      Swal.fire({ icon:"warning", title:"Löschen nicht erlaubt", text:"Dieser Termin kann nicht gelöscht werden.", confirmButtonColor:"#d92127" });
      return;
    }

    const url = type==="appointment" ? ROUTE.deleteAppointment(id) : ROUTE.deleteTask(id);
    const view=S.fc.view.type, date=S.fc.getDate();

    const res = await Swal.fire({
      title:"Are you sure?", text:"This action will permanently delete the event.", icon:"warning",
      showCancelButton:true, confirmButtonText:"Yes, delete it!", cancelButtonText:"Cancel",
      confirmButtonColor:"#d92127", cancelButtonColor:"#93c21c",
    });
    if (!res.isConfirmed) return;

    try{
      const r = await fetch(url, { method:"DELETE", headers:{ "X-CSRF-TOKEN": CSRF(), "Content-Type":"application/json" }});
      const d = await r.json();
      if (d.status==="success"){
        loadCalendarTasks(()=>{ S.fc.changeView(view); S.fc.gotoDate(date); });
        Swal.fire({ icon:"success", title:"Deleted!", text:"The event has been deleted successfully.", timer:1500, showConfirmButton:false });
      } else {
        Swal.fire({ icon:"error", title:"Error", text:"Failed to delete the event." });
      }
    }catch{
      Swal.fire({ icon:"error", title:"Error", text:"Something went wrong." });
    }
  });

  $(document).on("click",".duplicate-event", async function(e){
    e.preventDefault();
    const id=$(this).data("event-id");
    const r = await Swal.fire({
      title:"Duplizieren auf neues Datum", input:"date", inputLabel:"Wähle ein Datum",
      inputAttributes:{ min:new Date().toISOString().split("T")[0] },
      showCancelButton:true, confirmButtonText:"Duplizieren", cancelButtonText:"Abbrechen",
      inputValidator:(v)=> (!v ? "Datum ist erforderlich!" : undefined),
    });
    if (!r.isConfirmed) return;
    try{
      const res = await U.send("POST", ROUTE.duplicateAppointment, new URLSearchParams({ appointment_id:id, new_date:r.value }));
      Swal.fire("Erfolgreich!", res.message || "Dupliziert", "success");
      loadCalendarTasks(()=> S.fc.gotoDate(res?.data?.start_date || r.value));
    }catch{
      Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
    }
  });

  $(document).on("click",".edit-event", async function(e){
    e.preventDefault(); Swal.close();
    const id=$(this).data("event-id");

    const data = await U.getJSON(ROUTE.fetchMainAppointment(id));
    $(".new_task_card").show(); $(".title").text("TERMIN BEARBEITEN");

    // Basics
    $("#appointment_id").val(data.id);
    $("#name").val(data.name ?? ""); $("#note").val(data.note ?? "");
    $("#color").val(data.color ?? "").trigger("change"); $("#colorIcon").css("color", data.color ?? "#000");

    // Selects
    $("#appointment_type").val(data.appointment_type ?? "");
    $("#execution_type").val(data.execution_type ?? "").trigger("change");
    $("#priority").val(data.priority ?? "").trigger("change");
    $("#date_type").val(data.date_type ?? "").trigger("change");
    $("#repeat").val(data.repeat ?? "").trigger("change");

    // Dates/Times
    $("#start_date").val(data.start_date ?? ""); $("#end_date").val(data.end_date ?? "");
    $("#start_time").val(data.start_time ?? ""); $("#end_time").val(data.end_time ?? "");
    $("#total_time").val(data.total_time ?? "");

    // Reminder & next steps
    $("#reminder_date").val(data.reminder_date ?? ""); $("#reminder_time").val(data.reminder_time ?? "");
    if (data.next_step){
      if (!$(`#next_step option[value="${data.next_step}"]`).length) $("#next_step").append(new Option(data.next_step, data.next_step, true, true));
      $("#next_step").val(data.next_step).trigger("change");
    } else { $("#next_step").val("").trigger("change"); }

    try{
      const responsible = Array.isArray(data.responsible_report) ? data.responsible_report : JSON.parse(data.responsible_report||"[]");
      $("#report_responsible").val(responsible).trigger("change");
    }catch{ $("#report_responsible").val([]).trigger("change"); }

    // Address
    $("#full_address").val(data.full_address ?? "");
    $("#street-input").val(data.street ?? "");
    $("#city-input").val(data.city ?? "");
    $("#postal_code-input").val(data.postcode ?? "");
    $("#latitude-input").val(data.latitude ?? "");
    $("#longitude-input").val(data.longitude ?? "");

    // Contact
    $("#phone").val(data.phone ?? ""); $("#email").val(data.email ?? ""); $("#link").val(data.link ?? "");
    $("#contact_type").val(data.contact_type ?? ""); $("#description").val(data.description ?? "");
    // ---- contact mode -> select correct radio + trigger UI ----
      const mode = (data.contact_mode || "new"); // "new" | "select" | "ticket"
      $("#contact_mode").val(mode);

      // your UI toggle listens to input.contact-type-toggle change:
      $(`input.contact-type-toggle[value="${mode}"]`)
        .prop("checked", true)
        .trigger("change");

        // ---- preselect customer (only if mode=select) ----
        if (mode === "select" && data.customer_id) {
          const $cl = $("#customer_id"); // this is your .contact_list select
          const label = (data.name || `Kunde #${data.customer_id}`) + (data.contact_type ? ` - ${data.contact_type}` : "");

          if ($cl.find(`option[value="${data.customer_id}"]`).length === 0) {
            $cl.append(new Option(label, data.customer_id, true, true));
          }
          $cl.val(String(data.customer_id)).trigger("change");
        }


      const toBool = (v) => String(v ?? 0) === "1" || v === true || v === 1;

      $("#switchPublic").prop("checked", toBool(data.public));
      const hasInquiry =
        Array.isArray(data.product_inquiry) && data.product_inquiry.length > 0;

      const wantsInquiry = hasInquiry; // only open if snapshot exists

      $("#switchContact").prop("checked", wantsInquiry);

      // if your UI needs to react, trigger change only when true
      if (wantsInquiry) {
        $("#switchContact").trigger("change");
      } else {
        // hard-close inquiry UI (adjust selector to your wrapper)
        $("#appointmentWrapper").hide();
        $("#pre_type").val("").trigger("change");
        $("#source").val("").trigger("change");
      }

      $("#switchReport").prop("checked", toBool(data.is_report));

    $("#pre_type").val(data.pre_type ?? "").trigger("change"); 

    if (data.source){
      if (!$(`#source option[value="${data.source}"]`).length) $("#source").append(new Option(data.source, data.source, true, true));
      $("#source").val(data.source).trigger("change");
    } else { $("#source").val("").trigger("change"); }

    $("#branch_id").val(data.branch_id ?? "").trigger("change");
    $("#branch_address_id").val(data.branch_address_id ?? "").trigger("change");
    $("#employee").val(data.employee_ids ?? []).trigger("change");
    $("#change_date").val(data.change_date ?? ""); $("#change_reason").val(data.change_reason ?? "");

    $(".audit-info").html(`
      <div>Erstellt von: <strong>${data.created_by_name ?? "-"}</strong></div>
      <div>Geändert von: <strong>${data.changed_by_name ?? "-"}</strong></div>
      <div>Erstellt am: ${data.created_at ?? "-"} | Geändert am: ${data.updated_at ?? "-"}</div>`);

  
        // Products (prefer product_json from fetch; fallback to products)
      let parsed = data.product_json ?? data.products ?? null;

        try {
          if (typeof parsed === "string") parsed = JSON.parse(parsed);
          if (typeof parsed === "string") parsed = JSON.parse(parsed);
        } catch (_) {
          parsed = null;
        }

        let ids = [];

        if (Array.isArray(parsed)) {
          ids = parsed
            .map(item => item.uid || `${item.name}_${item.alternative_id}`)
            .filter(Boolean);

          $("#products").val(JSON.stringify(parsed));
        } else if (parsed && typeof parsed === "object") {
          // backward compatibility with old saved object format
          ids = Object.entries(parsed)
            .map(([name, tuple]) => `${name}_${tuple?.[0]}`)
            .filter(Boolean);

          const converted = Object.entries(parsed).map(([name, tuple]) => ({
            uid: `${name}_${tuple?.[0]}`,
            name: name || '',
            alternative_id: tuple?.[0] || null,
            product_id: tuple?.[1] || null,
            customer_id: tuple?.[2] || null,
            city: null
          }));

          $("#products").val(JSON.stringify(converted));
        }

        loadCustomerProducts(data.customer_id, ids.length ? ids : undefined);


    // Ticket prefill
    TICKETS.initSelects();
    const preProblemId = data.problem_id || null;
    const preTaskId = data.problem_task_id || null;
    const preTicketCust = data.ticket_customer_id || data.customer_id || null;
    const preCustLabel = data.customer_label || data.customer_name || null;

    if (preProblemId || preTaskId){
      if ($("#ticketMode").length) $("#ticketMode").prop("checked", true).trigger("change");
      TICKETS.setHidden({ problemId:preProblemId, problemTaskId:preTaskId, mode:"ticket" });
      if ($("#ticket_customer_id").length && preTicketCust){
        addOrSelect($("#ticket_customer_id"), preTicketCust, preCustLabel);
        await TICKETS.loadProblems(preTicketCust, preProblemId);
        if (preProblemId) await TICKETS.loadTasks(preProblemId, preTaskId);
      } else if (preProblemId) {
        await TICKETS.loadTasks(preProblemId, preTaskId);
      }
    } else {
      TICKETS.setHidden({});
    }

    function addOrSelect($sel,id,text){
      if(!$sel.find(`option[value="${id}"]`).length) $sel.append(new Option(text||`#${id}`, id, true, true));
      $sel.val(id).trigger("change");
    }

    // Inquiry snapshot
    const isInquiry = String(data.is_contact) === "1";
    const hasInquirySnapshot =
      Array.isArray(data.product_inquiry) && data.product_inquiry.length > 0;

    if (isInquiry && hasInquirySnapshot && window.prefillInquiryFromSnapshot) {
      window.prefillInquiryFromSnapshot(data.product_inquiry);
    }
  });

  // =========================
  // Filter by date (from mini)
  // =========================
  async function filterMainCalendarByDate(date){
    if (!date) return;
    const employeeData = getSelectedEmployeeData();
    try{
      const res = await U.getJSON(ROUTE.getCalendar, { employee_data: JSON.stringify(employeeData), search: S.currentSearch||"", filter_date: date });
      const rows = Array.isArray(res?.data) ? res.data : [];
      const events = rows.flatMap(mapServerItemToEvents);
      U.extractHolidayDates(events);
      initializeCalendar(events);
      S.fc.gotoDate(date);
    }catch(err){
      console.error("filterMainCalendarByDate:", err);
    }
  }

  // =========================
  // Employee picker
  // =========================
  function formatEmployee(e){
    if (!e.id) return e.text;
    const img = $(e.element).data("image");
    return `<div style="display:flex;align-items:center;"><img src="${img||"/images/default-avatar.png"}" style="width:20px;height:20px;border-radius:50%;margin-right:10px;"><span>${e.text}</span></div>`;
  }
  function initEmployeeSelect2(){
    $(".employee").select2({ templateResult:formatEmployee, templateSelection:formatEmployee, escapeMarkup:(m)=>m });
    $("#employee").select2({ templateResult:formatEmployee, templateSelection:formatEmployee, escapeMarkup:(m)=>m })
      .on("change", function(){
        const ids = ($(this).val()||[]).map(String);
        S.selectedEmployeeIds = new Set(ids);
        window.selectedEmployeeIds = S.selectedEmployeeIds;
        U.qa(".employee_check").forEach(cb=>{
          const id=String(cb.dataset.id); cb.checked = S.selectedEmployeeIds.has(id);
          const img = document.getElementById(`employeeCheck${id}`);
          if (img){ img.classList.toggle("emp_active", cb.checked); img.style.borderColor = cb.checked ? (img.style.borderColor||"red") : "transparent"; }
        });
      });
  }
  function syncCheckboxWithDropdown(){
    const ids=[]; U.qa(".employee_check:checked").forEach(cb=>{
      const id=cb.dataset.id; ids.push(id);
      const li=cb.closest(".list-item");
      const name=li?.querySelector("span")?.innerText?.trim() || "Unbekannt";
      const img=li?.querySelector("img")?.getAttribute("src") || "/images/default-avatar.png";
      U.ensureOption($("#employee"), id, name, img);
    });
    S.selectedEmployeeIds = new Set(ids);
    window.selectedEmployeeIds = S.selectedEmployeeIds;
    $("#employee").val(ids).trigger("change");
  }

  // Anfrage -> Teilnehmer sync
  window.updateParticipantsFromInquiry = function () {
    const ids = new Set();

    $("#inquiryPreviewBody .inquiry-employee-select, #inquiryPreviewBody .inquiry-field-employee-select").each(function () {
      const v = $(this).val();
      if (Array.isArray(v)) {
        v.forEach(id => id && ids.add(String(id)));
      } else if (v) {
        ids.add(String(v));
      }
    });

    const idArray = Array.from(ids);
    const $emp = $("#employee");

    idArray.forEach(id => {
      if (!$emp.find(`option[value="${id}"]`).length) {
        const label =
          $(`#inquiryPreviewBody select option[value="${id}"]:first`).text().trim() ||
          `#${id}`;
        $emp.append(new Option(label, id, false, false));
      }
    });

    $emp.val(idArray).trigger("change");

    S.selectedEmployeeIds = new Set(idArray);
    window.selectedEmployeeIds = S.selectedEmployeeIds;

    U.qa(".employee_check").forEach(cb => {
      const id = String(cb.dataset.id);
      const on = ids.has(id);
      cb.checked = on;
      const img = document.getElementById(`employeeCheck${id}`);
      if (img) {
        img.classList.toggle("emp_active", on);
        img.style.borderColor = on ? (img.style.borderColor || "red") : "transparent";
      }
    });

    if ($("#mobileEmployeeSelect").length) {
      $("#mobileEmployeeSelect").val(idArray).trigger("change.select2");
    }
  };

  async function fetchEmployees(q="", filter="employee"){
    const box = document.getElementById("search_emp_result"); if (!box) return;
    box.innerHTML = "";
    if (S.empAbort) S.empAbort.abort();
    S.empAbort = new AbortController();

    try{
      const r = await fetch(ROUTE.getEmployees(q, filter), { signal:S.empAbort.signal });
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      const result = await r.json();
      const data = result.data || [];
      if (!data.length){ box.innerHTML="<p>No results found.</p>"; return; }

      const seen = new Set(); const fav=(S.favoriteEmployeeIds||[]).map(String);

      if (U.isMobile()){
        const select=document.createElement("select");
        select.id="mobileEmployeeSelect"; select.className="form-control employee"; select.setAttribute("multiple","multiple");
        data.forEach(emp=>{
          if (seen.has(String(emp.id))) return; seen.add(String(emp.id));
          const opt=document.createElement("option");
          opt.value=emp.id;
          opt.text = `${emp.name} ${emp.lastname}`;
          select.appendChild(opt);
        });
        box.appendChild(select);
        $("#mobileEmployeeSelect").select2({
          templateResult:formatEmployee, templateSelection:formatEmployee, placeholder:"Mitarbeiter auswählen",
          width:"100%", escapeMarkup:(m)=>m, dropdownParent: $("#search_emp_result")
        }).off("change").on("change", function(){
          S.selectedEmployeeIds = new Set(($(this).val()||[]).map(String));
          window.selectedEmployeeIds = S.selectedEmployeeIds;
          loadCalendarTasks();
        });
        const pre = Array.from(S.selectedEmployeeIds);
        if (pre.length) $("#mobileEmployeeSelect").val(pre).trigger("change.select2");
        else if (fav.length){ $("#mobileEmployeeSelect").val(fav).trigger("change"); fav.forEach(id=> S.selectedEmployeeIds.add(String(id))); }
      } else {
        data.forEach(emp=>{
          if (seen.has(String(emp.id))) return; seen.add(String(emp.id));
          const border = emp.color || "red"; const checked = S.selectedEmployeeIds.has(String(emp.id));
          const div=document.createElement("div"); div.classList.add("list-item");
          div.innerHTML = `
            <div class="d-flex align-items-center m-0">
              <input type="checkbox" class="employee_check" data-id="${emp.id}" id="check${emp.id}" style="display:none" ${checked?"checked":""}>
              <div class="avatar mr-1">
                <img src="/images/employee/${emp.image}" alt="avatar" width="48" height="48" data-id="${emp.id}" class="employee_checkbox ${checked?"emp_active":""}" id="employeeCheck${emp.id}" style="border-color:${checked?border:"transparent"};border-radius:50%;padding:2px;">
              </div>
              <span><span style="font-size:11px;font-weight:bold;text-transform:uppercase;">${emp.name}</span></span>
            </div>`;
          const img=div.querySelector(`#employeeCheck${emp.id}`), cb=div.querySelector(`#check${emp.id}`);
          img.addEventListener("click", function(){
            const id=String(emp.id), on=!cb.checked; cb.checked=on;
            this.classList.toggle("emp_active", on); this.style.borderColor = on?border:"transparent";
            if (on) S.selectedEmployeeIds.add(id); else S.selectedEmployeeIds.delete(id);
            syncCheckboxWithDropdown(); loadCalendarTasks();
          });
          cb.addEventListener("change", function(){
            const id=String(emp.id); if (this.checked) S.selectedEmployeeIds.add(id); else S.selectedEmployeeIds.delete(id);
            syncCheckboxWithDropdown(); loadCalendarTasks();
          });
          box.appendChild(div);
        });
        U.qa(".employee_check").forEach(cb=>{
          const id=String(cb.dataset.id), on=S.selectedEmployeeIds.has(id); cb.checked=on;
          const img=document.getElementById(`employeeCheck${id}`); if (img){ img.classList.toggle("emp_active", on); img.style.borderColor = on ? (img.style.borderColor||"red") : "transparent"; }
        });
        syncCheckboxWithDropdown();
      }
    }catch(err){
      if (err.name==="AbortError") return;
      console.error("fetchEmployees:", err);
      box.innerHTML = "<p>Failed to fetch data. Please try again later.</p>";
    }
  }

  function bindSearchControls(){
    U.qa('input[name="filter"]').forEach(r=>{
      r.addEventListener("change", function(){ S.selectedEmployeeIds = U.selectedFromDOM(); fetchEmployees("", this.value); });
      if (!S.didAutoselectFavorites && S.selectedEmployeeIds.size===0 && S.favoriteEmployeeIds.length){
        S.favoriteEmployeeIds.forEach(id=> S.selectedEmployeeIds.add(String(id)));
        S.didAutoselectFavorites = true;
        autoSelectFavoriteEmployees();
      }
    });
    const input = U.q(".employee_search_input input");
    if (input && !input.dataset.bound){
      input.dataset.bound="1";
      let t; input.addEventListener("input", function(){
        S.selectedEmployeeIds = U.selectedFromDOM();
        clearTimeout(t); t=setTimeout(()=> fetchEmployees(this.value.trim(), "employee"), 200);
      });
    }
    fetchEmployees("", "employee");
  }

  // =========================
  // Inquiry + products
  // =========================
  function loadCustomerProducts(customerId, preselectIds){
    const $block=$('.product-select-block'), $select=$('#productSelect');
    $block.removeClass('d-none');
    if ($select.length){ if ($select.data('select2')){ $select.off().select2('destroy'); } $select.empty(); }

    if (!customerId){ initSelect2Singleton($select,{ multiple:true }); return; }

    $.ajax({ url: ROUTE.productsByCustomer, method:"GET", data:{ customer_id:customerId }, dataType:"json" })
      .done(groups => {
          let hasAny = false;

          const seenUids = new Set();
          const norm = (v) => String(v ?? "").trim();

          (groups || []).forEach(g => {
            if (!g || !Array.isArray(g.children) || !g.children.length) return;

            const $og = $('<optgroup>').attr('label', g.text || 'Gruppe');

            g.children.forEach(p => {
              const uid = norm(`${p.product_name}_${p.alternative_id}`);

              // ✅ IMPORTANT: skip duplicates (fixes your repeated WÄRMEPUMPE_2250)
              if (!uid || seenUids.has(uid)) return;
              seenUids.add(uid);

              S.productMap[uid] = p;

              $og.append(
                $('<option>')
                  .val(uid)
                  .text(`${p.product_name}${p.city ? ' (' + p.city + ')' : ''}`)
              );

              hasAny = true;
            });

            if ($og.children().length) $select.append($og);
          });

          initSelect2Singleton($select, { multiple: true });

          if (!hasAny) {
            $select.empty().append($('<option disabled>').text('— Keine Produkte für diesen Kontakt gefunden —'));
            initSelect2Singleton($select, { multiple: true });
            $('#products').val('');
            return;
          }

          // ✅ compute preselect ids
          let ids = [];
          if (Array.isArray(preselectIds) && preselectIds.length) {
            ids = preselectIds.map(norm);
          } else {
            const savedJson = $('#products').val();
                if (savedJson) {
                  try {
                    const parsed = JSON.parse(savedJson);

                    if (Array.isArray(parsed)) {
                      ids = parsed
                        .map(item => norm(item.uid || `${item.name}_${item.alternative_id}`))
                        .filter(Boolean);
                    } else if (parsed && typeof parsed === 'object') {
                      ids = Object.entries(parsed)
                        .map(([name, tuple]) => norm(`${name}_${tuple?.[0]}`))
                        .filter(Boolean);
                    }
                  } catch {}
                }
          }

          // ✅ apply selection AFTER select2 exists + after options exist
          const optionValues = new Set(Array.from($select[0].options).map(o => o.value));
          ids = ids.filter(uid => optionValues.has(uid));


          // bind change handler first (so hidden input stays synced)
         $select.off('change.products').on('change.products', function () {
            const val = $(this).val() || [];
            const out = [];

            val.forEach(uid => {
              const info = S.productMap[uid];
              if (!info) return;

              out.push({
                uid: uid,
                name: info.product_name || '',
                alternative_id: info.alternative_id || null,
                product_id: info.product_id || null,
                customer_id: info.customer_id || null,
                city: info.city || null
              });
            });

            $('#products').val(out.length ? JSON.stringify(out) : '');
          });

          // then set the value
          if (ids.length) {
            $select.val(ids).trigger('change');
          }
        })

      .fail(()=>{
        initSelect2Singleton($select,{ multiple:true });
      });
  }

  // =========================
  // Form save
  // =========================
  function getSelectedEmployeeData(){
    const ids = Array.from(S.selectedEmployeeIds).map(String);
    if (ids.length) return ids.map(id=>({ employee_id:id, tasks_only:0, appointments_only:1 }));
    if (U.isMobile()){
      const m=($("#mobileEmployeeSelect").val()||[]).map(String);
      if (m.length) return m.map(id=>({ employee_id:id, tasks_only:0, appointments_only:1 }));
    } else {
      const checks = U.qa(".employee_check:checked").map(cb=>String(cb.dataset.id));
      if (checks.length) return checks.map(id=>({ employee_id:id, tasks_only:0, appointments_only:1 }));
    }
    return [{ employee_id: AUTH_EMPLOYEE_ID, tasks_only:0, appointments_only:1 }];
  }

  $(".save-task").on("click", async function(e){
    e.preventDefault();

    syncCheckboxWithDropdown();
    TICKETS.initSelects();
    const $form = $("#task-store-form");

    const mode = TICKETS.currentMode();
    const ticketCustomerId = $("#ticket_customer_id").val();
    const ticketProblemId  = $("#ticket_problem_id").val();
    const ticketTaskId     = $("#ticket_task_id").val();
    const ticketAutoCreate = $("#ticket_create_task").is(":checked") ? 1 : 0;

    const rawName = ($("#name").val()||"").trim();
    const selContact = $("#customer_id").select2("data");
    const contactText = (selContact && selContact[0]?.text ? selContact[0].text.split(" - ")[0] : "").trim();
    const selProb = $("#ticket_problem_id").select2("data");
    const probTxt = (selProb && selProb[0]?.text ? selProb[0].text : "").trim();
    const selTask = $("#ticket_task_id").select2("data");
    const taskTxt = (selTask && selTask[0]?.text ? selTask[0].text : "").trim();

    const errs = [];
    const employee = $("#employee").val();
    const startDate = $("#start_date").val();
    const endDate   = $("#end_date").val();
    const reminderDate = $("#reminder_date").val();
    const nextStep  = $("#next_step").val();
    const responsible = $("#report_responsible").val();

    if (!employee || employee.length === 0) errs.push("Bitte weisen Sie mindestens einen Mitarbeiter zu.");

    let title = rawName;
    if (!title){
      if (mode==="select") title = contactText;
      else if (mode==="ticket"){
        title = taskTxt || probTxt || contactText;
        if (!title && ticketAutoCreate) title = `Ticket ${ticketProblemId||""}`.trim();
      }
    }
    if (!title) title = ($("#appointment_type").val()||"").trim() || ($("#full_address").val()||"").trim();
    $("#name").val(title);

    TICKETS.setHidden({ problemId:ticketProblemId||null, problemTaskId:ticketTaskId||null, mode, autoCreate:ticketAutoCreate });

    if (!title) errs.push("Der Titel darf nicht leer sein.");
    if (!startDate) errs.push("Das Startdatum darf nicht leer sein.");
    if (!endDate)   errs.push("Das Enddatum darf nicht leer sein.");
    if (startDate && endDate && new Date(startDate)>new Date(endDate)) errs.push("Das Startdatum darf nicht größer als das Enddatum sein.");
    if (startDate && endDate){
      const holidayOn = U.hasHolidayBetween(startDate, endDate);
      if (holidayOn) errs.push(`Datum ${holidayOn} ist ein Feiertag.`);
    }
    if (reminderDate){
      if (!nextStep) errs.push("Bitte wählen Sie einen nächsten Schritt.");
      if (!responsible || responsible.length===0) errs.push("Bitte wählen Sie einen Verantwortlichen.");
      else {
        const jsonResponsible = JSON.stringify([responsible]);
        if (!$("#responsible_json").length) $("<input>", { type:"hidden", id:"responsible_json", name:"responsible_json", value:jsonResponsible }).appendTo($form);
        else $("#responsible_json").val(jsonResponsible);
      }
    }
    if (mode==="ticket"){
      if (!ticketCustomerId) errs.push("Bitte wählen Sie einen Ticket-Kunden.");
      if (!ticketProblemId)  errs.push("Bitte wählen Sie ein Ticket/Problem.");
      if (!ticketTaskId && !ticketAutoCreate) errs.push('Bitte wählen Sie einen Ticket-Task oder aktivieren Sie "Neuen Ticket-Task aus Termin-Titel erstellen".');
    }
    if (errs.length){
      Swal.fire({ icon:"error", title:"Fehlerhafte Eingabe", html:`<ul style="text-align:left;">${errs.map(e=>`<li>${e}</li>`).join("")}</ul>` });
      return;
    }

    const appointmentId = $("#appointment_id").val();
    const method = appointmentId ? "PUT" : "POST";
    const url    = appointmentId ? ROUTE.updateMainAppointment(appointmentId) : ROUTE.storeMainAppointment;

    try{
      $(".save-task").prop("disabled", true).text("speichern...");
      await $.ajax({ url, type:method, data: $form.serialize() });

      $(".save-task").prop("disabled", false).text("speichern");
      $(".new_task_card").hide();
      $form.trigger("reset");
      $("#appointment_id").val("");
      $("#customer_id, #ticket_customer_id, #ticket_problem_id, #ticket_task_id").val(null).trigger("change");
      $("#name, #name_display, #contact_type, #phone, #email, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input, #full_address").val("");
      $("#contact_mode").val("new"); $("#newContact").prop("checked", true).trigger("change");
      TICKETS.setHidden({});
      Swal.fire({ icon:"success", title:"Erfolg", text: appointmentId ? "Termin erfolgreich aktualisiert!" : "Termin erfolgreich gespeichert!" });

      const view=S.fc.view.type, date=S.fc.getDate();
      loadCalendarTasks(()=>{ S.fc.changeView(view); S.fc.gotoDate(date); });
    }catch(xhr){
      $(".save-task").prop("disabled", false).text("speichern");
      const errors = xhr?.responseJSON?.errors || {};
      const html = Object.values(errors).flat().map(m=>`<li>${m}</li>`).join("");
      Swal.fire({ icon:"error", title:"Fehler", html:`<ul>${html || "Unbekannter Fehler aufgetreten."}</ul>` });
    }
  });

  // =========================
  // Settings
  // =========================
  function loadSettingsIntoModal(settings){
    if (settings.favorite_employees) $("#favoriteEmployees").val(settings.favorite_employees.map(String)).trigger("change");
    if (settings.hidden_views){
      $('input[name="hidden_views[]"]').each(function(){ $(this).prop("checked", settings.hidden_views.includes($(this).val())); });
    }
    if (settings.calendar_color) $("#calendarColorPicker").val(settings.calendar_color);
  }
  function applySettingsToCalendar(settings){
    if (settings.favorite_employees?.length && $("#mobileEmployeeSelect").length) $("#mobileEmployeeSelect").val(settings.favorite_employees).trigger("change");
    if (settings.hidden_views) settings.hidden_views.forEach(v=>{ const btn=document.querySelector(`.fc-${v}-button`); if (btn) btn.style.display="none"; });
    const fcEl=document.querySelector(".fc"); if (!fcEl) return;
    if (settings.calendar_color==="black"){ fcEl.style.backgroundColor="#111"; fcEl.style.color="#fff"; }
    else if (settings.calendar_color==="red"){ fcEl.style.backgroundColor="#ffefef"; fcEl.style.color=""; }
    else { fcEl.style.backgroundColor=""; fcEl.style.color=""; }
  }
  function loadUserCalendarSettings(){
    fetch(ROUTE.calendarSettingsGet)
      .then(r=>r.json())
      .then(({calendar_settings})=>{
        const favs = (calendar_settings.favorite_employee_ids || calendar_settings.favorite_employees || []).map(String);
        S.favoriteEmployeeIds = favs;
        S.selectedEmployeeIds = new Set([...Array.from(S.selectedEmployeeIds), ...favs]);
        loadSettingsIntoModal(calendar_settings);
        applySettingsToCalendar(calendar_settings);
        fetchEmployees("", "employee");
      });
  }
  $("#calendarSettingsForm").on("submit", async function(e){
    e.preventDefault();
    const $f=$(this), $btn=$f.find('button[type="submit"]');
    const settings = {
      favorite_employees: $("#favoriteEmployees").val() || [],
      hidden_views: $('input[name="hidden_views[]"]:checked').map((_,el)=>el.value).get(),
      calendar_color: $("#calendarColorPicker").val(),
    };
    $btn.prop("disabled", true).text("Speichern…");
    try{
      const res = await fetch(ROUTE.calendarSettingsSave, {
        method:"POST", credentials:"same-origin",
        headers:{ "Content-Type":"application/json", Accept:"application/json", "X-CSRF-TOKEN": CSRF() },
        body: JSON.stringify({ calendar_settings: settings }),
      });
      const text = await res.text(); let payload; try{ payload=JSON.parse(text); }catch{ payload={ raw:text }; }
      if (!res.ok){
        const msg = payload?.message || payload?.error || (payload?.raw && payload.raw.slice(0,200)) || `HTTP ${res.status}`;
        Swal.fire({ icon:"error", title:"Fehler", text: msg }); return;
      }
      if (payload.status==="success"){
        try{ applySettingsToCalendar(settings); }catch{}
        $("#calendarSettingsModal").one("hidden.bs.modal", function(){
          Swal.fire({ icon:"success", title:"Gespeichert!", text:"Einstellungen wurden gespeichert.", timer:1200, showConfirmButton:false })
            .then(()=> location.reload());
        }).modal("hide");
      } else {
        const msg = payload?.message || (payload?.errors && Object.values(payload.errors).flat().join("\n")) || "Einstellungen konnten nicht gespeichert werden.";
        Swal.fire({ icon:"error", title:"Fehler", text: msg });
      }
    }catch(err){
      Swal.fire({ icon:"error", title:"Netzwerkfehler", text:"Bitte erneut versuchen." });
    }finally{
      $btn.prop("disabled", false).text("Speichern");
    }
  });
  $("#calendarSettingsModal").on("shown.bs.modal", loadUserCalendarSettings);
  $("#favoriteEmployees").select2({ dropdownParent: $("#calendarSettingsModal"), width:"100%" });

  // =========================
  // Contact/ticket toggles + contacts select2
  // =========================
  $("input.contact-type-toggle").on("change", function(){
    const mode=$(this).val(); $("#contact_mode").val(mode);
    if (mode==="new"){
      $(".contact-name-block").removeClass("d-none");
      $(".contact-select-block").addClass("d-none");
      $(".ticket-block").addClass("d-none");
      TICKETS.setHidden({});
    } else if (mode==="select"){
      $(".contact-name-block").addClass("d-none");
      $(".contact-select-block").removeClass("d-none");
      $(".ticket-block").addClass("d-none");
      TICKETS.setHidden({});
    } else if (mode==="ticket"){
      $(".contact-name-block, .contact-select-block").addClass("d-none");
      $(".product-select-block, .ticket-block").removeClass("d-none");
      TICKETS.initSelects();
    }
  });

  $(".contact_list").select2({
    placeholder:"Wählen", allowClear:true, minimumInputLength:0,
    ajax:{
      url: ROUTE.contactList, type:"GET", dataType:"json", delay:250,
      data:(p)=>({ search:p.term||"" }),
      processResults:(data)=>({ results: $.map(data, item=>({
        id:item.main_id, text:`${item.name} ${item.lastname} - ${item.type}`, type:item.type,
        phone:item.phone||"", email:item.email||"", street:item.street||"", postcode:item.postcode||"", city:item.city||"",
        longitude:item.longitude||"", latitude:item.latitude||"",
        full_address: item.street && item.city && item.postcode ? `${item.street}, ${item.postcode} ${item.city}` : "",
      }))})
    }
  }).on("select2:select", function(e){
    const s=e.params.data; $("#contact_type").val(s.type);
    if (s.type==="Kunde"){ $(".product-select-block").removeClass("d-none"); loadCustomerProducts(s.id); }
    else {
      $(".product-select-block").addClass("d-none");
      $("#productSelect").empty().trigger("change"); $("#products").val("");
    }
    $(".phone").val(s.phone); $(".email").val(s.email); $("#full_address").val(s.full_address);
    $("#street-input").val(s.street); $("#city-input").val(s.city); $("#postal_code-input").val(s.postcode);
    $("#latitude-input").val(s.latitude); $("#longitude-input").val(s.longitude);
    if (s.text) $("#name").val(s.text.split(" - ")[0]);
  }).on("select2:clear", function(){
    $("#contact_type, .phone, .email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input").val("");
  }).on("select2:open", function(){ $(".select2-search__field").attr("placeholder","Tippen Sie, um zu suchen..."); });

  // =========================
  // Misc
  // =========================
  $(document).on('click', '#btnClearEmployees', function (e) {
    e.preventDefault();
    $('#employee').val(null).trigger('change');
    if ($('#mobileEmployeeSelect').length) {
      $('#mobileEmployeeSelect').val(null).trigger('change.select2');
    }
    $('.employee_check').prop('checked', false);
    $('.employee_checkbox').removeClass('emp_active').css('borderColor', 'transparent');
    $('[id^="appointmentWrapper"]').hide();
    if (typeof syncCheckboxWithDropdown === 'function') {
      syncCheckboxWithDropdown();
    }
    if (typeof loadCalendarTasks === 'function') {
      loadCalendarTasks();
    }
    if ($('#calendarSearch').length) {
      $('#calendarSearch').val(null).trigger('change');
    }
  });

  function selectCurrentUserOnly() {
    const currentId = AUTH_EMPLOYEE_ID;
    const $emp = $('#employee');
    $emp.val([currentId]).trigger('change');

    U.qa('.employee_check').forEach(cb => {
      cb.checked = (String(cb.dataset.id) === currentId);
    });
  }

 document.addEventListener("click", e => {
    if (!e.target.classList.contains("show_new_task")) return;
    
    // Fully reset before opening
    document.getElementById("task-store-form").reset();
    $("#appointment_id").val("");
    $(".title").text("TERMIN ERSTELLEN");
    $("#customer_id, #ticket_customer_id, #ticket_problem_id, #ticket_task_id, #productSelect, #report_responsible").val(null).trigger("change");

    selectCurrentUserOnly();
    if (D.newTaskCard) D.newTaskCard.style.display = "block";
  });

  document.addEventListener("change", function(e){
    if (e.target.classList.contains("employee_check")) syncCheckboxWithDropdown();
  });

  function autoSelectFavoriteEmployees() {
    const favoriteIds = (S.favoriteEmployeeIds || []).map(String);

    const persisted = U.selectedFromDOM();
    favoriteIds.forEach(id => persisted.add(id));
    S.selectedEmployeeIds = persisted;
    window.selectedEmployeeIds = S.selectedEmployeeIds;

    U.qa('.employee_check').forEach(cb => {
      const id = String(cb.dataset.id);
      const avatar = document.getElementById(`employeeCheck${id}`);
      const on = favoriteIds.includes(id);

      cb.checked = on;
      if (avatar) {
        avatar.classList.toggle('emp_active', on);
        avatar.style.borderColor = on ? (avatar.dataset.color || 'red') : 'transparent';
      }
    });

    if ($('#mobileEmployeeSelect').length) {
      $('#mobileEmployeeSelect').val(favoriteIds).trigger('change.select2');
    }

    $('#employee').val(favoriteIds).trigger('change');

    S.didAutoselectFavorites = true;
    loadCalendarTasks();
  }

  // =========================
  // Boot
  // =========================
  (function boot(){
    S.favoriteEmployeeIds = (window.favoriteEmployeeIds||[]).map(String);
    S.selectedEmployeeIds = new Set((S.favoriteEmployeeIds||[]).map(String));

    initEmployeeSelect2();
    bindSearchControls();

    if (typeof autoSelectFavoriteEmployees === "function" && S.favoriteEmployeeIds.length) {
      autoSelectFavoriteEmployees();
    } else {
      loadCalendarTasks();
    }

    document.addEventListener("change", function(e){
      if (e.target.classList.contains("employee_check") || e.target.classList.contains("employeeAppointment")) loadCalendarTasks();
    });
  })();

})();
</script>


<script>
  window.ALL_DEPARTMENTS = @json($departments ?? []);
  window.ALL_PRODUCTS    = @json($products ?? []);
  window.ALL_SERVICES    = @json($services ?? []);
</script>

<script>
/* =========================================================
   Inquiry UI: placeholder + visibility tied to "Anfrage" switch
   ========================================================= */
(function () {
  "use strict";

  function ensureInquiryPlaceholder() {
    const $tb = $("#inquiryPreviewBody");
    if (!$tb.find("tr").length) {
      $tb.html(
        '<tr data-placeholder="1">' +
          '<td colspan="6" class="text-center text-muted">' +
            'Bitte Produkt/Abteilung/Service wählen…' +
          '</td>' +
        '</tr>'
      );
    }
  }

  function clearInquiryPlaceholder() {
    $("#inquiryPreviewBody tr[data-placeholder='1']").remove();
  }

  function toggleInquiryWrapperVisibility() {
    const on = $("#switchContact").is(":checked");

    if (on) {
      // Anfrage ON:
      // - show inquiry table
      // - hide manual Teilnehmer block
      $("#inquiryPreviewWrapper").removeClass("d-none");
      $("#participantsBlock").addClass("d-none");

      ensureInquiryPlaceholder();
      if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
    } else {
      // Anfrage OFF:
      // - hide inquiry table
      // - show manual Teilnehmer block again
      $("#inquiryPreviewWrapper").addClass("d-none");
      $("#participantsBlock").removeClass("d-none");

      // If you want to fully reset when off, uncomment:
      // $("#inquiryPreviewBody").empty();
      // if (window.S) window.S.inquiryRowIndex = 0;
    }
  }


  // Bind + run on ready
  $(document).on("change", "#switchContact", toggleInquiryWrapperVisibility);
  $(toggleInquiryWrapperVisibility);

  // Clear placeholder whenever rows appear
  const tbody = document.getElementById("inquiryPreviewBody");
  if (tbody) {
    const mo = new MutationObserver(() => clearInquiryPlaceholder());
    mo.observe(tbody, { childList: true });
  }

  // If a previous fetch function exists, wrap it to ensure UI visibility
  if (window.fetchInquiryDepartmentEmployees) {
    const _origFetchInquiry = window.fetchInquiryDepartmentEmployees;
    window.fetchInquiryDepartmentEmployees = async function (selectedUids) {
      await _origFetchInquiry(selectedUids);
      if ($("#inquiryPreviewBody tr").length) clearInquiryPlaceholder();
      if ($("#switchContact").is(":checked")) {
        $("#inquiryPreviewWrapper").removeClass("d-none");
      }
    };
  }
})();
</script>

<script> 
    (function () {
      "use strict";

      // ---- Blade globals (already injected above this script) ----
      const ALL_DEPARTMENTS = Array.isArray(window.ALL_DEPARTMENTS) ? window.ALL_DEPARTMENTS : (window.ALL_DEPARTMENTS || []);
      const ALL_PRODUCTS    = Array.isArray(window.ALL_PRODUCTS)    ? window.ALL_PRODUCTS    : (window.ALL_PRODUCTS || []);
      const ALL_SERVICES    = Array.isArray(window.ALL_SERVICES)    ? window.ALL_SERVICES    : (window.ALL_SERVICES || []);

      // ---- Routes (merge if Script 1 set window.ROUTE) ----
      window.ROUTE = Object.assign({}, window.ROUTE || {}, {
        datasets: (window.ROUTE && window.ROUTE.datasets) || "/calendar/datasets",
      });

      // ---- Dataset service ----
      const DS = {
        _loading: null,
        departments: [],
        products: [],
        services: [],

        _normalizeArray(arr) {
          return (Array.isArray(arr) ? arr : []).map(o => {
            const id =
              o.id ??
              o.value ??
              o.department_id ??
              o.product_id ??
              o.service_id;

            const name =
              o.localized_name ??
              o.department_name ??
              o.article_group ??
              o.product_name ??
              o.phase_section ??
              o.title ??
              o.name ??
              `#${id ?? "?"}`;

            return { ...o, id, name };
          }).filter(x => x.id != null);
        },

        _seedFromBlade() {
          let seeded = false;
          if (ALL_DEPARTMENTS.length && !this.departments.length) {
            this.departments = this._normalizeArray(ALL_DEPARTMENTS);
            seeded = true;
          }
          if (ALL_PRODUCTS.length && !this.products.length) {
            this.products = this._normalizeArray(ALL_PRODUCTS);
            seeded = true;
          }
          if (ALL_SERVICES.length && !this.services.length) {
            this.services = this._normalizeArray(ALL_SERVICES);
            seeded = true;
          }
          return seeded;
        },

        async ensure() {
          if (this.departments.length || this.products.length || this.services.length) return;

          // 1) Seed from Blade (no network)
          const gotBlade = this._seedFromBlade();
          if (gotBlade) return;

          // 2) Fallback to API
          if (!window.ROUTE || !window.ROUTE.datasets) return;
          if (this._loading) return this._loading;

          this._loading = fetch(window.ROUTE.datasets, { headers: { Accept: "application/json" } })
            .then(r => r.ok ? r.json() : ({ departments: [], products: [], services: [] }))
            .then(j => {
              // Normalize and set only if still empty (prefer Blade if present later)
              const dep = this._normalizeArray(j.departments || []);
              const pro = this._normalizeArray(j.products    || []);
              const svc = this._normalizeArray(j.services    || []);
              if (!this.departments.length) this.departments = dep;
              if (!this.products.length)    this.products    = pro;
              if (!this.services.length)    this.services    = svc;
            })
            .catch(() => { /* keep current */ })
            .finally(() => { this._loading = null; });

          return this._loading;
        },

        depName(id) {
          const x = this.departments.find(d => String(d.id) === String(id));
          return x ? (x.localized_name || x.department_name || x.name || `Abteilung #${id}`) : `Abteilung #${id}`;
        },
        prodName(id) {
          const x = this.products.find(p => String(p.id) === String(id));
          return x ? (x.localized_name || x.article_group || x.name || x.product_name || `Produkt #${id}`) : `Produkt #${id}`;
        },
        svcName(id) {
          const x = this.services.find(s => String(s.id) === String(id));
          const raw = x ? (x.localized_name || x.name || x.phase_section || x.title || `Service #${id}`) : `Service #${id}`;
          return translateService(raw);
        },
      };

      // ---- Label normalization for services ----
      function translateService(raw) {
        const key = String(raw || "").toLowerCase().trim();
        const map = {
          complete: "Komplett", komplett: "Komplett", komplet: "Komplett",
          montage: "Montage", assembly: "Montage", einbau: "Montage", installation: "Montage",
          plan: "Planung", plane: "Planung", planung: "Planung", design: "Planung",
          repair: "Reparatur", reparatur: "Reparatur", fix: "Reparatur", instandsetzung: "Reparatur",
          maintenance: "Wartung", wartung: "Wartung",
          service: "Service",
          beratung: "Beratung", consulting: "Beratung",
          angebot: "Angebot", offer: "Angebot"
        };
        return map[key] || raw;
      }

      // ---- Option builders (use DS only; DS is seeded from Blade or API) ----
      function buildProductOptions() {
        return (DS.products || [])
          .map(p => `<option value="${p.id}">${DS.prodName(p.id)}</option>`)
          .join("");
      }
      function buildDepartmentOptions() {
        return (DS.departments || [])
          .map(d => `<option value="${d.id}">${DS.depName(d.id)}</option>`)
          .join("");
      }
      function buildServiceOptions() {
        return (DS.services || [])
          .map(s => {
            const label = translateService(s.localized_name || s.name || s.phase_section || s.title || `Service #${s.id}`);
            return `<option value="${s.id}">${label}</option>`;
          })
          .join("");
      }

      // ---- Row injection + UI wiring ----
      function appendInquiryRow(i, row, innHtml, outHtml) {
        const safeProduct = row.product_name || DS.prodName(row.product_id);
        const safeDept    = row.department   || DS.depName(row.department_id);
        const safeService = row.service_id ? DS.svcName(row.service_id) : translateService(row.service || "");

        const tr = `
          <tr data-index="${i}">
            <td>${safeProduct}
              <input type="hidden" name="inquiries[${i}][product_id]" value="${row.product_id || ""}">
            </td>
            <td>${safeDept}
              <input type="hidden" name="inquiries[${i}][department_id]" value="${row.department_id || ""}">
            </td>
            <td>${safeService}
              <input type="hidden" name="inquiries[${i}][service_id]" value="${row.service_id || ""}">
            </td>
            <td>
              <select name="inquiries[${i}][employee_id]" class="form-control inquiry-employee-select">
                <option value="">Innendienst wählen</option>${innHtml}
              </select>
            </td>
            <td>
              <select name="inquiries[${i}][field_employee_id]" class="form-control inquiry-field-employee-select">
                <option value="">Außendienst wählen</option>${outHtml}
              </select>
            </td>
            <td class="text-center" style="width:48px;">
              <button type="button" class="btn btn-sm btn-light remove-inquiry-row" title="Zeile entfernen">✕</button>
            </td>
          </tr>`;
        $("#inquiryPreviewBody").append(tr);
      }

      function hydrateInquiryTableUI() {
        $(".inquiry-employee-select, .inquiry-field-employee-select").select2({
          width: "100%",
          placeholder: "Mitarbeiter wählen",
          allowClear: true
        });

        $("#inquiryPreviewBody")
          .off("change.inquirySync", ".inquiry-employee-select, .inquiry-field-employee-select")
          .on("change.inquirySync", ".inquiry-employee-select, .inquiry-field-employee-select", function () {
            if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
          });
      }

      function reindexInquiryRows() {
        $("#inquiryPreviewBody tr").each(function (idx) {
          $(this).attr("data-index", idx);
          $(this).find("input[name*='inquiries[']").each(function () {
            this.name = this.name.replace(/inquiries\[\d+\]/, `inquiries[${idx}]`);
          });
          $(this).find("select[name*='inquiries[']").each(function () {
            this.name = this.name.replace(/inquiries\[\d+\]/, `inquiries[${idx}]`);
          });
        });
        if (window.S) window.S.inquiryRowIndex = $("#inquiryPreviewBody tr").length;
      }

      $(document).on("click", ".remove-inquiry-row", function () {
        $(this).closest("tr").remove();
        reindexInquiryRows();
        if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
      });

      // ---- Backend fetch for Innendienst/Außendienst per product ----
      async function fetchInquiryDepartmentEmployees(selectedUids) {
        await DS.ensure();

        if (!selectedUids || !selectedUids.length) {
          $("#inquiryPreviewWrapper").addClass("d-none");
          $("#inquiryPreviewBody").empty();
          if (window.S) window.S.inquiryRowIndex = 0;
          return;
        }

        const payload = selectedUids
          .map(uid => (window.S && window.S.productMap ? window.S.productMap[uid] : null))
          .filter(Boolean)
          .map(p => ({ product_id: p.product_id, alternative_id: p.alternative_id, customer_id: p.customer_id }));

        $.ajax({
          url: window.ROUTE.inquiryDeptEmployees,
          type: "POST",
          dataType: "json",
          data: { _token: $('meta[name="csrf-token"]').attr('content') || '', products: JSON.stringify(payload) },
          success(res) {
            const rows = res.data || res || [];
            const $tb = $("#inquiryPreviewBody");
            $tb.empty();

            if (!rows.length) {
              $("#inquiryPreviewWrapper").addClass("d-none");
              if (window.S) window.S.inquiryRowIndex = 0;
              return;
            }

            rows.forEach((row, i) => {
              const inn = (row.innendienst_employees || [])
                .map(e => `<option value="${e.id}">${(e.name || "")} ${(e.lastname || "")}</option>`).join("");
              const out = (row.aussendienst_employees || [])
                .map(e => `<option value="${e.id}">${(e.name || "")} ${(e.lastname || "")}</option>`).join("");
              appendInquiryRow(i, row, inn, out);
            });

            if (window.S) window.S.inquiryRowIndex = rows.length;

            hydrateInquiryTableUI();

            if ($("#switchContact").is(':checked')) {
              $("#inquiryPreviewWrapper").removeClass('d-none');
              if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
            } else {
              $("#inquiryPreviewWrapper").addClass('d-none');
            }
          },
          error(xhr) {
            console.error("inquiry.department.employees:", xhr.responseText);
            $("#inquiryPreviewWrapper").addClass("d-none");
            $("#inquiryPreviewBody").empty();
            if (window.S) window.S.inquiryRowIndex = 0;
          }
        });
      }

      // ---- “+” button: add an empty row built from DS (no backend roundtrip) ----
      $(document).on("click", "#addInquiryRow", async function () {
        await DS.ensure();
        $("#inquiryPreviewWrapper").removeClass("d-none");

        const index = window.S ? (window.S.inquiryRowIndex = (window.S.inquiryRowIndex || 0) + 1) - 1
                              : $("#inquiryPreviewBody tr").length;

        // employees fallback: copy first row, else from #employee select
        const $tb = $("#inquiryPreviewBody");
        let inn = $tb.find("tr:first-child select.inquiry-employee-select").html() || "";
        let fld = $tb.find("tr:first-child select.inquiry-field-employee-select").html() || "";
        if (!inn || !fld) {
          const all = $("#employee option").map(function () {
            return `<option value="${this.value}">${$(this).text()}</option>`;
          }).get().join("");
          inn = inn || all;
          fld = fld || all;
        }

        // Build options from DS (seeded from Blade or API)
        const productOptions = buildProductOptions();
        const deptOptions    = buildDepartmentOptions();
        const svcOptions     = buildServiceOptions();

        const row = `
          <tr data-index="${index}">
            <td>
              <select name="inquiries[${index}][product_id]" class="form-control select2-inquiry inquiry-product">
                <option value="">Produkt wählen…</option>${productOptions}
              </select>
            </td>
            <td>
              <select name="inquiries[${index}][department_id]" class="form-control select2-inquiry inquiry-department">
                <option value="">Abteilung wählen…</option>${deptOptions}
              </select>
            </td>
            <td>
              <select name="inquiries[${index}][service_id]" class="form-control select2-inquiry inquiry-service">
                <option value="">Leistung/Service wählen…</option>${svcOptions}
              </select>
            </td>
            <td>
              <select name="inquiries[${index}][employee_id]" class="form-control inquiry-employee-select">
                <option value="">Innendienst wählen</option>${inn}
              </select>
            </td>
            <td>
              <select name="inquiries[${index}][field_employee_id]" class="form-control inquiry-field-employee-select">
                <option value="">Außendienst wählen</option>${fld}
              </select>
            </td>
            <td class="text-center" style="width:48px;">
              <button type="button" class="btn btn-sm btn-light remove-inquiry-row" title="Zeile entfernen">✕</button>
            </td>
          </tr>`;
        $("#inquiryPreviewBody").append(row);

        $(".select2-inquiry").select2({ width: "100%", placeholder: "Bitte wählen", allowClear: true });
        hydrateInquiryTableUI();
      });

      // ---- Trigger backend fetch when product multiselect (#productSelect) changes in inquiry mode ----
      $(document).on("change", "#productSelect", function () {
        if (!$('#switchContact').is(':checked')) return;
        const val = $(this).val() || [];
        if (!val.length) {
          $('#inquiryPreviewWrapper').addClass('d-none');
          $('#inquiryPreviewBody').empty();
          if (window.S) window.S.inquiryRowIndex = 0;
          return;
        }
        fetchInquiryDepartmentEmployees(val);
      });

        // ---- Prefill from backend snapshot on edit ----
        window.prefillInquiryFromSnapshot = async function (snapshot) {
          if (!Array.isArray(snapshot) || !snapshot.length) return;

          await DS.ensure();

          const $tb = $("#inquiryPreviewBody");
          $tb.empty();

          // Build generic employee option list from main Teilnehmer select
          const allEmpOptions = $("#employee option").map(function () {
            const text = $(this).text().trim();
            const val  = this.value;
            if (!val) return "";
            return `<option value="${val}">${text}</option>`;
          }).get().join("");

          snapshot.forEach((item, idx) => {
            const row = {
              product_id:    item.product_id    || item.productId,
              department_id: item.department_id || item.departmentId,
              service_id:    item.service_id    || item.serviceId,
              service:       null,
              product_name:  null,
              department:    null,
            };

            // Uses DS to resolve names
            appendInquiryRow(idx, row, allEmpOptions, allEmpOptions);

            const $row = $("#inquiryPreviewBody tr").last();
            if (item.employee_id) {
              $row.find("select.inquiry-employee-select").val(String(item.employee_id));
            }
            const fe = item.field_employee_id || item.field_employee;
            if (fe) {
              $row.find("select.inquiry-field-employee-select").val(String(fe));
            }
          });

          if (window.S) window.S.inquiryRowIndex = snapshot.length;

          hydrateInquiryTableUI();

          // Show inquiry block and sync Teilnehmer
          $("#inquiryPreviewWrapper").removeClass("d-none");
          if (window.updateParticipantsFromInquiry) {
            window.updateParticipantsFromInquiry();
          }
        };



      // Warm up DS so first click has data
      $(function () { DS.ensure(); });

      // Export (optional)
      window.DS = DS;
      window.translateService = translateService;
      window.fetchInquiryDepartmentEmployees = fetchInquiryDepartmentEmployees;
    })();
</script>

 

<script>
var authUserName = "{{ auth()->user()->name }}";
</script> 
<!-- Serach and Filter by Employee and Task Title, Date :end_date -->
<!-- moving from menu to kalender tab  -->
<script>
  $(document).ready(function() {
      // Check if the URL contains a hash
      if (window.location.hash) {
          let tabHash = window.location.hash;

          // Find the tab and activate it
          let targetTab = $(`a[href="${tabHash}"]`);
          if (targetTab.length) {
              targetTab.tab('show'); // Bootstrap's tab method to show the tab
          }
      }

      // Update the URL hash when switching tabs
      $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
          let target = $(e.target).attr("href");
          history.replaceState(null, null, target);
      });
  });
</script>

<!-- Information Popup  -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("info_popup")) {
                let infoId = event.target.getAttribute("data-id");
                let infoType = event.target.getAttribute("data-type");

                fetch(`/get/info/${infoId}/${infoType}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let detailsTable = `
                            <table style="width:100%; border-collapse: collapse;">
                                <tr><th style="text-align:left; padding:5px;">Titel</th><td>${data.title}</td></tr>
                                <tr><th style="text-align:left; padding:5px;">Beschreibung</th><td>${data.description}</td></tr>
                                ${data.execution_type ? `<tr><th style="text-align:left; padding:5px;">Ausführungstyp</th><td>${data.execution_type}</td></tr>` : ""}
                                <tr><th style="text-align:left; padding:5px;">Startdatum</th><td>${data.start_date}</td></tr>
                                <tr><th style="text-align:left; padding:5px;">Enddatum</th><td>${data.end_date}</td></tr>
                                <tr><th style="text-align:left; padding:5px;">Startzeit</th><td>${data.start_time}</td></tr>
                                <tr><th style="text-align:left; padding:5px;">Endzeit</th><td>${data.end_time}</td></tr>
                            </table>
                        `;

                            Swal.fire({
                                title: "Beschreibung",
                                html: detailsTable,
                                icon: "info",
                                confirmButtonText: "OK",
                                customClass: {
                                    popup: 'swal-wide' // Optional: CSS class to widen the modal
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: data.message,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching event info:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    });
            }
        });
    });
</script>
<!-- Information Popup: end  -->

<!-- show map:  -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("show_map")) {
                let appointmentId = event.target.getAttribute("data-id");

                // Show loading dialog
                Swal.fire({
                    title: "Fetching Location...",
                    text: "Please wait while we load the map...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/get/map/${appointmentId}`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let destination = {
                                lat: parseFloat(data.latitude),
                                lng: parseFloat(data.longitude)
                            };

                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    let origin = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude,
                                    };

                                    // Once the location is retrieved, show the map
                                    showMapWithRoute(origin, destination, data.title);
                                }, function() {
                                    Swal.fire("Error", "Could not get your location.", "error");
                                });
                            } else {
                                Swal.fire("Error", "Geolocation is not supported by your browser.",
                                    "error");
                            }
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching map data:", error);
                        Swal.fire("Error", "Something went wrong. Please try again.", "error");
                    });
            }
        });
    });

    // Function to show the map with route and open Google Maps button
    function showMapWithRoute(origin, destination, locationTitle) {
        let googleMapsAPIKey = "AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo"; // Replace with your Google API Key
        let mapContainer = document.createElement("div");
        mapContainer.id = "map";
        mapContainer.style = "width: 100%; height: 400px; margin-top: 10px;";

        // Replace loading message with actual map
        Swal.fire({
            title: `Termin: ${locationTitle}`,
            html: `<div id="map" style="width: 100%; height: 400px;"></div>
                    <p><strong>Distance:</strong> <span id="distance"></span></p>
                    <p><strong>Estimated Time:</strong> <span id="duration"></span></p>
                    <a href="https://www.google.com/maps/dir/?api=1&origin=${origin.lat},${origin.lng}&destination=${destination.lat},${destination.lng}&travelmode=driving"
                        target="_blank" class="swal2-confirm swal2-styled">Open in Google Maps</a>`,
            icon: "info",
            didOpen: () => {
                let map = new google.maps.Map(document.getElementById("map"), {
                    center: origin,
                    zoom: 10,
                });

                let directionsService = new google.maps.DirectionsService();
                let directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                directionsService.route({
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING,
                    },
                    function(response, status) {
                        if (status === "OK") {
                            directionsRenderer.setDirections(response);
                            let route = response.routes[0].legs[0];

                            document.getElementById("distance").textContent = route.distance.text;
                            document.getElementById("duration").textContent = route.duration.text;
                        } else {
                            console.error("Directions request failed due to " + status);
                            Swal.fire("Error", "Could not get directions.", "error");
                        }
                    }
                );
            },
            width: 600,
            showCancelButton: false,
            showConfirmButton: false,
        });
    }
</script> 
<!-- show map end  -->  
<!-- script for hidding the day and month drop down:  -->

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const startDateInput = document.getElementById("start_date");
      const weekSelect = document.getElementById("week_select");
      const weekDropdownContainer = document.getElementById("week_dropdown_container");
      const dateType = document.getElementById("date_type");

      function getWeekNumber(date) {
          const tempDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
          const dayNum = tempDate.getUTCDay() || 7;
          tempDate.setUTCDate(tempDate.getUTCDate() + 4 - dayNum);
          const yearStart = new Date(Date.UTC(tempDate.getUTCFullYear(), 0, 1));
          return Math.ceil((((tempDate - yearStart) / 86400000) + 1) / 7);
      }

      function updateWeekDropdown() {
          const startDate = new Date(startDateInput.value);
          if (isNaN(startDate)) return;

          const currentWeek = getWeekNumber(startDate);
          const totalWeeks = 52;

          // Clear old options
          weekSelect.innerHTML = "";

          for (let i = currentWeek; i <= totalWeeks; i++) {
              const option = document.createElement("option");
              option.value = i;
              option.textContent = `Woche ${i}`;
              weekSelect.appendChild(option);
          }

          // Reinitialize Select2 for weekSelect (in case it was used)
          $('#week_select').select2({
              placeholder: "Wähle Woche(n)",
              allowClear: true
          });

          weekDropdownContainer.style.display = "block";
      }

      function toggleFields() {
          const selectedValue = $("#date_type").val();

          $(".from_day, .to_day, .from_month, .to_month").hide();
          $("#week_dropdown_container").hide();

          if (selectedValue === "daily") {
              $(".from_day, .to_day").show();
          } else if (selectedValue === "monthly") {
              $(".from_month, .to_month").show();
          } else if (selectedValue === "weekly") {
              if (startDateInput.value) {
                  updateWeekDropdown();
              }
          }
      }

      // Setup event listeners
      $("#date_type").on("change", toggleFields);
      $("#start_date").on("change", function() {
          if ($("#date_type").val() === "weekly") {
              updateWeekDropdown();
          }
      });

      // Initial setup
      toggleFields();
  });
</script> 
<!-- Color: start  -->
<script>
  $(document).ready(function() {
      $('#color-select').select2({
          templateResult: formatColor,
          templateSelection: formatColor,
          escapeMarkup: function(markup) {
              return markup;
          }
      });

      function formatColor(color) {
          if (!color.id) {
              return color.text;
          }

          var colorValue = $(color.element).data('color');
          var colorName = color.text;

          var markup = `
              <div style="display: flex; align-items: center;">
                  <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                  <span>${colorName}</span>
              </div>
          `;

          return markup;
      }
  });
</script> 
<!-- moving from menu to kalender tab  -->
<script>
  $(document).ready(function() {
      // Check if the URL contains a hash
      if (window.location.hash) {
          let tabHash = window.location.hash;

          // Find the tab and activate it
          let targetTab = $(`a[href="${tabHash}"]`);
          if (targetTab.length) {
              targetTab.tab('show'); // Bootstrap's tab method to show the tab
          }
      }

      // Update the URL hash when switching tabs
      $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
          let target = $(e.target).attr("href");
          history.replaceState(null, null, target);
      });
  });
</script>

<!-- Dupllicate: start  -->
<script>
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $(document).on("click", ".duplicate-event", function(e) {
      e.preventDefault();
      const eventId = $(this).data("event-id");

      Swal.fire({
          title: "Duplizieren auf neues Datum",
          input: "date",
          inputLabel: "Wähle ein Datum",
          inputAttributes: {
              min: new Date().toISOString().split("T")[0]
          },
          showCancelButton: true,
          confirmButtonText: "Duplizieren",
          cancelButtonText: "Abbrechen",
          inputValidator: (value) => {
              if (!value) {
                  return "Datum ist erforderlich!";
              }
          }
      }).then((result) => {
          if (result.isConfirmed) {
              const selectedDate = result.value;

              $.ajax({
                  url: "{{ route('appointment.duplicate') }}",
                  method: "POST",
                  data: {
                      appointment_id: eventId,
                      new_date: selectedDate
                  },
                  success: function(response) {
                      Swal.fire("Erfolgreich!", response.message, "success").then(() => {
                          loadCalendarTasks(() => {
                              calendar.gotoDate(response.data
                              .start_date); // optional: scroll to new event
                          });
                      });
                  },
                  error: function(xhr) {
                      console.log(xhr.responseJSON);
                      if (xhr.status === 422) {
                          let errors = xhr.responseJSON.errors;
                          let errorMessages = Object.values(errors).map(errArr => errArr.join(
                              ', ')).join('<br>');
                          Swal.fire("Validierungsfehler", errorMessages, "error");
                      } else {
                          Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
                      }
                  }
              });

          }
      });
  });
</script>

<!-- Dupllicate: end  --> 
<!-- Menu Close and Open Button: start  -->
<script>
  $(document).ready(function() {
      // Show the .new_task when the "Erstellen" button is clicked
      $('.create_new_task').on('click', function() {
          $('.new_task').css({
              right: '-100%', // Start offscreen (adjust based on your layout)
              display: 'block', // Ensure it's visible
          }).animate({
              right: '0', // Slide into view
          }, 500); // Animation duration in ms
      });

      // Hide the .new_task when the "abbrechen" button is clicked
      $('.new_task').on('click', '.close_task_window', function() {
          $('.new_task').animate({
              right: '-100%', // Slide out of view
          }, 500, function() {
              $(this).hide(); // Hide after animation completes
          });
      });
  });
</script>

<script>
  document.addEventListener("keydown", function(event) {
      const newTaskDiv = document.querySelector(".new_task");

      if (event.key === "Escape" && newTaskDiv.style.display === "block") {
          newTaskDiv.style.display = "none"; // Hide the new_task div
      }
  });
</script>
<!-- Menu Close and Open Button: end  -->
 
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
<!-- showing online Link:  -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const appointmentTypeDropdown = document.getElementById("execution_type");
      const internDiv = document.getElementById("intern");
      const externDiv = document.getElementById("extern");
      const linkDiv = document.getElementById("link_section");
      const branchSelect = document.querySelector("[name='branch_address_id']");
      const externInput = document.getElementById("full_address");

      function toggleSections() {
          const appointmentType = appointmentTypeDropdown.value;

          internDiv.style.display = "none";
          externDiv.style.display = "none";
          linkDiv.style.display = "none";

          resetHiddenInputs();

          if (appointmentType === "internal") {
              internDiv.style.display = "block";
              branchSelect.value = "";
          } else if (appointmentType === "external") {
              externDiv.style.display = "block";
          } else if (appointmentType === "online") {
              linkDiv.style.display = "block";
          } else if (appointmentType === "telephone") {
              // Do nothing for telephone appointments
          } else {
              externDiv.style.display = "block"; // Default to external
          }
      }

      function populateInternalAddress() {
          const selectedOption = branchSelect.options[branchSelect.selectedIndex];

          if (!selectedOption || !selectedOption.value) {
              resetHiddenInputs();
              return;
          }

          document.getElementById("full_address").value = selectedOption.innerText;
          document.getElementById("street-input").value = selectedOption.getAttribute("data-street") || "";
          document.getElementById("city-input").value = selectedOption.getAttribute("data-city") || "";
          document.getElementById("postal_code-input").value = selectedOption.getAttribute("data-postcode") || "";
          document.getElementById("latitude-input").value = selectedOption.getAttribute("data-latitude") || "";
          document.getElementById("longitude-input").value = selectedOption.getAttribute("data-longitude") || "";
      }

      function resetHiddenInputs() {
          document.getElementById("full_address").value = "";
          document.getElementById("street-input").value = "";
          document.getElementById("city-input").value = "";
          document.getElementById("postal_code-input").value = "";
          document.getElementById("latitude-input").value = "";
          document.getElementById("longitude-input").value = "";
      }

      // Ensure initializeAutocomplete is globally accessible
      window.initializeAutocomplete = function() {
          if (!externInput) return;

          const autocomplete = new google.maps.places.Autocomplete(externInput, {
              types: ['geocode'],
              componentRestrictions: {
                  country: 'DE'
              }
          });

          autocomplete.addListener('place_changed', () => {
              const place = autocomplete.getPlace();

              if (!place.geometry) {
                  console.error("No details available for input: '" + place.name + "'");
                  return;
              }

              let street = "",
                  city = "",
                  postalCode = "",
                  latitude = "",
                  longitude = "";

              place.address_components.forEach(component => {
                  const types = component.types;

                  if (types.includes("route")) {
                      street = component.long_name;
                  }
                  if (types.includes("locality") || types.includes("sublocality")) {
                      city = component.long_name;
                  }
                  if (types.includes("postal_code")) {
                      postalCode = component.long_name;
                  }
              });

              latitude = place.geometry.location.lat();
              longitude = place.geometry.location.lng();

              // Populate inputs with external address data
              document.getElementById("street-input").value = street;
              document.getElementById("city-input").value = city;
              document.getElementById("postal_code-input").value = postalCode;
              document.getElementById("latitude-input").value = latitude;
              document.getElementById("longitude-input").value = longitude;
          });
      };

      function loadGoogleMapsAPI() {
          if (!window.google || !window.google.maps) {
              const script = document.createElement("script");
              script.src =
                  "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places";
              script.async = true;
              script.defer = true;
              script.onload = function() {
                  initializeAutocomplete();
              };
              document.head.appendChild(script);
          } else {
              initializeAutocomplete();
          }
      }

      appointmentTypeDropdown.addEventListener("change", toggleSections);
      branchSelect.addEventListener("change", populateInternalAddress);

      toggleSections();
      loadGoogleMapsAPI();
  });
</script> 
<!-- Start Date and End date same value  -->

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const startDateInput = document.getElementById("start_date");
      const endDateInput = document.getElementById("end_date");

      function setEndDate() {
          if (!startDateInput.value) return; // If no start date, do nothing
          endDateInput.value = startDateInput.value; // Set end date to match start date
      }

      // Event listener to update end date when start date changes
      startDateInput.addEventListener("input", setEndDate);

      // Set default value on page load (if start date is already set)
      setEndDate();
  });
</script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const startDateInput = document.getElementById("start_date");
    const startTimeInput = document.getElementById("start_time");
    const endTimeInput = document.getElementById("end_time");
    const totalTimeInput = document.getElementById("total_time");
    const endDateInput = document.getElementById("end_date");
    const dateTypeInput = $("#date_type"); // Select2 uses jQuery selector

    // Function to set default working hours when selecting "Whole Day"
    function setWholeDayTime() {
        if (dateTypeInput.val() === "day") {
            startTimeInput.value = "08:00";
            endTimeInput.value = "16:00";
            totalTimeInput.value = 8; // 8 hours total
        }
    }

    // Function to set total_time to 8 hours when start_date is selected
    function setDefaultTotalTime() {
        if (startDateInput.value) {
            totalTimeInput.value = 8; // Default 8 hours
            endDateInput.value = startDateInput.value; // Set end_date same as start_date
        }
    }

    // Function to calculate time difference in hours
    function calculateTotalTime() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!startTime || !endTime) return;

        // Convert time to Date objects for calculation
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        // Ensure end time is after start time
        if (end < start) {
            showAlert("Fehler", "Endzeit muss nach der Startzeit liegen.", "error");
            endTimeInput.value = ""; // Reset end time
            return;
        }

        // Calculate difference in hours
        const diffInMs = end - start;
        const diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours

        totalTimeInput.value = diffInHours.toFixed(2); // Display in hours

        // Validate if time is within working hours (06:00 - 19:00)
        const startHour = start.getHours();
        const endHour = end.getHours();

        if (startHour < 6 || startHour >= 19 || endHour < 6 || endHour >= 19) {
            showAlert(
                "Achtung!",
                "Ihre gewählte Zeit liegt außerhalb der Arbeitszeit (06:00 - 19:00 Uhr).",
                "warning"
            );
        }
    }

    // Function to show SweetAlert2 alerts
    function showAlert(title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: "OK"
        });
    }

    // Event Listeners
    startDateInput.addEventListener("change", setDefaultTotalTime);
    startTimeInput.addEventListener("change", calculateTotalTime);
    endTimeInput.addEventListener("change", calculateTotalTime);



    // Initialize values on page load
    setDefaultTotalTime();
});
</script>

<!-- Start Date and End date same value : start -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");

    function setEndDate() {
        if (!startDateInput.value) return; // If no start date, do nothing
        endDateInput.value = startDateInput.value; // Set end date to match start date
    }

    // Event listener to update end date when start date changes
    startDateInput.addEventListener("input", setEndDate);

    // Set default value on page load (if start date is already set)
    setEndDate();
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
<script>
$(document).ready(function() {
    $('#next_step').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true,
        tags:true
    });

    $('#report_responsible').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true
    });

     
});
</script> 


<script>
(function(){
  // === Data sources (adjust routes if needed) ===============================
  // Server should return:
  //  GET /picker/employees?search= -> [{id, name, lastname, image}]
  //  GET /picker/teams?search=     -> [{id, name}]
  //  GET /picker/teams/{id}        -> {id, name, members: [{id, name, lastname, image, position}]}

  const ROUTES = {
    employees: "{{ route('picker.employees') }}",   // expects ?search=
    teams: "{{ route('picker.teams') }}",           // expects ?search=
    teamMembers: (id) => "{{ url('/picker/teams') }}/" + id
  };

  // Fallback: if you already have employees in blade, you can inline:
  
  const BOOT_EMPLOYEES = null; // leave null to use AJAX

  // Refs
  const $modal = $('#pickerModal');
  const $openBtn = $('#openPickerBtn');
  const $applyAll = $('#pickerApplyAll');

  const $empSearch = $('#pickerEmployeeSearch');
  const $empGrid   = $('#pickerEmployeeGrid');

  const $teamSearch = $('#pickerTeamSearch');
  const $teamList   = $('#pickerTeamList');
  const $teamMembers= $('#pickerTeamMembers');
  const $teamTitle  = $('#pickerTeamTitle');
  const $teamSelectAll = $('#pickerSelectAllTeam');
  const $teamClear  = $('#pickerClearTeam');
  const $teamApply  = $('#pickerApplyTeam');

  // State
    window.selectedEmployeeIds = new Set( ($('#employee').val() || []).map(String) );
  let currentTeamId = null;
  let currentTeamMembers = []; // {id, name, lastname, image, position}

  // Utils
  const imgUrl = (img) => img ? `/images/employee/${img}` : `/images/employee/default.png`;
  const fullName = (e) => [e.name, e.lastname].filter(Boolean).join(' ');
  const posText = (pos) => pos ? ` — ${pos}` : '';

  function toggleId(set, id) {
    id = String(id);
    if (set.has(id)) set.delete(id); else set.add(id);
  }

  // === Employees Tab ========================================================

  function renderEmployeeGrid(list) {
    $empGrid.empty();
    if (!list || !list.length) {
      $empGrid.html('<div class="text-muted p-2">Keine Ergebnisse.</div>');
      return;
    }
    list.forEach(e => {
      const id = String(e.id);
      const active = window.selectedEmployeeIds.has(id) ? 'active' : ''; 
      const $chip = $(`
        <div class="picker-chip ${active}" data-id="${id}" title="${fullName(e)}">
          <img src="${imgUrl(e.image)}" class="picker-avatar" alt="">
          <span style="font-size:12px; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            ${fullName(e)}
          </span>
        </div>
      `);
      $chip.on('click', () => {
        toggleId(window.selectedEmployeeIds, id); 
        $chip.toggleClass('active', selectedEmployeeIds.has(id));
      });
      $empGrid.append($chip);
    });
  }

  async function loadEmployees(search='') {
    if (BOOT_EMPLOYEES && !search) return renderEmployeeGrid(BOOT_EMPLOYEES);
    const url = new URL(ROUTES.employees, window.location.origin);
    if (search) url.searchParams.set('search', search);
    const res = await fetch(url.toString());
    const json = await res.json();
    renderEmployeeGrid(json.data || []);
  }

  // === Teams Tab ============================================================

  function renderTeamList(list) {
    $teamList.empty();
    if (!list || !list.length) {
      $teamList.html('<div class="text-muted p-2">Keine Teams gefunden.</div>');
      return;
    }
    list.forEach(t => {
      const $item = $(`<div class="picker-list-item" data-id="${t.id}">${t.name}</div>`);
      $item.on('click', () => selectTeam(t.id, t.name));
      $teamList.append($item);
    });
  }

  function renderTeamMembers(members) {
    $teamMembers.empty();
    if (!members || !members.length) {
      $teamMembers.html('<div class="text-muted p-2">Keine Mitglieder.</div>');
      return;
    }
    const $wrap = $('<div class="d-flex flex-wrap" style="gap:8px;"></div>');
    members.forEach(m => {
      const id = String(m.id);
    const active = window.selectedEmployeeIds.has(id) ? 'active' : '';
      const $chip = $(`
        <div class="picker-chip ${active}" data-id="${id}" title="${fullName(m)}${posText(m.position)}">
          <img src="${imgUrl(m.image)}" class="picker-avatar" alt="">
          <div style="display:flex;flex-direction:column;line-height:1;">
            <span style="font-size:12px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${fullName(m)}</span>
            <small class="text-muted" style="font-size:10px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.position || ''}</small>
          </div>
        </div>
      `);
      $chip.on('click', () => {
        toggleId(window.selectedEmployeeIds, id); 
        $chip.toggleClass('active', selectedEmployeeIds.has(id));
      });
      $wrap.append($chip);
    });
    $teamMembers.append($wrap);
  }

  async function loadTeams(search='') {
    const url = new URL(ROUTES.teams, window.location.origin);
    if (search) url.searchParams.set('search', search);
    const res = await fetch(url.toString());
    const json = await res.json();
    renderTeamList(json.data || []);
  }

  async function selectTeam(id, name='Team') {
    currentTeamId = id;
    $teamTitle.text(name);
    const res = await fetch(ROUTES.teamMembers(id));
    const json = await res.json();
    currentTeamMembers = (json.members || []).map(m => ({
      id: m.id,
      name: m.name,
      lastname: m.lastname,
      image: m.image,
      position: m.position || (m.pivot && m.pivot.position) || null
    }));
    renderTeamMembers(currentTeamMembers);
  }

  // === Apply to #employee and allow edit afterwards =========================

  function ensureOptionInSelect2(id, text, image) {
    const $sel = $('#employee');
    const exists = $sel.find(`option[value="${id}"]`).length > 0;
    if (!exists) {
      const opt = new Option(text, id, true, true);
      $(opt).attr('data-image', imgUrl(image));
      $sel.append(opt);
    }
  }

  function applySelectionToEmployeeSelect() {
    const ids = Array.from(window.selectedEmployeeIds); 
    // Ensure options exist
    // If you have an endpoint to resolve names by IDs, use it; otherwise we trust Select2 existing options
    ids.forEach(id => {
      // If option missing, create a generic label; your formatEmployee renderer shows avatar anyway
      if ($('#employee').find(`option[value="${id}"]`).length === 0) {
        ensureOptionInSelect2(id, `ID ${id}`, null);
      }
    });
    $('#employee').val(ids).trigger('change');
  }

  // === Wire up ==============================================================
  $openBtn.on('click', async () => {
    // Sync current selection from Select2 to chips
    selectedEmployeeIds = new Set( ($('#employee').val() || []).map(String) );

    // Default load Employees tab + Teams list
    await Promise.all([loadEmployees(''), loadTeams('')]);
    // If you want a default team selected, call selectTeam(firstId)
    $modal.modal('show');
  });

  // Search fields
  let empTimer = null;
  $empSearch.on('input', (e) => {
    clearTimeout(empTimer);
    empTimer = setTimeout(()=> loadEmployees(e.target.value.trim()), 250);
  });

  let teamTimer = null;
  $teamSearch.on('input', (e) => {
    clearTimeout(teamTimer);
    teamTimer = setTimeout(()=> loadTeams(e.target.value.trim()), 250);
  });

  // Team actions
  $teamSelectAll.on('click', () => {
    currentTeamMembers.forEach(m => selectedEmployeeIds.add(String(m.id)));
    renderTeamMembers(currentTeamMembers);
  });
  $teamClear.on('click', () => {
    currentTeamMembers.forEach(m => selectedEmployeeIds.delete(String(m.id)));
    renderTeamMembers(currentTeamMembers);
  });
  $teamApply.on('click', () => {
    // Ensure team members exist as options with their names + avatars
    currentTeamMembers.forEach(m => {
      ensureOptionInSelect2(String(m.id), fullName(m), m.image);
      selectedEmployeeIds.add(String(m.id));
    });
    applySelectionToEmployeeSelect();
    // Keep modal open so user can switch teams; or close if you prefer:
    // $modal.modal('hide');
  });

  // Apply all (from both tabs)
  $applyAll.on('click', () => {
    applySelectionToEmployeeSelect();
    $modal.modal('hide');
  });

})();
</script>
{{-- FullCalendar CSS/JS (v5) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calEl = document.getElementById('inquiry-mini-calendar');
  let calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'timeGridWeek',
    locale: 'de',
    firstDay: 1,
    slotMinTime: '07:00:00',
    slotMaxTime: '21:00:00',
    allDaySlot: false,
    height: 420,
    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
    initialDate: new Date(),
    events: [] // we load programmatically
  });
  calendar.render();

  // --- helpers ---
  function gatherSelection() {
    const internal = new Set();
    const external = new Set();
    const dates = [];

    $('#inquiryProductTable tbody tr').each(function(){
      const idx = $(this).data('index');
      const inVal = $(`.employee-select[data-index="${idx}"]`).val();
      const outVal = $(`.field-employee-select[data-index="${idx}"]`).val();
      const dtVal = $(`.termin-input[data-index="${idx}"]`).val(); // datetime-local

      if (inVal && !isNaN(inVal)) internal.add(parseInt(inVal,10));
      if (outVal && !isNaN(outVal)) external.add(parseInt(outVal,10));
      if (dtVal) {
        const d = dtVal.split('T')[0];
        if (d) dates.push(d);
      }
    });

    let anchorDate = (dates.length ? dates.sort()[0] : new Date().toISOString().slice(0,10));
    return {
      internal_ids: Array.from(internal),
      external_ids: Array.from(external),
      date: anchorDate
    };
  }

  // --- debounced refresher with stale-response guard ---
  let lastAnchor = null;
  let requestSeq = 0;   // increment per request
  let pendingSeq = 0;   // last request we care about

  const debounce = (fn, ms) => {
    let t; 
    return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), ms); };
  };

  const refreshCalendar = debounce(function(){
    const sel = gatherSelection();

    // Move calendar to correct week only if anchor changed
    if (sel.date !== lastAnchor) {
      lastAnchor = sel.date;
      calendar.gotoDate(sel.date);
    }

    calendar.removeAllEvents();

    // Nothing selected? show empty week (no fetch)
    if (!sel.internal_ids.length && !sel.external_ids.length) return;

    // Build URL with params
    const params = new URLSearchParams();
    sel.internal_ids.forEach(id => params.append('internal_ids[]', id));
    sel.external_ids.forEach(id => params.append('external_ids[]', id));
    params.append('date', sel.date);

    // Mark this request as the newest we care about
    const mySeq = ++requestSeq;
    pendingSeq = mySeq;

    $.getJSON('{{ route("inquiries.calendar.availability") }}?' + params.toString())
      .done(function(resp){
        // Ignore stale responses
        if (mySeq !== pendingSeq) return;

        (resp.events || []).forEach(ev => calendar.addEvent(ev));
        if (resp.weekStart) calendar.gotoDate(resp.weekStart);
      })
      .fail(function(xhr){
        // Ignore aborts / network hiccups
        if (xhr && xhr.statusText === 'abort') return;
        console.error('Calendar fetch error', xhr?.status, xhr?.responseText || xhr);
        if (window.toastr) toastr.error('Kalender konnte nicht geladen werden.');
      });
  }, 250);

  // --- bindings ---
  $(document).on('change', '.employee-select, .field-employee-select, .termin-input', refreshCalendar);
  $(document).on('click', '#addRow', () => setTimeout(refreshCalendar, 200));

  // first paint
  setTimeout(refreshCalendar, 300);
});
</script>

@endsection