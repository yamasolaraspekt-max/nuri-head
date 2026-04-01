@extends('admin.layouts.app')
@section('title')
KUNDE PROFILE
@endsection

@section('style')
 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- In your main Blade layout (e.g. admin.layouts.app or similar) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">

<style>
    .card-img-top.lazy {
    object-fit: cover;
    height: 180px;
    width: 100%;
}
</style>

<style>
.phase-table th,
.phase-table td {
    vertical-align: middle;
    font-size: 14px;
}

.phase-table .bg-success {
    background-color: #cce5b1 !important;
    color: #2c3e50;
}

.phase-table td {
    padding: 8px 10px;
}
</style>

<style>

.section-content {
    max-height: calc(100vh - 120px); /* adjust depending on your header height */
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 10px; /* space for scrollbar */
    scroll-behavior: smooth;
}

    .section-content::-webkit-scrollbar {
    width: 8px;
    }

    .section-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .section-content::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }

    .section-content::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

</style>

<!-- Kanban  -->
   <style>
        .kanban-board {
        overflow-x: auto;
        padding-bottom: 10px;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .kanban-board .kanban-header,
    .kanban-board .kanban-body {
        display: flex;
        flex-wrap: nowrap;
    }

    .kanban-column {
        width: 320px;
        min-width: 300px;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .kanban-title {
        background: #8fc73e;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 12px;
        border-radius: 6px 6px 0 0;
        font-size: 15px;
    }

    .kanban-dropzone {
        min-height: 350px;
        background-color: #f9fafb;
        padding: 10px;
        border: 1px dashed #ccc;
        border-top: none;
        border-radius: 0 0 6px 6px;
        transition: background 0.3s ease;
    }

    .kanban-dropzone:hover {
        background-color: #f1f5f9;
    }

    .kanban-card {
    background: #fff;
    border-left: 4px solid transparent; /* dynamically updated in Blade */
    transition:
        border-color 0.2s ease,
        transform 0.2s ease,
        box-shadow 0.2s ease;
    margin-bottom: 10px;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    position: relative;
    cursor: grab;
}

/* On hover: slightly lift the card */
    .kanban-card:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    /* Optional: fade "junk" cards */
    .kanban-card.junk {
        opacity: 0.6;
        border-left-color: #d1d5db;
        cursor: not-allowed;
    }

    /* Responsive support */
    @media (max-width: 576px) {
        .kanban-card {
            padding: 12px;
        }
    }

    .kanban-card .disable-pointer {
        pointer-events: none;
    }

    .kanban-card .rounded-circle {
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .kanban-card i {
        opacity: 0.6;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }

    .kanban-card i:hover {
        opacity: 1;
    }

    /* Badge in top-right corner */
    .kanban-card .initial-badge {
        position: absolute;
        top: 8px;
        right: 10px;
        background-color: #f1f5f9;
        color: #666;
        font-weight: bold;
        font-size: 11px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive fallback */
    @media (max-width: 576px) {
        .kanban-column {
            width: 90vw;
        }

        .kanban-card {
            padding: 12px;
        }

        .kanban-title {
            font-size: 14px;
        }
    }

   </style>

   <style>

    #note-scroll-wrapper {
        display: flex;
         flex-direction:column !important;
        height: 100%;
        padding-bottom: 60px; /* space for composer */
        scroll-behavior: smooth;
    }

    .note-card .card-body {
    
        background-color: #f1f0f0;
    }

    .note-card .dropdown-menu {
        font-size: 0.875rem;
    }

    .note-card img {
        object-fit: cover;
    }

    #note-container {
        max-height: 80%;
    }


    .modal-body {
        padding: 0;
        overflow: hidden;
    }

    .note-composer {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 500px;
        max-height: 0;
        overflow: hidden;
        padding: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 1050;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .note-composer.open {
        max-height: 300px;
        padding: 1rem;
    }

    .note-composer .submit-wrapper {
        display: none;
    }

    .note-composer.open .submit-wrapper {
        display: block;
    }


    .note-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1049;
    }

    #note-list {
    display: flex;
    flex-direction: column;
    width: 100%;
}


.note-reply {
    transition: opacity 0.4s ease;
}

.reply-container .card {
    background: #f8f9fa;
    border-left: 2px solid #ccc;
    margin-top: 0.5rem;
}

  
.animated-reply {
    animation: pulse 0.4s ease;
}

.reply-wrapper {
    overflow-anchor: auto;
    scroll-margin-bottom: 80px;
}


@keyframes pulse {
    0%   { transform: scale(1); background-color: #e7f6ff; }
    50%  { transform: scale(1.015); background-color: #d4f1ff; }
    100% { transform: scale(1); background-color: inherit; }
}

   </style>

   <style>
    .panel-toggle-btn {
        position: absolute;
        top: -40px;
        z-index: 9999;
        background: white;
        border: 1px solid #ccc;
        padding: 6px 10px;
        border-radius: 4px;
        color: #8fc73e;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
   </style>
 <style>
.delete-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    border: none;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    line-height: 20px;
    text-align: center;
}
.fade-in {
    opacity: 1;
}
.fade-out {
    opacity: 0 !important;
    transition: opacity 0.4s ease-out;
}
.card {
    box-shadow: 0 0 !important;
}

.main-fullscreen {
    width: 100% !important;
    flex: 1 1 auto;
    padding: 1rem !important;
}

.main-fullscreen-mode .customerSidebar,
.main-fullscreen-mode .right-panel {
    display: none !important;
}

.main-fullscreen-mode .contentStation {
    width: 100% !important;
    flex: 1 1 auto !important;
    max-width: 100% !important;
    padding: 1rem !important;
}
.contentStation {
    transition: all 0.3s ease;
}


.right-panel.fullscreen {
    position: relative;
    width: 100% !important;
    max-width: 100%;
    flex: 1 1 auto;
    z-index: 999;
    background: #fff;
    transition: all 0.3s ease;
}

.right-panel.fullscreen .note-scroll-wrapper {
    height: calc(100vh - 120px); /* adjust if needed */
    overflow-y: auto;
}


.contentStation.expanded {
    flex: 1 1 auto;
    width: auto !important;
}


.customerSidebar {
  width: 300px;
  flex-shrink: 0;
  transition: width 0.3s ease;
}

.customerSidebar.minimized {
  width: 60px;
  padding: 1rem 0.5rem;
}

.layout {
  overflow: hidden;
}

 

.contentStation {
  flex: 1 1 auto;           /* 👈 ALLOW it to grow */
  min-width: 0;             /* 👈 Prevent overflow */
  background: #ccc;
  transition: all 0.3s ease;
}

.right-panel {
  width: 360px;
  flex-shrink: 0;
  background: #f6f6f6;
  transition: all 0.3s ease;
  border-left: 1px solid #ccc;
}

#product_version_details {
    background: #ffffff00;
    width: 50px;
    justify-items: anchor-center;
    text-align: center;
    color: #686868;
    font-size: 11px;
}

</style>

<style>
    #deletedNotesModalBody .card-body  {
            background:white !important;
    }

    #deletedNotesModalBody .btn-success  {
            margin-bottom:0 !important;
    }
</style>

<style>
    /* Color when a collapse is expanded */
    .card .collapse.show {
        background-color:rgb(255, 255, 255);
    }

    /* Optional: color the header too when open */
    .card-header.active-stage {
        background-color: #c0d8ea !important;
        color: black !important;
    }

    #phaseSidebar .phase-sidebar-body .card-header  {
        padding:1rem !important;
    }

    #phaseSidebar .phase-sidebar-header h5 { 
            font-size: 24px;
            font-weight: bold;
            color: white;

    }


    .phase-sidebar {
    position: fixed;
    top: 0;
    right: -100%;
    width: 90%; 
    height: 100%;
    background: #fff;
    box-shadow: -4px 0 8px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
    }

    .phase-sidebar.open {
    right: 0;
    }

    .phase-sidebar-header {
    padding: 1rem;
    background: #2c3e4f;
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .phase-sidebar-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
    }

    .close-btn {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    }

    #phaseProductInitial { 
        padding: 10px;
        font-size: 20px;
        border-radius: 50%;
        color: #cfe09b;
        font-weight: bold;
        background-color: #ffffff !important;
        width: 50px;
        height: 50px;
        place-content: center;
    }


    .upload-icons,
    .file-icons {
    font-size: 22px;
    color: #cfdf9b;
    transition: 0.2s ease;
    }

    .upload-icons:hover,
    .file-icons:hover {
    color: #dc3545; /* red on hover */
    transform: scale(1.1);
    cursor: pointer;
    }

    .entry-col .badge {
        font-size: 10px;
        padding: 2px 6px;
        margin-bottom: 2px;
    }

    .badge-dark {
    background-color: #343a40;
    color: #fff;
    font-weight: 600;
    font-size: 11px;
}


#next_phase_station button {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.section-content {
    background:white;
}

#toggleNewNoteBtn , #btnToggleRightPanelFullscreen, #loadAllDeletedNotes {
    margin-right:5px;
    margin-left:1px;
}

.stage-icons {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    margin-top: -20px;
    background: #f8f9fa;
    border-radius: 8px;
}

 

.stage-icons i:hover {
    transform: scale(1.1);
    background: #e2e6ea;
}

 

 

.stage-icons i:hover {
    background: #d4edda;
    cursor: pointer;
}

.stage-icons i {
    font-size: 14px;
    padding: 10px;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
    cursor: pointer;
    color:white !important;
}

.stage-icons i:hover {
    transform: scale(1.1);
}


 

</style>


<style>
.report-sidebar {
    position: fixed;
    top: 0;
    right: -100%;
    width: 80%;
    height: 100%;
    background: #fff;
    z-index: 1050;
    transition: all 0.4s ease-in-out;
    box-shadow: -2px 0 6px rgba(0,0,0,0.2);
}

.report-sidebar.open {
    right: 0;
}

.report-sidebar.open {
    right: 0;
}

.timeline .timeline-item {
    border-left: 3px solid #93c21c;
    margin-left: 10px;
    padding-left: 15px;
    margin-bottom: 20px;
    position: relative;
}

.timeline .timeline-item::before {
    content: "";
    position: absolute;
    left: -10px;
    top: 0;
    background: #93c21c;
    border-radius: 50%;
    width: 10px;
    height: 10px;
}

.timeline .timeline-item::before {
    background: #add33e; /* Lime green dot */
}

.timeline .timeline-item {
    border-left: 3px solid #add33e;
    background: #f9f9f9;
    border-radius: 5px;
    padding: 10px 15px;
}

.timeline .timeline-item:hover {
    background: #eefbe0;
}
.report-header button i {
    font-size: 16px;
}
.toggle-report-form {
    background-color: #93c119 !important;
    color: white;
}
.report-form-modal {
    position: fixed;
    top: 60px;
    right: 0;
    width: 500px;
    height: calc(100% - 60px);
    background: #00000003;
    z-index: 1060;
    display: flex;
    align-items: center;
    justify-content: center;
}

.report-form-modal .modal-content {
    width: 500px;
    max-height: 95%;
    overflow-y: auto;
    border-radius: 8px;
}
.report-form-modal {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1080;
}

.bg-warning-light {
    background-color: #fff3cd !important;
    transition: background-color 0.4s;
}


.reply-preview {
    background-color: #f0f8e8;
    padding: 5px 10px;
    border-left: 3px solid #93c21c;
    border-radius: 4px;
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
                        <h2 class="content-header-title float-left mb-0">KUNDENPROFIL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                <li class="breadcrumb-item active">{{ $customer->name }} {{ $customer->lastname }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Insert the full HTML layout from above here (customer-nav, layout, sidebar, main-content, right-panel) -->
            @include('admin.new_leads.layouts.profile') 
        </div>


        <div class="modal fade" id="newProductModal" tabindex="-1" role="dialog" aria-labelledby="newProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <form method="POST" action="{{ route('lead_product_lists.bulk.store') }}">
                @csrf
                <input type="hidden" name="customer_id" id="product_customer_id">
                <input type="hidden" name="alternative_id" id="product_alternative_id">


                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Neues Produkt hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                    @include('admin.new_leads.layouts.new_product_form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveProductRows" class="btn btn-success float-right mt-2">
                            <i class="feather icon-save"></i> Speichern
                        </button>

                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Report Slider for each customer product  -->  
           <div id="reportSidebar" class="report-sidebar " style="display:none;">
                <div class="report-header d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0">Kundenprozessbericht</h5>
                    <div>
                        <button class="btn btn-sm btn-primary mr-1 toggle-report-form"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-danger close-report-sidebar"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div id="reportList" class="p-3 timeline overflow-auto" style="height: calc(100% - 60px);"></div>

                <!-- 🔻 FORM AS MODAL OVERLAY -->
                <div id="reportFormContainer" class="report-form-modal" style="display:none;">
                    <div class="modal-content bg-white p-4 ">
                        <form id="reportForm">
                            <input type="hidden" name="product_id">
                            <input type="hidden" name="customer_id">
                            <input type="hidden" name="alternative_id">
                            <div class="form-group">
                                <label>Datum</label>
                                <input type="date" name="report_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">

                            </div>
                            <div class="form-group">
                                <label>Stage</label>
                                <select name="stage" class="form-control" required>
                                    <option value="">-- auswählen --</option>
                                        @php
                                            $stageLabels = [
                                                'lead'      => 'Anfrage',
                                                'offer'     => 'Angebot',
                                                'deal'      => 'Vertrag',
                                                'project'   => 'Projekt',
                                                'complete'  => 'Abgeschlossen',
                                                'review'    => 'Überprüfung',
                                                'archive'   => 'Archiviert',
                                                'ticket'    => 'Ticket',
                                                'pause'     => 'Pausiert',
                                                'cancel'    => 'Storniert',
                                            ];
                                        @endphp

                                        @foreach($stageLabels as $stage => $label)
                                            <option value="{{ $stage }}">{{ $label }}</option>
                                        @endforeach

                                </select>
                            </div>
                            <div class="form-group">
                                <label>Bericht</label>
                                <div id="quill-editor" style="height: 150px;"></div>
                                <input type="hidden" name="report">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Speichern</button>
                                <button type="button" class="btn btn-light close-report-form">Abbrechen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
 <!-- Report Slider for each customer product  --> 


        <div id="commentSidebar" class="report-sidebar shadow" style="display:none;">
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <h5 class="mb-0">Kommentare</h5>
                <div>
                    <button class="btn btn-sm btn-success mr-2 open-comment-form"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-sm btn-danger close-comment-sidebar">×</button>
                </div>
            </div>
            <div id="commentContent" class="p-3 overflow-auto" style="height: calc(100% - 50px);"></div>

            <!-- 🔻 Modal Form -->
                <div id="commentFormModal" class="report-form-modal" style="display:none;">
                    <div class="modal-content bg-white p-3 shadow" style="width: 90%; max-width: 500px; position:relative;">
                        <button type="button" class="btn btn-sm btn-danger close-comment-form"
                                style="position:absolute; top:8px; right:8px; line-height:1;">×</button>

                        <form id="newCommentForm" class="mt-3">
                            <input type="hidden" name="report_id" id="report_id">
                            <input type="hidden" name="parent_id" id="parent_id">
                            <div id="commentMeta"></div>
                            <div id="quotedComment" class="alert alert-light py-2 px-3" style="display:none;"></div>

                            <textarea name="comment" class="form-control" rows="3" placeholder="Kommentieren..." required></textarea>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-light mr-2 close-comment-form">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Senden</button>
                            </div>
                        </form>
                    </div>
                </div>


        </div>


 
    </div>
</div>
@endsection

@section('script')

 
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
<script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"
  async defer></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('customerSidebar');
        const main = document.getElementById('mainContent');
        let sidebarManuallyExpanded = false;


     

        // ✅ Force-close all collapses on initial load
        document.querySelectorAll('.product-list, .sub-nav').forEach(el => {
            el.classList.remove('show');
            el.setAttribute('aria-expanded', 'false');
        });

        function collapseAll() {
            document.querySelectorAll('.product-list').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        }

        function minimizeSidebar() {
            sidebar.classList.add('minimized');
            document.querySelector('.contentStation')?.classList.add('expanded');
            collapseAll();
        }

        function expandSidebar() {
            sidebar.classList.remove('minimized');
            document.querySelector('.contentStation')?.classList.remove('expanded');
            sidebarManuallyExpanded = true;
            feather.replace();
        }

        function autoToggleSidebar() {
            if (!sidebarManuallyExpanded) {
                window.innerWidth < 992 ? minimizeSidebar() : expandSidebar();
            }
        }

        autoToggleSidebar();
        window.addEventListener('resize', autoToggleSidebar);

        window.togglecustomerSidebar = () => {
            sidebar.classList.contains('minimized') ? expandSidebar() : minimizeSidebar();
        };

        document.addEventListener('click', function (e) {
            const shouldExpand =
                e.target.closest('.object-header') ||
                e.target.closest('.project-link') ||
                e.target.closest('.sub-nav button') ||
                e.target.closest('.dashboard-btn');

            if (sidebar.classList.contains('minimized') && shouldExpand) {
                expandSidebar();
            }
        });


        window.toggleObject = (id) => {
            const target = document.getElementById(id);
            if (!target) return;

            const isOpen = target.style.display === 'block';

            // Collapse all other objects
            document.querySelectorAll('.product-list').forEach(el => el.style.display = 'none');

            if (!isOpen) {
                target.style.display = 'block';
                console.log(`[Object] ${id} → now open`);
            } else {
                target.style.display = 'none';
                console.log(`[Object] ${id} → now closed`);
            }

            // Reset all sub-sections
            document.querySelectorAll('.sub-nav').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        };

        window.toggleProduct = (productKey) => {
            const subNav = document.getElementById(productKey);
            const clicked = document.querySelector(`.project-link[data-product-key="${productKey}"]`);
            const parentId = productKey.match(/product(\d+)_\d+/)?.[1];
            const parentObjectList = document.getElementById(`object${parentId}`);
            const title = document.getElementById('note_title');
            if (title) title.textContent = 'NOTIZEN';

            if (!subNav || !clicked || !parentObjectList) return;

            const isOpen = subNav.style.display === 'block';

            // Collapse all sub-navs
            document.querySelectorAll('.sub-nav').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));

            if (!isOpen) {
                parentObjectList.style.display = 'block';
                subNav.style.display = 'block';
                clicked.classList.add('active');
                console.log(`[Product] ${productKey} was closed → now open`);

                const customerId = clicked.dataset.objectCustomerId;
                const alternativeId = clicked.dataset.objectAlternativeId;
                const productId = clicked.dataset.objectProduct;
                const noteList = document.getElementById('note-list');

                if (noteList && customerId && alternativeId && productId) {
                    noteList.innerHTML = '<div class="text-muted">Lade Notizen...</div>';
                    fetch(`/customer-notes/${customerId}/${alternativeId}/${productId}`)
                        .then(res => res.text())
                        .then(html => {
                            noteList.innerHTML = html;
                            feather.replace();
                            if (typeof initNoteListeners === 'function') initNoteListeners();
                        })
                        .catch(() => {
                            noteList.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
                        });
                }
            } else {
                console.log(`[Product] ${productKey} was open → now closed`);
            }
        };
 
        // ✅ Auto-bind click to all project links
        document.querySelectorAll('.project-link[data-product-key]').forEach(link => {
            link.addEventListener('click', function () {
                const productKey = this.dataset.productKey;
                toggleProduct(productKey);
            });
        });
    });


</script>


<!-- Maximize Toggle Buttons  -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mainContentToggle');
        const layout = document.querySelector('.layout');
        const sidebar = document.getElementById('customerSidebar');
        const rightPanel = document.querySelector('.right-panel');
        const content = document.querySelector('.contentStation');
        const icon = btn.querySelector('i');

        btn.addEventListener('click', () => {
            const isFullscreen = layout.classList.toggle('main-fullscreen-mode');

            // Toggle visibility
            sidebar.style.display = isFullscreen ? 'none' : '';
            rightPanel.style.display = isFullscreen ? 'none' : '';
            content.classList.toggle('main-fullscreen', isFullscreen);

            // Change icon class
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('btnToggleRightPanelFullscreen');
        const rightPanel = document.querySelector('.right-panel');
        const mainContent = document.querySelector('.contentStation');
        const sidebar = document.getElementById('customerSidebar');
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            const isFullscreen = rightPanel.classList.toggle('fullscreen');

            // Toggle visibility of side and main content
            sidebar.style.display = isFullscreen ? 'none' : '';
            mainContent.style.display = isFullscreen ? 'none' : '';

            // Toggle icon
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>

<!-- Maximize Toggle Buttons  --> 
<script>
    function loadSectionPartial(customer_id, alternative_id, product_id, section) {
        const url = `/customer/partial/${customer_id}/${alternative_id}/${product_id}/${section}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Laden des Abschnitts');
                return response.text();
            })
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;

                // Optional: load extra sub-sections (partial wrappers)
            })
            .catch(error => {
                document.getElementById('mainContent').innerHTML =
                    `<div class="alert alert-danger">${error.message}</div>`;
            });
    }
    
</script>
 

<script>
  function showTab(step) {
      document.querySelectorAll('.tab-pane').forEach((pane, idx) => {
          pane.classList.remove('active');
          if (idx === step - 1) pane.classList.add('active');
      });

      document.querySelectorAll('.wizard-step').forEach((stepEl, idx) => {
          stepEl.classList.remove('active');
          if (idx === step - 1) stepEl.classList.add('active');
      });
      updateProgressCounts();

  }


    function navigateTab(direction) {
        const steps = document.querySelectorAll('.wizard-step');
        let currentIndex = [...steps].findIndex(step => step.classList.contains('active'));
        let nextIndex = currentIndex + direction;
        if (nextIndex >= 0 && nextIndex < steps.length) {
            showTab(nextIndex + 1);
        }
    }

    function updateProgressCounts() {
        const forms = document.querySelectorAll('form.partial-form');

        forms.forEach(form => {
            const section = form.dataset.section;
            const counterEl = document.getElementById(`step${getStepIndex(section)}-count`);
            if (!counterEl) return;

            const tabPane = form.closest('.tab-pane');
            const wasHidden = tabPane && !tabPane.classList.contains('active');

            // 🧠 Temporarily show hidden tab to count inputs
            if (wasHidden) {
                tabPane.classList.add('temporary-visible');
                tabPane.classList.add('active');
            }

            const inputs = form.querySelectorAll('input, select, textarea');
            let total = 0;
            let filled = 0;

            inputs.forEach(input => {
                const type = input.type;
                const isHidden = input.offsetParent === null; // skip visually hidden (e.g., display: none)
                if (input.name === '_token' || isHidden) return;

                total++;

                if (['checkbox', 'radio'].includes(type)) {
                    if (input.checked) filled++;
                } else {
                    const val = input.value?.trim();
                    if (val !== '') filled++;
                }
            });

            counterEl.textContent = `(${filled}/${total})`;

            // 🔄 Re-hide tab if it was not active before
            if (wasHidden) {
                tabPane.classList.remove('active');
                tabPane.classList.remove('temporary-visible');
            }
        });

        function getStepIndex(section) {
            const map = ['object_data', 'roof_info', 'heating_info', 'e_mobility', 'energy_usage'];
            return map.indexOf(section) + 1;
        }
    }

 
        function loadFullAlternativeObject(button) {
                const customerId    = button.dataset.customerId;
                const alternativeId = button.dataset.alternativeId;
                const productId     = button.dataset.productId;

                const url = `/customer/alternative/partials/${customerId}/${alternativeId}/${productId}/objekt`;

                const mainContent = document.getElementById('mainContent');
                mainContent.innerHTML = `<div class="text-center py-4">Lade Objektdaten...</div>`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Fehler beim Laden des Objekts');
                        return response.text();
                    })
                    .then(html => {
                        // ✅ Inject the new content
                        mainContent.innerHTML = html;

                        // ✅ Replace feather icons
                        if (typeof feather !== 'undefined') feather.replace();

                        // ✅ Re-initialize power calculator
                        initPowerCalculatorWithIDs(mainContent);

                        // ✅ Recalculate progress indicators
                        updateProgressCounts();
                    })
                    .catch(error => {
                        mainContent.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                        console.error('❌ Fehler beim Laden:', error);
                    });
            }



    function initPowerCalculatorWithIDs(context = document) {
        const $household    = $(context).find('#power_household_input');
        const $heatpump     = $(context).find('#power_heatpump_input');
        const $electricCar  = $(context).find('#power_electric_car_input');
        const $other        = $(context).find('#power_other_input');
        const $total        = $(context).find('#power_total');        // Display for user (with comma)
        const $totalHidden  = $(context).find('#power_total_hidden');  // Actual value (with dot)

        function parseInput($el) {
            const val = $el.val().trim().replace(',', '.');
            const num = parseFloat(val);
            if (isNaN(num) || num < 0) {
                $el.addClass('is-invalid');
                return 0;
            } else {
                $el.removeClass('is-invalid');
                return num;
            }
        }

        function updateTotal() {
            const h  = parseInput($household);
            const wp = parseInput($heatpump);
            const ev = parseInput($electricCar);
            const o  = parseInput($other);

            const total = h + wp + ev + o;

            // 👁️ Display total with comma
            $total.val(total.toFixed(2).replace('.', ','));

            // 🧠 Hidden input for DB (with dot)
            $totalHidden.val(total.toFixed(2));

            // ℹ️ kWh / year
            let $year = $(context).find('#power_total_year');
            if (!$year.length) {
                $year = $('<small id="power_total_year" class="form-text text-muted"></small>').insertAfter($total);
            }
            const yearly = total * 365;
            $year.text('≈ ' + yearly.toLocaleString('de-DE') + ' kWh / Jahr');
        }


        $household.add($heatpump).add($electricCar).add($other)
            .off('input.powercalc')
            .on('input.powercalc', updateTotal);

        updateTotal();
    }


    $(document).ready(function () {
        // 🔁 Initial progress calculation when page loads
        updateProgressCounts();

        // 🔄 Recalculate on any form input change
        $(document).on('input change', 'form.partial-form input, form.partial-form select, form.partial-form textarea', updateProgressCounts);
    });



</script>
 
<script> 
    document.addEventListener('DOMContentLoaded', function () {
        const electricCarSelect = document.getElementById('electric_car');
        const electricCarPlan = document.getElementById('electric_car_plan');

        if (electricCarSelect) {
            electricCarSelect.addEventListener('change', function () {
                if (this.value === 'Geplant') {
                    electricCarPlan.style.display = 'block';
                } else {
                    electricCarPlan.style.display = 'none';
                }
            });
        }
    }); 
</script>


<!-- Saving the alternative data : -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        if (e.target.classList.contains('partial-form')) {
            e.preventDefault();

            const form = e.target;
            const section = form.dataset.section;
            const id = form.dataset.id;

            const formData = new FormData(form);
            formData.append('id', id);

            fetch(`/new_lead_profile/alternative/object/save`, {
              method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Speichern von ' + section);
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: `Abschnitt "${section}" erfolgreich gespeichert.`
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: error.message
                });
            });
        }
    });
});
</script>



<!-- Requesting the notes:  --> 
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = '{{ csrf_token() }}';
    const noteList = document.getElementById('note-list');
    const scrollWrapper = document.getElementById('note-scroll-wrapper');
    let offset = 0;
    const searchInput = document.getElementById('searchNote');
 

    // Auto-load first product
    const firstProject = document.querySelector('.project-link');
    if (firstProject) firstProject.click();

    // Handle project selection
    document.querySelectorAll('.project-link').forEach(link => {
        link.addEventListener('click', async function () {
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const { objectCustomerId, objectAlternativeId, objectProduct } = this.dataset;
            noteList.innerHTML = '<div class="text-muted">Loading notes...</div>';

            try {
                const res = await fetch(`/customer-notes/${objectCustomerId}/${objectAlternativeId}/${objectProduct}`);
                const html = await res.text();
                noteList.innerHTML = html;
                feather.replace();
                initNoteListeners();
 
               // Scroll to top (show latest note first)
                setTimeout(() => {
                    scrollWrapper.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);

            } catch {
                noteList.innerHTML = '<div class="text-danger">Error loading notes.</div>';
            }
        });
    });

    // Toggle composer visibility
    window.toggleNewNoteArea = () => {
        const composer = document.getElementById('newNoteComposer');
        const backdrop = document.getElementById('noteBackdrop');

        composer.classList.toggle('open');

        if (composer.classList.contains('open')) {
            backdrop.style.display = 'block';
            setTimeout(() => document.getElementById('newNoteText').focus(), 200);
        } else {
            backdrop.style.display = 'none';
        }
    };


    // Submit a new note
    window.submitNote = async () => {
        const input = document.getElementById('newNoteText');
        const composer = document.getElementById('newNoteComposer');
        const noteList = document.getElementById('note-list');
        const text = input.value.trim();

        if (!text) {
            return Swal.fire('Hinweis', 'Bitte eine Notiz eingeben.', 'warning');
        }

        // 👇 Grab context from note-list (dashboard or product)
        const customerId = noteList.dataset.customerId;
        const alternativeId = noteList.dataset.alternativeId;
        const productId = noteList.dataset.productId || null;
        const type = noteList.dataset.noteType || (productId ? 'product' : 'general');

        if (!customerId || !alternativeId) {
            return Swal.fire('Fehler', 'Kunde oder Alternative fehlt.', 'error');
        }

        const body = {
            customer_id: customerId,
            alternative_id: alternativeId,
            product_id: productId,
            type: type,
            description: text,
            priority: 'normal',
            color: '#cfe09b'
        };

        try {
            const res = await fetch('/customer-notes/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(body)
            });

            const html = await res.text();
            noteList.insertAdjacentHTML('afterbegin', html);

            feather.replace();
            initNoteListeners?.();

            input.value = '';
            composer.classList.remove('open');

        } catch (err) {
            console.error(err);
            Swal.fire('Fehler', 'Notiz konnte nicht gespeichert werden.', 'error');
        }
    };


    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();

        noteList.querySelectorAll('.note-card').forEach(card => {
            const content = card.querySelector('.note-description')?.innerText.toLowerCase() || '';
            const matches = content.includes(query);
            card.style.display = matches ? '' : 'none';
        });
    });

    // Delete note
    window.deleteNote = id => {
        Swal.fire({
            title: 'Are you sure?',
            text: "This note will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/customer-notes/delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf }
                }).then(() => {
                    document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                });
            }
        });
    };

    // Reply to note
        window.postReply = (parentId, input) => {
            const text = input.value.trim();
            if (!text) return;

            const btn = input.closest('.input-group').querySelector('button');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

            fetch(`/customer-notes/${parentId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = input.closest('.note-card');
                    const wrapper = card.querySelector('.reply-wrapper');

                    // 🆕 Animate-in new reply
                    const temp = document.createElement('div');
                    temp.innerHTML = data.reply;
                    const newReply = temp.firstElementChild;

                    newReply.style.opacity = 0;
                    newReply.style.transition = 'opacity 0.3s ease';

                    wrapper.appendChild(newReply);

                    setTimeout(() => {
                        newReply.style.opacity = 1;
                        newReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 10);

                    input.value = '';
                    feather.replace();
                }
            })
            .catch(() => {
                Swal.fire('Fehler', 'Antwort konnte nicht gesendet werden.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `Senden`;
            });
        };


        window.deleteReply = function (id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This reply will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/customer-notes/reply/${id}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(() => {
                        document.querySelector(`.reply-item[data-id="${id}"]`)?.remove();
                    });
                }
            });
        };


       

        function createReplyContainer(card) {
            const container = document.createElement('div');
            container.className = 'reply-container ms-4 mt-2';

            const cardBody = card.querySelector('.card-body');
            const inputGroup = cardBody.querySelector('.input-group');

            if (inputGroup) {
                cardBody.insertBefore(container, inputGroup);
            } else {
                cardBody.appendChild(container);
            }

            return container;
        }


    // Inline edit logic
    function initNoteListeners() {
        document.querySelectorAll('.inline-edit-description').forEach(input => {
            input.oninput = () => {
                const id = input.dataset.id;
                fetch(`/customer-notes/inline-update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ field: 'description', value: input.value })
                });
            };
        });

        document.addEventListener('click', function (e) {
            const item = e.target.closest('.priority-item');
            if (!item) return;

            const { id, value } = item.dataset;

            fetch(`/customer-notes/inline-update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ field: 'priority', value })
            });

            document.querySelectorAll(`.priority-item[data-id="${id}"]`).forEach(i =>
                i.classList.remove('active', 'fw-bold', 'text-primary', 'text-primary', 'text-danger')
            );

            item.classList.add('active', 'fw-bold');
            item.classList.add(value === 'low' ? 'text-primary' : value === 'high' ? 'text-danger' : 'text-primary');
        });

        document.querySelectorAll('.inline-edit-color').forEach(input => {
            input.oninput = () => {
                const id = input.dataset.id;
                const value = input.value;
                const card = document.querySelector(`.note-card[data-id="${id}"]`);
                if (card) card.style.borderRightColor = value;

                fetch(`/customer-notes/inline-update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ field: 'color', value })
                });
            };
        });
    }

    window.editReply = function (replyId) {
        const card = document.querySelector(`.reply-item[data-id="${replyId}"]`);
        if (!card) return;

        const textDiv = card.querySelector('.reply-text');
        const oldText = textDiv?.textContent?.trim();
        console.log('Found textDiv:', textDiv);
        console.log('Old text:', oldText);

        Swal.fire({
            title: 'Reply bearbeiten',
            input: 'textarea',
            inputLabel: 'Neue Nachricht',
            inputValue: oldText,
            inputAttributes: {
                'aria-label': 'Neue Nachricht eingeben'
            },
            showCancelButton: true,
            confirmButtonText: 'Aktualisieren',
            cancelButtonText: 'Abbrechen',
            showLoaderOnConfirm: true,
            preConfirm: (newText) => {
                if (!newText.trim()) {
                    Swal.showValidationMessage('Antwort darf nicht leer sein.');
                    return false;
                }

                return fetch(`/customer-notes/reply/${replyId}/update`, {
                    method: 'POST', // Use POST if PUT fails on your server
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ description: newText.trim() })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Fehler beim Speichern');
                    return res.json();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value.success) {
                textDiv.innerHTML = result.value.updated_description;
                Swal.fire('✅ Aktualisiert', '', 'success');
            }
        });
    }

    window.editNote = function (noteId) {
        const card = document.querySelector(`.note-card[data-id="${noteId}"]`);
        if (!card) return;

        const textDiv = card.querySelector('.note-description');
        const oldText = textDiv?.textContent?.trim();

        Swal.fire({
            title: 'Notiz bearbeiten',
            input: 'textarea',
            inputLabel: 'Neue Nachricht',
            inputValue: oldText,
            inputAttributes: {
                'aria-label': 'Neue Nachricht eingeben'
            },
            showCancelButton: true,
            confirmButtonText: 'Aktualisieren',
            cancelButtonText: 'Abbrechen',
            showLoaderOnConfirm: true,
            preConfirm: (newText) => {
                if (!newText.trim()) {
                    Swal.showValidationMessage('Text darf nicht leer sein.');
                    return false;
                }

                return fetch(`/customer-notes/${noteId}/update`, {
                    method: 'POST', // or PUT if you prefer
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ description: newText.trim() })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Fehler beim Speichern');
                    return res.json();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value.success) {
                textDiv.textContent = result.value.updated_description;
                Swal.fire('✅ Aktualisiert', '', 'success');
            }
        });
    };


    // 🗑️ Load deleted child notes for a parent note
// window.trashNotes = async function (noteId) {
//     const container = document.getElementById(`deletedNotesContainer${noteId}`);
//     if (!container) return;

//     container.innerHTML = '<div class="text-muted p-2">Gelöschte Notizen werden geladen...</div>';

//     try {
//         const res = await fetch(`/notes/deleted/${noteId}`);
//         const data = await res.json();

//         if (!data.length) {
//             container.innerHTML = '<div class="text-muted">Keine gelöschten Unter-Notizen.</div>';
//             return;
//         }

//         container.innerHTML = '';
//         data.forEach(note => {
//             const html = `
//                 <div class="card p-2 mb-2 border-left-danger  ">
//                     <div><strong>${note.description}</strong></div>
//                     <div class="mt-2">
//                         <button class="btn btn-sm btn-success " onclick="restoreDeletedNote(${note.id})">
//                             <i class="feather icon-rotate-ccw"></i> Wiederherstellen
//                         </button>
//                         <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteNote(${note.id})">
//                             <i class="feather icon-trash-2"></i> Endgültig löschen
//                         </button>
//                     </div>
//                 </div>
//             `;
//             container.innerHTML += html;
//         });
//     } catch {
//         container.innerHTML = '<div class="text-danger">Fehler beim Laden der gelöschten Notizen.</div>';
//     }
// };

// ♻️ Restore soft-deleted note
window.restoreDeletedNote = async function (id) {
    try {
        const res = await fetch(`/notes/restore/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            }
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire('Wiederhergestellt', 'Die Notiz wurde erfolgreich wiederhergestellt.', 'success');
            document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
            location.reload(); // Optional: to reload list
        } else {
            Swal.fire('Fehler', 'Konnte die Notiz nicht wiederherstellen.', 'error');
        }
    } catch {
        Swal.fire('Fehler', 'Konnte die Notiz nicht wiederherstellen.', 'error');
    }
};

// 🛡️ Permanently delete with admin authentication
window.permanentlyDeleteNote = function (id) {
    Swal.fire({
        title: 'Administrator-Zugriff erforderlich',
        html: `
            <input type="text" id="adminUser" class="swal2-input" placeholder="Benutzername">
            <input type="password" id="adminPass" class="swal2-input" placeholder="Passwort">
        `,
        confirmButtonText: 'Endgültig löschen',
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
            const user = document.getElementById('adminUser').value;
            const pass = document.getElementById('adminPass').value;
            if (!user || !pass) {
                Swal.showValidationMessage('Benutzername und Passwort sind erforderlich');
                return false;
            }
            return { user, pass };
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/notes/delete-permanent/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Gelöscht!', data.message, 'success');
                    document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                    location.reload();
                } else {
                    Swal.fire('Fehlgeschlagen', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Fehler', 'Serverfehler beim Löschen.', 'error');
            });
        }
    });
};

// 🔍 Open modal & load deleted child notes
window.openDeletedNotesModal = async function(noteId) {
    $('#deletedNotesModal').modal('show');
    const container = document.getElementById('deletedNotesModalBody');
    container.innerHTML = '<div class="text-muted">Gelöschte Notizen werden geladen...</div>';

    try {
        const res = await fetch(`/notes/deleted/${noteId}`);
        const data = await res.json();

        if (!data.html) {
            container.innerHTML = '<div class="text-muted">Keine gelöschten Unter-Notizen gefunden.</div>';
            return;
        }

        container.innerHTML = data.html;
        feather.replace();

    } catch (err) {
        container.innerHTML = '<div class="text-danger">Fehler beim Laden der Notizen.</div>';
    }
};


window.loadAllDeletedNotes = async function () {
    $('#noteDeletedModalWrapper').modal('show');
    const container = document.getElementById('noteDeletedModalBody');
    container.innerHTML = '<div class="text-muted">Lade gelöschte Notizen...</div>';

    try {
        const res = await fetch(`/notes/deleted-all`);
        const data = await res.json();

        container.innerHTML = data.html;
        feather.replace();
    } catch {
        container.innerHTML = '<div class="text-danger">Fehler beim Laden der gelöschten Notizen.</div>';
    }
};


    initNoteListeners();
});
</script>

 


<!-- Loading the checklists  -->
<script>
    function loadChecklist(button) {
        const customerId = button.dataset.customerId;
        const alternativeId = button.dataset.alternativeId;
        const productId = button.dataset.productId;
        const leadProductListId = button.dataset.productListId;

        const container = document.getElementById('mainContent');
        container.innerHTML = '<div class="p-3 text-center">Checkliste wird geladen...</div>';

        fetch('/lead-product-checklist/init', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                lead_product_list_id: leadProductListId,
            })
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                container.innerHTML = `<pre style="color:red">${text}</pre>`;
                throw e;
            }
        })
        .then(data => {
            if (data.success) {
                container.innerHTML = `
                    <form id="customerChecklistForm" class="form-preview bg-white p-4">
                        <input type="hidden" name="lead_product_list_id" value="${leadProductListId}">
                        <input type="hidden" name="customer_id" value="${customerId}">
                        <input type="hidden" name="alternative_id" value="${alternativeId}">
                        <input type="hidden" name="product_id" value="${productId}">
                        ${data.html}
                        <button type="submit" class="btn btn-primary mt-3">Speichern</button>
                    </form>
                `;

                attachChecklistEvents();
            } else {
                container.innerHTML = '<div class="alert alert-danger">Fehler beim Laden.</div>';
            }
        })
        .catch(err => {
            console.error('Fetch failed:', err);
            container.innerHTML = '<div class="alert alert-danger">Ein Fehler ist aufgetreten.</div>';
        });
    }

    function attachChecklistEvents() {
        const form = document.querySelector('#customerChecklistForm');
        if (!form) return;

        form.addEventListener('submit', submitChecklist);
        form.addEventListener('input', () => {
            evaluateFormulas();
            updateProgressBars();
        });

        evaluateFormulas();
        updateProgressBars();
    }

    function submitChecklist(e) {
        e.preventDefault();

        const form = document.getElementById('customerChecklistForm');
        const filledValues = {};
        const leadProductListId = form.querySelector('[name="lead_product_list_id"]').value;
        const customerId = form.querySelector('[name="customer_id"]').value;
        const alternativeId = form.querySelector('[name="alternative_id"]').value;
        const productId = form.querySelector('[name="product_id"]').value;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            const name = input.name.replace(/\[\]$/, '');

            if (input.type === 'checkbox') {
                filledValues[name] = input.checked ? 1 : 0;
            } else if (input.name.endsWith('[]')) {
                if (!Array.isArray(filledValues[name])) filledValues[name] = [];
                filledValues[name].push(input.value);
            } else {
                filledValues[name] = input.value;
            }
        });

        fetch('/lead-product-checklist/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                lead_product_list_id: leadProductListId,
                filled_values: filledValues,
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Gespeichert', 'Die Daten wurden erfolgreich gespeichert.', 'success');
            } else {
                Swal.fire('Fehler', data.message || 'Daten konnten nicht gespeichert werden.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Fehler', 'Ein Fehler ist aufgetreten.', 'error');
        });
    }

function evaluateFormulas() {
    const values = {};

    document.querySelectorAll('#customerChecklistForm input, #customerChecklistForm select, #customerChecklistForm textarea')
        .forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            values[input.name] = input.type === 'checkbox' ? (input.checked ? 1 : 0) : input.value;
        });

    document.querySelectorAll('.formula-field').forEach(field => {
        const formula = field.dataset.formula;
        const result = evaluateFormula(formula, values);
        field.value = isNaN(result) ? 'Fehler' : result;
    });
}

function evaluateFormula(formula, values) {
    try {
        const fns = { add, sub, mul, div, round, min, max, toNum };
        const valKeys = Object.keys(values).filter(key => /^[a-zA-Z_][a-zA-Z0-9_]*$/.test(key));
        const valVals = valKeys.map(k => toNum(values[k]));

        const fnKeys = Object.keys(fns);
        const fnVals = Object.values(fns);

        const fn = new Function(...fnKeys, ...valKeys, `return ${formula}`);
        return fn(...fnVals, ...valVals);
    } catch (e) {
        console.warn('Formula error:', formula, e);
        return 'Fehler';
    }
}

function toNum(val) {
    return val === '' || val == null || isNaN(val) ? 0 : Number(val);
}
function add(a, b) { return toNum(a) + toNum(b); }
function sub(a, b) { return toNum(a) - toNum(b); }
function mul(a, b) { return toNum(a) * toNum(b); }
function div(a, b) { const d = toNum(b); return d === 0 ? 0 : toNum(a) / d; }
function round(v, p = 0) { return Math.round(toNum(v) * 10 ** p) / 10 ** p; }
function min(...args) { return Math.min(...args.map(toNum)); }
function max(...args) { return Math.max(...args.map(toNum)); }

function updateProgressBars() {
    document.querySelectorAll('.accordion-section').forEach(section => {
        const inputs = section.querySelectorAll('input, select, textarea');
        let total = 0;
        let filled = 0;

        inputs.forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            total++;
            const isFilled = input.type === 'checkbox' ? input.checked : input.value !== '';
            if (isFilled) filled++;
        });

        const percent = total > 0 ? Math.round((filled / total) * 100) : 0;
        const bar = section.querySelector('.progress-bar');

        if (bar) {
            bar.style.width = percent + '%';
            bar.setAttribute('aria-valuenow', percent);
            bar.innerText = percent + '%';
        }
    });
}
</script>



<!-- Image of customres  -->


<script>
    function loadDocuments(button) {
    const customerId = button.dataset.customerId;
    const alternativeId = button.dataset.alternativeId;
    const productId = button.dataset.productId;
    const productListId = button.dataset.productListId;

    const container = document.getElementById('mainContent');
    container.innerHTML = `<div class="p-3 text-center">Dokumente werden geladen...</div>`;

    fetch('/document/load', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            customer_id: customerId,
            alternative_id: alternativeId,
            product_id: productId,
            product_list_id: productListId
        })
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        // Attach search input listener after content is loaded
            const searchInput = document.getElementById('searchImage');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const keyword = this.value.trim().toLowerCase();

                    document.querySelectorAll('.gallery-item').forEach(el => {
                        const name = el.dataset.name?.toLowerCase() || '';
                        const type = el.dataset.type?.toLowerCase() || '';
                        const dateRaw = el.dataset.date || '';
                        const dateFormatted = new Date(dateRaw).toLocaleDateString('de-DE');
                        const date = dateFormatted.toLowerCase();

                        const fullText = `${name} ${type} ${date}`;
                        el.style.display = fullText.includes(keyword) ? '' : 'none';
                    });
                });
            }

        feather.replace();

        // re-init GLightbox
        GLightbox({ selector: '.glightbox' });

        const stageFilter = document.getElementById('stageFilter');
            if (stageFilter) {
                stageFilter.addEventListener('change', function () {
                    const stage = this.value;
                    document.querySelectorAll('.gallery-item').forEach(el => {
                        el.style.display = !stage || el.dataset.stage === stage ? '' : 'none';
                    });
                });
            }


        // re-init Lazy Loading
        const lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove("lazy");
                        lazyImageObserver.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(img => lazyImageObserver.observe(img));
        }

        // re-init Dropzone
        Dropzone.autoDiscover = false;
        new Dropzone("#documentDropzone", {
            paramName: "file",
            maxFilesize: 10,
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx",
            success: function (file, response) {
                loadDocuments(button); // refresh on upload
            }
        });
    })
    .catch(err => {
        container.innerHTML = `<div class="text-danger">Fehler beim Laden der Dokumente</div>`;
    });
}

setTimeout(() => GLightbox({ selector: '.glightbox' }), 100);


function deleteDocument(id, el) {
    Swal.fire({
        title: 'Löschen bestätigen',
        text: 'Willst du dieses Dokument wirklich löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/document/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    el.closest('.gallery-item').remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Gelöscht!',
                        text: 'Das Dokument wurde entfernt.',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    GLightbox({
        selector: '.glightbox'
    });
});

function renameDocument(id, newName) {
    fetch('/document/rename', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id: id, image_name: newName })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Erfolgreich umbenannt!',
                showConfirmButton: false,
                timer: 1000
            });
        } else {
            throw new Error();
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Fehler beim Umbenennen!',
            text: 'Bitte erneut versuchen.'
        });
    });
}

</script>
 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
    if ("IntersectionObserver" in window) {
        let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    let img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove("lazy");
                    lazyImageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function (lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    }
});
 
</script>
 

<!-- Map view  -->

<script>
    // 🔥 GLOBAL STORE FOR MAP INSTANCES
window.googleMapsInstances = {};

// 👉 INIT MAP ON SIDEBAR OPEN
function openSidebarGallery(triggerEl) {
    const objectId = triggerEl.dataset.alternativeId;
    const address = triggerEl.dataset.address;
    const sidebar = document.getElementById('sidebarGallery' + objectId);
    if (!sidebar) return;

    sidebar.classList.add('active');

    // Load gallery images
    fetch(`/load-images/${objectId}`)
        .then(res => res.json())
        .then(images => {
            const gallery = document.getElementById('galleryImages' + objectId);
            if (!gallery) return;

            gallery.innerHTML = '';

            if (Array.isArray(images) && images.length > 0) {
                images.forEach(img => {
                    appendImageToGallery(objectId, img.image);
                });
                GLightbox({ selector: '.glightbox' });
            } else {
                gallery.innerHTML = '<p class="text-muted">Keine Bilder vorhanden.</p>';
            }
        })
        .catch(err => {
            console.warn("Image loading failed:", err);
        });

    // Delay map init so sidebar is visible
    setTimeout(() => initGoogleMap(objectId, address), 300);
}


function closeSidebarGallery(objectId) {
    document.getElementById('sidebarGallery' + objectId)?.classList.remove('active');
}

// ✅ INIT GOOGLE MAP
function initGoogleMap(objectId, address) {
    const mapDiv = document.getElementById('mapContainer' + objectId);
    if (!mapDiv) return;

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address }, (results, status) => {
        if (status === 'OK') {
            const map = new google.maps.Map(mapDiv, {
                center: results[0].geometry.location,
                zoom: 18,
                mapTypeId: 'roadmap',
            });
            new google.maps.Marker({ position: results[0].geometry.location, map });

            window.googleMapsInstances[objectId] = map;

            // Handle map type switching
            const select = document.getElementById('screenshotMode' + objectId);
            if (select) {
                select.addEventListener('change', () => {
                    const type = select.value;
                    if (type !== 'streetview') map.setMapTypeId(type);
                });
            }
        } else {
            Swal.fire('Fehler', 'Adresse nicht gefunden.', 'error');
        }
    });
}

// 🎯 TRIGGER SCREENSHOT
function triggerScreenshot(customerId, alternativeId) {
    const mode = document.getElementById('screenshotMode' + alternativeId)?.value || 'roadmap';
    if (mode === 'streetview') {
        captureStreetViewScreenshot(customerId, alternativeId);
    } else {
        captureStaticMapScreenshot(customerId, alternativeId);
    }
}

// 📷 STATIC MAP SCREENSHOT (Google Static Maps API)
function captureStaticMapScreenshot(customerId, alternativeId) {
    const map = window.googleMapsInstances[alternativeId];
    if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

    const center = map.getCenter();
    const zoom = map.getZoom();
    const type = map.getMapTypeId();

    const staticUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${center.lat()},${center.lng()}&zoom=${zoom}&size=800x400&maptype=${type}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

    fetch(staticUrl)
        .then(res => res.blob())
        .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'map_screenshot.png'));
}

// 📷 STREET VIEW SCREENSHOT (Google Street View API)
function captureStreetViewScreenshot(customerId, alternativeId) {
    const map = window.googleMapsInstances[alternativeId];
    if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

    const svService = new google.maps.StreetViewService();
    const panorama = map.getStreetView();
    const pov = panorama.getPov();
    const position = panorama.getPosition();

    if (!position) {
        Swal.fire('⚠️ Street View nicht aktiv', '', 'warning');
        return;
    }

    const lat = position.lat();
    const lng = position.lng();
    const heading = pov.heading || 0;
    const pitch = pov.pitch || 0;

    const metaUrl = `https://maps.googleapis.com/maps/api/streetview/metadata?location=${lat},${lng}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
    fetch(metaUrl)
        .then(res => res.json())
        .then(meta => {
            if (meta.status !== 'OK') {
                Swal.fire('Kein Street View Bild verfügbar', '', 'warning');
                return;
            }

            const viewUrl = `https://maps.googleapis.com/maps/api/streetview?size=800x400&location=${lat},${lng}&fov=90&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
            fetch(viewUrl)
                .then(res => res.blob())
                .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'streetview_screenshot.png'));
        });
}

// 📨 UPLOAD SCREENSHOT TO SERVER
function uploadScreenshot(blob, customerId, alternativeId, filename) {
    const formData = new FormData();
    formData.append('image', blob, filename);
    formData.append('customer_id', customerId);
    formData.append('alternative_id', alternativeId);
    formData.append('status', 'screenshot');

    fetch('/save-screenshot', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
        .then(res => res.json())
        .then(result => {
            if (result.success && result.image) {
                Swal.fire('✅ Screenshot gespeichert!', '', 'success');
                appendImageToGallery(alternativeId, result.image);
            } else {
                Swal.fire('Fehler beim Speichern', '', 'error');
            }
        });
}

function appendImageToGallery(alternativeId, imagePath) {
    const gallery = document.getElementById('galleryImages' + alternativeId);
    const fullUrl = `${window.location.origin}/public/uploads/${imagePath}`;

    const wrapper = document.createElement('div');
    wrapper.className = 'screenshot-item d-inline-block position-relative m-1';
    wrapper.style.width = '90px';

    const link = document.createElement('a');
    link.href = fullUrl;
    link.className = 'glightbox';
    link.setAttribute('data-gallery', `object-gallery-${alternativeId}`);
    link.setAttribute('data-title', 'Screenshot');

    const image = document.createElement('img');
    image.src = fullUrl;
    image.className = 'img-thumbnail';
    image.style = 'width: 90px; height: 60px; object-fit: cover;';
    link.appendChild(image);

    const delBtn = document.createElement('button');
    delBtn.className = 'btn btn-sm btn-danger position-absolute';
    delBtn.style = 'top: -5px; right: -5px; padding: 2px 5px; font-size: 12px;';
    delBtn.innerHTML = 'x';
    delBtn.onclick = () => deleteScreenshot(imagePath, wrapper);

    wrapper.appendChild(link);
    wrapper.appendChild(delBtn);
    gallery.appendChild(wrapper);

    GLightbox({ selector: '.glightbox' });
}

function deleteScreenshot(filename, wrapperElement) {
    Swal.fire({
        title: 'Bild löschen?',
        text: 'Dieses Bild wird dauerhaft entfernt.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('/delete-screenshot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ image: filename })
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        wrapperElement.classList.add('fade-out');
                        setTimeout(() => wrapperElement.remove(), 400);
                        Swal.fire('✅ Gelöscht!', '', 'success');
                    } else {
                        Swal.fire('❌ Fehler beim Löschen.', '', 'error');
                    }
                });
        }
    });
}


</script>



<script>
function loadkanban(customerId, alternativeId, productId, employeeId) {
    const container = document.getElementById('mainContent');
    container.innerHTML = '<div class="p-3 text-center">Kanban wird geladen...</div>';

    fetch(`/customer/process/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}`)
        .then(response => response.json())
        .then(data => {
            return fetch('/customer/process/kanban/view', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ leads: data })
            });
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            feather.replace();
            initKanbanSortable();
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden des Kanban-Boards.</div>`;
            console.error(err);
        });
}

function initKanbanSortable(onStageChangeSuccess = () => {}) {
    const dropzones = document.querySelectorAll('.kanban-dropzone');
    const borderColors = {
        lead: '#fcd34d',
        offer: '#93c5fd',
        deal: '#34d399',
        project: '#60a5fa',
        ticket: '#fca5a5',
        completed: '#a3e635',
        junk: '#d1d5db'
    };

    dropzones.forEach((zone, index) => {
        new Sortable(zone, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const el = evt.item;
                const fromStage = evt.from.closest('.kanban-column')?.dataset.stage;
                const toStage = evt.to.closest('.kanban-column')?.dataset.stage;

                if (!toStage || fromStage === toStage) return;

                const customerId = el.dataset.customerId;
                const alternativeId = el.dataset.alternativeId;
                const productId = el.dataset.productId;
                const employeeId = el.dataset.employeeId || 0;
                const service = el.dataset.service;
                const serviceId = el.dataset.serviceId || 0;
                const departmentId = el.dataset.departmentId || 0;

                if (!customerId || !alternativeId || !productId || !service || !toStage) {
                    console.error('[Kanban Error] Missing data');
                    return;
                }

                Swal.fire({
                    title: 'Notiz zum Statuswechsel',
                    input: 'textarea',
                    inputPlaceholder: 'Gib eine Beschreibung oder Notiz ein...',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen',
                    inputAttributes: {
                        autocapitalize: 'on'
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: (description) => {
                        const url = `/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${toStage}/${serviceId}/${departmentId}`;
                        return fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ description })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Fehler beim Speichern');
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`❌ ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(result => {
                    if (result.isConfirmed) {
                        // ✅ Style and update
                        el.style.borderLeftColor = borderColors[toStage] || '#ccc';
                        const stageLabel = el.querySelector('.kanban-stage-label');
                        if (stageLabel) {
                            const stageMap = {
                                lead: 'Kunde',
                                offer: 'Angebot',
                                deal: 'Auftrag',
                                project: 'Montage',
                                completed: 'Abgeschlossen',
                                ticket: 'Ticket',
                                junk: 'Junk'
                            };
                            stageLabel.textContent = stageMap[toStage] || toStage;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Status geändert',
                            text: result.value.message || 'Erfolgreich aktualisiert.',
                            timer: 1200,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        onStageChangeSuccess(el, toStage);
                    } else {
                        // ❌ Move back if canceled
                        evt.from.appendChild(el);
                    }
                });
            }
        });
    });
}
</script>


<script>
function openNewProductModal(el) {
    const customerId = el.dataset.customerId;
    const alternativeId = el.dataset.alternativeId;

    console.log('Clicked - Customer:', customerId, 'Alt:', alternativeId); // ✅ debug

    $('#product_customer_id').val(customerId);
    $('#product_alternative_id').val(alternativeId);

    $('#newProductModal').modal('show');
}
</script>


<script>


const productImage = "{{ asset('images/articles/') }}";
const employeeImage = "{{ asset('images/employee/') }}";
 
$(document).ready(function () { 

    let rowIndex = 0;
    const services = @json($new_services);
    const products = @json($new_products);
    const departments = @json($new_departments);

    $('#addRow').click(function () {
        let lastRow = $('#inquiryProductTable tbody tr:last');
        if (lastRow.length > 0) {
            const index = lastRow.data('index');
            const product = $(`.product-select[data-index="${index}"]`).val();
            const service = $(`.service-select[data-index="${index}"]`).val();
            const department = $(`.department-select[data-index="${index}"]`).val();
            const employee = $(`.employee-select[data-index="${index}"]`).val();

            let missingFields = [];
            if (!product) missingFields.push('Produkt');
            if (!service) missingFields.push('Service');
            if (!department) missingFields.push('Abteilung');
            if (!employee) missingFields.push('Mitarbeiter');

            if (missingFields.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: `Zeile ${index + 1} unvollständig`,
                    html: `Bitte füllen Sie folgende Felder aus: <strong>${missingFields.join(', ')}</strong>`,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-danger' },
                    buttonsStyling: false
                });
                return;
            }
        }

        rowIndex++;
        const newRow = `
            <tr data-index="${rowIndex}" class="align-middle">
                <td>
                    <select class="form-select product-select" name="product_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Produkt wählen</option>
                        ${products.map(p => `<option value="${p.id}" data-img="${p.image}">${p.article_group}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-select service-select" name="service_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Service wählen</option>
                    </select>
                </td>
                <td>
                    <select class="form-select department-select" name="department_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Abteilung wählen</option>
                        ${departments.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-select employee-select" name="employee_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Mitarbeiter wählen</option>
                    </select>
                </td>

                <td>
                    <select class="form-select interest-select" name="interest[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="intent">Kaufabsicht</option>
                        <option value="interest">Kaufinteresse</option>
                        <option value="option">Kaufoption</option>
                    </select>
                </td>

                   <td>
                    <select class="form-select realization-select" name="realization_time[]" data-index="${rowIndex}" style="width:100% !important;">
                         <option value="">Bitte auswählen</option> 
                        <option value="soon">Schnellstmöglich</option>
                        <option value="3">3 Monate</option>
                        <option value="6">6 Monate</option>
                        <option value="other">Sonstiges</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="Entfernen">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>`;

        $('#inquiryProductTable tbody').append(newRow);
        initializeSelect2(rowIndex);
    });


    // Select2 initializer for all dropdowns
    function initializeSelect2(index) {
        const productSel = `.product-select[data-index="${index}"]`;
        const serviceSel = `.service-select[data-index="${index}"]`;
        const deptSel = `.department-select[data-index="${index}"]`;
        const empSel = `.employee-select[data-index="${index}"]`;
        const interestSel = `.interest-select[data-index="${index}"]`;
        const realSel = `.realization-select[data-index="${index}"]`;

        $(productSel).select2().on('change', function () {
            loadServices(index);
            loadEmployees(index);
        });

        $(serviceSel).select2().on('change', function () {
            loadEmployees(index);
        });

        $(deptSel).select2().on('change', function () {
            loadEmployees(index);
        });


        $(interestSel).select2().on('change', function () {
            loadEmployees(index);
        });
        $(realSel).select2().on('change', function () {
            loadEmployees(index);
        });

        $(empSel).select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployeeSelection,
            escapeMarkup: m => m
        });
    }

    // Load services for selected product
    function loadServices(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const $service = $(`.service-select[data-index="${index}"]`);

        $service.empty().append('<option value="">Service wählen</option>');
        services.forEach(s => {
            if (s.product_id == productId) {
                $service.append(`<option value="${s.id}">${translateService(s.phase_section)}</option>`);
            }
        });

        $service.trigger('change');
    }

    // Load employees based on product, service, and department
    function loadEmployees(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const departmentId = $(`.department-select[data-index="${index}"]`).val();
        const serviceId = $(`.service-select[data-index="${index}"]`).val();
        const $employeeSelect = $(`.employee-select[data-index="${index}"]`);

        if (productId && departmentId && serviceId) {
            $.post('{{ route("inquiry.department.employees") }}', {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                department_id: departmentId,
                service_id: serviceId
            }, function (data) {
                $employeeSelect.empty().append('<option value="">Mitarbeiter wählen</option>');
                if (data.length > 0) {
                    data.forEach(emp => {
                        $employeeSelect.append(
                            `<option value="${emp.id}" data-img="${emp.image}" data-positions="${emp.positions.join(', ')}">${emp.name} ${emp.lastname}</option>`
                        );
                    });
                    setTimeout(() => {
                        $employeeSelect.val(data[0].id).trigger('change');
                    }, 100); // Let DOM update before setting default
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keine Mitarbeiter gefunden',
                        text: 'Für diese Auswahl existieren keine Mitarbeiter.',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-warning' },
                        buttonsStyling: false
                    });
                }

                $employeeSelect.select2({
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: m => m
                });
            });
        } else {
            $employeeSelect.empty().append('<option value="">Mitarbeiter wählen</option>').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployeeSelection,
                escapeMarkup: m => m
            });
        }
    }

    // Custom employee display
    function formatEmployee(emp) {
        if (!emp.id) return emp.text;
        const img = $(emp.element).data('img') ? `${employeeImage}/${$(emp.element).data('img')}` : '';
        const pos = $(emp.element).data('positions') || '';
        return `
            <div style="display:flex;align-items:center;">
                <img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                <div><strong>${emp.text}</strong><br><small>${pos}</small></div>
            </div>`;
    }

    function formatEmployeeSelection(emp) {
        return emp.text;
    }

    function translateService(s) {
        switch (s?.toLowerCase()) {
            case 'complete': return 'Komplett';
            case 'montage': return 'Montage';
            case 'product': return 'Kaufen';
            case 'plan': return 'Planung';
            case 'maintenance': return 'Wartung';
            case 'repair': return 'Reparatur';
            case 'others': return 'Sonstiges';
            default: return s;
        }
    }

    // Proper delete handling (for both new & old rows)
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').fadeOut(200, function () {
            $(this).remove();
        });
    });

    // Optional: Initialize existing rows on page load
    $('tbody tr').each(function () {
        const idx = $(this).data('index');
        initializeSelect2(idx);
    });


    // Save all rows via AJAX
    $('#saveProductRows').on('click', function () {
        const rows = $('#inquiryProductTable tbody tr');
        const payload = [];

        if (rows.length === 0) {
            Swal.fire('Hinweis', 'Bitte fügen Sie mindestens ein Produkt hinzu.', 'warning');
            return;
        }

        let isValid = true;

        rows.each(function () {
            const index = $(this).data('index');

            const rowData = {
                product_id: $(`.product-select[data-index="${index}"]`).val(),
                service_id: $(`.service-select[data-index="${index}"]`).val(),
                department_id: $(`.department-select[data-index="${index}"]`).val(),
                employee_id: $(`.employee-select[data-index="${index}"]`).val(),
                interest: $(`.interest-select[data-index="${index}"]`).val(),
                realization_time: $(`.realization-select[data-index="${index}"]`).val(),
                customer_id: $('#product_customer_id').val(),
                alternative_id: $('#product_alternative_id').val()

            };

            // Check if required fields are missing
            if (!rowData.product_id || !rowData.service_id || !rowData.department_id || !rowData.employee_id) {
                isValid = false;
                Swal.fire('Fehler', `Zeile ${index} ist unvollständig.`, 'error');
                return false; // break loop
            }

            payload.push(rowData);
        });

        if (!isValid) return;

        $.ajax({
            url: '{{ route("lead_product_lists.bulk.store") }}',
            type: 'POST',
            data: JSON.stringify({ rows: payload }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            beforeSend: function () {
                $('#saveProductRows').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Speichern...');
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolgreich gespeichert',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Fehler', res.message || 'Etwas ist schiefgelaufen.', 'error');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Serverfehler';
                Swal.fire('Fehler', msg, 'error');
            },
            complete: function () {
                $('#saveProductRows').prop('disabled', false).html('<i class="feather icon-save"></i> Speichern');
            }
        });
    });

});
</script>

@endsection


@push('scripts')
    

<!-- Ticket systems  --> 
<script> 
const context = document.getElementById('filterContext');
const customer_id = context.dataset.customer;
const alternative_id = context.dataset.alternative;
const product_id = context.dataset.product;

function LoadCustomerTicket(customerId, alternativeId, productId, tab) {
    const container = document.getElementById("mainContent");
    container.innerHTML = '<div class="p-4 text-center">Tickets werden geladen...</div>';

    fetch("/customer/tickets/load", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            customer_id: customerId,
            alternative_id: alternativeId,
            product_id: productId,
            tab: tab
        })
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        feather.replace();
        initKanbanSortable(); // ✅ re-initialize sortable
    });
}
    function filterTickets() {
        const date = document.getElementById('filterDate').value;
        const status = document.getElementById('filterStatus').value;
        const employee = document.getElementById('filterEmployee').value;

        // ✅ use correctly defined vars
        const context = document.getElementById('filterContext');
        const customer_id = context.dataset.customer;
        const alternative_id = context.dataset.alternative;
        const product_id = context.dataset.product;

        fetch('/customer/tickets/load', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id,
                alternative_id,
                product_id,
                date,
                status,
                employee
            })
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('mainContent').innerHTML = html;
            feather.replace();
            initKanbanSortable();
        });
    }


function initKanbanSortable() {
    document.querySelectorAll('.kanban-dropzone').forEach(zone => {
        new Sortable(zone, {
            group: 'tickets',
            animation: 150,
            onAdd: function (evt) {
                const ticketId = evt.item.dataset.id;
                const newStatus = evt.to.closest('.kanban-column').dataset.status;

                fetch('/ticket/status/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ id: ticketId, status: newStatus })
                }).then(res => res.json())
                  .then(res => {
                      if (res.success) {
                          console.log(`Ticket #${ticketId} → ${newStatus}`);
                      }
                  });
            }
        });
    });
} 
// Optional: Call this once if Kanban is rendered on page load
document.addEventListener('DOMContentLoaded', initKanbanSortable);
</script>



<!-- Loading customer info Product  -->
<script>
let currentCid = null;
let currentAid = null;
let currentPid = null;

// 🔄 On product button click
function leadProduct(button) {
    const $btn = $(button);
    currentCid = $btn.data('customer-id');
    currentAid = $btn.data('alternative-id');
    currentPid = $btn.data('product-id');

    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: `/lead-product/${currentCid}/${currentAid}/${currentPid}`,
        method: 'GET',
        success: function (response) {
            $('#mainContent').html(response);
            Swal.close();
            feather.replace();

            setTimeout(() => {
                const $modal = $('#addProductModal');
                const $productSelect = $('#customer_product_info');
                const $departmentSelect = $('#department_id');

                fetch(`/customer/load/product/${currentPid}`)
                    .then(res => res.json())
                    .then(data => {
                        // 1️⃣ Populate Product Select
                        $productSelect.empty();

                        (data.products || []).forEach(product => {
                            const image = product.images[0]?.image ? `/uploads/${product.images[0].image}` : '/images/icons/placeholder.svg';
                            const brand = product.brand?.name || '';
                            const description = product.short_description || '';

                            $productSelect.append(new Option(product.product, product.id, false, false))
                                .find(`option[value="${product.id}"]`)
                                .attr('data-brand', brand)
                                .attr('data-description', description)
                                .attr('data-image', image);
                        });

                        $productSelect.select2({
                            templateResult: formatProduct,
                            templateSelection: formatProductSelection,
                            allowClear: true,
                            dropdownParent: $modal
                        });

                        $productSelect.on('change', function () {
                            const selected = this.options[this.selectedIndex];
                            $('#manufacturer_note').val(selected.getAttribute('data-brand') || '');
                            $('#notes_note').val(selected.getAttribute('data-description') || '');
                        });

                        // 2️⃣ Populate Departments
                        $departmentSelect.empty().append(`<option value=""></option>`);
                        (data.departments || []).forEach(dept => {
                            $departmentSelect.append(new Option(dept.department_name, dept.id));
                        });

                        // 3️⃣ Show modal
                        // if (!$modal.hasClass('show')) {
                        //     $modal.modal('show');
                        // }
                    })
                    .catch(error => {
                        console.error('❌ Fehler beim Laden:', error);
                        Swal.fire('Fehler', 'Produktdaten konnten nicht geladen werden.', 'error');
                    });
            }, 300);
        },
        error: function () {
            Swal.fire('Error', 'Failed to load product info.', 'error');
        }
    });
}

// 🖼️ Select2 format with product image
function formatProduct(state) {
    if (!state.id) return state.text;
    const image = $(state.element).data('image');
    return $(`<span><img src="${image}" style="width:30px;height:30px;object-fit:cover;margin-right:8px"> ${state.text}</span>`);
}

function formatProductSelection(state) {
    return state.text;
}
</script>
 
 
<script>
function addProduct() {
    const selectedProduct = $('#customer_product_info option:selected');
    const data = {
        _token: '{{ csrf_token() }}',
        customer_id: currentCid,
        alternative_id: currentAid,
        product_id: currentPid,
        products: $('#customer_product_info').val(), // 👈 save to "products" column 
        product_name: selectedProduct.text(),
        manufacturer: $('#manufacturer_note').val(),
        serial_number: $('#serial_number').val(),
        installation_date: $('#installation_date').val(),
        installation_location: $('#installation_location').val(),
        purchased_from_us: $('#purchased_from_us').val(),
        purchase_date: $('#purchase_date').val(),
        invoice_reference: $('#invoice_reference').val(),
        warranty_until: $('#warranty_until').val(),
        guarantee_until: $('#guarantee_until').val(),
        image_available: $('#image_available').val(),
        installed_by: $('#installed_by').val(),
        department_id: $('#department_id').val(),
        notes: $('#notes_note').val()
    };

    $.post('/lead-product/store', data, function (response) {
        $('#addProductModal').modal('hide');
        Swal.fire('Erfolgreich', 'Produkt hinzugefügt', 'success');
        appendRow(response);
    }).fail(() => {
        Swal.fire('Fehler', 'Produkt konnte nicht gespeichert werden', 'error');
    });
}

function appendRow(product) {
    const row = `
        <tr data-id="${product.id}">
            <td>${product.product_name}</td>
            <td>${product.manufacturer ?? '—'}</td>
            <td>${product.serial_number ?? '—'}</td>
            <td>${product.installation_date ?? '—'}</td>
            <td>${product.purchase_date ?? '—'}</td>
            <td>${product.installed_by ?? '—'}</td>
            <td>${product.department_name ?? '—'}</td>
            <td>
                <button class="btn btn-sm btn-outline-secondary" onclick="editProduct(${product.id})">✏️</button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">🗑️</button>
            </td>
        </tr>
    `;
    $('#productTableBody').append(row);
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/lead-product/delete/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => {
                    $(`tr[data-id="${id}"]`).remove();
                    Swal.fire('Gelöscht', 'Eintrag wurde entfernt', 'success');
                },
                error: () => {
                    Swal.fire('Fehler', 'Konnte nicht gelöscht werden', 'error');
                }
            });
        }
    });
}

function editProduct(id) {
    const row = $(`tr[data-id="${id}"]`);
    const currentName = row.find('td:first').text();

    Swal.fire({
        title: 'Produktname bearbeiten',
        input: 'text',
        inputValue: currentName,
        showCancelButton: true,
        confirmButtonText: 'Aktualisieren'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/lead-product/update/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_name: result.value
                },
                success: () => {
                    row.find('td:first').text(result.value);
                    Swal.fire('Aktualisiert', 'Produktname geändert', 'success');
                },
                error: () => {
                    Swal.fire('Fehler', 'Konnte nicht aktualisiert werden', 'error');
                }
            });
        }
    });
}
</script>


<script>
    console.log('✅ addNewRoofEditProfile is defined here');

    let roofIndex = {{ isset($roofs) ? count($roofs) : 0 }};

    function addNewRoofEditProfile() {
        console.log('📦 Called addNewRoofEditProfile');
        fetch(`/admin/roofs/partial-edit-profile/${roofIndex}`)
            .then(res => res.text())
            .then(html => {
                const wrapper = document.getElementById('roof-wrapper');
                const newDiv = document.createElement('div');
                newDiv.innerHTML = html;
                wrapper.appendChild(newDiv);
                roofIndex++;
            })
            .catch(err => console.error('Fehler beim Laden des neuen Daches:', err));
    }
</script>

 
<!-- Customer Product List  -->
<script>
    const PLACEHOLDER_IMAGE = "{{ asset('images/icons/placeholder.svg') }}";
    const EMPLOYEE_IMAGE = "{{ asset('images/employee/') }}";
    const GENDER = "{{ asset('images/gender/male.png') }}";
</script>

<script>
    window.AppData = {
        customerStages: @json($customerStages ?? []),
        productInitials: @json($productInitials ?? []),
    };
</script>

 
<script>
 // rewritten script with card-based layout
 
// 🔄 DASHBOARD JS WITH CARD LAYOUT + FIXES

    document.addEventListener("DOMContentLoaded", () => {
        window.loadDashboard();
    });



            window.loadDashboard = () => {
                const container = document.getElementById('dashboard');
                if (!container) return console.error("❌ #dashboard not found.");

                const customerId = container.dataset.id;
                if (!customerId) {
                    container.innerHTML = "<p class='text-danger'>❗ No customer ID provided.</p>";
                    return;
                }

                const queryParams = new URLSearchParams({
                    customer_id: customerId,
                    product_id: window.selectedProductId || '',
                    service_id: window.selectedServiceId || '',
                    stage: window.selectedStage || '',
                    alternative_id: window.selectedAltId || ''
                });

                const services = { complete: 'Komplettlösung', montage: 'Montage', product: 'Produkt', plan: 'Planung', maintenance: 'Wartung', repair: 'Reparatur', emergency: 'Notdienst', others: 'Sonstiges' };
                const interests = { intent: 'Kaufabsicht', interest: 'Kaufinteresse', option: 'Kaufoption' };
                const realizations = { soon: 'Schnellstmöglich', 3: '3 Monate', 6: '6 Monate', other: 'Sonstiges' };
                
                
                    function highlightStageIcons(currentStage, container) {
                        if (!container) return;

                        container.querySelectorAll('i[data-stage]').forEach(icon => {
                            icon.classList.remove('active');
                            icon.style.color = '#ccc';
                        });

                        const activeIcon = container.querySelector(`i[data-stage="${currentStage}"]`);
                        if (activeIcon) {
                            activeIcon.classList.add('active');
                            activeIcon.style.color = '#fff';
                            activeIcon.style.backgroundColor = '#73b1d4'; // Green
                            activeIcon.title = currentStage.charAt(0).toUpperCase() + currentStage.slice(1);
                        }
                    }

                
                    fetch(`/api/objects-with-products?${queryParams}`)
                        .then(res => res.json())
                        .then(data => {
                            container.innerHTML = '';

                            if (!Array.isArray(data) || data.length === 0) {
                                container.innerHTML = "<p class='text-warning'>⚠️ Keine Objekte gefunden.</p>";
                                return;
                            }

                            const row = document.createElement('div');
                            row.className = 'd-flex flex-wrap';

                            data.forEach(object => {
                                    const block = document.createElement('div');
                                    block.className = 'house-block mb-4';
                                        const firstProduct = object.products?.[0]; 
                                    const header = document.createElement('div');
                                    header.className = 'house-header d-flex align-items-center p-2 border mb-0 bg-white position-relative';

                                    header.innerHTML = `
                                        <!-- Screenshot / Image -->
                                        <div class="house-img mr-2">
                                            <img src="${object.screenshot_image?.src || '/images/icons/placeholder.svg'}"
                                                style="width: 120px; object-fit: cover; cursor: pointer;"
                                                onclick="openSidebarGallery(this)"
                                                data-customer-id="${object.screenshot_image?.customer_id || ''}"
                                                data-alternative-id="${object.screenshot_image?.alternative_id || ''}"
                                                data-address="${object.screenshot_image?.address || ''}">
                                        </div>

                                        <!-- Address Info -->
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold text-primary" style="font-size: 1.1rem;">
                                                ${object.object_name || 'Objekt'}
                                            </div>
                                            <div class="text-muted">${object.street || ''}</div>
                                            <div class="text-muted">${object.postcode || ''} ${object.city || ''}</div>
                                        </div>

                                        <!-- Action Buttons -->
                                         <div class="flex-grow-1">
                                            <a type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light"
                                            href="/customer_profit/${object.customer_id}/${object.id}/${firstProduct?.product_id || ''}/${firstProduct?.section_id || ''}">
                                                <i class="feather icon-bar-chart-2"></i> WIRTSCHAFTLICHKEITSBERECHNUNG
                                            </a>
                                            <a type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light">
                                                <i class="feather icon-gift"></i> Förderungen
                                            </a>
                                        </div>
 
                                       <!-- Dropdown Menu (Top Right) -->
                                            <div class="position-absolute" style="top: 10px; right: 10px;">
                                               <div class="dropdown ml-auto">
                                                <button class="btn btn-sm btn-icon btn-outline-primary" type="button" data-toggle="dropdown">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="/new_object/${object.customer_id}">
                                                    <i class="feather icon-home mr-1"></i> Neues Objekt
                                                    </a>

                                                     <a class="dropdown-item" href="/new_lead_edit/${object.customer_id}/${object.id}">
                                                        <i class="feather icon-edit mr-1"></i> Objekt bearbeiten
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="openNewProductModal(this)" 
                                                        data-customer-id="${object.customer_id || ''}"
                                                        data-alternative-id="${object.id || ''}">
                                                        <i class="feather icon-plus-circle mr-1"></i> Neues Produkt
                                                    </a>

                                                    <a class="dropdown-item" onclick="resetAllSubNavs()">
                                                        <i class="feather icon-refresh mr-1"></i> Reset Cache
                                                    </a>

                                                </div>
                                                </div>

                                            </div>

                                    `;

                                    block.appendChild(header); 

                                const productRow = document.createElement('div');
                                productRow.className = 'd-flex flex-wrap';

                                (object.products || []).forEach(prod => {
                                    const latest = prod.history || {};
                                    const currentPhaseId = latest.phase_id || '';
                                    const currentActivityId = latest.activity_id || '';
                                    const carouselDivId = `next_phase_station_${prod.product_id}_${currentPhaseId}`;
                                    const total    = Number(prod.progress?.total) || 0;
                                    const done     = Number(prod.progress?.done) || 0;
                                    const progress = Number(prod.progress?.value) ?? (total > 0 ? Math.round((done / total) * 100) : 0);


                                    const progressClass = progress === 100 
                                        ? 'bg-primary' 
                                        : progress === 0 
                                            ? 'bg-secondary' 
                                            : 'bg-warning';



                                    const stageKey = (prod.stage_history || []).at(-1)?.stage || prod.stage || '';
                                    const stageText = translateStage(stageKey); 

                                    const blockedStages = ['junk', 'cancel', 'pause', 'absage'];
                                    const currentStage = prod.stage?.toLowerCase?.() || '';
                                    const isBlocked = blockedStages.includes(currentStage);
                         
                                   
                                    let blockReason = null;
                                    if (isBlocked) {
                                        const stageHistory = prod.stage_history || [];
                                        const blockEntry = stageHistory
                                            .filter(entry => entry.stage === currentStage && entry.description)
                                            .sort((a, b) => new Date(b.changed_at) - new Date(a.changed_at))[0];
                                        blockReason = blockEntry?.description || null;
                                    }

                                    const markedImg = latest.marked_by_image ? `${EMPLOYEE_IMAGE}/${latest.marked_by_image}` : GENDER;
                                    const doneImg = latest.done_by_image ? `${EMPLOYEE_IMAGE}/${latest.done_by_image}` : GENDER;

                                    const note = (object.card_notes || []).find(n =>
                                        n.product_id == prod.product_id &&
                                        n.customer_id == object.customer_id &&
                                        n.alternative_id == object.id
                                    );
                                    const noteTitle = note?.title ?? '';
                                    const noteDescription = note?.description ?? '';

                                    const card = document.createElement('div');
                                    card.className = 'product-status-card card flex-fill mr-1 mt-1';
                                    card.style.maxWidth = 'calc(40% - 1rem)';

                                    const customerStages = window.AppData?.customerStages || {};
                                    const productInitials = window.AppData?.productInitials || {};
                                    const versionKey = `${prod.product_id}_${object.customer_id}_${object.id}_${prod.service_id}`;
                                    const version = customerStages?.[versionKey]?.version ?? '?';
                                    const initial = productInitials?.[prod.product_id] || 'NA';
                                    const versionLabel = `${initial}-V${version}`;
                                    console.log('PROGRESS:', latest.progress);


                                    const stageOrder = ['lead', 'offer', 'deal', 'project', 'review', 'archive', 'junk', 'cancel'];
                                    const activeStage = (prod.stage_history || []).at(-1)?.stage || prod.stage || 'lead';
                                    const currentIndex = stageOrder.indexOf(activeStage);


                                    const icons = {
                                        lead: 'fa fa-rocket',
                                        offer: 'feather icon-file-text',
                                        deal: 'fa fa-euro',
                                        project: 'fa fa-wrench',
                                        review: 'fa fa-bar-chart',
                                        archive: 'feather icon-package',
                                        junk: 'feather icon-slash',
                                        cancel: 'feather icon-x-circle'
                                    };

                                    const titles = {
                                        lead: 'Lead',
                                        offer: 'Angebot',
                                        deal: 'Auftrag',
                                        project: 'Montage',
                                        review: 'Auswertung',
                                        archive: 'Archiv',
                                        junk: 'Junk',
                                        cancel: 'Abgesagt'
                                    };

                                    
                                    const stageIconsHTML = stageOrder.map((stage, index) => {
                                        let bgColor = '#e9e9e9'; // default: future
                                        let textColor = '#1641194';

                                        if (index < currentIndex) {
                                            bgColor = '#93c21c'; // done
                                            textColor = '#fff5f5';

                                        } else if (index === currentIndex) {
                                            bgColor = '#c0d8ea'; // current
                                            textColor = '#000';
                                        }

                                        return `
                                            <i class="${icons[stage]} stage-icon"
                                                data-stage="${stage}"
                                                data-product-id="${prod.product_id}"
                                                data-customer-id="${object.customer_id}"
                                                data-alternative-id="${object.id}"
                                                data-service-id="${prod.service_id}"
                                                title="${titles[stage]}"
                                                style="
                                                    background:${bgColor};
                                                    color:${textColor};
                                                    font-size:14px;
                                                    padding:10px;
                                                    border-radius:50%;
                                                    box-shadow:0 1px 3px rgba(0,0,0,0.1);
                                                    margin: 0 4px;
                                                    transition:transform 0.2s ease;
                                                    cursor:pointer;
                                                ">
                                            </i>`;
                                    }).join('');


                            
                                    const stageIconsId = `stage-icons-${prod.product_id}`;
 

                                    card.innerHTML = `
                                        <div class="card-body p-2 position-relative" ${isBlocked ? 'style="pointer-events: none; opacity: 0.4;"' : ''}>
                                        ${isBlocked ? `
                                            <div class="locked-overlay text-center p-2" style="
                                                position: absolute;
                                                inset: 0;
                                                background: rgba(255,255,255,0.85);
                                                z-index: 10;
                                                display: flex;
                                                flex-direction: column;
                                                align-items: center;
                                                justify-content: center;
                                                pointer-events: auto;
                                                opacity: 1;">
                                                
                                                <i class="feather icon-lock text-danger mb-2"
                                                style="font-size: 24px; cursor: pointer;"
                                                onclick="changeProductStage(${prod.product_id}, ${object.customer_id}, ${object.id}, ${prod.service_id})"></i>
                                                
                                                <div class="text-black small text-center" style="font-size: 20px; line-height: 1.4;">
                                                    🚫 Dieses Projekt befindet sich im Status<br>
                                                    "<strong>${stageText}</strong>" und ist derzeit gesperrt.
                                                </div>

                                                ${prod.block_reason?.description ? `
                                                <div class="mt-2 small text-center text-dark">
                                                    <img src="${EMPLOYEE_IMAGE}/${prod.block_reason.changed_by?.image || GENDER}" 
                                                        class="rounded-circle mb-1" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <div><strong>${prod.block_reason.changed_by?.name || '-'}</strong></div>
                                                    <div>${prod.block_reason.changed_at || ''}</div>
                                                    <div class="text-danger mt-1">„${prod.block_reason.description}“</div>
                                                </div>` : ''}
                                            </div>` : ''}



                                            <div class="dropdown position-absolute" style="top: 6px; left: 0px; z-index: 15;">
                                                <button class="btn btn-sm btn-icon" type="button" id="cardMenu${prod.product_id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cardMenu${prod.product_id}">
                                                    <a class="dropdown-item" href="#" onclick="openProductHistory(${prod.product_id}, ${object.customer_id}, ${object.id})">
                                                        <i class="feather icon-clock mr-1"></i> Verlauf
                                                    </a> 
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteProductCard(${prod.id})">
                                                        <i class="feather icon-trash-2 mr-1"></i> Löschen
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white font-weight-bold d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; font-size: 14px;">${prod.initial || 'NA'}</div>
                                                    <img src="${EMPLOYEE_IMAGE}/${prod.employee?.image || GENDER}" class="rounded-circle"
                                                        style="width: 24px; height: 24px; object-fit: cover; position: absolute; left: 49px; top: 46px;">
                                                    <div class="ml-2 small">
                                                        <div>${prod.department?.name || 'Keine Abteilung'}</div>
                                                        <div>${services[prod.service] || ''}</div>
                                                        <div>${interests[prod.interest] || ''}</div>
                                                    </div>
                                                </div>
                                        
                                                <div>
                                                    <button class="btn btn-icon rounded-circle  open-report-modal" style="background:#93c21c  !important; padding:11px !important"
                                                       data-toggle="tooltip"
                                                       data-placement="top"
                                                       data-original-title="Kundenprozessbericht"
                                                        data-product-id="${prod.product_id}"
                                                        data-stage="${stageKey}"
                                                        data-service-id="${prod.service_id}"
                                                        data-customer-id="${object.customer_id}"
                                                        data-alternative-id="${object.id}">
                                                        <i class="fa fa-clipboard" style="font-size:18px; color:white;"></i>
                                                    </button> 

                                                    <button class="btn btn-icon rounded-circle  open-phase-modal" style="background:#73b1d4 !important; padding:11px !important"
                                                        data-toggle="tooltip"
                                                       data-placement="top"
                                                       data-original-title="Checkliste für Kundenaufgaben"
                                                        data-product-id="${prod.product_id}"
                                                        data-stage="${stageKey}"
                                                        data-service-id="${prod.service_id}"
                                                        data-customer-id="${object.customer_id}"
                                                        data-alternative-id="${object.id}">
                                                        <i class="feather icon-clipboard" style="font-size:18px; color:white;"></i>
                                                    </button> 
                                                </div>
                                                    
                                            </div>
                                            <div class="mb-1" style="float: right;  text-align: right;  margin-left: -44px;">
                                                <p id="product_version_details" class="mb-1">
                                                        ${prod.initial_with_version || 'NA-V?'}
                                                    </p>
                                            </div>
            
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>Status</strong>
                                                    <small class="text-primary">
                                                        <span>${stageText}</span>
                                                        <i class="feather icon-edit ml-1 allow-edit-stage"
                                                            onclick="changeProductStage(${prod.product_id}, ${object.customer_id}, ${object.id}, ${prod.service_id})"></i>
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-1 mt-1">
                                                    <div class="progress" style="height: 8px; width: 85%;" title="${done} von ${total} erledigt">
                                                        <div class="progress-bar ${progressClass}" style="width: ${progress}%"></div>
                                                    </div>
                                                    <div class="small text-muted" style="min-width: 40px;">${done}/${total}</div>

                                            
                                                </div> 
                                                <div class="stage-icons d-flex flex-wrap justify-content-center gap-2" id="${stageIconsId}"></div>


                                                
                                            </div>
                                        
                                            <hr class="my-1 bg-light">
                                                <div>
                                                    <strong class="text-primary">Projekttitel</strong><br>
                                                    <div id="note-title-container">
                                                        ${noteTitle ? `
                                                        <div class="note-title-view d-flex justify-content-between">
                                                            <span class="text-black">${noteTitle}</span>
                                                            <i class="feather icon-edit text-primary cursor-pointer"
                                                            data-customer-id="${object.customer_id}" 
                                                            data-alternative-id="${object.id}" 
                                                            data-product-id="${prod.product_id}"
                                                            onclick="toggleNoteEdit(this, 'title')"></i>
                                                        </div>` : `
                                                        <input type="text" class="form-control" placeholder="Titel eingeben"
                                                                onblur="saveNoteField(this, 'title', ${object.customer_id}, ${object.id}, ${prod.product_id})">`}
                                                    </div>
                                                    </div>

                                                    <hr class="my-1 bg-light">

                                                    <div>
                                                    <strong class="text-primary">Projektbeschreibung</strong><br>
                                                    <div id="note-description-container">
                                                        ${noteDescription ? `
                                                        <div class="note-desc-view d-flex justify-content-between">
                                                            <div>${noteDescription.replace(/\n/g, '<br>')}</div>
                                                            <i class="feather icon-edit text-primary cursor-pointer"
                                                            data-customer-id="${object.customer_id}" 
                                                            data-alternative-id="${object.id}" 
                                                            data-product-id="${prod.product_id}"
                                                            onclick="toggleNoteEdit(this, 'description')"></i>
                                                        </div>` : `
                                                        <textarea class="form-control" placeholder="Beschreibung eingeben"
                                                                    onblur="saveNoteField(this, 'description', ${object.customer_id}, ${object.id}, ${prod.product_id})"></textarea>`}
                                                    </div>
                                                    </div>


                                            <hr class="my-1 bg-light">
                                            <div><strong class="text-primary">Phase</strong><br>${stageText}</div>
                                            <hr class="my-1 bg-light">
                                            <div><strong class="text-primary">Arbeitsschritt</strong><br>${latest.phase_name || '–'}</div>
                                            <hr class="my-1 bg-light">
                                            <div><strong class="text-primary">Aufgabe</strong><br>${latest.activity_title || '–'}</div>
                                            <hr class="my-1 bg-light">
                                            <div><strong class="text-primary">Zuständig</strong><br>${latest.done_by_name || '–'}
                                                <img src="${doneImg}" class="rounded-circle ml-1"
                                                    style="width: 40px; height: 40px; object-fit: cover; float: right; top: -12px; position: relative;">
                                            </div>
                                            <hr class="my-1 mt-2 bg-light">
                                            <div><strong class="text-primary">Erledigt am</strong><br>${latest.marked_by_name || '–'}<br>
                                                ${latest.changed_at ? new Date(latest.changed_at).toLocaleDateString('de-DE') : '–'}
                                                <img src="${markedImg}" class="rounded-circle ml-1"
                                                    style="width: 40px; height: 40px; object-fit: cover; float: right; top: -26px; position: relative;">
                                            </div>
                                            <hr class="my-1 mt-2 bg-light">
                                            <div><strong class="text-primary">Nächster Schritt</strong><br>
                                                <div id="${carouselDivId}" class="activity-carousel-loader"
                                                    data-product-id="${prod.product_id}"
                                                    data-phase-id="${currentPhaseId}"
                                                    data-activity-id="${currentActivityId}">
                                                    <i class="fa fa-hourglass-half"></i>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    const iconWrapper = card.querySelector(`#${stageIconsId}`);
                                    if (iconWrapper) iconWrapper.innerHTML = stageIconsHTML;


                                    productRow.appendChild(card);

                                   

                                    setTimeout(() => {
                                        const iconWrapper = card.querySelector('.stage-icons');
                                        highlightStageIcons(stageKey, iconWrapper);
                                        card.querySelectorAll(`#${stageIconsId} i`).forEach(icon => {
                                            icon.addEventListener('click', () => {
                                                const stage = icon.dataset.stage;
                                                const productId = icon.dataset.productId;
                                                const customerId = icon.dataset.customerId;
                                                const alternativeId = icon.dataset.alternativeId;
                                                const serviceId = icon.dataset.serviceId;

                                                confirmAndChangeStage(productId, customerId, alternativeId, serviceId, stage);
                                            });
                                        });

                                    }, 10);



                                    if (currentPhaseId && currentActivityId) {
                                        fetch(`/activity/carousel?phase_id=${currentPhaseId}&activity_id=${currentActivityId}&product_id=${prod.product_id}`)
                                            .then(res => res.text())
                                            .then(html => {
                                                const el = document.getElementById(carouselDivId);
                                                if (el) el.innerHTML = html;
                                            })
                                            .catch(() => {
                                                const el = document.getElementById(carouselDivId);
                                                if (el) el.innerHTML = '<span class="text-danger">Fehler</span>';
                                            });
                                    }
                                });

                                block.appendChild(productRow);
                                row.appendChild(block);
                            });

                            container.appendChild(row);
                        })
                        .catch(err => {
                            console.error("❌ Fetch failed:", err);
                            container.innerHTML = "<p class='text-danger'>🚨 Fehler beim Laden des Dashboards.</p>";
                        });


            
        }; 




    window.showDashboard = (el = null) => {
        // Use clicked button or fallback
        if (!el || !el.dataset) {
            el = document.querySelector('.dashboard-btn');
        }

        const customerId = el?.dataset?.customerId;
        const alternativeId = el?.dataset?.alternativeId;

        if (!customerId || !alternativeId) {
            return Swal.fire('Fehler', 'Kunde oder Alternative fehlt.', 'error');
        }

        // Load Notes
        const noteList = document.getElementById('note-list');
        if (noteList) {
            noteList.innerHTML = '<div class="text-muted">Lade allgemeine Notizen...</div>';

            fetch(`/customer-notes/${customerId}/${alternativeId}/general`)
                .then(res => res.text())
                .then(html => {
                    noteList.innerHTML = html;
                    feather.replace();
                    if (typeof initNoteListeners === 'function') initNoteListeners();

                    noteList.dataset.customerId = customerId;
                    noteList.dataset.alternativeId = alternativeId;
                    noteList.dataset.productId = '';
                    noteList.dataset.noteType = 'general';

                    const title = document.getElementById('note_title');
                    if (title) title.textContent = 'ALLGEMEIN';
                })
                .catch(() => {
                    noteList.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
                });
        }

        // Load Blade view into #mainContent, then load dynamic dashboard JSON
        const mainContent = document.getElementById('mainContent');
        if (mainContent) {
            mainContent.innerHTML = '<div class="text-muted p-2">Dashboard wird geladen...</div>';

            fetch(`/dashboard/customer/${customerId}/alternative/${alternativeId}`)
                .then(res => res.text())
                .then(html => {
                    mainContent.innerHTML = html;
                    feather.replace();
                    if (typeof initDashboardListeners === 'function') initDashboardListeners();

                    // 🔁 Now load dynamic dashboard data via dashboardLoad
                    if (typeof window.loadDashboard === 'function') {
                        window.loadDashboard(); // will populate #dashboard
                    }
                })
                .catch(() => {
                    mainContent.innerHTML = '<div class="text-danger p-2">Fehler beim Laden des Dashboards.</div>';
                });
        }
    };

    function setActiveSubNav(button) {
        const container = button.closest('.sub-nav');
        container.querySelectorAll('.nav-section-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');

        const productKey = container.id;
        const btnText = button.innerText.trim();
        localStorage.setItem(`activeSubNav_${productKey}`, btnText);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.sub-nav').forEach(container => {
            const productKey = container.id;
            const savedText = localStorage.getItem(`activeSubNav_${productKey}`);
            if (savedText) {
                const buttons = container.querySelectorAll('.nav-section-btn');
                buttons.forEach(btn => {
                    if (btn.innerText.trim() === savedText) {
                        btn.classList.add('active');

                        // ✅ Trigger the click to auto-load content
                        setTimeout(() => btn.click(), 300);
                    }
                });
            }
        });
    });


    function resetAllSubNavs() {
        // Clear all sub-nav active states
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('activeSubNav_')) {
                localStorage.removeItem(key);
            }
        });

        // Remove all active classes
        document.querySelectorAll('.nav-section-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Optional: hide all sub-nav sections
        // document.querySelectorAll('.sub-nav').forEach(container => {
        //     container.style.display = 'none';
        // });

        // Auto-close dropdown (if inside one)
        setTimeout(() => {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }, 100);

        // Show success alert
        Swal.fire({
            icon: 'success',
            title: 'Zurückgesetzt',
            text: 'Alle aktiven Navigationszustände wurden entfernt.',
            timer: 1500,
            showConfirmButton: false
        });

        // Optionally refresh feather icons if layout changed
        if (typeof feather !== 'undefined') feather.replace();
    }




    function openProductHistory(product_id, customer_id, alternative_id) {
        fetch(`/lead-product/stage-history/${customer_id}/${alternative_id}/${product_id}`)
            .then(res => res.json())
            .then(data => {
                if (!Array.isArray(data.history) || data.history.length === 0) {
                    return Swal.fire('Keine Daten', 'Es gibt keine Verlaufsdaten für dieses Produkt.', 'info');
                }

                const timelineItems = data.history.map(entry => {
                    const date = new Date(entry.changed_at).toLocaleString('de-DE');
                    const user = data.users?.[entry.changed_by] || `Mitarbeiter ID: ${entry.changed_by}`;
                    const stage = translateStage(entry.stage);
                    const note = entry.description ? `<div class="text-muted mt-1">${entry.description}</div>` : '';
                    
                    return `
                        <div class="timeline-item mb-3">
                            <div><strong><i class="feather icon-map-pin"></i>${stage}</strong> <small class="text-muted">(${date})</small></div>
                            <div><i class="feather icon-user"></i> ${user}</div>
                            ${note}
                            <hr class="my-1">
                        </div>
                    `;
                }).reverse().join('');

                Swal.fire({
                    title: 'Phasenverlauf',
                    html: `<div style="max-height: 400px; overflow-y: auto; text-align: left;">${timelineItems}</div>`,
                    width: 600,
                    confirmButtonText: 'Schließen'
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Fehler', 'Verlauf konnte nicht geladen werden.', 'error');
            });
    }


    function translateStage(stage) {
        const map = {
            lead: 'Lead',
            offer: 'Angebot',
            deal: 'Auftrag',
            project: 'Montage',
            completed: 'Abgeschlossen',
            archive: 'Archiv',
            pause: 'Pause',
            junk: 'Junk',
            cancel: 'Absage',
            ticket: 'Ticket',
            evaluation: 'Bewertung'
        };
        return map[stage] || stage;
    }


    window.saveNoteField = function (el, field, customerId, altId, productId) {
        const value = el.value.trim();
        if (!value) return;

        // Optional: Send AJAX here
        fetch('/save-customer-card-note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ field, value, customer_id: customerId, alternative_id: altId, product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            // Replace input/textarea with view mode
            const container = document.getElementById(`note-${field}-container`);
            const html = `
                <div class="note-${field}-view d-flex justify-content-between">
                    <${field === 'title' ? 'span' : 'div'} class="text-black">
                        ${value.replace(/\n/g, '<br>')}
                    </${field === 'title' ? 'span' : 'div'}>
                    <i class="feather icon-edit text-primary cursor-pointer"
                    data-customer-id="${customerId}" data-alternative-id="${altId}" data-product-id="${productId}"
                    onclick="toggleNoteEdit(this, '${field}')"></i>
                </div>`;
            container.innerHTML = html;
        });
    };


    window.toggleNoteEdit = function (icon, field) {
        const container = document.getElementById(`note-${field}-container`);
        if (!container) return;

        const wrapper = container.querySelector(`.note-${field}-view`);
        const currentText = wrapper?.querySelector('span, div')?.innerText?.trim() || '';

        const inputHtml = (field === 'title')
            ? `<input class="form-control note-input" value="${currentText}" 
                    onblur="saveNoteField(this, '${field}', ${icon.dataset.customerId}, ${icon.dataset.alternativeId}, ${icon.dataset.productId})">`
            : `<textarea class="form-control note-input" 
                        onblur="saveNoteField(this, '${field}', ${icon.dataset.customerId}, ${icon.dataset.alternativeId}, ${icon.dataset.productId})">${currentText}</textarea>`;

        container.innerHTML = inputHtml;
    };



    window.loadActivityCarousel = function (phaseId, activityId, productId) {
        const containerId = `next_phase_station_${productId}_${phaseId}`;
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '<i class="fa fa-hourglass-half" ></i>';
        fetch(`/activity/carousel?phase_id=${phaseId}&activity_id=${activityId}&product_id=${productId}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(() => {
                container.innerHTML = '<span class="text-danger">Fehler</span>';
            });
    }


    function parseJSON(json) {
        try {
            return typeof json === 'string' ? JSON.parse(json) : json;
        } catch (err) {
            console.warn("⚠️ JSON parse error:", json);
            return [];
        }
    }


    function getInitials(fullName) {
        if (!fullName) return '–';
        const parts = fullName.trim().split(' ');
        return (parts[0]?.charAt(0) || '') + (parts[1]?.charAt(0) || '');
    }

    function translateStage(stage) {
        const map = {
            offer: 'Angebot',
            deal: 'Auftrag',
            project: 'Montage',
            complete: 'Abschluss',
            completed: 'Abschluss',
            ticket: 'Ticket',
            evaluation: 'Auswertung',
            archive: 'Archiv',
            lead: 'Lead',
            pause: 'Pause',
            junk: 'Junk'
        };
        return map[stage] || stage;
    }

    // 🔄 Sidebar modal trigger
    $(document).on('click', '.open-phase-modal', function () {
        const $el = $(this);
        const sidebar = document.getElementById('phaseSidebar');
        const sidebarBody = sidebar.querySelector('.phase-sidebar-body');

        const productId = $el.data('product-id');
        const serviceId = $el.data('service-id');
        const stage = $el.data('stage');
        const customerId = $el.data('customer-id');
        const alternativeId = $el.data('alternative-id');
        const productInitial = $el.closest('.entry-row').find('.icon').text().trim() || '—';

        $('#phaseProductInitial').text(productInitial);
        sidebarBody.dataset.customerId = customerId;
        sidebarBody.dataset.alternativeId = alternativeId;
        sidebarBody.dataset.productId = productId;
        sidebarBody.dataset.serviceId = serviceId;

        sidebar.classList.add('open');
        sidebarBody.innerHTML = '<p>Lade...</p>';

        $.get('/modal/history', {
            product_id: productId,
            service_id: serviceId,
            stage,
            customer_id: customerId,
            alternative_id: alternativeId
        })
            .done(response => {
                    sidebarBody.innerHTML = response;

                    // 👇 Expand current stage group
                    const selector = `[data-toggle="collapse"][data-target="#collapse-${stage}"]`;
                    const toggleEl = sidebarBody.querySelector(selector);
                    const collapseId = toggleEl?.getAttribute('data-target');

                    if (collapseId) {
                        const content = sidebarBody.querySelector(collapseId);
                        if (content && !content.classList.contains('show')) {
                            $(content).collapse('show');
                        }
                    }

                    // ✅ Now run validation/locking logic after content loads
                    initActivityValidation();
                })
            .fail(() => {
                sidebarBody.innerHTML = '<p class="text-danger">❌ Fehler beim Laden der Phasen.</p>';
            });
    });


    function initActivityValidation() {
        const currentUserId = '{{ auth()->user()->name }}';

        $('.history-checkbox').off('change').on('change', function (e) {
            const checkbox = this;
            const selectedDoneBy = $(checkbox).closest('tr').find('.done-by-select').val();

            if (!selectedDoneBy || selectedDoneBy !== currentUserId) {
                e.preventDefault();
                checkbox.checked = false;

                Swal.fire({
                    icon: 'warning',
                    title: 'Nicht erlaubt',
                    text: 'Nur der zugewiesene Mitarbeiter darf diese Aufgabe als erledigt markieren.',
                });
            }
        });

        $('.done-by-select').each(function () {
            const select = $(this);
            const row = select.closest('tr');
            const checkbox = row.find('.history-checkbox');

            if (checkbox.prop('checked')) {
                select.prop('disabled', true);

                if (!select.next('.unlock-icon').length) {
                    const lock = $('<i class="feather icon-lock ml-1 text-danger cursor-pointer unlock-icon" title="Entsperren?"></i>');
                    select.after(lock);

                    lock.on('click', function () {
                        Swal.fire({
                            title: 'Passwort erforderlich',
                            input: 'password',
                            inputLabel: 'Gib dein Passwort ein',
                            inputAttributes: {
                                autocapitalize: 'off',
                                autocomplete: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Bestätigen',
                            showLoaderOnConfirm: true,
                            preConfirm: (password) => {
                                return fetch('/verify-unlock', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    },
                                    body: JSON.stringify({
                                        password,
                                        required_role: 'Customer'
                                    })
                                }).then(res => {
                                    if (!res.ok) throw new Error('Fehlgeschlagen');
                                    return res.json();
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed && result.value.success) {
                                Swal.fire({
                                    title: 'Entsperrt!',
                                    icon: 'success'
                                });
                                select.prop('disabled', false);
                                lock.remove();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Nicht erlaubt',
                                    text: result.value.message || 'Zugriff verweigert.',
                                });
                            }
                        });
                    });
                }
            }
        });

        $('[data-toggle="tooltip"]').tooltip();
    }


    

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePhaseSidebar();
        }
    });

    function closePhaseSidebar() {
        document.getElementById('phaseSidebar').classList.remove('open');
        setTimeout(() => {
            document.dispatchEvent(new Event("DOMContentLoaded"));
        }, 300);
    }


    $(document).on('click', '.change_stage', function () {
        const $btn = $(this);
        const customer_id = $btn.data('customer-id');
        const alternative_id = $btn.data('alternative-id');
        const product_id = $btn.data('product-id');
        const stage = $btn.data('stage');
        const service = $btn.data('service');
        const service_id = $btn.data('service-id');
        const employee_id = $btn.data('employee-id');
        const department_id = $btn.data('department-id');

        Swal.fire({
            title: 'Notiz zur Phase: ' + stage.toUpperCase(),
            html: `<div id="quillEditor" style="height: 200px;"></div>`,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            didOpen: () => {
                const quill = new Quill('#quillEditor', { theme: 'snow' });
                window.currentQuill = quill;
            },
            preConfirm: () => {
                const description = window.currentQuill.root.innerHTML;

                return fetch(`/lead/kanban/${customer_id}/${alternative_id}/${product_id}/${employee_id}/${service}/${stage}/${service_id}/${department_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    body: JSON.stringify({
                        description
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error("Backend-Fehler beim Statuswechsel");
                    return response.json();
                })
                .then(response => {
                    if (!response.success) throw new Error(response.message || 'Fehler beim Speichern');
                    Swal.fire('Erfolgreich!', 'Phase und Notiz gespeichert.', 'success').then(() => location.reload());
                })
                .catch(err => {
                    Swal.showValidationMessage(`Fehler: ${err.message}`);
                });
            }
        });
    });



    function deleteProductCard(leadProductId) {
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Dieses Produkt wird dauerhaft gelöscht.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/lead-product-lists/${leadProductId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Gelöscht!',
                            text: 'Das Produkt wurde erfolgreich gelöscht.',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // Refresh page after short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1300);
                    } else {
                        throw new Error(data.message || 'Löschen fehlgeschlagen.');
                    }
                })
                .catch(err => {
                    Swal.fire('Fehler', err.message, 'error');
                });
            }
        });
    }


</script>


 
<script>
function changeProductStage(productId, customerId, alternativeId, serviceId) {
    Swal.fire({
        title: 'Phase wirklich ändern?',
        text: 'Ihre Aufgabenhistorie wird anschließend entsprechend Ihrer letzten Aktivität dargestellt.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, weiter',
        cancelButtonText: 'Abbrechen'
    }).then(confirmRes => {
        if (!confirmRes.isConfirmed) return;

        Swal.fire({
            title: 'Phase auswählen',
            input: 'select',
            inputOptions: {
                'lead': 'Lead',
                'offer': 'Angebot',
                'deal': 'Auftrag',
                'project': 'Montage',
                'completed': 'Abgeschlossen',
                'archive': 'Archiv', 
                'pause': 'Pause',
                'junk': 'Junk',
                'cancel': 'Absage'
            },
            inputPlaceholder: 'Neue Phase wählen...',
            showCancelButton: true,
            confirmButtonText: 'Weiter',
        }).then(result => {
            if (!result.isConfirmed || !result.value) return;

            const selectedStage = result.value;

            Swal.fire({
                title: 'Notiz zur Phase',
                html: '<div id="quill-editor" style="height:200px;"></div>',
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                didOpen: () => {
                    window.quill = new Quill('#quill-editor', { theme: 'snow' });
                },
                preConfirm: () => {
                    return window.quill.root.innerHTML;
                }
            }).then(({ isConfirmed, value: note }) => {
                if (!isConfirmed || !note) return;

                const employeeId = window.currentEmployeeId || 0;
                const service = 'complete';
                const departmentId = 0;

                const postData = {
                    description: note
                };

                fetch(`/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${selectedStage}/${serviceId}/${departmentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(postData)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Gespeichert!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Fehler', res.message || 'Unbekannter Fehler', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Fehler', 'Verbindungsfehler beim Speichern.', 'error');
                });
            });
        });
    });
}

function confirmAndChangeStage(productId, customerId, alternativeId, serviceId, selectedStage) {
    Swal.fire({
        title: 'Notiz zur Phase',
        html: '<div id="quill-editor" style="height:200px;"></div>',
        showCancelButton: true,
        confirmButtonText: 'Speichern',
        didOpen: () => {
            window.quill = new Quill('#quill-editor', { theme: 'snow' });
        },
        preConfirm: () => {
            return window.quill.root.innerHTML;
        }
    }).then(({ isConfirmed, value: note }) => {
        if (!isConfirmed || !note) return;

        const employeeId = window.currentEmployeeId || 0;
        const service = 'complete';
        const departmentId = 0;

        const postData = { description: note };

        fetch(`/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${selectedStage}/${serviceId}/${departmentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(postData)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire('Gespeichert!', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Fehler', res.message || 'Unbekannter Fehler', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Fehler', 'Verbindungsfehler beim Speichern.', 'error');
        });
    });
}

</script>

 


<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector('.phase-sidebar-body');
    if (!sidebar) return;

    // ✅ Reusable save function
    function sendHistoryUpdate(data, row) {
        fetch("{{ route('ajax.save.customer.history') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const initialsCell = row.querySelector('.mark-by-cell');
                if (initialsCell) initialsCell.innerText = res.initials;

                const dateInput = row.querySelector('input[type="date"]');
                if (dateInput && !dateInput.value && res.done_date) {
                    dateInput.value = res.done_date;
                }

                row.classList.add('table-success');
                setTimeout(() => row.classList.remove('table-success'), 2000);
            } else {
                alert("⚠️ Fehler beim Speichern");
            }
        })
        .catch(() => {
            alert("⚠️ AJAX Fehler");
        });
    }

    // ✅ General row-wide change listener (for checkbox, date, note)
    sidebar.addEventListener('change', function (e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        const checkbox = row.querySelector('.history-checkbox');
        const dateInput = row.querySelector('input[type="date"]');
        const notesTextarea = row.querySelector('textarea');
        const doneBySelect = row.querySelector('.done-by-select');

        const data = {
            activity_id: checkbox.dataset.activityId,
            phase_id: checkbox.dataset.phaseId,
            customer_id: sidebar.dataset.customerId,
            alternative_id: sidebar.dataset.alternativeId,
            product_id: sidebar.dataset.productId,
            service_id: sidebar.dataset.serviceId,
            is_done: checkbox.checked ? 1 : 0,
            done_date: dateInput?.value || null,
            notes: notesTextarea?.value?.trim() || null,
            done_by: doneBySelect?.value || null
        };

        sendHistoryUpdate(data, row);
    });

    // ✅ Manual blur on note field (optional)
    sidebar.querySelectorAll('.note-textarea').forEach(textarea => {
        textarea.addEventListener('blur', function () {
            const row = this.closest('tr');
            const payload = {
                activity_id: this.dataset.activityId,
                phase_id: this.dataset.phaseId,
                customer_id: sidebar.dataset.customerId,
                alternative_id: sidebar.dataset.alternativeId,
                product_id: sidebar.dataset.productId,
                service_id: sidebar.dataset.serviceId,
                notes: this.value
            };
            sendHistoryUpdate(payload, row);
        });
    });

   

});

function closePhaseSidebar() {
    const sidebar = document.querySelector('#phaseSidebar');
    if (sidebar) sidebar.classList.remove('open');
}


</script>
 


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
            container: 'body' // Prevents clipping inside modals or containers
        });
    });

</script>


<script>
function uploadActivityFile(input) {
    const form = input.closest('form');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.closest('label').classList.add('selected');

            Swal.fire({
                icon: 'success',
                title: 'Upload Erfolgreich',
                text: '📎 ' + data.filename,
                timer: 2000,
                showConfirmButton: false
            });

            // Optional: reload sidebar or part of UI
            // if (typeof reloadSidebar === 'function') reloadSidebar();

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: data.message || 'Upload fehlgeschlagen.'
            });
        }
    })
    .catch(err => {
        console.error("Upload error", err);
        Swal.fire({
            icon: 'error',
            title: 'Upload-Fehler',
            text: err.message || 'Ein unerwarteter Fehler ist aufgetreten.'
        });
    });
}
</script> 


<script>
function loadTask(button) {
    const customerId     = button.getAttribute('data-customer-id');
    const alternativeId  = button.getAttribute('data-alternative-id');
    const productId      = button.getAttribute('data-product-id');
    const productListId  = button.getAttribute('data-product-list-id');

    // Store context globally
    lastTaskContext = { customerId, alternativeId, productId, productListId };

    const container = document.getElementById("mainContent");
    container.innerHTML = `<div class="text-center p-3"><span class="spinner-border text-primary"></span></div>`;

    fetch(`/load/task/view?customer_id=${customerId}&alternative_id=${alternativeId}&product_id=${productId}&product_list_id=${productListId}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            const createTaskBtn = container.querySelector('.create_new_task');
            if (createTaskBtn) {
                createTaskBtn.setAttribute('data-customer-id', customerId);
                createTaskBtn.setAttribute('data-alternative-id', alternativeId);
                createTaskBtn.setAttribute('data-product-id', productId);
                createTaskBtn.setAttribute('data-product-list-id', productListId);
            }

            initSortable();
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden der Aufgaben</div>`;
        });
}

function loadTaskData(customerId, alternativeId, productId, productListId) {
    const container = document.getElementById("mainContent");
    container.innerHTML = `<div class="text-center p-3"><span class="spinner-border text-primary"></span></div>`;

    fetch(`/load/task/view?customer_id=${customerId}&alternative_id=${alternativeId}&product_id=${productId}&product_list_id=${productListId}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            const createTaskBtn = container.querySelector('.create_new_task');
            if (createTaskBtn) {
                createTaskBtn.setAttribute('data-customer-id', customerId);
                createTaskBtn.setAttribute('data-alternative-id', alternativeId);
                createTaskBtn.setAttribute('data-product-id', productId);
                createTaskBtn.setAttribute('data-product-list-id', productListId);
            }

            initSortable();
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden der Aufgaben</div>`;
        });
}

$(document).ready(function () {
    function validateForm() {
        let errors = [];
        let taskTitle = $('#task_title').val().trim();
        let dueDate = $('#due_date').val().trim();

        if (!taskTitle) errors.push('Bitte geben Sie einen Aufgabentitel ein.');
        if (!dueDate) errors.push('Bitte wählen Sie ein Fälligkeitsdatum.');

        return errors;
    }

    function submitTaskForm(closeAfterSave) {
        let errors = validateForm();
        if (errors.length > 0) {
            Swal.fire({ icon: 'warning', title: 'Formular ungültig', html: errors.join('<br>') });
            return;
        }

        let formData = $('#task_form').serialize();

        $.ajax({
            type: 'POST',
            url: "{{ route('personal.task.customer.store') }}",
            data: formData,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Erfolgreich gespeichert!',
                    text: 'Die Aufgabe wurde erfolgreich gespeichert.',
                }).then(() => {
                    $('.new_task').removeClass('active');

                    const { customerId, alternativeId, productId, productListId } = lastTaskContext;
                    loadTaskData(customerId, alternativeId, productId, productListId);
                });

                // Optionally reset form if not closing modal
                if (!closeAfterSave) {
                    $('#task_form')[0].reset();
                }
            },

            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Ein Fehler ist aufgetreten.';
                Swal.fire({ icon: 'error', title: 'Fehler', text: errorMsg });
            }
        });
    }

    $('.save-task-close').on('click', function () {
        submitTaskForm(true);
    });

    $('.save-task-continue').on('click', function () {
        submitTaskForm(false);
    });
});

// 🔄 Enable drag-and-drop after content is loaded
function initSortable() {
    document.querySelectorAll('.kanban-column').forEach(col => {
        new Sortable(col, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const taskId = evt.item.dataset.taskId;
                const newStatus = evt.to.dataset.status;

                fetch(`/personal_task/update_status/${taskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(res => res.json())
                .then(data => console.log('✅ Status aktualisiert:', data));
            }
        });
    });
}

// 🔍 Search filter
function filterTasks() {
    const term = document.getElementById('taskSearchInput').value.toLowerCase();
    document.querySelectorAll('.task-card').forEach(card => {
        const title = card.dataset.title || '';
        card.style.display = title.includes(term) ? 'block' : 'none';
    });
}

// 💬 Toggle comment input
function toggleTaskNote(taskId) {
    const wrapper = document.getElementById(`task-note-wrapper-${taskId}`);
    if (wrapper.style.display === 'none') {
        wrapper.style.display = 'block';
        loadTaskNotes(taskId);
    } else {
        wrapper.style.display = 'none';
    }
}

function submitTaskNote(event, taskId) {
    event.preventDefault();
    const form = event.target;
    const comment = form.comment.value;

    fetch('/ajax/task_note/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ task_id: taskId, comment: comment })
    })
    .then(res => res.json())
    .then(() => {
        form.reset();
        loadTaskNotes(taskId);
    });
}

function loadTaskNotes(taskId) {
    fetch(`/ajax/task_note/list/${taskId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById(`comment-list-${taskId}`).innerHTML = html;
        });
}

// ✅ Call initSortable on page load in case it's not inside loadTask
document.addEventListener('DOMContentLoaded', () => {
    initSortable();
});


</script>
 

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.querySelector('.new_task');

    // 🔁 Use event delegation to catch future .create_new_task buttons
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.create_new_task');
        if (!button) return;

        const customerId    = button.dataset.customerId || '';
        const alternativeId = button.dataset.alternativeId || '';
        const productId     = button.dataset.productId || '';

        // ✅ Fill the hidden input fields inside the form
        document.getElementById('select_customer_id').value = customerId;
        document.getElementById('select_alternative_id').value = alternativeId;
        document.getElementById('select_product_id').value = productId;

        // ✅ Show modal
        modal.classList.add('active');
    });

    // ❌ Close the modal
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.close_task_window')) {
            modal.classList.remove('active');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapse = document.getElementById('collapseTaskKeys');
    const topEmployeeSection = document.getElementById('task_employee_section');

    $('#collapseTaskKeys').on('show.bs.collapse', function () {
        topEmployeeSection.style.display = 'none';
    });

    $('#collapseTaskKeys').on('hide.bs.collapse', function () {
        topEmployeeSection.style.display = 'block';
    });
});
</script>


<script>
    $(document).ready(function () {
        
        

        // Initialize select2 for existing rows
        initSelect2();  

        // Initialize Select2 for dynamically added rows
        function initSelect2() {
            $('.employee').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }

        // Employee formatting for Select2
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

      
    });
</script>


 

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let startDateInput = document.getElementById("start_date");
        let dueDateInput = document.getElementById("due_date");
        let dueTimeInput = document.getElementById("due_time");
        let totalDayInput = document.getElementById("total_day");
        let totalTimeInput = document.getElementById("total_time");

        // Make sure employeeOptions is defined globally (inject from backend)
        let employeeOptions = window.employeeOptions || [];

        function calculateTotalDaysAndHours() {
            let startDate = new Date(startDateInput.value);
            let dueDate = new Date(dueDateInput.value);

            if (!startDateInput.value || !dueDateInput.value || isNaN(startDate) || isNaN(dueDate)) {
                totalDayInput.value = "";
                totalTimeInput.value = "";
                return;
            }

            let workHoursPerDay = 24;
            let totalDays = 0;
            let totalWorkingHours = 0;
            let tempDate = new Date(startDate);

            while (tempDate <= dueDate) {
                let day = tempDate.getDay();
                if (day !== 0 && day !== 6) {
                    totalDays++;
                    totalWorkingHours += workHoursPerDay;
                }
                tempDate.setDate(tempDate.getDate() + 1);
            }

            if (dueTimeInput.value) {
                let [dueHour, dueMinute] = dueTimeInput.value.split(":").map(Number);
                let remainingHours = dueHour + (dueMinute > 0 ? 1 : 0);
                let lastDay = new Date(dueDate);
                let lastDayOfWeek = lastDay.getDay();

                while (lastDayOfWeek === 0 || lastDayOfWeek === 6) {
                    lastDay.setDate(lastDay.getDate() + 1);
                    lastDayOfWeek = lastDay.getDay();
                }

                totalWorkingHours -= workHoursPerDay;
                totalWorkingHours += remainingHours;
            }

            totalDayInput.value = totalDays;
            totalTimeInput.value = totalWorkingHours;

            updateTotalDuration();
        }

        function updateTotalDuration() {
            let total = 0;
            $('.task-duration').each(function () {
                let val = parseInt($(this).val()) || 0;
                total += val;
            });

            let allowed = parseInt($('#total_time').val()) || 0;
            let diff = allowed - total;

            $('#key_total_time').text(diff >= 0 ? `${diff} Std` : `Überschreitung um ${Math.abs(diff)} Std!`);

            if (total > allowed) {
                Swal.fire({
                    icon: "error",
                    title: "⚠ Zeitüberschreitung!",
                    text: `Die gesamte Dauer der Aufgaben beträgt ${total} Stunden, überschreitet jedoch die geplanten ${allowed} Stunden.`,
                });
            }
        }

        function initSelect2WithImages(selector) {
            $(selector).select2({
                templateResult: formatEmployeeOption,
                templateSelection: formatEmployeeSelection,
                escapeMarkup: m => m
            });
        }

        function formatEmployeeOption(option) {
            if (!option.id) return option.text;
            let img = $(option.element).data('image') || GENDER;
            return `
                <div class="d-flex align-items-center">
                    <img src="${img}" class="rounded-circle me-1" style="width: 28px; height: 28px; object-fit: cover;">
                    <span>${option.text}</span>
                </div>
            `;
        }

        function formatEmployeeSelection(option) {
            return option.text;
        }

        $(document).ready(function () {
            let keyTaskIndex = $('#key_task tbody tr').length;

            initSelect2WithImages('select[name^="key"][name$="[employee_id][]"]');

            $(document).on('click', '.add-task-steps', function () {
                keyTaskIndex++;
                let rowCount = $('#key_task tbody tr').length;

                let employeeOptionsHtml = employeeOptions.map(emp => {
                    return `<option value="${emp.id}" data-image="${emp.image}">${emp.name} ${emp.lastname}</option>`;
                }).join('');

                let newRow = `
                    <tr>
                        <td>${rowCount + 1}</td>
                        <td><input type="text" name="key[${keyTaskIndex}][task]" class="form-control"></td>
                        <td><input type="number" name="key[${keyTaskIndex}][duration]" class="form-control task-duration"></td>
                        <td>
                            <select name="key[${keyTaskIndex}][employee_id][]" class="form-control employee-select" multiple style="width:100%">
                                ${employeeOptionsHtml}
                            </select>
                        </td>
                        <td><textarea name="key[${keyTaskIndex}][key_description]" class="form-control"></textarea></td>
                        <td>
                            <button type="button" class="btn btn-icon btn-primary add-task-steps"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-icon btn-danger remove-task-steps"><i class="fa fa-minus"></i></button>
                        </td>
                    </tr>
                `;

                let $newRow = $(newRow);
                $('#key_task tbody').append($newRow);
                initSelect2WithImages($newRow.find('.employee-select'));
                updateTotalDuration();

            });

            $(document).on('click', '.remove-task-steps', function () {
                if ($('#key_task tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowIndexes();
                    updateTotalDuration();
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Achtung",
                        text: "Es muss mindestens ein Aufgabenschritt vorhanden sein.",
                    });
                }
            });

            $(document).on('input', '.task-duration', updateTotalDuration);

            function updateRowIndexes() {
                $('#key_task tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                    $(this).find('input, textarea, select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            name = name.replace(/\[\d+]/, `[${index}]`);
                            $(this).attr('name', name);
                        }
                    });
                });
            }

            startDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueTimeInput.addEventListener("change", calculateTotalDaysAndHours);

            updateTotalDuration();
        });
    });
</script>

 

<!-- Duplicate time area: end  -->
  
    <!-- Priority Script  -->
<script>
    $(document).ready(function () {
        // Add click event listener to each dropdown-item
        $('#color_drop_down .dropdown-item').on('click', function () {
            // Get the selected color value from the data-value attribute
            const selectedColor = $(this).data('value');

            // Update the hidden input value
            $('#color').val(selectedColor);

            // Update the icon's color
            $('#colorIcon').css('color', selectedColor);
        });

          // Add click event listener to each dropdown-item
            $('#priority_select .dropdown-item').on('click', function () {
                // Get the selected priority value from the data-value attribute
                const selectedPriority = $(this).data('value');

                // Get the selected icon's HTML
                const selectedIcon = $(this).html();

                // Update the hidden input value
                $('input[name="priority"]').val(selectedPriority);

                // Update the button's icon
                $('#priority_select button').html(selectedIcon);
            });
        
    });


</script>

    <!-- Priority Script end  -->



<!-- Deadline Script Toggle: start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get elements
        
        const repeatedButton = document.getElementById('repeated');
        const repeatedArea = document.querySelector('.repeated_area');
        const reminderButton = document.getElementById('reminder_check');
        const reminderArea = document.querySelector('.reminder_area');
        const addCalendarButton = document.getElementById('add_calendar');
        const addCalendarArea = document.getElementById('add_calendar_area');

        

        // Toggle repeated area
        repeatedButton.addEventListener('change', function () {
            if (this.checked) {
                repeatedArea.style.display = 'table-row';
            } else {
                repeatedArea.style.display = 'none';
            }
        });

        // Toggle reminder area
        reminderButton.addEventListener('change', function () {
            if (this.checked) {
                reminderArea.style.display = 'table-row';
            } else {
                reminderArea.style.display = 'none';
            }
        });

 
        // Initially hide all areas
       
        repeatedArea.style.display = 'none';
        reminderArea.style.display = 'none'; 
    });
</script>
 
<!-- Deadline Script Toggle: end  -->
<script>
$(document).ready(function () {
    const $select = $('#customerLeadProductSelect');
    const $switch = $('#customerSwitch');
    const $container = $('#customerSelectContainer');

    // Initialize Select2
    $select.select2({
        placeholder: 'Kunde suchen...',
        ajax: {
            url: '{{ route("lead.product.list.ajax") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            html: item.html,
                            alternative_id: item.alternative_id,
                            product_id: item.product_id
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            return $(data.html);
        },
        templateSelection: function (data) {
            // Fill hidden fields when selected
            if (data.alternative_id) {
                $('#select_alternative_id').val(data.alternative_id);
            }
            if (data.product_id) {
                $('#select_product_id').val(data.product_id);
            }
            return data.text;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Show/hide section based on switch
    $switch.on('change', function () {
        if ($(this).is(':checked')) {
            $container.slideDown();
            $(this).val(1);
        } else {
            $container.slideUp();
            $select.val(null).trigger('change');
            $('#select_alternative_id').val('');
            $('#select_product_id').val('');
            $(this).val(0);
        }
    });

    // Trigger correct state on page load
    if ($switch.is(':checked')) {
        $container.show();
    } else {
        $container.hide();
    }
});
</script>

<script>
// 🔁 This function initializes the power calculator
function initPowerCalculator(context = document) {
    const $fields = $(context).find('input[name="power_household"], input[name="power_heatpump"], input[name="power_electric_car"], input[name="power_other"]');
    const $totalField = $(context).find('#power_total');

    function validateNumber(input) {
        const val = input.val().trim().replace(',', '.');
        const num = parseFloat(val);
        if (isNaN(num) || num < 0) {
            input.addClass('is-invalid');
            return 0;
        } else {
            input.removeClass('is-invalid');
            return num;
        }
    }

    function calculateAndDisplay() {
        const household   = validateNumber($(context).find('input[name="power_household"]'));
        const heatpump    = validateNumber($(context).find('input[name="power_heatpump"]'));
        const electricCar = validateNumber($(context).find('input[name="power_electric_car"]'));
        const other       = validateNumber($(context).find('input[name="power_other"]'));

        const total = household + heatpump + electricCar + other;

        // Update main total field
        $totalField.val(total.toFixed(2).replace('.', ','));

        // Show annual consumption (optional badge)
        let $yearInfo = $(context).find('#power_total_year');
        if (!$yearInfo.length) {
            $yearInfo = $('<small id="power_total_year" class="form-text text-muted"></small>').insertAfter($totalField);
        }
        const yearly = total * 365;
        $yearInfo.text('≈ ' + yearly.toFixed(0).toLocaleString('de-DE') + ' kWh / Jahr');
    }

    // Bind calculation on input change
    $fields.off('input.powercalc').on('input.powercalc', calculateAndDisplay);

    // Initial run
    calculateAndDisplay();
}

// 🧲 Example usage after tab loads partial content
$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    const target = $($(e.target).attr('href'));
    if (target.length) {
        initPowerCalculator(target);
    }
});

// Also call once in case tab is active on page load
$(document).ready(function () {
    const activeTabPane = $('.tab-pane.active');
    if (activeTabPane.length) {
        initPowerCalculator(activeTabPane);
    }
});
</script>


<!-- Stage management Crude  -->
<script>
function loadStages(customer_id, alternative_id, product_id,section_id) {
    $.ajax({
        url: '/ajax/load-stages',
        method: 'GET',
        data: {
            customer_id,
            alternative_id,
            product_id,
            section_id
        },
        success: function (response) {
            $('#mainContent').html(response);
            feather.replace(); // if feather icons used
        },
        error: function () {
            alert('Fehler beim Laden der Stufen');
        }
    });
}
</script>

<script>
$(document).ready(function () {
    // Save selected version
    $(document).on('submit', '#stageVersionForm', function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Speichere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post('/ajax/save-customer-stage', $(this).serialize(), function(res){
            if (res.success) {
                Swal.fire('Gespeichert', 'Version erfolgreich gespeichert', 'success');
            }
        }).fail(function() {
            Swal.fire('Fehler', 'Speichern fehlgeschlagen', 'error');
        });
    });


    // 🔥 Change version and fetch new stages
   // Bind to document to support dynamic content
    $(document).on('change', '#versionSelect', function () {
        const version = $(this).val();
        const product_id = $('input[name="product_id"]').val();

        Swal.fire({
            title: 'Lade Version...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/ajax/load-version-stages',
            method: 'GET',
            data: {
                version: version,
                product_id: product_id
            },
            success: function (html) {
                $('#stageList').html(html);
                $('#selectedVersion').text(version);
                Swal.close();
            },
            error: function (xhr, status, error) {
                console.error('AJAX Fehler:', error);
                Swal.fire('Fehler', 'Konnte die Stufen nicht laden', 'error');
            }
        });
    });


});
</script>


<script>
function loadCalendar(cid, aid, pid) {
    const url = `/customer/calendar/view?cid=${cid}&aid=${aid}&pid=${pid}`;
    
    document.getElementById('mainContent').innerHTML = `
        <div class="text-center my-3">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Lade Kalender...</div>
        </div>`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('mainContent').innerHTML = html;
            feather.replace(); // For icons
        })
        .catch(err => {
            console.error(err);
            document.getElementById('mainContent').innerHTML = `<div class="text-danger">❌ Fehler beim Laden des Kalenders.</div>`;
        });
}
</script>

<script>
function showFullNote(el) {
    const text = el.dataset.note || 'Keine Notiz vorhanden.';
    
    Swal.fire({
        title: 'Kundennotiz',
        html: `<div style="white-space: pre-wrap; text-align: left;">${text}</div>`,
        icon: 'info',
        confirmButtonText: 'Schließen',
        width: '600px',
    });
}
</script>

<!-- Customer Report  -->
 <script>
     let editingReportId = null;

         let quill;

        document.addEventListener('DOMContentLoaded', function () {
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Bericht schreiben...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link']
                    ]
                }
            });
        });

 
    // Reset + open sidebar
    $(document).on('click', '.open-report-modal', function () {
        const sidebar = $('#reportSidebar');

        const product_id = $(this).data('product-id');
        const stage = $(this).data('stage');
        const customer_id = $(this).data('customer-id');
        const alternative_id = $(this).data('alternative-id');

        sidebar.addClass('open').show();

        $('#reportForm input[name=product_id]').val(product_id);
        $('#reportForm input[name=customer_id]').val(customer_id);
        $('#reportForm input[name=alternative_id]').val(alternative_id);
        $('#reportForm select[name=stage]').val(stage);
        $('#reportForm')[0].reset();
        quill.root.innerHTML = '';
        $('#reportFormContainer').hide();
        $('#reportForm button[type=submit]').text('Speichern');
        editingReportId = null;

        loadReports(product_id, customer_id, alternative_id);
    });

    // Replace loadReports to prepend new report
    function loadReports(product_id, customer_id, alternative_id) {
        $.get('/customer-reports/list', {
            product_id,
            customer_id,
            alternative_id
        }, function (html) {
            $('#reportList').html(html);
        });
    }


    $(document).on('click', '.edit-report', function () {
        const id = $(this).data('id');
        editingReportId = id;

        $.get('/customer-reports/show/' + id, function (data) {
            $('#reportForm input[name=report_date]').val(data.date);
            $('#reportForm select[name=stage]').val(data.stage);
            quill.root.innerHTML = data.report;

            $('#reportForm button[type=submit]').text('Aktualisieren');

            // Show modal form
            $('#reportFormContainer').fadeIn();
        });
    });


   
    $(document).on('click', '.close-report-sidebar', function () {
        $('#reportSidebar').removeClass('open');
        setTimeout(() => $('#reportSidebar').hide(), 300);
    });


    // Open form modal
    $(document).on('click', '.toggle-report-form', function () {
        $('#reportFormContainer').fadeIn();
    });

    // Close form modal
    $(document).on('click', '.close-report-form', function () {
        editingReportId = null;
        $('#reportForm')[0].reset();
        quill.root.innerHTML = '';
        $('#reportForm button[type=submit]').text('Speichern');
        $('#reportFormContainer').fadeOut();
    });


 
// Submit create/update
$(document).off('submit', '#reportForm').on('submit', '#reportForm', function (e) {
    e.preventDefault();

    const form = $(this);
    const isUpdate = editingReportId !== null;

    // Set report content from Quill
    form.find('[name="report"]').val(quill.root.innerHTML);

    // Prepare URL and method
    const url = isUpdate
        ? `/customer-reports/update/${editingReportId}`
        : '/customer-reports/store';

    const formData = form.serializeArray();

    // If it's an update, spoof _method = PUT for Laravel
    if (isUpdate) {
        formData.push({ name: '_method', value: 'PUT' });
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: $.param(formData),
        success: function () {
            const product_id = form.find('[name=product_id]').val();
            const customer_id = form.find('[name=customer_id]').val();
            const alternative_id = form.find('[name=alternative_id]').val();

            // Reload reports
            loadReports(product_id, customer_id, alternative_id);

            // Reset form and UI
            editingReportId = null;
            form[0].reset();
            quill.root.innerHTML = '';
            $('#reportForm button[type=submit]').text('Speichern');

            // Close modal form
            $('#reportFormContainer').fadeOut();
        }
    });
});



$(document).on('click', '.delete-report', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Bist du sicher?',
        text: "Der Bericht wird gelöscht.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/customer-reports/delete/${id}`,
                type: 'POST', // ✅ change to POST
                data: {
                    _method: 'DELETE', // ✅ method spoofing for Laravel
                    _token: $('meta[name="csrf-token"]').attr('content') // ✅ add CSRF token
                },
                success: function () {
                    const form = $('#reportForm');
                    loadReports(
                        form.find('[name=product_id]').val(),
                        form.find('[name=customer_id]').val(),
                        form.find('[name=alternative_id]').val()
                    );
                }
            });
        }
    });
});

 
 </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let replyingToText = '';

    // 🔁 Get current report ID from multiple sources
    function getReportId() {
        return $('#report_id').val() || $('.open-comment-sidebar').data('report-id') || null;
    }

    // 🔄 Reload comment list
    function reloadComments(reportId) {
        if (!reportId) return;
        $.get(`/customer-report-comments/${reportId}`, function (html) {
            $('#commentContent').hide().html(html).fadeIn();
        });
    }

    // 🧼 Clear comment form
    function clearCommentForm() {
        $('#newCommentForm')[0].reset();
        $('#quotedComment').hide().html('');
        $('#parent_id').val('');
        $('#newCommentForm input[name="_method"]').remove();
        $('#newCommentForm input[name="comment_id"]').remove();
    }


    // 🧭 Scroll to a comment
    function scrollToComment(id) {
        const target = $(`#comment-${id}`);
        if (!target.length) return;
        $('#commentContent').animate({
            scrollTop: target.position().top + $('#commentContent').scrollTop() - 60
        }, 400);
        target.addClass('bg-warning-light');
        setTimeout(() => target.removeClass('bg-warning-light'), 2000);
    }

    // 🔓 Open sidebar
    $(document).on('click', '.open-comment-sidebar', function () {
        const reportId = $(this).data('report-id');
        $('#report_id').val(reportId);
        $('#commentSidebar').addClass('open').show();
        reloadComments(reportId);
    });

    // ❌ Close sidebar
    $(document).on('click', '.close-comment-sidebar', function () {
        $('#commentSidebar').removeClass('open').hide();
        $('#commentFormModal').hide();
    });

    // ➕ Open new comment form
    $(document).on('click', '.open-comment-form', function () {
        $('#report_id').val($(this).data('report-id') || getReportId());
        clearCommentForm();
        $('#commentFormModal').fadeIn();
    });

    // ↩️ Reply
    $(document).on('click', '.reply-comment', function () {
        const commentId = $(this).data('id');
        const body = $(this).data('body');

        replyingToText = body;
        $('#quotedComment').html(`<small><i>Antwort auf:</i><br> ${body}</small>`).show();
        $('#parent_id').val(commentId);
        $('#report_id').val(getReportId());
        $('#commentFormModal').fadeIn();

        scrollToComment(commentId);
    });

    // ✏️ Edit comment
    $(document).on('click', '.edit-comment', function () {
        const id = $(this).data('id');
        const text = $(this).data('body');

        clearCommentForm();
        $('textarea[name="comment"]').val(text);
        $('#newCommentForm').append(`<input type="hidden" name="_method" value="PUT">`);
        $('#newCommentForm').append(`<input type="hidden" name="comment_id" value="${id}">`);
        $('#commentFormModal').fadeIn();
    });

    // ❌ Close form
    $(document).on('click', '.close-comment-form', function () {
        $('#commentFormModal').fadeOut();
        clearCommentForm();
    });

    // 💾 Submit comment (create or update)
    $(document).on('submit', '#newCommentForm', function (e) {
        e.preventDefault();

        const form = $(this);
        const isUpdate = form.find('input[name="comment_id"]').length > 0;
        const url = isUpdate
            ? `/customer-report-comments/${form.find('input[name="comment_id"]').val()}`
            : `/customer-report-comments`;

        $.ajax({
            type: 'POST',
            url,
            data: form.serialize(),
            success: function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: isUpdate ? 'Kommentar aktualisiert' : 'Kommentar hinzugefügt',
                    showConfirmButton: false,
                    timer: 1500
                });

                clearCommentForm();
                $('#commentFormModal').fadeOut();
                reloadComments(getReportId());
            }
        });
    });

    // 🗑️ Delete comment
    $(document).on('click', '.delete-comment', function () {
        const id = $(this).data('id');
        const reportId = getReportId();

        Swal.fire({
            title: 'Kommentar löschen?',
            text: 'Das kann nicht rückgängig gemacht werden.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/customer-report-comments/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Kommentar gelöscht',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    reloadComments(reportId);
                }
            });
        });
    });

    // 🔁 Scroll to quoted
    $(document).on('click', '.scroll-to-comment', function () {
        scrollToComment($(this).data('target'));
    });

    function closeCommentForm() {
        $('#commentFormModal').fadeOut();
        clearCommentForm();
    }

    // Close from buttons
    $(document).on('click', '.close-comment-form', function () {
        closeCommentForm();
    });

    // Close when clicking the dimmed overlay (outside modal-content)
    $(document).on('click', '#commentFormModal', function (e) {
        if (e.target === this) { // click on backdrop
            closeCommentForm();
        }
    });

    // Close on ESC key
    $(document).on('keyup', function (e) {
        if (e.key === 'Escape' && $('#commentFormModal').is(':visible')) {
            closeCommentForm();
        }
    });

});
</script>


@endpush



 