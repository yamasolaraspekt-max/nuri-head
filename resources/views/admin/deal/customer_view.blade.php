@extends('admin.layouts.app')

@section('title') AUFTRÄGE @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

 <style>
    .opens {
        border-color: #e53060;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .actives {
        border-color: #92b532;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .inactives {
        border-color: #78a7cc;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .project_ends {
        border-color: #213985;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }
    .project_cancel {
        background: white;
        padding: 6px;
         border-style: solid;
         border-color:#b1aaaa;
         height: 110px !important;
         width: 110px !important;
         margin-right: 11px;
    }
    .inner_size {
        height: 90px !important;
    }
   .articles {
    background: #b1aaaa;
    border-radius: 50%;
    height: 50px !important;
    width: 50px !important;
    margin-right: 11px;
    display: grid;
    align-items: center;
    text-align: center;
    cursor: pointer;
}
.articles input[type="radio"] {
    display: none;
}
.articles label {
    font-size: 20px !important;
    cursor: pointer;
    display: grid;
    align-items: center;
    height: 50px;
    width: 50px;
    margin: 0;
    padding: 0;
    border-radius: 50%; /* Ensure label maintains border-radius */
}
.articles input[type="radio"]:checked + label {
    background: #92b532;
    color: white;
    border-radius: 50%; /* Maintain border-radius when selected */
}
.article_text {
    color: #b1aaaa;
}
.article_text p {
    font-size: 15px !important;
}
 
    .scrollable-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-evenly;
        overflow-x: auto;
        width: 100%;
        padding: 10px 0;
    }

    .scrollable-container::-webkit-scrollbar {
        height: 8px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
    }

    .scrollable-container::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }

    .products {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0 2px !important;
        flex-direction: column;
    }

    .card {
        min-width: 150px;
        margin: 0 10px;
    }

    .inner_size {
        padding: 20px;
    }
    .modal-backdrop {
    z-index: 1040 !important;
    }
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
    position: absolute;
    }
   
    h4 .bold {
        font-size:13px !important;
        font-weight:200px !important;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .blink {
        animation: blink 1s infinite;
    }
    .bolders {
            font-size: 15px;
            font-weight: bolder;
            width: 167px;
        }

    .file-item {
        width: 137px;
    }
 
</style>



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
    .file-info{
        text-align:left;
        margin-bottom:6px;
    }
  </style>


<style>
.note-sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 80%;
    height: 100%;
    background: #fdfdfd;
    z-index: 1050;
    box-shadow: 4px 0 8px rgba(0,0,0,0.2);
    transition: left 0.3s ease;
    display: flex;
    flex-direction: column;
}

.note-sidebar.open {
    left: 0;
}

.sidebar-header {
    background: #ffc107;
    padding: 1rem;
    color: #000;
    position: sticky;
    top: 0;
    z-index: 10;
}

.note-messages {
    flex-grow: 1;
    overflow-y: auto;
}

.note-message {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 10px;
    background: #e9ecef;
    max-width: 80%;
}

.note-message.own {
    margin-left: auto;
    background: #f4f4f4;
}

.sidebar-footer {
    background: #fff;
    position: sticky;
    bottom: 0;
}
</style>


<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">
<style>
    .dropzone {
        border: 2px dashed #8fc73e;
        border-radius: 20px;
        background: #fafafa;
    }
 
</style>

<style>
.gallery-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
.gallery-item {
    width: 160px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
}
.gallery-item:hover {
    transform: scale(1.02);
}
.gallery-item img,
.gallery-item .file-icon {
    width: 100%;
    height: 120px;
    object-fit: cover;
}
.gallery-controls {
    padding: 8px;
}
</style>



<style>
    .kanban-board {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 12px;
        padding-bottom: 10px;
    }

    .kanban-column {
        min-width: 350px;
        background: #f9f9f9;
        border-right: 4px dotted #babdbf;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .kanban-column:last-child {
        border-right: none !important;
    }

    .kanban-item {
        cursor: grab;
    }

    .kanban-controls .form-control {
        min-width: 180px;
    }
</style>
<style> 
.kanban-item:hover {
    box-shadow: 0 0 8px rgba(0,0,0,0.15);
}
.kanban-placeholder {
    border: 2px dashed #ccc;
    min-height: 60px;
    margin: 10px 0;
} 


.kanban-card {
    position: relative;
    z-index: 1;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 10px;
}

.kanban-card.ui-sortable-helper {
    z-index: 9999 !important;
}

.kanban-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: move;
}
.kanban-item.dragging {
    transform: rotate(2deg) scale(1.02);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    z-index: 999;
    opacity: 0.9;
}
.kanban-placeholder {
    border: 2px dashed #ccc;
    margin: 8px 0;
    height: 80px;
}

.kanban-item.ui-sortable-helper {
    z-index: 1050 !important;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
    background: white;
}

.kanban-list {
    min-height: 100px;
    position: relative;
}



</style>


<style>
    .upload-sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 400px;
    height: 100%;
    background: #fff;
    border-right: 2px solid #ccc;
    z-index: 1055;
    transition: all 0.3s ease;
    overflow-y: auto;
}

.upload-sidebar.open {
    left: 0;
}

.upload-sidebar .sidebar-header {
    background-color: #007bff;
    color: white;
    padding: 15px;
}

.upload-sidebar .sidebar-body {
    padding: 15px;
}

#uploadBackdrop {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 1050;
    display: none;
}
#uploadBackdrop.show {
    display: block;
}


.upload-sidebar .dropzone-custom.dz-drag-hover {
    background-color: rgba(143, 199, 62, 0.1);
    border-color: #28a745;
}


.icon-toolbar .icon-action {
    background: none;
    border: none;
    padding: 6px;
    margin-right: 10px;
    font-size: 1.4rem;
    color: #444;
    transition: transform 0.2s ease, color 0.2s ease;
}

.icon-toolbar .icon-action:hover i {
    transform: scale(1.3);
    color: #000 !important;
}

.icon-toolbar .icon-action:last-child {
    margin-right: 0;
}

.icon-toolbar i {
    transition: transform 0.2s ease, color 0.2s ease;
    cursor: pointer;
}
 

</style>

<style>
.bg-highlight {
    background-color:rgba(142, 199, 62, 0.55) !important;
    animation: fadeOutHighlight 3s ease-in-out forwards;
}

@keyframes fadeOutHighlight {
    0%   { background-color: #8fc73e38; }
    100% { background-color: white; }
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
                        <h2 class="content-header-title float-left mb-0">AUFTRÄGE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/deal_all_list') }}">Aufträge</a></li>
                                 
                                </li>
                                <li class="breadcrumb-item active">
                                    @if(Route::currentRouteName() == 'deal.junk.list') 
                                    <a href="{{ route('deal.junk.list') }}">JUNK</a>
                                    @elseif(Route::currentRouteName() == 'deal.all.list') 
                                     <a href="{{ route('deal.all.list') }}">ALLE</a>
                                    @elseif(Route::currentRouteName() == 'deal.delete.list') 
                                     <a href="{{ route('deal.delete.list') }}">GELÖSCHTE</a>
                                    @else Neue @endif
                                </li>

                                    
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="content-body">
                <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section -->  
                                        <!-- Search Section -->
                                        <div class="row" style="display: flex ;flex-direction: row-reverse !important;"> 
                                            <div class="col-1">
                                               <!-- Trigger button -->
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#dealModal">Erstellen</button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="dealModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <form method="POST" action="{{ route('deal.store') }}" id="dealStore">
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                    <h5 class="modal-title" id="dealModalLabel">Neues Projekt erstellen</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                    <div class="row">
                                                                        <!-- Customer -->
                                                                        <div class="col-md-6 mb-2">
                                                                        <label>Kunde</label>
                                                                        <select name="customer_id" id="customer_id" class="form-control select2" required>
                                                                            <option value="">-- Wähle Kunde --</option>
                                                                            @foreach($customers as $cust)
                                                                            <option value="{{ $cust->id }}">{{ $cust->name }} {{ $cust->lastname }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        </div>

                                                                        <!-- Product -->
                                                                        <div class="col-md-6 mb-2">
                                                                        <label>Produkt</label>
                                                                        <select name="lead_product_list_id" id="product_list_id" class="form-control select2" required>
                                                                            <option value="">-- Wähle Produkt --</option>
                                                                        </select>
                                                                        </div>

                                                                        <!-- Hidden fields -->
                                                                        <input type="hidden" name="product_id" id="product_id">
                                                                        <input type="hidden" name="alternative_id" id="alternative_id">
                                                                        <input type="hidden" name="department_id" id="department_id">
                                                                        <input type="hidden" name="service_id" id="service_id">
                                                                        <input type="hidden" name="employee_id" id="employee_id">
                                                                        <input type="hidden" name="service" id="service_str">
                                                                    </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success">Projekt erstellen</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div> 
                                            </div> 
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        
                                        <section id="basic-tabs-components">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card overflow-hidden"> 
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <ul class="nav nav-tabs" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Kanban</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Liste</a>
                                                                    </li>
                                                                    
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                        <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                            @include('admin.deal.partials.kanban')
                                                                            </div>

                                                                    </div>
                                                                    <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                                        @include('admin.deal.partials.list')
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
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->




<!-- Sidebar -->
<div id="noteSidebar" class="note-sidebar">
    <div class="sidebar-header">
        <input type="text" id="searchNotes" placeholder="Suche Notizen..." class="form-control">
        <button id="closeSidebar" class="btn btn-sm btn-danger float-right">✕</button>
        
    </div>

    <div id="notesList" class="note-messages px-2">
        <!-- Notes will be dynamically loaded here -->
    </div>

    <div class="sidebar-footer p-2">
        <textarea id="newNoteContent" class="form-control" rows="2" placeholder="Neue Notiz schreiben..."></textarea>
        <button id="sendNote" class="btn btn-success btn-sm mt-2 w-100">Senden</button>
    </div>
</div>


   <!-- 📁 Upload Sidebar -->
<!-- Upload Sidebar -->
<div id="uploadSidebar" class="upload-sidebar">
    <div class="upload-header d-flex justify-content-between align-items-center p-2">
        <h5 class="mb-0">📤 Datei-Upload</h5>
        <button class="btn btn-sm btn-danger" id="closeUploadSidebar"><i class="fa fa-times"></i></button>
    </div>

    <form action="{{ url('customer_upload') }}" method="POST" class="dropzone dropzone-custom" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="customer_id" id="uploadCustomerId">
        <input type="hidden" name="alternative_id" id="uploadAlternativeId">
        <input type="hidden" name="product_id" id="uploadProductId">
        <input type="hidden" name="status" value="deal">
        <input type="hidden" name="stage_id" id="uploadStage">

        <div class="p-2">
            <label for="uploadStage">Stufe:</label>
            <select class="form-control form-control-sm" name="stage_id">
                <option value="">-- wählen --</option>
                <option value="order">Kundenauftrag</option>
                <option value="confirmed_order">Auftragsbestätigung</option>
                <option value="offer">Angebot</option>
            </select>
        </div>
    </form>

    <div class="p-2">
        <label for="filterStage">Galerie filtern:</label>
        <select id="filterStage" class="form-control form-control-sm mb-2">
            <option value="">Alle</option>
            <option value="order">Kundenauftrag</option>
            <option value="confirmed_order">Auftragsbestätigung</option>
            <option value="offer">Angebot</option>
        </select>
    </div>

    <div class="upload-gallery px-2"></div>
</div>

<!-- Backdrop -->
<div id="uploadBackdrop"></div>

 


@endsection
 
@section('script')
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
 


<!-- Quill Other Editor -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction
            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];

        document.querySelectorAll('.editor-container').forEach(function (editorContainer) {
            var quill = new Quill(editorContainer, {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });

            var targetTextarea = document.querySelector(editorContainer.getAttribute('data-target'));

            quill.on('text-change', function () {
                targetTextarea.value = quill.root.innerHTML;
            });
        });
    }); 
     
</script>


<!-- Updateing Price Start  -->  
<script>
 $(document).ready(function () {
    function saveCurrentPage() {
        const currentPage = $('.pagination .active span').text();
        localStorage.setItem('currentPage', currentPage);
    }

    function restorePage() {
        const savedPage = localStorage.getItem('currentPage');
        if (savedPage) {
            $('.pagination a').each(function () {
                if ($(this).text().trim() === savedPage) {
                    $(this)[0].click();
                }
            });
        }
    }

    // On double-click: create input or select based on field
    // $(document).on('dblclick', '.editable-cell', function () {
    //     const field = $(this).data('field');
    //     const id = $(this).data('id');
    //     const value = $(this).text().trim();

    //     let inputHTML = '';

    //     if (field === 'sign_date') {
    //         inputHTML = `<input type="date" class="form-control edit-field"
    //                           data-id="${id}"
    //                           data-field="${field}"
    //                           value="${value === 'unbekannt' ? '' : value}"
    //                           style="width:120px;">`;
    //     } else if (field === 'price') {
    //         inputHTML = `<input type="number" class="form-control edit-field"
    //                           data-id="${id}"
    //                           data-field="${field}"
    //                           value="${value === 'unbekannt' ? '' : value}"
    //                           style="width:120px;">`;
    //     } else if (field === 'status') {
    //         inputHTML = `
    //             <select class="form-control edit-field" data-id="${id}" data-field="status" style="width:130px;">
    //                 <option value="confirm" ${value === 'Bestätigt' ? 'selected' : ''}>Bestätigt</option>
    //                 <option value="inconfirm" ${value === 'Unbestätigt' ? 'selected' : ''}>Unbestätigt</option>
    //             </select>`;
    //     }

    //     $(this).html(inputHTML).find('.edit-field').focus();
    // });

    // Save on blur
    $(document).on('blur', '.edit-field', function () {
        const $input = $(this);
        const id = $input.data('id');
        const field = $input.data('field');
        const value = $input.val().trim();

        saveCurrentPage();

        $.ajax({
            url: "{{ route('deal.price') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                field: field,
                value: value
            },
            success: function (response) {
                Swal.fire("Erfolgreich!", response.message, "success").then(() => {
                    location.reload();
                });
            },
            error: function () {
                Swal.fire("Fehler!", "Aktualisierung fehlgeschlagen.", "error");
            }
        });
    });

    $(window).on('load', restorePage);
});

</script>


<!-- Updateing Price End  -->



<!-- Image Card: start:  --> 
 


<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    $('#customer_id').on('change', function () {
        let customerId = $(this).val();
        $('#product_list_id').html('<option>-- Lade Produkte --</option>');

        $.get(`/get-product-lists/${customerId}`, function (data) {
            $('#product_list_id').html('<option value="">-- Wähle Produkt --</option>');
            data.forEach(d => {
                $('#product_list_id').append(`
                    <option value="${d.id}"
                        data-product="${d.product_id}"
                        data-alternative="${d.alternative_id}"
                        data-department="${d.department_id ?? ''}"
                        data-service="${d.service_id ?? ''}"
                        data-employee="${d.employee_id ?? ''}"
                        data-service-str="${d.service ?? ''}">
                        ${d.article_group}
                    </option>
                `);
            });
        });
    });

    $('#product_list_id').on('change', function () {
        let selected = $(this).find('option:selected');
        $('#product_id').val(selected.data('product'));
        $('#alternative_id').val(selected.data('alternative'));
        $('#department_id').val(selected.data('department'));
        $('#service_id').val(selected.data('service'));
        $('#employee_id').val(selected.data('employee'));
        $('#service_str').val(selected.data('service-str'));
    });
});
</script>

<script>
function openEmployeeSelector(dealId, checkedById = null, reviewedById = null) {
    $.get('/get-employees', function (employees) {
        if (!employees.length) {
            Swal.fire('Keine Mitarbeiter gefunden');
            return;
        }

        let employeeOptions = {};
        employees.forEach(e => {
            employeeOptions[e.id] = `${e.name} ${e.lastname}`;
        });

        const checkedOptions = Object.entries(employeeOptions).map(([id, name]) => {
            const selected = (id == checkedById) ? 'selected' : '';
            return `<option value="${id}" ${selected}>${name}</option>`;
        }).join('');

        const reviewedOptions = Object.entries(employeeOptions).map(([id, name]) => {
            const selected = (id == reviewedById) ? 'selected' : '';
            return `<option value="${id}" ${selected}>${name}</option>`;
        }).join('');

        Swal.fire({
            title: 'Mitarbeiter auswählen',
            html: `
                <select id="checked_by" class="swal2-input">
                    <option value="">-- Geprüft durch --</option>
                    ${checkedOptions}
                </select>
                 
            `,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    checked_by: $('#checked_by').val(),
                    reviewed_by: $('#reviewed_by').val(),
                };
            },
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('/update-deal-reviewers', {
                    _token: '{{ csrf_token() }}',
                    deal_id: dealId,
                    checked_by: result.value.checked_by,
                    reviewed_by: result.value.reviewed_by,
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Gespeichert');
                        location.reload(); // or re-render DOM via JS
                    } else {
                        Swal.fire('Fehler beim Speichern');
                    }
                });
            }
        });
    });
}
</script>
<script>
$(document).on('click', '.editable-cell', function () {
    const dealId = $(this).data('id');
    const field = $(this).data('field');

    const labelMap = {
        sign_date: "Signierungsdatum",
        confirmed_at: "Bestätigt am",
        delivered_at: "Geliefert am",
        status: "Status",
        price: "Preis"
    };

    const label = labelMap[field] || field;
    const today = new Date().toISOString().split('T')[0];

    if (field === 'status') {
        Swal.fire({
            title: `${label} ändern`,
            input: 'select',
            inputOptions: {
                confirm: 'Bestätigt',
                inconfirm: 'Unbestätigt',
                open: 'Offen'
            },
            inputPlaceholder: 'Status wählen',
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',
            inputValidator: (value) => {
                if (!value) {
                    return 'Bitte einen Status wählen';
                }
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.post('/update-deal-date', {
                    _token: '{{ csrf_token() }}',
                    deal_id: dealId,
                    field: field,
                    value: result.value
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Gespeichert');
                        location.reload();
                    } else {
                        Swal.fire('Fehler beim Speichern');
                    }
                }).fail(() => {
                    Swal.fire('Fehler', 'Validierung fehlgeschlagen.', 'error');
                });
            }
        });

    } else if (field === 'price') {
        // 💶 Decimal input for price
        Swal.fire({
            title: `${label} ändern`,
            html: `<input type="number" id="priceInput" class="swal2-input" step="0.01" min="0" placeholder="z.B. 199.99">`,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
                const val = document.getElementById('priceInput').value;
                return val.trim() === '' ? null : parseFloat(val).toFixed(2);
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.post('/update-deal-date', {
                    _token: '{{ csrf_token() }}',
                    deal_id: dealId,
                    field: field,
                    value: result.value
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Gespeichert');
                        location.reload();
                    } else {
                        Swal.fire('Fehler beim Speichern');
                    }
                }).fail(() => {
                    Swal.fire('Fehler', 'Validierung fehlgeschlagen.', 'error');
                });
            }
        });

    } else {
        // 🗓️ Default date input
        Swal.fire({
            title: `${label} ändern`,
            html: `<input type="date" id="dateInput" class="swal2-input" value="${today}" placeholder="Datum wählen">`,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
                const val = document.getElementById('dateInput').value;
                return val.trim() === '' ? null : val;
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.post('/update-deal-date', {
                    _token: '{{ csrf_token() }}',
                    deal_id: dealId,
                    field: field,
                    value: result.value
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Gespeichert');
                        location.reload();
                    } else {
                        Swal.fire('Fehler beim Speichern');
                    }
                }).fail(() => {
                    Swal.fire('Fehler', 'Validierung fehlgeschlagen.', 'error');
                });
            }
        });
    }
});
</script>


<script>
let uploadSidebarParams = {};

$(document).on('click', '.open-upload-sidebar', function () {
    const customerId = $(this).data('customer-id');
    const alternativeId = $(this).data('alternative-id');
    const productId = $(this).data('product-id');

    uploadSidebarParams = { customerId, alternativeId };

    // Fill hidden inputs
    $('#upload_customer_id').val(customerId);
    $('#upload_alternative_id').val(alternativeId);
    $('#upload_product_id').val(productId);

    // Open sidebar
    $('#uploadSidebar').addClass('open');

    // Load files
    loadUploadGallery();
});

$('#closeUploadSidebar').on('click', function () {
    $('#uploadSidebar').removeClass('open');
});

function loadUploadGallery() {
    const stage = $('#filterStage').val();

    $('.upload-gallery').html('<div class="text-muted">Lade Dateien...</div>');

    $.get('/deal/load-customer-files', {
        ...uploadSidebarParams,
        stage: stage
    }, function (res) {
        $('.upload-gallery').html(res);
        GLightbox({ selector: '.glightbox' });
    });
}

$('#filterStage').on('change', loadUploadGallery);

// Rename file
$(document).on('change', '.rename-input', function () {
    const id = $(this).data('id');
    const new_name = $(this).val();
    $.post('/deal/rename-file', {
        _token: '{{ csrf_token() }}',
        id: id,
        new_name: new_name
    });
});

// Delete file
$(document).on('click', '.delete-file', function () {
    const id = $(this).data('id');
    if (!confirm("Datei wirklich löschen?")) return;

    $.post('/deal/delete-file', {
        _token: '{{ csrf_token() }}',
        id: id
    }, function () {
        loadUploadGallery();
    });
});

</script>

<script>
let currentUploadModalId = null;

$(document).on('click', '[data-toggle="modal"][data-target^="#upload"]', function () {
    const customerId = $(this).data('customer-id');
    const alternativeId = $(this).data('alternative-id');
    const productId = $(this).data('product-id');
    const targetModalId = $(this).data('target');

    currentUploadModalId = targetModalId; // save for later

    // When modal opens, load gallery
    $(targetModalId).on('shown.bs.modal', function () {
        loadCustomerGallery(customerId, alternativeId, $(this).find('.modal-body'));
    });
});

function loadCustomerGallery(customerId, alternativeId, $container) {
    $.get('/deal/load-customer-files', {
        customer_id: customerId,
        alternative_id: alternativeId
    }, function (res) {
        $container.find('.gallery-container').remove(); // clean old
        $container.append('<hr><div class="gallery-container d-flex">' + res + '</div>');
        GLightbox({ selector: '.glightbox' });
    });
}

// Rename
$(document).on('change', '.rename-input', function () {
    const id = $(this).data('id');
    const new_name = $(this).val();
    $.post('/deal/rename-file', {
        _token: '{{ csrf_token() }}',
        id: id,
        new_name: new_name
    });
});

// Delete
$(document).on('click', '.delete-file', function () {
    const id = $(this).data('id');
    if (!confirm("Datei wirklich löschen?")) return;

    $.post('/deal/delete-file', {
        _token: '{{ csrf_token() }}',
        id: id
    }, function () {
        if (currentUploadModalId) {
            const $modal = $(currentUploadModalId);
            const customerId = $modal.find('input[name="customer_id"]').val();
            const alternativeId = $modal.find('input[name="alternative_id"]').val();
            loadCustomerGallery(customerId, alternativeId, $modal.find('.modal-body'));
        }
    });
});
</script>

<script>
$(document).on('click', '.open-upload-modal', function () {
    const customerId = $(this).data('customer-id');
    const alternativeId = $(this).data('alternative-id');
    const itemId = $(this).data('item-id');

    const container = $('#upload' + itemId).find('.gallery-container');

    container.html('<div class="text-muted">Lade Dateien...</div>');

    $.get('/deal/load-customer-files', {
        customer_id: customerId,
        alternative_id: alternativeId
    }, function (res) {
        container.html(res);
        GLightbox({ selector: '.glightbox' });
    });
});
</script>


<script>
$(function () {
    // 🔍 Search & Filter
    $('#kanban-search, #kanban-filter').on('input change', function () {
        const keyword = $('#kanban-search').val().toLowerCase();
        const filter = $('#kanban-filter').val();
        const userId = '{{ auth()->user()->name }}';

        $('.kanban-item').each(function () {
            const card = $(this);
            const name = card.text().toLowerCase();
            const involved = card.html().includes(userId);
            const matches = name.includes(keyword);
            const show = matches && (filter === 'all' || involved);
            card.toggle(show);
        });
    });

    // 🧲 Drag & Drop
    $('.kanban-list').sortable({
        connectWith: '.kanban-list',
        placeholder: 'kanban-placeholder',
        tolerance: 'pointer',
        start: function (e, ui) {
            ui.placeholder.height(ui.item.height());
            ui.item.css('z-index', 9999); // prevent being hidden behind columns
        },
        stop: function (e, ui) {
            ui.item.css('z-index', ''); // reset z-index

            const itemId = ui.item.data('id');
            const newStatus = ui.item.closest('.kanban-list').data('status');

            // 🛰️ Send status update
            $.post('/deal/update-status', {
                _token: '{{ csrf_token() }}',
                id: itemId,
                status: newStatus
            }).done(() => {
                console.log(`Updated #${itemId} to ${newStatus}`);
            }).fail(err => {
                alert("Update failed!");
                console.error(err);
            });
        }
    }).disableSelection();



    $('#kanban-search, #kanban-filter, #kanban-stage, #kanban-product').on('input change', function () {
        const search = $('#kanban-search').val().toLowerCase();
        const filter = $('#kanban-filter').val();
        const stage = $('#kanban-stage').val();
        const product = $('#kanban-product').val();
        const myId = '{{ auth()->user()->name }}';

        $('.kanban-item').each(function () {
            const name = $(this).text().toLowerCase();
            const empId = $(this).data('emp-id');
            const itemStage = $(this).data('stage');
            const itemProduct = $(this).data('product-id');

            const matchSearch = name.includes(search);
            const matchFilter = filter === 'all' || empId == myId;
            const matchStage = !stage || itemStage == stage;
            const matchProduct = !product || itemProduct == product;

            $(this).toggle(matchSearch && matchFilter && matchStage && matchProduct);
        });
    });



    function reloadKanbanColumn(status) {
        $.get(`/deal/load-kanban-column/${status}`, function (html) {
            $(`.kanban-list[data-status="${status}"]`).html(html);
        });
    }


});
</script>

<script>
let currentNoteParams = {};

// 👉 Open Sidebar and Load Notes
$(document).on('click', '.open-notes-sidebar', function () {
    currentNoteParams = {
        customer_id: $(this).data('customer-id'),
        alternative_id: $(this).data('alternative-id'),
        product_id: $(this).data('product-id')
    };

    $('#noteSidebar').addClass('open');
    loadNotes();
});

// 👉 Close Sidebar
$('#closeSidebar').on('click', () => {
    $('#noteSidebar').removeClass('open');
});

// 👉 Load Notes (optionally filtered by search)
function loadNotes(search = '') {
    $.get('/deal/load-customer-notes', {
        ...currentNoteParams,
        search: search
    }).done(function (html) {
        $('#notesList').html(html);
    });
}

// 👉 Create New Note (top level)
$('#sendNote').on('click', function () {
    const content = $('#newNoteContent').val().trim();
    if (!content) return;

    $.post('/deal/store-customer-note', {
        _token: '{{ csrf_token() }}',
        ...currentNoteParams,
        description: content
    }).done(() => {
        $('#newNoteContent').val('');
        loadNotes();
    });
});

// 👉 Live Search Notes
$('#searchNotes').on('input', function () {
    loadNotes($(this).val().trim());
});

// 👉 Edit Note
function editNote(noteId) {
    const newText = prompt("Neue Notiz eingeben:");
    if (!newText) return;

    $.post('/deal/update-customer-note', {
        _token: '{{ csrf_token() }}',
        note_id: noteId,
        description: newText.trim()
    }).done(loadNotes);
}

// 👉 Delete Note
function deleteNote(noteId) {
    if (!confirm("Möchten Sie diese Notiz wirklich löschen?")) return;

    $.post('/deal/delete-customer-note', {
        _token: '{{ csrf_token() }}',
        note_id: noteId
    }).done(loadNotes);
}

// 👉 Toggle Reply Input Box
function toggleReplyInput(noteId) {
    $(`#reply_box_${noteId}`).slideToggle();
}

// 👉 Send Reply to a Parent Note
function sendReply(parentId) {
    const replyText = $(`#reply_input_${parentId}`).val().trim();
    if (!replyText) return;

    $.post('/deal/store-customer-note', {
        _token: '{{ csrf_token() }}',
        ...currentNoteParams,
        description: replyText,
        parent_id: parentId
    }).done(() => {
        $(`#reply_input_${parentId}`).val('');
        $(`#reply_box_${parentId}`).slideUp();
        loadNotes();
    });
}
</script>

<script>
$(document).ready(function () {
    // ✅ Deal form submission via AJAX
    $('#dealStore').submit(function (e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.attr('action');
        const formData = $form.serialize();
        const $submitBtn = $form.find('button[type="submit"]');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            beforeSend: function () {
                $submitBtn.prop('disabled', true).text('Speichern...');
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Projekt erstellt!',
                    text: response.message || 'Das Projekt wurde erfolgreich erstellt.'
                });

                $('#dealModal').modal('hide');
                $form[0].reset();
                $submitBtn.prop('disabled', false).text('Projekt erstellen');

                // ✅ Reload page and pass new deal ID via hash
                setTimeout(() => {
                    window.location.href = window.location.pathname + '#deal-' + response.deal_id;
                    window.location.reload();
                }, 1000);
            },
            error: function (xhr) {
                $submitBtn.prop('disabled', false).text('Projekt erstellen');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    const errorList = Object.values(errors).map(err => `<li>${err[0]}</li>`).join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validierungsfehler',
                        html: `<ul style="text-align:left;">${errorList}</ul>`
                    });
                } else if (xhr.status === 409) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Doppelter Eintrag',
                        text: xhr.responseJSON.message || 'Ein ähnliches Projekt existiert bereits.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: 'Unbekannter Fehler aufgetreten.'
                    });
                }
            }
        });
    });

    // ✅ On page load: highlight new row if redirected with hash
    const hash = window.location.hash;
    if (hash.startsWith('#deal-')) {
        const el = document.querySelector(hash);
        if (el) {
            el.classList.add('bg-highlight');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>


<script>
$(document).ready(function () {
    const hash = window.location.hash;

    if (hash.startsWith('#deal-')) {
        const row = document.querySelector(hash);
        if (row) {
            row.classList.add('bg-highlight');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>


@endsection
