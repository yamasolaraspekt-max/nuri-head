@extends('admin.layouts.app')

@section('title') PROJEKT @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

 <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<link rel="stylesheet" href="{{ asset('css/project-sider.css') }}" />
<link rel="stylesheet" href="{{ asset('css/comment-sider.css') }}" />
<link rel="stylesheet" href="{{ asset('css/project-file-sider.css') }}" />
<link rel="stylesheet" href="{{ asset('css/project-karban.css')}}" />
<link rel="stylesheet" href="{{ asset('css/project-comment.css')}}" />
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<meta name="current-employee-id" content="{{ auth()->user()->name }}">

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
   <script src="https://cdn.tailwindcss.com"></script>


 <style>
    .circle {
      width: 35px;
      height: 35px;
      background-color: #7DC242;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .line {
         width: 9px;
            height: 4px;
            background-color: #7DC242;
            margin-left: -3px;
            margin-right: -2px;
            position: relative;
            top: 2px;
    }
    .profile {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7DC242;
    }

    .profile-s {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f4a459;
    }
    .profile-r {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ea5455;
    }
    .text {
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
    }


    .modal {
            z-index: 1050 !important;
        }

    
        .modal-backdrop{
            display:none !important;
        }

        body.modal-open {
            overflow: hidden;
        }

 
        body {
        touch-action: pan-y;
        }
        .card {
        user-select: none;
        }
        .progress-bar {
        height: 40px;
        background-color: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        }
        .progress-fill {
        height: 100%;
        background-color: #4ade80;
        transition: width 0.3s ease, background-color 0.3s ease;
        }


        .project-head-div {
            display: flex !important;
            align-items: center;
        }


  </style>



    <style>
    .timeline-log-panel {
        position: fixed;
        top: 0;
        right: -100%;
        width: 89%;
        height: 100%;
        background-color: #f9fafb;
        box-shadow: -3px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        transition: right 0.4s ease-in-out;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        overflow-y: auto;
    }

    .timeline-log-panel.active {
        right: 0;
    }


.timeline-header {
    background: #aacd57;
    color: white;
    font-weight: 500;
    border-bottom: 1px solid #e9e9e9;
    backdrop-filter: blur(6px);
}

.timeline-filter select,
.timeline-filter input {
    max-width: 150px;
    margin-right: 5px;
}

.timeline-entry {
    border-left: 4px solid #2c3e50;
    background: white;
    padding: 12px 16px;
    margin-bottom: 12px;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 0 6px rgba(0,0,0,0.04);
}

.timeline-entry::before {
    content: '';
    position: absolute;
    left: -9px;
    top: 12px;
    width: 12px;
    height: 12px;
    background: #aacd57;
    border: 2px solid white;
    border-radius: 50%;
    z-index: 1;
}

.timeline-entry strong {
    font-size: 14px;
    color: #333;
}

.timeline-entry i {
    color: #555;
    margin-right: 4px;
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
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">PROJEKT</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                        <li class="breadcrumb-item active"><a  >Liste</a></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="content-body">
                                <section id="basic-tabs-components">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="cards overflow-hidden">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Kanban</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Liste</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" aria-controls="about" role="tab" aria-selected="false">Kalendar</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                <section>
                                                                    <div class="col-12">
                                                                        <div class="row">
                                                                            <div class="col-md-6 col-12 mb-1">
                                                                                <form id="kanbanSearchForm">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <input type="text" class="form-control" id="searchInput" placeholder="Geben Sie die Details Ihrer Suche ein" name="search">
                                                                                            <div class="input-group-append">
                                                                                                <button class="btn btn-primary waves-effect waves-light" type="button" id="searchButton">
                                                                                                    <i class="feather icon-search"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </form>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 mb-1">   </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="kanban-container" id="kanban">
                                                                                <div class="column" id="new" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>NEW</h3>
                                                                                </div>
                                                                                <div class="column" id="plan" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Planung</h3>
                                                                                </div>
                                                                                <div class="column" id="process" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Prozess</h3>
                                                                                </div>
                                                                                <div class="column" id="completed" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Abgeschlossen</h3>
                                                                                </div>
                                                                                <div class="column" id="junk" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Junk</h3>
                                                                                </div>
                                                                                <div class="column" id="pause" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Pause </h3>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </section>
                                                            </div>
                                                            <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                                <section id="project_list"></section>
                                                            </div>
                                                            
                                                            <div class="tab-pane" id="dropdown32" role="tabpanel" aria-labelledby="dropdown32-tab" aria-expanded="false">
                                                                <p>Chocolate croissant cupcake croissant jelly donut. Cheesecake toffee apple pie chocolate bar biscuit
                                                                    tart croissant. Lemon drops danish cookie. Oat cake macaroon icing tart lollipop cookie sweet bear
                                                                    claw.</p>
                                                            </div>
                                                            <div class="tab-pane" id="about" aria-labelledby="about-tab" role="tabpanel">
                                                                <p>Carrot cake dragée chocolate. Lemon drops ice cream wafer gummies dragée. Chocolate bar liquorice
                                                                    cheesecake cookie chupa chups marshmallow oat cake biscuit. Dessert toffee fruitcake ice cream
                                                                    powder
                                                                    tootsie roll cake.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 

        <div class="project-profile-overlay" onclick="closeSidebar()"></div>
        <div id="project-profile" class="project-profile">
            <div class="project-profile-header">
                <h2 id="customer_name"></h2>
                <div class="header-buttons">
                <button class="maximize-btn" onclick="toggleMaximizeSidebar()">&#x26F6;</button>
                <button class="close-btn" onclick="closeSidebar()">&times;</button>
                </div>
            </div>

            <div class="project-meta">
                <p><strong>Status:</strong> <span class="badge not-started" id="customer_status">Backlog</span></p>
                <p id="customer-product"><strong>Projekt-Address:</strong></p>
                <p id="contact-peeople"><strong>Ansprechtpartner:</strong></p>
                <span id="request_date"></span>

                <!-- Add button for Projektleiter  -->
                <div id="project-leader-display " class="d-flex">
                     <p class="mr-1"><strong>Projektleiter: </strong>
                        <div class="project-head-div mr-1 ">
                            <img id="project-leader-image" src="{{ asset('images/gender/male.png') }}" height="25" width="25" class="rounded-circle">
                            <span id="project-leader-name">-</span>
                        </div>
                    </p>
                        <button class="btn btn-icon btn-icon rounded-circle btn-flat-success ml-1 waves-effect waves-light " onclick="openProjectLeaderModal()"><i class="feather icon-plus"></i></button>

                </div>



                <p id="project-employee"><strong>Beteiligtepersonen:</strong></p>
                <p id="project-product"><strong>Produkt:</strong></p>
            </div>
            <div class="task-checklist" id="checklistSelectWrapper" style="display: none;">
                <select name="checklist" id="checklistSelect" class="form-control">
                    <option value=""></option>
                </select>
            </div>
            <div class="task-list" id="accordion-tasks">
                <h3>Aufgaben</h3>
            </div>



        <div class="task-list">
            <!-- Tabs -->
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <button onclick="showTab('list')" id="tab-list" class="tab-button">
                        <i class="feather icon-list"></i> Timeline
                    </button>
               
                </div>
 

             <div id="list-view" class="tab-view">
                <div style="display: flex; gap: 20px;">
                    <!-- Not started column -->
                    <div class="task-cards">
                        <strong class="badge not-started">Not started</strong>
                        <div class="task">Task</div>
                        <div class="task">Task2<br><small>Test Sadid</small></div>
                    </div>
                </div>
            </div>

            <!-- List View -->
             
 
        </div>
<!-- END: Content-->

 
            <!-- Modal for Adding Employee -->
                       
        <div class="modal fade text-left" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter hinzufügen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form  method="post" id="add_employe_form" action="{{ route('add.employee.to.project') }}">
                        @csrf
                        <input type="hidden" name="project_id" id="modal_project_id" value="">
                        <input type="hidden" name="old_employee" id="modal_old_employee" value="">
                        <input type="hidden" name="phase_id" id="modal_phase_id" value="">
                        <input type="hidden" name="activity_id" id="modal_activity_id" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id[]" id="employee_id_select" class="form-control employee" style="width: 100%;" multiple>
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}"
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                                <option value="controller">Kontroller</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="save-add-employee">Hinzufügen</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
            

        <!-- Change Employee  -->
        <div class="modal fade text-left" id="change_employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter ändern</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('update.employee.project') }}" method="post" id="change_employee_form">
                        @csrf
                        <input type="hidden" name="project_id" id="change_project_id" value="">
                        <input type="hidden" name="phase_id" id="change_phase_id" value="">
                        <input type="hidden" name="activity_id" id="change_activity_id" value="">
                        <input type="hidden" name="old_employee" id="change_old_employee" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id" id="employee_id" class="form-control employee" style="width: 100%;">
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}"
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact person -->
         
        <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="contactPeopleForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Neuer Ansprechpartner</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="customer_id" id="customer_id">
                            <input type="hidden" name="alternative_id" id="alternative_id">

                            <div class="form-group">
                                <label>Vorname</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Nachname</label>
                                <input type="text" name="lastname" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Beziehung</label>
                                <select name="relation" id="relation" class="form-control" required>
                                    <option value="">Bitte wählen</option>
                                    <option value="Vater">Vater</option>
                                    <option value="Mutter">Mutter</option>
                                    <option value="Bruder">Bruder</option>
                                    <option value="Schwester">Schwester</option>
                                    <option value="Tochter">Tochter</option>
                                    <option value="Sohn">Sohn</option>
                                    <option value="Onkel">Onkel</option>
                                    <option value="Tante">Tante</option>
                                    <option value="Freund">Freund</option>
                                    <option value="Andere">Andere</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Telefon</label>
                                <input type="text" name="phone" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Büro</label>
                                <input type="text" name="office" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Zuhause</label>
                                <input type="text" name="home" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>E-Mail</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Speichern</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



           <!-- Project Leader Modal -->
            <div class="modal fade" id="projectLeaderModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="projectLeaderForm" action="/project/assign-leader" method="POST">
                        @csrf
                    <input type="hidden" name="project_id" id="leader_project_id">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Projektleiter zuweisen</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        </div>
                        <div class="modal-body">
                        <select name="project_leader_id" id="project_leader_select" class="form-control"></select>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-primary save-person-leader">Speichern</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
 
            <!-- Comment Panel -->
            <div id="comment-panel" class="fixed top-0 right-0 w-[73vw] h-full bg-white shadow-2xl z-50 p-6 overflow-y-auto hidden border-l border-gray-300 transition-all duration-300 ease-in-out">
                <div class="flex justify-between items-center border-b pb-2 mb-3">
                    <h3 class="text-xl font-semibold">Kommentare</h3>
                    <button onclick="closeCommentLayout()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
                </div>

                <!-- Comments Body -->
              <!-- Search input -->
                <div class="mb-4">
                    <input type="text" id="comment-search" placeholder="🔍 Kommentar suchen..."
                        class="w-full px-2 border rounded shadow-sm focus:ring-2 focus:ring-blue-200"
                        oninput="filterComments()" />
                </div>

                <!-- Comments will be rendered here -->
                <div id="comment-body" class="space-y-3 mb-4">
                    <!-- Injected by JS -->
                </div>


                <!-- Comment Input -->
                <div class="mt-4">
                    <div id="comment-editor" class="h-40 mb-4 bg-white border border-gray-300 rounded-lg shadow-sm px-2"></div>
                    <button onclick="submitCommentFromInput()" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"> <i class="feather icon-mail" ></i>Kommentar abschicken</button>
                </div>
            </div>


 
          <!-- File Manager Panel -->
            <div id="file-panel" class="file-panel hidden">
                <div class="file-panel-header">
                    <h2>📁 Dateimanager</h2>
                    <button onclick="closeFilePanel()" class="text-2xl font-bold text-gray-500 hover:text-red-600">&times;</button>
               
                </div>

                <div class="file-panel-toolbar">
                    <input type="text" id="fileSearch" placeholder="🔍 Datei suchen..." class="search-input form-control w-full max-w-sm">
                   <button class="upload-label" onclick="openUploadModal()">
                        📤 Datei hochladen
                    </button>
                    <button onclick="refreshFiles()" class="btn btn-sm btn-secondary">
                        🔄 Aktualisieren
                    </button>
                </div>

                <div id="file-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Files will be appended here -->
                </div>

            </div>

 

            <div id="attachment-list"></div>

            <!-- Modal -->
            <div id="previewModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <iframe id="filePreviewFrame" class="w-100" style="min-height:500px;"></iframe>
                    </div>
                </div>
            </div>
            </div>


            <!-- Dropzone Upload Modal -->
                <div class="modal fade" id="uploadModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-4">
                    <div class="modal-header">
                        <h5 class="modal-title">📤 Dateien hochladen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/attachments/upload"
                            class="dropzone"
                            id="projectAttachmentDropzone"
                            method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="project_id" id="dz_project_id">
                            <input type="hidden" name="phase_id" id="dz_phase_id">
                            <input type="hidden" name="activity_id" id="dz_activity_id">
                        </form>
                    </div>
                    </div>
                </div>
                </div>



                <!-- Timeline Panel -->
                
                <div id="timeline-log-panel" class="timeline-log-panel">
                    <!-- Header -->
                    <div class="timeline-header d-flex justify-content-between align-items-center px-4 py-3">
                        <h5 class="mb-0 text-white">Timeline Aktivitäten</h5>
                        <button class=" btn btn-icon btn-outline-danger mr-1 mb-1 waves-effect waves-light" onclick="closeLogPanel()"><i class="feather icon-x"></i></button>
                    </div>

                    <!-- Filter and progress -->
                    <div class="timeline-filter px-4 py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                        <div class="d-flex align-items-center gap-2 flex-wrap w-75">
                            <select id="log-employee-filter" class="form-select form-select-sm w-50">
                                <option value="">👤 Alle Mitarbeiter</option>
                                @foreach(\App\Models\Employee::all() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <input type="date" id="log-date-filter" class="form-control form-control-sm" />
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="showAddLogModal()">+ Log</button>
                    </div>

                    <!-- Progress -->
                    <div class="px-4 py-2">
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-warning" id="timeline-progress-bar" role="progressbar" style="width: 0%; height: 9px;"></div>
                        </div>
                        <div class="text-end mt-1 small text-muted" id="progress-text">Fortschritt: 0%</div>
                    </div>

                    <!-- Body -->
                    <div id="timeline-log-content" class="timeline-body px-4 pt-2 pb-4" style="overflow-y:auto; flex-grow: 1;"></div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="addLogModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm" role="document">
                        <form id="add-log-form">
                            @csrf
                            <input type="hidden" name="project_id" id="log_project_id">
                            <input type="hidden" name="timeline_id" id="log_timeline_id">

                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title">Neuer Log-Eintrag</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Datum</label>
                                        <input type="date" name="done_date" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Mitarbeiter</label>
                                        <select name="done_by" class="form-control" required>
                                            @foreach(\App\Models\Employee::all() as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Speichern</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>








             
@endsection
 
@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>

<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>

<script>
    $(document).ready(function() {
        $('.articles input[type="radio"]').on('change', function() {
            // Reset styles for all labels
            $('.articles input[type="radio"] + label').css({
                'background': '#b1aaaa',
                'color': 'inherit',
                'border-radius': '50%'
            });

            // Apply styles for the selected label
            if (this.checked) {
                $(this).next('label').css({
                    'background': '#92b532',
                    'color': 'white',
                    'border-radius': '50%'
                });

                // Send AJAX request
                let articleGroup = $(this).val();
                $.ajax({
                    url: '/customer_details', // Your endpoint for searching article group
                    method: 'GET',
                    data: { search: articleGroup, is_ajax: true },
                    success: function(response) {
                        // Handle the response here
                        console.log(response);
                        // Update the page content based on the response
                        $('#results').html(response); // Assuming 'results' is the id of the element where you want to display the results
                    },
                    error: function(error) {
                        // Handle the error here
                        console.error(error);
                    }
                });
            }
        });
    });
</script>
<!-- Add Employee to proejct: start  -->
 
<script>
    $(document).ready(function () {
        // Initialize Select2 with custom template for displaying employee image
        $('#employee_id').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Add Employee button click handler
        $('.add_employee').on('click', function () {
            let projectId = $(this).data('project');
            $('#modal_project_id').val(projectId); // Set project_id in hidden input
        });

        // Function to format employee dropdown with image
        function formatEmployee(emp) {
            if (!emp.id) {
                return emp.text;
            }
            var imageUrl = $(emp.element).data('image');
            var markup = `
                <div class="d-flex align-items-center">
                    <img src="${imageUrl}" alt="" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                    <span>${emp.text}</span>
                </div>
            `;
            return markup;
        }

        // AJAX form submission
        $('#add_employe_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let form = $(this);
            let url = form.attr('action'); // Get form action URL
            let data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success message with SweetAlert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page after success
                        $('#employeeModal').modal('hide');

                        const projectId = $('#modal_project_id').val();
                        const phaseId = $('#modal_phase_id').val();
                        if (projectId && phaseId) {
                            fetchPhaseEmployees(projectId, phaseId);
                        }
                    });
                },
                error: function (xhr) {
                    // Show validation errors with SweetAlert
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Validierungsfehler',
                            html: errorMessages, // Display errors in HTML format
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

 <!-- Add Employee to proejct: end  -->

<!-- accepting request modal: start -->
 <script>
    $(document).ready(function () {
        // Open Modal and Populate Data
        $(document).on('click', '#accept_button', function () {
            const projectId = $(this).data('project');
            const employeeId = $(this).data('employee');

            // Populate hidden inputs in the modal
            $('#accept_project_id').val(projectId);
            $('#accept_employee_id').val(employeeId);

            // Open the modal
            $('#acceptModal').modal('show');
        });

        // Submit Form with AJAX
        $('#accept-request-form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const url = form.attr('action'); // Get form action URL
            const data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success alert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page
                        $('#acceptModal').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show error messages
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Fehler',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

<!-- accepting request modal: end -->
 


<script>
    $(document).ready(function() {
    $('.employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
    });

    function formatEmployee(employee) {
        if (!employee.id) {
            return employee.text;
        }

        const imageUrl = $(employee.element).data('image');
        const employeeName = employee.text;

        const markup = `
            <div style="display: flex; align-items: center;">
                <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
                <span>${employeeName}</span>
            </div>
        `;

        return markup;
    }

</script>

<script>
  function showTab(tab) {
    const tabs = ['list'];
    
    tabs.forEach(t => {
      document.getElementById(`${t}-view`).style.display = (t === tab) ? 'block' : 'none';
      document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
    });
  }
</script>


   
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const PROJECT_IMAGE_PATH = "{{ asset('images/projects') }}";
    const currentEmployeeId = {{ (int) auth()->user()->name }};

</script>

<script>
            let loadedFromDefault = false;
            let alreadySaved = false;
            let selectedChecklistId = null;
            let loadedFromChecklist = false;
            let checklistToSave = null; // stores the checklist + task structure for save
            let selectedCustomerId = null;
            let selectedAlternativeId = null;
            let selectedProductId = null;
            let selectedSectionName = null;
            let selectedProjectId = null;
            let selectedService = null;
            let contactIndex = 0;
            let selectedPhaseId = null;
            let selectedActivityId = null;
            let fileDataList = []; // store full file list for searching
            let quillMain;
            let allComments = []; // will hold all comments after loading
            const phaseEmployeeMap = new Map(); // key = phaseId, value = [employeeId, employeeId...]

            const emp_src = "{{ asset('images/employee/') }}";
                const statusMap = {
                    "published": "not-started",
                    "pending": "in-progress",
                    "completed": "done"
                };

            const projectStageNames = {
                "new": "Neu",
                "plan": "Planung",
                "process": "Prozess",
                "completed": "Abgeschlossen",
                "junk": "Junk",
                "pause": "Pausiert"
            };

            document.addEventListener("DOMContentLoaded", function () {
                loadProjectKanban();
                loadProjectList();

                document.getElementById("searchButton").addEventListener("click", function () {
                    let query = document.getElementById("searchInput").value.trim();
                    if (query === "") {
                        loadProjectKanban();
                    } else {
                        searchProjectKanban(query);
                    }
                });

                document.getElementById("searchInput").addEventListener("keypress", function (event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        let query = this.value.trim();
                        if (query === "") {
                            loadProjectKanban();
                        } else {
                            searchProjectKanban(query);
                        }
                    }
                });
            });

            document.getElementById("closeSidebarBtn").addEventListener("click", function () {
                closeSidebar();
            });

            function closeSidebar() {
                if (loadedFromChecklist && checklistToSave && !alreadySaved) {
                    Swal.fire({
                        title: "Checkliste speichern?",
                        text: "Möchtest du die geladene Checkliste ins Projekt speichern?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Ja, speichern",
                        cancelButtonText: "Nein"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            saveCurrentChecklistToProjectTasks(); // this will call the correct save function
                            alreadySaved = true;
                            actuallyCloseSidebar();
                        } else {
                            actuallyCloseSidebar();
                        }
                    });
                } else {
                    actuallyCloseSidebar();
                }
            }
 
            function loadProjectList() {
                fetch('{{ route("project.get.list") }}')
                    .then(res => res.json())
                    .then(data => renderProjectList(data))
                    .catch(err => console.error("Fehler beim Laden der Projekte:", err));
            }
 
            function loadProjectKanban() {
                fetch('{{ route("project.get.list") }}')
                    .then(res => res.json())
                    .then(data => renderProjectKanban(data))
                    .catch(err => console.error("Fehler beim Laden der Projekte:", err));
            }


            function searchProjectList(query) {
                fetch(`/project/search/status?search=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => renderProjectList(data))
                    .catch(err => console.error("Fehler bei der Suche:", err));
            }

        function loadProjectTasks(productId) {
            fetch(`/project/checklist/${productId}`)
                .then(res => {
                    if (!res.ok) throw new Error("Keine Checkliste gefunden");
                    return res.json();
                })
                .then(data => {
                    console.log("Checklist API Result:", data);

                    // ✅ Set selectedChecklistId from backend response
                    if (data.project_montage_id) {
                        selectedChecklistId = data.project_montage_id;
                        console.log("✅ Set selectedChecklistId:", selectedChecklistId);
                    } else {
                        selectedChecklistId = null;
                    }

                    if (!data.phases || !Array.isArray(data.phases)) {
                        console.warn("Phases not found or not an array.");
                        return;
                    }
 
                    renderTaskAccordion(data.phases);
                })
                .catch(err => {
                    console.warn("Fehler beim Laden der Aufgaben:", err);

                    Swal.fire({
                        title: "Keine Aufgaben gefunden",
                        text: "Für dieses Projekt wurden keine Aufgaben gefunden. Möchtest du eine neue Checkliste erstellen oder manuell Aufgaben hinzufügen?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Checkliste erstellen",
                        cancelButtonText: "Manuell hinzufügen"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/checklist/create";
                        } else {
                            renderEmptyTaskViews();
                        }
                    });
                });
        }


        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("searchInput").addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    const query = this.value.trim();
                    if (query === "") {
                        loadProjectList();
                    } else {
                        searchProjectList(query);
                    }
                }
            });
        });
 
        function renderEmptyTaskViews() {
            const boardView = document.getElementById("board-view");
            const listView = document.querySelector("#list-view .task-table tbody");
            const accordion = document.getElementById("accordion-tasks");

            boardView.innerHTML = `
                <div style="text-align:center; margin:20px;">
                    <p>Keine Aufgaben vorhanden.</p>
                    <button onclick="addMainTask()">+ Hauptaufgabe hinzufügen</button>
                    <button onclick="addSubTask()" >+ Unteraufgabe hinzufügen</button>
                    <button onclick="saveManualChecklist()">💾 Später speichern</button>
                </div>
            `;

            listView.innerHTML = `<tr><td colspan="5" style="text-align:center;">Keine Aufgaben</td></tr>`;
            accordion.innerHTML = `<h3>Aufgaben</h3><p>Keine Aufgaben vorhanden.</p>`;
        }
 
        function visitProfileFromList(projectId, customerId, alternativeId, productId, service) {
            const fakeBtn = document.createElement("button");
            fakeBtn.setAttribute("data-project-id", projectId);
            fakeBtn.setAttribute("data-customer-id", customerId);
            fakeBtn.setAttribute("data-alternative-id", alternativeId);
            fakeBtn.setAttribute("data-product-id", productId);
            fakeBtn.setAttribute("data-service", service);
            visitProfile(fakeBtn);
        }

        function editCardFromList(customerId, alternativeId) {
            window.location.href = `/new_lead_edit/${customerId}/${alternativeId}`;
        }

        function deleteCardFromList(projectId) {
            Swal.fire({
                title: "Bist du sicher?",
                text: "Projekt wird dauerhaft gelöscht.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ja, löschen",
                cancelButtonText: "Abbrechen"
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `/delete_lead_product/${projectId}`;
                }
            });
        }
 
        function addMainTask() {
            Swal.fire({
                title: 'Neue Hauptaufgabe',
                input: 'text',
                inputLabel: 'Phasenname',
                inputPlaceholder: 'Gib den Namen der Phase ein',
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                preConfirm: (phaseName) => {
                    if (!phaseName) {
                        Swal.showValidationMessage('Phasenname ist erforderlich');
                    }
                    return phaseName;
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const phaseName = result.value;

                    // ✅ Use global vars from visitProfile
                    const productId = selectedProductId;
                    const sectionName = selectedSectionName;

                    if (!productId || !sectionName) {
                        Swal.fire("Fehler", "Produkt oder Sektion nicht gefunden", "error");
                        return;
                    }

                    // ✅ No need to fetch section first — handled in backend
                    fetch("{{ route('task.phase.store.new') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: JSON.stringify({
                            phase_name: phaseName,
                            product_id: productId,
                            section_name: sectionName
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Erfolg", "Phase gespeichert", "success");
                            loadProjectTasks(productId); // 🔄 Refresh phase/task view
                        } else {
                            Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen", "error");
                        }
                    })
                    .catch(err => {
                        console.error("Fehler beim Speichern der Phase:", err);
                        Swal.fire("Fehler", "Fehler beim Speichern der Phase", "error");
                    });
                }
            });
        }
 
        function addSubTask() {
            const selectedCard = document.querySelector(".card.selected");

            if (!selectedCard) {
                Swal.fire("Fehler", "Kein Projekt ausgewählt", "error");
                return;
            }

            const productId = selectedCard.getAttribute("data-product-id");
            const sectionName = selectedCard.getAttribute("data-service");

            if (!productId || !sectionName) {
                Swal.fire("Fehler", "Produkt oder Service nicht gefunden", "error");
                return;
            }

            fetch(`/get-phases/${productId}`)
                .then(res => res.json())
                .then(phases => {
                    if (!phases.length) {
                        Swal.fire("Hinweis", "Bitte zuerst eine Hauptaufgabe (Phase) erstellen.", "info");
                        return;
                    }

                    let options = phases.map(p => `<option value="${p.id}">${p.phase_name}</option>`).join('');

                    Swal.fire({
                        title: "Neue Aufgabe hinzufügen",
                        html: `
                            <select id="phase_id" class="swal2-input">${options}</select>
                            <input id="task_title" class="swal2-input" placeholder="Titel">
                            <textarea id="task_desc" class="swal2-textarea" placeholder="Beschreibung"></textarea>
                        `,
                        showCancelButton: true,
                        confirmButtonText: "Speichern",
                        preConfirm: () => {
                            return {
                                phase_id: document.getElementById("phase_id").value,
                                title: document.getElementById("task_title").value,
                                description: document.getElementById("task_desc").value
                            };
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const formData = result.value;

                            fetch("{{ route('activities.store.new') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                                },
                                body: JSON.stringify({
                                    ...formData,
                                    product_id: productId,
                                    section_name: sectionName,
                                    status: "published"
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire("Gespeichert", "Aufgabe erfolgreich hinzugefügt", "success");
                                loadProjectTasks(productId);
                            })
                            .catch(err => {
                                console.error("Fehler:", err);
                                Swal.fire("Fehler", "Aufgabe konnte nicht gespeichert werden", "error");
                            });
                        }
                    });
                });
        }

        function actuallyCloseSidebar() {
            document.getElementById("project-profile").classList.remove("active", "fullscreen");
            document.querySelector(".project-profile-overlay").classList.remove("active");
        }


        function toggleMaximizeSidebar() {
            document.getElementById("project-profile").classList.toggle("fullscreen");
        }
 


        function saveManualChecklist() {
            Swal.fire("Gespeichert", "Manuelle Aufgaben wurden zwischengespeichert", "success");
        }

     
       
        async function renderTaskAccordion(phases) {
            const container = document.getElementById("accordion-tasks");
            container.innerHTML = "<h3 class='text-xl font-semibold mb-4'>Tasks</h3>";

            if (!phases?.length) {
                container.innerHTML += "<p>Keine Aufgaben vorhanden.</p>";
                return;
            }

            const timelinesMap = new Map();
            const timelines = await $.get(`/project-timeline/load/${selectedProjectId}`);
            timelines.forEach(t => {
                const key = `phase-${t.phase_id}-activity-${t.activity_id}`;
                timelinesMap.set(key, t);
            });

            for (const phase of phases) {
                await fetchPhaseEmployees(selectedProjectId, phase.phase_id); // ✅ Wait for employee data

                const contentId = `phase-content-${phase.phase_id}`;
                const headerFillId = `phase-progress-fill-${phase.phase_id}`;
                const headerLabelId = `phase-progress-label-${phase.phase_id}`;
                const totalTasks = phase.activities.length;
                const totalDuration = phase.activities.reduce((s, t) => s + parseFloat(t.duration || 0), 0);

                const header = document.createElement("div");
                header.className = "accordion-header";
                header.setAttribute("data-project-id", selectedProjectId);
                header.setAttribute("data-phase-id", phase.phase_id);
                header.innerHTML = `
                    <div class="relative w-full p-2 rounded-lg hover:bg-blue-100 transition" style="background:#2c3e50 !important">
                        <div class="absolute top-2 right-2 z-50">
                            <button class="text-gray-600 hover:text-blue-600" onclick="toggleMenu(this)">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="menu-dropdown hidden absolute right-0 mt-2 w-44 bg-white border rounded shadow-lg">
                                <button onclick="event.stopPropagation(); openEmployeeModal(${selectedProjectId}, ${phase.phase_id})" class="w-full text-left px-2 text-sm hover:bg-blue-50">
                                    <i class="feather icon-users"></i> Mitarbeiter
                                </button>
                                <button onclick="event.stopPropagation(); changePhaseDate(${phase.phase_id})" class="w-full text-left px-2 text-sm hover:bg-blue-50">
                                    <i class="feather icon-calendar"></i> Datum
                                </button>
                                <button onclick="event.stopPropagation(); openTimeline(${selectedProjectId}, ${phase.phase_id})" class="w-full text-left px-2 text-sm hover:bg-blue-50">
                                    <i class="fa fa-line-chart"></i> Timeline
                                </button>
                            </div>
                        </div>
                        <div onclick="document.getElementById('${contentId}').style.display = 
                            document.getElementById('${contentId}').style.display === 'none' ? 'block' : 'none'">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-1">
                                <div class="flex flex-col text-white text-base font-semibold">
                                    <i class="feather icon-folder"></i> ${phase.phase_name}
                                    <small class="font-normal text-gray-200">(${totalTasks} Aufgabe(n) · <i class="feather icon-clock"></i> ${totalDuration.toFixed(1)} Std)</small>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="flex items-center gap-1 text-sm text-white">
                                        <i class="feather icon-calendar"></i> ${phase.start_date || 'N/A'} → ${phase.end_date || 'N/A'}
                                    </span>
                                    <ul class='users-list flex gap-1 mt-1'></ul>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-lg overflow-hidden mt-2">
                                <div id="${headerFillId}" class="  h-full transition-all" style="width: 0%; background:#a9cd57 !important;"></div>
                            </div>
                            <div class="text-right text-xs text-gray-700 mt-0.5" id="${headerLabelId}">0% erledigt</div>
                        </div>
                    </div>
                `;

                const content = document.createElement("div");
                content.id = contentId;
                content.className = "accordion-content ml-1 mt-2";
                content.style.display = "none";
                content.setAttribute("data-phase-id", phase.phase_id);

                for (const task of phase.activities) {
                    const activityId = task.activity_id || task.id;
                    const taskId = `phase-${phase.phase_id}-activity-${activityId}`;
                    const progressId = `progress-bar-${taskId}`;
                    const fillId = `progress-fill-${taskId}`;
                    const labelId = `progress-label-${taskId}`;
                    const checkboxId = `done-progress-bar-${taskId}`;
                    const timeline = timelinesMap.get(taskId);
                    let range = timeline?.done_range || 0;

                    const taskEl = document.createElement("div");
                    taskEl.className = "task mb-1 border p-2 rounded-md bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-200";
                    taskEl.setAttribute("data-activity-id", activityId);
                    taskEl.innerHTML = `
                        <div class="mb-2">
                            <div class="flex justify-between items-center">
                                <strong>${task.title_activity}</strong> <small>(${task.duration || 0} Std)</small>
                                <div class="d-inline-flex align-items-center gap-6">
                                    <div class="position-relative d-inline-block" onclick="openCommentLayout(${selectedProjectId}, ${phase.phase_id}, ${activityId})" title="Kommentare">
                                        <i class="feather icon-message-square text-primary"></i>
                                        <span id="comment-badge-${activityId}" class="badge badge-pill badge-primary badge-up">0</span>
                                    </div>
                                    <div class="position-relative d-inline-block" onclick="openFilePanel(${selectedProjectId}, ${phase.phase_id}, ${activityId})" title="Dateien">
                                        <i class="feather icon-paperclip text-warning"></i>
                                        <span class="badge badge-pill badge-warning badge-up file-count-badge" data-activity-id="${activityId}">0</span>
                                    </div>
                                    <div class="position-relative d-inline-block" onclick="openLogPanel(${selectedProjectId}, ${phase.phase_id}, ${activityId})" title="Log">
                                        <i class="feather icon-server text-warning"></i>
                                        <span class="badge badge-pill badge-warning badge-up file-count-badge" data-activity-id="${activityId}">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm"> <i class="feather icon-chevron-right"></i> ${task.description || 'Keine Beschreibung'}</div>
                        </div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="${checkboxId}" class="w-4 h-4 text-green-500" disabled ${range === 100 ? 'checked' : ''}>
                                <span class="text-sm">Erledigt</span>
                            </label>
                            <span id="${labelId}" class="text-sm font-medium text-gray-700">${range}%</span>
                        </div>
                        <div id="${progressId}" class="progress-bar bg-gray-300 rounded h-2 relative overflow-hidden cursor-pointer" style="height:32px !important;">
                            <div id="${fillId}" class="progress-fill   h-full transition-all" style="width: ${range}%; background: #a9cd57;"></div>
                        </div>
                    `;

                    const progressBar = taskEl.querySelector(`#${progressId}`);
                    const progressFill = taskEl.querySelector(`#${fillId}`);
                    const progressLabel = taskEl.querySelector(`#${labelId}`);
                    const doneCheckbox = taskEl.querySelector(`#${checkboxId}`);

                    const steps = [0, 25, 50, 75, 100];
                    let isDragging = false;
                    let currentStep = steps.indexOf(range);
                    if (currentStep === -1) currentStep = 0;

                    const updateProgress = (stepIndex) => {
                        const allowed = phaseEmployeeMap.get(phase.phase_id) || [];
                        if (!allowed.includes(currentEmployeeId)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Nicht erlaubt',
                                text: 'Du bist diesem Projekt nicht zugewiesen.',
                            });
                            return;
                        }

                        const value = steps[stepIndex];
                        progressFill.style.width = `${value}%`;
                        progressLabel.textContent = `${value}%`;
                        doneCheckbox.checked = value === 100;

                        $.post('/project-timeline/progress-update', {
                            _token: document.querySelector('meta[name="csrf-token"]').content,
                            project_id: selectedProjectId,
                            phase_id: phase.phase_id,
                            activity_id: activityId,
                            done_range: value,
                            employee_id: currentEmployeeId
                        }, function (response) {
                            taskEl.querySelector('.progress-meta')?.remove();
                            const meta = document.createElement("div");
                            meta.className = "progress-meta text-xs text-gray-600 mt-1";
                            meta.innerHTML = `✅ ${response.done_range}% erledigt von <b>${response.done_by}</b> am <b>${response.done_date}</b>`;
                            progressBar.parentElement.appendChild(meta);

                            if (value === 100) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Aufgabe abgeschlossen!',
                                    html: `Gestartet: <b>${response.start_date}</b><br>Beendet: <b>${response.done_date}</b><br>Zeitdifferenz: <b>${response.date_difference} Tage</b>`
                                });
                            }

                            updatePhaseProgress(phase.phase_id);
                        });
                    };

                    const handleDrag = (e) => {
                        const rect = progressBar.getBoundingClientRect();
                        const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
                        const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
                        currentStep = percent < 12.5 ? 0 : percent < 37.5 ? 1 : percent < 62.5 ? 2 : percent < 87.5 ? 3 : 4;
                        updateProgress(currentStep);
                    };

                    progressBar.addEventListener('mousedown', (e) => { isDragging = true; handleDrag(e); });
                    progressBar.addEventListener('mousemove', (e) => { if (isDragging) handleDrag(e); });
                    document.addEventListener('mouseup', () => { isDragging = false; });

                    progressBar.addEventListener('touchstart', (e) => { isDragging = true; handleDrag(e); });
                    progressBar.addEventListener('touchmove', (e) => { if (isDragging) handleDrag(e); });
                    progressBar.addEventListener('touchend', () => { isDragging = false; });

                    if (range && timeline?.done_by && timeline?.done_date) {
                        const meta = document.createElement("div");
                        meta.className = "progress-meta text-xs text-gray-600 mt-1";
                        meta.innerHTML = `✅ ${range}% erledigt von <b>${timeline.done_by}</b> am <b>${timeline.done_date}</b>`;
                        taskEl.appendChild(meta);
                    }

                    content.appendChild(taskEl);
                }

                container.appendChild(header);
                container.appendChild(content);
                requestAnimationFrame(() => updatePhaseProgress(phase.phase_id));
            }
        }

 
        function updateTaskAuthorizationStyle(taskEl, phaseId) {
            const allowed = phaseEmployeeMap.get(phaseId) || [];
            if (!allowed.includes(currentEmployeeId)) {
                taskEl.classList.add("opacity-50", "cursor-not-allowed");
                taskEl.title = "Du bist nicht diesem Projekt zugewiesen.";
            } else {
                taskEl.classList.remove("opacity-50", "cursor-not-allowed");
                taskEl.title = "";
            }
        }

           // ✅ Safe Phase Progress Bar Updater
       
        function updatePhaseProgress(phaseId) {
            const fillEl = document.getElementById(`phase-progress-fill-${phaseId}`);
            const labelEl = document.getElementById(`phase-progress-label-${phaseId}`);

            const taskFills = document.querySelectorAll(`[id^="progress-fill-phase-${phaseId}-activity-"]`);
            let total = 0;
            let count = 0;

            taskFills.forEach(fill => {
                const width = parseInt(fill.style.width || '0');
                total += width;
                count++;
            });

            const avg = count > 0 ? Math.round(total / count) : 0;

            if (fillEl) fillEl.style.width = `${avg}%`;
            if (labelEl) labelEl.textContent = `${avg}% erledigt`;
        }


        function loadCommentCount(activityId) {
            fetch(`/comments/count/${activityId}`)
                .then(res => res.json())
                .then(data => {
                    const count = typeof data === 'number' ? data : data.count;
                    const badge = document.querySelector(`#comment-badge-${activityId}`);
                    if (badge) badge.textContent = count;
                })
                .catch(err => console.error("❌ Error loading comment count:", err));
        }

        function updateFileCountBadge(activityId) {
            fetch(`/attachments/activity/${activityId}`)
                .then(res => res.json())
                .then(data => {
                    const count = data.length;
                    const badge = document.querySelector(`.file-count-badge[data-activity-id="${activityId}"]`);
                    if (badge) {
                        badge.textContent = count;
                    }
                })
                .catch(err => {
                    console.error("❌ Fehler beim Aktualisieren des Datei-Counters:", err);
                });
        }
 
     

        function toggleMenu(button) {
            event.stopPropagation(); // prevent toggle opening
            const dropdown = button.nextElementSibling;
            const allMenus = document.querySelectorAll('.menu-dropdown');

            allMenus.forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });

            dropdown.classList.toggle('hidden');
        }
 
        function openEmployeeModal(projectId, phaseId) {
            // Set values in hidden inputs
            $('#modal_project_id').val(projectId);
            $('#modal_phase_id').val(phaseId);
            $('#modal_activity_id').val(''); // Optional: use if you want to assign to a specific task
            $('#modal_old_employee').val(''); // Optional: preload old employee IDs if needed

            // Reset the select box and role
            $('#employee_id_select').val(null).trigger('change');
            $('#employee_roll').val('member');

            // Show the modal
            $('#employeeModal').modal('show');
        }


        // adding Employee to the project :start
        // 🧠 Open modal and inject IDs
         
 

            // 🔁 Fetch assigned employees & update UI
        function fetchPhaseEmployees(projectId, phaseId) {
            $.ajax({
                url: `/project/employee/get/${projectId}/${phaseId}`,
                type: 'GET',
                success: function (employees) {
                    // Save allowed IDs to map
                    phaseEmployeeMap.set(phaseId, employees.map(emp => emp.id));

                    const header = $(`.accordion-header[data-project-id="${projectId}"][data-phase-id="${phaseId}"]`);
                    let usersList = header.find('.users-list');

                    if (!usersList.length) {
                        const target = header.find('.flex.flex-col');
                        if (target.length) {
                            target.append(`<ul class="users-list flex gap-1 mt-1"></ul>`);
                            usersList = header.find('.users-list');
                        }
                    }

                    usersList.empty();
                        employees.forEach(emp => {
                            usersList.append(`
                                <li class="avatar pull-up delete_user" title="${emp.name} ${emp.lastname}"
                                    data-project-id="${projectId}"
                                    data-phase-id="${phaseId}"
                                    data-employee-id="${emp.id}">
                                    <img class="media-object rounded-circle" src="/images/employee/${emp.image}" alt="${emp.name}" height="25" width="25">
                                </li>
                            `);
                        });


                        usersList.find('.delete_user').on('click', function () {
                            const $this = $(this);
                            const employeeId = $this.data('employee-id');
                            const projectId = $this.data('project-id');
                            const phaseId = $this.data('phase-id');

                            Swal.fire({
                                title: 'Mitarbeiter entfernen?',
                                text: 'Bist du sicher, dass du diesen Mitarbeiter entfernen möchtest?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ja, löschen',
                                cancelButtonText: 'Abbrechen'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: '/project/employee/delete',
                                        type: 'DELETE',
                                        data: {
                                            _token: $('meta[name="csrf-token"]').attr('content'),
                                            employee_id: employeeId,
                                            project_id: projectId,
                                            phase_id: phaseId
                                        },
                                        success: function (res) {
                                            if (res.success) {
                                                $this.remove();
                                                Swal.fire('Gelöscht!', 'Der Mitarbeiter wurde entfernt.', 'success');
                                                updatePhaseProgress(phaseId);
                                            }
                                        },
                                        error: function (xhr) {
                                            Swal.fire('Fehler', 'Mitarbeiter konnte nicht gelöscht werden.', 'error');
                                        }
                                    });
                                }
                            });
                        });


                    // 🔄 Update task style
                    document.querySelectorAll(`.accordion-content[data-phase-id="${phaseId}"] .task`).forEach(task => {
                        updateTaskAuthorizationStyle(task, phaseId);
                    });
                },
                error: function (xhr) {
                    console.error("❌ Failed to fetch employees:", xhr.responseText);
                }
            });
        }
 
        $('#change_employee_form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();

            const projectId = $('#change_project_id').val();
            const phaseId = $('#change_phase_id').val();

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (res) {
                    toastr.success(res.message);

                    $('#change_employee').modal('hide');
                    form.trigger('reset');

                    fetchPhaseEmployees(projectId, phaseId); // 🔄 update UI
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Fehler beim Speichern.');
                }
            });
        });

        // adding Employee to the project :end
 

        // Showing the timeline of project Phase :start
    
        

        // Showing the timeline of project Phase :end
        function searchProjectKanban(query) {
            fetch(`/project/search/status?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => renderProjectKanban(data))
                .catch(err => console.error("Fehler bei der Suche:", err));
        }

        function allowDrop(event) {
            event.preventDefault();
        }


        function renderProjectKanban(data) {
            let kanbanBoard = document.getElementById("kanban");
            kanbanBoard.innerHTML = "";

            Object.keys(projectStageNames).forEach(stageKey => {
                let stageColumn = document.createElement("div");
                stageColumn.className = "column";
                stageColumn.id = stageKey;
                stageColumn.setAttribute("ondrop", "drop(event)");
                stageColumn.setAttribute("ondragover", "allowDrop(event)");
                stageColumn.innerHTML = `<h3>${projectStageNames[stageKey]}</h3><div class="column-content"></div>`;
                kanbanBoard.appendChild(stageColumn);
            });

            data.forEach(project => {
                let updatedDate = new Date(project.updated_at).toLocaleDateString("de-DE", {
                    day: "2-digit", month: "2-digit", year: "numeric"
                });

                let employee = project.employee && project.employee.employee_id
                    ? {
                        employee_id: project.employee.employee_id,
                        name: project.employee.name,
                        lastname: project.employee.lastname,
                        image: project.employee.image
                    }
                    : null;

                let stage = project.stage.toLowerCase();
                if (!projectStageNames[stage]) stage = "new";

                addCard(
                    stage,
                    project.initial,
                    `${project.customer_name} ${project.customer_lastname}`,
                    `Email: ${project.email}`,
                    `<i class="feather icon-calendar warning"></i> ${updatedDate}`,
                    `${project.street}, ${project.postcode}, ${project.city}`,
                    project.customer_id,
                    project.alternative_id,
                    project.product_id,
                    project.service,
                    employee,
                    project.project_id
                );
            });

            document.querySelectorAll(".column").forEach(col => {
                if (!col.querySelector(".card")) {
                    col.innerHTML += `<small>Keine Daten</small>`;
                }
            });
        }

        function renderProjectList(data) {
            const listContainer = document.getElementById("project_list");
            listContainer.innerHTML = `
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Suche nach Kunden..." />
                <div class="project-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kunde</th>
                                <th>Details</th>
                                <th>Produkt</th>
                                <th>Mitarbeiter</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody id="project_list_body"></tbody>
                    </table>
                </div>
            `;

            const tbody = document.getElementById("project_list_body");

            data.forEach(project => {
                const updatedDate = new Date(project.updated_at).toLocaleDateString("de-DE", {
                    day: "2-digit", month: "2-digit", year: "numeric"
                });

                const employee = project.employee || {};
                const stage = project.stage?.toLowerCase() || "new";
                const stageLabel = projectStageNames[stage] || "Neu";

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td><strong>${project.customer_name} ${project.customer_lastname}</strong></td>
                    <td>
                        Email: ${project.email}<br>
                        📍 ${project.street}, ${project.postcode}, ${project.city}<br>
                        📅 ${updatedDate}
                    </td>
                    <td>${project.initial}</td>
                    <td>
                        ${employee.image ? `
                            <img src="${emp_src}/${employee.image}" class="rounded-circle" height="25" width="25" alt="${employee.name}" title="${employee.name} ${employee.lastname}">
                        ` : "Kein Mitarbeiter"}
                    </td>
                    <td><span class="badge badge-info">${stageLabel}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="visitProfileFromList(${project.project_id}, ${project.customer_id}, ${project.alternative_id}, ${project.product_id}, '${project.service}')">
                            <i class="feather icon-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editCardFromList(${project.customer_id}, ${project.alternative_id})">
                            <i class="feather icon-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCardFromList(${project.project_id})">
                            <i class="feather icon-trash"></i>
                        </button>
                    </td>
                `;

                tbody.appendChild(row);
            });
        }
 
        function addCard(columnId, product, customerName, customerDetails, date, address, customerId, alternativeId, productId, service, employee, projectId) {
            let column = document.getElementById(columnId);
            if (!column) return;

            let card = document.createElement("div");
            card.className = "card";
            card.id = "card-" + Math.random().toString(36).substr(2, 9);
            card.draggable = true;
            card.ondragstart = drag;
            card.onclick = (event) => selectCard(event, card);

            let employee_id = employee && employee.employee_id ? employee.employee_id : 0;
            card.setAttribute("data-customer-id", customerId);
            card.setAttribute("data-alternative-id", alternativeId);
            card.setAttribute("data-product-id", productId);
            card.setAttribute("data-service", service);
            card.setAttribute("data-employee-id", employee_id);
            card.setAttribute("data-lead-product-id", projectId);

            let employeeHtml = employee && employee.image
                ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
                        <li class="avatar pull-up" data-toggle="tooltip" title="${employee.name} ${employee.lastname}">
                            <img class="media-object rounded-circle" src="${emp_src}/${employee.image}" alt="${employee.name}" height="25" width="25">
                        </li>
                </ul>`
                : `<small>Kein Mitarbeiter zugewiesen</small>`;

            card.innerHTML = `
                <div class="card-header">
                    <strong>${customerName}</strong>
                    <div class='circle'>${product}</div>
                </div>
                <div>
                    <small>${customerDetails}</small><br>
                    <small>${date}</small><br>
                    <small>${address}</small>
                </div>
                <div class="employeeList">${employeeHtml}</div>
                <div class='card-actions'>
                <button class="profile"
                    id="visitProfileButton"
                    onclick="visitProfile(this)"
                    data-project-id="${projectId}"
                    data-customer-id="${customerId}"
                    data-alternative-id="${alternativeId}"
                    data-service="${service}"
                    data-product-id="${productId}">
                    <i class="feather icon-eye"></i>
                </button>


                    <button onclick="editCard('${card.id}')"><i class="feather icon-edit"></i></button>
                    <button onclick="deleteCard('${card.id}')"><i class="feather icon-trash"></i></button>
                </div>
            `;

            column.querySelector(".column-content").appendChild(card);
        }

        function visitProfile(button) {
            // Highlight selected card
            document.querySelectorAll(".card").forEach(c => c.classList.remove("selected"));
            const card = button.closest(".card");
            card.classList.add("selected");

            // Save global variables
            selectedCustomerId = button.getAttribute("data-customer-id");
            selectedAlternativeId = button.getAttribute("data-alternative-id");
            selectedProductId = button.getAttribute("data-product-id");
            selectedProjectId = button.getAttribute("data-project-id");
            selectedService = button.getAttribute("data-service");
            selectedSectionName = selectedService;

            // Show sidebar
            document.getElementById("project-profile")?.classList.add("active");
            document.querySelector(".project-profile-overlay")?.classList.add("active");

            const customerInfoUrl = `/project/customer/${selectedCustomerId}/${selectedAlternativeId}`;
            const taskUrl = `/customer/project/phase/get/${selectedCustomerId}/${selectedAlternativeId}/${selectedProductId}`;

            // Step 1: Load customer and leader info
            fetch(customerInfoUrl)
                .then(res => res.json())
                .then(data => {
                    console.log("✅ Customer Info:", data);

                    const customerName = document.getElementById("customer_name");
                    if (customerName) customerName.textContent = `${data.name} ${data.lastname}`;

                    const status = document.getElementById("customer_status");
                    if (status) {
                        status.textContent = data.stage;
                        status.className = `badge ${data.stage}`;
                    }

                    const contactPerson = document.getElementById("contact_person");
                    if (contactPerson) {
                        contactPerson.innerHTML = `<strong>Verfasser:</strong> ${data.emp_name} ${data.emp_lastname}`;
                    }

                    const requestDate = document.getElementById("request_date");
                    if (requestDate && data.request_date) {
                        requestDate.innerHTML = `<strong>Start Date:</strong> ${new Date(data.request_date).toLocaleDateString("de-DE")}`;
                    }

                    const leaderName = document.getElementById("project-leader-name");
                     const leaderImage = document.getElementById("project-leader-image");

                    if (leaderName && leaderImage) {
                        if (data.leader_name && data.leader_image) {
                            leaderName.textContent = `${data.leader_name} ${data.leader_lastname}`;
                            leaderImage.src = `/images/employee/${data.leader_image}`;
                        } else {
                            leaderName.textContent = "Kein Projektleiter";
                            leaderImage.src = "/images/gender/male.png";
                        }
                    }

                    // Produktname
                        document.getElementById("project-product").innerHTML = `<strong>Produkt:</strong> ${data.product_name}`;

                        // Beteiligte Personen
                        const employeeText = document.getElementById("project-employee");
                        if (employeeText && Array.isArray(data.employees)) {
                            const html = data.employees.map(emp => {
                                const img = emp.image ? `/images/employee/${emp.image}` : '/images/gender/male.png';
                                return `
                                        <div class="avatar ">
                                            <img src="${img}" alt="${emp.name} ${emp.lastname}" height="25" width="25">
                                        </div>
                                        `;
                            }).join(' ');
                            employeeText.innerHTML = `<strong>Beteiligte Personen:</strong> ${html}`;
                        }


                    return fetch(taskUrl);
                })
                .then(res => {
                    console.log("📥 Response status:", res.status);
                    if (!res.ok) throw new Error("Kein gespeichertes Aufgabenprofil");
                    return res.json();
                })
                .then(taskData => {
                    console.log("Received task data:", taskData);

                    if (taskData && Array.isArray(taskData.phases) && taskData.phases.length > 0) {
                        loadedFromChecklist = false;
                        checklistToSave = null;
                        selectedChecklistId = taskData.project_montage_id || null;
 
                        renderTaskAccordion(taskData.phases);
                    } else {
                        throw new Error("Gespeicherte Aufgaben leer");
                    }

                    loadContactPeople(selectedCustomerId, selectedAlternativeId);
                })
                .catch(error => {
                    console.warn("⚠️ Kein gespeichertes Aufgabenprofil gefunden:", error);

                    const checklistSelectWrapper = document.getElementById("checklistSelectWrapper");
                    if (checklistSelectWrapper) checklistSelectWrapper.style.display = "block";

                    loadedFromChecklist = true;

                    fetch(`/project/checklist/${selectedProductId}`)
                        .then(res => res.json())
                        .then(phaseData => {
                            console.log("📦 Default Checkliste geladen:", phaseData);

                            if (phaseData.phases && Array.isArray(phaseData.phases) && phaseData.phases.length > 0) {
                                checklistToSave = { ...phaseData, phases: phaseData.phases };
                                selectedChecklistId = phaseData.project_montage_id;
 
                                renderTaskAccordion(phaseData.phases);
                                loadAllChecklists(selectedProductId);

                                Swal.fire({
                                    title: "Hinweis",
                                    text: "Standard-Checkliste geladen. Du kannst sie beim Verlassen speichern.",
                                    icon: "info",
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire("Keine Aufgaben", "Die Standard-Checkliste enthält keine Aufgaben.", "info");
                                renderEmptyTaskViews();
                            }

                            loadContactPeople(selectedCustomerId, selectedAlternativeId);
                        })
                        .catch(err => {
                            console.error("❌ Fehler beim Laden der Standard-Checkliste:", err);
                            Swal.fire("Fehler", "Checkliste konnte nicht geladen werden", "error");
                        });
                });
        }
 

        function loadChecklistSelect(productId) {
        if (!productId) return;

        fetch(`/checklist/all/${productId}`)
            .then(res => {
                if (!res.ok) throw new Error("Checklisten konnten nicht geladen werden.");
                return res.json();
            })
            .then(data => {
                const select = document.getElementById("checklistSelect");
                if (!select) return;

                select.innerHTML = `<option value="">Bitte wählen</option>`;

                // ✅ SAFELY get checklist array
                const checklistArray = data.checklists || [];
                const defaultChecklist = data.default;

                checklistArray.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.list_name;

                    // ✅ Auto-select the default
                    if (defaultChecklist && item.id === defaultChecklist.id) {
                        option.selected = true;

                        fetch(`/checklist/by-id/${item.id}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.phases && Array.isArray(data.phases)) {
                                    renderTaskAccordion(data.phases);
                                }
                            });
                    }

                    select.appendChild(option);
                });

                bindChecklistChangeEvent(); // ✅ bind events AFTER filling options
            })


            .catch(err => {
                console.error("Fehler beim Laden der Checklisten:", err);
            });
        }
 
        document.addEventListener("DOMContentLoaded", function () {
            const checklistSelect = document.getElementById("checklistSelect");
            if (checklistSelect) {
                checklistSelect.addEventListener("change", function () {
                    const checklistId = this.value;
                    if (checklistId) {
                        fetch(`/checklist/by-id/${checklistId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.phases && Array.isArray(data.phases)) {
                                    renderTaskAccordion(data.phases);
                                } else {
                                    renderEmptyTaskViews();
                                }
                            })
                            .catch(err => {
                                console.error("Fehler beim Laden der ausgewählten Checkliste:", err);
                                renderEmptyTaskViews();
                            });
                    } else {
                        // If empty, fallback to default checklist
                        loadProjectTasks(selectedProductId);
                    }
                });
            }
        });

        // Global function to bind checklist change listener
   
          function loadAllChecklists(productId) {
            if (!productId) return;

            fetch(`/checklist/all/${productId}`)
                .then(res => {
                    if (!res.ok) throw new Error("Checklisten konnten nicht geladen werden.");
                    return res.json();
                })
                .then(data => {
                    const select = document.getElementById("checklistSelect");
                    if (!select) return;

                    select.innerHTML = `<option value="">Bitte wählen</option>`;

                    const checklistArray = data.checklists || [];
                    const defaultChecklist = data.default;

                    checklistArray.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.textContent = item.list_name;

                        // ✅ Auto-select the default checklist
                        if (defaultChecklist && item.id === defaultChecklist.id) {
                            option.selected = true;

                            fetch(`/checklist/by-id/${item.id}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.phases && Array.isArray(data.phases)) {
                                        renderTaskAccordion(data.phases);
                                    }
                                });
                        }

                        select.appendChild(option);
                    });

                    // ✅ Bind change event only after population
                    bindChecklistChangeEvent();
                })
                .catch(err => {
                    console.error("❌ Fehler beim Laden der Checklisten:", err);
                    Swal.fire("Fehler", "Checklisten konnten nicht geladen werden.", "error");
                });
        }
 
        function bindChecklistChangeEvent() {
            const checklistSelect = document.getElementById("checklistSelect");
            if (checklistSelect) {
                checklistSelect.addEventListener("change", function () {
                    const checklistId = this.value;

                    if (checklistId) {
                        fetch(`/checklist/by-id/${checklistId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.phases && Array.isArray(data.phases)) {
                                    renderTaskAccordion(data.phases);
                                } else {
                                    renderEmptyTaskViews();
                                }
                            })
                            .catch(err => {
                                console.error("Fehler beim Laden der ausgewählten Checkliste:", err);
                                renderEmptyTaskViews();
                            });
                    } else {
                        // fallback to default
                        loadProjectTasks(selectedProductId);
                    }
                });
            }
        }
 
        function saveCurrentChecklistToProjectTasks() {
            const checklistSelect = document.getElementById("checklistSelect");
            let checklistId = checklistSelect?.value || selectedChecklistId || null;

            const customerId = selectedCustomerId || null;
            const alternativeId = selectedAlternativeId || null;
            const productId = selectedProductId || null;

            const missing = [];
            if (!checklistId) missing.push("Checkliste");
            if (!customerId) missing.push("Kunde");
            if (!alternativeId) missing.push("Alternative");
            if (!productId) missing.push("Produkt");

            if (missing.length > 0) {
                Swal.fire({
                    title: "Fehlende Angaben",
                    html: `Bitte wähle zuerst:<br><strong>${missing.join(", ")}</strong>`,
                    icon: "warning"
                });
                return;
            }

            const accordion = document.getElementById("accordion-tasks");
            if (!accordion) {
                console.warn("❌ Kein accordion-tasks Element gefunden.");
                Swal.fire("Fehler", "Aufgabenbereich nicht gefunden", "error");
                return;
            }

            const phaseSections = accordion.querySelectorAll(".accordion-content");
            const phases = [];

            console.log("⏳ Parsing", phaseSections.length, "phasen...");

            phaseSections.forEach(section => {
                const phaseId = parseInt(section.getAttribute("data-phase-id"));
                if (!phaseId || isNaN(phaseId)) return;

                const activities = [];

                section.querySelectorAll(".task").forEach(task => {
                    const activityId = task.getAttribute("data-activity-id");
                    const checkbox = task.querySelector("input[type=checkbox]");
                    const parsedId = parseInt(activityId);

                    if (!isNaN(parsedId)) {
                        activities.push({
                            id: parsedId,
                            done: checkbox?.checked ? "true" : "false"
                        });
                    }
                });

                if (activities.length > 0) {
                    phases.push({
                        phase_id: phaseId,
                        activities: activities
                    });
                }
            });

            if (phases.length === 0) {
                console.warn("❌ Keine Aktivitäten gefunden in DOM. Checkliste wird nicht gespeichert.");
                Swal.fire("Keine Aufgaben", "Bitte füge zuerst Aufgaben hinzu.", "info");
                return;
            }

            const payload = {
                checklist_id: parseInt(checklistId),
                customer_id: parseInt(customerId),
                alternative_id: parseInt(alternativeId),
                product_id: parseInt(productId),
                project_id: selectedProjectId ? parseInt(selectedProjectId) : null,
                service: selectedService || null,
                phases: phases
            };

            console.log("📦 Sending Payload to Server:", payload);

            fetch("/customer/project/phase/save", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", "Checkliste wurde gespeichert", "success");
                } else {
                    Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen", "error");
                }
            })
            .catch(async err => {
                console.error("❌ Fehler beim Speichern:", err);
                try {
                    const errorData = await err.json();
                    const html = Object.entries(errorData.errors || {}).map(
                        ([key, val]) => `<b>${key}:</b> ${val}`
                    ).join("<br>");
                    Swal.fire("Validierungsfehler", html || "Unbekannter Fehler", "error");
                } catch {
                    Swal.fire("Fehler", "Unbekannter Fehler beim Speichern", "error");
                }
            });
        }
 
        function saveProjectPhase() {
            const card = document.querySelector(".card.selected");
            if (!card) return;

            const productId = card.getAttribute("data-product-id");
            const customerId = card.getAttribute("data-customer-id");
            const alternativeId = card.getAttribute("data-alternative-id");

            fetch("{{ route('customer.project.phase.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", "Phase wurde gespeichert", "success");
                } else {
                    Swal.fire("Fehler", data.message, "error");
                }
            })
            .catch(err => {
                console.error("Fehler beim Speichern:", err);
                Swal.fire("Fehler", "Speichern fehlgeschlagen", "error");
            });
        }

 

        function selectCard(event, card) {
            if (event.ctrlKey || event.metaKey) {
                card.classList.toggle("selected");
            } else {
                document.querySelectorAll(".card.selected").forEach(c => c.classList.remove("selected"));
                card.classList.add("selected");
            }
        }

        function drag(event) {
            event.dataTransfer.setData("text", event.target.id);
        }

    
        function drop(event) {
            event.preventDefault();
            let cardId = event.dataTransfer.getData("text");
            let card = document.getElementById(cardId);
            let column = event.target.closest(".column");

            if (card && column) {
                const newStatus = column.id;
                const projectId = card.getAttribute("data-lead-product-id");

                // Move the card visually
                column.querySelector(".column-content").appendChild(card);
                card.classList.remove("selected");

                // Send new status to backend
                fetch("{{ route('project.change.status') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({
                        project_id: projectId,
                        new_status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire("Fehler", data.message || "Status konnte nicht geändert werden.", "error");
                    }
                })
                .catch(err => {
                    console.error("Fehler beim Aktualisieren des Status:", err);
                    Swal.fire("Fehler", "Beim Ändern des Status ist ein Fehler aufgetreten.", "error");
                });
            }
        }


    

        function editCard(cardId) {
            let card = document.getElementById(cardId);
            if (!card) return;
            let customerId = card.getAttribute("data-customer-id");
            let alternativeId = card.getAttribute("data-alternative-id");
            window.location.href = `/new_lead_edit/${customerId}/${alternativeId}`;
        }

        function deleteCard(cardId) {
            let card = document.getElementById(cardId);
            if (!card) return;
            let projectId = card.getAttribute("data-lead-product-id");

            Swal.fire({
                title: "Bist du sicher?",
                text: "Projekt wird dauerhaft gelöscht.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ja, löschen",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/delete_lead_product/${projectId}`;
                }
            });
        }
  

       

        
        // Script for adding ansprechtpartner start:

       
            function loadContactPeople(customerId, alternativeId) {
                const container = document.getElementById('contact-peeople');
                container.innerHTML = `<strong>Ansprechpartner:</strong> `;

                fetch(`/contact-people/${customerId}/${alternativeId}`)
                    .then(res => res.json())
                    .then(data => {
                        let peopleHTML = '';

                        if (!data.length) {
                            peopleHTML = `<span class="text-muted mr-2">Keine Ansprechpartner</span>`;
                        } else {
                            peopleHTML = data.map(person => {
                                const name = person.name || '';
                                const relation = person.relation || '?';
                                const phone = person.phone || '-';

                                return `
                                    <span class="badge badge-light contact-pill" title="Kontakt: ${name}">
                                        ${name} (${relation}) – ${phone}
                                    </span>
                                `;
                            }).join(' ');
                        }

                        peopleHTML += `
                            <span class="badge badge-success contact-pill ml-1" style="cursor:pointer;"
                                onclick="openContactModal(${customerId}, ${alternativeId})">
                                + Hinzufügen
                            </span>
                        `;

                        container.innerHTML += peopleHTML;
                    })
                    .catch(err => {
                        console.error("Fehler beim Laden der Ansprechpartner:", err);
                        container.innerHTML += `<span class="text-danger">Fehler beim Laden</span>`;
                    });
            }


            function openContactModal(customerId, alternativeId) {
                $('#contactModal').modal('show');
                document.getElementById('customer_id').value = customerId;
                document.getElementById('alternative_id').value = alternativeId;

                // Reset form
                document.getElementById("contactPeopleForm").reset();

                // Initialize Select2
                setTimeout(() => {
                    $('#relation').select2({
                        tags: true,
                        placeholder: 'Beziehung wählen oder eingeben',
                        width: '100%'
                    });
                }, 10);
            }




        // Script for adding ansprechtpartner end:

      
      
        // ✅ This uses the above function
       
     
        function openProjectLeaderModal() {
            const projectId = selectedProjectId;
            $('#leader_project_id').val(projectId);
            $('#projectLeaderModal').modal('show');

            const select = document.getElementById('project_leader_select');
            select.innerHTML = '<option value="">Bitte wählen</option>';

            fetch('/employees/list')
                .then(res => res.json())
                .then(data => {
                    data.forEach(emp => {
                        const option = document.createElement('option');
                        option.value = emp.id;
                        option.text = `${emp.name} ${emp.lastname}`;
                        option.setAttribute('data-image', emp.image || 'default.png');
                        select.appendChild(option);
                    });

                    if ($.fn.select2 && $('#project_leader_select').hasClass("select2-hidden-accessible")) {
                        $('#project_leader_select').select2('destroy');
                    }

                    $('#project_leader_select').select2({
                        width: '100%',
                        dropdownParent: $('#projectLeaderModal'),
                        templateResult: formatEmpOption,
                        templateSelection: formatEmpOption
                    });
                })
                .catch(err => {
                    console.error("❌ Fehler beim Laden der Mitarbeiter:", err);
                    Swal.fire("Fehler", "Mitarbeiter konnten nicht geladen werden", "error");
                });
        }



        // ✅ Make sure this comes first
            function formatEmpOption(emp) {
                if (!emp.id) return emp.text;
                const image = $(emp.element).data('image') || 'default.png';
                return $(`
                    <span>
                        <img src="/images/employee/${image}" class="rounded-circle mr-2" style="height:26px;width:26px;object-fit:cover;">
                        ${emp.text}
                    </span>
                `);
            }
 
 

            // Comments section :start


                    // Initialize main Quill editor when panel opens
                    function initMainQuill() {
                        const editorContainer = document.getElementById("comment-editor");
                        if (editorContainer && !quillMain) {
                            quillMain = new Quill('#comment-editor', {
                                theme: 'snow',
                                placeholder: 'Schreibe einen Kommentar...',
                                modules: {
                                    toolbar: [
                                        [{ header: [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ list: 'ordered' }, { list: 'bullet' }],
                                        ['link', 'image', 'code-block'],
                                        ['clean']
                                    ]
                                }
                            });
                        }
                    }

                    // Open comment panel
                    function openCommentLayout(projectId, phaseId, activityId) {
                        selectedProjectId = projectId;
                        selectedPhaseId = phaseId;
                        selectedActivityId = activityId;

                        const panel = document.getElementById("comment-panel");
                        panel.classList.remove("hidden");
                        panel.classList.add("active");

                        initMainQuill();

                        // Load customer info
                        fetch(`/comment-meta/${projectId}`)
                            .then(res => res.json())
                            .then(data => {
                                document.getElementById("customer-name").textContent = data.customer_name;
                                document.getElementById("customer-address").textContent = data.address;
                                document.getElementById("customer-product").textContent = data.product;
                            });

                        loadComments(projectId, phaseId, activityId);
                        loadCommentCount(activityId);
                    }

                    // Close comment panel
                    function closeCommentLayout() {
                        const panel = document.getElementById("comment-panel");
                        panel.classList.add("hidden");
                        panel.classList.remove("active");

                        document.getElementById("project-profile")?.classList.add("active");
                        document.querySelector(".project-profile-overlay")?.classList.add("active");
                    }

                    // Load comments
                    function loadComments(projectId, phaseId, activityId) {
                            fetch(`/comments/${projectId}/${phaseId}/${activityId}`)
                                .then(res => res.json())
                                .then(data => {
                                    allComments = data; // store for filtering
                                    renderFilteredComments(allComments);
                                });
                        }


                    // Render comment + replies
                    function renderComment(comment) {
                        let replies = '';
                        comment.replies.forEach(reply => {
                            replies += `
                                <div class="ml-6 mt-3 p-3 rounded-md bg-gray-50 border border-gray-200 shadow-sm">
                                    <div class="text-sm text-gray-700 font-semibold mb-1">${reply.employee.name}</div>
                                    <div class="text-sm text-gray-800">${reply.comment}</div>
                                </div>`;
                        });

                        return `
                            <div class="mb-6 p-5 rounded-lg bg-white border border-gray-300 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="text-base font-semibold text-gray-900 mb-1">${comment.employee.name}</div>
                                        <div class="text-sm text-gray-800 leading-relaxed">${comment.comment}</div>
                                    </div>
                                    <div class="text-xs text-gray-400 ml-4 whitespace-nowrap">ID: ${comment.id}</div>
                                </div>

                                <div class="flex gap-3 text-sm text-gray-500 mt-3">
                                    <button onclick="showReplyForm(${comment.id})" class="hover:text-blue-600 font-medium">💬 Antworten</button>
                                    ${comment.is_my_comment ? `
                                        <button onclick="showEditForm(${comment.id}, \`${comment.comment}\`)" class="hover:text-yellow-600 font-medium">✏️ Bearbeiten</button>
                                        <button onclick="deleteComment(${comment.id})" class="hover:text-red-600 font-medium">🗑 Löschen</button>
                                    ` : ''}
                                </div>

                                <div id="reply-${comment.id}" class="mt-4"></div>
                                ${replies}
                            </div>
                        `;
                    }

                    // Submit main comment
                    function submitCommentFromInput() {
                        if (!quillMain) {
                            console.error("❌ Quill editor not initialized");
                            Swal.fire("Fehler", "Texteditor konnte nicht geladen werden", "error");
                            return;
                        }

                        const commentText = quillMain.root.innerHTML;

                        if (!commentText.trim() || commentText.trim() === "<p><br></p>") {
                            Swal.fire("Fehler", "Kommentar darf nicht leer sein", "warning");
                            return;
                        }

                        submitComment(selectedProjectId, selectedPhaseId, selectedActivityId, commentText);
                        quillMain.setText('');
                    }

                    // Submit to backend
                    function submitComment(projectId, phaseId, activityId, commentText, parentId = null) {
                        fetch("/comments", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
                            },
                            body: JSON.stringify({
                                project_id: projectId,
                                phase_id: phaseId,
                                activity_id: activityId,
                                comment: commentText.trim(),
                                parent_id: parentId
                            })
                        })
                        .then(res => {
                            if (!res.ok) throw res;
                            return res.json();
                        })
                        .then(() => {
                            loadComments(projectId, phaseId, activityId);
                            loadCommentCount(activityId);
                        })
                        .catch(async err => {
                            try {
                                const errorData = await err.json();
                                const html = Object.entries(errorData.errors || {}).map(
                                    ([key, val]) => `<b>${key}:</b> ${val}`
                                ).join("<br>");
                                Swal.fire("Validierungsfehler", html || "Unbekannter Fehler", "error");
                            } catch {
                                Swal.fire("Fehler", "Kommentar konnte nicht gespeichert werden", "error");
                                console.warn("❌ Response is not JSON:", err);
                            }
                        });
                    }

                    // Reply form with Quill
                    function showReplyForm(parentId) {
                        const replyDiv = document.getElementById(`reply-${parentId}`);
                        replyDiv.innerHTML = `
                            <div id="reply-editor-${parentId}" class="h-32 mb-1 border rounded bg-white"></div>
                            <button onclick="submitCommentReply(${parentId})" class="mt-1 bg-blue-600 text-white px-3 py-1 rounded">Antworten</button>
                        `;

                        new Quill(`#reply-editor-${parentId}`, {
                            theme: 'snow',
                            placeholder: 'Antwort schreiben...',
                            modules: {
                                toolbar: [['bold', 'italic'], ['link'], [{ list: 'ordered' }, { list: 'bullet' }]]
                            }
                        });
                    }

                    function submitCommentReply(parentId) {
                        const editor = document.querySelector(`#reply-editor-${parentId} .ql-editor`);
                        const text = editor ? editor.innerHTML : "";

                        if (!text.trim() || text === "<p><br></p>") return;

                        submitComment(selectedProjectId, selectedPhaseId, selectedActivityId, text, parentId);
                        loadCommentCount(selectedActivityId);
                    }

                    function showEditForm(commentId, currentText) {
                        const replyDiv = document.getElementById(`reply-${commentId}`);
                        replyDiv.innerHTML = `
                            <textarea id="edit-text-${commentId}" class="w-full border px-2 py-1 rounded">${currentText}</textarea>
                            <button onclick="updateComment(${commentId})" class="mt-1 bg-yellow-500 text-white px-3 py-1 rounded">Update</button>
                        `;
                    }

                    function updateComment(commentId) {
                        const text = document.getElementById(`edit-text-${commentId}`).value;
                        fetch(`/comments/${commentId}`, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
                            },
                            body: JSON.stringify({ comment: text })
                        }).then(() => loadComments(selectedProjectId, selectedPhaseId, selectedActivityId));
                    }

                    function deleteComment(commentId) {
                        fetch(`/comments/${commentId}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
                            }
                        }).then(() => {
                            loadComments(selectedProjectId, selectedPhaseId, selectedActivityId);
                            loadCommentCount(selectedActivityId);
                        });
                    }

                    function loadCommentCount(activityId) {
                        fetch(`/comments/count/${activityId}`)
                            .then(res => res.json())
                            .then(data => {
                                const count = typeof data === 'number' ? data : data.count;
                                const badge = document.querySelector(`#comment-badge-${activityId}`);
                                if (badge) badge.textContent = count;
                            })
                            .catch(err => console.error("❌ Error loading comment count:", err));
                    }


                    function filterComments() {
                        const query = document.getElementById("comment-search").value.toLowerCase();

                        const filtered = allComments.filter(comment => {
                            const text = comment.comment?.toLowerCase() || '';
                            const name = comment.employee?.name?.toLowerCase() || '';

                            // Check replies too
                            const replyMatch = comment.replies?.some(reply =>
                                reply.comment?.toLowerCase().includes(query) ||
                                reply.employee?.name?.toLowerCase().includes(query)
                            );

                            return text.includes(query) || name.includes(query) || replyMatch;
                        });

                        renderFilteredComments(filtered);
                    }


                    function renderFilteredComments(comments) {
                        const container = document.getElementById("comment-body");
                        container.innerHTML = "";

                        comments.forEach(comment => {
                            container.innerHTML += renderComment(comment);
                        });
                    }



            // Comments section :end

               // File Attachment section :start

                                        
                        Dropzone.options.projectAttachmentDropzone = {
                            paramName: "file",
                            maxFilesize: 10, // MB
                            uploadMultiple: false,
                            parallelUploads: 5,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            init: function () {
                                this.on("sending", function (file, xhr, formData) {
                                    formData.append("project_id", selectedProjectId);
                                    formData.append("phase_id", selectedPhaseId);
                                    formData.append("activity_id", selectedActivityId);
                                });

                                this.on("success", function (file, response) {
                                    console.log("✅ Upload erfolgreich:", response);

                                    const fileList = document.getElementById("file-list");

                                    if (!response || !response.id) {
                                        return refreshFiles();
                                    }

                                    const fileData = response;
                                    const extension = fileData.file_type.toLowerCase();
                                    const imageUrl = `${PROJECT_IMAGE_PATH}/${file.image}?v=${Date.now()}`;
                                    console.log("🖼 Image Debug:", file.image, "Full URL:", imageUrl);

                                    const isImage = /^(jpg|jpeg|png|webp|gif)$/i.test(extension);
                                    const isPDF = extension === 'pdf';

                                    let preview = '';
                                    if (isImage) {
                                        preview = `<img
                                                src="${imageUrl}"
                                                onerror="this.onerror=null; this.src='/images/placeholder.png'"
                                                class="w-16 h-16 object-cover rounded shadow"
                                                alt="${file.image_name}"
                                                />
                                                `;
                                    } else if (isPDF) {
                                        preview = `
                                            <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded">
                                                <embed src="${imageUrl}#toolbar=0" type="application/pdf" class="w-full h-full object-contain" />
                                            </div>`;
                                    } else {
                                        preview = `
                                            <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded">
                                                <i class="feather icon-file-text text-4xl text-gray-600"></i>
                                            </div>`;
                                    }

                                    const item = document.createElement("div");
                                    item.className = "p-3 bg-white shadow rounded flex flex-col justify-between transition duration-300 hover:shadow-lg";

                                    item.innerHTML = `
                                        ${preview}
                                        <div class="mt-2">
                                            <input type="text" class="form-control form-control-sm w-full mb-1"
                                                value="${fileData.image_name}"
                                                onchange="renameAttachment(${fileData.id}, this.value)">
                                            <small class="text-muted block">${fileData.uploader?.name || 'N/A'} · ${new Date(fileData.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <div class="flex justify-between mt-2">
                                            <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-primary">📥 Anzeigen</a>
                                            <button onclick="deleteAttachment(${fileData.id})" class="btn btn-sm btn-danger">🗑 Löschen</button>
                                        </div>
                                    `;

                                    fileList.prepend(item);
                                    updateFileCountBadge(selectedActivityId);
                                });

                                this.on("queuecomplete", function () {
                                    const modalEl = document.getElementById('uploadModal');
                                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                    if (modalInstance) modalInstance.hide();

                                    this.removeAllFiles();

                                    setTimeout(() => {
                                        loadProjectAttachments();
                                        loadFilesForActivity(selectedActivityId);
                                    }, 600);
                                });

                                this.on("error", function (file, errorMessage, xhr) {
                                    console.error("❌ Upload failed:", errorMessage);

                                    let message = "Unbekannter Fehler beim Upload";
                                    if (xhr && xhr.response) {
                                        try {
                                            const data = JSON.parse(xhr.response);
                                            message = Object.values(data.errors).flat().join("\n");
                                        } catch (e) {
                                            message = xhr.response;
                                        }
                                    }

                                    Swal.fire("Fehler", message, "error");
                                });
                            } // <-- Correct final closing bracket
                        };

 

                    function openUploadModal() {
                        document.getElementById('dz_project_id').value = selectedProjectId;
                        document.getElementById('dz_phase_id').value = selectedPhaseId;
                        document.getElementById('dz_activity_id').value = selectedActivityId;

                        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
                        modal.show();
                    }


                    function openFilePanel(projectId, phaseId, activityId) {
                            selectedProjectId = projectId;
                            selectedPhaseId = phaseId;
                            selectedActivityId = activityId;

                            // Hide the project sidebar
                            document.getElementById("project-profile")?.classList.remove("active");

                            // Show the file panel with transition
                            const filePanel = document.getElementById("file-panel");
                            filePanel.classList.remove("hidden");
                            setTimeout(() => filePanel.classList.add("active"), 10);

                            // Load files for this activity
                            loadFilesForActivity(activityId);
                            updateFileCountBadge(activityId); // 🔁 refresh the badge too

                            
                        }


                    function closeFilePanel() {
                        const filePanel = document.getElementById("file-panel");
                        filePanel.classList.remove("active");
                        setTimeout(() => filePanel.classList.add("hidden"), 300); // match CSS transition

                        // Show project sidebar again
                        document.getElementById("project-profile")?.classList.add("active");
                        }

                    function loadProjectAttachments() {
                        if (!selectedProjectId) {
                            console.error("❌ selectedProjectId is not defined");
                            return;
                        }

                        const container = document.getElementById("attachment-list");
                        container.innerHTML = `<div class="text-center text-muted">📂 Dateien werden geladen...</div>`;

                        fetch(`/attachments/${selectedProjectId}/${selectedPhaseId}/${selectedActivityId}`)
                            .then(res => res.json())
                            .then(data => {
                                container.innerHTML = "";

                                if (!data.length) {
                                    container.innerHTML = `<div class="text-center text-muted">Keine Dateien gefunden.</div>`;
                                    return;
                                }

                                data.forEach(file => {
                                    const extension = file.file_type.toLowerCase();
                                    const fileUrl = `${PROJECT_IMAGE_PATH}/${file.image}?v=${Date.now()}`;
                                    console.log("🖼", file.image, "->", fileUrl);

                                    const isImage = /^(jpg|jpeg|png|webp|gif)$/i.test(extension);

                                    const isPDF = extension === 'pdf';

                                    let preview = '';
                                    if (isImage) {
                                        preview = `<img
                                                    src="${fileUrl}"
                                                    class="w-16 h-16 object-cover rounded shadow"
                                                    alt="${file.image_name}"
                                                    onerror="this.onerror=null; this.src='/images/placeholder.png'"
                                                    />`;
                                    } else if (isPDF) {
                                        preview = `<embed src="${fileUrl}#toolbar=0" type="application/pdf" class="w-16 h-16 object-contain rounded shadow bg-gray-100" />`;
                                    } else {
                                        preview = `<i class="feather icon-file-text text-3xl text-gray-500"></i>`;
                                    }

                                    const item = document.createElement("div");
                                    item.className = "p-3 border rounded mb-3 flex items-start gap-3 bg-white shadow-sm";

                                    item.innerHTML = `
                                        <div>${preview}</div>
                                        <div class="flex-1">
                                            <input type="text" class="form-control form-control-sm w-full mb-1"
                                                value="${file.image_name}"
                                                onchange="renameAttachment(${file.id}, this.value)">
                                            <small class="text-muted block">${file.uploader?.name || 'N/A'} · ${new Date(file.created_at).toLocaleDateString()}</small>
                                            <div class="flex gap-2 mt-2">
                                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-info">👁 Anzeigen</a>
                                                <button onclick="deleteAttachment(${file.id})" class="btn btn-sm btn-danger">🗑 Löschen</button>
                                            </div>
                                        </div>
                                    `;

                                    container.appendChild(item);
                                });
                            })
                            .catch(err => {
                                console.error("❌ Fehler beim Laden der Dateien:", err);
                                container.innerHTML = `<div class="text-danger">Fehler beim Laden der Dateien.</div>`;
                            });
                    }

                   
                    function deleteAttachment(id) {
                        Swal.fire({
                            title: 'Bist du sicher?',
                            text: "Diese Datei wird dauerhaft gelöscht!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ja, löschen',
                            cancelButtonText: 'Abbrechen'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/attachments/${id}`, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        loadProjectAttachments();
                                        loadFilesForActivity(selectedActivityId);
                                        updateFileCountBadge(selectedActivityId);

                                        Swal.fire('Gelöscht!', 'Die Datei wurde entfernt.', 'success');
                                    } else {
                                        Swal.fire('Fehler', 'Konnte Datei nicht löschen.', 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Fehler', 'Verbindungsfehler beim Löschen.', 'error');
                                });
                            }
                        });
                    }

                    function openAttachmentModal(url) {
                        document.getElementById("filePreviewFrame").src = url;
                        $('#previewModal').modal('show');
                    }


                    function loadFilesForActivity(activityId) {
                        selectedActivityId = activityId;

                        const fileList = document.getElementById("file-list");
                        fileList.innerHTML = `<div class="text-center text-muted col-span-3">📂 Dateien werden geladen...</div>`;

                        fetch(`/attachments/activity/${activityId}`)
                            .then(res => res.json())
                            .then(data => {
                                fileDataList = data;
                                renderFileList(fileDataList);
                            })
                            .catch(err => {
                                console.error("❌ Fehler beim Laden der Dateien:", err);
                                fileList.innerHTML = `<div class="text-danger">Fehler beim Laden der Dateien.</div>`;
                            });
                    }


                   
                    function refreshFiles() {
                        if (!selectedActivityId) {
                            console.warn("❗ Kein Aktivitäts-ID ausgewählt");
                            return;
                        }

                        const fileList = document.getElementById("file-list");
                        fileList.innerHTML = `<div class="text-center text-muted col-span-3">🔄 Wird aktualisiert...</div>`;
                        
                        loadFilesForActivity(selectedActivityId);
                    }


                    document.addEventListener("DOMContentLoaded", function () {
                    const searchInput = document.getElementById("fileSearch");

                    if (searchInput) {
                        searchInput.addEventListener("keypress", function (event) {
                            if (event.key === "Enter") {
                                event.preventDefault();
                                const query = this.value.trim().toLowerCase();

                                const filtered = fileDataList.filter(file => {
                                    const name = file.image_name || file.name || file.original_name || "";
                                    return name.toLowerCase().includes(query);
                                });

                                console.log("🔍 Filtered by ENTER:", filtered);
                                renderFileList(filtered);
                            }
                        });
                    }
                });






                        function renameAttachment(id, newName) {
                            fetch(`/attachments/rename`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                                },
                                body: JSON.stringify({
                                    id: id,
                                    image_name: newName
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('✅ Umbenannt!', data.message, 'success');
                                } else {
                                    Swal.fire('❌ Fehler', 'Konnte nicht umbenennen.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error("❌ Rename Error:", err);
                                Swal.fire('❌ Fehler', 'Fehler beim Umbenennen.', 'error');
                            });
                        }



               
                function loadFilesForActivity(activityId) {
                    const fileList = document.getElementById("file-list");
                    fileList.innerHTML = `<div class="text-center text-muted col-span-3">📂 Dateien werden geladen...</div>`;

                    fetch(`/attachments/activity/${activityId}`)
                        .then(res => res.json())
                        .then(data => {
                            fileDataList = data; // ⬅️ Store full list for filtering
                            renderFileList(fileDataList);
                        })
                        .catch(err => {
                            console.error("❌ Fehler beim Laden der Dateien:", err);
                            fileList.innerHTML = `<div class="text-danger">Fehler beim Laden der Dateien.</div>`;
                        });
                }


               function renderFileList(data) {
                    const fileList = document.getElementById("file-list");
                    fileList.innerHTML = "";

                    if (!data.length) {
                        fileList.innerHTML = `<div class="text-center text-muted col-span-3">❌ Keine passenden Dateien gefunden.</div>`;
                        return;
                    }

                    data.forEach(file => {
                        const extension = file.file_type.toLowerCase();
                        const imageUrl = `${PROJECT_IMAGE_PATH}/${file.image}?v=${Date.now()}`;
                        console.log("🖼 Image Debug:", file.image, "Full URL:", imageUrl);


                       const isImage = /^(jpg|jpeg|png|webp|gif)$/i.test(extension);
                        const isPDF = extension === 'pdf';

                        let preview = '';
                        if (isImage) {
                            preview = `<img src="${imageUrl}" class="w-full h-40 object-cover rounded" alt="${file.image_name}">`;
                        } else if (isPDF) {
                            preview = `<div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded">
                                            <embed src="${imageUrl}#toolbar=0" type="application/pdf" class="w-full h-full object-contain" />
                                        </div>`;
                        } else {
                            preview = `<div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded">
                                            <i class="feather icon-file-text text-4xl text-gray-600"></i>
                                        </div>`;
                        }

                        const item = document.createElement("div");
                        item.className = "p-3 bg-white shadow rounded flex flex-col justify-between transition duration-300 hover:shadow-lg";

                        item.innerHTML = `
                            ${preview}
                            <div class="mt-2">
                                <input type="text" class="form-control form-control-sm w-full mb-1"
                                    value="${file.image_name}"
                                    onchange="renameAttachment(${file.id}, this.value)">
                                <small class="text-muted block">${file.uploader?.name || 'N/A'} · ${new Date(file.created_at).toLocaleDateString()}</small>
                            </div>
                            <div class="flex justify-between mt-2">
                                <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-primary">📥 Anzeigen</a>
                                <button onclick="deleteAttachment(${file.id})" class="btn btn-sm btn-danger">🗑 Löschen</button>
                            </div>
                        `;

                        fileList.appendChild(item);
                    });
                }



            function updateFileCountBadge(activityId) {
                fetch(`/attachments/activity/${activityId}`)
                    .then(res => res.json())
                    .then(data => {
                        const count = data.length;
                        const badge = document.querySelector(`.file-count-badge[data-activity-id="${activityId}"]`);
                        if (badge) {
                            badge.textContent = count;
                        }
                    })
                    .catch(err => {
                        console.error("❌ Fehler beim Aktualisieren des Datei-Counters:", err);
                    });
            }
 
 
               // File Attachment section :end

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("save-person-leader")) {
            const form = document.getElementById("projectLeaderForm");
            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", `Projektleiter: ${data.leader_name}`, "success");
                    $('#projectLeaderModal').modal('hide');

                    // Update Leader Display Section (top of profile)
                    const nameDisplay = document.getElementById("project-leader-name");
                    const imageDisplay = document.getElementById("project-leader-image");

                    if (nameDisplay) nameDisplay.textContent = data.leader_name;
                    if (imageDisplay) imageDisplay.src = `/images/employee/${data.leader_image || 'default.png'}`;

                    // Get image from selected option in Select2
                    const select = document.getElementById("project_leader_select");
                    const selectedOption = select.options[select.selectedIndex];
                    const image = selectedOption?.dataset.image || 'default.png';

                    // Update Employee Card UI (if card is selected)
                    const selectedCard = document.querySelector('.card.selected .employeeList');
                    if (selectedCard) {
                        selectedCard.innerHTML = `
                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                <li class="avatar pull-up" title="${data.leader_name}">
                                    <img class="media-object rounded-circle" src="/images/employee/${image}" alt="${data.leader_name}" height="25" width="25">
                                </li>
                            </ul>
                        `;
                    }
                } else {
                    Swal.fire("Fehler", data.message || "Projektleiter konnte nicht gespeichert werden.", "error");
                }
            })
            .catch(err => {
                console.error("❌ Fehler beim Speichern:", err);
                Swal.fire("Fehler", "Unbekannter Fehler beim Speichern", "error");
            });
        }
    });
});
</script>



<script>
$(document).on('click', '.add-employees-btn', function () {
    const projectId = $(this).data('project-id');
    
    // Set the project ID inside the modal
    $('#modal_project_id').val(projectId);

    // Optional: reset old employee or set it if needed
    $('#modal_old_employee').val('');

    // Show the modal
    $('#employee').modal('show');
});
 
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById("contactPeopleForm");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        console.log("Form submitted via JS");

        fetch("/contact-people/save", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(async res => {
            if (!res.ok) throw res;
            const text = await res.text();
            if (!text.startsWith("{")) throw new Error("Keine gültige JSON Antwort");
            return JSON.parse(text);
        })
        .then(data => {
            if (data.success) {
                Swal.fire("Gespeichert!", data.message || "Kontaktperson wurde gespeichert.", "success");
                $('#contactModal').modal('hide');

                const customerId = document.getElementById("customer_id").value;
                const alternativeId = document.getElementById("alternative_id").value;
                loadContactPeople(customerId, alternativeId);
            } else {
                Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen.", "error");
            }
        })
        .catch(err => {
            console.error("Fehler beim Speichern:", err);
            Swal.fire("Fehler", "Fehler beim Speichern oder ungültige Server-Antwort", "error");
        });
    });
});

</script>


<script>
    // ✅ OPEN LOG PANEL GLOBALLY
    window.openLogPanel = function(projectId, phaseId, activityId) {
        console.log('✅ openLogPanel fired:', projectId, phaseId, activityId);

        // Save to global scope for later use
        window.selectedProjectId = projectId;
        window.selectedPhaseId = phaseId;
        window.selectedActivityId = activityId;

        // Show the sliding panel
        $('#timeline-log-panel').addClass('active');

        // Show loading message
        $('#timeline-log-content').html('<p class="text-center text-muted">⏳ Lade Daten...</p>');

        // Load logs
        window.fetchLogs(projectId, phaseId, activityId);

        // Bind filters
        $('#log-employee-filter, #log-date-filter').off().on('change', function () {
            window.fetchLogs(projectId, phaseId, activityId);
        });
    }

    // ✅ FETCH LOGS
    window.fetchLogs = function(projectId, phaseId, activityId) {
        const empId = $('#log-employee-filter').val();
        const date = $('#log-date-filter').val();

        $.ajax({
            url: `/project-timeline/logs/${projectId}/${phaseId}/${activityId}`,
            method: 'GET',
            data: {
                emp_id: empId,
                date: date
            },
            success: function (response) {
                // 🟡 Render logs
                $('#timeline-log-content').html(response.html);

                // 🟡 Update progress bar visually
                const progress = parseInt(response.progress) || 0;
                $('#timeline-progress-bar')
                    .css('width', progress + '%');

                // 🟡 Update progress text
                $('#progress-text')
                    .text('Fortschritt: ' + progress + '%');
            },
            error: function () {
                $('#timeline-log-content').html('<p class="text-danger text-center">❌ Fehler beim Laden.</p>');
                $('#timeline-progress-bar').css('width', '0%');
                $('#progress-text').text('Fortschritt: 0%');
            }
        });
    }


    // ✅ CLOSE PANEL
    window.closeLogPanel = function () {
        $('#timeline-log-panel').removeClass('active');
    }

    // ✅ OPEN ADD MODAL
    window.showAddLogModal = function () {
        $('#addLogModal').modal('show');
        $('#log_project_id').val(window.selectedProjectId);
        $('#log_timeline_id').val(window.selectedTimelineId); // You must define this elsewhere!
    }

    // ✅ SAVE NEW LOG
    $(document).on('submit', '#add-log-form', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/project-timeline/logs/add',
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                $('#addLogModal').modal('hide');
                fetchLogs(window.selectedProjectId, window.selectedPhaseId, window.selectedActivityId);
            },
            error: function () {
                alert('Fehler beim Speichern.');
            }
        });
    });


    // ✅ Close the panel when clicking outside
    window.closeLogPanel = function() {
        $('#timeline-log-panel').removeClass('active');
    };

    // Click outside to close
    $(document).on('click', function(e) {
        const panel = $('#timeline-log-panel');
        if (panel.hasClass('active') &&
            !$(e.target).closest('#timeline-log-panel').length &&
            !$(e.target).closest('[onclick^="openLogPanel"]').length) {
            panel.removeClass('active');
        }
    });

    // ESC key to close
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#timeline-log-panel').removeClass('active');
        }
    });

</script>

@endsection


